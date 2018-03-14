<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
exit;
}

include "../conn.php";
include "../funcoes.php";
include "../classes/regiao.php";
include "../classes/cooperativa.php";
include "../classes/cooperado.php";
include "../classes/curso.php";




require("../rh/fpdf/fpdf.php");
define('FPDF_FONTPATH','../rh/fpdf/font/');

$pdf = new FPDF();



//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
$id_coop = $_GET['coop'];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA



$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');


//SELECIONANDO OS CLTS JA CADASTRADOS NA TAB FOLHA_PROC QUE ESTEJAM COM STATUS 2 = SELECIONADO ANTERIORMENTE
$REFolha_pro = mysql_query("SELECT * FROM folha_cooperado where id_autonomo = '$id_coop'  AND status = '3' ORDER BY id_folha ASC");




while($RowP = mysql_fetch_array($REFolha_pro)):


//REUNINDO DADOS DO PARTICIPANTE
$idParticipante = $RowP['id_autonomo'];
$participante = new cooperado();
$participante -> MostraCoop($idParticipante);
$id_cooperativa = $participante -> id_cooperativa;

$nomeP = $participante -> nome;
$nomeP = str_split($nomeP, 30);
$campo3 = $participante -> campo3;
$campo3 = sprintf("%03s",$campo3);
$id_curso = $participante -> id_curso;
//DADOS DA ATIVIDADE DO COOPERADO
$atividade = new tabcurso();
$atividade -> MostraCurso($id_curso);
$nomeAtividade = $atividade -> nome;
$nomeAtividadeT = str_split($nomeAtividade, 30);
$HoraAtividade = $atividade -> hora_mes;
//REUNINDO DADOS DA COOPERATIVA DO PARTICPANTE
$coop = new cooperativa();
$coop -> MostraCoop($id_cooperativa);

$nome = $coop -> nome;
$fantasia = $coop -> fantasia;
$cnpj = $coop -> cnpj;

$pdf->addPage( 'P','A4');

