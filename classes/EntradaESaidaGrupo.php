<?php

class EntradaESaidaGrupoClass {

    protected $id_grupo;
    protected $nome_grupo;
    protected $status_grupo;
    protected $terceiro;
    protected $id_user;
    protected $data_cad;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' entradaesaida_grupo ';
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
    function getIdGrupo() {
        return $this->id_grupo;
    }

    function getNomeGrupo() {
        return $this->nome_grupo;
    }

    function getStatusGrupo() {
        return $this->status_grupo;
    }

    function getTerceiro() {
        return $this->terceiro;
    }

    function getIdUser() {
        return $this->id_user;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    //SET's DA CLASSE
    function setIdGrupo($id_grupo) {
        $this->id_grupo = $id_grupo;
    }

    function setNomeGrupo($nome_grupo) {
        $this->nome_grupo = $nome_grupo;
    }

    function setStatusGrupo($status_grupo) {
        $this->status_grupo = $status_grupo;
    }

    function setTerceiro($terceiro) {
        $this->terceiro = $terceiro;
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
        $this->id_grupo = null;
        $this->nome_grupo = null;
        $this->status_grupo = null;
        $this->terceiro = null;
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
        $this->setFROM(' entradaesaida_grupo ');
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
            $this->setIdGrupo($this->row['id_grupo']);
            $this->setNomeGrupo($this->row['nome_grupo']);
            $this->setStatusGrupo($this->row['status_grupo']);
            $this->setTerceiro($this->row['terceiro']);
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
            'nome_grupo' => addslashes($this->getNomeGrupo()),
            'status_grupo' => addslashes($this->getStatusGrupo()),
            'terceiro' => addslashes($this->getTerceiro()),
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
        $this->setQUERY("UPDATE entradaesaida_grupo SET " . implode(", ", ($camposUpdate)) . " WHERE id_grupo = {$this->getIdGrupo()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO entradaesaida_grupo ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdGrupo(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE entradaesaida_grupo SET status_grupo = 0 WHERE id_grupo = {$this->getIdGrupo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM entradaesaida_grupo WHERE id_grupo = {$this->getIdGrupo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getById() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE id_grupo = {$this->getIdGrupo()}");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function listaGrupos() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE status_grupo = 1");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getSelect() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE status_grupo = 1 ORDER BY id_grupo");
        if ($this->setRs()) {
            $arrr = ['-1' => 'Selecione'];
            while ($this->getRow()) {
                $arrr[$this->getIdGrupo()] = $this->getIdGrupo() . ' - ' . $this->getNomeGrupo();
            }
            return $arrr;
        } else {
            die(mysql_error());
        }
    }

}
