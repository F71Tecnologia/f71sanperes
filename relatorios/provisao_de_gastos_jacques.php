<?php

/*
 * provisao_de_gastos
 * 
 * 00-00-0000
 * 
 * Rotina para processamento de provisão de gastos em lote
 * 
 * Versão: 1.1 - 31/07/2015 - Jacques - Implementação de tabela temporária para geração de provisão de gastos com compatibilidade retroativa
 * 
 * @author Não definido
 * 
 */

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include "../classes/FolhaClass.php";
include "../classes/calculos.php";

$id_regiao = $_REQUEST['regiao'];

$id_projeto = ($_REQUEST['projeto'] != (-1)) ? $_REQUEST['projeto'] : $_REQUEST['projeto_oculto'];
$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$folha = new Folha();
$calculos = new calculos();
$sql = "";

    
$table_rh_recisao = 'rh_recisao_provisao_de_gastos';


$debug = FALSE; // set TRUE para imprimir querys


$movimento_validos = array("5912,6007,9000,8080,9997,5012,5011,7001,6004,7003,8006,50249,80017");
$movs = array();
$movimentos = "SELECT cod,descicao,categoria FROM rh_movimentos WHERE incidencia IN('RESCISAO','FOLHA') GROUP BY cod ORDER BY descicao, categoria"; //AND cod IN(" .  implode(",", $movimento_validos). ")
$sql_movimento = mysql_query($movimentos) or die("Erro ao selecionar tipos de movimentos");
while ($rows_mov = mysql_fetch_assoc($sql_movimento)) {
    $movs[$rows_mov['cod']] = $rows_mov['cod'] . " - " . $rows_mov['descicao'] . " « " . $rows_mov['categoria'] . " » ";
}

$historico_gerado = "SELECT A.*, 
                        DATE_FORMAT(A.criado_em,'%d/%m/%Y - %H:%i:%s') AS data_formatada, 
                        B.nome AS nome_projeto, 
                        C.especifica, 
                        D.nome as criado_por_nome 
                    FROM header_recisao_lote AS A
                        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                        LEFT JOIN rhstatus AS C ON(A.tipo_dispensa = C.codigo)
                        LEFT JOIN funcionario AS D ON(A.criado_por = D.id_funcionario)
                    WHERE A.id_regiao = '{$_REQUEST['regiao']}'
                    ORDER BY id_header DESC";
$sql_historico = mysql_query($historico_gerado) or die("Erro ao selecionar header");

$tipo_dispensa = "SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY codigo ASC";
$sql_dispensa = mysql_query($tipo_dispensa) or die("Erro ao selecionar os tipos de dispensas");
$dispensa = array();
while ($linha = mysql_fetch_assoc($sql_dispensa)) {
    $dispensa[$linha['codigo']] = $linha['codigo'] . " - " . $linha['especifica'];
}


/**
 * RECUPERA TODO FGTS PAGO PARA O CLT, SOMA E CALCULA 50%
 */
if (isset($_REQUEST['method'])) {
    if ($_REQUEST['method'] == "soma_fgts") {
        $dados = $folha->getFgtsRecolhido($_REQUEST['clt']);
        echo json_encode($dados);
        exit();
    }
}

/**
 * VISUALIZA AS RESCISÕES
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "visualizarRescisao") {
    $return = array("status" => 0);
    $sql = "SELECT A.id_projeto AS projeto_rescisao, A.nome, A.aviso, C.especifica, A.sal_base, C.codigo, B.id_clt
        FROM $table_rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE A.id_recisao_lote = '{$_REQUEST['header']}' AND A.recisao_provisao_de_calculo = 1  GROUP BY A.id_clt ORDER BY C.codigo, B.nome";
    $visualiza_verifica = mysql_query($sql) or die("erro ao selecionar recisões");
    $dados = array();
    if ($visualiza_verifica) {
        while ($linha = mysql_fetch_assoc($visualiza_verifica)) {
            $dados[] = array("id" => $linha['id_clt'], "id_projeto" => $linha['projeto_rescisao'], "nome" => utf8_encode($linha['nome']), "aviso" => $linha['aviso'], "status_clt" => utf8_encode($linha['especifica']), "sal_base" => $linha['sal_base']);
        }
        $return = array("status" => 1, "dados" => $dados);
    }

    echo json_encode($return);
    exit();
}

/**
 * VERIFICA SE EXISTE RESCISÃO DE PROVISÃO COM AS CARACTERISTICAS ESCOLHIDA
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "verificaRescisao") {
    $return = array("status" => 0);
    $criteria = "";
    if (isset($_REQUEST['regiao'])) {
        $regiao = $_REQUEST['regiao'];
        $criteria .= "A.id_regiao = '{$regiao}'";
    }

    if (isset($_REQUEST['projeto'])) {
        $projeto = $_REQUEST['projeto'];
        $criteria .= " AND A.id_projeto = '{$projeto}'";
    }

    if (isset($_REQUEST['dispensa'])) {
        $motivo = $_REQUEST['dispensa'];
        $criteria .= " AND A.motivo = '{$motivo}'";
    }

    if (isset($_REQUEST['fator'])) {
        $fator = $_REQUEST['fator'];
        $criteria .= " AND A.fator = '{$fator}'";
    }

//    if (isset($_REQUEST['diasSaldo']) && $_REQUEST['diasSaldo'] != "") {
//        $saldoDias = $_REQUEST['diasSaldo'];
//        $criteria .= " AND A.dias_saldo = '{$saldoDias}'";
//    }

    if (isset($_REQUEST['dataDemi']) && $_REQUEST['dataDemi'] != "") {
        $dataDemi = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataDemi'])));
        $criteria .= " AND A.data_demi = '{$dataDemi}'";
    }

    if (isset($_REQUEST['dataAviso']) && $_REQUEST['dataAviso'] != "") {
        $dataAviso = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataAviso'])));
        $criteria .= " AND A.data_aviso = '{$dataAviso}'";
    }

    $verifica_recisao = "SELECT * FROM $table_rh_recisao AS A WHERE {$criteria} AND A.`status` = 0";
    //print_r($verifica_recisao); exit();
    $sql_verifica_recisao = mysql_query($verifica_recisao) or die("Erro ao selecionar dados de rescisão");
    $linhas_recisoes = mysql_num_rows($sql_verifica_recisao);

    if ($linhas_recisoes > 0) {
        $sql = "SELECT A.nome, A.aviso, C.especifica, A.sal_base, C.codigo, B.id_clt
        FROM $table_rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE {$criteria} AND A.recisao_provisao_de_calculo = 1  GROUP BY A.id_clt ORDER BY C.codigo, B.nome";
        $query_verifica = mysql_query($sql) or die("erro ao selecionar recisões");

        $dados = array();
        if ($query_verifica) {
            while ($linha = mysql_fetch_assoc($query_verifica)) {
                $dados[] = array("id" => $linha['id_clt'], "nome" => utf8_encode($linha['nome']), "aviso" => $linha['aviso'], "status_clt" => utf8_encode($linha['especifica']), "sal_base" => $linha['sal_base']);
            }
            $return = array("status" => 1, "dados" => $dados);
        }
    } else {
        $return = array("status" => 2);
    }

    echo json_encode($return);
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == "carregaFuncoes") {

    $funcoes = array();
    $query = "SELECT A.id_curso, A.nome
        FROM curso AS A
        WHERE A.id_regiao = '{$_REQUEST['regiao']}' AND A.campo3 = '{$_REQUEST['projeto']}'  AND A.`status` = 1
        ORDER BY A.nome";
    //echo $query;
    $sqlFuncoes = mysql_query($query) or die('Erro ao selecionar funcao');
    while ($linhas = mysql_fetch_assoc($sqlFuncoes)) {
        $funcoes[$linhas['id_curso']] = utf8_decode($linhas['nome']);
    }

    echo json_encode($funcoes);
    exit();
}

/**
 * VERIFICA OS PARTICIPANTES DO PROJETO SELECIONADO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "verificaParticipantes") {

    $return = array("status" => 0);
    $criteria = "";
    if (isset($_REQUEST['regiao'])) {
        $regiao = $_REQUEST['regiao'];
        $criteria .= "A.id_regiao = '{$regiao}' ";
    }

    if (isset($_REQUEST['projeto'])) {
        $projeto = $_REQUEST['projeto'];
        $criteria .= " AND A.id_projeto = '{$projeto}'";
    }


    $verifica_participantes = "SELECT A.id_clt, A.nome, B.nome as funcao, B.id_curso, C.especifica AS status, D.sallimpo, A.id_regiao, A.id_projeto
                    FROM rh_clt AS A 
                    LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                    LEFT JOIN rh_folha_proc AS D ON(D.id_clt = A.id_clt)
                    LEFT JOIN rhstatus AS C ON(A.`status` = C.codigo) WHERE {$criteria} AND (A.status < 60 || A.status = 200)  GROUP BY A.id_clt ORDER BY A.nome ";
    $sql_verifica_participantes = mysql_query($verifica_participantes) or die("Erro ao selecionar participantes");
    $linhas_participantes = mysql_num_rows($sql_verifica_participantes);

    if ($linhas_participantes > 0) {
        while ($linha = mysql_fetch_assoc($sql_verifica_participantes)) {
            $dados[] = array("id" => $linha['id_clt'], "nome" => utf8_encode($linha['nome']), "funcao" => utf8_encode($linha['funcao']), "id_curso" => $linha['id_curso'], "id_projeto" => $linha['id_projeto'], "id_regiao" => $linha['id_regiao'], "status" => utf8_encode($linha['status']), "sal_base" => $linha['sallimpo']);
        }
        $return = array("status" => 1, "id_projeto" => $_REQUEST['projeto'], "id_regiao" => $_REQUEST['regiao'], "dados" => $dados);
    }

    echo json_encode($return);
    exit();
}

/**
 * DESPROCESSAR RESCISÃO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "desprocessarRecisao") {
    $return = array("status" => 0);
    $query = "DELETE FROM header_recisao_lote WHERE id_header = '{$_REQUEST['header']}'";
    $query_linhas = "DELETE FROM $table_rh_recisao WHERE id_recisao_lote = '{$_REQUEST['header']}'";
    $sql_desprocessa = mysql_query($query) or die("Erro ao remover rescisão");
    $sql_desprocessa_linhas = mysql_query($query_linhas) or die("Erro ao remover rescisão linhas");
    if ($sql_desprocessa) {
        $return = array("status" => 1);
    }

    echo json_encode($return);
    exit();
}

/**
 * CADASTRA MOVIMENTOS PARA RESCISÃO, A TABELA QUE FICA ESSES MOVIMENTOS NÃO É A MESMA DOS MOVIMENTOS VÁLIDO PARA O CLT
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "cadastraMovimentos") {
    $return = array("status" => 0);
    $tipo_mov = "";
    $text_selected = explode("«", $_REQUEST['nome_movimento']);
    if (trim(str_replace(array("»", "Â"), "", $text_selected[1])) == "DESCONTO") {
        $tipo_mov = "DEBITO";
    } else {
        $tipo_mov = str_replace(array("»", "Â"), "", $text_selected[1]);
    }

    $query_cad_movimentos = "INSERT INTO tabela_morta_movimentos_recisao_lote (id_rescisao,id_clt,id_movimento,tipo,valor,status) VALUES ('{$_REQUEST['id_rescisao']}','{$_REQUEST['id_clt']}','{$_REQUEST['movimento']}','{$tipo_mov}','{$_REQUEST['valor_movimento']}','1')";
    $sql_movimentos = mysql_query($query_cad_movimentos) or die("Erro ao cadastrar movimentos");
    $ult_cad = mysql_insert_id();
    if ($sql_movimentos) {

        $query_movimentos = "SELECT A.*, B.descicao FROM tabela_morta_movimentos_recisao_lote AS A
                        LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod) WHERE A.id_mov = '{$ult_cad}'";
        $sql_movs = mysql_query($query_movimentos) or die("Erro ao selecionar ultimo movimento");

        $dados = array();
        while ($linhas_movs = mysql_fetch_assoc($sql_movs)) {
            $dados[] = array("id_mov" => $linhas_movs['id_mov'], "id_rescisao" => $linhas_movs['id_rescisao'], "id_clt" => $linhas_movs['id_clt'], "id_movimento" => $linhas_movs['id_movimento'], "tipo" => utf8_encode($linhas_movs['tipo']), "nome_movimento" => utf8_encode($linhas_movs['descicao']), "valor" => $linhas_movs['valor']);
        }
        $return = array("status" => 1, "dados" => $dados);
    }

    echo json_encode($return);
    exit();
}

/**
 * ATUALIZA VALOR LANÇADO PARA O MOVIMENTO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "atualizaValorMovimento") {
    $return = array("status" => 0);
    $query_update_movimentos = "UPDATE tabela_morta_movimentos_recisao_lote SET valor = '{$_REQUEST['valor']}' WHERE id_mov = '{$_REQUEST['movimento']}'";
    $sql_movimentos = mysql_query($query_update_movimentos) or die("Erro ao atulizar valor do movimento");
    if ($sql_movimentos) {
        $return = array("status" => 1);
    }

    echo json_encode($return);
    exit();
}

/**
 * REMOVE MOVIMENTOS LANÇADO PARA O MOVIMENTO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "removerMovimento") {
    $return = array("status" => 0);
    $query_remove_mov = "DELETE FROM tabela_morta_movimentos_recisao_lote WHERE id_mov = '{$_REQUEST['movimento']}'";
    $sql_remove = mysql_query($query_remove_mov) or die("Erro ao remover movimentos");
    if ($sql_remove) {
        $return = array("status" => 1);
    }
    echo json_encode($return);
    exit();
}

/**
 * LISTA MOVIMENTOS JA CADASTRADO PARA O CLT 
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "carrega_movimentos") {
    $return = array("status" => 0);
    $query_movimentos = "SELECT A.*, B.descicao FROM tabela_morta_movimentos_recisao_lote AS A
                        LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod) WHERE A.id_rescisao = '{$_REQUEST['rescisao']}' GROUP BY A.id_mov";
    $sql_movs = mysql_query($query_movimentos) or die("Erro ao selecionar movimentos");
    if ($sql_movs) {
        $dados = array();
        while ($linhas_movs = mysql_fetch_assoc($sql_movs)) {
            $dados[] = array("id_mov" => $linhas_movs['id_mov'], "id_rescisao" => $linhas_movs['id_rescisao'], "id_movimento" => $linhas_movs['id_movimento'], "tipo" => utf8_encode($linhas_movs['tipo']), "nome_movimento" => utf8_encode($linhas_movs['descicao']), "valor" => $linhas_movs['valor']);
        }
        $return = array("status" => 1, "dados" => $dados);
    }

    echo json_encode($return);
    exit();
}

/**
 * ABRI O ARQUIVO DE RESCISÃO PARA GRAVAR NO BANCO, COM OS DEVIDOS CALCULOS
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "gerarRescisao") {
    

    $return = array("status" => 0);
    $query = "SELECT id_clt, nome FROM rh_clt WHERE id_projeto = '{$_REQUEST['projeto']}' AND id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ") AND (status < 60 || status = 200)"; // id_clt = '53939' - (status < 60 || status = 200)
    $sql = mysql_query($query) or die("Erro ao selecionar participantes");
    $data_demi = (!empty($_REQUEST['dataDemi'])) ? date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataDemi']))) : "0000-00-00";
    $data_aviso = (!empty($_REQUEST['dataAviso'])) ? date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataAviso']))) : "0000-00-00";
    $query_header = "INSERT INTO header_recisao_lote (id_regiao,id_projeto,tipo_dispensa,fator,dias_de_saldo,data_demi,remuneracao_para_fins,quantidade_faltas,aviso_previo,dias_indenizados,data_aviso,devolucao_de_credito,criado_por) VALUES ('{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['dispensa']}','{$_REQUEST['fator']}','{$_REQUEST['diasSaldo']}','{$data_demi}','{$_REQUEST['remuneracoesRescisorias']}','{$_REQUEST['quantFaltas']}','{$_REQUEST['aviso']}','{$_REQUEST['diasIndOuTrab']}','{$data_aviso}','{$_REQUEST['devolucaoDeCredito']}','{$_COOKIE['logado']}')";
    $sql_header = mysql_query($query_header) or die("erro ao cadastrar header");
    $id_header = mysql_insert_id();

    if ($sql) {

        while ($linha = mysql_fetch_assoc($sql)) {

            //print_r($_REQUEST);exit();
            // Inicia o cURL acessando uma URL
            $dominio = $_SERVER['SERVER_NAME'];
            $URL = "http://$dominio/intranet/rh/recisao/recisao2.php";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $URL);

            $dados = array(
                "dispensa" => $_REQUEST['dispensa'],
                "fator" => $_REQUEST['fator'],
                "diastrab" => 0,
                "valor" => "0,00",
                "faltas" => 0,
                "aviso" => $_REQUEST['aviso'],
                "data_aviso" => $_REQUEST['dataAviso'],
                "tela" => 3,
                "idclt" => $linha['id_clt'],
                "regiao" => $_REQUEST['regiao'],
                "logado" => $_COOKIE['logado'],
                "data_demi" => date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataDemi']))),
                "recisao_coletiva" => 1,
                "id_header" => $id_header
            );

            curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dados);

            $response = curl_exec($ch);

            $errorMsg = curl_error($ch);
            $respostaHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            //echo $respostaHttp;
            
        }
        
        $d = array();
        $ult_projeto = "SELECT A.id_header, B.id_projeto, DATE_FORMAT(A.criado_em,'%d/%m/%Y - %H:%i:%s') AS data_formatada, B.nome AS nome_projeto, C.especifica AS dispensa, D.nome as criado_por_nome,
            A.fator, DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_saida, A.aviso_previo, DATE_FORMAT(A.data_aviso,'%d/%m/%Y') AS data_aviso
            FROM header_recisao_lote AS A
            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
            LEFT JOIN rhstatus AS C ON(A.tipo_dispensa = C.codigo)
            LEFT JOIN funcionario AS D ON(A.criado_por = D.id_funcionario)
            WHERE id_header = '{$id_header}'";

        $sql_ult_projeto = mysql_query($ult_projeto) or die("Erro ao selecionar dados do ultimo header cadastrado");
        while ($linha = mysql_fetch_assoc($sql_ult_projeto)) {

            $total_participantes = "SELECT COUNT(B.id_recisao) AS total FROM $table_rh_recisao AS B
                                        WHERE B.id_recisao_lote = '{$linha['id_header']}'
                                        GROUP BY B.id_projeto";
            $sql_total_participantes = mysql_query($total_participantes);
            $rows_total_participantes = mysql_fetch_assoc($sql_total_participantes);

            $d[] = array(
                "id_header" => $linha['id_header'],
                "id_projeto" => $linha['id_projeto'],
                "projeto" => utf8_encode($linha['nome_projeto']),
                "dispensa" => utf8_encode($linha['dispensa']),
                "fator" => $linha['fator'],
                "data_saida" => $linha['data_saida'],
                "aviso_previo" => $linha['aviso_previo'],
                "data_aviso" => $linha['data_aviso'],
                "criado_em" => $linha['data_formatada'],
                "criado_por" => utf8_encode($linha['criado_por_nome']),
                "total_participantes" => $rows_total_participantes['total']
            );
        }
        //aqui    
        $return = array("status" => 1, "dados_projeto" => $d);
    }

    echo json_encode($return);
    exit();
}


/**
 * ARRUMANDO AINDA
 */
