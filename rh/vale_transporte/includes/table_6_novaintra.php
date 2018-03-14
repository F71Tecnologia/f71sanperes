<?php include 'box_message.php';  ?>
<?php if(!empty($lista_dias_uteis)){ ?>
<table class="table table-condensed table-hover" id="table_6">
    <thead>
        <tr>
            <th colspan="9">Rela��o Dias �teis x Fun��es</th>
        </tr>
        <tr class="bg-primary valign-middle">
            <th>C�digo</th>
            <th>Dias �teis</th>
            <th>Compet�ncia</th>
            <th>CBO</th>
            <th>Fun��o</th>
            <th>Hor�rio</th>
            <th>Regi�o</th>
            <th>Excluir</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($lista_dias_uteis as $dias_uteis) {
        ?>
        <tr id="tr_dias_uteis_<?= $dias_uteis['id_vt_dias_uteis']; ?>">
            <td class="center"><?= $dias_uteis['id_vt_dias_uteis']; ?></td>
            <td class="center"  data-value="<?= utf8_decode($dias_uteis['dias_uteis']); ?>"><?= utf8_decode($dias_uteis['dias_uteis'])  ?></td>
            <td class="center" data-value=""><?= ($dias_uteis['sempre']==1) ? ' Sempre ' : ($meses[$dias_uteis['mes']].' de '.$dias_uteis['ano']); ?></td>
            <td class="center" ><?= ($dias_uteis['cod']>0) ? ( $dias_uteis['cod'].' - '.$dias_uteis['nome_cbo'] ) : ' - '; ?></td>
            <td class="center" data-value="<?= $dias_uteis['id_curso'].' - '.$dias_uteis['nome_curso']; ?>"><?= ($dias_uteis['cursos_inclusos']>0) ? $dias_uteis['cursos_inclusos'].' Fun��es inclusas.' : $dias_uteis['id_curso'].' - '.$dias_uteis['nome_curso']; ; ?></td>      
            <td class="center" data-value="<?= $dias_uteis['id_horario'].' - '.$dias_uteis['nome_horario']; ?>"><?= $dias_uteis['id_horario'].' - '.$dias_uteis['nome_horario']; ?></td>      
            <td class="center" data-value="<?= $dias_uteis['regiao']; ?>"><?= $dias_uteis['regiao']; ?></td>      
            <td class="center">
                <a id="del_dias_uteis_<?= $dias_uteis['id_vt_dias_uteis']; ?>" href="javascript:;" onclick="if (confirm('Dejesa realmente deletar essa registro?')){deletar_dias_uteis(<?= $dias_uteis['id_vt_dias_uteis']; ?>)} else { return false }" >
                    <img id="img_dias_uteis_<?= $dias_uteis['id_vt_dias_uteis']; ?>" src="../../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Excluir">
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
        <p>N�o h� registros realizados.</p>
    </div>
<?php } ?>