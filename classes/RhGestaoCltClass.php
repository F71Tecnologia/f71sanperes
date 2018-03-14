<?php

class RhGestaoCltClass {
    
    private   $super_class;    
    protected $error;
    private   $db;
    private   $date;

    private   $rh_gestao_clt_default = array(
                            'id_regiao' => 0,
                            'tot_evento' => 0,
                            'projeto' => '',
                            'tipo' => '',
                            'codigo' => 0,
                            'especifica' => '',
                            'id_clt' => 0,
                            'curso' => '',
                            'evento_data' => '',
                            'evento_data_retorno' => '',
                            'evento_dif' => 0,
                            'ferias_data_ini' => '',
                            'ferias_data_fim' => '',
                            'ferias_dif' => 0,
                            'recisao_data_adm' => '',
                            'recisao_data_demi' => '',
                            'recisao_dif' => 0
                            );
            
    private $rh_gestao_clt_save = array();
    
    private $rh_gestao_clt = array();
    
    
    

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
    
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }      
    
    public function setDefault(){

        $this->createCoreClass();
        
        $this->rh_gestao_clt = $this->rh_gestao_clt_default;
        
        
    }
    
    public function setIdRegiao($value){

        $this->rh_gestao_clt_save['id_regiao'] = ($this->rh_gestao_clt["id_regiao"] = $value);
       
    }
    
    private function setEventoTotais($value){
        
        $this->rh_gestao_clt_save['tot_evento'] = ($this->rh_gestao_clt["tot_evento"] = $value);
        
    }

    private function setProjeto($value){
        
        $this->rh_gestao_clt_save['projeto'] = ($this->rh_gestao_clt["projeto"]  = $value);

    }
    
    private function setCurso($value){
        
        $this->rh_gestao_clt_save['curso'] = ($this->rh_gestao_clt["curso"]  = $value);
        
    }

    private function setTipo($value){
        
        $this->rh_gestao_clt_save['tipo'] = ($this->rh_gestao_clt["tipo"]  = $value);

    }

    private function setCodigo($value){
        
        $this->rh_gestao_clt_save['codigo'] = ($this->rh_gestao_clt["codigo"] = $value);
        
    }
    
    private function setEspecifica($value){
        
        $this->rh_gestao_clt_save['especifica'] = ($this->rh_gestao_clt["especifica"]  = $value);

    }    

    public function setIdClt($value){
        
        $this->rh_gestao_clt_save['id_clt'] = ($this->rh_gestao_clt["id_clt"] = $value);
        
    }    

    private function setEventoData($value){
        
        $this->createCoreClass();
        
        $this->date->setDate($value,$value);

        $this->rh_gestao_clt_save['evento_data'] = ($this->rh_gestao_clt["evento_data"] = $value);
        
    }
    
    private function setEventoDataRetorno($value){
        
        $this->date->setDate($this->rh_gestao_clt["evento_data_retorno"],$value);
        $this->rh_gestao_clt_save['evento_data_retorno'] = "'".$this->rh_gestao_clt["evento_data_retorno"]."'";
        
    }

    private function setEventoDif($value){
        
        $this->rh_gestao_clt_save['evento_dif'] = ($this->rh_gestao_clt["evento_dif"] = $value);
        
    }

    private function setFeriasDataIni($value){
        
        $this->date->setDate($this->rh_gestao_clt["ferias_data_ini"],$value);
        $this->rh_gestao_clt_save['ferias_data_ini'] = "'".$this->rh_gestao_clt["ferias_data_ini"]."'";

    }

    private function setFeriasDataFim($value){
        
        $this->date->setDate($this->rh_gestao_clt["ferias_data_fim"],$value);
        $this->rh_gestao_clt_save['ferias_data_fim'] = "'".$this->rh_gestao_clt["ferias_data_fim"]."'";
        
    }
    
    private function setFeriasDif($value){
        
        $this->rh_gestao_clt_save['ferias_dif'] = ($this->rh_gestao_clt["ferias_dif"] = $value);
        
    }
    

    private function setRecisaoDataAdm($value){
        
        $this->date->setDate($this->rh_gestao_clt["recisao_data_adm"],$value);
        $this->rh_gestao_clt_save['recisao_data_adm'] = "'".$this->rh_gestao_clt["recisao_data_adm"]."'";
        
    }
    
    private function setRecisaoDataDemi($value){


        $this->date->setDate($this->rh_gestao_clt["recisao_data_demi"],$value);
        $this->rh_gestao_clt_save['recisao_data_demi'] = "'".$this->rh_gestao_clt["recisao_data_demi"]."'";
        
    }
    
    private function setRecisaoDif($value){
        
        $this->rh_gestao_clt_save['recisao_dif'] = ($this->rh_gestao_clt["recisao_dif"] = $value);
        
    }
    
    
    /*
     * Gets da classe
     */    
    
    public function getSuperClass() {
        
        return $this->super_class;
        
    }  
    
    public function getError(){
        
        return $this->error->getError();
        
    }
    
    public function getIdRegiao(){
        
        return $this->rh_gestao_clt['id_regiao'];
        
    }
    
    public function getEventoTotais(){
        
        return $this->rh_gestao_clt['tot_evento'];
        
    }
    
    public function getProjeto(){
        
        return $this->rh_gestao_clt['projeto'];
        
    }    
    
    public function getCurso(){
        
        return $this->rh_gestao_clt['curso'];
        
    }
    
    public function getTipo(){
        
        return $this->rh_gestao_clt['tipo'];
        
    }

    public function getCodigo(){
        
        return $this->rh_gestao_clt['codigo'];
        
    }
    
    public function getEspecifica(){
        
        return $this->rh_gestao_clt['especifica'];
        
    }    
    
    public function getIdClt(){
        
        return $this->rh_gestao_clt['id_clt'];
        
    }    
    
    public function getEventoData($format){
        
        return $this->date->getDate($this->rh_gestao_clt["evento_data"],$format);
        
    }
    
    public function getEventoDataRetorno($format){

        return $this->date->getDate($this->rh_gestao_clt["evento_data_retorno"],$format);

    }
    
    public function getEventoDif(){
        
        return $this->rh_gestao_clt['evento_dif'];
        
    }
    

    public function getFeriasDataIni($format){
        
        return $this->date->getDate($this->rh_gestao_clt["ferias_data_ini"],$format);
        
    }

    public function getFeriasDataFim($format){
        
        return $this->date->getDate($this->rh_gestao_clt["ferias_data_fim"],$format);
        
    }

    public function getFeriasDif(){
        
        return $this->rh_gestao_clt['ferias_dif'];
        
    }

    public function getRecisaoDataAdm($format){
        
        return $this->date->getDate($this->rh_gestao_clt["recisao_data_adm"],$format);
        
    }
    
    public function getRecisaoDataDemi($format){
        
        return $this->date->getDate($this->rh_gestao_clt["recisao_data_demi"],$format);
        
    }
    
    public function getEventoRecisaoDif(){
        
        return $this->rh_gestao_clt['recisao_dif'];
        
    }
    
    /*
     * Set de consultas
     * 
     * Retorna um vetor com informações da contratação do funcionário
     */
    
    
    
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
    
    
    public function getRow($collection){
        
        
        if($this->db->setRow($collection)){

            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->setIdClt($this->db->getRow('id_clt'));
            $this->setEventoTotais($this->db->getRow('tot_evento'));
            $this->setTipo($this->db->getRow('tipo'));
            $this->setCodigo($this->db->getRow('codigo'));
            $this->setEspecifica($this->db->getRow('especifica'));
            $this->setProjeto($this->db->getRow('projeto'));
            $this->setCurso($this->db->getRow('curso'));
            $this->setEventoData($this->db->getRow('evento_data'));
            $this->setEventoDataRetorno($this->db->getRow('evento_data_retorno'));
            $this->setEventoDif($this->db->getRow('evento_dif'));
            $this->setFeriasDataIni($this->db->getRow('ferias_ini'));
            $this->setFeriasDataFim($this->db->getRow('ferias_fim'));                
            $this->setFeriasDif($this->db->getRow('ferias_dif'));
            $this->setRecisaoDataAdm($this->db->getRow('recisao_adm'));                
            $this->setRecisaoDataDemi($this->db->getRow('recisao_demi'));   
            $this->setRecisaoDif($this->db->getRow('reicisao_dif'));

            
            if(is_object($this->getSuperClass())){
                
                $this->getSuperClass()->Clt->setDefault();
                $this->getSuperClass()->Clt->setIdClt($this->getIdClt());
                $this->getSuperClass()->Clt->select();
                $this->getSuperClass()->Clt->getRow();

            }


            return 1;

        }
        else{

            $this->error->setError($this->db->error->getError());            

            return 0;
        }
        
    }
    
    
    public function select($collection){
       
        $this->createCoreClass();
           
        $id_regiao = $this->getIdRegiao();
        
        //$this->db->setQuery("CALL sp_rh_gestao_clt({$id_regiao})");
        
	$this->db->setQuery("
                            SELECT
                                {$id_regiao} AS id_regiao,
                                p.nome AS projeto,
                                p.id_projeto,
                                e.codigo,
                                CONCAT(e.especifica,' -  ',t.total) AS especifica,
                                clt.id_clt,
                                clt.id_regiao,
                                (SELECT MAX(nome) FROM curso c WHERE c.id_curso=clt.id_curso LIMIT 1) AS curso,
                                vue.data AS evento_data,
                                vue.data_retorno AS evento_data_retorno,
                                DATEDIFF(vue.data_retorno,vue.data) AS retorno_dif,
                                vuf.data_ini AS ferias_data_ini,
                                vuf.data_fim As ferias_data_fim,
                                DATEDIFF(vuf.data_fim,vuf.data_ini) AS ferias_dif,
                                vur.data_adm AS recisao_data_adm,
                                vur.data_demi AS recisao_data_demi,
                                DATEDIFF(vur.data_demi,vur.data_adm) AS recisao_dif " 
                            ,SELECT);
                                
	$this->db->setQuery("
                            FROM 
                                (
                                SELECT 
                                        'eventos' AS tipo,
                                        codigo,
                                        especifica 
                                FROM rhstatus 
                                WHERE status_reg=1 AND codigo IN ('200','20','30','40','50','51','52','70','80','90','100','110','10')
                                UNION ALL
                                SELECT 
                                        'eventos_recisao' As tipo,
                                        codigo,
                                        especifica
                                FROM rhstatus 
                                WHERE status_reg=1 AND codigo IN ('60','61','62','63','64','65','81','101')	  
                                ) e INNER JOIN rh_clt clt ON clt.status=e.codigo 
                                         LEFT JOIN projeto p ON p.id_regiao=clt.id_regiao AND p.id_projeto=clt.id_projeto
                                         LEFT JOIN v_ult_evento vue ON vue.id_regiao=clt.id_regiao AND vue.id_projeto=clt.id_projeto AND vue.id_clt=clt.id_clt
                                         LEFT JOIN v_ult_ferias vuf ON vuf.regiao=clt.id_regiao AND vuf.projeto=clt.id_projeto AND vuf.id_clt=clt.id_clt
                                         LEFT JOIN v_ult_recisao vur ON vur.id_regiao=clt.id_regiao AND vur.id_projeto=clt.id_projeto AND vur.id_clt=clt.id_clt 
                			 LEFT JOIN (SELECT id_regiao, status, COUNT(id_clt) AS total FROM rh_clt GROUP BY id_regiao, status) t ON t.id_regiao=clt.id_regiao AND t.status=e.codigo
                            "
                            ,FROM);
        
        $this->db->setQuery("WHERE clt.id_regiao={$id_regiao}",WHERE);
                            
	$this->db->setQuery("ORDER BY 
                                e.tipo, 
                                e.codigo, 
                                clt.id_projeto, 
                                clt.nome ASC "
                            ,ORDER);
        
        if($this->db->setRs()) {
        
            if(!empty($collection)) {

                return $this->db->getCollection($collection);

            }
            else {
                
                return 1;
                
            }
            
        }
        else {
            
            return 0;
            
        }

        
        
    }
    
    

}