<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

$hostname = $_SERVER['REMOTE_ADDR'];

include "conn.php";
$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet - Relat&oacute;rio descritivo Mensal</title>
<style type="text/css">
<!--
body{
	font-family: Arial, Helvetica, sans-serif;
	text-transform: uppercase!important; 
	font-size: 11px;
	color:#000;
}
h1{
	margin:0px;
	padding:0px;
	font-size:14px;
	font-weight:bold;
}
.style2 {font-size: 12px}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style16 {
	font-size: 12px;
	font-weight: bold;
	color: #000;
}
.style19 {
	color: #000;
	font-weight: bold;
	font-size: 12px;
}
.style21 {
	font-size: 12px;
	font-weight: bold;
}
.style23 {font-size: 11px; font-weight: bold; color: #FF0000; }
.style24 {font-size: 12px; font-weight: bold; }
-->
</style>
<?php
print "
<script>
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
             

       situacao = \"\";  
       // verifica o dia valido para cada mes  
       if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
           situacao = \"falsa\";  
       }  

       // verifica se o mes e valido  
       if (mes < 01 || mes > 12 ) {  
              situacao = \"falsa\";  
       }  

      // verifica se e ano bissexto  
      if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
            situacao = \"falsa\";  
      }  
   
     if (d.value == \"\") {  
          situacao = \"falsa\";  
    }  

    if (situacao == \"falsa\") {  
       alert(\"Data digitada é inválida, digite novamente!\"); 
       d.value = \"\";  
       d.focus();  
    }  
	
}
</script></head>";

?>
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="jquery/combo.js" ></script>
<script type="text/javascript" src="js/global.js" ></script>
<script type="text/javascript">

$(function(){
	$('#projeto').combo({
					reposta : '#banco',
					url : 'novoFinanceiro/actions/combo.bancos.json.php'
				});
});
</script>

<link href="net1.css" rel="stylesheet" type="text/css" />
<link href="novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
<link href="novoFinanceiro/style/estilo_financeiro.css" rel="stylesheet" type="text/css">
<body>
<table id="tbRelatorio" width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td colspan="4"><img src="layout/topo.gif" width="750" height="38" /></td>
  </tr>
  
  <tr>
    <td width="21" rowspan="4" background="layout/esquerdo.gif">&nbsp;</td>
    <td width="354" align="center" valign="middle" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="349" align="left" bgcolor="#FFFFFF">&nbsp;</td>
    <td width="26" rowspan="4" background="layout/direito.gif">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF">
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?>
<h1>
RELAT&Oacute;RIO ANUAL DESCRITIVO
</h1>
</td>
  </tr>
  
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">

<form action="reldescritivoanual.php" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form1">
      <div align="right">
        <p align="center">
          <?php

if(empty($_REQUEST['projeto'])){
		
?>
          <br />
          <br />
        </p>
        <table width="90%" border="0" align="center" cellspacing="0" bordercolor="#999999">
          <tr>
            <td width="34%" height="27" align="right" valign="middle" bgcolor="#FFFFFF">SELECIONE O PROJETO: </td>
            <td width="66%" bgcolor="#FFFFFF"><div align="left"><span class="style24">
              &nbsp;&nbsp;
              <select name="projeto" id="projeto">
              <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao' ");
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome]</option>";
}

?>
              </select>
            </span></div></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE A CONTA: </td>
            <td height="27" align="center" bgcolor="#FFFFFF"><div align="left"><span class="style24">
              &nbsp;&nbsp;
              <select name="banco" id="banco" >
                <?php
