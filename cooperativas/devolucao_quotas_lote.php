<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../classes/regiao.php');

$id_user   = $_COOKIE['logado'];
$cooperado = $_REQUEST['coop'];
$data_cad  = date('Y-m-d');

// GRAVANDO NA TABELA DOCUMENTOS GERADOS
$result_verifica  = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '33' AND id_clt = '$cooperado'");
$num_row_verifica = mysql_num_rows($result_verifica);

if(empty($num_row_verifica)) {
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('33','$cooperado','$data_cad', '$id_user')");
} else {
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$id_user' WHERE id_clt = '$cooperado' AND tipo = '27'");
}
// GRAVANDO NA TABELA DOCUMENTOS GERADOS

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user    = mysql_fetch_array($result_user);

// FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master    = mysql_fetch_array($result_master);

// SELECIONANDO A REGIAO AO QUAL ESTA LOGADO
$result_re = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_user[id_regiao]'");
$row_re    = mysql_fetch_array($result_re);

// INICIANDO O SELECT DO COOPERADO
$RE_ree = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y') AS data_entrada FROM autonomo WHERE id_autonomo = '$cooperado'");
$Row    = mysql_fetch_array($RE_ree);

$RECoope = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$Row[id_cooperativa]'");
$RowCoop = mysql_fetch_array($RECoope);

// VERIFICANDO SE VAI TER LOGO OU NÃO
if(!empty($RowCoop['foto'])) {
	$logo = '<img src="logos/coop_'.$RowCoop['0'].$RowCoop['foto'].'" width="120" height="86" />';
} else {
	$logo = NULL;
}

$meses = array('Erro','Janeiro','Fevereiro','MarÇo','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

// INICIANDO FUNÇÃO PARA ESCREVER O VALOR EM EXTENSO
function valorPorExtenso($valor=0) {

	$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
	$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
	$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
	$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
	$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
	$u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
	$z=0;

	$valor   = number_format($valor, 2, ".", ".");
	$inteiro = explode(".", $valor);

	for($i=0;$i<count($inteiro);$i++)
		for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
			$inteiro[$i] = "0".$inteiro[$i];

	// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>INTRANET - DEVOLU&Ccedil;&Atilde;O DE QUOTAS - COOPERADO</title>
<style type="text/css">
body {
	margin:0px;
	font-family:Arial, Helvetica, sans-serif;
	line-height:22px;
}
</style>
<style type="text/css" media="print"> 
.noprint { 
   display:none; 
} 
</style>
</head>
<body onload="javascript:window.print()">

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EAEAEA">
  <tr>
    <td align="center" valign="top">
    
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:2px solid #666">
      <tr>
        <td height="124"><p class="MsoHeader" align="center" style='text-align:center; color:#666'>
        <?=$logo?>
        <br />
        <span style="font-size:12px;"><?=$RowCoop['nome']?></span>
        <br />
        <span style="font-size:10px; line-height:normal;"><?=$RowCoop['endereco']." Tel.: ".$RowCoop['tel']." CNPJ: ".$RowCoop['cnpj']?></span>
        
         </p></td>
        </tr>
      <tr>
        <td>
          <div style="margin:15px; font-family:Arial, Helvetica, sans-serif; font-size:13px" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="right">
              <tr>
                <td width="18%">&nbsp;</td>
                <td width="60%" align="center"><strong>DEVOLU&Ccedil;&Atilde;O DE QUOTAS DE CAPITAL SOCIAL</strong></td>
                <td width="22%" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
              </table>
              
           <?php $qr_total_pago = mysql_query("SELECT SUM(a.quota) FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$cooperado' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3'");
			  	 $total_pago	= @mysql_result($qr_total_pago,0);
				 $ValorParcela  = $Row['cota'] / $Row['parcelas']; ?>
            <br />
            <br />
            <br />
            <b><?=$Row['nome']?></b> matr&iacute;cula n&ordm;<b> <?=sprintf('%04d',$Row['campo3'])?></b>.
            <br />
            <div align="justify">Desligando-se, nesta data, a qual contribuiu do capital  social da <b><?=$RowCoop['nome']?> - <?=$RowCoop['fantasia']?></b>,  no valor de <b>R$ <?=number_format($ValorParcela,2,',','.')?> (<?=trim(valorPorExtenso($ValorParcela))?>)</b>  a  cr&eacute;dito de um Fundo de Reservas, que perfaziam at&eacute; a presente data o valor de <b>R$ <?=number_format($total_pago,2,',','.')?> (<?=trim(valorPorExtenso($total_pago))?>)</b>, ora descontado em folha,<b> </b>de minha produtividade, declaro como reembolsado conforme discrimina&ccedil;&atilde;o abaixo:</div>
            
            
            
            <br />
            <br />
            <table width="90%" border="0" cellspacing="0" cellpadding="0" align="center" style="text-align:center; background-color:#ddd;">
              <tr>
                <th width="28%">PARCELA</th>
                <th width="38%">M&Ecirc;S</th>
                <th width="34%">VALOR</th>
              </tr>
        <?php $qr_quotas = mysql_query("SELECT a.quota, date_format(b.data_inicio, '%m') AS mes FROM folha_cooperado a INNER JOIN folhas b ON a.id_folha = b.id_folha WHERE a.id_autonomo = '$cooperado' AND a.quota != '0.00' AND a.status = '3' AND b.status = '3' ORDER BY b.data_inicio ASC");
              while($row_quotas = mysql_fetch_assoc($qr_quotas)) {
				  
				  if($cor++%2==0) {
					  $fundo_cor = '#fafafa'; 
				  } else {
					  $fundo_cor = '#f3f3f3';
				  }
				  
				  $parcela++;
				  
				  echo '<tr style="background-color:'.$fundo_cor.'; text-align:center;">
				            <td>'.$parcela.'</td>
				            <td>'.strtoupper($meses[(int)$row_quotas['mes']]).'</td>
				            <td>R$ '.number_format($row_quotas['quota'],2,',','.').'</td>		
					    </tr>';
			  } ?>
              <tr style="font-weight:bold;">
                <td colspan="2" align="right">TOTAL:</th>
                <td align="center">R$ <?=number_format($total_pago,2,',','.')?></th>
              </tr>
            </table>
            <br />
<br />
            <div align="center"><br />
              <br />
              <br />
              <?php $data = $Row['data_entrada'];
					$completa  = new regiao();
					$completa -> RegiaoLogado();
					echo ', ';
					$completa -> MostraDataCompleta($data); ?>
              <br />
              <br />
              <br />
              <br />
  <br />
              </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" align="center">_________________________________________<br />
                  <b><?=$RowCoop['nome']?></b></td>
                <td width="50%" align="center">_________________________________________<br />
                  <b><?=$Row['nome']?></b></td>
                </tr>
              </table>
          </div></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top" class='noprint'><a href="javascript:window.close()" style="text-decoration:none; color:#000">fechar</a></td>
  </tr>
</table>
</body>
</html>