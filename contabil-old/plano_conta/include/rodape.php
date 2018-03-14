<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<p align="center">
<?php $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
      $master = mysql_fetch_assoc($qr_master); ?>
<b><?=$master['razao']?></b>
&nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS</p>