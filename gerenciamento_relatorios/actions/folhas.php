<?php

$campos_folha= implode(',',$array_campos_folhas[$tipo_contratacao] );




echo '<table border="1" id="folhas" style="display:none;">';


/*foreach($array_campos_folha_proc[$tipo_contratacao] as $campo2){
	 echo '<td>'.$nome_campo_folha_proc[$campo2].'</td>';							    	
}*/





///Criando as colunas com os nomees dos campos


echo '<tr><td colspan="'.sizeof($array_campos_folhas [$tipo_contratacao]).'">FOLHA</td></tr>';

echo '<tr>';
foreach($array_campos_folhas[$tipo_contratacao] as $campo1){
	 echo '<td>'.$nome_campo_folhas[$campo1].'</td>';							    	
} 

echo '</tr>';


						
$qr_folha		= mysql_query("SELECT $campos_folha FROM $folhas WHERE status_reg = 1 ") or die(mysql_error());

$total_campo_folha  = mysql_num_fields($qr_folha);	
while($row_folha 	= mysql_fetch_array($qr_folha)):


echo '<tr>';
		for($i=0;$i<$total_campo_folha;$i++) {
		
		echo '<td>'.$row_folha[$i].'</td>';
		}
		
echo '</tr>';

endwhile;

unset($campo_tabela,$condicao );
echo '</table>';
?>