<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = 1");
$row_empresa = mysql_fetch_assoc($qr_empresa);


$ano = $_POST['ano'];?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>:: Intranet :: Editar de Plano de contas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>

<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 

<style>

table tr td{ font-size:10px;  }
table tr td {height:10px;}

</style>

</head>
<body>
<div id="corpo">

<div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;"> 
                GERAR <span class="projeto"> PLANO DE CONTAS</span>
           </h2> 
           <p style="float:right;margin-top:40px;">
               <a href="../index.php?regiao=<?php echo $regiao;?>">&laquo; Voltar</a>
           </p>
           
           <p style="float:right;margin-left:15px;background-color:transparent;">
               <?php include('../../reportar_erro.php'); ?>   		
           </p>
           <div class="clear"></div>
           </div>



<table width="100%">
<tr>
	<td>Empresa:</td>
    <td><?php echo $row_empresa['razao']; ?></td>
	<td>Folha:</td>
    <td></td>
</tr>
<tr>
	<td>CNPJ:</td>
    <td><?php echo $row_empresa['cnpj']; ?></td>
    <td align="right">Emissão:</td>
    <td></td><?php date('d/m/Y'); ?></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td>Hora:</td>
    <td><?php echo date('H:m:ss'); ?></td>
</tr>
<tr>
	<td colspan="4"  style="text-align:center; font-weight:bold;">PLANO DE CONTAS</td>
</tr>

</table>



<table width="100%" >
<tr>	
	<td style="background-color:#BBB;">CÓDIGO</td>
    <td style="background-color:#BBB;">T</td>
     <td style="background-color:#BBB;">CLASSIFICAÇÃO</td>
    <td style="background-color:#BBB;">NOME</td> 
    <td style="background-color:#BBB;">GRAU</td>   
</tr>

