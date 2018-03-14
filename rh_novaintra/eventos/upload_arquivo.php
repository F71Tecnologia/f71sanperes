<?php header('Content-type: text/html; charset=ISO-8859-1'); ?>
<form action="../../include/upload_atestado.php" method="post" id="form_up_evento" enctype="multipart/form-data">

    <div class="row">
        <div class="form-group">

            <div class="col-lg-7">
                <input type="file" name="atestado" id="atestado" class="validate[required,custom[docsType]]">
            </div>
            <div class="col-lg-4">
                <input type="submit" class="btn btn-primary" value="Salvar">
            </div>
        </div>
    </div>

    <input type="hidden" name="id_evento" id="id_evento" value="">
    <input type="hidden" name="reg" id="reg" value="<?= sprintf('%03d', $row_clt['id_regiao']); ?>">
    <input type="hidden" name="projeto" id="projeto" value="<?= sprintf('%03d', $row_clt['id_projeto']); ?>">
    <input type="hidden" name="ID_participante" id="id_participante" value="<?= sprintf('%03d', $row_clt['id_clt']); ?>">
    <input type="hidden" name="tipo_contratacao" id="tipo_contratacao" value="2">

    <progress max="100" value="0">
        <!-- Browsers that support HTML5 progress element will ignore the html inside `progress` element. Whereas older browsers will ignore the `progress` element and instead render the html inside it. -->
        <div class="progress-bar">
            <span style="width:0%"></span>
        </div>
    </progress>
    <div id="status" class="hidden back-green"></div>
</form>