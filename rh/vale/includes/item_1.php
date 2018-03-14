<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Região:</label> 
        <?= montaSelect($regioes, $usuario['id_regiao'], 'id="regiao_'.$key.'" '); ?>
    </p>
    <p><label class="first">Projeto:</label> 
        <?php echo montaSelect($projetos, NULL, "id='projeto_" . $key . "' ") ?> 
        <label>
            <input type="checkbox" checked="checked" id="sobrescreve_cnpj_<?= $key; ?>"  />Usar o CNPJ do projeto
        </label>
    </p>
    <p><label class="first">Ano:</label> 
        <?php echo montaSelect($anos, date('Y'), " id='ano_" . $key . "' class='request_data' ") ?>        
    </p>
    <p><label class="first">Mês:</label> 
        <?php echo montaSelect($meses, $mes, "id='mes_" . $key . "' class='request_data'") ?>   
    </p>
    <p>
        <label class="first" for="dataini_<?= $key ?>">Data Inicial</label>
        <input type="text" class="cp_data" id="dataini_<?= $key ?>" value="<?= CalendarioClass::getDataInicial(); ?>" /> 
    </p>
    <p>
        <label class="first" for="datafim_<?= $key ?>">Data Final</label>
        <input type="text" class="cp_data" id="datafim_<?= $key ?>" value="<?= CalendarioClass::getDataFinal(); ?>"  />
    </p>
    <p>
        <label class="first">Dias Úteis:</label> 
        <input type="text"  id="dias_uteis_<?= $key ?>" value="<?= CalendarioClass::getTotalDiasUteis(); ?>" />        
    </p>    
    <p class="controls">        
        <input type="button" class="button" value="Filtrar" id="form<?= $key ?>" />   
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>
<script>
    $('.cp_data').datepicker({
        onSelect: function(){
            
            var arr_ini = $('#dataini_1').val().split('/');
            var arr_fim = $('#datafim_1').val().split('/');
            
            var dias_base = arr_ini[0];
            var mes_base = arr_ini[1];
            var ano_base = arr_ini[2];
            
            var dias_base_final = arr_fim[0];
            var mes_base_final = arr_fim[1];
            var ano_base_final = arr_fim[2];
            
            console.log(arr_ini);
            console.log(arr_fim);
            
            $.post(window.location, 
                    {
                        acao: 'calcula_datas', dia_base: dias_base, mes_base: mes_base, ano_base: ano_base,
                        dia_base_final: dias_base_final, mes_base_final: mes_base_final, ano_base_final: ano_base_final
                    }
                    , function(data) {
//                        $('#dataini_1').val(data.inicial.dia+'/'+data.inicial.mes+'/'+data.inicial.ano);
//                        $('#datafim_1').val(data.final.dia+'/'+data.final.mes+'/'+data.final.ano);
                        $('#dias_uteis_1').val(data.total_dias_uteis);
                        $('.cp_data').removeAttr('disabled');
            }, 'json');
        }
    });
</script>