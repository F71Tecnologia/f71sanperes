<div class="panel panel-default">
    <div class="panel-heading">Selecione</div>
    
    <div class="panel-body projeto">
        <div class="form-group">
            <label class="control-label col-xs-3" for="projeto">Projeto selecionado: </label>
            <div class="col-xs-9">
                <select name="projeto" id="projeto" class="form-control">
                    <option value="-1">Selecione um projeto</option>
                    <?php foreach ($projetos as $key1 => $value1) { ?>
                        <?php $selected = ($proj_selected == $key1) ? "selected='selected'" : "" ?>
                        <option value="<?php echo $key1; ?>" <?php echo $selected; ?>><?php echo $value1; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-xs-3" for="projeto">Selecionar Arquivo: </label> 
            <div class="col-xs-9"><input type="file" name="arquivo" class="form-control"  id="arquivo" /></div>
        </div>
    </div>    
    <div class="panel-footer text-right">
        <input type="button" class="btn btn-primary" value="Enviar" />   
    </div>
</div>
<div id="visualizar">
    <?php include_once 'table_'.$key.'.php'; ?>
</div>
