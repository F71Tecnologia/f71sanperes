<?php
include('../../conn.php');
include('../../funcoes.php');
include('../../classes/regiao.php');
include('../../classes/clt.php');
include('../../classes/curso.php');
require('../fpdf/fpdf.php');

if(empty($_REQUEST['gerar'])){ 
    $link = $_REQUEST['enc'];?>
    <table cellpadding='8' cellspacing='8' align='center' style="border:1px solid #ddd; border-radius:10px; -moz-border-radius:10px; background-color:#f5f5f5;">

        <tr>   <td align="right"></td>     </tr>
        <tr>
            <td>
                <div id="tela2">
                    <a class="botao" target="_blank" style="margin:10px auto;" href="ferias.php?gerar=1&enc=<?= $link ?>">Gerar PDF</a><br><br>
                    <a class="botao" style="margin:10px auto;" href="index.php">Voltar</a>
                </div>
            </td>
        </tr>
    </table>
<?php exit;}

// Recebendo a variável criptografada
list($regiao,$clt,$id_ferias) = explode('&',decrypt(str_replace('--','+',$_REQUEST['enc'])));




$data      = date('d/m/Y');

$ClassDATA = new regiao();

$qr_ferias = mysql_query("SELECT * , date_format(data_ini, '%d/%m/%Y') as data_ini, date_format(data_fim, '%d/%m/%Y') as data_fim FROM rh_ferias WHERE id_ferias = '$id_ferias'");
$ferias    = mysql_fetch_array($qr_ferias);


$Mes1 = explode('/', $ferias['data_ini']);
$Mes2 = explode('/', $ferias['data_fim']);

// Informações do CLT
$Clt = new clt();
$Clt -> MostraClt($clt);
$id_clt 		= $Clt -> id_clt;
$nome 		    = $Clt -> nome;
$campo1 	    = $Clt -> campo1;
$locacao 	    = $Clt -> locacao;
$id_curso 	    = $Clt -> id_curso;
$banco	 	    = $Clt -> banco;
$agencia 	    = $Clt -> agencia;
$conta	 	    = $Clt -> conta;
$salario	    = $Clt -> salario;
$data_entrada       = $Clt -> data_entrada;
$campo3             = $Clt -> campo3;
$admissao           = implode('/', array_reverse(explode('-', $data_entrada)));
$id_projeto         = $Clt->id_projeto;

$banco = @mysql_result(mysql_query("SELECT id_nacional FROM bancos WHERE id_banco = '$banco'"),0);

$qr_projeto  = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);



$qr_empresa  = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$id_projeto';");
$row_empresa = mysql_fetch_array($qr_empresa);





// Formatacoes
function formata_valor1($objeto) {
	$objeto = number_format($objeto,2,',','.');
	return $objeto;
}

function formata_valor2($objeto) {
    $objeto = str_replace('.',',', $objeto);
	return $objeto;
}

function valorPorExtenso($valor=0) {
	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
	$z=0;

	$valor = number_format($valor, 2, ".", ".");

	$inteiro = explode(".", $valor);

	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

	// $Fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
	$fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);

	for ($i=0;$i<count($inteiro);$i++) {
		$valor = $inteiro[$i];
		$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
		$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
		$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

		$r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
		$t = count($inteiro)-1-$i;
		$r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
		
		if ($valor == "000")$z++; elseif ($z > 0) $z--;

		if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t]; 

		if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;

	}
	return($rt ? $rt : "zero");
}

// Geral
switch(date('m')) {
	case "1":  $mes_portugues = "Janeiro"; 	    break;
	case "2":  $mes_portugues = "Fevereiro";	break;
	case "3":  $mes_portugues = "Mar&ccedil;o"; break;
	case "4":  $mes_portugues = "Abril";        break;
	case "5":  $mes_portugues = "Maio";         break;
	case "6":  $mes_portugues = "Junho";        break;
	case "7":  $mes_portugues = "Julho";        break;
	case "8":  $mes_portugues = "Agosto";       break;
	case "9":  $mes_portugues = "Setembro";     break;
	case "10": $mes_portugues = "Outubro"; 	    break;
	case "11": $mes_portugues = "Novembro"; 	break;
	case "12": $mes_portugues = "Dezembro"; 	break;
}

