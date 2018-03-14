<?php

class PedidosTipoClass { 

    protected $id_tipo;
    protected $descricao;
    protected $id_cnae;
    protected $id_tipo_saida;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' pedidos_tipo ';
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
    function getIdTipo() {
        return $this->id_tipo;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getIdCnae() {
        return $this->id_cnae;
    }

    function getIdTipoSaida() {
        return $this->id_tipo_saida;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdTipo($id_tipo) {
        $this->id_tipo = $id_tipo;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setIdCnae($id_cnae) {
        $this->id_cnae = $id_cnae;
    }

    function setIdTipoSaida($id_tipo_saida) {
        $this->id_tipo_saida = $id_tipo_saida;
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
        $this->setFROM(' pedidos_tipo ');
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
            $this->setIdTipo($this->row['id_tipo']);
            $this->setDescricao($this->row['descricao']);
            $this->setIdCnae($this->row['id_cnae']);
            $this->setIdTipoSaida($this->row['id_tipo_saida']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'descricao' => addslashes($this->getDescricao()),
            'id_cnae' => addslashes($this->getIdCnae()),
            'id_tipo_saida' => addslashes($this->getIdTipoSaida()),
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
        $this->setQUERY("UPDATE pedidos_tipo SET " . implode(", ", ($camposUpdate)) . " WHERE id_tipo = {$this->getIdTipo()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO pedidos_tipo ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdTipo(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE pedidos_tipo SET status = 0 WHERE id_tipo = {$this->getIdTipo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM pedidos_tipo WHERE id_tipo = {$this->getIdTipo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function getPedidosTipo($toArray = false) {
        $array = $this->makeCampos();
        
        $array = array_filter($array);
        
        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
        }
        
        $this->limpaQuery();
        $this->setWHERE(implode(' AND ', $condicoes));
        $this->setORDER("descricao ASC");

        if ($this->setRs()) {
            if($toArray){
                while($row = mysql_fetch_assoc($this->rs)){
                    $arrayX[] = $row;
                }
                return $arrayX;
            } else {
                return 1;
            }
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }
    
    
    public function getById() {
        $this->limpaQuery();
        $this->setWHERE("id_tipo = {$this->getIdTipo()} AND status = 1");
        $this->setLIMIT(1);
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function salvar(){
        if(empty($this->getIdProd())){
            return $this->insert();
        }else{
            return $this->update();
        }
    }

}

