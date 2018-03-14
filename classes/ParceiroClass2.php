<?php
/* 
 * Módulo Objeto da classe ParceirosClass2 orientado ao FrameWork do sistema da F71
 * Data Criação: 15/05/2015
 * Desenvolvimento: Renato
 * Versão: 0.1 (Build 00001)
 * 
 * Obs sobre a versão: 
 * 
 */

const       QUERY = 0;
const       SELECT = 1;
const       UPDATE = 2;
const       INSERT = 3;
const       WHERE = 4;
const       GROUP = 5;
const       HAVING = 6;
const       ORDER = 7;
const       LIMIT = 8;

const       DIA = 0;
const       MES = 1;
const       ANO = 2;

class ParceiroClass {
    const       VERSAO = '0.1.00001';              // Versão/build da classe
    
    protected $error = '';
    protected $parceiro_default = array(
        'parceiro_id' => 0,
	'id_regiao' => 0,
	'parceiro_nome' => '',
	'parceiro_logo' => '',
	'parceiro_endereco' => '',
	'parceiro_cnpj' => '',
	'parceiro_ccm' => '',
	'parceiro_ie' => '',
	'parceiro_im' => '',
	'parceiro_contato' => '',
	'parceiro_cpf' => '',
	'parceiro_bairro' => '',
	'parceiro_cidade' => '',
	'parceiro_estado' => '',
	'parceiro_telefone' => '',
	'parceiro_celular' => '',
	'parceiro_email' => '',
	'parceiro_banco' => '',
	'parceiro_agencia' => '',
	'parceiro_conta' => '',
	'parceiro_status' => '',
	'parceiro_autor'  => 0,
	'parceiro_data' => '',
	'parceiro_atualizacao' => '',
	'parceiro_id_atualizacao'  => 0
    );
    protected $parceiro = array();
    protected $query = array(QUERY => '',
                             SELECT => '',
                             UPDATE => '',
                             INSERT => '',
                             WHERE => '',
                             GROUP => '',
                             HAVING => '',
                             ORDER => '',
                             LIMIT => '');
    protected $rs;
    protected $row;
    protected $num_rows;
    
    public $parceiro_save = array();
    
    public function __construct() {
        try {
            
        } catch (Exception $ex) {
            print_array($ex);
        }
    }
    
    /*
     * Sets e Gets da classe
     */
    
    public function setDefault(){
        $this->parceiro_save = array();
        $this->parceiro = $this->parceiro_default;
    }
    
    public function setError($valor) {
        $this->error = $valor;
    } 
    
    public function getError() {
        return $this->error;
    } 
    
    public function setIdParceiro($valor) {
        $this->parceiro_save['parceiro_id'] = ($this->parceiro["parceiro_id"] = $valor);
    } 
    
    public function getIdParceiro() {
        return $this->parceiro["parceiro_id"];
    } 
    
    public function setIdRegiao($valor) {
        $this->parceiro_save['id_regiao'] = ($this->parceiro["id_regiao"] = $valor);
    } 
    
    public function getIdRegiao() {
        return $this->parceiro["id_regiao"];
    } 
    
    public function setParceiroNome($valor) {
        $this->parceiro_save['parceiro_nome'] = "'".($this->parceiro["parceiro_nome"] = $valor)."'";
    } 
    
    public function getParceiroNome() {
        return $this->parceiro["parceiro_nome"];
    } 
    
    public function setParceiroLogo($valor) {
        $this->parceiro_save['parceiro_logo'] = "'".($this->parceiro["parceiro_logo"] = $valor)."'";
    } 
    
    public function getParceiroLogo() {
        return $this->parceiro["parceiro_logo"];
    } 
    
    public function setParceiroEndereco($valor) {
        $this->parceiro_save['parceiro_endereco'] = "'".($this->parceiro["parceiro_endereco"] = $valor)."'";
    } 
    
    public function getParceiroEndereco() {
        return $this->parceiro["parceiro_endereco"];
    } 
    
    public function setParceiroCnpj($valor) {
        $this->parceiro_save['parceiro_cnpj'] = "'".($this->parceiro["parceiro_cnpj"] = $valor)."'";
    } 
    
    public function getParceiroCnpj() {
        return $this->parceiro["parceiro_cnpj"];
    } 
    
    public function setParceiroCcm($valor) {
        $this->parceiro_save['parceiro_ccm'] = "'".($this->parceiro["parceiro_ccm"] = $valor)."'";
    } 
    
    public function getParceiroCcm() {
        return $this->parceiro["parceiro_ccm"];
    } 
    
