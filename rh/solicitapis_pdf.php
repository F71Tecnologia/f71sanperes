<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>
<body>
<?php
include('../conn.php');

$projeto = $_GET['pro'];
$clt	 = $_GET['clt'];
$coop	 = $_GET['bol'];

if(isset($coop)) {

	$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') as data_nasci FROM autonomo WHERE id_autonomo = '$coop'");
	$row = mysql_fetch_assoc($result);
	$vinculo = $row['id_cooperativa'];
	$result_empresa = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$vinculo'");
	$row_empresa = mysql_fetch_array($result_empresa);

} elseif(isset($clt)) {
	
	$result = mysql_query("SELECT * , date_format(data_nasci, '%d/%m/%Y') as data_nasci FROM rh_clt WHERE id_clt = '$clt'");
    $row = mysql_fetch_array($result);
	$vinculo = $row['rh_vinculo'];
	$result_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_empresa = '$vinculo'");
	$row_empresa = mysql_fetch_array($result_empresa);

}

require_once("fpdf/fpdf.php");

define('FPDF_FONTPATH','fpdf/font/');
$pdf= new FPDF("P","cm","A4");
$pdf->SetAutoPageBreak(true,0.5); //Reduz a tolerancia da margem inferior
$pdf->Open();
$pdf->SetFont('Arial','B',9);
$pdf->Cell(5, 30, " ");

//$pdf->Output("arquivos/pis_".$clt."_".$projeto."_".$id_regiao,"I");

$pdf->Image('images/doc_cad_pis2.jpg', 1.85,0.9,17.4075, 28.1771 ,'jpg');

//Posicionamento horizontal da primeira parte do Documento de Cadastro do Trabalhado
$Lp = 0.08;

$pdf ->SetXY(8.3,1.8 + $Lp);
$pdf->Cell(3.1,0.5,$row_empresa['cnpj'],0,0,'L');

$pdf ->SetXY(8.2,2.4 + $Lp);
$pdf -> MultiCell(6.5,1.2,$row_empresa['endereco'],0,'L');

$pdf ->SetXY(1.89,5.8 + $Lp);
$pdf->Cell(4.1,0.5,$row_empresa['cnpj'],0,0,'L');

$pdf ->SetXY(6,5.8 + $Lp);
$pdf->Cell(13,0.5,$row_empresa['nome'],0,0,'L');

$pdf ->SetXY(1.89,6.6 + $Lp);
$pdf->Cell(17.3,0.5,$row_empresa['endereco'],0,0,'L');

$pdf ->SetXY(1.89,7.4 + $Lp);
$pdf->Cell(3.3,0.5,$row_empresa['tel'],0,0,'L');

$pdf ->SetXY(5.4,7.4 + $Lp);
$pdf->Cell(3.3,0.5,$row_empresa['fax'],0,0,'L');

$pdf ->SetXY(1.89,8.8 + $Lp);
$pdf->Cell(17.3,0.5,$row['nome'],0,0,'L');

$pdf ->SetXY(1.89,9.6 + $Lp);
$pdf->Cell(3.3,0.5,$row['data_nasci'],0,0,'L');

$pdf ->SetXY(5.2,9.6 + $Lp);
$pdf->Cell(1.2,0.5,$row['sexo'],0,0,'L');

$pdf ->SetXY(6.7,9.6 + $Lp);
$pdf->Cell(12.1,0.5,$row['mae'],0,0,'L');

$pdf ->SetXY(1.89,10.4 + $Lp);
$pdf->Cell(9.5,0.5,$row['naturalidade'],0,0,'L');

$pdf ->SetXY(11.5,10.4 + $Lp);
$pdf->Cell(1,0.5,$row['uf'],0,0,'L');

$pdf ->SetXY(12.5,10.4 + $Lp);
$pdf->Cell(2,0.5,'010',0,0,'L');

$pdf ->SetXY(1.89,11.2 + $Lp);
$pdf->Cell(2.8,0.5,$row['campo1'],0,0,'L');

$pdf ->SetXY(4.7,11.2 + $Lp);
$pdf->Cell(2.3,0.5,$row['serie_ctps'],0,0,'L');

$pdf ->SetXY(7,11.2 + $Lp);
$pdf->Cell(1,0.5,$row['uf_ctps'],0,0,'L');

$pdf ->SetXY(8.3,11.2 + $Lp);
$pdf->Cell(5,0.5,$row['cpf'],0,0,'L');

$pdf ->SetXY(1.89,12 + $Lp);
$pdf->Cell(4.3,0.5,$row['rg'],0,0,'L');

$pdf ->SetXY(6.3,12 + $Lp);
$pdf->Cell(1.8,0.5,$row['orgao'],0,0,'L');

$pdf ->SetXY(8.3,12 + $Lp);
$pdf->Cell(5.2,0.5,$row['titulo'],0,0,'L');

$pdf ->SetXY(13.5,12 + $Lp);
$pdf->Cell(1,0.5,$row['secao'],0,0,'L');

$pdf ->SetXY(1.89,12.8 + $Lp);
$pdf->Cell(17.3,0.5,$row['endereco'],0,0,'L');

