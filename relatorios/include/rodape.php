<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<p class="left">
<?php $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
      $master = mysql_fetch_assoc($qr_master); ?>
<img style="position:relative; top:7px;" src="../imagens/logomaster<?=$Master?>.gif" width="66" height="46"> <b><?=$master['razao']?></b>
&nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS</p>
<p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
<div class="clear"></div>