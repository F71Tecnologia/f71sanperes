<?php
/*
 * 27/04/2015
 * 
 * Módulo de classe de CTPS (Carteira de Trabalho por Tempo de Serviço)
 * 
 * Versão: 0.2 - 18/05/2015 - Adaptado e incorporado ao sistema de classes da CltClassObj em 18/05/2015
 * Versão: 0.3 - 03/08/2015 - Jacques - Adicionado busca por nome da CTSP para uso no módulo ver_clt.php 
 * Versão: 1.0 - 04/08/2015 - Jacques - Adaptação da classe para ficar em conformidade com o Framework da intra RH
 *  
 * @author: jacques@f71.com.br
 * 
 */
 
class CtpsClass {
    
    protected $error;
    private   $db;
    private   $date;
    
    private   $ctps_default = array(
                        'id_controle' => 0,
                        'id_regiao' => 0,
                        'id_user_cad' => 0,
                        'id_user_ent' => 0,
                        'nome' => '',
                        'numero' => '',
                        'serie' => '',
                        'uf' => '',
                        'obs' => '',
                        'preenchimento' => '',
                        'obs_preenchimento' => '',
                        'data_cad' => '',
                        'data_ent' => '',
                        'acompanhamento' => 0,
                        'status_reg' => 0,
                        'recebido_por' => '',
                        'entregue_por' => ''
                        );
    
    private   $ctps = array();
    
    private   $ctps_save = array();

    function __construct() {
        

    }
    
    public function setDefault(){
        
        $this->createCoreClass();
        $this->ctps_save = array();
        $this->ctps = $this->ctps_default;
        
    }
    
    public function setIdControle($value){
        
        $this->ctps['id_controle'] = $value;
        
    }
    
    public function setIdRegiao($value){

        $this->ctps_save['id_regiao'] = ($this->ctps["id_regiao"] = $value);
        
    }
    
    public function setIdUserCad($value = 0){
        
        $this->ctps_save['id_user_cad'] = ($this->ctps["id_user_cad"] = $value);
        
    }
    
    public function setIdUserEnt($value = 0){
        
        $this->ctps_save['id_user_ent'] = ($this->ctps["id_user_ent"] = $value);
        
    }
    
    public function setNome($value){
        
        $this->ctps_save['nome'] = ($this->ctps["nome"] = $value);
        
    }

    public function setNumero($value){

        $this->ctps_save['numero'] = ($this->ctps["numero"] = $value);

    }    

    public function setSerie($value){
        
        $this->ctps_save['serie'] = ($this->ctps["serie"] = $value);
        
    }    

    public function setUf($value){
        
        $this->ctps_save['uf'] = ($this->ctps["uf"] = $value);

    }    

    public function setObs($value){
        
        $this->ctps_save['obs'] = ($this->ctps["obs"] = $value);
        
    }    

    public function setPreenchimento($value){
        
        $this->ctps_save['preenchimento'] = ($this->ctps["preenchimento"] = $value);
        
    }    
    
    
    public function setObsPreenchimento($value){
        
        $this->ctps_save['obs_preenchimento'] = ($this->ctps["obs_preenchimento"] = $value);
        
    }    
    
    public function setDataCad($value){

        $this->ctps_save['data_cad'] = ($this->ctps["data_cad"] = $value);
        
    }    
    
    public function setDataEnt($value){
        
        $this->ctps_save['data_ent'] = ($this->ctps["data_ent"] = $value);
        
    }    
    
    public function setAcompanhamento($value){
        
        $this->ctps_save['acompanhamento'] = ($this->ctps["acompanhamento"] = $value);

    }    
    
    public function setStatusReg($value){
        
        $this->ctps_save['status_reg'] = ($this->ctps["status_reg"] = $value);
        
    }    
    
    public function setRecebidoPor($value){
        
        $this->ctps_save['recebido_por'] = ($this->ctps["recebido_por"] = $value);
        
    }    
    
    public function setEntreguePor($value){
        
        $this->ctps_save['entregue_por'] = ($this->ctps["entregue_por"] = $value);
        
    }    

