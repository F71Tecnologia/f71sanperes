<?php
/*
$espaco1 = sprintf("% 21s","");
$espaco2 = sprintf("% 8s","");
$espaco61 = sprintf("% 61s","");

$texto_demonstrativo_pg = sprintf("% -40s","INSTITUTO SORRINDO PARA A VIDA");

$salario_arquivo = str_replace(",", "", $row['salario']);
$salario_arquivo = sprintf("%013d", $salario_arquivo);

$codigo = sprintf("%06d", $row2['campo3']);

$nome_quebrado = str_split($row2['nome'], 28);

$nome = sprintf("% -40s", $row2['nome']);
$nome_bradesco = sprintf("% -38s", $row2['nome']);
$nome = $nome.$ai_ai;

$agencia = str_replace("-", "", $row2['agencia']);
$agencia = sprintf("%05s",$agencia);

$conta = str_replace("-", "",$row2['conta']);
$conta = sprintf("%08s", $conta);

$valores = sprintf("%09d", $valor_3);
$cont_linha = sprintf("%06d", $cont_arquivo);

$linha_real =  $linha_real."10206888897000118INSTITUTO SORRINDO P                         ".$agencia_e_conta."        ".$nome40.$data_pg.$salario_13digitos."     ".$agencia_e_conta_do_projeto."                                 ".$contador."\r\n";

$linha_bradesco = $linha_bradesco."1".$espaco61.$agencia."12345".$conta."  ".$nome_bradesco.$codigo.$salario_arquivo."298                                                    ".$cont_linha."\r\n";
*/
//--------------------------- GERANDO A LINHA DO ARQUIVO 1---------------------------//

if($banco == "BRADESCO"){

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

$linha_real =  $linha_real."10206888897000118INSTITUTO SORRINDO P                         ".$agencia_e_conta."        ".$nome40.$data_pg.$salario_13digitos."     ".$agencia_e_conta_do_projeto."                                 ".$contador."\r\n";

$linha_bradesco = $linha_bradesco."1".$espaco61.$agencia."12345".$conta."  ".$nome_bradesco.$codigo.$salario_arquivo."298                                                    ".$cont_linha."\r\n";

}else{

}
