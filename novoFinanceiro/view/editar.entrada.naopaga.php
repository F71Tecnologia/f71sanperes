<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$id_entrada = $_REQUEST['id_entrada'];
$regiao = $_REQUEST['regiao'];
define('edicao',empty($id_entrada));

$qr_entrada = mysql_query("SELECT * FROM entrada WHERE id_entrada = '$id_entrada'");
$row_entrada = mysql_fetch_assoc($qr_entrada);
$regiao = $row_entrada['id_regiao'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EDITAR ENTRADA</title>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script> 
<script type="text/javascript" src="../../js/formatavalor.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js"></script>
<script type="text/javascript" >
$(function(){
	
	var entrada = '<?=$id_entrada?>';
	
	$('.date').datepicker({
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
		changeYear: true
	});
	
	
	
	$('li.excluir').click(function(){
		var id_file = $(this).attr('value');
		if(!window.confirm('Tem certeza que deseja deletar este anexo?')){
			return false;
		}
		$.post('../actions/deletar.file.entrada.php',
				{ ID : id_file},
				function(retorno){
					if(retorno == '1'){
						alert('Erro ao atualizar a base de dados\n Por favor contate o setor de T.I');
					}else{
						window.location.reload();
					}
				});
	});
	
	$('#upload_anexo').uploadify({
		'uploader'       : '../../uploadfy/scripts/uploadify.swf',
		'script'         : '../actions/upload.entrada.php',
		'queueID'        : 'progressBar',
		'cancelImg'      : '../../uploadfy/cancel.png',
		'buttonImg'      : '../image/anexar.jpg',
		'width'          : 79,
		'height'		 : 80,
		'auto'           : false,
		'method'         : 'post',
		'multi'          : true,
		'fileDesc'       : 'Gif, Jpg , Png e pdf',
		'fileExt'        : '*.gif;*.jpg;*.png;*.pdf;',
		'onSelect'       :  function(){
			 					$('#progressBar').show();
							},
		'onAllComplete'  : function(){
								window.location.reload();
							},
		'onError'        : function (event,ID,fileObj,errorObj) {
		  					    alert(errorObj.type + ' Error: ' + errorObj.info);
						   }
	});
	
	$('#form').submit(function(){
		var dados = $(this).serialize();
		$.post('../actions/entrada.php',
		dados,
		function(retorno){
			
			alert(retorno);
			if($('#progressBar').html() != ""){
				$('#upload_anexo').uploadifySettings('scriptData',{id_entrada : retorno});
				$('#upload_anexo').uploadifyUpload();
			}else{
				window.location.reload();
			}
			
		});
	});
	
		
});
</script>
<link rel="stylesheet" type="text/css" href="../style/uploadify.css" />
<link href="../style/form.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../style/estrutura_views.css" />
<link rel="stylesheet" type="text/css" href="../style/uploadify.css" />
<link type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" />	
<style type="text/css">
#base_anexo ul {
	margin: 0px;
	padding: 0px;
	overflow: hidden;
}
#base_anexo ul li {
	padding: 0px;
	list-style-type: none;
	float: left;
	margin-top: 0px;
	margin-right: 3px;
	margin-bottom: 0px;
	margin-left: 3px;
}

#base_anexo li.excluir {
	background-color: #F00;
	height: 18px;
	width: 18px;
	text-align: center;
	color: #FFF;
	font-weight: bold;
	text-decoration: none;
	margin-left: -23px;
	z-index: 1000;
}
</style>
</head>
<body>
<div id="conteiner">
	<form method="post" name="form" id="form" onsubmit="return false;" >
   	  <fieldset>
       	<legend>Edi&ccedil;&atilde;o de saida.</legend>
            
<table width="90%" align="center">
<tr>
<td width="24%">PROJETO</td>
<td width="76%">
  <select name="projeto" id="projeto">
    <?php
    $qr_projeto = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_regiao = '$regiao'  AND status_reg = '1'");
    while($row_projeto = mysql_fetch_array($qr_projeto)):
		$selected = ($row_projeto[0] == $row_entrada['id_projeto']) ? 'selected="selected"' : '';
    	echo "<option $selected value=\"$row_projeto[0]\">$row_projeto[0] - $row_projeto[nome]</option>";
    endwhile;
    ?>
    </select>
