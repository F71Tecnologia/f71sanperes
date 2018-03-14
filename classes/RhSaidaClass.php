<?php
/*
 * PHO-DOC - RhSaidaClass.php
 * 
 * Descreva a função da classe aqui
 * 
 * 23-12-2015 
 *
 * @name RhSaidaClass 
 * @package RhSaidaClass
 * @access public/private/protected  
 * 
 * @version
 *
 * Versão: 3.0.0000 - 23-12-2015 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */            
      
        
class RhSaidaClass {

    private     $super_class;    
    public      $error;
    private     $date;
    private     $db; 
        
    private     $saida_default = array(
                                    'id_saida' => 0,
                                    'id_regiao' => 0,
                                    'id_projeto' => 0,
                                    'id_banco' => 0,
                                    'id_user' => 0,
                                    'nome' => '',
                                    'id_nome' => 0,
                                    'especifica' => '',
                                    'tipo' => 0,
                                    'adicional' => '',
                                    'valor' => '',
                                    'data_proc' => '',
                                    'data_vencimento' => '',
                                    'data_pg' => '',
                                    'hora_pg' => '',
                                    'comprovante' => 0,
                                    'tipo_arquivo' => '',
                                    'id_userpg' => 0,
                                    'id_compra' => '',
                                    'campo3' => '',
                                    'status' => 0,
                                    'id_deletado' => 0,
                                    'data_deletado' => '',
                                    'valor_bruto' => 0,
                                    'juridico' => 0,
                                    'id_referencia' => 0,
                                    'id_bens' => 0,
                                    'id_tipo_pag_saida' => 0,
                                    'id_categoria_pag_saida' => 0,
                                    'nosso_numero' => '',
                                    'cod_barra_consumo' => '',
                                    'cod_barra_gerais' => '',
                                    'nota_impressa' => 0,
                                    'id_clt' => 0,
                                    'entradaesaida_subgrupo_id' => 0,
                                    'tipo_boleto' => 0,
                                    'tipo_empresa' => 0,
                                    'id_fornecedor' => 0,
                                    'nome_fornecedor' => '',
                                    'cnpj_fornecedor' => '',
                                    'id_prestador' => '',
                                    'nome_prestador' => '',
                                    'cnpj_prestador' => '',
                                    'impresso' => 0,
                                    'user_impresso' => 0,
                                    'data_impresso' => '',
                                    'id_coop' => 0,
                                    'link_nfe' => '',
                                    'n_documento' => 0,
                                    'estorno' => 0,
                                    'estorno_obs' => '',
                                    'valor_estorno_parcial' => 0,
                                    'id_saida_pai' => 0,
                                    'darf' => 0,
                                    'tipo_darf' => 0,
                                    'mes_competencia' => 0,
                                    'ano_competencia' => 0,
                                    'id_autonomo' => 0,
                                    'dt_emissao_nf' => '',
                                    'tipo_nf' => 0,
                                    'rh_sindicato' => 0,
                                    'flag_remessa' => 0
                                    );

    private     $date_range = array(
                                'field' => '',
                                'ini' => '',
                                'fim' => '',
                                'fmt' => '',
                                'sql_fmt' => ''
                                );
                                
    private     $saida = array();

    private     $saida_save = array();
    
    /*
     * PHP-DOC - Set saida     
     */
     
    /*
     * PHP-DOC 
     * 
     * @name __construct
     * 
     * @internal - Método construtor de classe
     */
    public function __construct()
    {

    }     

    /*
     * PHP-DOC 
     * 
     * @name createCoreClass
     * 
     * @internal - Método que cria e instancia as classes de core (Mãe) para uso da classe
     */
    private function createCoreClass() {
        
        
        if(!isset($this->error)){
            
            include_once('ErrorClass.php');
            
            $this->error = new ErrorClass();        
            
            if(!is_object($this->getSuperClass()->error)){
                
               $this->getSuperClass()->error = $this->error;
               
            }
            
        }
        
        if(!isset($this->db)){
            
            include_once('MySqlClass.php');

            $this->db = new MySqlClass();
            
            if(!is_object($this->getSuperClass()->db)){
                
               $this->getSuperClass()->db = $this->db;
               
            }
            
        }
        
        if(!isset($this->date)){
            
            include_once('DateClass.php');

            $this->date = new DateClass();
            
            if(!is_object($this->getSuperClass()->date)){
                
               $this->getSuperClass()->date = $this->date;
               
            }
        }
        
    }       
    