$result_banco = mysql_query("SELECT * FROM bancos where interno = '1' and status_reg = '1' AND id_regiao = '$regiao'");
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[nome] $row_banco[agencia] / $row_banco[conta]</option>";
}
?>
              </select>
            </span></div></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O ANO:</td>
            <td height="27" align="center" bgcolor="#FFFFFF"><div align="left">
              &nbsp;&nbsp;
              <select name="ano" id="ano">
                <?php
				for($i = 2005; $i <= (date('Y') + 3); $i ++){
					$selected = ($i == date('Y')) ? 'selected="selected"' : '';
					echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				}
				?>
                
              </select>
            </div></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">
              SELECIONE O TIPO: 
            
            </span></td>
            <td height="27" align="center" bgcolor="#FFFFFF"><span class="style24">
              <div align="left">&nbsp;&nbsp;
                <select name="tipo" id="tipo">
                  <option value="entrada">Entrada</option>
                  <option value="saida" selected="selected">Saída</option>
            </select>
                  </label>
            </div>
              </span></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">DATA PARA REFERENCIA</td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
              <div align="left">&nbsp;&nbsp;
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
              <input name="gerar" type="submit" class="submit-go" id="gerar" value="GERAR RELATORIO" />
            </label></td>
            </tr>
        </table>
        <br />
        <?php
