<?php include 'box_message.php'; ?>
<?php if (!empty($relacao_tarifas)) { 
    
    $key = !isset($key) ? 3 : $key;
    
?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_<?= $key; ?>">
        <thead>
            <tr>
                <th colspan="9">Relação de Valores</th>
            </tr>
            <tr>
                <th>Editar</th>
                <th>Código</th>
                <th>Regiao</th>
                <th>Vinculos</th>
                <th>Valor</th>
                <th>Excluir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            foreach ($relacao_tarifas as $tarifa) {
                ?>
                <tr id="<?= $key.'_'.$tarifa['id_va_valor_diario']; ?>">
                    <td class="center"><input type="checkbox" value="<?= $tarifa['id_va_valor_diario']; ?>"></td>
                    <td class="center"><?= $tarifa['id_va_valor_diario']; ?></td>
                    <td class="center"><?= $tarifa['nome_regiao'] ?></td>
                    <td class="center"><?= $tarifa['vinculos'] ?></td>
                    <td class="center" id="td_valor_tarifa_<?= $tarifa['id_va_valor_diario']; ?>" data-valor="<?= $tarifa['valor_diario']; ?>" >R$ <?= $tarifa['valor_diario']; ?></td>            
                    <td class="center">
                        <a href="javascript:;" id="exclui_valor_diario_<?= $tarifa['id_va_valor_diario'].'_'.$tarifa['vinculos']; ?>" >
                            <img  src="../../imagens/icones/<?= ($tarifa['vinculos']>0) ? 'icon-delete-disabled.gif' : 'icon-excluir.png'; ?>" alt="Deletar" border="0" title="Excluir">
                        </a>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <div id="bts_controller_<?= $key; ?>" data-flag="0" style="display:none; text-align: right;">
        <input type="button" value="Cancelar" onclick="limpa_<?= $key; ?>()" />
        <input type="button" value="Salvar" onclick="salva_<?= $key; ?>()" />
    </div>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de tarifas realizadas.</p>
    </div>
<?php } ?>