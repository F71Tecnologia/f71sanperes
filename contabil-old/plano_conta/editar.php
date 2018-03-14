<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";



/////PEGANDO OS DADOS PARA  EXIBIRA NA PÁGINA/////////////////////////////
$regiao = mysql_real_escape_string($_GET['regiao']);
$tipo_tabela = $_GET['tb'];
$id = $_GET['id'];

switch($tipo_tabela){
		
	case 1: ///GRUPO
			$qr_plano = mysql_query("SELECT * FROM c_grupos WHERE c_grupo_status = 1 AND c_grupo_id = '$id' ");
			$tipo = 'GRUPO';
			$tabela = 'c_grupo';
			break;
	case 2: ///SUBGRUPO
			$qr_plano = mysql_query("SELECT * FROM c_subgrupos WHERE c_subgrupo_status = 1 AND c_subgrupo_id = '$id'");
			$tipo = 'SUBGRUPO';
			$tabela = 'c_subgrupo';
			break;
	
	case 3: ///TIPO
			$qr_plano = mysql_query("SELECT * FROM c_tipos WHERE c_tipo_status = 1 AND c_tipo_id = '$id'");
			$tipo = 'TIPO';
			$tabela = 'c_tipo';
			break;
			
	case 4: ///SUBTIPO
			$qr_plano = mysql_query("SELECT * FROM c_subtipos WHERE c_subtipo_status = 1 AND c_subtipo_id = '$id'");
			$tipo = 'SUBTIPO';
			$tabela = 'c_subtipo';
			break;
			
	case 5: ///SUBTIPO
			$qr_plano = mysql_query("SELECT * FROM c_contas WHERE c_conta_status = 1 AND c_conta_id = '$id'");
			$tipo = 'SUBTIPO';
			$tabela = 'c_conta';
			break;
}

$row_plano = mysql_fetch_assoc($qr_plano);

//////////////////////////////////////////////////////////////////
////////////FAZENDO O UPDATE ///////////////////////////////////

if(isset($_POST['atualizar'])){

$id 		 = $_POST['id'];
$tipo_tabela = $_POST['tipo_tabela'];
$grupo_id	 = $_POST['grupo'];
$subgrupo_id = $_POST['subgrupo'];
$tipo_id	 = $_POST['tipo'];
$subtipo_id	 = $_POST['subtipo'];


$codigo		 = $_POST['codigo'];
$T			 = $_POST['T'];
$classificacao = $_POST['classificacao'];
$nome		   =  $_POST['nome'];

switch($tipo_tabela){
		
	case 1: ///GRUPO
			$qr_plano = mysql_query("UPDATE c_grupos SET  c_grupo_T = '$T' , 
														  c_grupo_classificacao = '$classificacao', 
														  c_grupo_nome = '$nome' 
														  WHERE c_grupo_id = '$id' ");
			header("Location: listar_grupo.php");
			
			break;
	case 2: ///SUBGRUPO
			$qr_plano = mysql_query("UPDATE c_subgrupos SET  c_grupo_id = '$grupo_id',
															 c_subgrupo_T = '$T' , 
														     c_subgrupo_classificacao = '$classificacao', 
														     c_subgrupo_nome = '$nome' 
														     WHERE c_subgrupo_id = '$id' ");
			header("Location: listar_subgrupo.php");
			break;
	
	case 3: ///TIPO
			$qr_plano = mysql_query("UPDATE c_tipos SET  c_grupo_id = '$grupo_id',
														 c_subgrupo_id = '$subgrupo_id',	
														 c_tipo_T = '$T' , 
														 c_tipo_classificacao = '$classificacao', 
														 c_tipo_nome = '$nome' 
														 WHERE c_tipo_id = '$id' ");			
			header("Location: listar_tipo.php");
			break;
			
	case 4: ///SUBTIPO
			$qr_plano = mysql_query("UPDATE c_subtipos SET   c_grupo_id = '$grupo_id',
															 c_subgrupo_id = '$subgrupo_id',
															 c_tipo_id = '$tipo_id',	
															 c_subtipo_T = '$T' , 
															 c_subtipo_classificacao = '$classificacao', 
															 c_subtipo_nome = '$nome' 
															 WHERE c_subtipo_id = '$id' ");			
				header("Location: listar_subtipo.php");
				break;
				
				
	case 5: ///CONTAS
			$qr_plano = mysql_query("UPDATE c_contas SET     c_conta_id = '$grupo_id',
															 c_subgrupo_id = '$subgrupo_id',
															 c_tipo_id = '$tipo_id',	
															 c_subtipo_T = '$T' , 
															 c_conta_classificacao = '$classificacao', 
															 c_conta_nome = '$nome' 
															 WHERE c_conta_id = '$id' ") or die(mysql_error());			
				header("Location: listar_contas.php");
				break;
}

	
}
///////////////////////////////////////////////







?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>:: Intranet :: Editar de Plano de contas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../../favicon.ico">
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
	var valor = $(this).val();
	$.ajax({
		url: 'action.selects.php?tp=tipo&val='+valor,
		success: function(resposta){
					 $('#subtipo').html(resposta); 
			 	   
			  }		
		});
	});
	

	
	
	
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
		
		case 1: if(codigo == '') { $('.alert_cod').html('Digite o código'); } 
				else if( T == ''){ 	$('.alert_T').html('Digite o T'); }
				else if(classificacao == '') { $('.alert_classificacao').html('Digite a classificação'); }
				else if(	nome == '') {	$('.alert_nome').html('Digite o nome'); }
				return false;
		break;
		}
}	
	
	
});
</script>
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




