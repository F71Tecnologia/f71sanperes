<?php
// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$global = new GlobalClass();

$nome_pagina = 'Contas do Financeiro';
$breadcrumb_config = array("nivel" => "../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => $nome_pagina);
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <!--<div class="container-full over-x">-->
        <div class="container">
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - <?= $nome_pagina ?></small></h2></div>
            <?php if (isset($_GET['s'])) { ?><div class="alert alert-dismissable alert-success text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Saídas Geradas com Sucesso!</div><?php } ?>
            <?php if (isset($_GET['e'])) { ?><div class="alert alert-dismissable alert-danger text-bold"><button type="button" class="close" data-dismiss="alert">×</button>Erro: <?= $_GET['e'] ?>. Entre em contato com o suporte.</div><?php } ?>
            
            
            <div class="col-sm-12 no-padding">
                <a href="gestao_grupos/index.php">
                    <div class="col-lg-4 col-sm-6 pointer stat" >
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-primary darker"><!-- Success darker background -->
                                    <i class="fa fa-object-group bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Gestão de Grupos Financeiros</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
                <a href="gestao_subgrupos/index.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-success darker"><!-- Success darker background -->
                                    <i class="fa fa-object-ungroup bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Gestão de Subgrupos Financeiros</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
                <a href="gestao_contas/index.php">
                    <div class="col-lg-4 col-sm-6 pointer stat">
                        <div class="stat-panel">
                            <div class="stat-row">
                                <div class="stat-cell bg-warning darker"><!-- Success darker background -->
                                    <i class="fa fa-image bg-icon" style="font-size:60px;line-height:64px;height:64px;"></i><!-- Stat panel bg icon -->
                                    <span class="text-bg">Gestão de Contas Financeiros</span><br><!-- Big text -->
                                    <span class="text-sm"></span><!-- Small text -->
                                </div>
                            </div> <!-- /.stat-row -->
                        </div>
                    </div>
                </a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            
            <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="rel_notas_liberadas.js" type="text/javascript"></script>

    </body>
</html>
