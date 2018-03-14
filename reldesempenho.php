<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{

$hostname = $_SERVER['REMOTE_ADDR'];

include "conn.php";
$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
$id = $_REQUEST['id']

/*
 Ex: 3 - Em um concurso de perguntas e respostas, um jovem acertou 72 das 90 perguntas apresentadas. Qual foi a porcentagem de acertos? E a porcentagem de erros? Veja o código em PHP: 
<?
  $perguntas = 90;
  $acertos = 72;
  
  
  $mes1 = 50;
  $mes2 = 5;
  
  ($mes1 / $mes2) * 100) . "%" .
  
  
  echo "Porcentagem de acertos: " .
      (($acertos / $perguntas) * 100) . "%" . "<br>";

  echo "Porcentagem de erros: " .
      ((($perguntas - $acertos) / $perguntas) * 100) . "%";   

  // Os resultados serão 80% e 20%
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Relat&oacute;rio Mensal por item</title>
<style type="text/css">
<!--

body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	color: #003300;
	text-transform: uppercase; 
}
.style2 {font-size: 12px}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style24 {font-size: 12px; font-weight: bold; }
.style16 {	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
}
.style19 {	color: #FFFFFF;
	font-weight: bold;
}
.style21 {font-size: 11px; font-weight: bold; }
.style23 {font-size: 11px; font-weight: bold; color: #FF0000; }
.style25 {color: #FFFFFF}
.style26 {color: #FF0000}
.style29 {font-size: 10px; font-weight: bold; }
.styleralho {font-size: 10px; font-face:Arial Narrow,Arial}
-->
</style>
<link href="net1.css" rel="stylesheet" type="text/css" />
</head>


<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="bordaescura1px">
  

  <tr>
    <td width="23" rowspan="4">&nbsp;</td>
    <td width="403" align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="406" align="left" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="22" rowspan="4" >&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF"><div align="center" class="style6">
      <div align="left"><font face="Verdana, Arial, Helvetica, sans-serif"><span class="style7">
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src="imagens/certificadosrecebidos.gif" alt="logo" width="120" height="86" align="middle" />-->RELAT&Oacute;RIO DE DESEMPENHO</span></font></div>
    </div> </td>
  </tr>
  
  <tr>
    <td height="96" colspan="2" align="center" valign="top">
<?php

switch($id){
case 1:

?>
<br />
<form action="reldesempenho.php" method="post" name='form1' id="form1">
  <table width="90%" border="0" align="center" cellspacing="0" bordercolor="#999999">
          <tr>
            <td height="37" colspan="2" align="center" valign="middle" bgcolor="#FFFFFF"><div align="left"><span class="style24">&nbsp;&nbsp;&nbsp;&nbsp;SELECIONE O PROJETO:
              <select name="projeto" id="projeto">
                <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'");
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome]</option>";
}

?>
              </select>
            </span></div></td>
          </tr>
          
          <tr>
            <td height="39" colspan="2" align="center" bgcolor="#FFFFFF"><div align="left"><span class="style24">&nbsp;&nbsp;&nbsp;&nbsp;SELECIONE A CONTA:
              <select name="banco" id="banco">
                <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and interno = '1'");
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[nome] $row_banco[agencia] / $row_banco[conta]</option>";
}
?>
              </select>
            </span></div></td>
          </tr>
          <tr>
            <td width="46%" height="39" align="center" bgcolor="#FFFFFF"><div align="left"><span class="style24">&nbsp;&nbsp;&nbsp;&nbsp;MES PARA COMPARA&Ccedil;&Atilde;O:
                  <select name="nome1" id="nome1">
                    <option value="1" selected="selected">JANEIRO</option>
                    <option value="2">FEVEREIRO</option>
                    <option value="3">MAR&Ccedil;O</option>
                    <option value="4">ABRIL</option>
                    <option value="5">MAIO</option>
                    <option value="6">JUNHO</option>
                    <option value="7">JULHO</option>
                    <option value="8">AGOSTO</option>
                    <option value="9">SETEMBRO</option>
                    <option value="10">OUTUBRO</option>
                    <option value="11">NOVEMBRO</option>
                    <option value="12">DEZEMBRO</option>
                  </select>
            </span><span class="style24">&nbsp;&nbsp;ANO:
            <select name="ano1" id="ano1">
              <option>2005</option>
              <option>2006</option>
              <option>2007</option>
              <option>2008</option>
              <option>2009</option>
              <option>2010</option>
              <option>2011</option>
              <option>2012</option>
              <option>2013</option>
              <option>2014</option>
              <option>2015</option>
            </select>
            </span></div></td>
            <td width="54%" height="39" align="center" bgcolor="#FFFFFF"><div align="left"><span class="style24">&nbsp;&nbsp;&nbsp;&nbsp;M&Ecirc;S COMPARATIVO:
              <select name="nome2" id="nome2">
                  <option value="1" selected="selected">JANEIRO</option>
                  <option value="2">FEVEREIRO</option>
                  <option value="3">MAR&Ccedil;O</option>
                  <option value="4">ABRIL</option>
                  <option value="5">MAIO</option>
                  <option value="6">JUNHO</option>
                  <option value="7">JULHO</option>
                  <option value="8">AGOSTO</option>
                  <option value="9">SETEMBRO</option>
                  <option value="10">OUTUBRO</option>
                  <option value="11">NOVEMBRO</option>
                  <option value="12">DEZEMBRO</option>
                </select>
            </span><span class="style24"> </span><span class="style24">&nbsp;&nbsp;ANO:
            <select name="ano2" id="ano2">
              <option>2005</option>
              <option>2006</option>
              <option>2007</option>
              <option>2008</option>
              <option>2009</option>
              <option>2010</option>
              <option>2011</option>
              <option>2012</option>
              <option>2013</option>
              <option>2014</option>
              <option>2015</option>
            </select>
            </span></div></td>
          </tr>
          <tr>
            <td height="39" colspan="2" align="center" bgcolor="#FFFFFF"><label></label>              <span class="style24">
              <div align="left">&nbsp;&nbsp;&nbsp;&nbsp;SELECIONE O TIPO:&nbsp; 
                <select name="tipo" id="tipo">
                    <option value="entrada">Entrada</option>
                    <option value="saida" selected="selected">Sa&iacute;da</option>
                </select>
              </div>
              </span></td>
          </tr>
          <tr>
            <td height="39" colspan="2" align="center" bgcolor="#FFFFFF"><span class="style24">
              <div align="left">&nbsp;&nbsp;&nbsp;
                </label>
              DATA PARA REFERENCIA:&nbsp;&nbsp;
<select name="tipodata" id="tipodata">
  <option value="data_proc">Processamento</option>
  <option value="data_vencimento">Vencimento</option>
  <option value="data_pg" selected>Pagamento</option>
</select>
              </div>
            </span></td>
          </tr>
          <tr>
            <td height="39" colspan="2" align="center" bgcolor="#FFFFFF"><label>
              <input type="hidden" name="id" id="id" value="2"/>
              <input type="hidden" name="regiao" id="regiao" value="<?=$regiao?>"/>
              <input type="hidden" name="total_gra" id="total_gra" value="2"/>
              <input type="submit" name="gerar" id="gerar" value="GERAR RELATORIO" />
            </label></td>
          </tr>
        </table>
      </form>
      <?php
/* Liberando o resultado */
mysql_free_result($result_banco);
mysql_free_result($result_projeto);

break;

case 2:

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$banco = $_REQUEST['banco'];
$mes1 = $_REQUEST['nome1'];
$mes2 = $_REQUEST['nome2'];
$ano1 = $_REQUEST['ano1'];
$ano2 = $_REQUEST['ano2'];
$tipo = $_REQUEST['tipo'];
$tipodata = $_REQUEST['tipodata'];

if($tipodata == "data_proc"){
$tipodataf = "Processamento";
}elseif($tipodata == "data_vencimento"){
$tipodataf = "Vencimento";
}else{
$tipodataf = "Pagamento";
}


if($tipo == "saida"){
$tipo_consulta = "0";
}else{
$tipo_consulta = "1";
}

$result_mes1 = mysql_query("SELECT * FROM entradaesaida where tipo = '$tipo_consulta'");

$result_mes2 = mysql_query("SELECT * FROM entradaesaida where tipo = '$tipo_consulta'");

/*

$result_mes1 = mysql_query("SELECT DISTINCT tipo FROM $tipo where id_projeto = '$projeto' and id_banco = '$banco' and month(data_pg) = '$mes1' order by tipo asc");

$result_mes2 = mysql_query("SELECT DISTINCT tipo FROM $tipo where id_projeto = '$projeto' and id_banco = '$banco' and month(data_pg) = '$mes2' order by tipo asc");

$result = mysql_query("SELECT *,date_format(data_pg, '%d/%m/%Y')as data_pg2 FROM $tipo2 where id_projeto = '$projeto' and id_banco = '$banco' and tipo = '$tipo' and month(data_pg) = '$mes' order by data_pg desc");

*/

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$banco' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$tipo'");
$row_tipo = mysql_fetch_array($result_tipo);

switch ($mes1) {

case 1:
$mes_1 = "Janeiro";
break;
case 2:
$mes_1 = "Fevereiro";
break;
case 3:
$mes_1 = "Março";
break;
case 4:
$mes_1 = "Abril";
break;
case 5:
$mes_1 = "Maio";
break;
case 6:
$mes_1 = "Junho";
break;
case 7:
$mes_1 = "Julho";
break;
case 8:
$mes_1 = "Agosto";
break;
case 9:
$mes_1 = "Setembro";
break;
case 10:
$mes_1 = "Outubro";
break;
case 11:
$mes_1 = "Novembro";
break;
case 12:
$mes_1 = "Dezembro";
break;
}

switch ($mes2) {

case 1:
$mes_2 = "Janeiro";
break;
case 2:
$mes_2 = "Fevereiro";
break;
case 3:
$mes_2 = "Março";
break;
case 4:
$mes_2 = "Abril";
break;
case 5:
$mes_2 = "Maio";
break;
case 6:
$mes_2 = "Junho";
break;
case 7:
$mes_2 = "Julho";
break;
case 8:
$mes_2 = "Agosto";
break;
case 9:
$mes_2 = "Setembro";
break;
case 10:
$mes_2 = "Outubro";
break;
case 11:
$mes_2 = "Novembro";
break;
case 12:
$mes_2 = "Dezembro";
break;
}

$mes_1 = strtoupper($mes_1);
$mes_2 = strtoupper($mes_2);

$mes_1 = str_replace("ç","Ç",$mes_1);
$mes_2 = str_replace("ç","Ç",$mes_2);


?><br />
      <table width="90%" border="1" align="center" cellspacing="0" bordercolor="#999999">
          
          <tr>
            <td width="100%" height="39" colspan="2" align="center" bgcolor="#FFFFFF"><table width="99%" border="1" align="center" cellspacing="0" bordercolor="#003300">
              <tr>
                <td width="100%" bgcolor="#003300"><div align="center"><span class="style16">PROJETO:</span><span class="style19">
                    <?=$row_projeto['nome'];?>
                </span></div></td>
              </tr>
              <tr>
                <td bgcolor="#003300"><div align="center"><span class="style16">CONTA:</span><span class="style19">
                  <?=$row_banco['nome'];?>
                  <?=$row_banco['agencia'];?>
                  /
                  <?=$row_banco['conta'];?>
                </span></div></td>
              </tr>
              <tr>
                <td bgcolor="#003300"><div align="center">
                  <div align="center"><span class="style16">COMPARATIVO ENTRE:&nbsp;&nbsp; <span class="style23">
                    <?=$mes_1;?>
                    &nbsp;&nbsp; </span> e&nbsp;&nbsp; <span class="style23">
                      <?=$mes_2;?>
                      </span></span><span class="style16"> &nbsp;&nbsp;ANO DE REFER&Ecirc;NCIA:</span><span class="style19">
                        <?=$ano;?>
                      </span></div>
                </div></td>
              </tr>
              <tr>
                <td bgcolor="#003300"><div align="center">
                  <div align="center"><span class="style16">DATA DE REFERENCIA PARA BUSCA:</span><span class="style19">
                    <?=$tipodataf;?>
                  </span></div>
                </div></td>
              </tr>
            </table>            
            <label></label></td>
          </tr>
      </table>
      <br />
      <br />

<div align="left">
  <table width="40%" border="1" align="left" cellspacing="0"  bordercolor="#003300" >
        <tr>
          <td colspan="2" bgcolor="#003300"><div align="center" class="style23">
            <?=$mes_1;?>
          </div></td>
          </tr>
        <tr>
          <td width="186" bgcolor="#006600"><div align="center" class="style16">ITEM</div></td>
          <td width="128" bgcolor="#006600"><div align="center" class="style16">TOTAL</div></td>
        </tr>
        <?php
$cont = "0";
while($row_mes1 = mysql_fetch_array($result_mes1)){

$cont_row1 = mysql_num_rows($result_mes1);

$result_tipo_mes1 = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row_mes1[0]'");
$row_tipo_mes1 = mysql_fetch_array($result_tipo_mes1);

$result_cont_mes1 = mysql_query("SELECT * FROM $tipo where tipo = '$row_mes1[0]' and id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$mes1' and year($tipodata) = '$ano1' ");
$row_cont_mes1 = mysql_num_rows($result_cont_mes1);

while($row_somando_valor_mes1 = mysql_fetch_array($result_cont_mes1)){
$valor_mes1 = str_replace(",",".",$row_somando_valor_mes1['valor']);
$valor_total_mes1 = $valor_total_mes1 + $valor_mes1;
}

$valor_for_mes1 = number_format($valor_total_mes1,2,",",".");
$total_mes_mes1 = $valor_total_mes1;
$valor_total_mes1 = "0";

//PEDREIRA

$item = array('1');

//PEDREIRA

//ESCONDENDO AS LINHAS COM VALOR ZERADO

if($valor_for_mes1 == "0,00"){
$desable = "style='display:none'";
}else{
$desable = "";
}

print "
<tr $desable height='16'>
<td align='left' class='styleralho'>$row_mes1[0] - $row_tipo_mes1[nome]</td>
<td align='left' class='styleralho'>R$ $valor_for_mes1 <img src='imagensfinanceiro/seta_branca.gif' align='absmiddle'> </td>
</tr>
";
//<td align='center'>$row_cont</td>
$valor_total_final_mes1 = $valor_total_final_mes1 + $total_mes_mes1;

$cont ++;
}
?>
        <tr>
          <td width="186" bgcolor="#CCCCCC"><div align="right"><span class="style21">TOTAL EM
            <?=$mes_1;?>
&nbsp;&nbsp;&nbsp; </span></div></td>
          <td width="128" bgcolor="#CCCCCC"><div align="left">
          <span class="style23">R$ <?=$valor_total_final_mes1?></span></div></td>
        </tr>
      </table>
      </div>
      <table width="60%" border="1" align="center" cellspacing="0"  bordercolor="#003300" >
        <tr>
          <td colspan="4" bgcolor="#003300"><div align="center" class="style23">
            <?=$mes_2;?>
          </div></td>
        </tr>
        <tr>
          <td width="172" bgcolor="#006600"><div align="center" class="style16"></span>ITEM </div></td>
<td width="64" bgcolor="#006600"><div align="center" class="style16">
                  <div align="center" class="style19">TOTAL</div>
          </div></td>
<td width="94" bgcolor="#006600"><div align="center" class="style16">
                  <div align="center" class="style16">VARIA&Ccedil;&Atilde;O</div>
          </div></td>
          <td width="137" bgcolor="#006600"><div align="center" class="style16"></span>PERCENTUAL </div></td>
        </tr>

<?php
while($row_mes2 = mysql_fetch_array($result_mes2)){

$result_tipo_mes2 = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row_mes2[0]'");
$row_tipo_mes2 = mysql_fetch_array($result_tipo_mes2);

$result_cont_mes2 = mysql_query("SELECT * FROM $tipo where tipo = '$row_mes2[0]' and id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$mes2' and year($tipodata) = '$ano2'");
$row_cont_mes2 = mysql_num_rows($result_cont_mes2);


while($row_somando_valor_mes2 = mysql_fetch_array($result_cont_mes2)){
$valor_mes2 = str_replace(",",".",$row_somando_valor_mes2['valor']);
$valor_total_mes2 = $valor_total_mes2 + $valor_mes2;
}

$valor_for_mes2 = number_format($valor_total_mes2,2,",",".");
$total_mes_mes2 = $valor_total_mes2;


//------------------------------------ SUPER CALCULO ------------------------------------


$result_cont_mes1 = mysql_query("SELECT * FROM $tipo where tipo = '$row_mes2[0]' and id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$mes1' and year($tipodata) = '$ano1'");
$row_cont_mes1 = mysql_num_rows($result_cont_mes1);

while($row_somando_valor_mes1 = mysql_fetch_array($result_cont_mes1)){
$valor_mes1 = str_replace(",",".",$row_somando_valor_mes1['valor']);
$valor_total_mes1 = $valor_total_mes1 + $valor_mes1;
}

$comparando_mes1 = mysql_query("SELECT DISTINCT tipo FROM $tipo where id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$mes1' and year($tipodata) = '$ano1' and tipo = '$row_mes2[0]' order by tipo asc");
$conte_comparando = mysql_num_rows($comparando_mes1);

//VERIFICA SE NO MES ANTERIOR TEVE ALGUMA SAIDA/ENTRADA NO TIPO DE SAÍDA/ENTRADA

if($conte_comparando == "1"){

// ---------------- REGISTRANDO OS VALORES NAS VARIAVEIS

$valor_cauculo_mes1 = "$valor_total_mes1";
$valor_cauculo_mes2 = "$valor_total_mes2";

$valor_cauculo_mes1_double = $valor_cauculo_mes1 * 2;
$valor_cauculo_mes2_double = $valor_cauculo_mes2 * 2;


// ---------------- REGISTRANDO OS VALORES NAS VARIAVEIS

/*
$valor_teste_mes1 = "15871.78";
$valor_teste_mes2 = "26080.00";
*/

$desable = "";

//COMEÇO DO TESTE LÓGICO PARA DEFINIR QUAL O CÁLCULO A SER USADO
if($valor_cauculo_mes1 > $valor_cauculo_mes2){

$resposta1 = "Redução de: ";

$variacao = $valor_cauculo_mes1 - $valor_cauculo_mes2;

$resposta2 = (($variacao / $valor_cauculo_mes1) * 100);
$resp_for = number_format($resposta2,2,".","");
$resp_comp = "<font color=green><img src='imagensfinanceiro/seta_cima.gif' align='absmiddle'> <b>".$resposta1.$resp_for."%</b></font>";

}elseif($valor_cauculo_mes1 < $valor_cauculo_mes2){

$resposta1 = "Aumento de: ";

$variacao = $valor_cauculo_mes1 - $valor_cauculo_mes2;

$resposta2 = (($variacao / $valor_cauculo_mes1) * 100);
$resp_for = number_format($resposta2,2,".","");
$resp_comp = "<font color=red><img src='imagensfinanceiro/seta_baixo.gif' align='absmiddle'> <b>".$resposta1.$resp_for."%</b></font>";

}elseif($valor_cauculo_mes1 == $valor_cauculo_mes2){
$resp_comp = "<font color=blue><b>Igual</b> <img src='imagensfinanceiro/seta_branca.gif' align='absmiddle'> </font>";
}

//FIM DO TESTE LÓGICO PARA DEFINIR QUAL O CÁLCULO A SER USADO

}else{
$resp_comp = "<font color=green>Sem Indicador</font>";
$desable = "style='display:none'";
}

$variacao = str_replace("-","",$variacao);
$variacao_f = number_format($variacao,2,",",".");

//------------------------------------ SUPER CALCULO ------------------------------------

print "
 <tr $desable height='16'>
<td align='left' class='styleralho'>$row_mes2[0] - $row_tipo_mes2[nome]</td>
<td align='left' class='styleralho'>R$ $valor_for_mes2</td>
<td align='left' class='styleralho' bgcolor='#FFFFFF' ><b>R$ $variacao_f</b></td>
<td align='left' class='styleralho' bgcolor='#FFFFFF' >$resp_comp</td>
</tr>
";
//<td align='center'>$row_cont</td>ITEM1 (-) ITEM 2 (/) ITEM 1 = X%
$valor_total_final_mes2 = $valor_total_final_mes2 + $total_mes_mes2;

$variacao = "0";
$valor_total_mes1 = "0";
$valor_total_mes2 = "0";
}

//INICIO - CALCULO PARA VERIFICAR OS VALORES DO MES
if($valor_total_final_mes1 > $valor_total_final_mes2){

$resposta1 = "Redução de: ";

$variacao = $valor_total_final_mes1 - $valor_total_final_mes2;

$resposta2 = (($variacao / $valor_total_final_mes1) * 100);
$resp_for = number_format($resposta2,2,".","");
$resp_comp_a = "<font color=green><img src='imagensfinanceiro/seta_cima.gif' > <b>".$resposta1.$resp_for."%</b></font>";

}elseif($valor_total_final_mes1 < $valor_total_final_mes2){

$resposta1 = "Aumento de: ";

$variacao = $valor_total_final_mes1 - $valor_total_final_mes2;

$resposta2 = (($variacao / $valor_total_final_mes1) * 100);
$resp_for = number_format($resposta2,2,".","");
$resp_comp_a = "<font color=red><img src='imagensfinanceiro/seta_baixo.gif' > <b>".$resposta1.$resp_for."%</b></font>";

}elseif($valor_total_final_mes2 == $valor_total_final_mes2){
$resp_comp_a = "<font color=blue><b>Igual</b></font>";
}

//FIM DO TESTE LÓGICO PARA DEFINIR QUAL O CÁLCULO A SER USADO
?>
        <tr>
          <td width="172" bgcolor="#CCCCCC" ><div align="right">
            <p class="style21">TOTAL EM
              <?=$mes_2;?>
&nbsp;&nbsp;&nbsp; </p>
          </div></td>
          <td colspan="2" bgcolor="#CCCCCC"><div align="left"><span class="style23">R$ </span><span class="style23">
            <?=$valor_total_final_mes2?>
          </span></div></td>
          <td bgcolor="#CCCCCC" class='styleralho'><?=$resp_comp_a?>
          </td>
        </tr>
      </table>
      <br />
      <br />
<hr width="699" />
<table width="90%" border="1" align="center" cellspacing="0" bordercolor="#999999">
          <tr>
            <td width="100%" height="39" colspan="2" align="center" bgcolor="#FFFFFF"><br />
              GR&Aacute;FICO DE DESEMPENHO ENTRE <br />
              <span class="style16 style26"><span class="style21">
              <?=$mes_1;?>
              </span> e <span class="style21">
              <?=$mes_2;?>
              </span></span>
              <label></label>
              <br />
              <?php
			  
			  $qtd1 = $valor_total_final_mes1;
			  $qtd2 = $valor_total_final_mes2;
			  
			  $arquivo = "desempenho";
			  
			  ?>
              <br />
                   
              <br />
              <br /></td>
          </tr>
      </table>
        <p>
          <?php
/*
// Liberando o resultado 
mysql_free_result($result_banco);
mysql_free_result($result_projeto);
mysql_free_result($result_tipo);

// Fechando a conex&atilde;o */
mysql_close($conn);


break;

}
}


?>
        </p>
        </div>    </td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>
  
  
  
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#FFFFFF">
<?php
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table>

</body>
</html>
