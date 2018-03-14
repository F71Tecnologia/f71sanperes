<?php   

class AssocContasClass { 

    protected $id_assoc;
    protected $id_contabil;
    protected $id_conta;
    protected $data;
    protected $id_funcionario;
    protected $status;
    protected $id_projeto;
    protected $id_fornecedor;
    protected $folha;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_contas_assoc ';
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
    function getIdAssoc() {
        return $this->id_assoc;
    }

    function getIdContabil() {
        return $this->id_contabil;
    }

    function getIdConta() {
        return $this->id_conta;
    }

    function getData($formato = null) {
        if (empty($this->data) || $this->data == '0000-00-00') {
            return '';
        } else {
            return (!empty($formato)) ? date_format(date_create($this->data), $formato) : $this->data;
        }
    }

    function getIdFuncionario() {
        return $this->id_funcionario;
    }

    function getStatus() {
        return $this->status;
    }

    function getIdProjeto() {
        return $this->id_projeto;
    }

    function getIdFornecedor() {
        return $this->id_fornecedor;
    }

    function getFolha() {
        return $this->folha;
    }

    //SET's DA CLASSE
    function setIdAssoc($id_assoc) {
        $this->id_assoc = $id_assoc;
    }

    function setIdContabil($id_contabil) {
        $this->id_contabil = $id_contabil;
    }

