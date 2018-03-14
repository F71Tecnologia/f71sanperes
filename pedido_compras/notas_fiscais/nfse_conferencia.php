<div class="tab-pane fade <?= ($acoes->verifica_permissoes(112) && !$acoes->verifica_permissoes(111)) ? 'in active' : '' ?>" id="conferencia">
    <div class="panel panel-default">
        <table class="table table-striped table-hover table-condensed valign-middle text text-sm">
            <thead>
                <tr>
                    <th style="width: 8%" class="text text-right">Número NF</th>
                    <th style="width: 10%" class="text text-center">Data</th>
                    <th style="width: 24%">Projeto</th>
                    <th style="width: 46%">Prestador</th>
                    <th style="width: 12%">CNPJ</th>
                    <th colspan="2">&emsp;</th>
                </tr>
            </thead>
            <tbody> 
                <?php foreach ($nfse_servico_ok as $key => $value) { ?>
                    <tr>
                        <td class="text text-right"><?= $value['Numero'] ?></td>
                        <td class="text text-center"><?= converteData($value['DataEmissao'], 'd/m/Y') ?></td>
                        <td><?= $value['nome_projeto'] ?></td>
                        <td><?= $value['c_razao'] ?></td>
                        <td><?= $value['c_cnpj'] ?></td>
                        <td>
                            <?php $xxx = (empty($value['arquivo_pdf'])) ? 'disabled' : ''; ?>
                            <a href="../../compras/notas_fiscais/nfse_anexos/<?= $value['id_projeto'] ?>/<?= $value['arquivo_pdf'] ?>" target="_blank" class="btn btn-default btn-xs <?= $xxx ?>"  role="button"><i class="fa fa-file-pdf-o text-danger"></i> Ver PDF</a>
                        </td>
                        <td class="text text-right">
                            <button class="btn btn-xs btn-info btn-conferir" data-id="<?= $value['id_nfse'] ?>"><i class="fa fa-check-square-o"></i> Visualizar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
