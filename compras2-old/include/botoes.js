$(function(){

$('.imprimir').click(function(){
	
	window.print() ;
	$('.continuar').css('display','block');
	
	$('.aprovar').css('display','block');
	$('.reprovar').css('display','block');
	
	$('.deferir').css('display','block');
	$('.indeferir').css('display','block');
});



$('.continuar').click(function(){


	window.location.href = "../gestaocompras2.php";
	
});



$('.aprovar').click(function(){


	var id_compra = $('#id_compra').val();
	if(confirm('Aprovar esta compra?')){
	
	$.ajax({
		url: 'actions/action.muda_acompanhamento.php?aprovar=1&compra='+id_compra,
		success: function(resposta){
			alert('A compra foi aprovada!');
			window.location.href = '../gestaocompras2.php';
			
		}
		
		})
	}
	
});


$('.reprovar').click(function(){

	var id_compra = $('#id_compra').val();
	if(confirm('Você está preste a reprovar esta compra. Tem certeza disso?')){
	
	$.ajax({
		url: 'actions/action.muda_acompanhamento.php?cotacao=1&compra='+id_compra,
		success: function(resposta){
			alert('Compra não aprovada!');
			window.location.href = '../gestaocompras2.php';
			
		}
		
		})
	}
});





$('.deferir').click(function(){


		var id_compra = $('#id_compra').val();
	if(confirm('Deferir esta compra?')){
	
	$.ajax({
		url: 'actions/action.muda_acompanhamento.php?deferir=aberturaprocesso&compra='+id_compra,
		success: function(resposta){
			alert('A compra foi deferida!');
			window.location.href = '../gestaocompras2.php';
			
		}
		
		})
	}
	
});


$('.indeferir').click(function(){

	var id_compra = $('#id_compra').val();
	if(confirm('Você est	á preste a indeferir esta compra. Tem certeza disso?')){
	
	$.ajax({
		url: 'actions/action.muda_acompanhamento.php?selecao=1&compra='+id_compra,
		success: function(resposta){
			alert('Compra indeferida!');
			window.location.href = '../gestaocompras2.php';
			
		}
		
		})
	}
});



});	
