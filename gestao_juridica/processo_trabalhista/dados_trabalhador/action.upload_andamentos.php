<?php
if(isset($_REQUEST['upload'])){
	include '../../../conn.php';

	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = 'anexos';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	
	$qr_inser = mysql_query("INSERT INTO proc_andamento_anexo (andamento_anexo_nome, andamento_id, proc_id, andamento_anexo_ext, andamento_anexo_status)
												VALUES
												('$nome', '$id_andamento', '$id_processo','$extensao', '1')") or die(mysql_error());																


	if( $qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	if($extensao == 'pdf') { $json_resposta['src'] = 'http://www.netsorrindo.com/intranet/img_menu_principal/pdf.png' ;
	} else {
		$json_resposta['src']  = $diretorio.'/'.$nome.'.'.$extensao;
	}
	
	$json_resposta['ID']   = (int) @mysql_insert_id();
	


	echo json_encode($json_resposta);
	exit;
}



if(isset($_REQUEST['deletar'])){
	include "../../../conn.php";

	@mysql_query("UPDATE proc_andamento_anexo SET andamento_anexo_status = '0' WHERE andamento_anexo_id = '$_REQUEST[id_anexo]' LIMIT 1");
	exit;
}


if(isset($_REQUEST['ordem'])) {
    include "../../../conn.php";

    $id_anexo  = $_REQUEST['id_anexo'];
    $valor     = $_REQUEST['valor'];

    $qr_update = mysql_query("UPDATE proc_andamento_anexo SET andamento_anexo_ordem = '$valor' WHERE andamento_anexo_id = '$id_anexo' LIMIT 1");

    $json_resposta['erro'] = ($qr_update) ? false : true;

    echo json_encode($json_resposta);
    exit;
}



?>