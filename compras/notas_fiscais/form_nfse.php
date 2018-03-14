<?php
//session_start();
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/BotoesClass.php");
include("../../classes/NFSeClass.php");
include("../../classes/BancoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];
$acoes = new Acoes();

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

// seta um valor para variável aba. usado para definir a aba aberta.
//$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'cadastrar';

$global = new GlobalClass();
$nfse = new NFSe();
$sqlcfop = mysql_query("SELECT * FROM nfe_cfop ORDER BY id_cfop");

$op_projetos = array('-1' => '« Selecione a Região »');
$op_prestadores = array('-1' => '« Selecione o Projeto »');

if (isset($_REQUEST['id_edit'])) {
    $id = $_REQUEST['id_edit'];
    $nfse_arr = $nfse->getNFSeById($id);

    $anexos = $nfse->getAnexos($nfse_arr['id_nfse']);

    $op_projetos = getProjetos($id_regiao);
    $op_prestadores = GlobalClass::carregaPrestadorByProjeto($nfse_arr['id_projeto'],'','true');
}

$projeto1 = montaSelect($op_projetos, $nfse_arr['id_projeto'], "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador1'");

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "35", "area" => "Gestão de Contratos", "ativo" => "NFSe", "id_form" => "form1");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão NFe</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <style>
            .ui-autocomplete { z-index:30 !important;}  
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container"> 
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-compras-header">
                        <h2><span class="glyphicon glyphicon-shopping-cart"></span> - Gestão de Contratos <small>- Notas Fiscais de Serviços</small></h2>
                    </div>
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->
                    <div class="bs-component" role="tablist">
                        <div role="tabpanel" class="tab-pane fade in active" id="sel-nfse">
                            <?php if (!isset($_REQUEST['id_edit'])) { ?>
                                <ul class="compras nav nav-tabs nav-justified" style="margin-bottom: 15px;">
                                    <?php if ($acoes->verifica_permissoes(111)) { ?>
                                        <li class="active"><a class="compras" href="#form-servico" data-toggle="tab"> Cadastro Manual</a></li>
                                        <li><a class="compras" href="#ler_arquivo_xml" data-toggle="tab"> Importação XML</a></li>
                                    <?php } if ($acoes->verifica_permissoes(112)) { ?>
                                        <!--<li><a class="compras" href="#conferencia" data-toggle="tab">NFSe Canceladas <span class="badge">*</span></a></li>-->
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                            <div id="myTabContent1" class="tab-content"> 
                                <?php
                                if ($acoes->verifica_permissoes(111)) {
                                    include 'nfse_cadastro.php';
                                    include 'nfse_importacao.php';
                                }
//                                if ($acoes->verifica_permissoes(112)) {
//                                    include 'nfse_conferencia.php';
//                                }
				
                                ?>
                            </div>
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
        <script src="js/form_NFSe.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>