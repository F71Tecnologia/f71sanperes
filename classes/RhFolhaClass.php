<?php 
/*
 * PHO-DOC - RhFolhaClass.php
 * 
 * Classe da folha de pagamento 
 * 
 * 22-09-2015 
 *
 * @name RhFolhaClass 
 * @package RhFolhaClass
 * @access public 
 * 
 * @version 
 *
 * Versão: 3.0.0000 - 22/09/2015 - Jacques - Versão Inicial
 * Versão: 3.0.4562 - 01/12/2015 - Jacques - Correção na carga das variáveis do método select com os parámetros de intervalo
 * Versão: 3.0.4579 - 02/12/2015 - Jacques - Implementação do método getCollectionRescisoes 
 * Versão: 3.0.4903 - 14/12/2015 - Jacques - Desconsideredo provisoriamente a seleção por projeto no método getCollectionMovimentosFixosVariaveis para poder pegar funcionários que tiveram transferência de projeto
 * Versão: 3.0.4903 - 14/12/2015 - Jacques - Adicionado campos para serem somados no agrupamento do getCollectionRescisoes e criado condição no parametro de adição de campo
 *                                           para quando houver mais de um registro na tabela rh_recisao para rescisão complementar, não carregar duas vezes os movimentos
 * Versão: 3.0.5316 - 05/01/2016 - Jacques - Adicionado ao método select a posibilidade de filtrar pelo id da folha.
 * Versão: 3.0.5326 - 05/01/2016 - Jacques - Correção do bug que tinha na linha de macro de RhFolhaClass->getCollection agrupamento total geral a repetição do campo calculado valor_aviso_fun
 * Versão: 3.0.5639 - 19/01/2016 - Jacques - A classe data está gerando alguns bugs no framework quando é fazia, pois no início ela ficava setada com 19700101 e não fazia, isso em alguns caso como no select
 *                                           dessa classe estava fazando a chave ser considerada. Código do movimento da lagos passados por Gimenez
 *                                           que deverão entrar no calculo do salário variável '5012, 80024, 7004, 9000, 8080, 8005, 5912, 5061, 9997, 50242, 50227, 50228, 80025' 
 * Versão: 3.0.0000 - 14/03/2015 - Jacques - Correção no uso do método que estava chamando $this->value quando deveria ser $this->setValue
 * Versão: 3.0.7848 - 14/03/2015 - Jacques - Atualização da classe para instânciamento dinâmico
 * 
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */   

