<div class="tab-pane fade in active" id="arquivoXML">
    <div class="row">
        <div class="col-lg-12 margin_b15 text-right">
            <!--<a href="#" class="btn btn-success"><i class="fa fa-file-text"></i> Cad. NFe Sem Pedidos</a> <!-- so quando essa funcionalidade existir -->
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><h4 class="no-margin-b">Pedidos em Aberto</h4></div>
        <table class="table table-striped table-hover table-condensed">
            <thead>
                <tr>
                    <th class="text-center">N&ordm;</th>
                    <th>Data</th>
                    <th>Projeto</th>
                    <th>Fornecedor</th>
                    <th>Total</th>
                    <th>&emsp;</th>
                    <th style="width: 290px;">&emsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultapedido as $key => $value) { ?>
                    <tr>
                        <td class="text-center"><?= $value['id_pedido'] ?></td>
                        <td><?= converteData($value['dtpedido'], 'd/m/Y') ?></td>
                        <td><?= $value['upa'] ?></td>
                        <td><?= $value['razao'] ?></td>
                        <td class="text-right"><?= number_format($value['total'], 2, ',', '.') ?></td>
                        <td class="text-center pedido_status">
                            <?php if ($value['status'] == 3) { ?>
                                <span class="label label-danger">Sem NFe Vinculada</span>
                            <?php } else if ($value['status'] == 4) { ?>
                                <span class="label label-warning">Em Aberto</span>
                            <?php } ?>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary btn-xs btn-impotacao-nfe" 
                                    data-id="<?= $value['id_pedido'] ?>" 
                                    data-fornecedor="<?= $value['id_prestador'] ?>"
                                    data-projeto="<?= $value['id_projeto'] ?>">
                                <i class="fa fa-download"></i> Importar NFe
                            </button>
                            <?php if ($value['status'] == 4) { ?>
                                <button type="button" class="btn btn-success btn-xs finalizar" data-id="<?= $value['id_pedido'] ?>"><i class="fa fa-arrow-circle-down"></i> Finalizar</button>
                            <?php } else { ?>
                                <button type="button" class="btn btn-success btn-xs " data-id="<?= $value['id_pedido'] ?>" disabled><i class="fa fa-arrow-circle-down"></i> Finalizar</button>
                            <?php } ?>
                            <button type="button" class="btn btn-info btn-xs conferencia" data-id="<?= $value['id_pedido'] ?>"><i class="fa fa-list"></i> Conferência</button>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<!-- fim da importação do XML -->