<?
$handle = fopen('BANCOS/BRADESCO/CONTA_CORRENTE/'.$CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO."_".$TIPO.".txt", "a");
$COD_REGISTRO_TRAILLER = '9'; //VALOR CONSTANTE

$VALOR =array_sum($arrayValorTotalCorrente);
$VALOR = number_format($VALOR, 2, ".","");
$remover = array(".", "-", "/",",");
$VALORF  = str_replace($remover, "", $VALOR);	
$VALOR_TOTAL = sprintf("%013d",$VALORF);

$RESERVA = sprintf("% 180s",$RESERVA);

$numeroSequencialCorrente = $numeroSequencialCorrente + 1;
$NRO_SEQUENCIAL = sprintf("%06d",$numeroSequencialCorrente);

fwrite($handle, $COD_REGISTRO_TRAILLER, 1);
fwrite($handle, $VALOR_TOTAL, 13);
fwrite($handle, $RESERVA, 180);
fwrite($handle, $NRO_SEQUENCIAL, 6);
fwrite($handle, "\r\n");
fclose($handle);

?>