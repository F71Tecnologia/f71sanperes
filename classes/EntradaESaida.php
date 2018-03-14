<?php

class EntradaESaidaClass {

    protected $id_entradasaida;
    protected $cod;
    protected $nome;
    protected $descricao;
    protected $tipo;
    protected $grupo;
    protected $extra;
    protected $id_subgrupo;
    protected $codigof71;
    protected $faturamento;
    protected $status;
    protected $id_user;
    protected $data_cad;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' entradaesaida ';
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
    function getIdEntradasaida() {
        return $this->id_entradasaida;
    }

    function getCod() {
        return $this->cod;
    }

    function getNome() {
        return $this->nome;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getGrupo() {
        return $this->grupo;
    }

    function getExtra() {
        return $this->extra;
    }

    function getIdSubgrupo() {
        return $this->id_subgrupo;
    }

    function getCodigof71() {
        return $this->codigof71;
    }

    function getFaturamento() {
        return $this->faturamento;
    }

    function getStatus() {
        return $this->status;
    }

    function getIdUser() {
        return $this->id_user;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    //SET's DA CLASSE
    function setIdEntradasaida($id_entradasaida) {
        $this->id_entradasaida = $id_entradasaida;
    }

    function setCod($cod) {
        $this->cod = $cod;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setGrupo($grupo) {
        $this->grupo = $grupo;
    }

    function setExtra($extra) {
        $this->extra = $extra;
    }

    function setIdSubgrupo($id_subgrupo) {
        $this->id_subgrupo = $id_subgrupo;
    }

    function setCodigof71($codigof71) {
        $this->codigof71 = $codigof71;
    }

    function setFaturamento($faturamento) {
        $this->faturamento = $faturamento;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setIdUser($id_user) {
        $this->id_user = $id_user;
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
        $this->id_entradasaida = null;
        $this->cod = null;
        $this->nome = null;
        $this->descricao = null;
        $this->tipo = null;
        $this->grupo = null;
        $this->extra = null;
        $this->id_subgrupo = null;
        $this->codigof71 = null;
        $this->faturamento = null;
        $this->status = null;
        $this->id_user = null;
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
        $this->setFROM(' entradaesaida ');
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
            $this->setIdEntradasaida($this->row['id_entradasaida']);
            $this->setCod($this->row['cod']);
            $this->setNome($this->row['nome']);
            $this->setDescricao($this->row['descricao']);
            $this->setTipo($this->row['tipo']);
            $this->setGrupo($this->row['grupo']);
            $this->setExtra($this->row['extra']);
            $this->setIdSubgrupo($this->row['id_subgrupo']);
            $this->setCodigof71($this->row['codigof71']);
            $this->setFaturamento($this->row['faturamento']);
            $this->setStatus($this->row['status']);
            $this->setIdUser($this->row['id_user']);
            $this->setDataCad($this->row['data_cad']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'cod' => addslashes($this->getCod()),
            'nome' => addslashes($this->getNome()),
            'descricao' => addslashes($this->getDescricao()),
            'tipo' => addslashes($this->getTipo()),
            'grupo' => addslashes($this->getGrupo()),
            'extra' => addslashes($this->getExtra()),
            'id_subgrupo' => addslashes($this->getIdSubgrupo()),
            'codigof71' => addslashes($this->getCodigof71()),
            'faturamento' => addslashes($this->getFaturamento()),
            'status' => addslashes($this->getStatus()),
            'id_user' => addslashes($this->getIdUser()),
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
        $this->setQUERY("UPDATE entradaesaida SET " . implode(", ", ($camposUpdate)) . " WHERE id_entradasaida = {$this->getIdEntradasaida()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO entradaesaida ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdEntradasaida(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE entradaesaida SET status = 0 WHERE id_entradasaida = {$this->getIdEntradasaida()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM entradaesaida WHERE id_entradasaida = {$this->getIdEntradasaida()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    public function getById() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE id_entradasaida = {$this->getIdEntradasaida()}");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function listaEntradaESaida() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE status = 1 ORDER BY grupo,id_subgrupo,cod");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

}
