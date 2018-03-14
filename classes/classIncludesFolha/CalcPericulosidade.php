<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('ExtMovs.php');

/**
 * Description of CalcPericulosidade
 *
 * @author Ramon
 */
class CalcPericulosidade extends ExtMovs{
    private $objCalcFolha;
    private $objMovimento; 
    
    //put your code here
    public function Periculosidade($inf){
        
        $retorno = array();
        
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
        
        if($inf['periculosidade_30'] == 1 && $inf['dias_trab'] > 0){
            
            try{
                $periculosidade = $this->objCalcFolha->getPericulosidade($inf['sallimpo'], $inf['dias_trab']);

                $legenda = "({$inf['sallimpo']} * 0.30)/30) * {$inf['dias_trab']}";

                $this->objMovimento->carregaMovimentos($inf['ano_folha']);
                $this->objMovimento->setIdClt($inf['id_clt']); 
                $this->objMovimento->setMes($inf['mes_folha']);
                $this->objMovimento->setAno($inf['ano_folha']);
                $this->objMovimento->setIdRegiao($inf['id_regiao']);
                $this->objMovimento->setIdProjeto($inf['id_projeto']);   
                $this->objMovimento->setLegenda($legenda);
                $this->objMovimento->setIdMov(57);
                $this->objMovimento->setCodMov(6007);
                $this->objMovimento->setLancadoPelaFolha(1); 
                $this->objMovimento->setIdFolha($inf['id_folha']);

                if($periculosidade['valor_proporcional'] > 0){
                    $this->objMovimento->verificaInsereAtualizaFolha($periculosidade['valor_proporcional']); 

                    /**
                     * PREENCHENDO ARRAY DE 
                     * RETORNO
                     */
                    $retorno = array(
                        "periculosidade_integral" => $periculosidade['valor_integral'],
                        "periculosidade_proporcional" => $periculosidade['valor_proporcional'],
                        "periculosidade_13_integral" => $periculosidade['valor_13_integral']
                    );
                }
            }  catch (Exception $e){
                echo $e->getMessage();
            }  
            
        }else{
            
        }
        
    }
}
