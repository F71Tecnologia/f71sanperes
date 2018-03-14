<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Empresas</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-adm.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-admin-header">
                        <h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO <small>- Empresas</small></h2>
                    </div>
                    <!--resposta de algum metodo realizado-->
                    <?php //echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                </div>
            </div>
            

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                    <li role="presentation" class="active"><a class="admin" href="#consulta" aria-controls="home" role="tab" data-toggle="tab" id="tab-consulta">Consulta</a></li>
                    <li role="presentation"><a class="admin" href="#cad" aria-controls="profile" role="tab" data-toggle="tab" id="tab-cadastro">Cadastro</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active fade in" id="consulta">
                        <?php require_once 'table_empresa.php'; ?>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="cad">
                        <?php require_once 'form_empresa.php'; ?>
                    </div>
                </div>

            
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/inicio.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
        <script src="js/form_empresa.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
    </body>
</html>