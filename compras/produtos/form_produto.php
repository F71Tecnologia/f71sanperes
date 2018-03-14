<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/ProjetoClass.php");
include("../../wfunction.php");
include("../../classes/ProdutosClass.php");
include("../../classes/ProdutoFornecedorAssocClass.php");

$objProduto = new ProdutosClass();
$objProdFornecedor = new ProdutoFornecedorAssocClass();

$usuario = carregaUsuario();

$id_regiao = $usuario['id_regiao'];
$query = "SELECT regiao FROM regioes WHERE id_regiao = $id_regiao";
$arr_regiao = mysql_fetch_assoc(mysql_query($query));

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
// seta um valor para variável aba. usado para definir a aba aberta.
$aba = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'gestao';

$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "41", "area" => "Estoque", "ativo" => "Gestão de Produtos", "id_form" => "form-pedido");

$selectFornecedor = array('-1' => ' Selecione a Região ');

if (isset($_REQUEST['id'])) {
    $objProduto->setIdProd($_REQUEST['id']);
    $objProduto->getById();
    $objProduto->getRow();

    $objProdFornecedor->setIdProduto($_REQUEST['id']);
    $arrayAssoc = $objProdFornecedor->arrayAssoc();

    $query = "SELECT c_razao AS nome,REPLACE(REPLACE(REPLACE(c_cnpj,'.',''),'-',''),'/','') AS cnpj, c_cnpj FROM prestadorservico WHERE REPLACE(REPLACE(REPLACE(c_cnpj,'.',''),'-',''),'/','') = {$objProduto->getEmitCnpj(TRUE)}";
    $result = mysql_query($query);
    $row = mysql_fetch_assoc($result);
    $selectFornecedor = array($row['cnpj'] => "{$row['c_cnpj']} - {$row['nome']}");
}
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
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-estoque-header">
                        <h2><span class="fa fa-archive"></span> - ESTOQUE <small>- Gestão de Produtos</small></h2>
                    </div>

                    <form action="methods_prod.php" method="post" class="form-horizontal" id="form-cadastro" enctype="multipart/form-data">
                        <input type="hidden" name="home" id="home" value="">

                        <fieldset>
                            <legend>Cadastro</legend>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label for="" class="col-lg-2 control-label">Tipo de Produto</label>
                                        <div class="col-lg-2">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="tipo" class="tipo_produto validate[required]" id="optionsRadios1" value="1" <?= ($objProduto->getTipo() == 1) ? 'checked' : '' ?>>
                                                    Material Hospitalar
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="tipo" class="tipo_produto validate[required]" id="optionsRadios1" value="2" <?= ($objProduto->getTipo() == 2) ? 'checked' : '' ?>>
                                                    Medicamentos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($objProduto->getIdProd())) { ?>
                                        <input type="hidden" name="id_prod" id="id_prod" value="<?= $objProduto->getIdProd() ?>">
                                        <input type="hidden" name="fornecedor" id="cnpj_fornecedor" value="<?= $objProduto->getEmitCnpj(TRUE) ?>">

                                    <?php } ?>
                                    <div class="form-group">
                                        <label for="" class="col-lg-2 control-label" onblur="">Região</label>
                                        <div class="col-lg-9">
                                            <input type="text" id="regiao_nome" class="col-lg-2 form-control" value="<?= $id_regiao . " - " . $arr_regiao['regiao'] ?>" disabled>
                                            <input type="hidden" id='regiao' name='regiao' value="<?= $id_regiao ?>">

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="" class="col-lg-2 control-label" onblur="">Fornecedor</label>
                                        <div class="col-lg-9">
                                            <?= montaSelect($selectFornecedor, $objProduto->getEmitCnpj(TRUE), 'class="col-lg-2 form-control validate[required,custom[select]]" name="fornecedor" id="fornecedor"'); ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-lg-2 control-label">Código do Produto</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control validate[required]" id="cProd" name="cProd" placeholder="" value="<?= $objProduto->getCProd() ?>">
                                            <p class="help-block">Código informado pelo fornecedor</p>
                                        </div>
                                        <label for="inputEmail3" class="col-lg-2 control-label">EAN</label>
                                        <div class="col-lg-4">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="cEAN" name="cEAN" placeholder="" value="<?= $objProduto->getCEAN() ?>">
                                                <span class="input-group-addon">
                                                    <i class="glyphicon glyphicon-barcode"></i>
                                                </span>
                                            </div>
                                            <p class="help-block">Código de Barra</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-lg-2 control-label">Descrição do Produto</label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control validate[required]" id="xProd" name="xProd" placeholder="" value="<?= $objProduto->getXProd() ?>">
                                            <!--<p class="help-block">Código informado pelo fornecedor.</p>-->
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-lg-2 control-label">NCM</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="NCM" name="NCM" placeholder="" value="<?= $objProduto->getNCM() ?>">
                                            <!--<p class="help-block">Código informado pelo fornecedor.</p>-->
                                        </div>
                                        <!--</div><div class="form-group">-->
                                        <label for="inputEmail3" class="col-lg-2 control-label">Código EX da TIPI</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="EXTIPI" name="EXTIPI" placeholder="" value="<?= $objProduto->getEXTIPI() ?>">
                                            <!--<p class="help-block">Código informado pelo fornecedor.</p>-->
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-lg-2 control-label">Unidade Comercial</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="NCM" name="uCom" placeholder="" value="<?= $objProduto->getUCOM() ?>">
                                            <!--<p class="help-block">Código informado pelo fornecedor.</p>-->
                                        </div>
                                        <!--</div><div class="form-group">-->
                                        <!--                                        <label for="inputEmail3" class="col-lg-2 control-label">Valor Comercial</label>
                                                                                <div class="col-lg-4">
                                                                                    <div class="input-group">
                                                                                        <span class="input-group-addon">R$</span>
                                                                                        <input type="text" class="form-control validate[required]" id="vUnCom" name="vUnCom" placeholder="" value="<?= number_format($objProduto->getVUnCom(), 2, ',', '.') ?>">
                                                                                    </div>
                                                                                    <p class="help-block">Código informado pelo fornecedor.</p>
                                                                                </div>-->
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-lg-2 control-label">EAN Tributável</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="cEANTrib" name="cEANTrib" placeholder="" value="<?= $objProduto->getCEANTrib() ?>">
                                            <!--<p class="help-block">Código informado pelo fornecedor.</p>-->
                                        </div>
                                        <!--</div><div class="form-group">-->
                                        <label for="inputEmail3" class="col-lg-2 control-label">Unidade Tributável</label>
                                        <div class="col-lg-4">
                                            <input type="text" class="form-control" id="uTrib" name="uTrib" placeholder="" value="<?= $objProduto->getUTrib() ?>">
                                            <!--<p class="help-block">Código informado pelo fornecedor.</p>-->
                                        </div>
                                    </div>
                                </div><!-- /.panel-body -->

                                <table class="table table-striped table-condensed" id="tab_forncecedor_produto_assoc">
                                    <thead>
                                        <tr>
                                            <th style="width:65%">Projeto com Contrato Ativo</th>
                                            <th style="width:30%">Valor Comercial</th>
                                            <th style="width:5%">&thinsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($arrayAssoc) > 0) { ?>
                                            <?php foreach ($arrayAssoc as $value) { ?>
                                                <tr>
                                                    <td>
                                                        <?= $value['projeto_nome'] ?>
                                                        <input type="hidden" name="id_assoc[<?= $value['id_assoc'] ?>]" value="<?= $value['id_assoc'] ?>">
                                                        <input type="hidden" name="id_fornecedor[<?= $value['id_assoc'] ?>]" value="<?= $value['id_fornecedor'] ?>">
                                                    </td>

                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-addon hidden-xs">R$</span>
                                                            <input type="text" class="form-control money input-sm valor" name="valor[<?= $value['id_assoc'] ?>]" value="<?= number_format($value['valor_produto'], 2, ',', '.') ?>">
                                                        </div>
                                                    </td>
                                                    <td class="text-right">
                                                        <button class="btn btn-danger btn-sm excluir" type="button" data-id="<?= $value['id_assoc'] ?>"><i class="fa fa-trash"</button>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="3" class="info text-info text-center"><i class="fa fa-info-circle"></i> Selecione um <strong>Tipo de Produto</strong> e um <strong>Fornecedor</strong> para exibir os projetos.</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="panel-footer text-right">
                                    <button type="button" class="btn btn-default" onclick="window.history.back();"><i class="fa fa-reply"></i> Voltar</button>
                                    <?php if (empty($objProduto->getIdProd())) { ?>
                                        <button type="reset" class="btn btn-warning"><i class="fa fa-eraser"></i> Limpar</button>
                                    <?php } ?>
                                    <button type="submit" name="cadastro-salvar" value="Cadastrar" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Salvar</button>
                                </div>
                            </div><!-- /.panel-default -->
                        </fieldset>
                        <div id="resp-cadastro"></div>
                    </form>

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
        <script src="js/form_produto.js" type="text/javascript"></script>
    </body>
</html>