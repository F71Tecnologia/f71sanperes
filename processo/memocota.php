
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

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_prestador[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);


$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_prestador[id_projeto]'");
$row_projeto = mysql_fetch_assoc($qr_projeto);


$data = date("d/m/Y");

$mes = sprintf('%02s', date('m'));
$qr_meses = mysql_query("SELECT * FROM ano_meses WHERE num_mes = '$mes'");
$row_mes = mysql_fetch_assoc($qr_meses);

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
<style >
body{

text-transform:inherit;	
}


</style>

<style media="print">
table.menu{
 visibility:hidden;
 margin:0;
 
 
}
table{

border:0;u	
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
font-size:12px;
text-transform:inherit;	
}

</style>

</head>
<body  class="fundo_juridico" >

    
<table cellpadding="0" cellspacing="0"  width="900" align="center" style="border:#000 0px solid;padding:5px; " border="0">
<tr><td>
<table width="100%"  border="0px"  cellspacing="0" cellpadding="1">
	<tr>
    <td style="border-bottom-width:0px; border-bottom-color:#FFF">
    <table width="100%" cellpadding="0" style="border-color:#000;" border="1px" cellspacing="0">
    <tr>
    <td width="15%" align="center"><img src="../imagens/logomaster<?php echo $row_master['id_master']?>.gif"  /></td>
    <td width="60%" align="center"><b><?php echo $row_master['razao']?></b></td>
    <td width="25%" align="center">UF RESPONSÁVEL<br><b><?php echo $row_master['sigla'].' - '.$row_master['uf']?></b></td>
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
    <td width="20%" align="center">CODIFICAÇÃO<br><b>NOR-2000-001</b></td>
    <td width="10%" align="center">VERSÃO<br><b>01</b></td>
    <td width="10%" align="center">PÁGINA<br><b>1 / 1</b></td>
    </tr>
    </table>
    </td>
    </tr>
    
    </table>
   
  
    <tr>
    <td colspan="2" style="height:150px; " >
  		<strong> <?=$row_prestador['co_municipio']?>, &nbsp;&nbsp;</strong><?php echo  date('d').' de '.$row_mes['nome_mes'].' de '.date('Y')?>
    </td>
    </tr>
    
    
    <tr>
	    <td colspan="2" style="height:30px; " align="left" >
	  	    Senhores,   
	    </td>
    </tr>
    <tr>
	    <td colspan="2" style="height:30px; " >
	  	    Ref.: COTAÇÃO<br /><br />
              <strong>  <?=$row_prestador['c_razao']?></strong>
	    </td>
    </tr>
    
 
     <tr>
    <td colspan="2" style="height:300px; " >
                Conforme Regulamento de Compras e Contratação de Obras e Serviços do <?php echo $row_prestador['contratante']?>, bem como em cumprimento ao disposto na Lei Federal n.º 9637/98, solicito tomada de preços (cotação) de serviço especializado para prestação de:  <?php print "$row_prestador[especificacao]"; ?> EM <?=$row_prestador['co_municipio']?>. para complementação a <?php echo $projeto['nome']?>, no município de  <?=$row_prestador['co_municipio']?>.   
    
  	     Conforme regulamento de  contratações, preconizado na Lei n.º 9790/99 e Decreto n.º 3.100/99. Solicito  

        tomada de 

        pre&ccedil;os (cota&ccedil;&atilde;o) de servi&ccedil;o especializado para presta&ccedil;&atilde;o de servi&ccedil;os:<strong>
        <?php print "$row_prestador[especificacao]"; ?>
        </strong> 
        para   complementa&ccedil;&atilde;o a(o) <strong>  <?=$row_projeto['nome']?>
        </strong>, no munic&iacute;pio de <strong> <?=$row_prestador['co_municipio']?> </strong>.
    </td>
    </tr>
    <tr>
    <td colspan="2" >PROCESSO N&ordm;: 

        <?=$row_prestador['numero']?></td>
    </tr>
    <tr>
    <td colspan="2" style="height:150px;">&nbsp;</td>
    </tr>
     <tr>
    <td colspan="2" >_________________________________</td>
    </tr>
    <tr>
    <td colspan="2" >  <?=$row_func['nome']?></td>
    </tr>
      <tr>
    <td colspan="2" style="height:90px;">&nbsp;</td>
    </tr>
     <tr>
    <td colspan="2" align="center" ><b>EXEMPLAR Nº 00 - Vigência &nbsp; <?php echo  date('d').' de '.$row_mes['nome_mes'].' de '.date('Y')?></b></td>
    </tr>
    <tr>
    <td colspan="2" align="center" ><b>PROIBIDA A REPRODUÇÃO</b></td>
    </tr>
   
   </table>
   
</td>
</tr>
</table>
  
  
   
   <div class="rodape2">

  
  </div>
  
 
</body>
</html>




<?php

if($row_prestador['imprimir'] == "0"){

mysql_query("UPDATE prestadorservico SET imprimir = '1' WHERE id_prestador = '$id_prestador'") or die ("Erro no UPDATE<br><br>".mysql_error()) ;

}





?>