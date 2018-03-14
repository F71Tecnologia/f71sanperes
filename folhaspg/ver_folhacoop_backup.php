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
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);
//MASTER

// SELECIONANDO INFORMAÇÃO DA FOLHA
$result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio2,date_format(data_fim, '%d/%m/%Y')as data_fim2 FROM folhas WHERE id_folha = '$folha'");
$row_folha = mysql_fetch_array($result_folha);

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
$row_projeto = mysql_fetch_array($result_projeto);

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$mesINT = (int)$row_folha['mes'];
$mes_da_folha = $meses[$mesINT];

if($row_folha['terceiro'] == 1) {
	$tipo_terceiro = array('', 'Primeira Parcela', 'Segunda Parcela', 'Integral');
	$mensagem = 'Abono Natalino '.$tipo_terceiro[$row_folha['tipo_terceiro']];
} else {
	$mensagem = "$mes_da_folha / $row_folha[ano]";
}

//$result_folha_pro = mysql_query("SELECT * FROM folha_autonomo WHERE id_folha = '$folha' and status = '3'");
$result_folha_pro = mysql_query("SELECT * FROM folha_cooperado WHERE (id_folha = '$folha' and status = '3') or (id_folha = '$folha' and status = '4') ORDER BY nome");

$titulo = "Folha Sintética: Projeto $row_projeto[nome] mês de $mes_da_folha";

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
//
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
<link href="../net1.css" rel="stylesheet" type="text/css" />
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
#borda td{border-bottom:1px solid #000; padding:5px 0px;}
</style>
</head>
<body>
<table width="95%" border="0" align="center" class="bordaescura1px">
  <tr>
    <td align="center" valign="middle" bgcolor="#FFFFFF"><br />
      <table width="90%" border="0" align="center" >
      <tr>
        <td width="100%" height="92" align="center" class="show">
        
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr class="Texto10">
              <td width="16%" height="100" align="center"><span class="style1">
                <?=$LogoCoop?>
              </span></td>
            <td width="62%"><span class="Texto10">
              <?=$Gnome?>
              <br>CNPJ: <?=$Gcnpj?>
              </span><span class="style3"><br>
              </span></td>
            <td width="22%">
            <span class="Texto10">
            Data de Processamento: <br>
            <?=$row_folha['data_proc2']?></span></td>
            </tr>
        </table></td>
      </tr>
    </table>
      <br>
      <span class="titulo_opcoes">
      	  Folha de Produ&ccedil;&atilde;o  - <?=$mensagem?>
      </span>
      <br>
      <br>
    <?php
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
          <td width="20%" align="center" valign="middle" bgcolor="#CCCCCC" class="style23">Nome</td>
          <td width="9%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Produ&ccedil;&atilde;o Base</td>
          <td width="4%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Horas</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Rendim.</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Descontos</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">INSS</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">IRRF</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Quota</td>
          <td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Ajuda Custo</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Produ&ccedil;&atilde;o L&iacute;q.</td>
          <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Base<br>IMPOSTO</td>
          <?php if($id_user == '555' or $id_user == '999') { ?>
          	<td width="6%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">TX. Ope.</td>  
            <td width="7%" align="right" valign="middle" bgcolor="#CCCCCC" class="style23">Nota Fiscal.</td>
          <?php } ?>
        
        </tr>
        <?php
          $cont = "0";
		  while($row = mysql_fetch_array($result_folha_pro)){
		  
		  $REparti = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$row[id_autonomo]'");
		  $rowP = mysql_fetch_array($REparti);
		  
		  $result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$rowP[id_curso]'");
		  $row_curso = mysql_fetch_array($result_curso);	 
		
		  //-- FORMATANDO NO FORMATO BRASILEIRO --
		  $sal_baseF = number_format($row['salario'],2,",",".");
		  $rendiF = number_format($row['adicional'],2,",",".");
		  $descoF = number_format($row['desconto'],2,",",".");
		  $sal_liqF = number_format($row['salario_liq'],2,",",".");
		  $inssF = number_format($row['inss'],2,",",".");
		  
		  if($row['irrf'] >= 10.00){
			  $irrfF = number_format($row['irrf'],2,",",".");
		  }else{
			  $irrfF = '0,00';
			  $row['irrf'] = '0';
		  }
		  
		  $ajudaF = number_format($row['ajuda_custo'],2,",",".");
		  $quotaF = number_format($row['quota'],2,",",".");
		  $base_impostoF = number_format($row['base_imposto'],2,",",".");
		  $taxa_opeF = number_format($row['taxa_ope'],2,",",".");
		  $notaF = number_format($row['nota'],2,",",".");
		  
		  //-- FORMATO USA
		  $sal_liqT = number_format($row['salario_liq'],2,".","");

		  //---- EMBELEZAMENTO DA PAGINA ----------------------------------
		  $color = ($cont % 2) ? 'corfundo_um' : 'corfundo_dois';
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
			$result_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' and id_projeto = '$row_projeto[0]'");
			$result_bancoAUT = mysql_query("SELECT nome FROM bancos WHERE id_banco = '$row[banco]'");
			$RowBancAUT = mysql_fetch_array($result_bancoAUT);
			*/
		  print"
		  <tr height='20' class='novalinha $color'>
          <td align='center' valign='middle'>$rowP[id_autonomo]</td>
          <td align='lefth' valign='middle' >$alink $nomeT</a> $divTT</td>
          <td align='right' valign='middle' >$sal_baseF</td>
		  <td align='right' valign='middle' >$row[faltas]</td>
          <td align='right' valign='middle' >$rendiF</td>
          <td align='right' valign='middle' >$descoF</td>
          <td align='right' valign='middle' >$inssF</td>
          <td align='right' valign='middle' >$irrfF</td>
		  <td align='right' valign='middle' >$quotaF</td>
		  <td align='right' valign='middle' >$ajudaF</td>
		  <td align='right' valign='middle' >$sal_liqF</td>
		  <td align='right' valign='middle' >$base_impostoF</td>";
		  if($id_user == '555' or $id_user == '999') {
		  	print" 
		  <td align='right' valign='middle' >$taxa_opeF</td>
		   <td align='right' valign='middle' >$notaF</td>";
		  }
		  print"
		  		  </tr>";

		  $PARTE1 = $PARTE1."UPDATE folha_autonomo SET salario_liq = '$sal_liqT' WHERE id_folha_pro = '$row[0]';\r\n";
		  $cont ++;
		  //-- SOMANDO VARIAVEIS PARA OS TOTAIS --//
		  $TOsal_base = $TOsal_base + $sal_base;
		  $TOsal_liq = $TOsal_liq + $sal_liq; 
		  $TOrendi = $TOrendi + $rendi;
		  $TOdesco = $TOdesco + $desco;
		  $TOInss = $TOInss + $row['inss'];
		  $TOIrrf = $TOIrrf + $row['irrf'];
		  $TOquota = $TOquota + $row['quota'];
		  $TOajuda = $TOajuda + $row['ajuda_custo'];
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
		  $TOajudaF		 = number_format($TOajuda,2,",",".");
		  $ValorTaxaADMF = number_format($TOTaxaOP,2,",",".");
		  $TONotaFiscalF = number_format($TONOTAFIS,2,",",".");
		  
		  
		  $reSomaBASEIMP = mysql_query("SELECT SUM(base_imposto) FROM folha_cooperado WHERE id_folha = '$folha' and status = '3'");
		  $RowTotBaseImposto = mysql_fetch_array($reSomaBASEIMP);
		  $TO_BASE_IMPOSTO = number_format($RowTotBaseImposto['0'],2,",",".");
		
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
          <td align="right" valign="bottom" class="style23"><?=$TOajudaF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TOsal_liqF?></td>
          <td align="right" valign="bottom" class="style23"><?=$TO_BASE_IMPOSTO?></td>
          <?php if($id_user == '555' or $id_user == '999') { ?>
          	<td align="right" valign="bottom" class="style23"><?=$ValorTaxaADMF?></td>
            <td align="right" valign="bottom" class="style23"><?=$TONotaFiscalF?>
          <?php } ?>
          </td>
        </tr>
      </table>
      <br />
      <br>
      <table width="50%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="39%" height="270" align="center" valign="middle" bgcolor="#F8F8F8" style="border-right:solid 2px #FFF">
          
      <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="35" colspan="2" align="center" valign="middle" class="show">TOTALIZADORES</td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="22" align="right" valign="middle">Per&iacute;odo:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$row_folha['data_inicio2']." at&eacute; ".$row_folha['data_fim2']." - ".$row_folha['qnt_dias']." dias"?></td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td width="40%" height="22" align="right" valign="middle"> Retirada L&iacute;quida:</td>
          <td width="60%" height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOsal_liqF?>          </td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="22" align="right" valign="middle"> Base Valor Produ&ccedil;&atilde;o:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOsal_baseF?>          </td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td height="22" align="right" valign="middle">INSS:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOINSSF?></td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="22" align="right" valign="middle">IRRF:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOvalor_IRF?>          </td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td height="22" align="right" valign="middle">Quotas:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOValorQuotaF?></td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="22" align="right" valign="middle">Ajuda de Custo:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOajudaF?></td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td height="22" align="right" valign="middle">Desconto:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOdescoF?>          </td>
        </tr>
        <tr class="novalinha corfundo_um">
          <td height="22" align="right" valign="middle">Rendimento:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOrendiF?>          </td>
        </tr>
        <tr class="novalinha corfundo_dois">
          <td height="22" align="right" valign="middle">Pessoas  Listadas:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;          <?=$row_folha['participantes']?></td>
        </tr>
        
        <tr class="novalinha corfundo_um">
          <td height="22" align="right" valign="middle">Total folha:</td>
          <td height="22" align="left" valign="middle">&nbsp;&nbsp;            <?=$TOsal_liqF?>          </td>
        </tr>
      </table>
      
      </td>
    </tr>
  </table>
  <br>
  <br>
  
  <table cellpadding="0" cellspacing="0" style="border:0; width:50%;" id="borda">
     <tr>
       <td colspan="6" style="font-size:17px; font-weight:bold; text-align:center;">
         Lista de Bancos
       </td>
     </tr>
      
	<?php // Verificando os bancos envolvidos na folha de pagamento
		  $qr_bancos = mysql_query("SELECT DISTINCT(banco) FROM folha_cooperado WHERE id_folha = '$folha' AND banco != '0' AND banco != '9999' AND (status = '3' OR status = '4')");
		  $contCol   = 0;
		  
		  while($row_bancos = mysql_fetch_array($qr_bancos)) {
				
		  		$RE_Bancos  = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_bancos[0]'");
		  		$row_Bancos = mysql_fetch_array($RE_Bancos);
				
		  		// Encriptografando a variável
				$linkBanc = str_replace('+', '--', encrypt("$regiao&$row_Bancos[0]&$folha"));
		  		// ?>

		  		<tr>
		 		  <td><img src="../imagens/bancos/<?=$row_Bancos['id_nacional']?>.jpg" width="25" height="25"></td>
          		  <td><?=$row_Bancos['nome']?></td>	
				
		  <?php $refinalizado  = mysql_query("SELECT nome, banco, status FROM folha_cooperado WHERE id_folha = '$folha' AND banco = '$row_bancos[0]' AND status = '3'");
				$numfinalizado = mysql_num_rows($refinalizado);
				
				if(empty($numfinalizado)) { ?>
						
                    <td><a href="finalizados.php?regiao=<?=$regiao?>&folha=<?=$folha?>&projeto=<?=$row_projeto[0]?>&banco=<?=$rowBancosFinalizados[0]?>">FINALIZADO</a></td>
                    <td>&nbsp;</td>
                    <td><?=$numfinalizado?> Participantes</td>
			
            <?php } else { ?>
            
                    <td>
                    <form id="form<?=$contCol?>" name="form<?=$contCol?>" method="post" action="folha_bancocoo.php">
                      <label id="data_pag<?=$contCol?>" style="display:none;">
                      <input name="data" type="text" id="data[]" size="10" class="campotexto" onKeyUp="mascara_data(this)" maxlength="10"> 
                      <input type="hidden" name="enc" id="enc" value="<?=$linkBanc?>">
                      <input name="enviar" id="Enviar[]" type="submit" value="Gerar"/>
                      </label>
                    </form>
                    </td>
                    <td><img src="../rh/folha/imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Banco" style="cursor:hand;" onClick="document.all.data_pag<?=$contCol?>.style.display = (document.all.data_pag<?=$contCol?>.style.display == 'none') ? '' : 'none' ;"></td>		  
                    <td><?=$numfinalizado?> Participantes</td>
						
			<?php } $contCol++;
				}
		
	  
	   	// VERIFICA OS TIPOS DE PAGAMENTOS DA REGIÃO E PROJETO ATUAL
		$qr_cheque    = mysql_query("SELECT * FROM folha_cooperado folha INNER JOIN tipopg tipo ON folha.tipo_pg =  tipo.id_tipopg WHERE folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Cheque' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$row_projeto[0]' AND tipo.campo1 = '2'");
		$total_cheque = mysql_num_rows($qr_cheque);	  
	  	
		// Encriptografando a variável
		$linkcheque = str_replace('+', '--', encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]"));
		
		if(!empty($total_cheque)) { ?>
        
		<tr>
		  <td><img src="../imagens/bancos/cheque.jpg" width="25" height="25"></td>
          <td>Cheque</td>
		  <td>&nbsp;</td>
		  <td><a href="ver_cheque.php?enc=<?=$linkcheque?>"><img src="../rh/folha/imagens/ver_banc.png" border="0" alt="Visualizar Funcionários por Cheque"></a></td>
		  <td><?=$total_cheque?> Participantes</td> 
	  	</tr>
      <?php } ?>
    </table>
<br/>            
<br>
<br>
<?php
// ENCRIPTOGRAFANDO A VARIAVEL
$linkvolt = encrypt("$regiao&$regiao"); 
$linkvolt = str_replace('+', '--', $linkvolt);
$enc2     = str_replace('+', '--', $enc);
//

// ENCRIPTOGRAFANDO A VARIAVEL
$linkcompro = encrypt("$regiao&$folha"); 
$linkcompro = str_replace("+","--",$linkcompro);
$enc3       = str_replace("+","--",$linkcompro);
//
?>
<br>
   </td>
  </tr>
  <tr>
    <td align="center" valign="middle" bgcolor="#CCCCCC">
    <table width="49%" border="0" cellspacing="0" cellpadding="0" style="clear:both;">
      <tr>
        <td height="30" align="center" valign="middle"><b><a href='folha.php?id=9&<?="enc=".$linkvolt."&tela=1"?>' class="botao" style="color:#000">VOLTAR</a></b></td>
        <td height="30" align="center" valign="middle"><b><a href='ver_lista_banco.php?<?="enc=".$enc2?>'  class="botao" style="color:#000">VER LISTA POR BANCO</a></b></td>
        </tr>
      <?php if($row_folha['contratacao'] == 3) { ?>
      <tr>
        <td height="30" align="center" valign="middle"><b><a href='../cooperativas/recibocoop.php?<?="enc=".$enc3?>' target="_blank" class="botao" style="color:#000">RECIBOS DE PAGAMENTOS</a></b></td>
        <td height="30" align="center" valign="middle"><b><a href='ver_lista_inss.php?enc=<?=$enc2?>'  class="botao" style="color:#000">LISTA DE INSS</a></b></td>
      </tr>
      <?php } elseif($row_folha['contratacao'] == 4) { ?>
      <tr>
        <td height="30" align="center" valign="middle"><b><a href='../autonomo/gps.php?<?="enc=".$enc3?>' target="_blank"  class="botao" style="color:#000">GPS (autonomos)</a></b></td>
        <td height="30" align="center" valign="middle"><b><a href='../autonomo/rpa.php?<?="enc=".$enc3?>' target="_blank"  class="botao" style="color:#000">RPA (autonomos)</a></b></td>
      </tr>
      <?php } ?>
    </table>
    </td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>