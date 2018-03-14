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
	
	$('.valor_update').focusin(function(){
		var valor_original = $(this).val();
		$(this).val('');
		/*if($(this).val() == ''){
			$(this).val(valor_original);
		}*/
		$(this).change(function(){
			valor_original = $(this).val();
		});
		$(this).focusout(function(){
			if($(this).val() == ''){
				$(this).val(valor_original);
			}
		});
	});
	
	$('.valor_update').focusin(function(){
		$(this).parent().parent().addClass('destaque_focus');
	});
	$('.valor_update').focusout(function(){
		$(this).parent().parent().removeClass('destaque_focus');
	});
	
	$('input[name*=faltas]').change(function(){
		if($(this).val() == ''){
			$(this).val('0');
		}
		var valor = parseInt($(this).val()*1);
		if(isNaN(valor)){
			$(this).val('0');
		}
	});
		
	//$('.valor_real').priceFormat();

	$('.valor_update').change(function(){
		var id_folha_part = $(this).parent().parent().find('input.id_folha_participante').val();
		var dados_POST = { id_folha_participante : id_folha_part , valor : $(this).val(), caso : $(this).attr('name')  };
		$.post('sintetica_coo/update_valores.php',
		dados_POST,
		function(retorno){
			if(retorno.erro == '0') {
				alert('Erro ao atualizar registro!\n Por favor relate este erro ao setor de TI.');
				return false;
			} else {
				window.location.reload();
			}
		},
		'json'
		);
	});
});