if ((isset($_REQUEST['mostrar_rescisao']) || isset($_REQUEST['mostrar_prov_trab']) || isset($_REQUEST['modelo_xls'])) && !empty($_REQUEST['id_clt'])) {

    $id_projeto = (!empty($_REQUEST['projeto_oculto'])) ? $_REQUEST['projeto_oculto'] : $_REQUEST['projeto'];

    $sql = "SELECT B.desconto_inss, B.desconto_outra_empresa, D.nome as nome_funcao, C.especifica, C.codigo, A.*
        FROM $table_rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        LEFT JOIN curso AS D ON(D.id_curso = B.id_curso)
        WHERE A.id_projeto = '{$id_projeto}' AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
        AND A.recisao_provisao_de_calculo = 1 AND A.id_recisao_lote = '{$_REQUEST['header_lote']}' ORDER BY C.codigo, B.nome";

    $sql_status = "SELECT C.codigo, C.especifica 
        FROM $table_rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE A.id_projeto = '{$id_projeto}' AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
        AND A.recisao_provisao_de_calculo = 1 AND A.id_recisao_lote = '{$_REQUEST['header_lote']}' GROUP BY B.`status`";

    $sql_participantes = "SELECT COUNT(A.id_clt) AS total_participantes
        FROM $table_rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE A.id_projeto = '{$id_projeto}' AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
        AND A.recisao_provisao_de_calculo = 1 AND A.id_recisao_lote = '{$_REQUEST['header_lote']}' GROUP BY A.id_projeto ";

    $query_participantes = mysql_query($sql_participantes);
    $total_participantes = mysql_fetch_assoc($query_participantes);
}

if ($debug == TRUE) {
    echo "<!-- QUERY:: {$sql} -->";
    echo "<!-- QUERY_STATUS:: {$sql_status} -->";
    echo "<!-- QUERY_TOTAL_PARTICIPANTE:: {$sql_participantes} -->";
    echo "<!-- QUERY_VERIFICA:: {$verifica_recisao} -->";
}

if (!empty($sql)) {
    $qr_relatorio = mysql_query($sql) or die(mysql_error());

    $status = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
    if (isset($_REQUEST['mostrar_rescisao']) || isset($_REQUEST['mostrar_prov_trab']) || isset($_REQUEST['modelo_xls'])) {
        $status_array = array();
        $nome_status_array = array();
        $qr_status = mysql_query($sql_status);
        while ($linhas = mysql_fetch_array($qr_status)) {
            $status_array[] = $linhas["codigo"];
            $nome_status_array[$linhas["codigo"]] = $linhas["especifica"];
        }
    }
}

if (isset($_REQUEST['exportar_xls'])) {
    include_once 'provisao_de_gastos_xls_generator.php';
}



$fator = array("empregado" => "Empregado", "empregador" => "Empregador");
$aviso = array("trabalhado" => "Trabalhado", "indenizado" => "Indenizado");
$contratacao = array("1" => "Determinado", "2" => "Indeterminado");


