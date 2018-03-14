<?php
include("../conn.php");
$id = $_REQUEST['id'];
$tabela = $_REQUEST['tipo'];
$query = "
SELECT 
    date_format(A.data_proc, '%d/%m/%Y - %h:%m:%s') as data_proc, A.nome nomeEntradaSaida, A.especifica, A.valor, A.adicional,
    B.id_funcionario, B.nome nomeFuncionario,
    C.id_banco, C.nome nomeBanco, C.agencia, C.conta,
    E.nome_grupo
FROM $tabela A
LEFT JOIN funcionario B ON (A.id_user = B.id_funcionario)
LEFT JOIN bancos C ON (A.id_banco = C.id_banco)
LEFT JOIN entradaesaida D ON (A.tipo = D.id_entradasaida)
LEFT JOIN entradaesaida_grupo E ON (D.grupo = E.id_grupo)
WHERE id_$tabela = $id";
$query = mysql_query($query);
$row = mysql_fetch_assoc($query); ?>
<table class="table table-condensed table-bordered text-sm no-margin-b">
    <thead>
        <tr class="info valign-middle" style="text-transform: uppercase;">
            <th colspan="2">C&oacute;digo: <?=$id?> / Nome: <?=utf8_encode($tabela)?> <?=utf8_encode($row['nomeEntradaSaida'])?></th>
        </tr>
    </thead>
    <tbody>
        <tr class="valign-middle">
            <td>Grupo:</td>
            <td><?=utf8_encode($row['nome_grupo'])?></td>
        </tr>
        <tr class="valign-middle">
            <td>Tipo:</td>
            <td><?=utf8_encode($row['nomeEntradaSaida'])?></td>
        </tr>
        <tr class="valign-middle">
            <td>Especifica&ccedil;&atilde;o:</td>
            <td><?=utf8_encode($row['especifica'])?></td>
        </tr>
        <tr class="valign-middle">
            <td>Criado por:</td>
            <td><strong><?= utf8_encode($row[id_funcionario])." - ".utf8_encode($row[nomeFuncionario]) ?></strong></td>
        </tr>
        <tr class="valign-middle">
            <td>Data cria&ccedil;&atilde;o: </td>
            <td><?=utf8_encode($row['data_proc']);?></td>
        </tr>
        <tr class="valign-middle">
            <td>Banco: </td>
            <td><?= $row['id_banco'].' - '.utf8_encode($row['nomeBanco'])?></td>
        </tr>
        <tr class="valign-middle">
            <td>Agencia: </td>
            <td><?=utf8_encode($row['agencia'])?></td>
        </tr>
        <tr class="valign-middle">
            <td>Conta: </td>
            <td><?=utf8_encode($row['conta'])?></td>
        </tr>
        <tr class="valign-middle">
            <td>Valor: </td>
            <td><b>R$ <?=number_format(str_replace(",",'.',$row['valor']) +  str_replace(",",'.',$row['adcional']),2,',','.');?></b></td>
        </tr>
        <?php if($_COOKIE['acelerar']) { ?>
        <tr class="valign-middle">
            <td></td>
            <td>
                <button type="button" class="btn btn-xs btn-danger deletar<?=(!empty($row_saida['id_entrada'])) ? "Entrada" : "Saida"?>" data-toggle="tooltip" title="Deletar <?=(!empty($row_saida['id_entrada'])) ? "Entrada" : "Saida"?>" data-key="<?=$id?>">
                    <i class="fa fa-trash-o" border="0"></i>
                </button>
                <button type="button" class="btn btn-xs btn-success pagar<?=(!empty($row_saida['id_entrada'])) ? "Entrada" : "Saida"?>" data-toggle="tooltip" title="<?=(!empty($row_saida['id_entrada'])) ? "Confirmar Entrada" : "Pagar Saida"?>" data-key="<?=$id?>" data-periodo="<?= $row_saida['data_vencimento'] ?>">
                    <i class="fa fa-plus" alt="Editar" border="0"></i>
                </button>
                <?php if(!$row_saida['caixinha']) { ?>
                <button type="button" class="btn btn-xs btn-info pagarPeloCaixinha" data-toggle="tooltip" title="<?=(!empty($row_saida['id_entrada'])) ? "Confirmar Entrada Pelo Caixinha" : "Pagar Saida Pelo Caixinha"?>" data-key="<?=$id?>" data-tipo="<?=(!empty($row_saida['id_entrada'])) ? 2 : 1 ?>" data-periodo="<?= $row_saida['data_vencimento'] ?>">
                    <i class="fa fa-money" alt="" border="0"></i>
                </button>
                <?php } ?>
                <button type="button" class="btn btn-xs btn-default duplicarSaida" title="Duplicar saida" data-key="<?=$id?>" data-toggle="tooltip">
                    <i class="fa fa-copy"></i>
                </button>
                <a href="nota_debito.php?id=<?php echo $row_saida['id_saida'] ?>" target="_blank" class="btn btn-xs btn-default" title="Nota de Debito" data-toggle="tooltip">
                    <i class="fa fa-print"></i>
                </a>
                <?php if (!empty($row_saida['id_saida'])) { ?>
                    <a class="btn btn-xs btn-warning btnAcoes" href='form_saida.php?id_saida=<?php echo $row_saida['id_saida'] ?>' data-action='editar_saida' data-url='' data-key='<?php echo $row_saida['id_saida'] ?>' data-toggle='tooltip' title='EDITAR SAIDA'><i class='fa fa-pencil'></i></a>
                <?php } else if ($chave == 4) { ?>	
                    <button type="button" class="btn btn-xs btn-warning editar_entrada" id="e<?= $row_saida['id_entrada'] ?>" data-id="<?= $row_saida['id_entrada'] ?>" data-tipo="entrada" data-toggle="tooltip" title="Editar Entrada"><i class="fa fa-pencil"></i></button>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>