    /*
     * PHP-DOC 
     * 
     * @name setDefault
     * 
     * @internal - Método que define valores padrões para a superclasse
     */
    public function setDefault() {
        
        $this->createCoreClass();       
        
        $this->saida_save = array();
        
        $this->saida =  $this->saida_default;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setSuperClass
     * 
     * @internal - Método que define o ponteiro da superclasse
     */
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }
    
    
    public function setIdSaida($value) {

        $this->saida_save['id_saida'] = ($this->saida['id_saida'] = $value);

    }

    public function setIdRegiao($value) {

        $this->saida_save['id_regiao'] = ($this->saida['id_regiao'] = $value);

    }

    public function setIdProjeto($value) {

        $this->saida_save['id_projeto'] = ($this->saida['id_projeto'] = $value);

    }

    public function setIdBanco($value) {

        $this->saida_save['id_banco'] = ($this->saida['id_banco'] = $value);

    }

    public function setIdUser($value) {

        $this->saida_save['id_user'] = ($this->saida['id_user'] = $value);

    }

    public function setNome($value) {

        $this->saida_save['nome'] = ($this->saida['nome'] = $value);

    }

    public function setIdNome($value) {

        $this->saida_save['id_nome'] = ($this->saida['id_nome'] = $value);

    }

    public function setEspecifica($value) {

        $this->saida_save['especifica'] = ($this->saida['especifica'] = $value);

    }

    public function setTipo($value) {

        $this->saida_save['tipo'] = ($this->saida['tipo'] = $value);

    }

    public function setAdicional($value) {

        $this->saida_save['adicional'] = ($this->saida['adicional'] = $value);

    }

    public function setValor($value) {

        $this->saida_save['valor'] = ($this->saida['valor'] = $value);

    }

    public function setDataProc($value) {


        $this->saida_save['data_proc'] = ($this->saida['data_proc'] = $value);
        

    }

    public function setDataVencimento($value) {


        $this->saida_save['data_vencimento'] = ($this->saida['data_vencimento'] = $value);
        

    }

    public function setDataPg($value) {


        $this->saida_save['data_pg'] = ($this->saida['data_pg'] = $value);
        

    }

    public function setHoraPg($value) {


        $this->saida_save['hora_pg'] = ($this->saida['hora_pg'] = $value);
        

    }

    public function setComprovante($value) {

        $this->saida_save['comprovante'] = ($this->saida['comprovante'] = $value);

    }

    public function setTipoArquivo($value) {

        $this->saida_save['tipo_arquivo'] = ($this->saida['tipo_arquivo'] = $value);

    }

    public function setIdUserpg($value) {

        $this->saida_save['id_userpg'] = ($this->saida['id_userpg'] = $value);

    }

    public function setIdCompra($value) {

        $this->saida_save['id_compra'] = ($this->saida['id_compra'] = $value);

    }

    public function setCampo3($value) {

        $this->saida_save['campo3'] = ($this->saida['campo3'] = $value);

    }

    public function setStatus($value) {

        $this->saida_save['status'] = ($this->saida['status'] = $value);

    }

    public function setIdDeletado($value) {

        $this->saida_save['id_deletado'] = ($this->saida['id_deletado'] = $value);

    }

    public function setDataDeletado($value) {


        $this->saida_save['data_deletado'] = ($this->saida['data_deletado'] = $value);
        

    }

    public function setValorBruto($value) {

        $this->saida_save['valor_bruto'] = ($this->saida['valor_bruto'] = $value);

    }

    public function setJuridico($value) {

        $this->saida_save['juridico'] = ($this->saida['juridico'] = $value);

    }

    public function setIdReferencia($value) {

        $this->saida_save['id_referencia'] = ($this->saida['id_referencia'] = $value);

    }

    public function setIdBens($value) {

        $this->saida_save['id_bens'] = ($this->saida['id_bens'] = $value);

    }

    public function setIdTipoPagSaida($value) {

        $this->saida_save['id_tipo_pag_saida'] = ($this->saida['id_tipo_pag_saida'] = $value);

    }

