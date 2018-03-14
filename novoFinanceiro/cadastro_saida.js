$(function(){
	function mostraLoading(){

		$('#base').fadeTo('fast',0.5);
		$("#loading").fadeIn();
		$('.submit-go').attr('disabled',true);
	}

	function ocultaLoading(){
		$('#base').fadeTo('fast',1.0);
		$("#loading").fadeOut();
		$('.submit-go').removeAttr('disabled');
	}	

	function limpar(ID){  
            $(ID).find('input[type*=text]').val('');
        }


	function reloader(){
		window.location.reload();
		//window.close();
	}

	
        function limita_caractere(campo, limite, muda_campo){

            var tamanho = campo.val().length;   

            if(tamanho == limite ){
                campo.next().focus();
               var valor = campo.val().substr(0, limite);
                campo.val(valor)
            } 
            
        }


        function get_nomes(tipo_id){

            $.post('actions/combo.nome.json.php',
                                {tipo: tipo_id},
                                function(retorno){
                                        $("#nome").removeAttr('disabled');
                                                $("#nome").html('<option value="" selected="selected">Selecione</option>');
                                                $.each(retorno, function(i,valor){
                                                        if(valor.id_nome == id_nome){selected = 'selected="selected"';} else {selected = '';} 
                                                        $("#nome").append('<option value="'+valor.id_nome+'" '+selected+' >'+valor.nome+'</option>');
                                                });
                                                ocultaLoading();
                                                $("#nome").focus();
                                },
                                'json'
                                );
        }


        function concluir_cad_edit(){
        
            
           var tipo_formulario = $('.submit-go').val(); ///EDIÇÃO(atualizar) ou INCLUSÃO(cadastrar)
            if(tipo_formulario == 'Cadastrar'){
                   
                    if(confirm('Saída cadastrada com sucesso!\n Deseja cadastrar outra saída?')){ 
                        ocultaLoading();
                        limpar("#Form");                   
                        reloader();
                        
                        
                    } else {                     
                        window.location.href = 'index.php?enc='+$('#link_enc').val();                    
                    }
                    
            }else if(tipo_formulario == 'Atualizar'){              
             
                if (parent.window.hs) {
		var exp = parent.window.hs.getExpander();
		if (exp) {
			setTimeout(function() {
				exp.close();
                             parent.window.location.reload();
			},  5000);
		}
	}
            }
            
        }

	$("#tipo2").val($("#tipo").val());
	$("#tipo").change(function(){
		$("#tipo2").val($(this).val());               

	});



	$('#data, #dt_emissao_nf').datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true
	});
        
        
        
	
       $('#regiao_banco').change(function(){
           
           
            $.post('actions/combo.projeto.json.php',
           { regiao : $(this).val()},
            function(retorno){

                    $('#projeto').html('<option value="" >Selecione</option>');
                    $.each(retorno, function(i,valor){                  				
                            $('#projeto').append('<option value="'+valor.id_projeto+'" >'+valor.id_projeto+' - '+valor.nome+'</option>');
                    });

                    ocultaLoading();
                    $('#projeto').focus();
                      $('#banco').html(''); 
                      $('#banco').attr('disabled', true);
            },

            'json' ); 
            
           
       }) ;
        
        
        $('#projeto').change(function(){
            
            
            $.post('actions/combo.bancos.json.php',
           { projeto : $(this).val()},
            function(retorno){

                    $('#banco').html('<option value="" >Selecione</option>');
                    $.each(retorno, function(i,valor){                  				
                            $('#banco').append('<option value="'+valor.id_banco+'" >'+valor.id_banco+' - '+valor.nome+'</option>');
                    });

                    ocultaLoading();
                       $('#banco').attr('disabled', false);
                    $('#banco').focus();
            },

            'json' ); 
            
           
       }) ;
            
        

	$("#Form").submit(function(e){

		// Verificação do prestador
		var prestador = $('#interno').val();

		if(prestador != ''){

			var p = window.confirm("Atenção!!! \n" + 'Você esta certo que esta cadastrando uma saida para o prestador.\n' + $('#interno').find('option').filter(':selected').text() + ' de ' + $('#regiao-prestador').find('option').filter(':selected').text() + ' ?');

			if(p == false){

				return false;

			}

		}

		if($('#grupo').val() == ""){

			alert("SELECIOLE UM GRUPO!");
			return false;

		}

		if($('#tipo').val() == "" ){

			alert("SELECIOLE UM TIPO!");
			return false;

		}

		

		if($('#real').val() == "" || $('#real').val() == "0,00"){

			alert("DIGITE UM VALOR REAL!");
			return false;

		}

		

		if($('#data').val() == ""){

			alert("DIGITE UMA DATA!");
			return false;

		}

		/*

		if($('#nome').val() == ""){

			alert("SELECIONE UM NOME!");

			return false;

		}*/	

		mostraLoading();
		e.preventDefault();
		var dados = $(this).serialize();            
                
               
               
                
		$.post('actions/form.submit.php',
			  dados,
			  function(retorn)  {
                              
                           //   console.log(retorn);
				  if(retorn == "Erro"){
					  alert("Erro interno!");
					  return false;
				  }
                                  
				  if(($('#barra_upload').html() == "")  && ($('#barra_upload_pg').html() == "" ) ){
                                                                                                                         
                                           concluir_cad_edit(); 
                                        
				  }else{
                                            if($('#barra_upload').html() != "" ){
				  		$('#FileUp').uploadifySettings('scriptData',{Ultimo_ID: retorn});                                               
				  		$('#FileUp').uploadifyUpload();
                                            }
                                  
                                   if($('#barra_upload_pg').html() != null && $('#barra_upload_pg').html() != ""){
                                       
                                       
                                   
				  		$('#FileUp_pg').uploadifySettings('scriptData',{Ultimo_ID: retorn});
				  		$('#FileUp_pg').uploadifyUpload();
                                   }                                  
                                   
                                   
                                     setInterval(function(){
                                        

                                            if($('#barra_upload').html() == ''){
                                                  concluir_cad_edit(); 
                                            }else
                                            if($('#barra_upload_pg').html() == null && $('#barra_upload_pg').html() == ""){
                                                   concluir_cad_edit(); 
                                            }       
                                           // console.log($('#barra_upload').html());
                                         //   console.log($('#barra_upload_pg').html());
                                                
                                    },5000);   
				  		
				  }
			  }
			  );

	});
	

	$("#form2").submit(function(e){	
            
		mostraLoading();
		e.preventDefault();
		var dados = $(this).serialize();
		var exp = window.hs.getExpander();
                
		if (exp) {
				exp.close();
		}

		$.post('actions/form.submit.nomes.php',
				dados,
				function(retorno){

					$.each(retorno, function(i,valor){

							$("#nome").append('<option selected="selected" value="'+valor.id_nome+'">'+valor.nome+'</option>');
							ocultaLoading();
							limpar("#form2");
					});

					

				},

				'json'

		);

	});

	

	
       
        
	$("select[name=grupo]").change(function(){

		 mostraLoading();
                 var grupo_id    = $(this).val();                 
                 var subgrupo_id = $('#saida_subgrupo').val();
                 var selected_tipo;
                 $('#saida_subgrupo').val('');
                 
                 
                 if(grupo_id == ''){ ocultaLoading(); return false; }
                 
                 if( grupo_id >4){
                       $(".nomes-cad").show();
                       $("#bruto")    .parent().hide();
                       $("#bruto")    .val('0,00');
                       $('.prestador').hide();
                       $('.fornecedor').hide();
                       $('#campo_subgrupo').show();
                 }else if(grupo_id <= 4){
                     
                      $('.interno')  .hide();
                       $(".nomes-cad").show();
                       $("#bruto")    .parent().hide();
                       $("#bruto")    .val('0,00');
                       $('.prestador').hide();
                        $('.fornecedor').hide();
                       $('#campo_subgrupo').hide();
                     
                 } 
                 
                 

		$("#tipo").html('<option value="">Carregando...</option>');
		$("#tipo").attr('disabled','disabled');
		$("#nome").html('<option value="" selected="selected">Selecione</option>');
		
                                
                $.post('actions/combo.subgrupo.json.php',

			{grupo: grupo_id},

			function(retorno){

				$("#subgrupo").removeAttr('disabled');

					$("#subgrupo").html('<option value="" selected="selected">Selecione</option>');

					$.each(retorno, function(i,valor){
                                            
                                            if(subgrupo_id == valor.id){
                                                selected_tipo = 'selected="selected"';
                                                $("#tipo").attr('disabled',false);
                                            } else{ 
                                                selected_tipo = '';
                                            }
                                          
						$("#subgrupo").append('<option value="'+valor.id+'"'+selected_tipo+'>'+valor.id_subgrupo+'-'+valor.nome+'</option>');

					});

					ocultaLoading();
					
                                        $("#subgrupo").trigger("change");  
                                        
			},

			'json'			

			);


	});
        

   $("#tipo").change(function(){

		 mostraLoading();
                 var grupo = $('#grupo').val();
                 var subgrupo = $('#subgrupo').val();
                 var id_nome  = $('#id_nome').val();
            
                 var selected;
                 
                 
		if( ($(this).val() == '132') ||  (grupo == 30) || grupo == 80 || grupo == 20){

			 $('.interno')  .fadeIn('slow');
			 $(".nomes-cad").fadeOut('slow');
			 $("#bruto")    .parent().fadeIn('slow');
			 $("#bruto")    .val('0,00');
                      
                         

		 }else{

		 	$('.interno').hide();
			$("#bruto").parent().hide();
			$("#bruto").val('0,00');
			$('.nomes-cad').show();

		 }                 
                 
                 

		$("#nome").html('<option value="">Carregando...</option>');
		$("#nome").attr('disabled','disabled');
$.post('actions/combo.nome.json.php',

			{tipo:$(this).val()},
			function(retorno){

				$("#nome").removeAttr('disabled');
					$("#nome").html('<option value="" selected="selected">Selecione</option>');
					$.each(retorno, function(i,valor){

                                                if(valor.id_nome == id_nome){selected = 'selected="selected"';} else {selected = '';} 
						$("#nome").append('<option value="'+valor.id_nome+'" '+selected+' >'+valor.nome+'</option>');

					});

					ocultaLoading();

					$("#nome").focus();

			},

			'json'			

			);
	});
  
     
     
