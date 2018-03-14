<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario   = mysql_fetch_array($query_funcionario);


$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);


if(isset($_GET['clt'])) {
$id_clt   =  $_GET['clt'];


$qr_processo = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt'");
$row_processo = mysql_fetch_assoc($qr_processo);

$qr_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_processo[id_curso]'");
$row_atividade = mysql_fetch_assoc($qr_atividade);

} else if(isset($_GET['autonomo'])){

$id_autonomo  =  $_GET['autonomo'];


$qr_processo = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_autonomo'");
$row_processo = mysql_fetch_assoc($qr_processo);

$qr_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_processo[id_curso]'");
$row_atividade = mysql_fetch_assoc($qr_atividade);
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>

<style>

.add_documento,.add_responsavel{
cursor:pointer;	
}
</style>

<link rel="stylesheet" type="text/css" href="../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
    
<table cellpadding="0" cellspacing="0" border="2px" width="100%">
<tr><td>
   
   <table style="font-size: 22px;; height:"100%"; width="100%"">
   <tr>
    <td colspan="2" style="height:420px;">&nbsp;</td>
   </tr>
   <tr>
   <td colspan="2"><img src="../imagens/logomaster<?php echo $id_master;?>.gif"  /><br /><br /><br /><br /></td>
   </tr>
   <tr>
   	<td align="right"><strong>Nº do Processo:</strong></td>
    <td align="left"><?php echo formato_num_processo($row_processo['n_processo']);?>/<?php echo formato_num_processo($row_processo['matricula']);?></td>
   </tr>
  	<tr>
  		<td align="right"><strong>Atividade:</strong></td>
  		<td align="left"><?php echo $row_atividade['nome']; ?></td>
  	</tr>
    <tr>
    	<td align="right"><strong>Nome:</strong></td>
    	<td align="left"><?php echo $row_processo['nome']?></td>
    </tr>
    <tr>
    <td colspan="2" style="height:640px;">&nbsp;</td>
    </tr>
   </table>
   
</td>
</tr>
</table>
  
  
   
   <div class="rodape2">
     <?php
     $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
          $master = mysql_fetch_assoc($qr_master); ?>
     <?=$master['razao']?>
  
  </div>
  
 
          
   </div>
 </div>
</body>
</html>