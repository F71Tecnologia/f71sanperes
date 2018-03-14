<?php
include ("include/restricoes.php");
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";



$id_user   = $_COOKIE['logado'];
$id_prep = $_GET['id']; 
$regiao  = mysql_real_escape_string($_GET['regiao']);


$q_prep = mysql_query("SELECT * FROM prepostos WHERE prep_id = '$id_prep' AND prep_status= 1");
$row_prep =  mysql_fetch_assoc($q_prep);
extract($row_prep);

if(isset($_POST['atualizar'])){
	
$nome = mysql_real_escape_string($_POST['nome']);	

$rg = mysql_real_escape_string($_POST['rg']);
$cpf = mysql_real_escape_string($_POST['cpf']);		
$email = mysql_real_escape_string($_POST['email']);	
$endereco = mysql_real_escape_string($_POST['endereco']);
$tel = mysql_real_escape_string($_POST['telefone']);		
$cel = mysql_real_escape_string($_POST['cel']);	


	
$update = mysql_query("UPDATE  prepostos SET prep_nome = '$nome', 													 
													   prep_rg 		 = '$rg',
													   prep_cpf		 ='$cpf',
													   prep_email 	 = '$email',
													   prep_endereco	 = '$endereco',
													   prep_tel ='$tel',
													  prep_cel = '$cel',													
												      prep_cad = '$_COOKIE[logado]',
													  prep_data_cad = NOW(),
													  prep_status = 1
													  
													  WHERE prep_id = '$id_prep'
													  
													  ") or die(mysql_error());
						
						
if($update) {
	
header("Location: index.php?regiao=$regiao");

}




}






?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>

<script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>

<script type="text/javascript">
$(function() {


$('#cpf').mask('999.999.999-99');
$('#telefone').mask('(99)9999-9999');
$('#cel').mask('(99)9999-9999');

	$('#form1').validationEngine();
	$('input[name=tipo]').change(function(){
			
		var tipo = $(this).val();
		
		if(tipo == 1) {
			
			$('#oab').fadeIn();
		
		} else {
			$('#oab').fadeOut();
		}
	
	
	
	});
	
});

</script>


<title>Untitled Document</title>
</head>
<body>
<div id="corpo">

<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;">
               EDITAR <span class="projeto">PREPOSTO
                </span>
           </h2>
           
            
           <p style="float:right;margin-top:40px;">
               <a href="index.php?regiao=<?=$regiao?>">&laquo; Voltar</a>
           </p>
           
           <p style="float:right;margin-left:15px;background-color:transparent;">
               <?php include('../reportar_erro.php'); ?>   		
           </p>
           <div class="clear"></div>
      </div>

      <?php if(!empty($erros)) {
		  		$erros = implode('<br>', $erros);
				echo '<p style="background-color:#C30; padding:4px; color:#FFF;">'.$erros.'</p><p>&nbsp;</p>';
			} ?>
      
	<form action="<?php echo $_SERVER['PHP_SELF']?>?id=<?=$id_prep?>&regiao=<?=$regiao?>" method="post" name="form1" 
    id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">

    <table cellpadding="0" cellspacing="1" class="secao">
          <tr>
            <td colspan="4" class="secao_pai" style="border-top:1px solid #777;">DADOS</td>
          </tr>
        
          
          
          
          <tr>
             <td class="secao" width="20%">Nome:</td>
             <td colspan="3"><input name="nome" size="50" type="text" id="nome" class="validate[required]"  value="<?php echo $prep_nome?>"/></td>
          </tr>
          
        
           
           <tr>
            <td class="secao">RG:</td>
             <td ><input name="rg" size="15" type="text" id="rg"  class="validate[required]" value="<?php echo $prep_rg?>"/></td>
          
            <td class="secao">CPF:</td>
             <td ><input name="cpf" size="10" type="text" id="cpf" class="validate[required]" value="<?php echo $prep_cpf?>"/></td>
          </tr>
          
           <tr>
            <td class="secao">E-mail:</td>
             <td colspan="3"><input name="email" size="30" type="text" id="email"   value="<?php echo $prep_email?>"/></td>
          </tr>
          
           <tr>
            <td class="secao">Endere&ccedil;o:</td>
             <td colspan="3"><input name="endereco" size="50" type="text" id="endereco" class="validate[required]" value="<?php echo $prep_endereco?>"/></td>
          </tr>
          
          <tr>
            <td class="secao">Telefone:</td>
             <td colspan="3" ><input name="telefone" size="10" type="text" id="telefone" class="validate[required]" value="<?php echo $prep_tel?>"/></td>
          </tr>
          <tr>
            <td class="secao">CEL.:</td>
             <td  colspan="3"><input name="cel" size="10" type="text" id="cel" value="<?php echo $prep_cel?>"/></td>
          </tr>
          
        
          
          
          <tr>
          	<td  colspan="4" align="center" style="text-align:center;"><input name="atualizar" type="submit" value="ATUALIZAR"/></td>
          </tr>
          
    </table>
    </form>
    </td>
    </tr>

</table>
</div>
</body>
</html>
