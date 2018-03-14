<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../conn.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include "../../wfunction.php";
include "../../classes_permissoes/acoes.class.php";
include "../../classes/FolhaClass.php";
include "../../classes/calculos.php";
include "../../classes/CltClass.php";

$usuario = carregaUsuario();
$id_regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$id_projeto = $_REQUEST['projeto'];
$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
$optRegiao = getRegioes();
$ACOES = new Acoes();
$folha = new Folha();
$calculos = new calculos();
$clt = new CltClass();
$sql = "";

$movimento_validos = array("5912,6007,9000,8080,9997,5012,5011,7001,6004,7003,8006,50249,80017");
$movs = array();
$movimentos = "SELECT cod,descicao,categoria FROM rh_movimentos WHERE incidencia IN('RESCISAO','FOLHA') GROUP BY cod ORDER BY descicao, categoria"; //AND cod IN(" .  implode(",", $movimento_validos). ")
$sql_movimento = mysql_query($movimentos) or die("Erro ao selecionar tipos de movimentos");
while($rows_mov = mysql_fetch_assoc($sql_movimento)){
    $movs[$rows_mov['cod']] = $rows_mov['cod'] . " - " . $rows_mov['descicao'] . " « " . $rows_mov['categoria'] . " » ";
}

$historico_gerado = "SELECT A.*, DATE_FORMAT(A.criado_em,'%d/%m/%Y - %H:%i:%s') AS data_formatada, B.nome AS nome_projeto, C.especifica, D.nome as criado_por_nome FROM header_recisao_lote AS A
                    LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                    LEFT JOIN rhstatus AS C ON(A.tipo_dispensa = C.codigo)
                    LEFT JOIN funcionario AS D ON(A.criado_por = D.id_funcionario)
                    WHERE A.id_regiao = '{$id_regiao}'
                    ORDER BY id_header DESC";
$sql_historico = mysql_query($historico_gerado) or die("Erro ao selecionar header");

$tipo_dispensa = "SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY codigo ASC";
$sql_dispensa = mysql_query($tipo_dispensa) or die("Erro ao selecionar os tipos de dispensas");
$dispensa = array();
while($linha = mysql_fetch_assoc($sql_dispensa)){
    $dispensa[$linha['codigo']] = $linha['codigo'] ." - ".  $linha['especifica'];
}


/**
 * RECUPERA TODO FGTS PAGO PARA O CLT, SOMA E CALCULA 50%
 */
