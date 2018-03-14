<!--
<?php if ($validacao === TRUE) { // se true entao está td ok                    ?>
        <div class='note note-success'>
            <h4 class='note-title'>Conferência Ok</h4>
            NFe Nº {$array_nfe['nNF']} em conformidade com o Pedido Nº <?php echo $_REQUEST['pedidoss'] ?>
        </div>
<?php } else if ($validacao === NULL) { // se for NULL estao os array que comararam estão vazios | '===' é necessário!!!  ?>
        <div class='note note-danger'>
            <h4 class='note-title'>Erro ao validar NFe</h4>
            <p class='text-danger text-justify'>Ocorreu um erro ao validar NFe. Verifique se o pedido não possui itens ou se o arquivo XML está correto.</p>
        </div>
<?php } else if ($validacao === FALSE) { ?>
        <div class='note note-danger'>
            <h4 class='note-title'>DIVERGÊNCIAS</h4>
            <p><h4>NFe Nº <?= $array_nfe['nNF'] ?> sem conformidade com o Pedido Nº <?= $_REQUEST['pedidoss'] ?></h4></p>
        </div>
<?php } ?>
-->

<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#resumo" aria-controls="resumo" role="tab" data-toggle="tab">Resumo</a></li>
    <li role="presentation"><a href="#divergente" aria-controls="divergente" role="tab" data-toggle="tab">Itens com Diferenças <span class="badge"><?= count($objNfe->arrayItensErrados) ?></span></a></li>
    <li role="presentation"><a href="#faltando" aria-controls="faltando" role="tab" data-toggle="tab">Itens Faltando <span class="badge"><?= count($objNfe->arrayItensPedFalta) ?></span></a></li>
    <li role="presentation"><a href="#sobrando" aria-controls="sobrando" role="tab" data-toggle="tab">Itens Fora do Pedido / Não Vinculados <span class="badge"><?= count($objNfe->arrayItensNfeExtra) ?></span></a></li>
</ul>

<br>

