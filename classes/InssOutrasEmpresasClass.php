<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of InssOutrasEmpresasClass
 *
 * @author Renato
 */
class InssOutrasEmpresasClass { 

    protected $id_inss;
    protected $id_clt;
    protected $salario;
    protected $desconto;
    protected $inicio;
    protected $fim;
    protected $status;
    protected $data_cad;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' rh_inss_outras_empresas ';
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    protected $rs;
    protected $row;
    protected $numRows;

    function __construct() { 
        
    }

    //GET's DA CLASSE
    function getIdInss() {
        return $this->id_inss;
    }

    function getIdClt() {
        return $this->id_clt;
    }

    function getSalario() {
        return $this->salario;
    }

    function getDesconto() {
        return $this->desconto;
    }

    function getInicio($formato = null) {
        if (empty($this->inicio) || $this->inicio == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->inicio), $formato) : $this->inicio;
        }
    }

    function getFim($formato = null) {
        if (empty($this->fim) || $this->fim == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->fim), $formato) : $this->fim;
        }
    }

    function getStatus() {
        return $this->status;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    //SET's DA CLASSE
    function setIdInss($id_inss) {
        $this->id_inss = $id_inss;
    }

    function setIdClt($id_clt) {
        $this->id_clt = $id_clt;
    }

    function setSalario($salario) {
        $this->salario = $salario;
    }

    function setDesconto($desconto) {
        $this->desconto = $desconto;
    }

    function setInicio($inicio) {
        $this->inicio = $inicio;
    }

    function setFim($fim) {
        $this->fim = $fim;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
    }
    
    function setCnpjOutroVinculo($cnpj_outro_vinculo){
        $this->cnpj_outro_vinculo = $cnpj_outro_vinculo;
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

    //SET DEFAULT
    function setDefault() {
        $this->id_inss = null;
        $this->id_clt = null;
        $this->salario = null;
        $this->desconto = null;
        $this->inicio = null;
        $this->fim = null;
        $this->status = null;
        $this->data_cad = null;
    }

    protected function setRs() {
        if (!empty($this->QUERY)) {
            $sql = $this->QUERY;
        } else {
            $auxWhere = (!empty($this->WHERE)) ? " WHERE $this->WHERE " : null;
            $auxGroup = (!empty($this->GROUP)) ? " GROUP BY $this->GROUP " : null;
            $auxHaving = (!empty($this->HAVING)) ? " HAVING $this->HAVING " : null;
            $auxOrder = (!empty($this->ORDER)) ? " ORDER BY $this->ORDER " : null;
            $auxLimit = (!empty($this->LIMIT)) ? " LIMIT $this->LIMIT " : null;

            $sql = "SELECT $this->SELECT FROM $this->FROM $auxWhere $auxGroup $auxHaving $auxOrder $auxLimit";
        }

        $this->rs = mysql_query($sql);
        $this->numRows = mysql_num_rows($this->rs);
        return $this->rs;
    }

    protected function limpaQuery() {
        $this->setQUERY('');
        $this->setSELECT(' * ');
        $this->setFROM(' rh_inss_outras_empresas ');
        $this->setWHERE('');
        $this->setGROUP('');
        $this->setHAVING('');
        $this->setORDER('');
        $this->setLIMIT('');
    }

    public function getNumRows() {
        return $this->numRows;
    }

    protected function setRow($valor) {
        return $this->row = mysql_fetch_assoc($valor);
    }

    //RECUPERANDO INFO DO BANCO
    public function getRow() {

        if ($this->setRow($this->rs)) {
            $this->setIdInss($this->row['id_inss']);
            $this->setIdClt($this->row['id_clt']);
            $this->setSalario($this->row['salario']);
            $this->setDesconto($this->row['desconto']);
            $this->setInicio($this->row['inicio']);
            $this->setFim($this->row['fim']);
            $this->setStatus($this->row['status']);
            $this->setDataCad($this->row['data_cad']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_clt' => addslashes($this->getIdClt()),
            'salario' => addslashes($this->getSalario()),
            'desconto' => addslashes($this->getDesconto()),
            'inicio' => addslashes($this->getInicio()),
            'fim' => addslashes($this->getFim()),
            'status' => addslashes($this->getStatus()),
            'data_cad' => addslashes($this->getDataCad()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE rh_inss_outras_empresas SET " . implode(", ", ($camposUpdate)) . " WHERE id_inss = {$this->getIdInss()} LIMIT 1;");

        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function insert() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);

        $this->setQUERY("INSERT INTO rh_inss_outras_empresas ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdInss(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE rh_inss_outras_empresas SET status = 0 WHERE id_inss = {$this->getIdInss()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM rh_inss_outras_empresas WHERE id_inss = {$this->getIdInss()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getByIdClt() {
        $this->limpaQuery();

        $this->setWHERE("id_clt = {$this->getIdClt()}");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getByIdInss() {
        $this->limpaQuery();

        $this->setWHERE("id_inss = {$this->getIdInss()}");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function inativaByIdClt() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE rh_inss_outras_empresas SET status = 0 WHERE id_clt = {$this->getIdClt()};");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}    
//$objInssOutrasEmpresas->setIdInss($_REQUEST['id_inss']);
//$objInssOutrasEmpresas->setIdClt($_REQUEST['id_clt']);
//$objInssOutrasEmpresas->setSalario($_REQUEST['salario']);
//$objInssOutrasEmpresas->setDesconto($_REQUEST['desconto']);
//$objInssOutrasEmpresas->setInicio($_REQUEST['inicio']);
//$objInssOutrasEmpresas->setFim($_REQUEST['fim']);
//$objInssOutrasEmpresas->setStatus($_REQUEST['status']);
//$objInssOutrasEmpresas->setDataCad($_REQUEST['data_cad']);
