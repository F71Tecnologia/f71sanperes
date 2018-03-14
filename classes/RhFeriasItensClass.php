<?php

/*
 * PHP-DOC - RhFeriasItensClass.php 
 * 
 * Classe para criação de camada de compatibilidade retroativa na operacionalização das férias 
 *
 * 10-09-2015
 * 
 * @package RhFeriasItensClass 
 * @access public   
 * 
 * @version
 * 
 * Versão: 3.0.4385 - 24/11/2015 - Jacques - Versão Inicial
 * 
 * Adicionei a condição de verificação de existência de dias no segundo mês para evitar levar lixo para os registros em questão
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
     * PHP-DOC - Executa a inserção de registros da tabela rh_ferias_itens
     */
    public function insert(){
        
        if(empty($this->rh_ferias_itens_save)) $this->error->set('O vetor $this->rh_ferias_itens_save está vazio, isso gerou uma exceção no método rh->FeriasItens->insert() que impede sua finalização',E_FRAMEWORK_ERROR);
        
        $this->db->makeFieldInsert('rh_ferias_itens',$this->rh_ferias_itens_save);
        
        if(!$this->db->setRs()) {
            
            $this->error->set('Houve um erro na query de inserção do método insert da classe RhFeriasClass',E_FRAMEWORK_ERROR);
            
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

        if(empty($this->rh_ferias_itens_save)) $this->error->set('O vetor $this->rh_ferias_itens_save está vazio, isso gerou uma exceção no método rh->FeriasItens->update() que impede sua finalização',E_FRAMEWORK_ERROR);

        $this->db->makeFieldUpdate('rh_ferias_itens',$this->rh_ferias_itens_save);

        $this->db->setQuery(WHERE, " status = 1",ADD);

        if(!empty($id_ferias)) $this->db->setQuery(WHERE, "AND id_ferias = {$id_ferias}",ADD);

        if(!empty($id_legenda)) $this->db->setQuery(WHERE, " AND id_legenda = {$id_legenda}",ADD);

        if(empty($id_ferias) || empty($id_legenda)) $this->error->set("Necessário a definição da propriedade id_ferias e id_legenda para execução do método update da classe RhFeriasItensClas",E_FRAMEWORK_ERROR);

        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de inserção do método insert da classe RhFeriasItensClass',E_FRAMEWORK_ERROR);
            
        return 1;
        
    }    
   

}