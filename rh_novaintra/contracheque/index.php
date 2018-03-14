<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include("../../classes/ProjetoClass.php");
include('../../wfunction.php');


$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$objProjeto = new ProjetoClass();

// lista de projetos (usado no menu esquerdo)
$projetosList = $objProjeto->getProjetos($id_regiao);

// define qual o projeto será exibido em tela
$projeto_atual = $projetosList[0]['id_projeto'];

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-oculto", "ativo"=>"Contracheque");
$breadcrumb_pages = array("Gestão de RH"=>"../");
// tudo que está aqui deve ir para uma classe ----------------------------------
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Contracheques</title>
        <link rel="shortcut icon" href="../../favicon.ico">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Contracheques</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <div class="col-lg-3 no-padding-hr">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <ul class="nav nav-pills nav-stacked">
                            <?php foreach ($projetosList as $value) { ?>
                                <li <?= ($value['id_projeto'] == $projeto_atual) ? "class=\"active\"" : "" ?>>
                                    <a href="#" class="menu-left rh" data-id-projeto="<?= $value['id_projeto'] ?>">
                                        <span class="pull-right rh"><i class="fa fa-chevron-right"></i></span>
                                        <?= $value['id_projeto'] . ' - ' . $value['nome'] ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div><!-- /.col-lg-3 -->
                </div><!-- /.col-lg-3 -->
            </div>
            <div class="col-lg-9 no-padding-hr">
                <div class="panel-body">
                    <form action="#" method="post" id="form-oculto">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <?php for ($i = date("Y") - 5; $i <= date("Y"); $i++) { ?>
                                <li class="<?= ($i == date("Y")) ? "active" : "" ?>"><a href="#<?= $i ?>" class="ano" role="tab" data-toggle="tab"><?= $i ?></a></li>
                            <?php } ?>
                        </ul>
                        <div id="listaContra"></div>
                        <input type="hidden" name="id_folha"  id="id_folha">
                        <input type="hidden" name="home" id="home" value="" />
                    </form>
                </div>
            </div><!-- /.row -->
            <div class="clear"></div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>

        <script src="../../resources/js/rh/contracheque/index.js"></script>
    </body>
</html>
