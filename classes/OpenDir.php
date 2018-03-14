<?php
class OpenDir{
	private $diretorio,$tipo,$quantidade;
	private $ponteiro;
	public $arquivos,$pastas,$itens;
	
	const ERRO_OPEN_DIR = "Erro ao abrir diretorio, verifique se o caminho esta correto!";
	
	function OpenDir(){
	}
	
	public function abra($diretorio = NULL){
		if(empty($diretorio)){
			$caminho = getcwd();
		}else{
			$caminho = getcwd()."/".$diretorio; 
		}
		$this->ponteiro = opendir($caminho)or die(self::ERRO_OPEN_DIR);
		return $this->ponteiro;
	}
	
	public function leia(){
		while ($nome_itens = readdir($this->ponteiro)) {
			$this->itens[] = $nome_itens;
		}
		sort($this->itens);
		return $this->itens;
	}
	
	public function liste($tipos = array(), $pasta = NULL){
		
		foreach($this->itens as $iten){
			if($iten!="." && $iten!=".."){
				
				if (is_dir($iten)) {
					if(!isset($pasta)){
						$this->pastas[] = $iten;
					}
					
				} else{ 
					if(!empty($tipos)){
						$part_iten 	= explode('.', $iten);
						$tipo_iten 	= count($part_iten)-1;
						if(in_array($part_iten[$tipo_iten],$tipos)){
							$this->arquivos[] = $iten;
						}
					}else{
						$this->arquivos[] = $iten;
					}
					
				}
				
				$tudo[] = $iten;
				
			}
		}
		
		
		$this->tudo = $tudo;
		return $this->tudo;
	}
	
	
	
	
	
}

?>