/* Liberando o resultado */
mysql_free_result($result_banco);
mysql_free_result($result_projeto);
//mysql_free_result($result_tipo);

}else{

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$banco = $_REQUEST['banco'];
$ano = $_REQUEST['ano'];
$tipo = $_REQUEST['tipo'];
$tipodata = $_REQUEST['tipodata'];

if($tipodata == "data_proc"){
$tipodataf = "Processamento";
}elseif($tipodata == "data_vencimento"){
$tipodataf = "Vencimento";
}else{
$tipodataf = "Pagamento";
}

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$banco' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);


?>
        <hr />
        <table width="97%" border="0" align="center" cellspacing="0" bordercolor="#003300">
          <tr>
            <td colspan="2"><div align="center"><span class="style16">PROJETO:</span><span class="style19">
              <?=$row_projeto['nome'];?>
            </span></div></td>
          </tr>
          <tr>
            <td colspan="2"><div align="center"><span class="style16">CONTA:</span><span class="style19">
            <?=$row_banco['nome'];?>
            <?=$row_banco['agencia'];?>
/
<?=$row_banco['conta'];?>
            </span></div></td>
          </tr>
          <tr>
            <td colspan="2"><div align="center"><span class="style16">ANO DE REFER&Ecirc;NCIA:</span><span class="style19">
              <?=$ano;?>
            </span></div></td>
          </tr>
          <tr>
            <td colspan="2"><div align="center">
              <div align="center"><span class="style16">DATA DE REFERENCIA PARA BUSCA:</span><span class="style19">
                <?=$tipodataf;?>
              </span></div>
            </div></td>
          </tr>
          <tr>
            <td colspan="2"><div align="center">
              <div align="center"><span class="style16">TIPO:</span><span class="style19">
              <?=$tipo;?>
              </span></div>
            </div></td>
            </tr>
<?php
$meses = "1";
while($meses < 13){

switch ($meses) {

case 1:
$meses_1 = "Janeiro";
break;
case 2:
$meses_1 = "Fevereiro";
break;
case 3:
$meses_1 = "Março";
break;
case 4:
$meses_1 = "Abril";
break;
case 5:
$meses_1 = "Maio";
break;
case 6:
$meses_1 = "Junho";
break;
case 7:
$meses_1 = "Julho";
break;
case 8:
$meses_1 = "Agosto";
break;
case 9:
$meses_1 = "Setembro";
break;
case 10:
$meses_1 = "Outubro";
break;
case 11:
$meses_1 = "Novembro";
break;
case 12:
$meses_1 = "Dezembro";
break;

}

$result2 = mysql_query("SELECT DISTINCT tipo FROM $tipo where id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$meses' and year($tipodata) = '$ano' order by tipo asc");


?>

          <tr>
            <td colspan="2" bgcolor="#D8D8D8"><div align="center"><span class="style21"><?=$meses_1;?></span></div>              <div align="center"></div>              <div align="center"></div></td>
            </tr>
          

<?php

while($row2 = mysql_fetch_array($result2)){

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row2[0]'");
$row_tipo = mysql_fetch_array($result_tipo);

$result_cont2 = mysql_query("SELECT * FROM $tipo where tipo = '$row2[0]' and id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$meses' and year($tipodata) = '$ano' and status = '2'");
$result_cont_individual_tipo = mysql_num_rows($result_cont2);

while($row_somando_valor = mysql_fetch_array($result_cont2)){
$valor_s_ponto = $row_somando_valor['valor'];
$valor_puro = str_replace(",",".",$valor_s_ponto);

$valor_cauculo = $valor_puro + $valor_cauculo;

$valor_s_ponto_adi = $row_somando_valor['adicional'];
$valor_puro_adi = str_replace(",",".",$valor_s_ponto_adi);

$valor_cauculo_adi = $valor_puro_adi + $valor_cauculo_adi;


}

//$valor_total_tipo = "$valor_total_tipo" + "$valor_total_tipo_adi";

$valor_total_tipo = $valor_cauculo + $valor_cauculo_adi;
$valor_total_tipo_adi = $valor_cauculo_adi;

$valor_tipo_for = number_format($valor_total_tipo,2,",",".");
$valor_tipo_for_adi = number_format($valor_total_tipo_adi,2,",",".");
$class = ($alter_Color++%2) ? 'linha_um' : 'linha_dois';

print "
<tr class=\"$class\">
<td align='left'>$row2[0] - $row_tipo[nome]</td>
<td align='left'>R$ $valor_tipo_for</td>
</tr>
";
$valor_total_mensal = $valor_total_mensal + $valor_total_tipo;
$valor_cauculo = "0";
$valor = "0";
$valor_cauculo_adi = "0";
}


$valor_total_anual = $valor_total_anual + $valor_total_mensal;
$valor_total_mensal_for = number_format($valor_total_mensal,2,",",".");
?>        
<!--<tr>
            <td width="74%" bgcolor="#FFFFFF"><div align="center"><span class="style21">TIPO</span></div></td>
            <td width="26%" bgcolor="#FFFFFF"><div align="center"><span class="style21">VALOR</span></div></td>
          </tr>-->
          <tr>
            <td bgcolor="#F4F4F4"><div align="right"><span class="style21">TOTAL GASTO COM EM <?=$meses_1;?></span></div></td>
            <td bgcolor="#F4F4F4"><div align="center"><span class="style23">R$ <?=$valor_total_mensal_for;?></span></div></td>
          </tr>
          <tr>
            <td colspan="2"><hr /></td>
            </tr>
<?php
$meses ++;
$valor_total_mensal = "0";
$valor_total_mensal_for = "0";
}


$valor_total_anual_for = number_format($valor_total_anual,2,",",".");
?>          

          <tr>
            <td><div align="right"><span class="style21">TOTAL GASTO COM EM <?=$ano;?></span></div></td>
            <td bgcolor="#FFFFFF"><div align="center"><span class="style23">R$ <?=$valor_total_anual_for;?></span></div></td>
          </tr>
        </table>
        <p align="center"><a href="javascript:history.go(-1)"><img src="imagens/voltar.gif" alt="" border="0"/></a><p id="excel" style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Relatório')" value="Exportar para Excel" class="exportarExcel"></p> <br />
<?php 

}

?>
        </p>
        </div>
      </form>    </td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>
  
  
  
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="layout/baixo.gif" width="750" height="38" />
<?php
$rod = new empresa();
$rod -> rodape();
?>
</td>
  </tr>
</table>
<?php

/* Liberando o resultado 
mysql_free_result($result_banco);
mysql_free_result($result_projeto);
mysql_free_result($result_tipo);
*/
/* Fechando a conexão */
mysql_close($conn);
}

?>

</body>
</html>
