<?php

class ProtocolosTiposClass {

    protected $id_protocolos_tipo;
    protected $descricao;
    protected $data_cad;
    protected $status;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' protocolos_tipos ';
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
    function getIdProtocolosTipo() {
        return $this->id_protocolos_tipo;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdProtocolosTipo($id_protocolos_tipo) {
        $this->id_protocolos_tipo = $id_protocolos_tipo;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
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
        $this->id_protocolos_tipo = null;
        $this->descricao = null;
        $this->data_cad = null;
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
        $this->rs = mysql_query($sql);
        $this->numRows = mysql_num_rows($this->rs);
        return $this->rs;
    }

    protected function limpaQuery() {
        $this->setQUERY('');
        $this->setSELECT(' * ');
        $this->setFROM(' protocolos_tipos ');
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
            $this->setIdProtocolosTipo($this->row['id_protocolos_tipo']);
            $this->setDescricao($this->row['descricao']);
            $this->setDataCad($this->row['data_cad']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'descricao' => addslashes($this->getDescricao()),
            'data_cad' => addslashes($this->getDataCad()),
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
        $this->setQUERY("UPDATE protocolos_tipos SET " . implode(", ", ($camposUpdate)) . " WHERE id_protocolos_tipo = {$this->getIdProtocolosTipo()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO protocolos_tipos ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdProtocolosTipo(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE protocolos_tipos SET status = 0 WHERE id_protocolos_tipo = {$this->getIdProtocolosTipo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM protocolos_tipos WHERE id_protocolos_tipo = {$this->getIdProtocolosTipo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getProtocolosTipos($toArray = false) {
        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
        }

        $this->limpaQuery();
        $this->setWHERE(implode(' AND ', $condicoes));


        if ($this->setRs()) {
            if ($toArray) {
                while ($row = mysql_fetch_assoc($this->rs)) {
                    $arrayX[] = $row;
                }
                return $arrayX;
            } else {
                return 1;
            }
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->id_protocolos_tipo)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function listaTiposToSelect($todos = FALSE) {
        $this->setStatus(1);
        $this->getProtocolosTipos(false);
        if ($todos) {
            $arr = ['0' => 'TODOS'];
        } else {
            $arr = [-1 => 'Selecione'];
        }

        while ($this->getRow()) {
            $arr[$this->getIdProtocolosTipo()] = $this->getDescricao();
        }
        return $arr;
    }

}
