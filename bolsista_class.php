<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "conn.php";
$id = $_REQUEST['id'];

switch ($id) {
case 1:

//if (empty($_REQUEST['projeto'])){       //Esta tela será apresentada 1º e listará todos os projetos
$regiao = $_REQUEST['regiao'];

$result = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' AND status_reg = '1'");
$row_cont = mysql_num_rows($result);


//VERIFICANDO SE EXISTE PROJETO CADASTRADO PARA A REGIÃO SELECIONADA

if ($row_cont == "0"){                                     //CASO NÃO EXISTA PROJETO CADASTRADO PARA A REGIÃO SELECIONADA
print "<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>";
print "<center><br><div class='campotext4'> Visializando Projetos</div><br><br><span class='style1'>Nenhum Projeto encontrado para sua região!</span></center>";
print "<br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";

} else {											//CASO EXISTA PROJETO CADASTRADO PARA A REGIÃO SELECIONADA

print "<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>";
print "<br><div class='campotext4'> Visializando Projetos</div><br><br>";

print "<table bgcolor=#FFFFFF width='500' align='center'><tr class='linha' bgcolor=#CCCCCC><td align=center>Projeto</td><td align=center>Tema</td></tr>";
while ($row = mysql_fetch_array($result)){
print "<tr><td><a href=bolsista_class.php?id=2&projeto=$row[0]&regiao=$regiao class=link>$row[2]</a></td><td><span class='style3'>$row[2]</span></td></tr>";
}

print "</table><br><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";

}
break;

//------------------------------------------------------//
//  													//
//  ESTA TELA MOSTRARA TODOS OS AUTONOMOS DE ACORDO		//
//  COM O PROJETO SELECIONADO NA TELA ANTERIOR			//
//  													//
//------------------------------------------------------//

case 2:
$id_projeto = $_REQUEST['projeto'];
$id_regiao = $_REQUEST['regiao'];

//INICIANDO CONTADORES CLT NÃO AVALIADOS
$REClt = mysql_query("SELECT COUNT(id_clt) FROM rh_clt WHERE id_projeto = '$id_projeto' and id_psicologia = '0'");
$numCLT = mysql_fetch_array($REClt);
$RowNumCLT = $numCLT[0];

//INICIANDO CONTADORES AUTONOMO NÃO AVALIADOS
$REaut = mysql_query("SELECT COUNT(id_autonomo) FROM autonomo WHERE id_projeto = '$id_projeto' and id_psicologia = '0'");
$numAUT = mysql_fetch_array($REaut);
$RowNumAUT = $numAUT[0];


//INICIANDO CONTADORES CLT AVALIADOS
$REClt2 = mysql_query("SELECT COUNT(id_clt) FROM rh_clt WHERE id_projeto = '$id_projeto' and id_psicologia = '1'");
$numCLT2 = mysql_fetch_array($REClt2);
$RowNumCLT2 = $numCLT2[0];

//INICIANDO CONTADORES AUTONOMO AVALIADOS
$REaut2 = mysql_query("SELECT COUNT(id_autonomo) FROM autonomo WHERE id_projeto = '$id_projeto' and id_psicologia = '1'");
$numAUT2 = mysql_fetch_array($REaut2);
$RowNumAUT2 = $numAUT2[0];


$NaoAvaliados = $RowNumCLT + $RowNumAUT;
$Avaliados = $RowNumCLT2 + $RowNumAUT2;


//INICIO DA SEGUNDA PARTE
print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<meta http-equiv=\"Cache-Control\" content=\"No-Cache\">
<meta http-equiv=\"Pragma\"        content=\"No-Cache\">
<meta http-equiv=\"Expires\"       content=\"0\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\">

<script type=\"text/javascript\">
function ajaxFunction(part){
var xmlHttp;
var part;
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
    xmlHttp=new ActiveXObject(\"Msxml2.XMLHTTP\");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject(\"Microsoft.XMLHTTP\");
      }
    catch (e)
      {
      alert(\"Your browser does not support AJAX!\");
      return false;
      }
    }
  }
  xmlHttp.onreadystatechange=function() {
    if(xmlHttp.readyState==3){
		if(part <= 2){
			document.all.spantt.innerHTML=\"Aguarde\";
		}else{
			document.all.spantt2.innerHTML=\"Aguarde\";
		}
	}else if(xmlHttp.readyState==4){
		if(part <= 2){
			document.all.spantt.innerHTML=xmlHttp.responseText;
		}else{
			document.all.spantt2.innerHTML=xmlHttp.responseText;
		}
    }
  }

  xmlHttp.open(\"POST\",'bolsista_class_avaliados.php?id=' + part + '&regiao=$id_regiao&projeto=$id_projeto',true);
  xmlHttp.send(null);
  
  }
 
</script>
</head>
<body bgcolor='#D7E6D5'>
<form action='av_lote.php' method='post'>

<table width='97%' border='0' cellspacing='0' cellpadding='0' bgcolor='#FFFFFF' align=center>
 <tr>
    <td>
		<div style=\"float:right;\"></div>
		<div style=\"clear:right;\"></div>	
	
	</td>
</tr>

  <tr>
    <td>
	
	
<center><font color=RED><b>Funcionários NÃO Avaliados $NaoAvaliados</b>
<br><br>
<input type='button' onClick='ajaxFunction(1)' value='CLT'>&nbsp;&nbsp;&nbsp; <input type='button' onClick='ajaxFunction(2)' value='Autônomo / Cooperado'>
</font></center>
<br>
<div id='spantt' name='spantt'><center>Clique no botão Acima para Filtrar pelo TIPO DE CONTRATAÇÃO</center></div>
<br>";




   print "<input type='hidden' name='regiao' value='$id_regiao'/>
   <input type='hidden' name='projeto' value='$id_projeto'/>";
   print "<center></center></form>";
   
   //----------------------------------------------------------------------------------------------------------------------------

   //-------------------------------------||
   //-INICIANDO OS QUE JA FORAM AVALIADOS-||
   //-------------------------------------||
      
   //----------------------------------------------------------------------------------------------------------------------------
   
   
   
	//AUTONOMOS JA AVALIADOS
	$REAutonAva = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida 
	FROM autonomo WHERE id_psicologia = '1' and id_projeto = '$id_projeto' ORDER BY locacao,nome") or die(mysql_error());

	//CLTS JA AVALIADOS
	$RECLTAva = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida 
	FROM rh_clt WHERE id_psicologia = '1' and id_projeto = '$id_projeto' ORDER BY locacao,nome") or die(mysql_error());


   print "<form action='av_lote.php' method='post'>";
   
   print "</td></tr><tr><td bgcolor=#D7E6D5><hr color=#666666></td></tr><tr><td>
   <center>
   <br><br>
   
   <font color=blue><b>Funcionários Avaliados $Avaliados</b></font><br><br>
    
	<input type='button' onClick='ajaxFunction(3)' value='CLT'>&nbsp;&nbsp;&nbsp; <input type='button' onClick='ajaxFunction(4)' 
	value='Autônomo / Cooperado'>
   
   </center><br>
   

	

	<div id='spantt2' name='spantt'><center>Clique no botão Acima para Filtrar pelo TIPO DE CONTRATAÇÃO</center></div>";
	
	//INICIANDO A TABELA DOS CLTS
	
	
	

print "<input type='hidden' name='regiao' value='$id_regiao'/><input type='hidden' name='projeto' value='$id_projeto'/>";
print "</form><br>";

print "</td>
  </tr>
</table>
<br><br><center><a href='bolsista_class.php?id=1&regiao=$id_regiao' class='link'><img src='imagens/voltar.gif' border=0></a></center>
";

//FINAL DA TELA ONDE LISTA TODOS OS BOLSISTAS E CLTS AVALIADOS E NÃO AVALIADOS
break;



case 3:

$id_bolsista = $_REQUEST['bol'];
$tabela = $_REQUEST['tab'];
$id_regiao = $_REQUEST['regiao'];

$result_bolsista = mysql_query("SELECT * FROM bolsista$tabela where id_bolsista = $id_bolsista", $conn);
$row_bolsista = mysql_fetch_array($result_bolsista);

$result_psicologia = mysql_query("SELECT * FROM psicologia", $conn);
$result_cont = mysql_query("SELECT COUNT(*) FROM psicologia", $conn);
$row_cont = mysql_fetch_row($result_cont);

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\">
</head><body bgcolor='#D7E6D5'>";

print "
<form action='cadastro2.php' method='post' name='form1' class='style6' id='form1'>
<table width='60%' align='center' cellspacing='5'>
<tr> 
<td height='25' colspan='2' align='center' valign='middle'><div align='left'><span class='style2'> 

</span></div></td>
</tr>
<tr> 
<td height='25' colspan='2' align='center' valign='middle' bgcolor='#99CC99'>
<b>Classifica&ccedil;&atilde;o da Psicologia do Bolsista</b></td>
</tr>
<tr> 
<td height='25' colspan='2' align='center' valign='middle' bgcolor='#99CC99'>
<font color=red><b>$row_bolsista[nome] - $row_bolsista[atividade]</b></font></td>
</tr>";
$contagem = $row_cont['0'];
if ($contagem == "0"){
 print "
   <tr> 
    <td width='9%' align='center' valign='middle' class='style23' colspan='2'>
    <font size=3 class='style27'>A lista de opções ainda não existe! <br><a href=bolsista_class.php?id=4&id_regiao=$id_regiao class='style27'>Para criar clique aqui</a> </font></td>
  </tr>";
}else{

while ($row_psicologia = mysql_fetch_array($result_psicologia)){
 print "
   <tr> 
    <td width='9%' align='center' valign='middle' class='style23'>
	<input type='radio' name='radio' value='$row_psicologia[id_psicologia]'></td>
    <td width='91%' colspan='3'><font size=3 class='style27'>$row_psicologia[texto]</font></td>
  </tr>";
}
}
print "
<tr>
  <td colspan='2' valign='top' bgcolor='#99CC99'>
  
  <input type='hidden' name='id_cadastro' value='6'/>
  <input type='hidden' name='id_bolsista' value='$row_bolsista[0]'/>
  <input type='hidden' name='id_projeto' value='$tabela'/>
  <input type='hidden' name='id_regiao' value='$id_regiao'/>
  
  </td>
</tr>
<tr>
  <td colspan='2' align='center' bgcolor='#99CC99'><input type='submit' name='enviar' value='SALVAR' /></td>
</tr>
</table>
</form>";
break;
case 4:

$id_regiao = $_REQUEST['id_regiao'];

$result_cont = mysql_query("SELECT COUNT(*) FROM psicologia", $conn);
$row_cont = mysql_fetch_row($result_cont);

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\">
</head><body bgcolor='#D7E6D5'>";


print "
<form action='cadastro2.php' method='post' name='form1' class='style6' id='form1'>
<table width='60%' align='center' cellspacing='5'>
<tr> 
<td height='25' colspan='4' align='center' valign='middle'><div align='left'><span class='style2'> 
<br />
</span></div></td>
</tr>
<tr> 
<td height='25' colspan='4' align='center' valign='middle' bgcolor='#99CC99'><span class='style25'><strong>Criar a Lista de Classifica&ccedil;&atilde;o da Psicologia do Bolsista</strong> 
</span> </td>
</tr>
<tr> 
<td width='21%' align='center' valign='middle'><strong></strong></td>
<td width='79%' colspan='3' class='style27'>Existem $row_cont[0] itens cadastrados até o momento.</td>
</tr>
<tr> 
<td align='center' valign='middle'><div align='right' class='style27'><strong>Texto:</strong></div></td>
<td colspan='3'><input type='text' name='texto'></td>
</tr>
<tr> 
<td align='center' valign='top'>
<div align='right' class='style27'><strong>Descri&ccedil;&atilde;o:</strong></div></td>
<td colspan='3'><textarea name='descricao'></textarea></td>
</tr>
<tr> 
<td align='center' valign='middle'><strong></strong></td>
<td colspan='3'>&nbsp;</td>
</tr>
<tr> 
<td colspan='4' valign='top' bgcolor='#99CC99'><div align='center' class='style24'>OBS: verifique as informa&ccedil;&otilde;es antes de enviar</div></td>
</tr>
<tr> 
<td colspan='4' valign='top' bgcolor='#99CC99'><div align='center'>

  <input type='hidden' name='id_cadastro' value='7'/>
  <input type='hidden' name='id_regiao' value='$id_regiao'/>

<input type='submit' name='Submit' value='ENVIAR'>
</div></td>
</tr>
</table><br><br><a href='javascript:window.close()'><img src='imagens/sair.gif' border=0></a>";
}

?>