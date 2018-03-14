<?php
////////////////////////////////
//REGISTRO DE TRAILER DO LOTE//
//////////////////////////////

$BANCO = '341';
$LOTE_SERVICO = '0001';
$TIPO_REGISTRO = '5';	
//??BRANCO
$this->numeroSequencial[c] = $this->numeroSequencial[c] + 1;
$QUANT_REGISTROS = $this->numeroSequencial[c] + 1;
$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

$VALOR =array_sum($this->arrayValorTotal[c]);	
$VALOR = number_format($VALOR, 2, ".","");
$remover = array(".", "-", "/",",");
$VALORF  = str_replace($remover, "", $VALOR);	
$VALOR = sprintf("%018d",$VALORF);
$ZERO = '000000000000000000';

$OCORRENCIAS = ' ';
$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);	

$handle = fopen("../folha/BANCOS/ITAU/{$this->CAMINHO}_C.txt", "a");
fwrite($handle, $BANCO , 3);	
fwrite($handle, $LOTE_SERVICO , 4);	
fwrite($handle, $TIPO_REGISTRO , 1);
$BRANCO= ' ';
$BRANCO = sprintf("% 9s",$BRANCO);	
fwrite($handle, $BRANCO , 9);
fwrite($handle, $QUANT_REGISTROS , 6);
fwrite($handle, $VALOR , 18);	
fwrite($handle, $ZERO , 18);
$BRANCO= ' ';
$BRANCO = sprintf("% 171s",$BRANCO);	
fwrite($handle, $BRANCO , 171);
fwrite($handle, $OCORRENCIAS , 10);
fwrite($handle, "\r\n");		

/////////////////////////////////////
// REGISTRO DE TRAILER DO ARQUIVO //
///////////////////////////////////

//CONTROLE
$BANCO = '341';
$LOTE_SERVICO = '9999';
$TIPO_REGISTRO = '9';

//TOTAIS
$QUANT_LOTES = '000001';

$this->numeroSequencial[c] = $this->numeroSequencial[c] + 1;
$QUANT_REGISTROS = $this->numeroSequencial[c];
$QUANT_REGISTROS = $QUANT_REGISTROS+2;
$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

fwrite($handle, $BANCO , 3);	
fwrite($handle, $LOTE_SERVICO , 4);	
fwrite($handle, $TIPO_REGISTRO , 1);
$BRANCO = ' ';
$BRANCO = sprintf("% 9s",$BRANCO);	
fwrite($handle, $BRANCO , 9);
fwrite($handle, $QUANT_LOTES , 6);	
fwrite($handle, $QUANT_REGISTROS , 6);	
$BRANCO = ' ';
$BRANCO = sprintf("% 211s",$BRANCO); //VALOR REAL $BRANCO = sprintf("% 211s",$BRANCO);	
fwrite($handle, $BRANCO , 211);
fwrite($handle, "\r\n");
$BRANCO = ' ';
$BRANCO = sprintf("% 29s",$BRANCO); //VALOR REAL $BRANCO = sprintf("% 211s",$BRANCO);	
fwrite($handle, $BRANCO , 29);

if($this->numeroSequencial[c] == 2){
    unlink("../folha/BANCOS/ITAU/{$this->CAMINHO}_C.txt");
}

//SALARIO

$BANCO = '341';
$LOTE_SERVICO = '0001';
$TIPO_REGISTRO = '5';	
//??BRANCO
$this->numeroSequencial[s] = $this->numeroSequencial[s] + 1;
$QUANT_REGISTROS = $this->numeroSequencial[s] + 1;
$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

$VALOR =array_sum($this->arrayValorTotal[s]);	
$VALOR = number_format($VALOR, 2, ".","");
$remover = array(".", "-", "/",",");
$VALORF  = str_replace($remover, "", $VALOR);	
$VALOR = sprintf("%018d",$VALORF);
$ZERO = '000000000000000000';

$OCORRENCIAS = ' ';
$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);	

$handle = fopen("../folha/BANCOS/ITAU/{$this->CAMINHO}_S.txt", "a");
fwrite($handle, $BANCO , 3);	
fwrite($handle, $LOTE_SERVICO , 4);	
fwrite($handle, $TIPO_REGISTRO , 1);
$BRANCO= ' ';
$BRANCO = sprintf("% 9s",$BRANCO);	
fwrite($handle, $BRANCO , 9);
fwrite($handle, $QUANT_REGISTROS , 6);
fwrite($handle, $VALOR , 18);	
fwrite($handle, $ZERO , 18);
$BRANCO= ' ';
$BRANCO = sprintf("% 171s",$BRANCO);	
fwrite($handle, $BRANCO , 171);
fwrite($handle, $OCORRENCIAS , 10);
fwrite($handle, "\r\n");		

/////////////////////////////////////
// REGISTRO DE TRAILER DO ARQUIVO //
///////////////////////////////////

//CONTROLE
$BANCO = '341';
$LOTE_SERVICO = '9999';
$TIPO_REGISTRO = '9';

//TOTAIS
$QUANT_LOTES = '000001';

$this->numeroSequencial[s] = $this->numeroSequencial[s] + 1;
$QUANT_REGISTROS = $this->numeroSequencial[s];
$QUANT_REGISTROS = $QUANT_REGISTROS+2;
$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

fwrite($handle, $BANCO , 3);	
fwrite($handle, $LOTE_SERVICO , 4);	
fwrite($handle, $TIPO_REGISTRO , 1);
$BRANCO = ' ';
$BRANCO = sprintf("% 9s",$BRANCO);	
fwrite($handle, $BRANCO , 9);
fwrite($handle, $QUANT_LOTES , 6);	
fwrite($handle, $QUANT_REGISTROS , 6);	
$BRANCO = ' ';
$BRANCO = sprintf("% 211s",$BRANCO); //VALOR REAL $BRANCO = sprintf("% 211s",$BRANCO);	
fwrite($handle, $BRANCO , 211);
fwrite($handle, "\r\n");
$BRANCO = ' ';
$BRANCO = sprintf("% 29s",$BRANCO); //VALOR REAL $BRANCO = sprintf("% 211s",$BRANCO);	
fwrite($handle, $BRANCO , 29);

if($this->numeroSequencial[s] == 2){
    unlink("../folha/BANCOS/ITAU/{$this->CAMINHO}_S.txt");
}
?>