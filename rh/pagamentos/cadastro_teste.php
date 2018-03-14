<?php 
require("../../conn.php");

// 1 - GPS 2 - FGTS 3 - PIS 4 - IR 5 - RECISÃO

if(isset($_REQUEST['gps'])){
	$texto 	= "GPS";
	$tipo 	= 169;
	$tipo_pg = 1;
        $subgrupo = 3;
}elseif(isset($_REQUEST['fgts'])){
	$texto 	= "FGTS";
	$tipo 	= 167;
	$tipo_pg = 2;
        $subgrupo = 3;
}elseif(isset($_REQUEST['pis'])){
	$texto 	= "PIS";
	$tipo 	= 175;
	$tipo_pg = 3;
        $subgrupo = 4;
}elseif(isset($_REQUEST['ir'])){
	$texto 	= "IR";
	$tipo 	= 168;
	$tipo_pg = 4;
        $subgrupo = 3;
        
}elseif(isset($_REQUEST['recisao'])){
	$texto 	= "RESCISÃO";
	$tipo 	= 51;
	$tipo_pg = 5;
}

$mes = $_REQUEST['mes'];
$ano = $_REQUEST['ano'];
$projeto = $_REQUEST['projeto'];
$id_folha = $_REQUEST['folha'];
$tipo_folha = $_REQUEST['tipo'];

$tabela_consulta = 'rh_folha';
if($tipo_folha == 'COOP'){
	$tabela_consulta = 'folhas';
}

$sql_folha = "SELECT * FROM $tabela_consulta WHERE id_folha = '$id_folha'"; 
$query_folha =  mysql_query($sql_folha);
$row_folha = mysql_fetch_assoc($query_folha);


$query_banco = mysql_query("SELECT id_banco FROM bancos WHERE id_regiao = '$row_folha[regiao]' AND id_projeto = '$row_folha[projeto]' AND status_reg = '1'");
$banco = @mysql_result($query_banco,0);
if($banco == 0){
	echo "ESSE PROJETO NÃO TEM BANCO.<br />";
	exit;
}

$query_mes = mysql_query("SELECT nome_mes FROM ano_meses WHERE num_mes = '$mes'");
$nome_mes = @mysql_result($query_mes,0);

