<?php 

include "sql.php";
$join_prestador = mysql_query($sql_moster);
include "config.php";

?>
<script type="text/javascript">
$(function(){
	
	var data_atual = new Date();
	var mes_atual = (data_atual.getMonth() + 1);
	var ano_atual = data_atual.getFullYear();

	var mes = mes_atual;

	function ajax(){
		//eval('var dados = '+$(this).attr('rel'));
		
		var acao = $(this).attr('rel');

		if(acao == 'voltar'){

			mes = mes - 1;
		}else{
			
			mes = mes + 1;
		}

		if(mes == 0){
			mes = 12;

			ano_atual = ano_atual -1;
		}

		if(mes == 13){
			mes = 1;

			ano_atual = ano_atual + 1;
		}

		var dados_envio = {
			'mes' : mes,
			'ano' : ano_atual
		}

		//console.log(dados_envio,acao);

			
		
		$.ajax({
			url : 'Prestador/json_pagamentos.php',
			cache : true,
			data : dados_envio,
			success : function(retorno){

				

				
				$('tr#nagevador').nextAll().remove();
				$(''+retorno+'').insertAfter($('#nagevador'));

				$('.content_data').html(dados_envio.mes + '/' + dados_envio.ano);
				
			},
			dataType : 'html'
		});
	}
		
	$('.navegar').click(ajax);
	$('.navegar').trigger('click'); 
	
});
</script>
<div>

		
		<table width="100%" class="tabela" style="font-size:12px;">
        	<tr>
           		 <td class="titulo_tabela" colspan="7">PRESTADOR DE SERVI&Ccedil;O</td>
            </tr>
        
			<tr id="nagevador" class="linha_dois">
				<td colspan="7" align="center"> 
					<span style="float: left;">	
						<a href="#" onclick="return false;" class="navegar" rel="voltar" > 
							<img src="Prestador/img/left.png" alt="">
						</a> 
					</span>
				
					<span class="content_data"><?php echo date('m/Y');?></span>
					
					<span  style="float: right;">
						<a href="#" onclick="return false;" class="navegar" rel="avancar" > 
							<img src="Prestador/img/right.png" alt="">
						</a>
					</span>
				</td>
			</tr>
			<?php
			while($dados = mysql_fetch_assoc($join_prestador) ):
			
			
			if($dados['bruto'] < 5000 and $dados['prestador_tipo']){
				continue;
			}
			
			if($dados['prestador_tipo'] == 7){
				continue;
			}
			
			?>
			<tr class="linha_<?php if($linha++%2==0) { echo 'dois'; } else { echo 'um'; } ?>" height="45px;">
				<td><?=$dados['id_prestador'] . ' - ' . $dados['c_fantasia']?></td>
				<td>
					<?= $dados['id_regiao'] . ' - ';?>
					<?php 
						
						$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '{$dados['id_regiao']}'");
						echo @mysql_result($qr_regiao,0);
					?>
				</td>
				<td>
					<?= $dados['id_projeto'] . ' - ';?>
					<?php 
						$qr_regiao = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '{$dados['id_projeto']}'");
						echo @mysql_result($qr_regiao,0);
					?>
				</td>
                <td>R$ <?=number_format($dados['bruto'],2,',','.'); ?></td>
				<td>R$ <?=number_format($dados['parcial'],2,',','.'); ?></td>
                 <?php 
					// REGRAS DE BOTÃO
					// 1 - DARF IRRF , 2 - DARF CSLL , 3 - GPS
					$botoes_pj = array('1','2','3');
					$botoes_adm = array('1','2','3');
					$botoes_pub = array('1','2','3');
					$botoes_pjcoop = array('1','3');
					$botoes_pjpres = array('1','2','3');
					$botoes_pf = array('4','3');
					
					switch ($dados['prestador_tipo']):
						case 1: 
							$botoes = $botoes_pj;
							break;
						case 2:
							$botoes = $botoes_pjcoop;
							break;
						case 3:
							$botoes = $botoes_pf;
							break;
						case 4:
							$botoes = $botoes_pjpres;
							break;
						case 5:
							$botoes = $botoes_adm;
							break;
						case 6:
							$botoes = $botoes_pub;
							break;
						default:
							$botoes = array('1','2','3');
							break;
					endswitch;
					
					
					if(!empty($dados['id_pagamento'])):
	
						$qr_pagamento = mysql_query("SELECT * FROM prestador_pagamento WHERE id_pagamento = '$dados[id_pagamento]'");
						$row_pagamento = mysql_fetch_assoc($qr_pagamento);
						
					endif;
				?>
                	
				<td>
					<?php //echo $dados['id_pagamento'] . '<--';?>
                	<?php if(in_array('1',$botoes)): ?>
	                	<?php // VERIFICANDO SE JA FOI GERADA SAIDAS 
	                			if($row_pagamento['darf_irrf'] != 0.00):
	                	?>
	                		Darf IRRF (<?php echo number_format($row_pagamento['darf_irrf'],2,',','.'); ?>)
	                	<?php else:?>
		                	<a href="Prestador/confirmacao.php?id_prestador=<?php echo $dados['id_prestador']; ?>&tipo=1&mes=<?=$mes_atual?>&ano=<?=$ano_atual?>&id_pagamento=<?php echo $dados['id_pagamento'];?>" 
		                    onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">Darf IRRF</a>
	                    <?php endif;?>
                   	<?php endif; ?>
                   	<?php if(in_array('4',$botoes)): ?>
                   		<?php // VERIFICANDO SE JA FOI GERADA SAIDAS 
	                			if($row_pagamento['irrf'] != 0.00):
	                	?>
	                		IRRF (<?php echo number_format($row_pagamento['irrf'],2,',','.'); ?>)
	                	<?php else:?>
		                	<a href="Prestador/confirmacao.php?id_prestador=<?php echo $dados['id_prestador']; ?>&tipo=4&mes=<?=$mes_atual?>&ano=<?=$ano_atual?>&id_pagamento=<?php echo $dados['id_pagamento'];?>" 
		                    onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">IRRF</a>
		                <?php endif;?>
                   	<?php endif; ?>
               
                </td>
				<td>
                	<?php if(in_array('2',$botoes)): ?>
                		<?php // VERIFICANDO SE JA FOI GERADA SAIDAS 
	                			if($row_pagamento['darf_csll'] != 0.00):
	                	?>
	                		Darf CSLL (<?php echo number_format($row_pagamento['darf_csll'],2,',','.'); ?>)
	                	<?php else:?>
		                	<a href="Prestador/confirmacao.php?id_prestador=<?php echo $dados['id_prestador']; ?>&tipo=2&mes=<?=$mes_atual?>&ano=<?=$ano_atual?>&id_pagamento=<?php echo $dados['id_pagamento'];?>" 
		                    onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">Darf CSLL</a>
		                <?php endif;?>
                    <?php endif;?>
                </td>
				<td>
                	<?php if(in_array('3',$botoes) || in_array('4',$botoes)): ?>
                		<?php // VERIFICANDO SE JA FOI GERADA SAIDAS
                		
	                			if($row_pagamento['gps'] != 0.00):
	                	?>
	                		GPS (<?php echo number_format($row_pagamento['gps'],2,',','.'); ?>)
	                	<?php else:?>
		                	<a href="Prestador/confirmacao.php?id_prestador=<?php echo $dados['id_prestador']; ?>&tipo=3&mes=<?=$mes_atual?>&ano=<?=$ano_atual?>&id_pagamento=<?php echo $dados['id_pagamento'];?>" 
		                    onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )">GPS</a>
                    	<?php endif;?>
                    <?php endif; ?>
                </td>
                
			</tr>
			<?php 

			endwhile;?>
		</table>
		

	
</div>