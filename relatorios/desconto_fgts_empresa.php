<?php
/**
 * Relatório 
 * 
 * @file                desconto_fgts_empresa.php
 * @license		F71
 * @link		http://www.f71lagos.com/intranet/?class=rescisao/processar&id_clt=9999
 * @copyright           2016 F71
 * @author		Jacques <jacques@f71.com.br>
 * @package             webRescisaoClass
 * @access              public  
 * 
 * @version: 3.0.0000 - 06/01/2017 - Jacques - Versão Inicial 
 * 
 */
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include("../classes/global.php");
require_once ("../classes/FolhaClass.php");


if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=guia_fgts.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Guia de FGTS</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();
$mov = new Folha();

$breadcrumb_config = array("nivel" => "../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form-lista", "ativo" => "Relatório de Desconto de FGTS");
$breadcrumb_pages = array("Gestão de RH" => "/intranet/rh/principalrh.php");

$ano = 2016;
$filtro = $_REQUEST['filtrar'];
$tipoFolha = array(1 => 'Normal', 2 => '13º Integral', 3 => 'Primeira Parcela', 4 => 'Segunda Parcela');
$tipoFolhaSel = (isset($_REQUEST['tipoFolha'])) ? $_REQUEST['tipoFolha'] : null;

if (isset($filtro)) {

    $projeto = $_REQUEST['projeto'];
    $mes = sprintf("%02d", $_REQUEST['mes']);
    $ano = $_REQUEST['ano'];
    $tipo = $_REQUEST['tipoFolha'];

    if ($tipo == 1) {
        $conTipo = ' AND fl.terceiro = 2 ';
        $fpTipo = "AND fp.terceiro = 2";
    } else if ($tipo == 2) {
        $conTipo = ' AND fl.terceiro = 1 AND fl.tipo_terceiro = 3';
        $fpTipo = "AND fp.terceiro = 1";
    } else if ($tipo == 3) {
        $conTipo = ' AND fl.terceiro = 1 AND fl.tipo_terceiro = 1';
        $fpTipo = "AND fp.terceiro = 1";
    } else if ($tipo == 4) {
        $conTipo = ' AND fl.terceiro = 1 AND fl.tipo_terceiro = 2';
        $fpTipo = "AND fp.terceiro = 1";
    }

    if ($projeto > '0') {
        $projeto_sql = "AND folha.id_projeto = {$projeto}";
        $projeto_ferias = "AND projeto = '{$projeto}'";
        $projeto_aux = "AND A.id_projeto = '{$projeto}'";
    } else {
        $projeto_sql = null;
    }

    $qry_fp = "SELECT * FROM rh_folha fl WHERE mes = '{$mes}' AND ano = '{$ano}' $conTipo {$projeto_ferias}";
    $sql_fp = mysql_query($qry_fp) or die(mysql_error());

    while ($res_fp = mysql_fetch_assoc($sql_fp)) {
        $idFolha = $res_fp['id_folha'];
        $array_clt_fp[] = $res_fp['ids_movimentos_estatisticas'];
    }

    $all_mov_estatisticas = $array_clt_fp ? implode(",", $array_clt_fp) : "000";

    if ($mes == 12 && $ano == 2016 && $tipo == 4) {
        
        $sqlProc = "SELECT folha.id_clt,
                    folha.nome AS nome, 
                    folha.id_folha, 
                    unidade.id_unidade,
                    unidade.unidade AS unidade, 
                    funcao.nome AS nome_curso, 
                    P.nome AS nome_projeto
                    FROM rh_folha_proc AS folha
                    INNER JOIN rh_clt AS clt ON clt.id_clt = folha.id_clt
                    INNER JOIN rh_folha AS fp ON folha.id_folha = fp.id_folha
                    INNER JOIN curso AS funcao ON funcao.id_curso = clt.id_curso
                    INNER JOIN unidade AS unidade ON unidade.id_unidade = clt.id_unidade
                    INNER JOIN projeto AS P ON P.id_projeto = folha.id_projeto
                    INNER JOIN rhempresa AS EMP ON EMP.id_regiao = clt.id_regiao
                    WHERE folha.mes = {$mes} AND folha.ano = {$ano} AND folha.status = 3 /*AND folha.status_clt NOT IN(61,63,64,66)*/
                    {$fpTipo}
                    {$projeto_sql}
                    ORDER BY unidade,nome";
                    
//        if ($_COOKIE['debug'] == 666) {
//            print_array($sqlProc);
//        }
        $queryProc = mysql_query($sqlProc);
       
        while ($rowProc = mysql_fetch_assoc($queryProc)) {
//            if ($rowProc['id_clt'] == 2181 && $_COOKIE['debug'] == 666) {
            $array_clt[$rowProc['id_unidade']][$rowProc['id_clt']] = $rowProc;
            $array_total_unidade[$rowProc['id_unidade']]['id_folha'] = $rowProc['id_folha'];
            $array_total_unidade[$rowProc['id_unidade']]['nome'] = $rowProc['unidade'];
            
            $movimentos[$rowProc['id_clt']] = $mov->getResumoPorMovimentoClt($idFolha, $rowProc['id_clt']);
            $array_clt[$rowProc['id_unidade']][$rowProc['id_clt']]['base_inss'] = $movimentos[$rowProc['id_clt']]['base'];
            
            if ($idFolha != 105) {
                $array_total_unidade[$key]['total_base_inss'] += $movimentos[$value['id_clt']]['base'];
                $array_total_unidade[$key]['total_fgts'] += $movimentos[$value['id_clt']]['fgts'];

                $total['total_base_inss'] += $movimentos[$rowProc['id_clt']]['base'];
                $total['total_fgts'] += $movimentos[$rowProc['id_clt']]['fgts'];
            }
            
            $rendimento += $movimentos[$rowProc['id_clt']]['rendimento'];
            $cod5050 += $movimentos[$rowProc['id_clt']]['5050'];
            $cod80019 += $movimentos[$rowProc['id_clt']]['80019'];
            $cod80031 += $movimentos[$rowProc['id_clt']]['80031'];
            $nIncide += $movimentos[$rowProc['id_clt']]['n_incide'];
//            }
        }
        
        if ($idFolha == 105) {
            $i = 0;
            foreach ($array_clt as $unidade => $clt) {
//                print_array($clt);
                foreach ($clt as $id => $value) {
                    if ($value['base_inss'] > 3.00) {
                        $array_clt[$value['id_unidade']][$value['id_clt']]['base_inss'] -= 2.953944315545244;
                        $array_total_unidade[$value['id_unidade']]['total_base_inss'] += $array_clt[$value['id_unidade']][$value['id_clt']]['base_inss'];
                        $array_total_unidade[$value['id_unidade']]['total_fgts'] += $array_clt[$value['id_unidade']][$value['id_clt']]['base_inss'] * 0.08;
                        
                        $total['total_base_inss'] += $array_clt[$value['id_unidade']][$value['id_clt']]['base_inss'];
                        $total['total_fgts'] += $array_clt[$value['id_unidade']][$value['id_clt']]['base_inss'] * 0.08;
                    }
                }
                
//                $i = 0;
//                if ($bInss > 3.00) {
//                    $i++;
//                }
                
                        
//                $array_total_unidade[$key]['total_base_inss'] += $movimentos[$value['id_clt']]['base'];
//                $array_total_unidade[$key]['total_fgts'] += $movimentos[$value['id_clt']]['fgts'];
//
//                $total['total_base_inss'] += $movimentos[$rowProc['id_clt']]['base'];
//                $total['total_fgts'] += $movimentos[$rowProc['id_clt']]['fgts'];
            }
        }
        
//        if ($_COOKIE['debug'] == 666) {
//            print_array([$rendimento,$cod5050,$cod80019,$cod80031,$nIncide]);
//        }
//        exit;
        
//         $verifica = "
//            SELECT 
//                folha.id_clt,
//                folha.nome AS nome, 
//                folha.id_folha, 
//                unidade.id_unidade,
//                unidade.unidade AS unidade, 
//                funcao.nome AS nome_curso, 
//                folha.base_inss, 
//                folha.inss AS inss, 
//                P.nome AS nome_projeto, 
//                EMP.aliquotaRat, 
//                EMP.aliquota_rat,
//                CAST((folha.base_inss*0.20) AS DECIMAL(12,2)) as inss_em,
//                CAST((folha.base_inss*EMP.aliquota_rat) AS DECIMAL(12,2)) as inss_rat,
//                CAST((folha.base_inss*0.058) AS DECIMAL(12,2)) as inss_ter,(folha.base_inss * 0.08) as fgts,
//                CAST(   
//                        (
//                        SELECT (A.base_inss) base_n_incide
//                        FROM rh_folha_proc A
//                            LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
//                            LEFT JOIN rh_recisao C ON (A.id_clt = C.id_clt)
//                            LEFT JOIN rh_folha fl ON A.id_folha = fl.id_folha
//                        WHERE A.status = 3 $conTipo AND A.mes = {$mes} AND A.ano = {$ano} AND MONTH(C.data_demi) = {$mes} AND YEAR(C.data_demi) = {$ano} AND C.status = 1 AND C.rescisao_complementar = 0 AND B.status IN (61,64,66) AND A.id_clt = folha.id_clt {$projeto_aux}
//                        LIMIT 1    
//                        ) AS DECIMAL(12,2)
//                    ) AS base_n_incide,
//
//                CAST(
//                        (
//                        SELECT (sallimpo) base_fgts_acerto
//                        FROM rh_folha_proc A 
//                            LEFT JOIN rh_folha fl ON A.id_folha = fl.id_folha
//                        WHERE A.mes = {$mes} AND A.ano = {$ano} $conTipo AND status_clt IN (70) AND A.status = 3 AND A.id_clt = folha.id_clt {$projeto_aux}
//                        LIMIT 1
//                        ) AS DECIMAL(12,2)
//                    ) AS base_fgts_acerto, ADT.valor_movimento AS adiantamento13, ADT2.valor_movimento AS primeira_parcela13, ADT3.valor_movimento AS ajuste
//
//            FROM rh_folha_proc AS folha
//                INNER JOIN rh_clt AS clt ON clt.id_clt = folha.id_clt
//                INNER JOIN rh_folha AS fp ON folha.id_folha = fp.id_folha
//                INNER JOIN curso AS funcao ON funcao.id_curso = clt.id_curso
//                INNER JOIN unidade AS unidade ON unidade.id_unidade = clt.id_unidade
//                INNER JOIN projeto AS P ON P.id_projeto = folha.id_projeto
//                INNER JOIN rhempresa AS EMP ON EMP.id_regiao = clt.id_regiao
//                LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80031' AND id_movimento IN ($all_mov_estatisticas)) AS ADT ON (ADT.id_clt = folha.id_clt)
//                LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '5050' AND id_movimento IN ($all_mov_estatisticas)) AS ADT2 ON (ADT2.id_clt = folha.id_clt)
//                LEFT JOIN (SELECT id_clt, valor_movimento FROM rh_movimentos_clt WHERE cod_movimento = '80019' AND id_movimento IN ($all_mov_estatisticas)) AS ADT3 ON (ADT3.id_clt = folha.id_clt)
//            WHERE folha.mes = {$mes}  
//                AND folha.ano = {$ano}
//                AND folha.status = 3 /*AND folha.status_clt NOT IN(61,63,64,66)*/
//                {$fpTipo}
//                {$projeto_sql}
//            ORDER BY unidade,nome";
//                print_array($verifica);
    } else {
        $sql_descontados = "
            SELECT 
                folha.id_clt,
                folha.nome AS nome, 
                folha.id_folha, 
                unidade.id_unidade,
                unidade.unidade AS unidade, 
                funcao.nome AS nome_curso, 
                folha.base_inss, 
                folha.inss AS inss, 
                P.nome AS nome_projeto, 
                EMP.aliquotaRat, 
                EMP.aliquota_rat,
                CAST((folha.base_inss*0.20) AS DECIMAL(12,2)) as inss_em,
                CAST((folha.base_inss*EMP.aliquota_rat) AS DECIMAL(12,2)) as inss_rat,
                CAST((folha.base_inss*0.058) AS DECIMAL(12,2)) as inss_ter,(folha.base_inss * 0.08) as fgts,
                CAST(   
                        (
                        SELECT (A.base_inss) base_n_incide
                        FROM rh_folha_proc A
                            LEFT JOIN rh_clt B ON (A.id_clt = B.id_clt)
                            LEFT JOIN rh_recisao C ON (A.id_clt = C.id_clt)
                            LEFT JOIN rh_folha fl ON A.id_folha = fl.id_folha
                        WHERE A.status = 3 $conTipo AND A.mes = {$mes} AND A.ano = {$ano} AND MONTH(C.data_demi) = {$mes} AND YEAR(C.data_demi) = {$ano} AND C.status = 1 AND C.rescisao_complementar = 0 AND B.status IN (61,64,66) AND A.id_clt = folha.id_clt {$projeto_aux}
                        LIMIT 1    
                        ) AS DECIMAL(12,2)
                    ) AS base_n_incide,

                CAST(
                        (
                        SELECT (sallimpo) base_fgts_acerto
                        FROM rh_folha_proc A 
                            LEFT JOIN rh_folha fl ON A.id_folha = fl.id_folha
                        WHERE A.mes = {$mes} AND A.ano = {$ano} $conTipo AND status_clt IN (70) AND A.status = 3 AND A.id_clt = folha.id_clt {$projeto_aux}
                        LIMIT 1
                        ) AS DECIMAL(12,2)
                    ) AS base_fgts_acerto 

            FROM rh_folha_proc AS folha
                INNER JOIN rh_clt AS clt ON clt.id_clt = folha.id_clt
                INNER JOIN rh_folha AS fp ON folha.id_folha = fp.id_folha
                INNER JOIN curso AS funcao ON funcao.id_curso = clt.id_curso
                INNER JOIN unidade AS unidade ON unidade.id_unidade = clt.id_unidade
                INNER JOIN projeto AS P ON P.id_projeto = folha.id_projeto
                INNER JOIN rhempresa AS EMP ON EMP.id_regiao = clt.id_regiao
            WHERE folha.mes = {$mes}  
                AND folha.ano = {$ano}
                AND folha.status = 3 /*AND folha.status_clt NOT IN(61,63,64,66)*/
                {$fpTipo}
                {$projeto_sql}
            ORDER BY unidade,nome";

        if ($_COOKIE['debug'] == 666) {
            echo $all_mov_estatisticas;
            echo $sql_descontados;
        }

        $rs = mysql_query($sql_descontados) or die(mysql_error());

        $total_descontados = mysql_num_rows($rs);

        while ($row = mysql_fetch_assoc($rs)) {

//        if ($row['id_clt'] == 4655 || $row['id_clt'] == 4264 || $row['id_clt'] == 3223) {
//            $row['base_n_incide'] = 0;
//        }
            $array_clt[$row['id_unidade']][$row['id_clt']] = $row;

            $array_total_unidade[$row['id_unidade']]['id_folha'] = $row['id_folha'];
            $array_total_unidade[$row['id_unidade']]['nome'] = $row['unidade'];

            $total['total_base_inss'] += $row['base_inss'];
            $total['total_fgts'] += $row['base_inss'] * 0.08;
            $total['total_base_n_incide'] += $row['base_n_incide'];
            $total['total_adiantamento13'] += $row['adiantamento13'];
            $total['total_primeira_parcela13'] += $row['primeira_parcela13'];
            $total['total_ajuste'] += $row['ajuste'];
            $total['total_base_fgts_acerto'] += $row['base_fgts_acerto'];
            $array_total_unidade[$row['id_unidade']]['total_base_inss'] += $row['base_inss'];
            $array_total_unidade[$row['id_unidade']]['total_fgts'] += $row['base_inss'] * 0.08;
            $array_total_unidade[$row['id_unidade']]['total_base_n_incide'] += $row['base_n_incide'];
            $array_total_unidade[$row['id_unidade']]['total_base_fgts_acerto'] += $row['base_fgts_acerto'];
        }

        if ($_COOKIE['debug'] == 666) {
//            print_array($array_total_unidade);
        }

        $unidade = "";
        $unidade_nome = "";
        $toInss = 0;
        $toInss_emp = 0;
        $toInss_rat = 0;
    }
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <title>Relatório de Desconto de FGTS</title>
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
    </head>
    <body>
<?php include("../template/navbar_default.php"); ?>

        <div class="<?= ($container_full) ? 'container-full' : 'container' ?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Desconto de FGTS</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">

        <?php if (isset($_SESSION['regiao'])) { ?>                
                    <!--resposta de algum metodo realizado-->
                    <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                        <button type="button" class="close" data-dismiss="alert">Ã—</button>
                        <p><?php
            echo $_SESSION['MESSAGE'];
            session_destroy();
            ?></p>
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
<?php echo montaSelect(AnosArray(null, null), $ano, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Folha: </label>
                            <div class="col-lg-4">
<?php echo montaSelect($tipoFolha, $tipoFolhaSel, "id='tipoFolha' name='tipoFolha' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"></label>
                            <div class="col-lg-2">
                                <div class="input-group">
                                    <label class="input-group-addon pointer" for="filtroTipo">
                                        <input type="radio" name="filtroTipo" id="filtroTipo" value="1" checked="checked" />
                                    </label>
                                    <label class="form-control pointer" for="filtroTipo">Participantes</label>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="input-group">
                                    <label class="input-group-addon pointer" for="filtroTipo">
                                        <input type="radio" name="filtroTipo" id="filtroTipo" value="2" /> 
                                    </label>
                                    <label class="form-control pointer" for="filtroTipo"> Unidade</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php if (!empty($total_descontados) && (isset($_POST['filtrar']))) { ?>
                            
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de FGTS" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger"><span class="fa fa-file-pdf-o"></span> Gerar PDF</button>
                        <?php } ?>
                        <button type="button" value="Exportar" class="btn btn-success" id="exportarExcel"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                        <input type="hidden" id="data_xls" name="data_xls" value="">
                        <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Filtrar</button>
                    </div>
                </div>

                        <?php
                        if ($filtro) {
                            if (count($array_clt) > 0) {
                                ?>
                        <div id="relatorio_exp">
                        <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                            <thead>                    
                                <tr>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>Unidade</th>
                                    <th>Base FGTS</th>
                                    <th>FGTS</th>                        
                                </tr>
                            </thead>
                            <tbody>
        <?php
        foreach ($array_clt as $key_id_unidade => $value_total_unidade) {

            foreach ($value_total_unidade as $key_id_clt => $value) {

                if ($unidade != $value['id_unidade']) {

                    //CABEÇALHO, PRINTADO LINHA TOTALIZADOR
                    if (!empty($unidade)) {
                        
                    }

                    $unidade = $value['id_unidade'];
                    $unidade_nome = $value['unidade'];
                }
                ?>
                                        <tr class="linhasParticipantes">
                                            <td><?php echo $value['nome']; ?></td>
                                            <td><?php echo $value['nome_curso']; ?></td>
                                            <td><?php echo $unidade_nome; ?></td>
                                            <td class='text-right'><?php echo number_format($value['base_inss'] - ($value['base_n_incide'] - $value['adiantamento13'] - $value['primeira_parcela13'] - $value['ajuste']), 2, ',', '.'); ?></td>
                                            <td class='text-right'><?php echo number_format(($value['base_inss'] - ($value['base_n_incide'] - $value['adiantamento13'] - $value['primeira_parcela13'] - $value['ajuste'])) * 0.08, 2, ',', '.'); ?></td>
                                        </tr>
                <?php
            }
            ?>
                                    <tr class='warning'><td class='text-right' colspan='3'> <?= $array_total_unidade[$key_id_unidade]['nome'] ?>: </td> 
                                        <td class='text-right'><?= number_format($array_total_unidade[$key_id_unidade]['total_base_inss'] - ($array_total_unidade[$key_id_unidade]['total_base_n_incide'] - $array_total_unidade[$key_id_unidade]['total_adiantamento13'] - $array_total_unidade[$key_id_unidade]['total_primeira_parcela13'] - $array_total_unidade[$key_id_unidade]['total_ajuste']), 2, ',', '.') ?></td> 
                                        <td class='text-right'><?= number_format(($array_total_unidade[$key_id_unidade]['total_base_inss'] - ($array_total_unidade[$key_id_unidade]['total_base_n_incide'] - $array_total_unidade[$key_id_unidade]['total_adiantamento13'] - $array_total_unidade[$key_id_unidade]['total_primeira_parcela13'] - $array_total_unidade[$key_id_unidade]['total_ajuste'])) * 0.08, 2, ',', '.') ?></td>
                                    <?php
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class='danger'>
                                    <td class='text-right' colspan='3'>Total Geral:</td>
                                    <td class='text-right'><?php echo number_format(($total['total_base_inss'] - $total['total_base_n_incide'] - $total['total_adiantamento13'] - $total['total_primeira_parcela13'] - $total['total_ajuste']) + $total['base_fgts_acerto'], 2, ',', '.'); ?></td>
                                    <td class='text-right'><?php echo number_format((($total['total_base_inss'] - $total['total_base_n_incide'] - $total['total_adiantamento13'] - $total['total_primeira_parcela13'] - $total['total_ajuste']) + $total['base_fgts_acerto']) * 0.08, 2, ',', '.'); ?></td>
                                </tr>
                            </tfoot>

                        </table>
                        </div>
    <?php } else { ?>
                        <div class="alert alert-danger top30">                    
                            Nenhum registro encontrado
                        </div>
                    <?php
                    }
                }
                ?>

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
                                $(function () {
                                    $("body").on("click", "input[name='filtroTipo']", function () {
                                        var valor = $(this).val();
                                        if (valor == 2) {
                                            $(".linhasParticipantes").hide();
                                        } else {
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
