<?php
/*
 * PHO-DOC - RhTipoPgClass.php
 * 
 * Classe de definição dos tipos de pagamentos
 * 
 * 22-12-2015 
 *
 * @name RhTipoPg 
 * @package RhTipoPgClass
 * @access public 
 * 
 * @version
 *
 * Versão: 3.0.0000 - 22-12-2015 - Jacques - Versão Inicial
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */        

class TipoPgClass {
    
    public function select(){

        $this->createCoreClass();
        
        $this->db->setQuery(SELECT," 
                            id_tipopg,
                            id_regiao,
                            id_projeto,
                            tipopg,
                            campo1,
                            campo2,
                            campo3,
                            status_reg
                            ");
        
        $this->db->setQuery(FROM,"tipopg ");
        
        
        if(is_object($this->getSuperClass())){

            $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();        
            
            $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();        
            
            $id_clt = $this->getSuperClass()->Clt->getIdClt();        

            $id_tipopg = $this->getSuperClass()->Clt->getTipoPagamento();        
           
        }        
        else {

            $id_regiao = $this->getIdRegiao();        
            
            $id_projeto = $this->getIdProjeto();        
            
            $id_clt = $this->getIdClt();        

            $id_tipopg = $this->getTipoPagamento();        
            
        }

        $this->db->setQuery(WHERE,"1=1",ADD);
        
        if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

        if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}

        if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND campo1 = {$id_clt}",ADD);}

        if(!empty($id_ferias)) {$this->db->setQuery(WHERE,"AND id_tipopg = {$id_tipopg}",ADD);}
        
        $this->db->setQuery(ORDER,
                            "
                            id_regiao DESC,
                            id_projeto DESC    
                            ");
        
        if(!$this->db->setRs()) $this->error->set('Houve um erro na query de consulta do método select da classe RhTipoPgClass');
        
        return $this;
        
            
    }


}