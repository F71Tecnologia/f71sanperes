<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "conn.php";

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTER PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//----------------------------------------------------------------------

/*
$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];*/

$projeto = "11";
$regiao = "3";


$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

$data_hoje = date('d/m/Y');

$RESULT = mysql_query("SELECT distinct(id_curso) FROM autonomo WHERE id_projeto = '$projeto'");
$num_row = mysql_num_rows($RESULT); 


$bord = "style='border-bottom:#000000 solid 1px; border-left:#000000 solid 1px; '";
$bord1 = "style='border-bottom:#000000 solid 1px;'";

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- FUNCAO PARA CALCULAR IDADE

function CalcularIdade($nascimento) {
$hoje = date("d/m/Y"); //pega a data d ehoje
$aniv = explode("/", $nascimento); //separa a data de nascimento em array, utilizando o símbolo de - como separador
$atual = explode("/", $hoje); //separa a data de hoje em array
  
$idade = $atual[2] - $aniv[2];

if($aniv[1] > $atual[1]) //verifica se o mês de nascimento é maior que o mês atual
{
$idade--; //tira um ano, já que ele não fez aniversário ainda
} 
elseif($aniv[1] == $atual[1] && $aniv[0] > $atual[0]) //verifica se o dia de hoje é maior que o dia do aniversário
{
$idade--; //tira um ano se não fez aniversário ainda
}
return $idade; //retorna a idade da pessoa em anos
}

//------------------ FUNCAO PARA CALCULAR IDADE

?>
<html><head><title>Intranet</title>
<style>
h1 { page-break-after: always }
</style>
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\">
<style type="text/css">
<!--
.style1 {
font-family: Arial, Helvetica, sans-serif;
font-size: 12px;
}
-->
</style>
<link href="net1.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="85%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:solid 2px #999" class="bordaescura1px">
<tr> 
<td width="21%" height="108"><p class="MsoHeader" align="center" style='text-align:center'><span class="MsoHeader" style="text-align:center"><strong><span class="style5"><img src='imagens/logomaster<?=$row_user['id_master']?>.gif' alt="" width='120' height='86' /><br />
</span></strong><span style="font-size:10px">
<?=$row_master['razao']?>
</span></span></p></td>
<td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
style='font-size:16.0pt;color:red'><?php print "$row_pro[nome] <br> $row_pro[regiao] "; ?></span></b></p></td>
<td width="21%">&nbsp;</td>
</tr>
<tr> 
<td colspan="3"><div align="center">
<p><strong><br>
<br>
<font face="Arial, Helvetica, sans-serif" size="-1">



RELAT&Oacute;RIO DE PARTICIPANTES ATIVOS DO PROJETO POR ATIVIDADE.<br>
<br>
TOTAL DE ATIVIDADES:  <?php echo $num_row; ?>
<br>
<br>


<?php print "
<table width=97% cellpadding='0' cellspacing='0' border='0' style='border:solid 1px #000'>
<tr height=25>
<td bgcolor='#CCCCC' align=center class='style1' width='5%' $bord1>Cod.</td> 
<td bgcolor='#CCCCC' align=center class='style1' width='30%' $bord1>Atividade</td>  
<td bgcolor='#CCCCC' align=center class='style1' width='10%' $bord1>Participantes</td>
</tr>";


$cont = "0";

while($Row = mysql_fetch_array($RESULT)){
	
	$result_atividade = mysql_query("SELECT * FROM curso where id_curso = '$Row[0]'");
	$row_atividade = mysql_fetch_array($result_atividade);
	
	if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
	
	$REAut = mysql_query("SELECT id_autonomo FROM autonomo WHERE id_curso = '$Row[0]'");
	$numAut = mysql_num_rows($REAut);
	
	print "
	<TR bgcolor=$color height=30>
	<TD $bord1><font face='Arial, Helvetica, sans-serif' size='-3'><center>$Row[0]</center></font></TD>
	<TD $bord><font face='Arial, Helvetica, sans-serif' size='-3'>&nbsp; $row_atividade[nome]</font></TD>
	<TD $bord><font face='Arial, Helvetica, sans-serif' size='-3'>&nbsp; $numAut</font></TD>
	</TR>";
	
	$cont ++;	  
}

print "</TABLE><br><BR><br><Br>";
?> 
</font></strong></p>
</div></td>
</tr>
</table>
</body>
</html>
<?php
/* Liberando o resultado */
mysql_free_result($RESULT);
mysql_free_result($result_pro);
/* Fechando a conexão */
mysql_close($conn);
?>