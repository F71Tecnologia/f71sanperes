<div class="table-responsive">
    <table class="table table-hover table-condensed table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th style="width: 15%">Projeto</th>
                <th style="width: 25%">Razão Social</th>
                <th style="width: 25%">Nome Fantasia</th>
                <th>CNPJ</th>
    <!--            <th>Início do<br> Contrato</th>
                <th>Fim do<br> Contrato</th>-->
                <th>&emsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prestadores as $value) { ?>
                <tr>
                    <td><?= $value['id_prestador'] ?></td>
                    <td><?= $value['nome_projeto'] ?></td>
                    <td><?= $value['razao'] ?></td>
                    <td><?= $value['fantasia'] ?></td>
                    <td><?= $value['cnpj'] ?></td>
    <!--                <td><?= $value['contratado_em'] ?></td>
                    <td><?= $value['encerrado_em'] ?></td>-->
                    <td class="text-right">
                        <button type="button" class="btn btn-info btn-xs btn-imposto" data-id="<?= $value['id_prestador'] ?>"><i class="fa fa-money"></i> Impostos</button>
                    </td>
                </tr>
            <?php } ?>

        </tbody>
    </table>
</div>
