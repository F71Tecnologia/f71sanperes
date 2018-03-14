<?php 
require('../../../conn.php');

$POST = $_POST;

$ID = $_GET['ID'];
$ID_curso = $_POST['id_curso'];
$user = $_COOKIE['logado'];


$POST['data_nasci'] = implode("-",array_reverse(explode("/",$POST['data_nasci'])));
$POST['data_entrada'] = implode("-",array_reverse(explode("/",$POST['data_entrada'])));
$POST['salario'] = str_replace(",",".",str_replace(".","",$POST['salario']));

$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$ID'");
$row_clt = mysql_fetch_assoc($qr_clt);

$qr_curso = mysql_query("SELECT salario,cbo_codigo,id_curso FROM curso WHERE id_curso = '$row_clt[id_curso]';");
$row_curso = mysql_fetch_assoc($qr_curso);

$campos_curso[] = 'id_curso';
$campos_curso[] = 'funcao';
$campos_curso[] = 'cbo_codigo';
$campos_curso[] = 'salario';

$campos_clt[] = 'sexo';
$campos_clt[] = 'etnia';
$campos_clt[] = 'data_nasci';
$campos_clt[] = 'escolaridade';
$campos_clt[] = 'data_nasci';
$campos_clt[] = 'status_admi';
$campos_clt[] = 'campo1';
$campos_clt[] = 'serie_ctps';
$campos_clt[] = 'uf_ctps';
$campos_clt[] = 'pis';
$campos_clt[] = 'cpf';
$campos_clt[] = 'cep';
$campos_clt[] = 'data_entrada';
$campos_clt[] = 'data_demi';

$update_clt = array();
$update_curso = array();
foreach($campos_clt as $campo){
	if($row_clt[$campo] <> $POST[$campo]){
		$update_clt[$campo] = $POST[$campo];
	}
}

foreach($campos_curso as $campo){
	if($row_curso[$campo] <> $POST[$campo]){
		$update_curso[$campo] = $POST[$campo];
	}
}


// update rh_clt
$sql = "UPDATE rh_clt SET ";
$sql_partes = array();
foreach($update_clt as $campo => $valor){
	$sql_partes[] = $campo." = '".$valor."'";
}
if(!empty($sql_partes )){
	$sql_partes[] = " useralter = '".$user."'";
	$sql .= implode(',',$sql_partes);
	$sql .= " WHERE id_clt = '$ID' LIMIT 1";
	if(!empty($ID)) {
		$qr_clt = mysql_query($sql);
	}else{
		echo "0";
		exit;
	}
}
$SQL = $sql;
unset($sql_partes,$sql,$qr);

//update curso
$sql = "UPDATE curso SET ";
foreach($update_curso as $campo => $valor){
	$sql_partes[] = $campo." = '".$valor."'";
}
if(!empty($sql_partes)){
	$sql_partes[] = " user_alter = '".$user."'";
	$sql .= implode(',',$sql_partes);
	$sql .= " WHERE id_curso = '$ID_curso' LIMIT 1";
	if(!empty($ID_curso)){
		$qr_curso = mysql_query($sql);
	}else{
		echo "0";
		exit;
	}
}

if($qr_clt or $qr_curso) echo "1";
?>