<?php

/**
 * ADICIONANDO PENSAO ALIMENTICIA - 16/12/2016 - SINESIO LUIZ
 */
if (!empty($decimo_terceiro)) {



    $objMovimento->carregaMovimentos(2016);
// Parcela do Décimo Terceiro
    /*
     * 21/10/2016
     */
//    echo $tipo_terceiro;
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

// Calculando Décimo Terceiro


    include('eventos_dt.php');
    $Calc->dt_data($tipo_terceiro, $row_clt['data_entrada'], $ano, $mes, $salario_limpo, $clt, $meses_evento);

    $meses = ($Calc->meses_trab) - $meses_evento;

    /**
     * CASO DE HORISTA
     * 17/11/2016
     */
//    if($row_clt['id_curso'] == 6580 || $row_clt['id_curso'] == 6894){
//        /***
//        * GRAVANDO AS M�DIAS
//        */
//       $movMedias = $objCalcFolha->getMediaMovimentos($clt, $mes, $ano, $meses,1, $parcela); 
//       //$decimo_terceiro_credito = $movMedias['total_media_13'];
//    }else{media
    /**
     * VALOR DO D�CIMO TERCEIRO SEM RENDIMENTOS
     */
    $decimo_terceiro_credito = ($objCalcFolha->getValorDecimoTerceiro($salario_limpo, $meses, $tipo_terceiro)); ///$parcela
//    } 

    if (isset($tipo_terceiro) && !empty($tipo_terceiro) && $tipo_terceiro != "") {

        if ($tipo_terceiro == 1) {
            $decimo_terceiro_credito_final = $decimo_terceiro_credito / 2;
        }

        if ($_COOKIE['debug'] == 666) {
            //echo "DECIMO TERCEIRO CREDITO: " . $decimo_terceiro_credito_final . "<br />";
        }
    }


    if ($tipo_terceiro != 1) {
        $valor_parcela_anterior = @mysql_result(mysql_query("SELECT (B.a5029 + B.rend) FROM rh_folha as A
            INNER JOIN rh_folha_proc as B
            ON A.id_folha = B.id_folha
            WHERE B.id_clt = '$clt' AND A.ano = '$ano' 
            AND tipo_terceiro = 1 AND A.status = 3 AND B.status = 3; "), 0, 0);

        /*         * GRAVA NA TABELA DE MOVIMENTOS_CLT UM MOVIMENTO DE ADIANTAMENTO DE 13�
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



        /*         * *
         * GRAVANDO AS M�DIAS
         */
        $sqlVerificaRescisaoIndireta = "SELECT status FROM rh_clt WHERE id_clt = '{$clt}'";
        $queryVerificaRescisaoIndireta = mysql_query($sqlVerificaRescisaoIndireta);
        $resultVerificaRescisaoIndireta = mysql_result($queryVerificaRescisaoIndireta, 0);


//        if($_COOKIE['logado'] == 179){
        $queryVeficaMedia = "SELECT * FROM rh_movimentos_clt AS A 
                             WHERE A.id_clt = '{$clt}' AND 
                             A.id_mov = '466' AND A.status = 1 AND A.mes_mov = '$mes_dt'";

        $sqlQueryVerificaMedia = mysql_query($queryVeficaMedia) or die("Erro ao verificaar medias");
////        if ($_COOKIE['logado'] == 345 || $_COOKIE['logado'] == 179) {


        if ($_COOKIE['logado'] == 179) {
            echo '/////////////////////VARI�VEIS DE M�DIAS/////////////////////';
            echo '<pre>';
            print_r([$clt, $mes, $ano, $meses, 1, $parcela]);
            echo '</pre>';
        }

        $gratificacao_funcao = $objCalcFolha->getGratificacaoFuncao($clt);
        if ($gratificacao_funcao['valor_integral'] > 0) {

            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov($gratificacao_funcao['id_mov']);
            $objMovimento->setCodMov($gratificacao_funcao['cod_mov']);
            $objMovimento->setLancadoPelaFolha(1);

            $valor_gratificacao_funcao = ($gratificacao_funcao['valor_integral'] / 12) * $meses;
            $objMovimento->verificaInsereAtualizaFolha($valor_gratificacao_funcao);
        } else {

            $objMovimento->removeMovimento($clt, $folha, [$gratificacao_funcao['id_mov']]);
        }

        $quebra_caixa = $objCalcFolha->getQuebraCaixa($clt);
        if ($quebra_caixa['valor_integral'] > 0) {

            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov($quebra_caixa['id_mov']);
            $objMovimento->setCodMov($quebra_caixa['cod_mov']);
            $objMovimento->setLancadoPelaFolha(1);

            $valor_quebra_caixa = ($quebra_caixa['valor_integral'] / 12) * $meses;
            $objMovimento->verificaInsereAtualizaFolha($valor_quebra_caixa);
        } else {

            $objMovimento->removeMovimento($clt, $folha, [$quebra_caixa['id_mov']]);
        }

        $ajuda_custo = $objCalcFolha->getAjudaCusto($clt);
        if ($ajuda_custo['valor_integral'] > 0) {

            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov($ajuda_custo['id_mov']);
            $objMovimento->setCodMov($ajuda_custo['cod_mov']);
            $objMovimento->setLancadoPelaFolha(1);

            $valor_ajuda_custo = ($ajuda_custo['valor_integral'] / 12) * $meses;
            $objMovimento->verificaInsereAtualizaFolha($valor_ajuda_custo);
        } else {

            $objMovimento->removeMovimento($clt, $folha, [$ajuda_custo['id_mov']]);
        }

        $ad_tempo_servico = $objCalcFolha->getAdTempoServico($clt);
        if ($ad_tempo_servico['valor_integral'] > 0) {
            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setPorcentagem($ad_tempo_servico['porcentagem']);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov($ad_tempo_servico['id_mov']);
            $objMovimento->setCodMov($ad_tempo_servico['cod_mov']);
            $objMovimento->setLancadoPelaFolha(1);

            $valor_ad_tempo_servico = ($ad_tempo_servico['valor_integral'] / 12) * $meses;
            $objMovimento->verificaInsereAtualizaFolha($valor_ad_tempo_servico);
        } else {
            $objMovimento->removeMovimento($clt, $folha, [$ad_tempo_servico['id_mov']]);
        }

        $ad_cargo_confianca = $objCalcFolha->getAdCargoConfianca($clt);
        if ($ad_cargo_confianca['valor_integral'] > 0) {
            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setPorcentagem($ad_cargo_confianca['porcentagem']);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov($ad_cargo_confianca['id_mov']);
            $objMovimento->setCodMov($ad_cargo_confianca['cod_mov']);
            $objMovimento->setLancadoPelaFolha(1);

            $valor_ad_cargo_confianca = ($ad_cargo_confianca['valor_integral'] / 12) * $meses;
            $objMovimento->verificaInsereAtualizaFolha($valor_ad_cargo_confianca);
        } else {
            $objMovimento->removeMovimento($clt, $folha, [$ad_cargo_confianca['id_mov']]);
        }

        $ad_transferencia = $objCalcFolha->getAdTransferencia($clt);
        if ($ad_transferencia['valor_integral'] > 0) {
            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setPorcentagem($ad_transferencia['porcentagem']);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov($ad_transferencia['id_mov']);
            $objMovimento->setCodMov($ad_transferencia['cod_mov']);
            $objMovimento->setLancadoPelaFolha(1);

            $valor_ad_transferencia = ($ad_transferencia['valor_integral'] / 12) * $meses;
            $objMovimento->verificaInsereAtualizaFolha($valor_ad_transferencia);

            unset($ad_transferencia['ids'][$ad_transferencia['id_mov']]);
            $objMovimento->removeMovimento($clt, $folha, $ad_transferencia['ids']);
        } else {

            $objMovimento->removeMovimento($clt, $folha, $ad_transferencia['ids']);
        }

        $risco_vida = $objCalcFolha->getRiscoVida($clt);
        if ($risco_vida['valor_integral'] > 0) {
            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setPorcentagem($risco_vida['porcentagem']);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov($risco_vida['id_mov']);
            $objMovimento->setCodMov($risco_vida['cod_mov']);
            $objMovimento->setLancadoPelaFolha(1);

            $valor_risco_vida = ($risco_vida['valor_integral'] / 12) * $meses;
            $objMovimento->verificaInsereAtualizaFolha($valor_risco_vida);
        } else {
            $objMovimento->removeMovimento($clt, $folha, [$risco_vida['id_mov']]);
        }

//        if ($row_clt['id_curso'] != 6580 && $row_clt['id_curso'] != 6894) {
        //exit("aqui");
        if ($resultVerificaRescisaoIndireta != '67' && $resultVerificaRescisaoIndireta != '68') {
            $movMedias = $objCalcFolha->getMediaMovimentos($clt, $mes, $ano, $meses, 1, $parcela);

//                if ($_COOKIE['logado'] == 179) {
//                    echo '/////////////////////M�DIAS/////////////////////';
//                    echo '<pre>';
//                    print_r($movMedias);
//                    echo '</pre>';
//                }

            if ($movMedias['total_media_13'] > 0) {

                $objMovimento->setIdClt($clt);
                $objMovimento->setIdFolha($folha);
                $objMovimento->setMes($mes_dt);
                $objMovimento->setAno($ano);
                $objMovimento->setIdRegiao($regiao);
                $objMovimento->setIdProjeto($projeto);
                $objMovimento->setIdMov(324);
                $objMovimento->setCodMov(80045);
                $objMovimento->setLancadoPelaFolha(1);
                $objMovimento->verificaInsereAtualizaFolha($movMedias['total_media_13']);

//                $queryMedias = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,
//                                mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,
//                                nome_movimento,data_movimento,user_cad,valor_movimento,
//                                percent_movimento,lancamento,incidencia,tipo_qnt,qnt, 
//                                status, status_reg, lancado_folha )
//                                
//                                VALUES 
//                                
//                                ('{$clt}','{$regiao}','{$projeto}',
//                                '{$mes_dt}','{$ano}','324','80045',
//                                'CREDITO','M�DIA SOBRE 13�',NOW(),
//                                '{$_COOKIE[logado]}','{$movMedias['total_media_13']}','',
//                                '1','5020,5021,5023','','',1, 1,1)";
//                mysql_query($queryMedias);
            } else {

                $objMovimento->removeMovimento($clt, $folha, [324]);
            }
        }
//        }
        

        //INSALUBRIDADE 
        /**
         * LUCAS
         * ALINHANDO A INSALUBRIDADE NO DT. (15/12/2016)
         */
        $insalu = 0;
        $flagSindicato = $objCalcFolha->getAdNoturnoEmSindicato($clt);

        //ESSAS FUN��ES PAGAM A INSALUBRIDADE 40% SOBRE O SAL�RIO BASE. 
        //DIFERENTE DAS OUTRAS QUE PAGAM SOBRE SAL�RIO M�NIMO 
        if ($row_curso['nome'] == "SUPERVISOR DE APLICA��O TECNICA RADIOLOGICA" || $row_curso['nome'] == "SUPERVISOR DE APLICA��O T�CNICA RADIOL�GICA" || $row_curso['nome'] == "T�CNICO DE RAIO-X") {
            $curso_especiais[] = $row_curso['id_curso'];
        }

        $insalSobreSalBase = 0;

        if (in_array($row_curso['id_curso'], $curso_especiais)) {
            $insalSobreSalBase = 1;
        }

        if ($_COOKIE['debug'] == 666) {
            echo '//////////////////////////////SINDICATO////////////////////////////////';
            echo '<pre>';
            print_r($flagSindicato);
            echo '</pre>';
        }

        if (($flagSindicato['insalubridade'] == 1 || $row_curso['id_curso'] == 4063) && ($tipo_terceiro == 2 || $tipo_terceiro == 3)) {
            $tipo_insalubr = 1;
            if ($qnt_salInsalu == 0 || is_null($qnt_salInsalu)) {
                $qnt_salInsalu = 1;
            }
            if ($_COOKIE['debug'] == 666) {
                echo '//////////////////////////////VARI�VEIS DE INSALUBRIDADE////////////////////////////////';
                echo '<pre>';
                echo $dias . '<br>';
                echo $tipo_insalubr . '<br>';
                echo $qnt_salInsalu . '<br>';
                echo $ano . '<br>';
                echo $meses . '<br>';
                echo $insalSobreSalBase . '<br>';
                echo $salario_limpo . '<br>';
                echo '</pre>';
            }
            $insalubridade = $objCalcFolha->getInsalubridade($dias, $tipo_insalubr, $qnt_salInsalu, $ano, $meses, $insalSobreSalBase, $salario_limpo);

            if ($_COOKIE['debug'] == 666) {
                echo '//////////////////////////////INSALUBRIDADE////////////////////////////////';
                echo '<pre>';
                print_r($insalubridade);
                echo '</pre>';
            }

            if ($resultVerificaRescisaoIndireta != '67' && $resultVerificaRescisaoIndireta != '68') {

                if ($_COOKIE['debug'] == 666) {
                    echo '//////////////////////////////INSALUBRIDADE////////////////////////////////';
                    echo '<pre>';
                    print_r($insalubridade['cod_mov']);
                    echo '</pre>';
                }
                $valorInsalubridade = $insalubridade['valor_13_integral'];
                $objMovimento->setIdClt($clt);
                $objMovimento->setMes($mes_dt);
                $objMovimento->setAno($ano);
                $objMovimento->setIdRegiao($regiao);
                $objMovimento->setIdProjeto($projeto);
                $objMovimento->setIdMov($insalubridade['id_mov']);
                $objMovimento->setTipoQuantidade(0);
                $objMovimento->setQuantidade($dias);
                $objMovimento->setCodMov($insalubridade['cod_mov']);
                $objMovimento->setLancadoPelaFolha(1);
                $verifica = $objMovimento->verificaInsereAtualizaFolha($valorInsalubridade);
            } else {

                $sqlRemoveResponsabilidade = "UPDATE rh_movimentos_clt SET status = 0 WHERE id_clt = '{$clt}' AND id_mov = '{$insalubridade['id_mov']}' AND mes_mov = '$mes_dt' AND status = 1";
                $queryRemoveResponsabilidade = mysql_query($sqlRemoveResponsabilidade);
            }
        } else {

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
        if ($row_curso['periculosidade_30'] == 1 and $meses != 0 and ( $tipo_terceiro == 2 OR $tipo_terceiro == 3)) {

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
            //VERIFICA SE EXISTE OU N�O O MOVIMENTO DE INSALUBRIDADE E ADICIONA CASO N�O TENHA
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
    //////MOVMENTOS DE D�CIMO TERCEIRO/////////
    ///////////////////////////////////////////

    $qr_movimentos_dt = mysql_query("SELECT * FROM rh_movimentos_clt
                                        WHERE id_clt = '$clt'
                                        AND status = '1'
                                        AND mes_mov = '$mes_dt'
                                        AND ano_mov = '$ano' AND valor_movimento != '0,00' AND status > 0");
//    if ($_COOKIE['logado'] == 179) {
//        echo '//////////////////////////////////////ARR DE MOVIMENTOS//////////////////////////';
//        echo '<pre>';
//        print_r("SELECT * FROM rh_movimentos_clt
//                    WHERE id_clt = '$clt'
//                    AND status = '1'
//                    AND mes_mov = '$mes_dt'
//                    AND ano_mov = '$ano' AND id_mov != '465' ");
//        echo '</pre>';
//    }

    while ($row_movimento_dt = mysql_fetch_array($qr_movimentos_dt)) {

        if ($_COOKIE['logado'] == 179) {
            echo "<pre>";
            print_r($row_movimento_dt);
            echo "</pre>";
        }

        // Criando Array para Update em Movimentos
        $ids_movimentos_update_geral[] = $row_movimento_dt['id_movimento'];
        $ids_movimentos_estatisticas[] = $row_movimento_dt['id_movimento'];
        $ids_movimentos_update_individual[] = $row_movimento_dt['id_movimento'];

        // Acrescenta os Movimentos de Crédito nos Rendimentos de DT
        if ($row_movimento_dt['tipo_movimento'] == 'CREDITO') {
            $movimentos_rendimentos += $row_movimento_dt['valor_movimento'];

            // Acrescenta os Movimentos de Débito nos Descontos de DT
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
    /* CONDI��O PARA 13�    
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
                                    AND tipo_terceiro = 1 AND A.status = 3 AND B.status = 3; "), 0, 0);

    // FGTS sobre DT

    $base_fgts = $base_inss + $decimo_terceiro_credito;

    $fgts = $base_fgts * 0.08;

    if ($tipo_terceiro != 1) {

        if ($tipo_terceiro == 2) {
            $base_inss += ($decimo_terceiro_credito) + $movimento_parcela_anterior;
        } else {
            $base_inss += $decimo_terceiro_credito;
        }


        $Calc->MostraINSS($base_inss, $data_inicio);
        $inss_dt = $Calc->valor;
        $percentual_inss = (int) substr($Calc->percentual, 2);
        $faixa_inss = $Calc->percentual;
        $teto_inss = $Calc->teto;

        if ($_COOKIE['logado'] == 179) {
            echo "<pre>";
            echo "Base inss:::::::~ <br>";
            print_r($base_inss);
            echo "<br>";
            print_r($inss_dt);
            echo "</pre>";
        }

        /**
         * DESCONTO INSS OUTRA EMPRESA
         */
//                if ($base_inss != 0) {
//            if ($row_clt['desconto_inss'] == '1') {
//                if ($row_clt['tipo_desconto_inss'] == 'isento') {
//                    $inss_dt = 0;
//                } elseif ($row_clt['tipo_desconto_inss'] == 'parcial') {
//
//                    if (($row_clt['desconto_outra_empresa'] + $inss_dt) > $teto_inss) {
//                        $inss_dt = $teto_inss - $row_clt['desconto_outra_empresa'];
//                    }
//                }
//            }
//        }


        $vInssParcial = 0;
        $legendaDescontaOutraEmpresa = "";
        if ($base_inss != 0) {
            if ($row_clt['desconto_inss'] == '1') {
//            if ($row_clt['tipo_desconto_inss'] == 'isento') {
//                $inss = 0;
//            } elseif ($row_clt['tipo_desconto_inss'] == 'parcial') {

                /**
                 * CRIANDO VERIFICA��O 
                 * DE DESCONTO OUTRA EMPRESA
                 */
                $verificaINSSOutraEmpresa = "SELECT *
                        FROM rh_inss_outras_empresas AS A
                        WHERE A.id_clt = '{$clt}' AND '{$data_inicio}' BETWEEN A.inicio AND A.fim;";
                $qslVerInssOutraEmpresa = mysql_query($verificaINSSOutraEmpresa);
                if (mysql_num_rows($qslVerInssOutraEmpresa) > 0) {
                    $rows_clt = mysql_fetch_assoc($qslVerInssOutraEmpresa);

                    if ($_COOKIE['logado'] == 179) {
                        echo "<br>**********************INSS OUTRA EMPRESA************************<br>";
                        echo "VALOR INSS: " . $inss . "<br>";
                        echo "DESCONTO OUTRA EMPRESA: " . $rows_clt['desconto'] . "<br>";
                        echo "TETO: " . $teto_inss . "<br>";
                        echo "TIPO DESCONTO: " . $row_clt['tipo_desconto_inss'] . "<br>";
                        echo "<br>**************************************************<br>";
                    }
                    $legendaDescontaOutraEmpresa = "(DESCONTA {$rows_clt['desconto']} EM OUTRA EMPRESA)";
                    if ($_COOKIE['logado'] == 179) {
                        echo "INSS:::: " . $inss_dt . "<br>";
                        echo $rows_clt['desconto'];
                    }
                    if (($rows_clt['desconto'] + $inss_dt) > $teto_inss) {
//                        if($_COOKIE['logado'] == 179){
//                            echo "INSS: " . $inss . "<br>";
//                        }
                        $inss_dt = $teto_inss - $rows_clt['desconto'];
                        $vInssParcial = $rows_clt['desconto'];
//                        if ($_COOKIE['logado'] == 179) {
//                            echo "DESCONTO OUTRA EMPRESA: " . $rows_clt['desconto'] . "<br>";
//                            echo "NOVO INSS: " . $inss . "<br>";
//                        }
                        if ($inss_dt < 0) {
                            $inss_dt = 0;
                        }
                    } else {
                        //exit("aquiiii");
                        $inss_dt = $inss_dt;
                    }
                }
//                if (($row_clt['desconto_outra_empresa'] + $inss) > $teto_inss) {
//                    $inss = $teto_inss - $row_clt['desconto_outra_empresa'];
//                    
//                    if($inss < 0){
//                        $inss = 0;
//                    }
//                }
//            }
            }
        }

        // IRRF sobre DT
        if ($tipo_terceiro == 2) {
            $base_irrf = (($decimo_terceiro_credito + $base_irrf)) - $inss_dt;
        } else {
            $base_irrf = ($decimo_terceiro_credito + $base_irrf) - $inss_dt;
        }

        $Calc->MostraIRRF($base_irrf, $clt, $projeto, $data_inicio);
        $irrf_dt = $Calc->valor;
        $percentual_irrf = $Calc->percentual * 100;
        $faixa_irrf = $Calc->percentual;
        $fixo_irrf = $Calc->valor_fixo_ir;
        $ddir = $Calc->valor_deducao_ir_total;
        $filhos_irrf = $Calc->total_filhos_menor_21;
    }

    // Variáveis para Linha do Participante
    $inss_completo = $inss_dt;
    $irrf_completo = $irrf_dt;

    //A PRIMEIRA PARCELA DO 13� N�O HAVERA NENHUM RENDIMENTO
    if ($tipo_terceiro == 1)
        $rendimentos = 0;
    else
        $rendimentos = $movimentos_rendimentos;


    $descontos = $movimentos_descontos;

    /**
     * MOVIMENTOS DE ADIANTAMENTO
     * DE 13� SALARIO NAS F�RIAS
     */
    $verificaAdiantamentoEmFerias = "SELECT *
                                    FROM rh_movimentos_clt AS A
                                    WHERE A.cod_movimento IN(80030,5027) AND A.id_projeto = $projeto AND 
                                                    A.id_clt = '{$clt}' AND A.mes_mov = 17 AND A.status = 1";
    $sqlVerificaAdiantamentoEmFerias = mysql_query($verificaAdiantamentoEmFerias);
    $valorAdiantamento = 0;
    while ($rowsAdiantamento = mysql_fetch_assoc($sqlVerificaAdiantamentoEmFerias)) {
        $valorAdiantamento += $rowsAdiantamento['valor_movimento'];
    }

    /**
     * 21102016
     */
    if ($parcela == 1) {

        $liquido = $decimo_terceiro_credito / 2;
        $decimo_terceiro_credito_final = 0;

        /**
         * ZERANDO VALOR DA BASE
         * 13� SALARIO
         */
        if ($valorAdiantamento > 0) {
            $liquido = 0;
            $decimo_terceiro_credito_final = 0;

            $rendimentos = $valorAdiantamento;
            /**
             * LAN�AR MOVIMENTOS DE CREDITO
             * NA FOLHA DE 13�
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
             * LAN�AR MOVIMENTO DE D�BITO NO VALOR
             * DO ADIANTAMENTO NA FOLHA DE 13�
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
        } else {
            $objMovimento->removeMovimento($clt, 451);
            $objMovimento->removeMovimento($clt, 292);
        }
    } else if ($parcela == 2) {

        $queryPrimeiraParcela = "SELECT A.salliquido 
                                    FROM rh_folha_proc AS A
                                            LEFT JOIN rh_folha  AS B ON(A.id_folha = B.id_folha)
                                    WHERE A.id_clt = '{$clt}' AND B.tipo_terceiro = 1";
        $sqlPrimeiraParcela = mysql_query($queryPrimeiraParcela) or die("erro");
        $valorPrimeiroParceiro = 0;
        while ($rowsPrimeiraParc = mysql_fetch_assoc($sqlPrimeiraParcela)) {
            $valorPrimeiroParceiro = $rowsPrimeiraParc['salliquido'];
        }

        /**
         * LAN�AR MOVIMENTO DE D�BITO NO VALOR
         * DO ADIANTAMENTO NA FOLHA DE 13�
         */
        if ($valorPrimeiroParceiro > 0) {
            $objMovimento->setIdClt($clt);
            $objMovimento->setMes($mes_dt);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov(725);
            $objMovimento->setCodMov(80362);
            $objMovimento->setLancadoPelaFolha(1);
            $verifica = $objMovimento->verificaInsereAtualizaFolha($valorPrimeiroParceiro);
        }

        /**
         * LAN�AR MOVIMENTO DE D�BITO NO VALOR
         * DO ADIANTAMENTO NA FOLHA DE 13�
         */
        if ($valorAdiantamento > 0) {
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
    } else {
        $liquido = $decimo_terceiro_credito + $rendimentos - $descontos - $valorAdiantamento - $inss_completo - $irrf_completo;
    }

    /**
     * VERIFICANDO SE EXISTE RESCISAO PARA A PESSOA
     * E ZERANDO O LIQUIDO DA FOLHA DE 13
     */
    $verificaDecimoTerceiroEmRescisao = "SELECT * FROM rh_recisao AS A WHERE A.id_clt = '{$clt}' AND A.`status` = 1";
    $sqlDecimoTerceiroEmRescisao = mysql_query($verificaDecimoTerceiroEmRescisao);
    if (mysql_num_rows($sqlDecimoTerceiroEmRescisao) > 0) {
        $liquido = 0;
    }

    if ($tipo_terceiro == 2) {
        if ($resultVerificaRescisaoIndireta == 67 || $resultVerificaRescisaoIndireta == 68) {
            $liquido = 0;
            $rendimentos = 0;
            $descontos = 0;
            $inss_completo = 0;
            $irrf_completo = 0;
            unset($movimentos_rendimentos);
            unset($movimentos_descontos);
        }
    }

    /**
     * FEITO EM: 16/12/2016,
     * SINESIO LUIZ
     */
    if (!empty($row_clt['pensao_alimenticia']) && $row_clt['pensao_alimenticia'] > 0) {



        /**
         * VERIFICANDO FAVORECIDOS
         */
        $queryVerificaPensao = "SELECT * FROM favorecido_pensao_assoc AS A WHERE A.id_clt = '{$clt}'";
        $sqlVerificaPensao = mysql_query($queryVerificaPensao) or die("Erro ao selecionar favorecidos de pens�o");

        $arrayFavorecidos = array();
        if (mysql_num_rows($sqlVerificaPensao) > 0) {
            while ($linhaVerificaPensao = mysql_fetch_assoc($sqlVerificaPensao)) {
                $arrayFavorecidos[] = array(
                    "cpf" => $linhaVerificaPensao['cpf'],
                    "favorecido" => $linhaVerificaPensao['favorecido'],
                    "aliquota" => $linhaVerificaPensao['aliquota'],
                    "sobreSalLiquido" => $linhaVerificaPensao['sobreSalLiquido'],
                    "sobreSalBruto" => $linhaVerificaPensao['sobreSalBruto'],
                    "sobreSalMinimo" => $linhaVerificaPensao['sobreSalMinimo'],
                    "umTercoSobreLiquido" => $linhaVerificaPensao['umTercoSobreLiquido'],
                    "quantSalMinimo" => $linhaVerificaPensao['quantSalMinimo'],
                    "valorfixo" => $linhaVerificaPensao['valorfixo'],
                    "incide_13" => $linhaVerificaPensao['incide_13'],
                );
            }
        }

        if ($_COOKIE['logado'] == 179) {
            echo "<pre>";
            print_r($arrayFavorecidos);
            echo "</pre>";
        }

        $msgErrorPensao = "";
        $arrayPensoes = array();

        for ($i = 0; $i < count($arrayFavorecidos); $i++) {

            if ($arrayFavorecidos[$i]['incide_13'] == 1) {

                $totalFlagsMarcadas = 0;

                if (!is_null($arrayFavorecidos[$i]['sobreSalLiquido']) ||
                        !is_null($arrayFavorecidos[$i]['sobreSalBruto']) ||
                        !is_null($arrayFavorecidos[$i]['sobreSalMinimo']) ||
                        !is_null($arrayFavorecidos[$i]['umTercoSobreLiquido'])) {

                    /**
                     * QUANDO A PENS�O FOR SOBRE O 
                     * SALARIO LIQUIDO
                     * ESSA � A OP��O POR PADR�O
                     */
                    if ($arrayFavorecidos[$i]['sobreSalLiquido'] == 1) {
                        $baseUsadaNaPensao = "Sobre Sal�rio L�quido";
                        $totalFlagsMarcadas++;
                        $baseParaPensao = $liquido - $total_vt_vr_va;
                    }

                    /**
                     * QUANDO A PENS�O FOR SOBRE O 
                     * SALARIO BRUTO
                     */
                    if ($arrayFavorecidos[$i]['sobreSalBruto'] == 1) {
                        $baseUsadaNaPensao = "Sobre Sal�rio Bruto";
                        $totalFlagsMarcadas++;
                        $baseParaPensao = $base_inss - $total_vt_vr_va;
                    }

                    /**
                     * QUANDO A PENS�O FOR SOBRE  
                     *  1/3 SOBRE O LIQUIDO
                     */
                    if ($arrayFavorecidos[$i]['umTercoSobreLiquido'] == 1) {
                        $baseUsadaNaPensao = " 1/3 SOBRE O LIQUIDO";
                        $totalFlagsMarcadas++;
                        $baseParaPensao = $liquido - $total_vt_vr_va;
                    }

                    /**
                     * QUANDO A PENS�O FOR SOBRE O 
                     * SALARIO MINIMO, SERA CHECADO TAMBEM 
                     * A QUANTIDADE DE SALARIO MINIMO
                     */
                    if ($arrayFavorecidos[$i]['sobreSalMinimo'] == 1) {
                        $totalFlagsMarcadas++;
                        $baseParaPensao = 880.00;

                        if (!is_null($arrayFavorecidos[$i]['quantSalMinimo'])) {
                            $baseParaPensao = $baseParaPensao * $arrayFavorecidos[$i]['quantSalMinimo'];
                            $baseUsadaNaPensao = "Sobre {$arrayFavorecidos[$i]['quantSalMinimo']} Salario(s) M�nimo(s)";
                        } else {
                            $baseParaPensao = 0;
                            $msgErrorPensao .= " <br> A Quantidade de Sal�rio Minimo esta Zerada <br> ";
                        }
                    }

                    /**
                     * CALCULO DA PENS�O
                     */
                    if ($arrayFavorecidos[$i]['umTercoSobreLiquido'] == 1) {
                        $valorParaPensao = $baseParaPensao / 3;
                        /**
                         * ARRAY DE PENS�ES
                         */
                        $nomeMov = "PENS�O ALIMENT�CIA 1/3 SOBRE O LIQUIDO ";
                    } else {
                        $valorParaPensao = $baseParaPensao * $arrayFavorecidos[$i]['aliquota'];
                        /**
                         * ARRAY DE PENS�ES
                         */
                        $nomeMov = "PENS�O ALIMENT�CIA " . ($arrayFavorecidos[$i]['aliquota'] * 100) . "% ";
                    }


                    /**
                     * VALOR FIXO
                     */
                    if ($arrayFavorecidos[$i]['valorfixo'] > 0) {
                        $valorParaPensao = $arrayFavorecidos[$i]['valorfixo'];
                        $nomeMov = "PENS�O ALIMENT�CIA: {$arrayFavorecidos[$i]['favorecido']}";
                    }

                    if ($InfoFerias['pensao_ferias'] > 0) {
                        $arrayPensoes = array();
                    } else {
                        $arrayPensoes[] = array(
                            "nome" => $nomeMov,
                            "base" => $baseParaPensao,
                            "valor" => $valorParaPensao,
                            "aliquota" => $arrayFavorecidos[$i]['aliquota'],
                            "valorfixo" => $arrayFavorecidos[$i]['valorfixo'],
                            "legenda" => $baseUsadaNaPensao
                        );
                    }
                } else {
                    $msgErrorPensao .= " <br> Selecione uma Base de Calculo para Pens�o no Formul�rio do CLT <br> ";
                    $valorParaPensao = $arrayFavorecidos[$i]['valorfixo'];
                }


                /**
                 * 
                 */
                if ($valorParaPensao > 0) {
                    $idMov = 0;
                    $codMov = 0;
                    if ($arrayFavorecidos[$i]['aliquota'] == 0.15) {
                        $idMov = 54;
                        $codMov = 6004;
                    } else if ($arrayFavorecidos[$i]['aliquota'] == 0.20) {
                        $idMov = 223;
                        $codMov = 50222;
                    } else if ($arrayFavorecidos[$i]['aliquota'] == 0.25) {
                        $idMov = 372;
                        $codMov = 90019;
                    } else if ($arrayFavorecidos[$i]['aliquota'] == 0.30) {
                        $idMov = 63;
                        $codMov = 7009;
                    } else if ($arrayFavorecidos[$i]['aliquota'] == 0.32) {
                        $idMov = 326;
                        $codMov = 7010;
                    } else if ($arrayFavorecidos[$i]['aliquota'] == 0.40) {
                        $idMov = 327;
                        $codMov = 7011;
                    } else {
                        $idMov = 363;
                        $codMov = 7012;
                    }
                }

                /**
                 * SALVANDO DADOS DE PENS�O PARA 
                 * MONSTRAR NO CONTRA-CHEQUE
                 */
                $cpf = str_replace('.', '', $arrayFavorecidos[$i]['cpf']);
                $cpf = str_replace('-', '', $cpf);

                $queryDelete = mysql_query("DELETE FROM itens_pensao_para_contracheque WHERE cpf_favorecido = '{$cpf}' AND id_folha = '{$row_folha['id_folha']}'");
                $insertLinhaPensao = mysql_query("INSERT INTO itens_pensao_para_contracheque (id_folha,cod_mov,nome_mov,percent,base,valor_mov,cpf_favorecido) VALUES 
                     ('{$row_folha['id_folha']}','{$codMov}','{$nomeMov}','{$arrayFavorecidos[$i]['aliquota']}','{$baseParaPensao}','{$valorParaPensao}','{$cpf}')");


                /**
                 * VERIFICA SE ESTA MARCADO MAIS 
                 * DE UMA OP��O NO CADASTRO DO CLT
                 */
                if ($totalFlagsMarcadas > 1) {
                    $msgErrorPensao .= " <br> Existem mais de uma Base para calculo de Pens�o Aliment�cia marcada para o CLT  <br> ";
                    $baseParaPensao = 0;
                    $valorParaPensao = 0;
                }
            }
        }
//                      
        $valorParaPensaoFinal = 0;

        foreach ($arrayPensoes as $key => $values) {
            $valorParaPensaoFinal += $values['valor'];
        }

        if ($_COOKIE['logado'] == 179) {
            echo "valor " . $valorParaPensaoFinal . "<br>";
        }

        $liquido = $liquido - $valorParaPensaoFinal;
//        $descontos += $valorParaPensaoFinal;          
    }


    //echo $decimo_terceiro_credito;
    // Mais Variáveis para Update do Participante
    $base = $decimo_terceiro_credito;
    $fgts_completo = $fgts;

    // Mais Variáveis para Estatistica do Participante
    $valor_mes = $salario_limpo / 12;
    $valor_proporcional = round($valor_mes * $meses, 2);

    $idFolha = $row_folha['id_folha'];
    $objCalcFolha->removeCltRescindido($clt, $idFolha);
}

unset($valor_integral_movimento);
?>