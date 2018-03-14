<?php 
/*
 * RhFeriasCompClass.php
 * 
 * 10-09-2015
 * 
 * Classe para criação de camada de compatibilidade retroativa na operacionalização das férias
 * 
 * Versão: 3.0.0000 - 10/09/2015 - Jacques - 
 * 
 * @jacques
 * 
 */


class RhFeriasClass {
    
    private     $super_class;    
    protected   $error;
    private     $date;
    private     $db; 
    
    private     $rh_ferias_default = array(
                            'id' => 0,
                            'id_clt' => 0,
                            'id_registro_logico' => 0,
                            'id_legenda' => 0,
                            'unidade_medida' => 0,
                            'valor' => 0,
                            'data_inicio' => '',
                            'data_fim' => '',
                            'criado_por' => 0,
                            'criado_em' => '',
                            'status' => 1
                            );
    
    
    private     $rh_ferias_default_old = array(
                            'id_ferias' => 0,
                            'id_clt' => 0,
                            'nome' => '',
                            'regiao' => 0,
                            'projeto' => 0,
                            'mes' => 0,
                            'ano' => 0,
                            'data_aquisitivo_ini' => '',
                            'data_aquisitivo_fim' => '',
                            'data_ini' => '',
                            'data_fim' => '',
                            'data_retorno' => '',
                            'salario' => 0,
                            'salario_variavel' => 0,
                            'remuneracao_base' => 0,
                            'dias_ferias' => 0,
                            'valor_dias_ferias' => 0,
                            'valor_total_ferias' => 0,
                            'umterco' => 0,
                            'total_remuneracoes' => 0,
                            'pensao_alimenticia' => 0,
                            'inss' => 0,
                            'inss_porcentagem' => 0,
                            'ir' => 0,
                            'fgts' => 0,
                            'total_descontos' => 0,
                            'total_liquido' => 0,
                            'faltas' => 0,
                            'faltasano' => 0,
                            'mes_dt' => 0,
                            'mes_ferias' => 0,
                            'diasmes' => 0,
                            'valor_total_ferias1' => 0,
                            'valor_total_ferias2' => 0,
                            'acrescimo_constitucional1' => 0,
                            'acrescimo_constitucional2' => 0,
                            'total_remuneracoes1' => 0,
                            'total_remuneracoes2' => 0,
                            'ferias_dobradas' => '',
                            'dias_abono_pecuniario' => 0,
                            'abono_pecuniario' => 0,
                            'umterco_abono_pecuniario' => 0,
                            'vendido' => 0,
                            'movimentos' => 0,
                            'base_inss' => 0,
                            'base_irrf' => 0,
                            'percentual_irrf' => 0,
                            'valor_ddir' => 0,
                            'qnt_dependente_irrf' => 0,
                            'parcela_deducao_irrf' => 0,
                            'user' => 0,
                            'data_proc' => '',
                            'status' => 0,
                            'desprocessado_recisao' => 0,
                            'id_funcionario_desproc_rescisao' => 0
                            );
    
    
    public function __construct() {
        
        
    }
    
  
    public function setDefault() {
        
        $this->rh_ferias = $this->rh_ferias_default;
        
    }
    
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }  
    
    public function setId($valor){
        
        $this->rh_ferias['id_ferias'] = $valor;
        
    } 
    
    public function setIdClt($valor){
        
        $this->rh_ferias['id_clt'] = $valor;
        
    }     
    
    public function setNome($valor){
        
        $this->rh_ferias['nome'] = $valor;
        
    }     
    
    public function setRegiao($valor){
        
        $this->rh_ferias['regiao'] = $valor;
        
    }     
    
    public function setProjeto($valor){
        
        $this->rh_ferias['projeto'] = $valor;
        
    }     
    
    public function setMes($valor){
        
        $this->rh_ferias['mes'] = $valor;
        
    }    
    
    public function setAno($valor){
        
        $this->rh_ferias['ano'] = $valor;
        
    }     
    
    public function setDataAquisitivoIni($valor){
        
        $this->rh_ferias['data_aquisitivo_ini'] = $valor;
        
    }     

    public function setDataAquisitivoFim($valor){
        
        $this->rh_ferias['data_aquisitivo_fim'] = $valor;
        
    }     
    
    public function setDataIni($valor){
        
        $this->rh_ferias['data_ini'] = $valor;
        
    }     
    
    public function setDataFim($valor){
        
        $this->rh_ferias['data_fim'] = $valor;
        
    }     
    
    public function setDataRetorno($valor){
        
        $this->rh_ferias['data_retorno'] = $valor;
        
    }     
    
    public function setSalario($valor){
        
        $this->rh_ferias['salario'] = $valor;
        
    }  
    
    public function setSalarioVariavel($valor){
        
        $this->rh_ferias['salario_variavel'] = $valor;
        
    }    
    
    public function setRemuneracaoBase($valor){
        
        $this->rh_ferias['remuneracao_base'] = $valor;
        
    }    
    
    public function setDiasFerias($valor){
        
        $this->rh_ferias['dias_ferias'] = $valor;
        
    }    
    
    public function setValorDiasFerias($valor){
        
        $this->rh_ferias['valor_dias_ferias'] = $valor;
        
    }    

    public function setValorTotal($valor){
        
        $this->rh_ferias['valor_total_ferias'] = $valor;
        
    }    

    public function setUmTerco($valor){
        
        $this->rh_ferias['umterco'] = $valor;
        
    }    

    public function setTotalRemuneracoes($valor){
        
        $this->rh_ferias['total_remuneracoes'] = $valor;
        
    }    
    
    public function setPensaoAlimenticia($valor){
        
        $this->rh_ferias['pensao_alimenticia'] = $valor;
        
    }    

    public function setInss($valor){
        
        $this->rh_ferias['inss'] = $valor;
        
    }    

    public function setInssPorcentagem($valor){
        
        $this->rh_ferias['inss_porcentagem'] = $valor;
        
    }    
    
    public function setIr($valor){
        
        $this->rh_ferias['ir'] = $valor;
        
    }    

    public function setFgts($valor){
        
        $this->rh_ferias['fgts'] = $valor;
        
    }    

    public function setTotalDescontos($valor){
        
        $this->rh_ferias['total_descontos'] = $valor;
        
    }    
    
    public function setTotalLiquido($valor){
        
        $this->rh_ferias['TotalLiquido'] = $valor;
        
    }    
    
    public function setFaltas($valor){
        
        $this->rh_ferias['faltas'] = $valor;
        
    }    
    
    public function setFaltasAno($valor){
        
        $this->rh_ferias['faltasano'] = $valor;
        
    }    

    public function setMesDt($valor){
        
        $this->rh_ferias['mes_data'] = $valor;
        
    }    

    public function setMesFerias($valor){
        
        $this->rh_ferias['mes_ferias'] = $valor;
        
    }    

    public function setDiasMes($valor){
        
        $this->rh_ferias['diasmes'] = $valor;
        
    }    

    public function setValorTotalFerias1($valor){
        
        $this->rh_ferias['valor_total_ferias1'] = $valor;
        
    }    
    
    public function setValorTotalFerias2($valor){
        
        $this->rh_ferias['valor_total_ferias2'] = $valor;
        
    }    
    
    public function setAcrescimoConstitucional1($valor){
        
        $this->rh_ferias['acrescimo_constitucional1'] = $valor;
        
    }    
    
    public function setAcrescimoConstitucional2($valor){
        
        $this->rh_ferias['acrescimo_constitucional2'] = $valor;
        
    }    

    public function setTotalRemuneracoes1($valor){
        
        $this->rh_ferias['total_remuneracoes1'] = $valor;
        
    }    

    public function setTotalRemuneracoes2($valor){
        
        $this->rh_ferias['total_remuneracoes2'] = $valor;
        
    }    
    
    public function setFeriasDobradas($valor){
        
        $this->rh_ferias['ferias_dobradas'] = $valor;
        
    }    

    public function setUmTercoAbonoPecuniario($valor){
        
        $this->rh_ferias['umterco_abono_pecuniario'] = $valor;
        
    }    

    public function setVendido($valor){
        
        $this->rh_ferias['vendido'] = $valor;
        
    }   

    public function setDiasAbonoPecuniario($valor){
        
        $this->rh_ferias['dias_abono_pecuniario'] = $valor;
        
    }    

    public function setMovimentos($valor){
        
        $this->rh_ferias['movimentos'] = $valor;
        
    }    

    public function setBaseInss($valor){
        
        $this->rh_ferias['base_inss'] = $valor;
        
    }    

    public function setBaseIrrf($valor){
        
        $this->rh_ferias['base_irrf'] = $valor;
        
    }    

    public function setPercentualIrrf($valor){
        
        $this->rh_ferias['percentual_irrf'] = $valor;
        
    }    

    public function setValorDdir($valor){
        
        $this->rh_ferias['valor_ddir'] = $valor;
        
    }    

    public function setQntDependenteIrrf($valor){
        
        $this->rh_ferias['qnt_dependente_irrf'] = $valor;
        
    }    

    public function setParcelaDeducaoIrrf($valor){
        
        $this->rh_ferias['parcela_deducao_irrf'] = $valor;
        
    }    

    public function setUser($valor){
        
        $this->rh_ferias['user'] = $valor;
        
    }    

    public function setDataProc($valor){
        
        $this->rh_ferias['data_proc'] = $valor;
        
    }    

    public function setStatus($valor){
        
        $this->rh_ferias['status'] = $valor;
        
    }    

    public function setDesprocessadoRecisao($valor){
        
        $this->rh_ferias['desprocessado_recisao'] = $valor;
        
    }    

    public function setIdFuncionarioDesprocRescisao($valor){
        
        $this->rh_ferias['id_funcionario_desproc_rescisao'] = $valor;
        
    }    

    public function getSuperClass() {
        
        return $this->super_class;
        
    }      
    
    public function getFeriasId(){
        
        return $this->rh_ferias['id_ferias'];
        
    } 
    
    public function getFeriasIdClt(){
        
        return $this->rh_ferias['id_clt'];
        
    }     
    
    public function getFeriasNome(){
        
        return $this->rh_ferias['nome'];
        
    }     
    
    public function getFeriasRegiao(){
        
        return $this->rh_ferias['regiao'];
        
    }     
    
    public function getFeriasProjeto(){
        
        return $this->rh_ferias['projeto'];
        
    }     
    
    public function getFeriasMes(){
        
        return $this->rh_ferias['mes'];
        
    }    
    
    public function getFeriasAno(){
        
        return $this->rh_ferias['ano'];
        
    }     
    
    public function getFeriasDataAquisitivoIni(){
        
        return $this->rh_ferias['data_aquisitivo_ini'];
        
    }     

    public function getFeriasDataAquisitivoFim(){
        
        return $this->rh_ferias['data_aquisitivo_fim'];
        
    }     
    
    public function getFeriasDataIni(){
        
        return $this->rh_ferias['data_ini'];
        
    }     
    
    public function getFeriasDataFim(){
        
        return $this->rh_ferias['data_fim'];
        
    }     
    
    public function getFeriasDataRetorno(){
        
        return $this->rh_ferias['data_retorno'];
        
    }     
    
    public function getFeriasSalario(){
        
        return $this->rh_ferias['salario'];
        
    }  
    
    public function getFeriasSalarioVariavel(){
        
        return $this->rh_ferias['salario_variavel'];
        
    }    
    
    public function getFeriasRemuneracaoBase(){
        
        return $this->rh_ferias['remuneracao_base'];
        
    }    
    
    public function getFeriasDiasFerias(){
        
        return $this->rh_ferias['dias_ferias'];
        
    }    
    
    public function getFeriasValorDiasFerias(){
        
        return $this->rh_ferias['valor_dias_ferias'];
        
    }    

    public function getFeriasValorTotal(){
        
        return $this->rh_ferias['valor_total_ferias'];
        
    }    

    public function getFeriasUmTerco(){
        
        return $this->rh_ferias['umterco'];
        
    }    

    public function getFeriasTotalRemuneracoes(){
        
        return $this->rh_ferias['total_remuneracoes'];
        
    }    
    
    public function getFeriasPensaoAlimenticia(){
        
        return $this->rh_ferias['pensao_alimenticia'];
        
    }    

    public function getFeriasInss(){
        
        return $this->rh_ferias['inss'];
        
    }    

    public function getFeriasInssPorcentagem(){
        
        return $this->rh_ferias['inss_porcentagem'];
        
    }    
    
    public function getFeriasIr(){
        
        return $this->rh_ferias['ir'];
        
    }    

    public function getFeriasFgts(){
        
        return $this->rh_ferias['fgts'];
        
    }    

    public function getFeriasTotalDescontos(){
        
        return $this->rh_ferias['total_descontos'];
        
    }    
    
    public function getFeriasTotalLiquido(){
        
        return $this->rh_ferias['TotalLiquido'];
        
    }    
    
    public function getFeriasFaltas(){
        
        return $this->rh_ferias['faltas'];
        
    }    
    
    public function getFeriasFaltasAno(){
        
        return $this->rh_ferias['faltasano'];
        
    }    

    public function getFeriasMesDt(){
        
        return $this->rh_ferias['mes_data'];
        
    }    

    public function getFeriasMesFerias(){
        
        return $this->rh_ferias['mes_ferias'];
        
    }    

    public function getFeriasDiasMes(){
        
        return $this->rh_ferias['diasmes'];
        
    }    

    public function getFeriasValorTotalFerias1(){
        
        return $this->rh_ferias['valor_total_ferias1'];
        
    }    
    
    public function getFeriasValorTotalFerias2(){
        
        return $this->rh_ferias['valor_total_ferias2'];
        
    }    
    
    public function getFeriasAcrescimoConstitucional1(){
        
        return $this->rh_ferias['acrescimo_constitucional1'];
        
    }    
    
    public function getFeriasAcrescimoConstitucional2(){
        
        return $this->rh_ferias['acrescimo_constitucional2'];
        
    }    

    public function getFeriasTotalRemuneracoes1(){
        
        return $this->rh_ferias['total_remuneracoes1'];
        
    }    

    public function getFeriasTotalRemuneracoes2(){
        
        return $this->rh_ferias['total_remuneracoes2'];
        
    }    
    
    public function getFeriasFeriasDobradas(){
        
        return $this->rh_ferias['ferias_dobradas'];
        
    }    

    public function getFeriasUmTercoAbonoPecuniario(){
        
        return $this->rh_ferias['umterco_abono_pecuniario'];
        
    }    

    public function getFeriasVendido(){
        
        return $this->rh_ferias['vendido'];
        
    }   

    public function getFeriasDiasAbonoPecuniario(){
        
        return $this->rh_ferias['dias_abono_pecuniario'];
        
    }    

    public function getFeriasMovimentos(){
        
        return $this->rh_ferias['movimentos'];
        
    }    

    public function getFeriasBaseInss(){
        
        return $this->rh_ferias['base_inss'];
        
    }    

    public function getFeriasBaseIrrf(){
        
        return $this->rh_ferias['base_irrf'];
        
    }    

    public function getFeriasPercentualIrrf(){
        
        return $this->rh_ferias['percentual_irrf'];
        
    }    

    public function getFeriasValorDdir(){
        
        return $this->rh_ferias['valor_ddir'];
        
    }    

    public function getFeriasQntDependenteIrrf(){
        
        return $this->rh_ferias['qnt_dependente_irrf'];
        
    }    

    public function getFeriasParcelaDeducaoIrrf(){
        
        return $this->rh_ferias['parcela_deducao_irrf'];
        
    }    

    public function getFeriasUser(){
        
        return $this->rh_ferias['user'];
        
    }    

    public function getFeriasDataProc(){
        
        return $this->rh_ferias['data_proc'];
        
    }    

    public function getFeriasStatus($valor){
        
        return $this->rh_ferias['status'];
        
    }    

    public function getFeriasDesprocessadoRecisao(){
        
        return $this->rh_ferias['desprocessado_recisao'];
        
    }    

    public function getFeriasIdFuncionarioDesprocRescisao(){
        
        return $this->rh_ferias['id_funcionario_desproc_rescisao'];
        
    }    
    
    public function getFeriasRow(){
        
        if($this->db->setRow()){

            $this->setId($this->db->getRow('id_ferias'));
            $this->setIdClt($this->db->getRow('id_clt'));
            $this->setNome($this->db->getRow('nome'));
            $this->setRegiao($this->db->getRow('regiao'));
            $this->setMes($this->db->getRow('mes'));
            $this->setAno($this->db->getRow('ano'));
            $this->setDataAquisitivoIni($this->db->getRow('data_aquisitivo_ini'));            
            $this->setDataAquisitivoFim($this->db->getRow('data_aquisitivo_fim'));            
            $this->setDataIni($this->db->getRow('data_ini'));            
            $this->setDataFim($this->db->getRow('data_fim'));            
            $this->setDataRetorno($this->db->getRow('data_retorno'));            
            $this->setSalario($this->db->getRow('salario'));            
            $this->setSalarioVariavel($this->db->getRow('salario_variavel'));            
            $this->setRemuneracaoBase($this->db->getRow('remuneracao_base'));            
            $this->setDiasFerias($this->db->getRow('dias_ferias'));            
            $this->setValorDiasFerias($this->db->getRow('valor_dias_ferias'));            
            $this->setUmTerco($this->db->getRow('umterco'));            
            $this->setPensaoAlimenticia($this->db->getRow('pensao_alimenticia'));            
            $this->setInss($this->db->getRow('inss'));            
            $this->setInssPorcentagem($this->db->getRow('inss_porcentagem'));            
            $this->setIr($this->db->getRow('ir')); 
            $this->setFgts($this->db->getRow('fgts')); 
            $this->setTotalDescontos($this->db->getRow('total_descontos')); 
            $this->setTotalLiquido($this->db->getRow('total_liquido')); 
            $this->setFaltas($this->db->getRow('faltas')); 
            $this->setFaltasAno($this->db->getRow('faltasano')); 
            $this->setMesDt($this->db->getRow('mes_dt')); 
            $this->setMesFerias($this->db->getRow('mes_ferias')); 
            $this->setDiasMes($this->db->getRow('diasmes')); 
            $this->setValorTotalFerias1($this->db->getRow('valor_total_ferias1')); 
            $this->setValorTotalFerias2($this->db->getRow('valor_total_ferias2')); 
            $this->setAcrescimoConstitucional1($this->db->getRow('acrescimo_constitucional1'));             
            $this->setAcrescimoConstitucional2($this->db->getRow('acrescimo_constitucional2'));             
            $this->setTotalRemuneracoes1($this->db->getRow('total_remuneracoes1'));             
            $this->setTotalRemuneracoes2($this->db->getRow('total_remuneracoes2'));             
            $this->setFeriasDobradas($this->db->getRow('ferias_dobradas'));             
            $this->setDiasAbonoPecuniario($this->db->getRow('dias_abono_pecuniario'));             
            $this->setUmTercoAbonoPecuniario($this->db->getRow('umterco_abono_pecuniario'));             
            $this->setVendido($this->db->getRow('vendido'));    
            $this->setMovimentos($this->db->getRow('movimentos'));    
            $this->setBaseInss($this->db->getRow('base_inss'));    
            $this->setPercentualIrrf($this->db->getRow('percentual_irrf'));    
            $this->setValorDdir($this->db->getRow('valor_ddir'));    
            $this->setQntDependenteIrrf($this->db->getRow('qnt_dependente_irrf'));    
            $this->setParcelaDeducaoIrrf($this->db->getRow('parcela_deducao_irrf'));    
            $this->setUser($this->db->getRow('user'));    
            $this->setDataProc($this->db->getRow('data_proc'));    
            $this->setStatus($this->db->getRow('status'));    
            $this->setDesprocessadoRecisao($this->db->getRow('desprocessado_recisao'));    
            $this->setIdFuncionarioDesprocRescisao($this->db->getRow('id_funcionario_desproc_rescisao'));    
            
            
            return 1;
            
        }
        else{
            
            $this->error->setError($this->db->error->getError());            
            
            return 0;
        }
        
    }
    
    private function createCoreClass() {
        
        if(!isset($this->error)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].'intranet/classes/ErrorClass.php');
            $this->error = new ErrorClass();        
            
        }
        
        if(!isset($this->db)){
            
            include_once($_SERVER['DOCUMENT_ROOT'].'intranet/classes/MySqlClass.php');

            $this->db = new MySqlClass();
            
        }
        
    }    
    
    public function selectFerias(){
        
        $this->createCoreClass();
                
        $this->db->setQuery("SELECT 
                                id_ferias,
                                id_clt,
                                nome,
                                regiao,
                                projeto,
                                mes,
                                ano,
                                data_aquisitivo_ini,
                                data_aquisitivo_fim,
                                data_ini,
                                data_fim,
                                data_retorno,
                                salario,
                                salario_variavel,
                                remuneracao_base,
                                dias_ferias,
                                valor_dias_ferias,
                                valor_total_ferias,
                                umterco,
                                total_remuneracoes,
                                pensao_alimenticia,
                                inss,
                                inss_porcentagem,
                                ir,
                                fgts,
                                total_descontos,
                                total_liquido,
                                faltas,
                                faltasano,
                                mes_dt,
                                mes_ferias,
                                diasmes,
                                valor_total_ferias1,
                                valor_total_ferias2,
                                acrescimo_constitucional1,
                                acrescimo_constitucional2,
                                total_remuneracoes1,
                                total_remuneracoes2,
                                ferias_dobradas,
                                dias_abono_pecuniario,
                                abono_pecuniario,
                                umterco_abono_pecuniario,
                                vendido,
                                movimentos,
                                base_inss,
                                base_irrf,
                                percentual_irrf,
                                valor_ddir,
                                qnt_dependente_irrf,
                                parcela_deducao_irrf,
                                user,
                                data_proc,
                                status,
                                desprocessado_recisao,
                                id_funcionario_desproc_rescisao

                             FROM rh_ferias ",SELECT,false);
        
        if(class_exists('RhCltClass')){
    
            $id_clt = parent::getIdClt();
            
            $id_projeto = parent::getIdProjeto();

            $id_regiao = parent::getIdRegiao();
           
        }        
        else {
            
            $id_clt = $this->rh_ferias['id_clt'];
            
            $id_projeto = $this->rh_ferias['projeto'];

            $id_regiao = $this->rh_ferias['regiao'];
            
        }
        
        
        if(!empty($id_clt) || !empty($id_regiao) ||  !empty($id_projeto)) {

            $this->db->setQuery("WHERE 1=1",WHERE);
            
            $this->db->setQuery((!empty($id_clt)? "AND id_clt = {$id_clt}" : ""),WHERE,true);

            $this->db->setQuery((!empty($id_regiao)? "AND regiao = {$id_regiao}" : ""),WHERE,true);

            $this->db->setQuery((!empty($id_projeto)? "AND projeto = {$id_projeto}" : ""),WHERE,true);
            
        }

       
        if($this->db->setRs()){
            
            return 1;
            
        }
        else {

            $this->error->setError($this->db->error->getError());            
            return 0;
            
        }        
        
    }     
    
    
    
}