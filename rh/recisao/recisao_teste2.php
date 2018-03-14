<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}

include('../../conn.php');
include('../../classes/funcionario.php');
include('../../classes/curso.php');
include('../../classes/clt.php');
include('../../classes/projeto.php');
include('../../classes/calculos.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../funcoes.php');

$Fun     = new funcionario();
$Fun    -> MostraUser(0);
$user	 = $Fun -> id_funcionario;
$regiao  = $_REQUEST['regiao'];

$Curso 	 = new tabcurso();
$Clt 	 = new clt();
$ClasPro = new projeto();
$Calc	 = new calculos();

if(empty($_REQUEST['tela'])) {
	$tela = 1;
} else {
	$tela = $_REQUEST['tela'];
}

if($_GET['deletar'] == true) {
	//$movimentos = mysql_result(mysql_query("SELECT movimentos FROM rh_recisao WHERE id_recisao = '".$_GET['id']."' LIMIT 1"),0);
	//$total_movimentos = (int)count(explode(',',$movimentos));
	//mysql_query("UPDATE rh_movimentos_clt SET status_ferias = '1' WHERE id_movimento IN('".$movimentos."') LIMIT ".$total_movimentos."");
	mysql_query("UPDATE rh_recisao SET status = '0' WHERE id_recisao = '".$_GET['id']."' LIMIT 1");
	mysql_query("UPDATE rh_clt SET status = '10', data_saida = '', status_demi = '' WHERE id_clt = '".$_GET['id_clt']."' LIMIT 1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet :: Rescis&atilde;o</title>
<link href="../../favicon.ico" rel="shortcut icon" />
<link href="../../net1.css" rel="stylesheet" type="text/css">
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function() {
	$('#data_aviso').datepicker({
		changeMonth: true,
	    changeYear: true
	});
});
</script>
<style>
body {
	background-color:#FAFAFA; text-align:center; margin:0px;
}
p {
	margin:0px;
}
#corpo {
	width:90%; background-color:#FFF; margin:0px auto; text-align:left; padding-top:20px; padding-bottom:10px;
}


.verticaltext {
writing-mode: tb-rl;
filter: flipv fliph;
}

.novo_tr td{
font-size:10px;	
	
}
.tabela{
background-color:#FFF;
 margin:0px auto;
 width:100%; 
 border:0; 
 line-height:22px; 
 font-size:11px;
}

</style>

<style media="print">
#corpo {
	width:100%; background-color:#FFF; margin:0px auto; text-align:left; padding-top:20px; padding-bottom:10px;
}
.novo_tr{
font-size:10px;	
	
}
.tabela{
background-color:#FFF;
 margin:0px auto;
 width:100%; 
 border:0; 
 line-height:22px; 
 font-size:11px;
}
</style>
</head>
<body>
<div id="corpo">
<?php

list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));



$Clt 			   -> MostraClt($id_clt);
$nome 			   = $Clt -> nome;
$codigo 		   = $Clt -> campo3;
$data_demissao 	   = $Clt -> data_demi;
$contratacao 	   = $Clt -> tipo_contratacao;
$data_aviso_previo = $Clt -> data_aviso;
$data_demissaoF    = $Fun -> ConverteData($data_demissao);


$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE status IN(10,200) AND id_regiao = '$regiao'  ORDER BY nome ASC");
while($row_clt = mysql_fetch_assoc($qr_clt)):







$id_clt		      = $row_clt['id_clt'];

// Faltas no Mês
list($ano_demissao, $mes_demissao, $dia_demissao) = explode('-', $row_clt['data_demi']);




$qr_faltas = mysql_query("SELECT SUM(qnt) AS faltas FROM rh_movimentos_clt WHERE id_clt = '$id_clt' AND id_mov = '62' AND (status = '1' OR status = '5') AND mes_mov = '".$mes_demissao."' AND ano_mov = '".$ano_demissao."'");
$faltas    = @mysql_result($qr_faltas,0);

if($dia_demissao > 30) {
	$dias_trabalhados = 30;
} else {
	$dias_trabalhados = $dia_demissao;
}

// Calculando Saldo FGTS
$qr_liquido = mysql_query("SELECT SUM(salliquido) AS liquido FROM rh_folha_proc WHERE id_clt = '$id_clt' AND status = '3'");
$fgts = number_format(mysql_result($qr_liquido,0) * 0.08, 2, ',', '.'); 




$regiao		      = $regiao;
$fator		      = 'empregador';
$dispensa		  = '61';
$faltas			  = '';
$dias_trabalhados = 01;
$aviso			  = 'indenizado';
$previo			  = 30;
$fgts			  = $fgts;
$anterior		  = 0;
$valor			  = '0,00';
$data_aviso		  = implode('-', array_reverse(explode('/', $_REQUEST['data_aviso'])));
$devolucao  	  = '';//str_replace(',', '.', str_replace('.', '',  $_REQUEST['devolucao']));

$Clt 		   -> MostraClt($id_clt);
$nome 		    = $Clt -> nome;
$codigo 	    = $Clt -> campo3;
$data_demissao  = $Clt -> data_demi;
$data_entrada   = $Clt -> data_entrada;   
$idprojeto 	    = $Clt -> id_projeto;
$idcurso 	    = $Clt -> id_curso;
$idregiao 	    = $Clt -> id_regiao;
$data_demissaoF = $Fun -> ConverteData($data_demissao);
$data_entradaF  = $Fun -> ConverteData($data_entrada);

$restatus = mysql_query("SELECT especifica FROM rhstatus WHERE codigo = '$dispensa'");

if($valor == '0,00') {
	$Curso -> MostraCurso($idcurso);
	$salario_base = $Curso -> salario;
} else {
	$valor = str_replace(',', '.', str_replace('.', '', $valor));
	$salario_base = $valor;
}

$salario_base_limpo = $salario_base;

// Trabalhando com as Datas
$data_exp = explode('-', $data_demissao);
$data_adm = explode('-', $data_entrada);

$dia_demissao = (int)$data_exp[2];
$mes_demissao = (int)$data_exp[1];
$ano_demissao = (int)$data_exp[0];

$dia_admissao = (int)$data_adm[2];
$mes_admissao = (int)$data_adm[1];
$ano_admissao = (int)$data_adm[0];

// Verificando se o funcionário tem 1 ano de contratação
if(date('Y-m-d') >= date('Y-m-d', strtotime("$data_entrada +1 year"))) {
	$um_ano = '1';
} else {
	$um_ano = '0';
}




