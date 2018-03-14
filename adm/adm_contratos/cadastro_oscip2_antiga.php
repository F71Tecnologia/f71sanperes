<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');





$id_oscip=$_GET['id'];


if(isset($_POST['concluir'])){

header("Location:../../adm/adm_contratos/dados_oscip.php?m=$link_master");
}



$qr_oscip = mysql_query("SELECT * FROM  obrigacoes_oscip WHERE id_oscip ='$id_oscip' AND     status = '1' ");
			
$row_oscip=mysql_fetch_assoc($qr_oscip);


if(isset($_POST['enviar']))
{
	
	
}


if(isset($_REQUEST['ordem'])) {
   

    $id_anexo  = $_REQUEST['id_anexo'];
    $valor     = $_REQUEST['valor'];

    $qr_update = mysql_query("UPDATE obrigacoes_oscip_anexos SET anexo_ordem = '$valor' WHERE id_anexo = '$id_anexo' LIMIT 1");

    $json_resposta['erro'] = ($qr_update) ? false : true;

    echo json_encode($json_resposta);
    exit;
}
	
if(isset($_REQUEST['deletar'])){

	@mysql_query("UPDATE obrigacoes_oscip_anexos SET status = '0' WHERE id_anexo = '$_REQUEST[id_anexo]' LIMIT 1");
	exit;
}	

?>

<html>
<head>
<title>:: Intranet :: Cadastro OSCIP</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../js/ramon.js"></script>
<script type="text/javascript" src="../../jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>

<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">

<link href="../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 

<script type="text/javascript">

  hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
	
$(function(){
		$('.publicacao').change(function(){
            $('#publicacao').slideToggle('slow');
	});
	
	$('.documento').change(function(){
            $('#documento').slideToggle('slow');
	});
	
	
	
	function prencheItens(event, ID, fileObj, response, data){
			
		alert(response);
		eval("var retorno = "+response);
		if(retorno.erro == false){
			$('<table style="float:left">\n\
			<tr>\n\
			<td>\n\
			<img src="'+retorno.img+'" height="250" width="200" />\n\
			</td>\n\
			</tr>\n\
			<tr>\n\
			<td>\n\
			<input type="text" name="muda_ordem" class="muda_ordem_'+retorno.tipo+'" alt="'+retorno.ID+'" value="0"/>\n\
			</td>\n\
			</tr>\n\
			').prependTo('#fildset'+retorno.tipo);
		}else{
			alert('erro');
		}
	}
	
	
	$('.muda_ordem_publicacao').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_publicacao][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'cadastro_oscip2.php',
			data : { 'id_anexo' : id, 'valor' : valor, 'ordem' : true },
			dataType : 'json',
			success : function(resposta){
				if(resposta.erro == true){
					alert('erro...');
				}
			}
		});
	});
	
	$('.muda_ordem_documento').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_publicacao][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'cadastro_oscip2.php',
			data : { 'id_anexo' : id, 'valor' : valor, 'ordem' : true },
			dataType : 'json',
			success : function(resposta){
				if(resposta.erro == true){
					alert('erro...');
				}
			}
		});
	});
	
	function prencheItens(event, ID, fileObj, response, data){
		
	
			
		//alert(response);
		eval("var retorno = "+response);
		
		if(retorno.tipo == 1){
			var nome_tipo = 'publicacao';
		}else if(retorno.tipo == 2){
			var nome_tipo = 'documento';
		}
		
		if(retorno.erro == false){
			$('<table style="float:left">\n\
			<tr>\n\
			<td>\n\
			<img src="'+retorno.img+'" height="250" width="200" />\n\
			</td>\n\
			</tr>\n\
			<tr>\n\
			<td>\n\
			<input type="text" name="muda_ordem" class="muda_ordem_'+nome_tipo+'" alt="'+retorno.ID+'" value="0"/>\n\
			</td>\n\
			</tr>\n\
			').prependTo('#fildset_'+nome_tipo);
		}else{
			alert('erro');
		}
	}
	
	$("#anexo_publicacao").uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : 'upload.php',
				'buttonText'     : 'Enviar',
				'queueID'        : 'barra_progresso',
				'cancelImg'      : '../../uploadfy/cancel.png',
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : true,
				'fileDesc'       : 'gif jpg pdf',
				'fileExt'        : '*.gif;*.jpg;*.pdf;*.png;*.gif;*.jpeg;*.tiff;*.doc;',
				'scriptData': { 'upload2' : true ,
                            
                            'id_oscip' : $('#oscip').val(),
							'tipo': '1',
                                                      
                                    },
				     'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
		      	alert(errorObj.type + ' Error: ' + errorObj.info);
		    }		
			});
			
			
			
			
			$("#anexo_documento").uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : 'upload.php',
				'buttonText'     : 'Enviar',
				'queueID'        : 'barra_progresso2',
				'cancelImg'      : '../../uploadfy/cancel.png',
				
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : true,
				'fileDesc'       : 'gif jpg pdf',
				'fileExt'        : '*.gif;*.jpg;*.pdf;*.png;*.gif;*.jpeg;*.tiff;*.doc;',
				'scriptData': { 'upload2' : true ,
                            
                            'id_oscip' : $('#oscip').val(),
							'tipo': '2',
                                                   
                                    },
				'onComplete'  :  prencheItens,						 
									
									
				'onError'     : function (event,ID,fileObj,errorObj) {
				  alert(errorObj.type + ' Error: ' + errorObj.info);
				}	
			});
		
		$("#form1").validationEngine();	
		$('#data_publicacao').mask('99/99/9999');
		$('#tipo').validationEngine
		
		$('.btn_del').click(function(){
		var id_anexo = $(this).attr('rel');
		var este = $(this);
		$.ajax({
			url :  'cadastro_oscip2.php',
			data : { 'id_anexo' : id_anexo, 'deletar' : true },
			success : function (retorno){
				//alert(retorno);
				window.location.reload(); 
			},
			dataType : 'html'
		});
		
	});
		

});
</script>
</head>
<body>
<div id="corpo">






      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px; margin-top:40px">
               GERENCIAR ANEXOS:  <span class="projeto"><?=$row_oscip['tipo_oscip']?></span>
               
			

			     </span>
