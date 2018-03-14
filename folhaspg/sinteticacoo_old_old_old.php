<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../funcoes.php";


//RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);

$regiao = $decript[0];
$folha = $decript[1];
//RECEBENDO A VARIAVEL CRIPTOGRAFADA

//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO


//SELECIONANDO A FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio2,date_format(data_fim, '%d/%m/%Y')as data_fim2,date_format(data_proc, '%d/%m/%Y')as data_proc2 FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);


//SELECIONANDO OS CLTS JA CADASTRADOS NA TAB FOLHA_PROC QUE ESTEJAM COM STATUS 2 = SELECIONADO ANTERIORMENTE
$result_folha_pro = mysql_query("SELECT * FROM folha_cooperado where id_folha = '$folha' and status = '2' ORDER BY nome ASC");
$num_clt_pro = mysql_num_rows($result_folha_pro);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$mes_int = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mes_int];

$titulo = "Folha Autonomo: Projeto $row_projeto[nome] mês de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

$data_menor14 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 14));
$data_menor21 = date("Y-m-d", mktime(0,0,0, $mes,$dia,$ano - 21));

//DADOS DA COOPERATIVA GERAL DA FOLHA
include "../classes/cooperativa.php";
$CoopGeral = new cooperativa();
$CoopGeral -> MostraCoop($row_folha['coop']);

$Gid_coop	 	= $CoopGeral -> id_coop;
$Gnome	 		= $CoopGeral -> nome;
$Gfantasia		= $CoopGeral -> fantasia;
$Gcnpj			= $CoopGeral -> cnpj;
$Gfoto			= $CoopGeral -> foto;

if($Gfoto == "0"){
	$LogoCoop = "";
}else{
	$LogoCoop = "<img src='../cooperativas/logos/coop_".$Gid_coop.$Gfoto."' alt='' width='110' height='79' align='absmiddle' >";
}
//--------------------------------------------
?>
<html>
<head>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<link rel="stylesheet" href="../js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />

<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>

<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.style29 {
	font-family: arial;
	font-weight: bold;
}
.style30 {color: #663300}
-->
</style>

<script language="javascript"> 

  //o parâmentro form é o formulario em questão e t é um booleano 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
  } 

</script> 

</head>

<body>
<table width="99%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="81" align="center" valign="middle" bgcolor="#003300" class="title"><table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="e2e2e2">
          <tr>
              <td width="16%" height="98" align="center" valign="middle" bgcolor="e2e2e2"><span class="style1">
              <?=$LogoCoop?></span></td>
            <td width="62%" bgcolor="e2e2e2"><span class="style3">
              <?=$Gnome?><br>
              CNPJ : <?=$Gcnpj?>
              <br>
            </span></td>
            <td width="22%" bgcolor="e2e2e2">
            <span class="style3">
            Data de Processamento: <?=$row_folha['data_proc2']?>
            <br>
            Intervalo de: 
            <br>
            <?=$row_folha['data_inicio2']?> at&eacute; <?=$row_folha['data_fim2']?>
            </span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <span class="title">Folha de Produ&ccedil;&atilde;o de Cooperados -
