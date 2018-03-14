<?php
include "conn.php";
include "wfunction.php";
$usuario = carregaUsuario();//print_r($usuario);
$arraySimula = array(9 => '', 87 => '', 158 => '', 179 => '', 202 => '', 255 => '', 256 => '', 257 => '', 258 => '', 259 => '', 260 => '');
if(array_key_exists($_COOKIE[logado], $arraySimula)){
    if(!empty($_COOKIE[logado]) AND !empty($_REQUEST['funcionario']) AND empty($_COOKIE['simulado'])){
        $sqlLog = "INSERT INTO log 
        (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES 
        ('{$usuario['id_funcionario']}', '{$usuario['id_regiao']}', '{$usuario['tipo_usuario']}', '{$usuario['grupo_usuario']}', 'Simulação de Usuário', NOW(), '{$_SERVER['REMOTE_ADDR']}', 'Simulação de usuário {$_REQUEST['funcionario']}');";
        mysql_query($sqlLog);
        setcookie("simulado", $_COOKIE['logado']);
        setcookie("logado", $_REQUEST['funcionario']);
    }/*else if(!empty($_COOKIE[simulado])){
        setcookie("logado", $_COOKIE['simulado']);
        unset($_COOKIE[simulado]);
        setcookie('simulado', '', time() - 3600);
    }*/
}
//print_r($_COOKIE);
header("Location: index.php");