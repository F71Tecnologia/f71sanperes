<?php
class FinanArquivoBancoRetornoClass {

    protected $id_retorno;
    protected $data;
    protected $dcto;
    protected $conta_corrente;
    protected $lancamento;
    protected $credito;
    protected $debito;
    protected $saldo;
    protected $id_projeto;
    protected $id_ususario;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' financeiro_banco_retorno ';
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
    function getIdRetorno() {
        return $this->id_retorno;
    }

    function getData($formato = null) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato) : $this->data;
        }
    }

    function getDcto() {
        return $this->dcto;
    }

    function getContaCorrente() {
        return $this->conta_corrente;
    }

    function getLancamento() {
        return $this->lancamento;
    }

    function getCredito() {
        return $this->credito;
    }

    function getDebito() {
        return $this->debito;
    }

    function getSaldo() {
        return $this->saldo;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getIdUsusario() {
        return $this->id_ususario;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdRetorno($id_retorno) {
        $this->id_retorno = $id_retorno;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setDcto($dcto) {
        $this->dcto = $dcto;
    }

    function setContaCorrente($conta_corrente) {
        $this->conta_corrente = $conta_corrente;
    }

    function setLancamento($lancamento) {
        $this->lancamento = $lancamento;
    }

    function setCredito($credito) {
        $this->credito = $credito;
    }

    function setDebito($debito) {
        $this->debito = $debito;
    }

    function setSaldo($saldo) {
        $this->saldo = $saldo;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setIdUsusario($id_ususario) {
        $this->id_ususario = $id_ususario;
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
        $this->id_retorno = null;
        $this->data = null;
        $this->dcto = null;
        $this->conta_corrente = null;
        $this->lancamento = null;
        $this->credito = null;
        $this->debito = null;
        $this->saldo = null;
        $this->id_projeto = null;
        $this->id_ususario = null;
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
        $this->setFROM(' financeiro_banco_retorno ');
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
            $this->setIdRetorno($this->row['id_retorno']);
            $this->setData($this->row['data']);
            $this->setDcto($this->row['dcto']);
            $this->setContaCorrente($this->row['conta_corrente']);
            $this->setLancamento($this->row['lancamento']);
            $this->setCredito($this->row['credito']);
            $this->setDebito($this->row['debito']);
            $this->setSaldo($this->row['saldo']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setIdUsusario($this->row['id_ususario']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'data' => addslashes($this->getData()),
            'dcto' => addslashes($this->getDcto()),
            'conta_corrente' => addslashes($this->getContaCorrente()),
            'lancamento' => addslashes($this->getLancamento()),
            'credito' => addslashes($this->getCredito()),
            'debito' => addslashes($this->getDebito()),
            'saldo' => addslashes($this->getSaldo()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'id_ususario' => addslashes($this->getIdUsusario()),
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
        $this->setQUERY("UPDATE financeiro_banco_retorno SET " . implode(", ", ($camposUpdate)) . " WHERE id_retorno = {$this->getIdRetorno()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO financeiro_banco_retorno ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdRetorno(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE financeiro_banco_retorno SET status = 0 WHERE id_retorno = {$this->getIdRetorno()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM financeiro_banco_retorno WHERE id_retorno = {$this->getIdRetorno()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}
