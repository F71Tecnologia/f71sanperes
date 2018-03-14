<?php
include('../include/restricoes.php');
include('../../conn.php');
//include('../include/criptografia.php');

$parceiro = $_REQUEST['id'];

mysql_query("UPDATE parceiros SET parceiro_status=0, parceiro_atualizacao=NOW(),  parceiro_id_atualizacao='$_COOKIE[logado]' WHERE parceiro_id = '$parceiro'") or die(mysql_error());


if(isset($_REQUEST['exclui'])){
    echo json_encode(array('status'=>'1'));
    exit;
}

header("Location: index.php?m=$link_master");