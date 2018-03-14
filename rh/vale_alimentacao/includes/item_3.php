<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Região:</label> 
        <?php echo montaSelect($regioes, $usuario['id_regiao'], "id='regiao_" . $key . "' onchange='get_table_$key()' ") ?> 
    </p>
    <p>
        <label class="first" for="valor_<?= $key ?>" >Valor</label>
        <input type="text" id="valor_<?= $key ?>" class="money" />        
    </p>
    <p class="controls">        
        <input type="button" class="button" value="Cadastrar" onclick="form3()" />     
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_3.php'; ?>
</div>
