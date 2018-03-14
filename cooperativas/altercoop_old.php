<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

if(empty($_REQUEST['update'])){

$coop = $_REQUEST['coop'];

$RE = mysql_query("Select *, date_format(data_nasci, '%d/%m/%Y')as data_nasci, date_format(data_rg, '%d/%m/%Y')as data_rg, date_format(data_escola, '%d/%m/%Y')as data_escola, date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_exame, '%d/%m/%Y')as data_exame, date_format(data_saida, '%d/%m/%Y')as data_saida, date_format(data_ctps, '%d/%m/%Y')as data_ctps , date_format(dada_pis, '%d/%m/%Y')as dada_pis, date_format(c_nascimento, '%d/%m/%Y')as c_nascimento, date_format(e_dataemissao, '%d/%m/%Y')as e_dataemissao from autonomo where id_autonomo = '$coop'");
$Row = mysql_fetch_array($RE);

$REPro = mysql_query("SELECT * FROM projeto where id_projeto = '$Row[id_projeto]'");
$RowPro = mysql_fetch_array($REPro);

$regiao = $RowPro['id_regiao'];

//TIPO CONTRATACAO
switch($Row['tipo_contratacao']) {
	case 1:
       $sel_tipo1 = "selected";
	break;
	case 3:
	   $sel_tipo3 = "selected";
	break;
	case 4:
	   $sel_tipo4 = "selected";
	break;
}

//TIPO Estado Civil
switch ($Row['civil']) {
	case "Solteiro":
	$sel_ci1 = "selected";
	break;
	case "Casado";
	$sel_ci2 = "selected";
	break;
	case "Viúvo";
	$sel_ci3 = "selected";
	break;
	case "Sep. Judicialmente";
	$sel_ci4 = "selected";
	break;
	case "Divorciado";
	$sel_ci5 = "selected";
	break;
}

//TIPO SEXO
if($Row['sexo'] == "M"){ $SEXO1 = "checked"; }else{ $SEXO2 = "checked"; }

//TIPO ESTUDA
if($Row['estuda'] == "sim"){ $EscolaCheck1 = "checked"; }else{ $EscolaCheck2 = "checked"; }

//DEPENDENTES
$RE_Depe = mysql_query ("SELECT *, date_format(data1, '%d/%m/%Y')as data1, date_format(data2, '%d/%m/%Y')as data2, 
date_format(data3, '%d/%m/%Y')as data3, date_format(data4, '%d/%m/%Y')as data4, date_format(data5, '%d/%m/%Y')as data5 FROM dependentes WHERE id_bolsista = '$coop' and id_projeto = '$Row[id_projeto]'");
$RowDepe = mysql_fetch_array($RE_Depe);

//TPO CABELOS
switch ($Row['cabelos']) {
	case "Loiro":
	$caB1 = "selected";
	break;
	case "Castanho Claro";
	$caB2 = "selected";
	break;
	case "Castanho Escuro";
	$caB3 = "selected";
	break;
	case "Ruivo";
	$caB4 = "selected";
	break;
	case "Pretos";
	$caB5 = "selected";
	break;
}


//TPO OLHOS
switch ($Row['olhos']) {
	case "Castanho Claro":
	$Olhos1 = "selected";
	break;
	case "Castanho Escuro";
	$Olhos2 = "selected";
	break;
	case "Verde";
	$Olhos3 = "selected";
	break;
	case "Azul";
	$Olhos4 = "selected";
	break;
	case "Mel";
	$Olhos5 = "selected";
	break;
	case "Preto";
	$Olhos6 = "selected";
	break;
}

// FOTO
if($Row['foto'] == "1"){
$foto = "Deseja remover a foto? <input name='foto' type='checkbox' id='foto' value='3'/> Sim";
}else{
$foto = "<input name='foto' type='checkbox' id='foto' value='1' onClick=\"document.all.arquivo.style.display = (document.all.arquivo.style.display == 'none') ? '' : 'none' ;\">";
}

// TIPO TIPO CONTA
if($Row['tipo_conta'] == "salario"){ $TpConta1 = "checked"; }else{ $TpConta2 = "checked"; }

//FORMATANDO VALOR
$valor = number_format($Row['e_renda'],2,",",".");

//ATIVO INATIVO
if($Row['status'] == "1"){ $ativado1 = "checked"; }else{ $ativado2 = "checked"; }

//TIPO DE RECOLHIMENTO INSS
if($Row['tipo_inss'] == "1"){ $tipoINSS1 = "selected"; }else{ $tipoINSS2 = "selected";}

//COTA
$cota = number_format($Row['cota'],2,",",".");

//----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$local_banco = "Edição de Cooperado";
$acao_banco = "Editando o Cooperado ($Row[campo3]) $Row[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

//----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG

?>
<!-- AKI -->
<html>
<head><title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>


<link href="../net1.css" rel="stylesheet" type="text/css">

<style type="text/css">

<!--
.style2 {
font-family: Arial, Helvetica, sans-serif;
font-size: 12px;
font-weight: bold;
}
.style5 {color: #FF0000}
.style6 {
font-family: Arial, Helvetica, sans-serif;
font-size: 12px;
}
.style11 {font-weight: bold}
.style13 {font-weight: bold}
.style15 {font-weight: bold}
.style17 {font-weight: bold}
.style19 {font-weight: bold}
.style23 {font-weight: bold}
.style24 {
font-size: 10px;
font-weight: bold;
color: #003300;
}
.style25 {color: #003300}
.style26 {
color: #FFFFFF;
font-size: 10px;
}
.style27 {color: #FFFFFF; }
-->
</style>

<script language="javascript" src="../js/ramon.js"></script>
<style type='text/css'>
<!--

.style1 {
font-family: Arial, Helvetica, sans-serif;
font-weight: bold;
font-size: 12px;
}
.style3 {font-size: 12px}
.style6 {
	color: #000000
}
.style7 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
.style37 {font-family: Arial, Helvetica, sans-serif}
.style39 {
	font-family: Arial, Helvetica, sans-serif;
	color: #000000;
}
.style40 {font-weight: bold; font-family: Arial, Helvetica, sans-serif;}
.style41 {
color: #FFFFFF;
font-size: 16px;
}
.style42 {
	font-weight: bold;
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
}
.style43 {font-family: Arial, Helvetica, sans-serif; color: #FFFFFF; font-size: 14px; }
.style44 {font-family: Arial, Helvetica, sans-serif; color: #003300; font-size: 14px; }
.style45 {font-size: 14px}
.style46 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
.style47 {
font-size: 16px;
color: #FF0000;
}
.style48 {
font-size: 8px;
color: #FF0000;
}
.style49 {font-size: 9px}
.style71 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
-->
</style>

<script language="javascript"  type="text/javascript">
function FuncaoInss(a){
	d = document.all;

	if(a == 1){
		d.divInss.style.display = '';
		d.p_inss.style.display = '';
	}else if(a == 2){
		d.divInss.style.display = 'none';
		d.p_inss.style.display = 'none';
		d.p_inss.value = '';
		d.inss_recolher.value = 11;
	}else if(a == 3){
		porcentagem = d.p_inss.value;
		if(porcentagem <= 11){
			valor = 11 - porcentagem;
		}else{
			valor = 0;
		}
		d.inss_recolher.value = valor;
	}
}

</script>

</head>

<body onLoad="drogadebanco()">
<form action='' method='post' name='form1' enctype='multipart/form-data' onSubmit="return validaForm()">
  <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
    <tr>
<td colspan='2' bgcolor='#666666' class='style1'><div align='center' class='style43'>DADOS DO PROJETO</div></td>
</tr>


<tr>
<td width="32%" height='30'bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'><span class='style37'>Projeto:&nbsp;</span></div></td>
<td width="68%" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;<?=$RowPro['0']." - ".$RowPro['nome']?></span></td>
</tr>


<tr>
<td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'><span class='style37'>Cooperativa Vinculada:&nbsp;</span></div></td>
  <span class="style1"><span class="style39">
  
  </span></span>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<select name='vinculo' id='vinculo' class='campotexto'>
<?php
$RECoop = mysql_query("SELECT * FROM cooperativas WHERE id_regiao = '$Row[id_regiao]'");

while ($RowCoop = mysql_fetch_array($RECoop)){
	if($Row['id_cooperativa'] == $RowCoop['0']){
		print "<option value='$RowCoop[0]' selected>$RowCoop[0] - $RowCoop[fantasia]</option>";
	}else{
		print "<option value='$RowCoop[0]'>$RowCoop[0] - $RowCoop[fantasia]</option>";
	}
}

?>

</select>


</span></td>
</tr>


<tr>
  <td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'><span class='style37'>Atividade:&nbsp;</span></div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<select name='atividade' id='atividade' class='campotexto'>
  <?php 
	$RECurso = mysql_query("SELECT * FROM curso WHERE campo3 = '$Row[id_projeto]' AND tipo = '3' AND id_regiao = '$Row[id_regiao]' ORDER BY campo3");

	while ($RowCurso = mysql_fetch_array($RECurso)){
		if($Row['id_curso'] == $RowCurso['0']){
			print "<option value='$RowCurso[0]' selected>$RowCurso[0] - $RowCurso[nome]</option>";
		}else{
			print "<option value='$RowCurso[0]'>$RowCurso[0] - $RowCurso[nome]</option>";
		}
	}

?>
  
</select></span></td>
</tr>

<tr>
  
  
  <td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'><span class='style37'>Unidade:&nbsp;</span></div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
    <select name='locacao' id='locacao' class='campotexto'>
  <?php

	$result_unidade = mysql_query("SELECT * FROM unidade where id_regiao = '$Row[id_regiao]' and campo1 = '$Row[id_projeto]' ORDER BY unidade");
	while ($row_unidade = mysql_fetch_array($result_unidade)){
		if($row_unidade['unidade'] == "$Row[locacao]"){
			print "<option value='$row_unidade[unidade]' selected>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
		}else{
			print "<option value='$row_unidade[unidade]'>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
		}
	}
?>
      </select>
  </span></td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style6'>
Código:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style6'>&nbsp;&nbsp; 
<input name="codigo" type="text" id="codigo" value="<?=$Row['campo3']?>" size="10" 
onFocus="this.style.background='#CCCCCC'"
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>

</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>
Tipo Contratação:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
  <select name="contratacao" id="contratacao">
    <option value="1" <?=$sel_tipo1?>>Aut&ocirc;nomo</option>
    <option value="3" <?=$sel_tipo3?>>Cooperado</option>
    <option value="4" <?=$sel_tipo4?>>Aut&ocirc;nomo / PJ</option>
  </select>
</td>
</tr>

</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
<tr>
<td colspan='8' bgcolor='#666666' class='style1'><div align='center' class='style6 style3 style40 style42'>
<div align='center' class='style41'>DADOS CADASTRAIS</div>
</div></td>
</tr>
<tr height='30'>
<td width='13%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Nome:&nbsp;</span></div>
</div></td>
<td width='87%' colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='nome' type='text' class='campotexto' id='nome' 
style='background:#FFFFFF;'
onFocus="this.style.background='#CCCCCC'"
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" value="<?=$Row['nome']?>" size='75'/>
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Endereco:&nbsp;</span></div>
</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='endereco' type='text' class='campotexto' id='endereco' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" value="<?=$Row['endereco']?>" size='75'/>
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Bairro:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='bairro' type='text' class='campotexto' id='bairro' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" value="<?=$Row['bairro']?>" size='15'/>
&nbsp;&nbsp;</span></div>
</div></td>
<td bgcolor='#CCCCCC' class='style39'><div align='right' class='style6'>
<div align='right'><span class='style37'> <b>Cidade:&nbsp;</b></span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='cidade' type='text' class='campotexto' id='cidade' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" value="<?=$Row['cidade']?>" size='12'/>
</span></div>
</div></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>UF:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='uf' type='text' class='campotexto' id='uf' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()"
onkeyup="pula(2,this.id,cep.id)" value="<?=$Row['uf']?>" size='2' maxlength='2' />
</span></div>
</div></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>CEP:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='cep' type='text' class='campotexto' id='cep' 
style='background:#FFFFFF; text-transform:uppercase;'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'"
OnKeyPress="formatar('#####-###', this)" 
onKeyUp="pula(9,this.id,tel_fixo.id)" value="<?=$Row['cep']?>" size='10' maxlength='9' />
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Telefones:&nbsp;</span></div>
</div></td>
<td colspan='2' bgcolor='#CCCCCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='center'><span class='style37'>Fixo:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_fixo' type='text' class='campotexto' id='tel_fixo' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,tel_cel.id)" value="<?=$Row['tel_fixo']?>" size='14'>
</span></div>
</div></td>
<td bgcolor='#CCCCCC' class='style1'> <div align='center' class='style6 style37'>
<div align='right'><span class='style37'>Cel:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_cel' type='text' class='campotexto' id='tel_cel' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,tel_rec.id)" value="<?=$Row['tel_cel']?>" size='14' />
&nbsp;</span></div>
</div></td>
<td bgcolor='#CCCCCC' class='style1'><div align='center' class='style6 style37'>
<div align='right'><span class='style37'>Recado:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_rec' type='text' class='campotexto' id='tel_rec' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,data_nasci.id)" value="<?=$Row['tel_rec']?>" size='14' />
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'><span class='style37'>Data de Nascimento:&nbsp;</span></div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;
<input name='data_nasci' type='text' class='campotexto' id='data_nasci' 
style='background:#FFFFFF;'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'"
onKeyUp="mascara_data(this); pula(10,this.id,naturalidade.id)" value="<?=$Row['data_nasci']?>" size='10'>
</span> <span class='style6 style37'>&nbsp;</span></td>
<td bgcolor='#CCCCCC' class='style1'>
<div align='right' class='style39'>Naturalidade:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='10' value="<?=$Row['naturalidade']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nacionalidade:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='8' value="<?=$Row['nacionalidade']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Estado Civil:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<select name='civil' class='campotexto' id='civil'>
<option <?=$sel_ci1?>>Solteiro</option>
<option <?=$sel_ci2?>>Casado</option>
<option <?=$sel_ci3?>>Viúvo</option>
<option <?=$sel_ci4?>>Sep. Judicialmente</option>
<option <?=$sel_ci5?>>Divorciado</option>
</select>
</span></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right'  class='style39'>Sexo:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
<table align='left'>
<tr height='30'>
<td class='style39'><span class='style37'>
&nbsp;&nbsp;
<label>
<input type='radio' name='sexo' value='M' <?=$SEXO1?> /> Masculino </label></span></td>
<td class='style39'><span class='style37'>
&nbsp;&nbsp;
<label>		
<input type='radio' name='sexo' value='F' <?=$SEXO2?>/>Feminino</label></span></td>
</tr>
</table></td>
</tr>
<tr>
<td colspan='8' bgcolor='#666666' class='style1'><div align='center' class='style43'>DADOS DA FAMÍLIA E EDUCACIONAIS</div></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome do C&ocirc;njuge:&nbsp;</div></td>
  <td colspan='7' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
    <input name='c_nome' type='text' class='campotexto' id='c_nome' size='75' value="<?=$Row['c_nome']?>"
onfocus="this.style.background='#CCCCCC'"
onblur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
  </span></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>CPF C&ocirc;njuge:&nbsp;</div></td>
  <td colspan="2" bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style39">
    <input name='c_cpf' type='text' class='campotexto' id='c_cpf' size='17' maxlength='14' value="<?=$Row['c_cpf']?>"
                onkeypress="formatar('###.###.###-##', this)" 
                onfocus="this.style.background='#CCCCCC'" 
                onblur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(14,this.id,c_nascimento.id)"/>
    </span></td>
  <td colspan="2" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Data de Nascimento:&nbsp;</div></td>
  <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style6 style37">
    <input name='c_nascimento' type='text' id='c_nascimento' size='10' class='campotexto' value="<?=$Row['c_nascimento']?>"
onkeyup="mascara_data(this); pula(10,this.id,c_profissao.id)"
onfocus="this.style.background='#CCCCCC'" 
onblur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
    </span></td>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Profiss&atilde;o:</div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class="style37">
    &nbsp;&nbsp;
    <input name='c_profissao' type='text' class='campotexto' id='c_profissao' size='20' value="<?=$Row['c_profissao']?>"
onfocus="this.style.background='#CCCCCC'"
onblur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Filiação - Pai:&nbsp;</div></td>
  <td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
  <input name='pai' type='text' class='campotexto' id='pai' size='75' value="<?=$Row['pai']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Pai:</span>&nbsp;&nbsp;
    
  <input name='nacionalidade_pai' type='text' class='campotexto' id='nacionalidade_pai' size='15' value="<?=$Row['nacionalidade_pai']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>	
    
  </span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Filiação - Mãe:&nbsp;</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
<input name='mae' type='text' class='campotexto' id='mae' size='75' value="<?=$Row['mae']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Mãe:</span>&nbsp;&nbsp;
	
<input name='nacionalidade_mae' type='text' class='campotexto' id='nacionalidade_mae' size='15' value="<?=$Row['nacionalidade_mae']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>	



</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Estuda Atualmente?&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><table align='left'>
<tr height='30'>
<td class='style39'><span class='style39'>&nbsp;&nbsp;
<input type='radio' name='estuda' value='sim' <?=$EscolaCheck1?> />
SIM</span></td>
<td class='style39'><span class='style39'>&nbsp;&nbsp;
<input type='radio' name='estuda' value='nao' <?=$EscolaCheck2?> />
NÃO</span></td>
</tr>
</table></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Término em:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='data_escola' type='text' id='data_escola' size='10' class='campotexto' value="<?=$Row['data_escola']?>"
onKeyUp="mascara_data(this); pula(10,this.id,escolaridade.id)" maxlength='10' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Escolaridade:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;&nbsp;
<select name='escolaridade'>
<option value="12">12 - Não informado</option>
<? $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on' LIMIT 0,11");
while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) { ?>
<option value="<?=$escolaridade['id']?>"<? if($Row['escolaridade'] == $escolaridade['id']) { ?> selected<? } ?>>
<?=$escolaridade['cod']?> - <?=$escolaridade['nome']?>
</option>
<? } ?>
</select>
</span></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Instituíção:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;
<input name='instituicao' type='text' class='campotexto' id='instituicao' size='20' value="<?=$Row['instituicao']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Curso:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
<input name='curso' type='text' class='campotexto' id='curso' size='10' value="<?=$Row['curso']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Número de Filhos:&nbsp;</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;&nbsp;
<input name='filhos' type='text' class='campotexto  style37' id='filhos' size='2' value="<?=$Row['num_filhos']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
<div align='right'></div>    <div align='right'></div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;&nbsp;
<input name='filho_1' type='text' class='campotexto' id='filho_1' size='50' value="<?=$RowDepe['nome1']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_1' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_1'
onFocus="this.style.background='#CCCCCC'" value="<?=$RowDepe['data1']?>"
onBlur="this.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_2' type='text' class='campotexto' id='filho_2' size='50' value="<?=$RowDepe['nome2']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_2' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_2'
onFocus="this.style.background='#CCCCCC'" value="<?=$RowDepe['data2']?>"
onBlur="this.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_3' type='text' class='campotexto' id='filho_3' size='50' value="<?=$RowDepe['nome3']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_3' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_3'
onFocus="this.style.background='#CCCCCC'" value="<?=$RowDepe['data3']?>"
onBlur="this.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_4' type='text' class='campotexto' id='filho_4' size='50' value="<?=$RowDepe['nome4']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_4' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_4'
onFocus="this.style.background='#CCCCCC'" value="<?=$RowDepe['data4']?>"
onBlur="this.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;&nbsp;
<input name='filho_5' type='text' class='campotexto' id='filho_5' size='50' value="<?=$RowDepe['nome5']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_5' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_5'
onFocus="this.style.background='#CCCCCC'" value="<?=$RowDepe['data5']?>"
onBlur="this.style.background='#FFFFFF'" 
onkeyup="mascara_data(this)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr>
<td colspan='8' bgcolor='#666666' class='style1'><div align='center' class='style43'>APARÊNCIA</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'>
<div align='right' class='style39'>Cabelos:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;<select name='cabelos' id='cabelos'>
<option <?=$caB1?>>Loiro</option>
<option <?=$caB2?>>Castanho Claro</option>
<option <?=$caB3?>>Castanho Escuro</option>
<option <?=$caB4?>>Ruivo</option>
<option <?=$caB5?>>Pretos</option>
</select>
</td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Olhos:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>&nbsp;&nbsp;
<select name='olhos' id='olhos'>
<option <?=$Olhos1?>>Castanho Claro</option>
<option <?=$Olhos2?>>Castanho Escuro</option>
<option <?=$Olhos3?>>Verde</option>
<option <?=$Olhos4?>>Azul</option>
<option <?=$Olhos5?>>Mel</option>
<option <?=$Olhos6?>>Preto</option>
</select>
</span></span></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Peso:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
&nbsp;&nbsp;
<input name='peso' type='text' class='campotexto' id='peso' size='5' value="<?=$Row['peso']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</span></span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Altura:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
&nbsp;&nbsp;
<input name='altura' type='text' class='campotexto' id='altura' size='5' value="<?=$Row['altura']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
&nbsp;&nbsp; </span></span></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Etnia:&nbsp;</div></td>
      <td bgcolor='#FFFFFF'>
	  <select name='etnia'>
    <option value="6">Não informado</option>
<? $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' LIMIT 0,5");
while($etnia = mysql_fetch_assoc($qr_etnias)) { ?>
<option value="<?=$etnia['id']?>"<? if($Row['etnia'] == $etnia['id']) { ?> selected<? } ?>><?=$etnia['nome']?></option>
<?php } ?>
</select>
     </td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Marcas ou Cicatriz aparente:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='defeito' type='text' class='campotexto' id='defeito' size='18' value="<?=$Row['defeito']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
</tr>
<tr>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Deficiência:</div></td>
  <td bgcolor='#FFFFFF'>
  <select name='deficiencia'>
<option value="">Não é portador de deficiência</option>
<? $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) { ?>
<option value="<?=$deficiencia['id']?>"<? if($row['deficiencia'] == $deficiencia['id']) { ?> selected="selected"<? } ?>><?=$deficiencia['nome']?></option>
      <?php } ?>
      </select>
      </td>
    <td colspan='6' bgcolor='#FFFFFF' class='style1'>&nbsp;</td>
  </tr>
<td colspan='6' bgcolor='#FFFFFF' class='style1'>&nbsp;</td>
</tr>
<tr height='30' id='ancora_foto'>
<td colspan='8' bgcolor='#FFFFFF' class='style1'>
<div align='center' class='style39'>
Enviar Foto:&nbsp;
<?=$foto?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name='arquivo' type='file' id='arquivo' size='60' style='display:none'/> 
<br>
<span style="font-size:9px">(somente arquivo .gif) </span></div></td>
</tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
  <tr>
    <td colspan='8' bgcolor='#666666' class='style1'><div align='center' class='style43'>DOCUMENTAÇÃO</div></td>
  </tr>
  <tr height='30'>
    <td width='16%' height="30" bgcolor='#CCCCCC' class='style1'>
	<div align='right' class='style39'>Nº do RG:&nbsp;</div></td>
    <td width='14%' height="30" bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
	<input name='rg' type='text' id='rg' size='13' maxlength='14' class='campotexto' value="<?=$Row['rg']?>"
                OnKeyPress="formatar('##.###.###-#', this)" 
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(12,this.id,orgao.id)">    </td>
    <td width='13%' height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Orgão Expedidor:&nbsp;</div></td>
    <td width='9%' height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='orgao' type='text' class='campotexto' id='orgao' size='8' value="<?=$Row['orgao']?>"
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span> </td>
    <td width='5%' height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td width='7%' height="30" bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
    <input name='uf_rg' type='text' class='campotexto' id='uf_rg' size='2' maxlength='2' value="<?=$Row['uf_rg']?>"
                onfocus="this.style.background='#CCCCCC'" 
                onblur="this.style.background='#FFFFFF'"
				onKeyUp="pula(2,this.id,data_rg.id)"
                style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/></td>
    <td width='18%' height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Data Expedição:&nbsp;</div></td>
    <td width='18%' height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='data_rg' type='text' class='campotexto' size='12' maxlength='10' value="<?=$Row['data_rg']?>"
		id='data_rg'
        onFocus="this.style.background='#CCCCCC'" 
        onBlur="this.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,cpf.id)"
        style='background:#FFFFFF;'/>
		
    </span></td>
  </tr>
  <tr height='30'>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>CPF:&nbsp;</div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='cpf' type='text' class='campotexto' id='cpf' size='17' maxlength='14' value="<?=$Row['cpf']?>"
                OnKeyPress="formatar('###.###.###-##', this)" 
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(14,this.id,reservista.id)"/>
    </span></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Carteira do Conselho:&nbsp;</div></td>
    <td height="30" colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
      <input name='conselho' type='text' id='conselho' size='13' class='campotexto' value="<?=$Row['conselho']?>"
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Certificado de Reservista:&nbsp;</div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
      <input name='reservista' type='text' class='campotexto' id='reservista'  value="<?=$Row['reservista']?>"
	  size='18'
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>Nº Carteira de Trabalho:&nbsp;</span></div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='trabalho' type='text' class='campotexto' id='trabalho' size='15' value="<?=$Row['campo1']?>"
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Série:&nbsp;</div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
   <input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='10' value="<?=$Row['serie_ctps']?>"
        onfocus="this.style.background='#CCCCCC'"
        onblur="this.style.background='#FFFFFF'" style='background:#FFFFFF;'/>
          </span>	</td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
	<input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' value="<?=$Row['uf_ctps']?>"
                onfocus="this.style.background='#CCCCCC'" 
                onblur="this.style.background='#FFFFFF'" 
				onKeyUp="pula(2,this.id,data_ctps.id)"
                style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Data carteira de Trabalho:&nbsp;</div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'>
      &nbsp;&nbsp;
      
      <input name='data_ctps' type='text' class='campotexto' size='12' maxlength='10' id='data_ctps' value="<?=$Row['data_ctps']?>"
        onFocus="this.style.background='#CCCCCC'" 
        onBlur="this.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)"
        style='background:#FFFFFF;'/>    </td>
  </tr>
  <tr height='30'>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>Nº Título de Eleitor:&nbsp;</span></div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='titulo' type='text' class='campotexto' id='titulo2' size='10' value="<?=$Row['titulo']?>"
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;' />
    </span></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'> Zona:&nbsp;</span></div></td>
    <td height="30" colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='zona' type='text' class='campotexto' id='zona2' size='3' value="<?=$Row['zona']?>"
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>Seção:&nbsp;</span></div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='secao' type='text' class='campotexto' id='secao' size='3' value="<?=$Row['secao']?>"
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>PIS:&nbsp;</span></div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='pis' type='text' class='campotexto' id='pis' size='12' value="<?=$Row['pis']?>"
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Data Pis:&nbsp;</div></td>
    <td height="30" colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
      <input name='data_pis' type='text' class='campotexto' size='12' maxlength='10' id='data_pis' value="<?=$Row['dada_pis']?>"
        onFocus="this.style.background='#CCCCCC'" 
        onBlur="this.style.background='#FFFFFF'" 
		onKeyUp="mascara_data(this); pula(10,this.id,fgts.id)"
        style='background:#FFFFFF;'/></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>FGTS:&nbsp;</span></div></td>
    <td height="30" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='fgts' type='text' class='campotexto' id='fgts' size='10' value="<?=$Row['fgts']?>"
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>INSS a Recolher:&nbsp;</span></div></td>
    <td height="30" bgcolor='#FFFFFF' class='style39'>&nbsp;&nbsp;
      <input name="inss_recolher" type="text" class="campotexto" id="inss_recolher" 
      style='background:#FFFFFF; '
      onFocus="this.style.background='#CCCCCC'" 
	  onBlur="this.style.background='#FFFFFF';" value="<?=$Row['inss']?>" size="7"></td>
    <td height="30" bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>Tipo de recolhimento:&nbsp;</span></div></td>
    <td height="30" colspan="5" bgcolor='#FFFFFF' class='style1'><span class="style2">
      &nbsp;&nbsp;&nbsp;
      <select name='tipoinss' class='campotexto' id='tipoinss'>
        <option value="1" <?=$tipoINSS1?>>VALOR FIXO</option>
        <option value="2" <?=$tipoINSS2?>>VALOR PERCENTUAL</option>
      </select>
    </span></td>
    </tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' style="display:none">
  <tr>
    <td colspan='6' bgcolor='#003300' class='style1'><div align='center' class='style43'>BENEFÍCIOS</div></td>
  </tr>
  <tr height='30'>
    <td width='19%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>
	Assistência Médica:&nbsp;</div>	</td>
    <td bgcolor='#FFFFFF' class='style1'>
	
	<table width='100%' class=linha>
<tr> 
<td width='74'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='1' $chek_medi1>Sim</label></td><td width='255'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='0' $chek_medi0>Não</label>&nbsp;&nbsp; $mensagem_medi</td>
</tr>
</table>	</td>
    <td width='19%' bgcolor='#CCCCCC' class='style1'>
	<div align='right' class='style39'>Tipo de Plano:&nbsp;</div></td>
    <td width='19%' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
<select name='plano_medico' class='campotexto' id='plano_medico'>

<option value=1 $selected_planoF>Familiar</option>
<option value=2 $selected_planoI>Individual</option>
</select>   </td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Seguro, Apólice:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
          <select name='apolice' class='campotexto' id='apolice'>
<option value='0'>Não Possui</option>

<?php
$result_ap = mysql_query("SELECT * FROM apolice where id_regiao = $row[regiao]", $conn);
while ($row_ap = mysql_fetch_array($result_ap)){
  if($row_ap['id_apolice'] == $row[apolice]){
  print "<option value='$row_ap[id_apolice]' selected>$row_ap[razao]</option>";   
  }else{
  print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
  }
}


?>
</select>
        </select>
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Dependente:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='dependente' type='text' class='campotexto' id='dependente' size='20' value=''
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Insalubridade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
    <input name='insalubridade' type='checkbox' id='insalubridade2' value='1' $chek1/></td>
    
	<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Adicional Noturno:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
	<table class='linha'>
<tr> 
<td width='61'>&nbsp;&nbsp; 
<label><input type='radio' name='ad_noturno' value='1' $checkad_noturno1>Sim</label></td>
<td width='61'>&nbsp;&nbsp; 
<label><input type='radio' name='ad_noturno' value='0' $checkad_noturno0>Não</label></td>
</tr>
</table>
      </td>
  </tr>
  
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Vale Transporte:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='transporte' type='checkbox' id='transporte2' value='1' onClick="document.all.tablevale.style.display = (document.all.tablevale.style.display == 'none') ? '' : 'none' ;" $chek2 />

</td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Integrante do CIPA:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
	
<table class='linha'>
<tr> 
<td width='61'>&nbsp;&nbsp; 
<label><input type='radio' name='cipa' value='1' $checkedcipa1>Sim</label></td>
<td width='61'>&nbsp;&nbsp; 
<label><input type='radio' name='cipa' value='0' $checkedcipa0>Não</label></td>
</tr>
</table>	</td>
  </tr>  
</table>
<!-- ______________________________________________________________________________________________________ -->
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
  <tr>
    <td colspan='6' bgcolor='#666666' class='style1'><div align='center' class='style43'>INFORMA&Ccedil;&Otilde;ES PROFISSIONAIS</div></td>
  </tr>
  <tr height='30'>
    <td width='13%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Empresa:&nbsp;</div></td>
    <td colspan="3" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
      <input name='e_nome' type='text' class='campotexto' id='e_nome' size='50' value='<?=$Row['e_empresa']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span><span class='style39'>&nbsp;&nbsp;</span></td>
    <td width='10%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>CNPJ:&nbsp;</div></td>
    <td width='22%' bgcolor='#FFFFFF' class='style1'><span class="style39">
      &nbsp;&nbsp;
      <input name='e_cnpj' type='text' class='campotexto' id='e_cnpj' value='<?=$Row['e_cnpj']?>'
                style='background:#FFFFFF;' 
                onFocus="this.style.background='#CCCCCC'" 
                onBlur="this.style.background='#FFFFFF'"
                OnKeyPress="formatar('##.###.###/####-##', this)"
				onkeyup="pula(18,this.id,e_endereco.id)" size="19" maxlength='18'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Endere&ccedil;o:&nbsp;</div></td>
    <td width='24%' bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_endereco' type='text' class='campotexto' id='e_endereco' value='<?=$Row['e_endereco']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='35'/>
    </span></td>
    <td width='11%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Bairro:&nbsp;</div></td>
    <td width='20%' bgcolor='#FFFFFF' class='style1'><span class="style37">
      &nbsp;&nbsp;
      <input name='e_bairro' type='text' class='campotexto' id='e_bairro' value='<?=$Row['e_bairro']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Cidade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class="style39">
      &nbsp;&nbsp;<span class="style37">
      <input name='e_cidade' type='text' class='campotexto' id='e_cidade' value='<?=$Row['e_cidade']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
      </span></span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Estado:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class="style37">
      &nbsp;
      <input name='e_estado' type='text' class='campotexto' id='e_estado' size='2' maxlength='2' value='<?=$Row['e_estado']?>'
onChange="this.value=this.value.toUpperCase()" style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onKeyUp="pula(2,this.id,e_cep.id)" />
&nbsp;&nbsp; </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>CEP:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_cep' type='text' class='campotexto' id='e_cep' size='10' maxlength='9' value='<?=$Row['e_cep']?>'
        style='background:#FFFFFF; text-transform:uppercase;'
        onFocus="this.style.background='#CCCCCC'" 
        onBlur="this.style.background='#FFFFFF'"
        OnKeyPress="formatar('#####-###', this)" 
        onKeyUp="pula(9,this.id,e_ramo.id)" />
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Ramo Atividade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_ramo' type='text' class='campotexto' id='e_ramo' value='<?=$Row['e_ramo']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='30'/>
    </span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Telefone:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_telefone' type='text' id='e_telefone' size='14' value='<?=$Row['e_tel']?>'
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,e_ramal.id)" 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Ramal:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_ramal' type='text' id='e_ramal' size='14' value='<?=$Row['e_ramal']?>'
        onFocus="this.style.background='#CCCCCC'" 
        onBlur="this.style.background='#FFFFFF'" 
        style='background:#FFFFFF;' class='campotexto'>
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Fax:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'> &nbsp;&nbsp;<span class="style37">
      <input name='e_fax' type='text' id='e_fax' size='14' value='<?=$Row['e_fax']?>'
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,e_email.id)" 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>E-mail:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_email' type='text' id='e_email' size='30' value='<?=$Row['e_email']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style=" background:#FFFFFF; text-transform:lowercase" class='campotexto'>
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Tempo de Servi&ccedil;o:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_tempo' type='text' id='e_tempo' size='14' value='<?=$Row['e_tempo']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto' onChange="this.value=this.value.toUpperCase()">
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Profiss&atilde;o:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_profissao' type='text' id='e_profissao' size='14' onChange="this.value=this.value.toUpperCase()"
onFocus="this.style.background='#CCCCCC'" value='<?=$Row['e_profissao']?>'
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Cargo:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_cargo' type='text' id='e_cargo' size='20' value='<?=$Row['e_cargo']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto' onChange="this.value=this.value.toUpperCase()">
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Data Emiss&atilde;o
      :&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='e_dataemissao' type='text' class='campotexto' size='12' maxlength='10' id='e_dataemissao' value='<?=$Row['e_dataemissao']?>'
        onFocus="this.style.background='#CCCCCC'" 
        onBlur="this.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,e_referencia.id)"
        style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Refer&ecirc;ncia:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style39">
      <input name='e_referencia' type='text' class='campotexto' id='e_referencia' value='<?=$Row['e_referencia']?>'
                onFocus="this.style.background='#CCCCCC'" onChange="this.value=this.value.toUpperCase()"
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Renda:&nbsp;</div></td>
    <td colspan="5" bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style39">
      <input name='e_renda' type='text' class='campotexto' id='e_renda' style='background:#FFFFFF;' value='<?=$valor?>'
      onFocus="this.style.background='#CCCCCC'" onBlur="this.style.background='#FFFFFF'"
      onChange="this.value=this.value.toUpperCase()" OnKeyDown="FormataValor(this,event,17,2)" size="15" />
    </span></td>
    </tr>
</table>
<br>
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
  <tr>
    <td colspan='6' bgcolor='#666666' class='style1'><div align='center' class='style43'>REFER&Ecirc;NCIA</div></td>
    </tr>
  <tr height='30'>
    <td width='13%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
    <td colspan="5" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
      <input name='r_nome' type='text' class='campotexto' id='r_nome' size='50' value='<?=$Row['r_nome']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span><span class='style39'>&nbsp;&nbsp;</span><span class="style39"> &nbsp;&nbsp;</span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Endere&ccedil;o:&nbsp;</div></td>
    <td width='24%' bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='r_endereco' type='text' class='campotexto' id='r_endereco' value='<?=$Row['r_endereco']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='35'/>
      </span></td>
    <td width='11%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Bairro:&nbsp;</div></td>
    <td width='20%' bgcolor='#FFFFFF' class='style1'><span class="style37"> &nbsp;&nbsp;
      <input name='r_bairro' type='text' class='campotexto' id='r_bairro' value='<?=$Row['r_bairro']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
      </span></td>
    <td width="10%" bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Cidade:&nbsp;</div></td>
    <td width="22%" bgcolor='#FFFFFF' class='style1'><span class="style39"> &nbsp;&nbsp;<span class="style37">
      <input name='r_cidade' type='text' class='campotexto' id='r_cidade' value='<?=$Row['r_cidade']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
      </span></span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Estado:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='r_estado' type='text' class='campotexto' id='r_estado' onKeyUp="pula(2,this.id,r_cep.id)" value='<?=$Row['r_estado']?>'
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='2'/>
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>CEP:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_cep' type='text' class='campotexto' id='r_cep' size='10' maxlength='9' value='<?=$Row['r_cep']?>'
style='background:#FFFFFF; text-transform:uppercase;'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'"
OnKeyPress="formatar('#####-###', this)" 
onKeyUp="pula(9,this.id,r_email.id)" />
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Estado:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_email' type='text' class='campotexto' id='r_email'  value='<?=$Row['r_email']?>'
style=" background:#FFFFFF; text-transform:lowercase" 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" size="30"/>
    </span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Telefone:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='r_telefone' type='text' id='r_telefone' size='14' value='<?=$Row['r_tel']?>'
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,r_ramal.id)" 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
      </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Ramal:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_ramal' type='text' id='r_ramal' size='14' value='<?=$Row['r_ramal']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
      </span></td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Fax:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_fax' type='text' id='r_fax' size='14' value='<?=$Row['r_fax']?>'
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,data_entrada.id)" 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
      </span></td>
  </tr>
  </table>
