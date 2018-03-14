<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
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
include("../classes_permissoes/acoes.class.php");
//print_array($_REQUEST);exit;
$usuario = carregaUsuario();
$objAcao = new Acoes();
$id_regiao = $usuario['id_regiao'];

$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objProvisao = new ContabilFolhaProvisaoClass();
$objProvisaoProc = new ContabilFolhaProvisaoProcClass();

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'deletar') {
    $objProvisao->setIdProvisao($_REQUEST['id_provisao']);
    echo $objProvisao->inativa();
    $objProvisaoProc->setIdProvisao($_REQUEST['id_provisao']);
    $objProvisaoProc->inativaByIdProvisao();
    exit;
}

$objProvisao->setIdRegiao($usuario['id_regiao']);
$objProvisao->getByRegiao();

$nome_pagina = "Folha de Provisão";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta"); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?=$nome_pagina?></title>
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
        <?php include("../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?=$nome_pagina?></small></h2>
                    </div>
                    <?php if(isset($_GET['s'])) { ?>
                    <div class="alert alert-dismissable alert-success text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Provisão criada com sucesso!</div>
                    <?php } ?>
                    <div class="panel panel-default">
                        <div class="panel-body text-right ">
                            <?php if ($objAcao->verifica_permissoes(109)) { ?><a href="cad_folha_de_provisao.php" class="btn btn-success btn-sm" id="cadastrar"><i class="fa fa-plus"></i> Nova Provisão</a><?php } ?>
                        </div>
                        <div class="panel-footer" style="padding-top: 32px!important;">
                            <!--<legend>Selecionar o Projeto: </legend>
                            <div class="form-group form-horizontal">
                                <label class="col-sm-2 text-sm control-label">Projeto</label>
                                <div class="col-sm-4"><?= montaSelect(getProjetos($usuario['id_regiao']), $projetoR, "id='id_projeto' name='id_projeto' class='form-control input-sm validate[required,custom[select]]'"); ?></div>
                            </div>-->  
                            <?php 
                            if($objProvisao->getNumRows() > 0) { 
                                while($objProvisao->getRow()) { //$objProjeto->setIdProjeto($objProvisao->getIdProjeto());
                                    if($objProvisao->getIdProjeto() != $auxIdProjeto) { 
                                        
                                        if(!empty($auxIdProjeto)) { $auxAno = '';?>
                                        </tbody></table></div></div></div>
                                        <?php } ?>
                                        <div class="stat-panel text-center">
                                            <div class="stat-row">
                                                <!-- Dark gray background, small padding, extra small text, semibold text -->
                                                <div class="stat-cell bg-default padding-sm text-xs text-semibold text-left pointer projeto_provisao_head" data-projeto="<?= $objProvisao->getIdProjeto() ?>">
                                                    <i class="fa fa-home"></i>&nbsp;&nbsp;<?php $projeto = projetosId($objProvisao->getIdProjeto()); echo $projeto['nome'] ?>
                                                </div>
                                            </div> <!-- /.stat-row -->
                                            <div class="stat-row">
                                                <!-- Bordered, without top border, without horizontal padding -->
                                                <div class="stat-cell no-border-t no-padding-hr projeto_provisao_body no-padding-b" style="display: none;" id="<?= $objProvisao->getIdProjeto() ?>">
                                                    <table class="table table-condensed table-hover table-striped text-sm valign-middle no-margin-b">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="4">Provisão da folha no período</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $auxIdProjeto = $objProvisao->getIdProjeto();  } ?>
                                                            <?php if( $auxAno != $objProvisao->getAnoProvisao() ){ ?>
                                                                <tr class="info pointer ano_folha" data-key="<?= $objProvisao->getAnoProvisao() ?>">
                                                                    <td colspan="4" class="text-center"><?= $objProvisao->getAnoProvisao() ?></td><td>
                                                                </tr>
                                                            <?php } ?>
                                                            <tr class="folha <?= $objProvisao->getAnoProvisao() ?>" style="display: none;">
                                                                <td class="text-left">ID Folha <?= $objProvisao->getTitulo() ?></td>
                                                                <td class="text-right">Valor do provisionamento</td>
                                                                <td class="text-right"><?= 'R$ '.number_format($objProvisao->getRescisao() + $objProvisao->getMulta50() + $objProvisao->getFerias() + $objProvisao->getUmTerco() + $objProvisao->getDecimoTereiro() + $objProvisao->getFgts() + $objProvisao->getPis() + $objProvisao->getInss() + $objProvisao->getLei12506() +  $objProvisao->getRat() + $objProvisao->getOutras(), 2, ',', '.') ?></td>
                                                                <td class="text-right">
                                                                    <button class="btn btn-xs btn-info detalhar" data-toggle="tooltip" title="Detalhar" data-key="<?= $objProvisao->getIdProvisao() ?>"><i class="fa fa-search-plus"></i></button>
                                                                    <?php if ($objAcao->verifica_permissoes(110)) { ?><button class="btn btn-xs btn-danger deletar" data-toggle="tooltip" title="Deletar" data-key="<?= $objProvisao->getIdProvisao() ?>"><i class="fa fa-trash-o"></i></button><?php } ?>
                                                                    <?php if ($objAcao->verifica_permissoes(110)) { ?><button class="btn btn-xs btn-default Imprimir" data-toggle="tooltip" title="Imprimir" data-key="<?= $objProvisao->getIdProvisao() ?>"><i class="fa fa-print"></i></button><?php } ?>
                                                                </td>
                                                            </tr>
                                                            <?php $auxAno = $objProvisao->getAnoProvisao(); ?>
                                <?php } ?>
                                                    </tbody>
                                                    </table>
                                                </div>
                                            </div> <!-- /.stat-row -->
                                        </div> <!-- /.stat-panel -->
                            <?php } else { ?>
                                <div class="alert alert-info">Nenhuma Provisão Encontrada!</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div>
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