<form action="" name="form" id="form" method="post">
<table width="100%">
<!---
<tr>
	<td  style="text-align:right"  class="secao">GRAU:</td>
    <td>
    <select name="grau" id="grau" class="validate[required]"> 
    	<option value="">Selecione o grau....</option>
        <option value="1" <?php if($tipo_tabela == 1) echo 'selected="selected"';?>>1 - GRUPO</option>
        <option value="2"  <?php if($tipo_tabela == 2) echo 'selected="selected"';?>>2 - SUBGRUPO</option>
        <option value="3"  <?php if($tipo_tabela == 3) echo 'selected="selected"';?>>3 - TIPO</option>
        <option value="4"  <?php if($tipo_tabela == 4) echo 'selected="selected"';?>>4 - SUBTIPO</option>
        <option value="5"  <?php if($tipo_tabela == 5) echo 'selected="selected"';?>>5 - CONTA</option>
    </select>
    </td>
</tr>
--->



<?php if($tipo_tabela != 1) : ?>
<tr class="grupo">
	<td style="text-align:right"  class="secao">GRUPO:</td>
    <td>
    	<select name="grupo" id="grupo">
    	<?php
		echo '<option value="">Selecione ....</option>';
		$qr_grupo = mysql_query("SELECT * FROM c_grupos WHERE c_grupo_status = 1 ");
		while($row_grupo = mysql_fetch_assoc($qr_grupo)):
		
			$selected = ($row_grupo['c_grupo_id'] == $row_plano['c_grupo_id'])? 'selected="selected"' : '';
			echo '<option value="'.$row_grupo['c_grupo_id'].'"  '.$selected.'  >'.$row_grupo['c_grupo_nome'].'</option>';
			
		endwhile;
		?>
        </select></td>
</tr>
<?php  endif;  

 if($tipo_tabela != 1 and $tipo_tabela != 2) : 
