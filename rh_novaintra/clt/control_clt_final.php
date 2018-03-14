<?php

if (empty($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('../classes/SetorClass.php');
include('../classes/PlanoSaudeClass.php');
include('../classes/InssOutrasEmpresasClass.php');
include_once("../classes/LogClass.php");
include('../classes/CltClass.php');
include('../classes/WorldClass.php');
include('../classes/calculos.php');

function removeAspas($str) {
    $str = str_replace("'", "", $str);

    return mysql_real_escape_string(trim(str_replace('"', '', $str)));
}

function regra_geral_valida_nome($str){

    //elimina os espaços no inicio e no fim
    $str = trim($str);

    //substitui dois espaços por um
    $str = str_replace("  ", " ", $str);

    //verifica letras consecutivas
    if(preg_match('/(.)\\1{2}/', $str)){
    	return "Nome com mais de três caracteres iguais consecutivos.";
    }

    //verifica primeiro nome
    $arr_txt = str_split($str);
	$indice_encontrado = array_search(" ", $arr_txt);
	if($indice_encontrado < 2 ){
		return "Parte inicial do nome com menos de 2 caracteres. ";
	}

	//verifica mais de duas abreviações seguidas	
	$arr_abv 	= str_split($str);
	$i 			= 0;

	while($indice_encontrado_abv[$i] = array_search(" ", $arr_abv)){
		array_splice($arr_abv, 0, $indice_encontrado_abv[$i]+1);
		$i++;
	}

	$indice_anterior_igual_a_1 = 0;

	for($k = 0; $k < count($indice_encontrado_abv) -2 ; $k++){

		if($indice_encontrado_abv[$k+1] == 1 && $indice_anterior_igual_a_1 == 1){
			return "Mais de uma abreviação";
		}

		if($indice_encontrado_abv[$k+1] == 1){
			$indice_anterior_igual_a_1 = 1;		
		}else{
			$indice_anterior_igual_a_1 = 0;	
		} 
	}
    
    return $str;
}

$log = new Log ();
$cltClass = new CltClass ();
$world = new WorldClass ();
$calc = new calculos ();

$usuario = carregaUsuario();

$objPlanoSaude = new PlanoSaudeClass();
$objInssOutrasEmpresas = new InssOutrasEmpresasClass();

$id_projeto = (!empty($_REQUEST['pro'])) ? $_REQUEST['pro'] : $_REQUEST['projeto'];
$id_regiao = $_REQUEST['regiao'];

require_once ("control_clt_acao.php");

/**
 * CONFIG RH CLT
 */
$sqlConfig = "SELECT * FROM rh_clt_config WHERE id_regiao = '{$id_regiao}' LIMIT 1;";
$qryConfig = mysql_query($sqlConfig);
$rowConfig = mysql_fetch_assoc($qryConfig);

$id_clt = (!empty($_REQUEST['clt'])) ? $_REQUEST['clt'] : null;

/**
 * RECUPERANDO MUNICÍPIOS
 */
if (isset($_REQUEST['getAllMunicipios']) && $_REQUEST['getAllMunicipios']) {
    
    $uf = $_REQUEST['uf'];
    $arr = $world->getAllMunicipios($uf);
    
    $municipios = "<option value='-1'>".utf8_encode('« SELECIONE »')."</option>";
    foreach ($arr as $municipio) {
        
        $municipios .= "<option value='{$municipio['id_municipio']}'>".utf8_encode($municipio['municipio'])."</option>";
    }
    
    echo json_encode($municipios);
    exit();
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getMunicipioByUf') {
    //SELECT MUNICÍPIO DE NASCIMENTO
    $sqlMunNasc     = "SELECT * FROM municipios A WHERE sigla LIKE '{$_REQUEST['uf_nasc']}' ORDER BY municipio";
    $queryMunNasc   = mysql_query($sqlMunNasc);
    echo "<option value=''>SELECIONE</option>";
    while ($rowMunNasc = mysql_fetch_assoc($queryMunNasc)) {
        echo "<option value='{$rowMunNasc['id_municipio']}'>{$rowMunNasc['municipio']}</option>";
//        $arrMunNasc[$rowMunNasc['id_municipio']] = $rowMunNasc['municipio'];
    }
    exit;
}

if (!$id_clt) {
    ///GERANDO NÚMERO DE MATRICULA E O NÚMERO DO PROCESSO
    $verifica_matricula = mysql_result(mysql_query("SELECT MAX(matricula) FROM rh_clt WHERE id_projeto = {$id_projeto}"), 0);
    $matricula = $verifica_matricula + 1;
} //!$id_clt
else {
//    $verifica_matricula = mysql_result(mysql_query("SELECT matricula FROM rh_clt WHERE id_projeto = {$id_projeto} AND id_clt={$id_clt}"), 0);
    $verifica_matricula = mysql_result(mysql_query("SELECT matricula FROM rh_clt WHERE id_clt={$id_clt}"), 0);
    $matricula = $verifica_matricula;
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'verificaCpf') {
    $query = "SELECT A.id_clt,A.nome,B.nome AS projeto, B.id_projeto, C.especifica
    FROM rh_clt AS A
    LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
    LEFT JOIN rhstatus AS C ON(A.`status` = C.codigo)
    WHERE A.cpf = '{$_REQUEST['cpf']}' AND (A.`status` != 10 || A.status != 200 || A.status != 40) AND B.id_master = {$usuario['id_master']};";
    $sql = mysql_query($query) or die('Erro ao selecionar ex funcionario');
    $dados = array(
        "status" => 0
    );
    $d = array();
    if (mysql_num_rows($sql)) {
        while ($linha = mysql_fetch_assoc($sql)) {
            $d['id'] = $linha['id_clt'];
            $d['nome'] = $linha['nome'];
            $d['projeto'] = $linha['projeto'];
            $d['idprojeto'] = $linha['id_projeto'];
            $d['status'] = $linha['especifica'];
        } //$linha = mysql_fetch_assoc($sql)
        $dados = array(
            "status" => 1,
            "dados" => $d
        );
    } //mysql_num_rows($sql)
    echo json_encode($dados);
    exit();
} //isset($_REQUEST['method']) && $_REQUEST['method'] == 'verificaCpf'

/* Monta horários */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'horarios') {
//    $qr_horarios = mysql_query("SELECT id_horario, nome, entrada_1, saida_1, entrada_2, saida_2 FROM rh_horarios WHERE horas_semanais = (SELECT hora_semana FROM curso WHERE id_curso = '{$_REQUEST['id']}')");
    if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
        $sql_horarios = "SELECT A.*
                        FROM rh_horarios A
                        LEFT JOIN curso B ON (B.hora_semana = A.horas_semanais)
                        WHERE B.id_curso = '{$_REQUEST['id']}'";
        $qr_horarios = mysql_query($sql_horarios);
        $verifica_horario = mysql_num_rows($qr_horarios);
        if (!empty($verifica_horario)) {
            $array[$row_horarios['id_horario']] = "-- Selecione --";
            while ($row_horarios = mysql_fetch_array($qr_horarios)) {
                //$auxHorario = ($idHorario == $row_horarios['id_horario']) ? ", selected: 'selected' " : null;
                $array[$row_horarios['id_horario']] = "{$row_horarios['id_horario']} - {$row_horarios['nome']} ( {$row_horarios['entrada_1']} - {$row_horarios['saida_1']} - {$row_horarios['entrada_2']} - {$row_horarios['saida_2']} )";
            } //$row_horarios = mysql_fetch_array($qr_horarios)
            $disabled = empty($_REQUEST['rh_horario']) ? '' : 'readonly="readonly" tabindex="-1"';
            $html = montaSelect($array, $_REQUEST['rh_horario'], "$disabled class='form-control input-sm validate[required]' id='rh_horario' name='rh_horario'");
        } //!empty($verifica_horario)
        else {
            $html = '<a href="../rh_novaintra/curso" target="_blank"><label style=" cursor: default; cursor: pointer; ">Clique aqui para cadastrar um hor&aacute;rio</label></a>';
        }
    } else {
        $html = '<select class="form-control input-sm" id="rh_horario"  name="rh_horario" disabled="disabled"><option value="">Selecione uma Função</option></select>';
    }
    echo utf8_encode($html);
    exit;
} //isset($_REQUEST['method']) && $_REQUEST['method'] == 'horarios'

