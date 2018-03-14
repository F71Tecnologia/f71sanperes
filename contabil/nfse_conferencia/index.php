<?php
//session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
//require 'C:\xampp\htdocs\f71lagos-2.0\vendor\autoload.php';

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$acoes = new Acoes();

$global = new GlobalClass();
$nfse = new NFSe();

$dados['id_regiao'] = $id_regiao;
$nfse_servico_ok = $nfse->consultarServicoOk($dados);

//foreach ($nfse_servico_ok as $key => $value) {
//    $query = "SELECT ";
//}

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Tipos Sem Associação", "id_form" => "frmplanodeconta");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão NFe</title>
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
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- Conferência de NFSe </small></h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->

                    <?php if (count($nfse_servico_ok) > 0) { ?>

                        <div class="panel panel-default">
                            <table class="table table-striped table-hover valign-middle text text-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 8%" class="text text-right">Número NF</th>
                                        <th style="width: 10%" class="text text-center">Data</th>
                                        <th style="width: 24%">Projeto</th>
                                        <th style="width: 46%">Prestador</th>
                                        <th style="width: 12%">CNPJ</th>
                                        <th colspan="2">&emsp;</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <?php foreach ($nfse_servico_ok as $key => $value) { ?>
                                        <tr>
                                            <td class="text text-right"><?= $value['Numero'] ?></td>
                                            <td class="text text-center"><?= converteData($value['DataEmissao'], 'd/m/Y') ?></td>
                                            <td><?= $value['nome_projeto'] ?></td>
                                    <a href="../../Users/f71/Desktop/planodecontas.sql"></a>
                                    <td><?= $value['c_razao'] ?></td>
                                    <td><?= $value['c_cnpj'] ?></td>
                                    <td>
                                        <?php $xxx = (empty($value['arquivo_pdf'])) ? 'disabled' : ''; ?>
                                        <a href="../../compras/notas_fiscais/nfse_anexos/<?= $value['id_projeto'] ?>/<?= $value['arquivo_pdf'] ?>" target="_blank" class="btn btn-default btn-xs <?= $xxx ?>"  role="button"><i class="fa fa-file-pdf-o text-danger"></i> Ver PDF</a>
                                    </td>
                                    <td class="text text-right">
                                        <button class="btn btn-xs btn-warning btn-conferir" data-id="<?= $value['id_nfse'] ?>"><i class="fa fa-check-square-o"></i> Conferir</button>
                                    </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-dismissable alert-info">
                            <h4>Não há NFSe para conferência neste momento.</h4>
                            <a href="../../index.php" class="btn btn-default"><i class="fa fa-reply"></i> Voltar</a>
                        </div>
                    <?php } ?>

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
        <script src="../js/nfse_conferencia.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>