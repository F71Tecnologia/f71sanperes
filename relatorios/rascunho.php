                    <?php if (isset($_REQUEST['mostrar_prov_trab']) && $num_rows > 0) { ?>
    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Previsão de gastos')" value="Exportar para Excel" class="exportarExcel"></p>    
    <h3><?php echo $projeto['nome'] ?></h3>    
    <!--<p>Total de participantes: <b><?php //echo $total_participantes["total_participantes"];  ?></b></p>-->
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
        <?php $status = 0; ?>

        <?php
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
                    echo "<!-- QUERY DE TOTAL DE RENDIMENTOS::: {$movimentos} -->";

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
            ////////// CÁLCULO DE INSS /////////////
            ///////////////////////////////////////////////
            $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
            $data_exp = explode('-', $row_rel['data_demi']);
            if ($base_saldo_salario > 0) {
                $calculos->MostraINSS($base_saldo_salario, implode('-', $data_exp));
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

            if ($status != $row_rel["codigo"]) {
                $status = $row_rel["codigo"];
                ?>

                <?php if (!empty($total_sal_base)) { ?>
                    <?php
                    if ($row_rel['codigo'] != 20) {
                        $total_recisao_nao_paga += $total_liquido;
                    }
                    ?>
                    <tfoot>
                        <tr class="footer">
                            <td align="right" colspan="3">Total:</td>
                            <td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>    
                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>    
                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>    
                            <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>    
                            
                            <td align="right" ><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>

                            <!-- TOTAL DE DEDUÇÃO -->

                            <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>


                            <!-- DETALHES IMPORTANTES -->
                            <!-- BASES -->                        

                            <td align="right" style="background: #fff; border: 0px;"></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                            <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                            <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
                        </tr>
                        <tr>
                            <td colspan="37" style="border: 0px;"></td>
                        </tr>
                    </tfoot>

                <?php } else { ?>
                    <tfoot>
                        <tr class="footer">
                            <td colspan="74"></td>
                        </tr>
                    </tfoot>                    
                <?php } ?>
                <thead>
                    <tr>
                        <th colspan="3" class="cabecalho_compactar"><?php echo "<p style='text-transform:uppercase; text-align:left' > » " . $row_rel['especifica'] . " - " . $row_rel['aviso'] . "</p>"; ?></th>
                        <th colspan="15">Verbas Rescisórias</th>
                        <!--<th colspan="6">Deduções</th>-->
                        <th style="background: #fff; border: 0px;" ></th>
                        <th colspan="5">EMPRESA</th>
                    </tr>
                    <tr style="font-size:10px !important;">
                        <th rowspan="2">AÇÃO</th>
                        <th rowspan="2">ID</th>
                        <th rowspan="2"><span class="numero_rescisao">[11]</span>NOME</th>

                        <th rowspan="2">VALOR AVISO</th>  

                        <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->

                        <th rowspan="2" ><span class="numero_rescisao">[63]</span>13º Salário Proporcional</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[64]</span>13º Salário Exercício</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[70]</span>13º Salário (Aviso-Prévio Indenizado)</th> 
                        <th rowspan="2" ><span class="numero_rescisao">[65]</span>Férias Proporcionais</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS PROPORCIONAL </th> 
                        <th rowspan="2" ><span class="numero_rescisao">[66]</span>Férias Vencidas Per. Aquisitivo</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[]</span>1/3 DE FÉRIAS VENCIDAS</th> 
                        <th rowspan="2" ><span class="numero_rescisao">[68]</span>Terço Constitucional de Férias</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[69]</span>Aviso Prévio indenizado</th>  
                         
                        <th rowspan="2" ><span class="numero_rescisao">[71]</span>Férias (Aviso-Prévio Indenizado)</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[72]</span>Férias em dobro</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[73]</span>1/3 férias em dobro</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[82]</span> 1/3 DE FÉRIAS AVISO INDENIZADO </th>
                        <th rowspan="2" ><span class="numero_rescisao">[95]</span>Lei 12.506</th>  

                        <!--DEDUÇÕES--->

<!--                        <th rowspan="2" ><span class="numero_rescisao">[102]</span>Adiantamento de 13º Salário</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[103]</span>Aviso-Prévio Indenizado</th>  
                         <th rowspan="2" ><span class="numero_rescisao">[112.2]</span>Previdência Social - 13º Salário</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[114.2]</span>IRRF sobre 13º Salário</th>  
                        <th rowspan="2" ><span class="numero_rescisao">[115.2]</span>Adiantamento de 13º Salário</th>
                        <th rowspan="2" ><span class="numero_rescisao">[116]</span>IRRF Férias</th>  -->

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
                //VERBAS RESCISÓRIAS
//                $total_das_medias_outras_remuneracoes = 0;
//                $total_sal_base = 0;
//                $total_valor_aviso = 0;
//                $total_saldo_salario = 0;
//                $total_comissoes = 0;
//                $total_gratificacao = 0;
//                $total_insalubridade = 0;
//                $total_periculosidade = 0;
//                $total_adicional_noturno = 0;
//                $total_hora_extra = 0;
//                $total_gorjetas = 0;
//                $total_dsr = 0;
//                $total_reflexo_dsr = 0;
//                $total_multa_477 = 0;
//                $total_multa_479 = 0;
//                $total_sal_familia = 0;
//                $total_dt_salario = 0;
//                $total_terceiro_exercicio = 0;
//                $total_ferias_pr = 0;
//                $total_ferias_aquisitivas = 0;
//                $total_terco_constitucional = 0;
//                $total_aviso_indenizado = 0;
//                $total_terceiro_ss = 0;
//                $total_f_aviso_indenizado = 0;
//                $total_f_dobro = 0;
//                $total_umterco_f_dobro = 0;
//                $total_diferenca_salarial = 0;
//                $total_ajuda_custo = 0;
//                $total_lei_12_506 = 0;
//                $total_dif_dissidio = 0;
//                $total_vale_transporte = 0;
//                $total_ajuste_de_saldo = 0;
//                $total_rendimento = 0;


                //DEDUÇÕES
//                $total_pensao_alimenticia = 0;
//                $total_adiantamento_salarial = 0;
//                $total_adiantamento_13_salarial = 0;
//                $total_aviso_indenizado_debito = 0;
//                $total_multa_480 = 0;
//                $total_emprestimo_consignado = 0;
//                $total_vale_transporte_debito = 0;
//                $total_vale_alimentacao_debito = 0;
//                $total_inss_ss = 0;
//                $total_inss_dt = 0;
//                $total_ir_ss = 0;
//                $total_ir_dt = 0;
//                $total_devolucao = 0;
//                $total_outros = 0;
//                $total_adiantamento_13 = 0;
//                $total_faltas = 0;
//                $total_ir_ferias = 0;
//                $total_deducao = 0;
//                $total_liquido = 0;


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
                $total_inss_terceiro = 0;
                $total_fgts_recolher = 0;

//                //TOTALIZADOR FÉRIAS
//                $total_ferias_a_pagar = 0;
//
//                //TOTALIZADOR 13° 
//                $total_decimo_a_pagar = 0;
                ?>

            <?php } ?>

                     <tr class="<?php echo $class ?>" style="font-size:11px;">
                        <td align="left"><a href="javascript:;" class="lanca_movimento" data-rescisao="<?php echo $row_rel['id_recisao']; ?>" data-clt="<?php echo $row_rel['id_clt']; ?>"><img src="../imagens/icones/icon-view-dis.gif" title="lancar_movimentos" /></a></td>
                        <td align="left"><?php echo $row_rel['id_clt']; ?></td>
                        <td align="left"><a href='javascript:;' data-key='<?php echo $row_rel['id_clt']; ?>' data-nome='<?php echo $row_rel['nome']; ?>' class='calcula_multa' style='color: #4989DA; text-decoration: none;'><?php echo $row_rel['nome']; ?></a></td>
                        <td align="left" class="">
                            <?php 
                                if($row_rel['motivo'] != 60){
                                    $valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade']; echo "R$ " . number_format($valor_aviso, 2, ",", ".");  $total_valor_aviso += $valor_aviso;  
                                }else{
                                    $valor_aviso = 0; echo "R$ " . number_format($valor_aviso, 2, ",", ".");  $total_valor_aviso += $valor_aviso;  
                                }
                            ?>
                        </td>
                        
                        <?php if($row_rel['fator'] == "empregador"){
                            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
                        }else if($row_rel['fator'] == "empregado"){
                            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
                        } ?>  
                        
                        <!--DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS--->
                        <?php  if($row_rel['motivo'] == 64){$multa_479 = $row_rel['a479']; }else if($row_rel['motivo'] == 63){$multa_479 = null;} ?>
                        <td align="right" ><?php echo "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] <br /> R$ " . number_format($row_rel['dt_salario'], 2, ",", "."); $total_dt_salario += $row_rel['dt_salario']; $total_decimo_a_pagar += $row_rel['dt_salario'];  ?></td> <!-- 63 -->                      
                        <td align="right" ><?php echo "R$ " . number_format(0, 2, ",", "."); $total_terceiro_exercicio += 0; $total_decimo_a_pagar += 0; ?></td>    <!-- 64 -->   
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['terceiro_ss'], 2, ",", "."); $total_terceiro_ss += $row_rel['terceiro_ss']; $total_decimo_a_pagar +=  $row_rel['terceiro_ss']; ?></td>   <!-- 70 -->                      
                        <td align="right" ><?php echo "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] <br /> R$ " . number_format($row_rel['ferias_pr'], 2, ",", "."); $total_ferias_pr += $row_rel['ferias_pr']; $total_ferias_a_pagar += $row_rel['ferias_pr']; ?></td>  <!-- 65 -->  
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['umterco_fp'], 2, ",", "."); $total_umterco_fp += $row_rel['umterco_fp']; $total_ferias_a_pagar += $row_rel['umterco_fp']; ?></td> 
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['ferias_vencidas'], 2, ",", "."); $total_ferias_aquisitivas += $row_rel['ferias_vencidas']; $total_ferias_a_pagar += $row_rel['ferias_vencidas']; ?></td>  <!-- 66 -->                         
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['umterco_fv'], 2, ",", "."); $total_umterco_fv += $row_rel['umterco_fv']; $total_ferias_a_pagar += $row_rel['umterco_fv']; ?></td> 
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['umterco_fv'] + $row_rel['umterco_fp'], 2, ",", "."); $total_terco_constitucional += $row_rel['umterco_fv'] + $row_rel['umterco_fp']; $total_ferias_a_pagar += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];   ?></td>    <!-- 68 -->              
                        <td align="right" ><?php echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", "."); $total_aviso_indenizado += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']; ?></td>    <!-- 69 -->              
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['ferias_aviso_indenizado'], 2, ",", "."); $total_f_aviso_indenizado += $row_rel['ferias_aviso_indenizado']; $total_ferias_a_pagar += $row_rel['ferias_aviso_indenizado']; ?></td>              <!-- 71 -->           
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['fv_dobro'], 2, ",", "."); $total_f_dobro += $row_rel['fv_dobro']; ?></td>  <!-- 72 -->                           
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['um_terco_ferias_dobro'], 2, ",", "."); $total_umterco_f_dobro += $row_rel['um_terco_ferias_dobro']; $total_ferias_a_pagar += $row_rel['um_terco_ferias_dobro']; ?></td>  <!-- 73 -->                           
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['umterco_ferias_aviso_indenizado'], 2, ",", "."); $total_umterco_ferias_aviso += $row_rel['umterco_ferias_aviso_indenizado']; $total_ferias_a_pagar += $row_rel['umterco_ferias_aviso_indenizado']; ?></td>   <!-- 82 --> 
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['lei_12_506'], 2, ",", "."); $total_lei_12_506 += $row_rel['lei_12_506']; ?></td>  <!-- 95 -->                           
                        
                        <!--DEDUÇÕES--->
                        
                        <?php if(isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"])){ $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"];}elseif(isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50222]["valor"])){ $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][50222]["valor"]; }elseif(isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"])){ $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"];}else{ $pensao = 0;} ?>