//  60 = Com Justa Causa
//  61 = Sem Justa Causa
//  62 = Por outros motivos / 81 = Óbito
//  63 = Pedido de Dispensa Antes do Prazo
//  64 = Dispensa Sem Justa Causa Antecipado Fim Cont. Empregador
//  65 = Pedido de Dispensa
//  66 = Dispensa Sem Justa Causa Fim Cont. Empregador
// 101 = Afastado para Aposentaria

//   0 = NÃO 
//   1 = SIM 
//   2 = PAGA 
//   3 = DEPOSITADO

if($dispensa == 60) {
	
	$terceiro_ssF = 0;
	$t_ss 	= 1; // SALDO SALARIO
	$t_ap 	= 0; // AVISO PREVIO
	$t_fv 	= 1; // FERIAS VENCIDAS
	$t_fp 	= 0; // FERIAS PROPORCIONAIS
	$t_fa 	= 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 0; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 0; // FGTS MULTA 40
	$t_mu   = 0; // MULTA ART 479
	$cod_mov_fgts = 'H';
	$cod_saque_fgts = '02';

} elseif($dispensa == 61) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 1; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu = 0; // MULTA ART 479
	$cod_mov_fgts = '11';
	$cod_saque_fgts = '01';
	
	if($fator == 'empregado') {
		
		$t_f4 = 2; // FGTS MULTA 40
		$cod_mov_fgts = 'J';
		
		if($aviso == 'indenizado') {
			$t_ap = 1; // AVISO PREVIO (PAGA)
		}
		
	}
	
} elseif($dispensa == 62 or $dispensa == 81) {
	
	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa 	= 1; // FERIAS 1/3 ADICIONAL
	$t_13 	= 1; // DECIMO TERCEIRO
	$t_f8   = 1; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu = 1; // MULTA ART 479
	$cod_mov_fgts = '11';
	$cod_saque_fgts = '02';

} elseif($dispensa == 63) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 0	; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';

	
	if($fator == 'empregador') {
		$t_ap = 1; // AVISO PREVIO
	}
	
} elseif($dispensa == 64) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 0; // FGTS MULTA 40

if($fator == 'empregador') {
		$t_mu   = 1; // MULTA ART 479
} else {$t_mu   = 0; // MULTA ART 479
  } 
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';

} elseif($dispensa == 65) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 2; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 1; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';
	
	if($fator == 'empregador') {
		$t_ap = 1; // AVISO PREVIO
	}

} elseif($dispensa == 66) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 1; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 1; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '04';
	
} elseif($dispensa == 101) {

	$t_ss   = 1; // SALDO SALARIO
	$t_ap   = 2; // AVISO PREVIO
	$t_fv   = 1; // FERIAS VENCIDAS
	$t_fp   = 1; // FERIAS PROPORCIONAIS
	$t_fa   = 1; // FERIAS 1/3 ADICIONAL
	$t_13   = 1; // DECIMO TERCEIRO
	$t_f8   = 3; // FGTS 8
	$t_f4   = 1; // FGTS MULTA 40
	$t_mu   = 1; // MULTA ART 479
	$cod_mov_fgts = '01';
	$cod_saque_fgts = '02';
	
	if($fator == 'empregador') {
		$t_ap = 1; // AVISO PREVIO
	}

}