$Cabecalho  = $row_projeto['nome'];
$Cabecalho .= ', ';
$Cabecalho .= $ClassDATA -> MostraDataCompleta($data);
$Curso      = new tabcurso();
$Curso     -> MostraCurso($id_curso);
$cargo      = $Curso -> cargo;

$faltas           = $ferias['faltas'];
$dias_ferias_base = $ferias['dias_ferias'];
$retorno          = implode('/', array_reverse(explode('-', $ferias['data_retorno'])));
$aquisitivo_ini   = implode('/', array_reverse(explode('-', $ferias['data_aquisitivo_ini'])));
$aquisitivo_fim   = implode('/', array_reverse(explode('-', $ferias['data_aquisitivo_fim'])));
$porcentagem_inss = $ferias['inss_porcentagem'];

// Original
$salario_contratual       = $ferias['salario'];
$salario_variavel         = $ferias['salario_variavel'];
$remuneracao_base   	  = $ferias['remuneracao_base'];
$valor_dia_ferias 		  = $ferias['valor_dias_ferias'];
$total_remuneracoes       = $ferias['total_remuneracoes'];
$total_descontos 		  = $ferias['total_descontos'];
$total_liquido			  = $ferias['total_liquido'];
$abono_pecuniario         = $ferias['abono_pecuniario'];
$umterco_abono_pecuniario = $ferias['umterco_abono_pecuniario'];

// Formatados
$salario_contratualF       = formata_valor1($salario_contratual);
$salario_variavelF         = formata_valor2($salario_variavel);
$remuneracao_baseF         = formata_valor2($remuneracao_base);
$valor_dia_feriasF         = formata_valor2($valor_dia_ferias);
$final_remuneracoesF       = formata_valor1($total_remuneracoes);
$final_descontosF          = formata_valor1($total_descontos);
$final_liquidoF            = formata_valor1($total_liquido);
$abono_pecuniarioF         = formata_valor1($abono_pecuniario);
$umterco_abono_pecuniarioF = formata_valor1($umterco_abono_pecuniario);

