<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');


include('array_tabelas.php'); ////ARRAys com os nomes dos campos	

function array_campos($query){
	
	$num_campos = mysql_num_fields($query);
	for($i=0; $i<$num_campos; $i++){ $campos[] = mysql_field_name($query,$i); 	}
	
	return $campos;
}



$array_tp_contratacao   = explode('-', $_GET['tp_contratacao']);
$array_dados_relatorios = explode('-', $_GET['dados_rel']);


foreach($array_tp_contratacao as $tipo_contratacao):
	
	
  		include('tp_tabelas_contratacao.php');		
	
		foreach($array_dados_relatorios as $relatorio):
				
			if($relatorio == 1) {
			
					$qr_trab = mysql_query("SELECT * FROM $tabela_trab WHERE  tipo_contratacao = '$tipo_contratacao' AND status = 1") 
					or die(mysql_error()); 
					
					
						
						$campos_trab = array_campos($qr_trab); 					
						
						echo '<div class="box_campos">';
						echo '<h2>'.htmlentities($nome_tp_contrato).'</h2>';
						
							foreach($campos_trab as $chave => $campos) {
								
								if($nome_campo_dados[$campos] =='')continue;
								
								echo '<div class="campos">';
									echo '<input type="checkbox" name="campos['.$tipo_contratacao.'][]" value = "'.$campos.'"  class="'.$tipo_contratacao.'_'.$chave.'" />';						
									echo $nome_campo_dados[$campos];						
								echo '</div>';
							}
					
						echo '</div>';
			}
			
			
			if($relatorio == 2) {
				
				////Folha e rh_folhas
				echo '<div class="box_campos">';
				echo '<h2>Folha - '.htmlentities($nome_tp_contrato).'</h2>';				
						$qr_folha = mysql_query("SELECT * FROM $folhas");
						$campos_folhas = array_campos($qr_folha);
						
						
						foreach($campos_folhas as $chave => $c_folhas){
							
							if($nome_campo_folhas[$c_folhas] == '')continue;
							echo '<div class="campos">';
								echo '<input type="checkbox" name="campos_folha['.$tipo_contratacao.'][]" value = "'.$c_folhas.'"  class="'.$tipo_contratacao.'_'.$chave.'" />';
									echo $nome_campo_folhas[$c_folhas];						
							echo '</div>';
							
						}
				echo '</div>';
			
				///FOLHAS PROCESSADAS COM OS PARTICIPANTES
				echo '<div class="box_campos">';
				echo '<h2>Folha processada- '.htmlentities($nome_tp_contrato).'</h2>';	
							
					$qr_folha = mysql_query("SELECT * FROM $folha_proc");
					$campos_folhas_proc = array_campos($qr_folha);				
					
					foreach($campos_folhas_proc as $chave => $c_folhas_proc){
						
				     if($nome_campo_folha_proc[$c_folhas_proc] == '')continue;
						
						echo '<div class="campos">';
						echo '<input type="checkbox" name="campos_folha_proc['.$tipo_contratacao.'][]" value = "'.$c_folhas_proc.'"  class="'.$tipo_contratacao.'_'.$chave.'" />';	
								
								echo $nome_campo_folha_proc[$c_folhas_proc];	
							
						echo '</div>';
					}
			
			echo '</div>';
			}
			unset($campos_folhas_proc, $campos_folhas, $campos_trab);
		endforeach;	
	
endforeach;
		
echo '<div style="clear:left;"></div>';
echo '<input type="submit" name="enviar" value="GERAR RELATÓRIO"/>';


?>