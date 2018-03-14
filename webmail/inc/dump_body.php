<?php

include 'config.php';
include 'constants.php';
include 'functions.php';
include 'imap_connection.php';
include '../vendor/autoload.php';

$mailbox   = $_REQUEST['mailbox'];
$email_uid = (int) $_REQUEST['email_uid'];

// set internal encoding to utf-8
mb_internal_encoding('utf-8');

// marca o email como lido
imap_setflag_full($imap_stream, $email_uid, "\\Seen");

//-------------------------- BUILD MESSAGE VIEW --------------------------------

$server = new \Fetch\Server($_SESSION['host'], $_SESSION['port']);
$server->setAuthentication($_SESSION['email'], $_SESSION['password']);

//$utf7_mailbox = imap_utf7_encode($mailbox);
$utf7_mailbox = mb_convert_encoding($mailbox, 'UTF7-IMAP', 'UTF-8');

$server->setMailBox($utf7_mailbox);

$message = new \Fetch\Message($email_uid, $server);
$headers = $message->getHeaders();

$fetch_subject = sanitize_subject($message->getSubject());
$fetch_subject = $fetch_subject? $fetch_subject: $message->getSubject();
$fetch_body    = fix_encoding_body($message->getMessageBody(true));

if(trim($fetch_body)=="")
{
    $fetch_header  = imap_fetchheader($imap_stream, $email_uid, FT_UID);
    $fetch_body = fix_encoding_body(imap_body($imap_stream, $email_uid, FT_UID));
    if(strpos($fetch_header, "Content-Type: text/plain;") !== FALSE)
    {
	$fetch_body = str_replace(chr(13), "<br>", $fetch_body);
    }
}

// trata os dois possíveis formatos de data que podem ser recebidos
try {
    $mail_date = date("d/m/Y", $message->getDate());
} catch (Exception $e) {
    $mail_date = 'ERROR::DATE';
}

try {
    $mail_time = date("H:i", $message->getDate());
} catch (Exception $e) {
    $mail_time = 'ERROR:TIME';
}


//

$mail_head = array(
    /* Receivers */
    'to'   => call_user_func(function($headers) {
        $receivers = array();
        if (is_array($headers->to)) {
            foreach ($headers->to as $r) {
				$r->personal = imap_utf8($r->personal);

                $receivers[] = str_replace("_", " ", imap_utf8("<span class='name'>{$r->personal}</span> &lt;<a href=\"#\" class=\"mail-to\">{$r->mailbox}@{$r->host}</a>&gt;"));
            }

            return "<b>To: </b> ". implode(', ', $receivers) ."<br />";
        }

        return '';
    }, $headers),

    /* Copy */
    'cc'   => call_user_func(function($headers) {
        $copy = array();
        if (isset($headers->cc)) {
            foreach ($headers->cc as $c) {
				$c->personal = imap_utf8($c->personal);

				$copy[] = str_replace("_", " ", imap_utf8("<span class='name'>{$c->personal}</span> &lt;<a href=\"#\" class=\"mail-cc\">{$c->mailbox}@{$c->host}</a>&gt;"));
            }

            return "<b>Cc: </b> ". implode(', ', $copy) ."<br />";
        }

        return '';
    }, $headers),

    /* Sender */
    'from' => call_user_func(function($headers) {
        $sender = array();
        if ($headers->from) {
            foreach ($headers->from as $s) {
				$s->personal = imap_utf8($s->personal);

                $sender[] = str_replace("_", " ", imap_utf8("<span class='name'>{$s->personal}</span> &lt;<a href=\"#\" class=\"mail-from\">{$s->mailbox}@{$s->host}</a>&gt;"));
            }

            return "<b>From: </b> ". implode(', ', $sender) ."<br />";
        }

        return '';
    }, $headers),
);

//---------------------------- END MESSAGE VIEW --------------------------------




//-------------------------- BUILD ATTACHMENTS AREA ----------------------------
$structure   = $message->getStructure();
$attachments = array();

if (isset($structure->parts) && count($structure->parts)) {
    for($i = 0; $i < count($structure->parts); $i++) {
        $attachments[] = array(
            'is_attachment' => false,
            'filename'      => '',
            'name'          => '',
            'attachment'    => ''
        );

        if ($structure->parts[$i]->ifdparameters) {
            foreach ($structure->parts[$i]->dparameters as $object) {
                if (strtolower($object->attribute) == 'filename') {
                    $attachments[$i]['is_attachment'] = true;
                    $attachments[$i]['filename']      = imap_utf8($object->value);
                }
            }
        }

        if ($structure->parts[$i]->ifparameters) {
            foreach ($structure->parts[$i]->parameters as $object) {
                if (strtolower($object->attribute) == 'name') {
                    $attachments[$i]['is_attachment'] = true;
                    $attachments[$i]['name']          = imap_utf8($object->value);
                }
            }
        }

        if ($attachments[$i]['is_attachment']) {
            $attachments[$i]['attachment'] = imap_fetchbody($imap_stream, $email_uid, $i+1, FT_UID);
            
            if ($structure->parts[$i]->encoding == 3) {

                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
            }
            else if ($structure->parts[$i]->encoding == 4) {
                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
            }
        }
    }
}

