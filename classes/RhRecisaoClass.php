<?php 
/*
 * Classe RhRecisaoClass
 * 
 * Dependência: 
 * 
 */


class RhRecisaoClass {
    
    private     $super_class;    
    protected   $error;
    private     $date;
    private     $db; 
    
    private     $rh_recisao_default = array(
                                      'id_rescisao',
                                      'id_clt',
                                      'nome',
                                      'id_regiao',
                                      'id_projeto',
                                      'id_curso',
                                      'data_adm',
                                      'data_demi',  
                                      'data_proc',
                                      'dias_saldo',
                                      'um_ano',
                                      'meses_ativo',
                                      'motivo',
                                      'fator',
                                      'aviso',
                                      'aviso_valor',
                                      'avos_dt',
                                      'avos_fp',
                                      'dias_aviso',
                                      'data_aviso',
                                      'data_fim_aviso',
                                      'fgts8',
                                      'fgts40',
                                      'fgts_anterior',
                                      'fgts_cod',
                                      'fgts_saque',
                                      'sal_base',
                                      'saldo_salario',
                                      'inss_ss',    
                                      'ir_ss',
                                      'terceiro_ss',
                                      'previdencia_ss',
                                      'dt_salario',
                                      'inss_dt',
                                      'ir_dt',
                                      'previdencia_dt',
                                      'ferias_vencidas',
                                      'umterco_fv',
                                      'ferias_pr',
                                      'umterco_fp',
                                      'inss_ferias',
                                      'ir_ferias',
                                      'sal_familia',
                                      'to_sal_fami',
                                      'ad_noturno',
                                      'adiantamento',
                                      'insalubridade',
                                      'ajuda_custo',
                                      'vale_refeicao',
                                      'debito_vale_refeicao',
                                      'a480',
                                      'a479',
                                      'a477',
                                      'comissao',
                                      'gratificacao',
                                      'extra',
                                      'outros',
                                      'movimentos',
                                      'valor_movimentos',
                                      'total_rendimentos',
                                      'total_deducao',
                                      'total_liquido',
                                      'arredondamento_positivo',
                                      'devolucao',
                                      'faltas',
                                      'valor_faltas',
                                      'user',
                                      'folha',
                                      'status',
                                      'adicional_noturno',
                                      'dsr',
                                      'desc_auxilio_distancia',
                                      'um_terco_das_ferias_dobro',
                                      'fv_dobro',
                                      'aux_distancia',
                                      'reembolso_vale_refeicao',
                                      'periculosidade',
                                      'desconto_vale_alimentacao',
                                      'diferenca_salarial',
                                      'ad_noturno_plantao',
                                      'desconto',
                                      'desc_vale_transporte',
                                      'pensao_alimenticia_15',
                                      'pensao_alimenticia_20',
                                      'pensao_alimenticia_30',
                                      'lei_12_506',
                                      'ferias_aviso_indenizado',
                                      'umterco_ferias_aviso_indenizado',
                                      'adiantamento_13',
                                      'fp_data_ini',
                                      'fp_data_fim',
                                      'fv_data_ini',
                                      'fv_data_fim',
                                      'qnt_dependente_salfamilia',
                                      'base_inss_ss',
                                      'percentual_inss_ss',
                                      'base_irrf_ss',
                                      'percentual_irrf_ss',
                                      'parcela_deducao_irrf_ss',
                                      'qnt_dependente_irrf_ss',
                                      'valor_ddir_ss',
                                      'base_fgts_ss',
                                      'base_inss_13',
                                      'percentual_inss_13',
                                      'base_irrf_13',
                                      'percentual_irrf_13',
                                      'parcela_deducao_irrf_13',
                                      'base_fgts_13',
                                      'qnt_dependente_irrf_13',
                                      'valor_ddir_13',
                                      'desconto_inss',
                                      'salario_outra_empresa',
                                      'desconto_inss_outra_empresa',
                                      'vinculo_id_rescisao',
                                      'rescisao_complementar',
                                      'rescisao_provisao_de_calculo',
                                      'id_recisao_lote',
                                      'reintegracao'
                                      );
    
    private     $rh_recisao = array();

    
    public function __construct() {
        
        
    }
    
    public function setRecisaoRecisaoDefault() {

        $this->rh_recisao = $this->rh_recisao_default;
        
    }
    
    public function setSuperClass($value) {
        
        $this->super_class = $value;
        
    }     

    public function setRecisaoIdRecisao($valor) {

            $this->rh_recisao['id_recisao'] = $valor;

    } 
    
    public function setRecisaoIdClt($valor) {

            $this->rh_recisao['id_clt'] = $valor;

    } 
    
    public function setRecisaoNome($valor) {

            $this->rh_recisao['nome'] = $valor;

    } 
    
    public function setRecisaoIdRegiao($valor) {

            $this->rh_recisao['id_regiao'] = $valor;

    } 
    
    public function setRecisaoIdProjeto($valor) {

            $this->rh_recisao['id_projeto'] = $valor;

    } 
    
    public function setRecisaoIdCurso($valor) {

            $this->rh_recisao['id_curso'] = $valor;

    } 
    
    public function setRecisaoDataAdm($valor) {

            $this->rh_recisao['data_adm'] = $valor;

    } 
    
    public function setRecisaoDataDemi($valor) {

            $this->rh_recisao['data_demi'] = $valor;

    } 
    
    public function setRecisaoDataProc($valor) {

            $this->rh_recisao['data_proc'] = $valor;

    } 
    
    public function setRecisaoDiasSaldo($valor) {

            $this->rh_recisao['dias_saldo'] = $valor;

    } 
    
    public function setRecisaoUmAno($valor) {

            $this->rh_recisao['um_ano'] = $valor;

    } 
    
    public function setRecisaoMesesAtivo($valor) {

            $this->rh_recisao['meses_ativo'] = $valor;

    } 
    
    public function setRecisaoMotivo($valor) {

            $this->rh_recisao['motivo'] = $valor;

    } 
    
