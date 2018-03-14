<?php
//if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){      
//    $retorno = array("status" => 0);
//    
//    if($_REQUEST['method'] == "gerar"){        
        include ("../../conn.php"); 
        include ("../../wfunction.php");
        include ("../../classes/SefipClass.php");
        include ("../../classes/FolhaClass.php");
        include ("../../classes/EventoClass.php");
        include ('../../classes_permissoes/botoes.class.php');
        
//        $id_folha = $_REQUEST['folha'];
        
        $usuario = $_COOKIE['logado'];
        
        $sefip_ctr = new Folha();
//        $res_info = $sefip_ctr->getFolhaInfo($id_folha);
        
//        $projeto = $res_info['projeto'];
//        $regiao = $res_info['regiao'];
        $mes = $_REQUEST['mes'];
        $ano = $_REQUEST["ano"];
        $cnpj = $_REQUEST["cnpj"];
        $terceiro = $_REQUEST["terceiro"];
        $lote = $_REQUEST["lote"];
//        $id_master = $res_info["id_master"];
        
        $re = new SefipClass($mes, $ano, $id_master, $terceiro);
        $evento = new Eventos();
        
        if (empty($id_folha)) {
            $idFolhas = $re->getIdFolhas($cnpj, $terceiro);
            $rowIdFolha = mysql_fetch_assoc($idFolhas);
            $id_folha = implode($rowIdFolha, ',');
        }
        
        $arrayMov = $re->montaArrayStatusCodMovimento();
        $arrayRescisao = $re->montaArrayRescisao();
        
        if ($terceiro == 1) {
            $dtNome = '_DT';
        }
        
        $dataHoje = date('YmdHis');
        $nomeFile = normalizaNometoFile("SEFIP_{$cnpj}_{$mes}_{$ano}.re");
        $nomeFile2 = normalizaNometoFile("SEFIP_{$cnpj}_{$mes}_{$ano}{$dtNome}_$dataHoje.re");
        
        if($lote){
            $nomeFile = normalizaNometoFile("SEFIP_LOTE_{$mes}_{$ano}.re");
        }
//        $arquivo = fopen("arquivos/".$nomeFile, "w");
        
//        $nomeFile = normalizaNometoFile("SEFIP_" . $projeto . ".re");
        $arquivo = fopen($nomeFile, "w");
        
        $empregador = $re->getEmpregador($regiao, null, $cnpj);
        $rowEmpregador = mysql_fetch_assoc($empregador);
        
        $empregador2 = $re->getEmpregador($regiao, $projeto, $cnpj);
        $rowEmpregador2 = mysql_fetch_assoc($empregador2);                
        
        $re->montaReg00($arquivo, $rowEmpregador2);
        if ($terceiro == 2) {    
            $salFamiMaternidade = $re->getSalMaternidade_Familia($id_folha);
            $rowSalFamiMaternidade = mysql_fetch_assoc($salFamiMaternidade);
        
        //    echo "<pre>";
        //    print_r($rowEmpregador);
        //    echo "</pre>";
        //    
        //    exit();

            $re->montaReg10($arquivo, $rowEmpregador, $rowSalFamiMaternidade);
        } else {
            $re->montaReg10($arquivo, $rowEmpregador);
        }
        $re->montaReg12($arquivo, $rowEmpregador);