$masters = getMasters();

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'metodo_class_trib') {
    $cod_trab = $_REQUEST['cod_trab'];

    $sql_class_trib = " SELECT * FROM compatibilidade_clt_ct AS A 
                        LEFT JOIN classificacao_tributaria AS B ON(A.id_clasificacao_tributaria = B.id_classificacao) 
                        WHERE A.cod_categoria_trabalhador = {$cod_trab} AND B.id_classificacao > 0";

    $res_class_trib = mysql_query($sql_class_trib);

    while ($row_class_trib = mysql_fetch_assoc($res_class_trib)) {
        $arrayClassTrib[$row_class_trib['id_classificacao']] = $row_class_trib['id_classificacao'] . " - " . $row_class_trib['descricao'];
    }
    
    if (empty($arrayClassTrib)) {
        $arrayClassTrib[0] = 'Selecione o Tipo de Contrato';
        $disabled = 'disabled="disabled"';
    }
    $html_class_trib = montaSelect($arrayClassTrib, $_REQUEST['classificacao'], "class='form-control input-sm' id='classificacao_tributaria' name='classificacao_tributaria' $disabled");
    echo utf8_encode($html_class_trib);
    exit;
}


if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'metodo_tipo_lotacao') {
    $cod_trab = $_REQUEST['cod_trab'];

    $sql_lt = " SELECT * FROM compatibilidade_clt_tl AS A 
                        LEFT JOIN tipos_lotacao_tributaria AS B ON(A.id_tipo_lotacao_tributaria = B.id) 
                        WHERE A.cod_categoria_trabalhador = {$cod_trab}";

    $res_lt = mysql_query($sql_lt);

    while ($row_lt = mysql_fetch_assoc($res_lt)) {
        $arrayLotacoes[$row_lt['id']] = $row_lt['id'] . " - " . $row_lt['descricao'];
    }

    if (empty($arrayLotacoes)) {
        $arrayLotacoes[0] = 'Selecione o Tipo de Contrato';
        $disabled = 'disabled="disabled"';
    }
    $html_lt = montaSelect($arrayLotacoes, $_REQUEST['lotacao'], "class='form-control input-sm' id='tipo_lotacao_tributaria' name='tipo_lotacao_tributaria' $disabled");
    echo utf8_encode($html_lt);
    exit;
}



/* Seleciona Unidades  */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidades') {
    $sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$_REQUEST['id_projeto']} ORDER BY unidade");
    $arrayUnidades = array(
        "" => "-- SELECIONE --"
    );
    while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    } //$rowUnidades = mysql_fetch_assoc($sqlUnidades)
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], "class='form-control input-sm validate[required]' id='id_unidade' name='unidade[{$_REQUEST['ordem']}][id_unidade]'");
    exit;
} //isset($_REQUEST['method']) && $_REQUEST['method'] == 'unidades'

/* remove inss */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'excluir_inss') {
    $objInssOutrasEmpresas->setIdInss(removeAspas($_REQUEST['id_inss']));
    if ($objInssOutrasEmpresas->inativa() == 1) {
        echo json_encode(['msg' => 'Excluido com sucesso', 'status' => 'success']);
    } else {
        echo json_encode(['msg' => 'Erro ao excluir', 'status' => 'danger']);
    }
    exit();
}


if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'acha_horario') {
    $id_horario_select = $_REQUEST['id_horario_select'];

    $query_horario = "SELECT * FROM rh_horarios WHERE id_horario = {$id_horario_select}";
    $res_horario = mysql_query($query_horario);
    $row_horario = mysql_fetch_assoc($res_horario);

    $res_array = ['entrada' => $row_horario['entrada_1'],
        'saida_almoco' => $row_horario['saida_1'],
        'retorno_almoco' => $row_horario['entrada_2'],
        'saida' => $row_horario['saida_2']];


    echo json_encode($res_array);

    exit();
}



//excluir outros dependentes
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'exclusao_de_outro_dependente') {

    $id_outro_dependente = $_REQUEST['id_outro_dependente'];

    $sql_delete_dependente = "DELETE FROM dependente WHERE id_dependente = '{$id_outro_dependente}'";
    mysql_query($sql_delete_dependente);

    echo json_encode(['msg' => 'Excluido com sucesso', 'status' => 'success']);
    exit();
}


// excluir dependente
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'exclusao_de_dependente') {

    $nome_dependente = $_REQUEST['nome_dep_ajax'];
    $p = $_REQUEST['posicao_dependente'];

    if ($nome_dependente != '') {
        $sql_delete_dependente = "DELETE FROM dependente WHERE nome = '{$nome_dependente}' AND id_clt = {$id_clt} ";
        mysql_query($sql_delete_dependente);
    }

    $sql_delete_dependente2 = 'UPDATE dependentes 
							   SET   nome' . $p . '					= "",
									 data' . $p . ' 				= "",
									 portador_def' . $p . ' 		= 0,
									 dep' . $p . '_cur_fac_ou_tec 	= 0, 
									 cpf' . $p . ' 					= "", 
									 nao_ir_filho' . $p . ' 		= 0, 
									 salario_familia' . $p . ' 		= 0, 
									 dep_plano_saude' . $p . ' 		= 0 
							   WHERE id_clt = ' . $id_clt . ' OR id_bolsista = ' . $id_clt . '';

    mysql_query($sql_delete_dependente2);

    echo json_encode(['msg' => 'Excluido com sucesso', 'status' => 'success']);
    exit();
}

