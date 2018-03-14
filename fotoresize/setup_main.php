<?php
/*---------------------------------------------------+
| PHP-FOTORESIZE 
| $! setup_maim.php v1.0
+----------------------------------------------------+
| Copyright © 2008 - Luís Fred
| [url][/url]
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/

define('IN_PHPPHOTORESIZE', true);
$fotoresize_root_path = (defined('FOTORESIZE_ROOT_PATH')) ? FOTORESIZE_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($fotoresize_root_path . 'common.' . $phpEx);


//
// Pull all config data
//
$sql = "SELECT *
	FROM " . CONFIG_TABLE;
if(!$result = $db->sql_query($sql))
{
die("Could not query config information");
}
else
{
	while( $row = $db->sql_fetchrow($result) )
	{
		$config_name = $row['config_name'];
		$config_value = $row['config_value'];
		$default_config[$config_name] = isset($_POST['submit']) ? str_replace("'", "\'", $config_value) : $config_value;
		
		$new[$config_name] = ( isset($_POST[$config_name]) ) ? $_POST[$config_name] : $default_config[$config_name];
		
		if( isset($_POST['submit']) )
		{
			$sql = "UPDATE " . CONFIG_TABLE . " SET
				config_value = '" . str_replace("\'", "''", $new[$config_name]) . "'
				WHERE config_name = '$config_name'";
			if( !$db->sql_query($sql) )
			{
				die("Failed to update general configuration for $config_name");
			}
		} 
	}

	if( isset($_POST['submit']) )
	{
	Header('Location:' . redirect($fotoresize_root_path . 'index.' . $phpEx) );
		
	}
}

$current_template = ( $new['current_template'] == 'default' ) ? "selected=\"selected\"" : "";
$current_template = ( $new['current_template'] == 'black' ) ? "selected=\"selected\"" : "";





$template->set_filenames(array(
	'setup_main' => $fotoresize_root_path .'templates'. DIRECTORY_SEPARATOR . $board_config['current_template'] . DIRECTORY_SEPARATOR . 'setup_main.tpl')
);


$template->assign_vars(array(
	'L_TITLE' => $lang['title'],
	'L_HEADER_TEXT_LEFT' => $lang['setup_main_header_text_left'],
	'DEFAULT' => $current_template,
	'BLACK' => $current_template,
	'L_FOOTER' => $lang['footer'],
	'FOTORESIZER_SCRIPT_PATH' => $fotoresize_http_path,
	'CURRENT_TEMPLATE' => $board_config['current_template'],
	'L_SUBMIT' => $lang['Submit'],
	'L_SUBMIT_CONF' => $lang['Submit_conf'],
	'SCRIPT_PATH' => $new['script_path'],
	'SERVER_NAME' => $new['server_name'],
	'L_SERVER_NAME' => $lang['server_name'],
	'L_SERVER_NAME_EXPLAIN' => $lang['server_name_explain'],
	'L_SCRIPT_PATH' => $lang['script_path'],
	'L_SCRIPT_PATH_EXPLAIN' => $lang['script_path_explain'],
	'L_SKIN' => $lang['skin'],
	'L_SKIN_EXPLAIN' => $lang['skin_explain'],
	'L_INDEX' => $lang['index'],
	'FULL_PATH' => $fotoresize_http_path,
	'SERVER_PATH' => $fotoresize_http_path, 
	'INDEX_PATH' => $fotoresize_root_path . 'index.' . $phpEx,
	'FOTORESIZE_FORM_ACTION' => $fotoresize_root_path . 'setup_main.' . $phpEx
		)
);

$template->pparse('setup_main');







?>