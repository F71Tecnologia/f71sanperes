<?php
/*
 * PHO-DOC - RhRecisaoClass.php
 * 
 * Classe para manipula��o da tabela rh_recisao orientada a objetos
 *
 * 24-11-2015
 * 
 * @package RhRecisaoClass
 * @access public   
 * 
 * @version
 *  
 * Vers�o: 3.0.4385 - 24/11/2015 - Jacques - Vers�o Inicial
 * Vers�o: 3.0.7812 - 24/11/2015 - Jacques - Implementa��o de calculos rescis�rios
 * 
 * Obs: Condi��es que impedem o afastamento do Clt s�o as de estabilidade provis�ria como licen�a maternidade
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
     * @internal - M�todo para registrar na matriz de c�lculos os dias de saldo de sal�rio
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
     * @internal - M�todo obter o c�digo do saque do fgts de acordo com o status rescis�rio
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
     * @internal - M�todo obter o c�digo do movimento do fgts de acordo com o status rescis�rio
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
     * @internal - M�todo que adiciona as condi��es extendidas de um select de classe
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
     * @name setCalcFaltas
     * 
     * @internal - M�todo para calcular as faltas do Clt no per�odo de admiss�o
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
     * @internal - M�todo registras os dias de saldo de sal�rio do Clt 
     * 
     */    
    public function setCalcDiasSaldoSalario(){
        
        try {
            
            if(!is_object($this->getSuperClass()->Clt)) $this->error->set(array(6,__METHOD__),E_FRAMEWORK_ERROR);

            $this->getSuperClass()->Clt->saveClass();

            $this->getSuperClass()->Clt->setDateRangeFmt('Y-m-d');
                    
            $this->getSuperClass()->Clt->setDateRangeIni($this->getSuperClass()->Clt->getDataDemi());
                    
            $this->getSuperClass()->Clt->setDateRangeEnd($this->getSuperClass()->Clt->getDataDemi());
            
            echo 'Saldo Sal�rio = '.$this->getSuperClass()->Clt->getCalcDiasSaldoSalario();
            
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
     * @internal - M�todo para calcular o tempo de servi�o do Clt
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
     * @internal - M�todo para calcular os fatores do prazo de experi�ncia
     * 
     */    
    public function setCalcIndenicacaoArt479e480(){
        

        
    }
    
    /*
     * PHP-DOC
     * 
     * @name chkPodeRescindir
     * 
     * @internal - M�todo para verificar se o clt est� �pito a ser rescindido
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
     * @internal - Verifica se � uma rescis�o coletiva
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
     * @internal - M�todo para verificar se o clt est� �pito a ser rescindido
     * 
     */   
    public function chkPodeRescindir(){
        
        try {
            
            switch ($this->getSuperClass()->Clt->getStatusRealTime()) {
                case 10:

                    if($this->getSuperClass()->Clt->getEstabilidadeProvisoria()) $this->error->set('Clt encontrase em estabilidade provis�ria',E_FRAMEWORK_NOTICE);                  
                        
                    $this->setValue(1);

                    break;
                
                default:
                    
                    $this->getSuperClass()->Clt->setStatus($this->getSuperClass()->Clt->getStatusRealTime());
                    
                    if($this->getSuperClass()->Clt->getStatus()) $this->getSuperClass()->Status->select()->getRow();
                    
                    $this->error->set(empty($this->getSuperClass()->Clt->getStatus()) ? "Ops!!! Algo deu errado aqui, nenhum registro retornado para essa opera��o. Verifique a regi�o selecionada!" : "N�o � poss�vel lan�ar rescis�o para funcion�rio com evento ({$this->getSuperClass()->Status->getEspecifica()})",E_FRAMEWORK_NOTICE);

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
     * @internal - Executa o c�lculo do artigo 479 e 480 de acordo com o per�odo de experi�ncia
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
     * @internal - M�todo para definir as condi��es dos calculos de acordo com o c�digo rescis�rios
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
     * @internal - M�todo de excecu��o de c�lculo da rescis�o
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
             * Executa uma verifica��o para ver se j� existe rescis�o para o Clt
             */
            if(!$this->chkJaTemRescisao()->isOk()) $this->error->set("N�o foi poss�vel verificar se o Clt j� possui alguma rescis�o",E_FRAMEWORK_ERROR);

            /*
             * Executa uma verifica��o do status do Clt para analisar se o mesmo poder� ser rescindido ou por exemplo encontra-se em estabilidade provis�ria
             */
            if(!$this->chkPodeRescindir()->isOk()) $this->error->set("N�o foi poss�vel verificar a disponibilidade do funcion�rio para gerar a rescis�o",E_FRAMEWORK_ERROR);
            
            /*
             * Executa uma verifica��o para analisar se � uma rescis�o coletiva
             */
            if(!$this->chkRescisaoColetiva()->isOk()) $this->error->set("N�o foi poss�vel verificar se � uma rescis�o coletiva",E_FRAMEWORK_ERROR);

            /*
             * Executa o set de configura��o de condi��es de calculos de acordo com o tipo rescisorio
             */
            if(!$this->setConfigCalc()->isOk()) $this->error->set("N�o foi poss�vel definir as condi�oes de calculos rescis�rios",E_FRAMEWORK_ERROR);
            
            /*
             * Executa o c�lculo do artigo 479 e 480
             */
            if(!$this->setCalcArt479e480()->isOk()) $this->error->set("N�o foi poss�vel fazer o c�lculo do artigo 479 e 480",E_FRAMEWORK_ERROR);
            
           
            $this->setValue(1);
            
            
        } catch (Exception $ex) {
            
            $this->error->set(array(1,__METHOD__),E_FRAMEWORK_WARNING,$ex);

            $this->setValue(0);
            
        }
        
        return $this;
        
    }   
    

    
}