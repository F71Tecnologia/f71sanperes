<?php
if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
	exit;
}

include "conn.php";

$id = $_REQUEST['id'];

?>
<html><head><title>:: Intranet ::</title>

<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<meta http-equiv='Cache-Control' content='No-Cache'>
<meta http-equiv='Pragma'        content='No-Cache'>
<meta http-equiv='Expires'       content='No-Cache'>

<meta http-equiv='Expires' content='Fri, Jan 01 1900 00:00:00 GMT'/>   
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'/>   
<meta http-equiv='Cache-Control' content='post-check=0, pre-check=0'/>   
<meta http-equiv='Pragma' content='no-cache'/>

<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>

<script type="text/javascript" src="js/highslide-with-html.js"></script>
<link rel="stylesheet" type="text/css" href="js/highslide.css" />

<link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen" />
<link href="net1.css" rel="stylesheet" type="text/css">

<script type="text/javascript">
    hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>


<script type='text/javascript' src='js/ramon.js'></script>

</head>
<body>
<?php

switch ($id){

case 1:												//VISUALIZAR TODOS OS CURSOS / ATIVIDADES

?>

<br>
<div class="divtitulo">Atividades / Cursos Cadastrados</div>
<br />

<?php
$regiao = $_REQUEST['regiao'];
$result_pro = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' ORDER BY nome");

while ($row_pro = mysql_fetch_array($result_pro)){

?>

<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr class="linha" bgcolor="#CCCCCC">
    <td height="35" colspan="7" class="show">&gt;&gt; Projeto: <?=$row_pro['nome']?></td>
  </tr>
</table>

<table width="90%" border="0" align="center" cellspacing="0" bgcolor="#FFFFFF" id="tab_<?=$row_pro['0']?>" style="display:">
<tr class="secao" bgcolor="#CCCCCC">
<td height="25" align="center">Cód</td>
<td align="center">Curso</td>
<td align="center">Atividade</td>
<td align="center">Instituíção</td>
<td align="center">Valor</td>
<td align="center">Hora Mes</td>
<td align="center">TIPO</td></tr>

<?php
$result = mysql_query("SELECT * FROM curso WHERE id_regiao =  '$regiao' AND status = '1' AND campo3 = '$row_pro[0]' ORDER BY nome");
$cont = 0;
while ($row = mysql_fetch_array($result)){

$salario = number_format($row['salario'],2,",",".");

if($row['tipo'] == 1){
	$tipo = '<span class="aut">Autonomo</span>';
}elseif($row['tipo'] == 2){
	$tipo = '<span class="clt">CLT</span>';
}elseif($row['tipo'] == 3){
	$link = "<a href='ver_tudo.php?id=2&ativi=$row[0]&regiao=$regiao' target='_blanck'>";
	$tipo = '<span class="coo">Cooperado</span>';
}else{
	$tipo = '<span class="aut">Autonomo/PJ</span>';
}

$stilo = ($cont % 2) ? "corfundo_um" : "corfundo_dois";

?>

<tr class='novalinha <?=$stilo?>'>
<td><?=$row[0]?></td>
<td><?=$link." ".$row['nome']?></a></td>
<td><?=$row['campo2']?></td>
<td><?=$row['campo1']?></td>
<td><?="R$ ".$salario?></td>
<td><?=$row['hora_mes']?></td>
<td><?=$tipo?></td></tr>


<?php
unset($link);
$cont ++;
}  
?>
</table><br><br><br>
<?php
unset($cont);
}

?>


</table><center><a href='javascript:window.location.reload()' class='link'><font color=#FFFFFF>ATUALIZAR</fong></a></center>
<?php

break;



case 2:  						// VISUALIZAR AS INFORMAÇÕES ESPECIFICAS DOS CURSOS

$ativi = $_REQUEST['ativi'];
$regiao = $_REQUEST['regiao'];

$nomeid = "id_curso";				//VARIAVEL PARA O AJAX
$tabela = "curso";					//VARIAVEL PARA O AJAX
$tipoaj = "1";						//VARIAVEL PARA O AJAX ( TIPO DO CAMPO ESPECIAL OU NÃO EX: VALOR OU DATA )

include "classes/curso.php";
$curso = new tabcurso();
$curso -> MostraCurso($ativi);

//Declarando as Variaveis
$nome = $curso -> nome;
$campo2 = $curso -> campo2;
$area = $curso -> area;
$local = $curso -> local;
$salario = $curso -> salario;
$valor = $curso -> valor;
$parcelas =  $curso -> parcelas;
$descricao = $curso -> descricao;
$hora_mes = $curso -> hora_mes;

print "
<body onLoad=\"limpaCache('ver_tudo.php')\">
<table width='95%' border='1' align='center' cellpadding='0' cellspacing='0' id='tabelaoutros' bordercolor='#999999'>
<tr>
<td colspan='4' bgcolor='#003300' class='style1'><div align='center' class='style43'>DADOS DO CURSO</div></td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='ramon'><div align='right' class='ramon'>
Nome da Atividade:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='ramon'>
&nbsp;&nbsp; 
<input name='nome' type='text' class='campotexto' id='nome' size='50' value = '$nome'
onFocus=\"this.style.background='#CCFFCC'\"
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','$tipoaj','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />
</td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='ramon'><div align='right' class='ramon'>
Nome do Curso:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='ramon'>
&nbsp;&nbsp; 
<input name='campo2' type='text' class='campotexto' id='campo2' size='50' value='$campo2'
onFocus=\"this.style.background='#CCFFCC'\"
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','$tipoaj','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />
</td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='ramon'>
Área:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='area' type='text' class='campotexto' id='area' size='40' value='$area'
onFocus=\"this.style.background='#CCFFCC'\"
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','$tipoaj','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />
</td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='ramon'>
Local:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
&nbsp;&nbsp; 
<input name='local' type='text' class='campotexto' id='local' size='40' value='$local'
onFocus=\"this.style.background='#CCFFCC'\"
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','$tipoaj','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />
</td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='ramon'>
Valor:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
<div class='ramon'>&nbsp;&nbsp; 
<input name='salario' type='text' id='salario' size='11' class='campotexto' maxlength='13' value='$salario'
OnKeyDown=\"FormataValor(this,event,17,2)\"
onFocus=\"this.style.background='#CCFFCC'\" 
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','2','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />

&nbsp;&nbsp;&nbsp;&nbsp;
Parcelas:&nbsp;&nbsp;

<input name='parcelas' type='text' id='parcelas' size='10' class='campotexto' maxlength='13' value='$parcelas'
onFocus=\"this.style.background='#CCFFCC'\" 
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','$tipoaj','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />

</div>
</td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='style1'><div align='right' class='ramon'>
Horas Mes:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='style1'>
<div class='ramon'>&nbsp;&nbsp; 

<input name='hora_mes' type='text' id='hora_mes' size='11' class='campotexto' value='$hora_mes'
onFocus=\"this.style.background='#CCFFCC'\" 
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','$tipoaj','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />

</div>
</td>
</tr>

<tr>
<td height='30' bgcolor='#CCCCCC' class='ramon'><div align='right' class='ramon'>
Descrição:&nbsp;</div></td>
<td colspan='3' bgcolor='#FFFFFF' class='ramon'>
&nbsp;&nbsp; 
<textarea name='descricao' cols='35' rows='5' class='campotexto'  id='descricao'
onFocus=\"this.style.background='#CCFFCC'\" 
onBlur=\"ajaxUpload('$tabela',this.value,this.id,'$nomeid','$ativi','$tipoaj','0')\" 
onChange=\"this.value=this.value.toUpperCase()\" />$descricao</textarea>
</td>
</tr>

<tr>
<td height='40' class='ramon' bgcolor='#FFFFFF' colspan='4' align='center'><a href='ver_tudo.php?id=1&regiao=$regiao'> voltar </a></td>
</tr>

</table>


";

break;

case 3:

$regiao = $_REQUEST['regiao'];

$result = mysql_query("Select * from atividade where id_regiao = '$regiao'");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Atividades Cadastradas</font></b></center><br><table bgcolor=#FFFFFF width='500' align='center'><tr class='linha' bgcolor=#CCCCCC><td align=center width='50%'>Atividade:</td><td align=center>Área:</a></tr>";
while ($row = mysql_fetch_array($result)){
print "<tr class='linha'><td><center>$row[nome]</center></td><td><center>$row[area]</center></td></tr>";
}  
print "</table><br><br><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";


break;

case 4:                                        //VISUALIZAR BANCOS

$regiao = $_REQUEST['regiao'];

$result_pro = mysql_query("Select * from projeto where id_regiao = $regiao ORDER BY nome");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Bancos</font></b></center>";

while ($row_pro = mysql_fetch_array($result_pro)){

$result = mysql_query("Select * from bancos where id_regiao = '$regiao' and id_projeto = '$row_pro[0]' and status_reg = '1'");

print "
<table bgcolor=#FFFFFF width='90%' align='center'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center colspan='8'>
<font size=2>Projeto: $row_pro[2]</font></td></tr>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center width='25'>-</td>
<td align=center>Cód</td>
<td align=center>Banco</td>
<td align=center>Agencia</td>
<td align=center>Conta</td>
<td align=center>Endereco</td>
<td align=center>Telefone</td>
<td align=center>Gerente</td></tr>";

$cont = "0";

while ($row = mysql_fetch_array($result)){

 if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }
   
print "<tr class='linha' bgcolor=$color>
<td bgcolor=#FFFFFF align='center'><img src='imagens/bancos/$row[id_nacional].jpg' width='25' height='25' align='absmiddle'></td>
<td>$row[0]</td>
<td>$row[nome]</td>
<td>$row[agencia]</td>
<td>$row[conta]</td>
<td>$row[endereco]</td>
<td>$row[tel]</td>
<td>$row[gerente]</td></tr>";

$cont ++;
}
print "</table><br>";
}
print "<br><br><center><a href='javascript:window.location.reload()' class='link'><font color=#FFFFFF>ATUALIZAR</fong></a></center>";


