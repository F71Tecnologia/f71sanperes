<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$charset = mysql_set_charset('utf8');
$id_banco = $_REQUEST['banco'];
$regiao = $_REQUEST['regiao'];
$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$id_banco'");
$row_banco = mysql_fetch_assoc($qr_banco);

$qr_saidas_hoje = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento =  CURDATE()
								AND id_banco = '$id_banco'
								");
$qr_saidas_venciada = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento < CURDATE()
								AND data_vencimento != '0000-00-00'
								AND YEAR(data_vencimento) = '".date('Y')."'
								AND id_banco = '$id_banco'
								");
$qr_saidas_futuras = mysql_query("SELECT * FROM saida
								WHERE id_regiao =  '$regiao'
								AND STATUS =  '1'
								AND data_vencimento > CURDATE()
								AND id_banco = '$id_banco'");
								
$num_saidas_hoje = mysql_num_rows($qr_saidas_hoje);
$num_saidas_vencidas = mysql_num_rows($qr_saidas_venciada);
$num_saidas_futuras = mysql_num_rows($qr_saidas_futuras);
								
?>
<?php if(empty($num_saidas_hoje) && empty($num_saidas_vencidas) && empty($num_saidas_futuras)){
		echo "<center>Nenhuma saida encontrada.</center>";
		exit;
	   }	
 ?>

<table width="100%">
	<tr>
    	<td></td>
        <td>Cod.</td>
        <td>Nome</td>
        <td>Data vencimento</td>
        <td>Valor</td>
        <td>Pagar</td>
        <td>Deletar</td>
  	</tr>
    <?php if(!empty($num_saidas_hoje)): ?>
    <tr>
    	<td colspan="7">
        	<div class="divisor">
            	SAIDAS DE HOJE
            </div>
        </td>
    </tr>
	<?php while($row_saida_hoje = mysql_fetch_assoc($qr_saidas_hoje)):?>
    <?php 
		$saida_valor  = (float) str_replace(',','.',$row_saida_hoje['valor']);
		$saida_adicional  = (float) str_replace(',','.',$row_saida_hoje['adicional']);
		$Total = $saida_valor + $saida_adicional;
		$totalizador += $Total;
	?>
    <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
    	<td><input type="checkbox" class="saidas_check" /></td>
        <td><?=$row_saida_hoje['id_saida']?></td>
        <td><?=$row_saida_hoje['nome']?></td>
        <td><?=$row_saida_hoje['data_vencimento']?></td>
        <td>R$ <?=number_format($Total,2,',','.')?></td>
        <td><img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0"></td>
        <td><img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" ></td>
    </tr>
    <?php endwhile;?>
    <tr>
    	<td colspan="4" align="right">Total hoje: </td>
        <td colspan="3">R$ <?=number_format($totalizador,2,',','.')?></td>
    </tr>
    <?php		
		$totalizador_geral += $totalizador;
		unset($totalizador);
	?>
    <?php endif;?>
    <?php if(!empty($num_saidas_vencidas)): ?>
    <tr>
    	<td colspan="7">
        	<div class="divisor">
            	SAIDAS VENCIDAS
            </div>
        </td>
    </tr>
    <?php while($row_saida_venciada = mysql_fetch_assoc($qr_saidas_venciada)):?>
    <?php 
		$saida_valor  = (float) str_replace(',','.',$row_saida_venciada['valor']);
		$saida_adicional  = (float) str_replace(',','.',$row_saida_venciada['adicional']);
		$Total = $saida_valor + $saida_adicional;
		$totalizador += $Total;
	?>
    <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
    	<td><input type="checkbox" class="saidas_check" /></td>
        <td><?=$row_saida_venciada['id_saida']?></td>
        <td><?=$row_saida_venciada['nome']?></td>
        <td><?=$row_saida_venciada['data_vencimento']?></td>
        <td>R$ <?=number_format($Total,2,',','.')?></td>
       	<td><img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0"></td>
        <td><img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" ></td>
    </tr>
    <?php endwhile;?>
     
    <tr>
    	<td colspan="4" align="right">Total hoje: </td>
        <td colspan="3">R$ <?=number_format($totalizador,2,',','.')?></td>
    </tr>
    
    <?php		
		$totalizador_geral += $totalizador;
		unset($totalizador);
	?>
    <?php endif;?>
    <?php if(!empty($num_saidas_futuras)):?>
    <tr>
    	<td colspan="7">
        	<div class="divisor">
            	SAIDAS DE FUTURAS
            </div>
        </td>
    </tr>
    <?php while($row_saida_futuras = mysql_fetch_assoc($qr_saidas_futuras)):?>
    <?php 
		$saida_valor  = (float) str_replace(',','.',$row_saida_futuras['valor']);
		$saida_adicional  = (float) str_replace(',','.',$row_saida_futuras['adicional']);
		$Total = $saida_valor + $saida_adicional;
		$totalizador += $Total;
	?>
    <tr class="<? if($alternateColor++%2==0) { ?>linha_um<? } else { ?>linha_dois<? } ?>">
    	<td><input type="checkbox" class="saidas_check" /></td>
        <td><?=$row_saida_futuras['id_saida']?></td>
        <td><?=$row_saida_futuras['nome']?></td>
        <td><?=$row_saida_futuras['data_vencimento']?></td>
        <td>R$ <?=number_format($Total,2,',','.')?></td>
        <td><img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0"></td>
        <td><img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" ></td>
    </tr>
    <?php endwhile;?>
     
    <tr>
    	<td colspan="4" align="right">Total Futuras: </td>
        <td colspan="3">R$ <?=number_format($totalizador,2,',','.')?></td>
    </tr>
    <?php		
		$totalizador_geral += $totalizador;
		unset($totalizador);
	?>
    <?php endif;?>
    <tr>
    	<td colspan="4" align="right">Total FInal: </td>
        <td colspan="3">R$ <?=number_format($totalizador_geral,2,',','.')?></td>
    </tr>
</table>