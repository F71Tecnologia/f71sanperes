<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');
include('../../classes_permissoes/acoes.class.php');
include('../../classes_permissoes/regioes.class.php');

$acesso_exclusao = array(9,5,87);
$ACOES = new Acoes();
$REGIOES = new Regioes();


$qr_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_func);


$qr_master  = mysql_query("SELECT * FROM master WHERE id_master = '$row_func[id_master]'");
$row_master = mysql_fetch_assoc($qr_master);





?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Contratos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 
<script  type="text/javascript"> 
$(function(){
	
	$('#regiao').change(function(){
		var id_regiao = $(this).val();
		
		$.ajax({
		
			url : 'action.gerar_sefip.php?ajax=1&regiao='+id_regiao,
			type: 'POST',
			success: function(resposta){		
				$('#projeto').html(resposta);
				console.log(resposta);
			}
			
		});		
		
	});
	
	
	$('#projeto').change(function(){
		
	var id_projeto = $(this).val();
	var id_regiao  = $('#regiao').val();
	
		
		$.ajax({
		
			url : 'action.gerar_sefip.php?ajax=2&folha&regiao='+id_regiao+'&projeto='+id_projeto,
			type: 'POST',
			dataType:'json',
			success: function(resposta){
				
				console.log(resposta.mes);			
			$('#mes').html(resposta.mes);			
			$('#ano').html(resposta.ano);
			
			}
			
		});	
	
		
		
		
	});
	
	
	
	
	
	
	
	
});


</script>

</head>
<body>
<div id="corpo">    
    <div id="conteudo">

  		<img src="imagens/logo_sefip.jpg" width="357" height="150">
		<h3> 
        	GERAR SEFIP <br>
        	<?php echo $row_master['nome'];?>
        </h3>
	  <form>
            <table> 
                <tr>
                	<td>Regiao</td>
                    <td>
                    	<select name="regiao" id="regiao">
                        <option value=""> Selecione uma região...</option>
                         <option value=""> </option>
                    	<option value="todos">TODOS</option>
						
						<?php
						$REGIOES->Preenhe_select_por_master($row_master['id_master']);
						?>
                        </select>
                    </td>
                </tr>
                <tr>
                	<td>Projeto</td>
                    <td>
                    <select name="projeto" id="projeto">
                    </select>
                    </td>
                </tr>
                <tr>
                	<td>Código de recolhimento</td>
                    <td>
                    <select name="cod_recolhimento">
                        <option value="115">&nbsp;&nbsp;115</option>
                        <option value="150">&nbsp;&nbsp;150</option>
                        <option value="155">&nbsp;&nbsp;155</option>                    
                    </select>
                    </td>
                </tr>
                <tr>
                	<td colspan="2">Competência:</td>
                 </tr>
                 <tr>
                    <td>Mês:</td>
                    <td><select name="mes" id="mes"></select> </td>
                </tr>
                <tr>
                	<td>Ano:</td>
                	<td><select name="ano" id="ano"></select></td>
                </tr>
                
            </table>
		</form>

		
    <p style="margin-bottom:40px;"></p>
    </div>
    
    
    <div id="rodape">
        <?php /// include('../include/rodape.php'); ?>
    </div>
</div>
</body>
</html>