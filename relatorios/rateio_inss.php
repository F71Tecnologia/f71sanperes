<?php
if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

$arrayGambi[104] = array(
    3026 => 161.4,
//    3092 => 71.63,
//    3028 => 59.26,
//    2942 => 71.63,
//    4190 => 64.69,
//    3895 => 123.78,
//    3851 => 113.56,
//    2436 => 128.32,
//    4241 => 133.41,
//    4221 => 133.41,
    2420 => 142.23,
    3106 => 165.68,
//    4341 => 141.57,
//    2989 => 71.63,
//    3118 => 71.63,
    3127 => 59.26,
//    3913 => 150.83,
//    3131 => 71.63,
//    3875 => 131.56,
//    3140 => 71.63,
//    3141 => 71.63,
    2385 => 52.5,
//    2384 => 123.15,
    3147 => 158.03, //165.68
//    2951 => 59.26,
    3150 => 161.4,
//    3079 => 71.63
);
$arrayGambi[105] = array(
//    645 => 171.39,
    83 => 149.18,
    648 => 166.54,
    240 => 59.25,
    2461 => 123.66,
    96 => 130.34,
    2230 => 129.46,
    2465 => 141.91,
    630 => 64.25,
    2468 => 102.64,
    656 => 127.24,
    1084 => 127.01,
//    2491 => 146.48,
    682 => 175.08, //174.16
    2154 => 145.07,
    2083 => 59.25,
//    2067 => 144.85,
    2502 => 123.67,
    112 => 64.25,
    719 => 64.25,
    721 => 59.25,
    107 => 59.25,
    1078 => 127.01,
    2534 => 102.65,
    1088 => 127.01,
    1079 => 59.25,
    214 => 138.03,
    106 => 59.25,
    2561 => 66.27,
    2243 => 130.52,
    2584 => 123.67,
    1081 => 68.60,
    2585 => 68.33,
    87 => 104.1,
    636 => 64.25,
    2103 => 145.60,
    1082 => 139.49,
    2202 => 174.16,
    109 => 59.26,
    2226 => 174.16,
    2218 => 129.46,
    652 => 287.16,
    2091 => 59.25,
    667 => 145.21,
    2106 => 174.61,
    2057 => 171.17,
    2638 => 34.52,
    1085 => 59.11
);

/*
 * 07/03/2017
 * BY: MAX
 * COLOCANDO NA MÃO
 * POR CONTA DE NÃO ATUALIZAÇÃO DE CARTA DE INSS
 * GRAVOU BASE NAS FÉRIAS
 * 2603 - MAYARA CORRAL DA SILVA
 */
$arrayGambi[109] = array(
    2603 => 608.44
);

/*
 * 07/03/2017
 * BY: MAX
 * COLOCANDO NA MÃO PRA BATER OS CENTAVOS
 */
$arrayGambi[111] = array(
    2649 => 0.19
);

$arrayGambi[110] = array(
    3957 => 0.24
);

/*
 * 08/05/2017
 * BY: MAX
 * COLOCANDO NA MÃO PRA BATER OS CENTAVOS
 */
$arrayGambi[115] = array(
    4484 => 0.5
);

/**
 * 
 */
$arrayGambi[113] = array(
    2380 => 1004.55
);
$arrayGambi[114] = array(
    2487 => 113.82
);

$arrayGambi[118] = array(
    5087 => 505.15,
    2634 => 608.44,
    2173 => 608.44,
    87   => 0.14
);

$arrayGambi[119] = array(    
    2697 => 558.41
);

include('../conn.php');
include('../wfunction.php');
include("../classes/global.php");

