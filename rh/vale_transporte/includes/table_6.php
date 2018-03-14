<?php include 'box_message.php';  ?>
<?php if(!empty($lista_dias_uteis)){ ?>
<table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" id="table_6">
    <thead>
        <tr>
            <th colspan="9">Relação Dias úteis x Funções</th>
        </tr>
        <tr>
            <th>Código</th>
            <th>Dias Úteis</th>
            <th>Competência</th>
            <th>CBO</th>
            <th>Função</th>
            <th>Horário</th>
            <th>Região</th>
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
            <td class="center" data-value="<?= $dias_uteis['id_curso'].' - '.$dias_uteis['nome_curso']; ?>"><?= ($dias_uteis['cursos_inclusos']>0) ? $dias_uteis['cursos_inclusos'].' Funções inclusas.' : $dias_uteis['id_curso'].' - '.$dias_uteis['nome_curso']; ; ?></td>      
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
    <input type="button" value="Cancelar" onclick="limpar_edicao_concessionaria()" />
    <input type="button" value="Salvar" onclick="salvar_edicao_concessionaria()" />
</div>
<?php } else { 
    
$alert['message'] = 'Não há registros realizados.';
include 'box_message.php'; 
    
} ?>