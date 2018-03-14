<?php
/* 
 * Data Criação: 26/05/2015
 * Desenvolvimento: Renato
 * Versão: 0.1 (Build 00001)
 * 
 * Obs sobre a versão: 
 * 
 */

class MunicipiosClass {
    
    protected $id_municipio;
    protected $uf;
    protected $sigla;
    protected $estado;
    protected $cod_1;
    protected $cod_2;
    protected $municipio;
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " municipios ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsMunicipio;
    protected $rowMunicipio;
    protected $numRowsMunicipio;
        
    function __construct() {
        
    }
    
    function getIdMunicipio() {
        return $this->id_municipio;
    }

    function getUf() {
        return $this->uf;
    }

    function getSigla() {
        return $this->sigla;
    }

    function getEstado() {
        return $this->estado;
    }

    function getCod1() {
        return $this->cod_1;
    }

    function getCod2() {
        return $this->cod_2;
    }

    function getMunicipio() {
        return $this->municipio;
    }

    function setIdMunicipio($id_municipio) {
        $this->id_municipio = $id_municipio;
    }

    function setUf($uf) {
        $this->uf = $uf;
    }

    function setSigla($sigla) {
        $this->sigla = $sigla;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setCod1($cod_1) {
        $this->cod_1 = $cod_1;
    }

    function setCod2($cod_2) {
        $this->cod_2 = $cod_2;
    }

    function setMunicipio($municipio) {
        $this->municipio = $municipio;
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

    
    protected function setRsMunicipio($valor){ 
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
//        echo "A $sql A";
        $this->rsMunicipio = mysql_query($sql);
        $this->numRowsMunicipio = mysql_num_rows($this->rsMunicipio);
        return $this->rsMunicipio;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" municipios ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRowMunicipio(){
        return $this->numRowsMunicipio;
    }

    protected function setRowMunicipio($valor){
        return $this->rowMunicipio = mysql_fetch_assoc($valor);
    }
    
    public function getRowMunicipio(){

        if($this->setRowMunicipio($this->rsMunicipio)){
            
            $this->setIdMunicipio($this->rowMunicipio['id_municipio']);
            $this->setUf($this->rowMunicipio['uf']);
            $this->setSigla($this->rowMunicipio['sigla']);
            $this->setEstado($this->rowMunicipio['estado']);
            $this->setCod1($this->rowMunicipio['cod_1']);
            $this->setCod2($this->rowMunicipio['cod_2']);
            $this->setMunicipio($this->rowMunicipio['municipio']);
            
            return 1;
        } else{
            //$this->setError(mysql_error());
            return 0;
        }
    }
    
    public function getAllMunicipios(){
        $this->limpaQuery();
        $auxMunicipio = (!empty($this->getIdMunicipio())) ? " id_municipio = {$this->getIdMunicipio()} " : null ;
        
        $this->setWHERE("$auxMunicipio");
        $this->setGROUP("municipio");
        $this->setORDER("municipio");
        
        if($this->setRsMunicipio()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function getAllUf(){
        $this->limpaQuery();
        $auxUf = (!empty($this->getSigla())) ? " sigla = '{$this->getSigla()}' " : null ;
        
        $this->setWHERE("$auxUf");
        $this->setGROUP("sigla");
        $this->setORDER("sigla");
        
        if($this->setRsMunicipio()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
}