$('#subgrupo').change(function (){
    
    var saida_tipo_id = $('#saida_tipo').val();
    var grupo_id      = $('#grupo').val();
    var selected;
    
    $.post('actions/combo.tipo.php',
				{subgrupo: $(this).val(), grupo_id: grupo_id},
                                
				function(retorno){

					$("#tipo").removeAttr('disabled');
					$("#tipo").html('<option value="" selected="selected">Selecione</option>');                                      
                                      
                                        
					$.each(retorno, function(i,valor){

                                              if(saida_tipo_id == valor.id_entradasaida){selected = 'selected="selected"';} else {selected = '';} 
						
                                                $("#tipo").append('<option value="'+valor.id_entradasaida+'" '+selected+' >'+valor.id_entradasaida+' - '+valor.cod+'- '+valor.nome+'</option>');

					});
					ocultaLoading();                                          
					
                                         $("#tipo").trigger("change");  

				},

				'json'

				);
}); 


$('#regiao-prestador').change(function(){
        
       var projeto_prestador = $('#prestador_pg_projeto').val();
       var projeto_fornecedor = $('#fornecedor_pg_projeto').val();
       
       if(projeto_prestador != ''){ var projeto_id = projeto_prestador; }
       else
       {var projeto_id = projeto_fornecedor; }
       
        mostraLoading();
         $('.novoPrestador').attr('href',' ../processo/prestadorservico.php?regiao='+$(this).val()+'&id=1');
         
            
        $.post('actions/combo.projeto.json.php',

        { regiao : $(this).val()},

        function(retorno){

                $('#interno').html('<option value="" selected="selected" >Selecione</option>');

                $('#Projeto-prestador').html('<option value="" selected="selected" >Selecione</option>');

                $.each(retorno, function(i,valor){
                  				
                        if(projeto_id == valor.id_projeto){var selected = 'selected="selected"';} else { var selected = ''; }                     
                        $('#Projeto-prestador').append('<option value="'+valor.id_projeto+'" '+selected+' >'+valor.id_projeto+' - '+valor.nome+'</option>');

                });

                ocultaLoading();
                $('#Projeto-prestador').focus();
        },

        'json'

        );

});

	

	

