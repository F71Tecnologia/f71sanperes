<?php 

class verificacao{
	private $valor;
	
	function limpar($strValor){
		$strValor = str_replace(',','',$strValor);
		$strValor = str_replace('-','',$strValor);
		$strValor = str_replace('.','',$strValor);
		return $strValor;
	}
	
	function inicia(&$str){
		$this->valor = $this->limpar($str);
	}
	
	function numero(){
		if(is_numeric($this->valor)) 
				return true;
			else
				return false;
	}
	
	function quantidade($quant){
		if(strlen($this->valor) == $quant)
			return true;
		else
			return false;
	}
	
	function maxquant($quant){
		if(strlen($this->valor) <= $quant)
			return true;
		else
			return false;
	}
	
	function minquant(){
		if(strlen($this->valor) >= $quant)
			return true;
		else
			return false;
	}
	
	
	
}
?>