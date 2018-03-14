<?php
include ("include/restricoes.php");
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
$mes_passadoano	= date("Y-m", mktime(0,0,0, $mes_h-30, $dia_h, $ano));
// alterado por jr para 30 29/07/2010 16:49 (valor real é 3)
// Realterado por maikom para $dia_h-30 e não $mes_h-30, 30/07/2010 as 09:36hs
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
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/accordion/jquery.accordion.js"></script>
<script type="text/javascript" src="../jquery/jquery.tools.min.js"></script>
<!-- Datepicker -->
<script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />
<!-- Datepicker -->

<script type="text/javascript">
$().ready(function(){
	$('.ano').parent().next().hide();
	$(".ano").click(function(){
		$(this).parent().next().slideToggle();
		$('.ano').parent().next().hide();
	});
	$('.dataautonomos').parent().next().hide();
	$('.dataautonomos').click(function(){
		$(this).parent().next().slideToggle();
		$('.dataautonomos').parent().next().hide();
	});
	$('a.recisao').click(function(){
		$(this).next().toggle();
	});
	
	$('#btDeletar').click(function(){
		var n = $('.saidas_check:checked').length;
		if(n == 0) return false;
		if(window.confirm('Deseja DELETAR essas saidas?') ){
			$("#Form_saida").attr('action','../novoFinanceiro/actions/apaga.selecao.php');
			$("#Form_saida").submit();
		}
	});
	$('#btDeletar_entrada').click(function(){
		var n = $('.entradas_check:checked').length;
		if(n == 0) return false;
		if(window.confirm('Deseja DELETAR essas entradas?')){
		$('#Form_entrada').attr('action','../novoFinanceiro/actions/apaga.selecao.php');
		$("#Form_entrada").submit();
		}
	});
	
	$('#btPagar_entrada').click(function(){
		var n = $('.entradas_check:checked').length;
		if(n == 0) return false;
		if(window.confirm('Deseja PAGAR essas entradas?')){
		$('#Form_entrada').attr('action','../novoFinanceiro/actions/pagar.selecao.php');
		$("#Form_entrada").submit();
		}
	});
	$('#btPagar').click(function(){
		var n = $('.saidas_check:checked').length;
		if(n == 0) return false;
		if(window.confirm('Deseja PAGAR essas saidas?')){
		$('#Form_saida').attr('action','../novoFinanceiro/actions/pagar.selecao.php');
		$("#Form_saida").submit();
		}
	});
	$('[title]').tooltip({ position: "center left", tipClass: 'bloco'});
	
	$('a.edit_data').click(function(){
		$(this).parent().find('div').toggle();
	});
	$('a.update_date').click(function(){
		dados = {
			id : $(this).attr('alt'),
			data : $(this).prev().val()
		}
		$.post('../novoFinanceiro/actions/editar.data.saida.php',
		dados,
		function(retorno){
			if(retorno == '1'){
				window.location.reload();
			}else if(retorno == '2'){
				alert('Erro...');
			}
		}
		);
		
	});
	
	$('.date').datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true
	});
});
function confirmacao(url,mensagem){
	if(window.confirm(mensagem)){
		location.href = url;
	}
}
</script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
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
<link rel="stylesheet" type="text/css" href="../novoFinanceiro/style/form.css" />
<style type="text/css">
<!--
/* Criado por maikom james usado para o Title */
.bloco {
	display: none;
	font-size:12px;
	color:#333;
	background-color:#FFD2D2;
	padding: 5px;
	border: 1px solid #CCC;
	text-align:center;
}
/* Criado por maikom james usado para o Title */
img {
	border:none;
}
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	margin:0px;
}
table {
	font-weight:normal;
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
.apDiv1 {
	/*position:absolute;
	left:4px;
	top:152px;*/
	z-index:2;
	background:#FFF;
	border:solid 1px #000;	/*filter: alpha(opacity = 60);
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
.style25 {	
	font-size: 11px;
	font-weight: bold;
}
#provisao {
	margin: 5px;
	padding: 5px;
	border: 1px solid #CCC;
}
.provisao {
	padding: 5px;
	color: #000;
	font-weight: bold;
	font-size: 14px;
	font-family: Tahoma, Geneva, sans-serif;
}
.provisionamento {
	padding: 5px;
}
-->
<!-- linhas alternadas MAIKOM 20/08/2010 -->
.linha_um {
 background-color:#f5f5f5;
}
.linha_dois {
 background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
 border-bottom:1px solid #ccc;
}
.Total{
	font-size:12px;
	font-weight: bold;
}
strong{
	font-size:11px;
}
#topo{
	position:fixed;
	top:0px;
	background-color:#FFF;
	z-index:1000;
	width:100%;
}
#conteudo {
	top:80px;
	position: relative;
}
*html #conteudo{
	top:0px;
}

