<?php if (!empty($relacao_funcionarios)) { 
    
    $key = !isset($key) ? 4 : $key;
    
?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_<?= $key; ?>">
        <thead>
            <tr>
                <th colspan="9">Exportar Funcionários</th>
            </tr>
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Cpf</th>
                <th>Data Entrada</th>
                <th>Valor Diário</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($relacao_funcionarios as $funcionario) {
                ?>
                <tr id="tr_<?= $key.'_'.$funcionario['id_clt']; ?>">
                    <td class="center"><?= $funcionario['id_clt']; ?></td>
                    <td class="center" ><?= $funcionario['nome_funcionario'] ?></td>
                    <td class="center"><?= $funcionario['cpf']; ?></td>            
                    <td class="center"><?= $funcionario['data_entrada_f']; ?></td>            
                    <td class="center" data-value="R$ <?= number_format($funcionario['valor_diario'],2,',','.'); ?>">R$ <?= number_format($funcionario['valor_diario'],2,',','.'); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <br>
    <div id="bts_controller_<?= $key; ?>" data-flag="0" style="text-align: right;">
        <input type="button" value="Exportar" id="exporta_usuario"/>
    </div>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de tarifas realizadas.</p>
    </div>
<?php } ?>