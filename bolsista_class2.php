<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{
}
include "conn.php";

$regiao = $_REQUEST['regiao'];
$id = $_REQUEST['id'];
$projeto = $_REQUEST['projeto'];
?>
<htm>
<head>
<title> :: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="net.css" rel="stylesheet" type="text/css">
<script src="ajax_bolsista_class.js"></script>
<script language="javascript">
function avaliarFuncionarios(tipo, regiao, id, projeto){
	if (tipo == 'avaliados'){
		url="bolsista_class_avaliados.php?id="+id+"&regiao=" + regiao+"&projeto=" + projeto;	
		ajax(url);
	}
}
</script>
</head>
<body bgcolor='#D7E6D5'>
<input type="hidden" name="regiao" id="regiao" value="<?=$id_regiao?>">
<input type="hidden" name="ide" id="ide" value="<?=$id?>">
<input type="hidden" name="projeto" id="projeto" value="<?=$projeto?>">
<?
$resultProjeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND id_projeto = '$projeto'");
$projeto = mysql_fetch_array($resultProjeto);


?>
<table width="27%" align="center">
<caption><span class="linha"> Avaliação de desempenho em lote </span></caption>
<tr>
   <td colspan="2" align="center"><?=$projeto['nome'];?></td>
</tr>
<tr>
  <td width="4%"> <input type="radio" name="tipo" value="clt" onClick="javascript:avaliarFuncionarios(this.value)"></td>
  <td width="96%">CLT</td>
</tr>
<tr>
  <td width="4%"> <input type="radio" name="tipo" value="outros" onClick="javascript:avaliarFuncionarios(this.value)"></td>
  <td width="96%">Aut&ocirc;nomo / Cooperado</td>
</tr>
<tr>
  <td width="4%"> <input type="radio" name="tipo" value="avaliados" onClick="javascript:avaliarFuncionarios(this.value,regiao.value,ide.value, projeto.value)"></td>
  <td width="96%">Avaliados</td>
</tr>
</table>
<div id="funcionarios">

</div>
</body>
</html>