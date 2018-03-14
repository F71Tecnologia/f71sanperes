<?php
/* 
 * Data Criação: 26/05/2015
 * Desenvolvimento: Renato
 * Versão: 0.1 (Build 00001)
 * 
 * Obs sobre a versão: 
 * 
 */

class PrestadorTipoDocClass {
    
    protected $prestador_tipo_doc_id;
    protected $prestador_tipo_doc_nome;
    protected $ordem;
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " prestador_tipo_doc ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsPrestadorTipoDoc;
    protected $rowPrestadorTipoDoc;
    protected $numRowsPrestadorTipoDoc;
        
    function __construct() {
        
    }
    
    function getPrestador_tipo_doc_id() {
        return $this->prestador_tipo_doc_id;
    }

    function getPrestador_tipo_doc_nome() {
        return $this->prestador_tipo_doc_nome;
    }

    function getOrdem() {
        return $this->ordem;
    }

    function setPrestador_tipo_doc_id($prestador_tipo_doc_id) {
        $this->prestador_tipo_doc_id = $prestador_tipo_doc_id;
    }

    function setPrestador_tipo_doc_nome($prestador_tipo_doc_nome) {
        $this->prestador_tipo_doc_nome = $prestador_tipo_doc_nome;
    }

    function setOrdem($ordem) {
        $this->ordem = $ordem;
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

    
    protected function setRsPrestadorTipoDoc($valor){ 
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
        
        $this->rsPrestadorTipoDoc = mysql_query($sql);
        $this->numRowsPrestadorTipoDoc = mysql_num_rows($this->rsPrestadorTipoDoc);
        return $this->rsPrestadorTipoDoc;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" prestador_tipo_doc ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRowPrestadorTipoDoc(){
        return $this->numRowsPrestadorTipoDoc;
    }

    protected function setRowPrestadorTipoDoc($valor){
        return $this->rowPrestadorTipoDoc = mysql_fetch_assoc($valor);
    }
    
    public function getRowPrestadorTipoDoc(){

        if($this->setRowPrestadorTipoDoc($this->rsPrestadorTipoDoc)){
            
            $this->setPrestador_tipo_doc_id($this->rowPrestadorTipoDoc['prestador_tipo_doc_id']);
            $this->setPrestador_tipo_doc_nome($this->rowPrestadorTipoDoc['prestador_tipo_doc_nome']);
            $this->setOrdem($this->rowPrestadorTipoDoc['ordem']);
            
            return 1;
        } else{
            //$this->setError(mysql_error());
            return 0;
        }
    }
    
    public function getTipoDocumento(){
        $this->limpaQuery();
        $auxPrestadorTipoDoc = (!empty($this->getPrestador_tipo_doc_id())) ? " prestador_tipo_doc_id = {$this->getPrestador_tipo_doc_id()} " : null ;
        
        $this->setFROM("prestador_tipo_doc");
        $this->setWHERE("$auxPrestadorTipoDoc");
        $this->setORDER("ordem");
        
        if($this->setRsPrestadorTipoDoc()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
}