<?php
class caged {
	public $arquivo; 
	
	function dados($dados){
		$this->arquivo .= $dados;
	}
	function filler($N){
		for($i=0;$i<$N;$i++){
			$this->arquivo .= " ";
		}
	}
	function fechalinha(){
		$this->arquivo .= "\r\n";
	}
	
	function completa($str,$n,$caractere = " ",$lado = 'depois'){
		if(strlen($str) > $n){
			return sprintf('%0'.$n.'d',$str);
		}
		$quant 	= strlen($str);
		$total 	= $n - $quant;
		$str_final = $str;
		for($i=0;$i<$total;$i++){
			
				$complementos .= $caractere;
		
		}
		if($lado == 'depois'){
			$str_final .= $complementos;
		}else{
			$str_final = $complementos.$str_final;
		}
		return $str_final;
	}
}
?>