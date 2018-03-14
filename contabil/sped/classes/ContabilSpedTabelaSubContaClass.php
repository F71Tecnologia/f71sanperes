<?php
class ContabilSpedTabelaSubContaClass { 

    protected $id;
    protected $num;
    protected $descricao;
    protected $fundamento_legal;
    protected $conta_principal;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_sped_tabela_sub_conta ';
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

    function getNum() {
        return $this->num;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getFundamentoLegal() {
        return $this->fundamento_legal;
    }

    function getContaPrincipal() {
        return $this->conta_principal;
    }

    //SET's DA CLASSE
    function setId($id) {
        $this->id = $id;
    }

    function setNum($num) {
        $this->num = $num;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setFundamentoLegal($fundamento_legal) {
        $this->fundamento_legal = $fundamento_legal;
    }

    function setContaPrincipal($conta_principal) {
        $this->conta_principal = $conta_principal;
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
        $this->num = null;
        $this->descricao = null;
        $this->fundamento_legal = null;
        $this->conta_principal = null;
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
        $this->setFROM(' contabil_sped_tabela_sub_conta ');
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
            $this->setNum($this->row['num']);
            $this->setDescricao($this->row['descricao']);
            $this->setFundamentoLegal($this->row['fundamento_legal']);
            $this->setContaPrincipal($this->row['conta_principal']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'num' => addslashes($this->getNum()),
            'descricao' => addslashes($this->getDescricao()),
            'fundamento_legal' => addslashes($this->getFundamentoLegal()),
            'conta_principal' => addslashes($this->getContaPrincipal()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_sped_tabela_sub_conta SET " . implode(", ", ($camposUpdate)) . " WHERE id = {$this->getId()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_sped_tabela_sub_conta ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setId(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_sped_tabela_sub_conta SET status = 0 WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_sped_tabela_sub_conta WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}