// Movimentos
$qr_movimentos = mysql_query("SELECT * FROM rh_movimentos_clt
									   WHERE id_clt = '$id_clt'
									   AND tipo_movimento = 'CREDITO'
									   AND status = '1'
									   AND lancamento = '2'");
while($row_movimento = mysql_fetch_array($qr_movimentos)) {
			  
	// Acrescenta os Movimentos nas Bases de INSS e IRRF
	$incidencias = explode(',', $row_movimento['incidencia']);
				  
	foreach($incidencias as $incidencia) {
	
		if($incidencia == 5020) { // INSS
			$salario_calc_inss += $row_movimento['valor_movimento'];
		}
					  
		if($incidencia == 5021) { // IRRF
			$salario_calc_IR   += $row_movimento['valor_movimento'];
		}
		
		if($incidencia == 5023) { // FGTS
			$salario_calc_FGTS += $row_movimento['valor_movimento'];
		}
					  
	}
	

	
	
	// Novo Salário Base + Todos os Movimentos
	if($valor == '0,00') {
		$salario_base += $row_movimento['valor_movimento'];
	}
		
  
	$total_rendi += $row_movimento['valor_movimento'];
		  
	$array_codigos_rendimentos[] = $row_movimento['cod_movimento'];
	$array_valores_rendimentos[] = $row_movimento['valor_movimento'];
	  
}

if($array_valores_rendimentos == '') {
	$array_valores_rendimentos[] = '0';
}
// Fim dos Movimentos



/* Vale Refeição (Débito)
$qr_refeicao = mysql_query("SELECT * FROM rh_movimentos_clt
									 WHERE id_clt = '$id_clt'
									 AND status = '1'
									 AND lancamento = '2'
									 AND cod_movimento = '8006'
									 UNION
							SELECT * FROM rh_movimentos_clt
									 WHERE id_clt = '$id_clt'
									 AND status = '1'
									 AND lancamento = '1'
									 AND mes_mov = '$mes_demissao'
									 AND ano_mov = '$ano_demissao'
									 AND cod_movimento = '8006'");
while($row_refeicao = mysql_fetch_array($qr_refeicao)) {

	$vale_refeicao = $row_refeicao['valor_movimento'];
	$debito_vale_refeicao = $vale_refeicao * 0.20;
		  
} */



// Salário Família
$Calc -> Salariofamilia($salario_base,$id_clt,$idprojeto,$data_demissao,'2');
$total_menor	   = $Calc -> filhos_menores;
$valor_sal_familia = (($Calc -> valor) / 30) * $dias_trabalhados;



// Adicional Noturno
$Calc -> adnoturno($id_clt, '');
$valor_adnoturno = (($Calc -> valor) / 30) * $dias_trabalhados;



// Insalubridade / Periculosidade
$Calc -> insalubridade($id_clt,$data_demissao);
//$valor_insalubridade = (($Calc -> valor) / 30) * $dias_trabalhados;
$valor_insalubridade = $Calc -> valor;



// Hora Extra
$qr_hora_extra = mysql_query("SELECT SUM(valor_movimento) AS valor
									FROM rh_movimentos_clt 
								   WHERE id_clt = '$id_clt'
									 AND cod_movimento = '8080' 
									 AND mes_mov = '16' 
									 AND status = '1'");
$hora_extra = mysql_result($qr_hora_extra,0);



// Saldo de Salário e Faltas
$valor_salario_dia = ($salario_base - $total_rendi) / 30;
$data_base 		   = $data_demissao;
$valor_faltas	   = $valor_salario_dia * $faltas;
$saldo_de_salario  = $valor_salario_dia * $dias_trabalhados - $valor_faltas;


// Calculando Previdência
$Calc -> MostraINSS(($saldo_de_salario + $hora_extra), $data_base);
$previ_ss = $Calc -> valor;

if($t_ss == 1) {

	// Calculando INSS sobre Saldo de Salários
	$Calc -> MostraINSS($saldo_de_salario,$data_exp);
	$inss_saldo_salario = $Calc -> valor;
	//
	
	$base_irrf_saldo_salarios = $saldo_de_salario - $inss_saldo_salario - $previ_ss + $valor_insalubridade;
	
	// Calculando IRRF sobre Saldo de Salários
	$Calc -> MostraIRRF($base_irrf_saldo_salarios,$id_clt,$idprojeto,$data_base);
	$irrf_saldo_salario = $Calc -> valor;
	//

	$to_saldo_salario = $saldo_de_salario - $inss_saldo_salario - $irrf_saldo_salario;
	$to_descontos 	  = $irrf_saldo_salario + $inss_saldo_salario;
	$to_rendimentos   = $saldo_de_salario + $terceiro_ss;

} else {
	
	$to_saldo_salario = 0;
	
}




// Aviso Prévio
if($aviso == 'indenizado' and $t_ap == 2) {
	
	$aviso_previo 		  = 'PAGO pelo funcionário';
	$aviso_previo_valor_d = $salario_base - $valor_insalubridade; // valor desconto
	
} elseif($aviso == 'indenizado' and $t_ap == 1) {
	
	$aviso_previo 		  = 'indenizado';
	
	///NOVA REGRA DO AVISO PRÉVIO
	$dt_demissao = mktime(0,0,0,$mes_demissao, $dia_demissao, $ano_demissao);	
	$dt_admissao = mktime(0,0,0,$mes_admissao, $dia_admissao, $ano_admissao);
	$diferenca_anos = ($dt_demissao - $dt_admissao)/31536000;
	
	for($d=1;$d <= (int)$diferenca_anos; $d++){	
		$valor_diario_3 += ($salario_base/30) * 3;	
	}
	

	
	$aviso_previo_valor_a = $salario_base + $valor_diario_3; // valor acréscimo padrão, sem cálculos
	

	
	
	
	if($dispensa == 63  or $dispensa == 66) {
		
		$pri = $data_demissao;
		$seg = date('Y-m-d', mktime(0,0,0,$mes_admissao,$dia_admissao + 45,$ano_admissao));
		
		if(date('Y-m-d') > $seg) {
			$seg = date('Y-m-d', mktime(0,0,0,$mes_admissao,$dia_admissao + 90,$ano_admissao));
		}
		
		// Verificando a quantidade de dias que faltam para terminar o Aviso Prévio
		// Ex: Foi demitido em 01/01/2009 e o fim dos 90 dias seria 10/01/2009. Então faltariam 9 dias.
		$re   = mysql_query("SELECT data FROM ano WHERE data > '$pri' AND data < '$seg'");
		$dias = mysql_num_rows($re);
		
		// Valor acréscimo
		$art_479 = ($salario_base / 30) * ($dias / 2);
		$aviso_previo_valor_a = 0;
		
	}

} elseif($aviso == 'trabalhado' and $t_ap == 1) {
	
	$dt_aviso = explode('-',$data_aviso);
	
	$aviso_previo = "trabalhado até ".date('d/m/Y', mktime(0,0,0,$dt_aviso[1],$dt_aviso[2] +29, $dt_aviso[0]));
	$aviso_previo_valor_a = $salario_base;
	
} elseif($t_ap == 0) {
	
	$aviso_previo = 'Não recebe';
	
}




$to_descontos   = $to_descontos + $aviso_previo_valor_d;
$to_rendimentos = $to_rendimentos + $aviso_previo_valor_a ;
$total_outros_descontos = $aviso_previo_valor_d + $devolucao;


	

// Fim Aviso Prévio



// Décimo Terceiro (DT)
$qr_verifica_13_folha = mysql_query("SELECT a.id_clt FROM rh_folha_proc a INNER JOIN rh_folha b ON a.id_folha = b.id_folha WHERE a.id_clt = $id_clt AND a.ano = '$ano_demissao' AND a.status = '3'");

$verifica_13_folha    = mysql_num_rows($qr_verifica_13_folha);

///Verifica se  a pesssoa recebeu décimo terceiro em novembro
if($t_13 == 1 and empty($verifica_13_folha)) {
	
	if(date('t', mktime(0,0,0,$mes_demissao,$dia_demissao,$ano_demissao)) != 31) {
		$dia_quinze = 15;
	} else {
		$dia_quinze = 16;
	}

	// 2009 == 2009
	if($ano_admissao == $ano_demissao) {
		
		// 12 == 12
		if($mes_demissao == $mes_admissao) {
			
			if($dia_demissao >= $dia_quinze) {
				$meses_ativo_dt = 1;
			} else {
				
				$meses_ativo_dt = 0;
			}
		
		// 11 != 12
		} else {
			
			if($dia_demissao >= $dia_quinze) {
				$meses_ativo_dt = $mes_demissao - $mes_admissao + 1; 
			} else {
				$meses_ativo_dt = $mes_demissao - $mes_admissao; 
			}
			
		}
	
	// 2009 != 2010
	} else {
		
		if($dia_demissao >= $dia_quinze) {
			$meses_ativo_dt = $mes_demissao;
		} else {
			$meses_ativo_dt = $mes_demissao - 1;
		}
		
	}
	
	$valor_td = ($salario_base / 12) * $meses_ativo_dt;
	
	
	
	$Calc -> MostraINSS($valor_td,$data_demissao);
	$previ_dt = $Calc -> valor;
	
	// Calculando INSS sobre DT
	$Calc -> MostraINSS($valor_td,$data_exp);
	$valor_td_inss = $Calc -> valor;
	
	// Calculando IRRF sobre DT
	$base_irrf_td = $valor_td - $valor_td_inss;
	$Calc -> MostraIRRF($base_irrf_td,$id_clt,$idprojeto,$data_demissao);
	$valor_td_irrf = $Calc -> valor;
	
	// Valor do DT
	$total_dt 		= $valor_td - $valor_td_inss - $valor_td_irrf;
	$to_descontos 	= $to_descontos + $valor_td_inss + $valor_td_irrf;
	$to_rendimentos = $to_rendimentos + $valor_td;
	

} else {
	
	$total_dt 	 = 0;
	$meses_ativo = 0;
	
}
// Fim de Décimo Terceiro (DT)





// Verificando Direito de Férias
$qr_verifica_ferias    = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' ORDER BY id_ferias DESC");
$verifica_ferias 	   = mysql_fetch_assoc($qr_verifica_ferias);
$total_verifica_ferias = mysql_num_rows($qr_verifica_ferias);

if(empty($total_verifica_ferias)) {
	
	$aquisitivo_ini = $data_entrada;
	$aquisitivo_end = date('Y-m-d', strtotime("".$data_entrada." +1 year"));
	
} else {
	
	$aquisitivo_ini = date('Y-m-d', strtotime("".$data_entrada." + ".$total_verifica_ferias." year"));
	$aquisitivo_end = date('Y-m-d', strtotime("".$data_entrada." + ".($total_verifica_ferias+1)." year"));
	
}

// Verificando Períodos Gozados
$qr_periodos = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$id_clt' ORDER BY id_ferias ASC");
while($periodos = mysql_fetch_assoc($qr_periodos)) {
	$periodos_gozados[] = "$periodos[data_aquisitivo_ini]/$periodos[data_aquisitivo_fim]";
}

// Verificando Períodos Aquisitivos, Períodos Vencidos e Período Proporcional
list($ano_data_entrada,$mes_data_entrada,$dia_data_entrada) = explode('-', $data_entrada);
$quantidade_anos = (date('Y') - $ano_data_entrada) + 1;

for($a=0; $a<$quantidade_anos; $a++) {
	
	$aquisitivo_inicio = date('Y-m-d', strtotime("$data_entrada +$a year"));
	$aquisitivo_final  = date('Y-m-d', mktime('0','0','0', $mes_data_entrada, $dia_data_entrada - 1, $ano_data_entrada + $a + 1));
	
	if($aquisitivo_final > $data_demissao) {
		
		$periodo_aquisitivo     = $aquisitivo_inicio.'/'.$data_demissao;
		$periodos_aquisitivos[] = $aquisitivo_inicio.'/'.$data_demissao;
		
	} else {
		
		$periodo_aquisitivo     = $aquisitivo_inicio.'/'.$aquisitivo_final;
		$periodos_aquisitivos[] = $aquisitivo_inicio.'/'.$aquisitivo_final;
		
	}
	
	if(@!in_array($periodo_aquisitivo, $periodos_gozados) and $aquisitivo_final < $data_demissao) {
		
		$periodos_vencidos[]    = $aquisitivo_inicio.'/'.$aquisitivo_final;
		
	} elseif($aquisitivo_final >= $data_demissao and $aquisitivo_inicio < $data_demissao) {
		
		$periodo_proporcional[] = $aquisitivo_inicio.'/'.$data_demissao;
		
	}

}

// Buscando Faltas
include('faltas_rescisao.php');

// Fim da Verificação de Férias



// Férias Vencidas
if($t_fv == 1) {
	
	//print_r($periodos_vencidos);
	
	$total_periodos_vencidos = count($periodos_vencidos);
	
	if(empty($total_periodos_vencidos)) {
		
		$ferias_vencidas = 'não';
		$fv_valor_base 	 = 0;
		$fv_um_terco	 = 0;
		
	} elseif($total_periodos_vencidos == 1) {
		
		$ferias_vencidas = 'sim';
		$fv_valor_base 	 = (($salario_base - $valor_insalubridade) / 30) * $qnt_dias_fv + $valor_insalubridade;
		$fv_um_terco	 = $fv_valor_base / 3;
		$fv_total 		 = $fv_valor_base + $fv_um_terco ;
		
	} elseif($total_periodos_vencidos > 1) {
		
		$ferias_vencidas = 'sim';
		$fv_valor_base 	 = ((($salario_base - $valor_insalubridade) / 30) * $qnt_dias_fv + $valor_insalubridade) * $total_periodos_vencidos;
		$fv_um_terco	 = $fv_valor_base / 3;
		$fv_total 		 = $fv_valor_base + $fv_um_terco;
		$multa_fv		 = ((($salario_base - $valor_insalubridade) / 30) * $qnt_dias_fv) * 2;
			
	}

} else {
	
	$fv_total = 0;

}
// Fim de Férias Vencidas



	
// Férias Proporcionais
if($t_fp == 1) {
	
	list($periodo_proporcional_inicio,$periodo_proporcional_final) 					 = explode('/',$periodo_proporcional[0]);
	list($ano_proporcional_inicio,$mes_proporcional_inicio,$dia_proporcional_inicio) = explode('-',$periodo_proporcional_inicio);
	list($ano_proporcional_final,$mes_proporcional_final,$dia_proporcional_final)    = explode('-',$periodo_proporcional_final);

	// 2010 == 2010
	if($ano_proporcional_inicio == $ano_proporcional_final) {
	    $meses_ativo_fp = $mes_proporcional_final - $mes_proporcional_inicio;
		
		
	// 2009 != 2010
	} else {
		$meses_ativo_fp = (12 - $mes_proporcional_inicio) + $mes_proporcional_final; 	
	}
	
	// Dia Quinze
	if(date('t', mktime(0,0,0,$mes_proporcional_final,$dia_proporcional_final,$ano_proporcional_final)) != 31) {
		$dia_quinze = 15;
	} else { 
		$dia_quinze = 16;
	}
	
	if($dia_proporcional_final >= $dia_quinze) {
		$meses_ativo_fp += 1;
	}
	
	
	if($aviso == 'indenizado' and $fator == 'empregador' and $meses_ativo_fp != 12) {
		$meses_ativo_fp += 1;
		
	}
	
	
	
	
	$fp_valor_mes 	= ($salario_base / 30) * $qnt_dias_fp;
	$fp_valor_total = ($fp_valor_mes  / 12) * $meses_ativo_fp;
	
	
	
	
	if($t_fa == 1) {
		
		$fp_um_terco = $fp_valor_total / 3;
		$fp_total 	 = $fp_valor_total + $fp_um_terco;
		
	} else {
		
		$fp_total = $fp_valor_total;
		
	}
	
} else {
	
	$fp_total = 0;

}
// Fim de Férias Proporcionais


// Cálculo de Férias
$ferias_total   = $fp_total + $fv_total;
$to_rendimentos = $to_rendimentos + $fv_valor_base + $fp_valor_total + $fp_um_terco + $fv_um_terco;
	


/* Calculando IRRF sobre Férias
$Calc       -> MostraIRRF($ferias_total, $id_clt, $idprojeto, date('Y-m-d'));
$ferias_irrf = $Calc -> valor; */

// Calculando INSS sobre Férias
$ferias_inss = 0;

$ferias_total_final = $ferias_total - $ferias_irrf;
$to_descontos 	    = $to_descontos + $ferias_irrf;

// Fim de Férias



// Atraso de Rescisão
$data_demissao_1      = date('Y-m-d', strtotime("$data_demissao +1 days"));
$data_aviso_previo_1  = date('Y-m-d', strtotime("$data_aviso +1 days"));
$data_aviso_previo_10 = date('Y-m-d', strtotime("$data_aviso +10 days"));


if($dispensa != '63' or $dispensa != '64' or $dispensa != '66') {
	if(($fator == 'empregador' and $aviso == 'trabalhado' and date('Y-m-d') > $data_aviso_previo_1)  or
	   ($fator == 'empregador' and $aviso == 'indenizado' and date('Y-m-d') > $data_aviso_previo_10) or
	   ($fator == 'empregado' and $aviso == 'trabalhado' and date('Y-m-d') > $data_demissao_1)  or
	   ($fator == 'empregado' and $aviso == 'indenizado' and date('Y-m-d') > $data_aviso_previo_10)) {
		$valor_atraso = $salario_base;
		//$valor_atraso = 0;
	}
}

//}



// Décimo Terceiro Saldo de Salário (Indenizado)
if($fator == 'empregador' and $aviso == 'indenizado') {
	$num_ss = 1;
	$terceiro_ss = $salario_base / 12;
} else {
	$num_ss = 0;
	$terceiro_ss = 0;
}



// Outros Lançamentos
$result_eventos = mysql_query("SELECT DISTINCT(descicao),cod,id_mov FROM rh_movimentos WHERE incidencia = 'FOLHA' AND cod != '7001' AND cod != '5022' AND cod != '5049' AND cod != '5021' AND cod != '5019'");

while($row_evento = mysql_fetch_array($result_eventos)) {
		
	$result_total_evento = mysql_query("SELECT SUM(valor_movimento) AS valor FROM rh_movimentos_clt WHERE id_mov = '$row_evento[id_mov]' AND mes_mov = '16' AND cod_movimento != '8080' AND status = '1' AND id_clt = '$id_clt'");
	$row_total_evento = mysql_fetch_array($result_total_evento);
	
	$debitos_tab 	 = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','5030','5031','5032');
	$rendimentos_tab = array('5011','5012','5022','6006','6007','9000','5022','5024');
	
	if(in_array($row_evento['cod'], $debitos_tab)) { 
		$debito     = $row_total_evento['valor'];
		$rendimento = NULL;
	} else {
		$debito     = NULL;
		$rendimento = $row_total_evento['valor'];
	}
	
	if($row_evento['cod'] == '5024') {
		$sal_familia_anterior = $row_total_evento['valor'];
	}
	
	// Somando Variáveis
	$re_tot_desconto   += $debito;
	$re_tot_rendimento += $rendimento;
	
	// Limpando Variáveis
	unset($desconto,$rendimento);
				
}



// Outros que não são utilizados ainda
$valor_comissao		= NULL;
$valor_grativicacao	= NULL;
$valor_outro		= NULL;
	


// Totalizadores
$total_outros    = $valor_sal_familia + $valor_adnoturno + $valor_insalubridade + $valor_atraso + $terceiro_ss + $re_tot_rendimento + $vale_refeicao;
$total_descontos = $to_descontos + $re_tot_desconto + $previ_ss + $previ_dt + $devolucao + $debito_vale_refeicao + $art_479;
$to_rendimentos  = $to_rendimentos + $total_outros + $hora_extra;

unset($re_tot_desconto,$re_tot_rendimento);



// FGTS 8%
if($t_f8 == 1) {
	$fgts8_total    = $fgts;
	$mensagem_fgts8 = 'Recebe';
} elseif($t_f8 == 2) {
	$fgts8_total = 0;
} elseif($t_f8 == 3) {
	$fgts8_total    = $fgts;
	$mensagem_fgts8 = 'Depositado';
}



// FGTS 40%
if($t_f4 == 1) {
	$fgts4_total = 0;
} else {
	$fgts4_total = 0;
}



// Totalizadores
$valor_rescisao_final = $to_rendimentos - $total_descontos;

if($valor_rescisao_final < 0) {
	$arredondamento_positivo = abs($valor_rescisao_final);
	$valor_rescisao_final 	 = NULL;
	$to_rendimentos 		 = $to_rendimentos + $arredondamento_positivo;
} else {
	$arredondamento_positivo = NULL;
	$valor_rescisao_final 	 = $to_rendimentos - $total_descontos;
}




// Formatando as Variáveis
$arredondamento_positivoF  = number_format($arredondamento_positivo,2,',','.'); 
$saldo_de_salarioF 		   = number_format($saldo_de_salario,2,',','.');
$inss_saldo_salarioF 	   = number_format($inss_saldo_salario,2,',','.');
$irrf_saldo_salarioF 	   = number_format($irrf_saldo_salario,2,',','.');
$terceiro_ssF              = number_format($terceiro_ss,2,',','.');
$base_irrf_saldo_salariosF = number_format($base_irrf_saldo_salarios,2,',','.');
$to_saldo_salarioF         = number_format($to_saldo_salario,2,',','.');

$valor_tdF                 = number_format($valor_td,2,',','.');
$valor_td_inssF            = number_format($valor_td_inss,2,',','.');
$valor_td_irrfF            = number_format($valor_td_irrf,2,',','.');
$base_irrf_tdF             = number_format($base_irrf_td,2,',','.');
$total_dtF                 = number_format($total_dt,2,',','.');

$fv_valor_baseF            = number_format($fv_valor_base,2,',','.');
$fv_um_tercoF              = number_format($fv_um_terco,2,',','.');
$fp_valor_totalF           = number_format($fp_valor_total,2,',','.');
$fp_um_tercoF              = number_format($fp_um_terco,2,',','.');
$ferias_totalF             = number_format($ferias_total,2,',','.');
$ferias_irrfF              = number_format($ferias_irrf,2,',','.');
$ferias_total_finalF       = number_format($ferias_total_final,2,',','.');

$valor_sal_familiaF        = number_format($valor_sal_familia,2,',','.');
$valor_adnoturnoF          = number_format($valor_adnoturno,2,',','.');
$valor_insalubridadeF      = number_format($valor_insalubridade,2,',','.');
$valor_atrasoF             = number_format($valor_atraso,2,',','.');
$valor_comissaoF           = number_format($valor_comissao,2,',','.');
$valor_grativicacaoF       = number_format($valor_grativicacao,2,',','.');
$hora_extraF               = number_format($hora_extra,2,',','.');
$valor_outroF              = number_format($valor_outro,2,',','.');

$aviso_previo_valor_dF     = number_format($aviso_previo_valor_d,2,',','.');
$outros_descontosF         = number_format($outros_descontos,2,',','.');
$aviso_previo_valor_aF     = number_format($aviso_previo_valor_a,2,',','.');

$fgts8_totalF              = number_format($fgts8_total,2,',','.');
$fgts4_totalF              = number_format($fgts4_total,2,',','.');

$total_outrosF             = number_format($total_outros,2,',','.');
$total_descontosF          = number_format($total_descontos,2,',','.');
$to_rendimentosF           = number_format($to_rendimentos,2,',','.');
$valor_rescisao_finalF     = number_format($valor_rescisao_final,2,',','.');
$devolucaoF                = number_format($devolucao,2,',','.');
$total_outros_descontosF   = number_format($total_outros_descontos,2,',','.');

// Formatando as Variáveis para Arquivo TXT
$saldo_de_salarioT 		   = number_format($saldo_de_salario,2,'.','');
$inss_saldo_salarioT 	   = number_format($inss_saldo_salario,2,'.','');
$irrf_saldo_salarioT 	   = number_format($irrf_saldo_salario,2,'.','');
$terceiro_ssT 			   = number_format($terceiro_ss,2,'.','');
$base_irrf_saldo_salariosT = number_format($base_irrf_saldo_salarios,2,'.','');
$to_saldo_salarioT 		   = number_format($to_saldo_salario,2,'.','');
$previ_ssT 				   = number_format($previ_ss,2,'.','');
$valor_sal_familia_totT    = number_format($valor_sal_familia + $sal_familia_anterior,2,'.','');

$valor_tdT 				   = number_format($valor_td,2,'.','');
$valor_td_inssT 		   = number_format($valor_td_inss,2,'.','');
$valor_td_irrfT 		   = number_format($valor_td_irrf,2,'.','');
$base_irrf_tdT             = number_format($base_irrf_td,2,'.','');
$total_dtT                 = number_format($total_dt,2,'.','');
$previ_dtT                 = number_format($previ_dt,2,'.','');

$fv_valor_baseT 		   = number_format($fv_valor_base,2,'.','');
$fv_um_tercoT 			   = number_format($fv_um_terco,2,'.','');
$fp_valor_totalT           = number_format($fp_valor_total,2,'.','');
$fp_um_tercoT              = number_format($fp_um_terco,2,'.','');
$ferias_totalT             = number_format($ferias_total,2,'.','');
$ferias_inssT              = number_format($ferias_inss,2,'.','');
$ferias_irrfT              = number_format($ferias_irrf,2,'.','');
$ferias_total_finalT       = number_format($ferias_total_final,2,'.','');

$valor_sal_familiaT 	   = number_format($valor_sal_familia,2,'.','');
$valor_adnoturnoT          = number_format($valor_adnoturno,2,'.','');
$valor_insalubridadeT      = number_format($valor_insalubridade,2,'.','');
$vale_refeicaoT            = number_format($vale_refeicao,2,'.','');
$debito_vale_refeicaoT     = number_format($debito_vale_refeicao,2,'.','');
$valor_atrasoT             = number_format($valor_atraso,2,'.','');
$valor_comissaoT           = number_format($valor_comissao,2,'.','');
$valor_grativicacaoT       = number_format($valor_grativicacao,2,'.','');
$hora_extraT               = number_format($hora_extra,2,'.','');
$valor_outroT              = number_format($valor_outro,2,'.','');

if($t_ap == '2') {
	$aviso_previo_valorT   = number_format($aviso_previo_valor_d,2,'.','');
} else {
	$aviso_previo_valorT   = number_format($aviso_previo_valor_a,2,'.','');
}

$fgts8_totalT 			   = number_format($fgts8_total,2,'.','');
$fgts4_totalT 			   = number_format($fgts4_total,2,'.','');
$total_outrosT             = number_format($total_outros,2,'.','');
$to_rendimentosT 		   = number_format($to_rendimentos,2,'.','');
$to_descontosT 			   = number_format($total_descontos,2,'.','');
$valor_rescisao_finalT 	   = number_format($valor_rescisao_final,2,'.','');
$UltSalF 				   = number_format($salario_base,2,',','.');
$UltSalT 				   = number_format($salario_base,2,'.','');
$devolucaoT 			   = number_format($devolucao,2,'.','');
//

if(!empty($array_codigos_rendimentos)) {
	foreach($array_codigos_rendimentos as $chave => $valor) {
		$a_rendimentos   .= $valor.',';
		$a_rendimentosva .= $array_valores_rendimentos[$chave].',';
	}
}

// Selecionando a Dispensa selecionada para gravar na tabela Eventos
$qr_eventos = mysql_query("SELECT * FROM rhstatus WHERE codigo = '$dispensa'");
$row_evento = mysql_fetch_array($qr_eventos);


 ?>
  <table cellpadding="0" cellspacing="0" class="tabela" border="0">
  <tr>
        <td colspan="8" class="show" align="center"><?=$id_clt.' - '.$nome?></td>
      </tr>
      <tr class="novo_tr">
        <td>Data de Admiss&atilde;o:</td>        
        <td>Data de Demiss&atilde;o:</td>  
        <td >Motivo do Afastamento:</td>       
        <td >Salario base de c&aacute;lculo:</td>    
        <td >Fator:</td>         
        <td>Aviso pr&eacute;vio:</td>
       <td>Rendimentos:</td>
       <td>Descontos:</td>
       
      </tr>
      
      <tr>
      	<td><?=$data_entradaF?></td>
        <td><?=$data_demissaoF?></td>
        <td><?=@mysql_result($restatus,0)?></td>
        <td>R$ <?=number_format(($salario_base - $total_rendi),2,',','.')?></td>  
        <td><?=$fator?></td>       
        <td><?=$aviso_previo?></td>
        <td>R$ <?=$to_rendimentosF?></td>
        <td>R$ <?=number_format($total_descontos,2,',','.')?></td>
      </tr>
      
      <tr>
      		<td align="right" colspan="8">  <strong>Total a ser pago:  R$ <?=$valor_rescisao_finalF?><br />
            <?php if(!empty($arredondamento_positivo)) {
                      echo 'Arredondamento Positivo: '.$arredondamento_positivoF.'';
                  } ?></strong>
          </td>
      </tr>      
   </table>
      
      
      
  <table cellpadding="0" cellspacing="0" class="tabela" border="0">    
     <tr>
           <td colspan="5" class="divisor">Sal&aacute;rios</td> 
        </tr>
        <tr class="novo_tr">
            <td>Saldo de sal&aacute;rio (<?=$dias_trabalhados?>/30):</td>
            <td>INSS sobre salários:</td>
            <td>IRRF sobre salários:</td>
            <td>Previd&ecirc;ncia:</td>
            
        </tr>   
        
        <tr>
             <td>R$ <?=$saldo_de_salarioF?> <?php if(!empty($faltas)) { echo '('.$faltas.' faltas)'; } ?></td>
             <td>R$ <?=$inss_saldo_salarioF?></td>
             <td>R$ <?=$irrf_saldo_salarioF?></td>
             <td>R$ <?=number_format($previ_ss,2,',','.')?></td>
            
        </tr>
        <tr>
        	<td colspan="4" align="right"><strong>Total: R$ <?=number_format(($saldo_de_salario-$inss_saldo_salario-$irrf_saldo_salario-$previ_ss),2,',','.')?></strong>
            </td>
        </tr>
        
          </table>
          
          
       <table cellpadding="0" cellspacing="0" class="tabela" border="0">   
         <tr> 
            <td colspan="4" class="divisor">Décimo terceiro</td>
      	</tr>
          
     	<tr class="novo_tr">
            <td>Décimo terceiro proporcional (<?=$meses_ativo_dt?>/12):</td>            
            <td>INSS:</td>    
            <td>IRRF:</td>  
            <td>Previd&ecirc;ncia</td>   
           
            
          </tr>
          
          <tr>
          	<td>R$ <?=$valor_tdF?></td>
            <td>R$ <?=$valor_td_inssF?></td>
            <td>R$ <?=$valor_td_irrfF?></td>
            <td>R$ <?=number_format($previ_dt,2,',','.')?></td>
          
          </tr>
          
          <tr>
          	 <td colspan="4" align="right"><strong>Total: R$ <?=number_format(($valor_td-$valor_td_inss-$valor_td_irrf-$previ_dt),2,',','.')?></strong></td>
          </tr>
          
      </table>
      
      <table cellpadding="0" cellspacing="0" class="tabela" border="0">   
        <tr>
          <td colspan="7" align="left"><div class="divisor">Férias</div></td>
        </tr>
        
        <tr  class="novo_tr">
         <td>Férias vencidas:</td>
         <td>1/3 sobre férias vencidas:</td>
         <td >Férias proporcionais (<?=$meses_ativo_fp?>/12): </td>
         <td>1/3 sobre férias proporcionais:</td>
         <td >INSS sobre férias:</td>
         <td>IRRF sobre férias:</td>
         
        </tr>
        
        <tr>
         <td>R$ <?=$fv_valor_baseF?></td>
         <td>R$ <?=$fv_um_tercoF?></td>
         <td>R$ <?=$fp_valor_totalF?></td>
         <td>R$ <?=$fp_um_tercoF?></td>
         <td>R$ 0,00</td>
         <td>R$ <?=$ferias_irrfF?></td>         
        </tr>
      	
        <tr>
       <td colspan="7" align="right"><strong>Total: R$ <?=number_format(($fv_valor_base+$fv_um_terco+$fp_valor_total+$fp_um_terco-$ferias_irrf),2,',','.')?></strong>
       </td>
        </tr>
        
        
        
        </table>
        
     <table cellpadding="0" cellspacing="0" class="tabela" border="0">   
       <tr>
        <td colspan="7" class="divisor">Outros vencimentos</td>
      </tr>
      
      <tr class="novo_tr">
      	 <td>Sal&aacute;rio familia:</td>
         <td>Adicional noturno:</td>
         <td>Comiss&otilde;es: </td>
         <td>Gratifica&ccedil;&otilde;es:</td>
         <td>Horas extras:</td>
         <td>Insalubridade / Periculosidade:</td>
         <td>Atraso de Rescis&atilde;o (477):</td>
        
               
      </tr>
      
      <tr>
      	<td>R$ <?=$valor_sal_familiaF?></td>
        <td>R$ <?=$valor_adnoturnoF?></td>
        <td>R$ <?=$valor_comissaoF?></td>
        <td>R$ <?=$valor_grativicacaoF?></td>
        <td>R$ <?=$hora_extraF?></td>
        <td>R$ <?=$valor_insalubridadeF?></td>
        <td>R$ <?=$valor_atrasoF; unset ($valor_atrasoF);?></td>
       
      </tr>
      
      <tr class="novo_tr">
      	 <td>Outros:</td>
         <td>Aviso Prévio:</td>
         <td>13&ordm; Saldo Indenizado (<?=$num_ss?>/12):</span></td>
         <td>F&eacute;rias em Dobro:</td>
         <td>Vale Refei&ccedil;&atilde;o:</td>        
          <td colspan="2">D&eacute;bito de Vale Refei&ccedil;&atilde;o:</td> 
      </tr>
      	    
      <tr>
      	 <td>R$ <?=$valor_outroF?></td>
        <td>R$ <?=$aviso_previo_valor_aF?></td>
        <td>R$ <?=$terceiro_ssF?></td>
        <td>R$ <?=number_format($multa_fv,2,',','.')?></td>
        <td>R$ <?=number_format($vale_refeicao,2,',','.')?></td>
        <td colspan="2">R$ <?=number_format($debito_vale_refeicao,2,',','.')?></td>
      </tr>
      
      
      
      
      <tr>
      <td colspan="13">
                                  
                <?php $cont = 0;
                      $result_eventos = mysql_query("SELECT DISTINCT(descicao),cod,id_mov FROM rh_movimentos WHERE incidencia = 'FOLHA' AND cod != '7001' AND cod != '5022' AND cod != '5049' AND cod != '5021' AND cod != '5019'");
                      while($row_evento = mysql_fetch_array($result_eventos)) {
                    
                        if($cont % 2) { 
                            $color = 'corfundo_dois'; 
                        } else { 
                            $color = 'corfundo_um'; 
                        }
                        
                        $marg = "<div style='margin-right:5;'>";
                        
                        $result_total_evento = mysql_query("SELECT SUM(valor_movimento) AS valor FROM rh_movimentos_clt WHERE id_mov = '$row_evento[id_mov]' AND mes_mov = '16' AND cod_movimento != '8080' AND status = '1' AND id_clt = '$id_clt'");
                        $row_total_evento 	 = mysql_fetch_array($result_total_evento);
                        $total_evento        = mysql_num_rows($result_total_evento);
                        
                        if(!empty($total_evento)) {
                        
                            $debitos_tab = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','5030','5031','5032');
                            $rendimentos_tab = array('5011','5012','5022','6006','6007','9000','5022','5024');
                            
                            if(in_array($row_evento['cod'], $debitos_tab)) {
                                
                                $debito     = $row_total_evento['valor'];
                                $rendimento = '';
                                
                            } else {
                                
                                $debito     = '';
                                $rendimento = $row_total_evento['valor'];
                                
                            }
                            
                            if($rendimento == 0 and $debito == 0) {
                                
                                $disable = "style='display:none'";
                                
                            } else {
                                
                                $disable = "style='display:'";
                                
                            } 
                            
                            if($cont == 0 and ($rendimento != 0 or $debito != 0)) { $cont++; ?>
                   
                        
                    <table width="95%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td colspan="4" align="center" class="show">Outros Eventos</td>
                      </tr>
                      <tr class="novo_tr_dois">
                        <td width="35%">Evento</td>
                        <td width="35%">Descri&ccedil;&atilde;o</td>
                        <td width="15%">Rendimentos</td>
                        <td width="15%">Descontos</td>
                      </tr>  
                      
                      <?php } ?>
                        
                      <tr class="novalinha <?=$color?>" <?=$disable?>>
                        <td align="center"><?=$row_evento['cod']?></td>
                        <td align="left"><?=$row_evento['descicao']?></td>
                        <td align="right"><b><?php if(!empty($rendimento)) { echo number_format($rendimento,2,',','.'); } ?></b></td>
                        <td align="right"><b><?php if(!empty($debito)) { echo number_format($debito,2,',','.'); } ?></b></td>
                      </tr>
                        
                <?php // Somando Variáveis
                      $re_tot_desconto   += $debito;
                      $re_tot_rendimento += $rendimento;
                        
                      } else {
                        
                          $re_tot_desconto   = 0;
                          $re_tot_rendimento = 0;
                        
                      }
                
                      unset($desconto,$rendimento);
                    
                      }
                    
                    // Formatando Totais por Evento
                    $re_tot_rendimentoF = number_format($re_tot_rendimento,2,',','.');
                    $re_tot_descontoF   = number_format($re_tot_desconto,2,',','.');
                    
                    if($re_tot_rendimento != 0 or $re_tot_desconto != 0) {  ?>
                    
                          <tr class="novo_tr_dois">
                            <td colspan="2" align="right">TOTAIS</td>
                            <td><?=$re_tot_rendimentoF?></td>
                            <td><?=$re_tot_descontoF?></td>
                          </tr>
                        </table>
            <?php } ?>
        </td> 
      </tr>
      <tr>
       <td colspan="7"  align="right"><strong>Total: 
            R$ <?=number_format($valor_sal_familia+$valor_adnoturno+$valor_comissao+$valor_grativicacao+$hora_extra+$valor_insalubridade+$valor_atraso + $valor_outro+$aviso_previo_valor_a+$terceiro_ss+$re_tot_rendimento-$re_tot_desconto+$vale_refeicao-$debito_vale_refeicao,2,',','.')?></strong>
        </td>
      </tr>
      
      </table>
      
      
      
      <table cellpadding="0" cellspacing="0" class="tabela" border="0">   
      <tr>
        <td colspan="3"><div class="divisor">Outros descontos</div></td>
      </tr>
      
      <tr class="novo_tr">
      	 <td>Aviso Prévio pago pelo Funcion&aacute;rio:</td>
         <td>Devolu&ccedil;&atilde;o:</td>
         <td>Indeniza&ccedil;&atilde;o Artigo 479:</td>
       </tr>
      
      <tr>
      	 <td>R$ <?=$aviso_previo_valor_dF?></td>
         <td>R$ <?=$devolucaoF?></td>
         <td>R$ <?=number_format($art_479,2,',','.')?></td>
         
      </tr>
      <tr>
      	<td colspan="3" align="right"><strong>Total:
            R$ <?=$total_outros_descontosF?></strong>
        </td>
      </tr>
      </table>
      
      <table cellpadding="0" cellspacing="0" class="tabela" border="0">   
      
       <tr>
        <td colspan="13"><div class="divisor">FGTS</div></td>
      </tr>
        <tr class="novo_tr">
        	 <td>FGTS 8%:</td>
             <td>FGTS 40%:</td>
             <td colspan="11">Código de Saque:</td>
        </tr>
        
        <tr>
        	 <td>R$ <?=$fgts8_totalF?> (<?=$mensagem_fgts8?>)</td>
             <td>R$ <?=$fgts4_totalF?></td>
             <td colspan="11"><?=sprintf('%02d',$cod_saque_fgts)?></td>
        </tr>
      
      
  </table>
  
  <table>
  <tr>
  	<td>&nbsp;</td>
  </tr>
   <tr>
  	<td>&nbsp;</td>
  </tr>
   <tr>
  	<td>&nbsp;</td>
  </tr>
  </table>
  
<?php

	unset($salario_calc_inss, $salario_calc_IR, $salario_calc_FGTS,$total_rendi, $to_rendimentos,  $valor_salario_dia, $dias_trabalhados, $valor_faltas, $saldo_de_salario, $terceiro_ss, $aviso_previo_valor_a,$fv_valor_base, $fp_valor_total, $fp_um_terco, $fv_um_terco, $valor_insalubridade, $salario_base, $salario_base_limpo, $meses_ativo_fp, $t_fp, $ano_proporcional_inicio,$mes_proporcional_inicio,$dia_proporcional_inicio, $ano_proporcional_final,$mes_proporcional_final,$dia_proporcional_final, $periodo_proporcional_inicio,$periodo_proporcional_final,  $dia_quinze, $periodo_aquisitivo, $periodos_gozados, $aquisitivo_final, $data_demissao,  $aquisitivo_inicio, $data_entrada, $periodo_proporcional );
endwhile;
?>
</div>
</body>
</html>