<?php 
if(isset($_REQUEST['upload'])){
	
	include('../conn.php');
	
	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = $_SERVER['DOCUMENT_ROOT'].'/intranet/projeto/anexos/';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	$qr_inser = mysql_query("INSERT INTO projeto_anexos (anexo_projeto, anexo_tipo, anexo_nome, anexo_extensao, anexo_data, anexo_autor, anexo_status)
							  VALUES ('$projeto', '$tipo', '$nome', '$extensao', NOW(), '$usuario', '1')");

	if($qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$nome.'.'.$extensao;
	$json_resposta['ID']   = (int) @mysql_insert_id();
	$json_resposta['tipo'] = ($tipo == '1') ? 'proposta' : 'termo';

	echo json_encode($json_resposta);
	exit;
	
}
?>