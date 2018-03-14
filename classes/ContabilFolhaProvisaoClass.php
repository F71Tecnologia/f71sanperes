<?php 
class ContabilFolhaProvisaoClass { 

    protected $id_provisao;
    protected $id_folha;
    protected $id_regiao;
    protected $id_projeto;
    protected $data;
    protected $data_deletado;
    protected $qtd;
    protected $id_funcionario;
    protected $titulo;
    protected $rescisao;
    protected $multa50;
    protected $ferias;
    protected $um_terco;
    protected $decimo_tereiro;
    protected $fgts;
    protected $pis;
    protected $inss;
    protected $lei12506;
    protected $rat;
    protected $outras;
    protected $status;
    protected $rat_percent;
    protected $outros_percent;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_folha_provisao ';
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
    function getIdProvisao() {
        return $this->id_provisao;
    }

    function getIdFolha() {
        return $this->id_folha;
    }

    function getIdRegiao() {
        return $this->id_regiao;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getData($formato = null) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato) : $this->data;
        }
    }

    function getDataDeletado($formato = null) {
        if (empty($this->data_deletado) || $this->data_deletado == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_deletado), $formato) : $this->data_deletado;
        }
    }

    function getQtd() {
        return $this->qtd;
    }

    function getIdFuncionario() {
        return $this->id_funcionario;
    }

    function getTitulo() {
        return $this->titulo;
    }

    function getRescisao() {
        return $this->rescisao;
    }

    function getMulta50() {
        return $this->multa50;
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

    function getFgts() {
        return $this->fgts;
    }

    function getPis() {
        return $this->pis;
    }

    function getInss() {
        return $this->inss;
    }

    function getLei12506() {
        return $this->lei12506;
    }

    function getRat() {
        return $this->rat;
    }

    function getOutras() {
        return $this->outras;
    }

    function getStatus() {
        return $this->status;
    }

    function getRatPercent() {
        return $this->rat_percent;
    }

    function getOutrosPercent() {
        return $this->outros_percent;
    }

    //SET's DA CLASSE
    function setIdProvisao($id_provisao) {
        $this->id_provisao = $id_provisao;
    }

    function setIdFolha($id_folha) {
        $this->id_folha = $id_folha;
    }

    function setIdRegiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setDataDeletado($data_deletado) {
        $this->data_deletado = $data_deletado;
    }

    function setQtd($qtd) {
        $this->qtd = $qtd;
    }

    function setIdFuncionario($id_funcionario) {
        $this->id_funcionario = $id_funcionario;
    }

    function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    function setRescisao($rescisao) {
        $this->rescisao = $rescisao;
    }

    function setMulta50($multa50) {
        $this->multa50 = $multa50;
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

    function setFgts($fgts) {
        $this->fgts = $fgts;
    }

    function setPis($pis) {
        $this->pis = $pis;
    }

    function setInss($inss) {
        $this->inss = $inss;
    }

    function setLei12506($lei12506) {
        $this->lei12506 = $lei12506;
    }

    function setRat($rat) {
        $this->rat = $rat;
    }

    function setOutras($outras) {
        $this->outras = $outras;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setRatPercent($rat_percent) {
        $this->rat_percent = $rat_percent;
    }

    function setOutrosPercent($outros_percent) {
        $this->outros_percent = $outros_percent;
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
        $this->id_provisao = null;
        $this->id_folha = null;
        $this->id_regiao = null;
        $this->id_projeto = null;
        $this->data = null;
        $this->data_deletado = null;
        $this->qtd = null;
        $this->id_funcionario = null;
        $this->titulo = null;
        $this->rescisao = null;
        $this->multa50 = null;
        $this->ferias = null;
        $this->um_terco = null;
        $this->decimo_tereiro = null;
        $this->fgts = null;
        $this->pis = null;
        $this->inss = null;
        $this->lei12506 = null;
        $this->rat = null;
        $this->outras = null;
        $this->status = null;
        $this->rat_percent = null;
        $this->outros_percent = null;
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
        $this->setFROM(' contabil_folha_provisao ');
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
            $this->setIdProvisao($this->row['id_provisao']);
            $this->setIdFolha($this->row['id_folha']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setData($this->row['data']);
            $this->setDataDeletado($this->row['data_deletado']);
            $this->setQtd($this->row['qtd']);
            $this->setIdFuncionario($this->row['id_funcionario']);
            $this->setTitulo($this->row['titulo']);
            $this->setRescisao($this->row['rescisao']);
            $this->setMulta50($this->row['multa50']);
            $this->setFerias($this->row['ferias']);
            $this->setUmTerco($this->row['um_terco']);
            $this->setDecimoTereiro($this->row['decimo_tereiro']);
            $this->setFgts($this->row['fgts']);
            $this->setPis($this->row['pis']);
            $this->setInss($this->row['inss']);
            $this->setLei12506($this->row['lei12506']);
            $this->setRat($this->row['rat']);
            $this->setOutras($this->row['outras']);
            $this->setStatus($this->row['status']);
            $this->setRatPercent($this->row['rat_percent']);
            $this->setOutrosPercent($this->row['outros_percent']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_folha' => addslashes($this->getIdFolha()),
            'id_regiao' => addslashes($this->getIdRegiao()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'data' => addslashes($this->getData()),
            'data_deletado' => addslashes($this->getDataDeletado()),
            'qtd' => addslashes($this->getQtd()),
            'id_funcionario' => addslashes($this->getIdFuncionario()),
            'titulo' => addslashes($this->getTitulo()),
            'rescisao' => addslashes($this->getRescisao()),
            'multa50' => addslashes($this->getMulta50()),
            'ferias' => addslashes($this->getFerias()),
            'um_terco' => addslashes($this->getUmTerco()),
            'decimo_tereiro' => addslashes($this->getDecimoTereiro()),
            'fgts' => addslashes($this->getFgts()),
            'pis' => addslashes($this->getPis()),
            'inss' => addslashes($this->getInss()),
            'lei12506' => addslashes($this->getLei12506()),
            'rat' => addslashes($this->getRat()),
            'outras' => addslashes($this->getOutras()),
            'status' => addslashes($this->getStatus()),
            'rat_percent' => addslashes($this->getRatPercent()),
            'outros_percent' => addslashes($this->getOutrosPercent()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_folha_provisao SET " . implode(", ", ($camposUpdate)) . " WHERE id_provisao = {$this->getIdProvisao()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_folha_provisao ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdProvisao(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_folha_provisao SET data_deletado = NOW(), status = 0 WHERE id_provisao = {$this->getIdProvisao()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_folha_provisao WHERE id_provisao = {$this->getIdProvisao()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function getById() {
        $this->limpaQuery();
        $this->setWHERE("id_provisao = {$this->getIdProvisao()} AND status = 1");
        $this->setLIMIT(1);
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
}
    }
    
    public function getByRegiao($toArray = false) {
        $this->limpaQuery();
        $this->setWHERE("id_regiao = {$this->getIdRegiao()} AND status = 1");
        $this->setORDER("id_projeto ASC, id_folha DESC");
        if ($this->setRs()) {
            if($toArray){
                while ($this->getRow()){
                    $array[$this->getIdProjeto()][$this->getIdFolha()] = $this->row;
                }
                return $array;
            } else {
                return 1;
            }
        } else {
            die(mysql_error());
        }
    }
    
    public function getAnoProvisao() {
        $this->limpaQuery();
        $sql = "SELECT ano FROM rh_folha WHERE id_folha = {$this->getIdFolha()} LIMIT 1";
        $qry = mysql_query($sql);
        if ($qry) {
            $row = mysql_fetch_assoc($qry);
            return $row['ano'];
        } else {
            return mysql_error();
        }
    }
}