<br>
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
  <tr>
    <td colspan='4' bgcolor='#666666' class='style1'><div align='center' class='style43'>DADOS BANC&Aacute;RIOS</div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'> Tipo de Pagamento:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;
      <select name='tipopg' class='campotexto' id='tipopg'>
        <?php
	$result_pg = mysql_query("SELECT * FROM tipopg where id_projeto = '$Row[id_projeto]'");
	
	while ($row_pg = mysql_fetch_array($result_pg)){
		if($Row['tipo_pagamento'] == $row_pg['0']){
			print "<option value='$row_pg[0]' selected>$row_pg[tipopg]</option>";
		}else{
			print "<option value='$row_pg[0]'>$row_pg[tipopg]</option>";
		}
	}
?>
      </select>      &nbsp;</td>
  </tr>
  <tr height='30'>
    <td width='17%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Banco:&nbsp;</div></td>
    <td width='31%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
      <select name='banco' class='campotexto' id='banco' onChange="drogadebanco()">
        <option value='0'>Nenhum Banco</option>
        <?php
	$result_banco = mysql_query("SELECT * FROM bancos where id_projeto = '$Row[id_projeto]'");
	
	while ($row_banco = mysql_fetch_array($result_banco)){
		if($Row['banco'] == $row_banco['0']){
			print "<option value='$row_banco[0]' selected>$row_banco[id_banco] - $row_banco[nome]</option>";
		}else{
			print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
		}
	}
	
	if($Row['banco'] == "9999"){
	print "<option value='9999' selected>Outro</option></select>";
	}else{
	print "<option value='9999'>Outro</option></select>";
	}
  ?>
        </select>
      </span></td>
    <td width='17%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Ag&ecirc;ncia:&nbsp;</div></td>
    <td width='35%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='agencia' type='text' class='campotexto' id='agencia' size='12' value='<?=$Row['agencia']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
      </span></td>
  </tr>
  <tr height='30' id="linhabanc2">
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Conta:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class='style39'>
      <input name='conta' type='text' class='campotexto' id='conta' size='12' value='<?=$Row['conta']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
      </span> </td>
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Tipo de Conta:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'> &nbsp;&nbsp;
        <label>
          <input type='radio' name='radio_tipo_conta' value='salario' <?=$TpConta1?>>
          Conta Sal&aacute;rio </label>
        &nbsp;&nbsp;
        <label>
          <input type='radio' name='radio_tipo_conta' value='corrente' <?=$TpConta2?>>
          Conta Corrente </label>
        &nbsp;&nbsp; </span></td>
  </tr>
  <tr height='30' id="linhabanc3" style="display:none">
    <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Nome do Banco:&nbsp;<br />
      <span class='style49'>(caso n&atilde;o esteja na lista acima)&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class="style39">
      &nbsp;
      <input name='nomebanco' type='text' class='campotexto' id='nomebanco' size='50' value='<?=$Row['nome_banco']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span></td>
    <td bgcolor='#CCCCCC' class='style1'>&nbsp;</td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;</td>
  </tr>
