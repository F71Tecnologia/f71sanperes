<?php
set_time_limit(120);

if(empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/calculos.php";
include "../../classes/regiao.php";

// Definindo Classes
$Calc = new calculos();
$Regi = new regiao();

// Recebendo a Variável Criptografada
$enc = $_REQUEST['enc'];
$enc = str_replace('--', '+', $enc);
$enc = decrypt($enc);
$enc = explode('&', $enc);
$regiao = $enc[0];
$folha = $enc[1];
//-----------------------------------

$id_user = $_COOKIE['logado'];

$sql = "SELECT * FROM funcionario WHERE id_funcionario = '$id_user'";
$result_user = mysql_query($sql);
$row_user = mysql_fetch_array($result_user);

$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

$result_folha = mysql_query("SELECT *, date_format(data_proc, '%d/%m/%Y') AS data_proc2, date_format(data_inicio, '%d/%m/%Y')as data_inicio2, date_format(data_fim, '%d/%m/%Y')as data_fim2 FROM rh_folha WHERE id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

// Selecionando os CLTs já cadastrados na tabela FOLHA_PROC que estejam com STATUS = 2 selecionado anteriormente
$result_folha_pro = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '2' ORDER BY nome ASC");
$num_clt_pro = mysql_num_rows($result_folha_pro);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesFolhaTab = $row_folha['mes'];
$MesFolhaTab = (int)$MesFolhaTab;
$mes_da_folha = $meses[$MesFolhaTab];

$ano = date("Y");
$mes = date("m");
$dia = date("d");
$data = date("d/m/Y");
$data_menor21 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 21));

$result_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos WHERE cod != '0001' ORDER BY cod");

while($row_codigos = mysql_fetch_array($result_codigos)) {
	$ar_codigos[] = $row_codigos['0'];
}

if($row_folha['terceiro'] == 1) {
	switch ($row_folha['tipo_terceiro']) {
		case 1:
		$exib =  "13º Primeira parcela";
		break;
		case 2:
		$exib = "13º Segunda parcela";
		break;
		case 3:
		$exib = "13º Integral";
		break;
	}
} else {
	$exib = "$mes_da_folha / $row_folha[ano]";
}

$titulo = "Folha Sint&eacute;tica: $row_projeto[nome] ($exib)";

// Definindo Usuários para Finalizar a Folha
$acesso_finalizacao = array('9','33');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>
<body>						 
<table width="95%" border="0" align="center" bgcolor="#FFFFFF">
  <tr>
    <td align="center" valign="middle">
    <div style="font-size:9px; color:#eee; font-weight:bold;">
    	ID: 
		<?php echo $folha.", região: ";
              $Regi -> MostraRegiao($row_folha['regiao']);
              echo $Regi -> regiao;
              echo " CLT"; ?>
     </div>
        
    <table width="98%" border="0" align="center">
      <tr>
        <td height="115" colspan="3" align="center" valign="middle" class="show">
        <br>
        <img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" alt="" width="110" height="79">
             <br />
          <br />
          <?=$titulo?>
        <br />
          <br />
        </td>
      </tr>
      <tr class="linha">
        <td width="29%" height="29" align="center" valign="middle" bgcolor="#E2E2E2">
        	Data de Processamento: <?=$row_folha['data_proc2']?>
        </td>
        <td width="43%" height="29" align="center" valign="middle" bgcolor="#E2E2E2">
        	CNPJ: <?=$row_master['cnpj']?>
        </td>
        <td width="28%" height="29" align="center" valign="middle" bgcolor="#E2E2E2">
			De <?=$row_folha['data_inicio2']?> até <?=$row_folha['data_fim2']?>
        </td>
      </tr>
    </table>
    
     <br>
     
      <table width="98%" border="0" align="center" cellpadding="4" cellspacing="0" style="line-height:26px;">
        <tr bgcolor="#BBBBBB" height="35">
          <td width="9%" align="left" class="style23">Cod</td>
          <td width="27%" align="left" class="style23">Nome</td>
          <td width="2%" align="center" class="style23">Dias</td>
          <td width="8%" align="center" class="style23">Sal&aacute;rio</td>
          <td width="7%" align="center" class="style23">Rendimen.</td>
          <td width="7%" align="center" class="style23">Descontos</td>
          <td width="7%" align="center" class="style23">Sal Base</td>
          <td width="6%" align="center" class="style23">INSS</td>
          <td width="6%" align="center" class="style23">IRRF</td>
          <td width="5%" align="center" class="style23">Faltas</td>
          <td width="8%" align="center" class="style23">Sal Fam&iacute;lia</td>
          <td width="9%" align="center" class="style23">Sal L&iacute;quido</td>
        </tr>
  
       
       
