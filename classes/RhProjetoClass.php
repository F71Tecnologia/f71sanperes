<?php
/*
 * PHO-DOC - RhProjetosClass.php
 * 
 * 15-12-2015
 * 
 * @package RhMovimentosCltClass
 * @access public  
 * 
 * @version 
 * 
 * Vers�o: 3.0.0000 - 15/12/2015 - Jacques - Vers�o Inicial
 * Vers�o: 3.0.6545 - 16/02/2016 - Jacques - Atualiza��o da classe para inst�nciamento din�mico
 * Vers�o: 3.0.7220 - 02/03/2016 - Jacques - Adicionado o m�todo GetFields para uso no m�todo select 
 * 
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 *  
 */ 
class RhProjetoClass {

    
    public function select(){
        
        $this->createCoreClass();
       
        $this->db->setQuery(SELECT,$this->getFields());
        
        $this->db->setQuery(FROM, "projeto ");
        
        if(is_object($this->getSuperClass()->Clt)){

            $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();

        }        
        else {
            
            $id_projeto = $this->getId();
            
        }    
        
        $this->db->setQuery(WHERE, " 1 = 1 ");

        if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto IN ({$id_projeto})",ADD);}
        
        $this->db->setQuery(ORDER,"nome");
        
        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do m�todo select da classe RhProjetoClass',E_FRAMEWORK_ERROR);
        
        return $this;
        
    }
    
    public function getUnidadesTrabalhadas(){
        
        $this->createCoreClass();     
        
        if(is_object($this->getSuperClass()->Clt)){
            
            $unidades_trabalhadas = $this->getSuperClass()->Clt->select()->db->getCollection('cpf,id_projeto');            

        }        
        else {
            
            $this->error->set('O m�todo RhEventosClass->selectUnidadesTrabalhadas() possui dep�nd�ncia da Classe RhCltClass que n�o est� inst�nciada',E_FRAMEWORK_ERROR);
            
        }  
        
        $unidades = '';
        
        foreach ($unidades_trabalhadas['dados'][$this->getSuperClass()->Clt->getCpf()] as $key => $value) {
            
            $unidades .= "{$key},";
            
        }
        
        return substr_replace($unidades, '', -1);;
        
    }
    
}