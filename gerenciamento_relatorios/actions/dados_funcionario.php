<?php

$campos = implode(',',$array_campos[$tipo_contratacao]); 




///Criando as colunas com os nomees dos campos
echo '<table border="1" id="dados" style="display:none;">';

echo '<tr><td colspan="'.sizeof($array_campos).'">DADOS DO FUNCIONÁRIO</td></tr>';

echo '<tr>';
foreach($array_campos[$tipo_contratacao] as $campo){
	echo '<td>'.$nome_campo_dados[$campo].'</td>';							    	
}

echo '</tr>';

$qr_trabalhador = mysql_query("SELECT $campos FROM $tabela_trab WHERE tipo_contratacao = '$tipo_contratacao' $id_regiao $projeto ") or die(mysql_error());
$total_campos = mysql_num_fields($qr_trabalhador);
while($row_trabalhador = mysql_fetch_array($qr_trabalhador)):
	
	echo '<tr>';
		for($i =0;$i<$total_campos; $i++){
			echo '<td>'.$row_trabalhador[$i].'</td>';	
		}
	echo'</tr>';	
		

endwhile;

unset($campo_tabela,$condicao );
echo '</table>';
?>