</table>
<br>

<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
<tr>
<td colspan='4' bgcolor='#666666' class='style1'><div align='center' class='style43'>DADOS FINANCEIROS E DE CONTRATO</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCCCCC' class='style1'><div align='right'><span class='style39'>Data de Entrada:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
  <input name='data_entrada' type='text' class='campotexto' size='12' maxlength='10' id='data_entrada' value='<?=$Row['data_entrada']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,data_exame.id)"
style='background:#FFFFFF;'/></td>
<td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>
Data do Exame Admissional:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;
<input name='data_exame' type='text' class='campotexto' size='12' maxlength='10' id='data_exame' value='<?=$Row['data_exame']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)"
style='background:#FFFFFF;'/></td>
</tr>
<tr height='30'>
  <td width='23%' bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Local de Pagamento:&nbsp;</div></td>
  <td width='77%' colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
  <input name='localpagamento' type='text' class='campotexto' id='localpagamento' size='25'  value='<?=$Row['localpagamento']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase(); ValidaBanc();"/>
  </span></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'> Cota (R$):&nbsp;</div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class="style39">
    &nbsp;&nbsp;
    <input name='cota' type='text' class='campotexto' id='cota' size='13'  value='<?=$cota?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'"  OnKeyDown="FormataValor(this,event,17,2)"
