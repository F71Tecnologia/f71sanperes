<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../classes/curso.php";

$id = $_REQUEST['id'];

$REEsc = mysql_query("SELECT id_escala,mes FROM escala_proc WHERE id_escala_proc = '$id'");
$RowEsc = mysql_fetch_array($REEsc);

$REescala = mysql_query("SELECT * FROM  escala WHERE id_escala = '$RowEsc[id_escala]'");
$RowEscala = mysql_fetch_array($REescala);

$Curso = new tabcurso();

$Atividades = explode(", ",$RowEscala['id_curso']);
$contAtivi = count($Atividades);
	
if($contAtivi != 1){
		
	for($i=0 ; $i < $contAtivi; $i ++){
		
		$Ativ = $Atividades[$i];
		
		$Curso -> MostraCurso($Ativ);
		$Atividade = $Curso -> nome;
		
		$nomeatividade .= $Atividade." | ";
		
	}#END FOR
	
				
}else{	#CASO SEJE APENAS UMA ATIVIDADE
	
	$Curso -> MostraCurso($RowEscala['id_curso']);
	$nomeatividade = $Curso -> nome;	
	
}#END if($contAtivi != 1)
	
$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$mes = $RowEsc['mes'];
$MesInt = (int)$mes;
$NomeMES = $meses[$MesInt];


$tabela = "escala_proc";					//VARIAVEL PARA O AJAX
$nomeid = "id_escala_proc";					//VARIAVEL PARA O AJAX
$tipoaj = "1";								//VARIAVEL PARA O AJAX ( TIPO DO CAMPO ESPECIAL OU NÃO EX: VALOR OU DATA )
$pasta  = "1";								//HIERARQUIVA DA PASTA ( ../classes/ajaxupdate.php )

if(empty($_REQUEST['editar'])){
	$bteditar = "<a href='escalapronta.php?id=$id&editar=1' style='text-decoration:none; color:#666'>editar</a>";
}else{
	$bteditar = "<a href='escalapronta.php?id=$id' style='text-decoration:none; color:#666'>visualizar</a>";
}

$VerificaDuplicar = mysql_query("SELECT COUNT(id_escala) as num FROM escala_proc WHERE id_escala = '$RowEsc[id_escala]'");
$RowTT = mysql_fetch_array($VerificaDuplicar);

if($RowTT['num'] < 12){
	$btduplicar = " - [ <a href='duplica_escala.php?id=$id' style='text-decoration:none; color:#666'>duplicar</a> ]";
}
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<link href="../net1.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/ramon.js"></script>


<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
#apDiv1 {
	position:absolute;
	width:415px;
	height:44px;
	z-index:1;
	left: 2px;
	top: 13px;
}
-->
</style>

<script language="javascript">
//VERIFICANDO A POSIÇÃO EXATA DO CAMPO TEXTO, PARA A TABELA E A DIV ENTRAREM BEM EMBAIXO DO CAMPO
function getPosicaoElemento(elemID){
	
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
  offsetLeft = offsetLeft - 45;
  //INFORMANDO A POSIÇÃO PARA A DIV ENTRAR LOGO ABAIXO DO CAMPO TEXTO
  document.all.apDiv1.style.left= offsetLeft + "px";
  document.all.apDiv1.style.top= offsetTop + "px";

}

function funcaoSegura(){
	document.getElementById("nome").disabled=true;
	document.getElementById("id_curso").disabled=true;
	document.getElementById("id_projeto").disabled=true;
	document.getElementById("btCriar").style.display='none';
	//id_escala
}

// -----------------------------------------------------------------------------------------------------------

function ajaxFunctionlocal(a){
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
  
  if(a == 1){
	  var d = document.all.DivUnidade;
  }else if(a == 2){
	  var d = document.all.DivParticipantes;
  }else if(a == 3){
	  var d = document.all.DivNova;
  }
  
  xmlHttp.onreadystatechange=function() {
	d.innerHTML="<img src='../imagens/carregando/CIRCLE_BALL_branco.gif' align='absmiddle'> Aguarde.. ";
	if(xmlHttp.readyState==4){
		//document.all.ttdiv.value=xmlHttp.responseText;
		d.innerHTML=xmlHttp.responseText;
    }
  }
  
  if(a == 1){
	  var enviando = escape(document.getElementById('id_projeto').value);
  }else if(a == 2){
	  var enviando = escape(document.getElementById('id_projeto').value);
	  var enviando2 = escape(document.getElementById('unidade').value);
	  enviando = enviando + "&unidade=" + enviando2;
  }else if(a == 3){
	  var enviando = document.getElementById('periodos').value;
  }
  
  xmlHttp.open("GET",'ajax.php?projeto=' + enviando + '&id=' + a,true);
  xmlHttp.send(null);
  
  }

//------------------------------------------------------------------------------------------------------------------

