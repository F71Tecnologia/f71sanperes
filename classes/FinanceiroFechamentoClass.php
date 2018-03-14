<?php

class FinanceiroFechamentoClass { 

    protected $id_fechamento;
    protected $id_projeto;
    protected $mes_fechado;
    protected $ano_fechado;
    protected $usuario;
    protected $fechado_em;
    protected $status;
    protected $lancamento;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' financeiro_fechamento ';
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
    function getIdFechamento() {
        return $this->id_fechamento;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getMesFechado() {
        return $this->mes_fechado;
    }

    function getAnoFechado() {
        return $this->ano_fechado;
    }

    function getUsuario() {
        return $this->usuario;
    }

    function getFechadoEm() {
        return $this->fechado_em;
    }

    function getStatus() {
        return $this->status;
    }

    function getLancamento() {
        return $this->lancamento;
    }

    //SET's DA CLASSE
    function setIdFechamento($id_fechamento) {
        $this->id_fechamento = $id_fechamento;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setMesFechado($mes_fechado) {
        $this->mes_fechado = $mes_fechado;
    }

    function setAnoFechado($ano_fechado) {
        $this->ano_fechado = $ano_fechado;
    }

    function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    function setFechadoEm($fechado_em) {
        $this->fechado_em = $fechado_em;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setLancamento($lancamento) {
        $this->lancamento = $lancamento;
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
        $this->id_fechamento = null;
        $this->id_projeto = null;
        $this->mes_fechado = null;
        $this->ano_fechado = null;
        $this->usuario = null;
        $this->fechado_em = null;
        $this->status = null;
        $this->lancamento = null;
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
        $this->setFROM(' financeiro_fechamento ');
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
            $this->setIdFechamento($this->row['id_fechamento']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setMesFechado($this->row['mes_fechado']);
            $this->setAnoFechado($this->row['ano_fechado']);
            $this->setUsuario($this->row['usuario']);
            $this->setFechadoEm($this->row['fechado_em']);
            $this->setStatus($this->row['status']);
            $this->setLancamento($this->row['lancamento']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_projeto' => addslashes($this->getIdProjeto()),
            'mes_fechado' => addslashes($this->getMesFechado()),
            'ano_fechado' => addslashes($this->getAnoFechado()),
            'usuario' => addslashes($this->getUsuario()),
            'fechado_em' => addslashes($this->getFechadoEm()),
            'status' => addslashes($this->getStatus()),
            'lancamento' => addslashes($this->getLancamento()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE financeiro_fechamento SET " . implode(", ", ($camposUpdate)) . " WHERE id_fechamento = {$this->getIdFechamento()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO financeiro_fechamento ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdFechamento(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE financeiro_fechamento SET status = 0 WHERE id_fechamento = {$this->getIdFechamento()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM financeiro_fechamento WHERE id_fechamento = {$this->getIdFechamento()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function ver_periodos($id_regiao ) {
        
        $sql = "SELECT CONCAT(A.id_projeto, MONTH(A.data_vencimento), YEAR(A.data_vencimento), '1') id, COUNT(A.id_saida) qtde, A.id_projeto projeto_id, B.nome, MONTH(A.data_vencimento) mes_lanc, YEAR(A.data_vencimento) ano_lanc, 'despesa' lancamento
                FROM saida A 
                INNER JOIN rhempresa B ON (B.id_projeto = A.id_projeto)
                WHERE A.id_regiao = '{$id_regiao}' AND A.`status` = 2 AND A.trava_contabil = 0
                GROUP BY ano_lanc, mes_lanc, projeto_id 
                UNION ALL 
                SELECT  CONCAT(C.id_projeto, MONTH(C.data_vencimento), YEAR(C.data_vencimento), '2') id, COUNT(C.id_entrada) qtde, C.id_projeto projeto_id, D.nome, MONTH(C.data_vencimento) mes_lanc, YEAR(C.data_vencimento) ano_lanc, 'receita' lancamento 
                FROM entrada C 
                INNER JOIN rhempresa D ON (D.id_projeto = C.id_projeto)
                WHERE C.id_regiao = '{$id_regiao}' AND C.`status` = 2 AND C.trava_contabil = 0
                GROUP BY ano_lanc, mes_lanc, projeto_id 
                ORDER BY ano_lanc DESC, mes_lanc DESC, projeto_id ASC, lancamento ASC";
        
        $qry = mysql_query($sql);
        while ($row = mysql_fetch_assoc($qry)) {
            $return[] = $row;
        }
        return $return;
    }
}