class RhFolhaClass {
    /*
     * PHP-DOC 
     * 
     * @name getTotalIdsMovimentosEstatisticas
     * 
     * @internal - Método que obtem todos os Ids de movimentos Estatisticos das folhas
     * 
     * Para você chamar esse médoto é necessário instanciar a classe folha e passar os seguintes parâmetros antes de sua execução
     * 
     * $this->getSuperClass()->Folha->setDefault();
     * $this->getSuperClass()->Folha->setTerceiro(2);
     * $this->getSuperClass()->Folha->setDateRangeField("CONCAT(f.ano,LPAD(f.mes,2,'00'))");
     * $this->getSuperClass()->Folha->setDateRangeFmt('Ym');
     * $this->getSuperClass()->Folha->setDateRangeIni($this->getDateRangeIni());
     * $this->getSuperClass()->Folha->setDateRangeEnd($this->getDateRangeEnd());
     * 
     * Obs: Esses parâmetros não estão internos dentro do método para permitir flexibilidade na consulta do intervalo
     * 
     */      
    public function getTotalIdsMovimentosEstatisticas(){
        
        try {
            
            $this->db->setQuery(SELECT,
                                " 
                                GROUP_CONCAT(DISTINCT ids_movimentos_estatisticas) AS ids_movimentos_estatisticas
                                ");

            $this->db->setQuery(FROM,"rh_folha f INNER JOIN rh_folha_proc fp ON f.id_folha=fp.id_folha"); 

            /*
             * Status 3 indica folha processada
             */
            $this->db->setQuery(WHERE,"f.status = 3",ADD);

            if(is_object($this->getSuperClass()->Clt)){ 

                $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
                $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();
                $id_clt = $this->getSuperClass()->Clt->getIdClt();

            }        
            else {

                $id_regiao = $this->getIdRegiao();
                $id_projeto = $this->getIdProjeto();
                $id_clt = $this->getIdClt();

            } 

            $terceiro = $this->getTerceiro();

            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND f.regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND f.projeto = {$id_projeto}",ADD);}

            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND fp.id_clt = {$id_clt}",ADD);}

            if(!empty($terceiro)) {$this->db->setQuery(WHERE,"AND f.terceiro = {$terceiro}",ADD);}
            
            $this->setDateRangeQuery();
            
            $this->db->setQuery(ORDER,"f.ano DESC, f.mes DESC;");
            
            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->db->setRow();

            $this->setIdsMovimentosEstatisticas(str_replace(",,", ",",$this->db->getRow('ids_movimentos_estatisticas')));

            $this->setValue(1); 
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
                
        }
        
        return $this->getIdsMovimentosEstatisticas();
        
    }    
    /*
     * PHP-DOC - Obtem um conjunto de registros para movimentos fixos (que se repetem) e excepcionais variáveis dentro de um período
     */
    public function getCollectionMovimentosFixosVariaveis(){
        
        try {

            if(!is_object($this->getSuperClass()->Clt) || !is_object($this->getSuperClass()->FolhaProc) || !is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->setRegiao($this->getSuperClass()->Clt->getIdRegiao());
            
            $this->setTerceiro(2);
            $this->setDateRangeFmt('Ym');
            $this->setDateRangeField("CONCAT(ano,mes)");
            $this->setStatus(3);
            $data_ini = $this->getDateRangeIni();
            $data_fim = $this->getDateRangeEnd();
            
            

            $movimentos['total_geral'] = 0;
            
            /*
             * Lançamento de movimentos variáveis fora da folha
             */
            $this->getSuperClass()->MovimentosClt->setDefault();
            $this->getSuperClass()->MovimentosClt->setStatus(1);
            $this->getSuperClass()->MovimentosClt->setStatusReg(1);
            $this->getSuperClass()->MovimentosClt->setStatusFerias(1);
            $this->getSuperClass()->MovimentosClt->setIdClt($this->getSuperClass()->Clt->getIdClt());
            $this->getSuperClass()->MovimentosClt->setAnoMov($this->date->now('Y'));
            $this->getSuperClass()->MovimentosClt->setMesMov($this->getSuperClass()->getKeyMaster(11));
            $this->getSuperClass()->MovimentosClt->setTipoMovimento('CREDITO'); 

            /*
             * Não pode entrar para a média dos movimentos de salário variável a insalubridade e periculosidade
             * 
             * Se houver o lançamento de movimento KeyMaster = 12 significa que a média foi definida na mão e não é para entrar mais nenhum valor
             */
            $this->getSuperClass()->MovimentosClt->setCodMovimento($this->getSuperClass()->getKeyMaster(9));

            $this->getSuperClass()->MovimentosClt->select();
            
            while ($this->getSuperClass()->MovimentosClt->getRow()->isOk()) {
                
                $movimentos_fixos[$this->getSuperClass()->MovimentosClt->getCodMovimento()]['nome_movimento'] = $this->getSuperClass()->MovimentosClt->getNomeMovimento();

                $movimentos_fixos[$this->getSuperClass()->MovimentosClt->getCodMovimento()]['valor_movimento'] = round($this->getSuperClass()->MovimentosClt->getValorMovimento(),2);
                
                $movimentos['total_geral'] += round($this->getSuperClass()->MovimentosClt->getValorMovimento() * ($this->getSuperClass()->MovimentosClt->getCodMovimento()==90020 || $this->getSuperClass()->MovimentosClt->getCodMovimento()==90021 ? 12 : 1),2);
                
            }
            
            while ($data_ini->val('Ymd') <= $data_fim->val('Ymd') && is_array($movimentos_fixos)) {
                
                $movimentos['collection'][$data_ini->val('Y')][$data_ini->val('m')]['itens'] = $movimentos_fixos;
                
                $data_ini->sumMonth();
                
            }
            
            $this->select();
            
            /*
             * Loop para definir os movimentos variáveis que foram lançados na folha. Caso não haja 12 folhas ou mais então não processa média
             */

            while ($this->getRow()->isOk() && $this->db->getNumRows() >= 12) {
                
                $this->getSuperClass()->FolhaProc->setDefault()->setIdFolha($this->getIdFolha())->setIdClt($this->getSuperClass()->Clt->getIdClt())->setStatus(3)->setAno($this->getAno())->setMes($this->getMes())->select();
                
                while ($this->getSuperClass()->FolhaProc->getRow()->isOk()) {
                    
                    $this->getSuperClass()->MovimentosClt->setDefault();
                    $this->getSuperClass()->MovimentosClt->setIdClt($this->getSuperClass()->Clt->getIdClt());
                    $this->getSuperClass()->MovimentosClt->setIdMovimento($this->getSuperClass()->Folha->getIdsMovimentosEstatisticas());
                    $this->getSuperClass()->MovimentosClt->setTipoMovimento('CREDITO');

                    /*
                     * Não pode entrar para a média dos movimentos de salário variável a insalubridade e periculosidade
                     */
                    $this->getSuperClass()->MovimentosClt->setCodMovimento($this->getSuperClass()->getKeyMaster(5));

                    $this->getSuperClass()->MovimentosClt->select();
                    
                    while ($this->getSuperClass()->MovimentosClt->getRow()->isOk()) { 
                        
                        /*
                         * Monta uma matriz com valores exclusivos para cada código de movimento que entra na folha
                         */
                            
                        if(empty($movimentos['collection'][$this->getAno()][$this->getMes()]['itens'][$this->getSuperClass()->MovimentosClt->getCodMovimento()]['valor_movimento'])){

                            $movimentos['total_geral'] += $this->getSuperClass()->MovimentosClt->getValorMovimento();

                            $movimentos['collection'][$this->getAno()][$this->getMes()]['itens'][$this->getSuperClass()->MovimentosClt->getCodMovimento()]['nome_movimento'] = $this->getSuperClass()->MovimentosClt->getNomeMovimento();

                            $movimentos['collection'][$this->getAno()][$this->getMes()]['itens'][$this->getSuperClass()->MovimentosClt->getCodMovimento()]['valor_movimento'] = $this->getSuperClass()->MovimentosClt->getValorMovimento();

                        }
                        
                    }
                    
                }
                
            }
            
            return isset($movimentos) ? $movimentos : 0;
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);

        }
        
        return 0;
        
    }   
    /*
     * Obtem a média de salário variável do período aquisitivo com verificação de consistência para o valor obtido
     * 
     * Obs.: Segundo email de Michele Souza em 18/01/2016 não fazem parte do salário variável a diferença de dissídio.
     */
    public function getCalcMediaSalarioVariavel(){ 
        
        try {
            
            $collection_salario_variavel = $this->getCollectionMovimentosFixosVariaveis();

            if($collection_salario_variavel){

                return round($collection_salario_variavel['total_geral']/12,2);

            }
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return 0;
        
    }
    /*
     * PHP-DOC - Obtem um conjunto de registros de movimentos rescisórios
     * 
     * PROVENTOS:
     * -------------------------------------------------------------------------
     * 'SALDO DE SALÁRIO'                      => $rescisao['saldo_salario'],
     * 'FÉRIAS PROPORCIONAIS'                  => $rescisao['ferias_pr'],
     * 'FÉRIAS VENCIDAS'                       => $rescisao['ferias_vencidas'],
     * '1/3 CONSTITUCIONAL <br> DE FÉRIAS'     => $rescisao['umterco_fv'] + $rescisao['umterco_fp'],
     * '13º SALÁRIO'                           => $rescisao['dt_salario'],
     * '13º SALÁRIO (Aviso-Prévio Indenizado)' => $rescisao['terceiro_ss'],
     * 'AVISO PRÉVIO'                          => $rescisao['aviso_credito'],
     * 'GRATIFICAÇÕES'                         => $gratificacao,
     * 'INSALUBRIDADE'                         => $rescisao['insalubridade'],
     * 'ADICIONAL NOTURNO'                     => $adicional_noturno,
     * 'HORAS EXTRAS'                          => $hora_extra,
     * 'DSR'                                   => $dsr,
     * 'MULTA ART. 477'                        => $rescisao['a477'],
     * 'MULTA ART. 479/CLT'                    => $rescisao['multa_a479'],
     * 'SALÁRIO FAMÍLIA'                       => $rescisao['salario_familia'],
     * 'DIFERENÇA SALARIAL'                    => $diferenca_salarial,
     * 'AJUDA DE CUSTO'                        => $ajuda_custo,
     * 'VALE TRANSPORTE'                       => $vale_transporte,
     * 'VALE REFEIÇÃO'                         => $vale_refeicao,
     * 'AJUSTE DE SALDO DEVEDOR'               => $rescisao['arredondamento_positivo'],
     * 'LEI 12.506 (AVISO PRÉVIO)'             => $rescisao['lei_12_506'],* 
     * 
     * MOVIMENTOS 
     * -------------------------------------------------------------------------
     * $gratificacao                           = $movimentos[52];     
     * $adicional_noturno                      = $movimentos[55];
     * $hora_extra                             = $movimentos[56];
     * $dsr                                    = $movimentos[58];
     * $diferenca_salarial                     = $movimentos[80];
     * $ajuda_custo                            = $movimentos[82];
     * $vale_transporte                        = $movimentos[107];
     * $vale_refeicao                          = $movimentos[108];                                
     * $pensao_alimenticia                     = $movimentos[100];
     * $adiantamento_salarial                  = $movimentos[101];
     * $desconto_vale_transporte               = $movimentos[106];
     * $desconto_vale_alimentacao              = $movimentos[109];
     * $outros                                 = $movimentos[115];
     * $faltas                                 = $movimentos[117];
     */
    public function getCollectionRescisoes(){

        $status_clt = $this->getSuperClass()->getKeyMaster(6);    

        $this->select();

        $collection_movimentos = $this->getSuperClass()->Movimentos->select()->db->getCollection('id_mov','');

        while ($this->getRow()->isOk()) {

            $this->getSuperClass()->FolhaProc->setWhere("status_clt IN ($status_clt)");

            $this->getSuperClass()->FolhaProc->select();

            while ($this->getSuperClass()->FolhaProc->getRow()->isOk()) {

                $total_row_movimentos['CREDITO'] = 0.00;
                $total_row_movimentos['DEBITO'] = 0.00;

                $collection_group_movimentos['movimentos'] = $this->getSuperClass()->MovimentosRescisao->select()->db->getCollection('id_mov','','','clear');

                foreach ($collection_group_movimentos['movimentos']['dados'] as $key => $value) {

                    $collection['movimentos']['dados'][$collection_movimentos['dados'][$key]['categoria']][$key]['descicao'] = $collection_movimentos['dados'][$key]['descicao'];

                    $collection['movimentos']['dados'][$collection_movimentos['dados'][$key]['categoria']][$key]['valor'] +=  $value['valor'];

                    $total_row_movimentos[$collection_movimentos['dados'][$key]['categoria']] += $value['valor'];

                }

                $collection['rescisoes'] = $this->getSuperClass()->Rescisao->select()->db->getCollection( 
                        /*
                         * Definição da forma de agrupamento do vetor
                         */
                        'id_clt',
                        /*
                         * Criação de campo calculado em linha
                         */
                          'terco_constitucional=umterco_fv+umterco_fp,'
                         ."total_movimento_credito=(rescisao_complementar ? 0 : {$total_row_movimentos['CREDITO']}),"
                         ."total_movimento_dedito=(rescisao_complementar ? 0 : {$total_row_movimentos['DEBITO']}),"
                         ."valor_aviso_fun=(motivo==65 ? aviso_valor : 0),"
                         ."valor_aviso_emp=(motivo!=65 ? aviso_valor : 0),"
                         ."proventos=valor_aviso_emp+saldo_salario+ferias_pr+ferias_vencidas+terco_constitucional+dt_salario+terceiro_ss+insalubridade+a477+a479+arredondamento_positivo+lei_12_506,"                                 
                         ."descontos=valor_aviso_fun+inss_ss+inss_dt+ir_ss+ir_dt,"
                         ."total_proventos=total_movimento_credito+proventos,"
                         ."total_descontos=total_movimento_dedito+descontos",
                        /*
                         * Definição dos campos que deverão ser somados de acordo com o agrupamento e total geral
                         */
                        'valor_aviso_emp,saldo_salario,ferias_pr,ferias_vencidas,terco_constitucional,dt_salario,terceiro_ss,insalubridade,a477,a479,arredondamento_positivo,lei_12_506,proventos,total_movimento_credito,total_proventos,total_rendimento,valor_aviso_fun,inss_ss,inss_dt,ir_ss,ir_dt,total_movimento_debito,descontos,total_descontos'
                        ); 

            }

        }
        
        return $collection;
        
    }    
    
    
}
