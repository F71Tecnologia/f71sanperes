<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "../conn.php";

include('../classes_permissoes/acoes.class.php');

$id_compra = $_REQUEST['compra'];
$id_regiao = $_REQUEST['regiao'];
$valorlimite = $_REQUEST['valorlimite'];

echo "<br>id_compra=".$id_compra;
echo "<br>id_regiao=".$id_regiao;
echo "<br>valorlimite=".$valorlimite;
$qry_select = mysql_query("SELECT * FROM prestadorservico WHERE id_compra='$id_compra'");
$dados_prest = mysql_fetch_assoc($qry_select);

	mysql_query("UPDATE prestadorservico SET 
		valor_limite = '$valorlimite' WHERE id_compra = '$id_compra' ") or die ("Erro<br>".mysql_error());
		
		
print "<link href='adm/css/estrutura.css' rel='stylesheet' type='text/css'>
<br>
<div id='corpo'>
<div id='conteudo'>
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
<br>
<hr>
<center>
<font color=#FFFFFF>
Informações gravadas com sucesso!<br><br>
</font>
<br><br>
<a href='../gestaocompras.php?id=1&regiao=$regiao'><img src='imagens/voltar.gif' border=0></a>
</center>
";



}