</td>
</tr>
<tr>
<td>BANCO</td>
<td>
  <select name="banco" id="banco">
    <?php 
  $qr_bancos = mysql_query("SELECT id_banco, nome FROM bancos WHERE id_regiao = '$regiao' AND status_reg = '1'");
  while($row_bancos = mysql_fetch_array($qr_bancos)):
  	$selected = ($row_entrada['id_banco'] == $row_bancos[0]) ? 'seleted="selected"' : '';
  	echo "<option $selected value=\"$row_bancos[0]\">$row_bancos[0] - $row_bancos[1]</option>";
  endwhile;
  ?>
    </select>
</td>
</tr>
<tr>
<td>NOME</td>
<td>
  <input type="text" name="nome" id="nome" value="<?=(edicao) ? '' : $row_entrada['nome'];?>" /></td>
</tr>
<tr>
  <td height="23">DESCRI&Ccedil;&Atilde;O</td>
  <td><label for="descricao"></label>
    <input name="descricao" type="text" id="descricao" size="50" value="<?=(edicao) ? '' : $row_entrada['especifica'];?>" /></td>
  </tr>
<tr>
<td height="23">TIPO</td>
<td>
  <select name="tipo" id="tipo">
<?php 
$qr_tipo = mysql_query("SELECT id_entradasaida,nome FROM entradaesaida WHERE tipo='1' and grupo='5' ORDER BY nome");
while($row_tipo = mysql_fetch_array($qr_tipo)):
	$selected  = ($row_entrada['tipo'] == $row_tipo[0]) ? 'selected="selected"' : '';
	echo "<option $selected value=\"$row_tipo[0]\">$row_tipo[0] - $row_tipo[1]</option>";
endwhile;
?>
  </select>
</td>
</tr>
<tr>
<td>VALOR ADICIONAL</td>
<td>
  <input type="text" name="valor_adicional" id="valor_adicional" value="<?=(edicao) ?  '0,00' : number_format((float) str_replace(',','.',$row_entrada['adicional']),2,',','.');?>" onKeyDown="FormataValor(this,event,17,2)" /></td>
</tr>
<tr>
<td>VALOR</td>
<td>
  <input type="text" name="valor" id="valor" value="<?=(edicao)?  '0,00' : number_format((float) str_replace(',','.',$row_entrada['valor']),2,',','.');?>"  onKeyDown="FormataValor(this,event,17,2)"/></td>
</tr>
<tr>
<td>DATA PARA CREDITO</td>
<td><input name="data" type="text" class="date" id="data" value="<?=(edicao)? '' : implode('/',array_reverse(explode('-',$row_entrada['data_vencimento'])));?>" />
  <input type="hidden" name="id_regiao" id="id_regiao" value="<?=$regiao;?>" />
  <input type="hidden" name="edicao" id="edicao" value="<?php if($edicao){ echo '0'; }else{ echo '1';}?>" />
  <input type="hidden" name="entrada" id="entrada"  value="<?=(edicao) ?  '' : $id_entrada;?>"/></td>
</tr>
<?php # upload de anexos 
$qr_entrada_files = mysql_query("SELECT * FROM entrada_files WHERE id_entrada = '$row_entrada[id_entrada]' AND status = '1'");
?>
<tr>
<td colspan="2">
<div></div>
<div id="base_upload">
    <fieldset>
        <legend>Anexos</legend>
        <center><input type="file" name="upload_anexo" id="upload_anexo" /></center>
        <div id="progressBar"></div>
        <div id="base_anexo">
            <ul>
            	<?php while($row_entrada_files = mysql_fetch_assoc($qr_entrada_files)) : ?>
                    <li><img src="../comprovantes/entrada/<?=$row_entrada_files['id_files'].$row_entrada_files['tipo_files']?>" width="100" height="100"  /></li>
                    <li class="excluir" value="<?=$row_entrada_files['id_files']?>" style="cursor:pointer">X</li>
                <?php endwhile; ?>
            </ul>
        </div>
    </fieldset>
</div>
</td>
</tr>
<?php # FIM upload de anexos ?>
              <tr>
                <td>&nbsp;</td>
                <td><label for="valor"></label></td>
              </tr>
              <tr>
                <td colspan="2" align="center"><input name="button" type="submit" class="submit-go" id="button" value="CONCLUIR" /></td>
              </tr>
        </table>
    	</fieldset>
    </form>
</div>
</body>
</html>