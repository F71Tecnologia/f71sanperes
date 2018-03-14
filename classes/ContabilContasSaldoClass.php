<?php
class ContabilContasSaldoClass { 

    protected $id_saldo;
    protected $id_conta;
    protected $mes;
    protected $ano;
    protected $valor;
    protected $status;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_contas_saldo ';
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
    function getIdSaldo() {
        return $this->id_saldo;
    }

    function getIdConta() {
        return $this->id_conta;
    }

    function getMes() {
        return $this->mes;
    }

    function getAno() {
        return $this->ano;
    }

    function getValor() {
        return $this->valor;
    }

    function getStatus() {
        return $this->status;
    }

    //SET's DA CLASSE
    function setIdSaldo($id_saldo) {
        $this->id_saldo = $id_saldo;
    }

    function setIdConta($id_conta) {
        $this->id_conta = $id_conta;
    }

    function setMes($mes) {
        $this->mes = $mes;
    }

    function setAno($ano) {
        $this->ano = $ano;
    }

    function setValor($valor) {
        $this->valor = $valor;
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

    protected function setRs($valor) {
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
        $this->setFROM(' contabil_contas_saldo ');
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
            $this->setIdSaldo($this->row['id_saldo']);
            $this->setIdConta($this->row['id_conta']);
            $this->setMes($this->row['mes']);
            $this->setAno($this->row['ano']);
            $this->setValor($this->row['valor']);
            $this->setStatus($this->row['status']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_conta' => addslashes($this->getIdConta()),
            'mes' => addslashes($this->getMes()),
            'ano' => addslashes($this->getAno()),
            'valor' => addslashes($this->getValor()),
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
        $this->setQUERY("UPDATE contabil_contas_saldo SET " . implode(", ", ($camposUpdate)) . " WHERE id_saldo = {$this->getIdSaldo()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_contas_saldo ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdSaldo(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_contas_saldo SET status = 0 WHERE id_saldo = {$this->getIdSaldo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_contas_saldo WHERE id_saldo = {$this->getIdSaldo()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function verificaSaldo($idLancamentoItem) {
        $this->limpaQuery();
        
        $this->setSELECT("A.id_saldo, B.valor, B.tipo");
        $this->setFROM("contabil_contas_saldo A INNER JOIN contabil_lancamento_itens B ON (A.id_conta = B.id_conta AND B.`status` = 1 AND B.id_lancamento_itens = {$idLancamentoItem}) INNER JOIN contabil_lancamento C ON (B.id_lancamento = C.id_lancamento AND C.`status` = 1) INNER JOIN contabil_lote D ON (C.id_lote = D.id_lote AND A.mes = D.mes AND A.ano = D.ano AND D.`status` = 1)");
        $this->setWHERE("A.status = 1");
        $this->setLIMIT(1);
        if ($this->setRs()) {
            if($this->getNumRows() > 0){
                $row = mysql_fetch_assoc($this->rs);
                $row['valor'] = $this->formato_valor($row['valor']);
                if($row['tipo'] == 1){
                    $valor = $row['valor'] * -1;
                } else {
                    $valor = $row['valor'];
                }
                $update = mysql_query("UPDATE contabil_contas_saldo SET valor = (valor + $valor) WHERE id_saldo = {$row['id_saldo']}");
            } else {
                $this->limpaQuery();
                $this->setSELECT("B.id_conta, D.mes, D.ano, B.valor, B.tipo");
                $this->setFROM("contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (B.id_lancamento = C.id_lancamento AND C.`status` = 1) INNER JOIN contabil_lote D ON (C.id_lote = D.id_lote AND D.`status` = 1)");
                $this->setWHERE("B.`status` = 1 AND B.id_lancamento_itens = '{$idLancamentoItem}'");
                if ($this->setRs()) {
                    if($this->getNumRows() > 0){
                        $valor = 0.00;
                        $row = mysql_fetch_assoc($this->rs);
                        $row['valor'] = $this->formato_valor($row['valor']);
//                        $periodoAnterior = new DateTime("{$row['ano']}-".  sprintf('%02d',$row['mes'])."-01");
                        //print_array($periodoAnterior);
//                        $periodoAnterior->modify("-1 MONTH");
                        //print_array($periodoAnterior);
                        $valor = mysql_result(mysql_query("SELECT valor FROM contabil_contas_saldo WHERE status = '1' AND id_conta = '{$row['id_conta']}' AND mes < '{$row['mes']}' AND ano <= '{$row['ano']}' ORDER BY ano DESC, mes DESC LIMIT 1"), 0);
                        if($row['tipo'] == 1){
                            $valor = $valor + $row['valor'] * -1;
                        } else {
                            $valor = $valor + $row['valor'];
                        }
                        //echo "INSERT INTO contabil_contas_saldo (`id_conta`, `mes`, `ano`, `status`, `valor` ) VALUES ('{$row['id_conta']}', '{$row['mes']}', '{$row['ano']}', '1', '$valor');";
                        $insert = mysql_query("INSERT INTO contabil_contas_saldo (`id_conta`, `data`, `mes`, `ano`, `status`, `valor` ) VALUES ('{$row['id_conta']}', NOW(), '{$row['mes']}', '{$row['ano']}', '1', '$valor');");
                    }
                }
            }
            echo mysql_error();
        } else {
            die(mysql_error());
        }
    }
    
    public function verificaSaldo2($idLancamentoItem) {
        $this->limpaQuery();
        
        $this->setSELECT("B.id_lancamento_itens, B.tipo, B.valor, C.data_lancamento, B.id_conta");
        $this->setFROM("contabil_lancamento_itens B INNER JOIN contabil_lancamento C ON (B.id_lancamento = C.id_lancamento AND C.`status` = 1 AND contabil = 1)");
        $this->setWHERE("B.`status` = 1 AND B.id_lancamento_itens = {$idLancamentoItem}");
        $this->setLIMIT(1);
        if ($this->setRs()) {
            if($this->getNumRows() > 0){
                $row = mysql_fetch_assoc($this->rs);
                $row['valor'] = $this->formato_valor($row['valor']);
                $sql = mysql_query("SELECT * FROM contabil_contas_saldo_dia WHERE id_lancamento_itens = $idLancamentoItem AND status = 1 LIMIT 1;");
                if(mysql_num_rows($sql) == 0) {
                    $insert = "INSERT INTO contabil_contas_saldo_dia VALUES ('', {$row['id_conta']}, {$idLancamentoItem}, '{$row['data_lancamento']}', NOW(), {$row['tipo']}, '{$row['valor']}', 1);";
//                    echo $insert."<br>";
                    mysql_query($insert);
                } else {
                    $row2 = mysql_fetch_assoc($sql);
                    $update = "UPDATE contabil_contas_saldo_dia SET valor = '{$row['valor']}', id_conta = {$row['id_conta']}, data_proc = NOW(), tipo = {$row['tipo']} WHERE id_saldo = {$row2['id_saldo']} AND id_lancamento_itens = {$idLancamentoItem}";
//                    echo $update."<br>";
                    mysql_query($update);
                }
            }
            return mysql_error();
        } else {
            die(mysql_error());
        }
    }
    
    public function formato_valor($valor) {
    if (strpos($valor,',') === FALSE) {
        return number_format($valor, 2, ',', '.');
    } else {
        return $valor;
    }
}

}    
?>