<?php

class Rescisao {

    private $dados = array();
    private $programadores = array(179,158,349);
    /**
     * MÉTODO PARA SELEÇÃO DA RESCISÃO SIMPLES E COMPLEMENTAR
     * @param type $mes
     * @param type $ano
     * @param type $projeto
     * @param type $tipo (S=simples, C=complementar)
     * @return type
     */
    private function getDadosRescisao($mes, $ano, $projeto, $tipo, $bases = false) {
        $criteria = ($tipo == "S") ? " AND rescisao_complementar = '0'" : " AND rescisao_complementar = '1'";
        $query = "SELECT *, DATE_FORMAT(data_proc, '%Y-%m-%d') AS data_proc_antiga FROM rh_recisao WHERE MONTH(data_demi) = '{$mes}' AND YEAR(data_demi) = '{$ano}' AND id_projeto = '{$projeto}' AND status = '1' {$criteria}";
        //echo $query;
        $rescisao = mysql_query($query) or die("Error ao selecionar rescisao");
        return $rescisao;
    }

    /**
     * MÉTODO PARA MONTAR O ARRAY COM DADOS DA RESCISÃO
     * @param type $mes
     * @param type $ano
     * @param type $projeto
     * @param type $complementar
     * @param type $debug
     */
    public function montarArrayRescisao($mes, $ano, $projeto, $complementar = false, $debug = false) {
        
        //DADOS DA RESCISAO SIMPLES
        $dados_resc_simples = $this->getDadosRescisao($mes, $ano, $projeto, "S");
        
        while ($d = mysql_fetch_assoc($dados_resc_simples)) {
            
            //MODO DEBUG
            if ($debug) {
                $this->getDebug($d);
            }
            //MONTANDO ARRAY
            if (!empty($d['nome'])) {
                $this->dados[$d['id_clt']]['nome'] = $d['nome'];
            }
            if (!empty($d['saldo_salario']) && $d['saldo_salario'] != 0.00) {
                $this->dados[$d['id_clt']]['saldo_salario']['nome'] = "SALDO DE SALÁRIO";
                $this->dados[$d['id_clt']]['saldo_salario']['valor'] = $d['saldo_salario'];
                $this->dados[$d['id_clt']]['saldo_salario']['tipo'] = "";
            }
            if (!empty($d['ferias_pr']) && $d['ferias_pr'] != 0.00) {
                $this->dados[$d['id_clt']]['ferias_proporcional']['nome'] = "FÉRIAS PROPORCIONAIS";
                $this->dados[$d['id_clt']]['ferias_proporcional']['valor'] = $d['ferias_pr'];
            }
            if (!empty($d['umterco_fp']) && $d['umterco_fp'] != 0.00) {
                $this->dados[$d['id_clt']]['terco_constitucional_ferias']['nome'] = "1/3 CONSTITUCIONAL DE FÉRIAS";
                $this->dados[$d['id_clt']]['terco_constitucional_ferias']['valor'] = $d['umterco_fp'];
            }
            if (!empty($d['dt_salario']) && $d['dt_salario'] != 0.00) {
                $this->dados[$d['id_clt']]['decimo_terceiro_salario']['nome'] = "13° SALÁRIO";
                $this->dados[$d['id_clt']]['decimo_terceiro_salario']['valor'] = $d['dt_salario'];
            }
            if (!empty($d['previdencia_ss']) && $d['previdencia_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['previdencia_social']['nome'] = "PREVIDÊNCIA SOCIAL (INSS SALDO DE SALÁRIO)";
                $this->dados[$d['id_clt']]['previdencia_social']['valor'] = $d['previdencia_ss'];
            }
            if (!empty($d['previdencia_dt']) && $d['previdencia_dt'] != 0.00) {
                $this->dados[$d['id_clt']]['previdencia_social_deciso']['nome'] = "PREVIDÊNCIA SOCIAL 13º SALÁRIO (INSS 13º SALÁRIO)";
                $this->dados[$d['id_clt']]['previdencia_social_deciso']['valor'] = $d['previdencia_dt'];
            }
            if (!empty($d['terceiro_ss']) && $d['terceiro_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['decimo_terceiro_aviso']['nome'] = "13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)";
                $this->dados[$d['id_clt']]['decimo_terceiro_aviso']['valor'] = $d['terceiro_ss'];
            }
            if (!empty($d['aviso_valor']) && $d['aviso_valor'] != 0.00) {
                $this->dados[$d['id_clt']]['aviso_previo']['nome'] = "AVISO PRÉVIO";
                $this->dados[$d['id_clt']]['aviso_previo']['valor'] = $d['aviso_valor'];
            }
            if (!empty($d['insalubridade']) && $d['insalubridade'] != 0.00) {
                $this->dados[$d['id_clt']]['insalubridade']['nome'] = "INSALUBRIDADE";
                $this->dados[$d['id_clt']]['insalubridade']['valor'] = $d['insalubridade'];
            }
            if (!empty($d['lei_12_506']) && $d['lei_12_506'] != 0.00) {
                $this->dados[$d['id_clt']]['lei_12_506']['nome'] = "LEI 12.506 (AVISO PRÉVIO)";
                $this->dados[$d['id_clt']]['lei_12_506']['valor'] = $d['lei_12_506'];
            }
            if (!empty($d['inss_ss']) && $d['inss_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['inss']['nome'] = "INSS)";
                $this->dados[$d['id_clt']]['inss']['valor'] = $d['inss_ss'];
            }
            if (!empty($d['ir_ss']) && $d['ir_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['ir']['nome'] = "IRRF SALDO DE SALÁRIO)";
                $this->dados[$d['id_clt']]['ir']['valor'] = $d['ir_ss'];
            }
            if (!empty($d['inss_dt']) && $d['inss_dt'] != 0.00) {
                $this->dados[$d['id_clt']]['inss_dt']['nome'] = "PREVIDÊNCIA SOCIAL 13º SALÁRIO (INSS 13º SALÁRIO)";
                $this->dados[$d['id_clt']]['inss_dt']['valor'] = $d['inss_dt'];
            }
            if (!empty($d['ir_dt']) && $d['ir_dt'] != 0.00) {
                $this->dados[$d['id_clt']]['ir_dt']['nome'] = "IRRF 13º SALÁRIO";
                $this->dados[$d['id_clt']]['ir_dt']['valor'] = $d['ir_dt'];
            }
            if (!empty($d['inss_ferias']) && $d['inss_ferias'] != 0.00) {
                $this->dados[$d['id_clt']]['inss_ferias']['nome'] = "INSS SOBRE FÉRIAS";
                $this->dados[$d['id_clt']]['inss_ferias']['valor'] = $d['inss_ferias'];
            }
            if (!empty($d['ir_ferias']) && $d['ir_ferias'] != 0.00) {
                $this->dados[$d['id_clt']]['ir_ferias']['nome'] = "IR SOBRE FÉRIAS";
                $this->dados[$d['id_clt']]['ir_ferias']['valor'] = $d['ir_ferias'];
            }
            if (!empty($d['ferias_vencidas']) && $d['ferias_vencidas'] != 0.00) {
                $this->dados[$d['id_clt']]['ferias_vencidas']['nome'] = "FÉRIAS VENCIDA";
                $this->dados[$d['id_clt']]['ferias_vencidas']['valor'] = $d['ferias_vencidas'];
            }
            if (!empty($d['a479']) && $d['a479'] != 0.00) {
                $this->dados[$d['id_clt']]['multa_479']['nome'] = "MULTA ART. 479/CLT";
                $this->dados[$d['id_clt']]['multa_479']['valor'] = $d['a479'];
            }
            if (!empty($d['a480']) && $d['a480'] != 0.00) {
                $this->dados[$d['id_clt']]['multa_480']['nome'] = "MULTA ART. 480/CLT";
                $this->dados[$d['id_clt']]['multa_480']['valor'] = $d['a480'];
            }
            if (!empty($d['a477']) && $d['a477'] != 0.00) {
                $this->dados[$d['id_clt']]['multa_477']['nome'] = "MULTA ART. 477/CLT";
                $this->dados[$d['id_clt']]['multa_477']['valor'] = $d['a477'];
            }
            if (!empty($d['fgts_anterior']) && $d['fgts_anterior'] != 0.00) {
                $this->dados[$d['id_clt']]['fgts_anterior']['nome'] = "FGTS ANTERIOR";
                $this->dados[$d['id_clt']]['fgts_anterior']['valor'] = $d['fgts_anterior'];
            }
            if (!empty($d['fgts40']) && $d['fgts40'] != 0.00) {
                $this->dados[$d['id_clt']]['fgts40']['nome'] = "FGTS 40%";
                $this->dados[$d['id_clt']]['fgts40']['valor'] = $d['fgts40'];
            }
            if (!empty($d['fgts8']) && $d['fgts8'] != 0.00) {
                $this->dados[$d['id_clt']]['fgts8']['nome'] = "FGTS 8%";
                $this->dados[$d['id_clt']]['fgts8']['valor'] = $d['fgts8'];
            }

            /**
             * FOI NECESSÁRIO FAZER ISSO POR QUE NÃO SE UTILIZA MAIS A TABELA 
             * DE rh_movimentos_clt PARA GUARDAR MOVIMENTOS DE RESCISÃO
             */
            if ($data >= '2013-04-04') {
                //TABELA NOVA QUE GUARDA OS MOVIMENTOS DE RESCISÃO
                $dados_resc_movimentos = $this->getMovimentosRescisao($d['id_clt'], $d['id_recisao']);
                while ($rescisao_mov = mysql_fetch_assoc($dados_resc_movimentos)) {
                    $this->dados[$d['id_clt']][$rescisao_mov['id_mov']]['nome'] = $rescisao_mov['descicao'];
                    $this->dados[$d['id_clt']][$rescisao_mov['id_mov']]['valor'] = $rescisao_mov['valor'];
                }
            } else {
                //TABELA ANTIGA QUE GUARDA OS MOVIMENTOS DE RESCISÃO
                $dados_resc_movimentos = $this->getMovimentosRescisaoAntigo($d['id_clt']);
                while ($rescisao_mov = mysql_fetch_assoc($dados_resc_movimentos)) {
                    $this->dados[$d['id_clt']][$rescisao_mov['id_mov']]['nome'] = $rescisao_mov['nome_movimento'];
                    $this->dados[$d['id_clt']][$rescisao_mov['id_mov']]['valor'] = $rescisao_mov['valor_movimento'];
                }
            }
        }

        /**
         * RESCISÃO COMPLEMENTAR
         */
        if ($complementar) {
            if (!empty($d['saldo_salario']) && $d['saldo_salario'] != 0.00) {
                $this->dados[$d['id_clt']]['saldo_salario_c']['nome'] = "SALDO SALÁRIO COMPLEMENTAR";
                $this->dados[$d['id_clt']]['saldo_salario_c']['valor'] = $d['saldo_salario'];
            }
            if (!empty($d['ferias_pr']) && $d['ferias_pr'] != 0.00) {
                $this->dados[$d['id_clt']]['ferias_proporcional_c']['nome'] = "FÉRIAS PROPORCIONAIS COMPLEMENTAR";
                $this->dados[$d['id_clt']]['ferias_proporcional_c']['valor'] = $d['ferias_pr'];
            }
            if (!empty($d['umterco_fp']) && $d['umterco_fp'] != 0.00) {
                $this->dados[$d['id_clt']]['terco_constitucional_ferias_c']['nome'] = "1/3 CONSTITUCIONAL DE FÉRIAS";
                $this->dados[$d['id_clt']]['terco_constitucional_ferias_c']['valor'] = $d['umterco_fp'];
            }
            if (!empty($d['dt_salario']) && $d['dt_salario'] != 0.00) {
                $this->dados[$d['id_clt']]['decimo_terceiro_salario_c']['nome'] = "13° SALÁRIO";
                $this->dados[$d['id_clt']]['decimo_terceiro_salario_c']['valor'] = $d['dt_salario'];
            }
            if (!empty($d['previdencia_ss']) && $d['previdencia_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['previdencia_social_c']['nome'] = "PREVIDÊNCIA SOCIAL (INSS SALDO DE SALÁRIO)";
                $this->dados[$d['id_clt']]['previdencia_social_c']['valor'] = $d['previdencia_ss'];
            }
            if (!empty($d['previdencia_dt']) && $d['previdencia_dt'] != 0.00) {
                $this->dados[$d['id_clt']]['previdencia_social_deciso_c']['nome'] = "PREVIDÊNCIA SOCIAL 13º SALÁRIO (INSS 13º SALÁRIO)";
                $this->dados[$d['id_clt']]['previdencia_social_deciso_c']['valor'] = $d['previdencia_dt'];
            }
            if (!empty($d['inss_ss']) && $d['inss_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['inss_c']['nome'] = "INSS";
                $this->dados[$d['id_clt']]['inss_c']['valor'] = $d['inss_ss'];
            }
        }

        /**
         * VALORES DE BASE
         */
        if ($bases) {
            if (!empty($d['base_inss_ss']) && $d['base_inss_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['base_inss']['nome'] = "BASE INSS";
                $this->dados[$d['id_clt']]['base_inss']['valor'] = $d['base_inss_ss'];
            }
            if (!empty($d['base_fgts_ss']) && $d['base_fgts_ss'] != 0.00) {
                $this->dados[$d['id_clt']]['base_fgts']['nome'] = "BASE FGTS";
                $this->dados[$d['id_clt']]['base_fgts']['valor'] = $d['base_fgts_ss'];
            }
            if (!empty($d['base_inss_13']) && $d['base_inss_13'] != 0.00) {
                $this->dados[$d['id_clt']]['base_inss_decimo']['nome'] = "BASE INSS DECIMO TERCEIRO";
                $this->dados[$d['id_clt']]['base_inss_decimo']['valor'] = $d['base_inss_13'];
            }
            if (!empty($d['base_fgts_13']) && $d['base_fgts_13'] != 0.00) {
                $this->dados[$d['id_clt']]['base_fgts_decimo']['nome'] = "BASE FGTS DECIMO TERCEIRO";
                $this->dados[$d['id_clt']]['base_fgts_decimo']['valor'] = $d['base_fgts_13'];
            }
            if (!empty($d['valor_ddir_13']) && $d['valor_ddir_13'] != 0.00) {
                $this->dados[$d['id_clt']]['valor_ddir_decimo']['nome'] = "VALOR DDIR DECIMO TERCEIRO";
                $this->dados[$d['id_clt']]['valor_ddir_decimo']['valor'] = $d['valor_ddir_13'];
            }
        }

        //MODO DEBUG
        //$this->getDebug($this->dados);
    }
    
    
    /**
     * METODO COM TODOS OS PROVENTOS SEM O SALDO DE SALÁRIO
     * @param type $mes
     * @param type $ano
     * @param type $clt
     */
    public function getDadosRescisaoByClt($mes, $ano, $clt, $tipo = "S"){
        
        
        $criteria = ($tipo == "S") ? " AND rescisao_complementar = '0'" : " AND rescisao_complementar = '1'";
        $query = "SELECT *, DATE_FORMAT(data_proc, '%Y-%m-%d') AS data_proc_antiga FROM rh_recisao WHERE MONTH(data_demi) = '{$mes}' AND YEAR(data_demi) = '{$ano}' AND id_clt = '{$clt}' AND status = '1' {$criteria}";
        $rescisao = mysql_query($query) or die("Error ao selecionar rescisao");
        $dados = array();
        while ($d = mysql_fetch_assoc($rescisao)) {
            
            //MONTANDO ARRAY
            if (!empty($d['nome'])) {
                $dados[$d['id_clt']]['nome'] = $d['nome'];
            }
            
            if (!empty($d['ferias_pr']) && $d['ferias_pr'] != 0.00) {
                $dados[$d['id_clt']]['CREDITO']['ferias_proporcional']['nome'] = "FÉRIAS PROPORCIONAIS";
                $dados[$d['id_clt']]['CREDITO']['ferias_proporcional']['valor'] = $d['ferias_pr'];
            }
            if (!empty($d['umterco_fp']) && $d['umterco_fp'] != 0.00) {
                $dados[$d['id_clt']]['CREDITO']['terco_constitucional_ferias']['nome'] = "1/3 CONSTITUCIONAL DE FÉRIAS";
                $dados[$d['id_clt']]['CREDITO']['terco_constitucional_ferias']['valor'] = $d['umterco_fp'];
            }
            if (!empty($d['dt_salario']) && $d['dt_salario'] != 0.00) {
                $dados[$d['id_clt']]['CREDITO']['decimo_terceiro_salario']['nome'] = "13° SALÁRIO PROPORCIONAL";
                $dados[$d['id_clt']]['CREDITO']['decimo_terceiro_salario']['valor'] = $d['dt_salario'];
            }
            if (!empty($d['insalubridade']) && $d['insalubridade'] != 0.00) {
                $dados[$d['id_clt']]['CREDITO']['insalubridade']['nome'] = "INSALUBRIDADE";
                $dados[$d['id_clt']]['CREDITO']['insalubridade']['valor'] = $d['insalubridade'];
            }
            if (!empty($d['lei_12_506']) && $d['lei_12_506'] != 0.00) {
                $dados[$d['id_clt']]['CREDITO']['lei_12_506']['nome'] = "LEI 12.506 (AVISO PRÉVIO)";
                $dados[$d['id_clt']]['CREDITO']['lei_12_506']['valor'] = $d['lei_12_506'];
            }
            if (!empty($d['ferias_vencidas']) && $d['ferias_vencidas'] != 0.00) {
                $dados[$d['id_clt']]['CREDITO']['ferias_vencidas']['nome'] = "FÉRIAS VENCIDA";
                $dados[$d['id_clt']]['CREDITO']['ferias_vencidas']['valor'] = $d['ferias_vencidas'];
            }
            
            
            
            if (!empty($d['previdencia_ss']) && $d['previdencia_ss'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['previdencia_social']['nome'] = "PREVIDÊNCIA SOCIAL (INSS SALDO DE SALÁRIO)";
                $dados[$d['id_clt']]['DEBITO']['previdencia_social']['valor'] = $d['previdencia_ss'];
            }
            if (!empty($d['previdencia_dt']) && $d['previdencia_dt'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['previdencia_social_decimo']['nome'] = "PREVIDÊNCIA SOCIAL 13º SALÁRIO (INSS 13º SALÁRIO)";
                $dados[$d['id_clt']]['DEBITO']['previdencia_social_decimo']['valor'] = $d['previdencia_dt'];
            }
            if (!empty($d['terceiro_ss']) && $d['terceiro_ss'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['decimo_terceiro_aviso']['nome'] = "13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)";
                $dados[$d['id_clt']]['DEBITO']['decimo_terceiro_aviso']['valor'] = $d['terceiro_ss'];
            }
            if (!empty($d['aviso_valor']) && $d['aviso_valor'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['aviso_previo']['nome'] = "AVISO PRÉVIO";
                $dados[$d['id_clt']]['DEBITO']['aviso_previo']['valor'] = $d['aviso_valor'];
            }
            if (!empty($d['inss_ss']) && $d['inss_ss'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['inss']['nome'] = "INSS)";
                $dados[$d['id_clt']]['DEBITO']['inss']['valor'] = $d['inss_ss'];
            }
            
            if (!empty($d['ir_ss']) && $d['ir_ss'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['ir']['nome'] = "IRRF SALDO DE SALÁRIO)";
                $dados[$d['id_clt']]['DEBITO']['ir']['valor'] = $d['ir_ss'];
            }
            
            if (!empty($d['ir_dt']) && $d['ir_dt'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['ir_dt']['nome'] = "IRRF 13º SALÁRIO";
                $dados[$d['id_clt']]['DEBITO']['ir_dt']['valor'] = $d['ir_dt'];
            }
            if (!empty($d['inss_ferias']) && $d['inss_ferias'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['inss_ferias']['nome'] = "INSS SOBRE FÉRIAS";
                $dados[$d['id_clt']]['DEBITO']['inss_ferias']['valor'] = $d['inss_ferias'];
            }
            if (!empty($d['ir_ferias']) && $d['ir_ferias'] != 0.00) {
                $dados[$d['id_clt']]['DEBITO']['ir_ferias']['nome'] = "IR SOBRE FÉRIAS";
                $dados[$d['id_clt']]['DEBITO']['ir_ferias']['valor'] = $d['ir_ferias'];
            }
            
            
            
            if (!empty($d['a479']) && $d['a479'] != 0.00) {
                $dados[$d['id_clt']]['multa_479']['nome'] = "MULTA ART. 479/CLT";
                $dados[$d['id_clt']]['multa_479']['valor'] = $d['a479'];
            }
            if (!empty($d['a480']) && $d['a480'] != 0.00) {
                $dados[$d['id_clt']]['multa_480']['nome'] = "MULTA ART. 480/CLT";
                $dados[$d['id_clt']]['multa_480']['valor'] = $d['a480'];
            }
            if (!empty($d['a477']) && $d['a477'] != 0.00) {
                $dados[$d['id_clt']]['multa_477']['nome'] = "MULTA ART. 477/CLT";
                $dados[$d['id_clt']]['multa_477']['valor'] = $d['a477'];
            }
            if (!empty($d['fgts_anterior']) && $d['fgts_anterior'] != 0.00) {
                $dados[$d['id_clt']]['fgts_anterior']['nome'] = "FGTS ANTERIOR";
                $dados[$d['id_clt']]['fgts_anterior']['valor'] = $d['fgts_anterior'];
            }
            if (!empty($d['fgts40']) && $d['fgts40'] != 0.00) {
                $dados[$d['id_clt']]['fgts40']['nome'] = "FGTS 40%";
                $dados[$d['id_clt']]['fgts40']['valor'] = $d['fgts40'];
            }
            if (!empty($d['fgts8']) && $d['fgts8'] != 0.00) {
                $dados[$d['id_clt']]['fgts8']['nome'] = "FGTS 8%";
                $dados[$d['id_clt']]['fgts8']['valor'] = $d['fgts8'];
            }

            /**
             * FOI NECESSÁRIO FAZER ISSO POR QUE NÃO SE UTILIZA MAIS A TABELA 
             * DE rh_movimentos_clt PARA GUARDAR MOVIMENTOS DE RESCISÃO
             */
            //echo $data;
            
//            if ($data >= '2013-04-04') {
                //TABELA NOVA QUE GUARDA OS MOVIMENTOS DE RESCISÃO
                $dados_resc_movimentos = $this->getMovimentosRescisao($d['id_clt'], $d['id_recisao']);
                while ($rescisao_mov = mysql_fetch_assoc($dados_resc_movimentos)) {
                    $dados[$d['id_clt']][$rescisao_mov['categoria']][$rescisao_mov['id_mov']]['nome'] = $rescisao_mov['descicao'];
                    $dados[$d['id_clt']][$rescisao_mov['categoria']][$rescisao_mov['id_mov']]['valor'] = $rescisao_mov['valor'];
                }
//            } else {
//                //TABELA ANTIGA QUE GUARDA OS MOVIMENTOS DE RESCISÃO
//                $dados_resc_movimentos = $this->getMovimentosRescisaoAntigo($d['id_clt']);
//                while ($rescisao_mov = mysql_fetch_assoc($dados_resc_movimentos)) {
//                    $dados[$d['id_clt']][$rescisao_mov['tipo_movimento']][$rescisao_mov['id_mov']]['nome'] = $rescisao_mov['nome_movimento'];
//                    $dados[$d['id_clt']][$rescisao_mov['tipo_movimento']][$rescisao_mov['id_mov']]['valor'] = $rescisao_mov['valor_movimento'];
//                }
//            }
        }
        
        return $dados;
    }

    /**
     * MÉTODOS PARA RESCISÕES NOVAS
     * @param type $clt
     * @param type $rescisao
     * @return type
     */
    public function getMovimentosRescisao($clt = FALSE, $rescisao = FALSE) {
        $where_clt = ($clt) ? ' A.id_clt = ' . $clt . ' AND ' : '';
        $where_rescisao = ($rescisao) ? ' A.id_rescisao = ' . $rescisao . ' AND ' : '';
        $query = "SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao, B.categoria, A.incidencia FROM rh_movimentos_rescisao AS A LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov) WHERE $where_clt $where_rescisao A.status = 1";
        //echo "<!--" . $query . "-->";
        $dados_movimentos = mysql_query($query) or die("Erro ao selecionar movimentos de rescisao");
        return $dados_movimentos;
    }

    /**
     * MÉTODO PARA RESCISÕES ANTIGAS
     * @param type $clt
     * @return type
     */
    public function getMovimentosRescisaoAntigo($clt = FALSE) {
        $where_clt = ($clt) ? ' id_clt = ' . $clt . ' AND ' : '';
        $query = "SELECT * FROM rh_movimentos_clt WHERE $where_clt mes_mov = 16 AND status = 1;";
        $dados_movimentos = mysql_query($query) or die("Erro ao selecionar movimentos antigos de rescisao");
        return $dados_movimentos;
    }
    
    /**
     * 
     * @param type $clt
     * @param type $rescisao
     * @return type 
     */
    public function getMovimentosRescisaoArray($clt = FALSE, $rescisao = FALSE) {

        $result = $this->getMovimentosRescisao($clt, $rescisao);
        $arr = array();
        while ($row = mysql_fetch_array($result)) {
            $arr[$row['id_mov']] = $row;
        }
        return $arr;
    }

    /**
     * 
     * @param type $clt
     * @return type
     */
    public function getMovimentosRescisaoAntigoArray($clt = FALSE) {
        $result = $this->getMovimentosRescisaoAntigo($clt);
        $arr = array();
        while ($row = mysql_fetch_array($result)) {
            $arr[$row['id_mov']] = $row;
        }
        return $arr;
    }

    /**
     * MÉTODO DE RESCISÃO POR ID DO CLT
     * @param type $clt
     * @param type $mes
     * @param type $ano
     * @return type
     */
    public function getRescisaoByClt($clt, $mes =NULL, $ano=NULL) {
        
        $where = '';
        
        if(!is_null($mes)){
            $where .= ' AND MONTH(data_demi) = '.sprintf("%02d", $mes);
        }
        if(!is_null($ano)){
            $where .=' AND YEAR(data_demi) = '.$ano;
        }
        
        $query = "SELECT * FROM rh_recisao WHERE id_clt = '{$clt}' $where  AND status = '1' AND rescisao_complementar = '0'";
        $dados_rescisao = mysql_query($query) or die("Erro ao selecionar rescisão");
        return $dados_rescisao;
    }
    
    /**
     * 
     * @param type $id_recisao
     * @return type
     */
    public function listaItensRescisaoById($id_recisao){
        
        /**
         * Consulta da Rescisão
         */
        $qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao IN({$id_recisao})");
        
        while($rowsRescisao = mysql_fetch_assoc($qr_rescisao)){
            
            /**
             * 
             */
            if ($rowsRescisao['motivo'] == 65) {
                $aviso_previo_debito += $rowsRescisao['aviso_valor'];
            } else {
                $aviso_previo_credito += $rowsRescisao['aviso_valor'];
            }

            /**
             *  Multa de Atraso
             */
            if ($rowsRescisao['motivo'] == '64') {
                $multa_479 += $rowsRescisao['a479'];
                $multa_480 = NULL;
            } elseif ($rowsRescisao['motivo'] == '63') {
                $multa_479 = NULL;
                $multa_480 += $rowsRescisao['a480'];
            }

            $dados_recisao[58] = array(
                "movimento" => "Descanso Semanal Remunerado (DSR)",
                "tipo" => "CREDITO",
                "valor" => 0.00
            );

            //SALDO DE SALÁRIO
            $dados_recisao[50] = array(
                "movimento" => "Saldo de {$rowsRescisao['dias_saldo']} dias Salário",
                "tipo" => "CREDITO",
                "valor" => $rowsRescisao['saldo_salario']
            );

            //COMISSÃO
            /**
             * ATE AQUI NOS AJUDOU O SENHOR 
             */    
                
            $varComissoes += $rowsRescisao['comissao'];     
            if (!is_null($varComissoes) && $varComissoes > 0) {
                $dados_recisao[51] = array(
                    "movimento" => "Comissões",
                    "tipo" => "CREDITO",
                    "valor" => $varComissoes
                );
            }

            //INSALUBRIDADE
            $varInsalubridade += $rowsRescisao['insalubridade'];
            if (!is_null($varInsalubridade) && $varInsalubridade > 0) {
                $dados_recisao[53] = array(
                    "movimento" => "Adicional de Insalubridade",
                    "tipo" => "CREDITO",
                    "valor" => $varInsalubridade
                );
            }

            //REFLEXO DO DSR 
            $dados_recisao[59] = array(
                "movimento" => 'Reflexo do "DSR" sobre Salário Variável',
                "tipo" => "CREDITO",
                "valor" => 0.00
            );

            //MULTA 477
            $var477 += $rowsRescisao['a477'];
            $dados_recisao[60] = array(
                "movimento" => 'Multa Art. 477, § 8º/CLT',
                "tipo" => "CREDITO",
                "valor" => $var477
            );

            //SALARIO FAMILIA
            $varSalFamilia += $rowsRescisao['sal_familia'];
            $dados_recisao[62] = array(
                "movimento" => 'Salário-Família',
                "tipo" => "CREDITO",
                "valor" => $varSalFamilia
            );

            /*
             * 11/05/2016
             * by: MAX
             * SOLICITADO PELO ÍTALO(IABAS)
             * PQ ESTAVA TRAZENDO NUM CAMPO COM NUMERO 0(ZERO)
             * ENTÃO ELE DISTRIBUI ESSES MOVIMENTOS
             * EM ALGUNS CAMPOS DA RESCISÃO
             */

            /**
             * 21/06/2016
             * by: Ramon
             * ALANA SOLICITOU QUE TIRASSE A SOMA DA MÉDIA DO 13 E DAS FÉRIAS
             * POIS O VALOR CALCULADO DE 13 E DE FÉRIAS JÁ ESTÁ CONSIDERANDO A MÉDIA LANÇADA
             * COMENTANDO O CÓDIGO QUE O ITALO PEDIU PARA ACRECENTAR
             */
            $arrayMovMediasFeriasEDt = array(384, 385, 386, 387, 388, 408, 409, 410);


            /*
             * 04/10/2016
             * Renato
             * TIRANDO OS MOVIMENTOS DE MEDIA PARA AS RESCISÕES COMPLEMENTARES
             */
            if ($rowsRescisao['rescisao_complementar'] == 0) {

                $sql_movTemp = "SELECT B.descicao, B.id_mov, A.valor,B.categoria, C.tipo_movimento, C.valor_movimento, B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                                FROM rh_movimentos_rescisao AS A
                                LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                                LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$rowsRescisao[id_clt]}' AND A.status = 1) AS C ON(B.id_mov = C.id_mov)
                                WHERE A.id_clt = '{$rowsRescisao['id_clt']}' 
                                AND A.id_mov IN(" . implode(',', $arrayMovMediasFeriasEDt) . ")
                                AND A.status = 1 GROUP BY A.id_mov";
                $qr_movimentosTemp = mysql_query($sql_movTemp) or die(mysql_error());

            }

            /**
             * By Ramon 19/07/2016
             * A Alana lançou médias de ferias proporcionais, porem a pessoa teve 12 avos e na hora de GRAVAR a rescisão 
             * os valores q antes apareciam como proporcionais, terão q aparecer como férias VENCIDAS por conta dos 12 avos...
             * Então as médias q foram lançadas para calculo como ferias proporcionais agora vão pertencer as férias Vencidas...
             * Vamos a GAMBIARRA DA PROJEÇÃO
             * (MEDIA FERIAS PROJEÇAO AVISO PREVIO)
             */
            while ($row_movimentosTemp = mysql_fetch_assoc($qr_movimentosTemp)) {

                /* -------------BLOCO 13 SALARIO------------- */
                //MEDIA 13º PROJEÇAO AVISO PREVIO (junto com o campo 70)
                if ($row_movimentosTemp['id_mov'] == 410) {
                    $rowsRescisao['terceiro_ss'] = $rowsRescisao['terceiro_ss'] + $row_movimentosTemp['valor'];
                }

                //MEDIA SOBRE 13º SALARIO
                if ($row_movimentosTemp['id_mov'] == 384) {
                    $rowsRescisao['dt_salario'] = $rowsRescisao['dt_salario'] + $row_movimentosTemp['valor'];
                }

                /* -------------BLOCO FÉRIAS VENCIDAS------------- */
                //MEDIA SOBRE FERIAS INDENIZADAS
                if ($row_movimentosTemp['id_mov'] == 385) {
                    $rowsRescisao['ferias_vencidas'] = $rowsRescisao['ferias_vencidas'] + $row_movimentosTemp['valor'];
                }

                //MEDIA FERIAS PROJEÇAO AVISO PREVIO (junto com o campo 71)
                if ($row_movimentosTemp['id_mov'] == 408) {
                    $rowsRescisao['ferias_aviso_indenizado'] = $rowsRescisao['ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
                }

                //1/3 MEDIA FERIAS PROJEÇãO AVISO PREVIO (junto com o campo 75)
                if ($row_movimentosTemp['id_mov'] == 409) {
                    $rowsRescisao['umterco_ferias_aviso_indenizado'] = $rowsRescisao['umterco_ferias_aviso_indenizado'] + $row_movimentosTemp['valor'];
                }

                /* -------------BLOCO FÉRIAS PROPORCIONAIS------------- */
                //MEDIA SOBRE FERIAS PROPORCIONAIS
                if ($row_movimentosTemp['id_mov'] == 387) {
                    $rowsRescisao['ferias_pr'] = $rowsRescisao['ferias_pr'] + $row_movimentosTemp['valor'];
                }

                //1/3 DE MEDIA SOBRE FERIAS INDENIZADAS
                if ($row_movimentosTemp['id_mov'] == 386) {
                    $rowsRescisao['umterco_fp'] = $rowsRescisao['umterco_fp'] + $row_movimentosTemp['valor'];
                }

                //1/3 DE MEDIA SOBRE FERIAS PROPORCIONAIS
                if ($row_movimentosTemp['id_mov'] == 388) {
                    $rowsRescisao['umterco_fp'] = $rowsRescisao['umterco_fp'] + $row_movimentosTemp['valor'];
                }
            }

            //DECIMO TERCEIRO
            $varDtSalario += $rowsRescisao['dt_salario'] ;
            $avos_dt = sprintf('%02d', $rowsRescisao['avos_dt']);
            $dados_recisao[63] = array(
                "movimento" => "13º Salário Proporcional {$avos_dt}/12 avos",
                "tipo" => "CREDITO",
                "valor" => $varDtSalario
            );

            //DECIMO TERCEIRO EXERCICIO
            $dados_recisao[64] = array(
                "movimento" => '13º Salário Exercício 0/12 avos',
                "tipo" => "CREDITO",
                "valor" => 0.00
            );

            /**
             * By Ramon 19/07/2016
             * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PRÉVIO
             * TEXTO ENVIADO SKYPE:
             * "a lei 12.506 e médias sobre aviso prévio deve aparecer junto com o aviso prévio indenizado no termo"
             */
            // AVISO PREVIO
            if ($rowsRescisao['fator'] == 'empregado' && $rowsRescisao['aviso'] == 'PAGO pelo ') {
                $varAvisoPrevioDebito += $rowsRescisao['aviso_valor'];
                $aviso_previo_debito = $varAvisoPrevioDebito;
                $dados_recisao[103] = array(
                    "movimento" => "Aviso-Prévio Indenizado",
                    "tipo" => "DEBITO",
                    "valor" => $varAvisoPrevioDebito
                );
            } else {
                $varAvisoPrecioCredito += $rowsRescisao['aviso_valor'] + $rowsRescisao['lei_12_506'];
                $aviso_previo_credito = $varAvisoPrecioCredito;
                $dados_recisao[69] = array(
                    "movimento" => "Aviso-Prévio Indenizado",
                    "tipo" => "CREDITO",
                    "valor" => $varAvisoPrecioCredito
                );
            }

            ///DISPENSA ANTES DO TERMINO DE CONTRATO PELA EMPRESA
            if ($rowsRescisao['motivo'] != '63' and $rowsRescisao['motivo'] != '65') {
                $varMulta479 += $rowsRescisao['a479'];
                $multa_479 = $varMulta479;
                $dados_recisao[61] = array(
                    "movimento" => "Multa Art. 479/CLT",
                    "tipo" => "CREDITO",
                    "valor" => $varMulta479
                );
            } else {
                //INVERSO
                $varMulta480 += $rowsRescisao['a479'];
                $multa_480 = $varMulta480;
                $dados_recisao[104] = array(
                    "movimento" => "Multa Art. 480/CLT",
                    "tipo" => "DEBITO",
                    "valor" => $varMulta480
                );
            }

            /**
             * By Ramon 19/07/2016
             * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PRÉVIO
             * VERIFICANDO PRIMEIRO SE TEM AVISO PREVIO INDENIZADO
             * SÓ VAI CRIAR INFORMAÇÃO NA MATRIZ DE $dados_recisao PARA A lei SE NÃO TIVER VALOR NO AVISO PRÉVIO
             * POIS SE TIVER O AVISO JA ESTÁ SOMADO A LEI
             */
            //LEI 12.506
            if (!isset($dados_recisao[69]["valor"])) {
                $varLei12506 += $rowsRescisao['lei_12_506'];
                $dados_recisao[95] = array(
                    //"movimento" => "Lei 12.506 ({$row_rescisao['qnt_dias_lei_12_506']} dias)",
                    "movimento" => "Lei 12.506 ({$qtd_dias_12506} dias)",
                    "tipo" => "CREDITO",
                    "valor" => $varLei12506
                );
            }

            /* * ************************************************************** */
            //Pegando os valores dos moviemntos e inserindo nos campos de acordo com o ANEXO VIII da rescisão, o número do campo encontra-se na tabela rh_movimento

            if ($rowsRescisao['rescisao_complementar'] == 1) {
                $and_complementar = "AND A.complementar = 1";
            }

            //SOMANDO MOVIMENTOS Q TEM O MESMO id_mov
            $sql_mov = "SELECT  A.id_mov_rescisao,B.id_mov,C.id_movimento,
                                B.descicao, A.id_mov, B.categoria, C.tipo_movimento, SUM(A.valor) AS  valor , B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                                    FROM rh_movimentos_rescisao AS A
                                    LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                                    LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$rowsRescisao[id_clt]}' AND A.status = 1 GROUP BY A.id_mov) AS C ON(B.id_mov = C.id_mov)
                                    WHERE A.id_clt = '{$rowsRescisao[id_clt]}' 
                                    AND A.id_mov NOT IN(" . implode(',', $arrayMovMediasFeriasEDt) . ") 
                                    AND A.status = 1 {$and_complementar} AND A.id_rescisao = {$rowsRescisao['id_recisao']} GROUP BY A.id_mov"; //AND C.id_clt = '{$row_rescisao[id_clt]}' AND C.status = '1'";


            $qr_movimentos = mysql_query($sql_mov) or die(mysql_error());
            $qtd_movimentos = mysql_num_rows($qr_movimentos);

            while ($row_movimentos = mysql_fetch_assoc($qr_movimentos)) {

//                if($_COOKIE['logado'] == 179){
//                    echo "************************Movimentos com o mesmo campo****************************<br>";
//                    echo "<pre>";
//                        print_r($row_movimentos);
//                    echo "</pre>";
//                }

                $movimentos[$row_movimentos['campo_rescisao']] += $row_movimentos['valor'];
                $quantidade[$row_movimentos['campo_rescisao']] = $row_movimentos['qnt_horas']; //verificaQuantidade($row_movimentos['tipo_qnt'], $row_movimentos['qnt'], $row_movimentos['qnt_horas']);

                $nome_movimento = "";

                /**
                 * By Ramon 19/07/2016
                 * A pedido da Alana juntando alguns movimentos no mesmo campo de AVISO PRÉVIO
                 * VERIFICANDO PRIMEIRO SE TEM AVISO PREVIO INDENIZADO
                 * SÓ VAI CRIAR INFORMAÇÃO NA MATRIZ DE $dados_recisao PARA O MOVIMENTO "96 - MEDIA SOBRE AVISO PREVIO" SE NÃO TIVER VALOR NO AVISO PRÉVIO
                 * POIS SE TIVER O AVISO JA ESTÁ SOMADO O MOVI "96 - MEDIA SOBRE AVISO PREVIO"
                 */
                if (isset($dados_recisao[69]["valor"]) && $row_movimentos['campo_rescisao'] == 96) {
                    //SOMAR O VALOR DO MOVIMENTO COM OS VALORES JÁ REGISTRADOS NA MATRIZ
                    $dados_recisao[69]["valor"] += $row_movimentos['valor'];
                    //PULA ESSE MOVIMENTO DO ARRAY, E CONTINUA O WHILE DO PROXIMO MOVIMENTO
                    continue;
                }


                //TRATANDO NOME DO MOVIMENTOS JAJ LANÇADOS
                if ($row_movimentos['campo_rescisao'] == 117) {
                    $quant_faltas += $row_movimentos['qnt'];
                    $quant_horas += $row_movimentos['qnt_horas'];

                    if ($quant_faltas > 0) {

                        if ($id_clt == 144) {
                            $quant_faltas = 30;
                        }
                        $nome_movimento = "Faltas ({$quant_faltas} dias)";
                    } else {
                        $nome_movimento = "Faltas ({$quant_horas} horas)";
                    }
                } else if ($row_movimentos['campo_rescisao'] == 58) {
                    $nome_movimento = "Descanso Semanal Remunerado (DSR)";
                } else {
                    $nome_movimento = $row_movimentos['descicao'];
                }

                if ($row_movimentos['valor'] > 0) {



                    $dados_recisao[(String) $row_movimentos['campo_rescisao']]["movimento"] = $nome_movimento;
                    $dados_recisao[(String) $row_movimentos['campo_rescisao']]["tipo"] = $row_movimentos['categoria'];
                    $dados_recisao[(String) $row_movimentos['campo_rescisao']]["valor"] += $row_movimentos['valor'];
                    $dados_recisao[(String) $row_movimentos['campo_rescisao']]["percentual"] = $row_movimentos['percentual'] * 100;

                    if ($id_clt == 144) {
                        $dados_recisao[(String) $row_movimentos['campo_rescisao']]["valor"] = 1642.37;
                    }
                }
                /*     * *************************************************************** */

                if ($row_movimentos['id_mov'] == 292) {
                    $adiantamento_13 = $row_movimentos['valor'];
                }
            }

            //FÉRIAS PROPORCIONAIS
            $varFeriasProporcionais += $rowsRescisao['ferias_pr'];
            $dados_recisao[65] = array(
                "movimento" => "Férias Proporcionais " . sprintf('%02d', $rowsRescisao['avos_fp']) . "/12 avos<br>{$periodo_aquisitivo_fp}",
                "tipo" => "CREDITO",
                "valor" => $varFeriasProporcionais
            ); 

            //FÉRIAS VENCIDAS
            $texto_fv = "Férias Vencidas <br /> Per. Aquisitivo ";
            //$texto_fv .= ' de ' . formato_brasileiro($row_rescisao['fv_data_ini']) . " à " . formato_brasileiro($row_rescisao['fv_data_fim']) . "<br>";

            if ($row_rescisao['ferias_vencidas'] != '0.00') {
                $texto_fv .= '12/12 avos';
                $texto_fv .= 'de ' . formato_brasileiro($row_rescisao['fv_data_ini']) . " à " . formato_brasileiro($rowsRescisao['fv_data_fim']) . "<br>";
            } else {
                $texto_fv .= '0/12 avos';
            }

            if (!empty($row_rescisao['qnt_faltas_ferias_fv'])) {
                $texto_fv .= "<span>( Faltas: " . $rowsRescisao['qnt_faltas_ferias_fv'] . ")</span>";
            }

            $varFeriasVencidas += $rowsRescisao['ferias_vencidas'];
            $dados_recisao[66] = array(
                "movimento" => $texto_fv,
                "tipo" => "CREDITO",
                "valor" => $varFeriasVencidas
            );

            /*
             * 08/11/16
             * by: Max
             * A pedido do Ítalo, foi separado 
             * o campo 68(terço constitucional de férias) 
             * do campo 52(1/3 férias proporcionais)
             */

            //TERÇO CONSTITUCIONAL DE FERIAS

            $tercoConstitucionalFerias += $rowsRescisao['umterco_fv'];
            if ($complementar == "COMPLEMENTAR") {
                $tercoConstitucionalFerias = 0;
            }
            $dados_recisao[68] = array(
                "movimento" => "Terço Constitucional de Férias",
                "tipo" => "CREDITO",
            //    "valor" => $row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']
                "valor" => $tercoConstitucionalFerias
            );
            
            $varTercoFeriasProporcionais += $rowsRescisao['umterco_fp'];
            $dados_recisao[52] = array(
                "movimento" => "1/3 férias proporcionais",
                "tipo" => "CREDITO",
                "valor" =>$varTercoFeriasProporcionais
            );

            if ($id_clt == 3931) {
                $dados_recisao[68] = array(
                    "movimento" => "Terço Constitucional de Férias",
                    "tipo" => "CREDITO",
                    "valor" => 1020.36
                );
            }
            if ($id_clt == 3926) {
                $valorFdp2 = 1470.95;
                if ($complementar == "COMPLEMENTAR") {
                    $valorFdp2 = 0;
                }
                $dados_recisao[68] = array(
                    "movimento" => "Terço Constitucional de Férias",
                    "tipo" => "CREDITO",
                    "valor" => $valorFdp2
                );
            }

            $avos_projetado = $rowsRescisao['avos_projetado'];

            /**
             * By Ramon 17/10/16
             */
            $qntAvos13indenizado = 1;

            if ($avos_projetado > 0) {
                $qntAvos13indenizado = $avos_projetado;
            }

            if ($id_clt == 1952) {
                $qntAvos13indenizado = 3;
            }
            if ($id_clt == 1967 || $id_clt == 2023 || $id_clt == 4176 || $id_clt == 3111) {
                $qntAvos13indenizado = 2;
            }

            //13° SALÁRIO (AVISO PREVIO INDENIZADO)
            $terceiro_ss += $rowsRescisao['terceiro_ss'];
            $dados_recisao[70] = array(
                "movimento" => "13º Salário (Aviso-Prévio Indenizado {$qntAvos13indenizado}/12 avos)",
                "tipo" => "CREDITO",
                "valor" => $terceiro_ss
            );

            /**
             * By Ramon 17/10/16
             */
            $qntAvosFeriasIndenizado = 1;

            if ($avos_projetado > 0) {
                $qntAvosFeriasIndenizado = $avos_projetado;
            }

            if ($id_clt == 1952 || $id_clt == 1967 || $id_clt == 2023 || $id_clt == 3111 || $id_clt == 4021) {
                $qntAvosFeriasIndenizado = 2;
            }

            //13° SALÁRIO (AVISO PREVIO INDENIZADO)
            $varFeriasAvisoIndenizado += $rowsRescisao['ferias_aviso_indenizado'];
            $dados_recisao[71] = array(
                "movimento" => "Férias (Aviso-Prévio Indenizado {$qntAvosFeriasIndenizado}/12 avos)",
                "tipo" => "CREDITO",
                "valor" => $varFeriasAvisoIndenizado
            );

            if ($id_clt == 2987) {
                $dados_recisao[71] = array(
                    "movimento" => "Férias (Aviso-Prévio Indenizado 2/12 avos)",
                    "tipo" => "CREDITO",
                    "valor" => $rowsRescisao['ferias_aviso_indenizado']
                );
            }

            //FÉRIAS EM DOBRO
            $varFeriasDobro += $rowsRescisao['fv_dobro'];
            $dados_recisao[72] = array(
                "movimento" => "Férias em dobro",
                "tipo" => "CREDITO",
                "valor" => $varFeriasDobro
            );

            //1/3 FÉRIAS EM DOBRO
            $varTercoFeriasDobro += $rowsRescisao['um_terco_ferias_dobro'];
            $dados_recisao[73] = array(
                "movimento" => "1/3 férias em dobro",
                "tipo" => "CREDITO",
                "valor" => $varTercoFeriasDobro
            );

            //1/3 FÉRIAS EM DOBRO

            $umterco_ferias_aviso_indenizado = $rowsRescisao['umterco_ferias_aviso_indenizado'];
            if ($complementar == "COMPLEMENTAR") {
                $umterco_ferias_aviso_indenizado = 0;
            }
            
            $varTercoFeriasAvisoIndenizado += $umterco_ferias_aviso_indenizado;
            $dados_recisao[75] = array(
                "movimento" => "1/3 Férias (Aviso Prévio Indenizado)",
                "tipo" => "CREDITO",
                "valor" => $varTercoFeriasAvisoIndenizado
            );

            if ($id_clt == 3931) {
                $dados_recisao[75] = array(
                    "movimento" => "1/3 Férias (Aviso Prévio Indenizado)",
                    "tipo" => "CREDITO",
                    "valor" => 48.57
                );
            }

            if ($id_clt == 3926) {
                $valorFdp1 = 90.89;
                if ($complementar == "COMPLEMENTAR") {
                    $valorFdp1 = 0;
                }
                $dados_recisao[75] = array(
                    "movimento" => "1/3 Férias (Aviso Prévio Indenizado)",
                    "tipo" => "CREDITO",
                    "valor" => $valorFdp1
                );
            }

            //AJUSTE DE SALDO DEVEDOR
            $varArredondamentoPositivo += $rowsRescisao['arredondamento_positivo'];
            $dados_recisao[99] = array(
                "movimento" => "Ajuste do Saldo Devedor",
                "tipo" => "CREDITO",
                "valor" => $varArredondamentoPositivo
            );

            //ADIANTAMENTO DE 13° SALÁRIO
            $varAdiantamento += $rowsRescisao['adiantamento'];
            $dados_recisao[101] = array(
                "movimento" => "Adiantamento Salarial",
                "tipo" => "DEBITO",
                "valor" => $varAdiantamento
            );

            //ADIANTAMENTO DE 13° SALÁRIO
            $varAdiantamento13 += $rowsRescisao['adiantamento_13'];
            $dados_recisao[102] = array(
                "movimento" => "Adiantamento de 13º Salário",
                "tipo" => "DEBITO",
                "valor" => $varAdiantamento13
            );

            //ADIANTAMENTO DE 13° SALÁRIO
            $dados_recisao[105] = array(
                "movimento" => "Empréstimo em Consignação",
                "tipo" => "DEBITO",
                "valor" => 0.00
            );

            //PREVIDÊNCIA SOCIAL
            $varInssSaldoSalario += $rowsRescisao['inss_ss'];
            $dados_recisao["112.1"] = array(
                "movimento" => "Previdência Social",
                "tipo" => "DEBITO",
                "valor" => $varInssSaldoSalario
            );

            //PREVIDÊNCIA SOCIAL 13 SALARIO
            $varInssDt += $rowsRescisao['inss_dt'];
            $dados_recisao["112.2"] = array(
                "movimento" => "Previdência Social - 13º Salário",
                "tipo" => "DEBITO",
                "valor" => $varInssDt
            );

            //IRRF
            $varIrrfSaldoSalario += $rowsRescisao['ir_ss'];
            $dados_recisao["114.1"] = array(
                "movimento" => "IRRF",
                "tipo" => "DEBITO",
                "valor" => $varIrrfSaldoSalario
            );

            //IRRF
            $varIrrf13 += $rowsRescisao['ir_dt'];
            $dados_recisao["114.2"] = array(
                "movimento" => "IRRF sobre 13º Salário",
                "tipo" => "DEBITO",
                "valor" => $varIrrf13
            );

            //IRRF
            $varIrrfFerias += $rowsRescisao['ir_ferias'];
            $dados_recisao[116] = array(
                "movimento" => "IRRF Férias",
                "tipo" => "DEBITO",
                "valor" => $varIrrfFerias
            );
        }    

        //ORDERNANDO O ARRAY
        ksort($dados_recisao);
        
        return $dados_recisao;
        
    }
    
    /**
     * MÉTODO DE RESCISÃO POR ID DO CLT
     * @param type $clt
     * @param type $mes
     * @param type $ano
     * @return type
     */
    public function getRescisaoComplementarByClt($clt, $mes =NULL, $ano=NULL) {
        
        $where = '';
        
        if(!is_null($mes)){
            $where .= ' AND MONTH(data_demi) = '.sprintf("%02d", $mes);
        }
        if(!is_null($ano)){
            $where .=' AND YEAR(data_demi) = '.$ano;
        }
        
        $query = "SELECT * FROM rh_recisao WHERE id_clt = '{$clt}' $where  AND status = '1' AND rescisao_complementar = '1'";
        $dados_rescisao = mysql_query($query) or die("Erro ao selecionar rescisão");
        return $dados_rescisao;
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getRescisao($id) {
        $sql_rescisao = "SELECT A.*, DATE_FORMAT(A.data_aviso, '%d/%m/%Y') AS data_aviso_f, DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_demi_f, IF((A.motivo!=63 OR A.motivo!=65), '479', '480') AS campo_multa,IF(A.aviso='trabalhado','Aviso Prévio trabalhado','Aviso Prévio trabalhado') AS tipo_aviso, B.causa_afastamento, B.codigo_afastamento,
                (SELECT data_aquisitivo_ini FROM rh_ferias WHERE id_clt = A.id_clt AND status = '1' ORDER BY id_ferias DESC LIMIT 1) AS data_aquisitivo_ini,
                (SELECT data_aquisitivo_ini FROM rh_ferias WHERE id_clt = A.id_clt AND status = '1' ORDER BY id_ferias DESC LIMIT 1) AS data_aquisitivo_fim,
                IF((SELECT ferias_vencidas   FROM rh_ferias WHERE id_clt = A.id_clt AND status = '1' ORDER BY id_ferias DESC LIMIT 1)  > 0, '12/12 avos','0/12 avos') AS ferias_vencidas 
                FROM rh_recisao AS A 
                LEFT JOIN rhstatus AS B ON(A.motivo=B.codigo)
                WHERE A.id_recisao = '$id' AND A.`status` = '1'";

//        echo "<!--" . $sql_rescisao . "-->";

        $result = mysql_query($sql_rescisao);
        $rescisao = array();
        while ($resp = mysql_fetch_array($result)) {
            $rescisao[] = $resp;
        }
        return $rescisao;
    }

    /**
     * VERIFICA VALORES DE RESCISÕES COMPLEMENTARES
     */
    public function getRescisaoComplementar($clt, $mes, $ano, $projeto, $debug = false) {
        //DADOS DA RESCISAO COMPLEMENTAR
        $dados_resc_complementar = $this->getDadosRescisao($mes, $ano, $projeto, "C");
        while ($d = mysql_fetch_assoc($dados_resc_complementar)) {

            //MODO DEBUG
            if ($debug) {
                $this->getDebug($d);
            }
        }
    }


    /**
     * LEI A477 - ATRASO DE RESCISÃO
     * @param type $id_clt
     * @return type
     */
    function getA477($id_clt) {
        $sql_salario = "SELECT B.salario FROM rh_clt AS A LEFT JOIN curso AS B ON(A.id_curso=B.id_curso) WHERE id_clt=$id_clt";
//        echo "<!--" . $sql_salario . "-->";
        $salario = mysql_fetch_array(mysql_query($sql_salario));
        return $salario['salario'];
    }
    
    /**
     * 
     * @param type $id_clt
     * @param type $id_regiao
     * @param type $id_projeto
     * @param type $processado_por
     * @param type $processado_em
     * @param type $acao
     * @param type $tipo
     */
    public function criaLog($id_clt,$id_regiao,$id_projeto,$processado_por,$processado_em,$acao,$tipo){
        $retorno = false;
        $query = "INSERT INTO rescisao_log (id_clt,id_regiao,id_projeto,processado_por,processado_em,acao,tipo) VALUES (
                    '{$id_clt}','{$id_regiao}','{$id_projeto}','{$processado_por}','{$processado_em}','{$acao}','{$tipo}')";
        $sql = mysql_query($query) or die("erro ao gravar log de recisão");
        if($sql){
            $retorno = true; 
        }
        
        return $retorno;
    }
    
    /**
     * MÉTODO PARA VERIFICA SE A RECISÃO FOI PAGA
     * @param type $id_recisao
     * @param type $id_regiao
     * @param type $id_clt
     */
    public function verificaSaidaPagaDeRecisao($id_recisao, $id_regiao, $id_clt){
        
        $dados = array("status" => false);
        $query = "SELECT A.id_recisao, A.nome AS nome_clt, DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_demissao, C.valor, C.data_pg, if(C.status = 2, 'Pago','') AS status_saida
                    FROM rh_recisao AS A
                    LEFT JOIN pagamentos_especifico AS B ON(A.id_recisao = B.id_rescisao)
                    LEFT JOIN saida AS C ON(B.id_saida = C.id_saida)
                    WHERE A.id_recisao = '{$id_recisao}' AND C.status = '2'";
        
        //VERIFICA SE EXISTE SAIDA PAGA            
        $sql = mysql_query($query) or die("Erro ao verificar se existe saida paga para a recisão");
        if(mysql_num_rows($sql) > 0){
            $d = mysql_fetch_assoc($sql);
            $dados = array("status" => false, "dados" => $d);
        }else{
            $remove_movimentos = $this->removeMovimentosRecisao($id_clt, $id_recisao);
            if($remove_movimentos){
                $remove_saidas = $this->removeSaidaRescisao($id_clt, $id_regiao);
                $remove_complementar = $this->removeRescisaoComplementar($id_recisao);
                $msg = utf8_encode("Recisão desprocessada com sucesso");
                $dados = array("status" => true, "msg" => $msg);
            }
        }        
        
        echo json_encode($dados);
        exit();
                    
    }
    
    /**
     * REMOVE OS MOVIMENTOS DE REVISÃO, ATUALIZA O STATUS DA RECISÃO PARA 0, ATUALIZA DO CLT STATUS,DATA_SAIDA,DATA_DEMI
     * @param type $id_clt
     * @return boolean
     */
    public function removeMovimentosRecisao($id_clt, $id_recisao){
        $retorno = false;
        //ANTES DE DELETAR OS MOVIMENTOS DA TABELA DE RESCISAO, PRECISO VOLTAR OS MOVIMENTOS FUTUROS
        $sqlBuscaMovFuturo = "SELECT * FROM rh_movimentos_rescisao WHERE id_clt = '{$id_clt}' AND mov_futuro = 1";
        $resBuscaMovFuturo = mysql_query($sqlBuscaMovFuturo);
        while($rowMovFuturo = mysql_fetch_assoc($resBuscaMovFuturo)){
            //VOLTANDO AS INFORAMÇÕES DOS MOVIMENTOS FUTUROS
            mysql_query("UPDATE rh_movimentos_clt SET mes_mov = '{$rowMovFuturo['mes_mov']}' WHERE id_movimento = '{$rowMovFuturo['id_movimento']}'");
        }
        
        $query = "DELETE FROM rh_movimentos_rescisao WHERE id_clt = '{$id_clt}'";
        $sql = mysql_query($query) or die("Erro ao excluir movimentos de recisão");
        if($sql){
            //ATUALIZA STATUS DA RECISÃO PARA 0
            $atualiza_stauts_recisao = sqlUpdate("rh_recisao", array("status" => 0), "id_recisao = '{$id_recisao}' LIMIT 1");
            if($atualiza_stauts_recisao){
                //ATUALIZA DADOS DO CLT
                $atualiza_clt = sqlUpdate("rh_clt", array("status" => 200, "data_saida" => '', "status_demi" => ''), "id_clt = '{$id_clt}' LIMIT 1");
                if($atualiza_clt){
                    
                    //TA NOJENTO, SÓ ENQUANTO A ENTIDADE CLT NÃO FICA PRONTA
                    $query = "SELECT * FROM rh_clt WHERE id_clt = '{$id_clt}'";
                    $sql = mysql_query($query) or die("Erro ao selecionar clt");
                    $dados_clt = mysql_fetch_assoc($sql);
                    $data_cad = date("Y-m-d H:i:s");
                    $this->criaLog($id_clt, $dados_clt['id_regiao'], $dados_clt['id_projeto'], $_COOKIE['logado'], $data_cad, 2, 1);
                    
                    $retorno = true;
                }
            }
            
        }
        
        return $retorno;
    }
    
    /**
     * Método para mudar status das saidas geradas pela rescisão
     * @param type $id_clt
     * @param type $id_regiao
     * @return boolean
     */
    public function removeSaidaRescisao($id_clt, $id_regiao){
        $retorno = true;
        $query = "SELECT * FROM saida WHERE tipo = 170 AND id_regiao = '{$id_regiao}' AND id_clt = '{$id_clt}' AND status = 1";
        $sql = mysql_query($query) or die("Erro ao selecionar saidas");
        $array = array();
        if(mysql_num_rows($sql) > 0){
            while($linha = mysql_fetch_assoc($sql)){
                $array[] = $linha['id_saida'];
            }
            $query_update = "UPDATE saida SET status = 0 WHERE id_saida IN(" .  implode(",", $array). ")";
            $sql_update = mysql_query($query_update) or die("Erro ao trocar status da saida");
        }
        return $retorno;
    }
    
    /**
     * DESPROCESSA rescisão COMPLEMENTAr
     * @param type $id_rescisao
     * @return boolean
     */
    public function removeRescisaoComplementar($id_rescisao){
        $retorno = true;
        $query_update = "UPDATE rh_recisao SET status = 0 WHERE vinculo_id_rescisao = '{$id_rescisao}' AND status = 1";
        $sql_update = mysql_query($query_update) or die("Erro ao trocar status da rescisao complementar");
        
        return $retorno;
    }
    
    /**
     * USADO PARA PEGAR OS VALORES NA FOLHA
     * 
     * @param type $id_clt
     * @param type $mes
     * @param type $ano
     */
    public function getRescisaoFolha($id_clt, $mes, $ano) {
        
            $rescisao     = $this->getRescisaoByClt($id_clt, $mes, $ano);
            $row_rescisao = mysql_fetch_assoc($rescisao);
            $num_rescisao = mysql_num_rows($rescisao);
            
            if(!empty($num_rescisao)) {       
                
            // VariÃ¡veis para Linha e Update do Participante
            $dias          = $row_rescisao['dias_saldo'];
            $salario       = $row_rescisao['saldo_salario'];
            $base          = $row_rescisao['sal_base'];
            $base_inss      = $salario;
            $base_irrf      = $salario;
            $base_fgts      = $salario;
    
            if( (!empty($row_rescisao['base_inss_ss']) and $row_rescisao['base_inss_ss'] !='0.00') 
               or (!empty($row_rescisao['base_inss_13']) and $row_rescisao['base_inss_13'] !='0.00') ){

               $base_inss_13_rescisao = $row_rescisao['base_inss_13'];
               $base_inss             = $row_rescisao['base_inss_ss'];
               $base_irrf             = $row_rescisao['base_irrf_ss'] + $row_rescisao['base_irrf_13'];
               $base_fgts             = $row_rescisao['base_inss_ss'];
            
            } else {

                    $qr_movimentos = mysql_query("SELECT B.descicao, B.id_mov, A.valor, B.campo_rescisao, B.categoria
                    FROM rh_movimentos_rescisao as A 
                    INNER JOIN
                    rh_movimentos as B
                    ON A.id_mov = B.id_mov
                    WHERE A.id_clt = '$row_rescisao[id_clt]' 
                    AND A.id_rescisao = '$row_rescisao[id_recisao]' 
                    AND A.status = 1 AND A.incidencia = '5020,5021,5023'") or die(mysql_error());
                    while($row_movimentos = mysql_fetch_assoc($qr_movimentos)){  

                          $movimentos_resc[$row_movimentos['campo_rescisao']] += $row_movimentos['valor']; 

                            if($row_movimentos['categoria'] == 'CREDITO'){
                                $base_inss += $row_movimentos['valor'];
                                $base_irrf += $row_movimentos['valor'];
                                $base_fgts += $row_movimentos['valor'];
                            }elseif($row_movimentos['categoria'] == 'DEBITO' or $row_movimentos['categoria'] == 'DESCONTO'){
                                $base_inss -= $row_movimentos['valor'];
                                $base_irrf -= $row_movimentos['valor'];
                                $base_fgts -= $row_movimentos['valor'];
                            }
                    }
                    
                    $base_inss_13_rescisao   = $row_rescisao['dt_salario'];
                    $base_inss_13_rescisao  += $row_rescisao['terceiro_ss'];                    
                    $base_inss      += $row_rescisao['insalubridade'];
                    $base_irrf      += $row_rescisao['dt_salario'] ;
                    $base_irrf      += $row_rescisao['terceiro_ss'] ;
                    $base_irrf      += $row_rescisao['insalubridade'];                    
                    $base_fgts      += $row_rescisao['insalubridade'];	
            }

            $inss_rescisao = $row_rescisao['previdencia_ss'] + $row_rescisao['previdencia_dt'];
            $inss_completo = $inss_rescisao;

            $irrf_rescisao = $row_rescisao['ir_ss'] + $row_rescisao['ir_dt'];
            $irrf_completo = $irrf_rescisao;

            $rendimentos   = $row_rescisao['total_rendimento'] - $row_rescisao['saldo_salario'];
            $descontos     = $row_rescisao['total_deducao'] + $row_rescisao['total_liquido'] - $inss_completo - $irrf_completo;

            $toDescontos   = $descontos + $inss_completo + $irrf_completo;
            $toRendimentos = $salario + $rendimentos;
            $liquido        = round($row_rescisao['total_liquido'], 2); 
            
            if($base_inss < 0) { $base_inss = 0;}
            if($base_irrf < 0) { $base_irrf = 0;}
            if($base_fgts < 0) { $base_fgts = 0;}      
            if($liquido <=0.01){ $liquido =   0;} 

            // VariÃ¡veis para Estatistica do Participante
           // $Trab     -> calculo_proporcional($salario_limpo, $dias);
            //$valor_dia = $Trab -> valor_dia;

            // VariÃ¡veis para Estatistica da Folha
            $valor_rescisao    = $rendimentos;
            $desconto_rescisao = $descontos;
            
            
            
            if(in_array($row_rescisao['motivo'], array(61,63,64,66))){
                $resultado['base_fgts_sefip'] += $base_fgts;
            }
            
            
            $resultado['num_rescisao']          = $num_rescisao;
            $resultado['dias_saldosalario']     = $dias;
            $resultado['salario']               = $salario;
            $resultado['base_inss']             = $base_inss;
            $resultado['base_inss_13_rescisao'] = $base_inss_13_rescisao;
            $resultado['base_irrf']             = $base_irrf;
            $resultado['base_fgts']             = $base_fgts;
            $resultado['base_inss_13_rescisao'] = $base_inss_13_rescisao;
            $resultado['inss_rescisao']         = $inss_rescisao;     
            $resultado['inss_completo']         = $inss_completo;
            $resultado['irrf_rescisao']         = $irrf_rescisao;
            $resultado['irrf_completo']         = $irrf_completo;
            $resultado['rendimentos']           = $rendimentos;
            $resultado['total_rendimentos']     = $toRendimentos;            
            $resultado['descontos']             = $descontos;
            $resultado['total_desconto']        = $toDescontos;
            $resultado['valor_rescisao']        = $valor_rescisao;
            $resultado['desconto_rescisao']     = $desconto_rescisao;
            $resultado['liquido']               = $liquido;
            
            if($_COOKIE['logado'] == 179){
                echo "<pre>";
                    //print_r($row_rescisao);
                echo "</pre>";
            }
            
            return $resultado;
        }
    }
    
    public function getTotalRescisaoByFolha($projeto, $mes = array(), $ano = array()){
        $dados = array();
        
        if(is_array($mes)){
            $meses = implode($mes, ",");
        }
        
        if(is_array($ano)){
            $anos = implode($ano, ",");
        }
        
        $query = "SELECT MONTH(A.data_demi) AS mes, YEAR(A.data_demi) AS ano, A.fator, C.nome, SUM(A.total_rendimento - A.saldo_salario) AS subtotal
                    FROM rh_recisao AS A
                    LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                    LEFT JOIN curso AS C ON(B.id_curso = C.id_curso)
                    WHERE MONTH(A.data_demi) IN({$meses}) AND YEAR(A.data_demi) IN ({$anos})
                    AND A.id_projeto = '{$projeto}' AND A.status = 1 AND A.recisao_provisao_de_calculo = 0
                    GROUP BY mes,ano,C.nome  
                    ORDER BY mes, fator";       
        //echo $query;            
              
         $sql = mysql_query($query) or die("Erro ao selecionar rescisao");
         if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[$rows['mes']][$rows['ano']]["nome"] = "RESCISÃO"; 
                $dados[$rows['mes']][$rows['ano']]["cargo"][$rows["fator"]][$rows['nome']] = $rows['subtotal']; 
            }
        }
        
        return $dados; 
                    
        
    }
    
    /* INICIO PV */
    
    /**
     * Método para pegar os dados do CLT para a Rescisão
     * @param type $id_clt
     * @param type $motivo_rescisao  - Tipo da rescisao 
     * @return type
     */
    public function calculaRescisao(Array $dados){     
        
        $id_clt = isset($dados['id_clt']) ? $dados['id_clt'] : NULL;
        $dispensa = isset($dados['dispensa']) ? $dados['dispensa'] : NULL;
        
        
         $sql = "SELECT A.data_entrada, A.data_demi, TIMESTAMPDIFF(DAY , A.data_entrada, A.data_demi) AS periodo_dias, TIMESTAMPDIFF(MONTH , A.data_entrada, A.data_demi) AS periodo_meses , @var_periodo_anos :=TIMESTAMPDIFF(YEAR , A.data_entrada, A.data_demi) AS periodo_anos , A.id_clt, A.nome, A.campo3, A.data_demi, A.data_entrada, A.id_projeto, A.id_curso, A.id_regiao, A.id_curso, DATE_FORMAT(data_entrada, '%d/%m/%Y') AS data_entradaF, DATE_FORMAT(data_demi, '%d/%m/%Y') AS data_demiF, A.insalubridade, A.desconto_inss, A.tipo_desconto_inss, A.valor_desconto_inss, A.trabalha_outra_empresa, A.salario_outra_empresa, A.desconto_outra_empresa, IF(DATEDIFF(data_demi, data_entrada) >= 365, 1, 0) AS um_ano, B.salario, B.nome AS nome_funcao, B.tipo_insalubridade, B.qnt_salminimo_insalu, B.periculosidade_30, C.especifica AS tipo_rescisao
                FROM rh_clt AS A
                INNER JOIN curso AS B ON B.id_curso = A.id_curso
                LEFT JOIN rhstatus AS C ON C.codigo = 61
                LEFT JOIN rescisao_config AS D ON(C.codigo=D.tipo AND D.ano=IF(@var_periodo_anos >= 1, 1, 0))
                WHERE id_clt = '{$id_clt}'";
                        
         //echo $sql.'<br>';
         
         $arr = mysql_fetch_assoc(mysql_query($sql));
        
         return $this->setDados($arr);
         
    }
     private function setDados(Array $arr){
        $arr_keys = array_keys($arr);
        $arr_values = array_values($arr);
        
        for($x=0; $x<count($arr_keys); $x++){
            $campo = $arr_keys[$x];
            $this->$campo = $arr_values[$x];
        }
        return $this;
    }
    
    /* FIM PV */
    
    /**
     * MÉTODO PARA RETORNAR TOTALIZADORES DE RESCISÃO
     * @param type $projeto
     * @param type $mes_referente
     */
    public function getTotalizadorRescisaoByFolha($projeto, $mes_referente){
        
        //$this->getDebug($projeto);
        
        $dados = array();
        $rescisoes = array();
        
        $query = "SELECT A.*, CONCAT(MONTH(A.data_demi),'/',YEAR(A.data_demi)) AS periodo
            FROM rh_recisao AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN projeto AS C ON(A.id_projeto = C.id_projeto)
            WHERE DATE_FORMAT(A.data_demi, '%m/%Y')  = '{$mes_referente}'
            AND A.id_projeto = '{$projeto}' AND A.status = 1 AND A.recisao_provisao_de_calculo = 0";
         
        try{
           $sql = mysql_query($query); 
           while($rows = mysql_fetch_assoc($sql)){
               
               $rescisoes[] = $rows['id_recisao'];
               
               /**
                * SALÁRIO
                */
               $dados["CREDITO"]["saldo_salario"]["nome"] = "Saldo de Salário";
               $dados["CREDITO"]["saldo_salario"]["valor"] += $rows['saldo_salario'];
               
               /**
                * COMISSÕES 
                */
               $dados["CREDITO"]["comissoes"]["nome"] = "Comissões";
               $dados["CREDITO"]["comissoes"]["valor"] += $rows['comissao'];
               
               /**
                * INSALUBRIDADE
                */
               $dados["CREDITO"]["insalubridade"]["nome"] = "Insalubridade";
               $dados["CREDITO"]["insalubridade"]["valor"] += $rows['insalubridade'];
               
               /**
                * MULTA 477
                */
               $dados["CREDITO"]["477"]["nome"] = "Multa Art. 477";
               $dados["CREDITO"]["477"]["valor"] += $rows['a477'];
                              
               /**
                * MULTA 479
                */
               $dados["CREDITO"]["479"]["nome"] = "Multa Art. 479";
               $dados["CREDITO"]["479"]["valor"] += $rows['a479'];
               
               /**
                * SALARIO FAMÍLIA
                */
               $dados["CREDITO"]["salario_familia"]["nome"] = "Salário Família";
               $dados["CREDITO"]["salario_familia"]["valor"] += $rows['sal_familia'];
               
               /**
                * 13° SALARIO PROPORCIONAL
                */
               $dados["CREDITO"]["dt_salario"]["nome"] = "13° Salário Proporcional";
               $dados["CREDITO"]["dt_salario"]["valor"] += $rows['dt_salario'];
               
               /**
                * FERIAS PROPORCIONAIS
                */
               $dados["CREDITO"]["ferias_proporcional"]["nome"] = "Férias Proporcionais";
               $dados["CREDITO"]["ferias_proporcional"]["valor"] += $rows['ferias_pr'];
               
               /**
                * FÉRIAS VENCIDAS
                */
               $dados["CREDITO"]["ferias_vencidas"]["nome"] = "Férias Vencidas";
               $dados["CREDITO"]["ferias_vencidas"]["valor"] += $rows['ferias_vencidas'];
               
               /**
                * 1/3 FÉRIAS PROPORCIONAIS
                */
               $dados["CREDITO"]["um_terco_fp"]["nome"] = "Terço Constitucional de Férias";
               $dados["CREDITO"]["um_terco_fp"]["valor"] += $rows['umterco_fp'];
               
               /**
                * AVISO PRÉVIO
                */
               $dados["CREDITO"]["aviso_previo"]["nome"] = "Aviso Prévio Indenizado";
               $dados["CREDITO"]["aviso_previo"]["valor"] += ($rows['fator'] == "empregador") ? $rows['aviso_previo'] : 0;
               
               /**
                * 13° SALÁRIO (AVISO PRÉVIO INDENIZADO)
                */
               $dados["CREDITO"]["terceiro_ss"]["nome"] = "13° Salário (Aviso-Prévio Indenizado)";
               $dados["CREDITO"]["terceiro_ss"]["valor"] += $rows['terceiro_ss'];
               
               /**
                * FÉRIAS (AVISO PRÉVIO INDENIZADO) 
                */
               $dados["CREDITO"]["ferias_aviso_indenizado"]["nome"] = "Férias (Aviso-prévio Indenizado)";
               $dados["CREDITO"]["ferias_aviso_indenizado"]["valor"] += $rows['ferias_aviso_indenizado'];
               
               /**
                * FÉRIA EM DOBRO
                */
               $dados["CREDITO"]["ferias_dobro"]["nome"] = "Férias em dobro";
               $dados["CREDITO"]["ferias_dobro"]["valor"] += $rows['fv_dobro'];
               
               /**
                * 1/3 FÉRIAS EM DOBRO
                */
               $dados["CREDITO"]["terco_ferias_dobro"]["nome"] = "1/3 Férias em Dobro";
               $dados["CREDITO"]["terco_ferias_dobro"]["valor"] += $rows['umterco_ferias_dobro'];
               
               /**
                * Ajuste do Saldo Devedor
                */
               $dados["CREDITO"]["ajuste_saldo_devedor"]["nome"] = "Ajuste Do Saldo Devedor";
               $dados["CREDITO"]["ajuste_saldo_devedor"]["valor"] += $rows['arredondamento_positivo'];
               
               /**
                * 1/3 FÉRIAS AVISO PRÉVIO INDENIZADO
                */
               $dados["CREDITO"]["terco_ferias_aviso_indenizado"]["nome"] = "1/3 FÉRIAS (AVISO PRÉVIO INDENIZADO)";
               $dados["CREDITO"]["terco_ferias_aviso_indenizado"]["valor"] += $rows['umterco_ferias_aviso_indenizado'];
               
               /**
                * LEI 12.506
                */
               $dados["CREDITO"]["lei_12_506"]["nome"] = "LEI 12.506";
               $dados["CREDITO"]["lei_12_506"]["valor"] += $rows['lei_12_506'];
               
               
               /**
                * AVISO PRÉVIO
                */
               $dados["DEBITO"]["aviso_previo"]["nome"] = "Aviso Prévio Indenizado";
               $dados["DEBITO"]["aviso_previo"]["valor"] += ($rows['fator'] == "empregado") ? $rows['aviso_previo'] : 0;
               
               /**
                * PREVIDÊNCIA SOCIAL
                */
               $dados["DEBITO"]["previdencia_social"]["nome"] = "Previdência Social";
               $dados["DEBITO"]["previdencia_social"]["valor"] += $rows['previdencia_ss'];
               
               /**
                * PREVIDENCIA SOCIAL 13°
                */
               $dados["DEBITO"]["previdencia_social"]["nome"] = "Previdência Social - 13° Salario";
               $dados["DEBITO"]["previdencia_social"]["valor"] += $rows['previdencia_dt'];
               
               /**
                * IR_SS
                */
               $dados["DEBITO"]["ir_ss"]["nome"] = "IRRF";
               $dados["DEBITO"]["ir_ss"]["valor"] += $rows['ir_ss'];
               
               /**
                * IR_DT
                */
               $dados["DEBITO"]["ir_dt"]["nome"] = "IRRF sobre 13° Salário";
               $dados["DEBITO"]["ir_dt"]["valor"] += $rows['ir_dt'];               
               
               /**
                * DEVOLUÇÃO
                */
               $dados["DEBITO"]["devolucao"]["nome"] = "Dedução";
               $dados["DEBITO"]["devolucao"]["valor"] += $rows['devolucao'];
               
               /**
                * ADIANTAMENTO 13°
                */
               $dados["DEBITO"]["adiantamento_13"]["nome"] = "Adiantamento de 13°";
               $dados["DEBITO"]["adiantamento_13"]["valor"] += $rows['adiantamento_13'];
               
               /**
                * FALTAS
                */
               $dados["DEBITO"]["faltas"]["nome"] = "Faltas";
               $dados["DEBITO"]["faltas"]["valor"] += $rows['valor_faltas'];
               
               /**
                * FALTAS
                */
               $dados["DEBITO"]["irrf_ferias"]["nome"] = "IRRF Férias";
               $dados["DEBITO"]["irrf_ferias"]["valor"] += $rows['ir_ferias'];
               
               $dados["RESUMO"]["qnt"] += 1;
               $dados["RESUMO"]["valor"] += $rows['total_liquido'];
                              
           } 
        }  catch (Exception $e){
            echo $e->getMessage("Erro ao Selecionar dados da rescisão");
        }
        
        if(!empty($rescisoes)){
            $dados["CREDITO"] += $this->getMovimentosCreditoDeRescisao($rescisoes);
            $dados["DEBITO"] += $this->getMovimentosDebitoDeRescisao($rescisoes);
        }
        
        return $dados;
    }
    
    /**
     * MÉTODO RETORNA TODOS OS MOVIMENTOS DE CREDITO QUE JA FORAM USADOS EM RESCISÃO
     * @return type
     */
    public function getMovimentosCreditoDeRescisao($rescisoes = array()){
        //$this->getDebug($rescisoes);
        $movimentos = array();
            
            $query = "SELECT B.cod, A.nome_movimento, A.valor, SUM(A.valor) AS total
                FROM rh_movimentos_rescisao AS A 
                LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                WHERE A.id_rescisao IN(" .  implode(",", $rescisoes). ") AND B.categoria = 'CREDITO'
                GROUP BY B.cod";
            
            try{
                
                $sql = mysql_query($query);
                if(mysql_num_rows($sql) > 0){
                    while($rows = mysql_fetch_assoc($sql)){
                        $movimentos[$rows['cod']]["nome"] = $rows['nome_movimento'];
                        $movimentos[$rows['cod']]["valor"] += $rows['total'];
                    }
                }
                
            }catch (Exception $e){
                echo $e->getMessage("Erro ao selecionar rescisões para essa folha");
            }
               
            
        return $movimentos;
    }
    
    /**
     * MÉTODO RETORNA TODOS OS MOVIMENTOS DE DEBITO QUE JA FORAM USADOS EM RESCISÃO
     * @return type
     */
    public function getMovimentosDebitoDeRescisao($rescisoes = array()){
        //$this->getDebug($rescisoes);
        $movimentos = array();
            
            $query = "SELECT B.cod, A.nome_movimento, A.valor, SUM(A.valor) AS total
                FROM rh_movimentos_rescisao AS A 
                LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                WHERE A.id_rescisao IN(" .  implode(",", $rescisoes). ") AND B.categoria IN('DEBITO','DESCONTO')
                GROUP BY B.cod";
            
            try{
                
                $sql = mysql_query($query);
                if(mysql_num_rows($sql) > 0){
                    while($rows = mysql_fetch_assoc($sql)){
                        $movimentos[$rows['cod']]["nome"] = $rows['nome_movimento'];
                        $movimentos[$rows['cod']]["valor"] += $rows['total'];
                    }
                }
                
            }catch (Exception $e){
                echo $e->getMessage("Erro ao selecionar rescisões para essa folha");
            }
               
            
        return $movimentos;
    }
    
    public function listTiposRescisao($retorno="resource"){
        $sql = "SELECT id_status,especifica,codigo,saldo,codigo_afastamento,causa_afastamento,codigo_saque,cod_movimentacao,cod_esocial,motivo FROM rhstatus WHERE tipo = 'recisao' ORDER BY CAST(codigo AS SIGNED)";
        $result = mysql_query($sql);
        
        if($retorno=="resource"){
            return $result;
        }else{
            $opt = array(""=>"« Selecione »");
            
            while($row = mysql_fetch_assoc($result)){
                $opt[$row['codigo']] = $row['codigo']." - ".$row['especifica'];
            }
            
            return $opt;
        }
        
    }
    
    
    /**
     * MÉTODO DE DEBUG
     * @param type $objet
     */
    public function getDebug($objet) {
        echo "<pre>";
        print_r($objet);
        echo "</pre>";
    }
    
    /**
     * Metodo para verificar se quantidade de faltas
     * é maior do que 15 no mês
     * @param type $clt
     * @param type $ano
     * @return type
     */
    public function qtdFaltas($clt, $ano, $cod_mov = null) {
        $sql = "SELECT SUM(qnt) AS qnt_faltas, data_movimento, mes_mov, ano_mov, mes_anterior, mes, ano FROM
            
            (SELECT A.id_clt, A.qnt, A.data_movimento, A.mes_mov, A.ano_mov, A.mes_anterior,
            
            IF(A.mes_anterior = 1 && A.mes_mov != 16, CAST(DATE_FORMAT(DATE_ADD(CONCAT(A.ano_mov, '-', A.mes_mov, '-', '01'), INTERVAL -1 MONTH),'%m') AS UNSIGNED),
            IF(A.mes_anterior = 1 && A.mes_mov = 16, CAST(DATE_FORMAT(DATE_ADD(B.data_demi, INTERVAL -1 MONTH),'%m') AS UNSIGNED),
            IF(A.mes_anterior != 1 && A.mes_mov = 16, MONTH(B.data_demi),
            A.mes_mov))) AS mes,
            
            IF(A.mes_anterior = 1 && A.mes_mov != 16, DATE_FORMAT(DATE_ADD(CONCAT(A.ano_mov, '-', A.mes_mov, '-', '01'), INTERVAL -1 MONTH),'%Y'),
            IF(A.mes_anterior = 1 && A.mes_mov = 16, DATE_FORMAT(DATE_ADD(B.data_demi, INTERVAL -1 MONTH),'%Y'),
            A.ano_mov)) AS ano
            
            FROM rh_movimentos_clt AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            WHERE A.cod_movimento IN({$cod_mov}) AND A.id_clt = {$clt} AND A.ano_mov = {$ano} AND A.status = 1) tot_faltas
            
            WHERE ano = {$ano}
            GROUP BY mes";
        $qry = mysql_query($sql) or die(mysql_error());
        return $qry;
    }
    
    /**
     * Insere os dados na tabela de configuraçao da rescisão
     * Para no momento da impressão do aviso, já guardar o tipo de dispensa entre outros
     */   
    public function inserePreRescisao($dados){
        //ACERTA DATAS
        if(isset($dados['data_aviso'])){
            $dados['data_aviso'] = date("Y-m-d", strtotime(str_replace("/", "-", $dados['data_aviso'])));
        }
        if(isset($dados['data_demi'])){
            $dados['data_demi'] = date("Y-m-d", strtotime(str_replace("/", "-", $dados['data_demi'])));
        }
        
        //REMOVE CAMPO logado
        unset($dados['logado']);
        unset($dados['PHPSESSID']);
        
        //ADICIONANDO CAMPOS AUTOMATICOS
        $dados['processado_em'] = date("Y-m-d H:i:s");
        $dados['processado_por'] = $_COOKIE['logado'];
        
        $_campos = implode(",",array_keys($dados));
        $_valores = implode("','",$dados);
        $qr = "INSERT INTO rh_rescisao_clt_conf ({$_campos}) VALUES ('{$_valores}')";
        mysql_query($qr) or die("mysql: ".  mysql_error());
        return mysql_insert_id();
    }
    
    public function getDadosRescisaoCltConf($id_clt){
        //TRAZ A ULTIMA CONFIGURAÇÃO FEITA PARA O CLT
        $sql = "SELECT * FROM rh_rescisao_clt_conf WHERE id_clt = {$id_clt} ORDER BY id_rescisao_clt_conf DESC LIMIT 1;";
        $result = mysql_query($sql) or die(mysql_error());
        if(mysql_num_rows($result) == 1){
            $row = mysql_fetch_assoc($result);
        }else{
            $row['tipo'] = null;
            $row['fator'] = null;
        }
        
        return $row;
    }
    
    
    /**
     * METODO PARA RETORNA INFORMAÇÕES 
     * DE CONFIGURAÇÕES DE RESCISAO 
     * PARA INFORMAÇÕES DE TIPO RESCISÃO
     * @param type $dispensa
     * @param type $um_ano
     */
    public function getCodigosMovimentacao($dispensa, $um_ano, $clt){
        $dados = array();
        $query = "SELECT A.especifica, A.codigo_saque, A.cod_movimentacao, B.* FROM rhstatus as A
                            INNER JOIN rescisao_config as B 
                            ON A.codigo = B.tipo
                            WHERE A.codigo = '$dispensa' AND ano = '$um_ano'";
        
        $sql = mysql_query($query) or die("erro em selecionar movimentação");
        
        $cltQuery = "SELECT * FROM rh_clt AS A WHERE A.id_clt  = '{$clt}'";
        $cltSql = mysql_query($cltQuery);
        $tipoContrato = "";
        while($row = mysql_fetch_assoc($cltSql)){
            $tipoContrato = $row['tipo_contrato'];
        }
        
        while($rows = mysql_fetch_assoc($sql)){
            
            if($tipoContrato == 1){
                $codigoSaque = "01";
            }else if($tipoContrato == 3){
                $codigoSaque = "04";
            }
            
            $dados = array(
                "codigo_saque" => $codigoSaque, 
                "cod_movimentacao" => $rows['cod_movimentacao']
            );
        }
        
        return $dados;
        
    }
    
    /**
     * Métodod que busca as ultimas folhas, pega os (ids_movimentos_estatisticas) para calculo de média
     * Retorno um array de informações
     * @param int $id_clt
     * @param int $meses_trabalhados defalt = nulll
     * @return array
     * @author Ramon Lima <ramon@f71.com.br>
     * 
     */
    public function getMovimentosFixoParaMedia($id_clt,$meses_trabalhados=null,$data_demi=null) {
        /////////////////////
        // MOVIMENTOS FIXOS //
        ///////////////////
        
        $explodeDataDemi = explode('-', $data_demi);
        $anoDataDemi = $explodeDataDemi[0];
        
        /*TODA HORA MUDAM ISSO, OU É PELA QNT DE MESES OU FIXO POR 12*/
        $fator_divisivel = 12;
        //$fator_divisivel = $meses_trabalhados;
        
        //PEGANDO AS FOLHAS Q A PESSOA PARTICIPOU
        $qr_folha = mysql_query("SELECT  A.ids_movimentos_estatisticas, B.id_clt,A.mes,A.ano
                    FROM rh_folha as A
                    INNER JOIN rh_folha_proc as B
                    ON A. id_folha = B.id_folha
                    WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2 
                    AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");
        $ids_mov_estati = array();
        while ($row_folha = mysql_fetch_assoc($qr_folha)) {
            if (!empty($row_folha['ids_movimentos_estatisticas'])) {
                $ids_mov_estati[] = $row_folha['ids_movimentos_estatisticas'];
            }
            $mesesFolha[(int)$row_folha['mes']] = (int)$row_folha['ano'];
        }
        
        $paramet = '';
        if(!empty($ids_mov_estati)){
            $paramet = ' id_movimento IN(".implode(",",$ids_mov_estati).") OR ';
        }
        
        //A PEDIDO DA REJANE, COM BASE NO EMAIL ESTOU REMOVENDO O MOVIMENTO DE DIFERENÇA SALARIAL PARA O CALCULO DAS MÉDIAS #13/11/2014
        //A PEDIDO DO ITALO, REMOVENDO DIF SALARIAL DAS MEDIAS #06/07/2016
        //A PEDIDO DA ALANA, REMOVENDO COMPLEMENTO DE SALARIO DAS MEDIAS #27/06/2016
        //A PEDIDO DO ITALO, REMOVENDO DIF SALARIAL DAS MEDIAS #06/09/2016
        //A PEDIDO DO ITALO, REEMBOLSO #21/10/2016
        
        $arr_mov_remover_das_medias = array(90043,90017,5012,90028,80017,6006,90086,90036,90038,90016,90024);
        /*
         * leonardo
         * em 30/01/2017
         * questão de movimentos com insidencia na rescisao que não fazem parte da média. removendo da média movimentos para mes 16 por orientacao da Michele 
         * comentando query antiga
         */
//        $sql_movi = "SELECT *
//                        FROM rh_movimentos_clt
//                        WHERE (id_movimento IN(".implode(",",$ids_mov_estati).") OR (mes_mov = 16 AND status = 1) OR lancamento = 2) AND incidencia = '5020,5021,5023'  
//                        AND tipo_movimento = 'CREDITO' AND id_clt = {$id_clt} AND cod_movimento NOT IN (". implode(",", $arr_mov_remover_das_medias) .")";
        $sql_movi = "SELECT *
                        FROM rh_movimentos_clt
                        WHERE (id_movimento IN(".implode(",",$ids_mov_estati).") OR lancamento = 2) AND incidencia = '5020,5021,5023'  
                        AND tipo_movimento = 'CREDITO' AND id_clt = {$id_clt} AND cod_movimento NOT IN (". implode(",", $arr_mov_remover_das_medias) .")";

//        if($_COOKIE['logado'] == 349){
//            echo "movimentos ===========<br>";
//            echo $sql_movi;
//        }
                        
                        
        $qr_movimentos = mysql_query($sql_movi);  
        
        while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
//            if (in_array($_COOKIE['logado'], $this->programadores)) {
//                echo "QUERY MOVIMENTOS MEDIA : " . $sql_movi . "<br>";
//                echo "<BR>-------------MOVIMENTOS MÉDIA--------------<br><pre> ";
//                //echo "<BR>Qeury MOV: {$sql_movi}<BR><pre> ";
//                print_r($row_mov);
//                echo "</pre>";
//            }

            $movimentos[$row_mov['nome_movimento']] +=$row_mov['valor_movimento'];
            $listMovi[] = $row_mov;
            //BY RAMON (FEAT) SINESIO
            //ADICIONANDO REGRA PARA DUPLICAR MOVIMENTO QND FOR LANÇAMENTO DO TIPO SEMPRE
            if($row_mov['lancamento'] == 2){
                $mesLancamento2[(int)$row_mov['mes_mov']] = (int)$row_mov['mes_mov'];
                $listMoviSempre[(int)$row_mov['mes_mov']] = $row_mov;
            }
        }
        
        
        //ADICIONANDO REGRA PARA DUPLICAR MOVIMENTO QND FOR LANÇAMENTO DO TIPO SEMPRE
        //RODANDO CADA FOLHA
        $anoInicioMovSempre = 9999;
        $mesInicioMovSempre = 99;                               //INICIANDO VARIAVEL COM MES Q NÃO EXISTE, POIS A VALIDAÇÃO É SE O MES É MAIOR
        
        $mesesFolha2= array_keys($mesesFolha);
        $anosFolha2= array_values($anosFolha);
        foreach($mesesFolha as $mes => $ano) {
            if (in_array($mes,$mesLancamento2)){                 //VERIFICA SE TEM MOVIMENTO SEMPRE NA FOLHA Q ESTÁ RODANDO
                $mesInicioMovSempre = $mes;
                $anoInicioMovSempre = $ano;
            }else{
                //CASO O MES DA FOLHA Q ESTA RODANDO AGORA NÃO TENHA O MOVIMENTO LANÇADO SEMPRE
                //PRECISO VERIFICAR SE O MES ANTERIOR TEM O MOVIMENTO SEMPRE
   
//                if(($mes > $mesInicioMovSempre && $ano >= $anoInicioMovSempre)){
                $mes2_digitos = str_pad($mes, 2, '0', STR_PAD_LEFT);
                $mesInicioMovSempre_digitos = str_pad($mesInicioMovSempre, 2, '0', STR_PAD_LEFT);
                if(("{$ano}{$mes2_digitos}" > "{$anoInicioMovSempre}{$mesInicioMovSempre_digitos}")){
                    $novoMes = $mes;                              //ADICIONA O MOVIMENTO PARA O MES QUE ESTÁ RODANDO AGORA
                    $novoAno = $ano;                              //ADICIONA O MOVIMENTO PARA O MES QUE ESTÁ RODANDO AGORA
                    if(!in_array($novoMes, $mesesFolha2) && !in_array($novoAno, $anosFolha2)){           //VERIFICA SE EXISTE O MES SEGUINTE 
                    if (in_array($_COOKIE['logado'], $this->programadores)) {
                        echo 'break';
                    }
//                    if(!in_array($novoAno, $anosFolha2)){           //VERIFICA SE EXISTE O MES SEGUINTE 
                        break;
                    }
                    $dadosMovimentoSempre = $listMoviSempre[$mesInicioMovSempre];
                    $dadosMovimentoSempre['mes_mov'] = $novoMes;
                    $listMovi[] = $dadosMovimentoSempre;
                    $movimentos[$row_mov['nome_movimento']] +=$dadosMovimentoSempre['valor_movimento'];
                    
                }
            }
        }
        
        if (in_array($_COOKIE['logado'], $this->programadores)) {
            echo "<pre>LISTA MOVIMENTOS<br>";
                print_r($listMovi);
            echo "</pre>";
        }
        
        if($id_clt == 2778){
            unset($listMovi[14]);
        }
        
        if($id_clt == 2340){
            unset($listMovi[18]);
        }
        
        /*if (in_array($_COOKIE['logado'], $this->programadores)) {
            echo "<br>-------------MOVIMENTOS MÉDIA (SEMPRE)--------------<br><pre>";
            print_r($mesesFolha);
            print_r($mesLancamento2);
            print_r($listMoviSempre);
            echo "</pre><br>-------------MOVIMENTOS MÉDIA (SEMPRE)--------------<br>";
        }*/
        
        //--------------- MOVIMENTOS FUTUROS ---------------
        //By Ramon 05/07/2016
        //Fiz, a pedido do Sabino, mais algumas horas dps Alana falou q não tem q entrar nas médias
        if(!empty($data_demi)){
            /*list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $data_demi);
            $array_movimentos_futuros = array(90043);
            $sql_get_movimentos = "SELECT A.*, IF(A.lancamento = 1, 'LANÇADO', '') AS tipo_lancamento
                                                    FROM rh_movimentos_clt AS A
                                                    WHERE 
                                                    A.ano_mov >= {$ano_demissao} 
                                                    AND A.mes_mov >= {$mes_demissao} 
                                                    AND A.`status` = 1 
                                                    AND A.cod_movimento IN (".implode(',',$array_movimentos_futuros).")
                                                    AND A.id_mov NOT IN(56)
                                                    AND A.id_clt = '{$id_clt}'

                                                    ORDER BY A.nome_movimento";
            $result_mov_futu = mysql_query($sql_get_movimentos);
            
            if (in_array($_COOKIE['logado'], $this->programadores)) {
                echo "<br>SQL:{$sql_get_movimentos}<br>";
            }
            
            while($row_mov_futuro = mysql_fetch_assoc($result_mov_futu)){
                if (in_array($_COOKIE['logado'], $this->programadores)) {
                    echo "<BR>-------------MOVIMENTOS MÉDIA (FUTURO)--------------<br><pre> ";
                    //echo "<BR>Qeury MOV: {$sql_movi}<BR><pre> ";
                    print_r($row_mov_futuro);
                    echo "</pre>";
                }

                $movimentos[$row_mov_futuro['nome_movimento']] +=$row_mov_futuro['valor_movimento'];
                $listMovi[] = $row_mov_futuro;
            }*/
        }
        
        if (in_array($_COOKIE['logado'], $this->programadores)) {
            echo "<pre>";
            print_r("Meses para Médiass: " . $meses_trabalhados . "<br>");
            echo "</pre>";
        }

        if (sizeof($movimentos) > 0) {
            $total_rendi = (array_sum($movimentos) / $fator_divisivel);
            $total_rendi = number_format($total_rendi, 2, '.', '');
        } else {
            $total_rendi = 0;
        }
        
        $dados['total_rendi']   = $total_rendi;
        $dados['total_movi']    = array_sum($movimentos);
        $dados['movimentos']    = $listMovi;
        $dados['fator_divi']    = $fator_divisivel;
        

        return $dados;
    }
    
    /**
     * Métodod que busca as ultimas folhas, pega os (ids_movimentos_estatisticas) para calculo de média
     * Retorno um array de informações
     * @param int $id_clt
     * @param int $meses_trabalhados defalt = nulll
     * @return array
     * @author Ramon Lima <ramon@f71.com.br>
     * 
     */
    public function getMovimentosFixoParaMedia13($id_clt,$meses_trabalhados=null,$data_demi=null) {
        /////////////////////
        // MOVIMENTOS FIXOS //
        ///////////////////
        
        $explodeDataDemi = explode("-", $data_demi);
        $anoDemi = $explodeDataDemi[0];
                         
        /*TODA HORA MUDAM ISSO, OU É PELA QNT DE MESES OU FIXO POR 12*/
        $fator_divisivel = 12;
        //$fator_divisivel = $meses_trabalhados;
        
               
//        if($_COOKIE['logado'] == 179){
        if($_COOKIE['logado'] == 349){
            echo "<br>**************************DATA DEMI DENTRO DE getMovimentosFixoParaMedia13*********************************<br>";
            echo "<pre>";
                print_r($data_demi);
                print_r("SELECT  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                    FROM rh_folha as A
                    INNER JOIN rh_folha_proc as B
                    ON A. id_folha = B.id_folha
                    WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2 
                    AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) AND A.ano = '{$anoDemi}' ORDER BY A.ano,A.mes");
            echo "</pre>";
        }
        
        //PEGANDO AS FOLHAS Q A PESSOA PARTICIPOU
        $qr_folha = mysql_query("SELECT  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                    FROM rh_folha as A
                    INNER JOIN rh_folha_proc as B
                    ON A. id_folha = B.id_folha
                    WHERE B.id_clt   = '$id_clt'  AND B.status = 3 AND A.terceiro = 2 
                    AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) AND A.ano = '{$anoDemi}' ORDER BY A.ano,A.mes");
        $ids_mov_estati = array();
        while ($row_folha = mysql_fetch_assoc($qr_folha)) {
            if (!empty($row_folha['ids_movimentos_estatisticas'])) {
                $ids_mov_estati[] = $row_folha['ids_movimentos_estatisticas'];
            }
            $mesesFolha[(int)$row_folha['mes']] = (int)$row_folha['mes'];
        }
        
        $paramet = '';
        if(!empty($ids_mov_estati)){
            $paramet = ' id_movimento IN('.implode(",",$ids_mov_estati).') OR ';
        }
        
        //A PEDIDO DA REJANE, COM BASE NO EMAIL ESTOU REMOVENDO O MOVIMENTO DE DIFERENÇA SALARIAL PARA O CALCULO DAS MÉDIAS #13/11/2014
        //A PEDIDO DO ITALO, REMOVENDO DIF SALARIAL DAS MEDIAS #06/07/2016
        //A PEDIDO DA ALANA, REMOVENDO COMPLEMENTO DE SALARIO DAS MEDIAS #27/06/2016
        //A PEDIDO DO ITALO, REMOVENDO DIF SALARIAL DAS MEDIAS #06/09/2016
        //A PEDIDO DO ITALO, REEMBOLSO #21/10/2016
        
        $arr_mov_remover_das_medias = array(90043,90017,5012,90028,80017,6006,90086,90036,90038,90016,90024);
        /*
         * leonardo
         * em 30/01/2017
         * questão de movimentos com insidencia na rescisao que não fazem parte da média. removendo da média movimentos para mes 16 por orientacao da Michele 
         * comentando query antiga
         */
//        $sql_movi = "SELECT *
//                        FROM rh_movimentos_clt
//                        WHERE ( {$paramet} (mes_mov = 16 AND status = 1) OR (lancamento = 2 AND ano_mov = '{$anoDataDemi}')) AND incidencia = '5020,5021,5023'  
//                        AND tipo_movimento = 'CREDITO' AND id_clt = {$id_clt} AND cod_movimento NOT IN (". implode(",", $arr_mov_remover_das_medias) .")";
        $implode = implode(",", $arr_mov_remover_das_medias);
        
        $sql_movi = "SELECT *
                        FROM rh_movimentos_clt
                        WHERE ( {$paramet} (lancamento = 2 AND ano_mov = '{$anoDataDemi}')) AND incidencia = '5020,5021,5023'  
                        AND tipo_movimento = 'CREDITO' AND id_clt = {$id_clt} AND cod_movimento NOT IN ($implode)";
        $qr_movimentos = mysql_query($sql_movi); 
        
//        if ($_COOKIE['debug'] == 666) {
//            echo "query que quero ver: $sql_movi";
//        }
        
        while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
            if ($_COOKIE['debug'] == 666) {
                echo "QUERY MOVIMENTOS MEDIA : " . $sql_movi . "<br>";
                echo "<BR>-------------MOVIMENTOS MÉDIA--------------<br><pre> ";
                echo "<BR>Qeury MOV: {$sql_movi}<BR><pre> ";
                print_r($row_mov);
                echo "</pre>";
            }

            $movimentos[$row_mov['nome_movimento']] +=$row_mov['valor_movimento'];
            $listMovi[] = $row_mov;
            //BY RAMON (FEAT) SINESIO
            //ADICIONANDO REGRA PARA DUPLICAR MOVIMENTO QND FOR LANÇAMENTO DO TIPO SEMPRE
            if($row_mov['lancamento'] == 2){
                $mesLancamento2[(int)$row_mov['mes_mov']] = (int)$row_mov['mes_mov'];
                $listMoviSempre[(int)$row_mov['mes_mov']] = $row_mov;
            }
        }
        
        
        //ADICIONANDO REGRA PARA DUPLICAR MOVIMENTO QND FOR LANÇAMENTO DO TIPO SEMPRE
        //RODANDO CADA FOLHA
        $mesInicioMovSempre = 99;                               //INICIANDO VARIAVEL COM MES Q NÃO EXISTE, POIS A VALIDAÇÃO É SE O MES É MAIOR
        foreach($mesesFolha as $mes){
            if(in_array($mes,$mesLancamento2)){                 //VERIFICA SE TEM MOVIMENTO SEMPRE NA FOLHA Q ESTÁ RODANDO
                $mesInicioMovSempre = $mes;
            }else{
                //CASO O MES DA FOLHA Q ESTA RODANDO AGORA NÃO TENHA O MOVIMENTO LANÇADO SEMPRE
                //PRECISO VERIFICAR SE O MES ANTERIOR TEM O MOVIMENTO SEMPRE
                if($mes > $mesInicioMovSempre){
                    $novoMes = $mes;                              //ADICIONA O MOVIMENTO PARA O MES QUE ESTÁ RODANDO AGORA
                    if(!in_array($novoMes, $mesesFolha)){           //VERIFICA SE EXISTE O MES SEGUINTE 
                        break;
                    }
                    $dadosMovimentoSempre = $listMoviSempre[$mesInicioMovSempre];
                    $dadosMovimentoSempre['mes_mov'] = $novoMes;
                    $listMovi[] = $dadosMovimentoSempre;
                    $movimentos[$row_mov['nome_movimento']] +=$dadosMovimentoSempre['valor_movimento'];
                }
            }
        }
        
        if (in_array($_COOKIE['logado'], $this->programadores)) {
            echo "<pre>";
                print_r($listMovi);
            echo "</pre>";
        }
        
        if($id_clt == 2778){
            unset($listMovi[14]);
        }
        
        if($id_clt == 2340){
            unset($listMovi[18]);
        }
        
        /*if (in_array($_COOKIE['logado'], $this->programadores)) {
            echo "<br>-------------MOVIMENTOS MÉDIA (SEMPRE)--------------<br><pre>";
            print_r($mesesFolha);
            print_r($mesLancamento2);
            print_r($listMoviSempre);
            echo "</pre><br>-------------MOVIMENTOS MÉDIA (SEMPRE)--------------<br>";
        }*/
        
        //--------------- MOVIMENTOS FUTUROS ---------------
        //By Ramon 05/07/2016
        //Fiz, a pedido do Sabino, mais algumas horas dps Alana falou q não tem q entrar nas médias
        if(!empty($data_demi)){
            /*list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $data_demi);
            $array_movimentos_futuros = array(90043);
            $sql_get_movimentos = "SELECT A.*, IF(A.lancamento = 1, 'LANÇADO', '') AS tipo_lancamento
                                                    FROM rh_movimentos_clt AS A
                                                    WHERE 
                                                    A.ano_mov >= {$ano_demissao} 
                                                    AND A.mes_mov >= {$mes_demissao} 
                                                    AND A.`status` = 1 
                                                    AND A.cod_movimento IN (".implode(',',$array_movimentos_futuros).")
                                                    AND A.id_mov NOT IN(56)
                                                    AND A.id_clt = '{$id_clt}'

                                                    ORDER BY A.nome_movimento";
            $result_mov_futu = mysql_query($sql_get_movimentos);
            
            if (in_array($_COOKIE['logado'], $this->programadores)) {
                echo "<br>SQL:{$sql_get_movimentos}<br>";
            }
            
            while($row_mov_futuro = mysql_fetch_assoc($result_mov_futu)){
                if (in_array($_COOKIE['logado'], $this->programadores)) {
                    echo "<BR>-------------MOVIMENTOS MÉDIA (FUTURO)--------------<br><pre> ";
                    //echo "<BR>Qeury MOV: {$sql_movi}<BR><pre> ";
                    print_r($row_mov_futuro);
                    echo "</pre>";
                }

                $movimentos[$row_mov_futuro['nome_movimento']] +=$row_mov_futuro['valor_movimento'];
                $listMovi[] = $row_mov_futuro;
            }*/
        }
        
        if (in_array($_COOKIE['logado'], $this->programadores)) {
            echo "<pre>";
            print_r("Meses para Médiass: " . $meses_trabalhados . "<br>");
            echo "</pre>";
        }

        if (sizeof($movimentos) > 0) {
            $total_rendi = (array_sum($movimentos) / $fator_divisivel);
            $total_rendi = number_format($total_rendi, 2, '.', '');
        } else {
            $total_rendi = 0;
        }
        
        $dados['total_rendi']   = $total_rendi;
        $dados['total_movi']    = array_sum($movimentos);
        $dados['movimentos']    = $listMovi;
        $dados['fator_divi']    = $fator_divisivel;
        

        return $dados;
    }

}
