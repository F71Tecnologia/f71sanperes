<?php if (count($relacao_pedidos) > 0) { ?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="tab0">
        <thead>
            <tr>
                <th colspan="9">Rela��o de Pedidos</th>
            </tr>
            <tr>
                <th>C�digo</th>
                <th>M�s</th>
                <th>Ano</th>
                <th>Projeto</th>
                <th>Usu�rio</th>
                <th>Valor</th>
                <th>Visualizar</th>
                <th>Excluir</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($relacao_pedidos as $pedido){ ?>
           <tr id="tr_pedido_<?= $pedido['id_va_pedido']; ?>">
                <td class="center"><?= $pedido['id_va_pedido']; ?></td>
                <td class="center"><?= $pedido['mes']; ?></td>
                <td class="center"><?= $pedido['ano']; ?></td>
                <td class="center"><?= $pedido['projeto']; ?></td>
                <td class="center"><?= $pedido['nome_usuario']; ?></td>
                <td class="center">R$ <?= number_format($pedido['valor_pedido'],2,',','.'); ?></td>
                <td class="center">
                    <a href="javascript:;" title="Visualizar Rela��o"  id="ver_pedido_<?= $pedido['id_va_pedido']; ?>"><img src="../../imagens/file.gif" width="16" height="16" border="0" alt="Visualizar Rela��o" title="Visualizar Rela��o" ></a>
                </td>
                <td class="center">
                    <?php if($pedido['id_funcionario']==$_COOKIE['logado']){ ?>
                    <a href="javascript:;" id="link_del_pedido_<?= $pedido['id_va_pedido']; ?>"><img src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir"></a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <br><br>
    
<?php } else { ?>
<div id="din_<?= $key; ?>">
    <div class="message-box message-yellow">
        <p>N�o h� registros de pedidos realizados.</p>
    </div>
</div>
<?php } ?>
