<?php
if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='www.netsorrindo.com.br/intranet/login.php'>Logar</a>";
	exit;
}

include('../conn.php');
include('../funcoes.php');
include('../classes/calculos.php');
include('../classes/regiao.php');

$Regi = new regiao();
$Calc = new calculos();

// RECEBENDO A VARIAVEL CRIPTOGRAFADA
$enc  = $_REQUEST['enc'];
$enc  = str_replace("--","+",$enc);
$link = decrypt($enc); 
$decript = explode("&",$link);
$regiao  = $decript[0];
$folha   = $decript[1];
// RECEBENDO A VARIAVEL CRIPTOGRAFADA

// SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

// SELECIONANDO O INSTITUTO PARAR CARREGAR A LOGO
// SELECIONANDO A FOLHA
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

if($row_folha['terceiro'] == 1) {
	$tipo_terceiro = array('', 'Primeira Parcela', 'Segunda Parcela', 'Integral');
	$mensagem = 'Abono Natalino '.$tipo_terceiro[$row_folha['tipo_terceiro']];
} else {
	$mensagem = "$mes_da_folha / $row_folha[ano]";
}

$titulo = "Folha Autonomo: Projeto $row_projeto[nome] ".$mensagem;
$mesAnte = $mes_int - 1;
$mesAnte = sprintf("%02d", $mesAnte);
$data_base = explode("-",$row_folha['data_inicio']);
$ano_base = $data_base[0];
$titulo = "Folha Autonomo: Projeto $row_projeto[nome] mês de $mes_da_folha";
$ano  = date("Y");
$mes  = date("m");
$dia  = date("d");
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
$bonificacao	= $CoopGeral -> bonificacao;

//$Gfoto			= $CoopGeral -> foto;
if($Gfoto == "0"){
	$LogoCoop = "";
}else{
	$LogoCoop = "<img src='../cooperativas/logos/coop_".$Gid_coop.$Gfoto."' alt='' width='110' height='79' align='absmiddle' >";
}
//--------------------------------------------