if(isset($_REQUEST['method'])){
    if($_REQUEST['method'] == "soma_fgts"){
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
        FROM rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE A.id_recisao_lote = '{$_REQUEST['header']}' AND A.recisao_provisao_de_calculo = 1  GROUP BY A.id_clt ORDER BY C.codigo, B.nome";
    $visualiza_verifica = mysql_query($sql) or die("erro ao selecionar recisões");
    $dados = array();
    if($visualiza_verifica){
        while($linha = mysql_fetch_assoc($visualiza_verifica)){
            $dados[] = array("id" => $linha['id_clt'] ,"id_projeto" => $linha['projeto_rescisao'] ,"nome" => utf8_encode($linha['nome']),  "aviso" => $linha['aviso'], "status_clt" => utf8_encode($linha['especifica']), "sal_base" => $linha['sal_base'] );
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
    if(isset($_REQUEST['regiao'])){
        $regiao = $_REQUEST['regiao'];
        $criteria .= "A.id_regiao = '{$regiao}'";
    }
    
    if(isset($_REQUEST['projeto'])){
        $projeto = $_REQUEST['projeto'];
        $criteria .= " AND A.id_projeto = '{$projeto}'";
    }
    
    if(isset($_REQUEST['dispensa'])){
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

    $verifica_recisao = "SELECT * FROM rh_recisao AS A WHERE {$criteria}";
    //print_r($verifica_recisao); exit();
    $sql_verifica_recisao = mysql_query($verifica_recisao) or die("Erro ao selecionar dados de rescisão");
    $linhas_recisoes = mysql_num_rows($sql_verifica_recisao);
    
    if ($linhas_recisoes > 0) {
        $sql = "SELECT A.nome, A.aviso, C.especifica, A.sal_base, C.codigo, B.id_clt
        FROM rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE {$criteria} AND A.recisao_provisao_de_calculo = 1  GROUP BY A.id_clt ORDER BY C.codigo, B.nome";
        $query_verifica = mysql_query($sql) or die("erro ao selecionar recisões");
        
        $dados = array();
        if($query_verifica){
            while($linha = mysql_fetch_assoc($query_verifica)){
                $dados[] = array("id" => $linha['id_clt'] ,"nome" => utf8_encode($linha['nome']),  "aviso" => $linha['aviso'], "status_clt" => utf8_encode($linha['especifica']), "sal_base" => $linha['sal_base'] );
            }
            $return = array("status" => 1, "dados" => $dados);
        }
    } else {
       $return = array("status" => 2); 
    }
    
    echo json_encode($return);
    exit();
        
}

/**
 * VERIFICA OS PARTICIPANTES DO PROJETO SELECIONADO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "verificaParticipantes") {
    
    $return = array("status" => 0);
    $criteria = "";
    if(isset($_REQUEST['regiao'])){
        $regiao = $_REQUEST['regiao'];
        $criteria .= "A.id_regiao = '{$regiao}' ";
    }
    
    if(isset($_REQUEST['projeto'])){
        $projeto = $_REQUEST['projeto'];
        $criteria .= " AND A.id_projeto = '{$projeto}'";
    }
    

    $verifica_participantes = "SELECT A.id_clt, A.nome, B.nome as funcao, C.especifica AS status, D.sallimpo
                    FROM rh_clt AS A 
                    LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                    LEFT JOIN rh_folha_proc AS D ON(D.id_clt = A.id_clt)
                    LEFT JOIN rhstatus AS C ON(A.`status` = C.codigo) WHERE {$criteria} AND (A.status < 60 || A.status = 200)  GROUP BY A.id_clt ORDER BY A.nome " ;
    $sql_verifica_participantes = mysql_query($verifica_participantes) or die("Erro ao selecionar participantes");
    $linhas_participantes = mysql_num_rows($sql_verifica_participantes);
    
    if ($linhas_participantes > 0) {
         while($linha = mysql_fetch_assoc($sql_verifica_participantes)){
            $dados[] = array("id" => $linha['id_clt'] ,"nome" => utf8_encode($linha['nome']), "funcao" => utf8_encode($linha['funcao']), "status" => utf8_encode($linha['status']), "sal_base" => $linha['sallimpo']);
        }
        $return = array("status" => 1, "dados" => $dados);
    } 
    
    echo json_encode($return);
    exit();
        
}


/**
 * CADASTRA MOVIMENTOS PARA RESCISÃO, A TABELA QUE FICA ESSES MOVIMENTOS NÃO É A MESMA DOS MOVIMENTOS VÁLIDO PARA O CLT
 */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "cadastraMovimentos"){
    $return = array("status" => 0);
    $tipo_mov = "";
    $text_selected = explode("«", $_REQUEST['nome_movimento']);
    if(trim(str_replace(array("»","Â"), "", $text_selected[1])) == "DESCONTO"){
        $tipo_mov =  "DEBITO";
    }else{
        $tipo_mov = str_replace(array("»","Â"), "", $text_selected[1]);
    }
    
    $query_cad_movimentos = "INSERT INTO tabela_morta_movimentos_recisao_lote (id_rescisao,id_clt,id_movimento,tipo,valor,status) VALUES ('{$_REQUEST['id_rescisao']}','{$_REQUEST['id_clt']}','{$_REQUEST['movimento']}','{$tipo_mov}','{$_REQUEST['valor_movimento']}','1')";
    $sql_movimentos = mysql_query($query_cad_movimentos) or die("Erro ao cadastrar movimentos");
    $ult_cad = mysql_insert_id();
    if($sql_movimentos){
        
        $query_movimentos = "SELECT A.*, B.descicao FROM tabela_morta_movimentos_recisao_lote AS A
                        LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod) WHERE A.id_mov = '{$ult_cad}'";
        $sql_movs = mysql_query($query_movimentos) or die("Erro ao selecionar ultimo movimento");
        
        $dados = array();
        while($linhas_movs = mysql_fetch_assoc($sql_movs)){
            $dados[] = array("id_mov" => $linhas_movs['id_mov'], "id_rescisao" => $linhas_movs['id_rescisao'], "id_clt" => $linhas_movs['id_clt'], "id_movimento" => $linhas_movs['id_movimento'],  "tipo" => utf8_encode($linhas_movs['tipo']), "nome_movimento" => utf8_encode($linhas_movs['descicao']), "valor" => $linhas_movs['valor']);
        }
        $return = array("status" => 1, "dados" => $dados);
    }
    
    echo json_encode($return);
    exit();
}

/**
 * ATUALIZA VALOR LANÇADO PARA O MOVIMENTO
 */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "atualizaValorMovimento"){
    $return = array("status" => 0);
    $query_update_movimentos = "UPDATE tabela_morta_movimentos_recisao_lote SET valor = '{$_REQUEST['valor']}' WHERE id_mov = '{$_REQUEST['movimento']}'";
    $sql_movimentos = mysql_query($query_update_movimentos) or die("Erro ao atulizar valor do movimento");
    if($sql_movimentos){
        $return = array("status" => 1);
    }
    
    echo json_encode($return);
    exit();
    
}

/**
 * REMOVE MOVIMENTOS LANÇADO PARA O MOVIMENTO
 */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "removerMovimento"){
    $return = array("status" => 0);
        $query_remove_mov = "DELETE FROM tabela_morta_movimentos_recisao_lote WHERE id_mov = '{$_REQUEST['movimento']}'";
        $sql_remove = mysql_query($query_remove_mov) or die("Erro ao remover movimentos");
        if($sql_remove){
            $return = array("status" => 1);
        }
    echo json_encode($return);
    exit();
}

