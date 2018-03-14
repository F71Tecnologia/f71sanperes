<?php
include('classes/suporte.class.php');

$regiao =  $_GET['regiao'];
$pagina =  $_GET['pagina'];
$usuario = $_COOKIE['logado'];

//echo $_SERVER['HTTP_HOST'].'suporte/arquivos/';


if(isset($_POST['btnOK'])){


$assunto = $_POST['assunto'];
$mesg 	 = $_POST['mensagem']; 
$foto    = $_POST['foto'];
$regiao	 = $_POST['regiao'];
$pagina =  $_POST['pagina'];
$arquivo    = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

		if($foto == '1') {
					  
					  if($arquivo['type'] != 'image/x-png' && 
						 $arquivo['type'] != 'image/pjpeg' && 
						 $arquivo['type'] != 'image/gif'   && 
						 $arquivo['type'] != 'image/jpeg') { ?>
						 
						 <center>
						 <b>Tipo de arquivo n&iuml;&iquest;&frac12;o permitido, os &iuml;&iquest;&frac12;nicos padr&iuml;&iquest;&frac12;es permitidos s&iuml;&iquest;&frac12;o <i>gif</i>, <i>jpg</i> ou <i>png</i>
						 
						 </center>
					
					  <?php exit();
					
							} else { // aqui o arquivo ï¿½ realente de imagem e vai ser carregado para o servidor
					
								list($nulo,$file_type) = explode('.', $arquivo['name']); 
							   
								if($file_type == 'gif') {
									$tipo_arquivo = '.gif'; 
								} elseif($file_type == 'jpg' or $arquivo['type'] == 'jpeg') {
									$tipo_arquivo = '.jpg'; 
								} elseif($file_type == 'png') { 
									$tipo_arquivo = '.png'; 
								}
								
								// Resolvendo o nome e para onde o arquivo serï¿½ movido
								$diretorio = 'suporte/arquivos/';
						
							}
							
							//	adiciona os dados no banco
							$suporte = new Suporte($regiao, '', $assunto, $mesg ,$usuario, $pagina, $tipo_arquivo);
							$suporte->inserir();
					
					
							$id_insert = mysql_insert_id();
							
					
					
							$nome_tmp     = 'suporte_'.$regiao.'_'.$id_insert.$tipo_arquivo;
							$nome_arquivo = $diretorio.$nome_tmp;
					  
							move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die ("Erro ao enviar o Arquivo: $nome_arquivo"); ?>
					
					<script>
					alert ("Obrigado por entrar em contato.... \n Acompanhe periodicamente seu pedido, logo lhe responderemos!");
					
					</script>
            
    <?php } else { // AQUI Nï¿½O TEM ARQUIVO EM ANEXO

            $suporte = new Suporte($regiao, '', $assunto, $mesg ,$usuario,  $pagina,'');
            $suporte->inserir();
            
             ?>

<script>
alert("Obrigado por entrar em contato.... \n Acompanhe periodicamente seu pedido, logo lhe responderemos!");


</script>

<?php }
}








	/*if(isset($_REQUEST['upload'])){

	
	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	
	$diretorio = $_SERVER['DOCUMENT_ROOT'].'/intranet/suporte/arquivos';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome);
	
	
	$qr_inser = mysql_query("INSERT INTO suporte (arquivo)
							  VALUES ('$extensao')");



	$id_inserido = mysql_insert_id();

	$nome	  = 'suporte_'.$regiao.'_'.$id_inserido.'.'.$extensao;
	
	if($qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}
	$json_resposta['id_inserido']  = $id_inserido; 
	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/suporte/arquivos/suporte_'.$regiao.'_'.$nome.'.'.$extensao;
	$json_resposta['ID']   = (int) @mysql_insert_id();
	

	echo json_encode($json_resposta);
	exit;
	
}
*/
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Untitled Document</title>
<script src="jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="uploadfy/scripts/jquery.uploadify.v2.1.0.min.js"></script>
<link href="uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script>
$(function(){
	
	$('#btnOK').click(function() {
		
		
		var assunto = $('#assunto').val();
		var msg = $('#StMensagem').val();
		var pagina = $('#pagina').val();
		var regiao = $('#regiao').val();
		var id_suporte = $('#id_suporte').val();
		
		if(assunto == '') {
		
			
			$('#aviso_assunto').html('Digite o assunto');
			
			
		} else if(msg == '')  {
					
				$('#aviso_assunto').html('');
				
			$('#aviso_menssagem').html('Digite a menssagem.');
				
		} else{
					$('#aviso_menssagem').html('');
					
					$.ajax({
						
						type:'GET',
						url: 'box_suporte.php',
						data: 'assunto='+assunto +'&mensg='+msg+'&pagina='+pagina+'&regiao='+regiao+'&id_suporte='+id_suporte+'&ok=ok',
						async:false,
						success:function(te) {
						
						
								
								alert('Solicitação concluída!'+te);
								
					var assunto = $('#assunto').val('');
					var msg = $('#StMensagem').val('');
							
						}
						
						
					
					});
		
		}
		
	}); //FIM btnOK
	
	
	/*   $('#arquivo').uploadify({
			uploader'  : 'uploadfy/scripts/uploadify.swf',
			script'    : 'box_suporte.php',
			cancelImg' : 'uploadfy/cancel.png',
			folder'    : '/uploads',
			auto'      : true,
			multi'     : true,
			 buttonText'  : 'Enviar',
			fileExt'   : '*.jpg,*.gif,*.png',
			queueID'   : 'base_progresso',
			scriptData': {  upload : true ,
                                        regiao' : $('#regiao').val(),
                                        projeto' : $('#projeto').val(),
                                        usuario' : $('#usuario').val(),
                                        tipo'    : '1'
                                    },
			onComplete'  : function(event, queueID, fileObj, response, data) {
				
								eval(response);
								
				
							 $('#aviso_menssagem').html(response.id_inserido);
						
			}
	});
	*/
	
	
});






