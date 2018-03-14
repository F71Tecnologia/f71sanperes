<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{
?>
<?php 
require("../conn.php");
$id_file = $_GET['id_file'];
if(isset($_GET['multi'])){
	$multiplo 	= explode(',',$_GET['multi']);
	$id_file 	= $multiplo[0]; 
}
$query_documento = mysql_query("SELECT * FROM documentos as doc, doc_files as fil WHERE doc.id_documento = fil.id_documento AND fil.id_file = '$id_file'");
$row_documento = mysql_fetch_assoc($query_documento);
?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style4 {font-family: Arial, Helvetica, sans-serif}
.style28 {font-family: Arial, Helvetica, sans-serif; font-size: 10px; }
.style32 {
	font-size: 12px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
.style33 {font-family: Verdana, Arial, Helvetica, sans-serif}
.style34 {font-size: 10px}
.style41 {font-size: 12px; font-family: Verdana, Arial, Helvetica, sans-serif; font-weight: bold; color:#000; }
-->
</style>
</head>

<body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="top"><table width="750" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
        <tr align="center" valign="top">
          <td width="20" rowspan="2"> <div align="center"></div></td>
          <td align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><br>
                  <span class="style4">
				  <?php
					include "../empresa.php";
					$img = new empresa();
					$img->imagem();
					?></span></td>
              </tr>
            </table>
            <p class="style28"><span class="style41"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></span></p>
            <?php if(!empty($row_documento['recebimento_file']) and isset($_GET['recebimento'])){?>
            <p class="style28"><span class="style41"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PROTOCOLO DE RECEBIMENTO DE DOCUMENTOS</strong> 
            <?php }else{?>
            <p class="style28"><span class="style41"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PROTOCOLO DE ENVIO DE DOCUMENTOS</strong> 
            <?php }?>
            </span></p>
            <p class="style28">&nbsp;</p>
            <p class="style28"><span class="style41">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N&uacute;mero do protocolo de envio: <?=$id_file?></span></p>
            <p class="style28"><span class="style41"><strong><br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Documento: <?=$row_documento['nome_documento']?> </strong></span></p>
            <p class="style28"><span class="style41"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Referente a regi&atilde;o:
			<?php
				$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_documento[id_regiao]'");
				echo mysql_result($query_regiao,0);
            ?>
            <br>
            <br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enviado em: <?= implode("/",array_reverse(explode("-",$row_documento['data_file'])))?> <br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Funcion&aacute;rio: 
<?php
	$query_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row_documento[id_funcionario]'");
	$row_funcionario = mysql_fetch_assoc($query_funcionario);
	echo $row_funcionario['id_funcionario']." - ".$row_funcionario['nome'];
?>
</strong> <br>
<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;Data limite de envio: <?=$row_documento['dia_documento']?></span></p>
<p class="style28"><span class="style41">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nome do arquivo enviado:
<?php
if(isset($_GET['multi'])){
	foreach($multiplo as $id){
		$query = mysql_query("SELECT id_file, tipo_file FROM doc_files WHERE id_file = '$id';");
		$row = mysql_fetch_assoc($query);
		print $row['id_file'].".".$row['tipo_file']." ";
	}
	
	
}else{
	echo $id_file.".".$row_documento['tipo_file'];
}

?>
 </span></p>
 <?php if(!empty($row_documento['recebimento_file']) and isset($_GET['recebimento'])){ ?>
<p class="style28">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style41">Recebido por: 
	<?php
    $query_funcionario_envio = mysql_query("SELECT id_funcionario,nome FROM funcionario WHERE id_funcionario = '$row_documento[id_recebimento_file]'");
	$row_funcionario_envio = mysql_fetch_assoc($query_funcionario_envio);
	echo $row_funcionario_envio['id_funcionario']." - ".$row_funcionario_envio['nome'];
	?>
</span></p>
<p class="style28">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="style41">Data de recebimento: 
<?=
	implode("/",array_reverse(explode("-",$row_documento['data_recebimento_file'])));
?>
</span></p>
<?php }?>
<p class="style28">&nbsp;</p>
<p class="style28"><span class="style41"><br>
<br>
</span></p>
<p class="style28">              <font size="3">
              <center>              
              </center>
              </font></p>
            <p><font size="3"> <br>
              </font> </p>          </td>
          <td width="20" rowspan="2">&nbsp;</td>
        </tr>

        <tr>
          <td bgcolor="#8FC2FC" class="igreja" height="12">
            <div align="center"></div></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php
}
?>