/**
 * LISTA MOVIMENTOS JA CADASTRADO PARA O CLT 
 */
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "carrega_movimentos"){
    $return = array("status" => 0);
    $query_movimentos = "SELECT A.*, B.descicao FROM tabela_morta_movimentos_recisao_lote AS A
                        LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod) WHERE A.id_rescisao = '{$_REQUEST['rescisao']}' GROUP BY A.id_mov";
    $sql_movs = mysql_query($query_movimentos) or die("Erro ao selecionar movimentos");
    if($sql_movs){
        $dados = array();
        while($linhas_movs = mysql_fetch_assoc($sql_movs)){
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
if(isset($_REQUEST['method']) && $_REQUEST['method'] == "gerarRescisao"){
    
    
    $return  = array("status" => 0);
    $query = "SELECT id_clt, nome FROM rh_clt WHERE id_projeto = '{$_REQUEST['projeto']}' AND id_clt IN(" .  implode(",", $_REQUEST['id_clt']). ") AND (status < 60 || status = 200)"; // id_clt = '53939' - (status < 60 || status = 200)
    $sql = mysql_query($query) or die("Erro ao selecionar participantes");
    $data_demi = (!empty($_REQUEST['dataDemi'])) ? date("Y-m-d", strtotime(str_replace("/", "-",$_REQUEST['dataDemi']))) : "0000-00-00";
    $data_aviso = (!empty($_REQUEST['dataAviso'])) ? date("Y-m-d", strtotime(str_replace("/", "-",$_REQUEST['dataAviso']))) : "0000-00-00";
    $query_header = "INSERT INTO header_recisao_lote (id_regiao,id_projeto,tipo_dispensa,fator,dias_de_saldo,data_demi,remuneracao_para_fins,quantidade_faltas,aviso_previo,dias_indenizados,data_aviso,devolucao_de_credito,criado_por) VALUES ('{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['dispensa']}','{$_REQUEST['fator']}','{$_REQUEST['diasSaldo']}','{$data_demi}','{$_REQUEST['remuneracoesRescisorias']}','{$_REQUEST['quantFaltas']}','{$_REQUEST['aviso']}','{$_REQUEST['diasIndOuTrab']}','{$data_aviso}','{$_REQUEST['devolucaoDeCredito']}','{$_COOKIE['logado']}')";
    $sql_header = mysql_query($query_header) or die("erro ao cadastrar header");
    $id_header = mysql_insert_id();
    
    if($sql){
        while($linha = mysql_fetch_assoc($sql)){
            
            //print_r($_REQUEST);exit();
            
            // Inicia o cURL acessando uma URL
            $URL = 'http://f71lagos.com/intranet/rh/recisao/recisao2.php';

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
            curl_close ($ch);

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
        while($linha = mysql_fetch_assoc($sql_ult_projeto)){
            
            $total_participantes = "SELECT COUNT(B.id_recisao) AS total FROM rh_recisao AS B
                                        WHERE B.id_recisao_lote = '{$linha['id_header']}'
                                        GROUP BY B.id_projeto";
            $sql_total_participantes = mysql_query($total_participantes);
            $rows_total_participantes = mysql_fetch_assoc($sql_total_participantes);
                                        
            $d[] = array(
                "id_header" => $linha['id_header'], 
                "id_projeto" =>  $linha['id_projeto'],  
                "projeto" => utf8_encode($linha['nome_projeto']), 
                "dispensa" => utf8_encode($linha['especifica']), 
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
        $return  = array("status" => 1, "dados_projeto" => $d);
    }
    
    echo json_encode($return);
    exit();
}


/**
 * ARRUMANDO AINDA
 */
if (isset($_REQUEST['mostrar_rescisao'])) {
   
    
    $id_projeto = (!empty($_REQUEST['projeto_oculto'])) ? $_REQUEST['projeto_oculto'] : $_REQUEST['projeto'];
    
    $sql = "SELECT B.desconto_inss, B.desconto_outra_empresa, D.nome as nome_funcao, C.especifica, C.codigo, A.*
        FROM rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        LEFT JOIN curso AS D ON(D.id_curso = B.id_curso)
        WHERE A.id_projeto = '{$id_projeto}' AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
        AND A.recisao_provisao_de_calculo = 1 AND A.id_recisao_lote = '{$_REQUEST['header_lote']}' ORDER BY C.codigo, B.nome";

    $sql_status = "SELECT C.codigo, C.especifica 
        FROM rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE A.id_projeto = '{$id_projeto}' AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
        AND A.recisao_provisao_de_calculo = 1 AND A.id_recisao_lote = '{$_REQUEST['header_lote']}' GROUP BY B.`status`";

    $sql_participantes = "SELECT COUNT(A.id_clt) AS total_participantes
        FROM rh_recisao AS A
        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE A.id_projeto = '{$id_projeto}' AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
        AND A.recisao_provisao_de_calculo = 1 AND A.id_recisao_lote = '{$_REQUEST['header_lote']}' GROUP BY A.id_projeto ";

    $query_participantes = mysql_query($sql_participantes);
    $total_participantes = mysql_fetch_assoc($query_participantes);
}

echo "<!-- QUERY:: {$sql} -->";
echo "<!-- QUERY_STATUS:: {$sql_status} -->";
echo "<!-- QUERY_TOTAL_PARTICIPANTE:: {$sql_participantes} -->";
echo "<!-- QUERY_VERIFICA:: {$verifica_recisao} -->";

if (!empty($sql)) {
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $status = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
    if (isset($_REQUEST['mostrar_rescisao'])) {
        $status_array = array();
        $nome_status_array = array();
        $qr_status = mysql_query($sql_status);
        while ($linhas = mysql_fetch_array($qr_status)) {
            $status_array[] = $linhas["codigo"];
            $nome_status_array[$linhas["codigo"]] = $linhas["especifica"];
        }
    }
}

$fator = array("empregado" => "Empregado", "empregador" => "Empregador");
$aviso = array("trabalhado" => "Trabalhado", "indenizado" => "Indenizado");
$contratacao = array("1" => "Determinado", "2" => "Indeterminado");


$contratoSel = (isset($_REQUEST['contrato'])) ? $_REQUEST['contrato'] : "";
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $id_regiao;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : "";
$dispensaSel = (isset($_REQUEST['dispensa'])) ? $_REQUEST['dispensa'] : "";
$fatorSel = (isset($_REQUEST['fator'])) ? $_REQUEST['fator'] : "";
$avisoPrevioSel = (isset($_REQUEST['aviso'])) ? $_REQUEST['aviso'] : "";

$filtro = false;

if(isset($_REQUEST['visualizar_participantes'])){
    $filtro = true;
    $sql = "SELECT A.id_regiao, A.id_projeto, A.nome, A.id_clt, A.data_entrada, A.status, B.especifica, C.nome AS funcao, C.salario
        FROM rh_clt AS A
        LEFT JOIN rhstatus AS B ON(B.codigo = A.status)
        LEFT JOIN curso AS C ON(C.id_curso = A.id_curso)
        WHERE A.id_regiao = {$regiaoSel} AND A.id_projeto = {$projetoSel} AND A.status != 40 AND A.status < 60 AND A.status != 200
        ORDER BY B.codigo, A.nome";
    $qry = mysql_query($sql);
    $total_participantesLt = mysql_num_rows($qry);
}

if(isset($_REQUEST['gerar_coletiva'])){
    
    //Fc = férias coletiva
    $idClt_fc = $_REQUEST['id_clt'];
    $regiao_fc = $_REQUEST['regiao'];
    $projeto_fc = $_REQUEST['projeto'];
    $dataIni_fc = converteData($_REQUEST['dataIniFerias']);
    $dataFim_fc = converteData($_REQUEST['dataFimFerias']);
    $usuario_fc = $usuario['id_funcionario'];
    
    $clts_fc = implode(",", $idClt_fc);       
    
    $verifica_fc = mysql_query("SELECT * FROM rh_ferias_coletiva WHERE id_clt IN({$clts_fc})");
    $tot_vefirica = mysql_num_rows($verifica_fc);
    
    if($tot_vefirica >= 1){
        $msg_type = "yellow";
        $msg_text = "Erro ao criar Férias Coletivas, existe algum clt que já está incluso";
    }else{
        foreach ($idClt_fc as $k => $valor){
            $getClt = $clt->getClt($idClt_fc[$k]);
            $nomeClt_fc = $getClt['nome'];
            
            $qry = mysql_query("INSERT INTO rh_ferias_coletiva(id_clt,id_regiao,id_projeto,data_inicio,data_fim,data_proc,id_user) VALUES ({$idClt_fc[$k]},$regiao_fc,$projeto_fc,'$dataIni_fc','$dataFim_fc',NOW(),$usuario_fc);") or die(mysql_error());
        }
    }
    
    if($qry){
        $msg_type = "blue";
        $msg_text = "Férias Coletivas criada com sucesso!";
    }
}

?>
<html>
    <head>
        <title>:: Intranet :: Férias em Lote</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/ramon.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine_2.6.2.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        
        <script>
            $(function() {
                
                $("#form").validationEngine();
                
                $("#dataIniFerias").datepicker();
                $("#dataFimFerias").datepicker();
                
                $("#dataIniFerias").mask("99/99/9999");
                $("#dataFimFerias").mask("99/99/9999");
                
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");                                      
                
                $("body").on("click", "#id_clt_todos", function() {
                    if ($(this).is(":checked")) {
                        $(".clts").attr("checked", true);
                    } else {
                        $(".clts").attr("checked", false);
                    }
                });
                
                $("#visualizar_participantes").click(function(){
                    $("#dataIniFerias").removeClass('validate[required]');
                    $("#dataFimFerias").removeClass('validate[required]');
                    $(".clts").removeClass('validate[minCheckbox[1]]');
                });
                
            });
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
                height: 178px;
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
            
            #dataIniFerias, #dataFimFerias{
                width: 160px;
            }
            
        </style>

    </head>
    <body class="novaintra" >  
        <div id="fgts_folha">
        
        </div>
        <div id="content" style="width: 1300px; display: table;">
            <div class="carregando">
                <div class="box-message">
                    <img src="../../imagens/loading2.gif" />
                    <p>Não foi encontrato nenhum modelo de rescisão como solicitado, <br />Isso levará alguns minutos</p>
                </div>
            </div>
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Férias Coletivas</h2>
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
                                    <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'selectPequeno')); ?>
                                </p>
                                
                                <?php if($filtro){ ?>
                                <p id="periodo_aquisitivo">
                                    <label class="first">Data das Férias:</label>
                                    <input type="text" name="dataIniFerias" id="dataIniFerias" class="inputPequeno validate[required]" placeholder="Início" />
                                    <input type="text" name="dataFimFerias" id="dataFimFerias" class="inputPequeno validate[required]" placeholder="Fim" />
                                </p>
                                <?php } ?>
                                
                            </div>
                        </div>
                    </div>
                    <br class="clear"/>
                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="hidden" name="projeto_oculto" id="projeto_oculto" />
                        <input type="submit" name="visualizar_participantes" value="Visualizar Participantes" id="visualizar_participantes"/>
                        <?php if($filtro){ ?>
                        <input type="submit" name="gerar_coletiva" value="Gerar" id="gerar_coletiva"/>
                        <?php } ?>
                    </p>
                </fieldset>
                
                <br />
                
                <?php if($filtro){ 
                    if ($total_participantesLt > 0) {
                ?>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;">
                        <thead>
                            <tr><th colspan="6"></th></tr>
                            <tr style="font-size:10px !important;">
                                <th rowspan="2"><input type="checkbox" name="id_clt_todos" id="id_clt_todos"></th>
                                <th rowspan="2">NOME</th>
                                <th rowspan="2">FUNÇÃO</th>
                                <th rowspan="2">DATA DE ENTRADA</th>
                                <th rowspan="2">SALÁRIO BRUTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $status_feriasLote = "";
                            while($result_ferias = mysql_fetch_assoc($qry)){
                                if($status_feriasLote != $result_ferias['especifica']){
                                    $status_feriasLote = $result_ferias['especifica'];
                                    echo "<tr><td colspan='5' style='background: #F0F0F7; font-weight: bold;' align='center'>".ucwords($result_ferias['especifica'])."</td><tr />";
                                }
                            ?>
                            <tr class="" style="font-size:11px;">
                                <td align="center">
                                    <?php if($result_ferias['especifica'] == "Atividade Normal"){ ?>
                                    <input type="checkbox" class="clts validate[minCheckbox[1]]" name="id_clt[]" id="id_clt_<?php echo $result_ferias['id_clt']; ?>" value="<?php echo $result_ferias['id_clt']; ?>">
                                    <?php } ?>
                                </td>
                                <td align="left"><label for="id_clt_<?php echo $result_ferias['id_clt']; ?>"><?php echo $result_ferias['nome']; ?></label></td>
                                <td align="left"><?php echo $result_ferias['funcao']; ?></td>
                                <td align="right"><?php echo converteData($result_ferias['data_entrada'], "d/m/Y"); ?></td>
                                <td align="right"><?php echo formataMoeda($result_ferias['salario']); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <br/>
                    <div id='message-box' class='message-yellow'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                <?php }
                }?>
                
                <div id='message-box' class='message-<?php echo $msg_type; ?>'>
                    <p><?php echo $msg_text; ?></p>
                </div>
                
            </form>
        </div>
    </body>
</html>