<?php
include('../conn.php');
include('../classes/funcionario.php');

$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;
exit();
/*

if(!isset($_GET['pronto'])) { 

?>
<html>
<head>
<title>Gerando Rendimento</title>
<meta http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
<link href="../relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	$('#data').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	$('input[name*=radio_data]').change(function(){
		var valor = $(this).val();
		if(valor == 2) {
			$('#data').show();
		} else {
			$('#data').hide();
		}
	});
});
</script>
</head>
<body>


<div id="corpo">
 <div id="topo">
      <?php include('../relatorios/include/topo.php'); ?>
 </div>
 <div id="conteudo">
    <h1 style="margin:70px;"><span>RELATÓRIOS</span> INFORME DE RENDIMENTO</h1>
    <span style="color:#C30"><?=$empregado['nome']?></span>  
    <form name="rendimento" method="get" action="<?=$_SERVER['PHP_SELF']?>">           
       
        <input type="hidden" name="pro" value="<?=$_GET['pro']?>">
        <input type="hidden" name="id_reg" value="<?=$_GET['reg']?>">
        <input type="hidden" name="pronto" value="1">
        <input type="submit" name="submit" value="Gerar Rendimento" class="botao">
    </form>
 </div>
 <div id="rodape"></div>
</div>
	
   </body></html>
<?php } else {
	
*/	
require('../rh/fpdf/fpdf.php');
define('FPDF_FONTPATH','../rh/fpdf/font/');
$pdf = new FPDF("P","cm","A4");
$pdf->SetAutoPageBreak(true,0.0);
$pdf->Open();

$id_user       = $_COOKIE['logado'];
$result_user   = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user      = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

$regiao   = (isset($_GET['reg']))?$_GET['reg']:$_POST['regiao'];
$projeto  = (isset($_GET['pro']))?$_GET['pro']:$_POST['projeto'];
$ano_base = date('Y') - 1;


$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$Master' AND status = '1'");
$empresa = mysql_fetch_assoc($qr_empresa);
$dados_empresa = array($empresa['razao'],$empresa['cnpj'],$empresa['responsavel']);

// Consultando o Participante
	
$qr_trabalhador = mysql_query("SELECT * FROM rh_clt WHERE id_projeto = '$projeto'");
while($row_trabalhador = mysql_fetch_assoc($qr_trabalhador)):


  


