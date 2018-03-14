<?php
$BASEURLINTRANET = "../";

if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='{$BASEURLINTRANET}login.php'>Logar</a>";
    exit;
}

include_once($BASEURLINTRANET.'conn.php');
include_once($BASEURLINTRANET.'wfunction.php');
include_once($BASEURLINTRANET.'rh/fpdf/fpdf.php');

/**
 * RECEBENDO VARIVEIS
 */
$usuario = carregaUsuario();

/**
 * Instanciando Classe
 */
$objPdf = new FPDF("P", "cm", "A4");
$date = date('YmdHis');
//$cod = md5($_REQUEST['id'].$date);

/**
 * PEGA INFORMAÇÕES DA SAIDA
 */
$sqlSaidas = "
SELECT A.id_saida, CAST(REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor , A.adicional, A.tipo, A.nome,
IF(A.tipo = 327, CONCAT(TIMESTAMPDIFF(MONTH, E.contratado_em, A.data_vencimento), '/', TIMESTAMPDIFF(MONTH, E.contratado_em,E.encerrado_em)),A.n_documento) AS n_documento, 
DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS data_vencimento, DATE_FORMAT(A.data_proc, '%d/%m/%Y %H:%i:%s') AS data_proc, A.impresso, A.user_impresso, A.data_impresso, A.id_projeto, B.nome AS nomeProjeto, D.nome_grupo, C.nome AS nomeTipo,
F.nome1 AS nomeFuncionario, A.valor_bruto, A.valor_juros, A.valor_multa, A.taxa_expediente, A.desconto, A.valor_ir
FROM saida A 
LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
LEFT JOIN entradaesaida AS C ON (A.tipo = C.id_entradasaida)
LEFT JOIN entradaesaida_grupo AS D ON (C.grupo = D.id_grupo)
LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
LEFT JOIN funcionario F ON (A.id_user = F.id_funcionario)
WHERE A.id_saida = '{$_REQUEST['id']}'
ORDER BY A.id_projeto, id_saida";
//print_array($sqlSaidas);
$qrySaidas = mysql_query($sqlSaidas) or die(mysql_error());
$rowSaidas = mysql_fetch_assoc($qrySaidas);


//print_array($dadosEmpresa);

/**
 * Pagina 1
 */
$objPdf->AddPage();
$y = 0;
$x = 1;
$d = 19;


$objPdf->Image("../imagens/logomaster{$usuario['id_master']}.gif",9,0.5,3,2);
$objPdf->SetFont('Times', 'B', 12);
$objPdf->SetXY($x, 3 + $y);
$objPdf->Cell($d, 0.5, "CONFIRMAÇÃO DE CADASTRO SAÍDA ({$_REQUEST['id']})", 0, 1, 'C');
$objPdf->SetFont('Times', '', 10);
$objPdf->SetXY($x, 4 + $y);
$objPdf->Cell($d, 0.5, 'NOME: ' . $rowSaidas['nome'], 0, 0, 'L');
$objPdf->SetXY($x, 5 + $y);
$objPdf->Cell($d, 0.5, "PROJETO: {$rowSaidas['nomeProjeto']} | GRUPO: {$rowSaidas['nome_grupo']} | TIPO: {$rowSaidas['nomeTipo']}", 0, 0, 'L');
$objPdf->SetXY($x, 6 + $y);
$objPdf->Cell($d, 0.5, "VALOR BRUTO: " . number_format($rowSaidas['valor_bruto'], 2, ',', '.') . " | JUROS: " . number_format($rowSaidas['valor_juros'], 2, ',', '.') . " | MULTA: " . number_format($rowSaidas['valor_multa'], 2, ',', '.') . " | EXPEDIENTE: " . number_format($rowSaidas['taxa_expediente'], 2, ',', '.'), 0, 0, 'L');
$objPdf->SetXY($x, 7 + $y);
$objPdf->Cell($d, 0.5, "IR: " . number_format($rowSaidas['valor_ir'], 2, ',', '.') . " | DESCONTO: " . number_format($rowSaidas['desconto'], 2, ',', '.') . " | VALOR LÍQUIDO: " . number_format($rowSaidas['valor'], 2, ',', '.'), 0, 0, 'L');
$objPdf->SetXY($x, 8 + $y);
$objPdf->Cell($d, 0.5, "GRUPO: {$rowSaidas['nome_grupo']} | TIPO: {$rowSaidas['nomeTipo']}", 0, 0, 'L');
$objPdf->SetXY($x, 9 + $y);
$objPdf->Cell($d, 0.5, "VENCIMENTO: {$rowSaidas['data_vencimento']} | Nº DOC: {$rowSaidas['n_documento']}", 0, 0, 'L');
$objPdf->SetXY($x, 10 + $y);
$objPdf->Cell($d, 0.5, "CADASTRADO POR: {$rowSaidas['nomeFuncionario']} | {$rowSaidas['data_proc']}", 0, 0, 'L');
$objPdf->SetFont('Times', '', 8);
//$objPdf->SetXY($x, 11 + $y);
//$objPdf->Cell($d, 0.5, $cod, 0, 0, 'L');

/* PASTA */
$diretorio = 'pdf_confirmacao';
if (!file_exists("$diretorio")) {
    mkdir($diretorio, 777);
}

$objPdf->Output("{$diretorio}/{$_REQUEST['id']}_{$date}.pdf", 'F'); 

$dirFile = "{$diretorio}/{$_REQUEST['id']}_{$date}.pdf";
//header("Content-Type: application/save");
//header("Content-Length:$dirFile");
//header("Content-Disposition: attachment; filename='{$_REQUEST['id']}_{$date}.pdf'");
//header("Content-Transfer-Encoding: binary");
//header('Expires: 0');
//header('Pragma: no-cache');
//$fp = fopen("$dirFile", "r");
//fpassthru($fp);
//fclose($fp);
//sleep(3);
//echo "Location: $dirFile";
header("Location: $dirFile");
exit;
?>