<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes/FolhaClass.php");

$objFolha = new Folha();
$usuario = carregaUsuario();
//$container_full = true;
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$mes = ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
$ano = ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');

/**
 * MONTA SELECT CNPJ
 */
$sqlCNPJ = "SELECT CONCAT(A.cnpj,' - ',GROUP_CONCAT(B.nome SEPARATOR ', ')) nome, GROUP_CONCAT(A.id_projeto) id_projeto FROM rhempresa A LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto) WHERE A.id_master = {$usuario['id_master']} GROUP BY A.cnpj;";
$qryCNPJ = mysql_query($sqlCNPJ);
while($rowCNPJ = mysql_fetch_assoc($qryCNPJ)){
    $arrayCNPJ[$rowCNPJ['id_projeto']] = $rowCNPJ['nome'];
}

if($_REQUEST['filtrar']){
    
    /**
    * TRATAMENTO FORMATO DINHEIRO
    */
    $guia_inss = str_replace('R$ ','',str_replace(',','.',str_replace('.','',$_REQUEST['guia_inss'])));
    $guia_compensacao = str_replace('R$ ','',str_replace(',','.',str_replace('.','',$_REQUEST['guia_compensacao'])));
    $guia_fgts = str_replace('R$ ','',str_replace(',','.',str_replace('.','',$_REQUEST['guia_fgts'])));
    $guia_pis = str_replace('R$ ','',str_replace(',','.',str_replace('.','',$_REQUEST['guia_pis'])));
    $guia_ir = str_replace('R$ ','',str_replace(',','.',str_replace('.','',$_REQUEST['guia_ir'])));
    $guia_ir_autonomo = str_replace('R$ ','',str_replace(',','.',str_replace('.','',$_REQUEST['guia_ir_autonomo'])));
    $guia_iss_autonomo = str_replace('R$ ','',str_replace(',','.',str_replace('.','',$_REQUEST['guia_iss_autonomo'])));
    
    /**
    * MONTA ARRAY COM O NOME DOS PROJETOS
    */
    $sqlProjeto = "SELECT id_projeto, nome FROM projeto WHERE id_master = {$usuario['id_master']} AND id_projeto IN ({$_REQUEST['projeto_cnpj']})";
    $qryProjeto= mysql_query($sqlProjeto);
    while($rowProjeto = mysql_fetch_assoc($qryProjeto)){
       $arrayProjeto[$rowProjeto['id_projeto']] = $rowProjeto['nome'];
    }
    
    $array = $objFolha->getConferenciaFolhaSefipGuia($mes,$ano,array_keys($arrayProjeto));
    $arrayLegenda = array_keys($array);
}

if($_COOKIE['debug'] == 666){
    print_array('////////////////////////////////$arrayLegenda////////////////////////////////');
    print_array($arrayLegenda);
}
/**
 * PARAMETROS DE CONFIG DA PAGINA
 */
