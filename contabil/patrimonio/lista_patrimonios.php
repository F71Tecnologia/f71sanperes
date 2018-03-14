<?php
if(!$_COOKIE['logado']){ header("Location: ../../index.php"); }

include_once("../../conn.php");
include_once("../../wfunction.php");
include_once("../../classes/PatrimoniosClass.php");

$usuario = carregaUsuario();

$objPatrimonios = new PatrimoniosClass();

if(isset($_REQUEST['filtrar']) && $_REQUEST['projeto'] > 0){
    $projeto = $_REQUEST['projeto'];
    $objPatrimonios->setIdProjeto($projeto);
    $objPatrimonios->getPatrimoniosByProjeto();
}

if(isset($_REQUEST['salvar'])){
    
    $objPatrimonios->setIdProjeto($_REQUEST['id_projeto']);
    $objPatrimonios->setNumero($_REQUEST['numero']);
    $objPatrimonios->setNumeroSerie($_REQUEST['numero_serie']);
    $objPatrimonios->setNome($_REQUEST['nome']);
    $objPatrimonios->setDescricao($_REQUEST['descricao']);
    $objPatrimonios->setDataAquisicao(implode('-', array_reverse(explode('/', $_REQUEST['data_aquisicao']))));
    $objPatrimonios->setDataAcerto(implode('-', array_reverse(explode('/', $_REQUEST['data_acerto']))));
    $objPatrimonios->setDataCadastro(implode('-', array_reverse(explode('/', $_REQUEST['data_cadastro']))));
    $objPatrimonios->setDataContabilizacao(implode('-', array_reverse(explode('/', $_REQUEST['data_contabilizacao']))));
    $objPatrimonios->setDataVistoria(implode('-', array_reverse(explode('/', $_REQUEST['data_vistoria']))));
    $objPatrimonios->setDataMarcacao(implode('-', array_reverse(explode('/', $_REQUEST['data_marcacao']))));
    $objPatrimonios->setDataBaixa(implode('-', array_reverse(explode('/', $_REQUEST['data_baixa']))));
    $objPatrimonios->setVencimentoGarantia(implode('-', array_reverse(explode('/', $_REQUEST['vencimento_garantia']))));
    $objPatrimonios->setNNf($_REQUEST['n_nf']);
    $objPatrimonios->setChaveNfs($_REQUEST['chave_nfs']);
    $objPatrimonios->setValorOriginal($_REQUEST['valor_original']);
    $objPatrimonios->setValorCompra($_REQUEST['valor_compra']);
    $objPatrimonios->setValorAtualizado($_REQUEST['valor_atualizado']);
    $objPatrimonios->setValorBaixa($_REQUEST['valor_baixa']);
    $objPatrimonios->setStatus(1);
    
    if(!empty($_REQUEST['id_patrimonio'])){
        $objPatrimonios->setIdPatrimonio($_REQUEST['id_patrimonio']);
        $objPatrimonios->update();
    } else {
        $objPatrimonios->insert();
    }
    echo $objPatrimonios->getIdPatrimonio();
    exit;
}

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$nome_pagina = "Patrimônios Cadastrados";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Patrimônios" => "index.php")  ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../../favicon.png">
        <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <style>
            .ng-valid { border-color: green; }
            .ng-invalid { border-color: red; }
        </style>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><span class="fa fa-bar-chart"></span> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <form action="" method="post" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-md-offset-1 col-md-10">
                                        <label class="control-label text-sm">Projeto:</label>
                                        <?= montaSelect(getProjetos($usuario['id_regiao']), $projeto, 'name="projeto" id="projeto" class="form-control input-sm"') ?>
                                        <!--<input type="text" ng-model="p.novo.nome" placeholder="Nome Patrimônio" name="nome" id="nome" class="form-control input-sm" required>-->
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer text-right">
                                <button type="submit" class="btn btn-primary" name="filtrar"><i class="fa fa-filter"></i> Filtrar</button>
                                <button type="button" class="btn btn-success addEditPatrimonio" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i> Novo Patrimônio</button>
                            </div>
                        </div>
                    </form>
                    <?php if($objPatrimonios->getNumRows() > 0) {?>
                    <table class="table valign-middle text-sm">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th class="text-center">Data Aquisição</th>
                                <th class="text-right">Valor Original</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($objPatrimonios->getRow()) { ?>
                            <tr id="p<?= $objPatrimonios->getIdPatrimonio() ?>">
                                <td><?= $objPatrimonios->getNome() ?></td>
                                <td class="text-center"><?= $objPatrimonios->getDataAquisicao('d/m/Y') ?></td>
                                <td class="text-right"><?= $objPatrimonios->getValorOriginal() ?></td>
                                <td class="text-right">
                                    <button class="btn btn-xs btn-warning addEditPatrimonio" data-toggle="modal" data-target="#myModal" data-id="<?= $objPatrimonios->getIdPatrimonio() ?>"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-xs btn-danger deletarPatrimonio" data-id="<?= $objPatrimonios->getIdPatrimonio() ?>"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                    <div class="alert alert-info">Nenhum patrimonio cadastrado</div>
                    <?php } ?>
                </div>
            </div>
            <!-- MODAL -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form name="formPatrimonio" id="formPatrimonio" class="form-horizontal" novalidate></form>
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        <script src="js/lista_patrimonios.js"></script>
    </body>
</html>