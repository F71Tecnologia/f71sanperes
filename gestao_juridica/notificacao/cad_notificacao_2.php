<?php






include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "../include/criptografia.php";
include("../../wfunction.php");


$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_user   = $_COOKIE['logado'];
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);
$tipo_user         = $row_funcionario['tipo_usuario'];

$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);

$id_notificacao = $_GET['id'];




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 


<link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all"/>
<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all"/>
<link href="../../resources/css/main.css" rel="stylesheet" media="screen"/>
<link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen"/>
<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
<link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen"/>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
<!--<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>-->
<!--<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>-->
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css"/>
<!--<script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>-->
<script type="text/javascript" src="../../jquery.uploadify-v2.1.4/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="../../jquery.uploadify-v2.1.4/swfobject.js"></script>
<script type="text/javascript">
$(function(){


$('#anexos').uploadify({
			'uploader'  : '../../jquery.uploadify-v2.1.4/uploadify.swf',
			'script'    : 'action.upload_notificacao.php',
			'cancelImg' : '../../jquery.uploadify-v2.1.4/cancel.png',
			'folder'    : '/anexo_notificacoes',
			'auto'      : true,
            'multi'     : true,
			'buttonText'  : 'Enviar',
            'fileExt'   : '*.png',
            'queueID'   : 'base_progresso_proposta',
			'scriptData': { 'upload' : true ,
                            'id_notificacao' : $('#id_notificacao').val(),                          
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
			url :  'action.upload_notificacao.php',
			data : { 'id_anexo' : id_anexo, 'deletar' : true },
			success : function (retorno){
				window.location.reload();
			},
			dataType : 'html'
		});
		
	});
	
	
$('.muda_ordem_anexo').on('change',function(){
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
	url : 'action.upload_notificacao.php',
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

<style>
.add_documento,.add_responsavel{
cursor:pointer;	
}
</style>


</head>
<body  class="fundo_juridico" >
    <?php include("../../template/navbar_default.php"); ?>
    
<div class="container"> 
        <div class="page-header box-juridico-header"><h2><span class="glyphicon glyphicon-briefcase"></span> - GESTÃO JURÍDICA <small> - CADASTRO DE NOTIFICAÇÕES - ANEXAR ARQUIVOS</small></h2></div>
        <div class="panel panel-default">
        <div class="panel-heading text-bold hidden-print">Anexos</div>
        <div class="panel-body">
  
    <form name="form" method="post"  class="form-horizontal top-margin1" enctype="multipart/form-data" action="../index.php">
   
    
    <table cellpadding="0" cellspacing="1" class="secao" class="relacao" width="40%" style="border: 1px #CCC solid;"> 
        <tr>
            <td><h3>ENVIAR ANEXO(S)</h3></td>
            <td align="left">
                <input type="file" id="anexos" name="anexos"/>
                <div id="base_progresso_proposta"></div>
                <input type="hidden" name="id_notificacao" id="id_notificacao" value="<?php echo $id_notificacao; ?>"/>
            </td>
        </tr>
   </table>

     <div>
         <fieldset id="fildset_anexo" style="text-align:center;">
         <legend>Anexo</legend>
        	<hr>
        	<?php 
			$qr_anexos = mysql_query("SELECT * FROM notificacao_anexos WHERE notificacao_id = '$id_notificacao'  AND anexo_status = '1' ORDER BY anexo_ordem ASC");
			
			$num_rows = mysql_num_rows($qr_anexos);
			echo (empty($num_rows)) ? '<center>Nenhum Programa de trabalho anexado</center>' : '';
			
			while($row_anexos = mysql_fetch_assoc($qr_anexos)) :?>   
            <table style="float:left">
            <tr>
                <td colspan="2">         
                    <img src="<?php echo 'anexo_notificacoes/'.$row_anexos['anexo_nome'].'.'.$row_anexos['anexo_extensao']?>" height="250" width="200" />
                </td>
            </tr>
                
            <tr>
                <td>
                    <br/>
                    <input type="text" name="muda_ordem" class="muda_ordem_anexo form-control" value="<?=$row_anexos['anexo_ordem']?>" alt="<?=$row_anexos['anexo_id']?>"/> </td>
                
                <td>
                    <a href="#" rel="<?=$row_anexos['anexo_id']?>" class="btn_del" onClick="return false"><img src="../../uploadfy/cancel.png" /></a>
                </td>
            </tr>
            </table>
            <?php endwhile;?>
            </fieldset>
        </div>
        <br>
        <div class="panel-footer text-right hidden-print controls">
       <button type="submit" name="submit" id="concluir" value="CONCLUIR" class="btn btn-primary"><span class="fa fa-filter"></span> Concluir</button>
    </form>               
        <div style="text-align:left">
            <?php include('../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>      
    </div>
    </div>
    </div>
</div>
</body>
</html>