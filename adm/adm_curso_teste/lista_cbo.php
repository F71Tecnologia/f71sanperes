<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php
include('../../conn.php');

// auto complete
$q = strtolower($_GET["q"]);

mysql_query("SET NAMES 'utf8'");

$sql = "SELECT * FROM rh_cbo WHERE nome LIKE '$q%'";
$rsd = mysql_query($sql);
$tot = mysql_num_rows($rsd);

if($tot != 0){
    while($rs = mysql_fetch_array($rsd)) {
        $nome = utf8_decode($rs['nome']);
        echo $nome. " - " . $rs['cod'] . "\n";
    }
}else{
    echo "Nenhum registro encontrado";
}
?>