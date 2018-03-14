<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');

$acesso_exclusao = array(9,5);
?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Notas Fiscais</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<style type="text/css">
.tr_titulo { font-size: 12px; font-weight: bold; }
</style>

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 
<script type="text/javascript"> 
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>

<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript">
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

	$('.azul').parent().css({'background-color': '#7EB3F1'});
	$('.vermelho').parent().css({'background-color': '#ffb8c0'});
	
});	
</script>
</head>
<body>
<div id="corpo">
    <div id="menu" class="nota">
    	<?php include "include/menu.php"; ?>
    </div>
    <div id="conteudo" style="text-transform:uppercase;">  
<?php

$tipos = array(1 => 'Projetos Ativos',0 => 'Projetos Inativos');

// Loop dos Status  
foreach($tipos as $status => $nome_status) {
	      
	  $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_master = '$Master'  AND status_reg = '$status'");
	  while($row_projetos = mysql_fetch_assoc($qr_projetos)) :
	  
	       //Bloqueio das regiões 15, 36, 57 e do projeto 3236 pq não sei se pode apagar ele do banco		
		  //if($row_projetos['id_regiao']=='15' or $row_projetos['id_regiao']=='36' or $row_projetos['id_regiao']=='37' or $row_projetos['id_projeto'] == 3236) continue; 
		  
		  $projetos[] = $row_projetos['id_projeto'];   
		  
	  endwhile;
	  
		 
	  $projetos = implode(',', $projetos);
	 
	  // Loop dos Projetos e Subprojetos
      $qr_projeto = mysql_query("SELECT *
							       FROM projeto
							      WHERE id_projeto IN ($projetos) ORDER BY regiao ASC");
	  while($row_projeto = mysql_fetch_assoc($qr_projeto)): 
	  
			 //verificação de notas
			$qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1' ORDER BY data_emissao DESC");
			$num_notas = mysql_num_rows($qr_notas);
			if (!empty($num_notas)) {
			
			
		   $projeto = $row_projeto['id_projeto'];
		   $subprojeto = $row_projeto['id_subprojeto'];
		   $regiao = $row_projeto['id_regiao'];
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
            
          <table style="width:100%; margin-bottom:50px;" cellspacing="1" cellpadding="4" class="relacao"> 
			<tr>
				<td align="center" colspan="7" style="font-style: italic;text-align:center;"><?=$row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?><td>
		   </tr>
           
			<?php 
				// LOOP DE NOTAS
				
				$array_assoc2 = array();
				while($row_notas = mysql_fetch_assoc($qr_notas)):
					
					$ano=substr($row_notas['data_emissao'], 0,4);
					
					
					// parceiro
					$qr_parceiro = mysql_query("SELECT 	parceiro_id, parceiro_nome FROM parceiros WHERE parceiro_id = '$row_notas[id_parceiro]'");
					$row_parceiro = mysql_fetch_assoc($qr_parceiro);
					$nome_parceiro = $row_parceiro['parceiro_id'] . ' - ' . $row_parceiro['parceiro_nome'];  
					
					
					//totalizadores por ano
					
					$total_notas_anos = mysql_num_rows(mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1' AND YEAR(data_emissao) = '$ano' "));
					$total_ano += str_replace(',','.',$row_notas['valor']);
					$a++;
					
						if($ano != $ano_anterior) {
						
							
							echo '<tr><td colspan="11">&nbsp;</td></tr>';
							echo '<tr class="novo2"><td colspan="11"><span style=" font-size:14px;">'.$ano.'</span></td></tr>';
							echo '<tr class="secao">
											   <td>Cod.</td>
											   <td width="18%">Parceiro</td>
											   <td>Nº</td>
											   <td>Data de Emissão</td>
											   <td>Valor NF / Carta Medição</td>
											   <td>Repasse<br>Parceiro</td>
											   <td>Diferenca</td>
											   <td>Status</td>
											   <td>Editar</td>
											   ';
						if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
											  
											  echo' <td>Excluir</td>';
						}
											   
											   echo'<td>Última edição</td>
									</tr>';
							
						}
						
				
				$qr_total_anos = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
							(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
							INNER JOIN entrada 
							ON notas_assoc.id_entrada = entrada.id_entrada
							WHERE notas.id_notas = '$row_notas[id_notas]' AND YEAR(data_emissao) = '$ano'  AND entrada.status = '2';
							");
						$total_entrada_anos = (float) @mysql_result($qr_total_anos,0); 
						$totalizador_repasse_anos += $total_entrada_anos;
						
						
					$diferenca_anos = (float) $total_entrada_anos - str_replace(',','.',$row_notas['valor']) ;
					$total_diferenca_anos += $diferenca_anos; 
					
						
												
					$ano_anterior = $ano;
					
				
			?>
            <?php 
				
				
					$qr_entrada = mysql_query("SELECT * FROM entrada  WHERE id_entrada = '$row_notas[id_entrada]';");
					$row_entrada = mysql_fetch_assoc($qr_entrada);				
					switch ($row_entrada['status']){
						case 0 :
							$status_entrada = "Entrada apagada.";
							break;
						case 1 :
							$status_entrada = "Entrada não confirmada.";
							break;
						case 2 :
							$status_entrada = "Entrada confirmada.";
							break;
					}
					
					// Adicionado por maikom em 27/09/2011
					// verificando o id_entrada existe 2 resgistros ou mais iguais indicando que ela
					// pertence a mais de uma nota. 
					
					$qr_assoc = mysql_query("SELECT * FROM notas_assoc WHERE id_notas = '$row_notas[id_notas]'");
					
					$array_assoc = array();
					while($row_assoc = mysql_fetch_assoc($qr_assoc)){
						
						$qr_dupli = mysql_query("SELECT * FROM notas_assoc WHERE id_entrada = '$row_assoc[id_entrada]'");
						$num_dupli = mysql_num_rows($qr_dupli);
						if($num_dupli > 1){
							$duplicado = 'Duplicado';
							while($row_dupli = mysql_fetch_assoc($qr_dupli)){
								$array_assoc[$row_dupli['id_notas']][] = $row_dupli['id_entrada'];
							}
							
							$array_assoc2 = $array_assoc;
							
						}else{
							$duplicado = 'Não duplicado';
						}
						
					}

					if(!empty($array_assoc)){ continue; }
				?>
                
			<tr class="linha_<?php  if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
				
                <td><?= $row_notas['id_notas']  . ' - ' . $duplicado; ?>
                </td>
				<td><?= $nome_parceiro ?></td>
				<td><?= $row_notas['numero']; ?></td>
                <td><?=implode('/',array_reverse(explode('-',$row_notas['data_emissao']))); ?></td>
				<td>R$ <?php 
						$totalizador_valor += str_replace(',','.',$row_notas['valor']);
						echo number_format($row_notas['valor'],2,',','.'); 		
						?>
                </td>
                <td>
                	<a href="view_entrada.php?nota=<?php echo $row_notas['id_notas'];  ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" style="text-decoration:none;">
                	R$ 
                	<?php 

						$qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
							(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
							INNER JOIN entrada 
							ON notas_assoc.id_entrada = entrada.id_entrada
							WHERE notas.id_notas = '$row_notas[id_notas]' AND entrada.status = '2';
							");
						$total_entrada = (float) @mysql_result($qr_total,0); 
						$totalizador_repasse += $total_entrada;
						echo number_format($total_entrada,2,',','.');
					?>
                    	<img src="../../novoFinanceiro/image/seta.gif" border="0"/>
                    </a>
                </td>
                <td <?php 
                $vn = (float) str_replace(',','.',$row_notas['valor']);
					if($vn > $total_entrada){
						echo 'class="vermelho"';
					}else if($vn < $total_entrada){
						echo 'class="azul"';
						
					}
				?>>R$
                	<?php 
					$diferenca = (float) $total_entrada - str_replace(',','.',$row_notas['valor']) ;
					$totalizador_diferenca += $diferenca; 
					echo number_format($diferenca,2,',','.');
					?>
                </td>
				
                <td>
                <?php 
				/*$qr_imagem	=	mysql_query("SELECT notas.id_notas, notas_files.id_file, notas_files.tipo, notas_files.status
									FROM notas
									INNER JOIN notas_files ON notas_files.id_notas = '$row_notas[id_notas]'");
				$row_imagem	=	mysql_fetch_assoc($qr_imagem);*/
				?>
                <a href="visializa_files.php?id_nota=<?php echo $row_notas['id_notas']; ?>" target="_blank" >                     
                <img src="../../imagens/print.png" alt="imprimir" width="50" height="38" />
                </a>
                	     
                </td>
				<td>
					<a href="edicao.php?m=<?php echo $_GET['m'];?>&id=<?php echo $row_notas['id_notas'];?>">
            <img src="../../imagens/editar_projeto.png" alt="Editar nota">
             </a> 
					
				</td>
                
                <?php 
				if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
					?>
                <td>
                	<a href="exclusao.php?m=<?php echo $_GET['m'] ;?>&id=<?php  echo $row_notas['id_notas'];?>">
            <img src="../../imagens/lixo.gif" alt="Excluir nota"/></a>
                </td>
                
                <?php }?>
                
                <td>
                <?php
				
				  if($row_notas['ultima_edicao']!='0000-00-00 00:00:00'){
					  $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[editado_por]'");
				 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
				 $nome_func=explode(' ', $row_funcionario['nome']);
				 
				 $data=date('d/m/Y',strtotime($row_notas['ultima_edicao']));
				 echo 'Editado por:<br> '.$nome_func[0].' <br>em '.$data;
				 
				  } else {
					   $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[id_funcionario]'");
				 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
				 $nome_func=explode(' ', $row_funcionario['nome']);
				 $data=date('d/m/Y',strtotime($row_notas['nota_data']));
				 
				 echo 'Cadastrado por '.$nome_func[0].' em '.$data;
				 
				  }
				
				?>
                
                
                </td>
			</tr>

            
		<?php	
		
			if($a==$total_notas_anos){
				?>
                <tr><td colspan="10"><?php print_r($array_assoc2);?></td></tr>
            <?php
            // REPETINDO CODIGO, BACALHAU!!!!
			foreach($array_assoc2 as $id_notas => $array_entradas):
				
			$qr_notas = mysql_query("SELECT * FROM notas WHERE id_notas = '$id_notas'");
			$row_notas = mysql_fetch_assoc($qr_notas);
			
			
			?>
            <tr class="linha_<?php  if($alternateColor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
				
                <td><?= $row_notas['id_notas']  . ' - ' . $duplicado; ?>
                </td>
				<td><?= $nome_parceiro ?></td>
				<td><?= $row_notas['numero']; ?></td>
                <td><?=implode('/',array_reverse(explode('-',$row_notas['data_emissao']))); ?></td>
				<td>R$ <?php 
						$totalizador_valor += str_replace(',','.',$row_notas['valor']);
						echo number_format($row_notas['valor'],2,',','.'); 		
						?>
                </td>
                <td> 
			<?php
            	/*$qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
                    (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
                    INNER JOIN entrada 
                    ON notas_assoc.id_entrada = entrada.id_entrada
                    WHERE notas.id_notas = '$row_notas[id_notas]' AND entrada.status = '2';
                    ");
                $total_entrada = (float) @mysql_result($qr_total,0); 
                $totalizador_repasse += $total_entrada;
                echo number_format($total_entrada,2,',','.');*/?>
                    <img src="imagens/arow.png" />
                    <?php echo $array_entradas[0]; ?>
                </td>
                <td <?php 
                	$vn = (float) str_replace(',','.',$row_notas['valor']);
					if($vn > $total_entrada){
						echo 'class="vermelho"';
					}else if($vn < $total_entrada){
						echo 'class="azul"';
					}
				?>>R$
                	<?php 
					$diferenca = (float) $total_entrada - str_replace(',','.',$row_notas['valor']) ;
					$totalizador_diferenca += $diferenca; 
					echo number_format($diferenca,2,',','.');
					?>
                </td>
				
                <td>
                <?php 
				/*$qr_imagem	=	mysql_query("SELECT notas.id_notas, notas_files.id_file, notas_files.tipo, notas_files.status
									FROM notas
									INNER JOIN notas_files ON notas_files.id_notas = '$row_notas[id_notas]'");
				$row_imagem	=	mysql_fetch_assoc($qr_imagem);*/
				?>
                <a href="visializa_files.php?id_nota=<?php echo $row_notas['id_notas']; ?>" target="_blank" >                     
                <img src="../../imagens/print.png" alt="imprimir" width="50" height="38" />
                </a>
                	     
                </td>
				<td>
					<a href="edicao.php?m=<?php echo $_GET['m'];?>&id=<?php echo $row_notas['id_notas'];?>">
            <img src="../../imagens/editar_projeto.png" alt="Editar nota">
             </a> 
					
				</td>
                
                <?php 
				if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
					?>
                <td>
                	<a href="exclusao.php?m=<?php echo $_GET['m'] ;?>&id=<?php  echo $row_notas['id_notas'];?>">
            <img src="../../imagens/lixo.gif" alt="Excluir nota"/></a>
                </td>
                
                <?php }?>
                
                <td>
                <?php
				
				  if($row_notas['ultima_edicao']!='0000-00-00 00:00:00'){
					  $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[editado_por]'");
				 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
				 $nome_func=explode(' ', $row_funcionario['nome']);
				 
				 $data=date('d/m/Y',strtotime($row_notas['ultima_edicao']));
				 echo 'Editado por:<br> '.$nome_func[0].' <br>em '.$data;
				 
				  } else {
					   $qr_funcionario=mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[id_funcionario]'");
				 $row_funcionario=mysql_fetch_assoc($qr_funcionario);
				 $nome_func=explode(' ', $row_funcionario['nome']);
				 $data=date('d/m/Y',strtotime($row_notas['nota_data']));
				 
				 echo 'Cadastrado por '.$nome_func[0].' em '.$data;
				 
				  }
				
				?>
                
                
                </td>
			</tr>
            <?php endforeach;?>	
                
                <?php 
				
							
								echo '<tr class="secao">
											<td>Total:</td>
											<td></td>
											<td></td>
											<td></td>
											<td>'.'R$ '.number_format($total_ano,2,',','.').'</td>
											<td>'.'R$ '.number_format($totalizador_repasse_anos,2,',','.').'</td>
											<td>'.'R$ '.number_format($total_diferenca_anos,2,',','.').'</td>
											<td></td>
											<td></td>
											<td></td>';
											
						
              if(in_array($_COOKIE['logado'],$acesso_exclusao)) { 
				
					echo '<td></td>';
				}
				echo '</tr>';
											
								unset($ano_anterior,$total_ano,$a,$totalizador_repasse_anos,$diferenca_anos,$total_diferenca_anos );
							}
		
		endwhile;// Notas
		
		?>

        <tr >
            	<td width="10%" ></td>
                <td width="5%"></td>
                <td width="5%"></td>
                <td width="8%">Total do projeto:</td>
                <td width="15%"><?php echo 'R$ '.number_format($totalizador_valor,2,',','.');  ?></td>
                <td width="13%"><?php echo 'R$ '.number_format($totalizador_repasse,2,',','.'); ?></td>
                <td  width="15%"><?php echo 'R$ '.number_format($totalizador_diferenca,2,',','.'); ?></td>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                
                <td></td>
              
            </tr>           
        </table>
    
       
        </td>
      </tr>
    </table>

	 <?php 
		  $regiao_anterior = $row_projeto['id_regiao'];
		  $status_anterior = $row_projeto['status_reg'];
		 
		unset($totalizador_valor,$totalizador_repasse,$totalizador_diferenca);
			
		} ///fim if empty notas
	
	
	 endwhile; // FIM DO LOOP DOS PROJETOS 
	
	unset($projetos);

} // Fim do Loop dos Status

?>

    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('../include/rodape.php'); ?>
    </div>
</div>
</body>
</html>