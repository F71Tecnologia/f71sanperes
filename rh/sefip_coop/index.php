<?php
if(empty($_COOKIE['logado'])) {
   print "<script>location.href = '../../login.php?entre=true';</script>";
} else {
   include('../../conn.php');
   include('../../wfunction.php');
   include('../../classes/funcionario.php');
   $Fun = new funcionario();
   $Fun -> MostraUser(0);
   $Master = $Fun -> id_master;
}

if($_GET['excluir'] == true) {
	if($_GET['tipo'] == 3) {
		mysql_query("DELETE FROM sefip WHERE mes = '$_GET[mes]' AND ano = '$_GET[ano]' AND tipo_sefip = '3' LIMIT 1");
	} else {
		mysql_query("DELETE FROM sefip WHERE regiao = '$_GET[regiao]' AND projeto = '$_GET[projeto]' AND mes = '$_GET[mes]' AND ano = '$_GET[ano]' AND folha = '$_GET[folha]' AND tipo_sefip = '4' LIMIT 1");
	}
	header("Location: index.php?regiao=$_GET[regiao]");
}
$qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_funcionario);


$qr_regioes = mysql_query("SELECT * FROM regioes WHERE id_master = '$row_func[id_master]' AND id_regiao != 36");
while($row_regioes = mysql_fetch_assoc($qr_regioes)):

$regioes[] = $row_regioes['id_regiao'];


endwhile;

$regioes = implode(',',$regioes);



?>
<html>
<head>
<title>Gerar SEFIP Cooperado</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="../../novoFinanceiro/style/form.css"/>
<!-- higtslide -->
<script type="text/javascript" src="../../js/highslide-with-html.js"></script>
<link href="../../js/highslide.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    hs.graphicsDir = '../../images-box/graphics/';
	hs.outlineType = 'rounded-white';
	hs.showCredits = false;
	hs.wrapperClassName = 'draggable-header';
</script>
<!-- higtslide -->
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../novoFinanceiro/style/form.css" rel="stylesheet" type="text/css">
<script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<script language="javascript">
function Reload() {
	setTimeout("location.href = '<?=$_SERVER['PHP_SELF']?>?regiao=<?=$_GET['regiao']?>&aberto=true'",5000);
}
$(function(){
	indice = 0;
	$('.folha_mes').tooltip({
		tip: '.tooltip',
		onBeforeShow: function(){
			var title = this.getTip();
			var indice = 0;
			
			title.find('.confirmadata').click(function(){
				var href = $(this).parent().prev().attr('href');
				var data = $(this).prev().val();
				$(this).attr('href',href+'&data='+data);
				$(this).click();
				/*window.open(href+'&data='+data, '_blank');
				window.location.reload();*/
			});
			title.find('.dataSefip').click(function(){
				
				$(this).next().fadeIn();
				title.find('.date').keyup(function(){
					var valor = $(this).val();
					if(valor.length == 2 || valor.length == 5 ){
						$(this).val(valor+"/");
					}
					var matriz = valor.split('/');
					if(matriz[0] > 31){
						alert("Digite um dia válido!");
						$(this).val('');
						return false;
					}
					if(matriz[1] > 12){
						alert("Digite um mes válido!");
						$(this).val(matriz[0]+'/');
						return false;
					}
					if(matriz[2] > 2050){
						alert("Digite um ano válido!");
						$(this).val(matriz[0]+'/'+matriz[1]+'/');
						return false;
					}
					
				});
			});
		},
		onBeforeHide : function(){
			var title = this.getTip();
			title.find('.dataSefip').next().hide();
			title.find('.date').val('');
		}
	});
	
	$('.ano').click(function(){
		$('.ano').not(this).find('table').hide();
		$('.ano').find('.titulo').css('background-color','#F1F1F1');
		$(this).find('.titulo').css('background-color','#bbb');
		$(this).find('table').toggle();
	});
	
	
	// recisao
	$('.ano_recisao').click(function(){
		$('.ano_recisao').not(this).next('.meses_recisao').slideUp('slow');
		$(this).next('.meses_recisao').slideToggle('slow');
	});
	$('.mes_recisao').click(function(){
		$('.mes_recisao').not(this).next().slideUp('slow');
		$('.mes_recisao').removeClass('mes_focus');
		$(this).addClass("mes_focus");
		$(this).next().slideToggle('slow');
	});
	
	$('input[name*=data]').click(function(){
			alert('ISSO AI');
	});

});
</script>
<style type="text/css">
.ano {
	text-align:center;
}
.ano table {
	display:none;
}
.folha_mes {
	cursor:pointer;
	width:100%;
}
.titulo {
	background-color:#F1F1F1; cursor:pointer; font-size:13px; padding:4px 0px 4px 0px; width:100%; text-align:center; font-weight:bold; margin-top:10px; clear:both;
}
.tooltip {
	display:none; background-color:#fff; border:1px solid #777; padding:5px; font-size:13px; -moz-box-shadow:2px 2px 11px #666; -webkit-box-shadow:2px 2px 11px #666; text-align:left; line-height:30px;
}
.tooltip a {
	color:#222; text-decoration:none;
}
.dados {
	font-size:13px;
}
.cabecalho {
	font-weight: bold; font-size:13px;
}
.ano_recisao{
	padding:4px; margin:5px 0px; background-color:#F1F1F1; text-align:center; cursor:pointer;
}
.mes_recisao{
	padding:0px 10px; margin: 3px 0px; cursor:pointer;  background-color:#F9F7F7;
}
.mes_focus{
		padding:0px 10px; margin: 3px 0px; cursor:pointer;  background-color:#CCC;
}
.recindidos table{
	font-size:12px;
}
</style>
</head>


