<?php 


/*
 * Totalizadores Clt
 */

$total['valor_aviso'] = array('linha' => 0,
                              'grupo' => 0,
                              'geral' => 0);

$total['dt_salario'] = array('linha' => 0,
                              'grupo' => 0,
                              'geral' => 0);

$total['terceiro_exercicio'] = array('linha' => 0,
                                     'grupo' => 0,
                                     'geral' => 0);

$total['terceiro_ss'] = array('linha' => 0,
                              'grupo' => 0,
                              'geral' => 0);

$total['ferias_pr'] = array('linha' => 0,
                            'grupo' => 0,
                            'geral' => 0);

$total['ferias_vencidas'] = array('linha' => 0,
                            'grupo' => 0,
                            'geral' => 0);

$total['ferias_aviso_indenizado'] = array('linha' => 0,
                            'grupo' => 0,
                            'geral' => 0);

$total['fv_dobro'] = array('linha' => 0,
                            'grupo' => 0,
                            'geral' => 0);

$total['um_terco_ferias_dobro'] = array('linha' => 0,
                            'grupo' => 0,
                            'geral' => 0);

$total['umterco_ferias_aviso_indenizado'] = array('linha' => 0,
                            'grupo' => 0,
                            'geral' => 0);

$total['lei_12_506'] = array('linha' => 0,
                            'grupo' => 0,
                            'geral' => 0);


/*
 * Totalizadores empresa
 */

$total['pis'] = array('linha' => 0,
                      'grupo' => 0,
                      'geral' => 0);

$total['fgts_multa'] = array('linha' => 0, 
                             'grupo' => 0, 
                             'geral' => 0);

$total['inss_empresa'] = array('linha' => 0, 
                               'grupo' => 0,
                               'geral' => 0);

$total['inss_terceiro'] = array('linha' => 0,
                                'grupo' => 0,
                                'geral' => 0);
$total['fgts_recolher'] = array('linha' => 0,
                                'grupo' => 0,
                                'geral' => 0);

/*
 * Totalizadores Gerais
 */

$total['ferias_a_pagar'] = array('linha' => 0,
                                       'grupo' => 0,
                                       'geral' => 0);

$total['decimo_a_pagar'] = array('linha' => 0,
                                 'grupo' => 0,
                                 'geral' => 0);

$total['terco_constitucional'] = array('linha' => 0,
                                       'grupo' => 0,
                                       'geral' => 0);



