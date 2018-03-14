<?php  

include("m2brimagem.class.php");  

$arquivo    = $_GET['foto'];  

$largura    = $_GET['w'];  

$altura     = $_GET['h'];  

$oImg = new m2brimagem($arquivo);  

$valida = $oImg->valida();  

if($valida == 'OK'){  

     $oImg->redimensiona($largura,$altura,'fill');  

     $oImg->grava();  

}else{  

  die($valida);  

}  

exit;  
?> 
