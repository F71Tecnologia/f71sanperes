<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/PrestadorServicoClass.php');
include('../../classes_permissoes/acoes.class.php');

function salva_nfse_codigo_servico_assoc(array $array) {
//    var_dump($array);
    $id = $array['id'];
    unset($array['id']);

    $query_servico = "SELECT id FROM nfse_codigo_servico WHERE codigo = '{$array['CodigoTributacaoMunicipio']}'";
    $arr_servico = mysql_fetch_assoc(mysql_query($query_servico));
//    print_array($arr_servico);
    $array['id_codigo_servico'] = $arr_servico['id'];
    unset($array['CodigoTributacaoMunicipio']);

    if (!empty($id)) {
        foreach ($array as $key => $value) {
            $string[] = "$key = '$value'";
        }
        $qr = "UPDATE nfse_codigo_servico_assoc SET " . implode(", ", $string) . " WHERE id =" . $id;
    } else {
        $keys = array_keys($array);
        $values = array_values($array);
        $qr = "INSERT INTO nfse_codigo_servico_assoc (" . implode(", ", $keys) . ") VALUES ('" . implode("', '", $values) . "')";
    }
//    echo $qr;
    mysql_query($qr) or die($qr . ' - ' . mysql_error());
}

function salvar_contas_assoc($id_prestador, $impostos) {
    mysql_query("DELETE FROM contabil_contas_assoc_prestador WHERE id_prestador = {$id_prestador}");
    $dre = $impostos['dre'];
    unset($impostos['dre']);
    foreach ($impostos as $id_imposto => $contas) {
        if ($contas['passivo'] > 0) {
            $insert[] = "('{$_REQUEST['prestador']}','{$id_imposto}','{$contas['passivo']}','{$dre}','1')";
        }
    }
    $insert = "INSERT INTO contabil_contas_assoc_prestador (`id_prestador`, `id_imposto`, `id_conta_passivo`, `id_conta_dre`, `status`) VALUES " . implode(', ', $insert) . ";";
    mysql_query($insert);
}

function carregaAssocImpostos($id_prestador) {
    $query = "SELECT * FROM contabil_contas_assoc_prestador WHERE id_prestador = $id_prestador";
    $result = mysql_query($query) or die($query . '<br>' . mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
        $retorno[$row['id_imposto']] = ['passivo' => $row['id_conta_passivo'], 'dre' => $row['id_conta_dre']];
    }
    return $retorno;
}

function getTipo($id_subgrupo) {
    $option[-1] = 'Selecione';

    $subgrupo = $id_subgrupo;

    $sel_subgrupo = mysql_query("SELECT id_subgrupo FROM entradaesaida_subgrupo WHERE id = {$subgrupo}");
    $res_subgrupo = mysql_fetch_assoc($sel_subgrupo);

    $sub = $res_subgrupo['id_subgrupo'];

    if ($sub != "") {
        $query = mysql_query("SELECT id_entradasaida, cod, nome, id_entradasaida FROM  entradaesaida WHERE cod LIKE '$sub%'");
        while ($row = mysql_fetch_assoc($query)) {
            $option[$row['id_entradasaida']] = $row['id_entradasaida'] . ' - ' . $row['cod'] . ' - ' . $row['nome'];
        }
    }
    return $option;
}

// get tipos
if ($_REQUEST['method'] === 'getTipo') {
    $array = getTipo($_REQUEST['id_sub']);
    foreach ($array as $key => $value) {
        $option .= utf8_encode('<option value="' . $key . '" >' . $value . ' </option>');
    }

    exit($option);
}

$Acoes = NEW Acoes();

$usuario = carregaUsuario();
$especialidade = isset($_POST['c_especialidade']) ? $_POST['c_especialidade'] : NULL;
//$id_prestador_post = isset($_REQUEST['prestador']) ? $_REQUEST['prestador'] : 1291;
$id_prestador_post = isset($_REQUEST['prestador']) ? $_REQUEST['prestador'] : NULL;

$dev = FALSE;

