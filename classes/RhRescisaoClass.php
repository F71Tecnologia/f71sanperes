<?php
/*
 * PHO-DOC - RhRecisaoClass.php
 * 
 * Classe para manipulação da tabela rh_recisao orientada a objetos
 *
 * 24-11-2015
 * 
 * @package RhRecisaoClass
 * @access public   
 * 
 * @version
 *  
 * Versão: 3.0.4385 - 24/11/2015 - Jacques - Versão Inicial
 * Versão: 3.0.7812 - 24/11/2015 - Jacques - Implementação de calculos rescisórios
 * 
 * Obs: Condições que impedem o afastamento do Clt são as de estabilidade provisória como licença maternidade
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 */
        
class RhRescisaoClass { 
    
    private $regra_motivo = array();
    
    private $matriz_de_calculos = array(
                                        'cod_fgts' => array(
                                                          60 => array(
                                                                      'mov' => ' H',
                                                                      'saq' => '00'
                                                                      ),
                                                          61 => array(
                                                                      'mov' => '11',
                                                                      'saq' => '01'
                                                                      ),
                                                          63 => array(
                                                                      'mov' => '01',
                                                                      'saq' => '  '
                                                                      ),
                                                          64 => array(
                                                                      'mov' => '01',
                                                                      'saq' => '04'
                                                                      ),
                                                          65 => array(
                                                                      'mov' => '01',
                                                                      'saq' => '  '
                                                                      ),
                                                          66 => array(
                                                                      'mov' => '01',
                                                                      'saq' => '  '
                                                                      ),
                                                          81 => array(
                                                                      'mov' => '11',
                                                                      'saq' => '  '
                                                                      ),
                                                          101 => array(
                                                                      'mov' => '01',
                                                                      'saq' => '  '
                                                                      )
                                                          ),   
                                        'tempo_servico' => array(
                                                                'dias_restantes_periodo_experiencia' => 0,
                                                                'anos' => 0,
                                                                'meses' => 0,
                                                                'dias' => 0
                                                                ),
                                        'vencimentos' => array(
                                                                'saldo_salario' => array(
                                                                                        'dias' => 0,
                                                                                        'valor' => 0
                                                                                        ),
                                                                'aviso_previo' => 0,
                                                                'ferias' => array(
                                                                                 'vencidas' => array(
                                                                                                    'periodo' => array(
                                                                                                                       'ini' => '',
                                                                                                                       'fim' => ''
                                                                                                                       ),
                                                                                                    'avos' => 0,
                                                                                                    'valor' => 0,
                                                                                                    'um_terco' => 0
                                                                                                    ),
                                                                                 'proporcional' =>  array(
                                                                                                    'periodo' => array(
                                                                                                                       'ini' => '',
                                                                                                                       'fim' => ''
                                                                                                                       ),
                                                                                                    'avos' => 0,
                                                                                                    'valor' => 0,
                                                                                                    'um_terco' => 0
                                                                                                    ),
                                                                                 'aviso_indenizado' =>  array(
                                                                                                    'periodo' => array(
                                                                                                                       'ini' => '',
                                                                                                                       'fim' => ''
                                                                                                                       ),
                                                                                                    'avos' => 0,
                                                                                                    'valor' => 0,
                                                                                                    'um_terco' => 0
                                                                                                    ),
                                                                                 'em_dobro' =>  array(
                                                                                                    'periodo' => array(
                                                                                                                       'ini' => '',
                                                                                                                       'fim' => ''
                                                                                                                       ),
                                                                                                    'avos' => 0,
                                                                                                    'valor' => 0,
                                                                                                    'um_terco' => 0
                                                                                                    ),
                                                                                 ),
                                                                'decimo_terceiro' => array(
                                                                                            'proporcional' =>  array(
                                                                                                           'periodo' => array(
                                                                                                                              'ini' => '',
                                                                                                                              'fim' => ''
                                                                                                                              ),
                                                                                                           'avos' => 0,
                                                                                                           'valor' => 0,
                                                                                                           'um_terco' => 0
                                                                                                           ),

                                                                                            'saldo_indenizado' =>  array(
                                                                                                           'periodo' => array(
                                                                                                                              'ini' => '',
                                                                                                                              'fim' => ''
                                                                                                                              ),
                                                                                                           'avos' => 0,
                                                                                                           'valor' => 0,
                                                                                                           'um_terco' => 0
                                                                                                           )
                                                                                            ),
                                                                'outros' => array(
                                                                                    'salario_familia' => 0,
                                                                                    'aviso_previo' => 0,
                                                                                    'atraso_rescisao_477' => 0,
                                                                                    'insalubridade' => 0,
                                                                                    'lei_12506' => 0,
                                                                                    'indenizacao_art_479_e_480' => array('dias_restantes_contrato_experiencia' => 0,
                                                                                                                         'valor' => 0
                                                                                                                         )
                                                                                   ),
                                                            ),
                                        'descontos' => array(
                                                            'aviso_previo' => 0,
                                                            'indenizacao_art_480' => 0,
                                                            'devolucao' => 0,
                                                            'inss' => 0,
                                                            'irrf' => 0
                                                            ),
                                        'total' => array(
                                                        'rendimentos' => 0,
                                                        'descontos' => 0,
                                                        'liquido' => 0
                                                        )
                                        );
    
    
    /*
     * PHP-DOC 
     * 
     * @name setDiasSaldoSalario
     * 
     * @internal - Método para registrar na matriz de cálculos os dias de saldo de salário
     * 
     */    
    public function setDiasSaldoSalario($value){
        
        $this->matriz_de_calculos['vencimentos']['saldo_salario']['dias'] = $value;
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getCodSaqueFgts
     * 
     * @internal - Método obter o código do saque do fgts de acordo com o status rescisório
     * 
     */    
    public function getCodSaqueFgts(){
        
        return $this->matriz_de_calculos['cod_fgts'][$this->getSuperClass()->Clt->getStatus()]['saq'];
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name getCodMovFgts
     * 
     * @internal - Método obter o código do movimento do fgts de acordo com o status rescisório
     * 
     */    
    public function getCodMovFgts(){
        
        return $this->matriz_de_calculos['cod_fgts'][$this->getSuperClass()->Clt->getStatus()]['mov'];
        
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

            $this->db->setQuery(WHERE," AND status",ADD);

            if(is_object($this->getSuperClass()->FolhaProc) && $this->getMagneticKey()){

                $id_regiao = $this->getSuperClass()->FolhaProc->getIdRegiao();
                $id_projeto = $this->getSuperClass()->FolhaProc->getIdProjeto();
                $id_clt = $this->getSuperClass()->FolhaProc->getIdClt();

            }        
            else {

                if(is_object($this->getSuperClass()->Clt) && $this->getMagneticKey()){

                    $id_regiao = $this->getSuperClass()->Clt->getIdRegiao();
                    $id_projeto = $this->getSuperClass()->Clt->getIdProjeto();
                    $id_clt = $this->getSuperClass()->Clt->getIdClt();

                }        
                else {

                    $id_regiao = $this->getIdRegiao();
                    $id_projeto = $this->getIdProjeto();
                    $id_clt = $this->getIdClt();

                } 

            } 


            $dateRangeFmt = $this->GetDateRangeFmt();
            $dateRangeIni = $this->getDateRangeIni($dateRangeFmt)->val();
            $dateRangeFim = $this->getDateRangeFim($dateRangeFmt)->val();
            $dateRangeSqlFmt = $this->getDateRangeSqlFmt($dateRangeFmt);
            $dateRangeField = $this->getDateRangeField();

            if(!empty($id_regiao)) {$this->db->setQuery(WHERE,"AND id_regiao = {$id_regiao}",ADD);}

            if(!empty($id_projeto)) {$this->db->setQuery(WHERE,"AND id_projeto = {$id_projeto}",ADD);}

            if(!empty($id_clt)) {$this->db->setQuery(WHERE,"AND id_clt = {$id_clt}",ADD);}

            if(!empty($dateRangeIni) && !empty($dateRangeFim)) {$this->db->setQuery(WHERE,"AND $dateRangeField BETWEEN '{$dateRangeIni}' AND '{$dateRangeFim}'",ADD);}

            if(empty($id_regiao) && empty($id_projeto) && empty($dateRangeIni) && empty($dateRangeFim)) $this->error->set(array(4,__METHOD__),E_FRAMEWORK_ERROR);

            $this->db->setQuery(ORDER,
                                "
                                id_projeto,
                                nome,
                                data_demi    
                                ");
            
            $this->setValue(1);
            
        } 
        catch (Exception $ex) {

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
     * @name setCalcFaltas
     * 
     * @internal - Método para calcular as faltas do Clt no período de admissão
     * 
     */    
    public function setCalcFaltas(){

        $this->setValue(1);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcDiasSaldoSalario()
     * 
     * @internal - Método registras os dias de saldo de salário do Clt 
     * 
     */    
    public function setCalcDiasSaldoSalario(){
        
        try {
            
            if(!is_object($this->getSuperClass()->Clt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            $this->getSuperClass()->Clt->saveClass();

            $this->getSuperClass()->Clt->setDateRangeFmt('Y-m-d');
                    
            $this->getSuperClass()->Clt->setDateRangeIni($this->getSuperClass()->Clt->getDataDemi());
                    
            $this->getSuperClass()->Clt->setDateRangeEnd($this->getSuperClass()->Clt->getDataDemi());
            
            echo 'Saldo Salário = '.$this->getSuperClass()->Clt->getCalcDiasSaldoSalario();
            
            $this->setDiasSaldoSalario(0);
            
            $this->getSuperClass()->Clt = $this->getSuperClass()->Clt->getRestoreClass();

            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->setValue(0);
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return $this;
        
    }

    /*
     * PHP-DOC 
     * 
     * @name setCalcTempoServico
     * 
     * @internal - Método para calcular o tempo de serviço do Clt
     * 
     */    
    public function setCalcTempoServico(){
        
        $this->setValue(1);
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcIndenicacaoArt479e480
     * 
     * @internal - Método para calcular os fatores do prazo de experiência
     * 
     */    
    public function setCalcIndenicacaoArt479e480(){
        

        
    }
    
    /*
     * PHP-DOC
     * 
     * @name chkPodeRescindir
     * 
     * @internal - Método para verificar se o clt está àpito a ser rescindido
     * 
     */   
    public function chkJaTemRescisao(){
        
        try {
            
            $this->setValue($this->select()->isOk());
            
        } catch (Exception $ex) {
            
            $this->setValue(0);
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return $this;
        
    }    
    
    /*
     * PHP-DOC
     * 
     * @name chkRescisaoColetiva
     * 
     * @internal - Verifica se é uma rescisão coletiva
     * 
     */   
    public function chkRescisaoColetiva(){
        
        try {
            
            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->setValue(0);
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return $this;
        
    }    
    
    /*
     * PHP-DOC
     * 
     * @name chkPodeRescindir
     * 
     * @internal - Método para verificar se o clt está àpito a ser rescindido
     * 
     */   
    public function chkPodeRescindir(){
        
        try {
            
            switch ($this->getSuperClass()->Clt->getStatusRealTime()) {
                case 10:

                    if($this->getSuperClass()->Clt->getEstabilidadeProvisoria()) $this->error->set('Clt encontrase em estabilidade provisória',E_FRAMEWORK_NOTICE);                  
                        
                    $this->setValue(1);

                    break;
                
                default:
                    
                    $this->getSuperClass()->Clt->setStatus($this->getSuperClass()->Clt->getStatusRealTime());
                    
                    if($this->getSuperClass()->Clt->getStatus()) $this->getSuperClass()->Status->select()->getRow();
                    
                    $this->error->set(empty($this->getSuperClass()->Clt->getStatus()) ? "Ops!!! Algo deu errado aqui, nenhum registro retornado para essa operação. Verifique a região selecionada!" : "Não é possível lançar rescisão para funcionário com evento ({$this->getSuperClass()->Status->getEspecifica()})",E_FRAMEWORK_NOTICE);

                    $this->setValue(0);

                    break;
            }
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }         
        
        return $this;
        
    }
    
    /*
     * PHP-DOC
     * 
     * @name setCalcArt479e480
     * 
     * @internal - Executa o cálculo do artigo 479 e 480 de acordo com o período de experiência
     * 
     */   
    public function setCalcArt479e480(){
        
        try {
            
            
            
            $this->setValue(1);
            
        } catch (Exception $ex) {
            
            $this->setValue(0);
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
        }
        
        return $this;
        
    }    
    
    /*
     * PHP-DOC
     * 
     * @name setConfigCalc
     * 
     * @internal - Método para definir as condições dos calculos de acordo com o código rescisórios
     * 
     */   
    public function setConfigCalc(){
        
        try {

            
            
        }    
        catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);
            
            $this->setValue(0);
            
        }         
        
        return $this;
        
    }
    
    /*
     * PHP-DOC 
     * 
     * @name setCalcRescisao
     * 
     * @internal - Método de excecução de cálculo da rescisão
     * 
     * Consultor de RH: Donato Laviano
     * 
     * Obs: 
     * 
     */
    public function setCalcRescisao(){
        
        try {
            
            $this->setCalcDiasSaldoSalario();
            
            /*
             * Executa uma verificação para ver se já existe rescisão para o Clt
             */
            if(!$this->chkJaTemRescisao()->isOk()) $this->error->set("Não foi possível verificar se o Clt já possui alguma rescisão",E_FRAMEWORK_ERROR);

            /*
             * Executa uma verificação do status do Clt para analisar se o mesmo poderá ser rescindido ou por exemplo encontra-se em estabilidade provisória
             */
            if(!$this->chkPodeRescindir()->isOk()) $this->error->set("Não foi possível verificar a disponibilidade do funcionário para gerar a rescisão",E_FRAMEWORK_ERROR);
            
            /*
             * Executa uma verificação para analisar se é uma rescisão coletiva
             */
            if(!$this->chkRescisaoColetiva()->isOk()) $this->error->set("Não foi possível verificar se é uma rescisão coletiva",E_FRAMEWORK_ERROR);

            /*
             * Executa o set de configuração de condições de calculos de acordo com o tipo rescisorio
             */
            if(!$this->setConfigCalc()->isOk()) $this->error->set("Não foi possível definir as condiçoes de calculos rescisórios",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o cálculo do artigo 479 e 480
             */
            if(!$this->setCalcArt479e480()->isOk()) $this->error->set("Não foi possível fazer o cálculo do artigo 479 e 480",E_FRAMEWORK_ERROR);
            
           
            $this->setValue(1);
            
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
            
        }
        
        return $this;
        
    }   
    

    
}