<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');

//ARRAY DE FUNCIONARIOS PARA VISUALIZAR APENAS SERVIÇOS DE TERCEIROS
//178 => MILTON
$funcionario_contabilidade = array(178);
$funcionario_financeiro = array(71,137);

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"Contratos de Serviços Terceirizados","id_form"=>"form1");

$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$usuario_id = $usuario['id_funcionario'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$dataMesIni = date("Y-m") . "-31";

//----- CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON 
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;
    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_banco']] = $row['id_banco'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}

// CASO TENHA PROJETO (EM TODOS OS CASOS DPS DO POST)
if (isset($_REQUEST['projeto'])) {

    $id_projeto = $_REQUEST['projeto'];
    $id_banco = $_REQUEST['banco'];
    $mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos

    $where = "C.id_projeto = {$_REQUEST['projeto']} AND C.id_banco = {$_REQUEST['banco']}"; //BASE DA QUERY
    $wherePassado = "id_projeto = {$_REQUEST['projeto']}";

    $whereData = "month(C.data_vencimento) = {$mes2d} AND year(C.data_vencimento) = {$_REQUEST['ano']}";

    $dtPassado = date("Y-m", strtotime($_REQUEST['ano'] . "-" . $mes2d . "-01 -1 month"));
    $anoPassado = substr($dtPassado, 0, 4);
    $mesPassado = substr($dtPassado, 5, 2);
    $whereDataPassado = "month(data_vencimento) = {$mesPassado} AND year(data_vencimento) = {$anoPassado}";

    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    $historico = false;

    if ((isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {

        /* RECUPERANDO OS PROJETOS JA FINALIZADOS */
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
        $qr_verifica = PrestacaoContas::getQueryVerifica("terceiro", $dataMesRef, $dataMesIni);

        $rs_verifica = mysql_query($qr_verifica);
        $total_verifica = mysql_num_rows($rs_verifica);
        $projetosFaltante = array();
        $contErro = 0;
        $finalizados = array();

        while ($rowVeri = mysql_fetch_assoc($rs_verifica)) {
            //VERIFICA SE OS OUTROS NÃO ESTÃO FINALIZADOS
            if ($rowVeri['gerado_embr'] == null && $rowVeri['id_banco'] != $id_banco) {
                $btexportar = false;
                $projetosFaltante[$contErro]['nome'] = $rowVeri['projeto'];
                $projetosFaltante[$contErro]['banco'] = " Banco: " . $rowVeri['id_banco'] . " AG: " . $rowVeri['agencia'] . " CC: " . $rowVeri['conta'];
                $contErro++;
            } elseif ($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['id_banco'] == $id_banco) {  //VERIFICA SE O ATUAL ESTÁ FINALIZADO
                $btfinalizar = false;
            }

            //VERIFICA SE SÓ TEM 1 E SE JA FOI FINALIZADO
            if ($total_verifica == 1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null) {
                $btfinalizar = false;
            }

            //PRESTAÇÕES FINALIZADAS PARA A EXPORTAÇÃO
            if ($rowVeri['gerado_embr'] != null) {
                $finalizados[] = $rowVeri['id_prestacao'];
            }

            //CASO A PESQUISADA ESTIVER FINALIZADA, PEGA DO HISTÓRICO
            if ($rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null && $rowVeri['id_banco'] == $id_banco) {
                $historico = $rowVeri['id_prestacao'];
            }
        }

        if ($btfinalizar)
            $btexportar = false;

        $proj_faltantes = count($projetosFaltante);
    }

    //QUERY FILTRO E FINALIZAR
    if ($historico === false) {
        
        $qr_verifica_validacao = "SELECT nome, DATE_FORMAT(criado_em,'%H:%i:%s - %d/%m/%Y') as data_criacao
            FROM log_prestacao AS A
            LEFT JOIN (SELECT id_funcionario,nome FROM funcionario) AS B ON(A.criado_por = B.id_funcionario)
            WHERE A.id_projeto = '{$id_projeto}' AND A.id_banco = '{$_REQUEST['banco']}' AND  A.mes = '{$_REQUEST['mes']}' AND  A.ano = '{$_REQUEST['ano']}' AND A.tipo_prestacao = 'terceiro'";
        
        $qr = "SELECT *, 
                IF(mes<=3, \"1\",
                IF(mes<=6, \"2\",
                IF(mes<=9, \"3\", \"4\"))) AS trimestre
                FROM (
                SELECT 
                A.id_prestador,A.id_regiao,A.prestacao_contas,C.id_projeto AS projsaida,C.id_saida,A.id_projeto,A.c_razao,A.c_cnpj,A.assunto, DATE_FORMAT(A.contratado_em, '%Y-%m') AS data_formatada_contratada, DATE_FORMAT(A.encerrado_em, '%Y-%m') AS data_formatada_encerrado,
                A.contratado_em,A.encerrado_em, D.medida, 
                DATE_FORMAT(A.contratado_em, '%d/%m/%Y') AS contratado_embr, 
                DATE_FORMAT(A.encerrado_em, '%d/%m/%Y') AS encerrado_embr, 
                DATE_FORMAT(A.contratado_em, '%Y-%m') AS contratado_em2, 
                DATE_FORMAT(A.encerrado_em, '%Y-%m') AS encerrado_em2, 
                MONTH(C.data_vencimento) as mes, FLOOR(DATEDIFF(A.encerrado_em,A.contratado_em)/30) as vigencia,
                B.cod_sesrj,B.cod_contrato, 
                CAST(REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS contratado, 
                SUM(CAST( REPLACE(C.valor, ',', '.') as decimal(13,2))) as valor2,
                CAST(REPLACE(C.valor, ',', '.') as decimal(13,2)) AS valor_pago
                FROM prestadorservico AS A
                /*LEFT JOIN saida AS C ON (C.id_prestador=A.id_prestador AND C.status != 0 AND C.status != 1 AND C.estorno = 0 AND MONTH(data_vencimento) = '{$_REQUEST['mes']}' AND YEAR(data_vencimento) = '{$_REQUEST['ano']}' AND C.id_banco = '{$_REQUEST['banco']}')*/
                LEFT JOIN (SELECT * FROM saida WHERE status != 0 AND status != 1 AND estorno = 0 AND MONTH(data_vencimento) = '{$_REQUEST['mes']}' AND YEAR(data_vencimento) = '{$_REQUEST['ano']}' AND id_banco = '{$_REQUEST['banco']}') AS C ON (C.id_prestador=A.id_prestador)
                LEFT JOIN projeto AS B ON(C.id_projeto=B.id_projeto)
                LEFT JOIN prestador_medida AS D ON(A.id_medida=D.id_medida)
                WHERE A.id_projeto = '{$id_projeto}' AND A.status = 1
                GROUP BY A.id_prestador
                ORDER BY A.c_razao) AS B WHERE '$anoMesReferencia' BETWEEN data_formatada_contratada AND data_formatada_encerrado ";
          //echo $qr; 
//        $qr = "SELECT *,
//                IF(mes<=3, \"1\",
//                IF(mes<=6, \"2\",
//                IF(mes<=9, \"3\", \"4\"))) AS trimestre,
//                IF(anterior IS NULL, 0, anterior) as mesantes
//                FROM (
//                SELECT 
//                A.id_prestador,A.id_regiao,A.prestacao_contas,C.id_projeto as projsaida,C.id_saida,A.id_projeto,A.c_razao,A.c_cnpj,A.assunto,D.medida,
//                A.contratado_em,A.encerrado_em,
//                DATE_FORMAT(A.contratado_em, '%d/%m/%Y') as contratado_embr,
//                DATE_FORMAT(A.encerrado_em, '%d/%m/%Y') as encerrado_embr,
//                DATE_FORMAT(A.contratado_em, '%Y-%m') as contratado_em2,
//                DATE_FORMAT(A.encerrado_em, '%Y-%m') as encerrado_em2,
//                MONTH(C.data_vencimento) as mes, FLOOR(DATEDIFF(A.encerrado_em,A.contratado_em)/30) as vigencia,
//                B.cod_sesrj,B.cod_contrato,
//                CAST( REPLACE(A.valor, ',', '.') as decimal(13,2)) as contratado,
//                --(SELECT SUM(CAST( REPLACE(valor, ',', '.') as decimal(13,2))) FROM saida WHERE id_prestador = A.id_prestador AND {$wherePassado} AND {$whereDataPassado}) as anterior,
//                SUM(CAST( REPLACE(C.valor, ',', '.') as decimal(13,2))) as valor2,
//                CAST( REPLACE(C.valor, ',', '.') as decimal(13,2)) AS valor_pago
//                FROM prestadorservico AS A
//                LEFT JOIN saida AS C ON (C.id_prestador=A.id_prestador AND C.status != 0 AND C.estorno = 0 AND MONTH(data_vencimento) = '{$_REQUEST['mes']}' AND YEAR(data_vencimento) = '{$_REQUEST['ano']}')
//                LEFT JOIN projeto AS B ON(C.id_projeto=B.id_projeto)
//                LEFT JOIN prestador_medida AS D ON(A.id_medida=D.id_medida)
//                WHERE (C.status = 1 OR '{$anoMesReferencia}' BETWEEN A.contratado_em AND A.encerrado_em) AND C.estorno = 0 AND $where AND $whereData
//                AND '{$anoMesReferencia}' BETWEEN A.contratado_em AND A.encerrado_em    
//                GROUP BY A.id_prestador ORDER BY A.c_razao) AS B";
//             
    } else {
        
        //RENOMEANDO OS CMAPOS, PARA APARECEREM NO RELATÓRIO SEM MODIFICAR O HTML
        $qr = "SELECT ref_trimestre AS trimestre,
                    DATE_FORMAT(contrato_ini, '%d/%m/%Y') as contratado_embr,
                    DATE_FORMAT(contrato_fim, '%d/%m/%Y') as encerrado_embr,
                    MONTH(ano_mes_ref) as mes,
                    razao AS c_razao,
                    mes_ant AS mesantes, 
                    cnpj AS c_cnpj,
                    servico AS assunto,
                    mes_atual AS valor2,
                    mes_proxi AS contratado,
                    vigencia,
                    medida,
                    id_prestador,
                    prestacontas as prestacao_contas
                FROM prestacoes_contas_terceiro WHERE id_prestacao = {$historico} AND prestacontas = 1";
    }
    
  // echo $qr;
    
    //QUERY EXPORTAÇÃO
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        $qr = "SELECT *,
                    DATE_FORMAT(contrato_ini, '%Y-%m') as contratado_em2,
                    DATE_FORMAT(contrato_fim, '%Y-%m') as encerrado_em2
                FROM prestacoes_contas_terceiro WHERE id_prestacao IN (" . implode(",", $finalizados) . ") AND cod_unidade != ''";
    }

    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
    $reMaster = mysql_query($qrMaster);
    $roMaster = mysql_fetch_assoc($reMaster);
}

if(isset($_REQUEST['validar']) && !empty($_REQUEST['validar'])){
    $query_log = "INSERT INTO log_prestacao (id_projeto,id_banco,mes,ano,tipo_prestacao,criado_por) VALUES ('{$_REQUEST['projeto']}','{$_REQUEST['banco']}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','terceiro','{$_COOKIE['logado']}')";
    $sql_log = mysql_query($query_log);
    header("Location: finan_terceiros.php");
}

//FINALIZANDO A PRESTAÇÃO DESSE PROJETO
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    echo "<!-- " . $qr . " -->";
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);

    $qrT = "SELECT SUM(valor2) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalMes = number_format($rowT['total'], 2, ",", "");

    $referencia = "{$_REQUEST['ano']}-{$mes2d}-01";

    $campos = "id_projeto, id_regiao, id_banco, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, valor_total,status";
    $valores = array(
        $_REQUEST['projeto'],
        $rowT['id_regiao'],
        $_REQUEST['banco'],
        "terceiro",
        $referencia,
        date("Y-m-d H:i:s"),
        $usuario_id,
        $linhas,
        "0",
        $rowT['total'],
        "1");

    sqlInsert("prestacoes_contas", $campos, $valores);
    $id = mysql_insert_id();

    $matriz = array();
    $count = 0;
    if ($linhas > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $matriz[$count][] = $id;
            $matriz[$count][] = $roMaster['cod_os'];
            $matriz[$count][] = $row['cod_sesrj'];
            $matriz[$count][] = $row['cod_contrato'];
            $matriz[$count][] = $row['contratado_em'];

            $matriz[$count][] = $row['id_prestador'];
            $matriz[$count][] = $row['assunto'];
            $matriz[$count][] = $row['c_razao'];
            $matriz[$count][] = $row['c_cnpj'];
            $matriz[$count][] = $row['medida'];
            $matriz[$count][] = $row['contratado_em'];
            $matriz[$count][] = $row['encerrado_em'];
            $matriz[$count][] = $row['anterior'];
            $matriz[$count][] = $row['valor2'];
            $matriz[$count][] = $row['contratado'];

            $matriz[$count][] = $row['vigencia'];
            $matriz[$count][] = $row['trimestre'];
            $matriz[$count][] = $anoMesReferencia;
            $matriz[$count][] = $row['prestacao_contas'];

            $count++;
        }
    } else {
        $matriz[$count][] = $id;
        $matriz[$count][] = "";
        $matriz[$count][] = "Não foram adquiridos novos bens nesse mês";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
        $matriz[$count][] = "";
    }

    $campos = array(
        "id_prestacao",
        "cod_os",
        "cod_unidade",
        "cod_contrato",
        "data_apresentacao",
        "id_prestador",
        "servico",
        "razao",
        "cnpj",
        "medida",
        "contrato_ini",
        "contrato_fim",
        "mes_ant",
        "mes_atual",
        "mes_proxi",
        "vigencia",
        "ref_trimestre",
        "ano_mes_ref",
        "prestacontas"
    );
    sqlInsert("prestacoes_contas_terceiro", $campos, $matriz);
    echo "<script>location.href='finan_terceiros.php'</script>";
    exit;
}

/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
    error_reporting(E_ERROR);
    //echo $qr;exit;
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = ($linhas == 0) ? 5 : $linhas + 5; //CASO NÃO TENHA RESULTADO VAI CONTAR OS PROJETOS A ADD 5 LINHAS (CABEÇALHO)

    $qrT = "SELECT SUM(mes_atual) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalMes = number_format($rowT['total'], 2, ",", "");

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_RTER_" . date("Ymd") . "_" . $mes2d . "{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    /* ESCREVENDO NO ARQUIVO */
    /* HEADER */
    $handle = fopen($filename, "w");
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};RTER;3.1;01.01.01.01\r\n");

    /* DETAIL */
    /* --CASO NÃO TENHA BENS ADQUIRIDOS NO PERIODO SELECIONADO, MUDAR O CABEÇALHO DO DETALHE-- */
    if ($linhas == 0) {
        fwrite($handle, "S;COD_OS;COD_UNIDADE;COD_CONTRATO;ANO_MES_REF;DESCRICAO\r\n");
    } else {
        fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;DATA_APRESENTACAO;RAZAO_SOCIAL;CNPJ;SERVICO;VALOR_MES;VIGENCIA;");
        fwrite($handle, "CONTRATO_ANO_MES_INICIO;CONTRATO_ANO_MES_FIM;REF_TRI;REF_ANO_MES\r\n");
    }

    //ESCREVENDO AS LINHAS NO ARQUIVO CASO TENHA BENS
    if ($linhas >= 1) {
        while ($row = mysql_fetch_assoc($result)) {
            $valor = str_replace(".", ",", $row['mes_atual']);

            fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_unidade']};{$row['cod_contrato']};{$row['contrato_ini']};{$row['razao']};{$row['cnpj']};{$row['servico']};{$valor};");
            fwrite($handle, "{$row['medida']};{$row['contratado_em2']};{$row['encerrado_em2']};{$row['mes_proxi']};{$anoMesReferencia}\r\n");

            $id_projeto = $row['id_projeto'];
            $id_regiao = $row['id_regiao'];
        }
        unset($row);
    } else {
        fwrite($handle, "S;{$row['cod_sesrj']};556;004/2012;{$anoMesReferencia};Sem movimento;");
        $id_projeto = $_REQUEST['projeto'];
        $id_regiao = 0;
        $linhas = 1; //está dando erro qnd nao tem registro
    }

    fwrite($handle, "T;QUANTIDADE_REGISTROS;TOTAL_VALOR1\r\n");
    fwrite($handle, "T;{$linhas};{$totalMes}");

    /* ------------- */
    fclose($handle);

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($filename));
    header("Content-Disposition: attachment; filename={$fname}");
    flush();

    readfile($filename);
    exit;
}

/* FILTRO PARA MOSTRAR O RELATÓRIO */
/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    
    $result_verifica_validacao = mysql_query($qr_verifica_validacao);
    $linhas_verifica_validacao = mysql_num_rows($result_verifica_validacao);
    
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    
    $qrT = "SELECT SUM(valor2) AS total FROM (" . $qr . ") AS B";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $totalMes = number_format($rowT['total'], 2, ",", "");
    
    echo "<!-- " . $qr . " -->";
    echo "<!--VER " . $qr_verifica . " -->";
}

