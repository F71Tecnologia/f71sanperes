<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
	include('../../conn.php');
	
	$query = "
		SELECT notas_assoc.id_notas, notas_assoc.id_entrada FROM notas INNER JOIN notas_assoc USING(id_notas)
			WHERE notas_assoc.id_entrada IN(
			SELECT id_entrada 
			FROM notas_assoc
			GROUP BY id_entrada
			HAVING COUNT(*) > 1
		)
		AND notas.id_projeto = '3232' 
		AND notas.status = '1'
		ORDER BY notas_assoc.id_entrada	
	";
	$result = mysql_query($query);
	$aux = 0;
	while($row = mysql_fetch_array($result)):
		$id_entrada = $row['id_entrada'];
		
		if ($aux != $id_entrada)
		{
			echo "---------"."<br/>";
			
			
		}else{
		print $row['id_notas']." - ".$row['id_entrada']."<br/>";	
		}
		
		$aux = $id_entrada;
		 
		
		
	endwhile;
?>
</body>
</html>