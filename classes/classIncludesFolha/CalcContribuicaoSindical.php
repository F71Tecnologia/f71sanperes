<?php

/*
 * CONTRIBUIÇÃO SINDICAL
 * 
 *  - O FUNCIONÁRIO TEM Q ESTAR MARCADO Q NÃO CONTRIBUIU ESSE ANO, E Q NÃO É ISENTO
 * 
 *  - O MES DE DESCONTO DA CONTRIBUIÇAO SINDICAL É MARÇO (03)
 * 
 *  - O FUNCIONÁRIO NÃO PODE ESTAR SOB LICENÇA MATERNIDADE
 * 
 *  - O FUNCIONÁRIO NÃO PODE ESTAR COM 30 DIAS DE EVENTO
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
        
        //VERIFICA SE A PESSOA NÃO ESTA ISENTA ESSE ANO
        if ($info['ano_contribuicao'] != $info['ano_folha'] && $info['terceiro'] == 2 && $info['status_clt'] != 50 && $mesInt == $mesContribuicao) {
            
            //AQUI É O CASO PARA TODOS OS FUNCIONÁRIOS NO MES 3
            $valorContribuicao = $umDiaDeTrabalho;
            
        }else if ($mesInt >= $mesContribuicao && $info['novo_em_folha'] == 1 && $info['ano_contribuicao'] != $info['ano_folha']) {
            
            // VERIFICANDO SE É ADMISSAO, POIS SE FOR ADMISSÃO APÓS O MES DA CONTRIBUIÇÃO TEM Q CONTRIBUIR
            $valorContribuicao = $umDiaDeTrabalho;
            
        }
        
        if($info['dias_trab'] <= 0){
            $valorContribuicao = 0;
        }
        
        
        /*VAMOS LANÇAR O MOVIMENTO*/
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
            * LANÇANDO MOVIMENTO
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