    public function setIdCategoriaPagSaida($value) {

        $this->saida_save['id_categoria_pag_saida'] = ($this->saida['id_categoria_pag_saida'] = $value);

    }

    public function setNossoNumero($value) {

        $this->saida_save['nosso_numero'] = ($this->saida['nosso_numero'] = $value);

    }

    public function setCodBarraConsumo($value) {

        $this->saida_save['cod_barra_consumo'] = ($this->saida['cod_barra_consumo'] = $value);

    }

    public function setCodBarraGerais($value) {

        $this->saida_save['cod_barra_gerais'] = ($this->saida['cod_barra_gerais'] = $value);

    }

    public function setNotaImpressa($value) {

        $this->saida_save['nota_impressa'] = ($this->saida['nota_impressa'] = $value);

    }

    public function setIdClt($value) {

        $this->saida_save['id_clt'] = ($this->saida['id_clt'] = $value);

    }

    public function setEntradaesaidaSubgrupoId($value) {

        $this->saida_save['entradaesaida_subgrupo_id'] = ($this->saida['entradaesaida_subgrupo_id'] = $value);

    }

    public function setTipoBoleto($value) {

        $this->saida_save['tipo_boleto'] = ($this->saida['tipo_boleto'] = $value);

    }

    public function setTipoEmpresa($value) {

        $this->saida_save['tipo_empresa'] = ($this->saida['tipo_empresa'] = $value);

    }

    public function setIdFornecedor($value) {

        $this->saida_save['id_fornecedor'] = ($this->saida['id_fornecedor'] = $value);

    }

    public function setNomeFornecedor($value) {

        $this->saida_save['nome_fornecedor'] = ($this->saida['nome_fornecedor'] = $value);

    }

    public function setCnpjFornecedor($value) {

        $this->saida_save['cnpj_fornecedor'] = ($this->saida['cnpj_fornecedor'] = $value);

    }

    public function setIdPrestador($value) {

        $this->saida_save['id_prestador'] = ($this->saida['id_prestador'] = $value);

    }

    public function setNomePrestador($value) {

        $this->saida_save['nome_prestador'] = ($this->saida['nome_prestador'] = $value);

    }

    public function setCnpjPrestador($value) {

        $this->saida_save['cnpj_prestador'] = ($this->saida['cnpj_prestador'] = $value);

    }

    public function setImpresso($value) {

        $this->saida_save['impresso'] = ($this->saida['impresso'] = $value);

    }

    public function setUserImpresso($value) {

        $this->saida_save['user_impresso'] = ($this->saida['user_impresso'] = $value);

    }

    public function setDataImpresso($value) {


        $this->saida_save['data_impresso'] = ($this->saida['data_impresso'] = $value);
        

    }

    public function setIdCoop($value) {

        $this->saida_save['id_coop'] = ($this->saida['id_coop'] = $value);

    }

    public function setLinkNfe($value) {

        $this->saida_save['link_nfe'] = ($this->saida['link_nfe'] = $value);

    }

    public function setNDocumento($value) {

        $this->saida_save['n_documento'] = ($this->saida['n_documento'] = $value);

    }

    public function setEstorno($value) {

        $this->saida_save['estorno'] = ($this->saida['estorno'] = $value);

    }

    public function setEstornoObs($value) {

        $this->saida_save['estorno_obs'] = ($this->saida['estorno_obs'] = $value);

    }

    public function setValorEstornoParcial($value) {

        $this->saida_save['valor_estorno_parcial'] = ($this->saida['valor_estorno_parcial'] = $value);

    }

    public function setIdSaidaPai($value) {

        $this->saida_save['id_saida_pai'] = ($this->saida['id_saida_pai'] = $value);

    }

    public function setDarf($value) {

        $this->saida_save['darf'] = ($this->saida['darf'] = $value);

    }

    public function setTipoDarf($value) {

        $this->saida_save['tipo_darf'] = ($this->saida['tipo_darf'] = $value);

    }

    public function setMesCompetencia($value) {

        $this->saida_save['mes_competencia'] = ($this->saida['mes_competencia'] = $value);

    }

    public function setAnoCompetencia($value) {

        $this->saida_save['ano_competencia'] = ($this->saida['ano_competencia'] = $value);

    }

    public function setIdAutonomo($value) {

        $this->saida_save['id_autonomo'] = ($this->saida['id_autonomo'] = $value);

    }

