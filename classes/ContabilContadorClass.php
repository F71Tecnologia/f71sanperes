<?php
class ContabilContadorClass { 

    protected $id_contador;
    protected $nome;
    protected $cadastro;
    protected $crc;
    protected $crc_control;
    protected $crc_uf;
    protected $cpf;
    protected $profissional;
    protected $status;
    protected $tel_comercial;
    protected $tel_celular;
    protected $email;

    protected $QUERY;
    protected $SELECT = ' * ';
    protected $FROM = ' contabil_contador ';
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
    function getIdContador() {
        return $this->id_contador;
    }

    function getNome() {
        return $this->nome;
    }

    function getCadastro() {
        return $this->cadastro;
    }

    function getCrc() {
        return $this->crc;
    }

    function getCrcControl() {
        return $this->crc_control;
    }

    function getCrcUf() {
        return $this->crc_uf;
    }

    function getCpf($limpo = false) {
        return ($limpo) ? str_replace(array('.','-','/'), '' ,$this->cpf) : $this->cpf;
    }

    function getProfissional() {
        return $this->profissional;
    }

    function getStatus() {
        return $this->status;
    }

    function getTelComercial() {
        return $this->tel_comercial;
    }

    function getTelCelular() {
        return $this->tel_celular;
    }

    function getEmail() {
        return $this->email;
    }

    //SET's DA CLASSE
    function setIdContador($id_contador) {
        $this->id_contador = $id_contador;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setCadastro($cadastro) {
        $this->cadastro = $cadastro;
    }

    function setCrc($crc) {
        $this->crc = $crc;
    }

    function setCrcControl($crc_control) {
        $this->crc_control = $crc_control;
    }

    function setCrcUf($crc_uf) {
        $this->crc_uf = $crc_uf;
    }

    function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    function setProfissional($profissional) {
        $this->profissional = $profissional;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setTelComercial($tel_comercial) {
        $this->tel_comercial = $tel_comercial;
    }

    function setTelCelular($tel_celular) {
        $this->tel_celular = $tel_celular;
    }

    function setEmail($email) {
        $this->email = $email;
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
        $this->id_contador = null;
        $this->nome = null;
        $this->cadastro = null;
        $this->crc = null;
        $this->crc_control = null;
        $this->crc_uf = null;
        $this->cpf = null;
        $this->profissional = null;
        $this->status = null;
        $this->tel_comercial = null;
        $this->tel_celular = null;
        $this->email = null;
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
        $this->setFROM(' contabil_contador ');
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
            $this->setIdContador($this->row['id_contador']);
            $this->setNome($this->row['nome']);
            $this->setCadastro($this->row['cadastro']);
            $this->setCrc($this->row['crc']);
            $this->setCrcControl($this->row['crc_control']);
            $this->setCrcUf($this->row['crc_uf']);
            $this->setCpf($this->row['cpf']);
            $this->setProfissional($this->row['profissional']);
            $this->setStatus($this->row['status']);
            $this->setTelComercial($this->row['tel_comercial']);
            $this->setTelCelular($this->row['tel_celular']);
            $this->setEmail($this->row['email']);
            return 1;
        } else {
            //$this->setError(mysql_error());
            return 0;
        }
    }

    private function makeCampos() {

        $array = array(
            'nome' => addslashes(utf8_decode($this->getNome())),
            'cadastro' => addslashes($this->getCadastro()),
            'crc' => addslashes($this->getCrc()),
            'crc_control' => addslashes($this->getCrcControl()),
            'crc_uf' => addslashes($this->getCrcUf()),
            'cpf' => addslashes($this->getCpf()),
            'profissional' => addslashes($this->getProfissional()),
            'status' => addslashes($this->getStatus()),
            'tel_comercial' => addslashes($this->getTelComercial()),
            'tel_celular' => addslashes($this->getTelCelular()),
            'email' => addslashes($this->getEmail()),
        );

        return $array;
    }

    public function update() {
        $this->limpaQuery();
        $array = $this->makeCampos();

        foreach ($array as $key => $value) {
            $camposUpdate[] = "$key = '$value'";
        }
        $this->setQUERY("UPDATE contabil_contador SET " . implode(", ", ($camposUpdate)) . " WHERE id_contador = {$this->getIdContador()} LIMIT 1;");

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

        $this->setQUERY("INSERT INTO contabil_contador ($keys) VALUES ('$values');");
        if ($this->setRs()) {
            $this->setIdContador(mysql_insert_id());
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function inativa() {
        $this->limpaQuery();

        $this->setQUERY("UPDATE contabil_contador SET status = 0 WHERE id_contador = {$this->getIdContador()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function deleta() {
        $this->limpaQuery();

        $this->setQUERY("DELETE FROM contabil_contador WHERE id_contador = {$this->getIdContador()} LIMIT 1;");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }
    
    public function consulta(){
        $this->limpaQuery();
        $this->setWHERE("cpf = '{$this->getCpf()}'");
        if ($this->setRs()) {
            return 1;
        } else {
            die(mysql_error());
        }
    }

    public function listar() {
        $sql = ("SELECT * FROM contabil_contador WHERE status = 1");
        $qry = mysql_query($sql);
        while($row = mysql_fetch_assoc($qry)){
            $array[$row['id_contador']] = $row;
        }

        return $array;
    }
    
    public function retorna_contador($id) {
        $sql = ("SELECT * FROM contabil_contador WHERE status = 1 AND id_contador = '{$id}'");
        $qry = mysql_query($sql);
        return $row = mysql_fetch_assoc($qry);
    }
                    
    public function alteracao($id, $cpf, $crc_uf, $crc, $crc_control, $nome, $telefone, $celular, $email, $profissional) {
        $nome = utf8_decode($nome);
        $update = "UPDATE contabil_contador
                SET cpf = '{$cpf}', crc = '{$crc}', profissional = '{$profissional}',
                crc_uf = '{$crc_uf}', crc_control = '{$crc_control}', nome = '{$nome}',
                tel_comercial = '{$telefone}', tel_celular = '{$celular}', email = '{$email}'
                WHERE id_contador = '{$id}' LIMIT 1";
        $result = mysql_query($update) or die('Erro' . mysql_error());

        return $result;
    }

}