<?php 
include ("../../conn.php");
include("../../wfunction.php");
$usuario = carregaUsuario();

$id_entrada = $_REQUEST['id'];
$qr_entrada = mysql_query("SELECT * FROM entrada as A LEFT JOIN bancos as B ON (B.id_banco = A.id_banco) WHERE A.id_entrada = '$id_entrada'");
$row_entrada = mysql_fetch_assoc($qr_entrada);

$qr_notas = mysql_query("SELECT * FROM (notas INNER JOIN parceiros ON notas.id_parceiro = parceiros.parceiro_id) INNER JOIN notas_assoc ON notas_assoc.id_notas = notas.id_notas WHERE notas_assoc.id_entrada = '$id_entrada' LIMIT 1");
$row_notas = mysql_fetch_assoc($qr_notas);

?>

<form action="" method="post" id="form_editar_entrada" class="form-horizontal">
    <table class="table table-bordered table-condensed text-sm">
        <tr class="bg-info valign-middle">
            <td colspan="2">
                <?=$row_notas['id_notas'].' - ' . $row_notas['numero']?>
            </td>
        </tr>
        <tr class="valign-middle">
            <td width="25%">Nome</td>
            <td>
                <input type="text" name="nome" id="nome" class="form-control" value="<?=utf8_encode($row_entrada['nome'])?>" />
                <input type="hidden" name="id_entrada" id="id_entrada" value="<?=$row_entrada['id_entrada']?>" />
                <input type="hidden" name="update_entrada" value="update_entrada" />
            </td>
        </tr>
        <tr class="valign-middle bloco_notas" <?php if($row_entrada['tipo'] != 12){ ?>style="display: none;"<?php } ?>>
            <td>Parceiro nota</td>
            <td>
                <select name="parceiros_notas" id="parceiros_notas" class="form-control">
                    <?php 
                    $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE id_regiao = '$row_notas[id_regiao]'");
                    while($row_parceiros = mysql_fetch_assoc($qr_parceiros)){ ?>
                        <option value="<?=$row_parceiros['parceiro_id']?>"><?=$row_parceiros['parceiro_id'].' - '.utf8_encode($row_parceiros['parceiro_nome'])?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr class="valign-middle">
            <td>Valor:</td>
            <td><input type="text" name="valor" class="form-control" value="<?php echo $row_entrada['valor']; ?>" /></td>
        </tr>
        <tr class="valign-middle">
            <td>Data de vencimento:</td> 
            <td><input type="text" name="data_vencimento" class="data form-control" value="<?php echo implode('/',array_reverse(explode('-',$row_entrada['data_vencimento'])));?>" /></td>
        </tr>
        <tr class="valign-middle">
            <td>Banco:</td> 
            <td>
                <select name="id_banco" class="form-control">
                     <?php 
                    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_regiao = {$usuario['id_regiao']}");
                    while($row_bancos = mysql_fetch_assoc($qr_bancos)){ ?>
                        <option value="<?=$row_bancos['id_banco']?>" <?php if($row_entrada['id_banco'] == $row_bancos['id_banco']){ echo "selected";}?>>
                            <?=$row_bancos['id_banco']." - ".$row_bancos['nome']." / Conta:".$row_bancos['conta']." - Ag:".$row_bancos['agencia']?>
                        </option>
                    <?php } ?>
                </select>
             </td>
        </tr>
        <tr class="valign-middle">
            <td>Descri&ccedil;&atilde;o</td>
            <td><textarea name="descricao" class="form-control" id="descricao" cols="30" rows="5"><?=stripslashes(utf8_encode($row_entrada['especifica']))?></textarea></td>
        </tr>
    </table>
</form>