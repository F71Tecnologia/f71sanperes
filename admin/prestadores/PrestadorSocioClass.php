<?php

class SocioClass {
    
    protected $id_socio;
    protected $nome;
    protected $tel;
    protected $cpf;
    protected $id_contabil_empresa;
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " prestador_socio ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsSocio;
    protected $rowSocio;
    protected $numRowsSocio;
        
    function __construct() {
        
    }
    
    function getIdSocio() {
        return $this->id_socio;
    }

    function getNome() {
        return $this->nome;
    }

    function getTel() {
        return $this->tel;
    }

    function getCpf() {
        return $this->cpf;
    }

    function getIdContabilEmpresa() {
        return $this->id_contabil_empresa;
    }

    function setIdSocio($id_socio) {
        $this->id_socio = $id_socio;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setTel($tel) {
        $this->tel = $tel;
    }

    function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    function setIdContabilEmpresa($id_contabil_empresa) {
        $this->id_contabil_empresa = $id_contabil_empresa;
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

    protected function setRsSocio($valor){ 
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
        
        $this->rsSocio = mysql_query($sql);
        $this->numRowsSocio = mysql_num_rows($this->rsSocio);
        return $this->rsSocio;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" prestador_socio ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRowSocio(){
        return $this->numRowsSocio;
    }

    protected function setRowSocio($valor){
        return $this->rowSocio = mysql_fetch_assoc($valor);
    }
    
    public function getRowSocio(){

        if($this->setRowSocio($this->rsSocio)){
            
            $this->setIdSocio($this->rowSocio['id_socio']);
            $this->setNome($this->rowSocio['nome']);
            $this->setTel($this->rowSocio['tel']);
            $this->setCpf($this->rowSocio['cpf']);
            $this->setIdContabilEmpresa($this->rowSocio['id_contabil_empresa']);
            
            return 1;
        } else{
            //$this->setError(mysql_error());
            return 0;
        }
    }
    
    public function getSocios(){
        $this->limpaQuery();
        $auxSocio = (!empty($this->getIdSocio())) ? " AND id_socio = {$this->getIdSocio()} " : null ;
        $auxPrestador = (!empty($this->getIdContabilEmpresa())) ? " AND id_contabil_empresa = {$this->getIdContabilEmpresa()} " : null ;
        
        $this->setWHERE("status = 1 $auxSocio $auxPrestador");
        $this->setORDER("nome");
        
        if($this->setRsSocio()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function insertSocio(){
        $this->limpaQuery();
        
        $array['id_contabil_empresa'] = $this->getIdContabilEmpresa();
        $array['nome'] = $this->getNome();
        $array['tel'] = $this->getTel();
        $array['cpf'] = $this->getCpf();
        
        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);
        
        $this->setQUERY("INSERT INTO prestador_socio ($keys) VALUES ('$values');");
        
//        echo $sql = "$this->QUERY";
        
        if($this->setRsSocio()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function updateSocio(){
        $this->limpaQuery();
        
        $array['id_contabil_empresa'] = $this->getIdContabilEmpresa();
        $array['nome'] = $this->getNome();
        $array['tel'] = $this->getTel();
        $array['cpf'] = $this->getCpf();
        
        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        
        $this->setQUERY("UPDATE prestador_socio SET " . implode(", ",($camposUpdate)) ." WHERE id_socio = {$this->getIdSocio()} LIMIT 1;");
        
        if($this->setRsSocio()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function removerSocio() {
        $QUERY = "UPDATE prestador_socio SET status = 0 WHERE id_socio = {$this->getIdSocio()} LIMIT 1;";
        $this->setQUERY($QUERY);
        if($this->setRsSocio()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
}