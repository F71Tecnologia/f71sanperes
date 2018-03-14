<?php
if(empty($_COOKIE['logado'])){
print "<script>location.href = 'login.php?entre=true';</script>";
} else {
include "conn.php";
include "funcoes.php";

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$result_regi = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_regi = mysql_fetch_array($result_regi);

$data = date('d/m/Y');

$result_sexo_f = mysql_query("SELECT * FROM rh_clt where sexo = 'F' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_f = mysql_num_rows($result_sexo_f);

$result_sexo_m = mysql_query("SELECT * FROM rh_clt where sexo = 'M' and id_regiao = '$regiao' and status != '62'");
$row_cont_sexo_m = mysql_num_rows($result_sexo_m);


$result_cont_total_geral = mysql_query("SELECT id_clt FROM rh_clt where id_regiao = '$regiao'");
$row_cont_total_geral = mysql_num_rows($result_cont_total_geral);

$dia = date('d');
$mes = date('m');
$ano = date('Y');
$data_antiga = date("Y-m-d", mktime (0, 0, 0, $mes  , $dia - 90, $ano));
$data_atual = date("d/m/Y");


	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkfo = encrypt("$regiao&1"); 
	$linkfo = str_replace("+","--",$linkfo);
	// -----------------------------

	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkevento = encrypt("$regiao"); 
	$linkevento = str_replace("+","--",$linkevento);
	// -----------------------------
	
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkferias = encrypt("$regiao&1"); 
	$linkferias = str_replace("+","--",$linkferias);
	// -----------------------------
	
?>
<html>
<head>
<title>:: Intranet :: Gest&atilde;o de RH</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel='shortcut icon' href='favicon.ico'> 
<link href="net1.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();

}
MM_reloadPage(true);

function popup(caminho,nome,largura,altura,rolagem) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -50;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
function popup2(caminho,nome,largura,altura,rolagem) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -50;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
function popup3(caminho,nome,largura,altura,rolagem) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -50;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
function popup4(caminho,nome,largura,altura) {
var esquerda = (screen.width - largura) / 2;
var cima = (screen.height - altura) / 2 -60;
window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}
//-->
</script>
<style type="text/css">
body {
	margin:0px;
}
.style34 {
	font-size:12px;
	font-weight:bold;
	color:#FFF;
}
.style35 {
	font-family:Geneva, Arial, Helvetica, sans-serif;
	font-weight:bold;
}
.style36 {
	font-size:14px
}
.style38 {
	font-size:16px;
	font-weight:bold;
	font-family:Geneva, Arial, Helvetica, sans-serif;
	color:#FFF;
}
.style39 {
	font-family:Geneva, Arial, Helvetica, sans-serif;
	font-weight:bold;
	font-size:14px;
	color:#000;
}
a:active {
    color:#F90;
}
</style>
<script language="javascript">
//o parâmentro form é o formulario em questão e t é um booleano 
function ticar(form, t) { 
campos = form.elements; 
for (x=0; x<campos.length; x++) 
if (campos[x].type == "checkbox") campos[x].checked = t; 
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
window.open(theURL,winName,features);
}
</script> 
</head>
<body>

<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td align="center" valign="top"> 
		<table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
		  <tr> 
	        <td colspan="4">
            	<img src="layout/topo.gif" width="750" height="38">
            </td>
          </tr>
          <tr>
            <td width="21" rowspan="5" background="layout/esquerdo.gif">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td width="26" rowspan="5" background="layout/direito.gif">&nbsp;</td>
          </tr>
          <tr>
            <td background="imagens/fundo_cima.gif"><div align="center">
                <p align="left" class="style6"><span class="style34">&nbsp;<strong><br>
                &nbsp;
                <?=$row_user['nome1']?>
                <br>
                &nbsp;&nbsp;<?=$data?><BR>
                &nbsp;&nbsp;Regi&atilde;o: 
                <?=$row_regi['regiao']?>
                </strong></span><br>
                <br>
                </p>
                </div>
             </td>
			 <td background="imagens/fundo_cima.gif">
             	&nbsp;&nbsp;<img src="imagens/adicionar_bolsista.gif" alt="gestao" width="40" height="40" align="absmiddle"><span class="style38">GEST&Atilde;O DE RECURSOS HUMANOS</span>
             </td>
           </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><div align="center"></div></td>
		  </tr>
		  <tr>
			<td colspan="2" align="center" bgcolor="#FFFFFF">
            
<table width="90%" height="300px" bgcolor="#FFFFFF" border="0" align="center" cellspacing="2" cellpadding="2">
  <tr>
  <?php
  		//BLOQUEIO PAULO MONTEIRO SJR 30-03 - 13hs
  		if($id_user != '73') {
		?>
	<td width="33%" bgcolor="#003300">
		<div align="center" class="style27 style36 style35">EMPRESA</div>
	</td>
	<td width="35%" bgcolor="#003300" align="center" class="style27 style36 style35">
    	FUNCION&Aacute;RIOS
    </td>
    <td width="32%" bgcolor="#003300" align="center" class="style27 style36 style35">
    	FOLHA, RELAT&Oacute;RIOS e IMPOSTOS
    </td>
  </tr>
  <tr>
    <td class="rh" valign="middle" bgcolor="#EEEEEE">
	    <a href="#">
    	    <img src="rh/imagensrh/dadosempresa.gif" alt="empresa" width="150" height="40" onClick="MM_openBrWindow('rh/rh_empresa.php?id=1&regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
    <td class="rh" valign="middle" bgcolor="#EEEEEE">
	    <a href="rh/clt.php?regiao=<?=$regiao?>">
    	    <img src="rh/imagensrh/edicao.gif" alt="editar" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
    <td class="rh" valign="middle" bgcolor="#EEEEEE">
	    <a href="rh/folha/folha.php?tela=1&enc=<?=$linkfo?>" target="_blank">
    	    <img src="rh/imagensrh/situacoes.gif" alt="folha" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td><?php 
	  }  
		  ?>
  </tr>
  <tr>
	<td class="rh" valign="middle" bgcolor="#E9E9E9">
		<a href="#">
        	<img src="rh/imagensrh/feriados.gif" alt="feriados" width="150" height="40" onClick="MM_openBrWindow('rh/rh_feriados.php?id=1&amp;regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
       </a>
	</td>
	<td class="rh" valign="middle" bgcolor="#E9E9E9">
		<a href="rh/rh_eventos.php?enc=<?=$linkevento?>">
        	<img src="rh/imagensrh/eventos.gif" alt="eventos" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
	<td class="rh" valign="middle" bgcolor="#E9E9E9">
		<a href="rh/pis/index.php?regiao=<?=$_GET['regiao']?>" target="_blank">
        	<img src="rh/imagensrh/pis.gif" alt="PIS" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
  </tr>
  <tr>
	<td class="rh" valign="middle" bgcolor="#E4E4E4">
		<a href="rh/rh_impostos.php?id=1&amp;regiao=<?=$regiao?>">
        	<img src="rh/imagensrh/taxas.gif" alt="taxas" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
	<td class="rh" valign="middle" bgcolor="#E4E4E4">
		<a href="rh/rh_movimentos.php?regiao=<?=$regiao?>&tela=1">
        	<img src="rh/imagensrh/movimentos.gif" alt="movimentos" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
         </a>
     </td>
	<td class="rh" valign="middle" bgcolor="#E4E4E4">
		<a href="rh/rais/index.php?id=1&regiao=<?=$regiao?>" target="_blank">
        	<img src="rh/imagensrh/ponto.gif" alt="rais" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
  </tr>
  <tr>
	<td class="rh" valign="middle" bgcolor="#DFDFDF">
		<a href="#">
        	<img src="rh/imagensrh/sindicatos.gif" alt="sindicatos" width="150" height="40" onClick="MM_openBrWindow('rh/rh_sindicatos.php?id=1&regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
     </td>
	<td class="rh" valign="middle" bgcolor="#DFDFDF">
		<a href="rh/contracheque/solicita.php?id=1&enc=<?=$linkfo?>" target="_blank">
        	<img src="rh/imagensrh/contracheques.gif" alt="contras" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
	<td class="rh" valign="middle" bgcolor="#DFDFDF">
		<a href="rh/sefip/index.php?regiao=<?=$regiao?>" target="_blank">
        	<img src="rh/imagensrh/sefip.gif" alt="SEFIP" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
  </tr>
  <tr>
	<td class="rh" valign="middle" bgcolor="#DADADA">
		<a href="rh/rh_horarios.php?regiao=<?=$regiao?>" target="_blank">
        	<img src="rh/imagensrh/horarios.gif" alt="horarios" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
	<td class="rh" valign="middle" bgcolor="#DADADA">
  		<a href="rh/recisao/recisao.php?regiao=<?=$_GET['regiao']?>">
  			<img src="rh/imagensrh/rescisao.gif" alt="rescis&atilde;o" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
	<td class="rh" valign="middle" bgcolor="#D5D5D5">
    	<a href="rh/ir/index.php?regiao=<?=$_GET['regiao']?>" target="_blank">
        	<img src="rh/imagensrh/irrf.gif" alt="darf" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
  </tr>
  <tr>
	<td class="rh" valign="middle" bgcolor="#D5D5D5">
		<a href="rh/rh_telavale.php?regiao=<?=$regiao?>" target="_blank">
        	<img src="rh/imagensrh/vale.gif" alt="vale" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
	<td class="rh" valign="middle" bgcolor="#D5D5D5">
		<a href="rh/ferias/index.php?tela=1&enc=<?=$linkferias?>">
        	<img src="rh/imagensrh/ferias.gif" alt="ferias" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
	<td class="rh" valign="middle" bgcolor="#D5D5D5">
    	<a href="rendimento/index2.php?id_reg=<?=$regiao?>&tela=1" target="_blank">
        	<img src="rh/imagensrh/informe_rendimento.gif" alt="Informe de Rendimento" width="150" height="40" style="opacity:0.84; filter:alpha(opacity=84)" onMouseOver="this.style.opacity=1; this.filters.alpha.opacity=100" onMouseOut="this.style.opacity=0.84; this.filters.alpha.opacity=84">
        </a>
    </td>
  </tr>
  <tr>
  	<td align="center" valign="middle" bgcolor="#FFCCCC">
    	<?php 
		$filtro_user = array(75,9,33,77,5,68,82);
		if(in_array($id_user,$filtro_user)){ ?>
        <a href="rh/pagamentos/index.php?id=<?=$_GET['id']?>&regiao=<?=$regiao?>" style="display:block;">
        	<img src="rh/imagensrh/pagamentos.png" />
        </a>
        <?php }?>
    </td>
  	<td align="center" valign="middle" bgcolor="#FFCCCC">
    	<a href="#" style="display:block;">
        	<img src="rh/notifica/imagens/avisos.gif" alt="avisos" width="150" height="40" border="0" onClick="MM_openBrWindow('rh/notifica/avisos.php?regiao=<?=$regiao?>','','scrollbars=yes,resizable=yes,width=760,height=600')">
        </a>
    </td>
    <td align="center" valign="middle" bgcolor="#FFCCCC">
    	<a href="rh/caged/">
        	<img src="rh/imagensrh/bt_caged.png" width="150" height="40">
        </a>
    </td>
  </tr>
</table>


<br>

<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
  <td colspan="2" class="titulo_rh">CONTROLE DE PARTICIPANTES NA REGI&Atilde;O AT&Eacute; A DATA ATUAL</td>
  </tr>
  <tr>
  <td colspan="2" class="descricao_rh">Total de participantes: <?=$row_cont_total_geral?></td>
  </tr>
  <tr>
  <td colspan="2" class="titulo_rh">CONTROLE DE FUNCION&Aacute;RIOS POR SITUA&Ccedil;&Atilde;O ATUAL</td>
  </tr>
  <?php
$cont = "0";

$result_rhstatus = mysql_query("SELECT * FROM rhstatus where status_reg = '1'");
while($row_rhstatus = mysql_fetch_array($result_rhstatus)){

$result_cont_status = mysql_query("SELECT id_clt FROM rh_clt where status = '$row_rhstatus[codigo]' and id_regiao = '$regiao'");
$row_cont_status = mysql_num_rows($result_cont_status);

if($cont % 2){ $cor_linha="#FFFFFF"; }else{ $cor_linha="#EFEFEF"; }


print "
<tr style='line-height:20px;' bgcolor='$cor_linha'>
<td><div align='left' class='style25'><span class='style39'>($row_rhstatus[codigo]) $row_rhstatus[especifica]</span></div></td>
<td><div class='style25'><span class='style39'>&nbsp;&nbsp;$row_cont_status</span></div></td>
</tr>
";
$cont ++;
}

?>
  
  <tr>
  <td colspan="2" class="titulo_rh">CONTROLE DE FUNCION&Aacute;RIOS ATIVOS POR SEXO</td>
  </tr>
  <tr>
  <td bgcolor="#FFFFFF"><div class="style25"><span class="style39">Homens</span></div></td>
  <td bgcolor="#FFFFFF"><div class='style25'><span class='style39'>&nbsp;&nbsp;<?=$row_cont_sexo_m?></span></div></td>
  </tr>
  <tr bgcolor="#CCFFFF">
    <td bgcolor="#EBEBEB"><div class="style25"><span class="style39">Mulheres</span></div></td>
    <td bgcolor="#EBEBEB"><div class='style25'><span class='style39'>&nbsp;&nbsp;<?=$row_cont_sexo_f?></span></div></td>
  </tr>
  <tr>
    <td colspan="2" class="titulo_rh">CONTROLE DE FUNCION&Aacute;RIOS EM EXPERI&Ecirc;NCIA</td>
    </tr>
  <tr bgcolor="#CCFFFF">
    <td bgcolor="#FFFFCC"><div class="style25"><span class="style39">Funcionário em experiência</span></div></td>
    <td bgcolor="#FFFFCC"><div class='style25'><span class='style39'>&nbsp;
      <? 
  $result_data_entrada = mysql_query("SELECT id_clt FROM rh_clt WHERE data_entrada > '$data_antiga' AND id_regiao = '$regiao'");
  $row_datas = mysql_num_rows($result_data_entrada);
  print "$row_datas";
  ?></span></div></td>
  </tr>
</table>
</td>
</tr>
<tr>
<td width="155">&nbsp;</td>
<td width="549">&nbsp;</td>
</tr>
<tr valign="top"> 
<td height="37" colspan="4" bgcolor="#E2E2E2"> <img src="layout/baixo.gif" width="750" height="38"> 
<?php
include('empresa.php');
$rod = new empresa();
$rod -> rodape();
?></td>
</tr>
</table>
</td>
</tr>
</table>
<?php } ?>
</body>
</html>
