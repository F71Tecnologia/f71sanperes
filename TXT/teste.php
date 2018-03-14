<?php 

class txt {
	
	private $arquivo,$fp;
	public $layout;
	public $node;
	
	function txt($arquivo){
		$this->arquivo = $arquivo;
		$fp = fopen($arquivo,"r");
		$this->fp = $fp;
		$this->layout = (string) fread($fp, filesize($arquivo));
		$this->CriaNodes();
	}
	
	
	function CriaNodes(){
		$node = explode('{{',$this->layout); 
		foreach($node as $no){
			if(empty($no)) continue;
			$this->node[] = str_replace('}}','',$no);	
		}
	}
	
	function ler_etiqueta(){
		
	}
	
	
}
$obj =  new txt('layout.txt');
//echo $obj->layout;
echo '<pre>';
print_r($obj->node);
?>