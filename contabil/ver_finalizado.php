<form action="classificacao_controle.php" method="post" class="form-horizontal" id="form_conciliacao">

    <?php while ($objLancamento->getRow()) {
        $objLancamentoItens->setIdLancamento($objLancamento->getIdLancamento());
        $objLancamentoItens->setStatus(1);
        $linhas = $objLancamentoItens->getLancamentoItens();
        ?>
        <div class="panel panel-default panel-lancamento"> 
            
            <div class="panel-heading">
                <label><span class="sr-only"><?= $objLancamento->getIdLancamento() ?></span></label>
                <?= ConverteData($objLancamento->getDataLancamento(), 'd/m/Y') ?>
            </div>
            <table class="table table-stripe table-condensed" id="table_<?= $objLancamento->getIdLancamento() ?>">
                <thead>
                    <tr>
                        <th style="width: 25%;">Conta</th>
                        <th>Descrição</th>
                        <th style="width: 25%;">Valor</th>
                        <th>Tipo</th>
                    </tr> 
                </thead>
                <tbody><?php
                    $total = 0;
                    foreach ($linhas as $value) {
                        if ($value['tipo'] == 1) {
                            $total +=$value['valor'];
                        } else {
                            $total -=$value['valor'];
                        } ?>
                        <tr>
                            <td><?= $value['conta'] ?></td>
                            <td><?= $value['descricao'] ?></td>
                            <td>R$ <?= number_format($value['valor'], 2, ',', '.') ?></td>
                            <td><?= ($value['tipo'] == 1) ? "Credor" : "Devedor" ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-right">Saldo:</th>
                        <td colspan="2">
                            R$ <?= number_format($total, 2, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php } ?>
</form>
