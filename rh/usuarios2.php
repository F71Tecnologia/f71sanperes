<?php

 // Configura��o
 include "../conn.php";
 
 // Seleciona os usu�rios
 $result = mysql_query("SELECT * FROM rh_clt WHERE nome LIKE '%".addslashes($_GET['login'])."%' AND id_regiao = '".addslashes($_GET['reg'])."'");
 $quantidade = mysql_num_rows($result);
  
 $Acao = mysql_query("SELECT * FROM rh_clt WHERE nome LIKE '%".addslashes($_GET['login'])."%' AND id_regiao = '".addslashes($_GET['reg'])."' LIMIT 0,100");
 $Resultados = mysql_num_rows($Acao);
 
 if($quantidade > 100){
	 $texto = "+ de ";
 }else{
	 $texto = "";
 }
 
 // Faz loop dos resultados
 while($row = mysql_fetch_array($Acao) ){
  
  $nome_texto = str_replace("�","a",$row['nome']);
  $nome_texto = str_replace("�","e",$nome_texto);
  $nome_texto = str_replace("�","i",$nome_texto);
  $nome_texto = str_replace("�","o",$nome_texto);
  $nome_texto = str_replace("�","u",$nome_texto);
  $nome_texto = str_replace("�","a",$nome_texto);
  $nome_texto = str_replace("�","o",$nome_texto);
  $nome_texto = str_replace("�","c",$nome_texto);
  $nome_texto = str_replace("�","C",$nome_texto);
  $nome_texto = str_replace("�","a",$nome_texto);
  $nome_texto = str_replace("�","e",$nome_texto);
  $nome_texto = str_replace("�","o",$nome_texto);
  $nome_texto = str_replace("�","A",$row['nome']);
  $nome_texto = str_replace("�","E",$nome_texto);
  $nome_texto = str_replace("�","I",$nome_texto);
  $nome_texto = str_replace("�","O",$nome_texto);
  $nome_texto = str_replace("�","U",$nome_texto);
  $nome_texto = str_replace("�","A",$nome_texto);
  $nome_texto = str_replace("�","O",$nome_texto);
  $nome_texto = str_replace("�","C",$nome_texto);
  $nome_texto = str_replace("�","A",$nome_texto);
  $nome_texto = str_replace("�","E",$nome_texto);
  $nome_texto = str_replace("�","O",$nome_texto);
	 

 echo "".$Resultados."\n"."<a href=ver_clt.php?reg=$row[id_regiao]&clt=$row[0]&ant=&pro=$row[id_projeto]&pagina=clt>".$row['campo3']."- ".$nome_texto."</a>\n";
 }
 
?>