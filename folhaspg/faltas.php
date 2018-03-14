<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../funcoes.php";

//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$autonomo = $decript[0];
$tela = $decript[1];
$folha = $decript[2];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

//SELECIONANDO A FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

//SELECIONANDO OS CLTS JA CADASTRADOS NA TAB FOLHA_PROC QUE ESTEJAM COM STATUS 2 = SELECIONADO ANTERIORMENTE
if($row_folha['contratacao'] == 1){
	$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo where id_folha_pro = '$autonomo' and status = '2' ORDER BY nome ASC");
	$row = mysql_fetch_array($result_folha_pro);
	$tr = "";
	$tipo = "Faltas";
}else{
	$result_folha_pro = mysql_query("SELECT * FROM folha_cooperado where id_folha_pro = '$autonomo' and status = '2' ORDER BY nome ASC");
	$row = mysql_fetch_array($result_folha_pro);
	
	$RE_p = mysql_query("SELECT inss,tipo_inss FROM autonomo WHERE id_autonomo = '$row[id_autonomo]'");
	$Row_p = mysql_fetch_array($RE_p);
	
	//TIPO DE RECOLHIMENTO INSS
	if($Row_p['tipo_inss'] == "1"){ $tipoINSS1 = "selected"; }else{ $tipoINSS2 = "selected";}
	
	$tipo = "Horas Trabalhadas";
	
	$tr = "<tr>
    <td height=\"25\" align=\"right\" valign=\"middle\" class=\"Texto10Azul\">INSS</td>
    <td height=\"25\" align=\"left\" valign=\"middle\" class=\"Texto10Azul\">&nbsp;
<input name='inss' type='text' id='inss' size='4' class='campotexto' maxlength=\"13\" value='$Row_p[inss]'
onfocus=\"this.style.background='#CCFFCC'\"  onblur=\"this.style.background='#FFFFFF'; INSS();\"  
 style='background:#FFFFFF;' /> % &nbsp;&nbsp;(valor máximo 11%)</td> </tr>
 
    <tr>
    <td height=\"25\" align=\"right\" valign=\"middle\" class=\"Texto10Azul\">Tipo Recolhimento</td>
    <td height=\"25\" align=\"left\" valign=\"middle\" class=\"Texto10Azul\">&nbsp;
	 <select name='tipo_inss' class='campotexto' id='tipo_inss'>
        <option value='1' $tipoINSS1>VALOR FIXO</option>
        <option value='2' $tipoINSS2>VALOR PERCENTUAL</option>
      </select>
	
	</td> </tr>
 
 ";
}


?>

<html><head><title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net2.css" rel="stylesheet" type="text/css">
<script language="javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
$(function(){
	$("#faltas").keyup(function(){
		var campo = $(this);
		var txt = campo.val();
		campo.val(txt.replace(',', '.'));
	});
});
</script>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style></head>
<body onLoad="document.all.rendi.focus();">
<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center" valign="middle">
<br>
<?php
if(empty($_REQUEST['participante'])){
?>
<form action="" method="post" name="form1">
<table width="80%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EFEFEF" 
            style="border-bottom: solid 1px #999; border-left:solid 1px #999; border-right:solid 1px #999; border-top:solid 1px #999;">
  <tr>
    <td height="18" colspan="2" align="center" valign="middle" bgcolor="#666666"><div class="style27"><?=$row['nome']?></div></td>
  </tr>
  <tr>
    <td width="35%" height="25" align="right" valign="middle" class="Texto10Azul">Rendimentos:</td>
    <td width="65%" height="25">&nbsp;
    <input name='rendi' type='text' id='rendi' size='13' class='campotexto' maxlength="13" value='<?=$row['adicional']?>'
                OnKeyDown="FormataValor(this,event,20,2)"
                onfocus="this.style.background='#CCFFCC'"  
                onblur="this.style.background='#FFFFFF'"  
                style='background:#FFFFFF;' tabindex="0"/></td>
  </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Texto10Azul">Descontos</td>
    <td height="25">&nbsp;
      <input name='desco' type='text' id='desco' size='13' class='campotexto' maxlength="13" value='<?=$row['desconto']?>'
                OnKeyDown="FormataValor(this,event,20,2)"
                onfocus="this.style.background='#CCFFCC'"  
                onblur="this.style.background='#FFFFFF'"  
                style='background:#FFFFFF;' /></td>
  </tr>
  <tr>
    <td height="25" align="right" valign="middle" class="Texto10Azul"><?=$tipo?></td>
    <td height="25">&nbsp;
      <input name='faltas' type='text' class='campotexto' id='faltas'  value='<?=$row['faltas']?>'
      style='background:#FFFFFF;' 
      onfocus="this.style.background='#CCFFCC'"  
      onblur="this.style.background='#FFFFFF'" size='5' maxlength="5"/>
      
      <input type="hidden" name="participante" value="<?=$autonomo?>" id="participante">
      <input type="hidden" name="tela" value="2" id="tela">
      <input type="hidden" name="linkenc" value="<?=$enc?>" id="linkenc">
      <input type="hidden" name="folha" value="<?=$folha?>" id="folha">
      <input type="hidden" name="id_participante" value="<?=$row['id_autonomo']?>" id="id_participante">
      
      </td>
  </tr>
  <?=$tr?>
  <tr>
    <td height="25" colspan="2" align="center" valign="middle" class="Texto10Azul"><input type="submit" value="Enviar"></td>
  </tr>
</table>
</form>
<script>
function INSS(){
	d = document.all;
	if(d.inss.value > 11 & d.tipo_inss.value == 2){
		alert("Atenção, Inss não pode passar de 11%");
		d.inss.value = '11';
		d.inn.focus();
	}
}
</script>
<?php
}else{

$participante = $_REQUEST['participante'];
$id_participante = $_REQUEST['id_participante'];
$faltas = $_REQUEST['faltas'];
$rendi = $_REQUEST['rendi'];
$desco = $_REQUEST['desco'];
$folha = $_REQUEST['folha'];
$inss = $_REQUEST['inss'];
$tipo_inss = $_REQUEST['tipo_inss'];

$linkenc = $_REQUEST['linkenc'];

//FORMATANDO PARA GRAVAR NA TABELA
$rendiF = str_replace(".","",$rendi);
$rendiF = str_replace(",",".",$rendiF);

$descoF = str_replace(".","",$desco);
$descoF = str_replace(",",".",$descoF);

//SELECIONANDO A FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

//JOGANDO AS FALTAS OS RENDIMENTOS E OS DESCONTOS
if($row_folha['contratacao'] == 1){
	mysql_query("UPDATE folha_autonomo SET faltas='$faltas', adicional='$rendiF', desconto='$descoF' WHERE id_folha_pro = '$participante' ");
}else{
	mysql_query("UPDATE folha_cooperado SET faltas='$faltas', adicional='$rendiF', desconto='$descoF' WHERE id_folha_pro = '$participante' ");
	mysql_query("UPDATE autonomo SET inss='$inss', tipo_inss = '$tipo_inss' WHERE id_autonomo = '$id_participante' ");
}

print "
<script>
//alert(\"Informações gravadas! \\n\\nNão esqueça de ATUALIZAR a FOLHA para ver as alterações\");
location.href=\"faltas.php?enc=$linkenc\"
</script>";


}
?>
</td>
</tr>
</table>
</body>
</html>
