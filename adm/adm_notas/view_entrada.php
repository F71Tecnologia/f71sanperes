<?php 

include "../../conn.php";

if(isset($_GET['excluir'])){
	mysql_query("DELETE FROM notas_assoc WHERE id_entrada = '$_GET[id]'");
	header('location : view_entrada.php?nota='.$_GET['nota']);
}

$id_nota  = $_GET['nota'];

$qr_entrada 	= mysql_query("SELECT *, entrada.status as status_entrada FROM  entrada INNER JOIN notas_assoc ON notas_assoc.id_entrada = entrada.id_entrada WHERE notas_assoc.id_notas = '$id_nota' AND entrada.status IN (2,1);");
$row_entrada = mysql_fetch_assoc($qr_entrada);
$num_entrada 	= mysql_num_rows($qr_entrada);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/estrutura.css" type="text/css" rel="stylesheet" />
<title>Visualizar Entrada</title>
</head>
<body>

<?php if(empty($num_entrada)):?>
       	<center>Nenhuma entrada cadastrada para essa nota.</center>
<?php 
	exit;
endif;?>
	<table class="relacao">
    	<tr>
        	<td colspan="4"><h1 style="width:210px;">Entrada(s) relacionadas a nota <?php 
			$qr_notas = mysql_query("SELECT numero FROM notas WHERE id_notas = '$row_entrada[id_notas]'");
				echo @mysql_result($qr_notas,0);?></h1></td>
        </tr>
        
        
        <tr class="secao">
        	<td width="25%">Cod.</td>
            <td width="25%">Valor</td>
            <td width="25%">Data pagamento</td>
            <td width="25%">Banco</td>
            <td></td>
            <td>status</td>
        </tr>
        <?php 
			$totalizador = 0;
		do{?>
        <tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
        	<td><?=$row_entrada['id_entrada'];?></td>
            <td>R$ <?=number_format(str_replace(',','.',$row_entrada['valor']),2,',','.');?></td>
            <td><?=implode('/',array_reverse(explode('-',$row_entrada['data_vencimento']))); ?></td>
            <td>
            	
				<?php 
					$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_entrada[id_banco]' LIMIT 1");
					$row_banco = mysql_fetch_assoc($qr_banco);
					echo $row_banco['id_banco'] . ' - ' . utf8_encode($row_banco['nome']);
				?>
				
			</td>
            <td><?php 
            	// BLOQUEIO PARA EXCLUIR
            	if(in_array($_COOKIE['logado'],array(5,75,9))):?>
            	<a href="?id=<?php echo $row_entrada['id_entrada']; ?>&excluir=true&nota=<?php echo $id_nota; ?>" onclick="return window.confirm('Tem certeza que deseja excluir?');">excluir</a></td>
        		<?php endif; ?>
        	<td>
        		
        		<?php if($row_entrada['status_entrada'] == '1'){
        			echo "n&atilde;o confirmada.";
        		} elseif($row_entrada['status_entrada'] == '2'){
        			echo "confirmada.";
        		}?>
        		
        	</td>
        </tr>
       	<?php
			 $totalizador += (float) str_replace(',','.',$row_entrada['valor']);
			
		}while($row_entrada = mysql_fetch_assoc($qr_entrada));?>     
        <tr>
        	<td align="right">Total:</td>
            <td><?=number_format($totalizador,2,',','.');?></td>
        	<td></td>
            <td></td>
        </tr>   
    </table>
</body>
</html>