<?php

include 'imap_connection.php';

$uid = $_REQUEST['uid'];

$response = array();

if (filter_var($uid, FILTER_VALIDATE_INT)) {
	$msg_no = @imap_msgno($imap_stream, $uid);
	$msg_no++;
	
	$new_uid = @imap_uid($imap_stream, $msg_no);
	
	if (is_int($new_uid)) {
		$response['new_uid'] = $new_uid;
	}
	else {
		$response['error'] = "Email UID inválido";
	}
}
else {
	$response['error'] = "Email UID inválido";
}

exit(json_encode($response));