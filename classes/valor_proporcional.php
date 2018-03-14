<?php 
class proporcional {
	
	public $valor_dia, $valor_proporcional,$valor_hora,$valor_proporcional_hora;
	
	function calculo_proporcional($valor, $dias) {
	      $this->valor_dia          = $valor / 30;
 		  $this->valor_proporcional = round($this->valor_dia * $dias, 2);
	}
	
	
	
	
}
?>