

include_once 'constants.php';
include_once 'imap_connection.php';
include_once 'functions.php';
include_once 'db_utils.php';

//$_SESSION['favorite'] = 'N';

//echo "favorite = [{$_SESSION['favorite']}]";
//$_SESSION['favorite'] = 'S';

$search_query = $_REQUEST['search_query'];
$box = $_REQUEST['box'];
$boxfull = $_REQUEST['boxfull'];

$sort_query = isset($_REQUEST['sort_query']) ? $_REQUEST['sort_query'] : 'SORTDATE';
$sort_order = isset($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : 'asc';

$final_response = array(
    'order_by' => $sort_query,
    'order' => $sort_order
);

// set internal encoding to utf-8
mb_internal_encoding('utf-8');

// se não for informada a página, assume que é a primeira
if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
    $page = (int) $_REQUEST['page'];
} else {
    $page = 1;
}

$sort_order = strtolower($sort_order) == 'desc' ? 1 : 0;

if ($sort_query && $sort_order) {
    $sort_query = strtoupper($sort_query);

    switch ($sort_query) {
	case 'SORTFROM':
	    $emails = imap_search($imap_stream, 'FROM "' . $search_query . '"', SE_UID);
	    break;

	case 'SORTTO':
	    $emails = imap_search($imap_stream, 'TO "' . $search_query . '"', SE_UID);
	    break;

	case 'SORTSUBJECT':
	    $emails = imap_search($imap_stream, 'SUBJECT "' . $search_query . '"', SE_UID);
	    break;

	case 'SORTDATE':
	    $emails = imap_search($imap_stream, $search_query, SE_UID);
	    rsort($emails);
	//break;
    }

    if ($sort_order) {
	$emails = array_reverse($emails);
    }
} else {
    $emails = imap_search($imap_stream, $search_query, SE_UID);

    if (is_array($emails)) {
	rsort($emails);
    }
}

//

$pagination = '';
$max_page_links = 5;
$total_messages = imap_num_msg($imap_stream);

$favoritos = array();

if ($_SESSION['favorite'] == 'S') {
    $sql = "select id_message from webmail_msg_favorite where id_funcionario = '{$_SESSION['id_user']}' and boxfull = '{$_SESSION['boxfull']}';";
    $res = mysql_query($sql);

    $cont = 0;
    while ($row = mysql_fetch_assoc($res)) {
	$favoritos[$cont] = $row['id_message'];
	$cont++;
    }
    $total_messages_r = $total_messages;
    $total_messages = $cont;
    
} else {
    $favoritos[0] = '0';
}
$total_pages = $total_messages > MAX_EMAILS_FOR_PAGE ?
	ceil($total_messages / MAX_EMAILS_FOR_PAGE) : 0;

if ($total_pages > 1) {
    // botão de "anterior"
    if ($page > 1) {
	$pagination .= '<a style="margin-right:5px;" href="?page=1' . "&box={$box}&boxfull={$boxfull}\">&laquo;&laquo;</a>";
	$pagination .= "<a href=\"?page=" . ($page - 1) . "&box={$box}&boxfull={$boxfull}\">&laquo;</a>";
    } else {
	$pagination .= '<span class="disabled">&laquo;</span>';
    }

    // intervalo das páginas
    $start_at = 1;

    if ($page >= 3)
	$start_at = $page - 2;
    if (($page == $total_pages || $page == $total_pages - 1) && $total_pages > 4)
	$start_at = $total_pages - 4;

    for ($count = $start_at; $count <= $start_at + $max_page_links - 1 && $count <= $total_pages; ++$count) {
	if ($count == $page)
	    $pagination .= '<span class="current">' . $count . '</span>';
	else
	    $pagination .= '<a href="?page=' . $count . "&box={$box}&boxfull={$boxfull}\">{$count}</a>";
    }

    // botão de "próximo"
    if ($page < $total_pages) {
	$pagination .= '<a href="?page=' . ($page + 1) . "&box={$box}&boxfull={$boxfull}\">&raquo;</a>";
	$pagination .= '<a style="margin-left:5px;" href="?page=' . ($total_pages) . "&box={$box}&boxfull={$boxfull}\">&raquo;&raquo;</a>";
    } else {
	$pagination .= '<span class="disabled">&raquo;</span>';
    }
}

$response = array();
$mails_unseen_n = 0;

