<?php
include ("../../include/restricoes.php");
include('../../../conn.php');
include('../../../upload/classes.php');


if($_COOKIE['logado'] == 87) {

print_R($_REQUEST);
exit;
}

if(isset($_POST['enviar']) and $_POST['tipo'] == 'andamentos'){
	
	

$id_processo      = mysql_real_escape_string($_POST['id_processo']);	
$data_movimento   = implode('-', array_reverse(explode('/',$_POST['data_movimento'])));
$status_processo  = mysql_real_escape_string($_POST['status_processo']);
$valor 		  	  =  str_replace(',','.',str_replace('.','',$_POST['valor']));		
$data_pg 		  = implode('-',array_reverse(explode('/',$_POST['data_pg'])));	
$n_parcelas		  = mysql_real_escape_string($_POST['n_parcelas']);
$documento = $_FILES['documento'];




$status_tipo = array(7,8,9,10); 

if(!in_array($status_processo, $status_tipo)) {

	unset($valor, $data_pg, $n_parcelas);
}



$qr_insert = mysql_query("INSERT INTO proc_trab_andamento (proc_id, 	proc_status_id, andamento_data_movi, andamento_valor,andamento_data_pg, andamento_parcelas,   andamento_data_cad, andamento_usuario_cad, andamento_status)
																		VALUES ('$id_processo', '$status_processo', '$data_movimento','$valor', '$data_pg', '$n_parcelas',  NOW(), '$_COOKIE[logado]',1)") or die(mysql_error());


$ultimo_id_andamento = mysql_insert_id();							

//VERIFICANDO A EXTENSÃO DO ANEXO
switch( $documento['type']) {	
		case 'image/jpeg': $extensao = '.jpg';
		break;
		case 'image/gif': $extensao = '.gif';
		break;
		case 'image/png': $extensao = '.png';
		break;
		case 'application/pdf': $extensao = '.pdf';
		break;
		case 'application/msword': $extensao = '.doc';
		break;	
}

$documento_nome = $ultimo_id_andamento.'_'.$status_processo; 

mysql_query("INSERT INTO proc_andamento_anexo (andamento_anexo_nome, andamento_id, andamento_anexo_ext, andamento_anexo_status)
												VALUES
												('$documento_nome', '$ultimo_id_andamento', '$extensao', '1')") or die(mysql_error());																

if($qr_insert){				
				
				move_uploaded_file($documento['tmp_name'],'anexos/'.$documento_nome.$extensao);			
					
				
}
}









////////////////////////////////////////////////////////////////////////////////////   MOVIMENTOS
if(isset($_POST['enviar']) and $_POST['tipo'] == 'movimentos'){
$id_processo      = mysql_real_escape_string($_POST['id_processo']);	
$data_movimento   = implode('-', array_reverse(explode('/',$_POST['data_movimento'])));

$qr_andamentos 	= mysql_query("SELECT * FROM proc_trab_andamento WHERE proc_id = '$id_processo' AND andamento_status = 1 ORDER BY proc_status_id  DESC");
$row_andamentos = mysql_fetch_assoc($qr_andamentos);



//$valor 		  	  =  str_replace(',','.',str_replace('.','',$_POST['valor']));		
//$data_pg 		  = implode('-',array_reverse(explode('/',$_POST['data_pg'])));	
//$n_parcelas		  = mysql_real_escape_string($_POST['n_parcelas']);
$documento        = $_FILES['documento'];
$obs 			  = $_POST['obs'];




////////////////
$status_tipo = array(7,8,9,10); 
if(!in_array($status_processo, $status_tipo)) {

	unset($valor, $data_pg, $n_parcelas);
}
//////////////////


$qr_insert = mysql_query("INSERT INTO proc_trab_movimentos (proc_id, proc_status_id, data_movimento,   obs, data_cad, user_cad, status)
																		VALUES ('$id_processo', '$row_andamentos[proc_status_id]', '$data_movimento', '$obs',  NOW(), '$_COOKIE[logado]',1)") or die(mysql_error());


$ultimo_id = mysql_insert_id();							

//VERIFICANDO A EXTENSÃO DO ANEXO
switch( $documento['type']) {	
		case 'image/jpeg': $extensao = '.jpg';
		break;
		case 'image/gif': $extensao = '.gif';
		break;
		case 'image/png': $extensao = '.png';
		break;
		case 'application/pdf': $extensao = '.pdf';
		break;
		case 'application/msword': $extensao = '.doc';
		break;	
}


$documento_nome = $ultimo_id.'_'.$status_processo; 

mysql_query("INSERT INTO proc_trab_mov_anexos (proc_trab_mov_nome, proc_trab_mov_id, proc_trab_mov_extensao, proc_trab_mov_status)
												VALUES
												('$documento_nome', '$ultimo_id', '$extensao', '1')") or die(mysql_error());																

if($qr_insert){				
				
				move_uploaded_file($documento['tmp_name'],'movimentos_anexos/'.$documento_nome.$extensao);			
					
				
}
}













if($_GET['tp'] == 2){
	header("Location: ver_trabalhador_clt.php?id_processo=$id_processo");
} else {
	header("Location: ver_trabalhador.php?id_processo=$id_processo");
}
?>