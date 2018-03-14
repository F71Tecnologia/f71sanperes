<?php
include ("../../include/restricoes.php");
include '../../../conn.php';

if(isset($_GET['regiao'])){

$regiao_id = mysql_real_escape_string($_GET['regiao']);


echo '<option value=""> Selecione um projeto...</option>';

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao_id' AND status_reg != 2 ORDER BY status_reg DESC");
while($row_projeto = mysql_fetch_assoc($qr_projeto)):

if($status_anterior != $row_projeto['status_reg']){
	
	$nome_status = ($row_projeto['status_reg'] == 1)? '<optgroup label="PROJETOS ATIVOS"></optgroup>' : '<optgroup label="PROJETOS INATIVOS"></optgroup>';
	echo $nome_status;
	
}

echo '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['id_projeto'].' - '.utf8_encode($row_projeto['nome']).'</option>';


$status_anterior = $row_projeto['status_reg'];
endwhile;
	
}


?>