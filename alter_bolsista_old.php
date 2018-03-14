<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
} else {

include "conn.php";

if (empty($_REQUEST['update'])) {

$id_bolsista = $_REQUEST['bol'];
$tabela = $_REQUEST['tab'];
$id_projeto = $_REQUEST['pro'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '27' and id_clt = '$id_bolsista'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('27','$id_bolsista','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bolsista' and tipo = '27'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$result = mysql_query("Select *, date_format(data_nasci, '%d/%m/%Y')as data_nascimento, date_format(data_rg, '%d/%m/%Y')as data_rg2, date_format(data_escola, '%d/%m/%Y')as data_escola2, date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_exame, '%d/%m/%Y')as data_exame, date_format(data_saida, '%d/%m/%Y')as data_saida, date_format(data_ctps, '%d/%m/%Y')as data_ctps2, date_format(dada_pis, '%d/%m/%Y')as dada_pis2 from autonomo where id_autonomo = '$id_bolsista'");
$row = mysql_fetch_array($result);

$result_vale = mysql_query("Select * from  vale where id_bolsista = '$row[0]' and id_projeto = '$row[id_projeto]'");
$row_vale = mysql_fetch_array($result_vale);

$result_depe = mysql_query ("SELECT *, date_format(data1, '%d/%m/%Y')as datas1, date_format(data2, '%d/%m/%Y')as datas2, date_format(data3, '%d/%m/%Y')as datas3, date_format(data4, '%d/%m/%Y')as datas4, date_format(data5, '%d/%m/%Y')as datas5 FROM dependentes WHERE id_bolsista = '$id_bolsista' and id_projeto = '$id_projeto' AND contratacao = '$row[tipo_contratacao]'");
$row_depe = mysql_fetch_array($result_depe);

$result_pro = mysql_query("Select * from projeto where id_projeto = '$row[id_projeto]'");
$row_pro = mysql_fetch_array($result_pro);

$result_reg = mysql_query("Select * from regioes where id_regiao = '$row[id_regiao]'");
$row_reg = mysql_fetch_array($result_reg);

$result_curso = mysql_query("Select * from curso where id_curso = '$row[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);


if($row['insalubridade'] == "1"){
$chek1 = "checked";
}else{
$chek1 = "";
}

if($row_vale['status_vale'] == "1"){
$chek2 = "checked";
}else{
$chek2 = "";
}

if($row['assinatura'] == "1"){
$selected_ass_sim = "checked";
$selected_ass_nao = "";
}else if($row['assinatura'] == "0"){
$selected_ass_sim = "";
$selected_ass_nao = "checked";
}else{
$selected_ass_sim = "";
$selected_ass_nao = "";
$mensagem_ass = "<font color=red size=1><b>Não marcado</b></font>";
}

if($row['distrato'] == "1"){
$selected_ass_sim2 = "checked";
$selected_ass_nao2 = "";
}else if($row['distrato'] == "0"){
$selected_ass_sim2 = "";
$selected_ass_nao2 = "checked";
}

if($row['outros'] == "1"){
$selected_ass_sim3 = "checked";
$selected_ass_nao3 = "";
}else if($row['outros'] == "0"){
$selected_ass_sim3 = "";
$selected_ass_nao3 = "checked";
}

if($row['sexo'] == "M"){
$chekH = "checked";
$chekF = "";
$mensagem_sexo = "";
}else if($row['sexo'] == "F"){
$chekH = "";
$chekF = "checked";
$mensagem_sexo = "";
}else{
$chekH = "";
$chekF = "";
$mensagem_sexo = "<font color=red size=1><b>Cadastrar Sexo</b></font>";
}


if($row['medica'] == "0"){
$chek_medi0 = "checked";
$chek_medi1 = "";
$mensagem_medi = "";
}else if($row['medica'] == "1"){
$chek_medi0 = "";
$chek_medi1 = "checked";
$mensagem_medi = "";
}else{
$chek_medi0 = "";
$chek_medi1 = "";
$mensagem_medi = "<font color=red size=1><b>Selecione uma opção</b></font>";
}

if($row['plano'] == "1"){
$selected_planoF = "selected";
$selected_planoI = "";
}else{
$selected_planoF = "";
$selected_planoI = "selected";
}

if($row_vale['tipo_vale'] == "1"){
$selected_valeC = "selected";
$selected_valeP = "";
$selected_valeA = "";
}else if($row_vale['tipo_vale'] == "2"){
$selected_valeC = "";
$selected_valeP = "selected";
$selected_valeA = "";
}else if($row_vale['tipo_vale'] == "3"){
$selected_valeC = "";
$selected_valeP = "";
$selected_valeA = "selected";
}

if($row['ad_noturno'] == "1"){
$checkad_noturno1 = "checked";
$checkad_noturno0 = "";
}else{
$checkad_noturno1 = "";
$checkad_noturno0 = "checked";
}

if($row['estuda'] == "sim"){
$chekS = "checked";
$chekN = "";
}else{
$chekS = "";
$chekN = "checked";
}

if($row['cipa'] == "1"){
$checkedcipa1 = "checked";
$checkedcipa0 = "";
}else{
$checkedcipa1 = "";
$checkedcipa0 = "checked";
}

if($row['status'] == "1"){
$AVISO = "";
$status_ativado = "checked";
$status_desativado = "";
$data_desativacao = "";
}else{
$AVISO = "Este Funcionário Encontra-se DESATIVADO";
$status_ativado = "";
$status_desativado = "checked";
$data_desativacao = "$row[data_saida]";
}

if($row['foto'] == "1"){
$foto = "Deseja remover a foto? <input name='foto' type='checkbox' id='foto' value='3'/> Sim";
}else{
$foto = "<input name='foto' type='checkbox' id='foto' value='1' onClick=\"document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;\">";
}


//----- INI -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$funcionario = mysql_fetch_array($qr_funcionario);
$ip = $_SERVER['REMOTE_ADDR'];
$local_banco = "Edição de Bolsista";
$acao_banco = "Editando o Bolsista ($row[campo3]) $row[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

//----- FIM -- GRAVANDO AS INFORMAÇÕES DO LOGIN NA TABELA LOG


print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\">
<script language=\"javascript\" src=\"js/ramon.js\"></script>

</head>
<script language=\"JavaScript\">

function VerificaCoop(){
	var TipoC = document.all.tipo_bol.value;
	
	if(TipoC == 3){
		document.all.linhacoop.style.display='';
	}else{
		document.all.linhacoop.style.display='none';
	}
	
}

</script>
<style type='text/css'>
<!--
body {
	background-color: #5C7E59;
}
.style1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 12px;
}
.style3 {font-size: 12px}
.style6 {color: #003300}
.style7 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
.style37 {font-family: Arial, Helvetica, sans-serif}
.style39 {font-family: Arial, Helvetica, sans-serif; color: #003300;}
.style40 {font-weight: bold; font-family: Arial, Helvetica, sans-serif;}
.style41 {
	color: #FFFFFF;
	font-size: 16px;
}
.style42 {font-weight: bold; color: #003300; font-family: Arial, Helvetica, sans-serif;}
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
-->
</style>";

print "
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
<body bgcolor='#D7E6D5' onLoad='VerificaCoop(); drogadebanco()'>

<form action='alter_bolsista.php' method='post' id='form1' name='form1' onSubmit=\"return validaForm()\" enctype='multipart/form-data'>

<table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#5C7E59' class='linha' align='center'>
<tr>
<td colspan=4>
<div align='center' class='style43'>
EDITANDO CADASTRO<br><br>
</div>
</td>
</tr>

<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='2' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS DO PROJETO</div></td>
  </tr>
  <tr>
    <td height='30' bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>
	Código:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
    <input name='codigo' type='text' class='campotexto' id='codigo' size='10' value='$row[campo3]'
        onFocus=\"document.all.codigo.style.background='#CCFFCC'\"
        onBlur=\"document.all.codigo.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' /></td>
  </tr>
  <tr>
    <td height='30' width='23%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Tipo de Contratação:&nbsp;</span></div></td>
    <td width='77%' bgcolor='#FFFFFF' class='style1'><span class='style39'> &nbsp;&nbsp;
        <select name='tipo_bol' id='tipo_bol' class='campotexto' onchange='VerificaCoop()'>";
if($row[tipo_contratacao] == "1"){
print "
<option value=1 selected>Autônomo</option>
<option value=3>COOPERADO</option>";
}else if($row[tipo_contratacao] == "2"){
print "
<option value=1>Autônomo</option>
<option value=3>COOPERADO</option>";
}else{
print "
<option value=1>Autônomo</option>
<option value=3 selected>COOPERADO</option>";
}
print "
        </select>
    </span></td>
  </tr>
  <tr>
    <td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Projeto:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
	<span class='style39'>&nbsp; &nbsp;$row_pro[2]
	
    </span></td>
  </tr>
  <tr>
    <td height='30' height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Curso:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <select name='id_curso' id='id_curso' class='campotexto'>";

$result_grupo = mysql_query("SELECT * FROM curso where campo3 = '$row[id_projeto]' and (tipo = '1' or tipo='3') ORDER BY nome");
while ($row_grupo = mysql_fetch_array($result_grupo)){
  if($row_grupo['id_curso'] == "$row_curso[id_curso]"){
   print "<option value='$row_grupo[id_curso]' selected>$row_grupo[0] - $row_grupo[campo2] / $row_grupo[salario] - $row_grupo[campo1]</option>";   
  }else{
  print "<option value='$row_grupo[id_curso]'>$row_grupo[0] - $row_grupo[campo2] / $row_grupo[salario] - $row_grupo[campo1]</option>";
  }
}

print "
     </select>
    </span></td>
  </tr>
  <tr>
    <td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39' onClick='VerificaCoop()'><span class='style37'>Unidade:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <select name='lotacao' class='campotexto' id='lotacao'>";

$result_unidade = mysql_query("SELECT * FROM unidade where id_regiao = '$row[id_regiao]'  and campo1 = '$row[id_projeto]' ORDER BY unidade");
while ($row_unidade = mysql_fetch_array($result_unidade)){
  if($row_unidade['unidade'] == "$row[locacao]"){
   print "<option selected>$row_unidade[unidade]</option>";   
  }else{
  print "<option>$row_unidade[unidade]</option>";
  }
}

print "
          </select>
    </span></td>
  </tr>
  
  <tr id='linhacoop'>
    <td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Cooperativa:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <select name='cooperativa' class='campotexto' id='cooperativa'>";

$RECoop = mysql_query("SELECT id_coop,nome FROM cooperativas where id_regiao = '$row[id_regiao]' ORDER BY nome");
while ($RowCoop = mysql_fetch_array($RECoop)){
  if($RowCoop['0'] == "$row[id_cooperativa]"){
   print "<option value='$RowCoop[0]' selected>$RowCoop[0] - $RowCoop[nome]</option>";
  }else{
  print "<option value='$RowCoop[0]'>$RowCoop[0] - $RowCoop[nome]</option>";
  }
}

print "
          </select>
    </span></td>
  </tr>
  
  
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='8' bgcolor='#003300' class='style1'><div align='center' class='style6 style3 style40 style42'>
      <div align='center' class='style41'>DADOS CADASTRAIS</div>
    </div></td>
  </tr>
  
  <tr height='30'>
    <td width='13%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='right'><span class='style37'>Nome:&nbsp;</span></div>
    </div></td>
    <td width='87%' colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
        <input name='nome' type='text' class='campotexto' id='nome' size='75' value='$row[nome]'
        onFocus=\"document.all.nome.style.background='#CCFFCC'\"
        onBlur=\"document.all.nome.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
      </span></div>
    </div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='right'><span class='style37'>Endereco:&nbsp;</span></div>
    </div></td>
    <td colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
     <input name='endereco' type='text' class='campotexto' id='endereco' size='75' value='$row[endereco]'
        onFocus=\"document.all.endereco.style.background='#CCFFCC'\" 
        onBlur=\"document.all.endereco.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
      </span></div>
    </div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='right'><span class='style37'>Bairro:&nbsp;</span></div>
    </div></td>
    <td bgcolor='#FFFFFF' class='style1'><div align='left' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
        <input name='bairro' type='text' class='campotexto' id='bairro' size='15' value='$row[bairro]'
        onFocus=\"document.all.bairro.style.background='#CCFFCC'\" 
        onBlur=\"document.all.bairro.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
        &nbsp;&nbsp;</span></div>
    </div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='right'><span class='style37'> Cidade:&nbsp;</span></div>
    </div></td>
    <td bgcolor='#FFFFFF' class='style1'><div align='left' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
        <input name='cidade' type='text' class='campotexto' id='cidade' size='12' value='$row[cidade]'
        onFocus=\"document.all.cidade.style.background='#CCFFCC'\" 
        onBlur=\"document.all.cidade.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
      </span></div>
    </div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='right'><span class='style37'>UF:&nbsp;</span></div>
    </div></td>
    <td bgcolor='#FFFFFF' class='style1'><div align='left' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
        <input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' value='$row[uf]'
        onFocus=\"document.all.uf.style.background='#CCFFCC'\" 
        onBlur=\"document.all.uf.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"
        onkeyup=\"pula(2,this.id,cep.id)\" />
      </span></div>
    </div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='right'><span class='style37'>CEP:&nbsp;</span></div>
    </div></td>
    <td bgcolor='#FFFFFF' class='style1'><div align='left' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
        <input name='cep' type='text' class='campotexto' id='cep' size='10' maxlength='9' value='$row[cep]'
        style='background:#FFFFFF; text-transform:uppercase;'
        onFocus=\"document.all.cep.style.background='#CCFFCC'\" 
        onBlur=\"document.all.cep.style.background='#FFFFFF'\"
        OnKeyPress=\"formatar('#####-###', this)\" 
        onKeyUp=\"pula(9,this.id,tel_fixo.id)\" />
      </span></div>
    </div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='right'><span class='style37'>Telefones:&nbsp;</span></div>
    </div></td>
    <td colspan='2' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
      <div align='center'><span class='style37'>Fixo:&nbsp;</span></div>
    </div></td>
    <td bgcolor='#FFFFFF' class='style1'><div align='center' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_fixo' type='text' id='tel_fixo' size='14' value='$row[tel_fixo]'
onKeyPress=\"return(TelefoneFormat(this,event))\" 
onKeyUp=\"pula(13,this.id,tel_cel.id)\" 
onFocus=\"document.all.tel_fixo.style.background='#CCFFCC'\" 
onBlur=\"document.all.tel_fixo.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' class='campotexto'>
        </span></div>
    </div></td>
    <td bgcolor='#CCFFCC' class='style1'> <div align='center' class='style39'>
      <div align='right'><span class='style37'>Cel:&nbsp;</span></div>
    </div></td>
    <td bgcolor='#FFFFFF' class='style1'><div align='center' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='14' value='$row[tel_cel]'  onKeyPress=\"return(TelefoneFormat(this,event))\" 
onKeyUp=\"pula(13,this.id,tel_rec.id)\" 
onFocus=\"document.all.tel_cel.style.background='#CCFFCC'\" 
onBlur=\"document.all.tel_cel.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' />
        &nbsp;</span></div>
    </div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='center' class='style39'>
      <div align='right'><span class='style37'>Recado:&nbsp;</span></div>
    </div></td>
    <td bgcolor='#FFFFFF' class='style1'><div align='center' class='style39'>
      <div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='14' value='$row[tel_rec]' 
onKeyPress=\"return(TelefoneFormat(this,event))\" 
onKeyUp=\"pula(13,this.id,data_nasc.id)\" 
onFocus=\"document.all.tel_rec.style.background='#CCFFCC'\" 
onBlur=\"document.all.tel_rec.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' />
        </span></div>
    </div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Data de Nascimento:&nbsp;</span></div></td>
    <td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;
      
<input name='data_nasc' type='text' id='data_nasc' size='12' class='campotexto' value='$row[data_nascimento]'
onKeyUp=\"mascara_data(this); pula(10,this.id,naturalidade.id)\"
onFocus=\"document.all.data_nasc.style.background='#CCFFCC'\" 
onBlur=\"document.all.data_nasc.style.background='#FFFFFF'\" 
style='background:#FFFFFF;'>
	  
    </span> <span class='style6 style37'>&nbsp;</span></td>
    <td bgcolor='#CCFFCC' class='style1'>
	<div align='right' class='style39'>Naturalidade:&nbsp;</div></td>
    <td colspan='2' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
<input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='15'
  value='$row[naturalidade]'
        onFocus=\"document.all.naturalidade.style.background='#CCFFCC'\" 
        onBlur=\"document.all.naturalidade.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nacionalidade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
        <input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='12' 
		value='$row[nacionalidade]'
        onFocus=\"document.all.nacionalidade.style.background='#CCFFCC'\" 
        onBlur=\"document.all.nacionalidade.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
   </td>
  </tr>

  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estado Civil:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
       
	   <input name='civil' type='text' value='$row[civil]' class='campotexto' id='civil' size='12'
	   onFocus=\"document.all.civil.style.background='#CCFFCC'\" 
        onBlur=\"document.all.civil.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\">
	   
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'  class='style39'>Sexo:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
	
	<table class=linha><tr>
<td>&nbsp;&nbsp;<label><input type='radio' id='sexo' name='sexo' value='M' $chekH>  Masculino </label></td>
<td>&nbsp;&nbsp;<label><input type='radio' id='sexo' name='sexo' value='F' $chekF> Feminino </label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$mensagem_sexo</td>
</tr></table>

	</td>
  </tr>
  
  
  <tr>
    <td colspan='8' bgcolor='#CCFF99' class='style1'><div align='center' class='style44'>DADOS DA FAMÍLIA E EDUCACIONAIS</div></td>
  </tr>
  <tr height='30'>
    
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Filiação - Pai:&nbsp;</div></td>
    <td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
        <input name='pai' type='text' class='campotexto' id='pai' size='75' value='$row[pai]'
        onFocus=\"document.all.pai.style.background='#CCFFCC'\" 
        onBlur=\"document.all.pai.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Pai:</span>&nbsp;&nbsp;
	
        <input name='nacionalidade_pai' type='text' class='campotexto' id='nacionalidade_pai' size='15' value='$row[nacionalidade_pai]'
        onFocus=\"document.all.nacionalidade_pai.style.background='#CCFFCC'\" 
        onBlur=\"document.all.nacionalidade_pai.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>	
	 </span>

</td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Filiação - Mãe:&nbsp;</div></td>
    <td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
        <input name='mae' type='text' class='campotexto' id='mae' size='75' value='$row[mae]'
        onFocus=\"document.all.mae.style.background='#CCFFCC'\" 
        onBlur=\"document.all.mae.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Mãe:</span>&nbsp;&nbsp;
	
        <input name='nacionalidade_mae' type='text' class='campotexto' id='nacionalidade_mae' size='15' value='$row[nacionalidade_mae]'
        onFocus=\"document.all.nacionalidade_mae.style.background='#CCFFCC'\" 
        onBlur=\"document.all.nacionalidade_mae.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>	
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estuda Atualmente?&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'>


<table class=linha><tr>
<td>&nbsp;&nbsp;<label><input type='radio' name='estuda' value='sim' $chekS> Sim </label></td>
<td>&nbsp;&nbsp;<label><input type='radio' name='estuda' value='nao' $chekN> Não </label>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$mensagem_sexo</td>
</tr></table>

	  
	  
	  </td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Término em:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;

<input name='data_escola' type='text' id='data_escola' size='12' class='campotexto' value='$row[data_escola2]'
onKeyUp=\"mascara_data(this); pula(10,this.id,escolaridade.id)\" maxlength='10' 
onFocus=\"document.all.data_escola.style.background='#CCFFCC'\" 
onBlur=\"document.all.data_escola.style.background='#FFFFFF'\" 
style=\"background:#FFFFFF\">
	  
	  
	  </td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Escolaridade:&nbsp;</div></td>
    <td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;&nbsp;
<select name='escolaridade' class='campotexto'>"; ?>
<option value="12">12 - Não informado</option>
<?php $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on' LIMIT 0,11");
while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) { ?>
<option value="<?=$escolaridade['id']?>"<? if($row['escolaridade'] == $escolaridade['id']) { ?> selected<? } ?>>
<?=$escolaridade['cod']?> - <?=$escolaridade['nome']?>
</option>
<?php } print "
</select>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Instituíção:&nbsp;</div></td>
    <td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;
        <input name='instituicao' type='text' class='campotexto' id='instituicao' size='20' 
		value='$row[instituicao]'
        onFocus=\"document.all.instituicao.style.background='#CCFFCC'\" 
        onBlur=\"document.all.instituicao.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Curso:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
        <input name='curso' type='text' class='campotexto' id='zona' size='20' value='$row[curso]'
        onFocus=\"document.all.curso.style.background='#CCFFCC'\" 
        onBlur=\"document.all.curso.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Número de Filhos:&nbsp;</div></td>
    <td colspan='7' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;&nbsp;
    <input name='filhos' type='text' class='campotexto  style37' id='filhos' size='2' value='$row[num_filhos]'
        onFocus=\"document.all.filhos.style.background='#CCFFCC'\" 
        onBlur=\"document.all.filhos.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;'/>
    <div align='right'></div>    <div align='right'></div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;&nbsp;
     
<input name='filho_1' type='text' class='campotexto' id='filho_1' size='50' value='$row_depe[nome1]'
onFocus=\"document.all.filho_1.style.background='#CCFFCC'\" 
onBlur=\"document.all.filho_1.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>

    </td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
      &nbsp;&nbsp;
<input name='data_filho_1' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_1'
value='$row_depe[datas1]'
        onFocus=\"document.all.data_filho_1.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_filho_1.style.background='#FFFFFF'\" 
		onKeyUp=\"mascara_data(this); pula(10,this.id,filho_2.id)\"
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
      <input name='filho_2' type='text' class='campotexto' id='filho_2' size='50' value='$row_depe[nome2]'
        onFocus=\"document.all.filho_2.style.background='#CCFFCC'\" 
        onBlur=\"document.all.filho_2.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span>      <div align='right' class='style39'></div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
    &nbsp;&nbsp;
    <input name='data_filho_2' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_2'
	value='$row_depe[datas2]'	
        onFocus=\"document.all.data_filho_2.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_filho_2.style.background='#FFFFFF'\" 
		onKeyUp=\"mascara_data(this); pula(10,this.id,filho_3.id)\"
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
      <input name='filho_3' type='text' class='campotexto' id='filho_3' size='50' value='$row_depe[nome3]'
onFocus=\"document.all.filho_3.style.background='#CCFFCC'\" 
onBlur=\"document.all.filho_3.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    &nbsp;</span>      <div align='right' class='style39'></div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
    &nbsp;&nbsp;
    <input name='data_filho_3' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_3' 
	value='$row_depe[datas3]'
        onFocus=\"document.all.data_filho_3.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_filho_3.style.background='#FFFFFF'\" 
		onKeyUp=\"mascara_data(this); pula(10,this.id,filho_4.id)\"
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
      <input name='filho_4' type='text' class='campotexto' id='filho_4' size='50' value='$row_depe[nome4]'
onFocus=\"document.all.filho_4.style.background='#CCFFCC'\" 
onBlur=\"document.all.filho_4.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span>      <div align='right' class='style39'></div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
    &nbsp;&nbsp;
    <input name='data_filho_4' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_4' 
	value='$row_depe[datas4]' 
        onFocus=\"document.all.data_filho_4.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_filho_4.style.background='#FFFFFF'\" 
		onKeyUp=\"mascara_data(this); pula(10,this.id,filho_5.id)\"
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;&nbsp;
      <input name='filho_5' type='text' class='campotexto' id='filho_5' size='50' value='$row_depe[nome5]'
onFocus=\"document.all.filho_5.style.background='#CCFFCC'\" 
onBlur=\"document.all.filho_5.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span>      <div align='right' class='style39'></div></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
    &nbsp;&nbsp;
    <input name='data_filho_5' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_5'
        onFocus=\"document.all.data_filho_5.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_filho_5.style.background='#FFFFFF'\" 
		onkeyup=\"mascara_data(this)\" value='$row_depe[datas5]'
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  
  <tr>
    <td colspan='8' bgcolor='#CCFF99' class='style1'><div align='center' class='style44'>APARÊNCIA</div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'>
	<div align='right' class='style39'>Cabelos:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;<select name='cabelos' class='campotexto' id='cabelos'>";
 
 $result_cabelos = mysql_query("SELECT * FROM tipos WHERE tipo = '1' and status = '1'");
 while($row_cabelos = mysql_fetch_array($result_cabelos)){
   if($row['cabelos'] == $row_cabelos['nome']){
     print "<option selected>$row_cabelos[nome]</option>";
   }else{
     print "<option>$row_cabelos[nome]</option>";
   }
}
 
print "</select>
    </td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Olhos:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>&nbsp;&nbsp;
          <select name='olhos' class='campotexto' id='olhos'>";
	  
   $result_olhos = mysql_query("SELECT * FROM tipos WHERE tipo = '2' and status = '1'");
   while($row_olhos = mysql_fetch_array($result_olhos)){
       if($row['olhos'] == $row_olhos['nome']){
          print "<option selected>$row_olhos[nome]</option>";
       }else{
          print "<option>$row_olhos[nome]</option>";
      }
    }
 
print "
</select>
    </span></span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Peso:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
      &nbsp;&nbsp;
      <input name='peso' type='text' class='campotexto' id='peso' size='5' value='$row[peso]'
        onFocus=\"document.all.peso.style.background='#CCFFCC'\" 
        onBlur=\"document.all.peso.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' />
    </span></span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Altura:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
      &nbsp;&nbsp;
      <input name='altura' type='text' class='campotexto' id='altura' size='5' value='$row[altura]'
        onFocus=\"document.all.altura.style.background='#CCFFCC'\" 
        onBlur=\"document.all.altura.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' />
&nbsp;&nbsp; </span></span></td>
     <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Etnia:&nbsp;</div></td>
      <td bgcolor='#CCFFCC'>
	  <select name='etnia' class='campotexto'>"; ?>
    <option value="6">Não informado</option>
<? $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' LIMIT 0,5");
while($etnia = mysql_fetch_assoc($qr_etnias)) { ?>
<option value="<?=$etnia['id']?>"<? if($row['etnia'] == $etnia['id']) { ?> selected<? } ?>><?=$etnia['cod']?> - <?=$etnia['nome']?></option>
<?php } print "</select>
     </td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Marcas ou Cicatriz aparente:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
      <input name='defeito' type='text' class='campotexto' id='defeito' size='18' value='$row[defeito]'
        onFocus=\"document.all.defeito.style.background='#CCFFCC'\" 
        onBlur=\"document.all.defeito.style.background='#FFFFFF'\" 
        style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </td>
  </tr>
  <tr>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Deficiência:</div></td>
  <td bgcolor='#FFFFFF'>
  <select name='deficiencia' class='campotexto'>"; ?>
<option value="">Não é portador de deficiência</option>
<? $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
while($deficiencia = mysql_fetch_assoc($qr_deficiencias)) { ?>
<option value="<?=$deficiencia['id']?>"<? if($row['deficiencia'] == $deficiencia['id']) { ?> selected="selected"<? } ?>><?=$deficiencia['nome']?></option>
<?php } print "</select>
    <td colspan='7' class='campotexto' class='style1'>&nbsp;</td>
  </tr>
  <tr height='30' id='ancora_foto'>
    <td colspan='8' bgcolor='#FFFFFF' class='style1'>
	<div align='center' class='style39'>
Foto:&nbsp;&nbsp;&nbsp;
$foto
&nbsp;&nbsp;&nbsp;&nbsp;
<table border='0' cellspacing='0' cellpadding='0' class='linha' id='tablearquivo' style='display:none'>
<tr>
<td width='27%' align='right'><span class='style39'>ENVIAR FOTO:</span></td>
<td width='73%' align='right'><span class='style16'> 
<input name='arquivo' type='file' id='arquivo' size='60' />
<span class='style16'> &nbsp;&nbsp;&nbsp;&nbsp;</span> </span></td>
</tr>
</table>

</div></td>
</tr>
</table>


<br />



<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='8' bgcolor='#003300' class='style1'><div align='center' class='style43'>DOCUMENTAÇÃO</div></td>
  </tr>
  <tr height='30'>
    <td width='16%' bgcolor='#CCFFCC' class='style1'>
	<div align='right' class='style39'>Nº do RG:&nbsp;</div></td>
    <td width='12%' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
	<input name='rg' type='text' id='rg' size='13' maxlength='14' class='campotexto' value='$row[rg]'
                OnKeyPress=\"formatar('##.###.###-###', this)\" 
                onFocus=\"document.all.rg.style.background='#CCFFCC'\" 
                onBlur=\"document.all.rg.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'
				onkeyup=\"pula(14,this.id,orgao.id)\">
    </td>
    <td width='15%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Orgão Expedidor:&nbsp;</div></td>
    <td width='9%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='orgao' type='text' class='campotexto' id='orgao' size='8' value='$row[orgao]'
onFocus=\"document.all.orgao.style.background='#CCFFCC'\" 
onBlur=\"document.all.orgao.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span> </td>
    <td width='5%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td width='7%' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
    <input name='uf_rg' type='text' class='campotexto' id='uf_rg' size='2' maxlength='2' value='$row[uf_rg]'
                onfocus=\"document.all.uf_rg.style.background='#CCFFCC'\" 
                onblur=\"document.all.uf_rg.style.background='#FFFFFF'\"
         style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\" onkeyup=\"pula(2,this.id,data_rg.id)\"/></td>
    <td width='18%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data Expedição:&nbsp;</div></td>
    <td width='18%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
      <input name='data_rg' type='text' class='campotexto' size='12' maxlength='10' value='$row[data_rg2]'
		id='data_rg'
        onFocus=\"document.all.data_rg.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_rg.style.background='#FFFFFF'\" 
		onkeyup=\"mascara_data(this); pula(10,this.id,cpf.id)\"
        style='background:#FFFFFF;'/>
		
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>CPF:&nbsp;</div></td>
    <td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='cpf' type='text' class='campotexto' id='cpf' size='17' maxlength='14' value='$row[cpf]'
                OnKeyPress=\"formatar('###.###.###-##', this)\" 
                onFocus=\"document.all.cpf.style.background='#CCFFCC'\" 
                onBlur=\"document.all.cpf.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'
				onkeyup=\"pula(14,this.id,reservista.id)\"/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Certificado de Reservista:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
      <input name='reservista' type='text' class='campotexto' id='reservista' 
	  size='18' value='$row[reservista]'
                onFocus=\"document.all.reservista.style.background='#CCFFCC'\" 
                onBlur=\"document.all.reservista.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Nº Carteira de Trabalho:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='trabalho' type='text' class='campotexto' id='trabalho' size='15' value='$row[campo1]'
                onFocus=\"document.all.trabalho.style.background='#CCFFCC'\" 
                onBlur=\"document.all.trabalho.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Série:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
     <input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='10' value='$row[serie_ctps]'
        onfocus=\"document.all.serie_ctps.style.background='#CCFFCC'\"
        onblur=\"document.all.serie_ctps.style.background='#FFFFFF'\" style='background:#FFFFFF;'/>
          </span></span>
		  
	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
	<input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' value='$row[uf_ctps]'
                onfocus=\"document.all.uf_ctps.style.background='#CCFFCC'\" 
                onblur=\"document.all.uf_ctps.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\" 
				onkeyup=\"pula(2,this.id,data_ctps.id)\" /></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data carteira de Trabalho:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
      &nbsp;&nbsp;
      
      <input name='data_ctps' type='text' class='campotexto' size='12' maxlength='10' id='data_ctps' 
	value='$row[data_ctps2]'
        onFocus=\"document.all.data_ctps.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_ctps.style.background='#FFFFFF'\" 
		onkeyup=\"mascara_data(this); pula(10,this.id,titulo2.id)\"
        style='background:#FFFFFF;'/>
      
    </td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Nº Título de Eleitor:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='titulo' type='text' class='campotexto' id='titulo2' size='12' value='$row[titulo]'
                onFocus=\"document.all.titulo2.style.background='#CCFFCC'\" 
                onBlur=\"document.all.titulo2.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;' />
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'> Zona:&nbsp;</span></div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='zona' type='text' class='campotexto' id='zona2' size='3' value='$row[zona]'
                onFocus=\"document.all.zona2.style.background='#CCFFCC'\" 
                onBlur=\"document.all.zona2.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Seção:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='secao' type='text' class='campotexto' id='secao' size='3' value='$row[secao]'
                onFocus=\"document.all.secao.style.background='#CCFFCC'\" 
                onBlur=\"document.all.secao.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style28'><span class='style39'>PIS:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='pis' type='text' class='campotexto' id='pis' size='12' value='$row[pis]'
                onFocus=\"document.all.pis.style.background='#CCFFCC'\" 
                onBlur=\"document.all.pis.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data Pis:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;

    <input name='data_pis' type='text' class='campotexto' size='12' maxlength='10' id='data_pis'
	value='$row[dada_pis2]'
        onFocus=\"document.all.data_pis.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_pis.style.background='#FFFFFF'\" 
		onkeyup=\"mascara_data(this); pula(10,this.id,fgts.id)\"
        style='background:#FFFFFF;'/>
	
	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>FGTS:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='fgts' type='text' class='campotexto' id='fgts' size='10' value='$row[fgts]'
                onFocus=\"document.all.fgts.style.background='#CCFFCC'\" 
                onBlur=\"document.all.fgts.style.background='#FFFFFF'\" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
</table>


<br />




<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='6' bgcolor='#003300' class='style1'><div align='center' class='style43'>BENEFÍCIOS</div></td>
  </tr>
  <tr height='30'>
    <td width='19%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
	Assistência Médica:&nbsp;</div>	</td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'>
	
	<table width='100%' class=linha>
<tr> 
<td width='74'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='1' $chek_medi1>Sim</label></span></td><td width='255'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='0' $chek_medi0>Não</label></span>&nbsp;&nbsp; $mensagem_medi</td>
</tr>
</table>	</td>
    <td width='19%' bgcolor='#CCFFCC' class='style1'>
	<div align='right' class='style39'>Tipo de Plano:&nbsp;</div></td>
    <td width='19%' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
<select name='plano_medico' class='campotexto' id='plano_medico'>

<option value=1 $selected_planoF>Familiar</option>
<option value=2 $selected_planoI>Individual</option>
</select>   </td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Seguro, Apólice:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'><span class='style39'>&nbsp;&nbsp;
          <select name='apolice' class='campotexto' id='apolice'>
<option value='0'>Não Possui</option>";

$result_ap = mysql_query("SELECT * FROM apolice where id_regiao = $row[id_regiao]");
while ($row_ap = mysql_fetch_array($result_ap)){
  if($row_ap['id_apolice'] == $row[apolice]){
  print "<option value='$row_ap[id_apolice]' selected>$row_ap[razao]</option>";   
  }else{
  print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
  }
}


print "
</select>
        </select>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Dependente:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='dependente' type='text' class='campotexto' id='dependente' size='20' value=''
onFocus=\"document.all.dependente.style.background='#CCFFCC'\" 
onBlur=\"document.all.dependente.style.background='#FFFFFF'\" 
style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Insalubridade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'>&nbsp;&nbsp;
    <input name='insalubridade' type='checkbox' id='insalubridade2' value='1' $chek1/></td>
    
	<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Vale Transporte:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
    &nbsp;<input name='transporte' type='checkbox' id='transporte2' value='1' $chek2/>    </td>
  </tr>
  
  
  
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo de Vale:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='5'><span class='style39'>
      &nbsp;&nbsp;
      <select name='tipo_vale' class='campotexto'>
            <option value='1' $selected_valeC>Cartão</option>
            <option value='2' $selected_valeP>Papel</option>
			<option value='3' $selected_valeA>Ambos</option>
          </select>
    </span></td>
  </tr>
  
  
  
  
  
  
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Cartão 1:&nbsp;</div></td>
    <td width='15%' bgcolor='#FFFFFF' class='style1'><span class='style39'>
      &nbsp;
      <input name='num_cartao' type='text' class='campotexto' id='num_cartao' size='12'
		value='$row_vale[numero_cartao]'
onfocus=\"document.all.num_cartao.style.background='#CCFFCC'\" 
onblur=\"document.all.num_cartao.style.background='#FFFFFF'\" 
style='background:#FFFFFF;'/>
    </span></td>
    <td width='15%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Valor Total 1:&nbsp;</div></td>
    <td width='13%' bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='valor_cartao' type='text' class='campotexto' id='valor_cartao' size='12' 
	  value='$row_vale[valor_cartao]'
              onkeydown=\"FormataValor(this,event,20,2)\" 
              onfocus=\"document.all.valor_cartao.style.background='#CCFFCC'\" 
              onblur=\"document.all.valor_cartao.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Cartão 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo_cartao_1' type='text' class='campotexto' id='tipo_cartao_1' size='12' 
	  value='$row_vale[tipo_cartao_1]' onChange=\"this.value=this.value.toUpperCase()\"
              onfocus=\"document.all.tipo_cartao_1.style.background='#CCFFCC'\" 
              onblur=\"document.all.tipo_cartao_1.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Cartão 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
      &nbsp;
      <input name='num_cartao2' type='text' class='campotexto' id='num_cartao2' size='12'
		value='$row_vale[numero_cartao2]'
onfocus=\"document.all.num_cartao2.style.background='#CCFFCC'\" 
onblur=\"document.all.num_cartao2.style.background='#FFFFFF'\" 
style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Valor Total 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='valor_cartao2' type='text' class='campotexto' id='valor_cartao2' size='12' 
	  value='$row_vale[valor_cartao2]'
              onkeydown=\"FormataValor(this,event,20,2)\" 
              onfocus=\"document.all.valor_cartao2.style.background='#CCFFCC'\" 
              onblur=\"document.all.valor_cartao2.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Cartão 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo_cartao_2' type='text' class='campotexto' id='tipo_cartao_2' size='12' 
	  value='$row_vale[tipo_cartao_2]'
              onChange=\"this.value=this.value.toUpperCase()\" 
              onfocus=\"document.all.tipo_cartao_2.style.background='#CCFFCC'\" 
              onblur=\"document.all.tipo_cartao_2.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
  </tr>
  
   
  
  <tr height='30'>
    <td  bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
	Papel: &nbsp;&nbsp;Quantidade 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_qnt_1' type='text' class='campotexto' id='vale_qnt_1' size='3' value='$row_vale[qnt1]'
onFocus=\"document.all.vale_qnt_1.style.background='#CCFFCC'\" 
onBlur=\"document.all.vale_qnt_1.style.background='#FFFFFF'\" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>&nbsp;Valor 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_1' type='text' class='campotexto' id='vale_valor_1' size='12' 
	  value='$row_vale[valor1]'
              onkeydown=\"FormataValor(this,event,20,2)\" 
              onfocus=\"document.all.vale_valor_1.style.background='#CCFFCC'\" 
              onblur=\"document.all.vale_valor_1.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 1:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo1' type='text' class='campotexto' id='tipo1' size='12' 
	  value='$row_vale[tipo1]'
              onChange=\"this.value=this.value.toUpperCase()\"
              onfocus=\"document.all.tipo1.style.background='#CCFFCC'\" 
              onblur=\"document.all.tipo1.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Quantidade 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
      <input name='vale_qnt_2' type='text' class='campotexto' id='vale_qnt_2' size='3' value='$row_vale[qnt2]'
onFocus=\"document.all.vale_qnt_2.style.background='#CCFFCC'\" 
onBlur=\"document.all.vale_qnt_2.style.background='#FFFFFF'\" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Valor 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_2' type='text' class='campotexto' id='vale_valor_2' size='12' 
	value='$row_vale[valor2]'
              onkeydown=\"FormataValor(this,event,20,2)\" 
              onfocus=\"document.all.vale_valor_2.style.background='#CCFFCC'\" 
              onblur=\"document.all.vale_valor_2.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 2:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo2' type='text' class='campotexto' id='tipo2' size='12' 
	  value='$row_vale[tipo2]'
              onChange=\"this.value=this.value.toUpperCase()\"
              onfocus=\"document.all.tipo2.style.background='#CCFFCC'\" 
              onblur=\"document.all.tipo2.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Quantidade 3:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
      <input name='vale_qnt_3' type='text' class='campotexto' id='vale_qnt_3' size='3' value='$row_vale[qnt3]'
onFocus=\"document.all.vale_qnt_3.style.background='#CCFFCC'\" 
onBlur=\"document.all.vale_qnt_3.style.background='#FFFFFF'\" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>&nbsp;Valor 3:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_3' type='text' class='campotexto' id='vale_valor_3' size='12' 
	value='$row_vale[valor3]'
              onkeydown=\"FormataValor(this,event,20,2)\" 
              onfocus=\"document.all.vale_valor_3.style.background='#CCFFCC'\" 
              onblur=\"document.all.vale_valor_3.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 3:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo3' type='text' class='campotexto' id='tipo3' size='12' 
	  value='$row_vale[tipo3]'
               onChange=\"this.value=this.value.toUpperCase()\"
              onfocus=\"document.all.tipo3.style.background='#CCFFCC'\" 
              onblur=\"document.all.tipo3.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Quantidade 4:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_qnt_4' type='text' class='campotexto' id='vale_qnt_4' size='3' 
	value='$row_vale[qnt4]'
onFocus=\"document.all.vale_qnt_4.style.background='#CCFFCC'\" 
onBlur=\"document.all.vale_qnt_4.style.background='#FFFFFF'\" 
style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>&nbsp;Valor 4:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='vale_valor_4' type='text' class='campotexto' id='vale_valor_4' size='12' 
	value='$row_vale[valor4]'
              onkeydown=\"FormataValor(this,event,20,2)\" 
              onfocus=\"document.all.vale_valor_4.style.background='#CCFFCC'\" 
              onblur=\"document.all.vale_valor_4.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo Vale 4:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    <input name='tipo4' type='text' class='campotexto' id='tipo4' size='12' 
	  value='$row_vale[tipo4]'
               onChange=\"this.value=this.value.toUpperCase()\"
              onfocus=\"document.all.tipo4.style.background='#CCFFCC'\" 
              onblur=\"document.all.tipo4.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Adicional Noturno:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1' colspan='3'>
	
<table class='linha'>
<tr> 
<td width='98'>&nbsp;&nbsp; 
<label><input type='radio' name='ad_noturno' value='1' $checkad_noturno1>Sim</label></td>
<td width='86'>&nbsp;&nbsp; 
<label><input type='radio' name='ad_noturno' value='0' $checkad_noturno0>Não</label></td>
</tr>
</table>	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Integrante do CIPA:&nbsp;</div></td>
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


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS BANCÁRIOS</div></td>
  </tr>
  
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
	Tipo de Pagamento:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
      <select name='tipopg' class='campotexto' id='tipopg'>";

$RE_pg_dep = mysql_query("SELECT id_tipopg FROM tipopg where id_projeto = '$id_projeto' and campo1 = '1'");
$Row_pg_dep = mysql_fetch_array($RE_pg_dep);

$RE_pg_che = mysql_query("SELECT id_tipopg FROM tipopg where id_projeto = '$id_projeto' and campo1 = '2'");
$Row_pg_che = mysql_fetch_array($RE_pg_che);

$result_pg = mysql_query("SELECT * FROM tipopg where id_projeto = '$id_projeto'", $conn);
while ($row_pg = mysql_fetch_array($result_pg)){
  if($row_pg['0'] == $row['tipo_pagamento']){
   print "<option value='$row_pg[id_tipopg]' selected>$row_pg[tipopg]</option>";   
  }else{
  print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
  }
}

print "</select>

&nbsp;</td>
  </tr>
  
  <tr height='30'>
    <td width='17%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Banco:&nbsp;</div></td>
    <td width='31%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
      <select name='banco' class='campotexto' onChange='drogadebanco()'>
<option value='0'>Nenhum Banco</option>";
$sql_banco = "SELECT * FROM bancos where id_regiao = '$row[id_regiao]' and id_projeto = '$row[id_projeto]' AND status_reg = '1'";
$result_banco = mysql_query($sql_banco, $conn);
while ($row_banco = mysql_fetch_array($result_banco)){
if($row['banco'] == "$row_banco[0]"){
print "<option value=$row_banco[0] selected>$row_banco[nome]</option>";
}else{
print "<option value=$row_banco[0]>$row_banco[nome]</option>";
}
}
if($row['banco'] == "9999"){
print "<option value='9999' selected>Outro</option></select>";
}else{
print "<option value='9999'>Outro</option></select>";
}

print "</select>
    </span></td>
    <td width='17%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39' onClick=\"bloqEnter()\">Agência:&nbsp;</div></td>
    <td width='35%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='agencia' type='text' class='campotexto' id='agencia' size='12' value='$row[agencia]'
              onFocus=\"document.all.agencia.style.background='#CCFFCC'\" 
              onBlur=\"document.all.agencia.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/>
    </span></td>
  </tr>

 <tr height='30' id='linhabanc2'>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39' onClick=\"ValidaBanc()\">Conta:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class='style39'>
      <input name='conta' type='text' class='campotexto' id='conta' size='12' value='$row[conta]'
              onFocus=\"document.all.conta.style.background='#CCFFCC'\" 
              onBlur=\"document.all.conta.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/>
    </span></td>
	<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tipo de Conta:&nbsp;</div></td>
	<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style39'>";  
	
	$tipo = $row['tipo_conta'];
	if ($tipo == 'salario'){
		$checkedSalario = 'checked';	
	}else if ($tipo == 'corrente'){
		$checkedCorrente = 'checked';
	}
	
	print "<label><input type='radio' name='radio_tipo_conta' value='salario' $checkedSalario>Conta Salário </label>&nbsp;&nbsp;
<label><input type='radio' name='radio_tipo_conta' value='corrente' $checkedCorrente>Conta Corrente </label>&nbsp;&nbsp;
	</div></td>
	
  </tr>
  
  
  <tr height='30' id='linhabanc3'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39' >Outro Banco:&nbsp;</div></td>
	<td bgcolor='#FFFFFF' class='style1' colspan='3'>&nbsp;
	<input name='nome_banco' type='text' class='campotexto' id='nome_banco' size='30' value='$row[nome_banco]'
              onFocus=\"this.style.background='#CCFFCC'\" 
              onBlur=\"this.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;'/></td>
  </tr>


</table>
<span class='style1'><br />
</span>
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS FINANCEIROS E DE CONTRATO</div></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Data de Entrada:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
        
		<input name='data_entrada' type='text' class='campotexto' size='12' maxlength='10' id='data_entrada'
		value='$row[data_entrada2]'
        onFocus=\"document.all.data_entrada.style.background='#CCFFCC'\" 
        onBlur=\"document.all.data_entrada.style.background='#FFFFFF'\" 
		onkeyup=\"mascara_data(this); pula(10,this.id,exame_data.id)\"
        style='background:#FFFFFF;'/>
		
    </td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
	Data do Exame Admissional:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;
    
	<input name='exame_data' type='text' class='campotexto' size='12' maxlength='10' id='exame_data'
	value='$row[data_exame]'
        onFocus=\"document.all.exame_data.style.background='#CCFFCC'\" 
        onBlur=\"document.all.exame_data.style.background='#FFFFFF'\" 
		onkeyup=\"mascara_data(this); pula(10,this.id,localpagamento.id)\"
        style='background:#FFFFFF;'/>
	
	</td>
  </tr>
  <tr height='30'>
    <td width='23%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Local de Pagamento:&nbsp;</div></td>
    <td width='77%' colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
      <input name='localpagamento' type='text' class='campotexto' id='localpagamento' size='25'  
	  value='$row[localpagamento]'
              onFocus=\"document.all.localpagamento.style.background='#CCFFCC'\" 
              onBlur=\"document.all.localpagamento.style.background='#FFFFFF'\" 
              style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\"/>
    </span></td>
  </tr>
  
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Observações:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>
	&nbsp;&nbsp;
	<textarea name='observacoes' id='observacoes' class='campotexto' cols='55' rows='4'  
              onFocus=\"document.all.observacoes.style.background='#CCFFCC'\" 
onBlur=\"document.all.observacoes.style.background='#FFFFFF'\" style='background:#FFFFFF;' onChange=\"this.value=this.value.toUpperCase()\">$row[observacao]</textarea></td>
  </tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td width='254%' colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style39'>FINALIZAÇÃO DO CADASTRAMENTO</div></td>
  </tr>
  
  
  
  
  <tr height='30'>
    <td colspan='4' bgcolor='#FFFFCC' class='style1'>
	<div align='center' class='style39'>
<p><br> 
O Contrato foi ASSINADO?
&nbsp;&nbsp;
<table class=linha><tr>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura' name='assinatura' value='1' $selected_ass_sim> Sim </label></td>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura' name='assinatura' value='0' $selected_ass_nao> Não</label></td>
</tr></table>
<br>

O Distrato foi ASSINADO?
&nbsp;&nbsp;
<table class=linha><tr>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura2' name='assinatura2' value='1' $selected_ass_sim2> Sim </label></td>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura2' name='assinatura2' value='0' $selected_ass_nao2> Não</label></td>
</tr></table>
<br>

Outros documentos foram ASSINADO?
&nbsp;&nbsp;
<table class=linha><tr>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura3' name='assinatura3' value='1' $selected_ass_sim3> Sim </label></td>
<td>&nbsp;&nbsp;<label><input type='radio' id='assinatura3' name='assinatura3' value='0' $selected_ass_nao3> Não</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$mensagem_ass</td>
</tr></table>
<br>



<br>
O Funcionário participa ATIVAMENTE das Atividades do Projeto?
<input type='radio' id='radio1' name='radio6' value='1' $status_ativado/>Sim  - 
<input type='radio' id='radio2' name='radio6' value='0' $status_desativado/>Não
<br>
<font color=red>Caso NÃO coloque a data da desativação:</font>
<input name='data_desativacao' type='text' class='campotexto' id='data_desativacao' size='12' maxlength='10' value='$data_desativacao' 
onFocus=\"document.all.data_desativacao.style.background='#CCFFCC'\" 
onBlur=\"document.all.data_desativacao.style.background='#FFFFFF'\" 
onkeyup=\"mascara_data(this);\"
style='background:#FFFFFF;'/>


<br><br>
      </p>
      <p>
	  <span class='style47'>NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</span>
	  <br />
	  </p>
      <table width='200' border='0' align='center' cellpadding='0' cellspacing='0'>
        <tr height='30'>
          <td align='center' class='style7'>
		  -
		  </td>
          <td align='center' valign='middle' class='style7'>
		  
		  <input type='submit' name='Submit' value='ENVIAR' class='campotexto' />
		  
          <br /></td>
        </tr>
      </table>
      <br />
      <div align='center'><span class='style7'>
        <input type='hidden' name='id_cadastro' value='4' />
        <input type='hidden' name='id_projeto' value='$projeto' />
        <input type='hidden' name='user' value='$id_user' />
      </span><br />
      </div>
      </div></td>
  </tr>
</table>

  <span class='style7'>

<input type='hidden' name='update' value='1'>

<input type='hidden' name='id_bolsista' value='$row[0]'>
<input type='hidden' name='regiao' value='$row[id_regiao]'>
<input type='hidden' name='pro' value='$id_projeto'></td>

  </span></td>
</tr>
</table>
</form><br><a href='javascript:history.go(-1)' class='link'><img src='imagens/voltar.gif' border=0></a>";

print "
<script>

function validaForm(){
	d = document.form1;

	if (d.cpf.value == \"\" ){
		alert(\"O campo CPF deve ser preenchido!\");
		d.cpf.focus();
		return false;
	}
	
return true;

}

function bloqEnter() {
	d = document.form1;
	var iKeyCode; 
	iKeyCode = d.agencia.value;
	buffer = iKeyCode.charCodeAt;
	
	
	
	var variavel = d.agencia.value;
	alert(variavel.charCodeAt(0));
}

function ValidaBanc(){
	d = document.form1;
	deposito = \"$Row_pg_dep[0]\";
	cheque = \"$Row_pg_che[0]\";
	
	if(document.getElementById(\"tipopg\").value == deposito){
		
	if (document.getElementById(\"banco\").value == 0){
		alert(\"Selecione um banco!\");
		return false;
	}
	
	if (d.agencia.value == \"\" ){
		alert(\"O campo Agencia deve ser preenchido!\");
		d.agencia.focus();
		return false;
	}
	
	if (d.conta.value == \"\" ){
		alert(\"O campo Conta deve ser preenchido!\");
		d.conta.focus();
		return false;
	}
	

}

if(document.getElementById(\"tipopg\").value == cheque){
	
	if (document.getElementById(\"banco\").value != 0){
		alert(\"Para pagamentos em cheque deve selecionar SEM BANCO!\");
		return false;
	}
	d.agencia.value = \"\";
	d.conta.value = \"\";

}

}

</script></body></html> ";



} else {
	
// Log - Edição de Bolsista
$qr_colunas = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$_POST[id_bolsista]'");
$coluna = mysql_fetch_assoc($qr_colunas);

$qr_dependentes = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$_POST[id_bolsista]'");
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

$colunas = array($coluna['campo3'], $coluna['tipo_contratacao'], $coluna['id_curso'], $coluna['locacao'], $coluna['nome'], $coluna['endereco'], $coluna['bairro'], $coluna['cidade'], $coluna['uf'], $coluna['cep'], $coluna['tel_fixo'], $coluna['tel_cel'], $coluna['tel_rec'], $coluna['data_nasci'], $coluna['naturalidade'], $coluna['nacionalidade'], $coluna['civil'], $coluna['sexo'], $coluna['pai'], $coluna['nacionalidade_pai'], $coluna['mae'], $coluna['nacionalidade_mae'], $coluna['estuda'], $coluna['data_escola'], $coluna['escolaridade'], $coluna['instituicao'], $coluna['curso'], $coluna['num_filhos'], $dependentes['nome1'], $dependentes['data1'], $dependentes['nome2'], $dependentes['data2'], $dependentes['nome3'], $dependentes['data3'], $dependentes['nome4'], $dependentes['data4'], $dependentes['nome5'], $dependentes['data5'], $coluna['cabelos'], $coluna['olhos'], $coluna['peso'], $coluna['altura'], $coluna['etnia'], $coluna['defeito'], $coluna['deficiencia'], $coluna['foto'], $coluna['rg'], $coluna['orgao'], $coluna['uf_rg'], $coluna['data_rg'], $coluna['cpf'], $coluna['reservista'], $coluna['campo1'], $coluna['serie_ctps'], $coluna['uf_ctps'], $coluna['data_ctps'], $coluna['titulo'], $coluna['zona'], $coluna['secao'], $coluna['pis'], $coluna['dada_pis'], $coluna['fgts'], $coluna['medica'], $coluna['plano'], $coluna['apolice'], $coluna['campo2'], $coluna['insalubridade'], $coluna['ad_noturno'], $coluna['transporte'], $coluna['cipa'], $coluna['banco'], $coluna['agencia'], $coluna['conta'], $coluna['tipo_conta'], $coluna['nome_banco'], $coluna['data_entrada'], $coluna['data_exame'], $coluna['localpagamento'], $coluna['tipo_pagamento'], $coluna['observacao'], $coluna['assinatura'], $coluna['distrato'], $coluna['outros']);

$posts = array($_POST['codigo'], $_POST['tipo_bol'], $_POST['id_curso'], $_POST['lotacao'], $_POST['nome'], $_POST['endereco'], $_POST['bairro'], $_POST['cidade'], $_POST['uf'], $_POST['cep'], $_POST['tel_fixo'], $_POST['tel_cel'], $_POST['tel_rec'], formata($_POST['data_nasc']), $_POST['naturalidade'], $_POST['nacionalidade'], $_POST['civil'], $_POST['sexo'], $_POST['pai'], $_POST['nacionalidade_pai'], $_POST['mae'], $_POST['nacionalidade_mae'], $_POST['estuda'], formata($_POST['data_escola']), $_POST['escolaridade'], $_POST['instituicao'], $_POST['curso'], $_POST['filhos'], $_POST['filho_1'], formata($_POST['data_filho_1']), $_POST['filho_2'], formata($_POST['data_filho_2']), $_POST['filho_3'], formata($_POST['data_filho_3']), $_POST['filho_4'], formata($_POST['data_filho_4']), $_POST['filho_5'], formata($_POST['data_filho_5']), $_POST['cabelos'], $_POST['olhos'], $_POST['peso'], $_POST['altura'], $_POST['etnia'], $_POST['defeito'], $_POST['deficiencia'], formata2($_POST['foto']), $_POST['rg'], $_POST['orgao'], $_POST['uf_rg'], formata($_POST['data_rg']), $_POST['cpf'], $_POST['reservista'], $_POST['trabalho'], $_POST['serie_ctps'], $_POST['uf_ctps'], formata($_POST['data_ctps']), $_POST['titulo'], $_POST['zona'], $_POST['secao'], $_POST['pis'], formata($_POST['data_pis']), $_POST['fgts'], $_POST['medica'], $_POST['plano_medico'], $_POST['apolice'], $_POST['dependente'], formata2($_POST['insalubridade']), $_POST['ad_noturno'], formata2($_POST['transporte']), $_POST['cipa'], $_POST['banco'], $_POST['agencia'], $_POST['conta'], $_POST['radio_tipo_conta'], $_POST['nome_banco'], formata($_POST['data_entrada']), formata($_POST['exame_data']), $_POST['localpagamento'], $_POST['tipopg'], $_POST['observacoes'], $_POST['assinatura'], $_POST['assinatura2'], $_POST['assinatura3']);

$campos = array("o código", "o tipo de contratação", "o curso", "a unidade", "o nome", "o endereço", "o bairro", "a cidade", "o estado", "o CEP", "o telefone fixo", "o telefone celular", "o telefone de recado", "a data de nascimento", "a naturalidade", "a nacionalidade", "o estado civil", "o sexo", "o nome do pai", "a nacionalidade do pai", "o nome da mãe", "a nacionalidade da mãe", "o estudo", "o término do estudo", "a escolaridade", "a instituição escolar", "o curso", "o número de filhos", "o nome do 1º filho", "a data de nascimento do 1º filho", "o nome do 2º filho", "a data de nascimento do 2º filho", "o nome do 3º filho", "a data de nascimento do 3º filho", "o nome do 4º filho", "a data de nascimento do 4º filho", "o nome do 5º filho", "a data de nascimento do 5º filho", "a cor do cabelo", "a cor dos olhos", "o peso", "a altura", "a etnia", "a marca", "a deficiência", "a foto", "o RG", "o órgão do RG", "o estado do RG", "a data do RG", "o CPF", "o certificado de reservista", "a carteira de trabalho", "a série do CTPS", "o estado do CTPS", "a data do CTPS", "o Título de Eleitor", "a zona do Título", "a secão do Título", "o PIS", "a data do PIS", "o FGTS", "a assistência médica", "o tipo de plano", "a apólice", "o dependente", "a insalubridade", "o adicional noturno", "o vale transporte", "o integrante do CIPA", "o banco", "a agência", "a conta", "o tipo de conta", "o nome do banco", "a data de entrada", "a data de exame", "o local de pagamento", "o tipo de pagamento", "as observações", "a assinatura", "o distrato", "os outros documentos");

$n = 0;
$edicao = "";

for ($a=0; $a<=82; $a++) {
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
$local = "Edição de Bolsista - ($coluna[campo3]) $coluna[nome]";
$local_banco = "Edição de Bolsista";
$acao_banco = "Editou o Bolsista ($coluna[campo3]) $coluna[nome]";

mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die ("Erro Inesperado<br><br>".mysql_error());

$nome_arquivo = "log/".$funcionario[0].".txt";

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



$id_bolsista = $_REQUEST['id_bolsista'];
$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['pro'];

$id_user = $_COOKIE['logado'];
$data_hoje = date('Y-m-d');

$lotacao = $_REQUEST['lotacao'];

$tipo_contratacao = $_REQUEST['tipo_bol'];

if($tipo_contratacao == 3 or $tipo_contratacao == 4){
	$cooperativa = $_REQUEST['cooperativa'];
}else{
	$cooperativa = "0";
}

$nome = $_REQUEST['nome'];
$assinatura = $_REQUEST['assinatura'];
$assinatura2 = $_REQUEST['assinatura2'];
$assinatura3 = $_REQUEST['assinatura3'];
$sexo = $_REQUEST['sexo'];
$endereco = $_REQUEST['endereco'];
$bairro = $_REQUEST['bairro'];
$cidade = $_REQUEST['cidade'];
$uf = $_REQUEST['uf'];
$cep = $_REQUEST['cep'];
$tel_fixo = $_REQUEST['tel_fixo'];
$tel_cel = $_REQUEST['tel_cel'];
$tel_rec = $_REQUEST['tel_rec'];
$data_nasci = $_REQUEST['data_nasc'];
$naturalidade = $_REQUEST['naturalidade'];
$nacionalidade = $_REQUEST['nacionalidade'];
$civil = $_REQUEST['civil'];
$rg = $_REQUEST['rg'];
$uf_rg = $_REQUEST['uf_rg'];
$secao = $_REQUEST['secao'];
$data_rg = $_REQUEST['data_rg'];
$cpf = $_REQUEST['cpf'];
$titulo = $_REQUEST['titulo'];
$zona = $_REQUEST['zona'];
$orgao = $_REQUEST['orgao'];
$pai = $_REQUEST['pai'];
$mae = $_REQUEST['mae'];
$nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
$nacionalidade_mae = $_REQUEST['nacionalidade_mae'];
$estuda = $_REQUEST['estuda'];
$escola_dia = $_REQUEST['escola_dia'];
$escolaridade = $_REQUEST['escolaridade'];
$instituicao = $_REQUEST['instituicao'];
$curso = $_REQUEST['curso'];
$banco = $_REQUEST['banco'];
$agencia = $_REQUEST['agencia'];
$conta = $_REQUEST['conta'];
$tipoDeConta = $_REQUEST['radio_tipo_conta'];
$localpagamento = $_REQUEST['localpagamento'];
$apolice = $_REQUEST['apolice'];
$tabela = $_REQUEST['tabela'];
$status = $_REQUEST['radio6'];
$data_entrada = $_REQUEST['data_entrada'];

$codigo = $_REQUEST['codigo'];

$id_curso = $_REQUEST['id_curso'];
$trabalho = $_REQUEST['trabalho'];
$dependente = $_REQUEST['dependente'];

$serie_ctps = $_REQUEST['serie_ctps'];
$uf_ctps = $_REQUEST['uf_ctps'];

$nome_banco = $_REQUEST['nome_banco'];
$pis = $_REQUEST['pis'];
$fgts = $_REQUEST['fgts'];
$tipopg = $_REQUEST['tipopg'];
$filhos = $_REQUEST['filhos'];
$observacao = $_REQUEST['observacoes'];
$medica = $_REQUEST['medica'];
$plano = $_REQUEST['plano_medico'];
$data_ctps = $_REQUEST['data_ctps'];
$data_pis = $_REQUEST['data_pis'];

$tipo_vale = $_REQUEST['tipo_vale'];

$num_cartao = $_REQUEST['num_cartao'];
$valor_cartao = $_REQUEST['valor_cartao'];
$tipo_cartao_1 = $_REQUEST['tipo_cartao_1'];

$numero_cartao2 = $_REQUEST['num_cartao2'];
$valor_cartao2 = $_REQUEST['valor_cartao2'];
$tipo_cartao_2 = $_REQUEST['tipo_cartao_2'];

$qnt1 = $_REQUEST['vale_qnt_1'];
$valor1 = $_REQUEST['vale_valor_1'];

$qnt2 = $_REQUEST['vale_qnt_2'];
$valor2 = $_REQUEST['vale_valor_2'];

$qnt3 = $_REQUEST['vale_qnt_3'];
$valor3 = $_REQUEST['vale_valor_3'];

$qnt4 = $_REQUEST['vale_qnt_4'];
$valor4 = $_REQUEST['vale_valor_4'];

$tipo1 = $_REQUEST['tipo1'];
$tipo2 = $_REQUEST['tipo2'];
$tipo3 = $_REQUEST['tipo3'];
$tipo4 = $_REQUEST['tipo4'];

$ad_noturno = $_REQUEST['ad_noturno'];
$exame_data = $_REQUEST['exame_data'];

$reservista = $_REQUEST['reservista'];
$cabelos = $_REQUEST['cabelos'];
$peso = $_REQUEST['peso'];
$altura = $_REQUEST['altura'];
$olhos = $_REQUEST['olhos'];
$defeito = $_REQUEST['defeito'];
$cipa = $_REQUEST['cipa'];
$etnia = $_REQUEST['etnia'];
$deficiencia = $_REQUEST['deficiencia'];

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


if($status == '0'){
$desativacao = $_REQUEST['data_desativacao'];
}else{
$desativacao = "";
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
 return "";
 }
}

$data_entrada2 = ConverteData($data_entrada);
$data_rg2 = ConverteData($data_rg);
$data_nasci2 = ConverteData($data_nasci);
$data_ctps = ConverteData($data_ctps);
$data_pis1 = ConverteData($data_pis);
$exame_data = ConverteData($exame_data);
$desativacao = ConverteData($desativacao);

$data_filho_1 = ConverteData($data_filho_1);
$data_filho_2 = ConverteData($data_filho_2);
$data_filho_3 = ConverteData($data_filho_3);
$data_filho_4 = ConverteData($data_filho_4);
$data_filho_5 = ConverteData($data_filho_5);


if($codigo == "INSERIR"){
$resultado_teste2 = "0";
}else{
$result_teste2 = mysql_query("SELECT id_autonomo FROM autonomo where campo3 = '$codigo' and id_autonomo <> '$id_bolsista' and id_projeto = '$projeto'" , $conn);
$resultado_teste2 = mysql_num_rows($result_teste2);
}

if ($resultado_teste2 >= 1) {

print "
<br>
<link href='../net.css' rel='stylesheet' type='text/css'>
<body bgcolor='#D7E6D5'>
<center>
<br>JÁ EXISTE UM PARTICIPANTE CADASTRADO COM ESTE CÓDIGO: <font color=#FFFFFF><b>$codigo</b></font>
</center>
</body>
";
} else {
 
mysql_query ("update autonomo set localpagamento = '$localpagamento', locacao = '$lotacao', nome = '$nome', sexo = '$sexo', endereco = '$endereco', bairro = '$bairro', cidade = '$cidade', uf = '$uf', cep = '$cep', tel_fixo = '$tel_fixo',
tel_cel = '$tel_cel', tel_rec = '$tel_rec', data_nasci = '$data_nasci2', naturalidade = '$naturalidade', nacionalidade = '$nacionalidade', civil = '$civil', rg = '$rg', orgao = '$orgao', data_rg = '$data_rg2', cpf = '$cpf', titulo = '$titulo', zona = '$zona', secao = '$secao', pai = '$pai', nacionalidade_pai = '$nacionalidade_pai', mae = '$mae', nacionalidade_mae = '$nacionalidade_mae', estuda = '$estuda', data_escola = '$data_escola', escolaridade = '$escolaridade', instituicao = '$instituicao', curso = '$curso', banco = '$banco', agencia ='$agencia', conta = '$conta',tipo_conta = '$tipoDeConta',  status = '$status', data_saida = '$desativacao', campo3 = '$codigo', tipo_contratacao = '$tipo_contratacao', id_curso = '$id_curso', apolice = '$apolice', data_entrada = '$data_entrada2', campo2 = '$dependente', campo1 = '$trabalho',
 data_exame = '$exame_data', reservista = '$reservista', etnia = '$etnia', deficiencia = '$deficiencia', cabelos = '$cabelos', peso = '$peso', altura = '$altura'
, olhos = '$olhos', defeito = '$defeito', cipa = '$cipa', ad_noturno = '$ad_noturno', plano = '$plano', assinatura = '$assinatura', distrato = '$assinatura2', outros = '$assinatura3', pis = '$pis', dada_pis = '$data_pis1', data_ctps = '$data_ctps', serie_ctps = '$serie_ctps', uf_ctps = '$uf_ctps', uf_rg = '$uf_rg', fgts = '$fgts', insalubridade = '$insalubridade', transporte = '$transporte', medica = '$medica', tipo_pagamento = '$tipopg', nome_banco = '$nome_banco', num_filhos = '$filhos', observacao = '$observacao', foto = '$foto_banco', id_cooperativa = '$cooperativa', dataalter = '$data_hoje', useralter = '$id_user' where id_autonomo = '$id_bolsista' LIMIT 1") or die ("Erro no UPDATE:<br><br><font color=red> ".mysql_error());

//VERIFICA SE O BOLSISTA JA ESTÁ CADASTRADO NA TABELA DE VALES
$result_cont_vale = mysql_query ("SELECT id_bolsista FROM vale WHERE id_bolsista = '$id_bolsista' and id_projeto = '$id_projeto'");
$row_cont_vale = mysql_num_rows($result_cont_vale);

if($row_cont_vale == "0"){
mysql_query ("INSERT INTO vale(id_regiao,id_projeto,id_bolsista,nome,cpf,tipo_vale,numero_cartao,valor_cartao,quantidade,qnt1,valor1,qnt2,valor2,qnt3,valor3,qnt4,valor4,tipo1,tipo2,tipo3,tipo4,tipo_cartao_1,tipo_cartao_2,numero_cartao2,valor_cartao2) values 
('$regiao','$id_projeto','$id_bolsista','$nome','$cpf','$tipo_vale','$num_cartao','$valor_cartao','$quantidade','$qnt1','$valor1','$qnt2','$valor2','$qnt3','$valor3','$qnt4','$valor4','$tipo1','$tipo2','$tipo3','$tipo4','$tipo_cartao_1','$tipo_cartao_2','$numero_cartao2','$valor_cartao2')") or die ("houve algum erro de digitação no incert dos vales query: ". mysql_error());
}else{
mysql_query ("update vale set tipo_vale = '$tipo_vale', numero_cartao =  '$num_cartao', valor_cartao = '$valor_cartao', qnt1 = '$qnt1', valor1 = '$valor1', qnt2 = '$qnt2', valor2 ='$valor2', qnt3 = '$qnt3', valor3 = '$valor3', qnt4 = '$qnt4', valor4 = '$valor4', tipo1 = '$tipo1', tipo2 = '$tipo2', tipo3 = '$tipo3', tipo4 = '$tipo4', tipo_cartao_1 = '$tipo_cartao_1', tipo_cartao_2 = '$tipo_cartao_2', numero_cartao2 = '$numero_cartao2', valor_cartao2 = '$valor_cartao2', status_vale = '$transporte' 
where id_projeto = '$id_projeto' and id_bolsista = '$id_bolsista' ") or die ("houve algum erro de digitação na terceira query: ". mysql_error());
}
//FINALIZANDO O PROCESSAMENTO DOS DADOS A RESPEITO DO VALE


//VERIFICA SE O BOLSISTA JA ESTÁ CADASTRADO NA TABELA DEPENDENTES
$result_cont1 = mysql_query ("SELECT id_bolsista FROM dependentes WHERE id_bolsista = '$id_bolsista' AND id_projeto = '$id_projeto' AND 
contratacao = '$tipo_contratacao'");


if(mysql_num_rows($result_cont1) == "0"){
mysql_query ("INSERT INTO dependentes(id_regiao,id_projeto,id_bolsista,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5) values 
('$regiao','$id_projeto','$id_bolsista','$nome','$data_filho_1','$filho_1','$data_filho_2','$filho_2','$data_filho_3','$filho_3',
'$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or die ("ERRO (insert dependentes): ". mysql_error());
}else{
mysql_query ("update dependentes vale set data1 = '$data_filho_1', nome1 = '$filho_1', data2 = '$data_filho_2', nome2 = '$filho_2', data3 = '$data_filho_3', nome3 = '$filho_3', data4 = '$data_filho_4', nome4 = '$filho_4', data5 = '$data_filho_5', nome5 = '$filho_5' WHERE id_projeto = '$id_projeto' AND id_bolsista = '$id_bolsista' AND contratacao = '$tipo_contratacao'") or die ("ERRO (update de dependentes): ". mysql_error());
}

$arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

if($foto_up == "1"){

if(!$arquivo)
{
    $mensagem = "Não acesse esse arquivo diretamente!";
}
// Imagem foi enviada, então a move para o diretório desejado
else
{
    $tipo_arquivo = ".gif";
	// Resolvendo o nome e para onde o arquivo será movido
    $diretorio = "fotos/";
	$nome_tmp = $regiao."_".$id_projeto."_".$id_bolsista.$tipo_arquivo;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

}

}else{
// SEM IMAGEM
}

header("Location: bolsista.php?projeto=$id_projeto&regiao=$regiao&sucesso=edicao");
exit;

}

}

}
?>