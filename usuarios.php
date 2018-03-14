<?php

 // Configuraзгo
 include "conn.php";
 
 // Seleciona os usuбrios
 $result = mysql_query("SELECT * FROM rh_cbo WHERE nome LIKE '%".addslashes($_GET['login'])."%'");
 $quantidade = mysql_num_rows($result);
  
 $Acao = mysql_query("SELECT * FROM rh_cbo WHERE nome LIKE '%".addslashes($_GET['login'])."%' LIMIT 0,100");
 $Resultados = mysql_num_rows($Acao);
 
 if($quantidade > 100){
	 $texto = "+ de ";
 }else{
	 $texto = "";
 }
 
 // Faz loop dos resultados
 while($row = mysql_fetch_array($Acao) ){
  
  $nome_texto = str_replace("б","a",$row['nome']);
  $nome_texto = str_replace("й","e",$nome_texto);
  $nome_texto = str_replace("н","i",$nome_texto);
  $nome_texto = str_replace("у","o",$nome_texto);
  $nome_texto = str_replace("ъ","u",$nome_texto);
  $nome_texto = str_replace("ъ","u",$nome_texto);
  $nome_texto = str_replace("г","a",$nome_texto);
  $nome_texto = str_replace("х","o",$nome_texto);
  $nome_texto = str_replace("з","c",$nome_texto);
  $nome_texto = str_replace("З","C",$nome_texto);
  $nome_texto = str_replace("в","a",$nome_texto);
  $nome_texto = str_replace("к","e",$nome_texto);
  $nome_texto = str_replace("ф","o",$nome_texto);
  $nome_texto = str_replace("Й","E",$nome_texto);
	 
	 
 echo "".$Resultados."\n".$row['0']."- ".$nome_texto."(".$row['cod'].")\n";
 }
 
?>