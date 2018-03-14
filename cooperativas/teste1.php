<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
exit;
}

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$id_regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['pro'];

$REPro = mysql_query("SELECT * FROM projeto where id_projeto = '$projeto'");
$RowPro = mysql_fetch_array($REPro);

//$resposta = chmod ("../rh/folha/Apagar2", 0755);  					//MUDANDO ARQUIVO PARA LEITURA E ESCRITA E TAL...
//$resposta3 = unlink ("../rh/folha/Apagar1/FP_24_03_0_TST.txt"); 		//DELETANDO UM ARQUIVO DENTRO DA PASTA....
//$resposta2 = rmdir ("../rh/folha/Apagar1"); 							//DELETANDO A PASTA..
//unlink 

//FP_24_03_0_TST.txt
//print $resposta3." - ".$resposta2;										//IMPRIMINDO A RESPOSTA
?>

<html>
<head><title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>


<link href="net.css" rel="stylesheet" type="text/css">

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
body {
background-color: #5C7E59;
}
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

<?php
if(empty($_REQUEST['update'])){

?>

<script language="javascript" src="../js/ramon.js"></script>
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
</style>

</head>

<?php 


// PEGANDO O MAIOR NUMERO
$resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3 , 
MAX(campo3) 
FROM autonomo 
WHERE id_regiao= '$id_regiao' 
AND id_projeto ='$projeto' 
AND campo3 != 'INSERIR' 
GROUP BY campo3 DESC 
LIMIT 0,1");
$row_maior = mysql_fetch_array ($resut_maior); 

$codigo = $row_maior[0] + 1;
$codigo = sprintf("%04d",$codigo);
?>

<body>
<form action='cadcooperado.php' method='post' name='form1' enctype='multipart/form-data' onSubmit="return validaForm()">
<table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#5C7E59' class='linha' align='center'>
<tr>
<td colspan=2><div align=center class='style7'>
<div align='left'><img src='../imagens/cadastrabolsista.gif' width='440' height='20' /><br />
Cadastramento de Integrante de acordo com o Projeto Selecionado <BR><BR> </div>
</div></td>
  </tr>
</table>
<br />
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
<td colspan='2' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS DO PROJETO</div></td>
</tr>


<tr>
<td width="32%" height='30'bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Projeto:&nbsp;</span></div></td>
<td width="68%" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;<?=$RowPro['0']." - ".$RowPro['nome']?></span></td>
</tr>


<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Cooperativa Vinculada:&nbsp;</span></div></td>
  <span class="style1"><span class="style39">
  
  </span></span>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<select name='vinculo' id='vinculo' class='campotexto'>
<?php
$RECoop = mysql_query("SELECT * FROM cooperativas WHERE id_regiao = '$id_regiao'");

while ($RowCoop = mysql_fetch_array($RECoop)){
   print "<option value='$RowCoop[0]'>$RowCoop[0] - $RowCoop[fantasia]</option>";
}

?>

</select>


</span></td>
</tr>


<tr>
  <td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Atividade:&nbsp;</span></div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<select name='atividade' id='atividade' class='campotexto'>
  <?php 
	$RECurso = mysql_query("SELECT * FROM curso WHERE campo3 = '$projeto'");

	while ($RowCurso = mysql_fetch_array($RECurso)){
		print "<option value='$RowCurso[0]'>$RowCurso[0] - $RowCurso[nome]</option>";
	}

?>
  
</select></span></td>
</tr>

<tr>
  
  
  <td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Unidade:&nbsp;</span></div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
    <select name='locacao' id='locacao' class='campotexto'>
  	<?php

	$result_unidade = mysql_query("SELECT * FROM unidade where id_regiao = '$id_regiao' and campo1 = '$projeto' ORDER BY unidade");
	while ($row_unidade = mysql_fetch_array($result_unidade)){
	print "<option value='$row_unidade[unidade]'>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
	}
	
	?>
      </select>
  </span></td>
</tr>

<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style6'>
Código:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style6'>&nbsp;&nbsp; <?=$codigo?>
</tr>

<tr>
<td height='30' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Tipo Contratação:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>

<label class='style39'><input name='contratacao' type='radio' id='contratacao' value='3' checked> Cooperado</label></td>
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
<td width='13%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Nome:&nbsp;</span></div>
</div></td>
<td width='87%' colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='nome' type='text' class='campotexto' id='nome' size='75'
onFocus="document.all.nome.style.background='#CCFFCC'"
onBlur="document.all.nome.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Endereco:&nbsp;</span></div>
</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='endereco' type='text' class='campotexto' id='endereco' size='75' 
onFocus="document.all.endereco.style.background='#CCFFCC'" 
onBlur="document.all.endereco.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Bairro:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='bairro' type='text' class='campotexto' id='bairro' size='15' 
onFocus="document.all.bairro.style.background='#CCFFCC'" 
onBlur="document.all.bairro.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;&nbsp;</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'> Cidade:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='cidade' type='text' class='campotexto' id='cidade' size='12' 
onFocus="document.all.cidade.style.background='#CCFFCC'" 
onBlur="document.all.cidade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>UF:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' 
onFocus="document.all.uf.style.background='#CCFFCC'" 
onBlur="document.all.uf.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"
onkeyup="pula(2,this.id,cep.id)" />
</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>CEP:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='left' class='style6 style3 style40 style42'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='cep' type='text' class='campotexto' id='cep' size='10' maxlength='9' 
style='background:#FFFFFF; text-transform:uppercase;'
onFocus="document.all.cep.style.background='#CCFFCC'" 
onBlur="document.all.cep.style.background='#FFFFFF'"
OnKeyPress="formatar('#####-###', this)" 
onKeyUp="pula(9,this.id,tel_fixo.id)" />
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='right'><span class='style37'>Telefones:&nbsp;</span></div>
</div></td>
<td colspan='2' bgcolor='#CCFFCC' class='style1'><div align='right' class='style6 style3 style40 style42'>
<div align='center'><span class='style37'>Fixo:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_fixo' type='text' id='tel_fixo' size='14' 
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,tel_cel.id)" 
onFocus="document.all.tel_fixo.style.background='#CCFFCC'" 
onBlur="document.all.tel_fixo.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'> <div align='center' class='style6 style37'>
<div align='right'><span class='style37'>Cel:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='14' onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,tel_rec.id)" 
onFocus="document.all.tel_cel.style.background='#CCFFCC'" 
onBlur="document.all.tel_cel.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
&nbsp;</span></div>
</div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='center' class='style6 style37'>
<div align='right'><span class='style37'>Recado:&nbsp;</span></div>
</div></td>
<td bgcolor='#FFFFFF' class='style1'><div align='center' class='style6 style40'>
<div align='left'><span class='style37'>&nbsp;&nbsp;
<input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='14' onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,data_nasci.id)" 
onFocus="document.all.tel_rec.style.background='#CCFFCC'" 
onBlur="document.all.tel_rec.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</span></div>
</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'><span class='style37'>Data de Nascimento:&nbsp;</span></div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;
<input name='data_nasci' type='text' id='data_nasci' size='10' class='campotexto'
onKeyUp="mascara_data(this); pula(10,this.id,naturalidade.id)"
onFocus="document.all.data_nasci.style.background='#CCFFCC'" 
onBlur="document.all.data_nasci.style.background='#FFFFFF'" 
style='background:#FFFFFF;'>
</span> <span class='style6 style37'>&nbsp;</span></td>
<td bgcolor='#CCFFCC' class='style1'>
<div align='right' class='style39'>Naturalidade:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='10'  
onFocus="document.all.naturalidade.style.background='#CCFFCC'" 
onBlur="document.all.naturalidade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nacionalidade:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='8' 
onFocus="document.all.nacionalidade.style.background='#CCFFCC'" 
onBlur="document.all.nacionalidade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estado Civil:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<select name='civil' class='campotexto' id='civil'>
<option>Solteiro</option>
<option>Casado</option>
<option>Viúvo</option>
<option>Sep. Judicialmente</option>
<option>Divorciado</option>
</select>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right'  class='style39'>Sexo:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
<table align='left'>
<tr height='30'>
<td class='style39'><span class='style37'>
&nbsp;&nbsp;
<label>
<input type='radio' name='sexo' value='M' checked='checked' /> Masculino </label></span></td>
<td class='style39'><span class='style37'>
&nbsp;&nbsp;
<label>		
<input type='radio' name='sexo' value='F' />Feminino</label></span></td>
</tr>
</table></td>
</tr>
<tr>
<td colspan='8' bgcolor='#CCFF99' class='style1'><div align='center' class='style44'>DADOS DA FAMÍLIA E EDUCACIONAIS</div></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome do C&ocirc;njuge:&nbsp;</div></td>
  <td colspan='7' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
    <input name='c_nome' type='text' class='campotexto' id='c_nome' size='75'
