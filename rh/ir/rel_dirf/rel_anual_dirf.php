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


$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);

$qr_regiao2 = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_user[id_master]' ");
while($row_regioes = mysql_fetch_assoc($qr_regiao2)):

$regioes_master[] = $row_regioes['id_regiao'];

endwhile;

$regioes_master = implode(',', $regioes_master);


$regioes = $_POST['regioes'];

if($regioes == 'todos') {
		$regiao = "id_regiao IN($regioes_master)";
} else {
		$regiao = 'id_regiao = '.$regioes;	
}

}





?>
<html>
<head>
<title>Gerar IRRF</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../adm/css/estrutura.css" rel="stylesheet" type="text/css">
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

.tabelas{
margin-top:20px;	
border-collapse:collapse;
border: 1px solid #CECECE;	
}



.regiao{
width:775px;
height:auto;
text-decoration:none;
background-color: #CCC;
color:#333;
display:block;
margin-top:10px;
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
		
.nome{
height:40px;
margin-top:30px;
margin-bottom: 10px;
width:600px;
font-size:12px;
font-weight:bold;
border-collapse:collapse;
border:0;
	
}
.nome tr{
background-color:#F3F3F3;
color:   #666;
padding-left:10px;	
}

h3.regiao{
padding-top:10px;
height:30px;
background-color:  #D5EAFF;
border: 1px solid #BEBEBE;	
	
}


</style>
<style media="print">
.clt{ visibility:visible;}


.linha_um, .linha_dois{
	
border:0;
border-collapse:collapse;	
}

hr{ visibility:none; }
	

</style>
</head>
<body>
	<div id="corpo">
    	<div id="conteudo">
        
       
        
         <img src="../imagens/logo_ir.jpg" width="500" height="150">
             <h4>Relatório  DIRF <?php echo  $ano; ?></h4>        
             
                <?php
				///////////NOME DOS MESES
				$qr_meses = mysql_query("SELECT * FROM ano_meses");
				while($row_mes = mysql_fetch_assoc($qr_meses)):
				
					$meses[(int)$row_mes['num_mes']] = $row_mes['nome_mes'];
				endwhile;
				//////////////////////////
				
				
			  $cont=0;									
			  
			  //////////VERIFICA SE EXISTEM CLTS
			  $verifica_clt = mysql_num_rows(mysql_query("SELECT   rh_clt.cpf,  rh_clt.nome,  rh_clt.id_clt  FROM  rh_clt
									  INNER JOIN rh_folha_proc 
									  ON rh_clt.id_clt = rh_folha_proc.id_clt
									  WHERE rh_clt.$regiao AND rh_folha_proc.status = 3  AND rh_folha_proc.ano = '$ano' 
									   GROUP BY rh_clt.id_clt  "));	
									   
									   
				if($verifica_clt !=0) {					   
				
						
		                $qr_regiao = mysql_query("SELECT * FROM regioes  WHERE $regiao ORDER BY regiao") or die(mysql_error());                                        
		                while($row_regiao = mysql_fetch_assoc($qr_regiao)):
						
							  															
							  $qr_clt = mysql_query("SELECT   rh_clt.cpf,  rh_clt.nome,  rh_clt.id_clt, rh_clt.id_regiao, rh_clt.id_projeto  FROM  rh_clt
													  INNER JOIN rh_folha_proc 
													  ON rh_clt.id_clt = rh_folha_proc.id_clt
													  WHERE rh_clt.id_regiao ='$row_regiao[id_regiao]' AND rh_folha_proc.status = 3  AND rh_folha_proc.ano = '$ano'
													   GROUP BY rh_clt.id_clt ORDER BY rh_clt.nome ASC");
							  if(mysql_num_rows($qr_clt) ==0) continue;
							  					  
												  
							 // if($row_regiao['id_regiao'] != $regiao_anterior){ echo '<h3 class="regiao">'.$row_regiao['regiao'].'</h3>'; }							  
							  while($row_clt = mysql_fetch_assoc($qr_clt)):
							        
								   $cont++;
								   if($cont == 2){
									
									$style='style="tyle="page-break-after: always;"';   
									
								   } else {
								   
								   $style= '';
								   
								   }
								  
								  
								  
								   if($row_clt['id_clt'] != $clt_anterior) { 
								   ?>    
										
										<table   class="tabelas" <?php echo $style; ?>>
										 <tr class="nome">
										   <td width="22">CPF: </td>
										   <td width="130" align="left"> &nbsp;&nbsp;&nbsp;<?php echo $row_clt['cpf'];?> </td>
										   <td width="30" align="right">NOME: </td>
										   <td colspan="4" align="left">&nbsp;&nbsp;&nbsp;<?php echo $row_clt['nome'];?>  </td>
		                                   
										 </tr>
										
									
									<?php } ?>
									
		                            
		                             <tr class="titulo_tabela1" >
		                              <td>MÊS</td>
		                              <td>RENDIMENTOS</td>
		                              <td>PREVIDÊNCIA OFICIAL</td>
		                              <td>PREVIDÊNCIA PRIVADA</td>
		                              <td>DEPENDENTES (DDIR)</td>
		                              <td>PENSÃO ALIMENTICIA</td>
		                              <td>IMPOSTO RETIDO</td>
		                            </tr>
		                            
		                            <?php
									///PEGANDO OS DADOS DA FOLHA POR MES
									for($i=1; $i<=12;$i++) {
										
									$mes = sprintf('%02d',$i);	
									
									$qr_folha_proc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_clt = '$row_clt[id_clt]' AND mes = '$mes' AND ano = '$ano' AND status = 3");				 								
									$row_folha_proc = mysql_fetch_assoc($qr_folha_proc);
									
									
									if($j++ % 2 == 0){$class = 'linha_um';} else { $class = 'linha_dois'; }
									?>
		                            
			                            <tr class="<?php echo $class;?>" height="25">
			                                <td><?php echo $meses[(int)$mes]; ?></td>
		                                    <td><?php echo number_format($row_folha_proc['sallimpo'],2,',','.');?></td>
		                                    <td><?php echo number_format($row_folha_proc['inss'],2,',','.');?></td>
		                                    <td><?php echo number_format(0,2,',','.');?></td>
		                                    <td><?php echo number_format($row_folha_proc['a5049'],2,',','.');?></td>
		                                    <td><?php echo number_format(0,2,',','.');?></td>
		                                    <td><?php echo number_format($row_folha_proc['a5021'],2,',','.');?></td>
			                            </tr>
		                        	<?php 
											  
									}//mes
									
									
								////////////////////////////////////////////	
								////VERIFICANDO DÉCIMO TERCEIRO//////////////
								////////////////////////////////////////////
								$qr_folha = mysql_query("SELECT rh_folha_proc.*
														FROM rh_folha 
														INNER JOIN rh_folha_proc
														ON rh_folha.id_folha = rh_folha_proc.id_folha
														WHERE  rh_folha.ano = '$ano' 
														AND rh_folha.regiao = '$row_regiao[id_regiao]' 
														AND rh_folha.projeto = '$row_clt[id_projeto]' 
														AND rh_folha.terceiro = 1
														AND rh_folha_proc.id_clt = '$row_clt[id_clt]'
														AND rh_folha.status_reg = 1
														GROUP BY rh_folha.id_folha														
														");
														
									while($row_folha = mysql_fetch_assoc($qr_folha)):																			
										
										$sal_terceiro  += $row_folha['valor_dt'];	
										$inss_dt       += $row_folha['inss_dt'];
										$dependente_dt += $row_folha['a5049'];
										$irrf_dt 	   += $row_folha['a5021'];  										
										
										
									endwhile;									
									?>
									
									   <tr class="<?php echo $class;?>" height="25">
			                                <td> 13º </td>
		                                    <td><?php echo number_format($sal_terceiro,2,',','.');?></td>
		                                    <td><?php echo number_format($inss_dt,2,',','.');?></td>
		                                    <td><?php echo number_format(0,2,',','.');?></td>
		                                    <td><?php echo number_format($dependente_dt,2,',','.');?></td>
		                                    <td><?php echo number_format(0,2,',','.');?></td>
		                                    <td><?php echo number_format($irrf_dt,2,',','.');?></td>
			                            </tr>
									
												
									
								<?php
									unset($sal_terceiro, $inss_dt, $dependente_dt, $irrf_dt);
									
									
									////////////////////////////////////FIM DÉCIMO TERCEIRO									
									echo '</table>';
									
									
									
									$clt_anterior = $row_clt['id_clt'];									
							  endwhile;////FIM CLT
							  
							  
							 $regiao_anterior = $row_regiao['id_regiao']; 						
						endwhile;///REGIÃO       
						
						
				} else {
				
			
				
				$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE  $regiao"), 0);
				echo '<span>Sem trabalhadores em '.$ano.' na região '.$nome_regiao.'</span><br>';
					
				}
               
                ?>
</div>
</body>
</html>