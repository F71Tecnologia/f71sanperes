<?php

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";

// RECEBENDO VARIAVEIS
$enc = $_REQUEST['enc'];
$enc = str_replace("--", "+", $enc);
$link = decrypt($enc);
$decript = explode("&", $link);
$regiao = $decript[0];
$clt = $decript[1];
$id_folha = $decript[2];

if ($clt == "todos") {
    $ini = $_REQUEST['ini'];
    $fim = $_REQUEST['fim'];
    $REfolhaproc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha' AND status = '3'  ORDER BY nome LIMIT $ini,50") or die(mysql_error());
    $NumRegistros = mysql_num_rows($REfolhaproc);
    $nomearquivo = "contracheques_clt.pdf";
} else {
    $sql_clt = "SELECT A.*, B.*, DATE_FORMAT(B.data_nasci, '%d/%m/%Y') AS data_nasci_f FROM rh_folha_proc AS A LEFT JOIN rh_clt AS B ON(A.id_clt=B.id_clt) WHERE A.id_clt = '$clt' AND A.id_folha = '$id_folha'";
//    echo $sql_clt;
    $REfolhaproc = mysql_query($sql_clt);
    $nomearquivo = "contracheque_unico_clt.pdf";
}

require("../fpdf/fpdf.php");
define('FPDF_FONTPATH', '../fpdf/font/');

define('CELL_W',13.8);
define('CELL_H',5);
define('CELL_H_MIN',0.8);

$pdf = new FPDF("L", "cm", "A4");
$pdf->AddPage();

$pdf->SetFont('Arial', '', 8);
$pdf->SetTopMargin(1);

while ($RowFolhaPro = mysql_fetch_array($REfolhaproc)) {
//    echo '<pre>';
//    print_r($RowFolhaPro);
//    echo '<pre>';
    $pdf->Cell(CELL_W, CELL_H_MIN, $RowFolhaPro['id_clt'].' - '.$RowFolhaPro['nome'], 1, '0', 'L');
    $pdf->Cell(CELL_W, CELL_H_MIN, null, 1, '0', 'C');
    $pdf->Ln(CELL_H_MIN);
    
    $dados = 'DT.NASCIMENTO: '.$RowFolhaPro['data_nasci_f'];
    
    $pdf->Cell(CELL_W, CELL_H, $dados, 1, '0', 'L');
    $pdf->Cell(CELL_W, CELL_H, null, 1, '0', 'L');
    $pdf->Ln(CELL_H);
    
    $pdf->Cell(CELL_W, CELL_H, null, 1, '0', 'L');
    $pdf->Cell(CELL_W, CELL_H, null, 1, '0', 'L');
    $pdf->Ln(CELL_H);
    $pdf->Cell(CELL_W, CELL_H, null, 1, '0', 'L');
    $pdf->Cell(CELL_W, CELL_H, null, 1, '0', 'L');
}





$pdf->Output('as.pdf', 'I');
