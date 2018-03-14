<?php

if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{



include "conn.php";



$id_bol = $_REQUEST['bol'];

$id_bol3 = $_REQUEST['bol3'];

$id_bol2 = $_REQUEST['bol2'];

$tab = $_REQUEST['tab'];

$pro = $_REQUEST['pro'];

$id_reg = $_REQUEST['id_reg'];


$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(sis_data_cadastro, '%d/%m/%Y')as sis_data_cadastro,date_format(data_rg, '%d/%m/%Y')as data_rg FROM $tab where id_bolsista = '$id_bol'", $conn);
$row = mysql_fetch_array($result_bol);


$result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso where id_curso = $row[id_curso]", $conn);
$row_bol3 = mysql_fetch_array($result_bol3);


$result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = $row[id_curso]", $conn);
$row_bol2 = mysql_fetch_array($result_bol2);


$result_reg = mysql_query("Select * from  regioes where id_regiao = $row[regiao]", $conn);
$row_reg = mysql_fetch_array($result_reg);


$result_curso = mysql_query("Select * from  curso where id_curso = $row[id_curso]", $conn);
$row_curso = mysql_fetch_array($result_curso);


$result_pro = mysql_query("Select * from  projeto where id_projeto = $pro", $conn);
$row_pro = mysql_fetch_array($result_pro);

$result_abol = mysql_query("SELECT *,date_format(dada_pis, '%d/%m/%Y') as dada_pis FROM a$tab where id_bolsista = '$id_bol'");
$row_abol = mysql_fetch_array($result_abol);


$result_vale = mysql_query("Select * from vale where id_bolsista = '$id_bol'", $conn);
$row_vale = mysql_fetch_array($result_vale);

$result_banco = mysql_query("Select * from bancos where id_banco = '$row[banco]'");
$row_banco = mysql_fetch_array($result_banco);	

$result_depende = mysql_query ("SELECT *,date_format(data1, '%d/%m/%Y')as data1 ,date_format(data2, '%d/%m/%Y')as data2, date_format(data3, '%d/%m/%Y')as data3, date_format(data4, '%d/%m/%Y')as data4 ,date_format(data5, '%d/%m/%Y')as data5 FROM dependentes where id_bolsista = '$id_bol'", $conn);
$row_depende = mysql_fetch_array($result_depende);	

$dia = date('d');

$mes = date('n');

$ano = date('Y');

switch ($mes) {

case 1:

$mes = "Janeiro";

break;

case 2:

$mes = "Fevereiro";

break;

case 3:

$mes = "Março";

break;

case 4:

$mes = "Abril";

break;

case 5:

$mes = "Maio";

break;

case 6:

$mes = "Junho";

break;

case 7:

$mes = "Julho";

break;

case 8:

$mes = "Agosto";

break;

case 9:

$mes = "Setembro";

break;

case 10:

$mes = "Outubro";

break;

case 11:

$mes = "Novembro";

break;

case 12:

$mes = "Dezembro";

break;

}



?>

<html xmlns="undefined">

<head>

<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">

<title>SOLICITA&Ccedil;&Atilde;O DE VALE TRANPORTE </title>

<style>

<!--

p.MsoAcetate, li.MsoAcetate, div.MsoAcetate

	{font-size:8.0pt;

	font-family:"Tahoma","sans-serif";}

.style4 {font-size: 11px; font-weight: bold; color: #FFFFFF;}

.style8 {font-size: 12px}

body {

	margin-left: 5px;

	margin-top: 0px;

	margin-right: 5px;

	margin-bottom: 0px;

}
.style9 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style11 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
}
.style12 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
}
.style13 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 9px;
}
.style16 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; }
.style19 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #FFFFFF;
	font-size: 13px;
}

-->

</style>

</head>

<body bgcolor="#FFFFFF" lang=PT-BR>

<table width="720">

  <tr>

    <td><table width="650">
      <tr>
        <td width="155"><?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src="imagens/certificadosrecebidos.gif" alt="soe" width="120" height="86" align="left">--></td>
        <td width="334"><table width="95" height="38" border="1" align="center" cellpadding="0" cellspacing="0" hspace="0" vspace="0">
          <tr>
              <td width="103" height="15" align="left" valign="middle" bgcolor="#336633"><p align="center" class="style4">C&Oacute;DIGO:</p></td>
            </tr>
            <tr>
              <td height="20" align="left" valign="top"><div align="center"><b><?php print "$row[campo3]"; ?></b></div></td>
            </tr>
          </table>          </td>
        <td width="103"><?php 

$result_abolsistas = mysql_query("Select * from abolsista$pro where id_bolsista = '$id_bol'");
$row_abol = mysql_fetch_array($result_abolsistas);

$nome_arq = str_replace(" ", "_", $row['nome']);	
$id_bolsistaaa = $row['0'];

if($row_abol['foto'] == "1"){
$nome_imagem = $id_reg."_".$pro."_".$id_bolsistaaa.".gif"; 
}else{
$nome_imagem = 'semimagem.gif';
} 

print "<img src='fotos/$nome_imagem' width='100' height='130' border=1 align='absmiddle'>";   