onfocus="this.style.background='#CCFFCC'"
onblur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
  </span></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>CPF C&ocirc;njuge:&nbsp;</div></td>
  <td colspan="2" bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style39">
    <input name='c_cpf' type='text' class='campotexto' id='c_cpf' size='17' maxlength='14'
                onkeypress="formatar('###.###.###-##', this)" 
                onfocus="this.style.background='#CCFFCC'" 
                onblur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(14,this.id,c_nascimento.id)"/>
    </span></td>
  <td colspan="2" bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data de Nascimento:&nbsp;</div></td>
  <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style6 style37">
    <input name='c_nascimento' type='text' id='c_nascimento' size='10' class='campotexto'
onkeyup="mascara_data(this); pula(10,this.id,c_profissao.id)"
onfocus="this.style.background='#CCFFCC'" 
onblur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
    </span></td>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Profiss&atilde;o:</div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class="style37">
    &nbsp;&nbsp;
    <input name='c_profissao' type='text' class='campotexto' id='c_profissao' size='20'
onfocus="this.style.background='#CCFFCC'"
onblur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span></td>
</tr>
<tr height='30'>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Filiação - Pai:&nbsp;</div></td>
  <td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
  <input name='pai' type='text' class='campotexto' id='pai' size='75' 
onFocus="document.all.pai.style.background='#CCFFCC'" 
onBlur="document.all.pai.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Pai:</span>&nbsp;&nbsp;
    
  <input name='nacionalidade_pai' type='text' class='campotexto' id='nacionalidade_pai' size='15' 
onFocus="document.all.nacionalidade_pai.style.background='#CCFFCC'" 
onBlur="document.all.nacionalidade_pai.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>	
    
  </span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Filiação - Mãe:&nbsp;</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
<input name='mae' type='text' class='campotexto' id='mae' size='75' 
onFocus="document.all.mae.style.background='#CCFFCC'" 
onBlur="document.all.mae.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Mãe:</span>&nbsp;&nbsp;
	
<input name='nacionalidade_mae' type='text' class='campotexto' id='nacionalidade_mae' size='15' 
onFocus="document.all.nacionalidade_mae.style.background='#CCFFCC'" 
onBlur="document.all.nacionalidade_mae.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>	



