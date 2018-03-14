<?php
////////////////////////////////////////////////////////////////////////
//REGISTRO DE DETALHES - SEGMENTO A (OBRIGATÓRIO - REMESSA / RETORNO)//
//////////////////////////////////////////////////////////////////////					

$BANCO = '033'; //VALOR CONSTANTE
$LOTE_SERVICO = '0001';
$TIPO_REGISTRO = '3';	

$this->numeroSequencial[$row_clt[tipo_conta][0]] = $this->numeroSequencial[$row_clt[tipo_conta][0]]+1;
$QUANT_REGISTROS = $this->numeroSequencial[$row_clt[tipo_conta][0]];
$QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

$SEGMENTO = 'A';

$TIPO_MOVIMENTACAO = '0'; //INCLUSAO
$CODIGO_MOVIMENTACAO = '00';

//DADOS DA EMPRESA
$NUMERO_CNPJ_EMPRESA = $row_master['cnpj'];
$remover = array(".", "-", "/",",");
$NUMERO_CNPJ_EMPRESA = str_replace($remover, "", $NUMERO_CNPJ_EMPRESA);
$NUMERO_CNPJ_EMPRESA = sprintf("%014s",$NUMERO_CNPJ_EMPRESA);
$NOME_DA_EMPRESA = sprintf("%- 20s",$row_master['nome']);
//DADOS DO BANCO DA EMPRESA
$AGENCIA = $row_banco['agencia'];					
$AGENCIA = substr($AGENCIA, 0, 4); 
$AGENCIA = sprintf("%04d",$AGENCIA);

$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");					 
$NUMERO_CONTAT = str_replace($remover, "", $NUMERO_CONTA);
$NUMERO_CONTAF = substr($NUMERO_CONTAT, 0, 9);							//NUMERO DA CONTA SEM O DIGITO mudado para 9 aqui e embaixo

$NUMERO_CONTA = sprintf("%7s  ",$NUMERO_CONTAF);


//FAVORECIDO
$COMPENSACAO_FAVORECIDO = ''; //NÃO FORNECER
$COMPENSACAO_FAVORECIDO = sprintf("%03d",$COMPENSACAO_FAVORECIDO);

$BANCO_FAVORECIDO = 033;
$BANCO_FAVORECIDO = sprintf("%03d",$BANCO_FAVORECIDO);

$CODIGO_AGENCIA_FAVORECIDO = $row_clt['agencia'];
$CODIGO_AGENCIA_FAVORECIDO = substr($CODIGO_AGENCIA_FAVORECIDO, 0, 4);
$CODIGO_AGENCIA_FAVORECIDO = sprintf("%04d",$CODIGO_AGENCIA_FAVORECIDO);

$NUMERO_CONTA_FAVORECIDO = $row_clt['conta'];
$remover = array(".", "-", "/",",");				
$NUMERO_CONTA_FAVORECIDO = str_replace($remover, "", $NUMERO_CONTA_FAVORECIDO);				
$NUMERO_CONTA_FAVORECIDO = sprintf("%09d",$NUMERO_CONTA_FAVORECIDO); //alterado de 8 para 9

$DV_AGENCIA_CONTA = ' ';				
$DV_AGENCIA_CONTA = sprintf("% 1s",$DV_AGENCIA_CONTA);

$NOME_FAVORECIDO = $row_clt['nome'];
$NOME_FAVORECIDO = sprintf("% -40s",$NOME_FAVORECIDO);

//CRÉDITO
$data = $regiao.date('dmYHis').$row_clt[0];;
$NOSSO_NUMERO = '$data';
$NOSSO_NUMERO = sprintf("% -20s",$NOSSO_NUMERO);


$DATA_PAGAMENTO = date("dmy", mktime(0,0,0, $m, $d, $a));
//$DATA_PAGAMENTO = $d.$m.$y;
$DATA_PAGAMENTO = sprintf("%06d",$DATA_PAGAMENTO);