    public function setRecisaoFator($valor) {

            $this->rh_recisao['fator'] = $valor;

    } 
    
    public function setRecisaoAviso($valor) {

            $this->rh_recisao['aviso'] = $valor;

    } 
    
    public function setRecisaoAvisoValor($valor) {

            $this->rh_recisao['aviso_valor'] = $valor;

    } 
    
    public function setRecisaoAvosDt($valor) {

            $this->rh_recisao['avos_dt'] = $valor;

    } 
    
    public function setRecisaoAvosFp($valor) {

            $this->rh_recisao['avos_fp'] = $valor;

    } 
    
    public function setRecisaoDiasAviso($valor) {

            $this->rh_recisao['dias_aviso'] = $valor;

    } 
    
    public function setRecisaoDataAviso($valor) {

            $this->rh_recisao['data_aviso'] = $valor;

    } 
    
    public function setRecisaoDataFimAviso($valor) {

            $this->rh_recisao['data_fim_aviso'] = $valor;

    } 
    
    public function setRecisaoFgts8($valor) {

            $this->rh_recisao['fgts8'] = $valor;

    } 
    
    public function setRecisaoFgts40($valor) {

            $this->rh_recisao['fgts40'] = $valor;

    } 
    
    public function setRecisaoFgtsAnteior($valor) {

            $this->rh_recisao['fgts_anterior'] = $valor;

    } 
    
    public function setRecisaoFgtsCod($valor) {

            $this->rh_recisao['fgts_cod'] = $valor;

    } 
    
    public function setRecisaoFgtsSaque($valor) {

            $this->rh_recisao['fgts_saque'] = $valor;

    } 
    
    public function setRecisaoSalBase($valor) {

            $this->rh_recisao['sal_base'] = $valor;

    } 
    
    public function setRecisaoSaldoSalario($valor) {

            $this->rh_recisao['saldo_salario'] = $valor;

    } 
    
    public function setRecisaoInssSs($valor) {

            $this->rh_recisao['inss_ss'] = $valor;

    } 
    
    public function setRecisaoIrSs($valor) {

            $this->rh_recisao['ir_ss'] = $valor;

    } 
    
    public function setRecisaoTerceiroSs($valor) {

            $this->rh_recisao['terceiro_ss'] = $valor;

    } 
    
    public function setRecisaoPrevidenciaSs($valor) {

            $this->rh_recisao['previdencia_ss'] = $valor;

    } 
    
    public function setRecisaoDtSalario($valor) {

            $this->rh_recisao['dt_salario'] = $valor;

    } 
    
    public function setRecisaoInssDt($valor) {

            $this->rh_recisao['inss_dt'] = $valor;

    } 
    
    public function setRecisaoIrClt($valor) {

            $this->rh_recisao['ir_dt'] = $valor;

    } 
    
    public function setRecisaoPrevidenciaClt($valor) {

            $this->rh_recisao['previdencia_dt'] = $valor;

    } 
    
    public function setRecisaoFeriasVencidas($valor) {

            $this->rh_recisao['ferias_vencidas'] = $valor;

    } 
    
    public function setRecisaoUmTercoFv($valor) {

            $this->rh_recisao['umterco_fv'] = $valor;

    } 
    
    public function setRecisaoFeriasPr($valor) {

            $this->rh_recisao['ferias_pr'] = $valor;

    } 
    
    public function setRecisaoUmTercoFp($valor) {

            $this->rh_recisao['umterco_fp'] = $valor;

    } 
    
    public function setRecisaoInssFerias($valor) {

            $this->rh_recisao['inss_ferias'] = $valor;

    } 
    
    public function setRecisaoIrFerias($valor) {

            $this->rh_recisao['ir_ferias'] = $valor;

    } 
    
    public function setRecisaoSalFamilia($valor) {

            $this->rh_recisao['sal_familia'] = $valor;

    } 
    
    public function setRecisaoToSalFami($valor) {

            $this->rh_recisao['to_sal_fami'] = $valor;

    } 
    
    public function setRecisaoAdNoturno($valor) {

            $this->rh_recisao['ad_noturno'] = $valor;

    } 
    
    public function setRecisaoAdiantamento($valor) {

            $this->rh_recisao['adiantamento'] = $valor;

    } 
    
    public function setRecisaoInsalubridade($valor) {

            $this->rh_recisao['insalubridade'] = $valor;

    } 
    
    public function setRecisaoAjudaCusto($valor) {

            $this->rh_recisao['ajuda_custo'] = $valor;

    } 
    
    public function setRecisaoValeRefeicao($valor) {

            $this->rh_recisao['vale_refeicao'] = $valor;

    } 
    
    public function setRecisaoDebitoValeRefeicao($valor) {

            $this->rh_recisao['debito_vale_refeicao'] = $valor;

    } 
    
    public function setFRecisaoA480($valor) {

            $this->rh_recisao['a480'] = $valor;

    } 
    
    public function setRecisaoA479($valor) {

            $this->rh_recisao['a479'] = $valor;

    } 
    
    public function setRecisaoA477($valor) {

            $this->rh_recisao['a477'] = $valor;

    } 
    
    public function setRecisaoComissao($valor) {

            $this->rh_recisao['comissao'] = $valor;

    } 
    
    public function setRecisaoGratificacao($valor) {

            $this->rh_recisao['gratificacao'] = $valor;

    } 
    
    public function setRecisaoExtra($valor) {

            $this->rh_recisao['extra'] = $valor;

    } 
    
    public function setRecisaoOutros($valor) {

            $this->rh_recisao['outros'] = $valor;

    } 
    
    public function setRecisaoMovimentos($valor) {

            $this->rh_recisao['movimentos'] = $valor;

    } 
    
    public function setRecisaoValorMovimentos($valor) {

            $this->rh_recisao['valor_movimentos'] = $valor;

    } 
    
    public function setRecisaoTotalRendimento($valor) {

            $this->rh_recisao['total_rendimento'] = $valor;

    } 
    
    public function setRecisaoTotalDecucao($valor) {

            $this->rh_recisao['total_deducao'] = $valor;

    } 
    
