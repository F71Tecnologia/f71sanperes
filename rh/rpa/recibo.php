<?php 

include "../../conn.php";
include "../../funcoes/extenso.php";

$id = $_GET['ID']; // ID Rh_rpa
$qr_rpa = mysql_query("SELECT * FROM  rh_rpa WHERE id_rpa = '$id'");
$row_rpa = mysql_fetch_assoc($qr_rpa);

$qr_auto = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row_rpa[id_autonomo]'");
$row_auto = mysql_fetch_assoc($qr_auto);

$qr_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_auto[id_curso]'");
$row_curso = mysql_fetch_assoc($qr_curso);

$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '1'");
$row_empresa = mysql_fetch_assoc($qr_empresa);


$diretorio = "arquivos/";
$nome_arquivo = $row_rpa['id_rpa'].'.pdf';

$total_decontos = $row_rpa['inss']+$row_rpa['irrf'];

if(!file_exists($diretorio.$nome_arquivo) && empty($row_rpa['recibo'])):

// atualiza o status do recibo
mysql_query("UPDATE rh_rpa SET recibo = '1' WHERE id_rpa = '$row_rpa[id_rpa]'");

include "../fpdf/fpdf.php";
define('FPDF_FONTPATH','../fpdf/font/');

$pdf= new FPDF("P","cm","A4");
$pdf->SetAutoPageBreak(true,0.0); // Reduz a tolerância da margem inferior
$pdf->Open();
$pdf->SetFont('Arial','B',16);

$pdf->Cell(5, 30, " ");

$pdf->Image('rpa.jpg', 0.5,0.5,20,28,'jpg');

$pdf->SetXY(14.5,3.5);
$pdf->Write(0,$id);

$pdf->SetXY(3,4.5);
$pdf->Write(0,utf8_decode($row_empresa['razao']));


$pdf->SetXY(3,6.5);
$pdf->Write(0,utf8_decode('MATRÍCULA: '.$row_empresa['cnpj']));

$pdf->SetXY(3,8);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(15.5,0,utf8_decode('RECEBI DA EMPRESA ACIMA IDENTIFICADA POR PRESTAÇÃO DOS'),0,1,'L');
$pdf->SetXY(3,8.5);
$pdf->cell(15.5,0,utf8_decode('SERVIÇOS DE INSTRUTORIA ('.$row_curso['nome'].'),'));
$pdf->SetXY(3,9);
$pdf->cell(15.5,0,utf8_decode('A IMPORTÂNCIA DE R$'.number_format($row_rpa['valor']-$total_decontos,2,',','.')));
$pdf->SetXY(3,9.5);
$pdf->cell(15.5,0,utf8_decode('('.extenso($row_rpa['valor']-$total_decontos,true).').'));


$pdf->SetXY(3,10);
$pdf->SetFont('Arial','BIU',12);
$pdf->cell(15.5,0,utf8_decode('DISCRIMINAÇÃO ABAIXO'));


$pdf->SetXY(3,11);
$pdf->SetFont('Arial','',12);
$pdf->cell(15.5,0,utf8_decode('ESPECIFICAÇÃO:'));

$pdf->SetXY(3,12);
$pdf->cell(15.5,0,utf8_decode('I - VALOR DE SERVIÇOS PRESTADOS'));
$pdf->SetXY(13,12);
$pdf->cell(15.5,0,'R$'.number_format($row_rpa['valor'],2,',','.'));


$pdf->SetXY(3,12.5);
$pdf->cell(15.5,0,utf8_decode('II - REEMBOLSO (10% ATÉ O SALÁRIO BASE)'));
$pdf->SetXY(13,12.5);
$pdf->cell(15.5,0,'R$ 0,00');


$pdf->SetXY(3,13);
$pdf->cell(15.5,0,utf8_decode('VALOR BRUTO'));
$pdf->SetXY(13,13);
$pdf->cell(15.5,0,'R$ '.number_format($row_rpa['valor'],2,',','.'));

$pdf->SetXY(3,13.5);
$pdf->cell(15.5,0,utf8_decode('DESCONTOS'));

$pdf->SetXY(3,14);
$pdf->cell(15.5,0,utf8_decode('I- ISS'));
$pdf->SetXY(13,14);
$pdf->cell(15.5,0,'R$ 0,00');


$pdf->SetXY(3,14.5);
$pdf->cell(15.5,0,utf8_decode('II- IRRF (TABELA)'));
$pdf->SetXY(13,14.5);
$pdf->cell(15.5,0,'R$ '.number_format($row_rpa['irrf'],2,',','.'));


$pdf->SetXY(3,15);
$pdf->cell(15.5,0,utf8_decode('III- INSS (20%)'));
$pdf->SetXY(13,15);
$pdf->cell(15.5,0,'R$ '.number_format($row_rpa['inss'],2,',','.'));




$pdf->SetXY(3,16);
$pdf->cell(15.5,0,utf8_decode('TOTAL DESCONTOS'));
$pdf->SetXY(13,16);
$pdf->cell(15.5,0,'R$ '.number_format($total_decontos,2,',','.'));


$pdf->SetXY(3,16.5);
$pdf->cell(15.5,0,utf8_decode('VALOR LÍQUIDO'));
$pdf->SetXY(13,16.5);
$pdf->cell(15.5,0,'R$ '.number_format($row_rpa['valor']-$total_decontos,2,',','.'));

$pdf->SetXY(3,18);
$pdf->SetFont('Arial','BUI',12);
$pdf->cell(15.5,0,utf8_decode('IDENTIFICAÇÃO DO RECEBINTE'));


$pdf->SetXY(3,19);
$pdf->SetFont('Arial','',12);
$pdf->cell(15.5,0,utf8_decode('NOME COMPLETO: '.$row_auto['nome']),0,1,'C');

$pdf->SetXY(3,21);
$pdf->cell(15.5,0,utf8_decode('____________________________________________'),0,1,'C');

$pdf->SetXY(3,21.5);
$pdf->SetFont('Arial','I',10);
$pdf->cell(15.5,0,utf8_decode('ASSINATURA:'),0,1,'C');

$pdf->SetXY(3,23);
$pdf->SetFont('Arial','',12);
$pdf->cell(15.5,0,utf8_decode('PIS/PASEP:  '.$row_auto['pis']));

$pdf->SetXY(3,23.5);
$pdf->cell(15.5,0,utf8_decode('OCUPAÇÃO PRINCIPAL: '.$row_curso['nome']));

$pdf->SetXY(3,24);
$pdf->cell(15.5,0,utf8_decode('RG: '.$row_auto['rg']));
$pdf->SetXY(11,24);
$pdf->cell(15.5,0,utf8_decode('LOCAL: '.utf8_decode($row_auto['cidade'])));

$pdf->SetXY(3,24.5);
$pdf->cell(15.5,0,utf8_decode('CPF: '.$row_auto['cpf']));
$pdf->SetXY(11,24.5);
$pdf->cell(15.5,0,utf8_decode('DATA: '.implode("/",array_reverse(explode('-',$row_rpa['data'])))));

$pdf->SetXY(3,25);
$pdf->cell(15.5,0,utf8_decode('BANCO: '.$row_auto['banco']));
$pdf->SetXY(11,25);
$pdf->cell(15.5,0,utf8_decode('AG: '.$row_auto['agencia']));
$pdf->SetXY(14,25);
$pdf->cell(15.5,0,utf8_decode('C/C: '.$row_auto['conta']));



$pdf->Output($diretorio.$nome_arquivo);

endif;
echo '<script>window.location.href="'.$diretorio.$nome_arquivo.'"</script>';

?>