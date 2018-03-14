<?php
<head>
<link href="net1.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor='#D7E6D5'>
<?
if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{

include "conn.php";

//ATUALIZA AS DATAS REFERENTES AOS PROJETOS ENTREGUES
$gestores=$_REQUEST['gestores'];

$dataEntrega=$_REQUEST['dataentrada'];
$data=explode("/",$dataEntrega);
$d = $data[0];
$m = $data[1];
$a = $data[2];
$dataEntrega = $a.'-'.$m.'-'.$d; 

$dataRelatrorioSemestral=$_REQUEST['datarelatoriosemestral'];
$data2=explode("/",$dataRelatrorioSemestral);
$d2 = $data2[0];
$m2 = $data2[1];
$a2 = $data2[2];
$dataRelatrorioSemestral = $a2.'-'.$m2.'-'.$d2;

$dataRelatorioTrimestral=$_REQUEST['datarelatoriotrimestral'];
$data3=explode("/",$dataRelatorioTrimestral);
$d3 = $data3[0];
$m3 = $data3[1];
$a3 = $data3[2];
$dataRelatorioTrimestral = $a3.'-'.$m3.'-'.$d3;

$dataRelatorioDeCapacitacao=$_REQUEST['datarelatoriodecapacitacao'];
$data4=explode("/",$dataRelatorioDeCapacitacao);
$d4 = $data4[0];
$m4 = $data4[1];
$a4 = $data4[2];
$dataRelatorioDeCapacitacao = $a4.'-'.$m4.'-'.$d4;

$dataRelatorioDeDesempenho=$_REQUEST['datarelatoriodedesempenho'];
$data5=explode("/",$dataRelatorioDeDesempenho);
$d5 = $data5[0];
$m5 = $data5[1];
$a5 = $data5[2];
$dataRelatorioDeDesempenho = $a5.'-'.$m5.'-'.$d5;

$regiao = $_REQUEST['regiao'];
$id_projeto = $_REQUEST['id_projeto'];

mysql_query("UPDATE projeto SET data_entrega = '$dataEntrega', data_semestral = '$dataRelatrorioSemestral', data_trimestral = '$dataRelatorioTrimestral', data_capacita = '$dataRelatorioDeCapacitacao', data_desempenho = '$dataRelatorioDeDesempenho', gestores = '$gestores' WHERE id_regiao = '$regiao' AND id_projeto = '$id_projeto'")or die(mysql_error());
}

print "
<script>
alert ('Dados gravadas com sucesso.');
location.href=\"ver.php?regiao=$regiao\"
</script>
";

?>
</body>
</html>