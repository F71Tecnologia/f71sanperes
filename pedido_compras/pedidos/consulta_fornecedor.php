<?php
include("../../conn.php");

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "consultar"){
$cnpj_fornecedor = $_REQUEST['cnpj_fornecedor'];
 
//$reg = simplexml_load_file("http://cep.republicavirtual.com.br/web_cep.php?formato=xml&cep=" . $cnpj_fornecedor);
 
$reg1 = mysql_query("SELECT * FROM fornecedores WHERE cnpj = '$cnpj_fornecedor'");
$reg = mysql_fetch_assoc($reg1);

$reg3 = mysql_num_rows($reg1);


$dados['sucesso'] =   $reg3;
$dados['nome']     =  $reg['nome'];
$dados['razao']  =  $reg['razao'];
$dados['endereco']  =  $reg['endereco'];
$dados['tel']  =  $reg['tel'];
$dados['email']  = $reg['email'];
 
echo json_encode($dados);
}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "arquivoPrincipal"){
    $id = $_REQUEST['id'];

    $reg1 = mysql_query("SELECT * FROM item_orcamento WHERE id_item_pedido = '$id' AND principal = 1");
    $reg = mysql_fetch_assoc($reg1);

    $reg3 = mysql_num_rows($reg1);

    $dados['sucesso'] =   $reg3;
    echo json_encode($dados);

}