.conteudo-correcao{
	width: 100%;
	width /*\**/: 95%\9;
}

table{ 
	font-size:12px;
}
<!-- -->
</style>
</head>
<body>
<div id="topo">
<?php 
	$query_master = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
	$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
?>
	<table width="100%" >
    	<tr>
    	  <td width="110" rowspan="2">
                  <img src="../imagens/logomaster<?=@mysql_result($query_master,0)?>.gif" width="110" height="79"></td>
              <td align="left" valign="top"><br />
                Data:&nbsp;<strong><?=date("d/m/Y");?></strong>&nbsp;<br />
        voc&ecirc; est&aacute; visualizando a Regi&atilde;o:&nbsp;<strong><?=@mysql_result($query_regiao,0);?></strong></td>
        	<td><br />
        <!--Controle de regiao -->
        <div>
        <form name="formRegiao" id="formRegiao" method="get">
        <table>
        <tr>
            <td><span style="color:#000">Região</span></td>
            <td><select name="regiao" onchange="MM_jumpMenu('parent',this,0)">
            <?php
            $qr_selecao_regiao = mysql_query("SELECT * FROM regioes WHERE status = '1'");
			
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
            </td>
          </tr>
           
    </table>
</div>
<table  border="0" cellspacing="0" cellpadding="0" id="conteudo" class="conteudo-correcao">
  <tr>
    <td height="31" bgcolor="#999999">
    <table width="100%" height="30px" border="0" cellpadding="0" cellspacing="0" class="menusCima">
      <tr>
        <td height="51" align="center" valign="middle" bgcolor="#999999"><div style="margin-left:17px" align="center">
        <a href="javascript:abrir('../novoFinanceiro/cadastrarsaida.php?regiao=<?=$regiao?>','750','550','Saída');" class="linkMenu">Cadastrar Sa&iacute;das</a>
        </div></td>
        <td align="center" valign="middle" bgcolor="#999999"><img src="imagensfinanceiro/separador.jpg" width="3" height="27" /></td>
        <td align="center" valign="middle" bgcolor="#999999"><div align="center">
        <a href="javascript:abrir('entradas.php?regiao=<?=$regiao?>','750','550','Entrada');" class="linkMenu">Cadastrar Entradas</a>
        </div></td>
        <td align="center" valign="middle" bgcolor="#999999"><img src="imagensfinanceiro/separador.jpg" width="3" height="27" /></td>
        <td align="center" valign="middle" bgcolor="#999999"><div style="margin-left:5px" align="center">
        <a href="javascript:abrir('login_adm2.php?regiao=<?=$regiao;?>','600','400','Rel');" class="linkMenu">Relat&oacute;rios</a> </div></td>
        <td align="center" valign="middle" bgcolor="#999999"><img src="imagensfinanceiro/separador.jpg" alt="" width="3" height="27" /></td>
        <td align="center" valign="middle" bgcolor="#999999"><div style="margin-left:5px" align="center">
        <a href="javascript:abrir('saidacaixinha.php?regiao=<?=$regiao?>','680','280','Caixa');" class="linkMenu">Cadastrar Sa&iacute;das de Caixa</a></div></td>
        <td align="center" valign="middle" bgcolor="#999999"><img src="imagensfinanceiro/separador.jpg" alt="" width="3" height="27" /></td>
        <td align="center" valign="middle" bgcolor="#999999"><div style="margin-left:5px" align="center"><a href="javascript:abrir('../calculadora/caculadora.html','560','370','Calculadora');"  class="linkMenu">Calculadora</a></div></td>
        <td align="center" valign="middle" bgcolor="#999999"><img src="imagensfinanceiro/separador.jpg" alt="" width="3" height="27" /></td>
        <?php
		// permissao da provisão
		$permissao = array('5','27','9','75','77','64');
		if(in_array($userlog,$permissao)){
		?>      
        <td align="center" valign="middle" bgcolor="#999999"><div style="margin-left:5px" align="center"><a href="javascript:abrir('cadastro.provisao.php?regiao=<?=$regiao?>','700','500','Cadastro de provisão')" class="linkMenu">Cadastro de provisão</a></div></td>
        <?php }?>
        </tr>
    </table>
    </td>
  </tr>
  
   <?php
  		//BLOQUEIO PAULO MONTEIRO SJR 16-03 - 17hs
  		// or $userlog == '27'  or $userlog == '1'
  		if($userlog != '73'){
  		?>
  <tr>
  <td height="192"><br />
    <table width="97%" border="0" cellspacing="0" cellpadding="0" align="center">
      <tr>
        <td valign="top">
        <!-- INICIO DO CONTROLE DE COMBUSTIVEL -->
      <?php
  		//SOMENTE EUGENIO E SILVANIA PODEM VER CONTROLE DE COMBUSTIVEL
		// or $userlog == '27'  or $userlog == '1'
  		if($userlog == '27' or $userlog == '52' or $userlog == '5' or $userlog == '1' or $userlog == '65' or $userlog == '9' or $userlog == '64' or $userlog == '77' or $userlog =='75'){
  		?>
        <div id="apDiv2" >
          <table width="100%" border="0" cellpadding="0" cellspacing="0"  background="imagensfinanceiro/barra3.gif" class="titulosTab" id="TBcombustivel">
            <tr>
              <td height="21" bgcolor="#999999">&nbsp;&nbsp;CONTROLE DE COMBUST&Iacute;VEL:</td>
              </tr>
          </table>
          <span id="FimComb"></span>
          <?php
	echo "<table width='100%' border='0' cellspacing='1' cellpadding='0' bgcolor='#CCCCCC' id='TabelaCombustivel'>";
	$REComb = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cad FROM fr_combustivel where status_reg='1'");
	$cont = "0";
	while($RowComb = mysql_fetch_array($REComb)){
		if($cont % 2){ $color="#FFFFFF"; }else{ $color="#EEEEEE"; }
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
	<td align='center' >$NOME[0]</td>
	<td align='center' >$RowREG[regiao]</td>
	<td align='center' >$RowComb[destino]</td>
	<td align='center' >$RowComb[data_cad]</td>
	<td align='center' >
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
        <!-- TOTALIZADOR -->
	<?php
    $users = array('75','9','27','5','64','77','89'); // filtro de usuarios
    if(in_array($userlog,$users)):?>
    <div id="apDiv2" >
      <table width="100%" border="0" cellpadding="1" cellspacing="1"  class="titulosTab" id="TBcombustivel">
        <tr>
          <td width="70%" height="21" bgcolor="#999999">&nbsp;&nbsp;CONTAS VENCIDAS:</td>
        </tr>
      </table>
          
	<table width='100%' border='0' cellspacing='1' cellpadding='3'  id='TabelaCombustivel'>
	<tr>
	<td><strong>Regi&atilde;o</strong></td>
    <td align="center"><strong>Proximas</strong></td>
	<td align="center"><strong>Hoje</strong></td>
	<td align="center"><strong>Vencidas</strong></td>
	<td >&nbsp;</td>
	</tr>
    <?php 
	$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '1' AND id_master = '1'");
	while($row_regioes = mysql_fetch_assoc($qr_regioes)):
		$qr_cont_hoje = mysql_query("SELECT * 
								FROM saida
								WHERE id_regiao =  '$row_regioes[id_regiao]'
								AND STATUS =  '1'
								AND data_vencimento =  CURDATE()");
		$qr_cont_vencidas = mysql_query("SELECT * 
								FROM saida
								WHERE id_regiao =  '$row_regioes[id_regiao]'
								AND STATUS =  '1'
								AND data_vencimento < CURDATE()
								AND data_vencimento != '0000-00-00'
								AND YEAR(data_vencimento) = '".date('Y')."'
								");
		$qr_cont_avencer = mysql_query("SELECT * 
								FROM saida
								WHERE id_regiao =  '$row_regioes[id_regiao]'
								AND STATUS =  '1'
								AND data_vencimento > CURDATE()
							");
		$num_hoje = mysql_num_rows($qr_cont_hoje);
		$num_vencimento = mysql_num_rows($qr_cont_vencidas);
		$num_avencer = mysql_num_rows($qr_cont_avencer);
	if(!empty($num_hoje) or !empty($num_vencimento) or !empty($num_avencer)):?>
    <tr  class="linha_<?php if($linha++%2==0) { echo 'dois'; } else { echo 'um'; } ?>">
    	<td class="linhaspeq"><?=$row_regioes['id_regiao'].' - '.$row_regioes['regiao']?></td>
        <td align="center" class="linhaspeq"><?=$num_avencer?></td>
        <td align="center" class="linhaspeq"><?=$num_hoje?></td>
        <td align="center" class="linhaspeq"><?=$num_vencimento?></td>
        <td align="center" class="linhaspeq"><a href="?regiao=<?=$row_regioes['id_regiao']?>">ver</a></td>
    </tr>    
    <?php endif;?>
    <?php endwhile;?>
	</table>
    </div>
        <?php endif;?>
        <!-- TOTALIZADOR -->  
        <div>
        </td>
        <td width="61%" valign="top">
          <table width='100%' border='0' cellpadding='1' cellspacing='1' class='titulosTab' id='TBreembolso'  style="margin-left:15px;">
            <tr>
              <td width='55%' height='21' bgcolor="#999999">&nbsp;&nbsp;CONTROLE DE REEMBOLSO: </td>
            </tr>
          </table>

			<table width='100%' border='0' cellpadding='3' cellspacing='1' style="margin-left:15px;">
<?php
	$REReem = mysql_query("SELECT *,date_format(data, '%d/%m/%Y %H:%i:%s')as data FROM fr_reembolso WHERE status = '1'");
	$cont = '0';
	while($RowReem = mysql_fetch_array($REReem)):
	  if($cont % 2){ $color='#FFFFFF'; }else{ $color='#EEEEEE'; }
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
	  $link = "<a href='../frota/ver_reembolso.php?id=1&reembolso=$RowReem[0]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" title=\"Confirmar reembolso\">";
?>
            <tr class="linha_<?php if($linha++%2==0) { echo 'dois'; } else { echo 'um'; } ?>">
            <td width='5%' align='center' class="linhaspeq"><?=$codigo?></td>
            <td width='15%' class="linhaspeq"align='center'><?=$RowReem['data']?></td>
            <td width='60%' class="linhaspeq"align='left'><?=$NOME?></td>
            <td class="linhaspeq" ><b>R$ <?=$valorF?></b></td>
            <td class="linhaspeq" align='center' ><?=$link?><img src='imagensfinanceiro/checked.png' alt='Editar' width="16" height="16" border=0> </a></td>
            </tr>
<?php    
		$soma = $soma + $valor;
		$cont ++;
	endwhile;
    $soma_f = number_format($soma,2,",",".");
?>
	<tr>
    <td colspan='3' align="right">
    	<span class="Total">TOTAL DE REEMBOLSO: </span>
    </td>
    <td>
    <span class="Total">
		R$  <?=$soma_f?>
    </span>
    </td>
    <td colspan='3'>
    </td>
    </tr>
    <?php   
	unset($soma_f,$cont,$soma,$valor);
	?> 
   </table>
   </td>
      </tr>
      <tr>
        <td colspan='2'>
    <br />
<form action="" name="Form_saida" id="Form_saida" method="post" >
        <div class='apDiv1'>
          <table width='100%' border='0' cellpadding='0' cellspacing='0'  background='imagensfinanceiro/barra3.gif' class='titulosTab' id='TBsaidas'>
            <tr>
              <td width='70%' height='21' bgcolor="#999999">&nbsp;&nbsp;RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS CADASTRADAS POR DATA: </td>
              <td width='68%' align='right' bgcolor="#999999">
              <input type="hidden" name="logado" id="logado" value="<?= $userlog ?>" />
              <input type="hidden" name="idtarefa" id="idtarefa" value="1" />
              </td>
            </tr>
          </table>
  <?php
	  $soma = "0";
  	// MOSTRANDO SAÍDAS DO MES ANTERIOR NÃO PAGAS ---------------------------------------------
	print "<table width='100%' border='0'  cellspacing='1' cellpadding='3' id='TabelaSaida'>";
	$result_saidas_a = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2, 
	date_format(data_proc, '%d/%m/%Y - %h:%m:%s')as data_proc FROM saida WHERE id_regiao = '$regiao' AND status = '1' 
	AND data_vencimento BETWEEN '$mes_passadoano-01' AND '$dtHojeYm-00' ORDER BY data_vencimento");
    $row_linhas = mysql_num_rows($result_saidas_a);
	$cont = "0";
	while($row_saidas_a = mysql_fetch_array($result_saidas_a)){
	  $result_banco_saida_a = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas_a[id_banco]'");
	  $row_banco_saida_a = mysql_fetch_array($result_banco_saida_a);
	  if($cont % 2){ $color="#EEEEEE"; }else{ $color="#FFFFFF"; }
	  if($row_saidas_a['id_banco'] == "0"){
	  $pagar_imagem_a = "<a onclick=\"confirmacao(this.href,'Deseja PAGAR esta conta?')\" title=\"Pagar\" href=../edit_saidas.php?idsaida=$row_saidas_a[0]&tabela=saida&regiao=$regiao>
	  <img src=imagensfinanceiro/editar.gif alt='Editar' border=0>";
	  }else{
	  $pagar_imagem_a = "<a onclick=\"confirmacao(this.href,'Deseja PAGAR esta conta?')\" title=\"Pagar\" href=../ver_tudo.php?id=17&pro=$row_saidas_a[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1>
	  <img src=imagensfinanceiro/Money-32.png alt='Pagar' border=0>";
	  }
	  if($row_saidas_a['comprovante'] == "0"){
	  $anexo_a = "";
	  }else{
	  $anexo_a = "<img src=\"imagensfinanceiro/attach-32.png\" alt='Anexo'>";
	  }
	  $valor1_a = "$row_saidas_a[valor]";
	  $adicional1_a = "$row_saidas_a[adicional]";
	  $valor_a = str_replace(",", ".", $valor1_a);
	  $adicional_a = str_replace(",", ".", $adicional1_a);
	  $valor_final_a = $valor_a + $adicional_a;
	  $valor_f_a = number_format($valor_final_a,2,",",".");
	  $nFunc -> MostraUser($row_saidas_a['id_user']);
	  $Nome = $nFunc -> nome1;
	 if($linha++%2==0){
	 	$class = 'dois';
	 }else{
	 	$class = 'um';
	 }
	print "
	<tr class='linha_$class'>
	<td class='linhaspeq' align='center'><input type='checkbox' name='saidas[]' class='saidas_check'  value='$row_saidas_a[0]'  /></td>
	<td class='linhaspeq' width=50 >$row_saidas_a[0] </td>
	<td class='linhaspeq'>$row_saidas_a[data_vencimento2]
	<a href=\"#\" class=\"edit_data\" onclick=\"return false\"><img src=\"../novoFinanceiro/image/editar.gif\" width=\"16\" height=\"16\" ></a>
	<div style=\"display:none\">
		<input type=\"text\" name=\"data\" class=\"date\">
		<a href=\"#\" alt=\"$row_saidas_a[0]\" onclick=\"return false\" class=\"update_date\">
		<img src=\"../novoFinanceiro/image/salvar.gif\" width=\"16\" height=\"16\" >
		</a>
	</div>
	</td>
	<td align='left' class='linhaspeq'> $anexo_a <a href='../ver_tudo.php?regiao=$regiao&id=16&saida=$row_saidas_a[0]&entradasaida=1' target='_blank'>$row_saidas_a[nome]</a>
		<a title=\"Detalhes\" href='../novoFinanceiro/view/detalhes.saidas.php?ID=$row_saidas_a[0]&tipo=saida' onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-ajax', wrapperClassName: 'highslide-white', outlineType: 'rounded-white', outlineWhileAnimating: true, objectType: 'ajax', preserveContent: true} )\">
	<img src=\"../novoFinanceiro/image/seta.gif\" >
	</a>
	</td>
	<td class='linhaspeq' align='left' >$row_banco_saida_a[id_banco] -  $row_banco_saida_a[nome] / AG: $row_banco_saida_a[agencia] Conta:$row_banco_saida_a[conta]</td>
	<td class='linhaspeq' ><b>R$ $valor_f_a</b></td>
	<td class='linhaspeq' align=\"right\">$pagar_imagem_a</a></td>
	<td class='linhaspeq' align='right'><a onclick=\"confirmacao(this.href,'Deseja DELETAR esta conta?')\" title=\"Deletar\" href=../ver_tudo.php?id=17&pro=$row_saidas_a[0]&tipo=deletar&tabela=saida&regiao=$regiao>
	<img src=imagensfinanceiro/Delete-32.png alt='Deletar' border=0 ></a></td></tr>";
	$soma_a = "$soma_a" + "$valor_final_a";
	$cont ++;
	}
print "<td class='linhaspeq' colspan=10><hr color='#CCCCCC'></td>";
  // MOSTRANDO SAÍDAS DO MES ATUAL NÃO PAGAS ---------------------------------------------
	$result_saidas = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2, date_format(data_proc, '%d/%m/%Y - %h:%m:%s')as 
	data_proc FROM saida WHERE id_regiao = '$regiao' AND status = '1' AND data_vencimento BETWEEN '$dtHojeYm-01' AND '$MesqVemYm-31' ORDER BY 
	data_vencimento");
	$cont = "0";
	while($row_saidas = mysql_fetch_array($result_saidas)){
	  $result_banco_saida = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_saidas[id_banco]'");
	  $row_banco_saida = mysql_fetch_array($result_banco_saida);
	  if($row_saidas['id_banco'] == "0"){
	  $pagar_imagem = "<a onclick=\"confirmacao('../edit_saidas.php?idsaida=$row_saidas[0]&tabela=saida&regiao=$regiao','Deseja PAGAR esta conta?')\" title=\"Pagar\" href='#'>
	  <img src=imagensfinanceiro/editar.gif alt='Editar' border=0>";
	  }else{
	  $pagar_imagem = "<a onclick=\"confirmacao('../ver_tudo.php?id=17&pro=$row_saidas[0]&tipo=pagar&tabela=saida&regiao=$regiao&idtarefa=1','Deseja PAGAR esta conta?')\" title=\"Pagar\" href='#'>
	  <img src=\"imagensfinanceiro/Money-32.png\" alt='Pagar' border=0>";
	  }
	  if($row_saidas['comprovante'] == "0"){
	  $anexo = "";
	  }else{
	  $anexo = "<img src=\"imagensfinanceiro/attach-32.png\" alt='Anexo'>";
	  }
	  if("20/04/2008" <= "12/04/2008"){
	  $cor = "#FF9598";
	  }else{
	  $cor = "";
	  }
	  if($cont % 2){ $color="#FFFFFF"; }else{ $color="#EEEEEE"; }
	  $valor1 = "$row_saidas[valor]";
	  $adicional1 = "$row_saidas[adicional]";
	  $valor = str_replace(",", ".", $valor1);
	  $adicional = str_replace(",", ".", $adicional1);
	  $valor_final = $valor + $adicional;
	  $valor_f = number_format($valor_final,2,",",".");
	  $nFunc -> MostraUser($row_saidas['id_user']);
	  $Nome = $nFunc -> nome1;
	   if($linha++%2==0){
	 	$class = 'dois';
	 }else{
	 	$class = 'um';
	 }
	?>
	<?php	
    print "<tr class='linha_$class' height=20>
	<td align='center' class='linhaspeq'><input  type='checkbox' name='saidas[]' class='saidas_check' value='$row_saidas[0]'  /></td>
	<td class='linhaspeq' width=50>
			$row_saidas[0]
	</td>
	<td class='linhaspeq' >
	$row_saidas[data_vencimento2]
	<a href=\"#\" class=\"edit_data\" onclick=\"return false\"><img src=\"../novoFinanceiro/image/editar.gif\" width=\"16\" height=\"16\" ></a>
	<div style=\"display:none\">
		<input type=\"text\" name=\"data\" class=\"date\">
		<a href=\"#\" alt=\"$row_saidas[0]\" onclick=\"return false\" class=\"update_date\"><img src=\"../novoFinanceiro/image/salvar.gif\" width=\"16\" height=\"16\" ></a>
	</div>
	</td>
	<td class='linhaspeq' align='left' >$anexo <a href='../ver_tudo.php?regiao=$regiao&id=16&saida=$row_saidas[0]&entradasaida=1' target='_blank'>$row_saidas[nome]</a> 		
	<a title=\"Detalhes\" href='../novoFinanceiro/view/detalhes.saidas.php?ID=$row_saidas[0]&tipo=saida' onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-ajax', wrapperClassName: 'highslide-white', outlineType: 'rounded-white', outlineWhileAnimating: true, objectType: 'ajax', preserveContent: true} )\">
	<img src=\"../novoFinanceiro/image/seta.gif\" >
	</a>
	</td>
	<td class='linhaspeq' align='left' >$row_banco_saida[id_banco] - $row_banco_saida[nome] / AG: $row_banco_saida[agencia] Conta:$row_banco_saida[conta]</td>
	<td class='linhaspeq' ><b>R$ $valor_f </b></td>
	<td class='linhaspeq' align=\"right\">$pagar_imagem</a></td>
	<td class='linhaspeq' align='right'><a onclick=\"confirmacao('../ver_tudo.php?id=17&pro=$row_saidas[0]&tipo=deletar&tabela=saida&regiao=$regiao','Deseja DELETAR esta conta?')\" title=\"Deletar\" href='#'>
	<img src=\"imagensfinanceiro/Delete-32.png\" alt='Deletar' border=0 ></a></td></tr>";
	$soma = "$soma" + "$valor_final";
	$cont ++;
	
	
	
	
	}
    $soma_f = number_format($soma,2,",",".");
	
	
    print "	
	<tr>
    <td height='20' colspan='5' align='right' >
	<span class='Total'>TOTAL DE SA&Iacute;DAS - $mes:</span>
	</td>
	<td>R$ $soma_f </td>
	<td colspan='5'></td>
	</tr>";
	print "
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td >&nbsp;</td>
		<td align=\"right\">
			<table cellpadding=\"5\">
				<tr align='left'>
					<td><a href=\"#\" onclick=\"javascript:$('.saidas_check').attr('checked','checked'); return false;\" >Marcar todos</a></td>
					<td align='right'><a href=\"#\"  onclick=\"javascript:$('.saidas_check').attr('checked',''); return false;\">Desmarcar todos</a></td>
				</tr>
			</table>
		</td>
		<td >
		<table cellpadding=\"5\">
			<tr>
				<td>
					<a id='btPagar' title=\"Pagar selecionados\" href='#' onclick='return false' class='informa'>
						<img src=\"imagensfinanceiro/Money-32.png\" alt='Pagar' border=0>
					</a>
				</td>
				
				<td>
					<a id='btDeletar' title=\"Deletar selecionados\" href='#' onclick='return false' class='informa'>
						<img src=imagensfinanceiro/Delete-32.png alt='Deletar' border=0>
					</a>
				</td>
			</tr>
		</table>
		</td>
	</tr>";
	print "</table>";
	?>
	<?php  
	  unset($soma_f,$cont,$soma,$valor);
	?>
      </span>
      </div>
      </form>
      </td>
        </tr>
      <tr>
        <td colspan="2">
       	  <br />
          <form method="post" name="Form_entrada" id="Form_entrada" action="">
            <table width="100%" border="0" cellpadding="0" cellspacing="0"  class="titulosTab" id="TBsaidas2">
              <tr>
                <td width="70%" height="21" bgcolor="#999999">&nbsp;&nbsp;RELA&Ccedil;&Atilde;O DE ENTRADAS CADASTRADAS POR DATA: </td>
                <td width="68%" align="right" bgcolor="#999999"><div style="margin-right:5px; float:right"></div>
                <div style="margin-right:5px; float:right"></div>
                
                <input type="hidden" name="idtarefa" id="idtarefa" value="2" />
              	<input type="hidden" name="logado" id="logado" value="<?= $userlog ?>" />
                </td>
              </tr>
            </table>
            <span class="style25">
<?php
$soma2 = "0";
print "<table width='100%' border='0'  cellspacing='1' cellpadding='3' id='TabelaEntrada'>";
$result_entradas = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2,date_format(data_proc, '%d/%m/%Y - %h:%m:%s')as data_proc FROM entrada where id_regiao='$regiao' and status='1' ORDER BY data_vencimento");
	$cont = "0";
	while($row_entradas = mysql_fetch_array($result_entradas)){
	  $result_banco_entradas = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_entradas[id_banco]'");
	  $row_banco_entradas = mysql_fetch_array($result_banco_entradas);
	  $valor2 = str_replace(",", ".", $row_entradas['valor']);
	  $adicional2 = str_replace(",", ".", $row_entradas['adicional']);
	  $valor2_f = number_format($valor2,2,",",".");
	  $adicional2_f = number_format($adicional2,2,",",".");
	  $nFunc -> MostraUser($row_entradas['id_user']);
	  $Nome = $nFunc -> nome1; 
	 
	 if($linha++%2==0) { 
	 		$class =  'dois'; 
		} else { 
			$class = 'um';
		}
	print "<tr class='linha_$class' >";
	print "<td class='linhaspeq' align=\"center\"><input type=\"checkbox\" name=\"entradas[]\" class=\"entrada_check\" value=\"$row_entradas[0]\" /></td>";
	print "<td class='linhaspeq' >$row_entradas[0]</td>
	<td class='linhaspeq' >$row_entradas[data_vencimento2]</td>
	<td class='linhaspeq'align='left' >
	<a href='../ver_tudo.php?regiao=$regiao&id=16&saida=$row_entradas[0]&entradasaida=2' target='_blank'>$row_entradas[nome]</a>
	<a title=\"Detalhes\" href='../novoFinanceiro/view/detalhes.saidas.php?ID=$row_entradas[0]&tipo=entrada' onclick=\"return hs.htmlExpand(this, { contentId: 'highslide-html-ajax', wrapperClassName: 'highslide-white', outlineType: 'rounded-white', outlineWhileAnimating: true, objectType: 'ajax', preserveContent: true} )\">
	<img src=\"../novoFinanceiro/image/seta.gif\" >
	</a>
	</td>
	<td align='left' class='linhaspeq'>$row_banco_entradas[nome] / AG: $row_banco_entradas[agencia] Conta:$row_banco_entradas[conta]</td>
	<td class='linhaspeq'>R$ $adicional2_f </td><td>R$ $valor2_f</td>
	<td class='linhaspeq' align=\"right\"><a title=\"Pagar\" onclick=\"confirmacao('../ver_tudo.php?id=17&pro=$row_entradas[0]&tipo=pagar&tabela=entrada&regiao=$regiao&idtarefa=2','Deseja PAGAR esta entrada?')\" href='#'>
	<img src=imagensfinanceiro/Money-32.png alt='Confirmar' border=0></a></td>
	<td class='linhaspeq' align='right'><a title=\"Deletar\" onclick=\"confirmacao('../ver_tudo.php?id=17&pro=$row_entradas[0]&tipo=deletar&tabela=entrada&regiao=$regiao','Deseja DELETAR esta entrada?')\" href='#'>
	<img src=imagensfinanceiro/Delete-32.png alt='Deletar' border=0 ></a></td></tr>";
	$valor_soma2 = $valor2 + $adicional2;
	$soma2 = "$soma2" + "$valor_soma2";
	$cont ++;
	}
	$soma2_f = number_format($soma2,2,",",".");
	print "
	<tr>
	<td colspan='6' align='right'><span class='Total'>TOTAL DE ENTRADAS - $mes:</span></td>
	<td > <span class='Total'>R$ $soma2_f</span> </td>
	<td colspan='3'></td>
	</tr>";
	print "
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=\"right\">
			<table cellpadding=\"5\">
				<tr>
					<td align='left'>
						<a href=\"#\" onclick=\"javascript:$('.entrada_check').attr('checked','checked');return false;\" onclick=\"return false\">
							Marcar todos
						</a>
					</td>
					<td align='right'>
						<a href=\"#\"  onclick=\"javascript:$('.entrada_check').attr('checked','');return false;\">
							Desmarcar todos
						</a>
					</td>
				</tr>
			</table>
		</td>
		<td>
		<table cellpadding=\"5\">
			<tr>
				<td>
					<a id='btPagar_entrada'  title=\"Pagar selecionados\" href='#' class='informa' onclick='return false'>
						<img src=\"imagensfinanceiro/Money-32.png\" alt='Pagar' border=0>
					</a>
				</td>
				<td>
					<a id='btDeletar_entrada'  title=\"Deletar selecionados\" href='#'  class='informa' onclick='return false'>
						<img src=imagensfinanceiro/Delete-32.png alt='Deletar' border=0>
					</a>
				</td>
			</tr>
		</table>
		</td>
	</tr>		
	";
	print "</table>";
	  unset($soma_f,$cont,$soma,$valor);
?>
            </span>
            </span>
            
            </form>
            </td>
      </tr>
      <tr>
        <td colspan="2"><br />
     <!-- CAIXINHA-->
           <table width="100%" border="0" cellpadding="0" cellspacing="0"  class="titulosTab" id="TBCaixinha">
              <tr>
                <td width="70%" height="21" bgcolor="#999999">&nbsp;&nbsp;RELA&Ccedil;&Atilde;O DE SA&Iacute;DAS DO CAIXA: </td>
                <td width="68%" align="right" bgcolor="#999999"><div style="margin-right:5px; float:right"></div>
                <div style="margin-right:5px; float:right"></div></td>
              </tr>
            </table>
            <?php
	  $somaCA = "0";
	  $cont = "";
	print "<table width='100%' border='0' cellpadding='0' cellspacing='0' id='TabelaCaixinha'>";
	$result_caixa = mysql_query("SELECT *,date_format(data_vencimento, '%d/%m/%Y')as data_vencimento2 ,date_format(data_proc, '%d/%m/%Y')as data_proc 
	FROM caixa where id_regiao = '$regiao' and status = '1' and data_proc >= '$ano-$mes_h-01'");
	while($row_caixa = mysql_fetch_array($result_caixa)){
		if($cont % 2){ $color="#FFFFFF"; }else{ $color="#EEEEEE"; }
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
	<td align='left' class='linhaspeq' >$row_caixa[data_proc] - Nome: $row_caixa[nome]</td>
	<td class='linhaspeq' ><b>R$ $valor2_fCA<b></td>
	<td class='linhaspeq'><b>R$ $adicionalCA</b></td>
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
	<td class='linhaspeq'><div align='center' style='color:#000000; font-size:14px'><b>R$ $somaCA_F</b></div></td>
	<td class='linhaspeq'><div align='center' style='color:#000000; font-size:14px'><b>R$ $saldo_caixinha_formatado </b></div></td>
	</tr>
	</table>
	</td></tr></table>"; 
	  unset($soma_f);
	  unset($cont);
	  unset($soma);
	  unset($valor);
	  ?>
      <!-- FIM CAIXINHA -->
		</td>
	</tr>
</table>
 <?php }  ?>
  <tr>
    <td>&nbsp;<br /><br /></td>
  </tr>
</table>
</body>
</html>
<?php
/* Fechando a conexão */
mysql_close($conn);
?>