<?php
session_start();
include_once 'db_utils.php';

$uid = $_REQUEST['uid'];
$action = $_REQUEST['action'];

var_dump($_SESSION);

if (is_numeric($uid) && isset($action)) {
    if($action === 'fav')
    {
	$sql = "INSERT INTO webmail_msg_favorite (id_message, id_funcionario, boxfull) VALUES ({$uid}, '{$_SESSION['id_user']}', '{$_SESSION['boxfull']}');";
	$result = mysql_query($sql);
    }else
    {
	$sql = "delete from webmail_msg_favorite where id_message ={$uid};";
	$result = mysql_query($sql);
    }
    //echo "sql = [$sql]\n";
}