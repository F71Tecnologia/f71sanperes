<?php
function logVar($lin)
{
	$log = fopen('/home/ispv/public_html/intranet/webmail_dev/log/php.log', 'a');
	$tmp = $lin . "\n";
	fwrite($log, $tmp);
	fclose($log);
}

function logDump($lin)
{
	ob_start();
	var_dump($lin);
	$result = ob_get_clean();
	
	$log = fopen('/home/ispv/public_html/intranet/webmail_dev/log/php.log', 'a');
	$tmp = "\n" . $result . "\n";
	fwrite($log, $tmp);
	fclose($log);
}
?>