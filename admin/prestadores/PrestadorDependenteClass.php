<?php

class PrestadorDependenteClass {

    protected $prestador_dep_id;
    protected $id_contabil_empresa;
    protected $prestador_dep_nome;
    protected $prestador_dep_parentesco;
    protected $prestador_dep_data_nasc;
    protected $prestador_dep_status;
    protected $prestador_dep_tel;
    protected $QUERY;
    protected $SELECT = " * ";
    protected $FROM = " prestador_dependente ";
    protected $WHERE;
    protected $GROUP;
    protected $HAVING;
    protected $ORDER;
    protected $LIMIT;
    protected $rsPrestadorDependente;
    protected $rowPrestadorDependente;
    protected $numRowsPrestadorDependente;

    function __construct() {
        
    }

    function getIdDependente() {
        return $this->prestador_dep_id;
    }

    function getIdContabilEmpresa() {
        return $this->id_contabil_empresa;
    }

    function getNome() {
        return $this->prestador_dep_nome;
    }

    function getParentesco() {
        return $this->prestador_dep_parentesco;
    }

    function getDataNasc() {
        return $this->prestador_dep_data_nasc;
    }

    function getStatus() {
        return $this->prestador_dep_status;
    }

    function getTel() {
        return $this->prestador_dep_tel;
    }

    function setIdDependente($prestador_dep_id) {
        $this->prestador_dep_id = $prestador_dep_id;
    }

    function setIdContabilEmpresa($id_contabil_empresa) {
        $this->id_contabil_empresa = $id_contabil_empresa;
    }

    function setNome($prestador_dep_nome) {
        $this->prestador_dep_nome = $prestador_dep_nome;
    }

    function setParentesco($prestador_dep_parentesco) {
        $this->prestador_dep_parentesco = $prestador_dep_parentesco;
    }

    function setDataNasc($prestador_dep_data_nasc) {
        $this->prestador_dep_data_nasc = $prestador_dep_data_nasc;
    }

    function setStatus($prestador_dep_status) {
        $this->prestador_dep_status = $prestador_dep_status;
    }

    function setTel($prestador_dep_tel) {
        $this->prestador_dep_tel = $prestador_dep_tel;
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

    protected function setRsPrestadorDependente($valor) {
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

        $this->rsPrestadorDependente = mysql_query($sql);
        $this->numRowsPrestadorDependente = mysql_num_rows($this->rsPrestadorDependente);
        return $this->rsPrestadorDependente;
    }

    protected function limpaQuery() {
        $this->setQUERY("");
        $this->setSELECT(" * ");
        $this->setFROM(" prestador_dependente ");
        $this->setWHERE("");
        $this->setGROUP("");
        $this->setHAVING("");
        $this->setORDER("");
        $this->setLIMIT("");
    }

    public function getNumRowPrestadorDependente() {
        return $this->numRowsPrestadorDependente;
    }

    protected function setRowPrestadorDependente($valor) {
        return $this->rowPrestadorDependente = mysql_fetch_assoc($valor);
    }

    public function getRowPrestadorDependente() {

        if ($this->setRowPrestadorDependente($this->rsPrestadorDependente)) {

            $this->setIdDependente($this->rowPrestadorDependente['prestador_dep_id']);
            $this->setNome($this->rowPrestadorDependente['prestador_dep_nome']);
            $this->setTel($this->rowPrestadorDependente['prestador_dep_tel']);
            $this->setDataNasc($this->rowPrestadorDependente['prestador_dep_data_nasc']);
            $this->setIdContabilEmpresa($this->rowPrestadorDependente['id_contabil_empresa']);
            $this->setParentesco($this->rowPrestadorDependente['prestador_dep_parentesco']);
            $this->setStatus($this->rowPrestadorDependente['prestador_dep_status']);

            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    public function getPrestadorDependentes() {
        $this->limpaQuery();
        $auxPrestadorDependente = (!empty($this->getIdDependente())) ? " AND prestador_dep_id = {$this->getIdDependente()} " : null;
        $auxPrestador = (!empty($this->getIdContabilEmpresa())) ? " AND id_contabil_empresa = {$this->getIdContabilEmpresa()} " : null;

        $this->setWHERE("prestador_dep_status = 1 $auxPrestadorDependente $auxPrestador");
        $this->setORDER("prestador_dep_nome");

        if ($this->setRsPrestadorDependente()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function insertPrestadorDependente() {
        $this->limpaQuery();

        $array['prestador_dep_nome'] = $this->getNome();
        $array['prestador_dep_tel'] = $this->getTel();
        $array['prestador_dep_data_nasc'] = $this->getDataNasc();
        $array['id_contabil_empresa'] = $this->getIdContabilEmpresa();
        $array['prestador_dep_parentesco'] = $this->getParentesco();
        $array['prestador_dep_status'] = 1;

        $keys = implode(',', array_keys($array));
        $values = implode("' , '", $array);

        $this->setQUERY("INSERT INTO prestador_dependente ($keys) VALUES ('$values');");

//        echo $sql = "$this->QUERY";
//        echo $this->QUERY."<br>";
        if ($this->setRsPrestadorDependente()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function updatePrestadorDependente() {
        $this->limpaQuery();

        $array['prestador_dep_nome'] = $this->getNome();
        $array['prestador_dep_tel'] = $this->getTel();
        $array['prestador_dep_data_nasc'] = $this->getDataNasc();
        $array['id_contabil_empresa'] = $this->getIdContabilEmpresa();
        $array['prestador_dep_parentesco'] = $this->getParentesco();
        //$array['prestador_dep_status'] = $this->getStatus();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }

        $this->setQUERY("UPDATE prestador_dependente SET " . implode(", ", ($camposUpdate)) . " WHERE prestador_dep_id = {$this->getIdDependente()} LIMIT 1;");
//        echo $this->QUERY."<br>";
        if ($this->setRsPrestadorDependente()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function removerDependente() {
        $QUERY = "UPDATE prestador_dependente SET prestador_dep_status = 0 WHERE prestador_dep_id = {$this->getIdDependente()} LIMIT 1;";
        $this->setQUERY($QUERY);
        if ($this->setRsPrestadorDependente()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

}