//criação da array de setores
$setorObj = new SetorClass();
$setorObj->setProjeto($_REQUEST['projeto']);
$setorObj->getSetor();
$arraySetor[''] = '--Selecione o Setor--';
while ($setorObj->getRowSetor()) {
    $arraySetor[$setorObj->getIdSetor()] = $setorObj->getNome();
}

/* Seleciona valores para linhas de onibus */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'valLinha') {
    $linha = $_REQUEST['linha'];
    $sql = "SELECT valor_tarifa FROM rh_vt_linha WHERE id_vt_linha = '$linha'";
    $query = mysql_query($sql);
    $row = mysql_fetch_array($query);
    echo json_encode($row[0]);
    exit;
} //isset($_REQUEST['method']) && $_REQUEST['method'] == 'valLinha'

$id_clt = (!empty($_REQUEST['clt'])) ? $_REQUEST['clt'] : null;

$action = 'salvar';

if ($id_clt) {
    $action = 'editar';
    $sqlClt = "
    SELECT A.*, B.pais AS pais_nasc, C.pais AS pais_nacionalidade 
    FROM rh_clt A 
    LEFT JOIN paises AS B ON (A.id_pais_nasc = B.id_pais)
    LEFT JOIN paises AS C ON (A.id_pais_nacionalidade = C.id_pais)
    WHERE A.id_clt = '{$id_clt}'
    LIMIT 1";
    //select CLT
    $qryClt = mysql_query($sqlClt);
    $rowClt = mysql_fetch_assoc($qryClt);

    //SELECT MUNICÍPIO DE NASCIMENTO
    $sqlMunNasc     = "SELECT * FROM municipios A WHERE sigla LIKE '{$rowClt['uf_nasc']}' ORDER BY municipio";
    $queryMunNasc   = mysql_query($sqlMunNasc);
    while ($rowMunNasc = mysql_fetch_assoc($queryMunNasc)) {
        $arrMunNasc[$rowMunNasc['id_municipio']] = $rowMunNasc['municipio'];
    }
    
    //select do sindicato
    $sqlSindicato = "SELECT id_sindicato FROM rhsindicato WHERE id_sindicato = '{$rowClt['rh_sindicato']}' LIMIT 1";
    $qrySindicato = mysql_query($sqlSindicato);
    $rowSindicato = mysql_fetch_assoc($qrySindicato);

    $sqlInssOutrasEmpresas = mysql_query("SELECT * FROM rh_inss_outras_empresas WHERE id_clt = {$id_clt}") or die(mysql_error());
    while ($rowInssOutrasEmpresas = mysql_fetch_assoc($sqlInssOutrasEmpresas)) {
        $arrayInssOutrasEmpresas[] = $rowInssOutrasEmpresas;
    } //$rowInssOutrasEmpresas = mysql_fetch_assoc($sqlInssOutrasEmpresas)
    // ** SELEÇÃO DA NOVA 
    // TABELA DE DEPENDENTES ***
    //mae
    $sqlMae = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 13";
    $resultMae = mysql_query($sqlMae);
    $rowMae = mysql_fetch_array($resultMae);
    $cpfMae = $rowMae['cpf'];

    //pai
    $sqlPai = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 13";
    $resultPai = mysql_query($sqlPai);
    $rowPai = mysql_fetch_array($resultPai);
    $cpfPai = $rowPai['cpf'];

    //conjuge
    $sqlConjuge = "SELECT * FROM dependente WHERE id_clt={$id_clt} and parentesco = 1";
    $resultConjuge = mysql_query($sqlConjuge);
    $rowConjuge = mysql_fetch_array($resultConjuge);
    $cpfConjuge = $rowConjuge['cpf'];

    /**
     * SELECIONA OS DEPENDENTES
     */
    $sqlNovoDependentes = mysql_query("SELECT * FROM dependente WHERE id_clt = '$id_clt' AND parentesco IN (3)") or die("ERRO N. DEPENDENTE: " . mysql_error());
    while ($rowNovoDependentes = mysql_fetch_assoc($sqlNovoDependentes)) {
        $arrayDependentes[] = $rowNovoDependentes;
    }
    
    
    $sqlDependentes = mysql_query("SELECT * FROM dependentes WHERE (id_bolsista = '$id_clt' OR id_clt = '$id_clt')  AND id_projeto = '{$rowClt['id_projeto']}' AND contratacao = '{$rowClt['tipo_contratacao']}' LIMIT 1") or die("ERRO DEPENDENTE 1: " . mysql_error());
    while ($rowDependentes = mysql_fetch_assoc($sqlDependentes)) {
        //        print_array($rowDependentes);       
        $arrayDependentes['id_dependentes'] = $rowDependentes['id_dependentes'];
//        $arrayDependentes['ddir_pai'] = $rowDependentes['ddir_pai'];
//        $arrayDependentes['ddir_mae'] = $rowDependentes['ddir_mae'];
//        $arrayDependentes['ddir_conjuge'] = $rowDependentes['ddir_conjuge'];
//        $arrayDependentes['incapaz_trab_pai'] = $rowDependentes['incapaz_trab_pai'];
//        $arrayDependentes['incapaz_trab_mae'] = $rowDependentes['incapaz_trab_mae'];
//        $arrayDependentes['incapaz_trab_conjuge'] = $rowDependentes['incapaz_trab_conjuge'];
        if(count($arrayDependentes) == 0) {
            if ($rowDependentes['nome1']) {
                $arrayDependentes[0] = array(
                    'nome' => $rowDependentes['nome1'],
                    'data_nasc' => $rowDependentes['data1'],
                    'cpf' => $rowDependentes['cpf1'],
                    'deficiencia' => $rowDependentes['portador_def1'],
                    'fac_tec' => $rowDependentes['dep1_cur_fac_ou_tec'],
                    'nao_ir_filho' => $rowDependentes['nao_ir_filho1'],
                    'salario_familia' => $rowDependentes['salario_familia1'],
                    'dep_plano_saude' => $rowDependentes['dep_plano_saude1'],
                    'incapaz_trab' => $rowDependentes['incapaz_trab_filho1']
                );
            } //$rowDependentes['nome1']
            if ($rowDependentes['nome2']) {
                $arrayDependentes[1] = array(
                    'nome' => $rowDependentes['nome2'],
                    'data_nasc' => $rowDependentes['data2'],
                    'cpf' => $rowDependentes['cpf2'],
                    'deficiencia' => $rowDependentes['portador_def2'],
                    'fac_tec' => $rowDependentes['dep2_cur_fac_ou_tec'],
                    'nao_ir_filho' => $rowDependentes['nao_ir_filho2'],
                    'salario_familia' => $rowDependentes['salario_familia2'],
                    'dep_plano_saude' => $rowDependentes['dep_plano_saude2'],
                    'incapaz_trab' => $rowDependentes['incapaz_trab_filho2']
                );
            } //$rowDependentes['nome2']
            if ($rowDependentes['nome3']) {
                $arrayDependentes[2] = array(
                    'nome' => $rowDependentes['nome3'],
                    'data_nasc' => $rowDependentes['data3'],
                    'cpf' => $rowDependentes['cpf3'],
                    'deficiencia' => $rowDependentes['portador_def3'],
                    'fac_tec' => $rowDependentes['dep3_cur_fac_ou_tec'],
                    'nao_ir_filho' => $rowDependentes['nao_ir_filho3'],
                    'salario_familia' => $rowDependentes['salario_familia3'],
                    'dep_plano_saude' => $rowDependentes['dep_plano_saude3'],
                    'incapaz_trab' => $rowDependentes['incapaz_trab_filho3']
                );
            } //$rowDependentes['nome3']
            if ($rowDependentes['nome4']) {
                $arrayDependentes[3] = array(
                    'nome' => $rowDependentes['nome4'],
                    'data_nasc' => $rowDependentes['data4'],
                    'cpf' => $rowDependentes['cpf4'],
                    'deficiencia' => $rowDependentes['portador_def4'],
                    'fac_tec' => $rowDependentes['dep4_cur_fac_ou_tec'],
                    'nao_ir_filho' => $rowDependentes['nao_ir_filho4'],
                    'salario_familia' => $rowDependentes['salario_familia4'],
                    'dep_plano_saude' => $rowDependentes['dep_plano_saude4'],
                    'incapaz_trab' => $rowDependentes['incapaz_trab_filho4']
                );
            } //$rowDependentes['nome4']
            if ($rowDependentes['nome5']) {
                $arrayDependentes[4] = array(
                    'nome' => $rowDependentes['nome5'],
                    'data_nasc' => $rowDependentes['data5'],
                    'cpf' => $rowDependentes['cpf5'],
                    'deficiencia' => $rowDependentes['portador_def5'],
                    'fac_tec' => $rowDependentes['dep5_cur_fac_ou_tec'],
                    'nao_ir_filho' => $rowDependentes['nao_ir_filho5'],
                    'salario_familia' => $rowDependentes['salario_familia5'],
                    'dep_plano_saude' => $rowDependentes['dep_plano_saude5'],
                    'incapaz_trab' => $rowDependentes['incapaz_trab_filho5']
                );
            } //$rowDependentes['nome5']
            if ($rowDependentes['nome6']) {
                $arrayDependentes[5] = array(
                    'nome' => $rowDependentes['nome6'],
                    'data_nasc' => $rowDependentes['data6'],
                    'cpf' => $rowDependentes['cpf6'],
                    'deficiencia' => $rowDependentes['portador_def6'],
                    'fac_tec' => $rowDependentes['dep6_cur_fac_ou_tec'],
                    'nao_ir_filho' => $rowDependentes['nao_ir_filho6'],
                    'salario_familia' => $rowDependentes['salario_familia6'],
                    'dep_plano_saude' => $rowDependentes['dep_plano_saude6'],
                    'incapaz_trab' => $rowDependentes['incapaz_trab_filho6']
                );
            } //$rowDependentes['nome6']
            if ($rowDependentes['ddir_pai']) {
                $arrayDependentes['ddir_pai'] = $rowDependentes['ddir_pai'];
            } //$rowDependentes['ddir_pai']
            if ($rowDependentes['ddir_mae']) {
                $arrayDependentes['ddir_mae'] = $rowDependentes['ddir_mae'];
            } //$rowDependentes['ddir_mae']
            if ($rowDependentes['ddir_conjuge']) {
                $arrayDependentes['ddir_conjuge'] = $rowDependentes['ddir_conjuge'];
            } //$rowDependentes['ddir_conjuge']
        }
    } //$rowDependentes = mysql_fetch_assoc($sqlDependentes)
    // **** SELECIONA FAVORECIDOS ****
    for ($i = 0; $i <= 5; $i++) {
        if ($arrayDependentes[$i]['nome']) {
            //puxa os dados de pensao do filho de acordo com o nome do filho na tab dependentes

            $qrFav = "select * from favorecido_pensao_assoc where id_clt = {$id_clt} and (nome_dependente ='{$arrayDependentes[$i]['nome']}') AND status_reg = 1";
            $result = mysql_query($qrFav);
            if (mysql_num_rows($result) != 0) {

                //incluir o resultado na arrayFavorecidos
                while ($row = mysql_fetch_assoc($result)) {
                    $arrayFav[$i] = $row;
                    $arrayFav[$i]['pensao'] = 1;
                    
                    $hide_aliquota = $hide_valor = $hide_qnt_sal_min = 'hide';
                    
                    if ($arrayFav[$i]['base_pensao'] == 3) {
                        $hide_qnt_sal_min = '';
                    } else if ($arrayFav[$i]['base_pensao'] == 4) {
                        $hide_valor = '';
                    }  else {
                        $hide_aliquota = '';
                    }
                    
                } //$row = mysql_fetch_assoc($result)
            } //mysql_num_rows($result) != 0            
        } //$arrayDependentes[$i]['nome']
    } //$i = 1; $i <= 6; $i++
    // VALE TRANSPORTE
    if ($rowClt['transporte'] == "1") {
        $result_vale = mysql_query("SELECT * FROM rh_vale WHERE id_clt = '$id_clt' AND status_reg = 1");
        $row_vale = mysql_fetch_array($result_vale);
    }

    /**
     * SELECIONA OS DESCONTOS OUTRA EMPRESA
     */
    $objInssOutrasEmpresas->setIdClt($id_clt);
    $objInssOutrasEmpresas->getByIdClt();
    $countInssOutrasEmpresas = 0;
} //$id_clt

