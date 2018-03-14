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
//MASTER
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//MASTER

// SELECIONANDO INFORMAÇÃO DA FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio2,date_format(data_fim, '%d/%m/%Y')as data_fim2 FROM folhas where id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

//$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' and status = '3'");
$result_folha_pro = mysql_query("SELECT * FROM folha_cooperado WHERE (id_folha = '$folha' and status = '3') or (id_folha = '$folha' and status = '4') ORDER BY nome");

$titulo = "Folha de Produção: Projeto $row_projeto[nome] mês de $mes_da_folha";

$ano = date("Y");
$mes = date("m");
$dia = date("d");

$data = date("d/m/Y");

$RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1' limit 1");
$row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

$RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2' limit 1");
$row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);


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

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$titulo?></title>
<link href="../net.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>
<script language='javascript'>

   function mascara_data(d){  
       var mydata = '';  
       data = d.value;  
       mydata = mydata + data;  
       if (mydata.length == 2){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 5){  
          mydata = mydata + '/';  
          d.value = mydata;  
       }  
          if (mydata.length == 10){  
          verifica_data(d);  
         }  
      } 
           
         function verifica_data (d) {  

         dia = (d.value.substring(0,2));  
         mes = (d.value.substring(3,5));  
         ano = (d.value.substring(6,10));  
             

       situacao = "";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = "falsa";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = "falsa";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = "falsa";  
      }  
   
     if (d.value == "") {  
          situacao = "falsa";  
    }  

    if (situacao == "falsa") {  
       alert("Data digitada é inválida, digite novamente!"); 
       d.value = "";  
       d.focus();  
    }  
	
}
</script>
<style type="text/css">
a:visited {font-size: 10px; color: #F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}
a:link{font-size: 10px; color:#F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}


</style>
</head>
<body>
<table width="95%" border="0" align="center">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center">
      <tr>
        <td width="100%" height="92" align="center" valign="middle" bgcolor="#666666" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
              <td width="16%" height="100" align="center"><span class="style1">
                <?=$LogoCoop?>
              </span></td>
            <td width="62%"><span class="style1">
              <?=$Gnome?>
              <br>
CNPJ :
<?=$Gcnpj?>
<br>
            </span></td>
            <td width="22%">
            <span class="style1">
            Data de Processamento: <br>
            <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br />
      <span class="title">Folha de Produ&ccedil;&atilde;o Cooperados - 
      <?=$mes_da_folha?> / <?=$row_folha['ano']?></span><br />
      <span class="title"><br />
    </span>
    <?
		//VERIFICA SE A FOLHA JÁ FOI FINALIZADA
		$resultStatusFolha = mysql_query("SELECT status FROM folhas WHERE id_folha = $folha");
		$rowStatusFolha = mysql_fetch_array($resultStatusFolha);
		if ($rowStatusFolha[0] == '4'){
				print "<span style='color:red; font-family:verdana, areal'> <strong>FINALIZADA</strong> </span>";
		}
	?>
    <br/>
    <br/>
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td width="4%" height="25" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">C&oacute;digo</td>
          <td width="19%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Produ&ccedil;&atilde;o Base</td>
          <td width="4%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Horas</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos</td>
          <td width="4%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
          <td width="4%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">IRRF</td>
          <td width="5%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Quota</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Beneficios</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Produ&ccedil;&atilde;o L&iacute;q.</td>
          <td width="9%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Base IMPOS</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">TX. Ope.</td>
          <td width="8%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Nota Filcal</td>
        </tr>
        <?php
          $cont = "0";
		  while($row = mysql_fetch_array($result_folha_pro)){
		  
		  $REparti = mysql_query("SELECT * FROM autonomo where id_autonomo = '$row[id_autonomo]'");
		  $rowP = mysql_fetch_array($REparti);
		  
		  $result_curso = mysql_query("SELECT * FROM curso where id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);	 
		
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
		  $sal_baseF = number_format($row['parte1'],2,",",".");
		  $beneficioF = number_format($row['parte2'],2,",",".");
		  $rendiF = number_format($row['adicional'],2,",",".");
		  $descoF = number_format($row['desconto'],2,",",".");
		  $sal_liqF = number_format($row['salario_liq'],2,",",".");
		  $inssF = number_format($row['inss'],2,",",".");
		  $irrfF = number_format($row['irrf'],2,",",".");
		  $quotaF = number_format($row['quota'],2,",",".");
		  $base_impostoF = number_format($row['base_imposto'],2,",",".");
		  $taxa_opeF = number_format($row['taxa_ope'],2,",",".");
		  $notaF = number_format($row['nota'],2,",",".");
		  
		  //-- FORMATO USA
		  $sal_liqT = number_format($row['salario_liq'],2,".","");

		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  if($cont % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
		  $nome = str_split($row['nome'], 30);
		  $nomeT = sprintf("% -30s", $nome[0]);
		  $bord = "style='border-bottom:#000 solid 1px;'";
		  //-----------------

				$tipoR = $rowP['tipo_conta'];
				if ($tipoR == 'salario'){
					$checkedSalario = 'checked';	
				}else if ($tipoR == 'corrente'){
					$checkedCorrente = 'checked';
				}
		/*
		$tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
	     $rowTipoPg = mysql_fetch_array($tiposDePagamentos);
	  	  $pgEmCheque = $rowTipoPg[0];		  
		  
		  if (($row['conta'] == '') or ($row['conta'] == '0')){
		  		mysql_query("UPDATE folha_autonomo SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo]");
		  }
		  if (($row['agencia'] == '') or ($row['agencia'] == '0')){
		  		mysql_query("UPDATE folha_autonomo SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo]");
		  }if ($rowP['tipo_conta'] == ''){
		  		mysql_query("UPDATE folha_autonomo SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo]");
		  }
		 
		  
		  $resultCheque = mysql_query("SELECT tipo_pg, banco FROM folha_autonomo WHERE id_folha = '$folha' and id_autonomo = $row[id_autonomo] and status = '3'");
		  $rowTipoPg = mysql_fetch_array($resultCheque);
		  
		  if ($rowTipoPg[0] == $pgEmCheque){
			  $option ="<option value='$pgEmCheque'>Cheque</option><option value='$row_TipoDepo[0]' >Depósito</option>";		  	
		  }else{
		  		$option ="<option value='$row_TipoDepo[0]' >Depósito</option><option value='$pgEmCheque'>Cheque</option>";
		  }
		  
			$NovoBanco = "";
			$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and id_projeto = '$row_projeto[0]'");
			$result_bancoAUT = mysql_query("SELECT nome FROM bancos where id_banco = '$row[banco]'");
			$RowBancAUT = mysql_fetch_array($result_bancoAUT);
			*/
		  print"
		  <tr height='20' class='style28' bgcolor=$color>
          <td align='center' valign='middle' $bord> $rowP[campo3]</td>
          <td align='lefth' valign='middle' $bord>$alink $nomeT</a> $divTT</td>
          <td align='right' valign='middle' $bord>$sal_baseF</td>
		  <td align='right' valign='middle' $bord>$row[faltas]</td>
          <td align='right' valign='middle' $bord>$rendiF</td>
          <td align='right' valign='middle' $bord>$descoF</td>
          <td align='right' valign='middle' $bord>$inssF</td>
          <td align='right' valign='middle' $bord>$irrfF</td>
		  <td align='right' valign='middle' $bord>$quotaF</td>
		  <td align='right' valign='middle' $bord>$beneficioF</td>
		  <td align='right' valign='middle' $bord>$sal_liqF</td>
		  <td align='right' valign='middle' $bord>$base_impostoF</td>
		  <td align='right' valign='middle' $bord>$taxa_opeF</td>
		  <td align='right' valign='middle' $bord>$notaF</td>
		  </tr>";

		  $PARTE1 = $PARTE1."UPDATE folha_autonomo SET salario_liq = '$sal_liqT' WHERE id_folha_pro = '$row[0]';\r\n";
		  $cont ++;
		  //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		  $TOsal_base = $TOsal_base + $sal_base;
		  $TOBenificio = $TOBenificio + $row['parte2'];
		  $TOsal_liq = $TOsal_liq + $sal_liq; 
		  $TOrendi = $TOrendi + $rendi;
		  $TOdesco = $TOdesco + $desco;
		  $TOInss = $TOInss + $row['inss'];
		  $TOIrrf = $TOIrrf + $row['irrf'];
		  $TOquota = $TOquota + $row['quota'];
		  $TObase_imposto = $TObase_imposto + $row['base_imposto'];
		  $TOTaxaOP = $TOTaxaOP + $row['taxa_ope'];
		  $TONOTAFIS = $TONOTAFIS + $row['nota'];
		  
		  
		  //-- LIMPANDO VARIAVEIS
		  unset($sal_base);
		  unset($sal_liq);
		  unset($rendi);
		  unset($desco);
		  unset($faltas);
		  unset($dias_trab);
		  unset($diaria);
		  unset($sal_liqT);
		  
		  }
		
		
		//-- FORMATANDO OS TOTAIS FORMATO BRASILEIRO--//
		  $TOsal_baseF = number_format($row_folha['total_bruto'],2,",",".");
		  $TOsal_liqF = number_format($row_folha['total_liqui'],2,",",".");
		  $TOrendiF = number_format($row_folha['rendimentos'],2,",",".");
		  $TOdescoF = number_format($row_folha['descontos'],2,",",".");
		  
		  
		  $TOINSSF = number_format($TOInss,2,",",".");
		  $TOvalor_IRF = number_format($TOIrrf,2,",",".");
		  $TOValorQuotaF = number_format($TOquota,2,",",".");
		  $TObase_impostoF = number_format($TObase_imposto,2,",",".");
		  $ValorTaxaADMF = number_format($TOTaxaOP,2,",",".");
		  $TONotaFiscalF = number_format($TONOTAFIS,2,",",".");
		  
		  $TOBenificioF = number_format($TOBenificio,2,",",".");
		  
		
		?>
        <tr>
          <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
          <td align="right" valign="bottom" class="style23">TOTAIS:</td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_baseF?></td>
          <td align="right" valign="bottom" class="style23">&nbsp;</td>
          <td align="right" valign="bottom" class="style23"><?=$TOrendiF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOdescoF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOINSSF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOvalor_IRF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOValorQuotaF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOBenificioF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_liqF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TObase_impostoF?></td>
          <td align="right" valign="bottom" class="style23"><?=$ValorTaxaADMF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TONotaFiscalF?></td>
        </tr>
      </table>
      <br />
      <br>
      <table width="34%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="24" colspan="2" align="center" valign="middle" bgcolor="#CCCCCC" class="title"><span class="linha">TOTALIZADORES</span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Per&iacute;odo:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=$row_folha['data_inicio2']." at&eacute; ".$row_folha['data_fim2']." - ".$row_folha['qnt_dias']." dias"?></td>
        </tr>
        <tr>
          <td width="46%" height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> Retirada L&iacute;quida:</span></td>
          <td width="54%" height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> &nbsp;&nbsp;<span class="style23">
            <?=$TOsal_liqF?>
          </span></span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha"> Base Valor Produ&ccedil;&atilde;o:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha"><span class="style23"> &nbsp;&nbsp;
            <?=$TOsal_baseF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">INSS:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=$TOINSSF?></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">IRRF:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;<span class="style23">
            <?=$TOvalor_IRF?>
          </span></td>
        </tr>
        <tr>
          <td height="20" align="right" valign="middle" bgcolor="#f0f0f0" class="title"><span class="linha">Quotas:</span></td>
          <td height="20" align="left" valign="middle" bgcolor="#f0f0f0" class="linha">&nbsp;&nbsp;
            <?=$TOValorQuotaF?></td>
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
          <?=$row_folha['participantes']?></td>
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
      
<?php

		//VERIFICANDO QUAIS BANCOS ESTÃO ENVOLVIDOS COM ESSA FOLHA DE PAGAMENTO
		$RE_Bancs = mysql_query("SELECT banco FROM folha_cooperado where (id_folha = '$folha' and status = '3' and banco != '0') or 
		(id_folha = '$folha' and status = '4' and banco != '0') group by banco");
		$num_Bancs = mysql_num_rows($RE_Bancs);
		
		echo "<table border='0' width='50%' border='0' cellpadding='0' cellspacing='0'>";
		echo "<tr><td colspan=6 $bord align='center'><div style='font-size: 17px;'><b>Lista de Bancos</b></div></td></tr>";
		$contCol = 0;
		while($row_Bancs = mysql_fetch_array($RE_Bancs)){		  
				
				$RE_ToBancs = mysql_query("SELECT banco FROM folha_cooperado where id_folha = '$folha' and status = '3' and banco = '$row_Bancs[0]' 
				and tipo_pg = '$row_TipoDepo[0]'");	 
		  		$num_ToBancs = mysql_num_rows($RE_ToBancs);	
				
		  		$RE_Bancos = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_Bancs[0]'");
		  		$row_Bancos = mysql_fetch_array($RE_Bancos);
				
		  		//-- ENCRIPTOGRAFANDO A VARIAVEL
				$linkBanc = encrypt("$regiao&$row_Bancos[0]&$folha"); 
				$linkBanc = str_replace("+","--",$linkBanc);
		  		// -----------------------------
		  		$linkBank = "folha_bancocoo.php?enc=$linkBanc";
				
		  		echo "<tr>";
		 		echo "<td align='center' valign='middle' width='30' $bord>";
		  		echo "<img src=../imagens/bancos/$row_Bancos[id_nacional].jpg  width='25' height='25' align='absmiddle' border='0'></td>";
          		echo "<td valign='middle' $bord><div style='font-size: 15px;'>&nbsp;&nbsp;".$row_Bancos['nome']."</div></a></td>";	
				$resultBancosFinalizados = mysql_query("SELECT banco FROM folha_autonomo WHERE id_folha = '$folha' and status='4' and 
				banco = '$row_Bancs[0]' group by banco");
				$numBancosFinalizados = mysql_affected_rows();
				if ($numBancosFinalizados != 0){
						
						$rowBancosFinalizados = mysql_fetch_array($resultBancosFinalizados);
						$resultPartFinalizados = mysql_query("SELECT id_autonomo FROM folha_cooperado where id_folha = '$folha' and status = '4'
						and banco = '$rowBancosFinalizados[0]'");
						$numPartFinalizados = mysql_num_rows($resultPartFinalizados);
						
						print "<td  align='right' valign='bottom'  $bord>";						
						print "&nbsp;&nbsp;<a href=finalizados.php?regiao=$regiao&folha=$folha&projeto=$row_projeto[0]&banco=$rowBancosFinalizados[0]>
						FINALIZADO</a>";
						print "</td>";
						print "<td $bord>&nbsp;</td>";
						echo "<td align='center' valign='middle' width='10%' $bord>$numPartFinalizados Participantes</td>";
				}else{
							if ($num_ToBancs != 0){
							echo "<td valign='center' $bord> <form id='form$contCol' name='form$contCol' method='post' action='$linkBank'>&nbsp;<label id='data_pag$contCol' style='display:none'><input name='data' type='text' id='data[]' size='10' class='campotexto' onKeyUp='mascara_data(this)' maxlength='10' onFocus=\"this.style.background='#CCFFCC'\" onBlur=\"this.style.background='#FFFFFF'\" style='background:#FFFFFF' > <input name='enviar' id='Enviar[]' type='submit' value='Gerar'/> </label></td>";
							echo "</form>";	
							echo "<td align='center' valign='middle' width='5%' $bord><a style='TEXT-DECORATION: none;'>		  <img src='../rh/folha/imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Banco' style='cursor:hand' onClick=\"document.all.data_pag$contCol.style.display = (document.all.data_pag$contCol.style.display == 'none') ? '' : 'none' ;\"></a></td>";		  
							echo "<td align='center' valign='middle' width='10%' $bord>$num_ToBancs Participantes</td>";
							 }else{	
						 		 echo "<td $bord>&nbsp;</td>";
								 echo "<td $bord align='right'><span style='font-family:verdana, arial; font-size:9px; color:red'><strong>VERIFICAR</strong></span></td>";
								 echo "<td align='center' valign='middle' width='15%' $bord>$num_ToBancs Participantes</td>";	
						 }
				}

				
		  		$contCol ++ ;
		}
		
	  
	   	//VERIFICA OS TIPOS DE PAGAMENTOS DA REGIÃO E PROJETO ATUAL
		$tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
		$rowTipoPg = mysql_fetch_array($tiposDePagamentos);
		
		$RE_ToCheq = mysql_query("SELECT * FROM folha_cooperado WHERE id_folha = '$folha' and tipo_pg = '$rowTipoPg[id_tipopg]' and status = '3' or (id_folha = '$folha' and banco = '0' and status = '3') or (id_folha = '$folha' and agencia = '' and status = '3') or (id_folha = '$folha' and conta = '' and status = '3')");
	  	$num_ToCheq = mysql_num_rows($RE_ToCheq);	  
	  	
		//-- ENCRIPTOGRAFANDO A VARIAVEL
		$linkcheque = encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]");
		$linkcheque = str_replace("+","--",$linkcheque);
	  	// -----------------------------	  
		
		if ($num_ToCheq != 0 ){
		echo "<tr>";
		echo "<td align='center' valign='middle' width='30' $bord>";
		echo "<img src=../imagens/bancos/cheque.jpg  width='25' height='25' align='absmiddle' border='0'></td>";
        echo "<td valign='middle' $bord><div style='font-size: 15px;'>&nbsp;&nbsp;Cheque</div></a></td>";
		echo "<td valign='center' $bord>&nbsp;</td>";
		echo "<td align='center' valign='middle' width='15%' $bord><a href='ver_cheque.php?enc=$linkcheque'><img src='../rh/folha/imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Cheque'></a></td>";
		echo "<td align='center' valign='middle' width='10%' $bord>$num_ToCheq Participantes</td>"; 
		}
	  	echo "</tr></table>";
		print "<br/>";
		
	  ?>             
      <br>
<br>
<?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt = str_replace("+","--",$linkvolt);

$enc2 = str_replace("+","--",$enc);
// -----------------------------

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkcompro = encrypt("$regiao&$folha"); 
$linkcompro = str_replace("+","--",$linkcompro);

$enc3 = str_replace("+","--",$linkcompro);
// -----------------------------

?>
<br></td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <b><a href='folha.php?id=9&<?="enc=".$linkvolt."&tela=1"?>' style="text-decoration:none; color:#000">VOLTAR</a></b>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b><a href='ver_lista_banco.php?<?="enc=".$enc2?>' style="text-decoration:none; color:#000">VER LISTA POR BANCO</a></b>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <b><a href='../cooperativas/recibocoop.php?<?="enc=".$enc3?>' target="_blank" style="text-decoration:none; color:#000">RECIBOS DE PAGAMENTOS</a></b>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>