style='background:#FFFFFF;'/>
  </span></td>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Parcelas:&nbsp;</div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class="style39">
    &nbsp;
    <input name='parcelas' type='text' class='campotexto' id='parcelas' size='3'  value='<?=$Row['parcelas']?>'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
  </span></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCCCCC' class='style1'><div align='right' class='style39'>Observações:&nbsp;</div></td>
  <td colspan='3' bgcolor='#FFFFFF' class='style1'>
  &nbsp;&nbsp;
  <textarea name='observacoes' id='observacoes' class='campotexto' cols='55' rows='4'
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"><?=$Row['observacao']?></textarea></td>
</tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
<tr>
<td width='254%' colspan='4' bgcolor='#666666' class='style1'><div align='center' class='style41'>FINALIZAÇÃO DO CADASTRAMENTO</div></td>
</tr>
<tr height='30'>
<td colspan='4' bgcolor='#FFFFCC' class='style1'>
<div align='center' class='style39'>
  <p>
    
    <span class='style47'>NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</span></p>
O Funcionário participa ATIVAMENTE das Atividades do Projeto?
<label><input type='radio' id='radio1' name='status' value='1' <?=$ativado1?>/>Sim  </label>
- 
<label><input type='radio' id='radio2' name='status' value='0' <?=$ativado2?>/>Não </label>
<br>
<font color=red>Caso NÃO, coloque a data da desativação:</font>
<input name='data_desativacao' type='text' class='campotexto' id='data_desativacao' size='12' maxlength='10' value='<?=$Row['data_saida']?>' 
onFocus="this.style.background='#CCCCCC'" 
onBlur="this.style.background='#FFFFFF'" 
onkeyup="mascara_data(this);"
style='background:#FFFFFF;'/>


