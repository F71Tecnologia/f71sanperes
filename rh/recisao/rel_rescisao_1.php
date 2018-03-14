<?php
if(empty($_COOKIE['logado'])) {
	print "<script>location.href = '../login.php?entre=true';</script>";
	exit;
}


include('../../conn.php');
include('../../classes/funcionario.php');
include('../../classes/curso.php');
include('../../classes/clt.php');
include('../../classes/projeto.php');
include('../../classes/calculos.php');
include('../../classes/abreviacao.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../classes/valor_proporcional.php');
include('../../funcoes.php');
include("../../classes_permissoes/regioes.class.php");
$Fun     = new funcionario();
$Fun    -> MostraUser(0);
$user	 = $Fun -> id_funcionario;
$regiao  = $_REQUEST['regiao'];

$Curso 	 = new tabcurso();
$Clt 	 = new clt();
$ClasPro = new projeto();
$Calc	 = new calculos();



$sql = "SELECT * FROM funcionario where id_funcionario = '$_COOKIE[logado]'";
$result_user = mysql_query($sql, $conn);
$row_user = mysql_fetch_array($result_user);



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Intranet :: Rescis&atilde;o</title>
<link href="../../favicon.ico" rel="shortcut icon" />
<link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function() {
	$('#data_aviso').datepicker({
		changeMonth: true,
	    changeYear: true,
		onSelect: function(dateText,inst){	$('#trabalhadores').html('');	}
	});
	
$('.linha_1, .linha_2').click(function(){		

	if($(this).attr('class') == 'linha_1' || $(this).attr('class') == 'linha_2' ) {
	$(this).addClass('linha_azul_1');	

} else {
	
	$(this).removeClass('linha_azul_1');			
	}
});

$('#regiao').change(function(){
	
	var regiao = $(this).val();
	
	$('.loader_projeto').html('<img src="../../img_menu_principal/loader.gif" width="40" height="25"/> ');
	$.ajax({
		url: 'action.projetos.php?regiao='+regiao,
		success: function(resposta){
				
			$('#projeto').html(resposta);
			$('.loader_projeto').html('');
				
		}
		
		});
	
	
});

////verifica DATA do aviso
	
	$('#ok').click(function(){
		
			var regiao 		   = $('#regiao').val();
			var data_escolhida = $('#data_aviso').val();
			var projeto   	   = $('#projeto').val();
			
			$.ajax({
				url: 'action.verifica_folha.php?data='+data_escolhida+'&regiao='+regiao+'&projeto='+projeto,
				type:'GET',
				dataType:'json',
				success: function(resposta) {
					
					//if(parseInt(resposta.verifica) == 0) {		
					
					   // $('#data_aviso').val('');
						//$('#trabalhadores').html('');
						//alert('A data escolhida ultrapassou o prazo de 31 dias após a última folha finalizada \n\n Data da última folha: '+resposta.data_ult_folha+'.');
				    	
					
							//} else if(parseInt(resposta.verifica) == 1){
								
								$('#trabalhadores').html('<img src="../../img_menu_principal/loader.gif" width="110" height="65"/> ');
								
								$.ajax({
									url: 'action.clts.php?regiao='+regiao+'&projeto='+projeto,
									type:'GET',
									success: function(resposta){							
										$('#trabalhadores').html(resposta);
																
									}
								
								});
									  }  /* else if(parseInt(resposta.verifica) == 2) {
					
										$('#trabalhadores').html('');
										alert('Não existe folha para este projeto.');					  
									  }*/
				
				//}
		  });
	
	});


$('.marcar_todos').live('click',function(){
		
	$('.clt').attr('checked', true);
	$('.linha_um, .linha_dois').addClass('linha_selecionada');

	});

$('.desmarcar_todos').live('click',function(){
		
	$('.clt').attr('checked', false);
	$('.linha_um, .linha_dois').removeClass('linha_selecionada'); 
	
	});
	
	
	
$('.clt').live('change',function(){	

	var checkbox = $(this)
	var checked = checkbox.attr('checked');
	

	if(checked == true) {
		
		checkbox.attr('checked', false);	
		linha.removeClass('linha_selecionada');
		
	} else {
		checkbox.attr('checked', true);
		linha.addClass('linha_selecionada');
	}
})
	
$('.linha_um, .linha_dois').live('click', function() {
	
	var linha    = $(this);
	var checkbox = $(this).find('.clt'); 
	var checked = checkbox.attr('checked');
	

	if(checked == true) {
		
		checkbox.attr('checked', false);	
		linha.removeClass('linha_selecionada');
		
	} else {
		checkbox.attr('checked', true);
		linha.addClass('linha_selecionada');
	}

});
	

});
</script>
<style>
.linha_um:hover, .linha_dois:hover{ background-color:  #D7EBFF; }

.marcar_todos,.desmarcar_todos  {	text-decoration:none; }
.marcar_todos:hover,.desmarcar_todos:hover { text-decoration:underline; }
.linha_selecionada{ background-color:  #C4C4C4;	}
</style>
</head>
<body>
<div id="corpo">
	<div id="conteudo">
    
    	<a href="recisao.php?regiao=<?php echo $regiao;?>" style="text-decoration:none; font-size:12px;float:left;" ><<< Voltar</a>
        <span class="left"></span>
        
    	<br /><img src="../../imagens/logomaster<?=$row_user['id_master']?>.gif" width="110" height="79">
            <h2>RELATÓRIO DAS RESCISÕES</h2>
            <form name="form" method="post" action="rel_rescisao_2.php">
            
            <table width="100%" class="relacao2">
            <tr>
                <td width="40%" align="right">REGIÃO</td>
                <td align="left" width="60%">
                <select name="regiao" id="regiao">
                    <?php
                    $regiao = new Regioes();
                    $regiao->Preenhe_select_sem_master();	
                    ?>    	
                </select>
                </td>
            </tr>
            <tr>
                <td align="right">PROJETO:</td>
                <td align="left">
                <select name="projeto" id="projeto">
                
                </select>
                <span class="loader_projeto"></span>
                </td>
            </tr>
            <tr>
                <td align="right">DATA DO AVISO:</td>
                <td align="left"><input name="data_aviso" type="text" id="data_aviso"/></td>
            </tr>
            <tr>
            	<td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><input name="enviar" type="button" value="OK" id="ok"/></td>
            </tr>
            
            </table>
            
            <div id="trabalhadores">
            
            </div>
            
            
</form>
	</div>
</div>
</body>
</html>