<?php

include 'imap_connection.php';
include '../vendor/autoload.php';

$mailbox   = $_REQUEST['boxfull'];

$server = new \Fetch\Server($_SESSION['host'], $_SESSION['port']);
$server->setAuthentication($_SESSION['email'], $_SESSION['password']);
$server->setMailBox($mailbox);

imap_delete($server->getImapStream(), '1:*');

$server->expunge();
