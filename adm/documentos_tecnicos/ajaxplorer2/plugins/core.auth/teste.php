<?php
include "../../../../../conn.php";


if(isset($_GET['teste'])){
	
$teste = $_GET['teste'];
$qr_funcionario = mysql_query("SELECT * FROM doc_tecnico_acesso WHERE funcionario_id = '$_COOKIE[logado]'");
$row_funcionario = mysql_fetch_assoc($qr_funcionario);
$json = array('usuario' => $row_funcionario['usuario'],
			  'senha'   => $row_funcionario['senha']
			  );
echo json_encode($json);

exit();
}
?>
