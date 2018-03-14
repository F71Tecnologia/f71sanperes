<?php
/*
 * PHO-DOC - RhMovimentosRescisaoClass.php
 * 
 * 01-12-2015
 * 
 * Classe da tabela rh_movimentos_rescisao
 * 
 * Versão: 3.0.0000 - ??/??/???? - Jacques - Versão Inicial 
 * 
 * @jacques
 *  
 */            
            
        
class RhMovimentosRescisaoClass {

    private     $super_class;    
    public      $error;
    public      $date;
    public      $db; 
    private     $value;
        
    private     $rh_movimentos_rescisao_default = array(
                                    'id_mov_rescisao' => 0,
                                    'id_rescisao' => 0,
                                    'id_mov' => 0,
                                    'id_clt' => 0,
                                    'nome_movimento' => '',
                                    'tipo_qnt' => 0,
                                    'qnt' => 0,
                                    'qnt_horas' => '',
                                    'valor' => 0,
                                    'status' => 0,
                                    'incidencia' => '',
                                    'complementar' => 0
                                    );

    private     $date_range = array(
                                'field' => '',
                                'ini' => '',
                                'fim' => '',
                                'fmt' => '',
                                'sql_fmt' => ''
                                );
                                
    private     $rh_movimentos_rescisao = array();

    private     $rh_movimentos_rescisao_save = array();
    
    /*
     * PHP-DOC - Set rh_movimentos_rescisao     */
    public function __construct()
    {

    }    
    
    public function __toString() {
        
        return (string)$this->value;
        
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
        
        $this->rh_movimentos_rescisao_save = array();
        
        $this->rh_movimentos_rescisao =  $this->rh_movimentos_rescisao_default;
        
        return $this;
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name setValue
     * 
     * @internal - Define valorer para retorno no uso de métodos encadeados
     */
    public function setValue($value){
        
        $this->value = $value;
        
    }      
    
    /*
     * PHP-DOC - Define o Handle da Super Classe
     */
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }
    
    
    public function setIdMovRescisao($value) {

        $this->rh_movimentos_rescisao_save['id_mov_rescisao'] = ($this->rh_movimentos_rescisao['id_mov_rescisao'] = $value);

    }

    public function setIdRescisao($value) {

        $this->rh_movimentos_rescisao_save['id_rescisao'] = ($this->rh_movimentos_rescisao['id_rescisao'] = $value);

    }

    public function setIdMov($value) {

        $this->rh_movimentos_rescisao_save['id_mov'] = ($this->rh_movimentos_rescisao['id_mov'] = $value);

    }

    public function setIdClt($value) {

        $this->rh_movimentos_rescisao_save['id_clt'] = ($this->rh_movimentos_rescisao['id_clt'] = $value);

    }

    public function setNomeMovimento($value) {

        $this->rh_movimentos_rescisao_save['nome_movimento'] = ($this->rh_movimentos_rescisao['nome_movimento'] = $value);

    }

    public function setTipoQnt($value) {

        $this->rh_movimentos_rescisao_save['tipo_qnt'] = ($this->rh_movimentos_rescisao['tipo_qnt'] = $value);

    }

    public function setQnt($value) {

        $this->rh_movimentos_rescisao_save['qnt'] = ($this->rh_movimentos_rescisao['qnt'] = $value);

    }

    public function setQntHoras($value) {


        $this->rh_movimentos_rescisao_save['qnt_horas'] = ($this->rh_movimentos_rescisao['qnt_horas'] = $value);
        

    }

    public function setValor($value) {

        $this->rh_movimentos_rescisao_save['valor'] = ($this->rh_movimentos_rescisao['valor'] = $value);

    }

    public function setStatus($value) {

        $this->rh_movimentos_rescisao_save['status'] = ($this->rh_movimentos_rescisao['status'] = $value);

    }

    public function setIncidencia($value) {

        $this->rh_movimentos_rescisao_save['incidencia'] = ($this->rh_movimentos_rescisao['incidencia'] = $value);

    }

