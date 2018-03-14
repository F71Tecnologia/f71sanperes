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

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => "Tipos Sem Associação", "id_form" => "frmplanodeconta");
?>
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
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- Impostos </small></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                <form action="controle_impostos.php" class="form-horizontal" method="post" id="form_consulta">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 control-label">Região</label>
                                <div class="col-sm-9">
                                    <?= montaSelect(getRegioes(), $id_regiao, 'name="id_regiao" id="id_regiao" class="form-control"'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 control-label">Projeto</label>
                                <div class="col-sm-9">
                                    <?= montaSelect(array('-1'=>'« Selecione a Região »'), null, 'name="id_projeto" id="id_projeto" class="form-control"'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-primary" name="method" value="consultar_prestadores"><i class="fa fa-filter"></i> Filtrar</button>
                        </div>
                    </div>

                    <div class="panel panel-default hidden" id="tabela_prestadores"></div>
                </form>
            </div><!--  /.col-sm-12 -->
            </div><!--  /.row -->
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
        <script src="js/index.js" type="text/javascript"></script>
    </body>
</html>