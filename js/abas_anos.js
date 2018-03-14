$(function(){
	
	
    $('.titulo_ano').click(function(){
	
        var tabela = $(this).next();
        $(".listaProcessos").show();
        $(".j_novo").show();
        $(".cadastroProcesso").hide();
        if(tabela.css('display') == 'none') {
            $('.folhas').fadeOut(700);
            tabela.toggle('slow').delay(1200);
            return false;
        } else {
            tabela.fadeOut(500);
            return false;
        }
		
        
    });
	
	
	
})