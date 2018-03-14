<form id="form_impostos" method="post" action="controle_impostos.php">
    <input type="hidden" name="id_contrato" value="<?= $_REQUEST['id_prestador'] ?>">
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-success" id="add_tr_imposto"><i class="fa fa-plus"></i> Novo Imposto</button>
            <br>
            <br>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <table class="table table-hover table-condensed table-striped" id="tbl-impostos">
                    <thead>
                        <tr>
                            <th style="width: 45%">Imposto</th>
                            <th style="width: 45%">Aliquota</th>
                            <th>&emsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assoc_impostos as $value) { ?>
                            <tr>
                                <td>
                                    <?= montaSelect($selectImpostos, $value['id_imposto'], 'name="id_imposto[]" class="form-control input-sm validate[required,custom[select]]"'); ?>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control money aliquota input-sm validate[required]" name="aliquota[]" value="<?= $value['aliquota'] ?>">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <input type="hidden" name="id_assoc[]" id="id_assoc" value="<?= $value['id_assoc'] ?>">
                                    <button type="button" class="btn btn-danger btn-sm btn_remove" data-id="<?= $value['id_assoc'] ?>"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                <?= montaSelect($selectImpostos, NULL, 'name="id_imposto[]" class="form-control input-sm validate[required,custom[select]]"'); ?>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control money aliquota input-sm validate[required]" name="aliquota[]">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td class="text-right">
                                <input type="hidden" name="id_assoc[]" id="id_assoc" value="">
                                <button type="button" class="btn btn-danger btn-sm btn_remove" data-id=""><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
