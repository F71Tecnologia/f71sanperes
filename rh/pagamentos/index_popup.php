<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");

$id_folha = $_REQUEST['id'];
$tipo = $_REQUEST['tipo'];
$tipo_contrato_pg = $_REQUEST['tipo_contrato'];
$reenvio = false;


switch($tipo_contrato_pg){
    
    case 1: $tabela_folha       = 'rh_folha';
            $tabela_proc        = 'rh_folha_proc';
            $tabela_trabalhador  ='rh_clt';
            $id_trab            = 'id_clt';
            break;
       
    case 2:  $tabela_folha       = 'folhas';
             $tabela_proc        = 'folha_cooperado';
             $tabela_trabalhador ='autonomo';
             $id_trab            = 'id_autonomo';
             break;
         
         
    
}


$qrFolha = "SELECT A.id_folha,A.mes,A.ano,A.total_liqui,A.`status`,
            B.nome as projeto,
            C.nome as usuario,            
            (SELECT COUNT($id_trab) FROM $tabela_proc WHERE id_folha = A.id_folha AND status = 3) as total_participante
            FROM $tabela_folha AS A
            LEFT JOIN projeto AS B ON (A.projeto=B.id_projeto)
            LEFT JOIN funcionario AS C ON(A.user=C.id_funcionario)
            WHERE A.id_folha = {$id_folha}";

            
$resultF = mysql_query($qrFolha);
$folha = mysql_fetch_assoc($resultF);

$qrRegistros = "SELECT  A.id_pg,B.id_saida,B.nome as descricao,B.especifica,B.tipo,B.valor,B.comprovante,
                        B.id_userpg,B.status,B.id_deletado,B.data_deletado,
                        C.nome as enviadopor,
                        D.nome,D.agencia,D.conta,
                        E.nome as pago_por,
                        DATE_FORMAT(B.data_proc, '%d/%m/%Y') as processado,
                        DATE_FORMAT(B.data_vencimento, '%d/%m/%Y') as pago,
                        (SELECT tipo_saida_file FROM saida_files WHERE id_saida = A.id_saida LIMIT 1) as anexo,
                        (SELECT tipo_pg FROM saida_files_pg WHERE id_saida = A.id_saida LIMIT 1) as comprovante
                        FROM pagamentos AS A
                        LEFT JOIN saida AS B ON (A.id_saida=B.id_saida)
                        LEFT JOIN funcionario AS C ON (B.id_user=C.id_funcionario)
                        LEFT JOIN bancos AS D ON (B.id_banco=D.id_banco)
                        LEFT JOIN funcionario AS E ON (B.id_userpg=E.id_funcionario)
                        WHERE A.id_folha = {$id_folha} AND A.tipo_pg = {$tipo} AND A.tipo_contrato_pg = {$tipo_contrato_pg}
                        ORDER BY B.data_proc DESC";

$resultR = mysql_query($qrRegistros);
$cont = 0;

$nomeTipos = array("1"=>"GPS","2"=>"FGTS","3"=>"PIS","4"=>"IR");

ob_start();
?>
<fieldset>
    <div class="fleft">
        <p><label class="first">Folha:</label> <?php echo $folha['id_folha'] ?></p>
        <p><label class="first">Projeto:</label> <?php echo $folha['projeto'] ?></p>
        <p><label class="first">Finalizado Por:</label> <?php echo $folha['usuario'] ?></p>
    </div>
    <div class="fleft">
        <p><label class="first">Valor:</label>R$ <?php echo number_format($folha['total_liqui'], 2, ",", ".") ?></p>
        <p><label class="first">Mes/Ano:</label> <?php echo $folha['mes'] . "/" . $folha['ano'] ?></p>
        <p><label class="first">Participantes:</label> <?php echo $folha['total_participante'] ?></p>
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