break;

case 5:                                        //VISUALIZAR APÓLICES

$regiao = $_REQUEST['regiao'];

$result = mysql_query("Select * from apolice where id_regiao = '$regiao' and status_reg = '1'");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Visualizando Apólice</font></b></center><br><br>
<table bgcolor=#FFFFFF width='500' align='center'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center>Cód</td>
<td align=center>Banco</td>
<td align=center>Apólice</td>
<td align=center>Contrato</td>
<td align=center>Telefone</td>
<td align=center>Gerente</td></tr>";
$cont = "1";
while ($row = mysql_fetch_array($result)){

 if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }
   
print "<tr class='linha' bgcolor=$color>
<td>$row[0]</td>
<td>$row[razao]</td>
<td>$row[apolice]</td>
<td>$row[contrato]</td>
<td>$row[tel]</td>
<td>$row[gerente]</td></tr>";

//print "<tr class='linha' bgcolor=$color><td>$row[razao]</td><td>$row[apolice]</td><td>$row[contrato]</td><td>$row[tel]</td><td>$row[gerente]</td><td align=center><span onClick=\"document.all.linha$row[0].style.display = (document.all.linha$row[0].style.display == 'none') ? '' : 'none'; \">X</td></tr>";

//print "<form action='cadastro2.php' method='post'>
//<tr class='linha' style='display:none' bgcolor='$color' id='linha$row[0]'>
//<td><input type='text' name='razao' id='razao' value='$row[razao]' size=10></td>
//<td><input type='text' name='apolice' id='apolice' value='$row[apolice]' size=10></td>
//<td><input type='text' name='contrato' id='contrato' value='$row[contrato]' size=10></td>
//<td><input type='text' name='tel' id='tel' value='$row[tel]' size=10></td>
//<td><input type='text' name='gerente' id='gerente' value='$row[gerente]' size=10></td>
//<td><input type='submit' value='Enviar'></td></tr></form>";

$cont ++;
}  
print "</table><br><center><a href='javascript:window.location.reload()' class='link'><font color=#FFFFFF>ATUALIZAR</fong></a></center>";


break;

case 6:                                        //VISUALIZAR USUÁRIOS

$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];

$result_user = mysql_query("Select * from funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$result = mysql_query("Select * from funcionario where id_regiao = '$regiao' and status_reg = '1' ORDER BY nome");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Funcionários</font></b></center>
<table bgcolor=#FFFFFF align='center' width='90%'>
<tr class='linha' bgcolor=#CCCCCC><td align=center>Nome</td><td align=center>Função</td><td align=center>Nome no Sistema</td><td align=center>Folha de Ponto</td></tr>";
$cont = "1";
while ($row = mysql_fetch_array($result)){

 if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }

if($row_user['tipo_usuario'] == "1"){
$link = "<a href=cadastro.php?id=12&user=$row[0]&pag=2>$row[nome]</a>";
}else{
$link = "$row[nome]";
}

print "<tr class='linha' bgcolor=$color>
<td>$link</td>
<td>$row[funcao]</td><td>$row[nome1]</td><td><a href='ponto.php?id=1&user=$row[0]&id_reg=$regiao' target='blank'>VER</a></td>
</tr>";
//print "<tr class='linha' bgcolor=$color><td>$row[nome]</td><td>$row[funcao]</td><td>$row[nome1]</td></tr>";

$cont ++;
}  
print "</table><br><br><center><br><a href='javascript:window.location.reload()' class='link'><font color=#FFFFFF>ATUALIZAR</fong></a></center>";


break;

case 7:                                        //VISUALIZAR UNIDADES

$regiao = $_REQUEST['regiao'];

$result_pro = mysql_query("Select * from projeto where id_regiao = $regiao ORDER BY nome");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Unidades</font></b></center>";

while ($row_pro = mysql_fetch_array($result_pro)){

$result = mysql_query("Select * from unidade where id_regiao = '$regiao' and campo1 = '$row_pro[0]' and status_reg = '1' ORDER BY unidade");

print "
<table bgcolor=#FFFFFF width='90%' align='center'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center colspan='6'><font size=2>Projeto: $row_pro[2]</font></td></tr>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center>Cód</td>
<td align=center>Unidade</td>
<td align=center>Telefone</td>
<td align=center>Endereço</td>
<td align=center>Responsavel</td>
<td align=center>Email</td></tr>";
$cont = "0";
while ($row = mysql_fetch_array($result)){

if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }

print "<tr class='linha' bgcolor=$color>
<td>$row[0]</td>
<td>$row[2]</td>
<td>$row[4]</td>
<td>$row[3]</td>
<td>$row[6]</td>
<td>$row[8]</td></tr>";

$cont ++;
  
} 
print "</table><br><br><br>";
}
print "</table><center><a href='javascript:window.location.reload()' class='link'><font color=#FFFFFF>ATUALIZAR</fong></a></center>";


break;

case 8:					//VER TIPO DE PAGAMENTOS

$regiao = $_REQUEST['regiao'];

$result_pro = mysql_query("Select * from projeto where id_regiao = $regiao ORDER BY nome");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Tipos de Pagamentos</font></b></center>";

while ($row_pro = mysql_fetch_array($result_pro)){

$result = mysql_query("Select * from tipopg where id_regiao = '$regiao' and id_projeto = '$row_pro[0]' and status_reg = '1'");

print "
<table bgcolor=#FFFFFF width='90%' align='center'>
<tr class='linha' bgcolor=#CCCCCC><td align=center colspan='2'><font size=2>Projeto $row_pro[2]</font></td></tr>";

$cont = "0";

while ($row = mysql_fetch_array($result)){

 if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }
   
print "<tr class='linha' bgcolor=$color><td>$row[tipopg]</td></tr>";

$cont ++;
}
print "</table><br>";
}
print "<br><center><a href='javascript:window.location.reload()' class='link'><font color=#FFFFFF>ATUALIZAR</fong></a></center>";

break;


case 9:

$regiao = $_REQUEST['regiao'];

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Escolha o Projeto desejado</font></b></center><br>
<form action='ver_tudo.php' method='post' name='form1' onSubmit=\"return validaForm()\">

<table width='70%' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center' class='linha'>
<tr>
<td width='3%' valign='top'><img src='imagens/arre_cima1.gif' width='21' height='18' /></td>
<td width='94%'>&nbsp;</td>
<td width='3%' align='right' valign='top'><img src='imagens/arre_cima2.gif' alt='' width='18' height='21' /></td>
</tr>
<tr>
<td height='100'>&nbsp;</td>
<td>
<center>
Projeto:
<br><br>
<select name='id_projeto' id='id_projeto' class='campotexto'>";

$result_grupo = mysql_query("SELECT * FROM projeto where id_regiao = $regiao");
while ($row_grupo = mysql_fetch_array($result_grupo)){
print "<option value='$row_grupo[id_projeto]'>$row_grupo[nome]</option>";
}

print "</select>

<br>
<input type='hidden' name='id' value='10'>
<input type='hidden' name='regiao' value='$regiao'>
<br><br>
<br><br>
<input type='submit' name='Submit' value='Enviar' class='campotexto'>


</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td valign='bottom'><img src='imagens/arre_baixo1.gif' alt='' width='18' height='21' /></td>
    <td>&nbsp;</td>
    <td valign='bottom' align='right'><img src='imagens/arre_baixo2.gif' alt='' width='21' height='18' /></td>
  </tr>
