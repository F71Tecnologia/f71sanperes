<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/regiao.php');
include('../../classes/clt.php');
include('../../classes/curso.php');

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
list($regiao,$idclt,$id) = explode('&',decrypt(str_replace('--','+',$_REQUEST['enc'])));
//

if($_COOKIE[logado] == 87){
    
    echo 'aqui';
    exit;
}


$RE_recisao = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao = '$id'");
$Row        = mysql_fetch_array($RE_recisao);

$RE_motivo  = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$Row[motivo]'");
$Row_motivo = mysql_fetch_array($RE_motivo);

$nomearquivo = 'rescisao_complementar_'.$idclt.'_1.pdf';

$data      = date('d/m/Y');
$ClassDATA = new regiao();
$ClassDATA -> RegiaoLogado();

require("../fpdf/fpdf.php");
define('FPDF_FONTPATH','../fpdf/font/');

$pdf= new FPDF("P","cm","A4");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Termo de Rescis&atilde;o de Contrato PDF</title>
</head>
<body>
<?php

# -----------------------------------------------------------------------------------
# |                   RECOLHENDO INFORMAÇOES DO PARTICIPANTE                        |
# -----------------------------------------------------------------------------------

$Clt           = new clt();
$Clt          -> MostraClt($idclt);
$pis 		   = $Clt -> pis;
$nome 		   = $Clt -> nome;
$codigo 	   = $Clt -> campo3;
$endereco 	   = $Clt -> endereco;
$bairro	 	   = $Clt -> bairro;
$cidade 	   = $Clt -> cidade;
$uf		 	   = $Clt -> uf;
$cep	 	   = $Clt -> cep;
$cartrab 	   = $Clt -> campo1;
$cpf	 	   = $Clt -> cpf;
$data_nasci	   = $Clt -> data_nasci;
$data_nasci2   = $Clt -> data_nasci2;
$mae	 	   = $Clt -> mae;
$data_entrada2 = $Clt -> data_entrada2;
$data_demi2	   = $Clt -> data_demi2;

# -- DADOS DA EMPRESA
$Clt      -> EmpresadoCLT($idclt);
$cnpj 	   = $Clt -> cnpj;
$razao 	   = $Clt -> razao;
$Eendereco = $Clt -> endereco;
$Ecep      = $Clt -> cep;
//

list($Eendereco1,$Ebairro,$Ecidade,$Euf) = explode(' - ',$Eendereco);

$Sal_base 		= number_format($Row['sal_base'], 2,",",".");
$valor_multa	= number_format($Row['sal_base'], 2,",",".");
$rendimentos    = number_format($Row['total_liquido'], 2,",",".");
$descontos      = number_format($Row['total_liquido'], 2,",",".");
$to_rendimentos = number_format($Row['total_liquido']+$Row['sal_base'], 2,",",".");
$to_descontos 	= number_format($Row['total_liquido'], 2,",",".");
$to_liquido     = number_format($Row['sal_base'], 2,",",".");

if(empty($Row['data_aviso'])) {
	$data_aviso = $data_demi2;
} else {
	$data_aviso = implode('/', array_reverse(explode('-', $Row['data_aviso'])));
}

# -----------------------------------------------------------------------------------
# |                   INICIANDO O PROCESSO DE CRIAÇÃO DO PDF                        |
# -----------------------------------------------------------------------------------


$pdf->SetAutoPageBreak(true,0.0); // Reduz a tolerância da margem inferior
$pdf->Open();
$pdf->SetFont('Arial','B',8);
$pdf->Cell(5, 30, " ");

$pdf->Image('recisao.jpg', 0.5,0.5,20,28,'jpg');

$nPag = $pdf->PageNo();

#-- DADOS DA EMPRESA -------------------------------------------------------------

$pdf ->SetXY(2.2,2.1);
$pdf->Cell(0,0,$cnpj,0,0,'L');

$pdf ->SetXY(7.4,2.1);
$pdf->Cell(0,0,$razao,0,0,'L');

$pdf ->SetXY(2.2,3);
$pdf->Cell(0,0,$Eendereco1,0,0,'L');

$pdf ->SetXY(15.2,3);
$pdf->Cell(0,0,$Ebairro,0,0,'L');

$pdf ->SetXY(2.2,3.8);
$pdf->Cell(0,0,$Ecidade,0,0,'L');

$pdf ->SetXY(7.2,3.8);
$pdf->Cell(0,0,$Euf,0,0,'L');

$pdf ->SetXY(9.2,3.8);
$pdf->Cell(0,0,$Ecep,0,0,'L');

$pdf ->SetXY(12.5,3.8);
$pdf->Cell(0,0,"",0,0,'L');			#CNAE

$pdf ->SetXY(15.2,3.8);
$pdf->Cell(0,0,"",0,0,'L'); 		#CNPJ/CEI Tomador/Obra

