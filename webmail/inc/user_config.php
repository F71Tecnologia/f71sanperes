<?php

$pageResponse = array();

//echo "gravar<br>\n";
//var_dump($_REQUEST['config']);
//var_dump($_REQUEST['email']);

$objConfig = array(
	'config' => ((isset($_REQUEST['config']))?  $_REQUEST['config']: null),
	'email' => ((isset($_REQUEST['email']))?  $_REQUEST['email']: null),
);

//var_dump($objConfig);

if (!is_null($objConfig['email']))	$userConfigFullFileName = getcwd().'/user_config'.'/'.preg_replace('/\W/', '_', $objConfig['email']).'_config.json';



if (!isset($userConfigFullFileName))
	exit;

if (!is_null($objConfig['config'])) {

	$FH = fopen($userConfigFullFileName, 'w');

} else {
	exit;
}

(isset($FH))?
	fwrite($FH, $objConfig['config']):	exit;

header('Content-type: application/json');
exit(json_encode($objConfig['config']));