$contratoSel = (isset($_REQUEST['contrato'])) ? $_REQUEST['contrato'] : "";
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : "";
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : "";
$dispensaSel = (isset($_REQUEST['dispensa'])) ? $_REQUEST['dispensa'] : "";
$fatorSel = (isset($_REQUEST['fator'])) ? $_REQUEST['fator'] : "";
$avisoPrevioSel = (isset($_REQUEST['aviso'])) ? $_REQUEST['aviso'] : "";
?>
<html>
    <head>
        <title>:: Intranet :: Previsão de Gasto</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script src="../js/ramon.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine_2.6.2.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>

        <script>
            $(function () {

                $("#form").validationEngine();

                $("#dataDemi").datepicker();
                $("#dataAviso").datepicker();

                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");

                /****************************FILTRO DE FUNÇÃO************************************/
                $("body").on("click", "#filtro_funcao", function () {
                    
                    // desmarca todos os CLTs
                    $('#id_clt_todos').attr("checked", false);
                    $('.clts').attr("checked", false);
                    
                    $("#checkboxes").css('display', 'none'); // oculta as funcoes
                    
                    $("#tbRelatorio tbody tr").hide(); // esconde todas as linhas dos CLTs
                    $('.checkFuncao:checked').each(function () {
                        var funcao = $(this).val();
                        $("#tbRelatorio tbody tr[data-curso='" + funcao + "']").show(); // exibe os CLTs pro funcao
                    });
                });

                $("body").on('change', '.tudo', function () {
                    console.log('aloha');
                    if ($(this).is(":checked")) {
                        $(".checkFuncao").attr("checked", true);
                    } else {
                        $(".checkFuncao").attr("checked", false);
                    }
                });

                /****************************FILTRO DE FUNÇÃO************************************/

                $("body").on("click", ".calcula_multa", function () {
                    var clt = $(this).data("key");
                    var nome = $(this).data("nome");
                    var html = "";
                    var ano = 0;
                    var tamanho = 260;
                    var tamanhoNovo = 0;

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            clt: clt,
                            method: "soma_fgts"
                        },
                        success: function (data) {
                            var total_anos = 0;
                            $.each(data, function (k, v) {
                                var qntAnos = Object.keys(data).length;
                                tamanhoNovo = tamanho * qntAnos;
                                html += "<div class='lista_fgts'>";

                                if (ano != k) {
                                    ano = k;
                                    var total = 0;
                                    html += "<h3>" + k + "</h3>";
                                    $.each(v, function (mes, tipo) {
                                        $.each(tipo, function (k, valor) {
                                            if (k == "normal") {
                                                html += "<p>" + mes + "/" + ano + " - " + valor + "</p>";
                                            } else {
                                                html += "<p>" + mes + "/" + ano + " - " + valor + " (13°)" + "</p>";
                                            }
                                            total = total + parseFloat(valor);
                                            total_anos += parseFloat(valor);
                                        });
                                    });
                                    html += "<h2>" + total.toFixed(2) + "</h2>";
                                }
                                html += "</div>";
                            });

                            html += "<div id='total_anos'><p><span>Total: </span>" + total_anos.toFixed(2) + "</p><p><span>Valor Multa FGTS 50%: </span>" + (total_anos * 0.50).toFixed(2) + "</p></div>";
                            $("#fgts_folha").html(html);


                            thickBoxModal("Dados de FGTS - " + nome, "#fgts_folha", 700, tamanhoNovo);
                        }
                    });

                });

                $("body").on("click", ".visualizar", function () {
                    $("#tbRelatorio").remove();
                    $(".totalizador").remove();
                    $(".imprime").remove();
                    var id_header = $(this).data("key");
                    var projeto = $(this).data("projeto");
                    $("#projeto_oculto").val(projeto);
                    $.ajax({
                        url: "provisao_de_gastos.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            method: "visualizarRescisao",
                            header: id_header
                        },
                        success: function (data) {
                            var html = "";
                            if (data.status == 1) {
                                html += "<input type='hidden' name='header_lote' id='header_lote' value='" + id_header + "' /><input type='submit' name='mostrar_prov_trab' id='mostrar_prov_trab' value='Provisão Trabalista' data-headerlote='" + id_header + "' style='margin: 10px; float:right' /><input type='submit' name='mostrar_rescisao' id='mostrar_rescisao' value='Visualizar Rescisão' data-headerlote='" + id_header + "' style='margin: 10px; float:right' />";
                                html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto;'><thead><tr><th colspan='6'></th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>AVISO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr class='' style='font-size:11px;'><td align='center'><input type='checkbox' class='clts' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' /></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.aviso + "</td><td align='left'>" + v.status_clt + "</td><td align='right'>" + v.sal_base + "</td></tr>";
                                });
                                html += "</table>";
                                $("#lista_funcionarios").html(html);
                            }
                        }
                    });
                });

                $("#visualizar_participantes").click(function () {

                    var dados = $("#form").serialize();
                    $.ajax({
                        url: "provisao_de_gastos.php?method=verificaParticipantes&" + dados,
                        type: "POST",
                        dataType: "json",
                        success: function (data) {
                            if (data.status == 1) {
                                $.ajax({
                                    url: "",
                                    data: {
                                        method: "carregaFuncoes",
                                        regiao: data.id_regiao,
                                        projeto: data.id_projeto
                                    },
                                    type: "POST",
                                    dataType: "json",
                                    success: function (funcao) {
                                        var html = "";
                                        html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto; margin-top: 20px;'><thead><tr><th colspan='6' style='height:90px; text-align:left; background:white; border-top: 1px solid #ccc'> ";
                                        html += "<p>Selecione uma Função:</p>";
                                        html += "<div class=\"multiselect\"><div class=\"selectBox\" onclick=\"showCheckboxes()\">";
                                        html += "<select >";
                                        html += "<option value='0'>« Selecione »</option>";
                                        html += "</select>";

                                        html += "<div class=\"overSelect\"></div></div>";
                                        html += "<div id=\"checkboxes\">";
                                        html += "<label for=\"a-0\"><input name='filtro_funcao[]' class='tudo' type=\"checkbox\" id=\"a-0\" value='0'/>« Todos »</label>";
                                        $.each(funcao, function (k, v) {
                                            html += "<label for=\"a-" + k + "\"><input name='filtro_funcao[]' class='checkFuncao' type=\"checkbox\" id=\"a-" + k + "\" value='" + k + "'/>" + v + "</label>";
                                        });
                                        html += "</div>";
                                        html += "</div>";
                                        html += "<button type='button' id='filtro_funcao'>Filtrar</button>";

//                                        html += "<p style='float:left;'>Selecione uma Função:</p><select name='filtro_funcao' id='filtro_funcao' style='width:320px; height:28px; clear: both; float:left;'>";
//                                        html += "<option value='todos'>« Todos »</option>";
//                                            $.each(funcao, function (k, v) {
//                                                html += "<option value='"+k+"'>"+v+"</option>";
//                                            });
//                                        html += "</select>";
                                        html += "</th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>FUNÇÃO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                        $.each(data.dados, function (k, v) {
                                            html += "<tr class='' style='font-size:11px;' data-curso='" + v.id_curso + "'><td align='center'><input type='checkbox' class='clts validate[minCheckbox[1]]' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' /></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.funcao + "</td><td align='left'>" + v.status + "</td><td align='right'>R$ " + v.sal_base + "</td></tr>";
                                        });
                                        html += "</table>";

                                        $("#lista_funcionarios").html(html);
                                        $("#gerar").remove();
                                        $(".controls").append("<input type='button' name='gerar' value='Gerar' id='gerar'/>");
                                        $("#dispensa, #fator, #dataDemi").removeAttr("disabled");
                                    }
                                });
                            }
                        }
                    });

                });

                $("body").on("click", "#gerar", function () {
                    $("#projeto_oculto").val("");
                    var dados = $("#form").serialize();

                    //if invalid do nothing
                    if (!$("#form").validationEngine('validate')) {
                        return false;
                    }
                    $.ajax({
                        url: "provisao_de_gastos.php?method=verificaRescisao&" + dados,
                        type: "POST",
                        dataType: "json",
                        success: function (data) {
                            var html = "";
                            if (data.status == 1) {
                                html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto;'><thead><tr><th colspan='6'></th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>AVISO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr class='' style='font-size:11px;'><td align='center'><input type='checkbox' class='clts' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' /></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.aviso + "</td><td align='left'>" + v.status_clt + "</td><td align='right'>" + v.sal_base + "</td></tr>";
                                });
                                html += "</table>";

                                $("#lista_funcionarios").html(html);
                            } else if (data.status == 2) {
                                $("#lista_funcionarios").html('');
                                thickBoxConfirm("Gerar novas rescisões", "Não foi encontrado nenhuma rescisão com as configurações selecionadas, deseja criar agora?", 500, 350, function (data) {
                                    if (data == true) {
                                        $(".carregando").show();
                                        $.ajax({
                                            url: "provisao_de_gastos.php?method=gerarRescisao&" + dados,
                                            type: "POST",
                                            dataType: "json",
                                            success: function (data) {
                                                //console.log(data);
                                                if (data) {
                                                    $(".carregando").hide();
                                                    if (data.status == 1) {
                                                        $.each(data.dados_projeto, function (k, v) {
                                                            console.log(v);
                                                            html += "<tr class='tr_" + v.id_header + "'><td>" + v.projeto + "</td><td>" + v.dispensa + "</td><td>" + v.fator + " </td><td>" + v.data_saida + "</td><td>" + v.aviso_previo + "</td><td>" + v.data_aviso + " </td><td>" + v.criado_por + "</td><td>" + v.criado_em + "</td><td align='center'>" + v.total_participantes + "</td><td><a href='javascript:;' data-key='" + v.id_header + "'data-projeto='" + v.id_projeto + "' class='visualizar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-view.gif' title='visualizar' /></a></td><td><a href='javascript:;' data-key='" + v.id_header + "' class='desprocessar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-delete.gif' title='desprocessar' /></a></td></tr>";
                                                        });
                                                        $("#historico_gerado").append(html);
                                                    }
                                                }
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });
                });

                $("body").on("click", "#id_clt_todos", function () {
                    var tudo = $(this).is(":checked");
                    $('.clts').each(function () {
                        var teste_funcao = $(this).closest('tr').css('display') !== 'none';
                        console.log($(this).closest('tr').css('display') !== 'none');
                        console.log($(this).html());
                        if (tudo && teste_funcao) {
                            console.log('oi');
                            $(this).attr("checked", true);
                        } else {
                            console.log('no');
                            $(this).attr("checked", false);
                        }

                    });

//                    var checado = $(this).is(":checked");
//                    var funcao_valida = ($('#'));
//                    if (checado && funcao_valida) {
//                        $(".clts").attr("checked", true);
//                    } else {
//                        $(".clts").attr("checked", false);
//                    }
                });

                $("body").on('click', ".xpandir", function () {
                    $(this).removeClass();
                    $(this).addClass("compactar");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "30"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "44"});
                    $(".area").css({display: "block"});
                    $(".esconder").show();
                    if ($('span').hasClass('compactarr') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "64"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".cabecalho_compactar").attr({colspan: "61"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "47"});
                    }
                });

                $("body").on("click", ".compactar", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandir");
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "13"});
                    $(".area").css({display: "none"});
                    $(".esconder").css({display: "none"});
                    if ($('span').hasClass('compactarr') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "8"});
                        $(".cabecalho_compactar").attr({colspan: "38"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "30"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "2"});
                        $(".cabecalho_compactar").attr({colspan: "16"});
                    }

                });

                $("body").on('click', ".xpandirr", function () {
                    $(this).removeClass();
                    $(this).addClass("compactarr");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "17"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "30"});
                    $(".areaa").css({display: "block"});
                    $(".esconderr").show();

                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "64"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "61"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "33"});
                    }
                });

                $("body").on("click", ".compactarr", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandirr");
                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "47"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "44"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "2"});
                        $(".cabecalho_compactar").attr({colspan: "16"});
                    } else {
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "13"});
                    }

                    $(".areaa").css({display: "none"});
                    $(".esconderr").css({display: "none"});
                });

                $("body").on('click', ".xpandirrr", function () {
                    $(this).removeClass();
                    $(this).addClass("compactarrr");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "2"});
                    $(".cabecalho_compactar").attr({colspan: "16"});
                    $(".areaaa").css({display: "block"});
                    $(".esconderrr").show();
                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "64"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "47"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "33"});
                    }
                });

                $("body").on("click", ".compactarrr", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandirrr");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "13"});

                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "61"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "44"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "30"});
                    }

                    $(".areaaa").css({display: "none"});
                    $(".esconderrr").css({display: "none"});
                });

                $("#dispensa").change(function () {
                    var tipo = $(this).val();
                    if (tipo == 61 || tipo == 65) {
                        $("#diasIndOuTrab").removeAttr("disabled");
                        $("#aviso").removeAttr("disabled");
                        $("#dataAviso").removeAttr("disabled");
                    } else {
                        $("#diasIndOuTrab").attr({disabled: "disabled"});
                        $("#aviso").attr({disabled: "disabled"});
                        $("#dataAviso").attr({disabled: "disabled"});
                    }
                }).trigger("change");

                $("body").on("click", ".desprocessar", function () {
                    var header = $(this).data("key");
                    thickBoxConfirm("Desprocessar rescisões", "Deseja realmente desprocessar?", 500, 350, function (data) {
                        if (data == true) {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "desprocessarRecisao",
                                    header: header
                                },
                                success: function (data) {
                                    $(".tr_" + header).remove();
                                }
                            });
                        }
                    });

                    $("#lista_funcionarios").html("");
                });

                $("#movimento").change(function () {
                    var movimento = $("#movimento :selected").text();
                    $("#nome_movimento").val(movimento);
                });

                $(".lanca_movimento").click(function () {
                    var rescisao = $(this).data("rescisao");
                    var clt = $(this).data("clt");
                    $("#id_rescisao").val(rescisao);
                    $("#id_clt").val(clt);
                    $("#lancamento_mov").show();
                    thickBoxModal("Lançamento de movimentos", "#lancamento_mov", 920, 700);

                    $("body").on("click", ".ui-icon-closethick", function () {
                        location.reload();
                    });

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            rescisao: rescisao,
                            method: "carrega_movimentos"

                        },
                        success: function (data) {
                            if (data) {
                                var html = "";
                                html += "<table id='tab_movimentos' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
                                html += "<thead><tr><td>COD</td><td>NOME</td><td>TIPO</td><td style='width:200px'>VALOR</td><td colspan='2'>AÇÕES</td></tr></thead><tbody>";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr style='height: 46px;' class='tr_" + v.id_mov + "'><td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td> " + v.tipo + " </td><td><span class='valor_" + v.id_mov + "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor movimento_" + v.id_mov + "' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td></tr>";
                                });
                                html += "</tbody></table>";
                                $("#dados_histarico").html(html);
                            }

                        }
                    });

                });

                $("body").on("click", ".remover_valor", function () {
                    var movimento = $(this).data("movimento");
                    thickBoxConfirm("Remover Movimento", "Deseja realmente Remover esse movimento?", 500, 350, function (data) {
                        if (data == true) {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "removerMovimento",
                                    movimento: movimento
                                },
                                success: function (data) {
                                    $(".tr_" + movimento).remove();
                                }
                            });
                        }
                    });

                });

                $("body").on("click", ".editar_valor", function () {
                    $(".valor_mov_edit").hide();
                    var movimento = $(this).data("movimento");
                    var valor_movimento = $(this).attr("data-valor");
                    $(".valor_" + movimento).html("<input type='text' name='valor_mov_edit' class='valor_mov_edit' data-mov_input='" + movimento + "' value='" + valor_movimento + "' class='input_edit' />");
                });

                $("body").on("blur", ".valor_mov_edit", function () {
                    var valor = $(this).val();
                    var movimento = $(this).data("mov_input");

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            valor: valor,
                            movimento: movimento,
                            method: "atualizaValorMovimento"
                        },
                        success: function (data) {
                            if (data.status) {
                                $(".mensagem").html("<span class='vermelho'>Movimento atualizado com sucesso</span>");
                            }
                        }
                    });
                    $(".valor_" + movimento).text(valor);
                    $(".movimento_" + movimento).attr({"data-valor": valor});
                });

                $("#cadastrar_mov").click(function () {

                    var movimento = $("#movimento").val();
                    var valor_movimento = $("#valor_movimento").val();
                    var rescisao = $("#id_rescisao").val();
                    var clt = $("#id_clt").val();
                    var nome_mov = $("#nome_movimento").val();
                    $("#valor_movimento").val("");
                    $.ajax({
                        url: "provisao_de_gastos.php",
                        type: "post",
                        dataType: "json",
                        data: {
                            method: "cadastraMovimentos",
                            movimento: movimento,
                            valor_movimento: valor_movimento,
                            id_rescisao: rescisao,
                            id_clt: clt,
                            nome_movimento: nome_mov
                        },
                        success: function (data) {
                            if (data.status) {
                                $(".mensagem").html("<span class='vermelho'>Movimento cadastrado com sucesso</span>");
                                var html = "";
                                $.each(data.dados, function (k, v) {
                                    html += "<tr class='tr_" + v.id_mov + "'>";
                                    html += "<td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td>" + v.tipo + "</td><td><span class='valor_" + v.id_mov + "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor movimento_" + v.id_mov + "' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td>";
                                    html += "</tr>";
                                });

                                $("#tab_movimentos").append(html);
                            }
                        }
                    });
                });
                
                
                
                $('body').on('click',"#mostrar_rescisao,#mostrar_prov_trab",function(){
                    $("#projeto").removeClass('validate[required, custom[select]]');
                    $("#form").submit();
                });
                

                // download do excel -------------------------------------------
