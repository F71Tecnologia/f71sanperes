<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/OrcamentoClass.php");

$acoes = new Acoes();
$usuario = carregaUsuario();
$objOrcamento = new OrcamentoClass();
$container_full = true;
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

/**
 * RECUPERAÇÃO DOS VALORES SELECIONADOS
 */
$id_projeto = ($_REQUEST['id_projeto']) ? $_REQUEST['id_projeto'] : null;
$mes = ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
$ano = ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');

/**
 * MONTA SELECT DOS PROJETOS
 */
$sqlProjetos = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_master = {$usuario['id_master']} ORDER BY nome") or die("ERRO AO SELECIONAR O PROJETO:" . mysql_error());
//$arrayProjetos = array("" => "-- TODOS --");
while ($rowProjetos = mysql_fetch_assoc($sqlProjetos)) {
    $arrayProjetos[$rowProjetos['id_projeto']] = $rowProjetos['id_projeto'] . " - " . $rowProjetos['nome'];
}

/**
 * MONTA SELECT DAS UNIDADES
 */

$auxProjeto = ($_REQUEST['id_projeto']) ? "WHERE campo1 = {$_REQUEST['id_projeto']}" : null;
$sqlUnidades = mysql_query("SELECT id_unidade, campo1, unidade FROM unidade $auxProjeto ORDER BY unidade");
$arrayUnidades = array("" => "-- TODAS --");
while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
    $arrayUnidadesProjeto[$rowUnidades['campo1']][] = $rowUnidades['id_unidade'];
    if($_REQUEST['method'] == 'unidades'){
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
    } else {
        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . $rowUnidades['unidade'];
    }
}
if($_REQUEST['method'] == 'unidades'){
    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], 'class="form-control" id="id_unidade" name="id_unidade"');
    exit;
}

if(isset($_REQUEST['filtrar'])){
    echo $id_projeto = $_REQUEST['id_projeto'];
    echo $id_unidade = $_REQUEST['id_unidade'];    
    include("../conn.php");
    include("../wfunction.php");
    if(!include_once(ROOT_CLASS.'RhClass.php')) die ('Não foi possível incluir '.ROOT_CLASS.'RhClass.php'); 
    if(!empty($id_unidade)){
        $rh = new RhClass();
        $rh->AddClassExt('Clt');
        $rh->Clt->setIdRegiao($id_projeto);
        $rh->Clt->setIdProjeto($id_projeto);
        $rh->Clt->setIdUnidade($id_unidade);
        $dias_ferias_vencidas = $rh->Clt->select()->db->getCollection('situacao_ferias,id_clt',"situacao_ferias=(dias_ferias_vencidas > 0 ? 0 : 1)");
        print_array($dias_ferias_vencidas);
    }else{
        $rh = new RhClass();
        $rh->AddClassExt('Clt');
        $rh->Clt->setIdRegiao($id_projeto);
        $rh->Clt->setIdProjeto($id_projeto);
        $dias_ferias_vencidas = $rh->Clt->select()->db->getCollection('situacao_ferias,id_clt',"situacao_ferias=(dias_ferias_vencidas > 0 ? 0 : 1)");
        print_array($dias_ferias_vencidas);
    }
}

/**
 * PARAMETROS DE CONFIG DA PAGINA
 */
$nome_pagina = "RELATÓRIO DE FÉRIAS VENCIDAS";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php", "Férias"=>"/intranet/rh_novaintra/ferias");

