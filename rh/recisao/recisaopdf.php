<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/regiao.php');
include('../../classes/clt.php');
include('../../classes/curso.php');

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
list($regiao,$idclt,$id) = explode('&',decrypt(str_replace('--','+',$_REQUEST['enc'])));
//

$RE_recisao = mysql_query("SELECT * FROM rh_recisao WHERE id_recisao = '$id'");
$Row = mysql_fetch_array($RE_recisao);

$RE_motivo = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$Row[motivo]'");
$Row_motivo = mysql_fetch_array($RE_motivo);

$nomearquivo = 'rescisao_'.$idclt.'_1.pdf';

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

list($Eendereco1,$Ebairro,$Ecidade,$Euf) = explode(' - ',$Eendereco);

# -- INFORMAÇÕES DE VALORES DA RECISÃO

$Sal_base 		 = number_format($Row['sal_base'], 2,",",".");
$saldo_salario 	 = number_format($Row['saldo_salario'], 2,",",".");
$dt_salario 	 = number_format($Row['dt_salario'], 2,",",".");
$terceiro_ss 	 = number_format($Row['terceiro_ss'], 2,",",".");
$ferias_vencidas = number_format($Row['ferias_vencidas'], 2,",",".");
$ferias_pr 		 = number_format($Row['ferias_pr'], 2,",",".");
$umterco_ferias  = number_format(($Row['umterco_fv'] + $Row['umterco_fp']), 2,",",".");

$insalubridade 	= number_format($Row['insalubridade'], 2,",",".");
$vale_refeicao 	= number_format($Row['vale_refeicao'], 2,",",".");
$debito_vale_refeicao = number_format($Row['debito_vale_refeicao'], 2,",",".");
$valor_faltas   = number_format($Row['valor_faltas'], 2,",",".");
$sal_familia 	= number_format($Row['sal_familia'], 2,",",".");
$ad_noturno 	= number_format($Row['ad_noturno'], 2,",",".");
$comissao 		= number_format($Row['comissao'], 2,",",".");
$gratificacao 	= number_format($Row['gratificacao'], 2,",",".");
$extra 			= number_format($Row['extra'], 2,",",".");
$outros_red		= number_format($Row['outros'], 2,",",".");

$movimentos		= explode(",",$Row['movimentos']);
$movimentos_va	= explode(",",$Row['valor_movimentos']);

$cont		= count($movimentos);
$outros_ren = 0;
$outros_des = 0;

for($i=0; $i<=$cont; $i++) {
	
	$mov = $movimentos[$i];
	$mov = explode('-',$mov);
	$qr_categoria = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '$mov[0]'");
	$categoria    = mysql_fetch_assoc($qr_categoria);
	
	if($categoria['categoria'] == 'CREDITO') {
		$outros_ren += $movimentos_va[$i];
	} elseif($categoria['categoria'] == 'DEBITO') {
		$outros_des += $movimentos_va[$i];
	}
	
}

if(!empty($Row['a477'])) {
	$atraso_rescisao = number_format($Row['a477'], 2,",",".");
} elseif(!empty($Row['a479'])) {
	$atraso_rescisao = number_format($Row['a479'], 2,",",".");
}

$inss_ss  = number_format($Row['inss_ss'], 2,",",".");
$inss_dt  = number_format($Row['inss_dt'], 2,",",".");
$ir_ss 	  = number_format($Row['ir_ss'], 2,",",".");
$previ_ss = number_format($Row['previdencia_ss'], 2,",",".");
$previ_dt = number_format($Row['previdencia_dt'], 2,",",".");

$ir_fe = number_format($Row['ir_fv'] + $Row['ir_fp'], 2,",",".");
$ir_dt = number_format($Row['ir_dt'], 2,",",".");

$to_rendimentos = number_format($Row['total_rendimento'], 2,",",".");
$to_descontos 	= number_format($Row['total_deducao'], 2,",",".");
$to_liquido 	= number_format($Row['total_liquido'], 2,",",".");

$arredondamento_positivo = number_format($Row['arredondamento_positivo'], 2,",",".");

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

# ---------------

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

# -- IMPRESSÃO DOS VALORES

$aviso_valor = $Row['aviso_valor']; // $aviso_valor = $Row['aviso_valor'] - $outros_ren;

if($aviso_valor < 0) {
	$aviso_valor = NULL;
}

$aviso_valor = number_format($aviso_valor, 2,',','.');
$devolucao   = number_format($Row['devolucao'], 2,",",".");

