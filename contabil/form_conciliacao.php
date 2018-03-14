<?php

/*
 * essa tela foi descontinuada.
 * suas funçoes foram emcorporadas na tela classificaca.php
 * em 22/09/2016
 * se já passou muito tempo e não fez falta, favor, excluir
 *
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * ---------------------------- DESCONTINUADA ----------------------------------
 * 
 */

header('Content-Type: text/html; charset=iso-8859-1');
include_once("../conn.php");
include_once("../wfunction.php");
include_once("../classes/c_planodecontasClass.php");
include_once("../classes/c_classificacaoClass.php");
include_once("../classes/ContabilLancamentoClass.php");
include_once("../classes/ContabilLancamentoItemClass.php");
include_once("../classes/ContabilLoteClass.php");
include_once("../classes_permissoes/acoes.class.php");

include_once("../classes/global.php");

if(isset($_REQUEST['method']) && $_REQUEST['method'] == 'getTipos'){
    $sqlTipos = "SELECT id_entradasaida, nome FROM entradaesaida WHERE grupo >= 5 ORDER BY id_entradasaida, grupo";
    $qryTipos = mysql_query($sqlTipos)or die(mysql_error());
    $optTipos = "<option value=''>SELECIONE O HISTORICO</option>";
    while($rowTipos = mysql_fetch_assoc($qryTipos)){
        $optTipos .= "<option value='{$rowTipos['nome']}'>{$rowTipos['nome']} ({$rowTipos['id_entradasaida']})</option>";
    }
    echo $optTipos;exit;
}

$ACOES = new Acoes();
$usuario = carregaUsuario();
$classificacao = new c_classificacaoClass();
$objLancamento = new ContabilLancamentoClass();
$objLancamentoItens = new ContabilLancamentoItemClass();

