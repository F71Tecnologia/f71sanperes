<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../../conn.php");
include("../../../classes/global.php");
include("../../../classes/ProjetoClass.php");
include("../../../admin/prestadores/MunicipiosClass.php");
include("classes/AssocReceitaClass.php");
include("../../../classes/c_planodecontasClass.php");
include("../../../wfunction.php");

$objMunicipio = new MunicipiosClass();
$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$objAcesso = new c_planodecontasClass();

$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'gestao';

$ativo = '1';
$passivo = '2';
$global = new GlobalClass();
$objAssociar = new AssocReceitaClass();
$OpcaoAcesso = $objAcesso->getPlanoAcesso($_REQUEST['projeto']);

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$nome_pagina = "Associar Receitas da Prorrogação do Contrato";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "38", "area" => "Gest�o Contabil", "ativo" => $nome_pagina, "id_form" => "form_associacao_despesa");
$breadcrumb_pages = array("Associar" => "contas_assoc.php");

$OpcaoProvisao = array(
    '-1' => '',
    '1' => 'Apostilamento',
    '2' => 'Termo de Parceria',
    '3' => 'Termo Aditivo',
    '4' => 'Novo Conv�nio',
    '5' => 'Contrato de Gestão'
);

$OpcaoAcesso = $objAcesso->getPlanoAcesso($_REQUEST['projeto']);

$sqlPlanoContas = "SELECT * FROM contabil_planodecontas WHERE status = 1 AND id_projeto IN(0,{$_REQUEST['projeto']}) AND classificador < 3 AND classificacao = 'A' ORDER BY classificador";
$qryPlanoContas = mysql_query($sqlPlanoContas)or die(mysql_error());
//$optPlanoContas = "<option value=''>SELECIONE</option>";
$ops_contas['ativo'][-1] = '<< Selecione >>';
$ops_contas['passivo'][-1] = '<< Selecione >>';

while ($rowPlanoContas = mysql_fetch_assoc($qryPlanoContas)) {
    $id = substr($rowPlanoContas['classificador'], 0, 1);

    $tipo = ($id >= 2) ? 'passivo' : 'ativo';
    $ops_contas[$tipo][$rowPlanoContas['id_conta']] = $rowPlanoContas['classificador'] . ' - ' . $rowPlanoContas['descricao'];
}
$contaAtivo = montaSelect($ops_contas['ativo'], NULL, 'name="contabil_a" id="contabil_a" class="form-control"');
$contaPassivo = montaSelect($ops_contas['passivo'], NULL, 'name="contabil_p" id="contabil_p" class="form-control"');
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Receitas</title>

        <link rel="shortcut icon" href="../../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

    </head>
    <body>
<?php include("../../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><span class="glyphicon glyphicon-briefcase"></span> - Contabilidade <small>- <?php echo utf8_decode('Provisionar Receita do Contrato em Vigência ( Cliente )') ?></small></h2>
                    </div>
                    <div class="bs-component" role="tablist">
                        <form action="contas_assoc_controle.php" method="post" class="form-horizontal" id="form-cadastro" enctype="multipart/form-data">
                            <input type="hidden" name="home" id="home" value="">
                            <input type="hidden" name="projeto" id="projeto" value="<?= $_REQUEST['projeto'] ?>">
                            <input type="hidden" name="usuario" id="usuario" value="<?= $_COOKIE['logado'] ?>">
                            <fieldset>
                                <label class="label-control">ASSOCIAR</label>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="" class="col-lg-2 control-label">Documento</label>
                                            <div class="col-lg-8">
                                                <select class="form-control" name="finan" id="finan_despesa">
<?php foreach ($OpcaoProvisao as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>" class="text-sm text-uppercase">
                                                        <?php echo $value ?>
                                                        </option>
                                                        <?php } ?>
                                                </select>
                                            </div>
                                        </div>                                        
                                        <hr>
                                        <div class="form-group">
                                            <label for="" class="col-lg-2 control-label">Ativo</label>
                                            <div class="col-xs-8"><?= $contaAtivo ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-lg-2 control-label">Passivo</label>
                                            <div class="col-xs-8"><?= $contaPassivo ?></div>
                                        </div>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <button type="button" class="btn btn-default" onclick="window.history.back();"><i class="fa fa-reply"></i> Voltar</button>
<?php if (empty($objAssociar->getId())) { ?>
                                            <button type="reset" class="btn btn-warning"><i class="fa fa-eraser"></i> Limpar</button>
                                        <?php } ?>
                                        <button type="submit" name="associar-salvar" value="Associar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
                                    </div>                                    
                                </div>
                            </fieldset>
                            <div id="resp-cadastro"></div>
                        </form>
                    </div>
                </div>
            </div>

<?php include_once '../../../template/footer.php'; ?>

        </div><!-- container -->
        <script src="../../../js/jquery-1.10.2.min.js"></script>
        <script src="../../../js/jquery.form.js"></script>
        <script src="../../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../../js/global.js"></script>
        <script src="../../../resources/js/bootstrap.min.js"></script>
        <script src="../../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../../resources/js/main.js"></script>
        <script src="../../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/form_associacao.js" type="text/javascript"></script>
    </body>
</html>