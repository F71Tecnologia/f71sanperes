<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../../conn.php";
include "../../classes/regiao.php";

$id_user = $_COOKIE['logado'];
$cooperado = $_REQUEST['coop'];

// PEGA O ID DO FUNCIONÁRIO LOGADO E SELECIONA OS DADOS DELE NA BASE DE DADOS
$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$data_cad = date('Y-m-d');

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '26' and id_clt = '$cooperado'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	
	mysql_query("INSERT INTO rh_doc_status (tipo,id_clt,data,id_user) VALUES ('26','$cooperado','$data_cad', '$id_user')")or die(mysql_error());
	
	
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$id_user' WHERE id_clt = '$cooperado' and tipo = '26'");
}
//FAZENDO UM SELECT NA TABELA MASTAR PARA PEGAR AS INFORMAÇÕES DA EMPRESA
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");
$row_master = mysql_fetch_array($result_master);

//SELECIONANDO A REGIAO AO QUAL ESTA LOGADO
$result_re = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_user[id_regiao]'");
$row_re = mysql_fetch_array($result_re);


//INICIANDO O SELECT DO COOPERADO
$RE_ree = mysql_query("SELECT *,date_format(data_nasci, '%d/%m/%Y') as data_nasci FROM autonomo WHERE id_autonomo = '$cooperado'");
$Row = mysql_fetch_array($RE_ree);

$RECoope = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$Row[id_cooperativa]'");
$RowCoop = mysql_fetch_array($RECoope);

//VERIFICANDO SE VAI TER LOGO OU NÃO
if($RowCoop['foto'] != "0"){
	$LOGO = "<img src='../logos/coop_".$RowCoop['0'].$RowCoop['foto']."' width='120' height='86' />";
}else{
	$LOGO = "";
}


$codigo = sprintf("%04d",$Row['campo3']);

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

//CALCULAR VALOR DE CADA PARCELA
$ValorCOTA = $Row['cota'];
$QntParcela = $Row['parcelas'];
if($ValorCOTA != 0 and $QntParcela != 0){
	$ValorParcela = $ValorCOTA / $QntParcela;
}else{
	$ValorParcela = 0;
}


$valor_e = valorPorExtenso($ValorCOTA);
$valor2_e = valorPorExtenso($ValorParcela);

//FORMATANDO VALORES
$ValorCOTAF = number_format($ValorCOTA,2,",",".");
$ValorParcelaF = number_format($ValorParcela,2,",",".");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>INTRANET - FICHA DE ADES&Atilde;O - COOPERADO</title>
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

<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#EAEAEA">
  <tr>
    <td align="center" valign="top">
    
    <table width="700" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:2px solid #666">
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
          <div style="margin:10px; font-family:Arial, Helvetica, sans-serif; font-size:13px" align="left">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="right">
              <tr>
                <td width="18%">&nbsp;</td>
                <td width="60%" align="center"><b>FICHA DE ADESÃO</b></td>
                <td width="22%" align="right"><b>Numero: <?=$codigo?></b> &nbsp;&nbsp;&nbsp;&nbsp;</td>
                </tr>
              </table>
            <br />
            <br />
            <br />
            <b><?=$Row['nome']?></b>, Brasileiro(a), <?=$Row['civil']."(a)"?><br />
            RG nº <b><?=$Row['rg']." ".$Row['uf_rg']?></b>, CPF nº <b><?=$Row['cpf']?></b>,&nbsp;Carteira de Conselho n&ordm; <b>
            <?=$Row['conselho']?>
            </b><br />
             residente à <b><?=$Row['endereco']." - ".$Row['bairro']." - ".$Row['cidade']." - ".$Row['uf']?></b><br />
            <br />
            <div align="justify">Vem pelo presente instrumento perante ao CONSELHO DE ADMINISTRAÇÃO desta Cooperativa, requerer que seja aceito o meu ingresso na qualidade de cooperado, aderindo desde já em todos os termos estatutários e  regimentais, declarando deles ter tomado conhecimentos, dando minha integral concordância, mediante as condições descritas.</div>
            
            
            
            <br />
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="6%" height="93" align="right" valign="top">1.&nbsp;&nbsp;&nbsp;</td>
                <td width="94%" valign="top"><div align="justify" style="margin-right:17px; font-size:13px">
               A opção quanto à integralização de minha quota-parte dar-se-á mediante ao pagamento do valor de <b>R$ <?=$ValorCOTAF?> (&nbsp;
                  <?=$valor_e?> )</b>, sendo <b>R$ <?=$ValorParcelaF?> (&nbsp;
                  <?=$valor2_e?> )</b> para integralização de uma quota parte pelo seu valor atual, ficando sob minha responsabilidade a subscrição de minha quota-parte no ato da minha adesão, do qual terei o direito da totalidade de minha quota-parte quando então poderei exercer a condição plena de cooperado conforme o que determina o Estatuto da Cooperativa.
                  </div></td>
                </tr>
              <tr>
                <td height="79" align="right" valign="top">2.&nbsp;&nbsp;&nbsp;</td>
                <td valign="top"><div align="justify" style="margin-right:17px; font-size:13px">Declaro ainda, dentro dos objetivos sócias da <b><?=$RowCoop['nome']?></b>, haver aderido voluntariamente a todos os seus  princípios, concordando com o que é estabelecido em seu Estatuto  e principalmente quanto à minha condição de cooperado, não mantendo qualquer vinculo empregatício com a (s) empresa (s) nas quais prestarei minhas atividades profissionais.</div></td>
                </tr>
              <tr>
                <td height="66" align="right" valign="top">3.&nbsp;&nbsp;&nbsp;</td>
                <td valign="top"><div align="justify" style="margin-right:17px; font-size:13px">Comprometo-me a prestar serviços na(s) empresa(s) com diligência, zelar pelos meus interesses, comprometendo-me ainda a manter regularidade e assiduidade em meus plantões, caso contrário a direção da <b><?=$RowCoop['nome']?></b> terá o direito de substituir-me, ficando  também sujeito as sanções no Estatuto.</div></td>
                </tr>
              <tr>
                <td height="44" align="right" valign="top">4.&nbsp;&nbsp;&nbsp;</td>
                <td valign="top"><div align="justify" style="margin-right:17px; font-size:13px">Declaro estar ciente  do regulamento interno.</div></td>
              </tr>
              </table>
            <br />
            <div align="center">Assim sendo, para que produza os efeitos legais nos termos da Lei nº 5.764 de 16.12.1971, assino a presente.<br />
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
    </table></td>
  </tr>
  <tr>
    <td align="center" valign="top" class='noprint'><a href="javascript:window.close()" style="text-decoration:none; color:#000">fechar</a></td>
  </tr>
</table>
</body>
</html>