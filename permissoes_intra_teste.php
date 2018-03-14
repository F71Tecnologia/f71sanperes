<?php
include "conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

// SELECIONANDO AS REGIÕES CADASTRADAS NO BANCO
$sql = "SELECT * from regioes where id_master = '$row_user[id_master]'";
$result = mysql_query($sql, $conn);

//PEGANDO O ID DO CADASTRO
$id = $_REQUEST['id'];


//////////////////////////////FUNÇÕES
function gerar_btn_acoes($botoes_id, $nome_botao){
	
	$array_status = array( 1 => 'REGIÕES INATIVAS', 0 => 'REGIÕES ATIVAS');
	
	////permisões pra deletar, exluir e etc;.
	$qr_acoes = mysql_query("SELECT * FROM acoes WHERE   botoes_id = '$botoes_id' ORDER BY tp_contratacao_id ASC") or die(mysql_error());
	
	
	if(mysql_num_rows($qr_acoes) != 0) {
		
		echo '<div class="acoes">';
		echo '<h4>Ações - '.$nome_botao.'</h4>';
		
		while($row_acoes = mysql_fetch_assoc($qr_acoes)):	  
											 
			echo '<input type="checkbox" name="acoes[]" value="'.$row_acoes['acoes_id'].'" > ('.$row_acoes['acoes_id'].') '.$row_acoes['acoes_nome'].'<br>';
		
		endwhile;		
		echo '</div>';
		
		
		
		switch($botoes_id){
										
				case 33:
			    case 60:						
									echo '<div class="acoes_folhas">';
								
									foreach($array_status as $status=> $nome_status) {
									
										
										
											if($status == 0) {
											
												$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
												echo '<span> REGIÕES INATIVAS </span><br>';		
													
											} else {
											
												$qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
												echo '<span> REGIÕES ATIVAS</span> <br>';														
											
											}															
											
											while($row_regioes = mysql_fetch_assoc($qr_regioes)):
											
												$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
												$row_master = mysql_fetch_assoc($qr_master);	
																							
																									
												if($row_master['status'] 	== 0 ) continue;																								   
												if($row_master['id_master'] != $master_anterior) {  echo '<span >'.$row_master['nome'].'</span> <br>'; 	}            
															
												echo '<input name="regiao_folhas['.$row_botoes['botoes_id'].'" type="checkbox" value="'.$row_regioes['id_regiao'].'"/>'.$row_regioes['id_regiao'].' - '.($row_regioes['regiao']).'<br>';					
												$master_anterior = $row_master['id_master'];									
									
											endwhile;
									}
				break;													
	
	}///FIM SWITCH 
			
		
		echo '</div>';
		
		
		

	}///FIM veririfca acoes
	
}

?>
<html>
<head><title>:: Intranet ::</title>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<script language="javascript" src="jquery-1.3.2.js"></script>
<script src='ajax.js' type='text/javascript'></script>
<script language="javascript" src='js/ramon.js' type='text/javascript'></script>
<script language="javascript" src='jquery/jquery-1.4.2.min.js' type='text/javascript'></script>

<link href='autocomp/css.css' type='text/css' rel='stylesheet'>
<link href="adm/css/estrutura.css" rel="stylesheet" type="text/css">
<script>
$(function(){
	
	$('.todos_master').change(function(){
	
	 var checked = $(this).attr('checked');
	 var master  = $(this).val();
	 
	 alert(master);
	 if(checked){
		 
		$('.master_'+master).attr('checked',true) ;	 
	 }else {
		 	$('.master_'+master).attr('checked',false) ;	
		 
	 }
	
		
	});
});
</script>


