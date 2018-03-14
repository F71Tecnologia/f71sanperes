<?php
session_start();

include '../vendor/autoload.php';

if (!isset($_REQUEST['target']) || !isset($_REQUEST['uid']) || !isset($_REQUEST['mailbox'])) {
    exit;
}

$uids    = explode('|', $_REQUEST['uid']);
$target  = mb_convert_encoding($_REQUEST['target'], 'UTF7-IMAP', 'UTF-8');
$mailbox = mb_convert_encoding($_REQUEST['mailbox'], 'UTF7-IMAP', 'UTF-8');

$server = new \Fetch\Server($_SESSION['host'], $_SESSION['port']);
$server->setAuthentication($_SESSION['email'], $_SESSION['password']);
$server->setMailBox($mailbox);

foreach ($uids as $uid) {
    $message = new \Fetch\Message($uid, $server);
    $message->moveToMailBox($target);
}