$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]] form-control");
$meses = mesesArray(null);
$anos = anosArray(null, null);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$erros = 0;
$idsErros = array();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: CONTRATOS DE SERVIÇOS TERCEIRIZADOS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.png" />                   
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">           
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />     
        
        <!--<link href="../net1.css" rel="stylesheet" type="text/css" />VAI SAIR-->
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>        
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>        
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function(){
                $("#form1").validationEngine();
                
                $("#projeto").change(function(){
                    var $this = $(this);
                    if($this.val() != "-1"){
                        showLoading($this,"../");
                        $.post('finan_terceiros.php', { projeto: $this.val(), method: "loadbancos" }, function(data) {
                            removeLoading();
                            if(data.status==1){
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options){
                                    selected = "";
                                    if(i==$("#bancSel").val()){
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        },"json");
                    }
                }).trigger("change");
                
                $('.acao_ocultar').click(function(){
                    $('.col_esq').toggle();
                });
            });
        </script>

        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
        </style>
        <style>
            .avisos_eventos{
                border-bottom: 1px solid #ccc;
                padding: 25px;
                box-sizing: border-box;
                background: #FFE2E2;
                position: relative;
                display: table;
                width: 100%;
            }
            .avisos_eventos h2{
                color: #930;
                margin: 10px 0px;
            }
            .col_esq{
                width: 100%;
                float: left;
                border-bottom: 1px solid #F7CCCC;
                height: 32px;
            }
            .titulo_categoria{
                display: block;
                border-bottom: 9px solid #F1AAAA;
                padding: 4px 10px;                                                
            }
            .funcionario_evento{
                padding-left: 10px;
                line-height: 28px;
                font-size: 13px;
            }
            .acao_ocultar{
                border: 10px solid transparent;
                width: 0px;
                height: 0px;
                display: block;
                position: absolute;
                right: 29px;
                border-bottom-color: #E53939;
                cursor: pointer;
            }   
        </style>
    </head>
    <body id="page-despesas" class="novaintra">
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - Prestação de Contas</h2></div>
            
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <input type="hidden" name="home" id="home" value="" />                                
                
                <fieldset>
                    <legend>Contratos de Serviços Terceirizados</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Projeto</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(PrestacaoContas::carregaProjetos($master), $projetoR, $attrPro) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Banco</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(array("-1" => "« Todos »"), null, "id='banco' name='banco' class='form-control'") ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Mês</label>
                        <div class="col-lg-4">
                            <div class="input-daterange input-group" id="bs-datepicker-range">                                
                                <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]] form-control'") ?>
                                <span class="input-group-addon">Ano</span>                                
                                <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]] form-control'") ?>
                            </div>
                            <p class="help-block">(Mês de contratação)</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" name="filtrar" value="Filtrar" class="btn btn-primary" />
                        </div>
                    </div>
                </fieldset>
                
                <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                    <br/>  
                    <p style="text-align: right;">
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Servico de Terceiros')" class="btn btn-success exportarExcel"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    </p>                                        
                    
                    <div class="alert alert-dismissable alert-warning">                
                        <strong>Unidade Gerenciada: </strong> <?php echo $projeto['nome']; ?>
                        <strong class="borda_titulo">O responsável: </strong> <?php echo $roMaster['nome']; ?>
                        <strong class="borda_titulo">Mês Referente: </strong> <?php echo $mesShow; ?>
                    </div>
                    
                    <?php 
                    if ($btfinalizar) { 
                        $queryContrRecentes = "
                        SELECT DATE_FORMAT(A.contratado_em,'%Y%m') contrat, A.c_razao, DATE_FORMAT(A.contratado_em,'%d/%m/%Y') contratadoBR, DATE_FORMAT(A.encerrado_em,'%d/%m/%Y') encerradoBR, B.nome
                        FROM prestadorservico A LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
                        WHERE (DATE_FORMAT(A.contratado_em,'%Y%m') = '{$_REQUEST['ano']}".sprintf("%02d",$_REQUEST['mes'])."' OR DATE_FORMAT(A.encerrado_em,'%Y%m') = '{$_REQUEST['ano']}".sprintf("%02d",$_REQUEST['mes'])."') AND A.status = 1 AND B.id_projeto = {$_REQUEST['projeto']}
                        ORDER BY A.id_projeto, A.c_razao, A.contratado_em, A.encerrado_em";
                        
                        //echo '<pre><!--'; print_r($queryContrRecentes); echo '--></pre>';
                        
                        //if($_COOKIE[logado] == 257){echo "TESTE";exit;}
                        $queryContrRecentes = mysql_query($queryContrRecentes);
                        
                        if(mysql_num_rows($queryContrRecentes)){?>                    
                            <div class="avisos_eventos alert alert-warning top30">
                                <div class="acao_ocultar" style="margin-right: 110px;">
                                    <p style="margin-left: 15px; font-weight: bold; color: chocolate; font-size: 15px; margin-top: -5px;">Ocultar/Listar</p>
                                </div>
                                <h4 class="titulo_categoria"><i class="fa fa-list-alt"></i> LISTA DE CONTRATOS</h4>
                                <?php 
                                while($rowContrRecentes = mysql_fetch_assoc($queryContrRecentes)){
                                    if($rowContrRecentes[contrat] == $_REQUEST['ano'].$mes2d){
                                        echo "
                                        <div class='col_esq'>
                                            <p class='funcionario_evento'><!--{$rowContrRecentes['nome']} - -->Contratado em: {$rowContrRecentes['contratadoBR']} - {$rowContrRecentes['c_razao']}</p>
                                        </div>";
                                    }else{
                                        echo "
                                        <div class='col_esq'>
                                            <p class='funcionario_evento'><!--{$rowContrRecentes['nome']} - -->Encerrado em: {$rowContrRecentes['encerradoBR']} - {$rowContrRecentes['c_razao']}</p>
                                        </div>";
                                    }
                                } ?>
                            </div><br>
                        <?php }
                    } ?>
                    <table id="tbRelatorio" class="grid table table-hover table-striped table-bordered">
                        <thead>                                                        
                            <tr>
                                <th colspan="9" class="text-center fundo_titulo">CONTRATOS DE SERVIÇOS TERCEIRIZADOS</th>
                            </tr>  
                            <tr class="titulo">
                                <th rowspan="2">-</th>
                                <th rowspan="2">Serviço Contratado</th>
                                <th rowspan="2">Razão Social Contratado</th>
                                <th rowspan="2">CNPJ Contratado</th>
                                <th rowspan="2">Unidade de Medida</th>
                                <th colspan="2">Vigência do Contrato</th>
                                <th colspan="2">Valor Contrato (R$)</th>
                            </tr>
                            <tr class="titulo">
                                <th>Data Início</th>
                                <th>Data Término</th>
                                <!-- <th>Mês Anterior</th> -->
                                <th>Mensal Estimado</th>
                                <th><?php echo mesesArray($_REQUEST['mes']) ?></th>
                            </tr>
                        </thead>
                        <tbody>                            
                        <?php
                        $totaldesc = 0;
                        while ($row = mysql_fetch_assoc($result)) {

                            $cl = "";
                            if ($row['prestacao_contas'] == 0) {
                                $totaldesc+= $row['valor2'];
                                $cl = " style='color: #C50505;font-weight: bold;'";
                            } else {
                                if (empty($row['assunto']) || empty($row['c_razao']) || empty($row['c_cnpj']) || !validaData($row['contratado_embr'], "d/m/Y") || !validaData($row['encerrado_embr'], "d/m/Y")) {
                                    $erros++;
                                    $msgErro = "Foram encontrados erros que impossibilitam a finalização da prestação. Verifique se todos os campos estão preenchidos corretamente.";
                                }
                                if ($row['projsaida'] != $row['id_projeto']) {
                                    $erros++;
                                    $msgErro = "Foram encontrados erros que impossibilitam a finalização da prestação. Saída vinculada a um prestador de outro projeto:";
                                    $idsErros[] = $row['id_saida'];
                                }
                            }

                            echo "<tr{$cl}>";
                            echo "<td>{$row['id_prestador']}</td>";
                            echo "<td>{$row['assunto']}</td>";
                            echo "<td>{$row['c_razao']}</td>";
                            echo "<td>{$row['c_cnpj']}</td>";
                            echo "<td>{$row['medida']}</td>";
                            echo "<td>{$row['contratado_embr']}</td>";
                            echo "<td>{$row['encerrado_embr']}</td>";
                            //echo "<td> - </td>";
                            //echo "<td class=\"txright\">".  number_format($row['mesantes'], 2, ",", ".") . "</td>";
                            echo "<td class=\"txright\">" . number_format($row['contratado'], 2, ",", ".") . "</td>";
                            echo "<td class=\"txright\">" . number_format($row['valor2'], 2, ",", ".") . "</td>";
                            echo "</tr>";

                            $valor_total += $row['valor2']; 
                            $total_mensal += $row['contratado'];
                        }

                        ?>
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <td colspan="6">&nbsp;</td>
                                <td class="txcenter">Total:</td>
                                <td class="txright text-bold">R$ <?php echo number_format($total_mensal, 2, ",", ".") ?></td>
                                <td class="txright text-bold">R$ <?php echo number_format($valor_total, 2, ",", ".") ?></td>
                                <!--<td class="txright">R$ -</td>-->
                            </tr>
                        </tfoot>
                    </table>
                            
                <?php } else { ?>
                    <?php if ($projetoR !== null) { ?>
                    <br/>                                        
                    <div class="alert alert-success top30">                    
                        Nenhum pagamento efetuado a terceiros nessa competência
                    </div>
                    <?php } ?>
                <?php } ?>
                
                <?php if ($projetoR !== null) { ?>
                <?php if ($btexportar) { ?>
                    <p class="controls pull-right">                                            
                        <button type="submit" class="button btn btn-primary" name="exportar"><span class="fa fa-share-square-o"></span>&nbsp;&nbsp;Exportar</button>
                    </p>
                <?php } ?>
                    
                    <div class="clear"></div>

                <br/>
                <?php if ($btfinalizar) { ?>
                    <?php //if ($erros == 0) { ?>
                        <p class="controls"> 
                            <?php if(in_array($_COOKIE['logado'], $funcionario_contabilidade)){ ?>
                                                            
                                    <button type="submit" class="button btn btn-warning" name="finalizar"><span class="fa fa-power-off"></span>&nbsp;&nbsp;Finalizar Prestação</button>
                                
                                    <?php if($linhas_verifica_validacao > 0){ ?>
                                    <div class="alert alert-warning top30">
                                        <?php while($linha = mysql_fetch_assoc($result_verifica_validacao)){ ?>
                                            <p>Prestação validada por <?php echo $linha['nome'] ?> às <?php echo $linha['data_criacao'] ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                        
                            <?php }else if(in_array($_COOKIE['logado'], $funcionario_financeiro)){ ?>
                                <?php if($linhas_verifica_validacao == 0){ ?>                                    
                                    <button type="submit" class="button btn btn-warning" name="finalizar"><span class="fa fa-check-square-o"></span>&nbsp;&nbsp;Validar Prestação</button>
                                <?php }else{ ?>
                                    <div class="alert alert-warning top30">
                                        <?php while($linha = mysql_fetch_assoc($result_verifica_validacao)){ ?>
                                            <p>Prestação validada por <?php echo $linha['nome'] ?> às <?php echo $linha['data_criacao'] ?></p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>    
                        </p>
                        
                    <?php //} else { ?>
<!--                                            
                        <div id='message-box' class='message-yellow'>
                            <p><?php //echo $msgErro . " ";
                            //echo (count($idsErros) > 0) ? implode(", ", $idsErros) : ""; ?></p>
                        </div>-->
                    <?php //} ?>
                <?php } else { ?>                    
                    <div class="alert alert-danger top30">                    
                        Prestação finalizada.
                    </div>
                <?php } ?>

                    <?php if ($proj_faltantes > 0) { ?>
                    <div class="alert alert-info top30">
                        <p>Foi verificado a existencia de <?php echo $contErro ?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                        <br />
                        <ul>
                    <?php
                    foreach ($projetosFaltante as $val) {
                        echo "<li>" . $val['nome'] . $val['banco'] . "</li>";
                    }
                    ?>
                        </ul>
                    </div>
                    <?php } ?>
                <?php } ?>
            </form>
            
            <?php include_once '../template/footer.php'; ?>
            
        </div>
    </body>
</html>