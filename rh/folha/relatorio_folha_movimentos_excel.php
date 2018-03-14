<?php

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

require("../../conn.php");
require("../../wfunction.php");
include_once("../../funcoes.php");
include_once('../../classes/MovimentoClass.php');

$usuario = carregaUsuario();

$projeto = $_REQUEST['pro'];
$regiao = $_REQUEST['reg'];

// -----------------------------------------------------------------------------
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
require_once '../../classes/phpexcel/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();

// Set document properties
$objPHPExcel->getProperties()->setCreator("INTRANET")
        ->setLastModifiedBy("INTRANET")
        ->setTitle("Relatorio PIS NIT")
        ->setSubject("Relatorio PIS NIT")
        ->setDescription("Arquivo gerado pelo sistema.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Planilha");


// largura das colunas
foreach (range('A', 'X') as $columnID) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
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
// -----------------------------------------------------------------------------
// consulta igual ao relatório -------------------------------------------------
$objMovimento = new Movimentos();

// Id da Folha
//$enc = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
//$folha = $enc[1];

$qry_projeto = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_regiao='{$usuario['id_regiao']}' ORDER BY nome");
$arrayProjetos = array('-1' => '-- Selecione --');
while ($dados_projeto = mysql_fetch_assoc($qry_projeto)) {
    $arrayProjetos[$dados_projeto['id_projeto']] = $dados_projeto['nome'];
}

$qry_movimentos = mysql_query("SELECT id_mov,cod,descicao,categoria FROM rh_movimentos ORDER BY categoria,descicao;") or die("Erro ao selecionar movimentos: " . mysql_error());
$arrayMovimentos = array((-1) => '-- Todos os Movimentos --');
while ($dados_mov = mysql_fetch_assoc($qry_movimentos)) {
    $cat = (!empty($dados_mov['categoria'])) ? " - " . $dados_mov['categoria'] . " - " : ' - ';
    $arrayMovimentos[$dados_mov['id_mov']] = $dados_mov['cod'] . $cat . $dados_mov['descicao'];
}

