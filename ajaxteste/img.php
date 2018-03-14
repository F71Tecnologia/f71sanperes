<?php

$im = imagecreate(100, 100);

$string = 'Intranet';

$bg = imagecolorallocate($im, 255, 255, 200);
$black = imagecolorallocate($im, 0, 0, 0);

// prints a black "P" in the top left corner
imagechar($im, 99, 50, 50, $string, $black);

header('Content-type: image/png');
imagepng($im);

?>