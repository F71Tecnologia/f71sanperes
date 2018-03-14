<table class="table table-striped"> 
    <thead>
        <tr class="text text-sm text-danger">
            <td>&emsp;</td>
            <td style="width: 17%;">Reten��o de COFINS</td>
            <td style="width: 17%;">Reten��o de CSLL</td>
            <td style="width: 17%;">Reten��o de INSS</td>
            <td style="width: 17%;">Reten��o de IRPJ</td>
            <td style="width: 17%;">Reten��o de PIS</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($array as $key => $values) { ?>
            <tr>
                <td><strong><?= $key === 'nfse'?'NFSe':'Calculado' ?></strong></td>
                <td>R$ <?= number_format((float) $values['COFINS'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format((float) $values['CSLL'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format((float) $values['INSS'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format((float) $values['IRRF'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format((float) $values['PIS'], 2, ',', '.') ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
