<?php 

include "../../conn.php";


$charset = mysql_set_charset('utf8');

$tipo = $_POST['tipo'];
$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$descricao = $_POST['descricao'];

$sql = "INSERT INTO entradaesaida_nomes (id_entradasaida,nome,cpfcnpj,descricao) VALUES ('$tipo','$nome','$cpf','$descricao')";
mysql_query($sql);

$id = mysql_insert_id();
$query = mysql_query("SELECT id_nome,nome FROM entradaesaida_nomes WHERE id_nome = '$id'");
$row = mysql_fetch_object($query);
$json[] = $row;
echo json_encode($json);

?>