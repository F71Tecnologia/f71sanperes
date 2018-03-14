<?php
include('../../conn.php');
include('../../classes/EmpresaClass.php');
include('../../wfunction.php');

$usuario = carregaUsuario();

$id_empresa = $_GET['id'];

alteraStatusEmpresa($id_empresa, $usuario);
?>