<?php
include('../conn.php');


if(isset($_REQUEST['upload2'])){
	
	extract($_REQUEST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = $_SERVER['DOCUMENT_ROOT'].'/intranet/projeto/anexos';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	$qr_inser = mysql_query("INSERT INTO projeto_anexos (anexo_projeto, anexo_tipo, anexo_nome, anexo_extensao, anexo_data, anexo_autor, anexo_status)
							  VALUES ('$projeto', '$tipo', '$nome', '$extensao', NOW(), '$usuario', '1')");

	if($qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	if($extensao == 'pdf'){
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/img_menu_principal/pdf.png';
	}else {
		$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$nome.'.'.$extensao;
	}
	$json_resposta['ID']   = (int) @mysql_insert_id();
	switch ($tipo) {
				case 1: $json_resposta['tipo']='proposta';
					break;
				case 2: $json_resposta['tipo']='termo';
					break;
				case 3: $json_resposta['tipo']='programa_trabalho';
					break;	
					case 4: $json_resposta['tipo']='termo_recisao';
					break;	
					case 5: $json_resposta['tipo']='publicacao_termo';
					break;	
		
		}
	//$json_resposta['tipo'] = ($tipo == '1') ? 'proposta' : 'termo';

	echo json_encode($json_resposta);
	exit;
	
}
?>