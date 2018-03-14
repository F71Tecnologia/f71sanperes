<input type="button" class="btn btn-success" value="Novo Cadastro" id="show_form_cad_<?= $key; ?>" >
<input type="button" class="btn btn-primary" value="Filtrar" onclick="get_table_<?= $key; ?>()" />
<br><br>
<fieldset id="form_cad_<?= $key; ?>">
    <legend>Cadastrar Concessionárias</legend>
    <div class="form-group">
        <label class="col-xs-1 control-label">Tipo:</label> 
        <div class="col-xs-11"><?php echo montaSelect($tipos_concenssionarias, NULL, "id='tipo_" . $key . "' class='form-control'") ?></div>
    </div>
    <div class="form-group">
        <label class="col-xs-1 control-label" for="nome_<?= $key ?>">Nome</label>
        <div class="col-xs-11"><input type="text" class="form-control" id="nome_<?= $key ?>" /></div>
    </div>
    <div class="form-group">        
        <input type="button" class="btn btn-primary" value="Cadastrar" onclick="form5()" />
    </div>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_5.php'; ?>
</div>
