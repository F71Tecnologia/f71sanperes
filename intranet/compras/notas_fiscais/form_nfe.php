<?php
//error_reporting(E_ALL);

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFeClass.php");
include("../../classes/ContabilFornecedorClass.php");
include("../../classes/pedidosClass.php");
include("../../classes/Class.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'cadastrar';

$projeto = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador3'");

$global = new GlobalClass();
$pedido = new pedidosClass();

//$fornecedor = $pedido->pesquisafornecedor(); //($_REQUEST['regiao'], $_REQUEST['projeto']);
$dados = array(
    'id_prestador' => $_REQUEST['prestador']
);

$consultapedido1 = $pedido->pedidosAbertos($dados);
$consultapedido2 = $pedido->PedidosEnviados($dados);


$consultapedido = array_merge($consultapedido1, $consultapedido2);

$sqlcfop = mysql_query("SELECT * FROM nfe_cfop ORDER BY id_cfop");

function checkSel($Selecao1, $Selecao2) {
    return ($Selecao1 == $Selecao2) ? 'active' : '';
}

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "41", "area" => "Estoque", "ativo" => "NFe", "id_form" => "form1");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Compras e Contratos :: Notas Fiscais</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-estoque.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
            a.btn-show-colapsed, a.btn-show-colapsed:hover, a.btn-show-colapsed:active, a.btn-show-colapsed:visited {
                display:block; 
                text-decoration: none;
            }
            .table > tbody > tr > td {
                vertical-align: middle;
            }

        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-estoque-header">
                        <h2><span class="fa fa-archive"></span> - ESTOQUE <small>- Notas Fiscais de Produtos</small></h2>
                    </div>
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->
                    <div class="bs-component" role="tablist">
                        <div id="myTabContent1" class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="sel-nfe">
                                <ul class="nav nav-tabs nav-justified" style="margin-bottom: 15px;">
                                    <li class="active"><a href="#arquivoXML" data-toggle="tab">Recebimento de Notas</a></li>
                                    <li><a href="#visualizaXML" data-toggle="tab">Consulta de NFe Salvas </a></li>
                                </ul>
                                <div id="myTabContent" class="tab-content"> 
                                    <?php include 'nfe_lista_pedidos_abertos.php'; ?>
                                    <?php include 'nfe_consulta.php'; ?>
                                </div> 
                            </div> 
                        </div> 
                    </div>
                </div>  
            </div> 
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/financeiro/saida.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/form_NFe.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
    </body>
</html>