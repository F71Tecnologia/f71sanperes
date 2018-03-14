<html><head><title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<link href="../net.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
#apDiv1 {
	position:absolute;
	width:415px;
	height:44px;
	z-index:1;
	left: 9px;
	top: 16px;
}
-->
</style>
</head>

<body>
<?php
if(empty($_REQUEST['id'])){

//$projeto = $_REQUEST['projeto'];
//$regiao = $_REQUEST['regiao'];

$projeto = 9;
$regiao = 3;

?>
<script language="javascript">

//VERIFICANDO A POSIÇÃO EXATA DO CAMPO TEXTO, PARA A TABELA E A DIV ENTRAREM BEM EMBAIXO DO CAMPO
function getPosicaoElemento(){
	
	//ID DO CAMPO TEXTO
	elemID = "username";
	
    var offsetTrail = document.getElementById(elemID);
    var offsetLeft = 0;
    var offsetTop = 0;
    while (offsetTrail) {
        offsetLeft += offsetTrail.offsetLeft;
        offsetTop += offsetTrail.offsetTop;
        offsetTrail = offsetTrail.offsetParent;
    }
    if (navigator.userAgent.indexOf("Mac") != -1 && 
        typeof document.body.leftMargin != "undefined") {
        offsetLeft += document.body.leftMargin;
        offsetTop += document.body.topMargin;
    }
  
  //SOMANDO 22 PX A MAIS DO CAMPO TEXTO PARA A TABELA ENTRAR ABAIXO
  offsetTop = offsetTop + 22;
  
  //INFORMANDO A POSIÇÃO PARA A DIV ENTRAR LOGO ABAIXO DO CAMPO TEXTO
  document.all.apDiv1.style.left= offsetLeft + "px";
  document.all.apDiv1.style.top= offsetTop + "px";
  

}



</script>
<script type="text/javascript">
function ajaxFunction(){
var xmlHttp;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
  xmlHttp.onreadystatechange=function() {
    if(document.getElementById('username').value == ''){
		document.all.ttdiv.style.display="none";
	}else{
		document.all.ttdiv.style.display="";
		if(xmlHttp.readyState==3){
			document.all.spantt.innerHTML="<div align='center' style='background-color:#5C7E59'><img src='../imagens/carregando/CIRCLE_BALL.gif' align='absmiddle'>Aguarde</div>";
		}else if(xmlHttp.readyState==4 & xmlHttp.status == 200){
	      //document.all.ttdiv.value=xmlHttp.responseText;
		  document.all.spantt.innerHTML=xmlHttp.responseText;
      }
    }
  }

  var enviando = escape(document.getElementById('username').value);
  xmlHttp.open("GET",'teste1.php?procura=' + enviando + '&id=1',true);
  xmlHttp.send(null);
  
  }
 
</script>

<form name="myForm">
<div align="center" id="TESTE"> Nome: 
  <input name="username" type="text" onKeyUp="ajaxFunction();getPosicaoElemento();" onFocus="getPosicaoElemento();" size="50" id="username"/>
  <img src='../imagens/carregando/CIRCLE_BALL.gif' style="display:none"></div>
</form>

<br><Br>
<div id="apDiv1" style="float:left">
  <table border="0" width="326" cellpadding="0" cellspacing="0" id="ttdiv" style="border: solid 1px #000; display:none" 
  background="../imagens/trans.png">
 <tr>
  <td><span style='font-size:13px' id="spantt"></span></td>
 </tr>
</table>
</div>
<br>
<center>
<iframe name="iframe" id="iframe" scrolling="yes" width="100%" height="80%" src="teste1.php?id=2" frameborder="0"></iframe>
<br>
<br>
</center>

<br>
<?php

}else{

//header("Content-Type: text/html;  charset=ISO-8859-1",true);    ESSA É A SOLUÇÃO PARA ACENTUAÇÃO NO AJAX

$id = $_REQUEST['id'];

switch($id){

	case 1:

include "../conn.php";



$recebi = $_REQUEST['procura'];
$pro = $_REQUEST['pro'];
$reg = $_REQUEST['reg'];

$REAuto = mysql_query("SELECT * FROM autonomo WHERE status = '1' and id_regiao = '3' and id_projeto = '11' and nome LIKE '%$recebi%'");
$numAut = mysql_num_rows($REAuto);
/*
$RECLT = mysql_query("SELECT * FROM rh_clt WHERE status < '60' and id_regiao = '$reg' and id_projeto = '$pro' and nome LIKE '%$recebi%'");
$numClt = mysql_num_rows($RECLT);
*/
// and $numClt == 0
if($numAut == 0){
	$Devolver = "<div style='color:#FF0000;' align='center'>Sua busca n&atilde;o retornou Resultado</div>";
}else{
	//WHILE DE AUTONOMOS
	while($RowTeste = mysql_fetch_array($REAuto)){
		//http://www.netsorrindo.com.br/intranet/ver_bolsista.php?reg=3&bol=4108&pro=11
		$li = "<a href='../ver_bolsista.php?reg=$RowTeste[id_regiao]&bol=$RowTeste[0]&pro=$RowTeste[id_projeto]' target='iframe'
		onCLick=\"document.all.ttdiv.style.display='none'; 
		document.all.username.value='".$RowTeste['nome']."' \" style=\"text-decoration:none\">";
		$Devolver1 .= "<div onMouseOver=\"this.style.background='#09F'\" onMouseOut=\"this.style.background=''\">$li".$RowTeste['nome']."</a></div>";
	}
	/*
	//WHILE DE CLTS
	while($RowCLT = mysql_fetch_array($RECLT)){
		//http://www.netsorrindo.com.br/intranet/ver_bolsista.php?reg=3&bol=4108&pro=11
		$li2 = "<a href='../ver_bolsista.php?reg=$RowCLT[id_regiao]&bol=$RowCLT[0]&pro=$RowCLT[id_projeto]' target='iframe'
		onCLick=\"document.all.ttdiv.style.display='none'; 
		document.all.username.value='".$RowCLT['nome']."' \" style=\"text-decoration:none\">";
		$Devolver2 .= "<div onMouseOver=\"this.style.background='#09F'\" onMouseOut=\"this.style.background=''\">$li2".$RowCLT['nome']."</a></div>";
	}
	*/
	
}

echo $Devolver1;
//echo $Devolver1;

break;

case 2:

//aki não vai imprimir nada para a tela ficar toda VERDE
?>



<?php



break;

}


}

?>
</body>
</html>