// Consultando a Folha
$qr_folha = mysql_query("SELECT rh_folha.terceiro,rh_folha_proc.inss, rh_folha_proc.a5021, rh_folha_proc.fgts, rh_folha_proc.a6004, rh_folha_proc.a7009, rh_folha_proc.a5011,rh_folha_proc.salliquido
						FROM rh_folha 
						 INNER JOIN rh_folha_proc 
						 ON rh_folha.id_folha = rh_folha_proc.id_folha
						 WHERE rh_folha.ano = '$ano_base'  AND rh_folha.projeto = '$projeto' 
						 AND rh_folha.regiao = '$regiao' AND rh_folha.status = '3'
						 AND rh_folha_proc.id_clt = '$row_trabalhador[id_clt]' AND rh_folha_proc.status IN(3,4)" ) or die(mysql_error()) ;

while($folha = mysql_fetch_assoc($qr_folha)) {

	
	
		/////Férias
		$qr_ferias  = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_trabalhador[id_clt]' AND ano = '$ano_base'  AND status = '1'");
		while($row_ferias = mysql_fetch_assoc($qr_ferias)):
		
	
		
		if(!empty($row_ferias['total_liquido'])) {
			$salario_ferias = $row_ferias['total_liquido'];
		} else {
			$salario_ferias = NULL;
		}		
	endwhile;
			

	
	// Tipo de Contratação CLT		
		if($folha['terceiro'] == "1") {
			$salario13 += $folha['salliquido'];
		} else {
			$salario += $folha['salliquido'];
		}
		

		$inss += $folha['inss'];
		$ir += $folha['a5021'];
		$fgts += $folha['fgts'];
		$pensao_alimenticia += $folha['a6004'] + $folha['a7009'];
		$ajuda_custo += $folha['a5011'];
	
	
}
// Consultando a Rescisão (CLT)

	$qr_rescisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$id' AND year(data_demi) = '$ano_base' AND motivo IN (60,61,62,63,80,81,100)");
	$row_rescisao   = mysql_fetch_assoc($qr_rescisao);
	$total_rescisao = mysql_num_rows($qr_rescisao);
	if(!empty($total_rescisao)) {
		//$rescisao = ($row_rescisao['fgts8'] + $fgts) * 0.40;
		$rescisao = $row_rescisao['total_liquido'];
		$outros_rendimentos = $row_rescisao['saldo_salario'] - $row_rescisao['inss_ss'];
	}

if(!empty($ir)) {
	
$pdf->SetFont('Arial','B',9);
$pdf->Cell(5, 30, " ");
$pdf->Image('imagens/fundo_rendimento.gif', 0.5,0,20,28,'gif');

$pdf ->SetXY(15.05,2.73);
$pdf->Cell(0,0,$ano_base,0,0,'L');

$pdf->SetFont('Arial','B',10);
$pdf ->SetXY(1.6,4.45);
$pdf->Cell(0,0,substr($dados_empresa[0],0,60),0,0,'L');

$pdf ->SetXY(14.8,4.45);
$pdf->Cell(0,0,$dados_empresa[1],0,0,'L');

$pdf ->SetXY(6.05,6.45);
$pdf->Cell(0,0,$row_trabalhador['nome'],0,0,'L');

$pdf ->SetXY(1.6,6.45);
$pdf->Cell(0,0,$row_trabalhador['cpf'],0,0,'L');

$pdf ->SetXY(1.6,7.45);
if($row_trabalhador['tipo_contratacao'] == 2) {
	$pdf->Cell(0,0,"Rendimentos do trabalho assalariado",0,0,'L');
} elseif($row_trabalhador['tipo_contratacao'] != 2) {
	$pdf->Cell(0,0,"Rendimento de bolsa-auxílio",0,0,'L');
}

$pdf ->SetXY(14.8,9.06);
$pdf->Cell(0,0,"R$ ".number_format($salario+$salario_ferias, 2, ",", "."),0,0,'L');

$pdf ->SetXY(14.8,10);
$pdf->Cell(0,0,"R$ ".number_format($inss, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,10.92);
$pdf->Cell(0,0,"R$ ".number_format('', 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,11.85);
$pdf->Cell(0,0,"R$ ".number_format($pensao_alimenticia, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,12.8);
$pdf->Cell(0,0,"R$ ".number_format($ir, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,14.75);
$pdf->Cell(0,0,"R$ ".number_format('', 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,15.67);
$pdf->Cell(0,0,"R$ ".number_format($ajuda_custo, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,16.62);
$pdf->Cell(0,0,"R$ ".number_format('', 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,17.52);
$pdf->Cell(0,0,"R$ ".number_format('', 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,18.45);
$pdf->Cell(0,0,"R$ ".number_format('', 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,19.4);
$pdf->Cell(0,0,"R$ ".number_format($rescisao, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,20.3);
$pdf->Cell(0,0,"R$ ".number_format($outros_rendimentos, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,22.1);
$pdf->Cell(0,0,"R$ ".number_format($salario13, 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(14.8,23);
$pdf->Cell(0,0,"R$ ".number_format('', 2, ",", ".")."",0,0,'L');

$pdf ->SetXY(1.55,26.45);
$pdf->Cell(0,0,$dados_empresa[2],0,0,'L');

if(empty($_GET['data'])) {
	$data = date('d/m/Y');
} else {
	$data = $_GET['data'];
}

$pdf ->SetXY(9.1,26.45);
$pdf->Cell(0,0,$data,0,0,'L');



}


unset($inss, $ir, $fgts, $pensao_alimenticia, $ajuda_custo,$salario,$salario13, $outros_rendimentos, $rescisao, $salario_ferias );
endwhile;

//}

$pdf->Output("rendimento.pdf","I");
/*echo "<b>Gerando arquivo PDF...</b>";
print "<script>location.href=\"rendimento2.pdf\"</script>";
*/

?>