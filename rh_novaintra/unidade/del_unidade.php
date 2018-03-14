<?php
include('../../conn.php');
include('../../classes/UnidadeClass.php');
include('../../wfunction.php');
require_once ('../../classes/LogClass.php');

$usuario = carregaUsuario();
$log = new Log();

$id_unidade = $_GET['id'];

$antigo = $log->getLinha('unidade',$id_unidade);
$del_uni = alteraStatusUnidade($id_unidade, $usuario);
$novo = $log->getLinha('unidade',$id_unidade);

$log->log('2', "Unidade ID $id_unidade excluida",'unidade',$antigo,$novo);
?>