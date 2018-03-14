
<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}
include('../conn.php');
include('../empresa.php');
$img = new empresa();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link rel="shortcut icon" href="../../../favicon.png">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet">
        <link href="../../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../../resources/css/style-print.css" rel="stylesheet">
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <!--<button type="button" id="voltar" class="btn btn-default navbar-btn"><i class="fa fa-reply"></i>Voltar</button>-->
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <div class="text-center">
                <?php $img->imagem(); ?>
            </div>
            <h1 class="text-center">Dados do Empresa</h1>
            <?php require_once 'table_dados_empresa.php'; ?>
            
        </div><!-- /.pagina -->

        <!-- javascript aqui -->
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
