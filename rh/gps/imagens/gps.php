
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SAL&Aacute;RIO FAM&Iacute;LIA</title>
<link href="../../net.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
body {
 background-color: #CCC;
}
-->
</style>
<link href="../../../net.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
</head>

<body onLoad="MM_preloadImages('processar2.gif','pagar2.gif')">
<table width="80%" border="0" align="center"  bgcolor="#FFFFFF" cellpadding="5" cellspacing="5">
  <tr>
    <td width="100%" align="center" valign="middle"><p class="linha">
<?php
include "../../../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?></p></td>
  </tr>
  <tr>
    <td align="center" valign="middle"><img src="logogps.jpg" width="212" height="82"><br>
      <span class="linha">GERENCIAMENTO</span><br>
      <br>
      <table width="90%" border="1" cellpadding="0" cellspacing="0" style="border-bottom-color:#999; border-top-color:#999; border-left-color:#999; border-right-color:#999">
        <tr>
          <td colspan="4" align="center" valign="middle" bgcolor="#666666"><strong class="style6">Rela&ccedil;&atilde;o de Folhas de Pagamento Finalizadas</strong></td>
        </tr>
        <tr class="campotexto">
          <td align="center" bgcolor="#CCFFCC">FOLHA</td>
          <td align="center" bgcolor="#CCFFCC">M&Ecirc;S</td>
          <td align="center" bgcolor="#CCFFCC">GERAR</td>
          <td align="center" bgcolor="#CCFFCC">ENVIAR PARA O FINANCEIRO</td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center"><a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image2','','processar2.gif',1)"><img src="processar.gif" name="Image2" width="150" height="25" border="0"></a></td>
          <td align="center"><a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image3','','pagar2.gif',1)"><img src="pagar.gif" name="Image3" width="200" height="45" border="0"></a></td>
        </tr>
      </table>
      <br>
      <br>
<hr color="#CCCCCC">
      <br>
      <table width="90%" border="1" cellpadding="0" cellspacing="0" style="border-bottom-color:#999; border-top-color:#999; border-left-color:#999; border-right-color:#999">
        <tr>
          <td colspan="3" align="center" valign="middle" bgcolor="#666666"><strong class="style6">Rela&ccedil;&atilde;o de Folhas de Pagamento Finalizadas</strong></td>
        </tr>
        <tr class="campotexto">
          <td align="center" bgcolor="#CCFFCC">FOLHA</td>
          <td align="center" bgcolor="#CCFFCC">M&Ecirc;S</td>
          <td align="center" bgcolor="#CCFFCC">GERARVISUALIZAR</td>
        </tr>
        <tr>
          <td align="center">&nbsp;</td>
          <td align="center">&nbsp;</td>
          <td align="center"><a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image4','','processar2.gif',1)"><img src="processar.gif" name="Image4" width="150" height="25" border="0"></a></td>
        </tr>
      </table>
      <p><br>      
    &nbsp;</p></td>
  </tr>
  <tr>
    <td align="center" valign="middle">&nbsp;</td>
  </tr>
</table>
<p class="linha">&nbsp;</p>
</body>
</html>
