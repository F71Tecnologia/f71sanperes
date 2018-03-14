<a href="javascript:;" onclick="get_table_0();" title="Voltar"><img src="../../imagens/seta_esquerda.jpg" alt="Voltar"><span style="position: absolute; line-height: 44px;margin-left: 8px;">Voltar</span></a>
<br><br>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%">
    <thead>
        <tr>
            <th colspan="6"><?= $relacao[0]['nome_projeto'].' '.$relacao[0]['mes'].'/'.$relacao[0]['ano']; ?> - RELAÇÃO DE FUNCIONÁRIOS BENEFICIADOS</th>
        </tr>
        <tr>
            <th>ID </th>
            <th>Nome</th>
            <th>Dias Úteis</th>
            <th>Valor Diário</th>
            <th>Total</th>
            <th>Assinatura</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cont = 0;
        $total = 0;
        if (!empty($relacao)) {
            foreach($relacao as $linha){
                $cont++;
                $total += $linha['dias_uteis'] * $linha['vt_valor_diario'];
                ?>
                <tr class="<?= ($cont % 2 == 0) ? 'odd' : 'even' ?>">
                    <td class="center"><?= $linha['id_clt']; ?></td>
                    <td><?= $linha['nome'] ?></td>
                    <td class="center" ><?= $linha['dias_uteis']; ?></td>
                    <td  class="center" >R$ <?= number_format($linha['vt_valor_diario'], 2, ',', '.'); ?></td>
                    <td  class="center" >R$ <?= number_format($linha['dias_uteis'] * $linha['vt_valor_diario'], 2, ',', '.'); ?></td>
                    <td style="width: 200px; background: #FFF;">&nbsp;</td>
                </tr>
                <?php
                
            }
        }
        ?>
        <tr>
            <td colspan="6" style="text-align: right;">
                <dl style="font-weight: bolder; padding: 20px 5px 0">
                    <dd>TOTAL DE  BENEFICIADOS: <?= $cont; ?></dd>
                    <dd>VALOR TOTAL: R$ <?= number_format($total, 2, ',', '.'); ?></dd>
                </dl>
            </td>
        </tr>
    </tbody>
</table>