$('#Projeto-prestador').change(function(){

                     mostraLoading();

                     var regiao_id     = $('#regiao-prestador').val();
                     var projeto_id    = $(this).val();
                     var prestador_id  = $('#prestador_id').val();
                    var fornecedor_id = $('#fornecedor_id').val();
                    var selected;
                  
                    
                     ////CARREGANDO OS PRESTADORES
                    $.post('actions/combo.prestador.json.php',
                    { regiao : regiao_id, projeto : projeto_id },
                    function(retorno){
                            $('#interno').html('<option value="" selected="selected" >Selecione</option>');
                            $.each(retorno, function(i,valor){
                                    
                                    if(prestador_id == valor.id_prestador){ selected = 'selected="selected"'; } else { selected = ''; }
                                    
                                    
                                    $('#interno').append('<option value="'+valor.id_prestador+'" '+selected+' >'+valor.id_prestador+' - '+valor.numero+' - '+valor.c_fantasia+' - '+valor.c_cnpj+'</option>');

                            });

                           
                            $('#interno').focus();
                    },
                    'json'
                    );
                      
                      
                   ///CARREGANDO OS FORNECEDORES     
                   $.post('actions/combo.fornecedor.json.php',
                    { regiao : regiao_id, projeto : projeto_id },
                    function(retorno){
                            if(retorno != null){
                            $('#fornecedor').html('<option value="" selected="selected" >Selecione</option>');
                            $.each(retorno, function(i,valor){
                                    
                                    
                                    if(fornecedor_id == valor.id_fornecedor){ selected = 'selected="selected"'; } else { selected = ''; }
                                    
                                    $('#fornecedor').append('<option value="'+valor.id_fornecedor+'" '+selected+' >'+valor.id_fornecedor+' - '+valor.nome+'</option>');

                            });
                            }
                           
                            //$('#fornecedor').focus();
                    },
                    'json'
                    );     
                ocultaLoading();

            });

	

	

	$('#FileUp').uploadify({

				'uploader'       : '../uploadfy/scripts/uploadify.swf',
				'script'         : '../include/upload_financeiro.php',
				'folder'         : 'fotos',
				'buttonText'     : 'Anexo',
				'queueID'        : 'barra_upload',
				'cancelImg'      : '../uploadfy/cancel.png',
				'width'          : 190,
				'height'	 : 80,
				'auto'           : false,
				'method'         : 'post',
 				'multi'          : true,
				'fileDesc'       : 'Gif, Jpg , Png e Pdf',
				'onError'         : function(event,queueID,fileObj,errorObj){
										//alert("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
                                                                              //  console.log("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
                                                                    },

			 	'onSelect'        : function(){
									$("#barra_upload").next().append('<span style="color:#F00;font-size:10px;">Clique em cadastrar para concluir o envio!</span>');								}	});	

	$("a.highslide").click(function(){

		if($("#tipo").val() != ""){
			hs.htmlExpand(this, { outlineType: 'rounded-white', wrapperClassName: 'draggable-header', contentId: 'cadastro_nomes' } );
		}else{

			alert("Selecione um tipo primeiro!");

		}

	}
    );
        
        
        
        $("#FileUp_pg").uploadify({
		'uploader'       : '../uploadfy/scripts/uploadify.swf',
		'script'         : 'actions/upload.comprovante.pg.php',
		'folder'         : 'fotos',
		'buttonText'     : 'Comprovante',
		'queueID'        : 'barra_upload_pg',
		'cancelImg'      : '../uploadfy/cancel.png',		
		'width'          : 190,
		'height'	 : 80,
		'method'         : 'post',
		'multi'          : true,
		'fileDesc'       : 'Gif, Jpg , Png e pdf',
		'fileExt'        : '*.gif;*.jpg;*.png;*.pdf;',
                'onComplete'   : function(resposta){
                                                               //console.log(resposta);
                                                                        
									}
              /*  'onError'         : function(event,queueID,fileObj,errorObj){
										alert("Atenção \n O arquivo não foi anexado.\n Erro: \n"+errorObj.type);
									}*/
		//'scriptData'	 : {Ultimo_ID : $('#id_saida').val()}
		
	});

	
        
        
        
        
        
        
        
        
        