</table><br>";

print "
</fomr>
<br><br><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";


break;

// ----------------------------- SELECIONAR AS DATAS --------------------------
case 10:

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];

$result = mysql_query("Select * from projeto where id_projeto = '$id_projeto' and status_reg = '1'");
$row = mysql_fetch_array($result);

$result2 = mysql_query("Select *, date_format(data_ini, '%d/%m/%Y')as data_ini2, date_format(data_fim, '%d/%m/%Y')as data_fim2, date_format(data_pro, '%d/%m/%Y')as data_pro2 from folhas where projeto = '$id_projeto' and status_reg = '1' and tipo_folha = '1' ORDER BY mes");

$result3 = mysql_query("Select *, date_format(data_ini, '%d/%m/%Y')as data_ini2, date_format(data_fim, '%d/%m/%Y')as data_fim2, date_format(data_pro, '%d/%m/%Y')as data_pro2 from folhas where projeto = '$id_projeto' and status_reg = '1' and tipo_folha = '2' ORDER BY mes");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>

<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr><td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/verbolsistas.gif'> <br> <br></div><BR></td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>

<tr><td align='center' colspan='2'>Projeto</td></tr>
<tr><td align='center' colspan='2'>&nbsp;</td></tr>
<tr><td colspan='2' align='center'><font size=3> $row[nome]</font></td></tr>
<tr><td align='right'>&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
<tr><td align='right'>&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
<tr><td align='center' colspan=2> Folhas deste projeto:<br><br>";

while($row2 = mysql_fetch_array($result2)){

print "Folha mes: $row2[mes] - $row2[qnt_dias] Dias - inicio: $row2[data_ini2] até $row2[data_fim2] &nbsp;&nbsp;&nbsp;&nbsp;
<a href=ver_tudo.php?id=13&mes=$row2[mes]&regiao=$regiao&id_projeto=$id_projeto&qnt_dias=$row2[qnt_dias]&data_ini=$row2[data_ini2]&data_fim=$row2[data_fim2] target=_blank >
Ver Folha</a>
<br>
";

}
print "<br><br></td></tr>

<tr>
<td align='center' colspan=2>

<form action='ver_tudo.php' method='post' name='form2' id='form2' target='iframe1'>

Informe o mês do Pagamento: <br><br>
<select name='mes_pagamento' class='campotexto' id='mes_pagamento'>
<option value='01'>Janeiro</option>
<option value='02'>Fevereiro</option>
<option value='03'>Mar&ccedil;o</option>
<option value='04'>Abril</option>
<option value='05'>Maio</option>
<option value='06'>Junho</option>
<option value='07'>Julho</option>
<option value='08'>Agosto</option>
<option value='09'>Setembro</option>
<option value='10'>Outubro</option>
<option value='11'>Novembro</option>
<option value='12'>Dezembro</option>
</select>

<br><br>

<hr><br>
<h2><center> ADIANTAMENTO </center></h2>

";

while($row3 = mysql_fetch_array($result3)){

print "Adiantamento do mes: $row3[mes] - até dia $row3[data_fim2] &nbsp;&nbsp;&nbsp;&nbsp;
<a href=adiantamento.php?id=2&mes=$row3[mes]&regiao=$regiao&id_projeto=$id_projeto&data_ini=$row3[data_ini2]&data_fim=$row3[data_fim2] target=_blank >
Ver Adiantamento</a>
<br>
";

}
print "

<br>
<br>

<input type='checkbox' name='adiantamento' value='1'> Selecione para gerar o ADIANTAMENTO


<br><br>


<hr><br>
<h2><center> PAGAMENTO </center></h2>

Selecione a data para gerar a folha de pagamento ou o adiantamento:
<br><br>

<table width='241' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td width='66'>&nbsp;
<select name='dia' class='campotexto' id='dia'>
<option value='01'>01</option>
<option value='02'>02</option>
<option value='03'>03</option>
<option value='04'>04</option>
<option value='05'>05</option>
<option value='06'>06</option>
<option value='07'>07</option>
<option value='08'>08</option>
<option value='09'>09</option>
<option value='10'>10</option>
<option value='11'>11</option>
<option value='12'>12</option>
<option value='13'>13</option>
<option value='14'>14</option>
<option value='15'>15</option>
<option value='16'>16</option>
<option value='17'>17</option>
<option value='18'>18</option>
<option value='19'>19</option>
<option value='20'>20</option>
<option value='21'>21</option>
<option value='22'>22</option>
<option value='23'>23</option>
<option value='24'>24</option>
<option value='25'>25</option>
<option value='26'>26</option>
<option value='27'>27</option>
<option value='28'>28</option>
<option value='29'>29</option>
<option value='30'>30</option>
<option value='31'>31</option>
</select></td>
<td width='104'><select name='mes' class='campotexto' id='mes'>
<option value='01'>Janeiro</option>
<option value='02'>Fevereiro</option>
<option value='03'>Mar&ccedil;o</option>
<option value='04'>Abril</option>
<option value='05'>Maio</option>
<option value='06'>Junho</option>
<option value='07'>Julho</option>
<option value='08'>Agosto</option>
<option value='09'>Setembro</option>
<option value='10'>Outubro</option>
<option value='11'>Novembro</option>
<option value='12'>Dezembro</option>
</select></td>
<td width='71'><select name='ano' class='campotexto' id='ano'>";

for ($i = 2008; $i <= 2018; $i++) {
$ano_agora = date('Y');
  if($i == $ano_agora){
	  print "<option value='$i' selected>$i</option>";
  }else{
	  print "<option value='$i'>$i</option>";
  }

}

print "
</select></td>
</tr>
</table>
<br>
<br>

Quantidade de Dias: &nbsp;&nbsp;<input name='qnt_dias' type='text' class='campotexto' id='qnt_dias' size='3'> 

&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='Submit' value='CALCULAR' class='campotexto'>

<br><br>


<span id='resultado2'>Resultado</span><br><br>


<iframe width='90%' height='100' src='ver_tudo.php?id=11&id_projeto=$id_projeto&regiao=$regiao' frameborder='0' scrolling='no' name='iframe1'></iframe>

<br><br>
</td>
</tr>
<tr>
<td align='center' colspan=2> &nbsp;</td>
</tr>
</table>";

print "
<input type='hidden' name='id' value='11'>
<input type='hidden' name='id_projeto' value='$id_projeto'>
<input type='hidden' name='regiao' value='$regiao'>
</fomr>
<br><br><br><a href='ver_tudo.php?id=9&regiao=$regiao' class='link'><img src='imagens/voltar.gif' border=0></a>";

break;

// --------------------------- CALCULANDO AS DATAS PARA GERAR A FOLHA --------------------------
case 11:

