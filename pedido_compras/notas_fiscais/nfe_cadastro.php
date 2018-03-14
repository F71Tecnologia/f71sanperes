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

$global = new GlobalClass();
$objPedidos = new pedidosClass();

$arr_pedido = $objPedidos->consultaPedidoById($_REQUEST['id']);

$sqlcfop = mysql_query("SELECT * FROM nfe_cfop ORDER BY id_cfop");
$select_cfop[-1] = "Selecione";
while ($escolha = mysql_fetch_array($sqlcfop)) {
    $select_cfop[$escolha['id_cfop']] = $escolha['id_cfop'] . " - " . $escolha['descricao'];
}


// -----------------------------------------------------------------------------
//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "41", "area" => "Estoque", "ativo" => "Cadastro Manual", "id_form" => "form1");
$breadcrumb_pages = array("NFe" => "form_nfe.php");
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
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
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
                    <form action="nfe_controle.php" method="post" name="form1" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" >
                        <input type="hidden" name="home" id="home">

                        <input type="hidden" name="id_prestador" value="<?= $arr_pedido['id_prestador'] ?>">
                        <input type="hidden" name="id_regiao" value="<?= $arr_pedido['id_regiao'] ?>">
                        <input type="hidden" name="id_projeto" value="<?= $arr_pedido['id_projeto'] ?>">
                        <input type="hidden" name="razao_cnpj" value="<?= $arr_pedido['razao_cnpj'] ?>">

                        <div class="panel panel-default">
                            <div class="panel-heading">Formulário de Cadastro de NFe</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Fornecedor</label>
                                    <div class="col-sm-4">
                                        <p class="form-control-static"><?= $arr_pedido['razao'] ?></p>

                                    </div>
                                    <label class="col-lg-2 control-label">CNPJ</label>
                                    <div class="col-sm-4">
                                        <p class="form-control-static"><?= mascara_string('##.###.###/####-##', $arr_pedido['razao_cnpj']) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label">Destinatário</label>
                                    <div class="col-sm-4">
                                        <p class="form-control-static"><?= $arr_pedido['upa'] ?></p>
                                    </div>
                                    <label class="col-lg-2 control-label">CNPJ</label>
                                    <div class="col-sm-4">
                                        <p class="form-control-static"><?= $arr_pedido['upa_cnpj'] ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="chaveacesso" class="col-lg-2 control-label">Chave de Acesso</label>
                                    <div class="col-lg-6">
                                        <input id="chaveacesso" name="chaveacesso" type="text" class="form-control text-center validate[required]" placeholder="Chave de Acesso" onkeypress="formata_mascara(this, '#### #### #### #### #### #### #### #### #### #### ####')">
                                    </div>
                                    <label for="numeronf" class="col-lg-1 control-label">NF</label>
                                    <div class="col-lg-2">
                                        <input id="numeronf" name="numeronf" type="text" class="form-control text-center validate[required]" maxlength="18">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cfop" class="col-lg-2 control-label">Natureza da Operação</label>
                                    <div class="col-lg-5">
                                        <?= montaSelect($select_cfop, $_REQUEST['cfop'], 'class="col-lg-4 form-control" name="cfop" id="cfop"') ?>
                                    </div>
                                    <label for="dt_emissao_nf" class="col-lg-2 control-label">Data Emissão</label>
                                    <div class="col-lg-2">
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center data validate[required]" name="dt_emissao_nf" id="dt_emissao_nf">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table table-striped table-hover valign-middle">
                                            <thead>
                                                <tr class="text text-sm"> 
                                                    <th>Código</th>
                                                    <th>Produto/ Descrição</th>
                                                    <th>NCM/ST</th>
                                                    <th>Lote</th>
                                                    <th>Validade</th>
                                                    <th>Und</th>
                                                    <th>Quantidade</th>
                                                    <th>Vlr Unitário</th>
                                                    <th>Valor (R$)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tb-itens">
                                                <?php
                                                $total = 0;
                                                foreach ($arr_pedido['itens'] as $value) {
                                                    $total +=$value['vProd'];
                                                    ?>
                                                    <tr class="valign-middle">
                                                        <td>
                                                            <?= $value['cProd'] ?>
                                                            <input type="hidden" name="cProd[]" value="<?= $value['cProd'] ?>">
                                                        </td>
                                                        <td>
                                                            <?= $value['xProd'] ?>
                                                            <input type="hidden" name="id_prod[]" value="<?= $value['id_prod'] ?>">
                                                        </td>
                                                        <td><?= $value['NCM'] ?></td>
                                                        <td><input type="text" name="nLote[]" class="form-control input-sm"></td>
                                                        <td><input type="text" name="dVal[]" class="form-control input-sm datav"></td>
                                                        <td><?= $value['uCom'] ?></td>
                                                        <td><input type="text" name="qCom[]" class="form-control input-sm text-right money qCom  validate[required]" value="<?= number_format($value['qCom'], 2, ',', '.') ?>"></td>
                                                        <td><input type="text" name="vUnCom[]" class="form-control input-sm text-right money vUnCom  validate[required]" value="<?= number_format($value['vUnCom'], 2, ',', '.') ?>"></td>
                                                        <td><input type="text" name="vProd[]" class="form-control input-sm text-right valor" value="<?= number_format($value['vProd'], 2, ',', '.') ?>" readonly></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="valign-middle"> 
                                                    <td colspan="8" class="text-bold text-right">Total:</td>
                                                    <td><input class="form-control input-sm text-right" name="vNF" id="vNF" value="<?= number_format($total, 2, ',', '.') ?>" readonly></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div><!-- /.col-lg-12 -->
                                </div><!-- /.row -->
                            </div><!-- /.panel-body -->
                            <div class="panel-footer text-right">
                                <button type="submit" value="Salvar" name="cadastro-salvar" class="btn btn-primary" id="cadastro-salvar"><i class="fa fa-floppy-o"></i> Salvar</button>
                            </div>
                        </div><!-- /.panel-default -->
                    </form>
                    <div id="resp_form_cad" class="loading"></div>
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
        <script src="js/nfe_cadastro.js" type="text/javascript"></script><!-- depois apontar para aresourse -->
    </body>
</html>