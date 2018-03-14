<?php
session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/global.php");

$container_full = true;

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$saida = new Saida();
$global = new GlobalClass();

if(isset($_REQUEST['filtrar'])){
    $id_projeto = $_REQUEST['projeto'];
    $nome_projeto = projetosId($_REQUEST['projeto']);
    $master_projeto = masterId($nome_projeto['id_master']);
    $mesShow = mesesArray($_REQUEST['mes']) . "/" .$_REQUEST['ano'];    
    $filtro = true;
    
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    $banco = $_REQUEST['banco'];
    
    $whereData[] = "MONTH(data_vencimento) = '{$mes}' AND YEAR(data_vencimento) = '{$ano}'";
    $whereData[] = ($id_projeto) ? "id_projeto = '{$id_projeto}'" : null;
    $whereData[] = ($banco) ? "id_banco = '{$banco}'" : null;
    $whereData[] = "status = 2";
    $whereData = array_filter($whereData);
    
    $completeWhere = implode(' AND ', $whereData);
    
    $result_det = $saida->getDetalhado($completeWhere);
    $qr = $result_det." GROUP BY C.id_entradasaida ORDER BY C.cod";
    $result = mysql_query($qr) or die(mysql_error());
    $total_detalhado = mysql_num_rows($result);
    
    $qr_totais = $result_det." GROUP BY A.id_grupo";
    $result_totais = mysql_query($qr_totais);
    $totais = array();
    while ($row_total = mysql_fetch_assoc($result_totais)) {
        $totais[$row_total['id_grupo']] = $row_total['total'];
    }
    
    $qr_subtotais = $result_det." GROUP BY B.id";
    $result_subtotais = mysql_query($qr_subtotais);
    $subtotais = array();
    while ($row_subtotal = mysql_fetch_assoc($result_subtotais)) {
        $subtotais[$row_subtotal['idsub']] = $row_subtotal['total'];
    }
    
    $qt_totalfinal = "SELECT SUM(CAST(
            REPLACE(total, ',', '.') AS DECIMAL(13,2))) AS total
            FROM ({$result_det}) as q";
    
    $result_totalfinal = mysql_query($qt_totalfinal);
    $row_totalfinal = mysql_fetch_assoc($result_totalfinal);
    
}

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
if(isset($_REQUEST['projeto'])){
    $projetoR = $_REQUEST['projeto'];
    $bancoR = $_REQUEST['banco'];
    $mesR = $_REQUEST['mes'];
    $anoR = $_REQUEST['ano'];
}

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Relatório Detalhado");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório Detalhado</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <style>
            @media print {
                .show_print {
                    display: table-row!important;
                }
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - Relatório Detalhado</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Relatório Detalhado</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($global->carregaProjetosByMaster($usuario['id_master'], ['' => 'Todos']), $projetoR, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                            <label for="select" class="col-lg-1 control-label">Banco</label>
                            <div class="col-lg-4">
                                <?php echo montaSelect($global->carregaBancosByMaster($usuario['id_master'], array("" => "Todos os bancos"), null), $bancoR, "id='banco' name='banco' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Mês</label>
                            <div class="col-lg-4">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php echo montaSelect(mesesArray(),($mesR) ? $mesR : date('m'), "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php echo montaSelect(AnosArray(2016,null),($anoR) ? $anoR : date('Y'), "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <input type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary" />
                    </div>
                </div>
            
            <?php
            if ($filtro) {
                if ($total_detalhado > 0) {
            ?>
            
            <input type="hidden" name="where" id="where" value="<?php echo $completeWhere ?>" />
            <input type="hidden" name="vars" id="vars" value="<?php echo "{$mes}_{$ano}_{$banco}" ?>" />
            
            <div class="alert alert-dismissable alert-warning">                
                <strong>Unidade Gerenciada: </strong> <?php echo $nome_projeto['nome']; ?>
                <strong class="borda_titulo">O responsável: </strong> <?php echo $master_projeto['nome']; ?>
                <strong class="borda_titulo">Mês Referente: </strong> <?php echo $mesShow; ?>
            </div>
            
            <table class='table table-bordered table-hover table-condensed text-sm valign-middle'>
                <thead>                    
                    <tr>
                        <th colspan="3" class="text-center fundo_titulo">Despesas realizadas</th>
                    </tr>
                    <tr class="bg-primary">
                        <th>Código</th>
                        <th>Despesa</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php    
                    $antesGrupo = "";
                    $antesSubGrupo = "";
                    $i = 0;
                    while ($row = mysql_fetch_assoc($result)) {
                        
                        $grafico[$row['id_grupo']]['nome'] = $row['nome_grupo'];
                        $grafico[$row['id_grupo']]['total'] = number_format($totais[$row['id_grupo']], 2, '.', '');
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['nome'] = $row['subgrupo'];
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['total'] = number_format($subtotais[$row['idsub']], 2, '.', '');
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['dados'][$row['cod']]['nome'] = $row['nome'];
                        $grafico[$row['id_grupo']]['dados'][$row['id_subgrupo']]['dados'][$row['cod']]['total'] = number_format($row['total'], 2, '.', '');
                        
                        if ($antesGrupo != $row['id_grupo']) {
                            $antesGrupo = $row['id_grupo'];
                    ?>
                    
                    <tr class='active'>
                        <td>0<?php echo str_replace("0", "", $row['id_grupo']) ?></td>
                        <td><?php echo $row['nome_grupo']; ?></td>
                        <td><?php echo formataMoeda($totais[$row['id_grupo']]); ?></td>
                    <tr>
                    
                    <?php
                        }
                        if ($antesSubGrupo != $row['id_subgrupo']) {
                            $antesSubGrupo = $row['id_subgrupo'];
                    ?>
                    
                    <tr class='active'>
                        <td><span class='artificio1'></span><?php echo $row['id_subgrupo']; ?></td>
                        <td><?php echo $row['subgrupo']; ?></td>
                        <td class='txright'><?php echo formataMoeda($subtotais[$row['idsub']]); ?></td>
                    <tr>
                    
                    <?php } ?>
                    
                    <tr>
                        
                        <?php if($row['total'] == ""){ ?>
                        <td><span class='artificio2'></span><?php echo $row['cod']; ?></td>
                        <?php }else{ ?>
                        <td>
                            <span class='artificio2'></span>
                            <a href="javascript:;" class="clk" data-key="<?php echo str_replace(".", "", $row['cod']); ?>"><?php echo $row['cod']; ?></a>
                        </td>
                        <?php } ?>
                        
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo formataMoeda($row['total']); ?></td>
                    </tr>
                    
                    <?php
                    if($row['total'] != ""){
                        $res = $saida->getDespesas($row['cod'], $completeWhere);
                        $tot = mysql_num_rows($res);
                    ?>
                    <tr id="tbl<?php echo $i++; ?>" class="occ <?php echo str_replace(".", "", $row['cod']); ?> show_print">
                        <td colspan="3">
                            <table class='table table-bordered'>
                                <tbody>
                                    <?php
                                    while ($rowd = mysql_fetch_assoc($res)) {
                                        
                                        $comprovante = "-";
                                        if($rowd['comprovante'] == 2){
                                            $comprovante = "<a class='btn btn-xs btn-info btn-outline arq' data-key='".str_replace(".", "", $rowd['id_saida'])."'><span class='fa fa-paperclip'></span></a>";
                                        }
                                        
                                        $especifica = ($rowd['especifica'] == "") ? "-" : $rowd['especifica'];
                                        
                                        if($rowd['estorno'] == 2){
                                            $valor = "R$".number_format($rowd['cvalor'],2,",",".")." - ".number_format($rowd['valor_estorno_parcial'],2,",",".");
                                        }else{
                                            $valor = "R$".number_format($rowd['cvalor'],2,",",".");
                                        }
                                    ?>
                                    <tr class="active">
                                        <td><?php echo $rowd['id_saida']; ?></td>
                                        <td><?php echo $rowd['nome']; ?></td>
                                        <td><?php echo $especifica; ?></td>
                                        <td><?php echo $valor; ?></td>
                                        <td><?php echo $rowd['dataBr']; ?></td>
                                        <td class="text-center hidden-print"><?php echo $comprovante; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php }} ?>
                </tbody>
                
                <tfoot>
                    <tr class="info">
                        <td></td>
                        <td></td>
                        <td><strong>Total: </strong><?php echo formataMoeda($row_totalfinal['total']); ?></td>
                    </tr>
                </tfoot>
            </table>
            <div id="highcharts"></div>
            
            <?php } else { ?>
                <div class="alert alert-danger top30">                    
                    Nenhum registro encontrado
                </div>
            <?php }
            } ?>
        
        </form>
            
            <?php include('../../template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/highcharts/highcharts.js"></script>
        <script src="../../resources/js/highcharts/highcharts.drilldown.js"></script>
        <script src="../../resources/js/highcharts/highcharts.exporting.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/detalhado.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine({promptPosition : "topRight"});
            
                $('#highcharts').highcharts({
                    lang: { drillUpText: '<< Voltar para {series.name}' },
                    chart: { type: 'column' },
                    title: { text: 'Despesas realizadas' },
                    //subtitle: { text: 'Click the columns to view versions. Source: <a href="http://netmarketshare.com">netmarketshare.com</a>.' },
                    xAxis: { type: 'category' },
                    yAxis: { title: { text: 'R$' } },
                    legend: { enabled: false },
                    plotOptions: {
                        series: {
                            borderWidth: 0,
                            dataLabels: {
                                enabled: true,
                                format: '{point.yText}'
                            }
                        }
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>R$ {point.yText}</b><br/>'
                    },
                    series: [{
                        name: "Todos",
                        colorByPoint: true,
                        data: [
                            <?php foreach ($grafico as $idGrupo => $grupoValue) { ?>
                                {
                                    name: "<?=$grupoValue['nome']?>",
                                    y: <?=$grupoValue['total']?>,
                                    yText: "<?=number_format($grupoValue['total'], 2,',','.')?>",
                                    drilldown: "<?=$idGrupo?>"
                                },
                            <?php } ?>
                        ]
                    }],
                    drilldown: {
                        drillUpButton: { relativeTo: 'spacingBox', position: { y: -4, x: -50 } },
                        series: [
                            <?php foreach ($grafico as $idGrupo => $grupoValue) { ?>
                                {
                                    name: "<?=$grupoValue['nome']?>",
                                    id: "<?=$idGrupo?>",
                                    data: [
                                        <?php foreach ($grupoValue['dados'] as $idSubGrupo => $subGrupoValue) { ?>
                                            { 
                                                name: "<?=$subGrupoValue['nome']?>", 
                                                y: <?=$subGrupoValue['total']?>, 
                                                yText: "<?=number_format($subGrupoValue['total'], 2,',','.')?>",
                                                drilldown: "<?=$idSubGrupo?>"
                                            },
                                        <?php } ?>
                                    ]
                                },
                            <?php } ?>
                            <?php foreach ($grafico as $idGrupo => $grupoValue) { ?>
                                <?php foreach ($grupoValue['dados'] as $idSubGrupo => $subGrupoValue) { ?>
                                    {
                                        name: "<?=$subGrupoValue['nome']?>",
                                        id: "<?=$idSubGrupo?>",
                                        data: [
                                            <?php foreach ($subGrupoValue['dados'] as $tipoCod => $tipoValue) { ?>
                                                { 
                                                    name: "<?=$tipoValue['nome']?>", 
                                                    y: <?=$tipoValue['total']?>,
                                                    yText: "<?=number_format($tipoValue['total'], 2,',','.')?>",
                                                },
                                            <?php } ?>
                                        ]
                                    },
                                <?php } ?>
                            <?php } ?>
                        ]
                    }
                });
            });
        </script>
    </body>
</html>
