<?php

//if (!isset($_COOKIE['logado'])) {
//    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
//    exit;
//}
//
//
//echo '<pre>';
//print_r($_REQUEST);
//echo '</pre>';

/* pega um valor inteiro e transforma nas representaçoes de colunas do excel
 * @$int - integer começa em 0
 */


function getLetraCol($int = 0) {
    $letrasCol = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ', 'GA', 'GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ', 'GK', 'GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GV', 'GW', 'GX', 'GY', 'GZ', 'HA', 'HB', 'HC', 'HD', 'HE', 'HF', 'HG', 'HH', 'HI', 'HJ', 'HK', 'HL', 'HM', 'HN', 'HO', 'HP', 'HQ', 'HR', 'HS', 'HT', 'HU', 'HV', 'HW', 'HX', 'HY', 'HZ', 'IA', 'IB', 'IC', 'ID', 'IE', 'IF', 'IG', 'IH', 'II', 'IJ', 'IK', 'IL', 'IM', 'IN', 'IO', 'IP', 'IQ', 'IR', 'IS', 'IT', 'IU', 'IV', 'IW', 'IX', 'IY', 'IZ',);
    return $letrasCol[$int];
}

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Sao_Paulo');

if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once '../classes/phpexcel/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();


// Set document properties
$objPHPExcel->getProperties()->setCreator("INTRANET")
        ->setLastModifiedBy("INTRANET")
        ->setTitle("Provisao de Gastos")
        ->setSubject("Provisao de Gastos")
        ->setDescription("Arquivo gerado pelo sistema.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Planilha");


// largura das colunas
foreach (range(0, 79) as $columnID) {
    $objPHPExcel->getActiveSheet()->getColumnDimension(getLetraCol($columnID))
            ->setAutoSize(true);
}

//estilos para linha de titulo
$header = new PHPExcel_Style();
$header->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => '99999999')
            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
            )
));


$row_total = new PHPExcel_Style();
$row_total->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'CCCCCCCC')
            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
            )
));

$erase = new PHPExcel_Style();
$erase->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFFFFFFF')
            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
            )
));

$par = new PHPExcel_Style();
$par->applyFromArray(
        array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'EEEEEEEE')
            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
            )
));

$impar = new PHPExcel_Style();
$impar->applyFromArray(
        array(
//            'fill' => array(
//                'type' => PHPExcel_Style_Fill::FILL_SOLID,
//                'color' => array('argb' => 'EEEEEEEE')
//            ),
            'borders' => array(
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array(
                        'rgb' => '000000'
                    )
                ),
            )
));


