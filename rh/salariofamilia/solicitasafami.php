<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
//PEGANDO O ID DO CADASTRO
include "../../conn.php";
$id = 1;
$id_clt = $_REQUEST['clt'];
$id_user = $_COOKIE['logado'];
$id_projeto = $_REQUEST['pro'];
$id_regiao = $_REQUEST['id_reg'];


$result = mysql_query(" SELECT * FROM rh_clt where id_clt = $id_clt ", $conn);
$row = mysql_fetch_array($result);

if(!empty($row['id_antigo'])) {
	$referencia_familia = $row['id_antigo']; 
} else {
	$referencia_familia = $row['id_clt'];
}

$data_menor14 = date('Y-m-d', mktime(0,0,0, $mes, $dia, $ano - 14));
	
$menor1 = mysql_query("SELECT *
					   FROM dependentes
					   WHERE id_bolsista = '$referencia_familia'
					   AND data1 > '$data_menor14' AND data1 != '0000-00-00'
					   AND id_projeto = '$id_projeto'");
$row_menor1 = mysql_fetch_array($menor1);

$menor2 = mysql_query("SELECT *
					   FROM dependentes 
					   WHERE id_bolsista = '$referencia_familia' 
					   AND data2 > '$data_menor14' AND data2 != '0000-00-00' 
					   AND id_projeto = '$id_projeto'");
$row_menor2 = mysql_fetch_array($menor2);
	  
$menor3 = mysql_query("SELECT *
					   FROM dependentes 
					   WHERE id_bolsista = '$referencia_familia' 
					   AND data3 > '$data_menor14' AND data3 != '0000-00-00' 
					   AND id_projeto = '$id_projeto'");
$row_menor3 = mysql_fetch_array($menor3);

$menor4 = mysql_query("SELECT *
					   FROM dependentes
					   WHERE id_bolsista = '$referencia_familia' 
					   AND data4 > '$data_menor14' AND data4 != '0000-00-00' 
					   AND id_projeto = '$id_projeto'");
$row_menor4 = mysql_fetch_array($menor4);

$menor5 = mysql_query("SELECT *
					   FROM dependentes 
					   WHERE id_bolsista = '$referencia_familia' 
					   AND data5 > '$data_menor14' AND data5 != '0000-00-00' 
					   AND id_projeto = '$id_projeto'");
$row_menor5 = mysql_fetch_array($menor5);

$vinculo_tb_rh_clt_e_rhempresa = $row['rh_vinculo'];

$result_empresa= mysql_query("SELECT * FROM rhempresa WHERE id_empresa = $vinculo_tb_rh_clt_e_rhempresa");
$row_empresa = mysql_fetch_array($result_empresa);

$result_dependente = mysql_query("SELECT * , CURDATE(),
(YEAR(CURDATE())-YEAR(data1)) - (RIGHT(CURDATE(),5)<RIGHT(data1,5)) AS idade1,
(YEAR(CURDATE())-YEAR(data2)) - (RIGHT(CURDATE(),5)<RIGHT(data2,5)) AS idade2,
(YEAR(CURDATE())-YEAR(data3)) - (RIGHT(CURDATE(),5)<RIGHT(data3,5)) AS idade3,
(YEAR(CURDATE())-YEAR(data4)) - (RIGHT(CURDATE(),5)<RIGHT(data4,5)) AS idade4,
(YEAR(CURDATE())-YEAR(data5)) - (RIGHT(CURDATE(),5)<RIGHT(data5,5)) AS idade5
FROM dependentes WHERE id_bolsista = '$referencia_familia' AND id_projeto = '$id_projeto'");
$row_dependente = mysql_fetch_array($result_dependente);

$result_regiao = mysql_query(" SELECT * FROM regioes WHERE id_regiao = '$id_regiao' ", $conn);
$row_regiao = mysql_fetch_array($result_regiao);
$regiao = $row_regiao['regiao'];


$result_dependente2 = mysql_query("SELECT *, 
date_format(data1, '%d/%m/%Y')AS data_nasc1 ,
date_format(data2, '%d/%m/%Y')AS data_nasc2 ,
date_format(data3, '%d/%m/%Y')AS data_nasc3 ,
date_format(data4, '%d/%m/%Y')AS data_nasc4 ,
date_format(data5, '%d/%m/%Y')AS data_nasc5 
FROM dependentes WHERE id_bolsista = '$referencia_familia' AND id_projeto = '$id_projeto'");
$row_dependente2 = mysql_fetch_array($result_dependente2);

//Atualizando a tabela rh_doc_status para mostrar a data em que foi gerado a última termo de responsabilidade para salário família
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '16' and id_clt = '$id_clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('16','$id_clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_clt' and tipo = '16'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$dia = date('d');
$mes = date('m');
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
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>SOLICITA&Ccedil;&Atilde;O DE SAL&Aacute;RIO FAM&Iacute;LIA</title>

<link href="../../net1.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="700" border="0" align="center" cellpadding="5" cellspacing="0" class="bordaescura1px">
  <tr>
    <td colspan="2" align="center" bgcolor="#FFFFFF" class="campotexto">
<?php
include "../../empresa.php";
$img= new empresa();
$img -> imagemCNPJ();
?></td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#D6D6D6"><span class="campotexto"><strong><br />
    </strong></span><span class="title"><strong>Termo de  Responsabilidade para Sal&aacute;rio Fam&iacute;lia</strong></span><span class="campotexto"><strong><br />
    <br />
    </strong></span></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">      <blockquote>
        <p><span class="linha"><strong><br />
          Empresa:</strong> </span><span class="style2"><?=$row_empresa['nome']?></span></p>
        <p><span class="linha"><strong>Funcion&aacute;rio:</strong>   </span><span class="style2"><?=$row['id_clt']?> - <?=$row['nome']?></span></p>
        <p><span class="linha"><strong>Identidade:</strong> </span><span class="style2"><?=$row['rg']?></span><span class="linha">  </span><br />
          <br />
        </p>
    </blockquote>    </td>
  </tr>
  <tr>
    <td colspan="2" align="center" valign="middle" bgcolor="#FFFFFF" class="linha">  1ª Via : Empregadora  -   2ª Via : Empregado   </td>
  </tr>
  <tr>
    <td colspan="2" align="center" bgcolor="#D6D6D6" class="linha">  <strong>BENEFICI&Aacute;RIOS</strong></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF"><table width="100%" border="0" cellpadding="3" cellspacing="0">
      <tr>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>Nome do Filho</strong></span></td>
        <td align="center" bgcolor="#CCCCCC"><span class="linha"><strong>Data Nascimento</strong></span></td>
        </tr>
      <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
		<?php if(mysql_num_rows($menor1) != 0) {
                  echo '<tr>';
                  echo '<td align="center">'.$row_menor1['nome1'].'</td>';
                  echo '<td align="center">'.implode('/', array_reverse(explode('-',$row_menor1['data1']))).'</td>';
                  echo '</tr>';
              }
			  if(mysql_num_rows($menor2) != 0) {
                  echo '<tr>';
                  echo '<td align="center">'.$row_menor2['nome2'].'</td>';
                  echo '<td align="center">'.implode('/', array_reverse(explode('-',$row_menor2['data2']))).'</td>';
                  echo '</tr>';
              }
			  if(mysql_num_rows($menor3) != 0) {
                  echo '<tr>';
                  echo '<td align="center">'.$row_menor3['nome3'].'</td>';
                  echo '<td align="center">'.implode('/', array_reverse(explode('-',$row_menor3['data3']))).'</td>';
                  echo '</tr>';
              }
			  if(mysql_num_rows($menor4) != 0) {
                  echo '<tr>';
                  echo '<td align="center">'.$row_menor4['nome4'].'</td>';
                  echo '<td align="center">'.implode('/', array_reverse(explode('-',$row_menor4['data4']))).'</td>';
                  echo '</tr>';
              }
			  if(mysql_num_rows($menor5) != 0) {
                  echo '<tr>';
                  echo '<td align="center">'.$row_menor5['nome5'].'</td>';
                  echo '<td align="center">'.implode('/', array_reverse(explode('-',$row_menor5['data5']))).'</td>';
                  echo '</tr>';
              } ?>
    </table>
    </td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF"><blockquote>
      <p class="linha">       Pelo presente TERMO DE RESPONSABILIDADE declaro estar ciente de que  deverei comunicar de imediato a 
        ocorrência dos  seguintes fatos ou ocorrências que determinam a perda do direito ao  salário-família: <br />
        <br />
        - &Oacute;BITO DO FILHO; <br />
        - CESSA&Ccedil;&Atilde;O DA INVALIDEZ DE FILHO  INV&Aacute;LIDO; <br />
        - SENTEN&Ccedil;A JUDICIAL QUE DETERMINE O  PAGAMENTO A OUTREM ( casos de desquite ou separa&ccedil;&atilde;o,
        abandono de filho ou perda do p&aacute;trio  poder. <br />
        <br />
        Estou ciente , ainda , de que a falta de  cumprimento do compromisso ora assumido , al&eacute;m de obrigar a 
        devolu&ccedil;&atilde;o das  import&acirc;ncias recebidas indevidamente , sujeitar-me &agrave;s penalidades previstas no  art. 171 do C&oacute;digo 
        Penal&nbsp; e a rescis&atilde;o do contrato de trabalho , por  justa causa , nos termos do art. 482 da Consolida&ccedil;&atilde;o das Leis de 
        Trabalho. </p>
    </blockquote></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
  <tr>
    <td width="533" height="85" align="center" bgcolor="#FFFFFF"><span class="style2"><br />
    <?php 

	print "$regiao, $dia de $mes de $ano";	
	
	?></span><br />
      <br />
      <br />
<br />
    <span class="linha">_____________________________________________________<br />
    ASSINATURA<br />
    <br />
    </span></td>
    <td width="147" align="center" valign="top" bgcolor="#FFFFFF" class="linha"><table width="100%" height="116" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center" valign="top" class="igreja">IMPRESS&Atilde;O DIGITAL</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">&nbsp;</td>
  </tr>
</table>
</body>
</html>