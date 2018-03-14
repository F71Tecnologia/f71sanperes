<?php
class AssocBancosClass { 

    protected $id_assoc;
    protected $id_conta;
    protected $id_banco;
    protected $data;
    protected $id_funcionario;
    protected $status;
    protected $id_projeto;
    protected $bco;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_contas_assoc_banco ';
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

    function getIdConta() {
        return $this->id_conta;
    }

    function getIdBanco() {
        return $this->id_banco;
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

    function getBco() {
        return $this->bco;
    }

    //SET's DA CLASSE
    function setIdAssoc($id_assoc) {
        $this->id_assoc = $id_assoc;
    }

    function setIdConta($id_conta) {
        $this->id_conta = $id_conta;
    }

    function setIdBanco($id_banco) {
        $this->id_banco = $id_banco;
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

    function setBco($bco) {
        $this->bco = $bco;
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
        $this->id_conta = null;
        $this->id_banco = null;
        $this->data = null;
        $this->id_funcionario = null;
        $this->status = null;
        $this->id_projeto = null;
        $this->bco = null;
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
        $this->setFROM(' contabil_contas_assoc_banco ');
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
            $this->setIdConta($this->row['id_conta']);
            $this->setIdBanco($this->row['id_banco']);
            $this->setData($this->row['data']);
            $this->setIdFuncionario($this->row['id_funcionario']);
            $this->setStatus($this->row['status']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setBco($this->row['bco']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_conta' => addslashes($this->getIdConta()),
            'id_banco' => addslashes($this->getIdBanco()),
            'data' => addslashes($this->getData()),
            'id_funcionario' => addslashes($this->getIdFuncionario()),
            'status' => addslashes($this->getStatus()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'bco' => addslashes($this->getBco()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_contas_assoc_banco SET " . implode(", ", ($camposUpdate)) . " WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_contas_assoc_banco ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdAssoc(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_contas_assoc_banco SET status = 0 WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_contas_assoc_banco WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function consulta_cc(){
        $this->limpaQuery();
        
        $this->setWHERE("id_banco = '{$this->getIdBanco()}'");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

}