//        $idsCltAnteriores = $re->getIdsCltAnteriores($regiao);
        $idsCltAnteriores = $re->getIdsCltAnteriores($id_folha);
        while ($rowIdsCltAnt = mysql_fetch_assoc($idsCltAnteriores)) {
            $arrayIdCltAnt[] = $rowIdsCltAnt['id_cltAnteriores'];
        }

        $re->setId_regiao($regiao);
        $re->setId_projeto($projeto);
        $empregado = $re->getEmpregado($id_folha);

        //exit();
        
        while ($rowEmpregado = mysql_fetch_assoc($empregado)) {        
            
//            if($_COOKIE['logado'] == 354 AND $rowEmpregado['id_trab'] == 4454){
//                print_array($rowEmpregado);                
//            }
            
            /*
             * 05/02/2018
             * By: Max
             * CRIADO PARA CASOS DE DEMISSÃO E ADMISSÃO NO MESMO MÊS 
             * CHAMADO DE MÚLTIPLOS VÍNCULOS         
             */
            if(array_key_exists($rowEmpregado['pislimpo'], $arrayReg30)){
                $arrayReg30[$rowEmpregado['pislimpo']]['ocorrencia'] = '05';
                $rowEmpregado['pislimpo'] = $rowEmpregado['pislimpo']."X";
                $rowEmpregado['ocorrencia'] = '05';                
            }
            
            $arrayDadosBasicos[$rowEmpregado['pislimpo']]['pis'] = $rowEmpregado['pislimpo'];
            $arrayDadosBasicos[$rowEmpregado['pislimpo']]['id_projeto'] = $rowEmpregado['id_projeto'];
            $arrayDadosBasicos[$rowEmpregado['pislimpo']]['id_regiao'] = $rowEmpregado['id_regiao'];
            $arrayDadosBasicos[$rowEmpregado['pislimpo']]['data_entrada'] = $rowEmpregado['data_entrada'];
            $arrayDadosBasicos[$rowEmpregado['pislimpo']]['nome'] = $rowEmpregado['nome'];
            $arrayDadosBasicos[$rowEmpregado['pislimpo']]['campo1'] = $rowEmpregado['campo1'];
            $arrayDadosBasicos[$rowEmpregado['pislimpo']]['serie_ctps'] = $rowEmpregado['serie_ctps'];

            if($rowEmpregado['categoria'] != "13"){
                $arrayDadosBasicos[$rowEmpregado['pislimpo']]['id_trab'] = $rowEmpregado['id_trab'];
            }

            $arrayReg30[$rowEmpregado['pislimpo']]['data_nasci'] = $rowEmpregado['data_nasci'];
            $arrayReg30[$rowEmpregado['pislimpo']]['data_importacao'] = $rowEmpregado['data_importacao'];
            $arrayReg30[$rowEmpregado['pislimpo']]['cod'] = $rowEmpregado['cod'];
            $arrayReg30[$rowEmpregado['pislimpo']]['base_inss_13_rescisao'] = $rowEmpregado['base_inss_13_rescisao'];
            $arrayReg30[$rowEmpregado['pislimpo']]['base_inss'] = $rowEmpregado['base_inss'];
            $arrayReg30[$rowEmpregado['pislimpo']]['base_fgts'] = $rowEmpregado['base_fgts'];
            $arrayReg30[$rowEmpregado['pislimpo']]['decimo_terceiro'] = $rowEmpregado['decimo_terceiro'];
            $arrayReg30[$rowEmpregado['pislimpo']]['status_clt'] = $rowEmpregado['status_clt'];
            $arrayReg30[$rowEmpregado['pislimpo']]['ocorrencia'] = $rowEmpregado['ocorrencia'];
            $arrayReg30[$rowEmpregado['pislimpo']]['inss_rescisao'] = $rowEmpregado['inss_rescisao'];
            $arrayReg30[$rowEmpregado['pislimpo']]['desconto_inss'] = $rowEmpregado['desconto_inss'];
            $arrayReg30[$rowEmpregado['pislimpo']]['tipo_desconto_inss'] = $rowEmpregado['tipo_desconto_inss'];
            $arrayReg30[$rowEmpregado['pislimpo']]['valDescSegurado'] = $rowEmpregado['valDescSegurado'];
            $arrayReg30[$rowEmpregado['pislimpo']]['mes'] = $rowEmpregado['mes'];
            $arrayReg30[$rowEmpregado['pislimpo']]['ano'] = $rowEmpregado['ano'];
            $arrayReg30[$rowEmpregado['pislimpo']]['data_inicio'] = $rowEmpregado['data_inicio'];
            $arrayReg30[$rowEmpregado['pislimpo']]['data_final'] = $rowEmpregado['data_fim'];
            $arrayReg30[$rowEmpregado['pislimpo']]['data_demi'] = $rowEmpregado['data_demi'];
            $arrayReg30[$rowEmpregado['pislimpo']]['valor_ferias'] = $rowEmpregado['valor_ferias'];
            $arrayReg30[$rowEmpregado['pislimpo']]['categoria'] = $rowEmpregado['categoria'];

        //    print_array($arrayReg30);

//            if ($terceiro == 2) {
//                if (!empty($rowEmpregado['sefip_codigo'])) {
//                    $arrayReg13[$rowEmpregado['pislimpo']]['sefip_codigo'] = $rowEmpregado['sefip_codigo'];
//                    $arrayReg13[$rowEmpregado['pislimpo']]['sefip_valor'] = $rowEmpregado['sefip_valor'];
//                    
//                    if($_COOKIE['debug'] == 666){
//                        echo "////////////////////////////////";
//                        echo "REG 13";
//                        echo "////////////////////////////////";
//                        print_array($rowEmpregado);
//                    }
//                }
//            }

            if ($terceiro == 2) {
                if($rowEmpregado['categoria'] != "13"){
                    $arrayReg14[$rowEmpregado['pislimpo']]['endereco'] = $rowEmpregado['endereco'];
                    $arrayReg14[$rowEmpregado['pislimpo']]['bairro'] = $rowEmpregado['bairro'];
                    $arrayReg14[$rowEmpregado['pislimpo']]['cep'] = $rowEmpregado['cep'];
                    $arrayReg14[$rowEmpregado['pislimpo']]['cidade'] = $rowEmpregado['cidade'];
                    $arrayReg14[$rowEmpregado['pislimpo']]['uf'] = $rowEmpregado['uf'];
                }
            }

            $parte = $rowEmpregado['parte'];
            
            unset($rowEmpregado);
        }
