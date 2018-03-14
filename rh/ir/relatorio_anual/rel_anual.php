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

}
?>
<html>
<head>
<title>Gerar IRRF</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../net1.css" rel="stylesheet" type="text/css" />
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../../jquery/jquery-1.4.2.min.js"></script>
<script src="../../../js/global.js"></script>
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
background-color:  #CCC;
color:#333;
display:block;
margin-top:5px;
font-size:12px;
text-transform:uppercase;
	
}

.clt{

background-color: #CCC;
color:#000;
font-size:12px;
}

.clt2 {
background-color:   #F3F3F3;
color:#000;
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
		height:60px;
		float:left;
		background-color:    #E4E4E4;
		padding:2px;
		}
		
.meses2{ 
		margin-top:10px;
		margin-left:3px;
		margin-bottom:5px;
		height:60px;
		float:left;
		background-color:  #E4E4E4;
		padding:2px;
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
             <h4>Relatório de <?php echo  $ano; ?></h4>     
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p>
             <table id="tbRelatorio" width="100%">
                <?php
                $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao != 36") or die(mysql_error());                                        
                while($row_regiao = mysql_fetch_assoc($qr_regiao)):
                  
                  $qr_folha = mysql_query("SELECT DISTINCT(id_clt), nome, id_projeto, nome FROM rh_folha 
                                            INNER JOIN rh_folha_proc 
                                            ON rh_folha.id_folha = rh_folha_proc.id_folha
                                            WHERE rh_folha.status = '3' AND rh_folha.regiao = '$row_regiao[id_regiao]' AND  rh_folha.ano = '$ano' ORDER BY rh_folha.projeto ASC") or die(mysql_error()); 
										
                    $numero_folha = mysql_num_rows($qr_folha);					
					if($numero_folha !=0) { 
					
						if($row_regiao['id_regiao'] != $regiao_anterior)  {  
						?>
                        	<tr>
                        		<td colspan="6">&nbsp;</td>
                            </tr>
                           <tr>
                           	<td td colspan="2" style="background-color:  #F8F8F8; padding-left:10px;"> <?php echo  $row_regiao['regiao']; ?></td>
                           </tr>
                         
                        
                                
                                    <?php
                                    while($row_folha = mysql_fetch_assoc($qr_folha)):	
									
									$class = ($i++ % 2 == 0) ? 'linha1' : 'linha2';
									
									//nome projeto	
									 if($row_folha['id_projeto'] != $projeto_anterior){ 
									 $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[id_projeto]'");
									 $row_projeto = mysql_fetch_assoc($qr_projeto); 
									  echo '<tr><td colspan="6">&nbsp;</td></tr>
									  		<tr  bgcolor="#EEDDDD" ><td colspan="6" style="padding-left:15px;">'.$row_projeto['id_projeto'].' - '.$row_projeto['nome'].'<td></tr>
											<tr><td colspan="6">&nbsp;</td></tr>	
											<tr style="font-size:12px; background-color:#CCC">
												<td align="center">CÓD.</td>
												<td align="center">NOME</td>
												<td align="center">CPF</td>
												<td align="center">TOTAL ANUAL</td>
												<td align="center">Nº DE DEPENDENTES</td>
											</tr>';
											
											
									  }
									  									  							                                   
									if($row_folha['id_clt'] != $clt_anterior) { 
									?>
                                      
                                         
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
												
												$i++;
												$class = ($i % 2) ? 'class="clt"' : 'class="clt2"' ;
												?>                                            
                                                <tr  <?php echo $class; ?>>
                                                	<td align="center"><?php echo $row_folha['id_clt']; ?></td>
                                                	<td > <?php echo $row_folha['nome']; ?> </td>
													<td align="center"><?php echo $row_clt['cpf']; ?></td>                                                   
                                                	
                                                <?php
												$qr_folha2 = mysql_query("SELECT SUM(a5021) FROM rh_folha 
																							INNER JOIN rh_folha_proc 
																							ON rh_folha.id_folha = rh_folha_proc.id_folha
																							WHERE rh_folha.status = '3' AND rh_folha.regiao = '$row_regiao[id_regiao]' AND  rh_folha.ano = '$ano' AND id_clt = '$row_folha[id_clt]' ORDER BY rh_folha.mes ASC") or die(mysql_error()); 											
													$total_ir = mysql_result($qr_folha2,0);										
													
																				
												?>
                                                <td align="center">   R$ <?php echo  number_format($total_ir,2,',','.'); ?> <br></td>                                              
                                                <td align="center">  <?php echo $numfilhos; ?></strong></td>	
                                                </tr>                                                         	                
                                    <?php
									
									unset($total_ir,$numfilhos);	
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