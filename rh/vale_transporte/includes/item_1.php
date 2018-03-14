<fieldset>
    <legend>Selecione</legend>
    
    
    <p><label class="first">Projeto:</label> 
        <?php echo montaSelect($projetos, NULL, "id='projeto_" . $key . "' class='projeto' ") ?> 
        <label>
            <input type="checkbox" checked="checked" id="sobrescreve_cnpj_<?= $key; ?>" />Usar o CNPJ do projeto
        </label>
    </p>
    <p><label class="first">Ano:</label> 
        <?php echo montaSelect($anos, date('Y'), " id='ano_" . $key . "'  class='request_data' ") ?>      
    </p>
    <p><label class="first">Mês:</label> 
        <?php echo montaSelect($meses, (date('m')+1), "id='mes_" . $key . "' class='request_data'") ?>        
    </p>
    <p>
        <label class="first" for="dataini_<?= $key ?>">Data Inicial</label>
        <input type="text" class="cp_data date_f" id="dataini_<?= $key ?>" value="<?= $data_calendario['inicial']['dia'].'/'.$data_calendario['inicial']['mes'].'/'.$data_calendario['inicial']['ano']; ?>" /> 

    </p>
    <p>
        <label class="first" for="datafim_<?= $key ?>">Data Final</label>
        <input type="text" class="cp_data" id="datafim_<?= $key ?>" value="<?= $data_calendario['final']['dia'].'/'.$data_calendario['final']['mes'].'/'.$data_calendario['final']['ano']; ?>"  />
    </p>
    <p>
        <label class="first">Dias Úteis:</label> 
             <input type="text" class="cp_data"  id="dias_uteis_<?= $key ?>" value="<?= $data_calendario['total_dias_uteis']; ?>"  />   
    </p>    
    <p class="controls">        
        <input type="button" class="button" value="Gerar Pedido" onclick="form1()" />   
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
