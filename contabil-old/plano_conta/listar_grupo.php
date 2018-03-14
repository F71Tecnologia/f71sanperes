<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$regiao = mysql_real_escape_string($_GET['regiao']);
$qr_grupo = mysql_query("SELECT * FROM c_grupos WHERE c_grupo_status = 1");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>:: Intranet :: Editar de Plano de contas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>

<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 

</head>
<body>
<div id="corpo">

<div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;"> 
                EDITAR <span class="projeto"> GRUPO</span>
           </h2> 
           <p style="float:right;margin-top:40px;">
               <a href="../index.php?regiao=<?php echo $regiao;?>">&laquo; Voltar</a>
           </p>
           
           <p style="float:right;margin-left:15px;background-color:transparent;">
               <?php include('../../reportar_erro.php'); ?>   		
           </p>
           <div class="clear"></div>
           </div>



<table width="100%" >
<tr>	
	<td style="background-color:#BBB;">CÓDIGO</td>
    <td style="background-color:#BBB;">NOME</td>
    <td style="background-color:#BBB;">T</td>
    <td style="background-color:#BBB;">CLASSIFICAÇÃO</td>
    <td style="background-color:#BBB;">GRAU</td>
    <td style="background-color:#BBB;"></td>
    <td style="background-color:#BBB;"></td>
</tr>
<?php 
while($row_grupo = mysql_fetch_assoc($qr_grupo)):
?>
<tr>
    <td><?php echo $row_grupo['c_grupo_id'];?></td>
    <td><?php echo $row_grupo['c_grupo_nome'];?></td>
    <td><?php echo $row_grupo['c_grupo_T'];?></td>
    <td><?php echo $row_grupo['c_grupo_classificacao'];?></td>
    <td><?php echo $row_grupo['c_grupo_grau'];?></td>
    <td><a href="editar.php?tb=1&id=<?php echo $row_grupo['c_grupo_id'];?>">Editar</a></td>
    <td><a href="excluir.php?tb=1&id=<?php echo $row_grupo['c_grupo_id'];?>">Excluir</a></td>
<?php
endwhile;
?>
</table>
</div>
</body>
</html>
