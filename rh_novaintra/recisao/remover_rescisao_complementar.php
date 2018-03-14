<?php 
require_once("../../classes/LogClass.php");
require_once('../../conn.php');
extract($_POST);

$id_rescisao_comp = $id;

$log = new Log();

$sql = "UPDATE rh_recisao SET status = 0 WHERE id_recisao = {$id_rescisao_comp}";
$antigo = $log->getLinha("rh_recisao",$id_rescisao_comp);
mysql_query($sql);
$novo = $log->getLinha("rh_recisao",$id_rescisao_comp);

$log->log(2,"Desprocessou a Rescisão Complementar $id_rescisao_comp","rh_recisao",$antigo,$novo);

$redirect = "nova_rescisao.php";
header("location:$redirect");
