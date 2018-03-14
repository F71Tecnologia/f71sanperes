<?php
//session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/BotoesClass.php");
include("../../classes/ContabilHistoricoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$acoes = new Acoes();

$global = new GlobalClass();

$objHistorico = new ContabilHistoricoPadraoClass();

$array_historico = $objHistorico->listarHistoricos();

$titulo = "Gestão de Históricos Padrões";

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Gestão de Históricos Padrões", "id_form" => "frmplanodeconta");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $titulo ?></title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <style>
            table.nfse{
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
            }
            table.nfse td, table.nfse th{
                border: 1px solid #000;
                padding: 3px;
            }

            tr.warning td{
                background-color: #ffff99;
            }
            tr.danger td{
                background-color: #ff9999;
            }

        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container"> 

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $titulo ?></small></h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->

                    <div class="margin_b15 text-right">
                        <button type="button" id="btn_add" class="btn btn-success"><i class="fa fa-plus"></i> Cadastrar</button>
                    </div>



                    <div class="panel panel-default">
                        <table class="table table-striped table-hover valign-middle text-sm" id="tbl_historico">
                            <thead>
                                <tr>
                                    <th style="width: 10%;" class="text-center">Código</th>
                                    <th style="width: 70%;" class="text-center">Histórico</th>
                                    <th style="width: 20%;" colspan="2">&emsp;</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php if ($objHistorico->getNumRows() > 0) { ?>
                                    <?php while($objHistorico->getRow()){ ?>
                                        <tr>
                                            <td class="text-center"><?= $objHistorico->getIdHistorico() ?></td>
                                            <td><?= $objHistorico->getTexto() ?></td>

                                            <td class="text-right">
                                                <button class="btn btn-xs btn-success btn_editar" data-id="<?= $objHistorico->getIdHistorico() ?>"><i class="fa fa-pencil"></i> Editar</button>
                                                <!--<button class="btn btn-xs btn-info btn_vincular" data-id="<?= $objHistorico->getIdHistorico() ?>"><i class="fa fa-link"></i> Vincular</button>-->
                                                <button class="btn btn-xs btn-danger btn_excluir" data-id="<?= $objHistorico->getIdHistorico() ?>"><i class="fa fa-trash"></i> Excluir</button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr id="no_info">
                                        <td class="text-center text-bold text-info" colspan="4">Não há NFSe para conferência neste momento.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>


                    <?php include_once '../../template/footer.php'; ?>
                </div>
            </div>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../js/historico.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>