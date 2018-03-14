<?php

class FeriasProvisao{
    
    function getClts($regiao, $objCalcFolha) {
        if(!empty($_REQUEST['clt'])){
            $and = "WHERE A.id_clt = '{$_REQUEST['clt']}'";
        }else{
            $and = "WHERE A.id_regiao = '{$regiao}' AND B.tipo IS NULL";
        }
        
        $qry_clt = "SELECT A.id_clt, A.matricula, A.id_regiao, A.nome, A.data_entrada, C.id_curso, C.nome AS nome_curso, C.salario, C.qnt_salminimo_insalu, C.hora_mes, D.adNoturno, D.hr_noturna, D.prcentagem_add_noturno
            FROM rh_clt AS A
            LEFT JOIN rhstatus AS B ON(A.status = B.codigo)
            LEFT JOIN curso AS C ON(A.id_curso = C.id_curso)
            LEFT JOIN rhsindicato AS D ON(A.rh_sindicato = D.id_sindicato)
            {$and}
            ORDER BY A.nome";
        $sql_clt = mysql_query($qry_clt) or die(mysql_error());
        
        $total = 0;
        
        while($row_clt = mysql_fetch_assoc($sql_clt)){
            $clts[$row_clt['id_clt']] = $row_clt;
            $stringIds .= $row_clt['id_clt'].",";
            
            $salario = $row_clt['salario'];
            
            /**
             * ESSA VARIÁVEL VAI SER
             * PARAMETRO PARA VARIOS 
             * CALCULOS JÁ QUE NO 
             * IABAS SP TUDO É FLEGADO
             * NO SINDICATO 
             */
            $flagSindicato = $objCalcFolha->getAdNoturnoEmSindicato($row_clt['id_clt']);
            
//            print_array($flagSindicato);
            
            //DADOS DA FUNÇÃO
            $id_curso = $row_clt['id_curso'];
            $nome_curso = $row_clt['nome_curso'];
            $qnt_salInsalu = $row_clt['qnt_salminimo_insalu'];
            
            // INSALUBRIDADE
            if($qnt_salInsalu == 0 || is_null($qnt_salInsalu)){
                $qnt_salInsalu = 1;
            }
            
            /**
            * ESSAS FUNÇÕES PAGAM A INSALUBRIDADE 40% SOBRE O SALÁRIO BASE. 
            * DIFERENTE DAS OUTRAS QUE PAGAM SOBRE SALÁRIO MÍNIMO 
            */
            if($nome_curso == "SUPERVISOR DE APLICAÇÃO TECNICA RADIOLOGICA" || 
              $nome_curso == "SUPERVISOR DE APLICAÇÃO TÉCNICA RADIOLÓGICA" || 
              $nome_curso == "TÉCNICO DE RAIO-X"){
              $curso_especiais[] = $id_curso;
            }
            
            $insalSobreSalBase = 0;
            if(in_array($id_curso, $curso_especiais)){
               $insalSobreSalBase = 1;
            }
            
            $tipo_insalubr = 1;
            
            /*
             * 22/02/17
             * by: Max
             * SOLICITADO PELO TIAGO
             * OS SINDICATOS DE DENTISTA IRÃO PAGAR 40% DE INSALUBRIDADE
             */
            if(($flagSindicato['id_sind'] == 30) || ($flagSindicato['id_sind'] == 33)){
                $tipo_insalubr = 2;
            }
            
            if($flagSindicato['insalubridade'] == 1){
                $insalubridade = $objCalcFolha->getInsalubridade(30, $tipo_insalubr, $qnt_salInsalu, date('Y'), null, $insalSobreSalBase, $salario,$id_curso);                                
                $clts[$row_clt['id_clt']]['valor_insalubridade'] = $insalubridade['valor_integral'];
                $clts[$row_clt['id_clt']]['cod_insalubridade'] = $insalubridade['cod_mov'];
                
//                print_array($insalubridade);
            }else{
                $clts[$row_clt['id_clt']]['valor_insalubridade'] = 0;
            }   
            
            $total++;
        }
        
//        print_array($clts);
        
        $dados = array(
            "clts"      => $clts,
            "stringIds" => $stringIds,
            "total"     => $total
        );
        
        return $dados;
    }
    
    function getPeriodosAquisitivos($ids_clts, $dados_clts) {
        #6 - lista apenas os períodos não gozados e não vencidos
        #5 - lista apenas os períodos não gozados e vencidos e em dobro
        #4 - lista apenas os períodos não gozados e vencidos, mas não em dobro
        #3 - lista apenas os períodos não gozados e vencidos
        #2 - lista apenas os períodos não gozados
        #1 - lista apenas os períodos gozados
        #0 - lista todos os períodos
        
        $resource_periodo = mysql_query("CALL periodo_aquisitivo('{$ids_clts}', 2)") or die(mysql_error());
        while($row_periodo = mysql_fetch_assoc($resource_periodo)){
            $dadosPeriodo[$row_periodo['id_clt']]['periodos'][] = $row_periodo;
            $dadosPeriodo[$row_periodo['id_clt']]['dados'] = $dados_clts[$row_periodo['id_clt']];
        }
        
        return $dadosPeriodo;
    }
    
