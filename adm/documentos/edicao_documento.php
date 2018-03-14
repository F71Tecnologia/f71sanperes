<?php 
include('../include/restricoes.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');
include('../../conn.php');

$documento_id = $_GET['id'];
$qr_documento = mysql_query("SELECT * FROM modelo_documentos WHERE documento_id = '$documento_id' AND documento_status = '1'");
$row_doc = mysql_fetch_assoc($qr_documento);

if(isset($_POST['concluir'])){
	
	$nome = trim($_POST['nome']);
	$descricao = trim($_POST['descricao']);
	
	$inserir = mysql_query("UPDATE modelo_documentos SET id_master = '$Master', documento_nome ='$nome', documento_descricao = '$descricao',  documento_data_atualizacao = NOW(), documento_usuario_atualizacao = $_COOKIE[logado] WHERE documento_id = '$_POST[documento_id]' LIMIT 1") or die(mysql_error());
															
header("Location: index.php?m=$link_master");										
	

}

	
?>
<html>
<head>
<title>:: Intranet :: Cadastro de Modelo de Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../../favicon.ico">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../jquery/priceFormat.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js"></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
$(function(){
	
	$('#form1').validationEngine();
	
	
	
        $('#up_documento').uploadify({
			'uploader'  : '../../uploadfy/scripts/uploadify.swf',
			'script'    : 'upload.php',
			'cancelImg' : '../../uploadfy/cancel.png',
			'folder'    : '/uploads',
			'buttonText'	: 'Enviar',
			'auto'      : true,
			'multi'     : false,
			'fileExt'   : '*.doc,*.pdf',
			'queueID'   : 'base_progresso_documento',
			'scriptData': {  upload : true,
							 'documento_id' : $('#documento_id').val() },
			'onComplete'  : function(event, ID, fileObj, response, data){
							eval('var resposta = '+ response);
							$('#base_documento').html('<img src="'+resposta.src+'" width="100" height="100" />');
							
							}
	});
	
	
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
           <h2 style="float:left; font-size:18px;margin-top:40px;">EDITAR <span class="projeto">MODELO DE DOCUMENTO</span></h2>
           <p style="float:right;margin-top:40px;">
               <a href="../../adm/documentos/index.php?m=<?=$link_master?>">&laquo; Voltar</a>
           </p>
            <p style="float:right;margin-top:15px;">
             <?php include('../../reportar_erro.php'); ?>
           </p>
           <div class="clear"></div>
      </div>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?=$link_master?>&id=<?php echo $id_subprojeto;?>&regiao=<?php echo $id_regiao;?>" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
       <table cellpadding="0" cellspacing="1" class="secao">
              <tr>
                <td class="secao_pai" colspan="6">DOCUMENTO</td>
              </tr>
             <tr>
                  <td class="secao">Nome:</td>
                  <td><input name="nome" type="text" id="nome" class="validate[required]" size="30" value="<?php echo $row_doc['documento_nome'];?>"/></td>         
         	 </tr> 
              <tr>
                  <td class="secao">Descrição:</td>
                  <td><textarea  name="descricao" id="descricao" cols="30"  rows="5" ><?php echo $row_doc['documento_descricao'];?></textarea> </td>         
         	 </tr>  
              
              
           <tr>
            <td valign="top" align="center" class="secao" width="20%">Enviar documento:</td>
         
          	
              <td>
              <input type="file" name="up_documento" id="up_documento" />
              
                <div id="base_progresso_documento"></div>
              
              	 <div id="base_documento" style="margin-top:10px;">
                 <?php 
				 $qr_anexo = mysql_query("SELECT * FROM modelo_documento_anexos WHERE anexo_id_documento = '$row_doc[documento_id]'");
				 $row_anexo = mysql_fetch_assoc($qr_anexo);
                 
				 if($row_anexo['anexo_extensao'] == 'doc') {
				 				              
                 	echo '<img src="../../imagens/word.jpg" width="100" height="100"/>';
                 
				 } elseif($row_anexo['anexo_extensao'] == 'pdf'){
					echo '<img src="../../imagens/Acrobat1.png" width="100" height="100"/>';
					} else {
				                  
                 echo '<img src="anexos/'.$row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'].'" width="150" height="100"/>';
                 		 
				 }
				 ?>
				 
                 
                 
                 </div>
              
     </td>
    </tr>
    </table

        
        ><div align="center">
            <input type="submit" name="concluir" id="concluir" value="CONCLUIR" class="botao" />
        </div>
             <input type="hidden" name="documento_id" id="documento_id" value="<?=$documento_id?>" />
            <input type="hidden" name="usuario" id="usuario" value="<?=$id_user?>" />
            <input type="hidden" name="update"  value="1" />
        </form>
    </td>
  </tr>
</table>
</div>
</body>
</html>