<?php $cont = "0";
	  while($row_cltFolha = mysql_fetch_array($result_folha_pro)) {
		  
		  $result_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$row_cltFolha[id_clt]' ORDER BY nome ASC");
		  $row_clt = mysql_fetch_array($result_clt);
		  
		  $array_ids_totalizadores[] = $row_clt[0];
			  
		  $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);
		  
		  
		  
          
		  // Definindo variáveis importantes para cálculos INSS,IR,FGTS
		  $salario_base 		= $row_curso['salario'];
		  $salario_base_limpo 	= $row_curso['salario'];
		  $salario_bruto 		= $row_curso['salario'];
		  $salario_calc_inss 	= $salario_bruto;
		  $salario_calc_IR 		= $salario_bruto;
		  $salario_calc_FGTS 	= $salario_bruto;

          


		  // Pegando todos os lançamentos como SEMPRE para incrementar no SALÁRIO BASE
		  $result_sempre = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$row_clt[0]' AND status = '1' AND id_mov != '94' AND lancamento = '2'");
		  while($row_sempre = mysql_fetch_array($result_sempre)) {
			  
			  $cred_exp = explode(",",$row_sempre['incidencia']);
			  $cont_cred_exp = count($cred_exp);
			  
			  // Acrescenta os valores marcados como SEMPRE na BASE DE INSS,IRRF,FGTS
			  for($i=0; $i <= $cont_cred_exp; $i++) {
				  
				  if($cred_exp[$i] == 5020) {
					  if($row_sempre['tipo_movimento'] == "CREDITO") {
						  $salario_calc_inss = $salario_calc_inss + $row_sempre['valor_movimento'];
					  } else {
						  $salario_calc_inss = $salario_calc_inss - $row_sempre['valor_movimento'];
					  }
				  }
				  
				  if($cred_exp[$i] == 5021) {
					  if($row_sempre['tipo_movimento'] == "CREDITO") {
						  $salario_calc_IR = $salario_calc_IR + $row_sempre['valor_movimento'];
					  } else {
						  $salario_calc_IR = $salario_calc_IR - $row_sempre['valor_movimento'];
					  }
				  }
				  
				  if($cred_exp[$i] == 5023) {
					  if($row_sempre['tipo_movimento'] == "CREDITO") {
						  $salario_calc_FGTS = $salario_calc_FGTS + $row_sempre['valor_movimento'];
					  } else {
						  $salario_calc_FGTS = $salario_calc_FGTS - $row_sempre['valor_movimento'];
			  		  }
				  }
				  
			  }
			  //
		  
			  // SALÁRIO BASE + Todos os movimentos marcados como SEMPRE
			  if($row_sempre['tipo_movimento'] == "CREDITO") {
				  if($row_sempre['cod_movimento'] != '8006') {
				  	$salario_base = $salario_base + $row_sempre['valor_movimento'];
					$total_rendi = $total_rendi + $row_sempre['valor_movimento'];
				    $AR_rendimentos[] = $row_sempre['cod_movimento'];
				    $AR_rendimentosva[] = $row_sempre['valor_movimento'];
				  }
			  } else {
				  $salario_base = $salario_base - $row_sempre['valor_movimento'];
				  $total_debito = $total_debito + $row_sempre['valor_movimento'];
				  $AR_descontos[] = $row_sempre['cod_movimento'];
				  $AR_descontosva[] = $row_sempre['valor_movimento'];
			  }
			  //
		  
		  unset($cred_exp);
		  unset($cont_cred_exp);
		  
		  }
		  // Fim dos lançamentos marcados como SEMPRE

		
          
	
		  // Verificando se o CLT entrou depois do dia 1° do Mês
		  $d_trabalhando = $row_cltFolha['dias_trab'];
		  
		  // Consulta de FÉRIAS
		  $qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_clt[id_clt]' AND mes = '$row_folha[mes]' AND ano = '$row_folha[ano]' AND status = '1'");
		  $row_ferias = mysql_fetch_assoc($qr_ferias);
		  $ferias = mysql_num_rows($qr_ferias);
		  
		  if(!empty($ferias)) {
			  $ferias_inicio = explode("-", $row_ferias['data_ini']);
		 	  $ferias_fim = explode("-", $row_ferias['data_fim']);
		 	  $folha_inicio = explode('-', $row_folha['data_inicio']);
		  	  $folha_fim = explode('-', $row_folha['data_fim']);
			  
		  	  $dif_1 = (int)((mktime(0,0,0,$ferias_inicio[1],$ferias_inicio[2],$ferias_inicio[0]) - mktime(0,0,0,$folha_inicio[1],$folha_inicio[2],$folha_inicio[0]))/86400);

			  if($row_ferias['data_fim'] < $row_folha['data_fim']) {
		  	  	  $dif_2 = (int)((mktime(0,0,0,$ferias_fim[1],$ferias_fim[2],$ferias_fim[0]) - mktime(0,0,0,$folha_fim[1],$folha_fim[2],$folha_fim[0]))/86400);
			  }
			  
			  $dif = $dif_1 + abs($dif_2) + 1; // Jr. 09/04/2010 (+ 1 dia)
			  $d_trabalhando = $dif;
			  unset($dif);
		  }
		  //
		  
		  // Consulta de EVENTOS (Licensa Médica)
		  $qr_eventos = mysql_query("SELECT * FROM rh_eventos WHERE cod_status = '20' AND id_clt = '$row_clt[id_clt]'");
		  $eventos = mysql_fetch_assoc($qr_eventos);
		  $numero_eventos = mysql_num_rows($qr_eventos);
		  
		  if(!empty($numero_eventos) and $regiao == '28') {
			  $data_evento = explode("-",$eventos['data']);
			  $dif = (int)((mktime(0,0,0,$data_evento[1],$data_evento[2],$data_evento[0]) - mktime(0,0,0,'03','01','2010'))
			  /86400);
			  $d_trabalhando += abs($dif);
		  }
		  
		  unset($numero_eventos);
		  //
		  
		  if(!empty($d_trabalhando) or (!empty($ferias) and empty($d_trabalhando))) {
			  $salario_dia = $salario_base / 30;
			  $salario_base = round($salario_dia * $d_trabalhando,2);
			  
			  $salario_calc_inss 	= $salario_base;
		  	  $salario_calc_IR 		= $salario_base;
		  	  $salario_calc_FGTS 	= $salario_base;
			  unset($salario_dia);
		  }
		  //

		  
		  
		  
		  // Calculando FALTAS (8000)
		  $result_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$row_clt[0]' AND id_mov = '62' AND status = '1' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]'");
		  $row_faltas = mysql_fetch_array($result_faltas);
		  $num_faltas = mysql_num_rows($result_faltas);
		  
		  if($num_faltas == 1) {
			  
			  $valor_faltas = $row_faltas['valor_movimento'];
			  $num_faltas = $row_faltas['qnt'];
			  
			  // Diminuindo o Valor da Falta nos cálculos de INSS e FGTS
			  $salario_calc_inss 	= $salario_calc_inss - $row_faltas['valor_movimento'];
			  $salario_calc_IR 		= $salario_calc_IR - $row_faltas['valor_movimento'];
			  //
			  
			  $texto_movimentos .= "UPDATE rh_movimentos_clt SET status = '5', id_folha = '$row_folha[0]' WHERE id_movimento = '$row_faltas[0]';\r\n";
			  
		  } elseif($num_faltas >= 2) {
			  
			  $mensagem = "<script type=\"text/javascript\">
			  alert('..............ATENÇÃO..............\\n\\n EXISTE $num_faltas FALTAS CADASTRADAS PARA O FUNCIONÁRIO $row_clt[nome] !
				FAVOR ACERTAR SÓ PODE HAVER 1 FALTA CADASTRADA COM A QUANTIDADE DE FALTAS NO MES');</script>
			  <font color=red size=4>Atenção, esta folha pode conter erro de calculo devido a cadastro errado de faltas no 
			  CLT $row_clt[nome]. </font>";
		
		  }
		  
		  // Diminuindo o Valor da Falta no Salário Base para Cálculos
		  $salario_base = $salario_base - $valor_faltas;
		  $salario_base_resumo_evento = $salario_base + $valor_faltas;
		  $salario_base_resumo_evento_to = $salario_base_resumo_evento_to + $salario_base_resumo_evento;
		  
		  //$pre_liquido_menos_faltas = $row_curso['salario'] - $valor_faltas;
		  //$liquido_menos_faltas += $pre_liquido_mais_faltas;
		  //
		  
		  
		  
		  
		  // Criando a Variável dos Dias Trabalhados
		  if($num_faltas == 1) {
			  $d_trabalhando -= $row_faltas['qnt'];
		  }
		  //
		  
		  
		  
		  
		  // DÉCIMO TERCEIRO SALÁRIO (DT)
		  if($row_folha['terceiro'] == 1) {
			  
			  if($row_folha['tipo_terceiro'] == 1) {
				  $mes_mov = 13;
			  } elseif($row_folha['tipo_terceiro'] == 2) {
				  $mes_mov = 14;
			  } else {
				  $mes_mov = 15;
			  }

			  
			  // Calculando os Rendimentos de DT
			  $result_creditos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'CREDITO' AND id_clt = '$row_clt[0]' AND status = '1' AND (lancamento = '1' AND mes_mov = '$mes_mov' AND ano_mov = '$row_folha[ano]')");
			  $cont_creditos = mysql_num_rows($result_creditos);
				  
			  while($row_creditos = mysql_fetch_array($result_creditos)) {
				  
					$cred_exp = explode(",",$row_creditos['incidencia']);
					$cont_cred_exp = count($cred_exp);
					  
					for($i=0; $i<=$cont_cred_exp; $i++) {
						
						if($cred_exp[$i] == 5020) {
							$salario_calc_inss = $salario_calc_inss + $row_creditos['valor_movimento'];
						}
						if($cred_exp[$i] == 5021) {
							$salario_calc_IR = $salario_calc_IR + $row_creditos['valor_movimento'];
						}
						if($cred_exp[$i] == 5023) {
							$salario_calc_FGTS = $salario_calc_FGTS + $row_creditos['valor_movimento'];
						}
						
					}
				  
					$valor_dt_rend = $valor_dt_rend + $row_creditos['valor_movimento'];
					$AR_rendimentos[] = $row_creditos['cod_movimento'];
					$AR_rendimentosva[] = $row_creditos['valor_movimento'];
				  
					$texto_movimentos .= "UPDATE rh_movimentos_clt SET status = '5', id_folha = '$row_folha[0]' WHERE id_movimento = '$row_creditos[0]';\r\n";
		
			  }
			  
			  if(empty($valor_dt_rend)) {
			  		$valor_dt_rend = 0;
			  }
			  // Fim de Rendimentos DT

			  // Calculando os Descontos de DT
			  $result_debito = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'DEBITO' AND id_clt = '$row_clt[0]' AND status = '1' AND id_mov != '62' AND (lancamento = '1' AND mes_mov = '$mes_mov' AND ano_mov = '$row_folha[ano]')");
			  $cont_debito = mysql_num_rows($result_debito);
	
			  while($row_debito = mysql_fetch_array($result_debito)) {
				  
				  $debt_exp = explode(",",$row_debito['incidencia']);
				  $cont_debt_exp = count($debt_exp);
				  
				  for($i=0; $i<=$cont_debt_exp; $i++) {
					  
					  if($debt_exp[$i] == 5020) {
						  $salario_calc_inss = $salario_calc_inss - $row_debito['valor_movimento'];
					  }
					  
					  if($debt_exp[$i] == 5021) {
						  $salario_calc_IR = $salario_calc_IR - $row_debito['valor_movimento'];
					  }
					  
					  if($debt_exp[$i] == 5023) {
						  $salario_calc_FGTS = $salario_calc_FGTS - $row_debito['valor_movimento'];
					  }
					  
				  }
				  
				  $valor_dt_debito = $valor_dt_debito + $row_debito['valor_movimento'];
				  $AR_descontos[] = $row_debito['cod_movimento'];
				  $AR_descontosva[] = $row_debito['valor_movimento'];

				  $texto_movimentos .= "UPDATE rh_movimentos_clt SET status = '5', id_folha = '$row_folha[0]' WHERE id_movimento = '$row_debito[0]';\r\n";
			  
			  }
			  
			  if(empty($valor_dt_debito)) {
			  		$valor_dt_debito = 0;
			  }
			  // Fim de Descontos DT

			  $Calc -> dt_data($row_folha['tipo_terceiro'],$row_clt['data_entrada'],$row_folha['ano'],$row_folha['mes'],$salario_base_limpo,$row_clt[0],$valor_dt_rend);
			  $valor_dt = $Calc -> valor;
			  $desconto_dt = $Calc -> desconto_dt;
			  $desconto_dt += $valor_dt_debito;
			  unset($valor_dt_debito);
				  
			  if($row_folha['tipo_terceiro'] == 2 or $row_folha['tipo_terceiro'] == 3) {
			
				  $total_debito = $total_debito + $desconto_dt;
				  
				  // Calculando INSS sobre DT
				  $Calc -> MostraINSS($valor_dt,$row_folha['data_inicio2']);
				  $valor_inss_dt = $Calc -> valor;
				  
				  // Acrescentando o Código e o Valor para serem exibidas no totalizador (Fim da Página)
				  $AR_rendimentos[] = "5031";
				  $AR_rendimentosva[] = $valor_inss_dt;
				
				  // Calculando IRRF sobre DT
				  $base_irrf_dt = $valor_dt - $valor_inss_dt;
				  $Calc -> MostraIRRF($base_irrf_dt,$row_clt[0],$row_clt['id_projeto'],$row_folha['data_inicio2']);
				  $valor_irrf_dt = $Calc -> valor;
				  
				  // Acrescentando o Código e o Valor para serem exibidas no totalizador (Fim da Página)
				  $AR_rendimentos[] = "5030";
				  $AR_rendimentosva[] = $valor_irrf_dt;
				  //
				  
				  unset($base_irrf_dt);
			  
		      }
		  
		      if($row_folha['tipo_terceiro'] != 3){
				  
			  		// Resetando variáveis importantes para cálculos INSS,IR,FGTS
					$salario_bruto 		= 0;
					$salario_base 		= 0;
					$salario_calc_inss 	= $salario_base;
					$salario_calc_IR 	= $salario_base;
					$salario_calc_FGTS 	= $salario_base;
		  	  		//
					
		      }

		  } else { // Se a folha não for DT resetamos todas as variáveis DT
		  
			  $valor_inss_dt = 0;
			  $valor_irrf_dt = 0;
			  $valor_dt 	 = 0;
		  
		  }

		  // Fim do DÉCIMO TERCEIRO SALÁRIO (DT)
		  
			


		  // Calculando os RENDIMENTOS 
		  
		  $result_creditos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'CREDITO' AND id_clt = '$row_clt[0]' AND status = '1' AND lancamento = '1' AND id_mov != '94' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]'");
		  $cont_creditos = mysql_num_rows($result_creditos);
		  
		  while($row_creditos = mysql_fetch_array($result_creditos)) {

		  	  $cred_exp = explode(",",$row_creditos['incidencia']);
			  $cont_cred_exp = count($cred_exp);
			  
			  // INSS,IRRF,FGTS == 5020,5021,5023
			  for($i=0; $i<=$cont_cred_exp; $i++) {
				  
				  if($cred_exp[$i] == 5020) {
					  $salario_calc_inss = $salario_calc_inss + $row_creditos['valor_movimento'];
				  }
				  
				  if($cred_exp[$i] == 5021) {
					  $salario_calc_IR = $salario_calc_IR + $row_creditos['valor_movimento'];
				  }
				  
				  if($cred_exp[$i] == 5023) {
					  $salario_calc_FGTS = $salario_calc_FGTS + $row_creditos['valor_movimento'];
				  }
				  
			  }
		      
			  // SALÁRIO BASE + Todos os movimentos
			  if($row_creditos['cod_movimento'] != '8006') {
			  	$salario_base = $salario_base + $row_creditos['valor_movimento'];
				$total_rendi = $total_rendi + $row_creditos['valor_movimento'];
			    $AR_rendimentos[] = $row_creditos['cod_movimento'];
			    $AR_rendimentosva[] = $row_creditos['valor_movimento'];
			  }
			  //
		  
		  	  $texto_movimentos .= "UPDATE rh_movimentos_clt SET status = '5', id_folha = '$row_folha[0]' WHERE id_movimento = '$row_creditos[0]';\r\n";

		  } 
		  // Fim dos RENDIMENTOS

          
		  

		  // Calculando os DESCONTOS
		  
		  // Iniciando a Variável Final Liquido Individual para Correção Posterior
		  $valor_final_individual = 0;
		  
		  $result_debito = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'DEBITO' AND id_clt = '$row_clt[0]' AND status = '1' AND id_mov != '62' AND (lancamento = '1' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]')"); // (62) FALTA já foi calculado acima
		  $cont_debito = mysql_num_rows($result_debito);

		  while($row_debito = mysql_fetch_array($result_debito)) {
			  
			  $debt_exp = explode(",",$row_debito['incidencia']);
			  $cont_debt_exp = count($debt_exp);
			  
			  // INSS,IRRF,FGTS == 5020,5021,5023
			  for($i=0; $i<=$cont_debt_exp; $i++) {
				  
				  if($debt_exp[$i] == 5020) {
					  $salario_calc_inss = $salario_calc_inss - $row_debito['valor_movimento'];
				  }
				  
				  if($debt_exp[$i] == 5021) {
					  $salario_calc_IR = $salario_calc_IR - $row_debito['valor_movimento'];
				  }
				  
				  if($debt_exp[$i] == 5023) {
					  $salario_calc_FGTS = $salario_calc_FGTS - $row_debito['valor_movimento'];
				  }
				  
			  }
		  	  
			  // SALÁRIO BASE + Todos os movimentos
			  
			  // Movimentos 9500 e 7003 não incidem
			  if($row_debito['cod_movimento'] != '9500' and $row_debito['cod_movimento'] != '7003') {
			  	$salario_base = $salario_base - $row_debito['valor_movimento'];
			  }
			  
			  // Corrigindo o valor liquido para o participante caso tenha movimentos 9500 e 7003
			  if($row_debito['cod_movimento'] == '9500' or $row_debito['cod_movimento'] == '7003') {
				$valor_final_individual -= $row_debito['valor_movimento'];
			  }
			  
			  $total_debito = $total_debito + $row_debito['valor_movimento'];
			  $AR_descontos[] = $row_debito['cod_movimento'];
			  $AR_descontosva[] = $row_debito['valor_movimento'];
			  //
			  
			  if($row_debito['lancamento'] == 1) {
				  $texto_movimentos .= "UPDATE rh_movimentos_clt SET status = '5', id_folha = '$row_folha[0]' WHERE id_movimento = '$row_debito[0]';\r\n";
			  }
		  
		  }
		  // Fim dos DESCONTOS
		  
		  

		  
		  // CONTRIBUIÇÃO SINDICAL (Apenas uma vez ao ano)
		  $result_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$row_clt[rh_sindicato]'");
		  $row_sindicato = mysql_fetch_array($result_sindicato);
		  $num_sindicato = mysql_num_rows($result_sindicato);
		  
		  if(!empty($num_sindicato)) {
		  
			  if($MesFolhaTab == $row_sindicato['mes_desconto']) {
				  $valor_desconto_sindicato = $salario_base_limpo / 30;
			  } else {
				  $valor_desconto_sindicato = "0";
			  }
			 
		  }
		  
		  $total_debito = $total_debito + $valor_desconto_sindicato;
		  // Fim da CONTRIBUIÇÃO SINDICAL
		  
		  
		  
		  
		  // Calculando INSS sobre Salário
		  if($row_clt['desconto_inss'] == '1') {
			  $salario_calc_inss = 0;
		  }
		  
		  $Calc -> MostraINSS($salario_calc_inss,$row_folha['data_inicio2']);
		  $valor_inss = $Calc -> valor;
		  $taxa_inss = $Calc -> percentual;
		  
		  // Total de BASE CALCULO INSS
		  $salario_base_calc_inss = $salario_base_calc_inss + $salario_calc_inss;
		  
		  // Fim de INSS sobre Salário
		  
		  
		  
		  
		  // Calculando IRRF
		  $salario_calc_IR = $salario_calc_IR - $valor_inss;
		  
		  $Calc -> MostraIRRF($salario_calc_IR,$row_clt[0],$row_clt['id_projeto'],$row_folha['data_inicio2']);
		  $valor_IR = $Calc -> valor;
		  
		  $filhos_deducao = $Calc -> total_filhos_menor_21;
		  $valor_deducao_ir = $Calc -> valor_deducao_ir_total;
		  
		  $valor_IR = $valor_IR + $valor_irrf_dt;
		  
		  // Total de BASE CALCULO IRRF
		  $salario_base_calc_irrf = $salario_base_calc_irrf + $salario_calc_IR;
		  $to_deducao_ir = $to_deducao_ir + $valor_deducao_ir;
		  
		  // Fim de IRRF
		  
		  
		  
		  // Calculando VALE TRANSPORTE (DÉBITO)
		  if($row_clt['transporte'] == "1" and $row_folha['terceiro'] != 1) {
		  		
			  $result_vale = mysql_query("SELECT a7001 FROM rh_folha_proc WHERE id_clt = '$row_clt[0]' AND id_folha = '$folha'");
			  $row_vale = mysql_fetch_array($result_vale);
			  $total_vale = $row_vale['0'];
			  
			  $PercentVale = $salario_bruto * 0.06;
			  if($PercentVale <= $total_vale) {
				  $total_vale = $PercentVale;
			  }
		
			  $total_vale = number_format($total_vale,2,".","");
			  $total_vale_final += $total_vale;
			  
		  }
		  // Fim de VALE TRANSPORTE
		  
		  
		  
		  
		  // Calculando VALE REFEIÇÃO (DÉBITO)
		  $qr_refeicao = mysql_query("SELECT * FROM rh_movimentos_clt WHERE cod_movimento = '8006' AND id_clt = '$row_clt[0]' AND status = '1'");
		  $refeicao = mysql_fetch_assoc($qr_refeicao);
		  $num_refeicao = mysql_num_rows($qr_refeicao);
		  
		  if($refeicao['lancamento'] == '1') {
			  unset($qr_refeicao, $refeicao, $num_refeicao);
			  $qr_refeicao = mysql_query("SELECT * FROM rh_movimentos_clt WHERE cod_movimento = '8006' AND id_clt = '$row_clt[0]' AND status = '1' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]' AND lancamento = '1'");
			  $refeicao = mysql_fetch_assoc($qr_refeicao);
			  $num_refeicao = mysql_num_rows($qr_refeicao);
		  }
		  
		  if(!empty($num_refeicao) and $row_folha['terceiro'] != 1) {
			  
			  $vr = $refeicao['valor_movimento'] * 0.20;
			  $vr = number_format($vr,2,".","");
			  
		  }
		  // Fim de VALE REFEIÇÃO (DÉBITO)
		  
		  
		  
		  
		  // Calculando VALE REFEIÇÃO (CRÉDITO)
		  if(!empty($num_refeicao) and $row_folha['terceiro'] != 1) {
			  
			  $vale_alimentacao = number_format($refeicao['valor_movimento'],2,".","");
			  $total_rendi = $total_rendi + $refeicao['valor_movimento'];
			  $AR_rendimentos[] = $refeicao['cod_movimento'];
			  $AR_rendimentosva[] = $refeicao['valor_movimento'];
			  
		  }
		  // Fim de VALE REFEIÇÃO (CRÉDITO)
          
		  
		  
		  
		  // Calculando SALÁRIO FAMILIA
		  if(!empty($row_clt['id_antigo'])) {
				$referencia_familia = $row_clt['id_antigo']; 
		  } else {
				$referencia_familia = $row_clt['id_clt'];
		  }

	   	  if($row_folha['terceiro'] != 1) {
			  $calc_sal_familia = $salario_base;
			  $Calc -> Salariofamilia($calc_sal_familia,$referencia_familia,$row_clt['id_projeto'],$row_folha['data_inicio2'],'2');
			  $valor_familia = $Calc -> valor;
			  $total_menor = $Calc -> filhos_menores;
	      }
		  
		  $qr_mes_anterior = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'CREDITO' AND id_clt = '$row_clt[0]' AND status = '1' AND id_mov = '94' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]'");
		  $mes_anterior = mysql_fetch_assoc($qr_mes_anterior);
		  $valor_familia += $mes_anterior['valor_movimento'];

		  // Fim do SALÁRIO FAMILIA

		
		  $total_debito = $total_debito + $total_vale + $vr;
		  $total_rendi = $total_rendi + $valor_dt;

          

		  // Calculando FGTS
		  $salario_base_calc_fgts = $salario_base_calc_inss;
		  $fgts = $salario_base_calc_fgts * .08 ;
		  // Fim do FGTS
		  
		  
		  
		  
		  // SALÁRIO LÍQUIDO
		  $valor_final_individual = $salario_base + $valor_familia - $valor_inss - $valor_IR + $refeicao['valor_movimento'] - $vr + $valor_desconto_sindicato - $total_vale + $valor_final_individual + $valor_dt - $desconto_dt - $valor_inss_dt - $valor_irrf_dt;
		  //
		  
		  
		  
		  // Criando Variável 'SÓ INSS' para separar os valores de INSS
		  $valor_soh_inss = $valor_soh_inss + $valor_inss;
		  
		  // Atualizando INSS, Acrescentando o valor do INSS DO DÉCIMO TERCEIRO
		  $valor_inss = $valor_inss + $valor_inss_dt;		  




		  // Embelezamento da Página
		  if($cont%2) { 
		  		$color = "#f0f0f0"; 
		  } else { 
		  		$color = "#dddddd"; 
		  }
		  
			$extraido = explode(" ", $row_clt['nome']);
			$primeiro_nome = $extraido[0];
			$segundo_nome = $extraido[1];
			$terceiro_nome = $extraido[2];
			$quarto_nome = $extraido[3];
			$quinto_nome = $extraido[4];
			
			if ($quarto_nome == "DAS" or $quarto_nome == "DA" or $quarto_nome == "DE" or $quarto_nome == "DOS" or $quarto_nome == "DO" or $quarto_nome == "E") {
					$nomeT = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome $quinto_nome";
			} else {
					$nomeT = "$primeiro_nome $segundo_nome $terceiro_nome $quarto_nome";
			}
			
			if($row_clt['status'] == '50' or $row_clt['status'] == '51') {
				$nomeT = "<span style='color:#693;'>$nomeT</span>";
			}
		  //


		  
		  
		  // Mudando tudo quando for RESCISÃO ou FÉRIAS
		  if($row_cltFolha['ferias'] != 0 or !empty($ferias)) {
			  
			  // FÉRIAS
			  if($row_cltFolha['ferias'] == 1 or !empty($ferias)) {
				  
				  $nomeT = "<span style='color:#06C'>".$nomeT."</span>";
				  $salario_base_limpoF = number_format($salario_base_limpo,2,",",".");
				  $total_rendiF = number_format($total_rendi,2,",",".");
				  $total_debitoF = number_format($total_debito,2,",",".");
				  $salario_baseF = number_format($salario_base,2,",",".");
				  $valor_inssF = number_format($valor_inss,2,",",".");
			   	  $valor_IRF = number_format($valor_IR,2,",",".");
			      $valor_faltasF = number_format($valor_faltas,2,",",".");
				  $valor_familiaF = number_format($valor_familia,2,",",".");
				  $valor_final_individual = $salario_base + $total_rendi + $valor_familia - $valor_faltas - $total_debito - $valor_inss - $valor_IR;        
				  $valor_final_individualF = number_format($valor_final_individual,2,",",".");
				  $valor_final_individualF = "<span style='color:#06C'>$valor_final_individualF</span>";
			  
			  // RESCISÃO
			  } elseif($row_cltFolha['ferias'] == 2) {
				  
				  $nomeT = "<span style='color:#C30'>".$nomeT."</span>";
			      $valor_final_individualF = "<span style='color:#C30'>0,00</span>";
				  
				  $reres = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = '$row_clt[0]'");
				  $rowres = mysql_fetch_array($reres);
				  
				  $movi = explode(",", $rowres['movimentos']);
				  $valo = explode(",", $rowres['valor_movimentos']);
				  
				  for($i = 0; $i <= count($movi); $i ++) {
					  
					  $movimentoo = explode("-",$movi[$i]);
					  
					  if($movimentoo[0] == 1) {
						  $o_rendi .= $valo[$i];
					  } else {
						  $o_desco .= $valo[$i];
					  }
				  
				  }
				  
				  $salario_bruto = $rowres['saldo_salario'];
				  
				  if($rowres['fator'] == "empregado" and $rowres['aviso'] == "indenizado") {
					  $desconto_aviso = $rowres['aviso_valor'];
					  $rendimen_aviso = 0;
				  } else {
					  $rendimen_aviso = $rowres['aviso_valor'];
					  $desconto_aviso = 0;
				  }
				  
				  
				  
				  
				  // Somando para colunas RENDIMENTOS
				  $total_rendi = $rendimen_aviso + $rowres['dt_salario'] + $rowres['terceiro_ss'] + $rowres['ferias_vencidas'] + $rowres['ferias_pr'] + $rowres['umterco_fp'] + $rowres['umterco_fv'] + $rowres['insalubridade'] + $o_rendi + $rowres['a479'] + $vale_alimentacao;
				  //
				  
				  
				  
				 
				  // Somando para colunas DESCONTOS
				  $total_debito = $rowres['total_liquido'] + $o_desco + $desconto_aviso + $vr;
				  //
				  
				  
				  
				  
				  // Mais Variáveis Definidas
				  $valor_inss = $rowres['previdencia_ss'] + $rowres['previdencia_dt'];
				  $valor_IR = $rowres['ir_ss'] + $rowres['ir_dt'] + $rowres['ir_ferias'];
				  $valor_familia = $rowres['to_sal_fami'];
				  $salario_base_limpoF = $rowres['sal_base'];
				  $valor_faltasF = 0;
				  $valor_final_individual = $salario_bruto + $total_rendi + $valor_familia - $valor_faltas - $total_debito - $valor_inss - $valor_IR;
				  $valor_desconto_sindicato = 0;
				  $valor_deducao_ir = 0;
				  $fgts = 0;
				  $to_deducao_ir = 0;
				  $salario_baseF = "0,00";
				  $salario_base_limpoF = "0,00";
				  $salario_bruto_echoF = number_format($rowres['saldo_salario'],2,",",".");
				  $valor_inssF = number_format($rowres['previdencia_ss'] + $rowres['previdencia_dt'],2,",",".");
				  $valor_familiaF = number_format($rowres['to_sal_fami'],2,",",".");
				  $total_debitoF = number_format($total_debito,2,",",".");
				  $total_rendiF = number_format($total_rendi,2,",",".");
				  $valor_IRF = number_format($rowres['ir_ss'] + $rowres['ir_dt'] + $rowres['ir_ferias'],2,",",".");
				  
			  }
			  // Fim se é FÉRIAS ou RESCISÃO
		  
		  
		  
		  
		  // Funcionário em ATIVIDADE NORMAL
		  } else {
		  
			  $salario_base_limpoF = number_format($salario_base_limpo,2,",",".");
			  $salario_baseF = number_format($salario_base,2,",",".");
			  $total_rendiF = number_format($total_rendi,2,",",".");
			  $total_debitoF = number_format($total_debito,2,",",".");
			  $valor_inssF = number_format($valor_inss,2,",",".");
			  $valor_IRF = number_format($valor_IR,2,",",".");
			  $valor_familiaF = number_format($valor_familia,2,",",".");
			  $salario_base_limpoF = number_format($salario_base_limpo,2,",",".");
			  $valor_faltasF = number_format($valor_faltas,2,",","."); 
			  $valor_final_individualF = number_format($valor_final_individual,2,",",".");
			  $valor_desconto_sindicatoF = number_format($valor_desconto_sindicato,2,",",".");
			  $valor_deducao_irF = number_format($valor_deducao_ir,2,",",".");
			  $fgtsF = number_format($fgts,2,",",".");
			  $valor_deducao_irF = number_format($valor_deducao_ir,2,",",".");
		  
		  } ?>
          
          
          
          
		  <tr style="text-align:center; background-color:<?=$color?>;">
		      <td style="text-align:left;"><?php echo $row_clt['campo3'].' / '.$row_clt[0]; ?></td>
		      <td style="text-align:left;"><?=$nomeT?></td>
              <td>
			  	<?php if(!empty($d_trabalhando)) { echo $d_trabalhando; } else { echo '&nbsp;'; } ?>
              </td>
			  <td><?=$salario_base_limpoF?></td>
			  <td><?=$total_rendiF?></td>
			  <td><?=$total_debitoF?></td>
			  <td><?=$salario_baseF?></td>
			  <td><?=$valor_inssF?></td>
			  <td><?=$valor_IRF?></td>
			  <td><?=$valor_faltasF?></td>
			  <td><?=$valor_familiaF?></td>
			  <td><?=$valor_final_individualF?></td>
		  </tr>
          
          
          
          
          <?php // Criando linha para o arquivo TXT
		  
		 	// Formatando para o TXT
			$salbaseT = number_format($salario_base,2,".","");
			$salario_base_limpoT = number_format($salario_base_limpo,2,".","");
			$rendT = number_format($total_rendi,2,".","");
			$descoT = number_format($total_debito,2,".","");
			$inssT = number_format($valor_inss,2,".","");
			$valor_IRT = number_format($valor_IR,2,".","");
			$valor_familiaT = number_format($valor_familia,2,".","");
			$salliquidoT = number_format($valor_final_individual,2,".","");
			$valor_deducao_irT = number_format($valor_deducao_ir,2,".","");
			$total_valeT = number_format($total_vale,2,".","");
			$vrT = number_format($vr,2,".","");
			$vale_alimentacaoT = number_format($vale_alimentacao,2,".","");
			$valor_desconto_sindicatoT = number_format($valor_desconto_sindicato,2,".","");
			$fgtsT = number_format($fgts,2,".","");
			$valor_dtT			= number_format($valor_dt,2,".","");
			$valor_inss_dtT		= number_format($valor_inss_dt,2,".","");
			$valor_irrf_dtT		= number_format($valor_irrf_dt,2,".","");
			$valor_soh_inssT	= number_format($valor_soh_inss,2,".","");

  
		  
			// Todos os Códigos já estão em uma Array, ele pega essa Array e seleciona todos os valores de cada Código e joga a resposta em Outra Array
		  	$numero_ar = count($ar_codigos);
		  	for ($r = 0; $r < $numero_ar; $r++) {
				
			  	$ok = $ar_codigos[$r];
			  	$result_now = mysql_query("SELECT sum(valor_movimento) FROM rh_movimentos_clt WHERE cod_movimento = '$ok' 
				and id_clt = '$row_clt[0]' and status = '1' and mes_mov = '$row_folha[mes]'");
				$row_now = mysql_fetch_array($result_now);
				$ar_codigos_test[$ok] = $row_now['0'];
				
		  	}

			// Pega os Códigos e insere em uma Variável Única, e pega os valores e coloca em Outra Array
		  	for ($r = 0; $r < $numero_ar; $r++) {


			  
				  $ok = $ar_codigos[$r];
				  $chaves_do_array = array_keys($ar_codigos_test);
				  $chaves_do_array2 = $chaves_do_array2.",a".$chaves_do_array[$r];
				  
				  $chave_do_arrayAGORA = ", a".$chaves_do_array[$r];
				  
				  $valor_individual_arr = number_format($ar_codigos_test[$ok], 2,".","");
				  $valores_do_array = $valores_do_array.",'".$valor_individual_arr."'";
				  
				  if($chave_do_arrayAGORA == ", a5049" and $valor_deducao_ir > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_deducao_irT."'";
				  } elseif($chave_do_arrayAGORA == ", a7001" and $total_vale > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$total_valeT."'";
				  } elseif($chave_do_arrayAGORA == ", a8003" and $vr > 0.00) {
				  	  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$vrT."'";
				  } elseif($chave_do_arrayAGORA == ", a5020" and $valor_soh_inss > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_soh_inssT."'";
				  } elseif($chave_do_arrayAGORA == ", a5021" and $valor_IR > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_IRT."'";
				  } elseif($chave_do_arrayAGORA == ", a5022" and $valor_familia > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_familiaT."'";
				  } elseif($chave_do_arrayAGORA == ", a5019" and $valor_desconto_sindicato > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_desconto_sindicatoT."'";
				  } elseif($chave_do_arrayAGORA == ", a5029" and $valor_dt > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_dtT."'";
				  } elseif($chave_do_arrayAGORA == ", a5030" and $valor_irrf_dt > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_irrf_dtT."'";
				  } elseif($chave_do_arrayAGORA == ", a5031" and $valor_inss_dt > 0.00) {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_inss_dtT."'";
				  } else {
					  $ArrayCODeArrayVALOR = $chave_do_arrayAGORA." = '".$valor_individual_arr."'";
				  }
				  
				  $ArrayCODeArrayVALORB = $ArrayCODeArrayVALORB.$ArrayCODeArrayVALOR;
			  		
		   }		  
		  
		  
		  
		  
		  // Inserindo a QUANTIDADE DE FALTAS no Campo A80002 no final da tabela
		  if(!empty($num_faltas)) {
			  $Faltas_TXT = ", a80002 = '$num_faltas' ";
		  } else {
			  $Faltas_TXT = ", a80002 = '0' ";
		  }
		  
		  // Inserindo a QUANTIDADE DE FILHOS DO CLT QUE RECEBA SALÁRIO FAMÍLIA no Campo A50222 no final da tabela
		  if(!empty($valor_familia)) {
			  $QtdFilhos_TXT = ", a50222 = '$total_menor' ";
		  } else {
			  $QtdFilhos_TXT = ", a50222 = '0' ";
		  }
		  
		  // Inserindo a A QUANTIDADE DE FILHOS CASO HAJA DEDUÇÃO DO IMPOSTO DE RENDA no Campo A50492 no final da tabela
		  if($valor_deducao_ir > 0.00) {
			  $QtdFilhosDedu_TXT = ", a50492 = '$filhos_deducao' ";
		  } else {
			  $QtdFilhosDedu_TXT = ", a50222 = '0' ";
		  }
		  
		  


		  // Juntando as Variáveis onde estão os Códigos e os Valores na Variável do Arquivo TXT

		  $primeira_parte = "UPDATE rh_folha_proc SET cod = '$row_clt[campo3]', nome = '$row_clt[nome]', status_clt = '$row_clt[status]', id_banco = '$row_clt[banco]', agencia = '$row_clt[agencia]', conta = '$row_clt[conta]', cpf = '$row_clt[cpf]', salbase = '$salbaseT', sallimpo = '$salario_base_limpoT', rend = '$rendT', desco = '$descoT', inss = '$inssT', t_inss = '$row_inss[faixa]', imprenda = '$imprendaT', t_imprenda = '$row_IR[faixa]', d_imprenda = '$row_IR[fixo]', fgts = '$fgtsT', base_irrf = '$salario_calc_IR', salfamilia = '$salfamiliaT', salliquido = '$salliquidoT' ";

		  $UltimaParteTXT = $QtdFilhos_TXT.$Faltas_TXT.$QtdFilhosDedu_TXT.", status = '3' WHERE id_folha = '$folha' and id_clt ='$row_clt[0]'";

		  $conteudo = $conteudo."$primeira_parte"."$ArrayCODeArrayVALORB"."$UltimaParteTXT;\r\n";
		  
		  //
		  
		  
		  
		  
		  // Somando os TOTAIS INDIVIDUAIS
		  
		  $final_falta = $final_falta + $valor_faltas;
		  $final_INSS = $final_INSS + $valor_inss;
		  $final_IR = $final_IR + $valor_IR;
		  #$final_deducaoIR = $final_deducaoIR + $valor_deducao_ir;
		  $final_familia = $final_familia + $valor_familia;
		  $final_sindicato = $final_sindicato + $valor_desconto_sindicato;
		  $final_liquido = $final_liquido + $valor_final_individual;
		  $final_rendimen = $final_rendimen + $total_rendi;
		  $final_descon = $final_descon + $total_debito;
		  $final_soh_inss 	= $final_soh_inss + $valor_soh_inss;
		  $final_salario_dt	= $final_salario_dt + $valor_dt;
		  $final_inss_dt = $final_inss_dt + $valor_inss_dt;
		  $final_irrf_dt	= $final_irrf_dt + $valor_irrf_dt;	  
		  $vale_transporte_final = $vale_transporte_final + $total_vale;
		  $vr_final = $vr_final + $vr;
		  $vale_alimentacao_final = $vale_alimentacao_final + $vale_alimentacao;
		  $salario_base_limpo_final = $salario_base_limpo_final + $salario_base_limpo;
		  $salario_base_final = $salario_base_final + $salario_base;
		  $valor_final = $valor_final + $valor_final_individual;
		  
		  // Fim dos TOTAIS INDIVIDUAIS
		  
		  unset($valor_familia);
		  
		  // Limpeza DT
		  
		  unset($valor_dt, 
				$desconto_dt,
				$valor_inss_dt,
				$valor_irrf_dt);
		  
		  // Limpando Arrays
		  
		  unset($incidencia_credito, 
				$valores_credito, 
				$total_rendi, 
				$valor_inss, 
				$total_vale,
				$vr,
				$vale_alimentacao,
				$total_debito, 
				$valor_IR, 
				$cont_menor1, 
				$cont_menor2, 
				$cont_menor3, 
				$cont_menor4, 
				$cont_menor5, 
				$total_menor, 
				$valor_desconto_sindicato, 
				$valor_faltas, 
				$total_filhos_menor_21, 
				$valor_deducao_ir, 
				$valor_soh_inss, 
				$salario_base_resumo_evento);
		  
		  // Limpando do Arquivo TXT
		  
		  unset($salbaseT, 
				$rendT, 
				$descoT, 
				$inssT, 
				$imprendaT, 
				$salfamiliaT, 
				$salliquidoT, 
				$valor_deducao_irT, 
				$valor_soh_inssT, 
				$chaves_do_array2, 
				$valores_do_array, 
				$ArrayCODeArrayVALORB, 
				$ar_codigos_test, 
				$AR_rendimentos, 
				$AR_rendimentosva, 
				$codigos_rendimentos, 
				$valores_rendimentos);

		  // Fim da Limpeza de Arrays
		  
		  
		  
		  
		  $cont++;
		  $cont2 = $cont2+1;
		  
		  
		  
	    
	    } 
		// Terminando o LOOP


		

	    // Formatando os DADOS FINAIS
		
		// Total
		$TOTAL_RENDIMENTO = $final_rendimen + $Total_ferias_rend;
		
		// Definindo as bases de INSS, IRRF e FGTS
		$final_base_INSS = $salario_base_final + $TOTAL_RENDIMENTO;
		$final_base_sohINSS = $salario_base_final + $TOTAL_RENDIMENTO - $final_salario_dt;
		$final_base_IRRF = $salario_base_final + $TOTAL_RENDIMENTO - $final_INSS;
		$totalDeFGTS = $final_base_INSS * 0.08;
		$final_base_INSSF = number_format($final_base_INSS,2,",",".");
		$final_base_IRRFF = number_format($final_base_IRRF,2,",",".");

		// Novas Variáveis de TOTAIS
		$salario_base_calc_inssF = number_format($salario_base_calc_inss,2,",",".");
		$salario_base_calc_fgtsF = number_format($salario_base_calc_fgts,2,",",".");
		$to_deducao_irF = number_format($to_deducao_ir,2,",",".");
		$salario_base_calc_irrfF = number_format($salario_base_calc_irrf - $to_deducao_ir,2,",",".");
		$total_fgtsF = number_format($fgts,2,",",".");
		$valor_finalF = number_format($valor_final,2,",",".");
		$salario_base_limpo_finalF = number_format($salario_base_limpo_final,2,",",".");
		$salario_base_finalF  = number_format($salario_base_final,2,",",".");
		$vale_transporte_finalF = number_format($vale_transporte_final,2,",",".");
		$final_INSSF = number_format($final_INSS,2,",",".");
		$final_IRF = number_format($final_IR,2,",",".");
		$final_deducaoIRF = number_format($final_deducaoIR,2,",",".");
		$final_familiaF = number_format($final_familia,2,",",".");
		$final_sindicatoF = number_format($final_sindicato,2,",",".");
		$final_rendimenF = number_format($final_rendimen,2,",",".");
		$final_desconF = number_format($final_descon,2,",",".");	
		$final_salario_dtF = number_format($final_salario_dt,2,",",".");
		$final_inss_dtF = number_format($final_inss_dt,2,",",".");
		$final_irrf_dtF = number_format($final_irrf_dt,2,",",".");	
		$final_liquidoF = number_format($final_liquido,2,",","."); 
		$salario_brutoF = number_format($salario_bruto,2,",",".");
		$salario_calc_IRF = number_format($salario_calc_IR,2,",",".");
		$totalDeFGTSF = number_format($totalDeFGTS,2,",",".");
		$vr_finalF = number_format($vr_final,2,",",".");
		$vale_alimentacao_finalF = number_format($vale_alimentacao_final,2,",",".");
		
		// Verificando se vai exibir ou não os DESCONTOS FIXOS (Ex: Vale, Inss, Ir, Família)
		$movimentos_fixos = array(0001,7001,5020,5021,5022,5019,5047);
		$valores_movimentos_fixos = array($salario_base_finalF,$vale_transporte_finalF,$final_INSSF,$final_IRF,$final_familiaF, $final_sindicatoF,$final_deducaoIRF);
		
		if(empty($final_IR)) {
			$disable = "style='display:none'";
		} else {
			$disable = "style='display:'";
		}
		
		// Somando TOTAIS NORMAIS com TOTAIS DE FÉRIAS
		?>
        
         <tr>
          <td align="center" valign="middle" class="style23">&nbsp;</td>
          <td align="right" valign="bottom" class="style23">&nbsp;</td>
          <td align="center" valign="bottom" class="style23">TOTAIS:</td>
          <td align="center" valign="bottom" class="style23"><?=$salario_base_limpo_finalF?></td>
          <td align="center" valign="bottom" class="style23"><?=number_format($TOTAL_RENDIMENTO,2,",",".")?></td>
          <td align="center" valign="bottom" class="style23"><?=$final_desconF?></td>
          <td align="center" valign="bottom" class="style23"><?=$salario_base_finalF?></td>
          <td align="center" valign="bottom" class="style23"><?=$final_INSSF?></td>
          <td align="center" valign="bottom" class="style23"><?=$final_IRF?></td>
          <td align="center" valign="bottom" class="style23">&nbsp;</td>
          <td align="center" valign="bottom" class="style23"><?=$final_familiaF?></td>
          <td align="center" valign="bottom" class="style23"><?=$valor_finalF?></td>
        </tr>
      </table>
        
      <br />
      <?=$mensagem?>
      <br>
      
      <?php // Verificando se existe algum evento fixo zerado para ocultar
	  
	  if($vale_transporte_finalF == "0,00") {
		  $stilo_oculto1 = "style='display:none'";
	  }
	  if($final_INSSF == "0,00") {
		  $stilo_oculto2 = "style='display:none'";
	  }
	  if($final_IRF == "0,00") {
		  $stilo_oculto3= "style='display:none'";
	  }
	  if($final_familiaF == "0,00") {
		  $stilo_oculto4= "style='display:none'";
	  }
	  if($final_sindicatoF == "0,00") {
		  $stilo_oculto5= "style='display:none'";
	  }
	  if($final_deducaoIRF == "0,00") {
		  $stilo_oculto6= "style='display:none'";
	  }
	  
	  // ?>
      <br>
      <br>
      <table width="97%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="39%" align="center" valign="top" bgcolor="#F8F8F8" style="border-right:solid 2px #FFF">
          
          <br>
          <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td height="24" colspan="2" align="center" valign="middle" class="show">TOTALIZADORES</td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td width="55%" align="right" valign="middle">Sal&aacute;rios L&iacute;quidos:</td>
              <td width="45%" align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=$final_liquidoF?>
              </b></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td align="right" valign="middle">Base de INSS:</td>
              <td align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=$salario_base_calc_inssF?>
              </b></td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td align="right" valign="middle">Base de IRRF:</td>
              <td align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=$salario_base_calc_irrfF?>
              </b></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td align="right" valign="middle">Base de FGTS:</td>
              <td align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=$salario_base_calc_fgtsF?>
              </b></td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td align="right" valign="middle">Total de FGTS:</td>
              <td align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=$total_fgtsF?>
              </b></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td align="right" valign="middle">Base de FGTS (Sefip):</td>
              <td align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=$salario_base_calc_fgtsF?>
              </b></td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td align="right" valign="middle">FGTS a Recolher (Sefip):</td>
              <td align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=$total_fgtsF?>
              </b></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td align="right" valign="middle">Total Dependentes IR (DDIR):</td>
              <td align="left" valign="middle">&nbsp;&nbsp;
              <b><?=$to_deducao_irF?></b></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td align="right" valign="middle">Multa do FGTS:</td>
              <td align="left" valign="middle">&nbsp;&nbsp;&nbsp;<b>0,00</b></td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td align="right" valign="middle">Funcion&aacute;rios Listados:</td>
              <td align="left" valign="middle">&nbsp;&nbsp;<b>
                <?=mysql_num_rows($result_folha_pro)?>
              </b></td>
            </tr>
          </table>
          
          </td>
          <td width="61%" align="center" valign="top" bgcolor="#F8F8F8" style="border-left:solid 2px #FFF">
          
          <br>
          <table width="95%" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td height="30" colspan="4" align="center" valign="middle" class="show">Resumo por Evento (R$)</td>
            </tr>
            <tr class="novo_tr_dois">
              <td width="11%" height="21" align="center" valign="middle">Evento</td>
              <td width="45%" valign="middle" style="text-align:left;">Descri&ccedil;&atilde;o</td>
              <td width="21%" valign="middle" style="text-align:right;">Rendimentos</td>
              <td width="23%" valign="middle" style="text-align:right;">Descontos</td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td height="18" align="center" valign="middle">0001</td>
              <td align="left" valign="middle" >SALARIO BASE</td>
              <td align="right" valign="middle"><b>
                <?php // echo number_format($salario_base_resumo_evento_to,2,",",".");
						 echo $salario_base_limpo_finalF; ?>
              </b></td>
              <td align="right" valign="middle">&nbsp;</td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td height="18" align="center" valign="middle">7001</td>
              <td align="left" valign="middle">DESCONTO VALE TRANSPORTE</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><div style="margin-right:5;"><b>
                <?=$vale_transporte_finalF?>
              </b></div></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td height="18" align="center" valign="middle">8003</td>
              <td align="left" valign="middle">DESCONTO VALE REFEI&Ccedil;&Atilde;O</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><div style="margin-right:5;"> <b>
                <?=$vr_finalF?>
              </b></div></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td height="18" align="center" valign="middle">8003</td>
              <td align="left" valign="middle">VALE ALIMENTA&Ccedil;&Atilde;O PROXIMO MES</td>
              <td align="right" valign="middle"><div style="margin-right:5;"> <b>
                <?=$vale_alimentacao_finalF?>
              </b></div></td>
              <td align="right" valign="middle">&nbsp;</td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td height="18" align="center" valign="middle">5020</td>
              <td align="left" valign="middle">INSS</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><div style="margin-right:5;"><b>
                <?=number_format($final_soh_inss,2,",",".")?>
              </b></div></td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td height="18" align="center" valign="middle">5021</td>
              <td align="left" valign="middle">IMPOSTO DE RENDA</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><div style="margin-right:5;"><b>
                <?=$final_IRF?>
              </b></div></td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td height="18" align="center" valign="middle">5022</td>
              <td align="left" valign="middle">SAL&Aacute;RIO FAMILIA</td>
              <td align="right" valign="middle"><b>
                <?=$final_familiaF?>
              </b></td>
              <td align="right" valign="middle">&nbsp;</td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td height="18" align="center" valign="middle">5019</td>
              <td align="left" valign="middle">CONTRIBUI&Ccedil;&Atilde;O SINDICAL</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><span style="margin-right:5;"><b>
                <?=$final_sindicatoF?>
              </b></span></td>
            </tr>
            <tr class="novalinha corfundo_um" style="display:none">
              <td height="18" align="center" valign="middle">5049</td>
              <td align="left" valign="middle">DDIR - Dedu&ccedil;&atilde;o de Imposto de renda por Dependente</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><span style="margin-right:5;"><b>
                <?=$final_deducaoIRF?>
              </b></span></td>
            </tr>
            <?php if($row_folha['terceiro'] == 1){ ?>
            <tr class="novalinha corfundo_um">
              <td height="18" align="center" valign="middle">5029</td>
              <td align="left" valign="middle">D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
              <td align="right" valign="middle"><b>
                <?=$final_salario_dtF?>
              </b></td>
              <td align="right" valign="middle">&nbsp;</td>
            </tr>
            <tr class="novalinha corfundo_dois">
              <td height="18" align="center" valign="middle">5030</td>
              <td align="left" valign="middle">IRRF D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><span style="margin-right:5;"><b><?=$final_irrf_dtF?></b></span></td>
            </tr>
            <tr class="novalinha corfundo_um">
              <td height="18" align="center" valign="middle">5031</td>
              <td align="left" valign="middle">INSS TERCEIRO SAL&Aacute;RIO</td>
              <td align="right" valign="middle">&nbsp;</td>
              <td align="right" valign="middle"><span style="margin-right:5;"><b><?=$final_inss_dtF?></b></span></td>
            </tr>
            <?php }
			
		$cont = "0";
		
		// Array para Movimentos SQL IN
		$array_ids_totalizadores = implode(',',$array_ids_totalizadores);
		
		// Primeiro Seleciona Todos os Códigos
		$result_events = mysql_query("SELECT distinct(descicao),cod,id_mov FROM rh_movimentos WHERE incidencia = 'FOLHA' AND cod != '7001' AND cod != '5022' AND cod != '5049' AND cod != '5021' AND cod != '5019'");
		while($row_events = mysql_fetch_array($result_events)) {
		
			if($cont % 2) { 
				$color = "corfundo_dois"; 
			} else { 
				$color = "corfundo_um"; 
			}
			
			$marg = "<div style='margin-right:5;'>";
			
			
			
			
			// Soma os Valores de Cada Código Selecionado na Query Anterior
			$result_total_evento = mysql_query("SELECT SUM(valor_movimento) AS valor FROM rh_movimentos_clt WHERE id_mov = '$row_events[id_mov]' AND mes_mov = '$row_folha[mes]' AND ano_mov = '$row_folha[ano]' AND id_projeto = '$row_projeto[0]' AND status = '1' AND status_folha != '5' AND lancamento = '1' AND id_clt IN($array_ids_totalizadores)");
			$row_total_evento = mysql_fetch_array($result_total_evento);
			
			$result_total_evento2 = mysql_query("SELECT SUM(valor_movimento) AS valor FROM rh_movimentos_clt WHERE id_mov = '$row_events[id_mov]' AND id_projeto = '$row_projeto[0]' AND status = '1' AND status_folha != '5' AND lancamento = '2' AND id_clt IN($array_ids_totalizadores)");
			$row_total_evento2 = mysql_fetch_array($result_total_evento2);
			
			
			

			// Cria a Array para destinguir qual Coluna entrará o Valor do Código (RENDIMENTO ou DESCONTO)
			$debitos_tab = array('5019','5020','5021','6004','7003','8000','7009','5020','5020','5021','5021','5021','5020','9500','5030','5031','5032','8003');
			$rendimentos_tab = array('5011','5012','5022','6006','6007','9000','5022','8004','8005','8006');
			
			
			
		    
			if(in_array($row_events['cod'], $debitos_tab)) { 
				$debito = $row_total_evento['valor'] + $row_total_evento2['valor'];
				$rendimento = NULL;
			} else {
				$debito = NULL;
				$rendimento = $row_total_evento['valor'] + $row_total_evento2['valor'];
			}
		
			if($rendimento == 0 and $debito == 0) {
				$disable = "style='display:none'";
			} else {
				$disable = "style='display:'";
			}
		
			echo "<tr class=\"novalinha $color\" $disable>";
			echo "<td height='18' align='center' valign='middle'>$row_events[cod]</td>";
			echo "<td align='left' valign='middle'>$row_events[descicao]</td>";
			echo "<td align='right' valign='middle'><b>";
			
			if(!empty($rendimento)) { 
				echo number_format($rendimento,2,",","."); 
			}
			
			echo "</b></td>";
        	echo "<td align='right' valign='middle'><b>$marg";
			
			if(!empty($debito)) { 
				echo number_format($debito,2,",",".");
			}
			
			echo "</div></b></td></tr>";
        
			// Somando Variáveis
			$re_tot_desconto = $re_tot_desconto + $debito;
			$re_tot_rendimento = $re_tot_rendimento + $rendimento;
			
			// Limpando Variáveis
			$desconto = NULL;
			$rendimento = NULL;
			
			$cont ++;
			
		}
		
		// Somando Variáveis com os Fixos
		//$re_tot_rendimento = $re_tot_rendimento + $salario_base_resumo_evento_to + $final_familia + $vale_alimentacao_final;
		$re_tot_rendimento = $re_tot_rendimento + $salario_base_limpo_final + $final_familia + $vale_alimentacao_final;
		$re_tot_desconto = $re_tot_desconto + $vale_transporte_final + $final_INSS + $final_IR + $final_sindicato + $vr_final;
		
		// Formatando Totais por Evento
		$re_tot_rendimentoF = number_format($re_tot_rendimento,2,",",".");
		$re_tot_descontoF = number_format($re_tot_desconto,2,",",".");
        ?>
        
            <tr class="novo_tr_dois">
              <td colspan="2" align="center" valign="middle">TOTAIS</td>
              <td align="right" valign="middle" style="text-align:right; height:20px;"><?=$re_tot_rendimentoF?></td>
              <td align="right" valign="middle" style="text-align:right; margin-right:5px;"><?=$re_tot_descontoF?></td>
            </tr>
          </table>
          </td>
        </tr>
      </table>
