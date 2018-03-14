<?php
include ("../../include/restricoes.php");
include '../../../conn.php';

if(isset($_GET['regiao'])){

$regiao_id  = mysql_real_escape_string($_GET['regiao']);
$projeto_id =  mysql_real_escape_string($_GET['projeto']);

echo '   <option value=""> Selecione um banco...</option>';
$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao_id' AND id_projeto = '$projeto_id' ORDER BY nome ASC");
while($row_banco = mysql_fetch_assoc($qr_banco)):

echo '<option value="'.$row_banco['id_banco'].'">'.htmlentities($row_banco['nome']).' AG:'.$row_banco['agencia'].' C: '.$row_banco['conta'].'</option>';

endwhile;
	
}


?>