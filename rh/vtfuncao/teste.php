<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Teste</title>
</head>

<body>
<?
include "../../conn.php";
include "dias_trab.php";

$obj = new dias_trab();
$arrayID = array('518');
$quant = count ($arrayID);

$iniPeriodo = '2010-08-01';
$fimPeriodo =  '2010-08-30';

echo 'Data de Inicio '.$iniPeriodo.', data de fim '.$fimPeriodo.'';
print "<hr color=red>";

for ($i = 0; $i<$quant; $i++){
	$obj -> calcperiodo($iniPeriodo, $fimPeriodo,$arrayID[$i]);
	echo $obj->nome.'<br>';
	echo $obj->funcao.'<br>';
	
	$teste = $obj ->imprimir();
	print $teste.' dias<br><br>';
	print $obj ->imprimir_valor();
}
?>
</body>
</html>