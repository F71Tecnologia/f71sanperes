<?php

session_start();

//var_dump($_REQUEST);
//exit();

if(trim($_REQUEST['param']) !== "")
{
	$param = base64_decode($_REQUEST['param']);
	$par = explode("&",$param);
}

$webmail_host = trim($_REQUEST['webmail_host'])==''?$par[0]:$_REQUEST['webmail_host'];

if(trim($webmail_host)!=="")
{
	$param = base64_decode($_REQUEST['param']);
	$par = explode("&",$param);
	
        
        
    $_SESSION['webmail_host'] = $webmail_host;
    $_SESSION['email'] = $par[1];
    $_SESSION['password'] = urldecode($par[2]);
    $_SESSION['hostref'] = $par[3];
    $_SESSION['hostname'] = $par[4];
    $_SESSION['boxfull'] = $_REQUEST['boxfull'];
    $_SESSION['id_regiao'] = $par[5];
    $_SESSION['id_master'] = $par[6];
    $_SESSION['id_user'] = $par[7];
}



$guess_host = (preg_match("/\@(.+)$/", $_SESSION['email'], $m) )? 'mail.'.$m[0]: '';
$host = $_SESSION['webmail_host'] ? $_SESSION['webmail_host'] : $guess_host;

$host = 'localhost';
$_SESSION['host'] = $host;
$_SESSION['port'] = 143;
//$_SESSION['port'] = 993;

//$_SESSION['hostref']  = '{'. $host .':143/novalidate-cert}';
//$_SESSION['hostname'] = '{'. $host .':143/novalidate-cert}INBOX';

$_SESSION['hostref']  = '{'. $host .':143/novalidate-cert}';
$_SESSION['hostname'] = '{'. $host .':143/novalidate-cert}INBOX';

$_SESSION['email']    =  $_SESSION['email'] ? $_SESSION['email'] : '';

$_SESSION['password'] =  $_SESSION['password'] ? $_SESSION['password'] : '';

$_SESSION['boxfull'] = isset($_REQUEST['boxfull']) ? $_REQUEST['boxfull'] : 'INBOX';

if ($_COOKIE['logado']==258)
{
    var_dump($_SESSION);
    flush();
}

if (!isset($_GET['box']) && !isset($_GET['boxfull'])) {
	header('Location: index.php?box=Inbox&boxfull=INBOX.Inbox');
}

require_once 'inc/content.php';

//ramon@institutolagosrio.com.br //ramon2012