<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/OrcamentoClass.php");
include("../../classes/global.php");

$acoes = new Acoes();
$usuario = carregaUsuario();
$objOrcamento = new OrcamentoClass();
$objGlobal = new GlobalClass();
$container_full = true;
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//$arrayMeses = array(3,4,5,6,7,8,9,10,11,12,1,2);
$arrayMeses = array(1,2,3,4,5,6,7,8,9,10,11,12);

/**
 * RECUPERAÇÃO DOS VALORES SELECIONADOS
 */
$id_projeto = ($_REQUEST['id_projeto']) ? $_REQUEST['id_projeto'] : null;
$mes = ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
$ano = ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');

$check['simples'] = $_REQUEST['check_simples'];

$check['previsto'] = (!empty($_REQUEST['check']['previsto']) || !isset($_REQUEST['check']));
$check['realizado'] = (!empty($_REQUEST['check']['realizado']) || !isset($_REQUEST['check']));
$check['provisionado'] = (!empty($_REQUEST['check']['provisionado']));

/**
 * MONTA SELECT DOS PROJETOS
 */
$sqlProjetos = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_master = '{$usuario['id_master']}' ORDER BY nome") or die("ERRO AO SELECIONAR O PROJETO:" . mysql_error());
$arrayProjetos = array("" => "-- TODOS --");
while ($rowProjetos = mysql_fetch_assoc($sqlProjetos)) {
    $arrayProjetos[$rowProjetos['id_projeto']] = $rowProjetos['id_projeto'] . " - " . $rowProjetos['nome'];
}

/**
 * MONTA SELECT DAS UNIDADES
 */

//$auxProjeto = ($_REQUEST['id_projeto']) ? "WHERE campo1 = {$_REQUEST['id_projeto']}" : null;
//$sqlUnidades = mysql_query("SELECT id_unidade, campo1, unidade FROM unidade $auxProjeto ORDER BY unidade");
//$arrayUnidades = array("" => "-- TODAS --");
//while ($rowUnidades = mysql_fetch_assoc($sqlUnidades)) {
//    $arrayUnidadesProjeto[$rowUnidades['campo1']][] = $rowUnidades['id_unidade'];
//    if($_REQUEST['method'] == 'unidades'){
//        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . utf8_encode($rowUnidades['unidade']);
//    } else {
//        $arrayUnidades[$rowUnidades['id_unidade']] = $rowUnidades['id_unidade'] . " - " . $rowUnidades['unidade'];
//    }
//}
//if($_REQUEST['method'] == 'unidades'){
//    echo montaSelect($arrayUnidades, $_REQUEST['id_unidade'], 'class="form-control" id="id_unidade" name="id_unidade"');
//    exit;
//}

/**
 * MONTA ARRAY DAS UNIDADES DOS PROJETOS
 */
foreach ($arrayUnidadesProjeto as $key => $value) {
    $arrayUnidadesProjeto[$key] = implode(',',$value);
}
//print_array($_REQUEST['id_projeto']);

if(isset($_REQUEST['filtrar'])){
    $arrayGetOrcamento = $objOrcamento->getOrcamento1($ano, $mes, $_REQUEST['tipo'], $_REQUEST['id_banco'], $check, $_REQUEST['id_projeto']);
//    print_array($arrayGetOrcamento['arrayOrcamento']);
}

if(!$_REQUEST['tipo']){
    $arrayMeses = array($mes);
}

/**
 * PARAMETROS DE CONFIG DA PAGINA
 */
