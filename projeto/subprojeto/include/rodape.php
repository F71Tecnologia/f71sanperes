<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<p style="text-align:center;">
<?php $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
      $master = mysql_fetch_assoc($qr_master); ?>
<img style="position:relative; top:7px; margin-bottom:7px;" src="../imagens/logomaster<?=$Master?>.gif" width="66" height="46"> <br /><b><?=$master['razao']?></b>
&nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS</p>
<?php if(!empty($final_participantes)) { ?><p class="right"><br><br><a href="#corpo">Subir ao topo</a></p><?php } ?>
<div class="clear"></div>