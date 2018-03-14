<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="www.netsorrindo.com.br/intranet/login.php">Logar</a>';
	exit;
}

include('../../conn.php');
include('../../classes/funcionario.php');
require('../fpdf/fpdf.php');

$Fun      = new funcionario();
$Fun     -> MostraUser(0);
$Master   = $Fun -> id_master;
$ano_base = date('Y') - 1;

//mysql_query("INSERT INTO rais (tipo,ano_base,regiao,autor,data) VALUES ('pdf','$ano_base',$_GET[regiao],'$_COOKIE[logado]',NOW())");

define('FPDF_FONTPATH','../fpdf/font/');
$pdf = new FPDF("L","cm","A4");
$pdf->SetAutoPageBreak(true,0.0);
$pdf->Open();

$regiao     = $_GET['regiao'];
$qr_empresa = mysql_query("SELECT * FROM master WHERE id_master = '$Master' AND status = '1'");
$empresa    = mysql_fetch_assoc($qr_empresa);
$endereco   = explode(',', $empresa['endereco']);
$numero     = explode('-', $endereco[1]);
?>
<html>
<head>
<title>Gerando RAIS</title>
<meta http-equiv="Content-Type" Content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
$Inicio    = 0;
$numeracao = 0;

// Consulta para número de empregados
$qr_pai  = mysql_query("SELECT * FROM rh_clt WHERE  year(data_entrada) <= '$ano_base' AND (year(data_saida) >= '$ano_base' OR data_saida = '0000-00-00' OR data_saida = NULL) ORDER BY 'nome' ASC");
$num_pai = mysql_num_rows($qr_pai);

// Consulta para número de páginas
$max = 5;

// DIVIDE E ARREDONDA PARA CIMA - MAIKOM 20/09/2010 8:52 AM
//$calc = gmp_div_q($num_pai, $max, GMP_ROUND_PLUSINF);
//$pedaco = gmp_strval($calc);
// MUDADO PARA:
$calc   = ceil($num_pai / $max);
$pedaco = (string) $calc;