    public function setDtEmissaoNf($value) {


        $this->saida_save['dt_emissao_nf'] = ($this->saida['dt_emissao_nf'] = $value);
        

    }

    public function setTipoNf($value) {

        $this->saida_save['tipo_nf'] = ($this->saida['tipo_nf'] = $value);

    }

    public function setRhSindicato($value) {

        $this->saida_save['rh_sindicato'] = ($this->saida['rh_sindicato'] = $value);

    }

    public function setFlagRemessa($value) {

        $this->saida_save['flag_remessa'] = ($this->saida['flag_remessa'] = $value);

    }

    public function setDateRangeField($value){

        $this->date_range['field'] = $value;
        
    }
    
    public function setDateRangeIni($value){
    
        $this->date_range['ini'] = $value;
        
    }

    public function setDateRangeFim($value){
        
        $this->date_range['fim'] = $value;

        
    }

    public function setDateRangeFmt($value){
        
        $this->date_range['fmt'] = $value;
        
        $this->setDateRangeSqlFmt($value);
        
    }
    
    private function setDateRangeSqlFmt($value){

        $this->date_range['sql_fmt'] = $this->date->getFmtDateConvSql($value);
        
    }
    
    public function setWhere($value){

        $this->db->setQuery(WHERE," {$value} AND ",$ADD);
        
    }

    /*
     * PHP-DOC - Get saida     */
     
    /*
     * PHP-DOC 
     * 
     * @name getSuperClass
     * 
     * @internal - Método que obtem o ponteiro da superclasse
     */
    public function getSuperClass() {
        
        return $this->super_class;
        
    }       
    

