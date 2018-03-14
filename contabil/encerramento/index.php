<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$nome_pagina = "Encerramento do Exercício";
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "form1"); 
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: </title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header"><h2><span class="fa fa-unlock-alt"></span> - Encerramento Contábil do Exercício</h2></div>
                    <div class="bs-component">
                        <div class="detalhes-modulo">
                            <?php echo $botoes->getHtmlBotoesModulo(48, 38) ?>
                        </div>
                    </div> 
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
<!--        <form name="planodecontas" id="formplanodecontas" action="">
            <input type="hidden" name="home" id="home" value="" />
            <input type="hidden" name="id_user" id="id_user" value="<?php echo $_COOKIE['logado'] ?>" />
        </form>-->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
    </body>
</html>