<?php header('Content-Type: text/html; charset=utf-8'); ?>


<table class="table table-striped table-hover table-bordered">
    <thead>
        <tr>
            <th rowspan="2">N&ordm; Reg. F&iacute;sico</th>
            <th rowspan="2">Cod. Horigem / Destino</th>
            <th rowspan="2">NIS/PIS</th>
            <th colspan="2">Registro L&oacute;gico</th>
            <th colspan="4">Campo</th>
            <th colspan="3">Retorno</th>
        </tr>
        <tr>
            <th>Tipo</th>
            <th>Seq.</th>
            <th>Cod.</th>
            <th>Seq.</th>
            <th>Flag.</th>
            <th>Conte&uacute;do</th>
            <th>NIS Ativo</th>
            <th  colspan="2">Cod.</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($array_arquivo as $key => $value) { ?>
            <tr id="tr-<?= $key ?>">
                <td> <?= $value['numOrdem'] ?> </td>
                <td> <?= $value['CodOrigem'] ?> </td>
                <td> <?= $value['NIS'] ?> </td>
                <td> <?= $value['TipoRegLogico'] ?> </td>
                <td> <?= $value['SeqRegLogico'] ?> </td>
                <td> <?= $value['CodCampo'] ?> </td>
                <td> <?= $value['SeqCampo'] ?> </td>
                <td> <?= $value['FlagCampo'] ?> </td>
                <td> <?= $value['conteudoCampo'] ?> </td>
                <td> <?= $value['NISAtivo'] ?> </td>
                <td> <?= $value['COdRetorno'] ?> </td>
                <td>
                    <?php if($value['COdRetorno'] != '0000'){ ?>
                    <button type="button" class="btn btn-info btn-xs ver_info_erro" data-erros="<?= $value['COdRetorno'] ?>"><i class="fa fa-info-circle"></i></button>
                    <?php } ?>
                </td>

            </tr>
        <?php } ?>
    </tbody>
</table>