    public function getIdSaida($value) {

        if(empty($value)){
            
            return $this->saida['id_saida'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_saida'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdRegiao($value) {

        if(empty($value)){
            
            return $this->saida['id_regiao'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_regiao'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdProjeto($value) {

        if(empty($value)){
            
            return $this->saida['id_projeto'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_projeto'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdBanco($value) {

        if(empty($value)){
            
            return $this->saida['id_banco'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_banco'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdUser($value) {

        if(empty($value)){
            
            return $this->saida['id_user'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_user'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getNome() {

        return $this->saida['nome'];

    }    

    public function getIdNome($value) {

        if(empty($value)){
            
            return $this->saida['id_nome'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_nome'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getEspecifica() {

        return $this->saida['especifica'];

    }    

    public function getTipo($value) {

        if(empty($value)){
            
            return $this->saida['tipo'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['tipo'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getAdicional() {

        return $this->saida['adicional'];

    }    

    public function getValor() {

        return $this->saida['valor'];

    }    

    public function getDataProc($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->saida['data_proc'])->get($value);    
        
    } 

    public function getDataVencimento($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->saida['data_vencimento'])->get($value);    
        
    } 

    public function getDataPg($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->saida['data_pg'])->get($value);    
        
    } 

    public function getHoraPg($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->saida['hora_pg'])->get($value);    
        
    } 

    public function getComprovante($value) {

        if(empty($value)){
            
            return $this->saida['comprovante'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['comprovante'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getTipoArquivo() {

        return $this->saida['tipo_arquivo'];

    }    

    public function getIdUserpg($value) {

        if(empty($value)){
            
            return $this->saida['id_userpg'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_userpg'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdCompra() {

        return $this->saida['id_compra'];

    }    

    public function getCampo3() {

        return $this->saida['campo3'];

    }    

    public function getStatus($value) {

        if(empty($value)){
            
            return $this->saida['status'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['status'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdDeletado($value) {

        if(empty($value)){
            
            return $this->saida['id_deletado'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_deletado'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getDataDeletado($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->saida['data_deletado'])->get($value);    
        
    } 

    public function getValorBruto($value) {

        if(empty($value)){
            
            return $this->saida['valor_bruto'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['valor_bruto'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getJuridico($value) {

        if(empty($value)){
            
            return $this->saida['juridico'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['juridico'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdReferencia($value) {

        if(empty($value)){
            
            return $this->saida['id_referencia'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_referencia'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdBens($value) {

        if(empty($value)){
            
            return $this->saida['id_bens'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_bens'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdTipoPagSaida($value) {

        if(empty($value)){
            
            return $this->saida['id_tipo_pag_saida'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_tipo_pag_saida'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdCategoriaPagSaida($value) {

        if(empty($value)){
            
            return $this->saida['id_categoria_pag_saida'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_categoria_pag_saida'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getNossoNumero() {

        return $this->saida['nosso_numero'];

    }    

    public function getCodBarraConsumo() {

        return $this->saida['cod_barra_consumo'];

    }    

    public function getCodBarraGerais() {

        return $this->saida['cod_barra_gerais'];

    }    

    public function getNotaImpressa($value) {

        if(empty($value)){
            
            return $this->saida['nota_impressa'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['nota_impressa'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdClt($value) {

        if(empty($value)){
            
            return $this->saida['id_clt'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_clt'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getEntradaesaidaSubgrupoId($value) {

        if(empty($value)){
            
            return $this->saida['entradaesaida_subgrupo_id'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['entradaesaida_subgrupo_id'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getTipoBoleto($value) {

        if(empty($value)){
            
            return $this->saida['tipo_boleto'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['tipo_boleto'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getTipoEmpresa($value) {

        if(empty($value)){
            
            return $this->saida['tipo_empresa'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['tipo_empresa'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdFornecedor($value) {

        if(empty($value)){
            
            return $this->saida['id_fornecedor'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_fornecedor'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getNomeFornecedor() {

        return $this->saida['nome_fornecedor'];

    }    

    public function getCnpjFornecedor() {

        return $this->saida['cnpj_fornecedor'];

    }    

    public function getIdPrestador() {

        return $this->saida['id_prestador'];

    }    

    public function getNomePrestador() {

        return $this->saida['nome_prestador'];

    }    

    public function getCnpjPrestador() {

        return $this->saida['cnpj_prestador'];

    }    

    public function getImpresso($value) {

        if(empty($value)){
            
            return $this->saida['impresso'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['impresso'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getUserImpresso($value) {

        if(empty($value)){
            
            return $this->saida['user_impresso'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['user_impresso'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getDataImpresso($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->saida['data_impresso'])->get($value);    
        
    } 

    public function getIdCoop($value) {

        if(empty($value)){
            
            return $this->saida['id_coop'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_coop'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getLinkNfe() {

        return $this->saida['link_nfe'];

    }    

    public function getNDocumento($value) {

        if(empty($value)){
            
            return $this->saida['n_documento'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['n_documento'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getEstorno($value) {

        if(empty($value)){
            
            return $this->saida['estorno'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['estorno'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getEstornoObs() {

        return $this->saida['estorno_obs'];

    }    

    public function getValorEstornoParcial($value) {

        if(empty($value)){
            
            return $this->saida['valor_estorno_parcial'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['valor_estorno_parcial'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdSaidaPai($value) {

        if(empty($value)){
            
            return $this->saida['id_saida_pai'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_saida_pai'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getDarf($value) {

        if(empty($value)){
            
            return $this->saida['darf'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['darf'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getTipoDarf($value) {

        if(empty($value)){
            
            return $this->saida['tipo_darf'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['tipo_darf'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getMesCompetencia($value) {

        if(empty($value)){
            
            return $this->saida['mes_competencia'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['mes_competencia'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getAnoCompetencia($value) {

        if(empty($value)){
            
            return $this->saida['ano_competencia'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['ano_competencia'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdAutonomo($value) {

        if(empty($value)){
            
            return $this->saida['id_autonomo'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['id_autonomo'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getDtEmissaoNf($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->saida['dt_emissao_nf'])->get($value);    
        
    } 

    public function getTipoNf($value) {

        if(empty($value)){
            
            return $this->saida['tipo_nf'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['tipo_nf'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getRhSindicato($value) {

        if(empty($value)){
            
            return $this->saida['rh_sindicato'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['rh_sindicato'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getFlagRemessa($value) {

        if(empty($value)){
            
            return $this->saida['flag_remessa'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->saida['flag_remessa'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name getDateRangeField
     * 
     * @internal - Método que define o campo para montagem de uma query por intervalo
     */
    public function getDateRangeField(){
        
        return $this->date_range['field'];        
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getDateRangeIni
     * 
     * @internal - Método que define o campo com data de início para montagem de uma query por intervalo
     */
    public function getDateRangeIni($value){
    
        $date = clone $this->date;
    
        return $date->set($this->date_range['ini'])->get($value);    
        
    }

    /*
     * PHP-DOC 
     * 
     * @name getDateRangeFim
     * 
     * @internal - Método que define o campo com data de fim para montagem de uma query por intervalo
     */
    public function getDateRangeFim($value){

        $date = clone $this->date;
    
        return $date->set($this->date_range['fim'])->get($value);    
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getDateRangeFmt
     * 
     * @internal - Método que define a formatação do campo de data para montagem de uma query por intervalo
     */
    public function getDateRangeFmt($value){
        
        return $this->date_range['fmt'];        
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getDateRangeSqlFmt
     * 
     * @internal - Método que define a formatação do campo de data no padrão MySql para montagem de uma query por intervalo
     */
    public function getDateRangeSqlFmt($value){
        
        return $this->date_range['sql_fmt'];        
        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name getRow
     * 
     * @internal - Método que obtem uma linha de registros para a classe
     */
    public function getRow(){

        if($this->db->setRow()){

            $this->setIdSaida($this->db->getRow('id_saida'));
            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->setIdProjeto($this->db->getRow('id_projeto'));
            $this->setIdBanco($this->db->getRow('id_banco'));
            $this->setIdUser($this->db->getRow('id_user'));
            $this->setNome($this->db->getRow('nome'));
            $this->setIdNome($this->db->getRow('id_nome'));
            $this->setEspecifica($this->db->getRow('especifica'));
            $this->setTipo($this->db->getRow('tipo'));
            $this->setAdicional($this->db->getRow('adicional'));
            $this->setValor($this->db->getRow('valor'));
            $this->setDataProc($this->db->getRow('data_proc'));
            $this->setDataVencimento($this->db->getRow('data_vencimento'));
            $this->setDataPg($this->db->getRow('data_pg'));
            $this->setHoraPg($this->db->getRow('hora_pg'));
            $this->setComprovante($this->db->getRow('comprovante'));
            $this->setTipoArquivo($this->db->getRow('tipo_arquivo'));
            $this->setIdUserpg($this->db->getRow('id_userpg'));
            $this->setIdCompra($this->db->getRow('id_compra'));
            $this->setCampo3($this->db->getRow('campo3'));
            $this->setStatus($this->db->getRow('status'));
            $this->setIdDeletado($this->db->getRow('id_deletado'));
            $this->setDataDeletado($this->db->getRow('data_deletado'));
            $this->setValorBruto($this->db->getRow('valor_bruto'));
            $this->setJuridico($this->db->getRow('juridico'));
            $this->setIdReferencia($this->db->getRow('id_referencia'));
            $this->setIdBens($this->db->getRow('id_bens'));
            $this->setIdTipoPagSaida($this->db->getRow('id_tipo_pag_saida'));
            $this->setIdCategoriaPagSaida($this->db->getRow('id_categoria_pag_saida'));
            $this->setNossoNumero($this->db->getRow('nosso_numero'));
            $this->setCodBarraConsumo($this->db->getRow('cod_barra_consumo'));
            $this->setCodBarraGerais($this->db->getRow('cod_barra_gerais'));
            $this->setNotaImpressa($this->db->getRow('nota_impressa'));
            $this->setIdClt($this->db->getRow('id_clt'));
            $this->setEntradaesaidaSubgrupoId($this->db->getRow('entradaesaida_subgrupo_id'));
            $this->setTipoBoleto($this->db->getRow('tipo_boleto'));
            $this->setTipoEmpresa($this->db->getRow('tipo_empresa'));
            $this->setIdFornecedor($this->db->getRow('id_fornecedor'));
            $this->setNomeFornecedor($this->db->getRow('nome_fornecedor'));
            $this->setCnpjFornecedor($this->db->getRow('cnpj_fornecedor'));
            $this->setIdPrestador($this->db->getRow('id_prestador'));
            $this->setNomePrestador($this->db->getRow('nome_prestador'));
            $this->setCnpjPrestador($this->db->getRow('cnpj_prestador'));
            $this->setImpresso($this->db->getRow('impresso'));
            $this->setUserImpresso($this->db->getRow('user_impresso'));
            $this->setDataImpresso($this->db->getRow('data_impresso'));
            $this->setIdCoop($this->db->getRow('id_coop'));
            $this->setLinkNfe($this->db->getRow('link_nfe'));
            $this->setNDocumento($this->db->getRow('n_documento'));
            $this->setEstorno($this->db->getRow('estorno'));
            $this->setEstornoObs($this->db->getRow('estorno_obs'));
            $this->setValorEstornoParcial($this->db->getRow('valor_estorno_parcial'));
            $this->setIdSaidaPai($this->db->getRow('id_saida_pai'));
            $this->setDarf($this->db->getRow('darf'));
            $this->setTipoDarf($this->db->getRow('tipo_darf'));
            $this->setMesCompetencia($this->db->getRow('mes_competencia'));
            $this->setAnoCompetencia($this->db->getRow('ano_competencia'));
            $this->setIdAutonomo($this->db->getRow('id_autonomo'));
            $this->setDtEmissaoNf($this->db->getRow('dt_emissao_nf'));
            $this->setTipoNf($this->db->getRow('tipo_nf'));
            $this->setRhSindicato($this->db->getRow('rh_sindicato'));
            $this->setFlagRemessa($this->db->getRow('flag_remessa'));

            return 1;

        }
        else{

            return 0;
        }

    }
    
    /*
     * PHP-DOC 
     * 
     * @name select
     * 
     * @internal - Método que seleciona os registro para a classe
     */
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
                            id_saida,
                            id_regiao,
                            id_projeto,
                            id_banco,
                            id_user,
                            nome,
                            id_nome,
                            especifica,
                            tipo,
                            adicional,
                            valor,
                            data_proc,
                            data_vencimento,
                            data_pg,
                            hora_pg,
                            comprovante,
                            tipo_arquivo,
                            id_userpg,
                            id_compra,
                            campo3,
                            status,
                            id_deletado,
                            data_deletado,
                            valor_bruto,
                            juridico,
                            id_referencia,
                            id_bens,
                            id_tipo_pag_saida,
                            id_categoria_pag_saida,
                            nosso_numero,
                            cod_barra_consumo,
                            cod_barra_gerais,
                            nota_impressa,
                            id_clt,
                            entradaesaida_subgrupo_id,
                            tipo_boleto,
                            tipo_empresa,
                            id_fornecedor,
                            nome_fornecedor,
                            cnpj_fornecedor,
                            id_prestador,
                            nome_prestador,
                            cnpj_prestador,
                            impresso,
                            user_impresso,
                            data_impresso,
                            id_coop,
                            link_nfe,
                            n_documento,
                            estorno,
                            estorno_obs,
                            valor_estorno_parcial,
                            id_saida_pai,
                            darf,
                            tipo_darf,
                            mes_competencia,
                            ano_competencia,
                            id_autonomo,
                            dt_emissao_nf,
                            tipo_nf,
                            rh_sindicato,
                            flag_remessa
                            ");
        
        $this->db->setQuery(FROM,"saida");
        
        $this->db->setQuery(WHERE,"status_pg = 1",ADD);
        
        $id_regiao = $this->getRegiao();
        $id_projeto = $this->getProjeto();
        $id_pg = $this->getIdPg();
        
        $this->setDateRangeField("CONCAT(ano_pg,mes_pg)");
        $this->setDateRangeFmt('Ym');
        
        $dateRangeField = $this->getDateRangeField();
        $dateRangeFmt = $this->getDateRangeFmt('Ym');
        $dateRangeIni = $this->getDateRangeIni($dateRangeFmt)->val();
        $dateRangeFim = $this->getDateRangeFim($dateRangeFmt)->val();
        $dateRangeSqlFmt = $this->getDateRangeSqlFmt($dateRangeFmt);
        
        if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

        if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}

        if(!empty($terceiro)) {$this->db->setQuery(WHERE,"AND id_pg = {$id_pg}",ADD);} 

        if(!empty($dateRangeIni) && !empty($dateRangeFim)) {$this->db->setQuery(WHERE,"AND $dateRangeField BETWEEN '{$dateRangeIni}' AND '{$dateRangeFim}'",ADD);}
        
        $this->db->setQuery(ORDER,
                            "
                            ano_pg DESC,
                            mes_pg DESC    
                            ");
        
        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do método select da classe RhSaidaClass');
            
        return $this->db;        
            
    }

}
