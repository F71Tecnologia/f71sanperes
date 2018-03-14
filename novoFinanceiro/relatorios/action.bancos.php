<?php
include ("../include/restricoes.php");
	
include "../../conn.php";
include "../../funcoes.php";

if(isset($_GET['regiao'])){

$regiao = mysql_real_escape_string($_GET['regiao']);

$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao'");
while($row_banco = mysql_fetch_assoc($qr_bancos)):

echo '<option value="'.$row_banco['id_banco'].'"> '.$row_banco['id_banco'].' - '.$row_banco['nome'].' - '.$row_banco['conta'].' / '.$row_banco['agencia'].'</option> ';

endwhile;	
}

?>