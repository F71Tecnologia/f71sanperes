<fieldset>
    <legend>Selecione</legend>
    <div class="form-group">
        <label class="col-xs-1 control-label">Projeto:</label> 
        <div class="col-xs-11"><?php echo montaSelect($projetos, ((isset($post_projeto) && !empty($post_projeto)) ? $post_projeto : $usuario['id_projeto']), "id='projeto_" . $key . "' class='projeto form-control' onchange='get_table_0();' ")?></div>
    </div>
</fieldset>
<br>
<div id="din_<?= $key; ?>">
    <?php include_once 'table_0_novaintra.php'; ?>
</div>