<fieldset>
    <legend>Selecione</legend>
    <p><label class="first">Região:</label> 
        <?= montaSelect($regioes, $usuario['id_regiao'], 'id="regiao_'.$key.'"'); ?>
    </p>
    <p><label class="first">Projeto:</label> 
        <?php echo montaSelect($projetos, isset($post_projeto) ? $post_projeto : $usuario['id_projeto'], "id='projeto_" . $key . "'") ?>
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
            <input type="checkbox" id="alimentacao_<?= $key ?>" checked="checked"  /> Somente quem solicitou vale alimentação
        </label>
    </p>
    <p>
        <label style="margin-left: 130px;" >
            <input type="checkbox"  onclick="$('#box_data_entrada_<?= $key ?>').toggle();" id="data_entrada_<?= $key ?>"  class="form_data"  /> Filtrar por data de entrada
        </label>
    </p>
    <p id="box_data_entrada_<?= $key ?>" style="display:none;"><label class="first">Data de Entrada:</label> 
        <?php echo montaSelect($meses, date('m'), "id='mes_" . $key . "'") ?>        
        <?php echo montaSelect($anos,date('Y'), "id='ano_" . $key . "'") ?>        
    </p>
    <p class="controls">        
        <input type="button" class="button" value="Filtrar" id="form4" />     
    </p>
</fieldset>
<br><br>
<div id="din_<?= $key; ?>"></div>