<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
}else{

include "conn.php";

$id_bolsista = $_REQUEST['bol'];
$id_projeto = $_REQUEST['pro'];
$id_regiao = $_REQUEST['regiao'];
$tabela = $_REQUEST['tab'];

//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
$data_cad = date('Y-m-d');
$user_cad = $_COOKIE['logado'];

$result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '18' and id_clt = '$id_bolsista'");
$num_row_verifica = mysql_num_rows($result_verifica);
if($num_row_verifica == "0"){
	//mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('18','$id_bolsista','$data_cad', '$user_cad')");
}else{
	//mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_bolsista' and tipo = '18'");
}
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

$result_bol = mysql_query("SELECT *, date_format(data_nasci, '%d/%m/%Y')as data2, date_format(data_entrada, '%d/%m/%Y')as data_entrada2 FROM autonomo where id_autonomo = '$id_bolsista'", $conn);
$row_bol = mysql_fetch_array($result_bol);

$result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$id_projeto'", $conn);
$row_pro = mysql_fetch_array($result_pro);

$result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_curso = mysql_query("Select * from curso where id_curso = $row_bol[id_curso]", $conn);
$row_curso = mysql_fetch_array($result_curso);

$qr_Master = mysql_query("SELECT * FROM master WHERER id_regiao = '$id_regiao'");
$row_master = mysql_fetch_assoc($qr_master);



$data_hj = date('d/m/Y');

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

}


?>
<html>
<head>
<title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href='net1.css' rel='stylesheet' type='text/css'>
</head>

<body>
<table width="100%">
	<tr>
    	<td>
        
        <h3>CONTRATO DE PRESTAÇÃO DE SERVIÇOS</h3>
        
        <p>Pelo presente instrumento particular de prestação de serviços, de um lado, <?php echo $row_master['razao']?>, associação civil sem fins lucrativos, com sede <?php echo $row_master['endereco']?>, inscrita no CNPJ/MF sob o nº <?php echo $row_master['CNPJ']?>, neste ato por seu representante legal,<?php echo $row_master['telefone']?>, <?php echo $row_master['nacionalidade']?>, <?php echo $row_master['civil']?>, <?php echo $row_master['formacao']?>, portador da Cédula de Identidade n.º <?php echo $row_master['rg']?>, inscrito no CPF/MF sob o n.º <?php echo $row_master['cpf']?>, doravante denominada CONTRATANTE; e, de outro lado, <EMPRESA CONTRATANTE>, sociedade simples, com sede na <ENDEREÇO>, inscrita no  CNPJ sob n.º < CNPJ>, neste ato representada pelo(a) <RESPONSÁVE>, <NACIONALIDADE>, <ESTADO CIVIL>, <FUNCAO>, portador da cédula de identidade RG n.º<RG>, inscrito no CPF/MF sob n.º <CPF>, doravante denominada CONTRATADA, tem justo e contratado o seguinte:</p>
        </td>
    <tr>

</table>

</body>
</html>