    public function setParceiroIe($valor) {
        $this->parceiro_save['parceiro_ie'] = "'".($this->parceiro["parceiro_ie"] = $valor)."'";
    } 
    
    public function getParceiroIe() {
        return $this->parceiro["parceiro_ie"];
    } 
    
    public function setParceiroIm($valor) {
        $this->parceiro_save['parceiro_im'] = "'".($this->parceiro["parceiro_im"] = $valor)."'";
    } 
    
    public function getParceiroIm() {
        return $this->parceiro["parceiro_im"];
    } 
    
    public function setParceiroContato($valor) {
        $this->parceiro_save['parceiro_contato'] = "'".($this->parceiro["parceiro_contato"] = $valor)."'";
    } 
    
    public function getParceiroContato() {
        return $this->parceiro["parceiro_contato"];
    } 
    
    public function setParceiroCpf($valor) {
        $this->parceiro_save['parceiro_cpf'] = "'".($this->parceiro["parceiro_cpf"] = $valor)."'";
    } 
    
    public function getParceiroCpf() {
        return $this->parceiro["parceiro_cpf"];
    } 
    
    public function setParceiroBairro($valor) {
        $this->parceiro_save['parceiro_bairro'] = "'".($this->parceiro["parceiro_bairro"] = $valor)."'";
    } 
    
    public function getParceiroBairro() {
        return $this->parceiro["parceiro_bairro"];
    } 
    
    public function setParceiroCidade($valor) {
        $this->parceiro_save['parceiro_cidade'] = "'".($this->parceiro["parceiro_cidade"] = $valor)."'";
    } 
    
    public function getParceiroCidade() {
        return $this->parceiro["parceiro_cidade"];
    } 
    
    public function setParceiroEstado($valor) {
        $this->parceiro_save['parceiro_estado'] = "'".($this->parceiro["parceiro_estado"] = $valor)."'";
    } 
    
    public function getParceiroEstado() {
        return $this->parceiro["parceiro_estado"];
    } 
    
    public function setParceiroTelefone($valor) {
        $this->parceiro_save['parceiro_telefone'] = "'".($this->parceiro["parceiro_telefone"] = $valor)."'";
    } 
    
    public function getParceiroTelefone() {
        return $this->parceiro["parceiro_telefone"];
    } 
    
    public function setParceiroCelular($valor) {
        $this->parceiro_save['parceiro_celular'] = "'".($this->parceiro["parceiro_celular"] = $valor)."'";
    } 
    
    public function getParceiroCelular() {
        return $this->parceiro["parceiro_celular"];
    } 
    
    public function setParceiroEmail($valor) {
        $this->parceiro_save['parceiro_email'] = "'".($this->parceiro["parceiro_email"] = $valor)."'";
    } 
    
    public function getParceiroEmail() {
        return $this->parceiro["parceiro_email"];
    } 
    
    public function setParceiroBanco($valor) {
        $this->parceiro_save['parceiro_banco'] = "'".($this->parceiro["parceiro_banco"] = $valor)."'";
    } 
    
    public function getParceiroBanco() {
        return $this->parceiro["parceiro_banco"];
    } 
    
    public function setParceiroAgencia($valor) {
        $this->parceiro_save['parceiro_agencia'] = "'".($this->parceiro["parceiro_agencia"] = $valor)."'";
    } 
    
    public function getParceiroAgencia() {
        return $this->parceiro["parceiro_agencia"];
    } 
    
    public function setParceiroConta($valor) {
        $this->parceiro_save['parceiro_conta'] = "'".($this->parceiro["parceiro_conta"] = $valor)."'";
    } 
    
    public function getParceiroConta() {
        return $this->parceiro["parceiro_conta"];
    } 
    
    public function setParceiroStatus($valor) {
        $this->parceiro_save['parceiro_status'] = "'".($this->parceiro["parceiro_status"] = $valor)."'";
    } 
    
    public function getParceiroStatus() {
        return $this->parceiro["parceiro_status"];
    } 
    
    public function setParceiroAutor($valor) {
        $this->parceiro_save['parceiro_autor'] = ($this->parceiro["parceiro_autor"] = $valor);
    } 
    
    public function getParceiroAutor() {
        return $this->parceiro["parceiro_autor"];
    } 
    
    public function setParceiroData($valor) {
        $this->parceiro_save['parceiro_data'] = "'".($this->parceiro["parceiro_data"] = $valor)."'";
    } 
    