if ($emails) {

    $i = 0;
    while ($i < $total_messages_r) {
	$uid = $emails[$i];
	if ($_SESSION['favorite'] == 'S') {
	    if (in_array($uid, $favoritos)) {
		$overview = imap_fetch_overview($imap_stream, $uid, FT_UID);

		if (!$overview[0]->seen)
		    $mails_unseen_n++;
	    }
	}else {
	    $overview = imap_fetch_overview($imap_stream, $uid, FT_UID);
	    if (!$overview[0]->seen)
		$mails_unseen_n++;
	}
	$i++;
    }
    
    $c = ($page * MAX_EMAILS_FOR_PAGE) - MAX_EMAILS_FOR_PAGE;
    $i = $c;
    
    //echo "c = [$c]\n";
    //echo "cond = [".($page * MAX_EMAILS_FOR_PAGE)."]\n";
    
    //for ($c = ($page * MAX_EMAILS_FOR_PAGE) - MAX_EMAILS_FOR_PAGE; $c <= $page * MAX_EMAILS_FOR_PAGE; ++$c) {
    while ($c <= $page * MAX_EMAILS_FOR_PAGE){

	$email_uid = $emails[$i];
	if (!$email_uid)
	{
	    $c++;
	    continue;
	}
	if (in_array($email_uid, $favoritos) || $_SESSION['favorite'] != 'S') {
	    $qr_reply = mysql_query("SELECT id FROM webmail_msg_answered WHERE id_message = '{$email_uid}' LIMIT 1");
	    $count_reply = mysql_num_rows($qr_reply);

	    $qr_rw = mysql_query("SELECT id FROM webmail_msg_forwarded WHERE id_message = '{$email_uid}' LIMIT 1");
	    $count_rw = mysql_num_rows($qr_rw);

	    $qr_fav = mysql_query("SELECT id FROM webmail_msg_favorite WHERE id_message = '{$email_uid}' LIMIT 1");
	    $count_fav = mysql_num_rows($qr_fav);

	    $reply = ($count_reply == 1) ? true : false;
	    $forwarded = ($count_rw == 1) ? true : false;
	    $favorite = ($count_fav == 1) ? 'est_02' : 'est_01'; // Imagem com a estrela.


	    $overview = imap_fetch_overview($imap_stream, $email_uid, FT_UID);

	    $subject = sanitize_subject($overview[0]->subject);
	    $subject = $subject ? $subject : ($overview[0]->subject ? imap_utf8($overview[0]->subject) : "(Sem assunto)");

	    $header = imap_fetchheader($imap_stream, $email_uid, FT_UID);

	    $pos = strripos($header, "X-Priority:");

	    //echo "pos = [$pos]<br>\n";

	    if ($pos) {
		$priority = trim(substr($header, $pos + 11, 3));

		//echo "priority = [$priority]<br>\n";

		if ($priority == 1) {
		    $priority = "Urgente";
		} elseif ($priority == 3) {
		    $priority = "Importante";
		} else {
		    $priority = "Normal";
		}
	    } else {
		$priority = "Normal";
	    }

	    $a_response = array(
		'li_class' => $overview[0]->seen ? 'read' : 'unread',
		'uid' => $email_uid,
		'number' => imap_msgno($imap_stream, $email_uid),
		'has_att' => has_attachment($imap_stream, $email_uid),
		'has_reply' => $reply,
		'has_rw' => $forwarded,
		'has_fav' => $favorite,
		'from' => $overview[0]->from ? htmlspecialchars(imap_utf8($overview[0]->from)) : "(Sem remetente)",
		'to' => $overview[0]->to ? imap_utf8($overview[0]->to) : "(Sem destinat?rio)",
		'subject' => $subject,
		'priority' => $priority
	    );

	    //var_dump($a_response);
	    //exit();

	    try {
		$mail_date = format_imap_date($overview[0]->date);
		$a_response['date'] = $mail_date;
	    } catch (Exception $e) {
		$a_response['date'] = $overview[0]->date;
	    }

	    $response[] = $a_response;
	    $c++;
	}
	$i++;
    }
}

// calcula espaço usado pelo usuario
$percent_usage = '';
$quota_info = '';
$quota = imap_get_quotaroot($imap_stream, "INBOX");
if (is_array($quota)) {
    $storage = $quota['STORAGE'];

    $b_usage = $storage['usage'] * 1024;
    $b_limit = $storage['limit'] * 1024;

    if ($b_limit != 0) {
	$percent_usage = $b_usage / ($b_limit / 100);
	$percent_usage = sprintf("%02.2f", $percent_usage);
	$quota_info = formatSizeUnits($b_usage, false) . ' de ' . formatSizeUnits($b_limit, false);
    } else {
	$quota_info = formatSizeUnits($b_usage, false) . ' de ' . formatSizeUnits($b_limit, false);
    }
}

imap_close($imap_stream);

$final_response['emails'] = $response;
$final_response['pagination'] = $pagination;
$final_response['percent_usage'] = $percent_usage;
$final_response['quota_info'] = $quota_info;
$final_response['mails_unseen_n'] = $mails_unseen_n;
$final_response['mails_n'] = $total_messages;

header('Content-type: application/json');
exit(json_encode($final_response));
