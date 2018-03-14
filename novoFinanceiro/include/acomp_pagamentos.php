<?php
$array_status = array( 1 => 'SAIDAS A PAGAR', 2 => 'SAIDAS PAGAS');

foreach($array_status as $chave => $titulo) {
	
		$qr_saida = mysql_query("SELECT *,MONTH(data_vencimento) as mes, YEAR(data_vencimento) as ano  FROM saida WHERE  status = '$chave' AND id_regiao = '$regiao'  AND  juridico = 1 ORDER BY data_vencimento ASC");
		
		if(mysql_num_rows($qr_saida )!=0) {
			?>
			
			<h3 style="width: 120px; background-color:#E0E0E0; border-left:5px #909090 solid;  border-bottom:2px #909090 solid; padding:5px; font-weight:100;margin-bottom:0; font-size:13px; margin-left:3px;">
			<?php  echo $titulo; ?>
			</h3>		
			<table width="100%" class="tabela">


			<?php
			while($row_saida = mysql_fetch_assoc($qr_saida)):
				
				
				$mes 	  = sprintf('%02s',$row_saida['mes']);
				$nome_mes =  @mysql_result(mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'"),0);	
					
				$class = ($i++ % 2 == 0)? 'class="linha_um"': 'class="linha_dois"';
				
				if($mes != $mes_anterior) {
				 ?>
                  		
	                     <tr style= "background-color:#9F9F9F;  color:#FFFFFF;" height="20">
					 		<td colspan="7" align="center"> <?php echo $nome_mes.' / '.$row_saida['ano'];?></td>
					     </tr>
                         
	                   <tr class="titulo" height="30" style="font-size:12px;">
	                        <?php if($chave != 2){ ?> <td></td>    <?php }?>
	                        <td align="center">COD.</td>
	                        <td>NOME</td>
	                        <td align="center">DATA VENCIMENTO</td>
	                        <td align="center">VALOR</td>	                      
	                        <td align="center">STATUS</td>
	                        
	                    </tr>
				<?php
				}
				?>
				<tr <?php echo $class;?> height="20">      
	            
	            	<?php if($chave != 2){ ?>
		                 <td align="center"> 
		                	 <a href="view/editar.saida.naopaga.php?id=<?=$row_saida['id_saida']?>&tipo=saida"  onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><img src="image/editar.gif" width="16" height="16" border="0"></a>align="center"
		                 </td>   
	                 <?php }?>
	                      
					<td align="center"><?php echo $row_saida['id_saida'];?></td>
					<td>
						<?php echo $row_saida['nome']?>
	                	  <span style="display:none"><?=$row_saida['nome']?></span>
				            <a title="Detalhes" href="view/detalhes.saidas.php?ID=<?php echo $row_saida['id_saida']; ?>&tipo=saida" onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">
				            <img src="image/seta.gif" border="0" >
				            </a>
	                
	                </td>
					<td align="center"><?php echo implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])))?></td>
					<td align="center">R$ <?php echo $row_saida['valor']; ?></td>
					<td align="center"><img src="../imagens/bolha<?php echo $row_saida['status']?>.png" width="20" height="20"/></td>
				</tr>
				
				
				
				<?php
				
				$mes_anterior = $mes;
			endwhile;
		
		
		echo '</table>
		<div style="height:50px;"></div>
		';	
		}
}


?>


