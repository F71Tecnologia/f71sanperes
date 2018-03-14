<?php
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
include('include/criptografia.php');
include('../classes/formato_data.php');

include('actions/array_tabelas.php'); ////ARRAys com os nomes dos campos	

function verifica_campos($query,$campo,$valor){
	
																	
																		
	  $field = mysql_fetch_field($query,$campo );
		switch($field->type) {
		
		case 'date':	echo '<td>'.implode('/',array_reverse(explode('-',$valor))).'</td>';
		 break;
		case 'string' : echo '<td>asdasdsd'.($valor).'</td>';
		break;
		
		 
	}
			
		switch(mysql_field_name($query, $campo)	 ){
		
		
		
		case  'regiao' :	$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$valor'"),0);
										echo '<td>'.htmlentities($nome_regiao).'</td>';
		break;
		
		case 'id_regiao' :  $nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$valor'"),0);
										echo '<td>'.htmlentities($nome_regiao).'</td>';
		break;
		
		case 'projeto': 		$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$valor'"),0);
							    echo '<td>'.htmlentities($nome_projeto).'</td>';
		break;
		
		case 'id_projeto' :		$nome_projeto = mysql_result(mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$valor'"),0);
								echo '<td>'.htmlentities($nome_projeto).'</td>';
		break;
		case 'salbase':  echo '<td>'.number_format($valor,2,',','.').'</td>';
		break;
		case 'sallimpo': echo '<td>'.number_format($valor,2,',','.').'</td>';
		break;
		
		
		default: echo '<td>'.$valor.'</td>';
		
		
		
		}
			
								

	
}

if(isset($_REQUEST)){
	


$array_tipo_dados  		 = $_POST['tipo_dados'];
$id_regiao  			 = $_POST['regiao'];
$id_projeto 			 = $_POST['projeto']; 
$id_master 				 = $_POST['unidade'];
$id_curso				 = $_POST['curso'];
$array_tipo_contratacao  = $_POST['tipo_contratacao'];
$array_tipo_relatorio    = $_POST['dados_relatorio'];
$array_campos            = $_POST['campos'];
$array_campos_folhas  	 = $_POST['campos_folha'];
$array_campos_folha_proc =	$_POST['campos_folha_proc'];

///MONTANDO QUERY

if(in_array('unidade', $array_tipo_dados)){   $unidade =  " AND id_master = $id_master "; }
if(in_array('regiao', $array_tipo_dados)) {   $id_regiao  = "AND id_regiao = $id_regiao ";   $regiao   = "AND regiao = $id_regiao ";  }
if(in_array('projeto', $array_tipo_dados)){   $projeto = "AND id_projeto = $id_projeto "; }
if(in_array('curso', $array_tipo_dados))  {   $curso   = 'AND id_curso = '.$id_curso; }




}
?>

<html>
<head>
<title>GERENCIAMENTO DE RELAT&Oacute;RIOS</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="estilo.css" rel="stylesheet" type="text/css">
<link href="../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../jquery-1.3.2.js"></script> 
<script type="text/javascript">
$(function(){
	
	$('.tipo_dados').click(function(){
		
		var tipo = $(this).attr('href');
		
		if($('#'+tipo).css('display') == 'none'){
		$('.'+tipo).show();
		} else {
			$('.'+tipo).hide();	
			
		}
		
		return false;
		
		});
		
		
		$('.mostrar').click(function() {
			alert($(this).next().show())				
		});
		
	
/*	$('#folhas tr ').click(function(){
		
		
		var linha = $(this)
		
		$('#folhas tr' ).animate({
			
			 opacity: '0.5'
			})
			
			$(this).animate({
			
			 opacity: '0.9',
			 
			})
			$('#folhas').animate({
				
				left: '+=300'
				})
			
		
		})
	*/
});

</script>

</head>
<body style="text-transform:uppercase;">
    <div id="corpo">
       
        <div id="conteudo">  
          <h2>GERENCIAMENTO DE RELATÓRIOS</h2>
          <ul>
		  <?php
		  foreach($array_tipo_relatorio as $tipo){
			  
			switch($tipo) {
				case 1: echo  '<li><a href="dados" class="tipo_dados" >DADOS DO FUNCIONÁRIO</a></li>';
				break;
				case 2: echo  '<li><a href="folhas" class="tipo_dados" >INFORMAÇÕES SOBRE FOLHAS DE PAGAMENTO</a></li>';
				break;
				case 3: echo  '<li><a href="ferias" class="tipo_dados" >FÉRIAS</a></li>';
				break;
				case 4: echo  '<li><a href="eventos" class="tipo_dados" >EVENTOS</a></li>';
				break;
				case 5: echo  '<li><a href="movimentos" class="tipo_dados" >MOVIMENTOS</a></li>';
				break;
				case 6: echo  '<li><a href="rescisao" class="tipo_dados" >RESCISÃO</a></li>';
				break;
				
			
				
			}
			
			  
		  }
		  ?>
          </ul>
          
		  
		  
		  
		  <?php
		  	
          foreach($array_tipo_contratacao as $tipo_contratacao) :
		  
			  include('actions/tp_tabelas_contratacao.php');
						
						
				 
			  echo'<BR><a href="#" class="mostrar">'.$nome_tp_contrato.'</a>';	
			 
		//	 echo '<table border="1">'; 
			//  echo '<tr class="titulo_tabela">';
			
				echo '<table border="1" id="" style="display:BLOCK;">';
			
						
				
				
				
				
				echo '<tr><td colspan="'.sizeof($array_campos).'">DADOS DO FUNCIONÁRIO</td></tr>';

					///PREENCHENDO A LINHA COM O NOME DOS CAMPOS
					
					////DADOS FUNCIONÁRIOS
					
					$campos 		   = implode(',',$array_campos[$tipo_contratacao]); 
					$campos_folha_proc = implode(',',$array_campos_folha_proc[$tipo_contratacao] );		
					
					
							
					echo '<tr>';
					foreach($array_campos[$tipo_contratacao] as $campo){
						echo '<td>'.$nome_campo_dados[$campo].'</td>';							    	
					}					
				
					 echo '<td colspan="'.sizeof($array_campos_folha_proc[$tipo_contratacao]).'" align="center"> FOLHAS</td>';							    	
					 
					
					
					echo '</tr>';
				/////FIM LINHA TITULO
				
				
						
					$qr_trabalhador = mysql_query("SELECT $campos FROM $tabela_trab WHERE tipo_contratacao = '$tipo_contratacao' $id_regiao $projeto ") or die(mysql_error());
					$total_campos = mysql_num_fields($qr_trabalhador);
					while($row_trabalhador = mysql_fetch_array($qr_trabalhador)):
				
					echo '<tr>';
							foreach($array_tipo_relatorio as $relatorio):
												switch($relatorio) {
														case 1:  // include('actions/dados_funcionario.php');
														
																
																		for($i =0;$i<$total_campos; $i++){
																			
																		verifica_campos($qr_trabalhador,$i, $row_trabalhador[$i]);
																		
																		}
																		
																		
																		
																		
																		
																		
																	
														  break;
														case 2: //include('actions/folhas.php');
														 												
																							
																	$qr_folha_proc		= mysql_query("SELECT $campos_folha_proc FROM $folha_proc WHERE $id_trab = '$row_trabalhador[$id_trab]' ") or die(mysql_error());																	
																	$total_campo_folha_proc  = mysql_num_fields($qr_folha_proc);	
																	echo '<td><div class="folhas"><table>';
																	
																	
																		if(mysql_num_rows($qr_folha_proc) !=0){	
																			
																			echo '<tr>';
																			////folha
																			foreach($array_campos_folha_proc[$tipo_contratacao] as $campo1){
																				 echo '<td>'.$nome_campo_folha_proc[$campo1].'</td>';							    	
																			} 
																			echo '</tr>';
																			while($row_folha_proc 	= mysql_fetch_array($qr_folha_proc)):
																			
																					echo '<tr>';
																					
																							for($i=0;$i<$total_campo_folha_proc;$i++) {
																							
																							verifica_campos($qr_folha_proc,$i, $row_folha_proc[$i]);
																							}
																							
																					echo '</tr>';
																	
																			endwhile;
																		}
																	
																	echo '</table></div></td>';
																																	
															  break;
															  
														case 3:
															  break;
															  
															  
														case 4: 
															  break;
													}
						
								endforeach;	
					
					echo '</tr>';
					endwhile;
					
			
				
				/*
				echo '</tr>';	
			
				if(sizeof($array_campos[$tipo_contratacao])== 0 )
				{  	$campos_trab = '*'; }
				else
				{	$campos_trab = implode(',' ,$array_campos[$tipo_contratacao]);		
				}
				
				
				
				$qr_trabalhador	 = mysql_query("SELECT $campos_trab FROM $tabela_trab WHERE tipo_contratacao = '$tipo_contratacao' $regiao $projeto ") or die(mysql_error());
				
				$total_campo_trabahador  = mysql_num_fields($qr_trabalhador);
				
				while($row_trabalhador = mysql_fetch_array($qr_trabalhador)):
				
				
				if(in_array(1,$array_tipo_relatorio)){
							echo '<tr>';
									
									if(sizeof($array_campos[$tipo_contratacao]!= 0 )) {
										for($i=0;$i<$total_campo_trabahador;$i++) {
											echo '<td>'.$row_trabalhador[$i].'</td>';
										}
									}
									
				}
									
							
				if(in_array(2,$array_tipo_relatorio)){
						
					
						$campos_folha_proc = implode(',',$array_campos_folha_proc[$tipo_contratacao] );
						
						$qr_folha_proc 		= mysql_query("SELECT $campos_folha_proc FROM $folha_proc WHERE $id_trab = '$row_trabalhador[$id_trab]' ") or die(mysql_error());
						$row_folha_proc 	= mysql_fetch_array($qr_folha_proc);
						$total_campo_folha  = mysql_num_fields($qr_folha_proc);	
						
					
							for($i=0;$i<$total_campo_folha;$i++) {
							
							echo '<td>'.$row_folha_proc[$i].'</td>';
							}
						
						
						
					}
					
					
					
					echo '</tr>';
				endwhile;
				
				
				
						
					*/				
				echo '<table>';
			
				
		  endforeach;		 
			
		  
		 
		 
		
		  ?>
          
          
		</div>
    </div>
</body>
</html>