<?php

class ContabilImposto {

    protected $id_imposto;
    protected $sigle;
    protected $nome;
    protected $abrangencia;
    protected $data_cad;
    protected $status = '1';
    
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " contabil_impostos ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    protected $rsImposto;
    protected $rowImposto;
    protected $numRowsImposto;

    function __construct() {
        
    }

    function getIdImposto() {
        return $this->id_imposto;
    }

    function getSigla() {
        return $this->sigla;
    }

    function getNome() {
        return $this->nome;
    }

    function getAbrangencia() {
        return $this->abrangencia;
    }

    function getStatus() {
        return $this->status;
    }

    function getDataCad($formato = 'd/m/Y') {
        if (empty($this->data_cad) || $this->data_cad == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_cad), $formato) : $this->data_cad;
        }
    }

    function setidImposto($id_imposto) {
        $this->id_imposto = $id_imposto;
    }

    function setSigla($sigla) {
        $this->sigla = $sigla;
    }
    function setNome($nome) {
        $this->nome = $nome;
    }

    function setAbrangencia($abrangencia) {
        $this->abrangencia = $abrangencia;
    }

    function setStatus($status) {
        $this->status = $status;
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

    protected function setRsImposto($valor) {
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
//        echo $sql;
        $this->rsImposto = mysql_query($sql);
        $this->numRowsImposto = mysql_num_rows($this->rsImposto);
        return $this->rsImposto;
    }

    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" contabil_impostos ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }

    public function getNumRowImposto() {
        return $this->numRowsImposto;
    }

    protected function setRowImposto($valor) {
        return $this->rowImposto = mysql_fetch_assoc($valor);
    }

    public function getRowImposto() {

        if ($this->setRowImposto($this->rsImposto)) {

            $this->setidImposto($this->rowImposto['id_imposto']);
            $this->setSigla($this->rowImposto['sigla']);
            $this->setNome($this->rowImposto['nome']);
            $this->setAbrangencia($this->rowImposto['abrangencia']);
            $this->setDataCad($this->rowImposto['data_cad']);
            $this->setStatus($this->rowImposto['status']);

            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    public function getImpostos() {
        $this->limpaQuery();
        $auxPrestador = (!empty($this->getidImposto())) ? " AND id_imposto = {$this->getIdImposto()} " : null;

        $this->setWHERE("status=1 $auxImposto $auxPrestador");

        if ($this->setRsImposto()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function insertImposto() {
        $this->limpaQuery();

        $array['id_imposto'] = $this->getidImposto();
        $array['sigla'] = $this->getSigla();
        $array['nome'] = $this->getNome();
        $array['abrangencia'] = $this->getAbrangencia();
        $array['data_cad'] = $this->getDataCad();
        $array['status'] = $this->getStatus();

        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);

        $this->setQUERY("INSERT INTO contabil_impostos ($keys) VALUES ('$values');");

//        echo $sql = "$this->QUERY";
//        echo $this->QUERY."<br>";
        if ($this->setRsImposto()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function updateImposto() {
        $this->limpaQuery();

        $array['id_imposto'] = $this->getidImposto();
        $array['sigla'] = $this->getSigla();
        $array['nome'] = $this->getNome();
        $array['abrangencia'] = $this->getAbrangencia();
        $array['data_cad'] = $this->getDataCad();
        $array['status'] = $this->getStatus();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }

        $this->setQUERY("UPDATE contabil_impostos SET " . implode(", ", ($camposUpdate)) . " WHERE id_assoc = {$this->getIdDependente()} LIMIT 1;");
//        echo $this->QUERY."<br>";
        if ($this->setRsImposto()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function removerDependente() {
        $QUERY = "UPDATE contabil_impostos SET status = 0 WHERE id_assoc = {$this->getIdDependente()} LIMIT 1;";
        $this->setQUERY($QUERY);
        if ($this->setRsImposto()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

}
