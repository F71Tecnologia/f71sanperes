<?php
include('../../conn.php');
include('../../classes/FuncoesClass_1.php');
include('../../wfunction.php');
require_once ('../../classes/LogClass.php');

$log = new Log();
$usuario = carregaUsuario();

$nome = $_GET['nome'];

$del_curso = FuncoesClass::alteraStatusCursoByNome($nome, $usuario);

$log->log('2',"Curso desativado: $nome",'curso');
?>