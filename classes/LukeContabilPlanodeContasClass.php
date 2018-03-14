<?php
require_once('ContabilLoteClass.php');
require_once('ContabilContasSaldoClass.php');

class LukeContabilPlanodeContasClass {
    
       protected $id_conta;
    protected $classificador;
    protected $acesso;
    protected $classificacao;
    protected $natureza;
    protected $descricao;
    protected $id_projeto;
    protected $cta_superior;
    protected $cta_encerramento;
    protected $status;
    protected $id_historico;
    protected $sped;
    protected $nivel;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' luke_planodecontas ';
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
    function getIdConta() {
        return $this->id_conta;
    }

    function getClassificador() {
        return $this->classificador;
    }

    function getAcesso() {
        return $this->acesso;
    }

    function getClassificacao() {
        return $this->classificacao;
    }

    function getNatureza() {
        return $this->natureza;
    }

    function getDescricao() {
        return $this->descricao;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getCtaSuperior() {
        return $this->cta_superior;
    }

    function getCtaEncerramento() {
        return $this->cta_encerramento;
    }

    function getStatus() {
        return $this->status;
    }

    function getIdHistorico() {
        return $this->id_historico;
    }

    function getSped() {
        return $this->sped;
    }

    function getNivel() {
        return $this->nivel;
    }

    //SET's DA CLASSE
    function setIdConta($id_conta) {
        $this->id_conta = $id_conta;
    }

    function setClassificador($classificador) {
        $this->classificador = $classificador;
    }

    function setAcesso($acesso) {
        $this->acesso = $acesso;
    }

    function setClassificacao($classificacao) {
        $this->classificacao = $classificacao;
    }

    function setNatureza($natureza) {
        $this->natureza = $natureza;
    }

    function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setCtaSuperior($cta_superior) {
        $this->cta_superior = $cta_superior;
    }

    function setCtaEncerramento($cta_encerramento) {
        $this->cta_encerramento = $cta_encerramento;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setIdHistorico($id_historico) {
        $this->id_historico = $id_historico;
    }

    function setSped($sped) {
        $this->sped = $sped;
    }

    function setNivel($nivel) {
        $this->nivel = $nivel;
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
        $this->id_conta = null;
        $this->classificador = null;
        $this->acesso = null;
        $this->classificacao = null;
        $this->natureza = null;
        $this->descricao = null;
        $this->id_projeto = null;
        $this->cta_superior = null;
        $this->cta_encerramento = null;
        $this->status = null;
        $this->id_historico = null;
        $this->sped = null;
        $this->nivel = null;
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
        $this->setFROM(' luke_planodecontas ');
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
            $this->setIdConta($this->row['id_conta']);
            $this->setClassificador($this->row['classificador']);
            $this->setAcesso($this->row['acesso']);
            $this->setClassificacao($this->row['classificacao']);
            $this->setNatureza($this->row['natureza']);
            $this->setDescricao($this->row['descricao']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setCtaSuperior($this->row['cta_superior']);
            $this->setCtaEncerramento($this->row['cta_encerramento']);
            $this->setStatus($this->row['status']);
            $this->setIdHistorico($this->row['id_historico']);
            $this->setSped($this->row['sped']);
            $this->setNivel($this->row['nivel']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'classificador' => addslashes($this->getClassificador()),
            'acesso' => addslashes($this->getAcesso()),
            'classificacao' => addslashes($this->getClassificacao()),
            'natureza' => addslashes($this->getNatureza()),
            'descricao' => addslashes($this->getDescricao()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'cta_superior' => addslashes($this->getCtaSuperior()),
            'cta_encerramento' => addslashes($this->getCtaEncerramento()),
            'status' => addslashes($this->getStatus()),
            'id_historico' => addslashes($this->getIdHistorico()),
            'sped' => addslashes($this->getSped()),
            'nivel' => addslashes($this->getNivel()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE luke_planodecontas SET " . implode(", ", ($camposUpdate)) . " WHERE id_conta = {$this->getIdConta()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO luke_planodecontas ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdConta(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE luke_planodecontas SET status = 0 WHERE id_conta = {$this->getIdConta()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM luke_planodecontas WHERE id_conta = {$this->getIdConta()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function retorna($conta) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
//        $auxProjeto = (!empty($projeto)) ? " AND id_projeto IN(0,'{$projeto}') " : $projeto;
        $qry = "SELECT *, classificacao, classificador, descricao FROM luke_planodecontas WHERE REPLACE(classificador, '.', '') LIKE '{$conta}%' AND status = 1 AND sped = 1 ORDER BY classificador";
        
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }  

    public function getPlanoFull($projeto) {
        $sql = "SELECT * FROM luke_planodecontas A WHERE A.id_projeto IN(0,'{$projeto}') AND A.`status` = 1 ORDER BY A.classificador";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['classificador']] = $row;
        }

        return $array;
    }
    
    public function editar($conta,$projeto) {
        $sql = "SELECT * FROM luke_planodecontas A WHERE A.id_projeto = '{$projeto}' AND A.id_conta = '{$conta}'";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['id_conta']] = $row;
        }
        return $array;
    }
    
    public function retornaplano($conta, $descricao, $projeto = null) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxProjeto = (!empty($projeto)) ? " AND id_projeto = $projeto " : $projeto;
        $qry = "SELECT * FROM luke_planodecontas WHERE sped = 1 OR id_projeto = '{$projeto}' ORDER BY classificador";
        
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function alteracao($conta, $classificador, $pai, $reduzido, $nivel, $descricao, $natureza, $classificacao, $id_historico) {
    //$descricao = utf8_decode($descricao);
        $update = "UPDATE luke_planodecontas
                   SET classificador = '{$classificador}', cta_superior = '{$pai}', acesso = '{$reduzido}', nivel = '{$nivel}',
                   natureza = '{$natureza}', classificacao = '{$classificacao}', descricao = '{$descricao}', id_historico = '$id_historico'
                   WHERE id_conta = '{$conta}'";
        $result = mysql_query($update) or die('Erro' . mysql_error());

        return $result;
    }
    
    public function contasProjeto($id_projeto, $arrayContas) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $auxContas = (!empty($arrayContas)) ? " AND id_conta IN (".implode(', ', $arrayContas).")" : null;
        $qry = "SELECT * FROM luke_planodecontas WHERE id_projeto = $id_projeto  AND status = 1 $auxContas ORDER BY classificador";
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function retornaLancamento($conta, $projeto) {        
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $qry = "SELECT MAX(id_lancamento) FROM contabil_lancamento WHERE id_projeto = $projeto"; 
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function novaconta($codigo, $conta_pai, $classificador, $descricao, $classificacao, $natureza, $projeto) {
        $descricao = utf8_decode($descricao);
        $verificar = $this->verificarConta($classificador, $codigo, $projeto);
        $sped = $this->verificar_sped($classificador);
        if ($verificar) {
            return json_encode(array('status' => FALSE, 'msg' => 'Conta Ja Existente...!'));
        } else {
            if ($codigo == null) { 
                $sql_ultimo = "SELECT MAX(id_conta) FROM luke_planodecontas";
                $result = mysql_query($sql_ultimo) or die('Erro' . mysql_error());
                $dado = mysql_fetch_array($result);
                $codigo = $dado[0] + 1;
            }
            $nivel = count(explode('.', $classificador));
            $qry_novaCta = "INSERT INTO luke_planodecontas (acesso, cta_superior, classificador, descricao, classificacao, natureza, id_projeto, nivel, sped)
                            VALUES ('{$codigo}','{$conta_pai}','{$classificador}','{$descricao}','{$classificacao}','{$natureza}','{$projeto}','{$nivel}','0')";

            $result = mysql_query($qry_novaCta) or die(mysql_error());
            
            $id_conta = mysql_insert_id();
            
            $data = new DateTime(date('Y').sprintf('%02d',date('m'))."-01");
            
            return ($result) ? json_encode(array('status' => TRUE, 'id_conta' => $id_conta, 'msg' => 'Conta salva!')) : json_encode(array('status' => FALSE, 'msg' => 'Erro ao salvar Conta!'));
        }
    }
    
    public function verificar_sped($conta) {
        $query = "SELECT count(id_conta) as sped FROM luke_planodecontas WHERE classificador = '{$conta}' AND sped = 1";
        $teste = mysql_fetch_assoc(mysql_query($query));
        if ($teste['sped'] == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function verificarConta($conta, $acesso, $empresa) {
        $query = "SELECT count(id_conta) AS c FROM luke_planodecontas WHERE classificador = '{$conta}' AND acesso = '{$acesso}' AND id_projeto = '{$empresa}'";
        $teste = mysql_fetch_assoc(mysql_query($query));
        if ($teste['c'] == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function cancelar($conta) {
        $update = "UPDATE luke_planodecontas SET status = 0 WHERE id_conta = '{$conta}'";
        $result = mysql_query($update) or die('Erro' . mysql_error());

        return $result;
    }

    public function retorna_conta_pai($conta, $projeto = null) {
        mysql_query("SET NAMES 'utf8'");
        mysql_query('SET character_set_connection=utf8');
        mysql_query('SET character_set_client=utf8');
        mysql_query('SET character_set_results=utf8');
        $qry = "SELECT id_conta, classificador FROM luke_planodecontas WHERE classificador LIKE '{$conta}' AND status = 1 ORDER BY classificador";
       
        $result = mysql_query($qry) or die('Erro ao consultar Plano de Contas: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
        return $return;
    }
    
    public function niveisContas($inProjetos) {
        $sql = "SELECT classificador, descricao, classificacao, nivel FROM luke_planodecontas WHERE id_projeto = 0 AND status = 1 ORDER BY classificador ASC";
        $result = mysql_query($sql) or die('Erro: ' . mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            $return[] = $row;
        }
//        print_array($return);
        return $return;
    }
    
    public function getPlanoAcesso($projeto) {
        $sql = "SELECT * FROM luke_planodecontas A WHERE A.id_projeto = '{$projeto}' AND A.`status` = 1 AND A.classificacao = 'A' ORDER BY classificador"; 
        $array[" "] = " ";
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['id_conta']] = $row['acesso'].' | '.$row['descricao'].' | ('. $row['classificador'].')';        
        }

        return $array;
    }
    
    public function inserirLancamento($array) {
        
//        if(array_key_exists('id_saida', $array)){
//            $array['historico'] = mysql_result(mysql_query("SELECT B.nome FROM saida A LEFT JOIN entradaesaida B ON (A.classificacao = B.id_entradasaida) WHERE A.id_saida = {$array['id_saida']} LIMIT 1"), 0);
//        } else {
//            $array['historico'] = mysql_result(mysql_query("SELECT B.nome FROM entrada A LEFT JOIN entradaesaida B ON (A.classificacao = B.id_entradasaida) WHERE A.id_entrada = {$array['id_entrada']} LIMIT 1"), 0);
//        }
        
        $keys = implode(',', array_keys($array));
        $values = implode("', '", $array);

        $insert = "INSERT INTO contabil_lancamento ($keys) VALUES ('" . $values . "');";
//        echo $insert."<br>";
        mysql_query($insert) or die('Erro' . mysql_error());
        return mysql_insert_id();
    }
    
    public function listar($id_projeto){
        
    }

    public function updateLancamentoContabil($id_lancamento) {
        
        //Rogerio disse para usar a data de pagamento (18/09/2015 09:17:37)
        //Ramon disse para usar a data de vencimento (18/09/2015 11:30:00)
        
        $sqlSaida = mysql_fetch_assoc(mysql_query("SELECT *, MONTH(data_pg) mes_vencimento, YEAR(data_pg) ano_vencimento FROM saida A INNER JOIN contabil_lancamento B ON (A.id_saida = B.id_saida AND B.id_lancamento = {$id_lancamento}) LIMIT 1;"));
        $sqlEntrada = mysql_fetch_assoc(mysql_query("SELECT *, MONTH(data_pg) mes_vencimento, YEAR(data_pg) ano_vencimento FROM entrada A INNER JOIN contabil_lancamento B ON (A.id_entrada = B.id_entrada AND B.id_lancamento = {$id_lancamento}) LIMIT 1;"));
        
        if(is_array($sqlSaida)){
            $array = $sqlSaida;
        } else if(is_array($sqlEntrada)){
            $array = $sqlEntrada;
        }
        $nomeProjeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = {$array['id_projeto']} LIMIT 1"), 0);
        
        $objLote = new ContabilLoteClass();
        $objLote->setIdProjeto($array['id_projeto']);
        $objLote->setMes($array['mes_vencimento']);
        $objLote->setAno($array['ano_vencimento']);
        $objLote->setStatus(1);
        $objLote->setUsuarioCriacao($_COOKIE['logado']);
        $objLote->setDataCriacao(date('Y-m-d H:i:s'));
        $objLote->setLoteNumero("$nomeProjeto ".sprintf("%02d",$array['mes_vencimento'])."/{$array['ano_vencimento']} - FINANCEIRO");
        $objLote->setTipo(6);
        $objLote->verificaLote();
        
        $update = "UPDATE contabil_lancamento SET contabil = 2, data_lancamento = '{$array['data_vencimento']}', id_lote = '{$objLote->getIdLote()}' WHERE id_lancamento = '$id_lancamento' LIMIT 1;";
        mysql_query($update) or die('Erro' . mysql_error());
    }
 
    public function inserirItensLancamento($array) {
        
        foreach ($array as $itens) {
            
            if($itens['id_banco']){
                $sql =  "SELECT *, id_conta AS conta FROM contabil_contas_assoc_banco WHERE id_banco = {$itens['id_banco']} AND status = 1 LIMIT 1" ;
            } elseif($itens['fornecedor']){
                $sql = "SELECT A.*, A.id_contabil AS conta FROM contabil_contas_assoc A INNER JOIN luke_planodecontas B ON (A.id_contabil = B.id_conta AND B.id_projeto = {$itens['id_projeto']}) WHERE A.id_fornecedor = {$itens['fornecedor']} AND A.status = 1 LIMIT 1";
            } elseif($itens['tipo']){
                $sql = "SELECT A.*, A.id_contabil AS conta FROM contabil_contas_assoc A INNER JOIN luke_planodecontas B ON (A.id_contabil = B.id_conta AND B.id_projeto = {$itens['id_projeto']}) WHERE A.id_conta = {$itens['id_conta']} AND A.status = 1 LIMIT 1";
            }
            $rowConta = mysql_fetch_assoc(mysql_query($sql));
            
            unset($itens['id_banco'],$itens['id_conta'],$itens['fornecedor'],$itens['id_projeto']);
            
            $keys = implode(',', array_keys($itens));
            $values = "('" . implode("' , '", $itens) . "', '{$rowConta['conta']}')";
            
            $insert = "INSERT INTO contabil_lancamento_itens ($keys, id_conta) VALUES $values;";
            $result = mysql_query($insert) or die('Erro' . mysql_error());
            
        }
        return ($result) ? $id_lancamento_itens : FALSE;
    }

    public function inserirItensMovimentacaoFinanceira($array) {
        foreach ($array as $itens) {
            
            if($itens['id_banco']){
                $sql =  "SELECT *, id_conta AS conta FROM contabil_contas_assoc_banco WHERE id_banco = {$itens['id_banco']} AND status = 1 LIMIT 1" ;
            } elseif($itens['banco']){
                $sql = "SELECT A.*, A.id_contabil AS conta FROM contabil_contas_assoc A INNER JOIN luke_planodecontas B ON (A.id_contabil = B.id_conta AND B.id_projeto = {$itens['id_projeto']}) WHERE A.conta_projeto = {$itens['banco']} AND A.status = 1 LIMIT 1";
            }
            $rowConta = mysql_fetch_assoc(mysql_query($sql));
            unset($itens['id_banco'],$itens['id_tipo'],$itens['banco'],$itens['id_projeto']);
            
            $keys = implode(',', array_keys($itens));
            $values = "('" . implode("' , '", $itens) . "', '{$rowConta['conta']}')";
            
            $insert = "INSERT INTO contabil_lancamento_itens ($keys, id_conta) VALUES $values;";
            $result = mysql_query($insert) or die('Erro' . mysql_error());
            
            $id_lancamento_itens = mysql_insert_id();
        }
        
        return ($result) ? $id_lancamento_itens : FALSE;
    }
    
}
