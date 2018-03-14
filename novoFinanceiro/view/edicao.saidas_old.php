<?php
include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";
$qr_saida = mysql_query("SELECT *, tipo.nome as tipo_saida, saida.nome as nome_saida FROM saida INNER JOIN entradaesaida as tipo
ON saida.tipo = tipo.id_entradasaida
WHERE saida.id_saida = '$_GET[ID]'");
$row_saida = mysql_fetch_assoc($qr_saida);

$query_grupos = mysql_query("SELECT * FROM entradaesaida_grupo WHERE id_grupo = '$row_saida[grupo]'");
$row_grupos = mysql_fetch_assoc($query_grupos);

$query_file_pg = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$_GET[ID]'");
$num_file_pg = mysql_num_rows($query_file_pg);
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
	
	function fecha(){
		parent.window.location.reload();
		if (parent.window.hs) {
			var exp = parent.window.hs.getExpander();
			if (exp) {
				exp.close();
			}
		}
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
						if(valor.id_entradasaida != 19){// não pode ser do tipo caixa
						$("#tipo").append('<option value="'+valor.id_entradasaida+'">'+valor.id_entradasaida+' - '+valor.nome+'</option>');
						}
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
		'script'         : '../actions/upload.comprovante.pg.php',
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
		'fileDesc'       : 'Gif, Jpg , Png e pdf',
		'fileExt'        : '*.gif;*.jpg;*.png;*.pdf;',
		'scriptData'	 : {Ultimo_ID : '<?=$_GET['ID']?>'},
		'onAllComplete'  : function(){
								ocultaLoading();
								fecha();
							}
	});
	
	$("a.highslide").click(function(){
		if($("#tipo").val() != ""){
			hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header', contentId: 'cadastro_nomes' } );
		}else{
			alert("Selecione um tipo primeiro!");
		}
	});
	
	$("#form").submit(function(e){
		e.preventDefault();
		if($("#grupo").val() == ""){
			alert("SELECIONE UM GRUPO!");
			return false;
		}
		if($("#tipo").val() == ""){
			alert("SELECIONE UM TIPO!");
			return false;
		}
		if($("#nome").val() == ""){
			alert("SELECIONE UM NOME!");
			return false;
		}	
		mostraLoading();
		var dados = $(this).serialize();
		$.post('../actions/edicao.saida.php',
		dados,
		function(volta){
			
			if(volta == ""){
				alert("Erro...");
			}else{
				if($('#barra_upload').html() != ""){
					$("#anexo").uploadifyUpload();
				}else{
					ocultaLoading();
					fecha();
					
				}
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
                    <td width="171">(<?=$_GET['ID']?>) <?=$row_saida['nome_saida'];?></td>
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
										echo "<option selected='selected' value='$row_grupo[id_grupo]'>$row_grupo[id_grupo] - $row_grupo[nome_grupo]</option>";
									}else{
										echo "<option value='$row_grupo[id_grupo]'>$row_grupo[id_grupo] - $row_grupo[nome_grupo]</option>";
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
                            <?php 
							$query_tipo = mysql_query("SELECT * FROM entradaesaida WHERE grupo = '$row_saida[grupo]'");
							while($row_tipo = mysql_fetch_assoc($query_tipo)):?>
                            <?php if($row_saida['id_entradasaida'] == $row_tipo['id_entradasaida']):?>
                            	<option value="<?=$row_tipo['id_entradasaida']?>" selected="selected"><?=$row_tipo['id_entradasaida'].' - '.$row_tipo['nome']?></option>
                            <?php else:?>
                            	<option value="<?=$row_tipo['id_entradasaida']?>"><?=$row_tipo['id_entradasaida'].' - '.$row_tipo['nome']?></option>
                            <?php endif; ?>
                            <?php endwhile;?>
                        </select>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>NOME</td>
                  <td>
                  	  <select name="nome" id="nome">
                      	<?php 
							$query_nome =  mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_entradasaida = '$row_saida[id_entradasaida]' ORDER BY nome");
							while($row_nome = mysql_fetch_assoc($query_nome)): ?>
                            <?php if($row_nome['id_nome'] == $row_saida['id_nome']):?>
								<option selected="selected" value="<?=$row_nome['id_nome']?>"><?=$row_nome['nome']?></option>
                           	<?php elseif($row_saida['id_nome'] == '0'): ?>
                            <option selected="selected" value="">selecione</option>
                            <?php break;?>
                            <?php else: ?>
                           	 <option value="<?=$row_nome['id_nome']?>"><?=$row_nome['nome']?></option>
                            <?php endif; ?>
						<?php endwhile;?>
                  	  	
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
                <?php if(empty($num_file_pg)): ?>
                <tr>
                  <td colspan="3" align="center" id="barra_upload" ></td>
                </tr>
                <tr>
                	<td colspan="3" align="center">Comprovante de pagamento<br />
                	  <label for="anexo"></label>
               	    <input type="file" name="anexo" id="anexo" /></td>
               	</tr>
                <?php else: ?>
				<tr>
                  <td colspan="3" align="center" id="barra_upload" ></td>
                </tr>
				<?php while($row_file_pg = mysql_fetch_assoc($query_file_pg)): ?>
                <?php $link_encryptado = encrypt('ID='.$row_saida['id_saida'].'&tipo=1'); ?>
                
                <tr>
                	<td colspan="3" align="center">
                    	Comprovante de pagamento<br />
                        <?php if($row_file_pg['tipo_pg'] == '.pdf'): ?>
                       		<a href="comprovantes.php?<?=$link_encryptado?>" target="_blank">
                        		<img src="../image/File-pdf-32.png"  />
                          	</a>
                        <?php else:?>
                    	<a href="comprovantes.php?<?=$link_encryptado?>" target="_blank">
                		<img src="../../classes/img.php?foto=../comprovantes/<?=$row_file_pg['id_pg'].'.'.$row_file_pg['id_saida'].'_pg'.$row_file_pg['tipo_pg']?>&w=100&h=100" border="0" />
                        </a>
                        <?php endif; ?>
                    </td>
               	</tr>
                <?php endwhile; ?>
                <?php endif; ?>
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