// Definindo Usuários para Finalizar a Folha
$acesso_finalizacao = array('9','20','33','77', '5');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>:: Intranet ::</title>
<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript" src="../js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="../js/lightbox.js"></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../js/ramon.js"></script>
<link href="../net1.css" rel="stylesheet" type="text/css" />
<link href="../js/highslide.css" rel="stylesheet" type="text/css" />
<link href="../js/lightbox.css" rel="stylesheet" type="text/css" media="screen"/>
<script type="text/javascript">
$().ready(function(){
	$(".ajuda").change(function(){
		$.post('update_ajuda.php',{'ID' : $(this).next().val(), 'valor' : $(this).val()}, function(dados){
			
		});
	});
});
</script>
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
<style type="text/css">
body {
	margin:0;
}
.style29 {
	font-family: arial;
	font-weight: bold;
}
.style30 {
	color:#630;
}
.borda { 
	border-bottom:#aaa solid 1px;
}
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
<table width="99%" border="0" align="center" class="bordaescura1px">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><div style="font-size:9px; text-align:left; color:#E2E2E2;"><b>ID:
      <?php
    echo $folha.", regi&atilde;o: ";
	$Regi -> MostraRegiao($row_folha['regiao']);
	echo $Regi -> regiao;
	echo ($row_folha['contratacao'] == 4) ? " Autonomo PJ" : " Cooperado";
	#echo " Cooperado";
	?>
    </b></div>
      <table width="90%" border="0" align="center" class="bordaescura1px">
        <tr>
        <td width="100%" height="81" align="center" valign="middle" bgcolor="#003300" class="show">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr class="Texto10">
              <td width="16%" height="98" align="center" valign="middle" bgcolor="#e2e2e2"><span class="style3">
              <?=$LogoCoop?></span></td>
            <td width="62%" bgcolor="#e2e2e2"><?=$Gnome?><br>
              CNPJ : <?=$Gcnpj?>              <br></td>
            <td width="22%" bgcolor="#e2e2e2">
            Data de Processamento: <?=$row_folha['data_proc2']?>
            <br>
            Intervalo de: 
            <br>
            <?=$row_folha['data_inicio2']?> at&eacute; <?=$row_folha['data_fim2']?></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <span class="title">Folha de Produ&ccedil;&atilde;o de Cooperados - <?=$mensagem?> - <?=$row_projeto['nome']?></span><br />
      <span class="title"><br />
    </span>
      <table align="center" cellpadding="4" cellspacing="1" style="width:95%; border:1px solid #ddd;">
        <tr style="text-align:center; background-color:#CCC; font-weight:bold;">
          <td width="4%" class="borda">COD</td>
          <td width="20%" style="text-align:left;" class="borda">NOME</td>
          <td width="10%" class="borda">PRODU&Ccedil;&Atilde;O</td>
          <?php if($row_folha['terceiro'] == 1) { print '	
		  <td width="5%" class="borda">MESES</td>'; } ?>
          <td width="5%" class="borda">HORAS</td>
          <td width="5%" class="borda">RENDIMENTO</td>
          <td width="6%" class="borda">DESCONTO</td>
          <td width="8%" class="borda">BASE</td>
          <td width="8%" class="borda">INSS</td>
          <td width="6%" class="borda">IR</td>
          <td width="6%" class="borda">QUOTA</td>
          <td width="6%" class="borda">AJUDA CUSTO</td>
          <td width="6%" class="borda">PRODU&Ccedil;&Atilde;O LIQUIDA</td>
<?php if($id_user == '555' or $id_user == '999') { ?>
       	  	<td width="5%">TAXA COOPERATIVA</td>
            
          <?php } ?>
          <td width="8%" class="borda"><span style="display:yes;">NOTA FISCAL</span></td>
        </tr>
       
       <?php $cont = 0;
		  
		  // INICIO DO PROCESSAMENTO DE CADA AUTONOMO
		  while($row = mysql_fetch_array($result_folha_pro)) {
			  
		  // SELECIONA TODAS AS INFORMAÇES DE CADA AUTONOMO
		  $REparti = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row[id_autonomo]'");
		  $rowP    = mysql_fetch_array($REparti);
		  
		  // SELECIONA O CURSO DA PESSOA PARA SABER VALOR DE SALARIO
		  $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$rowP[id_curso]'");
		  $row_curso    = mysql_fetch_array($result_curso);
		  
		  // SELECIONA A COOPERATIVA DE CADA PARTICIPANTE
		  $RECoope  = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$rowP[id_cooperativa]'");
		  $ROWCoope = mysql_fetch_array($RECoope);
		  
		  // Meses
		  list($ano_entrada,$mes_entrada,$dia_entrada) = explode('-', $rowP['data_entrada']);
	
		  // 2010 == 2010
		  if($ano_entrada == $row['ano']) {  
			
			  // 12 != 12
			  if($mes_entrada != $row['mes']) {
				  $meses_trab = 12 - $mes_entrada;
			  } else {$meses_trab = 0;}
			
			  if($dia_entrada <= 15) {
				  $meses_trab += 1;
			  }
			
		  } else {
			
			  $meses_trab = 12;
			
		  }

		  // Quota
		  if($folha != '693' or strstr($rowP['nome'],'MARIA CRISTINA DE OLIV') or strstr($rowP['nome'],'LUCIANA APARECIDA AL') or strstr($rowP['nome'],'CRISTIANO DOS SAN') or strstr($rowP['nome'],'JUSSARA CASAROTI F')) {
			  
			  $qr_valor_quota_paga = mysql_query("SELECT SUM(a.quota) FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$row[id_autonomo]' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3'");
			  $valor_quota_paga = @mysql_result($qr_valor_quota_paga,0);
	
			  $valor_quota_total = $rowP['cota'];
			  $numero_parcelas   = $rowP['parcelas'];
			  @ $valor_parcela     = $valor_quota_total / $numero_parcelas;
			  
			  if($valor_quota_paga < $valor_quota_total) {
				  if(($valor_quota_paga + $valor_parcela) > $valor_quota_total) {
					  $valor_quota = $valor_quota_total - $valor_quota_paga;
				  } else {
					  $valor_quota = $valor_parcela;
					  
						  if($row_folha['terceiro'] == 1) {
							  $valor_quota = 0;
						  }
				  }
				  $parcela_quota = mysql_num_rows(mysql_query("SELECT * FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$row[id_autonomo]' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3'")) + 1;
			  } else {
				  $valor_quota   = 0;
				  $parcela_quota = 0;
			  }
		  
		  }
		  

		  //INICIANDO CALCULOS DOS COOPERADOS
		  //FORMATA OS VALORES
		  //$sal_base = number_format($row_curso['valor'],2,".","");

		  if($row_folha['terceiro'] == 1) {
		  	  $sal_base = ($row_curso['salario'] / 12) * $meses_trab;
		  } else {
			  $sal_base = $row_curso['salario'] ;
		  }  
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
		  
		  if($row['faltas'] > '0') {
			  $H_trab = $row['faltas'];
		  }else{
			  $H_trab = $row_curso['hora_mes'];
		  }
		  
		  $sal_liq = $H_trab * $h_dia;					//SALARIO LIQUIDO BASEADO EM HORAS
		  //$sal_liq = $dias_trab * $diaria;			//SALARIO LIQUIDO BASEADO EM DIAS
		 // $sessenta = $sal_liq * 0.60;
		//  $quarenta = $sal_liq * 0.40;
		  $base_calculos_INSS_IR = $sal_liq;
		  //-----------------------------------------//
		  
		  
		  //-----------CALCULANDO OS RENDIMENTOS -------------------//
		  $rendi = $row['adicional'] + $bonificacao;
		  $sal_liq = $sal_liq + $rendi;
		  
		  $base_calculos_INSS_IR = $base_calculos_INSS_IR + $rendi;
		  //-----------------------------------------//
		  
		  
		  //-----------CALCULANDO OS DESCONTOS -------------------
		  $desco = $row['desconto'];
		  $sal_liq = $sal_liq - $desco;
		  
		  $base_calculos_INSS_IR = $base_calculos_INSS_IR - $desco;
		  //-----------------------------------------//
		  
		  
		  //----------- COTA DA COOPERATIVA -------------------
		  
		  $sal_liq = $sal_liq - $valor_quota;
		  
		  //-----------------------------------------//
		  
		  
		  //-----------CALCULANDO INSS -------------------
		  //SE FOR IGUAL A 1 ENÃO O INSS VAI SER UM VALOR FIXO, EDITADO NA TELA DE EDIÇÃO DOS COOPERADOS
		  // CASO SEJE COOPERADO VAI RODAR ESSE INSS
		  $result_inss = mysql_query("SELECT faixa, fixo, percentual, piso, teto
		                                FROM rh_movimentos
									   WHERE cod = '5024'
										 AND CURDATE() BETWEEN data_ini AND data_fim");
		  $row_inss = mysql_fetch_array($result_inss);
		  
		  $MinimoINSS = $row_inss['piso'];
		  $MaximoINSS = $row_inss['teto'];
		  
		  //$MinimoINSS = 40.80;
		  //$MaximoINSS = 381.41; // 375.82;
		  
		$ajuda_custo = $row['ajuda_custo'];
		  // VERIFICA SE É VALOR DEFINIDO OU SE É PERCENTUAL
		  if($rowP['tipo_inss'] == 1) {	// VALOR DEFINIDO
			  
			  $valor_inss = $rowP['inss'];
			  $sal_liq = $sal_liq - $valor_inss;
			  
		  } else {		// PERCENTUAL
			  
			  if($rowP['tipo_contratacao'] == 3) {
				  $taxa_inss = $rowP['inss'] / 100;
				  $valor_inss = $base_calculos_INSS_IR * $taxa_inss;
			  } else {
				  $taxa_inss = 0.2;
				  $valor_inss = $base_calculos_INSS_IR * $taxa_inss;
			  }
			  
			  
			  	#ARREDONDAMENTO PARA BAIXO
			  	$inss_saldo_salario   = number_format($valor_inss,3,".","");
				$inss_saldo_salarioex = explode(".",$inss_saldo_salario);
				$decimal = substr($inss_saldo_salarioex[1], 0, 2); 
					
				$valor_inss = $inss_saldo_salarioex[0].".".$decimal;
		  
			  //VERIFICANDO SE O VALOR JA CALCULADO DO INSS ULTRAPASSA O TETO MAXIMO DE INSS
			  if($valor_inss > $MaximoINSS){
				  $valor_inss = $MaximoINSS;
			  }
		  	  
			  $sal_liq = $sal_liq - $valor_inss + $ajuda_custo;
			  
		  } // FIM DA VERIFICAÇÃO SE É VALOR DEFINIDO OU SE É PERCENTUAL

		  $BASE_CALCULOS = $base_calculos_INSS_IR;
		  
		  //FIXANDO LOGO O VALOR BASE DE IMPOSTOS
		  $base_calculos_IR = $base_calculos_INSS_IR - $valor_inss;			//FIXANDO BASE DE CALCULOS DO IR
		  
		  //-------------------------
		  
		  //----------- CALCULANDO IMPOSTO DE RENDA -------------------
		  
		  //DEFININDO O VALOR MINIMO DO IR
		  $MinimoIR = 10.00;
		  
		  //VERIFICANDO SE EXISTE ALGUM VESTIGIO DE IR DO MES ANTERIOR
		  $REVestIR = mysql_query("SELECT irrf FROM folha_cooperado WHERE mes = '$mesAnte' and id_autonomo = '$rowP[0]' 
		  and status = '3' and irrf <= '$MinimoIR'");
		  $numVetIR = mysql_num_rows($REVestIR);
		  
		  if($numVetIR != 0){
			  $RowVetIR = mysql_fetch_array($REVestIR);
			  $ValorVestigio = $RowVetIR['irrf'];
		  }else{
			  $ValorVestigio = 0;
		  }
		  
		  //VALOR DO IR SERÁ: BASE DE CALCULO VEZES PERCENTUAL DA FAIXA DO IR MENOS O VALOR FIXO SOMADO AO VESTIGIO DE IR CASO HAJA.
		  $Calc -> MostraIRRF($base_calculos_IR,$rowP[0],$rowP['id_projeto'],$row_folha['data_inicio2']); // cop
		  $valor_IR = $Calc -> valor + $ValorVestigio;
		  
		  #$valor_IR = (($base_calculos_IR * $row_IR['percentual']) - $row_IR['fixo']) + $ValorVestigio;
		  
		  $gravarIR = $valor_IR;		//essa variavel vai ser uzada somente para gravar na base de dados
  
		  if($valor_IR >= $MinimoIR){
			  $sal_liq = $sal_liq - $valor_IR;
		  }else{
			  $valor_IR = 0;
		  }
		  
		  
		  $TXIrrf = $row_IR['percentual'] * 100;
		  //-------------------------
		  
		  
		  //-- CALCULOS DA COOPERATIVA -------------------
		  $TxCoope = $ROWCoope['taxa'];
		  #$TXOperacioanl = $sal_liq * $TxCoope;
		  $TXOperacioanl = $BASE_CALCULOS * $TxCoope;
		  
		  $NotaFiscal = $sal_liq + $TXOperacioanl + $valor_inss + $valor_IR + $valor_quota;
		  
		  //-------------------
		  
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
		  //$sessenta = number_format($sessenta,2,",",".");
		//  $quarenta = number_format($quarenta,2,",",".");
		  $sal_baseF = number_format($sal_base,2,",",".");
		  $base_calculos_INSS_IRF = number_format($BASE_CALCULOS,2,",",".");
		  $rendiF = number_format($rendi,2,",",".");
		  $descoF = number_format($desco,2,",",".");
		  $sal_liqF = number_format($sal_liq,2,",",".");
		  $ValorINSSF = number_format($valor_inss,2,",",".");
		  $ValorQuotaF = number_format($valor_quota,2,",",".");
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
		  $ValorQuotaT = number_format($valor_quota,2,".","");
		  $valor_IRT = number_format($valor_IR,2,".","");
		  $TXOperacioanlT = number_format($TXOperacioanl,2,".","");
		  $NotaFiscalT = number_format($NotaFiscal,2,".","");
		  $gravarIRT = number_format($gravarIR,2,".","");
		  //-- ENCRIPTOGRAFANDO A VARIAVEL
		  $linkfalt = encrypt("$row[0]&1&$folha"); 
		  $linkfalt = str_replace("+","--",$linkfalt);
		  // -----------------------------  
		 
		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  #if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $color = ($cont % 2) ? 'corfundo_um' : 'corfundo_dois';
		  $nome = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#ccc solid 1px;'";
		  
		  if($rowP['inss'] < 11 and $rowP['tipo_inss'] == 2){
			  $InssPrint = "($rowP[inss]%)";
		  }elseif($rowP['tipo_inss'] == 1){
			  $InssPrint = "(v.d.)";
		  }
		  
		  $vest = ($ValorVestigio == 0) ? "" : "*";
			  
		  //-----------------
		  
		  print"
		  <tr class='novalinha $color'>
          <td align='center' valign='middle' $bord>$rowP[campo3]</td>
          <td align='left' valign='middle' $bord>
		  <a href='faltas.php?enc=$linkfalt' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\">$nomeT</a></td>
          <td align='center' valign='middle' $bord>$sal_baseF</td>";
		  if($row_folha['terceiro'] == 1) { print "
		  <td align='center' valign='middle' $bord>$meses_trab</td>"; }
		  print "
          <td align='center' valign='middle' $bord>$H_trab &nbsp;</td>
		  <td align='center' valign='middle' $bord>$rendiF</td>
          <td align='center' valign='middle' $bord>$descoF</td>
		  <td align='center' valign='middle' $bord>$base_calculos_INSS_IRF</td>
          <td align='center' valign='middle' $bord>$InssPrint $ValorINSSF</td>
		  <td align='center' valign='middle' $bord>$valor_IRF $vest</td>
		  <td align='center' valign='middle' $bord><a href='relacao_quotas.php?id=$row[id_autonomo]' onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\">$ValorQuotaF</a></td>
		  <td align='center' valign='middle' $bord><input style=\"text-align:center;\" name=\"ajuda\" type=\"text\" class=\"ajuda\" size=\"5\" onKeyDown=\"FormataValor(this,event,20,2)\" value=\"$ajuda_custo\"/><input type=\"hidden\" name=\"ID\" class=\"ID\" value=\"$row[id_folha_pro]\"/></td>
	      <td align='center' valign='middle' $bord>$sal_liqF</td>";
		  if($id_user == '555' or $id_user == '999') {
		  	  print "<td align='center' valign='middle' $bord>$TXOperacioanlF</td>";
		  }
		  print "
<td align='center' valign='middle' $bord style='display:yes'>$NotaFiscalF</td>
		  </tr>";
		  
		  $PARTE1 = $PARTE1."UPDATE folha_cooperado SET salario = '$sal_baseT', salario_liq = '$sal_liqT', meses_trab = '$meses_trab', h_trab = '$H_trab', h_mes = '$row_curso[hora_mes]', inss = '$ValorINSST', t_inss = '$rowP[inss]' , irrf = '$gravarIRT', t_irrf = '$TXIrrf', d_irrf = '$valor_deducao_ir', quota = '$ValorQuotaT', p_quota = '$parcela_quota', base_imposto = '$base_calculos_INSS_IRT', base_irrf = '$base_calculos_IR', taxa_ope = '$TXOperacioanlT', t_ope = '$ROWCoope[taxa]' , nota = '$NotaFiscalT', faltas = '$H_trab', status = '3' WHERE id_folha_pro = '$row[0]';\r\n";
		  $cont ++;
		  
		  
		  //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		  $TOsal_base = $TOsal_base + $sal_base;
		  $TOsal_liq = $TOsal_liq + $sal_liq;
		  $TOrendi = $TOrendi + $rendi;
		  $TOdesco = $TOdesco + $desco;
		  $TOINSS = $TOINSS + $valor_inss;
		  $TOValorQuota = $TOValorQuota + $valor_quota;
		  
		  $TOAjudaCusto += $ajuda_custo;
		  $TOvalor_IR = $TOvalor_IR + $valor_IR;
		  $TOBaseImposto = $TOBaseImposto + $BASE_CALCULOS;
		  $TOTXOperacioanl = $TOTXOperacioanl + $TXOperacioanl;
		  $TONotaFiscal = $TONotaFiscal + $NotaFiscal;
		  $TOValorVestigio = $TOValorVestigio + $ValorVestigio;
		  //$TOquarenta = $quarenta + $quarenta;
		  //-- LIMPANDO VARIAVEIS
		  unset($sal_base);
		  unset($sal_liq);
		  unset($rendi);
		  unset($desco);
		  unset($faltas);
		  unset($dias_trab);
		  unset($diaria);
		  unset($ValorINSS);
		  unset($valor_quota);
		  unset($numero_parcelas);
		  
		  unset($ajuda_custo);
		  unset($valor_IR);
		  unset($BASE_CALCULOS);
		  unset($InssPrint);
		  unset($valor_deducao_ir);
		  unset($ValorVestigio);
		  }
		
		
		//-- FORMATANDO OS TOTAIS FORMATO BRASILEIRO--//
		  $Fsal_base2 = number_format($Fsal_base2,2,",",".");
		  $TOsal_baseF = number_format($TOsal_base,2,",",".");
		  $TOsal_liqF = number_format($TOsal_liq,2,",",".");
		  $TOrendiF = number_format($TOrendi,2,",",".");
		  $TOdescoF = number_format($TOdesco,2,",",".");
		  $TOINSSF = number_format($TOINSS,2,",",".");
		  $TOValorQuotaF = number_format($TOValorQuota,2,",",".");
		  
		  $TOAjudaCustoF = number_format($TOAjudaCusto,2,",",".");
	
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
          <td align="center" valign="bottom" class="style23"><?=$TOsal_baseF?></td>
          <?php if($row_folha['terceiro'] == 1) { print "
		  <td align='center' valign='middle' $bord></td>"; 
		  } ?>
          <td align="center" valign="bottom" class="style23">&nbsp;</td>
          <td align="center" valign="bottom" class="style23"><?=$TOrendiF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOdescoF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOBaseImpostoF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOINSSF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOvalor_IRF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOValorQuotaF?></td>
          <td align="center" valign="bottom" class="style23"><?=$TOAjudaCustoF?></td>
         <!-- <td align="center" valign="bottom" class="style23">  </td> -->
          <td align="center" valign="bottom" class="style23"><?=$TOsal_liqF?></td>
<?php if($id_user == '555' or $id_user == '999') { ?>
          	<td align="center" valign="bottom" class="style23"><?=$TOTXOperacioanlF?></td>
          <?php } ?>
          <td align="center" valign="bottom" class="style23" style="display:yes"><?=$TONotaFiscalF?></td>
         </tr>
		<tr>
          <td colspan="2"></td>
          <td style="text-align:center; font-weight:bold;"><?=$Fsal_base2 ?></td>
          <td colspan="11"></td>
        </tr>
        
      </table>
      <br />
      <?=$mensagem?>
      <br>
      
      <br>
      <table width="41%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordaescura1px">
        <tr>
          <td height="26" colspan="2" align="center" valign="middle" class="show">TOTALIZADORES</td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="20" align="right" valign="middle">Per&iacute;odo:</td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$row_folha['data_inicio2']." até ".$row_folha['data_fim2']." - ".$row_folha['qnt_dias']." dias"?></td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td width="36%" height="20" align="right" valign="middle">Retirada L&iacute;quida:</td>
          <td width="64%" height="20" align="left" valign="middle" >&nbsp;&nbsp;<?=$TOsal_liqF?>
            </td>
        </tr>
        <tr  class="novalinha corfundo_um">
          <td height="20" align="right" valign="middle" >Base Valor Produ&ccedil;&atilde;o:</td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$TOsal_baseF?></td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td height="20" align="right" valign="middle">INSS:</span></td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$TOINSSF?></td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="20" align="right" valign="middle">IRRF:</span></td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$TOvalor_IRF?></td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td height="20" align="right" valign="middle">Quotas:</td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$TOValorQuotaF?></td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="20" align="right" valign="middle">Desconto:</td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$TOdescoF?></td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td height="20" align="right" valign="middle">Rendimento:</td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$TOrendiF?></td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="20" align="right" valign="middle">Cooperados  Listados:</td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$cont?></td>
        </tr>
        
        <tr class="novalinha corfundo_dois">
          <td height="20" align="right" valign="middle">Total folha:</td>
          <td height="20" align="left" valign="middle">&nbsp;&nbsp;<?=$TOsal_liqF?></td>
        </tr>
      </table>
      <br>
      *v.d. ( valor definido )<br>
      <table width="30%" border="0" align="center" cellpadding="0" cellspacing="0" style="display:none">
        <tr>
          <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="linha">TOTALIZADORES COOPERATIVA</span></td>
        </tr>
        <tr>
          <td width="46%" height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title">&nbsp;</td>
          <td width="54%" height="20" align="left" valign="middle" >&nbsp;&nbsp;</span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle">IRRF (1,5%):</span></td>
          <td height="20" align="left" valign="middle"> &nbsp;&nbsp;</span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle">PIS (0,65%):</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle">COFINS (3%):</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;</td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle">VR DEPOSITO:</span></td>
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
	
	$add = encrypt("$regiao&$folha&2"); 
	$add = str_replace("+","--",$add);
	
	$linkFim = encrypt("$regiao&$folha"); 
	$linkFim = str_replace("+","--",$linkFim);
	// -----------------------------
?>
</td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC"><table width="90%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="37" align="center"><a href='javascript:window.location.reload()' class="botao">ATUALIZAR</a></td>
        <td align="center"><a href='folha2.php?<?="enc=".$add?>'  class="botao">ADICIONAR COOPERADO</a></td>
        <td align="center"><a href='folha.php?id=9&<?="enc=".$linkvolt?>' class="botao">VOLTAR</a></td>
      </tr>
      <tr>
        <td align="center">&nbsp;</td>
        <td align="center">
        <?php if(in_array($_COOKIE['logado'], $acesso_finalizacao)) { ?>
        	<a href='acao_folha.php?<?="enc=".$linkFim?>' style="text-decoration:none; color:#F00"  class="botao">FINALIZAR</a>
        <?php } ?>
        </td>
        <td align="center">&nbsp;</td>
      </tr>
    </table></td>
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