if(empty($_REQUEST['dia'])){
print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>
<body topmargin='0'>
</body>
</html>";

}else{

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$qnt_dias = $_REQUEST['qnt_dias'];
$qnt_dias1 = "$qnt_dias" - "1";
$dia = $_REQUEST['dia'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$mes_pagamento = $_REQUEST['mes_pagamento'];

$data = "$dia/$mes/$ano";
$data_ini_adianta = "01/$mes/$ano";

$data_fim = date("d/m/Y", mktime(0, 0, 0, $mes, $dia+$qnt_dias1, $ano));

if(empty($_REQUEST['adiantamento'])){
$adiantamento = "0";
$link157 = "
Data Inicial: $data &nbsp;&nbsp; - &nbsp;&nbsp;Data Final: $data_fim <br>
Pagando Folha do mes: $mes_pagamento 
</span><br>
<a href='ver_tudo.php?id=12&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data&data_fim=$data_fim&qnt_dias=$qnt_dias&mes_pagamento=$mes_pagamento' class='link3' target='_black'>Gerar Folha</a>";
}else{
$adiantamento = $_REQUEST['adiantamento'];
$link157 = "
Data Inicial: $data_ini_adianta &nbsp;&nbsp; - &nbsp;&nbsp;Data Final: $data <br>
Adiantamento do mes: $mes_pagamento</span><br>
<a href='adiantamento.php?id=1&projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini_adianta&data_fim=$data&mes_pagamento=$mes_pagamento' class='link3' target='_black'>Gerar Adiantamento</a>";
}


print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>
<body topmargin='0'>
<center><span class='style28'>
$link157
</center>
</body>
</html>";

}

break;


// ----------------- FOLHA SIMPLES - INSERIR ALGUNS DADOS NA TABELA FOLHA -------------------
case 12:

//FORMATANDO DATA
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

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$qnt_dias = $_REQUEST['qnt_dias'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$mes = $_REQUEST['mes_pagamento'];

$data_pro = date('Y-m-d');

$data_ini_f = ConverteData($data_ini);
$data_fim_f = ConverteData($data_fim);
$data_pg_f = ConverteData($data_pg);
$data_pro2 = ConverteData($data_pro);

$result_folhas_c = mysql_query("SELECT id_folha FROM folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '1'");
$row_folhas_c = mysql_num_rows($result_folhas_c);


if($row_folhas_c == "0"){
mysql_query("INSERT INTO folhas(mes,projeto,data_pro,data_ini,data_fim,qnt_dias,tipo_folha) VALUES ('$mes','$id_projeto','$data_pro','$data_ini_f','$data_fim_f','$qnt_dias','1')");

//where data_entrada < '2009-01-01' and data_saida = '0000-00-00' and id_projeto = '10' and status = '1'

// BOLSISTA QUE ENTROU ANTES DA DATA INICIAL E NÃO SAIU
$result1 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida from autonomo where data_entrada < '$data_ini_f' and data_saida = '0000-00-00' and status = '1' and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_1 = mysql_num_rows($result1);

// BOLSISTA QUE ENTROU ANTES DA DATA INICIAL E SAÍU ANTES DE FECHAR A FOLHA
$result2 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida2, date_format(data_saida, '%m') as mes_tal from autonomo where tipo_contratacao = '1' and data_entrada < '$data_ini_f' and data_saida <= '$data_fim_f' and data_saida > '$data_ini_f' and status = '0'  and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_2 = mysql_num_rows($result2);

// BOLSISTA QUE ENTROU DEPOIS DA DATA INICIAL
$result3 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada2, date_format(data_saida, '%d/%m/%Y')as data_saida2 from autonomo where tipo_contratacao = '1' and data_entrada >= '$data_ini_f' and data_entrada < '$data_fim_f' and data_saida = '0000-00-00' and status = '1'  and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_3 = mysql_num_rows($result3);

// BOLSISTA QUE ENTROU DEPOIS DA DATA INICIAL E SAIU ANTES DE FECHAR A FOLHA
$result4 = mysql_query("Select *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida from autonomo where tipo_contratacao = '1' and data_entrada >= '$data_ini_f' and data_saida <= '$data_fim_f' and data_saida > '$data_ini_f' and status = '0' and id_projeto = '$id_projeto' ORDER BY nome");
$contagem_re_4 = mysql_num_rows($result4);

$result_folhas = mysql_query("SELECT * FROM folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '1'");
$row_folhas = mysql_fetch_array($result_folhas);

//CRIANDO UMA NOVA TABELA PARA GUARDAR AS INFORMAÇÕES GERADAS

/*
RESULT1 = ENTROU ANTES E NÃO SAIU
RESULT2 = ENTROU ANTES E SAIU NO MEIO DO MES QUE ESTÁ GERANDO A FOLHA
RESULT3 = ENTROU DEPOIS DA DATA INICIAL DA FOLHA
RESULT4 = ENTROU DEPOIS DA DATA INICIAL DA FOLHA E SAIU ANTES DE FECHAR O MES
*/

// ------------------------------   RESULT 1   ---------------------------------------------
while ($row1 = mysql_fetch_array($result1)){

$result_curso1 = mysql_query("Select * from curso where id_curso = '$row1[id_curso]'");
$row_curso1 = mysql_fetch_array($result_curso1);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row1[banco]','$id_projeto','$data_pro','$data_pg_f','$row1[0]','$row1[nome]','$row1[agencia]','$row1[conta]','$row1[tipo_pagamento]','1','1','1');") or die("Erro no Insert 1");
}

}
// ------------------------------   RESULT 1   ---------------------------------------------

// ------------------------------   RESULT 2   ---------------------------------------------
while ($row2 = mysql_fetch_array($result2)){

$result_curso2 = mysql_query("Select * from curso where id_curso = '$row2[id_curso]'");
$row_curso2 = mysql_fetch_array($result_curso2);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row2[banco]','$id_projeto','$data_pro','$data_pg_f','$row2[0]','$row2[nome]','$row2[agencia]','$row2[conta]','$row2[tipo_pagamento]','1','2','1');") or die("Erro no Insert 2");
}

}
// ------------------------------   RESULT 2   ---------------------------------------------

// ------------------------------   RESULT 3   ---------------------------------------------
while ($row3 = mysql_fetch_array($result3)){

$result_curso3 = mysql_query("Select * from curso where id_curso = '$row3[id_curso]'");
$row_curso3 = mysql_fetch_array($result_curso3);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row3[banco]','$id_projeto','$data_pro','$data_pg_f','$row3[0]','$row3[nome]','$row3[agencia]','$row3[conta]','$row3[tipo_pagamento]','1','3','1');") or die("Erro no Insert 3");
}

}
// ------------------------------   RESULT 3   ---------------------------------------------

// ------------------------------   RESULT 4   ---------------------------------------------
while ($row4 = mysql_fetch_array($result4)){

$result_curso4 = mysql_query("Select * from curso where id_curso = '$row4[id_curso]'");
$row_curso4 = mysql_fetch_array($result_curso4);

if($row_folhas['ini'] == "1" and $row_folhas['fim'] == "0"){
mysql_query("INSERT INTO folha_$id_projeto(id_folhas,mes,banco,projeto,data_pro,data_pg,id_bolsista,nome,agencia,conta,tipo_pg,sit,result,status) VALUES ('$row_folhas[0]','$mes','$row4[banco]','$id_projeto','$data_pro','$data_pg_f','$row4[0]','$row4[nome]','$row4[agencia]','$row4[conta]','$row4[tipo_pagamento]','1','4','1');") or die("Erro no Insert 4");
}

}
// ------------------------------   RESULT 4   ---------------------------------------------

if($row_folhas['fim'] == "0"){
mysql_query("UPDATE folhas SET fim = '1' where mes = '$mes' and projeto = '$id_projeto'");
} else {
}

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>
<body>
<br><center>
<b><font color=#000000>Participantes<br>
Folha referente ao mes: $mes  <br>
Folha do dia $data_ini até o dia $data_fim<br>
Data de Processamento da folha - $data_pro2 <br><br>
<hr>

<a href='ver_tudo.php?id=13&id_projeto=$id_projeto&regiao=$regiao&data_ini=$data_ini&data_fim=$data_fim&qnt_dias=$qnt_dias&mes=$mes' style='TEXT-DECORATION: none;'>
<img src='imagens/continuar_ver_folha.gif' border='0' align='absmiddle'>
<font color=#FFFFFF size=3>VISUALIZAR A FOLHA</a>
</font></b><br><hr><br>

PARTICIPANTES: ENTROU ANTES E NÃO SAIU $contagem_re_1<br>
PARTICIPANTES: ENTROU ANTES E SAIU NO MEIO DO MES QUE ESTÁ GERANDO A FOLHA $contagem_re_2<br>
PARTICIPANTES: ENTROU DEPOIS DA DATA INICIAL DA FOLHA $contagem_re_3<br>
PARTICIPANTES: ENTROU DEPOIS DA DATA INICIAL DA FOLHA E SAIU ANTES DE FECHAR O MES $contagem_re_4<br>

</center>
</body>
</html>";

} else {

print "<script> alert(\"Ja existe uma folha para o mes selecionado\"); </script>";
print "<br><BR><center><h1><font color=#FFFFFF>Volte e faça novamente!</font></h1></center>";
}


break;

case 13:				//MOSTRANDO A FOLHA COM O CALCULO


//FORMATANDO DATA
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

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];
$qnt_dias = $_REQUEST['qnt_dias'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];
$mes = $_REQUEST['mes'];

$data_ini_f = ConverteData($data_ini);
$data_fim_f = ConverteData($data_fim);

$resultf = mysql_query("SELECT * FROM folha_$id_projeto where mes = '$mes' and projeto = '$id_projeto'");
$rowf = mysql_fetch_array($resultf);

$result_folhas = mysql_query("SELECT *, date_format(data_pro, '%d/%m/%Y')as data_pro FROM folhas where mes = '$mes' and projeto = '$id_projeto' and status = '1' and tipo_folha = '1'");
$folhas = mysql_fetch_array($result_folhas);

$result1 = mysql_query("SELECT *, date_format(data_pro, '%d/%m/%Y')as data_pro2 FROM folha_$id_projeto where mes = '$mes' order by nome ASC");

