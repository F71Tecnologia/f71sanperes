<?php
/*
 * chamado no indexx.php
 */
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: PIS em Lote ::</title>

        <link rel="shortcut icon" href="../../favicon.png" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">

    </head>
    <body>

        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-xs-12">

                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - PIS em Lote</small></h2></div>

                    <ul class="nav nav-tabs" style="margin-bottom: 20px;">
                        <li role="presentation"><a href="form_pis.php"> Gerar Pis Em Lote</a></li>
                        <li role="presentation" class="active"><a href="#">Atualizar Pis em Lote</a></li>
                    </ul>
                </div>
                <div class="col-xs-12">
                    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" name="form-atualizar" id="form-atualizar" class="form-horizontal top-margin1" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1" class="col-lg-2 control-label">Arquivo de Retorno:</label>
                                        <div class="col-lg-5">
                                            <input type="file" class="form-control filestyle" id="arquivo" name="arquivo" data-buttonText=" Selecione Arquivo">
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                            <div class="panel-footer text-right">
                                <?php if ($_COOKIE['logado'] == 256) { ?>
                                  <input type="submit" value="Debug" name="method" class="btn btn-info">
                                <?php } ?>
                                <input type="submit" value="Visualizar" name="method" class="btn btn-info">
                            </div><!-- /.panel-footer -->
                        </div><!-- /.panel -->
                        <div id="resp-autalizar"></div>
                    </form>
                </div><!-- /.col-xs-12 -->

            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="js/inicio.js" type="text/javascript"></script>
    </body>
</html>