<?php
class ContabilLoteClass {

    protected $id_lote;
    protected $id_projeto;
    protected $lote_numero;
    protected $data_criacao;
    protected $data_cancelamento;
    protected $usuario_criacao;
    protected $usuario_cancelamento;
    protected $ano;
    protected $mes;
    protected $status;
    protected $tipo;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_lote ';
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
    function getIdLote() {
        return $this->id_lote;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getLoteNumero() {
        return $this->lote_numero;
    }

    function getDataCriacao() {
        return $this->data_criacao;
    }

    function getDataCancelamento() {
        return $this->data_cancelamento;
    }

    function getUsuarioCriacao() {
        return $this->usuario_criacao;
    }

    function getUsuarioCancelamento() {
        return $this->usuario_cancelamento;
    }

    function getAno() {
        return $this->ano;
    }

    function getMes() {
        return $this->mes;
    }

    function getStatus() {
        return $this->status;
    }

    function getTipo() {
        return $this->tipo;
    }

    //SET's DA CLASSE
    function setIdLote($id_lote) {
        $this->id_lote = $id_lote;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setLoteNumero($lote_numero) {
        $this->lote_numero = $lote_numero;
    }

    function setDataCriacao($data_criacao) {
        $this->data_criacao = $data_criacao;
    }

    function setDataCancelamento($data_cancelamento) {
        $this->data_cancelamento = $data_cancelamento;
    }

    function setUsuarioCriacao($usuario_criacao) {
        $this->usuario_criacao = $usuario_criacao;
    }

    function setUsuarioCancelamento($usuario_cancelamento) {
        $this->usuario_cancelamento = $usuario_cancelamento;
    }

    function setAno($ano) {
        $this->ano = $ano;
    }

    function setMes($mes) {
        $this->mes = $mes;
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
        $this->id_lote = null;
        $this->id_projeto = null;
        $this->lote_numero = null;
        $this->data_criacao = null;
        $this->data_cancelamento = null;
        $this->usuario_criacao = null;
        $this->usuario_cancelamento = null;
        $this->ano = null;
        $this->mes = null;
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
        $this->setFROM(' contabil_lote ');
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
            $this->setIdLote($this->row['id_lote']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setLoteNumero($this->row['lote_numero']);
            $this->setDataCriacao($this->row['data_criacao']);
            $this->setDataCancelamento($this->row['data_cancelamento']);
            $this->setUsuarioCriacao($this->row['usuario_criacao']);
            $this->setUsuarioCancelamento($this->row['usuario_cancelamento']);
            $this->setAno($this->row['ano']);
            $this->setMes($this->row['mes']);
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
            'id_projeto' => addslashes($this->getIdProjeto()),
            'lote_numero' => addslashes($this->getLoteNumero()),
            'data_criacao' => addslashes($this->getDataCriacao()),
            'data_cancelamento' => addslashes($this->getDataCancelamento()),
            'usuario_criacao' => addslashes($this->getUsuarioCriacao()),
            'usuario_cancelamento' => addslashes($this->getUsuarioCancelamento()),
            'ano' => addslashes($this->getAno()),
            'mes' => addslashes($this->getMes()),
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
        $this->setQUERY("UPDATE contabil_lote SET " . implode(", ", ($camposUpdate)) . " WHERE id_lote = {$this->getIdLote()} LIMIT 1;");

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
        
        $this->setQUERY("INSERT INTO contabil_lote ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdLote(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_lote SET status = 0 WHERE id_lote = {$this->getIdLote()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_lote WHERE id_lote = {$this->getIdLote()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    // --------- Editado -------------------------------------------------------
    
    public function getById() {
        $this->limpaQuery();
        $this->setWHERE("id_lote = {$this->getIdLote()}");
        $this->setLIMIT(1);

        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }
    
    public function verificaLote() {
        $this->limpaQuery();
        $this->setWHERE("mes = {$this->getMes()} AND ano = {$this->getAno()} AND tipo = {$this->getTipo()} AND id_projeto = {$this->getIdProjeto()} AND status = {$this->getStatus()}");
        $this->setORDER("id_lote ASC");
        $this->setLIMIT(1);

        if ($this->setRs()) {
            
            if($this->getNumRows() > 0){
                $this->getRow();
                return 1;
            } else {
                //$this->setLoteNumero("$nomeProjeto ".sprintf("%02d",$this->getMes())."/{$this->getAno()}");
                $this->insert();
                return 2;
            }
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }
    
    public function listaLotes($dados, $projetos) {
        $where = $this->prepara_where($dados);
        $where .= (!empty($projetos)) ? " AND a.id_projeto IN ($projetos) " : '';
        $query = "SELECT a.*, b.nome AS nome_projeto,c.nome AS nome_usuario, a.data_criacao AS criacao_lote
                    FROM contabil_lote AS a
                    INNER JOIN projeto AS b ON (a.id_projeto = b.id_projeto)
                    INNER JOIN funcionario AS c ON (a.usuario_criacao = c.id_funcionario)
                    /*INNER JOIN (SELECT id_lote,max(data_lancamento) AS data_lancamento FROM contabil_lancamento WHERE contabil = '1' GROUP BY id_lote) AS d ON (a.id_lote = d.id_lote)*/ $where";

        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $x[] = $row;
        }
        return $x;
    }

    protected function prepara_where($dados) {
        if (is_array($dados)) {
            $dados = array_filter($dados); //limpa campos vazios
            foreach ($dados as $key => $value) {
                $cond[] = "a.$key = '$value'";
            }
            return (!empty($cond)) ? "WHERE " . implode(' AND ', $cond) : '';
        } else if (!empty($dados)) {
            return "WHERE " . $dados;
        } else {
            return '';
        }
    }
    
}
