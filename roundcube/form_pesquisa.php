
<script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
</script>
<script type="text/javascript">
$(function(){
	
	
$('#enviar').click(function(){
	
var busca = $('#busca').val();
var tipo_pesquisa = '';

$('.tipo_pesquisa').each( function(i) {
				
				
	if($(this).attr('checked') ==	'checked') {  tipo_pesquisa += $(this).val()+'|'; }  });
	$.ajax({
		url: 'busca_email.php?busca='+busca+'&tipo_pesquisa='+tipo_pesquisa,
		success: function(resposta) {
				
			$('#messagelist').html(resposta);
		
		}
		
		});


})
});
</script>

<div style="margin-top:0; z-index:1000; width:700px; height:50px;">


<form name="form" method="post" action="" onsubmit="return false">
<table>
<tr>	
    <td  style="font-size:9px;">
        <input type="checkbox" name="tipo_pesquisa[]" value="1" class="tipo_pesquisa" /> Assunto
        <input type="checkbox" name="tipo_pesquisa[]" value="2" class="tipo_pesquisa"/> Remetente
        <input type="checkbox" name="tipo_pesquisa[]" value="3" class="tipo_pesquisa"/> Para
        <input type="checkbox" name="tipo_pesquisa[]" value="4" class="tipo_pesquisa"/> Mensagem inteira
     </td>
     <td><input name="busca" type="text" id="busca"/> </td>
    <td><input name="enviar" type="submit"  value="Pesquisar" id="enviar"/></td>
</tr>
<tr>
	
</tr>
</table>
</form>
</div>


