<?php

class SetorClass {
    
    protected $id_setor;
    protected $nome;
    protected $id_projeto;
    protected $id_unidade;
    protected $id_gerencia;
    protected $data_cad;
    protected $id_funcionario_cad;
    protected $status;
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " setor ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsSetor;
    protected $rowSetor;
    protected $numRowsSetor;
        
    function __construct() {
        
    }
    
    function getIdSetor() {
        return $this->id_setor;
    }

    function getNome() {
        return $this->nome;
    }
    function getProjeto() {
        return $this->id_projeto;
    }
    function getUnidade() {
        return $this->id_unidade;
    }
    
    function getGerencia() {
        return $this->id_gerencia;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    function getIdFuncionarioCad() {
        return $this->id_funcionario_cad;
    }

    function getStatus() {
        return $this->status;
    }
    
    function setIdSetor($id_setor) {
        $this->id_setor = $id_setor;
    }
    
    function setProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }
    
    function setUnidade($id_unidade) {
        $this->id_unidade = $id_unidade;
    }
    
    function setNome($nome) {
        $this->nome = $nome;
    }
    
    function setGerencia($id) {
        $this->id_gerencia = $id;
    }

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
    }

    function setIdFuncionarioCad($id_funcionario_cad) {
        $this->id_funcionario_cad = $id_funcionario_cad;
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

    protected function setRsSetor($valor){ 
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
        
        $this->rsSetor = mysql_query($sql);
        $this->numRowsSetor = mysql_num_rows($this->rsSetor);
        return $this->rsSetor;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" setor ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRowSetor(){
        return $this->numRowsSetor;
    }

    protected function setRowSetor($valor){
        return $this->rowSetor = mysql_fetch_assoc($valor);
    }
    
    public function getRowSetor(){

        if($this->setRowSetor($this->rsSetor)){
            
            $this->setIdSetor($this->rowSetor['id_setor']);
            $this->setGerencia($this->rowSetor['id_gerencia']);
            $this->setNome($this->rowSetor['nome']);
            $this->setProjeto($this->rowSetor['id_projeto']);
            $this->setUnidade($this->rowSetor['id_unidade']);
            $this->setStatus($this->rowSetor['status']);
            
            return 1;
        } else{
            return 0;
        }
    }
    
    public function getSetor(){
        $this->limpaQuery();
        
        $auxIdSetor = ($this->getIdSetor() > 0) ? " AND id_setor = {$this->getIdSetor()} ":null;
        
        $this->setWHERE("status = 1 $auxIdSetor");
        $this->setORDER("nome ASC");
        
//        echo $this->QUERY."<br>";
        if($this->setRsSetor()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function getSoterByUnidade($unidade){
        $this->limpaQuery();
        
        $this->setWHERE("id_unidade = $unidade");
        $this->setORDER("nome ASC");
        
        if($this->setRsSetor()){
            return 1;
        } else {
            return 0;
        }
    }
    
    public function getCltSetor($id_regiao){
        $this->limpaQuery();
        
        $sql = "SELECT id_setor, COUNT(*) tot FROM rh_clt WHERE id_regiao = $id_regiao GROUP BY id_setor";
        $qry = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['id_setor']] = $row['tot'];
        }
        return $array;
    }
    
    public function insertSetor(){
        $this->limpaQuery();
        
        $array['nome'] = $this->getNome();
        $array['id_projeto'] = $this->getProjeto();
        $array['id_unidade'] = $this->getUnidade();
//        $array['id_gerencia'] = $this->getGerencia();
        $array['data_cad'] = $this->getDataCad();
        $array['id_funcionario_cad'] = $this->getIdFuncionarioCad();
        $array['status'] = 1;
        
        $keys = implode(', ', array_keys($array));
        $values = implode("' , '", $array);
        
        $this->setQUERY("INSERT INTO setor ($keys) VALUES ('$values');");
        
//        echo $this->QUERY."<br>";
        if($this->setRsSetor()){
            return mysql_insert_id();
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function updateSetor(){
        $this->limpaQuery();
        
        $array['nome'] = $this->getNome();
        $array['id_projeto'] = $this->getProjeto();
        $array['id_unidade'] = $this->getUnidade();
        $array['data_cad'] = $this->getDataCad();
        $array['id_funcionario_cad'] = $this->getIdFuncionarioCad();
        
        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        
        $this->setQUERY("UPDATE setor SET " . implode(", ",($camposUpdate)) ." WHERE id_setor = {$this->getIdSetor()} LIMIT 1;");
//        echo $this->QUERY."<br>";
        if($this->setRsSetor()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function deletarSetor(){
        $this->limpaQuery();
        $this->setQUERY("UPDATE setor SET status = 0 WHERE id_setor = {$this->getIdSetor()} LIMIT 1;");
        echo $this->QUERY."<br>";
        if($this->setRsSetor()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
}