<br><br>
<span class="style71">
<input type='submit' name='Submit' value='ENVIAR' />
</span><br />
<div align='center'><span class='style7'>


</span><br />
</div>
</div></td>
</tr>
</table>
<span class='style7'>

<input type='hidden' name='cooperado' value='<?=$coop?>'/>
<input type='hidden' name='regiao' value='<?=$regiao?>'/>
<input type='hidden' name='id_cadastro' value='4'>
<input type='hidden' name='projeto' value='<?=$Row['id_projeto']?>'>
<input type='hidden' name='user' value='<?=$id_user?>'>
<input type='hidden' name='update' value='1'>

</span></tr>
</table>
</form><br><a href='../ver_bolsista.php?reg=<?=$Row['id_regiao']?>&bol=<?=$coop?>&pro=<?=$Row['id_projeto']?>' class='link'><img src='../imagens/voltar.gif' border=0></a>


<script>
function validaForm(){
	d = document.form1;
	if (d.nome.value == "" ){
		alert("O campo Nome deve ser preenchido!");
		d.nome.focus();
		return false;
	}
	if (d.endereco.value == "" ){
		alert("O campo Endereço deve ser preenchido!");
		d.endereco.focus();
		return false;
	}
	if (d.data_nasci.value == "" ){
		alert("O campo Data de Nascimento deve ser preenchido!");
		d.data_nasci.focus();
		return false;
	}
		if (d.rg.value == "" ){
		alert("O campo RG deve ser preenchido!");
		d.rg.focus();
		return false;
	}
	if (d.cpf.value == "" ){
		alert("O campo CPF deve ser preenchido!");
		d.cpf.focus();
		return false;
	}
	if (d.inss_recolher.value == "" ){
		alert("O campo INSS a Recolher deve ser preenchido!");
		d.inss_recolher.focus();
		return false;
	}
	if (d.tipoinss.value == "2" && !(d.inss_recolher.value <= 11)){
		alert("O campo INSS a Recolher não pode passar de 11%!");
		d.inss_recolher.focus();
		return false;
	}
	if (d.localpagamento.value == "" ){
		alert("O campo Local de Pagamento deve ser preenchido!");
		d.localpagamento.focus();
		return false;
	}
	return true;   
}