    public function setRecisaoTotalLiquido($valor) {

            $this->rh_recisao['total_liquido'] = $valor;

    } 
    
    public function setRecisaoArredondamentoPositivo($valor) {

            $this->rh_recisao['arredondamento_positivo'] = $valor;

    } 
    
    public function setRecisaoDevolucao($valor) {

            $this->rh_recisao['devolucao'] = $valor;

    } 
    
    public function setRecisaofaltas($valor) {

            $this->rh_recisao['faltas'] = $valor;

    } 
    
    public function setRecisaoValorFaltas($valor) {

            $this->rh_recisao['valor_faltas'] = $valor;

    } 
    
    public function setRecisaoUser($valor) {

            $this->rh_recisao['user'] = $valor;

    } 
    
    public function setRecisaoFolha($valor) {

            $this->rh_recisao['folha'] = $valor;

    } 
    
    public function setRecisaoStatus($valor) {

            $this->rh_recisao['status'] = $valor;

    } 
    
    public function setRecisaoAdicionalNoturno($valor) {

            $this->rh_recisao['adicional_noturno'] = $valor;

    } 
    
    public function setRecisaoDsr($valor) {

            $this->rh_recisao['dsr'] = $valor;

    } 
    
    public function setRecisaoDescAuxilioDistancia($valor) {

            $this->rh_recisao['desc_auxilio_distancia'] = $valor;

    } 
    
    public function setRecisaoUmTercoFeriasDobro($valor) {

            $this->rh_recisao['um_terco_ferias_dobro'] = $valor;

    } 
    
    public function setRecisaoFvDobro($valor) {

            $this->rh_recisao['fv_dobro'] = $valor;

    } 
    
    public function setRecisaoAuxDistancia($valor) {

            $this->rh_recisao['aux_distancia'] = $valor;

    } 
    
    public function setRecisaoReembolsoValeRefeicao($valor) {

            $this->rh_recisao['reembolso_vale_refeicao'] = $valor;

    } 
    
    public function setRecisaoPericulosidade($valor) {

            $this->rh_recisao['periculosidade'] = $valor;

    } 
    
    public function setRecisaoDescontoValeAlimentacao($valor) {

            $this->rh_recisao['desconto_vale_alimentacao'] = $valor;

    } 
    
    public function setRecisaoDiferencaSalarial($valor) {

            $this->rh_recisao['diferenca_salarial'] = $valor;

    } 
    
    public function setRecisaoAdNoturnoPlantao($valor) {

            $this->rh_recisao['ad_noturno_plantao'] = $valor;

    } 
    
    public function setRecisaoDesconto($valor) {
        
            $this->rh_recisao['desconto'] = $valor;

    } 
    
    public function setRecisaoDescValeTransporte($valor) {

            $this->rh_recisao['desc_vale_transporte'] = $valor;

    } 
    
    public function setRecisaoPensaoAlimenticia15($valor) {

            $this->rh_recisao['pensao_alimenticia_15'] = $valor;

    } 
    
    public function setRecisaoPensaoAlimenticia20($valor) {

            $this->rh_recisao['pensao_alimenticia_20'] = $valor;

    } 
    
    public function setRecisaoPensaoAlimenticia30($valor) {

            $this->rh_recisao['pensao_alimenticia_30'] = $valor;

    } 
    
    public function setRecisaoLei12506($valor) {

            $this->rh_recisao['lei_12_506'] = $valor;

    } 
    
    public function setRecisaoFeriasAvisoIndenizado($valor) { //xxxxxxxxxxx

            $this->rh_recisao['ferias_aviso_indenizado'] = $valor;

    } 
    
    public function setRecisaoumterco_ferias_aviso_indenizado($valor) {

            $this->rh_recisao['umterco_ferias_aviso_indenizado'] = $valor;

    } 
    
    public function setRecisaoAdiantamento_13($valor) {

            $this->rh_recisao['adiantamento_13'] = $valor;

    } 
    
    public function setRecisaofp_data_ini($valor) {

            $this->rh_recisao['fp_data_ini'] = $valor;

    } 
    
    public function setRecisaofp_data_fim($valor) {

            $this->rh_recisao['fp_data_fim'] = $valor;

    } 
    
    public function setRecisaofv_data_ini($valor) {

            $this->rh_recisao['fv_data_ini'] = $valor;

    } 
    
    public function setRecisaofv_data_fim($valor) {

            $this->rh_recisao['fv_data_fim'] = $valor;

    } 
    
    public function setRecisaoqnt_dependente_salfamilia($valor) {

            $this->rh_recisao['qnt_dependente_salfamilia'] = $valor;

    } 
    
    public function setRecisaobase_inss_ss($valor) {

            $this->rh_recisao['base_inss_ss'] = $valor;

    } 
    
    public function setRecisaopercentual_inss_ss($valor) {

            $this->rh_recisao['percentual_inss_ss'] = $valor;

    } 
    
    public function setRecisaobase_irrf_ss($valor) {

            $this->rh_recisao['base_irrf_ss'] = $valor;

    } 
    
    public function setRecisaopercentual_irrf_ss($valor) {

            $this->rh_recisao['percentual_irrf_ss'] = $valor;

    } 
    
    public function setRecisaoparcela_deducao_irrf_ss($valor) {

            $this->rh_recisao['parcela_deducao_irrf_ss'] = $valor;

    } 
    
    public function setRecisaoqnt_dependente_irrf_ss($valor) {

            $this->rh_recisao['qnt_dependente_irrf_ss'] = $valor;

    } 
    
    public function setRecisaovalor_ddir_ss($valor) {

            $this->rh_recisao['valor_ddir_ss'] = $valor;

    } 
    
    public function setRecisaobase_fgts_ss($valor) {

            $this->rh_recisao['base_fgts_ss'] = $valor;

    } 
    
    public function setRecisaobase_inss_13($valor) {

            $this->rh_recisao['base_inss_13'] = $valor;

    } 
    
    public function setRecisaopercentual_inss_13($valor) {

            $this->rh_recisao['percentual_inss_13'] = $valor;

    } 
    
