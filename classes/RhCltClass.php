<?php
/*
 * PHO-DOC - RhCltClass.php
 * 
 * Classe de manipula��o dos registros de clt 
 * 
 * 01-10-2015
 *
 * @name RhCltClass 
 * @package RhCltClass 
 * @access public 
 *  
 * @version 
 *
 * Vers�o: 3.0.5055 - 01/10/2015 - Jacques - Vers�o Inicial 
 * Vers�o: 3.0.7221 - 02/03/2016 - Jacques - Adicionado o m�todo GetFields para uso no m�todo select 
 * Vers�o: 3.0.7475 - 08/03/2016 - Jacques - Adicionado o m�todo getRowExt para inclus�o de propriedades extendidas da classe e o campo status_real_time 
 *                                           que gera uma consulta com o status real dos clts. 
 * Vers�o: 3.0.7769 - 10/03/2016 - Jacques - Atualiza��o da classe para inst�nciamento din�mico
 * Vers�o: 3.0.7769 - 10/03/2016 - Jacques - Adicionado o evento onUpdate para execu��o de processo na atualiza��o da classe.
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
                                        'Aguardando Contrata��o',
                                        'Per�odo de Experi�ncia',
                                        'Atividade Normal',
                                        'Aguardando Demiss�o'
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
     * @internal - Qualquer inclus�o, altera��o ou exclus�o de registros relacionados ao Clt dever�o gerar esse
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
     * @internal - M�todo para armazenar na propriedade da classe os dias que restam para finalizar o contrato de experi�ncia
     *             Caso o per�odo j� esteja finalizado esse valor ser� zerado
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
     * @internal - Essa propriedade define uma condi��o para consultar apenas os Clts que est�o ativos no sistema, ou seja, podem estar em evento
     *             mas n�o demitidos.
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
     * @internal - Essa propriedade define se o clt possui mais de um ano de tempo de servi�o
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
     * @internal - Essa propriedade define o status do Clt em um determinado per�odo de dada definida na propriedade setStatusDateFuture
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
     * @internal - Esse m�todo faz a convers�o do n�mero do CTPS
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
     * @internal - M�todo para armazenar na propriedade da classe os dias que restam para finalizar o contrato de experi�ncia
     *             Caso o per�odo j� esteja finalizado esse valor ser� zerado
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
     * @internal - M�todo para obter a informa��o armazenada que define se o Clt tem mais de um ano ou n�o de contrata��o
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
     * @internal - M�todo extendido da classe din�mica que consulta a classe RhStatus para verificar se o status do Clt � rescis�rio
     * 
     */    
    public function chkRescisao(){
        
        try {

            /*
             * Verifica se a classe est� inst�nciada
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
     * @internal - M�todo extendido da classe din�mica para carregar propriedades extendidaas do m�todo select
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
     * @internal - M�todo que adiciona um campo calculado ao m�todo select da classe
     *             Criar dois campos na consulta, um com a data da contrata��o do Clt e outro com o status dessa contrata��o
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
     * @internal - M�todo que adiciona um campo calculado ao m�todo select da classe
     *             C�lculo do art. 479 e 480 - Encontra o n�mero de dias restantes para o t�rmino do primeiro ou segundo contrato
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
     * @internal - M�todo que adiciona um campo calculado ao m�todo select da classe
     *             Caso haja par�metro na propriedade StatusDateFuture da classe, a consulta ir� verificar o status do Clt nesse per�odo
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
     * @internal - M�todo que adiciona um campo calculado ao m�todo select da classe
     *             Verifica se o Clt j� tem mais de um ano de casa
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
     * @internal - M�todo que adiciona um campo calculado ao m�todo select da classe
     *             Verifica se o Clt j� recebeu d�cimo terceiro do ano
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
     * @internal - M�todo para verificar se o Clt encontra-se em estabilidade provis�ria
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
     * @internal - M�todo para obter o total de dias em eventos n�o pagos no m�s
     * 
     */    
    public function getDiasEventosNaoPagosMes(){
        
        try {
            
            /*
             * Verifica se � uma rescis�o e o Clt encontra-se em evento
             */
            $ultimo_evento = $this->getSuperClass()->Eventos->setDefault()->setMagneticKey(0)->setIdRegiao($this->getIdRegiao())->setIdProjeto($this->getIdProjeto())->setIdClt($this->getIdClt())->setDateRangeField("DATE_FORMAT(data,'%Y-%m')")->setDateRangeFmt("Y-m")->setDateRangeIni($this->getDateRangeIni())->setDateRangeEnd($this->getDateRangeEnd())->getUltimoEvento();

            /*
             * Verifica se � uma rescis�o e o Clt encontra-se em evento
             */
            if($this->chkRescisao()->isOk() && ($ultimo_evento['data_fim']->val() > $this->getDataSaida()->val() || $ultimo_evento['data_fim']->val()==0)) $this->error->set('O funcion�rio ainda encontra-se em evento, portanto n�o � permitida sua rescis�o',E_FRAMEWORK_ERROR);
        
            /*
             * Caso possua algum evento no per�odo
             */
            if($ultimo_evento['dias'] > $dias_pela_empresa ) {
                
                /*
                 * Caso ano e mes inicial e final do �ltimo evento sejam o mesmo da data de refer�ncia ent�o e uma entrada e saida de evento dentro do mesmo m�s
                 */
                if($ultimo_evento['data_ini']->get('Y-m')->val()==$this->getDateRangeIni('Y-m')->val() && $ultimo_evento['data_fim']->get('Y-m')->val()==$this->getDateRangeIni('Y-m')->val()){
                    
                    /*
                     * Abate-se os dias que s�o pelo INSS (Previd�ncia) ao n�mero total de dias no m�s
                     */
                    $dias_nao_pagos = $ultimo_evento['dias'] - $dias_pela_empresa;

                }else {

                    /*
                     * Caso ano e mes do in�cio do �ltimo evento sejam o mesmo da data de refer�ncia ent�o � uma entrada de evento
                     */
                    if($ultimo_evento['data_ini']->get('Y-m')->val()==$this->getDateRangeIni('Y-m')->val()) {
                        
                        /*
                         * Se o n�mero de dias em evento for maior que os dias pela empresa, ent�o 
                         */
                        $dias_nao_pagos = $dias_trabalhado_mes - ($ultimo_evento['data_ini']->minusDays(1)->get('d')->val() + $dias_pela_empresa);

                    }   
                    else {

                        /*
                         * Caso ano e mes do final do �ltimo evento sejam o mesmo da data de refer�ncia ent�o e uma sa�da de evento
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
     * @internal - M�todo para obter o n�mero total de dias de trabalho de refer�ncia no m�s
     * 
     */    
    public function getDiasTrabalhadoMes(){
        
        try {
            
            $dias_trabalhado_mes = 0;

            $dias_no_mes = 30;
            
            $dias_pela_empresa = 15;
            
            /*
             * Verifica se as classes est�o instanciadas
             */
            
            if(!is_object($this->getSuperClass()->Folha)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(!is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(!is_object($this->getSuperClass()->Eventos)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if($this->chkRescisao()->isOk()){
            
                /*
                 * Caso rescis�o seja no dia 29 de fevereiro ou seja o dia 31 de qualquer m�s ent�o 
                 * $dias_trabalhado_mes igual a 30, sen�o � o n�mero do dia da demiss�o
                 */
                $dias_trabalhado_mes = (($this->date->now()->get('m')->val()==2 && $this->getDataSaida('d')->val() > 28) || $this->getDataSaida('d')->val() == 31) ? 30 : $this->getDataSaida('d')->val();
                
                $this->setDateRangeIni($this->getDataSaida())->setDateRangeEnd($this->getDataSaida());
                
            }  
            else {

                /*
                 * Se os meses da entrada e da data de refer�ncia forem iguais ent�o � o m�s de admiss�o
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
                 * Sen�o � um m�s de atividade normal
                 */
                else {
                    
                    $dias_trabalhado_mes = $dias_no_mes;
                    
                }

            }    
            
            /*
             * Verifica as faltas no m�s para abater nos dias trabalhados no m�s
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
     * @internal - M�todo para calcular e retornar os dias trabalhados menos dos dias de faltas e de eventos em que o Clt esteve
     *             Dias trabalhados para efeito de c�lculo de saldo de sal�rio
     *             $dias_trabalhados -= ($faltas + $dias_de_eventos)
     * 
     *             Obs: Qualquer evento exceto licen�a maternidade abate nos dias de saldo de sal�rio
     *                  Ou seja, todo evento em que o Clt esteja pelo INSS.
     * 
     *             Para efeito de folha:
     *             Os primeiros quinze dias de evento mais os dias trabalhados
     *             Voltando de evento n�o conta os dias do evento.
     * 
     *             Excess�o licen�a maternidade.
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
     * @internal - M�todo para calcular e retornar o valor da Insalubridade do Clt
     * 
     */    
    public function getCalcTotInsalubridade(){
        
        try {
            
            $valor = 0;

            if(!is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
            
            if(!$cod_movimento = $this->getSuperClass()->getKeyMaster(3)) $this->error->set(array(8,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->setTipoMovimento('CREDITO');

            /*
             * Pega todos os movimentos que n�o foram exclu�dos
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
             * Verifica��o de inconsist�ncia nos registros:
             * 
             * Executa uma verifica��o se existe alguma defini��o de Insalubridade no Clt ou Periculosidade em Cursos
             * e n�o existe movimento definido para o Clt ou vive-versa.
             */

            if(($this->getSuperClass()->Clt->getInsalubridade() || $this->getSuperClass()->Curso->getPericulosidade30()) &&  empty($this->db->getRow('cod_movimento'))){

                $this->error->set('Erro na verifica��o de Consist�ncia: Existe insalubridade/periculosidade definida para o Clt ou Curso mas n�o existe movimento',E_FRAMEWORK_NOTICE);            


            }elseif((!$this->getSuperClass()->Clt->getInsalubridade() && !$this->getSuperClass()->Curso->getPericulosidade30()) &&  $this->db->getRow('cod_movimento')){

                $this->error->set('Erro na verifica��o de Consist�ncia: Existe insalubridade/periculosidade lan�ada em movimento para o Clt mas n�o existe refer�ncia a ele definida no cadastro ou fun��o',E_FRAMEWORK_NOTICE);  

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
             * Caso contr�rio seta valor zerado
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
     * PHP-DOC - Faz o levantamento de faltas no per�odo aquisitivo 
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
             * Pega todos os movimentos que n�o foram exclu�dos
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
     * @internal - M�todo que adiciona as condi��es extendidas de um select de classe
     * 
     */    
    public function selectExt(){
        
        try {

            /*
             * Adi��o de campos extra a cl�usula select
             */
            $this->setFieldCodStatusContratacao();
            $this->setFieldDiasRestantesContratoExperiencia();
            $this->setFieldStatusRealTime();
            $this->setFieldMesesTrabalhadoUmAno();
            $this->setFieldJaRecebeuDecimoTerceiro();

            /*
             * Condi��o espec�fica para busca por nome do clt
             */
            if(!empty($this->getNome())) {$this->db->setQuery(WHERE,"AND nome LIKE '%{$this->getNome()}%' ",ADD);}

            /*
             * Condi��o para listar apenas os Clts que est�o ativos no sistema, ou seja, n�o possuem rescis�o
             * Entretanto foi constatado que ser� necess�rio fazer uma cr�tica com rela��o ao status real e do registro
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
     * @internal - M�todo que adiciona as condi��es extendidas de atualiza��o dessa classe
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





