<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";


if(isset($_POST['concluir'])){
  header('Location: ver_trabalhador.php?id_processo='.$_POST['id_processo']);
}



$id_user   = $_COOKIE['logado'];
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);
$tipo_user         = $row_funcionario['tipo_usuario'];

$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);


$id_documento = $_GET['id'];
$id_clt 	  = $_GET['clt'];

$qr_trab = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$id_clt'");
$row_trab = mysql_fetch_assoc($qr_trab);

$nome_doc = mysql_result(mysql_query("SELECT descricao FROM upload WHERE id_upload = '$id_documento'"),0);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>
<script type="text/javascript">
$(function(){


$('#documento').uploadify({
			'uploader'       : '../uploadfy/scripts/uploadify.swf',
			'script'         : 'action.upload.php',
			'buttonText'     : 'Enviar anexo',
			'queueID'        : 'barra_processo',
			'cancelImg'      : '../uploadfy/cancel.png',
			'auto'           : true,
			'method'         : 'post',
			'multi'          : true,
			'fileExt'        : '*.gif;*.jpg;*.pdf;',
            'queueID'     	 : 'base_progresso_proposta',
			'scriptData'	 : { 'upload' : true ,
	                            'id_documento' : $('#id_documento').val(), 	
								'id_clt'		: $('#id_clt').val()						                     
	                           },
         'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
		      	alert(errorObj.type + ' Error: ' + errorObj.info);
		    }		
	});




$('.btn_del').click(function(){
	var id_anexo = $(this).attr('rel');
	var este = $(this);
	$.ajax({
		url :  'action.upload.php',
		data : { 'id_anexo' : id_anexo, 'deletar' : true },
		success : function (retorno){
			window.location.reload();
		},
		dataType : 'html'
	});
	
});
	
	
$('.muda_ordem_documento').live('change',function(){
var valor = $(this).val();
var index = 0;
var quant = $('input[class=muda_ordem_documentos][value='+valor+']').length;
if(quant > 1){
	alert('Já existe um pagina com este numero, por favor verifique!');
	$(this).val('0');
	return false;
}

var id = $(this).attr('alt');

$.ajax({
	url : 'action.upload.php',
	data : { 'id_anexo' : id, 'valor' : valor, 'ordem' : true },
	dataType : 'json',
	success : function(resposta){
		if(resposta.erro == true){
			alert('erro...');
		}
	}
});
});
	
	
	

});


function prencheItens(event, ID, fileObj, response, data){
		
		eval("var retorno = "+response);
		console.log(retorno.src);
		if(retorno.erro == false){
			$('<table style="float:left">\n\
			<tr>\n\
			<td>\n\
			<img src="'+retorno.src+'" height="250" width="200" />\n\
			</td>\n\
			</tr>\n\
			<tr>\n\
			<td>\n\
			<input type="text" name="muda_ordem" class="muda_ordem_documento" alt="'+retorno.ID+'" value="0"/>\n\
			</td>\n\
			</tr>\n\
			').prependTo('#fildset_documento');
		}else{
			alert('erro');
		}
	}
</script>

<style>
.add_documento,.add_responsavel{
cursor:pointer;	
}
</style>

<link rel="stylesheet" type="text/css" href="../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
    
    <img src="../imagens/logomaster<?php echo $id_master;?>.gif"/>
    <h3>
    	ANEXAR <span style="text-transform:uppercase;"> <?php echo $nome_doc;?></span>
    </h3>
   <form name="form" method="post"  enctype="multipart/form-data" action="ver_clt.php?clt=<?php echo $id_clt?>&pro=<?php echo $row_trab['id_projeto'] ?>&reg=<?php echo $row_trab['id_regiao'];?> ">
   
    
    <table cellpadding="0" cellspacing="1" class="secao" class="relacao" width="40%" style="border: 1px #CCC solid;"> 
     
   
   	<tr>
    	<td><h3>ENVIAR ANEXO(S)</h3></td>
    	<td align="left">
        	<input type="file" id="documento" name="documento"/>
            <div id="base_progresso_proposta"></div>
        	<input type="hidden" name="id_documento" id="id_documento" value="<?php echo $id_documento; ?>"/>
        </td>
    </tr>
    
   </table>
   
   
     <div>
        <fieldset id="fildset_documento">
         <legend>Anexo</legend>
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM documento_clt_anexo WHERE id_clt = '$id_clt' AND id_upload = '$id_documento' and anexo_status=1 ORDER BY ordem ASC") or die(mysql_error());
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhum anexo.</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">       
            <?php
            if($row_anexos['anexo_extensao'] == 'pdf'){
			?>
            
              <img src="../img_menu_principal/pdf.png" height="250" width="200">
			<?php			
			}else {
				?>
                  <img src="<?php echo 'documentos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" >
                <?php
			}
			
			?>  
          </td>
            </tr>
            <tr>
            <td><input type="text" name="muda_ordem" class="muda_ordem_documento" value="<?=$row_anexos['ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/> </td>
            <td>
            	
                
                <a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
            <?php endwhile;?>
            </fieldset>
        </div>
       <table  width="100%" style="margin:20px;"> 
       
          <tr>
          	<td align="center" style="text-align:center"> 
           <input type="hidden" name="id_clt" id="id_clt" value="<?php echo $id_clt; ?>"/>
           <input type="submit" name="concluir" id="concluir" value="CONCLUIR" />
       		 
             </td>
          </tr>
        </table>
    </form>            
   <div class="rodape2">
     
     <?php
     $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
          $master = mysql_fetch_assoc($qr_master); ?>
     <?=$master['razao']?>
     &nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS    
  </div>
  
 
          
   </div>
 </div>
</body>
</html>
