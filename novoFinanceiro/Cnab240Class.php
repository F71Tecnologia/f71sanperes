<?php

/* 
 * Mѓdulo Objeto de exportaчуo de arquivo remessa para os bancos padrуo CNAB240
 * Data Criaчуo: 20/03/2015
 * Desenvolvimento: Jacques de Azevedo Nunes
 * e-mail: jacques@f71.com.br
 * Versуo: 1.0
 * 
 * Estrutura do arquivo RET
 *  - header do arquivo
 *  - header do lote
 *  - detalhe do segmento
 *  - trailler de lote
 *  - trailler de arquivo
 * 
 * MT101 - Referente р pagamentos locais no Brasil
 * ===============================================
 * 1. Crщdito em Conta Corrente
 * 2. Crщdito em Conta Salсrio
 * 3. Crщdito em Conta Poupanчa
 * 4. DOC/TED
 * 5. Emissуo de Recibo (OP)
 * 6. Emissуo de Cheque Administrativo
 * 7. Pagamento de Tэtulos de Cobranчa Bancсria
 * 8. Pagamento de Contas de Concessionсrias
 * 9. Pagamento de Tributos Padrуo com Cѓdigo de Barras
 * 10.Pagamento do Tributos GPS
 * 11.Pagamento do Tributo DARF e DARF Simples
 * 
 * Estrutura da mensagem
 * =====================
 * Sequъncia A: De apenas uma ocorrъncia, contъm informaчѕes gerais que se aplicam a todas as operaчѕes individuais descritas na sequъncia B 
 * Sequъncia B: Щ repetitiva, isto щ, cada ocorrъncia щ utilizada para indicar os detalhes de cada operaчуo, individualmente.
 * 
 * Obs.: Utiliza sequъncia A para instruчуo de um њnico ordenante e sequъncia B para vсrios
 * 
 * Alteraчѕes a serem realizadas na base de dados
 * 1. Inclusуo do padrуo de tipo de pagamento no formato do cnab240 do bradesco
 * 2. Inclusуo de campo flag_remessa (int) 1 digito default 0 (0=Sem remessa processada, 1=Com remessa processada, 2=Com retorno processado)
 */
