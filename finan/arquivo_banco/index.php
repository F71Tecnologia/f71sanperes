<?php
<<<<<<< HEAD
    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../login.php?entre=true';</script>";
    }

    include("../../conn.php");
    include("../../funcoes.php");
    include("../../wfunction.php");
    include("../../classes_permissoes/acoes.class.php");
    include("../../classes/BotoesClass.php");
    include("../../classes/BancoClass.php");
    include("../../classes/SaidaClass.php");
    include("../../classes/EntradaClass.php");
    include("../../classes/FinaceiroClass.php");
    include("../../classes/AbastecimentoClass.php");
    include("../../classes/ReembolsoClass.php");
    include("../../classes/NFSeClass.php");

    $acoes = new Acoes();
    $usuario = carregaUsuario();
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

    //CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
    $botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
    $botoesMenu = $botoes->getBotoesMenuModulo(3);


    $breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Principal");
=======
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../funcoes.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/BotoesClass.php");
include("../../classes/BancoClass.php");
include("../../classes/SaidaClass.php");
include("../../classes/EntradaClass.php");
include("../../classes/FinaceiroClass.php");
include("../../classes/AbastecimentoClass.php");
include("../../classes/ReembolsoClass.php");
include("../../classes/NFSeClass.php");
//include("../classes/FolhaClass.php");
//include("../classes/EventoClass.php");

$acoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if (isset($_REQUEST['filtra'])) {
    $projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;
    $data_ini = $_REQUEST['inicio'];
    $data_fin = $_REQUEST['final'];
    $arrayRetornaTrava = $objTrava->retornaTrava($projeto, $data_ini, $data_fin);
}



//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'],$dadosHeader['fullRootPath']);
$botoesMenu = $botoes->getBotoesMenuModulo(3);

//LISTA DE EVENTOS
//$objEvento = new Eventos();
//$listaEventos = $objEvento->listaEventos();
//$dadosEventos = $objEvento->getTerminandoEventos(date("Y-m-d"), $usuario['id_regiao'], null, null, 10);

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"4", "area"=>"Financeiro", "id_form"=>"form1", "ativo"=>"Principal");
//$breadcrumb_pages = array("Lista Projetos" => "ver.php");
>>>>>>> contabil

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Financeira</title>
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
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - FINANCEIRO</h2></div>
<<<<<<< HEAD
                    <div class="bs-component">
                        <?php include("bco_arquivo_retorno.php")?>
=======
                    <div class="panel panel-body">
                        <?php $sql
                        ?>
                        <div>
                            <table>
                                
                            </table>                            
                            
                        </div>
                        <div>
                            <table>
                                
                            </table>                            
                            
                        </div>
                        <div id="myTabContent" class="tab-content">
                            
                            
                            <div class="tab-pane active">
                                <?php include("bco_arquivo_retorno.php")?>
                            </div>
                        </div>
>>>>>>> contabil
                    </div>
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
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/financeiro/index.js"></script>
    </body>
</html>