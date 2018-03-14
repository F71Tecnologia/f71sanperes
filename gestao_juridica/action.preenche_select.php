<?php
include ("include/restricoes.php");

include('../conn.php');

if(isset($_GET['adv'])) {
$qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");

while($row_advogado = mysql_fetch_assoc($qr_advogado)):

if($row_advogado['adv_id'] == $advogados[0]) {$selected  = 'selected="selected"';  $adv_id = $row_advogado['adv_id']; } else {$selected  = '';}
?>
	 <option value="<?php echo $row_advogado['adv_id']?>" <?php echo $selected ?> > <?php echo $row_advogado['adv_nome']?> </option>

<?php
endwhile;

}



if(isset($_GET['regiao'])) {
	
$id_regiao = mysql_real_escape_string($_GET['regiao']);
$qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$id_regiao' ORDER BY status_reg,nome");

while($row_projeto = mysql_fetch_assoc($qr_projetos)):

if($row_projeto['status_reg'] != $status_anterior) {
	
	if($row_projeto['status_reg'] == 1) { echo ' <option></option><optgroup label="PROJETOS ATIVOS"></optgroup>'; } else if($row_projeto['status_reg'] == 0){  echo '<option></option><optgroup label="PROJETOS DESATIVADOS"></optgroup>'; }	
}

?>
	 <option value="<?php echo $row_projeto['id_projeto']?>"  > <?php echo  utf8_encode($row_projeto['nome'])?> </option>

<?php


$status_anterior = $row_projeto['status_reg'];
endwhile;
	
	
}



if(isset($_GET['funcionario'])) {
	

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE status_reg = 1 ORDER BY nome") or die(mysql_error());

while($row_funcionario = mysql_fetch_assoc($qr_funcionario)):


?>
	 <option value="<?php echo $row_funcionario['id_funcionario']?>" style="text-transform:uppercase"> <?php echo  htmlentities($row_funcionario['nome1'])?> </option>

<?php

endwhile;
	
}
?>
