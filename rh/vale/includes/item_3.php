<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Região:</label> 
        <?php echo montaSelect($regioes, $usuario['id_regiao'], " id='get_valores_diarios' ") ?> 
        <input type="button" value="Novo Cadastro" id="show_form_cad_<?= $key ?>">
    </p>
</fieldset>
<br>
<fieldset id="form_cad_<?= $key ?>" style="display: none;">
    <legend>Cadastrar Valor Diário</legend>
    <p><label class="first">Região:</label> 
        <span id="nome_regiao_<?= $key ?>"><?= $regioes[$usuario['id_regiao']]; ?></span>
    </p>
    <p>
        <label class="first" for="valor_<?= $key ?>" >Digite o Valor</label>
        <input type="text" id="valor_<?= $key ?>" class="money" />
    </p>
    <p class="controls">        
        <input type="button" class="button" value="Cadastrar" id="cadastrar_valor_diario" />
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_3.php'; ?>
</div>
