<?php

include 'config.php';
include 'constants.php';
include 'functions.php';
include 'imap_connection.php';
include '../vendor/autoload.php';

$request = parse_request(array(
	'mailbox' => null,
	'email_uid' => null,
));

$mailbox   = (($_REQUEST['mailbox']=="INBOX.Inbox")?"INBOX":$_REQUEST['mailbox']);
$email_uid = (int) $request['email_uid'];
mb_internal_encoding('utf-8');

// marca o email como lido
imap_setflag_full($imap_stream, $email_uid, "\\Seen");

//-------------------------- BUILD MESSAGE VIEW --------------------------------

$server = new \Fetch\Server($_SESSION['host'], $_SESSION['port']);
$server->setAuthentication($_SESSION['email'], $_SESSION['password']);

$utf7_mailbox = imap_utf7_encode($mailbox);
$server->setMailBox($utf7_mailbox);

$message = new \Fetch\Message($email_uid, $server);
$headers = $message->getHeaders();

$parsed_header = array(
	'date' => null,
	'subject' => null,
	'toaddress' => null,
	'senderaddress' => null,
);

foreach ($headers as $key => $value) {
	if (in_array($key, array_keys($parsed_header))) $parsed_header[$key] = $value;
}
$subject = imap_mime_header_decode($parsed_header['subject']);
$parsed_header['subject'] = $subject[0]->text;

header('Content-type: application/json');
exit(json_encode(
	$parsed_header
));