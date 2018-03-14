<?php
include('../../conn.php');
include('../../funcoes.php');
include "../../classes/FolhaClass.php";

$folha = $_GET['folha'];

$qr_folha  = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '$folha'");
$row_folha = mysql_fetch_assoc($qr_folha);

$total_participantes = mysql_num_rows(mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '3'"));

$movimentos = $row_folha['ids_movimentos_update'];
$array_movimentos = explode(',',$movimentos);
$total_movimentos = count($array_movimentos);

mysql_query("UPDATE rh_folha SET status = '2' WHERE id_folha = '$folha' LIMIT 1");
mysql_query("UPDATE rh_folha_proc SET status = '2' WHERE id_folha = '$folha' AND status = '3' LIMIT $total_participantes");
if(!empty($array_movimentos[0])) {
	mysql_query("UPDATE rh_movimentos_clt SET status = '1', id_folha = '' WHERE id_movimento IN($movimentos) LIMIT $total_movimentos");
}

$linkreg = str_replace('+','--',encrypt($row_folha['regiao']));

//LOG PARA FECHAR FOLHA
$folhas = new Folha();
$folhas->logDesprocessaFolha($folha, $_COOKIE['logado']);


header("Location: folha.php?enc=$linkreg&tela=1");
exit();
?>