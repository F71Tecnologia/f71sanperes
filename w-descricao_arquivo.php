<?php

// ------------------------------- GERANDO O CONTETUDO DO ARQUIVO -----------------------------------//
 //------------------ ARQUIVO BRADESCO -----------------
if($row_banco['id_nacional'] == "237"){              

$espaco1 = sprintf("% 21s","");

$espaco2 = sprintf("% 8s","");
$espaco61 = sprintf("% 61s","");

$salario_arquivo = str_replace(",", "", $row['salario']);
$salario_arquivo = sprintf("%013d", $salario_arquivo);

$nome = sprintf("% -40s", $row2['nome']);
$nome_bradesco = sprintf("% -38s", $row2['nome']);
$nome = $nome.$ai_ai;

$agencia = str_replace("-", "", $row2['agencia']);
$agencia = sprintf("%05s",$agencia);

$conta = str_replace("-", "",$row2['conta']);
$conta = sprintf("%08s", $conta);

$valores = sprintf("%09d", $valor_3);
$cont_linha = sprintf("%06d", $cont_arquivo);

$linha = $linha."1".$espaco61.$agencia."12345".$conta."  ".$nome_bradesco.$codigo.$salario_arquivo."298                                                          ".$cont_linha."\r\n";

}elseif($row_banco['id_nacional'] == "356"){
// ---------------------------------------ARQUIVO BANCO REAL ----------------------------------------

$salario_arquivo = str_replace(",", "", $row['salario']);
$salario_arquivo = sprintf("%013d", $salario_arquivo);

$nome = str_split($row2['nome'], 40);
$nomeT = sprintf("% -40s", $nome[0]);

$banco_agencia = sprintf("%04s", $row_banco['agencia']);
$banco_conta = sprintf("%07s", $row_banco['conta']);

$agencia = str_replace("-", "", $row2['agencia']);
$agencia = sprintf("%04s",$agencia);

$conta = str_replace("-", "",$row2['conta']);
$conta = str_replace("x", "X", $conta);
$conta = sprintf("%08s", $conta);

$valores = sprintf("%09d", $valor_3);
$cont_linha = sprintf("%06d", $cont_arquivo);

$salario_arquivo = str_replace(",", "", $row['salario']);
$salario_arquivo = sprintf("%013d", $salario_arquivo);

$d = explode("/",$data_pg);
$data_paga = date("dmy", mktime(0, 0, 0, $d[1], $d[0], $d[2]));

$nome = sprintf("% -40s", $row2['nome']);

$linha =  $linha."10206888897000118INSTITUTO SORRINDO P                         ".$agencia.$conta."        ".$nomeT.$data_paga.$salario_arquivo."001      ".$banco_agencia.$banco_conta."                                 ".$cont_linha."\r\n";



}elseif($row_banco['id_nacional'] == "001"){
// ---------------------------------------ARQUIVO BANCO DO BRASIL----------------------------------------

$salario_arquivo = str_replace(",", "", $row['salario']);
$salario_arquivo = sprintf("%030d", $salario_arquivo);

$nome = sprintf("% -50s", $row2['nome']);

$cont_arquivobb = sprintf("%05d", $cont_arquivobb);

$agencia = str_replace("-", "", $row2['agencia']);
$agencia = sprintf("%05s",$agencia);

$conta = str_replace("-", "",$row2['conta']);
$conta = str_replace(" ", "",$conta);
$conta = str_replace("x", "X", $conta);
$conta = sprintf("%06s", $conta);

$d = explode("/",$data_pg);
$data_paga = date("dmY", mktime(0, 0, 0, $d[1], $d[0], $d[2]));

$linha =  $linha."00100013".$cont_arquivobb."A0000000010".$agencia."0000000".$conta." ".$nome.$data_paga."BRL".$salario_arquivo."                    00000000000000000000000                             000000000000000000000000\r\n";


}elseif($row_banco['id_nacional'] == "341"){ //-------------------- BANCO ITAÚ ---------------

$salario_arquivo = str_replace(",", "", $row['salario']);
$salario_arquivo = sprintf("%030d", $salario_arquivo);

$nome = sprintf("% -50s", $row2['nome']);

$cont_arquivobb = sprintf("%05d", $cont_arquivobb);

$agencia = str_replace("-", "", $row2['agencia']);
$agencia = sprintf("%04s",$agencia);

$conta = str_replace("-", "",$row2['conta']);
$conta = sprintf("%014s", $conta);
$conta = str_split($conta, 13);              //DIVIDE A CONTA COM 14 CARACTERES SENDO [0] DE 1 A 13 E [1] O ULTIMO

$d = explode("/",$data_pg);
$data_paga = date("dmY", mktime(0, 0, 0, $d[1], $d[0], $d[2]));

$linha =  $linha."34100013".$cont_arquivobb."A0000003410".$agencia." ".$conta[0]." ".$conta[1].$nome.$data_paga."REA".$salario_arquivo."                                                                     00000000000000                       \r\n";

}

//--------------------------- GERANDO A LINHA DO ARQUIVO 1---------------------------//

?>