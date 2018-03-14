<?php
if(empty($_COOKIE['logado2'])){
print "<html><head><title>:: Financeiro ::</title>
<link href='net2.css' rel='stylesheet' type='text/css'>
<body>
<font color=#FFFFFF>
<br><center><h1>Desculpe!</h1><br>Você não tem permissão para ver está página.</conter>
</font></body></html>
";
exit;
}

include "conn.php";
$regiao = $_REQUEST['regiao'];
$userlog = $_COOKIE['logado'];

if(!empty($_REQUEST['apro'])){
	
	$id_user = $_COOKIE['logado2'];
	$apro = $_REQUEST['apro'];
	$vale = $_REQUEST['vale'];
	$valor = $_REQUEST['valor'];
	$regiao = $_REQUEST['regiao'];
	$idComb = $_REQUEST['idcomb'];
	$dataCad = date('Y-m-d');
	
	if($apro == 1){		
		mysql_query("UPDATE fr_combustivel SET status_reg = '2', data_libe = '$dataCad', numero='$vale', user_libe = '$id_user' WHERE 
		id_combustivel = '$idComb'");
		$link = "frota/printcombustivel.php?com=$idComb&regiao=$regiao";
	}else{
		mysql_query("UPDATE fr_combustivel SET status_reg = '0', data_libe = '$dataCad', user_libe = '$id_user' WHERE id_combustivel = '$idComb'");
		$link = "financeiro.php?regiao=$regiao";
	}
	
	
	print "<script>
	location.href=\"$link\";
	</script>";
	
	exit;
}


$mes2 = date('F');

$dia_h = date('d');
$mes_h = date('m');
$ano = date('Y');

/*
$mes_q_vem = $mes_h + 1;
$mes_passado = $mes_h - 1;
$ano_passado = $ano - 1;
*/

$mes_passadoano	= date("Y-m", mktime(0,0,0, $mes_h - 1, $dia_h, $ano));
$mes_q_vem 		= date("m", mktime(0,0,0, $mes_h + 1, $dia_h, $ano));
$ano_passado 	= date("Y", mktime(0,0,0, $mes_h , $dia_h, $ano - 1));

switch ($mes_h) {
case 1:
$mes = "Janeiro";
break;
case 2:
$mes = "Fevereiro";
break;
case 3:
$mes = "Março";
break;
case 4:
$mes = "Abril";
break;
case 5:
$mes = "Maio";
break;
case 6:
$mes = "Junho";
break;
case 7:
$mes = "Julho";
break;
case 8:
$mes = "Agosto";
break;
case 9:
$mes = "Setembro";
break;
case 10:
$mes = "Outubro";
break;
case 11:
$mes = "Novembro";
break;
case 12:
$mes = "Dezembro";
break;
}

$data_hoje = "$dia_h/$mes_h/$ano";
$dia_amanha = "$dia_h" + "1";

//-------------VERIVICANDO AS CONTAS PARA HOJE------------------
$result_jr = mysql_query("SELECT * FROM saida where id_regiao = '$regiao' and status = '1'
and data_vencimento = '$ano-$mes_h-$dia_h' ORDER BY data_vencimento");

$result_banco_jr = mysql_query("SELECT * FROM bancos where id_regiao='$regiao' and saldo LIKE '-%'");

$linha_jr = mysql_num_rows($result_jr);

$linha_banco_jr = mysql_num_rows($result_banco_jr);

if($linha_jr > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr CONTA(S) A PAGAR HOJE');</script>";
}else{
}
if($linha_banco_jr > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_banco_jr SALDO(S) NEGATIVO(S)');</script>";
}else{
}

?>
<html><head><title>:: Financeiro ::</title>

<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<script type="text/javascript" src="js/highslide-with-html.js"></script>
<link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="js/highslide.css" />

<link href="net1.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="js/ramon.js"></script>
<script type="text/javascript">
    hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>

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
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<style type="text/css">
<!--
body {
	background-color: #5C7E59;
}
body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
}
.style2 {font-size: 12px}
.style3 {
	color: #FF0000;
	font-weight: bold;
}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style9 {color: #FF0000}
.style12 {
	font-size: 12px;
	font-weight: bold;
	color: #003300;
}
.style13 {font-size: 10px}
.style25 {
	font-size: 11px;
	font-weight: bold;
}
.style29 {color: #000000}
.style71 {color: #003300}
.style131 {font-size: 10px}
-->
</style></head>

<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  <tr>
    <td width="21" rowspan="28" background="layout/esquerdo.gif" bgcolor="#5C7E59">&nbsp;</td>
    <td colspan="2" bgcolor="#003300"><div align="center"><span class="style5 style27"><strong>GERENCIADOR FINANCEIRO </strong></span></div></td>
    <td width="26" rowspan="28" background="layout/direito.gif" bgcolor="#5C7E59">&nbsp;</td>
  </tr>
  <?php
  //SOMENTE EUGENIO E SILVANIA PODEM VER CONTROLE DE COMBUSTIVEL
  // or $userlog == '27'
  if($userlog == '27' or $userlog == '28' or $userlog == '1'){
  ?>
  <tr>
    <td height="19" colspan="2" bgcolor="#FFFFCC">&nbsp;&nbsp;&nbsp;
    <img src="imagensfinanceiro/combustivel.png" width="25" height="25" align="absmiddle">
    <strong class="style3">&nbsp;CONTROLE DE COMBUST&Iacute;VEL</strong></td>
  </tr>
  <tr>
    <td height="66" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">
    
    <?php
	
	echo "<table width='95%' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC'>";
	
	print "<tr>
	<th>PEDIDO POR</th>
	<th>DE</th>
	<th>PARA</th>
	<th>DATA</th>
	<th>LIBERAR</th>
	</tr>";
	
	$REComb = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cad FROM fr_combustivel where status_reg='1'");
	$cont = "0";
	while($RowComb = mysql_fetch_array($REComb)){
		if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		
		
		if($RowComb['funcionario'] == 2){ //FUNCIONARIO EXTERNO ( NÃO ESTA CADASTRADO NA TABELA FUNCIONARIOS )
			$REFuncionario = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$RowComb[id_user]'");
			$RowFuncionario = mysql_fetch_array($REFuncionario);
			$NOME = $RowComb['nome'];
			$RG = $RowComb['rg'];
		}else{//FUNCIONARIO INTERNO ( SELECIONAMOS O NOME E O CPF DELE CADASTRADO NA BASE DE DADOS )
			$REUser = mysql_query("SELECT nome,rg FROM funcionario where id_funcionario = '$RowComb[id_user]'");
			$RowUser = mysql_fetch_array($REUser);
			$NOME = $RowUser['nome'];
			$RG = $RowUser['rg'];
		}
		
		
		$REREG = mysql_query("SELECT regiao FROM regioes where id_regiao = '$RowComb[id_regiao]'");
		$RowREG = mysql_fetch_array($REREG);
		
	print "<tr>
	<td align='center' bgcolor='#FFFFFF'>$NOME</td>
	<td align='center' bgcolor='#FFFFFF'>$RowREG[regiao]</td>
	<td align='center' bgcolor='#FFFFFF'>$RowComb[destino]</td>
	<td align='center' bgcolor='#FFFFFF'>$RowComb[data_cad]</td>
	<td align='center' bgcolor='#FFFFFF'>
	<a href='#' 
	onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header',headingText: 'Liberar' } )\" 
	class='highslide'> OK </a>
	
	
	<div class='highslide-maincontent'>
	<form action='' method='post' name='form'>
	
	<table width='526' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC'>
		<tr>
			<td align='center' colspan='2' bgcolor='#FFFFFF'>
			<label><input type='radio' name='apro' id='apro' value='1'>&nbsp;Aprovar</label> &nbsp;&nbsp;
			<label><input type='radio' name='apro' id='apro' value='2'>&nbsp;Recusar</label>
			</td>
		</tr>
		<tr>
			<th align='right'>Número do Vale:</th>
			<td>&nbsp;<input name='vale' type='text' size='20' id='vale'/>&nbsp;</td>
		</tr>
		<tr>
			<th align='right'>Valor do Vale:</th>
			<td>&nbsp;<input name='valor' type='text' size='13' id='valor' OnKeyDown=\"FormataValor(this,event,17,2)\"/>&nbsp;</td>
		</tr>
		<tr>
			<td align='center' colspan='2' bgcolor='#FFFFFF'><input type='submit' value='Enviar' /></td>
		</tr>
	</table>
	<input type='hidden' id='regiao' name='regiao' value='$regiao'/>
	<input type='hidden' id='idcomb' name='idcomb' value='$RowComb[0]'/>
	</form>
	</div>
	
	</td>
	</tr>";
	$cont ++;
	}
	
	echo "</table>";
    
    ?>
    </td>
  </tr>
  <?php
  }
  ?>
  <tr>
    <td colspan="2" bgcolor="#FFFFCC"><span class="style5">&nbsp;&nbsp;&nbsp;<img src="imagensfinanceiro/relatafin.gif" alt="contas" width="25" height="25" align="absmiddle" />&nbsp;<strong class="style3">RELAT&Oacute;RIOS FINANCEIROS &amp; ACESS&Oacute;RIOS</strong></span></td>
  </tr>
  <tr>
    <td colspan="2">
    
    <table border="0" width="95%">
        <tr>
          <td width="91" class="style3"> <div align="right">RELAT&Oacute;RIOS <br>
            FINANCEIROS </div></td>
          <td width="117"><div align="center"><a href="#" onClick="MM_openBrWindow('login_adm2.php?regiao=<?=$regiao;?>','','scrollbars=yes,resizable=yes,width=750,height=550')"><img src='imagens/ver_relatorio.gif' alt="" border=0 align="middle"></a> </div>
          <td width="102"><div align="right" class="style3"><span class="style31">CALCULADORA <br>
            FINANCEIRA</span></div>
          <td width="50"><div align="center"><a href="#">
          <img src="calculadora/botao.gif" alt="calculadora" width="50" height="68" border="0" onClick="MM_openBrWindow('calculadora/caculadora.html','','width=465,height=615')"></a> </div>
            <td width="145"><div align="right" class="style3"><span class="style31">CALEND&Aacute;RIO<br>
            MENSAL</span></div>
            <td width="55"> 
			<?php 
			
			print " ";
			include "dfcalendar.php"; 
			
			?>  </td>
        </tr>
     </table>
      <BR><BR>

     </td>
  </tr>
  <tr>
    <td colspan="2"><div align="center"></div></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFCC"><div align="center" class="style3">
      <div align="left">&nbsp;&nbsp;&nbsp;<img src="imagensfinanceiro/gerenciador.gif" alt="gastos" width="25" height="25" align="absmiddle" />&nbsp;CONTROLE GASTOS</div>
    </div></td>
  </tr>
  <tr>
    <td colspan="2" align="left" valign="middle" bgcolor="#FFFFFF"><span class="style12">&nbsp;&nbsp;&nbsp;&nbsp;<span class="style131">SOLICITA&Ccedil;&Atilde;O DE REEMBOLSO:</span></span></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" bgcolor="#FBDAAC"><span class="style71">
<?php
	print "<table width='100%' border=1 bordercolor=#FFE6FF cellpadding='0' cellspacing='0'>";
	
	$REReem = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM fr_reembolso WHERE status = '1'");

	while($RowReem = mysql_fetch_array($REReem)){
	  
	  if($RowReem['funcionario'] == "1"){
	  	$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$RowReem[id_user]'");
	  	$row_user = mysql_fetch_array($result_user);
	  	$NOME = $row_user['nome1'];  
	  }else{
	  	$NOME = $RowReem['nome']; 
	  }
	  
	  $pagar_imagem = "-";
	  
	  $codigo = sprintf("%05d",$RowReem['0']);
	  $valor = $RowReem['valor'];
	  
	  $valorF = number_format($valor,2,",",".");
	  
	  $link = "<a href='frota/ver_reembolso.php?id=1&reembolso=$RowReem[0]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\">";

	print "
	<tr class='style13'>
	<td width=50 align='center'><b> $codigo </b></td>
	<td width=80 align='center'><b>$RowReem[data]</b></td>
	<td width=350 align='left'><b>$NOME</b></td>
	<td>R$ $valorF</td>
	<td align='center'>$link<img src=imagensfinanceiro/editar.gif alt='Editar' border=0> </a></td>
	<td align='center'><img src=imagensfinanceiro/deletar.gif alt='Deletar' border=0></td></tr>";
    
	$soma = $soma + $valor;

	}
	
    $soma_f = number_format($soma,2,",",".");

    print "</table></td>
  </tr>
  <tr>
    <td height='18' colspan='2' align='center' valign='top' bgcolor='#FBDAAC'>
	<hr color='#F8B55A'>
	<strong>TOTAL DE REEMBOLSO: <span class='style9'>R$ $soma_f </span>"; 
	  
	  ?>
    </span></td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF">      
    <div align="center" class="style6"> 
        <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7"><?php print "<a href='saidas.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?>&nbsp;&nbsp;&nbsp;<img src="imagensfinanceiro/saidas.gif" alt="saidas" width="25" height="25" align="absmiddle" border="0"/><span class="style3">CADASTRAR SA&Iacute;DA</span></a></span></font></div>
      </div></td>
  </tr>
  <tr>
    <td height="16" colspan="2" align="center" valign="top">    <div align="left"><span class="style12">&nbsp;&nbsp;&nbsp;&nbsp;<span class="style13">RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS CADASTRADAS POR DATA: </span></span></div></td>
  </tr>
  <tr>
    <td height="54" colspan="2" align="center" valign="top" bgcolor="#FFCCFF"><span class="style7"> 
      <?PHP

	  $soma = "0";

  // MOSTRANDO SAÍDAS DO MES ANTERIOR NÃO PAGAS ---------------------------------------------

	  
	print "<table width='100%' border=1 bordercolor=#FFE6FF cellpadding='0' cellspacing='0'>";
	
	$result_saidas_a = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 FROM saida 
	where id_regiao = '$regiao' and status = '1' and data_vencimento >= '2008-08-01' 
	and data_vencimento <= '2009-02-29' ORDER BY data_vencimento");
    $row_linhas = mysql_num_rows($result_saidas_a);
	
	while($row_saidas_a = mysql_fetch_array($result_saidas_a)){
	  $result_banco_saida_a = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas_a[id_banco]'");
	  $row_banco_saida_a = mysql_fetch_array($result_banco_saida_a);
	  
	  if($row_saidas_a['id_banco'] == "0"){
	  $pagar_imagem_a = "<a href=edit_saidas.php?idsaida=$row_saidas_a[0]&tabela=saida&regiao=$regiao>
	  <img src=imagensfinanceiro/editar.gif alt='Editar' border=0>";
	  }else{
	  $pagar_imagem_a = "<a href=ver_tudo.php?id=17&pro=$row_saidas_a[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1>
	  <img src=imagensfinanceiro/pagar.gif alt='Pagar' border=0>";
	  }
	  
	  if($row_saidas_a['comprovante'] == "0"){
	  $anexo_a = "";
	  }else{
	  $anexo_a = "<img src=imagensfinanceiro/anexo.gif alt='Anexo'>";
	  }
	  	  

	  $valor1_a = "$row_saidas_a[valor]";
	  $adicional1_a = "$row_saidas_a[adicional]";
	  
	  $valor_a = str_replace(",", ".", $valor1_a);
	  $adicional_a = str_replace(",", ".", $adicional1_a);
	  
	  $valor_final_a = $valor_a + $adicional_a;
	  
	  $valor_f_a = number_format($valor_final_a,2,",",".");

	print "
	<tr class='style13' bgcolor=$cor>
	<td width=30><b>$row_saidas_a[0] </b></td>
	<td><b>$row_saidas_a[data_vencimento2]</b></td>
	<td align='left'><b><a href='ver_tudo.php?regiao=$regiao&id=16&saida=$row_saidas_a[0]&entradasaida=1' target='_blank'>$row_saidas_a[nome]</a></b></td>
	<td align='left'><b>$row_banco_saida_a[nome] / AG: $row_banco_saida_a[agencia] Conta:$row_banco_saida_a[conta]</b></td>
	<td>R$ $valor_f_a</td>
	<td>$anexo_a</td>
	<td>
	$pagar_imagem_a</a></td>
	<td><a href=ver_tudo.php?id=17&pro=$row_saidas_a[0]&tipo=deletar&tabela=saida&regiao=$regiao>
	<img src=imagensfinanceiro/deletar.gif alt='Deletar' border=0></a></td></tr>";
    
	$soma_a = "$soma_a" + "$valor_final_a";

	}

print "<td colspan=8><hr color='#CC33CC'></td>";

  // MOSTRANDO SAÍDAS DO MES ATUAL NÃO PAGAS ---------------------------------------------

	$result_saidas = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 FROM saida 
	where id_regiao = '$regiao' and status = '1' and data_vencimento >= '$ano-$mes_h-01' 
	and data_vencimento <= '$ano-$mes_q_vem-31' ORDER BY data_vencimento");

	while($row_saidas = mysql_fetch_array($result_saidas)){
	  $result_banco_saida = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas[id_banco]'");
	  $row_banco_saida = mysql_fetch_array($result_banco_saida);
	  
	  if($row_saidas['id_banco'] == "0"){
	  $pagar_imagem = "<a href=edit_saidas.php?idsaida=$row_saidas[0]&tabela=saida&regiao=$regiao>
	  <img src=imagensfinanceiro/editar.gif alt='Editar' border=0>";
	  }else{
	  $pagar_imagem = "<a href=ver_tudo.php?id=17&pro=$row_saidas[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1>
	  <img src=imagensfinanceiro/pagar.gif alt='Pagar' border=0>";
	  }
	  
	  if($row_saidas['comprovante'] == "0"){
	  $anexo = "";
	  }else{
	  $anexo = "<img src=imagensfinanceiro/anexo.gif alt='Anexo'>";
	  }
	  
	  if("20/04/2008" <= "12/04/2008"){
	  $cor = "#FF9598";
	  }else{
	  $cor = "";
	  }
	  

	  $valor1 = "$row_saidas[valor]";
	  $adicional1 = "$row_saidas[adicional]";
	  
	  $valor = str_replace(",", ".", $valor1);
	  $adicional = str_replace(",", ".", $adicional1);
	  
	  $valor_final = $valor + $adicional;
	  
	  $valor_f = number_format($valor_final,2,",",".");

	print "
	<tr class='style13' bgcolor=$cor>
	<td width=30><b>$row_saidas[0] </b></td>
	<td><b>$row_saidas[data_vencimento2]</b></td>
	<td align='left'><b><a href='ver_tudo.php?regiao=$regiao&id=16&saida=$row_saidas[0]&entradasaida=1' target='_blank'>$row_saidas[nome]</a></b></td>
	<td align='left'><b>$row_banco_saida[nome] / AG: $row_banco_saida[agencia] Conta:$row_banco_saida[conta]</b></td>
	<td>R$ $valor_f</td>
	<td>$anexo</td>
	<td>
	$pagar_imagem</a></td>
	<td><a href=ver_tudo.php?id=17&pro=$row_saidas[0]&tipo=deletar&tabela=saida&regiao=$regiao>
	<img src=imagensfinanceiro/deletar.gif alt='Deletar' border=0></a></td></tr>";
    
	$soma = "$soma" + "$valor_final";

	}
	
    $soma_f = number_format($soma,2,",",".");

    print "</table>
		<br>
      </span></td>
  </tr>
  <tr>
    <td height='18' colspan='2' align='center' valign='top' bgcolor='#FFCCFF'>
	<hr color='#CC33CC'>
	<strong>TOTAL 
      DE SA&Iacute;DAS -  

	<span class='style9'>    $mes:    R$ $soma_f </span>"; 
	  
	  ?>
      </strong></span></div></td>
  </tr>
  <tr>
    <td height="18" colspan="2" valign="top"><div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style25">&nbsp;&nbsp;</span><span class="style7"><?php print "<a href='saidacaixinha.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?></span><span class="style25"><img src="imagensfinanceiro/caixa.gif" alt="entrdas" width="25" height="25" align="absmiddle" border="0"/>&nbsp;<span class="style3">CADASTRAR SA&Iacute;DAS DE CAIXA</span></span></font></div></a></div></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top"><div align="left"><span class="style12">&nbsp;&nbsp;&nbsp;&nbsp;<span class="style13">RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS DO CAIXA: </span></span></div></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top" bgcolor="#99CCCC"><span class="style7"><span class="style25">
      <?PHP

	  $soma = "0";
	  
	  print "<table width='100%' color='#99CCCC'>";
	$result_caixa = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 ,date_format(data_proc, '%d/%m/%Y')as data_proc FROM caixa where id_regiao = '$regiao' and status = '1' and 
	data_proc >= '$ano-$mes_h-01'");
	while($row_caixa = mysql_fetch_array($result_caixa)){
	  	  
	  if("20/04/2008" <= "12/04/2008"){
	  $cor = "#FF9598";
	  }else{
	  $cor = "";
	  }
	  

	  $valor12 = "$row_caixa[valor]";
	  $adicional12 = "$row_caixa[adicional]";


	  $valor2 = str_replace(".", "", $valor12);
	  $valor2 = str_replace(",", ".", $valor2);
	  
	  $adicional2 = str_replace(".", "", $adicional12);
	  $adicional2 = str_replace(",", ".", $adicional2);
	  
	  $valor_final2 = $valor2 + $adicional2;
	  
	  $valor_f2 = number_format($valor_final2,2,",",".");
	  $valor2_f = number_format($valor2,2,",",".");

	print "
	<tr class='style13'>
	<td align='left'>
	<b>$row_caixa[data_proc] - Nome: $row_caixa[nome]</b></td>
	<td>Valor: R$ $valor2_f</td>
	<td>Adicional: R$ $adicional2</td>
	</tr>";
    
	$soma2 = "$soma2" + "$valor_final2";

	}
	
    $soma_f2 = number_format($soma2,2,",",".");
	
	$result_caixinha = mysql_query("SELECT saldo FROM caixinha WHERE id_regiao = '$regiao'");
	while($row_caixinha = mysql_fetch_array($result_caixinha)){
	
	$saldo_caixinha = str_replace(",",".", $row_caixinha['saldo']);
	$saldo_caixinha_formatado = number_format($saldo_caixinha,2,",",".");
	$soma_saldo = $soma_saldo + $saldo_caixinha;
	
	}
	$saldo_caixinha = number_format($soma_saldo,2,",",".");
	
	$calculo_caixinha = $soma_saldo - $soma2;
	
	$calculo_caixinha_f = number_format($calculo_caixinha,2,",",".");
    
	print "
	</table>
	<br>
    </span>
	</td>
    </tr>
    <tr>
    
	<td height='18' colspan='2' align='center' valign='top' bgcolor='#99CCCC'>
	<hr color='#418383'>
	<table width='100%'>
	<tr> 
    <td width='50%' bgcolor='#003300'><div align='center' class='style5 style27'>TOTAL DE SA&Iacute;DAS DO CAIXA</div></td>
    <td width='50%' bgcolor='#003300'><div align='center' class='style5 style27'>SALDO DO CAIXA</div></td>
	</tr>
	<tr>
	<td><div class='style9' align='center'><b>R$ $soma_f2</b></div></td>
	<td><div class='style9' align='center'><b>R$ $saldo_caixinha_formatado </b></div></td>
	</tr>
	</table>
	"; 
	  
	  ?>
    </span></span></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="right" valign="top"><div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7"><?php print "<a href='entradas.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?>&nbsp;&nbsp;&nbsp;<img src="imagensfinanceiro/entradas.gif" alt="entrdas" width="25" height="25" align="absmiddle" border="0"/>&nbsp;<span class="style3">CADASTRAR ENTRADA</span><span class="style9"></a></span></span></font></div></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="right" valign="top"><div align="left"><span class="style12">&nbsp;&nbsp;&nbsp;<span class="style13">RELA&Ccedil;&Atilde;O DE ENTRADAS CADASTRADAS POR DATA: </span></span></div></td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top" bgcolor="#CCFFFF"><p><span class="style25">
      <?PHP
$soma2 = "0";
	  
print "<table width='100%'>";
$result_entradas = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 FROM entrada where id_regiao='$regiao' and status='1' ORDER BY data_vencimento");
	while($row_entradas = mysql_fetch_array($result_entradas)){
	  $result_banco_entradas = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_entradas[id_banco]'");
	  $row_banco_entradas = mysql_fetch_array($result_banco_entradas);

	  
	  $valor2 = str_replace(",", ".", $row_entradas['valor']);
	  $adicional2 = str_replace(",", ".", $row_entradas['adicional']);
	  $valor2_f = number_format($valor2,2,",",".");
	  $adicional2_f = number_format($adicional2,2,",",".");

	print "<tr class='style13'><td><b>$row_entradas[data_vencimento2]</b></td><td align='left'><b><a href='ver_tudo.php?regiao=$regiao&id=16&saida=$row_entradas[0]&entradasaida=2' target='_blank'>$row_entradas[nome]</a></b></td><td align='left'><b>$row_banco_entradas[nome] / AG: $row_banco_entradas[agencia] Conta:$row_banco_entradas[conta]</b></td><td>Adi: R$ $adicional2_f </td><td>Valor: R$ $valor2_f</td>	<td>
<a href='ver_tudo.php?id=17&pro=$row_entradas[0]&tipo=pagar&tabela=entrada&regiao=$regiao&idtarefa=2'>
<img src=imagensfinanceiro/pagar.gif alt='Confirmar' border=0></a></td>
<td><a href=ver_tudo.php?id=17&pro=$row_entradas[0]&tipo=deletar&tabela=entrada&regiao=$regiao>
<img src=imagensfinanceiro/deletar.gif alt='Deletar' border=0></a></td></tr>";
    
	$valor_soma2 = $valor2 + $adicional2;
	
	$soma2 = "$soma2" + "$valor_soma2";
	
	}
	print "</table>";
	$soma2_f = number_format($soma2,2,",",".");
	?>
    </span></p>    </td>
  </tr>
  <tr>
    <td height="18" colspan="2" align="center" valign="top" bgcolor="#CCFFFF"> 
    <hr color="#0033CC">
      <span class="style2"><strong><span class="style29">TOTAL DE ENTRADAS - </span> 
      <?php	  print "<span class='style9'>      $mes:    R$ $soma2_f</span>"; ?>
    </strong></span></td>
  </tr>
  
  
  <tr>
    <td height="18" colspan="2" align="right" valign="top">&nbsp;</td>
  </tr>
  

  
  
  <tr>
    <td width="532" height="17" align="right" valign="top" bgcolor="#FFFFFF" class="style3"></td>
    <td width="171" align="center" valign="middle" bgcolor="#FFFFFF" class="style3">
    <a href="financeiro/novofinanceiro.php?regiao=<?=$regiao?>">.</a></td>
  </tr>
   <tr>
    <td height="17" colspan="2" align="right" valign="top" bgcolor="#FFFFFF" class="style3">
    <div align="center">
      <br>
      
      <br>
    </div></td>
  </tr>
  <tr>
    <td height="17" colspan="2" valign="top" bgcolor="#FFFFFF" class="style3"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="49%" bgcolor="#FFFFCC"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style25">&nbsp;&nbsp;</span><span class="style25"><?php print "<a href='cadfornecedores.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?></span><span class="style25"><img src="imagensfinanceiro/cadastrofornecedores.gif" alt="" width="25" height="25" align="absmiddle" border="0">&nbsp;<span class="style5">CADASTRAR FORNECEDOR</span></span></font></td>
          <td width="51%" align="right" bgcolor="#FFFFCC"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style25">&nbsp;&nbsp;</span><span class="style25">
<?php print "<a href='fornecedores.php?regiao=$regiao' style='TEXT-DECORATION: none;'>"; ?></span><span class="style25">&nbsp;<span class="style5">VISUALIZAR FORNECEDORES</span>&nbsp;<img src="imagensfinanceiro/cadastrofornecedores.gif" width="25" height="25" align="absmiddle" border="0"> &nbsp;&nbsp; </span></font></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="20" colspan="2" align="right" valign="top" bgcolor="#FFFFFF" class="style3">&nbsp;</td>
  </tr>
</table>
<div align="center"><img src="layout/baixo.gif" width="750" height="38"><br>
</div>
<?php
include "empresa.php";
$rod = new empresa();
$rod -> rodape();
?>
</body>
</html>
<?php

/* Liberando o resultado */
mysql_free_result($result_saidas);
mysql_free_result($result_entradas);

/* Fechando a conexão */
mysql_close($conn);


?>