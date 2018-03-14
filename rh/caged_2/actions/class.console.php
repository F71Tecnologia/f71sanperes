<?php
class console{
	private $base,$erro;
	
	function inicia($valor){
		$this->base = $valor;
	}
	
	function quantidade($quant){
		if(strlen($this->base) == $quant){
			return true;
		}else{
			$this->erro[] = "Quantidade de caracteres deve ser igual a $quant.";
		}
	}
	
	function maxquant($quant){
		if(strlen($this->base) <= $quant){
			return true;
		}else{
			$this->erro[] = "Quantidade de caractere maximo é de $quant.";
		}
	}
	function minquant($quant){
		if(strlen($this->base) >= $quant){
			return true;
		}else{
			$this->erro[] = "Quantidade de caractere minima é de $quant.";
		}		
	}
}
?>