    public function setComplementar($value) {

        $this->rh_movimentos_rescisao_save['complementar'] = ($this->rh_movimentos_rescisao['complementar'] = $value);

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

        $this->db->setQuery(QUERY,$value,$ADD);
        
    }

    /*
     * PHP-DOC - Get rh_movimentos_rescisao     */
     
    /*
     * PHP-DOC - Obtem o ponteiro da Super Classe
     */
    public function getSuperClass() {
        
        return $this->super_class;
        
    }       
    

    public function getIdMovRescisao($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['id_mov_rescisao'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['id_mov_rescisao'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdRescisao($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['id_rescisao'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['id_rescisao'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdMov($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['id_mov'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['id_mov'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIdClt($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['id_clt'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['id_clt'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getNomeMovimento() {

        return $this->rh_movimentos_rescisao['nome_movimento'];

    }    

    public function getTipoQnt($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['tipo_qnt'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['tipo_qnt'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getQnt($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['qnt'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['qnt'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getQntHoras($value) {
    
        $date = clone $this->date;
    
        return $date->set($this->rh_movimentos_rescisao['qnt_horas'])->get($value);    
        
    } 

    public function getValor($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['valor'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['valor'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getStatus($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['status'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['status'], $casas_decimais, $separador_fracao, $separador_unidades);
            
        } 
       
    }    

    public function getIncidencia() {

        return $this->rh_movimentos_rescisao['incidencia'];

    }    

    public function getComplementar($value) {

        if(empty($value)){
            
            return $this->rh_movimentos_rescisao['complementar'];
            
        }
        else {
            
            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->rh_movimentos_rescisao['complementar'], $casas_decimais, $separador_fracao, $separador_unidades);
            
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

            $this->setIdMovRescisao($this->db->getRow('id_mov_rescisao'));
            $this->setIdRescisao($this->db->getRow('id_rescisao'));
            $this->setIdMov($this->db->getRow('id_mov'));
            $this->setIdClt($this->db->getRow('id_clt'));
            $this->setNomeMovimento($this->db->getRow('nome_movimento'));
            $this->setTipoQnt($this->db->getRow('tipo_qnt'));
            $this->setQnt($this->db->getRow('qnt'));
            $this->setQntHoras($this->db->getRow('qnt_horas'));
            $this->setValor($this->db->getRow('valor'));
            $this->setStatus($this->db->getRow('status'));
            $this->setIncidencia($this->db->getRow('incidencia'));
            $this->setComplementar($this->db->getRow('complementar'));

            return 1;

        }
        else{

            return 0;
        }

    }
    
    /*
     * PHP-DOC - Seleciona um conjunto de registros baseados nos parámetros pre-definidos da classe
     */
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
                            id_mov_rescisao,
                            id_rescisao,
                            id_mov,
                            id_clt,
                            nome_movimento,
                            tipo_qnt,
                            qnt,
                            qnt_horas,
                            valor,
                            status,
                            incidencia,
                            complementar
                            ");
        
        $this->db->setQuery(FROM,"rh_movimentos_rescisao");
        
        $this->db->setQuery(WHERE, " status = 1 ",ADD);
        
        if(is_object($this->getSuperClass()->FolhaProc)){

            $id_clt = $this->getSuperClass()->FolhaProc->getIdClt();
            
        }   
        else {
            
            $id_clt = $this->getIdClt();
            
        }
        
        $id_mov_rescisao = $this->getIdMovRescisao();
        
        $id_rescisao = $this->getIdRescisao();
        
        $id_mov = $this->getIdMov();
       

        if(!empty($id_mov_rescisao)) {$this->db->setQuery(WHERE,"AND id_mov_rescisao = {$id_mov_rescisao}",ADD);}

        if(!empty($id_rescisao)) {$this->db->setQuery(WHERE,"AND id_rescisao = {$id_rescisao}",ADD);}

        if(!empty($id_mov)) {$this->db->setQuery(WHERE,"AND id_mov = {$id_mov}",ADD);}
        
        if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}
        
        $this->db->setQuery(ORDER, " id_clt, id_mov ");

        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do método select da classe RhMovimentosRescisaoClass');
        
        return $this;
            
    }

}
