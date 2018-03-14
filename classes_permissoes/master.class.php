<?php
class Master{

public function Preenhe_master($id_master){
			
				$qr_master = mysql_query("SELECT DISTINCT(funcionario_regiao_assoc.id_master), master.nome FROM master 
										 INNER JOIN funcionario_regiao_assoc
										 ON master.id_master = funcionario_regiao_assoc.id_master
										 WHERE master.status = '1'  AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]' ") or die(mysql_error());		
				while($row_master = mysql_fetch_assoc($qr_master)):
				
					$selected = ($id_master == $row_master['id_master'])? 'selected="selected"': '';
					echo '<option value="'.$row_master['id_master'].'" '.$selected.'>'.$row_master['nome'].'</option>';
				 
				endwhile;	
						
			
		
}


}

?>