<?php 
include "../../conn.php";
include "../../funcoes.php";

$id_saida = $_REQUEST['id'];

$tabela = (isset($_GET['entrada'])) ? 'entrada' : 'saida';
$qr_saida = mysql_query("SELECT * FROM $tabela WHERE id_$tabela = '$id_saida' LIMIT 1");
$row_saida = mysql_fetch_assoc($qr_saida);

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
						$('#anexo').uploadifyUpload();
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
  <legend>Edi&ccedil;&atilde;o de saida</legend>
    <table width="372" border="0" align="center" cellpadding="0" cellspacing="5">
      <tr>
        <td colspan="3">Saida (<?=$id_saida?>) <?=$row_saida['nome'];?></td>
        </tr>
      <tr>
        <td width="44">BANCO</td>
        <td width="265">
        	<select name="banco">
                <?php 
					$qr_banco = mysql_query("SELECT id_banco, nome FROM bancos WHERE id_regiao = '$row_saida[id_regiao]' AND status_reg = '1' AND interno = '1'");
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
        <td colspan="3" align="center"><input type="file" name="anexo" id="anexo" /></td>
      </tr>
   
      <tr>
        <td colspan="3">
        	<div class="anexos">
            	<ul>
                
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