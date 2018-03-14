<?php
header("Content-Type: text/html;  charset=ISO-8859-1",true);

include "conn.php";
include "classes/projeto.php";

?>
<html><head><title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<link href="net1.css" rel="stylesheet" type="text/css">
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

$projeto = $_REQUEST['projeto'];
$regiao = $_REQUEST['regiao'];

$Projeto = new projeto();
$Projeto -> MostraProjeto($projeto);

$NomProjeto = $Projeto -> nome;
$NumProjeto = $Projeto -> id_projeto;

//$projeto = 9;
//$regiao = 3;

?>
<a href="ver.php?projeto=<?=$projeto?>&regiao=<?=$regiao?>" class="link"><img src="imagens/voltar.gif" border="0"></a>
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
			document.all.spantt.innerHTML="<div align='center' style='background-color:#5C7E59'><img src='imagens/carregando/CIRCLE_BALL.gif' align='absmiddle'>Aguarde</div>";
		}else if(xmlHttp.readyState==4){
	      //document.all.ttdiv.value=xmlHttp.responseText;
		  document.all.spantt.innerHTML=xmlHttp.responseText;
      }
    }
  }

  var enviando = escape(document.getElementById('username').value);
  xmlHttp.open("GET",'ver_avancado.php?procura=' + enviando + '&id=1&pro=<?=$projeto?>&reg=<?=$regiao?>',true);
  xmlHttp.send(null);
  
  }
 
</script>
<table width="750" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td colspan="3"><img src='layout/topo.gif' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width="21" height="72" background='layout/esquerdo.gif'>&nbsp;</td>
    <td width="703" align="center" bgcolor="#FFFFFF">
    <div class="titulo_opcoes"><?=$NumProjeto." - ".$NomProjeto?></div>
<br>
	
    <form name="myForm">
      <div align="center" id="TESTE"> Nome:
        <input name="username" type="text" onKeyUp="ajaxFunction();getPosicaoElemento();" onFocus="getPosicaoElemento();" size="50" id="username"/>
        <img src='imagens/carregando/CIRCLE_BALL.gif' style="display:none"></div>
    </form></td>
    <td width="26" background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3"><img src='layout/baixo.gif' width='750' height='38' /></td>
  </tr>
</table>
<br><Br>
<div id="apDiv1" style="float:left">
  <table border="0" width="326" cellpadding="0" cellspacing="0" id="ttdiv" style="border: solid 1px #000; display:none" 
  background="imagens/trans.png">
 <tr>
  <td><span style='font-size:13px' id="spantt"></span></td>
 </tr>
</table>
</div>
<br>
<center>

<br>
<br>
</center>

<br>
<?php } else {

//header("Content-Type: text/html;  charset=ISO-8859-1",true);    ESSA É A SOLUÇÃO PARA ACENTUAÇÃO NO AJAX

$id = $_REQUEST['id'];

switch($id){
case 1:

$recebi = $_REQUEST['procura'];
$pro = $_REQUEST['pro'];
$reg = $_REQUEST['reg'];

$qr_busca = mysql_query("SELECT id_autonomo, nome, id_regiao, id_projeto, tipo_contratacao FROM autonomo WHERE status = '1' AND id_regiao = '$reg' AND id_projeto = '$pro' AND nome LIKE '%$recebi%' UNION SELECT id_clt, nome, id_regiao, id_projeto, tipo_contratacao FROM rh_clt WHERE status < '60' AND id_regiao = '$reg' AND id_projeto = '$pro' AND nome LIKE '%$recebi%'");
$total = mysql_num_rows($qr_busca);

if(empty($total)) {
	    $Devolver = "<div style='color:#FF0000;' align='center'>Sua busca n&atilde;o retornou resultado</div>";
} else {
	while($busca = mysql_fetch_array($qr_busca)){
	    if($busca['tipo_contratacao'] == "2") {
		    $li = "<a href='rh/ver_clt.php?reg=$busca[id_regiao]&clt=$busca[0]&pro=$busca[id_projeto]' target='iframe'
		           onCLick=\"document.all.ttdiv.style.display='none'; 
		           document.all.username.value='".$busca['nome']."' \" style=\"text-decoration:none\">";
	    } else {
		    $li = "<a href='ver_bolsista.php?reg=$busca[id_regiao]&bol=$busca[0]&pro=$busca[id_projeto]' target='iframe'
		          onCLick=\"document.all.ttdiv.style.display='none'; 
		          document.all.username.value='".$busca['nome']."' \" style=\"text-decoration:none\">";
	    }
		$Devolver .= "<div onMouseOver=\"this.style.background='#09F'\" onMouseOut=\"this.style.background=''\">$li".$busca['nome']."</a></div>";
	}
}

echo $Devolver;

break;
case 2:

//aki não vai imprimir nada para a tela ficar toda VERDE
?>

<?php break;
}
}
?>
</body>
</html>