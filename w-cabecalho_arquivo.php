<?php

$data_ddmmyyyy = date('dmY');
$data_ddmmyyyyH = date('dmYHis');
$data_ddmmyy = date('dmy');
$data_pg_ddmmyyyy = str_replace("/","",$data_pg);
$banco_agencia = sprintf("%05s", $row_banco['agencia']);
$banco_conta = sprintf("%08s", $row_banco['conta']);


if($row_banco['id_nacional'] == "237"){ //---------------------------------------- BRADESCO

$codigo_banco = "00000";    //(código fornecido pelo banco 5 digitos)

$cabecalho = "01REMESSA03CREDITO C/C    ".$banco_agencia."     ".$banco_conta."  ".$codigo_banco."INSTITUTO SORRINDO PARA A237BRADESCO       ".$data_pg_ddmmyyyy."01600BPI".$data_ddmmyyyy." N                                                                          000001\r\n";

}elseif($row_banco['id_nacional'] == "356"){ //----------------------------------- REAL ---------------

$cabecalho = "0        03CREDITOS C/C   INSTITUTO SORRINDO P                              275BANCO REAL S.A ".$data_ddmmyy."1600 BPI                                                                                      000001\r\n";


}elseif($row_banco['id_nacional'] == "001"){ //---------------------------- BANCO DO BRASIL ---------------

$nao_identificado = "29";

$banco_agencia = str_replace("-","",$row_banco['agencia']);
$banco_agencia = sprintf("%06s", $banco_agencia);

$banco_conta = str_replace("-","",$row_banco['conta']);
$banco_conta = sprintf("%013s", $banco_conta);


$cabecalho = "00100000         206888897000118000Paraty0126       ".$banco_agencia.$banco_conta." INSTITUTO SORRINDO PARA A VIDABANCO DO BRASIL S/A                     1".$data_ddmmyyyyH."0000".$nao_identificado."03000000                                                      000            
00100011C3001020 206888897000118000Paraty0126       0085080000000195863 INSTITUTO SORRINDO PARA A VIDA                                        RUA JOAO CAETANO              00359               ITABORAI            24800000RJ                  \r\n";



}elseif($row_banco['id_nacional'] == "341"){ //-------------------- BANCO ITAÚ ---------------



$cabecalho = "34100000      040206888897000118                    04567 000000000008 0INSTITUTO SORRINDO PARA A VIDABANCO ITAU ITABORAI                     1".$data_ddmmyyyyH."00000000000000                                                                     
34100011C3001030 206888897000118                    04567 000000000008 0INSTITUTO SORRINDO PARA A VIDA                                        RUA JOAO CAETANO              00359               ITABORAI            24800000RJ\r\n";


}

// ------------------------------------- FIM-CABEÇALHO DO ARQUIVO --------------------------------------//


?>