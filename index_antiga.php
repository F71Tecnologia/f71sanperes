<?php
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet ::</title>
<link rel="shortcut icon" href="favicon.ico" />
<script language="JavaScript" type="text/JavaScript">
function abrir(URL,Nombre,w,h,a) {
	
	if(w == 0 && h == 0){
		var w = 780;
  		var h = 650;
	}
	
	var left = 99;
 	var top = 99;


  	window.open(URL, Nombre+'a', 'width='+w+', height='+h+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=yes');

}
</script>
</head>
<?php
if(empty($_COOKIE['logado'])){
print "<script>location.href = 'login.php?entre=true';</script>";
} else { ?>
<frameset rows='80,*' cols="*" framespacing="0" frameborder="no" border="0" >
  <frame src="principal2.php" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" />
  <frame src="principal.php" name="mainFrame" id="mainFrame" />
</frameset>
<noframes><body>
</body>
</noframes>
<?php } ?>
</html>
