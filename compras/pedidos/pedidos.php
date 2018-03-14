<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/global.php");
include("../../classes/pedidosClass.php");
include("../../classes/PedidosTipoClass.php");
include("../../wfunction.php");

$usuario = carregaUsuario();
$global = new GlobalClass();

$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$abasel = (isset($_REQUEST['aba'])) ? $_REQUEST['aba'] : 'solicitapedido';

$projeto1 = montaSelect($global->carregaProjetosByRegiao($id_regiao), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]' data-for='contrato'");

function checkAba($aba1, $aba2, $fade = FALSE) {
    $return = ($aba1 == $aba2 && $fade) ? 'in ' : '';
    $return .= ($aba1 == $aba2) ? ' active' : '';
    return $return;
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "41", "area" => "Estoque", "ativo" => "Gestão de Pedidos", "id_form" => "form-pedido");

$pedido = new pedidosClass();
$objPedidosTipo = new PedidosTipoClass();

$dadosConsultaPed['id_projeto'] = (isset($_REQUEST['projeto']) && !empty($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : NULL;
$dadosConsultaPed['id_regiao'] = $usuario['id_regiao'];

$lista = $pedido->pedidosSolicitados($dadosConsultaPed); // pedidos solicitados pelas unidades ou setor de compras
$listaenvia = $pedido->pedidosConfirmados($dadosConsultaPed); // pedidos confirmados pelo setor de compras, aguardando envio
$pedidos_cancelados = $pedido->pedidosCancels(); //pedidos que foram cancelados
$fornecedor = array(-1 => '« Selecione o Projeto »'); //($_REQUEST['regiao'], $_REQUEST['projeto']);

$objPedidosTipo->getPedidosTipo();
$tipos_ped['-1'] = '« Selecione »';
while ($objPedidosTipo->getRow()) {
    $tipos_ped[$objPedidosTipo->getIdTipo()] = $objPedidosTipo->getDescricao();
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
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
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
                        <h2><span class="fa fa-archive"></span> - ESTOQUE <small>- Pedidos</small></h2>
                    </div>
                    <div role="tabpanel">
                        <ul class="nav nav-tabs nav-justified " style="margin-bottom: 15px;">
                            <li class="<?= checkAba('solicitapedido', $abasel) ?>"><a href="#solicitapedido" data-toggle="tab">Solicitação</a></li> 
                            <li class="<?= checkAba('confirmapedidos', $abasel) ?>"><a href="#confirmapedidos" data-toggle="tab">Confirmação</a></li>
                            <li class="<?= checkAba('enviarpedidos', $abasel) ?>"><a href="#enviarpedidos" data-toggle="tab">Enviar</a></li>
                            <li class="<?= checkAba('pedidosfinalizado', $abasel) ?>"><a href="#pedidosenviados" data-toggle="tab">Finalizados</a></li>
                            <li class="<?= checkAba('pedidoscancelados', $abasel) ?>"><a href="#pedidoscancelados" data-toggle="tab">Cancelados</a></li>
                        </ul>

                        <!-- solicitação de do pedido -->
                        <div class="tab-content">
                            <div class="tab-pane fade <?= checkAba('solicitapedido', $abasel, 1) ?>" id="solicitapedido">
                                <form action="pedidos_methods.php" method="post" class="form-horizontal" id="form-pedido" enctype="multipart/form-data">
                                    <input type="hidden" name="home" id="home" value="">
                                    <fieldset>
                                        <legend>Cadastro do Pedido</legend>
                                        <div class="panel panel-default">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="contrato" class="col-lg-2 control-label">Pedido</label>
                                                    <div class="col-lg-4">
                                                        <?= montaSelect($tipos_ped, null, 'name="filtra_tipo" id="filtra_tipo" class="validate[required,custom[select]] form-control" data-for="contrato"') ?>
                                                    </div>
                                                    <label for="projeto1" class="col-lg-1 control-label">Projeto</label>
                                                    <div class="col-lg-4"><input type="hidden" name="regiao1" value="<?= $id_regiao ?>">

                                                        <?php echo $projeto1; ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="fornecedor" class="col-lg-2 control-label" onblur="">Fornecedor</label>
                                                    <div class="col-lg-9">
                                                        <?php echo montaSelect($fornecedor, null, "name='id_prestador' id='contrato' class='form-control validate[required,custom[select]]'"); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Observação</label>
                                                    <div class="col-lg-9">
                                                        <textarea rows="3" class="form-control" id="observacao" name="observacao"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="" class="col-lg-2 control-label" onblur="">Importação?</label>
                                                    <div class="col-lg-2">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="radio" name="importacao" class="importacao" value="s"> SIM
                                                            </label>
                                                        </div>  
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="radio" name="importacao"class="importacao" value="n" checked> NÃO
                                                            </label>
                                                        </div>  
                                                    </div>
                                                </div>
                                                <div class="form-group" id="div_importar" style="display:none">
                                                    <div class="col-lg-5 col-lg-offset-2">
                                                        <input type="file" accept=".xls, .xlsx" name="arquivo_excel" id="arquivo_excel" class="form-control filestyle" data-buttonText="&ensp;Arquivo Excel">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="panel-footer text-right">
                                                <!--<a href="#" class="btn btn-default" id="gera_excel"><i class="fa fa-file-excel-o text-success"></i> Gerar Excel para UPAs</a>-->
                                                <button type="submit" id="buscarprods" name="buscarprods" value="Produtos" class="btn btn-primary"><i class="fa fa-list-ul"></i> Listar Produtos</button>
                                            </div>
                                            <table class="bg-stable  table table-condensed table-striped hide text" id="tab-produtos">
                                                <thead class="bg-primary">
                                                    <tr>
                                                        <th style="width:50%">Descrição</th>
                                                        <th style="width:10%" class="text-center">Und</th>
                                                        <th style="width:10%" class="text text-center text-sm" style="font-size:0.8em">Vlr Acordado</th>
                                                        <th style="width:15%" class="text-center">Quantidade</th>
                                                        <th style="width:15%" class="text-center">R$ Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody class=" text-sm">
                                                    <?php $vlrTot = 0 ?>
                                                    <!-- (#tab_prod) -->
                                                </tbody>
                                                <tfoot class="text text-right">
                                                    <tr tr class="valign-middle">
                                                        <td colspan="3"></td>
                                                        <td><button type="submit" class="btn btn-block btn-success gera-pedido" onclick="" id="gerarpedido" name="gerarpedido" value="Solicitar Pedido">Solicitar Pedido <i class="fa fa-arrow-right"></i></button></td>
                                                        <td><input type="text" name="somapedido" id="somapedido" class="text form-control text-right" readonly> </td>

                                                    </tr>
                                                </tfoot>                                                    
                                            </table>
                                        </div>
                                    </fieldset>
                                    <div>
                                    </div>
                                </form>
                            </div>
                            <!-- solicitação de do pedido -->

                            <!-- confirmacao do pedido -->
                            <div class="tab-pane fade <?= checkAba('confirmapedidos', $abasel, 1) ?>" id="confirmapedidos">
                                <form action="confirmapedido.php" id="form-confirmaPedido" method="post" class="form-horizontal" enctype="multipart/form-data">
                                    <div id="confirmarPedido" class="loading">
                                        <legend>Analise e confirmação do pedido solicitado</legend>
                                        <?php if (count($lista) > 0) { ?>
                                            <table class="table table-striped table-hover" id="table-confirma-pedido">
                                                <thead>
                                                    <tr>
                                                        <th>Pedido</th>
                                                        <th>Data</th>
                                                        <th>Unidade</th>
                                                        <th>Fornecedor</th>
                                                        <th>Total R$</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($lista as $value) { ?>
                                                        <tr class="text text-sm" id="tr-<?= $value['id_pedido'] ?>">
                                                            <td class="text-center"><?= $value['id_pedido'] ?></td>
                                                            <td><?= converteData($value['dtpedido'], "d/m/Y") ?></td>
                                                            <td><?= $value['upa'] ?></td>
                                                            <td><?= $value['razao'] ?></td>
                                                            <td>R$<span class="pull-right"><?= number_format($value['total'], 2, ',', '.'); ?></span></td>
                                                            <td>
                                                                <a href="#" class="btn btn-block btn-success btn-xs pedido-detalhes" data-id="<?= $value['id_pedido'] ?>">
                                                                    <i class="fa fa-file-text-o"></i> Detalhar
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-block btn-info btn-xs btn-confirmaOk" data-id="<?= $value['id_pedido'] ?>" onclick="confirmaOK(<?= $value['id_pedido'] ?>);">
                                                                    <i class="fa fa-check"></i> Confirmar
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php } else { ?>
                                            <div class="alert alert-dismissable alert-warning">
                                                <button type="button" class="close" data-dismiss="alert">×</button>
                                                <h4>Atenção!</h4>
                                                <p>Não há Pedidos Soliciados.</p>
                                            </div>
                                        <?php } ?> 
                                    </div>
                                </form>
                            </div>   <!-- analise e confirmação do pedido -->

                            <div class="tab-pane fade <?= checkAba('enviarpedidos', $abasel, 1) ?>" id="enviarpedidos">
                                <form action="confirmapedido.php" id="form-enviarPedido" method="post" class="form-horizontal" enctype="multipart/form-data">
                                    <div id="enviarPedido" class="loading">
                                        <legend>Envia o pedido ao fornecedor</legend>
                                        <?php if (count($listaenvia) > 0) { ?>

                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2">Pedido</th>
                                                        <th>Total R$</th>
                                                        <th>Fornecedor</th>
                                                        <th>Projeto</th>
                                                        <th></th>
                                                    </tr> 
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($listaenvia as $values) { ?>
                                                        <tr class="text text-sm" id="tr-<?= $values['id_pedido'] ?>">
                                                            <td class="text-center"><?= $values['id_pedido'] ?></td>
                                                            <td><?= converteData($values['dtpedido'], "d/m/Y") ?></td>
                                                            <td>R$<span class="pull-right"><?= number_format($values['total'], 2, ',', '.'); ?></span></td>
                                                            <td><?= $values['razao'] ?></td>
                                                            <td><?= $values['upa'] ?></td>
                                                            <td>
                                                                <a href="#" class="btn btn-block btn-success btn-xs enviar-pedido" data-id="<?= $values['id_pedido'] ?>" data-email="<?= $values['email'] ?>" data-upa="<?= $values['upa'] ?>"><i class="fa fa-envelope-o"> </i> Enviar</a><?= $values[''] ?>
                                                                <a href="#" class="btn btn-block btn-info btn-xs sem_enviar" data-id="<?= $values['id_pedido'] ?>" data-email="<?= $values['email'] ?>" data-upa="<?= $values['upa'] ?>"><i class="fa fa-check"> </i> Finalizar Sem Enviar</a><?= $values[''] ?>

                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php } else {
                                            ?>
                                            <div class="alert alert-dismissable alert-warning text text-center">
                                                <h4>Não há Pedidos para serem Enviados ao Fornecedor...</h4>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>   <!-- envio do pedido ao fornecedor -->

                            <div class="tab-pane fade <?= checkAba('pedidosfinalizado', $abasel, 1) ?>" id="pedidosenviados">
                                <form method="post" class="form-horizontal" id="form_filtro_finalizados" action="pedidos_methods.php">
                                    <div class="panel panel-default">
                                        <div class="panel-body">

                                            <div class="form-group">
                                                <label for="tipo_finalizado" class="col-lg-2 control-label">Tipo de Pedido</label>
                                                <div class="col-lg-6">
                                                    <?php echo montaSelect(array('-1' => 'Selecione', '1' => 'Material Hospitalar', '2' => 'Medicamentos'), NULL, 'name="tipo_finalizado" id="tipo_finalizado" class="validate[required,custom[select]] form-control"') ?>
                                                </div> 
                                            </div>
                                            <div class="form-group">
                                                <label for="tipo_finalizado" class="col-lg-2 control-label">Mes/Ano do Pedido</label>
                                                <div class="col-lg-5">
                                                    <div class="input-group">
                                                        <?php echo montaSelect(mesesArray(), date('m'), 'name="mes" id="mes" class="form-control"') ?>
                                                        <span class="input-group-addon">/</span>
                                                        <?php echo montaSelect(anosArray(2016, date('Y')), NULL, 'name="ano" id="ano" class="form-control"') ?>
                                                    </div>
                                                </div> 
                                            </div>

                                        </div>
                                        <div class="panel-footer text-right">
                                            <button type="submit" class="btn btn-primary" name="filtrar_finalizados" value="1"><i class="fa fa-filter"></i> Filtrar</button>
                                        </div>
                                    </div>
                                </form>
                                <table class="table table-striped table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Data</th>
                                            <th>Projeto</th>
                                            <th>Tipo</th>
                                            <th>Fornecedor</th>
                                            <th style="width: 260px"></th>                                            
                                        </tr>
                                    </thead>
                                    <tbody id="ped_finalizados">
                                        <?php foreach ($listaEnviados as $value) { ?>
                                            <tr id="tr-<?= $value['id_pedido'] ?>">
                                                <td class="text-center"><?= $value['id_pedido'] ?></td>
                                                <td><?= converteData($value['dtpedido'], "d/m/Y") ?></td>
                                                <td><?= $value['upa'] ?></td>
                                                <td>
                                                    <?php
                                                    if ($value['tipo'] == 2) {
                                                        echo 'Medicamentos';
                                                    } else if ($value['tipo'] == 1) {
                                                        echo 'Material';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?= $value['razao'] ?></td>
                                                <td class="text-right">
                                                    <a href="pdf/PED<?= $value['id_pedido'] ?>.pdf" target="_blank" class="btn btn-default btn-xs"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a>
                                                    <button type="button" class="btn btn-info btn-xs conferencia" data-id="<?= $value['id_pedido'] ?>"><i class="fa fa-list"></i> Conferência</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div><!-- historico de pedidos -->

                            <div class="tab-pane fade <?= checkAba('pedidoscancelados', $abasel, 1) ?>" id="pedidoscancelados">

                                <?php if (count($pedidos_cancelados) > 0) { ?>
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Data</th>
                                                <th>Fornecedor</th>
                                                <th>Projeto</th>
                                                <th>Motivo</th>
                                                <th>Funcionário</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-uppercase text-sm text-danger">
                                            <?php foreach ($pedidos_cancelados as $value) { ?>
                                                <tr id="tr-<?= $value['id_pedido'] ?>">
                                                    <td><?= $value['id_pedido'] ?></td>
                                                    <td><?= converteData($value['dtpedido'], "d/m/Y") ?></td>
                                                    <td><?= $value['razao'] ?></td>
                                                    <td><?= $value['upa'] ?></td>
                                                    <td><?= $value['observacao'] ?></td>
                                                    <td><?= $value['confirmado'] ?></td>
                                                    <td>
                                                        <a href="#" class="btn  btn-warning btn-xs pedido_reabrir" data-id="<?= $value['nrpedido'] ?>" >
                                                            <i class="fa fa-file-text-o"></i> Reabrir <?= $values[''] ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <?php } else { ?>
                                    <div class="alert alert-dismissable alert-warning text text-center">
                                        <h4>Não há Pedidos Canelados...</h4>
                                    </div>
                                <?php } ?>
                            </div><!-- historico de pedidos -->
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.11.2.custom/jquery-ui.min.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/pedidos.js" type="text/javascript"></script>
    </body>
</html>