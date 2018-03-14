<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript">
$().ready(function(){
		$('div.conteudo').hide();	
	$('div.linha').click(function(){
		$('div.conteudo').hide('fast');				  
		$(this).next().slideToggle("fast");
	});
	
});
</script>
</head>

<body>
<div class="linha" >DOCUMENTO >>></div>
<div class="conteudo">
REGIAO<br />
REGIAO<br />
REGIAO<br />
REGIAO<br />
</div>

<div class="linha" >DOCUMENTO >>> <a href="#">X</a></div>
<div class="conteudo">
REGIAO<br />
REGIAO<br />
REGIAO<br />
REGIAO<br />
</div>
<div class="linha" >DOCUMENTO >>> <a href="#">X</a></div>
<div class="conteudo">
REGIAO<br />
REGIAO<br />
REGIAO<br />
REGIAO<br />
</div>
<div class="linha" >DOCUMENTO >>> <a href="#">X</a></div>
<div class="conteudo">
REGIAO<br />
REGIAO<br />
REGIAO<br />
REGIAO<br />
</div>
</body>
</html>