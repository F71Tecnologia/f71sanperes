<?php

class RhProcessosInternoClass {
    
    
    protected $error;
    private   $db;
    private   $date;
    

    private   $rh_processos_interno_default = array(
                            'proc_interno_id' => 0,
                            'id_clt' => 0,
                            'proc_interno_nome' => '',
                            'proc_interno_numero' => '',
                            'proc_interno_atividade' => '',
                            'data_cad' => '',
                            'proc_interno_status'
                            );
            
    
    private $rh_processos_interno = array();
    

    public function __construct() {
        
        try {

            $this->setDefault();
           
        } catch (Exception $ex) {
            
            print_array($ex);
            exit('Não foi possível atribuir valor default ao objeto RhGestaoCltClass');            

        }


    }
    
    /*
     * Sets da classe
     */    
    
    public function setProcessosInternoDefault(){
        
        $this->createCoreClass();
        
        $this->rh_processos_interno_save = array();
        $this->rh_processos_interno = $this->rh_processos_interno_default;
        
    }
    
    public function setProcessosInternoIdClt($value){

        $this->rh_processos_interno_save['id_clt'] = ($this->rh_processos_interno["id_clt"] = $value);
        
    }
    
    public function setProcessosInternoNome($value){
        
        $this->rh_processos_interno_save['proc_interno_nome'] = ($this->rh_processos_interno["proc_interno_nome"] = $value);
        
    }
    
    public function setProcessosInternoNumero($value){
        
        $this->rh_processos_interno_save['proc_interno_numero'] = ($this->rh_processos_interno["proc_interno_numero"] = $value);

    }

    public function setProcessosInternoAtividade($value){
        
        $this->rh_processos_interno_save['proc_interno_atividade'] = ($this->rh_processos_interno["proc_interno_atividade"] = $value);

    }

    public function setProcessosInternoDataCad($value){
        
        $this->createCoreClass();
        
        $this->date->setDate($value,$value);
        
        $this->rh_processos_interno_save['data_cad'] = $this->rh_processos_interno["data_cad"]."'";
        
    }
    
    public function setProcessosInternoStatus($value){
        
        $this->rh_processos_interno_save['proc_interno_status'] = ($this->rh_processos_interno["proc_interno_status"] = $value);
        
    }
    
    /*
     * Gets da classe
     */    
    
    public function getError(){
        
        return $this->error->getError();
        
    }
    
    public function getProcessosInternoId(){
        
        return $this->rh_processos_interno['proc_interno_id'];
        
    }
    
    public function getProcessosInternoIdClt(){
        
        return $this->rh_processos_interno['id_clt'];
        
    }
    
    public function getProcessosInternoNome(){
        
        return $this->rh_processos_interno['proc_interno_nome'];
        
    }
    
    public function getProcessosInternoNumero(){
        
        return $this->rh_processos_interno['proc_interno_numero'];
        
    }
    
    public function getProcessosInternoAtividade($value){
        
        return $this->rh_processos_interno['proc_interno_atividade'];

    }    

    public function getProcessosInternoDataCad($format){
        
        return $this->date->getDate($this->rh_processos_interno['data_cad'],$format);
        
    }
    
    public function getProcessosInternoStatus(){
        
        return $this->rh_processos_interno['proc_interno_status'];
        
    }

    public function getRowProcessosInterno(){

        
        if($this->db->setRow()){
            
           
            if(class_exists('RhCltClass')){
                
                parent::setDefault();
                parent::setIdClt($this->setProcessosInternoIdClt());
                
            }
            return 1;


        }
        else{

            $this->error->setError($this->db->error->getError());
            
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
        
        if(!isset($this->date)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].'intranet/classes/DateClass.php');

            $this->date = new DateClass();
            
        }
        
        
    }
    
    private function insertCheck(){
        
        foreach ($this->rh_processos_interno_save as $key => $value) {
            
            switch ($key) {
                case 'id_clt':
                case 'proc_interno_nome':
                case 'proc_interno_atividade':
                    
                    if(empty($value)){
                        
                        $this->error->setError("Campo [{$key}] obrigatório para essa inclusão");
                        
                        return 0;
                        
                    }
                    
                    break;
                    
                case 'proc_interno_numero':
                    
                    if(is_numeric($value)){
                        
                        $this->error->setError("Campo [{$key}] deve ser numérico");
                        
                        return 0;
                        
                    }
                    
                    
                    break;

                default:
                    break;
            }
            
        }
        
        return 1;
        
    }
    
    public function selectProcessosInterno(){
       
        $this->createCoreClass();
        
        if(class_exists('RhCltClass')){
    
            $id_clt = parent::getIdClt();
           
        }        
        else {
            
            $id_clt = $this->setProcessosInternoIdClt();
            
        }    
        
        $this->db->setQuery("SELECT
                                proc_interno_id,
                                id_clt,
                                proc_interno_nome,
                                proc_interno_numero,
                                proc_interno_atividade,
                                data_cad,
                                proc_interno_status
                            "
                            ,SELECT);

        $this->db->setQuery("FROM processos_interno ",FROM);
        
        
        if(!empty($id_clt)) {

            $this->db->setQuery("WHERE 1=1 ",WHERE);
            
            $this->db->setQuery((!empty($id_clt)? "AND id_clt = {$id_clt}" : ""),WHERE,true);
            
        }
        
        //$this->db->setQuery("ORDER BY proc_interno_id");
        
        return $this->db->setRs();

    }
    
    public function insertProcessosInterno(){
        
        $this->createCoreClass();
        
        $this->db->setQuery($this->db->makeCamposInsert('processos_interno',$this->rh_processos_interno_save),INSERT);

        if($this->insertCheck()){
            
            return 1;
            
            if($this->db->setRs()){

                return 1;

            }
            else {

                $this->error->setError($this->db->error->getError());            
                
                return 0;

            }
            
        }
        else {
            
            return 0;
            
        }
        
    }

}