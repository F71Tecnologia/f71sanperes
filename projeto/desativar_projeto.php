<?php
//desativa os projetos
include('include/restricoes.php');
include('../conn.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../funcoes.php');
include('../adm/include/criptografia.php');

$id_projeto=$_GET['id'];
$id_user    = $_COOKIE['logado'];
$id_regiao=$_GET['regiao'];
$qr_projeto=mysql_query("SELECT nome,regiao FROM projeto WHERE id_projeto='$id_projeto'");
$row_projeto=mysql_fetch_assoc($qr_projeto);




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




if(isset($_POST['update'])) {
	$id_projeto=$_POST['projeto'];
	$id_regiao = $_POST['regiao'];
	
	
	
	
	
	
mysql_query("UPDATE projeto SET status_reg='0' WHERE id_projeto='$id_projeto' LIMIT 1");	
	
$verifica_regiao = mysql_num_rows(mysql_query("SELECT * FROM projeto WHERE id_regiao='$id_regiao' AND status_reg='1'"));


if (empty($verifica_regiao)){
								mysql_query("UPDATE regioes SET status='0', status_reg='0' WHERE id_regiao='$id_regiao' LIMIT 1");
							}
	


header("Location:../adm/adm_projeto/index.php?m=$link_master");

	
}

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
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../jquery.uploadify-v2.1.4/swfobject.js"></script>

<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../js/highslide.css" /> 

<script type="text/javascript">


  hs.graphicsDir = '../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
$(function(){
	$('.termo_recisao').change(function(){
            $('#termo_recisao').slideToggle('slow');
	});
	
	$('.publicacao_termo').change(function(){
            $('#publicacao_termo').slideToggle('slow');
	});
	
	$('.muda_ordem_termo_recisao').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_termo_recisao][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'desativar_projeto.php',
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
	
	$('.muda_ordem_publicacao_termo').live('change',function(){
		var valor = $(this).val();
		var index = 0;
		var quant = $('input[class=muda_ordem_publicacao_termo][value='+valor+']').length;
		if(quant > 1){
			alert('JÃ¡ existe um pagina com este numero, por favor verifique!');
			$(this).val('0');
			return false;
		}
	   
		var id = $(this).attr('alt');
	   
		$.ajax({
			url : 'desativar_projeto.php',
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

	
	$('#up_termo_recisao').uploadify({
			'uploader'  : '../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'upload.php',
			'cancelImg' : '../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/uploads',
			'auto'      : true,
            'multi'     : true,
            'fileExt'   : '*.jpg',
            'queueID'   : 'base_progresso_termo_recisao',
			'scriptData': { 'upload2' : true ,
                            
                            'projeto' : $('#projeto').val(),
                            'usuario' : $('#usuario').val(),
                            'tipo'    : '4'
                                    },
            'onComplete'  : prencheItens,
			'onError'     : function (event,ID,fileObj,errorObj) {
		      	alert(errorObj.type + ' Error: ' + errorObj.info);
		    }		
	});
	
	$('#up_publicacao_termo').uploadify({
			'uploader'  : '../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'upload.php',
			'cancelImg' : '../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/uploads',
			'auto'      : true,
            'multi'     : true,
            'fileExt'   : '*.jpg',
            'queueID'   : 'base_progresso_publicacao_termo',
			'scriptData': { 'upload2' : true ,
                            
                            'projeto' : $('#projeto').val(),
                            'usuario' : $('#usuario').val(),
                            'tipo'    : '5'
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
			url :  'desativar_projeto.php',
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
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;">DESATIVAR PROJETO: <span class="projeto"><?=$id_projeto.' - '.$row_projeto['nome'].' '.'('.$row_projeto['regiao'].')'?></span></h2>
          
           <p style="float:right;margin-left:15px;background-color:transparent;">
            
            <?php include('../reportar_erro.php'); ?> 
         
           </p>
           <div class="clear"></div>
           
      
      </div>
      
      
       <div id="topo_menu" style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           
           <p style="float:left;margin-left:150px;">
               <a  href="../adm/adm_projeto/index.php?m=<?=$link_master?>"> Página Inicial</a>
               <a  href="edicao_projeto.php?m=<?=$link_master?>&id=<?=$id_projeto?>">Editar projeto</a>
               
               
              
			   
			  <a   href="cadastro_projeto_2.php?m=<?=$link_master?>&id=<?=$id_projeto?>"> Gerenciar Anexos do projeto </a>
			 
           </p>
           <div class="clear"></div>
      </div>

        <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master; ?>" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
        <table cellpadding="0" cellspacing="1" class="secao">
          <tr>
            <td class="secao_pai" colspan="2">DOCUMENTA&Ccedil;&Atilde;O</td>
          </tr>
          
            <td  class="secao">Termo de recisão:</td>
            <td>
                <label><input type="radio" name="termo_recisao" class="termo_recisao" value="1" > Sim</label>
                <label><input type="radio" name="termo_recisao" class="termo_recisao" value="0" > N&atilde;o</label>
            </td>
          </tr>
          <tr>
            <td class="secao">Publicação do Termo de recisão:</td>
            <td>
                <label><input type="radio" name="publicacao_termo" class="publicacao_termo" value="1" > Sim</label>
                <label><input type="radio" name="publicacao_termo" class="publicacao_termo" value="0" > N&atilde;o</label>
            </td>
          </tr>
        </table>
        
        <div id="termo_recisao" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td>Termo de recisão</td>
                </tr>
            	<tr>
                	<td><input type="file" name="up_termo_recisao" id="up_termo_recisao" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="base_progresso_termo_recisao"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="base_termo_recisao">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
         <div>
         <fieldset id="fildset_termo_recisao">
         <legend>Termo de recisão</legend>        
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$id_projeto' AND anexo_tipo = '4' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
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
            	<input type="text" name="muda_ordem" class="muda_ordem_termo_recisao" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/>
            </td>
            <td>
            	<a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../uploadfy/cancel.png" /></a>
            </td>
            </tr>
            </table>
            <?php endwhile;?>
        </fieldset>
        </div>
        
       
        <!-----publicação do termo de recisao-------------------------------------------------------------->
        
        <div id="publicacao_termo" class="upload" style="display:none;">
        	<table>
                 <tr>
                	<td>Programa de trabalho</td>
                </tr>
            	<tr>
                	<td><input type="file" name="up_publicacao_termo" id="up_publicacao_termo" /></td>
                </tr>
                <tr>
                    <td>
                        <div id="base_progresso_publicacao_termo"></div>
                    </td>
                </tr>
                <tr>
                	<td>
                        <div id="base_publicacao_termo">
                            <ul></ul>
                        </div>
                    <td>
                </tr>
            </table>
        </div>
         <div>
         <fieldset id="fildset_publicacao_termo">
         <legend>Publicação de termo de recisão</legend>        
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM projeto_anexos WHERE anexo_projeto = '$id_projeto' AND anexo_tipo = '5' AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhuma Publicação do termo de recisão anexada</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
            <td colspan="2">         
            <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/intranet/projeto/anexos/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" ></td>
            </tr>
            <tr>
            <td>
            	<input type="text" name="muda_ordem" class="muda_ordem_publicacao_termo" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/>
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