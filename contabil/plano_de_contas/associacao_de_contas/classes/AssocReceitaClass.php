<?php

class AssocReceitaClass { 

    protected $id;
    protected $id_contabil_1;
    protected $id_contabil_2;
    protected $id_conta;
    protected $data;
    protected $id_funcionario;
    protected $status;
    protected $id_projeto;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_receita_assoc ';
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
    function getId() {
        return $this->id;
    }

    function getIdContabil1() {
        return $this->id_contabil_1;
    }

    function getIdContabil2() {
        return $this->id_contabil_2;
    }

    function getIdConta() {
        return $this->id_conta;
    }

    function getData($formato = null) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato) : $this->data;
        }
    }

    function getIdFuncionario() {
        return $this->id_funcionario;
    }

    function getStatus() {
        return $this->status;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    //SET's DA CLASSE
    function setId($id) {
        $this->id = $id;
    }

    function setIdContabil1($id_contabil_1) {
        $this->id_contabil_1 = $id_contabil_1;
    }

    function setIdContabil2($id_contabil_2) {
        $this->id_contabil_2 = $id_contabil_2;
    }

    function setIdConta($id_conta) {
        $this->id_conta = $id_conta;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setIdFuncionario($id_funcionario) {
        $this->id_funcionario = $id_funcionario;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
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
        $this->id = null;
        $this->id_contabil_1 = null;
        $this->id_contabil_2 = null;
        $this->id_conta = null;
        $this->data = null;
        $this->id_funcionario = null;
        $this->status = null;
        $this->id_projeto = null;
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
        $this->setFROM(' contabil_receita_assoc ');
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
            $this->setId($this->row['id']);
            $this->setIdContabil1($this->row['id_contabil_1']);
            $this->setIdContabil2($this->row['id_contabil_2']);
            $this->setIdConta($this->row['id_conta']);
            $this->setData($this->row['data']);
            $this->setIdFuncionario($this->row['id_funcionario']);
            $this->setStatus($this->row['status']);
            $this->setIdProjeto($this->row['id_projeto']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_contabil_1' => addslashes($this->getIdContabil1()),
            'id_contabil_2' => addslashes($this->getIdContabil2()),
            'id_conta' => addslashes($this->getIdConta()),
            'data' => addslashes($this->getData()),
            'id_funcionario' => addslashes($this->getIdFuncionario()),
            'status' => addslashes($this->getStatus()),
            'id_projeto' => addslashes($this->getIdProjeto()),
        );

        return array_filter($array);
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_receita_assoc SET " . implode(", ", ($camposUpdate)) . " WHERE id = {$this->getId()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_receita_assoc ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setId(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_receita_assoc SET status = 0 WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_receita_assoc WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function consulta(){
        $this->limpaQuery();
        
        $this->setWHERE("id = '{$this->getIdConta()}' AND id_contabil_1 = '{$this->getIdContabil1()}' AND id_contabil_2 = '{$this->getIdContabil2()}' AND id_projeto = '{$this->getIdProjeto()}'");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    } 

}