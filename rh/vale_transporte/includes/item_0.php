<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Projeto:</label> 
        <?php echo montaSelect($projetos, ((isset($post_projeto) && !empty($post_projeto)) ? $post_projeto : $usuario['id_projeto']), "id='projeto_" . $key . "' class='projeto' onchange='get_table_0();' ") ?>
    </p>
</fieldset>
<br>
<div id="din_<?= $key; ?>">
    <?php
    include_once 'table_0.php';
    ?>
</div>