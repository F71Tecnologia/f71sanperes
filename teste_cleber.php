<?php
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php

include "conn.php";

$result01 = mysql_query("SELECT * FROM rh_clt WHERE sexo = 'F' AND id_regiao = 4");
$result02 = mysql_query("SELECT * FROM rh_clt WHERE sexo = 'M' AND id_regiao = 4");

$cont01 = mysql_num_rows($result01);
$cont02 = mysql_num_rows($result02);

$TotalDeRegistros = $cont01 + $cont02;
echo "Foram selecionados ".$TotalDeRegistros." registros."."</br>";
echo $cont01." do sexo feminino e ".$cont02." do sexo Masculino."."</br>";
$cont = "0";
while ($row01 = mysql_fetch_array($result01, MYSQL_ASSOC)) {
	if($cont % 2){$cor = "#EEF4DF";}else{$cor = "#FFFFFF";}
	echo "<div style='float:left; whith:100%; background-color:$cor'>";
	echo "<div style='float:left; width:400px'>".$row01['nome']."</div>";
	echo "<div style='float:left; width:200px'>".$row01['cpf']."</div>";
	echo "<div style='float:left; width:50px'>".$row01['sexo']."</div>";
	echo "</div>";
	$cont ++;
}

echo "</br></br></br>";
while ($row02 = mysql_fetch_array($result02, MYSQL_ASSOC)) {
	echo "<div style='float:left; whith:100%'>";
	echo "<div style='float:left; width:400px'>".$row02['nome']."</div>";
	echo "<div style='float:left; width:200px'>".$row02['cpf']."</div>";
	echo "<div style='float:left; width:50px'>".$row02['sexo']."</div>";
	echo "</div>";
}
?>
</body>
</html>