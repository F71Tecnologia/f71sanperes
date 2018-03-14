<?php

class ContabilFolhaProvisaoProcClass { 

    protected $id_provisao_proc;
    protected $id_provisao;
    protected $id_clt;
    protected $salario;
    protected $um_doze;
    protected $rescisao;
    protected $rescisao50;
    protected $ferias;
    protected $um_terco;
    protected $decimo_tereiro;
    protected $pis;
    protected $fgts;
    protected $inss;
    protected $rat;
    protected $outras_entidades;
    protected $lei12506_dias;
    protected $lei12506_valor;
    protected $rescisao_parcela;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_folha_provisao_proc ';
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
    function getIdProvisaoProc() {
        return $this->id_provisao_proc;
    }

    function getIdProvisao() {
        return $this->id_provisao;
    }

    function getIdClt() {
        return $this->id_clt;
    }

    function getSalario() {
        return $this->salario;
    }

    function getUmDoze() {
        return $this->um_doze;
    }

    function getRescisao() {
        return $this->rescisao;
    }

    function getRescisao50() {
        return $this->rescisao50;
    }

    function getFerias() {
        return $this->ferias;
    }

    function getUmTerco() {
        return $this->um_terco;
    }

    function getDecimoTereiro() {
        return $this->decimo_tereiro;
    }

    function getPis() {
        return $this->pis;
    }

    function getFgts() {
        return $this->fgts;
    }

    function getInss() {
        return $this->inss;
    }

    function getRat() {
        return $this->rat;
    }

    function getOutrasEntidades() {
        return $this->outras_entidades;
    }

    function getLei12506Dias() {
        return $this->lei12506_dias;
    }

    function getLei12506Valor() {
        return $this->lei12506_valor;
    }

    function getRescisaoParcela() {
        return $this->rescisao_parcela;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdProvisaoProc($id_provisao_proc) {
        $this->id_provisao_proc = $id_provisao_proc;
    }

    function setIdProvisao($id_provisao) {
        $this->id_provisao = $id_provisao;
    }

    function setIdClt($id_clt) {
        $this->id_clt = $id_clt;
    }

    function setSalario($salario) {
        $this->salario = $salario;
    }

    function setUmDoze($um_doze) {
        $this->um_doze = $um_doze;
    }

    function setRescisao($rescisao) {
        $this->rescisao = $rescisao;
    }

    function setRescisao50($rescisao50) {
        $this->rescisao50 = $rescisao50;
    }

    function setFerias($ferias) {
        $this->ferias = $ferias;
    }

    function setUmTerco($um_terco) {
        $this->um_terco = $um_terco;
    }

    function setDecimoTereiro($decimo_tereiro) {
        $this->decimo_tereiro = $decimo_tereiro;
    }

    function setPis($pis) {
        $this->pis = $pis;
    }

    function setFgts($fgts) {
        $this->fgts = $fgts;
    }

    function setInss($inss) {
        $this->inss = $inss;
    }

    function setRat($rat) {
        $this->rat = $rat;
    }

    function setOutrasEntidades($outras_entidades) {
        $this->outras_entidades = $outras_entidades;
    }

    function setLei12506Dias($lei12506_dias) {
        $this->lei12506_dias = $lei12506_dias;
    }

    function setLei12506Valor($lei12506_valor) {
        $this->lei12506_valor = $lei12506_valor;
    }

    function setRescisaoParcela($rescisao_parcela) {
        $this->rescisao_parcela = $rescisao_parcela;
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
        $this->id_provisao_proc = null;
        $this->id_provisao = null;
        $this->id_clt = null;
        $this->salario = null;
        $this->um_doze = null;
        $this->rescisao = null;
        $this->rescisao50 = null;
        $this->ferias = null;
        $this->um_terco = null;
        $this->decimo_tereiro = null;
        $this->pis = null;
        $this->fgts = null;
        $this->inss = null;
        $this->rat = null;
        $this->outras_entidades = null;
        $this->lei12506_dias = null;
        $this->lei12506_valor = null;
        $this->rescisao_parcela = null;
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
        $this->setFROM(' contabil_folha_provisao_proc ');
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
            $this->setIdProvisaoProc($this->row['id_provisao_proc']);
            $this->setIdProvisao($this->row['id_provisao']);
            $this->setIdClt($this->row['id_clt']);
            $this->setSalario($this->row['salario']);
            $this->setUmDoze($this->row['um_doze']);
            $this->setRescisao($this->row['rescisao']);
            $this->setRescisao50($this->row['rescisao50']);
            $this->setFerias($this->row['ferias']);
            $this->setUmTerco($this->row['um_terco']);
            $this->setDecimoTereiro($this->row['decimo_tereiro']);
            $this->setPis($this->row['pis']);
            $this->setFgts($this->row['fgts']);
            $this->setInss($this->row['inss']);
            $this->setRat($this->row['rat']);
            $this->setOutrasEntidades($this->row['outras_entidades']);
            $this->setLei12506Dias($this->row['lei12506_dias']);
            $this->setLei12506Valor($this->row['lei12506_valor']);
            $this->setRescisaoParcela($this->row['rescisao_parcela']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_provisao' => addslashes($this->getIdProvisao()),
            'id_clt' => addslashes($this->getIdClt()),
            'salario' => addslashes($this->getSalario()),
            'um_doze' => addslashes($this->getUmDoze()),
            'rescisao' => addslashes($this->getRescisao()),
            'rescisao50' => addslashes($this->getRescisao50()),
            'ferias' => addslashes($this->getFerias()),
            'um_terco' => addslashes($this->getUmTerco()),
            'decimo_tereiro' => addslashes($this->getDecimoTereiro()),
            'pis' => addslashes($this->getPis()),
            'fgts' => addslashes($this->getFgts()),
            'inss' => addslashes($this->getInss()),
            'rat' => addslashes($this->getRat()),
            'outras_entidades' => addslashes($this->getOutrasEntidades()),
            'lei12506_dias' => addslashes($this->getLei12506Dias()),
            'lei12506_valor' => addslashes($this->getLei12506Valor()),
            'rescisao_parcela' => addslashes($this->getRescisaoParcela()),
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
        $this->setQUERY("UPDATE contabil_folha_provisao_proc SET " . implode(", ", ($camposUpdate)) . " WHERE id_provisao_proc = {$this->getIdProvisaoProc()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_folha_provisao_proc ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdProvisaoProc(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_folha_provisao_proc SET status = 0 WHERE id_provisao_proc = {$this->getIdProvisaoProc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_folha_provisao_proc WHERE id_provisao_proc = {$this->getIdProvisaoProc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

   
    public function getByIdProvisao() {
        $this->limpaQuery();

        $this->setWHERE("id_provisao = {$this->getIdProvisao()} AND status = 1");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
}