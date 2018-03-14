<?
$handle = fopen('BANCOS/BRADESCO/CONTA_POUPANCA/'.$CONSTANTE."_".$DD."_".$MM."_".$NUM_ARQUIVO."_".$TIPO.".txt", "a");
$COD_REGISTRO_TRAILLER = '9'; //VALOR CONSTANTE

$VALOR_TOTAL = $valor_liquiFinalF;
$VALOR_TOTAL = number_format($VALOR_TOTAL, 2, ".","");
$remover = array(".", "-", "/",",");
$VALOR_TOTAL = str_replace($remover, "", $VALOR_TOTAL);
$VALOR_TOTAL = sprintf("%013d",$VALOR_TOTAL);

$RESERVA = sprintf("% 180s",$RESERVA);

$numeroSequencial = $numeroSequencial + 1;
$NRO_SEQUENCIAL = sprintf("%06d",$numeroSequencial);

fwrite($handle, $COD_REGISTRO_TRAILLER, 1);
fwrite($handle, $VALOR_TOTAL, 13);
fwrite($handle, $RESERVA, 180);
fwrite($handle, $NRO_SEQUENCIAL, 6);
fclose($handle);

?>