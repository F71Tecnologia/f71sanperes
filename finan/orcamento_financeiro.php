<?php
include("../conn.php");
$orcamento_financeiro = $_REQUEST['orcamento_financeiro'];

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "aceitar"){

    $reg1 = mysql_query("UPDATE item_orcamento SET flag = 2 WHERE id_orcamento = '$orcamento_financeiro'") or die(mysql_error());

    echo json_encode($reg1);

}

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "excluir"){

    $reg1 = mysql_query("UPDATE item_orcamento SET flag = 0 WHERE id_orcamento = '$orcamento_financeiro'") or die(mysql_error());

    echo json_encode($reg1);

}
 