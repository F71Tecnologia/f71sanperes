<?php include 'box_message.php'; ?>
<?php if(!empty($lista_concessionarias)){ ?>
<table class="table table-condensed table-hover" id="table_5">
    <thead>
        <tr>
            <th colspan="9">Relação de Concessionárias</th>
        </tr>
        <tr class="bg-primary valign-middle">
            <th>Editar</th>
            <th>Código</th>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Região</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($lista_concessionarias as $concessionaria) {
        ?>
        <tr id="tr_vt_concessionaria_<?= $concessionaria['id_concessionaria']; ?>">
            <td class="center"><input type="checkbox" onclick="editar_concessionaria(<?= $concessionaria['id_concessionaria']; ?>)" data-value="<?= $concessionaria['id_concessionaria']; ?>"></td>
            <td class="center"><?= $concessionaria['id_concessionaria']; ?></td>
            <td class="center"  data-value="<?= utf8_decode($concessionaria['nome']); ?>"><?= utf8_decode($concessionaria['nome'])  ?></td>
            <td class="center" data-value="<?=  utf8_decode($concessionaria['tipo_concessionaria']); ?>"><?=  utf8_decode($concessionaria['tipo_concessionaria']); ?></td>      
            <td class="center" ><?= $concessionaria['id_regiao'].' - '. utf8_decode($concessionaria['nome_regiao']); ?></td> 
            <td class="center">
                <a id="del_concessionaria_<?= $concessionaria['id_concessionaria']; ?>" href="javascript:;" onclick="if (confirm('Dejesa realmente deletar essa tarifa?')){deletar_concessionaria(<?= $concessionaria['id_concessionaria']; ?>)} else { return false }" >
                    <img id="img_concessionaria_<?= $concessionaria['id_concessionaria']; ?>" src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir">
                </a>
            </td>
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>
<div id="bts_controller_5" data-flag="0" style="display:none; text-align: right;">
    <input type="button" class="btn btn-default" value="Cancelar" onclick="limpar_edicao_concessionaria()" />
    <input type="button" class="btn btn-primary" value="Salvar" onclick="salvar_edicao_concessionaria()" />
</div>
<?php } else { ?>
    <div id="message-box" class="alert alert-warning">
        <p>Não há registros realizados.</p>
    </div>
<?php } ?>