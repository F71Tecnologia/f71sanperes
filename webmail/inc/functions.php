<?php
include_once 'imap_connection.php';

/**
 * Monta arvore de pastas de email com base em strings descrevendo a estrutura.
 * Ex: $a = mount_mailbox_tree(array('INBOX.Minha.Pasta'));
 *
 *     array(size=1)
 *         'Inbox' => null
 *         'Minha' =>
 *             array(size=1)
 *                 'Pasta' => null
 */
function mount_mailbox_tree($mailboxes) {
	$tree = array();

	function transform(&$at, $path) {
        global $hostref;
        global $email;
        global $password;

        $value = null;

        $conn_string = $hostref;


        if (preg_match('/^INBOX$/i', $path)) {
            $conn_string .= strtoupper($path);
        }
        else {
            $conn_string .= 'INBOX.'.$path;
        }

        // calcula quantidade de mensagens e mensagens lida em cada pasta | Desnecessário fazer lado servidor (nesse ambiente atual de server misto), Tava sendo feito também em [list_mailboxes]
        $status = "";

        $imap_stream = imap_open($conn_string, $email, $password, OP_READONLY)
            or die("ERROR: ". imap_last_error());

        if (!empty($path)) {
         $keys = explode('.', $path);

         while (count($keys) > 0) {
            if (count($keys) === 1) {
               $at[array_shift($keys)] = array(
                'INBOX.'. $path,
                null, //Retirando os contadores de emails não lidos pois estavam deixando a geração o JSON da estrutura de pastas lento => 9s+
                $value
                );
           } else {
               $key = array_shift($keys);

               if (!isset($at[$key])) {
                  $at[$key] = array(
                    'INBOX.'. $path,
                    null, //Retirando os contadores de emails não lidos pois estavam deixando a geração o JSON da estrutura de pastas lento => 9s+
                    array()
                    );
              }

              $at = &$at[$key][2];
          }
      }

      return $at;
  } else {
     return array();
 }
}


foreach ($mailboxes as $box) {
    $encode_type = mb_detect_encoding($box);
    if (preg_match("/UTF\-8/", $encode_type)) {
        $box = utf8_decode($box);
    }

    $box = $box;
    print $box." was $encode_type but now  is -> ".mb_detect_encoding($box). "</br>";

    // executa a transformação dos caminhos de pasta no IMAP
    if (preg_match('/^INBOX$/i', $box)) {
        $box = ucfirst(strtolower($box));
        transform($tree, $box);
    }
    else {
        $box = preg_replace('/^INBOX\./i', '', $box);
        transform($tree, $box);
    }
}

return $tree;
}



function fix_encoding_body($text='') {
	$encoding = mb_detect_encoding($text, mb_detect_order(), true);
	if ($encoding == 'utf-8')
		return $text;

    return iconv($encoding, 'utf-8', $text);
}



function sanitize_subject($subject) {
	$subject_struct = imap_mime_header_decode($subject);
	$fetch_subject  = array();

	for ($i=0; $i < count($subject_struct); ++$i) {
		if ($subject_struct[$i]->charset != 'utf-8')
			$fetch_subject[] = iconv($subject_struct[$i]->charset, 'utf-8', $subject_struct[$i]->text);
		else
			$fetch_subject[] = $subject_struct[$i]->text;
	}

	return implode(' ', $fetch_subject);
}



function formatSizeUnits($bytes, $decimals=true) {

	$decimals = $decimals ? 2 : 0;

    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, $decimals) . 'GB';
    }
    else if ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, $decimals) . 'MB';
    }
    else if ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, $decimals) . 'KB';
    }
    else if ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    }
    else if ($bytes == 1) {
        $bytes = $bytes . ' byte';
    }
    else {
        $bytes = '0 bytes';
    }

    return $bytes;
}



function get_mime_type(&$structure) {
    $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

    if($structure->subtype) {
        return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
    }

    return "TEXT/PLAIN";
}



function has_attachment($stream, $email_uid) {
    $structure = imap_fetchstructure($stream, $email_uid, FT_UID);

    if (isset($structure->parts) && count($structure->parts)) {
        for($i = 0; $i < count($structure->parts); $i++) {
            if ($structure->parts[$i]->ifdparameters) {
                foreach ($structure->parts[$i]->dparameters as $object) {
                    if (strtolower($object->attribute) == 'filename') {
                        return true;
                    }
                }
            }

            if ($structure->parts[$i]->ifparameters) {
                foreach ($structure->parts[$i]->parameters as $object) {
                    if (strtolower($object->attribute) == 'name') {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}

// Dump a log to local_log.txt
// usage dump_log(MIXED, __FILE__.__LINE__, 'string indicativa', 'TIPO DO LOG')
function dump_log($thing, $filenameNline = __FILE__, $message = 'Dump of ', $type = 'WATCH') {
    if (!$thing) return;

    ob_start();
    var_dump($thing);
    $dump = ob_get_clean();
    $when = date('d-m-Y # G:i:s');
    $where = $filenameNline;

    $log = "[$when]  [$where]  [$type]  [$message] =>\n $dump";
    fopen('local_log.txt','a+');
    error_log($log, 3,'local_log.txt');

    return;
}

//Dado um array de chaves casa com as chaves de $_REQUEST e retorna um array associativo.
function parse_request($assoc_arr){
    if(!$assoc_arr) return;
    //array_combine($assoc_arr, $assoc_arr);

    $flag_to_not_destroy = false;
    foreach ($assoc_arr as $requested_key => $default) {
        $assoc_arr[$requested_key] = ((isset($assoc_arr[$requested_key]))?  $default: null); // Setar valor padrão para a chave caso Não seja null.

        foreach ($_REQUEST as $key => $value) {
            if ($key == $requested_key){
                $assoc_arr[$requested_key] = $value;

                $flag_to_not_destroy = true;
            }
        }
    }

    if(!$flag_to_not_destroy) return false;
    return $assoc_arr;
}

function dump($thing) {
    if (!$thing) return;
    print "<pre>";
        var_dump($thing);
    print "</pre>";
}

// baseado na estrutura de imap_fetchstructure testa se o arquivo exite;
function existAttachment($part){
    if (isset($part->parts)){
        foreach ($part->parts as $partOfPart){
            existAttachment($partOfPart);
        }
    }
    else{
        if (isset($part->disposition)){
            if ($part->disposition == 'attachment'){
                var_dump($part->dparameters[0]->value);

                return true;
            }
        }
    }
}
// Pega uma data no formato [Tue, 06 May 2014 12:43:22]
// e devolve num formato padrão [06/05/2014 12:43]
function format_imap_date($str_date, $dateMask = 'd/m/Y H:i') {
    if (!$str_date) return;

    $date = (array) new DateTime($str_date);

    $mail_date = $date['date'];
    $mail_date = new DateTime($mail_date);
    $mail_date = date_format($mail_date, $dateMask);

    return $mail_date;
}