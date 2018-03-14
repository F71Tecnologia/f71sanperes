<?php
include('../../../include/restricoes.php');
include('../../../../conn.php');
include('../../../../funcoes.php');
include('../../../include/criptografia.php');

$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_funcionario);




$id_entregue = $_POST['entregue_id'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../../../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../../../jquery/jquery-1.4.2.min.js" ></script>
<script src="../../../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<link href="../../../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<link href="../../../../adm/css/estrutura.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../../../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../../../../jquery.uploadify-v2.1.4/swfobject.js"></script>
<script type="text/javascript">
$(function(){


$('#anexos').uploadify({
			'uploader'  : '../../../../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : '../action.upload.php',
			'cancelImg' : '../../../../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '../anexo_modelo7',
			'auto'      : true,
            'multi'     : true,
			'buttonText'  : 'Enviar',
            'fileExt'   : '*.png',
            'queueID'   : 'base_progresso_proposta',
			'scriptData': { 'upload' : true ,
                            'entregue_id' : $('#entregue_id').val(),                          
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
			url :  '../action.upload.php',
			data : { 'id_anexo' : id_anexo, 'deletar' : true },
			success : function (retorno){
				window.location.reload();
			},
			dataType : 'html'
		});
		
	});
	
	
$('.muda_ordem_anexo').live('change',function(){
var valor = $(this).val();
var index = 0;
var quant = $('input[class=muda_ordem_anexo][value='+valor+']').length;
if(quant > 1){
	alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
	$(this).val('0');
	return false;
}

var id = $(this).attr('alt');

$.ajax({
	url : '../action.upload.php',
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
		
		
		console.log(response)
		eval("var retorno = "+response);
		
		if(retorno.erro == false){
			$('<table style="float:left">\n\
			<tr>\n\
			<td>\n\
			<img src="'+retorno.src+'" height="250" width="200" />\n\
			</td>\n\
			</tr>\n\
			<tr>\n\
			<td>\n\
			<input type="text" name="muda_ordem" class="muda_ordem_anexo" alt="'+retorno.ID+'" value="0"/>\n\
			</td>\n\
			</tr>\n\
			').prependTo('#fildset_anexo');
		}else{
			alert('erro');
		}
	}
</script>
</head>

<body>
<div id="corpo">
	<div id="conteudo">
    
      <img src="../../../../imagens/logomaster<?php echo $row_func['id_master'];?>.gif"/>
    <h3>RELATÓRIO DA COMISSÃO DE AVALIAÇÃO (OSCIP E PARCEIRO)</h3>
    
    
    
    <form name="form" action="../../index.php?m=<?php echo $link_master; ?>" method="post" > 
	 <table cellpadding="0" cellspacing="1" class="secao" class="relacao" width="40%" style="border: 1px #CCC solid;"> 
   	<tr>
    	<td><h3>ENVIAR ANEXO(S)</h3></td>
    	<td align="left">
        	<input type="file" id="anexos" name="anexos"/>
            <div id="base_progresso_proposta"></div>
        	<input type="hidden" name="entregue_id" id="entregue_id" value="<?php echo $id_entregue; ?>"/>
        </td>
    </tr>
    
   </table>
   
   
     <div>
        <fieldset id="fildset_anexo">
         <legend>Anexo</legend>
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM rel_comissao_avaliacao WHERE entregue_id = '503'  AND anexo_status = '1' ORDER BY anexo_ordem ASC") or die(mysql_error());
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhum documento</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">         
            <img src="<?php echo '../anexo_modelo7/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" ></td>
            </tr>
            <tr>
            <td><input type="text" name="muda_ordem" class="muda_ordem_anexo" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/> </td>
            <td>
            	
                
                <a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../../../../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
            <?php endwhile;?>
            </fieldset>
        </div>
       <table  width="100%" style="margin:20px;"> 
       
          <tr>
          	<td align="center" style="text-align:center"> 
           
            <input type="submit" name="Submit" id="concluir" value="CONCLUIR" />
       		 
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
