<?php

include_once('../../../wfunction.php');
/*
 * PHO-DOC - calculos_folha.php
 * 
 * ??-??-????
 * 
 * Classe para calculos da folha de pagamento
 * 
 * Versão: 3.0.8695 - 20/03/2016 - Jacques - Condição específica adicionada a pedido de Gimenez para o projeto UPA BEBEDOURO que não possui 
 *                                           sindicato definido ainda, mas precisa haver o desconto sindical.
 * Versão: 3.0.8724 - 30/03/2016 - Jacques - Adição de rotina para inclusão de lançamento de movimento de contribuição sindical
 * 
 * @Autor não definido
 *  
 */

/**
 * SINÉSIO LUIZ
 * 13/01/2015
 * SEMPRE QUE O MÊS DE REFERÊNCIA FOR JANEIRO
 */
if ($mes == "01") {

    $calcFolha = new Calculo_Folha();
    //MÉTODO QUE CALCULA A MÉDIA DE 13°
    $ano_anterior = $ano - 1;
    $mediasAnoAnterior = $calcFolha->getMediaMovimentos($clt, $mes, $ano_anterior, 12, 1, 1, 1);

    $objMovimento->setIdClt($clt);
    $objMovimento->setMes($mes);
    $objMovimento->setAno($ano);
    $objMovimento->setIdRegiao($regiao);
    $objMovimento->setIdProjeto($projeto);
    $objMovimento->setIdMov(310);
    $objMovimento->setCodMov(80035);
    $objMovimento->setLancadoPelaFolha(1);
//    if($mediasAnoAnterior['residuo_media'] != 0 && $regiao != 48){
    $dados = $objMovimento->verificaInsereAtualizaFolha($mediasAnoAnterior['residuo_media']);
//    }    
}



$array_cagada_folha = array();


/**
 * STATUS DO CLT
 */
$competenciaFolha = $ano . '-' . $mes;
$novoStatusClt = $objCalcFolha->getStatusAtualPorCompetecia($competenciaFolha, $data_fim, $clt, $projeto);


// Consulta de Dados do Participante
$qr_clt = mysql_query(" SELECT A.*, B.desconto
                        FROM rh_clt A
                        LEFT JOIN rh_inss_outras_empresas B ON (A.id_clt = B.id_clt AND DATE('2017-09-01') >= B.inicio AND DATE('2017-09-30') <= B.fim)
                        WHERE A.id_clt = $clt");
$row_clt = mysql_fetch_array($qr_clt);

/**
 * SINESIO LUIZ 
 * 07/07/2017
 * ATUALIZANDO INFORMAÇÕES DO SINDICATO NA 
 * FOLHA DE PAGAMENTO.. CASO SEJA MUDADO NO CADASTRO 
 * DO CLT 
 * 
 */
$qryUpdateSindicato = "UPDATE rh_folha_proc SET id_sindicato = '{$row_clt['rh_sindicato']}' WHERE id_clt = '{$clt}' AND id_folha = '{$row_folha['id_folha']}'";
$sqlUpdateSindicato = mysql_query($qryUpdateSindicato) or die('Erro ao atualizar sindicato');


// Buscando a Atividade do Participante e o Salário Limpo TIPO DE INSALUBRIDADE
$qr_curso = mysql_query("SELECT sobre_aviso, salario, id_curso, nome, tipo_insalubridade, qnt_salminimo_insalu, periculosidade_30, horista_plantonista FROM curso WHERE id_curso = '$row_clt[id_curso]'") or die(mysql_error());
//echo "SELECT salario, nome, tipo_insalubridade, qnt_salminimo_insalu, periculosidade_30 FROM curso WHERE id_curso = '$row_clt[id_curso]'";
//@$salario_limpo = mysql_result($qr_curso, 0, 0);
$row_curso = mysql_fetch_assoc($qr_curso);

//if($_COOKIE['logado'] == 87){
///VERIFICA O ALTERAÇÔES SALARIAIS
$qr_teste = mysql_query("SELECT E.id_curso, @var_competencia:='$row_folha[data_inicio]' AS data_competencia, IF(IF((
        SELECT @var_salario_competencia:=G.salario_novo AS salario_competencia
        FROM rh_salario AS G
        WHERE G.status=1 AND G.id_curso=E.id_curso AND DATE_FORMAT(G.data,'%Y-%m')<= DATE_FORMAT(@var_competencia,'%Y-%m')
        ORDER BY G.data DESC
        LIMIT 1),
         @var_salario_competencia,
         (
        SELECT @var_salario_competencia:=I.salario_antigo
        FROM rh_salario AS I
        WHERE DATE_FORMAT(I.data,'%Y-%m')> DATE_FORMAT(@var_competencia,'%Y-%m') AND I.id_curso=E.id_curso AND I.status=1
        ORDER BY DATE_FORMAT(I.data,'%Y-%m-%d') ASC
        LIMIT 1)
        ), @var_salario_competencia,E.salario) AS salario_competencia
        FROM curso AS E
        WHERE E.id_curso = $row_clt[id_curso];");


$row_salario = mysql_fetch_assoc($qr_teste);


//if (mysql_num_rows($qr_teste) != 0) {
//    $salario_limpo = $row_salario['salario_competencia'];
//    
//} else {
//    
//}
$salario_limpo = $row_curso['salario'];

/*
 * APAGAR 
 */
//$salario_limpo = 0;
//}else {
//$salario_limpo  = $row_curso['salario'];   
//}


$tipo_insalubr = $row_curso['tipo_insalubridade'];
$qnt_salInsalu = $row_curso['qnt_salminimo_insalu'];

//echo "Sinesio: " . $qnt_salInsalu;
//BUSCANDO HORARIO DO CLT
$qr_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = '{$row_clt['rh_horario']}'");
$row_horario = mysql_fetch_assoc($qr_horario);
$hora_mensal = $row_horario['horas_mes'];
$adNoturno = $row_horario['adicional_noturno'];
$horas_noturnas = $row_horario['horas_noturnas'];
$percentNoturno = $row_horario['porcentagem_adicional'];


$tipo_falta = array(1 => 'HORAS', 2 => 'DIAS');

// 13º Salário
include('dt.php');

//if($_COOKIE['logado'] == 260){
if ($decimo_terceiro) {
    $dados_rescisao = $objRescisao->getRescisaoFolha($clt, $mes, $ano);
    $num_rescisao = $dados_rescisao['num_rescisao'];

    if (!empty($num_rescisao)) {
        $liquido = 0;
        $meses = 0;
    }
}
//}

if (empty($decimo_terceiro)) {
    /////////////////////////////////////////////////////////
    ////////   RESCISÃO      ///////////////////////////////
    ///////////////////////////////////////////////////////
    $dados_rescisao = $objRescisao->getRescisaoFolha($clt, $mes, $ano);


    $num_rescisao = $dados_rescisao['num_rescisao'];
    if (!empty($num_rescisao)) {

        $dias = $dados_rescisao['dias_saldosalario'];
        $Trab->calculo_proporcional($salario_limpo, $dias);
        $valor_dia = $Trab->valor_dia;  // Variavel para Estatistica do Participante (relatorio.php)

        $salario = $dados_rescisao['salario'];
        $base_inss = $dados_rescisao['base_inss'];
        $base_inss_13_rescisao = $dados_rescisao['base_inss_13_rescisao'];
        $base_irrf = $dados_rescisao['base_irrf'];
        $base_fgts = $dados_rescisao['base_fgts'];
        $base_fgts_sefip += $dados_rescisao['base_fgts_sefip'];
        $inss_rescisao = $dados_rescisao['inss_rescisao'];
        $inss_completo = $dados_rescisao['inss_completo'];
        $irrf_rescisao = $dados_rescisao['irrf_rescisao'];
        $irrf_completo = $dados_rescisao['irrf_completo'];
        $rendimentos = $dados_rescisao['rendimentos'];
        $toRendimentos = $dados_rescisao['total_rendimentos'];

        $descontos = $dados_rescisao['descontos'];

        $toDescontos = $dados_rescisao['total_desconto'];
        $desconto_rescisao = $dados_rescisao['desconto_rescisao'];


        $liquido_rescisao = $dados_rescisao['liquido'];


        $valor_rescisao = $dados_rescisao['total_desconto']; //$dados_rescisao['valor_rescisao'];
        $liquido = 0; //$dados_rescisao['liquido'];

        if (in_array($_COOKIE['logado'], $programadores)) {
//            echo "<pre>";
//               print_r("Base FGTS Sefip: " . $base_fgts_sefip . "<br>");
//            echo "</pre>";
        }
    }
}

//////////////8
/*
  // Rescisão
  if(empty($decimo_terceiro)) {
  include('rescisao.php');
  }

 */