// CORPO -----------------------------------------------------------------------
// PROVISAO GASTOS -------------------------------------------------------------
if (isset($_REQUEST['modelo_xls']) && $_REQUEST["modelo_xls"] == 'mostrar_rescisao') {
    $status = 0;

    $row = 1;
    while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

        $mov = array();
        $total_movimentos = array();
        $movimentos_incide = 0;
        $query_movimento_recisao = "SELECT A.id_mov, A.id_rescisao, A.id_clt, A.id_movimento, A.valor, TRIM(A.tipo) as tipos, B.incidencia_inss 
                                FROM tabela_morta_movimentos_recisao_lote AS A 
                                LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod)
                                WHERE A.id_clt = {$row_rel['id_clt']} AND A.id_rescisao = '{$row_rel['id_recisao']}'";
        if ($debug == TRUE) {
            echo $query_movimento_recisao;
        }

        $sql_movimento_recisao = mysql_query($query_movimento_recisao) or die("Erro ao selecionar movimentos de rescisao");

        while ($rows_movimentos = mysql_fetch_assoc($sql_movimento_recisao)) {
            $mov[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']][$rows_movimentos['tipos']][$rows_movimentos['id_movimento']]["valor"] = $rows_movimentos['valor'];
            if ($rows_movimentos['tipos'] == "CREDITO" && $rows_movimentos['incidencia_inss'] == '1') {
                $movimentos_incide += $rows_movimentos['valor'];
            }
            if ($rows_movimentos['tipos'] == "DEBITO") {

                // if apenas para correcao de bug na hora de gerar o excel
                if (!isset($total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'])) {
                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] = 0;
                }

                $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] += $rows_movimentos['valor'];
            } else if ($rows_movimentos['tipos'] == "CREDITO") {

                // if apenas para correcao de bug na hora de gerar o excel
                if (!isset($total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'])) {
                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] = 0;
                }

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
            if (!empty($row_folha['ids_movimentos_estatisticas'])) {

                $movimentos = "SELECT *
                               FROM rh_movimentos_clt
                               WHERE id_movimento IN({$row_folha['ids_movimentos_estatisticas']}) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = '{$row_rel['id_clt']}' AND id_mov NOT IN(56,200) ";
                $qr_movimentos = mysql_query($movimentos);
//                echo "<!-- QUERY DE TOTAL DE RENDIMENTOS::: {$movimentos} -->";

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
//        echo $row_rel['id_recisao'] . "<br>";
        //serva apenas para tirar um bug
        if (!isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
            $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"] = 0;
        }


        $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
        $data_exp = explode('-', $row_rel['data_demi']);
        if ($base_saldo_salario > 0) {
            $calculos->MostraINSS($base_saldo_salario, implode('-', $data_exp)); // deve estar instanciada no provisão de gastos.
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
//        $class = ($cont++ % 2 == 0) ? "even" : "odd";

        if ($status != $row_rel["codigo"]) {
            $status = $row_rel["codigo"];

            if (!empty($total_sal_base)) {

                if ($row_rel['codigo'] != 20) {
                    $total_recisao_nao_paga += $total_liquido;
                }
                // -------------------------------------------------------------------------
                // RODAPE ------------------------------------------------------------------
                // TOTAL -------------------------------------------------------------------

                $objPHPExcel->getActiveSheet()->setSharedStyle($row_total, "A" . ($row) . ":BR" . ($row));

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("A" . $row, utf8_encode("TOTAL"));
                $sheet->mergeCells('A' . $row . ':G' . $row);

                //AÇÃO
                //ID
                //[11]NOME
                //[24]DATA DE ADMISSÃO
                //[25]DATA DO AVISO PRÉVIO
                //[26]DATA DE AFASTAMENTO
                //FUNÇÃO
                //MÉDIA DAS OUTRAS REMUNERAÇÕES
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("H" . $row, utf8_encode("R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", ".")));
                //SALÁRIO BASE
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("I" . $row, utf8_encode("R$ " . number_format($total_sal_base, 2, ",", ".")));

//                $objPHPExcel->setActiveSheetIndex(0)
//                        ->setCellValue("T" . $row, utf8_encode("R$ " . number_format($total_valor_aviso, 2, ",", ".")));
                //[50]SALDO DE SALÁRIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("J" . $row, utf8_encode("R$ " . number_format($total_saldo_salario, 2, ",", ".")));
                //[51]COMISSÕES
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("K" . $row, utf8_encode("R$ " . number_format($total_comissoes, 2, ",", ".")));
                //[52]GRATIFICAÇÃO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("L" . $row, utf8_encode("R$ " . number_format($total_gratificacao, 2, ",", ".")));
                //[53]ADICIONAL DE INSALUBRIDADE
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("M" . $row, utf8_encode("R$ " . number_format($total_insalubridade, 2, ",", ".")));
                //[54]ADICIONAL DE PERICULOSIDADE
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("N" . $row, utf8_encode("R$ " . number_format($total_periculosidade, 2, ",", ".")));
                //[55]ADICIONAL NOTURNO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("O" . $row, utf8_encode("R$ " . number_format($total_adicional_noturno, 2, ",", ".")));
                //[56]HORAS EXTRAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("P" . $row, utf8_encode("R$ " . number_format($total_hora_extra, 2, ",", ".")));
                //[57]GORJETAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("Q" . $row, utf8_encode("R$ " . number_format($total_gorjetas, 2, ",", ".")));
                //[58]DESCANSO SEMANAL REMUNERADO (DSR)
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("R" . $row, utf8_encode("R$ " . number_format($total_dsr, 2, ",", ".")));
                //[59]REFLEXO DO "DSR" SOBRE SALÁRIO VARIÁVEL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("S" . $row, utf8_encode("R$ " . number_format($total_reflexo_dsr, 2, ",", ".")));
                //[60]MULTA ART. 477, § 8º/CLT
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("T" . $row, utf8_encode("R$ " . number_format($total_multa_477, 2, ",", ".")));
                //[61]MULTA ART. 479/CLT
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("U" . $row, utf8_encode("R$ " . number_format($total_multa_479, 2, ",", ".")));
                //[62]SALÁRIO-FAMÍLIA
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("V" . $row, utf8_encode("R$ " . number_format($total_sal_familia, 2, ",", ".")));
                //[63]13º SALÁRIO PROPORCIONAL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("W" . $row, utf8_encode("R$ " . number_format($total_dt_salario, 2, ",", ".")));
                //[64]13º SALÁRIO EXERCÍCIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("X" . $row, utf8_encode("R$ " . number_format($total_terceiro_exercicio, 2, ",", ".")));
                //[65]FÉRIAS PROPORCIONAIS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("Y" . $row, utf8_encode("R$ " . number_format($total_ferias_pr, 2, ",", ".")));
                //[]1/3 DE FÉRIAS PROPORCIONAL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("Z" . $row, utf8_encode("R$ " . number_format($total_umterco_fp, 2, ",", ".")));
                //[66]FÉRIAS VENCIDAS PER. AQUISITIVO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AA" . $row, utf8_encode("R$ " . number_format($total_ferias_aquisitivas, 2, ",", ".")));
                //[]1/3 DE FÉRIAS VENCIDAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AB" . $row, utf8_encode("R$ " . number_format($total_umterco_fv, 2, ",", ".")));
                //[68]TERÇO CONSTITUCIONAL DE FÉRIAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AC" . $row, utf8_encode("R$ " . number_format($total_terco_constitucional, 2, ",", ".")));
                //[69]AVISO PRÉVIO INDENIZADO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AD" . $row, utf8_encode("R$ " . number_format($total_aviso_indenizado, 2, ",", ".")));
                //[70]13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AE" . $row, utf8_encode("R$ " . number_format($total_terceiro_ss, 2, ",", ".")));
                //[71]FÉRIAS (AVISO-PRÉVIO INDENIZADO)
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AF" . $row, utf8_encode("R$ " . number_format($total_f_aviso_indenizado, 2, ",", ".")));
                //[72]FÉRIAS EM DOBRO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AG" . $row, utf8_encode("R$ " . number_format($total_f_dobro, 2, ",", ".")));
                //[73]1/3 FÉRIAS EM DOBRO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AH" . $row, utf8_encode("R$ " . number_format($total_umterco_f_dobro, 2, ",", ".")));
                //[82] 1/3 DE FÉRIAS AVISO INDENIZADO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AI" . $row, utf8_encode("R$ " . number_format($total_umterco_ferias_aviso, 2, ",", ".")));
                //[80]DIFERENÇA SALARIAL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AJ" . $row, utf8_encode("R$ " . number_format($total_diferenca_salarial, 2, ",", ".")));
                //[82]AJUDA DE CUSTO ART. 470/CLT
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AK" . $row, utf8_encode("R$ " . number_format($total_ajuda_custo, 2, ",", ".")));
                //[95]LEI 12.506
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AL" . $row, utf8_encode("R$ " . number_format($total_lei_12_506, 2, ",", ".")));
                //[95]DIFERENÇA DISSÍDIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AM" . $row, utf8_encode("R$ " . number_format($total_dif_dissidio, 2, ",", ".")));
                //[106]VALE TRANSPORTE
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AN" . $row, utf8_encode("R$ " . number_format($total_vale_transporte, 2, ",", ".")));
                //[99]AJUSTE DO SALDO DEVEDOR
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AO" . $row, utf8_encode("R$ " . number_format($total_ajuste_de_saldo, 2, ",", ".")));
                //TOTAL RESCISÓRIO BRUTO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AP" . $row, utf8_encode("R$ " . number_format($total_rendimento, 2, ",", ".")));
                //[100]PENSÃO ALIMENTÍCIA
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AQ" . $row, utf8_encode("R$ " . number_format($total_pensao_alimenticia, 2, ",", ".")));
                //[101]ADIANTAMENTO SALARIAL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AR" . $row, utf8_encode("R$ " . number_format($total_adiantamento_salarial, 2, ",", ".")));
                //[102]ADIANTAMENTO DE 13º SALÁRIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AS" . $row, utf8_encode("R$ " . number_format($total_adiantamento_13_salarial, 2, ",", ".")));
                //[103]AVISO-PRÉVIO INDENIZADO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AT" . $row, utf8_encode("R$ " . number_format($total_aviso_indenizado_debito, 2, ",", ".")));
                //[104]MULTA ART. 480/CLT
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AU" . $row, utf8_encode("R$ " . number_format($total_multa_480, 2, ",", ".")));
                //[105]EMPRÉSTIMO EM CONSIGNAÇÃO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AV" . $row, utf8_encode("R$ " . number_format($total_emprestimo_consignado, 2, ",", ".")));
                //[106]VALE TRANSPORTE
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AW" . $row, utf8_encode("R$ " . number_format($total_vale_transporte_debito, 2, ",", ".")));
                //[109]VALE ALIMENTAÇÃO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AX" . $row, utf8_encode("R$ " . number_format($total_vale_alimentacao_debito, 2, ",", ".")));
                //[112.1]PREVIDÊNCIA SOCIAL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AY" . $row, utf8_encode("R$ " . number_format($total_inss_ss, 2, ",", ".")));
                //[112.2]PREVIDÊNCIA SOCIAL - 13º SALÁRIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("AZ" . $row, utf8_encode("R$ " . number_format($total_inss_dt, 2, ",", ".")));
                //[114.1]IRRF
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BA" . $row, utf8_encode("R$ " . number_format($total_ir_ss, 2, ",", ".")));
                //[114.2]IRRF SOBRE 13º SALÁRIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BB" . $row, utf8_encode("R$ " . number_format($total_ir_dt, 2, ",", ".")));
                //[115]DEVOLUÇÃO DE CRÉDITO INDEVIDO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BC" . $row, utf8_encode("R$ " . number_format($total_devolucao, 2, ",", ".")));
                //[115.1]OUTROS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BD" . $row, utf8_encode("R$ " . number_format($total_outros, 2, ",", ".")));
                //[115.2]ADIANTAMENTO DE 13º SALÁRIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BE" . $row, utf8_encode("R$ " . number_format($total_adiantamento_13, 2, ",", ".")));
                //[117]FALTAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BF" . $row, utf8_encode("R$ " . number_format($total_faltas, 2, ",", ".")));
                //[116]IRRF FÉRIAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BG" . $row, utf8_encode("R$ " . number_format($total_ir_ferias, 2, ",", ".")));
                //TOTAL DAS DEDUÇÕES
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BH" . $row, utf8_encode("R$ " . number_format($total_deducao, 2, ",", ".")));
                //VALOR RESCISÓRIO LÍQUIDO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BI" . $row, utf8_encode("R$ " . number_format($total_liquido, 2, ",", ".")));
                //BASE INSS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BJ" . $row, utf8_encode("R$ " . number_format($total_base_inss, 2, ",", ".")));
                //BASE FGTS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BK" . $row, utf8_encode("R$ " . number_format($total_base_fgts, 2, ",", ".")));
                //BASE PIS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BL" . $row, utf8_encode("R$ " . number_format($total_base_pis, 2, ",", ".")));


                // PIS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BN" . $row, utf8_encode("R$ " . number_format($total_pis, 2, ",", ".")));
                // MULTA DE 50% DO FGTS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BO" . $row, utf8_encode("R$ " . number_format($total_multa_fgts, 2, ",", ".")));
                //INSS A RECOLHER EMPRESA
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BP" . $row, utf8_encode("R$ " . number_format($total_inss_empresa, 2, ",", ".")));

                //INSS A RECOLHER TERCEITO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BQ" . $row, utf8_encode("R$ " . number_format($total_inss_terceiro, 2, ",", ".")));

                //FGTS A RECOLHER
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("BR" . $row, utf8_encode("R$ " . number_format($total_fgts_recolher, 2, ",", ".")));

                // FIM RODAPE --------------------------------------------------------------
                // -------------------------------------------------------------------------

                $row++;
            }

// -----------------------------------------------------------------------------
// CABECALHO -------------------------------------------------------------------

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, utf8_encode("{$row_rel["especifica"]} - {$row_rel['aviso']}"));
            $sheet->mergeCells('A' . $row . ':BL' . $row);

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('U' . $row, utf8_encode('EMPRESA'));
            $sheet->mergeCells('BN' . $row . ':BR' . $row);

            $row++; // para ir para segunda linha do cabecalho
            $row_prev = $row - 1; // para ir para segunda linha do cabecalho
            $row_next = $row + 1; // para ir para segunda linha do cabecalho

            $objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . $row_prev . ":BR" . $row_next);


            $row_1 = array('AÇÃO', 'ID', '[11] NOME', '[24] DATA DE ADMISSÃO', '[25] DATA DO AVISO PRÉVIO', '[26] DATA DE AFASTAMENTO', 'FUNÇÃO', 'MÉDIA DAS OUTRAS REMUNERAÇÕES', 'SALÁRIO BASE', '[50] SALDO DE SALÁRIO', '[51] COMISSÕES', '[52] GRATIFICAÇÃO', '[53] ADICIONAL DE INSALUBRIDADE', '[54] ADICIONAL DE PERICULOSIDADE',
                '[55] ADICIONAL NOTURNO', '[56] HORAS EXTRAS', '[57] GORJETAS', '[58] DESCANSO SEMANAL REMUNERADO (DSR)', '[59] REFLEXO DO "DSR" SOBRE SALÁRIO VARIÁVEL', '[60] MULTA ART. 477, § 8º/CLT', '[61] MULTA ART. 479/CLT', '[62] SALÁRIO-FAMÍLIA', '[63] 13º SALÁRIO PROPORCIONAL', '[64] 13º SALÁRIO EXERCÍCIO',
                '[65] FÉRIAS PROPORCIONAIS', '[] 1/3 DE FÉRIAS PROPORCIONAL', '[66] FÉRIAS VENCIDAS PER. AQUISITIVO', '[] 1/3 DE FÉRIAS VENCIDAS', '[68] TERÇO CONSTITUCIONAL DE FÉRIAS', '[69] AVISO PRÉVIO INDENIZADO', '[70] 13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)', '[71] FÉRIAS (AVISO-PRÉVIO INDENIZADO)', '[72] FÉRIAS EM DOBRO',
                '[73] 1/3 FÉRIAS EM DOBRO', '[82] 1/3 DE FÉRIAS AVISO INDENIZADO', '[80] DIFERENÇA SALARIAL', '[82] AJUDA DE CUSTO ART. 470/CLT', '[95] LEI 12.506', '[95]DIFERENÇA DISSÍDIO', '[106] VALE TRANSPORTE', '[99] AJUSTE DO SALDO DEVEDOR', 'TOTAL RESCISÓRIO BRUTO', '[100] PENSÃO ALIMENTÍCIA', '[101] ADIANTAMENTO SALARIAL',
                '[102] ADIANTAMENTO DE 13º SALÁRIO', '[103] AVISO-PRÉVIO INDENIZADO', '[104] MULTA ART. 480/CLT', '[105] EMPRÉSTIMO EM CONSIGNAÇÃO', '[106] VALE TRANSPORTE', '[109] VALE ALIMENTAÇÃO', '[112.1] PREVIDÊNCIA SOCIAL', '[112.2] PREVIDÊNCIA SOCIAL - 13º SALÁRIO', '[114.1] IRRF', '[114.2] IRRF SOBRE 13º SALÁRIO',
                '[115] DEVOLUÇÃO DE CRÉDITO INDEVIDO', '[115.1] OUTROS', '[115.2] ADIANTAMENTO DE 13º SALÁRIO', '[117] FALTAS', '[116] IRRF FÉRIAS', 'TOTAL DAS DEDUÇÕES', 'VALOR RESCISÓRIO LÍQUIDO', 'BASE INSS', 'BASE FGTS', 'BASE PIS', '', 'PIS', 'MULTA DE 50% DO FGTS', 'INSS A RECOLHER', '', 'FGTS A RECOLHER'
            );
            foreach ($row_1 as $id => $value) {
                $letra = getLetraCol($id);

                if ($value != 'INSS A RECOLHER' && $value != '') {
                    $sheet->mergeCells("{$letra}{$row}:{$letra}{$row_next}");
                } else if ($value != '') {
                    $id_next = $id + 1;
                    $letra2 = getLetraCol($id_next);
                    $sheet->mergeCells("{$letra}{$row}:{$letra2}{$row}");
                }

                $celula = "{$letra}{$row}";
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($celula, utf8_encode($value));
            }

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("BP{$row_next}", utf8_encode('EMPRESA'));
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("BQ{$row_next}", utf8_encode('TERCEIRO'));

            $row += 2; // para pular para proxima linha        
// FIM CABECALHO ---------------------------------------------------------------
// -----------------------------------------------------------------------------
// LIMPA TOTAIS ----------------------------------------------------------------
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


            //DEDUÇÕES
            $total_pensao_alimenticia = 0;
            $total_adiantamento_salarial = 0;
            $total_adiantamento_13_salarial = 0;
            $total_aviso_indenizado_debito = 0;
            $total_multa_480 = 0;
            $total_emprestimo_consignado = 0;
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
            $total_inss_terceiro = 0;
            $total_fgts_recolher = 0;

            //TOTALIZADOR FÉRIAS
            $total_ferias_a_pagar = 0;

            //TOTALIZADOR 13° 
            $total_decimo_a_pagar = 0;
// FIM LIMPA TOTAIS ------------------------------------------------------------
        }

        // cor da linha --------------------------------------------------------
        if ($row % 2 == 0) {
            $objPHPExcel->getActiveSheet()->setSharedStyle($par, "A" . ($row) . ":BR" . ($row));
        } else {
            $objPHPExcel->getActiveSheet()->setSharedStyle($impar, "A" . ($row) . ":BR" . ($row));
        }

        //AÇÃO
        //ID
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("B" . $row, utf8_encode($row_rel[id_clt]));
        //[11]NOME
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("C" . $row, utf8_encode($row_rel[nome]));
        //[24]DATA DE ADMISSÃO
        $data_adm = (!empty($row_rel['data_adm'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_adm']))) : "0000-00-00";
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("D" . $row, utf8_encode($data_adm));
        //[25]DATA DO AVISO PRÉVIO
        $xls_25 = (!empty($row_rel['data_aviso'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_aviso']))) : "00/00/0000";
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("E" . $row, utf8_encode($xls_25));
        //[26]DATA DE AFASTAMENTO
        $xls_26 = (!empty($row_rel['data_demi'])) ? date("d/m/Y", str_replace("-", "/", strtotime($row_rel['data_demi']))) : "0000-00-00";
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("F" . $row, utf8_encode($xls_26));
        //FUNÇÃO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("G" . $row, utf8_encode($row_rel['nome_funcao']));
        //MÉDIA DAS OUTRAS REMUNERAÇÕES
        $xls_media_outras_remuneracoes = "R$ " . number_format($total_rendi, 2, ",", ".");
        $total_das_medias_outras_remuneracoes += $total_rendi;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("H" . $row, utf8_encode($xls_media_outras_remuneracoes));
        //SALÁRIO BASE
        $xls_sal_base = "R$ " . number_format($row_rel['sal_base'], 2, ",", ".");
        $total_sal_base += $row_rel['sal_base'];
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
                $total_a_ser_pago[$status_clt] += $row_rel['total_rendimento'] + ($total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']);
            }
        }

        if ($row_rel['fator'] == "empregador") {
            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
        } else if ($row_rel['fator'] == "empregado") {
            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("I" . $row, utf8_encode($xls_sal_base));

        //[50]SALDO DE SALÁRIO
        $xls_50 = "[" . $row_rel['dias_saldo'] . "/30] R$ " . number_format($row_rel['saldo_salario'], 2, ",", ".");
        $total_saldo_salario += $row_rel['saldo_salario'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("J" . $row, utf8_encode($xls_50));
        //[51]COMISSÕES
        $xls_51 = "R$ " . number_format($row_rel['comissao'], 2, ",", ".");
        $total_comissoes += $row_rel['comissao'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("K" . $row, utf8_encode($xls_51));
        //[52]GRATIFICAÇÃO
        $xls_52 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"], 2, ",", ".");
        $total_gratificacao += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5912]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("L" . $row, utf8_encode($xls_52));
        //[53]ADICIONAL DE INSALUBRIDADE
        $xls_53 = "R$ " . number_format($row_rel['insalubridade'], 2, ",", ".");
        $total_insalubridade += $row_rel['insalubridade'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("M" . $row, utf8_encode($xls_53));
        //[54]ADICIONAL DE PERICULOSIDADE
        $xls_54 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"], 2, ",", ".");
        $total_periculosidade += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][6007]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("N" . $row, utf8_encode($xls_54));
        //[55]ADICIONAL NOTURNO
        $xls_55 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"], 2, ",", ".");
        $total_adicional_noturno += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9000]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("O" . $row, utf8_encode($xls_55));
        //[56]HORAS EXTRAS
        $xls_56 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"], 2, ",", ".");
        $total_hora_extra += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][8080]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("P" . $row, utf8_encode($xls_56));
        //[57]GORJETAS
        $xls_57 = "R$ " . number_format(0, 2, ",", ".");
        $total_gorjetas += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("Q" . $row, utf8_encode($xls_57));
        //[58]DESCANSO SEMANAL REMUNERADO (DSR)
        $xls_58 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"], 2, ",", ".");
        $total_dsr += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][9997]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("R" . $row, utf8_encode($xls_58));
        //[59]REFLEXO DO "DSR" SOBRE SALÁRIO VARIÁVEL
        $xls_59 = "R$ " . number_format(0, 2, ",", ".");
        $total_reflexo_dsr += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("S" . $row, utf8_encode($xls_59));
        //[60]MULTA ART. 477§ 8º/CLT
        $xls_60 = "R$ " . number_format($row_rel['a477'], 2, ",", ".");
        $total_multa_477 += $row_rel['a477'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("T" . $row, utf8_encode($xls_60));
        //[61]MULTA ART. 479/CLT
        if ($row_rel['motivo'] == 64) {
            $multa_479 = $row_rel['a479'];
        } else if ($row_rel['motivo'] == 63) {
            $multa_479 = null;
        }

        $xls_61 = "R$ " . number_format($multa_479, 2, ",", ".");
        $total_multa_479 += $multa_479;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("U" . $row, utf8_encode($xls_61));
        //[62]SALÁRIO-FAMÍLIA
        $xls_62 = "R$ " . number_format($row_rel['sal_familia'], 2, ",", ".");
        $total_sal_familia += $row_rel['sal_familia'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("V" . $row, utf8_encode($xls_62));
        //[63]13º SALÁRIO PROPORCIONAL
        $xls_63 = "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] R$ " . number_format($row_rel['dt_salario'], 2, ",", ".");
        $total_dt_salario += $row_rel['dt_salario'];
        $total_decimo_a_pagar += $row_rel['dt_salario'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("W" . $row, utf8_encode($xls_63));
        //[64]13º SALÁRIO EXERCÍCIO
        $xls_64 = "R$ " . number_format(0, 2, ",", ".");
        $total_terceiro_exercicio += 0;
        $total_decimo_a_pagar += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("X" . $row, utf8_encode($xls_64));
        //[65]FÉRIAS PROPORCIONAIS
        $xls_65 = "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] R$ " . number_format($row_rel['ferias_pr'], 2, ",", ".");
        $total_ferias_pr += $row_rel['ferias_pr'];
        $total_ferias_a_pagar += $row_rel['ferias_pr'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("Y" . $row, utf8_encode($xls_65));
        //[]1/3 DE FÉRIAS PROPORCIONAL,
        $xls_umterco_fp = "R$ " . number_format($row_rel['umterco_fp'], 2, ",", ".");
        $total_umterco_fp += $row_rel['umterco_fp'];
        $total_ferias_a_pagar += $row_rel['umterco_fp'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("Z" . $row, utf8_encode($xls_umterco_fp));
        //[66]FÉRIAS VENCIDAS PER. AQUISITIVO
        $xls_66 = "R$ " . number_format($row_rel['ferias_vencidas'], 2, ",", ".");
        $total_ferias_aquisitivas += $row_rel['ferias_vencidas'];
        $total_ferias_a_pagar += $row_rel['ferias_vencidas'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AA" . $row, utf8_encode($xls_65));
        //[]1/3 DE FÉRIAS VENCIDAS
        $xls_umterco_fv = "R$ " . number_format($row_rel['umterco_fv'], 2, ",", ".");
        $total_umterco_fv += $row_rel['umterco_fv'];
        $total_ferias_a_pagar += $row_rel['umterco_fv'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AB" . $row, utf8_encode($xls_umterco_fv));
        //[68]TERÇO CONSTITUCIONAL DE FÉRIAS
        $xls_68 = "R$ " . number_format($row_rel['umterco_fv'] + $row_rel['umterco_fp'], 2, ",", ".");
        $total_terco_constitucional += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
        $total_ferias_a_pagar += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AC" . $row, utf8_encode($xls_68));
        //[69]AVISO PRÉVIO INDENIZADO
        $xls_69 = "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
        $total_aviso_indenizado += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AD" . $row, utf8_encode($xls_69));
        //[70]13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)
        $xls_70 = "R$ " . number_format($row_rel['terceiro_ss'], 2, ",", ".");
        $total_terceiro_ss += $row_rel['terceiro_ss'];
        $total_decimo_a_pagar += $row_rel['terceiro_ss'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AE" . $row, utf8_encode($xls_70));
        //[71]FÉRIAS (AVISO-PRÉVIO INDENIZADO)
        $xls_71 = "R$ " . number_format($row_rel['ferias_aviso_indenizado'], 2, ",", ".");
        $total_f_aviso_indenizado += $row_rel['ferias_aviso_indenizado'];
        $total_ferias_a_pagar += $row_rel['ferias_aviso_indenizado'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AF" . $row, utf8_encode($xls_71));
        //[72]FÉRIAS EM DOBRO
        $xls_72 = "R$ " . number_format($row_rel['fv_dobro'], 2, ",", ".");
        $total_f_dobro += $row_rel['fv_dobro'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AG" . $row, utf8_encode($xls_72));
        //[73]1/3 FÉRIAS EM DOBRO
        $xls_73 = "R$ " . number_format($row_rel['um_terco_ferias_dobro'], 2, ",", ".");
        $total_umterco_f_dobro += $row_rel['um_terco_ferias_dobro'];
        $total_ferias_a_pagar += $row_rel['um_terco_ferias_dobro'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AH" . $row, utf8_encode($xls_73));
        //[82] 1/3 DE FÉRIAS AVISO INDENIZADO
        $xls_82 = "R$ " . number_format($row_rel['umterco_ferias_aviso_indenizado'], 2, ",", ".");
        $total_umterco_ferias_aviso += $row_rel['umterco_ferias_aviso_indenizado'];
        $total_ferias_a_pagar += $row_rel['umterco_ferias_aviso_indenizado'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AI" . $row, utf8_encode($xls_82));
        //[80]DIFERENÇA SALARIAL
        $xls_80 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"], 2, ",", ".");
        $total_diferenca_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5012]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AJ" . $row, utf8_encode($xls_80));
        //[82]AJUDA DE CUSTO ART. 470/CLT
        $xls_82 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"], 2, ",", ".");
        $total_ajuda_custo += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][5011]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AK" . $row, utf8_encode($xls_82));
        //[95]LEI 12.506
        $xls_95 = "R$ " . number_format($row_rel['lei_12_506'], 2, ",", ".");
        $total_lei_12_506 += $row_rel['lei_12_506'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AL" . $row, utf8_encode($xls_95));
        //[95]DIFERENÇA DISSÍDIO
        $xls_95 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"], 2, ",", ".");
        $total_dif_dissidio += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][80017]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AM" . $row, utf8_encode($xls_95));
        //[106]VALE TRANSPORTE
        $xls_106 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"], 2, ",", ".");
        $total_vale_transporte += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'][7001]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AN" . $row, utf8_encode($xls_65));
        //[99]AJUSTE DO SALDO DEVEDOR
        $xls_99 = "R$ " . number_format($row_rel['arredondamento_positivo'], 2, ",", ".");
        $total_ajuste_de_saldo += $row_rel['arredondamento_positivo'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AO" . $row, utf8_encode($xls_65));
        //TOTAL RESCISÓRIO BRUTO
        $xls_total_bruto = "R$ " . number_format($row_rel['total_rendimento'], 2, ",", ".");
        $total_rendimento += $row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AP" . $row, utf8_encode($xls_total_bruto));
        //[100]PENSÃO ALIMENTÍCIA
        if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"])) {
            $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][6004]["valor"];
        } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50222]["valor"])) {
            $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][50222]["valor"];
        } elseif (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"])) {
            $pensao = $mov[$row_rel['id_recisao']][$row_rel['id_clt']][7009]["valor"];
        } else {
            $pensao = 0;
        }
        $xls_100 = "R$ " . number_format($pensao, 2, ",", ".");
        $total_pensao_alimenticia += $pensao;
        $total_deducao_debito +=$pensao;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AQ" . $row, utf8_encode($xls_100));
        //[101]ADIANTAMENTO SALARIAL
        $xls_101 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"], 2, ",", ".");
        $total_adiantamento_salarial += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][7003]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AR" . $row, utf8_encode($xls_101));
        //[102]ADIANTAMENTO DE 13º SALÁRIO
        $xls_102 = "R$ " . number_format(0, 2, ",", ".");
        $total_adiantamento_13_salarial += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AS" . $row, utf8_encode($xls_102));
        //[103]AVISO-PRÉVIO INDENIZADO
        $xls_103 = "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
        $total_aviso_indenizado_debito += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AT" . $row, utf8_encode($xls_103));
        //[104]MULTA ART. 480/CLT
        if ($row_rel['motivo'] == 64) {
            $multa_480 = null;
        } else if ($row_rel['motivo'] == 63) {
            $multa_480 = $row_rescisao['a480'];
        }
        $xls_104 = "R$ " . number_format($multa_480, 2, ",", ".");
        $total_multa_480 += $multa_480;
        $total_deducao_debito += $multa_480;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AU" . $row, utf8_encode($xls_104));
        //[105]EMPRÉSTIMO EM CONSIGNAÇÃO
        $xls_105 = "R$ " . number_format(0, 2, ",", ".");
        $total_emprestimo_consignado += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AV" . $row, utf8_encode($xls_105));
        //[106]VALE TRANSPORTE
        $xls_106 = "R$ " . number_format(0, 2, ",", ".");
        $total_vale_transporte_debito += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AW" . $row, utf8_encode($xls_106));
        //[109]VALE ALIMENTAÇÃO
        $xls_109 = "R$ " . number_format($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8006]["valor"], 2, ",", ".");
        $total_vale_alimentacao_debito += $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][8006]["valor"];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AX" . $row, utf8_encode($xls_109));
        //[112.1]PREVIDÊNCIA SOCIAL
        $xls_112_1 = "R$ " . number_format($inss_saldo_salario, 2, ",", ".");
        $total_inss_ss += $inss_saldo_salario;
        $total_deducao_debito += $inss_saldo_salario;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AY" . $row, utf8_encode($xls_112_1));
        //[112.2]PREVIDÊNCIA SOCIAL - 13º SALÁRIO
        $xls_112_2 = "R$ " . number_format($row_rel['inss_dt'], 2, ",", ".");
        $total_inss_dt += $row_rel['inss_dt'];
        $total_deducao_debito += $row_rel['inss_dt'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("AZ" . $row, utf8_encode($xls_112_2));
        //[114.1]IRRF
        $xls_114_1 = "R$ " . number_format($calculos->valor, 2, ",", ".");
        $total_ir_ss += $calculos->valor;
        $total_deducao_debito += $calculos->valor;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BA" . $row, utf8_encode($xls_114_1));
        //[114.2]IRRF SOBRE 13º SALÁRIO
        $xls_114_2 = "R$ " . number_format($row_rel['ir_dt'], 2, ",", ".");
        $total_ir_dt += $row_rel['ir_dt'];
        $total_deducao_debito += $row_rel['ir_dt'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BB" . $row, utf8_encode($xls_114_2));
        //[115]DEVOLUÇÃO DE CRÉDITO INDEVIDO
        $xls_115 = "R$ " . number_format($row_rel['devolucao'], 2, ",", ".");
        $total_devolucao += $row_rel['devolucao'];
        $total_deducao_debito += $row_rel['devolucao'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BC" . $row, utf8_encode($xls_115));
        //[115.1]OUTROS
        $xls_115_1 = "R$ " . number_format(0, 2, ",", ".");
        $total_outros += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BD" . $row, utf8_encode($xls_115_1));
        //[115.2]ADIANTAMENTO DE 13º SALÁRIO
        $xls_115_2 = "R$ " . number_format($row_rel['adiantamento_13'], 2, ",", ".");
        $total_adiantamento_13 += $row_rel['adiantamento_13'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BE" . $row, utf8_encode($xls_115_2));
        //[117]FALTAS
        if (isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
            $movimento_falta = $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
        } else {
            $movimento_falta = 0;
        }
        $xls_117 = "R$ " . number_format($row_rel['valor_faltas'] + $movimento_falta, 2, ",", ".");
        $total_faltas += $row_rel['valor_faltas'] + $movimento_falta;
        $total_deducao_debito -= $row_rel['valor_faltas'] + $movimento_falta;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BF" . $row, utf8_encode($xls_117));
        //[116]IRRF FÉRIAS
        $xls_116 = "R$ " . number_format($row_rel['ir_ferias'], 2, ",", ".");
        $total_ir_ferias += $row_rel['ir_ferias'];
        $total_deducao_debito += $row_rel['ir_ferias'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BG" . $row, utf8_encode($xls_116));
        //TOTAL DAS DEDUÇÕES
        $xls_total_deducoes = "R$ " . number_format($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'], 2, ",", ".");
        $total_deducao += $row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BH" . $row, utf8_encode($xls_total_deducoes));
        //VALOR RESCISÓRIO LÍQUIDO
        $xls_total_liquido = "R$ " . number_format(($row_rel['total_rendimento']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']), 2, ",", ".");
        $total_liquido += ($row_rel['total_rendimento'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) - ($row_rel['total_deducao'] + $total_movimentos[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO']);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BI" . $row, utf8_encode($xls_total_liquido));
        //BASE INSS
        $xls_base_inss = "R$ " . number_format($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'], 2, ",", ".");
        $total_base_inss += $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BJ" . $row, utf8_encode($xls_base_inss));
        //BASE FGTS
        $xls_base_fgts = "R$ " . number_format($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
        $total_base_fgts += $row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BK" . $row, utf8_encode($xls_base_fgts));
        //BASE PIS
        $xls_base_pis = "R$ " . number_format($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss'], 2, ",", ".");
        $total_base_pis += $row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BL" . $row, utf8_encode($xls_base_pis));


        // PIS
        $xls_pis = "R$ " . number_format(($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01, 2, ",", ".");
        $total_pis += ( $row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {

//                if(!isset($total_pis_a_pagar[$status_clt])){
//                    $total_pis_a_pagar[$status_clt]=0;
//                }

                $total_pis_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BN" . $row, utf8_encode($xls_pis));

        // MULTA DE 50% DO FGTS
        $xls_multa_fgts = "R$ " . number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", ".");
        $total_multa_fgts += $folha->getMultaFgts($row_rel['id_clt']);
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
                if ($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador") {
//                    
//                    if(!isset($total_multa_a_pagar[$status_clt])){
//                        $total_multa_a_pagar[$status_clt]=0;
//                    }
//                                        
                    $total_multa_a_pagar[$status_clt] += $folha->getMultaFgts($row_rel['id_clt']);
                }
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BO" . $row, utf8_encode($xls_multa_fgts));

        // INSS A RECOLHER EMPRESA
        $xls_inss_empresa = "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20, 2, ",", ".");
        $total_inss_empresa += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
//                
//                // if para resolver bug
//                if(!isset($total_inss_empresa_a_pagar[$status_clt])){
//                    $total_inss_empresa_a_pagar[$status_clt] =0;
//                }
//                
                $total_inss_empresa_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BP" . $row, utf8_encode($xls_inss_empresa));

        // INSS A RECOLHER TERCEIRO
        $xls_inss_terceiro = "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068, 2, ",", ".");
        $total_inss_terceiro += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
//                
//                if(!isset($total_inss_terceiro_a_pagar[$status_clt])){
//                    $total_inss_terceiro_a_pagar[$status_clt]=0;
//                }
//                
                $total_inss_terceiro_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BQ" . $row, utf8_encode($xls_inss_terceiro));

        // FGTS A RECOLHER
        $xls_fgts_recolher = "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08, 2, ",", ".");
        $total_fgts_recolher += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
//                
//                if(!isset($total_fgts_recolher_a_pagar)){
//                    $total_fgts_recolher_a_pagar[$status_clt]=0;
//                }
//                
                $total_fgts_recolher_a_pagar[$status_clt] += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("BR" . $row, utf8_encode($xls_fgts_recolher));
//echo $row;

        $row++;
    }

    $total_recisao_nao_paga += $total_liquido;

    // -------------------------------------------------------------------------
    // RODAPE ------------------------------------------------------------------
    // TOTAL -------------------------------------------------------------------

    $objPHPExcel->getActiveSheet()->setSharedStyle($row_total, "A" . ($row) . ":BR" . ($row));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A" . $row, utf8_encode("TOTAL"));
    $sheet->mergeCells('A' . $row . ':G' . $row);

    //AÇÃO
    //ID
    //[11]NOME
    //[24]DATA DE ADMISSÃO
    //[25]DATA DO AVISO PRÉVIO
    //[26]DATA DE AFASTAMENTO
    //FUNÇÃO
    //MÉDIA DAS OUTRAS REMUNERAÇÕES
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("H" . $row, utf8_encode("R$ " . number_format($total_das_medias_outras_remuneracoes, 2, ",", ".")));
    //SALÁRIO BASE
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("I" . $row, utf8_encode("R$ " . number_format($total_sal_base, 2, ",", ".")));

//                $objPHPExcel->setActiveSheetIndex(0)
//                        ->setCellValue("T" . $row, utf8_encode("R$ " . number_format($total_valor_aviso, 2, ",", ".")));
    //[50]SALDO DE SALÁRIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("J" . $row, utf8_encode("R$ " . number_format($total_saldo_salario, 2, ",", ".")));
    //[51]COMISSÕES
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("K" . $row, utf8_encode("R$ " . number_format($total_comissoes, 2, ",", ".")));
    //[52]GRATIFICAÇÃO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("L" . $row, utf8_encode("R$ " . number_format($total_gratificacao, 2, ",", ".")));
    //[53]ADICIONAL DE INSALUBRIDADE
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("M" . $row, utf8_encode("R$ " . number_format($total_insalubridade, 2, ",", ".")));
    //[54]ADICIONAL DE PERICULOSIDADE
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("N" . $row, utf8_encode("R$ " . number_format($total_periculosidade, 2, ",", ".")));
    //[55]ADICIONAL NOTURNO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("O" . $row, utf8_encode("R$ " . number_format($total_adicional_noturno, 2, ",", ".")));
    //[56]HORAS EXTRAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("P" . $row, utf8_encode("R$ " . number_format($total_hora_extra, 2, ",", ".")));
    //[57]GORJETAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("Q" . $row, utf8_encode("R$ " . number_format($total_gorjetas, 2, ",", ".")));
    //[58]DESCANSO SEMANAL REMUNERADO (DSR)
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("R" . $row, utf8_encode("R$ " . number_format($total_dsr, 2, ",", ".")));
    //[59]REFLEXO DO "DSR" SOBRE SALÁRIO VARIÁVEL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("S" . $row, utf8_encode("R$ " . number_format($total_reflexo_dsr, 2, ",", ".")));
    //[60]MULTA ART. 477, § 8º/CLT
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("T" . $row, utf8_encode("R$ " . number_format($total_multa_477, 2, ",", ".")));
    //[61]MULTA ART. 479/CLT
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("U" . $row, utf8_encode("R$ " . number_format($total_multa_479, 2, ",", ".")));
    //[62]SALÁRIO-FAMÍLIA
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("V" . $row, utf8_encode("R$ " . number_format($total_sal_familia, 2, ",", ".")));
    //[63]13º SALÁRIO PROPORCIONAL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("W" . $row, utf8_encode("R$ " . number_format($total_dt_salario, 2, ",", ".")));
    //[64]13º SALÁRIO EXERCÍCIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("X" . $row, utf8_encode("R$ " . number_format($total_terceiro_exercicio, 2, ",", ".")));
    //[65]FÉRIAS PROPORCIONAIS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("Y" . $row, utf8_encode("R$ " . number_format($total_ferias_pr, 2, ",", ".")));
    //[]1/3 DE FÉRIAS PROPORCIONAL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("Z" . $row, utf8_encode("R$ " . number_format($total_umterco_fp, 2, ",", ".")));
    //[66]FÉRIAS VENCIDAS PER. AQUISITIVO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AA" . $row, utf8_encode("R$ " . number_format($total_ferias_aquisitivas, 2, ",", ".")));
    //[]1/3 DE FÉRIAS VENCIDAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AB" . $row, utf8_encode("R$ " . number_format($total_umterco_fv, 2, ",", ".")));
    //[68]TERÇO CONSTITUCIONAL DE FÉRIAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AC" . $row, utf8_encode("R$ " . number_format($total_terco_constitucional, 2, ",", ".")));
    //[69]AVISO PRÉVIO INDENIZADO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AD" . $row, utf8_encode("R$ " . number_format($total_aviso_indenizado, 2, ",", ".")));
    //[70]13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AE" . $row, utf8_encode("R$ " . number_format($total_terceiro_ss, 2, ",", ".")));
    //[71]FÉRIAS (AVISO-PRÉVIO INDENIZADO)
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AF" . $row, utf8_encode("R$ " . number_format($total_f_aviso_indenizado, 2, ",", ".")));
    //[72]FÉRIAS EM DOBRO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AG" . $row, utf8_encode("R$ " . number_format($total_f_dobro, 2, ",", ".")));
    //[73]1/3 FÉRIAS EM DOBRO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AH" . $row, utf8_encode("R$ " . number_format($total_umterco_f_dobro, 2, ",", ".")));
    //[82] 1/3 DE FÉRIAS AVISO INDENIZADO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AI" . $row, utf8_encode("R$ " . number_format($total_umterco_ferias_aviso, 2, ",", ".")));
    //[80]DIFERENÇA SALARIAL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AJ" . $row, utf8_encode("R$ " . number_format($total_diferenca_salarial, 2, ",", ".")));
    //[82]AJUDA DE CUSTO ART. 470/CLT
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AK" . $row, utf8_encode("R$ " . number_format($total_ajuda_custo, 2, ",", ".")));
    //[95]LEI 12.506
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AL" . $row, utf8_encode("R$ " . number_format($total_lei_12_506, 2, ",", ".")));
    //[95]DIFERENÇA DISSÍDIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AM" . $row, utf8_encode("R$ " . number_format($total_dif_dissidio, 2, ",", ".")));
    //[106]VALE TRANSPORTE
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AN" . $row, utf8_encode("R$ " . number_format($total_vale_transporte, 2, ",", ".")));
    //[99]AJUSTE DO SALDO DEVEDOR
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AO" . $row, utf8_encode("R$ " . number_format($total_ajuste_de_saldo, 2, ",", ".")));
    //TOTAL RESCISÓRIO BRUTO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AP" . $row, utf8_encode("R$ " . number_format($total_rendimento, 2, ",", ".")));
    //[100]PENSÃO ALIMENTÍCIA
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AQ" . $row, utf8_encode("R$ " . number_format($total_pensao_alimenticia, 2, ",", ".")));
    //[101]ADIANTAMENTO SALARIAL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AR" . $row, utf8_encode("R$ " . number_format($total_adiantamento_salarial, 2, ",", ".")));
    //[102]ADIANTAMENTO DE 13º SALÁRIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AS" . $row, utf8_encode("R$ " . number_format($total_adiantamento_13_salarial, 2, ",", ".")));
    //[103]AVISO-PRÉVIO INDENIZADO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AT" . $row, utf8_encode("R$ " . number_format($total_aviso_indenizado_debito, 2, ",", ".")));
    //[104]MULTA ART. 480/CLT
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AU" . $row, utf8_encode("R$ " . number_format($total_multa_480, 2, ",", ".")));
    //[105]EMPRÉSTIMO EM CONSIGNAÇÃO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AV" . $row, utf8_encode("R$ " . number_format($total_emprestimo_consignado, 2, ",", ".")));
    //[106]VALE TRANSPORTE
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AW" . $row, utf8_encode("R$ " . number_format($total_vale_transporte_debito, 2, ",", ".")));
    //[109]VALE ALIMENTAÇÃO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AX" . $row, utf8_encode("R$ " . number_format($total_vale_alimentacao_debito, 2, ",", ".")));
    //[112.1]PREVIDÊNCIA SOCIAL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AY" . $row, utf8_encode("R$ " . number_format($total_inss_ss, 2, ",", ".")));
    //[112.2]PREVIDÊNCIA SOCIAL - 13º SALÁRIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("AZ" . $row, utf8_encode("R$ " . number_format($total_inss_dt, 2, ",", ".")));
    //[114.1]IRRF
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BA" . $row, utf8_encode("R$ " . number_format($total_ir_ss, 2, ",", ".")));
    //[114.2]IRRF SOBRE 13º SALÁRIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BB" . $row, utf8_encode("R$ " . number_format($total_ir_dt, 2, ",", ".")));
    //[115]DEVOLUÇÃO DE CRÉDITO INDEVIDO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BC" . $row, utf8_encode("R$ " . number_format($total_devolucao, 2, ",", ".")));
    //[115.1]OUTROS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BD" . $row, utf8_encode("R$ " . number_format($total_outros, 2, ",", ".")));
    //[115.2]ADIANTAMENTO DE 13º SALÁRIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BE" . $row, utf8_encode("R$ " . number_format($total_adiantamento_13, 2, ",", ".")));
    //[117]FALTAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BF" . $row, utf8_encode("R$ " . number_format($total_faltas, 2, ",", ".")));
    //[116]IRRF FÉRIAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BG" . $row, utf8_encode("R$ " . number_format($total_ir_ferias, 2, ",", ".")));
    //TOTAL DAS DEDUÇÕES
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BH" . $row, utf8_encode("R$ " . number_format($total_deducao, 2, ",", ".")));
    //VALOR RESCISÓRIO LÍQUIDO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BI" . $row, utf8_encode("R$ " . number_format($total_liquido, 2, ",", ".")));
    //BASE INSS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BJ" . $row, utf8_encode("R$ " . number_format($total_base_inss, 2, ",", ".")));
    //BASE FGTS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BK" . $row, utf8_encode("R$ " . number_format($total_base_fgts, 2, ",", ".")));
    //BASE PIS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BL" . $row, utf8_encode("R$ " . number_format($total_base_pis, 2, ",", ".")));


    // PIS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BN" . $row, utf8_encode("R$ " . number_format($total_pis, 2, ",", ".")));
    // MULTA DE 50% DO FGTS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BO" . $row, utf8_encode("R$ " . number_format($total_multa_fgts, 2, ",", ".")));
    //INSS A RECOLHER EMPRESA
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BP" . $row, utf8_encode("R$ " . number_format($total_inss_empresa, 2, ",", ".")));

    //INSS A RECOLHER TERCEITO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BQ" . $row, utf8_encode("R$ " . number_format($total_inss_terceiro, 2, ",", ".")));

    //FGTS A RECOLHER
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("BR" . $row, utf8_encode("R$ " . number_format($total_fgts_recolher, 2, ",", ".")));


    // FIM RODAPE --------------------------------------------------------------
    // -------------------------------------------------------------------------
    $objPHPExcel->getActiveSheet()->setSharedStyle($erase, "BM1:BM" . ($row));

    
    foreach ($status_array as $status_clt) {
        $row += 2; //pula duas linhas

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A" . $row, utf8_encode("TOTALIZADORES ({$nome_status_array[$status_clt]})"));
        $objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . ($row) . ":D" . ($row));
        $sheet->mergeCells('A' . $row . ':D' . $row);

        $arr_totalizadores = array(
            "PIS:" => "R$ " . number_format($total_pis_a_pagar[$status_clt], 2, ",", "."),
            "GRRF:" => "R$ " . number_format($total_multa_a_pagar[$status_clt], 2, ",", "."),
            "FGTS RECOLHER:" => "R$ " . number_format($total_multa_a_pagar[$status_clt], 2, ",", "."),
            "INSS RECOLHER EMPRESA:" => "R$ " . number_format($total_fgts_recolher_a_pagar[$status_clt], 2, ",", "."),
            "INSS RECOLHER TERCEIRO:" => "R$ " . number_format($total_inss_empresa_a_pagar[$status_clt], 2, ",", "."),
            "(+) SUBTOTAL:" => "R$ " . number_format($total_inss_terceiro_a_pagar[$status_clt], 2, ",", "."),
            "(+) TOTAL A SER PAGO(RESCISÕES):" => "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt], 2, ",", "."),
            "(=) TOTAL:" => "R$ " . number_format($total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt] , 2, ",", "."),
        );
        $total_geral_pis += $total_pis_a_pagar[$status_clt];
        $total_geral_multa += $total_multa_a_pagar[$status_clt];
        $total_geral_fgts_recolher += $total_fgts_recolher_a_pagar[$status_clt];
        $total_geral_inss_emp += $total_inss_empresa_a_pagar[$status_clt];
        $total_geral_inss_terceiro += $total_inss_terceiro_a_pagar[$status_clt];
        $sub_total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt];
        $total_geral_a_ser_pago += $total_a_ser_pago[$status_clt];
        $total_geral += $total_pis_a_pagar[$status_clt] + $total_multa_a_pagar[$status_clt] + $total_inss_empresa_a_pagar[$status_clt] + $total_inss_terceiro_a_pagar[$status_clt] + $total_fgts_recolher_a_pagar[$status_clt] + $total_a_ser_pago[$status_clt];
        
        $row++; // pula mais uma linha

        foreach ($arr_totalizadores as $key => $value) {
            if ($row % 2 == 0) { // cor das linhas
                $objPHPExcel->getActiveSheet()->setSharedStyle($par, "A" . ($row) . ":D" . ($row));
            } else {
                $objPHPExcel->getActiveSheet()->setSharedStyle($impar, "A" . ($row) . ":D" . ($row));
            }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A" . $row, utf8_encode($key));
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("D" . $row, utf8_encode($value));
            $row++;
        }
    }
    
            $row += 2; //pula duas linhas

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A" . $row, utf8_encode("TOTALIZADOR GERAL"));
        $objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . ($row) . ":D" . ($row));
        $sheet->mergeCells('A' . $row . ':D' . $row);

        $arr_totalizadores = array(
            "PIS:" => "R$ " . number_format($total_geral_pis, 2, ",", "."),
            "GRRF:" => "R$ " . number_format($total_geral_multa, 2, ",", "."),
            "FGTS RECOLHER:" => "R$ " . number_format($total_geral_fgts_recolher, 2, ",", "."),
            "INSS RECOLHER EMPRESA:" => "R$ " . number_format($total_geral_inss_emp, 2, ",", "."),
            "INSS RECOLHER TERCEIRO:" => "R$ " . number_format($total_geral_inss_terceiro, 2, ",", "."),
            "(+) SUBTOTAL:" => "R$ " . number_format($sub_total_geral, 2, ",", "."),
            "(+) TOTAL A SER PAGO(RESCISÕES):" => "R$ " . number_format($sub_total_geral + $total_geral_a_ser_pago, 2, ",", "."),
            "(=) TOTAL:" => "R$ " . number_format(($sub_total_geral + $total_geral_a_ser_pago) + (($sub_total_geral + $total_geral_a_ser_pago) * 0.01) , 2, ",", "."),
        );
               
        $row++; // pula mais uma linha

        foreach ($arr_totalizadores as $key => $value) {
            if ($row % 2 == 0) { // cor das linhas
                $objPHPExcel->getActiveSheet()->setSharedStyle($par, "A" . ($row) . ":D" . ($row));
            } else {
                $objPHPExcel->getActiveSheet()->setSharedStyle($impar, "A" . ($row) . ":D" . ($row));
            }
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("A" . $row, utf8_encode($key));
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("D" . $row, utf8_encode($value));
            $row++;
        }

    // -------------------------------------------------------------------------
    // TOTALIZADORES -----------------------------------------------------------
    // FIM TOTALIZADORES -------------------------------------------------------
    // -------------------------------------------------------------------------
}
// FIM PROVISAO GASTOS ---------------------------------------------------------
// PROVISAO TRABALHISTA --------------------------------------------------------
if (isset($_REQUEST['modelo_xls']) && $_REQUEST["modelo_xls"] == 'mostrar_prov_trab' && $num_rows > 0) {
    $status = 0;
    $qtd_status = 0;
    $row = 1;
    while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

        $mov = array();
        $total_movimentos = array();
        $movimentos_incide = 0;
        $query_movimento_recisao = "SELECT A.id_mov, A.id_rescisao, A.id_clt, A.id_movimento, A.valor, TRIM(A.tipo) as tipos, B.incidencia_inss 
                                FROM tabela_morta_movimentos_recisao_lote AS A 
                                LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.cod)
                                WHERE A.id_clt = {$row_rel['id_clt']} AND A.id_rescisao = '{$row_rel['id_recisao']}'";

        if ($debug == TRUE) {
            echo $query_movimento_recisao;
        }

        $sql_movimento_recisao = mysql_query($query_movimento_recisao) or die("Erro ao selecionar movimentos de rescisao");

        while ($rows_movimentos = mysql_fetch_assoc($sql_movimento_recisao)) {
            $mov[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']][$rows_movimentos['tipos']][$rows_movimentos['id_movimento']]["valor"] = $rows_movimentos['valor'];
            if ($rows_movimentos['tipos'] == "CREDITO" && $rows_movimentos['incidencia_inss'] == '1') {
                $movimentos_incide += $rows_movimentos['valor'];
            }
            if ($rows_movimentos['tipos'] == "DEBITO") {

                // if apenas para correcao de bug na hora de gerar o excel
                if (!isset($total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'])) {
                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] = 0;
                }

                $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['DEBITO'] += $rows_movimentos['valor'];
            } else if ($rows_movimentos['tipos'] == "CREDITO") {

                // if apenas para correcao de bug na hora de gerar o excel
                if (!isset($total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'])) {
                    $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] = 0;
                }

                $total_movimentos[$rows_movimentos['id_rescisao']][$rows_movimentos['id_clt']]['CREDITO'] += $rows_movimentos['valor'];
            }
        }

        //////////////////////
        // MOVIMENTOS FIXOS //
        //////////////////////

        $qr_folha = mysql_query("select  A.ids_movimentos_estatisticas, B.id_clt,A.mes
                        FROM rh_folha as A
                        INNER JOIN rh_folha_proc as B
                        ON A. id_folha = B.id_folha
                        WHERE B.id_clt   = '{$row_rel['id_clt']}'  AND B.status = 3 AND A.terceiro = 2 
                        AND A.data_inicio >= DATE_SUB(NOW(), INTERVAL 13 MONTH) ORDER BY A.ano,A.mes");

        $movimentos = 0;
        $total_rendi = 0;

        while ($row_folha = mysql_fetch_assoc($qr_folha)) {
            if (!empty($row_folha['ids_movimentos_estatisticas'])) {

                $movimentos = "SELECT *
                               FROM rh_movimentos_clt
                               WHERE id_movimento IN({$row_folha['ids_movimentos_estatisticas']}) AND incidencia = '5020,5021,5023'  AND tipo_movimento = 'CREDITO' AND id_clt = '{$row_rel['id_clt']}' AND id_mov NOT IN(56,200) ";
                $qr_movimentos = mysql_query($movimentos);
//                echo "<!-- QUERY DE TOTAL DE RENDIMENTOS::: {$movimentos} -->";

                while ($row_mov = mysql_fetch_assoc($qr_movimentos)) {
                    $movimentos += $row_mov['valor_movimento'];
                }
            }
        }

        if ($movimentos > 0) {
            $total_rendi = $movimentos / 12;
        } else {
            $total_rendi = 0;
        }


        ////////////////////////////////////////
        ////////// CÁLCULO DE INSS /////////////
        ////////////////////////////////////////
//        echo $row_rel['id_recisao'] . "<br>";
        //serva apenas para tirar um bug
        if (!isset($mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"])) {
            $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"] = 0;
        }


        $base_saldo_salario = $row_rel['saldo_salario'] + $row_rel['insalubridade'] + $movimentos_incide - $mov[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'][50249]["valor"];
        $data_exp = explode('-', $row_rel['data_demi']);
        if ($base_saldo_salario > 0) {
            $calculos->MostraINSS($base_saldo_salario, implode('-', $data_exp)); // deve estar instanciada no provisão de gastos.
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
//        $class = ($cont++ % 2 == 0) ? "even" : "odd";


        if ($status != $row_rel["codigo"]) {
            $status = $row_rel["codigo"];
            $qtd_status++;

            if ($total_valor_aviso_par != 0) {
//                if ($row_rel['codigo'] != 20) {
//                    $total_recisao_nao_paga += $total_liquido;
//                }
                // -------------------------------------------------------------
                // RODAPE PARCIAL-----------------------------------------------
                // TOTAL -------------------------------------------------------

                $objPHPExcel->getActiveSheet()->setSharedStyle($row_total, "A" . ($row) . ":X" . ($row));

                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("A" . $row, utf8_encode("TOTAL PARCIAL"));
                $sheet->mergeCells('A' . $row . ':C' . $row);

                // VALOR AVISO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("D" . $row, utf8_encode("R$ " . number_format($total_valor_aviso_par, 2, ",", ".")));

                // DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS ------------------------
                // [63] 13O SALARIO PROPORCIONAL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("E" . $row, utf8_encode("R$ " . number_format($total_dt_salario_par, 2, ",", ".")));

                // [64] 13O SALARIO EXECICIO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("F" . $row, utf8_encode("R$ " . number_format($total_terceiro_exercicio_par, 2, ",", ".")));

                // [70] 13o SALARIO (AVISO PRÉVIO IDENIZADO)
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("G" . $row, utf8_encode("R$ " . number_format($total_terceiro_ss_par, 2, ",", ".")));

                // [65] FÉRIAS PROPORCIONAIS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("H" . $row, utf8_encode("R$ " . number_format($total_ferias_pr_par, 2, ",", ".")));

                // [] 1/3 DE FÉRIAS PROPORCIONAL
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("I" . $row, utf8_encode("R$ " . number_format($total_umterco_fp_par, 2, ",", ".")));

                // [66] FÉRIAS VENCIDAS PER. AQUISITIVO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("J" . $row, utf8_encode("R$ " . number_format($total_ferias_aquisitivas_par, 2, ",", ".")));

                // [] 1/3 DE FÉRIAS VENCIDAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("K" . $row, utf8_encode("R$ " . number_format($total_umterco_fv_par, 2, ",", ".")));

                // [68] TERÇO CONSTITUCIONAL DE FÉRIAS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("L" . $row, utf8_encode("R$ " . number_format($total_terco_constitucional_par, 2, ",", ".")));

                // [71] FÉRIAS (AVISO-PRÉVIO INDENIZADO)
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("M" . $row, utf8_encode("R$ " . number_format($total_f_aviso_indenizado_par, 2, ",", ".")));

                // [72] FÉRIAS EM DOBRO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("N" . $row, utf8_encode("R$ " . number_format($total_f_dobro_par, 2, ",", ".")));

                // [73] 1/3 FÉRIAS EM DOBRO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("O" . $row, utf8_encode("R$ " . number_format($total_umterco_f_dobro_par, 2, ",", ".")));

                // [82] 1/3 DE FÉRIAS AVISO INDENIZADO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("P" . $row, utf8_encode("R$ " . number_format($total_umterco_ferias_aviso_par, 2, ",", ".")));

                // [95] LEI 12.506
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("Q" . $row, utf8_encode("R$ " . number_format($total_lei_12_506_par, 2, ",", ".")));

                // [69]AVISO PRÉVIO INDENIZADO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("R" . $row, utf8_encode("R$ " . number_format($total_aviso_indenizado_par, 2, ",", ".")));

                // PIS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("T" . $row, utf8_encode("R$ " . number_format($total_pis_par, 2, ",", ".")));

                // MULTA DE 50% DO FGTS
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("U" . $row, utf8_encode("R$ " . number_format($total_multa_fgts_par, 2, ",", ".")));

                //INSS A RECOLHER EMPRESA
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("V" . $row, utf8_encode("R$ " . number_format($total_inss_empresa_par, 2, ",", ".")));

                //INSS A RECOLHER TERCEITO
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("W" . $row, utf8_encode("R$ " . number_format($total_inss_terceiro_par, 2, ",", ".")));

                //FGTS A RECOLHER
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("X" . $row, utf8_encode("R$ " . number_format($total_fgts_recolher_par, 2, ",", ".")));

                $row++;

                // limpa rodape parcial ----------------------------------------
                $total_aviso_indenizado_par = $total_f_aviso_indenizado_par = $total_ferias_aquisitivas_par = $total_fgts_recolher_par = $total_inss_empresa_par = $total_inss_terceiro_par = $total_lei_12_506_par = $total_multa_fgts_par = $total_pis_par = $total_terceiro_exercicio_par = $total_terceiro_ss_par = $total_terco_constitucional_par = $total_umterco_f_dobro_par = $total_umterco_ferias_aviso_par = $total_umterco_fp_par = $total_umterco_fv_par = $total_valor_aviso_par = $total_ferias_pr_par = 0;
                // fim limpa rodape parcial ------------------------------------
                // FIM RODAPE PARCIAL ------------------------------------------
                // -------------------------------------------------------------
            }

// -----------------------------------------------------------------------------
// CABECALHO -------------------------------------------------------------------

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $row, utf8_encode("{$row_rel["especifica"]} - {$row_rel['aviso']}"));
            $sheet->mergeCells('A' . $row . ':R' . $row);

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('U' . $row, utf8_encode('EMPRESA'));
            $sheet->mergeCells('U' . $row . ':X' . $row);

            $row++; // para ir para segunda linha do cabecalho
            $row_prev = $row - 1; // para ir para segunda linha do cabecalho
            $row_next = $row + 1; // para ir para segunda linha do cabecalho

            $objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . $row_prev . ":X" . $row_next);


            $row_1 = array('AÇÃO', 'ID', '[11] NOME', 'VALOR AVISO', '[63] 13º SALÁRIO PROPORCIONAL', '[64] 13º SALÁRIO EXERCÍCIO', '[70] 13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)', '[65] FÉRIAS PROPORCIONAIS', '[] 1/3 DE FÉRIAS PROPORCIONAL', '[66] FÉRIAS VENCIDAS PER. AQUISITIVO', '[] 1/3 DE FÉRIAS VENCIDAS', '[68] TERÇO CONSTITUCIONAL DE FÉRIAS', '[71] FÉRIAS (AVISO-PRÉVIO INDENIZADO)', '[72] FÉRIAS EM DOBRO', '[73] 1/3 FÉRIAS EM DOBRO', '[82]  1/3 DE FÉRIAS AVISO INDENIZADO', '[95] LEI 12.506', '[69] AVISO PRÉVIO INDENIZADO', '', 'PIS', 'MULTA DE 50% DO FGTS', 'INSS A RECOLHER', '', 'FGTS A RECOLHER');
            foreach ($row_1 as $id => $value) {
                $letra = getLetraCol($id);

                if ($value != 'INSS A RECOLHER' && $value != '') {
                    $sheet->mergeCells("{$letra}{$row}:{$letra}{$row_next}");
                } else if ($value != '') {
                    $id_next = $id + 1;
                    $letra2 = getLetraCol($id_next);
                    $sheet->mergeCells("{$letra}{$row}:{$letra2}{$row}");
                }

                $celula = "{$letra}{$row}";
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue($celula, utf8_encode($value));
            }

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("V{$row_next}", utf8_encode('EMPRESA'));
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("W{$row_next}", utf8_encode('TERCEIRO'));


            $row += 2; // para pular para proxima linha        
// FIM CABECALHO ---------------------------------------------------------------
// -----------------------------------------------------------------------------
            //DETALHES IMPORTANTES
//            $total_umterco_ferias_aviso = 0;
//            $total_umterco_fp = 0;
//            $total_umterco_fv = 0;
//            $total_ferias_vencida = 0;
//            $total_f_dobro_fv = 0;
//
//            //BASES
//            $total_base_inss = 0;
//            $total_base_fgts = 0;
//            $total_base_pis = 0;
//            $total_pis = 0;
//            $total_multa_fgts = 0;
//            $total_inss_empresa = 0;
//            $total_inss_terceiro = 0;
//            $total_fgts_recolher = 0;
//                //TOTALIZADOR FÉRIAS
//                $total_ferias_a_pagar = 0;
//
//                //TOTALIZADOR 13° 
//                $total_decimo_a_pagar = 0;
        }

        // cor da linha --------------------------------------------------------
        if ($row % 2 == 0) {
            $objPHPExcel->getActiveSheet()->setSharedStyle($par, "A" . ($row) . ":X" . ($row));
        } else {
            $objPHPExcel->getActiveSheet()->setSharedStyle($impar, "A" . ($row) . ":X" . ($row));
        }

        // ACAO FICA VAZIO
        // 
        // ID D0 CLT
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("B" . $row, utf8_encode($row_rel['id_clt']));

        // NOME DO CLT
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("C" . $row, utf8_encode($row_rel['nome']));

        // VALOR AVISO
        if ($row_rel['motivo'] != 60) {
            $valor_aviso = $row_rel['sal_base'] + $total_rendi + $row_rel['insalubridade'];
            $xls_valor_aviso = "R$ " . number_format($valor_aviso, 2, ",", ".");
            $total_valor_aviso += $valor_aviso;
            $total_valor_aviso_par += $valor_aviso;
        } else {
            $valor_aviso = 0;
            $xls_valor_aviso = "R$ " . number_format($valor_aviso, 2, ",", ".");
            $total_valor_aviso += $valor_aviso;
            $total_valor_aviso_par += $valor_aviso;
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("D" . $row, utf8_encode($xls_valor_aviso));

        if ($row_rel['fator'] == "empregador") {
            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'] = $row_rel['aviso_valor'];
        } else if ($row_rel['fator'] == "empregado") {
            $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['DEBITO'] = $row_rel['aviso_valor'];
        }


        if ($row_rel['motivo'] == 64) {
            $multa_479 = $row_rel['a479'];
        } else if ($row_rel['motivo'] == 63) {
            $multa_479 = null;
        }

        // DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS --------------------------------
        // [63] 13O SALARIO PROPORCIONAL
        $xls_63 = "[" . sprintf('%02d', $row_rel['avos_dt']) . "/12] R$ " . number_format($row_rel['dt_salario'], 2, ",", ".");
        $total_dt_salario += $row_rel['dt_salario'];
        $total_dt_salario_par += $row_rel['dt_salario'];
        $total_decimo_a_pagar += $row_rel['dt_salario'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("E" . $row, utf8_encode($xls_63));

        // [64] 13O SALARIO EXECICIO
        $xls_64 = "R$ " . number_format(0, 2, ",", ".");
        $total_terceiro_exercicio += 0;
        $total_terceiro_exercicio_par += 0;
        $total_decimo_a_pagar += 0;
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("F" . $row, utf8_encode($xls_64));

        // [70] 13o SALARIO (AVISO PRÉVIO IDENIZADO)
        $xls_70 = "R$ " . number_format($row_rel['terceiro_ss'], 2, ",", ".");
        $total_terceiro_ss += $row_rel['terceiro_ss'];
        $total_terceiro_ss_par += $row_rel['terceiro_ss'];
        $total_decimo_a_pagar += $row_rel['terceiro_ss'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("G" . $row, utf8_encode($xls_70));

        // [65] FÉRIAS PROPORCIONAIS
        $xls_65 = "[" . sprintf('%02d', $row_rel['avos_fp']) . "/12] R$ " . number_format($row_rel['ferias_pr'], 2, ",", ".");
        $total_ferias_pr += $row_rel['ferias_pr'];
        $total_ferias_pr_par += $row_rel['ferias_pr'];
        $total_ferias_a_pagar += $row_rel['ferias_pr'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("H" . $row, utf8_encode($xls_65));

        // [] 1/3 FERIAS PROPORCIONAIS
        $xls_umterco_fp = "R$ " . number_format($row_rel['umterco_fp'], 2, ",", ".");
        $total_umterco_fp += $row_rel['umterco_fp'];
        $total_umterco_fp_par += $row_rel['umterco_fp'];
        $total_ferias_a_pagar += $row_rel['umterco_fp'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("I" . $row, utf8_encode($xls_umterco_fp));

        // [66] FERIAS VENCIDAS PER. AQUISITIVO
        $xls_66 = "R$ " . number_format($row_rel['ferias_vencidas'], 2, ",", ".");
        $total_ferias_aquisitivas += $row_rel['ferias_vencidas'];
        $total_ferias_aquisitivas_par += $row_rel['ferias_vencidas'];
        $total_ferias_a_pagar += $row_rel['ferias_vencidas'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("J" . $row, utf8_encode($xls_66));

        // 1/3 FERIAS VENCIDAS
        $xls_umterco_fv = "R$ " . number_format($row_rel['umterco_fv'], 2, ",", ".");
        $total_umterco_fv += $row_rel['umterco_fv'];
        $total_umterco_fv_par += $row_rel['umterco_fv'];
        $total_ferias_a_pagar += $row_rel['umterco_fv'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("K" . $row, utf8_encode($xls_umterco_fv));


        // [68] TERÇO CONSTITUCIONAL DE FÉRIAS
        $xls_68 = "R$ " . number_format($row_rel['umterco_fv'] + $row_rel['umterco_fp'], 2, ",", ".");
        $total_terco_constitucional += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
        $total_terco_constitucional_par += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
        $total_ferias_a_pagar += $row_rel['umterco_fv'] + $row_rel['umterco_fp'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("L" . $row, utf8_encode($xls_68));

        // [71] FÉRIAS (AVISO PRÉVIO IDENIZADO)
        $xls_71 = "R$ " . number_format($row_rel['ferias_aviso_indenizado'], 2, ",", ".");
        $total_f_aviso_indenizado += $row_rel['ferias_aviso_indenizado'];
        $total_f_aviso_indenizado_par += $row_rel['ferias_aviso_indenizado'];
        $total_ferias_a_pagar += $row_rel['ferias_aviso_indenizado'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("M" . $row, utf8_encode($xls_71));

        // [72] FÉRIAS EM DOBRO
        $xls_72 = "R$ " . number_format($row_rel['fv_dobro'], 2, ",", ".");
        $total_f_dobro += $row_rel['fv_dobro'];
        $total_ferias_a_pagar += $row_rel['fv_dobro'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("N" . $row, utf8_encode($xls_72));

        // [73] 1/3 FERIAS EM DOBRO
        $xls_73 = "R$ " . number_format($row_rel['um_terco_ferias_dobro'], 2, ",", ".");
        $total_umterco_f_dobro += $row_rel['um_terco_ferias_dobro'];
        $total_ferias_a_pagar += $row_rel['um_terco_ferias_dobro'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("O" . $row, utf8_encode($xls_73));

        // [82] 1/3 DE FERIAS AVISO IDENIZADO
        $xls_82 = "R$ " . number_format($row_rel['umterco_ferias_aviso_indenizado'], 2, ",", ".");
        $total_umterco_ferias_aviso += $row_rel['umterco_ferias_aviso_indenizado'];
        $total_umterco_ferias_aviso_par += $row_rel['umterco_ferias_aviso_indenizado'];
        $total_ferias_a_pagar += $row_rel['umterco_ferias_aviso_indenizado'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("P" . $row, utf8_encode($xls_82));

        // [95] LEI 12.506
        $xls_95 = "R$ " . number_format($row_rel['lei_12_506'], 2, ",", ".");
        $total_lei_12_506 += $row_rel['lei_12_506'];
        $total_lei_12_506_par += $row_rel['lei_12_506'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("Q" . $row, utf8_encode($xls_95));

        // [69] AVISO PREVIO IDENIZADO
        $xls_69 = "R$ " . number_format($aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'], 2, ",", ".");
        $total_aviso_indenizado += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
        $total_aviso_indenizado_par += $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("R" . $row, utf8_encode($xls_69));

        // DEDUÇÕES --------------------------------------------------------
        // NÃO É NECESSÁRIO
        // 
        // PIS
        $xls_pis = "R$ " . number_format(($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01, 2, ",", ".");
        $total_pis += ( $row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
        $total_pis_par += ( $row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {

//                if(!isset($total_pis_a_pagar[$status_clt])){
//                    $total_pis_a_pagar[$status_clt]=0;
//                }

                $total_pis_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.01;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("T" . $row, utf8_encode($xls_pis));

        // MULTA DE 50% DO FGTS
        $xls_multa_fgts = "R$ " . number_format($folha->getMultaFgts($row_rel['id_clt']), 2, ",", ".");
        $total_multa_fgts += $folha->getMultaFgts($row_rel['id_clt']);
        $total_multa_fgts_par += $folha->getMultaFgts($row_rel['id_clt']);
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
                if ($row_rel['motivo'] == 61 && $row_rel['fator'] == "empregador") {
//                    
//                    if(!isset($total_multa_a_pagar[$status_clt])){
//                        $total_multa_a_pagar[$status_clt]=0;
//                    }
//                                        
                    $total_multa_a_pagar[$status_clt] += $folha->getMultaFgts($row_rel['id_clt']);
                }
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("U" . $row, utf8_encode($xls_multa_fgts));

        // INSS A RECOLHER EMPRESA
        $xls_inss_empresa = "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20, 2, ",", ".");
        $total_inss_empresa += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
        $total_inss_empresa_par += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
//                
//                // if para resolver bug
//                if(!isset($total_inss_empresa_a_pagar[$status_clt])){
//                    $total_inss_empresa_a_pagar[$status_clt] =0;
//                }
//                
                $total_inss_empresa_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.20;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("V" . $row, utf8_encode($xls_inss_empresa));

        // INSS A RECOLHER TERCEIRO
        $xls_inss_terceiro = "R$ " . number_format(($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068, 2, ",", ".");
        $total_inss_terceiro += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
        $total_inss_terceiro_par += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
//                
//                if(!isset($total_inss_terceiro_a_pagar[$status_clt])){
//                    $total_inss_terceiro_a_pagar[$status_clt]=0;
//                }
//                
                $total_inss_terceiro_a_pagar[$status_clt] += ($row_rel['lei_12_506'] + $valor_aviso + $row_rel['dt_salario'] + $row_rel['terceiro_ss']) * 0.068;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("W" . $row, utf8_encode($xls_inss_terceiro));

        // FGTS A RECOLHER
        $xls_fgts_recolher = "R$ " . number_format(($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08, 2, ",", ".");
        $total_fgts_recolher += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
        $total_fgts_recolher_par += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
        foreach ($status_array as $status_clt) {
            if ($row_rel['codigo'] == $status_clt) {
//                
//                if(!isset($total_fgts_recolher_a_pagar)){
//                    $total_fgts_recolher_a_pagar[$status_clt]=0;
//                }
//                
                $total_fgts_recolher_a_pagar[$status_clt] += ($row_rel['saldo_salario'] + $row_rel['dt_salario'] + $movimentos_incide + $row_rel['terceiro_ss'] + $row_rel['lei_12_506'] + $aviso[$row_rel['id_recisao']][$row_rel['id_clt']]['CREDITO']) * 0.08;
            }
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("X" . $row, utf8_encode($xls_fgts_recolher));
//echo $row;

        $row++;
    }

//    $total_recisao_nao_paga += $total_liquido;

    if ($total_valor_aviso_par != 0 && $qtd_status > 1) {
//                if ($row_rel['codigo'] != 20) {
//                    $total_recisao_nao_paga += $total_liquido;
//                }
        // -------------------------------------------------------------
        // RODAPE PARCIAL-----------------------------------------------
        // TOTAL -------------------------------------------------------

        $objPHPExcel->getActiveSheet()->setSharedStyle($row_total, "A" . ($row) . ":X" . ($row));

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A" . $row, utf8_encode("TOTAL PARCIAL"));
        $sheet->mergeCells('A' . $row . ':C' . $row);

        // VALOR AVISO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("D" . $row, utf8_encode("R$ " . number_format($total_valor_aviso_par, 2, ",", ".")));

        // DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS ------------------------
        // [63] 13O SALARIO PROPORCIONAL
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("E" . $row, utf8_encode("R$ " . number_format($total_dt_salario_par, 2, ",", ".")));

        // [64] 13O SALARIO EXECICIO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("F" . $row, utf8_encode("R$ " . number_format($total_terceiro_exercicio_par, 2, ",", ".")));

        // [70] 13o SALARIO (AVISO PRÉVIO IDENIZADO)
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("G" . $row, utf8_encode("R$ " . number_format($total_terceiro_ss_par, 2, ",", ".")));

        // [65] FÉRIAS PROPORCIONAIS
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("H" . $row, utf8_encode("R$ " . number_format($total_ferias_pr_par, 2, ",", ".")));

        // [] 1/3 DE FÉRIAS PROPORCIONAL
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("I" . $row, utf8_encode("R$ " . number_format($total_umterco_fp_par, 2, ",", ".")));

        // [66] FÉRIAS VENCIDAS PER. AQUISITIVO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("J" . $row, utf8_encode("R$ " . number_format($total_ferias_aquisitivas_par, 2, ",", ".")));

        // [] 1/3 DE FÉRIAS VENCIDAS
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("K" . $row, utf8_encode("R$ " . number_format($total_umterco_fv_par, 2, ",", ".")));

        // [68] TERÇO CONSTITUCIONAL DE FÉRIAS
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("L" . $row, utf8_encode("R$ " . number_format($total_terco_constitucional_par, 2, ",", ".")));

        // [71] FÉRIAS (AVISO-PRÉVIO INDENIZADO)
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("M" . $row, utf8_encode("R$ " . number_format($total_f_aviso_indenizado_par, 2, ",", ".")));

        // [72] FÉRIAS EM DOBRO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("N" . $row, utf8_encode("R$ " . number_format($total_f_dobro_par, 2, ",", ".")));

        // [73] 1/3 FÉRIAS EM DOBRO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("O" . $row, utf8_encode("R$ " . number_format($total_umterco_f_dobro_par, 2, ",", ".")));

        // [82] 1/3 DE FÉRIAS AVISO INDENIZADO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("P" . $row, utf8_encode("R$ " . number_format($total_umterco_ferias_aviso_par, 2, ",", ".")));

        // [95] LEI 12.506
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("Q" . $row, utf8_encode("R$ " . number_format($total_lei_12_506_par, 2, ",", ".")));

        // [69]AVISO PRÉVIO INDENIZADO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("R" . $row, utf8_encode("R$ " . number_format($total_aviso_indenizado_par, 2, ",", ".")));

        // PIS
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("T" . $row, utf8_encode("R$ " . number_format($total_pis_par, 2, ",", ".")));

        // MULTA DE 50% DO FGTS
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("U" . $row, utf8_encode("R$ " . number_format($total_multa_fgts_par, 2, ",", ".")));

        //INSS A RECOLHER EMPRESA
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("V" . $row, utf8_encode("R$ " . number_format($total_inss_empresa_par, 2, ",", ".")));

        //INSS A RECOLHER TERCEITO
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("W" . $row, utf8_encode("R$ " . number_format($total_inss_terceiro_par, 2, ",", ".")));

        //FGTS A RECOLHER
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("X" . $row, utf8_encode("R$ " . number_format($total_fgts_recolher_par, 2, ",", ".")));

        $row++;

        // limpa rodape parcial ----------------------------------------
        $total_aviso_indenizado_par = $total_f_aviso_indenizado_par = $total_ferias_aquisitivas_par = $total_fgts_recolher_par = $total_inss_empresa_par = $total_inss_terceiro_par = $total_lei_12_506_par = $total_multa_fgts_par = $total_pis_par = $total_terceiro_exercicio_par = $total_terceiro_ss_par = $total_terco_constitucional_par = $total_umterco_f_dobro_par = $total_umterco_ferias_aviso_par = $total_umterco_fp_par = $total_umterco_fv_par = $total_valor_aviso_par = $total_ferias_pr_par = 0;
        // fim limpa rodape parcial ------------------------------------
        // FIM RODAPE PARCIAL ------------------------------------------
        // -------------------------------------------------------------
    }


    // -------------------------------------------------------------------------
    // RODAPE ------------------------------------------------------------------
    // TOTAL -------------------------------------------------------------------

    $objPHPExcel->getActiveSheet()->setSharedStyle($row_total, "A" . ($row) . ":X" . ($row));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A" . $row, utf8_encode("TOTAL"));
    $sheet->mergeCells('A' . $row . ':C' . $row);

    // VALOR AVISO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("D" . $row, utf8_encode("R$ " . number_format($total_valor_aviso, 2, ",", ".")));

    // DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS ------------------------------------
    // [63] 13O SALARIO PROPORCIONAL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("E" . $row, utf8_encode("R$ " . number_format($total_dt_salario, 2, ",", ".")));

    // [64] 13O SALARIO EXECICIO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("F" . $row, utf8_encode("R$ " . number_format($total_terceiro_exercicio, 2, ",", ".")));

    // [70] 13o SALARIO (AVISO PRÉVIO IDENIZADO)
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("G" . $row, utf8_encode("R$ " . number_format($total_terceiro_ss, 2, ",", ".")));

    // [65] FÉRIAS PROPORCIONAIS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("H" . $row, utf8_encode("R$ " . number_format($total_ferias_pr, 2, ",", ".")));

    // [] 1/3 DE FÉRIAS PROPORCIONAL
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("I" . $row, utf8_encode("R$ " . number_format($total_umterco_fp, 2, ",", ".")));

    // [66] FÉRIAS VENCIDAS PER. AQUISITIVO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("J" . $row, utf8_encode("R$ " . number_format($total_ferias_aquisitivas, 2, ",", ".")));

    // [] 1/3 DE FÉRIAS VENCIDAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("K" . $row, utf8_encode("R$ " . number_format($total_umterco_fv, 2, ",", ".")));

    // [68] TERÇO CONSTITUCIONAL DE FÉRIAS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("L" . $row, utf8_encode("R$ " . number_format($total_terco_constitucional, 2, ",", ".")));

    // [71] FÉRIAS (AVISO-PRÉVIO INDENIZADO)
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("M" . $row, utf8_encode("R$ " . number_format($total_f_aviso_indenizado, 2, ",", ".")));

    // [72] FÉRIAS EM DOBRO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("N" . $row, utf8_encode("R$ " . number_format($total_f_dobro, 2, ",", ".")));

    // [73] 1/3 FÉRIAS EM DOBRO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("O" . $row, utf8_encode("R$ " . number_format($total_umterco_f_dobro, 2, ",", ".")));

    // [82] 1/3 DE FÉRIAS AVISO INDENIZADO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("P" . $row, utf8_encode("R$ " . number_format($total_umterco_ferias_aviso, 2, ",", ".")));

    // [95] LEI 12.506
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("Q" . $row, utf8_encode("R$ " . number_format($total_lei_12_506, 2, ",", ".")));

    // [69]AVISO PRÉVIO INDENIZADO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("R" . $row, utf8_encode("R$ " . number_format($total_aviso_indenizado, 2, ",", ".")));

    // PIS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("T" . $row, utf8_encode("R$ " . number_format($total_pis, 2, ",", ".")));

    // MULTA DE 50% DO FGTS
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("U" . $row, utf8_encode("R$ " . number_format($total_multa_fgts, 2, ",", ".")));

    //INSS A RECOLHER EMPRESA
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("V" . $row, utf8_encode("R$ " . number_format($total_inss_empresa, 2, ",", ".")));

    //INSS A RECOLHER TERCEITO
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("W" . $row, utf8_encode("R$ " . number_format($total_inss_terceiro, 2, ",", ".")));

    //FGTS A RECOLHER
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("X" . $row, utf8_encode("R$ " . number_format($total_fgts_recolher, 2, ",", ".")));

    // FIM RODAPE --------------------------------------------------------------
    // -------------------------------------------------------------------------

    $objPHPExcel->getActiveSheet()->setSharedStyle($erase, "S1:S" . ($row));

    $row += 2; //pula duas linhas

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A" . $row, utf8_encode("TOTALIZADORES"));
    $objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . ($row) . ":D" . ($row));
    $sheet->mergeCells('A' . $row . ':D' . $row);

    $arr_totalizadores = array(
        "FÉRIAS:" => "R$ " . number_format($total_ferias_a_pagar, 2, ",", "."),
        "13° SALÁRIO:" => "R$ " . number_format($total_decimo_a_pagar, 2, ",", "."),
        "PROVISÃO RESCISÕES:" => "R$ " . number_format($total_aviso_indenizado + $total_multa_fgts + $total_lei_12_506, 2, ",", "."),
        "AVISO PRÉVIO:" => "R$ " . number_format($total_aviso_indenizado, 2, ",", "."),
        "MULTA FGTS:" => "R$ " . number_format($total_multa_fgts, 2, ",", "."),
        "LEI 12/506:" => "R$ " . number_format($total_lei_12_506, 2, ",", "."),
        "PROVISÃO INSS S/PROV. TRABALISTA:" => "R$ " . number_format(($total_decimo_a_pagar + $total_aviso_indenizado + $total_lei_12_506) * 0.268, 2, ",", "."),
        "PROVISÃO FGTS S/PROV. TRABALISTA:" => "R$ " . number_format(($total_decimo_a_pagar + $total_aviso_indenizado + $total_lei_12_506) * 0.08, 2, ",", "."),
        "PROVISÃO PIS S/PROV. TRABALISTA:" => "R$ " . number_format($total_decimo_a_pagar * 0.01, 2, ",", "."),
    );

    $row++; // pula mais uma linha

    foreach ($arr_totalizadores as $key => $value) {
        if ($row % 2 == 0) { // cor das linhas
            $objPHPExcel->getActiveSheet()->setSharedStyle($par, "A" . ($row) . ":D" . ($row));
        } else {
            $objPHPExcel->getActiveSheet()->setSharedStyle($impar, "A" . ($row) . ":D" . ($row));
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("A" . $row, utf8_encode($key));
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue("D" . $row, utf8_encode($value));
        $row++;
    }

    // -------------------------------------------------------------------------
    // TOTALIZADORES -----------------------------------------------------------
    // FIM TOTALIZADORES -------------------------------------------------------
    // -------------------------------------------------------------------------
}
// FIM PROVISAO TRABALHISTA ----------------------------------------------------
// FIM CORPO -------------------------------------------------------------------
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle(utf8_encode('Provisão Trabalista'));


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a clientâ??s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="provisao_de_gastos_' . date('YmdHis') . '.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
