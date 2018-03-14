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
class CalcAuxilioCreche extends ExtMovs{
     
    private $objCalcFolha;
    private $objMovimento; 
     
    public function AuxilioCreche($inf){
         
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
        
        /**
         * 
         */
        if($inf['creche'] == 1 && $inf['status_clt'] != 67){
            
            try {
                
                $valorFixoAuxCreche   = $inf['creche_base'];
                $porcentagemAuxCreche = $inf['creche_percentual'];
                $idadeAuxCreche       = $inf['creche_idade'] + 0.1;
                $piso                 = $inf['piso'];

                $queryVerDependente  = "SELECT * FROM dependentes AS A WHERE
                                            A.id_bolsista = '{$inf['id_clt']}' AND A.id_projeto = '{$inf['id_projeto']}'  AND A.contratacao = 2";

                 
                $sqlVerDependente = mysql_query($queryVerDependente) or die("Erro ao selecionar participantes");                            
                if(mysql_num_rows($sqlVerDependente) > 0){
                    $filhos = array();
                    while($rowsDependentes = mysql_fetch_assoc($sqlVerDependente)){
                        
                        $dataAtual = date("Y-m-d");
                        
                        if($inf['sexo'] == 'F' || ($inf['sexo'] == 'M' && $rowsDependentes['possui_guarda1'] == 1)){
                            /**
                            * FILHO 1 
                            */
                           if(!empty($rowsDependentes['data1'])){
                              $dias1 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data1'])) / (60 * 60 * 24));
                           }else{
                              $dias1 = 0; 
                           }

                           $filhos[1]["nome"]        = $rowsDependentes['nome1'];
                           $filhos[1]["nascimento"]  = $rowsDependentes['data1'];
                           $filhos[1]["dias"]        = $dias1;
                           $filhos[1]["idade"]       = $dias1/365;                

                           /**
                            * FILHO 2 
                            */
                           if(!empty($rowsDependentes['data2'])){
                               $dias2 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data2'])) / (60 * 60 * 24));
                           }else{
                               $dias2 = 0;
                           }

                           $filhos[2]["nome"]        = $rowsDependentes['nome2'];
                           $filhos[2]["nascimento"]  = $rowsDependentes['data2'];
                           $filhos[2]["dias"]        = $dias2;
                           $filhos[2]["idade"]       = $dias2/365;                

                           /**
                            * FILHO 3 
                            */
                           if(!empty($rowsDependentes['data3'])){
                               $dias3 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data3'])) / (60 * 60 * 24));
                           }else{
                               $dias3 = 0;
                           }

                           $filhos[3]["nome"]        = $rowsDependentes['nome3'];
                           $filhos[3]["nascimento"]  = $rowsDependentes['data3'];
                           $filhos[3]["dias"]        = $dias3;
                           $filhos[3]["idade"]       = $dias3/365;   

                           /**
                            * FILHO 4 
                            */
                           if(!empty($rowsDependentes['data4'])){
                               $dias4 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data4'])) / (60 * 60 * 24));
                           }else{
                               $dias4 = 0;
                           }

                           $filhos[4]["nome"]        = $rowsDependentes['nome4'];
                           $filhos[4]["nascimento"]  = $rowsDependentes['data4'];
                           $filhos[4]["dias"]        = $dias4;
                           $filhos[4]["idade"]       = $dias4/365;   

                           /**
                            * FILHO 5 
                            */
                           if(!empty($rowsDependentes['data5'])){
                               $dias5 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data5'])) / (60 * 60 * 24));
                           }else{
                               $dias5 = 0;
                           }

                           $filhos[5]["nome"]        = $rowsDependentes['nome5'];
                           $filhos[5]["nascimento"]  = $rowsDependentes['data5'];
                           $filhos[5]["dias"]        = $dias5;
                           $filhos[5]["idade"]       = $dias5/365;   

                           /**
                            * FILHO 6 
                            */
                           if(!empty($rowsDependentes['data6'])){
                               $dias6 = floor((strtotime($dataAtual) - strtotime($rowsDependentes['data6'])) / (60 * 60 * 24));
                           }else{
                               $dias6 = 0;
                           }

                           $filhos[6]["nome"]        = $rowsDependentes['nome6'];
                           $filhos[6]["nascimento"]  = $rowsDependentes['data6'];
                           $filhos[6]["dias"]        = $dias6;
                           $filhos[6]["idade"]       = $dias6/365;  
                        }else{
                            
                            $this->objMovimento->removeMovimento($inf['id_clt'], $inf['id_folha'], array(397)); 
                            
                        }  
                    }               
                }


                /**
                 * PELA MILESIMA VEZ ... 
                 * ESTOU ALTERANDO A REGRA DE 
                 * AUXILIO CRECHE 
                 * REMOVENDO O  + 1
                 * EM: 22/08/2016
                 * Por: SINESIO LUIZ
                 */
                for($i=1;$i<=6;$i++){
                   if(($filhos[$i]["idade"] <= ($idadeAuxCreche)) && $filhos[$i]["idade"] > 0){
                       $countFilhos++;
                   } 
                }        
 
                if($countFilhos > 0){
                    if(!empty($valorFixoAuxCreche) && $valorFixoAuxCreche != '0.00' && $valorFixoAuxCreche > 0){
                        $valorAuxCreche = $valorFixoAuxCreche * $countFilhos;
                        $legenda = "{$valorFixoAuxCreche} x {$countFilhos} Filho(s)";
                    }else{
                        $valorAuxCreche = ($piso * $porcentagemAuxCreche) * $countFilhos;
                        $legenda = "({$piso} x {$porcentagemAuxCreche}) x {$countFilhos} Filho(s)";
                    }     

                    if($valorAuxCreche > 0){

                        /**
                         * AUXILIO CRECHES    
                         */
                        $this->objMovimento->carregaMovimentos($inf['ano_folha']);
                        $this->objMovimento->setIdClt($inf['id_clt']); 
                        $this->objMovimento->setMes($inf['mes_folha']);
                        $this->objMovimento->setAno($inf['ano_folha']);
                        $this->objMovimento->setIdRegiao($inf['id_regiao']);
                        $this->objMovimento->setIdProjeto($inf['id_projeto']);   
                        $this->objMovimento->setLegenda($legenda);
                        $this->objMovimento->setIdMov(397);
                        $this->objMovimento->setCodMov(80048); 
                        $this->objMovimento->setLancadoPelaFolha(1);    
                        $this->objMovimento->setIdFolha($inf['id_folha']);
                        if($valorAuxCreche>0){
                            $this->objMovimento->verificaInsereAtualizaFolha($valorAuxCreche,'1,2');
                        }
                    }
                }else{
                    $this->objMovimento->removeMovimento($clt,397);        
                }  

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
