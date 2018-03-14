<?php
include_once 'db_utils.php';

$email = $_REQUEST['email'];
$sql = "select * from funcionario_email_assoc e where e.email = '{$email}';";
$sql = "
		select 
			e.senha, m.email_servidor 
		from 
			funcionario_email_assoc e inner join 
			master m on e.id_master = m.id_master
		where 
			e.email = '{$email}';";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) 
{
	$pass = $row['senha'];
	$dom = $row['email_servidor'];
}

echo "imap_open({".$dom.":143/novalidate-cert}INBOX\", {$email}, {$pass})";

$mbox = imap_open("{".$dom.":143/novalidate-cert}INBOX", $email, $pass)
      or die("can't connect: " . imap_last_error());

$check = imap_mailboxmsginfo($mbox);

if ($check) {
	$var[0] = array('date'=>$check->Date,
				'messages'=>$check->Nmsgs, 
				'recent'=>$check->Recent, 'unread'=>$check->Unread, 
				'deleted'=>$check->Deleted, 'size'=>$check->Size,
				'error'=>'');
} else {
	$var[0] = array('error'=>"imap_check() failed: " . imap_last_error());
}
imap_close($mbox);

//header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
echo json_encode($var);
?>