<?php



if (!empty($decimo_terceiro)) {
    
    $objMovimento->carregaMovimentos(2016);
// Parcela do DÃ©cimo Terceiro
    /*
     * 21/10/2016
     */
    switch ($tipo_terceiro) {
        case 1:
            $mes_dt = '13';
            $parcela = 1;
            break;
        case 2:
            $mes_dt = '14';
             $parcela = 2;
            break;
        case 3:
            $mes_dt = '15';
             $parcela = 3;
            break;
    }

// Calculando DÃ©cimo Terceiro


    include('eventos_dt.php');
    $Calc->dt_data($tipo_terceiro, $row_clt['data_entrada'], $ano, $mes, $salario_limpo, $clt, $meses_evento);
        
    $meses = ($Calc->meses_trab) - $meses_evento;
    
    if($row_clt['data_entrada'] > '2016-11-15' && $parcela = 1){
        $meses = 0;
        
    }       
        
    /**
     * CASO DE HORISTA
     * 17/11/2016
     */
//    if($row_clt['id_curso'] == 6580 || $row_clt['id_curso'] == 6894){
//        /***
//        * GRAVANDO AS MÉDIAS
//        */
//       $movMedias = $objCalcFolha->getMediaMovimentos($clt, $mes, $ano, $meses,1, $parcela); 
//       //$decimo_terceiro_credito = $movMedias['total_media_13'];
//    }else{
        /**
         * VALOR DO DÉCIMO TERCEIRO SEM RENDIMENTOS
         */
        $decimo_terceiro_credito = ($objCalcFolha->getValorDecimoTerceiro($salario_limpo,$meses,$tipo_terceiro)); ///$parcela
      
//    } 
    
     if(isset($tipo_terceiro) && !empty($tipo_terceiro) && $tipo_terceiro != ""){
        
        if($tipo_terceiro == 1){
            $decimo_terceiro_credito_final = $decimo_terceiro_credito/2;
             
        }

        if($_COOKIE['logado'] == 179){
            //echo "DECIMO TERCEIRO CREDITO: " . $decimo_terceiro_credito_final . "<br />";
        }
    }
    
    
    if($tipo_terceiro != 1){
         
         $valor_parcela_anterior = @mysql_result(mysql_query("SELECT (B.a5029 + B.rend) FROM rh_folha as A
            INNER JOIN rh_folha_proc as B
            ON A.id_folha = B.id_folha
            WHERE B.id_clt = '$clt' AND A.ano = '$ano' 
            AND tipo_terceiro = 1 AND A.status = 3 AND B.status = 3; "),0,0);
     
        /**GRAVA NA TABELA DE MOVIMENTOS_CLT UM MOVIMENTO DE ADIANTAMENTO DE 13°
         * PARA SEGUNDA PARCELA E INTEGRAL
         */  
//        $objMovimento->setIdClt($clt); 
//        $objMovimento->setMes($mes_dt);
//        $objMovimento->setAno($ano);
//        $objMovimento->setIdRegiao($regiao);
//        $objMovimento->setIdProjeto($projeto);   
//        $objMovimento->setIdMov(292);
//        $objMovimento->setCodMov(80030);               
//        $objMovimento->setLancadoPelaFolha(1);
//        $verifica = $objMovimento->verificaInsereAtualizaFolha($valor_parcela_anterior);
        
        
        
        /***
         * GRAVANDO AS MÉDIAS
         */
        $movMedias = $objCalcFolha->getMediaMovimentos($clt, $mes, $ano, $meses,1, $parcela); 
        
        $queryVeficaMedia = "SELECT * FROM rh_movimentos_clt AS A 
                             WHERE A.id_clt = '{$clt}' AND 
                             A.id_mov = '324' AND A.status = 1";
        $sqlQueryVerificaMedia = mysql_query($queryVeficaMedia) or die("Erro ao verificaar medias");              
        
        $queryMedias = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,
                            mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,
                            nome_movimento,data_movimento,user_cad,valor_movimento,
                            percent_movimento,lancamento,incidencia,tipo_qnt,qnt, 
                            status, status_reg, lancado_folha ) 
                            
                            VALUES 
                              
                            ('{$clt}','{$regiao}','{$projeto}',
                            '{$mes_dt}','{$ano}','324','80045',
                            'CREDITO','MÉDIA SOBRE 13°',NOW(),
                            '{$_COOKIE[logado]}','{$movMedias['total_media_13']}','',
                            '1','5020,5021,5023','','',1, 1,1)";
        
        if(mysql_num_rows($sqlQueryVerificaMedia) == 0){
            mysql_query($queryMedias);
        }else{
            while($rowsMedia = mysql_fetch_assoc($sqlQueryVerificaMedia)){
                if(($rowsMedia['valor_movimento'] != $movMedias['total_media_13']) OR ($rowsMedia['incidencia'] != "5020,5021,5023")){
                    $queryRemoveMedia = mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_clt = '{$clt}' AND id_mov = '324' AND status = 1");
                    mysql_query($queryMedias);
                }
            }            
        }
                             
        
        //INSALUBRIDADE 
        
        $insalu = 0;
        
        //ESSAS FUNÇÕES PAGAM A INSALUBRIDADE 40% SOBRE O SALÁRIO BASE. 
        //DIFERENTE DAS OUTRAS QUE PAGAM SOBRE SALÁRIO MÍNIMO 
        if($row_curso['nome'] == "SUPERVISOR DE APLICAÇÃO TECNICA RADIOLOGICA" || $row_curso['nome'] == "SUPERVISOR DE APLICAÇÃO TÉCNICA RADIOLÓGICA" || $row_curso['nome'] == "TÉCNICO DE RAIO-X"){
            $curso_especiais[] = $row_curso['id_curso'];
        }

        $insalSobreSalBase = 0;
        if(in_array($row_curso['id_curso'], $curso_especiais)){
            $insalSobreSalBase = 1;
        }
        
        if ($row_clt['insalubridade'] == 1 and $row_curso['tipo_insalubridade'] != 0 and $meses != 0 and ($tipo_terceiro == 2 OR $tipo_terceiro == 3)) {
           $insalubridade      = $objCalcFolha->getInsalubridade($dias,$tipo_insalubr,$qnt_salInsalu,$ano,$meses,$insalSobreSalBase,$salario_limpo);
           $valorInsalubridade = $insalubridade['valor_13_integral']; 
           $objMovimento->setIdClt($clt); 
           $objMovimento->setMes($mes_dt);
           $objMovimento->setAno($ano);
           $objMovimento->setIdRegiao($regiao);
           $objMovimento->setIdProjeto($projeto);   
           $objMovimento->setIdMov($insalubridade['id_mov']);
           $objMovimento->setTipoQuantidade(2);
           $objMovimento->setQuantidade($dias);
           $objMovimento->setCodMov($insalubridade['cod_mov']);               
           $objMovimento->setLancadoPelaFolha(1);
           $verifica = $objMovimento->verificaInsereAtualizaFolha($valorInsalubridade);      

        } else {

           //CONDIÇÂO PARA REMOVER A INSALUBRUIDADE CASO SEJ DESMARCAR NO CADASTRO DE CLT
           $qr_verifica_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                        WHERE id_clt = '$clt' 
                                                        AND id_mov IN (56,235,200) AND lancamento = 1 AND ano_mov = '$ano'
                                                        AND mes_mov = '$mes_dt' AND status = 1 ");
           $row_insalubridade = mysql_fetch_assoc($qr_verifica_insalubridade);
           $verifica_insalubridade = mysql_num_rows($qr_verifica_insalubridade);
           if ($verifica_insalubridade != 0) {
               mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
           }           
        }

        //PERICULOSIDADE
       $pericu = 0; //$row_curso['periculosidade_30']
       if ( $row_curso['periculosidade_30'] == 1 and $meses != 0 and ($tipo_terceiro == 2 OR $tipo_terceiro == 3)) {
           
           $periculosidade = $objCalcFolha->getPericulosidade($salario_limpo, $dias, $meses);
           $objMovimento->setIdClt($clt); 
           $objMovimento->setMes($mes_dt);
           $objMovimento->setAno($ano);
           $objMovimento->setIdRegiao($regiao);
           $objMovimento->setIdProjeto($projeto);   
           $objMovimento->setTipoQuantidade(2);
           $objMovimento->setQuantidade($dias);
           $objMovimento->setIdMov($periculosidade['id_mov']);
           $objMovimento->setCodMov($periculosidade['cod_mov']);
           $objMovimento->setLancadoPelaFolha(1);               
           $verifica = $objMovimento->verificaInsereAtualizaFolha($periculosidade['valor_integral']);  


       } else {
           //VERIFICA SE EXISTE OU NÃO O MOVIMENTO DE INSALUBRIDADE E ADICIONA CASO NÃO TENHA
           $qr_veri_periculosidade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                       WHERE id_clt = '$clt' 
                                                                       AND id_mov IN (57) AND lancamento = 1 AND ano_mov = '$ano'
                                                                       AND mes_mov = '$mes_dt' AND status = 1 ");
           $row_insalubridade = mysql_fetch_assoc($qr_veri_periculosidade);
           $verifica_perculosidade = mysql_num_rows($qr_veri_periculosidade);
           if ($verifica_perculosidade != 0) {
               mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
           }
       }
       ///FIM PERICULOSIDADE 
    }
    
    ///////////////////////////////////////////
    //////MOVMENTOS DE DÉCIMO TERCEIRO/////////
    ///////////////////////////////////////////
    $qr_movimentos_dt = mysql_query("SELECT * FROM rh_movimentos_clt
                                        WHERE id_clt = '$clt'
                                        AND status = '1'
                                        AND mes_mov = '$mes_dt'
                                        AND ano_mov = '$ano'");
    while ($row_movimento_dt = mysql_fetch_array($qr_movimentos_dt)) {

        // Criando Array para Update em Movimentos
        $ids_movimentos_update_geral[] = $row_movimento_dt['id_movimento'];
        $ids_movimentos_estatisticas[] = $row_movimento_dt['id_movimento'];
        $ids_movimentos_update_individual[] = $row_movimento_dt['id_movimento'];

        // Acrescenta os Movimentos de CrÃ©dito nos Rendimentos de DT
        if ($row_movimento_dt['tipo_movimento'] == 'CREDITO') {
            $movimentos_rendimentos += $row_movimento_dt['valor_movimento'];

            // Acrescenta os Movimentos de DÃ©bito nos Descontos de DT
        } elseif ($row_movimento_dt['tipo_movimento'] == 'DEBITO') {
            $movimentos_descontos += $row_movimento_dt['valor_movimento'];
        }

        // Acrescenta os Movimentos nas Bases de INSS e IRRF
        $incidencias = explode(',', $row_movimento_dt['incidencia']);

        foreach ($incidencias as $incidencia) {

            if ($incidencia == 5020) { // INSS
                if ($row_movimento_dt['tipo_movimento'] == 'CREDITO') {
                    $base_inss += $row_movimento_dt['valor_movimento'];
                } elseif ($row_movimento_dt['tipo_movimento'] == 'DEBITO') {
                    $base_inss -= $row_movimento_dt['valor_movimento'];
                }
            }

            if ($incidencia == 5021) { // IRRF
                if ($row_movimento_dt['tipo_movimento'] == 'CREDITO') {
                    $base_irrf += $row_movimento_dt['valor_movimento'];
                } elseif ($row_movimento_dt['tipo_movimento'] == 'DEBITO') {
                    $base_irrf -= $row_movimento_dt['valor_movimento'];
                }
            }
        }
        
    } // Fim dos Movimentos
        
    // Se for Licensa sem Vencimentos
    /* CONDIÇÂO PARA 13º    
        $qr_maternidade = mysql_query("SELECT nome, mes, ano, (SUM(a6005)/12) as total_maternidade FROM rh_folha_proc WHERE id_clt = $clt AND status = 3"); 
        $row_maternidade = mysql_fetch_assoc($qr_maternidade);
        $salario_maternidade = $row_maternidade['total_maternidade'] /$parcela;

             $movimentos_rendimentos += $salario_maternidade;
             $base_inss  += $salario_maternidade;
             $base_irrf  += $salario_maternidade;
             $base_fgts  += $salario_maternidade;
        //    echo $clt.' - '.$salario_maternidade.'<br>';
    */

    $movimento_parcela_anterior = @mysql_result(mysql_query("SELECT rend FROM rh_folha as A
                                    INNER JOIN rh_folha_proc as B
                                    ON A.id_folha = B.id_folha
                                    WHERE B.id_clt = '$clt' AND A.ano = '$ano' 
                                    AND tipo_terceiro = 1 AND A.status = 3 AND B.status = 3; "),0,0);
    
    // FGTS sobre DT
    $base_fgts = $base_inss + $decimo_terceiro_credito;
    $fgts = $base_fgts * 0.08;

    if ($tipo_terceiro != 1) {

            if ($tipo_terceiro == 2) {
                $base_inss += ($decimo_terceiro_credito) + $movimento_parcela_anterior;
            } else {
                $base_inss += $decimo_terceiro_credito ;
            }

        $Calc->MostraINSS($base_inss, $data_inicio);
        $inss_dt = $Calc->valor;
        $percentual_inss = (int) substr($Calc->percentual, 2);
        $faixa_inss = $Calc->percentual;
        $teto_inss = $Calc->teto;
       
        /* DESCONTO INSS OUTRA EMPRESA */
        if ($base_inss != 0) {
            if ($row_clt['desconto_inss'] == '1') {
                if ($row_clt['tipo_desconto_inss'] == 'isento') {
                    $inss_dt = 0;
                } elseif ($row_clt['tipo_desconto_inss'] == 'parcial') {

                    if (($row_clt['desconto_outra_empresa'] + $inss_dt) > $teto_inss) {
                        $inss_dt = $teto_inss - $row_clt['desconto_outra_empresa'];
                    }
                }
            }
        }
                
                
      // IRRF sobre DT
        if ($tipo_terceiro == 2) {
            $base_irrf = (($decimo_terceiro_credito + $base_irrf)) - $inss_dt;
           
            
            
        } else {
            $base_irrf = ($decimo_terceiro_credito + $base_irrf)- $inss_dt;
        }

        $Calc->MostraIRRF($base_irrf, $clt, $projeto, $data_inicio);
        $irrf_dt = $Calc->valor;
        $percentual_irrf = (int) substr($Calc->percentual, 2);
        $faixa_irrf = $Calc->percentual;
        $fixo_irrf = $Calc->valor_fixo_ir;
        $ddir = $Calc->valor_deducao_ir_total;
        $filhos_irrf = $Calc->total_filhos_menor_21;

    }

    // VariÃ¡veis para Linha do Participante
    $inss_completo = $inss_dt;
    $irrf_completo = $irrf_dt;
    
    //A PRIMEIRA PARCELA DO 13º NÃO HAVERA NENHUM RENDIMENTO
    if($tipo_terceiro == 1)
        $rendimentos = 0;
    else    
        $rendimentos = $movimentos_rendimentos;
    
    
    $descontos = $movimentos_descontos;
    
    /**
    * MOVIMENTOS DE ADIANTAMENTO
    * DE 13º SALARIO NAS FÉRIAS
    */
    $verificaAdiantamentoEmFerias = "SELECT *
                                    FROM rh_movimentos_clt AS A
                                    WHERE A.cod_movimento IN(80030,5027) AND A.id_projeto = $projeto AND 
                                                    A.id_clt = '{$clt}' AND A.mes_mov = 17 AND A.status = 1";
    $sqlVerificaAdiantamentoEmFerias = mysql_query($verificaAdiantamentoEmFerias);
    $valorAdiantamento = 0;
    while($rowsAdiantamento = mysql_fetch_assoc($sqlVerificaAdiantamentoEmFerias)){
        $valorAdiantamento += $rowsAdiantamento['valor_movimento'];
    }
    
    /**
     * 21102016
     */
    if($parcela == 1){
        
        $liquido = $decimo_terceiro_credito / 2;
        $decimo_terceiro_credito_final = 0;

        /**
         * ZERANDO VALOR DA BASE
         * 13º SALARIO
         */
        if($valorAdiantamento > 0){
            $liquido = 0;
            $decimo_terceiro_credito_final = 0;
        
            $rendimentos = $valorAdiantamento;
            /**
             * LANÇAR MOVIMENTOS DE CREDITO
             * NA FOLHA DE 13º
             */
            $objMovimento->setIdClt($clt); 
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto); 
            $objMovimento->setIdMov(292);
            $objMovimento->setCodMov(80030);
            $objMovimento->setLancadoPelaFolha(1);               
            $verifica = $objMovimento->verificaInsereAtualizaFolha($valorAdiantamento); 

            /**
             * LANÇAR MOVIMENTO DE DÉBITO NO VALOR
             * DO ADIANTAMENTO NA FOLHA DE 13º
             */
            $objMovimento->setIdClt($clt); 
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto); 
            $objMovimento->setIdMov(451);
            $objMovimento->setCodMov(80031);
            $objMovimento->setLancadoPelaFolha(1);               
            $verifica = $objMovimento->verificaInsereAtualizaFolha($valorAdiantamento); 
            
        }else{
            $objMovimento->removeMovimento($clt,451);    
            $objMovimento->removeMovimento($clt,292);    
        }
        
    }else if($parcela == 2){
        
        $queryPrimeiraParcela  = "SELECT A.salliquido 
                                    FROM rh_folha_proc AS A
                                            LEFT JOIN rh_folha  AS B ON(A.id_folha = B.id_folha)
                                    WHERE A.id_clt = '{$clt}' AND B.tipo_terceiro = 1";
        $sqlPrimeiraParcela = mysql_query($queryPrimeiraParcela) or die("erro");    
        $valorPrimeiroParceiro = 0;
            while($rowsPrimeiraParc = mysql_fetch_assoc($sqlPrimeiraParcela)){
                $valorPrimeiroParceiro = $rowsPrimeiraParc['salliquido'];
            }
        
            /**
             * LANÇAR MOVIMENTO DE DÉBITO NO VALOR
             * DO ADIANTAMENTO NA FOLHA DE 13º
             */
            if($valorPrimeiroParceiro > 0){
                $objMovimento->setIdClt($clt); 
                $objMovimento->setMes($mes_dt);
                $objMovimento->setAno($ano);
                $objMovimento->setIdRegiao($regiao);
                $objMovimento->setIdProjeto($projeto); 
                $objMovimento->setIdMov(452);
                $objMovimento->setCodMov(5050);
                $objMovimento->setLancadoPelaFolha(1);               
                $verifica = $objMovimento->verificaInsereAtualizaFolha($valorPrimeiroParceiro); 
            }
            
            /**
             * LANÇAR MOVIMENTO DE DÉBITO NO VALOR
             * DO ADIANTAMENTO NA FOLHA DE 13º
             */
            if($valorAdiantamento > 0){
                $objMovimento->setIdClt($clt); 
                $objMovimento->setMes($mes_dt);
                $objMovimento->setAno($ano);
                $objMovimento->setIdRegiao($regiao);
                $objMovimento->setIdProjeto($projeto); 
                $objMovimento->setIdMov(451);
                $objMovimento->setCodMov(80031);
                $objMovimento->setLancadoPelaFolha(1);               
                $verifica = $objMovimento->verificaInsereAtualizaFolha($valorAdiantamento); 
            }
            
        $liquido = ($decimo_terceiro_credito + $rendimentos - $descontos - $inss_completo - $irrf_completo);
        $decimo_terceiro_credito_final = $decimo_terceiro_credito;
    }else{
        $liquido = $decimo_terceiro_credito + $rendimentos - $descontos - $valorAdiantamento - $inss_completo - $irrf_completo;
    }
        
    //echo $decimo_terceiro_credito;
    
    // Mais VariÃ¡veis para Update do Participante
        $base = $decimo_terceiro_credito;
        $fgts_completo = $fgts;

    // Mais VariÃ¡veis para Estatistica do Participante
        $valor_mes = $salario_limpo / 12;
        $valor_proporcional = round($valor_mes * $meses, 2);
}

unset($valor_integral_movimento);

?>