<?php 
include ("include/restricoes.php");
include "../conn.php";
$regiao = $_REQUEST['regiao'];
$id_user = $_COOKIE['logado'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Intranet - Financeiro</title>
<style type="text/css">
body {
	background-color: #F3F3F3;
	text-align:center;
}
a, a:link, a:active{
	margin:0px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333;
	text-decoration: underline;
}
</style>
<!-- highslide -->
<link rel="stylesheet" type="text/css" href="../js/highslide.css" />
<script type="text/javascript" src="../js/highslide-with-html.js"></script>
<script type="text/javascript" >
	hs.graphicsDir = '../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.showCredits = false;
	hs.wrapperClassName = 'draggable-header';
</script>
<!-- highslide -->
<link href="style/estrutura.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css" media="screen" title="no title" charset="utf-8" />
<link href="../uploadfy/css/default.css" rel="stylesheet" type="text/css" />
<link href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
<script language="javascript" type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="../js/formatavalor.js"></script>
<script type="text/javascript">
$(function(){
	function mostraLoading(){
		$('#base').fadeTo('fast',0.5);
		$("#loading").fadeIn();
	}
	function ocultaLoading(){
		$('#base').fadeTo('fast',1.0);
		$("#loading").fadeOut();
	}
	
	function limpar(ID){
		$(ID).find('input[type*=text]').val('');
	}
	
	function reloader(){
		opener.location.reload();
		window.close();
		
	}
	
	$("#tipo2").val($("#tipo").val());
	$("#tipo").change(function(){
		$("#tipo2").val($(this).val());
	});

	$('#data').datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true
	});
	
	$("#Form").submit(function(e){
		// Verificação do prestador
		var prestador = $('#interno').val();
		if(prestador != ''){
			var p = window.confirm("Atenção!!! \n" + 'Você esta certo que esta cadastrando uma saida para o prestador.\n' + $('#interno').find('option').filter(':selected').text() + ' de ' + $('#regiao-prestador').find('option').filter(':selected').text() + ' ?');
			if(p == false){
				return false;
			}
		}
		if($('#grupo').val() == ""){
			alert("SELECIOLE UM GRUPO!");
			return false;
		}
		if($('#tipo').val() == "" ){
			alert("SELECIOLE UM TIPO!");
			return false;
		}
		
		if($('#real').val() == "" || $('#real').val() == "0,00"){
			alert("DIGITE UM VALOR REAL!");
			return false;
		}
		
		if($('#data').val() == ""){
			alert("DIGITE UMA DATA!");
			return false;
		}
		/*
		if($('#nome').val() == ""){
			alert("SELECIONE UM NOME!");
			return false;
		}*/
		
		mostraLoading();
		e.preventDefault();
		var dados = $(this).serialize();  
		$.post('actions/form.submit.php',
			  dados,
			  function(retorn){
				  if(retorn == "Erro"){
					  alert("Erro interno!");
					  return false;
					 
				  }
				  if($('#barra_upload').html() == ""){
				  	alert("Cadastrado com sucesso!");
					ocultaLoading();
					limpar("#Form");
					reloader();
					
				  }else{
				  		$('#FileUp').uploadifySettings('scriptData',{Ultimo_ID: retorn});
				  		$('#FileUp').uploadifyUpload();
						
				  }
				  
			  }
			  
			
			  );
	});
	
	$("#form2").submit(function(e){	
		
		mostraLoading();
		e.preventDefault();
		var dados = $(this).serialize();
		var exp = window.hs.getExpander();
		if (exp) {
				exp.close();
		}
		$.post('actions/form.submit.nomes.php',
				dados,
				function(retorno){
					$.each(retorno, function(i,valor){
							$("#nome").append('<option selected="selected" value="'+valor.id_nome+'">'+valor.nome+'</option>');
							ocultaLoading();
							limpar("#form2");
							
					});
					
				},
				'json'
		);
	});
	
	
	$("select[name*=grupo]").change(function(){
		 mostraLoading();
		$("#tipo").html('<option value="">Carregando...</option>');
		$("#tipo").attr('disabled','disabled');
		$("#nome").html('<option value="" selected="selected">Selecione</option>');
		$.post('actions/combo.tipo.php',
				{grupo:$(this).val()},
				function(retorno){
					$("#tipo").removeAttr('disabled');
					$("#tipo").html('<option value="" selected="selected">Selecione</option>');
					$.each(retorno, function(i,valor){
						$("#tipo").append('<option value="'+valor.id_entradasaida+'">'+valor.id_entradasaida+' - '+valor.nome+'</option>');
					});
					ocultaLoading();
					$("#tipo").focus();
				},
				'json'
				);
	});
	
	$("#tipo").change(function(){
		 mostraLoading();
		 if($(this).val() == '132'){
			 $('.interno').fadeIn('slow');
			 $(".nomes-cad").fadeOut('slow');
		 }else{
		 	$('.interno').hide();
			$('.nomes-cad').show();
		 }
		$("#nome").html('<option value="">Carregando...</option>');
		$("#nome").attr('disabled','disabled');
		$.post('actions/combo.nome.json.php',
			{tipo:$(this).val()},
			function(retorno){
				$("#nome").removeAttr('disabled');
					$("#nome").html('<option value="" selected="selected">Selecione</option>');
					$.each(retorno, function(i,valor){
						$("#nome").append('<option value="'+valor.id_nome+'">'+valor.nome+'</option>');
					});
					ocultaLoading();
					$("#nome").focus();
			},
			'json'			
			);
	});
	<?php
	$regioes_prestadores = array(15,37,36);
	 if(in_array($regiao,$regioes_prestadores)):?>
	$('#regiao-prestador').change(function(){
		 mostraLoading();
		$.post('actions/combo.projeto.json.php',
		{ regiao : $(this).val()},
		function(retorno){
			$('#interno').html('<option value="" selected="selected" >Selecione</option>');
			$('#Projeto-prestador').html('<option value="" selected="selected" >Selecione</option>');
			$.each(retorno, function(i,valor){
				$('#Projeto-prestador').append('<option value="'+valor.id_projeto+'">'+valor.id_projeto+' - '+valor.nome+'</option>');
			});
			ocultaLoading();
			$('#Projeto-prestador').focus();

		},
		'json'
		);
	});
	
	
	$('#Projeto-prestador').change(function(){
		 mostraLoading();
		$.post('actions/combo.prestador.json.php',
		{ regiao : $('#regiao-prestador').val(), projeto : $(this).val()},
		function(retorno){
			$('#interno').html('<option value="" selected="selected" >Selecione</option>');
			$.each(retorno, function(i,valor){
				$('#interno').append('<option value="'+valor.id_prestador+'">'+valor.id_prestador+' - '+valor.c_fantasia+'</option>');
			});
			ocultaLoading();
			$('#interno').focus();
		},
		'json'
		);
	});
	
	<?php endif;?>
	
	$('#FileUp').uploadify({
				'uploader'       : '../uploadfy/scripts/uploadify.swf',
				'script'         : '../include/upload_financeiro.php',
				'folder'         : 'fotos',
				'buttonText'     : 'Enviar foto',
				'queueID'        : 'barra_upload',
				'cancelImg'      : '../uploadfy/cancel.png',
				'buttonImg'      : 'image/anexar.jpg',
				'width'          : 79,
				'height'		 : 80,
				'auto'           : false,
				'method'         : 'post',
 				'multi'          : true,
				'fileDesc'       : 'Gif, Jpg , Png e Pdf',
				'fileExt'        : '*.gif;*.jpg;*.png;*.pdf;',
				'onAllComplete'   : function(){
										ocultaLoading();
										alert("Cadastrado com sucesso!");
										limpar("#Form");
										reloader();
									},
				'onError'         : function(event,queueID,fileObj,errorObj){
										alert("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
									},
			 	'onSelect'        : function(){
									$("#barra_upload").append('<span style="color:#F00;font-size:10px;">Clique em cadastrar para concluir o envio!</span>');									}	});	
	$("a.highslide").click(function(){
		if($("#tipo").val() != ""){
			hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header', contentId: 'cadastro_nomes' } );
		}else{
			alert("Selecione um tipo primeiro!");
		}
	});
	
	
});
</script>
</head>
<body>
<div id="loading">
	<img src="image/ajax-loader.gif" width="32" height="32" />
    Carregando...
