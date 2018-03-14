<?php
error_reporting(E_ALL);
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

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

// -----------------------------------------------------------------------------
$query = "SELECT assoc.*, exer.nome nome_exercicio,proj.nome nome_projeto
FROM contabil_exercicios_assoc assoc
INNER JOIN contabil_exercicios exer ON assoc.id_exercicio = exer.id_exercicio
INNER JOIN projeto proj ON assoc.id_projeto = proj.id_projeto";
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $assoc_array[] = $row;
}
// -----------------------------------------------------------------------------
// 
// 
//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$titulo = "Cadastro de Tipo de Exercício";
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $titulo, "id_form" => "frmplanodeconta");
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
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $titulo ?> </small></h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->
                </div>
                <div class="col-sm-12 margin_b20 text-right">
                    <a href="form.php" class="btn btn-success"><i class="fa fa-plus"></i> Novo</a>
                </div>
                <div class="col-sm-12">
                    <div class="panel panel-default">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Projeto</th>
                                    <th>Exercício</th>
                                    <th>&emsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assoc_array as $value) { ?>
                                    <tr>
                                        <td><?= $value['nome_projeto'] ?></td>
                                        <td><?= $value['nome_exercicio'] ?></td>
                                        <td class="text-right"><a href="form.php?id=<?= $value['id'] ?>" class="btn btn-success btn-xs"><i class="fa fa-pencil"></i></a></td>
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
        <!--<script src="../../resources/js/bootstrap-dialog.min.js"></script>-->
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <!--<script src="../../js/jquery.maskedinput-1.3.1.js"></script>-->
        <!--<script src="../../resources/js/financeiro/saida.js"></script>-->
        <!--<script src="../../js/jquery.validationEngine-2.6.js"></script>-->
        <!--<script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>-->
        <!--<script src="../../js/jquery.form.js" type="text/javascript"></script>-->
        <!--<script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>-->
    </body>
</html>