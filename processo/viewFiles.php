<?php 
require_once('../conn.php');
$id_pg = $_REQUEST['pg'];
$qr = mysql_query("SELECT * FROM prestador_pg_files WHERE id_pg = '{$id_pg}'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Anexos <?=$id_pg?></title>
</head>
<body>
<?php while($row = mysql_fetch_assoc($qr)):?>
	<img src="comprovantes/<?php echo $row['nome'].'.'.$row['tipo']; ?>" />
    <p>&nbsp;</p>
<?php endwhile;?>
</body>
</html>