//                $(".exportarExcel").click(function(){
////                    $("#form").attr("action","provisao_de_gastos_xls_generator.php");
//                    $("#form").submit();
//                });

            });

            // MULTI SELECT
            var expanded = false;
            function showCheckboxes() {
                var checkboxes = document.getElementById("checkboxes");
                if (!expanded) {
                    checkboxes.style.display = "block";
                    expanded = true;
                } else {
                    checkboxes.style.display = "none";
                    expanded = false;
                }
            }
            // FIM MULTI SELECT


        </script>
        <style>

            .input_edit{
                height: 19px;
                width: 46px;
                box-sizing: border-box;
                padding: 3px;
            }


            #total_anos{
                display: block;
                margin-top: 555px;
                margin-left: 10px;
                text-align: right;
                margin-right: 10px;
            }
            #total_anos p{
                font-family: arial;
                color: #333;
                font-size: 15px;
            }
            #total_anos span{
                font-weight: bold;
            }
            #fgts_folha{
                display: none;
            }
            .lista_fgts{
                border: 1px solid #ccc;
                padding: 5px;
                width: 207px;
                height: 535px;
                float: left;
                margin: 0px 10px;
                box-sizing: border-box;
            }
            .lista_fgts h3{
                border-bottom: 3px solid #333;
            }
            .lista_fgts h2{
                font-size: 16px;
                text-align: right;
                margin: 0px;
                background: #F5F3F3;
                width: 100%;
                padding: 5px;
                box-sizing: border-box;
            }
            .lista_fgts p{
                border-bottom: 1px dotted #ccc;
            }
            .header{
                font-weight: bold;
                background: #F3F3F3 !important;
                font-size: 11px !important;
                color: #333;
            }
            .footer{
                font-weight: bold;
                background: #F3F3F3;
            }

            .totalizador{
                border: 1px solid #ccc;
                padding: 5px;
                margin: 10px 10px;
                width: 347px;
                height: 424px;
                background: #f3f3f3;
                float: left;
            }
            .totalizador p{
                border-bottom: 1px dotted #ccc;
                padding-bottom: 2px;
            }
            .totalizador span{
                font-weight: bold;
                float: right;
            }
            .semborda{
                border: 0px !important;
            }
            .titulo{
                font-weight: bold;
                color: #000;
                text-align: center;
                font-size: 14px;
                margin: 5px 0px 20px 0px;
                border: 2px solid #B1A8A8 !important;
                padding: 1px 0px;
                background: #DFDFDF;
                height: 35px;
            }
            .compactar, .compactarr, .compactarrr, .xpandir, .compactarr, .xpandirr, .xpandirrr{
                float: right;
                font-family: verdana;
                font-size: 10px;
                font-weight: bold;
                color: #CA1E17;
                text-transform: uppercase;
                cursor: pointer;
            }

            .compactar:before, .compactarr:before, .compactarrr:before{
                content: " -";
                background: #1D1A1A;
                border-radius: 65%;
                padding: 1px 5px;
                font-weight: bold;
                color: #fff;
                margin-right: 5px;
            }

            .xpandir:before, .xpandirr:before, .xpandirrr:before{
                content: " +";
                background: #1D1A1A;
                border-radius: 65%;
                padding: 1px 3px;
                font-weight: bold;
                color: #fff;
                margin-right: 5px;
            }

            .esconder, .esconderr, .esconderrr{
                display: none;
            }

            .area, .areaa, .areaaa{
                border: 2px solid;
                height: 16px;
                width: 99%;
                margin-left: 5px;
                border-bottom: 0px;
                display: none;
            }

            .box{
                border: 0px solid #ccc;
                padding: 10px;
                box-sizing: border-box;
                margin: 5px;
                width: 1285px;
            }
            .col-esq, .col-dir{
                float: left;
                margin: 0px 5px;
                width: 590px;
            }

            .col-esq label, .col-dir label{
                width: 200px !important;
            }

            .inputPequeno{
                width: 324px;
                height: 27px;
                padding: 10px;
            }

            .selectPequeno{
                width: 324px;
                height: 28px;
                padding: 0px;
            }
            .carregando{
                width: 100%;
                height: 100%;
                position: fixed;
                top: 0px;
                left: 0px;
                background: #fff;
                opacity: 0.95;
                display: none;
            }
            .carregando img{
                width: 160px;
                box-sizing: border-box;
                text-align: center;
                margin-left: 150px;
            }
            .carregando .box-message{
                position: absolute;
                top: 150px;
                left: 37%;
                background: #F8F8F8;
                padding: 15px;
                box-sizing: border-box;
                box-shadow: 5px 5px 80px #333;
            }
            .carregando .box-message p{
                font-family: arial;
                font-size: 14px;
                color: #333;
                font-weight: bold;
                text-align: center;
            }

            .historico{
                height: 436px;
                overflow: auto;
            }

            th > span{
                font-weight: bold !important;
                margin-right: 5px;
                color: #888;
                //display: block;
            }

            th{
                font-weight: 500 !important;
                font-size: 12px !important; 
                text-transform: uppercase;
            }

            #lancamento_mov{
                display: none;
            }

            #lancamento_mov label{
                display: block;
                margin: 5px 0px;
                text-align: left;
                width: 200px;
                text-transform: uppercase;
                font-size: 11px;
                color: #333;
            }

            #lancamento_mov input[type='text']{
                width: 90px;
                padding: 5px;
            }

            #lancamento_mov input[type='button']{
                width: 160px;
                padding: 9px;
                background: #f1f1f1;
                border: 1px solid #ccc;
                font-weight: bold;
                cursor: pointer;
            }

            #lancamento_mov input[type='button']:hover{
                color: #999;
            }

            #box-1{
                box-sizing: border-box;
                padding: 15px 0px;
            }

            #lancamento_mov fieldset{
                border: 0px;
                margin-left: 20px;
            }
            .descricao_box{
                font-family: arial;
                font-size: 14px;
                color: #666;
                text-transform: uppercase;
                border-bottom: 1px dotted #ccc;
                width: 670px;
                padding-bottom: 5px;
            }
            .texto_pequeno{
                font-size: 11px !important;
                text-transform: uppercase !important;
            }

            .vermelho{
                color: red;
            }

            #tab_movimentos td{
                padding: 8px !important;
            }



            /* MULTISELECT */
            .multiselect {
                display: inline-block;
                width: 400px;
            }
            .multiselect select{
                padding: 5px;
            }
            .selectBox {
                position: relative;
            }
            #filtro_funcao{
                padding: 5px;
            }
            .selectBox select {
                width: 100%;
                font-weight: bold;
            }
            .overSelect {
                position: absolute;
                left: 0; right: 0; top: 0; bottom: 0;
            }
            #checkboxes {
                overflow: auto;
                max-height: 300px;
                width: 400px;
                position: absolute;
                display: none;
                border: 1px #dadada solid;
                z-index: 100;
                background-color: #FFF;
            }
            #checkboxes label {
                display: block;
                text-align:left;
            }
            #checkboxes label:hover {
                background-color: #1e90ff;
            }
            /* FIM MULTISELECT */

        </style>

    </head>
    <body class="novaintra" >  
        <div id="fgts_folha">

        </div>
        <div id="content" style="width: 1300px; display: table;">
            <div class="carregando">
                <div class="box-message">
                    <img src="../imagens/loading2.gif" />
                    <p>Não foi encontrato nenhum modelo de rescisão como solicitado, <br />Isso levará alguns minutos</p>
                </div>
            </div>
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Previsão de Gastos de Recisão</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>

                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <div class="box"> 
                            <div class="col-esq">
                                <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                                <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />

                                <p>
                                    <label class="first">Região:</label>
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'selectPequeno validate[required, custom[select]]')); ?> 
                                </p>                        
                                <p>
                                    <label class="first">Projeto:</label>
                                    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'selectPequeno validate[required, custom[select]]')); ?>
                                </p>
                                <p>
                                    <label class="first">Tipo de Dispensa:</label>
                                    <?php echo montaSelect($dispensa, $dispensaSel, array('name' => "dispensa", 'id' => 'dispensa', 'class' => 'selectPequeno validate[required, custom[select]]', 'disabled' => 'disabled')); ?>
                                </p>
                                <p>
                                    <label class="first">Fator:</label>
                                    <?php echo montaSelect($fator, $fatorSel, array('name' => "fator", 'id' => 'fator', 'class' => 'selectPequeno validate[required, custom[select]]', 'disabled' => 'disabled')); ?>
                                </p>

                            </div>
                            <div class="col-dir">
                                <p>
                                    <label class="first">Data Demissão:</label>
                                    <input type="text" name="dataDemi" id="dataDemi" class="inputPequeno validate[required]" value="<?php echo $_REQUEST['dataDemi']; ?>" disabled="true"/>
                                </p>
                                <p>
                                    <label class="first">Aviso prévio:</label>
                                    <?php echo montaSelect($aviso, $avisoPrevioSel, array('name' => "aviso", 'id' => 'aviso', 'class' => 'selectPequeno', 'disabled' => 'disabled')); ?>
                                </p>

                                <p>
                                    <label class="first">Data do Aviso:</label>
                                    <input type="text" name="dataAviso" id="dataAviso" class="inputPequeno"  disabled="disabled" value="<?php echo (isset($_REQUEST['dataAviso'])) ? $_REQUEST['dataAviso'] : ""; ?>" />
                                </p>              

                            </div>
                        </div>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="hidden" name="projeto_oculto" id="projeto_oculto" />
                        <input type="button" name="visualizar_participantes" value="Visualizar Participantes" id="visualizar_participantes"/>
                    </p>
                </fieldset>

                <fieldset class="noprint historico">
                    <p class="txt-red">Histório dos últimos 30 dias.</p>
                    <table id="historico_gerado" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%">
                        <thead>
                            <tr>
                                <th>Projeto</th>
                                <th>Dispensa</th>
                                <th>Fator</th>
                                <th>Data Demissão</th>
                                <th>Aviso Prévio Indenizado</th>
                                <th>Data do Aviso</th>
                                <th>Criado Por</th>
                                <th>Criado Em</th>
                                <th>Total de Participantes</th>
                                <th colspan="2">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($linha_header = mysql_fetch_assoc($sql_historico)) { ?>
                                <?php
                                if($linha_header['criado_em'] > '20150730') {
                                    
                                    $table_rh_recisao = 'rh_recisao_provisao_de_gastos';
                                    
                                }
                                else{
                                    
                                    $table_rh_recisao = 'rh_recisao';
                                    
                                }
                                    
                                $total_participantes = "SELECT COUNT(B.id_recisao) AS total FROM $table_rh_recisao AS B
                                        WHERE B.id_recisao_lote = '{$linha_header['id_header']}'
                                        GROUP BY B.id_projeto";
                                $sql_total_participantes = mysql_query($total_participantes);
                                $rows_total_participantes = mysql_fetch_assoc($sql_total_participantes);
                                ?>

                                <tr class="tr_<?php echo $linha_header['id_header']; ?>">
                                    <td><?php echo $linha_header['nome_projeto']; ?></td>
                                    <td><?php echo $linha_header['especifica']; ?></td>
                                    <td><?php echo $linha_header['fator']; ?></td>
                                    <td><?php echo date("d/m/Y", strtotime(str_replace("/", "-", $linha_header['data_demi']))); ?></td>
                                    <td><?php echo $linha_header['aviso_previo']; ?></td>
                                    <td><?php echo ($linha_header['data_aviso'] != "0000-00-00") ? date("d/m/Y", strtotime(str_replace("/", "-", $linha_header['data_aviso']))) : ""; ?></td>
                                    <td><?php echo $linha_header['criado_por_nome']; ?></td>
                                    <td><?php echo ($linha_header['data_formatada'] != "00/00/0000 - 00:00:00") ? $linha_header['data_formatada'] : ""; ?></td>
                                    <td align="center"><?php echo $rows_total_participantes['total'] ?></td>
                                    <td><a href="javascript:;" data-key='<?php echo $linha_header['id_header']; ?>' data-projeto="<?php echo $linha_header['id_projeto']; ?>" class="visualizar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-view.gif" title="visualizar" /></a></td>
                                    <td><a href="javascript:;" data-key='<?php echo $linha_header['id_header']; ?>' class="desprocessar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-delete.gif" title="desprocessar" /></a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </fieldset>

                <div id="lista_funcionarios"></div>

                <!-------------------------- provisão de gastos ----------------------------------------------------------------------------------------------------------------------->

                <?php if (isset($_REQUEST['mostrar_rescisao']) && $num_rows > 0) { ?>
                    <p style="text-align: left; margin-top: 20px" class="imprime">
                        <input type="submit" name="exportar_xls" value="Exportar para Excel" class="exportarExcel">
                        <input type="hidden" name="modelo_xls" value="mostrar_rescisao">
                    </p>    
                    <input type="hidden" name="header_lote" value="<?= $_REQUEST['header_lote'] ?>">
                    <input type="hidden" name="projeto_oculto" value="<?= $_REQUEST['projeto_oculto'] ?>">

                    <h3><?php echo $projeto['nome'] ?></h3>    
                    <!--<p>Total de participantes: <b><?php //echo $total_participantes["total_participantes"];      ?></b></p>-->
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto; border: 0px;"> 
                        <thead>
                            <tr style="height: 30px; background: #fff; border: 0px;">
                                <td colspan="10" class="area-xpandir-1"><span class="xpandir"></span></td>
                                <td colspan="1" class="area-xpandir-2"><div class="area"></div></td>
                                <td colspan="1" class="area-xpandir-3"><span class="xpandirr"></span></td>
                                <td colspan="1" class="area-xpandir-4"><div class="areaa"></div></td>
                                <td colspan="1" class="area-xpandir-5"><span class="xpandirrr"></span></td>
                                <td colspan="1" class="area-xpandir-6"><div class="areaaa"></div></td>
                            </tr>
                        </thead>
                        <?php $status = 0; ?>

                        <?php
                        while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

                            $mov = array();
                            $total_movimentos = array();
                            $movimentos_incide = 0;
                            $query_movimento_recisao = "SELECT A.id_mov, A.id_rescisao, A.id_clt, A.id_movimento, A.valor, TRIM(A.tipo) as tipos, B.incidencia_inss 
                                FROM tabela_morta_movimentos_recisao_lote AS A 
                                LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod)
                                WHERE A.id_clt = {$row_rel['id_clt']} AND A.id_rescisao = '{$row_rel['id_recisao']}'";

                            if ($_COOKIE['logado'] == 179) {
                                echo $query_movimento_recisao . "<br>";
                            }
                            $sql_movimento_recisao = mysql_query($query_movimento_recisao) or die("Erro ao selecionar movimentos de rescisao");

                            while ($rows_movimentos = mysql_fetch_assoc($sql_movimento_recisao)) {
                                $mov[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']][$rows_movimentos['tipos']][$rows_movimentos['id_movimento']]["valor"] = $rows_movimentos['valor'];
                                if ($rows_movimentos['tipos'] == "CREDITO" && $rows_movimentos['incidencia_inss'] == '1') {
                                    $movimentos_incide += $rows_movimentos['valor'];
                                }
                                if ($rows_movimentos['tipos'] == "DEBITO") {
                                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] += $rows_movimentos['valor'];
                                } else if ($rows_movimentos['tipos'] == "CREDITO") {
                                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] += $rows_movimentos['valor'];
                                }
                            }

                            /////////////////////
                            // MOVIMENTOS FIXOS /////
                            ///////////////////

                            $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '{$row_rel['id_clt']}'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

                            $movimentos = 0;
                            $total_rendi = 0;

                            while ($row_folha = mysql_fetch_assoc($qr_folha)) {
                                if (!empty($row_folha[ids_movimentos_estatisticas])) {

                                    $movimentos = "SELECT *
                               FROM rh_movimentos_clt
                               WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = '{$row_rel['id_clt']}' AND id_mov NOT IN(56,200) ";
                                    $qr_movimentos = mysql_query($movimentos);
                                    echo "<!-- QUERY DE TOTAL DE RENDIMENTOS::: {$movimentos} -->";

                                    while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                        $movimentos += $row_mov['valor_movimento'];
                                    }
                                }
                            }