$row_projeto = mysql_fetch_assoc(mysql_query("SELECT * FROM projeto WHERE id_projeto = {$id_projeto} LIMIT 1;"));
/**
 * SELECIONA TODOS OS SINDICATOS DA REGIÃO
 */

$sql_sinficatos = "SELECT id_sindicato, nome FROM rhsindicato WHERE status = 1 ORDER BY nome";
$sqlArraySindicatos = mysql_query($sql_sinficatos);
$arraySindicatos[''] = "-- SELECIONE --";
while ($rowArraySindicatos = mysql_fetch_assoc($sqlArraySindicatos)) {
    $arraySindicatos[$rowArraySindicatos['id_sindicato']] = "{$rowArraySindicatos['id_sindicato']} - {$rowArraySindicatos['nome']}";
} //$rowArraySindicatos = mysql_fetch_assoc($sqlArraySindicatos)


/**
 * SELECIONA TIPOS DE CONTA FAVORECIDO
 */
$arrayTipoContaFav = array('' => ' -- Selecione --', 1 => 'Corrente', 2 => 'Poupança');

/**
 * SELECIONA TODAS AS FUNÇÕES DO PROJETO
 */
$sqlCurso = mysql_query("SELECT id_curso, nome, valor, salario FROM curso 
                        WHERE tipo IN(0,2) AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
$verifica_curso = mysql_num_rows($sqlCurso);
if (!empty($verifica_curso)) {
    $arrayFuncoes[''] = "-- SELECIONE --";
    while ($row_curso = mysql_fetch_assoc($sqlCurso)) {

        $salario = number_format((!empty($row_curso['valor'])) ? $row_curso['valor'] : $row_curso['salario'], 2, ',', '.');
        $nomeNovo = "{$row_curso['nome']} {$row_curso['letra']}{$row_curso['numero']}";
        $arrayFuncoes[$row_curso['id_curso']] = "{$row_curso['id_curso']} - {$nomeNovo} (Valor: $salario)";
    }
} //!empty($verifica_curso)
else {
    $arrayFuncoes[''] = "Nenhum Curso Cadastrado para o Projeto";
}
/**
 * SELECIONA TODOS OS TIPOS DE ADMISSAO
 */
$sqlTipoAdm = mysql_query("SELECT id_status_admi, codigo, especifica FROM rhstatus_admi");
$arrayTipoAdm = array("" => "-- SELECIONE --");
while ($rowTipoAdm = mysql_fetch_assoc($sqlTipoAdm)) {
    $arrayTipoAdm[$rowTipoAdm['id_status_admi']] = $rowTipoAdm['codigo'] . " - " . $rowTipoAdm['especifica'];
} //$rowTipoAdm = mysql_fetch_assoc($sqlTipoAdm)

/**
 * SELECIONA TODOS OS PROJETOS
 */
$sqlProjetos = mysql_query("SELECT id_projeto, nome FROM projeto WHERE status_reg = 1 AND id_master = " . $usuario['id_master'] . " ORDER BY nome") or die("ERRO AO SELECIONAR OS PROJETOS: " . mysql_error());
$arrayProjetos = array("" => "-- SELECIONE --");
while ($rowProjetos = mysql_fetch_assoc($sqlProjetos)) {
    $arrayProjetos[$rowProjetos['id_projeto']] = $rowProjetos['nome'];
} //$rowProjetos = mysql_fetch_assoc($sqlProjetos)
//SELECIONA OUTROS DEPENDENTES
$sql_outros_dependentes = "SELECT *, DATE_FORMAT(data_nasc, '%d/%m/%Y') as data_formatada FROM dependente WHERE id_clt = '{$id_clt}' AND parentesco NOT IN (2, 3, 13)";
$res_outros_deps = mysql_query($sql_outros_dependentes);

while ($row_outros_deps = mysql_fetch_assoc($res_outros_deps)) {
    $array_outros_dependentes[] = $row_outros_deps;
}

/**
 * SELECIONA TODAS AS UNIDADES DO PROJETO
 */
$sqlUnidades = mysql_query("SELECT id_unidade, unidade FROM unidade WHERE campo1 = {$id_projeto} ORDER BY unidade");
$arrayUnidades = array("" => "-- SELECIONE --");
while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
    $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . $rowUnidades['unidade'];
} //$rowUnidades = mysql_fetch_assoc($sqlUnidades)

