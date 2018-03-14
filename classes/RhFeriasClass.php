<?php 
/*
 * PHP-DOC - RhFeriasClass.php 
 * 
 * Classe para cria��o de camada de compatibilidade retroativa na operacionaliza��o das f�rias 
 *
 * 10-09-2015
 * 
 * @package RhFeriasClass  
 * @access public   
 * 
 * @version
 * 
 * Vers�o: 3.0.4385 - 24/11/2015 - Jacques - Vers�o Inicial
 * Vers�o: 3.0.4823 - 10/09/2015 - Jacques - Adicionado o calculo do desconto parcial do INSS pois estava s� estava pelo total $this->setInssValor($teto_desconto_inss - $this->getSuperClass()->Clt->getValorDescontoInss());
 * Vers�o: 3.0.4902 - 14/12/2015 - Jacques - Retirado a condi��o de sele��o por projetos no m�todo getPeriodosGozados pois pode haver caso de transfer�ncia de projetos ent�o o clt mantem o per�odo aquisitivo
 * Vers�o: 3.0.5483 - 12/12/2016 - Jacques - Bug na opera��o com o vetor $periodoAquivisitoPendente[$key]['data_aquisitivo_ini'] onde o mesmo setava equivocadamente a classe setDataAquisitivoIni gerando erro quando havia mais de um per�odo aquisitivo
 * Vers�o: 3.0.5614 - 18/01/2016 - Jacques - Conforme reuni�o no mesmo dia dessa anota��o com Gimenez e Leonardo o valor base para fins de f�rias n�o dever� mais somar o ter�o constitucional
 *                                           Tamb�m n�o dever� mais ser discriminado o valor do dia no per�odo de gozo, assim como a diferen�a do segundo m�s do mesmo,
 *                                           dever� ser calculada pela diferen�a do primeiro e n�o mais proporcional a valor dia a fim de avitar diferen�a nos arredondamentos.
 *                                           Obs.: A nova forma de definir a divis�o dos valores gerou uma diferen�a de R$4,80 para para o id_ferias 5183. Ou seja
 *                                           O que antes era 0,16 centavos agora s�o 3,80.
 *                                           Os c�digos 5,6 respectivamente de total de remunera��es na montagem de rh_ferias_itens est�va se repetindo nas chaves de acr�scimo constitucional
 *                                           em virtude da tabela de legendas perdias e recadastras na lagos
 * Vers�o: 3.0.5883 - 26/01/2016 - Jacques - Adicionei a condi��o de verifica��o de exist�ncia de dias no segundo m�s para evitar levar lixo para os registros em quest�o
 *                                           Adicionado verifica��o de dias de f�rias do segundo m�s e valor dos campos para inser��o dos registros
 * Vers�o: 3.0.5883 - 01/02/2016 - Jacques - Adicionado o armazenamento e processamento de valor por dependente atrav�z do m�todo setIrrfValorDeducaoDependente e setParcelaDeducaoIrrf. Corrigido tamb�m o valor passado para o m�todo $calculos->MostraIRRF que recebia o id da regi�o ao invez do projeto
 * Vers�o: 3.0.8998 - 20/04/2016 - Jacques - Adicionada op��o de ignorar f�rias dobradas e a op��o de retorno de evento n�o geralas quando as f�rias come�ar imediatamente um dia ap�s do evento
 * Vers�o: 3.0.9071 - 29/04/2016 - Jacques - Adicionada op��o de lan�amentos de m�dias fixas no sal�rio vari�vel como tamb�m o processamento de itens de movimento
 * 
 * Adicionei a condi��o de verifica��o de exist�ncia de dias no segundo m�s para evitar levar lixo para os registros em quest�o
 * 
 * @todo 
 * 
 * OBS: em 02/02/2016 Sin�sio me pediu para fazer uma verifica��o em todas as rotinas que usam a classe calculos pois n�o estava sendo computado os 
 *      dependentes na rotinas de folha, f�rias e rescis�o. Esse erro � cr�tico j� que a �nica classe que � usada pela nova rotina de f�rias vem da
 *      classe calculos que est� no arquivo classes\calculos.php
 * 
 * ATEN��O: 1. Incluir o processamento favorecido_pensao_assoc para computar pens�o no calculo e processamento das f�rias
 *          2. Na hora de listar o per�odo aquisitivo dever� ser verificado quantos dias de eventos teve no per�odo
 * 
 * Instru��es de Ramon:
 *          3. ok - Possibilidade de op��o de n�o lan�ar faltas quando houver.
 *          4. ok - Fixar Title resumo de per�odo em duas linhas e duas colunas e tirar de dentro do panel
 *          5. ok - Aumentar largura do modal.
 *          6. ok - Destaca onde come�a os c�lculos separando o que � soma, subtra��o de forma bem vis�vel
 *          7. ok - Lupa para m�dia de sal�rio vari�vel ao lado do R$.
 *          8. ok - Espa�ar valor e moeda.
 *          9. ok - Lan�ar os valores em ambas as tabelas (rh_ferias) e (rh_ferias_itens)
 *         10. ok Adi��o da revis�o no footer
 * 
 * Sugest�es de Sin�sio:
 *         11. ok - Adicionar na listagem de sal�rio vari�vel um agrupamento por mes e tipo de remunera��o com totalizador geral
 *         12. ok - Adicionar o n�mero de dependentes ou n�o para o calculo do IR
 *         13. ok - Definir procedimento final para calculo do valor de insalubridade e periculosidade
 *         13. Ao verificar o status do Clt e na tabela de eventos, detectar inconsist�ncia, ent�o atualizar o status do Clt
 * 
 * 
 *         14. Fazer a contabiliza��o das faltas incluindo as horas trabalhadas baseadas na fun��o 
 *             (No caso de m�dico soma as horas de faltas e divide por 36 e arredonda para baixo).
 *         
 * Minhas pend�ncias:
 *         1.  ok - Necess�rio criar um m�todo para verificar o status do clt e se o mesmo encontra-se em evento no sistema, fazendo uma consist�ncia de 
 *             ambas informa��es.
 *         2.  ok - Quando o funcion�rio estiver em evento e seu retorno ultrapassar o per�odo concessivo, dever� existir uma flag para ignorar as f�rias 
 *             em dobro com log para informar essa opera��o. 
 *         3.  Implementar limpeza de buffer para evitar erro no uso de print em debug para uso de AJAX com JSON.
 *         4.  Implementar a possibilidade de levar em considera��o funcion�rios que sofreram transfer�ncias de projetos em 'SELECT * FROM rh_transferencias WHERE id_clt = @clt' para uso do m�todo getPeriodosGozados
 *             
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 *  
 */


class RhFeriasClass {
    
    private     $calc_array = array(
                        'inss' => array('base' => 0,
                                        'valor' => 0,
                                        'percent' => 0
                                        ),
                        'fgts' => array(
                                        'base' => 0,
                                        'valor' => 0,
                                        'percent' => 0
                                        ),
                        'irrf' => array('base' => 0,
                                        'valor' => 0,
                                        'percent' => 0,
                                        'qnt_dependentes' => 0,
                                        'valor_deducao_dependente' => 0,
                                        'parcela_deducao' => 0
                                        ),
                        'movimentos' => array('total' => array(
                                                              'credito' => 0,
                                                              'debito' => 0
                                                              )
                                              ),
                        'salario_extra' => 0,
                        'periodo_abono_ini' => '',
                        'periodo_abono_fim' => '',
                        'remuneracao_base_dia' => 0,
                        'remuneracao_base_dia_ferias_dobradas' => 0,
                        'base_ferias_dobradas' => 0,
                        'um_terco_ferias_dobradas' => 0,
                        'total_ferias_dobradas' => 0,
                        'valor_dias_ferias_dobradas' => 0,
                        'ignorar_faltas' => 0,
                        'ignorar_ferias_dobradas' => 0,
                        'metade_ferias' => 0,
                        'quantidade_dias' => 30,
                        'dias1' => 0,
                        'dias2' => 0,
                        'adicional' => array('insalubridade' => 157.60,
                                              'periculosidade' => 453.23,
                                              'total' => 0),
                        'total_liquido_1' => 0,
                        'total_liquido_2' => 0
                        );
    
    private $rh_movimentos_clt;     
    
    private $movimentos_total = array(
                                'credito' => 0,
                                'debito'  => 0
                                );
    

    private function setSalarioExtra($value){
    
        $this->calc_array['salario_extra'] = $value;
        
        return $this;
        
    }
    
    private function setRemuneracaoBaseDia($value){
    
        $this->calc_array['remuneracao_base_dia'] = $value;
        
        return $this;
        
    }
    
    private function setRemuneracaoBaseDiaFeriasDobradas($value){
    
        $this->calc_array['remuneracao_base_dia_ferias_dobradas'] = $value;
        
        return $this;
        
    }
    
    private function setValorDiasFeriasDobradas($value){
    
        $this->calc_array['valor_dias_ferias_dobradas'] = $value;
        
        return $this;
        
    }
    
    private function setInssBase($value){
    
        $this->calc_array['inss']['base'] = $value;
        
        return $this;
        
    }
    
    private function setInssValor($value){

        $this->calc_array['inss']['valor'] = $value;
        
        return $this;
        
    }

    private function setInssPercent($value){

        $this->calc_array['inss']['percent'] = "$value";
        
        return $this;
        
    }

    private function setFgtsBase($value){

        $this->calc_array['fgts']['base'] = $value;
        
        return $this;
        
    }
    
    private function setFgtsValor($value){

        $this->calc_array['fgts']['valor'] = $value;
        
        return $this;
        
    }

    private function setFgtsPercent($value){

        $this->calc_array['fgts']['percent'] = "$value";
        
        return $this;
        
    }

