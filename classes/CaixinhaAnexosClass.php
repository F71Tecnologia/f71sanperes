<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CaixinhaAnexosClass
 *
 * @author Ramon
 */
class CaixinhaAnexosClass { 

    protected $id_anexo;
    protected $id_caixinha;
    protected $nome;
    protected $extensao;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' caixinha_anexos ';
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
    function getIdAnexo() {
        return $this->id_anexo;
    }

    function getIdCaixinha() {
        return $this->id_caixinha;
    }

    function getNome() {
        return $this->nome;
    }

    function getExtensao() {
        return $this->extensao;
    }

    //SET's DA CLASSE
    function setIdAnexo($id_anexo) {
        $this->id_anexo = $id_anexo;
    }

    function setIdCaixinha($id_caixinha) {
        $this->id_caixinha = $id_caixinha;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setExtensao($extensao) {
        $this->extensao = $extensao;
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
        $this->id_anexo = null;
        $this->id_caixinha = null;
        $this->nome = null;
        $this->extensao = null;
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
        $this->setFROM(' caixinha_anexos ');
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
            $this->setIdAnexo($this->row['id_anexo']);
            $this->setIdCaixinha($this->row['id_caixinha']);
            $this->setNome($this->row['nome']);
            $this->setExtensao($this->row['extensao']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_caixinha' => addslashes($this->getIdCaixinha()),
            'nome' => addslashes($this->getNome()),
            'extensao' => addslashes($this->getExtensao()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE caixinha_anexos SET " . implode(", ", ($camposUpdate)) . " WHERE id_anexo = {$this->getIdAnexo()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO caixinha_anexos ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdAnexo(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE caixinha_anexos SET status = 0 WHERE id_anexo = {$this->getIdAnexo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM caixinha_anexos WHERE id_anexo = {$this->getIdAnexo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getByIdCaixinha() {
        $this->limpaQuery();
        $this->setWHERE("id_caixinha = {$this->getIdCaixinha()}");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}    