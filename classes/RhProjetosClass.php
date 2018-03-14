<?php

const DATA_CONTRATACAO = 0;
const DATA_CONTRATACAO_FMT = 1;
const DATA_CONTRATACAO_DIA = 2;
const DATA_CONTRATACAO_MES = 3;
const DATA_CONTRATACAO_ANO = 4;
const DATA_CONTRATACAO_STATUS = 5;

class RhProjetosClass {

    private     $super_class;    
    public      $error;
    private     $date;
    private     $db;
    
    private     $projeto_default = array( 'id_projeto' => 0,
                                          'id_regiao' => 0,
                                          'id_master' => 0,
                                          'id_usuario' => 0,
                                          'administracao' => 0,
                                          'nome' => '',
                                          'tema' => '',
                                          'area' => '',
                                          'local' => '',
                                          'endereco' => '',
                                          'complemento' => '',
                                          'bairro' => '',
                                          'cidade' => '',
                                          'cep' => '',
                                          'regiao' => '',
                                          'tipo_contrato' => '',
                                          'numero_contrato' => '',
                                          'inicio' => '',
                                          'termino' => '',
                                          'prazo_renovacao' => '',
                                          'tipo_contratacao' => '',
                                          'descricao' => '',
                                          'total_participantes' => '',
                                          'proposta_parceria' => '',
                                          'termo_parceria' => '',
                                          'verba_destinada' => '',
                                          'verba_periodo' => '',
                                          'taxa_adm' => '',
                                          'taxa_parceiro' => '',
                                          'id_parceiro' => 0,
                                          'taxa_outra1' => '',
                                          'id_parceiro1' => 0,
                                          'taxa_outra2' => '',
                                          'id_parceiro2' => 0,
                                          'provisao_encargos' => '',
                                          'valor_ini' => '',
                                          'valor_acre' => '',
                                          'bolsista' => '',
                                          'sis_user' => 0,
                                          'status_reg' => 0,
                                          'entrega' => '',
                                          'data_entrega' => '',
                                          'trimestral' => '',
                                          'semestral' => '',
                                          'data_entrega' => '',
                                          'trimestral' => '',
                                          'semestral' => '',
                                          'data_semestral' => '',
                                          'data_trimestral' => '',
                                          'capacita' => '',
                                          'data_capacita' => '',
                                          'desempenho' => '',
                                          'data_desempenho' => '',
                                          'gestores' => '',
                                          'data' => '',
                                          'id_banco_principal' => 0,
                                          'estado' => '',
                                          'id_subprojeto' => '',
                                          'data_assinatura' => '',
                                          'id_usuario_atualizacao' => 0,
                                          'data_atualizacao' => '',
                                          'tipo_folha' => 0,
                                          'cnpj' => '',
                                          'cod_sesrj' => '',
                                          'cod_contrato' => '',
                                          'prestaconstas' => 0
                                );
    
    private    $projeto = array();
    
