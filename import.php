<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

 if($submit) {
 
 $dbhost = "localhost";
 $dbuser = "root"; 
 $dbpass = ""; 
 $dbname = "intranet"; 
 
 $con = mysql_connect($dbhost, $dbuser, $dbpass);
 mysql_select_db($dbname, $con);
 
 $mypath="./upload/"; //NESTA LINHA VOCÊ COLOCA O LOCAL ONDE TEM PERMISSÃO DE GRAVACÃO PARA QUE O ARQUIVO POSSA SER UPLOADADO
 $mytable="teste"; // AQUI VOCÊ ESCOLHE O NOME DA TABELA
 
 if ($upfile_size<="100000000") { // LIMITE DE UPLOAD DE 100K
 
 $status="FUNCIONOU";
 $uploaded=date("YmdHis");

 $myfile=$mypath .$uploaded .".csv"; //AQUI ELE RENOMEIA O ARQUIVO.
 
 if (copy($upfile, $myfile)) { 
 $status.=", O ARQUIVO FOI COPIADO PARA ALGUM LUGAR";
 
//COLOCAR O ARQUIVO NA TABELA
 $insert_csv="LOAD DATA LOCAL INFILE '$myfile' INTO TABLE $mytable FIELDS TERMINATED BY ','";
 $result_csv = mysql_query($insert_csv, $con) or die("NÃO VIROU... " .mysql_error());
 echo "$myfile<br>";
 echo "$upfile<br>";
 if ($result_csv) {
 $status.=" E AGORA IMPORTADO PARA A BASE DE DADOS";
 } else {
 $status.=" MAS NÃO FOI POSSIVEL COLOCAR NA BASE DE DADOS";
 }
 } else {
 $status.="... O ARQUIVO NAO FOI COPIADO";
 }
 echo "$status";
 } else {
 echo "ARQUIVO MUITO GRANDE";
 }
 } else {
 ?>
 <html>
 <head>
 <title>Upload csv-file</title>
 </head>
 <body bgcolor="#ffffff" text="#000000" id=all>
 <form enctype="multipart/form-data" action="<? echo "$PHP_SELF"; ?>" method=POST>
 <div align="center">
 <table border="0" cellpadding="0" cellspacing="0" width="600" align="center">
 <tr>
 <td width="200" align="left" valign="top">ESCOLHA O ARQUIVO PARA UPLOAD </td>
 <td width="400" valign="top"><input name="upfile" type="file"><br><br></td>
 </tr>
 <tr>
 <td width="100%" colspan="2" align="center"><input type="submit" name="submit" value="Upload"></td>
 </tr>
 </table>
 </div>
 </form>
 </body>
 </html>
 <?
 }
 
 }
 ?>