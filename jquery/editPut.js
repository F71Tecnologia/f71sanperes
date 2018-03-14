$.fn.extend({ 

	editPut: function(Metodos) { 
	
		var conteiner = this.parent();
		var input = this;
		input.hide();
		input.each(function(){
			$(this).parent().append('<span class="valor_do_input" style="display:block">'+$(this).val()+'</span>');
		});
		
		var valorSpan = conteiner.find('span.valor_do_input');
		
		
		valorSpan.click(function(){
			$(this).hide();
			$(this).prev().show();
			$(this).prev().focus();
		});
		
		input.blur(function(){
			$(this).hide();
			$(this).next().show();
			$(this).next().html($(this).val());
		});
	
	}

});