</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estuda Atualmente?&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><table align='left'>
<tr height='30'>
<td class='style39'><span class='style39'>&nbsp;&nbsp;
<input type='radio' name='estuda' value='sim' checked='checked' />
SIM</span></td>
<td class='style39'><span class='style39'>&nbsp;&nbsp;
<input type='radio' name='estuda' value='não' />
NÃO</span></td>
</tr>
</table></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Término em:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='data_escola' type='text' id='data_escola' size='10' class='campotexto'
onKeyUp="mascara_data(this); pula(10,this.id,escolaridade.id)" maxlength='10' 
onFocus="document.all.data_escola.style.background='#CCFFCC'" 
onBlur="document.all.data_escola.style.background='#FFFFFF'" 
style="background:#FFFFFF">
</td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Escolaridade:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;&nbsp;
<input name='escolaridade' type='text' class='campotexto' id='escolaridade' size='15' 
onFocus="document.all.escolaridade.style.background='#CCFFCC'" 
onBlur="document.all.escolaridade.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Instituíção:&nbsp;</div></td>
<td colspan='2' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;
<input name='instituicao' type='text' class='campotexto' id='titulo' size='20' 
onFocus="document.all.instituicao.style.background='#CCFFCC'" 
onBlur="document.all.instituicao.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Curso:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6 style37'>&nbsp;&nbsp;
<input name='curso' type='text' class='campotexto' id='zona' size='10' 
onFocus="document.all.curso.style.background='#CCFFCC'" 
onBlur="document.all.curso.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Número de Filhos:&nbsp;</div></td>
<td colspan='7' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;&nbsp;
<input name='filhos' type='text' class='campotexto  style37' id='filhos' size='2' 
onFocus="document.all.filhos.style.background='#CCFFCC'" 
onBlur="document.all.filhos.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
<div align='right'></div>    <div align='right'></div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;&nbsp;
<input name='filho_1' type='text' class='campotexto' id='filho_1' size='50' 
onFocus="document.all.filho_1.style.background='#CCFFCC'" 
onBlur="document.all.filho_1.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_1' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_1'
onFocus="document.all.data_filho_1.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_1.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_2.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_2' type='text' class='campotexto' id='filho_2' size='50' 
onFocus="document.all.filho_2.style.background='#CCFFCC'" 
onBlur="document.all.filho_2.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_2' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_2'
onFocus="document.all.data_filho_2.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_2.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_3.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_3' type='text' class='campotexto' id='filho_3' size='50' 
onFocus="document.all.filho_3.style.background='#CCFFCC'" 
onBlur="document.all.filho_3.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
&nbsp;</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_3' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_3'
onFocus="document.all.data_filho_3.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_3.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_4.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
<input name='filho_4' type='text' class='campotexto' id='filho_4' size='50' 
onFocus="document.all.filho_4.style.background='#CCFFCC'" 
onBlur="document.all.filho_4.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_4' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_4'
onFocus="document.all.data_filho_4.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_4.style.background='#FFFFFF'" 
onKeyUp="mascara_data(this); pula(10,this.id,filho_5.id)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
<td colspan='5' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;&nbsp;
<input name='filho_5' type='text' class='campotexto' id='filho_5' size='50' 
onFocus="document.all.filho_5.style.background='#CCFFCC'" 
onBlur="document.all.filho_5.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span>      <div align='right' class='style39'></div></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>nascimento:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style39'>
&nbsp;&nbsp;
<input name='data_filho_5' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_5'
onFocus="document.all.data_filho_5.style.background='#CCFFCC'" 
onBlur="document.all.data_filho_5.style.background='#FFFFFF'" 
onkeyup="mascara_data(this)"
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr>
<td colspan='8' bgcolor='#CCFF99' class='style1'><div align='center' class='style44'>APARÊNCIA</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'>
<div align='right' class='style39'>Cabelos:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;<select name='cabelos' id='cabelos'>
<option>Loiro</option>
<option>Castanho Claro</option>
<option>Castanho Escuro</option>
<option>Ruivo</option>
<option>Pretos</option>
</select>
</td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Olhos:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>&nbsp;&nbsp;
<select name='olhos' id='olhos'>
<option>Castanho Claro</option>
<option>Castanho Escuro</option>
<option>Verde</option>
<option>Azul</option>
<option>Mel</option>
<option>Preto</option>
</select>
</span></span></td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Peso:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
&nbsp;&nbsp;
<input name='peso' type='text' class='campotexto' id='peso' size='5' 
onFocus="document.all.peso.style.background='#CCFFCC'" 
onBlur="document.all.peso.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
</span></span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Altura:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style6'><span class='style37'>
&nbsp;&nbsp;
<input name='altura' type='text' class='campotexto' id='altura' size='5' 
onFocus="document.all.altura.style.background='#CCFFCC'" 
onBlur="document.all.altura.style.background='#FFFFFF'" 
style='background:#FFFFFF;' />
&nbsp;&nbsp; </span></span></td>
<td colspan='3' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Marcas ou Cicatriz aparente:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='defeito' type='text' class='campotexto' id='defeito' size='18' 
onFocus="document.all.defeito.style.background='#CCFFCC'" 
onBlur="document.all.defeito.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</td>
</tr>
<tr>
<td colspan='8' bgcolor='#FFFFFF' class='style1'>&nbsp;</td>
</tr>
<tr height='30'>
<td colspan='8' bgcolor='#FFFFFF' class='style1'>
<div align='center' class='style39'>
Enviar Foto:
<input name='foto' type='checkbox' id='foto' onClick="document.all.arquivo.style.display = (document.all.arquivo.style.display == 'none') ? '' : 'none' ;" value='1'/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name='arquivo' type='file' id='arquivo' size='60' style='display:none'/> 
<br>
<span style="font-size:9px">(somente arquivo .gif) </span></div></td>
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
	<input name='rg' type='text' id='rg' size='13' maxlength='14' class='campotexto'
                OnKeyPress="formatar('##.###.###-#', this)" 
                onFocus="document.all.rg.style.background='#CCFFCC'" 
                onBlur="document.all.rg.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(12,this.id,orgao.id)">
    </td>
    <td width='15%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Orgão Expedidor:&nbsp;</div></td>
    <td width='9%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='orgao' type='text' class='campotexto' id='orgao' size='8'
