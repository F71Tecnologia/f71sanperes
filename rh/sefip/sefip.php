<?php
include "../../conn.php";
include "../../classes/funcionario.php";
$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;
require("../fpdf/fpdf.php");

$folha = $_GET['folha'];
$regiao = $_GET['regiao'];

define('FPDF_FONTPATH','../fpdf/font/');
$pdf = new FPDF("P","cm","A4");
$pdf->SetAutoPageBreak(true,0.0);
$pdf->Open();

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$linha_regiao = mysql_fetch_assoc($qr_regiao);

$qr_folha = mysql_query("SELECT * FROM rh_folha WHERE status = '3' AND regiao = '$linha_regiao[id_regiao]' AND id_folha  = '$folha'");
$folha = mysql_fetch_assoc($qr_folha);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$folha[projeto]'");
$projeto = mysql_fetch_assoc($qr_projeto);

$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$Master' AND status = '1'");
$empresa = mysql_fetch_assoc($qr_empresa);

$meses = array('ERRO','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
?>
<html>
<head>
<title>Gerando SEFIP</title>
<meta http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
$Inicio = 0;
$Paginacao = 0;

$qr_pai = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha[id_folha]' AND id_regiao = '$folha[regiao]' AND status = '3' ORDER BY nome ASC");
$num_pai = mysql_num_rows($qr_pai);

// Soma Final do SEFIP

$qr_empregados = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha[id_folha]' AND id_regiao = '$folha[regiao]' AND status = '3'");
while($empregado = mysql_fetch_assoc($qr_empregados)) {

if($empregado['ferias'] == '1') {
$ano_folha = substr($folha['data_inicio'], 0, 4);
$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$empregado[id_clt]' AND year(data_ini) = '$ano_folha' AND rh_ferias.mes = '$folha[mes]'");
$ferias = mysql_fetch_assoc($qr_ferias);
}

$soma_remuneracao = ($empregado['salliquido'] - $empregado['a5029']) + $ferias['total_liquido'] + $soma_remuneracao;
$soma_remuneracao_13 = $empregado['a5029'] + $soma_remuneracao_13;
$soma_fgts = $empregado['fgts'] + $ferias['fgts'] + $soma_fgts;
$soma_inss = $empregado['inss'] + $ferias['inss'] + $soma_inss;
$soma_irrf = $empregado['a5021'] + $ferias['ir'] + $soma_irrf;

unset($qr_ferias);
unset($ferias);

}

unset($qr_empregados);
unset($empregado);

//

// Quebra Final

$qr_quebra_ferias_final = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha[id_folha]' AND id_regiao = '$folha[regiao]' AND status = '3' AND ferias = '1' ORDER BY nome ASC");
$num_quebra_ferias_final = mysql_num_rows($qr_quebra_ferias_final);
$quebra_ferias_final = $num_quebra_ferias_final * 0.7;

$max = 30;
$calc = gmp_div_q($num_pai, $max, GMP_ROUND_PLUSINF);
$pedaco = gmp_strval($calc);

$quebra_final = 5.5 + (0.7 * ($num_pai - (30 * ($pedaco - 1)))) + $quebra_ferias_final;

//

for($a=1; $a <= $pedaco; $a ++) {
	
$Paginacao++;

$pdf->SetFont('Arial','B',20);
$pdf->Cell(5, 30, " ");
$pdf->Image('imagens/fundo_pdf.gif', 0.5,0,22,16,'gif');
$pdf ->SetXY(1.1,2.8);
$pdf->Cell(0,0,"Relação de SEFIP",0,0,'L');
$pdf->SetFont('Arial','B',13);
$pdf ->SetXY(1.15,0.95);
$pdf->Cell(0,0,$empresa['razao'],0,0,'L');
$pdf->SetFont('Arial','B',7);
$pdf ->SetXY(18.4,0.8);
$pdf->Cell(0,0,"Página ".$Paginacao."/".$pedaco."",0,0,'L');
$pdf ->SetXY(1.15,1.5);
$pdf->Cell(0,0,"CNPJ: ".$empresa['cnpj']."",0,0,'L');
$pdf ->SetXY(17.6,1.5);
$pdf->Cell(0,0,date('d/m/Y  H:i'),0,0,'L');

$pdf -> SetFont('Arial','B',14);
$pdf -> SetXY(1.1,3.7);
$pdf -> Cell(0,0,"Folha de pagamento do mês de ".$meses[(int)$folha['mes']]." do projeto ".$folha['id_folha']." - ".$projeto['nome']."",0,0,'L');

$qr_empregados = mysql_query("SELECT rh_folha_proc.cod, rh_folha_proc.nome, rh_folha_proc.id_clt, rh_folha_proc.id_regiao, rh_folha_proc.ano, rh_folha_proc.mes, rh_folha_proc.salliquido, rh_folha_proc.a5029, rh_folha_proc.ferias, rh_folha_proc.a4002, rh_folha_proc.a5021, rh_clt.pis, rh_folha_proc.inss, rh_folha_proc.fgts FROM rh_folha_proc INNER JOIN rh_clt ON rh_folha_proc.id_clt = rh_clt.id_clt WHERE rh_folha_proc.id_folha = '$folha[id_folha]' AND rh_folha_proc.id_regiao = '$folha[regiao]' AND rh_folha_proc.status = '3' ORDER BY rh_clt.pis ASC LIMIT $Inicio,30");
while($empregado = mysql_fetch_assoc($qr_empregados)) {

if($empregado['ferias'] == '1') {
	$adicional_quebra = 1.4;
} else {
	$adicional_quebra = 0.7;
}

$quebra_linha = $quebra_linha + $adicional_quebra;
$quebra_ferias = $quebra_linha - 0.7;

if($empregado['ferias'] == '1') {
	$quebra = $quebra_ferias;
} else {
	$quebra = $quebra_linha;
}

$qr_filhos = mysql_query("SELECT * FROM dependentes WHERE id_bolsista = '$empregado[id_clt]' AND id_regiao = '$regiao' AND contratacao = '$tipo_contratacao'");
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

$pdf->SetFont('Arial','B',6.5);

$pdf ->SetXY(1.1,4.8+$quebra);
$pdf->Cell(0,0,$empregado['cod'],0,0,'L');

$pdf ->SetXY(2,4.8+$quebra);
$nome = substr($empregado['nome'], 0, 30);
$pdf->Cell(0,0,$nome,0,0,'L');

$pis = str_replace('-', '', $empregado['pis']);
$pdf ->SetXY(7.5,4.8+$quebra);
$pdf->Cell(0,0,$pis,0,0,'L');

$pdf ->SetXY(9.75,4.8+$quebra);
$pdf->Cell(0,0,number_format($empregado['salliquido'] - $empregado['a5029'], 2, ",", "."),0,0,'L');

$pdf ->SetXY(11.8,4.8+$quebra);
$pdf->Cell(0,0,number_format($empregado['a5029'], 2, ",", "."),0,0,'L');

$pdf ->SetXY(13.75,4.8+$quebra);
$pdf->Cell(0,0,number_format($empregado['fgts'], 2, ",", "."),0,0,'L');

$pdf ->SetXY(15,4.8+$quebra);
$pdf->Cell(0,0,number_format($empregado['inss'], 2, ",", "."),0,0,'L');

$pdf ->SetXY(16.1,4.8+$quebra);
$pdf->Cell(0,0,number_format($empregado['a5021'], 2, ",", "."),0,0,'L');

$pdf ->SetXY(17.9,4.8+$quebra);
$pdf->Cell(0,0,$numfilhos,0,0,'L');

$pdf ->SetXY(18.9,4.8+$quebra);
$pdf->Cell(0,0,"Folha",0,0,'L');

// Verifica se o empregado terá folha de férias

if($empregado['ferias'] == '1') {

$ano_folha = substr($folha['data_inicio'], 0, 4);
$qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$empregado[id_clt]' AND year(data_ini) = '$ano_folha' AND rh_ferias.mes = '$folha[mes]'");
$ferias = mysql_fetch_assoc($qr_ferias);

$pdf -> SetXY(14.45,4.8+$quebra_linha);
$pdf -> Cell(0,0,"Férias",0,0,'L');

$pdf -> SetXY(15.55,4.8+$quebra_linha);
$pdf -> Cell(0,0,"R$ ".number_format($ferias['total_remuneracoes'] - $ferias['inss'], 2, ",", ".")."",0,0,'L');

$pdf -> SetXY(18.2,4.8+$quebra_linha);
$pdf -> Cell(0,0,"R$ ".number_format($ferias['ir'], 2, ",", ".")."",0,0,'L');

}

//

$Inicio ++;

}

if($a == $pedaco) {
	
$pdf->SetFont('Arial','B',7.5);

$pdf ->SetXY(1.1,$quebra_final);
$pdf->Cell(0,0,"Pessoas Listadas: ".$num_pai."",0,0,'L');

$pdf ->SetXY(15,$quebra_final);
$pdf->Cell(0,0,"Total Remuneração: ".number_format($soma_remuneracao, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(15,$quebra_final+0.7);
$pdf->Cell(0,0,"Total Remuneração 13º: ".number_format($soma_remuneracao_13, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(15,$quebra_final+1.4);
$pdf->Cell(0,0,"Total FGTS: ".number_format($soma_fgts, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(15,$quebra_final+2.1);
$pdf->Cell(0,0,"Total INSS: ".number_format($soma_inss, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(15,$quebra_final+2.8);
$pdf->Cell(0,0,"Total IR: ".number_format($soma_irrf, 2, ",", ".")."",0,0,'L');
}

unset($quebra_linha);
unset($qr_empregados);
unset($empregado);

}

$pdf->Output("../arquivos/sefip.pdf");
echo "<b>Gerando arquivo PDF...</b>";
print "<script>location.href=\"../arquivos/sefip.pdf\"</script>";
$pdf->Close();
?>
</body>
</html>