<?php
/*
 * CONTROLLER: eventos/intex.php 
 * TELA:       rh_eventos_principal.php
 */
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Eventos ::</title>

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

    </head>
    <body>

        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">

                <div class="col-lg-12">

                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Formulário de Evento</small></h2></div>

                    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" name="form1" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label">Funcionário:</label>
                                        <label class="col-lg-9 control-label text-left"><?= '(' . $clt . ') ' . $row_clt['nome'] ?></label>
                                        <input type="hidden" name="id_clt" id="id_clt" value="<?= $row_clt['id_clt'] ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group">
                                        <label for="evento" class="col-lg-2 control-label">Ocorrência:</label>
                                        <div id="cel_evento" class="col-lg-9">
                                            <p class="form-control-static"><?= $row_evento['nome_status'] ?></p>
                                            <input type="hidden" name="evento" id="evento" value="<?= $row_evento['cod_status'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div id="row_data" class="row">
                                    <div class="form-group">
                                        <!-- <?= $class ?> <-- tem que fazer alguma coisa com isso -->
                                        <label for="data" class="col-lg-2 control-label">Data da Ocorrência:</label>
                                        <div class="col-lg-4">
                                            <!--                                            <div class="input-group">
                                                                                            <input name="data" id="data" class="form-control data validate[required]">
                                                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                                        </div>-->
                                            <p class="form-control-static"><?= $row_evento['data_br']; ?></p>
                                            <input type="hidden" min="0" size="3" maxlength="3" name="data" id="data" value="<?= $row_evento['data_br'] ?>"/>
                                        </div>
                                    </div>
                                </div>

                                <div id="row_dias" class="row">
                                    <div class="form-group">
                                        <label for="dias" class="col-lg-2 control-label">Duração da Ocorrência:</label>
                                        <div class="col-lg-4">
                                            <!--                                            <div class="input-group">
                                                                                            <input name="dias" id="dias" class="form-control dias" type="number" min="0" <?= $required ?>>
                                                                                            <span class="input-group-addon">dias</span>
                                                                                        </div>-->
                                            <?php
                                            if ($row_evento['cod_status'] != 10) { // status 10 nao pode modificar esse campo 
                                                if (!empty($row_evento['data_retorno_final_br']) && $row_evento['data_retorno_final_br'] == "00/00/0000") { // se não estiver vazio não pode modificar
                                                    ?>
                                                    <input type="number" min="0" name="dias" id="dias" class="dias form-control" style="width:5em;" value="<?= $row_evento['dias'] ?>" class="validate[required]">
                                                    <?php
                                                } else {
                                                    echo $row_evento['dias'];
                                                    echo "<input type=\"hidden\" min=\"0\" size=\"3\" maxlength=\"3\" name=\"dias\" id=\"dias\" value=\"{$row_evento['dias']}\"/>";
                                                }
                                            } else {
                                                echo $row_evento['dias'];
                                                echo "<input type=\"hidden\" min=\"0\" size=\"3\" maxlength=\"3\" name=\"dias\" id=\"dias\" value=\"{$row_evento['dias']}\"/>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div id="row_retorno" class="row">
                                    <div class="form-group">
                                        <label for="data_retorno" class="col-lg-2 control-label">Data de Retorno:</label><!-- Nome Anterior: Retorno da Ocorrência -->
                                        <div class="col-lg-3">
                                            <!--                                            <div class="input-group">
                                                                                            <input name="data_retorno" id="data_retorno" class="form-control data" type="text" >
                                                                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                                        </div>-->
                                            <?php if ($row_evento['cod_status'] != 10) { // status 10 nao pode modificar esse campo   ?>
                                                <div class="input-group">
                                                    <input type="text" name="data_retorno" class="data form-control" id="data_retorno" value="<?= $row_evento['data_retorno_br'] ?>" class="validate[required]">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>
                                                <?php
                                            } else {
                                                echo $row_evento['data_retorno_br'];
                                                echo "<input type=\"hidden\" name=\"data_retorno\" id=\"data_retorno\" class=\"data form-control\" value=\"{$row_evento['data_retorno_br']}\"/>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>



                                <div id="row_obs" class="row">
                                    <div class="form-group">
                                        <label for="observacao" class="col-lg-2 control-label">Observação:</label>
                                        <div class="col-lg-9">
                                            <textarea name="observacao" id="observacao" class="form-control" rows="3"><?= $row_evento['obs'] ?></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /.panel-body -->

                            <div class="panel-footer text-right">
                                <input type="hidden" name="id_clt"  id="id_clt"  value="<?= $clt ?>" />
                                <input type="hidden" name="projeto" id="projeto" value="<?= $row_clt['id_projeto'] ?>" />
                                <input type="hidden" name="status_clt" id="status_clt" value="<?= $row_clt['status'] ?>" />
                                <input type="hidden" name="regiao"  id="regiao"  value="<?= $id_regiao ?>" />
                                <input type="hidden" name="pronto"  id="pronto"  value="1" />
                                <?php // if ($row_clt['status'] != 10) { ?>
                                <!--<button type="button" class="btn btn-default" id="novo_evento">Novo Evento</button>-->
                                <?php // } ?>
                                <button class="btn btn-default" type="button" onclick="window.history.back();"><span class="fa fa-reply"></span> Voltar</button>
                                <!--<a href="index.php" class="btn btn-default"><span class="fa fa-reply"></span> Voltar</a>-->
                                <input type="submit" class="btn btn-primary" value="Concluir">
                            </div><!-- /.panel-footer -->
                        </div><!-- /.panel-defaut -->
                    </form>
                </div><!-- /.col-lg-12 -->

            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <!--<script src="../../js/ramon.js" type="text/javascript"></script>-->

        <!-- scripts da pagina -->
        <script src="../../resources/js/rh/eventos/edit_evento.js" type="text/javascript"></script>
    </body>
</html>