if($Row['fator'] == 'empregado' and $Row['aviso'] == 'indenizado') {		#PAGO PELO FUNCIONÁRIO
	
	$pdf ->SetXY(5.6,10.4);
	$pdf->Cell(0,0,"R$ 0,00",0,0,'L');				#Aviso prévio indenizado
	
	$pdf ->SetXY(14.7,15.6);
	$pdf->Cell(0,0,'Aviso Prévio Pago',0,0,'L'); #DEVOLUÇÃO
	
	$pdf ->SetXY(14.7,16);
	$pdf->Cell(0,0,'Pelo Funcionário',0,0,'L');
	
	$pdf ->SetXY(17.8,15.8);
	$pdf->Cell(0,0,"R$ ".$aviso_valor,0,0,'L');		#Aviso prévio indenizado
	
	$pdf ->SetXY(14.7,19);
	$pdf->Cell(0,0,"",0,0,'L');
	
	$pdf ->SetXY(3.1,10.4);
	$pdf->Cell(0,0,"0 Dias",0,0,'L');
	
} else {
	
	$pdf ->SetXY(5.6,10.4);
	$pdf->Cell(0,0,"R$ ".$aviso_valor,0,0,'L'); // Aviso Prévio Indenizado
	
	$pdf ->SetXY(3.1,10.4);
	$pdf->Cell(0,0,$Row['dias_aviso']." Dias",0,0,'L');
	
	$pdf ->SetXY(14.7,15.6);
	$pdf->Cell(0,0,'Devolução de',0,0,'L'); // Devolução
	
	$pdf ->SetXY(14.7,16);
	$pdf->Cell(0,0,'Crédito Indevido',0,0,'L');
	
	$pdf ->SetXY(17.8,15.8);
	$pdf->Cell(0,0,"R$ ".$devolucao,0,0,'L');
	
}

$pdf ->SetXY(5.6,11.1);
$pdf->Cell(0,0,"R$ ".$saldo_salario,0,0,'L');			#saldo de salarios

$pdf ->SetXY(5.6,12);
$pdf->Cell(0,0,"R$ ".$dt_salario,0,0,'L');				#13o salario

$pdf ->SetXY(5.6,12.9);
$pdf->Cell(0,0,"R$ ".$terceiro_ss,0,0,'L');				#13o salario indenizado sobre saldo de salario

$pdf ->SetXY(5.6,13.8);
$pdf->Cell(0,0,"R$ ".$ferias_vencidas,0,0,'L');			#FÉRIAS VENCIDAS

$pdf ->SetXY(5.6,14.8);
$pdf->Cell(0,0,"R$ ".$ferias_pr,0,0,'L');				#FÉRIAS PROPORCIONAIS

$pdf ->SetXY(5.6,15.8);
$pdf->Cell(0,0,"R$ ".$umterco_ferias,0,0,'L');			#1/3 DAS FÉRIAS PR + 1/3 DAS FÉRIAS VENCIDAS

$pdf ->SetXY(5.6,16.8);
$pdf->Cell(0,0,"R$ ".$sal_familia,0,0,'L');				#SALARIO FAMILIA

$pdf ->SetXY(5.6,17.7);
$pdf->Cell(0,0,"R$ ".$ad_noturno,0,0,'L');				#ADICIONAL NOTURNO

$pdf ->SetXY(5.6,18.6);
$pdf->Cell(0,0,"R$ ".$comissao,0,0,'L');				#COMISSAO

$pdf ->SetXY(5.6,19.5);
$pdf->Cell(0,0,"R$ ".$gratificacao,0,0,'L');			#GRATIFICAÇÃO

$pdf ->SetXY(5.6,20.3);
$pdf->Cell(0,0,"R$ ".$extra,0,0,'L');					#HORA EXTRA

$pdf ->SetXY(12,10.4);
$pdf->Cell(0,0,"R$ ".$insalubridade,0,0,'L');			#INSALUBRIDADE

/*$pdf ->SetXY(12,11.1);
$pdf->Cell(0,0,"R$ ".number_format($outros_ren,2,",","."),0,0,'L');				#OUTROS RENDIMENTOS

$pdf ->SetXY(9,11.1);
$pdf->Cell(0,0,"Outros Rendi.",0,0,'L');				#TEXTO OUTROS*/

$pdf ->SetXY(22,17.9);
$pdf->Cell(0,0,"R$ ".number_format($atraso_rescisao,2,",","."),0,0,'L');				#Indenização art 479

$pdf ->SetXY(26,17.9);
$pdf->Cell(0,0,"Indenização Art.",0,0,'L');				#Indenização art 479

$pdf ->SetXY(26,17.9);
$pdf->Cell(0,0,"479",0,0,'L');							#Indenização art 479

