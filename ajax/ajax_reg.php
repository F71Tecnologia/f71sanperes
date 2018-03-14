<?php

include "../conn.php";
mysql_query ('SET character_set_client=utf8');
mysql_query ('SET character_set_connection=utf8');
mysql_query ('SET character_set_results=utf8');

//EXECUTANDO O AJAX CARREGANDO AS CIDADES DE ACORDO COM A UF SELECIONADA
	
$uf = $_REQUEST['uf'];

$qr_municipios = mysql_query("SELECT * FROM municipios WHERE sigla = '$uf' ORDER BY municipio");

$retorno = '&nbsp;&nbsp;<select name="cidade" id="cidade">'."\n";

while($row_municipios = mysql_fetch_array($qr_municipios)){
	$retorno .= '<option value="'.$row_municipios['municipio'].'">'.$row_municipios['municipio'].'</option>'."\n";
}

$retorno .= '</select>'."\n";
	
echo $retorno;

?>