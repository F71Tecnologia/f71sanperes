<?php


include "include/restricoes.php";
include "../conn.php";
include "../classes/funcionario.php";

$nFunc = new funcionario();

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
		$link = "../frota/printcombustivel.php?com=$idComb&regiao=$regiao";
	}else{
		mysql_query("UPDATE fr_combustivel SET status_reg = '0', data_libe = '$dataCad', user_libe = '$id_user' WHERE id_combustivel = '$idComb'");
		$link = "novofinanceiro.php?regiao=$regiao";
	}
	
	
	print "<script>
	location.href=\"$link\";
	</script>";
	
	exit;
}

//VARIAVEIS NECESSÁRIAS PARA AS CONSULTAS

$mes2 = date('F');
$dia_h = date('d');
$mes_h = date('m');
$ano = date('Y');
$dtHojeYm = date('Y-m');


#DEFININDO QUANDO FOI O MES PASSADO PROXIMO MES E OUTROS
$mes_passadoano	= date("Y-m", mktime(0,0,0, $mes_h - 3, $dia_h, $ano));
$mes_q_vem 		= date("m", mktime(0,0,0, $mes_h + 1, $dia_h, $ano));
$MesqVemYm 		= date("Y-m", mktime(0,0,0, $mes_h + 1, $dia_h, $ano));
$ano_passado 	= date("Y", mktime(0,0,0, $mes_h , $dia_h, $ano - 1));

$data_hoje = "$dia_h/$mes_h/$ano";
$dia_amanha = "$dia_h" + "1";

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];


//-------------VERIVICANDO AS CONTAS PARA HOJE------------------
$result_jr = mysql_query("SELECT * FROM saida where id_regiao = '$regiao' and status = '1'
and data_vencimento = '$ano-$mes_h-$dia_h' ORDER BY data_vencimento");

$result_banco_jr = mysql_query("SELECT * FROM bancos where id_regiao='$regiao' and saldo LIKE '-%' AND status_reg = '1'");

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

//EMBELEZAMENTO
$bord = "style='border-bottom:#FFF solid 1px;'";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Cache-Control" content="No-Cache">
<meta http-equiv="Pragma"        content="No-Cache">
<meta http-equiv="Expires"       content="0">
<title>::: Intranet :::</title>


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

<script language="javascript">
function abrir(URL,w,h,NOMEZINHO) {
	var width = w;
  	var height = h;
	
	var left = 99;
	var top = 99;

window.open(URL,NOMEZINHO, 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=no');

}

function CorFundo(campo,cor){
	var d = document;
	if(cor == 1){
		var color = "#F2F2E3";
	}else{
		var color = "#FFFFFF";
	}

	d.getElementById(campo).style.background=color;
	
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>

<style type="text/css">
<!--
body {
	font-family:Arial, Helvetica, sans-serif;
}
.menusCima {
	color:#FFF;
	font-size:12px;
	text-decoration:none;
}
.linkMenu {
	text-decoration:none;
	color:#FFF;
}
.titulosTab {
	color:#FFF;
	font-size:10px;
	font-weight:bold;
	border-bottom:#666 solid 1px;
}
.linhaspeq{
	font-size:11px;
}
#apDiv1 {
	/*position:absolute;
	left:4px;
	top:152px;*/
	z-index:2;
	background:#FFF;
	border:solid 1px #000;
	width: auto;
	height: auto;
	/*filter: alpha(opacity = 60);
	opacity:0.60;
	-moz-opacity: 0.60;*/
}
.dragme{position:relative;}
#apDiv2 {
	/*position:absolute;
	left:8px;
	top:35px;*/
	background:#FFF;
	border:solid 1px #000;
	width: auto;
	height: auto;
	z-index:1;
}
#apDiv3 {
	/*position:absolute;
	left:474px;
	top:36px;*/
	background:#FFF;
	border:solid 1px #000;
	width: auto;
	height: auto;
	z-index:3;
}
#apDiv4 {
	z-index:2;
	background:#FFF;
	border:solid 1px #000;
	width: auto;
	height: auto;
}
#apDiv5 {
	z-index:2;
	background:#FFF;
	border:solid 1px #000;
	width: auto;
	height: auto;
}

.style25 {	font-size: 11px;
	font-weight: bold;
}
-->
</style>

