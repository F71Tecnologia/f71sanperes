<?php

class ProdutoFornecedorAssocClass {

    protected $id_assoc;
    protected $id_produto;
    protected $id_fornecedor;
    protected $valor_produto;
    protected $data_cad;
    protected $status;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' produto_fornecedor_assoc ';
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
    function getIdAssoc() {
        return $this->id_assoc;
    }

    function getIdProduto() {
        return $this->id_produto;
    }

    function getIdFornecedor() {
        return $this->id_fornecedor;
    }

    function getValorProduto() {
        return $this->valor_produto;
    }

    function getDataCad() {
        return $this->data_cad;
    }
    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdAssoc($id_assoc) {
        $this->id_assoc = $id_assoc;
    }

    function setIdProduto($id_produto) {
        $this->id_produto = $id_produto;
    }

    function setIdFornecedor($id_fornecedor) {
        $this->id_fornecedor = $id_fornecedor;
    }

    function setValorProduto($valor_produto) {
        $this->valor_produto = $valor_produto;
    }

    function setDataCad($data_cad) {
        $this->data_cad = $data_cad;
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
        $this->id_assoc = null;
        $this->id_produto = null;
        $this->id_fornecedor = null;
        $this->valor_produto = null;
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
        $this->setFROM(' produto_fornecedor_assoc ');
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
            $this->setIdAssoc($this->row['id_assoc']);
            $this->setIdProduto($this->row['id_produto']);
            $this->setIdFornecedor($this->row['id_fornecedor']);
            $this->setValorProduto($this->row['valor_produto']);
            $this->setDataCad($this->row['data_cad']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_produto' => addslashes($this->getIdProduto()),
            'id_fornecedor' => addslashes($this->getIdFornecedor()),
            'valor_produto' => addslashes($this->getValorProduto()),
            'data_cad' => addslashes($this->getDataCad()),
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
        $this->setQUERY("UPDATE produto_fornecedor_assoc SET " . implode(", ", ($camposUpdate)) . " WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO produto_fornecedor_assoc ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdAssoc(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();
        $this->setQUERY("UPDATE produto_fornecedor_assoc SET status = 0 WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM produto_fornecedor_assoc WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getAssoc($toArray = false) {
        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
        }

        $this->limpaQuery();
        $this->setWHERE(implode(' AND ', $condicoes));


        if ($this->setRs()) {
            if ($toArray) {
                while ($row = mysql_fetch_assoc($this->rs)) {
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
        $this->setWHERE("id_assoc = {$this->getIdProd()}");
        $this->setLIMIT(1);
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->getIdAssoc())) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    // pega campos das duas tabelas refentes
    public function arrayAssoc() {
        $this->setStatus(1);
        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "b.$key = '$value'";
        }

        $this->setWHERE(implode(' AND ', $condicoes));
        
        $auxWhere = (!empty($this->WHERE)) ? " WHERE $this->WHERE " : null;
       $query = "SELECT b.*,d.nome AS projeto_nome
                        FROM produto_fornecedor_assoc AS b 
                        INNER JOIN prestadorservico AS c ON (b.id_fornecedor = c.id_prestador AND prestador_tipo = 1)
                        INNER JOIN projeto AS d ON c.id_projeto = d.id_projeto
                        $auxWhere";
        $this->setQUERY($query);
        if ($this->setRs()) {
            while ($row = mysql_fetch_assoc($this->rs)) {
                $arrayX[] = $row;
            }
            return $arrayX;
        } else {
            return 0;
        }
    }

}