print "<br><center>
<b>
<table width='80%' border='0' cellpadding='0' cellspacing='0' background='layout/tab_folha_fundo.gif'>
  <tr>
    <td width='4%'><img src='layout/tab_folha_esquerda.gif' width='26' height='147' /></td>
    <td width='26%' valign='top'>
	<font color=#FFFFFF size=3><b>
	<br />
      Folha Referente ao Mês:<br />
      Data Processamento:<br />
      <br />
      Data Inicio:<br />
    Data Fim:</td>
	</b></font>
    <td width='22%' valign='top'>
	<font color=#FFFFFF size=3><b>
	<br />
      $mes<br />
      $folhas[data_pro]<br />
    <br />
    $data_ini<br />
    $data_fim</td>
    </b></font>
	<td width='44%' align='center' valign='middle'>
	
	<a href='acao_folha.php?id=1&id_projeto=$id_projeto&mes=$mes&regiao=$regiao&id_folha=$folhas[0]&tipo=2'
	 style='TEXT-DECORATION: none;'>
	<font color=#FFFFFF size=3><b>
	<img src='imagens/desgerar_folha.gif' border='0' align='absmiddle'>
	DESPROCESSAR FOLHA</b></font>
	</a>
	
	<br><br>
	
	<a href='javascript:window.location.reload()' style='TEXT-DECORATION: none;'>
	<font color=#FFFFFF size=3><b>
	<img src='imagens/atualizar_pg.gif' border='0' align='absmiddle'>
	ATUALIZAR FOLHA	</b></font>
	</a>
	</td>
	</b></font>
    <td width='4%' align='right'>
	<img src='layout/tab_folha_direita.gif' width='26' height='147' /></td>
  </tr>
</table>
<br>
<a href='cadastro2.php?id_cadastro=20&zokpower=321&id_projeto=$id_projeto&mes=$mes&sit_1=0&sit_2=1&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao' target='_blank' style='TEXT-DECORATION: none;'>
<img src='imagens/remover_bolsista.gif' border='0' align='absmiddle'>
<font color=#FFFFFF>
DESATIVAR TODOS DA FOLHA
</font>
</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='cadastro2.php?id_cadastro=20&zokpower=321&id_projeto=$id_projeto&mes=$mes&sit_1=1&sit_2=0&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao' target='_blank' style='TEXT-DECORATION: none;'>
<img src='imagens/adicionar_bolsista.gif' border='0' align='absmiddle'>
<font color=#FFFFFF>
ATIVAR TODOS DA FOLHA
</font>
</a>
<br>
<hr>
</font></b></center>


<table bgcolor=#FFFFFF align='center' width='97%'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center width='3%'> </td>
<td align=center width='5%'>Cód.</td>
<td align=center width='25%'>Nome</td>
<td align=center width='10%'>Salário Bruto</td>
<td align=center width='4%'>Faltas</td>
<td align=center width='4%'>Dias Trab</td>
<td align=center width='7%'>Adicional</td>
<td align=center width='7%'>Descontos</td>
<td align=center width='7%'>13º </td>
<td align=center width='7%'>Valor Diária</td>
<td align=center           >Adiantamento</td>
<td align=center width='10%'>Salário Liquido</td>
<td align=center width='10%'>Ação</td>
</tr>";

$valor_total = "0";
$linha = "";
$cont_color = "0";

while ($row1 = mysql_fetch_array($result1)){

if($cont_color % 2){ $color=""; }else{ $color="#ECF2EC"; }

$result2 = mysql_query("Select * from autonomo where id_autonomo = '$row1[7]' ");
$row2 = mysql_fetch_array($result2);

$result_curso1 = mysql_query("Select * from curso where id_curso = '$row2[id_curso]'");
$row_curso1 = mysql_fetch_array($result_curso1);

$result_con_ad = mysql_query("Select id_folha from folhas where mes = '$mes' and projeto = '$id_projeto' and tipo_folha = '2' ");
$cont_adianta = mysql_num_rows($result_con_ad);

if($row1['sit'] == "0"){
  $imagem = "deletado";
  $mensagem = "Ativar";
 }else if($row1['sit'] == "1" and $row1['status'] == "2"){
  $imagem = "pago";
  $mensagem = "PAGO";
 }else if($row1['sit'] == "1"){
  $imagem = "ok";
  $mensagem = "Desativar";
}

if($row1['status'] == "2"){
  $imagem_pg = "pago";
  $link_pg = "<font color=#000000>$row1[nome]</font>";
 }else{
  $imagem_pg = "pago_n";
//href='ver_tudo.php?id=7&regiao=$regiao_usuario' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" class='link2'target='_blak'
  $link_pg = "<a href='ver_tudo.php?id=14&id_projeto=$id_projeto&mes=$mes&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&id_bolsista=$row1[7]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\"><font color=#000000>$row1[nome]</font></a>";
}


$result_teste = $row1['result'];

if($cont_adianta == "1"){                // PEGANDO O ADIANTAMENTO SE HOUVER-------------------------------

$result_adianta = mysql_query("Select * from folhaad_$id_projeto where id_bolsista = $row1[id_bolsista]");
$row_adianta = mysql_fetch_array($result_adianta);

$adianta = $row_adianta['salario'];

}else{
$adianta = "0";
}

switch ($result_teste){

case 1:

$diaria = "$row_curso1[valor]" / "30";

$dias_trabalhados = "$qnt_dias" - "$row1[faltas]";
$diaria_f = number_format($diaria,2,",",".");

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

$valor = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";


break;
case 2:

$diaria = "$row_curso1[valor]" / "30";

$result_dias = mysql_query("SELECT COUNT(*) FROM ano where data > '$data_ini_f' and data <= '$row2[data_saida]' ");
$row_dias = mysql_fetch_array($result_dias);

$dias_trabalhados = $row_dias['0'] - $row1['faltas'];

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$valor1 = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";
$valor = $valor1;

$dias_trab = "$qnt_dias" - "$row1[faltas]";
$diaria_f = number_format($diaria,2,",",".");
$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

break;

case 3:

$diaria = "$row_curso1[valor]" / "30";

$result_dias_t = mysql_query("SELECT data FROM ano where data >= '$row2[data_entrada]' and data <= '$data_fim_f'");
$row_dias = mysql_num_rows($result_dias_t);

$dias_trabalhados = $row_dias - $row1['faltas'];

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$diaria_f = number_format($diaria,2,",",".");
$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

$valor1 = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";
$valor = $valor1;

break;

case 4:

$diaria = "$row_curso1[valor]" / "30";

$result_dias_t = mysql_query("SELECT COUNT(*) FROM ano where data >= '$row2[data_entrada]' and data <= '$row2[data_saida]'");
$row_dias = mysql_fetch_array($result_dias_t);

$dias_trabalhados = $row_dias['0'] - $row1['faltas'];

$adicional_c = str_replace(",", ".", $row1['adicional']);
$desconto_c = str_replace(",", ".", $row1['desconto']);

$diaria_f = number_format($diaria,2,",",".");
$adicional = number_format($adicional_c,2,",",".");
$desconto = number_format($desconto_c,2,",",".");

$valor1 = "$dias_trabalhados" * "$diaria" + "$adicional_c" - "$desconto_c";
$valor = $valor1;

break;


}

$valor13 = $row1['valor_13'];

if($row1['sit'] == "0"){
$valor = "0";

 }else{

}

$valor = $valor - $adianta;

$adianta = number_format($adianta,2,",",".");
$valor_for2 = number_format($valor,2,",","");
$valor_for = number_format($valor,2,",",".");
$valor_13 = number_format($valor13,2,",",".");
$valor_curso = number_format($row_curso1['salario'],2,",",".");

$valor_total = $valor_total + $valor;
$valor_total_f = number_format($valor_total,2,",",".");
print "<tr class='linha'>

<td bgcolor=$color><img src='imagens/$imagem.gif'></td>
<td bgcolor=$color><font color=#000000>$row2[campo3]</font></td>
<td bgcolor=$color>$link_pg</td>
<td bgcolor=$color><font color=#000000>R$ $valor_curso</font></td>
<td bgcolor=$color><font color=#000000>$row1[faltas]</font></td>
<td bgcolor=$color><font color=#000000>$dias_trabalhados</font></td>
<td bgcolor=$color><font color=#000000>R$ $adicional</font></td>
<td bgcolor=$color><font color=#000000>R$ $desconto</font></td>
<td bgcolor=$color><font color=#000000>R$ $valor_13</font></td>
<td bgcolor=$color><font color=#000000>R$ $diaria_f</font></td>
<td bgcolor=$color><font color=#000000>R$ $adianta</font></td>
<td bgcolor=$color><font color=#000000>R$ $valor_for</font></td>
<td bgcolor='$color' align='center'><a href='cadastro2.php?id_cadastro=20&zokpower=323&id_projeto=$id_projeto&mes=$mes&sit_1=$row1[sit]&qnt_dias=$qnt_dias&data_ini=$data_ini&data_fim=$data_fim&regiao=$regiao&id_bolsista=$row1[7]' target='_blak' class=link2>$mensagem</a></td>
</tr>";
$cont_color ++;

mysql_query("UPDATE folha_$id_projeto SET salario = '$valor_for2' where mes = '$mes' and projeto = '$id_projeto' and id_bolsista = '$row2[0]'");

}

