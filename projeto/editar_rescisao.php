<?php 
include('include/restricoes.php');
include('../conn.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');
include('../adm/include/criptografia.php');


$id_subprojeto = $_GET['id'];
$id_regiao  = $_GET['regiao'];
	

$qr_subprojeto = mysql_query("SELECT *FROM subprojeto WHERE id_subprojeto = '$id_subprojeto' ");
$row_subprojeto = mysql_fetch_assoc($qr_subprojeto);


if(isset($_POST['enviar'])) {
	
				

$id_subprojeto    =	$_POST['id_subprojeto'];
$termino    	  = implode('-', array_reverse(explode('/', $_POST['data_rescisao'])));
$id_regiao 		  = $_POST['id_regiao'];
			
$query = mysql_query("UPDATE  subprojeto SET   termino 		      = '$termino',
											   data_assinatura    = '$termino',
											   tipo_termo_aditivo = '3'
											   WHERE  id_subprojeto = '$id_subprojeto' LIMIT 1")
											   or die(mysql_error());
										   				
header("Location:desativar_projeto2.php?m=$link_master&id=$id_subprojeto&regiao=$id_regiao");
									
								
}
									
?>



<html>
<head>
<title>:: Intranet :: Edi&ccedil;&atilde;o de Projeto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>

<script type="text/javascript">
$(function(){
  $('#data_rescisao').mask('99/99/9999');
});

</script>
</head>
<body>

<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;">DESATIVAR<span class="projeto"> PROJETO</span>
           </h2>
    
           </p>
           <div class="clear"></div>
      </div>

    
      
	<form action="" method="post" name="form1" enctype="multipart/form-data" onSubmit="return validaForm()">

    
     <table cellpadding="0" align="center" cellspacing="1" class="secao">
       

      <tr>
      	<td align="right">Data da rescisão:</td>
        <td><input type="text" name="data_rescisao" id="data_rescisao" value="<?php echo implode('/', array_reverse(explode('-',$row_subprojeto['termino'])))?>"/></td>
      </tr>
      
     
      
      
      <tr>
        <td  align="center"  colspan="2">
            <input type="hidden" name="tipo_subprojeto" value="RESCISÃO" >
        	<input type="hidden" name="id_subprojeto" value="<?php echo $id_subprojeto; ?>"/>
     		<input type="hidden" name="id_regiao" 	   value="<?php echo $id_regiao; ?>"/>
            
        	<input name="enviar" type="submit" id="enviar" value="OK"/>
        </td>
      </tr>
    </table>
	</form></td>
  </tr>
</table>

</div>
</body>
</html>