    public function getError(){
        
        return $this->error->getError();
        
    }
    
    
    public function getIdControle(){
        
        return $this->ctps['id_controle'];
        
    }
    
    public function getIdRegiao(){
        
        return $this->ctps['id_regiao'];
    }
    
    public function getIdUserCad(){
        
        return $this->ctps['id_user_cad'];
        
    }
    
    public function getIdUserEnt(){
        
        return $this->ctps['id_user_ent'];
        
    }
    
    public function getNome(){
        
        return $this->ctps['nome'];
        
    }

    public function getNumero(){
        
        return $this->ctps['numero'];
    }    

    public function getSerie(){
        
        return $this->ctps['serie'];
        
    }    

    public function getUf(){
        
        return $this->ctps['uf'];
        
    }    

    public function getObs($value){
        
        return $this->ctps['obs'];
        
    }    
    
    
    public function getObsPreenchimento(){
        
        return $this->ctps['obs_preenchimento'];
        
    }    
    
    public function getDataCad($value){
        
        $date = clone $this->date;
        
        return $date->set($this->ctps['data_cad'])->get($value);    
        
    }    
    
    public function getDataEnt($format){
        
        $date = clone $this->date;
    
        return $date->set($this->ctps['data_ent'])->get($value);    

    }
    
    public function getDataEnt2(){
        
        return $this->ctps['data_ent'];

    }
    
    public function getAcompanhamento(){
        
        return $this->ctps['acompanhamento'];
        
    }    
    
    public function getStatusReg(){
        
        return $this->ctps['status_reg'];
        
    }   
    
    public function getRecebidoPor(){
        
        return $this->ctps['recebido_por'];
        
    }    
    
    public function getEntreguePor(){
        
        return $this->ctps['entregue_por'];
        
    }    
    
    /*
     * Retorna o total de linhas do recordset
     */
    public function getCountRow(){

        return $this->db->getNumRows();
        
    }
    
    public function getKey(){
        
        return mysql_insert_id();

    }
   
  
   private function errorInsert(){
        
       
       if(empty($this->getIdRegiao())){
           $this->setError('ID_REGIAO não definido');
           return 1;
       }

       if(empty($this->getIdUserCad())){
           $this->setError('ID_USER_CAD não definido');
           return 1;
       }
       
       if(empty($this->getNome()) || strlen($this->getNome()) > 250){
           $this->setError('Erro no campo NOME: zerado, nulo o maior que 250');
           return 1;
       }

       if(empty($this->getNumero())==0 || strlen($this->getNumero()) > 150){
           $this->setError('Erro no campo NÚMERO: zerado, nulo ou maior que 150');
           return 1;
       }
       
       if(empty($this->getSerie())==0 || strlen($this->getSerie()) > 150){
           $this->setError('Erro no campo SÉRIE: zerado, nulo ou maior que 150');
           return 1;
       }
       
       if(empty($this->getUf())==0 || strlen($this->getUf())!=2 ){
           $this->setError('Erro no campo UF: zerado, nulo ou diferente de 2');
           return 1;
       }
       
       if(strlen($this->getObs($value)) > 250){
           $this->setError('Erro no campo OBS: maior que 250');
           return 1;
       }
       
      
       if(empty($this->getDataCad())){
           $this->setError('DATA DO CADASTRAMENTO não definida');
           return 1;
       }
       
       return 0;
        
    }

    
    
    private function createCoreClass() {
        
        if(!isset($this->error)){
            
            include_once('ErrorClass.php');
            $this->error = new ErrorClass();        
            
        }
        
        if(!isset($this->db)){
            
            include_once('MySqlClass.php');

            $this->db = new MySqlClass();
            
        }
        
        if(!isset($this->date)){
            
            include_once('DateClass.php');

            $this->date = new DateClass();
            
        }
        
        
    }

       
    public function insert(){
        
        $this->createCoreClass();
        
        $this->db->makeFieldInsert('controlectps',$this->ctps_save);
        
        $ok = $this->db->setRs();
        
        $this->setIdControle($this->getKey());
                
        return $ok;
    
    }
    
