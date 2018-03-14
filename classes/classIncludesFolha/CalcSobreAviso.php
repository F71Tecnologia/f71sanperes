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
class CalcSobreAviso extends ExtMovs {
 
    private $objMovimento;

    public function SobreAviso($inf) {
        
        /**
        * INCLUDES 
        */ 
        include_once('MoviemntoClass.php');

        /**
         * OBJETO
         */ 
        $this->objMovimento = new Movimentos();

        if ($inf['indenizacao_sobre_aviso'] == 1) {

            try {
 
                $legenda = "(({$inf['salbase']} x 33,33%) / 30) x {$inf['dias_trab']}";
                
                $valorSobreAviso = (($inf['salbase'] * 0.3333) / 30) * $inf['dias_trab'];
                
                $this->objMovimento->carregaMovimentos($inf['id_folha']);
                $this->objMovimento->setIdClt($inf['id_clt']);
                $this->objMovimento->setMes($inf['mes_folha']);
                $this->objMovimento->setAno($inf['ano_folha']);
                $this->objMovimento->setIdRegiao($inf['id_regiao']);
                $this->objMovimento->setIdProjeto($inf['id_projeto']);
                $this->objMovimento->setIdFolha($inf['id_folha']);
                $this->objMovimento->setLegenda($legenda);
                $this->objMovimento->setIdMov(395);
                $this->objMovimento->setCodMov(10013);
                $this->objMovimento->setLancadoPelaFolha(1);
                

                if ($valorSobreAviso > 0) {
                    $verifica = $this->objMovimento->verificaInsereAtualizaFolha($valorSobreAviso);
                }

                $this->objMovimento->limpaVariaveis();

                /**
                 * PREENCHENDO ARRAY DE 
                 * RETORNO
                 */
                $retorno = array();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            
        } else {
            $this->objMovimento->removeMovimento($inf['id_clt'], $inf['id_folha'], array(395));
        }

        /**
         * LIMPANDO ATTR
         */
        $this->objMovimento = null;

        return $retorno;
    }

}