if (isset($_REQUEST['mostrar_prov_trab']) && $num_rows > 0) { 

?>
    <p style="text-align: left; margin-top: 20px" class="imprime">
<!--                        <input type="submit" name="exportar_xls" value="Exportar para Excel" class="exportarExcel">
        <input type="hidden" name="modelo_xls" value="mostrar_prov_trab">-->
        <input type="button" id="exportarExcel" name="exportarExcel" value="Exportar para Excel">
        <input type="hidden" id="data_xls" name="data_xls" value=""> 
    </p>    


    <input type="hidden" name="id_rescisao_lote" value="<?= $_REQUEST['id_rescisao_lote'] ?>">
    <input type="hidden" name="projeto_oculto" value="<?= $_REQUEST['projeto_oculto'] ?>">
    <h3><?php echo $projeto['nome'] ?></h3>    
    <!--<p>Total de participantes: <b><?php //echo $total_participantes["total_participantes"];    ?></b></p>-->
    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto; border: 0px;"> 
    <!--                        <thead>
            <tr style="height: 30px; background: #fff; border: 0px;">
                <td colspan="11" class="area-xpandir-1"><span class="xpandir"></span></td>
                <td colspan="1" class="area-xpandir-2"><div class="area"></div></td>
                <td colspan="1" class="area-xpandir-3"><span class="xpandirr"></span></td>
                <td colspan="1" class="area-xpandir-4"><div class="areaa"></div></td>
                <td colspan="1" class="area-xpandir-5"><span class="xpandirrr"></span></td>
                <td colspan="1" class="area-xpandir-6"><div class="areaaa"></div></td>
            </tr>
        </thead>-->
        <?php

        $status = 0;

        while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

            $mov = array();
            $total_movimentos = array();
            $movimentos_incide = 0;
            $query_movimento_recisao = "SELECT A.id_mov, A.id_rescisao, A.id_clt, A.id_movimento, A.valor, TRIM(A.tipo) as tipos, B.incidencia_inss 
                FROM tabela_morta_movimentos_recisao_lote AS A 
                LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod)
                WHERE A.id_clt = {$row_rel['id_clt']} AND A.id_rescisao = '{$row_rel['id_recisao']}'";
            $sql_movimento_recisao = mysql_query($query_movimento_recisao) or die("Erro ao selecionar movimentos de rescisao");

            while ($rows_movimentos = mysql_fetch_assoc($sql_movimento_recisao)) {
                $mov[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']][$rows_movimentos['tipos']][$rows_movimentos['id_movimento']]["valor"] = $rows_movimentos['valor'];
                if ($rows_movimentos['tipos'] == "CREDITO" && $rows_movimentos['incidencia_inss'] == '1') {
                    $movimentos_incide += $rows_movimentos['valor'];
                }
                if ($rows_movimentos['tipos'] == "DEBITO") {
                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] += $rows_movimentos['valor'];
                } else if ($rows_movimentos['tipos'] == "CREDITO") {
                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] += $rows_movimentos['valor'];
                }
            }

            /////////////////////
            // MOVIMENTOS FIXOS /////
            ///////////////////

            $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
        FROM rh_folha as A
        INNER JOIN rh_folha_proc as B
        ON A. id_folha = B.id_folha
        WHERE B.id_clt   = '{$row_rel['id_clt']}'  AND B.status = 3 AND A.terceiro = 2 
        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

            $movimentos = 0;
            $total_rendi = 0;

            while ($row_folha = mysql_fetch_assoc($qr_folha)) {
                if (!empty($row_folha[ids_movimentos_estatisticas])) {

                    $movimentos = "SELECT *
               FROM rh_movimentos_clt
               WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = '{$row_rel['id_clt']}' AND id_mov NOT IN(56,200) ";
                    $qr_movimentos = mysql_query($movimentos);

                    while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                        $movimentos += $row_mov['valor_movimento'];
                    }
                }
            }

