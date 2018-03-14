<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");

$id_rpa       = $_REQUEST['id_rpa'];
$tipo           = $_REQUEST['tipo']; /// TIPO: 1- GPS, 2 - IR
$id_autonomo    = $_REQUEST['id_autonomo'];
$reenvio = false;


if($tipo == 1){ $nome_tipo = 'GPS';}
elseif($tipo == 2){ $nome_tipo = 'IR';}


$qr_rpa = mysql_query("SELECT A.id_rpa, B.nome  FROM rpa_autonomo  as A
INNER JOIN autonomo as B
ON A.id_autonomo = B.id_autonomo
INNER JOIN rpa_saida_assoc as C
ON C.id_rpa = A.id_rpa
WHERE A.id_rpa = $id_rpa;
");

$row_rpa = mysql_fetch_assoc($qr_rpa);
$nomeTipos = array("1"=>"GPS","2"=>"FGTS","3"=>"PIS","4"=>"IR");

ob_start();

?>
<fieldset>
    <div class="fleft">
      
      
       
    </div>
    <div class="fleft"> 
            <p><h3>RPA -  Recibo de Pagamento de Autônomo</h3></p>
            <p><label class="first"> Nº do recibo:</label> <?php echo $row_rpa['id_rpa'] ?></p>  
            <p><label class="first">Tipo: </label> <?php echo $nome_tipo?></p>
            <p><label class="first">Nome:</label> <?php echo $row_rpa['nome'] ?></p>
      
    </div>
</fieldset>
<br/><br/>
<?php if (mysql_num_rows($resultR) > 0) { ?>
    <table class="grid" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>Status</th>
                <th>Enviado Em</th>
                <th>Enviado Por</th>
                <th>Descrição</th>
                <th>Especificação</th>
                <th>Valor</th>
                <th>Vencimento Em</th>
                <th>Pago Por</th>
                <th colspan="2">Arquivos</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysql_fetch_assoc($resultR)) {
                $anexo = "-";
                $comp = "-";
                //VERIFICA COMPROVANTE
                if ($row['anexo'] != "") {
                    $link_encryptado = encrypt('ID=' . $row['id_saida'] . '&tipo=0');
                    $anexo = "<a target=\"_blank\" title=\"Anexo\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado . "\"><img src=\"../../financeiro/imagensfinanceiro/attach-32.png\"  /></a>";
                }
                if ($row['comprovante'] != "") {
                    $link_encryptado_pg = encrypt('ID=' . $row['id_saida'] . '&tipo=1');
                    $comp = "<a target=\"_blank\" title=\"Comprovante\" href=\"../../novoFinanceiro/view/comprovantes.php?" . $link_encryptado_pg . "\"><img src=\"../../financeiro/imagensfinanceiro/attach-32.png\"  /></a>";
                }
                ?>
                <tr class="<?php echo ($cont++ % 2 == 0) ? "even" : "odd"; ?>">
                    <td>
                        <?php
                        if ($row['status'] == 1) {
                            $reenvio = true;
                            echo "<span class='tx-blue' data-key='{$row['id_saida']}'>Não pago</span>";
                        } elseif ($row['status'] == 2) {
                            echo "<span class='tx-green' data-key='{$row['id_saida']}'>Pago</span>";
                        } else {
                            $reenvio = true;
                            echo "<span class='tx-red' data-key='{$row['id_saida']}'>Deletado</span>";
                        }
                        ?></td>
                    <td><?php echo $row['processado'] ?></td>
                    <td><?php echo $row['enviadopor'] ?></td>
                    <td><?php echo $row['descricao'] ?></td>
                    <td><?php echo $row['especifica'] ?></td>
                    <td>R$ <?php echo number_format(str_replace(",", ".", $row['valor']), 2, ",", ".") ?></td>
                    <td><?php echo $row['pago'] ?></td>
                    <td><?php echo $row['pago_por'] ?></td>
                    <td><?php echo $comp ?></td>
                    <td><?php echo $anexo ?></td>
                </tr>
    <?php } ?>
        </tbody>
    </table>

    <?php if ($reenvio) { ?>
        <div id="message-box" class="message-blue txcenter" style="margin-top: 10px;"><a href="">Enviar novo(a) <?php echo $nomeTipos[$tipo] ?></a></div>
    <?php } ?>
<?php } else { ?>
    <div id="message-box" class="message-red txcenter">Saída não gerada</div>
<?php } ?>
<?php
$html = ob_get_contents();
ob_clean();
echo utf8_encode($html);
?>