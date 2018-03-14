<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('ExtMovs.php');

/**
 * Description of CalcDsr
 *
 * @author Sinesio
 */
class CalcDsr extends ExtMovs{
    
    private $objCalcFolha;
    private $objMovimento; 
    
    //put your code here
    public function Dsr($inf){
        
        $retorno = array(); 
         
        if($inf['adicional_noturno'] == 1){
            
            //echo "DSR <br />";
            try{
                
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
                
                $dsr = $this->objCalcFolha->getDsr($inf['valor_integral_adnoturno'], $inf['dias_trab']);  
                  
                /**
                * LANÇANDO MOVIMENTO
                */
                $baseCalcAdicionalNoturno = number_format($inf['valor_integral_adnoturno'],2,',','.');
                $legenda = "({$baseCalcAdicionalNoturno} / 30) x {$dsr['diasDsr']}";
                
                
                $this->objMovimento->carregaMovimentos($inf['ano_folha']);
                $this->objMovimento->setIdClt($inf['id_clt']); 
                $this->objMovimento->setMes($inf['mes_folha']);
                $this->objMovimento->setAno($inf['ano_folha']);
                $this->objMovimento->setIdRegiao($inf['id_regiao']);
                $this->objMovimento->setIdProjeto($inf['id_projeto']);   
                $this->objMovimento->setLegenda($legenda);
                $this->objMovimento->setIdMov($dsr['id_mov']);
                $this->objMovimento->setCodMov($dsr['cod_mov']);               
                $this->objMovimento->setLancadoPelaFolha(1); 
                $this->objMovimento->setIdFolha($inf['id_folha']);
                $this->objMovimento->verificaInsereAtualizaFolha($dsr['valor_proporcional']); 
                $this->objMovimento->limpaVariaveis(); 
                
                 /**
                * PREENCHENDO ARRAY DE 
                * RETORNO
                */
                $retorno = array( 
                    "valor_integral_dsr" => $dsr['valor_integral'],
                    "valor_proporcional_dsr" => $dsr['valor_proporcional']                         
                );
                
                
            }  catch (Exception $e){
                echo $e->getMessage();
            }  
        } else{
            
            $this->removeMov($inf['id_clt'], $inf['id_folha'], 199, 9997);
            
        } 
        
        /**
         * LIMPANDO ATTR
         */
        $this->objCalcFolha = null;
        $this->objMovimento = null;
        
        return $retorno;
    } 
     
    
}
