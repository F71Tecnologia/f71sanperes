<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";

$id_user = $_COOKIE['logado'];
$reembolso = $_REQUEST['id'];

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//SELECIONANDO A REGIAO AO QUAL ESTA LOGADO
$result_re = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_user[id_regiao]'");
$row_re = mysql_fetch_array($result_re);

$RE_ree = mysql_query("SELECT *,date_format(data, '%d/%m/%Y') as data FROM fr_reembolso WHERE id_reembolso = '$reembolso'");
$RowRee = mysql_fetch_array($RE_ree);

$codigo = sprintf("%05d",$RowRee['0']);
$valorF = number_format($RowRee['valor'],2,",",".");

if($RowRee['funcionario'] == "1"){
	$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$RowRee[id_user]'");
	$row_user = mysql_fetch_array($result_user);
	$NOME = $row_user['nome1'];  
}else{
	$NOME = $RowRee['nome']; 
}

$dia = date('d');
$mes_h = date('m');
$ano = date('Y');
$hora = date('H');
$minuto = date('i');
$segundo = date('s');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
$MesInt = (int)$mes_h;
$mes = $meses[$MesInt];

//INICIANDO FUNÇÃO PARA ESCREVER O VALOR EM EXTENSO
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

$valor_e = valorPorExtenso($RowRee['valor']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SOLICITA&Ccedil;&Atilde;O DE REEMBOLSO</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
-->
</style>

<style type='text/css' media='print'> 
.noprint
{ 
   display: none; 
} 
</style>

<link href="../net1.css" rel="stylesheet" type="text/css" />
</head>

<body onload="javascript:window.print()">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EAEAEA">
  <tr>
    <td align="center" valign="top">
    
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:2px solid #666">
      <tr>
        <td width="21%"><p class="MsoHeader" align="center" style='text-align:center'><strong><span class="style5"> <br />
          <img src='../imagens/logomaster<?=$row_master['id_master']?>.gif' width='120' height='86' /><br />
          </span></strong><span style='font-size:10px'>
            <?=$row_master['razao']?>
          </span></p></td>
        <td width="58%"><p class="MsoHeader" align="center" style='text-align:center'><b><span
  style='font-size:16.0pt;color:red'></span></b><strong>SOLICITA&Ccedil;&Atilde;O DE REEMBOLSO</strong><br />
          <br />
          NUMERO 
          <b>
          <?=$codigo?>
          </b></p></td>
        <td width="21%">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
        <div style="margin:15px; font-family:Arial, Helvetica, sans-serif; font-size:13px" align="left"><br />
          Solicito o reembolso da quantia de
        
          <strong>R$ <?=$valorF?></strong> - ( <?=$valor_e?> )<br />
          Referente: 
          <strong>
          <?=$RowRee['descricao']?>
          </strong><br />
          <br />
          Em nome do(a) Sr.(a) <?=$NOME?>,&nbsp; referente(s) a despesa(s) relacionada(s)  abaixo, com NOTA(S) FISCAI(S) ORIGINAL(IS) quitada(s) em anexo. <br />
          <br />
          <br />
          O reembolso ser&aacute;  creditado na conta do benefici&aacute;rio em at&eacute; 03 dias &uacute;teis ap&oacute;s entrada na a(o) <?=$row_re['0']?><br />
          <br />
          <br />
          <table width="90%" border="0" cellspacing="1" cellpadding="0" align="center">
            <tr>
              <td height="25" colspan="2" align="center" bgcolor="#999999"><strong>DADOS PESSOAIS DO REQUERENTE</strong>:</td>
            </tr>
            <tr>
              <th width="29%" height="20" align="right" bgcolor="#CECECE">NOME:&nbsp;</th>
              <td width="71%" height="20" bgcolor="#EAEAEA">&nbsp;&nbsp;<strong>
                <?=$RowRee['favorecido']?>
              </strong></td>
            </tr>
            <tr>
              <th height="20" align="right" bgcolor="#CECECE">BANCO:&nbsp;</th>
              <td height="20" bgcolor="#EAEAEA">&nbsp;&nbsp;<strong>
                <?=$RowRee['banco']?>
              </strong></td>
            </tr>
            <tr>
              <th height="20" align="right" bgcolor="#CECECE">AG&Ecirc;NCIA:&nbsp;</th>
              <td height="20" bgcolor="#EAEAEA">&nbsp;&nbsp;<strong>
                <?=$RowRee['agencia']?>
              </strong></td>
            </tr>
            <tr>
              <th height="20" align="right" bgcolor="#CECECE">CONTA:&nbsp;</th>
              <td height="20" bgcolor="#EAEAEA">&nbsp;&nbsp;<strong>
                <?=$RowRee['conta']?>
              </strong></td>
            </tr>
            <tr>
              <th height="20" align="right" bgcolor="#CECECE">CPF:&nbsp;</th>
              <td height="20" bgcolor="#EAEAEA">&nbsp;&nbsp;<strong>
                <?=$RowRee['cpf']?>
              </strong></td>
            </tr>
          </table>
          <br />
          <br />
          <br />
          <div align="center"><?=$row_re['0'].", ".$dia." de ".$mes." de ".$ano. " ás ".$hora.":".$minuto." hora(s)";?><br />
            <br />
            _________________________________________________<br />
Assinatura do Solicitante </div><br />
        </div>
        &nbsp;</td>
      </tr>
      <tr>
        <td colspan="3">
        <hr color="#CCCCCC" />

          
          <div style="margin:15px; font-family:Arial, Helvetica, sans-serif; font-size:13px" align="left">
          
          <div align="center"><strong>APROVA&Ccedil;&Atilde;O DO REEMBOLSO</strong><br />
            <br />
<br />
            Declaro que recebi todas as notas anexadas a este formul&aacute;rio e<br />
(&nbsp;&nbsp;&nbsp; ) APROVO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  (&nbsp;&nbsp; )&nbsp;&nbsp; REPROVO<br />
O reembolso acima. </div>
          <br />
  			<br />
  			<br />
  <br />
  O dep&oacute;sito do valor solicitado a favor do requerente ser&aacute; creditado em  conta corrente em at&eacute; 3 dias &uacute;teis.<br />
  <br />
  <br />
  <div align="center">
    <?=$row_re['0'].", ".$dia." de ".$mes." de ".$ano. " ás ".$hora.":".$minuto." hora(s)"?>
    <br />
    <br />
    _________________________________________________<br />
    Carimbo e Assinatura do Financeiro</div>
<br />
          </div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top" class='noprint'><a href="javascript:window.close()" style="text-decoration:none; color:#000">fechar</a></td>
  </tr>
</table>
</body>
</html>