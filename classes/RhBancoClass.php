<?php

class RhBancoClass {

    private     $super_class;    
    protected   $error;
    private     $date;
    private     $db; 

    private     $bancos_default = array(
                        'id_banco' => 0,
                        'id_regiao' => 0,
                        'id_nacional' => '',
                        'administracao' => '',
                        'nome' => '',
                        'razao' => '',
                        'cnpj' => '',
                        'localidade' => '',
                        'conta' => '',
                        'agencia' => '',
                        'endereco' => '',
                        'tel' => '',
                        'gerente' => '',
                        'saldo' => '',
                        'site' => '',
                        'status_reg' => 0,
                        'id_projeto' => 0,
                        'interno' => '',
                        'num_razao' => '',
                        'cod_convenio' => '',
                        'cod_empresa' => '',
                        'regiao_referente' => 0,
                        'projeto_referente' => 0,
                        'sequencia_cnab240' => 0
                        );
    
    private     $bancos = array();
    
    public function __construct() {
        
        $this->setDefaultBanco();
        
    }
    

   
    private function setDefaultBanco() {
        
        $this->createCoreClass();

        $this->bancos = $this->bancos_default;

        
    }
    
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }
    
    public function setId($valor){
        
        return $this->bancos['id_banco'] = $valor;
        
    } 
    
    public function setIdRegiao($valor){
        
        return $this->bancos['id_regiao'] = $valor;
        
    } 

    public function setIdNacional($valor){
        
        return $this->bancos['id_nacional'] = $valor;
        
    } 
    
    public function setAdministracao($valor){
        
        return $this->bancos['administracao'] = $valor;
        
    } 
    
    public function setNome($valor){
        
        return $this->bancos['nome'] = $valor;
        
    } 
    
    public function setRazao($valor){
        
        return $this->bancos['razao'] = $valor;
        
    } 
    
    public function setCnpj($valor){
        
        return $this->bancos['cnpj'] = $valor;
        
    } 

    public function setLocalidade($valor){
        
        return $this->bancos['localidade'] = $valor;
        
    } 

    public function setConta($valor){
        
        return $this->bancos['conta'] = $valor;
        
    } 

    public function setAgencia($valor){
        
        return $this->bancos['agencia'] = $valor;
        
    } 
    
    public function setEndereco($valor){
        
        return $this->bancos['endereco'] = $valor;
        
    } 
    
    public function setTel($valor){
        
        return $this->bancos['tel'] = $valor;
        
    } 
    
    public function setGerente($valor){
        
        return $this->bancos['gerente'] = $valor;
        
    } 

    public function setSaldo($valor){
        
        return $this->bancos['saldo'] = $valor;
        
    } 

    public function setSite($valor){
        
        return $this->bancos['site'] = $valor;
        
    } 

    public function setStatusReg($valor){
        
        return $this->bancos['site'] = $valor;
        
    } 
    
    public function setIdProjeto($valor){
        
        return $this->bancos['id_projeto'] = $valor;
        
    } 

    public function setInterno($valor){
        
        return $this->bancos['interno'] = $valor;
        
    } 

    public function setNumRazao($valor){
        
        return $this->bancos['num_razao'] = $valor;
        
    } 

    public function setCodConvenio($valor){
        
        return $this->bancos['cod_convenio'] = $valor;
        
    } 
    
    public function setCodEmpresa($valor){
        
        return $this->bancos['cod_empresa'] = $valor;
        
    } 
    
    public function setRegiaoReferente($valor){
        
        return $this->bancos['regiao_referente'] = $valor;
        
    } 

    public function setProjetoReferente($valor){
        
        return $this->bancos['projeto_referente'] = $valor;
        
    } 
    
    public function setSequenciaCnab240($valor){
        
        return $this->bancos['sequencia_cnab240'] = $valor;
        
    }  
    
    public function getSuperClass() {
        
        return $this->super_class;
        
    }     
    
    public function getId(){
        
        return $this->bancos['id_banco'];
        
    } 
    
    public function getIdRegiao(){
        
        return $this->bancos['id_regiao'];
        
    } 

    public function getIdNacional(){
        
        return $this->bancos['id_nacional'];
        
    } 
    
    public function getAdministracao(){
        
        return $this->bancos['administracao'];
        
    } 
    
    public function getNome(){
        
        return $this->bancos['nome'];
        
    } 
    
    public function getRazao(){
        
        return $this->bancos['razao'];
        
    } 
    
    public function getCnpj(){
        
        return $this->bancos['cnpj'];
        
    } 

    public function getLocalidade(){
        
        return $this->bancos['localidade'];
        
    } 

    public function getConta(){
        
        return $this->bancos['conta'];
        
    } 

    public function getAgencia(){
        
        return $this->bancos['agencia'];
        
    } 
    
    public function getEntedero(){
        
        return $this->bancos['endereco'];
        
    } 
    
    public function getTel(){
        
        return $this->bancos['tel'];
        
    } 
    
    public function getGerente(){
        
        return $this->bancos['gerente'];
        
    } 

    public function getSaldo(){
        
        return $this->bancos['saldo'];
        
    } 

    public function getSite(){
        
        return $this->bancos['site'];
        
    } 

    public function getStatusReg(){
        
        return $this->bancos['site'];
        
    } 
    
    public function getIdProjeto(){
        
        return $this->bancos['id_projeto'];
        
    } 

    public function getInterno(){
        
        return $this->bancos['interno'];
        
    } 

    public function getNumRazao(){
        
        return $this->bancos['num_razao'];
        
    } 

    public function getCodConvenio(){
        
        return $this->bancos['cod_convenio'];
        
    } 
    
    public function getCodEmpresa(){
        
        return $this->bancos['cod_empresa'];
        
    } 
    
    public function getRegiaoReferente(){
        
        return $this->bancos['regiao_referente'];
        
    } 

    public function getProjetoReferente(){
        
        return $this->bancos['projeto_referente'];
        
    } 
    
    public function getSequenciaCnab240(){
        
        return $this->bancos['sequencia_cnab240'];
        
    }  
    
    public function getRow(){
        
        if($this->db->setRow()){
            
            $this->setId($this->db->getRow('id_banco'));
            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->setIdNacional($this->db->getRow('id_nacional'));
            $this->setAdministracao($this->db->getRow('administracao'));
            $this->setNome($this->db->getRow('nome'));
            $this->setRazao($this->db->getRow('razao'));
            $this->setCnpj($this->db->getRow('cnpj'));
            $this->setLocalidade($this->db->getRow('localidade'));
            $this->setConta($this->db->getRow('conta'));
            $this->setAgencia($this->db->getRow('agencia'));
            $this->setEndereco($this->db->getRow('endereco'));
            $this->setTel($this->db->getRow('tel'));
            $this->setGerente($this->db->getRow('gerente'));
            $this->setSaldo($this->db->getRow('saldo'));
            $this->setSite($this->db->getRow('site'));
            $this->setSaldo($this->db->getRow('saldo'));
            $this->setStatusReg($this->db->getRow('status_reg'));
            $this->setIdProjeto($this->db->getRow('id_projeto'));
            $this->setInterno($this->db->getRow['interno']);
            $this->setNumRazao($this->db->getRow('num_razao'));
            $this->setCodConvenio($this->db->getRow('cod_convenio'));
            $this->setCodEmpresa($this->db->getRow('cod_empresa'));
            $this->setRegiaoReferente($this->db->getRow('regiao_referente'));
            $this->setProjetoReferente($this->db->getRow('projeto_referente'));
            $this->setSequenciaCnab240($this->db->getRow('sequencia_cnab240'));
            
            return 1;
            
        }
        else{
            
            //$this->error->setError($this->db->error->getError());            
            
            return 0;
        }
        
    }
    
    private function createCoreClass() {
        
        if(!isset($this->error)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].'intranet/classes/ErrorClass.php');
            $this->error = new ErrorClass();        
            
        }
        
        if(!isset($this->db)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].'intranet/classes/MySqlClass.php');

            $this->db = new MySqlClass();
            
        }
        
        
        
    }    
    
    public function select(){
        
        $this->createCoreClass();
        
        $this->db->setQuery("SELECT 
                                id_banco,
                                id_regiao,
                                id_nacional,
                                administracao,
                                nome,
                                razao,
                                cnpj,
                                localidade,
                                conta,
                                agencia,
                                endereco,
                                tel,
                                gerente,
                                saldo,
                                site,
                                status_reg,
                                id_projeto,
                                interno,
                                num_razao,
                                cod_convenio,
                                cod_empresa,
                                regiao_referente,
                                projeto_referente,
                                sequencia_cnab240
                             FROM bancos ",SELECT);
        
        if(is_object($this->getSuperClass())){

            $id_banco = $this->getSuperClass()->Clt->getBanco();           
            $id_Regiao = $this->getSuperClass()->Clt->getIdRegiao();
           
        }        
        else {
            
            $id_banco = $this->getId();
            $id_regiao = $this->getIdRegiao();
            
        }
        
        
        if(!empty($id_banco) || !empty($id_regiao)){
            
            $this->db->setQuery("WHERE 1=1",WHERE,false);

            $this->db->setQuery((!empty($id_banco) ? "AND id_banco = {$id_banco}" : ""),WHERE,true);        

            $this->db->setQuery((!empty($id_regiao) ? "AND id_regiao = {$id_regiao}" : ""),WHERE,true);        
            
        }
        
        if($this->db->setRs()){
            
            return 1;
            
        }
        else {

            //$this->error->setError($this->db->error->getError());            
            return 0;
            
        }        
        
    }     
    
    
    
}