    private function setIrrfBase($value){

        $this->calc_array['irrf']['base'] = $value;
        
        return $this;
        
    }
    
    private function setIrrfValor($value){

        $this->calc_array['irrf']['valor'] = $value;
        
        return $this;
        
    }

    private function setIrrfPercent($value){

        $this->calc_array['irrf']['percent'] = "$value";
        
        return $this;
        
    }
    
    private function setIrrfQntDependentes($value){

        $this->calc_array['irrf']['qnt_dependentes'] = $value;
        
        return $this;
        
    }
    
    private function setIrrfValorDeducaoDependente($value){

        $this->calc_array['irrf']['valor_deducao_dependente'] = $value;
        
        return $this;
        
    }
    
    private function setIrrfQntParcelaDeducao($value){

        $this->calc_array['irrf']['parcela_deducao'] = $value;
        
        return $this;
        
    }
    
    private function setRemuneracaoBaseFeriasDobradas($value){
        
        $this->calc_array['base_ferias_dobradas'] = $value;
        
        return $this;
        
    }
    
    private function setValorTotalFeriasDobradas($value){
        
        $this->calc_array['total_ferias_dobradas'] = $value;
        
        return $this;
        
    }
    
    private function setUmTercoFeriasDobradas($value){
        
        $this->calc_array['um_terco_ferias_dobradas'] = $value;
        
        return $this;
        
    }
    
    public function setIgnorarFaltas($value){
        
        $this->calc_array['ignorar_faltas'] = $value; 
        
        return $this;
        
    }

    public function setIgnorarFeriasDobradas($value){
        
        $this->calc_array['ignorar_ferias_dobradas'] = $value;
        
        return $this;
        
    }

    public function setMetadeFerias($value){
        
        $this->calc_array['metade_ferias'] = $value;
        
        return $this;
        
    }
    
    public function setQuantidadeDias($value){
        
        $this->calc_array['quantidade_dias'] = $value;
        
        return $this;
        
    }
    
    public function setDiasFerias1($value){
        
        $this->calc_array['dias1'] = $value;
        
        return $this;
        
    }
    
    public function setDiasFerias2($value){
        
        $this->calc_array['dias2'] = $value;
        
        return $this;
        
    }
    
    public function setAdicionalInsalubridade($value){
        
        $this->calc_array['insalubridade'] = $value;
        
        return $this;
        
    }

    public function setAdicionalPericulosidade($value){
        
        $this->calc_array['periculosidade'] = $value;
        
        return $this;
        
    }
    
    public function setInsalubridadePericulosidade($value){
        
        $this->calc_array['adicional']['valor'] = $value;
        
        return $this;
        
    }

    public function setTotalMovimentosCredito($value){
        
        $this->calc_array['movimentos']['total']['credito'] = $value;
        
        return $this;
        
    }
    
    public function setTotalMovimentosDebito($value){
        
        $this->calc_array['movimentos']['total']['debito'] = $value;
        
        return $this;
        
    }
    
    public function setTotalLiquido1($value){
        
        $this->calc_array['total_liquido_1'] = $value;
        
        return $this;
        
    }

