<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/ComprasChamados.php");
include("../../wfunction.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]'");
$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "35", "area" => "Gestão de Compras e Contratos", "ativo" => "Chamados a Prestadores", "id_form" => "form-pedido");

?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">
        <style>
            #iframe{
                width: 100%; 
                height: 667px;
                border:none;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <form target="iframe" method="post" action="http://suporte.institutolagosrio.com.br/login/logar" id="form1">
                <input type="hidden" name="login" value="<?= base64_encode("--{$usuario['login']}++") ?>">
                <input type="hidden" name="senha" value="<?= base64_encode("++{$usuario['senha']}__") ?>">
                <input type="hidden" name="externo" value="<?= base64_encode('+-xX_') ?>">
            </form>
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-compras-header">
                        <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Compras e Contratos <small>- Chamados a Prestadores</small></h2>
                    </div>
                </div>
                <div class="col-lg-12">
                    <!--<iframe id="iframe" src="http://suporte.institutolagosrio.com.br/login/logar/<?= $hash ?>"></iframe>-->
                    <iframe id="iframe"  name="iframe"></iframe>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script>
            $(function () {

                $("#form1").submit();
                $('#iframe').load(function () {
                    this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
                });
            });
        </script>

    </body>
</html>