$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
$regiao = @mysql_result($query_regiao,0);
if($row_folha['terceiro'] == '1'){
	if($row_folha['tipo_terceiro'] == 3){
		$decimo3 = " - 13ª integral";
	}else{
		$decimo3 =" - 13ª ($row_folha[tipo_terceiro]ª) Parcela";
	}
}
$nome_completo = urldecode("$tipo_folha $texto $nome_mes/$ano $projeto Folha: $id_folha $decimo3- $regiao");
?>
<style type="text/css">
p.botao {
	border: 1px solid #949494;
	text-align: center;
	padding: 5px;
	background-color: #DADADA;
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	font-weight: bold;
	color: #333;
	cursor:pointer;
	width: 200px;
}
p.botao:hover {
	background-color: #C9C9C9;
}
body {
	font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
}
table {
	border: 1px solid #999;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>

<script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css"/>

<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>
<script type="text/javascript" src="../../jquery/priceFormat.js"></script>
<link rel="stylesheet" type="text/css" href="../../uploadfy/css/uploadify.css"/>
<script type="text/javascript">
$(function(){
	$('#valor').priceFormat({
		prefix: '',
		centsSeparator: ',',
		thousandsSeparator: '.'
	});
	var id_saida = 0;
	$('#data').mask('99/99/9999');
	$('#data').datepicker({
					dateFormat: 'dd/mm/yy',
					changeMonth: true,
					changeYear: true
				});

	$('p.botao').click(function(){
		if($('#progressBar').html() != ""){
                    
                  var   cod1 = $('#campo_codigo_gerais1').val();
                  var   cod2 = $('#campo_codigo_gerais2').val();
                  var   cod3 = $('#campo_codigo_gerais3').val();
                  var   cod4 = $('#campo_codigo_gerais4').val();
                  var   cod5 = $('#campo_codigo_gerais5').val();
                  var   cod6 = $('#campo_codigo_gerais6').val();
                  var   cod7 = $('#campo_codigo_gerais7').val();
                  var   cod8 = $('#campo_codigo_gerais8').val();
                    
                  var cod_barra_gerais = cod1+cod2+cod3+cod4+cod5+cod6+cod7+cod8;
                  
                  
                  
                 
			$.post('actions/cadastra.php',
					{
						id_folha : '<?=$row_folha['id_folha']?>',
						tipo_contrato : '<?=$tipo_folha?>',
						tipo 	: '<?=$tipo?>',
                                                subgrupo: '<?php echo $subgrupo;?>',
						nome 	: $('#nome').val(),
						valor 	: $('#valor').val(),
						data 	: $('#data').val(),
						regiao 	: '<?=$row_folha['regiao']?>',
						projeto : '<?=$row_folha['projeto']?>',
						banco   : $('#bancos').val(),
						mes_pg  : '<?=$mes?>',
						ano_pg  : '<?=$ano?>',
						tipo_pg : '<?=$tipo_pg?>',
                                                cod_barra_gerais: cod_barra_gerais 
                                                
                                                
					},
					function(result){
				id_saida = result;
                                
				<?php if($_COOKIE['logado'] == 87) {?>
                                  console.log(result);                                
                                <?php } ?>
                                    
				$('#arquivo').uploadifySettings('scriptData', {'id_saida'   : id_saida <?php if($texto == "GPS") {?>, tipo_gps: 1 <?php }?>});
				$('#arquivo').uploadifyUpload();
                                
                               
                                $('#arquivo2').uploadifySettings('scriptData', {'id_saida'   : id_saida <?php if($texto == "GPS") {?>, tipo_gps: 2 <?php }?> });
				$('#arquivo2').uploadifyUpload();
                                
			});
			reseta();
		}else{
			
			alert('Por favor anexe um arquivo');
		}
	});
	
		
	var Parametros = {
				'uploader'  : '../../uploadfy/scripts/uploadify.swf',
				'script'    : 'actions/upload.php',
				'cancelImg' : '../../uploadfy/cancel.png',                                
				'auto'      : false,
				'buttonText': 'Anexar PDF',
				'folder'    : '../comprovantes',
				'queueID'   : 'progressBar',
				'scriptData': {'id_saida'   : id_saida <?php if($texto == "GPS") {?>, tipo_gps: 1 <?php }?> },
				'fileDesc'  : 'Somente arquivos PDF',
				'fileExt'   : '*.pdf;',
				'onComplete': function(a,b,c,d){		
								alert('Concluido com sucesso!');							
										if (parent.window.hs) {
											var exp = parent.window.hs.getExpander();
											if (exp) {
													exp.close();
											}
										}							},
				'onAllComplete': function(){		
								}
                                                            } 
                                                            
          //USADO SOMENTE NA GPS                                                  
         var Parametros2 = {
				'uploader'  : '../../uploadfy/scripts/uploadify.swf',
				'script'    : 'actions/upload.php',
				'cancelImg' : '../../uploadfy/cancel.png',                                
				'auto'      : false,
				'buttonText': 'Anexar PDF',
				'folder'    : '../comprovantes',
				'queueID'   : 'progressBar',
				'scriptData': {'id_saida'   : id_saida, tipo_gps: 2 },
				'fileDesc'  : 'Somente arquivos PDF',
				'fileExt'   : '*.pdf;',
				'onComplete': function(a,b,c,d){		
								alert('Concluido com sucesso!');							
										if (parent.window.hs) {
											var exp = parent.window.hs.getExpander();
											if (exp) {
													exp.close();
											}
										}							},
				'onAllComplete': function(){		
								}
                                                            }                                                   
				
	$('#arquivo').uploadify(Parametros);
	$('#arquivo2').uploadify(Parametros2);
	
	function reseta(){
		$("input[type*='text']").each(function(){
			$(this).val('');
		});
	}
	
$('input[name=cod_barra]').change(function(){
    
    if($(this).val() == 1){        
         $('.campo_codigo_gerais').show();
    } else{
         $('.campo_codigo_gerais').hide();
         $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5 , #campo_codigo_gerais4, #campo_codigo_gerais6, #campo_codigo_gerais7, #campo_codigo_gerais8').val(''); 
    }
    
})

$('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5') .keyup(function(){ limita_caractere($(this), 5, 1) });
$('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
$('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });  
   
$('#campo_codigo_gerais8').keyup(function(){
    if ($(this).val().length >= 14){
         $(this).blur(); 
         var valor = $(this).val().substr(0, limite);
        $(this).val(valor) ; 
        
     }    
});
   
 function limita_caractere(campo, limite, muda_campo){    
 var tamanho = campo.val().length;   
 
    if(tamanho >= limite ){
        campo.next().focus();
        var valor = campo.val().substr(0, limite);
        campo.val(valor)
        
    } 
}
});
</script>
<form action="" method="post" enctype="multipart/form-data" name="form1" id="form">
    <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
          <td colspan="4" align="center">
          	<?=$nome_completo?>
          </td>
        </tr>
        <tr>
          <td width="269">&nbsp;</td>
          <td width="205" align="right"><span style="font-size:12px;">Valor : R$</span></td>
          <td width="1081"><input name="valor" type="text" id="valor" size="13" /></td>
          <td width="36">&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="right"><span style="font-size:12px;">Data :</span></td>
          <td><input name="data" type="text" id="data" size="13" /></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="right"><span style="font-size:12px;">Banco :</span></td>
          <td><label for="bancos"></label>
            <select name="bancos" id="bancos">
            <?php 
			$qr_bancos = mysql_query("SELECT * FROM bancos WHERE status_reg = '1'");
			while($row_bancos = mysql_fetch_assoc($qr_bancos)):
			if($grupo != $row_bancos['id_regiao']){ 
				$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_bancos[id_regiao]'");
				echo '<optgroup label="'.$row_bancos['id_regiao']. ' - ' .@mysql_result($qr_regiao,0).'">';
			
			}
			$grupo = $row_bancos['id_regiao'];
			?>
			<option value="<?=$row_bancos['id_banco']?>"><?=$row_bancos['id_banco'] . ' - ' . $row_bancos['nome']?></option>
			<?php
			if($grupo != $row_bancos['id_regiao'] && !empty($grupo)) {echo '</optgroup>';}
			$grupo = $row_bancos['id_regiao'];
			endwhile;
			?>
          </select></td>
          <td>&nbsp;</td>
        </tr>
        <?php if($texto == 'GPS'){ ?>
        <tr>
            <td></td>
            <td align="right">Código de barras:</td>
            <td colspan="3">
                   <input name="cod_barra" type="radio" value="1"/> Sim<br>
                   <input name="cod_barra" type="radio" value="0"/> Não <br>
                   
            </td>
       </tr>
         <tr class="campo_codigo_gerais" style="display:none;"> 
             <td></td>
             <td></td>
             <td colspan="2">
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais1" style="width:50px;"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais2" style="width:50px;"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais3" style="width:50px;"/>
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais4" style="width:60px;"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais5" style="width:50px;"/>
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais6" style="width:60px;"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais7" style="width:30px;"/>
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais8" style="width:130px;"/>    
             </td>    
            </tr>        
       
       
       

        
        
        
        <?php } ?>
        <tr>
          <td colspan="2" align="right" valign="midlle"><?php if($texto == "GPS") {?> GRF<?php } ?></td>
          <td><input type="file" name="arquivo" id="arquivo2" />         
            <br />
          <span style="color:#F00; font-size:10px;" >Aguarde a mensagem de conclus&atilde;o!</span></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><input type="hidden" name="nome" id="nome" value="<?=$nome_completo?>" /></td>
          <td colspan="2"><div id="progressBar"></div></td>
          <td>&nbsp;</td>
        </tr>
      
        <?php if($texto == "GPS") {?> 
        <tr>
          <td colspan="2" align="right" valign="midlle"> SEFIP </td>
          <td><input type="file" name="arquivo" id="arquivo" />         
            <br />
          <span style="color:#F00; font-size:10px;" >Aguarde a mensagem de conclus&atilde;o!</span></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><input type="hidden" name="nome" id="nome" value="<?=$nome_completo?>" /></td>
          <td colspan="2"><div id="progressBar"></div></td>
          <td>&nbsp;</td>
        </tr>
      
        
        <?php } ?>
          <tr>
          <td colspan="4" align="center"><p class="botao">Cadastrar</p></td>
        </tr>
    </table>
</form>
