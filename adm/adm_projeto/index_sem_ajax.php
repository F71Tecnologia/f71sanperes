<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');

$acesso_exclusao = array(9,5);

function verifica_expiracao($data)
{
	///verificar projeto expirado ou ok
	
			  list($ano,$mes,$dia)= explode('-', $data);
			  
			 $data_termino 		= mktime(0,0,0,$mes,$dia,$ano);
			 $data_hoje			= mktime(0,0,0,date('m'),date('d'),date('Y'));			 
			 $prazo_renovacao	= mktime(0,0,0,$mes,$dia-45,$ano); //45 dias
			
			 if(($prazo_renovacao<=$data_hoje) and($data_termino>=$data_hoje))
			 { 
			 $dif=($data_termino-$data_hoje)/86400;
			
					if($dif == 0 ){
						
						return '<span style="color:#F60;font-weight:bold;">Expira hoje!</span>';
						
					} else {
					
						return '<span style="color:#09C;font-weight:bold;">Expira em '.$dif.' dias!</span>';	 
					}
			 
			 }elseif($data_hoje>$data_termino)
			 {
				return '<span  style="color:#F00;font-weight:bold;"> Expirado</span>';
			 } else
			 {
				 return '<span style="color:#0C0;font-weight:bold;"> OK </span>';
			 }
} ?>

