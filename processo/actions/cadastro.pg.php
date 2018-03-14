<?php
require("../../classes/uploadfile.php");
require("../../conn.php");

if(!empty($_FILES['Filedata'])){
	
		
		$upload = new UploadFile("../comprovantes",array('gif','pdf','jpg','GIF','PDF','JPG'));
		$upload->arquivo($_FILES['Filedata']);
		$nome_file = uniqid($_REQUEST['id'].'_file');
		$upload->NomeiaFile($nome_file);
		$upload->verificaFile();
		$upload->Envia();
		mysql_query("INSERT INTO prestador_pg_files (id_pg, nome ,tipo) VALUES ('{$_REQUEST['id']}' ,'{$nome_file}' , '$upload->extensao');");
		mysql_query("UPDATE prestador_pg SET comprovante = '2' WHERE  id_pg = '{$_REQUEST['id']}' LIMIT 1;");
		echo json_encode(array('msg' => 'Cadastrado com sucesso!'));
		exit();
		
}

$regiao 		= $_REQUEST['regiao'];
$id_prestador 	= $_REQUEST['prestador'];
$valor 			= $_REQUEST['valor'];
$data 			= $_REQUEST['data'];
$documento 		= $_REQUEST['documento'];
$valor 			= str_replace(".","", $valor);

/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/

function ConverteData($Data){
	if (strstr($Data, "/")){//verifica se tem a barra /
		$d = explode ("/", $Data);//tira a barra
		$rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
		return $rstData;
	} elseif(strstr($Data, "-")){
		$d = explode ("-", $Data);
		$rstData = "$d[2]/$d[1]/$d[0]"; 
		return $rstData;
	}else{
		return "Data invalida";
	}

}

$data_f = ConverteData($data);

$result_cont = mysql_query("SELECT id_pg FROM prestador_pg where id_prestador = '$id_prestador'") 
or die ("Erro<br>".mysql_error());

$row_cont = mysql_num_rows($result_cont);

$parcela = $row_cont + 1;

mysql_query("INSERT INTO prestador_pg(id_prestador,id_regiao,valor,data,documento,parcela) 
values 
('$id_prestador','$regiao','$valor','$data_f','$documento','$parcela')") or die ("Erro<br>".mysql_error());

$max_query = mysql_query("SELECT MAX(id_pg) FROM prestador_pg");
$id_max = mysql_result($max_query,0);

	
	$array_responsta = array(
		'msg' => 'Cadastrado com sucesso!',
		'id'	=> $id_max
	);
	echo json_encode($array_responsta );
	exit;
?>