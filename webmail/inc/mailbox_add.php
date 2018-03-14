<?php

include 'imap_connection.php';

$parent = $_REQUEST['parent'];
$folder_name = $_REQUEST['name'];

$parent = $parent ? $parent : 'INBOX';

// Mailboxes precisam ser gravados em UTF-7 de acordo com [RFC2060]
//$new_mailbox = imap_utf7_encode($parent.'.'.$folder_name);
$new_mailbox = mb_convert_encoding($parent.'.'.$folder_name, 'UTF7-IMAP', 'UTF-8');

//echo $_SESSION['hostref'].$new_mailbox . "\n";

if ($parent && $folder_name) {
    $created = imap_createmailbox($imap_stream, $_SESSION['hostref'].$new_mailbox);

    exit(json_encode(array(
        'created' => $created
    )));
}
