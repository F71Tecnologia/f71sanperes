<?php
class Botoes {

private $sql;
private $row;


	public function verifica_permissao($botoes_id){
	
		$verifica_botao = mysql_num_rows(mysql_query("SELECT * FROM botoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND botoes_id  = '$botoes_id'"));
		if($verifica_botao != 0) { return true;} else { return false;}
				
	}
	
	
}

?>