// Muito Importante (Mesmo Mês)
if($Mes1[1] == $Mes2[1]) {
$dias_ferias = $dias_ferias_base;

// ++ Original
$valor_total_ferias1       = $ferias['valor_total_ferias'];
$acrescimo_constitucional1 = $ferias['umterco'];
$total_remuneracoes1       = $ferias['total_remuneracoes'];
$valor_inss                = $ferias['inss'];
$valor_irrf                = $ferias['ir'];
$pensao_alimenticia        = $ferias['pensao_alimenticia'];
$total_descontos           = $ferias['total_descontos'];
$total_liquido             = $ferias['total_liquido'];

// ++ Formatados
$valor_total_ferias1F       = formata_valor2($valor_total_ferias1);
$acrescimo_constitucional1F = formata_valor2($acrescimo_constitucional1);
$total_remuneracoes1F       = formata_valor2($total_remuneracoes1);
$valor_inssF                = formata_valor2($valor_inss);
$valor_irrfF                = formata_valor2($valor_irrf);
$pensao_alimenticiaF        = formata_valor2($pensao_alimenticia);
$total_descontosF           = formata_valor2($total_descontos);
$total_liquidoF             = formata_valor2($total_liquido);
$final_remuneracoesF        = formata_valor2($total_remuneracoes1);
$final_descontosF           = formata_valor2($total_descontos);
$final_liquidoF             = formata_valor2($total_liquido);
$valor_e                    = valorPorExtenso($total_liquido);

// 0,00
$total_remuneracoes2F = formata_valor1($x);
$valor_inss2F         = formata_valor1($x);
$total_descontos2F    = formata_valor1($x);
$total_liquido2F      = formata_valor1($x);

// Muito Importante (Meses Diferentes)
} else {

$diasmes      = cal_days_in_month(CAL_GREGORIAN, $Mes1[1], $Mes1[2]);
$dias_ferias  = $diasmes - $Mes1[0] + 1;
$dias_ferias2 = $dias_ferias_base - $dias_ferias;

// Periodo 1
$valor_total_ferias1       = $ferias['valor_total_ferias1'];
$acrescimo_constitucional1 = $ferias['acrescimo_constitucional1'];
$total_remuneracoes1       = $ferias['total_remuneracoes1'];
$valor_inss                = $ferias['inss'];
$valor_irrf                = $ferias['ir'];
$pensao_alimenticia        = $ferias['pensao_alimenticia'];
$total_descontos           = $pensao_alimenticia + $valor_inss + $valor_irrf;
$total_liquido             = $total_remuneracoes1 - $total_descontos;



// Periodo 2
$valor_total_ferias2       = $ferias['valor_total_ferias2'];
$acrescimo_constitucional2 = $ferias['acrescimo_constitucional2'];
$total_remuneracoes2       = $ferias['total_remuneracoes2'];
$valor_inss2               = $ferias['valor_total_ferias1'] +  $ferias['valor_total_ferias2'] + $ferias['acrescimo_constitucional1'] + $ferias['acrescimo_constitucional2'];
$total_descontos2          = $x;
$total_liquido2            = $total_remuneracoes2;

// Formatados Periodo 1
$valor_total_ferias1F       = formata_valor1($valor_total_ferias1);
$acrescimo_constitucional1F = formata_valor1($acrescimo_constitucional1);
$total_remuneracoes1F       = formata_valor1($total_remuneracoes1);
$valor_inssF                = formata_valor1($valor_inss);
$valor_irrfF                = formata_valor1($valor_irrf);
$pensao_alimenticiaF        = formata_valor1($pensao_alimenticia);
$total_descontosF           = formata_valor1($total_descontos);
$total_liquidoF             = formata_valor1($total_liquido);

// Formatados Periodo 2
$valor_total_ferias2F       = formata_valor1($valor_total_ferias2);
$acrescimo_constitucional2F = formata_valor1($acrescimo_constitucional2);
$total_remuneracoes2F       = formata_valor1($total_remuneracoes2);
$valor_inss2F               = formata_valor1($valor_inss2);
$total_descontos2F          = formata_valor1($total_descontos2);
$total_liquido2F            = formata_valor1($total_liquido2);

// Formatados Geral
$valor_e = valorPorExtenso($ferias['total_liquido']);

} // Fim

define('FPDF_FONTPATH','../fpdf/font/');
$pdf  = new FPDF("P","cm","A4");
$pdf -> SetAutoPageBreak(true,0.0); // Reduz a tolerância da margem inferior
$pdf -> Open();
$pdf -> SetFont('Arial','B',8);
$pdf -> Cell(5, 30, " ");

