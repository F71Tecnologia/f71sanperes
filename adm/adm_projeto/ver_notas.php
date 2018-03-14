<?php

//include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include('../include/criptografia.php');
include('../../classes/formato_data.php');

$id_projeto      = mysql_real_escape_string($_GET['id_projeto']);
$id_subprojeto   = mysql_real_escape_string($_GET['id_subprojeto']);
$tipo  			 = mysql_real_escape_string($_GET['tp']);
$totalizador  	 = 0;
 


if($tipo == 1) {

     
   $qr_notas = mysql_query("SELECT * FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE notas.id_projeto = '$id_projeto' AND notas.status = 1 AND notas.tipo_contrato = '$id_projeto'  AND entrada.status IN(2)  AND notas.tipo_contrato2 = 'projeto' ORDER BY entrada.data_vencimento");
   
   /*ANTES
     $qr_notas = mysql_query("SELECT * FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE notas.id_projeto = '$id_projeto' AND notas.status = 1 AND notas.tipo_contrato = '$id_projeto'  AND entrada.status IN(1,2)  AND notas.tipo_contrato2 = 'projeto' ORDER BY entrada.data_vencimento");
     */
 
}else {

//PROJETO
$qr_notas = mysql_query("SELECT * FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE notas.id_projeto = '$id_projeto' AND notas.status = 1 AND notas.tipo_contrato = '$id_subprojeto'  AND entrada.status IN(2) AND notas.tipo_contrato2 = 'subprojeto' ORDER BY entrada.data_vencimento");
	
/*ANTES
 * $qr_notas = mysql_query("SELECT * FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE notas.id_projeto = '$id_projeto' AND notas.status = 1 AND notas.tipo_contrato = '$id_subprojeto'  AND entrada.status IN(1,2) AND notas.tipo_contrato2 = 'subprojeto' ORDER BY entrada.data_vencimento");
 * 
 */



}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
</head>

<body style="background-color:#FFF; font-size:12px; text-transform:uppercase;">

<table>
	
    <?php 
	while($row_notas = mysql_fetch_assoc($qr_notas)):
	
	
		
	$ano = substr($row_notas['data_vencimento'], 0,4);
		
	if($ano != $ano_anterior) {
			
		echo '<tr>
				<td>&nbsp;</td>
			  </tr>
			  
			<tr class="novo2">
				<td colspan="4">'.$ano.'</td>
			</tr>
				
			<tr class="secao">
				<td width="20%">COD.</td>
				<td width="20%">VALOR</td>
				<td width="20%">DATA PAGAMENTO</td>
				<td width="30%">BANCO</td>   
			</tr>
			'
			;
		
	}
	?>
    <tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
    	<td><?php echo $row_notas['id_entrada']; ?></td>
        <td>
		<?php 
			
			$valor = str_replace(',','.',$row_notas['valor']);
			
			echo 'R$ '.number_format($valor,2,',','.');	
			$totalizador += $valor;
		 
		 ?>
         
         </td>
        <td><?php echo implode('/',array_reverse(explode('-',$row_notas['data_vencimento']))); ?></td>
        <td>  
		<?php 
					$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_notas[id_banco]' LIMIT 1");
					$row_banco = mysql_fetch_assoc($qr_banco);
					echo $row_banco['id_banco'] . ' - ' . utf8_encode($row_banco['nome']);
				?>
		</td>
        
     
	</tr>
	<?php
	
	$ano_anterior = $ano;
	endwhile;
	
	?>
    <tr>
    	<td>&nbsp;</td>
    </tr>
    
   <tr>
   <td colspan="5" align="right"><strong>Total: R$ <?php echo number_format($totalizador,2,',','.');?></strong></td>
   </tr> 
    
    
</table>
</body>
</html>
