<html>
<head>
  <title>Funcao para graficos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.style1 {color: #FFFFFF}
.style2 {
	color: #000000;
	font-weight: bold;
}
.style3 {color: #000000}
-->
</style>
</head>

<body>

<table width="700" align="center" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr>
    <td width="82" height="19">&nbsp;</td>
    <td width="615" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
        <!--DWLayoutTable-->
        <tr>
          <td width="615" height="19" valign="top" bgcolor="#CCCCCC"><div align="center"><strong>Gerador de gr&aacute;ficos usando PHP + GD </strong></div></td>
        </tr>
        </table></td>
    <td width="81"></td>
  </tr>
  <tr>
    <td height="16"></td>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
        <!--DWLayoutTable-->
        <tr>
          <td width="615" height="16"></td>
        </tr>
    </table></td>
    <td></td>
  </tr>
  <tr>
    <td height="19"></td>
		<form name="valores" action="graf_gui.php?total=<?=$_POST[parametro];?>" method="POST">
    <td valign="top" bgcolor="#CCCCCC">
	<?php
	  for ($i=1;$i<=($_POST[parametro]);$i++):
	  $nome = "nome".$i;
 	  $qtd = "qtd".$i;
	  
	?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr>
          <td width="261" height="22" valign="top"><div align="right">nome:
              <input type="text" name="<?=$nome;?>">
          </div></td>
          <td width="21">&nbsp;</td>
          <td width="333" valign="top"><div align="left">quantidade:
              <input type="text" name="<?=$qtd;?>">
          </div></td>
        </tr>
        </table>
	<?php
	  endfor;
	?>	
	    <table align="center">
		 <tr>
		   <td align="center" height="90">
		   <input type="submit" name="enviar" value="enviar">
		   </td>
		 </tr>
		</table>
	</td>
	     
	</form>
    <td></td>
  </tr>
  <tr>
    <td height="31"></td>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
      <!--DWLayoutTable-->
      <tr>
        <td width="615" height="31">&nbsp;</td>
      </tr>
    </table></td>
    <td></td>
  </tr>
  <tr>
    <td height="21"></td>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr>
          <td width="566" height="21" valign="top" bgcolor="#CCCCCC"><div align="center"><span class="style2">Desenvolvido por Guilherme Mota - 2004 </span></div></td>
        </tr>
	      </tr>
         <tr>
        <td width="566" height="21" valign="top" bgcolor="#CCCCCC"><div align="center">
	Quer o fonte? me mande um email => <a href="mailto:gui.mota@ig.com.br">gui.mota@ig.com.br</a>
	</div></td>
      </tr>
    </table></td>
    <td></td>
  </tr>
  <tr>
    <td height="32"></td>
    <td></td>
    <td></td>
  </tr>
</table>
</body>
</html>
