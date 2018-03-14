<?php
include "conn.php";

$RE = mysql_query("SELECT id_clt,nome,campo3 FROM rh_clt WHERE id_projeto = '11' AND  campo3 = '10' ORDER BY id_clt");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="net1.css" rel="stylesheet" type="text/css" />
<title>ARQUIVO PARA EXECUÇÃO DE QUERYS RAPIDAS</title>
</head>

<body>
<table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
  <tr class="fundo_azul">
    <td width="8%" height="26">ID</td>
    <td width="66%">NOME</td>
    <td width="17%">CODIGO</td>
    <td width="9%">NOVO COD</td>
  </tr>
  
  <?php
  $cont = "141";
  while($Row = mysql_fetch_array($RE)){
	  
	  /*
	  if(!empty($_REQUEST['vai'])){
		  mysql_query("UPDATE rh_clt SET campo3 = '$cont' WHERE id_clt = '$Row[id_clt]' ");
	  }else{
		  echo "UPDATE rh_clt SET campo3 = '$cont' WHERE id_clt = '$Row[id_clt]'";
	  }*/
						  
	  
  ?>
  <tr>
    <td><?=$Row['id_clt']?></td>
    <td><?=$Row['nome']?></td>
    <td><?=$Row['campo3']?></td>
    <td><?=$cont?></td>
  </tr>
  <?php
  $cont ++;
  
  }
  ?>
  
</table>




</body>
</html>