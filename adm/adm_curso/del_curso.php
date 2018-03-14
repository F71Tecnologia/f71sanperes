<?php
include('../../conn.php');
include('../../classes/FuncoesClass.php');
include('../../wfunction.php');

$usuario = carregaUsuario();

$id_curso = $_GET['id'];

$del_curso = FuncoesClass::alteraStatusCurso($id_curso, $usuario);
?>