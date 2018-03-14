<?php
include('../adm/include/restricoes.php');
include('../conn.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');
include('../adm/include/criptografia.php');

if(isset($_POST['concluir'])){
	
$regiao = $_POST['regiao'];
$id_compra = $_POST['id_compra'];



	header("Location: ver_cotacao.php?compra=$id_compra");
	
	//header("Location: ../gestaocompras.php?regiao=$regiao");

exit;
	
}

$id_compra = mysql_real_escape_string($_GET['compra']);
$regiao    =   mysql_real_escape_string($_GET['regiao']);
$array_fornecedor = array(1 =>'fornecedor1', 2 => 'fornecedor2', 3 => 'fornecedor3');

$qr_compra = mysql_query("SELECT * FROM compra2 WHERE id_compra = '$id_compra'");
$row_compra = mysql_fetch_assoc($qr_compra);

$nome_forne[1] = @mysql_result(mysql_query("SELECT nome FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor1]'"),0);
$nome_forne[2]= @mysql_result(mysql_query("SELECT nome FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor2]'"),0);
$nome_forne[3] = @mysql_result(mysql_query("SELECT nome FROM fornecedores WHERE id_fornecedor = '$row_compra[fornecedor3]'"),0);

?>
<html>
<head>
<title>:: Intranet :: Cadastro de Projeto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="favicon.ico" rel="shortcut icon">
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../jquery/priceFormat.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>


<script type="text/javascript" src="../js/highslide-with-html.js"></script> 

<script type="text/javascript">
$(function(){
	$('.fornecedor1').change(function(){
            $('#fornecedor1').slideToggle('slow');
	});
	
	$('.fornecedor2').change(function(){
            $('#fornecedor2').slideToggle('slow');
	});
	$('.fornecedor3').change(function(){
            $('#fornecedor3').slideToggle('slow');
	});

	$('.muda_ordem_fornecedor1').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_fornecedor1][value='+valor+']').length;
		if(quant > 1){
			alert('Já existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'upload.php',
			data : { 'id_anexo' : id, 'valor' : valor, 'ordem' : true },
			dataType : 'json',
			success : function(resposta){
				if(resposta.erro == true){
					alert('erro...');
				}
			}
		});
	});
	
	// DUPLICADO
	
	$('.muda_ordem_fornecedor2').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_fornecedor2][value='+valor+']').length;
		if(quant > 1){
			alert('Já existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'upload.php',
			data : { 'id_anexo' : id, 'valor' : valor, 'ordem' : true },
			dataType : 'json',
			success : function(resposta){
				if(resposta.erro == true){
					alert('erro...');
				}
			}
		});
	});
	
	$('.muda_ordem_fornecedor3').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_fornecedor3][value='+valor+']').length;
		if(quant > 1){
			alert('Já existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'upload.php',
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
		if(retorno.erro == false){
			$('<table style="float:left">\n\
			<tr>\n\
			<td>\n\
			<img src="'+retorno.src+'" height="250" width="200" />\n\
			</td>\n\
			</tr>\n\
			<tr>\n\
			<td>\n\
			<input type="text" name="muda_ordem" class="muda_ordem_'+retorno.tipo+'" alt="'+retorno.ID+'" value="0"/>\n\
			</td>\n\
			</tr>\n\
			').prependTo('#fildset_'+retorno.tipo);
		}else{
			alert('erro');
		}
	}
	
	$('#up_fornecedor1').uploadify({
			'uploader'  : '../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'upload.php',
			'cancelImg' : 'j../query.uploadify-v2.1.4/cancel.png',
			'folder'    : '/upload',
			'auto'      : true,
            'multi'     : true,
			 'buttonText'  : 'Enviar',
            'fileExt'   : '*.jpg',
            'queueID'   : 'base_progresso_fornecedor1',
			'scriptData': { 'upload' : true ,
                           'id_compra' : $('#id_compra').val(),                        
                            'tipo'    : '1'
                                    },
            'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
		      	alert(errorObj.type + ' Error: ' + errorObj.info);
		    }		
	});

        $('#up_fornecedor2').uploadify({
			'uploader'  : '../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'upload.php',
			'cancelImg' : '../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/uploads',
            'auto'      : true,
            'multi'     : true,
			'buttonText'  : 'Enviar',
            'fileExt'   : '*.jpg',
            'queueID'   : 'base_progresso_fornecedor2',
			'scriptData': { 'upload':true,
							'id_compra' : $('#id_compra').val(),                        
                            'tipo'    : '2'
			},
			'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
			      alert(errorObj.type + ' Error: ' + errorObj.info);
			    }		
	});
	
	
	
	$('#up_fornecedor3').uploadify({
			'uploader'  : '../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'upload.php',
			'cancelImg' : '../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/uploads',
			'auto'      : true,
            'multi'     : true,
            'fileExt'   : '*.jpg',
			 'buttonText'  : 'Enviar',
            'queueID'   : 'base_progresso_fornecedor3',
			'scriptData': { 'upload':true, 
							'id_compra' : $('#id_compra').val(),                        
                            'tipo'    : '3'
			},
            'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
		      	alert(errorObj.type + ' Error: ' + errorObj.info);
		    }		
	});
	
	// DELETANDO 
	
	$('.btn_del').click(function(){
		var id_anexo = $(this).attr('rel');
		var este = $(this);
		$.ajax({
			url :  'upload.php',
			data : { 'id_anexo' : id_anexo, 'deletar' : true },
			success : function (retorno){
				
				window.location.reload();
			},
			dataType : 'html'
		});
		
	});

});
</script>
<style type="text/css">
.base_paginas { overflow: hidden; }
.base_paginas ul { margin: 0px; padding: 0px; overflow: hidden; }
.base_paginas ul li { float: left; list-style: none; }
#base_progresso { height: 200px; overflow: auto; }
#topo_menu a {
	color:#900;
	width:190px; 
	heigth:auto; 
	display:block;
	background-color:#F4F4F4; 
	float:left;
	margin-left:1px;
	text-align:center;
}
#topo_menu a:hover {
	color:#000;
	
	display:block;
	background-color:#C0C0C0;
	
}
</style>
</head>
<body>