/**
 * SELECIONA TODAS AS UF
 */
$sqlUfs = mysql_query("SELECT uf_sigla FROM uf ORDER BY uf_sigla");
$arrayUfs[""] = "-- SELECIONE --";
while ($rowUfs = mysql_fetch_assoc($sqlUfs)) {
    $arrayUfs[$rowUfs['uf_sigla']] = $rowUfs['uf_sigla'];
} //$rowUfs = mysql_fetch_assoc($sqlUfs)

/*
 * Seleciona os paises
 */
$sqlPaises = mysql_query("SELECT * FROM paises");
$arrayPaises[0] = "Selecione";

while ($row = mysql_fetch_assoc($sqlPaises)) {
    $arrayPaises[$row['id_pais']] = $row['pais'];
} //$row = mysql_fetch_assoc($sqlPaises)
/**
 * SELECIONA OS ESTADOS CIVIS
 */
$sqlEstadoCivil = mysql_query("SELECT id_estado_civil, nome_estado_civil FROM estado_civil WHERE cod_esocial > 0 ORDER BY nome_estado_civil");
$arrayEstadoCivil[""] = "-- SELECIONE --";
while ($rowEstadoCivil = mysql_fetch_assoc($sqlEstadoCivil)) {
    $arrayEstadoCivil[$rowEstadoCivil['id_estado_civil']] = $rowEstadoCivil['nome_estado_civil'];
} //$rowEstadoCivil = mysql_fetch_assoc($sqlEstadoCivil)

/**
 * SELECIONA AS NACIONALIDADES
 */
$sqlNacionalidades = mysql_query("SELECT codigo, nome FROM cod_pais_rais ORDER BY nome");
$arrayNacionalidades[""] = "-- SELECIONE --";
while ($rowNacionalidades = mysql_fetch_assoc($sqlNacionalidades)) {
    $arrayNacionalidades[$rowNacionalidades['nome']] = $rowNacionalidades['nome'];
} //$rowNacionalidades = mysql_fetch_assoc($sqlNacionalidades)

/**
 * SELECIONA AS ESCOLARIDADES
 */
$sqlEscolaridades = mysql_query("SELECT cod_esocial, id, nome FROM escolaridade WHERE status = 'on' AND cod_esocial > 0 ORDER BY cod_esocial");
$arrayEscolaridades[""] = "-- SELECIONE --";
while ($rowEscolaridades = mysql_fetch_assoc($sqlEscolaridades)) {
    $arrayEscolaridades[$rowEscolaridades['id']] = $rowEscolaridades['id'] . ' - ' . $rowEscolaridades['nome'];
} //$rowEscolaridades = mysql_fetch_assoc($sqlEscolaridades)

