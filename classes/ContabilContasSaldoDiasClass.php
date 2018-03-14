<?php

class ContabilContasSaldoDiaClass { 

    protected $id_saldo;
    protected $id_conta;
    protected $id_lancamento_itens;
    protected $data;
    protected $data_proc;
    protected $tipo;
    protected $valor;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_contas_saldo_dia ';
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
    function getIdSaldo() {
        return $this->id_saldo;
    }

    function getIdConta() {
        return $this->id_conta;
    }

    function getIdLancamentoItens() {
        return $this->id_lancamento_itens;
    }

    function getData($formato) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato = null) : $this->data;
        }
    }

    function getDataProc($formato) {
        if (empty($this->data_proc) || $this->data_proc == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_proc), $formato = null) : $this->data_proc;
        }
    }

    function getTipo() {
        return $this->tipo;
    }

    function getValor() {
        return $this->valor;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdSaldo($id_saldo) {
        $this->id_saldo = $id_saldo;
    }

    function setIdConta($id_conta) {
        $this->id_conta = $id_conta;
    }

    function setIdLancamentoItens($id_lancamento_itens) {
        $this->id_lancamento_itens = $id_lancamento_itens;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setDataProc($data_proc) {
        $this->data_proc = $data_proc;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setValor($valor) {
        $this->valor = $valor;
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
        $this->id_saldo = null;
        $this->id_conta = null;
        $this->id_lancamento_itens = null;
        $this->data = null;
        $this->data_proc = null;
        $this->tipo = null;
        $this->valor = null;
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
        $this->setFROM(' contabil_contas_saldo_dia ');
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
            $this->setIdSaldo($this->row['id_saldo']);
            $this->setIdConta($this->row['id_conta']);
            $this->setIdLancamentoItens($this->row['id_lancamento_itens']);
            $this->setData($this->row['data']);
            $this->setDataProc($this->row['data_proc']);
            $this->setTipo($this->row['tipo']);
            $this->setValor($this->row['valor']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_conta' => addslashes($this->getIdConta()),
            'id_lancamento_itens' => addslashes($this->getIdLancamentoItens()),
            'data' => addslashes($this->getData()),
            'data_proc' => addslashes($this->getDataProc()),
            'tipo' => addslashes($this->getTipo()),
            'valor' => addslashes($this->getValor()),
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
        $this->setQUERY("UPDATE contabil_contas_saldo_dia SET " . implode(", ", ($camposUpdate)) . " WHERE id_saldo = {$this->getIdSaldo()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_contas_saldo_dia ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdSaldo(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_contas_saldo_dia SET status = 0 WHERE id_saldo = {$this->getIdSaldo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_contas_saldo_dia WHERE id_saldo = {$this->getIdSaldo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}
