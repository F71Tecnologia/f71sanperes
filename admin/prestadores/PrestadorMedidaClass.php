<?php
/* 
 * Data Criação: 26/05/2015
 * Desenvolvimento: Renato
 * Versão: 0.1 (Build 00001)
 * 
 * Obs sobre a versão: 
 * 
 */

class MedidaClass {
    
    protected $id_medida;
    protected $medida;
    protected $status;
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " prestador_medida ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsMedida;
    protected $rowMedida;
    protected $numRowsMedida;
        
    function __construct() {
        
    }
    
    function getIdMedida() {
        return $this->id_medida;
    }

    function getMedida() {
        return $this->medida;
    }

    function getStatus() {
        return $this->status;
    }

    function setIdMedida($id_medida) {
        $this->id_medida = $id_medida;
    }

    function setMedida($medida) {
        $this->medida = $medida;
    }

    function setStatus($status) {
        $this->status = $status;
    }

        
    protected function setQUERY($QUERY) {
        $this->QUERY = $QUERY;
    }

    protected function setSELECT($SELECT) {
        $this->SELECT = $SELECT;
    }

    protected function setFROM($FROM) {
        $this->FROM = $FROM;
    }

    protected function setWHERE($WHERE) {
        $this->WHERE = $WHERE;
    }

    protected function setGROUP($GROUP) {
        $this->GROUP = $GROUP;
    }

    protected function setORDER($ORDER) {
        $this->ORDER = $ORDER;
    }

    protected function setLIMIT($LIMIT) {
        $this->LIMIT = $LIMIT;
    }

    protected function setHAVING($HAVING) {
        $this->HAVING = $HAVING;
    }

    
    protected function setRsMedida($valor){ 
        if(!empty($this->QUERY)){
            $sql = $this->QUERY;
        } else {
            $auxWhere  = (!empty($this->WHERE))  ? " WHERE $this->WHERE "    : null ;
            $auxGroup  = (!empty($this->GROUP))  ? " GROUP BY $this->GROUP " : null ;
            $auxHaving = (!empty($this->HAVING)) ? " HAVING $this->HAVING "  : null ;
            $auxOrder  = (!empty($this->ORDER))  ? " ORDER BY $this->ORDER " : null ;
            $auxLimit  = (!empty($this->LIMIT))  ? " LIMIT $this->LIMIT "    : null ;
            
            $sql = "SELECT $this->SELECT FROM $this->FROM $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }
        
        $this->rsMedida = mysql_query($sql);
        $this->numRowsMedida = mysql_num_rows($this->rsMedida);
        return $this->rsMedida;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" prestador_medida ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRowMedida(){
        return $this->numRowsMedida;
    }

    protected function setRowMedida($valor){
        return $this->rowMedida = mysql_fetch_assoc($valor);
    }
    
    public function getRowMedida(){

        if($this->setRowMedida($this->rsMedida)){
            
            $this->setIdMedida($this->rowMedida['id_medida']);
            $this->setMedida($this->rowMedida['medida']);
            $this->setStatus($this->rowMedida['status']);
            
            return 1;
        } else{
            //$this->setError(mysql_error());
            return 0;
        }
    }
    
    public function getMedidas(){
        $this->limpaQuery();
        $auxMedida = (!empty($this->getIdMedida())) ? " id_medida = {$this->getIdMedida()} " : null ;
        
        $this->setWHERE("$auxMedida");
        $this->setORDER("medida");
        
        if($this->setRsMedida()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
}