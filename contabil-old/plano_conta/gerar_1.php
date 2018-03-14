<?php

include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

$regiao = $_GET['regiao'];

if(isset($_POST['enviar'])){
	
$regiao   = $_POST['regiao'];
$grau 	  = $_POST['grau'];
$grupo_id 	  = $_POST['grupo'];
$subgrupo_id = $_POST['subgrupo'];
$tipo_id	  = $_POST['tipo'];
$subtipo_id = $_POST['subtipo'];
$nome 	  = $_POST['nome'];
$classificacao = $_POST['classificacao'];
$T 		  = $_POST['T'];
$codigo   = $_POST['codigo'];

switch($grau){
	case 1: mysql_query("INSERT INTO c_grupos (c_grupo_nome, c_grupo_classificacao, c_grupo_T, c_grupo_grau, c_grupo_user_cad, c_grupo_data_cad, c_grupo_status)
											   VALUES
											   ('$nome', '$classificacao', '$T', '$grau', '$_COOKIE[logado]', NOW(), '1')") or die(mysql_error());	
	header("Location: listar_grupo.php?tb=$grau&regiao=$regiao");
		break;
		
	case 2: mysql_query("INSERT INTO c_subgrupos (c_subgrupo_nome,c_grupo_id, c_subgrupo_classificacao, c_subgrupo_T, c_subgrupo_grau, c_subgrupo_user_cad, c_subgrupo_data_cad, c_subgrupo_status)
											   VALUES
											   ('$nome', '$grupo_id',  '$classificacao', '$T', '$grau', '$_COOKIE[logado]', NOW(), '1')");
											   	
	header("Location: listar_subgrupo.php?tb=$grau&regiao=$regiao");
	break;
	
	case 3: mysql_query("INSERT INTO c_tipos (c_tipo_nome,c_grupo_id, c_subgrupo_id,  c_tipo_classificacao, c_tipo_T, c_tipo_grau, c_tipo_user_cad, c_tipo_data_cad, c_tipo_status)
											   VALUES
											   ('$nome', '$grupo_id', '$subgrupo_id', '$classificacao', '$T', '$grau', '$_COOKIE[logado]', NOW(), '1')");
		
		header("Location: listar_tipo.php?tb=$grau&regiao=$regiao"); 
		break;	
	
	case 4: mysql_query("INSERT INTO c_subtipos (c_subtipo_nome,c_grupo_id, c_subgrupo_id, c_tipo_id , c_subtipo_classificacao, c_subtipo_T, c_subtipo_grau, c_subtipo_user_cad, c_subtipo_data_cad, c_subtipo_status)
											   VALUES
											   ('$nome', '$grupo_id', '$subgrupo_id', '$tipo_id', '$classificacao', '$T', '$grau', '$_COOKIE[logado]', NOW(), '1')") or die(mysql_error());  			
		header("Location: listar_subtipo.php?tb=$grau&regiao=$regiao");
		break;	
	
	case 5: mysql_query("INSERT INTO c_contas (c_conta_nome,c_grupo_id, c_subgrupo_id, c_tipo_id, c_subtipo_id, c_conta_classificacao, c_conta_T, c_conta_grau, c_conta_user_cad, c_conta_data_cad, c_conta_status)
											   VALUES
											   ('$nome', '$grupo_id', '$subgrupo_id', '$tipo_id', '$subtipo_id',  '$classificacao', '$T', '$grau', '$_COOKIE[logado]', NOW(), '1')") or die(mysql_error());	  
	header("Location: listar_conta.php?tb=$grau&regiao=$regiao");	
		break;
		

}
	

}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>:: Intranet :: Cadastro de Projeto</title>
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
<script type="text/javascript">
$(function(){
	
$('#ano').change(function(){
	
	$('form').submit();
		
	})	
	
	
})


</script>

</head>

<body>
<div id="corpo">

 <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;">
               CADASTRO <span class="projeto">PLANO DE CONTAS</span>
           </h2> 
           <p style="float:right;margin-top:40px;">
               <a href="../index.php?regiao=<?php echo $regiao;?>">&laquo; Voltar</a>
           </p>
           
           <p style="float:right;margin-left:15px;background-color:transparent;">
               <?php include('../../reportar_erro.php'); ?>   		
           </p>
           <div class="clear"></div>
      </div>
      
<form action="gerar.php" method="post" name="form" id="form">
<table  align="center">
<tr>
	<td>Ano:</td>
    <td>
    	<select name="ano" id="ano">
        	<option value=""> Selecione o ano...</option>
            <?php
            for($i=2008;$i<date('Y');$i++) { echo  '<option value="'.$i.'">'.$i.'</option>'; }
			?>
        </select>
    </td>
</tr>


</table>
</form>
</div>


</body>
</html>
