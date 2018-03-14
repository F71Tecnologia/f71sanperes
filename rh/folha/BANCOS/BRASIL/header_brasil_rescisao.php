<?php 

///////////////////////////////////
//	REGISTRO HEADER DE ARQUIVO	//
/////////////////////////////////
$handle = fopen("../folha/BANCOS/BRASIL/{$this->CAMINHO}_C.txt", "w");
$handle2 = fopen("../folha/BANCOS/BRASIL/{$this->CAMINHO}_S.txt", "w");
//CONTROLE
$BANCO = '001';
fwrite($handle, $BANCO,3);					
fwrite($handle2, $BANCO,3);					

$LOTE_SERVICO = '0000';
fwrite($handle, $LOTE_SERVICO,4);
fwrite($handle2, $LOTE_SERVICO,4);

$TIPO_REGISTRO = '0';
fwrite($handle, $TIPO_REGISTRO,1);
fwrite($handle2, $TIPO_REGISTRO,1);

$BRANCOS = ' ';
$BRANCOS = sprintf("% 9s",$BRANCOS);
fwrite($handle, $BRANCOS, 9);
fwrite($handle2, $BRANCOS, 9);

//INSCRIÇÃO DA EMPRESA
$TIPO_INSCRICAO_EMPRESA = '2';
fwrite($handle, $TIPO_INSCRICAO_EMPRESA, 1);
fwrite($handle2, $TIPO_INSCRICAO_EMPRESA, 1);

$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj'];
$remover = array(".", "-", "/",",");
$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);	
fwrite($handle, $NUMERO_INSCRICAO_EMPRESA, 14);
fwrite($handle2, $NUMERO_INSCRICAO_EMPRESA, 14);

if($row_banco['cod_convenio'] == ""){
        $CODIGO_CONVENIO_BANCO = '0008585910126';
}else{
        $CODIGO_CONVENIO_BANCO = '000'.$row_banco['cod_convenio'].'0126';
}

$CODIGO_CONVENIO_BANCO = sprintf("% -20s",$CODIGO_CONVENIO_BANCO);
fwrite($handle, $CODIGO_CONVENIO_BANCO, 20);
fwrite($handle2, $CODIGO_CONVENIO_BANCO, 20);

$AGENCIA = $row_banco['agencia'];					
$AGENCIA = substr($AGENCIA, 0, 4); 
$AGENCIA = sprintf("%05d",$AGENCIA);
fwrite($handle, $AGENCIA, 5);
fwrite($handle2, $AGENCIA, 5);

$str = $row_banco['agencia'];
$ultimoDigitoAgencia = $str{strlen($str)-1};			
$DV_AGENCIA_EMPRESA = $ultimoDigitoAgencia;	
$DV_AGENCIA_EMPRESA = sprintf("%0d",$DV_AGENCIA_EMPRESA);
fwrite($handle, $DV_AGENCIA_EMPRESA, 1);
fwrite($handle2, $DV_AGENCIA_EMPRESA, 1);

$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");					 
$NUMERO_CONTAF = str_replace($remover, "", $NUMERO_CONTA);
$NUMERO_CONTAF = substr($NUMERO_CONTAF, 0, 5);
$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTAF);
fwrite($handle, $NUMERO_CONTA, 12);
fwrite($handle2, $NUMERO_CONTA, 12);

$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");
$str = str_replace($remover, "", $NUMERO_CONTA);
$ultimoDigitoConta = $str{strlen($str)-1};			
$DV_CONTA_EMPRESA = $ultimoDigitoConta;	
fwrite($handle, $DV_CONTA_EMPRESA, 1);
fwrite($handle2, $DV_CONTA_EMPRESA, 1);

$DV_AGENCIA_CONTA = ' ';
fwrite($handle, $DV_AGENCIA_CONTA, 1);
fwrite($handle2, $DV_AGENCIA_CONTA, 1);

$NOME_EMPRESA = $row_master['razao'];
$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);
fwrite($handle, $NOME_EMPRESA, 30);
fwrite($handle2, $NOME_EMPRESA, 30);

$NOME_BANCO = 'BANCO DO BRASIL S.A.';
$NOME_BANCO = sprintf("% -30s",$NOME_BANCO);
fwrite($handle, $NOME_BANCO, 30);
fwrite($handle2, $NOME_BANCO, 30);

$BRANCOS = ' ';
$BRANCOS = sprintf("% 10s",$BRANCOS);
fwrite($handle, $BRANCOS, 10);
fwrite($handle2, $BRANCOS, 10);

$CODIGO_REMESSA = '1';
fwrite($handle, $CODIGO_REMESSA, 1);
fwrite($handle2, $CODIGO_REMESSA, 1);

$DATA_GERACAO_ARQUIVO = date('dmY');
fwrite($handle, $DATA_GERACAO_ARQUIVO, 8);
fwrite($handle2, $DATA_GERACAO_ARQUIVO, 8);