//                        echo "<pre>";
//                            print_r($movimentos);
//                        echo "</pre>";

            if ($movimentos > 0) {
                $total_rendi = $movimentos / 12;
            } else {
                $total_rendi = 0;
            }


            ///////////////////////////////////////////////
            ////////// CÁLCULO DE INSS ////////////////////
            ///////////////////////////////////////////////

            // 09/11/2015 - Desativado esse calculo, pois segundo Miltom o calculo certo é o debaixo.
            // $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];

            $base_saldo_salario = $row_rel['saldo_salario'] + $movimentos_incide + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];

            $data_exp = explode('-', $row_rel['data_demi']);

            if ($base_inss > 0) {
                $calculos->MostraINSS($base_inss, implode('-', $data_exp));
                $inss_saldo_salario = $calculos->valor;
                $percentual_inss = $calculos->percentual;

                if ($row_rel['desconto_inss'] == 1) {
                    if ($row_rel['desconto_outra_empresa'] + $inss_saldo_salario > $calculos->teto) {
                        $inss_saldo_salario = ($calculos->teto - $row_rel['desconto_outra_empresa'] );
                    }
                }
            } else {
                $base_saldo_salario = 0;
            }

            //CALCULO IRRF
            $irrf = 0;
            $base_irrf = $base_saldo_salario - $inss_saldo_salario;
            $calculos->MostraIRRF($base_irrf, $row_rel['id_clt'], $row_rel['id_projeto'], implode('-', $data_exp));

            $inss_recolher = $folha->getInssARecolher($row_rel['id_clt']);
            $class = ($cont++ % 2 == 0) ? "even" : "odd";

            if ($status != $row_rel["codigo"]){

                $total_recisao_nao_paga += $total_liquido;

                if($status){

                    footer('provisao_trabalhista',$total,'grupo','Total Parcial:',0);

                }    

                $status = $row_rel["codigo"];


                ?>
                <thead>
                    <tr>
                        <th colspan="3" class="cabecalho_compactar"><?php echo "<p style='text-transform:uppercase; text-align:left' > » " . $row_rel['especifica'] . " - " . $row_rel['aviso'] . "</p>"; ?></th>
                        <th colspan="15">Verbas Rescisórias</th>
                        <!--<th colspan="6">Deducões</th>-->
                        <th style="background: #fff; border: 0px;" ></th>
                        <th colspan="5">EMPRESA</th>
                    </tr>
                    <tr style="font-size:10px !important;">
                        <th rowspan="2">ACÃO</th>
                        <th rowspan="2">ID</th>
                        <th rowspan="2"><span class="numero_rescisao">[11]</span>NOME</th>

                        <th rowspan="2">VALOR AVISO</th>  

                        <!--DISCRIMINACÃO DAS VERBAS RESCISÓRIAS--->

                        <th rowspan="2" ><span class="numero_rescisao">[63]</span>13º Salário Proporcional</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[64]</span>13º Salário Exercício</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[70]</span>13º Salário (Aviso-Prévio Indenizado)</th> 
                        <th rowspan="2" ><span class="numero_rescisao">[65]</span>Férias Proporcionais</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[]</span>1/3 DE FERIAS PROPORCIONAL </th> 
                        <th rowspan="2" ><span class="numero_rescisao">[66]</span>Ferias Vencidas Per. Aquisitivo</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS VENCIDAS</th> 
                        <th rowspan="2" ><span class="numero_rescisao">[68]</span>Terco Constitucional de Ferias</th>  


                        <th rowspan="2" ><span class="numero_rescisao">[71]</span>Ferias (Aviso-Prévio Indenizado)</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[72]</span>Ferias em dobro</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[73]</span>1/3 ferias em dobro</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[82]</span> 1/3 DE FERIAS AVISO INDENIZADO </th>
                        <th rowspan="2" ><span class="numero_rescisao">[95]</span>Lei 12.506</th>  

                        <th rowspan="2" ><span class="numero_rescisao">[69]</span>Aviso Prévio indenizado</th>

                        <!--DEDUCOES--->

                        <!--
                        <th rowspan="2" ><span class="numero_rescisao">[102]</span>Adiantamento de 13º Salário</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[103]</span>Aviso-Prévio Indenizado</th>  
                         <th rowspan="2" ><span class="numero_rescisao">[112.2]</span>Previdencia Social - 13º Salário</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[114.2]</span>IRRF sobre 13º Salário</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[115.2]</span>Adiantamento de 13º Salário</th>
                        <th rowspan="2" ><span class="numero_rescisao">[116]</span>IRRF Férias</th>  
                        -->

                        <!-- DETALHES IMPORTANTES --->
                        <!--BASES -->

                        <!--EMPRESA-->
                        <th rowspan="2" style="background: #fff; border: 0px;"></th>   
                        <th rowspan="2">PIS</th>   
                        <th rowspan="2">MULTA DE 50% DO FGTS</th>   
                        <th colspan="2">INSS A RECOLHER</th>  
                        <th rowspan="2">FGTS A RECOLHER</th>

                    </tr>
                    <tr style="font-size:10px !important;">
                        <th>EMPRESA</th>   
                        <th>TERCEIRO</th>  
                    </tr>
                </thead>

            <?php 
            } 
            ?>

            <tr class="<?php echo $class ?>" style="font-size:11px;">
                <td align="left"><a href="javascript:;" class="lanca_movimento" data-rescisao="<?php echo $row_rel['id_recisao']; ?>" data-clt="<?php echo $row_rel['id_clt']; ?>"><img src="../imagens/icones/icon-view.gif" title="lancar_movimentos" /></a></td>
                <td align="left">
                    <?php echo $row_rel['id_clt']; ?>
                    <input type="hidden" name="id_clt[]" value="<?php echo $row_rel['id_clt']; ?>">
                </td>
                <td align="left"><a href='javascript:;' data-key='<?php echo $row_rel['id_clt']; ?>' data-nome='<?php echo $row_rel['nome']; ?>' class='calcula_multa' style='color: #4989DA; text-decoration: none;'><?php echo $row_rel['nome']; ?></a><br><?php echo $projeto['nome'] ?></td>
                <td align="left" class="">
                    <?php
                    if ($row_rel['motivo'] != 60) {
                        //linha comentada por Renato(13/03/2015) por inconsistencia
                        //$valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade'];
                        $total['valor_aviso']['linha'] = $row_rel['aviso_valor'];

                        echo "R$ " . number_format($total['valor_aviso']['linha'] , 2, ",", ".");
                    } else {
                        echo "R$ " . number_format(0, 2, ",", ".");
                        $total['valor_aviso']['linha'] += 0;
                    }

                    $total['valor_aviso']['grupo'] += $total['valor_aviso']['linha'];
                    $total['valor_aviso']['geral'] += $total['valor_aviso']['linha'];

                    ?>
                </td>

                <?php
                if ($row_rel['fator'] == "empregador") {
                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
                } else if ($row_rel['fator'] == "empregado") {
                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
                }
                ?>  

                <!--DISCRIMINACÃO DAS VERBAS RESCISÓRIAS--->
                <?php
                if ($row_rel['motivo'] == 64) {
                    $multa_479 = $row_rel['a479'];
                } else if ($row_rel['motivo'] == 63) {
                    $multa_479 = null;
                }
                ?>
                <td align="right" ><?php
                    $total['dt_salario']['linha'] = $row_rel['dt_salario'];
                    $total['dt_salario']['grupo'] += $total['dt_salario']['linha'];
                    $total['dt_salario']['geral'] += $total['dt_salario']['linha'];

                    $total['decimo_a_pagar']['linha'] = $total['dt_salario']['linha'];
                    $total['decimo_a_pagar']['grupo'] += $total['decimo_a_pagar']['linha'];
                    $total['decimo_a_pagar']['geral'] += $total['decimo_a_pagar']['linha'];

                    echo "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] <br /> R$ " . number_format($total['dt_salario']['linha'], 2, ",", ".");
                    ?>
                </td> <!-- 63 -->                      
                <td align="right" ><?php
                    $total['terceiro_exercicio']['linha'] = 0;
                    $total['terceiro_exercicio']['grupo'] += $total['terceiro_exercicio']['linha'];
                    $total['terceiro_exercicio']['geral'] += $total['terceiro_exercicio']['linha'];

                    $total['decimo_a_pagar']['linha'] = $total['terceiro_exercicio']['linha'];
                    $total['decimo_a_pagar']['grupo'] += $total['decimo_a_pagar']['linha'];
                    $total['decimo_a_pagar']['geral'] += $total['decimo_a_pagar']['linha'];

                    echo "R$ " . number_format($total['terceiro_exercicio']['linha'], 2, ",", ".");
                    ?>
                </td>    <!-- 64 -->   
                <td align="right" ><?php
                    $total['terceiro_ss']['linha'] = $row_rel['terceiro_ss'];
                    $total['terceiro_ss']['grupo'] += $total['terceiro_ss']['linha'];
                    $total['terceiro_ss']['geral'] += $total['terceiro_ss']['linha'];

                    $total['decimo_a_pagar']['linha'] = $total['terceiro_ss']['linha'];
                    $total['decimo_a_pagar']['grupo'] += $total['decimo_a_pagar']['linha'];
                    $total['decimo_a_pagar']['geral'] += $total['decimo_a_pagar']['linha'];

                    echo "R$ " . number_format($total['terceiro_ss']['linha'], 2, ",", ".");
                    ?>
                </td>   <!-- 70 -->                      
                <td align="right" ><?php
                    $total['ferias_pr']['linha'] = $row_rel['ferias_pr'];
                    $total['ferias_pr']['grupo'] += $total['ferias_pr']['linha'];
                    $total['ferias_pr']['geral'] += $total['ferias_pr']['linha'];

                    $total['ferias_a_pagar']['linha'] = $total['ferias_pr']['linha'];
                    $total['ferias_a_pagar']['grupo'] += $total['ferias_a_pagar']['linha'];
                    $total['ferias_a_pagar']['geral'] += $total['ferias_a_pagar']['linha'];

                    echo "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] <br /> R$ " . number_format($total['ferias_pr']['linha'], 2, ",", ".");
                    ?>
                </td>  <!-- 65 -->  
                <td align="right" ><?php
                    $total['umterco_fp']['linha'] = $row_rel['umterco_fp'];
                    $total['umterco_fp']['grupo'] += $total['umterco_fp']['linha'];
                    $total['umterco_fp']['geral'] += $total['umterco_fp']['linha'];

                    $total['ferias_a_pagar']['linha'] = $total['umterco_fp']['linha'];
                    $total['ferias_a_pagar']['grupo'] += $total['ferias_a_pagar']['linha'];
                    $total['ferias_a_pagar']['geral'] += $total['ferias_a_pagar']['linha'];

                    echo "R$ " . number_format($total['umterco_fp']['linha'], 2, ",", ".");
                    ?></td> 
                <td align="right" ><?php
                    $total['ferias_vencidas']['linha'] = $row_rel['ferias_vencidas'];
                    $total['ferias_vencidas']['grupo'] += $total['ferias_vencidas']['linha'];
                    $total['ferias_vencidas']['geral'] += $total['ferias_vencidas']['linha'];

                    $total['ferias_a_pagar']['linha'] = $total['ferias_vencidas']['linha'];
                    $total['ferias_a_pagar']['grupo'] += $total['ferias_a_pagar']['linha'];
                    $total['ferias_a_pagar']['geral'] += $total['ferias_a_pagar']['linha'];

                    echo "R$ " . number_format($total['ferias_vencidas']['linha'], 2, ",", ".");
                    ?>
                </td>  <!-- 66 -->                         
                <td align="right" ><?php
                    $total['umterco_fv']['linha'] = $row_rel['umterco_fv'];
                    $total['umterco_fv']['grupo'] += $total['umterco_fv']['linha'];
                    $total['umterco_fv']['geral'] += $total['umterco_fv']['linha'];

                    $total['ferias_a_pagar']['linha'] = $total['umterco_fv']['linha'];
                    $total['ferias_a_pagar']['grupo'] += $total['ferias_a_pagar']['linha'];
                    $total['ferias_a_pagar']['geral'] += $total['ferias_a_pagar']['linha'];

                    echo "R$ " . number_format($total['umterco_fv']['linha'], 2, ",", ".");
                    ?></td> 
                <td align="right" ><?php
                    $total['terco_constitucional']['linha'] = $total['umterco_fp']['linha'] +  $total['umterco_fv']['linha'];    
                    $total['terco_constitucional']['grupo'] += $total['terco_constitucional']['linha'];
                    $total['terco_constitucional']['geral'] += $total['terco_constitucional']['linha'];

                    echo "R$ " . number_format($total['terco_constitucional']['linha'], 2, ",", ".");
                    ?></td>    <!-- 68 -->              

                <td align="right" ><?php
                    $total['ferias_aviso_indenizado']['linha'] = $row_rel['ferias_aviso_indenizado'];
                    $total['ferias_aviso_indenizado']['grupo'] += $total['ferias_aviso_indenizado']['linha'];
                    $total['ferias_aviso_indenizado']['geral'] += $total['ferias_aviso_indenizado']['linha'];

                    $total['ferias_a_pagar']['linha'] = $total['ferias_aviso_indenizado']['linha'];
                    $total['ferias_a_pagar']['grupo'] += $total['ferias_a_pagar']['linha'];
                    $total['ferias_a_pagar']['geral'] += $total['ferias_a_pagar']['linha'];

                    echo "R$ " . number_format($total['ferias_aviso_indenizado']['linha'], 2, ",", ".");
                    ?></td>              <!-- 71 -->           
                <td align="right" ><?php
                    $total['fv_dobro']['linha'] = $row_rel['fv_dobro'];
                    $total['fv_dobro']['grupo'] += $total['fv_dobro']['linha'];
                    $total['fv_dobro']['geral'] += $total['fv_dobro']['linha'];

                    echo "R$ " . number_format($total['fv_dobro']['linha'], 2, ",", ".");
                    ?></td>  <!-- 72 -->                           
                <td align="right" ><?php
                    $total['um_terco_ferias_dobro']['linha'] = $row_rel['um_terco_ferias_dobro'];
                    $total['um_terco_ferias_dobro']['grupo'] += $total['um_terco_ferias_dobro']['linha'];
                    $total['um_terco_ferias_dobro']['geral'] += $total['um_terco_ferias_dobro']['linha'];

                    $total['ferias_a_pagar']['linha'] = $total['um_terco_ferias_dobro']['linha'];
                    $total['ferias_a_pagar']['grupo'] += $total['ferias_a_pagar']['linha'];
                    $total['ferias_a_pagar']['geral'] += $total['ferias_a_pagar']['linha'];

                    echo "R$ " . number_format($total['um_terco_ferias_dobro']['linha'], 2, ",", ".");
                    ?></td>  <!-- 73 -->                           
                <td align="right" ><?php
                    $total['umterco_ferias_aviso_indenizado']['linha'] = $row_rel['umterco_ferias_aviso_indenizado'];
                    $total['umterco_ferias_aviso_indenizado']['grupo'] += $total['umterco_ferias_aviso_indenizado']['linha'];
                    $total['umterco_ferias_aviso_indenizado']['geral'] += $total['umterco_ferias_aviso_indenizado']['linha'];

                    $total['ferias_a_pagar']['linha'] = $total['umterco_ferias_aviso_indenizado']['linha'];
                    $total['ferias_a_pagar']['grupo'] += $total['ferias_a_pagar']['linha'];
                    $total['ferias_a_pagar']['geral'] += $total['ferias_a_pagar']['linha'];

                    echo "R$ " . number_format($total['umterco_ferias_aviso_indenizado']['linha'], 2, ",", ".");
                    ?></td>   <!-- 82 --> 
                <td align="right" ><?php
                    $total['lei_12_506']['linha'] = $row_rel['lei_12_506'];
                    $total['lei_12_506']['grupo'] += $total['lei_12_506']['linha'];
                    $total['lei_12_506']['geral'] += $total['lei_12_506']['linha'];

                    echo "R$ " . number_format($total['lei_12_506']['linha'], 2, ",", ".");
                    ?></td>  <!-- 95 -->                           
                <td align="right" ><?php
                    $total['aviso_indenizado']['linha'] = $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                    $total['aviso_indenizado']['grupo'] += $total['aviso_indenizado']['linha'];
                    $total['aviso_indenizado']['geral'] += $total['aviso_indenizado']['linha'];


                    echo "R$ " . number_format($total['aviso_indenizado']['linha'], 2, ",", ".");
                    ?></td>    <!-- 69 -->  
                <!--DEDUCOES--->

                <?php
                if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"])) {
                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"];
                } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50222]["valor"])) {
                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][50222]["valor"];
                } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"])) {
                    $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"];
                } else {
                    $pensao = 0;
                }
                ?>
                <!-- Campo comentados

                <td align="right" >
                <?php
                echo "R$ " . number_format(0, 2, ",", ".");
                $total_adiantamento_13_salarial += 0;
                ?></td>   102                            
                        <td align="right" ><?php
                echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
                $total_aviso_indenizado_debito += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                ?></td>   103                            -->
                <?php
                if ($row_rel['motivo'] == 64) {
                    $multa_480 = null;
                } else if ($row_rel['motivo'] == 63) {
                    $multa_480 = $row_rescisao['a480'];
                }
                ?>
                <!--                        <td align="right" >
                <?php
                echo "R$ " . number_format($row_rel['inss_dt'], 2, ",", ".");
                $total_inss_dt += $row_rel['inss_dt'];
                $total_deducao_debito += $row_rel['inss_dt'];
                ?>
                </td>    112.2                      
                <td align="right" ><?php
                echo "R$ " . number_format($row_rel['ir_dt'], 2, ",", ".");
                $total_ir_dt += $row_rel['ir_dt'];
                $total_deducao_debito += $row_rel['ir_dt'];
                ?></td>     114.2                     
                        <td align="right" ><?php
                echo "R$ " . number_format($row_rel['adiantamento_13'], 2, ",", ".");
                $total_adiantamento_13 += $row_rel['adiantamento_13'];
                ?></td>     115.2                     -->

                <?php
                if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
                    $movimento_falta = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
                } else {
                    $movimento_falta = 0;
                }
                ?>
                <!--<td align="right" ><?php
                echo "R$ " . number_format($row_rel['ir_ferias'], 2, ",", ".");
                $total_ir_ferias += $row_rel['ir_ferias'];
                $total_deducao_debito += $row_rel['ir_ferias'];
                ?></td>     116 -->  

                <!-- OUTROS VALORES -->
                <!-- BASES -->

                <td align="right" style="background: #fff; border: 0px;"></td>                       
                <td align="right">
                    <?php
                    /*
                     * 09/11/2015 - Jacques
                     * Total Linha PIS 
                     * 
                     * Obs: Segundo o Milton a base do PIS incide apenas em cima do 13o
                     * 
                     *      A base de INSS e FGTS é apurada em cima do 13 + Aviso + Lei
                     * 
                     * 22/01/2016 - Segundo a interpretação que fiz na afirmativa acima do Milton na data supra-cita não apliquei ao valor acumulado trazido pela classe
                     * 
                     */
                    //($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.01
                    //($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01

                    // Bases para calculo de PIS, FGTS e INSS 
                    $base_fgts = ($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador") ? ($row_rel['saldo_salario']+$row_rel['terceiro_ss']+$row_rel['dt_salario']+$row_rel['lei_12_506']+$aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']+$total_movimentos_incide_fgts)-($row_rel['valor_faltas']+$movimento_falta) : 0; 
                    $base_inss = $total_rendimento - ($row_rel['ferias_pr']+$row_rel['ferias_vencidas']+$row_rel['sal_familia']+$row_rel['umterco_fv']+$row_rel['ferias_aviso_indenizado']);
                    $base_pis  = $base_inss;

                    // Fatores aplicados as bases
                    $total['pis']['linha'] = $base_pis * 0.01;
                    $total['fgts_multa']['linha'] = (($base_fgts * 0.08)*0.5)+$folha->getMultaFgts($row_rel['id_clt']);
                    $total['inss_empresa']['linha'] = $base_inss * $row_rel['taxaRAT']; 
                    $total['inss_terceiro']['linha'] = $base_inss * 0.058;
                    $total['fgts_recolher']['linha'] = $base_fgts * 0.08;

                    // Totalizadores de sub-grupos
                    $total['pis']['grupo'] += $total['pis']['linha'];
                    $total['fgts_multa']['grupo'] += $total['fgts_multa']['linha'];
                    $total['inss_empresa']['grupo'] += $total['inss_empresa']['linha'];
                    $total['inss_terceiro']['grupo'] += $total['inss_terceiro']['linha'];
                    $total['fgts_recolher']['grupo'] += $total['fgts_recolher']['linha'];

                    $total['pis']['geral'] +=$total['pis']['linha'];
                    $total['fgts_multa']['geral'] += $total['fgts_multa']['linha'];
                    $total['inss_empresa']['geral'] += $total['inss_empresa']['linha'];
                    $total['inss_terceiro']['geral'] += $total['inss_terceiro']['linha'];
                    $total['fgts_recolher']['geral'] += $total['fgts_recolher']['linha'];                                            

                    echo "R$ " . number_format($total['pis']['linha'], 2, ",", ".");

                    foreach ($status_array as $status_clt) {
                        if ($row_rel['codigo'] == $status_clt) {
                            $total_pis_a_pagar[$status_clt] += $total['pis']['linha'];
                        }
                    }
                    ?>
                </td>                       
                <td align="right">
                    <?php
                    /*
                     * 09/11/2015 - Jacques
                     * Tive que fazer o calculo do FGTS por fora da classe porque o Miltom disse que a base era a mesma no INSS
                     * 
                     * echo "R$ " . number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", ".");
                     */
                    echo "R$ " . number_format($total['fgts_multa']['linha'], 2, ",", ".");                                    

                    //$total_multa_fgts += $folha->getMultaFgts($row_rel['id_clt']);

                    foreach ($status_array as $status_clt) {
                        if ($row_rel['codigo'] == $status_clt) {
                            if ($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador") {
                                //$total_multa_a_pagar[$status_clt] += $folha->getMultaFgts($row_rel['id_clt']);
                                $total_multa_a_pagar[$status_clt] += $total['fgts_multa']['linha'];
                            }
                        }
                    }
                    ?>
                </td>                       
                <td align="right">
                    <?php
                    /*
                     * Total linha INSS Empresa
                     * 
                     * $total_inss_empresa += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
                     */

                    echo "R$ " . number_format($total['inss_empresa']['linha'], 2, ",", ".");

//                                    foreach ($status_array as $status_clt) {
//                                        if ($row_rel['codigo'] == $status_clt) {
//                                            //$total_inss_empresa_a_pagar[$status_clt] += $total_inss_empresa; 
//                                            $total_inss_empresa_a_pagar[$status_clt] += $total['inss_empresa']['linha']; 
//                                        }
//                                    }
                    ?>
                </td>  
                <td align="right">
                    <?php
                    /*
                     * Total linha INSS Terceiro
                     * 
                     * $total_inss_terceiro += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
                     */



                    echo "R$ " . number_format($total['inss_terceiro']['linha'], 2, ",", ".");

//                                    foreach ($status_array as $status_clt) {
//                                        if ($row_rel['codigo'] == $status_clt) {
//                                            //$total_inss_terceiro_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
//                                            $total_inss_terceiro_a_pagar[$status_clt] += $total['inss_terceiro']['linha'];
//                                        }
//                                    }
                    ?>
                </td>  
                <td align="right">
                    <?php
                    /*
                     * Total linha FGTS 
                     * 
                     * $total_fgts_recolher += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
                     */                                    

                    echo "R$ " . number_format($total['fgts_recolher']['linha'], 2, ",", ".");

//                                    foreach ($status_array as $status_clt) {
//                                        if ($row_rel['codigo'] == $status_clt) {
//                                            //$total_fgts_recolher_a_pagar[$status_clt] += $total_fgts_recolher;
//                                            $total_fgts_recolher_a_pagar[$status_clt] += $total['fgts_recolher']['linha'];                                            
//                                        }
//                                    }
                    ?>
                </td>
            </tr>                                

        <?php 
        } 

        $total_recisao_nao_paga += $total_liquido;

        footer('provisao_trabalhista',$total,'grupo','Total Parcial:',0);

        footer('provisao_trabalhista',$total,'geral','Total Geral:');

        ?>
    </table>
    <div class="totalizador">
        <p class="titulo">TOTALIZADORES<!--DEMONSTRATIVO FÉRIAS E 13° SALÁRIO--></p>
        <p>FÉRIAS: <span><?php echo "R$ " . number_format($total['ferias_a_pagar']['geral'], 2, ",", "."); ?></span></p>
        <p>13° SALÁRIO: <span><?php echo "R$ " . number_format($total['decimo_a_pagar']['geral'], 2, ",", "."); ?></span></p>
        <p>PROVISÃO RESCISOES: <span><?php echo "R$ " . number_format($total['aviso_indenizado']['geral'] + $total['fgts_multa']['geral'] + $total['lei_12_506']['geral'], 2, ",", "."); ?></span></p>
        <p>AVISO PRÉVIO: <span><?php echo "R$ " . number_format($total['aviso_indenizado']['geral'], 2, ",", "."); ?></span></p>
        <p>MULTA FGTS: <span><?php echo "R$ " . number_format($total['fgts_multa']['geral'], 2, ",", "."); ?></span></p>
        <p>LEI 12/506: <span><?php echo "R$ " . number_format($total['lei_12_506']['geral'], 2, ",", "."); ?></span></p>
        <!--<p>PROVISÃO INSS S/PROV. TRABALHISTA: <span><?php //echo "R$ " . number_format(($total['decimo_a_pagar']['linha'] + /* $total_aviso_indenizado + */ $total_lei_12_506) * 0.268, 2, ",", "."); ?></span></p>-->
        <p>PROVISÃO INSS S/PROV. TRABALHISTA: <span><?php echo "R$ " . number_format(($total['inss_empresa']['geral'] + $total['inss_terceiro']['geral']), 2, ",", "."); ?></span></p>
        <!--<p>PROVISÃO FGTS S/PROV. TRABALHISTA: <span><?php //echo "R$ " . number_format(($total['decimo_a_pagar']['linha'] + $total_aviso_indenizado + $total_lei_12_506) * 0.08, 2, ",", "."); ?></span></p>-->
        <p>PROVISÃO FGTS S/PROV. TRABALHISTA: <span><?php echo "R$ " .number_format($total['fgts_recolher']['geral'], 2, ",", "."); ?></span></p>
        <!--<p>PROVISÃO PIS S/PROV. TRABALHISTA: <span><?php //echo "R$ " . number_format($total['decimo_a_pagar']['linha'] * 0.01, 2, ",", "."); ?></span></p>-->
        <p>PROVISÃO PIS S/PROV. TRABALHISTA: <span><?php echo "R$ " . number_format($total['pis']['geral'], 2, ",", "."); ?></span></p>
        <p>TOTAL: <span>R$ <?= number_format($total['ferias_a_pagar']['geral'] + $total['decimo_a_pagar']['linha'] + $total['aviso_indenizado']['geral'] + $total['fgts_multa']['geral'] + $total['lei_12_506']['geral'], 2, ',', '.') ?></span>
        <p>MARGEM DE ERRO (5%): <span>R$ <?= number_format(($total['ferias_a_pagar']['geral'] + $total['decimo_a_pagar']['linha'] + $total['aviso_indenizado']['geral'] + $total['fgts_multa']['geral'] + $total['lei_12_506']['geral']) * 1.05, 2, ',', '.') ?></span>
    </div>

<?php } ?>

