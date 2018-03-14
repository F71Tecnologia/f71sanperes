<?php

    include("../../conn.php");
    include("../../funcoes.php");
    include("../../wfunction.php");
    include("../../classes_permissoes/acoes.class.php");

    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../../login.php?entre=true';</script>";
    }
    $acoes = new Acoes();
    $usuario = carregaUsuario();
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

    $mes = ($_REQUEST['mes']) ? $_REQUEST['mes'] : date('m');
    $ano = ($_REQUEST['ano']) ? $_REQUEST['ano'] : date('Y');
    $competencia = "{$ano}-{$mes}-01";

    $nome_pagina = "Importação xml Oxxy";
    $breadcrumb_config = array("nivel" => "../../", "key_btn" => "4", "area" => "Financeiro", "id_form" => "form1", "ativo" => "$nome_pagina");
//$breadcrumb_pages = array("Principal" => "../");
    ?>
    <!DOCTYPE html>
    <html lang="pt">
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>:: Intranet :: <?= $nome_pagina ?></title>
            <link href="../../favicon.png" rel="shortcut icon" />
            <!-- Bootstrap -->
            <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
            <!--<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">-->
            <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
            <link href="../../resources/css/main.css" rel="stylesheet" media="all">
            <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
            <!--<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="all">-->
            <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
            <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        </head>
        <body>
    <?php include("../../template/navbar_default.php"); ?>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro  - <small><?= $nome_pagina ?></small></h2></div>
                        <form action="" method="post" id="form1" class="form-horizontal top-margin1 hidden-print" enctype="multipart/form-data" autocomplete="off">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <legend>Importação Manual:</legend>
                                    <div class="form-group">
                                        <div class="col-lg-2 hidden-print">
                                            <label class="label-control"> Data de importação:</label>
                                        </div>
                                        <div class="col-lg-2 hidden-print">
                                            <div><input name="data_importacao" id="data_importacao" type="text" class="form-control data"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer text-right">
                                    <span id="btImport" name="btImport" value="btImport" class="btn btn-success"><i class="fa fa-feed"></i> Importar</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
    <?php include('../../template/footer.php'); ?>
            </div>
            <script src="../../js/jquery-1.10.2.min.js"></script>
            <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
            <script src="../../resources/js/bootstrap.min.js"></script>
            <script src="../../resources/js/bootstrap-dialog.min.js"></script>
            <script src="../../js/jquery.validationEngine-2.6.js"></script>
            <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
            <script src="../../resources/js/main.js"></script>
            <script src="../../js/global.js"></script>


            <script>
                $(function () {


                    $('body').on('click', '#btImport', function () {
                        cria_carregando_modal();
                        var url = 'http://<?php echo $_SERVER['SERVER_NAME'] ?>/api.php?method=oxxy&data=' + $('#data_importacao').val();
                        console.log(url);
                        $.post(
                                url,
                                {
                                }, function (data) {
                            remove_carregando_modal();
                            bootAlert('Dados importados!', 'Sucesso!', function () {
                            }, 'success');
                        }
                        );
                    });
                });
            </script>
        </body>
    </html>