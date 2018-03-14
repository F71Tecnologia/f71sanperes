<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$id_entrada = $_REQUEST['id'];
$qr_entrada = mysql_query("SELECT * FROM entrada WHERE id_entrada = '$id_entrada'");
$row_entrada = mysql_fetch_assoc($qr_entrada);

$qr_notas = mysql_query("SELECT * FROM (notas INNER JOIN parceiros ON notas.id_parceiro = parceiros.parceiro_id) INNER JOIN notas_assoc ON notas_assoc.id_notas = notas.id_notas WHERE notas_assoc.id_entrada = '$id_entrada' LIMIT 1");
$row_notas = mysql_fetch_assoc($qr_notas);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Editar entrada</title>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js"></script>
<script type="text/javascript">
function fecha(){
	parent.window.location.reload();
	if (parent.window.hs) {
		var exp = parent.window.hs.getExpander();
		if (exp) {
			exp.close();
		}
	}
}
$(function(){
	
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
		'scriptData'	 : {id_entrada : '<?=$id_entrada?>'},
		'onSelect'       :  function(){
			 					$('#progressBar').show();
							},
		'onAllComplete'  : function(){
								fecha();
							},
		'onError'     : function (event,ID,fileObj,errorObj) {
		  alert(errorObj.type + ' Error: ' + errorObj.info);
		}
	});
	
	
	
	$('#form').submit(function(){
		
		$('.submit-go').attr('disabled',true);
		var dados = $(this).serialize();
		
		$.post('../actions/update_entrada.php',
			dados,
			function(retorno){
				/*console.log(retorno);
				alert('A equipe de TI esta realizando testes, por favor tente mais tarde. Obrigado.');
				return false;*/
        
				if(retorno == '1') { 
					alert('Erro ao atualizar a base de dados\n Por favor contate o setor de T.I');
				}else{
					if($('#progressBar').html() != ''){
						$('#upload_anexo').uploadifyUpload();
					}else{
						fecha();
					}
				}
			}
			);
	});
	
	$('li.excluir').click(function(){
		var id_file = $(this).attr('value');
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
	
	
	
	$('#tipo').change(function(){
		var tipo = $(this).val();
		
		if(tipo != 12 ) {
			$('.bloco_notas').hide();
			return false;
		}
		
		
		
		$('.bloco_notas').show();
		
	});
	

	
	$('#regiao_notas').change(function (){
		
		var valor = $(this).val();
		$('#parceiros_notas').html('<option value="">Carregando...</option>');
		
		$.ajax({
			url : '../../financeiro/actions/combo.entradas.php',
			data : { 'parceiros' : true, 'regiao' : valor },
			success :  function(registro){

				$('#parceiros_notas').html('');
				
				if(registro.ativos.length != 0){
					$('#parceiros_notas').append('<optgroup label="Ativos">');
					$.each(registro.ativos, function(i,campos){
						
						$('#parceiros_notas').append('<option value="'+campos.parceiro_id+'">'+campos.parceiro_nome+'</option>'); 
						
					});
					$('#parceiros_notas').append('</optgroup>');
				}
				
				if(registro.desativados.length != 0){
					$('#parceiros_notas').append('<optgroup label="Desativados">');
					$.each(registro.desativados, function(i,campos){
						
						$('#parceiros_notas').append('<option value="'+campos.parceiro_id+'">'+campos.parceiro_nome+'</option>'); 
						
					});
					$('#parceiros_notas').append('</optgroup>');
				}
				
				$('#parceiros_notas').change();
				
				
			},
			dataType : 'json'
		});
		
		
	});
	
	
	$('#parceiros_notas').change(function (){
	
		var id_parceiro = $(this).val();
		
		$('#notas').html('<div style="text-align: center;">Carregando....</div>');
		
		
		$.ajax({
			
			url : '../../financeiro/actions/combo.entradas.php',
			data : { 'notas' : true, 'id_parceiro' :  id_parceiro, 'id_notas' : '<?php echo $row_notas['id_notas'];  ?>', id_entrada : '<?php echo $_GET['id_entrada'] ?>'},
			dataType : 'json',
			success : function(registros){
				
				
				
				if(registros.erro == '1'){
					$('#notas').html('<center>Nenhuma nota cadastrada!</center>');
					return false;
				}
				
				var table = '<table>';
				
				table += '<tr>\n\
								<td colspan="4" align="center">Notas</td>\n\
							</tr>';
				
				table += '<tr>\n\
								<td></td>\n\
								<td>Nº da nota</td>\n\
								<td>data</td>\n\
								<td>Valor</td>\n\
								<td>Ver Nota</td>\n\
							</tr>';
							
				
				var alternateColor = 0;
				// LOOP DAS NOTAS NÃO ASSOCIADAS
				$.each(registros.nao_associada, function (i, valor){
					

					if(alternateColor==0){
						
						var classe = 'linha_um';
						alternateColor = 1;
					}else{
						var classe = 'linha_dois';
						alternateColor = 0;
					}
					
					if(valor.anexo != ''){
						var link = '<a target="_blank" href="'+valor.anexo+'">Ver</a>';
					}else{
						var link = '';
					}
					
					table += '<tr class="' + classe + '" >\n\
								<td><input type="checkbox" name="check_nota[]" value="' + valor.id_notas + '"/></td>\n\
								<td>' + valor.numero + '</td>\n\
								<td>' + valor.data_emissao + '</td>\n\
								<td>R$ ' + valor.valor + '</td>\n\
								<td align="center">'+link+'</td>\n\
							</tr>';
														
				});
				
				
				table += '</table>';
				
				// LOOP DAS NOTAS ASSOCIADAS
				if(registros.associada.length != 0){
					
					table +=  '<table>\n\
								<tr class="tr_title">\n\
									<td colspan="7"><center>Notas associados</center></td>\n\
								</tr>\n\
								<tr>\n\
									<td></td>\n\
									<td>Nº nota</td>\n\
									<td>data</td>\n\
									<td>valor</td>\n\
									<td>entradas</td>\n\
									<td>ver entradas</td>\n\
									<td>ver notas</td>\n\
								</tr>';
					
					
				
				
				$.each(registros.associada, function (i, valor){
					
					
					if(alternateColor==0){
						
						var classe = 'linha_um';
						alternateColor = 1;
					}else{
						var classe = 'linha_dois';
						alternateColor = 0;
					}
					
					if(valor.anexo != ''){
						var link = '<a target="_blank" href="'+valor.anexo+'">Ver</a>';
					}else{
						var link = '';
					}
					
					var ver_entrada = '<a href="'+valor.link_entrada+'" >ver Entradas</a>';
					
					
					if(valor.checked == 1){
						var marcado = 'checked="checked"';
					}else{
						var marcado = '';
					}

					table += '<tr class="'+alternateColor+'">\n\
									<td><input ' + marcado + ' type="checkbox" name="check_nota[]" value="' + valor.id_notas +'" /></td>\n\
									<td>'+valor.numero+'</td>\n\
									<td>'+valor.data_emissao+'</td>\n\
									<td>'+valor.valor+'</td>\n\
									<td>'+valor.total_entrada+'</td>\n\
									<td>'+ver_entrada+'</td>\n\
									<td>'+link+'</td>\n\
								</tr>';
					
				});
				
				}
				
				table += '</table>';
				
				$('#notas').html(table);
				return false;
				
			}
			
		});
		
		
	});
	$('#notas table tr').live('mouseout', function(){
		
		$(this).removeClass('linha_ativa');
		
	});
	
	$('#notas table tr').live('click',function(){
		
		//$(this).find('input').attr('checked', true);
		
	});
	
	$('#parceiros_notas').change();
	
	$('#subtipo').change(function(){
		if($(this).val() == 4){
			$('#campo_n_subtipo').hide().find('input[type=text]').val('');
		}else{
			$('#campo_n_subtipo').show();
		}
	});

});		
</script>
<link href="../style/form.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../style/estrutura_views.css" />
<link rel="stylesheet" type="text/css" href="../style/uploadify.css" />