# -- DADOS D0 FUNCIONÁRIO --------------------------------------

$pdf ->SetXY(2.2,4.8);
$pdf->Cell(0,0,$pis,0,0,'L');

$pdf ->SetXY(7.4,4.8);
$pdf->Cell(0,0,$nome." ( $codigo )",0,0,'L');

$pdf ->SetXY(2.2,5.7);
$pdf->Cell(0,0,$endereco,0,0,'L');

$pdf ->SetXY(15.2,5.7);
$pdf->Cell(0,0,$bairro,0,0,'L');

$pdf ->SetXY(2.2,6.5);
$pdf->Cell(0,0,$cidade,0,0,'L');

$pdf ->SetXY(9.2,6.5);
$pdf->Cell(0,0,$uf,0,0,'L');

$pdf ->SetXY(10.4,6.5);
$pdf->Cell(0,0,$cep,0,0,'L');

$pdf ->SetXY(14.2,6.5);
$pdf->Cell(0,0,$cartrab,0,0,'L');

$pdf ->SetXY(2.2,7.4);
$pdf->Cell(0,0,$cpf,0,0,'L');

$pdf ->SetXY(7.2,7.4);
$pdf->Cell(0,0,$data_nasci2,0,0,'L');

$pdf ->SetXY(10.4,7.4);
$pdf->Cell(0,0,$mae,0,0,'L');

$pdf ->SetXY(2.2,8.3);
$pdf->Cell(0,0,"R$ ".$Sal_base,0,0,'L');

$pdf ->SetXY(7.9,8.3);
$pdf->Cell(0,0,$data_entrada2,0,0,'L');

$pdf ->SetXY(12.2,8.3);
$pdf->Cell(0,0,$data_aviso,0,0,'L');

$pdf ->SetXY(17.2,8.3);
$pdf->Cell(0,0,$data_demi2,0,0,'L');

$pdf ->SetXY(2.2,9.1);
$pdf->Cell(0,0,$Row_motivo['especifica'],0,0,'L');

$pdf ->SetXY(11.7,9.1);
$pdf->Cell(0,0,sprintf('%02d',$Row['fgts_saque']),0,0,'L');

$pdf ->SetXY(14.2,9.1);
$pdf->Cell(0,0,"0,00 %",0,0,'L');		# Pensão alimenticia

$pdf ->SetXY(17.8,9.1);
$pdf->Cell(0,0,"01",0,0,'L');			# Cat. Trabalhador

$pdf ->SetXY(8.8,15.6);
$pdf->Cell(0,0,'Valor Pago',0,0,'L');

$pdf ->SetXY(8.8,16);
$pdf->Cell(0,0,'na Rescisão',0,0,'L');

$pdf ->SetXY(12.0,15.8);
$pdf->Cell(0,0,'R$ '.$rendimentos,0,0,'L');

$pdf ->SetXY(14.7,15.6);
$pdf->Cell(0,0,'Valor Pago',0,0,'L');

$pdf ->SetXY(14.7,16);
$pdf->Cell(0,0,'na Rescisão',0,0,'L');

$pdf ->SetXY(18.1,15.8);
$pdf->Cell(0,0,'R$ '.$descontos,0,0,'L');

$pdf ->SetXY(8.7,16.8);
$pdf->Cell(0,0,'Multa por Atraso',0,0,'L');

$pdf ->SetXY(12.0,16.8);
$pdf->Cell(0,0,'R$ '.$valor_multa,0,0,'L');

$pdf ->SetXY(18,17.9);
$pdf->Cell(0,0,'',0,0,'L');

$pdf ->SetXY(12.1,20.3);
$pdf->Cell(0,0,"R$ ".$to_rendimentos,0,0,'L');			#TOTAL BRUTO

$pdf ->SetXY(17.8,19.4);
$pdf->Cell(0,0,"R$ ".$to_descontos,0,0,'L');			#TOTAL DEDUÇÕES

$pdf ->SetXY(17.8,20.3);
$pdf->Cell(0,0,"R$ ".$to_liquido,0,0,'L');				#LIQUIDO A RECEBER

$valor_multa = str_replace(',','.',str_replace('.','',$valor_multa));

// Inserindo na Base
mysql_query("INSERT INTO rh_rescisao_complementar (rescisao_rescisao,rescisao_valor_multa,rescisao_autor,rescisao_status,rescisao_criacao) VALUES ('$id','$valor_multa','$_COOKIE[logado]','1',NOW())");

# --------------- FINALIZANDO, FECHANDO E SALVANDO O ARQUIVO
$pdf->Output("../arquivos/recisaopdf/$nomearquivo");
echo "Gerando arquivo PDF.";
print "<script>location.href=\"../arquivos/recisaopdf/$nomearquivo\"</script>";
$pdf->Close();
?>
</body>
</html>