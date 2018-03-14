<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<?php
include('../../conn.php');

// auto complete
$q = strtolower($_GET["q"]);

$sql = "SELECT * FROM rh_cbo WHERE nome LIKE '$q%'";
$rsd = mysql_query($sql);

while($rs = mysql_fetch_array($rsd)) {    
    echo "{$rs['nome']} - {$rs['cod']}\n";
}
?>