    public function setRecisaobase_irrf_13($valor) {

            $this->rh_recisao['base_irrf_13'] = $valor;

    } 
    
    public function setRecisaopercentual_irrf_13($valor) {

            $this->rh_recisao['percentual_irrf_13'] = $valor;

    } 
    
    public function setRecisaoparcela_deducao_irrf_13($valor) {

            $this->rh_recisao['parcela_deducao_irrf_13'] = $valor;

    }
    
    public function setRecisaobase_fgts_13($valor) {

            $this->rh_recisao['base_fgts_13'] = $valor;

    }
    
    public function setRecisaoqnt_dependente_irrf_13($valor) {

            $this->rh_recisao['qnt_dependente_irrf_13'] = $valor;

    }
    
    public function setRecisaovalor_ddir_13($valor) {

            $this->rh_recisao['valor_ddir_13'] = $valor;

    }
    
    public function setRecisaoDesconto_inss($valor) {

            $this->rh_recisao['desconto_inss'] = $valor;

    }
    
    public function setRecisaosalario_outra_empresa($valor) {

            $this->rh_recisao['salario_outra_empresa'] = $valor;

    }
    
    public function setRecisaoDesconto_inss_outra_empresa($valor) {

            $this->rh_recisao['desconto_inss_outra_empresa'] = $valor;

    }
    
    public function setRecisaoVinculoId($valor) {

            $this->rh_recisao['vinculo_id_rescisao'] = $valor;

    }
    
    public function setRecisaorescisao_complementar($valor) {

            $this->rh_recisao['rescisao_complementar'] = $valor;

    }
    
    public function setRecisaorecisao_provisao_de_calculo($valor) {

            $this->rh_recisao['recisao_provisao_de_calculo'] = $valor;

    }
    
    public function setRecisaoid_recisao_lote($valor) {

            $this->rh_recisao['id_recisao_lote'] = $valor;

    }
    
    public function setRecisaoreintegracao($valor) {

            $this->rh_recisao['reintegracao'] = $valor;

    } 

    public function getSuperClass() {
        
        return $this->super_class;
        
    } 
    
    public function getRecisaoid_recisao() {

            return $this->rh_recisao['id_recisao'];

    }
    
    public function getRecisaoIdClt() {

            return $this->rh_recisao['id_clt'];

    }
    
    public function getRecisaoNome() {

            return $this->rh_recisao['nome'];

    } 
    
    public function getRecisaoIdRegiao() {

            return $this->rh_recisao['id_regiao'];

    }
    
    public function getRecisaoIdProjeto() {

            return $this->rh_recisao['id_projeto'];

    }
    
    public function getRecisaoIdCurso() {

            return $this->rh_recisao['id_curso'];

    }
    
    public function getRecisaoDataAdm() {

            return $this->rh_recisao['data_adm'];

    }
    
    public function getRecisaoDataDemi() {

            return $this->rh_recisao['data_demi'];

    }
    
    public function getRecisaoDataProc() {

            return $this->rh_recisao['data_proc'];

    }
    
    public function getRecisaoDiasSaldo() {

            return $this->rh_recisao['dias_saldo'];

    } 
    public function getRecisaoUmAno() {

            return $this->rh_recisao['um_ano'];

    }
    
    public function getRecisaoMesesAtivo() {

            return $this->rh_recisao['meses_ativo'];

    }
    
    public function getRecisaoMotivo() {

            return $this->rh_recisao['motivo'];

    }
    
    public function getRecisaoFator() {

            return $this->rh_recisao['fator'];

    }
    
    public function getRecisaoaviso() {

            return $this->rh_recisao['aviso'];

    }
    
    public function getRecisaoAvisoValor() {

            return $this->rh_recisao['aviso_valor'];

    }
    
    public function getRecisaoAvosDt() {

            return $this->rh_recisao['avos_dt'];

    }
    
    public function getRecisaoAvosFp() {

            return $this->rh_recisao['avos_fp'];

    }
    
    public function getRecisaoDiasAviso() {

            return $this->rh_recisao['dias_aviso'];

    }
    
    public function getRecisaoDataAviso() {

            return $this->rh_recisao['data_aviso'];

    }
    
    public function getRecisaoDataFimAviso() {

            return $this->rh_recisao['data_fim_aviso'];

    }
    
    public function getRecisaofgts8() {

            return $this->rh_recisao['fgts8'];

    }
    
    public function getRecisaoFgts40() {

            return $this->rh_recisao['fgts40'];

    }
    
    public function getRecisaofgts_anterior() {

            return $this->rh_recisao['fgts_anterior'];

    }
    
    public function getRecisaoFgtsCod() {

            return $this->rh_recisao['fgts_cod'];

    }
    
    public function getRecisaoFgtsSaque() {

            return $this->rh_recisao['fgts_saque'];

    }
    
    public function getRecisaoSalBase() {

            return $this->rh_recisao['sal_base'];

    }
    
    public function getRecisaoSaldoSalario() {

            return $this->rh_recisao['saldo_salario'];

    }
    
    public function getRecisaoInssSs() {

            return $this->rh_recisao['inss_ss'];

    }
    
    public function getRecisaoIrSs() {

            return $this->rh_recisao['ir_ss'];

    }
    
    public function getRecisaoTerceiroSs() {

            return $this->rh_recisao['terceiro_ss'];

    }
    
    public function getRecisaoPrevidenciaSs() {

            return $this->rh_recisao['previdencia_ss'];

    } 
    
    public function getRecisaoDtSalario() {

            return $this->rh_recisao['dt_salario'];

    }
    
    public function getRecisaoinss_dt() {

            return $this->rh_recisao['inss_dt'];

    }
    
    public function getRecisaoir_dt() {

            return $this->rh_recisao['ir_dt'];

    }
    
    public function getRecisaoprevidencia_dt() {

            return $this->rh_recisao['previdencia_dt'];

    }
    
    public function getRecisaoferias_vencidas() {

            return $this->rh_recisao['ferias_vencidas'];

    }
    
