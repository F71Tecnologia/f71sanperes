<?php

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
} 

include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/BotoesClass.php");
include_once("../../classes/NFeClass.php");
include_once("../../classes/BancoClass.php");
include_once("../../classes/global.php");
include_once("../../classes/c_planodecontasClass.php");
//include_once("../../classes/c_planodecontasClass.php");
include_once("../../classes/ContabilHistoricoClass.php");

$usuario = carregaUsuario();  
$id_regiao = $usuario['id_regiao'];

$sqlProjetosRegiao = "SELECT id_projeto FROM projeto WHERE id_regiao = {$usuario['id_regiao']}";
$qryProjetosRegiao = mysql_query($sqlProjetosRegiao);
$inProjetos[] = 0;
while($rowProjetosRegiao = mysql_fetch_assoc($qryProjetosRegiao)){
    $inProjetos[] = $rowProjetosRegiao['id_projeto'];
}

$botoes = new BotoesClass("../../img_menu_principal/");
$icon = $botoes->iconsModulos;
$plano = new c_planodecontasClass();

$nivel = $plano->niveisContas(implode(', ', $inProjetos));

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'cadastrar';

$contb_projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='contbprojeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador1'");
$contb_projeto2 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='contbprojeto2' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador2'");
$contb_projeto3 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='contbprojeto3' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador3'");

$global = new GlobalClass();

function checkSel($Selecao1, $Selecao2) {
    return ($Selecao1 == $Selecao2) ? 'active' : '';
}

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$objHistorico = new ContabilHistoricoPadraoClass();

$objHistorico->listarHistoricos();
$optHistorico[-1]='Selecione';
while ($objHistorico->getRow()) {
    $optHistorico[$objHistorico->getIdHistorico()] = $objHistorico->getTexto();
}
//    $breadcrumb_config = array("nivel"=>"../../../", "key_btn"=>"38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo"=>"Associação de Contas");

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo" => "Cadastro, Alteração ...", "id_form" => "form_planocontas_empresa");
$breadcrumb_pages = array("Plano de Contas" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão Contabil</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>
        <?php include_once("../../template/navbar_default.php"); ?> 
        <div class="container">
	    <input type="hidden" id="IdPlanoConta" value="">
            <div class="row"> 
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><?php echo $icon['38']?> - Contabilidade <small>- Plano de Contas</small></h2>
                    </div>
                    <!--resposta de algum metodo realizado-->
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>                                
                    <!--<div role="tabpanel">-->
                    <div class="bs-component" role="tablist">
                        <div role="tabpanel" class="tab-pane fade in active" id="">
                            <?php include_once 'PlanocontaReferencia.php'; ?>
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
        <script src="js/planodecontas.js" type="text/javascript"></script>
    </body>
</html>