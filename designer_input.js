var value;

//ATUA EM TODOS OS CAMPOS DO TIPO TEXT
$(':text').click(function(){
	$(this).css("background", "#CCFFCC"); 
	
})
$(':text').blur(function(){
	$(this).css("background", "#FFFFFF");
	value = $(this).val();
	value=value.toUpperCase();
	$(this).val(value);
})

//ATUA EM TODOS OS CAMPOS DO TIPO TEXTAREA
$(':input[type=textarea]').click(function(){
	$(this).css("background", "#CCFFCC"); 
	
})
$(':input[type=textarea]').blur(function(){
	$(this).css("background", "#FFFFFF");
	value = $(this).val();
	value=value.toUpperCase();
	$(this).val(value);
})