$nome_pagina = "RELATÓRIO PREVISTO E REALIZADO";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$nome_pagina");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"index.php");

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
<!--                                <div class="col-sm-6">
                                    <div class="text-bold">Projeto:</div>
                                    <div class="" id="div_projeto">
                                        <?= montaSelect($arrayProjetos, $_REQUEST['id_projeto'], 'class="form-control" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>-->
                                <div class="col-sm-3">
                                    <div class="text-bold">Banco:</div>
                                    <div class="" id="div_banco">
                                        <?= montaSelect($objGlobal->carregaBancosByMaster($usuario['id_master']), $_REQUEST['id_banco'], 'class="form-control" id="id_banco" name="id_banco"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">Competência:</div>
                                    <div class="input-group" id="">
                                        <?= montaSelect(mesesArray(), $mes, 'class="form-control" id="mes" name="mes"') ?>
                                        <div class="input-group-addon">/</div>
                                        <?= montaSelect(anosArray(2016), $ano, 'class="form-control" id="ano" name="ano"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">&nbsp;</div>
                                    <div class="input-group" id="">
                                        <label class="input-group-addon pointer" for="radio_mes"><input type="radio" id="radio_mes" name="tipo" value="0" <?= (empty($_REQUEST['tipo'])) ? ' CHECKED ' : null ?>></label>
                                        <label class="form-control pointer" for="radio_mes">Mensal</label>
                                        <label class="input-group-addon pointer" for="radio_ano"><input type="radio" id="radio_ano" name="tipo" value="1" <?= (!empty($_REQUEST['tipo']) || !isset($_REQUEST['tipo'])) ? ' CHECKED ' : null ?>></label>
                                        <label class="form-control pointer" for="radio_ano">Anual</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">&nbsp;</div>
                                    <div class="input-group" id="">
                                        <label class="input-group-addon pointer" for="check_simples"><input type="checkbox" id="check_simples" name="check_simples" value="1" <?= ($check['simples']) ? ' CHECKED ' : null ?>></label>
                                        <label class="form-control pointer" for="check_simples">Simplificado</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <div class="text-bold">&nbsp;</div>
                                    <div class="input-group" id="">
                                        <label class="input-group-addon pointer" for="check_previsto"><input type="checkbox" id="check_previsto" name="check[previsto]" value="1" <?= ($check['previsto']) ? ' CHECKED ' : null ?>></label>
                                        <label class="form-control pointer" for="check_previsto">PREVISTO</label>
                                        <label class="input-group-addon pointer" for="check_realizado"><input type="checkbox" id="check_realizado" name="check[realizado]" value="1" <?= ($check['realizado']) ? ' CHECKED ' : null ?>></label>
                                        <label class="form-control pointer" for="check_realizado">REALIZADO</label>
<!--                                        <label class="input-group-addon pointer" for="check_provisionado"><input type="checkbox" id="check_provisionado" name="check[provisionado]" value="1" <?= ($check['provisionado']) ? ' CHECKED ' : null ?>></label>
                                        <label class="form-control pointer" for="check_provisionado">PROVISIONADO</label>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button name="filtrar" class="btn btn-primary"><i class="fa fa-filter"></i> FILTRAR</button>
                        </div>
                    </div>
                    </form>
                    <hr>
                    <?php if((count($arrayGetOrcamento['arraySubGrupos']) + count($arrayGetOrcamento['arrayEntradas'])) > 0) { ?>
                    <button type="button" form="formPdf" name="pdf" data-title="Titulo" data-id="relatorio" id="pdf" data-orientacao="l" value="Gerar PDF" class="btn btn-danger hidden-print">Gerar PDF</button>
                    <button type="button" id="tableToExcelWithCss" class="btn btn-success margin_b10 pull-right hidden-print"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
                    <table id="relatorio" class="table table-bordered table-hover table-condensed valign-middle text-sm">
                        <tr>
                            <td class="text-center" rowspan="3" colspan="2"><img src="http://<?=$_SERVER['SERVER_NAME']?>/intranet/imagens/logomaster1.gif"></td>
                            <td class="text-center text-bold" colspan="<?= (!empty($_REQUEST['tipo'])) ? 36 : 3 ?>">CONTRATO DE GESTÃO <?= $ano ?></td>
                        </tr>
                        <tr>
                            <td class="text-right text-bold">Parceiro:</td>
                            <td class="text-left" colspan="<?= (!empty($_REQUEST['tipo'])) ? 35 : 2 ?>">Sanperes</td>
                        </tr>
                        <tr>
                            <td class="text-right text-bold">Vigência:</td>
                            <td class="text-left" colspan="<?= (!empty($_REQUEST['tipo'])) ? 35 : 2 ?>"><?= date('d/m/Y') ?></td>
                        </tr>
                        <?php if(count($arrayGetOrcamento['arrayEntradas']) > 0) { ?>
                            <tr class="danger">
                                <th rowspan="3" class="" class="text-center">RECEITA</th>
                                <th rowspan="3" class="text-center">DESCRIÇÃO</th>
                                <th class="text-center" colspan="<?= (!empty($_REQUEST['tipo'])) ? 30 : 3?>"><?= $ano ?></th>
                                <?php if(!empty($_REQUEST['tipo'])){ ?>
                                <th class="text-center" colspan="6"><?= $ano + 1 ?></th>
                                <?php } ?>
                            </tr>
                            <tr class="danger">
                                <?php foreach ($arrayMeses as $i) { ?>
                                    <th colspan="3" class="text-center" <?= ($i == date('m')) ? $borderMes : null ?>><?= mesesArray($i) ?></th>
                                <?php } ?>
                            </tr>
                            <tr class="">
                                <?php foreach ($arrayMeses as $i) { ?>
                                    <?php if($check['previsto']) { ?><th <?= ($i == date('m')) ? $borderMes : null ?> class="text-center info">PREVISTO</th><?php } ?>
                                    <?php if($check['realizado']) { ?><th <?= ($i == date('m')) ? $borderMes : null ?> class="text-center success">REALIZADO</th><?php } ?>
                                    <th <?= ($i == date('m')) ? $borderMes : null ?> class="text-center warning">SALDO</th>
                                <?php } ?>
                            </tr>
                            <?php foreach ($arrayGetOrcamento['arrayEntradas'] as $id_grupo => $valueGrupo) { ?>
                            <tr class="text-bolder" <?= $border ?>>
                                <td class="text-bold"><?= $id_grupo ?></td>
                                <td class="text-bold"><?= $valueGrupo['descricao'] ?></td>
                                <?php foreach ($arrayMeses as $i) { ?>
                                    <?php if($check['previsto']) { ?><td class="text-center info" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arrayOrcamentoMes'][$id_grupo][$i],2,',','.') ?></td><?php } ?>
                                    <?php if($check['realizado']) { ?><td class="text-center success" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($valueGrupo['mes'][$i][2],2,',','.') ?></td><?php } ?>
                                    <td class="text-center warning" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arraySaldoAcumuladoReceitaMes'][$id_grupo][$i],2,',','.') ?></td>
                                <?php } ?>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                            <tr><td colspan="<?php echo (!$check['previsto'] || !$check['realizado']) ? 26 : 38 ?>">&nbsp;</td></tr>
                        <?php if(count($arrayGetOrcamento['arraySubGrupos']) > 0) { ?>
                            <tr class="danger">
                                <th rowspan="3" class="" class="text-center">DESPESAS</th>
                                <th rowspan="3" class="text-center">DESCRIÇÃO</th>
                                <th class="text-center" colspan="<?= (!empty($_REQUEST['tipo'])) ? 36 : 3?>"><?= $ano ?></th>
                                <?php if(!empty($_REQUEST['tipo'])){ ?>
                                <!--<th class="text-center" colspan="6"><?= $ano + 1 ?></th>-->
                                <?php } ?>
                            </tr>
                            <tr class="danger">
                                <?php foreach ($arrayMeses as $i) { ?>
                                <th colspan="<?php echo (!$check['previsto'] || !$check['realizado']) ? 2 : 3 ?>" class="text-center" <?= ($i == date('m')) ? $borderMes : null ?>><?= mesesArray($i) ?></th>
                                <?php } ?>
                            </tr>
                            <tr class="">
                                <?php foreach ($arrayMeses as $i) { ?>
                                    <?php if($check['previsto']) { ?><th <?= ($i == date('m')) ? $borderMes : null ?> class="text-center info">PREVISTO</th><?php } ?>
                                    <?php if($check['realizado']) { ?><th <?= ($i == date('m')) ? $borderMes : null ?> class="text-center success">REALIZADO</th><?php } ?>
                                    <th <?= ($i == date('m')) ? $borderMes : null ?> class="text-center warning">SALDO</th>
                                <?php } ?>
                            </tr>
                            <?php foreach ($arrayGetOrcamento['arrayGrupos'] as $id_grupo => $valueGrupo) {  ?>
                                <tr class="active text-bolder" <?= $border ?>>
                                    <td class="text-bold"><?= $id_grupo ?></td>
                                    <td class="text-bold"><?= $valueGrupo['descricao'] ?></td>
                                    <?php foreach ($arrayMeses as $i) { ?>
                                        <?php if($check['previsto']) { ?><td class="text-bold text-center info" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arrayOrcamento'][$id_grupo][$i],2,',','.') ?></td><?php } ?>
                                        <?php if($check['realizado']) { ?><td class="text-bold text-center success" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arraySaidas'][$id_grupo][$i],2,',','.') ?></td><?php } ?>
                                        <td class="text-center warning" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arraySaldo'][$id_grupo][$i],2,',','.') ?></td>
                                    <?php } ?>
                                </tr>
                                <?php if(!$_REQUEST['check_simples']){ ?>
                                    <?php foreach ($arrayGetOrcamento['arraySubGrupos'][$id_grupo] as $id_subgrupo => $valueSubGrupo) { ?>
                                        <tr class="" <?= $border ?>>
                                            <td class="text-bold"><?= $id_subgrupo ?></td>
                                            <td class="text-bold"><?= $valueSubGrupo['descricao'] ?></td>
                                            <?php foreach ($arrayMeses as $i) { ?>
                                                <?php if($check['previsto']) { ?><td class="text-bold text-center info" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arrayOrcamento'][$id_subgrupo][$i],2,',','.') ?></td><?php } ?>
                                                <?php if($check['realizado']) { ?><td class="text-bold text-center success" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arraySaidas'][$id_subgrupo][$i],2,',','.') ?></td><?php } ?>
                                                <td class="text-center warning" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arraySaldo'][$id_subgrupo][$i],2,',','.') ?></td>
                                            <?php } ?>
                                        </tr>
                                        <?php foreach ($arrayGetOrcamento['arrayTipos'][$id_subgrupo] as $id_tipo => $valueTipo) { ?>
                                            <tr class="" <?= $border ?>>
                                                <td class=""><?= $id_tipo ?></td>
                                                <td class=""><?= $valueTipo['descricao'] ?></td>
                                                <?php foreach ($arrayMeses as $i) { ?>
                                                    <?php if($check['previsto']) { ?><td class="text-center info" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arrayOrcamento'][$id_tipo][$i],2,',','.') ?></td><?php } ?>
                                                    <?php if($check['realizado']) { ?><td class="text-center success" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arraySaidas'][$id_tipo][$i],2,',','.') ?></td><?php } ?>
                                                    <td class="text-center warning" <?= ($i == date('m')) ? $borderMes : null ?>><?= number_format($arrayGetOrcamento['arraySaldo'][$id_tipo][$i],2,',','.') ?></td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
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