    public function __construct() {
        
        try {
            
            $this->setDefault();
            
        } catch (Exception $ex) {
            
            print_array($ex);

        }


    }
    
      
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }     

    public function setDefault(){
        
        $this->projeto = $this->projeto_default;
        
    }
    
    public function setId($valor){
        
        $this->projeto['id_projeto'] = $valor;
        
    }
    
    public function setIdMaster($valor){
        
        $this->projeto['id_master'] = $valor;
        
    }

    public function setStatusReg($valor){
        
        $this->projeto['status_reg'] = $valor;
        
    }
    
    public function setNome($valor){
        
        $this->projeto['nome'] = $valor;
        
    }
    
    public function getSuperClass() {
        
        return $this->super_class;
        
    }      
    
    public function getId(){
        
        return $this->projeto['id_projeto'];
        
    }    
    
    public function getStatusReg(){
        
        return $this->projeto['status_reg'];
        
    }    
    
    public function getNome(){
        
        return $this->projeto['nome'];
        
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
       
        $this->db->setQuery(SELECT,"id_projeto,
                                    nome,
                                    status_reg 
                            ");
        
        $this->db->setQuery(FROM, "projeto ");
        
        if(is_object($this->getSuperClass())){

            $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();

        }        
        else {
            
            $id_projeto = $this->getId();
            
        }    
        
       if(!empty($id_projeto)) {
            
            $this->db->setQuery(WHERE,"1=1");

            $this->db->setQuery(WHERE,"AND id_projeto={$id_projeto} ",add);
           
        }        
        
        $this->db->setQuery(ORDER,"nome");
        
   
        if($this->db->setRs()){
            
            return 1;
            
        }
        else {

            $this->error->setError($this->db->error->getError());            
            return 0;
            
        }        
        
    }
    
    public function selectUnidadesTrabalhadas(){
        
        $this->createCoreClass();
        
        $this->db->setQuery("SELECT 
                          id_projeto,
                          nome,
                          status_reg "
                          ,SELECT);
        
        $this->db->setQuery("FROM projeto ",FROM);
        
        if(is_object($this->getSuperClass())){

            $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();
            $nome = $this->getSuperClass()->Clt->getNome();
            $cpf = $this->getSuperClass()->Clt->getCpf();
            $pis = $this->getSuperClass()->Clt->getPis();
            

        }         
        else {
            
            $id_projeto = $this->getId();
            
        }    
        
       if(!empty($id_projeto)) {
            
            $this->db->setQuery("WHERE 1=1 ",WHERE);

            $this->db->setQuery("AND id_projeto={$id_projeto} ", WHERE, true);
            
        }        
        
        $this->db->setQuery("ORDER BY nome ", ORDER);
        
     
        if($this->db->setRs()){
            
            return 1;
            
        }
        else {

            $this->error->setError($this->db->error->getError());            
            return 0;
            
        }        
        
    }
    
    public function getRow(){
        
        if($this->db->setRow()){
            
            $this->setId($this->db->getRow('id_projeto'));
            $this->setNome($this->db->getRow('nome'));
            $this->setStatusReg($this->db->getRow('status_reg'));
            
            return 1;
            
        }
        else {
            
            return 0;
            
        }
        
    }
    
    public function getUnidadesTrabalhadasRow(){
        
        if($this->db->setRow()){
            
            $this->setId($this->db->getRow('id_projeto'));
            $this->setNome($this->db->getRow('nome'));
            $this->setStatusReg($this->db->getRow('status_reg'));
            
            return 1;
            
        }
        else {
            
            return 0;
            
        }
        
    }    
    
    public function getUnidadesTrabalhadasTot(){
        
        return $this->db->getNumRows();
        
    }      
    
    public function getDadosContratacao($index){
        
        if(is_object($this->getSuperClass())){
    
            $data_contratacao = implode('/', array_reverse(explode('-',$this->getSuperClass()->Clt->getDataEntrada('d/m/Y'))));

            $data_contratacao_array = array(
                    "data" => $this->getSuperClass()->Clt->getDataEntrada(),
                    "data_fmt" => $this->getSuperClass()->Clt->getDataEntrada('d/m/Y'), 
                    "dia" => $data_contratacao[DATA_CONTRATACAO_DIA],
                    "mes" => $data_contratacao[DATA_CONTRATACAO_MES],
                    "ano" => $data_contratacao[DATA_CONTRATACAO_ANO],
                    "status_contratacao" => $this->getSuperClass()->Clt->getStatusContratacao()             
                    );

            if(isset($index)){

                return $data_contratacao_array[DATA_CONTRATACAO];

            }
            else {

                return $data_contratacao_array[$index];

            }

           
        }        
        else {
            
            $this->error->setError('A classe Projetos não foi instanciada como classe filha de RhCltClass');            
            return 0;                

            
        }          
        
    }   
    
        
}