print "</table><br><br>
<center><font color=#FFFFFF>Valor total da folha: R$ $valor_total_f</font><br><br>";

$tipo_pg_5 = mysql_query("SELECT * FROM tipopg  where id_projeto = '$id_projeto' and campo1 = '2'");
$row_tipo_pg_5 = mysql_fetch_array($tipo_pg_5);

$result_num_2 = mysql_query("SELECT COUNT(*) FROM folha_$id_projeto where sit = '1' and projeto = '$id_projeto' and mes = '$mes' and tipo_pg ='$row_tipo_pg_5[0]'"); 
$num_cheque = mysql_fetch_array($result_num_2);

print "<table border='0' cellspacing='0' cellpadding='0' class='tarefa' width=60%>
<tr bgcolor=#999999 height=26>
<td align=center background='layout/fundo_tab_cinza.gif' ><b>Nome do Banco</td>
<td align=center background='layout/fundo_tab_cinza.gif' ><b>Integrantes</td>
<td align=center background='layout/fundo_tab_cinza.gif' ><b> </td>
</tr>";

$result_banco = mysql_query("SELECT * FROM bancos where id_projeto = $id_projeto");
$cont3 = "0";
while($row_banco = mysql_fetch_array($result_banco)){

$result_cont_banco = mysql_query("SELECT COUNT(*) FROM folha_$id_projeto where projeto = '$id_projeto' and banco = '$row_banco[0]' and mes = '$mes' and sit = '1'"); 

$row_cont_banco = mysql_fetch_array($result_cont_banco);

if($cont3 % 2){ $color3="#f0f0f0"; }else{ $color3="#dddddd"; }

print "<tr bgcolor=$color3>
<td class=border2>$row_banco[nome]</td>
<td class=border2> $row_cont_banco[0] Participantes </td>
<td class=border3><a href='folha_pg.php?id=data&tipo_pg=$row_banco[0]&banco=$row_banco[0]&koeiurjdpll=banco&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto' target='_blank'>Pagar</a></td></tr>";

$cont3 ++;
}
print "
<tr bgcolor=#FFFEEF>
<td class=border2>Participantes que recebem em cheque</td>
<td class=border2>$num_cheque[0]</td>
<td class=border3><a href='folha_pg.php?id=2&tipo_pg=$row_pg[0]&koeiurjdpll=cheque&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto' target='_blank'>Pagar</a></td></tr>";

$result_cont_outro = mysql_query("SELECT COUNT(*) FROM folha_$id_projeto where projeto = '$id_projeto' and banco = '9999' and mes = '$mes' and sit = '1'");
$row_cont_outro = mysql_fetch_array($result_cont_outro);
print "
<tr bgcolor=#FFFEEE>
<td class=border2>Outros tipos de PG </td>
<td class=border2>$row_cont_outro[0] </td>
<td class=border3><a href='folha_pg.php?id=1&tipo_pg=0&banco=0&koeiurjdpll=banco&id_folhas=$folhas[0]&mes=$mes&id_projeto=$id_projeto' target='_blank'>Pagar</a></td></tr>";


print "</center></body></html>";
break;

case 14:                       //TELA PARA CADASTRAR AS FALTAS

$mes = $_REQUEST['mes'];
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['id_projeto'];
$id_bolsista = $_REQUEST['id_bolsista'];
$qnt_dias = $_REQUEST['qnt_dias'];
$data_ini = $_REQUEST['data_ini'];
$data_fim = $_REQUEST['data_fim'];

$result_bol = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_bolsista'");
$row_bol = mysql_fetch_array($result_bol);

$result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_pro = mysql_fetch_array($result_pro);

$result_fol = mysql_query("SELECT * FROM folha_$projeto WHERE id_bolsista = '$id_bolsista' and mes = '$mes'");
$row_fol = mysql_fetch_array($result_fol);

$ver_terceiro = $row_fol['terceiro'];

if($ver_terceiro == "0"){
$mensagem = "";
}else{
$mensagem = "Este funcionário ja está recebendo o seu 13º desde o mes: $row_fol[ini_13]";
}

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>

<form action='cadastro2.php' method='post' name='form1' onSubmit=\"return validaForm()\">
<table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='linha' align='center'>
<tr><td colspan='2' bgcolor='#5C7E59'><div align='left' class='style1'> <img src='imagens/verbolsistas.gif'> <br> <br></div><BR></td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>
<tr><td width='136'>&nbsp;</td><td width='318'>&nbsp;</td></tr>

<tr><td align='center' colspan='2'>Projeto</td></tr>
<tr><td align='center' colspan='2'>$row_bol[nome]</td></tr>
<tr><td colspan='2' align='center'>&nbsp;</td></tr>
<tr><td align='right'>&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
<tr>
<td align='center' colspan='2'>
<br> Faltas:&nbsp;&nbsp; 

<input name='faltas' type='text' class='campotexto' id='faltas' size='5' value='$row_fol[faltas]'>
Adicional:&nbsp;&nbsp; 

<input name='adicional' type='text' class='campotexto' id='adicional' size='10' value='$row_fol[adicional]'>&nbsp;&nbsp;
Desconto:&nbsp;&nbsp; 

<input name='desconto' type='text' class='campotexto' id='desconto' size='10' value='$row_fol[desconto]'><br><br>
<font color=red>$mensagem</font><br><br>
Pagar 13º: <input type='checkbox' name='terceiro' value='1'>&nbsp;&nbsp;
Número de Parcelas:&nbsp;&nbsp; <select name='parcelas' class='campotexto' id='parcelas'>
<option value='1'>1</option>
<option value='2'>2</option>
</select>
<br>
Selecionar mês de ínicio do pagamento: &nbsp;&nbsp;<select name='mes_pagamento' class='campotexto' id='mes_pagamento'>
<option value='01'>Janeiro</option>
<option value='02'>Fevereiro</option>
<option value='03'>Março</option>
<option value='04'>Abril</option>
<option value='05'>Maio</option>
<option value='06'>Junho</option>
<option value='07'>Julho</option>
<option value='08'>Agosto</option>
<option value='09'>Setembro</option>
<option value='10'>Outubro</option>
<option value='11'>Novembro</option>
<option value='12'>Dezembro</option>
</select><br>
</td>
</tr>
<tr>
<td align='center' colspan=2><input type='submit' name='Submit' value='Enviar' class='campotexto'>

<input type='hidden' name='id_cadastro' value='18'>
<input type='hidden' name='id_bolsista' value='$id_bolsista'>
<input type='hidden' name='projeto' value='$projeto'>
<input type='hidden' name='mes' value='$mes'>
<input type='hidden' name='id_regiao' value='$regiao'>
<input type='hidden' name='data_ini' value='$data_ini'>
<input type='hidden' name='data_fim' value='$data_fim'>
<input type='hidden' name='qnt_dias' value='$qnt_dias'>

</form>

</td>
</tr>
<tr>
<td align='center' colspan=2> &nbsp;</td>
</tr>
</table>";
break;

case 15:                         //RELATÓRIOS DE GESTÃO

$regiao = $_REQUEST['id_reg'];
$user =  $_REQUEST['id_user'];

if(empty($_REQUEST['projeto'])){
$projeto = '';
}else{
$projeto = $_REQUEST['projeto'];
}

$resutl_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' ");

$resutl_unidades = mysql_query("SELECT * FROM unidade where campo1 = '$projeto' ");

print "
<script type=\"text/JavaScript\">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+\".location='\"+selObj.options[selObj.selectedIndex].value+\"'\");
  if (restore) selObj.selectedIndex=0;
}
</script>
<form action='relatorio2.php' method='post' name='form55' id='form55'>
<center><FONT color=#FFFFFF><b>
<br>RELATÓRIO DE PARTICIPANTES DO PROJETO<br>
<br>Selecione o Projeto:<br><br>
<select name='projeto' id='projeto' class='campotexto' onchange=\"MM_jumpMenu('parent',this,0)\">
<option value='0'>-- Selecione o Projeto --</option>";

while($row_projeto = mysql_fetch_array($resutl_projeto)){
print "<option value='ver_tudo.php?id=15&id_reg=$regiao&id_user=$user&projeto=$row_projeto[0]'>$row_projeto[nome]</option>";
}

print "</select><br><br><br>
<select name='unidade' id='unidade' class='campotexto'>";

