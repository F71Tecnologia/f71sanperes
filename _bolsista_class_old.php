<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";
$id = $_REQUEST['id'];

switch ($id) {
case 1:

//if (empty($_REQUEST['projeto'])){       //Esta tela ser� apresentada 1� e listar� todos os projetos
$regiao = $_REQUEST['regiao'];

$result = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'");
$row_cont = mysql_num_rows($result);


//VERIFICANDO SE EXISTE PROJETO CADASTRADO PARA A REGI�O SELECIONADA

if ($row_cont == "0"){                                     //CASO N�O EXISTA PROJETO CADASTRADO PARA A REGI�O SELECIONADA
print "<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>";
print "<center><br><img src='imagens/visualizaprojeto.gif'><br><br><span class='style1'>Nenhum Projeto encontrado para sua regi�o!</span></center>";
print "<br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>";

} else {											//CASO EXISTA PROJETO CADASTRADO PARA A REGI�O SELECIONADA

print "<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\"><body bgcolor='#D7E6D5'>";
print "<br><img src='imagens/visualizaprojeto.gif'><br><br>";

print "<table bgcolor=#FFFFFF width='500' align='center'><tr class='linha' bgcolor=#CCCCCC><td align=center>Projeto</td><td align=center>Tema</td></tr>";
while ($row = mysql_fetch_array($result, $conn)){
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

//AUTONOMOS JA AVALIADOS
$result2 = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo 
WHERE id_psicologia = '1' and tipo_contratacao != '2' and id_projeto = '$id_projeto' ORDER BY nome") or die(mysql_error());

//SELECIONANDO UNIDADES
$result_unidades = mysql_query("Select * from unidade where campo1 = '$id_projeto' ORDER BY unidade");

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\"></head><body bgcolor='#D7E6D5'>
<form action='av_lote.php' method='post'>
<center><font color=#FFFFFF><b>Funcion�rios n�o Avaliados</b></font></center>";

//RODAR ENQUANTO TIVER UNIDADES
while ($row_unidades = mysql_fetch_array($result_unidades)){

$result = mysql_query("SELECT * , date_format(data_entrada, '%d/%m/%Y')as data_entrada,date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo 
WHERE id_psicologia = '0' and locacao = '$row_unidades[unidade]' and id_projeto = '$id_projeto' ORDER BY nome");

   print " <center><font color=#FFFFFF><b>$row_unidades[unidade]</b></font></center>
   <br><table bgcolor=#FFFFFF width='90%' align='center'><tr class='linha' bgcolor=#CCCCCC>
   <td align=center>--</td>
   <td align=center>C�d</td>
   <td align=center>Nome</td>
   <td align=center>Tipo</td>
   <td align=center>Sal�rio</td>
   <td align=center>Status</td> 
   <td align=center>Entrada - Sa�da</td>
   </tr>";
   
   while ($row = mysql_fetch_array($result)){
   
   $result_curso = mysql_query("Select * from curso where id_regiao = $id_regiao and id_curso = $row[id_curso]", $conn);
   $row_curso = mysql_fetch_array($result_curso);
   $curso = $row_curso['0']; 
   
   if ($row['status'] == "0"){
   $status = "<font color=red>Inativo</font>";
   }else{
   $status = "Ativo";
   }
   
   
   print "
   <tr>
   <td align=center><input name='id_parti[]' id='id_parti' type='checkbox' value='$row[id_autonomo]'></td>
   <td><span class='style3'>$row[campo3]</span></td>
   <td><a href=avalicao.php?bol=$row[id_autonomo]&tab=$id_projeto&regiao=$id_regiao class=link>$row[nome]</a></td>
   <td><span class='style3'>tipo</span></td>
   <td><span class='style3'>$valor_curso</span></td>
   <td align=center><span class='style3'>$status</span></td>
   <td align=center><span class='style3'>$row[data_entrada] - $row[data_saida]</span></td>
   </tr>";
}

//	GERANDO CLT
$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt
 WHERE id_projeto = '$id_projeto' and locacao = '$row_unidades[unidade]' and  status != '62' and id_psicologia = '0' ORDER BY nome");


while ($row_clt = mysql_fetch_array($result_clt)){
	
   $result_curso = mysql_query("Select * from curso where id_regiao = $id_regiao and id_curso = $row_clt[id_curso]");
   $row_curso = mysql_fetch_array($result_curso);
   $curso = $row_curso['0']; 
   
   if ($row['status'] == "0"){
   $status = "<font color=red>Inativo</font>";
   }else{
   $status = "Ativo";
   }
   
   /* FORMATANDO OS VALORES PARA MO�DA */
   setlocale(LC_MONETARY, 'pt_BR');
   $valor_curso = money_format('%n', $row_curso['salario']);
print "
   <tr>
   <td align=center><input name='id_parti' id='id_parti' type='checkbox' value='$row_clt[id_clt]'>
   <input type='hidden' name='clt' value='1' >
   <input type='hidden' name='projeto' value='$id_projeto' >
   <input type='hidden' name='regiao' value='$id_regiao' >
   
   </td>
   <td><span class='style3'>$row_clt[campo3]</span></td>
   <td><a href=avalicao.php?bol=$row_clt[0]&tab=$id_projeto&regiao=$id_regiao&tipo=clt class=link>$row_clt[nome]</a></td>
   <td><span class='style3'>tipo</span></td>
   <td><span class='style3'>$valor_curso</span></td>
   <td align=center><span class='style3'>$status</span></td>
   <td align=center><span class='style3'>$row_clt[data_entrada] - $row_clt[data_saida]</span></td>
   </tr>";

}

print "</table><br><br>";
}
   print "<input type='hidden' name='regiao' value='$id_regiao'/><input type='hidden' name='projeto' value='$id_projeto'/>";
   print "<center><input type='submit' value='Avaliar em LOTE'></center></form>";
   
   //-------------------------------------||
   //-INICIANDO OS QUE JA FORAM AVALIADOS-||
   //-------------------------------------||
   
   print "<form action='av_lote.php' method='post'>";
   
   print "<center><hr><font color=#FFFFFF><b>Funcion�rios Avaliados</b></font></center><br>
   <table bgcolor=#FFFFFF width='90%' align='center'><tr class='linha' bgcolor=#CCCCCC>
   <td align=center>--</td>
   <td align=center>C�d</td>
   <td align=center>Nome</td>
   <td align=center>Unidade</td>
   <td align=center>Sal�rio</td>
   <td align=center>Status</td> 
   <td align=center>Entrada - Sa�da</td>
   <td align=center>Avaliar novamente</td>
   </tr>";
   while ($row2 = mysql_fetch_array($result2)){
   
   $result_curso2 = mysql_query("Select * from curso where id_regiao = $id_regiao and id_curso = $row2[id_curso]");
   $row_curso2 = mysql_fetch_array($result_curso2);
   $curso2 = $row_curso2['0']; 
      if ($row2['status'] == "0"){
   $status2 = "<font color=red>Inativo</font>";
   }else{
   $status2 = "Ativo";
   }
   $valor_curso2 = money_format('%n', $row_curso2['valor']);   
   print "<tr>
   <td align=center><input name='id_parti[]' id='id_parti' type='checkbox' value='$row2[0]'></td>
   <td><span class='style3'>$row2[campo3]</span></td>
   <td><a href=avaliacao2.php?bol=$row2[id_autonomo]&tab=$id_projeto&regiao=$id_regiao class=link>$row2[nome]</a>
   <td><span class='style3'>$row2[locacao]</span></td>
   <td><span class='style3'>$valor_curso2</span></td>
   <td align=center><span class='style3'>$status2</span></td>
   <td align=center><span class='style3'>$row2[data_entrada] - $row2[data_saida]</span></td>
   <td align=center><span class='style3'><a href=avalicao.php?bol=$row2[0]&tab=$id_projeto&regiao=$id_regiao class=link>Avaliar</a></span></td>
   </tr>";
}
//	GERANDO CLT AVALIADO
$result_clt2 = mysql_query("Select *, date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida from rh_clt where id_projeto = '$id_projeto' and  status != '62' and id_psicologia = '1' ORDER BY nome");

while ($row_clt2 = mysql_fetch_array($result_clt2)){


 if ($row_clt2['status'] == "62"){
   $statusclt2 = "<font color=red>Inativo</font>";
 }else{
   $statusclt2 = "Ativo";
 }


print "
   <tr>
   <td align=center>--</td>
   <td><span class='style3'>$row_clt2[campo3]</span></td>
   <td><a href=avaliacao2.php?bol=$row_clt2[0]&tab=$id_projeto&regiao=$id_regiao&tipo=clt class=link>$row_clt2[nome]</a></td>
   <td><span class='style3'>$row_clt2[locacao]</span></td>
   <td align=center><span class='style3'>-</span></td>
   <td align=center><span class='style3'>$statusclt2</span></td>
   <td align=center><span class='style3'>$row_clt2[data_entrada] - $row_clt2[data_saida]</span></td>
   <td align=center><span class='style3'><a href=avalicao.php?bol=$row_clt2[0]&tab=$id_projeto&regiao=$id_regiao&tipo=clt class=link>Avaliar</a></span></td>
   </tr>";

}
print "<input type='hidden' name='regiao' value='$id_regiao'/><input type='hidden' name='projeto' value='$id_projeto'/>";
print "</table><br><center><input type='submit' value='Reavaliar em LOTE'></center></form>'";

print "<a href='bolsista_class.php?id=1&regiao=$id_regiao' class='link'><img src='imagens/voltar.gif' border=0></a>";

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
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
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
    <font size=3 class='style27'>A lista de op��es ainda n�o existe! <br><a href=bolsista_class.php?id=4&id_regiao=$id_regiao class='style27'>Para criar clique aqui</a> </font></td>
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
<link href=\"../net.css\" rel=\"stylesheet\" type=\"text/css\">
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
<td width='79%' colspan='3' class='style27'>Existem $row_cont[0] itens cadastrados at� o momento.</td>
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

}
?>