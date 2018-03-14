<?php

/*
 * OBSERVAÇÕES SOBRE O CALCULO DE VALE TRANSPORTE
 * 
 *  - O SISTEMA DEVE CALCULAR QNT A PESSOA GASTA POR MES
 *      - PARA DIARISTAS BASTA CONTAR QNTS DIAS UTEIS NO MES
 *      - PARA HORISTAS/PLANTONISTAS VERIFICAR DIAS_MES TABELA HORARIO (DEPENDENDO DO MES O RH DEVE FAZER UPDATE NO CAMPO)
 * 
 *  - O SISTEMA NÃO PODE DESCONTAR DE FUNCIONÁRIOS DE FÉRIAS
 * 
 *  - O SISTEMA NÃO PODE DESCONTAR EM FOLHA DE 13
 */

/**
 * PERGUNTAS
 * 
 * - OS 6% SEMPRE PEGO DO SALARIO BASE? OU DESCONTA FALTAS ENTRE OUTROS?
 * - SE A PESSOA TRABALHOU APENAS 15 E OS OUTROS 15 FICOU SOB EVENTO, OS 6% PROPORCIONALIZA OS 15 DIAS
 */

include_once('ExtMovs.php');

/**
 * Description of CalcValeTransporte
 *
 * @author Ramon
 */
class CalcValeTransporteSimples extends ExtMovs {

    private $objMovimento;

    public function ValeTransporteSimples($info) {
        
        $retorno = array();
        
        //VERIFICA SE O FUNCIONÁRIO TEM VALE TRANSPORTE, SE A FOLHA FOR NORMAL E NÃO ESTEJA DE FÉRIAS
        if ($info['transporte'] == 1 && $info['terceiro'] == 2 && $info['status_clt'] != 40) {
            
            /**
            * INCLUDES 
            */
            include_once('MoviemntoClass.php');

            /**
             * OBJETO
             */
            $this->objMovimento = new Movimentos();

            //TIRANDO OS 6%
            $aliquota = 0.06;
            $vale_transporte = $info['salbase'] * $aliquota;

            /**
            * LANÇANDO MOVIMENTO
            */
            $legenda = "({$info['salbase']} * {$aliquota})";

            $this->objMovimento->carregaMovimentos($info['ano_folha']);
            $this->objMovimento->setIdClt($info['id_clt']); 
            $this->objMovimento->setMes($info['mes_folha']);
            $this->objMovimento->setAno($info['ano_folha']);
            $this->objMovimento->setIdRegiao($info['id_regiao']);
            $this->objMovimento->setIdProjeto($info['id_projeto']);   
            $this->objMovimento->setLegenda($legenda);
            $this->objMovimento->setIdMov(203);
            $this->objMovimento->setCodMov(7001);
            $this->objMovimento->setLancadoPelaFolha(1); 
            $this->objMovimento->setIdFolha($info['id_folha']);

            if($vale_transporte > 0){
                $this->objMovimento->verificaInsereAtualizaFolha($vale_transporte); 
            }

            $this->objMovimento->limpaVariaveis();
            
            $sal = $info['salario'] - $vale_transporte;
            
            /**
             * PREENCHENDO ARRAY DE 
             * RETORNO
             */
            $retorno = array(
                "vale_transporte" => $vale_transporte,
                "salario" => $sal
            );
        }
        
        return $retorno;
        
    }

}
