<?php
/*
 * PHO-DOC - RhCltClass.php
 * 
 * Classe de manipulação dos registros de clt 
 * 
 * 01-10-2015
 *
 * @name RhCltClass 
 * @package RhCltClass 
 * @access public 
 *  
 * @version 
 *
 * Versão: 3.0.5055 - 01/10/2015 - Jacques - Versão Inicial 
 * Versão: 3.0.7221 - 02/03/2016 - Jacques - Adicionado o método GetFields para uso no método select 
 * Versão: 3.0.7475 - 08/03/2016 - Jacques - Adicionado o método getRowExt para inclusão de propriedades extendidas da classe e o campo status_real_time 
 *                                           que gera uma consulta com o status real dos clts. 
 * Versão: 3.0.7769 - 10/03/2016 - Jacques - Atualização da classe para instânciamento dinâmico
 * Versão: 3.0.7769 - 10/03/2016 - Jacques - Adicionado o evento onUpdate para execução de processo na atualização da classe.
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br 
 *  
 */ 

const DIA = 0;
const MES = 1;
const ANO = 2; 

const VENCIDOS = 0;
const NA_DATA = 1;
const A_VENCER = 2;



class RhCltClass {
    
    private $status_contratacao = array(
                                        'Aguardando Contratação',
                                        'Período de Experiência',
                                        'Atividade Normal',
                                        'Aguardando Demissão'
                                        );
    private $rh_clt_ext = array(
                                'cod_status_contratacao' => 0,
                                'ativo' => 0,
                                'status_real_time' => 0,
                                'status_date_future' => '',
                                'dias_restantes_contrato_experiencia' => 0
                                );
    
    
    

