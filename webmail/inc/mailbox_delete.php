<?php
include 'imap_connection.php';

/**
 * Deleta pasta e subpastas.
 */

$folder = mb_convert_encoding($_REQUEST['boxfull'], 'UTF7-IMAP', 'UTF-8');

if ($folder) {
    $total_messages = imap_num_msg($imap_stream);
    
    //echo "total = [$total_messages]\n";
    //exit();
    
    if($total_messages == 0)
    {
	$mb_list = imap_list($imap_stream, $_SESSION['hostref'], "{$folder}.*");

	if (is_array($mb_list)) {
	    foreach ($mb_list as $mb) {
		switch ($folder) {
		    // não deleta as pastas padrões do email
		    case 'INBOX':
		    case 'INBOX.Inbox':
		    case 'INBOX.Trash':
		    case 'INBOX.Sent':
		    case 'INBOX.Junk':
			break;

		    default:
			@imap_deletemailbox($imap_stream, mb_convert_encoding($mb, 'UTF7-IMAP', 'UTF-8'));
			//@imap_deletemailbox($imap_stream, $mb);
		}
	    }
	}

	switch ($folder) {
	    // não deleta as pastas padrões do email
	    case 'INBOX':
	    case 'INBOX.Inbox':
	    case 'INBOX.Trash':
	    case 'INBOX.Sent':
	    case 'INBOX.Junk':
		break;

	    default:
		@imap_deletemailbox($imap_stream, $_SESSION['hostref'].$folder);
	}
	$data = array('resp' => 'S');
    }else
    {
	$data = array('resp' => 'N');
    }
    echo json_encode($data);    
}
