<?php
$id_compra  = mysql_real_escape_string($_GET['compra']);

///usuario
$qr_user  = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user); 

//master
$qr_master  = mysql_query("SELECT * FROM master WHERE id_master= '$row_user[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

//compra
$qr_compra  = mysql_query("SELECT * FROM compra2 WHERE id_compra = '$id_compra'");
$row_compra = mysql_fetch_assoc($qr_compra);


$ano = date('Y');
$dia = date('d');
$mesnum = date('m');

$mes = sprintf('%02s',date('m'));

$mes = mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'"), 0);


$nome_fornecedor1 = @mysql_result(mysql_query("SELECT nome FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor1]'"),0);
$nome_fornecedor2 = @mysql_result(mysql_query("SELECT nome FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor2]'"),0);
$nome_fornecedor3 = @mysql_result(mysql_query("SELECT nome FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor3]'"),0);

switch($row_compra['fornecedor_escolhido']){

	case 1: $fornecedor_escolhido = $nome_fornecedor1;
	break;
	
	case 2: $fornecedor_escolhido = $nome_fornecedor2;
	break;
	
	case 3: $fornecedor_escolhido = $nome_fornecedor3;
	break;

	
}



function formato_valor($valor){

$valor = str_replace(',','.',str_replace('.','',$valor));

return $valor;
	
}

?>
