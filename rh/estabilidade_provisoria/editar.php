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

        <title>:: Intranet :: Estabilidade Provisória ::</title>

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

                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Estabilidade Provisória</small></h2></div>

                </div>
                <div class="col-xs-12">
                    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong>Edição</strong></div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Funcionário</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static"><?= $dados_est['id_clt'] . ' - ' . $dados_est['nome'] ?></p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tipo" class="col-sm-2 control-label">Motivo</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static"><?= $dados_est['tipo'] ?></p>
                                        <input type="hidden" name="tipo" id="tipo" value="<?= $dados_est['id_tipo'] ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="data_ini" class="col-sm-2 control-label">Data Inicial</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="data_ini" class="form-control validate[required]" id="data_ini" placeholder="dd/mm/aaaa" value="<?= $dados_est['data_ini_br'] ?>">
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="data_fim" class="col-sm-2 control-label">Data Final</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="data_fim" class="form-control" id="data_fim" placeholder="dd/mm/aaaa" value="<?= $dados_est['data_fim_br'] ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="obs" class="col-sm-2 control-label">Observação</label>
                                    <div class="col-sm-10">
                                        <textarea name="obs" class="form-control" id="obs" rows="3"><?= $dados_est['obs'] ?></textarea>
                                    </div>
                                </div>
                            </div><!-- /.panel-body -->
                            <div class="panel-footer text-right">
                                <input type="hidden" name="home" id="home" value="">
                                <input type="hidden" name="id_projeto" value="<?= $dados_est['id_projeto'] ?>">
                                <input type="hidden" name="id_clt" value="<?= $dados_est['id_clt'] ?>">
                                <input type="hidden" name="id_estabilidade" value="<?= $dados_est['id_estabilidade'] ?>"><!-- o id garante que será feito o update e não outro insert -->
                                <input type="hidden" name="nome" value="<?= $dados_est['nome'] ?>">
                                <button type="button" class="btn btn-default" onclick="window.history.back();"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>
                                <input type="submit" name="method" value="Salvar" class="btn btn-primary">
                            </div><!-- /.panel-footer -->
                        </div><!-- /.panel -->
                        <div id="resp-autalizar"><?php include 'tabela_estabilidade.php'; ?></div>
                    </form>
                </div><!-- /.col-xs-12 -->
                <div id="informacao" class="hidden"><?= $html_info ?></div>
            </div><!-- /.row -->
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js" type="text/javascript"></script>
        <script src="../../resources/js/main.js"></script>

        <script src="../../js/jquery.form.js"></script>
        <script src="js/editar.js" type="text/javascript"></script>
    </body>
</html>