<?php 


$xml = new XMLReader();
$xml->open("layout.xml");
while ($xml->read()) {
	echo $xml->localName ."<br />";
}
/*
class txt {
	
	private $arquivo,$fp;
	public $layout;
	
	function txt($arquivo){
		$this->arquivo = $arquivo;
		$fp = fopen($arquivo,"r");
		$this->fp = $fp;
		$this->layout = (string) fread($fp, filesize($arquivo)); 
	}
	
	function ler_etiqueta(){
		mysql_query("SELECT * FROM SAIDA WHERE ID_SAIDA");		
	}
	
	
}

$fp = fopen("layout.txt", "r");
$texto = fread($fp, filesize("layout.txt"));
fclose($fp);
echo $texto;

$obj =  new txt('layout.txt');
echo $obj->layout;
*/?>