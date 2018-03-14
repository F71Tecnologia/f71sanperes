<?php
include("../../conn.php");
include("../../wfunction.php");
//
//print_array($_REQUEST);
//exit();

$valor = $_REQUEST['valor_fornecedor'];
$cnpj = $_REQUEST['cnpj_fornecedor'];

//echo "SELECT * FROM fornecedores WHERE cnpj = '{$cnpj}'";
//exit();

$id_item_pedido = $_REQUEST['id_pedido'];
$query_for = mysql_query("SELECT * FROM fornecedores WHERE cnpj = '{$cnpj}'");
$query_for_row = mysql_num_rows($query_for);
$anexo = $_REQUEST[''];

if($query_for_row){
  $result = mysql_fetch_assoc($query_for);
  $id_fornecedor = $result['id_fornecedor']; 
}else{
  $nome = $_REQUEST['nome_fornecedor'];
  $razao = $_REQUEST['razao_fornecedor'];
  $endereco = $_REQUEST['endereco_fornecedor'];
  $telefone = $_REQUEST['tel_fornecedor'];
  $email = $_REQUEST['email_fornecedor'];

  $query_for = mysql_query("INSERT INTO fornecedores (nome,razao,endereco,cnpj,tel,email) VALUES ('{$nome}','{$razao}','{$endereco}','{$cnpj}','{$telefone}','{$email}')") OR die(mysql_error());
  $id_fornecedor = mysql_insert_id();
  
  
}

$query_item = mysql_query("INSERT INTO item_orcamento (id_item_pedido,id_fornecedor,valor,anexo) VALUES ('{$id_item_pedido}','{$id_fornecedor}','{$valor}','{$anexo}')") OR die(mysql_error());

echo mysql_insert_id();
exit;
?>