<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}

include "../conn.php";
include "../empresa.php";

$clt = $_REQUEST['clt'];
$pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['id_reg'];

$result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada, date_format(data_saida, '%d/%m/%Y')as data_saida FROM rh_clt where id_clt = '$clt'");
$row = mysql_fetch_array($result_bol);

$result_curso = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = $row[id_curso]");
$row_curso = mysql_fetch_array($result_curso);




$row_regiao_id = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_reg'"),0);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao_id'");
$row_master = mysql_fetch_assoc($qr_master);


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

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '7' and id_clt = '$clt'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('7','$clt','$data_cad', '$user_cad')");
}else{
	mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '7'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

?>
<html xmlns="undefined">
<head>
<meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
<title>CARTA DE REFER&Ecirc;NCIA</title>
<style>
<!--
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
	{font-size:8.0pt;
	font-family:"Tahoma","sans-serif";}
body {
	margin-left: 5px;
	margin-top: 0px;
	margin-right: 5px;
	margin-bottom: 0px;
}
.style9 {font-family: Arial, Helvetica, sans-serif}
.style12 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	font-size: 10px;
}
-->
</style>
    <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="../resources/css/style-print.css" rel="stylesheet">
    <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../resources/js/print.js" type="text/javascript"></script>
</head>
<body lang=PT-BR>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="text-center">
            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
            <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
        </div>
    </div>
</nav>
<div class="pagina">


<table width="700" align="center" bgcolor="#FFFFFF" class="bordaescura1px ">
  <tr>
    <td><table width="650" align="center">
      <tr>
        <td align="center" valign="middle"><div align="center"><span class="style9"><img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif"/><br /></span></div></td>
        </tr>
      <tr>
        <td><p align="center" class="style9"><br>
          </p>
          <p align="center"><strong><br>
            <br>
            </strong></p>
          <p align="center"><strong><br>
            <span class="style9">CARTA DE REFER&Ecirc;NCIA</span></strong></p>
          <p align="justify" class="style9"><br>
            <br>
            </p>
          <p align="justify" class="style9">&nbsp;</p>
          <p align="justify" class="style9"><br>
            Declaramos para os devidos fins, que o (a) Sr (a) <b><?php print "$row[nome]"; ?></b>,  
            portador(a) da CTPS: <b><?php print $row['campo1']; ?></b>, S&Eacute;RIE: <b><?php print $row['serie_ctps']; ?></b> e EMISS&Atilde;O: <b><?php print implode('/', array_reverse(explode('-', $row['data_ctps']))); ?> (<?php echo $row['uf_ctps']; ?>)</b>, foi nosso funcion&aacute;rio de <?php print "$row[data_entrada]"; ?> &agrave; <?php print "$row[data_saida]"; ?>, exercendo a  fun&ccedil;&atilde;o  
            de <b><?php print "$row_curso[nome]"; ?></b>, sendo que nada consta em  
            nossos arquivos que 
            desabone sua conduta profissional.</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">Atenciosamente
            ,<br>
            <br>
            <br>
            </p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9">&nbsp;</p>
          <p align="center" class="style9"><br>
          </p>
          <p align="center"><span class="style9"> _____________________________________________________<br>
              <strong> 
<?php 
$nomEmp= new empresa();
$nomEmp -> nomeEmpresa2(); 
?></strong></span><br>
              <br>
              <br>
              <br>
            </p>
          <p align="center">&nbsp;</p>
          <p align="center">&nbsp;</p>
          <p align="center">&nbsp;</p>
          <p align="center"><br>
            <br>
          </p></td>
        </tr>
      <tr>
        <td>
<?php
$end = new empresa();
$end -> endereco01('black');
?>
          </p></td>
        </tr>
    </table>      </td>
  </tr>
</table>
</div>
</body>
</html>