</script>


</head>

<body>
<div id="concluido"></div>

<div id="box">
<form action="box_suporte.php" method="post" enctype="multipart/form-data" id="form1" name="form1" onSubmit="return validaForm()">
      <input type="hidden" value="<?=$regiao?>" name="regiao" />
      <input type="hidden" value="2" name="tela" />
		<table cellpadding="4" cellspacing="1">
        
        	<tr>
            	<td>
                	<img src="imagensmenu2/helpdesk.gif" style="margin-left:10px;"/> 
                </td>
                <td colspan="2" align="left">
                	<strong> SUPORTE</strong>
                </td>
             </tr>
           
           
            <tr>
              <td class="secao">Assunto:</td>
              <td colspan="2"><input name="assunto" class="linha" id="assunto" size="50" maxlength="60" 
              onChange="this.value=this.value.toUpperCase()"/></td>
              <td><div id="aviso_assunto"></div></td>
            </tr>
            <tr>
              <td class="secao">Mensagem:</td>
              <td>
                <textarea name="mensagem" cols="48" rows="10" class="linha" id="StMensagem" 
                onChange="this.value=this.value.toUpperCase()"></textarea>
              </td>
              <td colspan="2"><div id="aviso_menssagem"></div>
              </td>
            </tr>
            <tr>
            
            
              <td class="secao">Anexo:</td>
              <td colspan="3">&nbsp;&nbsp;
                <label class="linha">
                  <input name='foto' type='checkbox' id='foto' 
onClick="document.all.logomarca.style.display = (document.all.logomarca.style.display == 'none') ? '' : 'none' ;" value="1"/> <b>Sim</b>
                </label>
                <span style="display:none;" id="logomarca"><br>&nbsp;&nbsp;
                  selecione:
                  <input type="file" name="arquivo" id="arquivo" class="campotexto">
                  <font color='#666666' style="font-size:9px;">(.jpg, .png, .gif, .jpeg)</font>
                </span>
                <div id="base_progresso">
                </div>
                
                
                </td>
            </tr>
            <tr>
              <td class="secao" align="center" colspan="4">
                <input type="submit" value="Criar Chamado" name="btnOK"  />        
				
             </td>
           </tr>
        </table>
        <input name="regiao"  id="regiao" type="hidden" value="<?php echo $regiao;?>"/>
        <input name="pagina"  id="pagina" type="hidden" value="<?php echo $pagina;?>"/>
        <input name="id_suporte"  id="id_suporte" type="hidden" value=""/>
        
        
        
	  </form>
</div>
</body>
</html>
