<?php 
//include('include/restricoes.php');
include('../conn.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');
include('../adm/include/criptografia.php');


$id_subprojeto = $_GET['id'];
$id_regiao     = $_GET['regiao'];
$id_user       = $_COOKIE['logado'];

$id_projeto = @mysql_result(mysql_query("SELECT id_projeto FROM subprojeto WHERE id_subprojeto = '$id_subprojeto'"),0);


	
	if(isset($_REQUEST['upload'])){

	
	extract($_POST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = $_SERVER['DOCUMENT_ROOT'].'/intranet/projeto/anexos/';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	
	$qr_inser = mysql_query("INSERT INTO projeto_anexos (anexo_projeto, anexo_tipo, anexo_nome, anexo_extensao, anexo_data, anexo_autor, anexo_status)
							  VALUES ('$projeto', '$tipo', '$nome', '$extensao', NOW(), '$usuario', '1')");

	if($qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	if($extensao == 'pdf'){
		$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/img_menu_principal/pdf.png';
	}else {
		$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$nome.'.'.$extensao;
	}
	$json_resposta['ID']   = (int) @mysql_insert_id();
	
	
	switch($tipo) {
		
		case 5: $json_resposta['tipo'] = 'publicacao_rescisao';
		break;
		
		case 4: $json_resposta['tipo'] = 'termo';
		break;
		
	
		
		
		}


	echo json_encode($json_resposta);
	exit;
	
}

if(isset($_REQUEST['ordem'])) {
   

    $id_anexo  = $_REQUEST['id_anexo'];
    $valor     = $_REQUEST['valor'];

    $qr_update = mysql_query("UPDATE projeto_anexos SET anexo_ordem = '$valor' WHERE anexo_id = '$id_anexo' LIMIT 1");

    $json_resposta['erro'] = ($qr_update) ? false : true;

    echo json_encode($json_resposta);
    exit;
}


if(isset($_REQUEST['deletar'])){


	@mysql_query("UPDATE projeto_anexos SET anexo_status = '0' WHERE anexo_id = '$_REQUEST[id_anexo]' LIMIT 1");
	echo true;
	exit;
}





if(isset($_POST['update'])) {
	
	

	
	header("Location: ../adm/adm_projeto/index.php?m=$link_master");
	exit;	
	
}
/*
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}*/



?>
<html>
<head>
<title>:: Intranet :: Cadastro de Projeto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../favicon.ico" rel="shortcut icon">
<link href="../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../jquery/priceFormat.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js"></script>

<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/highslide.css" /> 

<script type="text/javascript">

  hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';

$(function(){
	
	
	$('.termo_rescisao').change(function(){
            $('#termo_rescisao').slideToggle('slow');
	});
	$('.publicacao_rescisao').change(function(){
            $('#publicacao_rescisao').slideToggle('slow');
	});

	$('.muda_ordem_termo').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_termo][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'desativar_projeto2.php',
			data : { 'id_anexo' : id, 'valor' : valor, 'ordem' : true },
			dataType : 'json',
			success : function(resposta){
				if(resposta.erro == true){
					alert('erro...');
				}
			}
		});
	});
	
	
	
	$('.muda_ordem_publicacao_rescisao').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_publicacao_rescisao][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'desativar_projeto2.php',
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
	
	


	
        $('#up_publicacao_rescisao').uploadify({
			'uploader'  : '../uploadfy/scripts/uploadify.swf',
			'script'    : 'desativar_projeto2.php',
			'cancelImg' : '../uploadfy/cancel.png',
			'folder'    : '/uploads',
			'auto'      : true,
			'multi'     : true,
			 'buttonText'  : 'Enviar',
			'fileExt'   : '*.jpg',
			'queueID'   : 'base_progresso_publicacao_rescisao',
			'scriptData': {  upload : true ,
                                        'regiao' : $('#regiao').val(),
                                        'projeto' : $('#projeto').val(),
                                        'usuario' : $('#usuario').val(),
                                        'tipo'    : '5'
                                    },
			'onComplete'  : prencheItens
	});
	
	
        $('#up_termo').uploadify({
			'uploader'  : '../uploadfy/scripts/uploadify.swf',
			'script'    : 'desativar_projeto2.php',
			'cancelImg' : '../uploadfy/cancel.png',
			'folder'    : '/uploads',
			'auto'      : true,
			'multi'     : true,
			 'buttonText'  : 'Enviar',
			'fileExt'   : '*.jpg',
			'queueID'   : 'base_progresso_termo',
			'scriptData': {  upload : true ,
                                        'regiao' : $('#regiao').val(),
                                        'projeto' : $('#projeto').val(),
                                        'usuario' : $('#usuario').val(),
                                        'tipo'    : '4'
                                    },
			'onComplete'  : prencheItens
	});
	
	
	
	
	// DELETANDO 
	
	$('.btn_del').click(function(){
		var id_anexo = $(this).attr('rel');
		var este = $(this);
		$.ajax({
			url : 'desativar_projeto2.php',
			data : { 'id_anexo' : id_anexo, 'deletar' : true },
			success : function (retorno){
			
				window.location.reload();
			},
			dataType : 'html'
		});
		
	});
	
	
	/*$('#concluir').click(function(){
		$(this).attr('disabled','disabled');
		$(this).attr('value','Concluindo...');
	});*/

});
</script>
<style type="text/css">
.base_paginas { overflow: hidden; }
.base_paginas ul { margin: 0px; padding: 0px; overflow: hidden; }
.base_paginas ul li { float: left; list-style: none; }
#base_progresso { height: 200px; overflow: auto; }
</style>
</head>
<body>
<p>&nbsp;</p>
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
    
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;">GERENCIAR ANEXOS <span class="projeto">SUBPROJETO</span></h2>
           
              <p style="float:right;margin-top:40px;">
               <a href="../adm/adm_projeto/index.php?m=<?=$link_master?>">&laquo; Voltar</a>
           </p>
           
           <p style="float:right;margin-left:15px;background-color:transparent;">
            
         		  <?php include('../reportar_erro.php'); ?> 
          
           </p>
           
           
        
           
           <div class="clear"></div>
      </div>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?=$link_master?>&id=<?php echo $id_subprojeto;?>&regiao=<?php echo $id_regiao;?>" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
          <table cellpadding="0" cellspacing="1" class="secao">
          <tr>
            <td width="35%" class="secao"><table cellpadding="0" cellspacing="1" class="secao">
              <tr>
                <td class="secao_pai" colspan="6">DOCUMENTA&Ccedil;&Atilde;O</td>
              </tr>
              
              
              <tr>
            <td class="secao">Termo de rescisao:</td>
            <td colspan="5">
                <label><input type="radio" name="publicacao_rescisao" class="publicacao_rescisao" value="1" <?php if($publicacao_rescisao == 1) { echo 'selected="selected"'; } ?>> Sim</label>
                <label><input type="radio" name="publicacao_rescisao" class="publicacao_rescisao" value="0" <?php if($publicacao_rescisao == 0) { echo 'selected="selected"'; } ?>> N&atilde;o</label>
            </td>
          </tr>
          
              <tr>
                <td width="27%" class="secao">Publicação do termo de rescisão:</td>
                <td width="73%" colspan="5"><label>
                  <input type="radio" name="termo_rescisao" class="termo_rescisao" value="1" <?php if($termo_rescisao == 1) { echo 'selected="selected"'; } ?>>
                  Sim</label>
                  <label>
                    <input type="radio" name="termo_rescisao" class="termo_rescisao" value="0" <?php if($termo_rescisao == 0) { echo 'selected="selected"'; } ?>>
                    N&atilde;o</label></td>
              </tr>
              
              
            </table></td>
          </tr>
          </table>
      
          <div id="termo_rescisao" class="upload" style="display:none;">
   	  <table>
                    <tr>
                	<td>Termo de rescisão:</td>
                </tr>
            	<tr>
                    <td><input type="file" name="up_termo" id="up_termo" /></td>
                </tr>
                <tr>
                    <td><div id="base_progresso_termo"></div>
                   
                    <div id="termo_rescisao"></div> </td>
                </tr>
                <tr>
                	<td>
                         <div id="base_termo">
                             <ul></ul>
                         </div>
                        <td>
                </tr>
            </table>
        </div>
        
      
          
      <div id="publicacao_rescisao" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td>Publicação do termo de rescisão:</td>
                </tr>
            	<tr>
                	<td><input type="file" name="up_publicacao_rescisao" id="up_publicacao_rescisao" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="base_progresso_publicacao_rescisao"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="base_publicacao_rescisao">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
        
        
      
        
  
        
        
        <div>
        <fieldset id="fildset_termo">
         <legend>Termo de rescisão:</legend>
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$id_projeto' AND anexo_tipo = '4' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhum termo de parceria anexado</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">         
            <?php
			
            if($row_anexos['anexo_extensao'] == 'pdf') {
			?>
			  <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/img_menu_principal/pdf.png';?>" height="250" width="200" /></td>
           	
		<?php	}else {
			?>
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" ></td>
           
           <?php }?> </tr>
            <tr>
            <td><input type="text" name="muda_ordem" class="muda_ordem_termo" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/> </td>
            <td>
            	
                
                <a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
            <?php endwhile;?>
            </fieldset>
            
        </div>
        
        
        <div>
        <fieldset id="fildset_publicacao_rescisao">
         <legend>Publicação do termo de rescisão:</legend>
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$id_projeto' AND anexo_tipo = '5' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhuma Publicação da rescisão anexado</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">    
            <?php
			
            if($row_anexos['anexo_extensao'] == 'pdf') {
			?>
			  <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/img_menu_principal/pdf.png';?>" height="250" width="200" /></td>
           	
		<?php	}else {
			?>
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" ></td>
           
           <?php }?>
            </tr>
            <tr>
            <td><input type="text" name="muda_ordem" class="muda_ordem_publicacao_rescisao" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/> </td>
            <td>
            	
                
                <a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
            <?php endwhile;?>
            </fieldset>
        </div>
        
        
        
        <p></p>
        <div align="center">
            <input type="submit" name="Submit" id="concluir" value="CONCLUIR" class="botao" />
        </div>
            <input type="hidden" name="projeto" id="projeto" value="<?=$id_subprojeto?>" />
            <input type="hidden" name="regiao"  id="regiao" value="<?=$id_regiao?>" />
            <input type="hidden" name="usuario" id="usuario" value="<?=$id_user?>" />
            <input type="hidden" name="update"  value="1" />
           
        </form>
    </td>
  </tr>
</table>
</div>
</body>
</html>