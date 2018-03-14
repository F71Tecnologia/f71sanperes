<?php if(empty($arr_cls)){ ?>
    <div id="message-box" class="alert alert-warning">
        <p>Nenhum resultado para esta consulta</p>
    </div>
<?php }else{ ?>
<table class="table table-condensed table-hover">
    <thead>
        <tr>
            <th colspan="12">Projeto <?= $projetos[$post_projeto] . ' (CNPJ DO ARQUIVO ' . $post_cnpj . ') '; ?></th>
        </tr>
        <tr class="bg-primary valign-middle">
            <th>Editar</th>
            <th>Id</th>
            <th>Matricula</th>
            <th>Nome</th>
            <th>CPF</th>
            <th>Data Entrada</th>
            <th>Solicitou Vale Transporte</th>
            <th>Tipo Cartão</th>
            <th>Número Cartão</th>
            <th>Sobreescrever Dias Úteis</th>
            <th>Função</th>
            <th>Excluir Dias</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $cont = 1;
        foreach ($arr_cls as $clt) {
            $resumo_tipo_cartoes[$clt['tipo_cartao']][] = $clt['id_clt'];
            $class = (($cont % 2) == 0) ? 'even' : 'odd';
            echo '<tr>';
            echo '<td colspan="12" style="display: none;">';
            print_r($clt);
            echo '</td>';
            echo '</tr>';
            ?>
            <tr class="<?= $class; ?>" id="tr_<?= $clt['id_clt']; ?>" >
                <td class="center"><input type="checkbox" onclick="editao_clt_dias(<?= $clt['id_clt']; ?>)" data-value="<?= $clt['id_clt']; ?>"></td>
                <td class="center"><?= $clt['id_clt']; ?></td>
                <td class="center" <?= $clt['matricula']; ?></td>
                <td><?= $clt['nome']; ?></td>
                <td><?= $clt['cpf']; ?></td>
                <td><?= $clt['data_entrada_f']; ?></td>
                <td class="center" ><?= ($clt['transporte'] == '1') ? 'SIM' : 'NAO'; ?></td>
                <td class="center" ><?= $tipos_cartao[$clt['tipo_cartao']]; ?></td>
                <td class="center" ><?= $clt['cartao1'] ?></td>                                     
                <td class="center" data-value="<?= ($clt['dias_uteis']<0) ? '' : $clt['dias_uteis']; ?>" ><?= ($clt['dias_uteis']<0) ? '' : $clt['dias_uteis']; ?></td>                                     
                <td><?= $clt['id_curso'].' - '.$clt['nome_curso'] ?></td>                             
                <td class="center" data-value="" >
                    <?php if($clt['dias_uteis']>0){ ?>
                        <a href="javascript:;" onclick="if (confirm('Dejesa realmente remover os dias úteis')) {  deletar_dias_clt(<?= $clt['id_clt']; ?>)} else { return false; }">
                            <img id="del_dias_uteis_<?= $clt['id_clt']; ?>" src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir">
                        </a>
                    <?php } ?>
                </td>                                     
            </tr>
            <?php $cont++;
        } ?>
    </tbody>
</table>
<br>
<div id="bts_controller_3" data-flag="0" style="display:none; text-align: right;">
    <input type="button" class="btn btn-default" value="Cancelar" id="bt_limpar_clts_dias"  onclick="limpar_clts_dias()" />
    <input type="button" class="btn btn-primary" value="Salvar" id="bt_salvar_clts_dias" onclick="salvar_clts_dias()" />
</div>
<?php } ?>