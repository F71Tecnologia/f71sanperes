<?php
/*---------------------------------------------------+
| PHP-FOTORESIZE 
| $! resize.php
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


if( isset($_POST['submit']) )
	{

	 $i = 0;
    $msg = array( );
    $arquivos = array( array( ) );
    foreach(  $_FILES as $key=>$info ) {
        foreach( $info as $key=>$dados ) {
            for( $i = 0; $i < sizeof( $dados ); $i++ ) {
                $arquivos[$i][$key] = $info[$key][$i];
            }
        }
    }

    $i = 1;
	
     foreach( $arquivos as $file ) {
       
	
		   if( $file['name'] !='' ) {
	    	
	       if (is_uploaded_file($file['tmp_name'])) {
		
			$photo_types = array(".gif",".jpg",".jpeg",".png");
			$photo_name = strtolower(substr($file['name'], 0, strrpos($file['name'], ".")));
			$photo_ext = strtolower(strrchr($file['name'],"."));
			$photo_dest = RESIZED;
			  if($file['size'] > $board_config['photo_max_b']){
					echo('escolha uma imagem menor que' . parsebytesize($board_config['photo_max_b']) );
				} elseif (!in_array($photo_ext, $photo_types)) {
							echo($lang['photo_ext_error'] . ': ');
							for($i = 0; $i < count($photo_types); $i++)
		    						{
		    							if(count($photo_types[$i]))
											{
											echo($photo_types[$i] . ",\n\t");
													}
								}
		
		} else {
			$photo_file = image_exists($photo_dest, $photo_name.$photo_ext);
			if(!move_uploaded_file($file['tmp_name'], $photo_dest.$photo_file))
			{
		  echo ("<script>alert('".$lang['resize_no_move_upload'] .  $i . "');location='".$fotoresize_http_path . 'resize.' . $phpEx)."';</script>";
			}
			//move_uploaded_file($file['tmp_name'], $photo_dest.$photo_file);
			chmod($photo_dest.$photo_file, 0644);
			$imagefile = @getimagesize($photo_dest.$photo_file);
				$photo_r = image_exists($photo_dest, $photo_name."@_r".$photo_ext);
				createthumbnail($imagefile[2], $photo_dest.$photo_file, $photo_dest.$photo_r, $board_config['resized_width'], $board_config['resized_height']);
					
		}
	}

        }   
        else
		{
		echo ("<script>alert('".$lang['resize_no_source']. $i."');</script>");
		}
	
		if(file_exists($photo_dest.$photo_file) && file_exists($photo_dest.$photo_r))
				{
			echo ("<script>alert('". $lang['resize_ok'] . $i."');location='".$fotoresize_http_path . 'resize.' . $phpEx ."';</script>");					
				}
				
	
			       	        
        $i++;
        	
    }
	
	}
else
	{
	$template->set_filenames(array(
	'resize' => $fotoresize_root_path .'templates'. DIRECTORY_SEPARATOR . $board_config['current_template'] . DIRECTORY_SEPARATOR . 'resize.tpl')
);
	$template->assign_vars(array(
	'L_TITLE' => $lang['title'],
	'L_HEADER_TEXT_LEFT' => $lang['resize_header_text_left'],
	'L_IMG_INPUT' => $lang['imagem_input'],
	'L_IMG_INPUT_TEXT' => $lang['imagem_input_texto'],
	"L_SUBMIT" => $lang['Submit'],
	'L_FOOTER' => $lang['footer'],
	'L_INDEX' => $lang['index'],
	'L_REF' => $lang['ref'],
	'L_SETUP_MAIN' => $lang['setup_main'],
	'L_IMG01' => $lang['img01'],
	'L_IMG02' => $lang['img02'],
	'L_IMG03' => $lang['img03'],
	'L_IMG04' => $lang['img04'],
	'CURRENT_TEMPLATE' => $board_config['current_template'],
	'FULL_PATH' => $fotoresize_http_path,
	'CURRENT_TEMPLATE' => $board_config['current_template'],
	'L_UPLOAD_PROCESS' => $lang['upload_process'],
	'L_IMG_ALERT_FILESIZE' => sprintf($lang['max_filesize'], parsebytesize($board_config['photo_max_b']),'',''),
	'REF_PATH' => $fotoresize_http_path . 'create_ref.' . $phpEx,
	'SETUP_MAIN_PATH' => $fotoresize_root_path . 'setup_main.' . $phpEx,
	'INDEX_PATH' => $fotoresize_root_path . 'index.' . $phpEx,
	'FOTORESIZE_FORM_ACTION' => $fotoresize_http_path . 'resize.' . $phpEx
		)
);

$template->pparse('resize');
	
	}
	


?>