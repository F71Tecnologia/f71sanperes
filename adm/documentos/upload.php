<?php
include('../../conn.php');


//insere o anexo
if(isset($_REQUEST['upload'])){

	
	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = $_SERVER['DOCUMENT_ROOT'].'/intranet/adm/documentos/anexos';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	
	$qr_inser = mysql_query("INSERT INTO modelo_documento_anexos (anexo_id, anexo_nome, anexo_extensao, anexo_data, anexo_status)
																 VALUES
																 ('','$nome','$extensao', NOW(), '1') ");

	if($up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	if($extensao == 'doc'){
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/imagens/word.jpg';
	} 
	
	if($extensao == 'pdf'){
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/imagens/Acrobat1.png';
	} 
	
		if($extensao == 'jpg'){
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/documentos/anexos/'.$nome.'.'.$extensao;
	}
	
	
	$json_resposta['ID']   = (int) @mysql_insert_id();
	$json_resposta['tipo'] =  'documento';

	echo json_encode($json_resposta);
	exit;
	
}





//Atualiza o anexo
if(isset($_REQUEST['upload2'])){

	
	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = $_SERVER['DOCUMENT_ROOT'].'/intranet/adm/documentos/anexos';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	
	$qr_inser = mysql_query("UPDATE modelo_documento_anexos SET anexo_nome = '$nome', anexo_extensao = '$extensao', anexo_data = NOW() WHERE anexo_id_documento = '$documento_id' LIMIT 1");

	if($up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	if($extensao == 'doc'){
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/imagens/word.jpg';
	} 
	
	if($extensao == 'pdf'){
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/imagens/Acrobat1.png';
	} 
	
	if($extensao == 'jpg'){
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/documentos/anexos/'.$nome.'.'.$extensao;
	}
	
	$json_resposta['ID']   = (int) @mysql_insert_id();
	$json_resposta['tipo'] =  'documento';

	echo json_encode($json_resposta);
	exit;
	
}

?>