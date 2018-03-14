<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");



$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$clt = $_REQUEST['id_clt'];
$tipo = $_REQUEST['tipo']; // 1 - FÉRIAS, 2 - RECISÂO
$ferias = $_REQUEST['ferias'];

//MONTANDO QUERY RESCISAO
if($tipo == "2"){
    //LISTANDO SAÍDAS 
    $resultR = mysql_query("SELECT A.id_saida,B.nome,A.id_clt,
                                    DATE_FORMAT(B.data_vencimento, '%d/%m/%Y')  as data_vencimento, IF(B.data_pg = NULL,'',DATE_FORMAT(B.data_pg, '%d/%m/%Y' )) as data_pg, B.valor,B.status as status_saida,
                                    C.nome as nome_banco, C.conta, C.agencia, D.id_recisao,D.id_regiao,D.id_projeto
                                    FROM pagamentos_especifico AS A
                                    INNER JOIN saida as B ON (A.id_saida = B.id_saida)
                                    INNER JOIN bancos as C ON (C.id_banco = B.id_banco)
                                    INNER JOIN rh_recisao as D ON (D.id_clt = A.id_clt AND D.`status` != 0)
                                    WHERE B.status != '0' AND A.mes = '$mes' AND A.ano = '$ano' AND A.id_clt = '$clt' AND (B.tipo = '51' OR B.tipo = '170')");
}

$query_clt = mysql_query("SELECT A.id_clt,A.nome,B.nome as projeto FROM rh_clt AS A LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_assoc($query_clt);

$mes_nome = mesesArray($mes);

ob_start();
?>
<fieldset>
    <div class="fleft">
        <p><label class="first">CLT:</label> <?php echo $row_clt['id_clt']." - ".$row_clt['nome'] ?></p>
        <p><label class="first">Projeto:</label> <?php echo $row_clt['projeto'] ?></p>
        <p><label class="first">Mês:</label> <?php echo $mes_nome ?></p>
    </div>
</fieldset>
<br/><br/>
<?php if (mysql_num_rows($resultR) > 0) { ?>
    <table class="grid" cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Status</th>
                <th>N° Saída</th>
                <th>Descrição Saída</th>
                <th>Data de Vencimento</th>
                <th>Data de PG</th>
                <th>Valor</th>
                <th>Ver</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysql_fetch_assoc($resultR)) {
                $link = str_replace('+','--',encrypt("{$row['id_regiao']}&{$row['id_clt']}&{$row['id_recisao']}"));
                $link = "http://".$_SERVER['HTTP_HOST']."/intranet/rh/recisao/nova_rescisao_2.php?enc=".$link;
                ?>
                <tr class="<?php echo ($cont++ % 2 == 0) ? "even" : "odd"; ?>">
                    <td>
                        <?php
                        if ($row['status_saida'] == 1) {
                            $reenvio = true;
                            echo "<span class='tx-blue' data-key='{$row['id_saida']}'>Não pago</span>";
                        } elseif ($row['status_saida'] == 2) {
                            echo "<span class='tx-green' data-key='{$row['id_saida']}'>Pago</span>";
                        } else {
                            $reenvio = true;
                            echo "<span class='tx-red' data-key='{$row['id_saida']}'>Deletado</span>";
                        }
                        ?></td>
                    <td><?php echo $row['id_saida'] ?></td>
                    <td><?php echo $row['nome'] ?></td>
                    <td><?php echo $row['data_vencimento'] ?></td>
                    <td><?php echo $row['data_pg'] ?></td>
                    <td>R$ <?php echo number_format(str_replace(",", ".", $row['valor']), 2, ",", ".") ?></td>
                    <td class="center"><a href="<?php echo $link;?>" target="_blank">rescisão</a></td>
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