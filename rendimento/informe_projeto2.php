<?php
include('../conn.php');
include('../classes/funcionario.php');

$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;
exit();

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

$regiao   = $_GET['id_reg'];
$projeto  = $_GET['pro'];
$ano_base = date('Y') - 1;

// Consultando o Participante
$qr_empregado = mysql_query("SELECT * FROM autonomo WHERE id_projeto = '$projeto' AND id_regiao = '$regiao' AND tipo_contratacao = 1 ");
while($empregado = mysql_fetch_assoc($qr_empregado)):

		
		
		
		// Consultando a Empresa	
			$qr_empresa = mysql_query("SELECT nome,cnpj,diretor FROM cooperativas WHERE id_coop = '$empregado[id_cooperativa]' AND status_reg = '1'");
			$empresa = mysql_fetch_assoc($qr_empresa);
			$dados_empresa = array($empresa['nome'],$empresa['cnpj'],$empresa['diretor']);
		 
		
		// Consultando a Folha
		$qr_folha = mysql_query("SELECT id_folha FROM folhas WHERE year(data_inicio) = '$ano_base' AND contratacao = '3' AND projeto = '$projeto' AND regiao = '$regiao' AND status = '3'");
		while($folha = mysql_fetch_assoc($qr_folha)) {
		
			// Consultando a Folha Individual
			$qr_folha_individual = mysql_query("SELECT salario_liq, inss,irrf  FROM folha_cooperado WHERE id_folha = '$folha[id_folha]' AND id_autonomo = '$empregado[id_autonomo]' AND status IN (3,4)");
			$folha_individual = mysql_fetch_assoc($qr_folha_individual);
			
			$salario += $folha_individual['salario_liq'];
			$inss += $folha_individual['inss'];
			$ir += $folha_individual['irrf'];
				
			
		
		}
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
$pdf->Cell(0,0,$empregado['nome'],0,0,'L');

$pdf ->SetXY(1.6,6.45);
$pdf->Cell(0,0,$empregado['cpf'],0,0,'L');

$pdf ->SetXY(1.6,7.45);
if($empregado['tipo_contratacao'] == 2) {
	$pdf->Cell(0,0,"Rendimentos do trabalho assalariado",0,0,'L');
} elseif($empregado['tipo_contratacao'] != 2) {
	$pdf->Cell(0,0,"Rendimento de bolsa-auxílio",0,0,'L');
}

$pdf ->SetXY(14.8,9.06);
$pdf->Cell(0,0,"R$ ".number_format($salario, 2, ",", ".")."",0,0,'L');

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


endwhile;

}

$pdf->Output("rendimento.pdf","I");
/*echo "<b>Gerando arquivo PDF...</b>";
print "<script>location.href=\"rendimento2.pdf\"</script>";
*/

?>