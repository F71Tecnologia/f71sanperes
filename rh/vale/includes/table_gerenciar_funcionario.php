<?php include 'box_message.php';   ?>
<?php if (!empty($relacao_funcionarios)) { 
    
//    print_r($debug);
    
    $key = !isset($key) ? 2 : $key;
    
?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_<?= $key; ?>" data-tarifas="<?= $obj_relacao_tarifas; ?>">
        <thead>
            <tr>
                <th colspan="9">Relação de Funcionários</th>
            </tr>
            <tr>
                <th>Editar</th>
                <th>Código</th>
                <th>Matrícula</th>
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
                <tr id="<?= $key.'_'.$funcionario['id_clt']; ?>">
                    <td class="center"><input type="checkbox" value="<?= $funcionario['id_clt']; ?>" /></td>
                    <td class="center"><?= $funcionario['id_clt']; ?></td>
                    <td class="center" id="td_matricula_<?= $funcionario['id_clt']; ?>" data-key="<?= $funcionario['matricula']; ?>" ><?= $funcionario['matricula']; ?></td>
                    <td class="center" ><?= $funcionario['nome_funcionario'] ?></td>
                    <td class="center"><?= $funcionario['cpf']; ?></td>          
                    <td class="center"><?= $funcionario['data_entrada_f']; ?></td>
                    <td class="center" data-key="<?= $funcionario['id_va_valor_diario']; ?>" id="td_valor_<?= $funcionario['id_clt']; ?>" >R$ <?= number_format($funcionario['valor_diario'],2,',','.'); ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <p class="txright"><?= count($relacao_funcionarios); ?> registros encontrados.</p>
    <div id="box_acaopri_<?= $key; ?>" class="txright" >
        <input type="button" value="Exportar Usuários" />
    </div>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de tarifas realizadas.</p>
    </div>
<?php } ?>