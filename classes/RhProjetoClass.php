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
 * Versão: 3.0.0000 - 15/12/2015 - Jacques - Versão Inicial
 * Versão: 3.0.6545 - 16/02/2016 - Jacques - Atualização da classe para instânciamento dinâmico
 * Versão: 3.0.7220 - 02/03/2016 - Jacques - Adicionado o método GetFields para uso no método select 
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
        
        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do método select da classe RhProjetoClass',E_FRAMEWORK_ERROR);
        
        return $this;
        
    }
    
    public function getUnidadesTrabalhadas(){
        
        $this->createCoreClass();     
        
        if(is_object($this->getSuperClass()->Clt)){
            
            $unidades_trabalhadas = $this->getSuperClass()->Clt->select()->db->getCollection('cpf,id_projeto');            

        }        
        else {
            
            $this->error->set('O método RhEventosClass->selectUnidadesTrabalhadas() possui depêndência da Classe RhCltClass que não está instânciada',E_FRAMEWORK_ERROR);
            
        }  
        
        $unidades = '';
        
        foreach ($unidades_trabalhadas['dados'][$this->getSuperClass()->Clt->getCpf()] as $key => $value) {
            
            $unidades .= "{$key},";
            
        }
        
        return substr_replace($unidades, '', -1);;
        
    }
    
}