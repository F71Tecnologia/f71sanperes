<?php
include('../conn.php');

$pesquisa = $_GET['q'];

if(!empty($pesquisa)){
$qr_curso = mysql_query("SELECT * FROM rh_cbo WHERE nome LIKE '%$pesquisa%' ") or die(mysql_error());
	
	if(mysql_num_rows($qr_curso)) {
		
		while($row_curso = mysql_fetch_assoc($qr_curso)):
		echo "<a href='#' class='resposta_cbo' onclick='inserir_cbo(".$row_curso['id_cbo'].", this.innerHTML);'>".$row_curso['cod']." - ".htmlentities($row_curso['nome'])."</a>";
	endwhile;
	
	} else {
		
		echo 'Nenhum registro encontrado.';	
	}
	
	
} else {
echo 'Digite o nome do cargo.';	
}

?>