    public function getParceiroData($formato) {
        return isset($formato) ? gmdate($formato,strtotime($this->parceiro["parceiro_data"])) : $this->parceiro["parceiro_data"];
    } 
    
    public function setParceiroDataAtualizacao($valor) {
        $this->parceiro_save['parceiro_atualizacao'] = "'".($this->parceiro["parceiro_atualizacao"] = $valor)."'";
    } 
    
    public function getParceiroDataAtualizacao($formato) {
        return isset($formato) ? gmdate($formato,strtotime($this->parceiro["parceiro_atualizacao"])) : $this->parceiro["parceiro_atualizacao"];
    } 
    
    public function setParceiroIdAtualizacao($valor) {
        $this->parceiro_save['parceiro_id_atualizacao'] = ($this->parceiro["parceiro_id_atualizacao"] = $valor);
    } 
    
    public function getParceiroIdAtualizacao() {
        return $this->parceiro["parceiro_id_atualizacao"];
    } 
    
    /*
     * Set de consultas
     * 
     * Retorna um vetor com informações da contratação do funcionário
     */
    
    private function setQuery($valor,$index = QUERY){
        return $this->query[$index] = $valor;
    }
    
    public function getQuery($index){
        $query = '';
        if(empty($index)){
            for ($i = 0; $i < 9; $i++) {
                $query .= " {$this->query[$i]} ";
            }
        } else {
            $query = $this->query[$index];
        }
        return $query;
    }     
   
    private function setRs($valor){ 
        $this->rs = mysql_query($valor);
        $this->num_rows = mysql_num_rows($this->rs);
        return $this->rs;
    }
    
    public function getNumRow(){
        return $this->num_rows;
    }

    private function setRow($valor){
        return $this->row = mysql_fetch_array($valor);
    }
        
    private function selectCheck(){
        if(is_null($this->getIdParceiro()) || empty($this->getIdParceiro())){
            $this->setError('Não é possível realizar o método SELECT sem um id do Parceiro');            
            return 0;
        }
        return 1;
    }
    
    public function select(){
        if(!$this->selectCheck()){
            return 0;
        }
        
        $this->setQuery(($this->getIdParceiro() ? "WHERE parceiro_id = '{$this->getIdParceiro()}'" : ""),WHERE);
        
        if(!$this->setQuery("SELECT "
                . "     parceiro_id,"
                . "     id_regiao,"
                . "     parceiro_nome,"
                . "     parceiro_logo,"
                . "     parceiro_endereco,"
                . "     parceiro_cnpj,"
                . "     parceiro_ccm,"
                . "     parceiro_ie,"
                . "     parceiro_im,"
                . "     parceiro_contato,"
                . "     parceiro_cpf,"
                . "     parceiro_bairro,"
                . "     parceiro_cidade,"
                . "     parceiro_estado,"
                . "     parceiro_telefone,"
                . "     parceiro_celular,"
                . "     parceiro_email,"
                . "     parceiro_banco,"
                . "     parceiro_agencia,"
                . "     parceiro_conta,"
                . "     parceiro_status,"
                . "     parceiro_autor,"
                . "     parceiro_data,"
                . "     parceiro_atualizacao,"
                . "     parceiro_id_atualizacao"
                . " FROM parceiros ")){
            return 0;
        }
        
        if($this->setRs($this->getQuery())){
            return 1;
        } else {
            $this->setError(mysql_error());            
            return 0;
        }
    }
    
    public function selectAll(){
        $where = "WHERE parceiro_status = {$this->getParceiroStatus()}";
        $where .= ($this->getIdRegiao()) ? " AND id_regiao = {$this->getIdRegiao()} " : "";
        
        $this->setQuery($where,WHERE);
        $this->setQuery("ORDER BY parceiro_nome",ORDER);
        
        if(!$this->setQuery("SELECT "
                . "     parceiro_id,"
                . "     id_regiao,"
                . "     parceiro_nome,"
                . "     parceiro_logo,"
                . "     parceiro_endereco,"
                . "     parceiro_cnpj,"
                . "     parceiro_ccm,"
                . "     parceiro_ie,"
                . "     parceiro_im,"
                . "     parceiro_contato,"
                . "     parceiro_cpf,"
                . "     parceiro_bairro,"
                . "     parceiro_cidade,"
                . "     parceiro_estado,"
                . "     parceiro_telefone,"
                . "     parceiro_celular,"
                . "     parceiro_email,"
                . "     parceiro_banco,"
                . "     parceiro_agencia,"
                . "     parceiro_conta,"
                . "     parceiro_status,"
                . "     parceiro_autor,"
                . "     parceiro_data,"
                . "     parceiro_atualizacao,"
                . "     parceiro_id_atualizacao"
                . " FROM parceiros ")){
            return 0;
        }
        
        if($this->setRs($this->getQuery())){
            return 1;
        } else {
            $this->setError(mysql_error());            
            return 0;
        }
    }
    
