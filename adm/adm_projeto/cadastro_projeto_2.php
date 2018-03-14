<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');


if(isset($_REQUEST['upload'])){
	
	include('../conn.php');
	
	extract($_REQUEST);
	
	$arquivo  = $_FILES['Filedata']['name'];
	$nome	  = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
	$extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
	
	$diretorio = $_SERVER['DOCUMENT_ROOT'].'/intranet/pasta_teste';
	
	$up = move_uploaded_file($_FILES['Filedata']['tmp_name'],$diretorio.'/'.$nome.'.'.$extensao);
	
	$qr_inser = mysql_query("INSERT INTO projeto_anexos (anexo_projeto, anexo_tipo, anexo_nome, anexo_extensao, anexo_data, anexo_autor, anexo_status)
							  VALUES ('$projeto', '$tipo', '$nome', '$extensao', NOW(), '$usuario', '1')");

	if($qr_inser && $up) {
		$json_resposta['erro'] = false;
	} else {
		$json_resposta['erro'] = true;
	}

	$json_resposta['src']  = 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$nome.'.'.$extensao;
	$json_resposta['ID']   = (int) @mysql_insert_id();
	$json_resposta['tipo'] = ($tipo == '1') ? 'proposta' : 'termo';

	echo json_encode($json_resposta);
	exit;
	
}

if(isset($_REQUEST['deletar'])){

	@mysql_query("UPDATE projeto_anexos SET anexo_status = '0' WHERE anexo_id = '$_REQUEST[id_anexo]' LIMIT 1");
	exit;
}

if(isset($_REQUEST['ordem'])and !isset($_POST['update'])) {
   

    $id_anexo  = $_REQUEST['id_anexo'];
    $valor     = $_REQUEST['valor'];

    $qr_update = mysql_query("UPDATE projeto_anexos SET anexo_ordem = '$valor' WHERE anexo_id = '$id_anexo' LIMIT 1");

    $json_resposta['erro'] = ($qr_update) ? false : true;

    echo json_encode($json_resposta);
    exit;
}


if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}



if(isset($_POST['update'])) {
	
	extract($_POST);
	$qr_projeto=mysql_query("SELECT status_reg FROM projeto WHERE id_projeto='$projeto' ");
	$row_projeto=mysql_fetch_assoc($qr_projeto);
	if($row_projeto['status_reg']==0){
				
	mysql_query("UPDATE projeto SET proposta_parceria = '$proposta_parceria', termo_parceria = '$termo_parceria' WHERE id_projeto = '$projeto' LIMIT 1") or die(mysql_error());						  
		
		header("Location:desativar_projeto.php?m=$link_master&id=$projeto");
		exit;
		}
		
		
	mysql_query("UPDATE projeto SET proposta_parceria = '$proposta_parceria', termo_parceria = '$termo_parceria' WHERE id_projeto = '$projeto' LIMIT 1") or die(mysql_error());						  
	header("Location: ../adm/adm_projeto/index.php?m=$link_master");
	exit;	
	
}

$id_projeto = $_GET['projeto'];
$id_regiao  = $_GET['regiao'];
$id_user    = $_COOKIE['logado'];
?>
<html>
<head>
<title>:: Intranet :: Cadastro de Projeto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../favicon.ico" rel="shortcut icon">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../jquery/priceFormat.js"></script>
<script type="text/javascript" src="../../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../../jquery.uploadify-v2.1.4/swfobject.js"></script>

