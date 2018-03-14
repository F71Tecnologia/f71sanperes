<?php 
class EntradaTravarLancamentoClass { 

    protected $id_entrada;
    protected $id_regiao;
    protected $id_projeto;
    protected $id_banco;
    protected $id_user;
    protected $nome;
    protected $especifica;
    protected $tipo;
    protected $adicional;
    protected $valor;
    protected $data_proc;
    protected $data_vencimento;
    protected $data_pg;
    protected $hora_pg;
    protected $comprovante;
    protected $id_userpg;
    protected $campo2;
    protected $campo3;
    protected $status;
    protected $id_deletado;
    protected $data_deletado;
    protected $subtipo;
    protected $trava_contabil;
    protected $n_subtipo;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' entrada ';
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
    function getIdEntrada() {
        return $this->id_entrada;
    }

    function getIdRegiao() {
        return $this->id_regiao;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getIdBanco() {
        return $this->id_banco;
    }

    function getIdUser() {
        return $this->id_user;
    }

    function getNome() {
        return $this->nome;
    }

    function getEspecifica() {
        return $this->especifica;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getAdicional() {
        return $this->adicional;
    }

    function getValor() {
        return $this->valor;
    }

    function getDataProc() {
        return $this->data_proc;
    }

    function getDataVencimento($formato = null) {
        if (empty($this->data_vencimento) || $this->data_vencimento == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_vencimento), $formato) : $this->data_vencimento;
        }
    }

