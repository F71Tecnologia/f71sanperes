<?php 
class ContabilLancamentoItemClass { 

    protected $id_lancamento_itens;
    protected $id_lancamento;
    protected $id_conta;
    protected $valor;
    protected $documento;
    protected $tipo;
    protected $historico;
    protected $fornecedor;
    protected $banco;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_lancamento_itens ';
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
    function getIdLancamentoItens() {
        return $this->id_lancamento_itens;
    }

    function getIdLancamento() {
        return $this->id_lancamento;
    }

    function getIdConta() {
        return $this->id_conta;
    }

    function getValor() {
        return $this->valor;
    }

    function getDocumento() {
        return $this->documento;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getHistorico() {
        return $this->historico;
    }

    function getFornecedor() {
        return $this->fornecedor;
    }

    function getBanco() {
        return $this->banco;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdLancamentoItens($id_lancamento_itens) {
        $this->id_lancamento_itens = $id_lancamento_itens;
    }

    function setIdLancamento($id_lancamento) {
        $this->id_lancamento = $id_lancamento;
    }

    function setIdConta($id_conta) {
        $this->id_conta = $id_conta;
    }

    function setValor($valor) {
        $this->valor = $valor;
    }

    function setDocumento($documento) {
        $this->documento = $documento;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    function setHistorico($historico) {
        $this->historico = $historico;
    }

    function setFornecedor($fornecedor) {
        $this->fornecedor = $fornecedor;
    }

    function setBanco($banco) {
        $this->banco = $banco;
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
        $this->id_lancamento_itens = null;
        $this->id_lancamento = null;
        $this->id_conta = null;
        $this->valor = null;
        $this->documento = null;
        $this->tipo = null;
        $this->historico = null;
        $this->fornecedor = null;
        $this->banco = null;
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
        $this->setFROM(' contabil_lancamento_itens ');
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
            $this->setIdLancamentoItens($this->row['id_lancamento_itens']);
            $this->setIdLancamento($this->row['id_lancamento']);
            $this->setIdConta($this->row['id_conta']);
            $this->setValor($this->row['valor']);
            $this->setDocumento($this->row['documento']);
            $this->setTipo($this->row['tipo']);
            $this->setHistorico($this->row['historico']);
            $this->setFornecedor($this->row['fornecedor']);
            $this->setBanco($this->row['banco']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_lancamento' => addslashes($this->getIdLancamento()),
            'id_conta' => addslashes($this->getIdConta()),
            'valor' => addslashes($this->getValor()),
            'documento' => addslashes($this->getDocumento()),
            'tipo' => addslashes($this->getTipo()),
            'historico' => addslashes($this->getHistorico()),
            'fornecedor' => addslashes($this->getFornecedor()),
//            'banco' => addslashes($this->getBanco()),
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
        $this->setQUERY("UPDATE contabil_lancamento_itens SET " . implode(", ", ($camposUpdate)) . " WHERE id_lancamento_itens = {$this->getIdLancamentoItens()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_lancamento_itens ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdLancamentoItens(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_lancamento_itens SET status = 0 WHERE id_lancamento_itens = {$this->getIdLancamentoItens()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_lancamento_itens WHERE id_lancamento_itens = {$this->getIdLancamentoItens()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function excluirByLancamento($id_lancamento) {
        $this->setQUERY("UPDATE contabil_lancamento_itens SET status = 0 WHERE id_lancamento = $id_lancamento");
        $this->setRs();
        $this->setQUERY("SELECT GROUP_CONCAT(id_lancamento_itens) AS group_id FROM contabil_lancamento_itens A WHERE id_lancamento = $id_lancamento");
        
        $x = mysql_fetch_assoc($this->setRs());
        $this->setQUERY("UPDATE contabil_contas_saldo_dia SET status = 0 WHERE id_lancamento_itens IN ({$x['group_id']})");
        
        return $this->setRs();
    }

    public function salvar() {
        if (empty($this->id_lancamento_itens)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }
    
    public function getLancamentoItens() {
        $array = $this->makeCampos();

        $array = array_filter($array);
 

        foreach ($array as $key => $value) {
            $condicoes[] = "a.$key = '$value'";
        }

        $this->limpaQuery();
        $this->setSELECT('a.*, b.descricao,b.classificador AS conta');
        $this->setFROM('contabil_lancamento_itens AS a INNER JOIN contabil_planodecontas AS b ON (a.id_conta = b.id_conta)');
        $this->setWHERE(implode(' AND ', $condicoes));
        $this->setORDER('a.id_lancamento, a.tipo DESC');
        if ($this->setRs()) {
            while ($row = mysql_fetch_assoc($this->rs)) {
                $arrayx[] = $row;
            }
            return $arrayx;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }
    
    function getLivroDiario($projeto, $data_ini_bd, $data_fim_bd, $status = 1){
        $sql = "SELECT A.id_lancamento lancamento, B.id_lancamento_itens, DATE_FORMAT(A.data_lancamento, '%d/%m/%Y') data_lancamento, C.classificador, C.descricao, A.historico historico_l, B.historico historico_i, B.valor, C.natureza, B.tipo
                FROM contabil_lancamento A
                INNER JOIN contabil_lancamento_itens B ON (A.id_lancamento = B.id_lancamento AND B.status IN(1,3))
                INNER JOIN contabil_planodecontas C ON (B.id_conta = C.id_conta AND C.sped = 0 AND C.status = 1)
                WHERE A.data_lancamento BETWEEN '$data_ini_bd' AND '$data_fim_bd' AND A.id_projeto IN({$projeto}) AND C.classificador NOT IN('2.07.07.01.01') AND A.status > 0 
                ORDER BY A.data_lancamento, A.id_lancamento, B.tipo DESC";
                
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            
            $array[$row['data_lancamento']][$row['lancamento']][$row['historico_l']][$row['id_lancamento_itens']] = $row;
        }
        return $array;
    }
    public function encerramento_update($data_ini, $data_fim, $id_projeto){
        $this->limpaQuery();
        
        $this->setQUERY("UPDATE contabil_lancamento SET status = 3 WHERE data_lancamento BETWEEN '{$data_ini}' AND '{$data_fim}' AND id_projeto = '{$id_projeto}' AND status = 1");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

}
