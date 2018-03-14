<?php
include 'imap_connection.php';


$mb_list = imap_list($imap_stream, $_SESSION['hostref'], 'INBOX.Webmail.*');

if (is_array($mb_list)) {
    echo '<ul>';
    foreach ($mb_list as $mb) {
        echo "<li>{$mb}</li>";
    }
    echo '</ul>';
}
else {
    die('Imap list failed.');
}
