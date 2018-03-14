<?php

session_start();
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
//    print "<script>location.href = '../../login.php?entre=false';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include ("AssocContas.class.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);

//// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'despesas';

$prosoft_projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='prosoft_projeto1' name='prosoft_projeto1' class='form-control validate[required,custom[select]]'");
$prosoft_projeto2 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='prosoft_projeto2' name='prosoft_projeto2' class='form-control validate[required,custom[select]]'");
$folha_projeto = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='folha_projeto' name='folha_projeto' class='form-control validate[required,custom[select]]'");
$prosoft_projeto4 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='prosoft_projeto4' name='prosoft_projeto4' class='form-control validate[required,custom[select]]'");
$prosoft_relatorio = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='prosoft_relatorio' name='prosoft_relatorio' class='form-control validate[required,custom[select]]'");

$global = new GlobalClass();
$objPlanoConta = new PlanoContasAssoc();

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$nome_pagina = "Plano de Contas Associação";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "form_associacao_despesa");
$breadcrumb_pages = array("Integração Sistema Prosoft"=>"index.php");

$OpcaoCtaEnt = $objPlanoConta->consultaentrada();
$OpcaoCta = $objPlanoConta->consultaentradaesaida();
$OpcaoCtas = $objPlanoConta->consultaplanodecontas();
$OpcaoHist = $objPlanoConta->consultahistorico();
$OpcaoTerceiros = $objPlanoConta->consultaterceiros();
$OpcaoFolha = $objPlanoConta->consultafolha1() 

