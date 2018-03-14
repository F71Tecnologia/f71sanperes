<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Intranet :: Rescis&atilde;o</title>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <!-- NOVO -->
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/datepicker.css" rel="stylesheet" media="screen">  
        <!-- FIM DO NOVO -->
        
        <script type="text/javascript" src="../../js/jquery-1.8.3.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../js/global.js"></script>
        
        <script type="text/javascript">

            $(function() {
            });
                
        </script>
    </head>
    <body class='novaintra' cz-shortcut-listen="true">
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - RESCISÃO</h2></div>                                                                                      
            <form action="controlador.php" method="post" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data">                
                <!--resposta de algum metodo realizado-->
                <?php var_dump($clt_rescisao); ?>
                <fieldset>
                    <legend><?= $clt_rescisao->id_clt.' - '.$clt_rescisao->nome; ?></legend>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="diastrab" class="col-lg-2 control-label">
                                        <div class="thumbnail" style="width: 82px; float: right;">
                                                <img src="<?= $objClt->getFoto('../../'); ?>" alt="<?= $clt_rescisao->nome; ?>" />
                                        </div>
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" id="funcionario"  disabled="disabled"  name="funcionario" value="<?= $clt->id_clt.' - '.$clt->nome; ?>">
                                            <br>
                                        <input type="text" class="form-control" id="funcionario"  disabled="disabled"  name="funcionario" value="<?= $clt->id_curso.' - '.$clt->nome_curso; ?>">
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="panel-footer text-right">                            
                            <input type="submit" class="btn btn-primary" id="cadastrar" />
                            <input type="hidden" name="acao" id="acao" value="calcular_rescisao" />
                            <input type="hidden" name="id_clt" id="id_clt" value="<?= $id_clt ?>" />
                        </div>
                    </div>
                </fieldset>
            </form>
            <button type="button" class="btn btn-default" onclick="window.history.go(-1)" name="voltar"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>            
        </div>
    </body>
</html>