<br>
<?php   // Formatando os Totais para serem gravados no arquivo TXT
		$re_tot_rendimentoT = number_format($re_tot_rendimento,2,".","");
		$re_tot_descontoT = number_format($re_tot_desconto,2,".","");
		$TOTAL_RENDIMENTOT = number_format($TOTAL_RENDIMENTO,2,".","");
		$salario_base_calc_inssT = number_format($salario_base_calc_inss,2,".","");
		$salario_base_calc_irrfT = number_format($salario_base_calc_irrf - $to_deducao_ir,2,".","");
		$salario_base_calc_fgtsT = number_format($salario_base_calc_fgts,2,".","");
		$total_fgtsT = number_format($fgts,2,".","");
		$salario_base_finalT  = number_format($salario_base_final,2,".","");
		$final_liquidoTF = number_format($final_liquido,2,".","");
		$final_INSST = number_format($final_INSS,2,".","");
		$final_IRT = number_format($final_IR,2,".","");
		$totalDeFGTST = number_format($totalDeFGTS,2,".","");
		$final_rendimenT = number_format($final_rendimen,2,".","");
		$final_desconT = number_format($final_descon,2,".","");
		$final_familiaT = number_format($final_familia,2,".","");
		$final_soh_inssT = number_format($final_soh_inss,2,".","");
		$final_salario_dtT	= number_format($final_salario_dt,2,".","");
		$final_inss_dtT		= number_format($final_inss_dt,2,".","");
		$final_irrf_dtT		= number_format($final_irrf_dt,2,".","");
		$TOTAL_RENDIMENTOT = number_format($TOTAL_RENDIMENTOT,2,".","");

		$TERCEIRA_PARTE = "UPDATE rh_folha SET clts = '$cont2', rendi_indivi = '$TOTAL_RENDIMENTOT',rendi_final = '$re_tot_rendimentoT', descon_indivi = '$final_desconT', descon_final = '$re_tot_descontoT', total_salarios = '$salario_base_finalT', total_liqui = '$final_liquidoTF', total_familia = '$final_familiaT', base_inss = '$salario_base_calc_inssT', total_inss = '$final_soh_inssT', base_irrf = '$salario_base_calc_irrfT', total_irrf = '$final_IRT', base_fgts = '$salario_base_calc_fgtsT', total_fgts = '$total_fgtsT', base_fgts_sefip = '$salario_base_calc_fgtsT', total_fgts_sefip = '$total_fgtsT', multa_fgts = '0.00', valor_dt = '$final_salario_dtT', inss_dt = '$final_inss_dtT', ir_dt = '$final_irrf_dtT', valor_ferias = '$Total_ferias_rend', status = '3' WHERE id_folha = '$folha' LIMIT 1 ;\r\n";

		$conteudo = $conteudo.$TERCEIRA_PARTE.$texto_movimentos."\r\n";
		$nome_arquivo_download = "idfolha_".$folha.".txt";
		$arquivo = "/home/ispv/public_html/intranet/arquivos/folhaclt/".$nome_arquivo_download;
		
		
		
		
		// Tenta Abrir o Arquivo TXT
		if(!$abrir = fopen($arquivo, "wa+")) {
			echo "Erro abrindo arquivo ($arquivo)";
			exit;
		}

		// Escreve no Arquivo TXT
		if(!fwrite($abrir, $conteudo)) {
			echo "Erro escrevendo no arquivo ($arquivo)";
			exit;
		}

		// Fecha o Arquivo
		fclose($abrir);
    
	
	
	
	// Encriptografando a Variável
	$linkvolt = encrypt("$regiao&1");
	$linkvolt = str_replace("+","--",$linkvolt);
	
	$add = encrypt("$regiao&$folha&2");
	$add = str_replace("+","--",$add);
	
	$linkFim = encrypt("$regiao&$folha");
	$linkFim = str_replace("+","--",$linkFim);
	// -----------------------------
?>

</td>
  </tr>
  <tr>
    <td height="42" align="center" valign="middle" bgcolor="#CCCCCC">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
            <?php if(in_array($_COOKIE['logado'], $acesso_finalizacao)) { ?>
                <b><a href='acao_folha.php?enc=<?=$linkFim?>' class="botao">FINALIZAR</a></b>
            <?php } ?>
            </td>
            <td align="center">
                <b><a href='folha.php?enc=<?=$linkvolt."&tela=1"?>' class="botao">VOLTAR</a></b>
            </td>
            <td align="center">
                <b><a href='folha2.php?enc=<?=$add?>' class="botao">ADICIONAR CLT</a></b>
            </td>
        </tr>
    </table>
    <a href="<?=_URL."arquivos/folhaclt/".$nome_arquivo_download?>" target="_blank">abrir arquivo txt</a>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>