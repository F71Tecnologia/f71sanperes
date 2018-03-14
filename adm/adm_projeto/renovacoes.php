<?php 
include('../include/restricoes.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');
?>

<?php 
$id_projeto=$_GET['id'];
$regiao=$_GET['regiao'];

$qr_subprojeto=mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$id_projeto'") or die('erro');
$row_subprojeto=mysql_fetch_assoc($qr_subprojeto);

echo $row_subprojeto['tipo_contratacao'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Administra&ccedil;&atilde;o de Projetos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../../jquery-1.3.2.js"></script> 
<script type="text/javascript">

</head>


<body>
</body>
</html>