// Quando não for 13º nem Rescisão segue os Cálculos da Folha
if (empty($decimo_terceiro) and empty($num_rescisao)) {

    //////////////////////////////////////////////////////
    ////////////   EVENTOS   /////////////////////////////
    //////////////////////////////////////////////////////

    /*     * ****MÉTODO QUE SUBSTITUIU O INCLUDE ACIMA******** */
    /*     * ****SE DÉ MERDA DESCOMENTA A LINHA DE CIMA******* */

    $mes_referente = $ano . "-" . $mes;
    //OBJETO EVENTO
    $final_folha = $row_folha['data_fim'];
    if (date("d", strtotime(str_replace("/", "-", $final_folha))) != 30) {
        $final_folha = date("Y-m", strtotime(str_replace("/", "-", $final_folha))) . "-30";
    }

    $verificaEvento = $objEvento->validaEventoForFolha($clt, $mes_referente, $row_folha['data_inicio'], $final_folha);
    if ($_COOKIE['logado'] == 179) {
        echo "<pre>";
        print_r($verificaEvento);
        echo "</pre>";
    }

    $inicio = $verificaEvento['dt_inicio'];
    $fim = $verificaEvento['dt_fim'];
    $dias_evento = $verificaEvento['dias_evento'];
    $total_dias_em_evento = $verificaEvento['total_eventos'];
    $msg_15_dias = $verificaEvento['msg_15_dias'];
    $msg_evento = $verificaEvento['msg_evento'];
    $evento = $verificaEvento['evento'];
    $sinaliza_evento = $verificaEvento['sinaliza_evento'];
    $verifica_desconto_licenca = $verificaEvento['desconto_licenca'];
    $listCLtsEventos = $objEvento->getListCLtsEventos();

    //VERIFICAÇÃO PARA QUANDO QUANTIDADE DE DIAS TRABALHADOS FOR 0(ZERO) E LICENÇA DIFERENTE DE MATERNIDADE
    //REMOVER MOVIMENTOS CALCULADOS PELA FOLHA OU PARA COMPETÊNCIA ATUAL.
    //    if($dias <= 0 && $evento != '50'){
    //        $qr_limpa_movimentos = "UPDATE rh_movimentos_clt SET status = '0' 
    //            WHERE id_clt = '{$clt}' AND status = 1 
    //            AND mes_mov = '{$mes}' AND ano_mov = '{$ano}'";
    //
    //        $sql_limpa_movimentos = mysql_query($qr_limpa_movimentos) or die("Erro ao remover movimentos para clt em licença");
    //    }

    /*     * ********************************************************* */
    $eventoCodStatus = (!empty($verificaEvento['cod_status'])) ? $verificaEvento['cod_status'] : $row_evento['cod_status'];

//    $diasMes = cal_days_in_month ( CAL_GREGORIAN , $mes , $ano );


    /**
     * SINESIO LUIX 
     * 26/06/2017
     * VERIFICANDO SE O DESCONTO
     * DE 1 REAL PARA O FUNCIONÁRIO ESTA 
     * ATIVO NO PLANO DE SAUDE SELECIONADO (Assistencia Médica)
     */
    $qryPlanoSaude = "SELECT A.desconto_um_real FROM plano_saude AS A WHERE A.id_plano_saude = '{$row_clt['id_plano_saude']}' AND A.status = 1";
    $sqlPlanoSaude = mysql_query($qryPlanoSaude) or die('Erro ao selecionar plano de saude');
    while ($rowsPlanoSaude = mysql_fetch_assoc($sqlPlanoSaude)) {
        if ($rowsPlanoSaude['desconto_um_real'] == 1 && $eventoCodStatus != 40) {
            $objMovimento->setIdClt($clt);
            $objMovimento->setMes($mes);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov(444);/** 444 - LAGOS PRODUÇÃO * */
            $objMovimento->setCodMov(80091);
            $objMovimento->setLancadoPelaFolha(1);
            $objMovimento->verificaInsereAtualizaFolha(1.00);
        }
    }

    /**
     * SINESIO LUIZ 
     * 27/06/2017
     * VERIFICANDO DESCONTO DE VR
     * NA EMPRESA
     */
    $qryDescontoEmpresaVR = "SELECT A.desconto_um_real_vr FROM rhempresa AS A WHERE A.id_regiao = '{$row_clt['id_regiao']}' AND A.id_projeto = '{$row_clt['id_projeto']}'";
    $sqlDescontoEmpresaVR = mysql_query($qryDescontoEmpresaVR) or die('Erro ao lançar VT');
    while ($rowsDescontoEmpresaVR = mysql_fetch_assoc($sqlDescontoEmpresaVR)) {
        if ($rowsDescontoEmpresaVR['desconto_um_real_vr'] == 1 && $eventoCodStatus != 40) {
            $objMovimento->setIdClt($clt);
            $objMovimento->setMes($mes);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov(201);/** 201 - LAGOS PRODUÇÃO * */
            $objMovimento->setCodMov(10008);
            $objMovimento->setLancadoPelaFolha(1);
            $objMovimento->verificaInsereAtualizaFolha(1.00);
        }
    }

    //////////////////////////////////////////////////
    //////  FÉRIAS ///////////////////////////////////
    //////////////////////////////////////////////////
    //include('ferias.php');                
    /** Método substituto do include de férias */
    $InfoFerias = $objFerias->getFeriasFolha($clt, $row_folha['data_inicio'], $row_folha['data_fim']);
    /**
     * REMOVER ISSO 
     */
    //$InfoFerias = array();
//    $ferias = $InfoFerias['ferias'];
//    $base_inss_ferias = $InfoFerias['base_inss'];
//    $base_fgts_ferias = $InfoFerias['base_fgts'];
//    $inss_ferias = $InfoFerias['inss'];
//    $irrf_ferias = $InfoFerias['irrf'];
//    $fgts_ferias = $InfoFerias['fgts'];
//    $valor_ferias = $InfoFerias['valor_ferias'];
//    $desconto_ferias = $InfoFerias['desconto_ferias'];
//    $dias_ferias = $InfoFerias['dias_ferias'];
//    $aliquota_ferias = $InfoFerias['aliquota'];
//    //$dias_trabalhandos_ferias = $InfoFerias['dias_trabalhandos'];
//
//    $dias_trabalhandos_ferias = 30 - $dias_ferias;



    $ferias = $InfoFerias['ferias'];
    $base_inss_ferias = $InfoFerias['base_inss'];
    $base_fgts_ferias = $InfoFerias['base_fgts'];

    /**
     * NÃO DEIXANDO GRAVAR O CAMPO a5035 QUANDO 
     * FOR O 2° MES DE FERIAS
     * FEITO EM : 02/08/2016
     * POR SINESIO LUIZ
     */
    if (isset($ferias) && ($InfoFerias['mes'] == $InfoFerias['mes_ferias'] && $InfoFerias['ano'] == $InfoFerias['ano_ferias'])) {
        $inss_ferias = $InfoFerias['inss'];
        $irrf_ferias = $InfoFerias['irrf'];

//        $desconto_ferias = $InfoFerias['desconto_ferias'] + $InfoFerias['pensao_ferias'];
        $desconto_ferias = $InfoFerias['desconto_ferias']; // + $InfoFerias['pensao_ferias'];
    } else {
        $inss_ferias = 0;
        $irrf_ferias = 0;
        $InfoFerias['pensao_ferias'] = 0;
        $desconto_ferias = $InfoFerias['valor_ferias'];
    }

    $valor_ferias = $InfoFerias['valor_ferias'];
    $fgts_ferias = $InfoFerias['fgts'];
    $dias_ferias = $InfoFerias['dias_ferias'];
    $aliquota_ferias = $InfoFerias['aliquota'];
    //$dias_trabalhandos_ferias = $InfoFerias['dias_trabalhandos'];


    if ($diasMes == 31 && (isset($ferias) || isset($verificaEvento))) {

        /**
         * 
         */
        $retornoEvento = explode('-', $verificaEvento['data_retorno']);

        /**
         * 
         */
        $inicioEvento = explode('-', $verificaEvento['dt_inicio']);

        /**
         * 
         */
        if (isset($verificaEvento) &&
                //$verificaEvento['data_retorno'] != '0000-00-00' && 
                (
                ($retornoEvento[1] == $mes && $retornoEvento[0] == $ano) ||
                ($inicioEvento[1] == $mes && $inicioEvento[0] == $ano)
                )
        ) {

            $dias_trabalhandos_ferias = 31 - $dias_ferias;
            $total_dias_folha = 31;
        } elseif (isset($verificaEvento) && $verificaEvento['data_retorno'] == '0000-00-00') {
            //echo "<br>Ferrugem<br>";
            $dias_trabalhandos_ferias = 30 - $dias_ferias;
            $total_dias_folha = 30;
        }
    } else if ($diasMes == 28 && (isset($ferias) || isset($verificaEvento))) {

        /**
         * 
         */
        $retornoEvento = explode('-', $verificaEvento['data_retorno']);

        /**
         * 
         */
        $inicioEvento = explode('-', $verificaEvento['dt_inicio']);

        /**
         * 
         */
        if (isset($verificaEvento) &&
                //$verificaEvento['data_retorno'] != '0000-00-00' && 
                (
                ($retornoEvento[1] == $mes && $retornoEvento[0] == $ano) ||
                ($inicioEvento[1] == $mes && $inicioEvento[0] == $ano)
                )
        ) {

            $dias_trabalhandos_ferias = 28 - $dias_ferias;
            $total_dias_folha = 28;
        } elseif (isset($verificaEvento) && $verificaEvento['data_retorno'] == '0000-00-00') {
            //echo "<br>Ferrugem<br>";
            $dias_trabalhandos_ferias = 30 - $dias_ferias;
            $total_dias_folha = 30;
        }
    } else if ($diasMes == 29 && (isset($ferias) || isset($verificaEvento))) {

        /**
         * 
         */
        $retornoEvento = explode('-', $verificaEvento['data_retorno']);

        /**
         * 
         */
        $inicioEvento = explode('-', $verificaEvento['dt_inicio']);

        /**
         * 
         */
        if (isset($verificaEvento) &&
                //$verificaEvento['data_retorno'] != '0000-00-00' && 
                (
                ($retornoEvento[1] == $mes && $retornoEvento[0] == $ano) ||
                ($inicioEvento[1] == $mes && $inicioEvento[0] == $ano)
                )
        ) {

            $dias_trabalhandos_ferias = 29 - $dias_ferias;
            $total_dias_folha = 29;
        } elseif (isset($verificaEvento) && $verificaEvento['data_retorno'] == '0000-00-00') {
            //echo "<br>Ferrugem<br>";
            $dias_trabalhandos_ferias = 30 - $dias_ferias;
            $total_dias_folha = 30;
        }
    } else {

        $dias_trabalhandos_ferias = 30 - $dias_ferias;
        $total_dias_folha = 30;
    }

    // Faltas
    $qr_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND status = '1' AND id_Mov IN(62,232) AND mes_mov = '$mes' AND ano_mov = '$ano'");
    while ($faltas = mysql_fetch_assoc($qr_faltas)) {
        $ids_movimentos_estatisticas[] = $faltas['id_movimento'];
        $ids_movimentos_update_geral[] = $faltas['id_movimento'];
        $dias_faltas += $faltas['qnt'];
        $ids_movimentos_parcial[] = $faltas['id_movimento'];
        $qnt_faltas = $faltas['qnt'];
        $tipo_qnt_faltas += $faltas['tipo_qnt'];
    }

    //////////////////////////////////////////////////
    // Dias Trabalhados, Valor por Dia e Sal?rio
    /////////////////////////////////////////////////
    /////////////////////////////////////////////////////
    // Contratado depois do Início da Folha ////////////
    ////////////////////////////////////////////////////lastEventoFolha


    $dadosDiasTrab = $objCalcFolha->getDiasTrabalhadosFolha($data_inicio, $data_fim, $row_clt['data_entrada'], $dias_evento, $dias_ferias, $dias_trabalhandos_ferias, $total_dias_folha, $total_dias_em_evento);
    $dias = $dadosDiasTrab['dias'];

//    echo "Dias Trabalhados: " . $dias . "<br>";

    $novo_clt = $dadosDiasTrab['novo_clt'];
    $dias_entrada = $dadosDiasTrab['dias_entrada'];


    /* substituido pela classe  
      if ($row_clt['data_entrada'] >= $data_inicio and $row_clt['data_entrada'] <= $data_fim) {

      $inicio = explode('-', $row_clt['data_entrada']);
      $fim = explode('-', $data_fim);

      if ($inicio[1] == '02' and ($inicio[2] == 28 or $inicio[2] == 29)) {
      $dia_inicio = 30;
      } else {
      $dia_inicio = $inicio[2];
      }


      $dias_entrada = (30 - $dia_inicio) + 1;
      $dias = $dias_entrada - $dias_evento - $dias_ferias;
      // Calculando Dias da Entrada
      //$dias_entrada = abs(30 - (int)floor((strtotime($fim) - strtotime($inicio)) / 86400) - 1);

      $novo_clt = 1;
      } else {
      //        if($_COOKIE['logado'] == 158){
      //            echo "debugando";
      //        }
      if ($dias_trabalhandos_ferias == 0) {
      $dias = $total_dias_folha - $dias_evento - $dias_ferias;
      } else {
      $dias = $dias_trabalhandos_ferias;
      }
      }


      if ($dias < 0) {
      $dias = 0;
      }
     */

    $Trab->calculo_proporcional($salario_limpo, $dias);
    $valor_dia = $Trab->valor_dia;
    $salario = $Trab->valor_proporcional;

    $flagSindicato = $objCalcFolha->getAdNoturnoEmSindicato($clt);

    /**
     * @author: Lucas Praxedes
     * REGRA DE LANÇAMENTO DE AJUSTE DE FÉRIAS PRÓXIMO MÊS, REFERENTE A FEVEREIRO.
     */
    $mesFev = 2;
    $anoFev = date("Y");
    $ultimo_diaFev = date("t", mktime(0, 0, 0, $mesFev, '01', $anoFev));

    $diaInicioFerias = explode("-", $InfoFerias['data_ini']);
    $diaInicioFerias = $diaInicioFerias[2];

    if (isset($ferias) && $InfoFerias['mes'] == $InfoFerias['mes_ferias'] && $InfoFerias['ano'] == $InfoFerias['ano_ferias'] && $mes == 2 && $diaInicioFerias == 1) {

        $diffFev = $InfoFerias['dias_ferias_total'] - $ultimo_diaFev;

        if ($diffFev > 0) {
            $feriasMesTodo = true;

            $ajusteFerias = $valor_dia * $diffFev;

            $objMovimento->setIdClt($clt);
            $objMovimento->setMes($mes);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setIdMov(398);
            $objMovimento->setCodMov(80049);
            $objMovimento->setLancadoPelaFolha(1);
            $verifica = $objMovimento->verificaInsereAtualizaFolha($ajusteFerias, '1,2');
        } else if ($diffFev == 0) {
            $feriasMesTodo = true;
        }
    }

    /*    if($_COOKIE['logado'] == 87){   
      /////////////////////////////////
      /////// INSALUBRIDADE ///////////
      /////////////////////////////////
      if ($row_clt['insalubridade'] == 1  and $row_curso['tipo_insalubridade'] != 0 and ($dias - $dias_faltas > 0)) {

      $teste_insalubridade =  $objCalcFolha->getInsalubridade($dias, $tipo_insalubr, $qnt_salInsalu, $ano);
      if($novo_clt == 1 or $ferias == true ){
      $id_mov    = $teste_insalubridade['id_mov_proporcional'];
      $cod_mov   = $teste_insalubridade['cod_mov_proporcional'];
      $valor_mov = $teste_insalubridade['valor_proporcional'];
      }else {
      $id_mov    = $teste_insalubridade['id_mov'];
      $cod_mov   = $teste_insalubridade['cod_mov'];
      $valor_mov = $teste_insalubridade['valor_integral'];
      }

      $objMovimento->setIdClt($clt);
      $objMovimento->setIdMov($id_mov);
      $objMovimento->setCodMov($cod_mov);
      $objMovimento->setMes($mes);
      $objMovimento->setAno($ano);
      $objMovimento->setIdRegiao($regiao);
      $objMovimento->setIdProjeto($projeto);

      $testemov = $objMovimento->verificaMovimento();
      if(empty($testemov['num_rows'])){
      echo 'insere';
      $objMovimento->insereMovimento($valor_mov);
      } else {
      echo '<pre>';
      print_R($testemov);
      echo '</pre>';

      if($testemov['valor_movimento'] != $valor_mov){
      $objMovimento->updateValorPorId($testemov['id_movimento'],$valor_mov);
      }
      }
      unset($id_mov, $valor_mov,$cod_mov );
      }

      } else { */

    //////// INSALUBRIDADE  ////////
    ////////////////////////////////////////////////////////


    if (in_array($_COOKIE['logado'], $array_cagada_folha)) {
        $insalu = 0;
    } else {
        $insalu = $row_clt['insalubridade'];
    }
    if (in_array($_COOKIE['logado'], $programadores)) {
//        echo "<pre>";
//            print_r($row_curso);
//        echo "</pre>";
    }

    //ESSAS FUNÇÕES PAGAM A INSALUBRIDADE 40% SOBRE O SALÁRIO BASE. 
    //DIFERENTE DAS OUTRAS QUE PAGAM SOBRE SALÁRIO MÍNIMO 
    if ($row_curso['nome'] == "SUPERVISOR DE APLICAÇÃO TECNICA RADIOLOGICA" || $row_curso['nome'] == "SUPERVISOR DE APLICAÇÃO TÉCNICA RADIOLÓGICA" || $row_curso['nome'] == "TÉCNICO DE RAIO-X") {
        $curso_especiais[] = $row_curso['id_curso'];
    }

    $insalSobreSalBase = 0;
    if (in_array($row_curso['id_curso'], $curso_especiais)) {
        $insalSobreSalBase = 1;
    }

    //CONDIÇÃO DE INSALUBRIDADE
    // || ($dias_evento > 0 && $eventoCodStatus == 50))
    if ($insalu == 1 and $row_curso['tipo_insalubridade'] != 0 and ( ($dias - $dias_faltas > 0)) || ($dias_evento > 0 && $eventoCodStatus == 50)) {
        /**
         * 29/02/2016
         * PEDIDO DA MARIA DO RH DA LAGOS 
         * ADICIONEI A OPÇÃO DE CALCULO DE INSALUBRIDADE  PARA PESSOAS COM 
         * LICENÇA MATERNIDADE
         */
        //$diasInsa = $dias;
        if ($verificaEvento['cod_status'] == 50) {
            $diasInsa = $dias + $dias_evento;
        } else {
            $diasInsa = $dias;
        }

        $insaProp = 0;
        if ($novo_clt == 1 or $ferias == true or $sinaliza_evento == true) {
            $insaProp = 1;
        }

        $Trab->calculo_proporcional($salario_limpo, $diasInsa);
        $salarioIns = $Trab->valor_proporcional;

        $insalubridade = $objCalcFolha->getInsalubridade($diasInsa, $tipo_insalubr, $qnt_salInsalu, $ano, null, $insalSobreSalBase, $salarioIns, $insaProp);

        if (!$insaProp) {

            $sqlUpdateInsalubridade = "UPDATE rh_movimentos_clt SET STATUS = 0
                                           WHERE id_clt = $clt AND cod_movimento IN (80257,80258) AND ano_mov = $ano AND mes_mov = $mes";
            $queryUpdateInsalubridade = mysql_query($sqlUpdateInsalubridade);
        } else {

            $sqlUpdateInsalubridade = "UPDATE rh_movimentos_clt SET STATUS = 0
                                           WHERE id_clt = $clt AND cod_movimento IN (6006,50251) AND ano_mov = $ano AND mes_mov = $mes";
            $queryUpdateInsalubridade = mysql_query($sqlUpdateInsalubridade);
        }

        $valorInsalubridade = ($insaProp == 1) ? $insalubridade['valor_proporcional'] : $insalubridade['valor_integral'];
        $objMovimento->setIdClt($clt);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($insalubridade['id_mov']);
        $objMovimento->setTipoQuantidade(2);
        $objMovimento->setQuantidade($dias);
        $objMovimento->setCodMov($insalubridade['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $verifica = $objMovimento->verificaInsereAtualizaFolha($valorInsalubridade);


        /* } else {

          //CALCULANDO
          $qnt_dias_insalubridade = $dias;
          $INSALUBRIDADE_ = $CALC_NEW->Calcula_insalubridade($qnt_dias_insalubridade, $tipo_insalubr, $qnt_salInsalu);
          $INFO_MOV = $CALC_NEW->get_info_movimento($INSALUBRIDADE_['cod']);
          $valor_insalubridade = ($novo_clt == 1 or $ferias == true or $sinaliza_evento == true) ? $INSALUBRIDADE_['valor_proporcional'] : $INSALUBRIDADE_['valor_integral'];

          //VERIFICANDO SE FOI LANÇADO
          $verifica_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND id_mov IN('$INSALUBRIDADE_[id_mov]',200) AND ((mes_mov ='$mes' AND ano_mov = '$ano')   OR lancamento = 2 )  AND status = 1");
          $row_verifica_insalu = mysql_fetch_assoc($verifica_insalubridade);

          if (mysql_num_rows($verifica_insalubridade) == 0) {   //SE não existir o movimento de insalubridade lançado, aqui  adiciona
          mysql_query("INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia, status, status_reg, tipo_qnt, qnt)
          VALUES
          ('$clt','$regiao','$projeto','$mes','$ano','{$INFO_MOV['id_mov']}','{$INFO_MOV['cod']}','{$INFO_MOV['categoria']}','{$INFO_MOV['nome']}',NOW(),'{$_COOKIE[logado]}','{$valor_insalubridade}','{$INSALUBRIDADE_['percentual']}','1','5020,5021,5023',1, 1, 2,'$dias')") or die(mysql_error());
          } elseif ($valor_insalubridade != ((float) $row_verifica_insalu['valor_movimento'])) { //caso o valor calculado seja diferente do que está gravado no sistema
          mysql_query("UPDATE rh_movimentos_clt SET valor_movimento = '{$valor_insalubridade}' WHERE id_movimento = '{$row_verifica_insalu['id_movimento']}' AND id_clt = '{$clt}' LIMIT 1");
          }

          } */
    } else {
        //echo "aqui";
        //CONDIÇÂO PARA REMOVER A INSALUBRUIDADE CASO SEJ DESMARCAR NO CADASTRO DE CLT
        $qr_verifica_insalubridade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                    WHERE id_clt = '$clt' 
                                                                    AND id_mov IN (56,235,200) AND lancamento = 1 AND ano_mov = '$ano'
                                                                    AND mes_mov = '$mes' AND status = 1 ");
        $row_insalubridade = mysql_fetch_assoc($qr_verifica_insalubridade);
        $verifica_insalubridade = mysql_num_rows($qr_verifica_insalubridade);
        if ($verifica_insalubridade != 0) {
            mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
        }
    }


    /**
     * LANÇAMENTO DE SOBRE AVISO
     */
    if ($row_curso['sobre_aviso'] == 1) {
        $valorSobreAviso = (($row_curso['salario'] * 0.3333) / 30) * $diasInsa;
        $objMovimento->setIdClt($clt);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov(395);
        $objMovimento->setCodMov(10013);
        $objMovimento->setLancadoPelaFolha(1);
        $verifica = $objMovimento->verificaInsereAtualizaFolha($valorSobreAviso);
    }

    //fim INSALUBRIDADE
    //    }
    //////////////////////////////////
    //////// PERICULOSIDADE  ////////
    ////////////////////////////////

    if (in_array($_COOKIE['logado'], $array_cagada_folha)) {
        $pericu = 0;
    } else {
        $pericu = $row_curso['periculosidade_30'];
    }

    if ($pericu == 1) {

        /**
         * #2979
         * SINESIO LUIZ     
         * 31/05/2017
         */
        $basePericulosidade = 0;
//        if ($verificaEvento['cod_status'] == 50) {
//            $basePericulosidade = $salario_limpo;
//            $diasPericulosidade = 30;
//        } else {
//            $basePericulosidade = $salario;
//            $diasPericulosidade = $dias;
//        }

        $basePericulosidade = $salario;
        $diasPericulosidade = $dias;

        $pericProp = 0;
        if ($novo_clt == 1 or $ferias == true or $sinaliza_evento == true) {
            $pericProp = 1;
        }

        if ($_COOKIE['logado'] == 299) {
            echo 'folha';
            echo '<pre>';
            print_r($basePericulosidade);
            echo '</pre>';
            //exit();
        }
        $periculosidade = $objCalcFolha->getPericulosidade($basePericulosidade, $diasPericulosidade, null, $pericProp);

        if ($pericProp) {

            $sqlUpdatePericulosidade = "UPDATE rh_movimentos_clt SET STATUS = 0
                                           WHERE id_clt = $clt AND cod_movimento IN (6007) AND ano_mov = $ano AND mes_mov = $mes";
        } else {

            $sqlUpdatePericulosidade = "UPDATE rh_movimentos_clt SET STATUS = 0
                                           WHERE id_clt = $clt AND cod_movimento IN (80259) AND ano_mov = $ano AND mes_mov = $mes";
        }
        $queryUpdatePericulosidade = mysql_query($sqlUpdatePericulosidade);

        $objMovimento->setIdClt($clt);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setTipoQuantidade(2);
        $objMovimento->setQuantidade($dias);
        $objMovimento->setIdMov($periculosidade['id_mov']);
        $objMovimento->setCodMov($periculosidade['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $verifica = $objMovimento->verificaInsereAtualizaFolha($periculosidade['valor_proporcional']);
    } else {
        ////VERIFICA SE EXISTE OU NÃO O MOVIMENTO DE INSALUBRIDADE E ADICIONA CASO NÃO TENHA
        $qr_veri_periculosidade = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                    WHERE id_clt = '$clt' 
                                                                    AND id_mov IN (57) AND lancamento = 1 AND ano_mov = '$ano'
                                                                    AND mes_mov = '$mes' AND status = 1 ");
        $row_insalubridade = mysql_fetch_assoc($qr_veri_periculosidade);
        $verifica_perculosidade = mysql_num_rows($qr_veri_periculosidade);
        if ($verifica_perculosidade != 0) {
            mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_insalubridade[id_movimento]' LIMIT 1");
        }
    }
    ///FIM PERICULOSIDADE
    /////////////////////////////////////////////////////////
    /////////////// ADICIONAL NOTURNO ////////////////////
    ///////////////////////////////////////////////////////

    if ($_COOKIE['debug'] == 666) {
        echo "<pre>";
        print_r($flagSindicato);
        echo "</pre>";
    }

    if ($flagSindicato['flagAdNoturno'] == 1) {

        if ($adNoturno == 1 && $horas_noturnas > 0) {

            $arrAdNoturno = [
                406 => 80056,
                454 => 80093,
                403 => 80053,
                404 => 80054,
                405 => 80055];

            if ($percentNoturno == 0.20) {
                $adKey = 406;
            } else if ($percentNoturno == 0.30) {
                $adKey = 454;
            } else if ($percentNoturno == 0.35) {
                $adKey = 403;
            } else if ($percentNoturno == 0.40) {
                $adKey = 404;
            } else if ($percentNoturno == 0.50) {
                $adKey = 405;
            }

            $idMovAdNoturno = $adKey;
            $codMovAdNoturno = $arrAdNoturno[$adKey];

            $valorAdicional = $objCalcFolha->getMovAdNoturno($clt, $idMovAdNoturno, $horas_noturnas, 1);
            $hNot = "$horas_noturnas:00:00";

            $objMovimento->setIdClt($clt);
            $objMovimento->setIdFolha($folha);
            $objMovimento->setMes($mes);
            $objMovimento->setAno($ano);
            $objMovimento->setIdRegiao($regiao);
            $objMovimento->setIdProjeto($projeto);
            $objMovimento->setTipoQuantidade(1);
            $objMovimento->setQuantidade($dias);
            $objMovimento->setQuantidadeHoras($hNot);
            $objMovimento->setIdMov($idMovAdNoturno);
            $objMovimento->setCodMov($codMovAdNoturno);
            $objMovimento->setLancadoPelaFolha(1);

            if ($valorAdicional > 0) {
                $objMovimento->verificaInsereAtualizaFolha($valorAdicional);
            }

            unset($arrAdNoturno[$adKey]);
            $arrAdNoturno = array_flip($arrAdNoturno);

            $objMovimento->removeMovimento($clt, $folha, $arrAdNoturno);
        } else {
            $objMovimento->removeMovimento($clt, $folha, [403, 404, 405, 406, 454]);
        }
    } else {
        $objMovimento->removeMovimento($clt, $folha, [403, 404, 405, 406, 454]);
    }


    if ($dias <= 0) {
        /*         * ************LIMPANDO MOVIMENTOS QUANDO A QUANTIDADE DE DIAS TRABALHADO FOR ZERO************** */
//        $limpandoMovimento = "UPDATE rh_movimentos_clt SET status = '0' 
//                WHERE id_clt = '{$clt}' AND mes_mov = '{$mes}' 
//                AND ano_mov = '{$ano}' AND id_mov IN(66,199) AND status = '1'";
//        $sqlLimpaMovimentos = mysql_query($limpandoMovimento) or die("Erro ao limpar movimentos");

        if ($_COOKIE['logado'] == 179) {
            //echo "Não pode Calcular Ad.Noturno e DSR<br />";
        }
    }


    // Definindo Variáveis importantes para Base de Cálculos
    $base = $salario;
    $base_inss = $salario;
    $base_irrf = $salario;
    $base_fgts = $salario;

    ///////////////////////////
    // SALÁRIO MATERNIDADE/////
    ///////////////////////////        
    $mater = $dias_evento;
    $naoLancarMovimentos = 0;
    if ($eventoCodStatus == 50 and $mater != 0) {

        /**
         * Criar rubrica de Salário Maternidade
         * Solicito que seja criado a Rubrica de Salário Maternidade para ser informado em valor.
         * #2942
         */
        if ($row_curso['horista_plantonista'] == 1) {
            $salario_maternidade = 0;
            $naoLancarMovimentos = 1;
        } else {
            $salario_maternidade = $valor_dia * $dias_evento;
        }

        $movimentos_rendimentos += $salario_maternidade;
        $base_inss += $salario_maternidade;
        /**
         * By Ramon 08/09/2016
         * A pedido de Michele modificando base de IRRF, pois o caso da Luana do ADM esta errado 
         * pois esta considerando uma parte do salario, e tem q considerar tudo
         * ESTAVA ASSIM: $base_irrf += $salario_maternidade - $salario;
         * MODIFIQUEI P: $base_irrf += $salario_maternidade;
         */
        $base_irrf += $salario_maternidade;
        $base_fgts += $salario_maternidade - $salario;

        //Média dos ultimos 6 meses
        $media_mov = $objCalcFolha->getMediaMovimentos($clt, $mes, $ano, 6);
        $objMovimento->setIdClt($clt);
        $objMovimento->setIdMov(262);
        $objMovimento->setCodMov(50258);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setLancadoPelaFolha(1);


        if ($media_mov['total_media'] > 0) {
            $verifica = $objMovimento->verificaInsereAtualizaFolha($media_mov['total_media']);
        }
        unset($cod_mov, $mov, $media_mov);
    }
    //Pegar salario base quando for afastamento militar
//    else if($eventoCodStatus == 30 and $mater != 0){
//        $base_fgts = $salario_limpo;
//    }
    // Movimentos que não incidem no Salário Base
    $movimentos_base = array('7003', '8006', '9500');

    // Movimentos Proporcionais
    $movimentos_proporcionais = array('6006', '6007', '8004', '9000');

    /**
     * CONDIÇÃO PARA LICENÇAS  
     * (VALOR LICENÇA)  
     * IMPLEMENTANDO OBJMOVIMENTO PARA 
     * LANÇAMENTO DA RUBRICA
     * 17/05/2017 - SINÉSIO LUIZ
     */
    if ($verifica_desconto_licenca and $dias_evento > 0 and $eventoCodStatus != 50) {

        $valor_mov = $valor_dia * $dias_evento;

        $objMovimento->setIdClt($clt);
        $objMovimento->setIdMov(259);
        $objMovimento->setCodMov(10011);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setLancadoPelaFolha(1);
        $verifica = $objMovimento->verificaInsereAtualizaFolha($valor_mov);
    }

    $gratificacao_funcao = $objCalcFolha->getGratificacaoFuncao($clt);
    if ($gratificacao_funcao['valor_integral'] > 0) {

        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($gratificacao_funcao['id_mov']);
        $objMovimento->setCodMov($gratificacao_funcao['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->verificaInsereAtualizaFolha($gratificacao_funcao['valor_integral']);
    } else {

        $objMovimento->removeMovimento($clt, $folha, [$gratificacao_funcao['id_mov']]);
    }

    $quebra_caixa = $objCalcFolha->getQuebraCaixa($clt);
    if ($quebra_caixa['valor_integral'] > 0) {

        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($quebra_caixa['id_mov']);
        $objMovimento->setCodMov($quebra_caixa['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->verificaInsereAtualizaFolha($quebra_caixa['valor_integral']);
    } else {

        $objMovimento->removeMovimento($clt, $folha, [$quebra_caixa['id_mov']]);
    }

    $ajuda_custo = $objCalcFolha->getAjudaCusto($clt);
    if ($ajuda_custo['valor_integral'] > 0) {

        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($ajuda_custo['id_mov']);
        $objMovimento->setCodMov($ajuda_custo['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->verificaInsereAtualizaFolha($ajuda_custo['valor_integral']);
    } else {

        $objMovimento->removeMovimento($clt, $folha, [$ajuda_custo['id_mov']]);
    }

    $ad_tempo_servico = $objCalcFolha->getAdTempoServico($clt);
    if ($ad_tempo_servico['valor_integral'] > 0) {
        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setPorcentagem($ad_tempo_servico['porcentagem']);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($ad_tempo_servico['id_mov']);
        $objMovimento->setCodMov($ad_tempo_servico['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->verificaInsereAtualizaFolha($ad_tempo_servico['valor_integral']);
    } else {
        $objMovimento->removeMovimento($clt, $folha, [$ad_tempo_servico['id_mov']]);
    }

    $ad_cargo_confianca = $objCalcFolha->getAdCargoConfianca($clt);
    if ($ad_cargo_confianca['valor_integral'] > 0) {
        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setPorcentagem($ad_cargo_confianca['porcentagem']);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($ad_cargo_confianca['id_mov']);
        $objMovimento->setCodMov($ad_cargo_confianca['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->verificaInsereAtualizaFolha($ad_cargo_confianca['valor_integral']);
    } else {
        $objMovimento->removeMovimento($clt, $folha, [$ad_cargo_confianca['id_mov']]);
    }

    $ad_transferencia = $objCalcFolha->getAdTransferencia($clt);
    if ($ad_transferencia['valor_integral'] > 0) {
        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setPorcentagem($ad_transferencia['porcentagem']);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($ad_transferencia['id_mov']);
        $objMovimento->setCodMov($ad_transferencia['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->verificaInsereAtualizaFolha($ad_transferencia['valor_integral']);

        unset($ad_transferencia['ids'][$ad_transferencia['id_mov']]);
        $objMovimento->removeMovimento($clt, $folha, $ad_transferencia['ids']);
    } else {

        $objMovimento->removeMovimento($clt, $folha, $ad_transferencia['ids']);
    }
    
    $risco_vida = $objCalcFolha->getRiscoVida($clt);
    if ($risco_vida['valor_integral'] > 0) {
        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setPorcentagem($risco_vida['porcentagem']);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov($risco_vida['id_mov']);
        $objMovimento->setCodMov($risco_vida['cod_mov']);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->verificaInsereAtualizaFolha($risco_vida['valor_integral']);
    } else {
        $objMovimento->removeMovimento($clt, $folha, [$risco_vida['id_mov']]);
    }

    /*
     * ALTERAÇÃO PARA NÃO PEGAR OS MOVIMENTOS DO TIPO SEMPRE NAS FÉRIAS
     * ATERAÇÃO DE QUANDO TIVER LICENÇA
     * if(((isset($dias_ferias) and $dias_ferias < 30) or !isset($dias_ferias)) and $sem_mov_sempre != true ){ 
     */
    $movi_valores = $objMovimento->getMovimentosFolhaAberta($clt, $regiao, $mes, $ano, $dias_ferias, $dias, $sinaliza_evento, $naoLancarMovimentos);
    $ids_movimentos_update_geral = $objMovimento->ids_movimentos_update_geral;
    $ids_movimentos_estatisticas = $objMovimento->ids_movimentos_estatisticas;
    $ids_movimentos_parcial = $movi_valores['ids_movimentos_parcial'];
    $ids_movimentos_update_individual = $movi_valores['ids_movimentos_update_individual'];
    $movimentos_rendimentos += $movi_valores['total_rendimento'];
    $movimentos_descontos += $movi_valores['total_desconto'];
    $movimentosClt = $movi_valores['movimentos'];
    $base_inss += $movi_valores['base_inss'];
    $base_irrf += $movi_valores['base_irrf'];
    $base_fgts += $movi_valores['base_fgts'];
    $base_dsr = $movi_valores['base_dsr'];

//    if ($_COOKIE['logado'] == 299) {
//        echo '/////////////////////////////MOVIMENTOS//////////////////////////////';
//        echo '<pre>';
//        print_r($movi_valores);
//        echo '</pre>';
//    }

    if ($base_dsr > 0) {

        $dsr = $Calc->getDsr($base_dsr, $dias, $mes, $ano, $projeto);

        $objMovimento->setIdClt($clt);
        $objMovimento->setIdFolha($folha);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setIdMov(199);
        $objMovimento->setCodMov(9997);
        $objMovimento->setLancadoPelaFolha(1);
        $verifica = $objMovimento->verificaInsereAtualizaFolha($dsr['valor_integral']);
    } else {
        $objMovimento->removeMovimento($clt, $folha, [199]);
    }

    if ($_COOKIE['logado'] == 260) {
//        echo "BASE INSS FÉRIAS: {$base_inss_ferias} BASE INSS: {$base_inss}";
    }

    ////////////////////////////////////////
    //////// CÁLCULO DO INSS ///////////////
    ////////////////////////////////////////   
    //echo "Base INSS Ferias: " . $base_inss_ferias . "<br>";
    if ($base_inss < 0) {
        $base_inss = 0;
    }

    if ($base_fgts < 0) {
        $base_fgts = 0;
    }

    /**
     * @author Lucas Praxedes - 06/04/2017
     * REGRA DE LANÇAMENTO DE "AJUSTE DE SALDO DEVEDOR MES ANTERIOR"
     */
    $mesAjuste = $mes;
    $anoAjuste = $ano;

    if ($mesAjuste == 1) {
        $mesAjuste = 12;
        $anoAjuste--;
    } else if ($mesAjuste > 1) {
        $mesAjuste--;
    }


    $sqlGetAjuste = "SELECT * FROM rh_movimentos_clt WHERE cod_movimento = 80019 AND mes_mov = $mesAjuste AND ano_mov = $anoAjuste AND status > 0 AND id_clt = $clt";
    $queryGetAjuste = mysql_query($sqlGetAjuste);
    $rowsGetAjuste = mysql_num_rows($queryGetAjuste);

    if ($rowsGetAjuste > 0) {
        $arrGetAjuste = mysql_fetch_assoc($queryGetAjuste);
        $valorAjusteMesAnterior = $arrGetAjuste['valor_movimento'];

        $objMovimento->setIdClt($clt);
        $objMovimento->setIdMov(261);
        $objMovimento->setCodMov(50257);
        $objMovimento->setMes($mes);
        $objMovimento->setAno($ano);
        $objMovimento->setIdRegiao($regiao);
        $objMovimento->setIdProjeto($projeto);
        $objMovimento->setLancadoPelaFolha(1);
        $objMovimento->setTipoQuantidade(0);

        $ajusteMesAnterior = abs($valorAjusteMesAnterior);

        if ($ajusteMesAnterior > 0 && !($row_folha['id_folha'] == 2752 && ($clt == 9899 || $clt == 9942))) {
            $verifica = $objMovimento->verificaInsereAtualizaFolha($ajusteMesAnterior);
        }
    }

    if (isset($ferias)) {

        /*
         * PARA NÃO ENTRAR NAS FÉRIAS QUANDO FOR FÉRIAS QUEBRADAS
         * POIS JÁ ENTROU NO MÊS INICIAL DAS FÉRIAS
         */
        if ($InfoFerias['mes_ferias'] == $InfoFerias['mes'] && $InfoFerias['ano_ferias'] == $InfoFerias['ano']) {
            $base_inss += $base_inss_ferias;
        }

        $base_fgts += $base_inss_ferias;
        $Calc->MostraINSS($base_inss, $data_inicio);
    } else {
        $Calc->MostraINSS($base_inss, $data_inicio);
    }

    /**
     * 20/06/2017
     * SINESIO LUIZ 
     * CRIANDO RUBRICAS DE CONTRIBUIÇÃO PREVIDENCIÁRIA
     */
    $objMovimento->setIdClt($clt);
    $objMovimento->setIdMov(436);
    $objMovimento->setCodMov(80087);
    $objMovimento->setMes($mes);
    $objMovimento->setAno($ano);
    $objMovimento->setIdRegiao($regiao);
    $objMovimento->setIdProjeto($projeto);
    $objMovimento->setLancadoPelaFolha(1);
    $objMovimento->verificaInsereAtualizaFolha($base_inss);


    $query_verifica_teto_ferias = "SELECT A.teto
        FROM rh_movimentos AS A
        WHERE A.anobase = '{$InfoFerias['ano']}' AND A.cod = 5020 AND '{$base_inss_ferias}' BETWEEN A.v_ini AND A.v_fim AND
        '{$data_inicio}' BETWEEN A.data_ini AND A.data_fim";
    $sql_verifica_teto_ferias = mysql_query($query_verifica_teto_ferias) or die('Erro para verificar teto');
    $dados_ferias = mysql_fetch_assoc($sql_verifica_teto_ferias);
    $teto_ferias = $dados_ferias['teto'];


    if (($InfoFerias['mes_ferias'] == $InfoFerias['mes'] && $InfoFerias['ano_ferias'] == $InfoFerias['ano']) && $inss_ferias >= $teto_ferias && (!empty($inss_ferias) && !empty($teto_ferias))) {
        $inss = 0;
        //echo "aqui:9";
    } else {
        $inss = $Calc->valor;
        //echo "aqui:10";
    }


    if ($InfoFerias['mes_ferias'] == $InfoFerias['mes'] && $InfoFerias['ano_ferias'] == $InfoFerias['ano'] && $inss > 0) {
        $inss = $Calc->valor - $InfoFerias['inss'];
    }

    $percentual_inss = (int) substr($Calc->percentual, 2);
    $faixa_inss = $Calc->percentual;
    $teto_inss = $Calc->teto;
    //$teto_inss = 608.44;
//    if($_COOKIE['logado'] == 179){
//        echo "<pre>";
//            echo "INSS Completo DENTRO DO CALCULOS<br>";
//            print_r($Calc);exit();
//            print_r($teto_inss);exit();
//            print_r($inss_completo);exit();
//        echo "</pre>";
//    } 
    ////////////////////////////////////////////
    ///CONDIÇÃO PARA LICENÇAS (AJUSTE)   //////
    //////////////////////////////////////////
//    if ($verifica_desconto_licenca and $dias_evento > 0 and $eventoCodStatus != 50) {
    /* $objMovimento->setIdClt($clt); 
      $objMovimento->setIdMov(258);
      $objMovimento->setCodMov(10010);
      $objMovimento->setMes($mes);
      $objMovimento->setAno($ano);
      $objMovimento->setIdRegiao($regiao);
      $objMovimento->setIdProjeto($projeto);
      $objMovimento->setLancadoPelaFolha(1);
      $verifica = $objMovimento->verificaMovimento();
      if(empty($verifica['num_rows'])){
      $objMovimento->insereMovimento($valor_mov);

      } else {
      if($verifica['valor_movimento'] != $valor_mov){
      $objMovimento->updateValorPorId($verifica['id_movimento'],$valor_mov);
      }
      }
      unset($cod_mov, $mov,$media_mov);
     */
//        $id_mov = 258;
//        $valor_mov = ($dias_evento == 30) ? ($base_inss - $inss) : ($valor_dia * $dias_evento);
//        $verifica_mov = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_mov IN({$id_mov}) AND mes_mov = '$mes' AND ano_mov = '$ano' AND id_clt = '$clt' AND status = 1 ");
//        $row_verifica = mysql_fetch_assoc($verifica_mov);
//        if (mysql_num_rows($verifica_mov) == 0) {
//            mysql_query("INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,lancamento,incidencia, status, status_reg, qnt) 
//                                            VALUES 
//                                            ('$row_clt[id_clt]','$regiao','$projeto','$mes','$ano','{$id_mov}','{$INF_MOVIMENTOS[$id_mov]['cod']}','{$INF_MOVIMENTOS[$id_mov]['categoria']}','{$INF_MOVIMENTOS[$id_mov]['descicao']}',NOW(),'$_COOKIE[logado]','$valor_mov','1','',1, 1, '{$dias_evento}')") or die('erro');
//
//            $ultimo_id = mysql_insert_id();
//            $ids_movimentos_estatisticas[] = $ultimo_id;
//            $ids_movimentos_parcial[] = $ultimo_id;
//            $ids_movimentos_update_individual[] = $ultimo_id;
//            $movimentos_descontos += $valor_mov;
//        } else {
//            if (number_format($valor_mov, 2, '', '') != number_format($row_verifica['valor_movimento'], 2, '', '')) {
//                mysql_query("UPDATE rh_movimentos_clt SET valor_movimento = '{$valor_mov}' WHERE id_movimento = '{$row_verifica['id_movimento']}' LIMIT 1 ");
//                $movimentos_descontos += $valor_mov;
//            }
//        }
//        unset($ultimo_id, $valor_mov, $verifica_mov, $id_mov);
//    }

    if ($_COOKIE['logado'] == 299 && $_COOKIE['debug'] == 1) {
        echo '<pre>';
        print_r(["DESCONTO INSS: {$row_clt['desconto_inss']}", "TIPO DE DESCONTO INSS: {$row_clt['tipo_desconto_inss']}", "VALOR DESCONTO: {$row_clt['desconto']}"]);
        echo '</pre>';
    }

    /* DESCONTO INSS OUTRA EMPRESA */
    if ($base_inss != 0) {
        if ($row_clt['desconto_inss'] == '1') {
            if ($row_clt['tipo_desconto_inss'] == 'isento') {
                $inss = 0;
            } elseif ($row_clt['tipo_desconto_inss'] == 'parcial') {

                if (($row_clt['desconto'] + $inss) > $teto_inss) {
                    $inss = $teto_inss - $row_clt['desconto'];
                    $vInssParcial = number_format($row_clt['desconto'], 2, ',', '.');
                    if ($inss < 0 || $inss < 0.02) {
                        $inss = 0;
                    }
                }
            }
        }
    }

    ////////////////////////////////////////
    //////// CÁLCULO DO IRRF ///////////////
    ////////////////////////////////////////
    $base_irrf -= $inss;

    if ($base_irrf > 0) {

        $Calc->MostraIRRF($base_irrf, $clt, $projeto, $data_inicio);

        $irrf = $Calc->valor;
        $percentual_irrf = str_replace('.', ',', $Calc->percentual * 100);
        $faixa_irrf = $Calc->percentual;
        $fixo_irrf = $Calc->valor_fixo_ir;
        $ddir = $Calc->valor_deducao_ir_total;
        $filhos_irrf = $Calc->total_filhos_menor_21;


        $deducao_irrf = $ddir;
        $irrf_completo = $irrf;
    } else {
        $base_irrf = 0;
    }

    /**
     * 21/06/2017
     * SINESIO LUIZ 
     * TOTAL DA BASE DE CÁLCULO DO IRRF
     */
    $objMovimento->setIdClt($clt);
    $objMovimento->setIdMov(438);
    $objMovimento->setCodMov(80089);
    $objMovimento->setMes($mes);
    $objMovimento->setAno($ano);
    $objMovimento->setIdRegiao($regiao);
    $objMovimento->setIdProjeto($projeto);
    $objMovimento->setLancadoPelaFolha(1);
    $objMovimento->verificaInsereAtualizaFolha($base_irrf);


    //CONDIÇÃO DE PENSÃO ALIMENTICIA
    if (!empty($row_clt['pensao_alimenticia']) && $row_clt['pensao_alimenticia'] > 0) {
        //echo "SELECT * FROM rh_movimentos_clt WHERE id_mov IN (54,367,223,366,63,326,365,364,227,396) AND status = 1 AND id_clt = $clt AND mes_mov = $mes AND ano_mov = $ano";
        $sqlPA = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_mov IN (54,367,223,366,63,326,365,364,227,396) AND status = 1 AND id_clt = $clt AND mes_mov = $mes AND ano_mov = $ano");
        $numPA = mysql_num_rows($sqlPA);
        if ($numPA == 0) {
            //echo "SELECT * FROM rh_movimentos WHERE id_mov IN (54, 63, 223, 365) AND percentual = '{$row_clt['pensao_alimenticia']}' LIMIT 1";
            $pensaoAlimenticia = mysql_fetch_assoc(mysql_query("SELECT * FROM rh_movimentos WHERE id_mov IN (54,367,223,366,63,326,365,364,227,396) AND percentual = '{$row_clt['pensao_alimenticia']}' LIMIT 1"));
            $valorPensaoAlimenticia = ($base_inss - $inss - $irrf) * $row_clt['pensao_alimenticia'];
            $sqlPensaoAlimenticia = "INSERT INTO rh_movimentos_clt (
                id_clt,
                id_regiao,
                id_projeto,
                mes_mov,
                ano_mov,
                id_mov,
                cod_movimento,
                tipo_movimento,
                nome_movimento,
                data_movimento,
                user_cad,
                valor_movimento,
                percent_movimento,
                lancamento,
                incidencia,
                tipo_qnt,
                qnt, 
                STATUS, 
                status_reg, 
                lancado_folha
                ) VALUES (
                '$clt',
                '$regiao',
                '$projeto',
                '$mes',
                '$ano',
                '{$pensaoAlimenticia['id_mov']}',
                '{$pensaoAlimenticia['cod']}',
                '{$pensaoAlimenticia['categoria']}',
                '{$pensaoAlimenticia['descicao']}',
                NOW(),
                '{$_COOKIE['logado']}',
                '$valorPensaoAlimenticia',
                '{$pensaoAlimenticia['percentual']}',
                '1',
                '{$pensaoAlimenticia['incidencia']}',
                '',
                '',
                '1',
                '1', 
                '1');";
            $sqlPensaoAlimenticia = mysql_query($sqlPensaoAlimenticia);
        } else {
            $sqlPA = mysql_fetch_assoc($sqlPA);

            $pensaoAlimenticia = mysql_fetch_assoc(mysql_query("SELECT * FROM rh_movimentos WHERE id_mov IN (54,367,223,366,63,326,365,364,227,396) AND percentual = '{$row_clt['pensao_alimenticia']}' LIMIT 1;"));
            $valorPensaoAlimenticia = ($base_inss - $inss - $irrf) * $row_clt['pensao_alimenticia'];
            //echo "UPDATE rh_movimentos_clt SET id_mov = '{$pensaoAlimenticia['id_mov']}', cod_movimento = '{$pensaoAlimenticia['cod']}', tipo_movimento = '{$pensaoAlimenticia['categoria']}', nome_movimento = '{$pensaoAlimenticia['descicao']}', data_movimento = NOW(), user_cad = '{$_COOKIE['logado']}', valor_movimento = '$valorPensaoAlimenticia', percent_movimento = '{$pensaoAlimenticia['percentual']}' WHERE id_movimento = {$sqlPA['id_movimento']} LIMIT 1";
            $sqlPensaoAlimenticia = mysql_query("UPDATE rh_movimentos_clt SET id_mov = '{$pensaoAlimenticia['id_mov']}', cod_movimento = '{$pensaoAlimenticia['cod']}', tipo_movimento = '{$pensaoAlimenticia['categoria']}', nome_movimento = '{$pensaoAlimenticia['descicao']}', data_movimento = NOW(), user_cad = '{$_COOKIE['logado']}', valor_movimento = '$valorPensaoAlimenticia', percent_movimento = '{$pensaoAlimenticia['percentual']}' WHERE id_movimento = {$sqlPA['id_movimento']} LIMIT 1;");
        }
    } else {
        //CONDIÇÂO PARA REMOVER A INSALUBRUIDADE CASO SEJ DESMARCAR NO CADASTRO DE CLT
        $qr_verifica_pensao_alimenticia = mysql_query("SELECT * FROM rh_movimentos_clt 
                                                                    WHERE id_clt = '$clt' 
                                                                    AND id_mov IN (54,367,223,366,63,326,365,364,227) AND lancamento = 1 AND ano_mov = '$ano'
                                                                    AND mes_mov = '$mes' AND status = 1 ");
        $row_pensao_alimenticia = mysql_fetch_assoc($qr_verifica_pensao_alimenticia);
        $verifica_pensao_alimenticia = mysql_num_rows($qr_verifica_pensao_alimenticia);
        if ($verifica_pensao_alimenticia != 0) {
            mysql_query("UPDATE rh_movimentos_clt SET status = 0 WHERE id_movimento = '$row_pensao_alimenticia[id_movimento]' LIMIT 1");
        }
    }



    //$inss_completo = $inss;

    if ($inss_completo < 0) {
        $inss_completo = 0;
    }



    /*
     * CONDIÇÃO PARA PESSOAS EM SERVIÇO MILITAR
     * POIS ENTRA FGTS
     */
    if ($eventoCodStatus == 30 || $eventoCodStatus == 70) {
        $base_fgts = $salario_limpo;
    }


    /**
     * 21/06/2017
     * SINESIO LUIZ     
     * TOTAL DA BASE DE CÁLCULO DO FGTS
     */
    $objMovimento->setIdClt($clt);
    $objMovimento->setIdMov(437);
    $objMovimento->setCodMov(80088);
    $objMovimento->setMes($mes);
    $objMovimento->setAno($ano);
    $objMovimento->setIdRegiao($regiao);
    $objMovimento->setIdProjeto($projeto);
    $objMovimento->setLancadoPelaFolha(1);
    $objMovimento->verificaInsereAtualizaFolha($base_fgts);
//     
//    echo "<pre>";
//        echo "BASE FGTS : <br>";
//        print_r($base_fgts);
//    echo "</pre>";

    /*
     *  CÁLCULO DO FGTS  
     */
    $fgts = $base_fgts * 0.08;
    $fgts_completo = $fgts + $fgts_ferias;

    /**
     * CÁLCULO DO SALÁRIO FAMÍLIA 
     * DE ACORDO COM A DILEANE, SE A PESSO NÃO TEM DIAS DE SALDO DE TRABLAHO, ELA NÃO TEM DIREITO DE SALARIO FAMILIA
     * REMOVI DA LINHA ABAIXO O CRITÉRIO QUE BUSCAVA SOMENTE LICENÇA MÉDICA $row_evento['cod_status'] == 20 and

      [16:41:38] Dileane Salvadori: funciona assim
      [16:42:00] Dileane Salvadori: os casos que sao proporcionais de que tem direito ao salario familia este sim
      [16:42:11] Dileane Salvadori: mas no caso em que a pessoa esta em condição total não
     *
     */
    if ($dias == 0) { //condição para não pagar salario familia quando estiver sob licença medica e não tiver dias trabalhados
        $cont_salfamilia = 0;
    } else {
        $cont_salfamilia = 1;
    }

    if (empty($decimo_terceiro) and $dias_ferias != 30 and $cont_salfamilia == 1) {

        /**
         * Mudando a base do salario Familia ($salario_limpo)
         * para o Salario Proporcional
         */
        $base_familia = ($salario + $movimentos_rendimentos) - $familia_mes_anterior;

        if (!empty($row_clt['id_antigo'])) {
            $referencia_familia = $row_clt['id_antigo'];
        } else {
            $referencia_familia = $row_clt['id_clt'];
        }

        /**
         * CALCULO DO 
         * SALARIO FAMILIA
         */
        $salaFamiliaCLT = $Calc->Salariofamilia($base_familia, $referencia_familia, $projeto, $data_inicio, $row_clt['tipo_contratacao']);
        $filhos_familia = $salaFamiliaCLT['filhos_menores'];
        $familia = $salaFamiliaCLT['valor'];
        $fixo_familia = $salaFamiliaCLT['fixo'];

        if ($_COOKIE['logado'] == 179) {
            echo "Base Familia ->" . $base_familia . "<br>";
            echo "Filhos Menos ->" . $filhos_familia . "<br>";
            echo "Valor Salario Familia ->" . $familia . "<br>";
        }

        if ($dias > 0) {

            if ($familia > 0) {
                $verificaMovFamilia = mysql_query("Select * FROM rh_movimentos_clt WHERE id_clt = '{$clt}' AND cod_movimento = 5022 AND mes_mov = '{$mes}' AND ano_mov = '{$ano}' AND status > 0");
                $numRowsMovFamilia = mysql_num_rows($verificaMovFamilia);
                $sqlInsterFamilia = "INSERT INTO rh_movimentos_clt (
                                    id_clt,
                                    id_regiao,
                                    id_projeto,
                                    mes_mov,
                                    ano_mov,
                                    id_mov,
                                    cod_movimento,
                                    tipo_movimento,
                                    nome_movimento,
                                    data_movimento,
                                    user_cad,
                                    valor_movimento,
                                    percent_movimento,
                                    lancamento,
                                    incidencia,
                                    tipo_qnt,
                                    qnt, 
                                    status, 
                                    status_reg, 
                                    lancado_folha, 
                                    id_folha, 
                                    legenda 
                                )VALUES (
                                    '{$clt}',
                                    '{$regiao}',  
                                    '{$projeto}', 
                                    '{$mes}',
                                    '{$ano}', 
                                    '{$salaFamiliaCLT['id_mov']}', 
                                    '{$salaFamiliaCLT['cod_mov']}', 
                                    'CREDITO',
                                    'SALARIO FAMILIA',
                                    NOW(),
                                    '{$_COOKIE[logado]}', 
                                    '{$familia}', 
                                    '',
                                    '1',
                                    '',
                                    '',
                                    '',
                                    1,
                                    1,
                                    '1',
                                    '{$folha}',
                                    '')";

                if ($numRowsMovFamilia == 0) {
                    mysql_query($sqlInsterFamilia) or die('Erro ao inserir salario familia');
                }
            } else {

                $queryDeleteFamilia = "DELETE FROM rh_movimentos_clt 
                         WHERE id_clt = '{$clt}' AND cod_movimento = 5022  
                         AND mes_mov = '{$mes}' AND ano_mov = '{$ano}'";

                mysql_query($queryDeleteFamilia) or die('Erro ao remover');
            }
        } else {

            $queryDeleteFamilia = "DELETE FROM rh_movimentos_clt 
                         WHERE id_clt = '{$clt}' AND cod_movimento = 5022  
                         AND mes_mov = '{$mes}' AND ano_mov = '{$ano}'";

            mysql_query($queryDeleteFamilia) or die('Erro ao remover');

            $filhos_familia = 0;
            $familia = 0;
            $fixo_familia = 0;
        }
    }

    ///////////////////////////////////////////////////
    ////////VALE TRANSPORTE (DÉBITO) /////////////////
    //////////////////////////////////////////////////
    if (in_array($_COOKIE['logado'], $array_cagada_folha)) {
        $transporte = 0;
    } else {
        $transporte = $row_clt['transporte'];
    }


    if ($transporte == '1' and empty($decimo_terceiro) and $regiao != '10' and $row_clt['status'] != 20 and $row_clt['status'] != 50) {


        /* $qr_vale_transporte = mysql_query("SELECT vale.valor_total_func
          FROM rh_vale_r_relatorio vale
          INNER JOIN rh_vale_protocolo protocolo
          ON vale.id_protocolo = protocolo.id_protocolo
          WHERE vale.id_func = '$clt'
          AND vale.valor_total_func != ''
         * 
          AND protocolo.mes = '$mes'
          AND protocolo.ano = '$ano'");



          @$vale_transporte = mysql_result($qr_vale_transporte,0);


          if($_COOKIE['logado'] == 87 ){

          //   $verifica_vale = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$clt' AND mes_mov = '$mes' AND ano_mov = '$ano'  AND")



          $qr_vale_transporte = mysql_query("SELECT B.valor FROM rh_vale as A
          LEFT JOIN  rh_tarifas as B
          ON A.id_tarifa1 = B.id_tarifas
          WHERE A.id_clt = '$clt'");

          @$vale_transporte = mysql_result($qr_vale_transporte,0);
          //echo $salario_limpo .'<br>';


          }
          $limite_transporte = $salario_limpo * 0.06;

          if($vale_transporte > $limite_transporte) {
          $vale_transporte = $limite_transporte;
          }

         */

        /**
         * By Ramon 20/04/2017
         * Buscar valor diario na tabela rh_clt, calcular dias uteis no mes, verificar se é maior q 6% do salario e definir o valor de desconto
         */
        $legendaVT = formato_real($salario_limpo) . " x 6%";
        if ($row_clt['vt_valor_diario'] > 0) {
            $diasUteis = diasUteis($row_folha['mes'], $row_folha['ano']);
            //$diasUteis = (isset($diasUteisFolha)) ? $diasUteisFolha : 2;

            $vale_transporteDias = $diasUteis * $row_clt['vt_valor_diario'];
            $vale_transportePerc = $salario_limpo * 0.06;

            //VERIFICA QUEM É MAIOR, DIAS OU PERCENTUAL
            if ($vale_transporteDias > $vale_transportePerc) {
                $vale_transporte = $vale_transportePerc;            //O VALOR PELO CALCULO DE DIAS SENDO MAIOR, PREVALECE OS 6%
            } else {
                $vale_transporte = $vale_transporteDias;            //O PERCENTUAL SENDO MAIOR, PREVALECE O Q REALMENTE É GASTO
                $legendaVT = "Dias uteis $diasUteis x {$row_clt['vt_valor_diario']} valor diário";
            }
        } else {
            //SE NÃO ESTIVER PREENCHIDO VALOR DIARIO CALCULA SOMENTE OS 6%
            $vale_transporte = $salario_limpo * 0.06;
        }


        //VERIFICA SE TRABALHOU OS 30 DIAS 
        if (!$feriasMesTodo) {
            if ($dias < 30) {
                $vale_dia = $vale_transporte / 30;
                $vale_transporte = round($vale_dia * $dias, 2);
            }
        }

        //POR SEGURANÇA, ZERAR SE DIAS FOR <= 0 OU VALOR VOR MENOR Q 0
        if ($dias <= 0 || $vale_transporte < 0) {
            $vale_transporte = 0;
        }

        ///ALTERADO DIA 28/03/2013
        /* if (!$feriasMesTodo) {
          $vale_transporte = $salario_limpo * 0.06;
          //VERIFICA SE TRABALHOU OS 30 DIAS
          if ($dias < 30) {
          $vale_dia = $vale_transporte / 30;
          $vale_transporte = round($vale_dia * $dias, 2);
          }
          } */

        if ($row_clt['id_regiao'] == 45) {
            $vale_transporte = 0;
        }
    }



    ///////////////////////////////////////////////////
    //////// CONTRIBUIÇÃO SINDICAL ////////////////////
    //////////////////////////////////////////////////

    $rhSindicado = $row_clt[rh_sindicato];
    if ($rhSindicado < 0) {
        $rhSindicado = 0;
    }

//    if($_COOKIE['logado'] == 179){
//        echo "<br><br>";
//        echo "CONTRIBUICAO SINDICAL<br>";
//        echo "row_clt['id_projeto']: {$row_clt['id_projeto']} | mes_int: {$mes_int} | rhSindicado: {$rhSindicado} | row_clt['ano_contribuicao']: {$row_clt['ano_contribuicao']} | ano: {$ano} | dias_evento: {$dias_evento} | eventoCodStatus: {$eventoCodStatus} | sindicato: {$sindicato} | valor_dia: {$valor_dia}";
//    }

    $dataEntrada = $row_clt['data_entrada'];
    $dataEntradaMaisUmMes = date('Y-m-d', strtotime(" +1 month", strtotime($dataEntrada)));
    $monthPosEntrada = explode('-', $dataEntradaMaisUmMes);

    $lancaContribuicaoSind = 0;
    if ($monthPosEntrada[1] == $mes && $monthPosEntrada[0] == $ano) {
        $lancaContribuicaoSind = 1;
    }

//    echo "MES Folha: " . $mes . "<br>";
//    echo "Mes entrada: " . $monthPosEntrada[1] . "<br>";
//    echo "Lança contribuição: " . $lancaContribuicaoSind . "<br>"; 
    //$row_clt['id_projeto']==3353 && 

    if (($mes_int == 3) || ($lancaContribuicaoSind == 1)) {
        //exit('vasco');

        if ($_COOKIE['logado'] == 260) {
            echo "<br><br>";
            echo "IF1";
            echo "<br><br>";
        }

        if ($row_clt['id_projeto'] == 3353 && $mes_int == 3) {

            if ($_COOKIE['logado'] == 260) {
                echo "<br><br>";
                echo "IF2";
                echo "<br><br>";
            }

            if (!empty($rhSindicado) and $row_clt['ano_contribuicao'] != $ano) {
                if ($_COOKIE['logado'] == 260) {
                    echo "<br><br>";
                    echo "IF3";
                    echo "<br><br>";
                }

                $sindicato = $valor_dia;
            }
        } else {

            if ($_COOKIE['logado'] == 260) {
                echo "<br><br>";
                echo "IF4";
                echo "<br><br>";
            }

            //and !isset($ferias)
            $qr_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '{$rhSindicado}'");
            $row_sindicato = mysql_fetch_array($qr_sindicato);

//            if($_COOKIE['logado'] == 179){
//                echo "********************************************************************<br>";
//                echo "SELECT * FROM rhsindicato WHERE id_sindicato = '{$rhSindicado}'<br>";
//                echo "mes_int: {$mes_int}<br>";
//                echo "mes_desconto: {$row_sindicato['mes_desconto']}<br>";
//                echo "novo_clt: {$novo_clt}<br>";
//                echo "********************************************************************<br>";
//            }

            /* VERIFICA O MÊS DE CONTRIBUIÇÃO E SE NÃO É O MÊS DE ADMISSÃO 
             * CASO SEJAM ATENDIDAS,  VERIFICA SE FOI DESCONTADO A CONTRIBUIÇÃO, INCLUINDO-A CASO NÃO TENHA DESCONTADO
             */
            if ($mes_int >= $row_sindicato['mes_desconto'] && $novo_clt != 1) {

                if ($_COOKIE['logado'] == 260) {
                    echo "<br><br>";
                    echo "IF5";
                    echo "<br><br>";
                }

                /*
                 * @jacques - 29/03/2016
                 * Condição específica adicionada a pedido de Gimenez para o projeto UPA BEBEDOURO que não possui 
                 * sindicato definido ainda, mas precisa haver o desconto sindical
                 */

                $verifica_ctrSindical = mysql_query("SELECT  a5019 FROM rh_folha_proc WHERE id_clt = {$clt} AND status = 3 AND ano={$ano} AND a5019 != '0.00' AND a5019 IS NOT NULL;");

                if (mysql_num_rows($verifica_ctrSindical) == 0 || $row_clt['id_projeto'] == 3353) {

                    if ($_COOKIE['logado'] == 260) {
                        echo "<br><br>";
                        echo "IF6";
                        echo "<br><br>";
                    }

                    $sindicato = $valor_dia;
                }
            }
        }

        /*
         * 30/03/2016 - Jacques
         * Adição de rotina para inclusão de movimento sindical
         */

        $sQueryV = "
            SELECT 
                id_movimento,
                valor_movimento
            FROM rh_movimentos_clt
            WHERE status
                AND id_clt = {$clt}
                AND cod_movimento='5019'
                AND mes_mov = {$mes} 
                AND ano_mov = {$ano}
            ";
        $rs = mysql_query($sQueryV);

        $row = mysql_fetch_assoc($rs);

        /*
         * VERIFICA SE JA TEVE DESCONTO SINDICAL
         * NO ANO DA FOLHA, COM EXCESSÃO DO MÊS DA FOLHA
         */
        $qryV = "SELECT *
            FROM rh_movimentos_clt AS A
            WHERE A.cod_movimento = 5019 AND A.ano_mov = {$ano} AND A.status > 0 AND A.mes_mov = {$mes} AND A.id_clt = {$clt}";
        $sqlV = mysql_query($qryV) or die(mysql_error());
        $totV = mysql_num_rows($sqlV);
        $arrV = mysql_fetch_assoc($sqlV);

        //echo "Total de linhas: " . $totV;

        if ((mysql_num_rows($totV) == 0) || ($arrV['mes_mov'] != $mes && $arrV['ano_mov'] == $ano)) {
//            if($_COOKIE['logado'] == 179){
//                echo "<br><br>";
//                echo "IF7";
//                echo "<br><br>";
//            }

            if ((!$row && !empty($rhSindicado) and $row_clt['ano_contribuicao'] != $ano) || ($lancaContribuicaoSind == 1 && $totV == 0)) {
//                if($_COOKIE['logado'] == 179){
//                    echo "<br><br>";
//                    echo "IF8";
//                    echo "<br><br>";
//                }

                $sQuery = "INSERT INTO rh_movimentos_clt (
                    id_clt,
                    id_regiao,
                    id_projeto,
                    mes_mov,
                    ano_mov,
                    id_mov,
                    cod_movimento,
                    tipo_movimento,
                    nome_movimento,
                    data_movimento,
                    user_cad,
                    valor_movimento,
                    percent_movimento,
                    lancamento,
                    incidencia,
                    status, 
                    status_folha, 
                    status_reg
                    ) VALUES (
                    '$clt',
                    '$regiao',
                    '$projeto',
                    '$mes',
                    '$ano',
                    21,
                    '5019',
                    'DEBITO',
                    'CONTRIBUIÇÃO SINDICAL',
                    NOW(),
                    '{$_COOKIE['logado']}',
                    {$sindicato},
                    '0.00',
                    '1',
                    ',,',
                    1,
                    1,
                    1)";
                mysql_query($sQuery);
            } else if ((!empty($rhSindicado) and $row_clt['ano_contribuicao'] != $ano) || ($lancaContribuicaoSind == 1 && $totV > 0)) {
                if ($_COOKIE['logado'] == 260) {
                    echo "<br><br>";
                    echo "IF9";
                    echo "<br><br>";
                }

                $sQuery = "
                            UPDATE rh_movimentos_clt 
                                SET valor_movimento = {$sindicato}
                            WHERE id_movimento = {$row['id_movimento']} 
                          ";
                mysql_query($sQuery);
            }
        } else {
            
        }


//        if($_COOKIE['logado'] == 179){
//            echo "QUERY Sindical: " . $sQuery;
//        }
        //mysql_query($sQuery);
        //COLOQUEI ESSA POHHA PRA NÃO PRINTAR A CONTRIBUIÇÃO NA MÃO LÁ, MAS SIM PRA PUXAR DO MOVIMENTO.
        $sindSaveRhProc = $sindicato;
        $sindicato = 0;

        ////////////////
    }



    // Rendimentos
    $rendimentos = $movimentos_rendimentos + $valor_ferias;

    // Descontos
    $descontos = $movimentos_descontos + $desconto_ferias + $vale_refeicao + $vale_transporte + $sindicato;

//    if($_COOKIE['logado'] == 179){
//        echo "Movintos Desconto: " . $movimentos_descontos . "<br>";
//        echo "Desconto Férias: " . $desconto_ferias . "<br>";
//        echo "Vale Refeição: " . $vale_refeicao . "<br>";
//        echo "Vale Transporte: " . $vale_transporte . "<br>";
//        echo "Sindicato: " . $sindicato . "<br>";
//    }
//    
//    if($_COOKIE['logado'] == 179){
//        echo "*************************01/06/2015*****************************<br />";
//        echo "Descontos: " . $descontos . "<br />";
//        echo "Movimentos desconto: " . $movimentos_descontos . "<br />";
//        echo "Desconto de Ferias: " . $desconto_ferias . "<br />";
//        echo "Vale Refeição: " . $vale_refeicao . "<br />";
//        echo "Vale transporte: " . $vale_transporte . "<br />";
//        echo "Sindicato: " . $sindicato . "<br />";
//        echo "*****************************************************************<br />";
//    }

    if ($inss < 0.02) {
        $inss = 0;
    }

    $inss_completo = $inss + $inss_ferias;



//    if($inss_completo > 513.01){
//        $inss_completo = 513.01;
//    }

    if ($inss_completo > $teto_inss) {
        $inss_completo = $teto_inss;

        /*
         * FEITO PARA DESCONTAR O INSS ATÉ O TETO
         * POIS ESTAVA ULTRAPASSANDO O TETO
         * QUANDO SOMAVA O INSS DE FÉRIAS + INSS DE FOLHA
         */
        if (($InfoFerias['mes_ferias'] == $InfoFerias['mes'] && $InfoFerias['ano_ferias'] == $InfoFerias['ano'])) {
            if ($inss_ferias > $inss) {
                $inss = $teto_inss - $inss_ferias;
            } else {
                $inss = $teto_inss - $inss;
            }
        }
    }

//    if($_COOKIE['logado'] == 179){
//        echo "<pre>";
//            echo "INSS Completo DENTRO DO CALCULOS<br>";
//            print_r($inss_completo);exit();
//        echo "</pre>";
//    } 
//    if($inss > 513.01){
//        $inss = 513.01;
//    }
    if ($inss > $teto_inss) {
        $inss = $teto_inss;
    }

    if ($inss_completo < 0) {
        $inss_completo = 0;
    }

    if ($inss < 0) {
        $inss = 0;
    }


    $irrf_completo = $irrf + $irrf_ferias;



    ///////////////////////////////////////////////////////////////////////////
    /////////////////////////PENSAO ALIMENTICIA///////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
//    if(isset($row_clt['pensao_alimenticia']) && !empty($row_clt['pensao_alimenticia'])){
//        
//        //VERIFICANDO SE EXISTE PENSAO COM OUTRA PENCENT JA LANÇADA
//        $query_veri_lancamento  = "SELECT * FROM  rh_movimentos_clt AS A WHERE A.id_clt = '{$clt}' AND A.mes_mov = '{$mes}' AND A.ano_mov = '{$ano}' AND A.cod_movimento IN(6004,50222,7009) AND A.`status` = '1'";
//        $sql_veri_lancamento    = mysql_query($query_veri_lancamento) or die("Erro ao verificar lançamentos de pensao");
//        $dados_lancamento       = mysql_fetch_assoc($sql_veri_lancamento); 
//        
//        //SELECIONANDO DADOS DO MOVIMENTOS PARA LANÇAMENTOS
//        $query_ver_pensao       = "SELECT * FROM rh_movimentos WHERE cod IN(6004,50222,7009) AND percentual = '{$row_clt['pensao_alimenticia']}'";
//        $sql_ver_pensao         = mysql_query($query_ver_pensao) or die("Erro ao selecionar pensao");
//        $dados_pensao           = mysql_fetch_assoc($sql_ver_pensao);
//        $valor_pensao           = ($salario + $salario_maternidade + $rendimentos - $descontos - $inss_completo - $irrf_completo) * $row_clt['pensao_alimenticia'];
//        
//        //OBJETO DE INSERÇÃO DE MOVIMENTOS
//        $objMovimento->setIdClt($clt); 
//        $objMovimento->setMes($mes);
//        $objMovimento->setAno($ano);
//        $objMovimento->setIdRegiao($regiao);
//        $objMovimento->setIdProjeto($projeto);   
//        $objMovimento->setIdMov($dados_pensao['id_mov']);
//        $objMovimento->setCodMov($dados_pensao['cod']);               
//        $objMovimento->setLancadoPelaFolha(1);
//        
//        if($dados_lancamento['percent_movimento'] != $row_clt['pensao_alimenticia']){
//            //REMOVENDO TODOS OS MOVIMENTOS DE PENSÃO, CASO TROQUEM NO CADASTRO DO CLT A PORCENTAGEM DO MOVIMENTO
//            $query_rev_movs_pensao = "DELETE from rh_movimentos_clt WHERE id_clt = '{$clt}' AND mes_mov = '{$mes}' AND ano_mov = '{$ano}' AND cod_movimento IN(6004,50222,7009) AND status IN(0,1)";
//            $sql_rev_movs_pensao = mysql_query($query_rev_movs_pensao) or die ("Erro ao remover movimentos de pensao");
//            
//            if($sql_rev_movs_pensao){
//                //METODO DE CADASTRO OU EDIÇÃO
//                //a merda esta aqui
//                //$dados = $objMovimento->verificaInsereAtualizaFolha($valor_pensao);    
//            }
//        }
//    }
    // Salário Liquido
    if (empty($ferias)) {

        ////CONDIÇÃO PARA QUEM TIVER SOB LICENÇA MÉDICA PARA ZERAR O TOTAL LIQUIDO,
        //POIS QUANDO SE ESTÁ DE LICENÇA, A EMPRESA NÃO PAGA E SIM O INSS,
        //PORÉM PRECISA CONSTAR NO SEFIP POR TEM QUE VIR NA FOLHA PARA EFEITO DE INFORMAÇÃO
        //OBS. Não possui desconto de IRRF
        /*     if($row_clt['status'] == 20){




          $irrf_completo = 0;
          $irrf = 0;
          $descontos = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
          $liquido   =   $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;


          $verifica_mov_licenca = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_mov = 233 AND mes_mov = $mes  AND ano_mov= $ano  AND id_clt = '$clt' AND status = 1");

          if(mysql_num_rows($verifica_mov_licenca) == 0){


          $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = 233");
          $row_mov = mysql_fetch_assoc($qr_mov);



          mysql_query("INSERT INTO rh_movimentos_clt   (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia, status, status_reg)
          VALUES
          ('$clt','$regiao','$projeto','$mes','$ano','$row_mov[id_mov]','$row_mov[cod]','$row_mov[categoria]','$row_mov[descicao]',NOW(),'$_COOKIE[logado]','$descontos','','1','',1, 1) ");
          $ultimo_id = mysql_insert_id();
          $ids_movimentos_estatisticas[]      = $ultimo_id;
          $ids_movimentos_parcial[]           = $ultimo_id;
          $ids_movimentos_update_individual[] = $ultimo_id;
          unset($ultimo_id);
          }




          }else {
          $liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
          }
         * */

        //$liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo + $familia;
        $valorPositivo = $salario + $rendimentos; // + $familia;
        $valorNegativo = $descontos + $inss_completo + $irrf_completo;
        $liquido = $valorPositivo - $valorNegativo;
    } else {

        if ($dias_ferias > 30) {
            $dias_ferias = 30;
        }

        if ($dias_ferias == 0) {
            $liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo; // + $familia;
        } elseif ($dias_ferias <= 30 and $regiao == 48) {

            $liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo; // + $familia;
        } elseif ($dias_ferias <= 30) {

            $liquido = $salario + $rendimentos - $descontos - $inss_completo - $irrf_completo; // + $familia;
        }
    }

    /**
     * FEITO POR: SINESIO LUIZ
     * FEITO EM: 22/09/2016
     * COMENTANDO AÇÃO DE ZERAR O LIQUIDO 
     * QUANDO O MESMO FOR NEGATIVO - 
     * A PEDIDO DE MICHELE E FABIO SOUZA
     * 
     * if($liquido < 0){
     *    $liquido = 0;
     * }
     */
    $validandoLiquido = (string) $liquido;
    if ($validandoLiquido == '-2.2737367544323E-13') {
        $liquido = 0;
    }

    /**
     * FEITO POR MAX
     * FEITO EM: 26/09/2016
     * ADICIONANDO -0 NA VALIDAÇÃO
     * TEVE UM CASO COM -0
     * A PEDIDO DA MICHELE
     */
//    if ($_COOKIE['logado'] == 299) {
//        echo '------------------------LIQUIDO FINAL----------------------------';
//        echo '<pre>';
//        print_r($liquido);
//        echo '</pre>';
//     
//           
//    }

    if ($liquido < 0 && $liquido > -1) {

        $liquido = 0;
    }

    if ($validandoLiquido == '-1.4210854715202E-13') {
        $liquido = 0;
    }

    /**
     * ZERANDO VARIAVEIS
     */
    unset($novo_clt, $verifica_desconto_licenca);
}

$sqlUpdateFolhaProcAt = "UPDATE rh_folha_proc SET a5019 = '$sindSaveRhProc' WHERE id_clt = $clt AND id_folha = '{$row_folha['id_folha']}'";
$queryUpdateContSind = mysql_query($sqlUpdateFolhaProcAt);


