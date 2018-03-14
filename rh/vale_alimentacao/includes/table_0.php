<?php if (count($relacao_pedidos) > 0) { ?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="tab0">
        <thead>
            <tr>
                <th colspan="9">Relação de Pedidos</th>
            </tr>
            <tr>
                <th>Código</th>
                <th>Mês</th>
                <th>Ano</th>
                <th>Projeto</th>
                <th>Usuário</th>
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
                    <a href="javascript:;" title="Visualizar Relação" onclick="visualizar_pedido(<?= $pedido['id_va_pedido']; ?>);"  id="link_mov_pedido_<?= $pedido['id_va_pedido']; ?>"><img src="../../imagens/file.gif" width="16" height="16" border="0" alt="Visualizar Relação" title="Visualizar Relação" ></a>
                </td>
                <td class="center">
                    <?php if($pedido['id_funcionario']==$_COOKIE['logado']){ ?>
                    <a href="javascript:;" onclick="if(confirm('Dejesa realmente deletar esse pedido')){deletar_pedido(<?= $pedido['id_va_pedido']; ?>)}else{return false}"   id="link_del_pedido_<?= $pedido['id_va_pedido']; ?>"><img src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir"></a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de pedidos realizados.</p>
    </div>
<?php } ?>
<div id="din_<?= $key; ?>"></div>