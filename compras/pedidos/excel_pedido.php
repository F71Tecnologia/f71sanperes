<?php

header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/pedidosClass.php');
include('../../wfunction.php');
include('../../classes/PHPExcel/PHPExcel.php');

$usuario = carregaUsuario();

$pedido = new pedidosClass();


$id_fornecedor = $_REQUEST['id_prestador'];
$filtra_tipo = $_REQUEST['filtra_tipo'];
$condicao = (!empty($filtra_tipo)) ? " AND a.tipo = '$filtra_tipo' " : "";
$query = "SELECT *,b.valor_produto AS vUnCom 
        FROM nfe_produtos AS a 
        INNER JOIN produto_fornecedor_assoc AS b ON (a.id_prod = b.id_produto) 
        WHERE b.id_fornecedor = '$id_fornecedor' $condicao and a.status = 1;";
$qry = mysql_query($query);






/** Error reporting */
//error_reporting(E_ALL);

/** Include path * */
//ini_set('include_path', ini_get('include_path').';../Classes/');

/** PHPExcel */
include '../../classes/PHPExcel/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include '../../classes/PHPExcel/PHPExcel/Writer/Excel2007.php';

include 'aux_excel.php';

// Create new PHPExcel object
//echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();
// Set properties
//echo date('H:i:s') . " Set properties\n";

$objPHPExcel->getProperties()->setCreator("F71");
$objPHPExcel->getProperties()->setLastModifiedBy("F71");
$objPHPExcel->getProperties()->setTitle("Relatorio");
$objPHPExcel->getProperties()->setSubject("Relatorio");
$objPHPExcel->getProperties()->setDescription("Relatorio");


// Add some data
//echo date('H:i:s') . " Add some data\n";
$objPHPExcel->setActiveSheetIndex(0);

list($width, $height, $type, $attr) = getimagesize("../../imagens/logomaster6.gif");

// cabecalho -------------------------------------------------------------------
$objPHPExcel->getActiveSheet()->SetCellValue('A1', '');
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight($height);
$sheet->mergeCells("A1:K1");
$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'SOLICITACAO DE COMPRAS/SERVICOS - SC');
$sheet->mergeCells("A2:K2");


//$objPHPExcel->getActiveSheet()->setSharedStyle($center, "D1:{$col_x}3");
// cabecalho -------------------------------------------------------------------
// imagens ---------------------------------------------------------------------
$gdImage = imagecreatefromgif("../../imagens/logomaster6.gif");
// Add a drawing to the worksheetecho date('H:i:s') . " Add a drawing to the worksheet\n";
$objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
$objDrawing->setName('Sample image');
$objDrawing->setDescription('Sample image');
$objDrawing->setImageResource($gdImage);
$objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF);
$objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_GIF);
$objDrawing->setHeight($height * 0.9);
$objDrawing->setWidth($width * 0.9);
$objDrawing->setOffsetX(5);
$objDrawing->setOffsetY($height * 0.1);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objDrawing->setCoordinates('A1');

// imagens ---------------------------------------------------------------------

$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'Responsavel pela Solicitacao:');
$sheet->mergeCells("A3:E3");
$objPHPExcel->getActiveSheet()->SetCellValue('F3', 'Unidade:');
$sheet->mergeCells("F3:K3");
$objPHPExcel->getActiveSheet()->SetCellValue('A4', 'Data da Solicitacao:');
$sheet->mergeCells("A4:E4");
$objPHPExcel->getActiveSheet()->SetCellValue('F4', 'Fornecedor:');
$sheet->mergeCells("F4:K4");

// cabecalho das colunas -------------------------------------------------------
$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'Item');
$objPHPExcel->getActiveSheet()->SetCellValue('B5', 'Descricao');
$objPHPExcel->getActiveSheet()->SetCellValue('C5', 'Unidade de Fornecimento');
$objPHPExcel->getActiveSheet()->SetCellValue('D5', 'RDSS1');
$objPHPExcel->getActiveSheet()->SetCellValue('E5', 'RDSS2');
$objPHPExcel->getActiveSheet()->SetCellValue('F5', 'Total Saidas');
$objPHPExcel->getActiveSheet()->SetCellValue('G5', 'RSE');
$objPHPExcel->getActiveSheet()->SetCellValue('H5', 'Quant.');
$objPHPExcel->getActiveSheet()->SetCellValue('I5', 'Valor Unitario');
$objPHPExcel->getActiveSheet()->SetCellValue('J5', 'Total');
$objPHPExcel->getActiveSheet()->SetCellValue('K5', 'ID Sistema');
// cabecalho das colunas -------------------------------------------------------
// linhas ----------------------------------------------------------------------
$linha = 5;
$cont = 1;
while ($row = mysql_fetch_assoc($qry)) {

    $total_linha = 0;
    $objPHPExcel->getActiveSheet()->SetCellValue("A{$linha}", $cont);
    $objPHPExcel->getActiveSheet()->SetCellValue("B{$linha}", $row['xProd']);
    $objPHPExcel->getActiveSheet()->SetCellValue("C{$linha}", $row['uCom']);
    $objPHPExcel->getActiveSheet()->SetCellValue("I{$linha}", $row['vUnCom']);
    $objPHPExcel->getActiveSheet()->SetCellValue("J{$linha}", $row["=I{$linha}*H{$linha}"]);
    $objPHPExcel->getActiveSheet()->SetCellValue("K{$linha}", $row['id_prod']);

    $linha++;
    $cont++;
}
// -----------------------------------------------------------------------------
//foreach(range('A','K') as $columnID) {
//    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
//        ->setAutoSize(true);
//}
// Rename sheet
//echo date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
//$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a clientâ??s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="estatisticas_diarias_' . date('YmdHis') . '.xls"');
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


// Save Excel 2007 file
echo date('H:i:s') . " Write to Excel2007 format\n";
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));

// Echo done
echo date('H:i:s') . " Done writing file.\r\n";
?>