<?php 
//////////////////////////////////////////GRUPO
$qr_grupo = mysql_query("SELECT * FROM c_grupos WHERE c_grupo_status = '1' ") or die(mysql_error());				
while($row_grupo = mysql_fetch_assoc($qr_grupo)):
		
		$grupo_classificacao += $contador_classificacao + 1;		
		$grupo_id 			 = $row_grupo['c_grupo_id'];
		$grupo_T 			 = $row_grupo['c_grupo_T'];		
		$grupo_nome		   	 = $row_grupo['c_grupo_nome'];
		$grupo_grau		 	 = $row_grupo['c_grupo_grau'];

		if($grupo_id != $grupo_anterior){  ?>
    
            <tr>
                <td><?php echo $grupo_id; ?></td>
                <td><?php echo $grupo_T; ?></td>
                <td><?php echo $grupo_classificacao; ?></td>
                <td><strong><?php echo $grupo_nome; ?></strong></td>
                <td><?php echo $grupo_grau?>  </td>
            </tr>
        
        <?php } 

		/////////////////////////////////// SUBGRUPO
		$qr_subgrupo = mysql_query("SELECT * FROM c_subgrupos WHERE c_subgrupo_status = 1 AND c_grupo_id = '$grupo_id'");
		while($row_subgrupo = mysql_fetch_assoc($qr_subgrupo)):
				
				
				$subgrupo_classificacao = $subgrupo_classificacao + 1;
				
				$subgrupo_id 		 			 = $row_subgrupo['c_subgrupo_id'];
				$subgrupo_T		 			     = $row_subgrupo['c_subgrupo_T'];
				$subgrupo_nome			    	 = $row_subgrupo['c_subgrupo_nome'];
				$subgrupo_grau					 = $row_subgrupo['c_subgrupo_grau'];
		
				if($subgrupo_id != $subgrupo_anterior){  ?>
                    <tr>
                        <td><?php echo $subgrupo_id; ?></td>
                        <td><?php echo $subgrupo_T; ?></td>
                        <td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao; ?></td>
                        <td><strong><?php echo $subgrupo_nome; ?></strong></td>
                        <td><?php echo $subgrupo_grau; ?></td>
                    </tr>
                <?php } 
		
		
		
				///////////////////TIPO
				$qr_tipo = mysql_query("SELECT * FROM c_tipos WHERE c_tipo_status = '1' AND c_subgrupo_id = '$subgrupo_id'");
				while(($row_tipo = mysql_fetch_assoc($qr_tipo)) ):
				
				
						$tipo_classificacao = $tipo_classificacao + 1;
						
						$tipo_id						= $row_tipo['c_tipo_id'];
						$tipo_T				    		= $row_tipo['c_tipo_T'];
						$tipo_nome						= $row_tipo['c_tipo_nome'];
						$tipo_grau						= $row_tipo['c_tipo_grau'];
						
						if($tipo_id != $tipo_anterior){
							?>
							<tr>
								<td><?php echo $tipo_id; ?></td>
								<td><?php echo $tipo_T; ?></td>
								<td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao; ?></td>
								<td><strong><?php echo $tipo_nome; ?></strong></td>
								<td><?php echo $tipo_grau; ?></td>
							</tr>
							<?php }
						
						
						
						//////////////////////////////////////////////// SUBTIPO
						$qr_subtipo = mysql_query("SELECT * FROM  c_subtipos WHERE c_subtipo_status = 1 AND c_tipo_id = '$tipo_id'");
						while($row_subtipo = mysql_fetch_assoc($qr_subtipo)):
								
								
								$subtipo_id						= $row_subtipo['c_subtipo_id'];
								$subtipo_T				    	= $row_subtipo['c_subtipo_T'];								
								$subtipo_nome					= $row_subtipo['c_subtipo_nome'];
								$subtipo_grau					= $row_subtipo['c_subtipo_grau'];								
								
								
								if($subtipo_id	!= $subtipo_anterior  ){ ////////////////////EXIBINDO SUBTIPOS
								
										
											
										switch($subtipo_id) {
																					
											case 1:	 /////////CAIXA
													$class_subtipo += 0.1; //classificação dos subtipos	
											
											?>
														<tr>
															<td><?php echo $subtipo_id	; ?></td>
															<td><?php echo $subtipo_T; ?></td>
															<td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo; ?></td>
															<td style="font-weight:bold;"><?php echo $subtipo_nome; ?></td>
															<td><?php echo $subtipo_grau; ?></td>
														</tr>
														
														<?php	
															$qr_projetos = mysql_query("SELECT projeto.nome, projeto.id_projeto, projeto.regiao  FROM projeto
																						INNER JOIN master
																						ON projeto.id_master = master.id_master 
																						WHERE projeto.status_reg = 1 ");
															while($row_projeto = mysql_fetch_assoc($qr_projetos)):
															$cont_caixa += 0.1;
															
															?>
															<tr>
																<td><?php echo $row_projeto['id_projeto']?></td>
																<td></td>
																<td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo.sprintf('%04s',$cont_caixa); ?></td>
																<td style="text-transform:uppercase;"><?php echo 'Caixa-'.$row_projeto['nome'].' ('.$row_projeto['regiao'].')'; ?></td>
																<td>5</td>
															</tr>
															<?php
															endwhile;
															unset($cont_caixa);
															echo '<tr><td colspan="5">&nbsp;</td></td>';			
										break;
										
										case 2:		
										//////////////////////////////////////////////////////////////////////////
													///////CONDIÇÂO PARA PEGAR AS CONTAS DOS PROJETOS ATIVOS QUANDO O SUBTIPO FOR BANCOS CONTA MOVIMENTO
														$qr_master = mysql_query("SELECT * FROM master WHERE status = 1");
														while($row_master = mysql_fetch_assoc($qr_master)):
															
															$qr_projetos = mysql_query("SELECT projeto.nome, projeto.id_projeto, bancos.razao  FROM projeto
																						INNER JOIN bancos 
																						ON projeto.id_projeto = bancos.id_projeto
																						WHERE projeto.status_reg = 1 AND projeto.id_master = '$row_master[id_master]'
																						");	
															if(mysql_num_rows($qr_projetos) !=0) {
															$class_subtipo += 0.1; //classificação dos subtipos	
															
																
																	?>
																	<tr>
																		<td><?php echo $subtipo_id; ?></td>
																		<td><?php echo $subtipo_T; ?></td>
																		<td><?php  echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo; ?></td>
																		<td style="font-weight:bold;"><?php echo $subtipo_nome; ?></td>
																		<td><?php echo $subtipo_grau; ?></td>
																	</tr>
																	
																	<?php
																		while($row_projeto = mysql_fetch_assoc($qr_projetos)):
																		
																		$cont_bancos += 0.1;
																	?>
																		<tr>
																			<td><?php echo $row_projeto['id_projeto']?></td>
																			<td></td>
																			<td><?php  echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo.sprintf('%04s',$cont_bancos); ?></td>
																			<td style="text-transform:uppercase;"><?php echo $row_projeto['razao'].' - C/C '.$row_projeto['conta'].' ('.$row_projeto['nome'].')'; ?></td>
																			<td>5</td>
																		</tr>
																		<?php
																		
																		
																			endwhile;
																			echo '<tr><td colspan="5">&nbsp;</td></td>';
																			unset($cont_bancos);
															}
															endwhile;
															
										break;	
										
										case 3:  ///////APLICAÇÕES FINANCEIRAS PEGA O NOME DE TODOS OS BANCOS CUJA DATA NA TABELA SAÍDA SEJA DO ANO ESCOLHIDO
														$qr_master = mysql_query("SELECT * FROM master WHERE status = 1");
														while($row_master = mysql_fetch_assoc($qr_master)):
															
															$qr_bancos = mysql_query("SELECT  DISTINCT(saida.id_banco), bancos.razao, bancos.conta, projeto.nome
																						FROM projeto
																						INNER JOIN bancos
																						ON   projeto.id_projeto = bancos. id_projeto
																						INNER JOIN saida 
																						ON bancos.id_banco  =  saida.id_banco
																						WHERE
																						 YEAR(saida.data_pg)  = '$ano'
																						AND projeto.id_master = '$row_master[id_master]'
																						AND saida.status = 2 
																						");	
																						
															if(mysql_num_rows($qr_bancos) != 0 ){
															$class_subtipo += 0.1; //classificação dos subtipos	
															
															?>
																	<tr>
																		<td><?php echo $subtipo_id; ?></td>
																		<td><?php echo $subtipo_T; ?></td>
																		<td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo; ?></td>
																		<td style="font-weight:bold;"><?php echo $subtipo_nome; ?> - <?php echo $row_master['razao'];?></td>
																		<td><?php echo $subtipo_grau; ?></td>
																	</tr>
																	
																	<?php
																		while($row_banco = mysql_fetch_assoc($qr_bancos)):
																				
																				$cont_bancos += 0.1;
																			?>
																				<tr>
																					<td><?php echo $row_projeto['id_projeto']?></td>
																					<td></td>
																					<td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo.sprintf('%04s',$cont_bancos); ?></td>
																					<td style="text-transform:uppercase;"><?php echo $row_banco['razao'].' - C/C '.$row_banco['conta'].' ('.$row_banco['nome'].')'; ?></td>
																					<td>5</td>
																				</tr>
																				<?php
																		
																				
																			endwhile;
																			echo '<tr><td colspan="5">&nbsp;</td></td>';
																			unset($cont_bancos);
															}					
														endwhile;
										break;
										}
										
										if($subtipo_id !=1 and $subtipo_id !=2 and $subtipo_id !=3) 
										{
											
												$class_subtipo += 0.1; //classificação dos subtipos	
										?>
                                            <tr>
                                            	<td><?php echo $subtipo_id; ?></td>
                                                <td><?php echo $subtipo_T; ?></td>
                                                <td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo; ?></td>
                                                <td style="font-weight:bold;"><?php echo $subtipo_nome; ?></td>
                                                <td><?php echo $subtipo_grau; ?></td>
                                            </tr>                                           
                                           <?php
											
											
											//////////////////////////////////////////////CONTAS
										     $qr_conta = mysql_query("SELECT * FROM c_contas WHERE c_conta_status = '1' AND c_subtipo_id = '$subtipo_id'");
											 while($row_contas = mysql_fetch_assoc($qr_conta)):
											 
												$contas_classificacao += 0.1;
											 
											    $contas_id						= $row_contas['c_conta_id'];
												$contas_T				    	= $row_contas['c_conta_T'];												
												$contas_nome					= $row_contas['c_conta_nome'];
												$contas_grau					= $row_contas['c_conta_grau'];
												?>                                                
                                                
												<tr>
                                                    <td><?php echo $contas_id; ?></td>
                                                    <td><?php echo $contas_T; ?></td>
                                                    <td><?php echo $grupo_classificacao.'.'.$subgrupo_classificacao.'.'.$tipo_classificacao.$class_subtipo.sprintf('%04s',$contas_classificacao); ?></td>
                                                    <td><?php echo $contas_nome; ?></td>
                                                <td>5</td>
                                            </tr>
												
												
                                                <?php
											 endwhile;	
											 echo '<tr><td colspan="5">&nbsp;</td></td>';												
											}
								}
								
								
						unset($contas_classificacao);
						$subtipo_anterior = $subtipo_id;
						endwhile;
				
				unset($class_subtipo);
				$tipo_anterior = $row_tipo['c_tipo_id'];
				endwhile;
		
		
		unset($tipo_classificacao);
		$subgrupo_anterior = $row_subgrupo['c_subgrupo_id'];
		endwhile;
		
		unset($subgrupo_classificacao);

	unset($subgrupo_anterior, $tipo_anterior, $subtipo_anterior);
$grupo_anterior = $row_grupo['c_grupo_id'];
endwhile;
?>

</table>
</div>
</body>
</html>