?></td>
      </tr>
    </table>
      <table width="650">
        <tr>
          <td colspan="4" bgcolor="#336633"><div align="center" class="style19">DADOS DO FUNCION&Aacute;RIO</div></td>
        </tr>
        <tr>
          <td colspan="4"><span class="style9 style8">NOME: <b> <font color="#FF0000"> <?php print "$row[nome]"; ?> </font></b></span></td>
        </tr>
        <tr>
          <td colspan="4"><span class="style9 style8">UNIDADE: <b><font color="#FF0000"><?php print "$row[locacao]"; ?> </font></b></span></td>
        </tr>
        <tr>
          <td colspan="4"><span class="style9 style8">FILIA&Ccedil;&Atilde;O:<b> <font color="#FF0000"><?php print "$row[mae]"; ?></font></b> / <b><font color="#FF0000"><?php print "$row[pai]"; ?></font></b></span></td>
        </tr>
        <tr>
          <td colspan="2"><span class="style9 style8">NASCIMENTO: <b><font color="#FF0000"><?php print "$row[data_nasci]"; ?></font></b></span></td>
          <td colspan="2"><span class="style9 style8">CPF: <b><font color="#FF0000"><?php print "$row[cpf]"; ?></font></b></span></td>
        </tr>
        <tr>
          <td colspan="2"><span class="style9 style8">RG: <b><font color="#FF0000"><?php print "$row[rg]"; ?> / <?php print "$row[orgao]"; ?></font></b></span></td>
          <td colspan="2"><span class="style9 style8">EXPEDI&Ccedil;&Atilde;O: <b><font color="#FF0000"><?php print "$row[data_rg]"; ?></font></b></span></td>
        </tr>
        <tr>
          <td colspan="4"><span class="style9 style8">ENDERE&Ccedil;O: <b><font color="#FF0000"><?php print "$row[endereco]"; ?></font></b></span></td>
        </tr>
        <tr>
          <td colspan="2"><span class="style9 style8">BAIRRO: <b><font color="#FF0000"><?php print "$row[bairro]"; ?></font></b></span></td>
          <td colspan="2"><span class="style9 style8">CIDADE: <b><font color="#FF0000"><?php print "$row[cidade]"; ?></font></b></span></td>
        </tr>
        <tr>
          <td colspan="2"><span class="style9 style8">UF: <b><font color="#FF0000"><?php print "$row[uf]"; ?></font></b></span></td>
          <td colspan="2"><span class="style9 style8"><b>CEP: <font color="#FF0000"><?php print "$row[cep]"; ?></font></b></span></td>
        </tr>
        <tr>
          <td colspan="4"><span class="style9 style8">TURNO: _______________________________________________________</span></td>
        </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" bgcolor="#336633"><div align="center"><span class="style19">DADOS DO VALE SOLICITADO</span></div></td>
        </tr>
        
        <tr>
          <td colspan="2"><span class="style9">
            <label>&nbsp;&nbsp;
            <input type="checkbox" name="checkbox" id="checkbox">
            </label>
          &nbsp;&nbsp;VALE(S) EM PAPEL: </span> </td>
          <td width="145">&nbsp;</td>
          <td width="143">&nbsp;</td>
        </tr>
        <tr>
          <td><span class="style16">Vale 1:________________<br>
            <br>
          Quantidade 1:_________<br>
          <br>
          Tipo 1: _______________</span></td>
          <td><span class="style16">Vale 2:_____________<br>
              <br>
Quantidade 2:_______<br>
<br>
Tipo 2: _____________</span></td>
          <td><span class="style16">Vale 3:_____________<br>
              <br>
Quantidade 3:_______<br>
<br>
Tipo 3: _____________</span></td>
          <td><span class="style16">Vale 4:_____________<br>
              <br>
Quantidade 4:_______<br>
<br>
Tipo 4: _____________</span></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><span class="style9">
            <label>&nbsp;&nbsp;
            <input type="checkbox" name="checkbox2" id="checkbox2">
            </label>
&nbsp;VALE(S) EM CART&Atilde;O: </span></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td width="170"><span class="style16">          N&ordm; do Cart&atilde;o 1:_____________</span></td>
          <td width="130"><span class="style16">Valor 1:____________</span></td>
          <td><span class="style16">N&ordm; do Cart&atilde;o 2:__________</span></td>
          <td><span class="style16">Valor 2:____________</span></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4"><span class="style16">Itiner&aacute;rio De:_____________________________________________ Para:_________________________________________</span></td>
        </tr>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4"><span class="style16">Via&ccedil;&atilde;o:_______________________________________________________</span></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" bgcolor="#336633"><div align="center"><span class="style19">CONFIRMA&Ccedil;&Atilde;O DA SOLICITA&Ccedil;&Atilde;O</span></div></td>
        </tr>
        <tr>
          <td colspan="4" bgcolor="#FFFFFF">&nbsp;<br>
            <span class="style11">Atesto que solicitei os vales acima mencionados em quantidade e valor e tipo;            <br>
            <br>
            </span>
            <div align="center">
              <p><span class="style13"><br>
                _____________________________________________________<br>
                  <strong>ASSINATURA DO REQUERINTE</strong></span><span class="style12"><br>  
                  <br>
                  <br>
                  <br>
              </span></p>
              <p class="style12">_______________________________________________<br>
                <strong>ASSINATURA DO RESPONS&Aacute;VEL</strong></p>
              <p><br>
              </p>
            </div></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</body>

</html>

<?php

}

?>