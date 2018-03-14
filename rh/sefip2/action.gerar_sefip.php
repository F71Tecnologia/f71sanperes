<?php
include('../include/restricoes.php');
include('../../conn.php');

$id_regiao = $_GET['regiao'];
$id_projeto = $_GET['projeto'];



if(isset($_GET['ajax']) and $_GET['ajax'] == 1 ){
	
		echo '<option value=""> Selecione uma região...</option>
			  <option value=""> </option>
			  <option value="todos"> TODOS </option> ';
		
		$qr_projeto  = mysql_query("SELECT * FROM  projeto WHERE id_regiao = '$id_regiao'");
		while($row_projeto = mysql_fetch_assoc($qr_projeto)): 	
			
			echo '<option value="'.$row_projeto['id_projeto'].'">'.htmlentities($row_projeto['nome']).'</option> ';
			
			
		endwhile;	
}




if($_GET['ajax'] and $_GET['ajax'] == 2 ){


$mes_anterior = array();
$ano_anterior = array();

$qr_folha = mysql_query("SELECT   DISTINCT(mes), ano, nome_mes  
						FROM rh_folha 
						INNER JOIN ano_meses 
						ON ano_meses.num_mes = rh_folha.mes
						WHERE regiao IN($id_regiao) AND projeto IN($id_projeto) AND status = 3 ORDER BY `rh_folha`.`mes` ASC");
while($row_folha = mysql_fetch_assoc($qr_folha)):

	

	if(!in_array($row_folha['mes'], $mes_anterior)) { 
			
			$json['mes'] .= '<option value="'.$row_folha['mes'].'">'.htmlentities($row_folha['nome_mes']).'</option> '; 
	
	
	}
	
	
	
	if(!in_array($row_folha['ano'], $ano_anterior)) { 	$json['ano'] .= '<option value="'.$row_folha['ano'].'">'.$row_folha['ano'].'</option> ';	}
	
	$ano_anterior[] = $row_folha['ano'];
	$mes_anterior[] = $row_folha['mes'];


endwhile;


echo json_encode($json);

	
}





?>
