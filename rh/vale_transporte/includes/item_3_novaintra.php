<fieldset>
    <legend>Selecione</legend>
    <div class="form-group">
        <label class="col-xs-2 control-label">Projeto:</label> 
        <div class="col-xs-7"><?php echo montaSelect($projetos, isset($post_projeto) ? $post_projeto : $usuario['id_projeto'], "id='projeto_$key' class='projeto form-control'") ?></div>
        <label class="col-xs-3 control-label text-left">
            <input type="checkbox" checked="checked" id="sobrescreve_cnpj_<?= $key; ?>" /> Usar o CNPJ do projeto
        </label>
    </div>        
    <div class="form-group">
        <label class="col-xs-2 control-label" for="matricula_<?= $key ?>">Matrícula</label>
        <div class="col-xs-10"><input type="text" id="matricula_<?= $key ?>" class="form-control" /> <small> ( Separe com vírgulas para buscar mais de uma matrícula )</small></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label" for="cpf_<?= $key ?>">CPF</label>
        <div class="col-xs-10"><input type="text" id="cpf_<?= $key ?>" class="form-control" /></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label" for="nome_<?= $key ?>">Nome</label>
        <div class="col-xs-10"><input type="text" id="nome_<?= $key ?>" class="form-control" /></div>
    </div>
    <div class="form-group">
        <div class="col-xs-2"></div>
        <label class="col-xs-6 control-label text-left">
            <input type="checkbox" id="transporte_<?= $key ?>" checked="checked"  /> Somente quem solicitou vale transporte
        </label>
    </div>
    <div class="form-group">
        <div class="col-xs-2"></div>
        <label class="col-xs-6 control-label text-left" >
            <input type="checkbox" id="somente_novos_<?= $key ?>" /> Filtrar por data de entrada
        </label>
    </div>
    <div class="form-group" id="box_data_entrada_<?= $key ?>" style="display:none;">
        <label class="col-xs-2 control-label text-left" >Data de Entrada:</label>
        <div class="col-xs-10">
            <div class="input-group">
                <?php echo montaSelect($meses, date('m'), "id='mese_" . $key . "' class='form-control'") ?>
                <div class="input-group-addon"></div>
                <?php echo montaSelect($anos,date('Y'), "id='ano_" . $key . "' class='form-control'") ?>
            </div>
        </div>
    </div>
    <div class="form-group text-center">        
        <input type="button" class="btn btn-primary" value="Filtrar" onclick="form3()" />     
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>