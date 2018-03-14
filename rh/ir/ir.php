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

mysql_query("INSERT INTO ir (folha,regiao,tipo_contratacao,autor,data) VALUES ('$folha','$regiao','$tipo_contratacao','$_COOKIE[logado]',NOW())");

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
	$irrf = "a5021";
	$cod_empregado = "id_clt";
} elseif($tipo_contratacao == 3) {
	$banco = "folhas";
	$banco2 = "folha_cooperado";
	$coluna = "regiao";
	$codigo = "id_autonomo";
	$irrf = "irrf";
	$cod_empregado = "id_autonomo";
}

$qr_folha = mysql_query("SELECT * FROM $banco WHERE status = '3' AND regiao = '$linha_regiao[id_regiao]' AND id_folha  = '$folha'");
$folha = mysql_fetch_assoc($qr_folha);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$folha[projeto]'");
$projeto = mysql_fetch_assoc($qr_projeto);

$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$Master' AND status = '1'");
$empresa = mysql_fetch_assoc($qr_empresa);

$meses = array('ERRO','Janeiro','Fevereiro','Mar�o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
?>
<html>
<head>
<title>Gerando IRRF</title>
<meta http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
$Inicio = 0;
$Paginacao = 0;

$qr_pai = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna = '$folha[regiao]' AND status = '3' ORDER BY nome ASC");
$num_pai = mysql_num_rows($qr_pai);

// Soma Final do IR

$qr_empregados = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna = '$folha[regiao]' AND status = '3'");
while($empregado = mysql_fetch_assoc($qr_empregados)) {

if($empregado['ferias'] == '1') {
$ano_folha = substr($folha['data_inicio'], 0, 4);
$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$empregado[$cod_empregado]' AND year(data_ini) = '$ano_folha' AND rh_ferias.mes = '$folha[mes]'");
$ferias = mysql_fetch_assoc($qr_ferias);
}

$somairbase = $empregado['base_irrf'] + ($ferias['total_remuneracoes'] - $ferias['inss']) + $somairbase;
$somair = $empregado[$irrf] + $ferias['ir'] + $somair;

unset($qr_ferias);
unset($ferias);

}

unset($qr_empregados);
unset($empregado);

//

// Quebra Final

$qr_quebra_ferias_final = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna = '$folha[regiao]' AND status = '3' AND ferias = '1' ORDER BY nome ASC");
$num_quebra_ferias_final = mysql_num_rows($qr_quebra_ferias_final);
$quebra_ferias_final = $num_quebra_ferias_final * 0.7;

$max = 30;
// DIVIDE E ARREDONDA PARA CIMA - MAIKOM 20/09/2010 8:52 AM
//$calc = gmp_div_q($num_pai, $max, GMP_ROUND_PLUSINF);
//$pedaco = gmp_strval($calc);
// MUDADO PARA:
$calc = ceil($num_pai / $max);
$pedaco = (string) $calc;


$quebra_final = 5.5 + (0.7 * ($num_pai - (30 * ($pedaco - 1)))) + $quebra_ferias_final;

//

for($a=1; $a <= $pedaco; $a ++) {
	
$Paginacao++;

$pdf->SetFont('Arial','B',20);
$pdf->Cell(5, 30, " ");
$pdf->Image('imagens/fundo_pdf.gif', 0.5,0,22,16,'gif');
$pdf ->SetXY(1.1,2.8);
$pdf->Cell(0,0,"Rela��o de IRRF",0,0,'L');
$pdf->SetFont('Arial','B',13);
$pdf ->SetXY(1.15,0.95);
$pdf->Cell(0,0,$empresa['razao'],0,0,'L');
$pdf->SetFont('Arial','B',7);
$pdf ->SetXY(18.4,0.8);
$pdf->Cell(0,0,"P�gina ".$Paginacao."/".$pedaco."",0,0,'L');
$pdf ->SetXY(1.15,1.5);
$pdf->Cell(0,0,"CNPJ: ".$empresa['cnpj']."",0,0,'L');
$pdf ->SetXY(17.6,1.5);
$pdf->Cell(0,0,date('d/m/Y  H:i'),0,0,'L');

$pdf->SetFont('Arial','B',14);
$pdf ->SetXY(1.1,3.7);
$pdf->Cell(0,0,"Folha de pagamento do m�s de ".$meses[(int)$folha['mes']]." do projeto ".$folha['id_folha']." - ".$projeto['nome']."",0,0,'L');

$qr_empregados = mysql_query("SELECT * FROM $banco2 WHERE id_folha = '$folha[id_folha]' AND $coluna = '$folha[regiao]' AND status = '3' ORDER BY nome LIMIT $Inicio,30");
while($empregado = mysql_fetch_assoc($qr_empregados)) {

if($empregado['ferias'] == '1') {
$adicional_quebra = 1.4;
} else {
$adicional_quebra = 0.7;
}

$quebra_linha = $quebra_linha + $adicional_quebra;
$quebra_ferias = $quebra_linha - 0.7;

$qr_filhos = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$empregado[$cod_empregado]' AND id_regiao = '$regiao' AND contratacao = '$tipo_contratacao'");
$filhos = mysql_fetch_assoc($qr_filhos);

$numfilhos = 0;
if(!empty($filhos['nome5'])) {
	$numfilhos = $numfilhos + 1;
}
if(!empty($filhos['nome4'])) {
		$numfilhos = $numfilhos + 1;
}
if(!empty($filhos['nome3'])) {
		$numfilhos = $numfilhos + 1;
}
if(!empty($filhos['nome2'])) {
		$numfilhos = $numfilhos + 1;
} 
if(!empty($filhos['nome1'])) {
		$numfilhos = $numfilhos + 1;
}

$pdf->SetFont('Arial','B',9);

if($empregado['ferias'] == '1') {
$pdf ->SetXY(1.1,4.8+$quebra_ferias);
} else {
$pdf ->SetXY(1.1,4.8+$quebra_linha);
}
$pdf->Cell(0,0,$empregado[$codigo],0,0,'L');

if($empregado['ferias'] == '1') {
$pdf ->SetXY(2.2,4.8+$quebra_ferias);
} else {
$pdf ->SetXY(2.2,4.8+$quebra_linha);
}
$pdf->Cell(0,0,$empregado['nome'],0,0,'L');

if($empregado['ferias'] == '1') {
$pdf ->SetXY(10.25,4.8+$quebra_ferias);
} else {
$pdf ->SetXY(10.25,4.8+$quebra_linha);
}
$pdf->Cell(0,0,$empregado['cpf'],0,0,'L');

if($empregado['ferias'] == '1') {
$pdf ->SetXY(13.5,4.8+$quebra_ferias);
} else {
$pdf ->SetXY(13.5,4.8+$quebra_linha);
}
$pdf->Cell(0,0,$numfilhos,0,0,'L');

if($empregado['ferias'] == '1') {
$pdf ->SetXY(14.45,4.8+$quebra_ferias);
} else {
$pdf ->SetXY(14.45,4.8+$quebra_linha);
}
$pdf->Cell(0,0,"Folha",0,0,'L');

if($empregado['ferias'] == '1') {
$pdf ->SetXY(15.55,4.8+$quebra_ferias);
} else {
$pdf ->SetXY(15.55,4.8+$quebra_linha);
}
$pdf->Cell(0,0,"R$ ".number_format($empregado['base_irrf'], 2, ",", ".")."",0,0,'L');

if($empregado['ferias'] == '1') {
$pdf ->SetXY(18.2,4.8+$quebra_ferias);
} else {
$pdf ->SetXY(18.2,4.8+$quebra_linha);
}
$pdf->Cell(0,0,"R$ ".number_format($empregado[$irrf], 2, ",", ".")."",0,0,'L');

// Verifica se o empregado ter� folha de f�rias

if($empregado['ferias'] == '1') {

$ano_folha = substr($folha['data_inicio'], 0, 4);
$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$empregado[$cod_empregado]' AND year(data_ini) = '$ano_folha' AND rh_ferias.mes = '$folha[mes]'");
$ferias = mysql_fetch_assoc($qr_ferias);

$pdf ->SetXY(14.45,4.8+$quebra_linha);
$pdf->Cell(0,0,"F�rias",0,0,'L');

$pdf ->SetXY(15.55,4.8+$quebra_linha);
$pdf->Cell(0,0,"R$ ".number_format($ferias['total_remuneracoes'] - $ferias['inss'], 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(18.2,4.8+$quebra_linha);
$pdf->Cell(0,0,"R$ ".number_format($ferias['ir'], 2, ",", ".")."",0,0,'L');

}

//

$Inicio ++;

}

if($a == $pedaco) {
$pdf ->SetXY(1.1,$quebra_final);
$pdf->Cell(0,0,"Pessoas Listadas: ".$num_pai."",0,0,'L');

$pdf ->SetXY(11.7,$quebra_final);
$pdf->Cell(0,0,"Total BASE: R$ ".number_format($somairbase, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(16.65,$quebra_final);
$pdf->Cell(0,0,"Total IR: R$ ".number_format($somair, 2, ",", ".")."",0,0,'L');
}

unset($quebra_linha);
unset($qr_empregados);
unset($empregado);

}

$pdf->Output("../arquivos/ir.pdf");
echo "<b>Gerando arquivo PDF...</b>";
print "<script>location.href=\"../arquivos/ir.pdf\"</script>";
$pdf->Close();
$log->gravaLog('Relat�rio e Impostos', "Relat�rio Gerado: DARF IRRF - Projeto ".$folha['id_folha']." - ".$projeto['nome'] . " - " . $meses[(int)$folha['mes']] . "/" . $folha['ano']);
?>
</body>
</html>