    /*
     * PHP-DOC 
     * 
     * @name onUpdate
     * 
     * @internal - Qualquer inclusão, alteração ou exclusão de registros relacionados ao Clt deverão gerar esse
     *             evento.
     * 
     */   
    public function onUpdate(){
        
        $this->setDataUltimaAtualizacao($this->date->now());
        $this->update();
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setDiasRestanteContratoExperiencia
     * 
     * @internal - Método para armazenar na propriedade da classe os dias que restam para finalizar o contrato de experiência
     *             Caso o período já esteja finalizado esse valor será zerado
     * 
     */    
    public function setDiasRestanteContratoExperiencia($value){
        
        $this->rh_clt_ext['dias_restantes_contrato_experiencia'] = $value;
        
        return $this;
        
    }
    
    
    public function setCodStatusContratacao($value) {

        $this->rh_clt_ext['cod_status_contratacao'] = $value;
        
        return $this;

    }       
    
    /*
     * PHP-DOC 
     * 
     * @name setAtivos
     * 
     * @internal - Essa propriedade define uma condição para consultar apenas os Clts que estão ativos no sistema, ou seja, podem estar em evento
     *             mas não demitidos.
     * 
     */   
    public function setAtivo($value){
        
        $this->rh_clt_ext['ativo'] = $value;
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setUmAno
     * 
     * @internal - Essa propriedade define se o clt possui mais de um ano de tempo de serviço
     * 
     */   
    public function setUmAno($value){
        
        $this->rh_clt_ext['um_ano'] = $value;
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setStatusRealTime
     * 
     * @internal - Essa propriedade define o status do Clt em um determinado período de dada definida na propriedade setStatusDateFuture
     * 
     */   
    public function setStatusRealTime($value){
        
        $this->rh_clt_ext['status_real_time'] = $value;
        
        return $this;
        
    }

    /*
     * PHP-DOC 
     * 
     * @name setStatusDateFuture
     * 
     * @internal - Essa propriedade define uma data para averir o status do Clt a partir dela
     * 
     */   
    public function setStatusDateFuture($value){
        
        $this->rh_clt_ext['status_date_future'] = $value;
        
        return $this;
        
    }
    
    public function setSearch($value, $key, $operand, $inline, $add){
        
        $this->db->setSearch($value, $key, $operand, $inline, $add);
        
        return $this;
        
    }    
    
    public function setSelectTipo($value){
        
        $this->select_tipo = $value;
        
        return $this;
        
    }

    /*
     * PHP-DOC 
     * 
     * @name setNumeroCtps
     * 
     * @internal - Esse método faz a conversão do número do CTPS
     * 
     */   
    public function setNumeroCtps($value){
        
        $this->setCampo3($value);
        
        return $this;
        
    }
    
    public function getCodStatusContratacao() {

        return $this->rh_clt_ext['cod_status_contratacao'];

    }   
    
    /*
     * PHP-DOC 
     * 
     * @name setDiasRestanteContratoExperiencia
     * 
     * @internal - Método para armazenar na propriedade da classe os dias que restam para finalizar o contrato de experiência
     *             Caso o período já esteja finalizado esse valor será zerado
     * 
     */    
    public function getDiasRestanteContratoExperiencia(){
        
        return $this->rh_clt_ext['dias_restantes_contrato_experiencia'];
        
    }
    
    public function getAtivo(){
        
        return $this->rh_clt_ext['ativo'];
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getUmAno
     * 
     * @internal - Método para obter a informação armazenada que define se o Clt tem mais de um ano ou não de contratação
     * 
     */    
    public function getUmAno(){
        
        return $this->rh_clt_ext['um_ano'];
    }
    
    public function getStatusRealTime(){
        
        return $this->rh_clt_ext['status_real_time'];
        
    }

    public function getStatusDateFuture($value){
        
        $date = clone $this->date;

        return $date->set($this->rh_clt_ext['status_date_future'])->get($value);     
        
        
    }
    
    public function getNumeroCtps(){
        
        return $this->getCampo3();
        
    }
    
    public function getStatusContratacao(){
        
        return $this->status_contratacao[$this->getCodStatusContratacao()];

    }       

    public function getSearch(){
        
        return $this->db->getSearch();
        
    }    
    
    public function getSelectTipo(){
        
        return $this->select_tipo;
        
    }
    
    public function getTot(){
        
       return $this->db->getNumRows();
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name eRecisao
     * 
     * @internal - Método extendido da classe dinâmica que consulta a classe RhStatus para verificar se o status do Clt é rescisório
     * 
     */    
    public function chkRescisao(){
        
        try {

            /*
             * Verifica se a classe está instânciada
             */
            if(!is_object($this->getSuperClass()->Status)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            $this->setValue($this->getSuperClass()->Status->setDefault()->setCodigo($this->getStatus())->setTipo('recisao')->select()->getRow()->isOk());
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;
        
    } 
    
    /*
     * PHP-DOC 
     * 
     * @name getRowExt
     * 
     * @internal - Método extendido da classe dinâmica para carregar propriedades extendidaas do método select
     *             como um campo calculado.
     * 
     */    
    public function getRowExt(){
        
        $this->setCodStatusContratacao($this->db->getRow('cod_status_contratacao'));
        $this->setStatusRealTime($this->db->getRow('status_real_time'));
        $this->setDiasRestanteContratoExperiencia($this->db->getRow('dias_restantes_contrato_experiencia'));
        $this->setUmAno($this->db->getRow('um_ano'));
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setFieldCodStatusContratacao
     * 
     * @internal - Método que adiciona um campo calculado ao método select da classe
     *             Criar dois campos na consulta, um com a data da contratação do Clt e outro com o status dessa contratação
     * 
     */    
    private function setFieldCodStatusContratacao(){
        
        $this->db->setQuery(SELECT,",
                DATE_ADD(data_entrada, INTERVAL 90 DAY) AS data_contratacao, 
                CASE WHEN status = 200 THEN 3
                     WHEN data_entrada < DATE_SUB(CURDATE(), INTERVAL 90 DAY) THEN 2 
                     WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND data_entrada <= CURDATE() THEN 1 
                     ELSE 0 
                END AS cod_status_contratacao ",ADD);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setFieldDiasRestantesContratoExperiencia
     * 
     * @internal - Método que adiciona um campo calculado ao método select da classe
     *             Cálculo do art. 479 e 480 - Encontra o número de dias restantes para o término do primeiro ou segundo contrato
     * 
     */    
    public function setFieldDiasRestantesContratoExperiencia(){
        
        $data_demi = $this->getDataDemi('Y-m-d')=='1970-01-01' ? '' : $this->getDataDemi('Y-m-d');
        
        $this->db->setQuery(SELECT,",
                @data_demi = '{$data_demi}',
                @data_fim_pri_periodo:=DATE_ADD(data_entrada, INTERVAL + (SELECT primeiro_periodo FROM prazo_experiencia WHERE id_prazo = prazoexp) DAY),
                @data_fim_seg_periodo:=DATE_ADD(@data_fim_pri_periodo, INTERVAL + (SELECT segundo_periodo FROM prazo_experiencia WHERE id_prazo = prazoexp) DAY),
                id_clt,
                prazoexp,
                data_entrada,
                CASE WHEN @data_demi > @data_fim_seg_periodo  THEN 0
                    WHEN @data_demi <= @data_fim_pri_periodo THEN DATEDIFF(@data_fim_pri_periodo,@data_demi)
                    WHEN @data_demi <= @data_fim_seg_periodo THEN DATEDIFF(@data_fim_seg_periodo,@data_demi)
                END
                AS dias_restantes_contrato_experiencia ",ADD);
            
        return $this;    
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setFieldStatusRealTime
     * 
     * @internal - Método que adiciona um campo calculado ao método select da classe
     *             Caso haja parámetro na propriedade StatusDateFuture da classe, a consulta irá verificar o status do Clt nesse período
     * 
     */    
    public function setFieldStatusRealTime(){
        
        $status_date_future = empty($this->getStatusDateFuture()) ? "CURDATE()" : "'{$this->getStatusDateFuture()}'";

        $this->db->setQuery(SELECT,",
                @status_rescisao:=(SELECT motivo FROM rh_recisao r WHERE r.status AND r.id_clt = a.id_clt AND !a.reintegracao LIMIT 1),
                @status_eventos:=(SELECT cod_status FROM rh_eventos e WHERE e.status AND e.id_clt = a.id_clt AND ({$status_date_future} BETWEEN e.data AND e.data_retorno OR e.data_retorno='0000-00-00') LIMIT 1),
                @status_ferias:=(SELECT 40 status_real FROM rh_ferias f WHERE f.status AND f.id_clt = a.id_clt AND {$status_date_future} BETWEEN f.data_ini AND f.data_fim LIMIT 1),
                CASE WHEN @status_rescisao THEN @status_rescisao
                     WHEN @status_eventos THEN @status_eventos
                     WHEN @status_ferias THEN @status_ferias
                     ELSE 10
                END status_real_time ",ADD);
            
        return $this;    
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setFieldTemMaisDeUmAno
     * 
     * @internal - Método que adiciona um campo calculado ao método select da classe
     *             Verifica se o Clt já tem mais de um ano de casa
     * 
     */    
    public function setFieldMesesTrabalhadoUmAno(){
        
        $data_demi = $this->getDataDemi('Y-m-d')=='1970-01-01' ? '' : $this->getDataDemi('Y-m-d');
        
        $this->db->setQuery(SELECT,",
                            @data_ini := data_entrada entrada,
                            @data_demi := '{$data_demi}' saida,
                            @data_fim := IF(LENGTH(@data_demi),@data_demi,CURDATE()) data_fim,
                            @data_ini_periodo := CONCAT(YEAR(@data_ini),LPAD(MONTH(@data_ini),2,'00')) data_ini_periodo,
                            @data_fim_periodo :=  CONCAT(YEAR(@data_fim),LPAD(MONTH(@data_fim),2,'00')) data_fim_periodo,
                            @meses_de_trabalho := PERIOD_DIFF(@data_fim_periodo,@data_ini_periodo) meses_trabalhado,
                            IF(DATEDIFF(@data_fim,@data_ini) >= 365, 1, 0) as um_ano                                 
                            ",ADD);
            
        return $this;    
        
    }

    /*
     * PHP-DOC 
     * 
     * @name setField
     * 
     * @internal - Método que adiciona um campo calculado ao método select da classe
     *             Verifica se o Clt já recebeu décimo terceiro do ano
     * 
     */    
    public function setFieldJaRecebeuDecimoTerceiro(){
        
        $this->db->setQuery(SELECT,",
                            (
                            SELECT COUNT(fp.id_clt) 
                            FROM rh_folha f INNER JOIN rh_folha_proc fp ON f.id_folha = fp.id_folha 
                            WHERE fp.id_clt = id_clt AND fp.ano = YEAR(@data_demi) AND fp.status = 3 AND f.terceiro = 1
                            ) AS ja_recebeu_decimo_terceiro
                            ",ADD);
            
        return $this;    
        
    }

    /*
     * PHP-DOC 
     * 
     * @name getEstabilidadeProvisoria()
     * 
     * @internal - Método para verificar se o Clt encontra-se em estabilidade provisória
     * 
     */    
    public function getEstabilidadeProvisoria(){
    
    
        return 0;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getDiasEventosNaoPagosMes()
     * 
     * @internal - Método para obter o total de dias em eventos não pagos no mês
     * 
     */    
    public function getDiasEventosNaoPagosMes(){
        
        try {
            
            /*
             * Verifica se é uma rescisão e o Clt encontra-se em evento
             */
            $ultimo_evento = $this->getSuperClass()->Eventos->setDefault()->setMagneticKey(0)->setIdRegiao($this->getIdRegiao())->setIdProjeto($this->getIdProjeto())->setIdClt($this->getIdClt())->setDateRangeField("DATE_FORMAT(data,'%Y-%m')")->setDateRangeFmt("Y-m")->setDateRangeIni($this->getDateRangeIni())->setDateRangeEnd($this->getDateRangeEnd())->getUltimoEvento();

            /*
             * Verifica se é uma rescisão e o Clt encontra-se em evento
             */
            if($this->chkRescisao()->isOk() && ($ultimo_evento['data_fim']->val() > $this->getDataSaida()->val() || $ultimo_evento['data_fim']->val()==0)) $this->error->set('O funcionário ainda encontra-se em evento, portanto não é permitida sua rescisão',E_FRAMEWORK_ERROR);
        
            /*
             * Caso possua algum evento no período
             */
            if($ultimo_evento['dias'] > $dias_pela_empresa ) {
                
                /*
                 * Caso ano e mes inicial e final do último evento sejam o mesmo da data de referência então e uma entrada e saida de evento dentro do mesmo mês
                 */
                if($ultimo_evento['data_ini']->get('Y-m')->val()==$this->getDateRangeIni('Y-m')->val() && $ultimo_evento['data_fim']->get('Y-m')->val()==$this->getDateRangeIni('Y-m')->val()){
                    
                    /*
                     * Abate-se os dias que são pelo INSS (Previdência) ao número total de dias no mês
                     */
                    $dias_nao_pagos = $ultimo_evento['dias'] - $dias_pela_empresa;

                }else {

                    /*
                     * Caso ano e mes do início do último evento sejam o mesmo da data de referência então é uma entrada de evento
                     */
                    if($ultimo_evento['data_ini']->get('Y-m')->val()==$this->getDateRangeIni('Y-m')->val()) {
                        
                        /*
                         * Se o número de dias em evento for maior que os dias pela empresa, então 
                         */
                        $dias_nao_pagos = $dias_trabalhado_mes - ($ultimo_evento['data_ini']->minusDays(1)->get('d')->val() + $dias_pela_empresa);

                    }   
                    else {

                        /*
                         * Caso ano e mes do final do último evento sejam o mesmo da data de referência então e uma saída de evento
                         */
                        if($ultimo_evento['data_fim']->get('Y-m')->val()==$this->getDateRangeIni('Y-m')->val()){


                        }
                        
                    }    

                }
                
            }
            
           if($this->getStatus() == $this->getSuperClass()->getKeyMaster(1)){

                $dias_trabalhado_mes -= $faltas;

            }else{

            }              
            
            $this->setValue(1);
            
                    
        } catch (Exception $ex) {
            
            $dias_trabalhado_mes = 0;

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        $dias_eventos_nao_pagos_mes = 0;

        return $dias_eventos_nao_pagos_mes;
        
    }    
    
    /*
     * PHP-DOC 
     * 
     * @name getDiasTrabalhadoMes()
     * 
     * @internal - Método para obter o número total de dias de trabalho de referência no mês
     * 
     */    
    public function getDiasTrabalhadoMes(){
        
        try {
            
            $dias_trabalhado_mes = 0;

            $dias_no_mes = 30;
            
            $dias_pela_empresa = 15;
            
            /*
             * Verifica se as classes estão instanciadas
             */
            
            if(!is_object($this->getSuperClass()->Folha)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(!is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(!is_object($this->getSuperClass()->Eventos)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if($this->chkRescisao()->isOk()){
            
                /*
                 * Caso rescisão seja no dia 29 de fevereiro ou seja o dia 31 de qualquer mês então 
                 * $dias_trabalhado_mes igual a 30, senão é o número do dia da demissão
                 */
                $dias_trabalhado_mes = (($this->date->now()->get('m')->val()==2 && $this->getDataSaida('d')->val() > 28) || $this->getDataSaida('d')->val() == 31) ? 30 : $this->getDataSaida('d')->val();
                
                $this->setDateRangeIni($this->getDataSaida())->setDateRangeEnd($this->getDataSaida());
                
            }  
            else {

                /*
                 * Se os meses da entrada e da data de referência forem iguais então é o mês de admissão
                 */
                if($this->getDataEntrada('m')->val()==$this->getDateRangeIni('m')){

                    if($this->getDataEntrada('d')->val()==30 || $this->getDataEntrada('d')->val()==31){

                        $dias_trabalhado_mes = 1;

                    }
                    else {

                        $dias_trabalhado_mes = $dias_no_mes - $this->getDataEntrada('d')->val();

                    }

                }
                /*
                 * Senão é um mês de atividade normal
                 */
                else {
                    
                    $dias_trabalhado_mes = $dias_no_mes;
                    
                }

            }    
            
            /*
             * Verifica as faltas no mês para abater nos dias trabalhados no mês
             */
            $this->getSuperClass()->Folha->setDefault();
            $this->getSuperClass()->Folha->setTerceiro(2);
            $this->getSuperClass()->Folha->setDateRangeField("CONCAT(f.ano,LPAD(f.mes,2,'00'))");
            $this->getSuperClass()->Folha->setDateRangeFmt('Ym');
            $this->getSuperClass()->Folha->setDateRangeIni($this->getDateRangeIni());
            $this->getSuperClass()->Folha->setDateRangeEnd($this->getDateRangeEnd());
            
            $faltas = $this->getSuperClass()->Folha->getCalcFaltasNoPeriodo();
            
            $dias_eventos_nao_pagos_mes = $this->getDiasEventosNaoPagosMes();

            $dias_trabalhado_mes -= $faltas + $dias_eventos_nao_pagos_mes;
           
            $this->setValue(1);
                    
        } catch (Exception $ex) {
            
            $dias_trabalhado_mes = 0;

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $dias_trabalhado_mes;
            
            
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcDiasSaldoSalario()
     * 
     * @internal - Método para calcular e retornar os dias trabalhados menos dos dias de faltas e de eventos em que o Clt esteve
     *             Dias trabalhados para efeito de cálculo de saldo de salário
     *             $dias_trabalhados -= ($faltas + $dias_de_eventos)
     * 
     *             Obs: Qualquer evento exceto licença maternidade abate nos dias de saldo de salário
     *                  Ou seja, todo evento em que o Clt esteja pelo INSS.
     * 
     *             Para efeito de folha:
     *             Os primeiros quinze dias de evento mais os dias trabalhados
     *             Voltando de evento não conta os dias do evento.
     * 
     *             Excessão licença maternidade.
     * 
     */    
    public function getCalcDiasSaldoSalario(){
        
        try {

            $this->setValue(1);
            
            return $this->getDiasTrabalhadoMes();
            
                    
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return 0;
        
    }   
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcTotInsalubridade()
     * 
     * @internal - Método para calcular e retornar o valor da Insalubridade do Clt
     * 
     */    
    public function getCalcTotInsalubridade(){
        
        try {
            
            $valor = 0;

            if(!is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(!$cod_movimento = $this->getSuperClass()->getKeyMaster(3)) $this->error->set(array(8,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->setTipoMovimento('CREDITO');

            /*
             * Pega todos os movimentos que não foram excluídos
             */
            $this->setStatusReg(1);

            /*
             * Pega todos os movimento finalizados que recebem o status 5 que indica que foram efetivados
             */
            $this->setStatus(5);

            $id_regiao = $this->getIdRegiao();
            $id_projeto = $this->getIdProjeto();
            $id_clt = $this->getIdClt();
            $status = $this->getStatus();
            $status_reg = $this->getStatusReg();

            /*
             * Pegar apenas os valores maiores que zero para evitar pegar lixo e os ids de movimentos da folha
             */

            $this->db->setQuery(WHERE," cod_movimento IN ($cod_movimento) AND valor_movimento > 0 AND mes_mov >= 1 AND mes_mov <= 12",ADD);

            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}

            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}

            if(!empty($status_reg)) {$this->db->setQuery(WHERE,"AND status_reg = {$status_reg}",ADD);}

            if(!empty($status)) {$this->db->setQuery(WHERE,"AND status = {$status}",ADD);}

            $this->db->setQuery(ORDER,'ano_mov DESC,mes_mov DESC');

            $this->db->setQuery(LIMIT,'1');
            
            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);

            $this->db->setRow();

            /*
             * Verificação de inconsistência nos registros:
             * 
             * Executa uma verificação se existe alguma definição de Insalubridade no Clt ou Periculosidade em Cursos
             * e não existe movimento definido para o Clt ou vive-versa.
             */

            if(($this->getSuperClass()->Clt->getInsalubridade() || $this->getSuperClass()->Curso->getPericulosidade30()) &&  empty($this->db->getRow('cod_movimento'))){

                $this->error->set('Erro na verificação de Consistência: Existe insalubridade/periculosidade definida para o Clt ou Curso mas não existe movimento',E_FRAMEWORK_NOTICE);            


            }elseif((!$this->getSuperClass()->Clt->getInsalubridade() && !$this->getSuperClass()->Curso->getPericulosidade30()) &&  $this->db->getRow('cod_movimento')){

                $this->error->set('Erro na verificação de Consistência: Existe insalubridade/periculosidade lançada em movimento para o Clt mas não existe referência a ele definida no cadastro ou função',E_FRAMEWORK_NOTICE);  

            }

            $this->getSuperClass()->Curso->getPericulosidade30();

            $cod_movimento_array = explode(',',$cod_movimento);

            /*
             * Se insalubridade 20% atribui valor fixo de 176,00
             */
            if($this->db->getRow('cod_movimento')==$cod_movimento_array[0]){

                $valor = 880.00*0.20; //$this->db->getRow('valor_movimento'); 

            }
            /*
             * Se insalubridade 40% atribui valor fixo de 352,00
             */
            elseif($this->db->getRow('cod_movimento')==$cod_movimento_array[1]){

                $valor = 880.00*0.40; //$this->db->getRow('valor_movimento'); 

            }    
            /*
             * Se Periculosidade atribui valor do movimento
             */
            elseif($this->db->getRow('cod_movimento')==$cod_movimento_array[2]){

                $valor = $this->db->getRow('valor_movimento');

            }    
            /*
             * Caso contrário seta valor zerado
             */
            else {

                $valor = 0;

            }

        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return  $valor;
        
    }
    
    /*
     * PHP-DOC - Faz o levantamento de faltas no período aquisitivo 
     * 
     * Obs: tipo_qnt pode ser 1 = horas e 2 = dias
     */
    public function getCalcFaltasNoPeriodo(){
        
        try {
            
            if(!is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            /*
             * define todas as chaves de movimento relativo a faltas
             */
            $this->getSuperClass()->MovimentosClt->setCodMovimento($this->getSuperClass()->getKeyMaster(1));

            /*
             * Pega todos os movimentos que não foram excluídos
             */
            $this->getSuperClass()->MovimentosClt->setStatusReg(1); 

            /*
             * Pega todos os movimento finalizados que recebem o status 5
             */
            $this->getSuperClass()->MovimentosClt->setStatus(5);        

            /*
             * Pega apenas os registros de faltas em dias = 2
             */
            $this->getSuperClass()->MovimentosClt->setTipoQnt(2);

            $this->getSuperClass()->MovimentosClt->setIdRegiao($this->getIdRegiao());
            $this->getSuperClass()->MovimentosClt->setIdProjeto($this->getIdProjeto());
            $this->getSuperClass()->MovimentosClt->setIdClt($this->getIdClt());
            $this->getSuperClass()->MovimentosClt->setDateRangeField("CONCAT(ano_mov,LPAD(mes_mov,2,'00'))");
            $this->getSuperClass()->MovimentosClt->setDateRangeFmt('Ym');
            $this->getSuperClass()->MovimentosClt->setDateRangeIni($this->getDateRangeIni());
            $this->getSuperClass()->MovimentosClt->setDateRangeEnd($this->getDateRangeEnd());
            
//            exit($this->getSuperClass()->MovimentosClt->select("IF(ISNULL(SUM(qnt)),0,SUM(qnt)) AS total")->db->getLastQuery());
            
            if($this->getSuperClass()->MovimentosClt->select("IF(ISNULL(SUM(qnt)),0,SUM(qnt)) AS total")->getRow()->isOk()){
                
                return $this->getSuperClass()->MovimentosClt->db->getRow('total');

            }
            else {

                $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);
                
            }    
            
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return 0;
        
        
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name selectExt
     * 
     * @internal - Método que adiciona as condições extendidas de um select de classe
     * 
     */    
    public function selectExt(){
        
        try {

            /*
             * Adição de campos extra a cláusula select
             */
            $this->setFieldCodStatusContratacao();
            $this->setFieldDiasRestantesContratoExperiencia();
            $this->setFieldStatusRealTime();
            $this->setFieldMesesTrabalhadoUmAno();
            $this->setFieldJaRecebeuDecimoTerceiro();

            /*
             * Condição específica para busca por nome do clt
             */
            if(!empty($this->getNome())) {$this->db->setQuery(WHERE,"AND nome LIKE '%{$this->getNome()}%' ",ADD);}

            /*
             * Condição para listar apenas os Clts que estão ativos no sistema, ou seja, não possuem rescisão
             * Entretanto foi constatado que será necessário fazer uma crítica com relação ao status real e do registro
             */
            if(!empty($this->getAtivo())) {$this->db->setQuery(WHERE,"AND id_clt NOT IN (SELECT id_clt FROM rh_recisao WHERE status) ",ADD);}

            $this->db->setQuery(ORDER, " id_regiao, id_projeto, nome ASC ");
            
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        
        return $this;
        
    }
   
    /*
     * PHP-DOC 
     * 
     * @name updateExt
     * 
     * @internal - Método que adiciona as condições extendidas de atualização dessa classe
     * 
     */    
    public function updateExt(){
        
        try {

            $id_clt = $this->getIdClt();

            $this->db->setQuery(WHERE, " id_clt = {$id_clt} ");
        
            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;
        
       
    }
    
    

}