?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão NFe</title>
        <link rel="shortcut icon" href="../favicon.png">
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
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><span class="glyphicon glyphicon-usd"></span> - Gestão Contabil<small> - Associação Plano de Contas Prosoft</small></h2>
                    </div>
                    <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>
                    <div role="tabpanel">
                        <ul class="nav nav-tabs nav-justified" role="tablist" style="margin-bottom:30px;">
                            <li role="presentation" class="<?= checkAba('despesas', $aba) ?>"><a href="#despesas" aria-controls="despesas" role="tab" data-toggle="tab">Despesas</a></li>
                            <li role="presentation" class="<?= checkAba('receitas', $aba) ?>"><a href="#receitas" aria-controls="receitas" role="tab" data-toggle="tab">Receitas</a></li>
                            <li role="presentation" class="<?= checkAba('folha', $aba) ?>"><a href="#folha" aria-controls="folha" role="tab" data-toggle="tab">Folha de Pagamento</a></li>
                            <li role="presentation" class="<?= checkAba('empresas', $aba) ?>"><a href="#empresas" aria-controls="empresas" role="tab" data-toggle="tab">Empresas</a></li>
                            <li role="presentation" class="<?= checkAba('visualizar', $aba) ?>"><a href="#visualizar" aria-controls="visualizar" role="tab" data-toggle="tab">Relatório</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?= checkAba('despesas', $aba) ?>" id="despesas">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form action="associacaocontas.php" method="post" name="form_associacao_despesa" id ="form_associacao_despesa" class="form-horizontal" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="prosoft_regiao1" class="col-lg-2 control-label">Setor</label>
                                                    <div class="col-lg-6">
                                                        <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='prosoft_regiao1' name='prosoft_regiao1' class='validate[required,custom[select]] form-control' data-for='prosoft_projeto1'"); ?>
                                                    </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="prosoft_projeto1" class="col-lg-2 control-label">Empresa</label>
                                                <div class="col-lg-6">
                                                    <?php echo $prosoft_projeto1; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Conta</label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="prosoft_despesaL" id="prosoft_despesaL">
                                                        <?php foreach ($OpcaoCta as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" class="text-sm text-center">
                                                                <?php echo $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Classificador</label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="prosoft_despesaP" id="prosoft_despesaP">
                                                        <?php foreach ($OpcaoCtas as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" class="text-sm text-center">
                                                                <?php echo $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-right panel-footer">                                     
                                                <input class="btn btn-default" type="submit"  name="associar_despesas" id="associar_despesas" value="Associar Despesa"/>                                 
                                            </div>
                                        </form> 
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane <?= checkAba('receitas', $aba) ?>" id="receitas">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form action="associacaocontas.php" method="post" name="form_associacao_receita" id ="form_associacao_receita" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="prosoft_regiao2" class="col-lg-2 control-label">Setor</label>
                                                <div class="col-lg-6">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='prosoft_regiao2' name='prosoft_regiao2' class='validate[required,custom[select]] form-control' data-for='prosoft_projeto2'"); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="prosoft_projeto2" class="col-lg-2 control-label">Empresa</label>
                                                <div class="col-lg-6">
                                                    <?php echo $prosoft_projeto2; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Conta</label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="prosoft_receitaL" id="C">
                                                        <?php foreach ($OpcaoCtaEnt as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" class="text-sm text-center">
                                                                <?php echo $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Classificador</label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="prosoft_receitaP" id="prosoft_receitaP">
                                                        <?php foreach ($OpcaoCtas as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" class="text-sm text-center">
                                                                <?php echo $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="text-right panel-footer">                                     
                                                <input class="btn btn-default" type="submit"  name="associar_receitas" id="associar_receitas" value="Associar Receita"/>                                 
                                            </div>
                                        </form>  
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane <?= checkAba('folha', $aba) ?>" id="folha">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form action="associacaocontas.php" method="post" name="form_associacao_folha" id ="form_associacao_folha" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="folha_regiao" class="col-lg-2 control-label">Setor</label>
                                                <div class="col-lg-6">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='folha_regiao' name='folha_regiao' class='validate[required,custom[select]] form-control' data-for='folha_projeto'"); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="folha_projeto" class="col-lg-2 control-label">Projeto</label>
                                                <div class="col-lg-6">
                                                    <?php echo $folha_projeto; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Conta</label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="folha_codigo" id="folha_codigo">
                                                        <?php foreach ($OpcaoFolha as $key => $value) { ?>
                                                        <option value="<?php echo $key; ?>"  data-="<?php echo $key; ?>" class="text-sm text-center">
                                                                <?php echo $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Classificador</label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="folha_prosoft" id="folha_prosoft">
                                                        <?php foreach ($OpcaoCtas as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" class="text-sm text-center">
                                                                <?php echo $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Historico</label>
                                                <div class="col-lg-6">
                                                    <select class="form-control" name="folha_prosoft" id="folha_prosoft">
                                                        <?php foreach ($OpcaoHist as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" class="text-sm text-center">
                                                                <?php echo $value ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Tipo</label>
                                                <div class="col-sm-1">
                                                    <div class="radio">
                                                        <label><input name="folha_tipo" id="folha_tipo" type="radio" value="D"> Débito</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                    <div class="radio">
                                                        <label><input name="folha_tipo" id="folha_tipo" type="radio" value="C"> Crédito</label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-right panel-footer">                                     
                                                <input class="btn btn-default" type="submit"  name="associar_folha" id="associar_folha" value="Associar"/>
                                            </div>
                                        </form>  
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane <?= checkAba('empresas', $aba) ?>" id="empresas">
                                <div class="panel panel-default">
                                    <div class=" panel-body">
                                        <form action="associacaocontas.php" method="post" name="form_associacao_empresa" id ="form_associacao_empresa" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="prosoft_regiao4" class="col-lg-2 control-label">Setor</label>
                                                <div class="col-lg-6">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='prosoft_regiao4' name='prosoft_regiao4' class='validate[required,custom[select]] form-control' data-for='prosoft_projeto4'"); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="prosoft_projeto4" class="col-lg-2 control-label">Instituto Lagos</label>
                                                <div class="col-lg-6">
                                                    <?php echo $prosoft_projeto4; ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Prosoft</label>
                                                <div class="col-lg-2">
                                                    <input class="form-control text text-center" type="text" id="empresaprosoft" name="empresaprosoft" maxlength="4" />
                                                </div>
                                            </div>
                                            <div class="text-right panel-footer">                                     
                                                <input class="btn btn-default" type="submit"  name="associar_empresas" id="associar_empresas" value="Associar Empresa"/>
                                            </div>
                                        </form>  
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane <?= checkAba('visualizar', $aba) ?>" id="visualizar">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <form action="finan_plano_contas_rel.php" method="post" name="form_associacao_relatorio" id ="form_associacao_relatorio" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="regiao5" class="col-lg-2 control-label">Região</label>
                                                <div class="col-lg-5">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao5' name='regiao5' class='validate[required,custom[select]] form-control' data-for='prosoft_relatorio'"); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="prosoft_relatorio" class="col-lg-2 control-label">Projeto</label>
                                                <div class="col-lg-5">
                                                    <?php echo $prosoft_relatorio; ?>
                                                </div>
                                            </div>
                                            <div class="text-right panel-footer"> 
                                                <button type="submit" value="Listar" name="relatorio" id="relatorio"> Listar</button>
                                            </div>
                                        </form>  
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>        
                    <div id="resposta"></div>
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
        <script src="../js/prestacaocontas.js" type="text/javascript"></script>
        <style>
            .legends{
                font-size: 0.9em;
                color: silver;
            }
        </style>
    </body>
</html>
    