    function calcBase($dadosClt, $data_ini, $data_fim) {
//        print_array($dadosClt);
        
        //DADOS DO CLT
        $id_clt = $dadosClt['id_clt'];    
        
        //DATAS FORMATADAS
        $data_iniF = converteData($data_ini, "d/m/Y");
        $data_fimF = converteData($data_fim, "d/m/Y");
        
        $expDataI = explode('-', $data_ini);
        $mesI = $expDataI[1];
        $anoI = $expDataI[0];
        
        $expDataF = explode('-', $data_fim);
        $mesF = $expDataF[1];
        $anoF = $expDataF[0];
        $ultimo_dia_dataF = date("t", mktime(0,0,0,$mesF,'01',$anoF));
        
        $servidor = 'localhost';
        $usuario = 'f71iabas_intra';
        $senha = 'f71@iabasSP#2016_mySQL';
        $banco = 'f71iabas_intranet';
        mysql_connect($servidor, $usuario, $senha, true) or die(mysql_error());
        mysql_select_db($banco) or die(mysql_error());        
        
//        $qry = "SELECT A.id_clt, A.cod_movimento, A.mes_mov, A.ano_mov, A.valor_movimento, A.nome_movimento, A.qnt, A.tipo_qnt, A.qnt_horas, A.lancamento
//            FROM rh_movimentos_clt AS A
//            LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
//            WHERE A.id_clt = {$id_clt} AND A.`status` > 0 AND B.incidencia_inss = 1 AND A.tipo_movimento = 'CREDITO' 
//            AND B.descicao NOT LIKE '%13º%' AND B.descicao NOT LIKE '%1/3%' AND B.descicao NOT LIKE '%FERIAS%' AND B.descicao NOT LIKE '%INDENIZAÇÃO%' AND B.descicao NOT LIKE '%LEI %' AND B.descicao NOT LIKE '%MULTA %' AND B.descicao NOT LIKE '%REEMBOLSO%' AND B.descicao NOT LIKE '%SALÁRIO FAMÍLIA%'
//            AND IF(A.mes_mov > 12, A.data_movimento BETWEEN DATE('{$anoI}-{$mesI}-01') AND  DATE('{$anoF}-{$mesF}-31'), CONCAT(A.ano_mov, '-', A.mes_mov, '-01') BETWEEN DATE('{$anoI}-{$mesI}-01') AND DATE('{$anoF}-{$mesF}-31')) AND A.cod_movimento NOT IN(50451,80015,5045,90082,80030,5027,5080,50111,80019,80028,90016,90024,5913,40051,4005,5015,5029,80041,90046,50270,90064,90084,90085,90076,90077,80033,80043,90081,90030,5071,90058,4007,6005,6008,6009,0001,50258,50225,80103,8006,50221,70011,90038,9996,6006,50251) AND A.mes_mov NOT IN(16,17,18)
//            ORDER BY A.ano_mov, A.mes_mov";
        
        $qry = "
            SELECT *, SUM(valor_movimento) AS total_mov, SUM(valor_semqtd) AS valor_total_semqtd, SUM(mes_semqtd) AS meses_semqtd, SEC_TO_TIME(SUM(TIME_TO_SEC(tot_qnt_horas))) AS tot_horas, SUM(tot_qnt_segundos) AS tot_sec, IF(COUNT(*) > 12, 12, COUNT(*)) AS meses, ((SUM(tot_qnt_horas) / 3600) / COUNT(*)) media/*, SUM(tot_qnd_dias)  AS tot_dias*/ FROM (
                SELECT A.id_clt, A.cod_movimento, A.mes_mov, A.ano_mov, A.valor_movimento, A.nome_movimento, A.qnt, A.tipo_qnt, A.qnt_horas, A.lancamento, IF(B.id_movimento_calculo > 0, CONCAT('a', B.id_movimento_calculo), CONCAT('n', A.cod_movimento)) AS codd, B.id_movimento_calculo, B.percentual,
                
                SEC_TO_TIME(SUM(TIME_TO_SEC(
                IF(A.cod_movimento IN(9000,90101,5069,8005) AND A.tipo_qnt = 2, 
                        TIME(CONCAT(A.qnt * E.hr_noturna,':00:00')), 
                        A.qnt_horas
                )))) AS tot_qnt_horas,               
                
                SUM(TIME_TO_SEC(
                    IF(A.cod_movimento IN(9000,90101,5069,8005) AND A.tipo_qnt = 2, 
                        TIME(CONCAT(A.qnt * E.hr_noturna,':00:00')), 
                        A.qnt_horas
                    ))
                ) AS tot_qnt_segundos,
                
                IF((A.qnt = 0 OR A.qnt IS NULL OR A.qnt = '') AND A.qnt_horas = 0, SUM(A.valor_movimento), 0) AS valor_semqtd,	
                IF((A.qnt = 0 OR A.qnt IS NULL OR A.qnt = '') AND A.qnt_horas = 0, SUM(1), 0) AS mes_semqtd
                
                FROM rh_movimentos_clt AS A
                LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                INNER JOIN rh_movimentos_calculos AS C ON(B.id_movimento_calculo = C.id_movimento_calculo)
                LEFT JOIN rh_clt AS D ON(A.id_clt = D.id_clt)
                LEFT JOIN rhsindicato AS E ON(D.rh_sindicato = E.id_sindicato)
                WHERE A.id_clt = {$id_clt} AND A.`status` > 0 AND B.incidencia_inss = 1 AND A.tipo_movimento = 'CREDITO' 
                AND B.descicao NOT LIKE '%13º%' AND B.descicao NOT LIKE '%1/3%' AND B.descicao NOT LIKE '%FERIAS%' AND B.descicao NOT LIKE '%INDENIZAÇÃO%' AND B.descicao NOT LIKE '%LEI %' AND B.descicao NOT LIKE '%MULTA %' AND B.descicao NOT LIKE '%REEMBOLSO%' AND B.descicao NOT LIKE '%SALÁRIO FAMÍLIA%'
                AND IF(A.mes_mov > 12, A.data_movimento BETWEEN DATE('{$anoI}-{$mesI}-01') AND  DATE('{$anoF}-{$mesF}-{$ultimo_dia_dataF}'), CONCAT(A.ano_mov, '-', A.mes_mov, '-01') BETWEEN DATE('{$anoI}-{$mesI}-01') AND DATE('{$anoF}-{$mesF}-{$ultimo_dia_dataF}')) AND A.cod_movimento NOT IN(50451,80015,5045,90082,80030,5027,5080,50111,80019,80028,90016,90024,5913,40051,4005,5015,5029,80041,90046,50270,90064,90084,90085,90076,90077,80033,80043,90081,90030,5071,90058,4007,6005,6008,6009,0001,50258,50225,80103,8006,50221,70011,90038,9996,6006,50251) AND A.mes_mov NOT IN(16,17,18)
                GROUP BY A.cod_movimento, B.id_movimento_calculo, A.mes_mov, A.ano_mov
                ORDER BY A.ano_mov, A.mes_mov
            ) AS T
            GROUP BY codd
        ";
        
//        echo $qry."<br>";
        
        $sql = mysql_query($qry) or die(mysql_error());
        
        //CONSULTA TIPO DE INSALUBRIDADE
        $qry_mov_insal = mysql_query("SELECT A.descicao FROM rh_movimentos AS A WHERE A.cod = '{$dadosClt['cod_insalubridade']}'") or die(mysql_error());
        $res_mov_insal = mysql_result($qry_mov_insal,0);                
        
        $total_movimentacoes = 0;
        
        $total_movimentacoes += $dadosClt['valor_insalubridade'];
        
        while($res = mysql_fetch_assoc($sql)){
            $movimentacoes[$res['id_movimento_calculo']] = $res;
        }
        
//        echo "<br>### MOVIMENTAÇÕES: {$data_iniF} - {$data_fimF} ###";
//        print_array($movimentacoes);
        
//        exit();
        
        // HORA EXTRA 90%
        if($movimentacoes[1]['meses'] > 0){
            $dadosHE90 = array(
                "data_ini"      => $data_ini,
                "data_fim"      => $data_fim,
                "total_horas"   => $movimentacoes[1]['tot_horas'],
                "dados_clt"     => $dadosClt,
                "percentual"    => $movimentacoes[1]['percentual'],
                "avos_periodo"  => $movimentacoes[1]['meses']
            );
            $valorHE90 = FeriasProvisao::calcHoraExtra($dadosHE90, false);
            $total_movimentacoes += $valorHE90;
        }
        
        // HORA EXTRA 100%
        if($movimentacoes[2]['meses'] > 0){
            $dadosHE100 = array(
                "data_ini"      => $data_ini,
                "data_fim"      => $data_fim,
                "total_horas"   => $movimentacoes[2]['tot_horas'],
                "dados_clt"     => $dadosClt,
                "percentual"    => 1.0,
                "avos_periodo"  => $movimentacoes[2]['meses']
            );
            $valorHE100 = FeriasProvisao::calcHoraExtra($dadosHE100, false);
            $total_movimentacoes += $valorHE100;
        }
        
        // MÉDIA DE ADICIONAL NOTURNO
        if($movimentacoes[5]['meses'] > 0){
            $dadosMediaNoturno = array(
                "data_ini"               => $data_ini,
                "data_fim"               => $data_fim,
                "total_horas"            => $movimentacoes[5]['tot_horas'],
                "total_dias"             => $movimentacoes[5]['tot_dias'],
                "total_segundos"         => $movimentacoes[5]['tot_sec'],
                "dados_clt"              => $dadosClt,
                "avos_periodo"           => $movimentacoes[5]['meses']
            );
            $valorMediaNoturno = FeriasProvisao::calcMediaAdNoturno($dadosMediaNoturno, false);
            $total_movimentacoes += $valorMediaNoturno;
        }
        
        if($valorHE90 > 0){
            // MÉDIA DE ADICIONAL NOTURNO SOBRE HORA EXTRA 90%
            $dadosMediaNoturnoHE90 = array(
                "data_ini"               => $data_ini,
                "data_fim"               => $data_fim,
                "total_horas"            => $movimentacoes[4]['tot_horas'],
                "total_segundos"         => $movimentacoes[4]['tot_sec'],
                "dados_clt"              => $dadosClt,
                "avos_periodo"           => $movimentacoes[4]['meses'],
                "percentual_he"          => 0.9
            );
            $valorMediaNoturnoHE90 = FeriasProvisao::calcHoraExtraAdNoturno($dadosMediaNoturnoHE90, false);
            $total_movimentacoes += $valorMediaNoturnoHE90;
        }
        
        if($valorHE100 > 0){
            // MÉDIA DE ADICIONAL NOTURNO SOBRE HORA EXTRA 100%
            $dadosMediaNoturnoHE100 = array(
                "data_ini"               => $data_ini,
                "data_fim"               => $data_fim,
                "total_horas"            => $movimentacoes[4]['tot_horas'],
                "total_segundos"         => $movimentacoes[4]['tot_sec'],
                "dados_clt"              => $dadosClt,
                "avos_periodo"           => $movimentacoes[4]['meses'],
                "percentual_he"          => 1.0
            );
            $valorMediaNoturnoHE100 = FeriasProvisao::calcHoraExtraAdNoturno($dadosMediaNoturnoHE100, false);
            $total_movimentacoes += $valorMediaNoturnoHE100;
        }
        
        //GRATIFICAÇÕES
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[6]['lancamento'] == 2){
            $valorGratificacao = $movimentacoes[6]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>GRATIFICAÇÕES <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[6]['meses'] > 1){
                $dadosGratificacao = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[6]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[6]['meses']
                );