<?=$mes_da_folha?> / <?=$ano?> - <?=$row_projeto['nome']?></span><br />
      <span class="title"><br />
    </span>
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="4%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="21%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome</td>
          <td width="10%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Produ&ccedil;&atilde;o Cooperado</td>
          <td width="4%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Horas.</td>
          <td width="5%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos</td>
          <td width="9%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Base Imposto.</td>
          <td width="5%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
          <td width="5%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">IRRF</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Quota</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Beneficios</td>
           <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Produ&ccedil;&atilde;o. L&iacute;q.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23" style="display:none">TX. Coop.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23" style="display:none">Nota Fiscal.</td>
        </tr>
       
        <?php
          $cont = "0";
		  
		  //INICIO DO PROCESSAMENTO DE CADA AUTONOMO
		  while($row = mysql_fetch_array($result_folha_pro)){
			  
		  //SELECIONA TODAS AS INFORMAÇES DE CADA AUTONOMO
		  $REparti = mysql_query("SELECT * FROM autonomo where id_autonomo = '$row[id_autonomo]'");
		  $rowP = mysql_fetch_array($REparti);
		  
		  //SELECIONA O CURSO DA PESSOA PARA SABER VALOR DE SALARIO
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);
		  
		  //SELECIONA A COOPERATIVA DE CADA PARTICIPANTE
		  $RECoope = mysql_query("SELECT * FROM cooperativas where id_coop = '$rowP[id_cooperativa]'");
		  $ROWCoope = mysql_fetch_array($RECoope);
		  
		  //VARIAVEIS DA COOPERATIVA
		  $Dat = explode("-",$rowP['data_entrada']);
		  $Dia = $Dat[2];
		  $Mes = $Dat[1];
		  $Ano = $Dat[0];
		  
		  
		  if(!($rowP['cota'] != "0")){
		  
			  $ValorFundo = $ROWCoope['fundo'];
			  $QNTParcelas = $ROWCoope['parcelas'];
			  $DATAFimQuota = date("Y-m-d", mktime(0, 0, 0, $Mes + $QNTParcelas, $Dia, $Ano));
			  
			  $data_hoje = date('Y-m-d');
			  $Meshoje = date('m');
			  
			  if($data_hoje <= $DATAFimQuota){
				  
				  $ValorQuota = $ValorFundo / $QNTParcelas;
				  //DESCOBRINDO QUAL PARCELA ESTÁ NO MES ATUAL
				  $ParcelaQuota = $Meshoje - $Mes;
				  
			  }else{
				  $ValorQuota = 0;
				  $ParcelaQuota = 0;
			  }
		  
		  }else{
			  
			  	$ValorFundo = $rowP['cota'];
		  		$QNTParcelas = $rowP['parcelas'];
				
				$ValorQuota = $ValorFundo / $QNTParcelas;
				
				$DATAFimQuota = date("Y-m-d", mktime(0, 0, 0, $Mes + $QNTParcelas, $Dia, $Ano));
				
				$data_hoje = date('Y-m-d');
			  	$Meshoje = date('m');
				
				if($data_hoje <= $DATAFimQuota){
				  
				  $ValorQuota = $ValorFundo / $QNTParcelas;
				  //DESCOBRINDO QUAL PARCELA ESTÁ NO MES ATUAL
				  $ParcelaQuota = $Meshoje - $Mes;
				  
				  }else{
					  $ValorQuota = 0;
					  $ParcelaQuota = 0;
				 }
			  
		  }
		  //INICIANDO CALCULOS DOS COOPERADOS
		  //FORMATA OS VALORES
		  //$sal_base = number_format($row_curso['valor'],2,".","");
		  $sal_base = $row_curso['valor'] ;  
		  //-----------CALCULANDO HORAS/FALTAS--------------//
		  $faltas = $row['faltas'];						//VERIFICANDO QUANTAS FALTAS TEM NO MES
		  $qnt_dias = $row_folha['qnt_dias'];			//VERIFICANDO QUANTAS DIAS TEM A FOLHA
		  $dias_trab = $qnt_dias - $faltas;		  
		  $diaria = $sal_base / 30;
		  
		  if($row_curso['hora_mes'] > '0'){
			  $h_dia = $sal_base / $row_curso['hora_mes'];
		  }else{
			  $h_dia = $sal_base / 1;
		  }
		  
		  if($row['faltas'] > '0'){
			  $H_trab = $row['faltas'];
		  }else{
			  $H_trab = $row_curso['hora_mes'];
		  }
		  
		  $sal_liq = $H_trab * $h_dia;					//SALARIO LIQUIDO BASEADO EM HORAS
		  //$sal_liq = $dias_trab * $diaria;			//SALARIO LIQUIDO BASEADO EM DIAS
		  $sessenta = $sal_liq * 0.60;
		  $quarenta = $sal_liq * 0.40;
		  $base_calculos_INSS_IR = $sessenta;
		  //-----------------------------------------//
		  
		  
		  //-----------CALCULANDO OS RENDIMENTOS -------------------//
		  $rendi = $row['adicional'];
		  $sal_liq = $sal_liq + $rendi;
		  
		  $base_calculos_INSS_IR = $base_calculos_INSS_IR + $rendi;
		  //-----------------------------------------//
		  
		  
		  //-----------CALCULANDO OS DESCONTOS -------------------
		  $desco = $row['desconto'];
		  $sal_liq = $sal_liq - $desco;
		  
		  $base_calculos_INSS_IR = $base_calculos_INSS_IR - $desco;
		  //-----------------------------------------//
		  
		  
		  //----------- COTA DA COOPERATIVA -------------------
		  
		  $sal_liq = $sal_liq - $ValorQuota;
		  
		  //-----------------------------------------//
		  
		  
		  //-----------CALCULANDO INSS -------------------
		  
		  if($base_calculos_INSS_IR < 0){
			  $base_calculos_INSS_IR = 0;
		  }
		  
		  $taxa_inss = $rowP['inss'] / 100;
		  $valor_inss = $base_calculos_INSS_IR * $taxa_inss;
		  
		  $sal_liq = $sal_liq - $valor_inss;
		  
		  $BASE_CALCULOS = $base_calculos_INSS_IR;							//FIXANDO LOGO O VALOR BASE DE IMPOSTOS
		  
		  $base_calculos_IR = $base_calculos_INSS_IR - $valor_inss;			//FIXANDO BASE DE CALCULOS DO IR
		  
		  
		  //-------------------------

		  
		  //----------- CALCULANDO DEDUÇÃO DO IMPOSTO DE RENDA -------------------
		  
		  $menor21 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$rowP[0]' and data1 > '$data_menor21' 
		  and data1 != '0000-00-00' and id_projeto = '$rowP[id_projeto]'");
		  $row_menor21 = mysql_fetch_array($menor21);

		  $menor22 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$rowP[0]' and data2 > '$data_menor21' 
		  and data2 != '0000-00-00' and id_projeto = '$rowP[id_projeto]'");
		  $row_menor22 = mysql_fetch_array($menor22);
		  
		  $menor23 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$rowP[0]' and data3 > '$data_menor21' 
		  and data3 != '0000-00-00' and id_projeto = '$rowP[id_projeto]'");
		  $row_menor23 = mysql_fetch_array($menor23);

		  $menor24 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$rowP[0]' and data4 > '$data_menor21' 
		  and data4 != '0000-00-00' and id_projeto = '$rowP[id_projeto]'");
		  $row_menor24 = mysql_fetch_array($menor24);

		  $menor25 = mysql_query("SELECT count(data1)as cont FROM dependentes where id_bolsista = '$rowP[0]' and data5 > '$data_menor21' 
		  and data5 != '0000-00-00' and id_projeto = '$rowP[id_projeto]'");
		  $row_menor25 = mysql_fetch_array($menor25);
		  
		  $total_filhos_menor_21 =  $row_menor21['0'] + $row_menor22['0'] + $row_menor23['0'] + $row_menor24['0'] + $row_menor25['0'];
		  
		  
		  $result_deducao_ir = mysql_query("SELECT * FROM rh_movimentos where cod = '5049'");
		  $row_deducao_ir = mysql_fetch_array($result_deducao_ir);
		  
		  if($total_filhos_menor_21 != 0 and $base_calculos_IR > "$row_deducao_ir[v_ini]"){
		  	$filhos_deducao = $total_filhos_menor_21;
		   	$valor_deducao_ir = $total_filhos_menor_21 * $row_deducao_ir['fixo'];
			
			$base_calculos_IR = $base_calculos_IR - $valor_deducao_ir;
		  }
		  
		  // --------------- CALCULANDO IMPOSTO DE RENDA -------------
		  $result_IR = mysql_query("SELECT * FROM rh_movimentos where cod = '5021' and v_ini <= '$base_calculos_IR' and 
          v_fim >= '$base_calculos_IR'");
		  $row_IR = mysql_fetch_array($result_IR);
		  
		  $valor_IR = $base_calculos_IR * $row_IR['percentual'] - $row_IR['fixo'];
		  
		  $sal_liq = $sal_liq - $valor_IR;
		  
		  
		   
		  $TXIrrf = $row_IR['percentual'] * 100;
		  //-------------------------
		  
		  //-- CALCULOS DA COOPERATIVA -------------------
		  $TxCoope = $ROWCoope['taxa'];
		  $TXOperacioanl = $sal_liq * $TxCoope;
		  
		  $NotaFiscal = $sal_liq + $TXOperacioanl + $valor_inss + $valor_IR;
		  
		  //-------------------
		  
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
		  $sessentaF = number_format($sessenta,2,",",".");
		  $quarentaF = number_format($quarenta,2,",",".");
		  $sal_baseF = number_format($sal_base,2,",",".");
		  $base_calculos_INSS_IRF = number_format($BASE_CALCULOS,2,",",".");
		  $rendiF = number_format($rendi,2,",",".");
		  $descoF = number_format($desco,2,",",".");
		  $sal_liqF = number_format($sal_liq,2,",",".");
		  $ValorINSSF = number_format($valor_inss,2,",",".");
		  $ValorQuotaF = number_format($ValorQuota,2,",",".");
		  $valor_IRF = number_format($valor_IR,2,",",".");
		  $TXOperacioanlF = number_format($TXOperacioanl,2,",",".");
		  $NotaFiscalF = number_format($NotaFiscal,2,",",".");
		  $BaseImpostoF = number_format($BaseImposto,2,",",".");
		  //-- FORMATO USA
		  $sal_baseT = number_format($sal_base,2,".","");
		  $base_calculos_INSS_IRT = number_format($base_calculos_INSS_IR,2,".","");
		  $base_calculos_SO_IRT = number_format($base_calculos_IR,2,",",".");	//ja com deduçao de IRRF e valor do INSS
		  $sal_liqT = number_format($sal_liq,2,".","");
		  $ValorINSST = number_format($valor_inss,2,".","");
		  $ValorQuotaT = number_format($ValorQuota,2,".","");
		  $valor_IRT = number_format($valor_IR,2,".","");
		  $TXOperacioanlT = number_format($TXOperacioanl,2,".","");
		  $NotaFiscalT = number_format($NotaFiscal,2,".","");
		  $sessentaT = number_format($sessenta,2,".","");
		  $quarentaT = number_format($quarenta,2,".","");
		  //-- ENCRIPTOGRAFANDO A VARIAVEL
		  $linkfalt = encrypt("$row[0]&1&$folha"); 
		  $linkfalt = str_replace("+","--",$linkfalt);
		  // -----------------------------
	  		  
		 
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  if($rowP['inss'] < 11){
			  $InssPrint = "($rowP[inss]%)";
		  }
		  //-----------------
		  
		  print"
		  <tr height='20' class='style28' bgcolor=$color>
          <td align='center' valign='middle' $bord> $rowP[campo3]</td>
          <td align='lefth' valign='middle' $bord>
		  <a href='faltas.php?enc=$linkfalt' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\">$nomeT</a> </td>
          <td align='right' valign='middle' $bord>$sal_baseF</td>
          <td align='right' valign='middle' $bord>$H_trab &nbsp;</td>
		  <td align='right' valign='middle' $bord>$rendiF</td>
          <td align='right' valign='middle' $bord>$descoF</td>
		  <td align='right' valign='middle' $bord>$base_calculos_INSS_IRF</td>
          <td align='right' valign='middle' $bord>$InssPrint $ValorINSSF</td>
		  <td align='right' valign='middle' $bord>$valor_IRF</td>
		  <td align='right' valign='middle' $bord>$ValorQuotaF</td>
		  <td align='right' valign='middle' $bord>$quarenta</td>
	      <td align='right' valign='middle' $bord>$sal_liqF</td>
		  <td align='right' valign='middle' $bord style='display:none'>$TXOperacioanlF</td>
		  <td align='right' valign='middle' $bord style='display:none'>$NotaFiscalF</td>
		  </tr>";
		  
		  $PARTE1 = $PARTE1."UPDATE folha_cooperado SET salario = '$sal_baseT', parte1 = '$sessentaT', parte2 = '$quarentaT', salario_liq = '$sal_liqT', h_trab = '$H_trab', h_mes = '$row_curso[hora_mes]', inss = '$ValorINSST', t_inss = '$rowP[inss]' , irrf = '$valor_IRT', t_irrf = '$TXIrrf', d_irrf = '$valor_deducao_ir', quota = '$ValorQuotaT', p_quota = '$ParcelaQuota', base_imposto = '$base_calculos_INSS_IRT', base_irrf = '$base_calculos_IR', taxa_ope = '$TXOperacioanlT', t_ope = '$ROWCoope[taxa]' , nota = '$NotaFiscalT', faltas = '$H_trab', status = '3' WHERE id_folha_pro = '$row[0]';\r\n";
		  $cont ++;
		  
		  
		  //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		  $TOsal_base = $TOsal_base + $sal_base;
		  $TOsal_liq = $TOsal_liq + $sal_liq;
		  $TOrendi = $TOrendi + $rendi;
		  $TOdesco = $TOdesco + $desco;
		  $TOINSS = $TOINSS + $valor_inss;
		  $TOValorQuota = $TOValorQuota + $ValorQuota;
		  $TOvalor_IR = $TOvalor_IR + $valor_IR;
		  $TOBaseImposto = $TOBaseImposto + $BASE_CALCULOS;
		  $TOTXOperacioanl = $TOTXOperacioanl + $TXOperacioanl;
		  $TONotaFiscal = $TONotaFiscal + $NotaFiscal;
		  $TOquarenta = $quarenta + $quarenta;
		  //-- LIMPANDO VARIAVEIS
		  unset($sal_base);
		  unset($sal_liq);
		  unset($rendi);
		  unset($desco);
		  unset($faltas);
		  unset($dias_trab);
		  unset($diaria);
		  unset($ValorINSS);
		  unset($ValorQuota);
		  unset($valor_IR);
		  unset($BASE_CALCULOS);
		  unset($InssPrint);
		  unset($valor_deducao_ir);
		  }
		
		
		//-- FORMATANDO OS TOTAIS FORMATO BRASILEIRO--//
		  $Fsal_base2 = number_format($Fsal_base2,2,",",".");
		  $TOsal_baseF = number_format($TOsal_base,2,",",".");
		  $TOsal_liqF = number_format($TOsal_liq,2,",",".");
		  $TOrendiF = number_format($TOrendi,2,",",".");
		  $TOdescoF = number_format($TOdesco,2,",",".");
		  $TOINSSF = number_format($TOINSS,2,",",".");
		  $TOValorQuotaF = number_format($TOValorQuota,2,",",".");
		  $TOvalor_IRF = number_format($TOvalor_IR,2,",",".");
		  $TOBaseImpostoF = number_format($TOBaseImposto,2,",",".");
		  $TOTXOperacioanlF = number_format($TOTXOperacioanl,2,",",".");
		  $TONotaFiscalF = number_format($TONotaFiscal,2,",",".");
		
		//-- CALCULANDO A ATAXA ADMINISTRATIVA
			
			$ValorTaxaADM = $TOsal_liq * 0.04;
			$ValorTaxaADMF = number_format($ValorTaxaADM,2,",",".");
			
			
			$ValorFinalTotalGeral = $TOsal_liq - $ValorTaxaADM;
			$ValorFinalTotalGeralF = number_format($ValorFinalTotalGeral,2,",",".");
		
		//--------------------
		
		//CALCULANDO TOTAIS FINAIS
			
			$IRRF_Final_nota = $TONotaFiscal * 0.015;
			$PIS_Final_nota = $TONotaFiscal * 0.0065;
			$COFINS_Final_nota = $TONotaFiscal * 0.3;
			$INSS_coop_final = $TONotaFiscal * 0.15;
			$ISS_Final_nota = $TONotaFiscal * $ROWCoope['iss'];
			//$Valor_Deposito_FinalF = 
			
			$IRRF_Final_notaF = number_format($IRRF_Final_nota,2,",",".");
			$PIS_Final_notaF = number_format($PIS_Final_nota,2,",",".");
			$COFINS_Final_notaF = number_format($COFINS_Final_nota,2,",",".");
			$INSS_coop_finalF = number_format($INSS_coop_final,2,",",".");
			$ISS_Final_notaF = number_format($ISS_Final_nota,2,",",".");
			//$Valor_Deposito_FinalF
		
		//---------------------
		?>
        
         <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_baseF?></td>
          <td align="right" valign="bottom" class="style23">&nbsp;</td>
          <td align="right" valign="bottom" class="style23"><?=$TOrendiF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOdescoF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOBaseImpostoF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOINSSF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOvalor_IRF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOValorQuotaF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOquarenta?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_liqF?></td>
          <td align="right" valign="bottom" class="style23" style="display:none"><?=$TOTXOperacioanlF?></td>
          <td align="right" valign="bottom" class="style23" style="display:none"><?=$TONotaFiscalF?></td>
         </tr>
        
      </table>
      <br />
      <?=$mensagem?>
      <br>
      
      <br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="linha">TOTALIZADORES</span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Per&iacute;odo:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<?=$row_folha['data_inicio2']." até ".$row_folha['data_fim2']." - ".$row_folha['qnt_dias']." dias"?></td>
        </tr>
        <tr>
          <td width="46%" height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> Retirada L&iacute;quida:</span></td>
          <td width="54%" height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">
            &nbsp;&nbsp;<span class="style23">
            <?=$TOsal_liqF?>
            </span></span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> Base Valor Produ&ccedil;&atilde;o:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha"><span class="style23">
            &nbsp;&nbsp;
            <?=$TOsal_baseF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">INSS:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp; <?=$TOINSSF?></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">IRRF:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">
            <?=$TOvalor_IRF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Quotas:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp; <?=$TOValorQuotaF?></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Desconto:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">
            <?=$TOdescoF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Rendimento:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">
            <?=$TOrendiF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Cooperados  Listados:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=$cont?></td>
        </tr>
        
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Total folha:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">
            <?=$TOsal_liqF?>
          </span></td>
        </tr>
      </table>
      <br>
      <br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0" style="display:none">
        <tr>
          <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="linha">TOTALIZADORES COOPERATIVA</span></td>
        </tr>
        <tr>
          <td width="46%" height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title">&nbsp;</td>
          <td width="54%" height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> &nbsp;&nbsp;</span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">IRRF (1,5%):</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha"><span class="style23"> &nbsp;&nbsp;</span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">PIS (0,65%):</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">COFINS (3%):</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">VR DEPOSITO:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;</td>
        </tr>
      </table>
      <br>
<br>
<?php
	
	
//-- FORMATANDO OS TOTAIS FORMATO E.U.A. PARA SEREM GRAVADOS NO ARQUIVO TXT--//

		  $TOsal_baseT = number_format($TOsal_base,2,".","");
		  $TOsal_liqT = number_format($TOsal_liq,2,".","");
		  $TOrendiT = number_format($TOrendi,2,".","");
		  $TOdescoT = number_format($TOdesco,2,".","");		


$TERCEIRA_PARTE = "UPDATE folhas SET participantes = '$cont', rendimentos = '$TOrendiT ', descontos = '$TOdescoT', total_bruto = '$TOsal_baseT', total_liqui = '$TOsal_liqT', status = '3' WHERE id_folha = '$folha' LIMIT 1 ;\r\n";
	
		
		$conteudo = $PARTE1."$TERCEIRA_PARTE"."\r\n";

		
		$nome_arquivo_download = "cooperado_".$folha.".txt";
		$arquivo = "/home/ispv/public_html/intranet/arquivos/folhacooperado/".$nome_arquivo_download;

		//TENTA ABRIR O ARQUIVO TXT
		if (!$abrir = fopen($arquivo, "wa+")) {
		echo "Erro abrindo arquivo ($arquivo)";
		exit;
		}

		//ESCREVE NO ARQUIVO TXT
		if (!fwrite($abrir, $conteudo)) {
		print "Erro escrevendo no arquivo ($arquivo)";
		exit;
		}

		//FECHA O ARQUIVO
		fclose($abrir);
    
	//-- ENCRIPTOGRAFANDO A VARIAVEL
	$linkvolt = encrypt("$regiao&$regiao"); 
	$linkvolt = str_replace("+","--",$linkvolt);
	
	$add = encrypt("$regiao&$folha"); 
	$add = str_replace("+","--",$add);
	
	$linkFim = encrypt("$regiao&$folha"); 
	$linkFim = str_replace("+","--",$linkFim);
	// -----------------------------

?>

</td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='javascript:window.location.reload()' style="text-decoration:none; color:#000">ATUALIZAR</a></b>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b><a href='add.php?<?="enc=".$add."&tela=1"?>' style="text-decoration:none; color:#000">ADICIONAR COOPERADO</a></b>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b><a href='folha.php?id=9&<?="enc=".$linkvolt?>' style="text-decoration:none; color:#000">VOLTAR</a></b>
    
    <br>
    
    <b><a href='acao_folha.php?<?="enc=".$linkFim?>' style="text-decoration:none; color:#000">FINALIZAR</a></b>

    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>
<?php
/*CASO ERRO DE CALCULOS VERIFICAR OS DADOS
VERIFICAR A DATA DE ENTRADA DOS PARTICIPANTES
VERIFICAR A COOPERATIVA DOS PARTICIPANTES
VERIFICAR O VALOR DA ATIVIDADE (PONTUAÇÃO)
VERIFICAR FALTAS OU ADICIONAIS

*/
?>