Class CNAB240
{

    protected $path = "";                         // Caminho do arquivo de remessa ou retorno
    protected $file = "";                         // Nome do aruivo de remessa ou retoro
    protected $rs = "";                           // Record Set da consulta de dados de saэda
    protected $buffer ="";                        // Buffer que armazena todo os registros de saэda processados para serem salvos no arquivo de remessa
    protected $connect;                           //
    
    // Registro head principal padrуo bradesco

    // Head/Controle
    protected $header_banco = "";                   // Cѓdigo da instituiчуo bancсria no Brasil 3 posiчѕes num [1-3]
    protected $header_lote = "";                    // Nњmero do lote da operaчуo 4 posiчѕes num [4-7] default 0000
    protected $header_tipo = "";                    // Tipo de registro 1 posiчуo num [8-8] default 0
    // Head/Cnab
    protected $header_cnab_01 = "";                 // Uso exclusivo da FEBRAN/CNAB240 9 posiчѕes num [9-17] default brancos
    // Head/Empresa
    protected $header_insc_tipo = "";               // Tipo de inscriчуo da empresa 1 posiчуo num [18-18] 
    protected $header_num_insc = "";                // Nњmero de inscriчуo da empresa 14 posiчѕes num [19-32]
    protected $header_cod_convenio = "";            // Cѓdigo do convъnio no banco 20 posiчѕes alfa [33-52]
    protected $header_agencia = "";                 // Agъncia do banco 5 posiчѕes num [53-57]
    protected $header_agencia_digito = "";          // Dэgito da agъncia 1 posiчуo alfa [58-58]
    protected $header_conta = "";                   // Conta corrente 12 posiчoes num [59-70]
    protected $header_conta_digito = "";            // Dэgito da conta 1 posiчуo alfa [71]
    protected $header_agencia_conta_digito = "";    // Dэgito da agъncia e conta 1 posiчуo alfa [72-72]
    protected $header_nome_da_empresa = "";         // Razуo social da empresa 30 posiчoes alfa [73-102]
    // Head/Nome do banco
    protected $header_nome_do_banco = "";           // Nome do banco 30 posiчѕes alfa [103-132]
    // Head/Cnab
    protected $header_cnab_02 = "";                 // Uso exclusivo da FEBRAN/CNAB240 10 posiчѕes alfa [133-142] default brancos
    // Head/Arquivo
    protected $header_codigo = "";                  // Cѓdigo remessa/retorno 1 posiчуo num [143-143] 
    protected $header_data = "";                    // Data da geraчуo do arquivo 8 posiчѕes num [144-151]
    protected $header_hora = "";                    // Hora da geraчуo do arquivo 6 posiчѕes num [152-157]
    protected $header_sequencial = "";              // Sequencial do arquivo 6 posiчѕes num [158-163]
    protected $header_versao_layout = "";           // Versao layout do arquivo 3 posiчѕes num [164-166] default 050
    protected $header_densidade = "";               // Densidade de gravaчуo do arquivo 5 posiчѕes num [167-171]
    protected $header_reservado_banco =  "";        // Para uso reservado do banco 20 posiчѕes alfa [172-191]
    protected $header_reservado_empresa = "";       // Para uso reservado da empresa 20 posiчѕes alfa [192-211]
    protected $header_cnab_03 = "";                 // Uso exclusivo da FEBRAN/CNAB240 29 posiчѕes alfa [212-240] default brancos
    
    
    // Registro head de lote 
    
    // Lote/Controle
    protected $lote_banco = "";                     // Cѓdigo da instituiчуo bancсria no Brasil 3 posiчѕes num [1-3]
    protected $lote_lote = "";                      // Nњmero do lote da operaчуo 4 posiчѕes num [4-7] default 0000
    protected $lote_tipo = "";                      // Tipo de registro 1 posiчуo num [8-8] default 1
    // Lote/Serviчo
    protected $lote_tipo_operacao = "";             // Tipo de operaчуo 1 posiчуo alfa [9-9] default E
    protected $lote_tipo_servico = "";              // Nњmero de inscriчуo da empresa 2 posiчѕes num [10-11] default 04
    protected $lote_forma_lancamento = "";          // Forma de lanчamento 3 posiчѕes alfa [12-13] default 40
    protected $lote_layout = "";                    // Layout do lote 3 posiчѕes num [14-16] default 050
    protected $lote_cnab_01 = "";                   // Uso exclusivo da FEBRAN/CNAB240 1 posiчуo alfa [17-17] default brancos
    protected $lote_tipo_inscricao = "";            // Tipo de inscriчуo da empresa 1 posiчуo num [18-18] 
    protected $lote_num_inscricao = "";             // Nњmero de inscriчуo da empresa 14 posiчѕes num [19-32]
    protected $lote_cod_convenio_banco = "";        // Cѓdigo do convъnio no banco 20 posiчѕes alfa [33-52]
    protected $lote_agencia = "";                   // Agъncia 5 posiчѕes num [53-57]
    protected $lote_agencia_digito = "";            // Dэgito da agъncia 1 posiчуo alfa [58-58]
    protected $lote_conta = "";                     // Conta corrente 12 posiчoes num [59-70]
    protected $lote_conta_digito = "";              // Dэgito da conta 1 posiчуo alfa [71-71]
    protected $lote_agencia_conta_digito = "";      // Dэgito da agъncia e conta 1 posiчуo alfa [72-72]
    protected $lote_nome_da_empresa = "";           // Razуo social da empresa 30 posiчoes alfa [73-102]
    // Lote/Cnab
    protected $lote_cnab_02 = "";                   // Uso exclusivo da FEBRAN/CNAB240 40 posiчѕes alfa [103-142] default brancos
    // Lote/Arquivo
    protected $lote_data = "";                      // Data do saldo inicial 8 posiчѕes num [143-150] 
    protected $lote_valor = "";                     // Valor do saldo inicial 16+2 posiчѕes decimais num [151-168]
    protected $lote_situacao = "";                  // Situчуo do saldo inicial 1 posiчуo alfa [169-169]
    protected $lote_status = "";                    // Posiчуo do saldo inicial 1 posiчуo alfa [170-170] 
    protected $lote_tipo_moeda = "";                // Moeda referenciada no extrato 3 posiчѕes alfa [171-173]
    protected $lote_seq_extrato = "";               // Sequъncia do extrato 5 posiчѕes num [174-178]
    protected $lote_cnab_03 = "";                   // Uso exclusivo da FEBRAN/CNAB240 62 posiчѕes alfa [179-240] default brancos
    

    // Detalhe/Segmente E
    
    // Detalhe/Controle
    protected $detalhe_banco = "";                  // Cѓdigo da instituiчуo bancсria no Brasil para compensaчуo 3 posiчѕes num [1-3]
    protected $detalhe_lote = "";                   // Nњmero do lote de serviчo 4 posiчѕes num [4-7] default 0000
    protected $detalhe_tipo = "";                   // Tipo de registro 1 posiчуo num [8-8] default 3
    // Detalhe/Serviчo
    protected $detalhe_num_registro = "";           // Nњmero de registro do 5 posiчѕes num [9-13] 
    protected $detalhe_cod_segmento = "";           // Cѓdigo de segmento detalhe 1 posiчуo alfa [14-14]
    // Detalhe/Cnab
    protected $detalhe_cnab_01 = "";                // Uso exclusivo da FEBRAN/CNAB240 9 posiчѕes num [9-17] default brancos
    // Detalhe/Empresa
    protected $detalhe_tipo_inscricao = "";         // Tipo de inscriчуo 1 posiчуo num [18-18] 
    protected $detalhe_numero_inscricao = "";       // Nњmero de inscriчуo 14 posiчѕes num [19-32]
    protected $detalhe_cod_convenio = "";           // Cѓdigo do convъnio no Banco 20 posiчѕes alfa [33-52]
    protected $detalhe_agencia = "";                // Agъncia 5 posiчѕes num [53-57]
    protected $detalhe_agencia_digito = "";         // Dэgito verificador da agencia [58-58]
    protected $detalhe_conta = "";                  // Conta corrente 12 num [59-70]
    protected $detalhe_conta_digito = "";           // Dэgito verificador da conta 1 posiчуo alfa [71-71]
    protected $detalhe_agencia_conta_digito = "";   // Dэgito da agъncia e conta 1 posiчуo alfa [72-72]
    protected $detalhe_nome_da_empresa = "";        // Razуo social da empresa 30 posiчoes alfa [73-102]
     // Lote/Cnab
    protected $detalhe_cnab_02 = "";                // Uso exclusivo da FEBRAN/CNAB240 6 posiчѕes alfa [103-108] default brancos
    // Lote/Natureza
    protected $detalhe_natureza_lancamento = "";     // Natureza do lanчamento 3 posiчѕes alfa [109-111] 
    // Lote/Tipo Complemento
    protected $detalhe_tipo_complemento = "";        // Tipo do complemento 2 posiчѕes num [112-112]
    protected $detalhe_complemento = "";             // Complemento do lanчamento 20 posiчуo alfa [114-133]
    protected $detalhe_cpmf = "";                    // Identificaчуo de isenчуo do cpmf 1 alfa [134-134]
    protected $detalhe_data = "";                    // Posiчуo do saldo inicial 8 posiчуo num [135-142] 
    // Lote/Lanчamento
    protected $detalhe_lancamento_data = "";         // Data do lanчamento 8 posiчѕes num [143-150]
    protected $detalhe_lancamento_valor = "";        // Valor do lanчamento 16+2 num [151-169]
    protected $detalhe_lancamento_tipo = "";         // Tipo do lanчamento com valor a dщbito ou crщdito 1 posiчуo alfa [169-169]
    protected $detalhe_lancamento_categoria = "";    // Categoria do lanчamento 3 posiчѕes num [170-172]
    protected $detalhe_lancamento_cod_historico = "";// Cѓdigo do histѓrico no banco 4 alfa [173-176]
    protected $detalhe_lancamento_descricao = "";    // Descriчуo do histѓrico no banco 25 posiчѕes alfa [177-201]
    protected $detalhe_lancamento_documento = "";    // Nњmero do documento/complemento 39 posiчѕes alfa [202-240]
            
    // Trailler
    
    // Trailler/Controle
    protected $trailler_banco = "";               // Cѓdigo da instituiчуo bancсria no Brasil 3 posiчѕes num [1-3]
    protected $trailler_lote = "";                // Nњmero do lote da operaчуo 4 posiчѕes num [4-7] default 0000
    protected $trailler_tipo = "";                // Tipo de registro 1 posiчуo num [8-8] default 1
    // Trailler/Cnab
    protected $trailler_cnab_01 = "";             // Uso exclusivo da FEBRAN/CNAB240 9 posiчуo alfa [9-17] default brancos
    // Trailler/Empresa
    protected $trailler_tipo_inscricao = "";       // Tipo de inscriчуo 1 posiчуo num [18-18] 
    protected $trailler_numero_inscricao = "";     // Nњmero de inscriчуo 14 posiчѕes num [19-32]
    protected $trailler_cod_convenio = "";         // Cѓdigo do convъnio no Banco 20 posiчѕes alfa [33-52]
    protected $trailler_agencia = "";              // Agъncia 5 posiчѕes num [53-57]
    protected $trailler_agencia_digito = "";       // Dэgito verificador da agencia [58-58]
    protected $trailler_conta = "";                // Conta corrente 12 num [59-70]
    protected $trailler_conta_digito = "";         // Dэgito verificador da conta 1 posiчуo alfa [71-71]
    protected $trailler_agencia_conta_digito = ""; // Dэgito da agъncia e conta 1 posiчуo alfa [72-72]
    // Trailler/Cnab
    protected $trailler_cnab_02 = "";              // Uso exclusivo da FEBRAN/CNAB240 16 posiчѕes alfa [73-88] default brancos
    // Trailler/Valores
    protected $trailler_bloqueado1 = "";           // Vinculado do dia anteior 16+2 posiчѕes num [89-106]
    protected $trailler_limite = "";               // Limite da conta 16+2 posiчѕes num [107-124]
    protected $trailler_bloqueado2 = "";           // Vinculado do dia anteior 16+2 posiчѕes num [125-142]
    // Traillher/Saldo Final
    protected $trailler_data = "";                 // Data do saldo final 8 posiчѕes num [143-150]
    protected $trailler_valor = "";                // Valor do saldo final 16+2 posiчѕes num [151-168]
    protected $trailler_situacao = "";             // Situaчуo do saldo final 1 posiчуo alfa [169-169]
    protected $trailler_status = "";               // Posiчуo do saldo final 1 posiчуo alfa [170-170]
    // Trailler/Totais
    protected $trailler_tot_registros = "";        // Quantidade total de registro do lote 6 posiчѕes [171-176]
    protected $trailler_tot_debitos = "";          // Somatѓrio dos valores a dщbito 16+2 [177-194]
    protected $trailler_tot_creditos = "";         // Somatѓrio doa valores a crщdito 16+2 [195-212]
    protected $trailler_cnab_03 = "";              // Uso exclusivo da FEBRAN/CNAB240 28 posiчѕes alfa [213-240] default brancos

    // Registro Trailler de arquivo
    // Arquivo/Controle
    protected $arquivo_banco = "";                   // Cѓdigo da instituiчуo bancсria no Brasil 3 posiчѕes num [1-3]
    protected $arquivo_lote = "";                    // Nњmero do lote da operaчуo 4 posiчѕes num [4-7] default 9999
    protected $arquivo_tipo = "";                    // Tipo de registro 1 posiчуo num [8-8] default 9
    // Arquivo/Cnab
    protected $arquivo_cnab_01 = "";                 // Uso exclusivo da FEBRAN/CNAB240 9 posiчѕes alfa [9-17] default brancos
    protected $arquivo_tot_lotes = "";               // Quantidade de lotes do arquivo 6 posiчѕes nun [18-23]
    protected $arquivo_tot_registros = "";           // Quantidade de registros do arquivo 6 posiчѕes num [24-29]
    protected $arquivo_tot_conciliacao = "";         // Quantidade de contas para conciliaчуo 6 posiчѕes num [30-35]
    protected $arquivo_cnab_02 = "";                 // Uso exclusivo da FEBRAN/CNAB240 205 posiчѕes alfa [36-240] default brancos
    
    protected function Query($query){

      
        //mysql_select_db("$this->dbname");
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');	

        $this->query = $query;

        if($this->rs = mysql_query($this->query)){
            return 1;
        }else{
            // Caso ocorra um erro, exibe uma mensagem com o Erro
            $this->close();
            return 0;
        }        
    }    
            
    function __construct(){
        
    }
        
    /*
    * Set os valores default para todas as variсveis da classe referentes ao CNAB240
    */
    public function SetDefaultHeader(){
    

            /*
             * Define valores de inэcio das variсveis de cabeчсrio             * 
             */
  
            SetHeaderBanco(str_repeat(' ',3));
            SetHeaderLote(str_repeat('0',4));   // default 0000
            SetHeaderTipo(' ');
            SetHeaderCnab01(str_repeat('0',9)); // default 000000000
            SetHeaderInscTipo(' ');
            SetReaderNumInsc(str_repeat(' ',14));
            SetReaderCodConvenio(str_repeat(' ',20));
            SetHeaderAgencia(str_repeat(' ',5));
            SetHeaderAgenciaDigito(' ');
            SetHeaderConta(str_repeat(' ',12));
            SetHeaderContaDigito(' ');
            SetHeaderNomeDaEmpresa(str_repeat(' ',30));
            SetHeaderNomeDoBanco(str_repeat(' ',30));
            SetHeaderCnab02(str_repeat(' ',30));
            SetHeaderCodigo(' ');
            SetHeaderData(str_repeat(' ',8));
            SetHeaderHora(str_repeat(' ',6));
            SetHeaderSequencial(str_repeat(' ',6));
            SetHeaderVersaoLaytou(str_repeat('0',3)); // default 0
            SetHeaderDensidade(str_repeat(' ',5)); 
            SetHeaderReservadoBanco(str_repeat(' ',20)); 
            SetHeaderReservadoEmpresa(str_repeat(' ',20)); 
            SetHeaderCnab03(str_repeat(' ',29)); 
  
    }
    
    public function SetDefaultLote(){
            
            /*
             * Define valores de inэcio das variсveis de lote             * 
             */
            
            setLoteBanco('');
            setLoteLote('') ;
            setLoteTipo('');
            setLoteOperacao('');
            setLoteServico('');
            setLoteFormaLancamento('');
            setLoteLayout('');
            setLoteCnab01('');
            setLoteTipoInscricao('');
            setLoteNumInscricao('');
            setLoteCodConvenioBanco('');
            setLoteAgencia('');
            setLoteAgenciaDigito('');
            setLoteConta('');
            setLoteContaDigito('');
            setLoteAgenciaContaDigito('');
            setLoteNomeDaEmpresa('');
            setLoteCnab02('');
            setLoteData('');
            setLoteValor('');
            setLoteSituacao('');
            setLoteStatus('');
            setLoteTipoMoeda('');
            setLoteSeqExtrato('');
            setLoteCnab03('');

    }
    
    public function SetDefaultDetalhe(){
            
    
            /*
             * Define valores de inэcio do detalhe do arquivo             * 
             */
            
            setDetalheBanco('');
            setDetalheLote('');
            setDetalheTipo('');
            setDetalheNumRegistro('');
            setDetalheCodSegmento('');
            setDetalheCnab01('');
            setDetalheTipoInscricao('');
            setDetalheNumeroInscricao('');
            setDetalheCodConvenio('');
            setDetalheAgencia('');
            setDetalheAgenciaDigito('');
            setDetalheConta('');
            setDetalheContaDigito('');
            setDetalheAgenciaContaDigito('');
            setDetalheNomeDaEmpresa('');
            setDetalheCnab02('');
            setDetalheNaturezaLancamento('');
            setDetalheTipoComplemento('');
            setDetalheComplemento('');
            setDetalheCpmf('');
            setDetalheData('');
            setDetalheLancamentoValor('');
            setDetalheLancamentoTipo('');
            setDetalheLancamentoCategoria('');
            setDetalheLancamentoCodHistorico('');
            setDetalheLancamentoDescricao('');
            setDetalheLancamentoDocumento('');
    }
    
    public function SetDefaultTrailler(){
            

            /*
             * Define valores do trailler do arquivo             * 
             */
            
            setTraillerBanco('');
            setTraillerLote('');
            setTraillerTipo('');
            setTraillerCnab01('');
            setTraillerTipoInscricao('');
            setTraillerNumeroInscricao('');
            setTraillerCodConvenio('');
            setTraillerAgencia('');
            setTraillerAgenciaDigito('');
            setTraillerConta('');
            setTraillerContaDigito('');
            setTraillerCnab02('');
            setTraillerBloqueado1('');
            setTraillerLimite('');
            setTraillerBloqueado2('');
            setTraillerData('');
            setTraillerValor('');
            setTraillerSituacao('');
            setTraillerStatus('');
            setTraillerTotRegistros('');
            setTraillerTotDebitos('');
            setTraillerTotCreditos('');
            setTraillerCnab03('');
            
    }

    public function SetDefaultArquivo(){
            
            
            /*
             * Define valores finais do arquivo             * 
             */

            setArquivoBanco('');
            setArquivoLote('');
            setArquivoTipo('');
            setArquivoCnab01('');
            setArquivoTotLotes('');
            setArquivoTotRegistros('');
            setArquivoTotConciliacao('');
            setArquivoTotCnab02('');
            
    }
	
    /*PGDDMMX.REM OU PGDDMMXX.REM
     * 1 ou 2 variсveis alfanumщricas: 0, 01, AB, A1, etc.
     * Exemplo: PG250601.REM , PG2506AB.REM , PG2506A1.REM , etc.
     * Quanto ao arquivo-retorno terс a mesma formataчуo, porщm, com a extensуo RET.
     * Exemplo: PG250600.RET , PG250601.RET , PG2506AB.RET , ETC.
     */
    function setFile($file) {
            $this->file = $file;
    }	

    function setHeaderBanco($valor) {
            $this->header_banco = $valor;
    }	

    function setHeaderLote($valor) {
            $this->header_lote = $valor;
    }	

    function setHeaderTipo($valor) {
            $this->header_tipo = $valor;
    }	

    function setHeaderCnab01($valor) {
            $this->header_cnab_01 = $valor;
    }	

    function setHeaderInscTipo($valor) {
            $this->header_insc_tipo = $valor;
    }	

    function setReaderNumInsc($valor) {
            $this->header_num_insc = $valor;
    }	
    
    function setReaderCodConvenio($valor) {
            $this->header_cod_convenio = $valor;
    }	

    function setHeaderAgencia($valor) {
            $this->header_cod_convenio = $valor;
    }	
    
    function setHeaderAgenciaDigito($valor) {
            $this->header_agencia_digito = $valor;
    }	

    function setHeaderConta($valor) {
            $this->header_conta = $valor;
    }	

    function setHeaderContaDigito($valor) {
            $this->header_conta_digito = $valor;
    }	

    function setHeaderNomeDaEmpresa($valor) {
            $this->header_nome_da_empresa = $valor;
    }	
    
    function setHeaderNomeDoBanco($valor) {
            $this->header_nome_do_banco = $valor;
    }	
    
    function setHeaderCnab02($valor) {
            $this->header_cnab_02 = $valor;
    }	

    function setHeaderCodigo($valor) {
            $this->header_codigo = $valor;
    }	

    function setHeaderData($valor) {
            $this->header_data = $valor;
    }	
    
    function setHeaderHora($valor) {
            $this->header_hora = $valor;
    }	

    function setHeaderSequencial($valor) {
            $this->header_sequencial = $valor;
    }	

    function setHeaderVersaoLayout($valor) {
            $this->header_versao_layout = $valor;
    }	

    function setHeaderDensidade($valor) {
            $this->header_densidade = $valor;
    }	

    function setHeaderReservadoBanco($valor) {
            $this->header_reservado_banco = $valor;
    }	
    
    function setHeaderReservadoEmpresa($valor) {
            $this->header_reservado_empresa = $valor;
    }	

    function setHeaderReservadoCnab03($valor) {
            $this->header_cnab_03 = $valor;
    }	
    
    function setLoteBanco($valor) {
            $this->lote_banco = $valor;
    }	

    function setLoteLote($valor) {
            $this->lote_lote = $valor;
    }	

    function setLoteTipo($valor) {
            $this->lote_tipo = $valor;
    }	
    
    function setLoteOperacao($valor) {
            $this->lote_tipo_operacao = $valor;
    }	

    function SetLoteServico($valor){
           $this->lote_tipo_servico = $valor;
    }

    function SetLoteFormaLancamento($valor){
           $this->lote_forma_lancamento = $valor;
    }
    
    function SetLoteLayout($valor){
           $this->lote_layout = $valor;
    }

    function SetLoteCnab01($valor){
           $this->lote_cnab_01 = $valor;
    }
    
    function SetLoteTipoInscricao($valor){
           $this->lote_tipo_inscricao = $valor;
    }

    function SetLoteNumInscricao($valor){
           $this->lote_num_inscricao = $valor;
    }
    
    function SetLoteCodConvenioBanco($valor){
           $this->lote_cod_convenio_banco = $valor;
    }

    function SetLoteAgencia($valor){
           $this->lote_agencia = $valor;
    }
    
    function SetLoteAgenciaDigito($valor){
           $this->lote_agencia_digito = $valor;
    }

    function SetLoteConta($valor){
           $this->lote_conta = $valor;
    }

    function SetLoteContaDigito($valor){
           $this->lote_conta_digito = $valor;
    }

    function SetLoteAgenciaContaDigito($valor){
           $this->lote_agencia_conta_digito = $valor;
    }

    function SetLoteNomeDaEmpresa($valor){
           $this->lote_nome_da_empresa = $valor;
    }

    function SetLoteCnab02($valor){
           $this->lote_cnab_02 = $valor;
    }

    function SetLoteData($valor){
           $this->lote_data = $valor;
    }

    function SetLoteValor($valor){
           $this->lote_valor = $valor;
    }

    function SetLoteSituacao($valor){
           $this->lote_situacao = $valor;
    }
    
    function SetLoteStatus($valor){
           $this->lote_status = $valor;
    }

    function SetLoteTipoMoeda($valor){
           $this->lote_tipo_moeda = $valor;
    }

    function SetLoteSeqExtrato($valor){
           $this->lote_seq_extrato = $valor;
    }

    function SetLoteCnab03($valor){
           $this->lote_cnab_03 = $valor;
    }
    
    function SetDetalheBanco($valor){
           $this->detalhe_banco = $valor;
    }
    
    function SetDetalheLote($valor){
           $this->detalhe_lote = $valor;
    }

    function SetDetalheTipo($valor){
           $this->detalhe_tipo = $valor;
    }

    function SetDetalheNumRegistro($valor){
           $this->detalhe_num_registro = $valor;
    }

    function SetDetalheCodSegmento($valor){
           $this->detalhe_cod_segmento = $valor;
    }

    function SetDetalheCnab01($valor){
           $this->detalhe_cnab_01 = $valor;
    }
    
    function SetDetalheTipoInscricao($valor){
           $this->detalhe_tipo_inscricao = $valor;
    }

    function SetDetalheNumeroInscricao($valor){
           $this->detalhe_numero_inscricao = $valor;
    }

    function SetDetalheCodConvenio($valor){
           $this->detalhe_cod_convenio = $valor;
    }

    function SetDetalheAgencia($valor){
           $this->detalhe_agencia = $valor;
    }
    
    function SetDetalheAgenciaDigito($valor){
           $this->detalhe_agencia_digito = $valor;
    }
    
    function SetDetalheConta($valor){
           $this->detalhe_conta = $valor;
    }
    
    function SetDetalheContaDigito($valor){
           $this->detalhe_Conta_digito = $valor;
    }
    
    function SetDetalheAgenciaContaDigito($valor){
           $this->detalhe_agencia_conta_digito = $valor;
    }

    function SetDetalheNomeDaEmpresa($valor){
           $this->detalhe_nome_da_empresa = $valor;
    }

    function SetDetalheCnab02($valor){
           $this->detalhe_cnab_02 = $valor;
    }
    
    function SetDetalheNaturezaLancamento($valor){
           $this->detalhe_natureza_lancamento = $valor;
    }

    function SetDetalheTipoComplemento($valor){
           $this->detalhe_tipo_complemento = $valor;
    }
    
    function SetDetalheComplemento($valor){
           $this->detalhe_complemento = $valor;
    }
    
    function SetDetalheCpmf($valor){
           $this->detalhe_cpmf = $valor;
    }

    function SetDetalheData($valor){
           $this->detalhe_data = $valor;
    }

    function SetDetalheLancamentoValor($valor){
           $this->detalhe_lancamento_valor = $valor;
    }
    
    function SetDetalheLancamentoTipo($valor){
           $this->detalhe_lancamento_tipo = $valor;
    }

    function SetDetalheLancamentoCategoria($valor){
           $this->detalhe_lancamento_categoria = $valor;
    }

    function SetDetalheLancamentoCodHistorico($valor){
           $this->detalhe_lancamento_cod_historico = $valor;
    }
    
    function SetDetalheLancamentoDescricao($valor){
           $this->detalhe_lancamento_descricao = $valor;
    }
    
    function SetDetalheLancamentoDocumento($valor){
           $this->detalhe_lancamento_documento = $valor;
    }
    
    function SetTraillerBanco($valor){
           $this->trailler_banco = $valor;
    }
    
    function SetTraillerLote($valor){
           $this->trailler_lote = $valor;
    }
    
    function SetTraillerTipo($valor){
           $this->trailler_tipo = $valor;
    }

    function SetTraillerCnab01($valor){
           $this->trailler_cnab_01 = $valor;
    }
    
    function SetTraillerTipoInscricao($valor){
           $this->trailler_tipo_inscricao = $valor;
    }

    function SetTraillerNumeroInscricao($valor){
           $this->trailler_numero_inscricao = $valor;
    }

    function SetTraillerCodConvenio($valor){
           $this->trailler_cod_convenio = $valor;
    }

    function SetTraillerAgencia($valor){
           $this->trailler_agencia = $valor;
    }

    function SetTraillerAgenciaDigito($valor){
           $this->trailler_agencia_digito = $valor;
    }
    
    function SetTraillerConta($valor){
           $this->trailler_conta = $valor;
    }

    function SetTraillerContaDigito($valor){
           $this->trailler_conta_digito = $valor;
    }

    function SetTraillerCnab02($valor){
           $this->trailler_cnab_02 = $valor;
    }
    
    function SetTraillerBloqueado1($valor){
           $this->trailler_bloqueado1 = $valor;
    }

    function SetTraillerLimite($valor){
           $this->trailler_limite = $valor;
    }

    function SetTraillerBloqueado2($valor){
           $this->trailler_bloqueado2 = $valor;
    }

    function SetTraillerData($valor){
           $this->trailler_data = $valor;
    }

    function SetTraillerValor($valor){
           $this->trailler_valor = $valor;
    }
    
    function SetTraillerSituacao($valor){
           $this->trailler_situacao = $valor;
    }

    function SetTraillerStatus($valor){
           $this->trailler_status = $valor;
    }

    function SetTraillerTotRegistros($valor){
           $this->trailler_tot_registros = $valor;
    }
    
    function SetTraillerTotDebitos($valor){
           $this->trailler_tot_debitos = $valor;
    }
    
    function SetTraillerTotCreditos($valor){
           $this->trailler_tot_creditos = $valor;
    }

    function SetTraillerCnab03($valor){
           $this->trailler_cnab_03 = $valor;
    }
    
    function SetArquivoBanco($valor){
           $this->arquivo_banco = $valor;
    }

    function SetArquivoLote($valor){
           $this->arquivo_lote = $valor;
    }

    function SetArquivoTipo($valor){
           $this->arquivo_tipo = $valor;
    }
    
    function SetArquivoCnab01($valor){
           $this->arquivo_cnab_01 = $valor;
    }

    function SetArquivoTotLotes($valor){
           $this->arquivo_tot_lotes = $valor;
    }

    function SetArquivoTotRegistros($valor){
           $this->arquivo_tot_registros = $valor;
    }
    

    function SetArquivoTotConciliacao($valor){
           $this->arquivo_tot_conciliacao = $valor;
    }
    
    function SetArquivoTotCnab02($valor){
           $this->arquivo_cnab_02 = $valor;
    }
    
  
    
    /*
     * Executa consulta para exportaчуo de dados no formato cnab240
     */
    function Query($query){

      
        //mysql_select_db("$this->dbname");
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');	

        $this->query = $query;

        if($this->rs = mysql_query($this->query)){
            return 1;
        }else{
            // Caso ocorra um erro, exibe uma mensagem com o Erro
            $this->close();
            return 0;
        }        
    }

    // Retorna o nњmero total de linhas de retorno da consulta no record set
    function getNumRows(){
        
	return mysql_num_rows($this->rs);
        
    }

    // Obtem a linha corrente do objeto e passa para o prѓximo
    function getRow(){

        return mysql_fetch_array($this->rs);
        
    }

    // Obtem o objeto do record set
    function getObj(){
		
		return mysql_fetch_object($this->rs);
	
    }
	

    // Fecha a conexуo ao banco de dados
    function close()
    {
        //return @mysql_close($this->connect);
    }
    
    /*
     * Necessсrio verificar a existъncia de algum arquivo remessa jс processado para a mesma data
     * e caso seja necessсrio incrementar +1 no controle de arquivos remessa gerados no dia
     */
	

    /*
     * Exporta dados de pagamento no formato CNAB240 para aquivo texto
     */
    function OutputtoFile() 
    { 
        if ($handle = fopen($this->file, 'w+')) 
        { 
            if (flock($handle, LOCK_EX)) 
            { 
                fwrite($handle,'xxxxxxx'); 
                fclose($handle); 
                return 1; // Sucesso na exportaчуo do arquivo
            } 
            
        } 
        else {
                return 0; // Erro na exportaчуo do arquivo 
        }
    } 
    
    function OutPutRemessa(){
        
        $query = "SELECT ";
    
        query($query);
        
    }
 }



?>