                $valorGratificacao = FeriasProvisao::calcMedia($dadosGratificacao, false, 'GRATIFICAÇÕES');
            }
            
            if($movimentacoes[6]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>GRATIFICAÇÕES <strong>< 1 MES</strong></br><br>";
                }
            }
        }

        $total_movimentacoes += $valorGratificacao;        
        
        //AJUDA DE CUSTO        
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[7]['lancamento'] == 2){
            $valorAjudaCusto = $movimentacoes[7]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>AJUDA DE CUSTO <strong>SEMPRE</strong></br><br>";
            }
        }else{
            if($movimentacoes[7]['meses'] > 1){
                //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
                $dadosAjudaCusto = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[7]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[7]['meses']
                );

                $valorAjudaCusto = FeriasProvisao::calcMedia($dadosAjudaCusto, false, 'AJUDA DE CUSTO');
            }
            
            if($movimentacoes[7]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>AJUDA DE CUSTO <strong>< 1 MES</strong></br><br>";
                }
            }
        }

        $total_movimentacoes += $valorAjudaCusto;        
        
        //VANTAGEM PESSOAL
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[8]['lancamento'] == 2){
            $valorVantagemPessoal = $movimentacoes[8]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>VANTAGEM PESSOAL <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[8]['meses'] > 1){
                $dadosVantagemPessoal = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[8]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[8]['meses']
                );
                
                $valorVantagemPessoal = FeriasProvisao::calcMedia($dadosVantagemPessoal, false, 'VANTAGEM PESSOAL');
            }
            
            if($movimentacoes[8]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>VANTAGEM PESSOAL <strong>< 1 MES</strong></br><br>";
                }
            }
        }
        
        $total_movimentacoes += $valorVantagemPessoal;
        
        //DSR SOBRE COMISSÕES
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[9]['lancamento'] == 2){
            $valorDsrComissoes = $movimentacoes[9]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>DSR SOBRE COMISSÕES <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[9]['meses'] > 1){
                $dadosDsrComissoes = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[9]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[9]['meses']
                );
                
                $valorDsrComissoes = FeriasProvisao::calcMedia($dadosDsrComissoes, false, 'DSR SOBRE COMISSÕES');
            }
            
            if($movimentacoes[9]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>DSR SOBRE COMISSÕES <strong>< 1 MES</strong></br><br>";
                }
            }
        }
        
        $total_movimentacoes += $valorDsrComissoes;   
        
        //DSR SOBRE SALARIO HORISTA
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[10]['lancamento'] == 2){
            $valorDsrSalHorista = $movimentacoes[10]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>DSR SOBRE SALARIO HORISTA <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[10]['meses'] > 1){
                $dadosDsrSalHorista = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[10]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[10]['meses']
                );
                
                $valorDsrSalHorista = FeriasProvisao::calcMedia($dadosDsrSalHorista, false, 'DSR SOBRE SALARIO HORISTA');
            }
            
            if($movimentacoes[10]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>DSR SOBRE SALARIO HORISTA <strong>< 1 MES</strong></br><br>";
                }
            }
        }
        
        $total_movimentacoes += $valorDsrSalHorista; 
        
        //DSR SOBRE HORA EXTRA
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[11]['lancamento'] == 2){
            $valorDsrHE = $movimentacoes[11]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>DSR SOBRE HORA EXTRA <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[11]['meses'] > 1){
                $dadosDsrHE = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[11]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[11]['meses']
                );
                
                $valorDsrHE = FeriasProvisao::calcMedia($dadosDsrHE, false, 'DSR SOBRE HORA EXTRA');
            }
            
            if($movimentacoes[11]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>DSR SOBRE HORA EXTRA <strong>< 1 MES</strong></br><br>";
                }
            }
        }
        
        $total_movimentacoes += $valorDsrHE;
        
        //DSR SOBRE ADICIONAL NOTURNO
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[12]['lancamento'] == 2){
            $valorDsrAdNoturno = $movimentacoes[12]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>DSR SOBRE HORA EXTRA <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[12]['meses'] > 1){
                $dadosDsrAdNoturno = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[12]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[12]['meses']
                );
                
                $valorDsrAdNoturno = FeriasProvisao::calcMedia($dadosDsrAdNoturno, false, 'DSR SOBRE ADICIONAL NOTURNO');
            }
            
            if($movimentacoes[12]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>DSR SOBRE ADICIONAL NOTURNO <strong>< 1 MES</strong></br><br>";
                }
            }
        }
        
        $total_movimentacoes += $valorDsrAdNoturno;
        
        //HORAS EXTRAS EM VALORES
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[13]['lancamento'] == 2){
            $valorHEEmValores = $movimentacoes[13]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>HORAS EXTRAS EM VALORES <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[13]['meses'] > 1){
                $dadosHEEmValores = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[13]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[13]['meses']
                );
                
                $valorHEEmValores = FeriasProvisao::calcMedia($dadosHEEmValores, false, 'HORAS EXTRAS EM VALORES');
            }
            
            if($movimentacoes[13]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>HORAS EXTRAS EM VALORES <strong>< 1 MES</strong></br><br>";
                }
            }
        }
        
        //HORA EXTRA 90% SÓ COM VALORES
        if($movimentacoes[1]['valor_total_semqtd'] > 0){
            $dadosHEEmValoresSemQtd90 = array(
                "data_ini"               => $data_ini,
                "data_fim"               => $data_fim,
                "valor_total"            => $movimentacoes[1]['valor_total_semqtd'],
                "dados_clt"              => $dadosClt,
                "avos_periodo"           => $movimentacoes[1]['meses_semqtd']
            );
            
            $valorHEEmValores += FeriasProvisao::calcMedia($dadosHEEmValoresSemQtd90, false, 'HORAS EXTRAS 90% EM VALORES');
        }
        
        //HORA EXTRA 100% SÓ COM VALORES
        if($movimentacoes[2]['valor_total_semqtd'] > 0){
            $dadosHEEmValoresSemQtd100 = array(
                "data_ini"               => $data_ini,
                "data_fim"               => $data_fim,
                "valor_total"            => $movimentacoes[2]['valor_total_semqtd'],
                "dados_clt"              => $dadosClt,
                "avos_periodo"           => $movimentacoes[2]['meses_semqtd']
            );
            
            $valorHEEmValores += FeriasProvisao::calcMedia($dadosHEEmValoresSemQtd100, false, 'HORAS EXTRAS 100% EM VALORES');
        }
        
        $total_movimentacoes += $valorHEEmValores;
        
        //ADICIONAL NOTURNO EM VALORES
        //QUANDO FOR LANÇAMENTO SEMPRE, PEGA O VALOR FIXO, SENÃO CALCULA A MÉDIA
        if($movimentacoes[14]['lancamento'] == 2){
            $valorANEmValores = $movimentacoes[14]['valor_movimento'];
            
            if($_COOKIE['debug'] == 666){
                echo "<br>ADICIONAL NOTURNO EM VALORES <strong>SEMPRE</strong></br><br>";
            }
        }else{
            //NÃO CALCULA A MÉDIA QUANDO TEM SOMENTE 1 MÊS
            if($movimentacoes[14]['meses'] > 1){
                $dadosANEmValores = array(
                    "data_ini"               => $data_ini,
                    "data_fim"               => $data_fim,
                    "valor_total"            => $movimentacoes[14]['total_mov'],
                    "dados_clt"              => $dadosClt,
                    "avos_periodo"           => $movimentacoes[14]['meses']
                );
                
                $valorANEmValores = FeriasProvisao::calcMedia($dadosANEmValores, false, 'ADICIONAL NOTURNO EM VALORES');
            }
            
            if($movimentacoes[14]['meses'] == 1){
                if($_COOKIE['debug'] == 666){
                    echo "<br>ADICIONAL NOTURNO EM VALORES <strong>< 1 MES</strong></br><br>";
                }
            }
        }
        
        //ADICIONAL NOTURNO SÓ COM VALORES
        if($movimentacoes[5]['valor_total_semqtd'] > 0){
            $dadosANEmValoresSemQtd = array(
                "data_ini"               => $data_ini,
                "data_fim"               => $data_fim,
                "valor_total"            => $movimentacoes[5]['valor_total_semqtd'],
                "dados_clt"              => $dadosClt,
                "avos_periodo"           => $movimentacoes[5]['meses_semqtd']
            );
            
            $valorANEmValores += FeriasProvisao::calcMedia($dadosANEmValoresSemQtd, false, 'ADICIONAL NOTURNO SÓ COM VALORES');
        }
        
        //AD. NOTURNO HORA EXTRA SÓ COM VALORES
        if($movimentacoes[4]['valor_total_semqtd'] > 0){
            $dadosANHoraExtraEmValoresSemQtd = array(
                "data_ini"               => $data_ini,
                "data_fim"               => $data_fim,
                "valor_total"            => $movimentacoes[4]['valor_total_semqtd'],
                "dados_clt"              => $dadosClt,
                "avos_periodo"           => $movimentacoes[4]['meses_semqtd']
            );
            
            $valorANEmValores += FeriasProvisao::calcMedia($dadosANHoraExtraEmValoresSemQtd, false, 'AD. NOTURNO HORA EXTRA SÓ COM VALORES');
        }
        
        $total_movimentacoes += $valorANEmValores;
        
        $lista_movimentacoes['movimentos'] = array(
            $res_mov_insal                                => $dadosClt['valor_insalubridade'],
            "MÉDIA DE HORA EXTRA 90%"                     => $valorHE90,
            "MÉDIA DE HORA EXTRA 100%"                    => $valorHE100,
            "MÉDIA DE AD NOTURNO"                         => $valorMediaNoturno,
            "MÉDIA DE HORA EXTRA 90% SOBRE O AD NOTURNO"  => $valorMediaNoturnoHE90,
            "MÉDIA DE HORA EXTRA 100% SOBRE O AD NOTURNO" => $valorMediaNoturnoHE100,
            "MÉDIA DE GRATIFICAÇÕES"                      => $valorGratificacao,
            "MÉDIA DE AJUDA DE CUSTO"                     => $valorAjudaCusto,
            "VANTAGEM PESSOAL"                            => $valorVantagemPessoal,
            "MÉDIA DE DSR SOBRE COMISSÕES"                => $valorDsrComissoes,
            "MÉDIA DE DSR SOBRE SALARIO HORISTA"          => $valorDsrSalHorista,
            "MÉDIA DE DSR SOBRE HORA EXTRA"               => $valorDsrHE,
            "MÉDIA DE DSR SOBRE ADICIONAL NOTURNO"        => $valorDsrAdNoturno,
            "MÉDIA DE HORAS EXTRAS EM VALORES"            => $valorHEEmValores,
            "MÉDIA DE ADICIONAL NOTURNO EM VALORES"       => $valorANEmValores
        );
        
        $lista_movimentacoes['valor_total'] = $total_movimentacoes;
        