while($row_unidades = mysql_fetch_array($resutl_unidades)){
print "<option value='$row_unidades[0]'>$row_unidades[unidade]</option>";
}
print "</select><br>
<br>
Digite o ano de referencia: <input name='ano_2' type='text' class='campotexto' id='ano_2' size='5'>
<input type='hidden' name='tela' value='1'>
<input type='hidden' name='regiao' value='$regiao'>
<input type='hidden' name='projeto2' value='$projeto'>
<br>
<input type=submit value=GERAR>
</form>
<br>
<hr>
<br>
<form action='relatorio3.php' method='post' name='form55' id='form55'>
<br>RELATÓRIO TOTALIZADOR DO PROJETO<br>
<center><FONT color=#FFFFFF><b><br>Selecione o Projeto:<br><br>
<select name='projeto' id='projeto' class='campotexto'>";

$resutl_projeto2 = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' ");
while($row_projeto2 = mysql_fetch_array($resutl_projeto2)){
print "<option value=$row_projeto2[0]>$row_projeto2[nome]</option>";
}
print "</select><br><br>

Digite o ano de referencia: <input name='ano_2' type='text' class='campotexto' id='ano_2' size='5'>
<input type='hidden' name='tela' id='tela' value='1'>
<input type='hidden' name='regiao' value='$regiao'>
<br>
<input type=submit value=GERAR>
</form>

<hr>
<form action='relatorio4.php' method='post' name='form5' id='form5'>
<br>RELATÓRIO DE CAPACITAÇÃO<br>
<center><FONT color=#FFFFFF><b><br>Selecione o Projeto:<br><br>
<select name='projeto' id='projeto' class='campotexto'>";

$resutl_projeto2 = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' ");
while($row_projeto2 = mysql_fetch_array($resutl_projeto2)){
print "<option value=$row_projeto2[0]>$row_projeto2[nome]</option>";
}
print "</select><br><br>

<input type='hidden' name='id' value='1'>
<input type='hidden' name='regiao' value='$regiao'>
<br>
<input type=submit value=GERAR>
</form>

</FONT></b>";


break;

case 16:                       // VENDO AS SAÍDAS DO FINANCEIRO
$regiao = $_REQUEST['regiao'];
$id_saida = $_REQUEST['saida'];
$enrtadasaida = $_REQUEST['entradasaida'];

if($enrtadasaida == "1"){
$result = mysql_query("SELECT *, date_format(data_vencimento, '%d/%m/%Y')as data_vencimento, date_format(data_pg, '%d/%m/%Y')as data_pg FROM saida WHERE id_saida = '$id_saida'");
$row = mysql_fetch_array($result);
}else{
$result = mysql_query("SELECT *, date_format(data_vencimento, '%d/%m/%Y')as data_vencimento, date_format(data_pg, '%d/%m/%Y')as data_pg FROM entrada WHERE id_entrada = '$id_saida'");
$row = mysql_fetch_array($result);
}
$result_tipo = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '$row[tipo]'");
$row_tipo = mysql_fetch_array($result_tipo);

$nome_1 = str_split($row['nome'], 15);

    $nome_arq = str_replace(" ", "_", $nome_1[0]);
	$nome_arq = str_replace("/", "", $nome_arq);	
	$nome_arq = str_replace("*", "", $nome_arq);	
	$nome_arq = str_replace("-", "", $nome_arq);	
	$nome_arq = str_replace(".", "", $nome_arq);	
	$nome_arq = str_replace("&", "", $nome_arq);	
	$nome_arq = str_replace("!", "", $nome_arq);	
	$nome_arq = str_replace("?", "", $nome_arq);	
	$nome_arq = str_replace("ç", "c", $nome_arq);	
	$nome_arq = str_replace("á", "a", $nome_arq);
	$nome_arq = str_replace("é", "e", $nome_arq);
	$nome_arq = str_replace("í", "i", $nome_arq);
	$nome_arq = str_replace("ó", "o", $nome_arq);
	$nome_arq = str_replace("ú", "u", $nome_arq);
	$nome_arq = str_replace("ã", "a", $nome_arq);
	$nome_arq = str_replace("õ", "o", $nome_arq);
	$nome_arq = str_replace("Ç", "c", $nome_arq);	
	$nome_arq = str_replace("Á", "a", $nome_arq);
	$nome_arq = str_replace("É", "e", $nome_arq);
	$nome_arq = str_replace("Í", "i", $nome_arq);
	$nome_arq = str_replace("Ó", "o", $nome_arq);
	$nome_arq = str_replace("Ú", "u", $nome_arq);
	$nome_arq = str_replace("Ã", "a", $nome_arq);
	$nome_arq = str_replace("Õ", "o", $nome_arq);
	$nome_arq = str_replace("Ñ", "n", $nome_arq);
	$nome_arq = str_replace("ñ", "n", $nome_arq);
	$nome_arq = str_replace("~", "", $nome_arq);

if($row['comprovante'] == "1"){
$tipo_arq = "$row[tipo_arquivo]";

$img = "<b>Clique para ver o anexo</b><br>
<a href='comprovantes/$row[0]$tipo_arq' rel='lightbox' title='Anexo'>
<img src='imagens/ver_anexo.gif' border=0 ></a>";
}else{
$img = "";
}


print "<table width='750' border='0' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF'>
  <tr>
    <td height='38' colspan='4'><img src='layout/topo.gif' alt='2' width='750' height='38' /></td>
  </tr>
  <tr>
    <td width='17' rowspan='8' background='layout/esquerdo.gif'>&nbsp;</td>
    <td colspan='2'><div align='center' class='style2'>VISUALIZAÇÃO DE COMPROVANTE
	<br><br>$row[nome]</div></td>
    <td width='21' rowspan='8' background='layout/direito.gif'>&nbsp;</td>
  </tr>
  <tr>
    <td colspan='2' align='right' valign='top'>&nbsp;</td>
  </tr>
  
  <tr>
    <td width='370' height='19' align='right' valign='top' bgcolor='#666666'><div align='center' class='style11'>Valor pago:  R$ $row[valor]</div></td>
    <td align='center' valign='middle' bgcolor='#666666'><div align='center' class='style11'>Data de Pagamento: $row[data_pg]</div></td>
  </tr>
  <tr>
    <td height='19' align='right' valign='top' bgcolor='#666666'><div align='center'><span class='style12'></span><span class='style11'>Tipo de pagamento: $row_tipo[nome]</span></div></td>
    <td align='center' valign='middle' bgcolor='#666666'><div align='center' class='style11'>Data do Vencimento: $row[data_vencimento]</div></td>
  </tr>
  <tr>
    <td height='19' colspan='2' align='right' valign='top'><div align='center' class='style11'>Observações
    </div></td>
  </tr>
  <tr>
    <td colspan='2'><center>$row[especifica]</center></td>
  </tr>
    <tr>
    <td colspan=2 align=center>  <hr>  </td>
  </tr>
  </tr>
    <tr>
    <td colspan=2 align=center> $img
	<br><br><a href='javascript:window.close()'>Fechar</a>
</td>
  </tr>

  <tr valign='top'>
    <td height='37' colspan='4' bgcolor='#5C7E59'><img src='layout/baixo.gif' alt='1' width='750' height='38' />
        <div align='center' class='style6'><span class='style1'><strong>Intranet do Instituto Sorrindo Para a Vida</strong> - Acesso Restrito 
          a Funcion&aacute;rios </span><br />
      </div></td>
  </tr>
</table>";


break;

case 17:                       // PAGANDO OU DELETANDO AS ENTRADAS E SAÍDAS DO FINANCEIRO

$regiao = $_REQUEST['regiao'];
$tipo = $_REQUEST['tipo'];
$id_pro = $_REQUEST['pro'];
$tabela = $_REQUEST['tabela'];
$id_user = $_COOKIE['logado'];
$idtarefa = $_REQUEST['idtarefa'];


$data_hoje = date("Y-m-d");

