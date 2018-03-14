<?php
/*
 * PHO-DOC - RhPagamentosClass.php
 * 
 * Descreva a função da classe aqui
 * 
 * 23-12-2015 
 *
 * @name RhPagamentosClass 
 * @package RhPagamentosClass
 * @access public 
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

class RhPagamentosClass {

    private     $super_class;    
    public      $error;
    private     $date;
    private     $db; 
        
    private     $pagamentos_default = array(
                                'id_pg' => 0,
                                'id_saida' => 0,
                                'tipo_contrato_pg' => 0,
                                'id_folha' => 0,
                                'id_regiao' => 0,
                                'id_projeto' => 0,
                                'mes_pg' => '',
                                'ano_pg' => '',
                                'tipo_pg' => 0,
                                'tipo_descricao' => '',
                                'status_pg' => 0
                                    );

    private     $date_range = array(
                                'field' => '',
                                'ini' => '',
                                'fim' => '',
                                'fmt' => '',
                                'sql_fmt' => ''
                                );
                                
    private     $pagamentos = array();

    private     $pagamentos_save = array();
    
    /*
     * PHP-DOC - Set pagamentos     */
    public function __construct()
    {

    }     
    
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
     * PHP-DOC - Define valores padrões para a classe
     */
    public function setDefault() {
        
        $this->createCoreClass();       
        
        $this->pagamentos_save = array();
        
        $this->pagamentos =  $this->pagamentos_default;
        
    }
    
    /*
     * PHP-DOC - Define o Handle da Super Classe
     */
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }
    
    
    public function setIdPg($value) {

        $this->pagamentos_save['id_pg'] = ($this->pagamentos['id_pg'] = $value);

    }

    public function setIdSaida($value) {

        $this->pagamentos_save['id_saida'] = ($this->pagamentos['id_saida'] = $value);

    }

    public function setTipoContratoPg($value) {

        $this->pagamentos_save['tipo_contrato_pg'] = ($this->pagamentos['tipo_contrato_pg'] = $value);

    }

    public function setIdFolha($value) {

        $this->pagamentos_save['id_folha'] = ($this->pagamentos['id_folha'] = $value);

    }

    public function setIdRegiao($value) {

        $this->pagamentos_save['id_regiao'] = ($this->pagamentos['id_regiao'] = $value);

    }

    public function setIdProjeto($value) {

        $this->pagamentos_save['id_projeto'] = ($this->pagamentos['id_projeto'] = $value);

    }

    public function setMesPg($value) {

        $this->pagamentos_save['mes_pg'] = ($this->pagamentos['mes_pg'] = $value);

    }

    public function setAnoPg($value) {

        $this->pagamentos_save['ano_pg'] = ($this->pagamentos['ano_pg'] = $value);

    }

    public function setTipoPg($value) {

        $this->pagamentos_save['tipo_pg'] = ($this->pagamentos['tipo_pg'] = $value);

    }

    public function setTipoDescricao($value) {


    }

    public function setStatusPg($value) {

        $this->pagamentos_save['status_pg'] = ($this->pagamentos['status_pg'] = $value);

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
     * PHP-DOC - Get pagamentos     */
     
    /*
     * PHP-DOC - Obtem o ponteiro da Super Classe
     */
    public function getSuperClass() {
        
        return $this->super_class;
        
    }       
    

    public function getIdPg($value) {

        if(empty($value)){
            
            return $this->pagamentos['id_pg'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['id_pg'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdSaida($value) {

        if(empty($value)){
            
            return $this->pagamentos['id_saida'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['id_saida'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getTipoContratoPg($value) {

        if(empty($value)){
            
            return $this->pagamentos['tipo_contrato_pg'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['tipo_contrato_pg'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdFolha($value) {

        if(empty($value)){
            
            return $this->pagamentos['id_folha'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['id_folha'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdRegiao($value) {

        if(empty($value)){
            
            return $this->pagamentos['id_regiao'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['id_regiao'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdProjeto($value) {

        if(empty($value)){
            
            return $this->pagamentos['id_projeto'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['id_projeto'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getMesPg() {

        return $this->pagamentos['mes_pg'];

    }    

    public function getAnoPg() {

        return $this->pagamentos['ano_pg'];

    }    

    public function getTipoPg($value) {

        if(empty($value)){
            
            return $this->pagamentos['tipo_pg'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['tipo_pg'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    


    public function getStatusPg($value) {

        if(empty($value)){
            
            return $this->pagamentos['status_pg'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->pagamentos['status_pg'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    
    
    public function getDateRangeField(){
        
        return $this->date_range['field'];        
        
    }
    
    public function getDateRangeIni($value){
    
        $date = clone $this->date;
    
        return $date->set($this->date_range['ini'])->get($value);    
        
    }

    public function getDateRangeFim($value){

        $date = clone $this->date;
    
        return $date->set($this->date_range['fim'])->get($value);    
        
    }
    
    public function getDateRangeFmt($value){
        
        return $this->date_range['fmt'];        
        
    }
    
    public function getDateRangeSqlFmt($value){
        
        return $this->date_range['sql_fmt'];        
        
    }    

    public function getRow(){

        if($this->db->setRow()){

            $this->setIdPg($this->db->getRow('id_pg'));
            $this->setIdSaida($this->db->getRow('id_saida'));
            $this->setTipoContratoPg($this->db->getRow('tipo_contrato_pg'));
            $this->setIdFolha($this->db->getRow('id_folha'));
            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->setIdProjeto($this->db->getRow('id_projeto'));
            $this->setMesPg($this->db->getRow('mes_pg'));
            $this->setAnoPg($this->db->getRow('ano_pg'));
            $this->setTipoPg($this->db->getRow('tipo_pg'));
            $this->setTipoDescricao($this->db->getRow('tipo_descricao'));
            $this->setStatusPg($this->db->getRow('status_pg'));

            return 1;

        }
        else{

            return 0;
        }

    }
    
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
                            id_pg,
                            id_saida,
                            tipo_contrato_pg,
                            id_folha,
                            id_regiao,
                            id_projeto,
                            mes_pg,
                            ano_pg,
                            tipo_pg,
                            tipo_descricao,
                            status_pg
                            ");
        
        $this->db->setQuery(FROM,"pagamentos ");
        
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
        
        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do método select da classe RhFolhaClass');
            
        return $this->db;        
            
    }

}