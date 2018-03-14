<?php header('Content-Type: text/html; charset=utf-8'); ?>

<?php foreach ($array_arquivo['projeto'] as $projetos) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= $projetos['nomeProj'] ?>
        </div>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th style="width:35%;">Nome</th>
                    <th style="width:20%;">CPF</th>
                    <th style="width:20%;">Nascimento</th>
                    <th style="width:20%;">Novo PIS</th>
                    <th style="">Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projetos['clt'] as $value) { ?>
                    <tr id="tr-<?= $value['id_clt'] ?>">
                        <td> <?= $value['id_clt'] ?> <input type="hidden" name="id_clt[]" id="id_clt_<?= $value['id_clt'] ?>" value="<?= $value['id_clt'] ?>"> </td>
                        <td> <?= $value['nome'] ?> <input type="hidden" name="nome[]" id="nome_<?= $value['id_clt'] ?>" value="<?= $value['nome'] ?>"></td>
                        <td> <?= $value['cpf'] ?> <input type="hidden" name="cpf[]" id="cpf_<?= $value['id_clt'] ?>" value="<?= $value['cpf'] ?>"> </td>
                        <td> <?= substr($value['data_nasci'], 0, 2) . '/' . substr($value['data_nasci'], 2, 2) . '/' . substr($value['data_nasci'], 4, 4) ?> </td>
                        <td> <?= $value['novoPIS'] ?> <input type="hidden" name="novo_pis[]" id="novo_pis_<?= $value['id_clt'] ?>" value="<?= $value['novoPIS'] ?>"> </td>
                        <td class="text-center">
                            <?php if (count($value['erro']) > 0) { ?>
                                <button type="button" class="btn btn-warning btn-xs ver_info_erro" data-erros="<?= implode(',', $value['erro']) ?>"><i class="fa fa-info-circle"></i></button>
                            <?php } else { ?>
                                <span class="text-success"><i class="fa fa-check-circle fa-lg"></i></span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
<?php } ?>
<div class="panel panel-default">
    <div class="panel-body">
        <p><strong>Confirma atualiza&ccedil;&atilde;o de PIS para os funcion&aacute;ros acima?</strong></p>
    </div>
    <div class="panel-footer text-right">
        <a href="#" class="btn btn-default">Cancelar</a> 
        <button class="btn btn-primary" type="submit" name="method" value="atualizar">Atualizar PIS</button>
    </div>
</div>

