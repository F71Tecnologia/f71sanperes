<?php
session_start();

//include '../vendor/autoload.php';

$old_name = isset($_POST['old_name']) ? $_POST['old_name'] : NULL;
$rename = isset($_POST['rename']) ? $_POST['rename'] : NULL;
$parent = explode('.',$old_name);
array_pop($parent);
$parent[] = $rename;
$parent = implode('.', $parent);

include 'imap_connection.php';

//if (imap_renamemailbox($imap_stream , imap_utf7_encode("{$hostref}$old_name"), imap_utf7_encode("{$hostref}$parent"))) {
if (imap_renamemailbox($imap_stream , mb_convert_encoding("{$hostref}$old_name", 'UTF7-IMAP', 'UTF-8'), mb_convert_encoding("{$hostref}$parent", 'UTF7-IMAP', 'UTF-8')))
{
    exit('pasta renomeada');
}else
{
    echo 'Erro em renomear pasta'."\n";
    echo $old_name."\n";
    echo "INBOX.$rename"."\n";
}
echo imap_last_error();
exit();