    public function update(){
        
        $this->createCoreClass();
        
        $id_controle = $this->getIdControle();
        
        $this->db->makeFieldUpdate('controlectps',$this->ctps_save); 
       
        $this->db->setQuery(WHERE," id_controle = {$id_controle}");
        
        return $this->db->setRs();

    
    }    
    
    
    public function select() {

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT,"
                            id_controle,
                            id_regiao,
                            id_user_cad,
                            id_user_ent,
                            nome,
                            numero,
                            serie,
                            uf,
                            obs,
                            preenchimento,
                            obs_preenchimento,
                            data_cad,
                            data_ent,
                            acompanhamento,
                            status_reg,
                            (SELECT nome FROM funcionario WHERE id_funcionario=id_user_cad) recebido_por,
                            (SELECT nome FROM funcionario WHERE id_funcionario=id_user_ent) entregue_por
                            "
                            );

        $this->db->setQuery(FROM," controlectps ");
        
        $id_controle = $this->getIdControle();
        $id_regiao = $this->getIdRegiao();
        $acompanhamento = $this->getAcompanhamento();
        $numero = $this->getNumero();
        $serie = $this->getSerie();
        $uf = $this->getUf();
        $nome = $this->getNome();
        
        if(!empty($id_controle) || !empty($id_regiao) || !empty($acompanhamento) || !empty($numero) || !empty($serie) || !empty($uf) || !empty($nome)) {

            $this->db->setQuery(WHERE," 1=1 ");
            
            $this->db->setQuery(WHERE,(!empty($id_controle)? "AND id_controle = {$id_controle}" : ""),true);
            
            $this->db->setQuery(WHERE,(!empty($id_regiao)? "AND id_regiao = {$id_regiao}" : ""),true);
            
            $this->db->setQuery(WHERE,(!empty($numero) || !empty($serie) || !empty($uf)? "AND (numero = '{$numero}' || serie = '{$serie}' || uf = '{$uf}')" : ""),true);

            $this->db->setQuery(WHERE,(!empty($acompanhamento)? "AND acompanhamento = {$acompanhamento}" : ""),true);
            
            $this->db->setQuery(WHERE,(!empty($acompanhamento)? "AND nome LIKE '%{$nome}%'" : ""),true);
        }
        
        $this->db->setQuery(ORDER,"id_controle DESC");
        
        return $this->db->setRs();
      
        
    }
    
    /*
     * Obtem a linha do recorset e define as variáveis da classe
     */
    public function getRow(){
        
        if($this->db->setRow()){

            $this->setIdControle($this->db->getRow('id_controle'));
            $this->setIdRegiao($this->db->getRow('id_regiao'));
            $this->setIdUserCad($this->db->getRow('id_user_cad'));
            $this->setIdUserEnt($this->db->getRow('id_user_ent'));
            $this->setNome($this->db->getRow('nome'));
            $this->setNumero($this->db->getRow('numero'));
            $this->setSerie($this->db->getRow('serie'));
            $this->setUf($this->db->getRow('uf'));
            $this->setObs($this->db->getRow('obs'));
            $this->setPreenchimento($this->db->getRow('preenchimento'));
            $this->setObsPreenchimento($this->db->getRow('obs_preenchimento'));
            $this->setDataCad($this->db->getRow('data_cad'));
            $this->setDataEnt($this->db->getRow('data_ent'));
            $this->setAcompanhamento($this->db->getRow('acompanhamento'));
            $this->setStatusReg($this->db->getRow('status_reg'));
            $this->setRecebidoPor($this->db->getRow('recebido_por'));
            $this->setEntreguePor($this->db->getRow('entregue_por'));            

            return 1;


        }
        else{

            return 0;
        }
        
    }
    
    public function getLabelPreenchimento(){

        switch($this->ctps['preenchimento']){
            case 1: return "Assinar"; 
                    break;
            case 2: return "Dar Baixa"; 
                    break;
            case 3: return "Férias"; 
                    break;
            case 4: return "13º Salário"; 
                    break;
            case 5: return "Licança"; 
                    break;
            case 6: return "Outros"; 
                    break;
        }        
        
    }
   
}

