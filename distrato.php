<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id_bolsista = $_REQUEST['bol'];
$id_projeto = $_REQUEST['pro'];
$id_regiao = $_REQUEST['id_reg'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '19' and id_clt = '$id_bolsista'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('19','$id_bolsista','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bolsista' and tipo = '19'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$result_bol = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y')as data2, date_format(data_entrada, '%d/%m/%Y')as nova_data2 FROM autonomo where id_autonomo = '$id_bolsista'", $conn);
$row_bol = mysql_fetch_array($result_bol);

$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'", $conn);
$row_pro = mysql_fetch_array($result_pro);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);


$data_hj = date('d/m/Y');

$dia = date('d');
$mes = date('n');
$ano = date('Y');
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
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='net1.css' rel='stylesheet' type='text/css'>

<style type='text/css'>
<!--
.style1 {color: #FF0000;
	font-weight: bold;}
.style5 {font-size: 12px}
.style6 {font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;}
.style7 {color: #FF0000}
.style11 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; font-weight: bold; }
.style13 {font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style15 {color: #FF0000; font-weight: bold; font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
.style16 {font-size: 11px}
-->
</style>
</head>

<body bgcolor='#FFFFFF'>
<center>
<table border='0' cellpadding='0' cellspacing='0' width="757" bgcolor="#FFFFFF">
  <col class='xl65' width='87' style=' width:65pt' />
  <col class='xl65' width='361' style=' width:271pt' />
  <col class='xl65' width='87' style=' width:65pt' />
  <col class='xl65' width='296' style=' width:222pt' />
  <col class='xl65' width='87' style=' width:65pt' />
  <col class='xl65' width='361' style=' width:271pt' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' span='2' style='display:none; ' />
  <col class='xl65' width='0' span='2' style='display:none; ' />
  <col class='xl65' width='0' style='display:none;' />
  <col class='xl65' width='0' style='display:none; ' />
  <col class='xl65' width='0' span='18' style='display:none; ' />
  <tr>
    <td colspan="4">
	  <table width="100%">
	  <tr>
	  <td>
<?php
include "empresa.php";
$img= new empresa();
$img -> imagem();
?><!--<img src='imagens/certificadosrecebidos.gif' width='120' height='86' align='left' />--></td>    <td align="center">
<?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa(); 
?> <br />
            <br>
            <span class='style7'><font size="4" face="Verdana, Arial, Helvetica, sans-serif"><b><?php echo "$row_pro[nome]";?></b></font></span><br />
            <br>
            TERMO DE DISTRATO DE BOLSA-AUX&Iacute;LIO</td>
	  <td align="right"><img src='imagens/cobrinha.gif' width='99' height='123'></td>
	  </tr>	  
	  </table>
</td>
  </tr>
  <tr>
    <td width='33%'><span class='style6'>UNIDADE CEDENTE (INSTITUI&Ccedil;&Atilde;O DE ENSINO)</span></td>
    <td colspan='2'><span class='style6'>UNIDADE CONCEDENTE</span></td>
    <td width='33%' colspan='2'><span class='style6'>BOLSISTA</span></td>
  </tr>
  <tr>
    <td></td>
    <td colspan='2'></td>
    <td colspan='2'></td>
  </tr>
    <tr> 
      <td><blockquote>
          <p><span class='style11'>Raz&atilde;o Social:&nbsp;</span><span class='style13'>SOE 
            &ndash; Sistema Objetivo de Ensino</span><span class='style13'><strong><br />
            CNPJ:&nbsp;</strong></span><span class='style13'>03.635.819/0001-13<br />
            </span><span class='style13'><strong>Endere&ccedil;o:</strong></span><span class='style13'> 
            Rua Olinda Elis, 278 &ndash; Campo Grande - RJ<br />
            </span><span class='xl74 style13 style5' style='height:12.0pt'><strong>Certifica&ccedil;&atilde;o:</strong></span><span class='style13'> 
            Portaria E/SADE/AUT n. &ordm; 120 de 29/11/02</span></p>
        </blockquote></td>
    <td colspan='2'><span class='style13'><strong>Raz&atilde;o Social:&nbsp;</strong></span><span class='style13'>Instituto Sorrindo Para a Vida</span><span class='style13'><strong><br />
    CNPJ:&nbsp;</strong></span><span class='style13'>06.888.897/0001-18<br />
    </span><span class='style13'><strong>Endere&ccedil;o:</strong></span><span class='style13'> S&atilde;o Luis, 112 - 18&deg; Andar &ndash; Cj. 1802&nbsp; - S&atilde;o Paulo</span><span class='style13'><strong><br />
    Certifica&ccedil;&atilde;o:</strong></span><span class='style13'> OSCIP N.&ordm; 08026.012349/2004-40&nbsp;&nbsp;&nbsp;</span></td>
    <td colspan='2'><span class='style13'><strong>Nome:</strong></span><span class='style13'><span class='style7'> 
      <?php echo "$row_bol[nome]";?></span><span class='style13'><strong><br />
      Endere&ccedil;o: </strong></span><span class='style13'><span class='style7'><?php echo "$row_bol[endereco]";?></span><span class='style13'><strong><br />
      Telefone:</strong></span><span class='style13'><span class='style7'> <?php echo "$row_bol[tel_fixo]";?></span><span class='style13'><strong><br />
      Nascimento: </strong></span><span class='style13'><span class='style7'><?php echo "$row_bol[data2]";?></span><span class='style13'><strong><br />
      CPF: </strong></span><span class='style13'><span class='style7'> <?php echo "$row_bol[cpf]";?></span><span class='style13'><strong><br />
      RG:</strong></span><span class='style13'><span class='style7'> <?php echo "$row_bol[rg]";?></span><span class='style1'> 
      </span><br />
      <br />
      </span></td>
  </tr>
  
  
  <tr>
    <td colspan='5'></td>
  </tr>
  <tr>
    <td colspan='5'><span class='style5'></span><span class='style5'></span></td>
  </tr>
    <tr> 
      <td colspan='5'><blockquote>
          <p align="justify" class="style13">Pelo presente instrumento, distrato 
            de BOLSA-AUX&Iacute;LIO, que fazem de um lado a UNIDADE CONCECENTE, 
            neste ato representado pelo respons&aacute;vel supra-referido, aqui 
            apresentada como CONTRATANTE, e de outro lado o BOLSISTA, que tem 
            ajustado o presente distrato nas cl&aacute;usulas e condi&ccedil;&otilde;es 
            seguintes:</p>
          <p align="justify" class="style13"><strong>Cl&aacute;usula 1&ordm;. 
            OBJETO</strong><br>
            <br>
            O presente Termo de Distrato tem por objeto o Contrato BOLSA-AUX&Iacute;LIO 
            assinado em <span class='style13'><span class='style7'><b><?php echo "$row_bol[nova_data2]";?></b></span></span>;<br>
            <br>
            <strong>Cl&aacute;usula 2&ordm;. QUITA&Ccedil;&Atilde;O</strong><br>
            <br>
            Fica quitada, toda e qualquer pend&ecirc;ncia e obriga&ccedil;&atilde;o 
            do Contrato BOLSA-AUX&Iacute;LIO.<br>
            <br>
            <strong>Cl&aacute;usula 3&ordm;. OUTRAS DISPOSI&Ccedil;&Otilde;ES</strong><br>
            <br>
            Ficam os Termos restantes do Contrato BOLSA-AUX&Iacute;LIO tamb&eacute;m 
            renunciados e quitados, podendo atrav&eacute;s de comunica&ccedil;&atilde;o 
            e aceite entre as partes ser retomado a qualquer tempo. <br>
            <br>
            E por estarem assim, juntas, advindas descontratadas firmam o presente 
            instrumento em 02 (duas) vias de igual teor, na presen&ccedil;a da 
            testemunha abaixo que tamb&eacute;m subscrevem<br>
          </p>
          <p class="style15"><?php print "$row_reg[regiao], _______ de ___________________ de ________. "; ?></p>
        </blockquote></td>
  </tr>
  <tr>
    <td colspan='5'><span class='style16'><br />
      <br />
    </span></td>
  </tr>
  <tr>
    <td><div align='center'><span class='style13'>__________________________________</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>__________________________________</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>____________________________________</span></div></td>
  </tr>
  <tr>
    <td height='27'><div align='center'><span class='style13'>CEDENTE</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>CONCEDENTE</span></div></td>
    <td colspan='2'><div align='center'><span class='style13'>BOLSISTA</span></div></td>
  </tr>
  <tr>
    <td colspan='5'></td>
  </tr>
  <tr>
    <td colspan='5'></td>
  </tr>
  
  
  <tr>
    <td colspan='5'></td>
  </tr>
</table>
<table width='757' bgcolor="#FFFFFF">
  <tr> 
    <td><p align='center' class='style13'><br />
        <br />
        ______________________________________________<br />
      TESTEMUNHA</p>
      <p align='center' class='style13'>NOME:_________________________________</p>
      <p align='center' class='style13'>RG:____________________________________</p>
    <p align='center' class='style13'>CPF:&nbsp;__________________________________</p></td>
    <td><p align='center' class='style13'><br />
        <br />
        ______________________________________________<br />
      TESTEMUNHA</p>
      <p align='center' class='style13'>NOME:_________________________________</p>
      <p align='center' class='style13'>RG:____________________________________</p>
    <p align='center' class='style13'>CPF:&nbsp;__________________________________</p></td>
  </tr>
</table>
</center>
</body>
</html>
<?php
}
?>