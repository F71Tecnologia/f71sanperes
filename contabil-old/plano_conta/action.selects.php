<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";

if(isset($_GET['tp']) and $_GET['tp'] == 'grupo'){

$valor = $_GET['val'];

echo '<option value="">Selecione o subgrupo....</option>';

$qr_subgrupo = mysql_query("SELECT * FROM c_subgrupos WHERE c_grupo_id = '$valor'") or die(mysql_error());
while($row_subgrupo = mysql_fetch_assoc($qr_subgrupo)):
	echo '<option value="'.$row_subgrupo['c_subgrupo_id'].'">'.htmlentities($row_subgrupo['c_subgrupo_nome']).'</option> ';
endwhile;


}


if(isset($_GET['tp']) and $_GET['tp'] == 'subgrupo'){

$valor = $_GET['val'];

echo '<option value="">Selecione o tipo....</option>';

$qr_tipo = mysql_query("SELECT * FROM c_tipos WHERE c_subgrupo_id = '$valor'");
while($row_tipo = mysql_fetch_assoc($qr_tipo)):
	echo '<option value="'.$row_tipo['c_tipo_id'].'">'.htmlentities($row_tipo['c_tipo_nome']).'</option>';
endwhile;

}


if(isset($_GET['tp']) and $_GET['tp'] == 'tipo'){

$valor = $_GET['val'];

echo '<option value="">Selecione o subtipo....</option>';

$qr_subtipo = mysql_query("SELECT * FROM c_subtipos WHERE c_tipo_id = '$valor'");
while($row_subtipo = mysql_fetch_assoc($qr_subtipo)):
	echo '<option value="'.$row_subtipo['c_subtipo_id'].'">'.htmlentities($row_subtipo['c_subtipo_nome']).'</option>';
endwhile;

}


//////Criando os checkbox DEPESAS/*
if(isset($_GET['tp']) and $_GET['tp'] == 'despesas'){

$qr_depesas = mysql_query("SELECT * FROM c_despesas_gerais ");


echo '<script>
			$(".todos").change(function(){
				
				
				
				if($(this).attr("checked") == true) {	$(".check_despesas").attr("checked", true);
													} else { 	$(".check_despesas").attr("checked", false);}
				
				
				});	
		</script>
		
		
<table width="100%">

		<tr>
			<td><input name="todos" class="todos" type="checkbox"> Selecionar todos</td>
	    </tr>

<tr>';
while($row_despesas = mysql_fetch_assoc($qr_depesas)):
	
	echo '<td><input name="despesas[]" type="checkbox" value="'.$row_despesas['c_desp_gerais_id'].'" class="check_despesas"/> &nbsp&nbsp&nbsp'.htmlentities($row_despesas['c_desp_gerais_nome']).'</td>';
	
	$cont++;
	if($cont == 2){
	
	echo '</tr><tr>';
	unset($cont);
		
	}
	
endwhile;

}

echo '</table>';


?>