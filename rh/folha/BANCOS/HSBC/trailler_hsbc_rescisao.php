<?php

//SALARIO

$handle = fopen("../folha/BANCOS/HSBC/{$this->CAMINHO}_S.txt", "a");
$COD_REGISTRO_TRAILLER = '9'; //VALOR CONSTANTE

$VALOR = array_sum($this->arrayValorTotal[s]);
$VALOR = number_format($VALOR, 2, ".","");
$remover = array(".", "-", "/",",");
$VALORF  = str_replace($remover, "", $VALOR);	
$VALOR_TOTAL = sprintf("%013d",$VALORF);

$RESERVA = sprintf("% 180s",$RESERVA);

$this->numeroSequencial[s] = $this->numeroSequencial[s] + 1;
$NRO_SEQUENCIAL = sprintf("%06d",$this->numeroSequencial[s]);

fwrite($handle, $COD_REGISTRO_TRAILLER, 1);
fwrite($handle, $VALOR_TOTAL, 13);
fwrite($handle, $RESERVA, 180);
fwrite($handle, $NRO_SEQUENCIAL, 6);
fwrite($handle, "\r\n");
fclose($handle);
if($this->numeroSequencial[s] == 2){
    unlink("../folha/BANCOS/HSBC/{$this->CAMINHO}_S.txt");
}
//CORRENTE

$handle = fopen("../folha/BANCOS/HSBC/{$this->CAMINHO}_C.txt", "a");
$COD_REGISTRO_TRAILLER = '9'; //VALOR CONSTANTE

$VALOR = array_sum($this->arrayValorTotal[c]);
$VALOR = number_format($VALOR, 2, ".","");
$remover = array(".", "-", "/",",");
$VALORF  = str_replace($remover, "", $VALOR);	
$VALOR_TOTAL = sprintf("%013d",$VALORF);

$RESERVA = sprintf("% 180s",$RESERVA);

$this->numeroSequencial[c] = $this->numeroSequencial[c] + 1;
$NRO_SEQUENCIAL = sprintf("%06d",$this->numeroSequencial[c]);

fwrite($handle, $COD_REGISTRO_TRAILLER, 1);
fwrite($handle, $VALOR_TOTAL, 13);
fwrite($handle, $RESERVA, 180);
fwrite($handle, $NRO_SEQUENCIAL, 6);
fwrite($handle, "\r\n");
fclose($handle);
if($this->numeroSequencial[c] == 2){
    unlink("../folha/BANCOS/HSBC/{$this->CAMINHO}_C.txt");
}
?>