<style type="text/css">
.tr_title {
	background-color:#333;
	color:#FFF;
}


#anexos li{
	float: left;
	height: 100px;
	width: 100px;
	margin-right: 3px;
	margin-left: 3px;
	list-style-type: none;
}
#anexos li.excluir {
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
#anexos ul {
	margin: 0px;
	padding: 0px;
}
#progressBar {
	height: 80px;
	width: 300px;
	margin-top: 0px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
	overflow: auto;
	display: none;
}
#anexos {
	overflow: hidden;
	width: 80%;
	margin-top: 0px;
	margin-right: auto;
	margin-bottom: 0px;
	margin-left: auto;
}
#Btn_anexo {
	text-align: center;
}

#notas table { width: 100%; }

.linha_um {
	background-color:#FAFAFA;
}

.linha_dois {
	background-color:#F3F3F3;
}

.linha_ativa {
	background-color: #adaaaa; 
	color: #FFFFFF;
}
tr.linha_um, tr.linha_dois {
	font-size	:	13px;
	padding		:	4px; 
	font-weight	:	normal;
}
</style>
</head>
<body>
<div id="conteiner">
<?php  echo ($row_notas['id_notas'].' - ' . $row_notas['numero']);?>
  <form action="" onsubmit="return false" method="post" id="form">
    <fieldset>
  <legend>Edi&ccedil;&atilde;o de entrada</legend>
        <table width="90%" align="center">
              <tr>
                <td width="15%">Nome</td>
                <td colspan="2"><label for="nome"></label>
                <input type="text" name="nome" id="nome" value="<?=$row_entrada['nome']?>" />
                <input type="hidden" name="id_entrada" id="id_entrada" value="<?=$row_entrada['id_entrada']?>" /></td>
              </tr>
              
              
            
              <tr class="bloco_notas" <?php if($row_entrada['tipo'] != 12){?>style="display: none;"<?php } ?>>
              	<td>Parceiro nota</td>
              	<td colspan="2">
              		<select name="parceiros_notas" id="parceiros_notas">
                    	<?php 
						$qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE id_regiao = '$row_notas[id_regiao]'");
						while($row_parceiros = mysql_fetch_assoc($qr_parceiros)):
						?>
              			<option value="<?=$row_parceiros['parceiro_id']?>"><?=$row_parceiros['parceiro_id'].' - '.$row_parceiros['parceiro_nome']?></option>
                        <?php endwhile;?>
              		</select>
              	</td>
              </tr>
              <tr>
              	<td>Valor:</td>
                <td colspan="2"><input type="text" name="valor" value="<?php echo $row_entrada['valor']; ?>" /></td>
              </tr>
              <tr>
              	<td>Data de vencimento:</td> 
                <td colspan="2"><input type="text" name="data_vencimento" value="<?php echo implode('/',array_reverse(explode('-',$row_entrada['data_vencimento'])));?>" /></td>
              </tr>

              <tr>
                <td>Descri&ccedil;&atilde;o</td>
                <td width="68%">
                    <textarea name="descricao" id="descricao" cols="30" rows="5"><?=stripslashes($row_entrada['especifica'])?></textarea>
               	</td>
                <td width="17%">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="3">
                 <div id="Btn_anexo"><input type="file" name="upload_anexo" id="upload_anexo" /></div>
                 <div id="progressBar"></div>
                  <div id="anexos">
                  <ul>
                  	<?php
					$qr_entrada_files = mysql_query("SELECT * FROM entrada_files WHERE id_entrada = '$row_entrada[id_entrada]' AND status = '1'");
					while($row_entrada_files = mysql_fetch_assoc($qr_entrada_files)):
					?>
                    <li>
                   	
                   	  <img src="../comprovantes/entrada/<?=$row_entrada_files['id_files'].$row_entrada_files['tipo_files']?>" width="100" height="100"/>
     
                    </li>
                     <li class="excluir" value="<?=$row_entrada_files['id_files']?>" style="cursor:pointer">X</li>
                    <?php endwhile;?>
                    </ul>
                  </div>
                </td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td>&nbsp;</td>
          </tr>
              <tr>
                <td align="center">&nbsp;</td>
                <td align="center"><input name="button" type="submit" class="submit-go" id="button" value="Concluir"   /></td>
                <td>&nbsp;</td>
              </tr>
        </table>
    	</fieldset>
  </form>
</div>
</body>
</html>