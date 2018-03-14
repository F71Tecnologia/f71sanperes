<?php

$image = imagecreate(120, 100);

$white       = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);

$color[]     = imagecolorallocate($image, 0x99, 0xCC, 0x33);
$darkcolor[] = imagecolorallocate($image, 0x11, 0x11, 0x11);
$color[]     = imagecolorallocate($image, 0xCC, 0x33, 0x00);
$darkcolor[] = imagecolorallocate($image, 0x11, 0x11, 0x11);
$color[]     = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
$darkcolor[] = imagecolorallocate($image, 0x90, 0x90, 0x90);
$color[]     = imagecolorallocate($image, 0x00, 0x00, 0x80);
$darkcolor[] = imagecolorallocate($image, 0x00, 0x00, 0x50);
$color[]     = imagecolorallocate($image, 0xFF, 0x00, 0x00);
$darkcolor[] = imagecolorallocate($image, 0x90, 0x00, 0x00);
$color[]     = imagecolorallocate($image, 0x00, 0xFF, 0x00);
$darkcolor[] = imagecolorallocate($image, 0x00, 0x90, 0x00);
$color[]     = imagecolorallocate($image, 0xFF, 0xFF, 0x00);
$darkcolor[] = imagecolorallocate($image, 0x90, 0x90, 0x00);
$color[]     = imagecolorallocate($image, 0xFF, 0x00, 0xFF);
$darkcolor[] = imagecolorallocate($image, 0x90, 0x00, 0x90);

$grafico = $_GET['valores']; //defina aqui os valores do gráfico.

if (isset($_GET['id'])) $grafico = $_GET['id'];

$resuls = split('-', $grafico);

$soma = 0;

foreach($resuls as $indice => $resul) $soma += $resul;

foreach($resuls as $indice => $resul) {

 $angles[$indice] = ($resul * 360) / $soma;

}

for ($i = 57; $i > 50; $i--) {

 $inicio = 0;

 foreach($angles as $indice => $angle) {

 imagefilledarc($image, 60, $i, 100, 50, $inicio, ($inicio += $angle), $darkcolor[$indice], IMG_ARC_PIE);

 }

}

 $inicio = 0;

 foreach($angles as $indice => $angle) {

 imagefilledarc($image, 60, 50, 100, 50, $inicio, ($inicio += $angle), $color[$indice], IMG_ARC_PIE);

 }

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?> 