<?php
require("../conn.php");
$id_documento = $_REQUEST['id_doc'];
$id_funcionario = $_REQUEST['id_fun'];
$regiao = $_REQUEST['id_regiao'];
$mes_selecionado = $_REQUEST['mes'];

$query = mysql_query("SELECT * FROM documentos WHERE id_documento = '$id_documento'");
$row = mysql_fetch_assoc($query);
 
$query_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($query_regiao);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Envio de documentos</title>
<link rel="stylesheet" type="text/css" href="../uploadfy/css/default.css" />
<link rel="stylesheet" type="text/css" href="../uploadfy/css/uploadify.css" />
<script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript">
$().ready(function(){	
	// upload de arquivo unico
	$("#upload").uploadify({
				'uploader'       : '../uploadfy/scripts/uploadify.swf',
				'script'         : 'actions/upload.php',
				'buttonText'     : '',
				'width'          : '156',
				'height'         : '46',
				'queueID'        : 'bar_upload',
				'cancelImg'      : '../uploadfy/cancel.png',
				'buttonImg'      : '../imagens/botao_upload.jpg',
				'auto'           : true,
				'method'         : 'post',
				'sizeLimit'      : '10240000',
				'onError'        : function(event,queueID,fileObj,errorObj){
										alert("Tipo de erro: "+errorObj.type+"\n Informação: "+errorObj.info);
									}, 
 				'multi'          : true,
				'fileDesc'       : 'Você pode selecionar 1 ou Mais arquivos para envio \n Extenções permitidas, gif,jpg,doc,docx,xls,xlsx,pdf,re,rar,zip e txt',
				'fileExt'        : '*.gif;*.jpg;*.doc;*.docx;*.xls;*.xlsx;*.pdf;*.re;*.txt;*.rar;*.zip;',
				'onAllComplete'	 : function(){
										$('#bar_upload').hide();
										$('#linha').hide();
										parent.window.location.reload();
										if (parent.window.hs) {
											var exp = parent.window.hs.getExpander();
											if (exp) {
												
									 
												
													exp.close();
											
											}
										}
									},

				'onSelect'       : function(){
										$('#bar_upload').show();
										$('#upload').uploadifySettings('scriptData', {'id_documento' : '<?=$id_documento?>','id_funcionario' : '<?=$id_funcionario?>','id_regiao' : '<?=$regiao?>', 'mes_selecionado' : '<?=$mes_selecionado?>'});
									}
	});
});
</script>
<style type="text/css">
<!--
#bar_upload {
	position:absolute;
	left:15px;
	top:21px;
	width:264px;
	height:91px;
	z-index:1;
	display:none;
}
.base {
	margin: 5px;
	padding: 5px;
	border: 1px solid #999;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}
ul {
	margin: 0px;
	padding: 0px;
	overflow: hidden;
	width: 100%;
}
ul li {
	list-style-type: none;
	float: left;
	padding: 5px;
	background-color: #E9E9E9;
	margin-top: 1px;
	margin-right: 1px;
	margin-left: 1px;
	border: 1px solid #CCC;
	font-family: Arial, Helvetica, sans-serif;
	cursor: pointer;
}
-->
</style>
</head>
<body>

<div id="bar_upload"></div>

    <div class="base">
    <?=$row['nome_documento']." - ".$row_regiao['regiao']?><br />
      <input name='' id='upload' type='file' /> 
    </div>
</body>
</html>