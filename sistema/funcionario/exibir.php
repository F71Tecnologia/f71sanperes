<!DOCTYPE html>
<?php

include_once '../../conn.php';

//TABELA FUNCIONARIO_ASSOC

//DELETANDO todos os registros da TABELA funcionario_acoes_assoc onde id_funcionario foi selecionado na página form_usuario
mysql_query("DELETE FROM funcionario_acoes_assoc WHERE id_funcionario = {$_REQUEST['id_funcionario']}") or die('1'.mysql_error());
mysql_query("DELETE FROM botoes_assoc WHERE id_funcionario = {$_REQUEST['id_funcionario']}") or die('2'.mysql_error());

//DELETANDO todos os registros da TABELA funcionario_regiao_assoc onde id_funcionario foi selecionado na página form_usuario
mysql_query("DELETE FROM funcionario_regiao_assoc WHERE id_funcionario = {$_REQUEST['id_funcionario']}") or die('3'.mysql_error());

//TABELA BOTOES

//SELECIONANDO na TABELA botoes os campos botoes_id e botoes_nome
$exibir = mysql_query("SELECT botoes_id, botoes_nome FROM botoes") or die('4'.mysql_error());
$exibir_consulta = mysql_num_rows($exibir);

/*WHILE para fazer um looping para INSERIR na TABELA botoes_assoc os VALORES botoes_id e id_funcionario 
E INSERT DENTRO da TABELA botoes_assoc os VALORES botoes_id e id_funcionario*/
while ($row_armazena = mysql_fetch_assoc($exibir)){
    mysql_query("INSERT INTO botoes_assoc (botoes_id, id_funcionario) VALUES ({$row_armazena['botoes_id']}, {$_REQUEST['id_funcionario']})") or die('5'.mysql_error());
}
 
//TABELA ACOES

//SELECIONANDO acoes_id na TABELA ACOES
$exibir_acoes = mysql_query("SELECT acoes_id FROM acoes ") or die('6'.mysql_error());
$exibir_acoes_consulta = mysql_num_rows($exibir_acoes);

//WHILE para fazer um looping para INSERIR na TABELA funcionario_acoes_assoc os VALORES id_funcionario e acoes_id
while ($row_armazena_acoes = mysql_fetch_array($exibir_acoes)){
    mysql_query("INSERT INTO funcionario_acoes_assoc (id_funcionario, acoes_id) VALUES ({$_REQUEST['id_funcionario']}, {$row_armazena_acoes['acoes_id']})") or die('7'.mysql_error());
}

//TABELA FUNCIONARIO_REGIAO_ASSOC
//SELECIONANDO na TABELA funcionario_regiao_assoc os VALORES id_funcionario e id_regiao
$exibir_funcionario_regiao_assoc = mysql_query("SELECT id_regiao FROM regioes") or die('8'.mysql_error());
$exibir_funcionario_regiao_assoc_consulta = mysql_num_rows($exibir_funcionario_regiao_assoc);

//WHILE para fazer um looping para INSERIR na TABELA funcionario_regiao_assoc os VALORES id_funcionario e id_regiao
while($row_armazena_funcionario_regiao_assoc = mysql_fetch_array($exibir_funcionario_regiao_assoc)){
//    echo "INSERT INTO funcionario_regiao_assoc (id_funcionario, id_regiao) VALUES ({$_REQUEST['id_funcionario']}, {$row_armazena_funcionario_regiao_assoc_consulta['id_regiao']})";
    mysql_query("INSERT INTO funcionario_regiao_assoc (id_funcionario, id_regiao) VALUES ({$_REQUEST['id_funcionario']}, {$row_armazena_funcionario_regiao_assoc['id_regiao']})") or die('9'.mysql_error());
}
 
header('Location: index.php');
?>
