<?php if(isset($relacao_clt_movimento)){ ?>
<a href="javascript:;" onclick="voltar_lista_pedido()">Voltar</a><br><br>

<p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tab0.1', 'Relatorio')" value="Exportar para Excel" class="exportarExcel"></p>

<?php } ?>
<?php if (count($relacao_funcionarios) > 0) { 
    
    
    $arr_clt_pedido = array();
    $arr_clt_zerado = array();
    
    foreach($relacao_funcionarios as $funcionario){ 
        if($funcionario['valor_recarga']>0){
            $arr_clt_pedido[] = $funcionario;
        }else{
            $arr_clt_zerado[] = $funcionario;
        }
    }
    
    ?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" data-form="<?= $obj_form_data; ?>"  id="relacao_pedido" >
        <thead>
            <tr>
                <th colspan="9">Relação de Funcionários para o Pedido <?= $competencia.'  ('.$data_ini_fim.') '; ?></th>
            </tr>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Dias Úteis</th>
                <th>Valor Diário</th>
                <th>Recarga</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($arr_clt_pedido as $funcionario){ ?>
           <tr>
                <td class="center"><?= $funcionario['id_clt']; ?></td>
                <td><?= $funcionario['nome_funcionario']; ?></td>
                <td class="center"><?= $funcionario['dias_uteis']; ?></td>
                <td class="center">R$ <?= $funcionario['valor_diario']; ?></td>
                <td class="center">R$ <?= $funcionario['valor_recarga']; ?></td>           
            </tr>
            <?php } ?>
        </tbody>   
    </table>
    <br>
    <div class="txright">
        <!--<input type="button" value="Criar Arquivo Exportação AELO"  onclick="arquivo_aelo(<?php //= $id_pedido; ?>)" id="bt_arquivo_aelo" />-->
        <input type="button" value="Fechar Pedido"  id="cria_pedido" />
    </div>
    <p class="txright">Valor total do pedido: R$ <?= number_format($valor_total_pedido,2,',','.'); ?></p>
    <p class="txright"><?= count($arr_clt_zerado); ?> registros descartados do total de <?= count($relacao_funcionarios); ?>. <a href="javascript:;" onclick="$('#relacao_fora_pedido').toggle();" >Clique para Visualizar Relação.</a></p>
    
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="display: none;" id="relacao_fora_pedido">
        <thead>
            <tr>
                <th colspan="9">Relação de Funcionários para sem Vínculos em Tarifas</th>
            </tr>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Dias Úteis</th>
                <th>Valor Diário</th>
                <th>Recarga</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($arr_clt_zerado as $funcionario){ ?>
           <tr>
                <td class="center"><?= $funcionario['id_clt']; ?></td>
                <td><?= $funcionario['nome_funcionario']; ?></td>
                <td class="center"><?= $funcionario['dias_uteis']; ?></td>
                <td class="center">R$ <?= $funcionario['valor_diario']; ?></td>
                <td class="center">R$ <?= $funcionario['valor_recarga']; ?></td>
                <?php if(isset($relacao_clt_movimento)){ ?>
<!--                <td class="center">R$ <?php //= number_format($funcionario['salario'],2,',','.'); ?></td>
                <td class="center">R$ <?php //= number_format($funcionario['salario_porcentagem'],2,',','.'); ?></td>
                <td class="center">R$ <?php //= number_format($funcionario['desconto_movimento'],2,',','.'); ?></td>-->
                <?php } ?>                
            </tr>
            <?php } ?>
        </tbody>   
    </table>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de pedidos realizados.</p>
    </div>
<?php
} ?>
<div  id="table_fake">
    
</div>