    public function makeCampos($array){
        unset($array['parceiro_id']);
        foreach ($array as $key => $value) {
            if(empty($campos)){
                $campos .= " $key=$value ";
            } else {
                $campos .= ", $key=$value ";
            }
        }
        return $campos;
    }
    
    public function update(){
        
        if(!$this->selectCheck()){
            return 0;
        }
        
        $this->setQuery("WHERE parceiro_id = '{$this->getIdParceiro()}'",WHERE);
        
        $campos = $this->makeCampos($this->parceiro_save);
        if(empty($this->parceiro_save)){
            return 0;
        }
        
        if(!$this->setQuery(""
                ."UPDATE parceiros SET"
                . " $campos ")){

            $this->setError(mysql_error());
            return 0;
        }

        if($this->setRs($this->getQuery())){
            return 1;
        } else {
            $this->setError(mysql_error());            
            return 0;
        }        
        return 1;
    }
    
    public function insert(){
        
        if(empty($this->parceiro_save)){
            return 0;
        }
        
        $keys = implode(',', array_keys($this->parceiro_save));
        $values = implode(", ",($this->parceiro_save));
//        echo "INSERT INTO parceiros ($keys) VALUES ($values);";
        if(!$this->setQuery("INSERT INTO parceiros ($keys) VALUES ($values);")){
            $this->setError(mysql_error());
            return 0;
        }
        
        if($this->setRs($this->getQuery())){
            $this->setIdParceiro(mysql_insert_id());
            return 1;
        } else {
            $this->setError(mysql_error());            
            return 0;
        }        
        return 1;
    }
    
    public function getRow(){

        if($this->setRow($this->rs)){
            
            $this->setIdParceiro($this->row["parceiro_id"]);
            $this->setIdRegiao($this->row["id_regiao"]);
            $this->setParceiroNome($this->row["parceiro_nome"]);
            $this->setParceiroLogo($this->row["parceiro_logo"]);
            $this->setParceiroEndereco($this->row["parceiro_endereco"]);
            $this->setParceiroCnpj($this->row["parceiro_cnpj"]);
            $this->setParceiroCcm($this->row["parceiro_ccm"]);
            $this->setParceiroIe($this->row["parceiro_ie"]);
            $this->setParceiroIm($this->row["parceiro_im"]);
            $this->setParceiroContato($this->row["parceiro_contato"]);
            $this->setParceiroCpf($this->row["parceiro_cpf"]);
            $this->setParceiroBairro($this->row["parceiro_bairro"]);
            $this->setParceiroCidade($this->row["parceiro_cidade"]);
            $this->setParceiroEstado($this->row["parceiro_estado"]);
            $this->setParceiroTelefone($this->row["parceiro_telefone"]);
            $this->setParceiroCelular($this->row["parceiro_celular"]);
            $this->setParceiroEmail($this->row["parceiro_email"]);
            $this->setParceiroBanco($this->row["parceiro_banco"]);
            $this->setParceiroAgencia($this->row["parceiro_agencia"]);
            $this->setParceiroConta($this->row["parceiro_conta"]);
            $this->setParceiroStatus($this->row["parceiro_status"]);
            $this->setParceiroAutor($this->row["parceiro_autor"]);
            $this->setParceiroData($this->row["parceiro_data"]);
            $this->setParceiroDataAtualizacao($this->row["parceiro_atualizacao"]);
            $this->setParceiroIdAtualizacao($this->row["parceiro_id_atualizacao"]);
            
            return 1;
        }
        else{
            $this->setError(mysql_error());
            return 0;
        }
    }
    
    public function strToDate($dt,$data_formato = array(ANO,MES,DIA)){

        $data_array = explode(' ',$dt);
        
        $data = $data_array[0];
        $hora = $data_array[1];

        $data_replace = str_replace('/','-',$data);         
        $data_explode = explode('-',$data_replace); 

        $y = $data_explode[$data_formato[ANO]];
        $m = $data_explode[$data_formato[MES]];
        $d = $data_explode[$data_formato[DIA]];

        if (checkdate($d,$m,$y)){
           return "{$y}-{$m}-{$d} {$hora}";
        }
        
        $this->setError("Data Inválida!");
        return '';
    }
}