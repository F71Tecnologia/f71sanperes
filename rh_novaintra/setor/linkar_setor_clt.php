<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include("../../classes/SetorClass.php");

$usuario = carregaUsuario();
$hoje = new DateTime();

$setorObj = new SetorClass();

$setorObj->getSetor();
$arraySetor[''] = 'Selecione o Setor';
while($setorObj->getRowSetor()){
    $arraySetor[$setorObj->getIdSetor()] = $setorObj->getNome();
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Associar CLT ao Setor");
$breadcrumb_pages = array("Gestão de RH"=>"../../rh/principalrh.php", "Setor"=>"../setor");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Associar CLT ao Setor</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">

            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Associar CLT ao Setor</small></h2></div>
                </div>
            </div>
            <?php if(isset($_REQUEST['erro'])) { ?><div class="alert alert-danger">Nenhum Clt Selecionado!</div><?php } ?>
            <div class="row">
                <form action="action/action_linkar_setor_clt.php" method="post" class="form-horizontal" id="form">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading"></div><!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Projeto: </label>
                                    <div class="col-sm-4"><?= montaSelect(getProjetos($usuario['id_regiao']), null, 'name="projeto" id="projeto" class="form-control validate[required,custom[select]]"') ?></div>
                                    <label class="col-sm-1 control-label">Setor: </label>
                                    <div class="col-sm-4"><?= montaSelect($arraySetor, null, 'name="id_setor" id="id_setor" class="form-control validate[required,custom[select]]"') ?></div>
                                </div><!-- /.form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Função: </label>
                                    <div class="col-sm-4"><?= montaSelect($arrayCurso, null, 'name="curso" id="curso" class="form-control"') ?></div>
                                </div><!-- /.form-group -->
                            </div><!-- /.panel-body -->
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-sm-offset-1 col-sm-10" id="clts"></div><!-- /.col-sm-12 -->
                                </div><!-- /.form-group -->
                            </div><!-- /.panel-body -->
                            <div class="panel-footer text-right">
                                <input type="hidden" name="action" value="linkar">
                                <button type="submit" class="btn btn-primary" id="salvar"><i class="fa fa-save"></i> Salvar</button>
                            </div><!-- /.panel-footer -->
                        </div><!-- /.panel-info -->
                    </div>
                </form>
            </div><!-- /.row -->

            <?php include_once("../../template/footer.php"); ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/rh/setor/linkar_setor_clt.js"></script>
    </body>
</html>