    public function getRecisaoumterco_fv() {

            return $this->rh_recisao['umterco_fv'];

    }
    
    public function getRecisaoferias_pr() {

            return $this->rh_recisao['ferias_pr'];

    }
    
    public function getRecisaoumterco_fp() {

            return $this->rh_recisao['umterco_fp'];

    }
    
    public function getRecisaoinss_ferias() {

            return $this->rh_recisao['inss_ferias'];

    }
    
    public function getRecisaoir_ferias() {

            return $this->rh_recisao['ir_ferias'];

    }
    
    public function getRecisaosal_familia() {

            return $this->rh_recisao['sal_familia'];

    }
    
    public function getRecisaoto_sal_fami() {

            return $this->rh_recisao['to_sal_fami'];

    }
    
    public function getRecisaoad_noturno() {

            return $this->rh_recisao['ad_noturno'];

    }
    
    public function getRecisaoAdiantamento() {

            return $this->rh_recisao['adiantamento'];

    }
    
    public function getRecisaoInsalubridade() {

            return $this->rh_recisao['insalubridade'];

    }
    
    public function getRecisaoAjudaCusto() {

            return $this->rh_recisao['ajuda_custo'];

    }
    
    public function getRecisaoValeRefeicao() {

            return $this->rh_recisao['vale_refeicao'];

    }
    
    public function getRecisaoDebitoValeRefeicao() {

            return $this->rh_recisao['debito_vale_refeicao'];

    }
    
    public function getRecisaoa480() {

            return $this->rh_recisao['a480'];

    }
    
    public function getRecisaoa479() {

            return $this->rh_recisao['a479'];

    }
    
    public function getRecisaoa477() {

            return $this->rh_recisao['a477'];

    }
    
    public function getRecisaoComissao() {

            return $this->rh_recisao['comissao'];

    }
    
    public function getRecisaoGratificacao() {

            return $this->rh_recisao['gratificacao'];

    }
    
    public function getRecisaoExtra() {

            return $this->rh_recisao['extra'];

    }
    
    public function getRecisaoOutros() {

            return $this->rh_recisao['outros'];

    }
    
    public function getRecisaoMovimentos() {

            return $this->rh_recisao['movimentos'];

    }
    
    public function getRecisaoValorMovimentos() {

            return $this->rh_recisao['valor_movimentos'];

    }
    
    public function getRecisaoTotalRendimento() {

            return $this->rh_recisao['total_rendimento'];

    }
    
    public function getRecisaoTotalDeducao() {

            return $this->rh_recisao['total_deducao'];

    }
    
    public function getRecisaoTotalLiquido() {

            return $this->rh_recisao['total_liquido'];

    }
    
    public function getRecisaoArredondamentoPositivo() {

            return $this->rh_recisao['arredondamento_positivo'];

    }
    
    public function getRecisaoDevolucao() {

            return $this->rh_recisao['devolucao'];

    }
    
    public function getRecisaofaltas() {

            return $this->rh_recisao['faltas'];

    }
    
    public function getRecisaovalor_faltas() {

            return $this->rh_recisao['valor_faltas'];

    }
    
    public function getRecisaouser() {

            return $this->rh_recisao['user'];

    }
    
    public function getRecisaofolha() {

            return $this->rh_recisao['folha'];

    }
    
    public function getRecisaostatus() {

            return $this->rh_recisao['status'];

    }
    
    public function getRecisaoadicional_noturno() {

            return $this->rh_recisao['adicional_noturno'];

    }
    
    public function getRecisaodsr() {

            return $this->rh_recisao['dsr'];

    }
    
    public function getRecisaodesc_auxilio_distancia() {

            return $this->rh_recisao['desc_auxilio_distancia'];

    }
    
    public function getRecisaoum_terco_ferias_dobro() {

            return $this->rh_recisao['um_terco_ferias_dobro'];

    }
    
    public function getRecisaofv_dobro() {

            return $this->rh_recisao['fv_dobro'];

    }
    
    public function getRecisaoaux_distancia() {

            return $this->rh_recisao['aux_distancia'];

    }
    
    public function getRecisaoreembolso_vale_refeicao() {

            return $this->rh_recisao['reembolso_vale_refeicao'];

    }
    
    public function getRecisaopericulosidade() {

            return $this->rh_recisao['periculosidade'];

    } 
    
    public function getRecisaodesconto_vale_alimentacao() {

            return $this->rh_recisao['desconto_vale_alimentacao'];

    } 
    
    public function getRecisaodiferenca_salarial() {

            return $this->rh_recisao['diferenca_salarial'];

    } 
    
    public function getRecisaoad_noturno_plantao() {

            return $this->rh_recisao['ad_noturno_plantao'];

    } 
    
    public function getRecisaodesconto() {

            return $this->rh_recisao['desconto'];

    } 
    
    public function getRecisaodesc_vale_transporte() {

            return $this->rh_recisao['desc_vale_transporte'];

    } 
    
    public function getRecisaopensao_alimenticia_15() {

            return $this->rh_recisao['pensao_alimenticia_15'];

    } 
    
    public function getRecisaopensao_alimenticia_20() {

            return $this->rh_recisao['pensao_alimenticia_20'];

    } 
    
    public function getRecisaopensao_alimenticia_30() {

            return $this->rh_recisao['pensao_alimenticia_30'];

    } 
    
    public function getRecisaolei_12_506() {

            return $this->rh_recisao['lei_12_506'];

    } 
    
    public function getRecisaoferias_aviso_indenizado() {

            return $this->rh_recisao['ferias_aviso_indenizado'];

    } 
    
    public function getRecisaoumterco_ferias_aviso_indenizado() {

            return $this->rh_recisao['umterco_ferias_aviso_indenizado'];

    } 
    
    public function getRecisaoAdiantamento_13() {

            return $this->rh_recisao['adiantamento_13'];

    } 
    
    public function getRecisaofp_data_ini() {

            return $this->rh_recisao['fp_data_ini'];

    } 
    public function getRecisaofp_data_fim() {

            return $this->rh_recisao['fp_data_fim'];

    } 
    
