<?php
error_reporting(E_ALL); 

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");
include("../../classes/c_classificacaoClass.php");
include("../../classes/ContabilLancamentoClass.php");
include("../../classes/ContabilLoteClass.php");
require_once("../../classes/pdf/fpdf.php");


$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$id_master = $usuario['id_master'];

$query_master = "SELECT * FROM master WHERE id_master = $id_master";
$master = mysql_fetch_assoc(mysql_query($query_master));

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objClassificador = new c_classificacaoClass();

$projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
$mes = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

if (isset($_REQUEST['filtrar']) || isset($_REQUEST['filtra'])) {
   
    if($_REQUEST['filtra'] == 1) {
        $filtro = TRUE; 
    } elseif ($_REQUEST['filtra'] == 0) { 
        $filtro = FALSE; 
    } 
    
    $arrayClassificacao = $objClassificador->balancete($projeto, $mes, $ano, true, $filtro);
}

$nome_pagina = "Saldo";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina , "id_form" => "formSaldoPlanodeConta");
$breadcrumb_pages = array("Plano de Contas" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Implantar Saldo Inicial</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme_1.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include_once("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><?php echo $icon['38']?> - Contabilidade <small>- Ajuste de Saldo</small></h2>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body div-lista-filtro">
                            <div class="form-group form-horizontal">
                                <label class="col-sm-2 text-sm control-label">Projeto</label>
                                <div class="col-sm-8"><?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                            </div>
                        </div>
                        <div class="panel-footer div-lista-filtro text-right">
                            <button class="btn btn-primary btn-sm" id="exibe-contas"><i class="fa fa-filter"></i> Filtrar</button>
<!--                            <button class="btn btn-default btn-sm back" data-show="div-lista-planos"><i class="fa fa-reply-all"></i> Voltar</button>-->
                        </div>
                        <div class="panel-body border-t div-lista-planos hidden" id="lista-planos"></div>
                        <div class="panel-footer div-lista-planos text-right hidden">
                            <button class="btn btn-default btn-sm back" data-show="div-lista-filtro"><i class="fa fa-reply-all"></i> Voltar</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/implantar_planodecontas.js" type="text/javascript"></script>
    </body>
</html>