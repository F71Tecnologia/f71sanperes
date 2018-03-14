<?php

include 'imap_connection.php';
include '../vendor/autoload.php';

$emails    = explode(',', $_REQUEST['maillist']);
$permanent = $_REQUEST['permanent'] == '1' ? true : false;
$mailbox   = $_REQUEST['boxfull'];

$server = new \Fetch\Server($_SESSION['host'], $_SESSION['port']);
$server->setAuthentication($_SESSION['email'], $_SESSION['password']);
$server->setMailBox($mailbox);

if ($permanent) {
    foreach ($emails as $email) {
        $message = new \Fetch\Message($email, $server);
        $message->delete();
    }
}
else {
    foreach ($emails as $email) {
        $message = new \Fetch\Message($email, $server);
        $message->moveToMailBox("INBOX.Trash");
    }
}

$server->expunge();

header('Content-type: application/json');
echo json_encode(array(
    'errormsg' => imap_last_error(),
    'emails'   => $emails,
    'request'  => $_REQUEST
));

