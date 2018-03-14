<?php
include "../../conn.php";
$qr_saida = mysql_query("SELECT *, tipo.nome as tipo_saida FROM saida INNER JOIN entradaesaida as tipo
ON saida.tipo = tipo.id_entradasaida
WHERE saida.id_saida = '$_GET[ID]'");
$row_saida = mysql_fetch_assoc($qr_saida);

$query_grupos = mysql_query("SELECT * FROM entradaesaida_grupo WHERE id_grupo = '$row_saida[grupo]'");
$row_grupos = mysql_fetch_assoc($query_grupos);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<style type="text/css">
body{
	margin:0px;
	text-align:center;
	font-family:Arial, Helvetica, sans-serif;
	font-size: 12px;
}
#conteiner{
	text-align:left;
	margin:10px auto;
	width:550px;
}
legend{
	font-size:16px;
	text-transform: none;
}
#loading {
	top: 30%;
	position: absolute;
	left: 40%;
	display: none;
	z-index:1000;
}
#cadastro_nomes{
	width: 300px;
}
</style>

<!-- highslide -->
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
<script type="text/javascript" src="../../js/highslide-with-html.js"></script>
<script type="text/javascript" >
	hs.graphicsDir = '../../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.showCredits = false;
	hs.wrapperClassName = 'draggable-header';
</script>
<!-- highslide -->

<link href="../style/form.css" rel="stylesheet" type="text/css">
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script language="javascript" type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>

<script type="text/javascript">
$(function(){
	function limpar(ID){
		$(ID).find('input[type*=text]').val('');
	}
	function mostraLoading(){
		$("#loading").fadeIn();
	}
	function ocultaLoading(){
		$("#loading").fadeOut();
	}
	
	$("select[name*=grupo]").change(function(){
		 mostraLoading();
		$("#tipo").html('<option value="">Carregando...</option>');
		$("#tipo").attr('disabled','disabled');
		$("#nome").html('<option value="" selected="selected">Selecione</option>');
		$.post('../actions/combo.tipo.php',
				{grupo:$(this).val()},
				function(retorno){
					$("#tipo").removeAttr('disabled');
					$("#tipo").html('<option value="" selected="selected">Selecione</option>');
					$.each(retorno, function(i,valor){
						$("#tipo").append('<option value="'+valor.id_entradasaida+'">'+valor.id_entradasaida+' - '+valor.nome+'</option>');
					});
					ocultaLoading();
				},
				'json'
				);
	});
	
	$("#tipo").change(function(){
		 mostraLoading();
		$("#nome").html('<option value="">Carregando...</option>');
		$("#nome").attr('disabled','disabled');
		$.post('../actions/combo.nome.json.php',
			{tipo:$(this).val()},
			function(retorno){
				$("#nome").removeAttr('disabled');
					$("#nome").html('<option value="" selected="selected">Selecione</option>');
					$.each(retorno, function(i,valor){
						$("#nome").append('<option value="'+valor.id_nome+'">'+valor.nome+'</option>');
					});
					ocultaLoading();
			},
			'json'			
			);
	});
	
	$("#anexo").uploadify({
		'uploader'       : '../../uploadfy/scripts/uploadify.swf',
		'script'         : '../../include/upload_financeiro.php',
		'folder'         : 'fotos',
		'buttonText'     : 'Enviar foto',
		'queueID'        : 'barra_upload',
		'cancelImg'      : '../../uploadfy/cancel.png',
		'buttonImg'      : '../image/anexar.jpg',
		'width'          : 79,
		'height'		 : 80,
		'auto'           : false,
		'method'         : 'post',
		'multi'          : true,
		'fileDesc'       : 'Gif, Jpg e Png',
		'fileExt'        : '*.gif;*.jpg;*.png;',
		'scriptData'	 : {Ultimo_ID : '<?=$_GET['ID']?>'}
	});
	
	$("a.highslide").click(function(){
		if($("#tipo").val() != ""){
			hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header', contentId: 'cadastro_nomes' } );
		}else{
			alert("Selecione um tipo primeiro!");
		}
	});
	
	$("#form").submit(function(e){
		mostraLoading();
		e.preventDefault();
		var dados = $(this).serialize();
		$.post('../actions/edicao.saida.php',dados,function(ee){alert(ee); ocultaLoading();});
		
		
	});
	
	
	$("#form2").submit(function(e){	
		
		mostraLoading();
		e.preventDefault();
		var dados = $(this).serialize();
		var exp = window.hs.getExpander();
		if (exp) {
				exp.close();
		}
		$.post('../actions/form.submit.nomes.php',
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
	
	$("#tipo2").val($("#tipo").val());
	$("#tipo").change(function(){
		$("#tipo2").val($(this).val());
	});
});
</script>
</head>
<body>
<div id="loading">
	<img src="../image/ajax-loader.gif" width="32" height="32"/>
</div>
<div id="conteiner">
	<form action="" name="form" id="form" method="post" enctype="multipart/form-data">
    	<fieldset>
       	  <legend>Edi&ccedil;&atilde;o de saidas</legend>
           	<table width="505" align="center">
            	<tr>
                	<td width="105">SA&Iacute;DA</td>
                    <td width="171">(1234) ALUGUEM ISPV</td>
                    <td width="213">&nbsp;</td>
                </tr>
                <tr>
                	<td>GRUPO</td>
                    <td>
                    	<select name="grupo" id="grupo">
                        	<option value="">Selecione</option>
                            <?php
								$qr_grupo = mysql_query("SELECT * FROM entradaesaida_grupo");
								while($row_grupo = mysql_fetch_assoc($qr_grupo)){
									if($row_grupo['id_grupo'] == $row_saida['grupo']){
										echo "<option selected='selected' value='$row_grupo[id_grupo]'>$row_grupo[nome_grupo]</option>";
									}else{
										echo "<option value='$row_grupo[id_grupo]'>$row_grupo[nome_grupo]</option>";
									}
								}
							?>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                	<td>TIPO</td>
                    <td>
                    	<select name="tipo" id="tipo">
                        	<option value="" selected="selected"><?=$row_saida['tipo_saida']?></option>
                          
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>NOME</td>
                  <td>
                  	  <select name="nome" id="nome">
                  	  	<option value="">Selecione</option>
                      </select>
               	  		<a href="#" class="highslide" onClick="return false">
                        Adicionar
                        </a>
                  </td>
                  <td><input type="hidden" name="ID" id="ID" value="<?=$_GET['ID']?>" /></td>
                </tr>
                <tr>
                	<td>Descri&ccedil;&atilde;o</td>
                    <td colspan="2">
                    <textarea name="descricao" cols="40" rows="5" id="descricao"><?=$row_saida['especifica'];?></textarea>
                    </td>
                </tr>
                <tr>
                  <td colspan="3" align="center" id="barra_upload" >&nbsp;</td>
                </tr>
                <tr>
                	<td colspan="3" align="center">Comprovante de pagamento<br />
                	  <label for="anexo"></label>
               	    <input type="file" name="anexo" id="anexo" /></td>
               	</tr>
                <tr>
                	<td colspan="3" align="center"><input type="submit" name="button" id="button" value="Finalizar" class="submit-go" /></td>
                </tr>
                <tr>
                	<td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
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