<input type="button" class="btn btn-success" value="Novo Cadastro" id="show_form_cad_<?= $key; ?>" >
<input type="button" class="btn btn-primary" value="Filtrar" onclick="get_table_4()" />
<br><br>
<fieldset id="form_cad_<?= $key; ?>" style="display: none;" >
    <legend>Selecione</legend>
    <div class="form-group">
        <label class="col-xs-2 control-label">Itinerário:</label> 
        <div class="col-xs-10"><?php echo montaSelect($itinerarios, NULL, "id='tipo_" . $key . "' class='form-control'") ?></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label" for="descricao_<?= $key ?>">Descrição</label>
        <div class="col-xs-10"><input type="text" id="descricao_<?= $key ?>" class="form-control" /></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label">Concessionária:</label> 
        <div class="col-xs-10"><?php echo montaSelect( ((!empty($concessionarias)) ? $concessionarias : array('Não especificado')), NULL, "id='concessionaria_" . $key . "' class='form-control'") ?></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label">Linha:</label> 
        <div class="col-xs-10"><?php echo montaSelect($linhas, NULL, "id='linha_" . $key . "' class='form-control'") ?></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label" for="valor_<?= $key ?>" >Valor:</label>
        <div class="col-xs-10"><input type="text" id="valor_<?= $key ?>" class="form-control money2" /></div>
    </div>
    <div class="form-group">         
        <input type="button" class="btn btn-primary" value="Cadastrar" onclick="form4()" />     
    </div>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_4.php'; ?>
</div>
