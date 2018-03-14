<?php

class ContabilHistoricoPadraoClass {

    protected $id_historico;
    protected $texto;
    protected $status;
    protected $data_cad;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_historico_padrao ';
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
    function getIdHistorico() {
        return $this->id_historico;
    }

    function getTexto() {
        return $this->texto;
    }

    function getStatus() {
        return $this->status;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    //SET's DA CLASSE
    function setIdHistorico($id_historico) {
        $this->id_historico = $id_historico;
    }

    function setTexto($texto) {
        $this->texto = $texto;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
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
        $this->id_historico = null;
        $this->texto = null;
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
        $this->setFROM(' contabil_historico_padrao ');
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
            $this->setIdHistorico($this->row['id_historico']);
            $this->setTexto($this->row['texto']);
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
            'texto' => addslashes($this->getTexto()),
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
        $this->setQUERY("UPDATE contabil_historico_padrao SET " . implode(", ", ($camposUpdate)) . " WHERE id_historico = {$this->getIdHistorico()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_historico_padrao ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdHistorico(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();
        $this->setQUERY("UPDATE contabil_historico_padrao SET status = 0 WHERE id_historico = {$this->getIdHistorico()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_historico_padrao WHERE id_historico = {$this->getIdHistorico()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    //------------ EDITADO -----------------------------------------------------

    public function getHistoricoById(){
        $this->setQUERY("SELECT * FROM contabil_historico_padrao WHERE id_historico = {$this->id_historico}");
        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }


    public function listarHistoricos() {
        $this->setStatus(1);
        $this->limpaQuery();

        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
        }

        $this->setWHERE(implode(' AND ', $condicoes));
        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->id_historico)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

}
