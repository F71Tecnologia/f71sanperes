<?php

if($row_banco['id_nacional'] == "237"){
$valor_total_banco = sprintf("%013d", $valor_total_banco);


$rodape = "9".$valor_total_banco."                                                                                                                                                                                    ".$cont_linha;

}elseif($row_banco['id_nacional'] == "356"){

$rodape = "9                                                                                                                                                                                                 ".$cont_linha;


}elseif($row_banco['id_nacional'] == "001"){ //--------------------- BANCO DO BRASIL ---------------

$valor_total_banco = sprintf("%018d", $valor_total_banco);

$rodape = "00100015         000068".$valor_total_banco."000000000000000000                                                                                                                                                                                     
00199999         000001000070000000                                                                                                                                                                                                             ";



}elseif($row_banco['id_nacional'] == "341"){ //-------------------- BANCO ITAÚ ---------------

$valor_total_banco = sprintf("%013d", $valor_total_banco);

$rodape = "34100015         000175".$valor_total_banco."000000000000000000                                                                                                                                                                                     
34199999         000001000177                                                                                                                                                                                                                   ";


}

                                                                                                                                                                                                                   


?>