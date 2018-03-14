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

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;

$objClassificador = new c_classificacaoClass();

$projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
$mes = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

if(isset($_REQUEST['projeto']) && isset($_REQUEST['visualizar'])) { 
    $arrayVerificacao = $objClassificador->verificar_associacao($projeto);
//    print_array($arrayVerificacao);
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Tipos Sem Associação", "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Relatórios Contábeis"=>"index.php"); ?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Tipos Sem Associação</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- Tipos Sem Associação</small></h2>
                    </div>
                    <form action="" method="post" name="form_lote" id="form_lote" class="form-horizontal top-margin hidden-print" enctype="multipart/form-data">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="projeto1" class="col-sm-2 text-sm control-label">Projeto</label>
                                    <div class="col-sm-4"><?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                                    <!--<label for="" class="col-sm-1 text-sm control-label">Exercício</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='input-sm validate[required,custom[select]] form-control'"); ?>
                                            <div class="input-group-addon">/</div>
                                            <?php echo montaSelect(anosArray(), $ano, "id='ano' name='ano' class='input-sm validate[required,custom[select]] form-control'"); ?>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button type="submit" id="criar" name="visualizar" value="Visualizar" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                    <?php if(isset($_REQUEST['projeto']) && isset($_REQUEST['visualizar']) && is_array($arrayVerificacao)) { ?>
                    <table class="table table-condensed table-hover table-striped text-sm valign-middle">
                        <thead>
                            <tr class="active">
                                <td colspan="3" class="text-center text-bold">TIPOS DE LANÇAMENTO SEM ASSOCIAÇÃO (<?=count($arrayVerificacao)?>)</td>
                            </tr>
                            <tr class="bg-primary">
                                <td class="text-center text-bold">TIPO</td>
                                <td class="text-bold">Descrição</td>
                                <td class="text-center text-bold">Qtd</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arrayVerificacao as $tipo => $value) { ?>
                            <tr>
                                <td class="text-center"><?= $tipo ?></td>
                                <td class=""><?= $value['nome'] ?></td>
                                <td class="text-center"><?= $value['qtd'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="alert alert-warning">Nenhum Tipo de Lancamento para O Projeto Selecionado!</div>
                    <?php } ?>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js"></script>
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
        <script src="js/classificacao.js" type="text/javascript"></script>
    </body>
</html>