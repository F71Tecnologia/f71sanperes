<?php

/*
 * PHP-DOC - RhFeriasItensClass.php 
 * 
 * Classe para cria��o de camada de compatibilidade retroativa na operacionaliza��o das f�rias 
 *
 * 10-09-2015
 * 
 * @package RhFeriasItensClass 
 * @access public   
 * 
 * @version
 * 
 * Vers�o: 3.0.4385 - 24/11/2015 - Jacques - Vers�o Inicial
 * 
 * Adicionei a condi��o de verifica��o de exist�ncia de dias no segundo m�s para evitar levar lixo para os registros em quest�o
 * 
 * @todo
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 *  
 */      
            
        
class RhFeriasItensClass {

    
    /*
     * PHP-DOC - Executa a inser��o de registros da tabela rh_ferias_itens
     */
    public function insert(){
        
        if(empty($this->rh_ferias_itens_save)) $this->error->set('O vetor $this->rh_ferias_itens_save est� vazio, isso gerou uma exce��o no m�todo rh->FeriasItens->insert() que impede sua finaliza��o',E_FRAMEWORK_ERROR);
        
        $this->db->makeFieldInsert('rh_ferias_itens',$this->rh_ferias_itens_save);
        
        if(!$this->db->setRs()) {
            
            $this->error->set('Houve um erro na query de inser��o do m�todo insert da classe RhFeriasClass',E_FRAMEWORK_ERROR);
            
            return 0;
            
        }
        else {
            
            $this->setIdFeriasItens($this->db->getKey());  

            return 1;
            
        }
       
    }
    
    public function update(){

        $id_ferias = $this->getIdFerias();
        $id_legenda = $this->getIdLegenda();

        if(empty($this->rh_ferias_itens_save)) $this->error->set('O vetor $this->rh_ferias_itens_save est� vazio, isso gerou uma exce��o no m�todo rh->FeriasItens->update() que impede sua finaliza��o',E_FRAMEWORK_ERROR);

        $this->db->makeFieldUpdate('rh_ferias_itens',$this->rh_ferias_itens_save);

        $this->db->setQuery(WHERE, " status = 1",ADD);

        if(!empty($id_ferias)) $this->db->setQuery(WHERE, "AND id_ferias = {$id_ferias}",ADD);

        if(!empty($id_legenda)) $this->db->setQuery(WHERE, " AND id_legenda = {$id_legenda}",ADD);

        if(empty($id_ferias) || empty($id_legenda)) $this->error->set("Necess�rio a defini��o da propriedade id_ferias e id_legenda para execu��o do m�todo update da classe RhFeriasItensClas",E_FRAMEWORK_ERROR);

        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de inser��o do m�todo insert da classe RhFeriasItensClass',E_FRAMEWORK_ERROR);
            
        return 1;
        
    }    
   

}