$TIPO_MOEDA = 'BRL';
$TIPO_MOEDA = sprintf("% -3s",$TIPO_MOEDA);

$VALOR_PAGAMENTO  = $row_clt['salliquido'];
$this->arrayValorTotal[$row_clt[tipo_conta][0]][] = $VALOR_PAGAMENTO ;
$remover = array(".", "-", "/",",");
$VALOR_PAGAMENTOF  = str_replace($remover, "", $VALOR_PAGAMENTO );
$VALOR_PAGAMENTO  = sprintf("%013d" ,$VALOR_PAGAMENTOF );				

$QUANTIDADE_MOEDA = $VALOR_PAGAMENTOF;
$QUANTIDADE_MOEDA = sprintf("%015d",$QUANTIDADE_MOEDA);				

$NOSSO_NUMERO = '';
$NOSSO_NUMERO = sprintf("% -20s",$NOSSO_NUMERO);

$DATA_REAL = $d.$m.$a;//não usar
$DATA_REAL = sprintf("%08d",$DATA_REAL);

$VALOR_REAL = $VALOR_PAGAMENTOF;
$VALOR_REAL = sprintf("%015d",$VALOR_REAL);

$INFORMACAO2 = ' ';
$INFORMACAO2 = sprintf("% -40s",$INFORMACAO2);

$CODIGO_FINALIDADE_DOC = '06';
$CODIGO_FINALIDADE_DOC = sprintf("% -2s",$CODIGO_FINALIDADE_DOC);

$USO_FEBRABAN_CNBB = ' ';
$USO_FEBRABAN_CNBB = sprintf("% -10s",$USO_FEBRABAN_CNBB);

$AVISO_FAVORECIADO = 0;
$AVISO_FAVORECIADO = sprintf("%01d", $AVISO_FAVORECIADO);

$OCORRENCIAS = '00';
$OCORRENCIAS = sprintf("% -10s",$OCORRENCIAS);

//ESCREVENCO NO ARQUIVO
if($row_clt[tipo_conta] == 'corrente'){
    $handle = fopen("../folha/BANCOS/SANTANDER/{$this->CAMINHO}_C.txt", "a");
}else if($row_clt[tipo_conta] == 'salario'){
    $handle = fopen("../folha/BANCOS/SANTANDER/{$this->CAMINHO}_S.txt", "a");
}

fwrite($handle, "1", 1);
fwrite($handle, "02", 2); //IDENTIFICAÇÃO DA EMPRESA
fwrite($handle, $NUMERO_CNPJ_EMPRESA, 14);
fwrite($handle, $NOME_DA_EMPRESA, 20);

$RESERVADO_BRANCOS = sprintf("% 25s"," ");
fwrite($handle, $RESERVADO_BRANCOS, 25);

fwrite($handle, $CODIGO_AGENCIA_FAVORECIDO , 4);
fwrite($handle, "0", 1); // zero santander
fwrite($handle, $NUMERO_CONTA_FAVORECIDO , 9);

$RESERVADO_BRANCOS = sprintf("% 6s"," ");
fwrite($handle, $RESERVADO_BRANCOS, 6);

fwrite($handle, $NOME_FAVORECIDO , 40);
fwrite($handle, $DATA_PAGAMENTO , 6);
fwrite($handle, $VALOR_PAGAMENTO , 13);
fwrite($handle, "001", 3);

$RESERVADO_BRANCOS = sprintf("% 6s"," ");
fwrite($handle, $RESERVADO_BRANCOS, 6);

fwrite($handle, $AGENCIA, 4);
fwrite($handle, $NUMERO_CONTA, 9);

$RESERVADO_BRANCOS = sprintf("% 31s"," ");
fwrite($handle, $RESERVADO_BRANCOS, 31);

fwrite($handle, $QUANT_REGISTROS , 6);

fwrite($handle, "\r\n");
?>