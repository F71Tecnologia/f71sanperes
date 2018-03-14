<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

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
<script type="text/javascript">

$(function(){
	$('#projeto').combo({
					reposta : '#banco',
					url : 'novoFinanceiro/actions/combo.bancos.json.php'
				});
});
</script>
</head>
<link href="net1.css" rel="stylesheet" type="text/css" />
<link href="novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
<link href="novoFinanceiro/style/estilo_financeiro.css" rel="stylesheet" type="text/css">
<body>
<table width="750" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
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
?>RELAT&Oacute;RIO MENSAL DESCRITIVO
</td>
  </tr>
  
  <tr>
    <td height="96" colspan="2" align="center" valign="top" bgcolor="#FFFFFF">

<form action="" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form1">
      <div align="right">
        <p align="center"><br />
        </p>
<?php

if(empty($_REQUEST['projeto'])){
		
?>
        <table width="90%" border="0" align="center" cellspacing="0" bordercolor="#999999">
          <tr>
            <td width="36%" height="27" align="right" valign="middle" bgcolor="#FFFFFF">SELECIONE O PROJETO:</td>
            <td width="64%" align="left" bgcolor="#FFFFFF"><span class="style24">
              &nbsp;&nbsp;&nbsp;
              <select name="projeto" id="projeto">
                <?php
$result_projeto = mysql_query("SELECT * FROM projeto where id_regiao = '$regiao'  ");
while($row_projeto = mysql_fetch_array($result_projeto)){
print "<option value=$row_projeto[0]>$row_projeto[0] - $row_projeto[nome]</option>";
}

?>
              </select>
            </span></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE A CONTA:</td>
            <td align="left" valign="baseline" bgcolor="#FFFFFF"><span class="style24">
              &nbsp;&nbsp;&nbsp;
              <select name="banco" id="banco">
                <?php
$result_banco = mysql_query("SELECT * FROM bancos where id_regiao = '$regiao' and interno = '1' and status_reg ='1'");
while($row_banco = mysql_fetch_array($result_banco)){
print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[nome] $row_banco[agencia]/$row_banco[conta]</option>";
}
?>
              </select>
            </span></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O M&Ecirc;S / ANO:</td>
            <td align="left" bgcolor="#FFFFFF"><span class="style24">
              &nbsp;&nbsp;&nbsp;
<select name="mes" id="meses3">
 <?php 
            	$qr_meses = mysql_query("SELECT * FROM ano_meses");
				while($row_meses = mysql_fetch_array($qr_meses)){
					$selected = ($row_meses[0] == date('m')) ? 'selected="selected"' : '';
					echo "<option value=\"$row_meses[0]\" $selected>$row_meses[1]</option>";
				}
            ?> 
</select>
            &nbsp;
            <select name="ano" id="mes">
               <?php
				for($i = 2005; $i <= (date('Y') + 3); $i ++){
					$selected = ($i == date('Y')) ? 'selected="selected"' : '';
					echo '<option '.$selected.' value="'.$i.'" >'.$i.'</option>';
				}
				?>
            </select>
            </span></td>
          </tr>
          
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O &Iacute;TEM:</td>
            <td height="27" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;
              <select name="tipo" id="tiposdesaidas3">
              <option value='0'>Todos os Tipos - Entrada</option>
              <option value='0'>Todos os Tipos - Saída</option>
              <?php
$result_tipo = mysql_query("SELECT * FROM entradaesaida order by tipo,nome");
while($row_tipo = mysql_fetch_array($result_tipo)){

 if($row_tipo['tipo'] == "0"){
  $tipo_p = "(Saída)";
 }else{
  $tipo_p = "(Entrada)";
 }

print "<option value='$row_tipo[id_entradasaida]'>$row_tipo[0] - $row_tipo[nome] - $tipo_p</option>";
}

?>
            </select></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">SELECIONE O TIPO:</td>
            <td height="27" align="left" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;
              <select name="tipo2" id="tipo">
              <option value="entrada">Entrada</option>
              <option value="saida" selected="selected">Sa&iacute;da</option>
            </select></td>
          </tr>
          <tr>
            <td height="27" align="right" bgcolor="#FFFFFF">DATA PARA REFERENCIA: </label></td>
            <td height="27" align="center" valign="middle" bgcolor="#FFFFFF"><span class="style24">
              <div align="left">&nbsp;&nbsp;&nbsp;
                </label>
                <select name="tipodata" id="tipodata">
                  <option value="data_proc">Processamento</option>
                  <option value="data_vencimento">Vencimento</option>
                  <option value="data_pg" selected>Pagamento</option>
                </select>
              </div>
            </span></td>
          </tr>
          <tr>
            <td height="39" colspan="2" align="center" bgcolor="#FFFFFF"><input name="gerar" type="submit" class="submit-go" id="gerar" value="GERAR RELATORIO" /></td>
          </tr>
        </table>
        
      </form>
<?php
/* Liberando o resultado */
mysql_free_result($result_banco);
mysql_free_result($result_projeto);
mysql_free_result($result_tipo);

}else{

$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$banco = $_REQUEST['banco'];
$tipo = $_REQUEST['tipo'];
$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$tipo2 = $_REQUEST['tipo2'];
$tipodata = $_REQUEST['tipodata'];

if($tipodata == "data_proc"){
$tipodataf = "Processamento";
}elseif($tipodata == "data_vencimento"){
$tipodataf = "Vencimento";
}else{
$tipodataf = "Pagamento";
}


$result = mysql_query("SELECT *,date_format(data_pg, '%d/%m/%Y')as data_pg2 FROM $tipo2 where id_projeto =
 '$projeto' and id_banco = '$banco' and tipo = '$tipo' and month($tipodata) = '$mes' and year($tipodata) = '$ano' and status='2' order by data_pg desc");

if($tipo2 == "entrada"){
$tipo_select = "1";//ENTRADA É TIPO 1
}else{
$tipo_select = "0";//SAIDA É TIPO 0
}

$result_desc = mysql_query("SELECT * FROM entradaesaida WHERE tipo = '$tipo_select'");

$result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);

$result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$banco' and status_reg ='1'");
$row_banco = mysql_fetch_array($result_banco);

$result_tipo = mysql_query("SELECT * FROM entradaesaida where id_entradasaida = '$tipo'");
$row_tipo = mysql_fetch_array($result_tipo);

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

if($tipo != "0"){
?>
<table width="97%" border="0" align="center" cellspacing="0" bordercolor="#003300">
<tr>
<td colspan="5" bgcolor="#FFFFFF"><div align="center">
<span class="style16">PROJETO:</span> <span class="style19"><?=$row_projeto['nome'];?></span></div></td>
</tr>
<tr>
<td colspan="5" bgcolor="#FFFFFF"><div align="center">
<span class="style16">CONTA:</span> <span class="style19"><?=$row_banco['nome'];?> <?=$row_banco['agencia'];?> / <?=$row_banco['conta'];?></span></div></td>
</tr>
<tr>
  <td colspan="5" bgcolor="#FFFFFF"><div align="center">
    <div align="center"><span class="style16">ITEM:</span> <span class="style19">
      <?=$row_tipo['nome'];?>
      <br />
      </span><span class="style16">M&Ecirc;S DE REFER&Ecirc;NCIA:</span> <span class="style19">
        <?=$mes1;?>
      </span></div>
  </div></td>
</tr>
<tr>
<td colspan="5" bgcolor="#C5C2C2">&nbsp;</td>
        </tr>
          <tr>
            <td width="28%" bgcolor="#CACACA"><div align="center"><span class="style21">NOME</span></div></td>
            <td width="34%" bgcolor="#CACACA"><div align="center"><span class="style21">DESCRI&Ccedil;&Atilde;O</span></div></td>
            <td width="9%" bgcolor="#CACACA"><span class="style21">PAGO POR</span></td>
            <td width="14%" align="center" bgcolor="#CACACA"><span class="style21">PAGO EM</span></td>
            <td width="15%" bgcolor="#CACACA"><div align="center"><span class="style21">VALOR</span></div></td>
          </tr>
          <?php 
		  
		  while($row = mysql_fetch_array($result)){
		  $valor_cauculo = str_replace(",",".",$row['valor']);
		  $valor_for = number_format($valor_cauculo,2,",",".");
		  print "
          <tr>
            <td align='center'><span class='style21'>$row[nome]</span></td>
            <td align='center'><span class='style21'>$row[especifica]</span></td>
            <td align='center'><span class='style21'>$row[id_userpg]</span></td>
            <td align='center'><span class='style21'>$row[data_pg2]</span></td>
            <td align='left'><span class='style21'>R$ $valor_for</span></td>
          </tr>
          <tr>";

		  $valor_soma = $valor_soma + $valor_cauculo;
		  
		  }
		  
		  $valor_final = number_format($valor_soma,2,",",".");

		  ?>
            <td colspan="4"><div align="right"><span class="style21">TOTAL GASTO COM <?=$row_tipo['nome'];?> EM <?=$mes1;?>&nbsp;</span></div></td>
            <td align="left" bgcolor="#FFFFFF"><div align="center"><span class="style23">R$ <?=$valor_final;?></span></div></td>
          </tr>
      </table>    
      <p>&nbsp;</p>  
<?php

$valor_final = "0";
}else{
?>  
<br />
<br />
<table width="97%" border="0" align="center" cellspacing="0" bordercolor="#003300">
  <tr>
    <td bgcolor="#FFFFFF"><div align="center"> <span class="style16">PROJETO:</span> <span class="style19">
      <?=$row_projeto['nome'];?>
    </span></div></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"><div align="center"> <span class="style16">CONTA:</span> <span class="style19">
      <?=$row_banco['nome'];?>
      <?=$row_banco['agencia'];?>
      /
      <?=$row_banco['conta'];?>
    </span></div></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"><div align="center">
      <div align="center"><span class="style19"><span class="style16">M&Ecirc;S DE REFER&Ecirc;NCIA:</span>
        <?=$mes1;?>
        <br />
      </span></div>
    </div></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"><div align="center">
      <div align="center"><span class="style16">DATA DE REFERENCIA PARA BUSCA:</span><span class="style19">
        <?=$tipodataf;?>
      </span></div>
    </div></td>
  </tr>
  </table>
   <p>&nbsp;</p>  
<?php 
		  
		  while($row_desc = mysql_fetch_array($result_desc)){
  		  
		  $result_tipo2 = mysql_query("SELECT * FROM entradaesaida where 
		  id_entradasaida = '$row_desc[id_entradasaida]'");
		  $row_tipo2 = mysql_fetch_array($result_tipo2);
		  		  
		  $result_2 = mysql_query("SELECT *,date_format(data_pg, '%d/%m/%Y')as data_pg2 FROM $tipo2 where 
		  id_projeto = '$projeto' and id_banco = '$banco' and tipo = '$row_desc[id_entradasaida]' and
		   month($tipodata) = '$mes' and year($tipodata) = '$ano' and status='2' order by data_pg desc");
		   
		  $row_cont_result2 = mysql_num_rows($result_2);

		  if($row_cont_result2 == "0"){
		  $visualizar = "style='display:none'";
		  }else{
		  $visualizar = "";
		  }
?>

  <table width="97%" border="0" align="center" cellspacing="0" bordercolor="#003300" <?=$visualizar?>>
  <tr>
    <td colspan="5" bgcolor="#C5C2C2"><div align="center">
      <div align="center"><span class="style16">ITEM:</span> <span class="style19">
        <?=$row_tipo2['nome'];?>
        <br />
        </span></div>
    </div></td>
  </tr>
  <tr>
    <td width="28%" bgcolor="#CACACA"><div align="center"><span class="style21">NOME</span></div></td>
    <td width="34%" bgcolor="#CACACA"><div align="center"><span class="style21">DESCRI&Ccedil;&Atilde;O</span></div></td>
    <td width="9%" bgcolor="#CACACA"><span class="style21">PAGO POR</span></td>
    <td width="14%" align="center" bgcolor="#CACACA"><span class="style21">PAGO EM</span></td>
    <td width="15%" bgcolor="#CACACA"><div align="center"><span class="style21">VALOR</span></div></td>
  </tr>
  <?php
  
		  while($row_2 = mysql_fetch_array($result_2)){
		  
		  $valor_cauculo = str_replace(",",".",$row_2['valor']);
		  $valor_for = number_format($valor_cauculo,2,",",".");
		  $class = ($alter_color++%2) ? 'linha_um' : 'linha_dois';
		  print "
          <tr class=\"$class\">
            <td align='center'>$row_2[nome]</td>
            <td align='center'>$row_2[especifica]</td>
            <td align='center'>$row_2[id_userpg]</td>
            <td align='center'>$row_2[data_pg2]</td>
            <td align='left'>R$ $valor_for</td>
          </tr>
          <tr>";

		  $valor_soma = $valor_soma + $valor_cauculo;
		  
		  }
		  
		  $valor_final = number_format($valor_soma,2,",",".");
		  $valor_soma = "0";
		  
	?>
  <tr>
    <td colspan="4"><div align="right"><span class="style21">TOTAL GASTO COM
      <?=$row_tipo2['nome'];?>
      EM
      <?=$mes1;?>
      &nbsp;</span></div></td>
    <td align="left" bgcolor="#FFFFFF"><div align="center"><span class="style23">R$
      <?=$valor_final;?>
    </span></div></td>
  </tr>
</table>


  <?php
$valor_final = "0";
}
}
?>
  
  <div align="center"><br />
  <a href="javascript:history.go(-1)"><img src="imagens/voltar.gif" border="0"/></a>
  <br />

<?php

}

?>
  <br />
</div></td>
  </tr>
  
  <tr>
    <td height="18" colspan="2" align="center" valign="top">&nbsp;</td>
  </tr>
  
  
  
  <tr valign="top">
    <td height="37" colspan="4" bgcolor="#E2E2E2"><img src="layout/baixo.gif" width="750" height="38" />
<?php
$rod = new empresa();
$rod -> rodape();
?></td>
  </tr>
</table>
<?php


/* Fechando a conexão */
mysql_close($conn);
}

?>

</body>
</html>
