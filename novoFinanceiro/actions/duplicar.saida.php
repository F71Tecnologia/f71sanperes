<?php 

include "../../conn.php";

function duplica_saida ($array_colunas, $array_valores, $data_diferente = FALSE){
	
	if($data_diferente != FALSE){
		$array_valores['data_vencimento'] = $data_diferente;
	}
	
	$new_array = array_map(
		create_function('$key, $value', 'return $key." = \'".$value."\' ";'), 
		array_keys($array_valores), 
		array_values($array_valores)
	);
	
	return 'INSERT INTO saida SET ' . implode(' , ',$new_array);
	
}

$id_saida = $_POST['id_saida'];
$quant = $_POST['quant'];
$datas = $_POST['campos_add'];
// Pegando as colunas
$qr_colunas = mysql_query("DESCRIBE saida");
while($row_colunas = mysql_fetch_assoc($qr_colunas)){
	$array_colunas[] = $row_colunas['Field'];
}

// Pegando os dados da saida
$qr_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida'");
$row_saida = mysql_fetch_assoc($qr_saida);

// retirando o id_saida do array
$row_saida['id_saida'] = NULL;
// amanda 
$row_saida['id_user'] = $_COOKIE['logado'];
$sql = array();

if($_POST['datas'] == 'on' and !empty($datas)){
	
	foreach($datas as $data){
		$sql[] = duplica_saida ($array_colunas, $row_saida, implode('-',array_reverse(explode('/',$data))) );
	}

}else{
	
	for($i=0;$i<$quant;$i++){
		$sql[] = duplica_saida ($array_colunas, $row_saida);
	}

}

foreach($sql as $qr){
	mysql_query($qr) or die(mysql_error());
}


echo '<script type="text/javascript">
		parent.window.location.reload();
		if (parent.window.hs) {
			var exp = parent.window.hs.getExpander();
			if (exp) { exp.close(); }
		}
	</script>';


?>