<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Projeto:</label> 
        <?php echo montaSelect($projetos, isset($post_projeto) ? $post_projeto : $usuario['id_projeto'], "id='projeto_$key' class='projeto' ") ?>
        <label>
            <input type="checkbox" <?= ($cnpj_master_por_padrao) ? '' :' checked="checked" '; ?> id="sobrescreve_cnpj_<?= $key; ?>" />Usar o CNPJ do projeto
        </label>
    </p>        
    <p>
        <label class="first" for="matricula_<?= $key ?>">Matrícula</label>
        <input type="text" id="matricula_<?= $key ?>" /> <small> ( Separe com vírgulas para buscar mais de uma matrícula )</small>
    </p>
    <p>
        <label class="first" for="cpf_<?= $key ?>">CPF</label>
        <input type="text" id="cpf_<?= $key ?>" /> 
        
    </p>
    <p>
        <label class="first" for="nome_<?= $key ?>">Nome</label>
        <input type="text" id="nome_<?= $key ?>" /> 
    </p> 
    <p>
        <label style="margin-left: 130px;" >
            <input type="checkbox" id="transporte_<?= $key ?>" checked="checked"  /> Somente quem solicitou vale transporte
        </label>
    </p>
    <p>
        <label style="margin-left: 130px;" >
            <input type="checkbox" id="somente_novos_<?= $key ?>" /> Filtrar por data de entrada
        </label>
    </p>
    <p id="box_data_entrada_<?= $key ?>" style="display:none;"><label class="first">Data de Entrada:</label> 
        <?php echo montaSelect($meses, date('m'), "id='mese_" . $key . "'") ?>        
        <?php echo montaSelect($anos,date('Y'), "id='ano_" . $key . "'") ?>        
    </p>
    <p class="controls">        
        <input type="button" class="button" value="Filtrar" onclick="form7()" />     
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>