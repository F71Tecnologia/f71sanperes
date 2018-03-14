<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include("../../conn.php");
include("../../wfunction.php");

include("../../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Benefícios");
$breadcrumb_pages = array("Gestão de RH"=>"../../rh/principalrh.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de RH</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>- Benefícios</small></h2></div>
                    <div class="bs-component">
                        <div class="detalhes-modulo">
                            <div class="bs-glyphicons">
                                <div class="box-rh-list">
                                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 text-center">
                                        <a href="vale_refeicao/" class="text-center no-padding-vr">
                                            <div class="novo_ico thumbnail" style="height: 110px;">
                                                <div class="fa fa-cutlery"></div>
                                                <div class="display-table-cem">
                                                    <div class="text-bold text-center valign-middle vcenter text-uppercase" style="height: 50px;">Vale Refeição</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 text-center">
                                        <a href="vale_alimentacao/" class="text-center no-padding-vr">
                                            <div class="novo_ico thumbnail" style="height: 110px;">
                                                <div class="fa fa-shopping-basket"></div>
                                                <div class="display-table-cem">
                                                    <div class="text-bold text-center valign-middle vcenter text-uppercase" style="height: 50px;">Vale Alimentação</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 text-center">
                                        <a href="vale_transporte/" class="text-center no-padding-vr">
                                            <div class="novo_ico thumbnail" style="height: 110px;">
                                                <div class="fa fa-subway"></div>
                                                <div class="display-table-cem">
                                                    <div class="text-bold text-center valign-middle vcenter text-uppercase" style="height: 50px;">Vale Transporte</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    
                                    <?php if($_COOKIE['logado'] == 353){ ?>
                                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6 text-center">
                                        <a href="rh_movimentos_csv.php" class="text-center no-padding-vr">
                                            <div class="novo_ico thumbnail" style="height: 110px;">
                                                <div class="fa fa-download"></div>
                                                <div class="display-table-cem">
                                                    <div class="text-bold text-center valign-middle vcenter text-uppercase" style="height: 50px;">Importação de VT</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>     
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/rh/eventos/prorrogar_evento.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src="../../resources/js/rh/index.js" type="text/javascript"></script>
    </body>
</html>

