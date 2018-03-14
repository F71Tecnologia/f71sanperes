<?php 
/*
 * PHP-DOC - RhFeriasClass.php 
 * 
 * Classe para criação de camada de compatibilidade retroativa na operacionalização das férias 
 *
 * 10-09-2015
 * 
 * @package RhFeriasClass  
 * @access public   
 * 
 * @version
 * 
 * Versão: 3.0.4385 - 24/11/2015 - Jacques - Versão Inicial
 * Versão: 3.0.4823 - 10/09/2015 - Jacques - Adicionado o calculo do desconto parcial do INSS pois estava só estava pelo total $this->setInssValor($teto_desconto_inss - $this->getSuperClass()->Clt->getValorDescontoInss());
 * Versão: 3.0.4902 - 14/12/2015 - Jacques - Retirado a condição de seleção por projetos no método getPeriodosGozados pois pode haver caso de transferència de projetos então o clt mantem o período aquisitivo
 * Versão: 3.0.5483 - 12/12/2016 - Jacques - Bug na operação com o vetor $periodoAquivisitoPendente[$key]['data_aquisitivo_ini'] onde o mesmo setava equivocadamente a classe setDataAquisitivoIni gerando erro quando havia mais de um período aquisitivo
 * Versão: 3.0.5614 - 18/01/2016 - Jacques - Conforme reunião no mesmo dia dessa anotação com Gimenez e Leonardo o valor base para fins de férias não deverá mais somar o terço constitucional
 *                                           Também não deverá mais ser discriminado o valor do dia no período de gozo, assim como a diferença do segundo mês do mesmo,
 *                                           deverá ser calculada pela diferença do primeiro e não mais proporcional a valor dia a fim de avitar diferença nos arredondamentos.
 *                                           Obs.: A nova forma de definir a divisão dos valores gerou uma diferença de R$4,80 para para o id_ferias 5183. Ou seja
 *                                           O que antes era 0,16 centavos agora são 3,80.
 *                                           Os códigos 5,6 respectivamente de total de remunerações na montagem de rh_ferias_itens estáva se repetindo nas chaves de acréscimo constitucional
 *                                           em virtude da tabela de legendas perdias e recadastras na lagos
 * Versão: 3.0.5883 - 26/01/2016 - Jacques - Adicionei a condição de verificação de existência de dias no segundo mês para evitar levar lixo para os registros em questão
 *                                           Adicionado verificação de dias de férias do segundo mês e valor dos campos para inserção dos registros
 * Versão: 3.0.5883 - 01/02/2016 - Jacques - Adicionado o armazenamento e processamento de valor por dependente atravéz do método setIrrfValorDeducaoDependente e setParcelaDeducaoIrrf. Corrigido também o valor passado para o método $calculos->MostraIRRF que recebia o id da região ao invez do projeto
 * Versão: 3.0.8998 - 20/04/2016 - Jacques - Adicionada opção de ignorar férias dobradas e a opção de retorno de evento não geralas quando as férias começar imediatamente um dia apôs do evento
 * Versão: 3.0.9071 - 29/04/2016 - Jacques - Adicionada opção de lançamentos de médias fixas no salário variável como também o processamento de itens de movimento
 * 
 * Adicionei a condição de verificação de existência de dias no segundo mês para evitar levar lixo para os registros em questão
 * 
 * @todo 
 * 
 * OBS: em 02/02/2016 Sinésio me pediu para fazer uma verificação em todas as rotinas que usam a classe calculos pois não estava sendo computado os 
 *      dependentes na rotinas de folha, férias e rescisão. Esse erro é crítico já que a única classe que é usada pela nova rotina de férias vem da
 *      classe calculos que está no arquivo classes\calculos.php
 * 
 * ATENÇÃO: 1. Incluir o processamento favorecido_pensao_assoc para computar pensão no calculo e processamento das férias
 *          2. Na hora de listar o período aquisitivo deverá ser verificado quantos dias de eventos teve no período
 * 
 * Instruções de Ramon:
 *          3. ok - Possibilidade de opção de não lançar faltas quando houver.
 *          4. ok - Fixar Title resumo de período em duas linhas e duas colunas e tirar de dentro do panel
 *          5. ok - Aumentar largura do modal.
 *          6. ok - Destaca onde começa os cálculos separando o que é soma, subtração de forma bem visível
 *          7. ok - Lupa para média de salário variável ao lado do R$.
 *          8. ok - Espaçar valor e moeda.
 *          9. ok - Lançar os valores em ambas as tabelas (rh_ferias) e (rh_ferias_itens)
 *         10. ok Adição da revisão no footer
 * 
 * Sugestões de Sinésio:
 *         11. ok - Adicionar na listagem de salário variável um agrupamento por mes e tipo de remuneração com totalizador geral
 *         12. ok - Adicionar o número de dependentes ou não para o calculo do IR
 *         13. ok - Definir procedimento final para calculo do valor de insalubridade e periculosidade
 *         13. Ao verificar o status do Clt e na tabela de eventos, detectar inconsistência, então atualizar o status do Clt
 * 
 * 
 *         14. Fazer a contabilização das faltas incluindo as horas trabalhadas baseadas na função 
 *             (No caso de médico soma as horas de faltas e divide por 36 e arredonda para baixo).
 *         
 * Minhas pendências:
 *         1.  ok - Necessário criar um método para verificar o status do clt e se o mesmo encontra-se em evento no sistema, fazendo uma consistência de 
 *             ambas informações.
 *         2.  ok - Quando o funcionário estiver em evento e seu retorno ultrapassar o período concessivo, deverá existir uma flag para ignorar as férias 
 *             em dobro com log para informar essa operação. 
 *         3.  Implementar limpeza de buffer para evitar erro no uso de print em debug para uso de AJAX com JSON.
 *         4.  Implementar a possibilidade de levar em consideração funcionários que sofreram transferências de projetos em 'SELECT * FROM rh_transferencias WHERE id_clt = @clt' para uso do método getPeriodosGozados
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
     * Calcula a data de fim das férias baseadas no início, se tem abono ou não e sobre o total de concessão menos as faltas caso haja.
     */
    public function setCalcDataFim(){
        
        try {
            
            $qnt_dias = $this->getCalcLimiteDiasFeriasPorFalta();

            $qnt_dias_abono = $this->getVendido() ? $this->getCalcDiasAbonoPecuniario() : 0;

            $qnt_dias_ferias = $qnt_dias - $qnt_dias_abono;    

            /*
             * Define o número de dias de férias
             */
            $this->setDiasFerias($qnt_dias_ferias);

            /*
             * Define o número de dias de abono se houver
             */
            $this->setDiasAbonoPecuniario($qnt_dias_abono);            

            /*
             * Soma o número de dias a data de início para encontrar a data final
             */
            $this->setDataFim($this->getDataIni()->sumDays($this->getDiasFerias())->val());

            /*
             * Subtrai um dia da data fim, pois a soma de dias é incluvie a data de início
             */
            $this->setDataFim($this->getDataFim()->minusDays()->val());

            /*
             * Soma mais um dia a data de fim das férias para encontrar a data de retorno
             */
            $this->setDataRetorno($this->getDataFim()->sumDays()->val());


            /*
             * Define o mês e o ano do período de gozo em relação a data de início do gozo
             */
            $this->setMes($this->getDataIni('m')->val());
            $this->setAno($this->getDataIni('Y')->val());

            /*
             * Define o mês de férias
             */
            $this->setMesDt('');

            /*
             * Define o mês de férias
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
     * PHP-DOC - Encontra o salário base de calculo das férias
     */
    public function setCalcRemuneracaoBase(){
        
        try {
            
            /*
             * Define o valor inicial da remuneração de base de calculo
             */
            $this->setRemuneracaoBase($this->getSalario() + $this->getSalarioVariavel() + $this->getInsalubridadePericulosidade());

            /*
             * Define a remuneração base das férias dobradas
             */
            $this->setRemuneracaoBaseFeriasDobradas($this->chkFeriasDobradas()->isOk() ? $this->getSalario() + $this->getSalarioVariavel() : 0); 
            
            /*
             * Define o valor de um salário adicional lançável em movimentos
             */
            $this->setSalarioExtra($this->getCalcSalarioExtra()); 

            /*
             * Define o valor do salário base por dia
             */
            $this->setCalcRemuneracaoBasePorDia();
            
            /*
             * Define o valor do salário base por dia das férias dobradas
             */
            $this->setCalcRemuneracaoBasePorDiaFeriasDobradas();

            /*
             * Define o valor de um terço das férias
             */
            $this->setCalcUmTerco();      

            /*
             * Define o valor de um terço das férias dobradas
             */
            $this->setCalcUmTercoFeriasDobradas();      

            /*
             * Adiciono um terço a remuneração base de cálculo
             */        
            $this->setRemuneracaoBase($this->getRemuneracaoBase() + $this->getUmTerco());

            /*
             * Adiciono um terço a remuneração base de cálculo de férias dobradas
             */        
            $this->setRemuneracaoBaseFeriasDobradas($this->getRemuneracaoBaseFeriasDobradas() + $this->getUmTercoFeriasDobradas());

            /*
             * Define a flag de férias dobradas caso exista
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
     * @internal - Calcula caso haja o valor do salário lançável
     * 
     */       
    public function getCalcSalarioExtra(){
        
        $this->getSuperClass()->MovimentosClt->setDefault();
        $this->getSuperClass()->MovimentosClt->setIdClt($this->getSuperClass()->Clt->getIdClt()); 
        $this->getSuperClass()->MovimentosClt->setMesMov(17); // 17 = Lançamento de férias
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
     * @internal - Método para calcular e setar o número de faltas no período
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
             * Verifica se a classe Curso está instânciada e então obtem o salário do Clt na classe Curso
             */
            if(!is_object($this->getSuperClass()->Curso) || !is_object($this->getSuperClass()->Folha) || !is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            $this->getSuperClass()->Curso->select()->getRow();

            if(empty($this->getSuperClass()->Curso->getSalario())) {

                $this->error->set("Não existe valor de salário definido em curso",E_FRAMEWORK_ERROR);

            }
            else {

                $this->setSalario(round($this->getSuperClass()->Curso->getSalario(),2));

            }

            $this->getSuperClass()->Folha->setDateRangeIni($this->getDataAquisitivoIni());
            $this->getSuperClass()->Folha->setDateRangeEnd($this->getDataAquisitivoFim());

            /*
             * Define o resultado da média do salário variável no período menos os lançamentos de insalubridade e periculosidade
             */
            $this->setSalarioVariavel($this->getSuperClass()->Folha->getCalcMediaSalarioVariavel());  

            /*
             * Define o resultado da média do salário variável no período menos os lançamentos de insalubridade e periculosidade
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
     * PHP-DOC - define o salário das férias sem abono pecuniário. O valor total das férias nada mais é que:
     *           (Salário Base + Médias de Salário Variável + Insalubridade/Periculosidade) * dias de férias
     */
    public function setCalcValorTotalFerias(){
        
        try {

            $this->chkIsCalc('setCalcValorTotalFerias');
            
            /*
             * Define o valor total das férias
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
     * PHP-DOC - define o salário das férias dobradas sem abono pecuniário. O valor total das férias nada mais é que:
     *           (Salário Base + Médias de Salário Variável) * 2
     */
    public function setCalcValorTotalFeriasDobradas(){
        
        try {

            $this->chkIsCalc('setCalcValorTotalFerias');
            
            /*
             * Define o valor total das férias dobradas
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
     * @internal - Adiciona movimentos de CRÉDITO ou DÉBITO nas férias que são lançados fora da folha de pagamento
     * 
     */       
    public function setCalcMovimentosClt(){
        
        try {
            
            $this->setTotalMovimentosCredito(0);
            $this->setTotalMovimentosDebito(0);
            
            $this->getSuperClass()->MovimentosClt->setDefault();
            $this->getSuperClass()->MovimentosClt->setIdClt($this->getSuperClass()->Clt->getIdClt()); 
            $this->getSuperClass()->MovimentosClt->setMesMov(17); // 17 = Lançamento de férias
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
                            $this->error->set('Movimento sem definição de CRÉDITO ou DÉBITO',E_FRAMEWORK_ERROR);
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
     * PHP-DOC - Define o valor do salário base de calculo das férias por dia
     */
    public function setCalcRemuneracaoBasePorDia(){
        
        $this->setRemuneracaoBaseDia($this->getRemuneracaoBase()/30); 
        
        $this->setValue(1);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Define o valor do salário base de calculo das férias dobradas por dia
     */
    public function setCalcRemuneracaoBasePorDiaFeriasDobradas(){
        
        $this->setRemuneracaoBaseDiaFeriasDobradas($this->getRemuneracaoBaseFeriasDobradas()/30); 
        
        $this->setValue(1);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Obtem um terço das férias referente ao valor base de calculo 
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
     * PHP-DOC - Obtem o valor total líquido referente ao total_remuneracoes - total_descontos
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
     * PHP-DOC - Obtem uma coleção de movimentos
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
     * @internal - Método de seleção padrão de registros da classe
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

            if(empty($id_regiao) && empty($id_projeto) && empty($id_clt) && empty($id_ferias)) $this->error->set('Nenhum parámetro definido para o método select da classe RhFeriasClass',E_FRAMEWORK_ERROR);

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
     * @internal - Método extendido da classe dinâmica para carregar propriedades extendidaas do método select
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
     * @internal - Esse método retorna um array com os períodos de férias já gozados
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
             * Caso o número de dias gozados seja inferior a 20 dias, então é porque as férias
             * estão sendo parceladas conforme utilizado no Iabas
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
     * @internal - param type $tipo = serve para determinar uma janela de um ano a mais no período
     *              
     */    
    public function getPeriodoAquisitivoPendente($tipo = 1){
        
        try {
            
            $novo_periodo_aquisitivo_final = array();

            if(!is_object($this->getSuperClass()->Folha)) $this->error->set('Não é possível verificar o período aquisitivo pendente sem a classe folha estár instânciada',E_FRAMEWORK_ERROR);

            if(!is_object($this->getSuperClass()->Eventos)) $this->error->set('Não é possível verificar o período aquisitivo pendente sem a classe eventos estár instânciada',E_FRAMEWORK_ERROR);
            
            $ano = 0; $mes = 1; $dia = 2;

            $array_total_periodos = array();
            
            /*
             * Monta a listagem dos períodos aquisitivos a partir da data de admissão
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
             * O erro está nesse método
             */
            foreach ($periodoAquivisitoPendente as $key => $value) {


                $this->getSuperClass()->Clt->setDateRangeIni($periodoAquivisitoPendente[$key]['data_aquisitivo_ini']);
                $this->getSuperClass()->Clt->setDateRangeFim($periodoAquivisitoPendente[$key]['data_aquisitivo_fim']);

                $faltas = $this->getSuperClass()->Clt->getCalcFaltasNoPeriodo();

                $novo_periodo_aquisitivo = $this->getSuperClass()->Eventos->getNovaDataEventosComMaisDe180Dias();  

                if($novo_periodo_aquisitivo[$key]['soma_eventos_mais_180'] > 0) {

                    /*
                     * Pega o novo período aquisitivo em função dos novos eventos com 180 dias ou mais
                     */

                    $novo_periodo_aquisitivo_final[] = array(
                                                            'data_aquisitivo_ini' => $novo_periodo_aquisitivo[$key]['data_aquisitivo_ini'],
                                                            'data_aquisitivo_fim' => $novo_periodo_aquisitivo[$key]['data_aquisitivo_fim'],
                                                            'soma_eventos_mais_180' => $novo_periodo_aquisitivo[$key]['soma_eventos_mais_180'],
                                                            'faltas' => $faltas
                                                            );

                    $nova_data_admissao = explode('-',$novo_periodo_aquisitivo[$key]['data_aquisitivo_fim']);

                    /*
                     * Reconstrou a lista de períodos aquisitivos pendentes em função da soma dos eventos no período for igual ou maior que 180 dias
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
                     * Casa não haja evento no perído com 180 dias ou mais mantem os mesmos períodos aquisitivos pendentes
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
     * @internal - Executa a atualização de registros da tabela rh_ferias
     *             Essa atualização está sendo feita para correção dos valores distribuídos em dois meses
     *              
     */    
    public function update(){
        
        $id_ferias = $this->getIdFerias();

        $id_clt = $this->getIdClt();

        if(empty($this->rh_ferias_save)) $this->error->set('O vetor $this->rh_ferias_save está vazio, isso gerou uma exceção no método rh->Ferias->update() que impede sua finalização',E_FRAMEWORK_ERROR);

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
     * @internal - Executa a inserção de registros da tabela rh_ferias
     *              
     */    
    public function insert(){
        
        if(!is_object($this->getSuperClass()->FeriasItens)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

        if(empty($this->rh_ferias_save)) $this->error->set(array(7,__METHOD__),E_FRAMEWORK_ERROR);

        /*
         * Verifica se já houve processamento dessas férias para evitar inconsistência de lançamento
         */
        if($this->chkJaFoiProcessado()->isOk()) {
            
            $this->error->set("Esse período de férias já foi processado",E_FRAMEWORK_NOTICE);
            
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
     * @internal - Inclui os itens de férias
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
     * @internal - Verifica se os valores primários estão setados para calculo das férias
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

                    Não é possível executar o método $method() sem definir alguns valores da classe Férias:

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
     * @internal - Ferifica se já houve processamento do período referente ao período aquisitivo definido na classe
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
     * @internal - Método para calcular o número de dias disponíveis para gozar as férias proporcional as faltas
     *              
     */    
    public function getCalcLimiteDiasFeriasPorFalta() {
        
        try {
            
            $qnt_dias = $this->getQuantidadeDias();
            
            $this->chkIsCalc('getCalcLimiteDiasFeriasPorFalta');

            if(!is_object($this->getSuperClass()->MovimentosClt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            /*
             * Seta o range na folha pois é ele que deverá determinar os IDs de movimento no período
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
             * Caso as férias sejam dadas em duas etapas, por enquanto se ignora as faltas
             */
            $qnt_dias = $this->getMetadeFerias() ? 15 : $qnt_dias;
            
            $this->setValue(1);

            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
            
        }
        
        /*
         * Caso a propriedade setIgnorarFaltas esteja setada com 1, as mesmas não serão computadas
         */
        
        return $qnt_dias;

    }
    
    /*
     * PHP-DOC 
     * 
     * @name chkFeriasDobradas
     * 
     * @internal - No caso a data limite pela lei é de 30 dias de antecedência para o final do período aquisitivo
     *             Também existe o caso em que o CLT pode ter saído de evento e em virtude disso ter acabado de passar do prazo consessivo
     * 
     * Art 137 CLT: O empregador deverá conceder imediatamente as férias ao empregado assim que ele retornar ao trabalho, porém sem o pagamento 
     *              da multa prevista no artigo 137 da CLT. Isto porque, o atraso da concessão não foi de responsabilidade do empregador, pois o 
     *              período do auxílio-doença é incompatível com a concessão das férias uma vez que o contrato de trabalho tem efeito suspensivo 
     *              decorrente do afastamento do empregado por auxílio-doença.
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
             * Se o início do evento for menor que o concessivo e o seu final do evento menor que o concessivo então o Clt teve evento dentro do período concessivo. O que permite conceder as férias
             * No dia seguinte ao final do evento. No caso aqui está definido com um prazo de até um mês por orientação da Gimenez, mas isso vai ser mudado
             * para a solução que define o art 137 da CLT.
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
     * @internal - Calcula os dias de férias a serem gozadas com o desconto dos dias de abono pecuniário
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
     * @internal - Calcula os dias de férias a serem gozadas sem o desconto dos dias de abono pecuniário
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
     * @internal - Calcula os dias de abono pecuniário caso haja em função dos dias máximos de concessão
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
     * @internal - Obtem o salário base de calculo das férias
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
     * @internal - Obtem o valor total das férias dobradas
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
     * @internal - Obtem o salário de um terço de férias dobradas
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
     * @internal - Obtem o sub total líquido a receber das férias sem o desconto de pensão alimentícia
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
     * @internal - Obtem o valor do abono pecuniário
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
     * @internal - Obtem o valor de um terço do abono pecuniário
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
     * @internal - Cálculo de remuneracões de férias 
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
     * @internal - Obtem o valor total de descontos das férias
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
             * A data usada para calculo de INSS e IRRF é feita de acordo com o ano de processamento das férias.
             * Essa condição para quando o período de férias for no ano seguinte, calcular como ano vigente.
             * 
             * Informações retiradas da rotina antiga de calculo e confirmadas pelo consultor Rogério
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
                 * Caso já possua desconto de INSS em outra empresa, verifica se é parcial ou total
                 * e então faz o desconto proporcional de INSS sem ultrapassar o Teto na soma dos 
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
             * Caso haja férias dobradas então incide IR sobre o valor Total
             * 
             * Caso já haja desconto integral do INSS em outra empresa ou parcial
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
     * @internal - Executa o calculo da Pensão Alimentícia
     * 
     */    
    public function setCalcPensaoAlimenticia(){
        
        try {
            
            if(!$this->chkIsCalc('setCalcPensaoAlimenticia')->isOk()) $this->error->set("O Método rh->Ferias->setCalcPensaoAlimenticia não pode ser executado por falta de parámetros",E_FRAMEWORK_ERROR);

            if(!is_object($this->getSuperClass()->Folha)) $this->error->set("Método rh->Ferias->setCalcPensaoAlimenticia não pode ser executado porque a classe rh->Folha não está instânciada",E_FRAMEWORK_ERROR);

            /*
             * Define esses valores para a consulta ser realizada baseada nas folha do período
             */
            $this->getSuperClass()->Folha->setDateRangeFmt('Ym');
            $this->getSuperClass()->Folha->setDateRangeIni($this->getDataAquisitivoIni()->val());
            $this->getSuperClass()->Folha->setDateRangeFim($this->getDataAquisitivoFim()->val());        
            $this->getSuperClass()->Folha->setDateRangeField("CONCAT(f.ano,LPAD(f.mes,2,'00'))");

            $this->getSuperClass()->Folha->getTotalIdsMovimentosEstatisticas();

            /*
             * Calcula o valor da pensão no período pre-determinado
             */
            $this->setPensaoAlimenticia($this->getSuperClass()->MovimentosClt->getCalcValorPensao());
            
            if($this->getSuperClass()->Clt->getPensaoAlimenticia() && !$this->getPensaoAlimenticia()) $this->error->set('O sistema identificou que o Clt tem pensão marcada no cadastro mas não possui nenhum lançamento',E_FRAMEWORK_NOTICE);

            if(!$this->getSuperClass()->Clt->getPensaoAlimenticia() && $this->getPensaoAlimenticia()) $this->error->set('O sistema identificou que o Clt não tem pensão marcada no cadastro mas possui lançamento',E_FRAMEWORK_NOTICE);

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
     * @internal - Calcula o número de dias distribuídos em dois mesês quando houver 
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
            
            $this->error->set("A exceção no calculo de dias distribuídos em dois meses impediu a finalização do processo",E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
            
        }
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcValoresDistribuidosEmDoisMeses
     * 
     * @internal - Obtem o cálculo dos valores proporcionais a cada mês, quando as férias serão gozadas entre duas datas de meses diferentes
     *           e retorna um vetor.
     * 
     *           Adicionei a condição de verificação de existência de dias no segundo mês para evitar levar lixo para os registros em questão
     * 
     */    
    public function setCalcValoresDistribuidosEmDoisMeses(){
        
        try {

            /*
             * Calcula o número de dias divididos em dois meses
             */
            $this->setCalcDiasEmDoisMeses();

            /*
             * Defino o valor total apenas das férias proprocional aos dias de cada mês 
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
            
            $this->error->set('A exceção na distribuição de valores em dois meses impediu a finalização do processo',E_FRAMEWORK_WARNING,$ex);                

            $this->setValue(0);
            
        }
        
        return $this;
        
    }
    
    /*
     * PHP-DOC - Obtem o valor total das férias para base de calculo do INSS / FGTS
     */
    public function getSalarioFeriasBaseDeCalcInssFgts(){
        
        return $this->getRemuneracaoBaseFeriasDobradas();
        
    }  
    
    /*
     * PHP-DOC
     * 
     * @name chkPodeTirarFerias
     * 
     * @internal - Verifica se o Clt está em evento e pode ter férias lançadas para o mesmo.
     *             Importante implementar aqui uma verificação de consistência entre o status do clt e o lançamento em eventos
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
                    
                    $this->error->set(empty($this->getSuperClass()->Clt->getStatus()) ? "Ops!!! Algo deu errado aqui, nenhum registro retornado para essa operação. Verifique a região selecionada!" : "Não é possível lançar férias para funcionário com evento ({$this->getSuperClass()->Status->getEspecifica()})",E_FRAMEWORK_ERROR);

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
     * @internal - Constroi um vetor com valores a serem lançados na tabela de itens
     *             26/01/2016 - Adicionado verificação de dias de férias do segundo mês e valor dos campos para inserção dos registros
     *                          Adicionado lançamento de faltas
     */   
    public function getMakeItens(){
        
        /*
         * Monta um array atravéz dos valores de colunas da classe na seguinte formatação
         * 
         * 1: Índice de legenda
         * 2: Quantidade 
         * 3: Valor
         * 4: Incide IR
         * 5: Incide INSS
         * 6: Incide FGTS
         */
        
        $itens = array(); // Inicializa o array para evitar acumular valor
        
        /*
         * Total Férias Proporcionais (Incide IR, INSS e FGTS) 
         * 
         * Obs: getValorTotalFerias = $this->getSalario() + $this->getSalarioVariavel() + $this->getInsalubridadePericulosidade()) / 30) * $this->getDiasFerias()
         */
        if((int)$this->getValorTotalFerias1()) $itens[] = array(1,0,$this->getValorTotalFerias1(),1,1,1); 
        
        if($this->getDiasFerias2()) $itens[] = array(2,0,$this->getValorTotalFerias2(),1,1,1); 
        
        /*
         * Total de dias de Férias (Não é valor monetário) 
         */
        if($this->getDiasFerias1()) $itens[] = array(3,$this->getDiasFerias1(),0,0,0,0);

        if($this->getDiasFerias2())$itens[] = array(4,$this->getDiasFerias2(),0,0,0,0);
        
        /*
         * Total de Remunerações Proporcionais (Não Incide IR, INSS e FGTS) 
         */
        if((int)$this->getTotalRemuneracoes1()) $itens[] = array(5,0,$this->getTotalRemuneracoes1(),0,0,0);
        
        if($this->getDiasFerias2()) $itens[] = array(6,0,$this->getTotalRemuneracoes2(),0,0,0); 

        /*
         * 1/3 sobre Abono Pecuniário (Não Incide IR, INSS e FGTS) 
         */
        if((int)$this->getUmTercoAbonoPecuniario()) $itens[] = array(7,0,$this->getUmTercoAbonoPecuniario(),0,0,0);
        
        /*
         * Caso haja Pensão Alimentícia Insere o Valor (Não Incide IR, INSS e FGTS) 
         */
        if((int)$this->getPensaoAlimenticia()) $itens[] = array(8,0,$this->getPensaoAlimenticia(),0,0,0);
        
        /*
         * Total do Acréscimo constitucional 1/3 (Incide IR, INSS e FGTS) 
         */
        if((int)$this->getAcrescimoConstitucional1()) $itens[] = array(9,0,$this->getAcrescimoConstitucional1(),1,1,1);
        
        if($this->getDiasFerias2()) $itens[] = array(10,0,$this->getAcrescimoConstitucional2(),1,1,1); 
        
        /*
         * Total de Faltas computadas no período aquisitivo
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
     * PHP-DOC - Método para calculo das férias
     * 
     * Regra de calculo de férias
     * 
     * Consultor de RH: Rogério Jesus de Assis
     * 
     * Inf. Adicionais http://www.infomoney.com.br/imoveis/noticia/17448/aprenda-como-calcular-quanto-vai-receber-eacute-rias
     * 
     * Ferias = (UltSalario + (SomaDasMediasVariáveisDoPeriodoAquisitivo) /12) + 1/3
     * 
     * rh_clt desconto_inss (se tem desconto em outra empresa)
     * 
     * Obs: 
     *  1. Não existem excessões no somatório dos créditos de salário do Clt para fim de calculo de férias.
     *  2. SomaDasMediasVariáveisDoPeriodoAquisitivo não inclui o décimo terceiro salário
     *  3. No caso de férias dobradas, pegasse o ultimo Salário + Salário Variável no Período aquisitivo + 1/3 e multiplica-se por 12
     *  4. Verificando Abono Pecuniário (Venda de Dias) Venda das férias apenas sobre 1/3 do período (Arredondamento sempre para cima a favor do funcionário)
     *  5. INSS/FGTS/IRRF incide sobre o valor total das férias
     *  6. Pensão Alimentícia é aplicada sobre o salário Líquido (Salário - (INSS+IRRF))*Percentual de Pensão
     *  7. Caso o funcionário trabalhe em outra empresa e pague o teto do INSS (R$ 513.01) então não deverá ser descontato o INSS caso contrário o desconto e proporcional
     * 
     */
    public function setCalcFerias(){
        
        try {
            
            /*
             * Executa uma verificação do status do Clt para analisar se o mesmo não encontra-se em evento que o impessa de tirar férias
             */
            if(!$this->chkPodeTirarFerias()->isOk()) $this->error->set("Não foi possível verificar a disponibilidade do funcionário para tirar férias",E_FRAMEWORK_ERROR);

            /*
             * Contabiliza o número de faltas no período 
             */
            if(!$this->setCalcFaltas()->isOk()) $this->error->set("Não foi possível verificar se o funcionário possui faltas",E_FRAMEWORK_ERROR);

            /*
             * Executa o calculo para encontrar a data final do período de gozo a partir da data início e o desconto proporcional das faltas
             */
            if(!$this->setCalcDataFim()->isOk()) $this->error->set("Não foi possível calcular a data final do período de gozo",E_FRAMEWORK_ERROR);

            /*
             * Executa o calculo para setar o valor do salário base de calculo
             */
            if(!$this->setCalcSalarioVariavelInsalubridadePericulosidade()->isOk()) $this->error->set("Não foi possível calcular o valor do salário variável, insalubridade ou periculosidade",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o calculo para setar o valor do salário base de calculo
             */
            if(!$this->setCalcRemuneracaoBase()->isOk()) $this->error->set("Não foi possível calcular a remuneração base do funcionário",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o calculo de movimentos referentes a férias
             */
            if(!$this->setCalcMovimentosClt()->isOk()) $this->error->set("Não foi possível calcular os movimentos do funcionário",E_FRAMEWORK_ERROR);
            
            /*
             * Define o valor da remuneração total das férias com ou sem abono pecuniário
             */
            if(!$this->setCalcValorTotalFerias()->isOk()) $this->error->set("Não foi possível calcular o valor total das férias",E_FRAMEWORK_ERROR);

            /*
             * Define o valor da remuneração total das férias dobradas
             */
            if(!$this->setCalcValorTotalFeriasDobradas()->isOk()) $this->error->set("Não foi possível calcular o valor total das férias dobradas",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o calculo para encontrar valores e fatores do INSS,FGTS e IRRF
             */
            if(!$this->setCalcInssFgtsIrrf()->isOk()) $this->error->set("Não foi possível calcular os valores de INSS, FGTS e IRRF",E_FRAMEWORK_ERROR);

            /*
             * Executa o calculo de pensão alimentícia
             * 
             * Ficou pendente a pedido do Ramon a implementação do valor da pensão
             * 
             */
            if(!$this->setCalcPensaoAlimenticia()->isOk()) $this->error->set("Não foi possível calcular o valor da pensão alimentícia",E_FRAMEWORK_ERROR);
            
            /*
             * Executa a verificação de férias coletivas
             * 
             * ATENÇÃO - Implementação pendente
             * 
             * ATENÇÃO ATENÇÃO ATENÇÃO ATENÇÃO ATENÇÃO
             */

            /*
             * Define o valor do abono pecuniário
             */
            if(!$this->setCalcAbonoPecuniario()->isOk()) $this->error->set("Não foi possível calcular o valor do abono pecuniário",E_FRAMEWORK_ERROR);

            /*
             * Define o valor de um terço do abono pecuniário
             */
            if(!$this->setCalcUmTercoAbonoPecuniario()->isOk()) $this->error->set("Não foi possível calcular o valor de um terço do abono pecuniário",E_FRAMEWORK_ERROR);

            /*
             * Define o valor total dos rendimentos
             */
            if(!$this->setCalcTotalRendimentos()->isOk()) $this->error->set("Não foi possível calcular o valor da pensão alimentícia",E_FRAMEWORK_ERROR);

            /*
             * Calcula o valor total de descontos e seta a variável
             */
            
            if(!$this->setCalcTotalDescontos()->isOk()) $this->error->set("Não foi possível calcular o valor total dos descontos",E_FRAMEWORK_ERROR);
            
            /*
             * Calcula o valor total líquido a receber que é o valor total de rendimentos menos o valor total de descontos
             */
            if(!$this->setCalcTotalLiquido()->isOk()) $this->error->set("Não foi possível calcular o valor total dos descontos",E_FRAMEWORK_ERROR);    

            /*
             * Calcula os valores proporcionais para cada mês em que o período de gozo é realizado entre dois meses
             */
            if(!$this->setCalcValoresDistribuidosEmDoisMeses()->isOk()) $this->error->set("Não foi possível distribuir os valores de férias em dois meses",E_FRAMEWORK_ERROR);  

            /*
             * Verifica se houve instrução para ignorar férias do
             */
            if($this->getIgnorarFaltas()) $this->error->set("Instrução para ignorar faltas definida pelo usuário",E_FRAMEWORK_NOTICE);  
            
            /*
             * Verifica se houve instrução para ignorar férias dobradas pelo usuário e emite um aviso
             */
            if($this->getIgnorarFeriasDobradas()) $this->error->set("Instrução para ignorar férias dobradas definida pelo usuário",E_FRAMEWORK_NOTICE);  
            
            
            $this->setValue(1);
            
            
        } catch (Exception $ex) {

            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }
        
        return $this;
        
    }    

    
}
