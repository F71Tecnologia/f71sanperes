<?php 
include ("../include/restricoes.php");
include "../../conn.php";
include "../../funcoes.php";

$id_saida = $_REQUEST['id'];

# A principio erar para ser uma pagina para editar entradas e saidas. 
# MAs como no IPSV ninguem tem certeza de nada. ai fica esse bacalhau.

#$tabela = (isset($_GET['entrada'])) ? 'entrada' : 'saida';
$tabela = 'saida';

$qr_saida = mysql_query("SELECT * FROM $tabela WHERE id_$tabela = '$id_saida' LIMIT 1");
$row_saida = mysql_fetch_assoc($qr_saida);


///VERIFICA SE A SAIDA É VINCULADA A ALGUM PROCESSO JURÍDICO
$qr_processo_assoc = mysql_query("SELECT * FROM andamento_saida_assoc WHERE id_saida = '$row_saida[id_saida]'") or die(mysql_error());
$row_assoc 			= mysql_fetch_assoc($qr_processo_assoc);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="../style/form.css"/>
<link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
<title>Editar saida</title>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script language="javascript" type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>

<script type="text/javascript">
$(function(){
	
	var cont_li = $('.anexos ul li').length;
	var width = $('.anexos ul li').width();
	
	$('.anexos ul').width(cont_li*100);
	
	
	
	$("#form1").submit(function(e){
		$(this).find('input[type*=submit]').attr('disabled',false);
		e.preventDefault();
		var campo = false;
		$("input[type*=text],select").each(function(){
			if($(this).val() == ''){
				
				alert('Preencha todos os campos.');
				$(this).focus();
				campo = true;
				return false;
			}
		});
		if(campo){
			return false;
		}
		
		var dados = $(this).serialize();
		$.post('../actions/edita.saida.naopaga.php?tipo=<?=$tabela;?>',
			dados,
			function(retorno){
				if(retorno == '1'){
					alert('Erro ao realizar o update na base de dados.');
				}else{
					
					if($('#progressbar').html() == '' || $('#progressbar').html() == null){
						parent.window.location.reload();
						if (parent.window.hs) {
							var exp = parent.window.hs.getExpander();
							if (exp) { exp.close(); }
						}
					}else{
						<?php if(mysql_num_rows($qr_processo_assoc) == 0){?>						
						$('#anexo').uploadifyUpload();
						<?php } else { ?>
						$('#anexo_andamento').uploadifyUpload()
						<?php }?>
					}
				}
			}
			);
	});
	
	/*Upload*/
	$('#anexo').uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : '../../include/upload_financeiro.php',
				'folder'         : 'fotos',
				'buttonText'     : 'Enviar foto',
				'queueID'        : 'progressbar',
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
										parent.window.location.reload();
										if (parent.window.hs) {
											var exp = parent.window.hs.getExpander();
											if (exp) { exp.close(); }
										}
									},
				'onError'         : function(event,queueID,fileObj,errorObj){
										alert("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
									},
				'scriptData'      : {Ultimo_ID: '<?=$id_saida?>', tipo : '<?=$tabela?>'},
			 	'onSelect'        : function(){
										$('#progressbar').show();
										
									}	
	});	
	/*Upload*/
	
	
	/*Upload*/
	$('#anexo_andamento').uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : '../../gestao_juridica/processo_trabalhista/dados_trabalhador/action.upload_andamentos.php',
				'folder'         : '../../gestao_juridica/processo_trabalhista/dados_trabalhador/anexos',
				'buttonText'     : 'Enviar foto',
				'queueID'        : 'progressbar',
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
										parent.window.location.reload();
										if (parent.window.hs) {
											var exp = parent.window.hs.getExpander();
											if (exp) { exp.close(); }
										}
									},
				'onError'         : function(event,queueID,fileObj,errorObj){
										alert("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
									},
				'scriptData'      : { upload: 1,
									 id_andamento: '<?=$row_assoc['andamento_id']?>',
									 id_processo:  '<?=$row_assoc['proc_id']?>',
									},
			 	'onSelect'        : function(){
										$('#progressbar').show();
										
									}	
	});	
	/*Upload*/
	
	
	$('.date').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	
	
	
	$('li.excluir').click(function(){
		
		if(window.confirm('TEM CERTEZA QUE DESEJA DELETAR ESTE ANEXO?')){
			$.post('../actions/apaga.anexo.php',
			{id_saida_file : $(this).attr('value')},
			function(retono){
				if(retono == '1'){
					alert('Erro ao deletar anexo.');
					return false;
				}else{
					window.location.reload();
				}
			}
			);
		}
	});
	
	
	
		$('li.excluir_andamento').click(function(){
		
		if(window.confirm('TEM CERTEZA QUE DESEJA DELETAR ESTE ANEXO?')){
			$.post('../actions/excluir_anexo_andamento.php',
			{id_anexo_andamento : $(this).attr('value')},
			function(retono){
						
				if(retono == '1'){
					alert('Erro ao deletar anexo.');
					return false;
				}else{
					window.location.reload();
				}
			}
			);
		}
	});
	
	
	
	$('#regiao').change(function(){
		$('#projeto').attr('disabled',true);
		$.post('../actions/combo.projeto.json.php',
		{regiao :  $(this).val()},
		function(retorno){
			$('#projeto').html('<option value="" selected="selected">Selecione</option>');
			$('#banco').html('<option value="" selected="selected">Selecione</option>');
			$.each(retorno, function(i,valor){
				$('#projeto').append('<option value="'+valor.id_projeto+'">'+valor.id_projeto+' - '+valor.nome+'</option>');
			});
			$('#projeto').removeAttr('disabled');
		},
		'json'
		);
	});
	
	$('#projeto').change(function(){
		$('#banco').attr('disabled',true);
		$.post('../actions/combo.bancos.json.php',
		{ projeto: $(this).val() },
		function(retorno){
			$('#banco').html('<option value="" selected="selected">Selecione</option>');
			$.each(retorno, function(i,valor){
				$('#banco').append('<option value="'+valor.id_banco+'">'+valor.id_banco+' - '+valor.nome+'</option>');
			});
			$('#banco').removeAttr('disabled');
		},
		'json'
		);
	});
	
	
});
</script>
<style type="text/css">
body {
	background-color: #F2F2F2;
	margin: 0px;
	padding: 0px;
	text-align: center;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
#conteiner {
	width: 500px;
	margin-top: 10px;
	margin-right: auto;
	margin-bottom: 10px;
	margin-left: auto;
	padding: 10px;
	text-align: left;
	background-color: #FFF;
}
.anexos{
	width:330px;
	overflow:scroll;
}
.anexos ul{
	padding:0px;
	margin:0px;
	overflow:hidden;
}
.anexos ul li{
	float: left;
	list-style-type: none;
	margin: 0px 5px;
	border:solid #999 1px;
}
.anexos ul li.excluir {
	margin-left:-20px;
	padding:3px;
	color:#FFF;
	background-color:#D90000;
	font-weight:bold;
	cursor: pointer;
}

