<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id_bol = $_REQUEST['bol'];
$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '22' and id_clt = '$id_bol'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('22','$id_bol','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bol' and tipo = '22'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida FROM autonomo where id_autonomo = '$id_bol'");
$row = mysql_fetch_array($result_bol);

$result_reg = mysql_query("Select * from regioes where id_regiao = '$row[id_regiao]'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_curso = mysql_query("Select *,date_format(inicio, '%d/%m/%Y')as inicio,date_format(termino, '%d/%m/%Y')as termino from curso where id_curso = '$row[id_curso]'", $conn);
$row_curso = mysql_fetch_array($result_curso);

$result_pro = mysql_query("Select * from  projeto where id_projeto = '$pro'");
$row_pro = mysql_fetch_array($result_pro);

$dia = date('d');
$mes = date('n');
$ano = date('Y');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

$MesInt = (int)$mes;
$NomeMES = $meses[$MesInt];

if($row['data_saida'] == '00/00/0000') { 
        $termino = "até a presente data"; 
} else { 
        $termino = "a <b>$row[data_saida]</b>"; 
}
?>
<html xmlns="undefined">
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<title>CERTIFICADO</title>
<style>
<!--
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
	{font-size:8.0pt;
	font-family:"Tahoma","sans-serif";}
-->
</style>
</head>
<body bgcolor="#FFFFFF" lang=PT-BR>
<table width="85%" height="80%" border="15" align="center" bordercolor="#CCCCCC">
  <tr>
    <td><table width="100%" border="0" align="center" cellspacing="20">
      <tr>
        <td width="33%"><div align="right"><img src="certificado_arquivos/image001.jpg" alt="soe" width="81" height="84"></div></td>
        <td width="33%"><div align="center"><span style="text-align:center"><b><span
style='font-size:18.0pt;font-family:"Arial Black","sans-serif"'> CERTIFICADO</span></b></span></div></td>
        <td width="34%"><img src="certificado_arquivos/image003.jpg" alt="cobra" width="76" height="102"></td>
      </tr>
      <tr>
        <td colspan="3" style="background:url(certificado_arquivos/image002.jpg) center center no-repeat;" ><p style='text-align:center'><span
style='font-size:14.0pt;font-family:"Comic Sans MS"'>Certificamos que,</span></p>
            <p align=center style='text-align:center'><b><span
style='font-size:14.0pt;font-family:"Comic Sans MS"'> <?php print "$row[nome]"; ?></span></b></p>
          <blockquote>
              <p style="text-align:justify"><span style='font-size:14.0pt;
font-family:&quot;Comic Sans MS&quot;'>Concluiu com aproveitamento o Curso de
                Qualifica&ccedil;&atilde;o Profissional <b><?php print "$row_curso[nome]"; ?></b>, promovido pelo Instituto
                Sorrindo Para a Vida em parceria com o Sistema Objetivo de Ensino, no per&iacute;odo
                de <b><?=$row['data_entrada']?></b> <?=$termino?>.</span></p>
          </blockquote>
          <p align=center style='text-align:center'><b><span
style='font-size:14.0pt;font-family:"Comic Sans MS"'><?php print "$row_reg[regiao], $dia de $NomeMES de $ano"; ?></span></b><span
style='font-size:14.0pt;font-family:"Comic Sans MS"'>.</span></p>
          <p align=center style='text-align:center'><span
style='font-family:"Comic Sans MS"'>______________________________</span></p>
          <p align=center style='text-align:center'><b><span
style='font-family:"Comic Sans MS"'>SOE-CNPJ: 036358170001-13</span></b></p>
          <p align=center style='text-align:center'>&nbsp;</p></td>
      </tr>
    </table></td>
  </tr>
</table>
<p>&nbsp;</p>
</body>
</html>

<?php
}
?>
