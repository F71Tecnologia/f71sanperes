<?php 
if(empty($_COOKIE['logado'])){
print "<script>location.href = '../../login.php?entre=true';</script>";
} else {
include "../../../conn.php";
include "../../../classes/funcionario.php";
$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;

$ano = $_POST['ano'];

$regioes = $_POST['regioes'];

if($regioes == 'todos') {
		$regiao = 'id_regiao != 36';
} else {
		$regiao = 'id_regiao = '.$regioes;	
}

}


?>
<html>
<head>
<title>Gerar IRRF</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../../jquery/jquery-1.4.2.min.js"></script>
<script>
$(function(){
	$('.regiao').click(function(){
		
		var div = $(this).next();
			
		if(div.css('display') == 'block') 
		{
			div.css('display', 'none');
			return false;
			
		} else {
			
		$('.dados').css('display', 'none');
		div.css('display', 'block');
		return false;
			
		}			
	});
	
	$('.clt').click(function(){
		
		var div = $(this).next();
		var parametros = new Array();
		parametros = div.attr('rel').split(',');
		
			
		if(div.css('display') == 'block') 	{ div.css('display', 'none'); return false; }
		else {
			
			$('.folha_mensal').css('display', 'none');			
			$.ajax({
				url: 'action.clts.php?regiao='+parametros[0]+'&id_clt='+parametros[1]+'&ano='+parametros[2],
				success: function(resposta){
							div.html(resposta);
						}
				});
			
			div.css('display', 'block');
			div.css('font-size', '10');
			
			return false;
		}		
	
		
	});	
	
	
});
</script>
<style>
body{
	background-color:#EAEAEA;
	margin:0 auto;
	text-align:center;
	font-family:Tahoma, Geneva, sans-serif
}

#corpo {
	width:800px;
	margin:0 auto;
	text-align:center;
}	
#conteudo {
	background-color: #FFF;

}
.regiao{
width:775px;
height:auto;
text-decoration:none;
background-color: #CCC;
color:#333;
display:block;
margin-top:5px;
font-size:12px;
text-transform:uppercase;
	
}

.clt{
width:775px;
height:auto;
text-decoration:none;
background-color: transparent;
color:#000;
display:block;
margin-top:5px;	
font-size:12px;
}

.folha_mensal{
	font-size:12px;
}

