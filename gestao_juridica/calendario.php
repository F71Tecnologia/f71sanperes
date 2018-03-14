<script type="text/javascript">
$(function(){
	
	
	
	var data = new Date();
	var mes = data.getMonth()+1;
	var ano = data.getFullYear();	
		
	
	$('#calendario').html('<img src="../img_menu_principal/loader.gif" align="center" style="margin-left:170px;"/>');
	
	$.ajax({
		url:'action.calendario_teste.php?mes='+mes+'&ano='+ano,
		success: function(resposta) {
		
		$('#calendario').hide();
		$('#calendario').html(resposta).fadeIn();	
		}
		
		})
	





	
	$('.aviso, .aviso_verde').live('click',function(e){
	
			//$('#aviso_calen').hide();
			
			var andamentos = $(this).find('.andamento_id');
			var notificacoes = $(this).find('.notificacao_id');
			var array_andamentos = new Array(1);
			var array_notificacoes = new Array(1);
			
			//PEgando aa coordenadas do mouse			
			var X = e.pageX;
			var Y = e.pageY;
			
			
			andamentos.each(function(i)  { 	 array_andamentos[i]   = $(this).val();  })
			notificacoes.each(function(i){   array_notificacoes[i] = $(this).val(); })
			
			//$('#aviso_calen').html('<img src="../img_menu_principal/loader.gif" align="center" style="margin-left:170px;"/>').fadeIn(300)
			
			$.ajax({
				url:'action.box_aviso_calendario.php?andamentos='+array_andamentos.join(',')+'&noti='+array_notificacoes.join(','),
				dataType: 'html',
				success:function(resposta){
					
					$('#aviso_calen').css('left',X-15);
					$('#aviso_calen').css('top',Y);
					$('#aviso_calen').html(resposta).fadeIn(300);
					$('#aviso_calen').css('display','block');
					$('#aviso_calen').draggable();
					$('#aviso_calen').mouseleave(function() { $(this).hide(); }) 
				
				}
				});		
		
				
	},function(){}
		
)
		
	
});



$('#seta_esquerda').live('click', function(){
	
	
	var mes_calendario = $('#mes_calendario').val() - 1 ;
	var ano_calendario = $('#ano_calendario').val();
	 
	 if(mes_calendario == 0) {
		  ano_calendario =  ano_calendario - 1;
		  mes_calendario = 12;		 
	 }
	
	$('#calendario').html('<img src="../img_menu_principal/loader.gif" align="center" style="margin-left:170px;"/>');
	
			$.ajax({
				url:'action.calendario_teste.php?mes='+mes_calendario+'&ano='+ano_calendario,
				success: function(resposta){
				
				
					$('#calendario').hide();
					$('#calendario').html(resposta).fadeIn();	
					$('#mes_calendario').val(mes_calendario);	
					$('#ano_calendario').val(ano_calendario);
					
				}
				
				});
	
	
});

$('#seta_direita').live('click', function(){
	
	var mes_calendario = parseInt($('#mes_calendario').val()) + 1 ;
	var ano_calendario = parseInt($('#ano_calendario').val());
	 
	 if(mes_calendario == 13) {
		  ano_calendario =  ano_calendario + 1;
		  mes_calendario = 1;		 
	 }
	 
	$('#calendario').html('<img src="../img_menu_principal/loader.gif" align="center" style="margin-left:170px;"/>');
	
			$.ajax({
				url:'action.calendario_teste.php?mes='+mes_calendario+'&ano='+ano_calendario,
				success: function(resposta){
				
					$('#calendario').hide();
					$('#calendario').html(resposta).fadeIn();					
					$('#mes_calendario').val(mes_calendario);	
					$('#ano_calendario').val(ano_calendario);
					
				}
				
				});




	
	
});


$('#ok').live('click', function(){
	
	var mes = $('#mes_busca').val();
	var ano = $('#ano_busca').val();
	
	$('#calendario').html('<img src="../img_menu_principal/loader.gif" align="center" style="margin-left:170px;"/>');
	
	$.ajax({
				url:'action.calendario_teste.php?mes='+mes+'&ano='+ano,
				success: function(resposta){
				
					$('#calendario').hide();
					$('#calendario').html(resposta).fadeIn();	
					$('#mes_calendario').val(mes);	
					$('#ano_calendario').val(ano);
					
				}
				
				});
	
	
	
});



</script>
<style>
#calendario {

margin-left: 100px;
margin-bottom:20px;
}