/**
 * SELECIONA OS TIPOS SANGUINEOS
 */
$sqlTipoSangue = mysql_query("SELECT nome FROM tipo_sanguineo ORDER BY nome");
$arrayTipoSangue[""] = "-- SELECIONE --";
while ($rowTipoSangue = mysql_fetch_assoc($sqlTipoSangue)) {
    $arrayTipoSangue[$rowTipoSangue['nome']] = $rowTipoSangue['nome'];
} //$rowTipoSangue = mysql_fetch_assoc($sqlTipoSangue)

/**
 * SELECIONA OS OLHOS/CABELOS
 */
$sqlOlhosCabelos = mysql_query("SELECT nome, tipo FROM tipos WHERE status = '1' ORDER BY nome");
$arrayOlhosCabelos[1][""] = $arrayOlhosCabelos[2][""] = "-- SELECIONE --";
while ($rowOlhosCabelos = mysql_fetch_assoc($sqlOlhosCabelos)) {
    $arrayOlhosCabelos[$rowOlhosCabelos['tipo']][$rowOlhosCabelos['nome']] = $rowOlhosCabelos['nome'];
} //$rowOlhosCabelos = mysql_fetch_assoc($sqlOlhosCabelos)

/**
 * SELECIONA AS ETINIAS
 */
$sqlEtinia = mysql_query("SELECT id, nome FROM etnias WHERE status = 'on' ORDER BY id DESC");
$arrayEtinia[""] = "-- SELECIONE --";
while ($rowEtinia = mysql_fetch_assoc($sqlEtinia)) {
    $arrayEtinia[$rowEtinia['id']] = $rowEtinia['nome'];
} //$rowEtinia = mysql_fetch_assoc($sqlEtinia)

/**
 * SELECIONA AS DEFICIÊNCIA
 */
$sqlDeficiencias = mysql_query("SELECT id, nome FROM deficiencias WHERE status = 'on'");
$arrayDeficiencias[""] = "-- SELECIONE --";
while ($rowDeficiencias = mysql_fetch_assoc($sqlDeficiencias)) {
    $arrayDeficiencias[$rowDeficiencias['id']] = $rowDeficiencias['nome'];
} //$rowDeficiencias = mysql_fetch_assoc($sqlDeficiencias)

/**
 * LISTA OS BANCOS (Nome)
 */
$sqlBancos = mysql_query("SELECT id_lista, banco FROM listabancos WHERE status_reg = 1");
$arrayBancos[""] = "-- SELECIONE --";
while ($rowBancos = mysql_fetch_assoc($sqlBancos)) {
    $arrayBancos[$rowBancos['banco']] = $rowBancos['banco'];
} //$rowBancos = mysql_fetch_assoc($sqlBancos)
$arrayBancos["000"] = "Outro";

/**
 * LISTA OS tipos de Dependentes (Nome)
 */
$sqlTiposDependente = mysql_query("SELECT * FROM tipo_dependente AS R WHERE R.id_tpDep NOT IN (1,2,3,4)");
$arrayTiposDependente[""] = "-- SELECIONE --";
while ($rowTiposDependente = mysql_fetch_assoc($sqlTiposDependente)) {
    $arrayTiposDependente[$rowTiposDependente['id_tpDep']] = $rowTiposDependente['descricao_tpDep'];
}

$select_dep = montaSelect($arrayTiposDependente, '', 'class="form-control input-sm" id="tipo_dependente" name="outro_dependente[][tipo_dependente]"');


/**
 * LISTA OS BANCOS P/ Favorecidos de pensao (ID)
 */
$sqlBancosFav = mysql_query("SELECT id_lista, banco FROM listabancos WHERE status_reg = 1");
$arrayBancosFav[""] = "-- SELECIONE --";
while ($rowBancos = mysql_fetch_assoc($sqlBancosFav)) {
    $arrayBancosFav[$rowBancos['id_lista']] = $rowBancos['banco'];
} //$rowBancos = mysql_fetch_assoc($sqlBancosFav)
$arrayBancosFav["000"] = "Outro";


/*
 * Seleciona contas de bancos por projeto
 */
$sqlBancosProjeto = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$id_projeto' AND status_reg = '1'");
$arrayBancosProjeto[0] = "Sem Banco";
while ($rowBancos = mysql_fetch_assoc($sqlBancosProjeto)) {
    $arrayBancosProjeto[$rowBancos['id_banco']] = "{$rowBancos['id_banco']} - {$rowBancos['nome']} ";
} //$rowBancos = mysql_fetch_assoc($sqlBancosProjeto)
$arrayBancosProjeto[9999] = "Outro Banco";

/**
 * SELECIONA OS TIPO PAGAMENTO
 */
$sqlTipoPagamento = mysql_query("SELECT id_tipopg, tipopg FROM tipopg WHERE id_projeto = '$id_projeto' ORDER BY tipopg");
$arrayTipoPagamento[""] = "-- SELECIONE --";
while ($rowTipoPagamento = mysql_fetch_assoc($sqlTipoPagamento)) {
    $arrayTipoPagamento[$rowTipoPagamento['id_tipopg']] = $rowTipoPagamento['tipopg'];
} //$rowTipoPagamento = mysql_fetch_assoc($sqlTipoPagamento)

/**
 * SELECIONA OS TIPO CONTRATAÇÃO
 */
$sqlTipoContratacao = mysql_query("SELECT codigo, id_categoria_trab, descricao FROM categorias_trabalhadores WHERE status = 1 ORDER BY codigo");
$arrayTipoContratacao[""] = "-- SELECIONE --";
while ($rowTipoContratacao = mysql_fetch_assoc($sqlTipoContratacao)) {
    $arrayTipoContratacao[$rowTipoContratacao['codigo']] = $rowTipoContratacao['codigo'] . " - " . $rowTipoContratacao['descricao'];
} //$rowTipoContratacao = mysql_fetch_assoc($sqlTipoContratacao)

/**
 * SELECIONA OS VALES ALIMENTAÇÃO E REFEIÇÃO
 */
$sqlRefAli = mysql_query("SELECT B.nome_categoria, B.campo_clt, A.nome_tipo, A.id_va_tipos FROM rh_va_tipos AS A LEFT JOIN rh_va_categorias AS B ON(A.id_va_categoria=B.id_va_categoria) WHERE A.`status`=1 AND B.`status`=1");
while ($rowRefAli = mysql_fetch_assoc($sqlRefAli)) {
    $arrayRefAliNome[$rowRefAli['campo_clt']] = $rowRefAli['nome_categoria'];
    $arrayRefAli[$rowRefAli['campo_clt']][""] = "-- SELECIONE --";
    $arrayRefAli[$rowRefAli['campo_clt']][$rowRefAli['id_va_tipos']] = $rowRefAli['nome_tipo'];
} //$rowRefAli = mysql_fetch_assoc($sqlRefAli)


