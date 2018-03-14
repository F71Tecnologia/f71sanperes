<?php
include('../adm/include/restricoes.php');
include('../conn.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');
include('../adm/include/criptografia.php');


$id_compra = mysql_real_escape_string($_GET['compra']);
$regiao    =   mysql_real_escape_string($_GET['regiao']);
$array_fornecedor = array(1 =>'fornecedor1', 2 => 'fornecedor2', 3 => 'fornecedor3');

$qr_compra = mysql_query("SELECT * FROM compra WHERE id_compra = '$id_compra'");
$row_compra = mysql_fetch_assoc($qr_compra);

$nome_forne[1] = @mysql_result(mysql_query("SELECT razao FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor1]'"),0);
$nome_forne[2]= @mysql_result(mysql_query("SELECT razao FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor2]'"),0);
$nome_forne[3] = @mysql_result(mysql_query("SELECT razao FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor3]'"),0);

?>
<html>
<head>
<title>:: Intranet :: Cadastro de Projeto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="favicon.ico" rel="shortcut icon">
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../jquery/priceFormat.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>

</head>
<style>
h3 {
text-align:center;
background-color:  #E4E4E4;
}
.img{
text-align:center;
width:100%;
}
</style>
<body>
 
<div id="corpo">
	
    <?php
	foreach($array_fornecedor as $chave => $fornecedor) {
		
	echo '<h3>'.$nome_forne[$chave].'</h3>';	
		
		
	$qr_anexo = mysql_query("SELECT * FROM anexo_compra WHERE id_compra = '$id_compra' AND fornecedor = '$chave' AND anexo_status = 1");
		
	if(mysql_num_rows($qr_anexo) != 0) {
		while($row_anexo = mysql_fetch_assoc($qr_anexo)):
		
			echo '<div class="img"><img src="anexo_compras/'.$row_anexo['anexo_id'].'.'.$row_anexo['anexo_extensao'].'" width="50%" height="50%"/></div> <BR>';
		
		endwhile;		
		
	}
	
	}
	?>  
 <center> <div id="rodape"><?php include('../adm/include/rodape.php'); ?></div>
   </center>
</div>
</body>
</html>