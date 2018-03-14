<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Projeto:</label> 
        <?php echo montaSelect($projetos, NULL, "id='projeto_" . $key . "'") ?> 
        <label>
            <input type="checkbox" checked="checked" id="sobrescreve_cnpj_<?= $key; ?>" />Usar o CNPJ do projeto
        </label>
    </p>
    <p><label class="first">Ano:</label> 
        <?php //echo montaSelect($anos, date('Y'), " id='ano_" . $key . "' class='request_data' ") ?>        
        <?php echo montaSelect($anos,$data['ano'], " id='ano_" . $key . "' class='request_data' ") ?>        
    </p>
    <p><label class="first">Mês:</label> 
        <?php //echo montaSelect($meses, (date('m')+1), "id='mes_" . $key . "' class='request_data'") ?>        
        <?php echo montaSelect($meses, $data['mes'], "id='mes_" . $key . "' class='request_data'") ?>        
    </p>
    <p>
        <label class="first" for="dataini_<?= $key ?>">Data Inicial</label>
        <input type="text" class="cp_data" id="dataini_<?= $key ?>" value="<?= $primeiro_dia_mes; ?>" /> 
    </p>
    <p>
        <label class="first" for="datafim_<?= $key ?>">Data Final</label>
        <input type="text" class="cp_data" id="datafim_<?= $key ?>" value="<?= $ultimo_dia_mes; ?>"  />
    </p>
    <p>
        <label class="first">Dias Úteis:</label> 
             <input type="text" class="cp_data"  id="dias_uteis_<?= $key ?>" value="<?= $dias_uteis; ?>"  />        
    </p>    
    <p class="controls">        
        <input type="button" class="button" value="Filtrar" onclick="form1()" />   
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>