onFocus="document.all.orgao.style.background='#CCFFCC'" 
onBlur="document.all.orgao.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span> </td>
    <td width='5%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td width='7%' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
    <input name='uf_rg' type='text' class='campotexto' id='uf_rg' size='2' maxlength='2' 
                onfocus="document.all.uf_rg.style.background='#CCFFCC'" 
                onblur="document.all.uf_rg.style.background='#FFFFFF'"
				onKeyUp="pula(2,this.id,data_rg.id)"
                style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/></td>
    <td width='18%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data Expedição:&nbsp;</div></td>
    <td width='18%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='data_rg' type='text' class='campotexto' size='12' maxlength='10'
		id='data_rg'
        onFocus="document.all.data_rg.style.background='#CCFFCC'" 
        onBlur="document.all.data_rg.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,cpf.id)"
        style='background:#FFFFFF;'/>
		
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>CPF:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='cpf' type='text' class='campotexto' id='cpf' size='17' maxlength='14'
                OnKeyPress="formatar('###.###.###-##', this)" 
                onFocus="document.all.cpf.style.background='#CCFFCC'" 
                onBlur="document.all.cpf.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'
				onkeyup="pula(14,this.id,reservista.id)"/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Carteira do Conselho:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
      <input name='conselho' type='text' id='conselho' size='13' class='campotexto'
                onFocus="this.style.background='#CCFFCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Certificado de Reservista:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
      <input name='reservista' type='text' class='campotexto' id='reservista' 
	  size='18'
                onFocus="document.all.reservista.style.background='#CCFFCC'" 
                onBlur="document.all.reservista.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Nº Carteira de Trabalho:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
<input name='trabalho' type='text' class='campotexto' id='trabalho' size='15'
                onFocus="document.all.trabalho.style.background='#CCFFCC'" 
                onBlur="document.all.trabalho.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Série:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
     <input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='10'
        onfocus="document.all.serie_ctps.style.background='#CCFFCC'"
        onblur="document.all.serie_ctps.style.background='#FFFFFF'" style='background:#FFFFFF;'/>
          </span>
		  
	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>UF:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
	<input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' 
                onfocus="document.all.uf_ctps.style.background='#CCFFCC'" 
                onblur="document.all.uf_ctps.style.background='#FFFFFF'" 
				onKeyUp="pula(2,this.id,data_ctps.id)"
                style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data carteira de Trabalho:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>
      &nbsp;&nbsp;
      
      <input name='data_ctps' type='text' class='campotexto' size='12' maxlength='10' id='data_ctps'
        onFocus="document.all.data_ctps.style.background='#CCFFCC'" 
        onBlur="document.all.data_ctps.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,titulo2.id)"
        style='background:#FFFFFF;'/>
      
    </td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Nº Título de Eleitor:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='titulo' type='text' class='campotexto' id='titulo2' size='10'
                onFocus="document.all.titulo2.style.background='#CCFFCC'" 
                onBlur="document.all.titulo2.style.background='#FFFFFF'" 
                style='background:#FFFFFF;' />
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'> Zona:&nbsp;</span></div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='zona' type='text' class='campotexto' id='zona2' size='3'
                onFocus="document.all.zona2.style.background='#CCFFCC'" 
                onBlur="document.all.zona2.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Seção:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='secao' type='text' class='campotexto' id='secao' size='3'
                onFocus="document.all.secao.style.background='#CCFFCC'" 
                onBlur="document.all.secao.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style28'><span class='style39'>PIS:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='pis' type='text' class='campotexto' id='pis' size='12'
                onFocus="document.all.pis.style.background='#CCFFCC'" 
                onBlur="document.all.pis.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data Pis:&nbsp;</div></td>
    <td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;

    <input name='data_pis' type='text' class='campotexto' size='12' maxlength='10' id='data_pis'
        onFocus="document.all.data_pis.style.background='#CCFFCC'" 
        onBlur="document.all.data_pis.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,fgts.id)"
        style='background:#FFFFFF;'/>
	
	</td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>FGTS:&nbsp;</span></div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
        <input name='fgts' type='text' class='campotexto' id='fgts' size='10'
                onFocus="document.all.fgts.style.background='#CCFFCC'" 
                onBlur="document.all.fgts.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
</table>


<br />


<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' style="display:none">
  <tr>
    <td colspan='6' bgcolor='#003300' class='style1'><div align='center' class='style43'>BENEFÍCIOS</div></td>
  </tr>
  <tr height='30'>
    <td width='19%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
	Assistência Médica:&nbsp;</div>	</td>
    <td bgcolor='#FFFFFF' class='style1'>
	
	<table width='100%' class=linha>
<tr> 
<td width='74'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='1' $chek_medi1>Sim</label></td><td width='255'>&nbsp;&nbsp; 
<label><input type='radio' name='medica' value='0' $chek_medi0>Não</label>&nbsp;&nbsp; $mensagem_medi</td>
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
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
          <select name='apolice' class='campotexto' id='apolice'>
<option value='0'>Não Possui</option>

<?php
$result_ap = mysql_query("SELECT * FROM apolice where id_regiao = $id_regiao");
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
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Dependente:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
      <input name='dependente' type='text' class='campotexto' id='dependente' size='20' value=''
onFocus="document.all.dependente.style.background='#CCFFCC'" 
onBlur="document.all.dependente.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Insalubridade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
    <input name='insalubridade' type='checkbox' id='insalubridade2' value='1' $chek1/></td>
    
	<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Adicional Noturno:&nbsp;</div></td>
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
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Vale Transporte:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<input name='transporte' type='checkbox' id='transporte2' value='1' onClick="document.all.tablevale.style.display = (document.all.tablevale.style.display == 'none') ? '' : 'none' ;" $chek2 />

</td>
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
<!-- ______________________________________________________________________________________________________ -->
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
  <td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS BANCÁRIOS</div></td>
  </tr>
  <tr height='30'>
  <td width='17%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Banco:&nbsp;</div></td>
  <td width='31%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
  <select name='banco' class='campotexto' id='banco'>
  <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_projeto = '$projeto'");
