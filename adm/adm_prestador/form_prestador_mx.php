<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/PrestadorServicoClass.php');

$usuario = carregaUsuario();
$especialidade = isset($_POST['c_especialidade']) ? $_POST['c_especialidade'] : NULL;
$id_prestador_post = isset($_REQUEST['prestador']) ? $_REQUEST['prestador'] : NULL;

$dev = FALSE;

if (isset($id_prestador_post) && !empty($id_prestador_post)) {    
    $prestador = PrestadorServico::getPrestador($id_prestador_post);
    $sql_master = "SELECT * FROM regioes WHERE id_regiao=$prestador[id_regiao]";
    $regiao_result = mysql_fetch_array(mysql_query($sql_master));
        
    $prestador = PrestadorServico::getPrestador($id_prestador_post);
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
    $id_projeto = $_REQUEST['projeto'];
    $id_regiao = $_REQUEST['regiao'];
    $id_medida = $_REQUEST['id_medida'];
    $aberto_por = $_COOKIE['logado'];
    $contratado_em = implode("-", array_reverse(explode("/", $_REQUEST['contratado_em'])));
    $encerrado_em = implode("-", array_reverse(explode("/", $_REQUEST['encerrado_em'])));
    $contratante = RemoveCaracteresGeral($_REQUEST['contratante']);
    $endereco = $_REQUEST['endereco'];
    $cnpj = $_REQUEST['cnpj'];
    $responsavel = RemoveCaracteresGeral($_REQUEST['responsavel']);
    $civil = $_REQUEST['civil'];
    $nacionalidade = $_REQUEST['nacionalidade'];
    $formacao = $_REQUEST['formacao'];
    $rg = $_REQUEST['rg'];
    $cpf = $_REQUEST['cpf'];
    $c_fantasia = RemoveCaracteresGeral($_REQUEST['c_fantasia']);
    $c_razao = RemoveCaracteresGeral($_REQUEST['c_razao']);
    $c_endereco = $_REQUEST['c_endereco'];
    $c_cnpj = $_REQUEST['c_cnpj'];
    $c_ie = $_REQUEST['c_ie'];
    $c_im = $_REQUEST['c_im'];
    $c_tel = $_REQUEST['c_tel'];
    $c_fax = $_REQUEST['c_fax'];
    $c_responsavel = RemoveCaracteresGeral($_REQUEST['c_responsavel']);
    $c_civil = $_REQUEST['c_civil'];
    $c_nacionalidade = $_REQUEST['c_nacionalidade'];
    $c_formacao = $_REQUEST['c_formacao'];
    $c_rg = $_REQUEST['c_rg'];
    $c_cpf = $_REQUEST['c_cpf'];
    $c_email = $_REQUEST['c_email'];
    $c_site = $_REQUEST['c_site'];
    $co_responsavel = RemoveCaracteresGeral($_REQUEST['co_responsavel']);
    $co_tel = $_REQUEST['co_tel'];
    $co_fax = $_REQUEST['co_fax'];
    $co_civil = $_REQUEST['co_civil'];
    $co_nacionalidade = $_REQUEST['co_nacionalidade'];
    $co_email = $_REQUEST['co_email'];
    $objeto = $_REQUEST['objeto'];
    $assunto = $_REQUEST['objeto'];
    $valor = str_replace(".", "", $_REQUEST['valor']);
    $prestador_tipo = $_REQUEST['prestador_tipo'];
    $nome_banco = $_REQUEST['nome_banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $prestacao_contas = $_REQUEST['prestacao_contas'];
    
    //validando data/periodo
    if($encerrado_em != ""){
        if($contratado_em > $encerrado_em){
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser maior que Data Final";
            $cad = false;
        }elseif($contratado_em == $encerrado_em){
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser igual a Data Final";
            $cad = false;
        }
    }
    
    //validando cnpj
    $cnpj_val = validarCNPJ($c_cnpj);
    if(!$cnpj_val){
        $cor_msg = "message-red";
        $txt_msg = "CNPJ da Empresa Contratada Inválido";
        $cad = false;
    }
    
    //validando cpf
    $cpf_val = validaCPF($c_cpf);
    if(!$cpf_val){
        $cor_msg = "message-red";
        $txt_msg = "CPF do Responsável da Empresa Contratada Inválido";
        $cad = false;
    }
    
    if($cad){
        $sql_prestador = "INSERT INTO prestadorservico(id_regiao,
                id_projeto,
                id_medida,
                aberto_por,
                aberto_em,
                contratado_em,
                encerrado_em,
                contratante,
                endereco,
                cnpj,
                responsavel,
                civil,
                nacionalidade,
                formacao,
                rg,
                cpf,
                c_fantasia,
                c_razao,
                c_endereco,
                c_cnpj,
                c_ie,
                c_im,
                c_tel,
                c_fax,
                c_email,
                c_responsavel,
                c_civil,
                c_nacionalidade,
                c_formacao,
                c_rg,
                c_cpf,
                c_site,
                co_responsavel,
                co_tel,
                co_fax,
                co_civil,
                co_nacionalidade,
                co_email,
                objeto,
                assunto,
                valor,
                prestador_tipo,
                nome_banco,
                agencia,
                conta,
                prestacao_contas,
                especialidade
                ) VALUES (
                '$id_regiao',
                '$id_projeto',
                '$id_medida',
                '$aberto_por',
                NOW(),
                '$encerrado_em',
                '$contratado_em',
                '$contratante',
                '$endereco', 
                '$cnpj', 
                '$responsavel', 
                '$civil',
                '$nacionalidade',
                '$formacao',
                '$rg',
                '$cpf',
                '$c_fantasia',
                '$c_razao',
                '$c_endereco',
                '$c_cnpj',
                '$c_ie',
                '$c_im',
                '$c_tel',
                '$c_fax',
                '$c_email',
                '$c_responsavel',
                '$c_civil',
                '$c_nacionalidade',
                '$c_formacao',
                '$c_rg',
                '$c_cpf',
                '$c_site',
                '$co_responsavel',
                '$co_tel',
                '$co_fax',
                '$co_civil',
                '$co_nacionalidade',
                '$co_email',
                '$objeto',
                '$assunto',
                '$valor',
                '$prestador_tipo',
                '$nome_banco',
                '$agencia',
                '$conta',
                '$prestacao_contas',
                '$especialidade'
                )";

        $insert_prestador = mysql_query($sql_prestador);

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
        
        header('Location: index.php');
    }
}

