<?php

include_once("../../../conn.php");
include_once("../model/model.php");

$funcionario = new Empresa();
$func = $funcionario->getParticipantes();

$dadosFuncionario = array();
while($dados = mysql_fetch_assoc($func)){
    $dadosFuncionario[$dados['id_funcionario']]["id"] = $dados['id_funcionario'];
    $dadosFuncionario[$dados['id_funcionario']]["nome"] = $dados['nome'];
}