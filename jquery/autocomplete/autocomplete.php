<?php
include ('../../conn.php');
/**
 * AutoComplete Field - PHP Remote Script
 *
 * This is a sample source code provided by fromvega.
 * Search for the complete article at http://www.fromvega.com
 *
 * Enjoy!
 *
 * @author fromvega
 *
 */



// check the parameter
if(isset($_GET['part']) and $_GET['part'] != '')
{
	// initialize the results array
	$results = array();
	
	$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE nome LIKE '%$_GET[part]%'");
	while($row_clt = mysql_fetch_assoc($qr_clt)):
	
	if($row_clt['nome'] != NULL)
	$results[] = '<a href=cad_processo_clt.php?clt='.$row_clt['id_clt'].'&projeto='.$row_clt['id_projeto'].'&regiao='.$row_clt['id_regiao'].'&quot;>'.$row_clt['nome'].'</a>';
	
	
	endwhile;

	

	// return the array as json with PHP 5.2
	echo json_encode($results);

	// or return using Zend_Json class
	//require_once('Zend/Json/Encoder.php');
	//echo Zend_Json_Encoder::encode($results);
}