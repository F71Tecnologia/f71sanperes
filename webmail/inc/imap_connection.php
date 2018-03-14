<?php
error_reporting(0);
session_start();

$hostref  = $_SESSION['hostref'];
$hostname = $_SESSION['hostname'];
$email    = $_SESSION['email'];
$password = $_SESSION['password'];

$mailbox = isset($_REQUEST['boxfull']) ? $_REQUEST['boxfull'] : (isset($_SESSION['boxfull']) ? $_SESSION['boxfull']: 'INBOX');

$mailbox = mb_convert_encoding($mailbox, 'UTF7-IMAP', 'UTF-8');

$imap_stream  = imap_open($hostref.$mailbox, $email, $password)
    or die("ERROR: ". imap_last_error());


// pastas padrão
$mailboxes = imap_list($imap_stream, $hostref, '*');
$defaults  = array(
    'INBOX.Trash',
    'INBOX.Drafts',
    'INBOX.Sent',
    'INBOX.Junk'
);

if (is_array($mailboxes)) {
    foreach ($defaults as $box) {
        if (!in_array($box, $mailboxes)) {
	    $box = mb_convert_encoding($box, 'UTF7-IMAP', 'UTF-8');
            imap_createmailbox($imap_stream, $_SESSION['hostref'] . "{$box}");
        }
    }
}