.linha1{ background-color:  #F3F3F3;}
.linha2{ background-color:  #F3F3F3;}

.meses1{ 
		margin-top:10px;
		margin-left:3px;
		margin-bottom:5px;
		width:auto;
		height:110px;
		float:left;
		background-color:    #E4E4E4;
		padding:5px;
		border:1px solid #999;
		
		}
		
.meses2{ 
		margin-top:10px;
		margin-left:3px;
		margin-bottom:5px;
		height:110px;
		float:left;
		background-color:  #E4E4E4;
		padding: 5px;
		border:1px solid #999;
		
}		

</style>
<style media="print">
.clt{ visibility:visible;}

</style>
</head>
<body>
	<div id="corpo">
    	<div id="conteudo">
         <img src="../imagens/logo_ir.jpg" width="500" height="150">
             <h4>Relatório  DIRF <?php echo  $ano; ?></h4>        
               <table width="100%">
                <?php
				
                $qr_regiao = mysql_query("SELECT * FROM regioes WHERE  $regiao  ") or die(mysql_error());                                        
                while($row_regiao = mysql_fetch_assoc($qr_regiao)):
                  
                  $qr_folha = mysql_query("SELECT DISTINCT(id_clt), nome, id_projeto, nome FROM rh_folha 
                                            INNER JOIN rh_folha_proc 
                                            ON rh_folha.id_folha = rh_folha_proc.id_folha
                                            WHERE rh_folha.status = '3' AND rh_folha.regiao = '$row_regiao[id_regiao]' AND  rh_folha.ano = '$ano' AND rh_folha_proc.a5021 != '0.00' ORDER BY rh_folha.projeto ASC") or die(mysql_error()); 
										
                    $numero_folha = mysql_num_rows($qr_folha);	
					if($regioes != 'todos' and $numero_folha == 0) { echo 'Nenhum trabalhador com desconto de IR pra esta região';}
					
									
					if($numero_folha !=0) { 
					
						if($row_regiao['id_regiao'] != $regiao_anterior)  {  
						?>
                           <tr>
                           	<td><?php echo  $row_regiao['regiao']; ?></td>
                           </tr>
                         
                        
                                
                                    <?php
                                    while($row_folha = mysql_fetch_assoc($qr_folha)):	
									
									$class = ($i++ % 2 == 0) ? 'linha1' : 'linha2';
									
									//nome projeto	
									 if($row_folha['id_projeto'] != $projeto_anterior){ 
									 $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[id_projeto]'");
									 $row_projeto = mysql_fetch_assoc($qr_projeto); 
									  echo '<tr><td>&nbsp;</td></tr>
									  		<tr  bgcolor="#EEDDDD"><td>'.$row_projeto['id_projeto'].' - '.$row_projeto['nome'].'<td></tr>
											<tr><td>&nbsp;</td></tr>';	
									  }
									  									  							                                   
									if($row_folha['id_clt'] != $clt_anterior) { 
									?>
                                        <tr class="<?php echo $class; ?>">
                                         <td> 
                                         
                                         
                                         		<?php
                                                $qr_filhos = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$row_folha[id_clt]' AND id_regiao = '$row_regiao[id_regiao]' AND contratacao = '2'");
													$filhos = mysql_fetch_assoc($qr_filhos);
													
													$numfilhos = 0;
													if(!empty($filhos['nome5'])) {
														$numfilhos = $numfilhos + 1;
													}
													if(!empty($filhos['nome4'])) {
															$numfilhos = $numfilhos + 1;
													}
													if(!empty($filhos['nome3'])) {
															$numfilhos = $numfilhos + 1;
													}
													if(!empty($filhos['nome2'])) {
															$numfilhos = $numfilhos + 1;
													} 
													if(!empty($filhos['nome1'])) {
															$numfilhos = $numfilhos + 1;
													}					
												
                                                $qr_clt = mysql_query("SELECT cpf FROM rh_clt WHERE id_clt = '$row_folha[id_clt]'");
												$row_clt = mysql_fetch_assoc($qr_clt);
												
												
												
												
												?>
                                                <a href="#" class="clt" ><?php echo '<b> ('.$row_folha['id_clt'].') '.$row_folha['nome'].' CPF: '.$row_clt['cpf'].'</b>';?> </a> 
                                                
                                                
                                                
                                                
                                              	<table width="100%" style="font-size:12px;">
                                                <tr>
                                                	<td>
                                                <?php
                                                $meses = array(01 => 'Janeiro', 02 => 'Fevereiro', 03 => 'Março', 04 => 'Abril', 05 => 'Maio', 06 => 'Junho', 07 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro');
												
												$qr_folha2 = mysql_query("SELECT * FROM rh_folha 
																							INNER JOIN rh_folha_proc 
																							ON rh_folha.id_folha = rh_folha_proc.id_folha
																							WHERE rh_folha.status = '3' AND rh_folha.regiao = '$row_regiao[id_regiao]' AND  rh_folha.ano = '$ano' AND id_clt = '$row_folha[id_clt]' ORDER BY rh_folha.mes ASC") or die(mysql_error()); 
												
												
												
													while($row_folha2 = mysql_fetch_assoc($qr_folha2)):
													
													$j++;
													$classe = ($j % 2) ? 'class="meses1"' : 'class="meses2"';
													
													
													
														if($row_folha2['ferias'] == '1') {
												
														$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_folha2[id_clt]' AND YEAR(data_ini) = '$ano' AND rh_ferias.mes = '$row_folha2[mes]'");
														$ferias = mysql_fetch_assoc($qr_ferias);
														
														}
																										
														
														$mes = (int)$row_folha2['mes'];
														echo '<div '.$classe.'>';
														echo '<table style="font-size:12px; margin-top:5px;"> ';
														echo '<tr> <td> <b>'.$meses[$mes].'</b> </td> <td> ('.$row_folha2['id_folha'].')  </td> </tr>';
														echo '<tr> <td> INSS: </td> <td> <b> R$ '.number_format($row_folha2['a5020'],2,',','.').'</b> </td> </tr>';
														echo '<tr> <td> Salário: </td> <td> <b> R$ '.number_format($row_folha2['sallimpo_real'],2,',','.').'</b> </td> </tr>';
														echo '<tr> <td> IR: </td> <td> <b> R$ '.number_format($row_folha2['a5021'],2,',','.').'</b> </td> </tr>';
														echo '<tr> <td> BASE IR:  </td> <td> <b>R$ '.number_format( $row_folha2['base_irrf'],2,',','.').'</b>';
														
														
														if($row_folha2['ferias'] == '1') {
															echo '<tr> <td> FÉRIAS: </td> <td> <b>R$ '.number_format($ferias['total_remuneracoes'] - $ferias['inss'], 2, ",", ".").'</b> </td> </tr>';
														}		
														
														echo '</table>';												
														echo '</div>';
														
														$total_ir += 	$row_folha2['a5021'];
													endwhile;
													
												echo '<div class="meses1">
													 <strong> Total anual do IR: R$ '.number_format($total_ir,2,',','.').'</strong>	<br>
													<strong> Nº de dependentes: '.$numfilhos.'</strong>
													 											
													  </div>';
														
												unset($total_ir,$numfilhos);												
												?>
                                               
                                                
                                                	</td>
                                                </tr>
                                                </table>
                                         </td>
                                         </tr>                                                                       	                
                                    <?php
									}
									$projeto_anterior = $row_folha['id_projeto'];									
									$clt_anterior = $row_folha['id_clt'];									
                                    endwhile;
                                    ?>                            
                              
                            </div>
                        <?php					
						}
					}					
				
                $regiao_anterior = $row_regiao['id_regiao'];               
                endwhile;
                ?>
         </div>
</div>
</body>
</html>