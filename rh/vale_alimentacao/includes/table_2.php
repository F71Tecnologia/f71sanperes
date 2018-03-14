<?php include 'box_message.php';   ?>
<?php if (!empty($relacao_funcionarios)) { 
    
    $key = !isset($key) ? 2 : $key;
    
?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_<?= $key; ?>">
        <thead>
            <tr>
                <th colspan="9">Rela��o de Valores</th>
            </tr>
            <tr>
                <th>Editar</th>
                <th>C�digo</th>
                <th>Nome</th>
                <th>Cpf</th>
                <th>Data Entrada</th>
                <th>Solicitou VA</th>
                <th>Valor Di�rio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($relacao_funcionarios as $funcionario) {
                ?>
                <tr id="tr_<?= $key.'_'.$funcionario['id_clt']; ?>">
                    <td class="center"><input type="checkbox" onclick="editar_<?= $key ?>(<?= $funcionario['id_clt']; ?>)" data-value="<?= $funcionario['id_clt']; ?>"></td>
                    <td class="center"><?= $funcionario['id_clt']; ?></td>
                    <td class="center" ><?= $funcionario['nome_funcionario'] ?></td>
                    <td class="center"><?= $funcionario['cpf']; ?></td>            
                    <td class="center"><?= $funcionario['data_entrada_f']; ?></td>            
                    <td class="center" data-value="<?= $funcionario['solicitou_vale_alimentacao']; ?>"><?= $funcionario['solicitou_vale_alimentacao']; ?></td>
                    <td class="center" data-value="R$ <?= number_format($funcionario['valor_diario'],2,',','.'); ?>">R$ <?= number_format($funcionario['valor_diario'],2,',','.'); ?></td>
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
        <p>N�o h� registros de tarifas realizadas.</p>
    </div>
<?php } ?>