    public function getRecisaofv_data_ini() {

            return $this->rh_recisao['fv_data_ini'];

    } 
    
    public function getRecisaofv_data_fim() {

            return $this->rh_recisao['fv_data_fim'];

    } 
    
    public function getRecisaoqnt_dependente_salfamilia() {

            return $this->rh_recisao['qnt_dependente_salfamilia'];

    } 
    
    public function getRecisaobase_inss_ss() {

            return $this->rh_recisao['base_inss_ss'];

    } 
    
    public function getRecisaopercentual_inss_ss() {

            return $this->rh_recisao['percentual_inss_ss'];

    } 
    
    public function getRecisaobase_irrf_ss() {

            return $this->rh_recisao['base_irrf_ss'];

    } 
    
    public function getRecisaopercentual_irrf_ss() {

            return $this->rh_recisao['percentual_irrf_ss'];

    } 
    
    public function getRecisaoparcela_deducao_irrf_ss() {

            return $this->rh_recisao['parcela_deducao_irrf_ss'];

    } 
    
    public function getRecisaoqnt_dependente_irrf_ss() {

            return $this->rh_recisao['qnt_dependente_irrf_ss'];

    } 
    
    public function getRecisaovalor_ddir_ss() {

            return $this->rh_recisao['valor_ddir_ss'];

    } 
    
    public function getRecisaobase_fgts_ss() {

            return $this->rh_recisao['base_fgts_ss'];

    } 
    
    public function getRecisaobase_inss_13() {

            return $this->rh_recisao['base_inss_13'];

    } 
    
    public function getRecisaopercentual_inss_13() {

            return $this->rh_recisao['percentual_inss_13'];

    } 
    
    public function getRecisaobase_irrf_13() {

            return $this->rh_recisao['base_irrf_13'];

    } 
    
    public function getRecisaopercentual_irrf_13() {

            return $this->rh_recisao['percentual_irrf_13'];

    } 
    
    public function getRecisaoparcela_deducao_irrf_13() {

            return $this->rh_recisao['parcela_deducao_irrf_13'];

    } 
    
    public function getRecisaobase_fgts_13() {

            return $this->rh_recisao['base_fgts_13'];

    } 
    
    public function getRecisaoqnt_dependente_irrf_13() {

            return $this->rh_recisao['qnt_dependente_irrf_13'];

    } 
    
    public function getRecisaovalor_ddir_13() {

            return $this->rh_recisao['valor_ddir_13'];

    } 
    
    public function getRecisaodesconto_inss() {

            return $this->rh_recisao['desconto_inss'];

    } 
    
    public function getRecisaosalario_outra_empresa() {

            return $this->rh_recisao['salario_outra_empresa'];

    } 
    
    public function getRecisaodesconto_inss_outra_empresa() {

            return $this->rh_recisao['desconto_inss_outra_empresa'];

    } 
    
    public function getRecisaoVinculoId() {

            return $this->rh_recisao['vinculo_id_rescisao'];

    } 
    
    public function getRecisaorescisao_complementar() {

            return $this->rh_recisao['rescisao_complementar'];

    } 
    
    public function getRecisaorecisao_provisao_de_calculo() {

            return $this->rh_recisao['recisao_provisao_de_calculo'];

    } 
    
    public function getRecisaoid_recisao_lote() {

            return $this->rh_recisao['id_recisao_lote'];

    } 
    
    public function getRecisaoreintegracao() {

            return $this->rh_recisao['reintegracao'];

    } 
    