$nome_pagina = "RELATÓRIO CONFERÊNCIA FOLHA x GUIA SEFIP";
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"$nome_pagina");
$breadcrumb_pages = array("Gestão de Orçamentos"=>"../relatorios/gestao_orcamentos.php");

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
                    <form action="" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" autocomplete="off">
                    <div class="panel panel-default hidden-print">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-sm-8">
                                    <div class="text-bold">Projeto:</div>
                                    <div class="" id="">
                                        <?= montaSelect($arrayCNPJ, $_REQUEST['projeto_cnpj'], 'class="form-control input-sm" id="projeto_cnpj" name="projeto_cnpj"') ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="text-bold">Competência:</div>
                                    <div class="input-group" id="">
                                        <?= montaSelect(mesesArray(), $mes, 'class="form-control input-sm" id="mes" name="mes"') ?>
                                        <div class="input-group-addon">/</div>
                                        <?= montaSelect(anosArray(2016), $ano, 'class="form-control input-sm" id="ano" name="ano"') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-2">
                                    <div class="text-bold">Valor Guia INSS:</div>
                                    <div class="" id="">
                                        <input type="text" name="guia_inss" class="form-control input-sm valor" value="<?= $_REQUEST['guia_inss'] ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">Compensação:</div>
                                    <div class="" id="">
                                        <input type="text" name="guia_compensacao" class="form-control input-sm valor" value="<?= $_REQUEST['guia_compensacao'] ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">Valor Guia FGTS:</div>
                                    <div class="" id="">
                                        <input type="text" name="guia_fgts" class="form-control input-sm valor" value="<?= $_REQUEST['guia_fgts'] ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">Valor Guia PIS:</div>
                                    <div class="" id="">
                                        <input type="text" name="guia_pis" class="form-control input-sm valor" value="<?= $_REQUEST['guia_pis'] ?>">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="text-bold">Valor Guia IR:</div>
                                    <div class="" id="">
                                        <input type="text" name="guia_ir" class="form-control input-sm valor" value="<?= $_REQUEST['guia_ir'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-3">
                                    <div class="text-bold">Valor Guia ISS Autonomo:</div>
                                    <div class="" id="">
                                        <input type="text" name="guia_iss_autonomo" class="form-control input-sm valor" value="<?= $_REQUEST['guia_iss_autonomo'] ?>">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="text-bold">Valor Guia IR Autonomo:</div>
                                    <div class="" id="">
                                        <input type="text" name="guia_ir_autonomo" class="form-control input-sm valor" value="<?= $_REQUEST['guia_ir_autonomo'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button name="filtrar" class="btn btn-primary" value="filtrar"><i class="fa fa-filter"></i> FILTRAR</button>
                        </div>
                    </div>
                    </form>
                    <hr>
                    <?php if(count($array) > 0) { ?>
                    <button type="button" form="formPdf" name="pdf" data-title="Titulo" data-id="relatorio" id="pdf" data-orientacao="l" value="Gerar PDF" class="btn btn-danger">Gerar PDF</button>
                    <button type="button" id="tableToExcelWithCss" class="btn btn-success margin_b10 pull-right hidden-print"><i class="fa fa-file-excel-o"></i> Exportar para Excel</button>
                    <table id="relatorio" class="table table-bordered table-hover table-striped table-condensed valign-middle text-sm">
                        <tr>
                        <?php foreach ($arrayProjeto as $i => $value) { ?>
                            <td class="text-bold text-center" colspan="2">TOTALIZADOR: <?= $arrayProjeto[$i] ?></td>
                        <?php } ?>
                        </tr>
                        <?php foreach ($array as $legenda => $valor) { ?>
                        <tr>
                            <?php foreach ($arrayProjeto as $i => $value) { ?>
                            <td class="text-bold"><?= $legenda ?></td>
                            <td class="text-right"><?= ($legenda == 'PARTICIPANTES') ? $valor[$i] : number_format($valor[$i], 2, ',', '.') ?></td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                        <tr><td colspan="6">&nbsp;</td></tr>
                        <tr><td colspan="6" class="info text-center text-bold">TOTALIZADORES</td></tr>
                        <tr>
                            <td class="text-center warning" colspan="2">DESCONTO INSS</td>
                            <td class="text-center warning" colspan="2">DESCONTO FGTS</td>
                            <td class="text-center warning" colspan="2">DESCONTO PIS</td>
                        </tr>
                        <?php foreach ($arrayProjeto as $i => $value) { ?>
                        <tr>
                            <td>
                            <?= $arrayProjeto[$i] ?>
                            <?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[4]][$i]} + {$array[$arrayLegenda[6]][$i]} + {$array[$arrayLegenda[7]][$i]} + {$array[$arrayLegenda[8]][$i]} - {$array[$arrayLegenda[16]][$i]} + {$array[$arrayLegenda[18]][$i]})" : null ?>
                            </td>
                            <td class="text-right"><?= number_format($array[$arrayLegenda[4]][$i] + $array[$arrayLegenda[6]][$i] + $array[$arrayLegenda[7]][$i] + $array[$arrayLegenda[8]][$i] + $array[$arrayLegenda[16]][$i] - $array[$arrayLegenda[18]][$i], 2, ',', '.') ?></td>
                            <td>
                            <?= $arrayProjeto[$i] ?>
                            <?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[15]][$i]})" : null ?>
                            </td>
                            <td class="text-right"><?= number_format($array[$arrayLegenda[15]][$i], 2, ',', '.') ?></td>
                            <td>
                            <?= $arrayProjeto[$i] ?>
                            <?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[14]][$i]})" : null ?>
                            </td>
                            <td class="text-right"><?= number_format($array[$arrayLegenda[14]][$i], 2, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($_REQUEST['projeto_cnpj'] == '1,2') { ?>
                        <tr>
                            <td class="text-bold">INST. + NORTE<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[4]][1]} + {$array[$arrayLegenda[6]][1]} + {$array[$arrayLegenda[7]][1]} + {$array[$arrayLegenda[8]][1]} + {$array[$arrayLegenda[16]][1]} + {$array[$arrayLegenda[18]][1]} + {$array[$arrayLegenda[4]][2]} + {$array[$arrayLegenda[6]][2]} + {$array[$arrayLegenda[7]][2]} + {$array[$arrayLegenda[8]][2]} - {$array[$arrayLegenda[16]][2]} + {$array[$arrayLegenda[18]][2]})" : null ?></td>
                            <td class="text-bold text-right"><?= number_format($array[$arrayLegenda[4]][1] + $array[$arrayLegenda[6]][1] + $array[$arrayLegenda[7]][1] + $array[$arrayLegenda[8]][1] + $array[$arrayLegenda[16]][1] + $array[$arrayLegenda[18]][1] + $array[$arrayLegenda[4]][2] + $array[$arrayLegenda[6]][2] + $array[$arrayLegenda[7]][2] + $array[$arrayLegenda[8]][2] - $array[$arrayLegenda[16]][2] + $array[$arrayLegenda[18]][2], 2, ',', '.') ?></td>
                            <td class="text-bold">INST. + NORTE<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[15]][1]} + {$array[$arrayLegenda[15]][2]})" : null ?></td>
                            <td class="text-bold text-right"><?= number_format($array[$arrayLegenda[15]][1] + $array[$arrayLegenda[15]][2], 2, ',', '.') ?></td>
                            <td class="text-bold">INST. + NORTE<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[14]][1]} + {$array[$arrayLegenda[14]][2]})" : null ?></td>
                            <td class="text-bold text-right"><?= number_format($array[$arrayLegenda[14]][1] + $array[$arrayLegenda[14]][2], 2, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                        <!--<tr><td colspan="6">&nbsp;</td></tr>-->
                        <tr>
                            <td class="text-center warning" colspan="2">DESCONTO IR MES ANTERIOR</td>
                            <td class="text-center warning" colspan="2">DESCONTO IR AUTONOMO - MES ANTERIOR</td>
                            <td class="text-center warning" colspan="2">DESCONTO ISS AUTONOMO</td>
                        </tr>
                        <?php foreach ($arrayProjeto as $i => $value) { ?>
                        <tr>
                            <td>
                            <?= $arrayProjeto[$i] ?>
                            <?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[10]][$i]})" : null ?>
                            </td>
                            <td class="text-right"><?= number_format($array[$arrayLegenda[10]][$i], 2, ',', '.') ?></td>
                            <td>
                            <?= $arrayProjeto[$i] ?>
                            <?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[20]][$i]})" : null ?>
                            </td>
                            <td class="text-right"><?= number_format($array[$arrayLegenda[20]][$i], 2, ',', '.') ?></td>
                            <td>
                            <?= $arrayProjeto[$i] ?>
                            <?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[21]][$i]})" : null ?>
                            </td>
                            <td class="text-right"><?= number_format($array[$arrayLegenda[21]][$i], 2, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($_REQUEST['projeto_cnpj'] == '1,2') { ?>
                        <tr>
                            <td class="text-bold">INST. + NORTE<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[10]][1]} + {$array[$arrayLegenda[10]][2]})" : null ?></td>
                            <td class="text-bold text-right"><?= number_format($array[$arrayLegenda[10]][1] + $array[$arrayLegenda[10]][2], 2, ',', '.') ?></td>
                            <td class="text-bold">INST. + NORTE<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[20]][1]} + {$array[$arrayLegenda[20]][2]})" : null ?></td>
                            <td class="text-bold text-right"><?= number_format($array[$arrayLegenda[20]][1] + $array[$arrayLegenda[20]][2], 2, ',', '.') ?></td>
                            <td class="text-bold">INST. + NORTE<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[21]][1]} + {$array[$arrayLegenda[21]][2]})" : null ?></td>
                            <td class="text-bold text-right"><?= number_format($array[$arrayLegenda[21]][1] + $array[$arrayLegenda[21]][2], 2, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                        <tr><td colspan="6">&nbsp;</td></tr>
                        <tr><td colspan="6" class="danger text-center text-bold">TOTALIZADORES - GUIA DE PAGAMENTO</td></tr>
                        <?php if($_REQUEST['projeto_cnpj'] == '1,2') { ?>
                        <tr>
                            <td class="text-center warning">DESCONTO INSS<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[4]][1]} + {$array[$arrayLegenda[6]][1]} + {$array[$arrayLegenda[7]][1]} + {$array[$arrayLegenda[8]][1]} - {$array[$arrayLegenda[16]][1]} + {$array[$arrayLegenda[18]][1]} + {$array[$arrayLegenda[4]][2]} + {$array[$arrayLegenda[6]][2]} + {$array[$arrayLegenda[7]][2]} + {$array[$arrayLegenda[8]][2]} - {$array[$arrayLegenda[16]][2]} + {$array[$arrayLegenda[18]][2]} - {$guia_inss} - {$guia_compensacao})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[4]][1] + $array[$arrayLegenda[6]][1] + $array[$arrayLegenda[7]][1] + $array[$arrayLegenda[8]][1] - $array[$arrayLegenda[16]][1] + $array[$arrayLegenda[18]][1] + $array[$arrayLegenda[4]][2] + $array[$arrayLegenda[6]][2] + $array[$arrayLegenda[7]][2] + $array[$arrayLegenda[8]][2] - $array[$arrayLegenda[16]][2] + $array[$arrayLegenda[18]][2] - $guia_inss - $guia_compensacao, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO FGTS<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[15]][1]} + {$array[$arrayLegenda[15]][2]} - {$guia_fgts})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[15]][1] + $array[$arrayLegenda[15]][2] - $guia_fgts, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO PIS<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[14]][1]} + {$array[$arrayLegenda[14]][2]} - {$guia_pis})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[14]][1] + $array[$arrayLegenda[14]][2] - $guia_pis, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-center warning">DESCONTO IR MES ANTERIOR<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[10]][1]} + {$array[$arrayLegenda[10]][2]} - {$guia_ir})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[10]][1] + $array[$arrayLegenda[10]][2] - $guia_ir, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO IR AUTONOMO - MES ANTERIOR<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[20]][1]} + {$array[$arrayLegenda[20]][2]} - {$guia_ir_autonomo})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[20]][1] + $array[$arrayLegenda[20]][2] - $guia_ir_autonomo, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO ISS AUTONOMO<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[21]][1]} + {$array[$arrayLegenda[21]][2]} - {$guia_iss_autonomo})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[21]][1] + $array[$arrayLegenda[21]][2] - $guia_iss_autonomo, 2, ',', '.') ?></td>
                        </tr>
                        <?php } else { ?>
                        <tr>
                            <td class="text-center warning">DESCONTO INSS<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[4]][3]} + {$array[$arrayLegenda[6]][3]} + {$array[$arrayLegenda[7]][3]} + {$array[$arrayLegenda[8]][3]} - {$array[$arrayLegenda[16]][3]} + {$array[$arrayLegenda[18]][3]} - {$guia_inss} - {$guia_compensacao})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[4]][3] + $array[$arrayLegenda[6]][3] + $array[$arrayLegenda[7]][3] + $array[$arrayLegenda[8]][3] - $array[$arrayLegenda[16]][3] + $array[$arrayLegenda[18]][3] - $guia_inss - $guia_compensacao, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO FGTS<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[15]][3]} - {$guia_fgts})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[15]][3] - $guia_fgts, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO PIS<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[14]][3]} - {$guia_pis})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[14]][3] - $guia_pis, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-center warning">DESCONTO IR MES ANTERIOR<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[10]][3]} - {$guia_ir})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[10]][3] - $guia_ir, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO IR AUTONOMO - MES ANTERIOR<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[20]][3]} - {$guia_ir_autonomo})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[20]][3] - $guia_ir_autonomo, 2, ',', '.') ?></td>
                            <td class="text-center warning">DESCONTO ISS AUTONOMO<?= ($_COOKIE['debug'] == 666) ? "<br>({$array[$arrayLegenda[21]][3]} - {$guia_iss_autonomo})" : null ?></td>
                            <td class="text-center warning"><?= number_format($array[$arrayLegenda[21]][3] - $guia_iss_autonomo, 2, ',', '.') ?></td>
                        </tr>
                        <?php } ?>
                        <tr><td colspan="6">&nbsp;</td></tr>
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