$projeto = (!empty($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mes = (!empty($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$ano = (!empty($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

if (isset($_REQUEST['projeto']) AND ! empty($_REQUEST['projeto'])) {

    // Consulta da Folha
    $qr_folha = mysql_query("
    SELECT A.*, date_format(A.data_inicio, '%d/%m/%Y') AS data_inicio_br, 
    date_format(A.data_fim, '%d/%m/%Y') AS data_fim_br,
    date_format(A.data_proc, '%d/%m/%Y') AS data_proc_br ,
    B.id_projeto, B.nome nomeProjeto
    FROM rh_folha A LEFT JOIN projeto B ON B.id_projeto = A.projeto
    WHERE /*A.id_folha = '$folha' AND  A.mes = {$mes} AND A.ano = {$ano} AND*/ A.projeto = {$projeto} AND A.status = '2'");
    $row_folha = mysql_fetch_array($qr_folha);

    // Definindo MÃªs da Folha
    if (!empty($decimo_terceiro)) {
        switch ($tipo_terceiro) {
            case 1: $mes_folha = '13&ordm; Primeira parcela';
                break;
            case 2: $mes_folha = '13&ordm; Segunda parcela';
                break;
            case 3: $mes_folha = '13&ordm; Integral';
                break;
        }
    } else {
        $mes_folha = mesesArray($row_folha['mes']) . " / {$row_folha['ano']}";
    }

    // select novo para pegar só os movimentos
    $cond_mov = (isset($_REQUEST['movimento']) && $_REQUEST['movimento'] != (-1)) ? " AND id_mov = {$_REQUEST['movimento']}" : "";
    $query_mov_clt = "SELECT B.id_clt, B.nome, A.cod_movimento, A.nome_movimento, A.valor_movimento, A.tipo_movimento
                        FROM rh_movimentos_clt AS A
                        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                        WHERE A.id_projeto = '{$projeto}' AND ((A.mes_mov = '{$row_folha['mes']}' AND A.ano_mov = '{$row_folha['ano']}') OR A.lancamento = 2) AND A.status = 1 $cond_mov
                        ORDER BY B.nome";

    $result_mov_clt = mysql_query($query_mov_clt);
    $total_participantes = mysql_num_rows($result_mov_clt);
    while ($row = mysql_fetch_array($result_mov_clt)) {
        $clts[] = $row;
    }
//    
//    echo "<pre>";
//        print_r($row_folha);
//    echo "</pre>";
//    exit();
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Folha Sint&eacute;tica de CLT");
$breadcrumb_pages = array("Gestão de RH" => "../../principalrh.php?regiao=$usuario[id_regiao]");
// fim consulta igual ao relatório ---------------------------------------------
// criacao do xls --------------------------------------------------------------
$linha = 1;

//    if ($row_clt['id_projeto'] != $projeto_anterior) {
//        // CABECALHO -----------------------------------------------------------
//        // projeto
//        $query_proj = "SELECT nome FROM projeto WHERE id_projeto = '{$row_clt['id_projeto']}'";
//        $result_proj = mysql_query($query_proj) OR die('erro ao selecionar nome do projeto: ' . mysql_error());
//        $row_proj = mysql_fetch_assoc($result_proj);
//
//
//        $array_titulo = array('Matricula', 'Nome', 'Data de Nascimento', 'RG', 'CPF', 'PIS', 'Número da CTPS', 'Série da CTPS', 'UF da CTPS', 'Função', 'Mãe',);
//
//        $linha_seguinte = $linha + 1;
//
//        $objPHPExcel->getActiveSheet()->setSharedStyle($header, "A" . $linha . ":K" . $linha_seguinte);
//
//        $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A' . $linha, utf8_encode($row_proj['nome']));
//        $sheet->mergeCells('A' . $linha . ':K' . $linha);
//        $linha++;
//
//
//        foreach ($array_titulo as $key => $value) {
//            $coluna = getLetraCol($key);
//            $objPHPExcel->setActiveSheetIndex(0)
//                    ->setCellValue($coluna . $linha, utf8_encode($value));
//        }
//        $linha++;
//        // FIM CABECALHO -------------------------------------------------------
//    }


    // CORPO -------------------------------------------------------------------

    $clt = "";
    foreach ($clts as $linha_clt) {
        // cor da linha ------------------------------------------------------------
        if ($linha % 2 == 0) {
            $objPHPExcel->getActiveSheet()->setSharedStyle($par, "A" . ($linha) . ":K" . ($linha));
        } else {
            $objPHPExcel->getActiveSheet()->setSharedStyle($impar, "A" . ($linha) . ":K" . ($linha));
        }

        // ID CLT - NOME
        $xls_nome = $linha_clt['id_clt'] . " - " . $linha_clt['nome'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $linha, utf8_encode($xls_nome));

        $linha++; // pula linha
        
        // COD MOV - NOME MOV
        $xls_mov = $linha_clt['cod_movimento'] . " - " . $linha_clt['nome_movimento'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $linha, utf8_encode($xls_mov));
        
        // TIPO MOVIMENTO
        $xls_tipo_mov = $linha_clt['tipo_movimento'];
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B' . $linha, utf8_encode($xls_tipo_mov));
        
        // VALOR
        $xls_valor = number_format($linha_clt['valor_movimento'], 2, ',', '.');
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('C' . $linha, utf8_encode($xls_valor));
    }

    // FIM CORPO ---------------------------------------------------------------
//    $projeto_anterior = $row_clt['id_projeto'];
    $linha++;

$objPHPExcel->getActiveSheet()->setSharedStyle($row_total, "A" . ($linha) . ":K" . ($linha));
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A' . $linha, utf8_encode("TOTAL DE FUNCIONÁRIOS"));
$objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('B' . $linha, utf8_encode($total_funcionarios));

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle(utf8_encode('Relatório de PIS-NIT'));

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a clientâ??s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="relatorio_pis_nit_' . date('YmdHis') . '.xls"');
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
?>