<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
    
      <div id="topo_menu" >    
           <span style="float:right;background-color:transparent; margin-left:0;">
          			  <?php include('../reportar_erro.php'); ?> 
           </span>           
           <h2 style="float:left; font-size:18px; border-bottom:2px solid #F3F3F3; ">GERENCIAR ANEXOS DE COMPRAS: </span></h2>
           <div class="clear"></div>
           
         
      </div>
      
        <form action="" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
        <table cellpadding="0" cellspacing="1" class="secao">
          <tr>
            <td class="secao_pai" colspan="6">OR�AMMENTO</td>
          </tr>
          <tr>
            <td class="secao" width="30%"> <?php echo $nome_forne[1]; ?>

			</td>
            <td colspan="5">
                <label><input type="radio" name="fornecedor1" class="fornecedor1" value="1" ?> Sim</label>
                <label><input type="radio" name="fornecedor1" class="fornecedor1" value="0" > N&atilde;o</label>
            </td>
          </tr>
          <tr>
            <td class="secao"><?php echo $nome_forne[2]; ?></td>
            <td colspan="5">
                <label><input type="radio" name="fornecedor2" class="fornecedor2" value="1" > Sim</label>
                <label><input type="radio" name="fornecedor2" class="fornecedor2" value="0"> N&atilde;o</label>
            </td>
          </tr>
          <tr>
            <td class="secao"><?php echo $nome_forne[3]; ?></td>
            <td colspan="5">
                <label><input type="radio" name="fornecedor3" class="fornecedor3" value="1">Sim
                <label><input type="radio" name="fornecedor3" class="fornecedor3" value="0"> N&atilde;o</label>
            </td>
          </tr>
        </table>
        
        
        <?php
       
		
		foreach($array_fornecedor as $chave => $campo) {
		?>
        
        
        <div id="fornecedor<?php echo $chave;?>" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td><?php echo $nome_forne[$chave];?></td>
                </tr>
            	<tr>
                	<td><input type="file" name="up_fornecedor<?php echo $chave;?>" id="up_fornecedor<?php echo $chave;?>" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="base_progresso_fornecedor<?php echo $chave;?>"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="base_fornecedor<?php echo $chave;?>">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
        <?php } ?>
      
         
        
		<?php foreach($array_fornecedor as $chave => $campo) { ?>
        
		         <div>
		         <fieldset id="fildset_fornecedor<?php echo $chave;?>">
		         <legend><?php echo $nome_forne[$chave];?></legend>        
		        	<hr>
		        	<?php 
					$qr_anexos = mysql_query("SELECT * FROM anexo_compra WHERE id_compra = '$id_compra' AND fornecedor = '$chave' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
					
					$num_rows = mysql_num_rows($qr_anexos);
					echo (empty($num_rows)) ? '<center>N�o existe  anexo</center>' : '';
					
					while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
		            <table style="float:left">
		            <tr>
		            <td colspan="2">     
		            <?php    
		           if($row_anexos['anexo_extensao'] == 'pdf' ) {
					?>
					  <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/img_menu_principal/pdf.png';?>" height="250" width="200" /></td>
		           	
				<?php	} else {
					?>
		            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/compras/anexo_compras/'.$row_anexos['anexo_id'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" >
		           <?php }?> </tr>
		            <tr>
		            <td>
		            	<input type="text" name="muda_ordem" class="muda_ordem_fornecedor<?php echo $chave;?>" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/>
		            </td>
		            <td>
		            	<a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../uploadfy/cancel.png" /></a>
		            </td>
		            </tr>
		            </table>
		            <?php endwhile;?>
		        </fieldset>
		        </div>
          <?php } ?>
          
          
        <p></p>
        <br>
        <div align="center">
            <input type="submit" name="concluir" value="CONCLUIR" class="botao" />
        </div>
        
          <input type="hidden" name="id_compra" id="id_compra" value="<?=$id_compra?>" />  
            <input type="hidden" name="regiao"  id="regiao" value="<?=$regiao?>" />
            <input type="hidden" name="usuario" id="usuario" value="<?=$id_user?>" />
            <input type="hidden" name="ordem" id="ordem" value="1" />
            <input type="hidden" name="update"  value="1" />
        </form>
    </td>
  </tr>
</table>
 <center> <div id="rodape"><?php include('../adm/include/rodape.php'); ?></div>
   </center>
</div>
</body>
</html>