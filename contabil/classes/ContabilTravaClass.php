<?php
class ContabilTravaClass { 

    protected $id_trava;
    protected $periodo;
    protected $id_projeto;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_trava ';
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
    function getIdTrava() {
        return $this->id_trava;
    }

    function getPeriodo() {
        return $this->periodo;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    //SET's DA CLASSE
    function setIdTrava($id_trava) {
        $this->id_trava = $id_trava;
    }

    function setPeriodo($periodo) {
        $this->periodo = $periodo;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
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
        $this->id_trava = null;
        $this->periodo = null;
        $this->id_projeto = null;
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
        $this->setFROM(' contabil_trava ');
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
            $this->setIdTrava($this->row['id_trava']);
            $this->setPeriodo($this->row['periodo']);
            $this->setIdProjeto($this->row['id_projeto']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'periodo' => addslashes($this->getPeriodo()),
            'id_projeto' => addslashes($this->getIdProjeto()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_trava SET " . implode(", ", ($camposUpdate)) . " WHERE id_trava = {$this->getIdTrava()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_trava ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdTrava(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_trava SET status = 0 WHERE id_trava = {$this->getIdTrava()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();
        $this->setQUERY("DELETE FROM contabil_trava WHERE id_trava = {$this->getIdTrava()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function retornaTrava($projeto, $inicio, $final) {
        $ini = implode('-',array_reverse(explode('/', $inicio)));
        $fin  = implode('-',array_reverse(explode('/', $final))); 

        $sql = "SELECT B.id_trava, B.id_projeto, B.periodo, RPAD(REPLACE(REPLACE(A.data_lancamento,'-',''),'-',''),6,0) indice
                FROM contabil_lancamento A
                LEFT JOIN contabil_trava B ON B.id_projeto = A.id_projeto AND B.periodo = RPAD(REPLACE(REPLACE(A.data_lancamento,'-',''),'-',''),6,0) 
                WHERE A.id_projeto = '$projeto' AND A.`status` != 0  AND A.data_lancamento BETWEEN '$ini' AND '$fin'
                GROUP BY indice
                ORDER BY indice DESC";
        $result = mysql_query($sql) or die('Erro: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
             $l[] = $row;
            
        }
        return $l;
    }

    
    
    
        }

