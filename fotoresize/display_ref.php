<?php
/*---------------------------------------------------+
| PHP-FOTORESIZE 
| $! display_ref.php
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

include ($fotoresize_root_path . "common." . $phpEx);



$handle = @fopen("info_reflect.txt", "r");
if ($handle) {
    while (!feof($handle)) {
        $buffer = fgets($handle, 4096);
        list($photofile1, $photofile2, $photofile3, $photofile4) = explode('-', $buffer);
        $photofile1_path = substr(strrchr($photofile1, '|'), 1);
        $photofile1_name = strtolower(substr($photofile1, 0, strrpos($photofile1, "|")));
        $photofile2_path = substr(strrchr($photofile2, '|'), 1);
        $photofile2_name = strtolower(substr($photofile2, 0, strrpos($photofile2, "|")));
        $photofile3_path = substr(strrchr($photofile3, '|'), 1);
        $photofile3_name = strtolower(substr($photofile3, 0, strrpos($photofile3, "|")));
        $photofile4_path = substr(strrchr($photofile4, '|'), 1);
        $photofile4_name = strtolower(substr($photofile4, 0, strrpos($photofile4, "|")));
      }
    fclose($handle);
}

if(($photofile1_path == '' && $photofile1_name == ''))
{
	$photofile1_path = $fotoresize_root_path .'templates/'. $board_config['current_template'] .'/images/no_image.gif';
	$photofile1_name = 'sem foto';
}	
if(($photofile2_path == '' && $photofile2_name == ''))
{
	$photofile2_path = $fotoresize_root_path .'templates/'. $board_config['current_template'] .'/images/no_image.gif';
	$photofile2_name = 'sem foto';
}
if(($photofile3_path == '' && $photofile3_name == ''))
{
	$photofile3_path = $fotoresize_root_path .'templates/'. $board_config['current_template'] .'/images/no_image.gif';
	$photofile3_name = 'sem foto';
}
if(($photofile4_path == '' && $photofile4_name == ''))
{
	$photofile4_path = $fotoresize_root_path .'templates/'. $board_config['current_template'] .'/images/no_image.gif';
	$photofile4_name = 'sem foto';
}

$template->set_filenames(array(
	'display_reflect' => $fotoresize_root_path .'templates'. DIRECTORY_SEPARATOR . $board_config['current_template'] . DIRECTORY_SEPARATOR .'display_reflect.tpl')
);
	$template->assign_vars(array(
	'L_TITLE' => $lang['title'],
	'TEMPLATE_PATH' => $fotoresize_http_path . 'templates/' .   $board_config['current_template'],
	'SCRIPT_PATH' => $fotoresize_http_path,
	'PHOTO1_PATH' => $photofile1_path,
	'PHOTO1_NAME' => $photofile1_name,
	'PHOTO2_PATH' => $photofile2_path,
	'PHOTO2_NAME' => $photofile2_name,
	'PHOTO3_PATH' => $photofile3_path,
	'PHOTO3_NAME' => $photofile3_name,
	'PHOTO4_PATH' => $photofile4_path,
	'PHOTO4_NAME' => $photofile4_name,
	'CURRENT_TEMPLATE' => $board_config['current_template'],
	'H_LEFT_TEXT' => $lang['display_Painel_ref_leftText'],
	'FULL_PATH' => $fotoresize_http_path,
	'INDEX_PATH' => $fotoresize_http_path . 'index.' . $phpEx,
	'L_INDEX' => $lang['index'],
	'L_REF' => $lang['ref'],
	'L_IMG_LOADING' => $lang['img_loading'],
	'REF_PATH' => $fotoresize_http_path . 'create_ref.' . $phpEx,
	'L_FOOTER' => $lang['footer'] 
		)
);

$template->pparse('display_reflect');

?>