<?php

class ProtocolosArquivosClass {

    protected $id_protocolos_arquivos;
    protected $nome_arquivo;
    protected $id_protocolos_entregas;
    protected $data_cad;
    protected $status;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' protocolos_arquivos ';
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
    function getIdProtocolosArquivos() {
        return $this->id_protocolos_arquivos;
    }

    function getNomeArquivo() {
        return $this->nome_arquivo;
    }

    function getIdProtocolosEntregas() {
        return $this->id_protocolos_entregas;
    }

    function getDataCad() {
        return $this->data_cad;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdProtocolosArquivos($id_protocolos_arquivos) {
        $this->id_protocolos_arquivos = $id_protocolos_arquivos;
    }

    function setNomeArquivo($nome_arquivo) {
        $this->nome_arquivo = $nome_arquivo;
    }

    function setIdProtocolosEntregas($id_protocolos_entregas) {
        $this->id_protocolos_entregas = $id_protocolos_entregas;
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
        $this->id_protocolos_arquivos = null;
        $this->nome_arquivo = null;
        $this->id_protocolos_entregas = null;
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
        $this->setFROM(' protocolos_arquivos ');
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
            $this->setIdProtocolosArquivos($this->row['id_protocolos_arquivos']);
            $this->setNomeArquivo($this->row['nome_arquivo']);
            $this->setIdProtocolosEntregas($this->row['id_protocolos_entregas']);
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
            'nome_arquivo' => addslashes($this->getNomeArquivo()),
            'id_protocolos_entregas' => addslashes($this->getIdProtocolosEntregas()),
            'data_cad' => addslashes($this->getDataCad()),
            'status' => addslashes($this->getStatus()),
        );

        return array_filter($array);
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE protocolos_arquivos SET " . implode(", ", ($camposUpdate)) . " WHERE id_protocolos_arquivos = {$this->getIdProtocolosArquivos()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO protocolos_arquivos ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdProtocolosArquivos(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE protocolos_arquivos SET status = 0 WHERE id_protocolos_arquivos = {$this->getIdProtocolosArquivos()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM protocolos_arquivos WHERE id_protocolos_arquivos = {$this->getIdProtocolosArquivos()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function salvar() {
        if (empty($this->id_protocolos_arquivos)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function getProtocoloArquivoById() {
        $this->setWHERE("id_protocolos_arquivos = " . $this->getIdProtocolosArquivos());
        $this->setRs();
        $this->getRow();
    }
    
    public function listaByIdProtocolosEntregas() {
            $this->status = 1;
            $this->setWHERE("id_protocolos_entregas = '{$this->id_protocolos_entregas}' AND status = 1");
            $this->setRs();
            $arr = NULL;
            while ($this->getRow()) {
                $arr[] = [
                    'id_protocolos_arquivos' => $this->getIdProtocolosArquivos(),
                    'id_protocolos_entregas' => $this->getIdProtocolosEntregas(),
                    'nome' => $this->getNomeArquivo(),
                    'status' => $this->getStatus(),
                    'data_cad' => $this->getDataCad(),
                ];
            }
            return empty($arr) ? FALSE : $arr;
    }

}
