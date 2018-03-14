<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include "../classes/projeto.php";
include "../classes/curso.php";

$id = $_REQUEST['id'];
$regiao = $_REQUEST['regiao'];

$data = date('d/m/Y');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

if(!empty($_REQUEST['esc'])){
	$esc = $_REQUEST['esc'];
	
	$REesc = mysql_query("SELECT * FROM escala WHERE id_escala = '$esc'");
	$RowEsc = mysql_fetch_array($REesc);
	
	$ESCid_regiao = $RowEsc['id_regiao'];
	$ESCid_projeto = $RowEsc['id_projeto'];
	$ESCid_curso = $RowEsc['id_curso'];
	
	$Curso = new tabcurso();
	$Curso -> MostraCurso($ESCid_curso);
	
	$NomeCurso = $Curso -> nome;
	
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
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>

<style type="text/css">
<!--
.dragme{position:relative;}

body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style35 {	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style36 {	font-size: 14px;
	font-family: Verdana, Geneva, sans-serif;
}
.style40 {font-family: Geneva, Arial, Helvetica, sans-serif}

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
<script language="javascript">
//VERIFICANDO A POSIÇÃO EXATA DO CAMPO TEXTO, PARA A TABELA E A DIV ENTRAREM BEM EMBAIXO DO CAMPO
function getPosicaoElemento(elemID,outroID){
	
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
  
  xmlHttp.open("GET",'ajax.php?pro=<?=$ESCid_projeto?>&reg=<?=$ESCid_regiao?>&curso=<?=$ESCid_curso?>&id=5&idcampo=' + Element,true);
  xmlHttp.send(null);
  
  }
 

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location=	'"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}


</script>

<body onLoad="limpaCache('escala.php');">

<div id="apDiv1" style="float:left">
  <table border="0" width="326" cellpadding="0" cellspacing="0" id="ttdiv" style="border: solid 1px #000; display:none" 
  background="../imagens/trans.png">
 <tr>
  <td><span style='font-size:13px' id="spantt"></span></td>
 </tr>
</table>
</div>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"> 
<?php
if($id == 1){

$tabela = "escala";					//VARIAVEL PARA O AJAX
$nomeid = "id_escala";				//VARIAVEL PARA O AJAX
$tipoaj = "1";						//VARIAVEL PARA O AJAX ( TIPO DO CAMPO ESPECIAL OU NÃO EX: VALOR OU DATA )
$pasta  = "1";						//HIERARQUIVA DA PASTA ( ../classes/ajaxupdate.php )

?>
<br>


<table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="../layout/topo.gif" width="750" height="38"></td>
  </tr>
  <tr>
    <td width="21" rowspan="5" background="../layout/esquerdo.gif">&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td width="26" rowspan="5" background="../layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" colspan="2"><?php include('../reportar_erro.php'); ?></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="24" colspan="2" background="rh/imagens/fundo_cima.gif"><table  height="181" width="95%" border="1" align="center" cellspacing="0" bordercolor="#333333">
      <tr>
        <td height="35" bgcolor="#003300">
          <div align="center" class="style27"><strong>GERENCIAMENTO DE ESCALAS
            </strong></div></td>
        </tr>
      <tr>
        <td height="144" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0" bordercolor="#333333">
            <tr class="campotexto">
              <td align="center" valign="baseline"><br>
                <br>
                VISUALIZAR ESCALAS<br>
                <br>
                <a href="#"><img src="../imagens/verbolsista.gif" width="190" height="31" border="0" onClick="document.all.visualizar.style.display = (document.all.visualizar.style.display == 'none') ? '' : 'none' ;" ></a> <br>
                <br>
                <br>
                <br></td>
              <td align="center" valign="baseline"><br>
                <br>
                CADASTRO DE ESCALAS<br>
                <br>
                <a href="#"><img src="../imagens/castrobolsista.gif" width="190" height="31" border="0" 
                onClick="document.all.cadastro.style.display = (document.all.cadastro.style.display == 'none') ? '' : 'none' ;" ></a> <br>
                <br>
                <br></td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td width="155">&nbsp;</td>
    <td width="549">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="../layout/baixo.gif" width="750" height="38">
      <div align="center" class="style6"><br>
      </div></td>
  </tr>
</table>
<table width="90%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="display:none" id="visualizar" class="bordaescura1px">
  <tr>
    <td height="34" class="fundo_azul"><div align="center" class="titulo">ESCALAS</div></td>
  </tr>
  <tr>
    <td height="151" align="center" valign="top" ><?php
	//PREPARANDO VARIAVIES PARA ENVIAR AO CLASSE
	$tab			= "escala";
	$where			= "where id_regiao = '$regiao'"; 			//EX "WHERE id_escala = '1'"
	$campos			= array('id_escala','nome','periodos','hora1','hora2','hora3','hora4','hora5','hora6',);
	$titulos		= array('Cod','Nome','Periodos','Horario1','Horario2','Horario3','Horario4','Horario5','Horario6');
	$alinhamentos	= array('center','left','center','center','center','center','center','center','center');
	$tamanho		= "97%";
	
    include "../classes/selects.php";
	$printTab = new selects();
	$printTab -> MostraEscalas($tab,$where,$campos,$titulos,$alinhamentos,$tamanho);
	
	?>      <br></td>
  </tr>
  </table>
<table width="90%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="display:none" id="cadastro" class="bordaescura1px">
  <tr>
    <td height="34" class="fundo_azul"><div align="center" class="titulo">INFORMA&Ccedil;&Otilde;ES DA ESCALAS</div></td>
    </tr>
  <tr>
    <td height="151" align="center" valign="top" ><br>
      <form action="escala.php" name="form1" method="post">
        <table width="97%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
          <tr>
            <th height="25" colspan="2" bgcolor="#CCCCCC"><div style="font-size:14px">NOVA ESCALA</div></th>
          </tr>
          <tr>
            <td height="30" align="right" bgcolor="#EFEFEF"><div align="right" class="titulo_opcoes">Projeto:</div></td>
            <td height="30" bgcolor="#EFEFEF">&nbsp;&nbsp;
              <?php
				
				$pro = new projeto();
				$pro -> SelectProjetos("$regiao","id_projeto","1","1");  //( REGIAO , ID DO CAMPO, AJAX, IDAJAX )
				
				?></td>
          </tr>
          <tr>
            <td height="30" colspan="2" align="right" bgcolor="#DFDFDF"><table width="100%" border="0" cellpadding="0" cellspacing="0">
             <tr class="fundo_azul">
                <td height="29" colspan="2" valign="middle"><div class="titulo_claro">SELECIONE A(S) ATIVIDADE(S):</div></td>
              </tr>
                <td width="75%" height="119" valign="top"><div id='DivUnidade'>&nbsp;</div></td>
                <td width="25%" height="119" valign="top">
                <input type="button" name="insert" value="&lt;&lt; Inserir" onClick="insertValueQuery('id_curso','sql_query','visualatividade')" 
                title="Insere" />
                  <br></td>
              </tr>
              <tr class="fundo_azul">
                <td height="29" colspan="2" valign="middle"><div class="titulo_claro">ATIVIDADES SELECIONADAS:</div></td>
              </tr>
              <tr>
                <td height="101" colspan="2" align="center" valign="top">&nbsp;&nbsp; <br>
                  <textarea cols="80" rows="5" id="visualatividade" name="visualatividade" readonly></textarea>
                  <textarea cols="30" rows="5" id="sql_query" name="sql_query" style="display:none"></textarea>
                  <br>
                  <input type="button" name="limpar" value="&lt;&lt; Limpar" 
                  onClick="document.all.visualatividade.value=''; document.all.sql_query.value=''; document.all.id_projeto.style.display=''; " />
                  <br></td>
              </tr>
              <tr>
                <td colspan="2"></td>
              </tr>
            </table></td>
            </tr>
          <tr>
            <td width="16%" height="30" align="right" bgcolor="#EFEFEF"><div align="right" class="titulo_opcoes" style="titulo_opcoes">Nome da escala:</div></td>
            <td width="84%" height="30" bgcolor="#EFEFEF"><div>&nbsp;&nbsp;
              <input name="nome" type="text" id="nome" tabindex="0" onChange="this.value=this.value.toUpperCase()" size="50">
              &nbsp;&nbsp;</div></td>
          </tr>
          <tr>
            <td height="30" bgcolor="#DFDFDF"><div align="right" class="titulo_opcoes" style="titulo_opcoes">Quantidade de Periodos:</div></td>
            <td height="30" bgcolor="#DFDFDF"><div>&nbsp;&nbsp;
              <input name="periodos" type="text" class="campotexto4" id="periodos" tabindex="0" size="4" onKeyUp="ajaxFunctionlocal(3)">
              &nbsp;&nbsp;&nbsp;&nbsp;( 2, 3, 4, 5 ou 6 )</div></td>
          </tr>
          <tr>
            <td height="30" colspan="2" bgcolor="#EFEFEF"><div id='DivNova'>&nbsp;</div></td>
          </tr>
          <tr>
            <td height="41" colspan="2" align="center" valign="middle" bgcolor="#DFDFDF">
            <input type="hidden" name="regiao" value="<?=$regiao?>">
              <input type="hidden" name="id" value="3">
              <input type="submit" value="Enviar"></td>
          </tr>
        </table>
        <br>
      </form></td>
    </tr>
</table>
<p>&nbsp;</p>
<?php 
	  //TERMINA DE PRINTAR A PRIMEIRA PARTE
		}elseif($id == 2){
	  	
		$escala = $_REQUEST['esc'];

		if(empty($_REQUEST['mes'])){
			$ReVerificaMes = mysql_query("SELECT mes FROM escala_proc WHERE id_escala = '$RowEsc[id_escala]' ORDER BY mes");
	        while($RowVerificaMes = mysql_fetch_array($ReVerificaMes)){
		    $ArrayMesesDU[] = $RowVerificaMes['mes']; }
			$mes = "01";
			$Tab = "style='display:none'";
			$SelectMes = "<select id='mes' name='mes' onchange=\"MM_jumpMenu('parent',this,0)\">";
			$SelectMes .=  "<option>Selecione</option>";
			for($i=1; $i <=12; $i ++){
				$INTM = (int)$i;
				
				if($ArrayMesesDU != 0 ){
					if (!in_array("$i", $ArrayMesesDU)) {
						$SelectMes .=  "<option value='escala.php?id=2&esc=$escala&mes=$INTM'>$meses[$i]</option>";
					}
				}else{
					$SelectMes .=  "<option value='escala.php?id=2&esc=$escala&mes=$INTM'>$meses[$i]</option>";
				}
				
			}
			$SelectMes .=  "</select>";
			
		}else{
			$mes = $_REQUEST['mes'];
			$Tab = "style='display:'";
			$MesInt = (int)$mes;
			$NomeMES = $meses[$MesInt];
			$SelectMes = $NomeMES;
		}
		
		$ano = date('Y');
		
		$REescala = mysql_query("SELECT * FROM  escala WHERE id_escala = '$escala'");
		$RowEscala = mysql_fetch_array($REescala);
		
		$regiao = $RowEscala['id_regiao'];
		
	  ?>
      <a href="escala.php?id=1&regiao=<?=$regiao?>"><img src="../imagens/voltar.gif" border="0"></a>
      <br>
      <table width="90%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" id='cad' style="display:" >
        <tr>
          <td height="34" colspan="2" align="center" class="fundo_azul"><div align="center" class="titulo">MONTANDO A ESCALA
          <br>
          <?=$NomeCurso?>
          <br>
          <?=$SelectMes?>
          
          </div></td>
        </tr>
        <tr>
          <td height="138" colspan="2" align="center" valign="top"><!-- TABELINHA FORA -->
		  <br>
          
		  <form action="escala2.php" method="post" name="Form1" <?=$Tab?>>
		  <?php
          //######################################################################################################
		  $REMes = mysql_query("SELECT *,date_format(data, '%d')as data FROM ano WHERE month(data) = '$mes' and year(data)='$ano'");
		  
		  echo "<table width='90%' border='0' cellpadding='0' cellspacing='1' bgcolor='#999999' >";
		  echo "<tr height='30'><td align='center'>
		  <div onClick=\"document.all.ttdiv.style.display='none'\" style='cursor:pointer; color:#FF0000'><b>Fechar LISTA</b></div> </td>";
		  
		  for($i=1; $i <=$RowEscala['periodos']; $i ++){
			  
			  $ago = "hora".$i;
			  $horario = $RowEscala[$ago];
			  
			  echo "<td bgcolor='#E2E2E2' align='center'> $horario </td>";
		  }
		  echo "</tr>";
		  
		  while($Row = mysql_fetch_array($REMes)){
			  echo "<tr height='30'>";
			  
			  echo "<td width='20%' align='center' bgcolor='#E2E2E2'><b>$Row[data] de $NomeMES</b><div style='color:#0000FF'> $Row[nome] </div></td>";
			  
			  for($i=1; $i <=$RowEscala['periodos']; $i ++){
				  $aAj = "onKeyUp=\"ajaxFunction2(this.id);getPosicaoElemento(this.id);\" 
				  onFocus=\"ajaxFunction2(this.id);getPosicaoElemento(this.id);\"";
				  echo "<td bgcolor='#FFFFFF' align='center'><input type='text' name='$Row[data]_$i' id='$Row[data]_$i' size='15' $aAj> </td>";
			  }
			  
			  echo "</tr>";
			  
		  }
          echo "</table>";
          
		  ?>
          
          <br>
          <input type="submit" value="Enviar">
          <br>
          <input type="hidden" name="regiao" value="<?=$regiao?>">
          <input type="hidden" name="escala" value="<?=$esc?>">
          <input type="hidden" name="mes" value="<?=$mes?>">
          </form>
          <br></td>
        </tr>
      </table>
      <br>
<?php
//TERMINA DE PRINTAR A SEGUNDA PARTE
}elseif($id == 3){


	include "../classes/insert.php";
	
	$userid = $_COOKIE['logado'];
	
	$regiao = $_REQUEST['regiao'];
	$escala = $_REQUEST['escala'];
	$mes = $_REQUEST['mes'];
	$id_curso = $_REQUEST['sql_query'];
	
	//TIRANDO VIRGULA DO PRIMEIRO CARACTERERE
    $id_curso_final = substr("$id_curso", 2);
	
	$campos_reservados[] = 'Enviar';
	$campos_reservados[] = 'escala';
	$campos_reservados[] = 'mes';
	$campos_reservados[] = 'id';
	$campos_reservados[] = 'visualatividade';
	$campos_reservados[] = 'sql_query';
	$campos_reservados[] = 'id_curso';
	
	$conteudo = new insert();
	$conteudo -> campos_insert($HTTP_POST_VARS,$campos_reservados);
	
	$Campos = $conteudo -> campos;
	$Valores = $conteudo -> valores;
	
	//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
	$n_camp = strlen($Campos);						//CONTANDO A QUANTIDADE DE CARACTERS
	$n_camp = $n_camp - 1;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
	$Campos = str_split($Campos, $n_camp);		    //EXPLODINDO D VARIAVEL, JA SEM A VIRGULA
	
	//RESOLVENDO O PROBLEMA COM A ULTIMA VIRGULA
	$n_val = strlen($Valores);						//CONTANDO A QUANTIDADE DE CARACTERS
	$n_val = $n_val - 1;							//DIMINUINDO CARACTERS POR 4 PARA REMOVER A VIRGULA
	$Valores = str_split($Valores, $n_val);		    //EXPLODINDO D VARIAVEL, JA SEM A VIRGULA
	
	$Query = "INSERT INTO escala ($Campos[0],user_cad,id_curso) VALUES ($Valores[0],'$userid','$id_curso_final')";
	
	//echo $Query;
	
	mysql_query($Query) or die ("Erro no Insert <br><br>".mysql_error());
	
	
	 print "
	<script> 
	alert(\"Dados gravados com êxito!\");
	location.href = \"escala.php?id=1&regiao=$regiao\";
	</script>"; 

	/*
	$tabela = "escala";
	$campos = array(id_escala,id_regiao,id_projeto,id_curso,nome,status);
	$valores = array(NULL,$id_regiao,$id_pro,$curso,$nome,1);
	
	include "../classes/insert.php";
	$inserindo = new insert();
	$inserindo -> InsertDinamico($tabela,$campos,$valores);

	$variavel = $inserindo -> retorno;
	*/
}
	  
	  ?></td>
  </tr>
</table>
</body>
</html>