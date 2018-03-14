<?php 
include "../../conn.php";

$id_prestador = $_GET['id_prestador'];

$qr_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$id_prestador' LIMIT 1");
$row_prestador = mysql_fetch_assoc($qr_prestador);

$tipo = $_GET['tipo'];
// 1 - DARFF | 2 - DARFF (de novo!) | 3 - GPS

include "sql.php"; // INSTRUï¿½ï¿½ES SQL PARA AS CONSULTAS DE SAIDAS
include "config.php"; // ARQUIVO DE CONFIGURAï¿½ï¿½O DE TAXAS


$query = mysql_query($sql_row);
$saidas = array();
while($row = mysql_fetch_assoc($query)){
	$saidas[] = $row['id_saida'];
	$total_saida += $row['valor_saida'];
}
// PEGANDO AS SAIDAS REFERENTES

if(!empty($_REQUEST['id_pagamento'])){
	$total_saida = null;
	$qr = mysql_query("SELECT prestador_saidas.id_saida, 
							  REPLACE(saida.valor,',','.') AS valor_saida 
					   FROM prestador_pagamento INNER JOIN prestador_saidas USING(id_pagamento)
					   INNER JOIN saida ON saida.id_saida = prestador_saidas.id_saida
					   WHERE prestador_pagamento.id_pagamento = '$_REQUEST[id_pagamento]'");
	while($row = mysql_fetch_assoc($qr)):
		$saidas_diff[] = $row['id_saida'];
		$total_saida += $row['valor_saida'];
	endwhile;
	
	$saidas = $saidas_diff;
	
}


switch($tipo):
	
	case 1 : // SAIDA DO TIPO DARF  IRRF
		$tipo_doc = '1';
		$nome_saida = "DARF IRRF ".$row_prestador['c_fantasia'];
		$tipo_saida_nome = '144 - DARF IRRF';
		$tipo_saida = '144';
		
		$valor_saida = darf_irrf($total_saida,$row_prestador['prestador_tipo']);
		
		$valor_codigo = getCod($row_prestador['prestador_tipo'],'irrf');
		$valor_taxa = getTaxa($row_prestador['prestador_tipo'],'irrf');
		break;
	case 2: // SAIDA DO TIPO DARF  CSLL
		$tipo_doc = '2';
		$nome_saida = "DARF CSLL ".$row_prestador['c_fantasia'] . "";
		$tipo_saida_nome = '146 - CSLL PIS COFINS';
		$tipo_saida = '146';
		$valor_saida = darf_csll($total_saida,$row_prestador['prestador_tipo']);
		
		$valor_codigo = getCod($row_prestador['prestador_tipo'],'csll');
		$valor_taxa = getTaxa($row_prestador['prestador_tipo'],'csll');			
		break;
	case 3:  // SAIDA DO TIPO GPS
		$tipo_doc = '3';
		$nome_saida = "GPS ".$row_prestador['c_fantasia'];
		$tipo_saida_nome = '145 - GPS PRESTADOR DE SERVIÇO';
		$tipo_saida = '145';
		$valor_saida = gps($total_saida,$row_prestador['prestador_tipo']);
		
		$valor_codigo = '';
		$valor_taxa = getTaxa($row_prestador['prestador_tipo'],'gps');
		break;
	case 4: // SAIDA DO TIPO IRRF PESSOA FISICA
		$tipo_doc = '4';
		$nome_saida = "IRRF PESSOA F&iacute;SICA ".$row_prestador['c_fantasia'];
		$tipo_saida_nome = '144 - DARF IRRF';
		$tipo_saida = '144';
		//$valor_saida = gps($total_saida,$row_prestador['prestador_tipo']);
		$dados_ir = MostraIRRF($total_saida,$row_prestador['id_prestador']);
		$valor_saida = $dados_ir['valor'];
		
		$valor_codigo = '';
		$valor_taxa = $dados_ir['percentual'];
		break;
		
endswitch;
?>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>

<!-- SCRIPT JQUERY UI -->
<link href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<!-- FIM SCRIPT JQUERY UI -->

<!-- SCRIPT VALIDATION -->
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script> 
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script> 
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css"> 
<!-- FIM SCRIPT VALIDATION -->
<!-- SCRIPTS UPLOADIFY -->
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" language="javascript" type="text/javascript"></script>
<!-- FIM SCRIPTS UPLOADIFY -->

<link href="../style/estilo_financeiro.css" rel="stylesheet" type="text/css" />

<style type="text/css">

#anexos{
	overflow: hidden;
}
#anexos ul{
	overflow: hidden;
	padding: 0px;
}
#anexos ul li{
	float: left;
	margin: 0px 3px;
	padding: 5px;
}
</style>




