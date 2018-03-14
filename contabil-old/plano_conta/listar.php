<?php
//include('include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$regiao = mysql_real_escape_string($_GET['regiao']);
$tipo_tabela = $_GET['tb'];


				  
				  
switch($tipo_tabela){
	
case 1: ///GRUPO
		$qr_plano = mysql_query("SELECT * FROM c_grupos WHERE c_grupo_status = 1");
		$tipo = 'GRUPO';
		$tabela = 'c_grupo';
		break;
case 2: ///SUBGRUPO
		$qr_plano = mysql_query("SELECT * FROM c_subgrupos WHERE c_subgrupo_status = 1");
		$tipo = 'SUBGRUPO';
		$tabela = 'c_subgrupo';
		break;

case 3: ///TIPO
		$qr_plano = mysql_query("SELECT * FROM c_tipos WHERE c_tipo_status = 1");
		$tipo = 'TIPO';
		$tabela = 'c_tipo';
		break;
		
case 4: ///SUBTIPO
		$qr_plano = mysql_query("SELECT * FROM c_subtipos WHERE c_subtipo_status = 1");
		$tipo = 'SUBTIPO';
		$tabela = 'c_subtipo';
		break;
		

}



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
                EDITAR <span class="projeto"> <?php echo $tipo;?></span>
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
while($row_plano = mysql_fetch_assoc($qr_plano)):
?>

<tr>
    <td><?php echo $row_plano[$tabela.'_id'];?></td>
    <td><?php echo $row_plano[$tabela.'_nome'];?></td>
    <td><?php echo $row_plano[$tabela.'_T'];?></td>
    <td><?php echo $row_plano[$tabela.'_classificacao'];?></td>
    <td><?php echo $row_plano[$tabela.'_grau'];?></td>
    <td><a href="editar.php?tb=<?php echo $tipo_tabela;?>&id=<?php echo $row_plano[$tabela.'_id'];?>">Editar</a></td>
    <td><a href="excluir.php?tb=<?php echo $tipo_tabela;?>&id=<?php echo $row_plano[$tabela.'_id'];?>">Excluir</a></td>
<?php
endwhile;
?>

</table>
</div>
</body>
</html>