<!--                        <td align="right" ><?php echo "R$ " . number_format(0, 2, ",", "."); $total_adiantamento_13_salarial += 0; ?></td>   102                            
                        <td align="right" ><?php echo "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", "."); $total_aviso_indenizado_debito += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']; ?></td>   103                            -->
                        <?php  if($row_rel['motivo'] == 64){$multa_480 = null; }else if($row_rel['motivo'] == 63){$multa_480 = $row_rescisao['a480'];} ?>
<!--                        <td align="right" ><?php echo "R$ " . number_format($row_rel['inss_dt'], 2, ",", "."); $total_inss_dt += $row_rel['inss_dt']; $total_deducao_debito += $row_rel['inss_dt']; ?></td>    112.2                      
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['ir_dt'], 2, ",", "."); $total_ir_dt += $row_rel['ir_dt']; $total_deducao_debito += $row_rel['ir_dt']; ?></td>     114.2                     
                        <td align="right" ><?php echo "R$ " . number_format($row_rel['adiantamento_13'], 2, ",", "."); $total_adiantamento_13 += $row_rel['adiantamento_13']; ?></td>     115.2                     -->
                        
                        <?php if(isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])){ $movimento_falta = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"]; }else{$movimento_falta = 0;} ?>
                        <!--<td align="right" ><?php echo "R$ " . number_format($row_rel['ir_ferias'], 2, ",", "."); $total_ir_ferias += $row_rel['ir_ferias']; $total_deducao_debito += $row_rel['ir_ferias']; ?></td>     116 -->                    
                        
                        <!-- OUTROS VALORES -->
                        <!-- BASES -->
                        
                        <td align="right" style="background: #fff; border: 0px;"></td>                       
                        <td align="right">
                            <?php 
                            echo "R$ " . number_format(($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01, 2, ",", "."); 
                            $total_pis += ( $row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01; 
                            foreach ($status_array as $status_clt){
                                if($row_rel['codigo'] == $status_clt){ 
                                    $total_pis_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
                                }
                            }
                            
                            ?>
                        </td>                       
                        <td align="right">
                            <?php 
                                echo "R$ " . number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", "."); 
                                $total_multa_fgts += $folha->getMultaFgts($row_rel['id_clt']); 
                                foreach ($status_array as $status_clt){
                                    if($row_rel['codigo'] == $status_clt){ 
                                        if($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador"){
                                            $total_multa_a_pagar[$status_clt] += $folha->getMultaFgts($row_rel['id_clt']);
                                        }
                                    }
                                }   
                            ?>
                        </td>                       
                        <td align="right">
                            <?php 
                            echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20, 2, ",", "."); 
                            $total_inss_empresa += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20; 
                            foreach ($status_array as $status_clt){
                                if($row_rel['codigo'] == $status_clt){ 
                                    $total_inss_empresa_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20; 
                                }   
                            }
                            ?>
                        </td>  
                        <td align="right">
                            <?php 
                            echo "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068, 2, ",", "."); 
                            $total_inss_terceiro += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068; 
                            foreach ($status_array as $status_clt){    
                                if($row_rel['codigo'] == $status_clt){ 
                                    $total_inss_terceiro_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
                                }
                            }
                            ?>
                        </td>  
                        <td align="right">
                            <?php 
                            echo "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide +  $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08, 2, ",", "."); 
                            $total_fgts_recolher += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide +  $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08; 
                            foreach ($status_array as $status_clt){    
                                if($row_rel['codigo'] == $status_clt){ 
                                    $total_fgts_recolher_a_pagar[$status_clt] += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide +  $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
                                }
                            }
                            ?>
                        </td>
                    </tr>                                

    <?php } ?>
    <?php
    $total_recisao_nao_paga += $total_liquido;
    ?>
        <tfoot>
            <tr class="footer">
                <td align="right" colspan="3">Total:</td>
                <td align="right"><?php echo "R$ " . number_format($total_valor_aviso, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_dt_salario, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_terceiro_exercicio, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_terceiro_ss, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_ferias_pr, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_umterco_fp, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_ferias_aquisitivas, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_umterco_fv, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_terco_constitucional, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_f_aviso_indenizado, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_f_dobro, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_umterco_f_dobro, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_umterco_ferias_aviso, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></td>

                <!-- DEDUÇÕES  -->

<!--                <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                <td align="right" ><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>-->

                <!-- DETALHES IMPORTANTES-->

                <td align="right" style="background: #fff; border: 0px;"></td>                       
                <td align="right"><?php echo "R$ " . number_format($total_pis, 2, ",", "."); ?></td>                       
                <td align="right"><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></td>                       
                <td align="right"><?php echo "R$ " . number_format($total_inss_empresa, 2, ",", "."); ?></td> 
                <td align="right"><?php echo "R$ " . number_format($total_inss_terceiro, 2, ",", "."); ?></td> 
                <td align="right"><?php echo "R$ " . number_format($total_fgts_recolher, 2, ",", "."); ?></td> 
            </tr>
        </tfoot>
    </table>
    <div class="totalizador">
        <p class="titulo">TOTALIZADORES<!--DEMONSTRATIVO FÉRIAS E 13° SALÁRIO--></p>
        <p>FÉRIAS: <span><?php echo "R$ " . number_format($total_ferias_a_pagar, 2, ",", "."); ?></span></p>
        <p>13° SALÁRIO: <span><?php echo "R$ " . number_format($total_decimo_a_pagar, 2, ",", "."); ?></span></p>
        <p>PROVISÃO RESCISÕES: <span><?php echo "R$ " . number_format($total_aviso_indenizado+$total_multa_fgts+$total_lei_12_506, 2, ",", "."); ?></span></p>
        <p>AVISO PRÉVIO: <span><?php echo "R$ " . number_format($total_aviso_indenizado, 2, ",", "."); ?></span></p>
        <p>MULTA FGTS: <span><?php echo "R$ " . number_format($total_multa_fgts, 2, ",", "."); ?></span></p>
        <p>LEI 12/506: <span><?php echo "R$ " . number_format($total_lei_12_506, 2, ",", "."); ?></span></p>
        <p>PROVISÃO INSS S/PROV. TRABALISTA: <span><?php echo "R$ " . number_format(($total_decimo_a_pagar+$total_aviso_indenizado+$total_lei_12_506)*0.268, 2, ",", "."); ?></span></p>
        <p>PROVISÃO FGTS S/PROV. TRABALISTA: <span><?php echo "R$ " . number_format(($total_decimo_a_pagar+$total_aviso_indenizado+$total_lei_12_506)*0.08, 2, ",", "."); ?></span></p>
        <p>PROVISÃO PIS S/PROV. TRABALISTA: <span><?php echo "R$ " . number_format($total_decimo_a_pagar*0.01, 2, ",", "."); ?></span></p>
    </div>

<?php } ?>