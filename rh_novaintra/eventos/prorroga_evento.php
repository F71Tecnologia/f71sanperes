<?php header('Content-type: text/html; charset=ISO-8859-1'); ?>
<!-- trecho incorporado no bootstrap-modal da página acao_eventos_novo.php -->
<form class="form-horizontal" id="form-prorrogar" role="form">
    <input type="hidden" name="id_evento" id="id_evento" value="<?= $campos_eventos['id_evento'] ?>" />
    <input type="hidden" name="id_user" id="id_user" value="<?= $_COOKIE['logado'] ?>" />
    <input type="hidden" name="data_retorno2" id="data_retorno2" value="<?= $campos_eventos['data_retorno'] ?>" />

    <div class="row">
        <div class="form-group">
            <label for="dias2" class="col-lg-4 control-label">Quantidade de dias:</label>
            <div class="col-lg-6">
                <input name="dias2" id="dias2" min="0" class="form-control dias" type="number">
                <p class="help-block">A partir da data atual de retorno.</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label for="data_prorrogada" class="col-lg-4 control-label">Data de Prorrogação:</label>
            <div class="col-lg-6">
                <div class="input-group">
                    <input name="data_prorrogada" id="data_prorrogada" class="form-control data" type="text" value="<?= $valor_campo ?>">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <label for="obs" class="col-lg-4 control-label">Motivo:</label>
            <div class="col-lg-6">
                <textarea name="obs" id="obs" class="form-control" rows="3" type="text"><?= $campos_eventos['obs'] ?></textarea>
            </div>
        </div>
    </div>
</form>