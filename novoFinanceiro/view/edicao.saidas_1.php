<?php
//include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";
$qr_saida = mysql_query("SELECT *, tipo.nome as tipo_saida, saida.nome as nome_saida FROM saida INNER JOIN entradaesaida as tipo
ON saida.tipo = tipo.id_entradasaida
WHERE saida.id_saida = '$_GET[ID]'");
$row_saida = mysql_fetch_assoc($qr_saida);

$regiao = $row_saida['id_regiao'];

$query_grupos = mysql_query("SELECT * FROM entradaesaida_grupo WHERE id_grupo = '$row_saida[grupo]'");
$row_grupos = mysql_fetch_assoc($query_grupos);

$query_file_pg = mysql_query("SELECT * FROM saida_files_pg WHERE id_saida = '$_GET[ID]'");
$num_file_pg = mysql_num_rows($query_file_pg);

// VERIFICANDO SE EXISTE REGISTRO NA TABELA PRESTADOR_PG
$qr_prestador_pg = mysql_query("SELECT *  FROM prestador_pg WHERE id_saida = '$row_saida[id_saida]' AND status_reg = '1'");
$num_prestador_pg = mysql_num_rows($qr_prestador_pg);
$row_prestador_pg = mysql_fetch_assoc($qr_prestador_pg);

//print_r($row_prestador_pg);
$regioes_prestadores = array(15,37);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EDITAR SAIDA</title>
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
	position:fixed;
	left: 40%;
	display: none;
	z-index:1000;
}
#cadastro_nomes{
	width: 300px;
}
#lancamento_prestador{
	background:#FBFBFB;
	height:200px;
	overflow:scroll;
	text-align:center;
}
#lancamento_prestador div{
	text-align:left;
	padding:5px;
	margin: 3px auto;
	width:90%;
	border: 1px #FFF solid;
	cursor:pointer;
	background:#FFF;
	overflow:hidden;
	
}
#lancamento_prestador div:hover{
	border:solid #EAEAEA 1px;
}
#lancamento_prestador div span{
	width:20%;
	display:block;
	float:left;
}
#title_lancamentos{
	text-align:center;
	font-size:14px;
	color:#F20000;
}
.marcado {
	background:#CCC !important;
}
.desmarcado {
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
						if(valor.id_entradasaida != 19){// nï¿½o pode ser do tipo caixa
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
		 <?php //if(in_array($regiao,$regioes_prestadores) || !empty($num_prestador_pg)):?>
		 if($(this).val() == '132' || $(this).val() == '32'){
			 $('.prestador_campos').fadeIn('slow');
			 $("#nome_saida").fadeOut('slow');
		 }else{
		 	$('.prestador_campos').hide();
			$('#nome_saida').show();
		 }
		 <?php // endif;?>
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
	

	/*
	SCRIPT CASO O TIPO DA SAIDA FOR 132 - 
	*/
	$('#regiao-prestador').change(function(){
		 mostraLoading();
		 $('.novoPrestador').attr('href','../../processo/prestadorservico.php?regiao='+$(this).val()+'&id=1');
		$.post('../actions/combo.projeto.json.php',
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
		$.post('../actions/combo.prestador.json.php',
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
	
	
	
    /* FIM DO SCRIPT PRESTADOR */
	var lancamento_prestador = $('#lancamento_prestador');
	var id_lancamento = '';
	$('#interno').change(function(){
		mostraLoading();
		$('.display_lancamentos').show();
		$.post('../actions/combo.lancamento.prestador.json.php',
			{id_prestador: $(this).val()},
			function(dados){
				lancamento_prestador.html('');
				$.each(dados,function(i,valor){
					lancamento_prestador.append('<div id="'+valor.id_pg+'"><span>'+valor.documento+'</span><span>'+valor.data+'</span> <span>'+valor.valor+'</span><span>'+valor.comprovante+'</span>');
				});
				ocultaLoading();
				$('#lancamento_prestador div').click(function(){
					$('#lancamento_prestador div').attr('class','desmarcado');
					$(this).toggleClass('marcado');
					id_lancamento = $(this).attr('id');
					$('#lancamento_prest').val(id_lancamento);
				});
				
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
		if($("#tipo").val() == '132' || $("#tipo").val() == '32'){
			if($("#interno").val() == ""){
				alert("SELECIONE UM PRESTADOR!");
				return false;
			}
		}else{
			/*-if($("#nome").val() == ""){
				alert("SELECIONE UM NOME!");
				return false;
			}	*/
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
					alert(volta);
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
                	<td width="133">SA&Iacute;DA</td>
                    <td width="227">(<?=$_GET['ID']?>) <?=$row_saida['nome_saida'];?></td>
                    <td width="129">&nbsp;</td>
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
                <?php 
				if($row_saida['id_entradasaida']  == '132' or $row_saida['id_entradasaida']  == '32'){
					echo "<script>
						$(function(){
							$('.prestador_campos').show();
							$('#nome_saida').hide();
						});
						</script>";
				}else{
					echo "<script>
						$(function(){
							$('.prestador_campos').hide();
							$('#nome_saida').show();
						});
						</script>";
				}
				?>
               
                
                
                <tr class="prestador_campos" >
                  <td height="27">REGI&Acirc;O PRESTADOR</td>
                  <td colspan="2">
                  	<?php 
                  	// BUSCANDO OS dados de regiao do prestador
                    $qr_dados_prestador = mysql_query("SELECT * 
										FROM  `prestadorservico` 
										WHERE id_prestador = '$row_prestador_pg[id_prestador]'");
					
                    $rw_dados_prestador = mysql_fetch_assoc($qr_dados_prestador);
					
                  	?>
                  	<select name="regiao-prestador"  id="regiao-prestador">
                    <?php
                    
                    	//if(!in_array($regiao,$regioes_prestadores)){
                    		//$limitacao_regiao = " AND regioes.id_regiao = '$regiao'";
                    	//} else{
                    		$limitacao_regiao = "";
                    	//}
						$regioes_prestador = mysql_query("SELECT regioes.id_regiao,regioes.regiao,master.nome FROM regioes INNER JOIN master ON regioes.id_master = master.id_master WHERE regioes.status = '1'  AND master.status = '1' $limitacao_regiao");
						  while($rw_regioes_prestador = mysql_fetch_array($regioes_prestador)){
							 
							 // se a saida jï¿½ tiver nota cadastrada no prestador, marca o a regiao do prestador no combobox
							 
							 if(!empty($num_prestador_pg)){
							 	/*if($row_prestador_pg['id_regiao'] == $rw_regioes_prestador['id_regiao']){
								 	$selected = "selected=\"selected\"";
								 }else{
									$selected = "";
								 }*/
								if($rw_dados_prestador['id_regiao'] == $rw_regioes_prestador['id_regiao']){
								 	$selected = "selected=\"selected\"";
								 }else{
									$selected = "";
								 }
							 }else{
							 
								 if($regiao == $rw_regioes_prestador['id_regiao']){
								 	$selected = "selected=\"selected\"";
								 }else{
									$selected = "";
								 }
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
                  </td>
                </tr>
                <tr class="prestador_campos" >
                  <td height="27">PROJETO</td>
                  <td colspan="2"><select name="Projeto-prestador"  id="Projeto-prestador">
                    <?php 
                   
                    $projeto_prestador = (!empty($num_prestador_pg)) ? $row_prestador_pg['id_regiao'] : $row_saida['id_regiao'];
					
					
                    
				   	$qr_projeto = mysql_query("SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$rw_dados_prestador[id_regiao]'");
					while($rw_projeto = mysql_fetch_array($qr_projeto)){
						/*if(!empty($num_prestador_pg)){
							 // buscanco o projeto na tabele de prestador de serviï¿½o
							$qr_projeto_prestador = mysql_query("SELECT id_projeto FROM prestadorservico WHERE id_prestador = '$row_prestador_pg[id_prestador]'");
							$id_projeto_prestador = @mysql_result($qr_projeto_prestador,0);
							
							$selected = ($rw_projeto[0] == $id_projeto_prestador ? "selected=\"selected\"" : "");
						}else{
							$selected = ($rw_projeto[0] == $row_prestador_pg['id_projeto'] ? "selected=\"selected\"" : "");	
						}	
						*/
						$selected = ($rw_dados_prestador['id_projeto'] == $rw_projeto[0]) ? "selected=\"selected\"" : "";					
						echo '<option '.$selected.' value="'.$rw_projeto[0].'">'.$rw_projeto[0].' - '.$rw_projeto[1].'</option>';
					}
				   ?>
                  </select></td>
                </tr>
               
                <tr class="prestador_campos" > 
                  <td height="27">PRESTADOR</td>
                  <td colspan="2">
                  	<select name="interno" size="3" id="interno">
                	<option value=""  selected="selected">Selecione um nome</option>
                    <?php 
                    $prestador = (!empty($num_prestador_pg)) ? $rw_dados_prestador['id_regiao'] : $row_saida['id_regiao'];
                    $query_prestador = mysql_query("SELECT * FROM  prestadorservico WHERE id_regiao = '$prestador' AND status = '1'"); 
					while($row_prestador = mysql_fetch_assoc($query_prestador)){
						$selected = ($row_prestador['id_prestador'] == $row_prestador_pg['id_prestador'] ? "selected=\"selected\"" : "");
						
						echo '<option '.$selected.' value="'.$row_prestador['id_prestador'].'" >'.$row_prestador['id_prestador'].' - '.$row_prestador['c_fantasia'].'</option>';
					}
					?>
                    </select>
                    <a href="#" class="novoPrestador" target="_blank">Não esta na lista.</a>
                    <input type="hidden" name="lancamento_prest" id="lancamento_prest" value="" />
                  </td>
                </tr>
                
                <?php if(empty($num_prestador_pg)):?>
                <tr class="display_lancamentos" style="display:none">
                	<td colspan="3">
                    	<div id="title_lancamentos"><b>Selecione abaixo o lançamento a que esta saida se refere</b></div><br />
                        <div id="lancamento_prestador">
                        	<div >
                            	<span>{data}</span> <span>{valor}</span> <span>{comprovante}</span>
                            </div>
                        </div>
                        <input type="hidden" name="lancamento_prest" id="lancamento_prest" value="" />
                    </td>
                   
                </tr>
                <?php endif;?>
                
                
                <tr id="nome_saida" >
                  <td>NOME</td>
                  <td colspan="2">
                  	  <select name="nome" id="nome">
                      	<?php 
							$query_nome =  mysql_query("SELECT * FROM entradaesaida_nomes WHERE id_entradasaida = '$row_saida[id_entradasaida]' ORDER BY nome");
							while($row_nome = mysql_fetch_assoc($query_nome)): ?>
                            <?php if($row_nome['id_nome'] == $row_saida['id_nome']):?>
								<option selected="selected" value="<?=$row_nome['id_nome']?>"><?=$row_nome['nome']?></option>
                           	<?php elseif($row_saida['id_nome'] == '0'): ?>
                            <option selected="selected" value=""><?=$row_saida['nome_saida']?></option>
                            <?php break;?>
                            <?php else: ?>
                           	 <option value="<?=$row_nome['id_nome']?>"><?=$row_nome['nome']?></option>
                            <?php endif; ?>
						<?php endwhile;?>
                  	  	
                      </select>
               	  		<a href="#" class="highslide" onClick="return false">
                        Adicionar
                  </a>                  <input type="hidden" name="ID" id="ID" value="<?=$_GET['ID']?>" /></td>
                </tr>
                <tr>
                	<td>Descri&ccedil;&atilde;o</td>
                    <td colspan="2">
                    <textarea name="descricao" cols="40" rows="5" id="descricao"><?=$row_saida['especifica'];?></textarea>
                    </td>
                </tr>
                
                <?php if(isset($_GET['edicao'])):?>
                <script type="text/javascript">
                $('#FileUp_2').uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : '../../include/upload_financeiro.php',
				'folder'         : 'fotos',
				'buttonText'     : 'Enviar foto',
				'queueID'        : 'barra_upload_2',
				'cancelImg'      : '../../uploadfy/cancel.png',
				'buttonImg'      : '../image/anexar.jpg',
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
									$("#barra_upload_2").append('<span style="color:#F00;font-size:10px;">Clique em cadastrar para concluir o envio!</span>');									}	});	
                </script>
                <tr>
                	<td colspan="3">
                    	<div id="barra_upload_2"></div>
                         <center>
                            <input type="file" id="FileUp_2" name="FileUp_2"/>
                        </center>
                    </td>
                </tr>
                <?php 
				$qr_saida_files = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_saida[id_saida]'");
				while($row_saida_files = mysql_fetch_assoc($qr_saida_files)):
				?>
                <tr>
                	<td colspan="3" align="center">
                    <?php $link_encryptado = encrypt('ID='.$row_saida['id_saida'].'&tipo=0');?>
                    <?php if($row_saida_files['tipo_pg'] == '.pdf'):?>
                        <a href="comprovantes.php?<?=$link_encryptado?>" target="_blank">
                            <img src="../image/File-pdf-32.png"  />
                        </a>
                    <?php else:?>
                    	<a href="comprovantes.php?<?=$link_encryptado?>" target="_blank">
                		<img src="../../classes/img.php?foto=../comprovantes/<?=$row_saida_files['id_pg'].'.'.$row_saida_files['id_saida'].$row_saida_files['tipo_pg']?>&w=100&h=100" border="0" />
                        </a>
                    <?php endif;?>
                    </td>
                </tr>
                <?php endwhile;?>
                 <?php else:?>               
                <tr>
                  <td colspan="3" align="center" id="barra_upload" ></td>
                </tr>
                <tr>
                	<td colspan="3" align="center">Comprovante de pagamento<br />
                	  <label for="anexo"></label>
               	    <input type="file" name="anexo" id="anexo" />
                    </td>
               	</tr>
                <?php if(!empty($num_file_pg)): ?>
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
                <?php endif;?>
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