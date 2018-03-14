<?php

class ContabilSpedTabelaCodClass { 

    protected $id;
    protected $reg;
    protected $campo;
    protected $codigo;
    protected $descricao;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_sped_tabela_cod ';
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

    function getReg() {
        return $this->reg;
    }

    function getCampo() {
        return $this->campo;
    }

    function getCodigo() {
        return $this->codigo;
    }

    function getDescricao() {
        return $this->descricao;
    }

    //SET's DA CLASSE
    function setId($id) {
        $this->id = $id;
    }

    function setReg($reg) {
        $this->reg = $reg;
    }

    function setCampo($campo) {
        $this->campo = $campo;
    }

    function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
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
        $this->reg = null;
        $this->campo = null;
        $this->codigo = null;
        $this->descricao = null;
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
        $this->setFROM(' contabil_sped_tabela_cod ');
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
            $this->setReg($this->row['reg']);
            $this->setCampo($this->row['campo']);
            $this->setCodigo($this->row['codigo']);
            $this->setDescricao($this->row['descricao']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'reg' => addslashes($this->getReg()),
            'campo' => addslashes($this->getCampo()),
            'codigo' => addslashes($this->getCodigo()),
            'descricao' => addslashes($this->getDescricao()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_sped_tabela_cod SET " . implode(", ", ($camposUpdate)) . " WHERE id = {$this->getId()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_sped_tabela_cod ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setId(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_sped_tabela_cod SET status = 0 WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_sped_tabela_cod WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}