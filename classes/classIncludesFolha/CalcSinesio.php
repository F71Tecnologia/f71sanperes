<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('ExtMovs.php');

/**
 * Description of CalcInsalubridade
 *
 * @author Sinesio
 */
class CalcSinesio extends ExtMovs{
     
    private $objCalcFolha;
    private $objMovimento; 
     
    public function Sinesio($inf){
         
        /**
        * INCLUDES 
        */ 
        include_once('CalculoFolhaClass.php');
        include_once('MoviemntoClass.php');

        /**
         * OBJETO
         */ 
        $this->objCalcFolha = new Calculo_Folha();
        $this->objMovimento = new Movimentos();
        
        /**
         * RETORNO DO METODO
         */
        $retorno = array();
        
        if($validacacoAqui){
            
            try {

                /**
                * PREENCHENDO ARRAY DE 
                * RETORNO SE 
                 * NECESSÁRIO
                */
               $retorno = array();

            } catch (Exception $e) {
                echo $e->getMessage();
            }
            
        }
        
        /**
         * LIMPANDO ATTR
         */
        $this->objMovimento = null;

        return $retorno;
        
    }  
    
}
