<?php

header("Content-Type: text/html;  charset=ISO-8859-1",true); 
include "../conn.php";

//ESTE AJAX É ESPECIFICO PARA FAZER UPDATES DINAMICOS USANDO AS VARIAVEIS ENVIADAS

//RECEBENDO AS VARIAVEIS

$tabela = $_REQUEST['tabela'];
$valor = $_REQUEST['valor'];
$campo = $_REQUEST['campo'];
$nomeid = $_REQUEST['nomeid'];
$id = $_REQUEST['id'];
$tipo = $_REQUEST['tipo'];

/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/
function ConverteData($Data){
    if (strstr($Data, "/")){
        $d = explode ("/", $Data);//tira a barra
        $rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
        return $rstData;
    } elseif(strstr($Data, "-")){
        $d = explode ("-", $Data);
        $rstData = "$d[2]/$d[1]/$d[0]"; 
        return $rstData;
    }else{
        return "0";
    }
}
//$data_rg = ConverteData($data_rg);
if($tipo == 1){	//CASO SEJE NORMAL, VAI CONTINUAR COMO VEIO
    $valor = $valor;
}else if($tipo == 2){//CASO SEJE VAORES VAMOS REMOVER OS PONTOS, E POR A VIRGULA
    $valor = str_replace(".","",$valor);
    $valor = str_replace(",",".",$valor);
}else if($tipo == 3){//CASO SEJE DATA, VAMOS FORMATALAS PARA Y-m-d
    $valor = ConverteData($valor);
}

$sql = "UPDATE $tabela SET $campo = '$valor' WHERE $nomeid = '$id'";
mysql_query($sql) or die ("ERRO");

?>
