<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}

include("../conn.php");
include("../wfunction.php");
include("../classes/BotoesClass.php");
include("../classes/global.php");
include("../classes/ContabilFolhaProvisaoClass.php");
include("../classes/ContabilFolhaProvisaoProcClass.php");
//print_array($_REQUEST);exit;
$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objProvisao = new ContabilFolhaProvisaoClass();
$objProvisao->setIdProvisao($_REQUEST['id_provisao']);
$objProvisao->getById();
$objProvisao->getRow();

$objProvisaoProc = new ContabilFolhaProvisaoProcClass();
$objProvisaoProc->setIdProvisao($objProvisao->getIdProvisao());
$objProvisaoProc->getByIdProvisao();

$nome_pagina = "Detalhamento da Folha de Provisão";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Folha de Provisão" => "folha_de_provisao.php")
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        
    </head>
    <body>
        <script type="text/javascript" src="wz_tooltip.js"></script>
<?php include("../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <div class="stat-panel text-center">
                        <div class="stat-row">
                            <!-- Dark gray background, small padding, extra small text, semibold text -->
                            <div class="stat-cell bg-primary padding-sm text-xs text-semibold">
                                <i class="fa fa-map-o"></i>&nbsp;&nbsp;<?= $objProvisao->getTitulo() ?>
                            </div>
                        </div> <!-- /.stat-row -->
                        <div class="stat-row">
                            <!-- Bordered, without top border, without horizontal padding -->
                            <div class="stat-cell no-border-t no-padding-hr">
                                <table class="table table-condensed table-hover table-striped text-sm valign-middle">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th class="text-right">Salário</th>
                                            <th class="text-right">Aviso</th>
                                            <th class="text-right">Multa 50%</th>
                                            <th class="text-right">Férias</th>
                                            <th class="text-right">1/3 Férias</th>
                                            <th class="text-right">13º Salário</th>
                                            <th class="text-right">Lei 12.506</th>
                                            <th class="text-right">FGTS (8%)</th>
                                            <th class="text-right">PIS (1%)</th>
                                            <th class="text-right">RAT (<?= $objProvisao->getRatPercent() ?>%)</th>
                                            <th class="text-right">INSS (20%)</th>
                                            <th class="text-right">TERCEIROS (<?= $objProvisao->getOutrosPercent() ?>%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $tot_salario = 0;
                                        $tot_rescisao = 0;
                                        $tot_rescisao_50 = 0;
                                        $tot_ferias = 0;
                                        $tot_um_terco = 0;
                                        $tot_decimo_terceiro = 0;
                                        $tot_lei = 0;
                                        $tot_fgts = 0;
                                        $tot_inss = 0;
                                        $tot_pis = 0;
                                        $tot_rat = 0;
                                        $tot_outros = 0;
                                        
                                        
                                        while ($objProvisaoProc->getRow()) {
                                            
//                                            $tot_salario += $objProvisaoProc->getSalario();
                                            $tot_rescisao += $objProvisaoProc->getRescisao();
                                            $tot_rescisao_50 += $objProvisaoProc->getRescisao50();
                                            $tot_ferias += $objProvisaoProc->getFerias();
                                            $tot_um_terco += $objProvisaoProc->getUmTerco();
                                            $tot_decimo_terceiro += $objProvisaoProc->getDecimoTereiro();
                                            $tot_lei += $objProvisaoProc->getLei12506Valor();
                                            $tot_fgts += $objProvisaoProc->getFgts();
                                            $tot_inss += $objProvisaoProc->getInss();
                                            $tot_pis += $objProvisaoProc->getPis();
                                            $tot_rat += $objProvisaoProc->getRat();
                                            $tot_outros += $objProvisaoProc->getOutrasEntidades();
                                            ?>
                                            <tr>
                                                <td class="text-left"><?= mysql_result(mysql_query("SELECT nome FROM rh_clt WHERE id_clt = '{$objProvisaoProc->getIdClt()}' LIMIT 1;"), 0) ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getSalario(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getRescisao(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getRescisao50(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getFerias(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getUmTerco(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getDecimoTereiro(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getLei12506Valor(), 2, ',', '.') ?></td>
                                                <td class="text-right"><i onmouseover="Tip('Multa + Férias + 1/3 Férias + 13º Salário + Lei 12.506')" onmouseout="UnTip()"><?= number_format($objProvisaoProc->getFgts(), 2, ',', '.') ?></i></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getPis(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getRat(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getInss(), 2, ',', '.') ?></td>
                                                <td class="text-right"><?= number_format($objProvisaoProc->getOutrasEntidades(), 2, ',', '.') ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-bold" style="font-size: 1.2em;">
                                            <td class="text-left">TOTAL</td>
                                            <td class="text-right"><?= number_format($tot_salario, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_rescisao, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_rescisao_50, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_ferias, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_um_terco, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_decimo_terceiro, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_lei, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_fgts, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_pis, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_rat, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_inss, 2, ',', '.') ?></td>
                                            <td class="text-right"><?= number_format($tot_outros, 2, ',', '.') ?></td>
                                        </tr>
                                        <tr class="text-bold" style="font-size: 1.4em;">
                                            <td class="text-left" colspan="12">TOTAL GERAL</td>
                                            <td class="text-right"><?= number_format( $tot_rescisao + $tot_rescisao_50 + $tot_ferias + $tot_um_terco + $tot_decimo_terceiro + $tot_lei + $tot_fgts + $tot_inss + $tot_pis + $tot_rat + $tot_outros, 2, ',', '.') ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div> <!-- /.stat-row -->
                    </div> <!-- /.stat-panel -->
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
        <script type="text/javascript" src="wz_tooltip.js"></script>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/folha_de_provisao.js" type="text/javascript"></script>
    </body>
</html>