$usuario = carregaUsuario();
if($_COOKIE['logado'] == 179){
    print_r($usuario);
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Desconto de INSS");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$ano = date('Y');
$filtro = $_REQUEST['filtrar'];

if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=rateio-inss.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>PARTICIPANTES DE VA</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

if(isset($filtro)){	
    $projeto = $_REQUEST['projeto'];
    $mes = sprintf("%02d", $_REQUEST['mes']);
    $ano = $_REQUEST['ano'];
    $tipoFolha = $_REQUEST['tipoFolha'];
    $fp_13 = false;
    /*
     * CRIEI ESSAS VARIAVEIS,
     * POIS VAI TER QUE IMPLEMENTAR
     * ESSE RELATÓRIO PARA 13º
     * 
     * @author Lucas Praxedes Serra (10/01/2016)
     * ADAPTANDO PARA 13º
     */
    switch ($tipoFolha) {
        case '1':
            $folhaTerceiro = 'AND folha.terceiro = 2';
            $_and = "AND fp.terceiro = 2";
            $fp_13 = true;
            break;
        case '2':
            $folhaTerceiro = 'AND folha.terceiro = 1 AND folha.tipo_terceiro = 3';
            $_and = "AND fp.terceiro = 1 AND fp.tipo_terceiro = 3";
            $fp_13 = true;
            break;
        case '3':
            $folhaTerceiro = 'AND folha.terceiro = 1 AND folha.tipo_terceiro = 1';
            $_and = "AND fp.terceiro = 1 AND fp.tipo_terceiro = 1";
            $fp_13 = true;
            break;
        case '4':
            $folhaTerceiro = 'AND folha.terceiro = 1 AND folha.tipo_terceiro = 2';
            $_and = "AND fp.terceiro = 1 AND fp.tipo_terceiro = 2";
            $fp_13 = true;
            break;
    }
    
    if($projeto > '0'){
        $projeto_sql = "AND folha.id_projeto = {$projeto}";
        $projeto_ferias = "AND projeto = '{$projeto}'";
        $projeto_aux = "AND id_projeto = '{$projeto}'";
    }else{
        $projeto_sql = null;
    }

    //MOVIMENTOS LANÇADOS NA FOLHA
    $sql_movimentos_estatistica = "SELECT ids_movimentos_estatisticas FROM rh_folha AS fp WHERE mes = {$mes} AND status = 3 AND projeto = {$projeto} {$_and}";
    $qry_movimentos_estatistica = mysql_query($sql_movimentos_estatistica);
    $row_movimentos_estatistica = mysql_fetch_assoc($qry_movimentos_estatistica);
    $ids_movimentos_estatisticas = $row_movimentos_estatistica['ids_movimentos_estatisticas'];
    
    $sql_descontados = "
    	SELECT folha.id_clt, folha.nome AS nome, unidade.id_unidade, folha.id_folha, unidade.unidade AS unidade, funcao.nome AS nome_curso, folha.base_inss, IF(folha.status_clt=70,0,IF(folha.inss > 0,folha.inss,folha.inss_dt)) AS inss, P.nome AS nome_projeto,                
            CAST((folha.base_inss*0.058) AS DECIMAL(12,2)) as inss_ter, IF(folha.status_clt=70,0,((folha.base_inss + folha.base_inss_13_rescisao) - IFNULL(ADT.valor_movimento,0))) AS base_inss_folha, CAST((IF(folha.inss > 0,folha.inss,folha.inss_dt)) AS DECIMAL(12,2)) as inss_folha,
            IF(folha.data_proc > '2010-06-30', (folha.a6005 + IFNULL(SMMA.valor_movimento,0)), folha.salbase) AS sal_maternidade, 
            IF(folha.data_proc > '2010-06-30', folha.a5022, folha.sallimpo_real) AS sal_familia,
            
            CAST((SELECT (IF(folha.data_proc < '2016-06-30', base_inss, 0)) AS base_inss_ferias
            FROM rh_ferias
            WHERE '{$ano}-{$mes}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1 AND MONTH(data_ini) = {$mes} AND id_clt = folha.id_clt {$projeto_ferias}
            ORDER BY id_ferias DESC) AS DECIMAL(12,2)) AS base_inss_ferias,
IF(fp.terceiro = 1,0,              
            CAST((SELECT inss AS inss_ferias
            FROM rh_ferias
            WHERE '{$ano}-{$mes}' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1 AND MONTH(data_ini) = {$mes} AND id_clt = folha.id_clt {$projeto_ferias}
            ORDER BY id_ferias DESC) AS DECIMAL(12,2))
) AS inss_ferias,
IF(fp.terceiro = 1,0,            
            CAST((SELECT IF(motivo = 60, 0, IF(MONTH(data_demi) = 1 && DAY(data_demi) >= 15, base_inss_13, 0)) AS base_inss_13
            FROM rh_recisao
            WHERE MONTH(data_demi) = {$mes} AND YEAR(data_demi) = {$ano} AND status = 1 AND rescisao_complementar = 0 AND id_clt = folha.id_clt $projeto_aux) AS DECIMAL(12,2))
) AS base_inss_13,
IF(fp.terceiro = 1,0,                
            CAST((SELECT IF(motivo=60,0,inss_dt) AS inss_13
            FROM rh_recisao
            WHERE MONTH(data_demi) = {$mes} AND YEAR(data_demi) = {$ano} AND status = 1 AND rescisao_complementar = 0 AND id_clt = folha.id_clt $projeto_aux) AS DECIMAL(12,2))
) AS inss_13,
IF(fp.terceiro = 1,0,            
            CAST((SELECT inss_ss AS inss_res
            FROM rh_recisao
            WHERE MONTH(data_demi) = {$mes} AND YEAR(data_demi) = {$ano} AND status = 1 AND rescisao_complementar = 0 AND id_clt = folha.id_clt $projeto_aux) AS DECIMAL(12,2))
) AS inss_res,
            IF(folha.status_clt = 70, folha.inss, 0) AS inss_acident
            
            FROM rh_folha_proc AS folha
            INNER JOIN rh_clt AS clt
            ON clt.id_clt = folha.id_clt
            INNER JOIN rh_folha AS fp 
            ON folha.id_folha = fp.id_folha
            INNER JOIN curso AS funcao
            ON clt.id_curso = funcao.id_curso
            INNER JOIN unidade AS unidade
            ON unidade.id_unidade = clt.id_unidade
            INNER JOIN projeto AS P
            ON P.id_projeto = folha.id_projeto
            INNER JOIN rhempresa AS EMP
            ON EMP.id_regiao = clt.id_regiao
            LEFT JOIN (SELECT * FROM rh_movimentos_clt WHERE cod_movimento IN (6009) AND status > 0) AS SMMA ON (folha.id_clt = SMMA.id_clt AND fp.mes = SMMA.mes_mov AND fp.ano = SMMA.ano_mov)
            LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80031' AND mes_mov = '14' AND id_movimento IN ({$ids_movimentos_estatisticas})) AS ADT ON (ADT.id_clt = folha.id_clt)
            WHERE folha.mes = {$mes}
            AND folha.ano = {$ano}
            AND folha.status = 3 /*AND folha.status_clt NOT IN(61,63,64,66)*/
            {$projeto_sql}
            {$_and}
            ORDER BY unidade,nome";
    $qr_descontados = mysql_query($sql_descontados);
    $total_descontados = mysql_num_rows($qr_descontados);
    
    if($_COOKIE[debug] == 666){
        echo '<br>////////////////////////$sql_descontados////////////////////////<br>';
        echo $sql_descontados;
    }
    
    $qr_totalizador = mysql_query("
        SELECT 
        SUM(base_inss_folha) as total_base_inss_folha, 
        id_folha, 
        SUM(inss_folha) as total_inss_folha, 
        SUM(base_inss_ferias) as total_base_inss_ferias,         
        SUM(inss_ferias) as total_inss_ferias,         
        SUM(base_inss_13) as total_base_inss_13,         
        SUM(inss_13) as total_inss_13,         
        SUM(inss_res) as total_inss_res,                       
        SUM(inss_acident) as total_inss_acident,
        SUM(sal_maternidade) as total_sal_maternidade,         
        SUM(sal_familia) as total_sal_familia,           
        unidade,id_unidade FROM (
            {$sql_descontados}
        )AS temp 
        GROUP BY id_unidade
    ");
    
    $matrizTotal = array();
    $matrizTotalGeral = array();
    
    while($row_totalizador = mysql_fetch_assoc($qr_totalizador)){
        $matrizTotal[$row_totalizador['id_unidade']] = $row_totalizador;
        $matrizTotalGeral['base_inss_folha'] += $row_totalizador['total_base_inss_folha'];
        $matrizTotalGeral['inss_folha'] += $row_totalizador['total_inss_folha'];
        $matrizTotalGeral['base_inss_ferias'] += $row_totalizador['total_base_inss_ferias'];                         
        $matrizTotalGeral['inss_ferias'] += $row_totalizador['total_inss_ferias'];                         
        $matrizTotalGeral['base_inss_13'] += $row_totalizador['total_base_inss_13'];                         
        $matrizTotalGeral['inss_13'] += $row_totalizador['total_inss_13'];
        $matrizTotalGeral['inss_res'] += $row_totalizador['total_inss_res'];
        $matrizTotalGeral['inss_acident'] += $row_totalizador['total_inss_acident'];
        $matrizTotalGeral['sal_maternidade'] += $row_totalizador['total_sal_maternidade'];
        $matrizTotalGeral['sal_familia'] += $row_totalizador['total_sal_familia'];        
        $matrizTotalGeral['id_folha'] = $row_totalizador['id_folha'];                         
    }
    
    if($matrizTotalGeral['id_folha'] == 47){
        $matrizTotalGeral['inss_folha'] += 292.91;
    }
    
    // Percentual RAT
    if ($ano >= 2011 AND $ano <= 2014) {
        $percentual_rat = '0.01';
    } elseif ($ano >= 2015) {
        $percentual_rat = '0.02';
    } else {
        $percentual_rat = '0.03';
    }
    
    /*if($_COOKIE['logado']==158){
        echo "<pre>";
        print_r($matrizTotal);
        echo "</pre>";
        exit;
    }*/
    
    $unidade = "";
    $unidade_nome = "";
    $toInss = 0;
    $toInss_emp = 0;
    $toInss_rat = 0;
    
    //PEGA TETO DE INSS
    $sql_teto = mysql_query("SELECT teto FROM rh_movimentos WHERE cod IN(50241) AND anobase = {$ano}") or die(mysql_error());
    $teto_inss = mysql_result($sql_teto, 0);
    
    if($tipoFolha == 1){
        //AUTONOMOS
        $sql_descontadosA = "SELECT B.nome, C.id_unidade, C.unidade AS unidade, D.nome AS nome_curso, SUM(A.valor) AS base_inss, IF(SUM(A.valor_inss) > {$teto_inss}, {$teto_inss}, SUM(A.valor_inss)) AS valor_inss
            FROM rpa_autonomo AS A
            LEFT JOIN autonomo AS B ON(A.id_autonomo = B.id_autonomo)
            LEFT JOIN unidade AS C ON(B.id_unidade = C.id_unidade)
            LEFT JOIN curso AS D ON(B.id_curso = D.id_curso)
            WHERE A.mes_competencia = {$mes} AND A.ano_competencia = {$ano} AND A.id_projeto_pag = {$projeto} AND B.status_reg = 1 /*AND B.pis NOT IN
            (SELECT pis FROM rh_folha_proc rfp LEFT JOIN rh_clt rc ON (rfp.id_clt = rc.id_clt) WHERE rfp.mes = {$mes} AND rfp.ano = {$ano} AND rfp.id_projeto = {$projeto})*/
            GROUP BY A.id_autonomo";
        $qr_descontadosA = mysql_query($sql_descontadosA);
        $total_descontadosA = mysql_num_rows($qr_descontadosA);

        $qr_totalizadorA = mysql_query("
            SELECT 
            SUM(base_inss) as total_base_inss, 
            SUM(valor_inss) as total_inss,        
            unidade, id_unidade FROM (
                {$sql_descontadosA}
            )AS temp 
            GROUP BY id_unidade
        ");

        $matrizTotalA = array();
        $matrizTotalGeralA = array();

        while($row_totalizadorA = mysql_fetch_assoc($qr_totalizadorA)){        
            $matrizTotalGeralA['base_inss'] += $row_totalizadorA['total_base_inss'];
            $matrizTotalGeralA['inss'] += $row_totalizadorA['total_inss'];                                
        }
    }
}

$arrTipoFolha = array(1 => "Normal", 2 => "13º Integral", 3 => "13º Primeira Parcela", 4 => "13º Segunda Parcela");
$tipoFolhaSel = (isset($tipoFolha)) ? $tipoFolha : null;
?>
<!doctype html>
<html>
<head>
	<meta charset="iso-8859-1">
	<title>Relatório Retenção De INSS Empresa</title>
	<!-- Bootstrap -->
    <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
    <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
    <link href="../resources/css/main.css" rel="stylesheet" media="all">
    <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
    <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
    <link href="../css/progress.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    <script src="../js/jquery-1.10.2.min.js"></script>

    <style>
        .esconde100{ display:none !important; }
        .esconde43{ display:none !important; }
        .esconde57{ display:none !important; }
    </style>
</head>
<body>
	<?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Retenção de INSS Empresa</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">Ã—</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Relatório Detalhado</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto:</label>
                            <div class="col-lg-8">
                                <?php echo montaSelect($global->carregaProjetos(1, $default = array("-1" => "« Todos os Projetos »")), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Mês:</label>
                            <div class="col-lg-8">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php echo montaSelect(AnosArray(null,null), $ano, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Folha:</label>
                            <div class="col-lg-8">
                                <?php echo montaSelect($arrTipoFolha, $tipoFolhaSel, "id='tipoFolha' name='tipoFolha' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="box" class="col-lg-2 control-label"></label>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <label class="input-group-addon" for="filtroTipo">
                                        <input type="radio" name="filtroTipo" id="filtroTipo" value="1" checked="checked"> 
                                    </label>
                                    <label class="form-control pointer" for="filtroTipo">Participantes</label> 
                                </div>
                            </div>
<!--                            <div class="col-lg-2">
                                <input type="radio" name="filtroTipo" id="filtroTipo" value="2" /> Unidade
                            </div>-->
                        </div>
                    </div>
                    <div class="panel-footer text-right controls hidden-print">
                        <?php if(!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            <input type="hidden" id="data_xls" name="data_xls" value="">
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de INSS" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger">Gerar PDF</button>
                        <?php } ?>
                            <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>
            
            <?php
            if($filtro) {
                if($total_descontados > 0) {
            ?>
            
            <?php 
//                if($usuario['id_regiao'] == 1){
//                    echo $esconde = "esconde100";
//                }
            
            ?>
            <div id="relatorio_exp">
            <table class="table table-bordered table-condensed text-sm valign-middle" id="table_excel">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Unidade</th>
                        <th class="<?php echo $esconde; ?>">BASE DE INSS</th>
                        <th class="<?php echo $esconde; ?>">INSS</th>
                        <th class="<?php echo $esconde; ?>">INSS(EMPRESA)</th>
                        <th class="<?php echo $esconde; ?>">INSS(RAT)</th>
                        <th class="<?php echo $esconde; ?>">INSS(TERCEIRO)</th>
                        <th class="<?php echo $esconde; ?>">SAL. MATERNIDADE</th>
                        <th class="<?php echo $esconde; ?>">SAL. FAMÍLIA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row_descontado = mysql_fetch_assoc($qr_descontados)) { 
                        $row_descontado['inss_folha'] -= $arrayGambi[$row_descontado['id_folha']][$row_descontado['id_clt']];
                        $matrizTotalGeral['inss_folha'] -= $arrayGambi[$row_descontado['id_folha']][$row_descontado['id_clt']];                                                
                        
                        if($row_descontado['id_folha'] == 107){
                            if($row_descontado['inss_folha'] > 0){
                                $row_descontado['inss_folha'] -= 0.01;
                                $matrizTotalGeral['inss_folha'] -= 0.01;
                            }
                            
                            if($row_descontado['id_clt'] == 4656){
                                $row_descontado['inss_folha'] -= 1.18;
                                $matrizTotalGeral['inss_folha'] -= 1.18;
                            }
                        }
                        
                        if($row_descontado['id_folha'] == 108){
                            if($row_descontado['id_clt'] == 2343){
                                $row_descontado['inss_folha'] -= 740.25;
                                $matrizTotalGeral['inss_folha'] -= 740.25;
                            }
                        }
                        
                        if($row_descontado['id_folha'] == 116){
                            if($row_descontado['id_clt'] == 4974){
                                $row_descontado['inss_folha'] -= 0.17;
                                $matrizTotalGeral['inss_folha'] -= 0.17;
                            }
                        }
                        
                        if($row_descontado['id_folha'] == 96){
                            if($row_descontado['id_clt'] == 809){
                                
                                $row_descontado['inss_folha'] += 203.31;
                                $matrizTotalGeral['inss_folha'] += 203.31;
                                
                            } else if($row_descontado['id_clt'] == 100){
                                
                                $row_descontado['inss_folha'] += 382.76;
                                $matrizTotalGeral['inss_folha'] += 382.76;
                                
                            } else if($row_descontado['id_clt'] == 2524){
                                
                                $row_descontado['inss_folha'] += 414.55;
                                $matrizTotalGeral['inss_folha'] += 414.55;
                                
                            } else if($row_descontado['id_clt'] == 781){
                                
                                $row_descontado['inss_folha'] += 303.45;
                                $matrizTotalGeral['inss_folha'] += 303.45;
                                
                            } else if($row_descontado['id_clt'] == 2572){
                                
                                $row_descontado['inss_folha'] += 570.88;
                                $matrizTotalGeral['inss_folha'] += 570.88;
                                
                            } else if($row_descontado['id_clt'] == 2179){
                                
                                $row_descontado['inss_folha'] += 570.88;
                                $matrizTotalGeral['inss_folha'] += 570.88;
                                
                            } else if($row_descontado['id_clt'] == 828){
                                
                                $row_descontado['inss_folha'] += 325.07;
                                $matrizTotalGeral['inss_folha'] += 325.07;
                                
                            } 
//                            else if($row_descontado['id_clt'] == 3178){
//                                
//                                $row_descontado['inss_folha'] += 570.88;
//                                $matrizTotalGeral['inss_folha'] += 570.88;
//                                
//                            }
                        } else if($row_descontado['id_folha'] == 98) {
                            if($row_descontado['id_clt'] == 4736){
                                
                                $row_descontado['inss_folha'] += 106.66;
                                $matrizTotalGeral['inss_folha'] += 106.66;
                                
                            } else if($row_descontado['id_clt'] == 1048){
                                
                                $row_descontado['inss_folha'] += 143.15;
                                $matrizTotalGeral['inss_folha'] += 143.15;
                                
                            } else if($row_descontado['id_clt'] == 4735){
                                
                                $row_descontado['inss_folha'] += 20.99;
                                $matrizTotalGeral['inss_folha'] += 20.99;
                                
                            } else if($row_descontado['id_clt'] == 4630){
                                
                                $row_descontado['inss_folha'] += 91.52;
                                $matrizTotalGeral['inss_folha'] += 91.52;
                                
                            } else if($row_descontado['id_clt'] == 246){
                                
                                $row_descontado['inss_folha'] += 296.06;
                                $matrizTotalGeral['inss_folha'] += 296.06;
                                
                            }
                        } else if($row_descontado['id_folha'] == 97) {
                            if($row_descontado['id_clt'] == 4264){
                                
                                $row_descontado['inss_folha'] += 570.88;
                                $matrizTotalGeral['inss_folha'] += 570.88;
                                
                            } else if($row_descontado['id_clt'] == 3035){
                                
                                $row_descontado['inss_folha'] += 570.88;
                                $matrizTotalGeral['inss_folha'] += 570.88;
                                
                            } else if($row_descontado['id_clt'] == 4705){
                                
                                $row_descontado['inss_folha'] += 15.45;
                                $matrizTotalGeral['inss_folha'] += 15.45;
                                
                            } else if($row_descontado['id_clt'] == 3223){
                                
                                $row_descontado['inss_folha'] += 389.24;
                                $matrizTotalGeral['inss_folha'] += 389.24;
                                
                            } else if($row_descontado['id_clt'] == 2957){
                                
                                $row_descontado['inss_folha'] += 531.67 - 92.32;
                                $matrizTotalGeral['inss_folha'] += 531.67 - 92.32;
                                
                            } else if($row_descontado['id_clt'] == 2673){
                                
                                $row_descontado['inss_folha'] += 351.59;
                                $matrizTotalGeral['inss_folha'] += 351.59;
                                
                            } else if($row_descontado['id_clt'] == 2755){
                                
                                $row_descontado['inss_folha'] += 102.30;
                                $matrizTotalGeral['inss_folha'] += 102.30;
                                
                            } else if($row_descontado['id_clt'] == 4655){
                                
                                $row_descontado['inss_folha'] += 52.47;
                                $matrizTotalGeral['inss_folha'] += 52.47;
                                
                            }
                        } else if($row_descontado['id_folha'] == 119) {
                            if($row_descontado['id_clt'] == 4975){                                
                                $row_descontado['base_inss_folha'] -= 1814.41;
                                $matrizTotalGeral['base_inss_folha'] -= 1814.41;
                            }
                        } else if($row_descontado['id_folha'] == 118) {
                            if($row_descontado['id_clt'] == 87){                                
                                $row_descontado['base_inss_folha'] += 0.05;
                                $matrizTotalGeral['base_inss_folha'] += 0.05;
                            }
                        }
                        
                        
                        
                    if($unidade != $row_descontado['id_unidade']){
                        //CABEÇALHO, PRINTADO LINHA TOTALIZADOR
//                        if(!empty($unidade)){
//                            echo "<tr class='warning'><td class='text-right' colspan='3'> {$unidade_nome}: </td>
//                                <td class=" . $esconde .">".number_format($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13'], 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format($matrizTotal[$unidade]['total_inss_folha'] + $matrizTotal[$unidade]['total_inss_ferias'] + $matrizTotal[$unidade]['total_inss_13'] + $matrizTotal[$unidade]['total_inss_res'], 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13']) * 0.2, 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13']) * $percentual_rat, 2, ',', '.')."</td>                                                                
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13']) * 0.058, 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_sal_maternidade']), 2, ',', '.')."</td>                                
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_sal_familia']), 2, ',', '.')."</td>";
//                        }
                        
                        //CABEÇALHO, MUDANÇA DE UNIDADE
                        //echo "<tr class='active'><td colspan='6' class='text-center'> <i class='fa fa-home'></i>  {$row_descontado['unidade']}</td></tr>";
                        $unidade = $row_descontado['id_unidade'];
                        $unidade_nome = $row_descontado['unidade'];
                    }

                    ?>
                    <tr class="linhasParticipantes">
                        <td><?php echo $row_descontado['nome']; ?></td>
                        <td><?php echo $row_descontado['nome_curso']; ?></td>
                        <td><?php echo $unidade_nome; ?></td> 
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($row_descontado['base_inss_folha'] + $row_descontado['base_inss_ferias'] + $row_descontado['base_inss_13'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>">
                            <?php
                            $total_inss = ($row_descontado['inss_folha'] + $row_descontado['inss_ferias'] + $row_descontado['inss_13'] + $row_descontado['inss_res']) - $row_descontado['inss_acident'];
                            
                            echo ($total_inss == 0) ? "-" : number_format($total_inss, 2, ',', '.');
                            ?>
                        </td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontado['base_inss_folha'] + $row_descontado['base_inss_ferias'] + $row_descontado['base_inss_13']) * 0.2, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontado['base_inss_folha'] + $row_descontado['base_inss_ferias'] + $row_descontado['base_inss_13']) * $percentual_rat, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontado['base_inss_folha'] + $row_descontado['base_inss_ferias'] + $row_descontado['base_inss_13']) * 0.058, 2, ',', '.');  ?></td>                        
                        <td class="<?php echo $esconde; ?>"><?php echo ($row_descontado['sal_maternidade'] != 0) ? number_format(($row_descontado['sal_maternidade']), 2, ',', '.') : "-";  ?></td>                        
                        <td class="<?php echo $esconde; ?>"><?php echo ($row_descontado['sal_familia'] != 0) ? number_format(($row_descontado['sal_familia']), 2, ',', '.') : "-";  ?></td>                        
                    </tr>                    
                    
                    <?php                    
//                    echo "<tr class='warning'><td class='text-right' colspan='3'> {$unidade_nome}: </td> 
//                                <td class=" . $esconde .">".number_format($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13'], 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format($matrizTotal[$unidade]['total_inss_folha'] + $matrizTotal[$unidade]['total_inss_ferias'] + $matrizTotal[$unidade]['total_inss_13'] + $matrizTotal[$unidade]['total_inss_res'], 2, ',', '.')."</td>                                
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13']) * 0.2, 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13']) * $percentual_rat, 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_base_inss_folha'] + $matrizTotal[$unidade]['total_base_inss_ferias'] + $matrizTotal[$unidade]['total_base_inss_13']) * 0.058, 2, ',', '.')."</td>
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_sal_maternidade']), 2, ',', '.')."</td>                                
//                                <td class=" . $esconde .">".number_format(($matrizTotal[$unidade]['total_sal_familia']), 2, ',', '.')."</td>";
                    }
                    ?>
                </tbody>
                <tr class='danger'>
                        <td class='text-right' colspan="3">TOTAL CLT:</td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($matrizTotalGeral['base_inss_folha'] + $matrizTotalGeral['base_inss_ferias'] + $matrizTotalGeral['base_inss_13'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['inss_folha'] + $matrizTotalGeral['inss_ferias'] + $matrizTotalGeral['inss_13'] + $matrizTotalGeral['inss_res']) - $matrizTotalGeral['inss_acident'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['base_inss_folha'] + $matrizTotalGeral['base_inss_ferias'] + $matrizTotalGeral['base_inss_13']) * 0.2, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['base_inss_folha'] + $matrizTotalGeral['base_inss_ferias'] + $matrizTotalGeral['base_inss_13']) * $percentual_rat, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['base_inss_folha'] + $matrizTotalGeral['base_inss_ferias'] + $matrizTotalGeral['base_inss_13']) * 0.058, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['sal_maternidade']), 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['sal_familia']), 2, ',', '.');  ?></td>
                    </tr>
                    
                <?php if($tipoFolha == 1){ ?>                    
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Unidade</th>
                        <th class="<?php echo $esconde; ?>">BASE DE INSS</th>
                        <th class="<?php echo $esconde; ?>">INSS</th>
                        <th class="<?php echo $esconde; ?>">INSS(EMPRESA)</th>
                        <th class="<?php echo $esconde; ?>">INSS(RAT)</th>
                        <th class="<?php echo $esconde; ?>">INSS(TERCEIRO)</th>
                        <th class="<?php echo $esconde; ?>">SAL. MATERNIDADE</th>
                        <th class="<?php echo $esconde; ?>">SAL. FAMÍLIA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row_descontadoA = mysql_fetch_assoc($qr_descontadosA)) { ?>                    
                    <tr class="linhasParticipantes">
                        <td><?php echo $row_descontadoA['nome']; ?></td>
                        <td><?php echo $row_descontadoA['nome_curso']; ?></td>
                        <td><?php echo $row_descontadoA['unidade']; ?></td> 
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($row_descontadoA['base_inss'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>">
                            <?php
                            $total_inss = $row_descontadoA['valor_inss'];
                            
                            echo ($total_inss == 0) ? "-" : number_format($total_inss, 2, ',', '.');
                            ?>
                        </td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontadoA['base_inss']) * 0.2, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontadoA['base_inss']) * $percentual_rat, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontadoA['base_inss']) * 0.058, 2, ',', '.');  ?></td>                        
                        <td class="<?php echo $esconde; ?>">-</td>                        
                        <td class="<?php echo $esconde; ?>">-</td>                        
                    </tr>
                        
                    <?php                        
                    } ?>
                </tbody>
                <?php } ?>
                
                <tfoot>
                    
                    <?php if($tipoFolha == 1){ ?>
                    <tr class='danger'>
                        <td class='text-right' colspan="3">TOTAL AUTONOMO:</td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($matrizTotalGeralA['base_inss'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($matrizTotalGeralA['inss'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeralA['base_inss']) * 0.2, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>">-</td>                         
                        <td class="<?php echo $esconde; ?>">-</td>                         
                        <td class="<?php echo $esconde; ?>">-</td>
                        <td class="<?php echo $esconde; ?>">-</td>
                    </tr>
                    <?php } ?>
                    
                    <?php
                    $inss_geral = ($matrizTotalGeralA['inss'] + $matrizTotalGeral['inss_folha'] + $matrizTotalGeral['inss_ferias'] + $matrizTotalGeral['inss_13'] + $matrizTotalGeral['inss_res']) - $matrizTotalGeral['inss_acident'];
                    $base_inss_geral = $matrizTotalGeralA['base_inss'] + $matrizTotalGeral['base_inss_folha'] + $matrizTotalGeral['base_inss_ferias'] + $matrizTotalGeral['base_inss_13'];
                    $base_inss_clt = $matrizTotalGeral['base_inss_folha'] + $matrizTotalGeral['base_inss_ferias'] + $matrizTotalGeral['base_inss_13'];
                    ?>
                    
                    <tr class='warning'>
                        <td class='text-right' colspan="3">TOTAL GERAL:</td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($base_inss_geral, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($inss_geral, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($base_inss_geral) * 0.2, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($base_inss_clt) * $percentual_rat, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['base_inss_folha'] + $matrizTotalGeral['base_inss_ferias'] + $matrizTotalGeral['base_inss_13']) * 0.058, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['sal_maternidade']), 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeral['sal_familia']), 2, ',', '.');  ?></td>
                    </tr>
                    <tr class='warning'>
                        <td class='text-right' colspan="8">VALOR DA GPS(INSS(Clt e Autonomo) + INSS(EMPRESA(Clt e Autonomo)) + INSS(RAT) + INSS(Terceiro) - SAL. MATERNIDADE):</td>
                        <td class="<?php echo $esconde; ?>" colspan="2"><?php echo number_format(($inss_geral + ($base_inss_geral * 0.2) + ($base_inss_clt * $percentual_rat) + ($base_inss_clt * 0.058) - $matrizTotalGeral['sal_maternidade']), 2, ',', '.');  ?></td>
                    </tr>
                </tfoot>
            </table>
            </div>
            
            
            
            
            
            
            
            
            
            
            
            
            <?php // if($total_descontadosA > 0) { ?>
            
<!--            <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="1table_excel">
                <thead>
                    <tr class="bg-primary">
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Unidade</th>
                        <th class="<?php echo $esconde; ?>">BASE DE INSS</th>
                        <th class="<?php echo $esconde; ?>">INSS</th>
                        <th class="<?php echo $esconde; ?>">INSS(EMPRESA)</th>
                        <th class="<?php echo $esconde; ?>">INSS(RAT)</th>
                        <th class="<?php echo $esconde; ?>">INSS(TERCEIRO)</th>
                        <th class="<?php echo $esconde; ?>">SAL. MATERNIDADE</th>
                        <th class="<?php echo $esconde; ?>">SAL. FAMÍLIA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row_descontadoA = mysql_fetch_assoc($qr_descontadosA)) { ?>                    
                    <tr class="linhasParticipantes">
                        <td><?php echo $row_descontadoA['nome']; ?></td>
                        <td><?php echo $row_descontadoA['nome_curso']; ?></td>
                        <td><?php echo $row_descontadoA['unidade']; ?></td> 
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($row_descontadoA['base_inss'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>">
                            <?php
                            $total_inss = $row_descontadoA['valor_inss'];
                            
                            echo ($total_inss == 0) ? "-" : number_format($total_inss, 2, ',', '.');
                            ?>
                        </td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontadoA['base_inss']) * 0.2, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontadoA['base_inss']) * $percentual_rat, 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($row_descontadoA['base_inss']) * 0.058, 2, ',', '.');  ?></td>                        
                        <td class="<?php echo $esconde; ?>">-</td>                        
                        <td class="<?php echo $esconde; ?>">-</td>                        
                    </tr>
                        
                    <?php                        
                    } ?>
                </tbody>
                <tfoot>
                    <tr class='danger'>
                        <td class='text-right' colspan="3">TOTAL GERAL:</td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($matrizTotalGeralA['base_inss'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format($matrizTotalGeralA['inss'], 2, ',', '.');  ?></td>
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeralA['base_inss']) * 0.2, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeralA['base_inss']) * $percentual_rat, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>"><?php echo number_format(($matrizTotalGeralA['base_inss']) * 0.058, 2, ',', '.');  ?></td>                         
                        <td class="<?php echo $esconde; ?>">-</td>
                        <td class="<?php echo $esconde; ?>">-</td>
                    </tr>
                </tfoot>
            </table>-->
            
            <?php // } ?>
            
            <?php // } else { ?>
<!--                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>-->
            <?php }
            } ?>
        
        </form>
            
            <?php include('../template/footer.php'); ?>
        </div>
    
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/tooltip.js"></script>
    <script src="../resources/js/main.js"></script>
    <script src="../js/global.js"></script>
    <script>
        $(function(){
           $("body").on("click","input[name='filtroTipo']",function(){
                var valor = $(this).val();
                if(valor == 2){
                    $(".linhasParticipantes").hide();
                }else{
                    $(".linhasParticipantes").show();
                }
           });
           
           $("#exportarExcel").click(function () {
                $("#relatorio_exp img:last-child").remove();

                var html = $("#relatorio_exp").html();

                $("#data_xls").val(html);
                $("#form1").submit();
            });
        });
    </script>
    </body>
</html>
