<?php
if(isset($_REQUEST['upload'])){
	include '../../../conn.php';

	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = 'anexo_modelo7';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	
	$qr_inser = mysql_query("INSERT INTO rel_comissao_avaliacao (entregue_id, 	anexo_nome, anexo_extensao, anexo_status)
							  VALUES ('$entregue_id',  '$nome', '$extensao', '1')");

	if( $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	$json_resposta['src']  = 'anexo_modelo7/'.$nome.'.'.$extensao;
	$json_resposta['ID']   = (int) @mysql_insert_id();
	
	
	switch($tipo) {
		
	
		
		case 3: $json_resposta['tipo'] == 'proposta_parceria';
		break;
		
		
		
		}


	echo json_encode($json_resposta);
	exit;
}

if(isset($_REQUEST['deletar'])){
	include "../../../conn.php";

	@mysql_query("UPDATE rel_comissao_avaliacao SET anexo_status = '0' WHERE anexo_id = '$_REQUEST[id_anexo]' LIMIT 1");
	exit;
}


if(isset($_REQUEST['ordem'])) {
    include "../../../conn.php";

    $id_anexo  = $_REQUEST['id_anexo'];
    $valor     = $_REQUEST['valor'];

    $qr_update = mysql_query("UPDATE rel_comissao_avaliacao SET anexo_ordem = '$valor' WHERE anexo_id = '$id_anexo' LIMIT 1");

    $json_resposta['erro'] = ($qr_update) ? false : true;

    echo json_encode($json_resposta);
    exit;
}



?>