<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('ExtMovs.php');

/**
 * Description of CalcAdNoturno
 *
 * @author Sinesio
 */
class CalcAdNoturno extends ExtMovs{
    
    private $objCalcFolha;
    private $objMovimento; 
     
    public function AdNoturno($inf){
        
        $retorno = array(); 
        
        if($inf['adicional_noturno'] == 1){
            
            echo "AdNoturno <br />";
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
                 * 
                 */
                $baseCalcAdicionalNoturno = $inf['salbase'] + $inf['valor_proporcional_insalubridade'];

                /**
                 * 
                 */
                $adicional_noturno = $this->objCalcFolha->getAdicionalNoturno(
                                        $baseCalcAdicionalNoturno,
                                        $inf['horas_mes'],
                                        $inf['horas_noturnas'],
                                        $inf['dias_trab'],  
                                        $inf['id_curso']
                                    ); 
                 
                
                /**
                * LANÇANDO MOVIMENTO
                */
                $aliquota = $adicional_noturno['percent'] * 100;
                $baseCalcAdicionalNoturno = number_format($baseCalcAdicionalNoturno,2,',','.');
                $legenda = "((({$baseCalcAdicionalNoturno} / {$inf['horas_mes']}) x {$adicional_noturno['hora_noturna_adnoturno']}) x {$aliquota}%) / 30 x {$inf['dias_trab']} ";
                
                $this->objMovimento->carregaMovimentos($inf['ano_folha']);
                $this->objMovimento->setIdClt($inf['id_clt']); 
                $this->objMovimento->setMes($inf['mes_folha']);
                $this->objMovimento->setAno($inf['ano_folha']);
                $this->objMovimento->setIdRegiao($inf['id_regiao']);
                $this->objMovimento->setIdProjeto($inf['id_projeto']);   
                $this->objMovimento->setLegenda($legenda);
                $this->objMovimento->setIdMov($adicional_noturno['id_mov']);
                $this->objMovimento->setCodMov($adicional_noturno['cod_mov']);               
                $this->objMovimento->setLancadoPelaFolha(1); 
                $this->objMovimento->setIdFolha($inf['id_folha']);
                $this->objMovimento->verificaInsereAtualizaFolha($adicional_noturno['valor_proporcional']); 
                $this->objMovimento->limpaVariaveis();
                
                /**
                * PREENCHENDO ARRAY DE 
                * RETORNO
                */
                $retorno = array(
                    "percent_adnoturno" => $adicional_noturno['percent'], 
                    "valor_integral_adnoturno" => $adicional_noturno['valor_integral'],
                    "valor_proporcional_adnoturno" => $adicional_noturno['valor_proporcional']                         
                );
            
            }  catch (Exception $e){
                echo $e->getMessage();
            }  
        } else{
            
            $this->removeMov($inf['id_clt'], $inf['id_folha'], 66, 9000);
            
        }
        
        /**
         * LIMPANDO ATTR
         */
        $this->objCalcFolha = null;
        $this->objMovimento = null; 
        
        return $retorno;
    }
     
}
