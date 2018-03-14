<?php

//include('include/restricoes.php');
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
	
	case 5: 
	
			if($tipo_id == 20) {			
					$array_contas = $_REQUEST['despesas'];			
					foreach ($array_contas as $contas) {
						
						$qr_despesas = mysql_query("SELECT * FROM c_despesas_gerais WHERE c_desp_gerais_id = '$contas'");
						$row_despesa = mysql_fetch_assoc($qr_despesas);
						
						mysql_query("INSERT INTO c_contas (c_conta_nome,c_grupo_id, c_subgrupo_id, c_tipo_id, c_subtipo_id, c_conta_classificacao, c_conta_T, c_conta_grau, c_conta_user_cad, c_conta_data_cad, c_conta_status)
																   VALUES
																   ('$row_despesa[c_desp_gerais_nome]', '$grupo_id', '$subgrupo_id', '$tipo_id', '$subtipo_id',  '$classificacao', '$T', '$grau', '$_COOKIE[logado]', NOW(), '1')") or die(mysql_error());	
					
					}
							
			header("Location: listar_contas.php?tb=$grau&regiao=$regiao");			
			} else {
			
			
			mysql_query("INSERT INTO c_contas (c_conta_nome,c_grupo_id, c_subgrupo_id, c_tipo_id, c_subtipo_id, c_conta_classificacao, c_conta_T, c_conta_grau, c_conta_user_cad, c_conta_data_cad, c_conta_status)
													   VALUES
													   ('$nome', '$grupo_id', '$subgrupo_id', '$tipo_id', '$subtipo_id',  '$classificacao', '$T', '$grau', '$_COOKIE[logado]', NOW(), '1')") or die(mysql_error());	  
			header("Location: listar_contas.php?tb=$grau&regiao=$regiao");	
			}
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
		
		$('#form').validationEngine();
		
		$('#grau').change(function(){
			
			var valor = parseInt($(this).val());
			
			switch(valor){
			case '': 
					$('.grupo,.subgrupo, .tipo, .subtipo').hide();
				
			break;
			case 1: 
					$('.grupo,.subgrupo, .tipo, .subtipo').hide();
					
			break;
			
			case 2: $('.grupo').show()	;
					$('.subgrupo, .tipo, .subtipo').hide();
					
			break;
			
			case 3: $('.grupo').show();
					$('.subgrupo').show();				
					$('.tipo, .subtipo').hide();	
						
			break;
			
			case 4: $('.grupo').show()	;
					$('.subgrupo').show();
					$('.tipo').show();
					$('.subtipo').hide();
			break;
			
			case 5: 
					$('.grupo').show()	;
					$('.subgrupo').show();
					$('.tipo').show();
					$('.subtipo').show();
			break;
			
			
			}	
			
			});	
			
			$('#grupo').change(function(){
				var valor = $(this).val();
				$.ajax({
					url: 'action.selects.php?tp=grupo&val='+valor,
					type: 'GET',
					success: function(resposta) {
						 $('#subgrupo').html(resposta); 
						 $( '#tipo, #subtipo').html('');
					
					}		
					});
				});
			
			$('#subgrupo').change(function(){
				var valor = $(this).val();
				$.ajax({
					url: 'action.selects.php?tp=subgrupo&val='+valor,
					success: function(resposta){ 
								 $('#tipo').html(resposta); 
								  $('#subtipo').html('');
								 }		
					});
				});
			
			$('#tipo').change(function(){
				
				var grau  =  $('#grau').val();
				var valor = $(this).val();
				
				if(valor == 20 && grau == 5) {
					
				 Preenche_checkbox();
				
				}
				
				
				$.ajax({
					url: 'action.selects.php?tp=tipo&val='+valor,
					success: function(resposta){
								 $('#subtipo').html(resposta); 
							   
						  }		
			});
	

	
	
});



function Preenche_checkbox() {


	$.ajax({
			url: 'action.selects.php?tp=despesas',
			dataType: 'html',
			success: function(resposta){
				
						console.log(resposta)
						 $('.despesas').show();
						 $('#despesas').html(resposta); 
			}
	})	

	
}
	
	
	
function validar(){
	
var grau  = $('#grau').val();
var grupo = $('#grupo').val();
var subgrupo = $('#subgrupo').val();
var tipo = $('#tipo').val();
var grupo = $('#grupo').val();
var subtipo = $('#subtipo').val();
var codigo = $('#codigo').val();
var T = $('#T').val();
var classificacao = $('#codigo').val();
var nome = $('#nome').val();
	
	switch(grau){
		
		case 1: if(codigo == '') 			 { $('.alert_cod').html('Digite o código'); } 
				else if( T == '')			 { 	$('.alert_T').html('Digite o T'); }
				else if(classificacao == '') { $('.alert_classificacao').html('Digite a classificação'); }
				else if(	nome == '') 	 {	$('.alert_nome').html('Digite o nome'); }
				return false;
		break;
		}



}	
	
	
});