while ($row_banco = mysql_fetch_array($result_banco)){
print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
}
?>
  </select>
  </span></td>
  <td width='17%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Agência:&nbsp;</div></td>
  <td width='35%' bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;&nbsp;
  <input name='agencia' type='text' class='campotexto' id='agencia' size='12' 
onFocus="document.all.agencia.style.background='#CCFFCC'" 
onBlur="document.all.agencia.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
  </span></td>
  </tr>
  <tr height='30'>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Conta:&nbsp;</div></td>
  <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class='style39'>
  <input name='conta' type='text' class='campotexto' id='conta' size='12' 
onFocus="document.all.conta.style.background='#CCFFCC'" 
onBlur="document.all.conta.style.background='#FFFFFF'" 
style='background:#FFFFFF;'/>
  </span>
  <span class='style39'> &nbsp;&nbsp;
  <label><input type='radio' name='radio_tipo_conta' value='salario'>Conta Salário </label>&nbsp;&nbsp;
  <label><input type='radio' name='radio_tipo_conta' value='corrente'>Conta Corrente </label>&nbsp;&nbsp;
  </span>
    
  </td>
  <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome do Banco:&nbsp;<br /> 
  <span class='style49'>(caso não esteja na lista acima)&nbsp;</span></div></td>
  <td bgcolor='#FFFFFF' class='style1'><span class='style39'>
  &nbsp;&nbsp;
  <input name='nomebanco' type='text' class='campotexto' id='nomebanco' size='50' 
onFocus="document.all.nomebanco.style.background='#CCFFCC'" 
onBlur="document.all.nomebanco.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
  </span></td>
  </tr>
</table>
<br>
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='6' bgcolor='#003300' class='style1'><div align='center' class='style43'>INFORMA&Ccedil;&Otilde;ES PROFISSIONAIS</div></td>
  </tr>
  <tr height='30'>
    <td width='13%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Empresa:&nbsp;</div></td>
    <td colspan="3" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
      <input name='e_nome' type='text' class='campotexto' id='e_nome' size='50' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span><span class='style39'>&nbsp;&nbsp;</span></td>
    <td width='10%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>CNPJ:&nbsp;</div></td>
    <td width='22%' bgcolor='#FFFFFF' class='style1'><span class="style39">
      &nbsp;&nbsp;
      <input name='e_cnpj' type='text' class='campotexto' id='e_cnpj' 
                style='background:#FFFFFF;' 
                onFocus="this.style.background='#CCFFCC'" 
                onBlur="this.style.background='#FFFFFF'"
                OnKeyPress="formatar('##.###.###/####-##', this)"
				onkeyup="pula(18,this.id,e_endereco.id)" size="19" maxlength='18'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Endere&ccedil;o:&nbsp;</div></td>
    <td width='24%' bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_endereco' type='text' class='campotexto' id='e_endereco' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='35'/>
    </span></td>
    <td width='11%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Bairro:&nbsp;</div></td>
    <td width='20%' bgcolor='#FFFFFF' class='style1'><span class="style37">
      &nbsp;&nbsp;
      <input name='e_bairro' type='text' class='campotexto' id='e_bairro' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Cidade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class="style39">
      &nbsp;&nbsp;<span class="style37">
      <input name='e_cidade' type='text' class='campotexto' id='e_cidade' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
      </span></span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estado:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'><span class="style37">
      &nbsp;
      <input name='e_estado' type='text' class='campotexto' id='e_estado' size='2' maxlength='2' 
onChange="this.value=this.value.toUpperCase()"
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onKeyUp="pula(2,this.id,e_cep.id)" />
&nbsp;&nbsp; </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>CEP:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_cep' type='text' class='campotexto' id='e_cep' size='10' maxlength='9' 
        style='background:#FFFFFF; text-transform:uppercase;'
        onFocus="this.style.background='#CCFFCC'" 
        onBlur="this.style.background='#FFFFFF'"
        OnKeyPress="formatar('#####-###', this)" 
        onKeyUp="pula(9,this.id,e_ramo.id)" />
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Ramo Atividade:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_ramo' type='text' class='campotexto' id='e_ramo' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='30'/>
    </span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Telefone:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_telefone' type='text' id='e_telefone' size='14' 
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,e_ramal.id)" 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Ramal:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_ramal' type='text' id='e_ramal' size='14' 
        onFocus="this.style.background='#CCFFCC'" 
        onBlur="this.style.background='#FFFFFF'" 
        style='background:#FFFFFF;' class='campotexto'>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Fax:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'> &nbsp;&nbsp;<span class="style37">
      <input name='e_fax' type='text' id='e_fax' size='14' 
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,e_email.id)" 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>E-mail:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_email' type='text' id='e_email' size='30' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style=" background:#FFFFFF; text-transform:lowercase" class='campotexto'>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Tempo de Servi&ccedil;o:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_tempo' type='text' id='e_tempo' size='14' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto' onChange="this.value=this.value.toUpperCase()">
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Profiss&atilde;o:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='e_profissao' type='text' id='e_profissao' size='14' onChange="this.value=this.value.toUpperCase()"
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Cargo:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='e_cargo' type='text' id='e_cargo' size='20' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto' onChange="this.value=this.value.toUpperCase()">
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Data Emiss&atilde;o
      :&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='e_dataemissao' type='text' class='campotexto' size='12' maxlength='10' id='e_dataemissao'
        onFocus="this.style.background='#CCFFCC'" 
        onBlur="this.style.background='#FFFFFF'" 
		onkeyup="mascara_data(this); pula(10,this.id,e_referencia.id)"
        style='background:#FFFFFF;'/></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Refer&ecirc;ncia:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style39">
      <input name='e_referencia' type='text' class='campotexto' id='e_referencia' 
                onFocus="this.style.background='#CCFFCC'" 
                onBlur="this.style.background='#FFFFFF'" 
                style='background:#FFFFFF;'/>
    </span></td>
  </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Renda:&nbsp;</div></td>
    <td colspan="5" bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style39">
      <input name='e_renda' type='text' class='campotexto' id='e_renda' style='background:#FFFFFF;'
      onFocus="this.style.background='#CCFFCC'" onBlur="this.style.background='#FFFFFF'"
      onChange="this.value=this.value.toUpperCase()" OnKeyDown="FormataValor(this,event,17,2)" size="15" />
    </span></td>
    </tr>
