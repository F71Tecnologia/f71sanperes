<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/BotoesClass.php");
include_once("../../classes/c_planodecontasClass.php");

$usuario = carregaUsuario();  
$id_regiao = $usuario['id_regiao'];

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objPlanoContas = new c_planodecontasClass();

$arrayProjetos = getProjetos($usuario['id_regiao']);

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Implantar Plano de Contas", "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Plano de Contas" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Importar Plano de Contas</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
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
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- Implantar Plano de Contas (SPED)</small></h2>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body div-lista-filtro">
                            <legend>Selecionar contas que serão implantadas</legend>
                            <div class="form-group form-horizontal">
                                <div class="col-sm-4">
                                    <button class="btn btn-primary btn-sm" id="exibe-contas"><i class="fa fa-sort-amount-asc"></i> Padrão SPED</button>
                                    <input type="hidden" name="id_projeto" id="id_projeto" class="form-control" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="panel-body border-t div-lista-planos hidden" id="lista-planos"></div>
                        <div class="panel-footer div-lista-planos text-right hidden">
                            <button class="btn btn-default btn-sm back" data-show="div-lista-filtro"><i class="fa fa-reply-all"></i> Voltar</button>
                            <button class="btn btn-primary btn-sm" id="exibe-contas-editavel">Próximo <i class="fa fa-angle-double-right"></i></button>
                        </div>
                        <div class="panel-body border-t div-lista-planos-editavel hidden" id="lista-planos-editavel"></div>
                        <div class="panel-footer div-lista-planos-editavel text-right hidden">
                            <button class="btn btn-default btn-sm back" data-show="div-lista-planos"><i class="fa fa-reply-all"></i> Voltar</button>
                            <button class="btn btn-primary btn-sm" id="implatacao_planodecontas_salvar"><i class="fa fa-save"></i> Implantar</button>
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
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/implantar_planodecontas.js" type="text/javascript"></script>
    </body>
</html>