<!-- Tab panes -->
<div class="tab-content">
    <div role="tabpanel" class="tab-pane fade in active" id="resumo">
        <div class="panel panel-default habilitar">
            <table class='table-striped table table-hover table-bordered table-condensed'>
                <thead>
                    <tr class='text-center text-semibold'>
                        <th class='text-center text-semibold' colspan='2'></th>
                        <th class='text-center text-semibold text-danger' colspan='2'>PEDIDO NR - <?php echo $_REQUEST['pedidoss'] ?></th>
                        <th class='text-center text-semibold text-danger' colspan='2'>NFe NR - <?php echo $array_nfe['nNF'] ?></th>
                    </tr>
                    <tr class='text text-center text-sm'>
                        <th class='text text-center'>#</th>
                        <th class='text text-left'>Produto</th>
                        <th>Quantidade</th>
                        <th class='text text-right'>Valor R$</th>
                        <th>Quantidade</th>
                        <th class='text text-right'>Valor R$</th>
                    </tr>
                </thead>
                <tbody class='text-sm text-semibold'>
                    <?php
                    $i = 1;
                    foreach ($objNfe->arrayComparacao as $value) {
                        $vlr = number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") - number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".");
                        $qntd = (float) $value['NFE']['qCom'] - (float) $value['pedido']['qCom'];
                        $corqtd = ($value['pedido']['qCom'] == $value['NFE']['qCom']) ? "text-success" : "text-danger";
                        $corvlr = ($value['pedido']['vUnCom'] == $value['NFE']['vUnCom']) ? "text-success" : "text-danger";
                        ?>
                        <tr>    
                            <td class="text-center"><?= $i ?></td>
                            <td><input type="hidden" name="id_item_faltando[]" value="<?php echo $value['pedido']['id_item'] ?>">
                                <?php
                                if (empty($value['NFE'])) {
                                    echo $value['pedido']['xProd'];
                                } else {
                                    echo $value['NFE']['xProd'];
                                }
                                ?>
                            </td>
                            <?php if ((float) $value['pedido']['qCom'] == (float) $value['pedido']['qtd_faltando']) { ?>
                                <td class='text-center <?= $corqtd ?>'><?php echo (float) $value['pedido']['qCom'] ?></td>
                            <?php } else { ?>
                                <td class='text-center <?= $corqtd ?>'><?php echo (float) $value['pedido']['qtd_faltando'] ?></td>
                            <?php } ?>
                            <td class="text-right <?= $corvlr ?>"><?php echo number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".") ?></td>
                            <td class="text-center <?= $corqtd ?>"><?php echo (!empty($value['NFE']['qCom'])) ? (float) $value['NFE']['qCom'] : '-' ?></td>
                            <td class="text-right <?= $corvlr ?>"><?php echo (!empty($value['NFE']['vUnCom'])) ? number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") : '-' ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div><!-- /.tab-pane -->


    <div role="tabpanel" class="tab-pane fade" id="divergente">
        <div class="panel panel-default habilitar">
            <table class='table-striped table table-hover table-bordered table-condensed'>
                <thead>
                    <tr>
                        <th colspan="5" class="bg-info text-center">Produtos Com divergencia de Valor ou Quantidade</th>
                    </tr>
                    <tr class='text-center text-semibold'>
                        <th class='text-center text-semibold'></th>
                        <th class='text-center text-semibold' colspan='2'>PEDIDO NR - <?php echo $_REQUEST['pedidoss'] ?></th>
                        <th class='text-center text-semibold' colspan='2'>NFe NR - <?php echo $array_nfe['nNF'] ?></th>
                    </tr>
                    <tr class='text text-center text-sm'>
                        <th class='text text-left'>Produto</th>
                        <th>Quantidade</th>
                        <th class='text text-right'>Valor R$</th>
                        <th>Quantidade</th>
                        <th class='text text-right'>Valor R$</th>
                    </tr>
                </thead>
                <tbody class='text-sm text-semibold'>
                    <?php
                    foreach ($objNfe->arrayItensErrados as $value) {
                        $vlr = number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") - number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".");
                        $qntd = (float) $value['NFE']['qCom'] - (float) $value['pedido']['qCom'];
                        $corqtd = ($value['pedido']['qCom'] == $value['NFE']['qCom']) ? "text-success" : "text-danger";
                        $corvlr = ($value['pedido']['vUnCom'] == $value['NFE']['vUnCom']) ? "text-success" : "text-danger";
                        ?>
                        <tr>    
                            <td><input type="hidden" name="id_item_faltando[]" value="<?php echo $value['pedido']['id_item'] ?>"><?php echo $value['pedido']['xProd'] ?></td>
                            <?php if ((float) $value['pedido']['qCom'] == (float) $value['pedido']['qtd_faltando']) { ?>
                                <td class='text-center <?= $corqtd ?>'><?php echo (float) $value['pedido']['qCom'] ?></td>
                            <?php } else { ?>
                                <td class='text-center <?= $corqtd ?>'><?php echo (float) $value['pedido']['qtd_faltando'] ?></td>
                            <?php } ?>
                            <td class="text-right <?= $corvlr ?>"><?php echo number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".") ?></td>
                            <td class="text-center <?= $corqtd ?>"><?php echo (!empty($value['NFE']['qCom'])) ? (float) $value['NFE']['qCom'] : '-' ?></td>
                            <td class="text-right <?= $corvlr ?>"><?php echo (!empty($value['NFE']['vUnCom'])) ? number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") : '-' ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div><!-- /.tab-pane -->


    <div role="tabpanel" class="tab-pane fade" id="faltando">
        <div class="panel panel-default habilitar">
            <table class='table table-hover table-bordered table-condensed'>
                <thead>
                    <tr>
                        <th class='text-center text-semibold text-warning bg-warning' colspan='5'>NFe <?= $array_nfe['nNF'] ?> - Produtos faltando</th>
                    </tr>
                    <tr class='text text-center text-semibold'>
                        <th>Produto</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-center">Qtd Faltando</th>
                        <th class="text-right">Valor R$</th>
                    </tr>
                </thead>
                <tbody class='text-sm text-semibold text-warning'>
                    <?php
                    foreach ($objNfe->arrayItensPedFalta as $value) {
                        $vlr = number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") - number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".");
                        $qntd = (float) $value['NFE']['qCom'] - (float) $value['pedido']['qCom'];
                        ?>
                        <tr>
                            <td><?= $value['xProd'] ?></td>
                            <td class='text-center'><?= (float) $value['qCom'] ?></td>
                            <td class='text-center'><?= (float) $value['qtd_faltando'] ?></td>
                            <td class='text-right'><?= number_format((float) ($value['vUnCom']), 3, ",", ".") ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div><!-- /.tab-pane -->


    <div role="tabpanel" class="tab-pane fade" id="sobrando">

        <div class="panel panel-default habilitar">
            <table class='table table-hover table-bordered table-condensed'>
                <thead>
                    <tr>
                        <th class='text-center text-semibold text-danger bg-danger' colspan='5'>NFe <?= $array_nfe['nNF'] ?> - Produtos fora do pedido ou NÃO Vinculádos</th>
                    </tr>
                    <tr class='text text-center text-semibold'>
                        <th>Produto</th>
                        <th class="text-center">Quantidade</th>
                        <th class="text-right">Valor R$</th>
                        <th class="text-right">Vincular Produto</th>
                    </tr>
                </thead>
                <tbody class='text-sm text-semibold text-danger'>
                    <?php
                    foreach ($objNfe->arrayItensNfeExtra as $value) {
                        $vlr = number_format((float) ($value['NFE']['vUnCom']), 3, ",", ".") - number_format((float) ($value['pedido']['vUnCom']), 3, ",", ".");
                        $qntd = (float) $value['NFE']['qCom'] - (float) $value['pedido']['qCom'];
                        ?>
                        <tr>
                            <td><?= $value['xProd'] ?></td>
                            <td class="text-center"><?= (float) $value['qCom'] ?></td>
                            <td class="text-right"><?= number_format((float) ($value['vUnCom']), 3, ",", ".") ?></td>
                            <td class="text-right">
                                <input type="hidden" name="cProd" class="cProd" value="<?= $value['cProd'] ?>">
                                <?= montaSelect($optProdutos, NULL, 'name="id_produto" class="form-control vinculo_prod"' ) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div><!-- /.tab-pane -->

</div><!-- /.tab-content -->