</table>
<br>
<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
  <tr>
    <td colspan='6' bgcolor='#003300' class='style1'><div align='center' class='style43'>REFER&Ecirc;NCIA</div></td>
    </tr>
  <tr height='30'>
    <td width='13%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Nome:&nbsp;</div></td>
    <td colspan="5" bgcolor='#FFFFFF' class='style1'><span class='style39'>&nbsp;
      <input name='r_nome' type='text' class='campotexto' id='r_nome' size='50' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
    </span><span class='style39'>&nbsp;&nbsp;</span><span class="style39"> &nbsp;&nbsp;</span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Endere&ccedil;o:&nbsp;</div></td>
    <td width='24%' bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='r_endereco' type='text' class='campotexto' id='r_endereco' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='35'/>
      </span></td>
    <td width='11%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Bairro:&nbsp;</div></td>
    <td width='20%' bgcolor='#FFFFFF' class='style1'><span class="style37"> &nbsp;&nbsp;
      <input name='r_bairro' type='text' class='campotexto' id='r_bairro' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
      </span></td>
    <td width="10%" bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Cidade:&nbsp;</div></td>
    <td width="22%" bgcolor='#FFFFFF' class='style1'><span class="style39"> &nbsp;&nbsp;<span class="style37">
      <input name='r_cidade' type='text' class='campotexto' id='r_cidade' 
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='20'/>
      </span></span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Estado:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='r_estado' type='text' class='campotexto' id='r_estado' onKeyUp="pula(2,this.id,e_cep.id)"
style='background:#FFFFFF;' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" onChange="this.value=this.value.toUpperCase()" size='2'/>
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>CEP:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_cep' type='text' class='campotexto' id='r_cep' size='10' maxlength='9' 
style='background:#FFFFFF; text-transform:uppercase;'
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'"
OnKeyPress="formatar('#####-###', this)" 
onKeyUp="pula(9,this.id,r_email.id)" />
    </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>E-mail:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_email' type='text' class='campotexto' id='r_email' 
style=" background:#FFFFFF; text-transform:lowercase" 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" size="30"/>
    </span></td>
    </tr>
  <tr height='30'>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Telefone:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;<span class="style37">
      <input name='r_telefone' type='text' id='r_telefone' size='14' 
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,r_ramal.id)" 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
      </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Ramal:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_ramal' type='text' id='r_ramal' size='14' 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
      </span></td>
    <td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Fax:&nbsp;</div></td>
    <td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class="style37">
      <input name='r_fax' type='text' id='r_fax' size='14' 
onKeyPress="return(TelefoneFormat(this,event))" 
onKeyUp="pula(13,this.id,data_entrada.id)" 
onFocus="this.style.background='#CCFFCC'" 
onBlur="this.style.background='#FFFFFF'" 
style='background:#FFFFFF;' class='campotexto'>
      </span></td>
  </tr>
  </table>
<br>

<table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'>
<tr>
<td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS FINANCEIROS E DE CONTRATO</div></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right'><span class='style39'>Data de Entrada:&nbsp;</span></div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<input name='data_entrada' type='text' class='campotexto' size='12' maxlength='10' id='data_entrada'
onFocus="document.all.data_entrada.style.background='#CCFFCC'" 
onBlur="document.all.data_entrada.style.background='#FFFFFF'" 
onkeyup="mascara_data(this); pula(10,this.id,data_exame.id)"
style='background:#FFFFFF;'/>
</td>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Data do Exame Admissional:&nbsp;</div></td>
<td bgcolor='#FFFFFF' class='style1'>&nbsp;
<input name='data_exame' type='text' class='campotexto' size='12' maxlength='10' id='data_exame'
onFocus="document.all.data_exame.style.background='#CCFFCC'" 
onBlur="document.all.data_exame.style.background='#FFFFFF'" 
onkeyup="mascara_data(this); pula(10,this.id,localpagamento.id)"
style='background:#FFFFFF;'/>
</td>
</tr>
<tr height='30'>
<td width='23%' bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Local de Pagamento:&nbsp;</div></td>
<td width='77%' colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;<span class='style39'>
<input name='localpagamento' type='text' class='campotexto' id='localpagamento' size='25'  
onFocus="document.all.localpagamento.style.background='#CCFFCC'" 
onBlur="document.all.localpagamento.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"/>
</span></td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>
Tipo de Pagamento:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>&nbsp;&nbsp;
<select name='tipopg' class='campotexto' id='tipopg'>
<?php
$result_pg = mysql_query("SELECT * FROM tipopg where id_projeto = '$projeto'");
while ($row_pg = mysql_fetch_array($result_pg)){
print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
}
?>
</select>
&nbsp;</td>
</tr>
<tr height='30'>
<td bgcolor='#CCFFCC' class='style1'><div align='right' class='style39'>Observações:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp;
<textarea name='observacoes' id='observacoes' class='campotexto' cols='55' rows='4'  
onFocus="document.all.observacoes.style.background='#CCFFCC'" 
onBlur="document.all.observacoes.style.background='#FFFFFF'" 
style='background:#FFFFFF;' onChange="this.value=this.value.toUpperCase()"></textarea></td>
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
  <p>
    
    <span class='style47'>NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</span></p>
  <table width='200' border='0' align='center' cellpadding='0' cellspacing='0'>
<tr height='30'>
<td align='center' class='style7'>&nbsp;</td>
<td align='center' valign='middle' class='style7'><input type='submit' name='Submit' value='CADASTRAR' class='campotexto' /></td>
</tr>
</table>
<br />
<div align='center'><span class='style7'>


