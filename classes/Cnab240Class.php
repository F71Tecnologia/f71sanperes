<?php

/* 
 * M�dulo Objeto de exporta��o de arquivo remessa/retorno para os bancos padr�o CNAB240
 * Data Cria��o: 20/03/2015
 * Desenvolvimento: Jacques de Azevedo Nunes
 * e-mail: jacques@f71.com.br
 * Vers�o: 0.1 (Build 00001)
 * 
 * Obs sobre a vers�o: Essa vers�o da classe contempla por enquanto as modalidade do segmento A e E
 * do modelo CNAB240 posi��es
 *  
 * Estrutura do arquivo RET
 *  - [0] header do arquivo 
 *  - [1] header do lote
 *  - [2] Registros Iniciais do Lote (Opcional)
 *  - [3] detalhe do segmento
 *  - [4] Registros Finais do Lote (opcional)
 *  - [5] trailler de lote
 *  - [9] trailler de arquivo
 * 
 * Hierarquia dos processamentos
 * Servi�o/Forma/Categoria (Obs.: A forma determina o tipo de segmento utilizado / Segmento A e E implementado no detalhe)
 * 
 * Servi�o   (default 5  )
 * Forma     (tipos_pag_saida) determina o tipo de segmento utilizado na gera�ao do arquivo
 * Categoria (A ser implementada no sistema)
 * 
 * MT101 - Referente � pagamentos locais no Brasil
 * ===============================================
 * 1. Cr�dito em Conta Corrente
 * 2. Cr�dito em Conta Sal�rio
 * 3. Cr�dito em Conta Poupan�a
 * 4. DOC/TED
 * 5. Emiss�o de Recibo (OP)
 * 6. Emiss�o de Cheque Administrativo
 * 7. Pagamento de T�tulos de Cobran�a Banc�ria
 * 8. Pagamento de Contas de Concession�rias
 * 9. Pagamento de Tributos Padr�o com C�digo de Barras
 * 10.Pagamento do Tributos GPS
 * 11.Pagamento do Tributo DARF e DARF Simples
 * 
 * Estrutura da mensagem
 * =====================
 * Sequ�ncia A: De apenas uma ocorr�ncia, cont�m informa��es gerais que se aplicam a todas as opera��es individuais descritas na sequ�ncia B 
 * Sequ�ncia B: � repetitiva, isto �, cada ocorr�ncia � utilizada para indicar os detalhes de cada opera��o, individualmente.
 * 
 * Obs.: Utiliza sequ�ncia A para instru��o de um �nico ordenante e sequ�ncia B para v�rios
 * 
 * Altera��es a serem realizadas na base de dados necess�rias para o devido funcionamento da classe
 * 1. Inclus�o do padr�o de 'tipo de pagamento' no formato do cnab240 do bradesco tabela TIPO_PAG_SAIDA 
 * 2. Cria��o de campo ID_NACIONAL INT 3 DEFAULT NULL na tablea TIPO_PAG_SAIDA
 * 3. Cria��o de campo COD_FORMA_PAG INT 4 DEFAULT NULL na tabela TIPO_PAG_SAIDA
 * 4. Cria��o de campo SEQMENTO VARCHAR 1 DEFAULT NULL na tabela TIPO_PAG_SAIDA
 * 5. Cria��o de campo FLAG_REMESSA INT 1 DEFAULT 0 (0=Sem remessa processada, 1=Com remessa processada, 2=Com retorno processado) na tabela SAIDA
 * 6. Cria��o de campo ID_CATEGORIA_PAG_SAIDA INT 10 DEFAULT NULL na tabela SAIDA
 * 7. Cria��o de campo SEQUENCIA_CNAB240 INT 11 DEFAULT 0 na tabela BANCOS
 * 8. Cria��o da tabela CATEGORIA_PAG_SAIDA (ID_CATEGORIA_SAIDA INT 10 AUTO_INCREMENT, DESCRICAO VARCHAR 255 DEFAULT NULL, COD_CATEGORIA_PAG INT 10 DEFAULT 0, TIPO_OPERACAO VARCHAR 1 DEFAULT 'D')
 * 9. Cria��o da tabela CNAB240_RETORNO_MOTIVO (CODIGO VARCHAR(2), DESCRICAO VARCHAR(250))
 * 10.Cria��o da tabela CNAB240_RETORNO_MOVIMENTO (CODIGO VARCHAR(2), DESCRICAO VARCHAR(250))
 * 11.Cria��o da tabela CNAB240_RETORNO_OCORRENCIA (CODIGO VARCHAR(2), DESCRICAO VARCHAR(250))
 * 

 */
