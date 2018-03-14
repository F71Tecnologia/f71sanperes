<?php
$BANCO = '033';
$LOTE_SERVICO = '9999';
$TIPO_REGISTRO = '9';
$QUANT_LOTES = '000001';
$QUANT_CONTAS_CONCIL = '000001';

$this->numeroSequencial[s] = $this->numeroSequencial[s] + 1;
$QUANT_REGISTROS = $this->numeroSequencial[s];
$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

$handle = fopen("../folha/BANCOS/SANTANDER/{$this->CAMINHO}_S.txt", "a");
fwrite($handle, $TIPO_REGISTRO ,1);
$RESERVADO_BRANCOS = sprintf("% 193s"," ");
fwrite($handle, $RESERVADO_BRANCOS, 193);
fwrite($handle, $QUANT_REGISTROS ,6);	
fclose($handle);
if($this->numeroSequencial[s] == 2){
    unlink("../folha/BANCOS/SANTANDER/{$this->CAMINHO}_S.txt");
}

$this->numeroSequencial[c] = $this->numeroSequencial[c] + 1;
$QUANT_REGISTROS = $this->numeroSequencial[c];
$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

$handle = fopen("../folha/BANCOS/SANTANDER/{$this->CAMINHO}_C.txt", "a");
fwrite($handle, $TIPO_REGISTRO ,1);
$RESERVADO_BRANCOS = sprintf("% 193s"," ");
fwrite($handle, $RESERVADO_BRANCOS, 193);
fwrite($handle, $QUANT_REGISTROS ,6);	
fclose($handle);
if($this->numeroSequencial[c] == 2){
    unlink("../folha/BANCOS/SANTANDER/{$this->CAMINHO}_C.txt");
}
?>