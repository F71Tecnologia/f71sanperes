<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id = $_REQUEST['id'];

switch ($id){
case 1:

$id_tarefa = $_REQUEST['tarefa'];

$result = mysql_query("Select *, date_format(data_criacao, '%d/%m/%Y') as criacao, date_format(data_entrega, '%d/%m/%Y') as entrega from tarefa where id_tarefa = '$id_tarefa' ");
$row = mysql_fetch_array($result);

$status_tarefa = "$row[status_tarefa]";

if ($status_tarefa == 0 ){
	$men1 = "Tarefa Concluída";
	$cor = "blue";
	$botao = "";
}else{
	$men = "Tarefa em Aberto";
	$cor = "red";
	$botao = " <div style=\"text-align:center;\">
	<strong>Digite uma resposta ao criador desta tarefa:</strong><br>
	<table align=\"center\">
		<tr>
			<td align=\"center\">
				
				<textarea name='texto' id='texto' cols='80' rows=\"10\"></textarea><br><br>
				<input type='submit' value='Passar para TAREFA REALIZADA'>
			</td>
		</tr>
	</table>
	</div>
	";
}


$teste = nl2br($row['descricao']);
$anexo_nome = $row['anexo_nome'];
$anexo_extensao = $row['anexo_extensao'];

?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="net1.css" rel="stylesheet" type="text/css">
<script src="jquery/nicEdit.js" type="text/javascript"></script>
<script type="application/javascript">

//<![CDATA[
        bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
  //]]>
  
</script>


<style type="text/css">
body {
	margin:0px;
	background-color:#fafafa;
}
.normal {
	font-weight:normal;
	text-align:left;
}
p {
	margin:0px;
}
</style>
</head>
<body>
<table width="100%" border="0" cellpadding="12" cellspacing="0">
<tr bgcolor="#dee3ed">
<td colspan="2" style="border-bottom:1px solid #444;">
<table width="100%" cellpadding="2" cellspacing="0" style="font-weight:bold; font-size:12px; border:0px; text-align:right;">
<tr>
<td width="8%">De:</td>
<td width="22%" class="normal"><?=$row['criador']?></td>
<td width="15%">Data de Entrega:</td>
<td width="55%" class="normal"><?=$row['entrega']?></td>
</tr>
<tr>
<td>Assunto:</td>
<td class="normal"><?=$row['tarefa']?></td>
<td>Data de Criação:</td>
<td class="normal"><?=$row['criacao']?></td>
</tr>
</table>
</td>
</tr>
<tr> 
<td colspan="2" align="center">
<p>&nbsp;</p>
<b>Status:</b> <?=$men?>


<div style="float:left;text-align:left;"> 

<?php if (!empty($anexo_nome) and !empty($anexo_extensao)) : ?>

<table>
<tr>
	<td align="right" valign="top"><strong>Anexo: </strong></td>
    <td  valign="top" >
<?php  

switch ($anexo_extensao) {
	
		case ('.jpg' ): echo '<a href="anexo_tarefa/'.$anexo_nome.$anexo_extensao.'"/> <img src="anexo_tarefa/'.$anexo_nome.$anexo_extensao.'" width="20%" height="20%" title="Visualizar anexo"> </a>';
		break;
		
		case ('.gif' ): echo '<a href="anexo_tarefa/'.$anexo_nome.$anexo_extensao.'"/> <img src="anexo_tarefa/'.$anexo_nome.$anexo_extensao.'" width="20%" height="20%" ><br>      Visualizar imagem </a>';
		break;
		
		case ('.png' ): echo '<a href="anexo_tarefa/'.$anexo_nome.$anexo_extensao.'"/> <img src="anexo_tarefa/'.$anexo_nome.$anexo_extensao.'" width="20%" height="20%" ><br>      Visualizar imagem </a>';
		break;
		
		
		case '.pdf': echo '
							<a href="anexo_tarefa/'.$anexo_nome.$anexo_extensao.'"> <img src="imagens/pdf.gif" /> Visualizar arquivo</a>';
					 
		break;
}

endif;
?>
</td>
</tr>
</table>

</div>

<div style="clear:left;"></div>


<hr style="border:1px solid #ccc;">
<div style="font-size:13px; text-align:left;background-color: #FFF;"><?=$teste?></div>




<hr style="border:1px solid #ccc;">
</td>
</tr>
<tr>
<td colspan="2" align="center">
<form action="cadastro2.php" name="form1" method="post">
<input type="hidden" name="id_tarefa" value="<?=$row[0]?>">
<input type="hidden" name="id_cadastro" value="11">
<?=$botao?>
</form></td>
</tr>
<tr> 
<td colspan="2" align="center"><a href='javascript:window.close()' class="link"><img src="imagens/voltar.gif" border="0"></a></td>
</tr>
</table>
<?php
break;

case 2:

$id_tarefa = $_REQUEST['tarefa'];

$result = mysql_query("Select *, date_format(data_criacao, '%d/%m/%Y') as criacao, date_format(data_entrega, '%d/%m/%Y') as entrega from tarefa where id_tarefa = '$id_tarefa' ");
$row = mysql_fetch_array($result);

$teste = nl2br($row['descricao']);

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "
<body bgcolor='#D7E6D5'>
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr><td colspan='2'><div align='center' class='style1'>Projetos</div></td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td>
</tr>
<tr><td align='right'>Criador da Tarefa:</td>
<td>&nbsp;&nbsp; <font color=#FF0000>$row[criador]</font></td></tr>
<tr><td align='right'>Tarefa:</td>
<td>&nbsp;&nbsp; <font color=#FF0000>$row[tarefa]</font></td></tr>
<tr> 
<td align='right'>Data de Criação:</td>
<td>&nbsp;&nbsp; <font color=#FF0000>$row[criacao]</font></td>
</tr>
<tr> 
<td align='right'>Data de Entrega:</td>
<td>&nbsp;&nbsp; <font color=#FF0000>$row[entrega]</font></td>
</tr>
<tr> 
<td align='right'>Status da Tarefa:</td>
<td>&nbsp;&nbsp; <font color=#FF0000>$row[tarefa]</font></td>
</tr>
<tr> 
<td align='right'>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr> 
<td align='right' valign='top'>Descri&ccedil;&atilde;o:</td>
<td>&nbsp;</td>
<tr> 
<td align='right'>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr> 
<td align='center' colspan='2'><font color=#FF0000>$teste</font></td>
</tr>
<tr> 
<td colspan='2' align='center'></td>
</tr>
<tr>
<td colspan='2' align='center'>
</td>
</tr>
<tr> 
<td colspan='2' align='center'>&nbsp;</td>
</tr>
</table>
<br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";
break;


case 3:  					//EXCLUIR TAREFA

$id_tarefa = $_REQUEST['tarefa'];

$Num = count($id_tarefa);

for($i=0 ; $i < $Num; $i ++){
	mysql_query ("UPDATE  tarefa set status_reg = '0' where id_tarefa = '".$id_tarefa[$i]."'");
}

print "<script> location.href=\"principal.php\"; </script>";

break;



case 4:
$id_tarefa = $_REQUEST['tarefa'];



print "
<html><head><title>:: Intranet ::</title>
<script>
function fechar_janela(x) {
opener.location.href = 'principal.php';
return eval(x)
}
</script>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
</head><body bgcolor='#D7E6D5'>";

print "<br><br><center><span class='style27'> Tarefa Apagada! </span><br><br><a href='javascript:fechar_janela(window.close())''><img src='imagens/voltar.gif' border=0></a><center>";

break;

}

}
?>