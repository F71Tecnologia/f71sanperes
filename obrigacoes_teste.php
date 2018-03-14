<?php
include('conn.php');
$Master = 1;
$tipos = array(1 => 'Ativo',0 => 'Inativo');

for($i=2008; $i<=date('Y'); $i++){
	$anos[] = $i;
}
	
// Loop dos Status  
foreach($tipos as $status => $nome_status){ ?>

	<h3 class="titulo"><?php echo $nome_status; ?></h3>    
         
    <?php      
	  $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_master = '$Master'  AND status_reg = '$status'");
	  while($row_projetos = mysql_fetch_assoc($qr_projetos)) :
		  $projetos[] = $row_projetos['id_projeto'];   
	  endwhile;
		 
	  $projetos = implode(',', $projetos);
	 
	  // Loop dos Projetos e Subprojetos
      $qr_projeto = mysql_query("SELECT id_projeto, nome AS tipo_contrato , inicio, termino, id_subprojeto, id_regiao
							       FROM projeto
							      WHERE id_projeto IN ($projetos) ORDER BY regiao ASC");
	  while($row_projeto = mysql_fetch_assoc($qr_projeto)): 
	
		   $projeto = $row_projeto['id_projeto'];
		   $subprojeto = $row_projeto['id_subprojeto'];
		   $regiao = $row_projeto['id_regiao'];
			
			if($regiao != $regiao_anterior) { 
			   
				$ordem++;
				
				  $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
				  $row_regiao = mysql_fetch_assoc($qr_regiao);  
		   ?>
		   
			<a class="show <?php if($_GET['aberto'] == $ordem) { echo 'seta_aberto'; } else { echo 'seta_fechado'; } ?>" id="<?=$ordem?>" href=".<?=$ordem?>" onClick="return false">
			  <span style="text-transform:uppercase">  <?=$row_regiao['regiao']?></span>
				
              
		<?php } // Fim do if
	
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
            <td width="10%">&nbsp;</td>
          </tr>
    
	<?php
	
	// Listando as Obrigações Abertas dos Modelos Prontos
  $qr_obrigacoes = mysql_query("SELECT * FROM obrigacoes INNER JOIN obrigacoes_modelos ON modelo_id = obrigacao_modelo WHERE obrigacao_status = '1' AND obrigacao_projeto = '$row_projeto[id_projeto]' AND obrigacao_subprojeto = '$row_projeto[id_subprojeto]'");
  while($row_obrigacao = mysql_fetch_assoc($qr_obrigacoes)) {

		
	  $data_inicio  = $row_projeto['inicio'];
	  $data_termino = date('Y-m-d', strtotime("+1 year", strtotime($row_projeto['termino'])));
                                  
	  list($ano_projeto,$mes_projeto,$dia_projeto) = explode('-',$data_inicio);
	  
	  $qr_entregue    = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$row_obrigacao[obrigacao_id]' AND entregue_status = '1'");
	  
	  settype($anos_entregues,'array');
	  
	  while($row_anos_entregues = mysql_fetch_assoc($qr_entregue)){
	  
		$anos_entregues[] = substr($row_anos_entregues['entregue_datareferencia'],0,4); 
		
		}
	   $diferenca = array_diff($anos,$anos_entregues);
	   
	   unset($anos_entregues);
	 
	  $total_entregue = mysql_num_rows($qr_entregue);
	  
	  $dia_semana = date('w', mktime('0','0','0',$row_obrigacao['modelo_mes'],$row_obrigacao['modelo_dia'],2008 + $total_entregue));
		  if($dia_semana == 0){
			  $antecipacao_fds = 2;
		  } elseif($dia_semana == 6){
				$antecipacao_fds = 1;
		  } else {
				$antecipacao_fds = 0;
		  }
								  
	  $data_entrega = date('Y-m-d', mktime('0','0','0',$row_obrigacao['modelo_mes'],$row_obrigacao['modelo_dia']-$antecipacao_fds,2008 + $total_entregue));
	  unset($antecipacao_fds);
	  
	  //if($data_entrega < $data_termino) { ?>

	  <tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
		  <td align="left"><?php echo $row_obrigacao['modelo_nome']; ?></td>
		  <td><?php echo formato_brasileiro($data_entrega); ?></td>
		  <td><?php if($data_entrega > date('Y-m-d')) {
						echo 'Aberto';
					} else {
						echo 'Atrasado';
					} ?></td>
		  <?php if(in_array($row_obrigacao['modelo_id'],array('3','6'))) { ?>
		  <td>&nbsp;</td>
		  <td>&nbsp;</td>
          <td align="center"><a href="cadastro2.php?projeto=<?php echo $row_projeto['id_projeto']; ?>&subprojeto=<?php echo $row_projeto['id_subprojeto']; ?>&obrigacao=<?php echo $row_obrigacao['obrigacao_id']; ?>&m=<?php echo $_GET['m']; ?>" class="botao" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">Cadastrar</a></td>
          
          <?php } else { ?>
          
          <td>
            <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master.'&aberto='.$status; ?>" method="post">
            
            
               <select name="ano_competencia" >
               <?php 
               foreach($diferenca as $chave => $valor){
    
               ?>
               <option value="<?php echo $valor;?>"><?php echo $valor;?></option>
               <?php										   
               }
               
               ?>
               
               </select>
            </td>
              
              <td>
                <input type="text" name="obrigacao_data" class="obrigacao_data" size="10" maxlength="10" onKeyUp="mascara_data(this);"/>
                </td>
          <td align="center">
                <input type="hidden" name="obrigacao_entrega" value="<?php echo $row_obrigacao['obrigacao_id']; ?>">
                <input type="hidden" name="data_referencia" value="<?php echo $data_entrega; ?>">
                <input type="submit" value="Gerar" class="botao">
            </form>
          </td>
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
			
			$data_entrega = substr(date('Y-m-d', strtotime("$soma_mes month", strtotime($data_inicio))),0,8).$row_obrigacao['obrigacao_dia'];
                                  
		//if($data_entrega < $data_termino) { ?>

			<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
				 <td align="left"><?php echo $row_obrigacao['obrigacao_nome']; ?>
				  <?php /* if(!empty($row_obrigacao['obrigacao_anexo'])) { ?>(<a href="anexos/<?php echo $row_obrigacao['obrigacao_anexo']; ?>" target="_blank">Ver anexo</a>)<?php } ?></td>
			  <td><?php echo formato_brasileiro$data_entrega); */?>
                  </td>
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
			
		<?php } //fim obrigacoes abertas //

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
                                    
				$data_entrega = substr(date('Y-m-d', strtotime("$soma_mes month", strtotime($data_inicio))),0,8).$row_obrigacao['obrigacao_dia'];
			  
				//if($data_entrega < $data_termino) { ?>
		
					<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
					  <td align="left"><?php echo $row_obrigacao['obrigacao_nome']; ?>
						  <?php /* if(!empty($row_obrigacao['obrigacao_anexo'])) { ?>(<a href="anexos/<?php echo $row_obrigacao['obrigacao_anexo']; ?>" target="_blank">Ver anexo</a>)<?php } ?></td>
					  <td><?php echo formato_brasileiro$data_entrega); */?>
					  </td>
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
					
	<?php } //fim obrigacoes abertas // ?>
	 			 	</table> 
               
          
            
            </td>
           </tr>
         </table> 

				
 <?php		
	$regiao_anterior = $row_projeto['id_regiao'];
	
	
	
	
	endwhile;//fim projeto
	
	unset($projetos);
	
} // Fim do Loop dos Status

?>

 