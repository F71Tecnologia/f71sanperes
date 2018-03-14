<?php

/*
 * CONTRIBUI��O SINDICAL
 * 
 *  - O FUNCION�RIO TEM Q ESTAR MARCADO Q N�O CONTRIBUIU ESSE ANO, E Q N�O � ISENTO
 * 
 *  - O MES DE DESCONTO DA CONTRIBUI�AO SINDICAL � MAR�O (03)
 * 
 *  - O FUNCION�RIO N�O PODE ESTAR SOB LICEN�A MATERNIDADE
 * 
 *  - O FUNCION�RIO N�O PODE ESTAR COM 30 DIAS DE EVENTO
 * 
 */

include_once('ExtMovs.php');

/**
 * Description of ClassContribuicaoSindical
 *
 * @author Ramon
 */
class CalcContribuicaoSindical extends ExtMovs {
    private $objMovimento;

    public function ContribuicaoSindical($info) {
        
        $retorno = array();
        $mesInt = (int) $info['mes_folha'];
        $mesContribuicao = 3;
        $valorContribuicao = 0;
        $umDiaDeTrabalho = $info['salbase'] / 30;
        
        //VERIFICA SE A PESSOA N�O ESTA ISENTA ESSE ANO
        if ($info['ano_contribuicao'] != $info['ano_folha'] && $info['terceiro'] == 2 && $info['status_clt'] != 50 && $mesInt == $mesContribuicao) {
            
            //AQUI � O CASO PARA TODOS OS FUNCION�RIOS NO MES 3
            $valorContribuicao = $umDiaDeTrabalho;
            
        }else if ($mesInt >= $mesContribuicao && $info['novo_em_folha'] == 1 && $info['ano_contribuicao'] != $info['ano_folha']) {
            
            // VERIFICANDO SE � ADMISSAO, POIS SE FOR ADMISS�O AP�S O MES DA CONTRIBUI��O TEM Q CONTRIBUIR
            $valorContribuicao = $umDiaDeTrabalho;
            
        }
        
        if($info['dias_trab'] <= 0){
            $valorContribuicao = 0;
        }
        
        
        /*VAMOS LAN�AR O MOVIMENTO*/
        if($valorContribuicao > 0){
        
            /**
            * INCLUDES 
            */
            include_once('MoviemntoClass.php');

            /**
             * OBJETO
             */
            $this->objMovimento = new Movimentos();

            /**
            * LAN�ANDO MOVIMENTO
            */
            $legenda = "({$info['salbase']} / 30)";

            $this->objMovimento->carregaMovimentos($info['ano_folha']);
            $this->objMovimento->setIdClt($info['id_clt']); 
            $this->objMovimento->setMes($info['mes_folha']);
            $this->objMovimento->setAno($info['ano_folha']);
            $this->objMovimento->setIdRegiao($info['id_regiao']);
            $this->objMovimento->setIdProjeto($info['id_projeto']);   
            $this->objMovimento->setLegenda($legenda);
            $this->objMovimento->setIdMov(21);
            $this->objMovimento->setCodMov(5019);
            $this->objMovimento->setLancadoPelaFolha(1);
            $this->objMovimento->setIdFolha($info['id_folha']);
            $this->objMovimento->verificaInsereAtualizaFolha($valorContribuicao); 
            

            $this->objMovimento->limpaVariaveis();

            /**
             * PREENCHENDO ARRAY DE 
             * RETORNO
             */
            $retorno = array(
                "contribuicao_sindical" => $valorContribuicao                        
            );
        }
        
        return $retorno;
        
    }
}