//                        echo "<pre>";
//                            print_r($movimentos);
//                        echo "</pre>";

                            if ($movimentos > 0) {
                                $total_rendi = $movimentos / 12;
                            } else {
                                $total_rendi = 0;
                            }


                            ///////////////////////////////////////////////
                            ////////// CÁLCULO DE INSS /////////////
                            ///////////////////////////////////////////////
                            $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"] + $row_rel['lei_12_506'];
                            $data_exp = explode('-', $row_rel['data_demi']);
                            if ($base_saldo_salario > 0) {
                                //echo $base_saldo_salario;
                                $calculos->MostraINSS($base_saldo_salario, implode('-', $data_exp));
                                $inss_saldo_salario = $calculos->valor;
                                $percentual_inss = $calculos->percentual;

                                if ($row_rel['desconto_inss'] == 1) {
                                    if ($row_rel['desconto_outra_empresa'] + $inss_saldo_salario > $calculos->teto) {
                                        $inss_saldo_salario = ($calculos->teto - $row_rel['desconto_outra_empresa'] );
                                    }
                                }
                            } else {
                                $base_saldo_salario = 0;
                            }

                            //CALCULO IRRF
                            $irrf = 0;
                            $base_irrf = $base_saldo_salario - $inss_saldo_salario;
                            $calculos->MostraIRRF($base_irrf, $row_rel['id_clt'], $row_rel['id_projeto'], implode('-', $data_exp));

                            $inss_recolher = $folha->getInssARecolher($row_rel['id_clt']);
                            $class = ($cont++ % 2 == 0) ? "even" : "odd";

                            if ($status != $row_rel["codigo"]) {
                                $status = $row_rel["codigo"];
                                ?>

                                <?php if (!empty($total_sal_base)) { ?>
                                    <?php
                                    if ($row_rel['codigo'] != 20) {
                                        $total_recisao_nao_paga += $total_liquido;
                                    }
                                    ?>
                                    <tfoot>
                                        <tr class="footer">
                                            <td align="right" colspan="7">Total:</td>
                                            <td align="right"><?php echo "R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", "."); ?></td>
                                            <td align="right"><?php echo "R$ " . number_format($total_sal_base, 2, ",", "."); ?></td>
                                            <!--<td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>-->
                                            <td align="right"><?php echo "R$ " . number_format($total_saldo_salario, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_comissoes, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gratificacao, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_insalubridade, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_periculosidade, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_adicional_noturno, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_hora_extra, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gorjetas, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dsr, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_reflexo_dsr, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_477, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_479, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_sal_familia, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>    
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>    
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>    
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>    
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_diferenca_salarial, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuda_custo, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dif_dissidio, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_vale_transporte, 2, ",", "."); ?></td>
                                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuste_de_saldo, 2, ",", "."); ?></td>
                                            <td align="right"><?php echo "R$ " . number_format($total_rendimento, 2, ",", "."); ?></td>



                                            <!-- TOTAL DE DEDUÇÃO -->
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_pensao_alimenticia, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_salarial, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_multa_480, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_emprestimo_consignado, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_transporte_debito, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_alimentacao_debito, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_ss, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ss, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_devolucao, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_outros, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_faltas, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>
                                            <td align="right" class=""><?php echo "R$ " . number_format($total_deducao, 2, ",", "."); ?></td>
                                            <td align="right"><?php echo "R$ " . number_format($total_liquido, 2, ",", "."); ?></td>


                                            <!-- DETALHES IMPORTANTES -->
                                            <!-- BASES -->                        
                                            <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_inss, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_fgts, 2, ",", "."); ?></td>
                                            <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_pis, 2, ",", "."); ?></td>
                                            <td align="right" style="background: #fff; border: 0px;"></td>                       
                                            <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                                            <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                                            <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                                            <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                                            <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                                        </tr>
                                        <tr>
                                            <td colspan="37" style="border: 0px;"></td>
                                        </tr>
                                    </tfoot>

                                <?php } else { ?>
                                    <tfoot>
                                        <tr class="footer">
                                            <td colspan="74"></td>
                                        </tr>
                                    </tfoot>                    
                                <?php } ?>
                                <thead>
                                    <tr>
                                        <th colspan="13" class="cabecalho_compactar"><?php echo "<p style='text-transform:uppercase; text-align:left' > » " . $row_rel['especifica'] . " - " . $row_rel['aviso'] . "</p>"; ?></th>
                                        <th style="background: #fff; border: 0px;" ></th>
                                        <th colspan="5">EMPRESA</th>
                                    </tr>
                                    <tr style="font-size:10px !important;">
                                        <th rowspan="2">AÇÃO</th>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2"><span class="numero_rescisao">[11]</span>NOME</th>
                                        <th rowspan="2"><span class="numero_rescisao">[24]</span>DATA DE ADMISSÃO</th>
                                        <th rowspan="2"><span class="numero_rescisao">[25]</span>Data do Aviso Prévio</th>  
                                        <th rowspan="2"><span class="numero_rescisao">[26]</span>DATA DE AFASTAMENTO</th>                                
                                        <th rowspan="2">FUNÇÃO</th>  
                                        <th rowspan="2">MÉDIA DAS OUTRAS REMUNERAÇÕES</th>  
                                        <th rowspan="2">SALÁRIO BASE</th>  
                                        <!--<th rowspan="2">VALOR AVISO</th>-->  
                                        <th rowspan="2"><span class="numero_rescisao">[50]</span>SALDO DE SALÁRIO</th>

                                        <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[51]</span>COMISSÕES</th>
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[52]</span>GRATIFICAÇÃO</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[53]</span>ADICIONAL DE INSALUBRIDADE</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[54]</span>ADICIONAL DE PERICULOSIDADE</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[55]</span>ADICIONAL NOTURNO</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[56]</span>Horas Extras</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[57]</span>Gorjetas</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[58]</span>Descanso Semanal Remunerado (DSR)</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[59]</span>Reflexo do "DSR" sobre Salário Variável</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[60]</span>Multa Art. 477, § 8º/CLT</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[61]</span>Multa Art. 479/CLT</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[62]</span>Salário-Família</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[63]</span>13º Salário Proporcional</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[64]</span>13º Salário Exercício</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[65]</span>Férias Proporcionais</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS PROPORCIONAL </th> 
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[66]</span>Férias Vencidas Per. Aquisitivo</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS VENCIDAS</th> 
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[68]</span>Terço Constitucional de Férias</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[69]</span>Aviso Prévio indenizado</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[70]</span>13º Salário (Aviso-Prévio Indenizado)</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[71]</span>Férias (Aviso-Prévio Indenizado)</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[72]</span>Férias em dobro</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[73]</span>1/3 férias em dobro</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[82]</span> 1/3 DE FÉRIAS AVISO INDENIZADO </th>
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[80]</span>Diferença Salarial</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[82]</span>Ajuda de Custo Art. 470/CLT</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[95]</span>Lei 12.506</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[95]</span>Diferença Dissídio</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[106]</span>Vale Transporte</th>  
                                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[99]</span>Ajuste do Saldo Devedor</th>  
                                        <th rowspan="2" ><span class="numero_rescisao"></span>TOTAL RESCISÓRIO BRUTO</th>  

                                        <!--DEDUÇÕES--->
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[100]</span>Pensão Alimentícia</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[101]</span>Adiantamento Salarial</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[102]</span>Adiantamento de 13º Salário</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[103]</span>Aviso-Prévio Indenizado</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[104]</span>Multa Art. 480/CLT</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[105]</span>Empréstimo em Consignação</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[106]</span>Vale Transporte</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[109]</span>Vale Alimentação</th> 


                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[112.1]</span>Previdência Social</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[112.2]</span>Previdência Social - 13º Salário</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[114.1]</span>IRRF</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[114.2</span>IRRF sobre 13º Salário</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115]</span>Devolução de Crédito Indevido</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115.1]</span>Outros</th>  
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115.2]</span>Adiantamento de 13º Salário</th>
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[117]</span>Faltas</th>    
                                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[116]</span>IRRF Férias</th>  

                                        <th rowspan="2"><span class="numero_rescisao"></span>TOTAL DAS DEDUÇÕES</th>  
                                        <th rowspan="2" >VALOR RESCISÓRIO LÍQUIDO</th> 

                                        <!-- DETALHES IMPORTANTES --->
                                        <!--BASES -->
                                        <th rowspan="2" class="esconderrr">BASE INSS</th>   
                                        <th rowspan="2" class="esconderrr">BASE FGTS</th>  
                                        <th rowspan="2" class="esconderrr">BASE PIS</th>  

                                        <!--EMPRESA-->
                                        <th rowspan="2" style="background: #fff; border: 0px;"></th>   
                                        <th rowspan="2">PIS</th>   
                                        <th rowspan="2">MULTA DE 50% DO FGTS</th>   
                                        <th colspan="2">INSS A RECOLHER</th>  
                                        <th rowspan="2">FGTS A RECOLHER</th>

                                    </tr>
                                    <tr style="font-size:10px !important;">
                                        <th>EMPRESA</th>   
                                        <th>TERCEIRO</th>  
                                    </tr>
                                </thead>
                                <?php
                                //VERBAS RESCISÓRIAS
                                $total_das_medias_outras_remuneracoes = 0;
                                $total_sal_base = 0;
                                $total_valor_aviso = 0;
                                $total_saldo_salario = 0;
                                $total_comissoes = 0;
                                $total_gratificacao = 0;
                                $total_insalubridade = 0;
                                $total_periculosidade = 0;
                                $total_adicional_noturno = 0;
                                $total_hora_extra = 0;
                                $total_gorjetas = 0;
                                $total_dsr = 0;
                                $total_reflexo_dsr = 0;
                                $total_multa_477 = 0;
                                $total_multa_479 = 0;
                                $total_sal_familia = 0;
                                $total_dt_salario = 0;
                                $total_terceiro_exercicio = 0;
                                $total_ferias_pr = 0;
                                $total_ferias_aquisitivas = 0;
                                $total_terco_constitucional = 0;
                                $total_aviso_indenizado = 0;
                                $total_terceiro_ss = 0;
                                $total_f_aviso_indenizado = 0;
                                $total_f_dobro = 0;
                                $total_umterco_f_dobro = 0;
                                $total_diferenca_salarial = 0;
                                $total_ajuda_custo = 0;
                                $total_lei_12_506 = 0;
                                $total_dif_dissidio = 0;
                                $total_vale_transporte = 0;
                                $total_ajuste_de_saldo = 0;
                                $total_rendimento = 0;


                                //DEDUÇÕES
                                $total_pensao_alimenticia = 0;
                                $total_adiantamento_salarial = 0;
                                $total_adiantamento_13_salarial = 0;
                                $total_aviso_indenizado_debito = 0;
                                $total_multa_480 = 0;
                                $total_emprestimo_consignado = 0;
                                $total_vale_transporte_debito = 0;
                                $total_vale_alimentacao_debito = 0;
                                $total_inss_ss = 0;
                                $total_inss_dt = 0;
                                $total_ir_ss = 0;
                                $total_ir_dt = 0;
                                $total_devolucao = 0;
                                $total_outros = 0;
                                $total_adiantamento_13 = 0;
                                $total_faltas = 0;
                                $total_ir_ferias = 0;
                                $total_deducao = 0;
                                $total_liquido = 0;


                                //DETALHES IMPORTANTES
                                $total_umterco_ferias_aviso = 0;
                                $total_umterco_fp = 0;
                                $total_umterco_fv = 0;
                                $total_ferias_vencida = 0;
                                $total_f_dobro_fv = 0;

                                //BASES
                                $total_base_inss = 0;
                                $total_base_fgts = 0;
                                $total_base_pis = 0;
                                $total_pis = 0;
                                $total_multa_fgts = 0;
                                $total_inss_empresa = 0;
                                $total_inss_terceiro = 0;
                                $total_fgts_recolher = 0;

                                //TOTALIZADOR FÉRIAS
                                $total_ferias_a_pagar = 0;

                                //TOTALIZADOR 13° 
                                $total_decimo_a_pagar = 0;
                                ?>

                            <?php } ?>

                            <tr class="<?php echo $class ?>" style="font-size:11px;">
                                <td align="left"><a href="javascript:;" class="lanca_movimento" data-rescisao="<?php echo $row_rel['id_recisao']; ?>" data-clt="<?php echo $row_rel['id_clt']; ?>"><img src="../imagens/icones/icon-view.gif" title="lancar_movimentos" /></a></td>
                                <td align="left">
                                    <?php echo $row_rel['id_clt']; ?>
                                    <input type="hidden" name="id_clt[]" value="<?php echo $row_rel['id_clt']; ?>">
                                </td>
                                <td align="left"><a href='javascript:;' data-key='<?php echo $row_rel['id_clt']; ?>' data-nome='<?php echo $row_rel['nome']; ?>' class='calcula_multa' style='color: #4989DA; text-decoration: none;'><?php echo $row_rel['nome']; ?></a></td>
                                <td align="left"><?php echo (!empty($row_rel['data_adm'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_adm']))) : "0000-00-00"; ?></td>
                                <td align="left"><?php echo (!empty($row_rel['data_aviso'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_aviso']))) : "00/00/0000"; ?></td>
                                <td align="left"><?php echo (!empty($row_rel['data_demi'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_demi']))) : "0000-00-00"; ?></td>
                                <td align="left"><?php echo $row_rel['nome_funcao']; ?></td>
                                <td align="left"><?php
                                    echo "R$ " . number_format($total_rendi, 2, ",", ".");
                                    $total_das_medias_outras_remuneracoes += $total_rendi;
                                    ?></td>
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format($row_rel['sal_base'], 2, ",", ".");
                                    $total_sal_base += $row_rel['sal_base'];
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_a_ser_pago[$status_clt] += $row_rel['total_rendimento'] + ($total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']);
                                        }
                                    }
                                    ?>
                                </td> 
        <!--                                <td align="left" class="">
                                <?php
                                if ($row_rel['motivo'] != 60) {
                                    //linha comentada por Renato(13/03/2015) por inconsistencia
                                    //$valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade'];
                                    $valor_aviso = $row_rel['aviso_valor'];
                                    echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                                    $total_valor_aviso += $valor_aviso;
                                } else {
                                    $valor_aviso = 0;
                                    echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                                    $total_valor_aviso += $valor_aviso;
                                }
                                ?>
                                </td>-->

                                <?php
//                            echo "<pre>"; 
//                                print_r($row_rel);
//                            echo "<pre>"; 
                                ?>

                                <?php
                                if ($row_rel['fator'] == "empregador") {
                                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
                                } else if ($row_rel['fator'] == "empregado") {
                                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
                                }
                                ?>  

                                <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->
                                <td align="left" class=""><?php
                                    echo "[" . $row_rel['dias_saldo'] . "/30] <br /> R$ " . number_format($row_rel['saldo_salario'], 2, ",", ".");
                                    $total_saldo_salario += $row_rel['saldo_salario'];
                                    ?></td>
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['comissao'], 2, ",", ".");
                                    $total_comissoes += $row_rel['comissao'];
                                    ?></td> <!--- 51--->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"], 2, ",", ".");
                                    $total_gratificacao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"];
                                    ?></td> <!--- 52--->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['insalubridade'], 2, ",", ".");
                                    $total_insalubridade += $row_rel['insalubridade'];
                                    ?></td>  <!--- 53--->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"], 2, ",", ".");
                                    $total_periculosidade += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"];
                                    ?></td> <!--- 54--->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"], 2, ",", ".");
                                    $total_adicional_noturno += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"];
                                    ?></td> <!-- 55 -->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"], 2, ",", ".");
                                    $total_hora_extra += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"];
                                    ?></td> <!-- 56 -->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_gorjetas += 0;
                                    ?></td> <!-- 57 -->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"], 2, ",", ".");
                                    $total_dsr += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"];
                                    ?></td> <!-- 58 -->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_reflexo_dsr += 0;
                                    ?></td> <!-- 59 -->
                                <td align="left" class="esconder"><?php
                                    echo "R$ 0,00";
//                        $total_multa_477 += $row_rel['a477'];
//                        echo "R$ 0,00" . number_format($row_rel['a477'], 2, ",", ".");
                                    ?></td> <!-- 60 -->
                                <?php
                                if ($row_rel['motivo'] == 64) {
                                    $multa_479 = $row_rel['a479'];
                                } else if ($row_rel['motivo'] == 63) {
                                    $multa_479 = null;
                                }
                                ?>
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($multa_479, 2, ",", ".");
                                    $total_multa_479 += $multa_479;
                                    ?></td> <!-- 61 -->
                                <td align="left" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['sal_familia'], 2, ",", ".");
                                    $total_sal_familia += $row_rel['sal_familia'];
                                    ?></td> <!-- 62 -->
                                <td align="right" class="esconder"><?php
                                    echo "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] <br /> R$ " . number_format($row_rel['dt_salario'], 2, ",", ".");
                                    $total_dt_salario += $row_rel['dt_salario'];
                                    $total_decimo_a_pagar += $row_rel['dt_salario'];
                                    ?></td> <!-- 63 -->                      
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_terceiro_exercicio += 0;
                                    $total_decimo_a_pagar += 0;
                                    ?></td>    <!-- 64 -->                     
                                <td align="right" class="esconder"><?php
                                    echo "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] <br /> R$ " . number_format($row_rel['ferias_pr'], 2, ",", ".");
                                    $total_ferias_pr += $row_rel['ferias_pr'];
                                    $total_ferias_a_pagar += $row_rel['ferias_pr'];
                                    ?></td>  <!-- 65 -->  
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['umterco_fp'], 2, ",", ".");
                                    $total_umterco_fp += $row_rel['umterco_fp'];
                                    $total_ferias_a_pagar += $row_rel['umterco_fp'];
                                    ?></td> 
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['ferias_vencidas'], 2, ",", ".");
                                    $total_ferias_aquisitivas += $row_rel['ferias_vencidas'];
                                    $total_ferias_a_pagar += $row_rel['ferias_vencidas'];
                                    ?></td>  <!-- 66 -->                         
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['umterco_fv'], 2, ",", ".");
                                    $total_umterco_fv += $row_rel['umterco_fv'];
                                    $total_ferias_a_pagar += $row_rel['umterco_fv'];
                                    ?></td> 
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['umterco_fv'] + $row_rel['umterco_fp'], 2, ",", ".");
                                    $total_terco_constitucional += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                                    //linha comentada por Renato(13/03/2015) por já estar somando acima
                                    //$total_ferias_a_pagar += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                                    ?></td>    <!-- 68 -->              
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
                                    $total_aviso_indenizado += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                                    ?></td>    <!-- 69 -->              
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['terceiro_ss'], 2, ",", ".");
                                    $total_terceiro_ss += $row_rel['terceiro_ss'];
                                    $total_decimo_a_pagar += $row_rel['terceiro_ss'];
                                    ?></td>   <!-- 70 -->                      
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['ferias_aviso_indenizado'], 2, ",", ".");
                                    $total_f_aviso_indenizado += $row_rel['ferias_aviso_indenizado'];
                                    $total_ferias_a_pagar += $row_rel['ferias_aviso_indenizado'];
                                    ?></td>              <!-- 71 -->           
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['fv_dobro'], 2, ",", ".");
                                    $total_f_dobro += $row_rel['fv_dobro'];
                                    ?></td>  <!-- 72 -->                           
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['um_terco_ferias_dobro'], 2, ",", ".");
                                    $total_umterco_f_dobro += $row_rel['um_terco_ferias_dobro'];
                                    $total_ferias_a_pagar += $row_rel['um_terco_ferias_dobro'];
                                    ?></td>  <!-- 73 -->                           
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['umterco_ferias_aviso_indenizado'], 2, ",", ".");
                                    $total_umterco_ferias_aviso += $row_rel['umterco_ferias_aviso_indenizado'];
                                    $total_ferias_a_pagar += $row_rel['umterco_ferias_aviso_indenizado'];
                                    ?></td>   <!-- 82 --> 
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"], 2, ",", ".");
                                    $total_diferenca_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"];
                                    ?></td> <!-- 80 -->
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"], 2, ",", ".");
                                    $total_ajuda_custo += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"];
                                    ?></td>  <!-- 82 -->                           
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['lei_12_506'], 2, ",", ".");
                                    $total_lei_12_506 += $row_rel['lei_12_506'];
                                    ?></td>  <!-- 95 -->                           
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"], 2, ",", ".");
                                    $total_dif_dissidio += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"];
                                    ?></td>  <!-- 95 -->                           
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"], 2, ",", ".");
                                    $total_vale_transporte += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"];
                                    ?></td>  <!-- 106 -->                           
                                <td align="right" class="esconder"><?php
                                    echo "R$ " . number_format($row_rel['arredondamento_positivo'], 2, ",", ".");
                                    $total_ajuste_de_saldo += $row_rel['arredondamento_positivo'];
                                    ?></td>  <!-- 99 -->                           
                                <td align="right" class="">
                                    <?php
                                    echo "R$ " . number_format($row_rel['total_rendimento'], 2, ",", ".");
                                    $total_rendimento += $row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                                    ?>
                                </td>

                                <!--DEDUÇÕES--->

                                <?php
                                if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"])) {
                                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"];
                                } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50222]["valor"])) {
                                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][50222]["valor"];
                                } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"])) {
                                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"];
                                } else {
                                    $pensao = 0;
                                }
                                ?>
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($pensao, 2, ",", ".");
                                    $total_pensao_alimenticia += $pensao;
                                    $total_deducao_debito +=$pensao;
                                    ?></td>  <!-- 100 -->                           
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"], 2, ",", ".");
                                    $total_adiantamento_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"];
                                    ?></td>  <!-- 101 -->                           
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_adiantamento_13_salarial += 0;
                                    ?></td>  <!-- 102 -->                           
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
                                    $total_aviso_indenizado_debito += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                                    ?></td>  <!-- 103 -->                           
                                <?php
                                if ($row_rel['motivo'] == 64) {
                                    $multa_480 = null;
                                } else if ($row_rel['motivo'] == 63) {
                                    $multa_480 = $row_rescisao['a480'];
                                }
                                ?>
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($multa_480, 2, ",", ".");
                                    $total_multa_480 += $multa_480;
                                    $total_deducao_debito += $multa_480;
                                    ?></td>  <!-- 104 -->                           
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_emprestimo_consignado += 0;
                                    ?></td>  <!-- 105 -->                           
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_vale_transporte_debito += 0;
                                    ?></td>  <!-- 107 -->  
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8006]["valor"], 2, ",", ".");
                                    $total_vale_alimentacao_debito += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8006]["valor"];
                                    ?></td>  <!-- 109 -->  
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($inss_saldo_salario, 2, ",", ".");
                                    $total_inss_ss += $inss_saldo_salario;
                                    $total_deducao_debito += $inss_saldo_salario;
                                    ?></td>  <!-- 112.1 --> 
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($row_rel['inss_dt'], 2, ",", ".");
                                    $total_inss_dt += $row_rel['inss_dt'];
                                    $total_deducao_debito += $row_rel['inss_dt'];
                                    ?></td>   <!-- 112.2 -->                     
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($calculos->valor, 2, ",", ".");
                                    $total_ir_ss += $calculos->valor;
                                    $total_deducao_debito += $calculos->valor;
                                    ?></td>   <!-- 114.1 -->                     
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($row_rel['ir_dt'], 2, ",", ".");
                                    $total_ir_dt += $row_rel['ir_dt'];
                                    $total_deducao_debito += $row_rel['ir_dt'];
                                    ?></td>    <!-- 114.2 -->                    
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($row_rel['devolucao'], 2, ",", ".");
                                    $total_devolucao += $row_rel['devolucao'];
                                    $total_deducao_debito += $row_rel['devolucao'];
                                    ?></td>    <!-- 115 -->                    
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_outros += 0;
                                    ?></td>    <!-- 115.1 -->                    
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($row_rel['adiantamento_13'], 2, ",", ".");
                                    $total_adiantamento_13 += $row_rel['adiantamento_13'];
                                    ?></td>    <!-- 115.2 -->                    

                                <?php
                                if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
                                    $movimento_falta = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
                                } else {
                                    $movimento_falta = 0;
                                }
                                ?>
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($row_rel['valor_faltas'] + $movimento_falta, 2, ",", ".");
                                    $total_faltas += $row_rel['valor_faltas'] + $movimento_falta;
                                    $total_deducao_debito -= $row_rel['valor_faltas'] + $movimento_falta;
                                    ?></td>    <!-- 117 -->                    
                                <td align="right" class="esconderr"><?php
                                    echo "R$ " . number_format($row_rel['ir_ferias'], 2, ",", ".");
                                    $total_ir_ferias += $row_rel['ir_ferias'];
                                    $total_deducao_debito += $row_rel['ir_ferias'];
                                    ?></td>    <!-- 116 -->                    
                                <td align="right" class=""><?php
                                    echo "R$ " . number_format($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
                                    $total_deducao += $row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                                    ?></td> <!--echo "R$ " . number_format($total_deducao_debito + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", "."); $total_deducao += $total_deducao_debito + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']; -->         
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['total_rendimento']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']), 2, ",", ".");
                                    $total_liquido += ($row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']);
                                    ?>
                                </td>  

                                <!-- OUTROS VALORES -->
                                <!-- BASES -->

                                <td align="right" class="esconderrr"><?php
                                    echo "R$ " . number_format($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'], 2, ",", ".");
                                    $total_base_inss += $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'];
                                    ?></td> 
                                <td align="right" class="esconderrr"><?php
                                    echo "R$ " . number_format($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
                                    $total_base_fgts += $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                                    ?></td> 
                                <td align="right" class="esconderrr"><?php
                                    echo "R$ " . number_format($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss'], 2, ",", ".");
                                    $total_base_pis += $row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss'];
                                    ?></td> 
                                <td align="right" style="background: #fff; border: 0px;"></td>                       
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.01, 2, ",", ".");
                                    $total_pis += ( $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.01;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_pis_a_pagar[$status_clt] += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.01;
                                        }
                                    }
                                    ?>
                                </td>                       
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", ".");
                                    $total_multa_fgts += $folha->getMultaFgts($row_rel['id_clt']);
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            if ($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador") {
                                                $total_multa_a_pagar[$status_clt] += $folha->getMultaFgts($row_rel['id_clt']);
                                            }
                                        }
                                    }
                                    ?>
                                </td>                       
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20, 2, ",", ".");
                                    $total_inss_empresa += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_inss_empresa_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
                                        }
                                    }
                                    ?>
                                </td>  
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068, 2, ",", ".");
                                    $total_inss_terceiro += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_inss_terceiro_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
                                        }
                                    }
                                    ?>
                                </td>  
                                <td align="right">
                                    <?php
                                    if ($_COOKIE['logado'] == 179) {
                                        echo $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"];
                                        echo "<br>";
                                        echo "<br>";
                                        echo "Saldo de Salário: " . $row_rel['saldo_salario'] . "<br>";
                                        echo "Dt Salário: " . $row_rel['dt_salario'] . "<br>";
                                        echo "Movimentos Incide: " . $movimentos_incide . "<br>";
                                        echo "Saldo de salario 13°: " . $row_rel['terceiro_ss'] . "<br>";
                                        echo "Lei: " . $row_rel['lei_12_506'] . "<br>";
                                        echo "Aviso: " . $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] . "<br>";
                                        echo "<br>";
                                    }

                                    echo "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.08, 2, ",", ".");
                                    $total_fgts_recolher += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.08;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_fgts_recolher_a_pagar[$status_clt] += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.08;
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>                                

                        <?php } ?>
                        <?php
                        $total_recisao_nao_paga += $total_liquido;
                        ?>
                        <tfoot>
                            <tr class="footer">
                                <td align="right" colspan="7">Total:</td>
                                <td align="right"><?php echo "R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", "."); ?></td>
                                <td align="right"><?php echo "R$ " . number_format($total_sal_base, 2, ",", "."); ?></td>
                                <!--<td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>-->
                                <td align="right"><?php echo "R$ " . number_format($total_saldo_salario, 2, ",", "."); ?></td>

                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_comissoes, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gratificacao, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_insalubridade, 2, ",", "."); ?></td> 
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_periculosidade, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_adicional_noturno, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_hora_extra, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gorjetas, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dsr, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_reflexo_dsr, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_477, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_479, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_sal_familia, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_diferenca_salarial, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuda_custo, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dif_dissidio, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_vale_transporte, 2, ",", "."); ?></td>
                                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuste_de_saldo, 2, ",", "."); ?></td>
                                <td align="right"><?php echo "R$ " . number_format($total_rendimento, 2, ",", "."); ?></td>


                                <!-- DEDUÇÕES  -->
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_pensao_alimenticia, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr" ><?php echo "R$ " . number_format($total_adiantamento_salarial, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_multa_480, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_emprestimo_consignado, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_transporte_debito, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_alimentacao_debito, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_ss, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ss, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_devolucao, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_outros, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_faltas, 2, ",", "."); ?></td>
                                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>
                                <td align="right"><?php echo "R$ " . number_format($total_deducao, 2, ",", "."); ?></td>
                                <td align="right"><?php echo "R$ " . number_format($total_liquido, 2, ",", "."); ?></td>


                                <!-- DETALHES IMPORTANTES-->
                                <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_inss, 2, ",", "."); ?></td>
                                <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_fgts, 2, ",", "."); ?></td>
                                <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_pis, 2, ",", "."); ?></td>
                                <td align="right" style="background: #fff; border: 0px;"></td>                       
                                <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                                <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                                <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                                <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                                <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                            </tr>
                        </tfoot>
                    </table>
                    <?php foreach ($status_array as $status_clt) { ?>
                        <div class="totalizador">
                            <p class="titulo">TOTALIZADORES (<?php echo $nome_status_array[$status_clt]; ?>)</p>
                            <p>PIS: <span><?php
                                    echo "R$ " . number_format($total_pis_a_pagar[$status_clt], 2, ",", ".");
                                    $total_geral_pis += $total_pis_a_pagar[$status_clt];
                                    ?></span></p>
                            <p>GRRF: <span><?php
                                    echo "R$ " . number_format($total_multa_a_pagar[$status_clt], 2, ",", ".");
                                    $total_geral_multa += $total_multa_a_pagar[$status_clt];
                                    ?></span></p>
                            <p>FGTS RECOLHER: <span><?php
                                    echo "R$ " . number_format($total_fgts_recolher_a_pagar[$status_clt], 2, ",", ".");
                                    $total_geral_fgts_recolher += $total_fgts_recolher_a_pagar[$status_clt];
                                    ?></span></p>
                            <p>INSS RECOLHER EMPRESA: <span><?php
                                    echo "R$ " . number_format($total_inss_empresa_a_pagar[$status_clt], 2, ",", ".");
                                    $total_geral_inss_emp += $total_inss_empresa_a_pagar[$status_clt];
                                    ?></span></p>
                            <p>INSS RECOLHER TERCEIRO: <span><?php
                                    echo "R$ " . number_format($total_inss_terceiro_a_pagar[$status_clt], 2, ",", ".");
                                    $total_geral_inss_terceiro += $total_inss_terceiro_a_pagar[$status_clt];
                                    ?></span></p>

                            <p class="semborda">(+) SUBTOTAL: <span><?php
                                    echo "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt], 2, ",", ".");
                                    $sub_total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt];
                                    ?></span></p>
                            <p>(+) TOTAL A SER PAGO(RESCISÕES): <span><?php
                                    echo "R$ " . number_format($total_a_ser_pago[$status_clt], 2, ",", ".");
                                    $total_geral_a_ser_pago += $total_a_ser_pago[$status_clt];
                                    ?></span></p>
                            <p class="semborda">(=) TOTAL: <span><?php
                                    echo "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt], 2, ",", ".");
                                    $total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt];
                                    ?></span></p>
                        </div>
                    <?php } ?>

                    <div class="totalizador">
                        <p class="titulo">TOTALIZADOR GERAL</p>
                        <p>PIS: <span><?php echo "R$ " . number_format($total_geral_pis, 2, ",", "."); ?></span></p>
                        <p>GRRF: <span><?php echo "R$ " . number_format($total_geral_multa, 2, ",", "."); ?></span></p>
                        <p>FGTS RECOLHER: <span><?php echo "R$ " . number_format($total_geral_fgts_recolher, 2, ",", "."); ?></span></p>
                        <p>INSS RECOLHER EMPRESA: <span><?php echo "R$ " . number_format($total_geral_inss_emp, 2, ",", "."); ?></span></p>
                        <p>INSS RECOLHER TERCEIRO: <span><?php echo "R$ " . number_format($total_geral_inss_terceiro, 2, ",", "."); ?></span></p>

                        <p class="semborda">(+) SUBTOTAL: <span><?php echo "R$ " . number_format($sub_total_geral, 2, ",", "."); ?></span></p>
                        <p>(+) TOTAL A SER PAGO(RESCISÕES): <span><?php echo "R$ " . number_format($total_geral_a_ser_pago, 2, ",", "."); ?></span></p>
                        <p class="semborda">(=) TOTAL: <span><?php echo "R$ " . number_format($sub_total_geral + $total_geral_a_ser_pago, 2, ",", "."); ?></span></p>
                        <p class="semborda">MARGEM DE ERRO DE 1% : <span ><?php echo "R$ " . number_format(($sub_total_geral + $total_geral_a_ser_pago) + (($sub_total_geral + $total_geral_a_ser_pago) * 0.01), 2, ",", "."); ?></span></p>
                    </div>
                    <!--                    <div class="totalizador">
                                            <p class="titulo">DEMONSTRATIVO FÉRIAS E 13° SALÁRIO</p>
                                            <p>FÉRIAS: <span><?php echo "R$ " . number_format($total_ferias_a_pagar, 2, ",", "."); ?></span></p>
                                            <p>13° SALÁRIO: <span><?php echo "R$ " . number_format($total_decimo_a_pagar, 2, ",", "."); ?></span></p>
                                        </div>-->

                <?php } ?>

                <!-------------------------- fim provisão de gastos -------------------------------------------------------------------------------------------------------------------->

                <!-------------------------- provisão trabalhista ---------------------------------------------------------------------------------------------------------------------->

                <?php if (isset($_REQUEST['mostrar_prov_trab']) && $num_rows > 0) { ?>
                    <p style="text-align: left; margin-top: 20px" class="imprime">
                        <input type="submit" name="exportar_xls" value="Exportar para Excel" class="exportarExcel">
                        <input type="hidden" name="modelo_xls" value="mostrar_prov_trab">
                    </p>    
                    <input type="hidden" name="header_lote" value="<?= $_REQUEST['header_lote'] ?>">
                    <input type="hidden" name="projeto_oculto" value="<?= $_REQUEST['projeto_oculto'] ?>">
                    <h3><?php echo $projeto['nome'] ?></h3>    
                    <!--<p>Total de participantes: <b><?php //echo $total_participantes["total_participantes"];    ?></b></p>-->
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto; border: 0px;"> 
                    <!--                        <thead>
                            <tr style="height: 30px; background: #fff; border: 0px;">
                                <td colspan="11" class="area-xpandir-1"><span class="xpandir"></span></td>
                                <td colspan="1" class="area-xpandir-2"><div class="area"></div></td>
                                <td colspan="1" class="area-xpandir-3"><span class="xpandirr"></span></td>
                                <td colspan="1" class="area-xpandir-4"><div class="areaa"></div></td>
                                <td colspan="1" class="area-xpandir-5"><span class="xpandirrr"></span></td>
                                <td colspan="1" class="area-xpandir-6"><div class="areaaa"></div></td>
                            </tr>
                        </thead>-->
                        <?php $status = 0; ?>

                        <?php
                        while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

                            $mov = array();
                            $total_movimentos = array();
                            $movimentos_incide = 0;
                            $query_movimento_recisao = "SELECT A.id_mov, A.id_rescisao, A.id_clt, A.id_movimento, A.valor, TRIM(A.tipo) as tipos, B.incidencia_inss 
                                FROM tabela_morta_movimentos_recisao_lote AS A 
                                LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod)
                                WHERE A.id_clt = {$row_rel['id_clt']} AND A.id_rescisao = '{$row_rel['id_recisao']}'";
                            echo "<!--$query_movimento_recisao-->";
                            $sql_movimento_recisao = mysql_query($query_movimento_recisao) or die("Erro ao selecionar movimentos de rescisao");

                            while ($rows_movimentos = mysql_fetch_assoc($sql_movimento_recisao)) {
                                $mov[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']][$rows_movimentos['tipos']][$rows_movimentos['id_movimento']]["valor"] = $rows_movimentos['valor'];
                                if ($rows_movimentos['tipos'] == "CREDITO" && $rows_movimentos['incidencia_inss'] == '1') {
                                    $movimentos_incide += $rows_movimentos['valor'];
                                }
                                if ($rows_movimentos['tipos'] == "DEBITO") {
                                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] += $rows_movimentos['valor'];
                                } else if ($rows_movimentos['tipos'] == "CREDITO") {
                                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] += $rows_movimentos['valor'];
                                }
                            }

                            /////////////////////
                            // MOVIMENTOS FIXOS /////
                            ///////////////////

                            $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '{$row_rel['id_clt']}'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

                            $movimentos = 0;
                            $total_rendi = 0;

                            while ($row_folha = mysql_fetch_assoc($qr_folha)) {
                                if (!empty($row_folha[ids_movimentos_estatisticas])) {

                                    $movimentos = "SELECT *
                               FROM rh_movimentos_clt
                               WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = '{$row_rel['id_clt']}' AND id_mov NOT IN(56,200) ";
                                    $qr_movimentos = mysql_query($movimentos);
                                    echo "<!-- QUERY DE TOTAL DE RENDIMENTOS::: {$movimentos} -->";

                                    while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                                        $movimentos += $row_mov['valor_movimento'];
                                    }
                                }
                            }