if (isset($_REQUEST['editar'])) {
    //Código para editar
    $id_prestador = $id_prestador_post;
    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $id_medida = $_REQUEST['id_medida'];
    $contratado_em = implode("-", array_reverse(explode("/", $_REQUEST['contratado_em'])));
    $encerrado_por = (!empty($_REQUEST['encerrado_por'])) ? $_REQUEST['encerrado_por'] : $_COOKIE['logado'];
    $encerrado_em = implode("-", array_reverse(explode("/", $_REQUEST['encerrado_em'])));
    $contratante = RemoveCaracteresGeral($_REQUEST['contratante']);
    $endereco = $_REQUEST['endereco'];
    $cnpj = $_REQUEST['cnpj'];
    $responsavel = RemoveCaracteresGeral($_REQUEST['responsavel']);
    $civil = $_REQUEST['civil'];
    $nacionalidade = $_REQUEST['nacionalidade'];
    $formacao = $_REQUEST['formacao'];
    $rg = $_REQUEST['rg'];
    $cpf = $_REQUEST['cpf'];
    $c_fantasia = RemoveCaracteresGeral($_REQUEST['c_fantasia']);
    $c_razao = RemoveCaracteresGeral($_REQUEST['c_razao']);
    $c_endereco = $_REQUEST['c_endereco'];
    $c_cnpj = $_REQUEST['c_cnpj'];
    $c_ie = $_REQUEST['c_ie'];
    $c_im = $_REQUEST['c_im'];
    $c_tel = $_REQUEST['c_tel'];
    $c_fax = $_REQUEST['c_fax'];
    $c_responsavel = RemoveCaracteresGeral($_REQUEST['c_responsavel']);
    $c_civil = $_REQUEST['c_civil'];
    $c_nacionalidade = $_REQUEST['c_nacionalidade'];
    $c_formacao = $_REQUEST['c_formacao'];
    $c_rg = $_REQUEST['c_rg'];
    $c_cpf = $_REQUEST['c_cpf'];
    $c_email = $_REQUEST['c_email'];
    $c_site = $_REQUEST['c_site'];
    $co_responsavel = RemoveCaracteresGeral($_REQUEST['co_responsavel']);
    $co_tel = $_REQUEST['co_tel'];
    $co_fax = $_REQUEST['co_fax'];
    $co_civil = $_REQUEST['co_civil'];
    $co_nacionalidade = $_REQUEST['co_nacionalidade'];
    $co_email = $_REQUEST['co_email'];
    $objeto = $_REQUEST['objeto'];
    $valor = str_replace(".", "", $_REQUEST['valor']);
    $prestador_tipo = $_REQUEST['prestador_tipo'];
    $nome_banco = $_REQUEST['nome_banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $prestacao_contas = $_REQUEST['prestacao_contas'];
    
    //validando data/periodo
    if($encerrado_em != ""){
        if($contratado_em > $encerrado_em){
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser maior que Data Final";
            $cad = false;
        }elseif($contratado_em == $encerrado_em){
            $cor_msg = "message-red";
            $txt_msg = "Data Inicial não pode ser igual a Data Final";
            $cad = false;
        }
    }
    
    //validando cnpj
    $cnpj_val = validarCNPJ($c_cnpj);
    if(!$cnpj_val){
        $cor_msg = "message-red";
        $txt_msg = "CNPJ da Empresa Contratada Inválido";
        $cad = false;
    }
    
    //validando cpf
    $cpf_val = validaCPF($c_cpf);
    if(!$cpf_val){
        $cor_msg = "message-red";
        $txt_msg = "CPF do Responsável da Empresa Contratada Inválido";
        $cad = false;
    }
    
    if($cad){
        $sql_update_prestacaoservico = "UPDATE prestadorservico 
                    SET id_medida = '$id_medida',
                    contratado_em = '$contratado_em',
                    encerrado_por = '$encerrado_por',
                    encerrado_em = '$encerrado_em',
                    contratante = '$contratante',
                    endereco = '$endereco',
                    cnpj = '$cnpj',
                    responsavel = '$responsavel',
                    civil = '$civil',
                    nacionalidade = '$nacionalidade',
                    formacao = '$formacao',
                    rg = '$rg',
                    cpf = '$cpf',
                    c_fantasia = '$c_fantasia',
                    c_razao = '$c_razao',
                    c_endereco = '$c_endereco',
                    c_cnpj = '$c_cnpj',
                    c_ie = '$c_ie',
                    c_im = '$c_im',
                    c_tel = '$c_tel',
                    c_fax = '$c_fax',
                    c_email = '$c_email',
                    c_responsavel = '$c_responsavel',
                    c_civil = '$c_civil',
                    c_nacionalidade = '$c_nacionalidade',
                    c_formacao = '$c_formacao',
                    c_rg = '$c_rg',
                    c_cpf = '$c_cpf',
                    c_site = '$c_site',
                    co_responsavel = '$co_responsavel',
                    co_tel = '$co_tel',
                    co_fax = '$co_fax',
                    co_civil = '$co_civil',
                    co_nacionalidade = '$co_nacionalidade',
                    co_email = '$co_email',
                    objeto = '$objeto',
                    assunto = '$objeto',
                    valor = '$valor',
                    prestador_tipo = '$prestador_tipo',
                    nome_banco = '$nome_banco',
                    agencia = '$agencia',
                    conta = '$conta',
                    prestacao_contas = '$prestacao_contas',
                    id_regiao = '$id_regiao',
                    id_projeto = '$id_projeto',
                    especialidade = '$especialidade'
                    WHERE id_prestador = '$id_prestador'
                    LIMIT 1
                    ";

        if($dev){
            exit($sql_update_prestacaoservico);
        }

        $qr_update = mysql_query($sql_update_prestacaoservico);

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
                if($dev){
                    exit($sql_update_prestador_socio);
                }
                $update_socio = mysql_query($sql_update_prestador_socio);
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
                    '$id_prestador')");
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
                if($dev){
                    exit($sql_update_dependente);
                }
                $update_dependente = mysql_query($sql_update_dependente);
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
                if($dev){
                    exit($sql_update_dependente);
                }

                $insert_dependente = mysql_query($sql_update_dependente);

            }
        }

        //echo "<script>history.go(-2)</script>";
        header('Location: index.php');
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


