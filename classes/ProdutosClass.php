<?php

class ProdutosClass {

    protected $id_prod;
    protected $id_fornecedor;
    protected $emit_cnpj;
    protected $cProd;
    protected $cEAN;
    protected $xProd;
    protected $NCM;
    protected $EXTIPI;
    protected $uCom;
    protected $vUnCom;
    protected $cEANTrib;
    protected $uTrib;
    protected $status;
    protected $tipo;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' nfe_produtos ';
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
    function getIdProd() {
        return $this->id_prod;
    }

    function getIdFornecedor() {
        return $this->id_fornecedor;
    }

    function getEmitCnpj($limpo = false) {
        return ($limpo) ? str_replace(array('.', '-', '/'), '', $this->emit_cnpj) : $this->emit_cnpj;
    }

    function getCProd() {
        return $this->cProd;
    }

    function getCEAN() {
        return $this->cEAN;
    }

    function getXProd() {
        return $this->xProd;
    }

    function getNCM() {
        return $this->NCM;
    }

    function getEXTIPI() {
        return $this->EXTIPI;
    }

    function getUCom() {
        return $this->uCom;
    }

    function getVUnCom() {
        return $this->vUnCom;
    }

    function getCEANTrib() {
        return $this->cEANTrib;
    }

    function getUTrib() {
        return $this->uTrib;
    }

    function getStatus() {
        return $this->status;
    }

    function getTipo() {
        return $this->tipo;
    }

    //SET's DA CLASSE
    function setIdProd($id_prod) {
        $this->id_prod = $id_prod;
    }

    function setIdFornecedor($id_fornecedor) {
        $this->id_fornecedor = $id_fornecedor;
    }

    function setEmitCnpj($emit_cnpj) {
        $this->emit_cnpj = str_replace('/', '', str_replace('.', '', str_replace('-', '', $emit_cnpj)));
    }

    function setCProd($cProd) {
        $this->cProd = $cProd;
    }

    function setCEAN($cEAN) {
        $this->cEAN = $cEAN;
    }

    function setXProd($xProd) {
        $this->xProd = $xProd;
    }

    function setNCM($NCM) {
        $this->NCM = $NCM;
    }

    function setEXTIPI($EXTIPI) {
        $this->EXTIPI = $EXTIPI;
    }

    function setUCom($uCom) {
        $this->uCom = $uCom;
    }

    function setVUnCom($vUnCom) {
        $this->vUnCom = $vUnCom;
    }

    function setCEANTrib($cEANTrib) {
        $this->cEANTrib = $cEANTrib;
    }

    function setUTrib($uTrib) {
        $this->uTrib = $uTrib;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
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
        $this->id_prod = null;
        $this->id_fornecedor = null;
        $this->emit_cnpj = null;
        $this->cProd = null;
        $this->cEAN = null;
        $this->xProd = null;
        $this->NCM = null;
        $this->EXTIPI = null;
        $this->uCom = null;
        $this->vUnCom = null;
        $this->cEANTrib = null;
        $this->uTrib = null;
        $this->status = null;
        $this->tipo = null;
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
        $this->setFROM(' nfe_produtos ');
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
            $this->setIdProd($this->row['id_prod']);
            $this->setIdFornecedor($this->row['id_fornecedor']);
            $this->setEmitCnpj($this->row['emit_cnpj']);
            $this->setCProd($this->row['cProd']);
            $this->setCEAN($this->row['cEAN']);
            $this->setXProd($this->row['xProd']);
            $this->setNCM($this->row['NCM']);
            $this->setEXTIPI($this->row['EXTIPI']);
            $this->setUCom($this->row['uCom']);
            $this->setVUnCom($this->row['vUnCom']);
            $this->setCEANTrib($this->row['cEANTrib']);
            $this->setUTrib($this->row['uTrib']);
            $this->setStatus($this->row['status']);
            $this->setTipo($this->row['tipo']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_fornecedor' => addslashes($this->getIdFornecedor()),
            'emit_cnpj' => addslashes($this->getEmitCnpj()),
            'cProd' => addslashes($this->getCProd()),
            'cEAN' => addslashes($this->getCEAN()),
            'xProd' => addslashes($this->getXProd()),
            'NCM' => addslashes($this->getNCM()),
            'EXTIPI' => addslashes($this->getEXTIPI()),
            'uCom' => addslashes($this->getUCom()),
            'vUnCom' => addslashes($this->getVUnCom()),
            'cEANTrib' => addslashes($this->getCEANTrib()),
            'uTrib' => addslashes($this->getUTrib()),
            'status' => addslashes($this->getStatus()),
            'tipo' => addslashes($this->getTipo()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE nfe_produtos SET " . implode(", ", ($camposUpdate)) . " WHERE id_prod = {$this->getIdProd()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO nfe_produtos ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdProd(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE nfe_produtos SET status = 0 WHERE id_prod = {$this->getIdProd()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM nfe_produtos WHERE id_prod = {$this->getIdProd()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function getProdutos($toArray = false) {
        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
        }

        $this->limpaQuery();
        $this->setWHERE(implode(' AND ', $condicoes));
        $this->setORDER("xProd ASC");

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
        $this->setWHERE("id_prod = {$this->getIdProd()} AND status = 1");
        $this->setLIMIT(1);
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->getIdProd())) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function listaProdutos($cnpj, $tipo) {
        $query = "SELECT * FROM nfe_produtos 
                    WHERE REPLACE(REPLACE(REPLACE(emit_cnpj,'.',''),'-',''),'/','') = '$cnpj' AND tipo = '$tipo'
                    GROUP BY id_prod";
        $this->setQUERY($query);
        if ($this->setRs()) {
            while ($row = mysql_fetch_assoc($this->rs)) {
                $arrayX[] = $row;
            }
            return $arrayX;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

}
