<?php

require("../../conn.php");
$charset = mysql_set_charset('utf8');
//id_projeto = '{$_REQUEST['projeto']}'
$query = mysql_query("SELECT * FROM  bancos WHERE id_regiao = '{$_REQUEST['regiao']}' AND interno = '1'AND status_reg = '1'ORDER BY nome");

$json = array();
while ($row = mysql_fetch_assoc($query)) {
    $json[] = $row;
    //json_encode($json);
}
echo json_encode($json);
?>