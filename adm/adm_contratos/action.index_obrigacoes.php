<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');

$regiao_id = $_GET['regiao'];
$status    =  $_GET['status'];

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao_id' AND status_reg = '$status'") or die(mysql_error());
while($row_projeto = mysql_fetch_assoc($qr_projeto)):




// Criando Obrigações dos Modelos Prontos ainda não Criados
	$qr_modelos = mysql_query("SELECT * FROM obrigacoes_modelos WHERE modelo_status = '1'");
	while($row_modelo = mysql_fetch_assoc($qr_modelos)) {
	
		  $qr_verificacao    = mysql_query("SELECT * FROM obrigacoes WHERE obrigacao_projeto = '$row_projeto[id_projeto]' AND obrigacao_subprojeto = '$row_projeto[id_subprojeto]' AND obrigacao_modelo = '$row_modelo[modelo_id]'");
		  $total_verificacao = mysql_num_rows($qr_verificacao);
		  
		  if(empty($total_verificacao)) {
			 mysql_query("INSERT INTO obrigacoes (obrigacao_projeto, obrigacao_subprojeto, obrigacao_modelo) VALUES ('$row_projeto[id_projeto]', '$row_projeto[id_subprojeto]', '$row_modelo[modelo_id]')");
		  }
		  
				} //fim modelos ?>	
                
          
     <table style="width:100%; margin-bottom:50px;" cellspacing="1" cellpadding="4" class="relacao"> 
          <tr>
            <td colspan="5"><h1 style="width:550px;"><?php echo $row_projeto['id_projeto']; ?> - <?php echo $row_projeto['tipo_contrato']; ?></h1></td>
         </tr>
         <tr class="secao">
            <td width="100%" colspan="7">Obriga&ccedil;&otilde;es abertas</td>
         </tr>
         <tr class="secao">
            <td width="40%" align="left">Nome</td>
            <td width="18%">Data Limite</td>
            <td width="12%">Status</td>
            <td width="20%">Ano da Compet&ecirc;ncia</td>
            <td width="20%">Data da Entrega</td>
            <td width="10%" colspan="2">&nbsp;</td>
          </tr>
    
	<?php
	
	// Listando as Obrigações Abertas dos Modelos Prontos
  $qr_obrigacoes = mysql_query("SELECT * FROM obrigacoes INNER JOIN obrigacoes_modelos ON modelo_id = obrigacao_modelo WHERE obrigacao_status = '1' AND obrigacao_projeto = '$row_projeto[id_projeto]' AND obrigacao_subprojeto = '$row_projeto[id_subprojeto]'");
  while($row_obrigacao = mysql_fetch_assoc($qr_obrigacoes)) {
		
	  $data_inicio  = $row_projeto['inicio'];
	  $data_termino = date('Y-m-d', strtotime("+1 year", strtotime($row_projeto['termino'])));
                                  
	  list($ano_projeto,$mes_projeto,$dia_projeto) = explode('-',$data_inicio);
	  
	  $qr_entregue = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$row_obrigacao[obrigacao_id]' AND entregue_status = '1'");
	  
	  settype($anos_entregues,'array');
	  
	  while($row_anos_entregues = mysql_fetch_assoc($qr_entregue)){
	  
		  $anos_entregues[] = substr($row_anos_entregues['entregue_datareferencia'],0,4); 
		
	  }
	   
	 $diferenca = array_diff($anos,$anos_entregues);
	   
	 unset($anos_entregues);
	 
	 $total_entregue = mysql_num_rows($qr_entregue);
	 
	 //verificação  do anexo 1
	 list($assinatura_ano,$assinatura_mes, $assinatura_dia) = explode('-',$row_projeto['data_assinatura']); 
	 
	 if($row_obrigacao['modelo_id'] == 1){ 
	 	
	 	$dia_semana = date('w', mktime('0','0','0',$assinatura_mes, 15 +$assinatura_dia ,$assinatura_ano));
		
	 } else {
		  $dia_semana = date('w', mktime('0','0','0',$row_obrigacao['modelo_mes'],$row_obrigacao['modelo_dia'],2008 + $total_entregue));
	 }
	 
	 
	  if($dia_semana == 0) {
		  $antecipacao_fds = 2;
	  } elseif($dia_semana == 6) {
			$antecipacao_fds = 1;
	  } else {
			$antecipacao_fds = 0;
	  }
								  
	  $data_entrega = date('Y-m-d', mktime('0','0','0',$row_obrigacao['modelo_mes'],$row_obrigacao['modelo_dia']-$antecipacao_fds,$assinatura_ano+1));
	  
	   //calculo da data de anexo 1	    
		
	   
	   
	   
	   
	  unset($antecipacao_fds); ?>

	  <tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
		  <td align="left"><?php echo $row_obrigacao['modelo_nome']; ?></td>
          
          <?php 
		  //Exibir a data do anexo 1
		  if($row_obrigacao['modelo_id'] == 1) {
			  
			$data_entrega_anexo_1 = date('Y-m-d', mktime('0','0','0',$assinatura_mes,$assinatura_dia+ 15 - $antecipacao_fds, $assinatura_ano));  
			echo '<td>'.formato_brasileiro($data_entrega_anexo_1).'</td>';
				  
		  } else {
			  
			  echo '<td>'.formato_brasileiro($data_entrega).'</td>';
		  }
		  ?>
               
		  
          
		  <td><?php if($data_entrega > date('Y-m-d')) {
						echo 'Aberto';
					} else {
						echo 'Atrasado';
					} ?></td>
                    
		  <?php if(in_array($row_obrigacao['modelo_id'],array('3','6'))) { ?>
          
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="center" colspan="2"><a href="cadastro2.php?projeto=<?php echo $row_projeto['id_projeto']; ?>&subprojeto=<?php echo $row_projeto['id_subprojeto']; ?>&obrigacao=<?php echo $row_obrigacao['obrigacao_id']; ?>&m=<?php echo $_GET['m']; ?>" class="botao" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">Cadastrar</a></td>
          
          <?php } else { ?>
          <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master.'&aberto='.$status; ?>" method="post">
                <td>
                <?php
				$qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '$row_projeto[id_projeto] AND status = 1'");
				
				while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):
				
					if($data_termino>$termino_anterior){
					$data_termino = $row_subprojeto['termino'];	
					}
					
					$termino_anterior = $data_termino;
				
				endwhile;
				
				if($data_termino == 0){
					$data_final = $row_projeto['termino'];				
				} else {
					$data_final = $data_termino;				
				}
				 
				?>
                 <input name="ano_competencia" class="ano_competencia" type="text"   style="background-color:transparent; border:0" size="3">
                    <input type="hidden" name="projeto_inicio" class="projeto_inicio" value="<?php echo $row_projeto['inicio'];?>">
                      <input type="hidden" name="projeto_termino" class="projeto_termino"  value="<?php echo $data_final;?>">
                </td>
                <td>
                    <input type="text" name="obrigacao_data" class="obrigacao_data" size="10" maxlength="10" onKeyUp="mascara_data(this);"/>
                </td>
                
                <td align="center" colspan="2" align="center">
                    <input type="hidden" name="obrigacao_entrega" value="<?php echo $row_obrigacao['obrigacao_id']; ?>">
                    <input type="hidden" name="data_referencia" value="<?php echo $data_entrega; ?>">
                    <input type="submit"  class="botao_gerar" value="Gerar" disabled>
                
                </td>
            </form>
          <?php } ?>
          
      </tr>
    
          		
