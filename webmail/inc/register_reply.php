<?php
session_start();

include_once 'db_utils.php';

include 'imap_connection.php';

$uid = $_REQUEST['uid'];
$action = str_replace(':', '', trim(strtolower($_REQUEST['action'])));

$response = array(
	'uid'    => $uid,
	'action' => $action
);

if (is_numeric($uid) && isset($action)) {
	if ($action == 'fwd') {
		$result = mysql_query("INSERT INTO webmail_msg_forwarded (id_message) VALUES ({$uid})");
	}
	else if ($action == 're')  {
		$result = mysql_query("INSERT INTO webmail_msg_answered (id_message) VALUES ({$uid})");
	}
}


echo json_encode($response);
