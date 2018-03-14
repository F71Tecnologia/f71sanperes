<fieldset>
    <legend>Selecione</legend>

    <div class="projeto">
        <label for="projeto">Projeto selecionado: </label>
        <select name="projeto" id="projeto">
            <option value="-1">Selecione um projeto</option>
            <?php foreach ($projetos as $key => $value) { ?>
                <?php $selected = ($proj_selected == $key) ? "selected='selected'" : "" ?>
                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="projeto">
        <label style="width: 148px; vertical-align: middle;">Selecionar Arquivo: </label> 
        <input type="file" name="arquivo" class=""  id="arquivo"  />        
    </div>    
    <p class="controls">        
        <input type="button" class="button" value="Enviar" />   
    </p>
</fieldset>
<br><br>
<div id="visualizar"></div>
