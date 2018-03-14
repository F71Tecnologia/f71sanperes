<?php

include_once 'constants.php';
include_once 'imap_connection.php';
include_once 'functions.php';
include_once 'db_utils.php';

mb_internal_encoding('utf-8');

$response = array();
$search = parse_request(array(
	'mailbox' => null,
	'search' => null,
	'search_criteria' => null,
	'search_date_criteria' => null,
));

$search['mailbox'] = $_SESSION['boxfull'];

//echo "criteria = [{$search['search_criteria']}]\n";

if (!$search['search_criteria']) {
	exit(json_encode(array(
		'error' => 'dont know where to search',
	)));

} else if (preg_match('/from/', $search['search_criteria'])) {
	$search_query = 'FROM "'.$search['search'].'"'. ((isset($search['search_date_criteria']))? strtoupper($search['search_date_criteria'][0].' "'.$search['search_date_criteria'][1].'"'):'');

} else if (preg_match('/subject/', $search['search_criteria'])) {
	$search_query = 'SUBJECT "'.$search['search'].'"'.((isset($search['search_date_criteria']))? strtoupper($search['search_date_criteria'][0].' "'.$search['search_date_criteria'][1].'"'):'');

} else if (preg_match('/email_text/', $search['search_criteria'])) {
	$search_query = 'BODY "'.$search['search'].'"'.((isset($search['search_date_criteria']))? strtoupper($search['search_date_criteria'][0].' "'.$search['search_date_criteria'][1].'"'):'');

}

$imap_stream  = imap_open($hostref.$search['mailbox'], $email, $password, OP_READONLY);

if (!$imap_stream) exit(json_encode(array(
	'error' => 'can open mailbox '.$search['mailbox'].' to search',
)));

$uids = imap_search($imap_stream, $search_query, SE_UID);

if (!$uids) exit(json_encode(array(
	//'nenhum email encontrado',
)));

foreach($uids as $uid) {
	if (!$uid) continue;

	$overview = imap_fetch_overview($imap_stream, $uid, FT_UID);

	$qr_reply = mysql_query("SELECT id FROM webmail_msg_answered WHERE id_message = '".($uid)."' LIMIT 1");
	$count_reply = mysql_num_rows($qr_reply);

	$qr_rw = mysql_query("SELECT id FROM webmail_msg_forwarded WHERE id_message = '".($uid)."' LIMIT 1");
	$count_rw = mysql_num_rows($qr_rw);

	$reply = ($count_reply == 1) ? true : false;
	$forwarded = ($count_rw == 1) ? true : false;


	$subject = sanitize_subject($overview[0]->subject);
	$subject = $subject ? $subject : ($overview[0]->subject ? imap_utf8($overview[0]->subject) : "(Sem assunto)");
        //if (!$overview[0]->seen) $mails_unseen_n++;
	$a_response = array(
		'li_class'  => $overview[0]->seen ? 'read' : 'unread',
		'uid'       => $uid,
		'number'    => imap_msgno($imap_stream, $uid),
		'has_att'   => has_attachment($imap_stream, $uid),
		'has_reply' => $reply,
		'has_rw'    => $forwarded,
		'from'      => $overview[0]->from ? htmlspecialchars(imap_utf8($overview[0]->from)) : "(Sem remetente)",
		'to'        => $overview[0]->to   ? imap_utf8($overview[0]->to)                     : "(Sem destinat?rio)",
		'subject'   => $subject
	);

	try {
		$a_response['date'] = date_format(new DateTime( preg_replace('/\s*\([^\)]*\)\s*$/i', '', $overview[0]->date) ), "d/m/Y H:i");
	}
	catch (Exception $e) {
		$a_response['date'] = $overview[0]->date;
	}

	$response[] = $a_response;
}

exit(json_encode(array(
		'emails' => $response,
		'mailbox'=> $_SESSION['boxfull']
)));