function ValidaBanc(){
	d = document.form1;
	deposito = "$Row_pg_dep[0]";
	cheque = "$Row_pg_che[0]";
	
	if(document.getElementById("tipopg").value == deposito){
		
	if (document.getElementById("banco").value == 0){
		alert("Selecione um banco!");
		return false;
	}
	
	if (d.agencia.value == "" ){
		alert("O campo Agencia deve ser preenchido!");
		d.agencia.focus();
		return false;
	}
	
	if (d.conta.value == "" ){
		alert("O campo Conta deve ser preenchido!");
		d.conta.focus();
		return false;
	}
	

}

if(document.getElementById("tipopg").value == cheque){
	
	if (document.getElementById("banco").value != 0){
		alert("Para pagamentos em cheque deve selecionar SEM BANCO!");
		return false;
	}
	d.agencia.value = "";
	d.conta.value = "";

}

}
</script>
<?php 

}else{                       

// Log - Edição de Cooperado
$qr_colunas = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$_POST[cooperado]'");
$coluna = mysql_fetch_assoc($qr_colunas);

$qr_dependentes = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$_POST[cooperado]'");
$dependentes = mysql_fetch_assoc($qr_dependentes);

function formata($post_data) {
	  $formatado = implode("-", array_reverse(explode("/", $post_data)));
	  return $formatado;
}

function formata2($post_vazio) {
	  if(empty($post_vazio)) {
		  $formatado = "0";
	  } else {
		  $formatado = $post_vazio;
	  }
	  return $formatado;
}

function formata3($post_virgula) {
	  $formatado = str_replace(",", ".", $post_virgula);
	  return $formatado;
}

$colunas = array($coluna['id_cooperativa'], $coluna['id_curso'], $coluna['locacao'], $coluna['campo3'], $coluna['tipo_contratacao'], $coluna['nome'], $coluna['endereco'], $coluna['bairro'], $coluna['cidade'], $coluna['uf'], $coluna['cep'], $coluna['tel_fixo'], $coluna['tel_cel'], $coluna['tel_rec'], $coluna['data_nasci'], $coluna['naturalidade'], $coluna['nacionalidade'], $coluna['civil'], $coluna['sexo'], $coluna['c_nome'], $coluna['c_cpf'], $coluna['c_nascimento'], $coluna['c_profissao'], $coluna['pai'], $coluna['nacionalidade_pai'], $coluna['mae'], $coluna['nacionalidade_mae'], $coluna['estuda'], $coluna['data_escola'], $coluna['escolaridade'], $coluna['instituicao'], $coluna['curso'], $coluna['num_filhos'], $dependentes['nome1'], $dependentes['data1'], $dependentes['nome2'], $dependentes['data2'], $dependentes['nome3'], $dependentes['data3'], $dependentes['nome4'], $dependentes['data4'], $dependentes['nome5'], $dependentes['data5'], $coluna['cabelos'], $coluna['olhos'], $coluna['peso'], $coluna['altura'], $coluna['etnia'], $coluna['defeito'], $coluna['deficiencia'], $coluna['rg'], $coluna['orgao'], $coluna['uf_rg'], $coluna['data_rg'], $coluna['cpf'], $coluna['reservista'], $coluna['campo1'], $coluna['serie_ctps'], $coluna['uf_ctps'], $coluna['data_ctps'], $coluna['titulo'], $coluna['zona'], $coluna['secao'], $coluna['pis'], $coluna['dada_pis'], $coluna['fgts'], $coluna['e_empresa'], $coluna['e_cnpj'], $coluna['e_endereco'], $coluna['e_bairro'], $coluna['e_cidade'], $coluna['e_estado'], $coluna['e_cep'], $coluna['e_ramo'], $coluna['e_tel'], $coluna['e_ramal'], $coluna['e_fax'], $coluna['e_email'], $coluna['e_tempo'], $coluna['e_profissao'], $coluna['e_cargo'], $coluna['e_dataemissao'], $coluna['e_referencia'], $coluna['e_renda'], $coluna['banco'], $coluna['agencia'], $coluna['conta'], $coluna['tipo_conta'], $coluna['nome_banco'], $coluna['data_entrada'], $coluna['data_exame'], $coluna['localpagamento'], $coluna['tipo_pagamento'], $coluna['observacao']);

$posts = array($_POST['vinculo'], $_POST['atividade'], $_POST['locacao'], $_POST['codigo'], $_POST['contratacao'], $_POST['nome'], $_POST['endereco'], $_POST['bairro'], $_POST['cidade'], $_POST['uf'], $_POST['cep'], $_POST['tel_fixo'], $_POST['tel_cel'], $_POST['tel_rec'], formata($_POST['data_nasci']), $_POST['naturalidade'], $_POST['nacionalidade'], $_POST['civil'], $_POST['sexo'], $_POST['c_nome'], $_POST['c_cpf'], formata($_POST['c_nascimento']), $_POST['c_profissao'], $_POST['pai'], $_POST['nacionalidade_pai'], $_POST['mae'], $_POST['nacionalidade_mae'], $_POST['estuda'], formata($_POST['data_escola']), $_POST['escolaridade'], $_POST['instituicao'], $_POST['curso'], $_POST['filhos'], $_POST['filho_1'], formata($_POST['data_filho_1']), $_POST['filho_2'], formata($_POST['data_filho_2']), $_POST['filho_3'], formata($_POST['data_filho_3']), $_POST['filho_4'], formata($_POST['data_filho_4']), $_POST['filho_5'], formata($_POST['data_filho_5']), $_POST['cabelos'], $_POST['olhos'], $_POST['peso'], $_POST['altura'], $_POST['etnia'], $_POST['defeito'], $_POST['deficiencia'], $_POST['rg'], $_POST['orgao'], $_POST['uf_rg'], formata($_POST['data_rg']), $_POST['cpf'], $_POST['reservista'], $_POST['trabalho'], $_POST['serie_ctps'], $_POST['uf_ctps'], formata($_POST['data_ctps']), $_POST['titulo'], $_POST['zona'], $_POST['secao'], $_POST['pis'], formata($_POST['data_pis']), $_POST['fgts'], $_POST['e_empresa'], $_POST['e_cnpj'], $_POST['e_endereco'], $_POST['e_bairro'], $_POST['e_cidade'], $_POST['e_estado'], $_POST['e_cep'], $_POST['e_ramo'], $_POST['e_tel'], $_POST['e_ramal'], $_POST['e_fax'], $_POST['e_email'], $_POST['e_tempo'], $_POST['e_profissao'], $_POST['e_cargo'], formata($_POST['e_dataemissao']), $_POST['e_referencia'], formata3($_POST['e_renda']), $_POST['banco'], $_POST['agencia'], $_POST['conta'], $_POST['radio_tipo_conta'], $_POST['nome_banco'], formata($_POST['data_entrada']), formata($_POST['data_exame']), $_POST['localpagamento'], $_POST['tipopg'], $_POST['observacoes']);

$campos = array("o vinculo", "o curso", "a unidade", "o código", "o tipo de contratação", "o nome", "o endereço", "o bairro", "a cidade", "o estado", "o CEP", "o telefone fixo", "o telefone celular", "o telefone de recado", "a data de nascimento", "a naturalidade", "a nacionalidade", "o estado civil", "o sexo", "o nome do cônjuge", "o CPF do cônjuge", "a data de nascimento do cônjuge", "a profissão do cônjuge", "o nome do pai", "a nacionalidade do pai", "o nome da mãe", "a nacionalidade da mãe", "o estudo", "o término do estudo", "a escolaridade", "a instituição escolar", "o curso", "o número de filhos", "o nome do 1º filho", "a data de nascimento do 1º filho", "o nome do 2º filho", "a data de nascimento do 2º filho", "o nome do 3º filho", "a data de nascimento do 3º filho", "o nome do 4º filho", "a data de nascimento do 4º filho", "o nome do 5º filho", "a data de nascimento do 5º filho", "a cor do cabelo", "a cor dos olhos", "o peso", "a altura", "a etnia", "a marca", "a deficiência", "o RG", "o órgão do RG", "o estado do RG", "a data do RG", "o CPF", "o certificado de reservista", "a carteira de trabalho", "a série do CTPS", "o estado do CTPS", "a data do CTPS", "o Título de Eleitor", "a zona do Título", "a secão do Título", "o PIS", "a data do PIS", "o FGTS", "o nome da empresa", "o CNPJ da empresa", "o endereço da empresa", "o bairro da empresa", "a cidade da empresa", "o estado da empresa", "a CEP da empresa", "o ramo da empresa", "o telefone da empresa", "o ramal do telefone da empresa", "o fax da empresa", "o email da empresa", "o tempo de serviço na empresa", "a profissão na empresa", "o cargo na empresa", "a data de emissão na empresa", "a referência da empresa", "a renda da empresa", "o banco", "a agência", "a conta", "o tipo de conta", "o nome do banco", "a data de entrada", "a data de exame", "o local de pagamento", "o tipo de pagamento", "as observações");

$n = 0;
$edicao = "";

for ($a=0; $a<=93; $a++) {
	if(($colunas[$a] != $posts[$a]) and (empty($posts[$a]))) {
		$n++;
		$edicao .= " <b>$n)</b> removeu <b>$campos[$a] ($colunas[$a])</b>";
	} elseif(($colunas[$a] != $posts[$a]) and (empty($colunas[$a]))) {
		$n++;
		$edicao .= " <b>$n)</b> inseriu <b>$campos[$a] ($posts[$a])</b>";
	} elseif($colunas[$a] != $posts[$a]) {
		$n++;
		$edicao .= " <b>$n)</b> editou <b>$campos[$a]</b> de <b>$colunas[$a]</b> para <b>$posts[$a]</b>";
	}
}

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$data = date("d/m/Y H:i");
$cabecalho = "($funcionario[0]) $funcionario[nome] às ".$data."h (ip: $ip)";
$local = "Edição de Cooperado - ($coluna[campo3]) $coluna[nome]";
$local_banco = "Edição de Cooperado";
$acao_banco = "Editou o Cooperado ($coluna[campo3]) $coluna[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

$nome_arquivo = "../log/".$funcionario[0].".txt";

$arquivo = fopen("$nome_arquivo", "a");
fwrite($arquivo, "$cabecalho");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "$local");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "$edicao");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "---------------------------------------------------------------");
fwrite($arquivo, "\r\n");
fwrite($arquivo, "\r\n");
fclose($arquivo);
//