//FUNCAO AJAX 2
function ajaxFunction2(Element){
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
    //if(document.getElementById(Element).value == ''){
	//	document.all.ttdiv.style.display="none";
	//}else{
		document.all.ttdiv.style.display="";
		
		if(xmlHttp.readyState==3){
			document.all.spantt.innerHTML="<div align='center'>Aguarde...</div>";
		}else if(xmlHttp.readyState==4){
	      //document.all.ttdiv.value=xmlHttp.responseText;
		  document.all.spantt.innerHTML=xmlHttp.responseText;
      }
	  
    //}
  }
  //$ESCid_regiao; $ESCid_projeto; $ESCid_curso
  var enviando = escape(document.getElementById(Element).value);
  
  //alert('ajax.php?pro=<?=$ESCid_projeto?>&reg=<?=$ESCid_regiao?>&curso=<?=$ESCid_curso?>&id=5&idcampo=' + Element);
  
  xmlHttp.open("GET",'ajax.php?pro=<?=$RowEscala['id_projeto']?>&reg=<?=$RowEscala['id_regiao']?>&curso=<?=$RowEscala['id_curso']?>&id=5&idcampo=' + Element,true);
  xmlHttp.send(null);
  
  }
 

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location=	'"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}


</script>

</head>

<body onLoad="limpaCache('escalapronta.php');">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><br>
      <div id="apDiv1" style="float:left">
        <table border="0" width="326" cellpadding="0" cellspacing="0" id="ttdiv" style="border: solid 1px #000; display:none" 
  background="../imagens/trans.png">
          <tr>
            <td><span style='font-size:13px' id="spantt"></span></td>
          </tr>
        </table>
      </div>
      <table width="60%" border="0" cellpadding="0" cellspacing="0" class="novatabela" 
      style="border:#666 dashed 1px;  background:#f4f8fc; color:#666 ">
      <tr>
      <td height="97" align="center" valign="middle">
      <div style="font-size:14px;"><b>
        <?=$nomeatividade?>
      </b></div>
      <div style="font-size:14px;"><b>Escala referente ao m&ecirc;s de
        <?=$NomeMES?>
      </b></div>
      <br>
      [ <?=$bteditar?> ]
      <?=$btduplicar?>
      </td>
      </tr>
      </table>
      <br>
      <?php
	  #QUERY PARA DUPLICAR
	  #INSERT INTO TABELA1 (CAMPO1, CAMPO2) SELECT CAMPO1, CAMPO2 FROM DUAL WHERE CAMPO=VALOR
          //######################################################################################################
		  $ano = date('Y');
		  
		  $REMes = mysql_query("SELECT *,date_format(data, '%d')as data FROM ano WHERE month(data) = '$RowEsc[mes]' and year(data)='$ano'");
		  
		  echo "<table width='90%' border='0' cellpadding='0' cellspacing='1' bgcolor='#999999' >";
		  echo "<tr height='30'><td align='center'> - </td>";
		  
		  for($i=1; $i <=$RowEscala['periodos']; $i ++){
			  
			  $ago = "hora".$i;
			  $horario = $RowEscala[$ago];
			  
			  echo "<td bgcolor='#E2E2E2' align='center'> $horario </td>";
		  }
		  echo "</tr>";
		  
		  while($Row = mysql_fetch_array($REMes)){
			  echo "<tr height='30'>";
			  
			  echo "<td width='20%' align='center' bgcolor='#E2E2E2'><b>$Row[data] de $NomeMES </b><div style='color:#0000FF'> $Row[nome] </div></td>";
			  
			  for($i=1; $i <=$RowEscala['periodos']; $i ++){
				  
				  $nomeSELECT = $Row['data']."_".$i;
				  
				  $REUni = mysql_query("SELECT $nomeSELECT FROM escala_proc WHERE id_escala_proc = '$id' ");
				  $RowUni = mysql_fetch_array($REUni);
				  
				  $Nome = explode(" ",$RowUni['0']);
				  
				  //VERIFICANDO SE ESTA EDITANDO OU SE ESTÁ APENAS VENDO A ESCALA
				  if(empty($_REQUEST['editar'])){
					  //ESTIVER APENSA VENDO, VAMOS MOSTRAR O NOME E O SOBRE NOME
					  if($Nome[1] == "DE" or $Nome[1] == "DA" or $Nome[1] == "DO"){
						  $SegundoNome = $Nome[1]." ".$Nome[2];
					  }else{
						  $SegundoNome = $Nome[1];
					  }
					  
					  echo "<td bgcolor='#FFFFFF' align='center'><div name='$Row[data]_$i' id='$Row[data]_$i'>$Nome[0] $SegundoNome</div></td>";
					  
				  }else{
					  //ESTIVER EDITANDO, VAI APARECER O INPUT E OS NOME COMPLETO
					  $aAj = "onKeyUp=\"ajaxFunction2(this.id);getPosicaoElemento(this.id);\" 
					  onFocus=\"ajaxFunction2(this.id);getPosicaoElemento(this.id);\" 
					  onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$id','2','1')\" ";
					  
					  echo "<td bgcolor='#FFFFFF' align='center'>";
					  echo "<input type='text' name='$Row[data]_$i' id='$Row[data]_$i' size='15' $aAj 
					  value='$RowUni[0]'></td>";
				  }
					  
					  
					  
			  }
			  
			  echo "</tr>";
			  
		  }
          echo "</table>";
          
		  ?>
    <br>
    <a href="escala.php?id=1&id_reg=<?=$RowEscala['id_regiao']?>"><img src="../imagens/voltar.gif" width="111" height="31" border="0"></a><br></td>
  </tr>
</table>
</body>
</html>

