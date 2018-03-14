<a href="javascript:;" onclick="get_table_0();" title="Voltar"><i class="fa fa-arrow-circle-left" alt="Voltar"></i> Voltar</a>
<br><br>
<?php if(empty($res_movimento) || (!in_array($res_movimento['categoria'], array('DEBITO','DESCONTO'))) ){
    $alert['message'] = 'Não existe movimento configurado! Por favor entre em contato com o suporte!';
    include 'includes/box_message.php';
}else{

if (!empty($relacao)) { ?>

<table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th colspan="6">PED.<?= $relacao[0]['id_vt_pedido'].' - '.$relacao[0]['nome_projeto'].' '.$relacao[0]['mes'].'/'.$relacao[0]['ano']; ?> - RELAÇÃO DE FUNCIONÁRIOS PARA DESCONTO EM FOLHA</th>
        </tr>
        <tr class="active valign-middle">
            <th class="text-center">ID </th>
            <th class="text-left">Nome</th>
            <th class="text-center">Salário</th>
            <th class="text-center">Recarga no Mês</th>
            <th class="text-center">Desconto (Até %6)</th>
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
        <?php } ?>
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
<div class="alert alert-warning text-sm">
    <?='ATENÇÃO! AO SALVAR IRÁ LANÇAR MOVIMENTO DE '.$res_movimento['categoria'].' - COD.'.$res_movimento['cod'].' - '.$res_movimento['descicao'].' PARA TODOS DA RELAÇÃO PARA DESCONTO EM FOLHA - COMPETÊNCIA '.$relacao[0]['mes'].'/'.$relacao[0]['ano']?>
</div>
<div id="bts_controller_03" data-flag="4" style="text-align: right;">
    <input type="button" class="btn btn-default" value="Cancelar"  onclick="get_table_0();">
    <input type="button" class="btn btn-primary" value="Salvar" id="bt_lancar_movimentos"  onclick="lancar_movimentos(<?= $relacao[0]['id_vt_pedido']; ?>);">
</div>
<?php } }  ?>