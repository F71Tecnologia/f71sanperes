<?php
if(empty($_COOKIE['logado'])) {
    return false;
}

include('../../conn.php');

$projeto_id = $_POST['projeto_id'];
$options = '<option value="">Selecione</option>';

$qr_unidades = mysql_query("SELECT * FROM unidade WHERE campo1 = '{$projeto_id}'");
while($row_unidade = mysql_fetch_assoc($qr_unidades)) {
	$options .= '<option value="'.$row_unidade['id_unidade'].'">'.utf8_encode($row_unidade['unidade']).'</option>';
}

echo $options;
exit;
?>
