$.fn.extend({
	combo : function(parametros){
		parametros = {
			reposta : '#banco',
			url : 'novoFinanceiro/actions/combo.bancos.json.php'
		}
		
		var selectProjeto = this;
		var selectBancos = $(parametros.reposta);
		selectProjeto.change(function(){
			selectBancos.attr('disabled',true);
			selectBancos.html('<option >Carregando...</option>');
			$.post(parametros.url,
			{ projeto : $(this).val() },
			function(respostaJson){
				selectBancos.removeAttr('disabled');
				selectBancos.html('<option selected="selected" value="" >Selecione</option>');
				$.each(respostaJson, function(i, valor){
					selectBancos.append('<option value="'+valor.id_banco+'">'+valor.id_banco+' - '+valor.nome+'</option>');
				});
			},
			'json'
			);
		});
	}
});