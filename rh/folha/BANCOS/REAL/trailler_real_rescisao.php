<?php
/////////////////////////////////////
// REGISTRO DE TRAILER DO ARQUIVO //
///////////////////////////////////

/* CONTROLE */
$BANCO = '356';
$LOTE_SERVICO = '9999';
$TIPO_REGISTRO = '9';
//??CNAB

/* TOTAIS */
$QUANT_LOTES = '000001';
$QUANT_CONTAS_CONCIL = '000001';

for($i=1;$i<=2;$i++){
    if($i == 1){
        $handle = fopen("../folha/BANCOS/REAL/{$this->CAMINHO}_C.txt", "a");
        $this->numeroSequencial[c] = $this->numeroSequencial[c] + 1;
        $QUANT_REGISTROS = $this->numeroSequencial[c];
        $QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

        //$this->numeroSequencial[c] = $this->numeroSequencial[c] + 1;
    }else if($i == 2){
        $handle = fopen("../folha/BANCOS/REAL/{$this->CAMINHO}_S.txt", "a");
        $this->numeroSequencial[s] = $this->numeroSequencial[s] + 1;
        $QUANT_REGISTROS = $this->numeroSequencial[s];
        $QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

        //$this->numeroSequencial[s] = $this->numeroSequencial[s] + 1;
    }

    fwrite($handle, $TIPO_REGISTRO ,1);
    $RESERVADO_BRANCOS = sprintf("% 193s"," ");
    fwrite($handle, $RESERVADO_BRANCOS, 193);
    fwrite($handle, $QUANT_REGISTROS ,6);	

    fclose($handle);	
    
    if($i == 1){
        if($this->numeroSequencial[c] == 2){
            unlink("../folha/BANCOS/REAL/{$this->CAMINHO}_C.txt");
        }
    }else if($i == 2){
        if($this->numeroSequencial[s] == 2){
            unlink("../folha/BANCOS/REAL/{$this->CAMINHO}_S.txt");
        }
    }
}
?>