$HORA_GERACAO_ARQUIVO = date('His');
fwrite($handle, $HORA_GERACAO_ARQUIVO, 6);
fwrite($handle2, $HORA_GERACAO_ARQUIVO, 6);

$NUM_SEQUENCIAL = '000001';
fwrite($handle, $NUM_SEQUENCIAL, 6);
fwrite($handle2, $NUM_SEQUENCIAL, 6);

$LAYOUT_LOTE = '030';
fwrite($handle, $LAYOUT_LOTE, 3);
fwrite($handle2, $LAYOUT_LOTE, 3);

$DENCIDADE_GRAVACAO = 0;
$DENCIDADE_GRAVACAO = sprintf("%05d",$DENCIDADE_GRAVACAO);
fwrite($handle, $DENCIDADE_GRAVACAO, 5);
fwrite($handle2, $DENCIDADE_GRAVACAO, 5);

$BRANCOS = ' ';
$BRANCOS = sprintf("% 20s",$BRANCOS);
fwrite($handle, $BRANCOS, 20);
fwrite($handle2, $BRANCOS, 20);

$BRANCOS = ' ';
$BRANCOS = sprintf("% 20s",$BRANCOS);
fwrite($handle, $BRANCOS, 20);
fwrite($handle2, $BRANCOS, 20);

$BRANCOS = ' ';
$BRANCOS = sprintf("% 11s",$BRANCOS);
fwrite($handle, $BRANCOS, 11);					
fwrite($handle2, $BRANCOS, 11);					

$IDENTIFICACAO_COBRANCA = ' ';
$IDENTIFICACAO_COBRANCA = sprintf("% 3s",$IDENTIFICACAO_COBRANCA);
fwrite($handle, $IDENTIFICACAO_COBRANCA, 3);						
fwrite($handle2, $IDENTIFICACAO_COBRANCA, 3);						

$USO_EXCLUSIVO_VANS = '000';
fwrite($handle, $USO_EXCLUSIVO_VANS, 3);					
fwrite($handle2, $USO_EXCLUSIVO_VANS, 3);					

$TIPO_SERVIÇO = ' ';
$TIPO_SERVIÇO = sprintf("% 2s",$TIPO_SERVIÇO);
fwrite($handle, $TIPO_SERVIÇO, 2);								
fwrite($handle2, $TIPO_SERVIÇO, 2);								

$OCORRENCIAS = ' ';
$OCORRENCIAS = sprintf("% 10s",$OCORRENCIAS);
fwrite($handle, $OCORRENCIAS, 10);
fwrite($handle, "\r\n");
fwrite($handle2, $OCORRENCIAS, 10);
fwrite($handle2, "\r\n");

///////////////////////////////
//REGISTRO DE HEADER DE LOTE//
/////////////////////////////
$BANCO = '001';
fwrite($handle, $BANCO,3);					
fwrite($handle2, $BANCO,3);					

$LOTE_SERVICO = '0001';
fwrite($handle, $LOTE_SERVICO,4);
fwrite($handle2, $LOTE_SERVICO,4);

$TIPO_REGISTRO = '1';
fwrite($handle, $TIPO_REGISTRO,1);
fwrite($handle2, $TIPO_REGISTRO,1);

$OPERACAO = 'C';	
fwrite($handle, $OPERACAO, 1);					
fwrite($handle2, $OPERACAO, 1);					

$SERVICO = '30'; 
fwrite($handle, $SERVICO, 2);
fwrite($handle2, $SERVICO, 2);

$FORMA_LANCAMENTO = '01';
fwrite($handle, $FORMA_LANCAMENTO, 2);					
fwrite($handle2, $FORMA_LANCAMENTO, 2);					

$LAYOUT_LOTE = '020';
fwrite($handle, $LAYOUT_LOTE, 3);
fwrite($handle2, $LAYOUT_LOTE, 3);

$BRANCOS = ' ';
fwrite($handle, $BRANCOS, 1);					
fwrite($handle2, $BRANCOS, 1);					

$TIPO_INSCRICAO_EMPRESA = '2';
fwrite($handle, $TIPO_INSCRICAO_EMPRESA, 1);
fwrite($handle2, $TIPO_INSCRICAO_EMPRESA, 1);

$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj'];
$remover = array(".", "-", "/",",");
$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);	
fwrite($handle, $NUMERO_INSCRICAO_EMPRESA, 14);
fwrite($handle2, $NUMERO_INSCRICAO_EMPRESA, 14);

#$CODIGO_CONVENIO_BANCO = '0008585910126     TS';
#$CODIGO_CONVENIO_BANCO = sprintf("% -20s",$CODIGO_CONVENIO_BANCO);
fwrite($handle, $CODIGO_CONVENIO_BANCO, 20);
fwrite($handle2, $CODIGO_CONVENIO_BANCO, 20);

