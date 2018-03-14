<?php
include ("../../include/restricoes.php");
include('../../../conn.php');
include('../../../funcoes.php');
include('../../../classes/formato_data.php');
include('../../../classes/abreviacao.php');

list($regiao,$evento) = explode('&',decrypt(str_replace('--','+',$_REQUEST['enc'])));

$id_clt = mysql_real_escape_string($_GET['clt']);
$id_projeto = mysql_real_escape_string($_GET['pro']);
$id_regiao = mysql_real_escape_string($_GET['id_reg']);


?>
<html>
<head>
<title>:: Intranet :: Eventos</title>

<style type="text/css">
table {
	font-size:12px;
	}
tr.titulo{
	background-color:#818181;
	color: #FFF;
	font-size:12px;
	text-align:center;
	font-weight:bold;
	
}
tr.linha_um{
	background-color:   #EEE;

}

tr.linha_dois{
	background-color:    #FBFBFB;

}
tr.titulo_pagina {
background-color: #D3A9A9;
color:#fff;
text-align:center;
font-weight:bold;

}

</style>
</head>
<body>
<div id="corpo">

<table width="100%">
<tr class="titulo_pagina">
	<td colspan="5">RECISÃO</td>

</tr>
<tr class="titulo">
	
    <td  width="30%">TIPO DE EVENTO</td>
    <td  width="10%">DATA</td>
    <td  width="10%">DATA DE RETORNO</td>
    <td  width="10%">DIAS</td>
    <td  width="10%">OBSERVAÇÃO</td>
    
</tr>
<?php

$qr_rhstatus = mysql_query("SELECT * FROM rhstatus WHERE 1");
	while($row_status = mysql_fetch_assoc($qr_rhstatus)):


	$qr_eventos= mysql_query("SELECT rh_clt.id_clt, rh_clt.nome, rh_eventos.nome_status,  rh_eventos.data AS data_evento,  rh_eventos.data_retorno AS retorno, rh_eventos.dias,  rh_eventos.obs
								FROM rh_clt 
								INNER JOIN rh_eventos
								ON rh_clt.id_clt = rh_eventos.id_clt  							
								WHERE rh_clt.id_clt = '$id_clt' AND  rh_eventos.cod_status = '$row_status[codigo]';
								");
								
	if(mysql_num_rows($qr_eventos) != 0){							
	$row_eventos = mysql_fetch_assoc($qr_eventos);
?>							
    <tr class="linha_<?php if(($i++ % 2) == 0) {echo 'um'; } else {echo 'dois';}?>">
   		
         
         <td align="left"><?=$row_eventos['nome_status']?></td>
         <td align="center"><?php   if($row_eventos['data_evento'] != '0000-00-00') echo formato_brasileiro($row_eventos['data_evento'])?></td>
         <td align="center">
		 	<?php
            if($row_eventos['retorno'] != '0000-00-00') echo formato_brasileiro($row_eventos['retorno']);			
			?>
            
           </td>
         <td align="center"><?php
         if( $row_eventos['dias'] != 0) {
		 
		 echo $row_eventos['dias'];
		 
		 }?>
         
         </td>
        <td align="center"><?=$row_eventos['obs']?></td>
    </tr>



							
							
<?
	}
endwhile; // fim rhstatus
?>

</table>
</div>
         

</div>
</body>
</html>