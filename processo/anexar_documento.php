<?php
include('include/restricoes.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');

if(isset($_POST['enviar'])){
	
$tipo_documento = $_POST['tipo_documento'];
$data_vencimento = implode('-',array_reverse(explode('/',$_POST['data_vencimento'])));
$id_prestador   = mysql_real_escape_string($_POST['id_prestador']);
$arquivo 		= $_FILES['arquivo'];
$link_master	= $_POST['link_master'];
$regiao			= $_POST['regiao'];

$qr_tipo = mysql_query("SELECT * FROM prestador_tipo_doc WHERE prestador_tipo_doc_id = '$tipo_documento'");
$row_tipo = mysql_fetch_assoc($qr_tipo);

$nome_foto = $id_prestador.'_'.$row_tipo['prestador_tipo_doc_nome'].'_'.date('dmYhm');

switch($arquivo['type']){
		case 'image/jpeg': $extensao = '.jpg';
			break;		
		case 'image/jpg': $extensao = '.jpg';
			break;		
		case 'image/gif': $extensao = '.gif';
			break;		
		case 'image/png': $extensao = '.png';
			break;
		case 'application/msword': $extensao = '.doc';
			break;
		case 'application/pdf': $extensao = '.pdf';
			break;
	
}


$inserir = mysql_query("INSERT INTO prestador_documentos (id_prestador, prestador_tipo_doc_id, nome_arquivo, data_vencimento, extensao_arquivo, status) 
											VALUES
											('$id_prestador', '$tipo_documento','$nome_foto','$data_vencimento',  '$extensao', '1' )");
											
if($inserir){


move_uploaded_file($arquivo['tmp_name'], 'prestador_documentos/'.$nome_foto.$extensao);
header("Location: prestadorservico.php?regiao=$regiao&id=1&m=$link_master");

	
}

}




$id_prestador  = mysql_real_escape_string($_GET['id_prestador']);
$tipo_documento = mysql_real_escape_string($_GET['tipo']);
$regiao_id = mysql_real_escape_string($_GET['regiao']);
$link_master 	= $_GET['master'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
$('#data_vencimento').mask('99/99/9999');

});

</script>
</head>

<body>
<form name="form" method="post" enctype="multipart/form-data" action="anexar_documento.php">
<table>
	<tr>
    	<td>Data de Vencimento:</td>
        <td><input name="data_vencimento" id="data_vencimento" type="text"/></td>
    <tr>
    	<td>Anexar: </td>
        <td><input name="arquivo"  type="file" /></td>
    </tr>
    <tr>
    <td colspan="2" align="center"> 
     <input type="hidden" name="regiao" value="<?php echo $regiao_id; ?>" />
      <input type="hidden" name="link_master" value="<?php echo $link_master; ?>" />
    <input type="hidden" name="tipo_documento" value="<?php echo $tipo_documento; ?>" />
     <input type="hidden" name="id_prestador" value="<?php echo $id_prestador ?>" />
    <input type="submit" name="enviar" value="Enviar"/>
    </td>
</table>
</form>

</body>
</html>
