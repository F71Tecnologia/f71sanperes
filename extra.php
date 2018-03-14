<?php
include "conn.php";

print "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head><title>:: Intranet ::</title>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
<link href=\"net2.css\" rel=\"stylesheet\" type=\"text/css\">
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
</script></head>

<body bgcolor=#5C7E59>";

if(empty($_REQUEST['data_inicio'])){

print "
<form action=extra.php method=post name=form1>
<center> DIGITE A DATA FINAL:<br>
<input name='data_inicio' type='text' id='data_inicio' size='10' OnKeyUp='mascara_data(this)' maxlength='10'>
<br><br>
<input type='submit' name='Submit' value='Continuar' />
</form>
 ";

}else{
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

$data_inicio = $_REQUEST['data_inicio'];

$data_inicio_f = ConverteData($data_inicio);

$result = mysql_query("SELECT nome,date_format(data_entrada, '%d/%m/%Y') as data_entrada,date_format(data_saida, '%d/%m/%Y') as data_saida,campo3,locacao FROM bolsista1 WHERE tipo_contratacao = '2' and 
data_entrada <= '$data_inicio_f' ORDER BY year(data_entrada),month(data_entrada),locacao ASC");

print "<center>$data_inicio<br><br>
<table width=90% border='0' cellspacing='0' cellpadding='0' class='tarefa'><tr>
<td align=center background='layout/fundo_tab_cinza.gif'><b>COD</b></td>
<td align=center background='layout/fundo_tab_cinza.gif'><b>Nome</b></td>
<td align=center background='layout/fundo_tab_cinza.gif'><b>Data Entrada</b></td>
<td align=center background='layout/fundo_tab_cinza.gif'><b>Data Saida</b></td>
<td align=center background='layout/fundo_tab_cinza.gif'><b>Unidade</b></td>
</tr>";

$cont = "0";

while($row = mysql_fetch_array($result)){

if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }

$unidade = str_replace(" - SECRETARIA MUNICIPAL DE SAÚDE DE MAUÁ - SP","",$row[locacao]);
$unidade = str_replace(" - SECRETARIA MUNICIPAL DE SAÚDE DE MAUÁ -SP","",$unidade);
$unidade = str_replace(" - SECRETARIA MUNICIPAL DE SAÚDE DE MAUÁ-SP","",$unidade);
$unidade = str_replace(" - SECRETARIA MUNUNICIPAL DE SAUDE DE MAÚA-SP","",$unidade);
$unidade = str_replace("- SECRETARIA MUNICIPAL DE SAÚDE DE MAUÁ - SP","",$unidade);


print "
<tr bgcolor=$color>
<td class=border2>$row[campo3]</td>
<td class=border2 align=left>$row[nome]</td>
<td class=border2>$row[data_entrada]</td>
<td class=border2>$row[data_saida]</td>
<td class=border3 align=center>$unidade</td>
</tr>";
$cont ++;
}

print "</table>";
}
?>