    function setIdConta($id_conta) {
        $this->id_conta = $id_conta;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setIdFuncionario($id_funcionario) {
        $this->id_funcionario = $id_funcionario;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setIdProjeto($id_projeto) {
        $this->id_projeto = $id_projeto;
    }

    function setIdFornecedor($id_fornecedor) {
        $this->id_fornecedor = $id_fornecedor;
    }

    function setFolha($folha) {
        $this->folha = $folha;
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
        $this->id_assoc = null;
        $this->id_contabil = null;
        $this->id_conta = null;
        $this->data = null;
        $this->id_funcionario = null;
        $this->status = null;
        $this->id_projeto = null;
        $this->id_fornecedor = null;
        $this->folha = null;
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
        $this->setFROM(' contabil_contas_assoc ');
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
            $this->setIdAssoc($this->row['id_assoc']);
            $this->setIdContabil($this->row['id_contabil']);
            $this->setIdConta($this->row['id_conta']);
            $this->setData($this->row['data']);
            $this->setIdFuncionario($this->row['id_funcionario']);
            $this->setStatus($this->row['status']);
            $this->setIdProjeto($this->row['id_projeto']);
            $this->setIdFornecedor($this->row['id_fornecedor']);
            $this->setFolha($this->row['folha']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'id_contabil' => addslashes($this->getIdContabil()),
            'id_conta' => addslashes($this->getIdConta()),
            'data' => addslashes($this->getData()),
            'id_funcionario' => addslashes($this->getIdFuncionario()),
            'status' => addslashes($this->getStatus()),
            'id_projeto' => addslashes($this->getIdProjeto()),
            'id_fornecedor' => addslashes($this->getIdFornecedor()),
            'folha' => addslashes($this->getFolha()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_contas_assoc SET " . implode(", ", ($camposUpdate)) . " WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_contas_assoc ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdAssoc(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_contas_assoc SET status = 0 WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_contas_assoc WHERE id_assoc = {$this->getIdAssoc()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function consultabanco($id_projeto) {
        $qryEntra = mysql_query("SELECT * FROM bancos A WHERE A.id_projeto IN(0,{$_REQUEST['projeto']}) AND A.status_reg != 0");
        $OpcaoBanco[" "] = " ";

        while ($dados = mysql_fetch_array($qryEntra)) {
            $OpcaoBanco[$dados['id_banco']] = $dados['nome'].' ( '.$dados['conta'].' )';
        }
        return $OpcaoBanco;
    }

    public function consultaentrada() {
        $qryEntra = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1 AND grupo = 5");
        $OpcaoReceita[" "] = " ";

        while ($dados = mysql_fetch_array($qryEntra)) {
            $OpcaoReceita[$dados['id_entradasaida']] = $dados['nome'].' ( '.$dados['id_entradasaida'].' )';
        }
        return $OpcaoReceita;
    }

    public function consultafornecedor($id_projeto) {
        $qryEntra = mysql_query("SELECT * FROM prestadorservico WHERE id_projeto = '{$id_projeto}' AND status = 1 ORDER BY c_razao");
        $OpcaoFornecedor[" "] = " ";

        while ($dados = mysql_fetch_array($qryEntra)) {
            $OpcaoFornecedor[$dados['id_prestador']] = $dados['c_razao'].' ( '.$dados['id_prestador'].' )' ;
        }
        return $OpcaoFornecedor;
    }
    
    public function consultasaida() {
        $qryEntra = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1 AND grupo > 5 ORDER BY grupo ASC");
        $OpcaoDespesa[" "] = " ";

        while ($dados = mysql_fetch_array($qryEntra)) {
            $OpcaoDespesa[$dados['id_entradasaida']] = $dados['id_entradasaida'].' - '.$dados['nome'];
        }
        return $OpcaoDespesa;
    }
    
    public function consultafolha($folha) {
        switch ($folha) {
            case 1:
                $qryFolha = mysql_query("SELECT A.cod, A.id_mov, A.descicao FROM rh_movimentos A WHERE A.incidencia LIKE 'FOLHA' GROUP BY A.cod ORDER BY A.cod");
                $OpcaoFolha[" "] = " ";
                    
                while ($dados = mysql_fetch_assoc($qryFolha)) {
                    $OpcaoFolha[$dados['cod']] = '( '.$dados['cod'].' ) '.$dados['descicao'];
                }
                return $OpcaoFolha;        
            break;
            case 2:
                $qryFolha = mysql_query("SELECT A.cod, A.id_mov, A.descicao FROM rh_movimentos A WHERE A.incidencia LIKE 'FERIAS' GROUP BY A.cod ORDER BY A.cod");
                $OpcaoFolha = array(
                    ' ' => '',
                    '15039' => '( 15039 ) PENSÃO ALIMENTÍCIA',
                    '15038' => '( 15038 ) PENSÃO ALIMENTÍCIA'
                );
                    
                while ($dados = mysql_fetch_assoc($qryFolha)) {
                    $OpcaoFolha[$dados['cod']] = '( '.$dados['cod'].' ) '.$dados['descicao'];
                }
                return $OpcaoFolha;        
            break;
            case 3:
                $qryFolha = mysql_query("SELECT id_mov, nome_movimento, status FROM rh_movimentos_rescisao WHERE id_mov != 0 AND id_mov > 60 AND id_mov NOT IN(63) GROUP BY id_mov");
    
                $OpcaoFolha = array(
                    ' '  => '',
                    '50' => '( 50 ) SALDO DE SALÁRIO',
                    '51' => '( 51 ) COMISSÕES',
                    '52' => '( 52 ) GRATIFICAÇÕES ',
                    '53' => '( 53 ) ADICIONAL DE INSALUBRIDADE ',
                    '54' => '( 54 ) ADICIONAL DE PERICULOSIDADE',
                    '55' => '( 55 ) ADICIONAL NOTURNO',
                    '56' => '( 56 ) HORAS EXTRAS',
                    '57' => '( 57 ) GORJETAS',
                    '58' => '( 58 ) DESCANÇO SEMANAL REMUNERADO (DSR)',
                    '59' => '( 59 ) REFLEXO DO "DSR" SOBRE SALARIO VARIÁVEL',
                    '60' => '( 60 ) MULTA ART 477, 8ºCLT ',
                    '62' => '( 62 ) SALÁRIO-FAMÍLIA',
                    '63' => '( 63 ) 13º SALARIO PROPORCIONAL',
                    '100' => '( 100 ) PENSÃO ALIMENTÍCIA',
                    '101' => '( 101 ) ADIANTAMENTO SALARIAL',
                    '102' => '( 102 ) ADIANTAMENTO 13º SALÁRIO',
                    '103' => '( 103 ) AVISO-PRÉVIO INDENIZADO',
                    '104' => '( 104 ) MULTA ART 480 CLT',
                    '105' => '( 105 ) EMPRÉSTIMO EM CONSIGNAÇÃO',
                    '116' => '( 116 ) IRRF FÉRIAS',
                    '117' => '( 117 ) FALTAS (48HORAS)',
                    '1121' => '( 1121 ) PREVIDÊNCIA SOCIAL',
                    '1122' => '( 1122 ) PREVIDÊNCIA SOCIAL 13º SALÁRIO',
                    '1141' => '( 1141 ) IRRF',
                    '1142' => '( 1142 ) IRRF SOBRE 13º SALÁRIO',
                );
                
                while ($dados = mysql_fetch_assoc($qryFolha)) {
                    $OpcaoFolha[$dados['id_mov']] = '( '.$dados['id_mov'].' ) '.$dados['nome_movimento'];
                }
                return $OpcaoFolha;        
            break;
        }
    }
    
    public function consulta(){
        $this->limpaQuery();
        
        $this->setWHERE("id_conta = '{$this->getIdConta()}' AND id_contabil = '{$this->getIdContabil()}' AND id_projeto = '{$this->getIdProjeto()}' AND id_fornecedor = '{$this->getIdFornecedor()}'");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    } 
    
    public function getPlanoContaFull($projeto) {
        $sql = "SELECT A1.id_assoc AS id, C1.acesso AS acesso, C1.descricao AS classificacao, B1.cod AS codigo, B1.descicao AS descricao, '' AS id_fornecedor, '' AS fornecedor
                    FROM contabil_contas_assoc A1
                    INNER JOIN rh_movimentos B1 ON B1.cod = A1.id_conta
                    INNER JOIN contabil_planodecontas C1 ON C1.id_conta = A1.id_contabil
                    WHERE C1.id_projeto = '{$projeto}' AND A1.status = 1 AND A1.folha IN(1,2,3)
                    GROUP BY B1.cod, A1.folha 
                    UNION
                    SELECT A2.id_assoc AS id, C2.acesso AS acesso, C2.descricao AS classificacao, B2.id_entradasaida AS codigo, B2.nome AS descricao, D2.id_prestador AS id_fornecedor, D2.c_razao AS fornecedor
                    FROM contabil_contas_assoc A2
                    INNER JOIN entradaesaida B2 ON B2.id_entradasaida = A2.id_conta
                    INNER JOIN contabil_planodecontas C2 ON C2.id_conta = A2.id_contabil
                    LEFT JOIN prestadorservico D2 ON D2.id_prestador = A2.id_fornecedor
                    WHERE C2.id_projeto = '{$projeto}' AND A2.status = 1 AND A2.folha = 0
                    UNION
                    SELECT A3.id_assoc AS id, C3.acesso AS acesso, C3.descricao AS classificacao, B3.id_banco AS codigo, CONCAT(B3.nome, ' - C/C ',B3.conta) AS descricao, '', '' AS fornecedor
                    FROM contabil_contas_assoc_banco AS A3
                    INNER JOIN bancos AS B3 ON B3.id_banco = A3.id_banco
                    INNER JOIN contabil_planodecontas AS C3 ON C3.id_conta = A3.id_conta
                    WHERE C3.id_projeto = '{$projeto}' AND A3.status = 1
                    ORDER BY acesso";
            
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[] = $row;
        }

        return $array;
    }

}