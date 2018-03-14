<input type="button" class="button" value="Novo Cadastro" id="show_form_cad_<?= $key; ?>" >
<input type="button" class="button" value="Filtrar" onclick="get_table_4()" />
<br><br>
<fieldset id="form_cad_<?= $key; ?>" style="display: none;" >
    <legend>Selecione</legend>
    <p><label class="first">Itinerário:</label> 
        <?php echo montaSelect($itinerarios, NULL, "id='tipo_" . $key . "'") ?> 
    </p>
    <p>
        <label class="first" for="descricao_<?= $key ?>">Descrição</label>
        <input type="text" id="descricao_<?= $key ?>" /> 
    </p>
    <p><label class="first">Concessionária:</label> 
        <?php echo montaSelect( ((!empty($concessionarias)) ? $concessionarias : array('Não especificado')), NULL, "id='concessionaria_" . $key . "'") ?> 
    </p>
    <p><label class="first">Linha:</label> 
        <?php echo montaSelect($linhas, NULL, "id='linha_" . $key . "'") ?> 
    </p>
    <p>
        <label class="first" for="valor_<?= $key ?>" >Valor</label>
        <input type="text" id="valor_<?= $key ?>" class="money" />        
    </p>
    <p class="controls">         
        <input type="button" class="button" value="Cadastrar" onclick="form4()" />     
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_4.php'; ?>
</div>