//ALTERANDO COOPERADOS

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];
$id_bolsista = $_REQUEST['cooperado'];

//DADOS CONTRATAÇÃO
$vinculo = $_REQUEST['vinculo'];
$id_curso = $_REQUEST['atividade'];
$locacao = $_REQUEST['locacao'];
$tipo_contratacao = $_REQUEST['contratacao'];
$codigo = $_REQUEST['codigo'];

//DADOS CADASTRAIS
$nome = $_REQUEST['nome'];
$sexo = $_REQUEST['sexo'];
$endereco = $_REQUEST['endereco'];
$bairro = $_REQUEST['bairro'];
$cidade = $_REQUEST['cidade'];
$uf = $_REQUEST['uf'];
$cep = $_REQUEST['cep'];
$tel_fixo = $_REQUEST['tel_fixo'];
$tel_cel = $_REQUEST['tel_cel'];
$tel_rec = $_REQUEST['tel_rec'];

$data_nasci = $_REQUEST['data_nasci'];

$naturalidade = $_REQUEST['naturalidade'];
$nacionalidade = $_REQUEST['nacionalidade'];
$civil = $_REQUEST['civil'];

//DOCUMENTAÇÃO
$rg = $_REQUEST['rg'];
$uf_rg = $_REQUEST['uf_rg'];
$secao = $_REQUEST['secao'];
$data_rg = $_REQUEST['data_rg'];

$cpf = $_REQUEST['cpf'];
$conselho = $_REQUEST['conselho'];
$titulo = $_REQUEST['titulo'];
$zona = $_REQUEST['zona'];
$orgao = $_REQUEST['orgao'];

$inss_recolher = $_REQUEST['inss_recolher'];
$tipoinss = $_REQUEST['tipoinss'];

//DADOS DA FAMILIA E OUTROS
$c_nome = $_REQUEST['c_nome'];
$c_cpf = $_REQUEST['c_cpf'];
$c_nascimento = $_REQUEST['c_nascimento'];
$c_profissao = $_REQUEST['c_profissao'];

$pai = $_REQUEST['pai'];
$mae = $_REQUEST['mae'];
$nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
$nacionalidade_mae = $_REQUEST['nacionalidade_mae'];
$estuda = $_REQUEST['estuda'];

$data_escola = $_REQUEST['data_escola'];

$escolaridade = $_REQUEST['escolaridade'];
$instituicao = $_REQUEST['instituicao'];
$curso = $_REQUEST['curso'];

$data_entrada = $_REQUEST['data_entrada'];

$banco = $_REQUEST['banco'];
$agencia = $_REQUEST['agencia'];
$conta = $_REQUEST['conta'];
$nomebanco = $_REQUEST['nomebanco'];
$tipoDeConta = $_REQUEST['radio_tipo_conta'];

//DADOS PROFISSIONAIS
$e_nome = $_REQUEST['e_nome'];
$e_cnpj = $_REQUEST['e_cnpj'];
$e_endereco = $_REQUEST['e_endereco'];
$e_bairro = $_REQUEST['e_bairro'];
$e_cidade = $_REQUEST['e_cidade'];
$e_estado = $_REQUEST['e_estado'];
$e_cep = $_REQUEST['e_cep'];
$e_ramo = $_REQUEST['e_ramo'];
$e_tel = $_REQUEST['e_telefone'];
$e_ramal = $_REQUEST['e_ramal'];
$e_fax = $_REQUEST['e_fax'];
$e_email = $_REQUEST['e_email'];
$e_tempo = $_REQUEST['e_tempo'];
$e_profissao = $_REQUEST['e_profissao'];
$e_cargo = $_REQUEST['e_cargo'];
$e_dataemissao = $_REQUEST['e_dataemissao'];
$e_referencia = $_REQUEST['e_referencia'];
$e_renda = $_REQUEST['e_renda'];
$e_renda = str_replace(".","",$e_renda);
$e_renda = str_replace(",",".",$e_renda);

//REFERENCIA
$r_nome = $_REQUEST['r_nome'];
$r_endereco = $_REQUEST['r_endereco'];
$r_bairro = $_REQUEST['r_bairro'];
$r_cidade = $_REQUEST['r_cidade'];
$r_estado = $_REQUEST['r_estado'];
$r_cep = $_REQUEST['r_cep'];
$r_email = $_REQUEST['r_email'];
$r_tel = $_REQUEST['r_telefone'];
$r_ramal = $_REQUEST['r_ramal'];
$r_fax = $_REQUEST['r_fax'];


// DADOS FINAIS
$localpagamento = $_REQUEST['localpagamento'];
$apolice = $_REQUEST['apolice'];
$campo1 = $_REQUEST['trabalho'];
$campo2 = $_REQUEST['dependente'];

$cota = $_REQUEST['cota'];
$parcelas = $_REQUEST['parcelas'];
$cota = str_replace(".","",$cota);
$cota = str_replace(",",".",$cota);

$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacoes = $_REQUEST['observacoes'];

$medica = $_REQUEST['medica'];

 if(empty($_REQUEST['insalubridade'])){
   $insalubridade = "0";
   }else{
   $insalubridade = $_REQUEST['insalubridade'];
  }

 if(empty($_REQUEST['transporte'])){
  $transporte = "0";
  }else{
  $transporte = $_REQUEST['transporte'];
 }

 if(empty($_REQUEST['impressos2'])){
  $impressos = "0";
  }else{
  $impressos = $_REQUEST['impressos2'];
 }

$plano_medico = $_REQUEST['plano_medico'];

$serie_ctps = $_REQUEST['serie_ctps'];
$uf_ctps = $_REQUEST['uf_ctps'];

$pis_data = $_REQUEST['data_pis'];


//DADOS DO VALE TRANSPORTE
$tipo_vale = $_REQUEST['tipo_vale'];
$num_cartao = $_REQUEST['num_cartao'];
$num_cartao2 = $_REQUEST['num_cartao2'];

$vale1 = $_REQUEST['vale1'];
$vale2 = $_REQUEST['vale2'];
$vale3 = $_REQUEST['vale3'];
$vale4 = $_REQUEST['vale4'];
$vale5 = $_REQUEST['vale5'];
$vale6 = $_REQUEST['vale6'];

