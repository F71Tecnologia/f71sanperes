<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../wfunction.php');

$usuario = carregaUsuario();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Escalas");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Escalas</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        
        <div class="container">
            <form id="form1" method="post"><input type="hidden" name="home" id="home"></form>
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Escalas</small></h2></div>
                </div>
            </div>
            <div class="detalhes-modulo">
                <div class="bs-glyphicons">
                    <div class="box-rh-list">
                        <a class="bot_nv col-lg-2 col-md-4 col-sm-4 col-xs-6 valign-middle" href="acesso_escala.php" target="blank">
                            <img align="absmiddle" src="../../img_menu_principal/escala_trabalho.png"/>
                            <p>Cadastro Escala</p>
                        </a>
                        <a class="bot_nv col-lg-2 col-md-4 col-sm-4 col-xs-6 valign-middle" href="relatorio_acesso_restrito.php" target="blank">
                            <img align="absmiddle" src="../../img_menu_principal/rel_gerencial.png"/>
                            <p><!--Relatório -->Controle de Acesso</p>
                        </a>
                        <a class="bot_nv col-lg-2 col-md-4 col-sm-4 col-xs-6 valign-middle" href="relatorio_acesso_escala.php" target="blank">
                            <img align="absmiddle" src="../../img_menu_principal/rel_gerencial.png"/>
                            <p><!--Relatório -->Controle de Acesso Por Escala</p>
                        </a>
                        <!--a class="col-md-12 valign-middle btn btn-default text-center" href="relatorio_acesso_completo.php" target="blank">
                            <img align="absmiddle" src="../../img_menu_principal/rel_gerencial.png"/>
                            RELATÓRIO CONTROLE DE ACESSO POR COLABORADOR
                        </a-->
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</html>