$pdf ->SetXY(1.89,13.6 + $Lp);
$pdf->Cell(5.1,0.5,$row['bairro'],0,0,'L');

$pdf ->SetXY(7,13.6 + $Lp);
$pdf->Cell(7.5,0.5,$row['cidade'],0,0,'L');

$pdf ->SetXY(14.5,13.6 + $Lp);
$pdf->Cell(1,0.5,$row['uf'],0,0,'L');

$pdf ->SetXY(15.5,13.6 + $Lp);
$pdf->Cell(3.6,0.5,$row['cep'],0,0,'L');

//Segunda parte do Form. Cadastramento do Trabalhador no PIS
$L = $Lp + 14.64; //Posicionamento horizontal da segunda parte do Documento de Cadastro do Trabalhado

$pdf ->SetXY(8.3,1.8 + $L);
$pdf->Cell(3.1,0.5,$row_empresa['cnpj'],0,0,'L');

$pdf ->SetXY(8.2,2.4 + $L);
$pdf -> MultiCell(6.5,1.2,$row_empresa['endereco'],0,'L');

$pdf ->SetXY(1.89,5.8 + $L);
$pdf->Cell(4.1,0.5,$row_empresa['cnpj'],0,0,'L');

$pdf ->SetXY(6,5.8 + $L);
$pdf->Cell(13,0.5,$row_empresa['nome'],0,0,'L');

$pdf ->SetXY(1.89,6.6 + $L);
$pdf->Cell(17.3,0.5,$row_empresa['endereco'],0,0,'L');

$pdf ->SetXY(1.89,7.4 + $L);
$pdf->Cell(3.3,0.5,$row_empresa['tel'],0,0,'L');

$pdf ->SetXY(5.4,7.4 + $L);
$pdf->Cell(3.3,0.5,$row_empresa['fax'],0,0,'L');

$pdf ->SetXY(1.89,8.8 + $L);
$pdf->Cell(17.3,0.5,$row['nome'],0,0,'L');

$pdf ->SetXY(1.89,9.6 + $L);
$pdf->Cell(3.3,0.5,$row['data_nasci'],0,0,'L');

$pdf ->SetXY(5.2,9.6 + $L);
$pdf->Cell(1.2,0.5,$row['sexo'],0,0,'L');

$pdf ->SetXY(6.7,9.6 + $L);
$pdf->Cell(12.1,0.5,$row['mae'],0,0,'L');

$pdf ->SetXY(1.89,10.4 + $L);
$pdf->Cell(9.5,0.5,$row['naturalidade'],0,0,'L');

$pdf ->SetXY(11.5,10.4 + $L);
$pdf->Cell(1,0.5,$row['uf'],0,0,'L');

$pdf ->SetXY(12.5,10.4 + $L);
$pdf->Cell(2,0.5,'- ',0,0,'L');

$pdf ->SetXY(1.89,11.2 + $L);
$pdf->Cell(2.8,0.5,$row['campo1'],0,0,'L');

$pdf ->SetXY(4.7,11.2 + $L);
$pdf->Cell(2.3,0.5,$row['serie_ctps'],0,0,'L');

$pdf ->SetXY(7,11.2 + $L);
$pdf->Cell(1,0.5,$row['uf_ctps'],0,0,'L');

$pdf ->SetXY(8.3,11.2 + $L);
$pdf->Cell(5,0.5,$row['cpf'],0,0,'L');

$pdf ->SetXY(1.89,12 + $L);
$pdf->Cell(4.3,0.5,$row['rg'],0,0,'L');

$pdf ->SetXY(6.3,12 + $L);
$pdf->Cell(1.8,0.5,$row['orgao'],0,0,'L');

$pdf ->SetXY(8.3,12 + $L);
$pdf->Cell(5.2,0.5,$row['titulo'],0,0,'L');

$pdf ->SetXY(13.5,12 + $L);
$pdf->Cell(1,0.5,$row['secao'],0,0,'L');

$pdf ->SetXY(1.89,12.8 + $L);
$pdf->Cell(17.3,0.5,$row['endereco'],0,0,'L');

$pdf ->SetXY(1.89,13.6 + $L);
$pdf->Cell(5.1,0.5,$row['bairro'],0,0,'L');

$pdf ->SetXY(7,13.6 + $L);
$pdf->Cell(7.5,0.5,$row['cidade'],0,0,'L');

$pdf ->SetXY(14.5,13.6 + $L);
$pdf->Cell(1,0.5,$row['uf'],0,0,'L');

$pdf ->SetXY(15.5,13.6 + $L);
$pdf->MultiCell(3.6,0.5,$row['cep'],0,0,'L');

$pdf->Output("arquivos/arquivo.pdf");
echo "Gerando arquivo PDF.";
print "<script>location.href=\"arquivos/arquivo.pdf\"</script>";
$pdf->Close();

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

if(isset($coop)) {
	$pessoa = $_GET['bol'];
	$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '30' and id_clt = '$pessoa'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('30','$pessoa','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$pessoa' and tipo = '30'");
}
} elseif(isset($clt)) {
    $pessoa = $_GET['clt'];
	$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '5' and id_clt = '$pessoa'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('5','$pessoa','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$pessoa' and tipo = '5'");
}
}


//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

?>

</body>
</html>