</span><br />
</div>
</div></td>
</tr>
</table>
<span class='style7'>

<input type='hidden' name='regiao' value='<?=$id_regiao?>'/>
<input type='hidden' name='id_cadastro' value='4'>
<input type='hidden' name='projeto' value='<?=$projeto?>'>
<input type='hidden' name='user' value='<?=$id_user?>'>
<input type='hidden' name='update' value='1'>

</span></tr>
</table>
</form><br><a href='../ver.php?projeto=<?=$projeto?>&regiao=<?=$id_regiao?>' class='link'><img src='../imagens/voltar.gif' border=0></a>


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
if (d.localpagamento.value == "" ){
alert("O campo Local de Pagamento deve ser preenchido!");
d.localpagamento.focus();
return false;
}
return true;   }
</script>
<?php 

}else{                       //CADASTRO DE AUTONOMOS/COOPERADOS

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['projeto'];

//DADOS CONTRATAÇÃO
$vinculo = $_REQUEST['vinculo'];
$id_curso = $_REQUEST['atividade'];
$locacao = $_REQUEST['locacao'];
$tipo_contratacao = $_REQUEST['contratacao'];

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
$campo3 = $_REQUEST['codigo'];

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

$trabalho_data = $_REQUEST['data_ctps'];

$reservista = $_REQUEST['reservista'];
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


if(empty($_REQUEST['foto'])){
$foto = "0";
}else{
$foto = $_REQUEST['foto'];
}

if($foto == "1"){
  $foto_banco = "1";
  $foto_up = "1";
}else{
  $foto_banco = "0";
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
$pis_data     = ConverteData($pis_data);
$exame_data   = ConverteData($exame_data);
$trabalho_data = ConverteData($trabalho_data);
$c_nascimento = ConverteData($c_nascimento);
$e_dataemissao = ConverteData($e_dataemissao);

$data_cadastro = date('Y-m-d');

//VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELa
$verificando_clt = mysql_query("SELECT nome FROM autonomo WHERE nome = '$nome' and data_nasci = '$data_nasci' and rg = '$rg'");
$row_verificando_clt = mysql_num_rows($verificando_clt);

if ($row_verificando_clt >= "1") { //ABRIU O 2 IF

	print "<br>
	<link href='../net.css' rel='stylesheet' type='text/css'>
	<body bgcolor='#D7E6D5'>
	<center>
	<br>ESTE FUNCIONÁRIO JA ESTÁ CADASTRADO: <font color=#FFFFFF><b>$nome</b></font>
	</center>
	</body>";
	
	exit; 

} else { //CASO O FUNCIONÁRIO NÃO ESTEJA CADASTRADO VAI RODAR O INSERT //ELSE 2 IF

	$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'");
	$row_projeto = mysql_fetch_array($result_projeto);
	$data_cadastro = date('Y-m-d');
	$id_user = $_COOKIE['logado'];
	
	// GERANDO NOVAMENTE O CÓDIGO, PARA NÃO HAVER NÚMEROS DUPLICADOS NA TABELA
	$resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3 , 
	MAX(campo3) 
	FROM autonomo 
	WHERE id_regiao= '$regiao' 
	AND id_projeto ='$id_projeto' 
	AND campo3 != 'INSERIR' 
	GROUP BY campo3 DESC 
	LIMIT 0,1");
	$row_maior = mysql_fetch_array ($resut_maior); 
	
	$codigo = $row_maior[0] + 1;
	$codigo = sprintf("%04d",$codigo);
	
mysql_query ("insert into autonomo
(id_projeto,id_regiao,localpagamento,locacao,nome,sexo,endereco,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,
data_nasci,naturalidade,nacionalidade,civil,rg,orgao,data_rg,cpf,conselho,titulo,zona,secao,pai,nacionalidade_pai,mae,nacionalidade_mae,
estuda,data_escola,escolaridade,instituicao,curso,tipo_contratacao,banco,agencia,conta,tipo_conta,id_curso,apolice,data_entrada,campo1,campo2,
campo3,data_exame,reservista,cabelos,altura,olhos,peso,defeito,cipa,ad_noturno,plano,assinatura,distrato,
outros,pis,dada_pis,data_ctps,serie_ctps,uf_ctps,uf_rg,fgts,insalubridade,transporte,medica,tipo_pagamento,nome_banco,num_filhos,
observacao,impressos,sis_user,data_cad,foto,id_cooperativa,c_nome,c_cpf,c_nascimento,c_profissao,e_empresa,e_cnpj,e_ramo,e_endereco,e_bairro,e_cidade,e_estado,e_cep,e_tel,e_ramal,e_fax,e_email,e_tempo,e_profissao,e_cargo,e_renda,e_dataemissao,e_referencia,r_nome,r_endereco,r_bairro,r_cidade,
r_estado,r_cep,r_tel,r_ramal ,r_fax,r_email,rh_vinculo,rh_status,rh_horario,rh_sindicato,rh_cbo) 
VALUES
('$id_projeto','$regiao','$localpagamento','$locacao','$nome','$sexo','$endereco','$bairro','$cidade','$uf',
'$cep','$tel_fixo','$tel_cel','$tel_rec','$data_nasci','$naturalidade','$nacionalidade','$civil','$rg',
'$orgao','$data_rg','$cpf','$conselho','$titulo','$zona','$secao','$pai','$nacionalidade_pai','$mae','$nacionalidade_mae','$estuda',
'$data_escola','$escolaridade','$instituicao','$curso','$tipo_contratacao','$banco','$agencia','$conta','$tipoDeConta','$id_curso','$apolice',
'$data_entrada','$campo1','$campo2','$codigo','$exame_data','$reservista','$cabelos','$altura','$olhos','$peso','$defeito','$cipa',
'$ad_noturno','$plano_medico','$impressos','$assinatura2','$assinatura3','$pis','$pis_data','$trabalho_data','$serie_ctps',
'$uf_ctps','$uf_rg','$fgts','$insalubridade','$transporte','$medica','$tipopg','$nomebanco','$filhos','$observacoes','$impressos',
'$id_user','$data_cadastro','$foto_banco','$vinculo','$c_nome','$c_cpf','$c_nascimento','$c_profissao','$e_nome','$e_cnpj',
'$e_ramo','$e_endereco','$e_bairro','$e_cidade','$e_estado','$e_cep','$e_tel','$e_ramal','$e_fax','$e_email','$e_tempo','$e_profissao',
'$e_cargo','$e_renda','$e_dataemissao','$e_referencia','$r_nome','$r_endereco','$r_bairro','$r_cidade','$r_estado','$r_cep','$r_tel',
'$r_ramal','$r_fax','$r_email','$rh_vinculo','$rh_status','$rh_horario','$rh_sindicato','$rh_cbo')") or die ("Ops! Erro<br>" . mysql_error());

$row_id_participante = mysql_insert_id();
$row_id_clt = $row_id_participante;

}//AQUI TERMINA DE INSERIR OS DADOS DO CLT   //FECA O 2 IF


$id_bolsista = $row_id_participante;

//VALE TRANSPORTE
if($transporte == "1"){
mysql_query ("insert into rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,
id_tarifa5,id_tarifa6,cartao1,cartao2) values 
('$row_id_participante','$regiao','$projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$num_cartao','$num_cartao2')") or die ("$mensagem_erro - 2.3<br><br>".mysql_error());
}

//DEPENDENTES
if($filho_1 == "" and $filho_2 == "" and $filho_3 == "" and $filho_4 == "" and $filho_5 == ""){
	$naa = "";
}else{
	mysql_query ("insert into dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,
	nome4,data5,nome5) values ('$regiao','$id_projeto','$row_id_participante','$tipo_contratacao','$nome','$data_filho_1','$filho_1','$data_filho_2',
	'$filho_2','$data_filho_3','$filho_3','$data_filho_4','$filho_4','$data_filho_5','$filho_5')") or die 
	("$mensagem_erro 2.4<br><br>".mysql_error());
	$naa = "2";
}

//---------------------------------//
//---- SENHA PARA A TV SORRINDO ---//
//---------------------------------//

$n_id_curso = sprintf("%04d",$id_curso);
$n_regiao = sprintf("%04d",$regiao);
$n_id_bolsista = sprintf("%04d",$row_id_participante);

$cpf2 = str_replace(".","", $cpf);
$cpf2 = str_replace("-","", $cpf2);

// GERANDO A SENHA ALEATÓRIA
$target = "%%%%%%";

    $senha = "";
	$dig = "";
    $consoantes = "bcdfghjkmn123456789pqrstvwxyz1234567890bcdfghj123456789kmnpqrstvwxyz123456789"; 
    $vogais = "aeiou"; 
    $numeros = "123456789bcdfghjkmnpqrstvwxyzaeiou123456789"; 

    $a = strlen($consoantes)-1; 
    $b = strlen($vogais)-1; 
    $c = strlen($numeros)-1; 

    for($x=0;$x<=strlen($target)-1;$x++) 
    { 
        if(substr($target,$x,1) == "@") { 
            $rand = mt_rand(0,$c); 
            $senha .= substr($numeros,$rand,1); 
        } elseif(substr($target,$x,1) == "%") { 
            $rand = mt_rand(0,$a); 
            $senha .= substr($consoantes,$rand,1); 
        } elseif(substr($target,$x,1) == "&") { 
            $rand = mt_rand(0,$b); 
            $senha .= substr($vogais,$rand,1); 
        } else { 
            die("<b>Erro!</b><br><i>$target</i> é uma expressão inválida!<br><i>".substr($target,$x,1)."</i> é um caractér inválido.<br>"); 
        } 
    } 

$matricula = "$n_id_curso.$n_regiao.$n_id_bolsista-00";

mysql_query ("insert into tvsorrindo(id_clt,id_projeto,nome,cpf,matricula,senha,inicio) values
('$row_id_participante','$id_projeto','$nome','$cpf','$matricula','$senha','$inicio')") or die ("$mensagem_erro<br><Br>");

$id_tv = mysql_insert_id();

mysql_query ("UPDATE autonomo SET tvsorrindo = '$id_tv', senhatv = '$senha' WHERE id_autonomo = '$row_id_participante'");

//------------------------------

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
	$nome_tmp = $regiao."_".$id_projeto."_".$row_id_participante.$tipo_arquivo;
	$nome_arquivo = "$diretorio$nome_tmp" ;
	
	move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

}
}

//ver_bolsista.php?reg=24&bol=5237&pro=3220
$link_fim = "../ver_bolsista.php?reg=$regiao&bol=$row_id_participante&pro=$id_projeto";

print"
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='../net.css' rel='stylesheet' type='text/css'>

<style type='text/css'>
<!--
.style1 {color: #FF0000;
	font-weight: bold;}
.style5 {font-size: 12px}
.style6 {font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;}
.style7 {color: #FF0000}
.style11 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; }
.style13 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style15 {color: #FF0000; font-weight: bold; font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style16 {font-size: 11px}
-->
</style>
</head>

<body bgcolor='#FFFFFF'>
<center>
<br>FUNCIONÁRIO CADASTRADO COM SUCESSO!<br>
<br>O que você gostaria de fazer agora?<br>
<a href='cadcooperado.php?regiao=$regiao&pro=$id_projeto' style='color:#FFFFFF'>Cadastrar outro COOPERADO no mesmo Projeto.</a>
<br>
<a href='$link_fim' style='color:#FFFFFF'>Visualizar o COOPERADO cadastrado.</a>
</center>
</body>
</html>

";

}


?>
</body></html>