//DADOS ADICIONAIS
$ad_noturno = $_REQUEST['ad_noturno'];

$exame_data = $_REQUEST['data_exame'];

$data_ctps = $_REQUEST['data_ctps'];

$reservista = $_REQUEST['reservista'];
$etnia = $_REQUEST['etnia'];
$cabelos = $_REQUEST['cabelos'];
$peso = $_REQUEST['peso'];
$altura = $_REQUEST['altura'];
$olhos = $_REQUEST['olhos'];
$defeito = $_REQUEST['defeito'];
$cipa = $_REQUEST['cipa'];


$filho_1 = $_REQUEST['filho_1'];
$filho_2 = $_REQUEST['filho_2'];
$filho_3 = $_REQUEST['filho_3'];
$filho_4 = $_REQUEST['filho_4'];
$filho_5 = $_REQUEST['filho_5'];

$data_filho_1 = $_REQUEST['data_filho_1'];
$data_filho_2 = $_REQUEST['data_filho_2'];
$data_filho_3 = $_REQUEST['data_filho_3'];
$data_filho_4 = $_REQUEST['data_filho_4'];
$data_filho_5 = $_REQUEST['data_filho_5'];

$status = $_REQUEST['status'];
$data_desativacao = $_REQUEST['data_desativacao'];

if(empty($_REQUEST['foto'])){
$foto = "0";
}else{
$foto = $_REQUEST['foto'];
}

if($foto == "3"){
  $foto_banco = "0";
  $foto_up = "0";
}elseif($foto == "1"){
  $foto_banco = "1";
  $foto_up = "1";
}else{
$vendo_foto = mysql_query("SELECT foto FROM autonomo WHERE id_autonomo = '$id_bolsista'");
$row_vendo_foto = mysql_fetch_array($vendo_foto);

  $foto_banco = "$row_vendo_foto[foto]";
  $foto_up = "0";
}  


/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/


function ConverteData($Data){
 if (strstr($Data, "/"))//verifica se tem a barra /
 {
  $d = explode ("/", $Data);//tira a barra
 $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
 return $rstData;
 } elseif(strstr($Data, "-")){
 $d = explode ("-", $Data);
 $rstData = "$d[2]/$d[1]/$d[0]"; 
 return $rstData;
 }else{
 return "Data invalida";
 }
}

$data_filho_1 = ConverteData($data_filho_1);
$data_filho_2 = ConverteData($data_filho_2);
$data_filho_3 = ConverteData($data_filho_3);
$data_filho_4 = ConverteData($data_filho_4);
$data_filho_5 = ConverteData($data_filho_5);
$data_nasci   = ConverteData($data_nasci);
$data_rg      = ConverteData($data_rg);
$data_escola  = ConverteData($data_escola);
$data_entrada = ConverteData($data_entrada);
$data_desativacao = ConverteData($data_desativacao);
$pis_data     = ConverteData($pis_data);
$exame_data   = ConverteData($exame_data);
$data_ctps = ConverteData($data_ctps);
$c_nascimento = ConverteData($c_nascimento);
$e_dataemissao = ConverteData($e_dataemissao);

$data_alter = date('Y-m-d');

mysql_query ("UPDATE autonomo SET localpagamento = '$localpagamento', locacao = '$locacao', nome = '$nome', sexo = '$sexo', endereco = '$endereco', 
bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', tel_fixo = '$tel_fixo', tel_cel = '$tel_cel', tel_rec = '$tel_rec', 
data_nasci = '$data_nasci', naturalidade = '$naturalidade', nacionalidade = '$nacionalidade', civil = '$civil', rg = '$rg', orgao = '$orgao', 
data_rg = '$data_rg', cpf = '$cpf', titulo = '$titulo', zona = '$zona', secao = '$secao', inss = '$inss_recolher',tipo_inss = '$tipoinss', pai = '$pai', nacionalidade_pai = '$nacionalidade_pai', 
mae = '$mae', nacionalidade_mae = '$nacionalidade_mae', estuda = '$estuda', data_escola = '$data_escola', escolaridade = '$escolaridade', 
instituicao = '$instituicao', curso = '$curso', banco = '$banco', agencia ='$agencia', conta = '$conta',tipo_conta = '$tipoDeConta', 
status = '$status', data_saida = '$data_desativacao', campo3 = '$codigo', tipo_contratacao = '$tipo_contratacao', id_curso = '$id_curso', 
apolice = '$apolice', data_entrada = '$data_entrada', campo2 = '$dependente', campo1 = '$campo1', data_exame = '$exame_data', 
reservista = '$reservista', etnia = '$etnia', cabelos = '$cabelos', peso = '$peso', altura = '$altura', olhos = '$olhos', defeito = '$defeito', cipa = '$cipa', 
ad_noturno = '$ad_noturno', plano = '$plano', assinatura = '$assinatura', distrato = '$assinatura2', outros = '$assinatura3', pis = '$pis', 
dada_pis = '$pis_data', data_ctps = '$data_ctps', serie_ctps = '$serie_ctps', uf_ctps = '$uf_ctps', uf_rg = '$uf_rg', fgts = '$fgts', 
insalubridade = '$insalubridade', transporte = '$transporte', medica = '$medica', tipo_pagamento = '$tipopg', nome_banco = '$nomebanco', 
num_filhos = '$filhos', observacao = '$observacoes', foto = '$foto_banco', id_cooperativa = '$vinculo', 
c_nome = '$c_nome', c_cpf = '$c_cpf', c_nascimento = '$c_nascimento', c_profissao = '$c_profissao', e_empresa = '$e_nome', e_cnpj = '$e_cnpj', 
e_ramo = '$e_ramo', e_endereco = '$e_endereco', e_bairro = '$e_bairro', e_cidade = '$e_cidade', e_estado = '$e_estado', e_cep = '$e_cep', 
e_tel = '$e_tel', e_ramal = '$e_ramal', e_fax = '$e_fax', e_email = '$e_email', e_tempo = '$e_tempo', e_profissao = '$e_profissao', 
e_cargo = '$e_cargo', e_renda = '$e_renda', e_dataemissao = '$e_dataemissao', e_referencia = '$e_referencia', r_nome = '$r_nome', 
r_endereco = '$r_endereco', r_bairro = '$r_bairro', r_cidade = '$r_cidade', r_estado = '$r_estado', r_cep = '$r_cep', r_tel = '$r_tel', 
r_ramal = '$r_ramal', r_fax = '$r_fax', r_email = '$r_email', dataalter = '$data_alter', useralter = '$id_user', cota = '$cota', 
parcelas = '$parcelas' where id_autonomo = '$id_bolsista' LIMIT 1") or die ("Erro no UPDATE:<br><br><font color=red> ".mysql_error());

/*
//VALE TRANSPORTE
if($transporte == "1"){
mysql_query ("insert into rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,
id_tarifa5,id_tarifa6,cartao1,cartao2) values 
('$row_id_participante','$regiao','$projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$num_cartao','$num_cartao2')") or die ("$mensagem_erro - 2.3<br><br>".mysql_error());
}
*/

//DEPENDENTES
//VERIFICA SE O BOLSISTA JA ESTÁ CADASTRADO NA TABELA DEPENDENTES
$result_cont1 = mysql_query ("SELECT id_bolsista FROM dependentes WHERE id_bolsista = '$id_bolsista' and id_projeto = '$id_projeto'");
$row_cont1 = mysql_num_rows($result_cont1);

if($row_cont1 == "0"){

mysql_query ("INSERT INTO dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5) values 
('$regiao','$id_projeto','$id_bolsista','$tipo_contratacao','$nome','$data_filho_1','$filho_1','$data_filho_2','$filho_2','$data_filho_3','$filho_3',
'$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or die ("<center>O SERVIDOR NÃO RESPONDEU CONFORME DEVERIA...<br>". mysql_error());

}else{

mysql_query ("update dependentes set data1 = '$data_filho_1', nome1 = '$filho_1', data2 = '$data_filho_2', nome2 = '$filho_2', data3 = '$data_filho_3', nome3 = '$filho_3', data4 = '$data_filho_4', nome4 = '$filho_4', data5 = '$data_filho_5', nome5 = '$filho_5' where id_projeto = '$id_projeto' and id_bolsista = '$id_bolsista' ") or die ("houve algum erro de digitação na terceira query (update de dependentes): ". mysql_error());

}



//FAZENDO O UPLOAD DA FOTO
$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

if($foto_up == "1"){
if(!$arquivo){
    $mensagem = "Não acesse esse arquivo diretamente!";
}else{// Imagem foi enviada, então a move para o diretório desejado
    $nome_arq = str_replace(" ", "_", $nome);	
    $tipo_arquivo = ".gif";
	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "../fotos/";
	$nome_tmp = $regiao."_".$id_projeto."_".$id_bolsista.$tipo_arquivo;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

}
}


print "
<script>
alert(\"Informações gravadas com sucesso!\");
location.href=\"../bolsista.php?projeto=$id_projeto&regiao=$regiao\"
</script>";

}


?>
</body></html>