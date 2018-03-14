
$(function() {

$('#pesquisa_usuario').keyup(function(){
	
 var pesq_cbo = $(this).val();
 
 $('#lista_cbo').html('<img src="../img_menu_principal/loader.gif" style="margin-top:100px; margin-left:50px;"/>');
 
 if(pesq_cbo.length >=3) {
	
	
	
	$.ajax({
		url: 'action.cbo.php?q='+pesq_cbo,
		success: function(resposta){
		
			
			$('#lista_cbo').html(resposta);
		}
		
		});
		
		
		$('#lista_cbo').fadeIn('fast');
		
 } else {
	$('#lista_cbo').fadeOut('fast');
	 
 }
	
});


$('#salario').blur(function(){
	
$(this).css('background-color','#FFF');
	calc();
	
});


});

function inserir_cbo(id_cbo, texto){
	

$('#id_cbo').val(id_cbo);
$('#pesquisa_usuario').val(texto);
$('#lista_cbo').fadeOut('fast');

	
}

