<?php
session_start();

//include '../vendor/autoload.php';

$parent = isset($_POST['parent']) ? $_POST['parent'] : NULL;
$fullname = isset($_POST['name']) ? $parent.'.'.$_POST['name'] : NULL;

include 'imap_connection.php';

 
if (@imap_createmailbox($imap_stream , imap_utf7_encode("{$hostref}$fullname"))) {
    exit('pasta criada');
}else{
    echo 'Erro em criar pasta'."\n";
}

var_dump($_POST); exit();

