<?php

/* 
 * M�dulo Objeto de exporta��o de arquivo remessa para os bancos padr�o CNAB240
 * Data Cria��o: 20/03/2015
 * Desenvolvimento: Jacques de Azevedo Nunes
 * e-mail: jacques@f71.com.br
 * Vers�o: 1.0
 * 
 * Estrutura do arquivo RET
 *  - header do arquivo
 *  - header do lote
 *  - detalhe do segmento
 *  - trailler de lote
 *  - trailler de arquivo
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
 * Altera��es a serem realizadas na base de dados
 * 1. Inclus�o do padr�o de tipo de pagamento no formato do cnab240 do bradesco
 * 2. Inclus�o de campo flag_remessa (int) 1 digito default 0 (0=Sem remessa processada, 1=Com remessa processada, 2=Com retorno processado)
 */
Class CNAB240
{

    protected $path = "";                         // Caminho do arquivo de remessa ou retorno
    protected $file = "";                         // Nome do aruivo de remessa ou retoro
    protected $rs = "";                           // Record Set da consulta de dados de sa�da
    protected $buffer ="";                        // Buffer que armazena todo os registros de sa�da processados para serem salvos no arquivo de remessa
    protected $connect;                           //
    
    // Registro head principal padr�o bradesco

    // Head/Controle
    protected $header_banco = "";                   // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $header_lote = "";                    // N�mero do lote da opera��o 4 posi��es num [4-7] default 0000
    protected $header_tipo = "";                    // Tipo de registro 1 posi��o num [8-8] default 0
    // Head/Cnab
    protected $header_cnab_01 = "";                 // Uso exclusivo da FEBRAN/CNAB240 9 posi��es num [9-17] default brancos
    // Head/Empresa
    protected $header_insc_tipo = "";               // Tipo de inscri��o da empresa 1 posi��o num [18-18] 
    protected $header_num_insc = "";                // N�mero de inscri��o da empresa 14 posi��es num [19-32]
    protected $header_cod_convenio = "";            // C�digo do conv�nio no banco 20 posi��es alfa [33-52]
    protected $header_agencia = "";                 // Ag�ncia do banco 5 posi��es num [53-57]
    protected $header_agencia_digito = "";          // D�gito da ag�ncia 1 posi��o alfa [58-58]
    protected $header_conta = "";                   // Conta corrente 12 posi�oes num [59-70]
    protected $header_conta_digito = "";            // D�gito da conta 1 posi��o alfa [71]
    protected $header_agencia_conta_digito = "";    // D�gito da ag�ncia e conta 1 posi��o alfa [72-72]
    protected $header_nome_da_empresa = "";         // Raz�o social da empresa 30 posi�oes alfa [73-102]
    // Head/Nome do banco
    protected $header_nome_do_banco = "";           // Nome do banco 30 posi��es alfa [103-132]
    // Head/Cnab
    protected $header_cnab_02 = "";                 // Uso exclusivo da FEBRAN/CNAB240 10 posi��es alfa [133-142] default brancos
    // Head/Arquivo
    protected $header_codigo = "";                  // C�digo remessa/retorno 1 posi��o num [143-143] 
    protected $header_data = "";                    // Data da gera��o do arquivo 8 posi��es num [144-151]
    protected $header_hora = "";                    // Hora da gera��o do arquivo 6 posi��es num [152-157]
    protected $header_sequencial = "";              // Sequencial do arquivo 6 posi��es num [158-163]
    protected $header_versao_layout = "";           // Versao layout do arquivo 3 posi��es num [164-166] default 050
    protected $header_densidade = "";               // Densidade de grava��o do arquivo 5 posi��es num [167-171]
    protected $header_reservado_banco =  "";        // Para uso reservado do banco 20 posi��es alfa [172-191]
    protected $header_reservado_empresa = "";       // Para uso reservado da empresa 20 posi��es alfa [192-211]
    protected $header_cnab_03 = "";                 // Uso exclusivo da FEBRAN/CNAB240 29 posi��es alfa [212-240] default brancos
    
    
    // Registro head de lote 
    
    // Lote/Controle
    protected $lote_banco = "";                     // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $lote_lote = "";                      // N�mero do lote da opera��o 4 posi��es num [4-7] default 0000
    protected $lote_tipo = "";                      // Tipo de registro 1 posi��o num [8-8] default 1
    // Lote/Servi�o
    protected $lote_tipo_operacao = "";             // Tipo de opera��o 1 posi��o alfa [9-9] default E
    protected $lote_tipo_servico = "";              // N�mero de inscri��o da empresa 2 posi��es num [10-11] default 04
    protected $lote_forma_lancamento = "";          // Forma de lan�amento 3 posi��es alfa [12-13] default 40
    protected $lote_layout = "";                    // Layout do lote 3 posi��es num [14-16] default 050
    protected $lote_cnab_01 = "";                   // Uso exclusivo da FEBRAN/CNAB240 1 posi��o alfa [17-17] default brancos
    protected $lote_tipo_inscricao = "";            // Tipo de inscri��o da empresa 1 posi��o num [18-18] 
    protected $lote_num_inscricao = "";             // N�mero de inscri��o da empresa 14 posi��es num [19-32]
    protected $lote_cod_convenio_banco = "";        // C�digo do conv�nio no banco 20 posi��es alfa [33-52]
    protected $lote_agencia = "";                   // Ag�ncia 5 posi��es num [53-57]
    protected $lote_agencia_digito = "";            // D�gito da ag�ncia 1 posi��o alfa [58-58]
    protected $lote_conta = "";                     // Conta corrente 12 posi�oes num [59-70]
    protected $lote_conta_digito = "";              // D�gito da conta 1 posi��o alfa [71-71]
    protected $lote_agencia_conta_digito = "";      // D�gito da ag�ncia e conta 1 posi��o alfa [72-72]
    protected $lote_nome_da_empresa = "";           // Raz�o social da empresa 30 posi�oes alfa [73-102]
    // Lote/Cnab
    protected $lote_cnab_02 = "";                   // Uso exclusivo da FEBRAN/CNAB240 40 posi��es alfa [103-142] default brancos
    // Lote/Arquivo
    protected $lote_data = "";                      // Data do saldo inicial 8 posi��es num [143-150] 
    protected $lote_valor = "";                     // Valor do saldo inicial 16+2 posi��es decimais num [151-168]
    protected $lote_situacao = "";                  // Situ��o do saldo inicial 1 posi��o alfa [169-169]
    protected $lote_status = "";                    // Posi��o do saldo inicial 1 posi��o alfa [170-170] 
    protected $lote_tipo_moeda = "";                // Moeda referenciada no extrato 3 posi��es alfa [171-173]
    protected $lote_seq_extrato = "";               // Sequ�ncia do extrato 5 posi��es num [174-178]
    protected $lote_cnab_03 = "";                   // Uso exclusivo da FEBRAN/CNAB240 62 posi��es alfa [179-240] default brancos
    

    // Detalhe/Segmente E
    
    // Detalhe/Controle
    protected $detalhe_banco = "";                  // C�digo da institui��o banc�ria no Brasil para compensa��o 3 posi��es num [1-3]
    protected $detalhe_lote = "";                   // N�mero do lote de servi�o 4 posi��es num [4-7] default 0000
    protected $detalhe_tipo = "";                   // Tipo de registro 1 posi��o num [8-8] default 3
    // Detalhe/Servi�o
    protected $detalhe_num_registro = "";           // N�mero de registro do 5 posi��es num [9-13] 
    protected $detalhe_cod_segmento = "";           // C�digo de segmento detalhe 1 posi��o alfa [14-14]
    // Detalhe/Cnab
    protected $detalhe_cnab_01 = "";                // Uso exclusivo da FEBRAN/CNAB240 9 posi��es num [9-17] default brancos
    // Detalhe/Empresa
    protected $detalhe_tipo_inscricao = "";         // Tipo de inscri��o 1 posi��o num [18-18] 
    protected $detalhe_numero_inscricao = "";       // N�mero de inscri��o 14 posi��es num [19-32]
    protected $detalhe_cod_convenio = "";           // C�digo do conv�nio no Banco 20 posi��es alfa [33-52]
    protected $detalhe_agencia = "";                // Ag�ncia 5 posi��es num [53-57]
    protected $detalhe_agencia_digito = "";         // D�gito verificador da agencia [58-58]
    protected $detalhe_conta = "";                  // Conta corrente 12 num [59-70]
    protected $detalhe_conta_digito = "";           // D�gito verificador da conta 1 posi��o alfa [71-71]
    protected $detalhe_agencia_conta_digito = "";   // D�gito da ag�ncia e conta 1 posi��o alfa [72-72]
    protected $detalhe_nome_da_empresa = "";        // Raz�o social da empresa 30 posi�oes alfa [73-102]
     // Lote/Cnab
    protected $detalhe_cnab_02 = "";                // Uso exclusivo da FEBRAN/CNAB240 6 posi��es alfa [103-108] default brancos
    // Lote/Natureza
    protected $detalhe_natureza_lancamento = "";     // Natureza do lan�amento 3 posi��es alfa [109-111] 
    // Lote/Tipo Complemento
    protected $detalhe_tipo_complemento = "";        // Tipo do complemento 2 posi��es num [112-112]
    protected $detalhe_complemento = "";             // Complemento do lan�amento 20 posi��o alfa [114-133]
    protected $detalhe_cpmf = "";                    // Identifica��o de isen��o do cpmf 1 alfa [134-134]
    protected $detalhe_data = "";                    // Posi��o do saldo inicial 8 posi��o num [135-142] 
    // Lote/Lan�amento
    protected $detalhe_lancamento_data = "";         // Data do lan�amento 8 posi��es num [143-150]
    protected $detalhe_lancamento_valor = "";        // Valor do lan�amento 16+2 num [151-169]
    protected $detalhe_lancamento_tipo = "";         // Tipo do lan�amento com valor a d�bito ou cr�dito 1 posi��o alfa [169-169]
    protected $detalhe_lancamento_categoria = "";    // Categoria do lan�amento 3 posi��es num [170-172]
    protected $detalhe_lancamento_cod_historico = "";// C�digo do hist�rico no banco 4 alfa [173-176]
    protected $detalhe_lancamento_descricao = "";    // Descri��o do hist�rico no banco 25 posi��es alfa [177-201]
    protected $detalhe_lancamento_documento = "";    // N�mero do documento/complemento 39 posi��es alfa [202-240]
            
    // Trailler
    
    // Trailler/Controle
    protected $trailler_banco = "";               // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $trailler_lote = "";                // N�mero do lote da opera��o 4 posi��es num [4-7] default 0000
    protected $trailler_tipo = "";                // Tipo de registro 1 posi��o num [8-8] default 1
    // Trailler/Cnab
    protected $trailler_cnab_01 = "";             // Uso exclusivo da FEBRAN/CNAB240 9 posi��o alfa [9-17] default brancos
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
    protected $trailler_cnab_02 = "";              // Uso exclusivo da FEBRAN/CNAB240 16 posi��es alfa [73-88] default brancos
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
    protected $trailler_cnab_03 = "";              // Uso exclusivo da FEBRAN/CNAB240 28 posi��es alfa [213-240] default brancos

    // Registro Trailler de arquivo
    // Arquivo/Controle
    protected $arquivo_banco = "";                   // C�digo da institui��o banc�ria no Brasil 3 posi��es num [1-3]
    protected $arquivo_lote = "";                    // N�mero do lote da opera��o 4 posi��es num [4-7] default 9999
    protected $arquivo_tipo = "";                    // Tipo de registro 1 posi��o num [8-8] default 9
    // Arquivo/Cnab
    protected $arquivo_cnab_01 = "";                 // Uso exclusivo da FEBRAN/CNAB240 9 posi��es alfa [9-17] default brancos
    protected $arquivo_tot_lotes = "";               // Quantidade de lotes do arquivo 6 posi��es nun [18-23]
    protected $arquivo_tot_registros = "";           // Quantidade de registros do arquivo 6 posi��es num [24-29]
    protected $arquivo_tot_conciliacao = "";         // Quantidade de contas para concilia��o 6 posi��es num [30-35]
    protected $arquivo_cnab_02 = "";                 // Uso exclusivo da FEBRAN/CNAB240 205 posi��es alfa [36-240] default brancos
    
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
    * Set os valores default para todas as vari�veis da classe referentes ao CNAB240
    */
    public function SetDefaultHeader(){
    

            /*
             * Define valores de in�cio das vari�veis de cabe��rio             * 
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
             * Define valores de in�cio das vari�veis de lote             * 
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
             * Define valores de in�cio do detalhe do arquivo             * 
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
     * 1 ou 2 vari�veis alfanum�ricas: 0, 01, AB, A1, etc.
     * Exemplo: PG250601.REM , PG2506AB.REM , PG2506A1.REM , etc.
     * Quanto ao arquivo-retorno ter� a mesma formata��o, por�m, com a extens�o RET.
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
     * Executa consulta para exporta��o de dados no formato cnab240
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

    // Retorna o n�mero total de linhas de retorno da consulta no record set
    function getNumRows(){
        
	return mysql_num_rows($this->rs);
        
    }

    // Obtem a linha corrente do objeto e passa para o pr�ximo
    function getRow(){

        return mysql_fetch_array($this->rs);
        
    }

    // Obtem o objeto do record set
    function getObj(){
		
		return mysql_fetch_object($this->rs);
	
    }
	

    // Fecha a conex�o ao banco de dados
    function close()
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
    function OutputtoFile() 
    { 
        if ($handle = fopen($this->file, 'w+')) 
        { 
            if (flock($handle, LOCK_EX)) 
            { 
                fwrite($handle,'xxxxxxx'); 
                fclose($handle); 
                return 1; // Sucesso na exporta��o do arquivo
            } 
            
        } 
        else {
                return 0; // Erro na exporta��o do arquivo 
        }
    } 
    
    function OutPutRemessa(){
        
        $query = "SELECT ";
    
        query($query);
        
    }
 }



?>