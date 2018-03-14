<?php
$icon_folha = array('1'=>array('img'=>'icon-filego','msg'=>'Adicionar Descontos'),'2'=>array('img'=>'icon-filego-disabled','msg'=>'Os movimentos já foram adicionados') );
$projetos_pedidos = array();
foreach ($pedidos as $v) {
    $projetos_pedidos[$v['projeto']][$v['ano'] . str_pad($v['mes'], 2, '0', STR_PAD_LEFT)][] = $v;
    krsort($projetos_pedidos[$v['projeto']]);
}

if (count($pedidos) > 0) {
    ?>
    <?php
    foreach ($projetos_pedidos as $p_id => $p) {
        ?>
        <table class="table table-condensed table-hover" id="tab0">
            <thead>
                <tr>
                    <th colspan="9">Relação de Pedidos <?= $projetos[$p_id]; ?></th>
                </tr>
                <tr class="bg-primary valign-middle">
                    <th>Código</th>
                    <th>Mês/Ano</th>
                    <th>Projeto</th>
                    <th>Gerado por</th>
                    <th>Valor Total</th>
                    <th>Relação</th>
                    <th>Confirmar na Folha</th>
                    <th>Exportar</th>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($p as $k_data => $p_data) { ?>
                    <tr class="titulo tr-bg-active">
                        <td colspan="9" ><?= count($p_data); ?> pedido(s) para <?= substr($k_data, -2).'/'.substr($k_data, 0, 4); ?></td>
                    </tr>
                    <?php foreach ($p_data as $pedido) { ?>
                        <tr id="tr_vt_pedido_<?= $pedido['id_vt_pedido']; ?>" class="valign-middle">
                            <td class="center"><?= $pedido['id_vt_pedido']; ?></td>
                            <td class="center"><?= $pedido['mes'] . '/' . $pedido['ano']; ?></td>
                            <td><?= $pedido['nome_projeto']; ?></td>
                            <td><?= $pedido['nome_funcionario']; ?> em <?= $pedido['criacao']; ?></td>
                            <td>R$ <?= number_format($pedido['valor_total'], 2, ',', '.'); ?></td>                                                        
                            <td class="center" ><a href="javascript:;" title="Visualizar Relação" onclick="visualizar_pedido(<?= $pedido['id_vt_pedido']; ?>);" ><img src="../../imagens/file.gif" width="16" height="16" border="0" alt="Visualizar Relação" title="Visualizar Relação" id="img_pedido_<?= $pedido['id_vt_pedido']; ?>" ></a></td>
                            <td class="center"><a href="javascript:;"  title="Adicionar Desconto em folha" onclick="desconto_folha(<?= $pedido['id_vt_pedido']; ?>);" style="text-decoration: none;" ><img src="../../imagens/icones/<?= $icon_folha[$pedido['status']]['img'];?>.gif" border="0" id="img_folha_<?= $pedido['id_vt_pedido']; ?>" ><br><small><?= $icon_folha[$pedido['status']]['msg']; ?></small></a></td>
                            <td class="center">
                                <?php $arquivos = explode(',', $pedido['arquivos']);
                                foreach ($arquivos as $arquivo) {
                                    ?>
                                <a href="javascript:;" onclick="gerar_arquivo(<?= $pedido['id_vt_pedido']; ?>)" title="Exportar Arquivo" ><img src="../../imagens/icones/icon-download.png" alt="Exportar" border="0" title="Exportar Pedido" id="img_arquivo_<?= $pedido['id_vt_pedido']; ?>"></a>
                                <?php } ?>
                            </td>
                            <td class="center"><?php if ($pedido['status'] != 2 && $pedido['user'] == $usuario['id_funcionario']) { ?><a href="javascript:;" onclick="if (confirm('Dejesa realmente deletar esse pedido')) {
                                            deletar_pedido(<?= $pedido['id_vt_pedido']; ?>)
                                        } else {
                                            return false
                                        }" ><img  id="img_del_pedido_<?= $pedido['id_vt_pedido']; ?>" src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir"></a><?php } ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>                
            </tbody>
        </table>
    <?php } ?>
    <input type="hidden" name="pedido" id="pedido" value="" />
    <input type="hidden" name="mes_folha" id="mes_folha" value="" />
    <input type="hidden" name="ano_folha" id="ano_folha" value="" />
    <input type="hidden" name="projeto" id="projeto" value="" />
<?php } else { ?>
    <div id="message-box" class="alert alert-warning">
        <p>Não há registros de pedidos realizados.</p>
    </div>
<?php }
?>
<div  id="din_0">

</div>