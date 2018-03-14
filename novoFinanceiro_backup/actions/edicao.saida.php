<?php 
include "../../conn.php";


$id_saida = $_POST['ID'];
$tipo = $_POST['tipo'];
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];

$query_nome = mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_nome = '$nome'");
$row_nome = mysql_fetch_assoc($query_nome);

$sql = "UPDATE saida SET ";
$matriz[] = "nome = '$row_nome[nome]'";
$matriz[] = "id_nome = '$row_nome[id_nome]'";
$matriz[] = "especifica = '$descricao'";
$matriz[] = "tipo = '$tipo'";

$sql .= implode(", ",$matriz);

$sql .= " WHERE id_saida = '$id_saida' LIMIT 1;";
mysql_query($sql)or die(mysql_error().' \n '.$sql);
?>