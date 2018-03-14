<a href="javascript:;" onclick="get_table_0();" title="Voltar"><img src="../../imagens/seta_esquerda.jpg" alt="Voltar"><span style="position: absolute; line-height: 44px;margin-left: 8px;">Voltar</span></a>
<br><br>
<?php if(empty($res_movimento) || (!in_array($res_movimento['categoria'], array('DEBITO','DESCONTO'))) ){
    $alert['message'] = 'Não existe movimento configurado! Por favor entre em contato com o suporte!';
    include 'includes/box_message.php';
    
}else{
?>


<?php if (!empty($relacao)) { ?>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%">
    <thead>
        <tr>
            <th colspan="6">PED.<?= $relacao[0]['id_vt_pedido'].' - '.$relacao[0]['nome_projeto'].' '.$relacao[0]['mes'].'/'.$relacao[0]['ano']; ?> - RELAÇÃO DE FUNCIONÁRIOS PARA DESCONTO EM FOLHA</th>
        </tr>
        <tr>
            <th>ID </th>
            <th>Nome</th>
            <th>Salário</th>
            <th>Recarga no Mês</th>
            <th>Desconto (Até %6)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cont = 0;
        $total = 0;
        
            foreach($relacao as $linha){
                $cont++;
                $total += $linha['recarga_mes'];
                $total_desconto += $linha['desconto_folha'];
                ?>
                <tr class="<?= ($cont % 2 == 0) ? 'odd' : 'even' ?>">
                    <td class="center"><?= $linha['id_clt']; ?></td>
                    <td><?= $linha['nome'] ?></td>
                    <td class="center">R$ <?= number_format($linha['salario'],2,',','.'); ?></td>
                    <td class="center">R$ <?= number_format($linha['recarga_mes'],2,',','.'); ?></td>
                    <td class="center">R$ <?= number_format($linha['desconto_folha'],2,',','.'); ?></td>
                </tr>
                <?php
                
            }
            ?>
       
    </tbody>
    <tfoot>
        <tr>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th class="center">R$ <?= number_format($total,2,',','.'); ?></th>
            <th class="center">R$ <?= number_format($total_desconto,2,',','.'); ?></th>
        </tr>
    </tfoot>
</table>
<br>
<div id="bts_controller_03" data-flag="4" style="text-align: right;">
    <?php
    $alert['message'] = 'ATENÇÃO! AO SALVAR IRÁ LANÇAR MOVIMENTO DE '.$res_movimento['categoria'].' - COD.'.$res_movimento['cod'].' - '.$res_movimento['descicao'].' PARA TODOS DA RELAÇÃO PARA DESCONTO EM FOLHA - COMPETÊNCIA '.$relacao[0]['mes'].'/'.$relacao[0]['ano'];
    include 'includes/box_message.php';
    ?>
    <input type="button" value="Cancelar"  onclick="get_table_0();">
    <input type="button" value="Salvar" id="bt_lancar_movimentos"  onclick="lancar_movimentos(<?= $relacao[0]['id_vt_pedido']; ?>);">
</div>
<?php } }  ?>