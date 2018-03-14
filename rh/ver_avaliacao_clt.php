<?php
include('../conn.php');

header('Content-Type: text/html; charset=utf-8');

$clt_id = $_REQUEST["clt"];

$qry_clt = mysql_query("SELECT * FROM rh_avaliacao_clt WHERE clt_id = ".$clt_id."");
$dados = mysql_fetch_assoc($qry_clt);


?>

<html>
<table width="100%" cellpadding="15" cellspacing="0">
<tr>
<td colspan="2" bgcolor="#666666" align="center"><font color="FFFFFF"><b>FICHA CRITÉRIOS DE AVALIAÇÃO</b></font></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">1 - RESIDE PRÓXIMO AO LOCAL DE TRABALHO?</td>
<td bgcolor="#EBEBEB"><?php echo $dados["quest01"]; ?></td>
</tr>
<tr>
<td>2 - JÁ EXERCEU A MESMA FUNÇÃO?</td>
<td><?php echo $dados["quest02"]; ?></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">3 - QUAL LOCAL?</td>
<td bgcolor="#EBEBEB"><?php echo $dados["quest03"]; ?></td>
</tr>
<tr>
<td>4 - QUAL MOTIVO DO AFASTAMENTO?</td>
<td><?php echo $dados["quest04"]; ?></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">5 - TRABALHA EM OUTRO LOCAL?</td>
<td bgcolor="#EBEBEB"><?php echo $dados["quest05"]; ?></td>
</tr>
<tr>
<td>6 - ONDE?</td>
<td><?php echo $dados["quest06"]; ?></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">7 - ESTUDA ATUALMENTE?</td>
<td bgcolor="#EBEBEB"><?php echo $dados["quest07"]; ?></td>
</tr>
<tr>
<td>8 - QUAIS OUTROS CURSOS POSSUI?</td>
<td><?php echo $dados["quest08"]; ?></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">9 - QUAL O MÉTODO DE DESLOCAMENTO ATÉ O LOCAL DE TRABALHO?</td>
<td bgcolor="#EBEBEB"><?php echo $dados["quest09"]; ?></td>
</tr>
<tr>
<td>10 - QUANTO TEMPO DE EXPERIÊNCIA POSSUI NA ÁREA?</td>
<td><?php echo $dados["quest10"]; ?></td>
</tr>
<tr>
<td bgcolor="#EBEBEB">11 - POSSUI ALGUM VÍNCULO COM O GOVERNO?</td>
<td bgcolor="#EBEBEB"><?php echo $dados["quest11"]; ?></td>
</tr>
<tr>
<td>12 - MATRÍCULA:</td>
<td><?php echo $dados["quest12"]; ?></td>
</tr>
<tr>
<td colspan="2" align="center"><input type="button" name="imprimir" value="Imprimir" onClick="window.print();"></td>
</tr>
</table>
</html>

