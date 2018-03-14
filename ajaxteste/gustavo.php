<?php
//INCLUINDO O ARQUIVO DE CONEXAO COMO BANCO DE DADOS
include "../conn.php";

$qr_masculino = mysql_query("SELECT * FROM autonomo WHERE sexo = 'M' ORDER BY nome ASC LIMIT 0,30") or die (mysql_error());
$masculino = mysql_fetch_assoc($qr_masculino);

$qr_feminino = mysql_query("SELECT * FROM autonomo WHERE sexo = 'F' ORDER BY nome ASC LIMIT 0,30") or die (mysql_error());
$feminino = mysql_fetch_assoc($qr_feminino);
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
<title>Consulta</title>
</head>
<body>
<h1>Autônomos</h1>
<h2>Homens</h2>
<table cellpadding="4">
   <tr>
      <td><b>Nome</b></td>
      <td><b>Sexo</b></td>
      <td><b>Idade</b></td>
      <td><b>Data Nasc.</b></td>
   </tr>
<?php 
while($masculino = mysql_fetch_assoc($qr_masculino)){ 
?>
   <tr>
      <td><?php echo $masculino['nome']; ?></td>
      <td>Masculino</td>
      <td><?php $ano = explode('-', $masculino['data_nasci']); echo date('Y') - $ano[0]; ?> anos</td>
      <td><?php echo $masculino['data_nasci']; ?></td>
   </tr>
<?php
}
?>
</table>
<p>&nbsp;</p>
<h2>Mulheres</h2>
<table cellpadding="4">
   <tr>
      <td><b>Nome</b></td>
      <td><b>Sexo</b></td>
      <td><b>Idade</b></td>
      <td><b>Data Nasc.</b></td>
   </tr>
<?php  do { ?>
   <tr>
      <td><?php  echo $feminino['nome']; ?></td>
      <td>Feminino</td>
      <td><?php $ano = explode('-', $feminino['data_nasci']); echo date('Y') - $ano[0]; ?> anos</td>
      <td><?php  echo $feminino['data_nasci']; ?></td>
   </tr>
<?php  } while($feminino = mysql_fetch_assoc($qr_feminino)); ?>
</table>
</body>
</html>