/**
 * SELECIONA AS ALIQUOTAS DE PENSÃO
 */
$sql_aliq = "SELECT * FROM rh_movimentos WHERE descicao LIKE '%PENSÃO ALIMENTÍCIA JUDICIAL%' AND id_mov NOT IN (363,288) ORDER BY percentual ";
$sql_aliq_res = mysql_query($sql_aliq) or die('Erro ao carregar aliquotas');

while ($row_aliq_res = mysql_fetch_assoc($sql_aliq_res)) {
    $arrayAliquotasPensao[$row_aliq_res['percentual']] = $row_aliq_res['descicao'];
}


/**
 * SELECIONA OS VALES ALIMENTAÇÃO E REFEIÇÃO
 */
$objPlanoSaude->getPlanoSaude();
$arrayPlanoSaude['0'] = "-- SELECIONE --";
while ($objPlanoSaude->getRowPlanoSaude()) {
    $arrayPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] = $objPlanoSaude->getRazao();
} //$objPlanoSaude->getRowPlanoSaude()

/**
 * SELECIONA AS APOLICES
 */
$sqlApolices = mysql_query("SELECT id_apolice, razao FROM apolice WHERE id_regiao = {$usuario['id_regiao']}");
$arrayApolices[0] = "-- NÃO POSSUI --";
while ($rowApolices = mysql_fetch_assoc($sqlApolices)) {
    $arrayApolices[$rowApolices['id_apolice']] = $rowApolices['razao'];
} //$rowApolices = mysql_fetch_assoc($sqlApolices)

/**
 * SELECIONA OS VALES TRANSPORTES
 */
$sqlVT = mysql_query("SELECT A.id_tarifas, A.valor, A.tipo, A.itinerario, B.nome FROM rh_tarifas A LEFT JOIN rh_concessionarias B ON (A.id_concessionaria = B.id_concessionaria) WHERE A.id_regiao = {$usuario['id_regiao']} AND A.status_reg = '1'");
$arrayVT[0] = "-- NÃO POSSUI --";
while ($rowVT = mysql_fetch_assoc($sqlVT)) {
    $arrayVT[$rowVT['id_tarifas']] = "{$rowVT['valor']} - {$rowVT['tipo']} [{$rowVT['itinerario']}] - {$rowVT['nome']}";
} //$rowVT = mysql_fetch_assoc($sqlVT)

/**
 * Seleciona as linhas de transporte
 */
$sqlLinha = mysql_query("SELECT * FROM rh_vt_linha AS A ORDER BY A.nome");
$arrayLinha[] = '-- SELECIONE --';
while ($rowLinha = mysql_fetch_assoc($sqlLinha)) {
    $arrayLinha[$rowLinha['id_vt_linha']] = $rowLinha['nome'] . "Linha (" . $rowLinha['codigo'] . ")";
} //$rowLinha = mysql_fetch_assoc($sqlLinha)

/*
 * Seleciona os valores das tarifas
 */
$sqlVTValores = mysql_query("SELECT *FROM rh_vt_valores AS A WHERE A.status_reg = 1");
$arrayVTVal[] = "-- SELECIONE --";
while ($rowVTVal = mysql_fetch_assoc($sqlVTValores)) {
    $arrayVTVal[$rowVTVal['id_valor']] = "{$rowVTVal['valor']}";
} //$rowVTVal = mysql_fetch_assoc($sqlVTValores)

/**
 * Anos Contribuição
 */
$arrayAnosContrib = anosArray(2000, date('Y'));
$arrayAnosContrib[''] = "-- SELECIONE --";
$arrayAnosContrib[1] = "-- TOTAL --";
ksort($arrayAnosContrib);

$nome_pagina = "Gerenciamento de CLT";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array(
    "nivel" => "../",
    "key_btn" => "3",
    "area" => "Recursos Humanos",
    "id_form" => "form1",
    "ativo" => $nome_pagina
);

/*
 * vale transporte da Lagos
 */
$uqery_vale_trans = "SELECT * 
FROM rh_tarifas a
LEFT JOIN rh_concessionarias b ON b.id_concessionaria = a.id_concessionaria WHERE a.id_regiao = '$id_regiao' AND a.status_reg = '1'";
$resul_vale_trans = mysql_query($uqery_vale_trans);
$vale[0] = 'Não Tem';
while ($row_vale_trans = mysql_fetch_array($resul_vale_trans)) {
    $vale[$row_vale_trans[0]] = "$row_vale_trans[valor] - $row_vale_trans[tipo] [$row_vale_trans[itinerario]] - $row_vale_trans[nome]";
}

/*
 * o inss só pode ser excluido se for de mes que nao tem folha
 */
$query_last_folha = "SELECT CONCAT(ano,'-',mes) AS last_folha FROM rh_folha_proc a
WHERE a.id_clt = '$id_clt' AND a.`status` = 3
ORDER BY a.ano DESC,a.mes DESC
LIMIT 1;";
$last_folha = mysql_fetch_assoc(mysql_query($query_last_folha));


$array_cond_trab = array(
    1 => 'Visto Permanente',
    2 => 'Visto Temporário',
    3 => 'Asilado',
    4 => 'Refugiado',
    5 => 'Solicitante de Refúgio',
    6 => 'Residente em país fronteiriço do Brasil',
    7 => 'Deficiente Físico com mais de 51 anos',
    8 => 'Com residência provisória e anistiado, em situação irregular',
    9 => 'Com residência provisória e anistiado, em situação irregular',
    10 => 'Beneficiado pelo acordo entre países do Mercosul',
    11 => 'Dependente de agente diplomático e/ou consular de países que mantém convênio de reciprocidade para o exercício de atividade remunerada no Brasil',
    12 => 'Beneficiado pelo Tratado de Amizade, Cooperação e Consulta entre a República Federativa do Brasil e a República Portuguesa');

$munNascDis = $ufNascDis = ($rowClt['uf_nasc'] != 1) ? "" : "disabled='disabled'" ;
$temFilDis = $casBrasDis = $condEstrangDis = $dtChegada = ($rowClt['id_pais_nacionalidade'] == 1) ? "disabled='disabled'" : "" ;
//$dtChegada = ($rowClt['id_pais_nasc'] == 1) ? "disabled='disabled'" : "" ;

