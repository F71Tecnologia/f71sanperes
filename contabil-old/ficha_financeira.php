<?php 


include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include "include/criptografia.php";


$id_user   = $_COOKIE['logado'];
$regiao	 = $_GET['regiao'];
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);

$id_master         = $row_funcionario['id_master'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o Cont&aacute;bil</title> 
<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../jquery-1.3.2.js"></script> 
<script type="text/javascript"> 
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
$(document).ready(function(){
    $('.show').click(function() {
		$('.show').not(this).removeClass('seta_aberto');
	  	$('.show').not(this).addClass('seta_fechado');
		
		if($(this).attr('class')=='show seta_aberto') {
			$(this).removeClass('seta_aberto');
			$(this).addClass('seta_fechado');
		} else {
			$(this).removeClass('seta_fechado');
			$(this).addClass('seta_aberto');
		}

		$('.show').not(this).next().hide();
		$(this).next().css({'width':'100%'}).slideToggle('fast');
    });

	$('.azul').parent().css({'background-color': '#e2edfe'});
	$('.vermelho').parent().css({'background-color': '#ffb8c0'});
	
});	
</script>
<link rel="stylesheet" type="text/css" href="novoFinanceiro/style/form.css"/>

</head>
<body>
<div id="corpo">
<div id="menu" class="ficha_financeira">
	<?php include('include/menu_financeira.php'); ?>

</div>
		<div id="conteudo">
        
        <?php
	for($i=1;$i>=0; $i--){	
		
		if($i==0){
			echo '<h1>REGIÕES INATIVAS</h1>';
			$qr_regioes = mysql_query("SELECT * FROM regioes WHERE  id_master = '$id_master' AND status = '0' OR status_reg = '0' ORDER BY regiao")or die (mysql_error());
        	
		} else {
			echo '<h1>REGIÕES ATIVAS</h1>';
			$qr_regioes = mysql_query("SELECT * FROM regioes WHERE  id_master = '$id_master' AND status = '1' AND status_reg = '1' ORDER BY regiao")or die (mysql_error());
		
		}
		
		while($row_regiao = mysql_fetch_assoc($qr_regioes)):
				//BLOQUEIO REGIÃO SISTEMA
				if($row_regiao['id_regiao'] ==36) continue;
			 $ordem++;
			
			
			
			
			
			//VERIFICA SE EXISTEM SAIDAS
				$qr_projeto = mysql_query("SELECT projeto.nome,projeto.id_projeto, projeto.id_regiao  FROM projeto WHERE id_regiao = '$row_regiao[id_regiao]' AND status_reg = '$i'");
					
						$row_projeto = mysql_fetch_assoc($qr_projeto);
						
													 
						$query_pg_total = mysql_query("SELECT saida.id_saida, saida.data_vencimento, saida.valor, saida.status, saida.data_pg, saida.id_regiao, saida.id_projeto, prestador_pg.id_pg, prestador_pg.gerado, 
														prestador_pg.comprovante, prestador_pg.id_prestador, saida.comprovante,
														prestadorservico.c_razao
														FROM prestador_pg
														INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida
														INNER JOIN prestadorservico ON  prestador_pg.id_prestador = prestadorservico.id_prestador  
														WHERE prestador_pg.status_reg = '1'
														AND prestadorservico.id_projeto = '$row_projeto[id_projeto]'
														AND prestador_pg.id_regiao = '$row_regiao[id_regiao]'
														AND saida.status != '0'
														ORDER BY YEAR( saida.data_pg ), MONTH( saida.data_pg ),prestador_pg.id_prestador  ASC");
							 
												$linha = mysql_num_rows($query_pg_total);	
												if($linha ==0) continue;
													
              ?>
                   
              <a class="show <?php if($_GET['aberto'] == $ordem) { echo 'seta_aberto'; } else { echo 'seta_fechado'; } ?>"  id="<?=$ordem?>" href=".<?=$ordem?>" onClick="return false">
                  <span style="text-transform:uppercase">  <?=$row_regiao['regiao']?></span>
              </a>

    		  <div class="<?=$ordem?>" style="width:100%; <?php if($_GET['aberto'] != $ordem) { echo 'display:none;'; } ?>">
                       
                       <table  width="100%" style="width:100%; margin-bottom:50px;" cellspacing="1" cellpadding="4" class="relacao">    
              
              <?php
			  
						$qr_projeto = mysql_query("SELECT projeto.nome,projeto.id_projeto, projeto.id_regiao  FROM projeto WHERE id_regiao = '$row_regiao[id_regiao]' AND status_reg = '$i'");
					
						while($row_projeto = mysql_fetch_assoc($qr_projeto)):
						
													 
						$query_pg_total = mysql_query("SELECT saida.id_saida, saida.data_vencimento, saida.valor, saida.status, saida.data_pg, saida.id_regiao, saida.id_projeto, prestador_pg.id_pg, prestador_pg.gerado, 
														prestador_pg.comprovante, prestador_pg.id_prestador, saida.comprovante,
														prestadorservico.c_razao
														FROM prestador_pg
														INNER JOIN saida ON prestador_pg.id_saida = saida.id_saida
														INNER JOIN prestadorservico ON  prestador_pg.id_prestador = prestadorservico.id_prestador  
														WHERE prestador_pg.status_reg = '1'
														AND prestadorservico.id_projeto = '$row_projeto[id_projeto]'
														AND prestador_pg.id_regiao = '$row_regiao[id_regiao]'
														AND saida.status != '0'
														ORDER BY YEAR( saida.data_pg ), MONTH( saida.data_pg ),prestador_pg.id_prestador  ASC");

							 
							 
							 


							 
												$linha = mysql_num_rows($query_pg_total);	
												
												
												if(empty($linha)) continue;
												
												   ?>
                     	<tr>
                             <td align="center" colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td colspan="4"><span style="text-align:center; font-size:12px;text-transform:uppercase;"><?php echo $row_projeto['id_projeto'].' - '.$row_projeto['nome']?></span></td>
                        	
                        </tr>
												
							<?php			
							
										for($result=0;$result<$linha;$result++):
												
											
												
													$row_pg_total = mysql_fetch_assoc($query_pg_total);
																																		
												$cont++;
												
													$ano_saida = substr($row_pg_total['data_pg'],0,4);
													$razao = $row_pg_total['c_razao'];
													
													
														 
												    $meses = array(1 => 'Janeiro', 2=> 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho', 07 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
													if(substr($row_pg_total['data_pg'],5,2) < 10 ) {
														
													  $mes_saida =  str_replace('0','',substr($row_pg_total['data_pg'],5,2));
													} else {
														
														$mes_saida =  substr($row_pg_total['data_pg'],5,2);
													}
													
													
													
													$ids_prestador[] = $row_pg_total['id_prestador'];
													
														
												if($ano_anterior != $ano_saida):	
													
													
														if(!empty($total_mes)){
															
															echo'<tr>
																<td  align="right">&nbsp;</td>
																<td  align="right" colspan="2">Total do ano:</td>
																<td align="center" colspan="2">'.'R$ '.number_format($total_mes,'2',',','.').'</td>																										
															</tr>';	
														
															
															unset($total_mes);
															
														}
													?>		<tr >
																 <td align="center" colspan="4" style="font-size:12px; ">&nbsp;</td>
															</tr>
															
															<tr class="novo2">
																 <td align="center" colspan="4" style="font-size:12px;font-weight:bold; "><?php echo $ano_saida;?></td>
															</tr>
                                                          	</tr>
															
															
														  
															
														 <?php	
														 
														 //pega os anos para mostrar no campo select
														 $anos[]=$ano_saida;
														 
                                                         endif;  
														 
														 if($mes_saida_anterior != $mes_saida) {
														
														 
															 echo '				 
														
															 <tr>
																 <td align="center" colspan="4" style="font-size:12px;text-align:left;background-color:#E6E6E6; color:#000; margin-top:10px; text-transform:uppercase;">'.$meses[$mes_saida].' ('.$ano_saida.')</td>
														  <tr class="secao">
																<td align="center">Prestador de serviço</td>
																<td width="80" align="center">Saída</td>
																<td>Data de pagamento</td>
																<td>Valor</td>
															</tr>
															
															';
														 
														 
														 }
														 ?>
                                                 
													<tr class="linha_dois">
														<td  align="left"><?php echo $row_pg_total['c_razao']; ?></td>
														<td align="center"><?php echo $row_pg_total['id_saida']; ?></td>
														<td align="center"><?php echo implode('/',array_reverse(explode('-',$row_pg_total['data_pg']))); ?></td>
														<td align="center"><?php echo 'R$ '.number_format(str_replace(',','.',$row_pg_total['valor']),2,',','.'); ?></td>													
													</tr>
											  <?php
											  
											 
											  
											 $total_projeto += str_replace(',','.',$row_pg_total['valor']);
											 $total_mes += str_replace(',','.',$row_pg_total['valor']); 
											$ano_anterior = $ano_saida;
											$mes_saida_anterior = $mes_saida;
							  
							  			
                              		
										
										endfor;//FIM PRESTADOR
										
										
										//ids das cooperativas
								$ids = implode(',',array_unique($ids_prestador));
										
									
										
											?>
															<tr >
																<td  align="right">&nbsp;</td>
																<td  align="right" colspan="2">Total do ano:</td>
																<td align="center" colspan="2">R$ <?php echo number_format($total_mes,'2',',','.'); ?></td>																										
															</tr>
															
															
															<tr >
																<td  align="right">&nbsp;</td>
																<td  align="right" colspan="2">Total do projeto:</td>
																<td align="center" colspan="2">R$ <?php echo number_format($total_projeto,'2',',','.'); ?></td>																										
															</tr>
															
															<tr >
																<td  align="right">&nbsp;</td>
																<td  align="right" colspan="2">Imprimir:</td>
																<td align="center" colspan="2" width="180">
                                                                	<form action="imprime_ficha.php" method="post">
                                                                
                                                                <select name="ano" >
                                                                <option value=""> Selecione o ano...</option>
                                                                <?php
																	foreach($anos as $chave => $valor):
																?>
																	<option value="<?php echo $valor;?>"><?php echo $valor;?></option>
                                                              	<?php																	
																	endforeach;
																
																?>
                                                                
                                                                </select>
                                                                
                                                               
                                                                
                                                                
                                                                <input type="submit" value="OK"/>
                                                                
                                                                 <input type="hidden" name = "projeto" value="<?php echo $row_projeto['id_projeto']; ?>"/>
                                                                 <input type="hidden" name = "prestador" value="<?php echo $row_pg_total['id_prestador']; ?>"/>
                                                                  <input type="hidden" name = "regiao" value="<?php echo $row_regiao['id_regiao']; ?>"/>
                                                                  <input type="hidden" name = "ids" value="<?php echo $ids; ?>"/>
                                                                  
                                                                </form>
                                                                </td>																										
															</tr>
															
															
														<?php						
															
															unset($total_projeto,$total_projeto,$mes_saida_anterior,$ano_saida,$mes_saida,$anos,$total_mes,$ano_anterior);
															
															
															
															unset($ids_prestador);
														
												
													
															
						endwhile;// FIM PROJETO
		?>
		</table>
         </div>
        <?php
		endwhile;// FIM REGIÕES
	}
		?>
        
        
        
       <div id="rodape">
        <?php include('include/rodape.php'); ?>
    </div>
      		       
       
        
   </div>
</div>
</body>
</html>