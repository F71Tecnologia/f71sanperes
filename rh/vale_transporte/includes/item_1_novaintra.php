<fieldset>
    <legend>Selecione</legend>
    <div class="form-group">
        <label class="col-xs-2 control-label">Projeto:</label> 
        <div class="col-xs-7"><?=montaSelect($projetos, NULL, "id='projeto_$key' class='projeto form-control' ") ?></div>
        <label class="col-xs-3 control-label text-left no-padding-hr">
            <input type="checkbox" checked="checked" id="sobrescreve_cnpj_<?=$key?>" /> Usar o CNPJ do projeto
        </label>
    </div>
    
    <div class="form-group">
        <label class="col-xs-2 control-label">Ano:</label> 
        <div class="col-xs-10"><?php echo montaSelect($anos, date('Y'), " id='ano_" . $key . "'  class='request_data form-control' ") ?></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label">Mês:</label> 
        <div class="col-xs-10"><?php echo montaSelect($meses, (date('m')+1), "id='mes_" . $key . "' class='request_data form-control'") ?></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label" for="dataini_<?= $key ?>">Data Inicial:</label>
        <div class="col-xs-10"><input type="text" class="cp_data date_f form-control" id="dataini_<?= $key ?>" value="<?= $data_calendario['inicial']['dia'].'/'.$data_calendario['inicial']['mes'].'/'.$data_calendario['inicial']['ano']; ?>" /></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label" for="datafim_<?= $key ?>">Data Final:</label>
        <div class="col-xs-10"><input type="text" class="cp_data form-control" id="datafim_<?= $key ?>" value="<?= $data_calendario['final']['dia'].'/'.$data_calendario['final']['mes'].'/'.$data_calendario['final']['ano']; ?>"  /></div>
    </div>
    <div class="form-group">
        <label class="col-xs-2 control-label">Dias Úteis:</label> 
        <div class="col-xs-10"><input type="text" class="form-control"  id="dias_uteis_<?= $key ?>" value="<?= $data_calendario['total_dias_uteis']; ?>" readonly="" /></div>
    </div>    
    <div class="form-group text-center">        
        <input type="button" class="btn btn-primary" value="Gerar Pedido" onclick="form1()" />   
    </div>
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
            
            $.post(window.location, {
                acao: 'calcula_datas', dia_base: dias_base, mes_base: mes_base, ano_base: ano_base,
                dia_base_final: dias_base_final, mes_base_final: mes_base_final, ano_base_final: ano_base_final
            } , function(data) {
                $('#dias_uteis_1').val(data.total_dias_uteis);
                $('.cp_data').removeAttr('disabled');
            }, 'json');
        }
    });
</script>