<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/RescisaoClass.php');
include('../../classes/calculos.php');
include('classes/MovimentoRescisaoClass.php');
include('../../classes/CltClass.php');


$id_funcionario = isset($_COOKIE['logado']) ? $_COOKIE['logado'] : FALSE;



if (!$id_funcionario) {
    exit('ERRO DE LOGIN');
}
$dev = TRUE; //TRUE para não gravar (em teste)

$acao = isset($_POST['acao']) ? $_POST['acao'] : FALSE;

//52, 53,54,55,56,58,106,109, 115,117

switch ($acao) {
    case 'salva_rescisao_complementar':


        $nao_gravar = FALSE; // TRUE para testar sem gravar no banco;
        $print = FALSE; // TRUE PARA PRINTAR AS QUERYS


        $arr_credito = isset($_POST['credito']) ? $_POST['credito'] : FALSE; //campos fixos gravados na tabela
        $arr_debito = isset($_POST['debito']) ? $_POST['debito'] : FALSE; //campos fixos gravados na tabela
        $id_recisao = isset($_POST['id_recisao']) ? $_POST['id_recisao'] : FALSE;
        $id_clt = isset($_POST['id_clt']) ? $_POST['id_clt'] : FALSE;                
        
        $obj_rescisao = new Rescisao();

        $rescisao = $obj_rescisao->getRescisao($id_recisao);
        $rescisao = $rescisao[0];

        $arr_campos = array();
        $arr_campos['id_clt'] = $id_clt;
        $arr_campos['nome'] = "'" . $rescisao['nome'] . "'";
        $arr_campos['id_regiao'] = "'" . $rescisao['id_regiao'] . "'";
        $arr_campos['id_projeto'] = "'" . $rescisao['id_projeto'] . "'";
        $arr_campos['id_curso'] = "'" . $rescisao['id_curso'] . "'";
        $arr_campos['data_adm'] = "'" . $rescisao['data_adm'] . "'";
        $arr_campos['data_demi'] = "'" . $rescisao['data_demi'] . "'";
        $arr_campos['data_proc'] = "'" . $rescisao['data_proc'] . "'";
        $arr_campos['dias_saldo'] = "'" . $rescisao['dias_saldo'] . "'";
        $arr_campos['um_ano'] = "'" . $rescisao['um_ano'] . "'";
        $arr_campos['meses_ativo'] = "'" . $rescisao['meses_ativo'] . "'";
        $arr_campos['motivo'] = "'" . $rescisao['motivo'] . "'";
        $arr_campos['fator'] = "'" . $rescisao['fator'] . "'";
        $arr_campos['aviso'] = "'" . $rescisao['aviso'] . "'";
        $arr_campos['aviso_valor'] = '0';
        $arr_campos['avos_dt'] = "'" . $rescisao['avos_dt'] . "'";
        $arr_campos['avos_fp'] = "'" . $rescisao['avos_fp'] . "'";
        $arr_campos['dias_aviso'] = "'" . $rescisao['dias_aviso'] . "'";
        $arr_campos['data_aviso'] = "'" . $rescisao['data_aviso'] . "'";
        $arr_campos['data_fim_aviso'] = "'" . $rescisao['data_fim_aviso'] . "'";
        $arr_campos['fgts8'] = "'" . $rescisao['fgts8'] . "'";
        $arr_campos['fgts40'] = "'" . $rescisao['fgts40'] . "'";
        $arr_campos['fgts_anterior'] = "'" . $rescisao['fgts_anterior'] . "'";
        $arr_campos['fgts_cod'] = "'" . $rescisao['fgts_cod'] . "'";
        $arr_campos['fgts_saque'] = "'" . $rescisao['fgts_saque'] . "'";
        $arr_campos['sal_base'] = "'" . $rescisao['sal_base'] . "'";
        $arr_campos['user'] = "'" . $id_funcionario . "'";
        $arr_campos['folha'] = "'0'";
        $arr_campos['status'] = "'0'";
        $arr_campos['vinculo_id_rescisao'] = "'" . $id_recisao . "'";
        $arr_campos['rescisao_complementar'] = "'1'";



        $total_rendimento = 0;
        $total_deducao = 0;

        $arr_movimentos = array();


        $ids_mov = array();
        foreach ($arr_credito as $k => $v) {
            if (strpos($v['name'], 'ov_') > 0) { // mov_$id_mov
                $arr_mov = explode('_', $v['name']);
                $arr_movimentos[$arr_mov[1]]['valor'] = $v['valor'];
                $arr_movimentos[$arr_mov[1]]['qnt'] = $v['qnt'];
                $arr_movimentos[$arr_mov[1]]['qnt_tipo'] = $v['qnt_tipo'];
                $arr_movimentos[$arr_mov[1]]['tipo'] = 'CREDITO';
                $ids_mov[] = $arr_mov[1];
            } else {
                $arr_campos[$v['name']] = "'" . $v['valor'] . "'";
            }
            $total_rendimento += $v['valor'];
        }
        foreach ($arr_debito as $k => $v) {
            if (strpos($v['name'], 'ov_') > 0) { // mov_$id_mov
                $arr_mov = explode('_', $v['name']);
                $arr_movimentos[$arr_mov[1]]['valor'] = $v['valor'];
                $arr_movimentos[$arr_mov[1]]['qnt'] = $v['qnt'];
                $arr_movimentos[$arr_mov[1]]['qnt_tipo'] = $v['qnt_tipo'];
                $arr_movimentos[$arr_mov[1]]['tipo'] = 'DEBITO';
                $ids_mov[] = $arr_mov[1];
            } else {
                $arr_campos[$v['name']] = "'" . $v['valor'] . "'";
            }
            $total_deducao += $v['valor'];
        }
        
        /*
         * 10/03/2017
         * by: Max
         * COLOCANDO PRA GRAVAR DATAS DE FÉRIAS VENCIDAS E PROPORCIONAIS DA RESCISÃO PRINCIPAL
         * QUANDO TIVER VALOR NA RESCISÃO COMPLEMENTAR
         */
        if($arr_campos['ferias_pr'] != ""){            
            $arr_campos['fp_data_ini'] = "'{$rescisao['fp_data_ini']}'";
            $arr_campos['fp_data_fim'] = "'{$rescisao['fp_data_fim']}'";
        }
        
        if($arr_campos['ferias_vencidas'] != ""){            
            $arr_campos['fv_data_ini'] = "'{$rescisao['fv_data_ini']}'";
            $arr_campos['fv_data_fim'] = "'{$rescisao['fv_data_fim']}'";
        }
        
        $campos = array_keys($arr_campos);
        $valores = array_values($arr_campos);

        $campos = implode(',', $campos);
        $valores = implode(',', $valores);



        $sql_rescisao .= "INSERT INTO rh_recisao(" . $campos . ")" .
                " VALUES(" . $valores . ");";

        if ($nao_gravar) {
            $id_recisao_complementar = 0;
        } else {
            mysql_query($sql_rescisao);
            $id_recisao_complementar = mysql_insert_id();
        }
        if ($print) {
            echo $sql_rescisao . "\n";
            echo 'ID=> ' . $id_recisao_complementar . "\n";
        }

        if (!empty($ids_mov)) {

            $ids_mov = implode(',', $ids_mov);
            $sql = "SELECT A.*, CONCAT(CONCAT( IF(incidencia_inss=1,'5020',FALSE), ',' ,IF(incidencia_irrf=1,'5021',FALSE) , ',',IF(incidencia_fgts=1,'5023',FALSE)))  AS incidencia FROM rh_movimentos AS A WHERE id_mov IN($ids_mov);";
            $result = mysql_query($sql);

            $sql_insert_mov = "INSERT INTO rh_movimentos_rescisao(`id_rescisao`,`id_mov`,`id_clt`,`nome_movimento`,`tipo_qnt`,`qnt`,`qnt_horas`, `valor`,`status`,`incidencia`,`complementar`) VALUES";


            while ($row = mysql_fetch_array($result)) {
                $movimento = array('id_mov' => $row['id_mov'], 'nome' => utf8_encode($row['descicao']), 'valor' => $arr_movimentos[$row['id_mov']]['valor'], 'tipo' => $arr_movimentos[$row['id_mov']]['tipo'],
                        'qnt_tipo'=>$arr_movimentos[$row['id_mov']]['qnt_tipo'], 'qnt'=>$arr_movimentos[$row['id_mov']]['qnt']);

                $qnt_horas = '';
                $qnt_dias = '';
                
                if($movimento['qnt_tipo']==1){
                    $qnt_horas = $movimento['qnt'];
                }elseif($movimento['qnt_tipo']==2){
                    $qnt_dias = $movimento['qnt'];
                }
                
                $sql_insert_mov .= "('$id_recisao_complementar','$movimento[id_mov]','$id_clt','$movimento[nome]', '$movimento[qnt_tipo]', '$qnt_dias' , '$qnt_horas' ,'$movimento[valor]',1,'$row[incidencia]',1),";
            }

            $sql_insert_mov = substr($sql_insert_mov, 0, -1) . ';';

            if ($print) {
                echo $sql_insert_mov . "\n";
            }

            if (!$nao_gravar) {
                mysql_query($sql_insert_mov);
            }
        }

        //verificar os campos : a477, a479, a480, previdencia_ss, inss_ss, 

        $total_liquido = ($total_rendimento - $total_deducao);

        $sql_update = "UPDATE rh_recisao SET total_liquido='$total_liquido', total_deducao='$total_deducao', total_rendimento='$total_rendimento', `status`=1 WHERE id_recisao='$id_recisao_complementar'  LIMIT 1;";

        if (!$nao_gravar) {
            mysql_query($sql_update);
        }

        if ($print) {
            echo $sql_update . "\n";
        }
        echo json_encode(array('msg' => utf8_encode('RESCISÃO COMPLEMENTAR CRIADA COM SUCESSO!')));
        exit();

        break;
    case 'calcular_rescisao' :

        // tela de contabilização da rescisão
        $dados = array();
        $dados['id_clt'] = isset($_POST['id_clt']) ? $_POST['id_clt'] : NULL;
        $dados['dispensa'] = isset($_POST['dispensa']) ? $_POST['dispensa'] : NULL;
        $dados['fator'] = isset($_POST['fator']) ? $_POST['fator'] : NULL;
        $dados['dias_trabalhados'] = isset($_POST['dias_trabalhados']) ? $_POST['dias_trabalhados'] : NULL;
        $dados['remuneracao_rescisorios'] = isset($_POST['remuneracao_rescisorios']) ? $_POST['remuneracao_rescisorios'] : NULL;
        $dados['aviso_previo'] = isset($_POST['aviso_previo']) ? $_POST['aviso_previo'] : NULL;
        $dados['dias'] = isset($_POST['dias']) ? $_POST['dias'] : NULL;
        $dados['data_aviso'] = isset($_POST['data_aviso']) ? $_POST['data_aviso'] : NULL;
        $dados['devolucao_credito'] = isset($_POST['devolucao_credito']) ? $_POST['devolucao_credito'] : NULL;
        
        //////DADOS DO CLT
        $ano_atual = date('Y');
        
        $objClt = new CltClass();
        $objClt->carregaClt($dados['id_clt']);
        
        
        
        $rescisao = new Rescisao();
        $result_rescisao = $rescisao->getRescisaoByClt($dados['id_clt']);
        
        if(mysql_num_rows($result_rescisao)>0){
            exit('Erro! Já existe uma rescisão para este funcionário!');
        }
        
        $clt_rescisao = $rescisao->calculaRescisao($dados);
        
        
        $row_insalubridade = NULL;
        if($clt_rescisao->insalubridade==1){
            $sql_insalubridade = "SELECT @salario_minimo := (SELECT fixo FROM rh_movimentos WHERE cod=0001 AND anobase=DATE_FORMAT({$clt_rescisao->data_demi},'%Y')) AS salario_minimo, 
            @var_id_mov_insalubridade :=IF(A.tipo_insalubridade=1,56,IF(A.tipo_insalubridade=2,235,0)) AS id_mov_insalubridade,
            B.cod, B.percentual, ((@salario_minimo * A.qnt_salminimo_insalu) *  B.percentual) AS valor_integral, (((@salario_minimo * A.qnt_salminimo_insalu) *  B.percentual) / 30) AS valor_proporcional,
            A.* 
            FROM curso AS A 
            INNER JOIN rh_movimentos AS B ON(B.id_mov=@var_id_mov_insalubridade)
            WHERE A.id_curso={$clt_rescisao->id_curso};";
            $result = mysql_query($sql_insalubridade);
            $row_insalubridade = mysql_fetch_assoc($result);
            
        }
        
        include_once 'view/pre_rescisao.php';
        echo '<pre>';
        print_r($_POST);
        print_r($clt_rescisao);
        echo '</pre>';
        exit('2');
        
        $tabelaImpostos = array();
        //Carregando tabelas para calculo dos impostos
        $qr_impostos = mysql_query("SELECT id_mov,cod, descicao,categoria, faixa, v_ini, v_fim, percentual, fixo, piso, teto, anobase
                                    FROM rh_movimentos 
                                    WHERE cod IN(5020,5021,5022,5023,5024,5049, 50241,0001) AND anobase = DATE_FORMAT('{$clt_rescisao->data_demi}','%Y')") or die(mysql_error());
       while($row_mov = mysql_fetch_assoc($qr_impostos)){           
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['id_mov']     = $row_mov['id_mov'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['cod']        = $row_mov['cod'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['descicao']   = $row_mov['descicao'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['categoria']   = $row_mov['categoria'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['v_ini']      = $row_mov['v_ini'];   
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['v_fim']      = $row_mov['v_fim'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['percentual'] = $row_mov['percentual'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['fixo']       = $row_mov['fixo'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['piso']       = $row_mov['piso'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['teto']       = $row_mov['teto'];
           $tabelaImpostos[$row_mov['cod']][$row_mov['faixa']]['anobase']    = $row_mov['anobase'];
       }



        //Periculosidade
        if ($dadosClt['periculosidade_30'] == 1) {
            $periculosidade = $objCalcFolha->getPericulosidade($salario_base_limpo, $dias_trabalhados);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($id_projeto);
            $objMovimento->setIdClt($id_clt);
            $objMovimento->setIdMov(57);
            $objMovimento->setCodMov(6007);
            $objMovimento->setMes(16);
            $objMovimento->setAno(2014);
            $valor_mov = $periculosidade['valor_proporcional'];

            $verfica_movimento = $objMovimento->verificaMovimento();

            if (empty($verfica_movimento['num_rows'])) {
                $insere = $objMovimento->insereMovimento($valor_mov);
            }/* else {

              if($verfica_movimento['valor_movimento'] != number_format($valor_mov,2,'.','')){
              $objMovimento->updateValorPorId($verfica_movimento['id_movimento'], $valor_mov);
              }

              } */
        }


        ////////////////////////////////////////////////////
        ///////////   MÉDIA DOS MOVIMENTOS  RECEBIDOS  ////
        //////////////////////////////////////////////////
        //$media_movimentos = $objCalcFolha->getMediaMovimentos($id_clt, $arrayDataDemissao['mes'], $arrayDataDemissao['ano'], $mesesTrab,true); //Confirmar forma de calcular
        $media_movimentos = $objCalcRescisao->getMediaMovimentos();
        $total_rendi = $media_movimentos['total_media'];

        //////////////////////////////////////////////////////////
        /// Base de cáclulo para 13º, Férias e  Aviso Prévio /////
        /////////////////////////////////////////////////////////
        $salarioBaseCalc = $salario_contratual + $total_rendi + $insalubridade['valor_integral'] + $periculosidade['valor_integral'];



        //  $salarioFamilia = $objCalcRescisao->getSalarioFamilia($salario_contratual, $diasTrab, $dados['id_projeto'], $dados['data_demi']); //Verificar na calculos.php                    
        $art479 = $objCalcRescisao->getArt479($salario_contratual, $dadosClt['data_entrada'], $data_demissao);
        $art480 = $objCalcRescisao->getArt480($salario_contratual, $dadosClt['data_entrada'], $data_demissao);
        $art477 = $objCalcRescisao->getArt477($salario_contratual, $data_demissao);
        $avisoPrevio = $objCalcRescisao->getAvisoPrevio($salarioBaseCalc, $periodoTrabalhado['anos_trabalhados']);


        ////////////////////////////////////////////////////////////////////////////////
        ////CALCULO DE INSS E IRRF SOBRE SALDO DE SALARIO E MOVIMENTOS LANÇADOS  //////
        //////////////////////////////////////////////////////////////////////////////                        
        $saldo_salario = $objCalcRescisao->getSaldoSalario($salario_contratual, $diasTrab);
        $movimentosLancados = $objCalcRescisao->getMovimentosRescisaoLancados();

        $baseCalcINSS = $saldo_salario + $movimentosLancados['base_inss'] + $insalubridade['valor_proporcional'];
        $inss = $objCalcFolha->getCalcInss($baseCalcINSS, 2, $dadosClt['desconto_inss'], $dadosClt['tipo_desconto_inss'], $dadosClt['salario_outra_empresa'], $dadosClt['desconto_outra_empresa']);

        $baseCalcIrrf = $saldo_salario + $movimentosLancados['base_irrf'] + $insalubridade['valor_proporcional'] - $inss['valor_inss'];
        $irrf = $objCalcFolha->getCalcIrrf($baseCalcIrrf, $id_clt, 2);

        ///////////////////////////////////////////////////
        ////CALCULO DE INSS E IRRF SOBRE 13º SALARIO  ////
        /////////////////////////////////////////////////
        $decimoTerceiro = $objCalcRescisao->getDecimoTerceiroProporcional($salarioBaseCalc, $dadosClt['data_entrada'], $data_demissao);
        $inss_13 = $objCalcFolha->getCalcInss($decimoTerceiro['base_inss'], 2);

        $baseCalcIrrf_13 = $decimoTerceiro['base_inss'] - $inss_13['valor_inss'];
        $irrf_13 = $objCalcFolha->getCalcIrrf($baseCalcIrrf_13, $id_clt, 2);


        //////////////////////////////////
        /////////     Férias    /////////
        ////////////////////////////////

        $objCalcFerias->setIdClt($id_clt);
        $ferias = $objCalcFerias->getPeriodoFeriasRescisao($id_clt, $dadosClt['data_entrada'], $data_demissao);

        //Proporcionais
        $meses_fp = $objCalcRescisao->getCalculoQntAvos($ferias['periodo_proporcional']['inicio'], $ferias['periodo_proporcional']['fim']);

        //faltas no periodo
        // $qntFaltasProp = $objCalcFerias->getFaltasNoPeriodo($ferias['periodo_proporcional']['inicio'], $ferias['periodo_proporcional']['fim']);
        // $qntDiasFaltasProp = $objCalcRescisao->getDiaProporcionalFaltasRescisao($meses_fp, $qntFaltasProp['total_faltas']);

        $feriasProporcionais = $objCalcRescisao->getCalculoFeriasProp($salarioBaseCalc, $meses_fp);


        //Vencidas
        if (sizeof($ferias['periodos_vencido']) > 0) {
            foreach ($ferias['periodos_vencido'] as $periodo) {

                $dadosFeriasVenc = $objCalcRescisao->getCalcFeriasVencidas($salarioBaseCalc, $periodo['inicio'], $periodo['fim']);
                $feriasVencidas[] = $dadosFeriasVenc;
                $TOTAL_FERIAS_VENCIDAS += $dadosFeriasVenc['valor_ferias'] + $dadosFeriasVenc['valor_um_terco_ferias'];
            }
        }






        echo '<pre>';
        print_r($movimentosLancados);
        echo '</pre>';

        ////////////
        ///TOTAIS //
        ////////////
        $TOTAL_SALDO_SALARIO = $saldo_salario + $movimentosLancados['total_rendimentos'] - ($movimentosLancados['total_desconto'] + $inss['valor_inss'] + $irrf['valor_irrf']);
        $TOTAL_DECIMO_PROPORCIONAL = ($decimoTerceiro['valor_13'] + $decimoTerceiro['valor_13_indenizado']) - $inss_13['valor_inss'] - $irrf_13['valor_irrf'] - $decimoTerceiro['valor_13_folha'];
        $TOTAL_FERIAS_PROPORCIONAIS = $feriasProporcionais['valor_ferias'] + $feriasProporcionais['valor_um_terco_ferias'] + $feriasProporcionais['ferias_aviso_indenizado'] + $feriasProporcionais['um_terco_ferias_aviso_indenizado'];
        $TOTAL_OUTROS_VENCIMENTOS = $valor_sal_familia + $art477 + $art479 + $avisoPrevio['aviso_credito'] + $insalubridade['valor_proporcional'] + $avisoPrevio['valor_lei_12506'];
        $TOTAL_OUTROS_DESCONTOS = $art480 + $avisoPrevio['aviso_debito'];



        $to_rendimentos = $saldo_salario + $movimentosLancados['total_rendimentos'] + $decimoTerceiro['valor_13'] + $decimoTerceiro['valor_13_indenizado'] + $TOTAL_FERIAS_PROPORCIONAIS + $TOTAL_FERIAS_VENCIDAS + $TOTAL_OUTROS_VENCIMENTOS;

        $to_descontos = $movimentosLancados['total_desconto'] + $inss['valor_inss'] + $irrf['valor_irrf'] + $inss_13['valor_inss'] + $irrf_13['valor_irrf'] + $decimoTerceiro['valor_13_folha'] + $TOTAL_OUTROS_DESCONTOS;

        //////////////////////
        ///VALOR FINAL//////
        /////////////////////
        $valor_rescisao_final = $to_rendimentos - $to_descontos;

        if ($valor_rescisao_final < 0) {

            $arredondamento_positivo = abs($valor_rescisao_final);

            if ($dispensa == 60) {
                $valor_rescisao_final = $aviso_previo_valor_d;
            } else {
                $valor_rescisao_final = NULL;
            }

            $to_rendimentos = $to_rendimentos + $arredondamento_positivo;
            $valor_rescisao_final = NULL;
        } else {
            $arredondamento_positivo = NULL;
            $valor_rescisao_final = $to_rendimentos - $to_descontos;
        }
        
               
                
        $CAMPOS_INSERT['id_clt'] = $id_clt;
        $CAMPOS_INSERT['nome'] = $nome;
        $CAMPOS_INSERT['id_regiao'] = $dadosClt['id_regiao'];
        $CAMPOS_INSERT['id_projeto'] = $dadosClt['id_projeto'];
        $CAMPOS_INSERT['id_curso'] = $dadosClt['id_curso'];
        $CAMPOS_INSERT['data_adm'] = $dadosClt['data_entrada'];
        $CAMPOS_INSERT['data_demi'] = $data_demissao;
        $CAMPOS_INSERT['data_proc'] = date('Y-m-d');
        $CAMPOS_INSERT['dias_saldo'] = $diasTrab;
        $CAMPOS_INSERT['um_ano'] = $periodoTrabalhado['anos_trabalhados'];
        $CAMPOS_INSERT['motivo'] = $dispensa;
        $CAMPOS_INSERT['fator'] = $fator;
        $CAMPOS_INSERT['aviso'] = $aviso;
        $CAMPOS_INSERT['aviso_valor'] = $valor_aviso_previo;
        $CAMPOS_INSERT['dias_aviso'] = $previo;
        $CAMPOS_INSERT['data_fim_aviso'] = $data_fim_avprevio;
        $CAMPOS_INSERT['fgts8'] = $fgts8_totalT;
        $CAMPOS_INSERT['fgts40'] = $fgts4_totalT;
        $CAMPOS_INSERT['fgts_anterior'] = $anterior;
        $CAMPOS_INSERT['fgts_cod'] = $cod_mov_fgts;
        $CAMPOS_INSERT['fgts_saque'] = $cod_saque_fgts;
        $CAMPOS_INSERT['sal_base'] = $salario_base_limpo;
        $CAMPOS_INSERT['saldo_salario'] = $saldo_de_salario;
        $CAMPOS_INSERT['inss_ss'] = $inss_saldo_salario;
        $CAMPOS_INSERT['previdencia_ss'] = $inss_saldo_salario;
        $CAMPOS_INSERT['ir_ss'] = $irrf_saldo_salario;
        $CAMPOS_INSERT['terceiro_ss'] = $valor_13_indenizado;
        $CAMPOS_INSERT['dt_salario'] = $valor_td;
        $CAMPOS_INSERT['inss_dt'] = $valor_td_inss;
        $CAMPOS_INSERT['previdencia_dt'] = $valor_td_inss;
        $CAMPOS_INSERT['ir_dt'] = $valor_td_irrf;
        $CAMPOS_INSERT['ferias_vencidas'] = $fv_valor_base;
        $CAMPOS_INSERT['umterco_fv'] = $fv_um_terco;
        $CAMPOS_INSERT['ferias_pr'] = $fp_valor_total;
        $CAMPOS_INSERT['umterco_fp'] = $fp_um_terco;
        $CAMPOS_INSERT['sal_familia'] = $valor_sal_familia;
        $CAMPOS_INSERT['to_sal_fami'] = ($valor_sal_familia + $sal_familia_anterior);
        $CAMPOS_INSERT['insalubridade'] = $valor_insalubridade;
        $CAMPOS_INSERT['a480'] = $art_480;
        $CAMPOS_INSERT['a479'] = $art_479;
        $CAMPOS_INSERT['a477'] = $valor_atraso;
        $CAMPOS_INSERT['lei_12_506'] = $lei_12_506;
        $CAMPOS_INSERT['total_rendimento'] = $to_rendimentos;
        $CAMPOS_INSERT['total_deducao'] = $to_descontos;
        $CAMPOS_INSERT['total_liquido'] = $valor_rescisao_final;
        $CAMPOS_INSERT['arredondamento_positivo'] = $arredondamento_positivo;
        $CAMPOS_INSERT['avos_dt'] = $meses_ativo_dt;
        $CAMPOS_INSERT['avos_fp'] = $meses_ativo_fp;
        $CAMPOS_INSERT['data_aviso'] = $data_aviso;
        $CAMPOS_INSERT['devolucao'] = $devolucao;
        $CAMPOS_INSERT['faltas'] = $faltas;
        $CAMPOS_INSERT['valor_faltas'] = $valor_faltas;
        $CAMPOS_INSERT['user'] = $user;
        $CAMPOS_INSERT['ferias_aviso_indenizado'] = $ferias_aviso_indenizado;
        $CAMPOS_INSERT['umterco_ferias_aviso_indenizado'] = $umterco_ferias_aviso_indenizado;
        $CAMPOS_INSERT['adiantamento_13'] = $valor_decimo_folha;
        $CAMPOS_INSERT['um_terco_ferias_dobro'] = $fv_um_terco_dobro;
        $CAMPOS_INSERT['fv_dobro'] = $multa_fv;
        $CAMPOS_INSERT['fp_data_ini'] = $periodo_proporcional_inicio;
        $CAMPOS_INSERT['fp_data_fim'] = $periodo_proporcional_final;
        $CAMPOS_INSERT['qnt_dependente_salfamilia'] = $TOTAL_MENOR;
        $CAMPOS_INSERT['base_inss_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
        $CAMPOS_INSERT['percentual_inss_ss'] = $PERCENTUAL_INSS_SS;
        $CAMPOS_INSERT['base_irrf_ss'] = $BASE_CALC_IRRF_SALDO_SALARIO;
        $CAMPOS_INSERT['percentual_irrf_ss'] = $PERCENTUAL_IRRF_SS;
        $CAMPOS_INSERT['parcela_deducao_irrf_ss'] = $PARCELA_DEDUCAO_IR_SS;
        $CAMPOS_INSERT['qnt_dependente_irrf_ss'] = $QNT_DEPENDENTES_IRRF_SS;
        $CAMPOS_INSERT['valor_ddir_ss'] = $VALOR_DDIR_SS;
        $CAMPOS_INSERT['base_fgts_ss'] = $BASE_CALC_INSS_SALDO_SALARIO;
        $CAMPOS_INSERT['base_inss_13'] = $BASE_CALC_INSS_13;
        $CAMPOS_INSERT['percentual_inss_13'] = $PERCENTUAL_INSS_13;
        $CAMPOS_INSERT['base_irrf_13'] = $BASE_CALC_IRRF_13;
        $CAMPOS_INSERT['percentual_irrf_13'] = $PERCENTUAL_IRRF_13;
        $CAMPOS_INSERT['parcela_deducao_irrf_13'] = $PARCELA_DEDUCAO_IR_13;
        $CAMPOS_INSERT['base_fgts_13'] = $BASE_CALC_INSS_13;
        $CAMPOS_INSERT['qnt_dependente_irrf_13'] = $QNT_DEPENDENTES_IRRF_13;
        $CAMPOS_INSERT['valor_ddir_13'] = $VALOR_DDIR_13;
        $CAMPOS_INSERT['desconto_inss'] = $row_clt['desconto_inss'];
        $CAMPOS_INSERT['salario_outra_empresa'] = $row_clt['salario_outra_empresa'];
        $CAMPOS_INSERT['desconto_inss_outra_empresa'] = $row_clt['desconto_outra_empresa'];

        if ($_REQUEST['recisao_coletiva'] == 1) {
            $CAMPOS_INSERT['recisao_provisao_de_calculo'] = 1;
            $CAMPOS_INSERT['status'] = 0;
            $CAMPOS_INSERT['id_recisao_lote'] = $_REQUEST['id_header'];
        }


        foreach ($CAMPOS_INSERT as $campo => $valor) {
            $campos[] = $campo;
            $valores[] = "'$valor'";
        }
        $campos = implode(',', $campos);
        $valores = implode(',', $valores);


        // Arquivo TXT
        $conteudo = "INSERT INTO rh_recisao($campos ) VALUES ( $valores);\r\n";
        /*         * ***********GAMBI FILHO DA PUTA PRA FUNCIONAR ESSA PORRA*********** */
        if ($_REQUEST['recisao_coletiva'] == 1) {
            mysql_query($conteudo) or die("erro ao criar recisao em lote");
            $ultimo_rescisao_lote = mysql_insert_id();
//                                            if (sizeof($movimentos) > 0) {
//                                                $ids_movimentos = implode(',', $movimentos);
//
//                                                $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos)");
//                                                while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
//                                                    $query_movimento = "INSERT INTO rh_movimentos_rescisao (id_rescisao, id_mov, id_clt, nome_movimento, valor, incidencia ) VALUES ('{$ultimo_rescisao_lote}','{$row_mov[id_mov]}', '{$row_mov[id_clt]}', '{$row_mov[nome_movimento]}', '{$row_mov[valor_movimento]}','{$row_mov[incidencia]}' )";
//                                                    mysql_query($query_movimento) or die("Erro ao selecionar movimentos de rescisão");
//                                                }
//                                            }
        }


        $conteudo .= "UPDATE rh_clt SET status = '$dispensa', data_saida = '$data_demissao', status_demi = '1' WHERE id_clt = '$id_clt' LIMIT 1;\r\n";


        // AKI O PROBLEMA
        //$conteudo .= "INSERT INTO rh_eventos(id_clt, id_regiao, id_projeto, nome_status, cod_status, id_status, data, status) VALUES ('$id_clt', '$idregiao', '$idprojeto', '$row_evento[especifica]', '$dispensa', '$row_evento[0]', '$data_demissao', '1');\r\n";

        $nome_arquivo = 'recisaoteste_' . $id_clt . '_' . date('dmY') . '.txt';
        $arquivo = '../arquivos/' . $nome_arquivo;


        if (sizeof($movimentos) > 0) {
            $ids_movimentos = implode(',', $movimentos);

            $qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($ids_movimentos)");
            while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                $conteudo .= "INSERT INTO rh_movimentos_rescisao (id_rescisao, id_mov, id_clt, nome_movimento, valor, incidencia ) VALUES (ultimo_id_rescisao,'$row_mov[id_mov]', '$row_mov[id_clt]', '$row_mov[nome_movimento]', '$row_mov[valor_movimento]',  '$row_mov[incidencia]' ); \r\n";
            }
        }


// Tenta abrir o arquivo TXT            
        if (!$abrir = fopen($arquivo, "wa+")) {
            echo "Erro abrindo arquivo ($arquivo)";
            exit;
        }

// Escreve no arquivo TXT
        if (!fwrite($abrir, $conteudo)) {
            print "Erro escrevendo no arquivo ($arquivo)";
            exit;
        }


// Fecha o arquivo
        fclose($abrir);

// Encriptografando a variável
        $linkvolt = str_replace('+', '--', encrypt("$regiao&$id_clt"));
        $linkir = str_replace('+', '--', encrypt("$regiao&$id_clt&$nome_arquivo"));
        ?>
                        <table width="50%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td><a href="recisao2_teste.php?tela=4&enc=<?= $linkir ?>" class="botao recisao_lote">Processar Rescis&atilde;o</a></td>
                                <td><a href="recisao2_teste.php?tela=2&enc=<?= $linkvolt ?>" class="botao">Voltar</a></td>
                            </tr>
                        </table>
                        <p>&nbsp;</p>
                    </td>
                </tr>
            </table>
        </form>
        <?php
        break;

        break;
    default:
        break;
}