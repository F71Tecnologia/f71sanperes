<?php

 // Configuração
 include "../conn.php";
 
 // Seleciona os usuários
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
  
  $nome_texto = str_replace("á","a",$row['nome']);
  $nome_texto = str_replace("é","e",$nome_texto);
  $nome_texto = str_replace("í","i",$nome_texto);
  $nome_texto = str_replace("ó","o",$nome_texto);
  $nome_texto = str_replace("ú","u",$nome_texto);
  $nome_texto = str_replace("ã","a",$nome_texto);
  $nome_texto = str_replace("õ","o",$nome_texto);
  $nome_texto = str_replace("ç","c",$nome_texto);
  $nome_texto = str_replace("Ç","C",$nome_texto);
  $nome_texto = str_replace("â","a",$nome_texto);
  $nome_texto = str_replace("ê","e",$nome_texto);
  $nome_texto = str_replace("ô","o",$nome_texto);
  $nome_texto = str_replace("Á","A",$row['nome']);
  $nome_texto = str_replace("É","E",$nome_texto);
  $nome_texto = str_replace("Í","I",$nome_texto);
  $nome_texto = str_replace("Ó","O",$nome_texto);
  $nome_texto = str_replace("Ú","U",$nome_texto);
  $nome_texto = str_replace("Ã","A",$nome_texto);
  $nome_texto = str_replace("Õ","O",$nome_texto);
  $nome_texto = str_replace("Ç","C",$nome_texto);
  $nome_texto = str_replace("Â","A",$nome_texto);
  $nome_texto = str_replace("Ê","E",$nome_texto);
  $nome_texto = str_replace("Ô","O",$nome_texto);
	 

 echo "".$Resultados."\n"."<a href=ver_clt.php?reg=$row[id_regiao]&clt=$row[0]&ant=&pro=$row[id_projeto]&pagina=clt>".$row['campo3']."- ".$nome_texto."</a>\n";
 }
 
?>