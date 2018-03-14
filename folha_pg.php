<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id = $_REQUEST['id'];

print "
<html><head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net.css\" rel=\"stylesheet\" type=\"text/css\">
<style type=\"text/css\">
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
<script>
   function mascara_data(d){  
       var mydata = '';  
       data = d.value;  
       mydata = mydata + data;  
       if (mydata.length == 2){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 5){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 10){  
          verifica_data(d);  
         }  
      } 
           
         function verifica_data (d) {  

         dia = (d.value.substring(0,2));  
         mes = (d.value.substring(3,5));  
         ano = (d.value.substring(6,10));  
             

       situacao = \"\";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = \"falsa\";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = \"falsa\";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = \"falsa\";  
      }  
   
     if (d.value == \"\") {  
          situacao = \"falsa\";  
    }  

    if (situacao == \"falsa\") {  
       alert(\"Data digitada é inválida, digite novamente!\"); 
       d.value = \"\";  
       d.focus();  
    }  
	
}

</script>
</head>";

switch ($id){

case "data":							//ABRINDO A TELA PARA COLOCAR A DATA DE PAGAMENTO

$tipo_pg = $_REQUEST['tipo_pg'];
$id_folhas = $_REQUEST['id_folhas'];
$id_projeto = $_REQUEST['id_projeto'];
$mes = $_REQUEST['mes'];
$tipo = $_REQUEST['koeiurjdpll'];
$banco = $_REQUEST['banco'];

if(empty($_REQUEST['adicional'])){
$tabela = "folha_$id_projeto";
$adicional__ = "";
}else{
$tabela = "folhaad_$id_projeto";
$adicional__ = "<input type='hidden' name='adicional' value='1'>";
}

print "
<form action='folha_pg.php' method='post' name='form'>
<center><font color=#FFFFFF><b>
Data para Pagamento:<br>
<input name='data_pg' id='data_pg' type='text' class='campotexto' size='13' OnKeyUp=\"mascara_data(this)\">
<br><Br>
<input type='submit' name='Submit' value='Enviar' class='campotexto'>

<input type='hidden' name='tipo_pg' value='$tipo_pg'>
<input type='hidden' name='id_folhas' value='$id_folhas'>
<input type='hidden' name='id_projeto' value='$id_projeto'>
<input type='hidden' name='mes' value='$mes'>
<input type='hidden' name='koeiurjdpll' value='$tipo'>
<input type='hidden' name='banco' value='$banco'>
<input type='hidden' name='tabela' value='$tabela'>
<input type='hidden' name='id' value='1'>
$adicional__

</center></font></b>
</form>
";

break;

case 1:							//VENDO A FOLHA DE PAGAMENTO PELO TIPO DE PAGAMENTO (BANCO)


$tipo_pg = $_REQUEST['tipo_pg'];
$id_folhas = $_REQUEST['id_folhas'];
$id_projeto = $_REQUEST['id_projeto'];
$mes = $_REQUEST['mes'];
$tipo = $_REQUEST['koeiurjdpll'];
$banco = $_REQUEST['banco'];

if(empty($_REQUEST['data_pg'])){
$data_pg = "00/00/0000";
}else{
$data_pg = $_REQUEST['data_pg'];
}

if(empty($_REQUEST['adicional'])){
$tabela = "folha_$id_projeto";
}else{
$tabela = "folhaad_$id_projeto";
}

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

$ano = date('y');

$result_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$banco'"); 
$row_banco = mysql_fetch_array($result_banco);

$result = mysql_query("SELECT * FROM $tabela WHERE id_folhas = $id_folhas and banco = '$banco' and sit = '1' and salario != '0,00'");

$result_cont_zero = mysql_query("SELECT * FROM $tabela WHERE id_folhas = $id_folhas and banco = '$banco' and sit = '1' and salario = '0,00' ");
$cont_zero = mysql_num_rows($result_cont_zero);

print "<br><center>
<b>
<table width='80%' border='0' cellpadding='0' cellspacing='0' background='layout/tab_folha_fundo.gif'>
<tr>
<td width='4%'><img src='layout/tab_folha_esquerda.gif' width='26' height='147' /></td>
<td align='center' valign='middle' width='46%'>
<font color=#FFFFFF size=3><b>
Folha Referente ao Mês:&nbsp;$mes
<br><br>
Banco:&nbsp;$row_banco[id_nacional] - $row_banco[nome]
<br><br>
Data para pagamento:&nbsp;&nbsp;&nbsp;$data_pg
</b></font>
</td>
<td align='center' valign='middle' width='46%'>
<font color=#FFFFFF size=3><b>

<a href='#' style='TEXT-DECORATION: none;' onClick='confirm_entry()'>
<font color=#FFFFFF size=3><b>
<img src='imagens/desgerar_folha.gif' border='0' align='absmiddle'>
FINALIZAR FOLHA
</a>

</b></font>
</td>
<td width='4%' align='right'>
<img src='layout/tab_folha_direita.gif' width='26' height='147' /></td>
</tr>
</table>
<br>
<br>
<br>";

print "
<table bgcolor=#FFFFFF align='center' width='97%'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center width='5%'>Cód.</td>
<td align=center width='25%'>Nome</td>
<td align=center width='10%'>Salário Bruto</td>
<td align=center width='4%'>Faltas</td>
<td align=center width='10%'>Salário Liquido</td>
</tr>";

$valor_cotal = "0";
$cont_arquivo = "1";

include "w-cabecalho_arquivo.php";
$cont_arquivobb = "0";

while($row = mysql_fetch_array($result)){

$result2 = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row[id_bolsista]' and id_projeto = '$id_projeto'");
$row2 = mysql_fetch_array($result2);

$result3 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row2[id_curso]'");
$row3 = mysql_fetch_array($result3);

if($cont_color % 2){ $color="linhan"; }else{ $color="linha"; }


$salario_normal = $row['salario'];

$valor_curso = number_format($row3['salario'],2,",",".");

$salario_normal = str_replace(",",".", $salario_normal);
$salario_f = number_format($salario_normal,2,",",".");

print "<tr onmouseover=\"this.className='table_over'\" onmouseout=\"this.className='$color'\" class='$color'>
<td><font color=#000000>$row2[campo3]</font></td>
<td><font color=#000000>$row2[nome]</font></td>
<td><font color=#000000>R$ $valor_curso</font></td>
<td><font color=#000000>$row[faltas]</font></td>
<td><font color=#000000>R$ $salario_f</font></td>
</tr>";
$cont_color ++;
$valor_cotal = $valor_cotal + $salario_normal;
$cont_arquivo ++;
$cont_arquivobb ++;

include "w-descricao_arquivo.php";

}

$valor_ctotal_f = number_format($valor_cotal,2,",",".");
$valor_total_banco = str_replace(".","",$valor_cotal);

//--------------------------- GRAVANDO O ARQUIVO ---------------------------//
$cont_linha ++;
$cont_linha = sprintf("%06d", $cont_linha);

include "w-rodape_arquivo.php";

//PREPARA O CONTEÚDO A SER GRAVADO
//$conteudo = "$cabecalho_real$linha_real";
$conteudo = "$cabecalho$linha$rodape";

//ARQUIVO TXT

$d = str_replace ("ú","u", $row_banco['nome']);
$d = explode (" ", $d);

 $nome_banco_espaco = "$d[0]"; 

/*
NOME DO BANCO BASEA-SE EM:
ID DO PROJETO
PRIMEIRO NOME DO BANCO
ID DO BANCO
MES DA FOLHA
ANO CORRENTE
*/

$nome_arquivo_download = $id_projeto.$nome_banco_espaco.$row_banco['0'].$mes.$ano.".txt";
$arquivo = "/home/ispv/public_html/intranet/arquivos/".$nome_arquivo_download;

//TENTA ABRIR O ARQUIVO TXT
if (!$abrir = fopen($arquivo, "wa+")) {
echo "Erro abrindo arquivo ($arquivo)";
exit;
}

//ESCREVE NO ARQUIVO TXT
if (!fwrite($abrir, $conteudo)) {
print "Erro escrevendo no arquivo ($arquivo)";
exit;
}

//FECHA O ARQUIVO 
fclose($abrir); 

if($cont_zero == "0"){
$mensagem_zerada = "";
}else{
$mensagem_zerada = "Atenção: <br>
Existe(m) $cont_zero autonomo(s) com o salário ZERADO! (que não estão nesta lista)<br><br>
";
}

print "</table><br><br>
Valor final: R$ $valor_ctotal_f<br><br>
$mensagem_zerada
Baixar arquivo do banco: <a href=arquivos/$nome_arquivo_download>Aqui</a><BR><font size=1 color=#FFFFFF>(clique com o botão direito, depois em SALVAR DESTINO COMO...)";

print "
<script>
function confirm_entry()
{
input_box=confirm(\"Deseja realmente enviar para pagamento?\");
if (input_box==true)

{ 
// Output when OK is clicked
// alert (\"You clicked OK\"); 
location.href=\"folha_pg.php?id=3&id_projeto=$id_projeto&regiao=$row_banco[id_regiao]&id_folha=$id_folhas&banco=$row_banco[0]&mes=$mes\"
}

else
{
// Output when Cancel is clicked
// alert (\"You clicked cancel\");
}

}
-->
</script>";

break;


case 2:										//VENDO A FOLHA DE PAGAMENTO PELO TIPO DE PAGAMENTO (CHEQUE)

$tipo_pg = $_REQUEST['tipo_pg'];
$id_folhas = $_REQUEST['id_folhas'];
$id_projeto = $_REQUEST['id_projeto'];
$mes = $_REQUEST['mes'];
$tipo = $_REQUEST['koeiurjdpll'];

if(empty($_REQUEST['adicional'])){
$tabela = "folha_$id_projeto";
}else{
$tabela = "folhaad_$id_projeto";
}


$tipo_pg_5 = mysql_query("SELECT * FROM tipopg  where id_projeto = '$id_projeto' and campo1 = '2'");
$row_tipo_pg_5 = mysql_fetch_array($tipo_pg_5);

$result = mysql_query("SELECT * FROM $tabela WHERE id_folhas = $id_folhas and sit = '1' and tipo_pg = '$row_tipo_pg_5[0]'");

print "<br><center>
<b><font color=#000000>Participante<br>
Pagamento em Cheque referente ao mes $mes
<br><br>";

print "
<table bgcolor=#FFFFFF align='center' width='97%'>
<tr class='linha' bgcolor=#CCCCCC>
<td align=center width='5%'>Cód.</td>
<td align=center width='25%'>Nome</td>
<td align=center width='10%'>Salário Bruto</td>
<td align=center width='4%'>Faltas</td>
<td align=center width='10%'>Salário Liquido</td>
<td align=center width='10%'>Status</td>
</tr>";

$valor_cotal = "0";
while($row = mysql_fetch_array($result)){

$result2 = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row[id_bolsista]' and id_projeto = '$id_projeto'");
$row2 = mysql_fetch_array($result2);

$result3 = mysql_query("SELECT * FROM curso WHERE id_curso = '$row2[id_curso]'");
$row3 = mysql_fetch_array($result3);

if($cont_color % 2){ $color="linhan"; }else{ $color="linha"; }

if($row['sit'] == "0"){
  $mensagem = "não receberá";
 }else{
  $mensagem = "ok";
}

$salario = number_format($row['salario'],2,",",".");
$valor_curso = number_format($row3['salario'],2,",",".");

print "<tr onmouseover=\"this.className='table_over'\" onmouseout=\"this.className='$color'\" class='$color'>
<td><font color=#000000>$row2[campo3]</font></td>
<td><font color=#000000>$row2[nome]</font></td>
<td><font color=#000000>R$ $valor_curso</font></td>
<td><font color=#000000>$row[faltas]</font></td>
<td><font color=#000000>R$ $salario</font></td>
<td>$mensagem</td>
</tr>";
$cont_color ++;
$valor_cotal = $valor_cotal + $row['salario'];
}
$valor_ctotal_f = number_format($valor_cotal,2,",",".");
print "</table><br><br>Total da Folha: R$ $valor_ctotal_f<br><br>";


break;

case 3:

$id_projeto = $_REQUEST['id_projeto'];
$id_folha = $_REQUEST['id_folha'];
$regiao = $_REQUEST['regiao'];
$banco = $_REQUEST['banco'];
$mes = $_REQUEST['mes'];

print "
Projeto: $id_projeto <br>
Id Folha: $id_folha <br>
Banco: $banco<br>
Mes : $mes<br>
Região: $regiao <br>
";

mysql_query("UPDATE folha_$id_projeto SET status = '2' where id_folhas = '$id_folha' and banco = '$banco'");

/*
print "<script language= \"JavaScript\">location.href=\"gerando_dados.php\"</script>";
*/

break;
}

}
?>