//                        echo "<pre>";
//                            print_r($movimentos);
//                        echo "</pre>";

                            if ($movimentos > 0) {
                                $total_rendi = $movimentos / 12;
                            } else {
                                $total_rendi = 0;
                            }


                            ///////////////////////////////////////////////
                            ////////// CÁLCULO DE INSS /////////////
                            ///////////////////////////////////////////////
                            $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
                            $data_exp = explode('-', $row_rel['data_demi']);
                            if ($base_saldo_salario > 0) {
                                $calculos->MostraINSS($base_saldo_salario, implode('-', $data_exp));
                                $inss_saldo_salario = $calculos->valor;
                                $percentual_inss = $calculos->percentual;

                                if ($row_rel['desconto_inss'] == 1) {
                                    if ($row_rel['desconto_outra_empresa'] + $inss_saldo_salario > $calculos->teto) {
                                        $inss_saldo_salario = ($calculos->teto - $row_rel['desconto_outra_empresa'] );
                                    }
                                }
                            } else {
                                $base_saldo_salario = 0;
                            }

                            //CALCULO IRRF
                            $irrf = 0;
                            $base_irrf = $base_saldo_salario - $inss_saldo_salario;
                            $calculos->MostraIRRF($base_irrf, $row_rel['id_clt'], $row_rel['id_projeto'], implode('-', $data_exp));

                            $inss_recolher = $folha->getInssARecolher($row_rel['id_clt']);
                            $class = ($cont++ % 2 == 0) ? "even" : "odd";

                            if ($status != $row_rel["codigo"]) {
                                $status = $row_rel["codigo"];
                                ?>

                                <?php if (!empty($total_sal_base)) { ?>
                                    <?php
                                    if ($row_rel['codigo'] != 20) {
                                        $total_recisao_nao_paga += $total_liquido;
                                    }
                                    ?>
                                    <tfoot>
                                        <tr class="footer">
                                            <td align="right" colspan="3">Total:</td>
                                            <td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>    
                                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>    
                                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>    


                                            <td align="right" ><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                                            <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td> 
                                            <!-- TOTAL DE DEDUÇÃO -->

                                                                                <!--                            <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                                                                                                            <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                                                                                                            <td align="right" ><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                                                                                                            <td align="right" ><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                                                                                                            <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                                                                                                            <td align="right" ><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>-->


                                            <!-- DETALHES IMPORTANTES -->
                                            <!-- BASES -->                        

                                            <td align="right" style="background: #fff; border: 0px;"></td>                       
                                            <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                                            <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                                            <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                                            <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                                            <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                                        </tr>
                                        <tr>
                                            <td colspan="37" style="border: 0px;"></td>
                                        </tr>
                                    </tfoot>

                                <?php } else { ?>
                                    <tfoot>
                                        <tr class="footer">
                                            <td colspan="74"></td>
                                        </tr>
                                    </tfoot>                    
                                <?php } ?>
                                <thead>
                                    <tr>
                                        <th colspan="3" class="cabecalho_compactar"><?php echo "<p style='text-transform:uppercase; text-align:left' > » " . $row_rel['especifica'] . " - " . $row_rel['aviso'] . "</p>"; ?></th>
                                        <th colspan="15">Verbas Rescisórias</th>
                                        <!--<th colspan="6">Deduções</th>-->
                                        <th style="background: #fff; border: 0px;" ></th>
                                        <th colspan="5">EMPRESA</th>
                                    </tr>
                                    <tr style="font-size:10px !important;">
                                        <th rowspan="2">AÇÃO</th>
                                        <th rowspan="2">ID</th>
                                        <th rowspan="2"><span class="numero_rescisao">[11]</span>NOME</th>

                                        <th rowspan="2">VALOR AVISO</th>  

                                        <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->

                                        <th rowspan="2" ><span class="numero_rescisao">[63]</span>13º Salário Proporcional</th>  
                                        <th rowspan="2" ><span class="numero_rescisao">[64]</span>13º Salário Exercício</th>  
                                        <th rowspan="2" ><span class="numero_rescisao">[70]</span>13º Salário (Aviso-Prévio Indenizado)</th> 
                                        <th rowspan="2" ><span class="numero_rescisao">[65]</span>Férias Proporcionais</th>  
                                        <th rowspan="2" ><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS PROPORCIONAL </th> 
                                        <th rowspan="2" ><span class="numero_rescisao">[66]</span>Férias Vencidas Per. Aquisitivo</th>  
                                        <th rowspan="2" ><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS VENCIDAS</th> 
                                        <th rowspan="2" ><span class="numero_rescisao">[68]</span>Terço Constitucional de Férias</th>  


                                        <th rowspan="2" ><span class="numero_rescisao">[71]</span>Férias (Aviso-Prévio Indenizado)</th>  
                                        <th rowspan="2" ><span class="numero_rescisao">[72]</span>Férias em dobro</th>  
                                        <th rowspan="2" ><span class="numero_rescisao">[73]</span>1/3 férias em dobro</th>  
                                        <th rowspan="2" ><span class="numero_rescisao">[82]</span> 1/3 DE FÉRIAS AVISO INDENIZADO </th>
                                        <th rowspan="2" ><span class="numero_rescisao">[95]</span>Lei 12.506</th>  

                                        <th rowspan="2" ><span class="numero_rescisao">[69]</span>Aviso Prévio indenizado</th>

                                        <!--DEDUÇÕES--->

                                                            <!--                        <th rowspan="2" ><span class="numero_rescisao">[102]</span>Adiantamento de 13º Salário</th>  
                                                                                    <th rowspan="2" ><span class="numero_rescisao">[103]</span>Aviso-Prévio Indenizado</th>  
                                                                                     <th rowspan="2" ><span class="numero_rescisao">[112.2]</span>Previdência Social - 13º Salário</th>  
                                                                                    <th rowspan="2" ><span class="numero_rescisao">[114.2]</span>IRRF sobre 13º Salário</th>  
                                                                                    <th rowspan="2" ><span class="numero_rescisao">[115.2]</span>Adiantamento de 13º Salário</th>
                                                                                    <th rowspan="2" ><span class="numero_rescisao">[116]</span>IRRF Férias</th>  -->

                                        <!-- DETALHES IMPORTANTES --->
                                        <!--BASES -->

                                        <!--EMPRESA-->
                                        <th rowspan="2" style="background: #fff; border: 0px;"></th>   
                                        <th rowspan="2">PIS</th>   
                                        <th rowspan="2">MULTA DE 50% DO FGTS</th>   
                                        <th colspan="2">INSS A RECOLHER</th>  
                                        <th rowspan="2">FGTS A RECOLHER</th>

                                    </tr>
                                    <tr style="font-size:10px !important;">
                                        <th>EMPRESA</th>   
                                        <th>TERCEIRO</th>  
                                    </tr>
                                </thead>
                                <?php
                                //VERBAS RESCISÓRIAS
//                $total_das_medias_outras_remuneracoes = 0;
//                $total_sal_base = 0;
//                $total_valor_aviso = 0;
//                $total_saldo_salario = 0;
//                $total_comissoes = 0;
//                $total_gratificacao = 0;
//                $total_insalubridade = 0;
//                $total_periculosidade = 0;
//                $total_adicional_noturno = 0;
//                $total_hora_extra = 0;
//                $total_gorjetas = 0;
//                $total_dsr = 0;
//                $total_reflexo_dsr = 0;
//                $total_multa_477 = 0;
//                $total_multa_479 = 0;
//                $total_sal_familia = 0;
//                $total_dt_salario = 0;
//                $total_terceiro_exercicio = 0;
//                $total_ferias_pr = 0;
//                $total_ferias_aquisitivas = 0;
//                $total_terco_constitucional = 0;
//                $total_aviso_indenizado = 0;
//                $total_terceiro_ss = 0;
//                $total_f_aviso_indenizado = 0;
//                $total_f_dobro = 0;
//                $total_umterco_f_dobro = 0;
//                $total_diferenca_salarial = 0;
//                $total_ajuda_custo = 0;
//                $total_lei_12_506 = 0;
//                $total_dif_dissidio = 0;
//                $total_vale_transporte = 0;
//                $total_ajuste_de_saldo = 0;
//                $total_rendimento = 0;
                                //DEDUÇÕES
//                $total_pensao_alimenticia = 0;
//                $total_adiantamento_salarial = 0;
//                $total_adiantamento_13_salarial = 0;
//                $total_aviso_indenizado_debito = 0;
//                $total_multa_480 = 0;
//                $total_emprestimo_consignado = 0;
//                $total_vale_transporte_debito = 0;
//                $total_vale_alimentacao_debito = 0;
//                $total_inss_ss = 0;
//                $total_inss_dt = 0;
//                $total_ir_ss = 0;
//                $total_ir_dt = 0;
//                $total_devolucao = 0;
//                $total_outros = 0;
//                $total_adiantamento_13 = 0;
//                $total_faltas = 0;
//                $total_ir_ferias = 0;
//                $total_deducao = 0;
//                $total_liquido = 0;
                                //DETALHES IMPORTANTES
//                                    $total_umterco_ferias_aviso = 0;
//                                    $total_umterco_fp = 0;
//                                    $total_umterco_fv = 0;
//                                    $total_ferias_vencida = 0;
//                                    $total_f_dobro_fv = 0;
//
//                                    //BASES
//                                    $total_base_inss = 0;
//                                    $total_base_fgts = 0;
//                                    $total_base_pis = 0;
//                                    $total_pis = 0;
//                                    $total_multa_fgts = 0;
//                                    $total_inss_empresa = 0;
//                                    $total_inss_terceiro = 0;
//                                    $total_fgts_recolher = 0;
//                //TOTALIZADOR FÉRIAS
//                $total_ferias_a_pagar = 0;
//
//                //TOTALIZADOR 13° 
//                $total_decimo_a_pagar = 0;
                                ?>

                            <?php } ?>

                            <tr class="<?php echo $class ?>" style="font-size:11px;">
                                <td align="left"><a href="javascript:;" class="lanca_movimento" data-rescisao="<?php echo $row_rel['id_recisao']; ?>" data-clt="<?php echo $row_rel['id_clt']; ?>"><img src="../imagens/icones/icon-view.gif" title="lancar_movimentos" /></a></td>
                                <td align="left">
                                    <?php echo $row_rel['id_clt']; ?>
                                    <input type="hidden" name="id_clt[]" value="<?php echo $row_rel['id_clt']; ?>">
                                </td>
                                <td align="left"><a href='javascript:;' data-key='<?php echo $row_rel['id_clt']; ?>' data-nome='<?php echo $row_rel['nome']; ?>' class='calcula_multa' style='color: #4989DA; text-decoration: none;'><?php echo $row_rel['nome']; ?></a></td>
                                <td align="left" class="">
                                    <?php
                                    if ($row_rel['motivo'] != 60) {
                                        //linha comentada por Renato(13/03/2015) por inconsistencia
                                        //$valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade'];
                                        $valor_aviso = $row_rel['aviso_valor'];
                                        echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                                        $total_valor_aviso += $valor_aviso;
                                    } else {
                                        $valor_aviso = 0;
                                        echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                                        $total_valor_aviso += $valor_aviso;
                                    }
                                    ?>
                                </td>

                                <?php
                                if ($row_rel['fator'] == "empregador") {
                                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
                                } else if ($row_rel['fator'] == "empregado") {
                                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
                                }
                                ?>  

                                <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->
                                <?php
                                if ($row_rel['motivo'] == 64) {
                                    $multa_479 = $row_rel['a479'];
                                } else if ($row_rel['motivo'] == 63) {
                                    $multa_479 = null;
                                }
                                ?>
                                <td align="right" ><?php
                                    echo "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] <br /> R$ " . number_format($row_rel['dt_salario'], 2, ",", ".");
                                    $total_dt_salario += $row_rel['dt_salario'];
                                    $total_decimo_a_pagar += $row_rel['dt_salario'];
                                    ?></td> <!-- 63 -->                      
                                <td align="right" ><?php
                                    echo "R$ " . number_format(0, 2, ",", ".");
                                    $total_terceiro_exercicio += 0;
                                    $total_decimo_a_pagar += 0;
                                    ?></td>    <!-- 64 -->   
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['terceiro_ss'], 2, ",", ".");
                                    $total_terceiro_ss += $row_rel['terceiro_ss'];
                                    $total_decimo_a_pagar += $row_rel['terceiro_ss'];
                                    ?></td>   <!-- 70 -->                      
                                <td align="right" ><?php
                                    echo "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] <br /> R$ " . number_format($row_rel['ferias_pr'], 2, ",", ".");
                                    $total_ferias_pr += $row_rel['ferias_pr'];
                                    $total_ferias_a_pagar += $row_rel['ferias_pr'];
                                    ?></td>  <!-- 65 -->  
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['umterco_fp'], 2, ",", ".");
                                    $total_umterco_fp += $row_rel['umterco_fp'];
                                    $total_ferias_a_pagar += $row_rel['umterco_fp'];
                                    ?></td> 
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['ferias_vencidas'], 2, ",", ".");
                                    $total_ferias_aquisitivas += $row_rel['ferias_vencidas'];
                                    $total_ferias_a_pagar += $row_rel['ferias_vencidas'];
                                    ?></td>  <!-- 66 -->                         
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['umterco_fv'], 2, ",", ".");
                                    $total_umterco_fv += $row_rel['umterco_fv'];
                                    $total_ferias_a_pagar += $row_rel['umterco_fv'];
                                    ?></td> 
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['umterco_fv'] + $row_rel['umterco_fp'], 2, ",", ".");
                                    $total_terco_constitucional += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                                    //linha comentada por Renato(13/03/2015) por já estar somando acima
                                    //$total_ferias_a_pagar += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                                    ?></td>    <!-- 68 -->              

                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['ferias_aviso_indenizado'], 2, ",", ".");
                                    $total_f_aviso_indenizado += $row_rel['ferias_aviso_indenizado'];
                                    $total_ferias_a_pagar += $row_rel['ferias_aviso_indenizado'];
                                    ?></td>              <!-- 71 -->           
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['fv_dobro'], 2, ",", ".");
                                    $total_f_dobro += $row_rel['fv_dobro'];
                                    ?></td>  <!-- 72 -->                           
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['um_terco_ferias_dobro'], 2, ",", ".");
                                    $total_umterco_f_dobro += $row_rel['um_terco_ferias_dobro'];
                                    $total_ferias_a_pagar += $row_rel['um_terco_ferias_dobro'];
                                    ?></td>  <!-- 73 -->                           
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['umterco_ferias_aviso_indenizado'], 2, ",", ".");
                                    $total_umterco_ferias_aviso += $row_rel['umterco_ferias_aviso_indenizado'];
                                    $total_ferias_a_pagar += $row_rel['umterco_ferias_aviso_indenizado'];
                                    ?></td>   <!-- 82 --> 
                                <td align="right" ><?php
                                    echo "R$ " . number_format($row_rel['lei_12_506'], 2, ",", ".");
                                    $total_lei_12_506 += $row_rel['lei_12_506'];
                                    ?></td>  <!-- 95 -->                           
                                <td align="right" ><?php
                                    echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
                                    $total_aviso_indenizado += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                                    ?></td>    <!-- 69 -->  
                                <!--DEDUÇÕES--->

                                <?php
                                if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"])) {
                                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"];
                                } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50222]["valor"])) {
                                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][50222]["valor"];
                                } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"])) {
                                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"];
                                } else {
                                    $pensao = 0;
                                }
                                ?>
                                        <!--                        <td align="right" ><?php
                                echo "R$ " . number_format(0, 2, ",", ".");
                                $total_adiantamento_13_salarial += 0;
                                ?></td>   102                            
                                        <td align="right" ><?php
                                echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
                                $total_aviso_indenizado_debito += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                                ?></td>   103                            -->
                                <?php
                                if ($row_rel['motivo'] == 64) {
                                    $multa_480 = null;
                                } else if ($row_rel['motivo'] == 63) {
                                    $multa_480 = $row_rescisao['a480'];
                                }
                                ?>
                                        <!--                        <td align="right" ><?php
                                echo "R$ " . number_format($row_rel['inss_dt'], 2, ",", ".");
                                $total_inss_dt += $row_rel['inss_dt'];
                                $total_deducao_debito += $row_rel['inss_dt'];
                                ?></td>    112.2                      
                                        <td align="right" ><?php
                                echo "R$ " . number_format($row_rel['ir_dt'], 2, ",", ".");
                                $total_ir_dt += $row_rel['ir_dt'];
                                $total_deducao_debito += $row_rel['ir_dt'];
                                ?></td>     114.2                     
                                        <td align="right" ><?php
                                echo "R$ " . number_format($row_rel['adiantamento_13'], 2, ",", ".");
                                $total_adiantamento_13 += $row_rel['adiantamento_13'];
                                ?></td>     115.2                     -->

                                <?php
                                if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
                                    $movimento_falta = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
                                } else {
                                    $movimento_falta = 0;
                                }
                                ?>
                                        <!--<td align="right" ><?php
                                echo "R$ " . number_format($row_rel['ir_ferias'], 2, ",", ".");
                                $total_ir_ferias += $row_rel['ir_ferias'];
                                $total_deducao_debito += $row_rel['ir_ferias'];
                                ?></td>     116 -->                    

                                <!-- OUTROS VALORES -->
                                <!-- BASES -->

                                <td align="right" style="background: #fff; border: 0px;"></td>                       
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01, 2, ",", ".");
                                    $total_pis += ( $row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_pis_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
                                        }
                                    }
                                    ?>
                                </td>                       
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", ".");
                                    $total_multa_fgts += $folha->getMultaFgts($row_rel['id_clt']);
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            if ($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador") {
                                                $total_multa_a_pagar[$status_clt] += $folha->getMultaFgts($row_rel['id_clt']);
                                            }
                                        }
                                    }
                                    ?>
                                </td>                       
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20, 2, ",", ".");
                                    $total_inss_empresa += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_inss_empresa_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
                                        }
                                    }
                                    ?>
                                </td>  
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068, 2, ",", ".");
                                    $total_inss_terceiro += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_inss_terceiro_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
                                        }
                                    }
                                    ?>
                                </td>  
                                <td align="right">
                                    <?php
                                    echo "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08, 2, ",", ".");
                                    $total_fgts_recolher += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
                                    foreach ($status_array as $status_clt) {
                                        if ($row_rel['codigo'] == $status_clt) {
                                            $total_fgts_recolher_a_pagar[$status_clt] += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>                                

                        <?php } ?>
                        <?php
                        $total_recisao_nao_paga += $total_liquido;
                        ?>
                        <tfoot>
                            <tr class="footer">
                                <td align="right" colspan="3">Total:</td>
                                <td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>

                                <td align="right" ><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                                <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>

                                <!-- DEDUÇÕES  -->

                    <!--                <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                                    <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                                    <td align="right" ><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                                    <td align="right" ><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                                    <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                                    <td align="right" ><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>-->

                                <!-- DETALHES IMPORTANTES-->

                                <td align="right" style="background: #fff; border: 0px;"></td>                       
                                <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                                <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                                <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                                <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                                <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                            </tr>
                        </tfoot>
                    </table>
                    <div class="totalizador">
                        <p class="titulo">TOTALIZADORES<!--DEMONSTRATIVO FÉRIAS E 13° SALÁRIO--></p>
                        <p>FÉRIAS: <span><?php echo "R$ " . number_format($total_ferias_a_pagar, 2, ",", "."); ?></span></p>
                        <p>13° SALÁRIO: <span><?php echo "R$ " . number_format($total_decimo_a_pagar, 2, ",", "."); ?></span></p>
                        <p>PROVISÃO RESCISÕES: <span><?php echo "R$ " . number_format($total_aviso_indenizado + $total_multa_fgts + $total_lei_12_506, 2, ",", "."); ?></span></p>
                        <p>AVISO PRÉVIO: <span><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></span></p>
                        <p>MULTA FGTS: <span><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></span></p>
                        <p>LEI 12/506: <span><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></span></p>
                        <!--<p>PROVISÃO INSS S/PROV. TRABALHISTA: <span><?php //echo "R$ " . number_format(($total_decimo_a_pagar + /* $total_aviso_indenizado + */ $total_lei_12_506) * 0.268, 2, ",", "."); ?></span></p>-->
                        <p>PROVISÃO INSS S/PROV. TRABALHISTA: <span><?php echo "R$ " . number_format(($total_inss_empresa + $total_inss_terceiro), 2, ",", "."); ?></span></p>
                        <!--<p>PROVISÃO FGTS S/PROV. TRABALHISTA: <span><?php //echo "R$ " . number_format(($total_decimo_a_pagar + $total_aviso_indenizado + $total_lei_12_506) * 0.08, 2, ",", "."); ?></span></p>-->
                        <p>PROVISÃO FGTS S/PROV. TRABALHISTA: <span><?php echo "R$ " .number_format($total_fgts_recolher, 2, ",", "."); ?></span></p>
                        <!--<p>PROVISÃO PIS S/PROV. TRABALHISTA: <span><?php //echo "R$ " . number_format($total_decimo_a_pagar * 0.01, 2, ",", "."); ?></span></p>-->
                        <p>PROVISÃO PIS S/PROV. TRABALHISTA: <span><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></span></p>
                        <p>TOTAL: <span>R$ <?= number_format($total_ferias_a_pagar + $total_decimo_a_pagar + $total_aviso_indenizado + $total_multa_fgts + $total_lei_12_506, 2, ',', '.') ?></span>
                        <p>MARGEM DE ERRO (5%): <span>R$ <?= number_format(($total_ferias_a_pagar + $total_decimo_a_pagar + $total_aviso_indenizado + $total_multa_fgts + $total_lei_12_506) * 1.05, 2, ',', '.') ?></span>
                    </div>

                <?php } ?>

                <!-------------------------- fim provisão trabalhista ------------------------------------------------------------------------------------------------------------------>
            </form>
            <form name="form_movimento" action="" method="post" id="form_movimento">
                <div id="lancamento_mov">
                    <div id="box-1">
                        <input type="hidden" id="id_clt" name="id_clt" value="" />
                        <input type="hidden" id="id_rescisao" name="id_rescisao" value="" />
                        <input type="hidden" id="nome_movimento" name="nome_movimento" value="" />
                        <h3 class="descricao_box">» Lançamento de Novos Movimentos</h3>
                        <fieldset>
                            <p>
                                <label class="first">Selecione o movimento</label>
                                <?php echo montaSelect($movs, $movSel, array('name' => "movimento", 'id' => 'movimento', 'class' => 'selectPequeno texto_pequeno')); ?> 
                            </p>
                            <p>
                                <label class="first texto_pequeno">Valor do movimento</label>
                                <input type="text" name="valor_movimento" id='valor_movimento'  /> 
                            </p>
                            <p>
                                <input type="button" class="texto_pequeno" name="cadastrar_mov" id="cadastrar_mov" value="Cadastrar Movimento" />
                            </p>
                            <p class="mensagem"></p>
                        </fieldset>
                    </div>
                    <div id="box-2">
                        <h3 class="descricao_box">» Histórico de Movimentos Lançado</h3>
                        <div id="dados_histarico"></div>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>