?>
<html>
    <head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="prestador.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <?php if (!isset($_GET['dev'])) { ?>
        <script src="../../js/jquery.validationEngine-2-6-2..js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <?php } ?>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            function selectProjOption(options){
                $('#projeto').html(options);
                $('#showLoading').remove();
                <?php if(isset($prestador['id_projeto'])) { ?>
                    $('#projeto option[value=<?= $prestador['id_projeto']; ?>]').attr('selected','selected');
               <?php } ?>
            }
        
            $(function() {
                $("#regiao").ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, 
                selectProjOption
             , "projeto");
                
                
                $(".data").mask("99/99/9999");                
                $(".cpf").mask("999.999.999-99");
                $("#c_cpf").mask("999.999.999-99");                
                $(".cnpj").mask("99.999.999/9999-99");                
                $(".tel").mask("(99)9999-9999?9");
                
                $("#form1").validationEngine({promptPosition: "topRight"});
                
                var conts = 20;

                var contd = 20;

                $("#adicionar_socio").click(function() {
                    var clone = $("#socio1").clone();
                    conts++;
                    clone.attr("id", "socio" + conts);
                    clone.find("input").val("");

                    $(clone).appendTo("#socios");
                    $(".tel").unmask("(99)9999-9999?9");
                    $(".cpf").unmask("999.999.999-99");
                    $(".tel").mask("(99)9999-9999?9");
                    $(".cpf").mask("999.999.999-99");
                });

                $("#adicionar_dependente").click(function() {
                    var clone = $("#dependente1").clone();
                    contd++;
                    clone.attr("id", "dependente" + contd);
                    clone.find("input").val("");
                    $(clone).appendTo("#dependentes");
                    $(".tel").unmask("(99)9999-9999?9");
                    $(".tel").mask("(99)9999-9999?9");
                });

                
                //url
                $("#c_site").click(function() {
                    if ($(this).val() == '') {
                        $(this).val('http://www.');
                    }
                });
                $("#c_site").blur(function() {
                    if ($(this).val() == 'http://www.') {
                        $(this).val('');
                    }
                });                                                                

//                options_unidade_medida = $('.unidade_medida_a').children('option')

                
                $('#c_especialidade').change(function(e) {
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
                $("#c_especialidade_fake").blur(function() {
                    $('#c_especialidade option').each(function() {
                            $(this).removeAttr('selected');
                    });
                    
                    val = $('#c_especialidade_fake').val();
                    $('#c_especialidade').append('<option value="'+val+'" selected="selected">'+val+'</option>');
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
            .box-medicos { display: none; }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" >
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Prestador de Serviço</h2>
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
<!--                            <p><label class="first">Região:</label> <?php // echo montaSelect(GlobalClass::carregaRegioes($regiao_result['id_master']), $prestador['id_regiao'], "id='regiao' name='regiao' class='validate[custom[select]]' style='width: 300px;'") ?></p>-->
                            <p><label class="first">Região:</label> <?php echo montaSelect(GlobalClass::carregaRegioes($usuario['id_master']), $prestador[id_regiao], "id='regiao' name='regiao' class='validate[custom[select]]' style='width: 300px;'") ?></p>                            
                            <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $prestador['id_projeto'], "id='projeto' name='projeto' class='validate[custom[select]]'") ?></p>
                        </div>
                    <?php } else { ?>
                        <input type="hidden" name="regiao" id="projeto_pre" value="<?php echo $prestador['id_regiao']; ?>" />
                        <input type="hidden" name="projeto" id="regiao_pre" value="<?php echo $prestador['id_projeto']; ?>" />
                    <?php } ?>
                    <div class="colDir">
                        <p><label class='first'>Data Inicio:</label><input type="text" name="contratado_em" id="contratado_em" value="<?php echo $prestador['contratado_embr'] ?>" class="data validate[required,custom[dateBr]] date_f" /></p>
                        <p><label class='first'>Data Final:</label><input type="text" name="encerrado_em" id="encerrado_em" value="<?php echo $prestador['encerrado_embr'] ?>" class="data date_f validate[custom[dateBr]]" /></p>
                    </div>
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
                <fieldset>
                    <legend>Dados do Contratante</legend>                    
                    <p><label class='first'>Contratante: </label><input type="text" size="104" name="contratante" readonly="readonly" value="<?php echo $dados_prestador['contratante'] ?>" /></p>
                    <p><label class='first'>Endereço:</label><input type="text" size="104" name="endereco" readonly="readonly" value="<?php echo $dados_prestador['endereco'] ?>" /></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><input type="text" name="cnpj"  readonly="readonly" value="<?php echo $dados_prestador['cnpj'] ?>" size="16" class="cnpj" /></p>
                        <p><label class='first'>Responsavel:</label><input type="text" name="responsavel" readonly="readonly"  value="<?php echo $dados_prestador['responsavel'] ?>" size="38" /></p>
                        <p><label class='first'>Nascionalidade:</label><input type="text" name="nacionalidade" readonly="readonly" value="<?php echo $dados_prestador['nacionalidade'] ?>" size="16" /></p>
                        <p><label class='first'>RG:</label><input type="text" name="rg" readonly="readonly" value="<?php echo $dados_prestador['rg'] ?>" size="16" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><input type="text" name="estado_civil" readonly="readonly"  value="<?php echo $dados_prestador['estado_civil'] ?>" size="16" /></p>
                        <p><label class='first'>Formação:</label><input type="text" name="formacao"  readonly="readonly" value="<?php echo $dados_prestador['formacao'] ?>" size="30" /></p>
                        <p><label class='first'>CPF:</label><input type="text" name="cpf" readonly="readonly" value="<?php echo $dados_prestador['cpf'] ?>" size="30" class="cpf" /></p>
                    </div>
                </fieldset>

                <div class='message-box message-red contratada'></div>
                <fieldset>
                    <legend>Dados da Empresa Contratada</legend>
                    <p><label class='first'>Nome Fantasia:</label><input type="text" name="c_fantasia" id="c_fantasia" value="<?php echo $prestador['c_fantasia'] ?>" size="104" class="validate[required]" /></p>
                    <p><label class='first'>Razão Social:</label><input type="text" name="c_razao" id="c_razao" value="<?php echo $prestador['c_razao'] ?>" size="104" class="validate[required]" /></p>
                    <p><label class='first'>Endereço:</label><input type="text" name="c_endereco" id="c_endereco" value="<?php echo $prestador['c_endereco'] ?>" size="104" /></p>
                    <p><label class='first'>Tipo de contrato:</label><?php echo montaSelect($arrTipos, $prestador['prestador_tipo'], "id='prestador_tipo' name='prestador_tipo'") ?></p>

                    <?php
                    $arrEspecialidades = array('' => 'OUTRO', 'AMBULATORIAL' => 'Ambulatorial', 'HOSPITALAR' => 'Hospitalar');
                    ?>
                    <p id="p_especialidade"><label class='first'>Especialidade:</label><?php echo montaSelect($arrEspecialidades, $prestador['especialidade'], " name='c_especialidade' id='c_especialidade' ") ?></p>
                    <div class="colEsq">
                        <p><label class='first'>CNPJ:</label><input type="text" name="c_cnpj" id="c_cnpj" value="<?php echo $prestador['c_cnpj'] ?>" size="17" class="cnpj validate[required]" /></p>
                        <p><label class='first'>IM:</label><input type="text" name="c_im" id="c_im" value="<?php echo $prestador['c_im'] ?>" size="17" /></p>
                        <p><label class='first'>Fax:</label><input type="text" name="c_fax" id="c_fax" value="<?php echo $prestador['c_fax'] ?>" size="17" class="tel" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>IE:</label><input type="text" name="c_ie" id="c_ie" value="<?php echo $prestador['c_ie'] ?>" size="12" /></p>
                        <p><label class='first'>Telefone:</label><input type="text" name="c_tel" id="c_tel" value="<?php echo $prestador['c_tel'] ?>" size="12" class="tel"/></p>
                    </div>
                    <p class="clear valid_email"><label class='first'>E-mail:</label><input type="text" name="c_email" id="c_email" value="<?php echo $prestador['c_email'] ?>" size="104" class="validate[custom[email]]" /></p>

                    <div class="colEsq">
                        <p><label class='first'>Responsavel:</label><input type="text" name="c_responsavel" id="c_responsavel" value="<?php echo $prestador['c_responsavel'] ?>" size="35" class="validate[required]" /></p>
                        <p><label class='first'>Nascionalidade:</label><input type="text" name="c_nacionalidade" id="c_nacionalidade" value="<?php echo $prestador['c_nacionalidade'] ?>" size="35" /></p>
                        <p><label class='first'>RG:</label><input type="text" name="c_rg" id="c_rg" value="<?php echo $prestador['c_rg'] ?>" size="15" class="validate[required]" /></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Estado Civil:</label><?php echo montaSelect($arrEstadoCivil, $prestador['c_civil'], "id='c_civil' name='c_civil'") ?></p>
                        <p><label class='first'>Formação:</label><input type="text" name="c_formacao" id="c_formacao" value="<?php echo $prestador['c_formacao'] ?>" size="30" /></p>
                        <p><label class='first'>CPF:</label><input type="text" name="c_cpf" id="c_cpf" value="<?php echo $prestador['c_cpf'] ?>" size="15" /></p>
                    </div>

                    <p class="clear"><label class='first'>Site:</label><input type="text" name="c_site" id="c_site" value="<?php echo $prestador['c_site'] ?>" size="104" class="validate[custom[url]]" /></p>
                </fieldset>

                <fieldset>
                    <legend>Dados da pessoa de contato na contratada</legend>
                    <p><label class='first'>Nome Completo:</label><input type="text" name="co_responsavel" id="co_responsavel" value="<?php echo $prestador['co_responsavel'] ?>" size="104" class="validate[required]"/></p>
                    <p class="clear valid_email"><label class='first'>Email:</label><input type="text" name="co_email" id="co_email" value="<?php echo $prestador['co_email'] ?>" size="104" class="validate[custom[email]]" /></p>
                    <div class="colEsq">
                        <p><label class='first'>Telefone:</label><input type="text" name="co_tel" id="co_tel" value="<?php echo $prestador['co_tel'] ?>" size="12" class="tel" /></p>
                        <p><label class='first'>Estado Civil:</label><?php echo montaSelect($arrEstadoCivil, $prestador['co_civil'], "id='co_civil' name='co_civil'") ?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Fax:</label><input type="text" name="co_fax" id="co_fax" value="<?php echo $prestador['co_fax'] ?>" size="12" class="tel" /></p>
                        <p><label class='first'>Nacionalidade:</label><input type="text" name="co_nacionalidade" id="co_nacionalidade" value="<?php echo $prestador['co_nacionalidade'] ?>" size="30" /></p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Dados do contrato</legend>
                    <!--<p><label class='first-2'>Tem contrato?</label><?php // echo montaSelect($temContrato, $prestador['prestacao_contas'], "id='prestacao_contas' name='prestacao_contas'")          ?></p>-->

                    <p>
                        <label class='first-2'>Tem contrato?</label>
                        <select id="prestacao_contas" name="prestacao_contas">
                            <option value="1" <?php echo selected(1, $prestador['prestacao_contas']); ?>>Sim</option>
                            <option value="0" <?php echo selected(0, $prestador['prestacao_contas']); ?>>Não</option>                            
                        </select>
                    </p>

<!--<p><label class='first-2'>Assunto:</label><textarea name="assunto" id="assunto" rows="5" cols="72"><?php echo $prestador['assunto'] ?></textarea></p>-->
                    <p><label class='first-2' style="vertical-align:top!important;">Objeto:</label><textarea name="objeto" id="objeto" rows="5" cols="72"><?php echo $prestador['objeto'] ?> </textarea></p>
                    <!--<p><label class='first-2'>Especificação:</label><textarea name="especificacao" id="especificacao" rows="5" cols="72"><?php echo $prestador['especificacao'] ?></textarea></p>
                    <p><label class='first-2'>Município onde será<br>executado o serviço:</label><input type="text" name="co_municipio" id="co_municipio" value="<?php echo $prestador['co_municipio'] ?>" size="40" /></p>-->
                    <div class="colEsq">
                        <p><label class='first-2'>Unidade de Medida:</label><?php echo montaSelect($medidas, $prestador['id_medida'], "id='id_medida' name='id_medida' class='unidade_medida_a' ") ?></p>
                    </div>
                    <div class="colDir">
                        <p><label class='first'>Valor:</label><input type="text" name="valor" id="valor" class="valor_a" value="<?php
                            if ($prestador['valor'] > 0) {
                                echo number_format($prestador['valor'], 2, ",", ".");
                            } else {
                                echo "0";
                            }
                            ?>" size="20" class="validate[required]" /></p>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Dados Bancários</legend>
                    <p><label class='first'>Banco:</label><input type="text" name="nome_banco" id="nome_banco" value="<?php echo $prestador['nome_banco'] ?>" size="30" /></p>
                    <p><label class='first'>Agência:</label><input type="text" name="agencia" id="agencia" value="<?php echo $prestador['agencia'] ?>" size="30" class="validate[custom[onlyNumberSp]]" /></p>
                    <p><label class='first'>Conta:</label><input type="text" name="conta" id="conta" value="<?php echo $prestador['conta'] ?>" size="30" class="validate[custom[onlyNumberSp]]" /></p>
                </fieldset>

                <div class='message-box message-red socios'></div>
                <fieldset>
                    <legend>Sócios</legend>
                    <input style="margin-left: 10px;" type="button" id="adicionar_socio" name="adicionar_socio" value="Adicionar Sócio"/>
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
                                    <td><input type="text" name="tel_socio[]" id="tel_socio1" value="<?php echo $socios[$cont]['tel'] ?>" size="38" class="tel" /></td>
                                    <td><input type="text" name="cpf_socio[]" id="cpf_socio1" value="<?php echo $socios[$cont]['cpf'] ?>" size="30" class="cpf" /></td>
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