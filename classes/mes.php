<?php 
include "../conn.php";
class mes{
	
	private $tabela,$campo_chave,$campo_nome,$itens;
	
	function mes($tabela = "ano_meses", $campo_chave = "num_mes", $campo_nome = "nome_mes"){
		$this->tabela = $tabela;
		$this->campo_chave = $campo_chave;
		$this->campo_nome = $campo_nome;
		$sql = 'SELECT '.$this->campo_chave.','.$this->campo_nome.' FROM  '.$tabela.' ORDER BY '.$this->campo_chave;
		$query = @mysql_query($sql);
		while($row = mysql_fetch_assoc($query)){
			
			$this->itens[$row['num_mes']] = $row['nome_mes']; 
		}
	}
	
	function getItens(){
		return $this->itens;
	}
}
?>