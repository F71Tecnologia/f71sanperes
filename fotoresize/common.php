<?php
/*---------------------------------------------------+
| PHP-PHOTORESIZE 
| $! common.php
+----------------------------------------------------+
| Copyright © 2008 - Luís Fred
| [url][/url]
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/
if ( !defined('IN_PHPPHOTORESIZE') )
{
	die("Inacessível");
}

$fotoresize_root_path = (defined('FOTORESIZE_ROOT_PATH')) ? FOTORESIZE_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($fotoresize_root_path . 'config.' . $phpEx);

if( !defined("PHPPHOTORESIZE_INSTALLED") )
{
	header('Location: ' . $fotoresize_root_path . 'install/index.' . $phpEx);
	exit;
}

include($fotoresize_root_path . 'includes/constants.' . $phpEx);
include($fotoresize_root_path . 'includes/db.' . $phpEx);
include($fotoresize_root_path . 'includes/template.' . $phpEx);
include($fotoresize_root_path . 'functions/func_main.' . $phpEx);
include($fotoresize_root_path . 'functions/func_upload.' . $phpEx);

$template = new Template();



//
error_reporting  (E_ERROR | E_WARNING | E_PARSE); // This will NOT report uninitialized variables
set_magic_quotes_runtime(0); // Disable magic_quotes_runtime

$board_config = array();

$sql = "SELECT *
	FROM " . CONFIG_TABLE;
if( !($result = $db->sql_query($sql)) )
{
	die("Erro ao manipular dados de configuracao");
}

while ( $row = $db->sql_fetchrow($result) )
{
	$board_config[$row['config_name']] = $row['config_value'];
}

include($fotoresize_root_path . 'language/lang_' . $board_config['default_lang'] . '/lang.' . $phpEx);
	 
$server_protocol = 'http://';
$server_name = preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['server_name']));
$script_name = preg_replace('#^\/?(.*?)\/?$#', '\1', trim($board_config['script_path']));
$script_name = ($script_name == '') ? $script_name : '/' . $script_name;	

$fotoresize_http_path = $server_protocol . $server_name . $script_name . '/';

if(defined('PHPPHOTORESIZE_INSTALLED'))
{
if(file_exists($fotoresize_root_path . 'install/index.' . $phpEx))
{	

	message_die(GENERAL_MESSAGE, "Por favor, remova o diretorio install/ para poder prosseguir", "Alerta");
}
	
}
?>