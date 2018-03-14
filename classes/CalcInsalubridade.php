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
class CalcInsalubridade extends ExtMovs{
     
    private $objCalcFolha;
    private $objMovimento; 
     
    public function Insalubridade($inf){
        
        $retorno = array();
        
        if($inf['insalubridade_clt'] == 1){
            echo "insalubridade <br />"; 
             
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

                /**
                * CALCULO DE 
                * INSALUBRIDADE
                */
                $valorInsalubridade = $this->objCalcFolha->getInsalubridade(
                                      $inf['dias_trab'], 
                                      $inf['tipo_insalubridade'], 
                                      $inf['qnt_salminimo_insalu'], 
                                      $inf['ano_folha'], 
                                      null, 
                                      0, 
                                      $inf['sallimpo']); 
                
                /**
                * LANÇANDO MOVIMENTO
                */
                $aliquota = $valorInsalubridade['percentual'] * 100;
                $salarioMinimo = number_format($valorInsalubridade['salario_minimo'], 2,',','.');
                $legenda = "(({$salarioMinimo} x {$aliquota}%) /30) x {$inf['dias_trab']}";
                
                $this->objMovimento->carregaMovimentos($inf['ano_folha']);
                $this->objMovimento->setIdClt($inf['id_clt']); 
                $this->objMovimento->setMes($inf['mes_folha']);
                $this->objMovimento->setAno($inf['ano_folha']);
                $this->objMovimento->setIdRegiao($inf['id_regiao']);
                $this->objMovimento->setIdProjeto($inf['id_projeto']);   
                $this->objMovimento->setLegenda($legenda);
                $this->objMovimento->setIdMov($valorInsalubridade['id_mov']);
                $this->objMovimento->setCodMov($valorInsalubridade['cod_mov']);               
                $this->objMovimento->setLancadoPelaFolha(1); 
                $this->objMovimento->setIdFolha($inf['id_folha']);
                
                if($valorInsalubridade['valor_proporcional'] > 0){
                    $this->objMovimento->verificaInsereAtualizaFolha($valorInsalubridade['valor_proporcional']); 
                }
                
                $this->objMovimento->limpaVariaveis();
                
                /**
                 * PREENCHENDO ARRAY DE 
                 * RETORNO
                 */
                $retorno = array(
                    "percent_insalubridade" => $valorInsalubridade['percentual'],
                    "salario_minimo_insalubridade" => $valorInsalubridade['salario_minimo'],
                    "valor_integral_insalubridade" => $valorInsalubridade['valor_integral'],
                    "valor_proporcional_insalubridade" => $valorInsalubridade['valor_proporcional']                         
                );
                 
            }  catch (Exception $e){
                echo $e->getMessage();
            }  
            
        }else{
            
            $this->removeMov($inf['id_clt'], $inf['id_folha'], 56, 6006);
            
        }
        
        /**
         * LIMPANDO ATTR
         */
        $this->objCalcFolha = null;
        $this->objMovimento = null;
        
        return $retorno;
        
    }  
    
}
