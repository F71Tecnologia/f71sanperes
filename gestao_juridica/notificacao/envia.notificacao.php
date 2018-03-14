<?php
include ("../include/restricoes.php");

include('../../conn.php');

$tipo 				= $_POST['tipo'];
$n_documento		= $_POST['n_documento'];
$regiao  			= $_POST['regiao'];
$projeto 			= $_POST['projeto'];
$descricao 			= nl2br($_POST['descricao']); 
$array_documentos   = $_POST['documentos'];
$array_responsaveis = $_POST['responsaveis'];	
$data_limite        = implode('-', array_reverse(explode('/',$_POST['data_limite'])));



if(isset($_POST['enviar'])) {
		
		$insert = mysql_query("INSERT INTO notificacoes (tipos_notificacoes_id, notificacao_numero, id_regiao, id_projeto, notificacao_descricao, notificacao_data_limite, notificacao_user_cad, notificacao_data_cad, notificacao_status)
		VALUES
		('$tipo', '$n_documento', '$regiao', '$projeto', '$descricao','$data_limite', '$_COOKIE[logado]', NOW(), 1)") or die(mysql_error());
		
		$notificacao_id = mysql_insert_id();
		
		
		
		if($insert) {
			
				//////GRAVANDO OS NÚMEROS DOS DOCUMENTOS	
				if(sizeof($array_documentos) == 1) {
						
						mysql_query("INSERT INTO notific_doc_assoc(notificacoes_id, nome_documento) VALUES ('$notificacao_id', '$array_documentos[0]')");	
						
				} else {
						
					foreach($array_documentos as $documento) {		$sql_documentos[] = "(".$notificacao_id.", '".$documento."')";			}
						
					mysql_query("INSERT INTO notific_doc_assoc (notificacoes_id, nome_documento) VALUES ".implode(',',$sql_documentos));
				}
				
				
				/////GRAVANDO OS RESPONSÁVEIS
				if(sizeof($array_responsaveis) == 1) {
						
						mysql_query("INSERT INTO notific_responsavel_assoc (notificacoes_id, funcionario_id) VALUES ('$notificacao_id', '$array_responsaveis[0]')")or die(mysql_error());	
						
				} else {
						
					foreach($array_responsaveis as $responsavel) {	$sql_responsavel[] = "(".$notificacao_id.", '".$responsavel."')";		}
						
					mysql_query("INSERT INTO notific_responsavel_assoc (notificacoes_id, funcionario_id) VALUES ".implode(',',$sql_responsavel))or die(mysql_error());
				}
		
		
		}
		
		
		
		
		$nome_funcionario = mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"),0);
		$nome_regiao 	  = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'"),0);
		$nome_tipo 		  = mysql_result(mysql_query("SELECT tipos_notificacoes_nome  FROM tipos_notificacoes WHERE tipos_notificacoes_id = '$tipo'"),0);
	
		
		//$headers = 'From: <INTRANET> ';		
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$menssagem = 'Foi cadastrado um nova notifica&ccedil;&atilde;o do tipo: '.$nome_tipo.' de n&uacute;mero: '.$n_documento.', no dia '.date('d/m/Y').', na regi&atilde;o '.$nome_regiao.' com a data limite para '.implode('/',array_reverse(explode('-',$data_limite))).'.<br><br>
		Autor(a) do cadastro: '.$nome_funcionario;	
		mail('fernanda.souza@sorrindo.org', 'Nova notifica&ccedil;&atilde;o cadastrada.',$menssagem,$headers);
	
		
		
		
		header("Location: cad_notificacao_2.php?id=$notificacao_id");


}


//////////////////////////////////////////////////////////////// EDITAR
if(isset($_POST['atualizar'])){
	
	
	
		$notificacao_id = $_POST['notificacao_id'];
		$update = mysql_query("UPDATE notificacoes SET tipos_notificacoes_id = '$tipo',
													  notificacao_numero = '$n_documento',
													  id_regiao = '$regiao',
													  id_projeto =  '$projeto',
													  notificacao_descricao = '$descricao',
													  notificacao_data_limite = '$data_limite',
													  notificacao_user_cad =  '$_COOKIE[logado]',
													  notificacao_data_cad = NOW()
													  WHERE notificacao_id = '$notificacao_id'");
	
		
		if($update) {
			
				//////GRAVANDO OS NÚMEROS DOS DOCUMENTOS	
				
				
				mysql_query("DELETE FROM notific_doc_assoc WHERE notificacoes_id = '$notificacao_id'");
				if(sizeof($array_documentos) == 1) {
						
						mysql_query("INSERT INTO notific_doc_assoc(notificacoes_id, nome_documento) VALUES ('$notificacao_id', '$array_documentos[0]')");	
						
				} else {
						
					foreach($array_documentos as $documento) {	if(	!empty($documento))	$sql_documentos[] = "(".$notificacao_id.", '".$documento."')";			}
						
					
					mysql_query("INSERT INTO notific_doc_assoc (notificacoes_id, nome_documento) VALUES ".implode(',',$sql_documentos));
				}
				
				
				/////GRAVANDO OS RESPONSÁVEIS
				
				mysql_query("DELETE FROM notific_responsavel_assoc WHERE notificacoes_id = '$notificacao_id'");
				if(sizeof($array_responsaveis) == 1) {
						
						mysql_query("INSERT INTO notific_responsavel_assoc (notificacoes_id, funcionario_id) VALUES ('$notificacao_id', '$array_responsaveis[0]')")or die(mysql_error());	
						
				} else {
						
					foreach($array_responsaveis as $responsavel) {		if(	!empty($responsavel))   $sql_responsavel[] = "(".$notificacao_id.", '".$responsavel."')";		}
						
					mysql_query("INSERT INTO notific_responsavel_assoc (notificacoes_id, funcionario_id) VALUES ".implode(',',$sql_responsavel))or die(mysql_error());
				}
		
		
		}
		header("Location: cad_notificacao_2.php?id=$notificacao_id");

	
	
	
	
	
	
	
	
	
	
	
	
	
	
}








?>