<html>
<head>
<title>Administra&ccedil;&atilde;o de Projetos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../../jquery-1.3.2.js"></script> 
<script type="text/javascript"> 
    hs.graphicsDir = '../../images-box/graphics/';
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
<style>
.linha_lista{
	font-size:12px;
	color:#000;
}
.linha_1{
	font-size:12px;
	background-color:#D2E9FF;}
.linha_2{
	font-size:12px;
	background-color:#F3F3F3;
}

.editar {
	margin-left:16px;	
}
</style>
</head>
<body style="text-transform:uppercase;">
<div id="corpo">
        <div id="menu" class="projeto">
        	<?php include "include/menu.php"; ?>
        </div>
<div id="conteudo">

<?php

$tipos = array(1 => 'Projetos Ativos',0 => 'Projetos Inativos');

// Loop dos Status  
foreach($tipos as $status => $nome_status) {


	 	      
 $qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$Master' ORDER BY regiao");	
 while($row_regioes = mysql_fetch_assoc($qr_regioes)): 
 
	  // Loop dos Projetos e Subprojetos
      $qr_projeto = mysql_query("SELECT *
							       FROM projeto
							      WHERE id_regiao = '$row_regioes[id_regiao]' AND status_reg = '$status' ORDER BY data_assinatura ASC");
	  while($row_projeto = mysql_fetch_assoc($qr_projeto)): 
	
			
		   $projeto 	 = $row_projeto['id_projeto'];
		   $subprojeto 	 = $row_projeto['id_subprojeto'];
		   $regiao		 = $row_projeto['id_regiao'];
		   $status_atual = $row_projeto['status_reg'];
			
	       if($regiao != $regiao_anterior) { // Verificação de Região
			   
			   $ordem++;
			   
			   if($ordem != 1) { ?>
              	  </div>
              <?php }
			  
			  if($status_atual != $status_anterior) {
				  echo '<h3 class="titulo">'.$tipos[$status_atual].'</h3>';
			  }
				
			  $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
		      $row_regiao = mysql_fetch_assoc($qr_regiao); 	  ?>
		   
              <a class="show <?php if($_GET['aberto'] == $ordem) { echo 'seta_aberto'; } else { echo 'seta_fechado'; } ?>"  id="<?=$ordem?>" href=".<?=$ordem?>" onClick="return false">
                  <span style="text-transform:uppercase">  <?=$row_regiao['regiao']?></span>
              </a>

    		  <div class="<?=$ordem?>" style="width:100%; <?php if($_GET['aberto'] != $ordem) { echo 'display:none;'; } ?>">
		  
		<?php
			 } // Fim da Verificação de Região
		?>
       
                  
          <table  width="100%"style="width:100%; margin-bottom:50px;" cellspacing="1" cellpadding="4" class="relacao">    
          <tr align="center">
          
          <td colspan="13" ><span style="text-align:center; font-size:12px;text-transform:uppercase;"><?php echo $row_projeto['id_projeto'].' - '.$row_projeto['nome'];?></span>
          </td></tr> 
       
        <tr class="secao">
            <td width="30%">Projeto</td>
            <td width="34" align="center">Editar</td>
            <td width="46" align="center">Renovar</td>
            <td width="38" align="center">Anexos</td>
            <td width="30%">Local</td>
            <td width="143">Data de Assinatura</td>
             <td width="88">Início</td>
            <td width="101">Término</td>
            <td width="48">Valor Estimado</td>
            <td width="58">Valor Alcan&ccedil;ado</td>
            <td width="101" align="center">Status</td>
            <td width="30%" align="center">Última edição</td>
            
              <?php
		    if($status==1) { 
			
				if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
			?> 
				<td>		
						Desativar projeto
				</td>
			 <?php 
			 
				}
			 } ?>
                    
            
                  	</tr>
        	<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
            	<td>
					<?php		
                
                    echo $row_projeto['id_projeto'].' - '.$row_projeto['nome'];
                    
                    ?>
                </td>
                <td>
                	 <a style="margin-left:3px;margin-top:9px;text-decoration:none;" href="../../projeto/edicao_projeto.php?m=<?=$link_master?>&id=<?php echo $row_projeto['id_projeto']; ?>&regiao=<?php echo $row_projeto['id_regiao']; ?>">
                      <img src="../../imagens/editar_projeto.png"  title="Editar"/>
                  </a>
                  
                </td>
                <td>
                	<?php 
						// VERIFICANDO se esta expirado
						$qr_data=mysql_query("SELECT id_projeto FROM `projeto` WHERE TO_DAYS('$row_projeto[termino]') < TO_DAYS(now())");
	 					$resultado=mysql_num_rows($qr_data);
						if(!empty($resultado)) {
					?>
                    
                	<a style="margin-left:3px;margin-top:3px;text-decoration:none;" href="../../projeto/subprojeto/index.php?m=<?=$_GET['m']?>&id=<?=$row_projeto['id_projeto'];?>&regiao=<?=$row_projeto['id_regiao'];?>" ><img src="../../imagens/renovar_projeto.png"  title="Renovar"/></a>
                    
					<?php } else { ?>
                    
                    &nbsp;
                    
                    <?php } ?>
                    
                </td>
                <td>
                	<a href="anexos_projeto.php?m=<?=$link_master?>&id=<?php echo $row_projeto['id_projeto']; ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">ver</a>
                </td>
                <td>
                	<?php echo  $row_projeto['local']; ?>
                </td>
              
               <td>
                	<?php if ($row_projeto['data_assinatura'] !='0000-00-00')
					{
						
					echo formato_brasileiro($row_projeto['data_assinatura']);
					}else{
							echo "Data não informada";
					}
					
					 ?>
                     
                </td>
                <td>
                	<?php if ($row_projeto['inicio'] !='0000-00-00')
					{
						
					echo formato_brasileiro($row_projeto['inicio']);
					}else {
							echo "Data não informada";
					}
					 ?>
                     
                </td>
               
                <td>
				<?php
				 if($row_projeto['termino']!='0000-00-00')
					{
						
					 echo formato_brasileiro($row_projeto['termino']);
					} else{
							echo "Data não informada";
					}
				?>
                    
                </td>
                <td>
                
                <?php //valor alcançado dos projetos   
			    $qr_valor=mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
			    (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
			    INNER JOIN entrada 
			    ON notas_assoc.id_entrada = entrada.id_entrada
			    WHERE entrada.data_vencimento >='$row_projeto[inicio]' AND  entrada.data_vencimento <= '$row_projeto[termino]' AND notas.id_projeto = '$row_projeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_projeto[id_projeto] AND  notas.tipo_contrato2 = 'projeto'");
			   
			   
			   
			   
			    
			   
                
                $total_entrada = (float) @mysql_result($qr_valor,0);
				$valor_alcancado = $total_entrada;
				
				$totalizador += $valor_alcancado;
                
				if((( $row_projeto['verba_destinada'])==0.00) or (( $row_projeto['verba_destinada'])=='') )
					{	
						echo 'Valor não adicionado';
					} else {
						echo 'R$ '.number_format($row_projeto['verba_destinada'],2,',','.');
					}
					
				$totalizador_verbadestinada+= $row_projeto['verba_destinada'];
				?>
            
              </td>
              <td>
              
			  <?php  echo 'R$ '.number_format($valor_alcancado,2,',','.');  ?>
                     
			  </td>
                <td align="center">
						<?php
                         if((formato_brasileiro($row_projeto['inicio']) =='00/00/0000' ) or (formato_brasileiro($row_projeto['termino']) =='00/00/0000' )) {
                           
						   echo "Faltam informa&ccedil;&otilde;es"; 
                           
               			 }else{
							    $function = substr(verifica_expiracao($row_projeto['termino']),45,-7);
								   
								  if($function =='Expirado')
								  {
									  
	/*								 
									  $qr_verifica_subprojeto_1 = mysql_query("SELECT id_subprojeto,termino FROM subprojeto WHERE id_projeto = $row_projeto[id_projeto] AND status_reg = '$status' AND termino ='".date('Y-m-d')."' " );
									 $verifica1 = mysql_num_rows($qr_verifica_subprojeto_1);
									 */
									  
									  //verifica se existe subprojeto não expirado
									  $qr_verifica_subprojeto = mysql_query("SELECT id_subprojeto,termino FROM subprojeto WHERE id_projeto = $row_projeto[id_projeto] AND status_reg = '$status' AND termino >='".date('Y-m-d')."' ");
									  $verifica = mysql_num_rows($qr_verifica_subprojeto);
									  
									  
									  
									/*  if($verifica1 != 0) {
										  
										  	echo '<span style="color:#F60;font-weight:bold;">Expira hoje!</span>';
											
										  
									  }else*/if ($verifica!=0)
												  {
													  echo '<span style="color:#0C0;font-weight:bold;"> OK </span>';
												   } else {
													   
													 echo '<span  style="color:#F00;font-weight:bold;"> Expirado</span>';
												 }
										 
								  } else {
											  echo  verifica_expiracao($row_projeto['termino']);
								  }
					  
					  }	  ?>
                      
                 </td>
                 <td>
                 
                 <?php
				 //Mostrar o funcionario que fez a última edição e a data de alteração
				if($row_projeto['data_atualizacao']!='0000-00-00'){
					  	 
					 $qr_funcionario 	= mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_projeto[id_usuario_atualizacao]'");
					 $row_funcionario	= mysql_fetch_assoc($qr_funcionario);
					 $nome_func			= explode(' ', $row_funcionario['nome']);
						  
					 echo 'Editado por:<br> '.$nome_func[0].' <br>em '.formato_brasileiro($row_projeto['data_atualizacao']);
					 
				  }else{
					  
					   $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_projeto[id_usuario]'");
					   $row_funcionario=mysql_fetch_assoc($qr_funcionario);
				       $nome_func=explode(' ', $row_funcionario['nome']);
				 
								if($row_projeto['data'] != '') {
									echo 'Cadastrado por:<br> '.$nome_func[0].' <br>em '.date('d/m/Y',strtotime($row_projeto['data']));
								}
							
				}   ?>
                
                 </td>
                
                 
                  <?php 
				 if($status==1){ 
						 if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
				 ?>
                 
                  <td>	 
                     <a href="../../projeto/desativar_projeto.php?m=<?=$link_master?>&id=<?=$row_projeto['id_projeto']?>&regiao=<?php echo $row_regiao['id_regiao']; ?>"  onClick="return window.confirm('Deseja desativar o projeto <?=$row_projeto['nome']?> ?')"><img src="../../imagens/desativar.png" width="30" height="30"/></a>
                  </td>
                  
			  <?php } 
				 }
			  ?>
				 
                
 			</tr>
            
		 <?php  //Seleciona dados da tabela subprojeto exibie as linhas do projeto
					$qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY data_assinatura ASC ") or die('erro');
$n_linha_subprojeto = mysql_num_rows($qr_subprojeto);

while($rows_subprojeto=mysql_fetch_assoc($qr_subprojeto)):

if($n_linha_subprojeto!=0)
{
?>

			<tr class="novo">
      		  <td> 
			     
				<?php 
                switch($rows_subprojeto['tipo_termo_aditivo']){
                    
                    case 0:  echo $rows_subprojeto['numero_contrato'].' - '.$rows_subprojeto['tipo_subprojeto'];
                    break;
                    case 1:  echo $rows_subprojeto['numero_contrato'].' - '.$rows_subprojeto['tipo_subprojeto'].'<br>(Prorrogação)'; 
                    break;
                    case 2:  echo $rows_subprojeto['numero_contrato'].' - '.$rows_subprojeto['tipo_subprojeto'].'<br>(Atualização Contratual)'; 
                    break;
                }
                
                
                  ?>
          	  </td>
        	  <td>
              	 <a style="text-decoration:none;" href="../../projeto/subprojeto/edicao_subprojeto1.php?m=<?=$link_master?>&id=<?php echo $rows_subprojeto['id_subprojeto']; ?>&regiao=<?php echo $rows_subprojeto['id_regiao'];?>"><img src="../../imagens/editar_projeto.png" title="Editar"/></a>
              </td>              
       	  	  <td>&nbsp;
          		 
           	  </td>              
        	  <td>
              	<a href="../../projeto/subprojeto/anexos_subprojeto.php?m=<?=$link_master?>&id=<?php echo $rows_subprojeto['id_subprojeto']; ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">ver</a>
              </td>              
        	  <td>
			  	<?= $row_projeto['local']; ?>
             </td> 
        	 <td><?php if ($rows_subprojeto['data_assinatura'] !='0000-00-00')
					{
						
					echo formato_brasileiro($rows_subprojeto['data_assinatura']);
					}else{
							echo "Data não informada";
					}	 ?>
                    
              </td>
        	  <td>
              
              <?php 
			  if($rows_subprojeto['tipo_termo_aditivo']==1  ){	
							  if ($rows_subprojeto['inicio'] !='0000-00-00')
									{
										
									echo formato_brasileiro($rows_subprojeto['inicio']);
									}else{
											echo "Data não informada";
									}
							
			  }elseif($rows_subprojeto['tipo_subprojeto']!='APOSTILAMENTO' and $rows_subprojeto['tipo_subprojeto']!='TERMO ADITIVO')
						 {
							 
										 if ($rows_subprojeto['inicio'] !='0000-00-00'){
										echo formato_brasileiro($rows_subprojeto['inicio']); 
										  } else {
											  echo "Data não informada";
											  }
							}

					 ?>
                     
              </td>
              <td>
                      
                       <?php 
					     if($rows_subprojeto['tipo_termo_aditivo']==1)
						 {	
										   if ($rows_subprojeto['termino'] !='0000-00-00')
										{
											
										echo formato_brasileiro($rows_subprojeto['termino']);
										}else{
												echo "Data não informada";
										}
						
						 } elseif($rows_subprojeto['tipo_subprojeto']!='APOSTILAMENTO' and $rows_subprojeto['tipo_subprojeto']!='TERMO ADITIVO')
						 {
							 
										 if ($rows_subprojeto['termino'] !='0000-00-00'){
										echo formato_brasileiro($rows_subprojeto['termino']); 
										  } else {
											  echo "Data não informada";
											  }
							}

					 ?>                   
              </td>
              <td>               
			   <?php
		
			   
			   //verba destina subprojeto 
			 
			   
			      /*$qr_valor = mysql_query("SELECT SUM( REPLACE( VALOR,  ',',  '.' ) ) FROM  `entrada`  WHERE  `id_projeto` = '$row_projeto[id_projeto]' AND status ='2' AND tipo ='12' AND data_pg >=' $rows_subprojeto[inicio]' AND  data_pg <='$rows_subprojeto[termino]' ;");*/
				  
		  $qr_valor=mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
   INNER JOIN entrada 
   ON notas_assoc.id_entrada = entrada.id_entrada
   WHERE entrada.data_vencimento >='$rows_subprojeto[inicio]' AND  entrada.data_vencimento <= '$rows_subprojeto[termino]' AND notas.id_projeto = '$rows_subprojeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $rows_subprojeto[id_subprojeto]   AND notas.tipo_contrato2 = 'subprojeto'")or die(mysql_error());
   
   	 

		$total_entrada_sub	 = (float) @mysql_result($qr_valor,0);
		$valor_alcancado_sub =$total_entrada_sub;
		$totalizador		 += $valor_alcancado_sub;
		
	//totalizador	
		$verba_destinada             = str_replace(',','.',(str_replace('.','',$rows_subprojeto['verba_destinada'])));
		$totalizador_verbadestinada += $verba_destinada;
		
			   			   
		 if((( $rows_subprojeto['verba_destinada'])==0.00) or (( $rows_subprojeto['verba_destinada'])=='') )
						{
							
							echo 'Valor não adicionado';
						} else
						
						{
							echo 'R$ '.$rows_subprojeto['verba_destinada'];
						} ?>
                
           </td>           
       	   <td>
           
           <?php
          
					 echo 'R$ '.number_format($valor_alcancado_sub,2,',','.');
					 
				 /*if($valor_alcancado_sub>str_replace('.','',$rows_subprojeto['verba_destinada']) and $rows_subprojeto['verba_destinada']!=0.00)
					{
						echo '<br> <span  style="color:#F00;font-weight:bold;"> Verba do projeto ultrapassou o valor estimado!</span>';
					}*/
					
			?>  		
           
           </td>
        	  <td align="center">
              <?php
                      if((formato_brasileiro($rows_subprojeto['inicio']) =='00/00/0000' ) or (formato_brasileiro($rows_subprojeto['termino']) =='00/00/0000' )  )
                             
                             {
                                
								if($rows_subprojeto['tipo_subprojeto'] == 'TERMO ADITIVO' or $rows_subprojeto['tipo_subprojeto']== 'APOSTILAMENTO'){
									if($rows_subprojeto['tipo_termo_aditivo']==1){
									 echo "Faltam informa&ccedil;&otilde;es"; 
									} 
								} else {
									echo "Faltam informa&ccedil;&otilde;es"; 	
								}
              
               			 }else{
									  switch($rows_subprojeto['tipo_subprojeto']){
										  
										  case 'TERMO ADITIVO': if($rows_subprojeto['tipo_termo_aditivo']==1)
																{
																	 echo  verifica_expiracao($rows_subprojeto['termino']);
																	 }
																	 
																	 break;
										
										  case 'TERMO DE PARCERIA': echo  verifica_expiracao($rows_subprojeto['termino']);
																	 break;	
																	 
										  case 'NOVO CONVÊNIO': echo  verifica_expiracao($rows_subprojeto['termino']);
																	 break;		
																	 
										  case 'APOSTILAMENTO': if($rows_subprojeto['tipo_termo_aditivo']==1)
																{
																	 echo  verifica_expiracao($rows_subprojeto['termino']);
																	 }
																	 
																	 break;						 
																	 
									  }
						 }
					   ?>
              </td>
              <td>
              
               <?php
				 //Mostrar o funcionario que fez a última edição do subprojeto(renovação)
				
				 
				 
						  if($rows_subprojeto['subprojeto_data_atualizacao']!='0000-00-00'){
						 
						   
				$qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$rows_subprojeto[subprojeto_id_usuario_atualizacao]'");
				 $row_funcionario = mysql_fetch_assoc($qr_funcionario);
				 $nome=explode(' ',$row_funcionario['nome']);
				 
				 echo 'Editado por: <br>'.$nome[0].' <br>em '. formato_brasileiro($rows_subprojeto['subprojeto_data_atualizacao']);
									  
									  }else{
										  
										   $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$rows_subprojeto[id_usuario]'");
									 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
									  $nome_func=explode(' ', $row_funcionario['nome']);
									 
													if($rows_subprojeto['data']!=''){
														echo 'Cadastrado por:<br> '.$nome_func[0].' <br>em '.date('d/m/Y',strtotime($rows_subprojeto['data']));
													
													}
										  }
				 ?>
                 
                 </td>
                 
                 
                 <?php
				 if($status==1){
					 if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
					 echo '<td></td>';
				 }
				 }
				 ?>
          </tr>
          
          
         <?php  //fim loop da tabela subprojeto(renovação) 
			  }
			  endwhile; //fim if($n_linha_subprojeto!=0)?>   
         
           <tr>
        	<td></td>
            <td></td>
            <td width="1%"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td align="right">Total:</td>
            <td width="18%"><?php echo 'R$ '. number_format($totalizador_verbadestinada,2,',','.') ;?></td>
        	<td width="18%"><?php echo 'R$ '.number_format($totalizador,2,',','.');  ?><td>
            <td width="15%" colspan="3" > <?php
			
			if($totalizador > $totalizador_verbadestinada){
										echo '<br> <span  style="color:#F00;font-weight:bold; font-size:9px;"> Verba do projeto ultrapassou o valor estimado!</span>';
			}
					
					
					 unset ($totalizador_verbadestinada);								
            	unset($totalizador);	
			?>
            
                    </td>
                    <td ></td>
                    <td></td>
                </tr> 
                
                
               </table>
       
                </td>
              </tr>
            </table>

	 <?php 
		  $regiao_anterior = $row_projeto['id_regiao'];
		  $status_anterior = $row_projeto['status_reg'];
		
	 
	 endwhile; // FIM DO LOOP DOS PROJETOS 
endwhile;		
	unset($projetos);

} // Fim do Loop dos Status

?>

    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('include/rodape.php'); ?>
    </div>
</div>
</body>
</html>