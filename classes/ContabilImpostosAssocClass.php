<?php

class ContabilImpostosAssocClass {

    protected $id_assoc;
    protected $id_imposto;
    protected $id_contrato;
    protected $aliquota;
    protected $status;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_impostos_assoc ';
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
    function getIdAssoc() {
        return $this->id_assoc;
    }

    function getIdImposto() {
        return $this->id_imposto;
    }

    function getIdContrato() {
        return $this->id_contrato;
    }

    function getAliquota() {
        return $this->aliquota;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdAssoc($id_assoc) {
        $this->id_assoc = $id_assoc;
    }

    function setIdImposto($id_imposto) {
        $this->id_imposto = $id_imposto;
    }

    function setIdContrato($id_contrato) {
        $this->id_contrato = $id_contrato;
    }

    function setAliquota($aliquota) {
        $this->aliquota = $aliquota;
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

    //SET DEFAULT
    function setDefault() {
        $this->id_assoc = null;
        $this->id_imposto = null;
        $this->id_contrato = null;
        $this->aliquota = null;
        $this->status = null;
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
//        echo $sql;
        $this->rs = mysql_query($sql);
        $this->numRows = mysql_num_rows($this->rs);
        return $this->rs;
    }

    protected function limpaQuery() {
        $this->setQUERY('');
        $this->setSELECT(' * ');
        $this->setFROM(' contabil_impostos_assoc ');
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
            $this->setIdAssoc($this->row['id_assoc']);
            $this->setIdImposto($this->row['id_imposto']);
            $this->setIdContrato($this->row['id_contrato']);
            $this->setAliquota($this->row['aliquota']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_imposto' => addslashes($this->getIdImposto()),
            'id_contrato' => addslashes($this->getIdContrato()),
            'aliquota' => addslashes($this->getAliquota()),
            'status' => addslashes($this->getStatus()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_impostos_assoc SET " . implode(", ", ($camposUpdate)) . " WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_impostos_assoc ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdAssoc(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_impostos_assoc SET status = 0 WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_impostos_assoc WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getAssoc() {
        $this->limpaQuery();
        $array = $this->makeCampos();
        $array = array_filter($array);
        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }

        $this->setWHERE(implode(' AND ', $camposUpdate));

        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->id_assoc)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

}
