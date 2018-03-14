<?php

class NFSseSolicitacaoCorrecaoClass {

    protected $id_correcao;
    protected $id_nfse;
    protected $id_regiao;
    protected $motivo;
    protected $data_cad;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' nfse_solicitacao_correcao ';
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
    function getIdCorrecao() {
        return $this->id_correcao;
    }

    function getIdNfse() {
        return $this->id_nfse;
    }

    function getIdRegiao() {
        return $this->id_regiao;
    }

    function getMotivo() {
        return $this->motivo;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdCorrecao($id_correcao) {
        $this->id_correcao = $id_correcao;
    }

    function setIdNfse($id_nfse) {
        $this->id_nfse = $id_nfse;
    }

    function setIdRegiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setMotivo($motivo) {
        $this->motivo = $motivo;
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
        $this->id_correcao = null;
        $this->id_nfse = null;
        $this->id_regiao = null;
        $this->motivo = null;
        $this->data_cad = null;
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
        $this->setFROM(' nfse_solicitacao_correcao ');
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
            $this->setIdCorrecao($this->row['id_correcao']);
            $this->setIdNfse($this->row['id_nfse']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setMotivo($this->row['motivo']);
            $this->setDataCad($this->row['data_cad']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_nfse' => addslashes($this->getIdNfse()),
            'id_regiao' => addslashes($this->getIdRegiao()),
            'motivo' => addslashes($this->getMotivo()),
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
        $this->setQUERY("UPDATE nfse_solicitacao_correcao SET " . implode(", ", ($camposUpdate)) . " WHERE id_correcao = {$this->getIdCorrecao()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO nfse_solicitacao_correcao ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdCorrecao(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE nfse_solicitacao_correcao SET status = 0 WHERE id_correcao = {$this->getIdCorrecao()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM nfse_solicitacao_correcao WHERE id_correcao = {$this->getIdCorrecao()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function listar() {
        $this->setStatus(1);
        $this->limpaQuery();

        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
}

        $this->setWHERE(implode(' AND ', $condicoes));
        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->id_correcao)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function listarJoinNFSe() {
        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "a.$key = '$value'";
        }
$condicoes[] = "b.status = 1";

        $where = (count($condicoes) > 0) ? "WHERE " . implode(' AND ', $condicoes) : "";
        $query = "SELECT *,DATE_FORMAT(Competencia,'%d/%m/%Y') AS Competencia , c.c_razao AS nome_prestador
            FROM nfse_solicitacao_correcao AS a 
            INNER JOIN nfse AS b ON a.id_nfse = b.id_nfse 
            INNER JOIN (SELECT id_prestador,c_razao FROM prestadorservico) AS c ON b.PrestadorServico = c.id_prestador
            $where ORDER BY a.data_cad";
        $result = mysql_query($query);
        $array1 = array();
        while ($row = mysql_fetch_assoc($result)) {
            $array1[] = $row;
        }
        
        return $array1;
    }

}