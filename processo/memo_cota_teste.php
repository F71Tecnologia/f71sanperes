<?php
include('../adm/include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include "../adm/include/criptografia.php";


$regiao = $_REQUEST['regiao'];

$id_prestador = $_REQUEST['prestador'];

$id_user = $_COOKIE['logado'];



$result_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");

$row_func = mysql_fetch_array($result_func);



$result_prestador = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM prestadorservico WHERE id_prestador = '$id_prestador'");

$row_prestador = mysql_fetch_array($result_prestador);


$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_func[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


$data = date("d/m/Y");



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>SOLICITA&Ccedil;&Atilde;O INTERNA</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>
<script type="text/javascript" src="include/botoes.js"></script>
<style>
body{

text-transform:inherit;	
}


</style>

<style media="print">
table.menu{
 visibility:hidden;
 margin:0;
 
 
}
</style>
<style media="screen">

table.menu{

 width:100%;
 text-align:center;
 border:2px solid #CCC;
 padding-top:10px;
 margin-bottom:30px;
 
}
body{

text-transform:inherit;	
}

</style>

<link rel="stylesheet" type="text/css" href="../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">   
    
<table cellpadding="0" cellspacing="0"  width="100%">
<tr><td>
<table width="100%"  border="0px" style="border-color:#000 1px solid; font-size:19px" cellspacing="0" cellpadding="1">
	<tr>
    <td style="border-bottom-width:0px; border-bottom-color:#FFF">
    <table width="100%" cellpadding="0" style="border-color:#000;" border="1px" cellspacing="0">
    <tr>
    <td width="15%" align="center"><img src="../imagens/logomaster<?php echo $row_master['id_master']?>.gif"  /></td>
    <td width="60%" align="center"><b><?php echo $row_master['razao']?></b></td>
    <td width="25%" align="center">UF RESPONS�VEL<br><b>ILR-RJ</b></td>
    </tr>
    </table>
    </td>
    </tr>
    
    <tr>
    <td>
    <table width="100%" cellpadding="0" style="border-color:#000;" border="1" cellspacing="0">
    <tr>
    <td width="60%" align="left">TITULO:<br />
    <b>SOLICITA&Ccedil;&Atilde;O  INTERNA </b></td>
    <td width="20%" align="center">CODIFICA��O<br><b>NOR-2000-001</b></td>
    <td width="10%" align="center">VERS�O<br><b>01</b></td>
    <td width="10%" align="center">P�GINA<br><b>1 / 1</b></td>
    </tr>
    </table>
    </td>
    </tr>
    
    </table>
    <tr>
    <td colspan="2" style="height:250px;">&nbsp;</td>
    </tr>
  
    <tr>
    <td colspan="2" style="height:100px; font-size:22px" >
  		<strong>S&atilde;o Paulo, &nbsp;&nbsp;</strong>_____/_____/________.
    </td>
    </tr>
    
    
    <tr>
	    <td colspan="2" style="height:22px; font-size:22px" align="left" >
	  	    Senhores,   
	    </td>
    </tr>
    <tr>
	    <td colspan="2" style="height:22px; font-size:22px" >
	  	    Ref.: COTA��O<br />
              <strong>  <?=$row_prestador['c_razao']?></strong>
	    </td>
    </tr>
    
 
     <tr>
    <td colspan="2" style="height:360px; font-size:22px" >
  	���� Conforme regulamento de  contrata��es, preconizado na Lei n.� 9790/99 e Decreto n.� 3.100/99. Solicito  

        tomada de 

        pre&ccedil;os (cota&ccedil;&atilde;o) de servi&ccedil;o especializado para presta&ccedil;&atilde;o de servi&ccedil;os:<strong>
        <?php print "$row_prestador[especificacao]"; ?>
        </strong> 
        para   complementa&ccedil;&atilde;o a(o) <strong>  <?=$row_projeto['nome']?>
        </strong>, no munic&iacute;pio de <strong> <?=$row_prestador['co_municipio']?> </strong>.
    </td>
    </tr>
    <tr>
    <td colspan="2" style="font-size:22px">PROCESSO N&ordm;: 

        <?=$row_prestador['numero']?></td>
    </tr>
    <tr>
    <td colspan="2" style="height:150px;">&nbsp;</td>
    </tr>
     <tr>
    <td colspan="2" style="font-size:22px">_________________________________</td>
    </tr>
    <tr>
    <td colspan="2" style="font-size:22px">  <?=$row_func['nome']?></td>
    </tr>
      <tr>
    <td colspan="2" style="height:90px;">&nbsp;</td>
    </tr>
     <tr>
    <td colspan="2" align="center" style="font-size:18px"><b>EXEMPLAR N� 00 - Vig�ncia<?php echo $data?></b></td>
    </tr>
    <tr>
    <td colspan="2" align="center" style="font-size:18px"><b>PROIBIDA A REPRODU��O</b></td>
    </tr>
   
   </table>
   
</td>
</tr>
</table>
  
  
   
   <div class="rodape2">

  
  </div>
  
 
          
   </div>
 </div>
</body>
</html>




<?php

if($row_prestador['imprimir'] == "0"){

mysql_query("UPDATE prestadorservico SET imprimir = '1' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}





?>