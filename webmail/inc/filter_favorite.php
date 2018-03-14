<?php
session_start();

if($_SESSION['favorite'] == 'S')
{
    $_SESSION['favorite'] = 'N';
}else
{
    $_SESSION['favorite'] = 'S';
}
exit();
$where_request = isset($_REQUEST['where'])? $_REQUEST['where']: null;

if (!in_array($where_request, array(
	'list_emails',
	'list_mailboxes',
))) exit;

$mail_buffer = $_SESSION['mailbuffer']; //handle mailbuffer by ref on session storage
if(!isset($_SESSION['mailbuffer'])) {
	$_SESSION['mailbuffer'] = array(
		'list_emails' => null,
		'list_mailboxes' => null,
	);
}

//echo "where_request = [$where_request]<br>\n";

exit(
	eval(
		call_user_func($where_request)
	)
);


function list_emails() {
	$mail_buffer['list_emails'] = isset($_SESSION['mailbuffer']['list_emails'])?
	$_SESSION['mailbuffer']['list_emails']: file_get_contents("list_emails.php");

	//echo "buffer = [".$mail_buffer['list_emails']."]<br>\n";
	
	//###########################################################################################
	//
	// Cuidado! Se o arquivo PHP que o buffer chamar tiver a tag de início do PHP < vai dar ruim!
	
	return $mail_buffer['list_emails'];
}