$borderMes = ' style="border: 2px solid #00F;" '; 
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/main.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/dropzone/dropzone.css" rel="stylesheet" media="all">
        <link href="http://<?=$_SERVER['SERVER_NAME']?>/intranet/resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro  - <small><?= $nome_pagina ?></small></h2></div>
                    <?php if($_COOKIE['debug'] == 666 && ((count($arrayGetOrcamento['arraySubGrupos']) + count($arrayGetOrcamento['arrayEntradas'])) > 0)){ ?>
                    <?= $objOrcamento->getAvisos($arrayUnidadesProjeto[$_REQUEST['id_projeto']], $_REQUEST['id_unidade']) ?>
                    <?= $objOrcamento->getAvisosSaldoAcumulado($arrayGetOrcamento['arrayTotalSubGrupos'], $arrayGetOrcamento['arraySaldoAcumuladoDespesaTotal']) ?>
                    <?php } ?>
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default hidden-print">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <div class="text-bold">Projeto:</div>
                                    <div class="" id="div_projeto">
                                        <?= montaSelect($arrayProjetos, $_REQUEST['id_projeto'], 'class="form-control" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Unidade:</div>
                                    <div class="" id="div_unidade">
                                        <?= montaSelect($arrayUnidades, $_REQUEST['id_unidade'], 'class="form-control" id="id_unidade" name="id_unidade"') ?>
                                    </div>
                                </div>
                                <!--div class="col-sm-4">
                                    <div class="text-bold">Competência:</div>
                                    <div class="input-group" id="">
                                        <?= montaSelect(mesesArray(), $mes, 'class="form-control" id="mes" name="mes"') ?>
                                        <div class="input-group-addon">/</div>
                                        <?= montaSelect(anosArray(2016), $ano, 'class="form-control" id="ano" name="ano"') ?>
                                    </div>
                                </div-->
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button name="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> FILTRAR</button>
                        </div>
                    </div>
                    </form>
                    <hr>
                    <?php if(!empty($dias_ferias_vencidas)) { ?>
                    <button type="button" id="tableToExcelWithCss" class="btn btn-success margin_b10 pull-right hidden-print"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
                    <table id="relatorio" class="table table-bordered table-hover table-condensed valign-middle text-sm">
                        <tr>
                            <th class="text-center info">UNIDADE</th>
                            <th class="text-center info">NOME</th>
                            <th class="text-center info">FUNÇÃO</th>
                            <th class="text-center info">SALÁRIO</th>
                            <th class="text-center info">AQUIS. INI.</th>
                            <th class="text-center info">AQUIS. FIM.</th>
                            <th class="text-center info">INICIO</th>
                            <th class="text-center info">FIM</th>
                            <th class="text-center info">DIAS</th>
                            <th class="text-center info">REMUNERAÇÃO</th>
                            <th class="text-center info">DESCONTO</th>
                            <th class="text-center info">LÍQUIDO</th>
                            <th class="text-center info">&nbsp;</th>
                        </tr>
                        <?php foreach ($arrayFerias as $unidade => $clts) { ?>
                        <!--tr><td colspan="13" class="active"><?= $unidade ?></td></tr-->
                        <?php foreach ($clts as $id_clt => $clt) { $c = 0; ?>
                            <tr>
                                <td rowspan="<?= count($clt['periodos']) ?>"><?= $unidade ?></td>
                                <td rowspan="<?= count($clt['periodos']) ?>"><?= $dias_ferias_vencidas['nome'] ?></td>
                                <td rowspan="<?= count($clt['periodos']) ?>"><?= $clt['funcao'] ?></td>
                                <td class="text-right" rowspan="<?= count($clt['periodos']) ?>"><?= number_format($clt['valor'],2,',','.') ?></td>
                                <?php foreach ($clt['periodos'] as $id_ferias => $ferias) { $c++; ?>
                                <?php if($c > 1) { ?></tr><tr><?php } ?>
                                <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_aquisitivo_ini']))) ?></td>
                                <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_aquisitivo_fim']))) ?></td>
                                <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_ini']))) ?></td>
                                <td class="text-center"><?= implode('/', array_reverse(explode('-',$ferias['data_fim']))) ?></td>
                                <td class="text-center"><?= $ferias['dias_ferias'] ?></td>
                                <td class="text-right"><?= number_format($ferias['total_remuneracoes'],2,',','.') ?></td>
                                <td class="text-right"><?= number_format($ferias['total_descontos'],2,',','.') ?></td>
                                <td class="text-right"><?= number_format($ferias['total_liquido'],2,',','.') ?></td>
                                <td class="text-center"><a class="btn btn-xs btn-danger" href="../../?class=ferias/processar&method=gerarPdf&id_ferias=<?= $id_ferias ?>&value=pdf" target="_blank"><i class="fa fa-file-pdf-o"></a></td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <!--tr><td colspan="13">&nbsp;</td></tr-->
                        <?php } ?>
                        
                    </table>
                    <?php } else { ?>
                        <div class="alert alert-info text-bold">Nenhuma informação encontrada!</div>
                    <?php } ?>
                </div>
            </div>
            <?php include('../../template/footer.php'); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
        $(function(){
            $('body').on('change', '#id_projeto', function(){
                $.post('', {method: 'unidades', id_projeto: $(this).val()}, function(result){
                    $('#div_unidade').html(result);
                });
            });
            
            
            $('#tableToExcelWithCss').click(function(){
                $('th, td, tr').each(function($i,$v){
                    $($v).css('background-color', $($v).css('background-color'));
                    $($v).css('color', $($v).css('color'));
                    $($v).css('font', $($v).css('font'));
                    $($v).css('text-align', $($v).css('text-align'));
                    $($v).css('vertical-align', $($v).css('vertical-align'));
                });
                tableToExcel('relatorio', '', true);
            });
        });
        </script>  
    </body>
</html>