    public function getError(){
        
        return $this->error->getError();
        
    }    

    
    public function getRecisaoId(){
        
        return $this->rh_recisao['id_ferias'];
        
    } 
    
    
    public function getRecisaoRow(){
        
        
        if($this->db->setRow()){

//            $this->setRecisaoVinculoId($this->db->getRow('vinculo_id_rescisao')); 
//            $this->setvalor_movimentos($this->db->getRow('valor_movimentos')); 
//            $this->setvalor_faltas($this->db->getRow('valor_faltas')); 
//            $this->setvalor_ddir_ss($this->db->getRow('valor_ddir_ss')); 
//            $this->setvalor_ddir_13($this->db->getRow('valor_ddir_13')); 
//            $this->setvale_refeicao($this->db->getRow('vale_refeicao')); 
//            $this->setuser($this->db->getRow('user')); 
//            $this->setum_terco_ferias_dobro($this->db->getRow('um_terco_ferias_dobro')); 
//            $this->setum_ano($this->db->getRow('um_ano')); 
//            $this->setumterco_fv($this->db->getRow('umterco_fv')); 
//            $this->setumterco_fp($this->db->getRow('umterco_fp')); 
//            $this->setumterco_ferias_aviso_indenizado($this->db->getRow('umterco_ferias_aviso_indenizado')); 
//            $this->setto_sal_fami($this->db->getRow('to_sal_fami')); 
//            $this->settotal_rendimento($this->db->getRow('total_rendimento')); 
//            $this->settotal_liquido($this->db->getRow('total_liquido')); 
//            $this->settotal_deducao($this->db->getRow('total_deducao')); 
//            $this->setterceiro_ss($this->db->getRow('terceiro_ss')); 
//            $this->setstatus($this->db->getRow('status')); 
//            $this->setsal_familia($this->db->getRow('sal_familia')); 
//            $this->setsal_base($this->db->getRow('sal_base')); 
//            $this->setsaldo_salario($this->db->getRow('saldo_salario')); 
//            $this->setsalario_outra_empresa($this->db->getRow('salario_outra_empresa')); 
//            $this->setrescisao_complementar($this->db->getRow('rescisao_complementar')); 
//            $this->setreintegracao($this->db->getRow('reintegracao')); 
//            $this->setreembolso_vale_refeicao($this->db->getRow('reembolso_vale_refeicao')); 
//            $this->setrecisao_provisao_de_calculo($this->db->getRow('recisao_provisao_de_calculo')); 
//            $this->setqnt_dependente_salfamilia($this->db->getRow('qnt_dependente_salfamilia')); 
//            $this->setqnt_dependente_irrf_ss($this->db->getRow('qnt_dependente_irrf_ss')); 
//            $this->setqnt_dependente_irrf_13($this->db->getRow('qnt_dependente_irrf_13')); 
//            $this->setprevidencia_ss($this->db->getRow('previdencia_ss')); 
//            $this->setprevidencia_dt($this->db->getRow('previdencia_dt')); 
//            $this->setpericulosidade($this->db->getRow('periculosidade')); 
//            $this->setpercentual_irrf_ss($this->db->getRow('percentual_irrf_ss')); 
//            $this->setpercentual_irrf_13($this->db->getRow('percentual_irrf_13')); 
//            $this->setpercentual_inss_ss($this->db->getRow('percentual_inss_ss')); 
//            $this->setpercentual_inss_13($this->db->getRow('percentual_inss_13')); 
//            $this->setpensao_alimenticia_30($this->db->getRow('pensao_alimenticia_30')); 
//            $this->setpensao_alimenticia_20($this->db->getRow('pensao_alimenticia_20')); 
//            $this->setpensao_alimenticia_15($this->db->getRow('pensao_alimenticia_15')); 
//            $this->setparcela_deducao_irrf_ss($this->db->getRow('parcela_deducao_irrf_ss')); 
//            $this->setparcela_deducao_irrf_13($this->db->getRow('parcela_deducao_irrf_13')); 
//            $this->setoutros($this->db->getRow('outros')); 
//            $this->setNome($this->db->getRow('nome')); 
//            $this->setmovimentos($this->db->getRow('movimentos')); 
//            $this->setmotivo($this->db->getRow('motivo')); 
//            $this->setmeses_ativo($this->db->getRow('meses_ativo')); 
//            $this->setlei_12_506($this->db->getRow('lei_12_506')); 
//            $this->setir_ss($this->db->getRow('ir_ss')); 
//            $this->setir_ferias($this->db->getRow('ir_ferias')); 
//            $this->setir_dt($this->db->getRow('ir_dt')); 
//            $this->setinss_ss($this->db->getRow('inss_ss')); 
//            $this->setinss_ferias($this->db->getRow('inss_ferias')); 
//            $this->setinss_dt($this->db->getRow('inss_dt')); 
//            $this->setinsalubridade($this->db->getRow('insalubridade')); 
//            $this->setid_regiao($this->db->getRow('id_regiao')); 
//            $this->setid_recisao_lote($this->db->getRow('id_recisao_lote')); 
//            $this->setid_recisao($this->db->getRow('id_recisao')); 
//            $this->setid_projeto($this->db->getRow('id_projeto')); 
//            $this->setid_curso($this->db->getRow('id_curso')); 
//            $this->setIdClt($this->db->getRow('id_clt')); 
//            $this->setgratificacao($this->db->getRow('gratificacao')); 
//            $this->setfv_dobro($this->db->getRow('fv_dobro')); 
//            $this->setfv_data_ini($this->db->getRow('fv_data_ini')); 
//            $this->setfv_data_fim($this->db->getRow('fv_data_fim')); 
//            $this->setfp_data_ini($this->db->getRow('fp_data_ini')); 
//            $this->setfp_data_fim($this->db->getRow('fp_data_fim')); 
//            $this->setfolha($this->db->getRow('folha')); 
//            $this->setfgts_saque($this->db->getRow('fgts_saque')); 
//            $this->setfgts_cod($this->db->getRow('fgts_cod')); 
//            $this->setfgts_anterior($this->db->getRow('fgts_anterior')); 
//            $this->setfgts8($this->db->getRow('fgts8')); 
//            $this->setfgts40($this->db->getRow('fgts40')); 
//            $this->setferias_vencidas($this->db->getRow('ferias_vencidas')); 
//            $this->setferias_pr($this->db->getRow('ferias_pr')); 
//            $this->setferias_aviso_indenizado($this->db->getRow('ferias_aviso_indenizado')); 
//            $this->setfator($this->db->getRow('fator')); 
//            $this->setfaltas($this->db->getRow('faltas')); 
//            $this->setextra($this->db->getRow('extra')); 
//            $this->setdt_salario($this->db->getRow('dt_salario')); 
//            $this->setdsr($this->db->getRow('dsr')); 
//            $this->setdiferenca_salarial($this->db->getRow('diferenca_salarial')); 
//            $this->setdias_saldo($this->db->getRow('dias_saldo')); 
//            $this->setdias_aviso($this->db->getRow('dias_aviso')); 
//            $this->setdevolucao($this->db->getRow('devolucao')); 
//            $this->setdesc_vale_transporte($this->db->getRow('desc_vale_transporte')); 
//            $this->setdesc_auxilio_distancia($this->db->getRow('desc_auxilio_distancia')); 
//            $this->setdesconto_vale_alimentacao($this->db->getRow('desconto_vale_alimentacao')); 
//            $this->setdesconto_inss_outra_empresa($this->db->getRow('desconto_inss_outra_empresa')); 
//            $this->setdesconto_inss($this->db->getRow('desconto_inss')); 
//            $this->setdesconto($this->db->getRow('desconto')); 
//            $this->setdebito_vale_refeicao($this->db->getRow('debito_vale_refeicao')); 
//            $this->setdata_proc($this->db->getRow('data_proc')); 
//            $this->setdata_fim_aviso($this->db->getRow('data_fim_aviso')); 
//            $this->setDataDemi($this->db->getRow('data_demi')); 
//            $this->setdata_aviso($this->db->getRow('data_aviso')); 
//            $this->setdata_adm($this->db->getRow('data_adm')); 
//            $this->setcomissao($this->db->getRow('comissao')); 
//            $this->setbase_irrf_ss($this->db->getRow('base_irrf_ss')); 
//            $this->setbase_irrf_13($this->db->getRow('base_irrf_13')); 
//            $this->setbase_inss_ss($this->db->getRow('base_inss_ss')); 
//            $this->setbase_inss_13($this->db->getRow('base_inss_13')); 
//            $this->setbase_fgts_ss($this->db->getRow('base_fgts_ss')); 
//            $this->setbase_fgts_13($this->db->getRow('base_fgts_13')); 
//            $this->setavos_fp($this->db->getRow('avos_fp')); 
//            $this->setavos_dt($this->db->getRow('avos_dt')); 
//            $this->setaviso_valor($this->db->getRow('aviso_valor')); 
//            $this->setaviso($this->db->getRow('aviso')); 
//            $this->setaux_distancia($this->db->getRow('aux_distancia')); 
//            $this->setarredondamento_positivo($this->db->getRow('arredondamento_positivo')); 
//            $this->setajuda_custo($this->db->getRow('ajuda_custo')); 
//            $this->setad_noturno_plantao($this->db->getRow('ad_noturno_plantao')); 
//            $this->setad_noturno($this->db->getRow('ad_noturno')); 
//            $this->setadicional_noturno($this->db->getRow('adicional_noturno')); 
//            $this->setadiantamento_13($this->db->getRow('adiantamento_13')); 
//            $this->setadiantamento($this->db->getRow('adiantamento')); 
//            $this->seta480($this->db->getRow('a480')); 
//            $this->seta479($this->db->getRow('a479')); 
//            $this->seta477($this->db->getRow('a477')); 
            
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
    
    public function selectRecisao(){
        
        $this->createCoreClass();
                
        $this->db->setQuery("SELECT 
                                vinculo_id_rescisao, 
                                valor_movimentos, 
                                valor_faltas, 
                                valor_ddir_ss, 
                                valor_ddir_13, 
                                vale_refeicao, 
                                user, 
                                um_terco_ferias_dobro, 
                                um_ano, 
                                umterco_fv, 
                                umterco_fp, 
                                umterco_ferias_aviso_indenizado, 
                                to_sal_fami, 
                                total_rendimento, 
                                total_liquido, 
                                total_deducao, 
                                terceiro_ss, 
                                status, 
                                sal_familia, 
                                sal_base, 
                                saldo_salario, 
                                salario_outra_empresa, 
                                rescisao_complementar, 
                                reintegracao, 
                                reembolso_vale_refeicao, 
                                recisao_provisao_de_calculo, 
                                qnt_dependente_salfamilia, 
                                qnt_dependente_irrf_ss, 
                                qnt_dependente_irrf_13, 
                                previdencia_ss, 
                                previdencia_dt, 
                                periculosidade, 
                                percentual_irrf_ss, 
                                percentual_irrf_13, 
                                percentual_inss_ss, 
                                percentual_inss_13, 
                                pensao_alimenticia_30, 
                                pensao_alimenticia_20, 
                                pensao_alimenticia_15, 
                                parcela_deducao_irrf_ss, 
                                parcela_deducao_irrf_13, 
                                outros, 
                                nome, 
                                movimentos, 
                                motivo, 
                                meses_ativo, 
                                lei_12_506, 
                                ir_ss, 
                                ir_ferias, 
                                ir_dt, 
                                inss_ss, 
                                inss_ferias, 
                                inss_dt, 
                                insalubridade, 
                                id_regiao, 
                                id_recisao_lote, 
                                id_recisao, 
                                id_projeto, 
                                id_curso, 
                                id_clt, 
                                gratificacao, 
                                fv_dobro, 
                                fv_data_ini, 
                                fv_data_fim, 
                                fp_data_ini, 
                                fp_data_fim, 
                                folha, 
                                fgts_saque, 
                                fgts_cod, 
                                fgts_anterior, 
                                fgts8, 
                                fgts40, 
                                ferias_vencidas, 
                                ferias_pr, 
                                ferias_aviso_indenizado, 
                                fator, 
                                faltas, 
                                extra, 
                                dt_salario, 
                                dsr, 
                                diferenca_salarial, 
                                dias_saldo, 
                                dias_aviso, 
                                devolucao, 
                                desc_vale_transporte, 
                                desc_auxilio_distancia, 
                                desconto_vale_alimentacao, 
                                desconto_inss_outra_empresa, 
                                desconto_inss, 
                                desconto, 
                                debito_vale_refeicao, 
                                data_proc, 
                                data_fim_aviso, 
                                data_demi, 
                                data_aviso, 
                                data_adm, 
                                comissao, 
                                base_irrf_ss, 
                                base_irrf_13, 
                                base_inss_ss, 
                                base_inss_13, 
                                base_fgts_ss, 
                                base_fgts_13, 
                                avos_fp, 
                                avos_dt, 
                                aviso_valor, 
                                aviso, 
                                aux_distancia, 
                                arredondamento_positivo, 
                                ajuda_custo, 
                                ad_noturno_plantao, 
                                ad_noturno, 
                                adicional_noturno, 
                                adiantamento_13, 
                                adiantamento, 
                                a480, 
                                a479, 
                                a477"
                             ,SELECT);
        
        $this->db->setQuery("FROM rh_recisao ",FROM);
        
        
        if(class_exists('RhCltClass')){
    
            $id_clt = parent::getIdClt();
            
            $id_projeto = parent::getIdProjeto();

            $id_regiao = parent::getIdRegiao();
           
        }        
        else {
            
            $id_clt = $this->getRecisaoIdClt();
            
            $id_projeto = $this->getRecisaoIdProjeto();

            $id_regiao = $this->getRecisaoIdRegiao();
            
        }
        
        
        if(!empty($id_clt) || !empty($id_regiao) ||  !empty($id_projeto)) {

            $this->db->setQuery("WHERE 1=1",WHERE);
            
            $this->db->setQuery((!empty($id_clt)? "AND id_clt = {$id_clt}" : ""),WHERE,true);

            $this->db->setQuery((!empty($id_regiao)? "AND id_regiao = {$id_regiao}" : ""),WHERE,true);

            $this->db->setQuery((!empty($id_projeto)? "AND id_projeto = {$id_projeto}" : ""),WHERE,true);
            
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