// Laço de repetição de páginas
for($a=1; $a <= $pedaco; $a ++) {

$pdf->SetFont('Arial','B',8);
$pdf->Cell(5, 30, " ");

$pdf->Image('fundo_rais.gif', 0.5,0,28.7,20.5,'gif');

// Informações da Empresa
$pdf ->SetXY(0.5,3.2);
$pdf->Cell(0,0,$empresa['nome'],0,0,'L');

$pdf ->SetXY(0.5,4.3);
$pdf->Cell(0,0,$endereco[0],0,0,'L');

$pdf ->SetXY(11.5,4.3);
$pdf->Cell(0,0,$numero[0],0,0,'L');

$pdf ->SetXY(0.5,5.4);
$pdf->Cell(0,0,$complemento,0,0,'L');

$pdf ->SetXY(7.05,5.4);
$pdf->Cell(0,0,$numero[1],0,0,'L');

$pdf ->SetXY(0.5,6.5);
$pdf->Cell(0,0,$empresa['cep'],0,0,'L');

$pdf ->SetXY(3.6,6.5);
$pdf->Cell(0,0,$numero[2],0,0,'L');

$pdf ->SetXY(12,6.5);
$pdf->Cell(0,0,$numero[3],0,0,'L');

$pdf ->SetXY(0.5,7.6);
$pdf->Cell(0,0,$empresa['tel'],0,0,'L');

$pdf ->SetXY(5.2,7.6);
$pdf->Cell(0,0,$empresa['cod_municipio'],0,0,'L');

$pdf ->SetXY(9.6,7.6);
$pdf->Cell(0,0,$empresa['cnae'],0,0,'L');

$pdf ->SetXY(11.5,7.6);
$pdf->Cell(0,0,$empresa['nat_juridica'],0,0,'L');

$pdf ->SetXY(13.5,7.6);
$pdf->Cell(0,0,$empresa['proprietarios'],0,0,'L');

$pdf ->SetXY(15.8,7.6);
$pdf->Cell(0,0,$empresa['cnpj'],0,0,'L');

$pdf ->SetXY(27.5,4.3);
$pdf->Cell(0,0,$ano_base,0,0,'L');

// Laço de repetição de empregados (5 por página)
$qr_empregado = mysql_query("SELECT * FROM rh_clt WHERE  year(data_entrada) <= '$ano_base' AND (year(data_saida) >= '$ano_base' OR data_saida = '0000-00-00' OR data_saida = NULL) ORDER BY 'nome' ASC LIMIT $Inicio,5");
while($empregado = mysql_fetch_assoc($qr_empregado)) {

$numeracao++;
$quebra_linha = $quebra_linha + 2.35;

$data_nascimento = implode("/", array_reverse(explode("-", $empregado['data_nasci'])));
$data_admissao = implode("/", array_reverse(explode("-", $empregado['data_entrada'])));

$qr_cod_etnia = mysql_query("SELECT cod FROM etnias WHERE id = '$empregado[etnia]'");
$cod_etnia = mysql_fetch_assoc($qr_cod_etnia);
$etnia = number_format($cod_etnia['cod'],0,'.','.');

$qr_curso = mysql_query("SELECT cbo_codigo,salario  FROM curso WHERE id_curso = '$empregado[id_curso]'");
$curso = mysql_fetch_array($qr_curso);

if($empregado['nacionalidade'] == "BRASILEIRO" or $empregado['nacionalidade'] == "BRASILEIRA") {
	$nacionalidade = "10";
} else {
	$nacionalidade = "Outros";
}

$vinculo = "10";

if($empregado['escolaridade'] < 12 and $empregado['escolaridade'] > 0) {
	$qr_cod_escolaridade = mysql_query("SELECT cod FROM escolaridade WHERE id = '$empregado[escolaridade]'");
	$cod_escolaridade = mysql_fetch_assoc($qr_cod_escolaridade);
	$instrucao = number_format($cod_escolaridade['cod'],0,'.','.');
}

$qr_horario = mysql_query("SELECT * FROM rh_horarios WHERE id_horario = '$empregado[rh_horario]' AND id_regiao = '$empregado[id_regiao]'");
$horario = mysql_fetch_assoc($qr_horario);

if(isset($horario['horas_mes']) and isset($horario['dias_semana'])) {
$horas_semanais = number_format((($horario['saida_2'] - $horario['entrada_1']) - ($horario['entrada_2'] - $horario['saida_1'])) * $horario['dias_semana'],0,'.','.');
}

// Consulta de Rescisão
$qr_rescisao = mysql_query("SELECT data_demi, motivo, total_liquido  FROM rh_recisao WHERE id_clt = '$empregado[id_clt]' AND year(data_demi) = '$ano_base' AND motivo IN (60,61,62,80,81,100)");
$rescisao = mysql_fetch_assoc($qr_rescisao);
$verifica_rescisao = mysql_num_rows($qr_rescisao);

if(!empty($verifica_rescisao)) {
    $dia_mes_desligamento = substr(implode("/", array_reverse(explode("-", $rescisao['data_demi']))),0,5);
	$mes_desligamento = substr($dia_mes_desligamento, 3, 5);
	$mes_final = $mes_desligamento;
        if ($rescisao['motivo'] == 60) {
	           $causa = '10';
        } elseif ($rescisao['motivo'] == 61) {
	           $causa = '11';
        } elseif ($rescisao['motivo'] == 62 or $rescisao['motivo'] == 100) {
	           $causa = '12';
        } elseif ($rescisao['motivo'] == 80) {
	           $causa = '76';
        } elseif ($rescisao['motivo'] == 81) {
	           $causa = '60';
        }
} else {
	 $mes_final = "13";
}

// Consulta de Folha
for($i=1; $i<$mes_final; $i++) {
$tubarao = sprintf('%02d', $i);
$qr_folha = mysql_query("SELECT salliquido, a8006 FROM rh_folha_proc INNER JOIN rh_folha ON rh_folha_proc.id_folha = rh_folha.id_folha WHERE id_clt = '$empregado[id_clt]' AND id_regiao = '$empregado[id_regiao]' AND regiao = '$empregado[id_regiao]' AND rh_folha_proc.status = '3' AND rh_folha.status = '3' AND rh_folha_proc.mes = '$tubarao' AND year(rh_folha.data_inicio) = '$ano_base' AND rh_folha.terceiro = '2'");
$folha = mysql_fetch_assoc($qr_folha);
$numero_folha = mysql_num_rows($qr_folha);
	if(!empty($numero_folha)) {
	$meses[] = "R$ ".str_replace(".", ",", ($folha['salliquido']-$folha['a8006'])).""; //SUBTRAINDO O VALE REFEIÇÀO - SABINO 22/08/2011
	} else {
	$meses[] = NULL;
	}
}

if(!empty($verifica_rescisao)) {
    $meses[] = "R$ ".str_replace(".", ",", $rescisao['total_liquido'])."";
}

// Consulta de 13º Salário
$qr_salario13 = mysql_query("SELECT salliquido,rh_folha_proc.mes,tipo_terceiro FROM rh_folha_proc INNER JOIN rh_folha ON rh_folha_proc.id_folha = rh_folha.id_folha WHERE id_clt = '$empregado[id_clt]' AND id_regiao = '$empregado[id_regiao]' AND regiao = '$empregado[id_regiao]' AND rh_folha_proc.status = '3' AND rh_folha.status = '3' AND year(rh_folha.data_inicio) = '$ano_base' AND rh_folha.terceiro = '1'");
$numero_salario13 = mysql_num_rows($qr_salario13);
if(!empty($numero_salario13)) {
	while($salario13 = mysql_fetch_assoc($qr_salario13)) {
		if($salario13['tipo_terceiro'] == 3) {
          $valor13_2 = "R$ ".number_format($salario13['salliquido'],2,',','')."";
          $mes13_2 = $salario13['mes'];
		} elseif($salario13['tipo_terceiro'] == 1) {
          $valor13 = "R$ ".number_format($salario13['salliquido'],2,',','')."";
          $mes13 = $salario13['mes'];
		} elseif($salario13['tipo_terceiro'] == 2) {
          $valor13_2 = "R$ ".number_format($salario13['salliquido'],2,',','')."";
          $mes13_2 = $salario13['mes'];
		}
    }
}

$pdf ->SetXY(0.7,7.65+$quebra_linha);
$pdf->Cell(0,0,$numeracao,0,0,'L');

$pdf ->SetXY(1.3,6.97+$quebra_linha);
$pdf->Cell(0,0,$empregado['pis'],0,0,'L');

$pdf ->SetXY(1.3,7.75+$quebra_linha);
$pdf->Cell(0,0,$empregado['campo1'],0,0,'L');

$pdf ->SetXY(1.3,8.55+$quebra_linha);
$pdf->Cell(0,0,$empregado['cpf'],0,0,'L');

$pdf ->SetXY(4.75,6.97+$quebra_linha);
$pdf->Cell(0,0,$empregado['nome'],0,0,'L');

$pdf ->SetXY(4.2,6.97+$quebra_linha);
$pdf->Cell(0,0,$ok,0,0,'L');

$pdf ->SetXY(4.75,7.75+$quebra_linha);
$pdf->Cell(0,0,$data_nascimento,0,0,'L');

$pdf ->SetXY(7.05,7.75+$quebra_linha);
$pdf->Cell(0,0,$data_admissao,0,0,'L');

$pdf ->SetXY(9.9,7.75+$quebra_linha);
$pdf->Cell(0,0,$etnia,0,0,'L');

$pdf ->SetXY(4.75,8.55+$quebra_linha);
$pdf->Cell(0,0,$curso['cbo_codigo'],0,0,'L');

$pdf ->SetXY(6.35,8.55+$quebra_linha);
$pdf->Cell(0,0,$vinculo,0,0,'L');

$pdf ->SetXY(7.3,8.55+$quebra_linha);
$pdf->Cell(0,0,$instrucao,0,0,'L');

$pdf ->SetXY(8,8.55+$quebra_linha);
$pdf->Cell(0,0,$nacionalidade,0,0,'L');

$pdf ->SetXY(9.9,8.55+$quebra_linha);
$pdf->Cell(0,0,$dia_mes_desligamento,0,0,'L');

$pdf ->SetXY(11,8.55+$quebra_linha);
$pdf->Cell(0,0,$causa,0,0,'L');

$pdf ->SetXY(11.75,6.97+$quebra_linha);
$pdf->Cell(0,0,"R$ ".str_replace(".", ",", $curso['salario'])."",0,0,'L');

$pdf ->SetXY(14.4,6.97+$quebra_linha);
$pdf->Cell(0,0,$horas_semanais,0,0,'L');

$pdf ->SetXY(11.8,7.75+$quebra_linha);
$pdf->Cell(0,0,$valor13,0,0,'L');

$pdf ->SetXY(16.1,7.75+$quebra_linha);
$pdf->Cell(0,0,$mes13,0,0,'L');

$pdf ->SetXY(11.8,8.55+$quebra_linha);
$pdf->Cell(0,0,$valor13_2,0,0,'L');

$pdf ->SetXY(16.1,8.55+$quebra_linha);
$pdf->Cell(0,0,$mes13_2,0,0,'L');

$pdf ->SetXY(17.13,6.97+$quebra_linha);
$pdf->Cell(0,0,$meses[0],0,0,'L');

$pdf ->SetXY(20.38,6.97+$quebra_linha);
$pdf->Cell(0,0,$meses[1],0,0,'L');

$pdf ->SetXY(23.4,6.97+$quebra_linha);
$pdf->Cell(0,0,$meses[2],0,0,'L');

$pdf ->SetXY(26.25,6.97+$quebra_linha);
$pdf->Cell(0,0,$meses[3],0,0,'L');

$pdf ->SetXY(17.13,7.75+$quebra_linha);
$pdf->Cell(0,0,$meses[4],0,0,'L');

$pdf ->SetXY(20.38,7.75+$quebra_linha);
$pdf->Cell(0,0,$meses[5],0,0,'L');

$pdf ->SetXY(23.4,7.75+$quebra_linha);
$pdf->Cell(0,0,$meses[6],0,0,'L');

$pdf ->SetXY(26.25,7.75+$quebra_linha);
$pdf->Cell(0,0,$meses[7],0,0,'L');

$pdf ->SetXY(17.13,8.55+$quebra_linha);
$pdf->Cell(0,0,$meses[8],0,0,'L');

$pdf ->SetXY(20.38,8.55+$quebra_linha);
$pdf->Cell(0,0,$meses[9],0,0,'L');

$pdf ->SetXY(23.4,8.55+$quebra_linha);
$pdf->Cell(0,0,$meses[10],0,0,'L');

$pdf ->SetXY(26.25,8.55+$quebra_linha);
$pdf->Cell(0,0,$meses[11],0,0,'L');

unset($meses);
unset($causa);
unset($dia_mes_desligamento);
unset($mes_desligamento);
unset($mes_final);
unset($valor13);
unset($mes13);
unset($valor13_2);
unset($mes13_2);
$Inicio ++;
} // Fim dos loop dos 5 empregados

unset($quebra_linha);
unset($i);
unset($qr_empregado);
unset($empregado);
} // Fim do loop pai