    function getDataPg($formato = null) {
        if (empty($this->data_pg) || $this->data_pg == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_pg), $formato) : $this->data_pg;
        }
    }

    function getHoraPg() {
        return $this->hora_pg;
    }

    function getComprovante() {
        return $this->comprovante;
    }

    function getIdUserpg() {
        return $this->id_userpg;
    }

    function getCampo2() {
        return $this->campo2;
    }

    function getCampo3() {
        return $this->campo3;
    }

    function getStatus() {
        return $this->status;
    }

    function getIdDeletado() {
        return $this->id_deletado;
    }

    function getDataDeletado() {
        return $this->data_deletado;
    }

    function getSubtipo() {
        return $this->subtipo;
    }

    function getTravaContabil() {
        return $this->trava_contabil;
    }

    function getNSubtipo() {
        return $this->n_subtipo;
    }

    //SET's DA CLASSE
    function setIdEntrada($id_entrada) {
        $this->id_entrada = $id_entrada;
    }

    function setIdRegiao($id_regiao) {
        $this->id_regiao = $id_regiao;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setIdBanco($id_banco) {
        $this->id_banco = $id_banco;
    }

    function setIdUser($id_user) {
        $this->id_user = $id_user;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setEspecifica($especifica) {
        $this->especifica = $especifica;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setAdicional($adicional) {
        $this->adicional = $adicional;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setDataProc($data_proc) {
        $this->data_proc = $data_proc;
    }

    function setDataVencimento($data_vencimento) {
        $this->data_vencimento = $data_vencimento;
    }

    function setDataPg($data_pg) {
        $this->data_pg = $data_pg;
    }

    function setHoraPg($hora_pg) {
        $this->hora_pg = $hora_pg;
    }

    function setComprovante($comprovante) {
        $this->comprovante = $comprovante;
    }

    function setIdUserpg($id_userpg) {
        $this->id_userpg = $id_userpg;
    }

    function setCampo2($campo2) {
        $this->campo2 = $campo2;
    }

    function setCampo3($campo3) {
        $this->campo3 = $campo3;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setIdDeletado($id_deletado) {
        $this->id_deletado = $id_deletado;
    }

    function setDataDeletado($data_deletado) {
        $this->data_deletado = $data_deletado;
    }

    function setSubtipo($subtipo) {
        $this->subtipo = $subtipo;
    }

    function setTravaContabil($trava_contabil) {
        $this->trava_contabil = $trava_contabil;
    }

    function setNSubtipo($n_subtipo) {
        $this->n_subtipo = $n_subtipo;
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
        $this->id_entrada = null;
        $this->id_regiao = null;
        $this->id_projeto = null;
        $this->id_banco = null;
        $this->id_user = null;
        $this->nome = null;
        $this->especifica = null;
        $this->tipo = null;
        $this->adicional = null;
        $this->valor = null;
        $this->data_proc = null;
        $this->data_vencimento = null;
        $this->data_pg = null;
        $this->hora_pg = null;
        $this->comprovante = null;
        $this->id_userpg = null;
        $this->campo2 = null;
        $this->campo3 = null;
        $this->status = null;
        $this->id_deletado = null;
        $this->data_deletado = null;
        $this->subtipo = null;
        $this->trava_contabil = null;
        $this->n_subtipo = null;
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
        $this->setFROM(' entrada ');
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
            $this->setIdEntrada($this->row['id_entrada']);
            $this->setIdRegiao($this->row['id_regiao']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setIdBanco($this->row['id_banco']);
            $this->setIdUser($this->row['id_user']);
            $this->setNome($this->row['nome']);
            $this->setEspecifica($this->row['especifica']);
            $this->setTipo($this->row['tipo']);
            $this->setAdicional($this->row['adicional']);
            $this->setValor($this->row['valor']);
            $this->setDataProc($this->row['data_proc']);
            $this->setDataVencimento($this->row['data_vencimento']);
            $this->setDataPg($this->row['data_pg']);
            $this->setHoraPg($this->row['hora_pg']);
            $this->setComprovante($this->row['comprovante']);
            $this->setIdUserpg($this->row['id_userpg']);
            $this->setCampo2($this->row['campo2']);
            $this->setCampo3($this->row['campo3']);
            $this->setStatus($this->row['status']);
            $this->setIdDeletado($this->row['id_deletado']);
            $this->setDataDeletado($this->row['data_deletado']);
            $this->setSubtipo($this->row['subtipo']);
            $this->setTravaContabil($this->row['trava_contabil']);
            $this->setNSubtipo($this->row['n_subtipo']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_regiao' => addslashes($this->getIdRegiao()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'id_banco' => addslashes($this->getIdBanco()),
            'id_user' => addslashes($this->getIdUser()),
            'nome' => addslashes($this->getNome()),
            'especifica' => addslashes($this->getEspecifica()),
            'tipo' => addslashes($this->getTipo()),
            'adicional' => addslashes($this->getAdicional()),
            'valor' => addslashes($this->getValor()),
            'data_proc' => addslashes($this->getDataProc()),
            'data_vencimento' => addslashes($this->getDataVencimento()),
            'data_pg' => addslashes($this->getDataPg()),
            'hora_pg' => addslashes($this->getHoraPg()),
            'comprovante' => addslashes($this->getComprovante()),
            'id_userpg' => addslashes($this->getIdUserpg()),
            'campo2' => addslashes($this->getCampo2()),
            'campo3' => addslashes($this->getCampo3()),
            'status' => addslashes($this->getStatus()),
            'id_deletado' => addslashes($this->getIdDeletado()),
            'data_deletado' => addslashes($this->getDataDeletado()),
            'subtipo' => addslashes($this->getSubtipo()),
            'trava_contabil' => addslashes($this->getTravaContabil()),
            'n_subtipo' => addslashes($this->getNSubtipo()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE entrada SET " . implode(", ", ($camposUpdate)) . " WHERE id_entrada = {$this->getIdEntrada()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO entrada ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdEntrada(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE entrada SET status = 0 WHERE id_entrada = {$this->getIdEntrada()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM entrada WHERE id_entrada = {$this->getIdEntrada()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function updateTrava() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE entrada SET trava_contabil = 1 WHERE status = {$this->getStatus()} AND data_vencimento BETWEEN '{$this->getDataVencimento()}' AND LAST_DAY('{$this->getDataVencimento()}') AND id_projeto = '{$this->getIdProjeto()}'");
        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }
}