</h2>
             
           <p style="float:right;margin-top:40px;">
              <a href="../../adm/adm_contratos/dados_oscip.php?m=<?=$link_master?>"><span style="color:#900;">&laquo; Voltar</span></a>
           </p>
            <p style="float:right;">
           
             <?php    $pagina = $_SERVER['PHP_SELF']; ?>
             
         <span style="position:relative;  margin-right:10px;">   <a href="../../box_suporte.php?&regiao=<?php echo $regiao;?>&pagina=<?php echo $pagina;?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" ><img src="../../imagens/suporte.gif"  width="55" height="55"/></a></span>	
         
           </p>
           <div class="clear"></div>
      </div>

  
      <div>
      <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master; ?>" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
      
      
      <!-- tabela com os radios-->
          <table cellpadding="0" cellspacing="1" class="secao">
          <tr>
            <td class="secao_pai" colspan="6">DOCUMENTA&Ccedil;&Atilde;O</td>
          </tr>
          <tr>
            <td class="secao" width="30%">Anexo da publica&ccedil;&atilde;o:

			</td>
            <td colspan="5">
                <label><input type="radio" name="publicacao" class="publicacao" value="1" > Sim</label>
                <label><input type="radio" name="publicacao" class="publicacao" value="0" > N&atilde;o</label>
            </td>
          </tr>
          <tr>
            <td class="secao">Anexo do <?=$row_oscip['tipo_oscip']?>:</td>
            <td colspan="5">
                <label><input type="radio" name="documento" class="documento" value="1" > Sim</label>
                <label><input type="radio" name="documento" class="documento" value="0" > N&atilde;o</label>
            </td>
          </tr>
        </table>
        
        <!-- fim tabela com os radios-->
        
        <!-- exibe upload imagens publicacao-->
          <div id="publicacao" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td><b>Publicação:<b></td>
                </tr>
            	<tr>
                	<td><input type="file" name="anexo_publicacao" id="anexo_publicacao" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="barra_progresso"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="img_publicacao">
                            
                        </div>
                    <td>
                </tr>
            </table>
        </div>
         
          <!-- fim upload imagens publicacao-->
          
          <!-- exibe upload imagens documento-->
          <div id="documento" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td><span style="color:#C00;"><b><?=$row_oscip['tipo_oscip'];?>:</b></span></td>
                </tr>
            	<tr>
                	<td><input type="file" name="anexo_documento" id="anexo_documento" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="barra_progresso2"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="img_documento">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
         <div>
          <!-- fim upload imagens documento-->
         
         
         <fieldset id="fildset_publicacao">
         <legend><b>Publicação:<b></legend>
         
         <?php 
       $qr_anexos = mysql_query("SELECT * FROM  obrigacoes_oscip_anexos WHERE id_oscip = '$id_oscip' AND tipo_anexo = '1' AND   status = '1' ORDER BY anexo_ordem ASC");
			$cont=mysql_num_rows($qr_anexos);
			
			echo (empty($cont)) ? '<center>Nenhuma Publicação anexada</center>' : '';
			
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">  
            <?php
           if($row_anexos['extensao']=='pdf')
		   {
			   ?>
			   
			    <img src="../../imagens/Acrobat1.png" height="250" width="200"/>
			   
			   <?php }
			   else { ?>
                   
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_contratos/anexos_oscip/'.$row_anexos['id_anexo'].'.'.$row_anexos['extensao']?>" height="250" width="200">
            <?php } ?>
            
            </td>
            </tr>
            <tr>
            <td>
            	<input type="text" name="muda_ordem" class="muda_ordem_publicacao" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['id_anexo']?>"/>
            </td>
            <td>
            	<a href="#" rel="<?=$row_anexos['id_anexo']?>" class="btn_del" onClick="return false"><img src="../../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
             <?php endwhile;?>
        </fieldset>
        
        
			
            
			
         <!-- exibe upload imagens documento-->
          <div id="documento" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td>Adicionar documento:</td>
                </tr>
            	<tr>
                	<td><input type="file" name="anexo_documento" id="anexo_documento" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="barra_progresso2"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="img_documento">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
         <div>
          <!-- fim upload imagens documento-->
         
         
         <fieldset id="fildset_documento">
         <legend><span style="color:#900;font-weight:bold;"><?=$row_oscip['tipo_oscip'];?></span></legend>
         
         <?php 
       $qr_anexos = mysql_query("SELECT * FROM  obrigacoes_oscip_anexos WHERE id_oscip = '$id_oscip' AND tipo_anexo = '2' AND   status = '1' ORDER BY anexo_ordem ASC ");
			$cont=mysql_num_rows($qr_anexos);
			
			echo (empty($cont)) ? '<center>Nenhum documento anexado</center>' : '';
			
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">      
               
            <?php
           if($row_anexos['extensao']=='pdf')
		   {
			   ?>
			   	
			    <img src="../../imagens/Acrobat1.png" height="250" width="200"/><br><center>Arquivo PDF</center>
			   
			   <?php }
			   else { ?>
                   
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/adm/adm_contratos/anexos_oscip/'.$row_anexos['id_anexo'].'.'.$row_anexos['extensao']?>" height="250" width="200">
            <?php } ?></td>
            </tr>
            <tr>
            <td>
            	<input type="text" name="muda_ordem" class="muda_ordem_documento" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['id_anexo']?>"/>
            </td>
            <td>
            	<a href="#" rel="<?=$row_anexos['id_anexo']?>" class="btn_del" onClick="return false"><img src="../../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
             <?php endwhile;?>
        </fieldset>
        
        
      
      
      
      
      
	
      
      
      
      <br>
      
       <div align="center">
      <form>
    <input type="submit" name="concluir" value="Concluir"/>
    
    <input type="hidden" name="oscip" id="oscip" value="<?=$id_oscip?>"/>
        <input type="hidden" name="usuario" value="<?=$id_usuario?>" />
        <input type="hidden" name="master" value="<?=$id_master?>" />
        
        <input type="hidden" name="update" value="1" />
     </form>
     </div>
 <center> <div id="rodape"><?php include('include/rodape.php'); ?></div>
   </center>
</div>
</body>
</html>