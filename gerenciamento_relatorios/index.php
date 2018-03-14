	<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');


if(isset($_REQUEST)){
	

//print_r($_REQUEST);	
}

 ?>

<html>
<head>
<title>GERENCIAMENTO DE RELATÓRIOS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilo.css" rel="stylesheet" type="text/css">
<link href="../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../jquery-1.3.2.js"></script> 
<script type="text/javascript">
$(function(){
	
$('#parte_1').fadeIn('slow');

$('.avancar').click(function(){

	var numero_div  = parseInt($(this).attr('rel'));
	var proxima_div = parseInt($(this).attr('rel'))+1;
	var tipo_dados  = new Array();
	var tipo_contratacao = new Array();
	var dados_relatorios = new Array();
	
	
	
			switch(numero_div){
		
		case 1: if( $('.tipo_dados:checked').length >0){ mostra_div(numero_div, proxima_div); } else{ alert('Marque uma opção.'); }				
			break;
			
		case 2: if( $('.tipo_contratacao:checked').length >0){ mostra_div(numero_div, proxima_div); } else{ alert('Marque uma opção.'); }				
		break;
		
		case 3: if( $('.dados_relatorio:checked').length > 0) { mostra_div(numero_div, proxima_div);} else {  alert('Marque uma opção.'); return false; } 				
		break;	
		}
	
		if(numero_div == 3){
			
			//$('.tipo_dados:checked').each(function(i)		{  tipo_dados [i]      = $(this).next().val() });
			$('.tipo_contratacao:checked').each(function(i) {  tipo_contratacao[i]   = $(this).val() });
			$('.dados_relatorio:checked').each(function(i)  {  dados_relatorios[i]   = $(this).val() });
		
		
		
			$.ajax({
				url:'actions/action.busca_dados_tabelas.php?tp_contratacao='+tipo_contratacao.join('-')+'&dados_rel='+dados_relatorios.join('-'),
				success: function(resposta){
					
						$('#parte_4').html(resposta);
					}
				});			
		}	
})

$('.voltar').click(function(){
	var numero_div = parseInt($(this).attr('rel'));
	var div_anterior = parseInt($(this).attr('rel'))-1;
	
	 $('#parte_'+numero_div).slideUp('slow');
	 $('#parte_'+div_anterior).slideDown('slow'); 
	
});


///FUNÇÕES PARA PREENCHER SELECTS
$('#unidade').change(function(){
	
	var id = $(this).val();
	$.ajax({
		url:'actions/action.preenche_select.php?master='+id,
		success: function(resposta){  $('#regiao').html(resposta); }
		})	
	});

$('#regiao').change(function(){
	
	var id = $(this).val();
	$.ajax({
		url:'actions/action.preenche_select.php?regiao='+id,
		dataType: 'json',
		success: function(resposta){  $('#projeto').html(resposta.projeto);
								      $('#curso').html(resposta.curso); }
		})	
	});



/////Marcar e desmarcar os campos
$('.campos').live('click',function(){
	
	var div = $(this);
	var checkbox = $(this).find('input[type=checkbox]');
	
	
	if( checkbox.attr('checked') == false) {
		
	   checkbox.attr('checked',true);
	   div.addClass('campo_marcado');	   
	} else {
	 checkbox.attr('checked',false);
	 div.removeClass('campo_marcado');
	 }
	
	
	
});



function mostra_div(numero_div, proxima_div) {
	 $('#parte_'+numero_div).slideUp('slow');
	 $('#parte_'+proxima_div).slideDown('slow'); 
}


});




</script>

</head>
<body style="text-transform:uppercase;">
    <div id="corpo">
       
        <div id="conteudo">  
          <h2>GERENCIAMENTO DE RELATÓRIOS</h2>
        <form name="form1" action="processar_relatorio.php" method="post">
        
        
        <div id="parte_1">        	
            <h3>BUSCA DE DADOS POR:</h3>
        
        		
               <input type="checkbox" name="tipo_dados[]" value="unidade" class="tipo_dados"/> UNIDADE  
               <select name="unidade" id="unidade">
               <option value=""> Selecione uma unidade:</option>
			   <?php
               $qr_master = mysql_query("SELECT * FROM master WHERE status = 1");
			   while($row_master = mysql_fetch_assoc($qr_master)):	
			   		   
			   	echo '<option value="'.$row_master['id_master'].'">'.$row_master['nome'].'</option>';	
						   
			   endwhile;
			   ?>              
               </select></br>
               <input type="checkbox" name="tipo_dados[]" value="regiao"  class="tipo_dados"/>  REGIÃO  <select name="regiao" id="regiao"></select></br>
               <input type="checkbox" name="tipo_dados[]" value="projeto" class="tipo_dados"/>  PROJETO <select name="projeto" id="projeto"></select></br>
               <input type="checkbox" name="tipo_dados[]" value="curso"   class="tipo_dados"/>   CURSO   <select name="curso" id="curso"></select></br>
               <input type="button" name="avancar_parte1" id="avancar_parte1" class="avancar" value="AVANÇAR" rel="1" />
               
        	
        </div>
        
        
        <div id="parte_2">        	
        <h3>RELATÓRIO DO TIPO:</h3>        
               <input type="checkbox" name="tipo_contratacao[]" value="2"  class="tipo_contratacao"/> CLT </br>
               <input type="checkbox" name="tipo_contratacao[]" value="1" class="tipo_contratacao"/>  AUTONOMO </br>
               <input type="checkbox" name="tipo_contratacao[]" value="3" class="tipo_contratacao"/> COOPERADO </br>
               <input type="checkbox" name="tipo_contratacao[]" value="4" class="tipo_contratacao"/>   AUTONOMO/PJ </br>
               <input type="button" name="voltar_parte2" id="voltar_parte2" class="voltar" value="VOLTAR" rel="2"/>
               <input type="button" name="avancar_parte2" id="avancar_parte2" class="avancar" value="AVANÇAR" rel="2"/>
        </div>
        
        <div id="parte_3">        	
        <h3>PERSONALIZAÇÃO DO RELATÓRIO:</h3>        
               <input type="checkbox" name="dados_relatorio[]" value="1" class="dados_relatorio"/>  DADOS DO FUNCIONÁRIO </br>
               <input type="checkbox" name="dados_relatorio[]" value="2" class="dados_relatorio"/>  INFORMAÇÕES SOBRE FOLHAS DE PAGAMENTO </br>
               <input type="checkbox" name="dados_relatorio[]" value="3" class="dados_relatorio"/>  FÉRIAS </br>
               <input type="checkbox" name="dados_relatorio[]" value="4" class="dados_relatorio"/>  EVENTOS  </br>
               <input type="checkbox" name="dados_relatorio[]" value="5" class="dados_relatorio"/>  MOVIMENTOS </br>
               <input type="checkbox" name="dados_relatorio[]" value="6" class="dados_relatorio"/>  RESCISÃO  </br>
               
               <input type="button" name="avancar_parte3" id="avancar_parte3"  class="avancar" value="AVANÇAR" rel="3"/>
        </div>
       
       <div id="parte_4"> 
       <input type="button" name="voltar_parte4" id="voltar_parte4"  class="voltar" value="VOLTAR" rel="4"/>       
       </div> 
        
        
        </form>
        </div>
    </div>
</body>
</html>