.anexos ul li.excluir_andamento {
	margin-left:-20px;
	padding:3px;
	color:#FFF;
	background-color:#D90000;
	font-weight:bold;
	cursor: pointer;
}



#progressbar{
	overflow:auto;
	height:120px;
	border:solid #CCC 1px;
	background-color:#FFF;
	width: 330px;
	display: none;
}
</style>
</head>
<body>
<div id="conteiner">
  <form id="form1" name="form1" method="post" action="" onsubmit="">
  <fieldset >
  <legend>Edi&ccedil;&atilde;o de <?=strtoupper($tabela);?></legend>
    <table width="372" border="0" align="center" cellpadding="0" cellspacing="5">
      <tr>
        <td colspan="3">Saida (<?=$id_saida?>) <?=$row_saida['nome'];?></td>
        </tr>
      <tr>
        <td>REGI&Atilde;O</td>
        <td>
          <select name="regiao" id="regiao">
          	<?php 
				$qr_regiao = mysql_query("SELECT * FROM regioes WHERE status = '1' AND status_reg = '1'");
				while($row_regiao = mysql_fetch_assoc($qr_regiao)){
					$selectd = ($row_saida['id_regiao'] == $row_regiao['id_regiao']) ? 'selected="selected"' : '';
					echo "<option $selectd value=\"$row_regiao[id_regiao]\">$row_regiao[id_regiao] - $row_regiao[regiao]</option>";
				}
			?>
          </select>
        </td>
        <td></td>
      </tr>
      <tr>
        <td>PROJETO</td>
        <td>
          <select name="projeto" id="projeto">
          <?php
          $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$row_saida[id_regiao]' AND status_reg = '1'");
		  while($row_projeto = mysql_fetch_assoc($qr_projeto)){
			$selectd = ($row_projeto['id_projeto'] == $row_saida['id_projeto']) ? 'selected="selected"' : '';
		  	echo "<option $selectd value=\"$row_projeto[id_projeto]\">$row_projeto[id_projeto] - $row_projeto[nome]</option>";
		  }
		  ?>
          </select></td>
        <td></td>
      </tr>
      <tr>
        <td width="44">BANCO</td>
        <td width="265">
        	<select name="banco" id="banco">
                <?php 
					$qr_banco = mysql_query("SELECT id_banco, nome FROM bancos WHERE id_regiao = '$row_saida[id_regiao]' AND status_reg = '1' AND interno = '1' AND id_banco != 79");
					while($row_bancos = mysql_fetch_array($qr_banco)):
						$selectd = ($row_bancos[0] == $row_saida['id_banco']) ? "selected=\"selected\"" : "";	
						echo "<option value=\"$row_bancos[0]\" $selectd>$row_bancos[0] - $row_bancos[1]</option>";
					endwhile;
				?>
            </select>
       	  <input type="hidden" name="id_saida" id="id_saida" value="<?=$id_saida?>" /></td>
        <td width="63"></td>
      </tr>
      <tr>
        <td>DATA DE VENCIMENTO</td>
        <td><input type="text" name="data" class="date" value="<?=implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])))?>" /></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>DESCRI&Ccedil;&Atilde;O</td>
        <td>
          <textarea name="descricao" id="descricao" cols="35" rows="5">
          <?= $row_saida['especifica']; ?>
          </textarea>
        </td>
        <td>&nbsp;</td>
      </tr>
	  <?php if(!isset($_GET['entrada'])):?>
      
      <tr>
        <td colspan="3" align="center">COMPROVANTE</td>
        </tr>
     
      <tr>
        <td colspan="3" align="center">
       		<div id="progressbar"></div>
        </td>
      </tr>
      <tr>
      <?php   if(mysql_num_rows($qr_processo_assoc) == 0) { ?>
        <td colspan="3" align="center"><input type="file" name="anexo" id="anexo" />
       <?php } else { ?> 
        <td colspan="3" align="center"><input type="file" name="anexo" id="anexo_andamento" />
       <?php } ?>
        </td>
      </tr>
   
      <tr>
        <td colspan="3">
        	<div class="anexos">
            	<ul>
                <?php
			
					/////MOSTRANDO ANEXOS DO PROCESSO JURÍDICO
					if(mysql_num_rows($qr_processo_assoc) != 0){
					
					$qr_anexo = mysql_query("SELECT * FROM proc_andamento_anexo WHERE andamento_id = '$row_assoc[andamento_id]' AND andamento_anexo_status = 1") or die(mysql_error());
					while($row_anexo = mysql_fetch_assoc($qr_anexo)):
					
					  echo '<li>';
					  
					  echo '<a href="ver_andamento.php?id_anexo='.$row_anexo['andamento_anexo_id'].'&id_andamento='.$row_assoc['andamento_id'].'" target="_blank"> 
					  		<img src="../../gestao_juridica/processo_trabalhista/dados_trabalhador/anexos/'.$row_anexo['andamento_anexo_nome'].'.'.$row_anexo['andamento_anexo_ext'].'" width="100" height="100" />
							</a>';
							
					echo '</li>';
						echo "<li value=\"$row_anexo[andamento_anexo_id]\" class=\"excluir_andamento\">X</li>";
					
					endwhile;
					}
					
			
				
				?>
                
                
            	<?php 					
					$qr_saida_files = mysql_query("SELECT * FROM saida_files WHERE id_saida = '$row_saida[id_saida]'");
					$num_saida_files = mysql_num_rows($qr_saida_files);
					if(!empty($num_saida_files)):
						while($row_saida_files = mysql_fetch_assoc($qr_saida_files)):
							$link_encryptado = encrypt('ID='.$row_saida_files['id_saida'].'&tipo=0');
							echo "<li>";
							if($row_saida_files['tipo_saida_file'] == '.pdf'){
								
								echo "
								<a href=\"comprovantes.php?$link_encryptado\" target=\"_blank\">
								<img src=\"../image/File-pdf-32.png\" border=\"0\" width=\"100\" height=\"100\" />
								</a>
								";
							}else{
							echo "<a href=\"comprovantes.php?$link_encryptado\" target=\"_blank\"><img src=\"../../classes/img.php?foto=../comprovantes/$row_saida_files[id_saida_file].$row_saida_files[id_saida]$row_saida_files[tipo_saida_file]&w=100&h=100\" /></a>";
							}
							echo "</li>";
							echo "<li value=\"$row_saida_files[id_saida_file]\" class=\"excluir\">X</li>";
						endwhile;
						
					endif;
				?>
                </ul>
            </div>

        </td>
      </tr>
      <?php endif;?>
      <tr>
        <td colspan="3" align="center"><input type="submit" value="Atualizar"  class="submit-go" /></td>
      </tr>
    </table>
    </fieldset>
  </form>
</div>
</body>
</html>