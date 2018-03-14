<?php
include_once("../conn.php");
include_once("../wfunction.php");
include_once("../classes/PermissoesClass.php");
include_once("../classes/global.php");
include_once("permissoes_usuario.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Sistema</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        
        <?php //include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-sistema-header"><h2><span class="fa fa-users"></span> - PERMISSÕES DE FUCIONÁRIOS</h2></div>
                    <div class="bs-component">
                        <ul class="nav nav-tabs" style="margin-bottom: 15px;">
                            <li class="active"><a href="#master" data-toggle="tab">MASTER</a></li>
                            <li class=""><a href="#regioes" data-toggle="tab">REGIÕES</a></li>
                            <li class=""><a href="#acoes_botoes" data-toggle="tab">AÇÕES/BOTÕES</a></li>
                        </ul>
                        <div id="myTabContent" class="tab-content">
                            <div class="tab-pane fade active in" id="master">
                                <?php print_r($master); ?>                                
                            </div>
                            <div class="tab-pane fade in" id="regioes">regioes</div>
                            <div class="tab-pane fade in" id="acoes_botoes">açoes</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script>
            $(function() {

            });
        </script>
    </body>
</html>






