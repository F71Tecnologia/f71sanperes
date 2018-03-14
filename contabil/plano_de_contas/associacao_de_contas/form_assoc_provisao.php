<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../../conn.php");
include("../../../classes/global.php");
include("../../../classes/ProjetoClass.php");
include("../../../admin/prestadores/MunicipiosClass.php");
include("classes/AssocProvisaoClass.php");
include("../../../ContabilAssocFolhaProvisaoClass.php");
include("../../../classes/c_planodecontasClass.php");
include("../../../wfunction.php");

$objMunicipio = new MunicipiosClass();
$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$objAcesso = new c_planodecontasClass();

$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'gestao';

$passivo = '2';     // classificação = 2
$resultado = '3';   // classificação >= 3 
$global = new GlobalClass();
$objAssociar = new AssocProvisaoClass();
$OpcaoAcesso = $objAcesso->getPlanoAcesso($_REQUEST['projeto']);

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$nome_pagina = "Associar Contas da Provisão";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "form_associacao_despesa");
$breadcrumb_pages = array("Associar" => "contas_assoc.php");

$OpcaoProvisao = array(
    '-1' => 'Selecione',
    '1' => 'Aviso',
    '2' => 'Multa 50%',
    '3' => 'Férias',
    '4' => 'Férias 1/3',
    '5' => '13º Salário',
    '6' => 'Lei 12506',
    '7' => 'FGTS',
    '8' => 'PIS',
    '9' => 'RAT',
    '10' => 'INSS',
    '11' => 'OUTROS',
);

// $OpcaoFornecedor = $objAssociar->consultafornecedor($_REQUEST['projeto']);
$OpcaoAcesso = $objAcesso->getPlanoAcesso($_REQUEST['projeto']);

$sqlPlanoContas = "SELECT * FROM contabil_planodecontas WHERE id_projeto IN(0,'{$_REQUEST['projeto']}') AND  status = 1  AND classificador >= 2 AND classificacao = 'A' ORDER BY classificador";
$qryPlanoContas = mysql_query($sqlPlanoContas)or die(mysql_error());
//$optPlanoContas = "<option value=''>SELECIONE</option>";
$ops_contas['passivo'][-1] = '<< Selecione >>';
$ops_contas['resultado'][-1] = '<< Selecione >>';

while ($rowPlanoContas = mysql_fetch_assoc($qryPlanoContas)) {
    $id = substr($rowPlanoContas['classificador'], 0, 1);

    $tipo = ($id > 2) ? 'resultado' : 'passivo';
    $ops_contas[$tipo][$rowPlanoContas['id_conta']] = $rowPlanoContas['classificador'] . ' - ' . $rowPlanoContas['descricao'];
}
$contaPassivo = montaSelect($ops_contas['passivo'], NULL, 'name="contabil" id="contabil" class="form-control"');
$contaDRE = montaSelect($ops_contas['resultado'], NULL, 'name="contabil_dre" id="contabil_dre" class="form-control"');
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Rubricas</title>

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
                        <h2><span class="glyphicon glyphicon-briefcase"></span> - Contabilidade <small>- Provisionar Folha de Rescisão</small></h2>
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
                                            <label for="" class="col-lg-2 control-label">Rubrica</label>
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
                                            <label for="" class="col-lg-2 control-label">Passivo</label>
                                            <div class="col-xs-8"><?= $contaPassivo ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="col-lg-2 control-label">D R E</label>
                                            <div class="col-xs-8"><?= $contaDRE ?></div>
                                        </div>
                                    </div>
                                    <div class="panel-footer text-right">
                                        <button type="button" class="btn btn-default" onclick="window.history.back();"><i class="fa fa-reply"></i> Voltar</button>
                                        <?php if (empty($objAssociar->getIdProvisao())) { ?>
                                            <button type="reset" class="btn btn-warning"><i class="fa fa-eraser"></i> Limpar</button>
                                        <?php } ?>
                                        <button type="submit" name="provisao-salvar" value="Provisionar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
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