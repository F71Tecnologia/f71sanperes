<?php
include('adm/include/restricoes.php');
include('conn.php');
include('funcoes.php');
//include "../funcoes.php";
include "adm/include/criptografia.php";

$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario   = mysql_fetch_array($query_funcionario);


$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);


$id_autonomo   =  $_GET['autonomo'];
$projeto  =  $_GET['pro'];
$regiao   =  $_GET['id_reg'];






if(isset($_POST['cadastrar'])){

$nome 		= $_POST['nome'];
$id_autonomo 	= $_POST['id_autonomo'];
$atividade 	= $_POST['atividade'];
$n_processo = $_POST['n_processo'];
$projeto    = $_POST['projeto'];
$regiao     = $_POST['regiao'];

mysql_query("INSERT INTO processos_interno_autonomo (id_autonomo, proc_interno_nome, proc_interno_numero, proc_interno_atividade, data_cad, proc_interno_status)
											VALUES
											('$id_autonomo', '$nome', '$n_processo', '$atividade',NOW(),  '1')");
header("Location: ver_bolsista.php?bol=$id_autonomo&reg=$regiao&pro=$projeto");




	
}







?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js" ></script>
<script src="jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script src="jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="jquery.uploadify-v2.1.4/swfobject.js"></script>

<style>
.add_documento,.add_responsavel{
cursor:pointer;	
}
</style>

<link rel="stylesheet" type="text/css" href="adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
    
    <img src="imagens/logomaster<?php echo $id_master;?>.gif"/>
    <h3>ABERTURA DE PROCESSOS</h3>
    <form name="form" action="abertura_processo.php" method="post">
    <table  class="relacao"  width="100%">
    <tr>
    	<td class="titulo_tabela1" colspan="2">DADOS DO PROCESSO</td>
    </tr>
    <tr>
    	<td class="secao" width="40%">PROCESSO Nº:</td>
    	<td align="left" width="60%"><input type="text" name="n_processo" /></td>
    </tr>
    <tr>
    	<td class="secao"> ATIVIDADE:</td>
        <td align="left"><input name="atividade" type="text"/></td>
    </tr>
    <tr>
    	<td class="secao">NOME:</td>
    	<td align="left"><input name="nome" type="text"/></td>
    </tr>
    <tr>
    	<td colspan="2"> 
        
        <input type="hidden" name="id_autonomo" value="<?php echo $id_autonomo;?>"/>
        <input type="hidden" name="regiao" value="<?php echo $regiao;?>"/>
        <input type="hidden" name="projeto" value="<?php echo $projeto;?>"/>
        
        <input type="submit" name="cadastrar" value="CADASTRAR"/></td>
    <tr>
    
    </table>
    </form>
   <div class="rodape2">
     <?php
     $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
          $master = mysql_fetch_assoc($qr_master); ?>
     <?=$master['razao']?>
     &nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS    
  </div>
  
 
          
   </div>
 </div>
</body>
</html>