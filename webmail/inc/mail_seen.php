<?php
session_start();

include '../vendor/autoload.php';

if (!isset($_REQUEST['uids']) || !isset($_REQUEST['mailbox'])) {
    exit;
}

$email    	= $_SESSION['email'];
$password 	= $_SESSION['password'];
$uids		= explode('|', $_REQUEST['uids']);
$utf7_mailbox	= mb_convert_encoding($_REQUEST['mailbox'], 'UTF7-IMAP', 'UTF-8');

$server = new \Fetch\Server($_SESSION['host'], $_SESSION['port']);
$server->setAuthentication($email, $password);
$server->setMailBox($utf7_mailbox);

echo imap_setflag_full($server->getImapStream(), implode(',', $uids), "\\Seen", ST_UID);
