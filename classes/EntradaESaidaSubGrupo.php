<?php

class EntradaESaidaSubGrupoClass { 

    protected $id;
    protected $id_subgrupo;
    protected $entradaesaida_grupo;
    protected $nome;
    protected $status;
    protected $id_user;
    protected $data_cad;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' entradaesaida_subgrupo ';
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

    function getIdSubgrupo() {
        return $this->id_subgrupo;
    }

    function getEntradaesaidaGrupo() {
        return $this->entradaesaida_grupo;
    }

    function getNome() {
        return $this->nome;
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
    function setId($id) {
        $this->id = $id;
    }

    function setIdSubgrupo($id_subgrupo) {
        $this->id_subgrupo = $id_subgrupo;
    }

    function setEntradaesaidaGrupo($entradaesaida_grupo) {
        $this->entradaesaida_grupo = $entradaesaida_grupo;
    }

    function setNome($nome) {
        $this->nome = $nome;
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
        $this->id = null;
        $this->id_subgrupo = null;
        $this->entradaesaida_grupo = null;
        $this->nome = null;
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
        $this->setFROM(' entradaesaida_subgrupo ');
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
            $this->setIdSubgrupo($this->row['id_subgrupo']);
            $this->setEntradaesaidaGrupo($this->row['entradaesaida_grupo']);
            $this->setNome($this->row['nome']);
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
            'id_subgrupo' => addslashes($this->getIdSubgrupo()),
            'entradaesaida_grupo' => addslashes($this->getEntradaesaidaGrupo()),
            'nome' => addslashes($this->getNome()),
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
        $this->setQUERY("UPDATE entradaesaida_subgrupo SET " . implode(", ", ($camposUpdate)) . " WHERE id = {$this->getId()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO entradaesaida_subgrupo ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setId(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE entradaesaida_subgrupo SET status = 0 WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM entradaesaida_subgrupo WHERE id = {$this->getId()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }



public function getById() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE id = {$this->getId()}");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function listaSubGrupos() {
        $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE status = 1 ORDER BY id_subgrupo");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function getSelect(){
         $this->limpaQuery();

        $this->setQUERY("SELECT * FROM {$this->FROM} WHERE status = 1 ORDER BY id_subgrupo");
        if ($this->setRs()) {
            $arrr = ['-1' => 'Selecione'];
            while ($this->getRow()) {
                $arrr[$this->getId()] = $this->getIdSubgrupo(). ' - ' . $this->getNome();
            }
            return $arrr;
        } else {
            die(mysql_error());
        }
    }
}