<?php 
class SaidaImpostosRetidoClass { 

    protected $id_assoc;
    protected $id_saida;
    protected $id_saida_ref;
    protected $id_retencao;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' saida_impostos_retido ';
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

    function getIdSaida() {
        return $this->id_saida;
    }

    function getIdSaidaRef() {
        return $this->id_saida_ref;
    }

    function getIdRetencao() {
        return $this->id_retencao;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdAssoc($id_assoc) {
        $this->id_assoc = $id_assoc;
    }

    function setIdSaida($id_saida) {
        $this->id_saida = $id_saida;
    }

    function setIdSaidaRef($id_saida_ref) {
        $this->id_saida_ref = $id_saida_ref;
    }

    function setIdRetencao($id_retencao) {
        $this->id_retencao = $id_retencao;
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
        $this->id_saida = null;
        $this->id_saida_ref = null;
        $this->id_retencao = null;
        $this->status = null;
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
        $this->setFROM(' saida_impostos_retido ');
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
            $this->setIdSaida($this->row['id_saida']);
            $this->setIdSaidaRef($this->row['id_saida_ref']);
            $this->setIdRetencao($this->row['id_retencao']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_saida' => addslashes($this->getIdSaida()),
            'id_saida_ref' => addslashes($this->getIdSaidaRef()),
            'id_retencao' => addslashes($this->getIdRetencao()),
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
        $this->setQUERY("UPDATE saida_impostos_retido SET " . implode(", ", ($camposUpdate)) . " WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO saida_impostos_retido ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdAssoc(mysql_insert_id());
            return 1;
        } else {
//            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE saida_impostos_retido SET status = 0 WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM saida_impostos_retido WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getByIdSaidaRef() {
        $this->limpaQuery();

        $sql = "
        SELECT A.id_retencao, A.id_saida, B.status, B.n_documento, B.especifica
        FROM saida_impostos_retido A LEFT JOIN saida B ON (A.id_saida = B.id_saida) 
        WHERE A.status = 1 AND B.status != 0 AND A.id_saida_ref = {$this->getIdSaidaRef()}";
        $qry = mysql_query($sql);
        if ($qry) {
            while($row = mysql_fetch_assoc($qry)) { 
                $array[$row['id_retencao']] = $row;  
            }
            return $array;
        } else {
            die(mysql_error());
        }
    }

    public function getByIdSaida() {
        $this->limpaQuery();

        $sql = "
        SELECT A.id_retencao, A.id_saida, B.status, B.n_documento, B.especifica, B.valor_bruto, 
        TRUNCATE((IF(C.id_retencao_tipo = 3, B.valor_mao_obra, B.valor_bruto) * (C.valor / 100)),2) valor_retido, 
        D.nome nome_retencao, C.valor AS percent
        FROM saida_impostos_retido A 
        LEFT JOIN saida B ON (A.id_saida_ref = B.id_saida) 
        LEFT JOIN retencao C ON (A.id_retencao = C.id_retencao) 
        LEFT JOIN retencao_tipo D ON (D.id_retencao_tipo = C.id_retencao_tipo) 
        WHERE A.status = 1 AND A.id_saida = {$this->getIdSaida()}";
        $qry = mysql_query($sql);
        if ($qry) {
            while($row = mysql_fetch_assoc($qry)) {
                $array[] = $row;
            }
            return $array;
        } else {
            die(mysql_error());
        }
    }

    public function getValorRetencaoPaga() {
//        $this->limpaQuery();
//
//        $this->setQUERY("
//            SELECT 
//            ");
//        
//        if ($this->setRs()) {
//            while($this->getRow()) {
//                $array[$this->getIdRetencao()] = $this->getIdRetencao();
//            }
//            return $array;
//        } else {
//            die(mysql_error());
//        }
    }
}    
//    $objSaidaImpostosRetido->setIdAssoc($_REQUEST['id_assoc']);
//    $objSaidaImpostosRetido->setIdSaida($_REQUEST['id_saida']);
//    $objSaidaImpostosRetido->setIdRetencao($_REQUEST['id_retencao']);
//    $objSaidaImpostosRetido->setStatus($_REQUEST['status']);