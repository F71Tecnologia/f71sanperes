<?php
include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";
include "../../classes/clt.php";
include "../../classes/curso.php";

// RECEBENDO VARIAVEIS
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc);
$decript = explode("&",$link);
$regiao 	= $decript[0];
$clt 		= $decript[1];
$id_folha 	= $decript[2];
//

$data = date('d/m/Y');
$ClassDATA = new regiao();
$ClassDATA -> RegiaoLogado();
$Clt = new clt();
$Curso = new tabcurso();

require("../fpdf/fpdf.php");
define('FPDF_FONTPATH','../fpdf/font/');
$pdf = new FPDF("P","cm","A4");

if($clt == "todos") {
	
	$ini = $_REQUEST['ini'];
	$fim = $_REQUEST['fim'];
	$REfolhaproc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$id_folha' AND status = '3' ORDER BY nome LIMIT $ini,50");
	$NumRegistros = mysql_num_rows($REfolhaproc);
	$nomearquivo = "contracheques_clt.pdf";

} else {
	
	$REfolhaproc = mysql_query("SELECT * FROM rh_folha_proc WHERE id_clt = '$clt' AND id_folha = '$id_folha'");
	$nomearquivo = "contracheque_unico_clt.pdf";

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerando ContraCheque</title>
</head>
<body>
<?php
// SELECIONANDO TODOS OS TIPOS DE MOVIMENTOS DA TABELA
$Recodigos = mysql_query("SELECT distinct(cod),descicao,categoria FROM rh_movimentos WHERE cod != '0001' AND cod != '5024'  AND cod != '9991'  AND cod != '5044'  AND cod != '5035'  ORDER BY cod ASC");

while($row_codigos = mysql_fetch_array($Recodigos)){
	$ARcodigos[] 	= "a".$row_codigos['0'];
	$ARcodigosVER[]	= $row_codigos['0'];
	$ARnomes[] 		= $row_codigos['descicao'];
	$ARcategorias[]	= $row_codigos['categoria'];
}
//


// INICIANDO O LOOP
while($RowFolhaPro = mysql_fetch_array($REfolhaproc)) {
//


$REFolha = mysql_query("SELECT * FROM rh_folha WHERE id_folha = '".$RowFolhaPro['id_folha']."'");
$RowFolha = mysql_fetch_array($REFolha);


$MES = $ClassDATA -> MostraMes($RowFolhaPro['mes']);
$ANO = $RowFolha['ano'];


// Informações do CLT
$Clt -> MostraClt($RowFolhaPro['id_clt']);
$nome 		    = $Clt -> nome;
$campo1 	    = $Clt -> campo1;
$locacao 	    = $Clt -> locacao;
$id_curso 	    = $Clt -> id_curso;
$banco	 	    = $Clt -> banco;
$agencia 	    = $Clt -> agencia;
$conta	 	    = $Clt -> conta;
$salario	    = $Clt -> salario;
$data_entrada	= $Clt -> data_entrada;
$rh_vinculo		= $Clt -> rh_vinculo;
$campo3			= $Clt -> campo3;
$admissao = implode("/",array_reverse(explode("-", $data_entrada)));
//

// SELECIONANDO A EMPRESA AO QUAL O FUNCIONÁRIO ENCONTRA-SE VINCULADO
$REVinc = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND status = '1'");
$RowVinc = mysql_fetch_array($REVinc);
//


// Informações do Curso
$Curso -> MostraCurso($id_curso);
$cargo = $Curso -> cargo;
//


# -----------------------------------------------------------------------------------
#                    INICIANDO O PROCESSO DE CRIAÇÃO DO PDF                         #
# -----------------------------------------------------------------------------------


$pdf->SetAutoPageBreak(true,0.0); // Reduz a tolerância da margem inferior
$pdf->Open();
$pdf->SetFont('Arial','B',8);
$pdf->Cell(5, 30, " ");
$pdf->Image('../images/recibo_clt.gif', 0,0.2,21,28.500,'gif');


if(empty($RowFolhaPro['t_imprenda'])) { 
	$TaxaINSS = "0";
} else {
	$TaxaINSS = $RowFolhaPro['t_imprenda'];
}


$nPag = $pdf->PageNo();
$pdf ->SetXY(1,0.4);
$pdf->Cell(0,0,"Página $nPag",0,0,'R');


$a = 0;
$c = 0;
$i = 0;

for($a=0; $a<=1; $a++) {

	if($a == 1) { 
		$c = 14.1;
	}

$pdf->SetFont('Arial','B',8);
$pdf ->SetTextColor(0,0,0) ;

$pdf ->SetXY(1,1.6 + $c);
$pdf->MultiCell(7.5,.3,$RowVinc['razao'],0,'L',0);

$pdf ->SetXY(1,2.4 + $c);
$pdf->Cell(0,0,"CNPJ: ".$RowVinc['cnpj'],0,0,'L');

$pdf ->SetXY(13,2.4 + $c);
$pdf->Cell(0,0,"$MES / $ANO",0,0,'L');


// IMPRIMINDO DADOS PESSOAIS DOS CLTS
$pdf ->SetXY(1.3,3.3 + $c);
$pdf->Cell(0,0,$campo3,0,0,'L');

$pdf ->SetXY(2.5,3.3 + $c);
$pdf->Cell(0,0,$nome,0,0,'L');

$pdf ->SetXY(10.4,3.3 + $c);
$pdf->Cell(0,0,$cargo,0,0,'L');
//


// IMPRIMINDO DADOS DE PAGAMENTO TAIS COMO, INSS, FGTS, IRRF
// SERÁ IMPRESSO LINHA POR LINHA... TAXA POR TAXA.. SÓ VERIFICANDO SE EXISTE ESSA TAXA PARA ESTE CLT

$pdf ->SetXY(0.8,4.7 + $c);
$pdf->Cell(0,0,"0001",0,0,'L');

$pdf ->SetXY(1.7,4.7 + $c);
$pdf->Cell(0,0,"Sálario Base",0,0,'L');

// ACRESCENTAMOS O CAMPO SALLIMPO NA BASE DE DADOS, MAS AS FOLHAS QUE JÁ ESTAVAM FECHADAS ESTÃO COM ESSE CAMPO ZERADO
//if($RowFolhaPro['sallimpo'] == '0') {
	//$salb = $RowFolhaPro['salbase'] - $RowFolhaPro['a6006'];
//} else {
    //$salb = $RowFolhaPro['sallimpo_real'];
//}

// Se a Folha é nova...
if(date('Y-m-d') > date('2010-06-09')) {
	$salb = $RowFolhaPro['sallimpo_real'];
} else {
	$salb = $RowFolhaPro['salbase'] - $RowFolhaPro['a6006'];
}

$pdf ->SetXY(11.2,4.7 + $c);
$pdf->Cell(3.5,0,number_format($salb,2,",","."),0,0,'R');

// VÁRIOS DESCONTOS E RENDIMENTOS
$linha = 0;
$qnt   = count($ARcodigos);
$cont  = 0;

for($i=0; $i<$qnt; $i++) {
	
	$RE = mysql_query("SELECT ".$ARcodigos[$i]." AS campo FROM rh_folha_proc WHERE id_folha_proc = '".$RowFolhaPro['0']."'");
	$Row = mysql_fetch_array($RE);
	$Valor_F = number_format($Row['campo'],2,",",".");
		
		
	if($Row['campo'] != '0') {
			
		// ESPAÇO ENTRE AS LINHAS
		if($cont != 0) { 
			$linha = .4 * $cont; 
			
			
		}
			
		$linhaAgo = $linha + 5.1;
		//
			
		// SE CÓDIGO FOR 5049 = DDIR E VALOR DE IRRF FOR ZERO, NAO VAI IMPRIMIR (NUNCA MOSTRA DDIR, JR 14/01/2010)
		if($ARcodigosVER[$i] != '5049' ) {
			
			
			
			
			if($ARcodigosVER[$i] == '5022') {
				$qr_salario_maternidade  = mysql_query("SELECT a6005 AS valor FROM rh_folha_proc WHERE id_folha_proc = '".$RowFolhaPro[0]."'");
				$row_salario_maternidade = mysql_fetch_array($qr_salario_maternidade);
				if($row_salario_maternidade['valor'] != '0.00') {
					$salario_maternidade = ' (INCLUSO SALÁRIO MATERNIDADE)';
				}
			} else {
				unset($salario_maternidade);
			}

			$pdf ->SetXY(0.8,$linhaAgo + $c);
			$pdf->Cell(0,0,$ARcodigosVER[$i],0,0,'L');
				
			$pdf ->SetXY(1.7,$linhaAgo + $c);
			$pdf->Cell(0,0,$ARnomes[$i].$salario_maternidade,0,0,'L');
			
			// RENDIMENTO
			if($ARcategorias[$i] == 'CREDITO') {	
				$pdf ->SetXY(11.2,$linhaAgo + $c);
				$pdf->Cell(3.5,0,$Valor_F,0,0,'R');
				$TO_rendimentos = $TO_rendimentos + $Row['0'];
			// DESCONTO
			} else {
				$pdf ->SetXY(14.7,$linhaAgo + $c);
				$pdf->Cell(3.5,0,$Valor_F,0,0,'R');
				$TO_descontos = $TO_descontos + $Row['0'];	
			}
			
			if($ARcodigosVER[$i] == '5037') {
				
				$linhaAgo += 0.4;
				$cont++;
				
				$qr_valor_pago_ferias = mysql_query("SELECT valor_pago_ferias FROM rh_folha_proc WHERE id_folha_proc = '".$RowFolhaPro['0']."'");
				$valor_pago_ferias    = mysql_result($qr_valor_pago_ferias,0);
				
				$pdf ->SetXY(0.8,$linhaAgo);
				$pdf->Cell(0,0,'5037',0,0,'L');
					
				$pdf ->SetXY(1.7,$linhaAgo);	
				$pdf->Cell(0,0,'VALOR PAGO NAS FÉRIAS',0,0,'L');
				
				$pdf ->SetXY(14.7,$linhaAgo);
				$pdf->Cell(3.5,0,number_format($valor_pago_ferias,2,',','.'),0,0,'R');
				
				$TO_descontos = $TO_descontos + $Row['0'];
				
			}

			$cont ++;
				
		} // VERIFICAÇÀO DO DDIR E IRRF
		
	} // VERIFICANDO SE ESTÁ ZERADO

} // VARIOS DESCONTOS E RENDIMENTOS

// FORMATANDO OS TOTAIS

// ACRESCENTAMOS O CAMPO SALLIMPO NA BASE DE DADOS, MAS AS FOLHAS QUE JÁ ESTAVAM FECHADAS ESTÃO COM ESSE CAMPO ZERADO

if($RowFolhaPro['sallimpo'] == '0') {
	$TO_rendimentos = $TO_rendimentos + $RowFolhaPro['salbase'] - $RowFolhaPro['a6006']; // DESCONTANDO A INSALUBRIDADE
} else {
	$TO_rendimentos = $TO_rendimentos + $RowFolhaPro['sallimpo_real'];
}

$TO_rendimentosF = number_format($TO_rendimentos,2,",",".");
$TO_descontosF = number_format($TO_descontos,2,",",".");

$pdf ->SetXY(11.2,11.6 + $c);
$pdf->Cell(3.5,0,$TO_rendimentosF,0,0,'R');

$pdf ->SetXY(14.7,11.6 + $c);
$pdf->Cell(3.5,0,$TO_descontosF,0,0,'R');


// LIMPANDO VARIAVEIS DE TOTAIS
unset($TO_rendimentos);
unset($TO_descontos);
unset($TO_rendimentosF);
unset($TO_descontosF);


// TOTAL FONT 10 COR VERMELHA
$pdf ->SetFontSize(10);
$pdf ->SetTextColor(255,0,0) ;

$pdf ->SetXY(14.7,12.3 + $c);
$pdf->Cell(3.5,0,number_format($RowFolhaPro['salliquido'],2,",","."),0,0,'R');
//


$pdf ->SetFontSize(8);
$pdf ->SetTextColor(0,0,0) ;

$pdf ->SetXY(1.5,13.2 + $c);
$pdf->Cell(2.2,0,$RowFolhaPro['salbase'],0,0,'R');

$pdf ->SetXY(2.5,13.2 + $c);
$pdf->Cell(3.4,0,$RowFolhaPro['inss'],0,0,'R');

$pdf ->SetXY(6.3,13.2 + $c);
$pdf->Cell(3.3,0,$RowFolhaPro['salbase'],0,0,'R');

$pdf ->SetXY(10,13.2 + $c);
$pdf->Cell(2,0,$RowFolhaPro['fgts'],0,0,'R');

$pdf ->SetXY(13.5,13.2 + $c);
$pdf->Cell(2,0,$RowFolhaPro['base_irrf'],0,0,'R');

$pdf ->SetXY(16.9,13.2 + $c);
$pdf->Cell(3.5,0,$TaxaINSS,0,0,'L');


// PRINTANDO OS TITULOS DOS CAMPOS COM A FONTE MENOR

$pdf ->SetFontSize(5);
$pdf ->SetTextColor(0,99,33) ;

$pdf ->SetXY(1.1,2.9 + $c);
$pdf ->Cell(0,0,"CÓDIGO",0,0,'L');

$pdf ->SetXY(2.4,2.9 + $c);
$pdf ->Cell(0,0,"NOME DO FUNCIONÁRIO",0,0,'L');

$pdf ->SetXY(10.4,2.9 + $c);
$pdf ->Cell(0,0,"CBO",0,0,'L');

$pdf ->SetXY(11.4,2.9 + $c);
$pdf ->Cell(0,0,"EMP. LOCAL",0,0,'L');

$pdf ->SetXY(13.4,2.9 + $c);
$pdf ->Cell(0,0,"DEPTO.",0,0,'L');

$pdf ->SetXY(14.8,2.9 + $c);
$pdf ->Cell(0,0,"SETOR",0,0,'L');

$pdf ->SetXY(16.5,2.9 + $c);
$pdf ->Cell(0,0,"SEÇÃO FL",0,0,'L');

//

$pdf ->SetXY(0.8,4.3 + $c);
$pdf ->Cell(0,0,"COD.",0,0,'L');

$pdf ->SetXY(4.9,4.3 + $c);
$pdf ->Cell(0,0,"DESCRIÇÃO",0,0,'L');

$pdf ->SetXY(9.8,4.3 + $c);
$pdf ->Cell(0,0,"REFERÊNCIA",0,0,'L');

$pdf ->SetXY(12.2,4.3 + $c);
$pdf ->Cell(0,0,"VENCIMENTOS",0,0,'L');

$pdf ->SetXY(15.8,4.3 + $c);
$pdf ->Cell(0,0,"DESCONTOS",0,0,'L');

//

$pdf ->SetXY(2.4,12.8 + $c);
$pdf->Cell(0,0,"Salário-Base",0,0,'L');

$pdf ->SetXY(4.4,12.8 + $c);
$pdf->Cell(0,0,"Sal. Contr. INSS",0,0,'L');

$pdf ->SetXY(7.7,12.8 + $c);
$pdf->Cell(0,0,"Base de Cálc. FGTS",0,0,'L');

$pdf ->SetXY(10.7,12.8 + $c);
$pdf->Cell(0,0,"FGTS do mês",0,0,'L');

$pdf ->SetXY(13.9,12.8 + $c);
$pdf->Cell(0,0,"Base de Cálc. IRRF",0,0,'L');

$pdf ->SetXY(16.5,12.8 + $c);
$pdf->Cell(0,0,"Faixa IRRF",0,0,'L');


// PRINTANDO OS TOTAIS
$pdf ->SetFontSize(6);

$pdf ->SetXY(11.5,11.2 + $c);
$pdf->Cell(0,0,"TOTAL DE VENCIMENTOS",0,0,'L');

$pdf ->SetXY(15.2,11.2 + $c);
$pdf->Cell(0,0,"TOTAL DE DESCONTOS",0,0,'L');

$pdf ->SetXY(11.2,12.2 + $c);
$pdf->Cell(0,0,"VALOR LIQUIDO",0,0,'L');

// FINALIZANDO OS TEXTOS MENORES
}

$a = 0;
$c = 0;
$i = 0;

} 
// FINALIZANDO O LOOP

$pdf->Output("../arquivos/$nomearquivo");
echo "Gerando arquivo PDF.";
print "<script>location.href=\"../arquivos/$nomearquivo\"</script>";
$pdf->Close();
?>
</body>
</html>