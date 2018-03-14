<?php

class ContabilContasPagarClass { 

    protected $id_conta_pagar;
    protected $valor;
    protected $valor_liquido;
    protected $data_lancamento;
    protected $data_vencimento;
    protected $credor;
    protected $classificacao_credor;
    protected $documento;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_contas_pagar ';
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
    function getIdContaPagar() {
        return $this->id_conta_pagar;
    }

    function getValor() {
        return $this->valor;
    }

    function getValorLiquido() {
        return $this->valor_liquido;
    }

    function getDataLancamento($formato) {
        if (empty($this->data_lancamento) || $this->data_lancamento == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_lancamento), $formato) : $this->data_lancamento;
        }
    }

    function getDataVencimento($formato) {
        if (empty($this->data_vencimento) || $this->data_vencimento == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_vencimento), $formato) : $this->data_vencimento;
        }
    }

    function getCredor() {
        return $this->credor;
    }

    function getClassificacaoCredor() {
        return $this->classificacao_credor;
    }

    function getDocumento() {
        return $this->documento;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdContaPagar($id_conta_pagar) {
        $this->id_conta_pagar = $id_conta_pagar;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setValorLiquido($valor_liquido) {
        $this->valor_liquido = $valor_liquido;
    }

    function setDataLancamento($data_lancamento) {
        $this->data_lancamento = $data_lancamento;
    }

    function setDataVencimento($data_vencimento) {
        $this->data_vencimento = $data_vencimento;
    }

    function setCredor($credor) {
        $this->credor = $credor;
    }

    function setClassificacaoCredor($classificacao_credor) {
        $this->classificacao_credor = $classificacao_credor;
    }

    function setDocumento($documento) {
        $this->documento = $documento;
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

    protected function setRs($valor) {
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
        $this->setFROM(' contabil_contas_pagar ');
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
            $this->setIdContaPagar($this->row['id_conta_pagar']);
            $this->setValor($this->row['valor']);
            $this->setValorLiquido($this->row['valor_liquido']);
            $this->setDataLancamento($this->row['data_lancamento']);
            $this->setDataVencimento($this->row['data_vencimento']);
            $this->setCredor($this->row['credor']);
            $this->setClassificacaoCredor($this->row['classificacao_credor']);
            $this->setDocumento($this->row['documento']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_conta_pagar' => addslashes($this->getIdContaPagar()),
            'valor' => addslashes($this->getValor()),
            'valor_liquido' => addslashes($this->getValorLiquido()),
            'data_lancamento' => addslashes($this->getDataLancamento()),
            'data_vencimento' => addslashes($this->getDataVencimento()),
            'credor' => addslashes($this->getCredor()),
            'classificacao_credor' => addslashes($this->getClassificacaoCredor()),
            'documento' => addslashes($this->getDocumento()),
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
        $this->setQUERY("UPDATE contabil_contas_pagar SET " . implode(", ", ($camposUpdate)) . " WHERE id_conta_pagar = {$this->getIdContabilContasPagar()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_contas_pagar ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdContabilContasPagar(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_contas_pagar SET status = 0 WHERE id_conta_pagar = {$this->getIdContabilContasPagar()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_contas_pagar WHERE id_conta_pagar = {$this->getIdContabilContasPagar()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

}