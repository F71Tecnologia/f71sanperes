<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Projeto:</label> 
        <?php echo montaSelect($projetos, NULL, "id='projeto_" . $key . "' class='projeto' ") ?> 
        <label>
            <input type="checkbox" checked="checked" id="sobrescreve_cnpj_<?= $key; ?>" />Usar o CNPJ do projeto
        </label>
    </p>
    <p><label class="first">Tipo:</label> 
        <?php echo montaSelect($array_tipos_registros, NULL, "id='tipo_registro_" . $key . "'") ?>        
    </p>
    <p>
        <label class="first" for="matricula_<?= $key ?>">Matr�cula</label>
        <input type="text" id="matricula_<?= $key ?>" /><small> ( Separe com v�rgulas para buscar mais de uma matr�cula )</small>
        
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
        <input type="button" class="button" value="Filtrar" onclick="form2()" />     
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>
