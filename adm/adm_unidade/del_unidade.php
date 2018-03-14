<?php
include('../../conn.php');
include('../../classes/UnidadeClass.php');
include('../../wfunction.php');

$usuario = carregaUsuario();

$id_unidade = $_GET['id'];

$del_uni = alteraStatusUnidade($id_unidade, $usuario);
?>