$sqlTipoLogr = "SELECT * FROM tipos_de_logradouro";
$queryTipoLogr = mysql_query($sqlTipoLogr);
$arrTipoLogr[-1] = "« SELECIONE »";
while ($rowTipoLogr = mysql_fetch_assoc($queryTipoLogr)) {
    $arrTipoLogr[$rowTipoLogr['id_tp_logradouro']] = $rowTipoLogr['descricao_tp_logradouro'];
}

$sqlTipoRegPrev = "SELECT * FROM tipos_regime_previdenciario";
$queryTipoRegPrev = mysql_query($sqlTipoRegPrev);
$arrTipoRegPrev[-1] = "« SELECIONE »";
while ($rowTipoRegPrev = mysql_fetch_assoc($queryTipoRegPrev)) {
    $arrTipoRegPrev[$rowTipoRegPrev['id_regime_previdenciario']] = $rowTipoRegPrev['id_regime_previdenciario'] . ' - ' . $rowTipoRegPrev['nome'];
}

$sqlTipoAdmiEso = "SELECT * FROM tipos_admissao_esocial";
$queryTipoAdmiEso = mysql_query($sqlTipoAdmiEso);
$arrTipoAdmiEso[''] = "« SELECIONE »";
while ($rowTipoAdmiEso = mysql_fetch_assoc($queryTipoAdmiEso)) {
    $arrTipoAdmiEso[$rowTipoAdmiEso['id_tipo_admissao']] = $rowTipoAdmiEso['id_tipo_admissao'] . ' - ' . $rowTipoAdmiEso['descricao'];
}

$sqlIndAdmi = "SELECT * FROM tipos_indicativo_admissao";
$queryIndAdmi = mysql_query($sqlIndAdmi);
$arrIndAdmi[''] = "« SELECIONE »";
while ($rowIndAdmi = mysql_fetch_assoc($queryIndAdmi)) {
    $arrIndAdmi[$rowIndAdmi['id_indicativo_admissao']] = $rowIndAdmi['id_indicativo_admissao'] . ' - ' . $rowIndAdmi['descricao'];
}

$sqlTiposRegJor = "SELECT * FROM tipos_regime_jornada_empregado";
$queryTiposRegJor = mysql_query($sqlTiposRegJor);
$arrTiposRegJor[''] = "« SELECIONE »";
while ($rowTiposRegJor = mysql_fetch_assoc($queryTiposRegJor)) {
    $arrTiposRegJor[$rowTiposRegJor['id_regime_jornada']] = $rowTiposRegJor['id_regime_jornada'] . ' - ' . $rowTiposRegJor['descricao'];
}

$sqlNatAtiv = "SELECT * FROM natureza_atividade";
$queryNatAtiv = mysql_query($sqlNatAtiv);
$arrNatAtiv[''] = "« SELECIONE »";
while ($rowNatAtiv = mysql_fetch_assoc($queryNatAtiv)) {
    $arrNatAtiv[$rowNatAtiv['id_natureza_atividade']] = $rowNatAtiv['id_natureza_atividade'] . ' - ' . $rowNatAtiv['descricao'];
}

$sqlIndProv = "SELECT * FROM estatutario_ind_provimento";
$queryIndProv = mysql_query($sqlIndProv);
$arrIndProv[''] = "« SELECIONE »";
while ($rowIndProv = mysql_fetch_assoc($queryIndProv)) {
    $arrIndProv[$rowIndProv['cod_esocial']] = $rowIndProv['cod_esocial'] . ' - ' . $rowIndProv['descricao'];
}

$sqlTpProv = "SELECT * FROM estatutario_tp_provimento";
$queryTpProv = mysql_query($sqlTpProv);
$arrTpProv[''] = "« SELECIONE »";
while ($rowTpProv = mysql_fetch_assoc($queryTpProv)) {
    $arrTpProv[$rowTpProv['cod_esocial']] = $rowTpProv['cod_esocial'] . ' - ' . $rowTpProv['descricao'];
}

$arrTpSegreg = ['' => "« SELECIONE »", 
                 1 => "Plano previdenciário ou único", 
                 2 => "Plano financeiro"];

//$sqlPrestador = "SELECT A.id_prestador, A.c_razao
//                 FROM prestadorservico A
//                 WHERE A.id_regiao = $id_regiao AND A.id_projeto = $id_projeto AND A.encerrado_em >= NOW()
//                 ORDER BY c_razao";
//$queryPrestador = mysql_query($sqlPrestador);
//$arrPrestador[-1] = "« Nenhum »";
//while ($rowPrestador = mysql_fetch_assoc($queryPrestador)) {
//    $arrPrestador[$rowPrestador['id_prestador']] = $rowPrestador['id_prestador'] . ' - ' . $rowPrestador['c_razao'];
//}

$posClauAsseg       = [''  => "« SELECIONE »",
                        0  => "Não",
                        1  => "Sim"];

$arrTipoContrato    = [1  => "Prazo Indeterminado", 2  => "Prazo Determinado"];

$arrOptFgts         = [''  => "« SELECIONE »",
                        1  => "Optante",
                        2  => "Não Optante"];

$arrHipLeg          = [''  => "« SELECIONE »",
                        1  => "Necessidade de substituição transitória de pessoal permanente",
                        2  => "Demanda complementar de serviços"];

$arrTpIncCont   = ['' => "« SELECIONE »",
                    1 => "Locais sem filiais",
                    2 => "Estudo de mercado",
                    3 => "Contratação superior a 3 meses"];

$arrCpfCnpj     = ['' => "« SELECIONE »",
                    1 => "CPF",
                    2 => "CNPJ"];

$dateOptFgts = $world->dateCompare($rowClt['data_entrada'], '1988-10-04');
$dateOptFgts2 = $world->dateCompare($rowClt['data_entrada'], '1967-01-01');
$rowClt['data_opcao_fgts'] = implode('/',array_reverse(explode('-',$rowClt['data_opcao_fgts'])));
if ($dateOptFgts > 0) {
    $rowClt['opt_fgts'] = 1;
    $rowClt['data_opcao_fgts'] = implode('/',array_reverse(explode('-',$rowClt['data_entrada'])));
    $readOnlyOptFgts = 'style="pointer-events: none; touch-action: none;" readonly tabindex="-1"';
    $readOnlyDateOptFgts = 'style="pointer-events: none; touch-action: none;" readonly tabindex="-1"';
}

$arr_base_pensao = [0 => "« Selecione »",
                    1 => "1 - Salário Líquido",
                    2 => "2 - Salário Bruto",
                    3 => "3 - Salário Mínimo",
                    4 => "4 - Valor Fixo",
                    5 => "5 - 1/3 Sobre Líquido",
                    6 => "6 - % Sobre Salário Mínimo"];
