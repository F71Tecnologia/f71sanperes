<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Região:</label> 
        <?= montaSelect($regioes, $usuario['id_regiao'], 'id="regiao_'.$key.'"'); ?>
    </p>
    <p><label class="first">Projeto:</label> 
        <?= montaSelect($projetos, $projetos_keys[0], ' id="projeto_'.$key.'"'); ?>
    </p>
    <p class="controls">        
        <input type="button" class="button" value="Filtrar" onclick="getTable('0');">
    </p>
</fieldset>
<br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_pedidos.php'; ?>
</div>