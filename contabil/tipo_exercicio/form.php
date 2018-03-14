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

if (isset($_REQUEST['salvar'])) {
    $arr = [
        'id_projeto' => addslashes($_REQUEST['id_projeto']),
        'id_exercicio' => addslashes($_REQUEST['id_exercicio']),
    ];
    if (isset($_REQUEST['id'])) {
        $id = addslashes($_REQUEST['id_assoc']);

        foreach ($arr as $key => $value) {
            $xxx[] = "$key = '$value'";
        }

        $query = "UPDATE contabil_exercicios_assoc SET " . implode(', ', $xxx) . " WHERE id = {$id}";
    } else {
        $keys = array_keys($arr);
        $values = array_values($arr);
        $query = "INSERT INTO contabil_exercicios_assoc (" . implode(', ', $keys) . ") VALUES ('" . implode("', '", $values) . "')";
    }

    $status = mysql_query($query);

    $erros = mysql_error();

    $_SESSION['MESSAGE'] = $status || empty($erros) ? 'Salvo com sucesso.' : 'Erro ao salvar. ' . $erros;
    $_SESSION['MESSAGE_TYPE'] = $status || empty($erros) ? 'success' : 'danger';
}


if (isset($_REQUEST['id'])) {
    $id = addslashes($_REQUEST['id']);
    $query = "SELECT * FROM contabil_exercicios_assoc WHERE id={$id}";
    $assoc = mysql_fetch_assoc(mysql_query($query));
}

$query = "SELECT * FROM contabil_exercicios WHERE status = 1;";
$result = mysql_query($query);
$arr_exercicio['-1'] = 'Selecione';
while ($row = mysql_fetch_assoc($result)) {
    $arr_exercicio[$row['id_exercicio']] = $row['nome'];
}

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
                    <form class="form-horizontal" method="post">
                        <div class="panel panel-default">
                            <!--<div class="panel-heading"></div>-->
                            <?php if (isset($_REQUEST['id'])) { ?>
                                <input type="hidden" name="id_assoc" value="<?= $assoc['id'] ?>">
                            <?php } ?>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="id_projeto" class="col-sm-2 control-label">Projeto</label>
                                    <div class="col-sm-5">
                                        <?= montaSelect(getProjetos($id_regiao), $assoc['id_projeto'], 'class="form-control" id="id_projeto" name="id_projeto"') ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="id_exercicio" class="col-sm-2 control-label">Exercício</label>
                                    <div class="col-sm-5">
                                        <?= montaSelect($arr_exercicio, $assoc['id_exercicio'], 'class="form-control" id="id_exercicio" name="id_exercicio"') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <a class="btn btn-default" href="javascript:history.back()"><i class="fa fa-reply"></i> Voltar</a>
                                <button type="submit" name="salvar" value="1" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
                            </div>


                        </div>
                    </form>
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