if($tipo == "pagar"){
//AQUI ELE VAI RODAR O PAGAMENTO DA SAÍDA

$result = mysql_query("SELECT * FROM $tabela where id_$tabela = '$id_pro'");
$row = mysql_fetch_array($result);

$result_bancos = mysql_query("SELECT * FROM bancos where id_banco = '$row[id_banco]'");
$row_bancos = mysql_fetch_array($result_bancos);

$valor = "$row[valor]";
$adicional = "$row[adicional]";
$valor_banco = "$row_bancos[saldo]";

$valor = str_replace(",", ".", $valor);
$adicional = str_replace(",", ".", $adicional);
$valor_banco = str_replace(",", ".", $valor_banco);

$valor_final = $valor + $adicional;

if($idtarefa == "1"){
$saldo_banco_final = $valor_banco - $valor_final;
}else{
$saldo_banco_final = $valor_banco + $valor_final;
}

$valor_f = number_format($valor_final,2,",",".");
$saldo_banco_final_f = number_format($saldo_banco_final,2,",",".");
$saldo_banco_final_banco = number_format($saldo_banco_final,2,",","");


if($row['status'] == "1"){
mysql_query("UPDATE $tabela set status = '2', data_pg = '$data_hoje', id_userpg = '$id_user' where id_$tabela = '$id_pro'");
mysql_query("UPDATE bancos set saldo = '$saldo_banco_final_banco' where id_banco = '$row[id_banco]'");

  if($row['tipo'] == "66"){
  mysql_query("UPDATE compra SET acompanhamento = '6' where id_compra = '$row[id_compra]'");
  }

}else{
echo "Desculpe, mas esta conta ja foi paga ou deletada!";
}


echo "<br><center>
<br>Valor da Conta: R$ $valor
<br>Adicional: R$ $adicional
<br>Total a pagar: R$ $valor_f
<br>Valor no Banco: R$ $valor_banco
<br>Saldo atualizado do Banco: R$ $saldo_banco_final_f
<br><br><a href='financeiro/novofinanceiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a></center>
";

}else{
//AQUI ELE VAI DELETAR A SAÍDA
mysql_query("UPDATE $tabela set status = '0' where id_$tabela = '$id_pro'");
print "<br><br><center>Registro deletado com sucesso!<br><br>
<a href='financeiro/novofinanceiro.php?regiao=$regiao'><img src='imagens/voltar.gif' border=0></a></center>";
}
break;

case 18:                       // GRAVANDO ASSINATURA DE CONTRATO

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$bolsista = $_REQUEST['bolsista'];
$ass = $_REQUEST['ass'];
$tipo = $_REQUEST['tipo'];
$tab = $_REQUEST['tab'];

if($tab == "rh_clt"){
	$tabela = "rh_clt";
	$campo_tabela = "id_clt";
}else{
	$tabela = "autonomo";
	$campo_tabela = "id_autonomo";
}

if(empty($_REQUEST['pag'])){
	$link = "bolsista.php?projeto=$projeto&regiao=$regiao";
}else{
	$link = "rh/clt.php?regiao=$regiao";
}

if($tipo == "1"){
   mysql_query("UPDATE $tabela set assinatura = '$ass' where $campo_tabela = '$bolsista'");

}else if($tipo == "2"){
   mysql_query("UPDATE $tabela set distrato = '$ass' where $campo_tabela = '$bolsista'");

}else if($tipo == "3"){
   mysql_query("UPDATE $tabela set outros = '$ass' where $campo_tabela = '$bolsista'");
   
}
 
   
print "<script>
alert(\"Informações cadastradas com sucesso!\");
location.href=\"$link\"
</script>";


break;

case 19:         // MASTER VISUALIZAR USUÁRIOS ------------

$id_user = $_COOKIE['logado'];

$result_user = mysql_query("Select * from funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$result = mysql_query("Select * from funcionario where status_reg = '1' ORDER BY nome");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>CONTROLE AVANÇADO DE FUNCIONÁRIOS</font></b></center>
<table bgcolor=#FFFFFF align='center' width='90%'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center>Nome</td>
<td align=center>Nome no Sistema</td>
<td align=center colspan='3'>Ações</td>
</tr>";
$cont = "1";
while ($row = mysql_fetch_array($result)){

 if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }

if($row_user['tipo_usuario'] == "1"){
$link = "<a href=cadastro.php?id=12&user=$row[0]&master=1&pag=1>$row[nome]</a>";
}else{
$link = "$row[nome]";
}

print "<tr class='linha' bgcolor=$color>
<td>$link</td>
<td>$row[nome1]</td>
<td align='center'><a href=cadastro2.php?id_cadastro=25&funcionario=$row[0]><img src='imagens/mudar_senha.gif' border=0 alt='Alterar a Senha'></a></td>
<td align='center'><a href=ver_tudo.php?id=20&funcionario=$row[0]><img src='imagens/ver_log.gif' border=0 alt='Ver LOG'></a></td>
<td align='center'><a href=cadastro2.php?id_cadastro=25&funcionario=$row[0]&excluir=1><img src='imagens/deletar_usuario.gif' border=0 alt='Desativar Usuário'></a></td>
</tr>";
//print "<tr class='linha' bgcolor=$color><td>$row[nome]</td><td>$row[funcao]</td><td>$row[nome1]</td></tr>";

$cont ++;
}  

$row_con = mysql_num_rows($result);
print "</table><br><br>

<table align='center' width='100%'>
<tr><td height='25' background='imagens/bot_laranja_meio.gif' align='center'>
<font color='#000000' face='Arial'><b>$row_con funcionários ativos</b></font>
</td></tr>
</table>";


break;

case 20:         // VISUALIZANDO OS LOGS ------------

$id_user = $_COOKIE['logado'];
$funcionario = $_REQUEST['funcionario'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$funcionario'");
$row_user = mysql_fetch_array($result_user);

// Preparando Paginacao
$nav = "%s?id=20&funcionario=".$row_user['id_funcionario']."&pagina=%d%s";
$max_logs = 100;
$numero_pagina = 0;
if (isset($_GET['pagina'])) {
  $numero_pagina = $_GET['pagina'];
}
$start_log = $numero_pagina * $max_logs;
$qr_prelog = "SELECT *, date_format(horario, '%d/%m/%Y - %H:%i:%s')as data FROM log WHERE id_user = '$funcionario' ORDER BY id_log DESC";
$qr_limit_log = sprintf("%s LIMIT %d, %d", $qr_prelog, $start_log, $max_logs);
$qr_log = mysql_query($qr_limit_log) or die(mysql_error());
$all_logs = mysql_query($qr_prelog);
$total_logs = mysql_num_rows($all_logs);
$total_paginas = ceil($total_logs/$max_logs)-1;
//

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net1.css\" rel=\"stylesheet\" type=\"text/css\"></head>";

print "<body>";
print "<br><center><b><font color=#FFFFFF>Log do Funcionário $row_user[nome1] <a style='font-size:11px;' href='log/".$row_user['id_funcionario'].".txt'>ver arquivo txt</a></font></b></center>
<table bgcolor=#FFFFFF align='center' width='95%' cellspacing='4' cellpading='4'>
<tr class='linha' bgcolor=#CCCCCC>
<td width=5% align=center>Id</td>
<td width=15% align=center>Data e Hora</td>
<td width=10% align=center>Região</td>
<td width=10% align=center>Local</td>
<td width=50% align=center>Ação</td>
<td width=10% align=center>IP</td>
</tr>";
$cont = "1";
while ($log = mysql_fetch_array($qr_log)){

 if($cont % 2){ $color=""; }else{ $color="#ECF2EC"; }

$result_reg = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$log[id_regiao]'");
$row_reg = mysql_fetch_array($result_reg);

print "<tr class='linha' bgcolor=$color>
<td>$log[id_log]</td>
<td>$log[data]</td>
<td>$row_reg[regiao]</td>
<td>$log[local]</td>
<td>$log[acao]</td>
<td>$log[ip]</td>
</tr>";
//print "<tr class='linha' bgcolor=$color><td>$row[nome]</td><td>$row[funcao]</td><td>$row[nome1]</td></tr>";

$cont ++;
}  


print "
<tr><td colspan='6' align='right'>";
// Paginação

if ($numero_pagina > 0) { ?>
<a href="<?php printf($nav, $currentPage, 0, $string); ?>">&laquo; Primeira</a>&nbsp;
<?php }
if ($numero_pagina == 0) { ?>
<span class="morto">&laquo; Primeira</span>&nbsp;
<?php } 
if ($numero_pagina > 0) { ?>
<a href="<?php printf($nav, $currentPage, max(0, $numero_pagina - 1), $string); ?>">&#8249; Anterior</a>&nbsp;
<?php } 
if ($numero_pagina == 0) { ?>
<span class="morto">&#8249; Anterior</span>&nbsp;
<?php }
if ($numero_pagina < $total_paginas) { ?>
<a href="<?php printf($nav, $currentPage, min($total_paginas, $numero_pagina + 1), $string); ?>">Próxima &#8250;</a>&nbsp;
<?php } 
if ($numero_pagina >= $total_paginas) { ?>
<span class="morto">Próxima &#8250;</span>&nbsp;                   
<?php } 
if ($numero_pagina < $total_paginas) { ?>
<a href="<?php printf($nav, $currentPage, $total_paginas, $string); ?>">Última &raquo;</a>
<?php }                    
if ($numero_pagina >= $total_paginas) { ?>
<span class="morto">Última &raquo;</span>
<?php }
// Fim da Paginação
print "</table><br><br>";


print "<br><a href='javascript:history.go(-1)' class='link'><img src='imagens/voltar.gif' border=0></a>";


break;
}
?>
</body>
</html>