<?php } // fim obrigacoes

  // Listando as Obrigações Abertas Criadas
  $qr_obrigacoes = mysql_query("SELECT * FROM obrigacoes WHERE obrigacao_status = '1' AND obrigacao_modelo = '' AND obrigacao_projeto = '$row_projeto[id_projeto]'");
  while($row_obrigacao = mysql_fetch_assoc($qr_obrigacoes)) {
	  
	  $id_obrigacao=$row_obrigacao['obrigacao_id'];
	
	  $data_inicio  = $row_projeto['inicio'];
	  $data_termino = date('Y-m-d', strtotime("+1 year", strtotime($row_projeto['termino'])));
		
	  $qr_entregue    = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$row_obrigacao[obrigacao_id]' AND entregue_status = '1'");  	
								  
		  $total_entregue = mysql_num_rows($qr_entregue);
			
			switch($row_obrigacao['obrigacao_periodicidade']) {
				case 'mensal':
					$soma_mes = '1';
				break;
				case 'trimestral':
					$soma_mes = '3';
				break;
				case 'semestral':
					$soma_mes = '6';
				break;
				case 'anual':
					$soma_mes = '12';
				break;
			}
			
			if(!empty($total_entregue)) {
				$soma_mes *= $total_entregue + 1;
			}
			
			$data_entrega = substr(date('Y-m-d', strtotime("$soma_mes month", strtotime($data_inicio))),0,8).$row_obrigacao['obrigacao_dia']; ?>

			<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
				 <td align="left"><?php echo $row_obrigacao['obrigacao_nome']; ?></td>
                 <td>&nbsp;</td>
                 <td><?php if($data_entrega > date('Y-m-d')) {
                                echo 'Aberto';
                            } else {
                                echo 'Atrasado';
                            } ?></td>
                  <td>&nbsp;</td>
                  <td align="center">
                    <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master.'&aberto='.$status; ?>" method="post">
                        <input type="hidden" name="obrigacao_entrega" value="<?php echo $row_obrigacao['obrigacao_id']; ?>">
                        <input type="hidden" name="data_referencia" value="<?php echo $data_entrega; ?>">
                        <input type="submit" value="Entregar" class="botao">
                    </form>
                  </td>
			</tr>
			
		<?php } //fim obrigacoes abertas
		
		
		
		// Listando as Obrigações Entregues
							   unset($a);
							   $qr_entregues = mysql_query("SELECT * FROM obrigacoes INNER JOIN obrigacoes_entregues ON obrigacao_id = entregue_obrigacao LEFT JOIN obrigacoes_modelos ON obrigacao_modelo = modelo_id WHERE obrigacao_projeto = '$row_projeto[id_projeto]' AND obrigacao_subprojeto = '$row_projeto[id_subprojeto]' ORDER BY entregue_ano_competencia ASC");
							   while($row_entregue = mysql_fetch_assoc($qr_entregues)) {
								   
								   
								   
						//data limite
								   
						   $qr_entregue = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$row_entregue[obrigacao_id]' AND entregue_status = '1'");

settype($anos_entregues,'array');

								while($row_anos_entregues = mysql_fetch_assoc($qr_entregue)){
								
										  $anos_entregues[] = substr($row_anos_entregues['entregue_datareferencia'],0,4); 
										
										}
		
									$diferenca = array_diff($anos,$anos_entregues);
									
									unset($anos_entregues);
									
									$total_entregue = mysql_num_rows($qr_entregue);
									
									$dia_semana = date('w', mktime('0','0','0',$row_entregue['modelo_mes'],$row_entregue['modelo_dia'],$row_entregue['entregue_ano_competencia'] + $total_entregue));
									
									if($dia_semana == 0) {
									  $antecipacao_fds = 2;
									} elseif($dia_semana == 6) {
										$antecipacao_fds = 1;
									} else {
										$antecipacao_fds = 0;
									}
															  
									$data_limite= date('Y-m-d', mktime('0','0','0',$row_entregue['modelo_mes'],$row_entregue['modelo_dia']-$antecipacao_fds,$row_entregue['entregue_ano_competencia']+1 ));
									unset($antecipacao_fds);
								   
								   
								   
								    $ano_entrega = $row_entregue['entregue_ano_competencia'];
								   
								    								 
									if(empty($a)) { ?>

                                      <tr class="secao">
                                          <td width="100%" colspan="7">Obrigações entregues</td>
                                      </tr>
                                      <tr class="secao">
                                          <td width="40%" align="left">Nome</td>
                                          <td width="25%">Data Limite </td>
                                          <td width="18%">Ano da Competência</td>
                                          <td width="17%">Data de Entrega</td>
                                          <td colspan="3">&nbsp;</td>
                                           
                                         
                                      </tr>

                               <?php }
							    $a++;
							   
							         if($ano_entrega != $ano_entrega_anterior) { ?>
                                      	  <tr class="secao">
                                              <td width="100%" colspan="7"><?php echo $ano_entrega; ?></td>
                                               
                                          </tr>
                               <?php } ?>
                               
                               		<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                                      <td align="left"><?php if(!empty($row_entregue['obrigacao_modelo'])) { echo $row_entregue['modelo_nome']; } else { echo $row_entregue['obrigacao_nome']; } ?></td>
                                     <td> <?php echo formato_brasileiro($data_limite);?></td>
                                      <td><?php echo formato_brasileiro($row_entregue['entregue_ano_competencia']); ?></td>
                                      
                                       <td><?php if(!empty($row_entregue['obrigacao_modelo'])) { echo formato_brasileiro($row_entregue['entregue_dataproc']); } else { echo '&nbsp;'; } ?></td>
                                       
                                          
                                        
                                        <td>&nbsp;</td>    
                                      <td align="center">
                                        <?php if(!empty($row_entregue['obrigacao_modelo']) and !in_array($row_entregue['modelo_id'],array('3','6'))) { ?>
                                            <form action="modelos/<?php echo $row_entregue['modelo_arquivo']; ?>" method="post" target="_blank">
                                                <input type="hidden" name="master" value="<?php echo $Master; ?>">
                                                <input type="hidden" name="projeto" value="<?php echo $row_projeto['id_projeto']; ?>">
                                                <input type="hidden" name="ano" value="<?php echo $row_entregue['entregue_ano_competencia']; ?>">
                                                <input type="hidden" name="data" value="<?php echo $row_entregue['entregue_dataproc']; ?>">
                                                <input type="hidden" name="subprojeto" value="<?php echo $row_projeto['id_subprojeto']; ?>">
                                                 <input type="hidden" name="obrigacao_id" value="<?php echo $row_entregue['obrigacao_id'] ?>">
                                                  <input type="hidden" name="entregue_id" value="<?php echo $row_entregue['entregue_id'] ?>">
                                                
                                                <input type="submit" value="Visualizar" class="botao">
                                            </form>
                                        <?php } else { ?>
                                        	<form action="anexos/<?php echo $row_entregue['obrigacao_anexo']; ?>" method="post" target="_blank">
                                                <input type="submit" value="Visualizar" class="botao">
                                            </form>
                                        <?php } ?>
                                        </td>
                                        
                                        <?php  if(in_array($_COOKIE['logado'],$acesso_exclusao)) { ?>
                                        <td align="center">
                                            <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master.'&excluir='.$row_entregue['entregue_id'];?>" method="post">
                                                <input type="submit" value="Excluir" class="botao">
                                            </form>
                                        </td>
                                      <?php } ?>   
                                    </tr>
                                    
                               <?php $ano_entrega_anterior = $ano_entrega; } // obrigacoes entreges ?>

    
	 		</table>
     			
 <?php
	endwhile;
	?>