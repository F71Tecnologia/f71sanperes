<?php
session_start();

include 'imap_connection.php';

if (isset($_REQUEST['recipients']) && isset($_REQUEST['subject']) && isset($_REQUEST['email_body'])) {
    $recipients     = $_REQUEST['recipients'];
    $recipients_cc  = $_REQUEST['recipients_cc'];
    $recipients_cco = $_REQUEST['recipients_cco'];
    $subject        = $_REQUEST['subject'];
    $email_body     = $_REQUEST['email_body'];
    $attachments    = $_REQUEST['attachments'];
    
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
        $attachments = $attachments ? explode('|', $attachments) : '';
        
        foreach ($attachments as $filename) {
            if (!$filename) continue;
            
            $filepath = $filename;
            $filename = preg_replace('/.*\//i', '', $filename);
            
            $files[] = implode("\r\n", array(
                "",
                "--PHP-mixed-{$secure_hash}",
                "Content-Type: application/zip; name={$filename}",
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

    $headers = implode("\r\n", array(
        "Subject: {$subject}",
        "From: {$_SESSION['email']}",
        "Reply-To: {$_SESSION['email']}",
        "To: {$recipients}",
        "Cc: {$recipients_cc}",
        "Bcc: {$recipients_cco}",
        "Content-type: multipart/mixed; boundary=\"PHP-mixed-".$secure_hash."\""
    ));

    // ligando buffer de saída para criação do email
    ob_start();
    ?>

--PHP-mixed-<?php echo $secure_hash ?>

Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $secure_hash; ?>"

--PHP-alt-<?php echo $secure_hash; ?>

Content-Type: text/html; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

<?php echo $email_body; ?>

--PHP-alt-<?php echo $secure_hash; ?>--

<?php echo $files; ?>

--PHP-mixed-<?php echo $secure_hash ?>

    <?php
    // pegando conteúdo do buffer atual
    $message = stripslashes(ob_get_clean());
    $draft_saved = @imap_append($imap_stream, $_SESSION['hostname'].'.Drafts', implode("\r\n", array($headers, $message)), "\\Seen");
    
    if ($draft_saved) {
        $imap_stream  = imap_open($_SESSION['hostref'].'INBOX.Drafts', $_SESSION['email'], $_SESSION['password'])
            or die('ERROR: '. imap_last_error());
        
        $draft_uid = imap_uid($imap_stream, imap_num_msg($imap_stream));
        
        exit(json_encode(array(
            'draft_uid' => $draft_uid
        )));
    }
}
    
