<?php

class Projeto {
	
private $sql;
private $row;






public function Preenhe_select_por_master($id_master, $id_projeto = NULL){
	
		if($id_projeto) { $id_projeto = $id_projeto;  } else {	$id_projeto = ''; }	
		
		
	
		$array_status = array(1 => 'PROJETOS ATIVOS', 0 => 'PROJETOS INATIVOS');
				
		foreach($array_status as $status => $nome_status) {
			
		
			
			if($status == 0) {
				$qr_projeto = mysql_query("SELECT projeto.nome, projeto.id_projeto 
										 FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										INNER JOIN  projeto
										ON projeto.id_regiao = regioes.id_regiao
										
										WHERE (regioes.status = '$status' OR regioes.status_reg = '$status') 
										AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
										AND funcionario_regiao_assoc.id_master = '$id_master'
										AND projeto.status_reg = $status
										GROUP BY  projeto.id_projeto") or die(mysql_error());
			} else {
				$qr_projeto = mysql_query("SELECT projeto.nome, projeto.id_projeto  FROM regioes 
										 INNER JOIN funcionario_regiao_assoc
										 ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao
										 INNER JOIN  projeto
										ON projeto.id_regiao = regioes.id_regiao
										WHERE regioes.status = '$status' 
										AND regioes.status_reg = '$status'	 
										AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
										AND funcionario_regiao_assoc.id_master = '$id_master'
										AND projeto.status_reg = $status") or die(mysql_error());
			}
			
			
			if(mysql_num_rows($qr_projeto) != 0){
			
			echo '<option value=""></option>';	
			echo '<optgroup label="'.$nome_status.'">';			
				
				
				while($row_projeto = mysql_fetch_assoc($qr_projeto)):				
				
					$selected = ($id_projeto == $row_projeto['id_projeto'])? 'selected="selected"': '';
					echo '<option value="'.$row_projeto['id_projeto'].'" '.$selected.' >'.$row_projeto['id_projeto'].' - '.$row_projeto['nome'].'</option>';				
				 
				endwhile;	
										
			echo '</optgroup>';
				
			}
				
				
		}
}







}


?>