<link href="../net1.css" rel="stylesheet" type="text/css" />
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="31" background="imagensfinanceiro/barra1.gif">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="menusCima">
      <tr>
        <td width="172"><div style="margin-left:17px" align="center">
        <a href="javascript:abrir('saidas.php?regiao=<?=$regiao?>','750','550','Saída');" class="linkMenu">Cadastrar Sa&iacute;das</a>
        </div></td>
        <td width="13"><img src="imagensfinanceiro/barra2.gif" width="13" height="31" /></td>
        <td width="173"><div align="center">
        <a href="javascript:abrir('entradas.php?regiao=<?=$regiao?>','750','550','Entrada');" class="linkMenu">Cadastrar Entradas</a>
        </div></td>
        <td width="13"><img src="imagensfinanceiro/barra2.gif" width="13" height="31" /></td>
        <td width="156"><div style="margin-left:5px" align="center">
        <a href="javascript:abrir('login_adm2.php?regiao=<?=$regiao;?>','600','400','Rel');" class="linkMenu">Relat&oacute;rios</a> </div></td>
        <td width="13"><img src="imagensfinanceiro/barra2.gif" width="13" height="31" /></td>
        <td width="203"><div style="margin-left:5px" align="center">
        <a href="javascript:abrir('saidacaixinha.php?regiao=<?=$regiao?>','680','280','Caixa');" class="linkMenu">Cadastrar Sa&iacute;das de Caixa</a></div></td>
        <td width="13"><img src="imagensfinanceiro/barra2.gif" width="13" height="31" /></td>
        <td width="104"><div style="margin-left:5px" align="center"><a href="javascript:abrir('../calculadora/caculadora.html','560','370','Calculadora');" class="linkMenu">Calculadora</a></div></td>
        <td width="13"><img src="imagensfinanceiro/barra2.gif" width="13" height="31" /></td>
        <td width="127"><div style="margin-left:5px" align="center">&nbsp;</div></td>
        <td width="10"><img src="imagensfinanceiro/barra2.gif" width="13" height="31" /></td>
        <td width="74">&nbsp;</td>
        <td width="63">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
   <?php
  		//BLOQUEIO PAULO MONTEIRO SJR 16-03 - 17hs
  		// or $userlog == '27'  or $userlog == '1'
  		if($userlog != '73'){
  		?>
  <tr>
    <td height="192">
   
<?php 
$usuarios_permitidos = array('75','5','9','27', '64');
if(in_array($userlog,$usuarios_permitidos)){
	$qr_id_master = mysql_query("SELECT id_master FROM funcionario WHERE id_funcionario = '$userlog'");
	$id_master = mysql_result($qr_id_master,0);
?>
 <!--Controle de regiao -->
         <div style="margin-left:20px">
        <form name="formRegiao" id="formRegiao" method="get">
        <table  background="imagensfinanceiro/barra3.gif" class="titulosTab">
        	<tr>
            	<td style="font-size:12px">Região</td>
                <td><select name="regiao" onchange="MM_jumpMenu('parent',this,0)">
                <?php
                $qr_selecao_regiao = mysql_query("SELECT * FROM regioes WHERE status = '1' AND id_master = '$id_master'");
				while($row_selecao_regiao = mysql_fetch_assoc($qr_selecao_regiao)){
					if($_GET['regiao'] == $row_selecao_regiao['id_regiao']) {
						print "<option selected=\"selected\" value=\"?regiao=$row_selecao_regiao[id_regiao]\">$row_selecao_regiao[id_regiao] - $row_selecao_regiao[regiao]</option>";
					} else {
						print "<option value=\"?regiao=$row_selecao_regiao[id_regiao]\">$row_selecao_regiao[id_regiao] - $row_selecao_regiao[regiao]</option>";
					}
				}
				?>
                 	</select>
                </td>
               
            </tr>
        </table>
        </form>
        </div>
       <!--Controle de regiao -->
<?php }// Fim do if de controle de regiao ?>

    
    <br /> 
    <table width="97%" border="0" cellspacing="0" cellpadding="0" align="center">
      <tr>
        <td width="39%">
        <!-- INICIO DO CONTROLE DE COMBUSTIVEL -->
       <?php
  		//SOMENTE EUGENIO E SILVANIA PODEM VER CONTROLE DE COMBUSTIVEL
  		// or $userlog == '27'  or $userlog == '1'
  		if($userlog == '27' or $userlog == '28' or $userlog == '5' or $userlog == '1' or $userlog == '65' or $userlog == '9'){
  		?>
       
        <div id="apDiv2" >
          <table width="100%" border="0" cellpadding="0" cellspacing="0"  background="imagensfinanceiro/barra3.gif" class="titulosTab" id="TBcombustivel">
            <tr>
              <td width="70%" height="21">&nbsp;&nbsp;CONTROLE DE COMBUST&Iacute;VEL: </td>
              <td width="51%" align="right"><div style="margin-right:5px; float:right"> <img src="imagensfinanceiro/botao2.gif" border="0" id="BtMaxCombustivel" style="cursor:pointer" 
    onclick="MinMax(this.id,'TabelaCombustivel',2)"/></div>
                <div style="margin-right:5px; float:right"> <img src="imagensfinanceiro/botao1.gif" border="0" id="BtMinCombustivel" style="cursor:pointer" 
    onclick="MinMax(this.id,'TabelaCombustivel',1)"/></div></td>
            </tr>
          </table>
          <span id="FimComb"></span>
          <?php
	
	echo "<table width='100%' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC' id='TabelaCombustivel'>";
	
	
	$REComb = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cad FROM fr_combustivel where status_reg='1'");
	$cont = "0";
	while($RowComb = mysql_fetch_array($REComb)){
		if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		
		
		if($RowComb['funcionario'] == 2){ //FUNCIONARIO EXTERNO ( N&Atilde;O ESTA CADASTRADO NA TABELA FUNCIONARIOS )
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
		
		$NOME = explode(" ",$NOME);
		$codigo = sprintf("%04d",$RowComb['0']);
		
	print "<tr class='linhaspeq' bgcolor=$color>
	<td align='center' $bord>$NOME[0]</td>
	<td align='center' $bord>$RowREG[regiao]</td>
	<td align='center' $bord>$RowComb[destino]</td>
	<td align='center' $bord>$RowComb[data_cad]</td>
	<td align='center' $bord>
	<a href='#' 
	onclick=\"return hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header',headingText: 'Liberar' } )\" 
	class='highslide'> Liberar </a>
	
	
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
			<th align='right'>N&uacute;mero do Vale:</th>
			<td>&nbsp;<input name='vale' type='text' size='20' id='vale' value='$codigo'/>&nbsp;</td>
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
        </div>
		<?php
  		}
  		?>
        
        <!-- FINALIZANDO A DIV DO CONTROLE DE COMBUSTIVEL -->    
        <div>
        </td>
        <td width="61%"><div id="apDiv3" style="margin-left:15px; width:97% ">
          <table width='100%' border='0' cellpadding='0' cellspacing='0'  background='imagensfinanceiro/barra3.gif' class='titulosTab' id='TBreembolso'>
            <tr>
              <td width='55%' height='21'>&nbsp;&nbsp;CONTROLE DE REEMBOLSO: </td>
              <td width='45%' align='right'><div style='margin-right:5px; float:right'>
              <img src='imagensfinanceiro/botao2.gif' border='0' id='BtMaxReembolso' style='cursor:pointer' 
              onclick="MinMax(this.id,'TabelaReembolso',2)"/></div>
              <div style='margin-right:5px; float:right'>
              <img src='imagensfinanceiro/botao1.gif' border='0' id='BtMinReembolso' style='cursor:pointer' 
              onclick="MinMax(this.id,'TabelaReembolso',1)"/></div></td>
            </tr>
          </table>
          
          <span class='style71'>
          
	<table width='100%' border='0' cellspacing='1' cellpadding='0' id='TabelaReembolso'>
<?php
	$REReem = mysql_query("SELECT *,date_format(data, '%d/%m/%Y')as data FROM fr_reembolso WHERE status = '1'");
	
	$cont = '0';
	
	while($RowReem = mysql_fetch_array($REReem)){
	  if($cont % 2){ $color='#f0f0f0'; }else{ $color='#dddddd'; }
	  
	  if($RowReem['funcionario'] == '1'){
	  	$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$RowReem[id_user]'");
	  	$row_user = mysql_fetch_array($result_user);
	  	$NOME = $row_user['nome1'];  
	 
	 }else{
	  	$NOME = $RowReem['nome']; 
	  }
	  
	  $pagar_imagem = '-';	  
	  $codigo = sprintf('%05d',$RowReem['0']);
	  $valor = $RowReem['valor'];	  
	  $valorF = number_format($valor,2,",",".");
	  
	   $link = "<a href='../frota/ver_reembolso.php?id=1&reembolso=$RowReem[0]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\">";
?>
	
	<tr class='linhaspeq' bgcolor=<?=$color?>>
	<td width='5%' align='center' <?=$bord?>><b> <?=$codigo?> </b></td>
	<td width='15%' align='center' <?=$bord?>><b><?=$RowReem['data']?></b></td>
	<td width='60%' align='left'<?=$bord?>><b><?=$NOME?></b></td>
	<td <?=$bord?>>R$ <?=$valorF?></td>
	<td align='center' <?=$bord?>><?=$link?><img src='imagensfinanceiro/editar.gif' alt='Editar' border=0> </a></td>
	<td align='center' <?=$bord?>>&nbsp;</td></tr>
<?php    
	 $soma = $soma + $valor;
	$cont ++;
	}
	
    $soma_f = number_format($soma,2,",",".");
?>
    
	<tr><td height='20' colspan='6' align='center' valign='middle' bgcolor='#CCCCCC'>
	<div style='font-size:14px; font-weight:bold;'>TOTAL DE REEMBOLSO: <span class='style9'>R$  <?=$soma_f?> </span></div>
    </td>
    </tr>
    <?php   unset($soma_f);
	  unset($cont);
	  unset($soma);
	  unset($valor);
	  ?> 
    	 </table> </span></div> 
		
	         
                  </td>
      </tr>
      <tr>
        <td colspan='2'>
    <br />
        <div id='apDiv1'>
          <table width='100%' border='0' cellpadding='0' cellspacing='0'  background='imagensfinanceiro/barra3.gif' class='titulosTab' id='TBsaidas'>
            <tr>
              <td width='70%' height='21'>&nbsp;&nbsp;RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS CADASTRADAS POR DATA: </td>
              <td width='68%' align='right'><div style='margin-right:5px; float:right'> <img src='imagensfinanceiro/botao2.gif' border='0' id='BtMaxSaida' style='cursor:pointer'     onclick="MinMax(this.id,'TabelaSaida',2)"/></div>
                <div style='margin-right:5px; float:right'> <img src='imagensfinanceiro/botao1.gif' border="0" id='BtMinSaida' style='cursor:pointer' 
    onclick="MinMax(this.id,'TabelaSaida',1)"/></div></td>
            </tr>
          </table>
                   
  		          <?PHP
	  $soma = "0";

  // MOSTRANDO SAÍDAS DO MES ANTERIOR NÃO PAGAS ---------------------------------------------

	  
	print "<table width='100%' border='0' cellpadding='0' cellspacing='0' id='TabelaSaida'>";
	
	$result_saidas_a = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2, 
	date_format(data_proc, '%d/%m/%Y - %h:%m:%s')as data_proc FROM saida WHERE id_regiao = '$regiao' AND status = '1' 
	AND data_vencimento BETWEEN '$mes_passadoano-01' AND '$dtHojeYm-00' ORDER BY data_vencimento");
    $row_linhas = mysql_num_rows($result_saidas_a);
	$cont = "0";
	
	while($row_saidas_a = mysql_fetch_array($result_saidas_a)){
	  
	  $result_banco_saida_a = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas_a[id_banco]'");
	  $row_banco_saida_a = mysql_fetch_array($result_banco_saida_a);
	  
	  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
	  
	  if($row_saidas_a['id_banco'] == "0"){
	  $pagar_imagem_a = "<a href=../edit_saidas.php?idsaida=$row_saidas_a[0]&tabela=saida&regiao=$regiao>
	  <img src=imagensfinanceiro/editar.gif alt='Editar' border=0>";
	  }else{
	  $pagar_imagem_a = "<a href=../ver_tudo.php?id=17&pro=$row_saidas_a[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1>
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
	  
	  $nFunc -> MostraUser($row_saidas_a['id_user']);
	  $Nome = $nFunc -> nome1;

	print "
	<tr class='linhaspeq' bgcolor=$color height=20>
	<td width=50 $bord><b>$row_saidas_a[0] </b></td>
	<td $bord><b>$row_saidas_a[data_vencimento2]</b></td>
	<td align='left' $bord><b><a href='../ver_tudo.php?regiao=$regiao&id=16&saida=$row_saidas_a[0]&entradasaida=1' target='_blank'>$row_saidas_a[nome]</a></b></td>
	<td align='left' $bord><b>$row_banco_saida_a[nome] / AG: $row_banco_saida_a[agencia] Conta:$row_banco_saida_a[conta]</b></td>
	<td $bord><b>$Nome</b></td>
	<td $bord><b>$row_saidas[data_proc]</b></td>
	<td $bord>R$ $valor_f_a</td>
	<td $bord>$anexo_a</td>
	<td $bord>
	$pagar_imagem_a</a></td>
	<td $bord><a href=../ver_tudo.php?id=17&pro=$row_saidas_a[0]&tipo=deletar&tabela=saida&regiao=$regiao>
	<img src=imagensfinanceiro/deletar.gif alt='Deletar' border=0></a></td></tr>";
    
	$soma_a = "$soma_a" + "$valor_final_a";
	
	$cont ++;

	}

print "<td colspan=8><hr color='#CCCCCC'></td>";

  // MOSTRANDO SAÍDAS DO MES ATUAL NÃO PAGAS ---------------------------------------------
	
	$result_saidas = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2, date_format(data_proc, '%d/%m/%Y - %h:%m:%s')as 
	data_proc FROM saida WHERE id_regiao = '$regiao' AND status = '1' AND data_vencimento BETWEEN '$dtHojeYm-01' AND '$MesqVemYm-31' ORDER BY 
	data_vencimento");
	
	$cont = "0";
	
	while($row_saidas = mysql_fetch_array($result_saidas)){
	  $result_banco_saida = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas[id_banco]'");
	  $row_banco_saida = mysql_fetch_array($result_banco_saida);
	  
	  if($row_saidas['id_banco'] == "0"){
	  $pagar_imagem = "<a href=../edit_saidas.php?idsaida=$row_saidas[0]&tabela=saida&regiao=$regiao>
	  <img src=imagensfinanceiro/editar.gif alt='Editar' border=0>";
	  }else{
	  $pagar_imagem = "<a href=../ver_tudo.php?id=17&pro=$row_saidas[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1>
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
	  
		if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		
	  $valor1 = "$row_saidas[valor]";
	  $adicional1 = "$row_saidas[adicional]";
	  
	  $valor = str_replace(",", ".", $valor1);
	  $adicional = str_replace(",", ".", $adicional1);
	  
	  $valor_final = $valor + $adicional;
	  
	  $valor_f = number_format($valor_final,2,",",".");
	  
	  $nFunc -> MostraUser($row_saidas['id_user']);
	  $Nome = $nFunc -> nome1;

	print "
	<tr class='linhaspeq' bgcolor=$color height=20>
	<td width=50 $bord><b>$row_saidas[0] </b></td>
	<td $bord><b>$row_saidas[data_vencimento2]</b></td>
	<td align='left' $bord><b><a href='../ver_tudo.php?regiao=$regiao&id=16&saida=$row_saidas[0]&entradasaida=1' target='_blank'>$row_saidas[nome]</a></b></td>
	<td align='left' $bord><b>$row_banco_saida[nome] / AG: $row_banco_saida[agencia] Conta:$row_banco_saida[conta]</b></td>
	<td $bord><b>$Nome</b></td>
	<td $bord><b>$row_saidas[data_proc]</b></td>
	<td $bord>R$ $valor_f</td>
	<td $bord>$anexo</td>
	<td $bord>
	$pagar_imagem</a></td>
	<td $bord><a href=../ver_tudo.php?id=17&pro=$row_saidas[0]&tipo=deletar&tabela=saida&regiao=$regiao>
	<img src=imagensfinanceiro/deletar.gif alt='Deletar' border=0></a></td></tr>";
    
	$soma = "$soma" + "$valor_final";
	$cont ++;

	}
	
    $soma_f = number_format($soma,2,",",".");

    print "	
	<tr>
    <td height='20' colspan='8' align='center' valign='middle' bgcolor=#CCCCCC>
	<div style='font-size:14px; font-weight:bold;'>TOTAL DE SA&Iacute;DAS - $mes: R$ $soma_f </div>
	</td></tr>
	</table>
	"; 
	  unset($soma_f);
	  unset($cont);
	  unset($soma);
	  unset($valor);

	  ?>
          </span></div></td>
        </tr>
      <tr>
        <td colspan="2"><br />
          <div id="apDiv4">
            <table width="100%" border="0" cellpadding="0" cellspacing="0"  background="imagensfinanceiro/barra3.gif" class="titulosTab" id="TBsaidas2">
              <tr>
                <td width="70%" height="21">&nbsp;&nbsp;RELA&Ccedil;&Atilde;O DE ENTRADAS CADASTRADAS POR DATA: </td>
                <td width="68%" align="right"><div style="margin-right:5px; float:right">
                <img src="imagensfinanceiro/botao2.gif" border="0" id="BtMaxEntrada" style="cursor:pointer" 
                onclick="MinMax(this.id,'TabelaEntrada',2)"/>
                </div>
                <div style="margin-right:5px; float:right">
                
                <img src="imagensfinanceiro/botao1.gif" border="0" id="BtMinEntrada" style="cursor:pointer" 
                onclick="MinMax(this.id,'TabelaEntrada',1)"/></div></td>
              </tr>
            </table>
            <span class="style25">
            <?PHP
$soma2 = "0";
	  
print "<table width='100%' border='0' cellpadding='0' cellspacing='0' id='TabelaEntrada'>";
$result_entradas = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2,date_format(data_proc, '%d/%m/%Y - %h:%m:%s')as data_proc FROM entrada where id_regiao='$regiao' and status='1' ORDER BY data_vencimento");
	
	$cont = "0";
	
	while($row_entradas = mysql_fetch_array($result_entradas)){
	  $result_banco_entradas = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_entradas[id_banco]'");
	  $row_banco_entradas = mysql_fetch_array($result_banco_entradas);
		
		if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
	  
	  $valor2 = str_replace(",", ".", $row_entradas['valor']);
	  $adicional2 = str_replace(",", ".", $row_entradas['adicional']);
	  $valor2_f = number_format($valor2,2,",",".");
	  $adicional2_f = number_format($adicional2,2,",",".");
	  
	  $nFunc -> MostraUser($row_entradas['id_user']);
	  $Nome = $nFunc -> nome1;

	print "<tr class='linhaspeq' bgcolor=$color height=20>
	<td $bord><b>$row_entradas[0]</b></td>
	<td $bord><b>$row_entradas[data_vencimento2]</b></td>
	<td align='left' $bord><b><a href='../ver_tudo.php?regiao=$regiao&id=16&saida=$row_entradas[0]&entradasaida=2' target='_blank'>$row_entradas[nome]</a></b>
	</td $bord><td align='left'><b>$row_banco_entradas[nome] / AG: $row_banco_entradas[agencia] Conta:$row_banco_entradas[conta]</b></td>
	<td $bord><b>$Nome</b></td>
	<td $bord><b>$row_entradas[data_proc]</b></td>
	<td $bord>Adi: R$ $adicional2_f </td><td>Valor: R$ $valor2_f</td>
	<td $bord><a href='../ver_tudo.php?id=17&pro=$row_entradas[0]&tipo=pagar&tabela=entrada&regiao=$regiao&idtarefa=2'>
	<img src=imagensfinanceiro/pagar.gif alt='Confirmar' border=0></a></td>
	<td $bord><a href=../ver_tudo.php?id=17&pro=$row_entradas[0]&tipo=deletar&tabela=entrada&regiao=$regiao>
	<img src=imagensfinanceiro/deletar.gif alt='Deletar' border=0></a></td></tr>";
    
	$valor_soma2 = $valor2 + $adicional2;
	
	$soma2 = "$soma2" + "$valor_soma2";
	
	$cont ++;
	
	}
	$soma2_f = number_format($soma2,2,",",".");
	print "<tr>
    <td height='20' colspan='8' align='center' valign='middle' bgcolor=#CCCCCC>
	<div style='font-size:14px; font-weight:bold;'>TOTAL DE SA&Iacute;DAS - $mes: R$ $soma2_f </div>
	</td></tr>
	</table>";
	  unset($soma_f);
	  unset($cont);
	  unset($soma);
	  unset($valor);

	?>
            </span>          </span></div></td>
      </tr>
      
      <tr>
        <td colspan="2"><br />

      	<div id="apDiv5">
            <table width="100%" border="0" cellpadding="0" cellspacing="0"  background="imagensfinanceiro/barra3.gif" class="titulosTab" id="TBCaixinha">
              <tr>
                <td width="70%" height="21">&nbsp;&nbsp;RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS DO CAIXA: </td>
                <td width="68%" align="right"><div style="margin-right:5px; float:right">
                <img src="imagensfinanceiro/botao2.gif" border="0" id="BtMaxCaixinha" style="cursor:pointer" 
                onclick="MinMax(this.id,'TabelaCaixinha',2)"/>
                </div>
                <div style="margin-right:5px; float:right">
                
                <img src="imagensfinanceiro/botao1.gif" border="0" id="BtMinCaixinha" style="cursor:pointer" 
                onclick="MinMax(this.id,'TabelaCaixinha',1)"/></div></td>
              </tr>
            </table>
            
            <?PHP

	  $somaCA = "0";
	  $cont = "";
	  
	print "<table width='100%' border='0' cellpadding='0' cellspacing='0' id='TabelaCaixinha'>";
	$result_caixa = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 ,date_format(data_proc, '%d/%m/%Y')as data_proc 
	FROM caixa where id_regiao = '$regiao' and status = '1' and data_proc >= '$ano-$mes_h-01'");
	
	while($row_caixa = mysql_fetch_array($result_caixa)){
		if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

	  $valorCA = "$row_caixa[valor]";
	  $adicionalCA = "$row_caixa[adicional]";


	  $valorCA = str_replace(".", "", $valorCA);
	  $valorCA = str_replace(",", ".", $valorCA);
	  
	  $adicionalCA = str_replace(".", "", $adicionalCA);
	  $adicionalCA = str_replace(",", ".", $adicionalCA);
	  
	  $valor_finaCA = $valorCA + $adicionalCA;
	  
	  $valor_fCA = number_format($valor_finaCA,2,",",".");
	  $valor2_fCA = number_format($valorCA,2,",",".");

	print "
	<tr class='linhaspeq' bgcolor=$color height=20>
	<td align='left' $bord><b>$row_caixa[data_proc] - Nome: $row_caixa[nome]</b></td>
	<td $bord>Valor: R$ $valor2_fCA</td>
	<td $bord>Adicional: R$ $adicionalCA</td>
	</tr>";
    
	$somaCA = $somaCA + $valor_finaCA;
	$cont ++;
	}
	
    $somaCA_F = number_format($somaCA,2,",",".");
	
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
	
	
    <tr class='linhaspeq' >
	<td height='18' colspan='3' align='center'>
	<table width='100%'>
	<tr> 
    <td width='50%' bgcolor='#CCCCCC'><div align='center' style='color:#000000; font-size:14px'><b>TOTAL DE SA&Iacute;DAS DO CAIXA</b></div></td>
    <td width='50%' bgcolor='#CCCCCC'><div align='center' style='color:#000000; font-size:14px'><b>SALDO DO CAIXA</b></div></td>
	</tr>
	<tr class='linhaspeq' >
	<td><div align='center' style='color:#000000; font-size:14px'><b>R$ $somaCA_F</b></div></td>
	<td><div align='center' style='color:#000000; font-size:14px'><b>R$ $saldo_caixinha_formatado </b></div></td>
	</tr>
	</table>
	</td></tr></table>"; 
	  unset($soma_f);
	  unset($cont);
	  unset($soma);
	  unset($valor);

	  ?>
            </div>
<!-- ////////////////////////////////////// PROVISÂO  //////////////////////////////////////////-->

<!-- ////////////////////////////////////// PROVISÂO  //////////////////////////////////////////--> 
        </td>
      </tr>
    </table></td>
  </tr>
 <?php 
		} 
 ?>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script>
function MinMax(a,b,c){

if(c == 1){
$('#'+a).click(function(){ 
$('#'+b).fadeOut("slow");
})
}else if(c == 2){
$('#'+a).click(function(){ 
$('#'+b).fadeIn("slow");
})
}
}
  
</script>

        </body>
</html>
<?php
/* Fechando a conexão */
mysql_close($conn);
?>