<?php
/*---------------------------------------------------+
| PHP-FOTORESIZE 
| $! index.php
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
				die("Falha ao atuzairar as configuracoes  $config_name");
			}
		} 
	}

	if( isset($_POST['submit']) )
	{
	redirect($fotoresize_root_path . 'resize.' . $phpEx);
	}
}

$gd_library1 = ( $new['gd_library'] == 'gd1' ) ? "selected=\"selected\"" : "";
$gd_library2 = ( $new['gd_library'] == 'gd2' ) ? "selected=\"selected\"" : "";





$template = new Template();
$template->set_filenames(array(
	'index' => $fotoresize_root_path .'templates'. DIRECTORY_SEPARATOR . $board_config['current_template'] . DIRECTORY_SEPARATOR . 'index.tpl')
);


$template->assign_vars(array(
	'L_TITLE' => $lang['title'],
	'L_DIMEN_WIDTH_LABEL' => $lang['imagem_dimen_width_label'],
	'L_DIMEN_WIDTH_LABEL_TEXT' => $lang['imagem_dimen_widht_label_texto'],
	'L_DIMEN_HEIGHT_LABEL' => $lang['imagem_dimen_height_label'],
	'L_DIMEN_HEIGHT_LABEL_TEXT' => $lang['imagem_dimen_height_label_texto'],
	'IMG_WIDTH' => $new['resized_width'],
	'IMG_HEIGHT' => $new['resized_height'],
	'L_GD1' => $lang['gd1'],
	'L_GD2' => $lang['gd2'],
	'GD1' => $gd_library1,
	'GD2' => $gd_library2,
	'L_COMPRESS' => $lang['img_lib_compress'],
	'L_COMPRESS_TEXT' => $lang['img_lib_compress_texto'],
	"L_SUBMIT" => $lang['Submit'],
	'L_FOOTER' => $lang['footer'],
	'L_SETUP_MAIN' => $lang['setup_main'],
	'L_INDEX' => $lang['index'],
	'CURRENT_TEMPLATE' => $board_config['current_template'],
	'FULL_PATH' => $fotoresize_http_path,
	'SETUP_MAIN_PATH' => $fotoresize_root_path . 'setup_main.' . $phpEx,
	'INDEX_PATH' => $fotoresize_root_path . 'index.' . $phpEx,
	'FOTORESIZE_FORM_ACTION' => $fotoresize_http_path . 'index.' . $phpEx
		)
);

$template->pparse('index');







?>