if(!empty($_REQUEST['id_lancamento'])){
    $objLancamento->setStatus(1);
    $objLancamento->getLancamentos();
    $optTipo = array(1 => 'Crédito', 2 => 'Débito'); 
}

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$nome_pagina = "Conciliação Lançamento {$objLancamento->getIdLancamento()}";
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
$breadcrumb_pages = array("Classificação Contábil" => "classificacao.php")  ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../template/navbar_default.php") ?> 
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contabil-header">
                        <h2><span class="fa fa-bar-chart"></span> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    
                    <div class="alert alert-warning text-bold">TELA DESCONTINUADA!!!</div>
                    <?php if (!empty($_REQUEST['id_lancamento'])) { ?>
                        <form action="classificacao_controle.php" method="post" class="form-horizontal" id="form_conciliacao">
                            <input type="hidden" name="id_lancamento" id="id_lancamento" value="<?= $objLancamento->getIdLancamento() ?>">
                            <input type="hidden" name="projeto" id="projetos" value="<?= $objLancamento->getIdProjeto() ?>">
                            <input type="hidden" name="data_lancamento" id="data_lancamento" value="<?= $objLancamento->getDataLancamento() ?>">
                            <?php if ($objLancamento->getStatus() != 0) { ?>
                                <p class="text-left"> 
                                    <button type="button" class="btn btn-info btn-xs add_lancamento">
                                        <i class="fa fa-plus"></i> Lançamento
                                    </button>
                                </p>
                                <div id="container-lancamento">
                            <?php } ?>
                            <?php
                            $auxDataLancamento = null;

                            while ($objLancamento->getRow()) {
                                $objLancamentoItens->setIdLancamento($objLancamento->getIdLancamento());
                                $objLancamentoItens->setStatus(1);
                                $linhas = $objLancamentoItens->getLancamentoItens();
//                            print_array($linhas);
                                if ($objLancamento->getDataLancamento() != $auxDataLancamento) {
                                    if (!empty($auxDataLancamento)) {
                                        echo '</div></div>';
                                    } ?>
                                    <div class="panel panel-default panel-lancamento-data"> 
                                        <div class="panel-heading panel-lancamento-head">
                                            <div class="col-sm-11 no-padding-hr">
                                                <input type="text" value="<?= ConverteData($objLancamento->getDataLancamento(), 'd/m/Y') ?>" class="form-control input-sm data_lancamento text-center" style="width:100px; display: inline-block" readonly>
                                            </div>
                                            <div class="col-sm-1 text-right">
                                                <i class="fa fa-arrow-circle-o-down pointer toogle" style="font-size: 30px!important;" data-data="<?= $objLancamento->getDataLancamento() ?>"></i>
                                            </div>
                                            <div class="clear"></div>  
                                        </div>
                                        <div class="panel-body <?= $objLancamento->getDataLancamento() ?>" style="display:none;">
                                            <?php $auxDataLancamento = $objLancamento->getDataLancamento();
                                            
                                    } ?>
                                            <div class="panel panel-default panel-lancamento"> 
                                                <div class="panel-heading">
                                                    <span class="label_id_lancamento"><?= $objLancamento->getIdLancamento() ?> - <?= $objLancamento->getHistorico() ?></span>
                                                    <input type="hidden" name="data_lancamento[]" value="<?= ConverteData($objLancamento->getDataLancamento(), 'd/m/Y') ?>">
                                                    <input type="hidden" name="historico[]" value="<?= $objLancamento->getHistorico() ?>">
                                                    <input type="hidden" name="id_lancamento[]" value="<?= $objLancamento->getIdLancamento() ?>" class="id_lancamento">
                                                    <div class="pull-right">
                                                        <?php if ($objLancamento->getIdEntrada() == 0 && $objLancamento->getIdSaida() == 0) { ?>
            <?php if ($objLote->getStatus() < 2) { ?><button type="button" class="btn btn-danger btn-sm excluir_lancamento" title="Excluir Lancamento" data-id="<?= $objLancamento->getIdLancamento() ?>" data-data="<?= $objLancamento->getDataLancamento() ?>"><i class="fa fa-trash"></i></button><?php } ?>
        <?php } ?>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                                <div class="panel-body">
                                                    <table class="table table-stripe table-condensed text-sm valign-middle" id="table_<?= $objLancamento->getIdLancamento() ?>">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 16%;">Conta</th>
                                                                <th>Descrição</th>
                                                                <th style="width: 17%;">Valor</th>
                                                                <th class="text-center" style="width: 8%">Natureza</th>
                                                                <th style="width: 8%">&emsp;</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody><?php
                                                            $total = 0;
                                                            foreach ($linhas as $value) {
                                                                
                                                                if ($value['tipo'] == 1) {
                                                                    $cor_tr = 'success';
                                                                    $total +=$value['valor'];
                                                                } else {
                                                                    $cor_tr = 'danger';
                                                                    $total -=$value['valor'];
                                                                }
                                                                ?>
                                                                <tr class="<?= $cor_tr ?> tr<?= $value['id_lancamento_itens'] ?>">
                                                                    <td><?= $value['conta'] ?></td>
                                                                    <td><?= $value['descricao'] ?></td>
                                                                    <td>R$ <label class="lancamento_item_valor_label" data-id="<?= $objLancamento->getIdLancamento() ?>" data-valor="<?= $value['valor'] ?>"><?= number_format($value['valor'], 2, ',', '.') ?></label></td>
                                                                    <td class="text-center"><label class="lancamento_item_tipo_label" data-tipo="<?= $value['tipo'] ?>" data-id="<?= $objLancamento->getIdLancamento() ?>"><?= $optTipo[$value['tipo']] ?></label></td>
                                                                    <td rowspan="2" class="text-center">
                                                                        <?php if ($objLancamento->getIdEntrada() == 0 && $objLancamento->getIdSaida() == 0) { ?>
                <?php if ($objLote->getStatus() < 2) { ?><button type="button" class="btn btn-xs btn-warning editar_lancamento_item" data-id="<?= $value['id_lancamento_itens'] ?>" data-valor="<?= $value['valor'] ?>" data-tipo="<?= $value['tipo'] ?>" data-conta="<?= $value['conta'] ?>" data-descricao="<?= $value['descricao'] ?>" data-id_lancamento="<?= $objLancamento->getIdLancamento() ?>" data-historico_item="<?= $value['historico'] ?>" data-id_conta="<?= $value['id_conta'] ?>" title="Editar Item do Lançamento"><i class="fa fa-edit"></i></button><?php } ?>
                <?php if ($objLote->getStatus() < 2) { ?><button type="button" class="btn btn-xs btn-danger excluir_lancamento_item" data-id_lancamento="<?= $objLancamento->getIdLancamento() ?>" data-id="<?= $value['id_lancamento_itens'] ?>" title="Excluir Item do Lançamento"><i class="fa fa-trash"></i></button><?php } ?>
            <?php } ?>
                                                                    </td>                                                                
                                                                </tr>
                                                                <tr class="no-border <?= $cor_tr ?> tr<?= $value['id_lancamento_itens'] ?>">
                                                                    <td class="no-border" colspan="4"><?= $value['historico'] ?></td>
                                                                </tr>
                                                                <tr class="tr<?= $value['id_lancamento_itens'] ?>"><td colspan="5" class="no-border" ></td></tr>
        <?php } ?></tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th colspan="2" class="text-right">
                                                                    <span style="display: inline-block; padding-top:5px;">Saldo:</span>
                                                                </th>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <div class="input-group-addon">R$</div>
                                                                        <input type="text" class="form-control input-sm saldo saldo_<?= $objLancamento->getIdLancamento() ?>" id="saldo_<?= $objLancamento->getIdLancamento() ?>" data-id="<?= $objLancamento->getIdLancamento() ?>" value="<?= number_format($total, 2, ',', '.') ?>" readonly>
                                                                    </div>
                                                                </td>
                                                                <td colspan="2">
        <?php if ($objLancamento->getIdEntrada() == 0 && $objLancamento->getIdSaida() == 0) { ?>
            <?php if ($objLote->getStatus() < 2) { ?><button type="button" class="btn btn-block btn-sm btn-success add_lancamento_item" data-id="<?= $objLancamento->getIdLancamento() ?>"><i class="fa fa-plus"></i> Incluir</button><?php } ?>
        <?php } ?>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                <?php } ?>
                                    </div>
                                </div>

    <?php if ($objLote->getStatus() < 2) { ?>
                                </div>
                                <div class="panel panel-default">
                                    <div class="panel-footer text-right">
                                        <button type="button" class="btn btn-sm btn-primary btnSalvar"><i class="fa fa-save"></i> Salvar</button>
                                        <button type="button" class="btn btn-sm btn-warning btnFinalizar"><i class="fa fa-lock"></i> Finalizar</button>
                                    </div>
                                </div>
                        <?php } else { ?>
                                <div class="alert alert-success text-bold text-center">LOTE FINALIZADO!</div>
                        <?php } ?>
                        </form>
                    <?php } else { ?>
                        <div class="alert alert-info">Erro ao selecionar o lote!</div>
                    <?php } 
                    include_once '../template/footer.php'; ?>
                </div>
            </div>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/classificacao.js" type="text/javascript"></script>
    </body>
</html>
