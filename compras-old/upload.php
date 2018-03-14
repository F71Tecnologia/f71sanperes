<?php
if(isset($_REQUEST['upload'])){
	include '../conn.php';

	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = 'anexo_compras';
	
	
	
	
	$qr_inser = mysql_query( "INSERT INTO anexo_compra (id_compra, fornecedor, anexo_extensao, anexo_status) VALUES ('$id_compra' ,'$tipo',  '$extensao', '1')") or die(mysql_error());															
	$id_file = (int) @mysql_insert_id();
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$id_file.'.'.$extensao);


	if( $qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	if($extensao == 'pdf') { $json_resposta['src'] = 'http://www.netsorrindo.com/intranet/img_menu_principal/pdf.png' ;
	} else {
		$json_resposta['src']  = $diretorio.'/'.$id_file.'.'.$extensao;
	}
	
	$json_resposta['ID']   = $id_file;
	
	switch($tipo){
		
		
		case 1: $json_resposta['tipo'] = 'fornecedor1' ;
		break;
		case 2 : $json_resposta['tipo']= 'fornecedor2';
		break;
		case 3: $json_resposta['tipo']= 'fornecedor3';
		break;
	}





	echo json_encode($json_resposta);
	exit;
}



if(isset($_REQUEST['deletar'])){
	include "../conn.php";

	@mysql_query("UPDATE anexo_compra SET anexo_status = '0' WHERE anexo_id = '$_REQUEST[id_anexo]' LIMIT 1");
	exit;
}


if(isset($_REQUEST['ordem'])) {
    include "../conn.php";

    $id_anexo  = $_REQUEST['id_anexo'];
    $valor     = $_REQUEST['valor'];

   $qr_update = mysql_query("UPDATE anexo_compra SET anexo_ordem = '$valor' WHERE anexo_id = '$id_anexo' LIMIT 1");

    $json_resposta['erro'] = ($qr_update) ? false : true;

    echo json_encode($json_resposta);
    exit;
}



?>