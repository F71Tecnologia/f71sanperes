<?php
///////////////////////////////
//REGISTRO DE HEADER DE LOTE//
/////////////////////////////

/* DADOS DE CONTROLE */
$BANCO = '356'; //VALOR CONSTANTE
$LOTE_SERVICO = '0001';
$TIPO_REGISTRO = '1';

/* DADOS DO SERVIÇO */
$OPERACAO = 'C'; //LANÇAMENTO DE CRÉDITO
$SERVICO = '30';  //TIPO DE SERVIÇO 30 (OAGAMENTO DE SALÁRIOS)
$FORMA_LANCAMENTO = '01'; //CREDITO EM CONTA CORRENTE
$LAYOUT_LOTE = '030';
//??CNAB

/* DADOS DA EMPRESA */
//INSCRICAO					
$TIPO_INSCRICAO_EMPRESA = '2';									
$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj'];
$remover = array(".", "-", "/",",");
$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);					

//CONVÊNIO
$AGENCIA = $row_banco['agencia'];
$CODIGO_AGENCIA_EMPRESA = substr($AGENCIA, 0, 4); 
$CODIGO_AGENCIA_EMPRESA = sprintf("%05d",$CODIGO_AGENCIA_EMPRESA);

$str = $row_banco['agencia'];
$ultimoDigitoAgencia = $str{strlen($str)-1};			
$DV_AGENCIA_EMPRESA = $ultimoDigitoAgencia;	
$DV_AGENCIA_EMPRESA = sprintf("%0d",$DV_AGENCIA_EMPRESA);

$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");
$NUMERO_CONTA = str_replace($remover, "", $NUMERO_CONTA);
$NUMERO_CONTAF = substr($NUMERO_CONTA, 0, 7);
$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTAF);

$str = $row_banco['conta'];
$ultimoDigitoConta = $str{strlen($str)-1};			
$DV_CONTA_EMPRESA = $ultimoDigitoConta;	
$DV_CONTA_EMPRESA = sprintf("%0d",$DV_CONTA_EMPRESA);

$DV_CONTA_AGENCIA = $DV_AGENCIA_EMPRESA;

//CODIGO DE CONVENIO
$AGENCIA = $row_banco['agencia'];
$AGENCIA = substr($AGENCIA, 0, 4); 

$CONVENIO_EMPRESA = $AGENCIA.$NUMERO_CONTAF.'PG';
$CONVENIO_EMPRESA = sprintf("% -20s",$CONVENIO_EMPRESA);										


$NOME_EMPRESA = $row_master['razao'];
$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);

/* INFORMAÇÃO 1 */
$INFORMACAO1 = ' ';
$INFORMACAO1 = sprintf("% -40s",$INFORMACAO1);

/* ENDEREÇO DA EMPRESA */
$resultEmpresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_regiao[id_regiao]'");
$rowEmpresa = mysql_fetch_array($resultEmpresa);					

$LOGRADOURO_EMPRESA = $rowEmpresa['endereco'];
$remover = array(".", "-", "/",",");
$LOGRADOURO_EMPRESA = str_replace($remover, "", $LOGRADOURO_EMPRESA);	
$LOGRADOURO_EMPRESA = sprintf("% -30s",$LOGRADOURO_EMPRESA);

$NUMERO_EMPRESA = '0';
$NUMERO_EMPRESA = sprintf("%05d",$NUMERO_EMPRESA);

$COMPLEMENTO_EMPRESA = '';
$COMPLEMENTO_EMPRESA = sprintf("% -15s",$COMPLEMENTO_EMPRESA);

$CIDADE_EMPRESA =  '';
$CIDADE_EMPRESA = sprintf("% -20s",$CIDADE_EMPRESA);

$CEP_EMPRESA = '0';
$CEP_EMPRESA = sprintf("%05d",$CEP_EMPRESA);

$COMPLEMENRO_CEP_EMPRESA = '';
$COMPLEMENRO_CEP_EMPRESA = sprintf("% 3s",$COMPLEMENRO_CEP_EMPRESA);

$ESTADO_EMPRESA = '';
$ESTADO_EMPRESA = sprintf("% 2s",$ESTADO_EMPRESA);

$USO_FEBRABAN_CNBB = ' ';
$USO_FEBRABAN_CNBB = sprintf("% 8s",$USO_FEBRABAN_CNBB);

$OCORRENCIAS = '00';
$OCORRENCIAS = sprintf("% 10s",$OCORRENCIAS);	


////////////////////////////////////
// REGISTRO DE HEADER DE ARQUIVO //
//////////////////////////////////

/* DADOS DE CONTROLE */
$BANCO = '356'; //CÓDIGO DO BANCO NA COMPENSAÇÃO
$COD_BANCO = '275'; //CÓDIGO DO BANCO NA COMPENSAÇÃO
$TIPO_REGISTRO = '0'; //VALOR PADRÃO PARA HEADER DE ARQUIVO
$TIPO_REGISTRO_PADRAO = '3'; //VALOR PADRÃO PARA HEADER DE ARQUIVO
$NOME_DO_BANCO = "BANCO REAL S.A ";
//??CNAB 9 POSIÇÕES EM BRANCO BRANCOS

/* DADOS DA EMPRESA */	
/* INSCRIÇÃO */
$TIPO_INSCRICAO_EMPRESA = '2'; //TIPO DE INSCRIÇÃO DA EMPRESA, COM VALOR PADRÃO 2 (CGC / CNPJ)									
$NUMERO_INSCRICAO_EMPRESA = $row_master['cnpj']; //CNPJ DA EMPRESA
$remover = array(".", "-", "/",",");
$NUMERO_INSCRICAO_EMPRESA = str_replace($remover, "", $NUMERO_INSCRICAO_EMPRESA);
$NUMERO_INSCRICAO_EMPRESA = sprintf("%014s",$NUMERO_INSCRICAO_EMPRESA);

