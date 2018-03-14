<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
//include "../funcoes.php";
include "include/criptografia.php";

if(isset($_GET['clt']) or isset($_GET['todos'])){
   
    if(!empty($_GET['clt'])){
        $sql = "id_clt = $_GET[id_clt]";
    } else {
        $sql = 'id_projeto = '.$_GET['pro'].' AND status = 10 AND id_curso IN(1369,1370,1371,1372,1373,1374,1375,1376,1379,1380,1381,1387,1390,1391,1431,2012,1443,1444,1953,1952,2011,1950,1946,1942,1941,1940,1937,1936,1935,1934,1933,1932,1931,1930,1913,2010,1912,1910,1906,1902,1901,1900,1897,1896,1895,1894,1893,1892,1891,1890,1873,1872,2009,1870,1866,1862,1861,1860,1857,1856,1855,1853,1854,1852,1851,1850,1833,1832,2008,1830,1826,1822,1821,1820,1817,1816,1815,1814,1813,1812,1811,1810,1793,2007,1792,1790,1786,1782,1781,1780,1777,1776,1775,1774,1773,1772,1771,1770,1970,1971,1972,1973,1974,1975,1976,1977,1980,1981,1982,1986,1990,1992,1993,2013,2016,2017,2018,2022,2025,2028,2029,2030,2031,2032,2033,2034,2035,2036,2037,2038,2045,2057,2058,2059,2060,2062)';
    }
    
    
$qr_trab = mysql_query("SELECT * FROM rh_clt WHERE $sql ORDER BY nome");

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$_GET[pro]'");
$row_projeto = mysql_fetch_assoc($qr_trab);

	
} else if(isset($_GET['autonomo'])) {
	
$id_trabalhador = $_GET['autonomo'];
$qr_trab = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$id_trabalhador'");	
	
}


$row_trab = mysql_fetch_assoc($qr_trab);

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_funcionario);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_func[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);


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

<style >
body{
	font-weight:430;	
	text-transform:none;
	font-family:Arial, Helvetica, sans-serif;
}
p{
	width:100%;
	height:auto;
	text-align:left;
	padding-left: 10px;
	font-size:14px;
	text-transform:none;
	line-height:2em; 
	  
}
h3{
	text-align:center;
	
	font-weight:bold;
	font-size:16px;
}
.center{ text-align: center;}
table{ border: 1px solid #000;}

</style>
<style media="print">
    table{ border:0;}
</style>
<link rel="stylesheet" type="text/css" href="../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
    
  
  
  
  <?php while($row_trab = mysql_fetch_assoc($qr_trab)) {?>
  <table style="page-break-after: always;">
      <tr>
          <td><img src="../imagens/logomaster<?php echo $row_master['id_master'];?>.gif" />
                  <h3><?php echo $row_master['nome'];?></h3>
              </br></td>
      </tr>
      <tr>
          <td>
  <h3>Declaração de Jornada Semanal</h3>
  
  <p><?php echo $row_trab['nome'];?> ,declaro para os devidos fins  de direto que realizo as seguintes cargas horárias semanais no serviço público:</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  
  <p>______(por extenso) horas, no vínculo com o governo público federal;</p>
  <p>______(por extenso) horas, no vínculo com o governo público estadual;</p>
  <p>______(por extenso) horas, no vínculo com o governo público municipal;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  
  <p>Esclareço que meu CRM/CPF está vinculado  a ______(por extenso) horas semanais no CNES - Cadastro Nacional de Estabelecimentos de Saúde.</p>
  <p>Ante o  exposto, declaro  de forma irrevogável e irretratável, sob as penas da lei, que tenho plenas condições  de realizar:</p>
  <p>______(por extenso) horas de trabalho semanal sob o vínculo com a Organização Social <?php echo $row_master['nome'];?> e essa carga horária semanal pode ser totalmente 
  utilizada para cômputo  de horas  no CNES.</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  
  <p>Declaro ainda não haver superposição de caraga horária entre as atividades por mim exercidas.</p>
  <p>Comprometo-me a informar por escrito à Organização Social <?php echo $row_master['nome'];?> toda alteração (inclusão ou exclusão) na jornada acima transcrita, no prazo máximo de 
  3 (três) dias úteis anteriores à data da alteração ou, na impossibilidade, imediatamente à data de alteração </p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  
  <p class="center">Rio de Janeiro, <?php echo date('d')?> de <?php echo mesesArray(date('m'));?>  de <?php echo date('Y');?> </p>
  <p class="center">______________________________________________________</p>
  <p class="center">CRM/RJ</p>
  <p class="center">CPF/MF</p>
  
  
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>


</td>
      </tr>
  </table>
    
    <?php } ?>
   </div>
   <div class="rodape2">
  
          
   </div>
 </div>
</body>
</html>