//        echo count($arrayReg13);exit;
        foreach ($arrayReg13 as $key13 => $dadosEmpregado) {
            $re->montaReg13($arquivo, $rowEmpregador, $arrayDadosBasicos[$key13], $dadosEmpregado);
            if($_COOKIE['debug'] == 666){
                echo "////////////////////////////////";
                echo "REG 13 VALOR";
                echo "////////////////////////////////";
                print_array($key13);
                print_array($dadosEmpregado);
            }
        }
        
        if ($terceiro == 2) {
            foreach ($arrayReg14 as $key14 => $dadosEmpregado) {
                if(!in_array($key14,$arrayIdCltAnt)){
                    $re->montaReg14($arquivo, $rowEmpregador, $arrayDadosBasicos[$key14], $dadosEmpregado); // OPCIONAL
                }
            }
        }
        
        foreach ($arrayReg30 as $key30 => $dadosEmpregado) {
            if($_COOKIE['debug'] == 666){
                echo "////////////////////////////////";
                echo 'QUERY $key30';
                echo "////////////////////////////////";
                print_array($key30);
            }
            if ($terceiro == 2) {
                if ($mes == 11 || $mes == 12) {
                    $decimo_terceiro = $re->getDecimoTerceiroMes($arrayDadosBasicos[$key30]['id_trab'], $arrayDadosBasicos[$key30]['id_projeto'], $terceiro);
                    $rowDecimoTerceiro = mysql_fetch_assoc($decimo_terceiro);
                }
            } else {
                //mes de dezembro trazer as informações de 13º
//                if ($mes == 12) {
//                    $decimo_terceiro = $re->getDecimoTerceiroMes($arrayDadosBasicos[$key30]['id_trab'], $arrayDadosBasicos[$key30]['id_projeto'], $terceiro);
//                    $rowDecimoTerceiro = mysql_fetch_assoc($decimo_terceiro);
//                } else {
                    //if($_COOKIE[logado] == 35){
                        //$decimo_terceiro_p = mysql_query("SELECT dt_salario AS decimo_terceiro FROM rh_recisao WHERE id_clt = {$arrayDadosBasicos[$key30]['id_trab']} AND status = 1");
                        //$rowDecimoTerceiro = mysql_fetch_assoc($decimo_terceiro_p);
                        //echo "<pre>"; print_r($dadosEmpregado); print_r($rowDecimoTerceiro);
                    //}
//                }
            }
            if($_COOKIE['debug'] == 666){
                echo "////////////////////////////////";
                echo 'ARRAY $decimo_terceiro';
                echo "////////////////////////////////";
                print_array($rowDecimoTerceiro);
            }
            //$re->montaReg20($arquivo, $tomadorServ);  // NÃO É USADO
            //$re->montaReg21($arquivo, $tomadorServ); // NÃO É USADO

            $dias_trab = $re->getDiasTrabalhadosByAno($arrayDadosBasicos[$key30]['id_trab'], $arrayDadosBasicos[$key30]['id_projeto']);
            $rowDiasTrab = mysql_fetch_assoc($dias_trab);    
            
            
            $verifica_ferias = "SELECT *, DATE_FORMAT(data_fim, '%d-%m-%Y') AS data_fim, DATE_FORMAT(data_ini, '%d-%m-%Y') AS data_ini, DATE_FORMAT(ADDDATE(LAST_DAY(SUBDATE(data_fim, INTERVAL 1 MONTH)), 1), '%d-%m-%Y') AS data_ini2, DATE_FORMAT(LAST_DAY(data_ini), '%d-%m-%Y') AS ultimo_dia
                FROM rh_ferias
                WHERE id_clt = '{$arrayDadosBasicos[$key30]['id_trab']}' AND '2016-06' BETWEEN DATE_FORMAT(data_ini, '%Y-%m') AND DATE_FORMAT(data_fim, '%Y-%m') AND STATUS = 1 AND MONTH(data_ini) = 6
                ORDER BY id_ferias DESC";
            $query_ferias = mysql_query($verifica_ferias) or die(mysql_error());
            $resul_ferias = mysql_fetch_assoc($query_ferias);
            $total_ferias = mysql_num_rows($query_ferias);
            
            //SEFIP EM LOTE
            if($lote){
                $tomadorServ = mysql_fetch_assoc($re->getTomador($arrayDadosBasicos[$key30]['id_regiao'], $arrayDadosBasicos[$key30]['id_projeto']));

    //            print_array($tomadorServ);
    //            exit();

                if ($tomadorServ['inscResp'] != $cnpj_tomadorAnt) {
                    $re->montaReg20($arquivo, $tomadorServ, $rowEmpregador);
                    $cnpj_tomadorAnt = $tomadorServ['inscResp'];
                }
            }
                    
            $re->montaReg30($arquivo, $rowEmpregador, $arrayDadosBasicos[$key30], $dadosEmpregado, $arrayRescisao, $rowDiasTrab['dias_trab'], $rowDecimoTerceiro);
            
            if ($terceiro == 2) {

                if(($dadosEmpregado['status_clt'] >= 60 && $dadosEmpregado['status_clt'] <= 66) || ($dadosEmpregado['status_clt'] == 81) || ($dadosEmpregado['status_clt'] == 101) ){
//                    print_array($rowDiasTrab);
                    $codMov = $dadosEmpregado['status_clt'];
                    $dataMov = $dadosEmpregado['data_demi'];
                    
                    ///////////MOVIMENTO DE RESCISAO
                    if (!empty($codMov)) {
                        foreach ($arrayMov as $key => $value) {
                            if ($codMov == $key) {
                                $codMov = $value;
                                $re->montaReg32($arquivo, $rowEmpregador, $arrayDadosBasicos[$key30], $codMov, $dataMov);
                                break;
                            }
                        }
                    }
                    unset($codMov, $dataMov);
                }

                $data_demi = date("m/Y", str_replace("/", "-", strtotime($dadosEmpregado['data_demi'])));
                $data_evento = $dadosEmpregado['mes']."/".$dadosEmpregado['ano'];

        //        echo $arrayDadosBasicos[$key30]['nome'].": ".$data_evento."<br>";
                
                if($_COOKIE['logado'] == 353 && $arrayDadosBasicos[$key30]['id_trab'] == 2952){
//                    echo "<pre>";
//                    print_r($dadosEmpregado);
//                    echo "</pre>";
//                    echo "{$arrayDadosBasicos[$key30]['id_trab']}, '{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}', {$dadosEmpregado['data_inicio']}, {$dadosEmpregado['data_final']}<br>";
                }
                $dados = $evento->validaEventoForFolha($arrayDadosBasicos[$key30]['id_trab'], "{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}", $dadosEmpregado['data_inicio'], $dadosEmpregado['data_final']);
//                if($_COOKIE['debug'] == 667){
                    
//                    echo "////////////////////////////////";
//                    echo 'ARRAY $dados = $evento->validaEventoForFolha';
//                    echo "////////////////////////////////";
//                    print_array($dados);
                    
                    $s = "
                    SELECT 
                    A.cod_status AS cod_evento,ADDDATE(A.data, INTERVAL 15 DAY) AS dt_inicio, 
                    IF(A.data_retorno = '0000-00-00' || A.data_retorno > LAST_DAY('{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01'), LAST_DAY('{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01'), A.data_retorno) AS dt_fim,
                    IF(MONTH(ADDDATE(A.data, INTERVAL 15 DAY)) != MONTH('{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01'), '{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01',ADDDATE(A.data, INTERVAL 15 DAY)) AS dt_inicio,
                    B.cod_movimentacao
                    FROM rh_eventos A 
                    LEFT JOIN rhstatus B ON (A.cod_status = B.codigo)
                    WHERE 
                    A.status = 1 AND 
                    A.cod_status NOT IN (10) AND 
                    ADDDATE(A.data, INTERVAL 15 DAY) <= LAST_DAY('{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01') AND 
                    (
                        A.data_retorno = '0000-00-00' OR
                        ADDDATE(A.data, INTERVAL 15 DAY) BETWEEN '{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01' AND LAST_DAY('{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01') OR
                        A.data_retorno BETWEEN '{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01' AND LAST_DAY('{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01') OR
                        '{$dadosEmpregado['ano']}-{$dadosEmpregado['mes']}-01' BETWEEN ADDDATE(A.data, INTERVAL 15 DAY) AND A.data_retorno 
                    ) AND 
                    A.id_clt = '{$arrayDadosBasicos[$key30]['id_trab']}'
                    ORDER BY A.id_evento DESC 
                    LIMIT 1;";
                    $dados = mysql_query($s) or die(mysql_error());
                    $dados = mysql_fetch_assoc($dados);
                    
                    if($_COOKIE['debug'] == 666){
                        echo "////////////////////////////////";
                        echo 'ARRAY $s';
                        echo "////////////////////////////////";
                        print_array($s);
                    }
                    
//                }
                if (!empty($dados)) {
                    $codMov = $dados['cod_evento'];
                    $dataMov = $dados['dt_inicio'];
                }
                
                if($dados['cod_evento'] == 67 && $dadosEmpregado['status_clt'] != 67){
                    unset($codMov, $dataMov);
                }

                unset($dados);
                //if($total_ferias > 0){
                if($_COOKIE['debug'] == 666){
                    echo "////////////////////////////////";
                    echo 'ARRAY $data_evento';
                    echo "////////////////////////////////";
                    print_array($data_evento);
                    echo "////////////////////////////////";
                    echo 'ARRAY $dataMov';
                    echo "////////////////////////////////";
                    print_array($dataMov);
                    echo "////////////////////////////////";
                    echo 'ARRAY str_replace("-", "", $dataMov) <= $dadosEmpregado["ano"].$dadosEmpregado["mes"]."30"';
                    echo "////////////////////////////////";
                    print_array(str_replace('-', '', $dataMov) . '<=' . $dadosEmpregado['ano'].$dadosEmpregado['mes'].'30');
                }
                if($arrayDadosBasicos[$key30]['id_trab'] == 110 && $dadosEmpregado['ano'].$dadosEmpregado['mes'] == '201611'){
                    $dataMov = '2016-11-24';
                }
//                if(str_replace('-', '', $dataMov) <= $dadosEmpregado['ano'].$dadosEmpregado['mes'].'30' && str_replace('-', '', $dataMov) >= $dadosEmpregado['ano'].$dadosEmpregado['mes'].'01'){
                    if($data_evento == $data_demi){
                        if (!empty($codMov)) {
                            foreach ($arrayMov as $key => $value) {
                                if ($codMov == $key) {
                                    $codMov = $value;
                                    $re->montaReg32($arquivo, $rowEmpregador, $arrayDadosBasicos[$key30], $codMov, $dataMov);
                                    break;
                                }
                            }
                        }

                    }elseif($data_evento != $data_demi && ($dadosEmpregado['status_clt'] < 60 || $dadosEmpregado['status_clt'] == 70)){
                        if (!empty($codMov)) {
                            foreach ($arrayMov as $key => $value) {
                                if ($codMov == $key) {
                                    $codMov = $value;
                                    $re->montaReg32($arquivo, $rowEmpregador, $arrayDadosBasicos[$key30], $codMov, $dataMov);
                                    break;
                                }
                            }
                        }
                    }
//                }
//}
                unset($codMov, $dataMov, $rowDecimoTerceiro);
            }
        }
        
        if($_COOKIE['debug'] == 666){
            echo "////////////////////////////////";
            echo 'QUERY arrayAltonomos ($arrayDadosBasicos2)';
            echo "////////////////////////////////";
            print_array($arrayDadosBasicos2);
        }
        
        $re->montaReg90($arquivo);
        
//        echo "teste $teste "; exit;
        
        fclose($arquivo);
//        echo $nomeFile;
        $re->gravaLog($regiao, $projeto, $id_folha, $usuario, $parte);
        
        if($_COOKIE['debug'] == "sefip"){
            exit();
        }
        
        //  BAIXA O ARQUIVO
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: application/x-msdownload");
        header("Content-Length: " . filesize($nomeFile));
        header("Content-Disposition: attachment; filename=$nomeFile");
        flush();
        copy($nomeFile, $nomeFile2);
        readfile($nomeFile);
        
//        $retorno = array("status" => 1, "file" => $nomeFile);        
//        
//        echo json_encode($retorno);        
        exit();
//    }                
//}

//}
?>