for($i = 0 ;$i<2; $i++){
		
		if($i == 1) { $segunda_via = 135;}
		
		$pdf->Image('recibocoop.gif', -0.6,0.2+ $segunda_via,210,130,'gif');
		
		$pdf->SetFont('Arial','',8);
		
		$pdf ->SetXY(14,12 + $segunda_via);
		$pdf->Cell(3, 1, $fantasia, 0,1,'L');
		
		$pdf ->SetXY(14,16 + $segunda_via);
		$pdf->Cell(3, 0.8, 'CNPJ: '.$cnpj ,0,1,'L');
		
		$pdf ->SetXY(100,16 + $segunda_via);
		$pdf->Cell(3, 0.8, 'Periodo: '.$meses[(int)$RowP['mes']].'/'.$RowP['ano'] ,0,1,'L');
		
		$pdf ->SetXY(14,25 + $segunda_via);
		$pdf->Cell(3, 0.8, $campo3,0,1,'L');
		
		$pdf ->SetXY(28,25 + $segunda_via);
		$pdf->Cell(3, 0.8, $nomeP[0],0,1,'L');
		
		$pdf ->SetXY(90,25  + $segunda_via);
		$pdf->Cell(3, 0.8, $nomeAtividadeT[0],0,1,'L');
		
		//DESCRIÇÃO DOS VENCIMENTOS E DESCONTOS
		
		$SalBase   = number_format($RowP['salario'],2,",",".");
		//$Benefic = number_format($RowP['parte2'],2,",",".");
		$Adicional = number_format($RowP['adicional'],2,",",".");
		$Desconto  = number_format($RowP['desconto'],2,",",".");
		$Inss  = number_format($RowP['inss'],2,",",".");
		$Irrf  = number_format($RowP['irrf'],2,",",".");
		$Quota = number_format($RowP['quota'],2,",",".");
		$Ajuda = number_format($RowP['ajuda_custo'],2,",",".");
		
		$TO_Proventos  = $RowP['adicional'] + $RowP['salario'] + $RowP['ajuda_custo'];
		$TO_ProventosF = number_format($TO_Proventos,2,",",".");
		
		$TO_Descontos  = $RowP['desconto'] + $RowP['inss'] + $RowP['irrf'] + $RowP['quota'];
		$TO_DescontosF = number_format($TO_Descontos,2,",",".");
		
		$SalLiq   = number_format($RowP['salario_liq'],2,",",".");
		
		$BaseInss = number_format($RowP['base_imposto'],2,",",".");
		$BaseIrrf = number_format($RowP['base_irrf'],2,",",".");
		
		
		
		$pdf ->SetXY(15,39  + $segunda_via);
		$pdf->Cell(3, 0.8, '001',0,1,'L');
		$pdf ->SetXY(26,39  + $segunda_via);
		$pdf->Cell(3, 0.8, 'Valor base',0,1,'L');
		
		$pdf ->SetXY(100,39  + $segunda_via);
		$pdf->Cell(3, 0.8, '-',0,1,'C');
		$pdf ->SetXY(140,39  + $segunda_via);
		$pdf->Cell(1, 0.8, $SalBase,0,1,'R');
		
		
		$linha_espaco = 3.5;
		
		
		if($Adicional != 0){
				
			$pdf ->SetXY(15,39  + $segunda_via +$linha_espaco);
			$pdf->Cell(3, 0.8, '0002',0,1,'L');
			
			$pdf ->SetXY(26, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, 'Rendimento',0,1,'L');
			
			$pdf ->SetXY(100,39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, '-',0,1,'C');
			
			$pdf ->SetXY(140,39  + $segunda_via + $linha_espaco);
			$pdf->Cell(1, 0.8, $Adicional,0,1,'R');
		
			$linha_espaco += 3.5;
		 }
		
		
		if($Desconto != 0){
				
			$pdf ->SetXY(15,39  + $segunda_via +$linha_espaco);
			$pdf->Cell(3, 0.8, '0003',0,1,'L');
			
			$pdf ->SetXY(26, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, 'Descontos',0,1,'L');
			
			$pdf ->SetXY(100,39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, '-',0,1,'L');
			
			$pdf ->SetXY(173,39  + $segunda_via + $linha_espaco);
			$pdf->Cell(1, 0.8, $Desconto,0,1,'R');
		
				$linha_espaco += 3.5;
		 }
		
		if($Inss != 0){
				
			$pdf ->SetXY(15,39  + $segunda_via +$linha_espaco);
			$pdf->Cell(3, 0.8, '5024',0,1,'L');
			
			$pdf ->SetXY(26, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, 'INSS',0,1,'L');
			
			$pdf ->SetXY(100,39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, $RowP['t_inss'],0,1,'L');
			
			$pdf ->SetXY(173, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(1, 0.8, $Inss,0,1,'R');
		
			$linha_espaco += 3.5;
		 }
		
			
		if($Irrf != 0){
				
			$pdf ->SetXY(15,39  + $segunda_via +$linha_espaco);
			$pdf->Cell(3, 0.8, '5021',0,1,'L');
			
			$pdf ->SetXY(26, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, 'Imposto de Renda',0,1,'L');
			
			$pdf ->SetXY(100,39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, $RowP['t_irrf'],0,1,'L');
			
			$pdf ->SetXY(173, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(1, 0.8, $Irrf,0,1,'R');
		
			$linha_espaco += 3.5;
		 }
		
		if($Quota != 0){
				
			$pdf ->SetXY(15,39  + $segunda_via +$linha_espaco);
			$pdf->Cell(3, 0.8, '0055',0,1,'L');
			
			$pdf ->SetXY(26, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, 'Quota',0,1,'L');
			
			$pdf ->SetXY(100,39  + $segunda_via+ $linha_espaco);
			$pdf->Cell(3, 0.8, $RowP['p_quota'],0,1,'L');
			
			$pdf ->SetXY(173, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(1, 0.8, $Quota,0,1,'R');
		
			$linha_espaco += 3.5;
		 }
		
		if($Ajuda != 0){
				
			$pdf ->SetXY(15,39  + $segunda_via +$linha_espaco);
			$pdf->Cell(3, 0.8, '5011',0,1,'L');
			
			$pdf ->SetXY(26, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, 'Ajuda de Custo',0,1,'L');
			
			$pdf ->SetXY(100,39  + $segunda_via + $linha_espaco);
			$pdf->Cell(3, 0.8, '-',0,1,'L');
			
			$pdf ->SetXY(140, 39  + $segunda_via + $linha_espaco);
			$pdf->Cell(1, 0.8, $Ajuda,0,1,'R');
		
			$linha_espaco += 3.5;
		 }
		
		
		$pdf ->SetXY(15, 105 + $segunda_via);
		$pdf->Cell(1, 0.8, 'Horas mês: '. $RowP['h_mes'].'h',0,1,'L');
		
		$pdf ->SetXY(15, 109  + $segunda_via);
		$pdf->Cell(1, 0.8, 'Horas trabalhadas: '. $RowP['h_trab'].'h',0,1,'L');
		
		$pdf ->SetXY(140, 105  + $segunda_via);
		$pdf->Cell(1, 0.8, $TO_ProventosF,0,1,'R');
		
		$pdf ->SetXY(173, 105  + $segunda_via);
		$pdf->Cell(1, 0.8, $TO_DescontosF,0,1,'R');
			
		
		$pdf ->SetXY(173, 112  + $segunda_via);
		$pdf->Cell(1, 0.8, $SalLiq,0,1,'R');		
				
		$pdf ->SetXY(30,121  + $segunda_via);
		$pdf->Cell(1, 0.8, $SalLiq,0,1,'R');				
		
		$pdf ->SetXY(70, 121  + $segunda_via);
		$pdf->Cell(1, 0.8, $RowP['t_inss'],0,1,'R');		
		
		$pdf ->SetXY(116, 121  + $segunda_via);
		$pdf->Cell(1, 0.8, $BaseInss,0,1,'R');
				
		$pdf ->SetXY(148, 121  + $segunda_via);
		$pdf->Cell(1, 0.8, $BaseIrrf,0,1,'R');
		
		$pdf ->SetXY(168, 121  + $segunda_via);
		$pdf->Cell(1, 0.8, $RowP['t_irrf'],0,1,'R');
	

}


unset($segunda_via ,$linha_espaco);

endwhile;


$pdf->Output("recibos.pdf",'I');	
?>