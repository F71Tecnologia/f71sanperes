<?php
session_start();

include 'imap_connection.php';
include 'functions.php';
include_once 'db_utils.php';

foreach (array('recipients', 'subject', 'email_body') as $requested_key) {
    if (!isset($_REQUEST[$requested_key]))
        exit(json_encode(array(
            'ERROR' => 'missing: '. $requested_key,
        )));
}

$recipients     = $_REQUEST['recipients'];
$recipients_cc  = $_REQUEST['recipients_cc'];
$recipients_cco = $_REQUEST['recipients_cco'];
$subject        = $_REQUEST['subject'];
$email_body     = $_REQUEST['email_body'];
$attachments    = $_REQUEST['attachments'];
$relevance      = $_REQUEST['relevance'];
$acao		= $_REQUEST['acao'];

$secure_hash = md5(date('r', time()));
$files = array();

// validando destinatários, cópia e cópia oculta
if ($recipients) {
    $recipients = preg_replace('/\s*/', '', $recipients);

    $a_final = array();
    $a_recipients = explode(',', $recipients);

    foreach ($a_recipients as $recipient) {
        if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $a_final[] = $recipient;
        }
    }

    $recipients = implode(',', $a_recipients);
}

if ($recipients_cc) {
    $recipients_cc = preg_replace('/\s*/', '', $recipients_cc);

    $a_final = array();
    $a_recipients_cc = explode(', ', $recipients_cc);

    foreach ($a_recipients_cc as $recipient_cc) {
        if (filter_var($recipient_cc, FILTER_VALIDATE_EMAIL)) {
            $a_final[] = $recipient_cc;
        }
    }

    $recipients_cc = implode(',', $a_recipients_cc);
}

if ($recipients_cco) {
    $recipients_cco = preg_replace('/\s*/', '', $recipients_cco);

    $a_final = array();
    $a_recipients_cco = explode(', ', $recipients_cco);

    foreach ($a_recipients_cco as $recipient_cco) {
        if (filter_var($recipient_cco, FILTER_VALIDATE_EMAIL)) {
            $a_final[] = $recipient_cco;
        }
    }

    $recipients_cco = implode(',', $a_recipients_cco);
}

// preparando anexos
if ($attachments) {
    
    echo "attachments = [{$attachments}]\n\n";
    
    $attachments = $attachments ? explode('|', $attachments) : '';

    $arq = 1;
    foreach ($attachments as $filename) {
        if (!$filename) continue;
	$arq ++;
	
	if($acao!="")
	{
	    //$filename = $arq.preg_replace('/^[a-z]+\//i', '', $filename);
	    $filename = preg_replace('/^[a-z]+\//i', '', $filename);
	}else
	{
	    $filename = preg_replace('/^[a-z]+\//i', '', $filename);
	}
	
	$filepath = "uploads/".$filename;
	
        $file_size = filesize($filepath);
	
        $files[] = implode("\r\n", array(
            "",
            "--PHP-mixed-{$secure_hash}",
            "Content-Type: application/octet-stream; name=\"{$filename}\"",
            "Content-Transfer-Encoding: base64",
            "Content-Disposition: attachment",
            "",
            chunk_split(base64_encode(file_get_contents($filepath))),
            "",
        ));
    }

    $files[] = "\r\n--PHP-mixed-{$secure_hash}--";
}

$files = implode("\r\n", $files);

$date = date('D, d M Y H:i:s');

$priority = getHeaders($relevance);

//echo "data = [{$date}]<br>\n";

$headers = implode("\r\n", array(
    "Subject: {$subject}",
    "From: {$_SESSION['email']}",
    "Reply-To: {$_SESSION['email']}",
    "To: {$recipients}",
    "Cc: {$recipients_cc}",
    "Bcc: {$recipients_cco}",
    "Date: {$date} -0300",
    "Content-type: multipart/mixed; boundary=\"PHP-mixed-".$secure_hash."\"",
    "Content-Transfer-Encoding: 8bit",
    $priority[0],
    $priority[1],
    $priority[2]
));
        
    //1 = High, 3 = Normal, 5 = Low
    function getHeaders($priority)
    {
        $headers = array();
        
        switch ($priority) {
            case 'urgent':
                $headers[0] = "X-Priority: 1 (Highest)"; 
                $headers[1] = "X-MSMail-Priority: High";
                $headers[2] = "Importance: High";
                break;
            case 'important':
                $headers[0] = "X-Priority: 3 (Normal)";
                $headers[1] = "X-MSMail-Priority: Normal"; 
                $headers[2] = "Importance: Normal";
                break;
            case 'normal':
                $headers[0] = "X-Priority: 5 (Low)"; 
                $headers[1] = "X-MSMail-Priority: Low"; 
                $headers[2] = "Importance: Low";
                break;
        }
        return $headers;
    }
        
    // ligando buffer de saída para criação do email
    ob_start();
   ?>

--PHP-mixed-<?php print $secure_hash ?>

Content-Type: multipart/alternative; boundary="PHP-alt-<?php print $secure_hash; ?>"

--PHP-alt-<?php print $secure_hash; ?>

Content-Type: text/html; charset="utf-8"
Content-Transfer-Encoding: 8bit

<?php print $email_body; ?>

--PHP-alt-<?php print $secure_hash; ?>--

<?php print $files; ?>

--PHP-mixed-<?php print $secure_hash ?>

<?php

// pegando conteúdo do buffer atual
$message = stripslashes(ob_get_clean());

//var_dump($headers);
//exit();

$mail_sent = @imap_mail($recipients, $subject, $message, $headers, $recipients_cc, $recipients_cco);

if ($mail_sent) {
    // salva como enviado
    $utf7_sent_mailbox = '.'.imap_utf7_encode('Sent');
    @imap_append($imap_stream, $_SESSION['hostname'].$utf7_sent_mailbox, implode("\r\n", array($headers, $message)), "\\Seen");
    
    
    // Marcando como respondido ou enviado.
    $uid = $_REQUEST['uid'];
    $action = str_replace(':', '', trim(strtolower($_REQUEST['action'])));

    $response = array(
	    'uid'    => $uid,
	    'action' => $action
    );

    if (is_numeric($uid) && isset($action)) {
	    if ($action == 'fwd') {
		    $result = mysql_query("INSERT INTO webmail_msg_forwarded (id_message) VALUES ({$uid})");
	    }
	    else if ($action == 're')  {
		    $result = mysql_query("INSERT INTO webmail_msg_answered (id_message) VALUES ({$uid})");
	    }
    }    
}

// apagando os anexos enviados do servidor
if (is_array($attachments) && count($attachments)) {
    foreach ($attachments as $filepath) {
        unlink($filepath);
    }
}

exit($mail_sent ? 'success' : 'fail');