$attachments_list = array();

if (count($attachments) != 0) {
    foreach ($attachments as $at) {

	$filename = $at['filename'] ? $at['filename'] : $at['name'];
	$filename_a = imap_mime_header_decode($filename);
	$filename = $filename_a[0]->text;

	if(trim($filename_a[1]->text) !='')
	{
	    if(substr($filename, -1) == '.')
	    {
		$filename = trim($filename).trim($filename_a[1]->text);
	    }
	    else{
		$filename = trim($filename).'.'.trim($filename_a[1]->text);
	    }
	}
	
	$filename = str_replace("?", "", $filename);
	$filename = convert_ascii($filename);
	$filename_view = $filename;
	$filename_link = urlencode($filename);
	$filename_link = str_replace("+", " ", $filename_link);
		
        $filepath = 'attachments'. DS . $filename;
        $upfilepath = 'uploads'. DS . $filename;
        if (!$filename_view)  continue;

        $file_extension = preg_match("/\.([a-z0-9]+)$/i", $filename, $matches) ? strtolower($matches[1]) : '';
	
	//echo "matches = [{$matches[1]}]<br>\n";

        if (file_exists($filepath)) unlink($filepath);
	if (file_exists($upfilepath)) unlink($upfilepath);

        if (($at['is_attachment'] && $filename && !in_array($file_extension, $config_global['attachments']['blacklist'])) ) {
            file_put_contents($filepath, $at['attachment']);
            file_put_contents($upfilepath, $at['attachment']);
        }

        if (file_exists($filepath)) {
            $file_size = formatSizeUnits(filesize($filepath));
            $attachment_icon = $config_global['attachments']['icons']['generic'];

            foreach ($config_global['attachments']['icons'] as $exts => $path) {
                if (in_array($file_extension, explode('|', strtolower($exts)))) {
                    $attachment_icon = $path;
                }
            }
            $filename_crop = mb_strlen($filename_view) >= 20 ? mb_substr($filename_view, 0, 17) . '...' : $filename_view;
            $attachments_list[] = "<li><a target=\"_blank\" href=\"inc/attachments/{$filename_link}\" title=\"{$filename_view} ({$file_size})\"><img src=\"{$attachment_icon}\" /><p>{$filename_crop}</p></a></li>";
        }

    }
}

//---------------------------- END ATTACHMENTS AREA ----------------------------

//---------------------------- BUILD HTML VIEW ---------------------------------
$attachments_area = count($attachments_list) ? implode(' ', $attachments_list) : '';

$html_view = <<<HTML
<div class="email_header">
    <input type="hidden" id="uid" name="uid" value="{$email_uid}" />
	<div class="email_link_back"><a href="javascript:;" onclick="javascript: load_emails();"><< Voltar</a></div>
	<div style="position: relative; float: right;">
		<img class="email_up" src="assets/img/arrow-up-icon.png" width="20" height="20" title="Pr&oacute;ximo email" style="cursor: pointer;" />
		<br />
		<img class="email_down" src="assets/img/arrow-down-icon.png" width="20" height="20" title="Email anterior" style="cursor: pointer;" />
	</div>
    <h1>{$fetch_subject}</h1>
    <span class="time">{$mail_time}</span>
    <span class="date">{$mail_date}</span>
    <span class="email">
        {$mail_head['from']}
        {$mail_head['to']}
        {$mail_head['cc']}
    </span>
    <div class="attachments">
        <ul>
            {$attachments_area}
        </ul>
        <div style="clear: both; border: none;"></div>
    </div>
    <div class="header-separator"></div>
</div>
<div class="email-body">
    {$fetch_body}
</div>
HTML;

header('Content-type: text/html; charset=utf-8');
exit($html_view);

function convert_ascii($string)
{
    // Replace Single Curly Quotes
    $search[] = chr(226).chr(128).chr(152);
    $replace[] = "'";
    $search[] = chr(226).chr(128).chr(153);
    $replace[] = "'";

    // Replace Smart Double Curly Quotes
    $search[] = chr(226).chr(128).chr(156);
    $replace[] = '"';
    $search[] = chr(226).chr(128).chr(157);
    $replace[] = '"';

    // Replace En Dash
    $search[] = chr(226).chr(128).chr(147);
    $replace[] = '--';

    // Replace Em Dash
    $search[] = chr(226).chr(128).chr(148);
    $replace[] = '---';

    // Replace Bullet
    $search[] = chr(226).chr(128).chr(162);
    $replace[] = '*';

    // Replace Middle Dot
    $search[] = chr(194).chr(183);
    $replace[] = '*';

    // Replace Ellipsis with three consecutive dots
    $search[] = chr(226).chr(128).chr(166);
    $replace[] = '...';
    
    $search[] = chr(9);
    $replace[] = '';

    // Apply Replacements
    $string = str_replace($search, $replace, $string);

    // Remove any non-ASCII Characters
    $string = preg_replace("/[^\x01-\x7F]/","", $string);

    return $string;
}
