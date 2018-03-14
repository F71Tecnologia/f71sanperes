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
<title>Intranet - Relat&oacute;rio Mensal por item</title>
<link href="net1.css" rel="stylesheet" type="text/css" />
<link href="net.css" rel="stylesheet" type="text/css">
<link href="novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
<link href="novoFinanceiro/style/estilo_financeiro.css" rel="stylesheet" type="text/css">
<style type="text/css" >
body{
	font-family: Arial, Helvetica, sans-serif;
	text-transform: uppercase!important; 
	font-size: 11px;
	color:#000;
}
.style2 {font-size: 12px}
.style6 {font-size: 14px; font-weight: bold; color: #FFFFFF; }
.style7 {color: #003300}
.style16 {
	font-size: 12px;
	font-weight: bold;
	color: #FFFFFF;
}
.style19 {
	color: #FFFFFF;
	font-weight: bold;
}
.style21 {font-size: 11px; font-weight: bold; }
.style23 {font-size: 11px; font-weight: bold; color: #FF0000; }
.style24 {font-size: 12px; font-weight: bold; }
h1{
	margin:0px;
	padding:0px;
	font-size:14px;
	font-weight:bold;
}
</style>
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
<script type="text/javascript" src="jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="jquery/combo.js" ></script>
<script src="js/global.js" type="text/javascript"></script>
<script type="text/javascript">

$(function(){
	$('#projeto').combo({
					reposta : '#banco',
					url : 'novoFinanceiro/actions/combo.bancos.json.php'
				});
});
</script>

</head>
<link href="net.css" rel="stylesheet" type="text/css" />
<body>

<table id="tbRelatorio" width="750" border="0" align="center" cellpadding="0" cellspacing="0">
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
RELAT&Oacute;RIO MENSAL DESCRITIVO DETALHADO
</h1>
</td>
  </tr>
  
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">

<form action="relsdescritivodetalhado.php" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form1">
      <div align="right">
        <p align="center"><br />
          <?php
if(empty($_REQUEST['projeto'])){
?>
        </p>
        <table width="90%" border="0" align="center" cellspacing="0" bordercolor="#999999">
          <tr>
            <td width="29%" height="27" align="right" valign="middle" bgcolor="#FFFFFF">SELECIONE O PROJETO:<br /></td>
            <td width="71%" valign="middle" bgcolor="#FFFFFF"><div align="left"><span class="style24">
              &nbsp;&nbsp;&nbsp;
              <select name="projeto" class="campotexto" id="projeto">
                <?php
$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' ");
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome]</option>";
}

?>
              </select>
            </span></div></td>
          </tr>
          
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE A CONTA:<br /></td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><div align="left"><span class="style24">
              &nbsp;&nbsp;&nbsp;
              <select name="banco" class="campotexto" id="banco">
                <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and interno ='1' and status_reg ='1'");
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[nome] $row_banco[agencia]/$row_banco[conta]</option>";
}
?>
              </select>
            </span></div></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O M&Ecirc;S / ANO:</td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><div align="left">
              &nbsp;&nbsp;&nbsp;
<select name="mes" class="campotexto" id="meses3">
  <?php 
            	$qr_meses = mysql_query("SELECT * FROM ano_meses");
				while($row_meses = mysql_fetch_array($qr_meses)){
					$selected = ($row_meses[0] == date('m')) ? 'selected="selected"' : '';
					echo "<option value=\"$row_meses[0]\" $selected>$row_meses[1]</option>";
				}
            ?>
</select>
            &nbsp;
            <select name="ano" class="campotexto" id="mes">
              <?php
				for($i = 2005; $i <= (date('Y') + 3); $i ++){
					$selected = ($i == date('Y')) ? 'selected="selected"' : '';
					echo '<option '.$selected.' value="'.$i.'" >'.$i.'</option>';
				}
				?>
            </select>
            </div></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF"><label> SELECIONE O TIPO: </label></td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
              <div align="left" class="campotexto">&nbsp;&nbsp;
                <select name="tipo" class="campotexto" id="tipo" style="	text-transform: uppercase">
                  <option value="entrada">Entrada</option>
                  <option value="saida" selected="selected">Sa&iacute;da</option>
              </select>
              </div>
            </span></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF"><label>
             DATA PARA REFERENCIA:
              </label></td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
              <div align="left" class="campotexto">&nbsp;&nbsp;
<select name="tipodata" class="campotexto" id="tipodata">
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
$mes = $_REQUEST['mes'];
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

$result = mysql_query("SELECT DISTINCT tipo FROM $tipo where id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$mes' and year($tipodata) = '$ano' order by tipo asc");

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$banco' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

switch ($mes) {

case 1:
$mes1 = "Janeiro";
break;
case 2:
$mes1 = "Fevereiro";
break;
case 3:
$mes1 = "Março";
break;
case 4:
$mes1 = "Abril";
break;
case 5:
$mes1 = "Maio";
break;
case 6:
$mes1 = "Junho";
break;
case 7:
$mes1 = "Julho";
break;
case 8:
$mes1 = "Agosto";
break;
case 9:
$mes1 = "Setembro";
break;
case 10:
$mes1 = "Outubro";
break;
case 11:
$mes1 = "Novembro";
break;
case 12:
$mes1 = "Dezembro";
break;

}


?>
        <hr />
        <table width="97%" border="0" align="center" cellpadding="3" cellspacing="0" bordercolor="#003300">
          <tr>
            <td colspan="3" align="center" bgcolor="#F8F8F8">PROJETO:&nbsp;
              <?=$row_projeto['nome'];?>
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" bgcolor="#F8F8F8">CONTA:
              <?=$row_banco['nome'];?>
              <?=$row_banco['agencia'];?>
/
<?=$row_banco['conta'];?>
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" bgcolor="#F8F8F8">M&Ecirc;S DE REFER&Ecirc;NCIA:
              <?=$mes1;?>
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center" bgcolor="#F8F8F8">DATA DE REFERENCIA PARA BUSCA:
              <?=$tipodataf;?>
            </td>
            </tr>
          <tr>
            <td width="64%" bgcolor="#FFFFFF">ITEM</td>
            <td width="16%" bgcolor="#FFFFFF"><div align="center">QUANTIDADE</div></td>
            <td width="20%" bgcolor="#FFFFFF"><div align="center">VALOR TOTAL</div></td>
          </tr>
<?php

while($row = mysql_fetch_array($result)){

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$row[0]'");
$row_tipo = mysql_fetch_array($result_tipo);

$result_cont = mysql_query("SELECT * FROM $tipo where tipo = '$row[0]' and id_projeto = '$projeto' and id_banco = '$banco' and month($tipodata) = '$mes' and year($tipodata) = '$ano' and status='2'");
$row_cont = mysql_num_rows($result_cont);

while($row_somando_valor = mysql_fetch_array($result_cont)){
$valor = str_replace(",",".",$row_somando_valor['valor']);
$valor_total = $valor_total + $valor;
}

$valor_for = number_format($valor_total,2,",",".");
$total_mes = $valor_total;
$valor_total = "0";

$class = ($linhaAlter++%2) ? 'linha_um' : 'linha_dois';

print "<tr class=\"$class\">
<td align='left'>$row[0] - $row_tipo[nome]</td>
<td align='center'>$row_cont</td>
<td align='left'>R$ $valor_for</td>
</tr>";
$valor_total_final = $valor_total_final + $total_mes;
}

$valor_total_for = number_format($valor_total_final,2,",",".");
?>
          <tr>
            <td colspan="2"><div align="right"><span class="style21">TOTAL GASTO EM <?=$mes1;?>&nbsp;</span></div></td>
            <td bgcolor="#FFFFFF"><div align="center"><span class="style23">R$
                  <?=$valor_total_for;?>
            </span></div></td>
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
    <td height="18" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  
  
  
  <tr valign="top">
    <td height="37" colspan="4"><img src="layout/baixo.gif" width="750" height="38" />
<?php
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table>
<?php

/* Liberando o resultado */


/* Fechando a conexão */
mysql_close($conn);
}

?>

</body>
</html>
