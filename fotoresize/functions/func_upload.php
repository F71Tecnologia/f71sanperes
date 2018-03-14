<?php

/*---------------------------------------------------+
| PHP-FOTORESIZE
| $! func_upload.php
+----------------------------------------------------+
| Copyright © 2008 - Luís Fred
| [url][/url]
+----------------------------------------------------+
| Released under the terms & conditions of v2 of the
| GNU General Public License. For details refer to
| the included gpl.txt file or visit http://gnu.org
+----------------------------------------------------*/

/**
 * Função extraida de parte do código-fonte do php-fusion série 6
 * e otimizado para uso no PHP_PHOTORESIZE
*/

function createthumbnail($filetype, $origfile, $thumbfile, $new_w, $new_h) {
	
	global $board_config;
	if ($filetype == 1) { $origimage = imagecreatefromgif($origfile); }
	elseif ($filetype == 2) { $origimage = imagecreatefromjpeg($origfile); }
	elseif ($filetype == 3) { $origimage = imagecreatefrompng($origfile); }
	
	$old_x = imagesx($origimage);
	$old_y = imagesy($origimage);
	
	if ($old_x > $new_w || $old_y > $new_h) {
		if ($old_x < $old_y) {
			$thumb_w = round(($old_x * $new_h) / $old_y);
			$thumb_h = $new_h;
		} elseif ($old_x > $old_y) {
			$thumb_w = $new_w;
			$thumb_h = round(($old_y * $new_w) / $old_x);
		} else {
			$thumb_w = $new_w;
			$thumb_h = $new_h;
		}
	} else {
		$thumb_w = $old_x;
		$thumb_h = $old_y;
	}
	
	if ($board_config['gd_library']  == "gd1") {
		$thumbimage = imagecreate($thumb_w,$thumb_h);
		$result = imagecopyresized($thumbimage, $origimage, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
	} else {
		$thumbimage = imagecreatetruecolor($thumb_w,$thumb_h);
		$result = imagecopyresampled($thumbimage, $origimage, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
	}
	
	touch($thumbfile);

	if ($filetype == 1) { imagegif($thumbimage, $thumbfile); }
	elseif ($filetype == 2) { imagejpeg($thumbimage, $thumbfile); }
	elseif ($filetype == 3) { imagepng($thumbimage, $thumbfile); }
}

function image_exists($dir, $image) {
	$i = 1;
	$image_name = substr($image, 0, strrpos($image, "."));
	$image_ext = strrchr($image,".");
	while (file_exists($dir.$image)) {
		$image = $image_name."_".$i.$image_ext;
		$i++;
	}
	return $image;
}

?>