<script type="text/javascript">
$(function(){
	
	$('#regiao').change(function(){
		
		var id_regiao = $(this).val();
		
		$.ajax({
			url : 'actions/projetos.json.php',
			dataType : 'json',
			data : { 'regiao' : id_regiao},
			success: function (dados){
				$('#projeto').html('');
				$.each(dados,function(i,dado){
					$('#projeto').append('<option value="'+dado.id_projeto+'">'+dado.id_projeto+' - '+dado.nome+'</option>');
				});
			
				$('#projeto').change();
			}
		});
		
	});
	
	
	$('#projeto').change(function(){
		
		var id_projeto = $(this).val();
		
		$.ajax({
			url : 'actions/bancos.json.php',
			data : { 'projeto' : id_projeto },
			dataType : 'json',
			success : function(dados){
				$('#banco').html('');
				$.each(dados,function(i,dado){
					$('#banco').append('<option value="'+dado.id_banco+'">'+dado.id_banco+' - '+dado.nome+'</option>');
				});
			}
			
		});
		
		
	});
	
	$('#vencimento').datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true
	});
	
	$('#FileUp').uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : 'actions/upload.php',
				'buttonText'     : 'Enviar foto',
				'queueID'        : 'progress_bar',
				'cancelImg'      : '../../uploadfy/cancel.png',
				'buttonImg'      : '../image/anexar.jpg',
				'width'          : 79,
				'height'		 : 80,
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : true,
				'fileDesc'       : 'Gif, Jpg , Png e Pdf',
				'fileExt'        : '*.gif;*.jpg;*.png;*.pdf;',
				'onComplete'  : function(event, ID, fileObj, response, data) {
							
							eval('var resposta = '+response);							
							var li = $('<li>'+fileObj.name+' <a href="#" class="remove_anexo">X</a></li>').appendTo('#anexos ul');
			    			$('<input type="hidden" name="campo_anexo[]" class="campo_anexo" value="'+resposta.nome+'" />').appendTo(li);
                                                
                        }
				
	});
	
	$('.remove_anexo').live('click',function(){
		
		$(this).parent().remove();
		
	});
	
	
	$("#form_saida").submit(function(e){
		e.preventDefault();
		
		var dadosSerializados = $(this).serializeArray();
		
		$.ajax({
			url : 'actions/submit.php',
			data : dadosSerializados,
			dataType : 'json',
			success : function(resposta){
					console.log(resposta);
                            alert('CADASTRADO COM SUCESSO!');
                                var exp = window.hs.getExpander();
                                if (exp) {
                                      exp.close();
                                }
			}
		});
		
	});
	
	
});
</script>
<body style="background-color: #FFF;">
<div style="width: 500px;">
	
	<form action="" method="post" id="form_saida" name="form_saida">
		<table width="100%" style="font-size: 12px;">
			<tr class="linha_um">
				<td>Nome</td>
				<td><?php echo $nome_saida; ?><input type="hidden" name="nome" id="nome" value="<?php echo $nome_saida; ?>" /></td>				
			</tr>
			<tr class="linha_um">
				<td>Tipo sa&iacute;da</td>
				<td><?php echo $tipo_saida_nome; ?><input type="hidden" name="tipo" id="tipo" value="<?php echo $tipo_saida; ?>" /></td>
			</tr>
            <?php if(!empty($valor_codigo)):?>
            <tr class="linha_um">
            	<td>Cod. Darf</td>
                <td><?php echo $valor_codigo;?>
                <input type="hidden" name="cod_taxa" id="cod_taxa" value="<?php echo $valor_codigo; ?>" /></td>
            </tr>
            <?php endif;?>
			<tr class="linha_um">
				<td>Valor</td>
				<td>
				<?php 
					echo number_format($valor_saida,2,',','.');
				?>
                	(<?=$valor_taxa.'%'?>)
                	<input type="hidden" name="valor" id="valor" value="<?php echo number_format($valor_saida,2,'.',''); ?>" />
                	<input type="hidden" name="taxa" id="taxa" value="<?php echo $valor_taxa; ?>" />
                </td>
			</tr>
			<tr class="linha_um">
				<td>Saidas referentes</td>
				<td><?= implode(',',$saidas); ?><input type="hidden" name="saidas" id="saidas" value="<?php echo implode(',',$saidas);  ?>" /></td>
			</tr>
			<tr class="linha_um">
				<td>Região</td>
				<td>
					<select name="regiao" class="campotexto" id="regiao">
                        <option value="">- Selecione -</option>
                        <optgroup label="Regi&otilde;es em Funcionamento">
                
                <?php

                //
                    $qr_regioes_ativas = mysql_query("SELECT * FROM regioes WHERE status = '1'");
                    while($row_regiao = mysql_fetch_array($qr_regioes_ativas)) {
                        
                        if($regiao == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        }

						   ?>
                				
                                <option value="<?=$row_regiao['id_regiao']?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                    <?php  } ?>
                    
                </optgroup>
                <optgroup label="Regi&otilde;es Desativadas">
                
                <?php // Acesso a Regiï¿½es Desativadas
                
                
                
                    
                    $qr_desativadas = mysql_query("SELECT * FROM regioes WHERE status = '0'");
                    while($row_regiao = mysql_fetch_array($qr_desativadas)) {
                        
                        if($regiao_usuario == $row_regiao['id_regiao']) {
                            $selected = 'selected';
                        } else {
                            $selected = NULL;
                        } 
						
						?>
                        
                        <option value="<?=$row_regiao['id_regiao']?>" <?=$selected?>><?=$row_regiao['id_regiao'].' - '.$row_regiao['regiao']?></option>
                        
                <?php } ?>
                
                </optgroup>
                </select>
				</td>
			</tr>
			<tr class="linha_um">
				<td>Projeto</td>
				<td>
					<select name="projeto" id="projeto">
						<option value="">...</option>
					</select>
				</td>
			</tr>
			<tr class="linha_um">
				<td>Banco</td>
				<td>
					<select name="banco" id="banco" >
						<option value="">...</option>
					</select>
				</td>
			</tr>
			<tr class="linha_um">
				<td>Data vencimento</td>
				<td><input type="text" name="vencimento" id="vencimento" value="" /></td>
			</tr>
			<tr class="linha_um">
				<td colspan="2" align="center">
					<input type="file" name="FileUp" id="FileUp"/>
				</td>
			</tr>
			<tr class="linha_um">
				<td colspan="2">
					<div id="anexos">
						<ul></ul>
					</div>
				</td>
			</tr>
			<tr class="linha_um" id="conteiner_progress">
				<td colspan="2">
					<div id="progress_bar"></div>
				</td>
			</tr>
			<tr class="linha_um">
				<td colspan="2" align="center">
					<input type="submit" value="  Cadastrar  " />
					<input type="hidden" value="<?php echo $id_prestador;?>" name="id_prestador" id="id_prestador" />
					<input type="hidden" value="<?php echo $tipo_doc;?>" name="tipo_doc" id="tipo_doc"/>
                    <input type="hidden" name="mes" id="mes" value="<?php echo $_REQUEST['mes']; ?>" />
                    <input type="hidden" name="ano" id="ano" value="<?php echo $_REQUEST['ano']; ?>" />
                    <input type="hidden" name="id_pagamento" id="id_pagamento" value="<?php echo $_REQUEST['id_pagamento']; ?>" />
				</td>
			</tr>
			
		</table>
		
	</form>
	
</div>
</body>