<input type="button" class="button" value="Novo Cadastro" id="show_form_cad_<?= $key; ?>" >
<input type="button" class="button" value="Filtrar" onclick="get_table_<?= $key; ?>()" />
<br><br>
<fieldset id="form_cad_<?= $key; ?>">
    <legend>Cadastrar Concessionárias</legend>
    <p><label class="first">Tipo:</label> 
        <?php echo montaSelect($tipos_concenssionarias, NULL, "id='tipo_" . $key . "'") ?> 
    </p>
    <p>
        <label class="first" for="nome_<?= $key ?>">Nome</label>
        <input type="text" id="nome_<?= $key ?>" /> 
    </p>    
    <p class="controls">        
        <input type="button" class="button" value="Cadastrar" onclick="form5()" />     
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_5.php'; ?>
</div>
