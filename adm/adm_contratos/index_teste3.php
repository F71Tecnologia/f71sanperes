<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');
include('../../classes_permissoes/acoes.class.php');

$acesso_exclusao = array(9,5,87);
$ACOES = new Acoes();


if(isset($_POST['obrigacao_entrega'])) {
	
	if(strstr($_POST['obrigacao_data'], '/')) {
		$_POST['obrigacao_data'] = formato_americano($_POST['obrigacao_data']);
	}
$ano_competencia = $_POST['ano_competencia'];	
	
    mysql_query("INSERT INTO obrigacoes_entregues (entregue_obrigacao, entregue_dataproc, entregue_datareferencia, entregue_autor, entregue_data,entregue_ano_competencia) VALUES ('$_POST[obrigacao_entrega]', '$_POST[obrigacao_data]', '$_POST[data_referencia]', '$_COOKIE[logado]', NOW(),'$ano_competencia' )") or die(mysql_error());
	
	header('Location: '.$_SERVER['PHP_SELF'].'?m='.$link_master);
}

if(isset($_GET['excluir'])) {
	mysql_query("DELETE FROM obrigacoes_entregues WHERE entregue_id = '".$_GET['excluir']."' LIMIT 1") or die(mysql_error());
}
?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Contratos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 
<script type="text/javascript"> 
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
	
	function mascara_data(d){  
		var mydata = '';  
		data = d.value;  
		mydata = mydata + data;  
		if (mydata.length == 2){  
		mydata = mydata + '/';  
		d.value = mydata;  
		}  
		if (mydata.length == 5){  
		mydata = mydata + '/';  
		d.value = mydata;  
		}  
		if (mydata.length == 10){  
		verifica_data(d);  
		}  
	}
	function verifica_data (d) {  
		dia = (d.value.substring(0,2));  
		mes = (d.value.substring(3,5));  
		ano = (d.value.substring(6,10));  
		situacao = "";  
		// verifica o dia valido para cada mes  
		if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31) {  
			situacao = "falsa";  
		}  
		// verifica se o mes e valido  
		if (mes < 01 || mes > 12 ) {  
			situacao = "falsa";  
		}  
		// verifica se e ano bissexto  
		if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {  
			situacao = "falsa";  
		}  
		if (d.value == "") {  
			situacao = "falsa";  
		}  
		if (situacao == "falsa") {  
			alert("Data digitada é inválida, digite novamente!"); 
			d.value = "";  
			d.focus();  
		}  
	}
	
</script>
<script type="text/javascript">
$(function(){
   $('.show').click(function() {
		$('.show').not(this).removeClass('seta_aberto');
	  	$('.show').not(this).addClass('seta_fechado');
		
		if($(this).attr('class')=='show seta_aberto') {
			$(this).removeClass('seta_aberto');
			$(this).addClass('seta_fechado');
		} else {
			$(this).removeClass('seta_fechado');
			$(this).addClass('seta_aberto');
		}

		$('.show').not(this).next().hide();
		$(this).next().css({width:"100%"}).slideToggle('fast');
    });
	
	$('.obrigacao_data').datepicker({
		changeMonth: true,
	    changeYear: true,
	  onSelect: function(dateText, inst) {
		  
		  var ano = parseInt(dateText.substring(6)) -1;
		  
		  var inicio = parseInt($(this).parent().prev().find('.projeto_inicio').val().substr(0,4));
		  var  termino = parseInt($(this).parent().prev().find('.projeto_termino').val().substr(0,4)) ;
		 $(this).parent().prev().find('.ano_competencia').val(ano);
			 $(this).parent().next().find('.botao_gerar').attr('disabled',false);
		/*	
		  if((ano>=inicio) && (ano<=termino+1)){
		 	 $(this).parent().prev().find('.ano_competencia').val(ano);
			 $(this).parent().next().find('.botao_gerar').attr('disabled',false);
			 
		  } else {
		  	alert('O ano escolhido é menor que o inicio do projeto ou maior que o término do projeto!');
			$(this).parent().prev().find('.ano_competencia').val('');
			$(this).parent().next().find('.botao_gerar').attr('disabled','disabled');
			
		  }*/
		  
		  
		
		 }
		
		
	});
	
	/*
	$('.obrigacao_data').change(function(){
		var dateText = $(this).val();
		if(dateText != ''){
			  var ano = parseInt(dateText.substring(6)) -1;
		  
			  var inicio = parseInt($(this).parent().prev().find('.projeto_inicio').val().substr(0,4));
			  var  termino = parseInt($(this).parent().prev().find('.projeto_termino').val().substr(0,4)) ;
			
				
			  if((ano>=inicio) && (ano<=termino)){
				 $(this).parent().prev().find('.ano_competencia').val(ano);
				 $(this).parent().next().find('.botao_gerar').attr('disabled',false);
				 
			  } else {
				alert('O ano escolhido é menor que o inicio do projeto ou maior que o término do projeto!');
				$(this).parent().prev().find('.ano_competencia').val('');
				$(this).parent().next().find('.botao_gerar').attr('disabled','disabled');
				
			  }
			
		}
	});
	*/
	
	

	
});
</script>
<style>

