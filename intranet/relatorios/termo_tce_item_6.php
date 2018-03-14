<?php

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include "../conn.php";


$query = "SELECT a.nome,a.matricula,
b.nome AS funcao,
c.nome AS lotacao,
replace(replace(a.cpf,'.',''),'-','') AS cpf,
DATE_FORMAT(a.data_nasci,'%d/%m/%Y') AS data_nasci_br,
DATE_FORMAT(a.data_entrada,'%d/%m/%Y') AS data_entrada_br,
DATE_FORMAT(a.data_saida,'%d/%m/%Y') AS data_saida_br,
'CELETISTA' AS tipo_contrato,
'CLT' AS fundamentacao,
'NÃO' AS remetido,
'NÃO' AS firmou
FROM rh_clt AS a
INNER JOIN curso AS b ON (a.id_curso = b.id_curso)
INNER JOIN projeto AS c ON (a.id_projeto = c.id_projeto)
WHERE a.id_projeto NOT IN(13,7,14)
ORDER BY nome";

$qr_relatorio = mysql_query($query);

function getLetraCol($int = 0) {
    $letrasCol = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ', 'EA', 'EB', 'EC', 'ED', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ', 'FA', 'FB', 'FC', 'FD', 'FE', 'FF', 'FG', 'FH', 'FI', 'FJ', 'FK', 'FL', 'FM', 'FN', 'FO', 'FP', 'FQ', 'FR', 'FS', 'FT', 'FU', 'FV', 'FW', 'FX', 'FY', 'FZ', 'GA', 'GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ', 'GK', 'GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT', 'GU', 'GV', 'GW', 'GX', 'GY', 'GZ', 'HA', 'HB', 'HC', 'HD', 'HE', 'HF', 'HG', 'HH', 'HI', 'HJ', 'HK', 'HL', 'HM', 'HN', 'HO', 'HP', 'HQ', 'HR', 'HS', 'HT', 'HU', 'HV', 'HW', 'HX', 'HY', 'HZ', 'IA', 'IB', 'IC', 'ID', 'IE', 'IF', 'IG', 'IH', 'II', 'IJ', 'IK', 'IL', 'IM', 'IN', 'IO', 'IP', 'IQ', 'IR', 'IS', 'IT', 'IU', 'IV', 'IW', 'IX', 'IY', 'IZ',);
    return $letrasCol[$int];
}

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
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
        ->setTitle("TERMO TCE - ITEM 6")
        ->setSubject("TERMO TCE - ITEM 6")
        ->setDescription("TERMO TCE - ITEM 6")
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
// PROVISAO TRABALHISTA --------------------------------------------------------
$status = 0;
$qtd_status = 0;
$row = 1;

// -----------------------------------------------------------------------------
// CABECALHO -------------------------------------------------------------------

//$objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . $row_prev . ":X" . $row_next);

$row_1 = array(
    'NOME', 'FUNÇÃO CONTRATADA',
    'FUNÇÃO DESEMPRENHADA',
    'MATRICULA', 'CPF', 'LOTAÇÃO',
    'DATA DE NASCIMENTO', 'DATA DE INÍCIO DO CONTRATO',
    'DATA DE TERMINO DO CONTRATO', 'TIPO DE CONTRATO',
    'FUNDAMENTAÇÃO LEGAL', 'REMETIDO AO TCE',
    'FIRMOU DECLARAÇÃO DE ACULULAÇÃO OU NÃO?'
);

foreach ($row_1 as $id => $value) {
    $letra = getLetraCol($id);

    $celula = "{$letra}{$row}";
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue($celula, utf8_encode($value));
}
$objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . ($row) . ":M" . ($row));
$row += 1; // para pular para proxima linha        
// FIM CABECALHO ---------------------------------------------------------------
// -----------------------------------------------------------------------------


while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {

    // cor da linha --------------------------------------------------------
    if ($row % 2 == 0) {
        $objPHPExcel->getActiveSheet()->setSharedStyle($par, "A" . ($row) . ":M" . ($row));
    } else {
        $objPHPExcel->getActiveSheet()->setSharedStyle($impar, "A" . ($row) . ":M" . ($row));
    }

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A" . $row, utf8_encode($row_rel['nome']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("B" . $row, utf8_encode($row_rel['funcao']));
    
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("C" . $row, utf8_encode($row_rel['funcao']));
    
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("D" . $row, utf8_encode($row_rel['matricula']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("E" . $row, utf8_encode($row_rel['cpf']));
    
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("F" . $row, utf8_encode($row_rel['lotacao']));
    
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("G" . $row, utf8_encode($row_rel['data_nasci_br']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("H" . $row, utf8_encode($row_rel['data_entrada_br']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("I" . $row, utf8_encode($row_rel['data_saida_br']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("J" . $row, utf8_encode($row_rel['tipo_contrato']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("K" . $row, utf8_encode($row_rel['fundamentacao']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("L" . $row, utf8_encode($row_rel['remetido']));

    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("M" . $row, utf8_encode($row_rel['firmou']));

    $row++;
}

// FIM PROVISAO TRABALHISTA ----------------------------------------------------
// FIM CORPO -------------------------------------------------------------------
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle(utf8_encode('TERMO 3 TCE - ITEM 6'));


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a clientâ??s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="termo_3_tce_item_6_' . date('YmdHis') . '.xls"');
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