if($_COOKIE['logado'] == 87) {
    echo  $row_projeto['nome'];

    
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Gerando F&eacute;rias</title>
</head>
<body>
<?php
$pdf -> Image('../images/fundo_ferias.jpg', 0,0.9,20.4075,27.1771,'jpg');


$pdf -> SetXY(1.8,7.2);
$pdf -> Cell(0,0,$row_empresa['nome'],0,0,'L');

$pdf -> SetXY(1.8,3);
$pdf -> Cell(0,0,$numero_variavel." $Cabecalho",0,0,'L');

$pdf -> SetXY(2.8,3.54);
$pdf -> Cell(0,0,$nome . " ( " . $campo3 . " ) ",0,0,'L');

$pdf -> SetXY(15.82,3.55);
$pdf -> Cell(0,0,$admissao,0,0,'L');

$pdf -> SetXY(2.8,4.05);
$pdf -> MultiCell(0,0,$campo1,0,'L');

$pdf -> SetXY(6.9,4.05);
$pdf -> Cell(0,0,$locacao,0,0,'L');

$pdf -> SetXY(14.2,4.10);
$pdf -> Cell(0,0,$cargo,0,0,'L');

$pdf -> SetXY(1.8,5.7);
$pdf -> Cell(0,0,$aquisitivo_ini,0,0,'L');

$pdf -> SetXY(3.4,5.7);
$pdf -> Cell(0,0,"a",0,0,'L');

$pdf -> SetXY(3.9,5.7);
$pdf -> Cell(0,0,$aquisitivo_fim,0,0,'L');

$pdf -> SetXY(6.5,5.7);
$pdf -> Cell(0,0,$ferias['data_ini'],0,0,'L');

$pdf -> SetXY(8.1,5.7);
$pdf -> Cell(0,0,"a",0,0,'L');

$pdf -> SetXY(8.4,5.7);
$pdf -> Cell(0,0,$ferias['data_fim'],0,0,'L');

$pdf -> SetXY(11.36,5.7);
$pdf -> Cell(0,0,$retorno,0,0,'L');

$pdf -> SetXY(14.2,5.7);
$pdf -> Cell(0,0,"",0,0,'L');

$pdf -> SetXY(15.8,5.7);
$pdf -> Cell(0,0,"",0,0,'L');

$pdf -> SetXY(16.3,5.7);
$pdf -> Cell(0,0,"",0,0,'L');

$pdf -> SetXY(5,9.2);
$pdf -> Cell(0,0,$nome . " ( " . $campo3 . " ) ",0,0,'L');

$pdf -> SetXY(2.7,9.64);
$pdf -> Cell(0,0,"$campo1",0,0,'L');

$pdf -> SetXY(6.8,9.64);
$pdf -> Cell(0,0,"$locacao",0,0,'L');

$pdf -> SetXY(14.1,9.61);
$pdf -> Cell(0,0,"$cargo",0,0,'L');

$pdf -> SetXY(2.8,10);
$pdf -> Cell(0,0,$banco,0,0,'L');

$pdf -> SetXY(10.7,10);
$pdf -> Cell(0,0,$agencia,0,0,'L');

$pdf -> SetXY(14.1,10);
$pdf -> Cell(0,0,$conta,0,0,'L');

$pdf -> SetXY(1.8,10.95);
$pdf -> Cell(0,0,$aquisitivo_ini,0,0,'L');

$pdf -> SetXY(3.4,10.95);
$pdf -> Cell(0,0,"a",0,0,'L');

$pdf -> SetXY(3.9,10.95);
$pdf -> Cell(0,0,$aquisitivo_fim,0,0,'L');

$pdf -> SetXY(7.45,10.95);
$pdf -> Cell(0,0,$ferias['data_ini'],0,0,'L');

$pdf -> SetXY(9.05,10.95);
$pdf -> Cell(0,0,"a",0,0,'L');

$pdf -> SetXY(9.45,10.95);
$pdf -> Cell(0,0,$ferias['data_fim'],0,0,'L');

$pdf -> SetXY(13.4,10.95);
$pdf -> Cell(0,0,"",0,0,'L');

$pdf -> SetXY(15.2,10.95);
$pdf -> Cell(0,0,"",0,0,'L');

$pdf -> SetXY(15.7,10.95);
$pdf -> Cell(0,0,"",0,0,'L');

$pdf -> SetXY(1.8,12.4);
$pdf -> Cell(0,0,"-",0,0,'L');

$pdf -> SetXY(2,12.4);
$pdf -> Cell(0.40,0,$faltas,0,0,'C');

$pdf -> SetXY(2.32,12.4);
$pdf -> Cell(0,0,"-",0,0,'L');

$pdf -> SetXY(5.3,12.4);
$pdf -> Cell(0,0,"R$ ".$salario_contratualF,0,0,'L');

$pdf -> SetXY(8.9,12.4);
$pdf -> Cell(0,0,"R$ ".$salario_variavelF,0,0,'L');

$pdf -> SetXY(12.4,12.4);
$pdf -> Cell(0,0,"R$ ".$remuneracao_baseF,0,0,'L');

if($ferias['ferias_dobradas'] == "sim") {

$pdf -> SetXY(14,12.4);
$pdf -> Cell(0,0,"*Art. 137 CLT - FÉRIAS EM DOBRO",0,0,'L');

}

// Valores do 1º Mês
$pdf -> SetXY(6.150,13.38);
$pdf -> Cell(0,0,$ClassDATA -> MostraMes($Mes1[1])."/".$Mes1[2],0,0,'L');

$pdf -> SetXY(1.8,14.38);
$pdf -> Cell(0,0,$dias_ferias,0,0,'L');

$pdf -> SetXY(2.2,14.38);
$pdf -> Cell(0,0,"dias a",0,0,'L');

$pdf -> SetXY(3.1,14.38);
$pdf -> Cell(0,0,"R$ ".$valor_dia_feriasF,0,0,'L');

$pdf -> SetXY(1.8,15);
$pdf -> Cell(0,0,"Acréscimo constitucional 1/3",0,0,'L');

$pdf -> SetXY(8.5,14.38);
$pdf -> Cell(0,0,"R$ ".$valor_total_ferias1F,0,0,'L');

$pdf -> SetXY(8.5,15);
$pdf -> Cell(0,0,"R$ ".$acrescimo_constitucional1F,0,0,'L');

if(!empty($ferias['dias_abono_pecuniario'])) {

$pdf -> SetXY(1.8,15.7);
$pdf -> Cell(0,0,"Abono Pecuniário",0,0,'L');

$pdf -> SetXY(8.5,15.7);
$pdf -> Cell(0,0,"R$ ".$abono_pecuniarioF,0,0,'L');

$pdf -> SetXY(1.8,16.4);
$pdf -> Cell(0,0,"1/3 sobre Abono Pecuniário",0,0,'L');

$pdf -> SetXY(8.5,16.4);
$pdf -> Cell(0,0,"R$ ".$umterco_abono_pecuniarioF,0,0,'L');

}

$pdf -> SetXY(8.5,17.86);
$pdf -> Cell(0,0,"R$ ".$total_remuneracoes1F,0,0,'L');

$pdf -> SetXY(1.8,18.9);
$pdf -> Cell(0,0,"Pensão Alimentícia",0,0,'L');

$pdf -> SetXY(1.8,19.5);
$pdf -> Cell(0,0,"INSS",0,0,'L');

$pdf -> SetXY(2.8,19.5);
$pdf -> Cell(0,0,$porcentagem_inss. "%",0,0,'L');

$pdf -> SetXY(1.8,20.1);
$pdf -> Cell(0,0,"IRRF",0,0,'L');

$pdf -> SetXY(8.5,18.9);
$pdf -> Cell(0,0,"R$ ".$pensao_alimenticiaF,0,0,'L');

$pdf -> SetXY(8.5,19.5);
$pdf -> Cell(0,0,"R$ ".$valor_inssF,0,0,'L');

$pdf -> SetXY(8.5,20.1);
$pdf -> Cell(0,0,"R$ ".$valor_irrfF,0,0,'L');

$pdf -> SetXY(8.5,21);
$pdf -> Cell(0,0,"R$ ".$total_descontosF,0,0,'L');

$pdf -> SetXY(8.5,21.55);
$pdf -> Cell(0,0,"R$ ".$total_liquidoF,0,0,'L');

//

// Valores do 2º Mês
if($Mes1[1] != $Mes2[1]) {
	
$pdf -> SetXY(14.70,13.38);
$pdf -> Cell(0,0,$ClassDATA -> MostraMes($Mes2[1])."/".$Mes2[2],0,0,'L');
	
$pdf -> SetXY(10.4,14.38);
$pdf -> Cell(0,0,$dias_ferias2,0,0,'L');

$pdf -> SetXY(10.8,14.38);
$pdf -> Cell(0,0,"dias a",0,0,'L');

$pdf -> SetXY(11.7,14.38);
$pdf -> Cell(0,0,"R$ ".$valor_dia_feriasF,0,0,'L');

$pdf -> SetXY(10.4,15);
$pdf -> Cell(0,0,"Acréscimo constitucional 1/3",0,0,'L');

$pdf -> SetXY(17.26,14.38);
$pdf -> Cell(0,0,"R$ ".$valor_total_ferias2F,0,0,'L');

$pdf -> SetXY(17.26,15);
$pdf -> Cell(0,0,"R$ ".$acrescimo_constitucional2F,0,0,'L');

$pdf -> SetXY(17.3,17.86);
$pdf -> Cell(0,0,"R$ ".$total_remuneracoes2F,0,0,'L');

$pdf -> SetXY(10.4,18.9);
$pdf -> Cell(0,0,"Base de INSS",0,0,'L');

$pdf -> SetXY(10.4,19.5);
$pdf -> Cell(0,0,"IRRF",0,0,'L');

$pdf -> SetXY(17.3,18.9);
$pdf -> Cell(0,0,"R$ ".$valor_inss2F,0,0,'L');

$pdf -> SetXY(15.0,19.5);
$pdf -> Cell(0,0,"(Apurado em ".$ClassDATA -> MostraMes($Mes1[1])."/".$Mes1[2].")",0,0,'L');

$pdf -> SetXY(17.3,21);
$pdf -> Cell(0,0,"R$ ".$total_descontos2F,0,0,'L');

$pdf -> SetXY(17.3,21.55);
$pdf -> Cell(0,0,"R$ ".$total_liquido2F,0,0,'L');

}

//

// Valores Finais

$pdf -> SetXY(7.5,22.1);
$pdf -> Cell(0,0,"R$ ".$final_remuneracoesF,0,0,'L');

$pdf -> SetXY(11.3,22.1);
$pdf -> Cell(0,0,"R$ ".$final_descontosF,0,0,'L');

$pdf -> SetXY(17.3,22.1);
$pdf -> Cell(0,0,"R$ ".$final_liquidoF,0,0,'L');

//

$pdf -> SetXY(3.4,22.71);
$pdf -> Cell(0,0,$row_empresa['razao'] . " ( CNPJ: " . $row_empresa['cnpj'] . " )",0,0,'L');

$pdf -> SetXY(1.9,23.6);
$pdf -> Cell(0,0,"*** " . $valor_e . " ***",0,0,'L');

$pdf -> SetXY(12.8,25.6);
$pdf -> Cell(0,0,$nome,0,0,'L');

# DICIONADO EM 16/09/2011 Por Maikom James
# Colocando o numero do cpf no final do pdf

$qr_clt = mysql_query("SELECT cpf FROM rh_clt WHERE id_clt = '{$id_clt}' LIMIT 1");
$cpf = @mysql_result($qr_clt,0);

$pdf -> SetXY(13.5,25.9);
$pdf -> Cell(0,0,'('.$cpf.')',0,0,'L');


$nomearquivo = 'ferias_'.$clt.'_'.$id_ferias.'.pdf';

$pdf ->Output("../arquivos/ferias/$nomearquivo");
echo "<b>Gerando arquivo PDF...</b>";
echo "<script>location.href=\"../arquivos/ferias/$nomearquivo\"</script>";
$pdf -> Close();
?>
</body>
</html>