.botao_gerar {
	margin:0;
	background-color:#EEE;
	border:1px solid #999;
	padding:2px;
	font-weight:bold !important;
	color:#777;
	text-decoration:none;
	cursor:pointer;
	font-size:13px !important;
	font-family:Verdana, Geneva, sans-serif;
	display:block !important;
}
.botao_gerar:hover {
	background-color:#AAA;
	color:#FFF;
}
</style>
</head>
<body>
<div id="corpo">
    <div id="menu" class="contrato">
       <?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo">
    
<?php

$tipos = array(1 => 'Ativo',0 => 'Inativo');

for($i=2008; $i<=date('Y'); $i++){
	$anos[] = $i;
}
	
// Loop dos Status  
foreach($tipos as $status => $nome_status) {
	      
	  $qr_projetos = mysql_query("SELECT * FROM projeto
	  							 INNER JOIN funcionario_regiao_assoc
								 ON funcionario_regiao_assoc.id_regiao = projeto.id_regiao 
								 WHERE projeto.id_master = '$Master'  
								 AND projeto.status_reg = '$status'
								 AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]'
								 ");
	  while($row_projetos = mysql_fetch_assoc($qr_projetos)) :
	  
	  		
					//if($_COOKIE['logado'] != '75' and ($row_projetos['id_regiao']=='15' or $row_projetos['id_regiao']=='36' or $row_projetos['id_regiao']=='37' or $row_projetos['id_projeto'] == 3236)) continue; // bloqueia as regiões 15, 36, 37
			
			
			
	  
		  $projetos[] = $row_projetos['id_projeto'];   
	  endwhile;
		 
	  $projetos = implode(',', $projetos);
	 
	  // Loop dos Projetos e Subprojetos
      $qr_projeto = mysql_query("SELECT id_projeto, nome AS tipo_contrato , inicio, termino, id_subprojeto, id_regiao, status_reg,data_assinatura
							       FROM projeto
							      WHERE id_projeto IN ($projetos) ORDER BY regiao ASC");
	  while($row_projeto = mysql_fetch_assoc($qr_projeto)): 
	
			
		   $projeto = $row_projeto['id_projeto'];
		   $subprojeto = $row_projeto['id_subprojeto'];
		   $regiao = $row_projeto['id_regiao'];
		   $status_atual = $row_projeto['status_reg'];

			
	       if($regiao != $regiao_anterior) { // Verificação de Região
			   
			   $ordem++;
			   
			   if($ordem != 1) { ?>
              	  </div>
              <?php }
			  
			  if($status_atual != $status_anterior) {
				  echo '<h3 class="titulo">'.$tipos[$status_atual].'</h3>';
			  }
				
			  $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$regiao'");
		      $row_regiao = mysql_fetch_assoc($qr_regiao); 
			  
				
			  
			  ?>
		   
           
           
           
           
              <a class="show <?php if($_GET['aberto'] == $ordem) { echo 'seta_aberto'; } else { echo 'seta_fechado'; } ?>"  id="<?=$ordem?>" href=".<?=$ordem?>" onClick="return false">
                  <span style="text-transform:uppercase">  <?=$row_regiao['regiao']?></span>
              </a>

    		  <div class="<?=$ordem?>" style="width:100%; <?php if($_GET['aberto'] != $ordem) { echo 'display:none;'; } ?>">
		  
		<?php } // Fim da Verificação de Região
		
		
		
		
	
	// Criando Obrigações dos Modelos Prontos ainda não Criados
	$qr_modelos = mysql_query("SELECT * FROM obrigacoes_modelos WHERE modelo_status = '1'");
	while($row_modelo = mysql_fetch_assoc($qr_modelos)) {
	
		  $qr_verificacao    = mysql_query("SELECT * FROM obrigacoes WHERE obrigacao_projeto = '$row_projeto[id_projeto]' AND obrigacao_subprojeto = '$row_projeto[id_subprojeto]' AND obrigacao_modelo = '$row_modelo[modelo_id]'");
		  $total_verificacao = mysql_num_rows($qr_verificacao);
		  
		  if(empty($total_verificacao)) {
			 mysql_query("INSERT INTO obrigacoes (obrigacao_projeto, obrigacao_subprojeto, obrigacao_modelo) VALUES ('$row_projeto[id_projeto]', '$row_projeto[id_subprojeto]', '$row_modelo[modelo_id]')");
		  }
					  
				} //fim modelos ?>	
                
      <div class="titulo_projeto"> <?=$row_projeto['id_projeto'] . ' - ' . $row_projeto['tipo_contrato']; ?></div>
     		<span style="clear:left"></span>     
             
     <table style="width:100%; margin-bottom:50px;" cellspacing="1" cellpadding="4" class="relacao"> 
         
         <tr class="secao_nova">
            <td width="100%" colspan="7">OBRIGA&Ccedil;&Otilde;ES ABERTAS</td>
         </tr>
         <tr class="secao_nova">
            <td width="40%" align="left">Nome</td>
            <td width="18%">Data Limite</td>
            <td width="12%">Status</td>
            <td width="20%">Ano da Compet&ecirc;ncia</td>
            <td width="20%">Data da Entrega</td>
            <td width="10%" colspan="2">&nbsp;</td>
          </tr>
    
	<?php
	
	// Listando as Obrigações Abertas dos Modelos Prontos
  $qr_obrigacoes = mysql_query("SELECT * FROM obrigacoes INNER JOIN obrigacoes_modelos ON modelo_id = obrigacao_modelo WHERE obrigacao_status = '1' AND obrigacao_projeto = '$row_projeto[id_projeto]' AND obrigacao_subprojeto = '$row_projeto[id_subprojeto]' AND modelo_status = 1");
  while($row_obrigacao = mysql_fetch_assoc($qr_obrigacoes)) {
		
	  $data_inicio  = $row_projeto['inicio'];
	  $data_termino = date('Y-m-d', strtotime("+1 year", strtotime($row_projeto['termino'])));
                                  
	  list($ano_projeto,$mes_projeto,$dia_projeto) = explode('-',$data_inicio);
	  
	  $qr_entregue = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$row_obrigacao[obrigacao_id]' AND entregue_status = '1'");
	  
	  settype($anos_entregues,'array');
	  
	  while($row_anos_entregues = mysql_fetch_assoc($qr_entregue)){
	  
		  $anos_entregues[] = substr($row_anos_entregues['entregue_datareferencia'],0,4); 
		
	  }
	   
	 $diferenca = array_diff($anos,$anos_entregues);
	   
	 unset($anos_entregues);
	 
	 $total_entregue = mysql_num_rows($qr_entregue);
	 
	 //verificação  do anexo 1
	 list($assinatura_ano,$assinatura_mes, $assinatura_dia) = explode('-',$row_projeto['data_assinatura']); 
	 
	 if($row_obrigacao['modelo_id'] == 1){  
								 	
								 	$dia_semana = date('w', mktime('0','0','0',$assinatura_mes, 15 +$assinatura_dia ,$assinatura_ano));		
									
									$qr_verifica_publicacao =  mysql_query("SELECT * FROM obrigacoes_entregues WHERE  entregue_obrigacao = '$row_obrigacao[obrigacao_id]' AND entregue_status = '1' ");
									$verifica_publicacao = mysql_num_rows($qr_verifica_publicacao);
									$row_verifica_publicacao = mysql_fetch_assoc($qr_verifica_publicacao);
									
									
								 } else {
									  $dia_semana = date('w', mktime('0','0','0',$row_obrigacao['modelo_mes'],$row_obrigacao['modelo_dia'],$assinatura_ano+1));
								 }
								///////////////// 
								
								
								 
								  if($dia_semana == 0) {
									  $antecipacao_fds = 2;
								  } elseif($dia_semana == 6) {
										$antecipacao_fds = 1;
								  } else {
										$antecipacao_fds = 0;
								  }
											
								 				  
								  $data_entrega = date('Y-m-d', mktime('0','0','0',$row_obrigacao['modelo_mes'],$row_obrigacao['modelo_dia']-$antecipacao_fds,$assinatura_ano+1));
								  
								   //calculo da data de anexo 1	    
									
								   
								   
								   
								   
								  unset($antecipacao_fds); ?>
							
								  <tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
									  <td align="left"><?php echo $row_obrigacao['modelo_nome']; ?></td>
							          
							          <?php 
									  //Exibir a data do anexo 1
									  if($row_obrigacao['modelo_id'] == 1) {
										  
										$data_entrega_anexo_1 = date('Y-m-d', mktime('0','0','0',$assinatura_mes,$assinatura_dia+ 15 - $antecipacao_fds, $assinatura_ano));  
										echo '<td>'.formato_brasileiro($data_entrega_anexo_1).'</td>';
											  
									  } else {
										  
										  echo '<td>'.formato_brasileiro($data_entrega).' </td>';
									  }
									  ?>
							               
									  
							          
									  <td><?php 
									  
									  
									  
									  
									  if($data_entrega > date('Y-m-d')) {
													echo 'Aberto';
												} elseif($verifica_publicacao !=0) {
													echo 'Entregue';
												} else {
												
												echo 'Atrasado';
												}
												
												
												 ?></td>
							                    
									  <?php if(in_array($row_obrigacao['modelo_id'],array('3','6'))) { ?>
							          
							              <td>&nbsp;</td>
							              <td>&nbsp;</td>
							              <td align="center" colspan="2"><a href="cadastro2.php?projeto=<?php echo $row_projeto['id_projeto']; ?>&subprojeto=<?php echo $row_projeto['id_subprojeto']; ?>&obrigacao=<?php echo $row_obrigacao['obrigacao_id']; ?>&m=<?php echo $_GET['m']; ?>" class="botao" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">Cadastrar</a></td>
          
          
		  
		  
		  <?php } else { ?>
					          <form action="modelos/<?php  echo $row_obrigacao['modelo_arquivo'] ;?>.php" method="post">
					                <td>
					                <?php
									$qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '$row_projeto[id_projeto] AND status = 1'");
									
									while($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):
									
										if($data_termino>$termino_anterior){
										$data_termino = $row_subprojeto['termino'];	
										}
										
										$termino_anterior = $data_termino;
									
									endwhile;
									
									if($data_termino == 0){
										$data_final = $row_projeto['termino'];	
									
										
													
									} else {
										$data_final = $data_termino;				
									}
									 
									?>
					                 <input name="ano_competencia" class="ano_competencia" type="text"   style="background-color:transparent; border:0" size="3" value="<?php 	echo $row_verifica_publicacao['entregue_ano_competencia'];?>">
					                    <input type="hidden" name="projeto_inicio" class="projeto_inicio" value="<?php echo $row_projeto['inicio'];?>">
					                      <input type="hidden" name="projeto_termino" class="projeto_termino"  value="<?php echo $data_final;?>">
					                </td>
					                <td>
					                <?php 
									if($verifica_publicacao !=0) {
									
									$disabled = "disabled";
									echo date('d/m/Y', strtotime($row_verifica_publicacao['entregue_dataproc']));
									} else {
									?>
					                    <input type="text" name="obrigacao_data" class="obrigacao_data" size="10" maxlength="10" onKeyUp="mascara_data(this);" <?php echo $disabled; ?>/>
					               <?php 
									}
									
								   ?>
					                </td>
					                <td align="center" colspan="2" align="center">
					                <?php if($verifica_publicacao == 0) { ?>
										
					                    <input type="hidden" name="master" value="<?php echo $Master?>"/>
                                          <input type="hidden" name="obrigacao_id" value="<?php echo $row_obrigacao['obrigacao_id'];?>"/>
                                            <input type="hidden" name="entregue_id" value="<?php echo $row_obrigacao['entregue_id'];?>"/>
                                          
					                    <input type="hidden" name="id_projeto"  value="<?php echo $projeto?>"/>             
					                    <input type="hidden" name="obrigacao_entrega" value="<?php echo $row_obrigacao['obrigacao_id']; ?>"/>
					                    <input type="hidden" name="data_referencia" value="<?php echo $data_entrega; ?>"/>
                                        <?php
										
										if($ACOES->verifica_permissoes(69)){ 
					                    ?>
										<input type="submit"  class="botao_gerar" value="Gerar"  <?php echo $disabled; ?>/>					                	
					                <?php }
									}?>
					                </td>
					            </form>
          <?php } ?>
          
      </tr>
    
          		
<?php 

	unset($disabled,$verifica_publicacao, $qr_verifica_publicacao);
	} // fim obrigacoes

  // Listando as Obrigações Abertas Criadas
  $qr_obrigacoes = mysql_query("SELECT * FROM obrigacoes WHERE obrigacao_status = '1' AND obrigacao_modelo = '' AND obrigacao_projeto = '$row_projeto[id_projeto]'");
  while($row_obrigacao = mysql_fetch_assoc($qr_obrigacoes)) {
	  
	  $id_obrigacao = $row_obrigacao['obrigacao_id'];
	
	  $data_inicio  = $row_projeto['inicio'];
	  $data_termino = date('Y-m-d', strtotime("+1 year", strtotime($row_projeto['termino'])));
		
	  $qr_entregue    = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$row_obrigacao[obrigacao_id]' AND entregue_status = '1'");  	
								  
		  $total_entregue = mysql_num_rows($qr_entregue);
			
			switch($row_obrigacao['obrigacao_periodicidade']) {
				case 'mensal':
					$soma_mes = '1';
				break;
				case 'trimestral':
					$soma_mes = '3';
				break;
				case 'semestral':
					$soma_mes = '6';
				break;
				case 'anual':
					$soma_mes = '12';
				break;
			}
			
			if(!empty($total_entregue)) {
				$soma_mes *= $total_entregue + 1;
			}
			
			$data_entrega = substr(date('Y-m-d', strtotime("$soma_mes month", strtotime($data_inicio))),0,8).$row_obrigacao['obrigacao_dia']; ?>

			<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
				 <td align="left"><?php echo $row_obrigacao['obrigacao_nome']; ?></td>
                 <td>&nbsp;</td>
                 <td><?php if($data_entrega > date('Y-m-d')) {
                                echo 'Aberto';
                            } else {
                                echo 'Atrasado';
                            } ?></td>
                  <td>&nbsp;</td>
                  <td align="center">
                    <form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master.'&aberto='.$status; ?>" method="post">
                        <input type="hidden" name="obrigacao_entrega" value="<?php echo $row_obrigacao['obrigacao_id']; ?>">
                        <input type="hidden" name="data_referencia" value="<?php echo $data_entrega; ?>">
                        <input type="submit" value="Entregar" class="botao">
                    </form>
                  </td>
			</tr>
			
		<?php } //fim obrigacoes abertas
		
		
		
		// Listando as Obrigações Entregues
							   unset($a);
							   $qr_entregues = mysql_query("SELECT * FROM obrigacoes INNER JOIN obrigacoes_entregues ON obrigacao_id = entregue_obrigacao LEFT JOIN obrigacoes_modelos ON obrigacao_modelo = modelo_id WHERE obrigacao_projeto = '$row_projeto[id_projeto]' AND obrigacao_subprojeto = '$row_projeto[id_subprojeto]' AND modelo_status = 1 ORDER BY entregue_ano_competencia ASC ");
							   while($row_entregue = mysql_fetch_assoc($qr_entregues)) {
								   
								   
								   
						//data limite
								   
						   $qr_entregue = mysql_query("SELECT * FROM obrigacoes_entregues WHERE entregue_obrigacao = '$row_entregue[obrigacao_id]' AND entregue_status = '1'");

settype($anos_entregues,'array');

								while($row_anos_entregues = mysql_fetch_assoc($qr_entregue)){
								
										  $anos_entregues[] = substr($row_anos_entregues['entregue_datareferencia'],0,4); 
										
										}
		
									$diferenca = array_diff($anos,$anos_entregues);
									
									unset($anos_entregues);
									
									$total_entregue = mysql_num_rows($qr_entregue);
									
									$dia_semana = date('w', mktime('0','0','0',$row_entregue['modelo_mes'],$row_entregue['modelo_dia'],$row_entregue['entregue_ano_competencia'] + 1));
									
									if($dia_semana == 0) {
									  $antecipacao_fds = 2;
									} elseif($dia_semana == 6) {
										$antecipacao_fds = 1;
									} else {
										$antecipacao_fds = 0;
									}
															  
									$data_limite= date('Y-m-d', mktime('0','0','0',$row_entregue['modelo_mes'],$row_entregue['modelo_dia']-$antecipacao_fds,$row_entregue['entregue_ano_competencia']+1 ));  $Teste = $dia_semana;
									unset($antecipacao_fds);
								   
								   
								   
								    $ano_entrega = $row_entregue['entregue_ano_competencia'];
								   
								    								 
									if(empty($a)) { ?>

                                      <tr class="secao_nova">
                                          <td width="100%" colspan="7">Obrigações entregues</td>
                                      </tr>
                                      <tr class="secao_nova">
                                          <td width="40%" align="left">Nome</td>
                                          <td width="25%">Data Limite </td>
                                          <td width="18%">Ano da Competência</td>
                                          <td width="17%">Data de Entrega</td>
                                          <td colspan="3">&nbsp;</td>
                                           
                                         
                                      </tr>

                               <?php }
							    $a++;
							   
							         if($ano_entrega != $ano_entrega_anterior) { ?>
                                      	  <tr class="secao_nova">
                                              <td width="100%" colspan="7"><?php echo $ano_entrega; ?></td>
                                               
                                          </tr>
                               <?php } ?>
                               
                               		<tr class="linha_<?php if($cor++%2==0) { echo 'um'; } else { echo 'dois'; } ?>">
                                      <td align="left"><?php if(!empty($row_entregue['obrigacao_modelo'])) { echo $row_entregue['modelo_nome']; } else { echo $row_entregue['obrigacao_nome']; } ?></td>
                                     <td> <?php echo formato_brasileiro($data_limite);?></td>
                                      <td><?php echo formato_brasileiro($row_entregue['entregue_ano_competencia']); ?></td>
                                      
                                       <td><?php if(!empty($row_entregue['obrigacao_modelo'])) { echo formato_brasileiro($row_entregue['entregue_dataproc']); } else { echo '&nbsp;'; } ?></td>
                                       
                                          
                                        
                                        <td>&nbsp;</td>    
                                      <td align="center">
                                        <?php if(!empty($row_entregue['obrigacao_modelo']) and !in_array($row_entregue['modelo_id'],array('3','6'))) { ?>
                                            <form action="modelos/finalizados/<?php echo $row_entregue['modelo_arquivo']; ?>" method="post" target="_blank">
                                              
                                                 <input type="hidden" name="obrigacao_id" value="<?php echo $row_entregue['obrigacao_id'] ?>">
                                                 <input type="hidden" name="entregue_id" value="<?php echo $row_entregue['entregue_id'] ?>">
                                                  <input type="hidden" name="ano_competencia" value="<?php echo $row_entregue['entregue_ano_competencia'] ?>">
                                                   
                                                 <input type="submit" value="Visualizar" class="botao">
                                            </form>
                                        <?php } else { 
										
										 if($ACOES->verifica_permissoes(67)){ 
										?>
                                        	<form action="anexos/<?php echo $row_entregue['obrigacao_anexo']; ?>" method="post" target="_blank">
                                                <input type="submit" value="Visualizar" class="botao">
                                            </form>
                                        <?php } 
										
										}
										?>
                                        </td>
                                        
                                        <?php  if($ACOES->verifica_permissoes(68)){ ?>
                                        <td align="center">
                                            <form action="<?php echo 'excluir_obj_entregue.php?m='.$link_master.'&excluir='.$row_entregue['entregue_id'];?>" method="post">
                                                <input type="submit" value="Excluir" class="botao" onClick="if(!confirm('Deseja exluir este documento?')) return false;">
                                            </form>
                                        </td>
                                      <?php } ?>   
                                    </tr>
                                    
                               <?php $ano_entrega_anterior = $ano_entrega; } // obrigacoes entreges ?>

    
	 		</table>
     			
 <?php $regiao_anterior = $row_projeto['id_regiao'];
 	   $status_anterior = $row_projeto['status_reg'];
	
	
	
	
	endwhile; //fim projeto ?>
	    	
	<?php unset($projetos);

} // Fim do Loop dos Status

?>

    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('include/rodape.php'); ?>
    </div>
</div>
</body>
</html>