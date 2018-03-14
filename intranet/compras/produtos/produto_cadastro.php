<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/ProjetoClass.php");
include("../../classes/NFeClass.php");
include("../../wfunction.php");

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'gestao';

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador1'");
$projeto2 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto2' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador2'");
$projeto3 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto3' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador3'");
$projeto4 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto4' name='projeto' class='form-control validate[required,custom[select]]' data-for='prestador4'");
$global = new GlobalClass();

$selectFornecedor[-1] = 'Selecione';
foreach ($forn as $value) {
    $selectFornecedor[$value['id_fornecedor']] = $value['razao'];
}

function checkAba($aba1, $aba2) {
    return ($aba1 == $aba2) ? 'active' : '';
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "41", "area" => "Estoque", "ativo" => "Gestão de Produtos", "id_form" => "form-pedido");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-estoque-header">
                        <h2><span class="fa fa-archive"></span> - ESTOQUE <small>- Gestão de Produtos</small></h2>
                    </div>
                    <div role="tabpanel">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-justified" role="tablist" style="margin-bottom:30px;">
                            <!-- quando for ativar a parte de importação de produtos, descomentar as duas linhas abaixo -->
                            <!--<li role="presentation" class="<?= checkAba('gestao', $aba) ?>"><a href="#gestao" aria-controls="gestao" role="tab" data-toggle="tab">Gestão</a></li>-->
                            <!--<li role="presentation" class="<?= checkAba('importar', $aba) ?>"><a href="#importar" aria-controls="importar" role="tab" data-toggle="tab">Importação</a></li>-->
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane <?= checkAba('gestao', $aba) ?>" id="gestao">
                                <form action="methods_prod.php" method="post" class="form-horizontal" id="form-consulta">
                                    <div class="panel panel-default">
                                        <!--<div class="panel-heading">Filtro</div>-->
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label">Tipo de Produto</label>
                                                <div class="col-lg-2">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="tipo" class="tipo_produto" id="optionsRadios1" value="2" checked>
                                                            Medicamentos
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="tipo" class="tipo_produto" id="optionsRadios1" value="1" >
                                                            Material Hospitalar
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="regiao" class="col-lg-2 control-label">Região</label>
                                                <div class="col-lg-4">
                                                    <?php echo montaSelect($global->carregaRegioes($usuario['id_master']), $regiaoR, "id='regiao' name='regiao' class='validate[required,custom[select]] form-control'"); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="" class="col-lg-2 control-label" onblur="">Fornecedor</label>
                                                <div class="col-lg-9">
                                                    <?= montaSelect(array('-1' => '« Selecione a Região »'), NULL, 'class="col-lg-2 form-control validate[required,custom[select]]" name="fornecedor" id="fornecedor"'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-footer text-right">
                                            <a class="btn btn-success" href="form_produto.php"><i class="fa fa-plus"></i> Novo Produto</a>
                                            <button type="submit" class="btn btn-primary" name="consultar" value="consultar"><i class="fa fa-search"></i> Consultar</button>
                                        </div>
                                    </div>

                                    <div id="dados_consulta" class="loading"></div>
                                </form>
                            </div>

                            <!-- quando for ativar a importacao de produtos -->
<!--                            <div role="tabpanel" class="tab-pane <?= checkAba('importar', $aba) ?>" id="importar">
                                <form action="methods_prod.php" method="post" class="form-horizontal" id="form-xml" enctype="multipart/form-data">
                                    <fieldset>
                                        <legend>Importação dos Itens da NFe</legend>  CADASTRO DE PRODUTOS 
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="nfe" class="col-lg-2 control-label text-right">XML da NFe</label>
                                                    <div class="col-lg-6">                             
                                                        <input type="file" name="nfe" id="nfe" class="form-control filestyle" data-buttonText=" Selecione Arquivo">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-footer text-right">
                                                <button type="submit" value="Importar" name="importar" id="xml-importar" data-status="false" class="btn btn-primary"><i class="fa fa-download"></i> Importar</button>
                                                <input type="hidden" name="aba" id="aba" value="importar">
                                                <input type="hidden" name="acao" id="acao" value="upload">
                                                <button type="reset" value="Cancelar" id="xml-cancelar" class="btn btn-warning"><i class="fa fa-undo"></i> Cancelar</button>
                                                <button type="submit" value="Salvar" name="salvar-xml" id="xml-salvar" class="btn btn-success" data-status="false" disabled><i class="fa fa-floppy-o"></i> Salvar</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div id="list-prod-import" class="loading"> </div>
                                </form>
                            </div>  /#importar  -->

                        </div>
                    </div>

                </div><!-- col-lg-12 -->

            </div><!-- row -->

            <?php include_once '../../template/footer.php'; ?>

        </div><!-- container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/produto_cadastro.js" type="text/javascript"></script>
    </body>
</html>