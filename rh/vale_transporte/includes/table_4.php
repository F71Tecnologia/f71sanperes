<?php include 'box_message.php'; ?>
<?php if(!empty($lista_tarifas)){ ?>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_4">
    <thead>
        <tr>
            <th colspan="9">Relação de Tarifas </th>
        </tr>
        <tr>
            <th>Editar</th>
            <th>ID</th>
            <th>Itinerario</th>
            <th>Descrição</th>
            <th>Concessionária</th>
            <th>Valor</th>
            <th>Linha</th>
            <th>Vinculos</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($lista_tarifas as $tarifa) {
        ?>
        <tr id="tr_vt_tarifa_<?= $tarifa['id_tarifas']; ?>">
            <td class="center"><input type="checkbox" onclick="editar_tarifa(<?= $tarifa['id_tarifas']; ?>)" data-value="<?= $tarifa['id_tarifas']; ?>"></td>
            <td class="center"><?= $tarifa['id_tarifas']; ?></td>
            <td class="center"  data-value="<?= $tarifa['itinerario']; ?>"><?= $tarifa['itinerario']  ?></td>
            <td class="center" data-value="<?= utf8_decode($tarifa['descricao']); ?>"><?= utf8_decode($tarifa['descricao']); ?></td>
            <td class="center"  data-value="<?= utf8_decode($tarifa['nome_concessionaria']); ?>"><?= utf8_decode($tarifa['nome_concessionaria']); ?></td>
            <td class="center" data-value="R$ <?= number_format($tarifa['valor'],2,',','.'); ?>">R$ <?= number_format($tarifa['valor'],2,',','.'); ?></td>            
            <td class="center"  data-value="<?= trim(utf8_decode($linhas[$tarifa['linha']])); ?>"><?= $linhas[$tarifa['linha']]; ?></td>
            <td class="center"><?= $tarifa['vinculos']; ?></td>
            <td class="center">
                <a id="del_tarifa_<?= $tarifa['id_tarifas']; ?>" href="javascript:;" onclick="
                    <?php if($tarifa['vinculos']<=0){ ?> 
                        if (confirm('Deseja realmente deletar essa tarifa?')){  
                                deletar_tarifa(<?= $tarifa['id_tarifas']; ?>);
                            } else { return false }         
                    <?php }else{ ?> alert('Não é possível deletar tarifas com vinculos'); <?php } ?>" >
                    <img <?php if($tarifa['vinculos']>0){ ?> class="fade" <?php } ?> id="img_tarifa_<?= $tarifa['id_tarifas']; ?>" src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir">
                </a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<div id="bts_controller_4" data-flag="0" style="display:none; text-align: right;">
    <input type="button" value="Cancelar" onclick="limpar_edicao_tarifas()" />
    <input type="button" value="Salvar" onclick="salvar_edicao_tarifas()" />
</div>
<?php } else { ?>
    <div id="message-box" class="message-yellow">
        <p>Não há registros de tarifas realizadas.</p>
    </div>
    <?php } ?>