<style>
.acoes{

width:400px;
height:auto;
background-color:#E1F0F0;
margin-left:10px;
margin-top:0;
	
}
.acoes h4{ margin:0;}
</style>
</head>
<body>
<div id="corpo">
	<div id="conteudo">
		
		<form action='' method='post' name='form1' onSubmit="return validaForm()"  enctype='multipart/form-data'>		
		<h3>Gerenciamento de Acesso a Intranet</h3>
	
        <table>
        	<?php
		
		////CONTROLE DE ACESSO AS REGIõES
		$array_status = array(1 => 'REGIÕES ATIVAS', 0 => 'REGIÕES INATIVAS');
		
		foreach($array_status as $status=> $nome_status) {
		?>
		<tr>
			<td bgcolor="#EFEFEF" align="center" valign="top"><?php echo $nome_status; ?></td>
		    <td  colspan="6">
		            <table width="100%" cellspacing="0">
		            <?php
					
					
					if($status == 0) {
						$qr_regioes = mysql_query("SELECT * FROM regioes 
											       INNER JOIN master ON master.id_master = regioes.id_master
												   WHERE regioes.status = '$status' OR regioes.status_reg = '$status' ORDER BY master.id_master");			
					} else {
		            	$qr_regioes = mysql_query("SELECT * FROM regioes 
												   INNER JOIN master ON master.id_master = regioes.id_master
												   WHERE regioes.status = '$status' AND regioes.status_reg = '$status' ORDER BY master.id_master");
					}						
								
		            while($row_regioes = mysql_fetch_assoc($qr_regioes)):
					
					if($row_regioes['id_regiao'] == 38 and $row_regioes['id_regiao'] == 16) continue;
		                
		                if($row_regioes['id_master'] != $master_anterior) {
							 echo '<tr  bgcolor="#C7E2E2"><td align="left">'.$row_regioes['nome'].' 
							 <span style="float:right;"> <input name="todos_master"  type="checkbox" value="'.$row_regioes['id_master'].'_'.$status.'" class="todos_master"  />Marcar/Desmarcar todos </span>
							  </td>
							  </tr>'; 
					    }            
						?>    
							
		                <tr bgcolor="#D9ECFF">
								<td>
									<input type="hidden" name = "id_master"       value="<?php echo $row_regioes['id_master']; ?>" class="id_master"  />
									<input type="hidden" name = "id_funcionario"  value="<?php echo $id_user;?>" 					class="id_funcionario"/>											
									<input name="acesso_regiao" type="checkbox"   value="<?php echo $row_regioes['id_regiao']; ?>"   class="master_<?php echo $row_regioes['id_master']; ?>"/>
									<?php echo $row_regioes['id_regiao'].' - '.($row_regioes['regiao']); ?>
								</td>
		     				</tr>
					<?php
					  $master_anterior = $row_regioes['id_master'];	
					   
		            endwhile;					
					echo '<tr><td>&nbsp;</td></tr>';		            
					unset($master_anterior);
					?>  
		             </table>
		    </td>
		</tr>
		<tr><td>&nbsp;</td></tr>
        
		<?php } //fim foreach
		///////////////////////////////////////

		
		
		
		
		?>
				</table>
		
		
        
        
       
        
        
     <table>  
		<?php
		
		$qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE 1");
			while($row_pagina= mysql_fetch_assoc($qr_botoes_pg)): 
			
			
			
			echo '<tr bgcolor="#EFEFEF">
					<td colspan="8" align="center"><strong>'.$row_pagina['botoes_pg_nome'].'</strong><br></td>
					</tr>';
					

				///PERMISSÔES PARA OS RELATÓRIOS DO FINANCEIRO( ABA RELATÓRIOS )
					if($row_pagina['botoes_pg_id'] == 3){
						
						echo '<tr>
								<td style="background-color: #EFEFEF;" align="center">PÁGINA INICIAL</td>
								<td>
							';
						
								$qr_acoes = mysql_query("SELECT * FROM acoes WHERE botoes_pagina_id = '$row_pagina[botoes_pg_id]'");
								while($row_acoes = mysql_fetch_assoc($qr_acoes)):
								
									echo "<input type='checkbox' name='acoes[]' value='".$row_acoes['acoes_id']."' /> ".$row_acoes['acoes_nome']."<br>";
									
								endwhile;
								}
						
						echo '</td></tr>';
						////////////////////////////////////////////*/

			
				$qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina = '$row_pagina[botoes_pg_id]'");
				while($row_botoes_menu = mysql_fetch_assoc($qr_botoes_menu)):
				
				$todos++;
				?> 
		        
				<tr>
				 <td height="30" bgcolor="FFF" align="center" valign="top" style="background-color: #F5F5F5" >
		         		
					<?php echo $row_botoes_menu['botoes_menu_nome']?><br><br><br>                    
                   <input type="checkbox" class="tipo_menu"  name="todos" value="<?php echo $todos;?>">Marcar/Desmarcar todos
                    
                </td>
		            
				 <td colspan="7">         
				<?php
				  $qr_botoes = mysql_query("SELECT * FROM botoes WHERE   botoes_menu = '$row_botoes_menu[botoes_menu_id]'  ORDER BY  botoes_menu ASC");
				  while($row_botoes = mysql_fetch_assoc($qr_botoes)):
					 
					 
				  			echo ' <input type="checkbox" name="botoes[]" value="'.$row_botoes['id_botoes'].'" > '.$row_botoes['botoes_nome']. '<br>';					      
						    gerar_btn_acoes($row_botoes['botoes_id'], $row_botoes['botoes_nome']);/////GERAR A PARTE DAS AÇÕES
					 
					 
							/////GESTÃO DE COMPRAS
							if($row_botoes['botoes_id'] == 8) {
											
											echo '<div>';
											
                                           echo '<input type="checkbox" name="botoes[]" value="'.$row_botoes['botoes_id'].'"/>'.$row_botoes['botoes_nome'].'<br>';
										   
										   echo 'ETAPAS DE COMPRA<br>';
										   
										                                               
		                                   
											$qr_acompanhamento = mysql_query("SELECT * FROM acompanhamento_compra WHERE status = 1") or die(mysql_error()); 
											while($row_acomp = mysql_fetch_assoc($qr_acompanhamento)):
												
													$verifica_acomp = mysql_num_rows(mysql_query("SELECT * FROM func_acompanhamento_assoc WHERE id_funcionario = '$id_user' AND id_acompanhamento = '$row_acomp[acompanhamento_id]'" )) ;		
													$checked 		= ($verifica_acomp != 0)? 'checked="checked"': '';	
													
														echo '<input type="checkbox" name="acomp_compra[]" value="'.$row_acomp['acompanhamento_id'].'" '.$checked.'/> '.$row_acomp['acompanhamento_nome'].'<br>';	
											endwhile;										
											
											
												echo '</div>';
																
									}   ////////////FIM BOTÃO 8
									
																		
									endwhile;

		endwhile;		
		
		
endwhile; 

?>
		        
		        
		      </table></td></tr></form>
              </table>
</div>
</div>

</body>
</html>
<?php

?>