Class CNAB240
{

    const VERSAO = "0.1.00001";              // Vers�o/build da classe
    const LEN_CNAB240 = 240;                 // Define o tamanho do arquivo cnab240
    const EXT_FILE = ".REM";                 // Extens�o de arquivo remessa operacional
    const EXT_FILE_TEXT = ".RST";           // Extens�o de arquvio remessa para teste
    const FILE_LOG = "REM.LOG";              // Log de gera��o de arquivos remessa 
    
    protected $user = NULL;                       // Usu�rio respons�vel pela gera��o e cria��o do arquivo retorno
    protected $data_hora = "";
    protected $modo_teste = 1;                    // Define o modo padr�o de gera��o de arquivo remessa (defualt teste)
    protected $offset = 1;                        // offset para deslocamento de real de posi��o do arquivo
    
    protected $path = "";                         // Caminho do arquivo de remessa ou retorno
    protected $file = "";                         // Nome do aruivo de remessa ou retoro
    protected $query = "";                        // Query de consulta interna da classe
    protected $query_header = "";                 // Query de consulta do arquivo Header
    protected $query_lote = "";                   // Query de consulta do arquivo de Lote
    protected $query_detalhe = "";                // Query de consulta do arquivo detalhe
    protected $query_trailler = "";               // Query de consulta do arquivo trailler
    protected $query_arquivo = "";                // Query de consulta do arquivo final
    protected $query_remessa = "";                // Query de set de tuples de remessa processados
    protected $query_retorno = "";                // Query de set de tuples de retorno processados
    protected $rs = "";                           // Record Set gen�rico para uso geral
    protected $rsHeader = "";                     // Record Set da consulta para defini��o do header do arquivo
    protected $rsLote = "";                       // Record Set da consulta para defini��o do lote do arquivo    
    protected $rsDetalhe = "";                    // Record Set da consulta para defini��o do detalhe do arquivo
    protected $rsTrailler = "";                   // Record Set da consulta para defini��o do trailler do arquvo
    protected $rsArquivo = "";                    // Record Set da consulta para defini��o do arquivo do arquvo
    protected $row = "";                          // tuple de retorno de processamento do recordset
    protected $rowLabel = "";                     // Label do tipo de linha processada
    protected $rowString = "";                    // Linha em formato de string de uma tuple de recorset
    protected $buffer = "";                       // Buffer que armazena todo os registros de sa�da processados para serem salvos no arquivo de remessa
    protected $error = "";                        // Exite mensagens de erro da classe
    protected $controle_registro = "";            // Registra o controle de registro para processamento de arquivo retorno
    protected $campos = NULL ;                    // Vetor com nome de campos da tabela corrente
    protected $connect;                           
    
    // Defini��o de vari�veis para execu��o da Query de consulta e gera��o de arquivo
    protected $id_banco = "";                     // 
    protected $id_regiao = "";                    //
    protected $id_projeto = "";                   //
    protected $id_saida = "";                     // ids agrupados em string separados por virgula para sele��o de registros ex: '1,2,35'
    protected $controle_lote = 0;                 // Contador do arquivo de lote
    
    // Registro head principal padr�o bradesco

    // Head/Controle
    protected $header_controle_banco = "";                   // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $header_controle_lote = "";                    // N�mero do lote da opera��o 4 posi��es num [4-7] default 0000
    protected $header_controle_tipo = "";                    // Tipo de registro 1 posi��o num [8-8] default 0
    
    // Head/Cnab
    protected $header_cnab_1 = "";                 // Uso exclusivo da FEBRAN/CNAB240 9 posi��es num [9-17] default brancos
    
    // Head/Empresa
    protected $header_empresa_insc_tipo = "";       // Tipo de inscri��o da empresa 1 posi��o num [18-18] 
    protected $header_empresa_num_insc = "";        // N�mero de inscri��o da empresa 14 posi��es num [19-32]
    protected $header_empresa_cod_convenio = "";    // C�digo do conv�nio no banco 20 posi��es alfa [33-52]
    protected $header_empresa_agencia = "";         // Ag�ncia do banco 5 posi��es num [53-57]
    protected $header_empresa_agencia_digito = "";  // D�gito da ag�ncia 1 posi��o alfa [58-58]
    protected $header_empresa_conta = "";                   // Conta corrente 12 posi�oes num [59-70]
    protected $header_empresa_conta_digito = "";            // D�gito da conta 1 posi��o alfa [71]
    protected $header_empresa_agencia_conta_digito = "";    // D�gito da ag�ncia e conta 1 posi��o alfa [72-72]
    protected $header_empresa_nome = "";            // Raz�o social da empresa 30 posi�oes alfa [73-102]
        // Head/Nome do banco
    protected $header_banco_nome = "";              // Nome do banco 30 posi��es alfa [103-132]
    
    // Head/Cnab
    protected $header_cnab_2 = "";                 // Uso exclusivo da FEBRAN/CNAB240 10 posi��es alfa [133-142] default brancos
    
    // Head/Arquivo
    protected $header_arquivo_codigo = "";                  // C�digo remessa/retorno 1 posi��o num [143-143] 
    protected $header_arquivo_data = "";                    // Data da gera��o do arquivo 8 posi��es num [144-151]
    protected $header_arquivo_hora = "";                    // Hora da gera��o do arquivo 6 posi��es num [152-157]
    protected $header_arquivo_sequencial = "";              // Sequencial do arquivo 6 posi��es num [158-163]
    protected $header_arquivo_versao_layout = "";           // Versao layout do arquivo 3 posi��es num [164-166] default 050
    protected $header_arquivo_densidade = "";               // Densidade de grava��o do arquivo 5 posi��es num [167-171]
    protected $header_reservado_banco =  "";        // Para uso reservado do banco 20 posi��es alfa [172-191]
    protected $header_reservado_empresa = "";       // Para uso reservado da empresa 20 posi��es alfa [192-211]
    protected $header_cnab_3 = "";                 // Uso exclusivo da FEBRAN/CNAB240 29 posi��es alfa [212-240] default brancos
    
    
    // Registro head de lote 
    
    // Lote/Controle/Igual em todos os segmentos (Um lote � montado baseado no conjunto de servi�os/protudos comuns a serem processados
    protected $lote_controle_banco = "";                     // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $lote_controle_lote = "";                      // N�mero do lote da opera��o 4 posi��es num [4-7] default 0000
    protected $lote_controle_tipo = "";                      // Tipo de registro 1 posi��o num [8-8] default 1
    
    // Lote/Servi�o A,B,C e E
    protected $lote_servico_operacao = "";                   // Tipo de opera��o 1 posi��o alfa [9-9] default E
    protected $lote_servico_servico = "";                    // N�mero de inscri��o da empresa 2 posi��es num [10-11] default 04
    protected $lote_servico_forma = "";                      // Forma de lan�amento 3 posi��es alfa [12-13] default 40
    protected $lote_servico_layout = "";                     // Layout do lote 3 posi��es num [14-16] default 050
    
    // Lote/Servi�o A,B,C e E
    protected $lote_cnab_1 = "";                             // Uso exclusivo da FEBRAN/CNAB240 1 posi��o alfa [17-17] default brancos
    
    protected $lote_empresa_tipo_inscricao = "";             // Tipo de inscri��o da empresa 1 posi��o num [18-18] 
    protected $lote_empresa_num_inscricao = "";              // N�mero de inscri��o da empresa 14 posi��es num [19-32]
    protected $lote_empresa_cod_convenio_banco = "";         // C�digo do conv�nio no banco 20 posi��es alfa [33-52]
    protected $lote_empresa_agencia = "";                    // Ag�ncia 5 posi��es num [53-57]
    protected $lote_empresa_agencia_digito = "";             // D�gito da ag�ncia 1 posi��o alfa [58-58]
    protected $lote_empresa_conta = "";                      // Conta corrente 12 posi�oes num [59-70]
    protected $lote_empresa_conta_digito = "";               // D�gito da conta 1 posi��o alfa [71-71]
    protected $lote_empresa_agencia_conta_digito = "";       // D�gito da ag�ncia e conta 1 posi��o alfa [72-72]
    protected $lote_empresa_nome = "";                       // Raz�o social da empresa 30 posi�oes alfa [73-102]
    
    // Lote/Cnab/Segmento E
    protected $lote_cnab_2E = "";                            // Uso exclusivo da FEBRAN/CNAB240 40 posi��es alfa [103-142] default brancos

    // Lote/Cnab/Segmento A,B e C
    protected $lote_cnab_2ABC = "";                          // Uso exclusivo da FEBRAN/CNAB240 40 posi��es alfa [103-142] default brancos
    
    // Lote/Informa��o 1/Segmento A,B e C
    protected $lote_informacao1 = "";

    // Lote/Endere�o/Segmento A,B e C
    protected $lote_endereco_logradouro = "";
    protected $lote_endereco_numero = "";
    protected $lote_endereco_complemento = "";
    protected $lote_endereco_cidade = "";
    protected $lote_endereco_cep = "";
    protected $lote_endereco_comp_cep = "";
    protected $lote_endereco_estado = "";
    
    
    protected $lote_ocorrencias = "";


    // Lote/Arquivo/Segmento E
    protected $lote_data = "";                      // Data do saldo inicial 8 posi��es num [143-150] 
    protected $lote_saldo = "";                     // Valor do saldo inicial 16+2 posi��es decimais num [151-168]
    protected $lote_situacao = "";                  // Situ��o do saldo inicial 1 posi��o alfa [169-169]
    protected $lote_status = "";                    // Posi��o do saldo inicial 1 posi��o alfa [170-170] 
    protected $lote_tipo_moeda = "";                // Moeda referenciada no extrato 3 posi��es alfa [171-173]
    protected $lote_seq_extrato = "";               // Sequ�ncia do extrato 5 posi��es num [174-178]
    protected $lote_cnab_3E = "";                   // Uso exclusivo da FEBRAN/CNAB240 62 posi��es alfa [179-240] default brancos
    

    // Detalhe/Controle/Igual em todos os segmentos
    protected $detalhe_controle_banco = "";         // C�digo da institui��o banc�ria no Brasil para compensa��o 3 posi��es num [1-3]
    protected $detalhe_controle_lote = "";          // N�mero do lote de servi�o 4 posi��es num [4-7] default 0000
    protected $detalhe_controle_registro = "";      // Tipo de registro 1 posi��o num [8-8] default 3
    
    // Detalhe/Servi�o/Segmento A,E
    protected $detalhe_servico_num_registro = "";   // N�mero de registro do 5 posi��es num [9-13] 
    protected $detalhe_servico_cod_segmento = "";   // C�digo de segmento detalhe 1 posi��o alfa [14-14]
    
    // Detalhe/Favorecido/Segmento A
    protected $detalhe_favorecido_camara = "";      // C�digo da C�mara Centralizadora 3 posi��es num [18-20]
    protected $detalhe_favorecido_banco = "";       // C�digo do Banco do Favorecido 3 posi��es num [21-23]
    protected $detalhe_favorecido_agencia = "";     // Ag�ncia mantenedora da conta do favorecido 5 posi��es num [24-28]
    protected $detalhe_favorecido_agencia_digito = ""; // D�gito verificador da ag�ncia 1 posi��o alfa [29-29]
    protected $detalhe_favorecido_conta = "";         // N�mero da conta corrente 12 posi��es num [30-41]
    protected $detalhe_favorecido_conta_digito = "";  // D�gito verificador da Ag�ncia 1 posi��o alfa [42-42]
    protected $detalhe_favorecido_agencia_conta_digito = ""; // D�gito verificador da Ag�ncia e Conta 1 posi��o alfa [43-43]
    protected $detalhe_favorecido_nome = "";        // Nome do favorecido 30 posi��es alfa [44-73]
    
    // Detalhe/Cr�dito/Segmento A
    protected $detalhe_credito_seu_numero = "";             // N�mero do documento atribu�do pela empresa 20 posi�oes alfa [74-93]
    protected $detalhe_credito_data_pagamento = "";         // Data do pagamento 8 posi��es num [94-101]
    protected $detalhe_credito_moeda_tipo = "";             // Tipo da moeda 3 posi��es alfa [102-104]
    protected $detalhe_credito_moeda_quantidade = "";       // Quantidade da Moeda 10+5 posi��s num [105-119]
    protected $detalhe_credito_valor_pagamento = "";        // Valor do pagamento 13+2 posi��es num [120-134]
    protected $detalhe_credito_nosso_numero = "";           // N�mero do documento atribu�do pelo Banco 20 posi��es alfa [135-154]
    protected $detalhe_credito_data_real = "";              // Data real da efetiva��o do pagamento 8 posi��es num [155-162]
    protected $detalhe_credito_valor_real = "";             // Valor real da efetiva��o do pagamento 13+2 posi��es num [163-177]

    // Detalhe/Informa��o 2/Segmento A
    protected $detalhe_informacao_2 = "";                   // Outras informa��es para identifica��o de dep�sito Judicial e pagto sal�rios de servidores pelo SIAPE (formata��o G031) 40 posi��es alfa [178-217]

    // Detalhe/C�digo/Segmento A
    protected $detalhe_codigo_finalidade_doc = "";          // Complemento do tipo de servi�o 2 posi��es alfa [218-219]
    protected $detalhe_codigo_finalidade_ted = "";          // C�digo finalidade da TED 5 posi��es alfa [220-224]
    

    // Detalhe/Cnab/Todos segmentos
    protected $detalhe_cnab_1E = "";               // Uso exclusivo da FEBRAN/CNAB240 9 posi��es num [9-17] default brancos
    protected $detalhe_cnab_1A = "";               // Uso exclusivo da FEBRAN/CNAB240 5 posi��es num [225-229] default brancos    
    
    // Detalhe/Aviso/Segmento A
    protected $detalhe_aviso = "";                  // Aviso ao Favorecido 1 posi��o num [230-230]
    
    // Detalhe/Ocorr�ncias/Segmento A
    protected $detalhe_ocorrencias = "";            // C�digo das ocorr�ncias para retorno 10 posi��es alfa [231-240]

    // Detalhe/Empresa
    protected $detalhe_controle_registro_inscricao = "";  // Tipo de inscri��o 1 posi��o num [18-18] (0 - Isento/1 - CPF/2 - CNPJ/3 - PIS/9 - Outros)
    protected $detalhe_numero_inscricao = "";       // N�mero de inscri��o 14 posi��es num [19-32]
    protected $detalhe_cod_convenio = "";           // C�digo do conv�nio no Banco 20 posi��es alfa [33-52]
    protected $detalhe_agencia = "";                // Ag�ncia 5 posi��es num [53-57]
    protected $detalhe_agencia_digito = "";         // D�gito verificador da agencia 1 posi��o num [58-58]
    protected $detalhe_conta = "";                  // Conta corrente 12 posi��es num [59-70]
    protected $detalhe_conta_digito = "";           // D�gito verificador da conta 1 posi��o alfa [71-71]
    protected $detalhe_agencia_conta_digito = "";   // D�gito da ag�ncia e conta 1 posi��o alfa [72-72]
    protected $detalhe_empresa_nome = "";           // Raz�o social da empresa 30 posi�oes alfa [73-102]

    // Lote/Cnab/Segmento E
    protected $detalhe_cnab_2E = "";                 // Uso exclusivo da FEBRAN/CNAB240 6 posi��es alfa [103-108] default brancos

    // Lote/Natureza/Segmento E
    protected $detalhe_natureza_lancamento = "";     // Natureza do lan�amento 3 posi��es alfa [109-111] 

    // Lote/Tipo Complemento/Segmento E
    protected $detalhe_controle_registro_complemento = "";        // Tipo do complemento 2 posi��es num [112-112]
    protected $detalhe_complemento = "";             // Complemento do lan�amento 20 posi��o alfa [114-133]
    protected $detalhe_cpmf = "";                    // Identifica��o de isen��o do cpmf 1 alfa [134-134]
    protected $detalhe_data = "";                    // Posi��o do saldo inicial 8 posi��o num [135-142] 

    // Lote/Lan�amento/Segmento E
    protected $detalhe_lancamento_data = "";         // Data do lan�amento 8 posi��es num [143-150]
    protected $detalhe_lancamento_valor = "";        // Valor do lan�amento 16+2 num [151-169]
    protected $detalhe_lancamento_tipo = "";         // Tipo do lan�amento com valor a d�bito ou cr�dito 1 posi��o alfa [169-169]
    protected $detalhe_lancamento_categoria = "";    // Categoria do lan�amento 3 posi��es num [170-172]
    protected $detalhe_lancamento_cod_historico = "";// C�digo do hist�rico no banco 4 alfa [173-176]
    protected $detalhe_lancamento_historico = "";    // Descri��o do hist�rico no banco 25 posi��es alfa [177-201]
    protected $detalhe_lancamento_documento = "";    // N�mero do documento/complemento 39 posi��es alfa [202-240]
            
    // Trailler
    
    // Trailler/Controle
    protected $trailler_banco = "";               // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $trailler_lote = "";                // N�mero do lote da opera��o 4 posi��es num [4-7] default 0000
    protected $trailler_tipo = "";                // Tipo de registro 1 posi��o num [8-8] default 1
    // Trailler/Cnab
    protected $trailler_cnab_1 = "";              // Uso exclusivo da FEBRAN/CNAB240 9 posi��o alfa [9-17] default brancos
    // Trailler/Empresa
    protected $trailler_tipo_inscricao = "";       // Tipo de inscri��o 1 posi��o num [18-18] 
    protected $trailler_numero_inscricao = "";     // N�mero de inscri��o 14 posi��es num [19-32]
    protected $trailler_cod_convenio = "";         // C�digo do conv�nio no Banco 20 posi��es alfa [33-52]
    protected $trailler_agencia = "";              // Ag�ncia 5 posi��es num [53-57]
    protected $trailler_agencia_digito = "";       // D�gito verificador da agencia [58-58]
    protected $trailler_conta = "";                // Conta corrente 12 num [59-70]
    protected $trailler_conta_digito = "";         // D�gito verificador da conta 1 posi��o alfa [71-71]
    protected $trailler_agencia_conta_digito = ""; // D�gito da ag�ncia e conta 1 posi��o alfa [72-72]
    // Trailler/Cnab
    protected $trailler_cnab_2 = "";               // Uso exclusivo da FEBRAN/CNAB240 16 posi��es alfa [73-88] default brancos
    // Trailler/Valores
    protected $trailler_bloqueado1 = "";           // Vinculado do dia anteior 16+2 posi��es num [89-106]
    protected $trailler_limite = "";               // Limite da conta 16+2 posi��es num [107-124]
    protected $trailler_bloqueado2 = "";           // Vinculado do dia anteior 16+2 posi��es num [125-142]
    // Traillher/Saldo Final
    protected $trailler_data = "";                 // Data do saldo final 8 posi��es num [143-150]
    protected $trailler_valor = "";                // Valor do saldo final 16+2 posi��es num [151-168]
    protected $trailler_situacao = "";             // Situa��o do saldo final 1 posi��o alfa [169-169]
    protected $trailler_status = "";               // Posi��o do saldo final 1 posi��o alfa [170-170]
    // Trailler/Totais
    protected $trailler_tot_registros = "";        // Quantidade total de registro do lote 6 posi��es [171-176]
    protected $trailler_tot_debitos = "";          // Somat�rio dos valores a d�bito 16+2 [177-194]
    protected $trailler_tot_creditos = "";         // Somat�rio doa valores a cr�dito 16+2 [195-212]
    protected $trailler_cnab_3 = "";               // Uso exclusivo da FEBRAN/CNAB240 28 posi��es alfa [213-240] default brancos

    // Registro Trailler de arquivo
    // Arquivo/Controle
    protected $arquivo_banco = "";                   // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $arquivo_lote = "";                    // N�mero do lote da opera��o 4 posi��es num [4-7] default 9999
    protected $arquivo_tipo = "";                    // Tipo de registro 1 posi��o num [8-8] default 9
    // Arquivo/Cnab
    protected $arquivo_cnab_1 = "";                  // Uso exclusivo da FEBRAN/CNAB240 9 posi��es alfa [9-17] default brancos
    protected $arquivo_tot_lotes = "";               // Quantidade de lotes do arquivo 6 posi��es nun [18-23]
    protected $arquivo_tot_registros = "";           // Quantidade de registros do arquivo 6 posi��es num [24-29]
    protected $arquivo_tot_conciliacao = "";         // Quantidade de contas para concilia��o 6 posi��es num [30-35]
    protected $arquivo_cnab_2 = "";                  // Uso exclusivo da FEBRAN/CNAB240 205 posi��es alfa [36-240] default brancos
    
          
    function __construct(){ 
        
    }
    
    public function setTeste($valor){
            $this->modo_teste = $valor;
    }
    
    public function setBanco($valor){
            $this->id_banco = $valor;
    }
    
    public function setRegiao($valor){
            $this->id_banco = $valor;
    }
    
    public function setProjeto($valor){
            $this->id_projeto = $valor;
    }
    
    public function setSaida($valor){
            $this->id_saida = $valor;
    }
    
    public function setPath($valor) {
            $this->path = $valor;
    }	
    

    /*
     * Setar os IDs que dever�o gerar o arquivo remessa
     */
    public function setIdsSaidas($valor){
            $this->ids_saidas = $valor;
    }

    public function setSegmento($valor){
            $this->id_segmento = $valor;
    }
    
    public function setFile($valor) {
            $this->file = $valor;
    }	
    
    private function setQuery($valor){
            $this->query = $valor;
    }
    
    private function setError($valor) {
            $this->error = $valor;
    }
    
    private function setControleLote($valor) {
            $this->controle_lote = $valor;
    }
    
    /*PGDDMMX.REM OU PGDDMMXX.REM
     * 1 ou 2 vari�veis alfanum�ricas: 0, 01, AB, A1, etc.
     * Exemplo: PG250601.REM , PG2506AB.REM , PG2506A1.REM , etc.
     * Quanto ao arquivo-retorno ter� a mesma formata��o, por�m, com a extens�o RET.
     * Exemplo: PG250600.RET , PG250601.RET , PG2506AB.RET , ETC.
     */
    
    /*
     * Set de defini��es do registro Header do arquivo
     */
        
    private function setHeaderControleBanco($valor) {
            $this->header_controle_banco  = vsprintf("%3s",$valor);
    }	

    private function setHeaderControleLote($valor) {
            $this->header_controle_lote = vsprintf("%4s",$valor);
    }	

    private function setHeaderControleTipo($valor) {
            $this->header_controle_tipo = vsprintf("%1s",$valor);
    }	

    private function setHeaderCnab1($valor) {
            $this->header_cnab_1 = vsprintf("%9s",$valor);
    }	

    private function setHeaderEmpresaInscTipo($valor) {
            $this->header_empresa_insc_tipo = vsprintf("%1s",$valor);
    }	

    private function setHeaderEmpresaNumInsc($valor) {
            $this->header_empresa_num_insc = vsprintf("%14s",$valor);
    }	
    
    private function setHeaderEmpresaCodConvenio($valor) {
            $this->header_empresa_cod_convenio = vsprintf("%20s",$valor);
    }	

    private function setHeaderEmpresaAgencia($valor) {
            $this->header_empresa_agencia = vsprintf("%5s",$valor);
    }	
    
    private function setHeaderEmpresaAgenciaDigito($valor) {
            $this->header_empresa_agencia_digito = vsprintf("%1s",$valor);
    }	

    private function setHeaderEmpresaConta($valor) {
            $this->header_empresa_conta = vsprintf("%12s",$valor);
    }	

    private function setHeaderEmpresaContaDigito($valor) {
            $this->header_empresa_conta_digito = vsprintf("%1s",$valor);
    }	

    private function setHeaderEmpresaAgenciaContaDigito($valor) {
            $this->header_empresa_agencia_conta_digito = vsprintf("%1s",$valor);
    }	
    
    private function setHeaderEmpresaNome($valor) {
            $this->header_empresa_nome = vsprintf("%30s",$valor);
    }	
    
    private function setHeaderNomeBanco($valor) {
            $this->header_banco_nome = vsprintf("%30s",$valor);
    }	
    
    private function setHeaderCnab2($valor) {
            $this->header_cnab_2 = vsprintf("%10s",$valor);
    }	

    private function setHeaderArquivoCodigo($valor) {
            $this->header_arquivo_codigo = vsprintf("%1s",$valor);
    }	

    private function setHeaderArquivoData($valor) {
            $this->header_arquivo_data = vsprintf("%8s",$valor);
    }	
    
    private function setHeaderArquivoHora($valor) {
            $this->header_arquivo_hora = vsprintf("%6s",$valor);
    }	

    private function setHeaderArquivoSequencial($valor) {
            $this->header_arquivo_sequencial = vsprintf("%6s",$valor);
    }	

    private function setHeaderArquivoVersaoLayout($valor) {
            $this->header_arquivo_versao_laytou = vsprintf("%3s",$valor);
    }	

    private function setHeaderArquivoDensidade($valor) {
            $this->header_arquivo_densidade = vsprintf("%5s",$valor);
    }	

    private function setHeaderReservadoBanco($valor) {
            $this->header_reservado_banco = vsprintf("%20s",$valor);
    }	
    
    private function setHeaderReservadoEmpresa($valor) {
            $this->header_reservado_empresa = vsprintf("%20s",$valor);
    }	

    private function setHeaderCnab3($valor) {
            $this->header_cnab_3 = vsprintf("%29s",$valor);
    }	
    
    private function setQueryHeader($valor){
            $this->query_header = $valor;
    }
    
    
    /*
     * Defini��es dos registro de lote do arquivo
     */
    
    
    private function setLoteControleBanco($valor) {
            $this->lote_controle_banco = vsprintf("%3s",$valor);
    }	

    private function setLoteControleLote($valor) {
            $this->lote_controle_lote = vsprintf("%04s",$valor);
    }	

    private function setLoteControleTipo($valor) {
            $this->lote_controle_tipo = vsprintf("%1s",$valor);
    }	
    
    private function setLoteServicoOperacao($valor) {
            $this->lote_servico_operacao = vsprintf("%1s",$valor);
    }	

    private function setLoteServicoServico($valor){
           $this->lote_servico_servico = vsprintf("%2s",$valor);
    }

    private function setLoteServicoForma($valor){
           $this->lote_servico_forma = vsprintf("%2s",$valor);
    }
    
    private function setLoteServicoLayout($valor){
           $this->lote_servico_layout = vsprintf("%3s",$valor);
    }

    private function setLoteCnab1($valor){
           $this->lote_cnab_1 = vsprintf("%1s",$valor);
    }
    
    private function setLoteEmpresaTipoInscricao($valor){
           $this->lote_empresa_tipo_inscricao = vsprintf("%1s",$valor);
    }

    private function setLoteEmpresaNumInscricao($valor){
           $this->lote_empresa_num_inscricao = vsprintf("%14s",$valor);
    }
    
    private function setLoteEmpresaCodConvenioBanco($valor){
           $this->lote_empresa_cod_convenio_banco = vsprintf("%20s",$valor);
    }

    private function setLoteEmpresaAgencia($valor){
           $this->lote_empresa_agencia = vsprintf("%5s",$valor);
    }
    
    private function setLoteEmpresaAgenciaDigito($valor){
           $this->lote_empresa_agencia_digito = vsprintf("%1s",$valor);
    }

    private function setLoteEmpresaConta($valor){
           $this->lote_empresa_conta = vsprintf("%12s",$valor);
    }

    private function setLoteEmpresaContaDigito($valor){
           $this->lote_empresa_conta_digito = vsprintf("%1s",$valor);
    }

    private function setLoteEmpresaAgenciaContaDigito($valor){
           $this->lote_empresa_agencia_conta_digito = vsprintf("%1s",$valor);
    }

    private function setLoteEmpresaNome($valor){
           $this->lote_empresa_nome = vsprintf("%30s",$valor);
    }
    
    private function setLoteInformacao1($valor){
           $this->lote_informacao1 = vsprintf("%40s",$valor);
    }

    private function setLoteEnderecoLogradouro($valor){
           $this->lote_endereco_logradouro = vsprintf("%30s",$valor);
    }
    
    private function setLoteEnderecoNumero($valor){
           $this->lote_endereco_numero = vsprintf("%5s",$valor);
    }
    
    private function setLoteEnderecoComplemento($valor){
           $this->lote_endereco_complemento = vsprintf("%15s",$valor);
    }

    private function setLoteEnderecoCidade($valor){
           $this->lote_endereco_cidade = vsprintf("%20s",$valor);
    }

    private function setLoteEnderecoCep($valor){
           $this->lote_endereco_cep = vsprintf("%5s",$valor);
    }
    
    private function setLoteEnderecoCompCep($valor){
           $this->lote_endereco_comp_cep = vsprintf("%3s",$valor);
    }
    
    private function setLoteEnderecoEstado($valor){
           $this->lote_endereco_estado = vsprintf("%2s",$valor);
    }
    
    private function setLoteOcorrencias($valor){
           $this->lote_ocorrencias = vsprintf("%2s",$valor);
    }
    private function setLoteCnab2E($valor){
           $this->lote_cnab_2E = vsprintf("%40s",$valor);
    }

    private function setLoteCnab2ACB($valor){
           $this->lote_cnab_2ABC = vsprintf("%8s",$valor);
    }
    
    private function setLoteData($valor){
           $this->lote_data = vsprintf("%8s",$valor);
    }

    private function setLoteSaldo($valor){
           $this->lote_saldo = vsprintf("%18s",$valor);
    }

    private function setLoteSituacao($valor){
           $this->lote_situacao = vsprintf("%1s",$valor);
    }
    
    private function setLoteStatus($valor){
           $this->lote_status = vsprintf("%1s",$valor);
    }

    private function setLoteTipoMoeda($valor){
           $this->lote_tipo_moeda = vsprintf("%3s",$valor);
    }

    private function setLoteSeqExtrato($valor){
           $this->lote_seq_extrato = vsprintf("%5s",$valor);
    }

    private function setLoteCnab3E($valor){
           $this->lote_cnab_3E = vsprintf("%62s",$valor);
    }
    
    private function setQueryLote($valor){
            $this->query_lote = $valor;
    }
    
    /*
     * Set de denifi��es do registo detalhe do arquivo
     */
    
    
    private function setDetalheControleBanco($valor){
           $this->detalhe_controle_banco = vsprintf("%3s",$valor);
    }
    
    private function setDetalheControleLote($valor){
           $this->detalhe_controle_lote = vsprintf("%04s",$valor);
    }

    private function setDetalheControleTipo($valor){
           $this->detalhe_controle_registro = vsprintf("%1s",$valor);
    }

    private function setDetalheServicoNumRegistro($valor){
           $this->detalhe_servico_num_registro = vsprintf("%5s",$valor);
    }

    private function setDetalheServicoCodSegmento($valor){
           $this->detalhe_servico_cod_segmento = vsprintf("%1s",$valor);
    }

    private function setDetalheCnab1E($valor){
           $this->detalhe_cnab_1E = vsprintf("%3s",$valor);
    }
    
    private function setDetalheCnab1A($valor){
           $this->detalhe_cnab_1A = vsprintf("%5s",$valor);
    }
    
    private function setDetalheFavCamara($valor){
           $this->detalhe_favorecido_camara = vsprintf("%3s",$valor);
    }
    
    private function setDetalheFavBanco($valor){
           $this->detalhe_favorecido_banco = vsprintf("%3s",$valor);
    }

    private function setDetalheFavAgencia($valor){
           $this->detalhe_favorecido_agencia = vsprintf("%5s",$valor);
    }

    private function setDetalheFavAgenciaDigito($valor){
           $this->detalhe_favorecido_agencia_digito = vsprintf("%1s",$valor);
    }

    private function setDetalheFavConta($valor){
           $this->detalhe_favorecido_conta = vsprintf("%12s",$valor);
    }

    private function setDetalheFavContaDigito($valor){
           $this->detalhe_favorecido_conta_digito = vsprintf("%12s",$valor);
    }

    private function setDetalheFavAgenciaContaDigito($valor){
           $this->detalhe_favorecido_agencia_conta_digito = vsprintf("%1s",$valor);
    }
    
    private function setDetalheFavNome($valor){
           $this->detalhe_favorecido_nome = vsprintf("%30s",$valor);
    }

    private function setDetalheCredSeuNumero($valor){
           $this->detalhe_credito_seu_numero = vsprintf("%20s",$valor);
    }

    private function setDetalheCredDataPagamento($valor){
           $this->detalhe_credito_data_pagamento = vsprintf("%8s",$valor);
    }
    
    private function setDetalheCredMoedaTipo($valor){
           $this->detalhe_credito_moeda_tipo = vsprintf("%3s",$valor);
    }

    private function setDetalheCredMoedaQuantidade($valor){
           $this->detalhe_credito_moeda_quantidade = vsprintf("%15s",$valor);
    }
    
    private function setDetalheCredValorPagamnto($valor){
           $this->detalhe_credito_valor_pagamento = vsprintf("%15s",$valor);
    }
    
    private function setDetalheCredNossoNumero($valor){
           $this->detalhe_credito_nosso_numero = vsprintf("%20s",$valor);
    }
    
    private function setDetalheCredDataReal($valor){
           $this->detalhe_credito_data_real = vsprintf("%8s",$valor);
    }

    private function setDetalheCredValorReal($valor){
           $this->detalhe_credito_valor_real = vsprintf("%15s",$valor);
    }
    
    private function setDetalheInformacao2($valor){
           $this->detalhe_informacao_2 = vsprintf("%15s",$valor);
    }
    
    private function setDetalheCodFinalidadeDoc($valor){
           $this->detalhe_codigo_finalidade_doc = vsprintf("%2s",$valor);
    }

    private function setDetalheCodFinalidadeTed($valor){
           $this->detalhe_codigo_finalidade_ted = vsprintf("%5s",$valor);
    }
    
    private function setDetalheAviso($valor){
           $this->detalhe_aviso = vsprintf("%1s",$valor);
    }
    
    private function setDetalheOcorrencias($valor){
           $this->detalhe_ocorrencias = vsprintf("%10s",$valor);
    }

    
    private function setDetalheControleTipoInscricao($valor){
           $this->detalhe_tipo_inscricao = vsprintf("%1s",$valor);
    }

    private function setDetalheNumeroInscricao($valor){
           $this->detalhe_numero_inscricao = vsprintf("%14s",$valor);
    }

    private function setDetalheCodConvenio($valor){
           $this->detalhe_cod_convenio = vsprintf("%20s",$valor);
    }

    private function setDetalheAgencia($valor){
           $this->detalhe_agencia = vsprintf("%5s",$valor);
    }
    
    private function setDetalheAgenciaDigito($valor){
           $this->detalhe_agencia_digito = vsprintf("%1s",$valor);
    }
    
    private function setDetalheConta($valor){
           $this->detalhe_conta = vsprintf("%12s",$valor);
    }
    
    private function setDetalheContaDigito($valor){
           $this->detalhe_conta_digito = vsprintf("%1s",$valor);
    }
    
    private function setDetalheAgenciaContaDigito($valor){
           $this->detalhe_agencia_conta_digito = vsprintf("%1s",$valor);
    }

    private function setDetalheEmpresaNome($valor){
           $this->detalhe_empresa_nome = vsprintf("%30s",$valor);
    }

    private function setDetalheCnab02($valor){
           $this->detalhe_cnab_02 = vsprintf("%6s",$valor);
    }
    
    private function setDetalheNaturezaLancamento($valor){
           $this->detalhe_natureza_lancamento = vsprintf("%3s",$valor);
    }

    private function setDetalheControleTipoComplemento($valor){
           $this->detalhe_tipo_complemento = vsprintf("%2s",$valor);
    }
    
    private function setDetalheComplemento($valor){
           $this->detalhe_complemento = vsprintf("%20s",$valor);
    }
    
    private function setDetalheCpmf($valor){
           $this->detalhe_cpmf = vsprintf("%1s",$valor);
    }

    private function setDetalheData($valor){
           $this->detalhe_data = vsprintf("%8s",$valor);
    }

    private function setDetalheLancamentoData($valor){
           $this->detalhe_lancamento_data = vsprintf("%8s",$valor);
    }

    private function setDetalheLancamentoValor($valor){
           $this->detalhe_lancamento_valor = vsprintf("%18s",$valor);
    }
    
    private function setDetalheLancamentoTipo($valor){
           $this->detalhe_lancamento_tipo = vsprintf("%1s",$valor);
    }

    private function setDetalheLancamentoCategoria($valor){
           $this->detalhe_lancamento_categoria = vsprintf("%3s",$valor);
    }

    private function setDetalheLancamentoCodHistorico($valor){
           $this->detalhe_lancamento_cod_historico = vsprintf("%4s",$valor);
    }
    
    private function setDetalheLancamentoHistorico($valor){
           $this->detalhe_lancamento_descricao = vsprintf("%25s",$valor);
    }
    
    private function setDetalheLancamentoDocumento($valor){
           $this->detalhe_lancamento_documento = vsprintf("%39s",$valor);
    }
    
    private function setQueryDetalhe($valor){
            $this->query_detalhe = $valor;
    }

    /*
     * Set de denifi��es do registo trailler do arquivo
     */
    
    private function setTraillerBanco($valor){
           $this->trailler_banco = vsprintf("%3s",$valor);
    }
    
    private function setTraillerLote($valor){
           $this->trailler_lote = vsprintf("%04s",$valor);
    }
    
    private function setTraillerTipo($valor){
           $this->trailler_tipo = vsprintf("%1s",$valor);
    }

    private function setTraillerCnab01($valor){
           $this->trailler_cnab_1 = vsprintf("%9s",$valor);
    }
    
    private function setTraillerTipoInscricao($valor){
           $this->trailler_tipo_inscricao = vsprintf("%1s",$valor);
    }

    private function setTraillerNumeroInscricao($valor){
           $this->trailler_numero_inscricao = vsprintf("%14s",$valor);
    }

    private function setTraillerCodConvenio($valor){
           $this->trailler_cod_convenio = vsprintf("%20s",$valor);
    }

    private function setTraillerAgencia($valor){
           $this->trailler_agencia = vsprintf("%5s",$valor);
    }

    private function setTraillerAgenciaDigito($valor){
           $this->trailler_agencia_digito = vsprintf("%1s",$valor);
    }
    
    private function setTraillerConta($valor){
           $this->trailler_conta = vsprintf("%12s",$valor);
    }

    private function setTraillerContaDigito($valor){
           $this->trailler_conta_digito = vsprintf("%1s",$valor);
    }

    private function setTraillerAgenciaContaDigito($valor){
           $this->trailler_agencia_conta_digito = vsprintf("%1s",$valor);
    }
    private function setTraillerCnab2($valor){
           $this->trailler_cnab_2 = vsprintf("%16s",$valor);
    }
    
    private function setTraillerBloqueado1($valor){
           $this->trailler_bloqueado1 = vsprintf("%18s",$valor);
    }

    private function setTraillerLimite($valor){
           $this->trailler_limite = vsprintf("%18s",$valor);
    }

    private function setTraillerBloqueado2($valor){
           $this->trailler_bloqueado2 = vsprintf("%18s",$valor);
    }

    private function setTraillerData($valor){
           $this->trailler_data = vsprintf("%8s",$valor);
    }

    private function setTraillerValor($valor){
           $this->trailler_valor = vsprintf("%18s",$valor);
    }
    
    private function setTraillerSituacao($valor){
           $this->trailler_situacao = vsprintf("%1s",$valor);
    }

    private function setTraillerStatus($valor){
           $this->trailler_status = vsprintf("%1s",$valor);
    }

    private function setTraillerTotRegistros($valor){
           $this->trailler_tot_registros = vsprintf("%6s",$valor);
    }
    
    private function setTraillerTotDebitos($valor){
           $this->trailler_tot_debitos = vsprintf("%18s",$valor);
    }
    
    private function setTraillerTotCreditos($valor){
           $this->trailler_tot_creditos = vsprintf("%18s",$valor);
    }

    private function setTraillerCnab3($valor){
           $this->trailler_cnab_3 = vsprintf("%28s",$valor);
    }
    
    private function setQueryTrailler($valor){
            $this->query_trailler = $valor;
    }
    
    private function setArquivoBanco($valor){
           $this->arquivo_banco = vsprintf("%3s",$valor);
    }

    private function setArquivoLote($valor){
           $this->arquivo_lote = vsprintf("%4s",$valor);
    }

    private function setArquivoTipo($valor){
           $this->arquivo_tipo = vsprintf("%1s",$valor);
    }
    
    private function setArquivoCnab01($valor){
           $this->arquivo_cnab_1 = vsprintf("%9s",$valor);
    }

    private function setArquivoTotLotes($valor){
           $this->arquivo_tot_lotes = vsprintf("%6s",$valor);
    }

    private function setArquivoTotRegistros($valor){
           $this->arquivo_tot_registros = vsprintf("%6s",$valor);
    }
    
    private function setArquivoTotConciliacao($valor){
           $this->arquivo_tot_conciliacao = vsprintf("%6s",$valor);
    }
    
    private function setArquivoTotCnab02($valor){
           $this->arquivo_cnab_2 = vsprintf("%205s",$valor);
    }
    
    private function setQueryArquivo($valor){
            $this->query_arquivo = $valor;
    }
    
    private function setRow($valor){
            $this->row = $valor;
    }
    
    /*
     * Define os valores default para toda classe
     */
    
    private function setDefault(){

        $this->count_controle = 0;
        
        
        $this->setDefaultHeader();
        $this->setDefaultLote();
        $this->setDefaultDetalhe();
        $this->setDefaultTrailler();
        $this->setDefaultArquivo();
        $this->setDefaultRemessa();
        
    }
        
    /*
    * Set os valores default para todas as vari�veis da classe referentes ao CNAB240
    */
    private function setDefaultHeader(){
    

        /*
         * Define valores de in�cio das vari�veis de cabe��rio             * 
         */

        $this->setHeaderControleBanco('');
        $this->setHeaderControleLote('');      
        $this->setHeaderControleTipo('');
        $this->setHeaderCnab1('');    
        $this->setHeaderEmpresaInscTipo('');
        $this->setHeaderEmpresaNumInsc('');
        $this->setHeaderEmpresaCodConvenio('');
        $this->setHeaderEmpresaAgencia('');
        $this->setHeaderEmpresaAgenciaDigito('');
        $this->setHeaderEmpresaConta('');
        $this->setHeaderEmpresaContaDigito('');
        $this->setHeaderEmpresaAgenciaContaDigito('');
        $this->setHeaderEmpresaNome('');
        $this->setHeaderNomeBanco('');
        $this->setHeaderCnab2('');
        $this->setHeaderArquivoCodigo('');
        $this->setHeaderArquivoData('');
        $this->setHeaderArquivoHora('');
        $this->setHeaderArquivoSequencial('');
        $this->setHeaderArquivoVersaoLayout(''); 
        $this->setHeaderArquivoDensidade(''); 
        $this->setHeaderReservadoBanco(''); 
        $this->setHeaderReservadoEmpresa(''); 
        $this->setHeaderCnab3(''); 

        $this->setQueryHeader("
                        SELECT  b.id_nacional AS controle_banco,
                                '0000' AS controle_lote,
                                '0' AS controle_tipo,
                                SPACE(9) AS cnab1,
                                '2' AS empresa_tipo_inscricao,
                                REPLACE(REPLACE(REPLACE(rh.cnpj,'.',''),'/',''),'-','') AS empresa_numero_inscricao,
                                b.cod_convenio AS empresa_cod_convenio,
                                b.nome AS empresa_nome_banco,
                                SPACE(10) AS cnab2,
                                LEFT(REPLACE(CONCAT(b.agencia,SPACE(4)),'-',''),4) AS empresa_agencia,
                                RIGHT(CONCAT(SPACE(4),b.agencia),1) AS empresa_agencia_digito,
                                LEFT(REPLACE(CONCAT(b.conta,SPACE(5)),'-',''),5) AS empresa_conta,
                                RIGHT(CONCAT(SPACE(4),b.conta),1) AS empresa_conta_digito,
                                '6' AS empresa_agencia_conta_digito,
                                rh.nome AS empresa_nome,
                                b.nome AS empresa_nome_banco,
                                '1' AS arquivo_codigo,
                                DATE_FORMAT(NOW(), '%d%m%Y') AS arquivo_data,
                                DATE_FORMAT(NOW(), '%h%i%s') AS arquivo_hora,
                                b.sequencia_cnab240 AS arquivo_sequencia,
                                '050' AS arquivo_layout_arquivo,
                                '00000' AS arquivo_densidade,
                                SPACE(20) AS reservado_banco,
                                SPACE(20) AS reservado_empresa,
                                SPACE(29) AS cnab3
                        FROM saida s INNER JOIN regioes r ON s.id_regiao=r.id_regiao 
                                     INNER JOIN projeto p ON s.id_projeto=p.id_projeto
                                     INNER JOIN rhempresa rh ON s.id_projeto=rh.id_projeto
                                     INNER JOIN bancos b ON s.id_banco=b.id_banco
                                     INNER JOIN tipos_pag_saida tps ON s.id_tipo_pag_saida=tps.id_tipo_pag
                                     INNER JOIN categoria_pag_saida cps ON s.id_categoria_pag_saida=cps.id_categoria_saida
                        WHERE s.id_saida IN (".$this->ids_saidas.") AND s.flag_remessa=0
                        GROUP BY b.id_nacional,b.nome,b.agencia,b.conta
                        ORDER BY s.data_vencimento

                ");

  
    }
    
    private function setHeaderValue(){


        $this->setHeaderControleBanco($this->row['controle_banco']);
        $this->setHeaderControleLote($this->row['controle_lote']);                 // default 0000
        $this->setHeaderControleTipo($this->row['controle_tipo']);                 // Tipo de registro (0 - Header/2 - Registros Iniciais do Lote/3 - Detalhe/5 - Trailer do lote/9 - Trailer de arquivo
        $this->setHeaderCnab1($this->row['cnab1']);
        $this->setHeaderEmpresaInscTipo($this->row['empresa_tipo_inscricao']);    // Tipo de inscri��o fiscal na empresa (0 - Isento/1 - CPF/2 - CNPJ/3 - PIS/9 - Outros) Adicionar campo na tabela banco
        $this->setHeaderEmpresaNumInsc($this->row['empresa_numero_inscricao']);   // CNPJ ou CPF registrado na institui��o banc�ria -> rhempresa
        $this->setHeaderEmpresaCodConvenio($this->row['empresa_cod_convenio']);   // C�digo do conv�nio com o banco
        $this->setHeaderEmpresaAgencia($this->row['empresa_agencia']);
        $this->setHeaderEmpresaAgenciaDigito($this->row['empresa_agencia_digito']);
        $this->setHeaderEmpresaConta($this->row['empresa_conta']);
        $this->setHeaderEmpresaContaDigito($this->row['empresa_conta_digito']);
        $this->setHeaderEmpresaAgenciaContaDigito($this->row['empresa_agencia_conta_digito']);
        $this->setHeaderEmpresaNome($this->row['empresa_nome']);
        $this->setHeaderNomeBanco($this->row['empresa_nome_banco']);
        $this->setHeaderCnab2($this->row['cnab2']);
        $this->setHeaderArquivoCodigo($this->row['arquivo_codigo']);
        $this->setHeaderArquivoData($this->row['arquivo_data']);
        $this->setHeaderArquivoHora($this->row['arquivo_hora']);
        $this->setHeaderArquivoSequencial($this->row['arquivo_sequencia']);
        $this->setHeaderArquivoVersaoLayout($this->row['arquivo_layout_arquivo']);
        $this->setHeaderArquivoDensidade($this->row['arquivo_densidade']);
        $this->setHeaderReservadoBanco($this->row['reservado_banco']);
        $this->setHeaderReservadoEmpresa($this->row['reservado_empresa']);
        $this->setHeaderCnab3($this->row['cnab3']);
        
    }
    
    private function setDefaultLote(){
            
            /*
             * Define valores de in�cio das vari�veis de lote             * 
             */
            
            $this->setLoteControleBanco('');
            $this->setLoteControleLote('') ;
            $this->setLoteControleTipo('');
            $this->setLoteServicoOperacao('');
            $this->setLoteServicoServico('');
            $this->setLoteServicoForma('');
            $this->setLoteServicoLayout('');
            $this->setLoteCnab1('');
            $this->setLoteEmpresaTipoInscricao('');
            $this->setLoteEmpresaNumInscricao('');
            $this->setLoteEmpresaCodConvenioBanco('');
            $this->setLoteEmpresaAgencia('');
            $this->setLoteEmpresaAgenciaDigito('');
            $this->setLoteEmpresaConta('');
            $this->setLoteEmpresaContaDigito('');
            $this->setLoteEmpresaAgenciaContaDigito('');
            $this->setLoteEmpresaNome('');
            $this->setLoteInformacao1('');
            $this->setLoteEnderecoLogradouro('');
            $this->setLoteEnderecoNumero('');
            $this->setLoteEnderecoComplemento('');
            $this->setLoteEnderecoCidade('');
            $this->setLoteEnderecoCep('');
            $this->setLoteEnderecoEstado('');
            $this->setLoteCnab2E('');
            $this->setLoteCnab2ACB('');
            $this->setLoteOcorrencias('');
            $this->setLoteData('');
            $this->setLoteSaldo('');
            $this->setLoteSituacao('');
            $this->setLoteStatus('');
            $this->setLoteTipoMoeda('');
            $this->setLoteSeqExtrato('');
            $this->setLoteCnab3E('');
            
            $this->setQueryLote("
                        SELECT  b.id_nacional AS controle_banco,
                                @seq_lote:=@seq_lote+1 AS controle_lote,
                                '1' AS controle_tipo,
                                
                                tps.segmento AS servico_operacao,
                                '05' AS servico_servico,
                                tps.cod_forma_pag AS servico_forma,
                                '050' AS servico_layout,
                                
                                SPACE(1) AS cnab1,
                                
                                '2' AS empresa_tipo_inscricao,
                                REPLACE(REPLACE(REPLACE(rh.cnpj,'.',''),'/',''),'-','') AS empresa_numero_inscricao,
                                b.cod_convenio AS empresa_cod_convenio,
                                LEFT(REPLACE(CONCAT(b.agencia,SPACE(4)),'-',''),4) AS empresa_agencia,
                                RIGHT(CONCAT(SPACE(4),b.agencia),1) AS empresa_agencia_digito,
                                LEFT(REPLACE(CONCAT(b.conta,SPACE(5)),'-',''),5) AS empresa_conta,
                                RIGHT(CONCAT(SPACE(4),b.conta),1) AS empresa_conta_digito,
                                '6' AS empresa_agencia_conta_digito,
                                rh.nome AS empresa_nome,
                                
                                SPACE(40) AS informacao1,

                                rh.logradouro AS endereco_logradouro,
                                rh.numero AS endereco_numero,
                                rh.complemento AS endereco_complemento,
                                rh.municipio AS endereco_cidade,
                                LEFT(rh.cep,5) As endereco_cep,
                                RIGHT(rh.cep,3) As endereco_comp_cep,
                                rh.uf AS endereco_estado,
                                SPACE(40) AS cnab2,
                                SPACE(10) AS ocorrencias,
                                DATE_FORMAT(NOW(), '%d%m%Y') AS saldo_data,
                                REPLACE(REPLACE(REPLACE(FORMAT(b.saldo,2),',',''),'.',''),'-','')  AS saldo_valor,
                                (CASE WHEN b.saldo > 0 THEN 'C' ELSE 'D' END) AS saldo_situacao,
                                'I' AS saldo_status,
                                'BRL' AS saldo_moeda,
                                SPACE(5) AS saldo_sequencia,
                                SPACE(9) AS cnab3
                            FROM saida s INNER JOIN regioes r ON s.id_regiao=r.id_regiao 
                                         INNER JOIN projeto p ON s.id_projeto=p.id_projeto
                                         INNER JOIN rhempresa rh ON s.id_projeto=rh.id_projeto
                                         INNER JOIN bancos b ON s.id_banco=b.id_banco
                                         INNER JOIN tipos_pag_saida tps ON s.id_tipo_pag_saida=tps.id_tipo_pag
                                         INNER JOIN categoria_pag_saida cps ON s.id_categoria_pag_saida=cps.id_categoria_saida,
                                         (SELECT @seq_lote:=0) AS seq_lote
                            WHERE s.id_saida IN  (".$this->ids_saidas.") AND s.flag_remessa=0
                            GROUP BY tps.segmento
                            ORDER BY tps.segmento
                    ");
            

    }
    
    private function setLoteValue(){

        $this->setLoteControleBanco($this->row['controle_banco']);
        $this->setLoteControleLote($this->row['controle_lote']);
        $this->setLoteControleTipo($this->row['controle_tipo']);
        $this->setLoteCnab1($this->row['cnab1']);
        $this->setLoteServicoOperacao($this->row['servico_operacao']);
        $this->setLoteServicoServico($this->row['servico_servico']);
        $this->setLoteServicoForma($this->row['servico_forma']);
        $this->setLoteServicoLayout($this->row['servico_layout']);
        $this->setLoteEmpresaTipoInscricao($this->row['empresa_tipo_inscricao']);
        $this->setLoteEmpresaNumInscricao($this->row['empresa_numero_inscricao']);
        $this->setLoteEmpresaCodConvenioBanco($this->row['empresa_cod_convenio']);
        $this->setLoteEmpresaAgencia($this->row['empresa_agencia']);
        $this->setLoteEmpresaAgenciaDigito($this->row['empresa_agencia_digito']);
        $this->setLoteEmpresaConta($this->row['empresa_conta']);
        $this->setLoteEmpresaContaDigito($this->row['empresa_conta_digito']);
        $this->setLoteEmpresaAgenciaContaDigito($this->row['empresa_agencia_conta_digito']);
        $this->setLoteEmpresaNome($this->row['empresa_nome']);

        $this->setLoteInformacao1($this->row['informacao1']);

        $this->setLoteEnderecoLogradouro($this->row['endereco_logradouro']);
        $this->setLoteEnderecoNumero($this->row['endereco_numero']);
        $this->setLoteEnderecoComplemento($this->row['endereco_complemento']);
        $this->setLoteEnderecoCidade($this->row['endereco_cidade']);
        $this->setLoteEnderecoCEP($this->row['endereco_cep']);
        $this->setLoteEnderecoCompCep($this->row['endereco_comp_cep']);
        $this->setLoteEnderecoEstado($this->row['endereco_estado']);

        $this->setLoteOcorrencias($this->row['ocorrencias']);

        $this->setLoteCnab2E($this->row['cnab2']);
        $this->setLoteData($this->row['saldo_data']);
        $this->setLoteSaldo($this->row['saldo_valor']);
        $this->setLoteSituacao($this->row['saldo_situacao']);
        $this->setLoteStatus($this->row['saldo_status']);
        $this->setLoteTipoMoeda($this->row['saldo_moeda']);
        $this->setLoteSeqExtrato($this->row['saldo_sequencia']);
        $this->setLoteCnab3E($this->row['cnab3']);
        
    }
    
    private function setDefaultDetalhe(){
            
    
            /*
             * Define valores de in�cio do detalhe do arquivo             * 
             */
            
            $this->setDetalheControleBanco('');
            $this->setDetalheControleLote('');
            $this->setDetalheControleTipo('');
            
            $this->setDetalheServicoNumRegistro('');
            $this->setDetalheServicoCodSegmento('');

            $this->setDetalheCnab1E('');
            $this->setDetalheCnab1A('');
            
            $this->setDetalheFavCamara('');
            $this->setDetalheFavBanco('');
            $this->setDetalheFavAgencia('');
            $this->setDetalheFavAgenciaDigito('');
            $this->setDetalheFavConta('');
            $this->setDetalheFavContaDigito('');
            $this->setDetalheFavAgenciaContaDigito('');
            $this->setDetalheFavNome('');
            
            $this->setDetalheCredSeuNumero('');
            $this->setDetalheCredDataPagamento('');
            $this->setDetalheCredMoedaTipo('');
            $this->setDetalheCredMoedaQuantidade('');
            $this->setDetalheCredValorPagamnto('');
            $this->setDetalheCredNossoNumero('');
            $this->setDetalheCredDataReal('');
            $this->setDetalheCredValorReal('');

            $this->setDetalheInformacao2('');
            
            $this->setDetalheCodFinalidadeDoc('');
            $this->setDetalheCodFinalidadeTed('');
            
            $this->setDetalheAviso('');
            $this->setDetalheOcorrencias('');
            
            $this->setDetalheControleTipoInscricao('');
            $this->setDetalheNumeroInscricao('');
            $this->setDetalheCodConvenio('');
            $this->setDetalheAgencia('');
            $this->setDetalheAgenciaDigito('');
            $this->setDetalheConta('');
            $this->setDetalheContaDigito('');
            $this->setDetalheAgenciaContaDigito('');
            $this->setDetalheEmpresaNome('');
            $this->setDetalheCnab02('');
            $this->setDetalheNaturezaLancamento('');
            $this->setDetalheControleTipoComplemento('');
            $this->setDetalheComplemento('');
            $this->setDetalheCpmf('');
            $this->setDetalheData('');
            
            $this->setDetalheLancamentoData('');
            $this->setDetalheLancamentoValor('');
            $this->setDetalheLancamentoTipo('');
            $this->setDetalheLancamentoCategoria('');
            $this->setDetalheLancamentoCodHistorico('');
            $this->setDetalheLancamentoHistorico('');
            $this->setDetalheLancamentoDocumento('');
            
            
            $this->setQueryDetalhe(
                            "
                            SELECT 
                                    b.id_nacional AS controle_banco,
                                    '".$this->controle_lote."' AS controle_lote,
                                    '3' AS controle_tipo,
                                    
                                    @servico_sequencial:=@servico_sequencial+1 AS servico_sequencial,
                                    tps.segmento AS servico_cod_segmento,
                                    
                                    'CCC' AS favorecido_camara,
                                    'BBB' AS favorecido_banco,
                                    'AAAAA' AS favorecido_agencia,
                                    'D' AS favorecido_agencia_digito,
                                    'CCCCCCCCCCCC' AS favorecido_conta,
                                    'D' AS favorecido_conta_digito,
                                    'D' AS favorecido_agencia_conta_digito,
                                    'NNNNNNNNNNNNNNNNNNNNNNNNNNNNNN' AS favorecido_nome,
                                    
                                    'SNSNSNSNSNSNSNSNSNSN' AS credito_seu_numero,
                                    'DPDPDPDP' AS credito_data_pagamento,
                                    'TTT' AS credito_moeda_tipo,
                                    'MOEDAMOEDAMOEDA' AS credito_moeda_quantidade,
                                    'MOEDAMOEDAMOEDA' AS credito_valor_pagamento,
                                    'NOSSONUMERONOSSONUME' AS credito_nosso_numero,
                                    SPACE(8) AS credito_data_real,
                                    SPACE(15) AS credito_valor_real,
                                    
                                    SPACE(40) AS informacao_2,
                                    
                                    SPACE(2) AS codigo_finalidade_doc,
                                    SPACE(5) AS codigo_finalidade_ted,

                                    SPACE(5) AS cnab1A,
                                    SPACE(3) AS cnab1E,
                                    
                                    SPACE(1) AS aviso,
                                    SPACE(10) AS ocorrencias,
                                    
                                    '2' AS empresa_tipo_inscricao,
                                    REPLACE(REPLACE(REPLACE(rh.cnpj,'.',''),'/',''),'-','') AS empresa_numero_inscricao,
                                    b.cod_convenio AS empresa_cod_convenio,
                                    LEFT(REPLACE(CONCAT(b.agencia,SPACE(4)),'-',''),4) AS empresa_agencia,
                                    RIGHT(CONCAT(SPACE(4),b.agencia),1) AS empresa_agencia_digito,
                                    LEFT(REPLACE(CONCAT(b.conta,SPACE(5)),'-',''),5) AS empresa_conta,
                                    RIGHT(CONCAT(SPACE(4),b.conta),1) AS empresa_conta_digito,
                                    '6' AS empresa_agencia_conta_digito,
                                    LEFT(rh.nome,30) AS empresa_nome,
                                    SPACE(6) AS cnab2,
                                    'DPV' AS natureza_lancamento,
                                    '01' AS tipo_complemento,
                                    CONCAT(b.id_nacional,REPLACE(b.agencia,'-',''),REPLACE(b.conta,'-','')) AS complemento,
                                    'S' AS cpmf,
                                    DATE_FORMAT(s.data_proc,'%d%m%Y') AS data_contabil,
                                    DATE_FORMAT(s.data_vencimento, '%d%m%Y') AS lancamento_data,
                                    REPLACE(REPLACE(FORMAT(s.valor,2),',',''),'.','')  AS lancamento_valor,
                                    'D' AS lancamento_tipo,
                                    cps.cod_categoria_pag AS lancamento_categoria,
                                    SPACE(4) AS lancamento_codigo,
                                    LEFT(s.especifica,25) AS lancamento_historico,
                                    s.id_saida AS lancamento_documento
                            FROM saida s INNER JOIN regioes r ON s.id_regiao=r.id_regiao 
                                         INNER JOIN projeto p ON s.id_projeto=p.id_projeto
                                         INNER JOIN rhempresa rh ON s.id_projeto=rh.id_projeto
                                         INNER JOIN bancos b ON s.id_banco=b.id_banco
                                         INNER JOIN tipos_pag_saida tps ON s.id_tipo_pag_saida=tps.id_tipo_pag
                                         INNER JOIN categoria_pag_saida cps ON s.id_categoria_pag_saida=cps.id_categoria_saida,
                                         (SELECT @controle_lote:=0) AS controle_lote,
                                         (SELECT @servico_sequencial:=0) AS servico_sequencial
                            WHERE s.id_saida IN  (".$this->ids_saidas.")  AND tps.segmento='".$this->lote_servico_operacao."' AND s.flag_remessa=0
                            ORDER BY @controle_lote,@servico_sequencial,
                                     s.id_tipo_pag_saida,
                                     s.data_proc
                            ");
            
    }
    
    private function setDetalheValue(){

        $this->setDetalheControleBanco($this->row['controle_banco']);
        $this->setDetalheControleLote($this->row['controle_lote']);
        $this->setDetalheControleTipo($this->row['controle_tipo']);

        $this->setDetalheCnab1A($this->row['cnab1A']);
        $this->setDetalheCnab1E($this->row['cnab1E']);

        $this->setDetalheServicoNumRegistro($this->row['servico_sequencial']);
        $this->setDetalheServicoCodSegmento($this->row['servico_cod_segmento']);
        
        $this->setDetalheFavCamara($this->row['favorecido_camara']);
        $this->setDetalheFavBanco($this->row['favorecido_banco']);
        $this->setDetalheFavAgencia($this->row['favorecido_agencia']);
        $this->setDetalheFavAgenciaDigito($this->row['favorecido_agencia_digito']);
        $this->setDetalheFavConta($this->row['favorecido_conta']);
        $this->setDetalheFavContaDigito($this->row['favorecido_conta_digito']);
        $this->setDetalheFavAgenciaContaDigito($this->row['favorecido_agencia_conta_digito']);
        $this->setDetalheFavNome($this->row['favorecido_nome']);
        
        $this->setDetalheCredSeuNumero($this->row['credito_seu_numero']);
        $this->setDetalheCredDataPagamento($this->row['credito_data_pagamento']);
        $this->setDetalheCredMoedaTipo($this->row['credito_moeda_tipo']);
        $this->setDetalheCredMoedaQuantidade($this->row['credito_moeda_quantidade']);
        $this->setDetalheCredValorPagamnto($this->row['credito_valor_pagamento']);
        $this->setDetalheCredNossoNumero($this->row['credito_nosso_numero']);
        $this->setDetalheCredDataReal($this->row['credito_data_real']);
        $this->setDetalheCredValorReal($this->row['credito_valor_real']);
        
        $this->setDetalheInformacao2($this->row['informacao_2']);

        $this->setDetalheCodFinalidadeDoc($this->row['codigo_finalidade_doc']);
        $this->setDetalheCodFinalidadeTed($this->row['codigo_finalidade_ted']);

        $this->setDetalheAviso($this->row['aviso']);
        $this->setDetalheOcorrencias($this->row['ocorrencias']);

        $this->setDetalheLancamentoData($this->row['lancamento_data']);
        $this->setDetalheLancamentoValor($this->row['lancamento_valor']);
        $this->setDetalheLancamentoTipo($this->row['lancamento_tipo']);
        $this->setDetalheLancamentoCategoria($this->row['lancamento_categoria']);
        $this->setDetalheLancamentoCodHistorico($this->row['lancamento_codigo']);
        $this->setDetalheLancamentoHistorico($this->row['lancamento_historico']);
        $this->setDetalheLancamentoDocumento($this->row['lancamento_documento']);
        
    }
    
    private function setDefaultTrailler(){
            

            /*
             * Define valores do trailler do arquivo             * 
             */
            
            $this->setTraillerBanco('');
            $this->setTraillerLote('');
            $this->setTraillerTipo('');
            $this->setTraillerCnab01('');
            $this->setTraillerTipoInscricao('');
            $this->setTraillerNumeroInscricao('');
            $this->setTraillerCodConvenio('');
            $this->setTraillerAgencia('');
            $this->setTraillerAgenciaDigito('');
            $this->setTraillerConta('');
            $this->setTraillerContaDigito('');
            $this->setTraillerCnab2('');
            $this->setTraillerBloqueado1('');
            $this->setTraillerLimite('');
            $this->setTraillerBloqueado2('');
            $this->setTraillerData('');
            $this->setTraillerValor('');
            $this->setTraillerSituacao('');
            $this->setTraillerStatus('');
            $this->setTraillerTotRegistros('');
            $this->setTraillerTotDebitos('');
            $this->setTraillerTotCreditos('');
            $this->setTraillerCnab3('');
            
            $this->setQueryTrailler("
                            SELECT b.id_nacional AS controle_banco, 
                                '".$this->controle_lote."' AS controle_lote,
                                '5' AS controle_tipo, SPACE(9) AS cnab1,
                                '2' AS empresa_tipo_inscricao,
                                REPLACE(REPLACE(REPLACE(rh.cnpj,'.',''),'/',''),'-','') AS empresa_numero_inscricao,
                                b.cod_convenio AS empresa_cod_convenio,
                                LEFT(REPLACE(CONCAT(b.agencia, SPACE(4)),'-',''),4) AS empresa_agencia,
                                RIGHT(CONCAT(SPACE(4),b.agencia),1) AS empresa_agencia_digito,
                                LEFT(REPLACE(CONCAT(b.conta, SPACE(5)),'-',''),5) AS empresa_conta,
                                RIGHT(CONCAT(SPACE(4),b.conta),1) AS empresa_conta_digito,
                                '6' AS empresa_agencia_conta_digito, SPACE(16) AS cnab2,
                                '' AS valores_bloqueado1,
                                '' AS valores_limite,
                                '' AS valores_bloqueado2, DATE_FORMAT(NOW(), '%d%m%Y') AS saldo_data,
                                REPLACE(REPLACE(REPLACE(FORMAT(b.saldo- SUM(s.valor),2),',',''),'.',''),'-','') AS saldo_valor,
                                (CASE WHEN b.saldo- SUM(s.valor) > 0 THEN 'C' ELSE 'D' END) AS saldo_situacao,
                                'I' AS saldo_status, 
                                COUNT(s.id_saida) AS totais_lote,
                                REPLACE(REPLACE(REPLACE(FORMAT(SUM(s.valor),2),',',''),'.',''),'-','') AS totais_debitos, 
                                SPACE(18) AS totais_creditos, 
                                SPACE(28) AS cnab3
                            FROM saida s INNER JOIN regioes r ON s.id_regiao=r.id_regiao 
				 INNER JOIN projeto p ON s.id_projeto=p.id_projeto 
				 INNER JOIN rhempresa rh ON s.id_projeto=rh.id_projeto 
				 INNER JOIN bancos b ON s.id_banco=b.id_banco 
				 INNER JOIN tipos_pag_saida tps ON s.id_tipo_pag_saida=tps.id_tipo_pag
                                 INNER JOIN categoria_pag_saida cps ON s.id_categoria_pag_saida=cps.id_categoria_saida
                            WHERE s.id_saida IN  (".$this->ids_saidas.") AND s.flag_remessa=0
                            GROUP BY s.id_tipo_pag_saida
                            ORDER BY s.id_tipo_pag_saida
                    ");
            
            
    }
    
    private function setTraillerValue(){

        $this->setTraillerBanco($this->row['controle_banco']);
        $this->setTraillerLote($this->row['controle_lote']);
        $this->setTraillerTipo($this->row['controle_tipo']);
        $this->setTraillerCnab01($this->row['cnab1']);
        $this->setTraillerTipoInscricao($this->row['empresa_tipo_inscricao']);
        $this->setTraillerCodConvenio($this->row['empresa_cod_convenio']);
        $this->setTraillerNumeroInscricao($this->row['empresa_numero_inscricao']);
        $this->setTraillerCodConvenio($this->row['empresa_cod_convenio']);
        $this->setTraillerAgencia($this->row['empresa_agencia']);
        $this->setTraillerAgenciaDigito($this->row['empresa_agencia_digito']);                    
        $this->setTraillerConta($this->row['empresa_conta']);
        $this->setTraillerContaDigito($this->row['empresa_conta_digito']);
        $this->setTraillerAgenciaContaDigito($this->row['empresa_agencia_conta_digito']);
        $this->setTraillerCnab2($this->row['cnab2']);
        $this->setTraillerBloqueado1($this->row['valores_bloqueado1']);
        $this->setTraillerLimite($this->row['valores_limite']);
        $this->setTraillerBloqueado2($this->row['valores_bloqueado2']); 
        $this->setTraillerData($this->row['saldo_data']);
        $this->setTraillerValor($this->row['saldo_valor']);
        $this->setTraillerSituacao($this->row['saldo_situacao']);
        $this->setTraillerStatus($this->row['saldo_status']);
        $this->setTraillerTotRegistros($this->row['totais_lote']);
        $this->setTraillerTotDebitos($this->row['totais_debitos']);
        $this->setTraillerTotCreditos($this->row['totais_creditos']);
        $this->setTraillerCnab3($this->row['cnab3']);
        
    }

    private function setDefaultArquivo(){
            
            
            /*
             * Define valores finais do arquivo             * 
             */

            $this->setArquivoBanco('');
            $this->setArquivoLote('');
            $this->setArquivoTipo('');
            $this->setArquivoCnab01('');
            $this->setArquivoTotLotes('');
            $this->setArquivoTotRegistros('');
            $this->setArquivoTotConciliacao('');
            $this->setArquivoTotCnab02('');
            
            $this->setQueryArquivo("
                
                            SELECT b.id_nacional AS controle_banco, 
                                   '9999' AS controle_lote,
                                   '9' AS controle_tipo, 
                                   SPACE(9) AS cnab1,
                                   (SELECT COUNT(t.id_tipo_pag_saida) FROM (SELECT id_tipo_pag_saida FROM saida WHERE id_saida IN (".$this->ids_saidas.") GROUP BY id_tipo_pag_saida) t) AS totais_lotes,
                                   COUNT(s.id_saida) AS totais_registros,
                                   SPACE(205) AS cnab2
                            FROM saida s
                            INNER JOIN regioes r ON s.id_regiao=r.id_regiao
                            INNER JOIN projeto p ON s.id_projeto=p.id_projeto
                            INNER JOIN rhempresa rh ON s.id_projeto=rh.id_projeto
                            INNER JOIN bancos b ON s.id_banco=b.id_banco
                            INNER JOIN tipos_pag_saida tps ON s.id_tipo_pag_saida=tps.id_tipo_pag
                            INNER JOIN categoria_pag_saida cps ON s.id_categoria_pag_saida=cps.id_categoria_saida
                            WHERE s.id_saida IN (".$this->ids_saidas.") AND s.flag_remessa=0
                            GROUP BY b.id_nacional
                            ORDER BY b.id_nacional

                    ");
            
            
    }
    
    private function setArquivoValue(){
        
        $this->setArquivoBanco($this->row['controle_banco']);
        $this->setArquivoLote($this->row['controle_lote']);
        $this->setArquivoTipo($this->row['controle_tipo']);
        $this->setArquivoCnab01($this->row['cnab1']);
        $this->setArquivoTotLotes($this->row['totais_lotes']);
        $this->setArquivoTotRegistros($this->row['totais_registros']);
        $this->setArquivoTotCnab02($this->row['cnab2']);

        
    }
    
    private function setDefaultRemessa(){
            
            $this->setRemessaProcessada("
                            UPDATE saida SET flag_remessa=0 
                            WHERE id_saida IN (".$this->ids_saidas.")
                    ");
    }
    
    private function setRemessaProcessada($valor) {
        
            $this->query_remessa = $valor;
            
    }
    
    private function setRetornoProcessada($valor) {
        
            $this->query_retorno = $valor;
            
    }
    
    public function setUser($valor) {
        
        $this->user = vsprintf("%30s",$valor);
            
    }
    
    function setRowString($valor){
        
        $this->rowString = $valor;  
        
    }

    
    function getControleLote() {
        
            return $this->controle_lote;
            
    }
    
    function getRowString(){
        
        return $this->rowString;  
        
    }
    
    
    /*
     * Retorna o cabe��rio do arquivo de retorno concatenado
     */
    private function getHeader(){
    
        $this->setRowString($this->header_controle_banco 
                            .$this->header_controle_lote
                            .$this->header_controle_tipo
                            .$this->header_cnab_1
                            .$this->header_empresa_insc_tipo
                            .$this->header_empresa_num_insc
                            .$this->header_empresa_cod_convenio
                            .$this->header_empresa_agencia   
                            .$this->header_empresa_agencia_digito
                            .$this->header_empresa_conta
                            .$this->header_empresa_conta_digito
                            .$this->header_empresa_agencia_conta_digito
                            .$this->header_empresa_nome
                            .$this->header_banco_nome
                            .$this->header_cnab_2
                            .$this->header_arquivo_codigo
                            .$this->header_arquivo_data
                            .$this->header_arquivo_hora
                            .$this->header_arquivo_sequencial
                            .$this->header_arquivo_versao_laytou
                            .$this->header_arquivo_densidade
                            .$this->header_reservado_banco
                            .$this->header_reservado_empresa
                            .$this->header_cnab_3);

        return $this->getRowString();
        
    }
    
     /*
     * Retorna o lote do arquivo de retorno concatenado
     */

    private function getLote(){

        switch ($this->lote_servico_operacao) {
            case 'A': // Pagamento - Pagamentos atrav�z de cr�dito em conta corrente OP,DOC e TED
            case 'B': // Pagamento - Pagamentos atrav�z de cr�dito em conta corrente OP,DOC e TED
            case 'C': // Pagamento - Pagamentos atrav�z de cr�dito em conta corrente OP,DOC e TED
                        $this->setRowString($this->lote_controle_banco
                                    .$this->lote_controle_lote
                                    .$this->lote_controle_tipo
                                    .$this->lote_servico_operacao
                                    .$this->lote_servico_servico
                                    .$this->lote_servico_forma
                                    .$this->lote_servico_layout
                                    .$this->lote_cnab_1
                                    .$this->lote_empresa_tipo_inscricao
                                    .$this->lote_empresa_num_inscricao
                                    .$this->lote_empresa_cod_convenio_banco
                                    .$this->lote_empresa_agencia
                                    .$this->lote_empresa_agencia_digito
                                    .$this->lote_empresa_conta
                                    .$this->lote_empresa_conta_digito
                                    .$this->lote_empresa_agencia_conta_digito
                                    .$this->lote_empresa_nome
                                    .$this->lote_informacao1
                                    .$this->lote_endereco_logradouro
                                    .$this->lote_endereco_numero
                                    .$this->lote_endereco_complemento
                                    .$this->lote_endereco_cidade
                                    .$this->lote_endereco_cep
                                    .$this->lote_endereco_comp_cep
                                    .$this->lote_endereco_estado
                                    .$this->lote_cnab_2ABC
                                    .$this->lote_ocorrencias);
                break;
            case 'J': // Pagamento - Pagamentos atrav�z de t�tulo de cobran�a
                break;
            case 'O': // Pagamento - Pagamentos de tributos com c�digo de barras
                break;
            case 'W': // Pagamento - Pagamentos de tributos com/sem c�digo de barras
                break;
            case 'N': // Pagamento - Pagamentos de tributos sem c�digo de barras
                break;
            case 'Z': // Pagamento - Pagamentos de tributos com/sem c�digo de barras
                break;
            case 'E': 
                break;
            
        }
        
        return $this->getRowString();
                
        
    }
            
    /*
     * Retorna o detalhe do arquivo de retorno concatenado
     */
    
    private function getDetalhe(){
        
        switch ($this->lote_servico_operacao) {
            case 'A': // Pagamento - Pagamentos atrav�z de cr�dito em conta corrente OP,DOC e TED
                
                   $this->setRowString($this->detalhe_controle_banco
                                    .$this->detalhe_controle_lote
                                    .$this->detalhe_controle_registro
                           
                                    .$this->detalhe_servico_num_registro
                                    .$this->detalhe_servico_cod_segmento

                                    .$this->detalhe_favorecido_camara
                                    .$this->detalhe_favorecido_banco
                                    .$this->detalhe_favorecido_agencia
                                    .$this->detalhe_favorecido_agencia_digito
                                    .$this->detalhe_favorecido_conta
                                    .$this->detalhe_favorecido_conta_digito  
                                    .$this->detalhe_favorecido_agencia_conta_digito
                                    .$this->detalhe_favorecido_nome

                                    .$this->detalhe_credito_seu_numero
                                    .$this->detalhe_credito_data_pagamento
                                    .$this->detalhe_credito_moeda_tipo
                                    .$this->detalhe_credito_moeda_quantidade
                                    .$this->detalhe_credito_valor_pagamento
                                    .$this->detalhe_credito_nosso_numero
                                    .$this->detalhe_credito_data_real
                                    .$this->detalhe_credito_valor_real
                           
                                    .$this->detalhe_informacao_2
                           
                                    .$this->detalhe_codigo_finalidade_doc
                                    .$this->detalhe_codigo_finalidade_ted
                           
                                    .$this->detalhe_cnab_1A
                                    .$this->detalhe_aviso
                                    .$this->detalhe_ocorrencias);
                break;    
            case 'B': // Pagamento - Pagamentos atrav�z de cr�dito em conta corrente OP,DOC e TED
                break;
            case 'C': // Pagamento - Pagamentos atrav�z de cr�dito em conta corrente OP,DOC e TED
                break;
            case 'J': // Pagamento - Pagamentos atrav�z de t�tulo de cobran�a
                break;
            case 'O': // Pagamento - Pagamentos de tributos com c�digo de barras
                break;
            case 'W': // Pagamento - Pagamentos de tributos com/sem c�digo de barras
                break;
            case 'N': // Pagamento - Pagamentos de tributos sem c�digo de barras
                break;
            case 'Z': // Pagamento - Pagamentos de tributos com/sem c�digo de barras
                break;
            case 'E': 
                    $this->setRowString($this->detalhe_controle_banco
                                    .$this->detalhe_controle_lote
                                    .$this->detalhe_controle_registro
                            
                                    .$this->detalhe_servico_num_registro
                                    .$this->detalhe_servico_cod_segmento
                            
                                    .$this->detalhe_cnab_1E
                                    .$this->detalhe_tipo_inscricao
                                    .$this->detalhe_numero_inscricao
                                    .$this->detalhe_cod_convenio
                                    .$this->detalhe_agencia
                                    .$this->detalhe_agencia_digito
                                    .$this->detalhe_conta
                                    .$this->detalhe_conta_digito
                                    .$this->detalhe_agencia_conta_digito
                                    .$this->detalhe_empresa_nome
                                    .$this->detalhe_cnab_02
                                    .$this->detalhe_natureza_lancamento
                                    .$this->detalhe_tipo_complemento
                                    .$this->detalhe_complemento
                                    .$this->detalhe_cpmf
                                    .$this->detalhe_data
                            
                                    .$this->detalhe_lancamento_data
                                    .$this->detalhe_lancamento_valor
                                    .$this->detalhe_lancamento_tipo
                                    .$this->detalhe_lancamento_categoria
                                    .$this->detalhe_lancamento_cod_historico
                                    .$this->detalhe_lancamento_descricao
                                    .$this->detalhe_lancamento_documento);
                    break;
            
        }
        
        return $this->getRowString();
    
        
    }
    
    /*
     * Obtem o trailler do arquivo de retorno concatenado
     */
    
    private function getTrailler() {
       
        $this->setRowString($this->trailler_banco
                            .$this->trailler_lote
                            .$this->trailler_tipo
                            .$this->trailler_cnab_1
                            .$this->trailler_tipo_inscricao
                            .$this->trailler_numero_inscricao
                            .$this->trailler_cod_convenio
                            .$this->trailler_agencia
                            .$this->trailler_agencia_digito
                            .$this->trailler_conta
                            .$this->trailler_conta_digito
                            .$this->trailler_agencia_conta_digito
                            .$this->trailler_cnab_2
                            .$this->trailler_bloqueado1
                            .$this->trailler_limite
                            .$this->trailler_bloqueado2
                            .$this->trailler_data
                            .$this->trailler_valor
                            .$this->trailler_situacao
                            .$this->trailler_status
                            .$this->trailler_tot_registros
                            .$this->trailler_tot_debitos
                            .$this->trailler_tot_creditos
                            .$this->trailler_cnab_3);
        
        return $this->getRowString();
        
    }
    
    /*
     * Obtem o arquivo do arquivo de retorno concatenado
     */

    private function getArquivo() {

        $this->setRowString($this->arquivo_banco
                .$this->arquivo_lote
                .$this->arquivo_tipo
                .$this->arquivo_cnab_1
                .$this->arquivo_tot_lotes
                .$this->arquivo_tot_registros
                .$this->arquivo_tot_conciliacao
                .$this->arquivo_cnab_2);
        
        return $this->getRowString();
        

    }
    
    
    /*
     * Executa consulta para exporta��o de dados no formato cnab240
     */
    public function RunRemessa(){
        
        //mysql_select_db("$this->dbname");
        //mysql_query("SET NAMES 'utf8'");
        //mysql_query('SET character_set_connection=utf8');
        //mysql_query('SET character_set_client=utf8');	

        // echo $this->runSqlHeader() ? 'header-true' : 'header-false';
        // echo $this->rsHeader = mysql_query($this->queryHeader) ? 'header-true' : 'header-false';
        // echo $this->queryHeader;
        // echo 'Total de linhas: '.mysql_num_rows($this->rsHeader);


        $this->createFileName();
        $this->SetDefault();
        
        if(!$this->check()) return 0;
        
        
        if($this->runSqlHeader()){
            
            /*
             * Defini��o de Header de sa�da de arquivo retorno
             */
            
            $this->setHeaderValue();
            $this->row=$this->getRowHeader();
            $this->buffer=$this->GetHeader()."\n";

            if(!$this->checkRow()){
               return 0; 
            }
            

            
            if($this->runSqlLote()){

                
                //echo $this->query_lote;
                //echo $this->runSqlLote() ? 'true' : 'false';  
                //echo $this->getLoteControle();
                //echo $this->query_lote;
                
                    
                while ($this->row=$this->getRowLote()){
                    
                    /*
                     * Defini��o de Lote de sa�da de arquivo retorno
                     */

                    $this->setLoteValue();
                    $this->buffer.=$this->GetLote()."\n";
                    
                    if(!$this->checkRow()){
                        return 0;
                    }

                    
                    $this->setControleLote($this->row['controle_lote']);                 
                    $this->setDefaultDetalhe();
                    $this->setDefaultTrailler();
                    
                    //echo $this->row['id_tipo_pag_saida'];
                    //echo $this->query_detalhe;
                    //echo $this->runSqlDetalhe() ? 'true': 'false';


                    if($this->runSqlDetalhe()){

                        //echo $this->query_detalhe;
                        
                        while ($this->row=$this->getRowDetalhe()){

                            //echo $this->runSqlDetalhe() ? 'true': 'false';                        

                            /*
                             * Defini��o de detalhe de sa�da de arquivo retorno
                             */
                            
                            $this->setDetalheValue();
                            $this->buffer.=$this->getDetalhe()."\n";
                            
                            header('Content-Type: text/plain');
                            echo $this->getDetalhe();
                            
                            if(!$this->checkRow()){
                                return 0;
                            }
                            
                            
                        }
                        
                    }
                    else {
                        // Caso ocorra um erro, exibe uma mensagem com o Erro do detalhe
                        $this->close();
                        //$this->setError('Erro ao executar o metodo runSqlDetalhe'); 
                        echo $this->query_detalhe;
                    }
                    
                    if($this->runSqlTrailler()){

                        $this->row=$this->getRowTrailler();
                        $this->setTraillerValue();
                        $this->buffer.=$this->GetTrailler()."\n";
                        
                        if(!$this->checkRow()){
                            return 0;
                        }
                        
                    }
                    else {
                        // Caso ocorra um erro, exibe uma mensagem com o Erro do detalhe
                        $this->close();
                        $this->setError('Erro ao executar o m�todo runSqlTrailler');
                        return 0;
                    }
                    
                }
                
            }    
            else {
                // Caso ocorra um erro, exibe uma mensagem com o Erro do lote
                $this->close();
                $this->setError('Erro ao executar o m�todo runSqlLote');
                return 0;
            }
            
            if($this->runSqlArquivo()){
                
                $this->row=$this->getRowArquivo();
                $this->setArquivoValue();
                $this->buffer.=$this->getArquivo()."\n";
                
                if(!$this->checkRow()){
                    return 0;
                }
                
            }
            else {
                // Caso ocorra um erro, exibe uma mensagem com o Erro do arquivo
                $this->close();
                $this->setError('Erro ao executar o metodo runSqlArquivo');
                return 0;
            }
            
        } 
        else {
            // Caso ocorra um erro, exibe uma mensagem com o Erro do header
            $this->close();
            $this->setError('Erro ao executar o metodo runSqlTrailler');
            return 0;
        }   
        

        
        
        /*
         * Atualiza a flag dos registros processados para 1 indicando que j� foi encaminhado para 
         * para pagamento eletr�nico caso n�o haja nenhum erro no processamento
         */
        
        if(!$this->runSqlRemessa()){
          $this->setError('Erro ao aualizar registros processados para arquivo remessa');
          return 0;
        }


        /*
         * Salva uma linha no log referente a gera��o do arquivo remessa
         */
        
        $this->data_hora = gmdate("d-m-Y H:i:s", mktime());
        
        $this->OutputtoLog($this->data_hora.' - '.$this->user.' -> '.$this->file);
        
        return 1;

    }
    
    /*
     * Executa consulta para importa��o de dados no formato cnab240
     */
    public function RunRetorno(){

        $this->SetDefault();
        
        header('Content-Type: text/plain');

       
        if ($handle = fopen($this->path.$this->file, 'r')) 
        { 
            if (flock($handle, LOCK_EX)) 
            { 
                if ($handle) {
                    while (!feof($handle)) {
                        $this->setRow(fgets($handle, 4096));
                        
                        $this->setHeaderControleTipo(substr($this->row, 7, 1));
                        
                        //echo $row;

                        switch ($this->header_controle_tipo) {
                            case '0': // Registro header de arquivo
                                
                                $this->setHeaderControleBanco(substr($this->row, 0, 3));
                                $this->setHeaderControleLote(substr($this->row, 3, 4));
                                $this->setHeaderControleTipo(substr($this->row, 7, 1));

                                $this->setHeaderCnab1(substr($this->row, 8, 9));
                                
                                $this->setHeaderEmpresaInscTipo(substr($this->row, 17, 1));
                                $this->setHeaderEmpresaNumInsc(substr($this->row, 18, 14));
                                $this->setHeaderEmpresaCodConvenio(substr($this->row, 32, 20));
                                $this->setHeaderEmpresaAgencia(substr($this->row, 52, 5));
                                $this->setHeaderEmpresaAgenciaDigito(substr($this->row, 57, 1));
                                $this->setHeaderEmpresaConta(substr($this->row, 58, 12));
                                $this->setHeaderEmpresaContaDigito(substr($this->row, 70, 1));
                                $this->setHeaderEmpresaAgenciaContaDigito(substr($this->row, 71, 1));
                                $this->setHeaderEmpresaNome(substr($this->row, 72, 30));
                                $this->setHeaderNomeBanco(substr($this->row, 102, 30));
                                
                                $this->setHeaderCnab2(substr($this->row, 132, 10));
                                
                                $this->setHeaderArquivoCodigo(substr($this->row, 142, 1));
                                $this->setHeaderArquivoData(substr($this->row, 143, 8));
                                $this->setHeaderArquivoHora(substr($this->row, 151, 6));
                                $this->setHeaderArquivoSequencial(substr($this->row, 157, 6));
                                $this->setHeaderArquivoVersaoLayout(substr($this->row, 163, 3));
                                $this->setHeaderArquivoDensidade(substr($this->row, 166, 5));
                                
                                $this->setHeaderReservadoBanco(substr($this->row, 171, 20));
                                $this->setHeaderReservadoEmpresa(substr($this->row, 191, 20));
                                
                                $this->setHeaderCnab3(substr($this->row, 212, 29));
                                 
                                echo $this->getHeader();
                                 
                                break;    
                            case '1': // Registro header de lote
                                $this->setLoteControleBanco(substr($this->row, 0, 3));
                                $this->setLoteControleLote(substr($this->row, 3, 4));
                                $this->setLoteControleTipo(substr($this->row, 7, 1));
                                
                                $this->setLoteServicoOperacao(substr($this->row, 7, 1));
                                $this->setLoteServicoServico(substr($this->row, 9, 2));
                                $this->setLoteServicoForma(substr($this->row, 9, 2));
                                $this->setLoteServicoLayout(substr($this->row, 13, 3));

                                $this->setLoteCnab1(substr($this->row, 16, 1));
                                
                                $this->setLoteEmpresaTipoInscricao(substr($this->row, 17, 1));
                                $this->setLoteEmpresaNumInscricao(substr($this->row, 18, 14));
                                $this->setLoteEmpresaCodConvenioBanco(substr($this->row, 32, 20));
                                $this->setLoteEmpresaAgencia(substr($this->row, 52, 5));
                                $this->setLoteEmpresaAgenciaDigito(substr($this->row, 57, 1));
                                $this->setLoteEmpresaConta(substr($this->row, 58, 12));
                                $this->setLoteEmpresaContaDigito(substr($this->row, 70, 1));                                
                                $this->setLoteEmpresaAgenciaContaDigito(substr($this->row, 71, 1));                                

                                $this->setLoteEmpresaNome(substr($this->row, 72, 30));                                

                                $this->setLoteInformacao1(substr($this->row, 102, 40));                                

                                $this->setLoteEnderecoLogradouro(substr($this->row, 142, 30));                                
                                $this->setLoteEmpresaNome(substr($this->row, 72, 30));                                

                                echo $this->getLote();
                                
                                break;    
                            case '3': // Registro detalhe
                                break;
                            case '5': // Registro trailler de lote
                                break;
                            case '9': // Registro trailler de arquivo
                                break;
                            
                        }

                    }
                    fclose($handle);
                }                
                return 1; // Sucesso na grava��o do arquivo de log
            } 
            else {
                $this->setError('N�o poss�vel bloquear o arquivo retorno '.$this->file.' no servidor');
                return 0;
            }
        } 
        else {
            $this->setError('Erro na aberura do arquivo '.$this->file.' no servidor');
            return 0;
        }
        
    
    }
    
    // Retorna o buffer armazenada
    public function putBuffer(){
        echo $this->buffer;
    }
    
    // Retorna a mensagem de erro do processamento
    public function getError(){
        return $this->error;
    }

    // Retorna o n�mero total de linhas de retorno da consulta no record set
    public function getNumRows(){
	return mysql_num_rows($this->rs);
    }
    
    // Retorna o n�mero de vers�o da classe atual
    public function getVersao(){
        return $this->versao;
    }

    // Obtem a linha corrente do recordset do Header
    private function getRowHeader(){
        return mysql_fetch_array($this->rsHeader);
    }

    // Obtem a linha corrente do recordset do Lote
    private function getRowLote(){
        return mysql_fetch_array($this->rsLote);
    }

    // Obtem a linha corrente do recordset do detalhe
    private function getRowDetalhe(){
        return mysql_fetch_array($this->rsDetalhe);
    }
    
    // Obtem a linha corrente do recordset do Trailler
    private function getRowTrailler(){
        return mysql_fetch_array($this->rsTrailler);
    }
    
    // Obtem a linha corrente do recordset do Lote
    private function getRowArquivo(){
        return mysql_fetch_array($this->rsArquivo);
    }
    
    // Obtem o valor da vari�vel de string em linha
    private function getRow(){
        return $this->row;
    }
    
    // Retorna a localiza��o do arquivo CNAB240 gerado
    public function getURLCnab240(){
        return str_replace('/home/ispv/public_html/','http://f71lagos.com/',$this->path.$this->file);
    }   

    // Obtem o objeto do record set
    private function getObj(){
		return mysql_fetch_object($this->rs);
    }
    
    // Executa o SQL e retorna o recordset do header
    private function runSqlHeader() {
        $this->rowLabel =  'HEADER';
        return ($this->rsHeader = mysql_query($this->query_header));
    }
	
    // Executa o SQL e retorna o recordset do lote
    private function runSqlLote() {
        $this->rowLabel =  'LOTE';
        return ($this->rsLote = mysql_query($this->query_lote));
    }   
    
    // Executa o SQL e retorna o recordset do detalhe
    private function runSqlDetalhe() {
        $this->rowLabel =  'DETALHE';
        return ($this->rsDetalhe = mysql_query($this->query_detalhe));     
    }
    
    // Executa o SQL e retorna o recordset do trailler
    private function runSqlTrailler() {
        $this->rowLabel =  'TRAILLER';
        return ($this->rsTrailler = mysql_query($this->query_trailler));
    }
    
    // Executa o SQL e retorna o recordset do arquivo
    private function runSqlArquivo() {
        $this->rowLabel =  'ARQUIVO';
        return ($this->rsArquivo = mysql_query($this->query_arquivo));
    }
    
    // Executa o SQL para atualiza��o dos registros que est�o tendo remessa processada
    private function runSqlRemessa() {
        return mysql_query($this->query_remessa);
    }
    
    private function checkRow(){

        if($this->isNullArray($this->row)){
          $this->setError("$this->rowLabel com campo NULL na coluna ".strtoupper($this->isNullArray($this->row)));
          return 0;  
        }

        if($this->isZeroArray($this->row)){
          $this->setError("$this->rowLabel com campo Zerado na coluna ".strtoupper($this->isZeroArray($this->row)));
          return 0;  
        }

        if(strlen($this->getRowString())!=$this->LEN_CNAB240){
          $this->setError("Tamanho do $this->rowLabel de arquivo ".strlen($this->getRowString())." diferente de $this->LEN_CNAB240");
          return 0;  
        }
        
        return 1;
            
    }

    
    /*
     * Valida��o da classe
     */
    private function check() {
        
        if(is_null($this->user)) {
            $this->setError('Usuaio nao definido');
            return 0;
        }

        if(!is_dir($this->path)) {
            $this->setError('Caminho nao definido');
            return 0;
        }

        
        if(strlen($this->file)==0) {
            $this->setError('Arquivo nao definido');
            return 0;
        }

        if(file_exists($this->path.$this->file)) {
            $this->setError('Arquivo ja existente no servidor');
            return 0;
        }
        
        
        //echo phpinfo();
        
        /*
         * Procedimento para valida��o de todas as tabelas e campos adicionais na gera��o do arquivo remessa
         */
        
        $array = array('saida' => 62,'tipos_pag_saida' => 7,'bancos' => 24,'categoria_pag_saida' => 4);

        foreach ($array as $key => $value) {
        
            $query = "
                     SELECT COUNT(ordinal_position) AS total
                     FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE table_name = '$key'
                     ";        

            if($rs = mysql_query($query)){
                
                $row = mysql_fetch_array($rs);
                
                if($row['total'] < $value){

                    if($row['total'] > 0){
                        $this->setError("Tabela [$key] nao possui todos os campos necessarios para geracao de remessa");
                        return 0;
                    }
                    else {
                        $this->setError("Tabela [$key] inexistente necess�ria para geracao de remessa");
                        return 0;
                    }
                }
                
            }
            else {
                $this->setError("Erro na geracao da consulta a INFORMATION_SCHEMA");
                $this->setError($query);
                return 0;
            }
        }
        
        $query = "
                 SELECT COUNT(s.id_saida) AS totais_registros
                 FROM saida s
                 INNER JOIN regioes r ON s.id_regiao=r.id_regiao
                 INNER JOIN projeto p ON s.id_projeto=p.id_projeto
                 INNER JOIN rhempresa rh ON s.id_projeto=rh.id_projeto
                 INNER JOIN bancos b ON s.id_banco=b.id_banco
                 INNER JOIN tipos_pag_saida tps ON s.id_tipo_pag_saida=tps.id_tipo_pag
                 INNER JOIN categoria_pag_saida cps ON s.id_categoria_pag_saida=cps.id_categoria_saida
                 WHERE s.id_saida IN (".$this->ids_saidas.") AND s.flag_remessa=0
                 ";
        
        /*
         * Valida se o n�mero de ID passadas para processamento foram todas retornadas
         */
        
        $vet = explode(',', $this->ids_saidas);
        
        if($rs = mysql_query($query)){

           $row = mysql_fetch_array($rs);
            
           if($row['totais_registros']!=count($vet)){
             $this->setError("Total de ".$row['totais_registros']." registros retornados para geracao de remessa incompativel com os ".count($vet)." a serem processados");
             return 0;
           }
            
        }
        else {
            $this->setError("Erro na geracao da consulta e validacao das tabelas necessarias a execucao da classe");
            return 0;
        }
        
        return 1;
        
        
    }
    
    private function setCampos($rs){

        while ($row = mysql_fetch_assoc($rs)) {
            $this->campos[] = $row[Field];
        }    
       
    }
    
    /*
     * Function para verificar se existe algu�m elemento do array com valor null
     */
    private function isNullArray($arry) {
        
       
        foreach ($arry as $key => $value) {
            
         
           if(is_null($value) && !is_numeric($key)){
               return $key;
           }
           
        }
        
        return 0;

    }
    
/*
     * Function para verificar se existe algu�m elemento do array com valor null
     */
    private function isZeroArray($arry) {
        
       
        foreach ($arry as $key => $value) {
            
         
           if(strlen($value)==0 && !is_numeric($key)){
               return $key;
           }
           
        }
        
        return 0;

    }    
    
    
   
    /*
     * Function para verificar se existe algu�m elemento do array com valor null
     */
    private function tableCampo($table,$arry) {
        
        $sql = "SHOW COLUMNS from $table";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_assoc($query)) {
            $campos[] = $row[Field];
        }
       
        foreach ($arry as $key => $value) {
            
           if(is_null($value)){
                return $campos[$key];
           }
           
        }
        
        return 0;

    }

    
    
    /*
     * Cria nome de arquivo baseado nas defini��es da febraban
     * Ex: CB010501.REM (CB - Cobran�a Bradesco, 01 - Dia, 05 - M�s, 01 - Sequencial, REM - extens�o)
     */
    private function createFileName(){
        
        
        $this->data_hora = gmdate("dm", mktime());
        $count = 1;
        
        
        while (true) {

            $this->file = 'CB';                                     // Cobran�a Bradesco
            $this->file.= $this->data_hora;                         // Define dia/m�s
            $this->file.= str_pad($count++, 2, "0", STR_PAD_LEFT);  // Define 01 como primeiro arquivo do dia
            $this->file.= ($this->modo_teste) ? $this->EXT_FILE_TEXTE : $this->EXT_FILE;
            
            if(!file_exists($this->path.$this->file)){
               return 1;
            }
            
        }
       
    }
    
    
    
    // Fecha a conex�o ao banco de dados
    private function close()
    {
        //return @mysql_close($this->connect);
    }
    
    /*
     * Necess�rio verificar a exist�ncia de algum arquivo remessa j� processado para a mesma data
     * e caso seja necess�rio incrementar +1 no controle de arquivos remessa gerados no dia
     */
	

    /*
     * Exporta dados de pagamento no formato CNAB240 para aquivo texto
     */
    private function OutputtoFile() 
    { 
        
        $this->createFileName();
       
        if ($handle = fopen($this->path.$this->file, 'w+')) 
        { 
            if (flock($handle, LOCK_EX)) 
            { 
                fwrite($handle, $this->buffer); 
                fclose($handle); 
                return 1; // Sucesso na exporta��o do arquivo
            } 
            else {
                $this->setError('N�o poss�vel bloquear o arquivo '.$this->file.' no servidor');
                return 0;
            }
        } 
        else {
            $this->setError('Erro na cria��o do arquivo .REM no servidor '.$this->file);
            return 0;
        }
    } 
    
    /*
     * Log de gera��o de arquivos remessa
     */
    private function OutputtoLog($log) 
    { 
        
       
        if ($handle = fopen($this->path.$this->FILE_LOG, 'a+')) 
        { 
            if (flock($handle, LOCK_EX)) 
            { 
                fwrite($handle, $log."\n"); 
                fclose($handle); 
                return 1; // Sucesso na grava��o do arquivo de log
            } 
            else {
                $this->setError('N�o poss�vel bloquear o arquivo '.$this->FILE_LOG.' no servidor');
                return 0;
            }
        } 
        else {
            $this->setError('Erro na cria��o do arquivo '.$this->FILE_LOG.' no servidor');
            return 0;
        }
    } 
    
    
    
    /*
     * Escreve conte�do do buffer no servidor e gera retorno para posterior manipula��o.
     */
    public function OutPutRemessa(){
        
        $this->OutputtoFile();     // Escreve o conte�do do buffer em um arquivo no servidor

        //echo 'Filename='.$this->file;
        header('Content-Type: text/plain');
	header("Content-Length: ".filesize($this->path.$this->file));  	  // informa o tamanho do arquivo ao navegador 
	header("Content-Disposition: attachment; filename=".$this->file); // informa ao navegador que � tipo anexo e faz abrir a janela de download, tambem informa o nome do arquivo 
        
        readfile($this->path.$this->file); 
        //echo $this->buffer;         // Retorna o conte�do do buffer em formato texto pela porta 80
        
    }
    
    
 }



?>