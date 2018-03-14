<?php if (isset($_REQUEST['mostrar_rescisao']) && $num_rows > 0) { ?>
<div id="relatorio_exp">
    <p style="text-align: left; margin-top: 20px" class="imprime">
        <input type="button" id="exportarExcel" name="exportarExcel" value="Exportar para Excel">
        <!--<input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório de Previsao de Gastos de Recisao')" value="Exportar para Excel" class="exportarExcel">-->                        
        <!--<input type="submit" id="exportarExcel" name="exportarExcel" value="Exportar para Excel" class="exportarExcel">-->
        <input type="hidden" id="data_xls" name="data_xls" value="">
    </p>    
    <input type="hidden" name="id_rescisao_lote" value="<?= $_REQUEST['id_rescisao_lote'] ?>">
    <input type="hidden" name="projeto_oculto" value="<?= $_REQUEST['projeto_oculto'] ?>">
    <input type="hidden" id="clt_count" name="clt_count" value="0"/>                            
    <h3><?php echo $projeto['nome'] ?></h3>    
    <!--<p>Total de participantes: <b><?php //echo $total_participantes["total_participantes"];      ?></b></p>-->
    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto; border: 0px;"> 
        <thead>
            <tr style="height: 30px; background: #fff; border: 0px;">
                <td colspan="10" class="area-xpandir-1"><span class="xpandir"></span></td>
                <td colspan="1" class="area-xpandir-2"><div class="area"></div></td>
                <td colspan="1" class="area-xpandir-3"><span class="xpandirr"></span></td>
                <td colspan="1" class="area-xpandir-4"><div class="areaa"></div></td>
                <td colspan="1" class="area-xpandir-5"><span class="xpandirrr"></span></td>
                <td colspan="1" class="area-xpandir-6"><div class="areaaa"></div></td>
            </tr>
        </thead>
        <?php $status = 0; ?>

        <?php
        while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {


//                            if($_COOKIE['logado'] == 275){
//                                echo "<pre>" ;
//                                    print_r($row_rel);
//                                echo "</pre>" ;
//                            }

            $mov = array();
            $total_movimentos = array();
            $movimentos_incide = 0;
            $total_movimentos_incide_fgts = 0;

            /*
             * Busca pelos movimentos para rescisao na tabela morta
             */
            $q = "
                    SELECT id_clt, cod, SUM(valor) valor, tipos, incidencia_inss, incidencia_fgts
                    FROM (
                        SELECT 
                            A.id_mov, 
                            A.id_rescisao, 
                            A.id_clt, 
                            B.cod, 
                            A.valor, 
                            TRIM(A.tipo) as tipos, 
                            B.incidencia_inss,
                            B.incidencia_fgts
                        FROM tabela_morta_movimentos_recisao_lote AS A LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.id_mov)
                        WHERE 
                            A.id_clt = {$row_rel['id_clt']} 
                            AND A.id_rescisao = '{$row_rel['id_recisao']}'

                        UNION ALL

                        SELECT 
                            A.id_movimento id_mov, 
                            0 id_rescisao, 
                            A.id_clt, 
                            B.cod, 
                            A.valor_movimento valor, 
                            TRIM(A.tipo_movimento) tipos, 
                            B.incidencia_inss, 
                            B.incidencia_fgts
                        FROM rh_movimentos_clt AS A LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                        WHERE status AND A.id_clt = {$row_rel['id_clt']} AND A.cod_movimento IN ('0')
                    ) m
                    GROUP BY id_clt, cod, tipos, incidencia_inss, incidencia_fgts
                ";
                
            $rsMovimentoRecisao = mysql_query($q) or die($q);
            
            while ($rows_movimentos = mysql_fetch_assoc($rsMovimentoRecisao)) {

                $mov[$row_rel['id_recisao']][$rows_movimentos['id_clt']][$rows_movimentos['tipos']][$rows_movimentos['cod']]["valor"] += $rows_movimentos['valor'];

                if ($rows_movimentos['tipos'] == "CREDITO" && $rows_movimentos['incidencia_inss'] == '1') {

                    $movimentos_incide += $rows_movimentos['valor'];

                }
                else {

                    if ($rows_movimentos['tipos'] == "DEBITO") {

                        $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] += $rows_movimentos['valor'];

                    } else if ($rows_movimentos['tipos'] == "CREDITO") {

                        $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] += $rows_movimentos['valor'];

                    }

                }

            }

            //print_array($mov);

            /////////////////////
            // MOVIMENTOS FIXOS ///// 
            ///////////////////

            $q = 
            "
            SELECT  
                A.ids_movimentos_estatisticas, 
                B.id_clt,A.mes
            FROM rh_folha as A
                INNER JOIN rh_folha_proc as B ON A. id_folha = B.id_folha
            WHERE 
                B.id_clt = {$row_rel['id_clt']}  
                AND B.status = 3 
                AND A.terceiro = 2 
                AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) 
            ORDER BY 
                A.ano,
                A.mes
            ";

            $rsFolha = mysql_query($q);

            $movimentos = 0;
            $total_rendi = 0;

            while ($row_folha = mysql_fetch_assoc($rsFolha)) {

                if (!empty($row_folha['ids_movimentos_estatisticas'])) {

                    $q = "
                        SELECT *
                        FROM rh_movimentos_clt
                        WHERE 
                            id_movimento IN({$row_folha['ids_movimentos_estatisticas']}) 
                            AND incidencia = '5020,5021,5023'  
                            AND tipo_movimento = 'CREDITO' 
                            AND id_clt = '{$row_rel['id_clt']}' 
                            AND id_mov NOT IN(56,200,235,57,279) ";
                            
                    $rsMovimentos = mysql_query($q);

                    while ($row_mov = mysql_fetch_assoc($rsMovimentos)) {

                        $movimentos += $row_mov['valor_movimento'];

                    }
                    
                }

            }
            
            if ($movimentos > 0) {
                $total_rendi = $movimentos / 12;
            } else {
                $total_rendi = 0;
            }


            ///////////////////////////////////////////////
            ////////// CÁLCULO DE INSS /////////////
            ///////////////////////////////////////////////
            /**
             * FEITO POR SINESIO LUIZ
             * REMOVIDO A LEI 12_506 JUNTO AO LEONARDO DO RH PARA EFEITO DE BASE DE INSS
             */
            $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
            $data_exp = explode('-', $row_rel['data_demi']);

            if ($base_saldo_salario > 0) {
                //echo $base_saldo_salario;
                $calculos->MostraINSS($base_saldo_salario, implode('-', $data_exp));
                if($_COOKIE['logado'] == 179){
                    echo "Sinesio INSS: " . $calculos->valor;
                }
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

            $status_old = $status;

            if ($status != $row_rel["codigo"]) {
                $status = $row_rel["codigo"];
                $taxaRAT = $row_rel['taxaRAT']*100;
                ?>

                <?php if (!empty($total_sal_base)) { ?>
                    <?php
                    if ($row_rel['codigo'] != 20) {
                        $total_recisao_nao_paga += $total_liquido;
                    }
                    ?>
                    <tfoot>
                        <tr class="footer">
                            <td align="right" colspan="7">Total:</td>
                            <td align="right"><?php echo "R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", "."); ?></td>
                            <td align="right"><?php echo "R$ " . number_format($total_sal_base, 2, ",", "."); ?></td>
                            <!--<td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>-->
                            <td align="right"><?php echo "R$ " . number_format($total_saldo_salario, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_comissoes, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gratificacao, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_insalubridade, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_periculosidade, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_adicional_noturno, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_hora_extra, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gorjetas, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dsr, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_reflexo_dsr, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_477, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_479, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_sal_familia, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>    
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>    
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>    
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>    
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_diferenca_salarial, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuda_custo, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dif_dissidio, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_vale_transporte, 2, ",", "."); ?></td>
                            <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuste_de_saldo, 2, ",", "."); ?></td>
                            <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status_old], 2, ",", "."); ?></td>



                            <!-- TOTAL DE DEDUCÃO -->
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_pensao_alimenticia, 2, ",", "."); ?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_salarial, 2, ",", "."); ?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_multa_480, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_emprestimo_consignado, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_transporte_debito, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_alimentacao_debito, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_ss, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_dt, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ss, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_dt, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_devolucao, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_outros, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_faltas, 2, ",", ".");?></td>
                            <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", ".");?></td>
                            <td align="right" class="">(<?php echo "R$ " . number_format($total_grupo_deducao[$status_old], 2, ",", "."); ?>)</td>
                            <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status_old] - $total_grupo_deducao[$status_old], 2, ",", "."); ?></td>


                            <!-- DETALHES IMPORTANTES -->
                            <!-- BASES -->                        
                            <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_inss, 2, ",", "."); ?></td>
                            <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_fgts, 2, ",", "."); ?></td>
                            <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_pis, 2, ",", "."); ?></td>
                            <td align="right" style="background: #fff; border: 0px;"></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                            <td align="right"><?php echo "R$ " . number_format($total_inss_rat, 2, ",", "."); ?></td> 
                            <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                            <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                        </tr>
                        <tr>
                            <td colspan="37" style="border: 0px;"></td>
                        </tr>
                    </tfoot>

                <?php 

                    } else { 

                    ?>
                    <tfoot>
                        <tr class="footer">
                            <td colspan="74"></td>
                        </tr>
                    </tfoot>                    
                <?php } ?>
                <thead>
                    <tr>
                        <th colspan="13" class="cabecalho_compactar"><?php echo "<p style='text-transform:uppercase; text-align:left' > » " . $row_rel['especifica'] . " - " . $row_rel['aviso'] . "</p>"; ?></th>
                        <th style="background: #fff; border: 0px;" ></th>
                        <th colspan="6">EMPRESA</th>
                    </tr>
                    <tr style="font-size:10px !important;">
                        <th rowspan="2">ACAO</th>
                        <th rowspan="2">ID</th>
                        <th rowspan="2"><span class="numero_rescisao">[11]</span>NOME</th>
                        <th rowspan="2"><span class="numero_rescisao">[24]</span>DATA DE ADMISSAO</th>
                        <th rowspan="2"><span class="numero_rescisao">[25]</span>Data do Aviso Previo</th>  
                        <th rowspan="2"><span class="numero_rescisao">[26]</span>DATA DE AFASTAMENTO</th>                                
                        <th rowspan="2">FUNCAO</th>  
                        <th rowspan="2">MEDIA DAS OUTRAS REMUNERACOES</th>  
                        <th rowspan="2">SALARIO BASE</th>  
                        <!--<th rowspan="2">VALOR AVISO</th>-->  
                        <th rowspan="2"><span class="numero_rescisao">[50]</span>SALDO DE SALARIO</th>

                        <!--DISCRIMINACÃO DAS VERBAS RESCISÓRIAS--->
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[51]</span>COMISSOES</th>
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[52]</span>GRATIFICACAO</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[53]</span>ADICIONAL DE INSALUBRIDADE</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[54]</span>ADICIONAL DE PERICULOSIDADE</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[55]</span>ADICIONAL NOTURNO</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[56]</span>Horas Extras</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[57]</span>Gorjetas</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[58]</span>Descanso Semanal Remunerado (DSR)</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[59]</span>Reflexo do "DSR" sobre Salario Variavel</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[60]</span>Multa Art. 477, § 8º/CLT</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[61]</span>Multa Art. 479/CLT</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[62]</span>Salario-Familia</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[63]</span>13º Salario Proporcional</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[64]</span>13º Salario Exercicio</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[65]</span>Ferias Proporcionais</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[]</span>1/3 DE FERIAS PROPORCIONAL </th> 
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[66]</span>Ferias Vencidas Per. Aquisitivo</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[]</span>1/3 DE FERIAS VENCIDAS</th> 
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[68]</span>Terco Constitucional de Ferias</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[69]</span>Aviso Previo indenizado</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[70]</span>13º Salario (Aviso-Previo Indenizado)</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[71]</span>Ferias (Aviso-Previo Indenizado)</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[72]</span>Ferias em dobro</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[73]</span>1/3 ferias em dobro</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[82]</span> 1/3 DE FERIAS AVISO INDENIZADO </th>
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[80]</span>Diferenca Salarial</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[480]</span>Ajuda de Custo Art. 470/CLT</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[95]</span>Lei 12.506</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[200]</span>Diferenca Dissídio</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[106]</span>Vale Transporte</th>  
                        <th rowspan="2" class="esconder"><span class="numero_rescisao">[99]</span>Ajuste do Saldo Devedor</th>  
                        <th rowspan="2" ><span class="numero_rescisao"></span>TOTAL RESCISORIO BRUTO</th>  

                        <!--DEDUCOES--->
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[100]</span>Pensao Alimenticia</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[101]</span>Adiantamento Salarial</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[102]</span>Adiantamento de 13º Salario</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[103]</span>Aviso-Previo Indenizado</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[104]</span>Multa Art. 480/CLT</th>  
                        <!--<th rowspan="2" class="esconderr"><span class="numero_rescisao">[105]</span>Emprestimo em Consignacao</th>-->  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[234]</span>Auxílio Distância</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[106]</span>Vale Transporte</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[109]</span>Vale Alimentacao</th> 


                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[112.1]</span>Previdencia Social</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[112.2]</span>Previdencia Social - 13º Salario</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[114.1]</span>IRRF</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[114.2</span>IRRF sobre 13º Salario</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115]</span>Devolucao de Credito Indevido</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115.1]</span>Outros</th>  
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[115.2]</span>Adiantamento de 13º Salario</th>
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[117]</span>Faltas</th>    
                        <th rowspan="2" class="esconderr"><span class="numero_rescisao">[116]</span>IRRF Ferias</th>  

                        <th rowspan="2"><span class="numero_rescisao"></span>TOTAL DAS DEDUCOES</th>  
                        <th rowspan="2" >VALOR RESCISÓRIO LÍQUIDO</th> 

                        <!-- DETALHES IMPORTANTES --->
                        <!--BASES -->
                        <th rowspan="2" class="esconderrr">BASE INSS</th>   
                        <th rowspan="2" class="esconderrr">BASE FGTS</th>  
                        <th rowspan="2" class="esconderrr">BASE PIS</th>  

                        <!--EMPRESA-->
                        <th rowspan="2" style="background: #fff; border: 0px;"></th>   
                        <th rowspan="2">PIS</th>   
                        <th rowspan="2">MULTA DE 50% DO FGTS</th>   
                        <th colspan="3">INSS A RECOLHER</th>  
                        <th rowspan="2">FGTS A RECOLHER</th>

                    </tr>
                    <tr style="font-size:10px !important;">
                        <th>EMPRESA</th>   
                        <th><nobr>RAT (<?=$taxaRAT?>%)</nobr></th>   
                        <th>TERCEIRO</th>  
                    </tr>
                </thead>
                <?php
                //VERBAS RESCISÓRIAS
                $total_das_medias_outras_remuneracoes = 0;
                $total_sal_base = 0;
                $total_valor_aviso = 0;
                $total_saldo_salario = 0;
                $total_comissoes = 0;
                $total_gratificacao = 0;
                $total_insalubridade = 0;
                $total_periculosidade = 0;
                $total_adicional_noturno = 0;
                $total_hora_extra = 0;
                $total_gorjetas = 0;
                $total_dsr = 0;
                $total_reflexo_dsr = 0;
                $total_multa_477 = 0;
                $total_multa_479 = 0;
                $total_sal_familia = 0;
                $total_dt_salario = 0;
                $total_terceiro_exercicio = 0;
                $total_ferias_pr = 0;
                $total_ferias_aquisitivas = 0;
                $total_terco_constitucional = 0;
                $total_aviso_indenizado = 0;
                $total_terceiro_ss = 0;
                $total_f_aviso_indenizado = 0;
                $total_f_dobro = 0;
                $total_umterco_f_dobro = 0;
                $total_diferenca_salarial = 0;
                $total_ajuda_custo = 0;
                $total_lei_12_506 = 0;
                $total_dif_dissidio = 0;
                $total_vale_transporte = 0;
                $total_ajuste_de_saldo = 0;
                $total_rendimento = 0;


                //DEDUCOES
                $total_pensao_alimenticia = 0;
                $total_adiantamento_salarial = 0;
                $total_adiantamento_13_salarial = 0;
                $total_aviso_indenizado_debito = 0;
                $total_multa_480 = 0;
                $total_emprestimo_consignado = 0;
                $total_auxilio_distancia_debito = 0;
                $total_vale_transporte_debito = 0;
                $total_vale_alimentacao_debito = 0;
                $total_inss_ss = 0;
                $total_inss_dt = 0;
                $total_ir_ss = 0;
                $total_ir_dt = 0;
                $total_devolucao = 0;
                $total_outros = 0;
                $total_adiantamento_13 = 0;
                $total_faltas = 0;
                $total_ir_ferias = 0;
                $total_deducao = 0;
                $total_liquido = 0;

                //DETALHES IMPORTANTES
                $total_umterco_ferias_aviso = 0;
                $total_umterco_fp = 0;
                $total_umterco_fv = 0;
                $total_ferias_vencida = 0;
                $total_f_dobro_fv = 0;

                //BASES
                $total_base_inss = 0;
                $total_base_fgts = 0;
                $total_base_pis = 0;
                $total_pis = 0;
                $total_multa_fgts = 0;
                $total_inss_empresa = 0;
                $total_inss_rat = 0;
                $total_inss_terceiro = 0;
                $total_fgts_recolher = 0;

                //Totalizadores gerais
                $total_geral_rendimento = 0;
                $total_geral_deducao = 0;                                               

                //TOTALIZADOR FÉRIAS
                $total_ferias_a_pagar = 0;

                //TOTALIZADOR 13° 
                $total_decimo_a_pagar = 0;

                ?>

            <?php } ?>

            <tr class="<?php echo $class ?>" style="font-size:11px;">
                <td align="left"><a href="javascript:;" class="lanca_movimento" data-id_rescisao_lote="<?=$row_rel['id_recisao_lote']?>" data-id_rescisao="<?=$row_rel['id_recisao']?>" data-id_clt="<?=$row_rel['id_clt']?>"><img src="../imagens/icones/icon-view.gif" title="lancar_movimentos" /></a></td>
                <td align="left">
                    <?php echo $row_rel['id_clt'];?>
                    <input type="hidden" name="id_clt[]" value="<?php echo $row_rel['id_clt']; ?>">
                    <input type="hidden" name="id_recisao[]" value="<?php echo $row_rel['id_recisao']; ?>">
                </td>
                <td align="left"><a href='javascript:;' data-key='<?php echo $row_rel['id_clt']; ?>' data-nome='<?php echo $row_rel['nome']; ?>' class='calcula_multa' style='color: #4989DA; text-decoration: none;'><?php echo $row_rel['nome']; ?></a></td>
                <td align="left"><?php echo (!empty($row_rel['data_adm'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_adm']))) : "0000-00-00"; ?></td>
                <td align="left"><?php echo (!empty($row_rel['data_aviso'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_aviso']))) : "00/00/0000"; ?></td>
                <td align="left"><?php echo (!empty($row_rel['data_demi'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_demi']))) : "0000-00-00"; ?></td>
                <td align="left"><?php echo $row_rel['nome_funcao']; ?></td>
                <td align="left"><?php
                    echo "R$ " . number_format($row_rel['salario_variavel'], 2, ",", ".");
                    $total_das_medias_outras_remuneracoes += $row_rel['salario_variavel'];

                    ?></td>
                <td align="right">
                    <?php
                    echo "R$ " . number_format($row_rel['sal_base'], 2, ",", "."); 
                    $total_sal_base += $row_rel['sal_base'];
//                                    foreach ($status_array as $status_clt) {
//                                        if ($row_rel['codigo'] == $status_clt) {
//                                            $total_a_ser_pago[$status_clt] += $row_rel['total_rendimento'] + ($total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']);
//                                        }
//                                    }
                    ?>
                </td> 
<!--                                <td align="left" class="">
                <?php

                if ($row_rel['motivo'] != 60) {
                    //linha comentada por Renato(13/03/2015) por inconsistencia
                    //$valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade'];
                    $valor_aviso = $row_rel['aviso_valor'];
                    echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                    $total_valor_aviso += $valor_aviso;
                } else {
                    $valor_aviso = 0;
                    echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                    $total_valor_aviso += $valor_aviso;
                }
                ?>
                </td>-->

                <?php
//                            echo "<pre>"; 
//                                print_r($row_rel);
//                            echo "<pre>"; 
                ?>

                <?php
                if ($row_rel['fator'] == "empregador") {
                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
                } else if ($row_rel['fator'] == "empregado") {
                    $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
                }

                ?>  

                <!--DISCRIMINACÃO DAS VERBAS RESCISÓRIAS--->
                <td align="left" class=""><?php
                    echo "[" . $row_rel['dias_saldo'] . "/30] <br /> R$ " . number_format($row_rel['saldo_salario'], 2, ",", ".");
                    $total_saldo_salario += $row_rel['saldo_salario'];
                    $total_rendimento  = $row_rel['saldo_salario'];

                    ?></td>
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['comissao'], 2, ",", ".");
                    $total_comissoes += $row_rel['comissao'];
                    $total_rendimento += $row_rel['comissao'];

                    ?></td> <!--- 51--->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"], 2, ",", ".");
                    $total_gratificacao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"];
                    $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"];

                    ?></td> <!--- 52--->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['insalubridade'], 2, ",", ".");
                    $total_insalubridade += $row_rel['insalubridade'];
                    $total_rendimento  += $row_rel['insalubridade'];

                    ?></td>  <!--- 53--->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['periculosidade'], 2, ",", ".");
                    $total_periculosidade += $row_rel['periculosidade'];
                    $total_rendimento += $row_rel['periculosidade'];

                    ?></td> <!--- 54--->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"], 2, ",", ".");
                    $total_adicional_noturno += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"];
                    $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"];

                    ?></td> <!-- 55 -->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"], 2, ",", ".");
                    $total_hora_extra += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"];
                    $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"];
                    ?></td> <!-- 56 -->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format(0, 2, ",", ".");
                    $total_gorjetas += 0;
                    $total_rendimento += 0;
                    ?></td> <!-- 57 -->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"], 2, ",", ".");
                    $total_dsr += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"];
                    $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"];

                    ?></td> <!-- 58 -->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format(0, 2, ",", ".");
                    $total_reflexo_dsr += 0;
                    $total_rendimento += 0;
                    ?></td> <!-- 59 -->
                <td align="left" class="esconder"><?php
                    echo "R$ 0,00";
//                        $total_multa_477 += $row_rel['a477'];
//                        echo "R$ 0,00" . number_format($row_rel['a477'], 2, ",", ".");
                    ?></td> <!-- 60 -->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['a479'], 2, ",", ".");
                    $total_multa_479 += $row_rel['a479'];
                    $total_rendimento += $row_rel['a479'];
                    ?></td> <!-- 61 -->
                <td align="left" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['sal_familia'], 2, ",", ".");
                    $total_sal_familia += $row_rel['sal_familia'];
                    $total_rendimento += $row_rel['sal_familia'];
                    ?></td> <!-- 62 -->
                <td align="right" class="esconder"><?php
                    echo "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] <br /> R$ " . number_format($row_rel['dt_salario'], 2, ",", ".");
                    $total_dt_salario += $row_rel['dt_salario'];
                    $total_decimo_a_pagar += $row_rel['dt_salario'];
                    $total_rendimento += $row_rel['dt_salario'];
                    ?></td> <!-- 63 -->                      
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format(0, 2, ",", ".");
                    $total_terceiro_exercicio += 0;
                    $total_decimo_a_pagar += 0;
                    $total_rendimento += 0;
                    ?></td>    <!-- 64 -->                     
                <td align="right" class="esconder"><?php
                    echo "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] <br /> R$ " . number_format($row_rel['ferias_pr'], 2, ",", ".");
                    $total_ferias_pr += $row_rel['ferias_pr'];
                    $total_ferias_a_pagar += $row_rel['ferias_pr'];
                    $total_rendimento += $row_rel['ferias_pr'];
                    ?></td>  <!-- 65 -->  
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['umterco_fp'], 2, ",", ".");
                    $total_umterco_fp += $row_rel['umterco_fp'];
                    $total_ferias_a_pagar += $row_rel['umterco_fp'];
                    $total_rendimento += $row_rel['umterco_fp'];

                    ?></td> 
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['ferias_vencidas'], 2, ",", ".");
                    $total_ferias_aquisitivas += $row_rel['ferias_vencidas'];
                    $total_ferias_a_pagar += $row_rel['ferias_vencidas'];
                    $total_rendimento += $row_rel['ferias_vencidas'];
                    ?></td>  <!-- 66 -->                         
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['umterco_fv'], 2, ",", ".");
                    $total_umterco_fv += $row_rel['umterco_fv'];
                    $total_ferias_a_pagar += $row_rel['umterco_fv'];
                    $total_rendimento += $row_rel['umterco_fv'];
                    ?></td> 
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['umterco_fv'] + $row_rel['umterco_fp'], 2, ",", ".");
                    $total_terco_constitucional += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                    //$total_rendimento += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                    //linha comentada por Renato(13/03/2015) por já estar somando acima
                    //$total_ferias_a_pagar += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
                    ?></td>    <!-- 68 -->              
                <td align="right" class="esconder"><?php
                    
                    if ($row_rel['motivo'] == 61 && $row_rel['fator'] == 'empregador' && $row_rel['aviso'] == 'indenizado') { 
                        echo "R$ " . number_format($valor_aviso, 2, ",", ".");
                        $total_aviso_indenizado += $valor_aviso;
                        $total_rendimento += $valor_aviso;
                    } else {
                        echo "R$ " . number_format(0, 2, ",", ".");
                        $total_aviso_indenizado += 0;
                        $total_rendimento += 0;
                    } 
                    
                    ?></td>    <!-- 69 -->              
                <td align="right" class="esconder"><?php
                    if ($row_rel['motivo'] == 61 && $row_rel['fator'] == 'empregador' && $row_rel['aviso'] == 'indenizado') { 
                        echo "R$ " . number_format($row_rel['terceiro_ss'], 2, ",", ".");
                        $total_terceiro_ss += $row_rel['terceiro_ss'];
                        $total_decimo_a_pagar += $row_rel['terceiro_ss'];
                        $total_rendimento += $row_rel['terceiro_ss'];
                    }else{
                        echo "R$ " . number_format(0, 2, ",", ".");
                        $total_terceiro_ss += 0;
                        $total_decimo_a_pagar += 0;
                        $total_rendimento += 0;
                    }
                    ?></td>   <!-- 70 -->                      
                <td align="right" class="esconder"><?php
                    if ($row_rel['motivo'] == 61 && $row_rel['fator'] == 'empregador' && $row_rel['aviso'] == 'indenizado') { 
                        echo "R$ " . number_format($row_rel['ferias_aviso_indenizado'], 2, ",", ".");
                        $total_f_aviso_indenizado += $row_rel['ferias_aviso_indenizado'];
                        $total_ferias_a_pagar += $row_rel['ferias_aviso_indenizado'];
                        $total_rendimento += $row_rel['ferias_aviso_indenizado'];
                    }else{
                        echo "R$ " . number_format(0, 2, ",", ".");
                        $total_f_aviso_indenizado += 0;
                        $total_ferias_a_pagar += 0;
                        $total_rendimento += 0;
                    }
                    ?></td>              <!-- 71 -->           
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['fv_dobro'], 2, ",", ".");
                    $total_f_dobro += $row_rel['fv_dobro'];
                    $total_rendimento += $row_rel['fv_dobro'];

                    ?></td>  <!-- 72 -->                           
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['um_terco_ferias_dobro'], 2, ",", ".");
                    $total_umterco_f_dobro += $row_rel['um_terco_ferias_dobro'];
                    $total_ferias_a_pagar += $row_rel['um_terco_ferias_dobro'];
                    $total_rendimento  += $row_rel['um_terco_ferias_dobro'];

                    ?></td>  <!-- 73 -->                           
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['umterco_ferias_aviso_indenizado'], 2, ",", ".");
                    $total_umterco_ferias_aviso += $row_rel['umterco_ferias_aviso_indenizado'];
                    $total_ferias_a_pagar += $row_rel['umterco_ferias_aviso_indenizado'];
                    $total_rendimento  += $row_rel['umterco_ferias_aviso_indenizado'];

                    ?></td>   <!-- 82 --> 
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"], 2, ",", ".");
                    $total_diferenca_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"];
                    $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"];

                    ?></td> <!-- 80 -->
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"], 2, ",", ".");
                    $total_ajuda_custo += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"];
                    $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"];

                    ?></td>  <!-- 480 -->                           
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['lei_12_506'], 2, ",", ".");
                    $total_lei_12_506 += $row_rel['lei_12_506'];
                    $total_rendimento  += $row_rel['lei_12_506'];
                    ?></td>  <!-- 95 Lei 12.506-->                           
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"], 2, ",", ".");
                    $total_dif_dissidio += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"];
                    $total_rendimento  += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"];

                    ?></td>  <!-- 200 diferença de dissédio-->                           
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"], 2, ",", ".");
                    $total_vale_transporte += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"];
                    $total_rendimento += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"];
                    ?></td>  <!-- 106 -->                           
                <td align="right" class="esconder"><?php
                    echo "R$ " . number_format($row_rel['arredondamento_positivo'], 2, ",", ".");
                    $total_ajuste_de_saldo += $row_rel['arredondamento_positivo'];
                    $total_rendimento += $row_rel['arredondamento_positivo'];

                    ?></td>  <!-- 99 -->                           
                <td align="right" class="">
                    <?php
                    echo "R$ " . number_format($total_rendimento, 2, ",", ".");
                    $total_grupo_rendimento[$status] += $total_rendimento;


                    //echo "R$ " . number_format($row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
                    //$total_rendimento += $row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
                    ?>
                </td>

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
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($pensao, 2, ",", ".");
                    $total_pensao_alimenticia += $pensao;
                    $total_deducao_debito +=$pensao;
                    $total_deducao = $pensao;
                    ?></td>  <!-- 100 -->                           
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"], 2, ",", ".");
                    $total_adiantamento_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"];
                    $total_deducao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"];
                    ?></td>  <!-- 101 -->                           
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($adiantamento_13, 2, ",", ".");
                    $total_adiantamento_13_salarial += $adiantamento_13;
                    $total_deducao += $adiantamento_13;
                    ?></td>  <!-- 102 -->                           
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
                    $total_aviso_indenizado_debito += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                    $total_deducao += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
                    ?></td>  <!-- 103 -->                           
                <?php
                if ($row_rel['motivo'] == 64) {
                    $multa_480 = null;
                } else if ($row_rel['motivo'] == 63) {
                    $multa_480 = $row_rel['a480']; //$row_rescisao['a480']; 
                }
                ?>
                <td align="right" class="esconderr"><?php
                    
                    echo "R$ " . number_format($multa_480, 2, ",", ".");
                    $total_multa_480 += $multa_480;
                    $total_deducao_debito += $multa_480;
                    $total_deducao += $multa_480;
                    ?></td>  <!-- 104 -->                           
