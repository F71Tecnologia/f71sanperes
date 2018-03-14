
function formato_valor(valor){
	
valor = valor.replace('.','').replace(',','.');
return valor;
}
	
function float2moeda(num) {

   x = 0;

   if(num<0) {
      num = Math.abs(num);
      x = 1;
   }
   if(isNaN(num)) num = "0";
      cents = Math.floor((num*100+0.5)%100);

   num = Math.floor((num*100+0.5)/100).toString();

   if(cents < 10) cents = "0" + cents;
      for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
         num = num.substring(0,num.length-(4*i+3))+'.'
               +num.substring(num.length-(4*i+3));
   ret = num + ',' + cents;
   if (x == 1) ret = ' - ' + ret;return ret;

}	
	
	
$(function(){
	
	
$('.horas').focus(function(){	
	
	var linha = $(this).parent().parent();
	//linha.css('background-color','#FFF');
	
});
	
$('.horas, .hora_noturna, .horas_atraso').blur(function(){
	
	var linha 			 	= $(this).parent().parent();	
	var horas 		 		= linha.find('.horas').val();
	var hora_noturna 	    = linha.find('.hora_noturna').val();	
	var clt 		 		= linha.find('.id_clt').val();
	var id_folha 	 		= linha.find('.id_folha').val();
	var horas_atraso 		= linha.find('.horas_atraso').val();
	

			
	//linha.css('background-color','#EBEBEB');
	
	$.post( 'action.calcula_horista.php',{horas : horas, clt : clt, id_folha :id_folha, ajax: 1, hora_noturna_ajax: hora_noturna, horas_atraso : horas_atraso},
	function(data){
		
	
		
	
	
	///INSERINDO OS VALORES NOS CAMPOS DA FOLHA	
	  	linha.find('.base').html(data.base);
		linha.find('.xml_base').val(data.total_xml_base);		
	  	linha.find('.rendimentos').html(data.rendimentos);
	  	linha.find('.descontos').html(data.descontos);
	  	linha.find('.inss_completo').html(data.inss_completo);
	  	linha.find('.irrf_completo').html(data.irrf_completo);
	  	linha.find('.liquido').html(data.liquido);
		linha.find('.salario').val(data.base);
		linha.find('.decimo_terceiro').val(data.decimo_terceiro);
		linha.find('.ferias').val(data.ferias);
		linha.find('.desconto_ferias').val(data.desconto_ferias);
		linha.find('.rescisao').val(data.rescisao);
		linha.find('.desconto_rescisao').val(data.desconto_rescisao);
		
		linha.find('.inss_dt').val(data.inss_dt);
		linha.find('.inss_rescisao').val(data.inss_dt);
		
		
		linha.find('.irrf').val(data.irrf);
		linha.find('.irrf_dt').val(data.irrf_dt);
		linha.find('.irrf_ferias').val(data.irrf_ferias);
		linha.find('.irrf_rescisao').val(data.irrf_rescisao);
		
		
		linha.find('.fgts').val(data.fgts);
		linha.find('.fgts_dt').val(data.fgts_dt);
		linha.find('.fgts_ferias').val(data.fgts_ferias);
		linha.find('.fgts_rescisao').val(data.fgts_rescisao);
		
		linha.find('.fgts_completo').val(data.fgts_completo);
		linha.find('.vale_refeicao').val(data.vale_refeicao);
		linha.find('.salario_maternidade').val(data.salario_maternidade);
		linha.find('.vale_transporte').val(data.vale_transporte);
		
		
		linha.find('.base_inss').val(data.base_inss);
		linha.find('.base_inss_empresa').val(data.base_inss_empresa);
		linha.find('.base_inss_rat').val(data.base_inss_rat);
		linha.find('.base_inss_terceiros').val(data.base_inss_terceiros);
		
		linha.find('.base_irrf').val(data.base_irrf);
		linha.find('.base_fgts').val(data.base_fgts);
		linha.find('.base_fgts_ferias').val(data.base_fgts_ferias);
		linha.find('.ddir').val(data.ddir);
		linha.find('.sindicato').val(data.ddir);
		linha.find('.vale_transporte').val(data.vale_transporte);
		linha.find('.vale_refeicao').val(data.vale_refeicao);
		
		linha.find('.adicional_noturno_mes').html(data.adicional_noturno_mes);
		linha.find('.DSR').html(data.DSR);
		
		
	

	
	///////////////////////////////////////////
	
	
	///faz um somatório dos campos da folha
	var total_base  	   		= 0;
	var total_xml_base     	    = 0;
	var total_rendimentos  		= 0;
	var total_descontos    		= 0;
	var total_inss_completo		= 0;
	var total_irrf_completo		= 0;
	var total_familia      		= 0;
	var total_liquido 	   		= 0;
	
	var total_inss_dt 			= 0;
	var total_inss_rescisao 	=0;
	var total_base_inss   		= 0;
	var total_inss_ferias   	= 0;
	var total_base_inss_empresa = 0;
	var total_inss				= 0;
	var total_base_inss_rat 	= 0;
	var total_base_inss_terceiros = 0;
	
	var total_base_irrf 		= 0;
	var total_irrf_dt			= 0;
	var total_irrf_ferias  		= 0;
	var total_irrf_rescisao     = 0;
	
	var total_fgts_ferias 			= 0;
	var total_base_fgts 		= 0;
	var total_base_fgts_ferias	= 0;
	var total_ddir				= 0;
	
	var total_decimo_terceiro   = 0;
	var total_ferias  			= 0;
	var total_desconto_ferias   = 0;
	var total_rescisao			= 0;
	var total_desconto_rescisao = 0;
	
	var total_familia 				= 0;
	var total_salario_maternidade   = 0;
	var total_sindicato			    = 0;
	var total_vale_transporte 	= 0; 	
	var total_vale_refeicao	 	= 0; 	
	var total_rendimento_mov = 0; 	
	var total_desconto_mov = 0; 
	
	var total_adicional_noturno_mes = 0;

	var total_participantes = 0;
	var total_fgts 			= 0;
	var total_DSR 			= 0;
	
	
	
	$('.base').each(function(i){
		

		
	linha2 = $(this).parent();
	total_base 			+= parseFloat(formato_valor($(this).html()));
	
	
	total_participantes++;
	total_xml_base			+= parseFloat(formato_valor(linha2.find('.xml_base').val()));
	total_inss_completo			+= parseFloat(formato_valor(linha2.find('.inss_completo').html()));
	total_rendimentos   	+= parseFloat(formato_valor(linha2.find('.rendimentos').html()));
	total_descontos     	+= parseFloat(formato_valor(linha2.find('.descontos').html()));  
	total_irrf_completo 		+= parseFloat(formato_valor(linha2.find('.irrf_completo').html())); 
	total_familia 			+= parseFloat(formato_valor(linha2.find('.familia').html())); 
	total_liquido 			+= parseFloat(formato_valor(linha2.find('.liquido').html()));
	
	total_inss_dt			+= parseFloat(formato_valor(linha2.find('.inss_dt').val()));
	total_base_inss			+= parseFloat(formato_valor(linha2.find('.base_inss').val())); 
	total_inss_ferias		+= parseFloat(formato_valor(linha2.find('.inss_ferias').val())); 	
	total_base_inss_empresa	+= parseFloat(formato_valor(linha2.find('.base_inss_empresa').val())); 
	total_base_inss_rat	    += parseFloat(formato_valor(linha2.find('.base_inss_rat').val())); 
	
		
	total_base_inss_terceiros += parseFloat(formato_valor(linha2.find('.base_inss_terceiros').val())); 
	total_base_irrf			+= parseFloat(formato_valor(linha2.find('.base_irrf').val())); 
	total_base_fgts			+= parseFloat(formato_valor(linha2.find('.base_fgts').val())); 
	total_base_fgts_ferias	+= parseFloat(formato_valor(linha2.find('.base_fgts_ferias').val())); 
	total_ddir				+= parseFloat(formato_valor(linha2.find('.ddir').val())); 
	
	total_decimo_terceiro	+= parseFloat(formato_valor(linha2.find('.decimo_terceiro').val())); 
	total_ferias			+= parseFloat(formato_valor(linha2.find('.ferias').val())); 
	total_desconto_ferias	+= parseFloat(formato_valor(linha2.find('.desconto_ferias').val())); 
	total_rescisao			+= parseFloat(formato_valor(linha2.find('.rescisao').val())); 
	total_desconto_rescisao	+= parseFloat(formato_valor(linha2.find('.desconto_rescisao').val())); 
	total_inss_rescisao     += parseFloat(formato_valor(linha2.find('.inss_rescisao').val())); 
	
	total_irrf_dt			+= parseFloat(formato_valor(linha2.find('.irrf_dt').val())); 
	total_irrf_ferias       += parseFloat(formato_valor(linha2.find('.irrf_ferias').val()));  
	total_irrf_rescisao     += parseFloat(formato_valor(linha2.find('.irrf_rescisao').val()));  
	
	total_familia 			  += parseFloat(formato_valor(linha2.find('.familia').html()));
	total_salario_maternidade += parseFloat(formato_valor(linha2.find('.salario_maternidade').val()));
	total_sindicato			+= parseFloat(formato_valor(linha2.find('.sindicato').val()));
	total_vale_transporte   += parseFloat(formato_valor(linha2.find('.vale_transporte').val()));
	total_vale_refeicao	  += parseFloat(formato_valor(linha2.find('.vale_refeicao').val()));
	
	total_adicional_noturno_mes  += parseFloat(formato_valor(linha2.find('.adicional_noturno_mes').html()));
	
	total_fgts	  += parseFloat(formato_valor(linha2.find('.fgts').val()));
	total_fgts_ferias += parseFloat(formato_valor(linha2.find('.fgts_ferias').val()));
	total_DSR += parseFloat(formato_valor(linha2.find('.DSR').html()));
	
	});
	
	
	$('.total_inss_completo').html(float2moeda(total_inss_completo));
	$('.total_rendimentos').html(float2moeda(total_rendimentos));
	$('.total_descontos').html(float2moeda(total_descontos));
	$('.total_irrf_completo').html(float2moeda(total_irrf_completo));
	$('.total_familia').html(float2moeda(total_familia));
	$('.total_base').html(float2moeda(total_base));	
	$('.total_liquido').html(float2moeda(total_liquido));
	
	//ÁREA DE TOTALIZADOES
	$('.total_base_inss').html(float2moeda(total_base_inss));
	$('.total_inss_ferias').html(float2moeda(total_inss_ferias));
	$('.total_base_inss_empresa').html(float2moeda(total_base_inss_empresa));	
	$('.total_inss').html(float2moeda(total_inss_completo + total_inss_ferias));	
	$('.total_base_inss_rat').html(float2moeda(total_base_inss_rat));
	$('.total_base_inss_terceiros').html(float2moeda(total_base_inss_terceiros));
	$('.total_base_irrf').html(float2moeda(total_base_irrf));
	$('.total_base_fgts').html(float2moeda(total_base_fgts));		
	$('.total_base_fgts_ferias').html(float2moeda(total_base_fgts_ferias));
	$('.total_ddir').html(float2moeda(total_ddir));
	
	$('.total_base_fgts_completo').html(float2moeda(total_base_fgts + total_base_fgts_ferias));
	
	///RESUMO POR MOVIMENTO
	$('.total_decimo_terceiro').html(float2moeda(total_decimo_terceiro));
	$('.total_ferias').html(float2moeda(total_ferias));
	$('.total_desconto_ferias').html(float2moeda(total_desconto_ferias));
	$('.total_rescisao').html(float2moeda(total_rescisao));
	$('.total_desconto_rescisao').html(float2moeda(total_desconto_rescisao));
	$('.total_inss_dt').html(float2moeda(total_inss_dt));
	$('.total_inss_rescisao').html(float2moeda(total_inss_rescisao));
	$('.total_irrf_dt').html(float2moeda(total_irrf_dt));
	$('.total_irrf_ferias').html(float2moeda(total_irrf_ferias));
	$('.total_irrf_rescisao').html(float2moeda(total_irrf_rescisao));
	$('.total_familia').html(float2moeda(total_familia));
	$('.total_salario_maternidade').html(float2moeda(total_salario_maternidade));
	$('.total_sindicato').html(float2moeda(total_sindicato));
	$('.total_vale_transporte').html(float2moeda(total_vale_transporte));
	$('.total_vale_refeicao').html(float2moeda(total_vale_refeicao));
	$('.total_adicional_noturno').html(float2moeda(total_adicional_noturno_mes));
	$('.total_xml_base').html(float2moeda(total_xml_base));
	$('.total_dsr').html(float2moeda(total_DSR));
	
	
////CALCULA OS TOTAL DE RENDIMENTOS E DESCONTOS DA PARTE DE REUSMOS POR MOVIMENTO
	

$('.movimento').parent().next().each(function(i){
	
	
	
	
	
	var valor_rendimento  = $(this).children().next().next().html();
	var valor_desconto    = $(this).children().next().next().next().html();
	
	
	
	if(valor_rendimento != ''){
	
			if($(this).attr('class') != 'totais'){
					  total_rendimento_mov += parseFloat(formato_valor(valor_rendimento)); 
					
		
			}
	}
	
	
	if(valor_desconto != ''){
	
			if($(this).attr('class') != 'totais'){
				
						
						  	 	if(valor_desconto){  total_desconto_mov   += parseFloat(formato_valor(valor_desconto));  }
					
	
			
			}
	}
	
	
	

	
	
})
	
	$('.total_desconto_mov').html(float2moeda(total_desconto_mov));
	$('.total_rendimentos_mov').html(float2moeda(total_rendimento_mov));
	
	$('.total_liquido_mov').html(float2moeda(total_rendimento_mov - total_desconto_mov));
	
	
		
	
		
	
		
	
		
	
	},'json'//////fim function(data)
	
	
	)
	
	
	
	
	})	
	






	
	
});
	
