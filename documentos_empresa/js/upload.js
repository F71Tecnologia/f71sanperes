$().ready(function(){
	
	$("#uploadMult").hide();
		
	$("#mult").click(function(){
		$("#uploadUnico").hide();
		$("#uploadMult").show();
	});
	
	$("#unico").click(function(){
		$("#uploadUnico").show();
		$("#uploadMult").hide();
	});
	//upload de multiplos arquivos
	
	
	
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
 				'multi'          : false,
				'fileDesc'       : 'Extenções permitidas, gif,jpg,doc,docx,xls,xlsx,pdf,re,rar,zip e txt',
				'fileExt'        : '*.gif;*.jpg;*.doc;*.docx;*.xls;*.xlsx;*.pdf;*.re;*.txt;*.rar;*.zip;',
				'onComplete'     : function(a,b,c,d){
										alert(d);
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
