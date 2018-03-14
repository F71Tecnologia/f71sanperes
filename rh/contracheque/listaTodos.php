<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include("../../classes/ProjetoClass.php");
include "../../classes/clt.php";
include("../../funcoes.php");
include('../../wfunction.php');
include("../../classes/ContrachequeClass.php");

$contraObj = new Contracheque();

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
// RECEBENDO VARIAVEIS

$id_folha = $_REQUEST['id_folha'];

// RECEBENDO VARIAVEIS
//-- ENCRIPTOGRAFANDO A VARIAVEL
$vai = encrypt("$id_regiao&todos&$id_folha");
$vai = str_replace("+", "--", $vai);



$listaTodos = $contraObj->listaTodos($id_folha);

//-- ---------------------------
// tudo que está aqui deve ir para uma classe ----------------------------------

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Contracheque Todos");
$breadcrumb_pages = array("Gestão de RH"=>"../", "Contracheque"=>"../contracheque/solicita2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Administração de Feriados</title>
        <link rel="shortcut icon" href="../../favicon.ico" />

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
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">

        <style>
            .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus{
                background-color: #F58634;
            }
            .nav-pills a{
                color: #F58634;
            }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">

                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Recibos de Pagamentos</small></h2></div>
                    <!--<h3>Contracheques <small>Recibos de Pagamentos</small></h3>-->
                    <!--p><a class="btn btn-default" href="index.php"><i class="fa fa-reply"></i> Voltar</a></p-->

                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->

            <div class="row">
                <?php foreach ($listaTodos as $itens) { ?>
                    <div class="col-lg-3">
<!--                        <div class="thumbnail">
                            <img src="../../imagens/icons/att-pdf.png" alt="...">
                            <div class="caption">
                                <h4 class="text-center">Contracheques de <?= $itens['maxini']+1 ?> à <?= $itens['maxfim'] ?></h4>
                                <p>Gerar contracheques de 0 à 50</p>
                                <p>
                                    <a href="geracontra_4.php?enc=<?= $vai ?>&ini=<?= $itens['maxini'] ?>" class="btn btn-default btn-block" role="button" target="_blank">
                                        Gerar
                                    </a>
                                </p>
                            </div> /.caption 
                        </div> /.thumbnail -->
                        
                        <div class="stat-panel">
                            <!--<a href="geracontra_4.php?enc=<?= $vai ?>&ini=<?= $itens['maxini'] ?>" class="stat-cell col-xs-12 bg-danger bordered no-border-vr no-border-l valign-middle text-center text-lg" target="_blank">-->
                            <a href="contra_cheque_oo.php?enc=<?= $vai ?>&ini=<?= ($itens['maxini'] == 0) ? '00' :$itens['maxini'] ?>" class="stat-cell col-xs-12 bg-danger bordered no-border-vr no-border-l valign-middle text-center text-lg" target="_blank">
                                <i class="fa fa-3x fa-file-pdf-o"></i>&nbsp;&nbsp;<br>
                                <?= $itens['maxini']+1 ?> à <?= $itens['maxfim'] ?>
                            </a>
                        </div>
                        
                    </div><!-- /.col-lg-3 -->
                <?php } ?>


            </div><!-- /.row -->
            <form action="#" method="post" id="form1">
                <input type="hidden" name="home" id="home" value="" />
            </form>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../resources/js/rh/contracheque/listaIndividual.php.js"></script>
    </body>
</html>
