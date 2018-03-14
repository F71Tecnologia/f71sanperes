<?php

class ContabilLancamentoClass {

    protected $id_lancamento;
    protected $id_lote;
    protected $id_projeto;
    protected $id_usuario;
    protected $id_saida;
    protected $id_entrada;
    protected $id_folha;
    protected $folha;
    protected $data_lancamento;
    protected $historico;
    protected $contabil;
    protected $status;
    protected $trava_contabil;
    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_lancamento ';
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
    function getIdLancamento() {
        return $this->id_lancamento;
    }

    function getIdLote() {
        return $this->id_lote;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getIdUsuario() {
        return $this->id_usuario;
    }

    function getIdSaida() {
        return $this->id_saida;
    }

    function getIdEntrada() {
        return $this->id_entrada;
    }

    function getIdFolha() {
        return $this->id_folha;
    }

    function getFolha() {
        return $this->folha;
    }

    function getDataLancamento($formato = null) {
        if (empty($this->data_lancamento) || $this->data_lancamento == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data_lancamento), $formato) : $this->data_lancamento;
        }
    }

    function getHistorico() {
        return $this->historico;
    }

    function getContabil() {
        return $this->contabil;
    }

    function getStatus() {
        return $this->status;
    }

    function getTravaContabil() {
        return $this->trava_contabil;
    }

    //SET's DA CLASSE
    function setIdLancamento($id_lancamento) {
        $this->id_lancamento = $id_lancamento;
    }

    function setIdLote($id_lote) {
        $this->id_lote = $id_lote;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    function setIdSaida($id_saida) {
        $this->id_saida = $id_saida;
    }

    function setIdEntrada($id_entrada) {
        $this->id_entrada = $id_entrada;
    }

    function setIdFolha($id_folha) {
        $this->id_folha = $id_folha;
    }

    function setFolha($folha) {
        $this->folha = $folha;
    }

    function setDataLancamento($data_lancamento) {
        $this->data_lancamento = $data_lancamento;
    }

    function setHistorico($historico) {
        $this->historico = $historico;
    }

    function setContabil($contabil) {
        $this->contabil = $contabil;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setTravaContabil($trava_contabil) {
        $this->trava_contabil = $trava_contabil;
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
        $this->id_lancamento = null;
        $this->id_lote = null;
        $this->id_projeto = null;
        $this->id_usuario = null;
        $this->id_saida = null;
        $this->id_entrada = null;
        $this->id_folha = null;
        $this->folha = null;
        $this->data_lancamento = null;
        $this->historico = null;
        $this->contabil = null;
        $this->status = null;
        $this->trava_contabil = null;
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
        $this->setFROM(' contabil_lancamento ');
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
            $this->setIdLancamento($this->row['id_lancamento']);
            $this->setIdLote($this->row['id_lote']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setIdUsuario($this->row['id_usuario']);
            $this->setIdSaida($this->row['id_saida']);
            $this->setIdEntrada($this->row['id_entrada']);
            $this->setIdFolha($this->row['id_folha']);
            $this->setFolha($this->row['folha']);
            $this->setDataLancamento($this->row['data_lancamento']);
            $this->setHistorico($this->row['historico']);
            $this->setContabil($this->row['contabil']);
            $this->setStatus($this->row['status']);
            $this->setTravaContabil($this->row['trava_contabil']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_lote' => addslashes($this->getIdLote()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'id_usuario' => addslashes($this->getIdUsuario()),
            'id_saida' => addslashes($this->getIdSaida()),
            'id_entrada' => addslashes($this->getIdEntrada()),
            'id_folha' => addslashes($this->getIdFolha()),
            'folha' => addslashes($this->getFolha()),
            'data_lancamento' => addslashes($this->getDataLancamento()),
            'historico' => addslashes($this->getHistorico()),
            'contabil' => addslashes($this->getContabil()),
            'status' => addslashes($this->getStatus()),
            'trava_contabil' => addslashes($this->getTravaContabil()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_lancamento SET " . implode(", ", ($camposUpdate)) . " WHERE id_lancamento = {$this->getIdLancamento()} LIMIT 1;");

        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function updateTrava($id_projeto, $inicio,$final) {
//        print_array($id_projeto.' - '.$inicio.' - '.$final);
        $this->limpaQuery();
        $ini = implode('-',array_reverse(explode('/', $inicio)));
        $fim = implode('-',array_reverse(explode('/', $final)));
  //      print_array($id_projeto.' - '.$ini.' - '.$fim);
        
        $this->setQUERY("UPDATE contabil_lancamento SET trava_contabil = 1, trava = NOW() WHERE id_projeto = '$id_projeto' AND YEAR(data_lancamento) = YEAR('$exercicio') AND MONTH(data_lancamento) = MONTH('$exercicio')");
        if ($this->setRs()) {
            return 1;
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

    public function updateDesTrava($id_projeto, $periodo) {
        $this->limpaQuery();
        $exercicio = implode('-',array_reverse(explode('/', $periodo)));
        
        $this->setQUERY("UPDATE contabil_lancamento SET trava_contabil = 0, trava = NULL WHERE id_projeto = '$id_projeto' AND YEAR(data_lancamento) = YEAR('$exercicio') AND MONTH(data_lancamento) = MONTH('$exercicio')");

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

        $this->setQUERY("INSERT INTO contabil_lancamento ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdLancamento(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_lancamento SET status = 0 WHERE id_lancamento = {$this->getIdLancamento()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_lancamento WHERE id_lancamento = {$this->getIdLancamento()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    // ----- classe antiga -----------------------------------------------------

    public function getLancamentos($toArray = false) {
        $array = $this->makeCampos();

        $array = array_filter($array);

        foreach ($array as $key => $value) {
            $condicoes[] = "$key = '$value'";
        }

        $this->limpaQuery();
        $this->setWHERE(implode(' AND ', $condicoes));
        $this->setORDER("data_lancamento ASC");

        if ($this->setRs()) {
            if ($toArray) {
                while ($row = mysql_fetch_assoc($this->rs)) {
                    $arrayX[$row['data_lancamento']][$row['id_lancamento']] = $row;
                }
                return $arrayX;
            } else {
                return 1;
            }
        } else {
            return 0; //$this->setError(mysql_error());
        }
    }

//    
    public function encerramento($projeto, $conta, $valor, $historico) {
//        print_array($projeto . ' - ' . $conta . ' - ' . $valor . ' - ' . $historico);
        $qry_lancamento = "INSERT INTO contabil_lancamento (id_lote, id_projeto, id_usuario, data_lancamento, status)
                    VALUES ('{$lote}','{$projeto}','{$id_usuario}','{$dataLancamento}','1')";

        $result = mysql_query($qry_lancamento);
        return ($result) ? array('status' => TRUE, 'id_lancamento' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao salvar Classificação!');
    }

//
//    public function salvaLancamentoMultiplos($lote, $projeto, $id_usuario, $dataLancamento) {
//        $qry_lancamento = "INSERT INTO contabil_lancamento (id_lote, id_projeto, id_usuario, data_lancamento, status)
//                    VALUES ('{$lote}','{$projeto}','{$id_usuario}','{$dataLancamento}','1')";
//
//        $result = mysql_query($qry_lancamento);
//        return ($result) ? array('status' => TRUE, 'id_lancamento' => mysql_insert_id()) : array('status' => FALSE, 'msg' => 'Erro ao salvar Classificação!');
//    }

    public function preparaArrayItens($item_lancamento) {
        $qry_lancamentos = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, tipo, status, documento, historico)
                    VALUES ('{$item_lancamento['id_lancamento']}','{$item_lancamento['id_conta']}','{$item_lancamento['valor']}','{$item_lancamento['tipo']}','1','{$item_lancamento['documento']}','{$item_lancamento['historico']}')";

        $result = mysql_query($qry_lancamentos);
        return ($result);
    }

    public function inserirLancamento($array) {

        $keys = implode(',', array_keys($array));
        $values = implode("', '", $array);

        $insert = "INSERT INTO contabil_lancamento ($keys) VALUES ('" . $values . "');";
//        echo $insert."<br>";
        mysql_query($insert) or file_put_contents('Erro inserirLancamento: ' . $insert . ' - ' . mysql_error());
        return mysql_insert_id();
    }

    public function inserirItensLancamento($array) {
        foreach ($array as $itens) {
            $keys = implode(',', array_keys($itens));
            $values[] = "('" . implode("' , '", $itens) . "')";
        }
        $insert = "INSERT INTO contabil_lancamento_itens ($keys) VALUES " . implode(', ', $values) . ";";
        $result = mysql_query($insert) or file_put_contents('Erro inserirItensLancamento: '. $insert. ' - '. mysql_error());

        return ($result) ? mysql_insert_id() : FALSE;
    }

    public function listaLancamentos($dados) {

        $query = "SELECT B.id_lancamento, B.data_lancamento, A.id_lancamento_itens, C.acesso, A.valor, A.tipo, A.historico hist_lancamento_itens, B.`status`, A.`status`, B.historico hist_lancamento, C.descricao, C.classificador
                FROM contabil_lancamento_itens A
                INNER JOIN contabil_lancamento B ON (B.id_lancamento = A.id_lancamento AND B.status != 0)
                LEFT JOIN contabil_planodecontas C ON (C.id_conta = A.id_conta)
                WHERE B.`status` != 0 AND A.`status` != 0 AND B.id_projeto = '{$dados['id_projeto']}' AND SUBSTRING(B.data_lancamento,6,2) = {$dados['mes']} AND SUBSTRING(B.data_lancamento,1,4) = {$dados['ano']}
                ORDER BY B.data_lancamento, B.id_lancamento, A.id_lancamento_itens, A.tipo DESC";

        $result = mysql_query($query);
        while ($row = mysql_fetch_assoc($result)) {
            $x[$row['data_lancamento']][$row['id_lancamento']]['hist_lancamento']  = $row['hist_lancamento'];
            $x[$row['data_lancamento']][$row['id_lancamento']]['lan'][$row['id_lancamento_itens']]  = $row;
            
        }
        return $x;
    }
    
    public function retornaTrava($projeto, $inicio, $final) {
        $ini = implode('-',array_reverse(explode('/', $inicio)));
        $fin  = implode('-',array_reverse(explode('/', $final))); 

        $sql = "/*SELECT RPAD(
REPLACE(
REPLACE(A.data_lancamento,'-',''),'-',''),'8','0') indice, COUNT(A.id_lancamento) lancamentos, CONCAT(MONTH(A.data_lancamento),'/', YEAR(A.data_lancamento)) AS exercicio, A.id_projeto, A.trava_contabil
FROM contabil_lancamento A
WHERE A.id_projeto = '$projeto' AND A.data_lancamento BETWEEN '$ini' AND '$fin' AND A.`status`= 1 AND A.trava_contabil = 0
GROUP BY exercicio 
UNION ALL 
SELECT RPAD(
REPLACE(
REPLACE(A.data_lancamento,'-',''),'-',''),'8','0') indice, COUNT(A.id_lancamento) lancamentos, CONCAT(MONTH(A.data_lancamento),'/', YEAR(A.data_lancamento)) AS exercicio, A.id_projeto, A.trava_contabil
FROM contabil_lancamento A
WHERE A.id_projeto = '$projeto' AND A.data_lancamento BETWEEN '$ini' AND '$fin' AND A.`status`= 1 AND A.trava_contabil = 1
GROUP BY exercicio
ORDER BY indice DESC*/"
                . ""
                . "SELECT RPAD(REPLACE(REPLACE(A.data_lancamento,'-',''),'-',''),'8','0') indice, A.id_lancamento, A.`status`, A.trava_contabil
FROM contabil_lancamento A WHERE A.id_projeto = 3304 AND A.`status` = 1 AND A.data_lancamento BETWEEN '2016-12-01' AND '2016-12-31'
ORDER BY indice ASC, A.id_lancamento";
        $result = mysql_query($sql) or file_put_contents('Erro retornaTrava: '. $sql. ' - '. mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
             $l[] = $row;
            
        }
        return $l;
    }

    public function retornaTravaA($projeto, $inicio, $final) {
        $ini = implode('-',array_reverse(explode('/', $inicio)));
        $fin  = implode('-',array_reverse(explode('/', $final))); 

        $sql = "SELECT RPAD(REPLACE(REPLACE(A.data_lancamento,'-',''),'-',''),'8','0') indice, A.id_lancamento, A.`status`, A.trava_contabil
FROM contabil_lancamento A WHERE A.id_projeto = '$projeto' AND A.data_lancamento BETWEEN '$ini' AND '$fin' AND A.`status`= 1
ORDER BY indice ASC, A.id_lancamento";
        $result = mysql_query($sql) or file_put_contents('Erro retornaTravaA: ' . $sql . ' - ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
             $l[] = $row;
            
        }
        return $l;
    }

    public function salvar() {
        if (empty($this->id_lancamento)) {
            return $this->insert();
        } else {
            return $this->update();
        }
    }

    public function gera_lancamento_nfse_assoc($id_nfse, $id_lancamento) {
        $query = "INSERT INTO nfse_lancamentos_assoc (id_nfse,id_lancamento) VALUES ($id_nfse,$id_lancamento);";
        return mysql_query($query) or die('Erro ao lancar nfse_lancamentos_assoc: '.$query.' - '.mysql_error());
}
    
    public function excluir_lancamentos_assoc_nfse($id_nfse){
        $query = "UPDATE contabil_lancamento SET status = 0 WHERE id_lancamento IN(SELECT id_lancamento FROM nfse_lancamentos_assoc WHERE id_nfse = $id_nfse)";
        return mysql_query($query) or die('Error ao excluir lancamento: '.$query.' - '.mysql_error());
    }

}