.dia{ width:70px;
	height:40px;
	color: #D1D1D1;
}



table.calendario{
text-align:center;
border: 1px #D1D1D1 solid;
border-collapse:collapse;
float:left;
width:488px;
height:auto;
background-color:   #EFEFEF;
}

table.calendario tr{
	border: 1px #FFE6E6 solid;
	/*background-color:#E6E6E6;*/
}
table.calendario tr.nome_dias{

	background-color: #B5B5B5;
	color:#FFF;
	font-weight:bold;	
}

table.calendario td{
border: 1px #D1D1D1 solid;
}

table.calendario td.aviso.hoje{
border: 2px solid #5AF;
}


table.calendario td.dia.hoje{
border: 2px solid #5AF;
width:70px;
height:40px;
color: #000;

}


table.calendario td.aviso.expirado{

	background-color: #FF9D9D;	
	color:#000;
	border: 2px  #F55 solid;
	
}

table.calendario td.aviso{

	background-color:#FFF;	
	color:#FF4A4A;
	
}


table.calendario td.aviso_verde{

	background-color:  #C4FFD2;	
	color:#000;
	
}


table.calendario td.aviso:hover{
background-color:#B5B5B5;
color:#FFF;
}











#aviso_calen{
	width:880px;
	height: auto;
	position:absolute;
	background-color: #FFF;
	margin-left:5px;
	display:none;
	border: 1px solid #CCC;
	z-index:100;
	font-size:10px;
}


h3.titulo_aviso{
	font-size:12px;
	text-align:center;
	background-color: #747474;
	color:#FFF;	
	height:20px;
	margin-top:0;
	padding-top:5px;	
}



#seta_esquerda{
	margin-top:  85px;
	margin-right: 10px;
	width:33px;
	height:54px;
	cursor:pointer;
	float:left;	
	display:block;
	background-image: url('../img_menu_principal/seta_esq.png');

}

#seta_direita{
	margin-top: 85px;
	margin-left: 10px;
	width:33px;
	height:54px;
	cursor:pointer;
	float:left;	
	display:block;
	background-image: url('../img_menu_principal/seta_dir.png'); 
 	 
}

#nome_mes{
	width:488px;
	height:auto;
	text-align:center;
	float:left;
	background-color:  #C8C8C8;
	color:#EEE;
	margin-left: 43px;
	
	/*background-image: url('../img_menu_principal/cal_dia.png');*/
}

#nome_mes h3{
	font-size:16px;
	text-align:left;
	margin:0;
	padding-top:5px;
	height:20px;
	color:#FFF;
	font-style:italic;
}

.marcador{
	

background-position:10px 5px 0px;
background-repeat:no-repeat;	
width:15px; 
height:26px;
display:block;
padding-top:5px;
padding-left: 25px;
}

.realizado{
	text-decoration:none;
	background-color:#DBDBDB;
	border: 1px solid  #D2D2D2;
	padding:2px;	
	color:  #A7A7A7;
	font-weight:bold;
	
}

.realizado:hover{
color:#5D5D5D;	
}

#select{
width:488px;
height: auto;
text-align:right;	
margin-left:143px;
border-color: 1px #000 solid;
	
}


label{

margin-right:5px;
margin-left:10px;	
}


#verde {
}

#vermelho
</style>

<h4 style="text-align:center;margin-bottom:10px;font-weight:500">AVISOS</h4>
<div id="legenda">


</div>


<div id="select">
	<label>M&ecirc;s</label>
    <select name="mes_busca" id="mes_busca">
    <option value=""> </option>
	<?php
    $qr_meses = mysql_query("SELECT * FROM ano_meses WHERE 1");
	while($row_meses = mysql_fetch_assoc($qr_meses)):
	
	$selected = (date('m') == (int)$row_meses['num_mes'])? 'selected="selected"': '';
	echo '<option value="'.$row_meses['num_mes'].'"'.$selected.'> '.$row_meses['nome_mes'].'</option>';
	
	endwhile;
	
	?>
    
    </select>

  	<label>Ano</label>
	<select name="ano_busca" id="ano_busca">
    <?php
	for($i=2000;$i<=date('Y'); $i++){
		
		$selected = (date('Y') == $i)? 'selected="selected"': '' ;
		echo '<option value="'.$i.'" '.$selected.'> '.$i.'</option>';
	
		
	}
    ?>    
    </select>
    <input type="button" value="Ir" id="ok"/>
</div>

<div id="calendario">

</div>


