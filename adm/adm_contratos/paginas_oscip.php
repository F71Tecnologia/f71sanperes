<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');


$id_oscip=$_GET['id'];
$n_pagina=$_GET['pg'];
$tipo=$_GET['tp'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Untitled Document</title>
<style>

a{
position:relative;
}
</style>

<style media="print">
#botoes { 
display:none;
visibility:hidden;
}

</style>
</head>

<body>

<?php 


$qr_cont_anexos=mysql_query("SELECT * FROM obrigacoes_oscip_anexos WHERE id_oscip='$id_oscip' AND tipo_anexo='$tipo' AND status=1 ") or die('Erro');
$total_anexo=mysql_num_rows($qr_cont_anexos);


$qr_anexos=mysql_query("SELECT * FROM obrigacoes_oscip_anexos  WHERE id_oscip='$id_oscip' AND anexo_ordem='$n_pagina' AND tipo_anexo='$tipo' AND status='1' ");



while($row_anexo=mysql_fetch_assoc($qr_anexos)):


?>

<div id="botoes" style="text-align:center;margin-left:900px;">  
<span  style="width:auto;heigth:auto; text-decoration:none; text-align:center;position:relative;float:left;">

<?php

if($n_pagina!=0){
							if($n_pagina!=1 and $n_pagina!=0){
							echo '<a href="'. $_SERVER['PHP_SELF'].'?id='.$id_oscip.'&pg='.($n_pagina-1).'&tp='.$tipo.'"><img src="../../imagens/anterior.png"><br> Voltar</a> ';
							}
							
							?>
							
							</span>
							<span  style="width:auto;heigth:auto;text-decoration:none; text-align:center;position:relative;float:left;">
							<?php
						if($n_pagina!=$total_anexo ){
								
							echo ' <a  href="'. $_SERVER['PHP_SELF'].'?id='.$id_oscip.'&pg='.($n_pagina+1).'&tp='.$tipo.'"><img src="../../imagens/proximo.png"><br> Avançar</a>';
							}
	}
	

?>

</span>
</div>

<div style="width:auto;heigth:auto; text-align:center;position:relative;clear:left;">
<img  width="672" height="950" src="../../adm/adm_contratos/anexos_oscip/<?php echo $row_anexo['id_anexo'].'.'.$row_anexo['extensao'];  ?>"/>
</div>






<?php


endwhile;
?>
</body>
</html>