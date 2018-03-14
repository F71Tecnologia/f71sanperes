<?php
include "../../conn.php";
include "../../classes/funcionario.php";

include "../../classes/LogClass.php";
$log = new Log();

$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;
require("../fpdf/fpdf.php");

$folha = $_GET['folha'];
$regiao = $_GET['regiao'];
$tipo_contratacao = $_GET['tipo'];

mysql_query("INSERT INTO pis (folha,regiao,tipo_contratacao,autor,data) VALUES ('$folha','$regiao','$tipo_contratacao','$_COOKIE[logado]',NOW())");

define('FPDF_FONTPATH','../fpdf/font/');
$pdf = new FPDF("P","cm","A4");
$pdf->SetAutoPageBreak(true,0.0);
$pdf->Open();

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao' AND id_master = '$Master'");
$linha_regiao = mysql_fetch_assoc($qr_regiao);

if($tipo_contratacao == 2) {
	$banco = "rh_folha";
	$banco2 = "rh_folha_proc";
	$coluna = "id_regiao";
	$codigo = "cod";
} elseif($tipo_contratacao == 3) {
	$banco = "folhas";
	$banco2 = "folha_cooperado";
	$coluna = "regiao";
	$codigo = "id_autonomo";
}

$qr_folha = mysql_query("SELECT * FROM $banco WHERE status = '3' AND regiao = '$linha_regiao[id_regiao]' AND id_folha  = '$folha'");
$folha = mysql_fetch_assoc($qr_folha);

$meses = array('ERRO','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$folha[projeto]'");
$projeto = mysql_fetch_assoc($qr_projeto);

$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$Master' AND status = '1'");
$empresa = mysql_fetch_assoc($qr_empresa);
?>
<html>
<head>
<title>Gerando PIS</title>
<meta http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
</head>
<body>
<?php

// SOMA DOS PIS

$qr_empregados = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna = '$folha[regiao]' AND status = '3'");
while($empregado = mysql_fetch_assoc($qr_empregados)) {

if($tipo_contratacao == 2) {
	$salario = $empregado['salbase'] + $empregado['rend'] - $empregado['desco'];
} elseif($tipo_contratacao == 3) {
	$salario = $empregado['salario'] + $empregado['adicional'] - $empregado['desconto'];
}

$pis = $salario / 100;
$somasalario = $somasalario + $salario;
$somapis = $somapis + $pis;

}

unset($qr_empregados);
unset($empregado);

//

$Inicio = 0;
$Paginacao = 0;

$qr_pai = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna = '$folha[regiao]' AND status = '3' ORDER BY nome ASC");
$num_pai = mysql_num_rows($qr_pai);

$max = 34;
// DIVIDE E ARREDONDA PARA CIMA - MAIKOM 20/09/2010 8:52 AM
//$calc = gmp_div_q($num_pai, $max, GMP_ROUND_PLUSINF);
//$pedaco = gmp_strval($calc);
// MUDADO PARA:
$calc = ceil($num_pai / $max);
$pedaco = (string) $calc;

$quebra_final = 5.5 + (0.7 * ($num_pai - (34 * ($pedaco - 1))));

for($a=1; $a <= $pedaco; $a ++) {
	
$Paginacao++;

$pdf->SetFont('Arial','B',20);
$pdf->Cell(5, 30, " ");
$pdf->Image('imagens/fundo_pdf.gif', 0.5,0,22,16,'gif');
$pdf ->SetXY(1.1,2.8);
$pdf->Cell(0,0,"Relação de PIS",0,0,'L');
$pdf->SetFont('Arial','B',13);
$pdf ->SetXY(1.15,0.95);
$pdf->Cell(0,0,$empresa['razao'],0,0,'L');
$pdf->SetFont('Arial','B',7);
$pdf ->SetXY(18.4,0.8);
$pdf->Cell(0,0,"Página ".$Paginacao."/".$pedaco."",0,0,'L');
$pdf ->SetXY(1.15,1.5);
$pdf->Cell(0,0,"CNPJ: $empresa[cnpj]",0,0,'L');
$pdf ->SetXY(17.6,1.5);
$pdf->Cell(0,0,date('d/m/Y  H:i'),0,0,'L');

$pdf->SetFont('Arial','B',14);
$pdf ->SetXY(1.1,3.7);
$pdf->Cell(0,0,"Folha de pagamento do mês de ".$meses[(int)$folha['mes']]." do projeto ".$folha['id_folha']." - ".$projeto['nome']."",0,0,'L');

$qr_empregados = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna = '$folha[regiao]' AND status = '3' ORDER BY nome LIMIT $Inicio,34");
while($empregado = mysql_fetch_assoc($qr_empregados)) {

if($tipo_contratacao == 2) {
	$salario = $empregado['salbase'] + $empregado['rend'] - $empregado['desco'];
} elseif($tipo_contratacao == 3) {
	$salario = $empregado['salario'] + $empregado['adicional'] - $empregado['desconto'];
}

$quebra_linha = $quebra_linha + 0.7;

$pdf->SetFont('Arial','B',9);

$pdf ->SetXY(1.35,4.8+$quebra_linha);
$pdf->Cell(0,0,$empregado[$codigo],0,0,'L');

$pdf ->SetXY(3,4.8+$quebra_linha);
$pdf->Cell(0,0,$empregado['nome'],0,0,'L');

$pdf ->SetXY(11.7,4.8+$quebra_linha);
$pdf->Cell(0,0,$empregado['cpf'],0,0,'L');

$pdf ->SetXY(15.2,4.8+$quebra_linha);
$pdf->Cell(0,0,"R$ ".number_format($salario, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(18.1,4.8+$quebra_linha);
$pdf->Cell(0,0,"R$ ".number_format($salario / 100, 2, ",", ".")."",0,0,'L');

$Inicio ++;

}

if($a == $pedaco) {
$pdf ->SetXY(1.35,$quebra_final);
$pdf->Cell(0,0,"Pessoas Listadas: ".$num_pai."",0,0,'L');

$pdf ->SetXY(11.65,$quebra_final);
$pdf->Cell(0,0,"Total BASE: R$ ".number_format($somasalario, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(16.65,$quebra_final);
$pdf->Cell(0,0,"Total PIS: R$ ".number_format($somapis, 2, ",", ".")."",0,0,'L');
}

unset($quebra_linha);
unset($qr_empregados);
unset($empregado);

}

$pdf->Output("../arquivos/pis.pdf");
echo "<b>Gerando arquivo PDF...</b>";
print "<script>location.href=\"../arquivos/pis.pdf\"</script>";
$pdf->Close();
$log->gravaLog('Relatório e Impostos', "Relatório Gerado: DARF PIS - Projeto ".$folha['id_folha']." - ".$projeto['nome'] . " - " . $meses[(int)$folha['mes']] . "/" . $folha['ano']);
?>
</body>
</html>