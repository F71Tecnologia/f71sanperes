<?php
error_reporting(E_ALL);
echo "exec..\r\n";
$fp = fopen("ilog.txt", "a");
fwrite($fp, "Teste"."\r\n");
fclose($fp);
?>