$pdf ->SetXY(17.8,10.4);
$pdf->Cell(0,0,"R$ ".$previ_ss,0,0,'L');				#PREVIDÊNCIA

$pdf ->SetXY(17.8,11.1);
$pdf->Cell(0,0,"R$ ".$previ_dt,0,0,'L');				#PREVIDÊNCIA 13º SALARIO

$pdf ->SetXY(17.8,12.1);
$pdf->Cell(0,0,"R$ ".$ir_fe,0,0,'L');					#IR SOBRE FÉRIAS

$pdf ->SetXY(17.8,13);
$pdf->Cell(0,0,"R$ ".$ir_ss,0,0,'L');					#IR SOBRE SALDO DE SALARIO

$pdf ->SetXY(17.8,13.9);
$pdf->Cell(0,0,"R$ ".$ir_dt,0,0,'L');					#IR SOBRE 13º

$pdf ->SetXY(14.7,14.8);
$pdf->Cell(0,0,"Outros Desc.",0,0,'L');					#OUTROS DESCONTOS

$pdf ->SetXY(17.8,14.8);
$pdf->Cell(0,0,"R$ ".number_format($outros_des,2,",","."),0,0,'L');					#OUTROS DESCONTOS

$pdf ->SetXY(9,12.7);
$pdf->Cell(0,0,'Arredondamento',0,0,'L');

$pdf ->SetXY(9,13.1);
$pdf->Cell(0,0,'Positivo',0,0,'L');

$pdf ->SetXY(12,12.9);
$pdf->Cell(0,0,"R$ ".$arredondamento_positivo,0,0,'L');	#ARREDONDAMENTO POSITIVO

$pdf ->SetXY(9,13.8);
$pdf->Cell(0,0,'Vale Refeição',0,0,'L');

$pdf ->SetXY(12,13.9);
$pdf->Cell(0,0,"R$ ".$vale_refeicao,0,0,'L');	        #VALE REFEIÇÃO

$pdf ->SetXY(9,14.8);
$pdf->Cell(0,0,'Atraso de Rescisão',0,0,'L');

$pdf ->SetXY(12,14.9);
$pdf->Cell(0,0,"R$ ".$atraso_rescisao,0,0,'L');	        #ATRASO DE RESCISAO

$pdf ->SetXY(12,13.9);
$pdf->Cell(0,0,"R$ ".$vale_refeicao,0,0,'L');	        #VALE REFEIÇÃO

$pdf ->SetXY(14.7,16.8);
$pdf->Cell(0,0,'Déb. Vale Refeição',0,0,'L');

$pdf ->SetXY(18,16.9);
$pdf->Cell(0,0,"R$ ".$debito_vale_refeicao,0,0,'L');    #DÉBITO VALE REFEIÇÃO

$pdf ->SetXY(14.7,17.8);
$pdf->Cell(0,0,'Valor Faltas',0,0,'L');

$pdf ->SetXY(18,17.9);
$pdf->Cell(0,0,"R$ ".$valor_faltas,0,0,'L');    #DÉBITO VALE REFEIÇÃO

# -- TOTAIS

$pdf ->SetXY(12.1,20.3);
$pdf->Cell(0,0,"R$ ".$to_rendimentos,0,0,'L');	#TOTAL BRUTO

$pdf ->SetXY(17.8,19.3);
$pdf->Cell(0,0,"R$ ".$to_descontos,0,0,'L');	#TOTAL DEDUÇÕES

$pdf ->SetXY(17.8,20.3);
$pdf->Cell(0,0,"R$ ".$to_liquido,0,0,'L');		#LIQUIDO A RECEBER

# -- TOTAIS

$pdf ->SetXY(3.1,11.4);
$pdf->Cell(0,0,$Row['dias_saldo']." Dias",0,0,'L');

$pdf ->SetXY(3.05,12.25);
$pdf->Cell(0,0,sprintf('%02d',$Row['avos_dt']),0,0,'L');

$pdf ->SetXY(3.05,15.05);
$pdf->Cell(0,0,sprintf('%02d',$Row['avos_fp']),0,0,'L');

$pdf ->SetXY(2.880,13.130);
$pdf->Cell(0,0,"01",0,0,'L');

$pdf ->SetXY(2.930,14.030);
$pdf->Cell(0,0,"30 Dias",0,0,'L');




# --------------- FINALIZANDO, FECHANDO E SALVANDO O ARQUIVO
$pdf->Output("../arquivos/recisaopdf/$nomearquivo");
echo "Gerando arquivo PDF.";
print "<script>location.href=\"../arquivos/recisaopdf/$nomearquivo\"</script>";
$pdf->Close();
?>
</body>
</html>