$AGENCIA = $row_banco['agencia'];					
$AGENCIA = substr($AGENCIA, 0, 4); 
$AGENCIA = sprintf("%05d",$AGENCIA);
fwrite($handle, $AGENCIA, 5);
fwrite($handle2, $AGENCIA, 5);

$str = $row_banco['agencia'];
$ultimoDigitoAgencia = $str{strlen($str)-1};			
$DV_AGENCIA_EMPRESA = $ultimoDigitoAgencia;	
$DV_AGENCIA_EMPRESA = sprintf("%0d",$DV_AGENCIA_EMPRESA);
fwrite($handle, $DV_AGENCIA_EMPRESA, 1);
fwrite($handle2, $DV_AGENCIA_EMPRESA, 1);

$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");					 
$NUMERO_CONTAF = str_replace($remover, "", $NUMERO_CONTA);
$NUMERO_CONTAF = substr($NUMERO_CONTAF, 0, 5);
$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTAF);
fwrite($handle, $NUMERO_CONTA, 12);
fwrite($handle2, $NUMERO_CONTA, 12);

$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");
$str = str_replace($remover, "", $NUMERO_CONTA);
$ultimoDigitoConta = $str{strlen($str)-1};			
$DV_CONTA_EMPRESA = $ultimoDigitoConta;	
fwrite($handle, $DV_CONTA_EMPRESA, 1);
fwrite($handle2, $DV_CONTA_EMPRESA, 1);

$DV_AGENCIA_CONTA = ' ';
fwrite($handle, $DV_AGENCIA_CONTA, 1);
fwrite($handle2, $DV_AGENCIA_CONTA, 1);

$NOME_EMPRESA = $row_master['razao'];
$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);
fwrite($handle, $NOME_EMPRESA, 30);
fwrite($handle2, $NOME_EMPRESA, 30);

$MENSAGEM = ' ';
$MENSAGEM = sprintf("% -40s",$MENSAGEM);
fwrite($handle, $MENSAGEM, 40);
fwrite($handle2, $MENSAGEM, 40);

$resultEmpresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_regiao[id_regiao]'");
$rowEmpresa = mysql_fetch_array($resultEmpresa);

//ENDEREÇO DA EMPRESA
$LOGRADOURO_EMPRESA = $rowEmpresa['endereco'];
$remover = array(".", "-", "/",",");
$LOGRADOURO_EMPRESA = str_replace($remover, "", $LOGRADOURO_EMPRESA);	
$LOGRADOURO_EMPRESA = sprintf("% -30s",$LOGRADOURO_EMPRESA);
fwrite($handle, $LOGRADOURO_EMPRESA, 30);
fwrite($handle2, $LOGRADOURO_EMPRESA, 30);

$NUMERO_EMPRESA = 0;
$NUMERO_EMPRESA = sprintf("%05d",$NUMERO_EMPRESA);
fwrite($handle, $NUMERO_EMPRESA, 5);
fwrite($handle2, $NUMERO_EMPRESA, 5);

$COMPLEMENTO_EMPRESA = ' ';
$COMPLEMENTO_EMPRESA = sprintf("% -15s",$COMPLEMENTO_EMPRESA);
fwrite($handle, $COMPLEMENTO_EMPRESA, 15);					
fwrite($handle2, $COMPLEMENTO_EMPRESA, 15);					

$CIDADE_EMPRESA =  ' ';
$CIDADE_EMPRESA = sprintf("% -20s",$CIDADE_EMPRESA);
fwrite($handle, $CIDADE_EMPRESA, 20);
fwrite($handle2, $CIDADE_EMPRESA, 20);

$CEP_EMPRESA = 0;
$CEP_EMPRESA = sprintf("%05d",$CEP_EMPRESA);					
fwrite($handle, $CEP_EMPRESA, 5);
fwrite($handle2, $CEP_EMPRESA, 5);

$COMPLEMENTO_CEP_EMPRESA = ' ';
$COMPLEMENTO_CEP_EMPRESA = sprintf("% -3s",$COMPLEMENTO_CEP_EMPRESA);					
fwrite($handle, $COMPLEMENTO_CEP_EMPRESA, 3);					
fwrite($handle2, $COMPLEMENTO_CEP_EMPRESA, 3);

$ESTADO_EMPRESA = ' ';
$ESTADO_EMPRESA = sprintf("% -2s",$ESTADO_EMPRESA);
fwrite($handle, $ESTADO_EMPRESA, 2);
fwrite($handle2, $ESTADO_EMPRESA, 2);

$BRANCOS = ' ';
$BRANCOS = sprintf("% -8s",$BRANCOS);
fwrite($handle, $BRANCOS, 8);					
fwrite($handle2, $BRANCOS, 8);					

$OCORRENCIAS = ' ';
$OCORRENCIAS = sprintf("% 10s",$OCORRENCIAS);
fwrite($handle, $OCORRENCIAS, 10);	
fwrite($handle, "\r\n");
fwrite($handle2, $OCORRENCIAS, 10);	
fwrite($handle2, "\r\n");
?>