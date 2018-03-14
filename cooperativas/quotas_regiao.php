<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../classes/regiao.php";

$id_user = $_COOKIE['logado'];
$cooperado = $_REQUEST['coop'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '27' and id_clt = '$cooperado'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('27','$cooperado','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$cooperado' and tipo = '27'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

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



$codigo = sprintf("%04d",$Row['campo3']);

$dia = date('d');
$mes_h = date('m');
$ano = date('Y');

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




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>INTRANET - SUBSCRI&Ccedil;&Atilde;O DE QUOTAS - COOPERADO</title>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	font-family:Arial, Helvetica, sans-serif;
}
-->
</style>

<style type='text/css' media='print'> 
.noprint
{ 
   display: none; 
} 
</style>

</head>

<body onload="javascript:window.print()">
<?php

$id_regiao = 3;
$id_projeto = 3295;


//INICIANDO O SELECT DO COOPERADO
$RE_ree = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y') as data_entrada FROM autonomo WHERE id_regiao = '$id_regiao' AND id_projeto = '$id_projeto' ");
while($Row = mysql_fetch_array($RE_ree)):


$RECoope = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$Row[id_cooperativa]'");
$RowCoop = mysql_fetch_array($RECoope);

//VERIFICANDO SE VAI TER LOGO OU NÃO
if($RowCoop['foto'] != "0"){
	$LOGO = "<img src='logos/coop_".$RowCoop['0'].$RowCoop['foto']."' width='120' height='86' />";
}else{
	$LOGO = "";
}


//CALCULAR VALOR DE CADA PARCELA
$ValorCOTA = $Row ['cota'];
$QntParcela = $Row['parcelas'];
$ValorParcela = $ValorCOTA / $QntParcela;



$valor_e = valorPorExtenso($ValorCOTA);
$valor2_e = valorPorExtenso($ValorParcela);

$Parcela = valorPorExtenso($QntParcela);
$NumPArcelas = str_replace("reais","",$Parcela);


//FORMATANDO VALORES
$ValorCOTAF = number_format($ValorCOTA,2,",",".");
$ValorParcelaF = number_format($ValorParcela,2,",",".");

?>



<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EAEAEA" style="margin-bottom:30px;">
  <tr>
    <td align="center" valign="top">
    
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:2px solid #666; height:1000px;" >
      <tr>
        <td height="124"><p class="MsoHeader" align="center" style='text-align:center; color:#666'>
        <?=$LOGO?>
        <br />
        <span style="font-size:12px"><?=$RowCoop['nome']?></span>
        <br />
        <span style="font-size:10px"><?=$RowCoop['endereco']." Tel.: ".$RowCoop['tel']." CNPJ: ".$RowCoop['cnpj']?></span>
        
         </p></td>
        </tr>
      <tr>
        <td>
          <div style="margin:15px; font-family:Arial, Helvetica, sans-serif; font-size:13px" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="right">
              <tr>
                <td width="18%">&nbsp;</td>
                <td width="60%" align="center"><strong>SUBSCRI&Ccedil;&Atilde;O DE QUOTAS DE CAPITAL SOCIAL</strong></td>
                <td width="22%" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
              </table>
            <br />
            <br />
            <br />
            <b><?=$Row['nome']?></b> matr&iacute;cula n&ordm;<b> <?=$codigo?></b>.
            <br />
            <div align="justify">Subscreve, nesta data, 01 (uma) quota do capital  social da <b><?=$RowCoop['nome']?> - <?=$RowCoop['fantasia']?></b>,  no valor atual de <b>R$ <?=$ValorParcelaF?> (<?=$valor2_e?> )</b> e o restante a  cr&eacute;dito de um Fundo de Reservas, que perfazem nesta data o valor de <b>R$ <?=$ValorCOTAF?> (<?=$valor_e?> )</b>, valor este que ser&aacute; descontado em <b><?=$Row['parcelas']?> (<?=$NumPArcelas?> )</b>  parcelas de minha produtividade, conforme discrimina&ccedil;&atilde;o abaixo:</div>
            
            
            
            <br />
            <br />
            <table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
              <tr>
                <th width="28%" height="20" bgcolor="#CECECE">PARCELA</th>
                <th width="38%" height="20" bgcolor="#CECECE">VENCIMENTO</th>
                <th width="34%" bgcolor="#CECECE">VALOR</th>
              </tr>
              <?php
			  $Dat = explode("/",$Row['data_entrada']);
			  $Dia = $Dat[0];
			  $Mes = $Dat[1];
			  $Ano = $Dat[2];
			  
			  $bord = "style='border-bottom:#000 solid 1px;'";
			  
              for($i=1; $i<=$QntParcela; $i ++){
				  
				  if($i % 2){ $color="#f0f0f0"; }else{ $color="#dddddd"; }
				  
				  $DatAgora = date("d/m/Y", mktime(0, 0, 0, $Mes + $i, $Dia, $Ano));
				  
				  echo "<tr bgcolor='$color'>";
				  echo "<td width='28%' align='center' height='25' $bord><b>$i</b></td>";
				  echo "<td width='38%' align='center' $bord><b>$DatAgora</b></td>";
				  echo "<td width='34%' align='center' $bord><b>R$ $ValorParcelaF</b></td>";
				  echo "</tr>";
			  }
			  ?>
            </table>
            <br />
<br />
            <div align="center"><br />
              <br />
              <br />
              <?php
			    $data = $Row['data_entrada'];
				
			  	$completa= new regiao();
				$completa -> RegiaoLogado();
				echo ", ";
				$completa -> MostraDataCompleta($data);
			  ?>
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
<?php endwhile; ?>
</body>
</html>