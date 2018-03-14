<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br/><a href="../login.php">Logar</a>';
    exit;
}

error_reporting(E_ALL);

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include("../../classes/BotoesClass.php");
include("../../classes/ContabilLancamentoClass.php");
include("../classes/ContabilTravaClass.php");



$usuario = carregaUsuario(); // carrega dados do usuário
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


$objLancamento = new ContabilLancamentoClass();
$objTrava = new ContabilTravaClass();

if (isset($_REQUEST['travar'])) {
    print_array($_REQUEST);
    $objTrava->setPeriodo(substr($_REQUEST['trava'],0,6)); 
    $objTrava->setIdProjeto(substr($_REQUEST['trava'],6,4));
   
    $arrayTravar = $objTrava->insert();
}

if (isset($_REQUEST['filtra'])) {
    $projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
    $data_ini = $_REQUEST['inicio'];
    $data_fin = $_REQUEST['final'];
    $arrayRetornaTrava = $objTrava->retornaTrava($projeto, $data_ini, $data_fin);
}

//if (isset($_REQUEST['destravar'])) {
//    $objTrava->setIdTrava($_REQUEST['des_travar']);
//    $arrayTravar = $objTrava->deleta();
//}

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;
$method = (isset($_REQUEST['method'])) ? strtolower($_REQUEST['method']) : null;
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Contabilidade", "ativo" => "Lançamentos ", "id_form" => "form-lancamentos-contabil");
?>


<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" conent="width=device-width, initial-scale=1">

        <title>:: Intranet :: Contabilidade</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <!--<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">-->
    </head>    
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row" style="padding-bottom:15px;">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header"><h2><?php echo $icon['38'] ?> - CONTABILIDADE <small>- Trava Contábil </small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="contabil">
                            <form method="post" class="form-horizontal" id="form-trava-contabil">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="" class="col-sm-2 text-sm control-label">Projeto</label>
                                                    <div class="col-md-10"><?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='form-control input-sm validate[required,custom[select]]'") ?></div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group text-sm">
                                                    <label for="" class="col-sm-3 text-sm control-label">Exercício</label>
                                                    <div class="input-group col-md-8">
                                                        <div class="input-group-addon control-label text-sm">Início</div>
                                                        <input type="text" id='inicio' name='inicio' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $data_ini ?>">
                                                        <div class="input-group-addon control-label text-sm">Final</div>
                                                        <input type="text" id='final' name='final' class='text-sm text-center data validate[required,custom[select]] form-control' value="<?= $data_fin ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-footer">
                                        <div class="row">
                                            <div class="col-md-12 text-right">
                                                <button type="submit" id="filtra" name="filtra" value="" class="btn btn-info btn-sm"><i class="fa fa-check"> Filtrar</i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($_REQUEST['filtra'])) { ?>
                                    <div class="row">
                                        <?php foreach ($arrayRetornaTrava as $key => $value) {
                                            if (!empty($value[id_trava])) { ?>
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="thumbnail">
                                                        <div id="<?= $value[id_trava] ?>" class="caption bg-danger text-center">
                                                            <i class="text-danger">Período com trava</i>                                                                
                                                            <h3 class="text-danger"><?= substr($value['indice'], 4, 2) . ' / ' .substr($value['indice'], 0, 4) ?></h3>
                                                            <p>
                                                                <button type="button" class="btn btn-default des_travar" id="" name="" data-id_trava="<?= $value[id_trava] ?> ">
                                                                    <i class="fa fa-lock text-danger text-bold"> Destravar </i>
                                                                </button>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="thumbnail">
                                                        <div id="<?= $value[indice] ?>" class="caption bg-warning text-center">
                                                            <i class="text-warning">Período sem trava</i>                                                                
                                                            <h3 class="text text-warning"><?= substr($value['indice'], 4, 2) . ' / ' .substr($value['indice'], 0, 4) ?></h3>
                                                            <p>
                                                                <button type="button" class="btn btn-default travar" name="" id="" data-travar_periodo="<?= $value['indice'] ?>" data-travar_projeto="<?= $projeto ?>">
                                                                    <i class="fa fa-unlock text-default text-bold" aria-hidden="true"> Travar </i>
                                                                </button>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </form>
                        </div>
                    </div>                            
                </div>
            </div>
            <?php include("../../template/footer.php"); ?>
        </div><!-- /.container -->

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/trava.js"></script>
    </body>
</html>
