<?php
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?
class Imagem{

function LoadJpeg($imgname) {
	$im = @imagecreatefromjpeg($imgname); /* Abrindo imagem */
	if (!$im) { /* Se correto então gera imagem */
		$im = imagecreate(843,298); /* Criando nova imagem*/
		$bgc = imagecolorallocate($im, 255, 255, 255);
		$tc = imagecolorallocate($im, 0, 0, 0);
		imagefilledrectangle($im, 0, 0, 843, 298, $bgc);
		/* Erro na geração da imagem */
		imagestring($im, 1, 5, 5, "Erro na geração do boleto.", $tc);
	}
return $im;
}

function geraImagem($id){
	//imagejpeg($this -> LoadJpeg("c:\img\imagem.".$id.".jpg"));
imagejpeg($this -> LoadJpeg("C:\Users\Cleber\Pictures\ispv6".".jpg"));
}
}

$imagem = new Imagem();
$imagem -> geraImagem($_GET['id']);

?>

<form id="form1" name="form1" enctype="multipart/form-data" method="post" action="">
  <label>
    <input type="file" name="id" id="id" />
  </label>
  <label>
    <input type="submit" name="button" id="button" value="Submit" />
  </label>
</form>
</body>
</html>