$('#referencia').change(function(){
    
    var referencia = $(this).val();
    
    if(referencia == 2) {
        
        $('#campo_bens').show();
    } else {
       $('#campo_bens').hide();
         $('#bens').val('');
    }
    
    
    
})


$('#tipo_boleto').change(function(){
    var tipo = $(this).val();
    if(tipo == 1){
        $('#campo_nosso_numero').hide();
        $('.campo_codigo_consumo').show()
        $('.campo_codigo_gerais').hide();
        limpa_cod_barra();
    } else {
       $('#campo_nosso_numero').show();
       $('.campo_codigo_gerais').show();
        $('.campo_codigo_consumo').hide();
          limpa_cod_barra();

    }


})

$('#codigo_barra_consumo1, #codigo_barra_consumo3,#codigo_barra_consumo5, #codigo_barra_consumo7 ') .keyup(function(){ limita_caractere($(this), 11, 1) });
$('#codigo_barra_consumo2, #codigo_barra_consumo4, #codigo_barra_consumo6').keyup(function(){ limita_caractere($(this), 1, 1) }); 

$('#codigo_barra_consumo8').keyup(function(){
       
    if ($(this).val().length == 1){
        $('#real').focus();
    }
    
})

$('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5') .keyup(function(){    limita_caractere($(this), 5, 1) });
$('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
$('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });

$('#campo_codigo_gerais8').keyup(function(){
  
 
    if ($(this).val().length == 14){
         $('#real').focus();
    }
    
})


$('input[name=tipo_empresa]').change(function(){
   var tipo = $(this).val();
   
    if(tipo == 1){
        
        $('.prestador').show();
        $('.fornecedor').hide();
        
    } else {
            $('.fornecedor').show();
          $('.prestador').hide();
    }
    
});


$('#tipo_pagamento').change(function(){
    
    var tipo_pg = $(this).val();   
    if(tipo_pg == '1'){
        $('#campo_boleto').show();      
      //  $('.n_documento').hide();
         $('.link_nfe').hide();
        
    } else if(tipo_pg == 3) {
        
       //  $('.n_documento').show();  
         $('.link_nfe').show();  
         $('#campo_boleto').hide();      
         $('.campo_codigo_consumo').hide();      
         $('.campo_codigo_gerais').hide();      
         $('#campo_nosso_numero').hide(); 
        
    }else{
         $('#campo_boleto').hide();      
         $('.campo_codigo_consumo').hide();      
         $('.campo_codigo_gerais').hide();      
         $('#campo_nosso_numero').hide(); 
      //   $('.n_documento').show();
         $('.link_nfe').hide();
         
         limpa_cod_barra();
         
    }    
})


/////TRIGGERS
/*
 $("#grupo").trigger("change");  
 $('#regiao-prestador').trigger("change"); 
 $('#Projeto-prestador').trigger("change"); 
 
if($('#id_nome').val() != ''){  get_nomes( $('#tipo').val());  }



 if($('input[name=tipo_empresa]:checked')) { $('input[name=tipo_empresa]:checked').trigger('change');    }
 $('#referencia').trigger("change");
 $('#tipo_pagamento').trigger("change");

*/

////////////////////////////////////
///////////FUNÇÕES//////////////////
//////////////////////////////////



//////////////ANEXOS/////////////////
$('li.excluir, li.excluir_pg').click(function(){
		
               var div        = $(this);         
               var id_arquivo   = div.attr('value');
               var tipo_anexo = div.attr('rel');
               
             
               
		if(window.confirm('TEM CERTEZA QUE DESEJA DELETAR ESTE ANEXO?')){
			$.post('actions/apaga.anexo.php',
			{id : id_arquivo,
                        tipo_anexo : tipo_anexo},
                    
			function(retono){
				if(retono == '0'){
					
                                        alert('Erro ao deletar anexo.');
					return false;
                                        
				}else{
					div.prev().fadeOut('slow');                                        
                                        div.remove();
                                        //window.location.reload();
				}
			}
			);
		}
	});

$('select[name=estorno]').change(function(){

    if($(this).attr('checked') == false){
       $('.descricao_estorno').fadeOut();
       $('.valor_estorno_parcial').fadeOut();
       
    } else if($(this).val() == 1){
         $('.descricao_estorno').fadeIn();
          $('.valor_estorno_parcial').fadeOut();
    }else if($(this).val() == 2){
          $('.descricao_estorno').fadeIn();
          $('.valor_estorno_parcial').fadeIn();
    }else if($(this).val() == ''){
    $('.descricao_estorno').fadeOut();
    $('.valor_estorno_parcial').fadeOut();

    }
    
   
   
    
});


///////////////////////////

function limpa_cod_barra(){
   // /$('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais4, #campo_codigo_gerais5, #campo_codigo_gerais6,   #campo_codigo_gerais7,#campo_codigo_gerais8').val('');
   // $('#codigo_barra_consumo1, codigo_barra_consumo2, #codigo_barra_consumo3,#codigo_barra_consumo4,#codigo_barra_consumo5, #codigo_barra_consumo6, #codigo_barra_consumo7, #codigo_barra_consumo8').val('');  
    
}



});


