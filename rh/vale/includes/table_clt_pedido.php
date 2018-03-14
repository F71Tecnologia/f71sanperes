<a href="javascript:;" onclick="getTable('0');" title="Voltar"><img src="../../imagens/seta_esquerda.jpg" alt="Voltar"><span style="position: absolute; line-height: 44px;margin-left: 8px;">Voltar</span></a>
<br><br>
<?php

//    echo '<pre>';
//    print_r($relacao);
//    echo '</pre>';

?>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" style="width: 100%">
    <thead>
        <tr>
            <th colspan="7"> PED.<?= $info['id_va_pedido'].' - PROJ.'.$info['id_projeto'].' - '.$info['projeto'].' - COMPETÊNCIA '.$info['mes'].'/'.$info['ano']; ?> </th>
        </tr>
        <tr>
            <th>ID </th>
            <th>Matrícula </th>
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
                $total += $linha['valor_recarga'];
                ?>
                <tr class="<?= ($cont % 2 == 0) ? 'odd' : 'even' ?>">
                    <td class="center"><?= $linha['id_clt']; ?></td>
                    <td class="center"><?= $linha['matricula']; ?></td>
                    <td><?= $linha['nome_funcionario'] ?></td>
                    <td class="center" ><?= $linha['dias_uteis']; ?></td>
                    <td  class="center" >R$ <?= number_format($linha['valor_diario'], 2, ',', '.'); ?></td>
                    <td  class="center" >R$ <?= number_format($linha['valor_recarga'], 2, ',', '.'); ?></td>
                    <td style="width: 200px; background: #FFF;">&nbsp;</td>
                </tr>
                <?php                
            }
        }
        ?>
        <tr>
            <td colspan="7" style="text-align: right;">
                <dl style="font-weight: bolder; padding: 20px 5px 0">
                    <dd>TOTAL DE  BENEFICIADOS: <?= count($relacao); ?></dd>
                    <dd>VALOR TOTAL: R$ <?= number_format($total, 2, ',', '.'); ?></dd>
                    
                    
                    <dt style="margin: 20px 0 8px 0">
                        <a href="javascript:;"  data-toggle="modal" data-target="#myModal"  title="Exportar Pedido"  onclick="modalExportarPedido(<?= $info['id_va_pedido']; ?>)">Exportar Pedido <img src="../../imagens/icones/icon-download.png" width="16" height="16" border="0" alt="Exportar Pedido" title="Exportar Pedido" ></a>
                    </dt>
                    
                    <?php if(!empty($arquivos)){ ?>
                    <dt style="margin: 20px 0 8px 0" class="din_exportacao">Foram gerados <?= count($arquivos); ?> arquivo<?= (count($arquivos)>1) ? 's' : ''; ?> para download.</dt>
                    <?php  foreach ($arquivos as $arquivo) { ?>
                        <dd class="din_exportacao">
                            <a href="?tipo=<?= $arquivo['tipo']; ?>&download=<?= $arquivo['download']; ?>&name_file=<?= $arquivo['name_file']; ?>" title="Baixar">BAIXAR ARQUIVO <?= ($k + 1); ?> : <?= $arquivo['name_file']; ?></a>
                            <a href="?tipo=<?= $arquivo['tipo']; ?>&download=<?= $arquivo['download']; ?>'&name_file=<?= $arquivo['name_file']; ?>" title="Baixar"> <img src="/intranet/imagens/icones/icon-download.png" alt="Baixar"></a>
                        </dd>
                    <?php } } ?>
                </dl>
            </td>
        </tr>
    </tbody>
</table>