</div>

<div id="base">
<form method="post" action="" name="Form" id="Form">
	<fieldset class="Cadastro">
<legend>Cadastro de saida</legend> 
            <div>
            	<label for="projeto">PROJETO:</label>
                <?php $query_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");?>
				<select name="projeto" class="validate[required]">
                <?php while($row_projeto = mysql_fetch_assoc($query_projeto)){
						print '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['id_projeto'] .' - '. $row_projeto['nome'].'</option>';
					  }
					
				?>
                </select>
            </div>
            <div>
            	<label for="banco">CONTA PARA D&Eacute;BITO:</label>
                <?php $result_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' and interno = '1' AND status_reg = '1' ORDER BY nome ASC");?>
                <select name="banco" class="validate[required]">
                	<?php while($row_banco = mysql_fetch_assoc($result_banco)):
								print '<option value="'.$row_banco['id_banco'].'">'.$row_banco['id_banco'].' - '.$row_banco['nome'].'</option>';
						  endwhile;
					?>
                </select>
            </div>
			<div> 
				<label for="grupo">GRUPO:</label>
				<?php 
				$grupo = array('1'=>'Folha','2'=>'Reserva','3'=>'Taxa administrativa','4'=>'Tranferências ISPV');	
				?>  
                <select name="grupo" id="grupo" class="validate[required]">
                	<option value="" selected>Selecione</option>
                	<?php foreach($grupo as $chave => $valor):
							print '<option value="'.$chave.'">'.$chave.' - '.$valor.'</option>';
						  endforeach;
					?>
                </select>
			</div> 
			<div> 
				<label for="tipo">TIPO:</label>
                <select name="tipo" nome="tipo" id="tipo" class="validate[required]">
                	<option value="">Selecione um tipo</option>
                </select>
			</div> 
            <?php if(in_array($regiao,$regioes_prestadores )):?>
            <div class="interno" style="display:none;">
            	<label for="interno">Regiao: </label>
                <select name="regiao-prestador"  id="regiao-prestador">
                    <?php 
						$regioes_prestador = mysql_query("SELECT regioes.id_regiao,regioes.regiao,master.nome FROM regioes INNER JOIN master ON regioes.id_master = master.id_master
						 WHERE regioes.status = '1' AND regioes.id_regiao != 36 AND master.status = '1'");
						  while($rw_regioes_prestador = mysql_fetch_array($regioes_prestador)){
							 if($regiao == $rw_regioes_prestador[0]){
							 	$selected = "selected=\"selected\"";
							 }else{
								$selected = "";
							 }
							 if($repeat != $rw_regioes_prestador[2]){
							 	echo '<optgroup label="'.$rw_regioes_prestador[2].'">'; 
							 }
							 $repeat = $rw_regioes_prestador[2];
							 echo '<option '.$selected.' value="'.$rw_regioes_prestador[0].'" >'.$rw_regioes_prestador[0].' - '.$rw_regioes_prestador[1].'</option>'; 
							 if($repeat != $rw_regioes_prestador[2] && !empty($repeat)){
								 echo '</optgroup>';
							 }
							 $repeat = $rw_regioes_prestador[2];
						  }
					?>
                </select>
                
            </div>
            
            <div class="interno" style="display:none;">
            	<label for="interno">Projeto: </label>
                <select name="Projeto-prestador"  id="Projeto-prestador">
                   <?php 
				   	$qr_projeto = mysql_query("SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao'");
					while($rw_projeto = mysql_fetch_array($qr_projeto)){
						echo '<option value="'.$rw_projeto[0].'">'.$rw_projeto[0].' - '.$rw_projeto[1].'</option>';
					}
				   ?>
                </select>
                
            </div>
             <?php endif; ?>
            <div class="interno" style="display:none;">
            	<label for="interno">Prestador: </label>
                <select name="interno" size="3" id="interno">
                	<option value=""  selected="selected">Selecione um nome</option>
                    <?php $query_prestador = mysql_query("SELECT * FROM  prestadorservico WHERE id_regiao = '$regiao' AND status = '1'"); 
					while($row_prestador = mysql_fetch_assoc($query_prestador)){
						echo '<option value="'.$row_prestador['id_prestador'].'" >'.$row_prestador['id_prestador'].' - '.$row_prestador['c_fantasia'].'</option>';
					}
					?>
                </select>
                <a href="../processo/prestadorservico.php?regiao=<?=$regiao?>&id=1" target="_blank">Não esta na lista.</a>
            </div>
           
			<div class="nomes-cad"> 
			  <label for="nome">NOME:</label>
                <select name="nome" size="3" id="nome">
                	<option value="">Selecione um nome</option>
                </select>
                <a href="#" class="highslide" onClick="return false">
                Adicionar
                </a>
			</div>
            <div>
            	<label for="descricao">ESPECIFICA&Ccedil;&Atilde;O:</label>
                <input name="descricao" type="text" id="descricao"/>
            </div>
             <div>
            	<label for="adicional">VALOR ADICIONAL:</label>
                <input name="adicional" type="text" id="adicional" onKeyDown="FormataValor(this,event,17,2)" value="0,00"/>
            </div>
             <div>
            	<label for="real">VALOR REAL:</label>
                <input name="real" type="text" class="validate[required]" id="real" onKeyDown="FormataValor(this,event,17,2)" value="0,00"/>
            </div>
      <div>
            	<label for="data">DATA PARA CREDITO:</label>
                <input type="text" name="data" id="data" class="date" />
            </div>
            <div id="barra_upload"></div>
            <center>
            
                <input type="file" id="FileUp"/>
            </center>
            <center>
            	<input type="submit" class="submit-go" value="Cadastrar"/>
            	<input type="hidden" name="regiao" value="<?=$regiao?>" />
                <input type="hidden" name="logado" value="<?=$id_user?>" />
            </center>
            
		</fieldset> 
</form>
</div> 
<div style="display:none">
    <div id="cadastro_nomes">
    <div style="height:20px; border-bottom: 1px solid silver"> 
	        <a href="#" onClick="return hs.close(this)" class="control">Fechar</a> 
	</div> 
    <form name="form2" method="post"  id="form2" action="">
    <table width="0" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td align="right">NOME:</td>
      <td>
        <input type="text" name="nome" id="nome"></td>
      </tr>
    <tr>
      <td align="right">CNPJ/CPF:</td>
      <td>
        <input type="text" name="cpf" id="cpf"></td>
      </tr>
    <tr>
      <td align="right">DESCRICAO:</td>
      <td>
        <input type="text" name="descricao" id="descricao"></td>
      </tr>
    <tr>
      <td colspan="2" align="center">
      <input type="hidden" name="tipo" id="tipo2">
      <input type="submit" name="button" id="button" class="submit-go" value="Cadastrar"></td>
    </tr>
    </table>
    </form>
    </div>  
</div>
</body>
</html>