//        print_array($lista_movimentacoes);
        
        return $lista_movimentacoes;
    }
    
    static function calcTotalHoras($array_horas) {
        //inicializa a variavel segundos com 0
        $segundos = 0;

        foreach ($array_horas as $tempo){ //percorre o array $tempo
            list($h, $m, $s) = explode(':', $tempo); //explode a variavel tempo e coloca as horas em $h, minutos em $m, e os segundos em $s

            //transforma todas os valores em segundos e add na variavel segundos 
            
            $segundos += $h * 3600;
            $segundos += $m * 60;
            $segundos += $s;
        }

        $horas = floor($segundos / 3600); //converte os segundos em horas e arredonda caso nescessario
        $segundos %= 3600; // pega o restante dos segundos subtraidos das horas
        $minutos = floor($segundos / 60);//converte os segundos em minutos e arredonda caso nescessario
        $segundos %= 60;// pega o restante dos segundos subtraidos dos minutos
        
        $total_horas = "{$horas}:{$minutos}";
        
        return $total_horas;
    }
        
    static function calcHoraExtra($dados, $debug = false) {
//        print_array($dados);
        
        $avos_he = $dados['avos_periodo'];                
        $salario = $dados['dados_clt']['salario'];
        $horas_mes = $dados['dados_clt']['hora_mes'];
        $percentual_he = $dados['percentual'];
        
        //JOGANDO HORAS PARA FLOAT PARA CALCULO
        $total_he = $dados['total_horas'];
        list($qnt_hora, $qnt_minuto) = explode(':', $total_he);
        $total_he_decimal = $qnt_hora + ($qnt_minuto / 60);
        
        //FORMULA
        $media_horas = $total_he_decimal / $avos_he;
        $salario_hora = $salario / $horas_mes;
        $valor_horaHE = $salario_hora * $percentual_he;
        $valor_mediaHE = ($salario_hora + $valor_horaHE) * $media_horas;
        
        if($debug){        
            if($percentual_he == '0.90'){
                $txtHe = "90%";
            }elseif($percentual_he == '1.0'){
                $txtHe = "100%";
            }

            echo "### MÉDIA DE HORA EXTRA {$txtHe} ###<br>";
            echo "PERIODO: {$dados['data_ini']} - {$dados['data_fim']}<br>";
            echo "TOTAL DE HORAS: {$total_he} ({$total_he_decimal})<br>";
            echo "AVOS DENTRO DO PERIODO: {$avos_he}<br>";
            echo "SALARIO: {$salario}<br>";
            echo "HORAS/MES: {$horas_mes}<br>";
            echo "PERCENTUAL HORAS/EXTRA: {$percentual_he}<br>";
            echo "<strong>MÉDIA DAS HORAS ($total_he_decimal / $avos_he):</strong> {$media_horas}<br>";
            echo "<strong>SALARIO/HORA ($salario / $horas_mes):</strong> {$salario_hora}<br>";
            echo "<strong>VALOR/HORA DA HE ($salario_hora * $percentual_he):</strong> {$valor_horaHE}<br>";
            echo "<strong>VALOR DA MÉDIA DE HE: (($salario_hora + $valor_horaHE) * $media_horas)</strong> {$valor_mediaHE}<br>";
            
            echo "<br><br>";
        }
        
        return $valor_mediaHE;
    }
    
    static function calcHoraExtraAdNoturno($dados, $debug = false) {
//        print_array($dados);
        
        $salario = $dados['dados_clt']['salario'];
        $horas_mes = $dados['dados_clt']['hora_mes'];
        $percentual_hn = $dados['dados_clt']['prcentagem_add_noturno'];
        $percentual_he = $dados['percentual_he'];
        $avos_hn = $dados['avos_periodo'];
        
        //JOGANDO HORAS PARA FLOAT PARA CALCULO
        $total_hn = $dados['total_horas'];
        list($qnt_hora, $qnt_minuto) = explode(':', $total_hn);
        $total_hn_decimal = $qnt_hora + ($qnt_minuto / 60);
        
        //FORMULA
        $valor_hora = $salario / $horas_mes;
        $valor_hora_noturna = $valor_hora * $percentual_hn;
        $valor_hora_extra_ad_noturno = ($valor_hora_noturna * $percentual_he) + $valor_hora_noturna;
//        $valor_hora_extra_ad_noturno = ($valor_hora_noturna * $percentual_he);
        $media_hora_extra_noturna = $total_hn_decimal / $avos_hn;
        $valor_final_media = $media_hora_extra_noturna * $valor_hora_extra_ad_noturno;
        
        if($debug){
            if($percentual_he == '0.9'){
                $txtHe = "90%";
            }elseif($percentual_he == 2){
                $txtHe = "100%";
            }
            
            echo "### MÉDIA DE AD NOTURNO SOBRE HORA EXTRA {$txtHe} ###<br>";
            echo "PERIODO: {$dados['data_ini']} - {$dados['data_fim']}<br>";                        
            echo "TOTAL DE HORAS: {$total_hn} ({$total_hn_decimal})<br>";
            echo "SALARIO: {$salario}<br>";
            echo "HORAS/MES: {$horas_mes}<br>";
            echo "PERCENTUAL HORAS NOTURNAS: {$percentual_hn}<br>";
            echo "PERCENTUAL HORAS/EXTRA: {$percentual_he}<br>";
            echo "AVOS DENTRO DO PERIODO: {$avos_hn}<br>";
            
            echo "<strong>VALOR DA HORA ($salario / $horas_mes):</strong> {$valor_hora}<br>";
            echo "<strong>VALOR DA HORA NOTURNA ($valor_hora * $percentual_hn):</strong> {$valor_hora_noturna}<br>";
            echo "<strong>VALOR DA HORA SOBRE AD NOTURNO (($valor_hora_noturna * $percentual_he) + $valor_hora_noturna):</strong> {$valor_hora_extra_ad_noturno}<br>";            
            echo "<strong>MÉDIA DE HORAS EXTRAS NOTURNAS ($total_hn_decimal / $avos_hn):</strong> {$media_hora_extra_noturna}<br>";
            echo "<strong>VALOR FINAL DA MEDIA DE HORAS EXTRAS NOTURNAS ($media_hora_extra_noturna * $valor_hora_extra_ad_noturno):</strong> {$valor_final_media}<br>";
            
            echo "<br><br>";
        }
        
        return $valor_final_media;
    }
    
    static function calcMediaAdNoturno($dados, $debug = false) {
//        print_array($dados);
        
        $total_segundos = $dados['total_segundos'];
        
        //CONVERTENDO SEGUNDOS EM HORAS
        $total = $total_segundos;
        $horas = floor($total / 3600);
        $minutos = floor(($total - ($horas * 3600)) / 60);
        $segundos = floor($total % 60);
        
        $qtd_horas = $horas . ":" . $minutos . ":" . $segundos;        
        $avos_periodo = $dados['avos_periodo'];
        $salario = $dados['dados_clt']['salario'];
        $horas_mes = $dados['dados_clt']['hora_mes'];
        $percentual_adnoturno = $dados['dados_clt']['prcentagem_add_noturno'];
        
        //JOGANDO HORAS PARA FLOAT PARA CALCULO
        list($qnt_hora, $qnt_minuto) = explode(':', $qtd_horas);
        $total_hn_decimal = $qnt_hora + ($qnt_minuto / 60);        
        
        //FORMULA
        $media_hora_noturna = $total_hn_decimal / $avos_periodo;
        $salario_hora = $salario / $horas_mes;
        $valor_hora_noturna = $salario_hora * $percentual_adnoturno;
        $media_adnoturno = $valor_hora_noturna * $media_hora_noturna;
        
        if($debug){
            echo "### MEDIA AD NOTURNO ###<br>";
            echo "PERIODO: {$dados['data_ini']} - {$dados['data_fim']}<br>";
            echo "TOTAL DE HORAS: {$qtd_horas} ({$total_hn_decimal})<br>";
            echo "AVOS DENTRO DO PERIODO: {$avos_periodo}<br>";
            echo "SALARIO: {$salario}<br>";
            echo "HORAS/MES: {$horas_mes}<br>";
            echo "PERCENTUAL AD NOTURNO: {$percentual_adnoturno}<br>";
            echo "<strong>MÉDIA DE HORA NOTURNA ($total_hn_decimal / $avos_periodo):</strong> {$media_hora_noturna}<br>";
            echo "<strong>SALÁRIO/HORA ($salario / $horas_mes):</strong> {$salario_hora}<br>";
            echo "<strong>VALOR DE HORA NOTURNA ($salario_hora * $percentual_adnoturno):</strong> {$valor_hora_noturna}<br>";
            echo "<strong>MEDIA AD NOTURNO ($valor_hora_noturna * $media_hora_noturna):</strong> {$media_adnoturno}<br>";
            
            echo "<br><br>";
        }
        
        return $media_adnoturno;
    }
    
    static function calcMedia($dados, $debug = false, $nome = null) {
//        print_array($dados);
        
        $valor_total = $dados['valor_total'];
        $qtd_meses = $dados['avos_periodo'];
        $valor = $valor_total / $qtd_meses;
        
        if($debug){
            echo "### MEDIA {$nome} ###<br>";
            echo "PERIODO: {$dados['data_ini']} - {$dados['data_fim']}<br>";
            echo "VALOR TOTAL: {$valor_total}<br>";
            echo "QTD DE MESES DENTRO DO PERIODO: {$qtd_meses}<br>";
            
            echo "<strong>VALOR DA MEDIA ($valor_total / $qtd_meses):</strong> {$valor}<br>";            
            
            echo "<br><br>";
        }
        
        return $valor;
    }
    
    function Calc_qnt_meses_13_ferias($dt_inicial, $dt_final, $id_clt = NULL, $dataEntrada, $dataDemissao) {
        $begin = (!empty($dt_inicial)) ? new DateTime($dt_inicial) : new DateTime($dataEntrada);
        $end = (!empty($dt_final)) ? new DateTime($dt_final) : new DateTime($dataDemissao);
        $end = $end->modify( '+1 day' ); 

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);

        $mes_atual = 0;
        $count = 1;
        $dias = 1;
        $m = 0;

        foreach($daterange as $date){
            $count++;

            if($mes_atual != $date->format("m")){
                $mes_atual = $date->format("m");
                $count = 1;
            }

            if($count == 15){
                $m++;
            }
        }

        $dias_contabilizados = $m * 30;

        // Calcula a diferença em segundos entre as datas
        $diferenca = strtotime($dt_final) - strtotime($dt_inicial);

        if($m > 12) {
            $m -=12;
        }

        $meses_ativos = round($m);

        return $meses_ativos;
    }
    
}