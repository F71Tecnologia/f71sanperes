<?php 
include ("../include/restricoes.php");
include "../../conn.php";

$id_saida = $_GET['ID'];

$query_saida = mysql_query("SELECT * FROM saida WHERE id_saida = '$id_saida' LIMIT 1");
$row_saida = mysql_fetch_assoc($query_saida);
?>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript">
$(function(){
	$('#quant').keyup(function(){
		if($(this).val() <= 100){
			if($('#datas').attr('checked')){
				$('#datas').change();
			}	
		}else{
			$(this).val(0);
			alert('A quantidade de saidas deve ser menor que 100;');
		}
		
	});
	
	$('.input_data').live('focusin',function(){
		$(this).mask('99/99/9999');
	});
	
	$('#datas').change(coloca_campos);
	
	function coloca_campos(este){
		
		var tabela = $('#form_duplicar').find('table:first');
		
		var data_vencimaneto = '<?php echo implode('/',array_reverse(explode('-',$row_saida['data_vencimento']))) ?>';
		
		if($(this).attr('checked')){
			var quant = $('#quant').val();
			$('.campos_add').remove();
			for(i=0;i<quant;i++){
				tabela.append('<tr class="campos_add" >\n\
				<td>Data '+(i+1)+'</td>\n\
				<td><input type="text" class="input_data" name="campos_add[]" value="'+data_vencimaneto+'" /></td>\n\
				</tr>');
			}
		}else{
			
			$('.campos_add').remove();
		}
	
	}
	
});	
</script>
<form name="form_duplicar" id="form_duplicar" method="POST" action="actions/duplicar_saida.php">
	<table width="100%">
		<tr>
			<td colspan="2" align="center">
				Duplicar saídas
			</td>
		</tr>
		<tr style="display:none;">
			<td>Quantidade</td>
			<td><input type="text" value="1" name="quant" id="quant"/></td>
		</tr>
		<tr style="display:none;">
                    <td align="right"><input type="checkbox" name="datas" id="datas" checked /></td>
			<td>Criar saidas com datas diferentes</td>
		</tr>
                <tr class="campos_add" >
                    <td>Data</td>
                    <td><input type="text" class="input_data" name="campos_add[]" value="<?=implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])))?>" /></td>
                </tr>
	</table>
	<table  width="100%">
		<tr>
			<input type="hidden" name="id_saida" id="id_saida" value="<?php echo $id_saida; ?>" />
			<input type="hidden" name="mes" id="mes" value="<?php echo $_REQUEST['mes']; ?>" />
			<input type="hidden" name="ano" id="ano" value="<?php echo $_REQUEST['ano']; ?>" />
			<input type="hidden" name="id_rescisao" id="id_rescisao" value="<?php echo $_REQUEST['id_rescisao']; ?>" />
			<td align="center"><input type="submit" value="  DUPLICAR  " /></td>
		</tr>
	</table>
</form>