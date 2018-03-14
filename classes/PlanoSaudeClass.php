<?php

class PlanoSaudeClass {
    
    protected $id_plano_saude;
    protected $razao;
    protected $cnpj;
    protected $data_cad;
    protected $id_funcionario_cad;
    protected $status;
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " plano_saude ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    
    protected $rsPlanoSaude;
    protected $rowPlanoSaude;
    protected $numRowsPlanoSaude;
        
    function __construct() {
        
    }
    
    function getIdPlanoSaude() {
        return $this->id_plano_saude;
    }

    function getRazao() {
        return $this->razao;
    }

    function getCnpj() {
        return $this->cnpj;
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

    function setIdPlanoSaude($id_plano_saude) {
        $this->id_plano_saude = $id_plano_saude;
    }

    function setRazao($razao) {
        $this->razao = $razao;
    }

    function setCnpj($cnpj) {
        $this->cnpj = $cnpj;
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

    protected function setRsPlanoSaude($valor){ 
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
        
        $this->rsPlanoSaude = mysql_query($sql);
        $this->numRowsPlanoSaude = mysql_num_rows($this->rsPlanoSaude);
        return $this->rsPlanoSaude;
    }
    
    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" plano_saude ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }
    
    public function getNumRowPlanoSaude(){
        return $this->numRowsPlanoSaude;
    }

    protected function setRowPlanoSaude($valor){
        return $this->rowPlanoSaude = mysql_fetch_assoc($valor);
    }
    
    public function getRowPlanoSaude(){

        if($this->setRowPlanoSaude($this->rsPlanoSaude)){
            
            $this->setIdPlanoSaude($this->rowPlanoSaude['id_plano_saude']);
            $this->setRazao($this->rowPlanoSaude['razao']);
            $this->setCnpj($this->rowPlanoSaude['cnpj']);
            $this->setDataCad($this->rowPlanoSaude['data_cad']);
            $this->setIdFuncionarioCad($this->rowPlanoSaude['id_funcionario_cad']);
            $this->setStatus($this->rowPlanoSaude['status']);
            
            return 1;
        } else{
            return 0;
        }
    }
    
    public function getPlanoSaude(){
        $this->limpaQuery();
        
        $auxIdPlanoSaude = ($this->getIdPlanoSaude() > 0) ? " AND id_plano_saude = {$this->getIdPlanoSaude()} ":null;
        $auxCnpj = ($this->getCnpj() != '') ? " AND cnpj = '{$this->getCnpj()}' ":null;
        
        $this->setWHERE("status = 1 $auxIdPlanoSaude $auxCnpj");
        $this->setORDER("razao ASC");
        
//        echo $this->QUERY."<br>";
        if($this->setRsPlanoSaude()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function getCltPlanoSaude($id_regiao){
        $this->limpaQuery();
        
        $sql = "SELECT id_plano_saude, COUNT(*) tot FROM rh_clt WHERE id_regiao = $id_regiao GROUP BY id_plano_saude";
        $qry = mysql_query($sql) or die(mysql_error());
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['id_plano_saude']] = $row['tot'];
        }
        return $array;
    }
    
    public function getCltsId($id_plano){
        $sql = "SELECT A.*, B.nome AS nome_projeto
            FROM rh_clt AS A 
            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
            WHERE A.id_plano_saude = {$id_plano}";
        $qry = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($qry);
        
        return $row;
    }
    
    public function insertPlanoSaude(){
        $this->limpaQuery();
        
        $array['razao'] = utf8_encode(str_replace("'", ' ', $this->getRazao()));
        $array['cnpj'] = $this->getCnpj();
        $array['data_cad'] = $this->getDataCad();
        $array['id_funcionario_cad'] = $this->getIdFuncionarioCad();
        $array['status'] = 1;
        
        $keys = implode(', ', array_keys($array));
        $values = implode("' , '", $array);
        
        $this->setQUERY("INSERT INTO plano_saude ($keys) VALUES ('$values');");
        
//        echo $this->QUERY."<br>";
        if($this->setRsPlanoSaude()){
            return mysql_insert_id();
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function updatePlanoSaude(){
        $this->limpaQuery();
        
        $array['razao'] = utf8_encode(str_replace("'", ' ', $this->getRazao()));
        $array['cnpj'] = $this->getCnpj();
        $array['data_cad'] = $this->getDataCad();
        $array['id_funcionario_cad'] = $this->getIdFuncionarioCad();
        
        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        
        $this->setQUERY("UPDATE plano_saude SET " . implode(", ",($camposUpdate)) ." WHERE id_plano_saude = {$this->getIdPlanoSaude()} LIMIT 1;");
//        echo $this->QUERY."<br>";
        if($this->setRsPlanoSaude()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
    
    public function deletarPlanoSaude(){
        $this->limpaQuery();
        $this->setQUERY("UPDATE plano_saude SET status = 0 WHERE id_plano_saude = {$this->getIdPlanoSaude()} LIMIT 1;");
        //echo $this->QUERY."<br>";
        if($this->setRsPlanoSaude()){
            return 1;
        } else {
            return 0;//$this->setError(mysql_error());
        }
    }
}