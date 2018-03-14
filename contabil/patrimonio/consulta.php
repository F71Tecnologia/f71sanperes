<?php

$nome = $_REQUEST["nome"];
$regiao = $_REQUEST["regiao"];
$id_projeto = $_REQUEST["projeto"];

if($nome != "" ){

//echo $nome;

include_once '../../conn.php';

$sql = "SELECT *
        FROM rh_clt 
        WHERE nome LIKE '".$nome."%' 
        AND id_regiao = '".$regiao."'
        AND id_projeto = '".$id_projeto."'
        ORDER BY nome";

$resultado_nome = mysql_query($sql);

    if(mysql_num_rows($resultado_nome) > 0){
        while($option = mysql_fetch_array($resultado_nome)){

            echo "<input id='regiao' type='radio' name='id_clt' value='{$option['id_clt']}'>   {$option['nome']}<br>";

        }
    }else{
        echo "Nenhum Funcionario encontrado!";
    }   
}else{
    echo "Digite o nome do Funcionario!";   
} 
?>