<?php


class RhUnidadeClass {

    private     $super_class;
    protected   $error;
    private     $db;
    private     $date;
        
    private     $unidade_default = array(
                                        'id_unidade' => 0,
                                        'id_regiao' => 0,
                                        'unidade' => '',
                                        'local' => '',
                                        'tel' => '',
                                        'tel2' => '',
                                        'responsavel' => '',
                                        'cel' => '',
                                        'email' => '',
                                        'campo1' => '',
                                        'campo2' => '',
                                        'campo3' => '',
                                        'status_reg' => 0,
                                        'endereco' => '',
                                        'bairro' => '',
                                        'cidade' => '',
                                        'uf' => '',
                                        'cep' => '',
                                        'ponto_referencia' => ''
                                        );

    private     $unidade = array();

    private     $unidade_save = array();
    
    private     $search = '';
    
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }
    
    public function setSearch($value, $key, $operand, $inline, $add){
        
        $this->createCoreClass();

        $this->db->setSearch($value, $key, $operand, $inline, $add);
        
    }     
    
    public function setDefault() {
        
        $this->unidade = $this->unidade_default;
        
    }

    public function setId($value) {

        $this->unidade_save['id_unidade'] = ($this->unidade['id_unidade'] = $value);

    }

    public function setIdRegiao($value) {

        $this->unidade_save['id_regiao'] = ($this->unidade['id_regiao'] = $value);

    }

    public function get($value) {

        $this->unidade_save['unidade'] = ($this->unidade['unidade'] = $value);

    }

    public function setLocal($value) {

        $this->unidade_save['local'] = ($this->unidade['local'] = $value);

    }

    public function setTel($value) {

        $this->unidade_save['tel'] = ($this->unidade['tel'] = $value);

    }

    public function setTel2($value) {

        $this->unidade_save['tel2'] = ($this->unidade['tel2'] = $value);

    }

    public function setResponsavel($value) {

        $this->unidade_save['responsavel'] = ($this->unidade['responsavel'] = $value);

    }

    public function setCel($value) {

        $this->unidade_save['cel'] = ($this->unidade['cel'] = $value);

    }

    public function setEmail($value) {

        $this->unidade_save['email'] = ($this->unidade['email'] = $value);

    }
    
    public function setIdProjeto($value) {

        $this->setCampo1($value);

    }
    

    public function setCampo1($value) {

        $this->unidade_save['campo1'] = ($this->unidade['campo1'] = $value);

    }

    public function setCampo2($value) {

        $this->unidade_save['campo2'] = ($this->unidade['campo2'] = $value);

    }

    public function setCampo3($value) {

        $this->unidade_save['campo3'] = ($this->unidade['campo3'] = $value);

    }

    public function setStatusReg($value) {

        $this->unidade_save['status_reg'] = ($this->unidade['status_reg'] = $value);

    }

    public function setEndereco($value) {

        $this->unidade_save['endereco'] = ($this->unidade['endereco'] = $value);

    }

    public function setBairro($value) {

        $this->unidade_save['bairro'] = ($this->unidade['bairro'] = $value);

    }

    public function setCidade($value) {

        $this->unidade_save['cidade'] = ($this->unidade['cidade'] = $value);

    }

    public function setUf($value) {

        $this->unidade_save['uf'] = ($this->unidade['uf'] = $value);

    }

    public function setCep($value) {

        $this->unidade_save['cep'] = ($this->unidade['cep'] = $value);

    }

    public function setPontoReferencia($value) {

        $this->unidade_save['ponto_referencia'] = ($this->unidade['ponto_referencia'] = $value);

    }

    public function setOrder($value) {
        
        $this->createCoreClass();
        $this->db->setQuery("ORDER BY $value",ORDER);
        
    }
    
    public function getSuperClass() {
        
        return $this->super_class;
        
    }    

    public function getId($format) {

        return isset($format) ? vsprintf($format, $this->unidade['id_unidade']) : $this->unidade['id_unidade'];

    }    

    public function getIdRegiao($format) {

        return isset($format) ? vsprintf($format, $this->unidade['id_regiao']) : $this->unidade['id_regiao'];

    }    

    public function getUnidade() {

        return $this->unidade['unidade'];

    }    

    public function getLocal() {

        return $this->unidade['local'];

    }    

    public function getTel() {

        return $this->unidade['tel'];

    }    

    public function getTel2() {

        return $this->unidade['tel2'];

    }    

    public function getResponsavel() {

        return $this->unidade['responsavel'];

    }    

    public function getCel() {

        return $this->unidade['cel'];

    }    

    public function getEmail() {

        return $this->unidade['email'];

    }    

    public function getCampo1() {

        return $this->unidade['campo1'];

    }    

    public function getIdProjeto() {

        return $this->getCampo1();

    }    

    public function getCampo2() {

        return $this->unidade['campo2'];

    }    

    public function getCampo3() {

        return $this->unidade['campo3'];

    }    

    public function getStatusReg($format) {

        return isset($format) ? vsprintf($format, $this->unidade['status_reg']) : $this->unidade['status_reg'];

    }    

    public function getEndereco() {

        return $this->unidade['endereco'];

    }    

    public function getBairro() {

        return $this->unidade['bairro'];

    }    

    public function getCidade() {

        return $this->unidade['cidade'];

    }    

    public function getUf() {

        return $this->unidade['uf'];

    }    

    public function getCep() {

        return $this->unidade['cep'];

    }    

    public function getPontoReferencia() {

        return $this->unidade['ponto_referencia'];

    }    
    
    public function getNumRows(){
        
        return $this->db->getNumRows();
        
    }

    public function getRow($collection){

        if($this->db->setRow($collection)){

            $this->setId($this->db->getRow('id_unidade'));
            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->set($this->db->getRow('unidade'));
            $this->setLocal($this->db->getRow('local'));
            $this->setTel($this->db->getRow('tel'));
            $this->setTel2($this->db->getRow('tel2'));
            $this->setResponsavel($this->db->getRow('responsavel'));
            $this->setCel($this->db->getRow('cel'));
            $this->setEmail($this->db->getRow('email'));
            $this->setCampo1($this->db->getRow('campo1'));
            $this->setCampo2($this->db->getRow('campo2'));
            $this->setCampo3($this->db->getRow('campo3'));
            $this->setStatusReg($this->db->getRow('status_reg'));
            $this->setEndereco($this->db->getRow('endereco'));
            $this->setBairro($this->db->getRow('bairro'));
            $this->setCidade($this->db->getRow('cidade'));
            $this->setUf($this->db->getRow('uf'));
            $this->setCep($this->db->getRow('cep'));
            $this->setPontoReferencia($this->db->getRow('ponto_referencia'));

            return 1;

        }
        else{

            $this->error->setError($this->db->error->getError());            

            return 0;
        }

    }
    
    private function createCoreClass() {
        
        if(!isset($this->error)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'ErrorClass.php');
            $this->error = new ErrorClass();        
            
        }
        
        if(!isset($this->db)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'MySqlClass.php');

            $this->db = new MySqlClass();
            
        }
        
        if(!isset($this->date)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].PATH_CLASS.'DateClass.php');

            $this->date = new DateClass();
            
        }

    }
    
    public function select(){

        $this->createCoreClass();

        $this->db->setQuery("SELECT 
                                id_unidade,
                                id_regiao,
                                unidade,
                                local,
                                tel,
                                tel2,
                                responsavel,
                                cel,
                                email,
                                campo1,
                                campo2,
                                campo3,
                                status_reg,
                                endereco,
                                bairro,
                                cidade,
                                uf,
                                cep,
                                ponto_referencia,
                             FROM unidade ",SELECT);

        if(isset($superclasse)){
            
            $id_regiao  = $superclasse->getIdRegiao();
            $id_projeto = $superclasse->getIdProjeto();

            echo 'RhCltClass::getIdProjeto('.$id_projeto.')';


        }        
        else {

            $id_regiao  = $this->getIdRegiao();
            $id_projeto = $this->getIdProjeto();

        }

        $id_unidade = $this->getId();
        $status_reg = $this->getStatusReg();

        if(!empty($id_regiao) || !empty($id_unidade) || !empty($id_projeto) || !empty($status_reg) ){

            $this->db->setQuery("WHERE 1=1",WHERE,false);

            $this->db->setQuery((!empty($id_regiao) ? "AND id_regiao = {$id_regiao}" : ""),WHERE,true);        
            $this->db->setQuery((!empty($id_projeto) ? "AND id_projeto = {$id_projeto}" : ""),WHERE,true);        
            $this->db->setQuery((!empty($id_unidade) ? "AND id_unidade = {$id_unidade}" : ""),WHERE,true);        
            $this->db->setQuery((!empty($status_reg) ? "AND status_reg = {$status_reg}" : ""),WHERE,true);   
            
        }
        
        if($this->db->setRs()){

            return 1;

        }
        else {

            $this->error->setError($this->db->error->getError());            
            return 0;

        }        

    }   
    
}
