<?php
include('conn.php');

if(isset($_GET['master'])){

$master_id = mysql_real_escape_string($_GET['master']);
$email     = mysql_real_escape_string($_GET['email']);
$senha     = base64_decode($_GET['senha']);


$servidor_dominio = mysql_result(mysql_query("SELECT email_servidor FROM master WHERE id_master = '$master_id'"),0);


//VERIFICA SE A SENHA FOI ALTERADA PARA ATUALIZAR NO SISTEMA
$servidor = '{'.$servidor_dominio.':143/novalidate-cert}INBOX';
ini_set('display_errors', '1');
$mbox = @imap_open($servidor , $email, $senha) ;	
$erro = imap_last_error();	

if(!empty($erro)) {	
	echo 0;
	
	} else {
	
	echo  1;
	}
}