if (isset($id_prestador_post) && !empty($id_prestador_post)) {
    $prestador = mysql_fetch_assoc(PrestadorServico::getPrestador($id_prestador_post));

    $sql_master = "SELECT * FROM regioes WHERE id_regiao=$prestador[id_regiao]";
    $regiao_result = mysql_fetch_array(mysql_query($sql_master));

    $cnae_query = "SELECT id_cnae, codigo, descricao FROM cnae WHERE id_cnae=$prestador[id_cnae]";
    $cnae_select = mysql_fetch_array(mysql_query($cnae_query));

    $id_prestador = $prestador['id_prestador'];
    $projeto = montaQueryFirst("projeto", "*", "id_projeto={$prestador['id_projeto']}");
    $regiao = montaQueryFirst("regioes", "*", "id_regiao={$prestador['id_regiao']}");
    $socios = montaQuery("prestador_socio", "*", "id_prestador = {$id_prestador}");

    $dependentes = montaQuery("prestador_dependente", "*", "prestador_id = {$id_prestador}");
    $saidas = montaQuery("saida", "*", "id_prestador = {$id_prestador}");

    //Contagem de todos os dependentes cadastrados
    $num_socios = (count($socios) > 0 ) ? count($socios) : 1;
    $num_dependentes = (count($dependentes) > 0 ) ? count($dependentes) : 1;
    $_SESSION['voltarPrestador']['id_regiao'] = $prestador['id_regiao'];
    $_SESSION['voltarPrestador']['id_projeto'] = $prestador['id_projeto'];

    //Verificação para ver se já foi encerrado anteriormente
    //para que o update não mude o id de quem encerrou
    $encerrado = (!empty($prestador['encerrado_em'])) ? $prestador['encerrado_por'] : NULL;

    //MOVENDO OS SÓCIOS DA TABELA prestadorservico PARA A TABELA prestador_socio
    if (!empty($prestador['co_responsavel_socio1'])) {
        $qr_inserir_socio = mysql_query("INSERT INTO prestador_socio(nome,tel,id_prestador) VALUES('{$prestador['co_responsavel_socio1']}','{$prestador['co_tel_socio1']}','{$id_prestador}')");
        $qr_deletar_socio = mysql_query("UPDATE prestadorservico
                SET co_responsavel_socio1 = NULL,
                co_tel_socio1 = NULL,
                co_fax_socio1 = NULL,
                co_civil_socio1 = NULL,
                co_nacionalidade_socio1 = NULL,
                co_email_socio1 = NULL,
                co_email_socio1 = NULL,
                co_municipio_socio1 = NULL,
                data_nasc_socio1 = NULL
                WHERE id_prestador = '$id_prestador'
                LIMIT 1
                ");
    }

    if (!empty($prestador['co_responsavel_socio2'])) {
        $qr_inserir_socio = mysql_query("INSERT INTO prestador_socio(nome,tel, id_prestador) 
                VALUES('{$prestador['co_responsavel_socio2']}','{$prestador['co_tel_socio2']}','{$id_prestador}')");
        $qr_deletar_socio = mysql_query("UPDATE prestadorservico
                SET co_responsavel_socio2 = NULL,
                co_tel_socio2 = NULL,
                co_fax_socio2 = NULL,
                co_civil_socio2 = NULL,
                co_nacionalidade_socio2 = NULL,
                co_email_socio2 = NULL,
                co_email_socio2 = NULL,
                co_municipio_socio2 = NULL,
                data_nasc_socio2 = NULL
                WHERE id_prestador = '$id_prestador'
                LIMIT 1
                ");
    }


    $servico_assoc = "SELECT a.*,b.codigo,b.descricao FROM nfse_codigo_servico_assoc a
        LEFT JOIN nfse_codigo_servico b ON (a.id_codigo_servico = b.id)
        WHERE id_prestador = $id_prestador";
    $result = mysql_query($servico_assoc) or die($servico_assoc . ' - ' . mysql_error());
    $nfse_codigo_servico_assoc = mysql_fetch_assoc($result);
//    print_array($nfse_codigo_servico_assoc);

    $assoc_impostos = carregaAssocImpostos($id_prestador);
} else {
    $saidas = array();
    $_SESSION['voltarPrestador']['id_regiao'] = $_REQUEST['regiao'];
    $_SESSION['voltarPrestador']['id_projeto'] = $_REQUEST['projeto'];
    $prestador = array();
    $socios = array();
    $dependentes = array();
    $num_socios = 1;
    $num_dependentes = 1;
}

if (isset($_REQUEST['cadastrar'])) {
    $array['id_projeto'] = $_REQUEST['projeto'];
    $array['id_regiao'] = $_REQUEST['regiao'];
    $array['id_medida'] = $_REQUEST['id_medida'];
    $array['aberto_por'] = $_COOKIE['logado'];
    $array['contratado_em'] = implode("-", array_reverse(explode("/", $_REQUEST['contratado_em'])));
    $array['encerrado_em'] = implode("-", array_reverse(explode("/", $_REQUEST['encerrado_em'])));
    $array['contratante'] = RemoveCaracteresGeral($_REQUEST['contratante']);
    $array['endereco'] = $_REQUEST['endereco'];
    $array['cnpj'] = $_REQUEST['cnpj'];
    $array['responsavel'] = RemoveCaracteresGeral($_REQUEST['responsavel']);
    $array['civil'] = $_REQUEST['civil'];
    $array['nacionalidade'] = $_REQUEST['nacionalidade'];
    $array['formacao'] = $_REQUEST['formacao'];
    $array['rg'] = $_REQUEST['rg'];
    $array['cpf'] = $_REQUEST['cpf'];
    $array['c_fantasia'] = RemoveCaracteresGeral($_REQUEST['c_fantasia']);
    $array['c_razao'] = RemoveCaracteresGeral($_REQUEST['c_razao']);
    $array['c_endereco'] = $_REQUEST['c_endereco'];
    $array['c_cnpj'] = $_REQUEST['c_cnpj'];
    $array['c_ie'] = $_REQUEST['c_ie'];
    $array['c_im'] = $_REQUEST['c_im'];
    $array['c_tel'] = $_REQUEST['c_tel'];
    $array['c_fax'] = $_REQUEST['c_fax'];
    $array['c_responsavel'] = RemoveCaracteresGeral($_REQUEST['c_responsavel']);
    $array['c_civil'] = $_REQUEST['c_civil'];
    $array['c_nacionalidade'] = $_REQUEST['c_nacionalidade'];
    $array['c_formacao'] = $_REQUEST['c_formacao'];
    $array['c_rg'] = $_REQUEST['c_rg'];
    $array['c_cpf'] = $_REQUEST['c_cpf'];
    $array['c_email'] = $_REQUEST['c_email'];
    $array['c_site'] = $_REQUEST['c_site'];
    $array['co_responsavel'] = RemoveCaracteresGeral($_REQUEST['co_responsavel']);
    $array['co_tel'] = $_REQUEST['co_tel'];
    $array['co_fax'] = $_REQUEST['co_fax'];
    $array['co_civil'] = $_REQUEST['co_civil'];
    $array['co_nacionalidade'] = $_REQUEST['co_nacionalidade'];
    $array['co_municipio'] = $_REQUEST['co_municipio'];
    $array['numero'] = $_REQUEST['numero'];
    $array['co_email'] = $_REQUEST['co_email'];
    $array['objeto'] = $_REQUEST['objeto'];
    $array['assunto'] = $_REQUEST['objeto'];
    $array['valor'] = str_replace(".", "", $_REQUEST['valor']);
    $array['prestador_tipo'] = $_REQUEST['prestador_tipo'];
    $array['id_cnae'] = $_REQUEST['prestador_tipo_servico'];
    $array['nome_banco'] = $_REQUEST['nome_banco'];
    $array['agencia'] = $_REQUEST['agencia'];
    $array['agencia_dv'] = $_REQUEST['agencia_dv'];
    $array['conta'] = $_REQUEST['conta'];
    $array['conta_dv'] = $_REQUEST['conta_dv'];
    $array['prestacao_contas'] = $_REQUEST['prestacao_contas'];
    $array['c_cep'] = $_REQUEST['c_cep'];
    $array['c_id_tp_logradouro'] = $_REQUEST['c_id_tp_logradouro'];
    $array['c_bairro'] = addslashes($_REQUEST['c_bairro']);
    $array['c_numero'] = $_REQUEST['c_numero'];
    $array['c_complemento'] = $_REQUEST['c_complemento'];
    $array['c_uf'] = $_REQUEST['c_uf'];
    $array['c_cod_cidade'] = $_REQUEST['c_cod_cidade'];
    $array['especialidade'] = $_REQUEST['c_especialidade'];

    $cnae = explode('*', $_REQUEST['cnae']);
    $array['id_cnae'] = trim($cnae[1]);

    $cad = true;

    //validando data/periodo
    if ($array['encerrado_em'] != "") {
        if ($array['contratado_em'] > $array['encerrado_em']) {
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser maior que Data Final";
            $cad = false;
        } elseif ($array['contratado_em'] == $array['encerrado_em']) {
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser igual a Data Final";
            $cad = false;
        }
    }

    //validando cnpj
    $cnpj_val = validarCNPJ($array['c_cnpj']);
    if (!$cnpj_val) {
        $cor_msg = "message-red";
        $txt_msg = "CNPJ da Empresa Contratada Inválido";
        $cad = false;
    }

    //validando cpf
    $cpf_val = validaCPF($array['c_cpf']);
    if ($array['c_cpf'] != "") {
        if (!$cpf_val) {
            $cor_msg = "message-red";
            $txt_msg = "CPF do Responsável da Empresa Contratada Inválido";
            $cad = false;
        }
    }

    if($array['prestador_tipo'] == 3){
        $sqlVerificaCadastro = "
        SELECT * 
        FROM prestadorservico  A
        LEFT JOIN nfse_codigo_servico_assoc B ON (A.id_prestador = B.id_prestador)
        WHERE A.id_projeto = '{$array['id_projeto']}' AND A.c_cpf = '{$array['c_cpf']}' AND B.id_tipo_entradasaida = '{$_REQUEST['tipo']}' AND A.status = 1 AND B.status = 1";
    } else {
        $sqlVerificaCadastro = "
        SELECT * 
        FROM prestadorservico  A
        LEFT JOIN nfse_codigo_servico_assoc B ON (A.id_prestador = B.id_prestador)
        WHERE A.id_projeto = '{$array['id_projeto']}' AND A.c_cnpj = '{$array['c_cnpj']}' AND B.id_tipo_entradasaida = '{$_REQUEST['tipo']}' AND A.status = 1 AND B.status = 1";
    }
//    print_array($sqlVerificaCadastro);
    $rowVerificaCadastro = mysql_query($sqlVerificaCadastro);
    if(mysql_num_rows($rowVerificaCadastro) > 0){
        $cor_msg = "message-red";
        $txt_msg = "Já existe este CNPJ com esta associassão financeira cadastrado neste projeto!";
        $cad = false;
    }
//    echo "PASSOU";exit;

    if ($cad) {
        $array = array_filter($array);
        $colunas = implode(',', array_keys($array));
        $valores = implode("','", str_replace("'", "\'", array_values($array)));
        
        $sql_prestador = "INSERT INTO prestadorservico(aberto_em,$colunas) VALUES (NOW(),'$valores')";


        $qry_cnae_mais_usados = "UPDATE cnae SET mais_usados = 1 WHERE id_cnae = '{$array['id_cnae']}'";
        $sql_cnae_mais_usados = mysql_query($qry_cnae_mais_usados) or die(mysql_error());

        $insert_prestador = mysql_query($sql_prestador) or die(mysql_error());
        $id_prestador = mysql_insert_id();

        $nome_socios = $_REQUEST['nome_socio'];
        $tel_socios = $_REQUEST['tel_socio'];
        $cpf_socios = $_REQUEST['cpf_socio'];

        //Para cada sócio será realizado um cadastro
        for ($cont = 0; !empty($nome_socios[$cont]); $cont++) {
            $insert_socio = mysql_query("INSERT INTO prestador_socio(
                    nome,
                    tel,
                    cpf,
                    id_prestador
                    ) VALUES (
                    '$nome_socios[$cont]',
                    '$tel_socios[$cont]',
                    '$cpf_socios[$cont]',
                    '$id_prestador')");
        }

        $nome_dependentes = $_REQUEST['nome_dependente'];
        $tel_dependentes = $_REQUEST['tel_dependente'];
        $parentesco_dependentes = $_REQUEST['parentesco_dependente'];

        //Para cada dependente  irá realizar um cadastro
        for ($cont = 0; !empty($nome_dependentes[$cont]); $cont++) {
            $insert_dependente = mysql_query(" INSERT INTO prestador_dependente(prestador_id,
                    prestador_dep_nome,
                    prestador_dep_tel,
                    prestador_dep_parentesco,
                    prestador_dep_status
                    ) VALUES (
                    '$id_prestador',
                    '$nome_dependentes[$cont]',
                    '$tel_dependentes[$cont]',
                    '$parentesco_dependentes[$cont]',
                    '1'
                    )");
        }

        $tipo_retencao = mysql_query("SELECT * FROM retencao_tipo");
        while ($row_tipo_retencao = mysql_fetch_assoc($tipo_retencao)) {
            $valor = str_replace(',', '.', $_REQUEST[$row_tipo_retencao['slug']]);
            mysql_query("INSERT INTO retencao (id_prestador,id_retencao_tipo,valor) VALUES ({$id_prestador},{$row_tipo_retencao['id_retencao_tipo']},{$valor})");
        }

        $arr_assoc_finan = [
            'CodigoTributacaoMunicipio' => $_REQUEST['CodigoTributacaoMunicipio'],
            'id_prestador' => $id_prestador,
            'id_tipo_entradasaida' => $_REQUEST['tipo'],
            'id_subgrupo_entradasaida' => $_REQUEST['subgrupo'],
        ];
        if (!empty($_REQUEST['id_codigo_servico_assoc'])) {
            $arr_assoc_finan['id'] = $_REQUEST['id_codigo_servico_assoc'];
        }
        
        salva_nfse_codigo_servico_assoc($arr_assoc_finan);
        
        salvar_contas_assoc($id_prestador, $_REQUEST['imposto']);

        header('Location: index.php');
    }
}

if (isset($_REQUEST['editar'])) {
    //Código para editar

    $array['id_prestador'] = $id_prestador_post;
    $array['id_regiao'] = $_REQUEST['regiao'];
    $array['id_projeto'] = $_REQUEST['projeto'];
    $array['id_medida'] = $_REQUEST['id_medida'];
    $array['contratado_em'] = implode("-", array_reverse(explode("/", $_REQUEST['contratado_em'])));
    $array['encerrado_por'] = (!empty($_REQUEST['encerrado_por'])) ? $_REQUEST['encerrado_por'] : $_COOKIE['logado'];
    $array['encerrado_em'] = implode("-", array_reverse(explode("/", $_REQUEST['encerrado_em'])));
    $array['contratante'] = RemoveCaracteresGeral($_REQUEST['contratante']);
    $array['endereco'] = $_REQUEST['endereco'];
    $array['cnpj'] = $_REQUEST['cnpj'];
    $array['responsavel'] = RemoveCaracteresGeral($_REQUEST['responsavel']);
    $array['civil'] = $_REQUEST['civil'];
    $array['nacionalidade'] = $_REQUEST['nacionalidade'];
    $array['formacao'] = $_REQUEST['formacao'];
    $array['rg'] = $_REQUEST['rg'];
    $array['cpf'] = $_REQUEST['cpf'];
    $array['c_fantasia'] = RemoveCaracteresGeral($_REQUEST['c_fantasia']);
    $array['c_razao'] = RemoveCaracteresGeral($_REQUEST['c_razao']);
    $array['c_endereco'] = $_REQUEST['c_endereco'];
    $array['c_cnpj'] = $_REQUEST['c_cnpj'];
    $array['c_ie'] = $_REQUEST['c_ie'];
    $array['c_im'] = $_REQUEST['c_im'];
    $array['c_tel'] = $_REQUEST['c_tel'];
    $array['c_fax'] = $_REQUEST['c_fax'];
    $array['c_responsavel'] = RemoveCaracteresGeral($_REQUEST['c_responsavel']);
    $array['c_civil'] = $_REQUEST['c_civil'];
    $array['c_nacionalidade'] = $_REQUEST['c_nacionalidade'];
    $array['c_formacao'] = $_REQUEST['c_formacao'];
    $array['c_rg'] = $_REQUEST['c_rg'];
    $array['c_cpf'] = $_REQUEST['c_cpf'];
    $array['c_email'] = $_REQUEST['c_email'];
    $array['c_site'] = $_REQUEST['c_site'];
    $array['co_responsavel'] = RemoveCaracteresGeral($_REQUEST['co_responsavel']);
    $array['co_tel'] = $_REQUEST['co_tel'];
    $array['co_fax'] = $_REQUEST['co_fax'];
    $array['co_civil'] = $_REQUEST['co_civil'];
    $array['co_nacionalidade'] = $_REQUEST['co_nacionalidade'];
    $array['co_municipio'] = $_REQUEST['co_municipio'];
    $array['numero'] = $_REQUEST['numero'];
    $array['co_email'] = $_REQUEST['co_email'];
    $array['objeto'] = $_REQUEST['objeto'];
    $array['valor'] = str_replace(".", "", $_REQUEST['valor']);
    $array['prestador_tipo'] = $_REQUEST['prestador_tipo'];
    $array['id_cnae'] = $_REQUEST['prestador_tipo_servico'];
    $array['nome_banco'] = $_REQUEST['nome_banco'];
    $array['agencia'] = $_REQUEST['agencia'];
    $array['agencia_dv'] = $_REQUEST['agencia_dv'];
    $array['conta'] = $_REQUEST['conta'];
    $array['conta_dv'] = $_REQUEST['conta_dv'];
    $array['prestacao_contas'] = $_REQUEST['prestacao_contas'];
    $array['c_cep'] = $_REQUEST['c_cep'];
    $array['c_id_tp_logradouro'] = $_REQUEST['c_id_tp_logradouro'];
    $array['c_bairro'] = $_REQUEST['c_bairro'];
    $array['c_numero'] = $_REQUEST['c_numero'];
    $array['c_complemento'] = $_REQUEST['c_complemento'];
    $array['c_uf'] = $_REQUEST['c_uf'];
    $array['c_cod_cidade'] = $_REQUEST['c_cod_cidade'];
    $array['especialidade'] = $_REQUEST['c_especialidade'];

    $cnae = explode('*', $_REQUEST['cnae']);
    $array['id_cnae'] = trim($cnae[1]);

    $cad = true;

    //validando data/periodo
    if ($array['encerrado_em'] != "") {
        if ($array['contratado_em'] > $array['encerrado_em']) {
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser maior que Data Final";
            $cad = false;
        } elseif ($array['contratado_em'] == $array['encerrado_em']) {
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser igual a Data Final";
            $cad = false;
        }
    }

    //validando cnpj
    $cnpj_val = validarCNPJ($array['c_cnpj']);
    if (!$cnpj_val) {
        $cor_msg = "message-red";
        $txt_msg = "CNPJ da Empresa Contratada Inválido";
        $cad = false;
    }

    //validando cpf
    $cpf_val = validaCPF($array['c_cpf']);
    if ($array['c_cpf'] != "") {
        if (!$cpf_val) {
            $cor_msg = "message-red";
            $txt_msg = "CPF do Responsável da Empresa Contratada Inválido";
            $cad = false;
        }
    }
    
    if($array['prestador_tipo'] == 3){
        $sqlVerificaCadastro = "
        SELECT * 
        FROM prestadorservico  A
        LEFT JOIN nfse_codigo_servico_assoc B ON (A.id_prestador = B.id_prestador)
        WHERE A.id_projeto = '{$array['id_projeto']}' AND A.c_cpf = '{$array['c_cpf']}' AND B.id_tipo_entradasaida = '{$_REQUEST['tipo']}' AND A.status = 1 AND B.status = 1 AND A.id_prestador != '{$array['id_prestador']}'";
    } else {
        $sqlVerificaCadastro = "
        SELECT * 
        FROM prestadorservico  A
        LEFT JOIN nfse_codigo_servico_assoc B ON (A.id_prestador = B.id_prestador)
        WHERE A.id_projeto = '{$array['id_projeto']}' AND A.c_cnpj = '{$array['c_cnpj']}' AND B.id_tipo_entradasaida = '{$_REQUEST['tipo']}' AND A.status = 1 AND B.status = 1 AND A.id_prestador != '{$array['id_prestador']}'";
    }
//    print_array($sqlVerificaCadastro);
    $rowVerificaCadastro = mysql_query($sqlVerificaCadastro);
    if(mysql_num_rows($rowVerificaCadastro) > 0){
        $cor_msg = "message-red";
        $txt_msg = "Já existe este CNPJ com esta associassão financeira cadastrado neste projeto!";
        $cad = false;
    }

    if ($cad) {
        foreach ($array as $key => $value) {
            $x[] = "$key = '" . str_replace("'", "\'", $value) . "'";
        }

        $sets = implode(',', $x);
        $sql_update_prestacaoservico = "UPDATE prestadorservico 
                    SET $sets
                    WHERE id_prestador = '{$array['id_prestador']}'
                    LIMIT 1
                    ";

        if ($dev) {
            exit($sql_update_prestacaoservico);
        }

        $qr_update = mysql_query($sql_update_prestacaoservico) or die("UP prestador: " . mysql_error());

        $qry_cnae_mais_usados = "UPDATE cnae SET mais_usados = 1 WHERE id_cnae = '{$array['id_cnae']}'";
        $sql_cnae_mais_usados = mysql_query($qry_cnae_mais_usados) or die("UP cnae: " . mysql_error());

        $nome_socios = $_REQUEST['nome_socio'];
        $tel_socios = $_REQUEST['tel_socio'];
        $cpf_socios = $_REQUEST['cpf_socio'];
        $id_socios = $_REQUEST['id_socio'];
        $num_socios = count($id_socios);

        //Para cada sócio será realizado um cadastro
        for ($cont = 0; $cont < $num_socios; $cont++) {
            if (!empty($nome_socios[$cont]) && !empty($id_socios[$cont])) {

                $sql_update_prestador_socio = "UPDATE prestador_socio
                    SET nome = '$nome_socios[$cont]',
                    tel = '$tel_socios[$cont]',
                    cpf = '$cpf_socios[$cont]'
                    WHERE id_socio = '$id_socios[$cont]'
                    LIMIT 1
                    ";
                if ($dev) {
                    exit($sql_update_prestador_socio);
                }
                $update_socio = mysql_query($sql_update_prestador_socio) or die("UP socio: " . mysql_error());
            } else if (!empty($nome_socios[$cont]) && empty($id_socios[$cont])) {
                $insert_socio = mysql_query("INSERT INTO prestador_socio(
                    nome,
                    tel,
                    cpf,
                    id_prestador
                    ) VALUES (
                    '$nome_socios[$cont]',
                    '$tel_socios[$cont]',
                    '$cpf_socios[$cont]',
                    '$id_prestador')") or die("INS socio: " . mysql_error());
            }
        }

        $nome_dependentes = $_REQUEST['nome_dependente'];
        $tel_dependentes = $_REQUEST['tel_dependente'];
        $parentesco_dependentes = $_REQUEST['parentesco_dependente'];
        $id_dependentes = $_REQUEST['id_dependente'];
        $num_dependentes = count($id_dependentes);

        //Para cada dependente  irá realizar um cadastro
        for ($cont = 0; $cont < $num_dependentes; $cont++) {
            if (!empty($nome_dependentes[$cont]) && !empty($id_dependentes[$cont])) {

                $sql_update_dependente = "UPDATE prestador_dependente
                                                    SET prestador_id = '$id_prestador',
                                                    prestador_dep_nome = '$nome_dependentes[$cont]',
                                                    prestador_dep_tel = '$tel_dependentes[$cont]',
                                                    prestador_dep_parentesco = '$parentesco_dependentes[$cont]'
                                                    WHERE prestador_dep_id = '$id_dependentes[$cont]'
                                                    LIMIT 1
                                                    ";
                if ($dev) {
                    exit($sql_update_dependente);
                }
                $update_dependente = mysql_query($sql_update_dependente) or die("UP presDep: " . mysql_error());
            } else if (!empty($nome_dependentes[$cont]) && empty($id_dependentes[$cont])) {

                $sql_update_dependente = "INSERT INTO prestador_dependente(prestador_id,
                                                    prestador_dep_nome,
                                                    prestador_dep_tel,
                                                    prestador_dep_parentesco,
                                                    prestador_dep_status
                                                    ) VALUES (
                                                    '$id_prestador',
                                                    '$nome_dependentes[$cont]',
                                                    '$tel_dependentes[$cont]',
                                                    '$parentesco_dependentes[$cont]',
                                                    '1'
                                                    )";
                if ($dev) {
                    exit($sql_update_dependente);
                }

                $insert_dependente = mysql_query($sql_update_dependente) or die("INS presDep: " . mysql_error());

                $verificacao = mysql_query("SELECT * FROM retencao WHERE id_retencao_tipo = {$row_tipo_retencao['id_retencao_tipo']}");
                $total_verificacao = mysql_num_rows($verificacao);
                $valor = str_replace(',', '.', $_REQUEST[$row_tipo_retencao['slug']]);
                if ($total_verificacao) {
                    mysql_query("UPDATE retencao SET valor = {$valor} WHERE id_retencao_tipo = {$row_tipo_retencao['id_retencao_tipo']} AND id_prestador = {$id_prestador} LIMIT 1");
                } else {
                    mysql_query("INSERT INTO retencao (id_prestador,id_retencao_tipo,valor) VALUES ({$id_prestador},{$row_tipo_retencao['id_retencao_tipo']},{$valor})");
                }
            }

            $tipo_retencao = mysql_query("SELECT * FROM retencao_tipo");

            while ($row_tipo_retencao = mysql_fetch_assoc($tipo_retencao)) {
                $valor = str_replace(',', '.', $_REQUEST[$row_tipo_retencao['slug']]);
                mysql_query("INSERT INTO retencao (id_prestador,id_retencao_tipo,valor) VALUES ({$id_prestador},{$row_tipo_retencao['id_retencao_tipo']},{$valor})");
            }
//            var_dump($x);

            $arr_assoc_finan = [
                'CodigoTributacaoMunicipio' => $_REQUEST['CodigoTributacaoMunicipio'],
                'id_prestador' => $id_prestador,
                'id_tipo_entradasaida' => $_REQUEST['tipo'],
                'id_subgrupo_entradasaida' => $_REQUEST['subgrupo'],
            ];
            if (!empty($_REQUEST['id_codigo_servico_assoc'])) {
                $arr_assoc_finan['id'] = $_REQUEST['id_codigo_servico_assoc'];
            }

            salva_nfse_codigo_servico_assoc($arr_assoc_finan);

            salvar_contas_assoc($id_prestador, $_REQUEST['imposto']);

            header('Location: index.php');
        }
    }
}

//Array com os tipos de contrato
$arrTipos = array(
    "1" => "Pessoa Jurídica",
    "2" => "Pessoa Jurídica - Cooperativa",
    "3" => "Pessoa Física",
    "4" => "Pessoa Jurídica - Prestador de Serviço",
    "5" => "Pessoa Jurídica - Administradora",
    "6" => "Pessoa Jurídica - Publicidade",
    "7" => "Pessoa Jurídica Sem Retenção",
    "9" => "Pessoa Jurídica - Médico");

$arrTiposServicos = PrestadorServico::getListaServicos();

$temContrato = array("1" => "Sim", "0" => "Não");

$medidas = PrestadorServico::listMedidasForSelect();

$grauParentesco = montaQuery("grau_parentesco");

$optParentesco = array(0 => "« Selecione o Grau de Parentesco »");

//Array com os possíveis estados civis
$arrEstadoCivil = array(0 => "« Selecione um Estado Civil »", 1 => "Solteiro(a)", 2 => "Casado(a)", 3 => "Divorciado(a)", 4 => "Viúvo(a)");

//Montar um array com os tipos de graus de parentesco possiveis,
//retornados da tabela grau_parentesco
foreach ($grauParentesco as $value) {
    $optParentesco[$value['id_grau']] = $value['nome'];
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$regiaoR = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;

$result_contratante = mysql_query("SELECT * FROM master where id_master = '{$usuario['id_master']}'");
$row_contratante = mysql_fetch_array($result_contratante);


/* CHAMADA DOS CAMPOS DE DADOS FINANCEIROS */

//$grupos = mysql_query("SELECT * FROM entradaesaida_subgrupo WHERE entradaesaida_grupo IN (SELECT id_grupo FROM entradaesaida_grupo WHERE terceiro = 1);");
$grupos = mysql_query("SELECT * FROM entradaesaida_subgrupo WHERE entradaesaida_grupo >= 10;");
$subgrupo = array("-1" => "Selecione");
while ($rst = mysql_fetch_assoc($grupos)) {
    $subgrupo[$rst['id']] = ($rst['id_subgrupo'] . " - " . $rst['nome']);
}


/*
 * QUERY CNAE
 */
$cnae = mysql_query("SELECT * FROM cnae WHERE status = 1 ORDER BY codigo ASC") or die(mysql_error());
$opt_cnae[-1] = 'Selecione';
while ($row = mysql_fetch_array($cnae)) {
    $opt_cnae[$row['id_cnae']] = $row['codigo'] . ' - ' . $row['descricao'];
}

// aliquotas
$tipo_retencao = mysql_query("SELECT * FROM retencao_tipo");
while ($row_tipo_retencao = mysql_fetch_assoc($tipo_retencao)) {
    $arr_tipso_retencao[$row_tipo_retencao['id_retencao_tipo']] = $row_tipo_retencao;
}

$query = "SELECT * FROM retencao WHERE id_prestador = {$id_prestador} AND status = 1";
$result = mysql_query($query);
while ($valor_atual = mysql_fetch_assoc($result)) {
    $arr_retencao[$valor_atual['id_retencao_tipo']] = $valor_atual;
}

// query plano decontas
$qryPlanos = "SELECT * 
                FROM contabil_planodecontas 
                ORDER BY classificador";

$sqlPlanos = mysql_query($qryPlanos) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
$arrayPlanosCredor[] = $arrayPlanosDre[] = 'selecione';
while ($rowPlanos = mysql_fetch_assoc($sqlPlanos)) {
    $n = explode('.', $rowPlanos['classificador']);

    if ($n[0] < 3)
        $arrayPlanosCredor[$rowPlanos['id_conta']] = $rowPlanos['classificador'] . ' - ' . $rowPlanos['descricao'];
    else if ($n[0] >= 3)
        $arrayPlanosDre[$rowPlanos['id_conta']] = $rowPlanos['classificador'] . ' - ' . $rowPlanos['descricao'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>:: Intranet :: FORNECEDOR DE SERVIÇOS E PRODUTOS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../rh_novaintra//curso/jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        <!--<link href="../../js/autocomplete/chosen.jquery.css" rel="stylesheet" type="text/css" />-->
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="/intranet/resources/css/font-awesome.min.css"  rel="stylesheet" type="text/css"/>
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <?php if (!isset($_GET['dev'])) { ?>
            <script src="../../js/jquery.validationEngine-2-6-2..js" type="text/javascript"></script>
            <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <?php } ?>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <!--<script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>-->
        <!--<script src="../../js/autocomplete/chosen.jquery.js" type="text/javascript"></script>-->

        <script>

            function selectProjOption(options) {
                $('#projeto').html(options);
                $('#showLoading').remove();
<?php if (isset($prestador['id_projeto'])) { ?>
                    $('#projeto option[value=<?= $prestador['id_projeto']; ?>]').attr('selected', 'selected');
<?php } ?>
            }

            $(function () {

                $('.mask_float').maskMoney({thousands: '', decimal: ','});

                //autocomplete
//                $("#cnae").autocomplete("lista_servico.php", {
//                    width: 600,
//                    matchContains: false,
//                    minChars: 3,
//                    selectFirst: false
//                });
                $("#cnae").keyup(function () {
                    var cod = $('#cnae').val();
                    $(this).after('<i class="fa fa-spinner fa-spin form-control-feedback" id="loading"></i>');
                    $.post('lista_servico.php', {q: cod}, function (data) {
                        console.log(data);
                        $("#loading").remove();
                        $("#cnae").autocomplete({
                            source: data.servicos,
                            minLength: 3//,
//                            change: function (event, ui) {
//                                if (event.type == 'autocompletechange') {
//                                    var array_item = $("#CodigoTributacaoMunicipio").val().split(' - ');
//                                    $("#CodigoTributacaoMunicipio").val(array_item[0]);
//                                    $("#txt_servico").val(array_item[1]);
//                                }
//                            }
                        });
                    }, 'json');
                });

                $("#c_cep").mask("99999-999", {placeholder: " "});

                //AUTOCOMPLETE
                if ($(".chosen-select").length) {
                    $('.chosen-select').chosen();
                }
                if ($(".chosen-select-deselect").length) {
                    $('.chosen-select-deselect').chosen({allow_single_deselect: true});
                }

                /* carrega municípios para o campo cidade */
                $('#c_uf').change(function () {
                    var uf = $('#c_uf').val();
                    $('#c_cidade').val('');
                    $('#c_cod_cidade').val('');
                    $.post('../../busca_cep.php', {uf: uf, municipios: 1}, function (data) {

                        $("#c_cidade").autocomplete({source: data.municipios,
                            change: function (event, ui) {
                                if (event.type == 'autocompletechange') {
                                    var valor_municipio = ui.item.value.split(')-');
                                    $('#c_cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                                    $('#c_cidade').val(valor_municipio[1].trim());
                                }
                            }
                        });

                    }, 'json');
                });
                var cep_atual = $('#c_cep').val().replace("-", "").replace(".", "");
                var numero_atual = $('#c_numero').val();
                var complemento_atual = $('#c_complemento').val();
		//INICO VIA CEP
			//INICIO CEP

	 function limpa_formulario_cep() {
		// Limpa valores do formulÃ¡rio de cep.
		$("#c_endereco").val("");
		$("#c_bairro").val("");
		$("#c_cidade").val("");
		$("#c_uf").val("");
		//$("#ibge").val("");
	    }
			 //Quando o campo cep perde o foco.
	    $("#c_cep").blur(function() {
		//alert('dentro');return false;

		//Nova variÃ¡vel "cep" somente com dÃ­gitos.
		var cep = $(this).val().replace(/\D/g, '');

		//Verifica se campo cep possui valor informado.
		if (cep != "") {

				//ExpressÃ£o regular para validar o CEP.
		    var validacep = /^[0-9]{8}$/;

		    //Valida o formato do CEP.
		    if(validacep.test(cep)) {
			$('#cod_tp_logradouro').val("...");
                                    $('#c_endereco').val("...");
                                    $('#c_bairro').val("...");
                                    $('#c_uf').val("...");
                                    $('#c_cidade').val("...");
                                    $('#c_cidade').val("...");
			//Preenche os campos com "..." enquanto consulta webservice.
		
			//Consulta o webservice viacep.com.br/

			$.getJSON("//viacep.com.br/ws/"+ cep +"/json/?callback=?", function(data) {

			    if (!("erro" in data)) {
				
				    $('#c_id_tp_logradouro').val(data.cod_tp_logradouro);
                                    $('#c_endereco').val(data.logradouro);
                                    $('#c_bairro').val(data.bairro);
                                    $('#c_uf').val(data.uf);
                                    $('#c_cidade').val(data.localidade);
                                    $('#c_cidade').val(data.id_municipio);
				//Atualiza os campos com os valores da consulta.
	/*
                $('#c_cep').blur(function () {

                    $this = $(this);
                    $this.after('<img src="../../img_menu_principal/loader_pequeno.gif" alt="buncando endereço..." style="position: absolute; margin-top: -7px;" id="img_load_cep" />');
                    $('#c_id_tp_logradouro').attr('disabled', 'disabled');
                    $('#c_endereco').attr('disabled', 'disabled');
                    $('#c_bairro').attr('disabled', 'disabled');
                    $('#c_uf').attr('disabled', 'disabled');
                    $('#c_cidade').attr('disabled', 'disabled');*/
			    } //end if.
			    else {
				//CEP pesquisado nÃ£o foi encontrado.
								 limpa_formulario_cep();
				//alert("CEP nÃ£o encontrado.");
				bootAlert('CEP não encontrado!', 'Alerta', "Null", 'danger');        
			    }
			});
		    } //end if.
		    else {
			//cep Ã© invÃ¡lido.
			limpa_formulario_cep();
			bootAlert('Formato de CEP inválido!', 'Alerta', "Null", 'danger');        
		    }
		} //end if.
		else {
		    //cep sem valor, limpa formulÃ¡rio.
					 limpa_formulario_cep();
		}
	    });
	//FIM CEP 
	//FIM VIA CEP
			
		
		/*
                $('#c_cep').blur(function () {

                    $this = $(this);
                    $this.after('<img src="../../img_menu_principal/loader_pequeno.gif" alt="buncando endereço..." style="position: absolute; margin-top: -7px;" id="img_load_cep" />');
                    $('#c_id_tp_logradouro').attr('disabled', 'disabled');
                    $('#c_endereco').attr('disabled', 'disabled');
                    $('#c_bairro').attr('disabled', 'disabled');
                    $('#c_uf').attr('disabled', 'disabled');
                    $('#c_cidade').attr('disabled', 'disabled');

                    var cep = $this.val();
                    $.post('../../busca_cep.php', {cep: cep, id_municipio: 1, municipios: 1}, function (data) {

                        $('#c_id_tp_logradouro').removeAttr('disabled');
                        $('#c_endereco').removeAttr('disabled');
                        $('#c_bairro').removeAttr('disabled');
                        $('#c_uf').removeAttr('disabled');
                        $('#c_cidade').removeAttr('disabled');
                        $('#img_load_cep').remove();
			
			
                        if (data.cep == '') {
                            alert('Cep não encontrado!');
                        } else {
                            $("#c_cidade").autocomplete({source: data.municipios,
                                change: function (event, ui) {
                                    if (event.type == 'autocompletechange') {
                                        var valor_municipio = ui.item.value.split(')-');
                                        $('#c_cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                                        $('#c_cidade').val(valor_municipio[1].trim());
                                    }
                                }
                            });
                            $('#c_id_tp_logradouro').val(data.id_tp_logradouro);
                            $('#c_endereco').val(data.logradouro);
                            $('#c_bairro').val(data.bairro);
                            $('#c_uf').val(data.uf);
                            $('#c_cidade').val(data.cidade);
                            $('#c_cod_cidade').val(data.id_municipio);

                            if (data.cep == cep_atual) {
                                $('#c_numero').val(numero_atual);
                                $('#c_complemento').val(complemento_atual);
                            } else {
                                $('#c_numero').val('');
                                $('#c_complemento').val('');
                            }
                        }

                    }, 'json');

                });*/


                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"},
                        selectProjOption
                        , "projeto");


                $(".data").mask("99/99/9999");
                $(".cpf").mask("999.999.999-99");
                $("#c_cpf").mask("999.999.999-99");
                $(".cnpj").mask("99.999.999/9999-99");
                $(".tel").mask("(99)9999-9999?9");

                $("#valor").maskMoney({prefix: 'R$ ', allowNegative: true, thousands: '.', decimal: ','});

                $("#form1").validationEngine({promptPosition: "topRight"});

                var conts = 20;

                var contd = 20;

                var removeSocio = '';

                $("#adicionar_socio").click(function () {
                    var clone = $("#socio1").clone();
                    conts++;
                    clone.attr("id", "socio" + conts);
                    clone.find("input").val("");

                    $(clone).appendTo("#socios");
                    $(".tel").unmask("(99)9999-9999?9");
                    $(".cpf").unmask("999.999.999-99");
                    $(".tel").mask("(99)9999-9999?9");
                    $(".cpf").mask("999.999.999-99");
                    removeSocio = $(clone).attr('id').slice(-2);
                });

                $("#remover_socio").on('click', function () {
                    $('#socio' + removeSocio).remove();
                    removeSocio--;
                });

                $("#adicionar_dependente").click(function () {
                    var clone = $("#dependente1").clone();
                    contd++;
                    clone.attr("id", "dependente" + contd);
                    clone.find("input").val("");
                    $(clone).appendTo("#dependentes");
                    $(".tel").unmask("(99)9999-9999?9");
                    $(".tel").mask("(99)9999-9999?9");
                });


                //url
                $("#c_site").click(function () {
                    if ($(this).val() == '') {
                        $(this).val('http://www.');
                    }
                });
                $("#c_site").blur(function () {
                    if ($(this).val() == 'http://www.') {
                        $(this).val('');
                    }
                });

//                options_unidade_medida = $('.unidade_medida_a').children('option')


                $('#c_especialidade').change(function (e) {
                    if ($(this).val() == '') {
                        $('#p_especialidade').append('<input type="text" id="c_especialidade_fake" value="" />');
                    } else {
                        $('#c_especialidade_fake').remove();
                    }
                });
                if ($('#c_especialidade').val() == '') {
                    $('#p_especialidade').append('<input type="text" id="c_especialidade_fake" value="<?= $prestador['especialidade']; ?>" />');
                } else {
                    $('#c_especialidade_fake').remove();
                }
                $("#c_especialidade_fake").blur(function () {
                    $('#c_especialidade option').each(function () {
                        $(this).removeAttr('selected');
                    });

                    val = $('#c_especialidade_fake').val();
                    $('#c_especialidade').append('<option value="' + val + '" selected="selected">' + val + '</option>');
                });

                $(".selecionar").click(function () {
                    var id = $(this).data("id");
                    if ($(this).is(':checked')) {
                        $("#" + id).prop("disabled", false);
                    } else {
                        $("#" + id).prop("disabled", true);
                    }
                });

                $('body').on('change', '#subgrupo', function () {
                    $.post('#', {method: 'getTipo', id_sub: $(this).val()}, function (data) {
                        $("#tipo").html(data);
                    });
                });

                $("#CodigoTributacaoMunicipio").keyup(function () {
                    var cod = $('#CodigoTributacaoMunicipio').val();
                    $(this).after('<i class="fa fa-spinner fa-spin form-control-feedback" id="loading"></i>');
                    $.post('/intranet/compras/notas_fiscais/nfse_atualiza.php', {method: 'cosulta_cod_servico', cod: cod}, function (data) {
                        $("#loading").remove();
                        $("#CodigoTributacaoMunicipio").autocomplete({
                            source: data.servicos,
                            minLength: 3,
                            change: function (event, ui) {
                                if (event.type == 'autocompletechange') {
                                    var array_item = $("#CodigoTributacaoMunicipio").val().split(' :: ');
                                    $("#CodigoTributacaoMunicipio").val(array_item[0]);
                                    $("#txt_servico").val(array_item[1]);
                                }
                            }
                        });
                    }, 'json');
                });
                

                $("body").on("change", "#prestacao_contas", function () {
                    if ($(this).val() == 1) {
                        console.log("A");
                        $("#numero").addClass("validate[required]");
                        $("#objeto").addClass("validate[required]");
                    } else {
                        console.log("B");
                        $("#numero").removeClass("validate[required]");
                        $("#objeto").removeClass("validate[required]");
                    }
                });


            });



        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
                min-height: 0px;
                border-right: 0px;
                margin-right: 0px;
            }
            .colDir{
                width: auto;
                min-width: 0px;
                margin-left: 0px;
                min-height: 0px;
                border: 0px solid #ccc;
                padding: 0px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
            .contratante, .contratada, .socios, .medicos, .dados_projeto{
                display: none;
            }
            input[readonly=readonly] {
                background: #E2E2E2;
            }
            input[readonly=true] {
                background: #E2E2E2;
            }
            .box-medicos { display: none; }



        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 950px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>FORNECEDOR DE SERVIÇOS E PRODUTOS</h2>
                    </div>
                </div>

                <div id="message-box" class="<?php echo $cor_msg; ?> alinha2">
                    <?php echo $txt_msg; ?>
                </div>

                <div class='message-box message-red dados_projeto'></div>
                <fieldset>
                    <legend>Dados do Projeto</legend>
                    <?php if (count($saidas) == 0) { ?>
                        <div class="colEsq" style="margin-top:0;">
                            <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />     
    <!--                            <p><label class="first">Região:</label> <?php // echo montaSelect(GlobalClass::carregaRegioes($regiao_result['id_master']), $prestador['id_regiao'], "id='regiao' name='regiao' class='validate[custom[select]]' style='width: 300px;'")                           ?></p>-->
                            <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $prestador[id_regiao], "id='regiao' name='regiao' class='validate[custom[select]]' style='width: 300px;'") ?></p>                            
                            <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $prestador['id_projeto'], "id='projeto' name='projeto' class='validate[custom[select]]' style='width: 300px;'") ?></p>
                        </div>
                    <?php } else { ?>
                        <input type="hidden" name="regiao" id="projeto_pre" value="<?php echo $prestador['id_regiao']; ?>" />
                        <input type="hidden" name="projeto" id="regiao_pre" value="<?php echo $prestador['id_projeto']; ?>" />
                    <?php } ?>
                    <?php // if($Acoes->verifica_permissoes(96)){    ?>
                    <div class="colDir">
                        <p><label class='first'>Data Inicio:</label><input type="text" name="contratado_em" id="contratado_em" value="<?php echo $prestador['contratado_embr'] ?>" class="data validate[required,custom[dateBr]] date_f" /></p>
                        <p><label class='first'>Data Final:</label><input type="text" name="encerrado_em" id="encerrado_em" value="<?php echo $prestador['encerrado_embr'] ?>" class="data date_f validate[custom[dateBr]]" /></p>
                    </div>
                    <?php // } else {       ?>
                    <!--                    <div class="colDir">
                        <p><label class='first'>Data Inicio:</label><?php echo $prestador['contratado_embr'] ?><input type="hidden" name="contratado_em" id="contratado_em" value="<?php echo $prestador['contratado_embr'] ?>" /></p>
                        <p><label class='first'>Data Final:</label><?php echo $prestador['encerrado_embr'] ?><input type="hidden" name="encerrado_em" id="encerrado_em" value="<?php echo $prestador['encerrado_embr'] ?>" /></p>
                                        </div>    -->
                    <?php // }       ?>
                </fieldset>
                <div class='message-box message-red contratante'></div>
                <?php
                $dados_prestador = array();
                if (isset($id_prestador_post) && !empty($id_prestador_post)) {
                    $dados_prestador['contratante'] = $prestador['contratante'];
                    $dados_prestador['endereco'] = $prestador['endereco'];
                    $dados_prestador['cnpj'] = $prestador['cnpj'];
                    $dados_prestador['responsavel'] = $prestador['responsavel'];
                    $dados_prestador['nacionalidade'] = $prestador['nacionalidade'];
                    $dados_prestador['rg'] = $prestador['rg'];
                    $dados_prestador['estado_civil'] = $prestador['civil'];
                    $dados_prestador['formacao'] = $prestador['formacao'];
                    $dados_prestador['cpf'] = $prestador['cpf'];
                } else {
                    $dados_prestador['contratante'] = $row_contratante['razao'];
                    $dados_prestador['endereco'] = $row_contratante['endereco'];
                    $dados_prestador['cnpj'] = $row_contratante['cnpj'];
                    $dados_prestador['responsavel'] = $row_contratante['responsavel'];
                    $dados_prestador['nacionalidade'] = $row_contratante['nacionalidade'];
                    $dados_prestador['estado_civil'] = $row_contratante['civil'];
                    $dados_prestador['rg'] = $row_contratante['rg'];
                    $dados_prestador['formacao'] = $row_contratante['formacao'];
                    $dados_prestador['cpf'] = $row_contratante['cpf'];
                }
                ?>
                <!--<fieldset>
                    <legend>Dados do Contratante</legend>                    
                    <p><label class='first'>Contratante: </label><input type="text" size="98" name="contratante" readonly="readonly" value="<?php echo $dados_prestador['contratante'] ?>" /></p>
                    <p><label class='first'>Endereço:</label><input type="text" size="98" name="endereco" readonly="readonly" value="<?php echo $dados_prestador['endereco'] ?>" /></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><input type="text" name="cnpj"  readonly="readonly" value="<?php echo $dados_prestador['cnpj'] ?>" size="16" class="cnpj" /></p>
                        <p><label class='first'>Responsavel:</label><input type="text" name="responsavel" readonly="readonly"  value="<?php echo $dados_prestador['responsavel'] ?>" size="38" /></p>
                        <p><label class='first'>Nascionalidade:</label><input type="text" name="nacionalidade" readonly="readonly" value="<?php echo $dados_prestador['nacionalidade'] ?>" size="16" /></p>
                        <p><label class='first'>RG:</label><input type="text" name="rg" readonly="readonly" value="<?php echo $dados_prestador['rg'] ?>" size="16" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><input type="text" name="estado_civil" readonly="readonly"  value="<?php echo $dados_prestador['estado_civil'] ?>" size="16" /></p>
                        <p><label class='first'>Formação:</label><input type="text" name="formacao"  readonly="readonly" value="<?php echo $dados_prestador['formacao'] ?>" size="24" /></p>
                        <p><label class='first'>CPF:</label><input type="text" name="cpf" readonly="readonly" value="<?php echo $dados_prestador['cpf'] ?>" size="24" class="cpf" /></p>
                    </div>
                </fieldset>-->

                <div class='message-box message-red contratada'></div>
                <fieldset>
                    <legend>Dados da Empresa Contratada</legend>
                    <p><label class='first'>Nome Fantasia:</label><input type="text" name="c_fantasia" id="c_fantasia" value="<?php echo $prestador['c_fantasia'] ?>" size="98" class="" /></p>
                    <p><label class='first'>Razão Social:</label><input type="text" name="c_razao" id="c_razao" value="<?php echo $prestador['c_razao'] ?>" size="98" class="" /></p>
                    <p>
                        <label class='first'>CEP:</label><input type="text" name="c_cep" id="c_cep" value="<?= $prestador['c_cep'] ?>" maxlength="9" size="10" class=""/>
                        <input name="c_id_tp_logradouro" id="c_id_tp_logradouro" type="hidden" value="<?= $prestador['c_id_tp_logradouro'] ?>"  />
                        <label class='first'>Endereço:</label><input type="text" name="c_endereco" id="c_endereco" value="<?php echo $prestador['c_endereco'] ?>" size="64" />
                    </p>
                    <p>
                        <label class='first'>Nº:</label><input type="text" name="c_numero" id="c_numero" value="<?php echo $prestador['c_numero'] ?>" size="3" />
                        <label class='first'>Compl.:</label><input type="text" name="c_complemento" id="c_complemento" value="<?php echo $prestador['c_complemento'] ?>" size="22" />
                        <label class='first'>Bairro:</label><input type="text" name="c_bairro" id="c_bairro" value="<?php echo $prestador['c_bairro'] ?>" size="25" />
                    </p>
                    <p>
                        <label class='first'>UF:</label>
                        <select name="c_uf" id="c_uf" class="uf_select" data-tipo="cidade">
                            <option value=""></option>
                            <?php
                            $qr_uf = mysql_query("SELECT * FROM uf");
                            while ($row_uf = mysql_fetch_assoc($qr_uf)) {
                                $selected = ($prestador['c_uf'] == $row_uf['uf_sigla']) ? "selected" : "";
                                echo '<option value="' . $row_uf['uf_sigla'] . '" ' . $selected . '>' . $row_uf['uf_sigla'] . '</option>';
                            }
                            ?>    
                        </select>
                        <label class='first'>Cidade:</label>
                        <input name="c_cod_cidade" id="c_cod_cidade" type="hidden" value="<?= $prestador['c_cod_cidade'] ?>"/>
                        <input name="c_cidade" id="c_cidade" type="text" value="<?php echo $prestador['c_cidade'] ?>" size="40" class=""  />
                    </p>
                    <p><label class='first'>Tipo de contrato:</label><?php echo montaSelect($arrTipos, $prestador['prestador_tipo'], "id='prestador_tipo' name='prestador_tipo' class='validate[required]'") ?></p>
                    <p><label class='first'>CNAE principal:</label><input type="text" name="cnae" id="cnae" value="<?= $cnae_select['codigo']. ' - '.$cnae_select['descricao']. ' * '. $cnae_select['id_cnae'] ?>" size="80" class="form-control validate[required]" placeholder="Ex: Fabricação de Vinho" /></p>
                    <!--<?php echo montaSelect($arrTiposServicos, $prestador['id_cnae'], "id='prestador_tipo_servico' name='prestador_tipo_servico' class='chosen-select-deselect'") ?>-->

                    <?php
                    $arrEspecialidades = array('' => 'OUTRO', 'AMBULATORIAL' => 'Ambulatorial', 'HOSPITALAR' => 'Hospitalar');
                    ?>
                    <p id="p_especialidade"><label class='first'>Especialidade:</label><?php echo montaSelect($arrEspecialidades, $prestador['especialidade'], " name='c_especialidade' id='c_especialidade' ") ?></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><input type="text" name="c_cnpj" id="c_cnpj" value="<?php echo $prestador['c_cnpj'] ?>" size="17" class="cnpj" /></p>
                        <p><label class='first'>IM:</label><input type="text" name="c_im" id="c_im" value="<?php echo $prestador['c_im'] ?>" size="17" /></p>
                        <p><label class='first'>Fax:</label><input type="text" name="c_fax" id="c_fax" value="<?php echo $prestador['c_fax'] ?>" size="17" class="tel" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>IE:</label><input type="text" name="c_ie" id="c_ie" value="<?php echo $prestador['c_ie'] ?>" size="12" /></p>
                        <p><label class='first'>Telefone:</label><input type="text" name="c_tel" id="c_tel" value="<?php echo $prestador['c_tel'] ?>" size="12" class="tel"/></p>
                    </div>
                    <p class="clear valid_email"><label class='first'>E-mail:</label><input type="text" name="c_email" id="c_email" value="<?php echo $prestador['c_email'] ?>" size="98" class="validate[custom[email]]" /></p>

                    <div class="colEsq">
                        <p><label class='first'>Responsavel:</label><input type="text" name="c_responsavel" id="c_responsavel" value="<?php echo $prestador['c_responsavel'] ?>" size="35" class="" /></p>
                        <p><label class='first'>Nascionalidade:</label><input type="text" name="c_nacionalidade" id="c_nacionalidade" value="<?php echo $prestador['c_nacionalidade'] ?>" size="35" /></p>
                        <p><label class='first'>RG:</label><input type="text" name="c_rg" id="c_rg" value="<?php echo $prestador['c_rg'] ?>" size="15" class="" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><?php echo montaSelect($arrEstadoCivil, $prestador['c_civil'], "id='c_civil' name='c_civil'") ?></p>
                        <p><label class='first'>Formação:</label><input type="text" name="c_formacao" id="c_formacao" value="<?php echo $prestador['c_formacao'] ?>" size="24" /></p>
                        <p><label class='first'>CPF:</label><input type="text" name="c_cpf" id="c_cpf" class="" value="<?php echo $prestador['c_cpf'] ?>" size="15" /></p>
                    </div>

                    <p class="clear"><label class='first'>Site:</label><input type="text" name="c_site" id="c_site" value="<?php echo $prestador['c_site'] ?>" size="98" class="" /></p>
                </fieldset>

                <fieldset>
                    <legend>Dados da pessoa de contato na contratada</legend>
                    <p><label class='first'>Nome Completo:</label><input type="text" name="co_responsavel" id="co_responsavel" value="<?php echo $prestador['co_responsavel'] ?>" size="98" class=""/></p>
                    <p class="clear valid_email"><label class='first'>Email:</label><input type="text" name="co_email" id="co_email" value="<?php echo $prestador['co_email'] ?>" size="98" class="" /></p>
                    <div class="colEsq">
                        <p><label class='first'>Telefone:</label><input type="text" name="co_tel" id="co_tel" value="<?php echo $prestador['co_tel'] ?>" size="12" class="tel" /></p>
                        <p><label class='first'>Estado Civil:</label><?php echo montaSelect($arrEstadoCivil, $prestador['co_civil'], "id='co_civil' name='co_civil'") ?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Fax:</label><input type="text" name="co_fax" id="co_fax" value="<?php echo $prestador['co_fax'] ?>" size="12" class="tel" /></p>
                        <p><label class='first'>Nacionalidade:</label><input type="text" name="co_nacionalidade" id="co_nacionalidade" value="<?php echo $prestador['co_nacionalidade'] ?>" size="24" /></p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Dados do contrato</legend>
                    <!--<p><label class='first-2'>Tem contrato?</label><?php // echo montaSelect($temContrato, $prestador['prestacao_contas'], "id='prestacao_contas' name='prestacao_contas'")                                               ?></p>-->

                    <p>
                        <label class='first-2'>Tem contrato?</label>
                        <select id="prestacao_contas" name="prestacao_contas">
                            <option value="1" <?php echo selected(1, $prestador['prestacao_contas']); ?>>Sim</option>
                            <option value="0" <?php echo selected(0, $prestador['prestacao_contas']); ?>>Não</option>                            
                        </select>
                    </p>
                    <p><label class='first-2'>Número do contrato</label><input type="text" name="numero" id="numero" <?= ($prestador['prestacao_contas'] == '1') ? "class='validate[required]'" : ""; ?> value="<?php echo $prestador['numero'] ?>" size="40" /></p>
<!--<p><label class='first-2'>Assunto:</label><textarea name="assunto" id="assunto" rows="5" cols="72"><?php echo $prestador['assunto'] ?></textarea></p>-->
                    <p><label class='first-2' style="vertical-align:top!important;">Objeto:</label><textarea <?= ($prestador['prestacao_contas'] == '1') ? "class='validate[required]'" : ""; ?> name="objeto" id="objeto" rows="5" cols="72" class=""><?php echo $prestador['objeto'] ?> </textarea></p>
                    <!--<p><label class='first-2'>Especificação:</label><textarea name="especificacao" id="especificacao" rows="5" cols="72"><?php echo $prestador['especificacao'] ?></textarea></p>-->
                    <p><label class='first-2'>Município onde será<br>executado o serviço:</label><input type="text" name="co_municipio" id="co_municipio" value="<?php echo $prestador['co_municipio'] ?>" size="40" /></p>
                    <div class="colEsq">
                        <p><label class='first-2'>Unidade de Medida:</label><?php echo montaSelect($medidas, $prestador['id_medida'], "id='id_medida' name='id_medida' class='unidade_medida_a' ") ?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Valor:</label><input type="text" name="valor" id="valor" class="valor_a" value="<?php
                            if ($prestador['valor'] > 0) {
//                                echo number_format($prestador['valor'], 2, ",", ".");
                                echo $prestador['valor'];
                            } else {
                                echo "0";
                            }
                            ?>" size="20" class="validate[required]" /></p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Dados Bancários</legend>
<!--                    <p><label class='first'>Banco:</label><input type="text" name="nome_banco" id="nome_banco" value="--><?php //echo $prestador['nome_banco'] ?><!--" size="30" /></p>-->
                    <label for="" class="first">Banco:</label>
                    <select name="nome_banco" id="nome_banco">
                        <option value="-1">Selecione um Banco</option>
                        <?php
                        $sql = mysql_query("SELECT * FROM listabancos");
                        $selected = '';
                        while($row = mysql_fetch_assoc($sql)){
                            ($row['banco'] == $prestador['nome_banco']) ? $selected = 'selected' : $selected = '';
                            echo "<option value='$row[banco]' ".$selected.">".$row['banco']."</option>";
                        }

                        if($prestador['conta']){
                            $explodeConta = explode('-', $prestador['conta']);
                            $conta = $explodeConta[0];
                            $digito = $explodeConta[1];
                        }
                        ?>
                    </select>
                    <p><label class='first'>Agência:</label>
                        <input type="text" name="agencia" id="agencia" value="<?php echo $prestador['agencia'] ?>" size="30" class="" />
                        <input type="text" size="1" name="agencia_dv" id="digito" value="<?php echo $prestador['agencia_dv'] ?>">
                    </p>
                    <p><label class='first'>Conta:</label>
                        <input type="text" name="conta" id="conta" value="<?php echo $prestador['conta'] ?>" size="7" class="" />
                        <input type="text" size="1" name="conta_dv" id="digito" value="<?php echo $prestador['conta_dv'] ?>">
                    </p>
                </fieldset>

                <div class='message-box message-red socios'></div>
                <fieldset>
                    <legend>Sócios</legend>
                    <input style="margin-left: 10px;" type="button" id="adicionar_socio" name="adicionar_socio" value="Adicionar Sócio"/>
                    <input style="margin-left: 10px;" type="button" id="remover_socio" name="remover_socio" value="Remover Sócio"/>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>CPF</th>
                            </tr>
                        </thead>
                        <tbody id="socios">
                            <?php
//Enquanto houver s[ocios no array retornado
//irá criar e adicionar campos com as informações do dependente
                            for ($cont = 1; $cont <= $num_socios; $cont++) {
                                ?>
                                <tr id="socio<?php echo $cont; ?>">
                                    <td><input type="text" name="nome_socio[]" id="nome_socio1" value="<?php echo $socios[$cont]['nome'] ?>" size="38" /></td>
                                    <td><input type="text" name="tel_socio[]" id="tel_socio1" value="<?php echo $socios[$cont]['tel'] ?>" size="28" class="tel" /></td>
                                    <td><input type="text" name="cpf_socio[]" id="cpf_socio1" value="<?php echo $socios[$cont]['cpf'] ?>" size="20" class="cpf" /></td>
                                    <?php if (isset($id_prestador_post) && !empty($id_prestador_post)) { ?>
                                <input type="hidden" name="id_socio[]" value="<?php echo $socios[$cont]['id_socio']; ?>"/>
                            <?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Dependentes</legend>
                    <input style="margin-left: 10px;" type="button" id="adicionar_dependente" name="adicionar_dependente" value="Adicionar Dependente"/>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Grau Parentesco</th>
                            </tr>
                        </thead>
                        <tbody id="dependentes">
                            <?php
//Enquanto houver dependentes no array retornado
//irá criar e adicionar campos com as informações do dependente
                            for ($cont = 1; $cont <= $num_dependentes; $cont++) {
                                ?>
                                <tr id="dependente<?php echo $cont; ?>">
                                    <td><input type="text" id="nome_dependente" name="nome_dependente[]" value="<?php echo $dependentes[$cont]['prestador_dep_nome'] ?>" size="38" /></td>
                                    <td><input type="text" id="tel_dependente" name="tel_dependente[]" value="<?php echo $dependentes[$cont]['prestador_dep_tel'] ?>" size="30" class="tel" /></td>
                                    <td><?php echo montaSelect($optParentesco, $dependentes[$cont]['prestador_dep_parentesco'], "id='parentesco_dependente' name='parentesco_dependente[]' class='required[custom[select]]'") ?></td>
                                    <?php if (isset($id_prestador_post) && !empty($id_prestador_post)) { ?>
                                <input type="hidden" name="id_dependente[]" value="<?php echo $dependentes[$cont]['prestador_dep_id']; ?>"/>
                            <?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Retenção de Notas Fiscais de Serviço</legend>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                        <tr>
                            <th colspan="2">Contas do Prestador</th>
                        </tr>
                        <tr>
                            <th>Devedora</th>
                            <th>Credora</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= montaSelect($arrayPlanosDre, $assoc_impostos[0]['dre'], "id='conta' name='imposto[dre]' class='input-sm validate[required,custom[select]]' autocomplete='off'  style='width: 25em;'"); ?></td>
                            <td><?= montaSelect($arrayPlanosCredor, $assoc_impostos[0]['passivo'], "id='conta' name='imposto[0][passivo]' class='input-sm validate[required,custom[select]]' autocomplete='off' style='width: 25em;'"); ?></td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <table cellpadding="0" cellspacing="0" border="0" class="grid" style="width: 98%; margin: 10px;">
                        <thead>
                        <tr>
                            <th >Imposto</th>
                            <th >Alíquota</th>
                            <th >Conta Credora</th>
                            <th >&emsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr_tipso_retencao as $key => $row_tipo_retencao) {
                            if (isset($arr_retencao[$key])) {
                                $valor_atual = $arr_retencao[$key]['valor'];
                                $checked = 'checked';
                                $disabled = '';
                            } else {
                                $valor_atual = $row_tipo_retencao['valor'];
                                $disabled = 'disabled';
                                $checked = '';
                            }
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo $row_tipo_retencao['nome'] ?></strong>
                                </td>
                                <td>
                                    <input type="text" autocomplete="off" class="mask_float" name="<?php echo $row_tipo_retencao['slug'] ?>" id="valor<?= $key ?>" value="<?php echo number_format($valor_atual, 2, ',', '.') ?>" size="30" <?= $disabled ?> />
                                </td>
                                <td class=""><?= montaSelect($arrayPlanosCredor, $assoc_impostos[$key]['passivo'], "id='conta' name='imposto[{$key}][passivo]' class='input-sm validate[required,custom[select]] form-control' autocomplete='off' style='width: 25em;'"); ?></td>
                                <td>
                                    <input type="checkbox" class="selecionar" data-id="valor<?php echo $key ?>" <?= $checked ?>>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </fieldset>

                <!--                <fieldset>
                                    <legend>Associação Contábil</legend>
                
                                </fieldset>-->

                <fieldset>
                    <legend>Associação Financeira</legend>
                    <input name="id_codigo_servico_assoc" type="hidden" value="<?= $nfse_codigo_servico_assoc['id'] ?>">
                    <div class="form-group" style="margin-bottom:10px">
                        <label for="" class="col-sm-2 control-label first">Cod. Serviço</label>
                        <input type="text" class="form-control" name="CodigoTributacaoMunicipio" id="CodigoTributacaoMunicipio" placeholder="Código do Serviço" value="<?= $nfse_codigo_servico_assoc['codigo'] ?>">
                        <button class="btn btn-info" type="button" onclick="window.open('http://sped.rfb.gov.br/pagina/show/1601', '_blank');">Lista Sped <i class="fa fa-external-link"></i></button>
                        &emsp;
                        <input type="text" class="form-control" name="txt_servico" id="txt_servico" readonly value="<?= $nfse_codigo_servico_assoc['descricao'] ?>" style="width:30em">
                    </div>
                    <div class="form-group" style="margin-bottom:10px">
                        <label for="" class="col-sm-2 control-label first">Subgrupo</label>
                        <!--<div class="col-sm-10">-->
                        <?= montaSelect($subgrupo, $nfse_codigo_servico_assoc['id_subgrupo_entradasaida'], 'class="form-control validate[custom[select]]" id="subgrupo" name="subgrupo"') ?>
                        <!--</div>-->
                    </div>
                    <div class="form-group" style="margin-bottom:10px">
                        <label for="" class="col-sm-2 control-label first">Tipo</label>
                        <!--<div class="col-sm-10">-->
                        <?= montaSelect(getTipo($nfse_codigo_servico_assoc['id_subgrupo_entradasaida']), $nfse_codigo_servico_assoc['id_tipo_entradasaida'], 'class="form-control validate[custom[select]]" id="tipo" name="tipo"') ?>
                        <!--</div>-->
                    </div>
                </fieldset>

                <p class="controls">
                    <?php
//Verifica se foi selecionado um prestador na tela anterior
//Caso tenha sido selecionado o botão será de edição
                    if (isset($id_prestador_post) && !empty($id_prestador_post)) {
                        ?>
                        <input type="hidden" name="prestador" value="<?php echo $id_prestador; ?>"/>
                        <?php
                        //Verifica se o contrato já foi encerrado anteriormente
                        //para nao substituir o usuário que o encerrou anteriormente
                        //no update da linha
                        if (!empty($encerrado)) {
                            ?>
                            <input type="hidden" name="encerrado_por" value="<?php echo $encerrado; ?>"/>    
                        <?php } ?>
                        <input type="submit" name="editar" id="edit" value="Salvar" /> 
                        <?php
                    }
//Caso não tenha sido selecionado, será um novo cadastro
                    else {
                        ?>
                        <input type="submit" name="cadastrar" id="cad" value="Cadastrar" /> 
                    <?php } ?>
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /> 
                </p>
            </form>
        </div>
    </body>
</html>  