<body>
<table align="center" cellpadding="0" cellspacing="0" class="corpo" id="topo">
<tr>
	<td align="right">
     <?php include('../../reportar_erro.php'); ?>
    </td>
  </tr>

  <tr>
	<td align="center">
      <img src="imagens/logo_sefip.jpg" width="357" height="150"><br>
      <span style="font-style:italic;font-weight:bold; color:#999;">(COOPERADO)</span>
    </td>
  </tr>
  <tr>
    <td>

 <?php $meses = array('Janeiro' => '01','Fevereiro' => '02','Março' => '03','Abril' => '04','Maio' => '05','Junho' => '06','Julho' => '07','Agosto' => '08','Setembro' => '09','Outubro' => '10','Novembro' => '11','Dezembro' => '12');

	   // Loop dos Anos

	   for($ano=2009; $ano<=date('Y'); $ano++) { ?>

       <div class="ano">
            <div class="titulo">FOLHAS DE PAGAMENTO <span class="destaque"><?=$ano?></span></div>

          <table cellpadding="4" cellspacing="0" class="relacao">
              <tr class="secao">
                <td width="50%">Mês Referente</td>
                <td width="50%" align="center">Total de Participantes</td>
              </tr>

	 <?php // Loop dos Meses

	       foreach($meses as $nome_mes => $mes) {
			   
			   $qr_folha = mysql_query("SELECT *
			   							  FROM folhas
			   							 WHERE status = '3'
										   AND mes = '$mes'
										   AND ano = '$ano'
										   AND regiao IN($regioes)
										   AND contratacao = '3'");
									

			   $total_folha = mysql_num_rows($qr_folha);
			   
			   
			   if(!empty($total_folha)) { ?>		   

        <tr class="linha_<?php if($cor++%2==0) { ?>um<? } else { ?>dois<? } ?>">
           <td>
              <div class="folha_mes" title="<span style='color:#c30;'><?=$nome_mes?></span><br><span style='line-height:normal;'>

			  <?php while($folha = mysql_fetch_assoc($qr_folha)) {

                    	$qr_projeto   = mysql_query("SELECT id_projeto, nome FROM projeto WHERE id_projeto = '$folha[projeto]'");
						@$projeto 	  = mysql_result($qr_projeto,0,0);
						@$nome_projeto = mysql_result($qr_projeto,0,1);
						
                    	$qr_regiao   = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$folha[regiao]'");
						$regiao 	 = mysql_result($qr_regiao,0,0);
						$nome_regiao = mysql_result($qr_regiao,0,1);
						
                    	$participantes = mysql_query("SELECT * FROM folha_cooperado  WHERE id_folha = '$folha[id_folha]' AND status = '3'");
						$total_participantes = mysql_num_rows($participantes);
                    	$total_geral_participantes += $total_participantes;
						
						$qr_verifica_sefip = mysql_query("SELECT * FROM sefip WHERE mes = '$mes' AND ano = '$ano' AND regiao = '$regiao' AND projeto = '$projeto' AND folha = '$folha[id_folha]' AND tipo_sefip = '4'"); 
		  			 	$verifica_sefip    = mysql_num_rows($qr_verifica_sefip);
						
						if(!empty($verifica_sefip)) {
						
                    		echo "<a href='arquivos/
						".$regiao."_".$projeto."_".$mes."_".$ano.".re' target='_blank' title='Visualizar SEFIP'>".$nome_projeto." - ".$nome_regiao." (".$total_participantes.")</a> <a href='index.php?excluir=true&regiao=
						".$regiao."&projeto=".$projeto."&mes=".$mes."&ano=".$ano."&folha=".$folha['id_folha']."' title='Excluir SEFIP' style='color:#C30'>excluir</a><br>";
						
             			} else {
                      

                    		echo "<a href='sefiptexto2.php?mes=".$mes."&ano=".$ano."&regiao=".$regiao."&projeto=".$projeto."&folha=".$folha['id_folha']."' onClick='return false' class='dataSefip' title='Gerar SEFIP'>".$nome_projeto." - ".$nome_regiao." (".$total_participantes.")</a>";
							echo "<div style='display:none;'>
							
									<form name='sefip' action='sefiptexto2.php?mes=".$mes."&ano=".$ano."&regiao=".$regiao."&projeto=".$projeto."&folha=".$folha['id_folha']."' method='post'>";
									
							      echo "<input type='text' name='data' class='date' size='7'/> ";
									
								echo "	<input type='radio' name='empresa' value='1' checked ><span style='color:#993;'>COOPERATIVA</span>
									<input type='radio' name='empresa' value='2' ><span style='color:#993;'>INSTITUTO</span>
									
									   
									<input type='submit' style='color:#993;background-color:transparent;border:1px solid;border-color:#993;' value='Criar SEFIP'/>	
									   							  
								</form>	   
									   
							      </div><br>";
                        
             			}
					
					} ?> 

                    <br></span>

			   <?php $qr_verifica_sefip = mysql_query("SELECT * FROM sefip WHERE mes = '$mes' AND ano = '$ano' AND tipo_sefip = '3'"); 
		  			 $verifica_sefip    = mysql_num_rows($qr_verifica_sefip);

					 if(!empty($verifica_sefip)) {
						 
						echo "<a href='arquivos/".$mes."_".$ano.".re' target='_blank'>Visualizar SEFIP</a> <a href='index.php?excluir=true&mes=".$mes."&ano=".$ano."&tipo=3' title='Excluir SEFIP' style='color:#C30'>excluir</a><br>";

             		 } else { ?>
                        
                    	<a href='sefiptexto.php?mes=<?=$mes?>&ano=<?=$ano?>&parte=1&parte_sefip=1' target='_blank' onClick='return false' class='dataSefip'>Gerar SEFIP</a>
                       <div style='display:none;'>
							
                            <form name='sefip' action='sefiptexto.php?mes=<?=$mes?>&ano=<?=$ano?>&parte=1&parte_sefip=1' method='post'>
                            <input type='text' name='data' class='date' size='7'/>
                            <input type='radio' name='empresa' value='1' checked ><span style='color:#993;'>COOPERATIVA</span>
                            <input type='radio' name='empresa' value='2' ><span style='color:#993;'>INSTITUTO</span>
                            <input type='submit' style='color:#993;background-color:transparent;border:1px solid;border-color:#993;' value='OK'/>
                    </form>	   

                  </div><br>"
                        
                        
               <?php } ?>"> <?=$nome_mes?>
              </div>
           </td>
           <td align="center"><?=$total_geral_participantes?></td>
        </tr>  
		  <?php unset($total_geral_participantes); 
		  	   }
		   } // Fim do Loop dos Meses ?>
          </table>
		 </div>  
 	   <?php } // Fim do Loop dos Anos ?>
  </td>
 </tr>
 
</table>
</body>
</html>