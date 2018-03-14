<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/SuporteClass.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$botoes = new BotoesClass();
$classDefaults = $botoes->classModulos;
$modulos = $botoes->getModulos();
$objSuporte = new SuporteClass();

if(isset($_REQUEST['criar_chamado'])){
    $objSuporte->cadSuporte();
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <title>:: Intranet :: Suporte</title>
        
        <link rel="shortcut icon" href="../../favicon.png" />
        
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema</h2></div>                                                                                      
            <form action="" method="post" id="form_suporte" class="form-horizontal top-margin1" enctype="multipart/form-data">
                
                <input type="hidden" name="home" id="home" value="" />
                
                <ul class="breadcrumb">
                    <li><a href="../../">Home</a></li>
                    <li><a href="javascript:;" data-key="1" data-nivel="../../" data-form="form_suporte" class="return_principal">Principal</a></li>
                    <li><a href="index.php">Suporte</a></li>
                    <li class="active">Cadastro de Chamado</li>
                </ul>
                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-dismissable alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <input type="hidden" id="msg" value="<?php echo $_SESSION['MESSAGE']; ?>" />
                
                <fieldset>
                    <legend>Novo Suporte</legend>
                    <div class="panel panel-default">                        

                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="assunto" class="col-lg-2 control-label">Prioridade</label>
                                    <div class="col-lg-9 text-left">
                                        <select class="form-control" id="select" name="prioridade">
                                            <option id="opt01_cadsuporte" value="1">Baixa</option>
                                            <option id="opt02_cadsuporte" value="2">Media</option>
                                            <option id="opt03_cadsuporte" value="3">Alta</option>
                                            <option id="opt04_cadsuporte" value="4">Urgente</option>                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Assunto</label>
                                    <div class="col-lg-9">  
                                        <input type="text" class="form-control validate[required]" name="assunto" id="assunto">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Mensagem</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control validate[required]" rows="3" id="textArea" name="mensagem"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label">Anexo</label>
                                    <div class="col-lg-10">
                                        <input type="checkbox" id="anexo_cad"> sim
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="mensagem" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-10">
                                        <div class="pixel-file-input" id="file_cadsuporte">
                                            <input type="file" id="styled-finputs-example" name="arquivo" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-primary" name="criar_chamado" id="cria_chamado">Criar Chamado</button>
                        </div>
                    </div>                   
                </fieldset>
            </form>
                   
            <button type="button" class="btn btn-default" id="volta_index"><span class="fa fa-reply"></span>&nbsp;&nbsp;Voltar</button>
            
            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/sistema/suporte.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form_suporte").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>