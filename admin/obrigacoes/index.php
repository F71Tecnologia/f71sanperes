<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/regiao.php");
include("../../classes/ObrigacoesClass.php");
include('../../classes_permissoes/botoes.class.php');

$usuario = carregaUsuario();
$botao = new Botoes();

$objObrigacoes = new ObrigacoesClass();
$tiposObrigacoes = $objObrigacoes->getTipoObrigacoes();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"2", "area"=>"Administrativo", "id_form"=>"form1", "ativo"=>"Gestão de Obrigações");
$breadcrumb_pages = array("Principal" => "../index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de Obrigações</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/add-ons.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="all">
        <style>.bg-admin{padding: 10px 0 10px 0!important;}</style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-admin-header"><h2><span class="glyphicon glyphicon-cog"></span> - ADMINISTRATIVO<small> - Gestão de Obrigações</small></h2></div>
                </div>
            </div>
            
            <?php if($botao->verifica_permissao(95)){ ?>
            <div class="panel panel-default hidden-print">
                <div class="panel-footer text-right">
                    <a href="form_obrigacoes.php" class="btn btn-success text-uppercase"><i class="fa fa-plus"></i> Cadastrar Obrigações da Instituição</a>
                </div>
            </div>
            <?php } ?>
            
            <?php 
            foreach ($tiposObrigacoes as $tipo_obrigacao) { ?>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pointer margin_b5 tipo_obrig" data-toggle="tooltip" data-key="<?=$tipo_obrigacao['tipo_id']?>" title="<?=$tipo_obrigacao['tipo_nome']?>">
                    <div class="bg-admin col-xs-12"><!-- Small padding, without horizontal padding -->
                        <div class="col-xs-10 no-padding-r text-left text-sm text-bold visible-lg text-uppercase"><?= (strlen($tipo_obrigacao['tipo_nome']) > 36) ? substr($tipo_obrigacao['tipo_nome'], 0, 33).'...' : $tipo_obrigacao['tipo_nome']?></div>
                        <div class="col-xs-10 no-padding-r text-left text-sm text-bold visible-md text-uppercase"><?= (strlen($tipo_obrigacao['tipo_nome']) > 29) ? substr($tipo_obrigacao['tipo_nome'], 0, 26).'...' : $tipo_obrigacao['tipo_nome']?></div>
                        <div class="col-xs-10 no-padding-r text-left text-sm text-bold visible-sm text-uppercase"><?= (strlen($tipo_obrigacao['tipo_nome']) > 34) ? substr($tipo_obrigacao['tipo_nome'], 0, 31).'...' : $tipo_obrigacao['tipo_nome']?></div>
                        <div class="col-xs-10 no-padding-r text-left text-sm text-bold visible-xs text-uppercase"><?= $tipo_obrigacao['tipo_nome']?></div>
                        <div class="col-xs-2 text-right"><i class="fa fa-arrow-circle-down seta"></i></div><!-- Extra small text -->
                    </div>
                </div>
                <div class="col-lg-12 col-sm-12 col-xs-12 margin_b5 lista_obrig l<?=$tipo_obrigacao['tipo_id']?>" style="display: none;"></div>
            <?php } ?>
            <div class="clear"></div>
            <form id="editar_obrigacao" method="post"></form>
            <?php include("../../template/footer.php"); ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/administrativo/obrigacoes.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    </body>
</html>