$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");
$NUMERO_CONTA = str_replace($remover, "", $NUMERO_CONTA);
$NUMERO_CONTA = substr($NUMERO_CONTA, 0, 7);
$NUMERO_CONTAF = sprintf("%07s",$NUMERO_CONTA);

/* DADOS DA CONTA CORRENTE */
//AGENCIA
$AGENCIA = $row_banco['agencia'];					
$AGENCIA = substr($AGENCIA, 0, 4); 
$AGENCIAF = sprintf("%05d",$AGENCIA);

$str = $row_banco['agencia'];
$ultimoDigitoAgencia = $str{strlen($str)-1};			
$DV_AGENCIA_EMPRESA = $ultimoDigitoAgencia;
$DV_AGENCIA_EMPRESA = sprintf("%0d",$DV_AGENCIA_EMPRESA);

//CONVÊNIO
$CONVENIO_EMPRESA = $AGENCIA.$NUMERO_CONTAF.'PG'; //O CODIGO DE CONVÉNIO DA EMPRESA, CONSISTE NO NUMERO DA AGENCIA COM 4 DIGITOS, SEGUIDO DO NUMERO DA CONTA CORRENTE COM 7 DIGITOS, MAIS A LITERAL FIXA "PG"
$CONVENIO_EMPRESA = sprintf("% -20s",$CONVENIO_EMPRESA);					

//CONTA
$NUMERO_CONTA = $row_banco['conta'];
$remover = array(".", "-", "/",",");
$NUMERO_CONTA = str_replace($remover, "", $NUMERO_CONTA);
$NUMERO_CONTA = substr($NUMERO_CONTA, 0, 7);
$NUMERO_CONTA = sprintf("%012s",$NUMERO_CONTA);

$str = $row_banco['conta'];
$ultimoDigitoConta = $str{strlen($str)-1};			
$DV_CONTA_CORRENTE  = $ultimoDigitoConta;									
$DV_CONTA_CORRENTE = sprintf("%0d",$DV_CONTA_CORRENTE);

$DV_AGENCIA_CONTA_CONVENIO = $DV_AGENCIA_EMPRESA;

$NOME_EMPRESA = $row_master['razao'];
$NOME_EMPRESA = sprintf("% -30s",$NOME_EMPRESA);

$NOME_BANCO = $row_banco['nome'];
$NOME_BANCO = sprintf("% -30s",$NOME_BANCO);

//??CNAB

$CODIGO_REMESSA = '1'; //VALOR FIXO PARA ARQUIVO DE REMESSA (CLIENTE -> BANCO)
$DATA_GERACAO_ARQUIVO = date('dmy');
$HORA_GERACAO_ARQUIVO = date('His');

//SEQUENCIA PARA O ARQUIVO DETALHES_REAL_CORRENTE
$numSequenciaCorrenteREAL = 1;

$LAYOUT_ARQUIVO = '040'; //VALOR PADRÃO
$DENSIDADE_DE_GRAVACAO = '1600 ';
$TIPO_DE_GRAVACAO = 'BPI';			


// INICIO DA IMPRESSAO
for($i=1;$i<=2;$i++){
    if($i == 1){
        $handle = fopen("../folha/BANCOS/REAL/{$this->CAMINHO}_C.txt", "w+");
        $this->numeroSequencial[c] = 1;
        $QUANT_REGISTROS = $this->numeroSequencial[c];
        $QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

        //$this->numeroSequencial[c] = $this->numeroSequencial[c] + 1;
    }else if($i == 2){
        $handle = fopen("../folha/BANCOS/REAL/{$this->CAMINHO}_S.txt", "w+");
        $this->numeroSequencial[s] = 1;
        $QUANT_REGISTROS = $this->numeroSequencial[s];
        $QUANT_REGISTROS = sprintf("%06d",$QUANT_REGISTROS);

        //$this->numeroSequencial[s] = $this->numeroSequencial[s] + 1;
    }
    
    fwrite($handle, $TIPO_REGISTRO,1);

    $RESERVADO_BANCO = ' ';
    $RESERVADO_BANCO = sprintf("% 8s",$RESERVADO_BANCO);
    fwrite($handle, $RESERVADO_BANCO,8);
    fwrite($handle, $TIPO_REGISTRO, 1);
    fwrite($handle, $TIPO_REGISTRO_PADRAO, 1);

    $CREDITOSCC = sprintf("%- 15s","CREDITOS C/C");
    fwrite($handle, $CREDITOSCC, 15);

    $NOME_DA_EMPRESA = sprintf("%- 20s",$row_master['razao']);
    fwrite($handle, $NOME_DA_EMPRESA, 20);

    $RESERVADO_BANCO = sprintf("% 30s"," ");
    fwrite($handle, $RESERVADO_BANCO,30);
    fwrite($handle, $COD_BANCO, 3);					
    fwrite($handle, $NOME_DO_BANCO, 15);

    fwrite($handle, $DATA_GERACAO_ARQUIVO, 6);
    fwrite($handle, $DENSIDADE_DE_GRAVACAO, 5);
    fwrite($handle, $TIPO_DE_GRAVACAO, 3);

    $RESERVADO_BANCO = sprintf("% 86s"," ");
    fwrite($handle, $RESERVADO_BANCO,86);

    $NUMERO_DA_LINHA = sprintf("%06s",'1');					
    fwrite($handle, $NUMERO_DA_LINHA, 6);
    fwrite($handle, "\r\n");
}
?>