<!--                                    <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format(0, 2, ",", ".");
                    $total_emprestimo_consignado += 0;
                    ?></td>   105                            -->
                <td align="right" class="esconderr">
                    <?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][9999]["valor"], 2, ",", ".");
                    $total_auxilio_distancia_debito += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][9999]["valor"];
                    $total_deducao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][9999]["valor"];
                    ?>
                </td>  <!-- 234 -->  
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7001]["valor"], 2, ",", ".");
                    $total_vale_transporte_debito += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7001]["valor"];
                    ?></td>  <!-- 106 -->  
                <td align="right" class="esconderr"><?php
                    $valor = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8003]["valor"] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][10008]["valor"];
                    echo "R$ " . number_format($valor, 2, ",", ".");
                    $total_vale_alimentacao_debito += $valor;
                    $total_deducao += $valor;
                    ?></td>  <!-- 109 -->  
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($inss_saldo_salario, 2, ",", ".");
                    $total_inss_ss += $inss_saldo_salario;
                    $total_deducao_debito += $inss_saldo_salario;
                    $total_deducao += $inss_saldo_salario;
                    ?></td>  <!-- 112.1 --> 
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($row_rel['inss_dt'], 2, ",", ".");
                    $total_inss_dt += $row_rel['inss_dt'];
                    $total_deducao_debito += $row_rel['inss_dt'];
                    $total_deducao += $row_rel['inss_dt'];
                    ?></td>   <!-- 112.2 -->                     
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($calculos->valor, 2, ",", ".");
                    $total_ir_ss += $calculos->valor;
                    $total_deducao_debito += $calculos->valor;
                    $total_deducao += $calculos->valor;
                    ?></td>   <!-- 114.1 -->                     
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($row_rel['ir_dt'], 2, ",", ".");
                    $total_ir_dt += $row_rel['ir_dt'];
                    $total_deducao_debito += $row_rel['ir_dt'];
                    $total_deducao += $row_rel['ir_dt'];
                    ?></td>    <!-- 114.2 -->                    
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($row_rel['devolucao'], 2, ",", ".");
                    $total_devolucao += $row_rel['devolucao'];
                    $total_deducao_debito += $row_rel['devolucao'];
                    $total_deducao += $row_rel['devolucao'];
                    ?></td>    <!-- 115 -->                    
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format(0, 2, ",", ".");
                    $total_outros += 0;
                    ?></td>    <!-- 115.1 -->                    
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($row_rel['adiantamento_13'], 2, ",", ".");
                    $total_adiantamento_13 += $row_rel['adiantamento_13'];
                    $total_deducao += $row_rel['adiantamento_13'];
                    ?></td>    <!-- 115.2 -->                    

                <?php
                if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
                    $movimento_falta = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
                } else {
                    $movimento_falta = 0;
                }
                ?>
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($row_rel['valor_faltas'] + $movimento_falta, 2, ",", ".");
                    $total_faltas += $row_rel['valor_faltas'] + $movimento_falta;
                    $total_deducao_debito -= $row_rel['valor_faltas'] + $movimento_falta;
                    $total_deducao += $row_rel['valor_faltas'] + $movimento_falta;
                    ?></td>    <!-- 117 -->                    
                <td align="right" class="esconderr"><?php
                    echo "R$ " . number_format($row_rel['ir_ferias'], 2, ",", ".");
                    $total_ir_ferias += $row_rel['ir_ferias'];
                    $total_deducao_debito += $row_rel['ir_ferias'];
                    $total_deducao += $row_rel['ir_ferias'];

                    ?></td>    <!-- 116 -->                    
                <td align="right" class=""><?php 
                    $total_grupo_deducao[$status] += $total_deducao;
                    echo "R$ " . number_format($total_deducao, 2, ",", ".");
                    ?></td> <!--echo "R$ " . number_format($total_deducao_debito + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", "."); $total_deducao += $total_deducao_debito + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']; -->         
                <td align="right">
                    <?php
                    //@jacques 06/08/2015 - Foram criadas algumas variáveis de totalizacao parcial e geral para os campos total das deducões parciais e gerais e valor rescisório líquido
                    echo "R$ " . number_format($total_rendimento - $total_deducao, 2, ",", ".");

                    ?>
                </td>  

                <!-- OUTROS VALORES -->
                <!-- BASES -->

                <?php


                /**
                 * 09/11/2015 - Jacques
                 * Total Linha PIS 
                 * 
                 * Obs: Segundo o Milton a base do PIS incide apenas em cima do 13o 
                 * 
                 *      A base de INSS e FGTS é apurada em cima do 13 + Aviso + Lei
                 * 
                 * 22/01/2016 - Segundo a interpretação que fiz na afirmativa acima do Milton na data supra-cita não apliquei ao valor acumulado trazido pela classe
                 * 
                 * 
                 * 13/12/2016 - Nessa data ouve nova definição para criação das bases de fgts, inss e pis definidas por Michele onde:
                 *              base_fgts = ($row_rel['saldo_salario']+$row_rel['terceiro_ss']+$row_rel['dt_salario']+$row_rel['lei_12_506']+$aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']+$total_movimentos_incide_fgts)-($row_rel['valor_faltas']+$movimento_falta)
                 *              base_pis  = base_fgts
                 *              base_inss = $total_rendimento - ($row_rel['ferias_pr']+$row_rel['ferias_vencidas']+$row_rel['sal_familia']+$row_rel['umterco_fv']+$row_rel['ferias_aviso_indenizado']);
                 * 
                 */
                //($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] + $row_rel['insalubridade'] + $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"]) * 0.01
                //($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01
                //$base_pis  = $total_rendimento - $row_rel['lei_12_506'] - $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']  - $row_rel['sal_familia'];

                // Bases para calculo de PIS, FGTS e INSS 
                //echo $total_rendimento.'<br>';
                //echo $row_rel['lei_12_506'].'<br>';
                //echo $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'].'<br>';
                //echo $row_rel['sal_familia'].'<br>';


                //echo 'saldo_salario='.$row_rel['saldo_salario'].'<br>';
                //echo 'terceiro_ss='.$row_rel['terceiro_ss'].'<br>';
                //echo 'dt_salario='.$row_rel['dt_salario'].'<br>';
                //echo 'lei_12_506='.$row_rel['lei_12_506'].'<br>';
                //echo 'aviso='.$aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'].'<br>';
                //echo 'getMultaFgts='.$folha->getMultaFgts($row_rel['id_clt']);
                
//                echo 'Base FGTS:<br> ';
//                echo 'Retenção/Multa: '.number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", ".").'<br>';
//                echo '<br>';
//                echo '+Saldo Salário: '.number_format($row_rel['saldo_salario'], 2, ",", ".").'<br>';
//                echo '+Insalubridade: '.number_format($row_rel['insalubridade'], 2, ",", ".").'<br>';
//                echo '+13 Saldo Sal.: '.number_format($row_rel['terceiro_ss'], 2, ",", ".").'<br>';
//                echo '+13 Salário   : '.number_format($row_rel['dt_salario'], 2, ",", ".").'<br>';
//                echo '+Lei 12.506   : '.number_format($row_rel['lei_12_506'], 2, ",", ".").'<br>';
//                echo '+Ad. Noturno  : '.number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"], 2, ",", ".").'<br>'; // Adicional Noturno
//                echo '+DSR          : '.number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"], 2, ",", ".").'<br>'; // DSR
//                echo '+Hora Extra   : '.number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"], 2, ",", ".").'<br>'; // Hora Extra
//                echo '+Aviso        : '.number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".").'<br>';
//                echo '<br>';
//                echo '-Faltas/Atraso : '.number_format($row_rel['valor_faltas'], 2, ",", ".").'<br>';
                
                
                $base_fgts =   (
                                    $row_rel['saldo_salario']
                                   +$row_rel['insalubridade']
                                   +$row_rel['terceiro_ss']
                                   +$row_rel['dt_salario']
                                   +$row_rel['lei_12_506']
                                   +$mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"] // Adicional Noturno
                                   +$mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"] // DSR
                                   +$mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"] // Hora Extra
                                   +$aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']
                                   )
                                   -($row_rel['valor_faltas']
                               ); 

//                echo '<br>';
//                echo '<br>';
//                echo 'Base INSS:<br>';
//                echo '<br>';
//                echo 'Total           : '.number_format($total_rendimento, 2, ",", ".").'<br>';
//                echo 'Férias P.       : '.number_format($row_rel['ferias_pr'], 2, ",", ".").'<br>';
//                echo 'Férias V.       : '.number_format($row_rel['ferias_vencidas'], 2, ",", ".").'<br>';
//                echo 'Férias 1/3      : '.number_format($row_rel['umterco_fv'], 2, ",", ".").'<br>';
//                echo 'Férias 1/3 Prop.: '.number_format($row_rel['umterco_fp'], 2, ",", ".").'<br>';
//                echo 'Férias Ind      : '.number_format($row_rel['ferias_aviso_indenizado'], 2, ",", ".").'<br>';
//                echo 'Sal.Fam.        : '.number_format($row_rel['sal_familia'], 2, ",", ".").'<br>';
                
                $base_inss = $total_rendimento - ($row_rel['ferias_pr']+$row_rel['ferias_vencidas']+$row_rel['umterco_fv']+$row_rel['umterco_fp']+$row_rel['umterco_ferias_aviso_indenizado']+$row_rel['ferias_aviso_indenizado']+$row_rel['sal_familia']+$row_rel['a479']);
                $base_pis  = $base_inss;

                // Fatores aplicados as bases
                $empresa['pis'] = $base_pis * 0.01;
                $empresa['multa_fgts'] = (($row_rel['motivo'] == 61 || $row_rel['motivo'] == 64) && $row_rel['fator'] == "empregador" ? (($base_fgts * 0.08)*0.5) + $folha->getMultaFgts($row_rel['id_clt']) : 0);
                $empresa['inss_empresa'] = $base_inss * 0.20; 
                $empresa['inss_rat'] = $base_inss * $row_rel['taxaRAT']; 
                $empresa['inss_terceiro'] = $base_inss * 0.058;
                $empresa['fgts_recolher'] = $base_fgts * 0.08;
                

               ?>
                <td align="right" class="esconderrr"><?php
                    echo "R$ " . number_format($base_inss, 2, ",", ".");
                    ?></td> 
                <td align="right" class="esconderrr"><?php
                    echo "R$ " . number_format($base_fgts, 2, ",", ".");
                    ?></td> 
                <td align="right" class="esconderrr"><?php
                    echo "R$ " . number_format($base_pis, 2, ",", ".");
                    ?></td> 
                <td align="right" style="background: #fff; border: 0px;"></td>                       
                <td align="right">                        
                    <?php
                    echo "R$ " . number_format($empresa['pis'],2,',','.'); 

                    $total_pis += $empresa['pis'];

                    $total_pis_a_pagar[$row_rel['codigo']] += $empresa['pis'];

                    ?>
                   </td>                       
                <td align="right">
                    <nobr>
                    <?php
                    echo "R$ " . number_format($empresa['multa_fgts'], 2, ",", ".");

                    $total_multa_fgts += $empresa['multa_fgts'];

                    $total_multa_a_pagar[$row_rel['codigo']] += $empresa['multa_fgts'];

                    ?>
                    <a href="javascript:;" data-id_clt="<?=$row_rel['id_clt']?>" data-nome="<?=$row_rel['nome']?>" data-saldo_salario="<?=$row_rel['saldo_salario']?>" data-terceiro_ss="<?=$row_rel['terceiro_ss']?>" data-dt_salario="<?=$row_rel['dt_salario']?>" data-lei_12_506="<?=$row_rel['lei_12_506']?>" data-aviso="<?=$aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']?>" data-total_movimentos_incide_fgts="<?=$row_rel['total_movimentos_incide_fgts']?>" data-valor_faltas="<?=$row_rel['valor_faltas']?>"  data-movimento_falta="<?=$row_rel['movimento_falta']?>" class="lista_recolhimento_fgts" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-view.gif" title="Listar retenções do FGTS"></a>                                        
                    </nobr>
                </td>                       
                <td align="right"> 
                    <?php
                    echo "R$ " . number_format($empresa['inss_empresa'], 2, ",", ".");

                    $total_inss_empresa += $empresa['inss_empresa'];

                    $total_inss_empresa_a_pagar[$row_rel['codigo']] += $empresa['inss_empresa'];

                    ?>
                </td>  
                <td align="right"> 
                    <?php
                    echo "R$ " . number_format($empresa['inss_rat'], 2, ",", ".");

                    $total_inss_rat += $empresa['inss_rat'];

                    $total_inss_rat_a_pagar[$row_rel['codigo']] += $empresa['inss_rat'];

                    ?>
                </td>  
                <td align="right">
                    <?php
                    echo "R$ " . number_format($empresa['inss_terceiro'], 2, ",", ".");

                    $total_inss_terceiro += $empresa['inss_terceiro'];

                    $total_inss_terceiro_a_pagar[$row_rel['codigo']] += $empresa['inss_terceiro'];

                    ?>
                </td>  
                <td align="right">
                    <?php
                    echo "R$ " . number_format($empresa['fgts_recolher'], 2, ",", ".");

                    $total_fgts_recolher += $empresa['fgts_recolher'];

                    $total_fgts_recolher_a_pagar[$row_rel['codigo']] += $empresa['fgts_recolher'];

                    ?>
                </td>
            </tr>                                

        <?php } 

        $total_recisao_nao_paga += $total_liquido;
        /*
         * Impressao dos totalizadores de grupo da tabela
         */
        ?>
        <tfoot>
            <tr class="footer">
                <td align="right" colspan="7">Total:</td>
                <td align="right"><?php echo "R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", "."); ?></td>
                <td align="right"><?php echo "R$ " . number_format($total_sal_base, 2, ",", "."); ?></td>
                <!--<td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>-->
                <td align="right"><?php echo "R$ " . number_format($total_saldo_salario, 2, ",", "."); ?></td>

                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_comissoes, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gratificacao, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_insalubridade, 2, ",", "."); ?></td> 
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_periculosidade, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_adicional_noturno, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_hora_extra, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_gorjetas, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dsr, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_reflexo_dsr, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_477, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_multa_479, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_sal_familia, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_diferenca_salarial, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuda_custo, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_dif_dissidio, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_vale_transporte, 2, ",", "."); ?></td>
                <td align="right" class="esconder"><?php echo "R$ " . number_format($total_ajuste_de_saldo, 2, ",", "."); ?></td>
                <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status], 2, ",", "."); ?></td>


                <!-- DEDUCOES  -->
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_pensao_alimenticia, 2, ",", "."); ?></td>
                <td align="right" class="esconderr" ><?php echo "R$ " . number_format($total_adiantamento_salarial, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_multa_480, 2, ",", "."); ?></td>
                <!--<td align="right" class="esconderr"><?php echo "R$ " . number_format($total_emprestimo_consignado, 2, ",", "."); ?></td>-->
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_auxilio_distancia_debito, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_transporte_debito, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_vale_alimentacao_debito, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_ss, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ss, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_devolucao, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_outros, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_faltas, 2, ",", "."); ?></td>
                <td align="right" class="esconderr"><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>
                <td align="right"><?php echo "R$ " . number_format($total_grupo_deducao[$status], 2, ",", "."); ?></td>
                <td align="right"><?php echo "R$ " . number_format($total_grupo_rendimento[$status] - $total_grupo_deducao[$status], 2, ",", "."); ?></td>


                <!-- DETALHES IMPORTANTES-->
                <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_inss, 2, ",", "."); ?></td>
                <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_fgts, 2, ",", "."); ?></td>
                <td align="right" class="esconderrr"><?php echo "R$ " . number_format($total_base_pis, 2, ",", "."); ?></td>
                <td align="right" style="background: #fff; border: 0px;"></td>                       
                <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                <td align="right"><?php echo "R$ " . number_format($total_inss_rat, 2, ",", "."); ?></td> 
                <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
            </tr>
        </tfoot>
    </table>
    <?php 
    /*
     * Impressao do div com totalizadores de grupo
     */

    foreach ($status_array as $status_clt) { 
    ?>
        <div class="totalizador">
            <p class="titulo">TOTALIZADORES (<?php echo $nome_status_array[$status_clt]; ?>)</p>
            <p>PIS: <span><?php
                    echo "R$ " . number_format($total_pis_a_pagar[$status_clt], 2, ",", ".");
                    $total_geral_pis += $total_pis_a_pagar[$status_clt];
                    ?></span></p>
            <p>GRRF: <span><?php
                    echo "R$ " . number_format($total_multa_a_pagar[$status_clt], 2, ",", ".");
                    $total_geral_multa += $total_multa_a_pagar[$status_clt];
                    ?></span></p>
            <p>FGTS RECOLHER: <span><?php
                    echo "R$ " . number_format($total_fgts_recolher_a_pagar[$status_clt], 2, ",", ".");
                    $total_geral_fgts_recolher += $total_fgts_recolher_a_pagar[$status_clt];
                    ?></span></p>
            <p>INSS RECOLHER EMPRESA: <span><?php
                    echo "R$ " . number_format($total_inss_empresa_a_pagar[$status_clt], 2, ",", ".");
                    $total_geral_inss_emp += $total_inss_empresa_a_pagar[$status_clt];
                    ?></span></p>
            <p>INSS RAT (<?=$taxaRAT?>%): <span><?php
                    echo "R$ " . number_format($total_inss_rat_a_pagar[$status_clt], 2, ",", ".");
                    $total_geral_rat_emp += $total_inss_rat_a_pagar[$status_clt];
                    ?></span></p>
            <p>INSS RECOLHER TERCEIRO: <span><?php
                    echo "R$ " . number_format($total_inss_terceiro_a_pagar[$status_clt], 2, ",", ".");
                    $total_geral_inss_terceiro += $total_inss_terceiro_a_pagar[$status_clt];
                    ?></span></p>

            <p class="semborda">(+) SUBTOTAL: <span><?php
                    echo "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_rat_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt], 2, ",", ".");
                    $sub_total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_rat_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt];
                    ?></span></p>
            <p>(+) TOTAL A SER PAGO(RESCISOES): <span><?php
                    // Total a ser pago
                    $total_geral_a_ser_pago += ($total_a_ser_pago[$status_clt] += $total_grupo_rendimento[$status_clt] - $total_grupo_deducao[$status_clt]);
                    echo "R$ " . number_format($total_a_ser_pago[$status_clt], 2, ",", ".");
                    ?></span></p>
            <p class="semborda">(=) TOTAL: <span><?php
                    echo "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_rat_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt], 2, ",", ".");
                    $total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_rat_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt];
                    ?></span></p>
        </div>
    <?php 
    } 
    ?>

    <div class="totalizador">
        <p class="titulo">TOTALIZADOR GERAL</p>
        <p>PIS: <span><?php echo "R$ " . number_format($total_geral_pis, 2, ",", "."); ?></span></p>
        <p>GRRF: <span><?php echo "R$ " . number_format($total_geral_multa, 2, ",", "."); ?></span></p>
        <p>FGTS RECOLHER: <span><?php echo "R$ " . number_format($total_geral_fgts_recolher, 2, ",", "."); ?></span></p>
        <p>INSS RECOLHER EMPRESA: <span><?php echo "R$ " . number_format($total_geral_inss_emp, 2, ",", "."); ?></span></p>
        <p>INSS RECOLHER RAT (<?=$taxaRAT?>%):<span><?php echo "R$ " . number_format($total_geral_rat_emp, 2, ",", "."); ?></span></p>
        <p>INSS RECOLHER TERCEIRO: <span><?php echo "R$ " . number_format($total_geral_inss_terceiro, 2, ",", "."); ?></span></p>

        <p class="semborda">(+) SUBTOTAL: <span><?php echo "R$ " . number_format($sub_total_geral, 2, ",", "."); ?></span></p>
        <p>(+) TOTAL A SER PAGO(RESCISOES): <span><?php echo "R$ " . number_format($total_geral_a_ser_pago, 2, ",", "."); ?></span></p>
        <p class="semborda">(=) TOTAL: <span><?php echo "R$ " . number_format($sub_total_geral + $total_geral_a_ser_pago, 2, ",", "."); ?></span></p>
        <p class="semborda">MARGEM DE ERRO DE 3% : <span ><?php echo "R$ " . number_format(($sub_total_geral + $total_geral_a_ser_pago) + (($sub_total_geral + $total_geral_a_ser_pago) * 0.03), 2, ",", "."); ?></span></p>
    </div>
    <div id='totalizador' style='height: auto; text-align: center; clear: both;'>
        <input type='button' id='confirmar_rescisao1' value='Gerar Rescisão' class='class_button'>
        <input type='submit' style='display: none;' id='confirmar_rescisao' name='confirmar_rescisao' value='Gerar Rescisão' class='class_button'>
    </div>
</div>
<?php } ?>