?>
<tr class="subgrupo">
	<td style="text-align:right" class="secao">SUBGRUPO:</td>
    <td>
        	<select name="subgrupo" id="subgrupo">
            <?php
			echo '<option value="">Selecione ....</option>';
            $qr_subgrupo = mysql_query("SELECT * FROM c_subgrupos WHERE c_subgrupo_status = 1 AND c_grupo_id = '$row_plano[c_grupo_id]' ");
			while($row_subgrupo = mysql_fetch_assoc($qr_subgrupo)):
				
				$selected = ($row_subgrupo['c_subgrupo_id'] == $row_plano['c_subgrupo_id'])? 'selected="selected"' : '';
				echo '<option value="'.$row_subgrupo['c_subgrupo_id'].'" '.$selected.' >'.$row_subgrupo['c_subgrupo_nome'].'</option>';
			
			endwhile;
			?>
            
    	</select>
       
    </td>
</tr>

<?php endif;

if($tipo_tabela != 1 and $tipo_tabela != 2 and $tipo_tabela != 3) : 

?>

<tr class="tipo">
	<td a style="text-align:right""  class="secao">Tipo:</td>
    <td>
    	<select name="tipo" id="tipo">
          <?php
		  echo '<option value="">Selecione ....</option>';
            $qr_tipo = mysql_query("SELECT * FROM c_tipos WHERE c_tipo_status = 1 AND c_subgrupo_id = '$row_plano[c_subgrupo_id]'");
			while($row_tipo = mysql_fetch_assoc($qr_tipo)):
				
				$selected = ($row_tipo['c_tipo_id'] == $row_plano['c_tipo_id'])? 'selected="selected"' : '';
				echo '<option value="'.$row_tipo['c_tipo_id'].'" '.$selected.' >'.$row_tipo['c_tipo_nome'].'</option>';
			
			endwhile;
			?>
    	</select>       
    </td>
</tr>
<?php endif;

if($tipo_tabela != 1 and $tipo_tabela != 2 and $tipo_tabela != 3 and  $tipo_tabela != 4 ) : 
?>
<tr class="subtipo">
	<td  style="text-align:right" class="secao">Subtipo:</td>
    <td>
    <select name="tipo" id="tipo">
    <?php
	echo '<option value="">Selecione ....</option>';
      $qr_subtipo = mysql_query("SELECT * FROM c_subtipos WHERE c_subtipo_status = 1 AND c_tipo_id = '$row_plano[c_tipo_id]'");
			while($row_subtipo = mysql_fetch_assoc($qr_subtipo)):
				
				$selected = ($row_subtipo['c_tipo_id'] == $row_plano['c_tipo_id'])? 'selected="selected"' : '';
				echo '<option value="'.$row_subtipo['c_subtipo_id'].'" '.$selected.' >'.$row_subtipo['c_subtipo_nome'].'</option>';
			
			endwhile;
	?>
    	 </select>      
    </td>
</tr>
<?php endif; ?>


<tr>
	<td  style="text-align:right"  class="secao">T:</td>
    <td><input name="T" id="T" type="text" class="validate[required]" value="<?php echo $row_plano[$tabela.'_T'];?>"/></td>
</tr>
<tr>
	<td  style="text-align:right"  class="secao">Classificação:</td>
    <td><input name="classificacao" id="classificacao" type="text" class="validate[required]" value="<?php echo $row_plano[$tabela.'_classificacao'];?>"/></td>
</tr>
<tr>
	<td  style="text-align:right"  class="secao">Nome:</td>
    <td><input name="nome" id="nome" type="text" class="validate[required]"  value="<?php echo $row_plano[$tabela.'_nome'];?>" /></td>
</tr>
<tr>
 	<td colspan="2"  style="text-align:center"  >
    <input type="hidden" name="regiao" value="<?php echo $regiao;?>"/>
    <input type="hidden" name="tipo_tabela" value="<?php echo $tipo_tabela;?>"/>
   	<input type="hidden" name="id" value="<?php echo $id;?>"/>
    <input type="submit" name="atualizar" value="Atualizar"/>
</tr>

</table>
</form>
    


</table>






</div>
</body>
</html>