// Página Final da Empresa
$pdf->AddPage('P','A4');

$pdf->SetFont('Arial','',15);
$pdf->Image('recibo.jpg', 0.5,0,19,16.4,'jpg');

$pdf ->SetXY(16.5,3.5);
$pdf->Cell(0,0,$ano_base,0,0,'L');

$pdf ->SetXY(0.55,5.4);
$pdf->Cell(0,0,"".$empresa['nome']." , CNPJ : ".$empresa['cnpj']."",0,0,'L');

$pdf ->SetXY(0.55,6.9);
$pdf->Cell(0,0,"".$endereco[0]." , ".$numero[0]."",0,0,'L');

$pdf ->SetXY(0.45,8.5);
$pdf->Cell(0,0,$numero[1],0,0,'L');

$pdf ->SetXY(6.5,8.5);
$pdf->Cell(0,0,$empresa['cep'],0,0,'L');

$pdf ->SetXY(0.5,10);
$pdf->Cell(0,0,$numero[2],0,0,'L');

$pdf ->SetXY(7.3,10);
$pdf->Cell(0,0,$numero[3],0,0,'L');

$pdf ->SetXY(0.55,11.5);
$pdf->Cell(0,0,$pedaco,0,0,'L');

$pdf ->SetXY(0.5,13.1);
$pdf->Cell(0,0,$numero[2],0,0,'L');

$pdf ->SetXY(7.3,13.1);
$pdf->Cell(0,0,date("d.m.Y"),0,0,'L');

$pdf->Output("../arquivos/rais.pdf");
echo "<b>Gerando arquivo PDF...</b>";
print "<script>location.href=\"../arquivos/rais.pdf\"</script>";
$pdf->Close();

?>
</body>
</html>