    public function setTotalLiquido2($value){
        
        $this->calc_array['total_liquido_2'] = $value;
        
        return $this;
        
    }
    /*
     * Calcula a data de fim das f�rias baseadas no in�cio, se tem abono ou n�o e sobre o total de concess�o menos as faltas caso haja.
     */
    public function setCalcDataFim(){
        
        try {
            
            $qnt_dias = $this->getCalcLimiteDiasFeriasPorFalta();

            $qnt_dias_abono = $this->getVendido() ? $this->getCalcDiasAbonoPecuniario() : 0;

            $qnt_dias_ferias = $qnt_dias - $qnt_dias_abono;    

            /*
             * Define o n�mero de dias de f�rias
             */
            $this->setDiasFerias($qnt_dias_ferias);

            /*
             * Define o n�mero de dias de abono se houver
             */
            $this->setDiasAbonoPecuniario($qnt_dias_abono);            

            /*
             * Soma o n�mero de dias a data de in�cio para encontrar a data final
             */
            $this->setDataFim($this->getDataIni()->sumDays($this->getDiasFerias())->val());

            /*
             * Subtrai um dia da data fim, pois a soma de dias � incluvie a data de in�cio
             */
            $this->setDataFim($this->getDataFim()->minusDays()->val());

            /*
             * Soma mais um dia a data de fim das f�rias para encontrar a data de retorno
             */
            $this->setDataRetorno($this->getDataFim()->sumDays()->val());


            /*
             * Define o m�s e o ano do per�odo de gozo em rela��o a data de in�cio do gozo
             */
            $this->setMes($this->getDataIni('m')->val());
            $this->setAno($this->getDataIni('Y')->val());

            /*
             * Define o m�s de f�rias
             */
            $this->setMesDt('');

            /*
             * Define o m�s de f�rias
             */
            $this->setMesFerias($this->getDataIni('m')->val());
            
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;
        
        
    }
    /*
     * PHP-DOC - Encontra o sal�rio base de calculo das f�rias
     */
    public function setCalcRemuneracaoBase(){
        
        try {
            
            /*
             * Define o valor inicial da remunera��o de base de calculo
             */
            $this->setRemuneracaoBase($this->getSalario() + $this->getSalarioVariavel() + $this->getInsalubridadePericulosidade());

            /*
             * Define a remunera��o base das f�rias dobradas
             */
            $this->setRemuneracaoBaseFeriasDobradas($this->chkFeriasDobradas()->isOk() ? $this->getSalario() + $this->getSalarioVariavel() : 0); 
            
            /*
             * Define o valor de um sal�rio adicional lan��vel em movimentos
             */
            $this->setSalarioExtra($this->getCalcSalarioExtra()); 

            /*
             * Define o valor do sal�rio base por dia
             */
            $this->setCalcRemuneracaoBasePorDia();
            
            /*
             * Define o valor do sal�rio base por dia das f�rias dobradas
             */
            $this->setCalcRemuneracaoBasePorDiaFeriasDobradas();

            /*
             * Define o valor de um ter�o das f�rias
             */
            $this->setCalcUmTerco();      

            /*
             * Define o valor de um ter�o das f�rias dobradas
             */
            $this->setCalcUmTercoFeriasDobradas();      

            /*
             * Adiciono um ter�o a remunera��o base de c�lculo
             */        
            $this->setRemuneracaoBase($this->getRemuneracaoBase() + $this->getUmTerco());

            /*
             * Adiciono um ter�o a remunera��o base de c�lculo de f�rias dobradas
             */        
            $this->setRemuneracaoBaseFeriasDobradas($this->getRemuneracaoBaseFeriasDobradas() + $this->getUmTercoFeriasDobradas());

            /*
             * Define a flag de f�rias dobradas caso exista
             */
            $this->chkFeriasDobradas()->isOk() ? $this->setFeriasDobradas('sim') : $this->setFeriasDobradas('nao');


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
     * @name setCalcSalarioLancavel
     * 
     * @internal - Calcula caso haja o valor do sal�rio lan��vel
     * 
     */       
    public function getCalcSalarioExtra(){
        
        $this->getSuperClass()->MovimentosClt->setDefault();
        $this->getSuperClass()->MovimentosClt->setIdClt($this->getSuperClass()->Clt->getIdClt()); 
        $this->getSuperClass()->MovimentosClt->setMesMov(17); // 17 = Lan�amento de f�rias
        $this->getSuperClass()->MovimentosClt->setStatus(1);
        $this->getSuperClass()->MovimentosClt->setStatusReg(1);
        $this->getSuperClass()->MovimentosClt->setStatusFerias(1);
        $this->getSuperClass()->MovimentosClt->setCodMovimento('90036');

//        $this->getSuperClass()->MovimentosClt->setDateRangeField('ano_mov');
//        $this->getSuperClass()->MovimentosClt->setDateRangeFmt('Y');
//        $this->getSuperClass()->MovimentosClt->setDateRangeIni($this->getSuperClass()->Ferias->getDataIni()->val());
//        $this->getSuperClass()->MovimentosClt->setDateRangeFim($this->getSuperClass()->Ferias->getDataFim()->val());
        
        $this->getSuperClass()->MovimentosClt->select();
        
        return $this->getSuperClass()->MovimentosClt->getRow('valor_movimento');
        
    }
    /*
     * PHP-DOC
     * 
     * @name setCalcFaltas
     * 
     * @internal - M�todo para calcular e setar o n�mero de faltas no per�odo
     * 
     */   
    public function setCalcFaltas() {
        
        try {
        
            $this->setFaltas($this->getSuperClass()->Clt->getCalcFaltasNoPeriodo());
            
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }

        return $this;

    }
    
    /*
     * PHP-DOC - Calcula a soma o valor das bases
     */
    public function setCalcSalarioVariavelInsalubridadePericulosidade(){
        
        try {

            /*
             * Verifica se a classe Curso est� inst�nciada e ent�o obtem o sal�rio do Clt na classe Curso
             */
            if(!is_object($this->getSuperClass()->Curso) || !is_object($this->getSuperClass()->Folha) || !is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            $this->getSuperClass()->Curso->select()->getRow();

            if(empty($this->getSuperClass()->Curso->getSalario())) {

                $this->error->set("N�o existe valor de sal�rio definido em curso",E_FRAMEWORK_ERROR);

            }
            else {

                $this->setSalario(round($this->getSuperClass()->Curso->getSalario(),2));

            }

            $this->getSuperClass()->Folha->setDateRangeIni($this->getDataAquisitivoIni());
            $this->getSuperClass()->Folha->setDateRangeEnd($this->getDataAquisitivoFim());

            /*
             * Define o resultado da m�dia do sal�rio vari�vel no per�odo menos os lan�amentos de insalubridade e periculosidade
             */
            $this->setSalarioVariavel($this->getSuperClass()->Folha->getCalcMediaSalarioVariavel());  

            /*
             * Define o resultado da m�dia do sal�rio vari�vel no per�odo menos os lan�amentos de insalubridade e periculosidade
             */
            $this->setInsalubridadePericulosidade($this->getSuperClass()->MovimentosClt->getCalcTotInsalubridadePericulosidade());  

            
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - define o sal�rio das f�rias sem abono pecuni�rio. O valor total das f�rias nada mais � que:
     *           (Sal�rio Base + M�dias de Sal�rio Vari�vel + Insalubridade/Periculosidade) * dias de f�rias
     */
    public function setCalcValorTotalFerias(){
        
        try {

            $this->chkIsCalc('setCalcValorTotalFerias');
            
            /*
             * Define o valor total das f�rias
             */
            $this->setValorTotalFerias(round($this->getRemuneracaoBaseDia() * $this->getDiasFerias(),2)) ;
            
            $this->setValorDiasFerias($this->getValorTotalFerias()/$this->getDiasFerias());
            
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - define o sal�rio das f�rias dobradas sem abono pecuni�rio. O valor total das f�rias nada mais � que:
     *           (Sal�rio Base + M�dias de Sal�rio Vari�vel) * 2
     */
    public function setCalcValorTotalFeriasDobradas(){
        
        try {

            $this->chkIsCalc('setCalcValorTotalFerias');
            
            /*
             * Define o valor total das f�rias dobradas
             */
            $this->setValorTotalFeriasDobradas(round($this->getRemuneracaoBaseDiaFeriasDobradas() * 30,2));
            
            $this->setValorDiasFeriasDobradas($this->getValorTotalFeriasDobradas()/30);
            
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
     * @name setCalcMovimentosClt
     * 
     * @internal - Adiciona movimentos de CR�DITO ou D�BITO nas f�rias que s�o lan�ados fora da folha de pagamento
     * 
     */       
    public function setCalcMovimentosClt(){
        
        try {
            
            $this->setTotalMovimentosCredito(0);
            $this->setTotalMovimentosDebito(0);
            
            $this->getSuperClass()->MovimentosClt->setDefault();
            $this->getSuperClass()->MovimentosClt->setIdClt($this->getSuperClass()->Clt->getIdClt()); 
            $this->getSuperClass()->MovimentosClt->setMesMov(17); // 17 = Lan�amento de f�rias
            $this->getSuperClass()->MovimentosClt->setStatus(1);
            $this->getSuperClass()->MovimentosClt->setStatusReg(1);
            $this->getSuperClass()->MovimentosClt->setStatusFerias(1);
            
            
            $this->getSuperClass()->MovimentosClt->setDateRangeField('ano_mov');
            $this->getSuperClass()->MovimentosClt->setDateRangeFmt('Y');
            $this->getSuperClass()->MovimentosClt->setDateRangeIni($this->getSuperClass()->Ferias->getDataIni()->val());
            $this->getSuperClass()->MovimentosClt->setDateRangeFim($this->getSuperClass()->Ferias->getDataFim()->val());
            
            $this->getSuperClass()->MovimentosClt->db->setQuery(WHERE,"cod_movimento NOT IN ({$this->getSuperClass()->getKeyMaster(12)}) AND");
                
            $this->rh_movimentos_clt = $this->getSuperClass()->MovimentosClt->select()->db->getCollection('tipo_movimento,cod_movimento');
            
            foreach ($this->rh_movimentos_clt['dados'] as $collection_tipo => $tipo_itens) {  
                
                foreach ($tipo_itens as $collection_itens => $itens) {   
                    
                    switch ($collection_tipo) {
                        case 'CREDITO':
                            $this->setTotalMovimentosCredito($this->getTotalMovimentosCredito() + $itens['valor_movimento']);                
                            break;
                        case 'DEBITO':
                            $this->setTotalMovimentosDebito($this->getTotalMovimentosDebito() + $itens['valor_movimento']);                
                            break;
                        default:
                            $this->error->set('Movimento sem defini��o de CR�DITO ou D�BITO',E_FRAMEWORK_ERROR);
                            break;
                    }

                    if($itens['incidencia_inss']) $this->setInssBase($this->getInssBase() + $itens['valor_movimento']);                 

                    if($itens['incidencia_irrf']) $this->setIrrfBase($this->getIrrfBase() + $itens['valor_movimento']);    

                    if($itens['incidencia_fgts']) $this->setFgtsBase($this->getFgtsBase() + $itens['valor_movimento']);    
                        
                }
                
            }
            
            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        
        return $this;
        
    }
    /*
     * PHP-DOC - Define o valor do sal�rio base de calculo das f�rias por dia
     */
    public function setCalcRemuneracaoBasePorDia(){
        
        $this->setRemuneracaoBaseDia($this->getRemuneracaoBase()/30); 
        
        $this->setValue(1);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Define o valor do sal�rio base de calculo das f�rias dobradas por dia
     */
    public function setCalcRemuneracaoBasePorDiaFeriasDobradas(){
        
        $this->setRemuneracaoBaseDiaFeriasDobradas($this->getRemuneracaoBaseFeriasDobradas()/30); 
        
        $this->setValue(1);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Obtem um ter�o das f�rias referente ao valor base de calculo 
     */
    public function setCalcUmTerco(){
        
        $this->setUmTerco(round((($this->getRemuneracaoBase()/30) * $this->getDiasFerias())/3,2));
        
        $this->setValue(1);
        
        return $this;
        
    }      

    public function setCalcUmTercoFeriasDobradas(){
        
        $this->setUmTercoFeriasDobradas(round($this->getRemuneracaoBaseFeriasDobradas()/3,2));
        
        $this->setValue(1);
        
        return $this;
        
    }      
    /*
     * PHP-DOC - Obtem o valor total l�quido referente ao total_remuneracoes - total_descontos
     */
    public function setCalcTotalLiquido(){
     
        $this->setTotalLiquido(round($this->getTotalRemuneracoes()-$this->getTotalDescontos(),2));
        
        $this->setValue(1);
        
        return $this;
        
    }      
    
    public function getSalarioExtra($value){
        
       if(empty($value)){
            
            return $this->calc_array['salario_extra']; 
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['salario_extra'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
    }
    
    /*
     * PHP-DOC - Obtem uma cole��o de movimentos
     */
    public function getCollectionMovimentosClt(){
     
        return $this->rh_movimentos_clt;
        
    }  
    
    private function getRemuneracaoBaseDia($value){
    
       if(empty($value)){
            
            return $this->calc_array['remuneracao_base_dia'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['remuneracao_base_dia'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
        
    }
    
    private function getRemuneracaoBaseDiaFeriasDobradas($value){
    
       if(empty($value)){
            
            return $this->calc_array['remuneracao_base_dia_ferias_dobradas'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['remuneracao_base_dia_ferias_dobradas'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
        
    }
    
    private function getValorDiasFeriasDobradas($value){
    
       if(empty($value)){
            
            return $this->calc_array['valor_dias_ferias_dobradas'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['valor_dias_ferias_dobradas'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
        
    }
    
    /*
     * PHP-DOC - Obtem o ponteiro da Super Classe
     */
    public function getInssBase($value){
        
       if(empty($value)){
            
            return $this->calc_array['inss']['base'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['inss']['base'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
    }
    
    public function getInssValor($value){
        
       if(empty($value)){
            
            return $this->calc_array['inss']['valor'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['inss']['valor'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
    }

    public function getInssPercent($value){

       if(empty($value)){
            
            return $this->calc_array['inss']['percent'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['inss']['percent'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }           
        
    }

    public function getFgtsBase($value){
        
       if(empty($value)){
            
            return $this->calc_array['fgts']['base'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['fgts']['base'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
    }
    
    public function getFgtsValor($value){

       if(empty($value)){
            
            return $this->calc_array['fgts']['valor'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['fgts']['valor'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
    }

    public function getFgtsPercent($value){
        
       if(empty($value)){
            
            return $this->calc_array['fgts']['percent'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['fgts']['percent'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         

    }

    public function getIrrfBase($value){
        
       if(empty($value)){
            
            return $this->calc_array['irrf']['base'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['irrf']['base'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
    }
    
    public function getIrrfValor($value){
        
       if(empty($value)){
            
            return $this->calc_array['irrf']['valor'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['irrf']['valor'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         

    }

    public function getIrrfPercent($value){

       if(empty($value)){
            
            return $this->calc_array['irrf']['percent'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['irrf']['percent'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         
        
        
    }
    
    public function getIrrfQntDependentes(){

        return $this->calc_array['irrf']['qnt_dependentes'];
        
    }
    
    private function getIrrfValorDeducaoDependente(){

        return $this->calc_array['irrf']['valor_deducao_dependente'];
        
    }    
    
    public function getIrrfQntParcelaDeducao(){

        return $this->calc_array['inss']['parcela_deducao'];
        
    }
    
    public function getIgnorarFaltas(){

        return $this->calc_array['ignorar_faltas'];
        
    }
    
    public function getIgnorarFeriasDobradas(){
        
        return $this->calc_array['ignorar_ferias_dobradas'];
        
    }    
    
    public function getMetadeFerias(){
        
        return $this->calc_array['metade_ferias'];
        
    }    
    
    public function getQuantidadeDias(){
        
        $this->calc_array['quantidade_dias'];
        
        return $this;
        
    }
    
    public function getDiasFerias1(){
        
        return $this->calc_array['dias1'];
        
    }
    
    public function getDiasFerias2(){
        
        return $this->calc_array['dias2'];
        
    }
    
    public function getAdicionalInsalubridade($value){
        
        if(empty($value)){
            
            return $this->calc_array['adicional']['insalubridade'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['adicional']['insalubridade'], $casas_decimais, $separador_fracao, $separador_unidades);            
            
        }         
        
    }
    
    public function getAdicionalPericulosidade($value){
        
        if(empty($value)){
            
            return $this->calc_array['adicional']['periculosidade'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['adicional']['periculosidade'], $casas_decimais, $separador_fracao, $separador_unidades);            
            
        }         
        
    }
    
    public function getInsalubridadePericulosidade($value){
        
        if(empty($value)){
            
            return $this->calc_array['adicional']['valor'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['adicional']['valor'], $casas_decimais, $separador_fracao, $separador_unidades);            
            
        }         
        
        
        $this->calc_array['adicional']['valor'] = $value;
        
    }
    
    public function getTotalMovimentosCredito($value){
        
        if(empty($value)){
            
            return $this->calc_array['movimentos']['total']['credito'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['movimentos']['total']['credito'], $casas_decimais, $separador_fracao, $separador_unidades);            
            
        }         
        
        
        return $this->calc_array['movimentos']['total']['credito'];
        
    }
    
    public function getTotalMovimentosDebito($value){
        
        if(empty($value)){
            
            return $this->calc_array['movimentos']['total']['debito'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['movimentos']['total']['debito'], $casas_decimais, $separador_fracao, $separador_unidades);            
            
        }         
        
        
        return $this->calc_array['movimentos']['total']['debito'];
        
    }    
    
    public function getTotalLiquido1($value){
        
        if(empty($value)){
            
            return $this->calc_array['total_liquido_1'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['total_liquido_1'], $casas_decimais, $separador_fracao, $separador_unidades);            
            
        }         
        
    }

    public function getTotalLiquido2($value){
        
        if(empty($value)){
            
            return $this->calc_array['total_liquido_2'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['total_liquido_2'], $casas_decimais, $separador_fracao, $separador_unidades);            
            
        }         
        
    }    

    /*
     * PHP-DOC 
     * 
     * @name select
     * 
     * @internal - M�todo de sele��o padr�o de registros da classe
     *              
     */    
    public function selectExt(){
        
        try {

            $this->db->setQuery(WHERE," AND status ",ADD);

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

            $id_ferias = $this->getIdFerias(); 

            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND projeto = {$id_projeto}",ADD);}

            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}

            if(!empty($id_ferias)) {$this->db->setQuery(WHERE,"AND id_ferias = {$id_ferias}",ADD);}

            if(empty($id_regiao) && empty($id_projeto) && empty($id_clt) && empty($id_ferias)) $this->error->set('Nenhum par�metro definido para o m�todo select da classe RhFeriasClass',E_FRAMEWORK_ERROR);

            $this->db->setQuery(ORDER,
                                "
                                id_ferias DESC,
                                id_clt DESC    
                                ");
            
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
     * @name getRowExt
     * 
     * @internal - M�todo extendido da classe din�mica para carregar propriedades extendidaas do m�todo select
     *             como um campo calculado.
     * 
     */    
    public function getRowExt(){
        
        return $this;
        
    }
    
    
    /*
     * PHP-DOC 
     * 
     * @name getPeriodosGozados
     * 
     * @internal - Esse m�todo retorna um array com os per�odos de f�rias j� gozados
     *              
     */    
    public function getPeriodosGozados() {
        
        try {
            
            $this->db->setQuery(SELECT, "data_aquisitivo_ini, data_aquisitivo_fim");
            $this->db->setQuery(WHERE, "status");
            $this->db->setQuery(FROM, "rh_ferias");
            $this->db->setQuery(ORDER, "id_clt, id_ferias DESC");

            if(is_object($this->getSuperClass()->Clt)){

                $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
                $id_clt = $this->getSuperClass()->Clt->getIdClt();

            }        
            else {

                $id_regiao = $this->getIdRegiao();
                $id_clt = $this->getIdClt();

            } 

            /*
             * Caso o n�mero de dias gozados seja inferior a 20 dias, ent�o � porque as f�rias
             * est�o sendo parceladas conforme utilizado no Iabas
             */
            $this->db->setQuery(WHERE, "AND dias_ferias >= 20",ADD);
            
            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND projeto = {$id_projeto}",ADD);}

            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}
            
            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->setValue(1);
            
            return $this->db->getArray();
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);

            return 0;
            
        }
        
        
    }
    
    public function getRowMatched(){
        
        return $this->db->getRowMatched();
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getPeriodoAquisitivoPendente
     * 
     * @internal - param type $tipo = serve para determinar uma janela de um ano a mais no per�odo
     *              
     */    
    public function getPeriodoAquisitivoPendente($tipo = 1){
        
        try {
            
            $novo_periodo_aquisitivo_final = array();

            if(!is_object($this->getSuperClass()->Folha)) $this->error->set('N�o � poss�vel verificar o per�odo aquisitivo pendente sem a classe folha est�r inst�nciada',E_FRAMEWORK_ERROR);

            if(!is_object($this->getSuperClass()->Eventos)) $this->error->set('N�o � poss�vel verificar o per�odo aquisitivo pendente sem a classe eventos est�r inst�nciada',E_FRAMEWORK_ERROR);
            
            $ano = 0; $mes = 1; $dia = 2;

            $array_total_periodos = array();
            
            /*
             * Monta a listagem dos per�odos aquisitivos a partir da data de admiss�o
             */
            $quantidade_anos    = date('Y') - $this->getSuperClass()->Clt->getDataEntrada()->val('Y');
            
            $quantidade_anos    = ($tipo == 2) ? $quantidade_anos + 1: $quantidade_anos ;
            
            for($a = 0; $a < $quantidade_anos; $a++) {

                $array_total_periodos[$a]['data_aquisitivo_ini'] = $this->getSuperClass()->Clt->getDataEntrada();
                $array_total_periodos[$a]['data_aquisitivo_fim'] = $this->getSuperClass()->Clt->getDataEntrada();

                $array_total_periodos[$a]['data_aquisitivo_ini']->sumYear($a);
                $array_total_periodos[$a]['data_aquisitivo_fim']->minusDays(1)->sumYear($a + 1);
                
            }  

            $periodos_gozados  = $this->getPeriodosGozados();
            
            $periodoAquivisitoPendente = array_diff_assoc($array_total_periodos, $periodos_gozados);

            $nova_data_admissao = '';
            
            /*
             * O erro est� nesse m�todo
             */
            foreach ($periodoAquivisitoPendente as $key => $value) {


                $this->getSuperClass()->Clt->setDateRangeIni($periodoAquivisitoPendente[$key]['data_aquisitivo_ini']);
                $this->getSuperClass()->Clt->setDateRangeFim($periodoAquivisitoPendente[$key]['data_aquisitivo_fim']);

                $faltas = $this->getSuperClass()->Clt->getCalcFaltasNoPeriodo();

                $novo_periodo_aquisitivo = $this->getSuperClass()->Eventos->getNovaDataEventosComMaisDe180Dias();  

                if($novo_periodo_aquisitivo[$key]['soma_eventos_mais_180'] > 0) {

                    /*
                     * Pega o novo per�odo aquisitivo em fun��o dos novos eventos com 180 dias ou mais
                     */

                    $novo_periodo_aquisitivo_final[] = array(
                                                            'data_aquisitivo_ini' => $novo_periodo_aquisitivo[$key]['data_aquisitivo_ini'],
                                                            'data_aquisitivo_fim' => $novo_periodo_aquisitivo[$key]['data_aquisitivo_fim'],
                                                            'soma_eventos_mais_180' => $novo_periodo_aquisitivo[$key]['soma_eventos_mais_180'],
                                                            'faltas' => $faltas
                                                            );

                    $nova_data_admissao = explode('-',$novo_periodo_aquisitivo[$key]['data_aquisitivo_fim']);

                    /*
                     * Reconstrou a lista de per�odos aquisitivos pendentes em fun��o da soma dos eventos no per�odo for igual ou maior que 180 dias
                     */
                    $quantidade_anos    = date('Y') - $nova_data_admissao[$ano];

                    $quantidade_anos    = ($tipo == 2) ? $quantidade_anos + 1: $quantidade_anos ;

                    for($i = 0; $i < $quantidade_anos; $i++) {

                        $novo_periodo_aquisitivo_final[] = array(
                                                                'data_aquisitivo_ini' => date('Ymd', mktime('0','0','0', $nova_data_admissao[$mes], $nova_data_admissao[$dia], $nova_data_admissao[$ano] + $i)),
                                                                'data_aquisitivo_fim' => date('Ymd', mktime('0','0','0', $nova_data_admissao[$mes], $nova_data_admissao[$dia] - 1, $nova_data_admissao[$ano] + $i + 1)),
                                                                'soma_eventos_mais_180' => 0,
                                                                'faltas' => $faltas
                                                                );

                        } 

                    break;

                }
                else {

                    /*
                     * Casa n�o haja evento no per�do com 180 dias ou mais mantem os mesmos per�odos aquisitivos pendentes
                     */


                    $novo_periodo_aquisitivo_final[] = array(
                                                            'data_aquisitivo_ini' => $periodoAquivisitoPendente[$key]['data_aquisitivo_ini'],
                                                            'data_aquisitivo_fim' => $periodoAquivisitoPendente[$key]['data_aquisitivo_fim'],
                                                            'soma_eventos_mais_180' => 0,
                                                            'faltas' => $faltas
                                                            );

                }



            }

            $this->setValue(1);
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $novo_periodo_aquisitivo_final;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name update
     * 
     * @internal - Executa a atualiza��o de registros da tabela rh_ferias
     *             Essa atualiza��o est� sendo feita para corre��o dos valores distribu�dos em dois meses
     *              
     */    
    public function update(){
        
        $id_ferias = $this->getIdFerias();

        $id_clt = $this->getIdClt();

        if(empty($this->rh_ferias_save)) $this->error->set('O vetor $this->rh_ferias_save est� vazio, isso gerou uma exce��o no m�todo rh->Ferias->update() que impede sua finaliza��o',E_FRAMEWORK_ERROR);

        if(empty($id_ferias)) $this->error->set(array(5,__METHOD__),E_FRAMEWORK_ERROR);

        $this->getSuperClass()->Clt->onUpdate();
        
        $this->db->makeFieldUpdate('rh_ferias',$this->rh_ferias_save);

        $this->db->setQuery(WHERE, " id_ferias = {$id_ferias} ");

        if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);

        $itens = $this->getMakeItens();

        foreach ($itens as $key => $value) {

            $this->getSuperClass()->FeriasItens->setDefault();

            $this->getSuperClass()->FeriasItens->setIdFerias($id_ferias);
            $this->getSuperClass()->FeriasItens->setIdClt($id_clt);
            $this->getSuperClass()->FeriasItens->setIdLegenda($value[0]);
            $this->getSuperClass()->FeriasItens->setCompetencia($this->getDataIni('Y-m-d')->val());
            $this->getSuperClass()->FeriasItens->setQuantidade($value[1]);
            $this->getSuperClass()->FeriasItens->setValor($value[2]);
            $this->getSuperClass()->FeriasItens->setIncideIr($value[3]);
            $this->getSuperClass()->FeriasItens->setIncideInss($value[4]);
            $this->getSuperClass()->FeriasItens->setIncideFgts($value[5]);
            $this->getSuperClass()->FeriasItens->setCriadoPor($this->getUser());
            $this->getSuperClass()->FeriasItens->setStatus(1);

            if(!$this->getSuperClass()->FeriasItens->update() && $this->getSuperClass()->FeriasItens->db->getRowMatched()) {

                $this->getSuperClass()->FeriasItens->setCriadoEm($this->date->now()->get());
                $this->getSuperClass()->FeriasItens->insert();

            }    

        }        
        
        return $this;
       
    }
    
    /*
     * PHP-DOC 
     * 
     * @name insert
     * 
     * @internal - Executa a inser��o de registros da tabela rh_ferias
     *              
     */    
    public function insert(){
        
        if(!is_object($this->getSuperClass()->FeriasItens)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

        if(empty($this->rh_ferias_save)) $this->error->set(array(7,__METHOD__),E_FRAMEWORK_ERROR);

        /*
         * Verifica se j� houve processamento dessas f�rias para evitar inconsist�ncia de lan�amento
         */
        if($this->chkJaFoiProcessado()->isOk()) {
            
            $this->error->set("Esse per�odo de f�rias j� foi processado",E_FRAMEWORK_NOTICE);
            
            return 0;
            
        }
        else {
            
            $this->getSuperClass()->Clt->onUpdate();
            
            $this->db->makeFieldInsert('rh_ferias',$this->rh_ferias_save);
            
            if(!$this->db->setRs()) $this->error->set(array(2,__METHOD__),E_FRAMEWORK_ERROR);
            
            $this->setIdFerias($this->db->getKey());
            
            if($this->insertFeriasItens()){
                
                return 1;
                
            }
            else {
                
                return 0;
                
            }
        
        }

        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name insertFeriasItens
     * 
     * @internal - Inclui os itens de f�rias
     *              
     */    
    public function insertFeriasItens(){
        
        if(!is_object($this->getSuperClass()->FeriasItens))  $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);
        
        $itens = $this->getMakeItens();
        
        foreach ($itens as $key => $value) {
            
            $this->getSuperClass()->FeriasItens->setDefault();
            
            $this->getSuperClass()->FeriasItens->setIdClt($this->getIdClt());
            $this->getSuperClass()->FeriasItens->setIdFerias($this->getIdFerias());
            $this->getSuperClass()->FeriasItens->setIdLegenda($value[0]);
            $this->getSuperClass()->FeriasItens->setCompetencia($this->getDataIni('Ym')->val());
            $this->getSuperClass()->FeriasItens->setQuantidade($value[1]);
            $this->getSuperClass()->FeriasItens->setValor($value[2]);
            $this->getSuperClass()->FeriasItens->setIncideIr($value[3]);
            $this->getSuperClass()->FeriasItens->setIncideInss($value[4]);
            $this->getSuperClass()->FeriasItens->setIncideFgts($value[5]);
            $this->getSuperClass()->FeriasItens->setCriadoPor($this->getUser());
            $this->getSuperClass()->FeriasItens->setCriadoEm($this->date->now()->get());
            $this->getSuperClass()->FeriasItens->setStatus(1);

            if(!$this->setValue($this->getSuperClass()->FeriasItens->insert())){
                
                return 0;
                
            }
            
        }
        
        return 1;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name chkIsCalc
     * 
     * @internal - Verifica se os valores prim�rios est�o setados para calculo das f�rias
     *              
     */    
    public function chkIsCalc($method){ 
        
        try {

            $id_clt = is_object($this->getSuperClass()->Clt) ? $this->getSuperClass()->Clt->getIdClt() : $this->getIdClt();
            $data_aquisitivo_ini = $this->getDataAquisitivoIni();
            $data_aquisitivo_fim = $this->getDataAquisitivoFim();
            $data_ini = $this->getDataIni();

            if(empty($data_aquisitivo_ini) || empty($data_aquisitivo_fim) || empty($id_clt) || (empty($data_ini) && $method=="setCalcFerias")){

                $this->error->set("
                    <pre>

                    N�o � poss�vel executar o m�todo $method() sem definir alguns valores da classe F�rias:

                    Clt->setIdClt($id_clt)
                        ou
                    Ferias->setIdClt($id_clt)

                    Ferias->setDataAquisitivoIni('$data_aquisitivo_ini')
                    Ferias->setDataAquisitivoIni('$data_aquisitivo_fim')
                    Ferias->setDataIni('$data_ini')


                    </pre>
                    ",E_FRAMEWORK_ERROR);
                

            }
            
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
     * @name chkJaFoiProcessado
     * 
     * @internal - Ferifica se j� houve processamento do per�odo referente ao per�odo aquisitivo definido na classe
     *              
     */    
    private function chkJaFoiProcessado(){
        
        try {

            $periodos_gozados = $this->getPeriodosGozados();
            
            $this->setValue(0);

            foreach ($periodos_gozados as $key => $value) {

                if($value['data_aquisitivo_ini']==$this->getDataAquisitivoIni('Y-m-d')) {

                    $this->setValue(1);
                    
                }

            }
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }

        return $this;
        
    }   
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcLimiteDiasFeriasPorFalta
     * 
     * @internal - M�todo para calcular o n�mero de dias dispon�veis para gozar as f�rias proporcional as faltas
     *              
     */    
    public function getCalcLimiteDiasFeriasPorFalta() {
        
        try {
            
            $qnt_dias = $this->getQuantidadeDias();
            
            $this->chkIsCalc('getCalcLimiteDiasFeriasPorFalta');

            if(!is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            /*
             * Seta o range na folha pois � ele que dever� determinar os IDs de movimento no per�odo
             */

            $this->getSuperClass()->Clt->setDateRangeIni($this->getDataAquisitivoIni());
            $this->getSuperClass()->Clt->setDateRangeFim($this->getDataAquisitivoFim());

            $faltas = $this->getSuperClass()->Clt->getCalcFaltasNoPeriodo();
            
            $this->setFaltas($faltas);

            if ($faltas <= 5 || $this->getIgnorarFaltas()) {
                $qnt_dias = 30;
            } elseif ($faltas >= 6 and $faltas <= 14) {
                $qnt_dias = 24;
            } elseif ($faltas >= 15 and $faltas <= 23) {
                $qnt_dias = 18;
            } elseif ($faltas >= 24 and $faltas <= 32) {
                $qnt_dias = 12;
            } elseif ($faltas > 32) {
                $qnt_dias = 0;
            }
            
            /*
             * Caso as f�rias sejam dadas em duas etapas, por enquanto se ignora as faltas
             */
            $qnt_dias = $this->getMetadeFerias() ? 15 : $qnt_dias;
            
            $this->setValue(1);

            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
            
        }
        
        /*
         * Caso a propriedade setIgnorarFaltas esteja setada com 1, as mesmas n�o ser�o computadas
         */
        
        return $qnt_dias;

    }
    
    /*
     * PHP-DOC 
     * 
     * @name chkFeriasDobradas
     * 
     * @internal - No caso a data limite pela lei � de 30 dias de anteced�ncia para o final do per�odo aquisitivo
     *             Tamb�m existe o caso em que o CLT pode ter sa�do de evento e em virtude disso ter acabado de passar do prazo consessivo
     * 
     * Art 137 CLT: O empregador dever� conceder imediatamente as f�rias ao empregado assim que ele retornar ao trabalho, por�m sem o pagamento 
     *              da multa prevista no artigo 137 da CLT. Isto porque, o atraso da concess�o n�o foi de responsabilidade do empregador, pois o 
     *              per�odo do aux�lio-doen�a � incompat�vel com a concess�o das f�rias uma vez que o contrato de trabalho tem efeito suspensivo 
     *              decorrente do afastamento do empregado por aux�lio-doen�a.
     *              
     */    
    public function chkFeriasDobradas(){
        
        try {
            $this->chkIsCalc('chkFeriasDobradas');
            
            $this->setValue(0);
            
            $data_concessivo_fim = clone $this->date;
            
            $data_concessivo_fim->set($this->getDataAquisitivoFim())->sumMonth(12);
            
            $ult_evento = $this->getSuperClass()->Eventos->getUltimoEvento();
            
            if($this->getDataIni()->val() > $data_concessivo_fim->val()) {

                $this->setValue(1);

            }    
            
            /*
             * Se o in�cio do evento for menor que o concessivo e o seu final do evento menor que o concessivo ent�o o Clt teve evento dentro do per�odo concessivo. O que permite conceder as f�rias
             * No dia seguinte ao final do evento. No caso aqui est� definido com um prazo de at� um m�s por orienta��o da Gimenez, mas isso vai ser mudado
             * para a solu��o que define o art 137 da CLT.
             */
            
            if($ult_evento) { 
                
                if($ult_evento['data_ini']->val() < $data_concessivo_fim->val() && $ult_evento['data_fim']->val() > $data_concessivo_fim->val() && $this->getDataIni()->val() == $ult_evento['data_fim']->sumDays()->val()) {

                    $this->setValue(0);

                }
                
            }
            
            
            if($this->getIgnorarFeriasDobradas() && $this->isOk()) $this->setValue(0);
            
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
            
        }
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcDiasFeriasComAbonoPecuniario
     * 
     * @internal - Calcula os dias de f�rias a serem gozadas com o desconto dos dias de abono pecuni�rio
     *              
     */    
    public function getCalcDiasFeriasComAbonoPecuniario(){
        
        return ($this->getCalcLimiteDiasFeriasPorFalta()-$this->getCalcDiasAbonoPecuniario());
        
    }

    /*
     * PHP-DOC 
     * 
     * @name getCalcDiasFeriasSemAbonoPecuniario
     * 
     * @internal - Calcula os dias de f�rias a serem gozadas sem o desconto dos dias de abono pecuni�rio
     *              
     */    
    public function getCalcDiasFeriasSemAbonoPecuniario(){
        
        return ($this->getCalcLimiteDiasFeriasPorFalta());
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcDiasAbonoPecuniario
     * 
     * @internal - Calcula os dias de abono pecuni�rio caso haja em fun��o dos dias m�ximos de concess�o
     *              
     *             getDiasDeAbonoPecuniario = arredonda para cima((30 dias no mes)-(faltas no periodo aquisitivo)/3)
     */    
    public function getCalcDiasAbonoPecuniario(){
        
        return ceil((($this->getCalcLimiteDiasFeriasPorFalta()/3)));
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getRemuneracaoBaseFeriasDobradas
     * 
     * @internal - Obtem o sal�rio base de calculo das f�rias
     * 
     */    
    public function getRemuneracaoBaseFeriasDobradas($value){
    
       if(empty($value)){
            
            return $this->calc_array['base_ferias_dobradas'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['base_ferias_dobradas'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         

    }
    
    /*
     * PHP-DOC 
     * 
     * @name getValorTotalFeriasDobradas
     * 
     * @internal - Obtem o valor total das f�rias dobradas
     * 
     */    
    public function getValorTotalFeriasDobradas($value){
    
       if(empty($value)){
            
            return $this->calc_array['total_ferias_dobradas'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['total_ferias_dobradas'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         

    }
    

    /*
     * PHP-DOC 
     * 
     * @name getUmTercoFeriasDobradas
     * 
     * @internal - Obtem o sal�rio de um ter�o de f�rias dobradas
     * 
     */    
    public function getUmTercoFeriasDobradas($value){
    
       if(empty($value)){
            
            return $this->calc_array['um_terco_ferias_dobradas'];
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($this->calc_array['um_terco_ferias_dobradas'], $casas_decimais, $separador_fracao, $separador_unidades);            
        }         

    }
    
    /*
     * PHP-DOC 
     * 
     * @name getCalcSubTotalLiquido
     * 
     * @internal - Obtem o sub total l�quido a receber das f�rias sem o desconto de pens�o aliment�cia
     * 
     */    
    public function getCalcSubTotalLiquido($value){
        
       $liquido_a_receber = $this->getTotalRemuneracoes()-($this->getInssValor()+$this->getIrrfValor());
        
       if(empty($value)){
            
            return $liquido_a_receber;
            
        }
        else {

            $array_format = explode('|',$value);
            
            $moeda = $array_format[0];
            $separador_unidades = $array_format[1];
            $separador_fracao = $array_format[2];
            $casas_decimais = $array_format[3];
            
            return $moeda.number_format($liquido_a_receber, $casas_decimais, $separador_fracao, $separador_unidades);            
        }          
        
    }    
 
    /*
     * PHP-DOC 
     * 
     * @name setCalcAbonoPecuniario
     * 
     * @internal - Obtem o valor do abono pecuni�rio
     * 
     */    
    public function setCalcAbonoPecuniario(){
        
        $this->setAbonoPecuniario(round($this->getVendido() ? $this->getDiasAbonoPecuniario() * $this->getValorDiasFerias() : 0,2));
        
        $this->setValue(1);
        
        return $this;
            
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcUmTercoAbonoPecuniario
     * 
     * @internal - Obtem o valor de um ter�o do abono pecuni�rio
     * 
     */    
    public function setCalcUmTercoAbonoPecuniario(){
        
        $this->setUmTercoAbonoPecuniario(round($this->getAbonoPecuniario()/3,2));
        
        $this->setValue(1);
        
        return $this;
        
    } 
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcTotalRendimentos
     * 
     * @internal - C�lculo de remunerac�es de f�rias 
     * 
     *             salario_base + um_terco + abono_pecuniario + um_terco_abono_pecuniario 
     * 
     */    
    public function setCalcTotalRendimentos(){
        
//            echo $this->getValorTotalFerias().'<br>';
//            echo $this->getUmTerco().'<br>';
//            echo $this->getValorTotalFeriasDobradas().'<br>'; 
//            echo $this->getUmTercoFeriasDobradas().'<br>';
//            echo $this->getAbonoPecuniario().'<br>'; 
//            echo $this->getUmTercoAbonoPecuniario().'<br>';
//            echo $this->getTotalMovimentosCredito().'<br>';
//            exit();
        
        
        $this->setTotalRemuneracoes($this->getValorTotalFerias()+$this->getUmTerco()+$this->getValorTotalFeriasDobradas()+$this->getUmTercoFeriasDobradas()+$this->getAbonoPecuniario()+$this->getUmTercoAbonoPecuniario()+$this->getTotalMovimentosCredito());
        
        $this->setValue(1);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcTotalDescontos
     * 
     * @internal - Obtem o valor total de descontos das f�rias
     * 
     *             total_descontos = inss + irrf + pensao_alimenticia 
     * 
     */    
    public function setCalcTotalDescontos(){ 
        
        $this->setTotalDescontos($this->getInssValor()+$this->getIrrfValor()+$this->getPensaoAlimenticia()+$this->getTotalMovimentosDebito());
        
        $this->setValue(1);

        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcInssFgtsIrrf
     * 
     * @internal - Executa o calculo do INSS,IRRF e FGTS e seta os campos da classe
     * 
     */    
    public function setCalcInssFgtsIrrf(){
        
        try {
            
            include_once('calculos.php');

            $this->chkIsCalc('setCalcInssIrrfFgts');

            $calculos = new calculos();

            /*
             * A data usada para calculo de INSS e IRRF � feita de acordo com o ano de processamento das f�rias.
             * Essa condi��o para quando o per�odo de f�rias for no ano seguinte, calcular como ano vigente.
             * 
             * Informa��es retiradas da rotina antiga de calculo e confirmadas pelo consultor Rog�rio
             */

            $data_calc = date('Y') . '-01-01'; 

            $id_clt = $this->getSuperClass()->Clt->GetIdClt() ? $this->getSuperClass()->Clt->GetIdClt() : $this->GetIdClt();

            $id_regiao = $this->getSuperClass()->Clt->GetIdRegiao() ? $this->getSuperClass()->Clt->GetIdRegiao() : $this->GetRegiao();

            $id_projeto = $this->getSuperClass()->Clt->GetIdProjeto() ? $this->getSuperClass()->Clt->GetIdProjeto() : $this->GetProjeto();

            /*
             * Executa o calculo do INSS
             */

            $this->setInssBase($this->getValorTotalFerias()+$this->getUmTerco());

            $calculos->MostraINSS($this->getInssBase(), $data_calc);

            if($this->getSuperClass()->Clt->GetDescontoInss()){

                /* 
                 * Caso j� possua desconto de INSS em outra empresa, verifica se � parcial ou total
                 * e ent�o faz o desconto proporcional de INSS sem ultrapassar o Teto na soma dos 
                 * dois descontos
                 */

                if(strtoupper($this->getSuperClass()->Clt->getTipoDescontoInss())=='ISENTO'){

                    $this->setInss(0);
                    $this->setInssPercent('00');        


                }
                elseif(strtoupper($this->getSuperClass()->Clt->getTipoDescontoInss())=='PARCIAL'){

                    $teto_desconto_inss = $calculos->teto;

                    if(($this->getSuperClass()->Clt->getDescontoOutraEmpresa()) >= $teto_desconto_inss){

                        $this->setInssValor(0);

                    }
                    else {

                        $this->setInssValor($teto_desconto_inss - $this->getSuperClass()->Clt->getDescontoOutraEmpresa());

                    }


                }


            }
            else{

                $this->setInssValor($calculos->valor);

            }

            $this->setInssPercent($calculos->percentual * 100);        

            /*
             * Atualiza os campos de inss referentes a tabela
             */
            $this->setInss($this->getInssValor());

            $this->setInssPorcentagem($this->getInssPercent());    

            $this->setBaseInss($this->getInssBase());


            /*
             * Executa o calculo do FGTS
             */

            $this->setFgtsBase($this->getValorTotalFerias()+$this->getUmTerco());

            $this->setFgtsPercent(0.08);        

            $this->setFgtsValor(round($this->getFgtsBase() * $this->getFgtsPercent(),2));

            $this->setFgts($this->getFgtsValor());


            /*
             * Executa o calculo do IRRF
             * 
             * Caso haja f�rias dobradas ent�o incide IR sobre o valor Total
             * 
             * Caso j� haja desconto integral do INSS em outra empresa ou parcial
             */


            $this->setIrrfBase($this->getValorTotalFerias()+$this->getUmTerco()-$this->getInssValor()+$this->getValorTotalFeriasDobradas()+$this->getUmTercoFeriasDobradas());

            $calculos->MostraIRRF($this->getIrrfBase(), $id_clt, $id_projeto, $data_calc);

            $this->setIrrfValor(round($calculos->valor,2));

            $this->setIrrfPercent($calculos->percentual);   

            $this->setIrrfQntDependentes($calculos->total_filhos_menor_21);        

            $this->setIrrfValorDeducaoDependente($calculos->valor_deducao_ir_fixo);   

            $this->setIrrfQntParcelaDeducao($calculos->valor_fixo_ir);      


            /*
             * Atualiza o campo de Irrf referente a tabela
             */
            $this->setIr($this->getIrrfValor());

            $this->setBaseIrrf($this->getIrrfBase());

            $this->setPercentualIrrf($this->getIrrfPercent());

            $this->setValorDdir($this->getIr());

            $this->setQntDependenteIrrf(empty($this->getIrrfQntDependentes()) ? 0 : $this->getIrrfQntDependentes());

            $this->setParcelaDeducaoIrrf(empty($this->getIrrfQntParcelaDeducao()) ? 0 : $this->getIrrfQntParcelaDeducao());      

            $this->setParcelaDeducaoDependenteIrrf(empty($this->getIrrfValorDeducaoDependente()) ? 0 : $this->getIrrfValorDeducaoDependente());      
            
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
     * @name setCalcPensaoAlimenticia
     * 
     * @internal - Executa o calculo da Pens�o Aliment�cia
     * 
     */    
    public function setCalcPensaoAlimenticia(){
        
        try {
            
            if(!$this->chkIsCalc('setCalcPensaoAlimenticia')->isOk()) $this->error->set("O M�todo rh->Ferias->setCalcPensaoAlimenticia n�o pode ser executado por falta de par�metros",E_FRAMEWORK_ERROR);

            if(!is_object($this->getSuperClass()->Folha)) $this->error->set("M�todo rh->Ferias->setCalcPensaoAlimenticia n�o pode ser executado porque a classe rh->Folha n�o est� inst�nciada",E_FRAMEWORK_ERROR);

            /*
             * Define esses valores para a consulta ser realizada baseada nas folha do per�odo
             */
            $this->getSuperClass()->Folha->setDateRangeFmt('Ym');
            $this->getSuperClass()->Folha->setDateRangeIni($this->getDataAquisitivoIni()->val());
            $this->getSuperClass()->Folha->setDateRangeFim($this->getDataAquisitivoFim()->val());        
            $this->getSuperClass()->Folha->setDateRangeField("CONCAT(f.ano,LPAD(f.mes,2,'00'))");

            $this->getSuperClass()->Folha->getTotalIdsMovimentosEstatisticas();

            /*
             * Calcula o valor da pens�o no per�odo pre-determinado
             */
            $this->setPensaoAlimenticia($this->getSuperClass()->MovimentosClt->getCalcValorPensao());
            
            if($this->getSuperClass()->Clt->getPensaoAlimenticia() && !$this->getPensaoAlimenticia()) $this->error->set('O sistema identificou que o Clt tem pens�o marcada no cadastro mas n�o possui nenhum lan�amento',E_FRAMEWORK_NOTICE);

            if(!$this->getSuperClass()->Clt->getPensaoAlimenticia() && $this->getPensaoAlimenticia()) $this->error->set('O sistema identificou que o Clt n�o tem pens�o marcada no cadastro mas possui lan�amento',E_FRAMEWORK_NOTICE);

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
     * @name setCalcDiasEmDoisMeses
     * 
     * @internal - Calcula o n�mero de dias distribu�dos em dois mes�s quando houver 
     * 
     */    
    public function setCalcDiasEmDoisMeses(){
        
        try {

            if($this->getDataIni('m')->val()!=$this->getDataFim('m')->val()){

                $this->setDiasFerias1($this->getDataIni()->daysInMonth()->val() - $this->getDataIni('d')->val()+1);

                $this->setDiasFerias2($this->getDiasFerias() - $this->getDiasFerias1());

            }
            else {

                $this->setDiasFerias1($this->getDiasFerias());

                $this->setDiasFerias2(0);

            }   
            
            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->error->set("A exce��o no calculo de dias distribu�dos em dois meses impediu a finaliza��o do processo",E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
            
        }
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcValoresDistribuidosEmDoisMeses
     * 
     * @internal - Obtem o c�lculo dos valores proporcionais a cada m�s, quando as f�rias ser�o gozadas entre duas datas de meses diferentes
     *           e retorna um vetor.
     * 
     *           Adicionei a condi��o de verifica��o de exist�ncia de dias no segundo m�s para evitar levar lixo para os registros em quest�o
     * 
     */    
    public function setCalcValoresDistribuidosEmDoisMeses(){
        
        try {

            /*
             * Calcula o n�mero de dias divididos em dois meses
             */
            $this->setCalcDiasEmDoisMeses();

            /*
             * Defino o valor total apenas das f�rias proprocional aos dias de cada m�s 
             */
//            $this->setValorTotalFerias1(round($this->getValorDiasFerias() * $this->getDiasFerias1(),2));
            
            $this->setValorTotalFerias1(round(($this->getValorTotalFerias()/$this->getDiasFerias())*$this->getDiasFerias1(),2));            

            $this->setValorTotalFerias2($this->getDiasFerias2() ? round(($this->getValorTotalFerias()/$this->getDiasFerias())*$this->getDiasFerias2(),2) : 0);

            $this->setAcrescimoConstitucional1(round(($this->getUmTerco()/$this->getDiasFerias())*$this->getDiasFerias1(),2));

            $this->setAcrescimoConstitucional2($this->getDiasFerias2() ? round($this->getUmterco() - $this->getAcrescimoConstitucional1(),2) : 0);
            
//            echo $this->getValorTotalFerias1().'<br>';
//            echo $this->getAcrescimoConstitucional1().'<br>';
//            echo $this->getValorTotalFeriasDobradas().'<br>'; 
//            echo $this->getUmTercoFeriasDobradas().'<br>';
//            echo $this->getAbonoPecuniario().'<br>'; 
//            echo $this->getUmTercoAbonoPecuniario().'<br>';
//            echo $this->getTotalMovimentosCredito().'<br>';
//            exit();
            
            
            $this->setTotalRemuneracoes1(round($this->getValorTotalFerias1() + $this->getAcrescimoConstitucional1() + $this->getValorTotalFeriasDobradas() + $this->getUmTercoFeriasDobradas() + $this->getAbonoPecuniario() + $this->getUmTercoAbonoPecuniario()+$this->getTotalMovimentosCredito(),2));

            $this->setTotalRemuneracoes2($this->getDiasFerias2() ?  round($this->getValorTotalFerias2() + $this->getAcrescimoConstitucional2(),2) : 0);

            $this->setTotalLiquido1(round($this->getTotalRemuneracoes1() - $this->getTotalDescontos(),2));

            $this->setTotalLiquido2($this->getDiasFerias2() ? round($this->getValorTotalFerias2() + $this->getAcrescimoConstitucional2(),2) : 0);
            
            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->error->set('A exce��o na distribui��o de valores em dois meses impediu a finaliza��o do processo',E_FRAMEWORK_WARNING,$ex);                

            $this->setValue(0);
            
        }
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Obtem o valor total das f�rias para base de calculo do INSS / FGTS
     */
    public function getSalarioFeriasBaseDeCalcInssFgts(){
        
        return $this->getRemuneracaoBaseFeriasDobradas();
        
    }  
    
    /*
     * PHP-DOC
     * 
     * @name chkPodeTirarFerias
     * 
     * @internal - Verifica se o Clt est� em evento e pode ter f�rias lan�adas para o mesmo.
     *             Importante implementar aqui uma verifica��o de consist�ncia entre o status do clt e o lan�amento em eventos
     */   
    public function chkPodeTirarFerias(){
        
        try {
            
            switch ($this->getSuperClass()->Clt->getStatusRealTime()) {
                case 10:
                    $this->setValue(1);

                    break;
                
                default:
                    
                    $this->getSuperClass()->Clt->setStatus($this->getSuperClass()->Clt->getStatusRealTime());
                    
                    if($this->getSuperClass()->Clt->getStatus()) $this->getSuperClass()->Status->select()->getRow();
                    
                    $this->error->set(empty($this->getSuperClass()->Clt->getStatus()) ? "Ops!!! Algo deu errado aqui, nenhum registro retornado para essa opera��o. Verifique a regi�o selecionada!" : "N�o � poss�vel lan�ar f�rias para funcion�rio com evento ({$this->getSuperClass()->Status->getEspecifica()})",E_FRAMEWORK_ERROR);

                    $this->setValue(0);

                    break;
            }
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name getMakeItens
     * 
     * @internal - Constroi um vetor com valores a serem lan�ados na tabela de itens
     *             26/01/2016 - Adicionado verifica��o de dias de f�rias do segundo m�s e valor dos campos para inser��o dos registros
     *                          Adicionado lan�amento de faltas
     */   
    public function getMakeItens(){
        
        /*
         * Monta um array atrav�z dos valores de colunas da classe na seguinte formata��o
         * 
         * 1: �ndice de legenda
         * 2: Quantidade 
         * 3: Valor
         * 4: Incide IR
         * 5: Incide INSS
         * 6: Incide FGTS
         */
        
        $itens = array(); // Inicializa o array para evitar acumular valor
        
        /*
         * Total F�rias Proporcionais (Incide IR, INSS e FGTS) 
         * 
         * Obs: getValorTotalFerias = $this->getSalario() + $this->getSalarioVariavel() + $this->getInsalubridadePericulosidade()) / 30) * $this->getDiasFerias()
         */
        if((int)$this->getValorTotalFerias1()) $itens[] = array(1,0,$this->getValorTotalFerias1(),1,1,1); 
        
        if($this->getDiasFerias2()) $itens[] = array(2,0,$this->getValorTotalFerias2(),1,1,1); 
        
        /*
         * Total de dias de F�rias (N�o � valor monet�rio) 
         */
        if($this->getDiasFerias1()) $itens[] = array(3,$this->getDiasFerias1(),0,0,0,0);

        if($this->getDiasFerias2())$itens[] = array(4,$this->getDiasFerias2(),0,0,0,0);
        
        /*
         * Total de Remunera��es Proporcionais (N�o Incide IR, INSS e FGTS) 
         */
        if((int)$this->getTotalRemuneracoes1()) $itens[] = array(5,0,$this->getTotalRemuneracoes1(),0,0,0);
        
        if($this->getDiasFerias2()) $itens[] = array(6,0,$this->getTotalRemuneracoes2(),0,0,0); 

        /*
         * 1/3 sobre Abono Pecuni�rio (N�o Incide IR, INSS e FGTS) 
         */
        if((int)$this->getUmTercoAbonoPecuniario()) $itens[] = array(7,0,$this->getUmTercoAbonoPecuniario(),0,0,0);
        
        /*
         * Caso haja Pens�o Aliment�cia Insere o Valor (N�o Incide IR, INSS e FGTS) 
         */
        if((int)$this->getPensaoAlimenticia()) $itens[] = array(8,0,$this->getPensaoAlimenticia(),0,0,0);
        
        /*
         * Total do Acr�scimo constitucional 1/3 (Incide IR, INSS e FGTS) 
         */
        if((int)$this->getAcrescimoConstitucional1()) $itens[] = array(9,0,$this->getAcrescimoConstitucional1(),1,1,1);
        
        if($this->getDiasFerias2()) $itens[] = array(10,0,$this->getAcrescimoConstitucional2(),1,1,1); 
        
        /*
         * Total de Faltas computadas no per�odo aquisitivo
         */
        if($this->getFaltas()) $itens[] = array(11,$this->getFaltas(),0,1,1,1);    
        
        
        /*
         * Total referentes ao calculo do IRRF (Quantidade de dependentes e parcela por dependente)
         */

        if((int)$this->getQntDependenteIrrf()) $itens[] = array(12,$this->getQntDependenteIrrf(),0,1,1,1);
        
        if((int)$this->getParcelaDeducaoDependenteIrrf()) $itens[] = array(13,0,$this->getParcelaDeducaoDependenteIrrf(),1,1,1);
        
        return $itens;
        
        
    }

    /*
     * PHP-DOC - M�todo para calculo das f�rias
     * 
     * Regra de calculo de f�rias
     * 
     * Consultor de RH: Rog�rio Jesus de Assis
     * 
     * Inf. Adicionais http://www.infomoney.com.br/imoveis/noticia/17448/aprenda-como-calcular-quanto-vai-receber-eacute-rias
     * 
     * Ferias = (UltSalario + (SomaDasMediasVari�veisDoPeriodoAquisitivo) /12) + 1/3
     * 
     * rh_clt desconto_inss (se tem desconto em outra empresa)
     * 
     * Obs: 
     *  1. N�o existem excess�es no somat�rio dos cr�ditos de sal�rio do Clt para fim de calculo de f�rias.
     *  2. SomaDasMediasVari�veisDoPeriodoAquisitivo n�o inclui o d�cimo terceiro sal�rio
     *  3. No caso de f�rias dobradas, pegasse o ultimo Sal�rio + Sal�rio Vari�vel no Per�odo aquisitivo + 1/3 e multiplica-se por 12
     *  4. Verificando Abono Pecuni�rio (Venda de Dias) Venda das f�rias apenas sobre 1/3 do per�odo (Arredondamento sempre para cima a favor do funcion�rio)
     *  5. INSS/FGTS/IRRF incide sobre o valor total das f�rias
     *  6. Pens�o Aliment�cia � aplicada sobre o sal�rio L�quido (Sal�rio - (INSS+IRRF))*Percentual de Pens�o
     *  7. Caso o funcion�rio trabalhe em outra empresa e pague o teto do INSS (R$ 513.01) ent�o n�o dever� ser descontato o INSS caso contr�rio o desconto e proporcional
     * 
     */
    public function setCalcFerias(){
        
        try {
            
            /*
             * Executa uma verifica��o do status do Clt para analisar se o mesmo n�o encontra-se em evento que o impessa de tirar f�rias
             */
            if(!$this->chkPodeTirarFerias()->isOk()) $this->error->set("N�o foi poss�vel verificar a disponibilidade do funcion�rio para tirar f�rias",E_FRAMEWORK_ERROR);

            /*
             * Contabiliza o n�mero de faltas no per�odo 
             */
            if(!$this->setCalcFaltas()->isOk()) $this->error->set("N�o foi poss�vel verificar se o funcion�rio possui faltas",E_FRAMEWORK_ERROR);

            /*
             * Executa o calculo para encontrar a data final do per�odo de gozo a partir da data in�cio e o desconto proporcional das faltas
             */
            if(!$this->setCalcDataFim()->isOk()) $this->error->set("N�o foi poss�vel calcular a data final do per�odo de gozo",E_FRAMEWORK_ERROR);

            /*
             * Executa o calculo para setar o valor do sal�rio base de calculo
             */
            if(!$this->setCalcSalarioVariavelInsalubridadePericulosidade()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor do sal�rio vari�vel, insalubridade ou periculosidade",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o calculo para setar o valor do sal�rio base de calculo
             */
            if(!$this->setCalcRemuneracaoBase()->isOk()) $this->error->set("N�o foi poss�vel calcular a remunera��o base do funcion�rio",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o calculo de movimentos referentes a f�rias
             */
            if(!$this->setCalcMovimentosClt()->isOk()) $this->error->set("N�o foi poss�vel calcular os movimentos do funcion�rio",E_FRAMEWORK_ERROR);
            
            /*
             * Define o valor da remunera��o total das f�rias com ou sem abono pecuni�rio
             */
            if(!$this->setCalcValorTotalFerias()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor total das f�rias",E_FRAMEWORK_ERROR);

            /*
             * Define o valor da remunera��o total das f�rias dobradas
             */
            if(!$this->setCalcValorTotalFeriasDobradas()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor total das f�rias dobradas",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o calculo para encontrar valores e fatores do INSS,FGTS e IRRF
             */
            if(!$this->setCalcInssFgtsIrrf()->isOk()) $this->error->set("N�o foi poss�vel calcular os valores de INSS, FGTS e IRRF",E_FRAMEWORK_ERROR);

            /*
             * Executa o calculo de pens�o aliment�cia
             * 
             * Ficou pendente a pedido do Ramon a implementa��o do valor da pens�o
             * 
             */
            if(!$this->setCalcPensaoAlimenticia()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor da pens�o aliment�cia",E_FRAMEWORK_ERROR);
            
            /*
             * Executa a verifica��o de f�rias coletivas
             * 
             * ATEN��O - Implementa��o pendente
             * 
             * ATEN��O ATEN��O ATEN��O ATEN��O ATEN��O
             */

            /*
             * Define o valor do abono pecuni�rio
             */
            if(!$this->setCalcAbonoPecuniario()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor do abono pecuni�rio",E_FRAMEWORK_ERROR);

            /*
             * Define o valor de um ter�o do abono pecuni�rio
             */
            if(!$this->setCalcUmTercoAbonoPecuniario()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor de um ter�o do abono pecuni�rio",E_FRAMEWORK_ERROR);

            /*
             * Define o valor total dos rendimentos
             */
            if(!$this->setCalcTotalRendimentos()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor da pens�o aliment�cia",E_FRAMEWORK_ERROR);

            /*
             * Calcula o valor total de descontos e seta a vari�vel
             */
            
            if(!$this->setCalcTotalDescontos()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor total dos descontos",E_FRAMEWORK_ERROR);
            
            /*
             * Calcula o valor total l�quido a receber que � o valor total de rendimentos menos o valor total de descontos
             */
            if(!$this->setCalcTotalLiquido()->isOk()) $this->error->set("N�o foi poss�vel calcular o valor total dos descontos",E_FRAMEWORK_ERROR);    

            /*
             * Calcula os valores proporcionais para cada m�s em que o per�odo de gozo � realizado entre dois meses
             */
            if(!$this->setCalcValoresDistribuidosEmDoisMeses()->isOk()) $this->error->set("N�o foi poss�vel distribuir os valores de f�rias em dois meses",E_FRAMEWORK_ERROR);  

            /*
             * Verifica se houve instru��o para ignorar f�rias do
             */
            if($this->getIgnorarFaltas()) $this->error->set("Instru��o para ignorar faltas definida pelo usu�rio",E_FRAMEWORK_NOTICE);  
            
            /*
             * Verifica se houve instru��o para ignorar f�rias dobradas pelo usu�rio e emite um aviso
             */
            if($this->getIgnorarFeriasDobradas()) $this->error->set("Instru��o para ignorar f�rias dobradas definida pelo usu�rio",E_FRAMEWORK_NOTICE);  
            
            
            $this->setValue(1);
            
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;
        
    }    

    
}