<script type="text/javascript">
$(function(){
	$('.proposta_parceria').change(function(){
            $('#proposta_parceria').slideToggle('slow');
	});
	
	$('.termo_parceria').change(function(){
            $('#termo_parceria').slideToggle('slow');
	});
	$('.programa_trabalho').change(function(){
            $('#programa_trabalho').slideToggle('slow');
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
			url : 'cadastro_projeto_2.php',
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
	
	$('.muda_ordem_proposta').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_proposta][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'cadastro_projeto_2.php',
			data : { 'id_anexo' : id, 'valor' : valor, 'ordem' : true },
			dataType : 'json',
			success : function(resposta){
				if(resposta.erro == true){
					alert('erro...');
				}
			}
		});
	});
	
	$('.muda_ordem_programa_trabalho').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_programa_trabalho][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'cadastro_projeto_2.php',
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
	
	$('#up_parceria').uploadify({
			'uploader'  : '../../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'subprojeto/renovacao2.php',
			'cancelImg' : '../../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/uploads',
			'auto'      : true,
            'multi'     : true,
			 'buttonText'  : 'Enviar',
            'fileExt'   : '*.jpg',
            'queueID'   : 'base_progresso_proposta',
			'scriptData': { 'upload2' : true ,
                            'regiao' : $('#regiao').val(),
                            'projeto' : $('#projeto').val(),
                            'usuario' : $('#usuario').val(),
                            'tipo'    : '1'
                                    },
            'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
		      	alert(errorObj.type + ' Error: ' + errorObj.info);
		    }		
	});

        $('#up_termo').uploadify({
			'uploader'  : '../../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'subprojeto/renovacao2.php',
			'cancelImg' : '../../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/uploads',
            'auto'      : true,
            'multi'     : true,
			'buttonText'  : 'Enviar',
            'fileExt'   : '*.jpg',
            'queueID'   : 'base_progresso_termo',
			'scriptData': {  upload2 : true ,
                                        'regiao' : $('#regiao').val(),
                                        'projeto' : $('#projeto').val(),
                                        'usuario' : $('#usuario').val(),
                                        'tipo'    : '2'
                                    },
			'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
			      alert(errorObj.type + ' Error: ' + errorObj.info);
			    }		
	});
	
	
	
	$('#up_programa_trabalho').uploadify({
			'uploader'  : '../../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'subprojeto/renovacao2.php',
			'cancelImg' : '../../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/uploads',
			'auto'      : true,
            'multi'     : true,
            'fileExt'   : '*.jpg',
			 'buttonText'  : 'Enviar',
            'queueID'   : 'base_progresso_programa_trabalho',
			'scriptData': { 'upload2' : true ,
                            'regiao' : $('#regiao').val(),
                            'projeto' : $('#projeto').val(),
                            'usuario' : $('#usuario').val(),
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
			url :  'cadastro_projeto_2.php',
			data : { 'id_anexo' : id_anexo, 'deletar' : true },
			success : function (retorno){
				alert(retorno);
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
    <?php
	//Pega o nome e a regiao do projeto
	
	$qr_projeto=mysql_query("SELECT nome,regiao,status_reg FROM projeto WHERE id_projeto='$id_projeto'");
$row_projeto=mysql_fetch_assoc($qr_projeto);

	?>
    
      <div id="topo_menu" style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;">GERENCIAR ANEXOS DO PROJETO: <span class="projeto"><?=$id_projeto.' - '.$row_projeto['nome'].' '.' ('.$row_projeto['regiao'].')'?></span></h2>
           
           <div class="clear"></div>
      </div>
      
      <div id="topo_menu" style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           
           <p style="float:left;margin-left:150px;">
               <a  href="../adm/adm_projeto/index.php?m=<?=$link_master?>"> Página Inicial</a>
               <a  href="edicao_projeto.php?m=<?=$link_master?>&id=<?=$id_projeto?>">Editar projeto</a>
               
               
               <?php if($row_projeto['status_reg']==0){
			   
			   echo  '<a   href="desativar_projeto.php?m='.$link_master.'&id='.$id_projeto.'"> Gerenciar Anexos da rescisão </a>';
			   }
				   ?>
           </p>
           <div class="clear"></div>
      </div>

        <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master; ?>" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
        <table cellpadding="0" cellspacing="1" class="secao">
          <tr>
            <td class="secao_pai" colspan="6">DOCUMENTA&Ccedil;&Atilde;O</td>
          </tr>
          <tr>
            <td class="secao" width="30%">Proposta de parceria:

			</td>
            <td colspan="5">
                <label><input type="radio" name="proposta_parceria" class="proposta_parceria" value="1" <?php if($proposta_parceria == 1) { echo 'selected="selected"'; } ?>> Sim</label>
                <label><input type="radio" name="proposta_parceria" class="proposta_parceria" value="0" <?php if($proposta_parceria == 0) { echo 'selected="selected"'; } ?>> N&atilde;o</label>
            </td>
          </tr>
          <tr>
            <td class="secao">Termo de parceria:</td>
            <td colspan="5">
                <label><input type="radio" name="termo_parceria" class="termo_parceria" value="1" <?php if($termo_parceria == 1) { echo 'selected="selected"'; } ?>> Sim</label>
                <label><input type="radio" name="termo_parceria" class="termo_parceria" value="0" <?php if($termo_parceria == 0) { echo 'selected="selected"'; } ?>> N&atilde;o</label>
            </td>
          </tr>
          <tr>
            <td class="secao">Plano de trabalho:</td>
            <td colspan="5">
                <label><input type="radio" name="programa_trabalho" class="programa_trabalho" value="1" <?php if($programa_trabalho == 1) { echo 'selected="selected"'; } ?>> Sim</label>
                <label><input type="radio" name="programa_trabalho" class="programa_trabalho" value="0" <?php if($programa_trabalho == 0) { echo 'selected="selected"'; } ?>> N&atilde;o</label>
            </td>
          </tr>
        </table>
        
        <div id="proposta_parceria" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td>Proposta de parceria</td>
                </tr>
            	<tr>
                	<td><input type="file" name="up_parceria" id="up_parceria" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="base_progresso_proposta"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="base_proposta">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
         <div>
         <fieldset id="fildset_proposta">
         <legend>Proposta de parceria</legend>        
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$id_projeto' AND anexo_tipo = '1' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhuma Proposta de parceria anexado</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">         
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" ></td>
            </tr>
            <tr>
            <td>
            	<input type="text" name="muda_ordem" class="muda_ordem_proposta" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/>
            </td>
            <td>
            	<a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
            <?php endwhile;?>
        </fieldset>
        </div>
        
         <div id="termo_parceria" class="upload" style="display:none;">
        	<table>
                    <tr>
                	<td>Termo de parceria</td>
                </tr>
            	<tr>
                    <td><input type="file" name="up_termo" id="up_termo" /></td>
                </tr>
                <tr>
                    <td><div id="base_progresso_termo"></div></td>
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
        <div>
        
        
        <fieldset id="fildset_termo">
         <legend>Termo de parceria</legend>
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$id_projeto' AND anexo_tipo = '2' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhum termo de parceria anexado</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">         
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" ></td>
            </tr>
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
        <!-----programa de trabalho-------------------------------------------------------------->
        
        <div id="programa_trabalho" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td>Programa de trabalho</td>
                </tr>
            	<tr>
                	<td><input type="file" name="up_programa_trabalho" id="up_programa_trabalho" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="base_progresso_programa_trabalho"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="base_programa_trabalho">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
         <div>
         <fieldset id="fildset_programa_trabalho">
         <legend>Programa de trabalho</legend>        
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$id_projeto' AND anexo_tipo = '3' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhum Programa de trabalho anexado</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">         
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" ></td>
            </tr>
            <tr>
            <td>
            	<input type="text" name="muda_ordem" class="muda_ordem_programa_trabalho" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/>
            </td>
            <td>
            	<a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
            <?php endwhile;?>
        </fieldset>
        </div>
        
        
        <p></p>
        <br>
        <div align="center">
            <input type="submit" name="Submit" value="CONCLUIR" class="botao" />
        </div>
            <input type="hidden" name="projeto" id="projeto" value="<?=$id_projeto?>" />
            <input type="hidden" name="regiao"  id="regiao" value="<?=$id_regiao?>" />
            <input type="hidden" name="usuario" id="usuario" value="<?=$id_user?>" />
            <input type="hidden" name="ordem" id="ordem" value="1" />
            <input type="hidden" name="update"  value="1" />
        </form>
    </td>
  </tr>
</table>
 <center> <div id="rodape"><?php include('include/rodape.php'); ?></div>
   </center>
</div>
</body>
</html>