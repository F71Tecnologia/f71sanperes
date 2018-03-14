<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$edicao =  $_REQUEST['edicao'];
$id_entrada = $_REQUEST['entrada'];
$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['id_regiao'];
$banco = $_REQUEST['banco'];
$nome = utf8_decode($_REQUEST['nome']);
$descricao = utf8_decode($_REQUEST['descricao']);
$tipo = $_REQUEST['tipo'];
$valor_adicional = $_REQUEST['valor_adicional'];
$valor = $_REQUEST['valor'];
$data = implode('-',array_reverse(explode('/',$_REQUEST['data'])));

$id_user = $_COOKIE['logado'];


if($edicao == '1'){
	$insert = mysql_query("UPDATE entrada SET nome = '$nome',
								  especifica = '$descricao',
								  id_projeto = '$projeto',
								  id_regiao = '$regiao',
								  id_banco = '$banco',
								  tipo = '$tipo',
								  adicional = '$valor_adicional',
								  valor = '$valor',
								  data_vencimento = '$data',
								  id_user = '$id_user'
								  WHERE id_entrada = '$id_entrada'
								  LIMIT 1;
								  ");
	if(!$insert) { 
		echo 'Erro ao atualizar!';
	}else{
		echo 'Atualizado : '.$id_entrada;
	}

}else{
	$insert = mysql_query("INSERT INTO entrada 
						(nome, especifica, id_projeto, id_regiao, id_banco, tipo, adicional, valor, data_vencimento,id_user , data_proc)
						VALUES
						('$nome','$descricao','$projeto','$regiao','$banco','$tipo', '$valor_adicional', '$valor', '$data', '$id_user', NOW());");
	if(!$insert) { 
		echo 'ERRo ao inserir';
	}else{
		$qr_entrada = mysql_query("SELECT MAX(id_entrada) FROM entrada");
		echo 'INSERIDO '.@mysql_result($qr_entrada,0);
	}

}


?>