</script>

<style type="text/css">
.grupo, .subgrupo, .tipo, .subtipo, .despesas{ display:none;}



</style>


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
      
<form action="" method="post" name="form" id="form">
<table width="100%">





<tr>
	<td  style="text-align:right"  class="secao">GRAU:</td>
    <td>
    <select name="grau" id="grau" class="validate[required]"> 
    	<option value="">Selecione o grau....</option>
        <option value="1">1 - GRUPO</option>
        <option value="2">2 - SUBGRUPO</option>
        <option value="3">3 - TIPO</option>
        <option value="4">4 - SUBTIPO</option>
        <option value="5">5 - CONTA</option>
    </select>
    </td>
</tr>

<tr class="grupo">
	<td style="text-align:right"  class="secao">GRUPO:</td>
    <td>
    	<select name="grupo" id="grupo">
    	<?php
		echo '<option value="">Selecione ....</option>';
		$qr_grupo = mysql_query("SELECT * FROM c_grupos WHERE c_grupo_status = 1");
				
		while($row_grupo = mysql_fetch_assoc($qr_grupo)) {	echo '<option value="'.$row_grupo['c_grupo_id'].'">'.$row_grupo['c_grupo_nome'].'</option>'; }
		?>
        </select></td>
</tr>
<tr class="subgrupo">
	<td style="text-align:right" class="secao">SUBGRUPO:</td>
    <td>
        	<select name="subgrupo" id="subgrupo">
    	</select>
       
    </td>
</tr>

<tr class="tipo">
	<td a style="text-align:right""  class="secao">Tipo:</td>
    <td>
    	<select name="tipo" id="tipo">
    	</select>
       
    </td>
</tr>

<tr class="subtipo">
	<td  style="text-align:right" class="secao">Subtipo:</td>
    <td>
    	<select name="subtipo" id="subtipo">
    	</select>
       
    </td>
</tr>
<tr class="despesas">
	<td>TIPOS DE DESPESAS:</td>
    <td> 
    	<div id="despesas"> </div>
    </td>
</tr>



<tr>
	<td  style="text-align:right"  class="secao">T:</td>
    <td><input name="T" id="T" type="text" /></td>
</tr>


<!---

<tr>
	<td  style="text-align:right"  class="secao">Classificação:</td>
    <td><input name="classificacao" id="classificacao" type="text" class="validate[required]"/></td>
</tr>-->
<tr>
	<td  style="text-align:right"  class="secao">Nome:</td>
    <td><input name="nome" id="nome" type="text"/></td>
</tr>



<tr>
 	<td colspan="2"  style="text-align:center"  >
    <input type="hidden" name="regiao" value="<?php echo $regiao;?>"/>
    <input type="submit" name="enviar" value="Enviar"/>
</tr>

</table>
</form>
</div>


</body>
</html>
