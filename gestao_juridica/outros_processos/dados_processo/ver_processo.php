<?php
include ("../../include/restricoes.php");
include('../../../conn.php');
include('../../../upload/classes.php');
include('../../../classes/funcionario.php');
include('../../../classes/formato_data.php');
include('../../../classes/formato_valor.php');
$Fun = new funcionario();
$Fun -> MostraUser(0);
$Master = $Fun -> id_master;




////ANEXAR ANDAMENTOS 
if(isset($_POST['enviar']) and $_POST['tipo'] == 'andamentos'){
	


$id_processo      = mysql_real_escape_string($_POST['id_processo']);	
$data_movimento   = implode('-', array_reverse(explode('/',$_POST['data_movimento'])));
$status_processo  = mysql_real_escape_string($_POST['status_processo']);
$valor 		  	  =  str_replace(',','.',str_replace('.','',$_POST['valor']));		
$data_pg 		  = implode('-',array_reverse(explode('/',$_POST['data_pg'])));	
$n_parcelas		  = mysql_real_escape_string($_POST['n_parcelas']);
$horario 		  = $_POST['horario'];




$status_tipo = array(7,8,9,10); 

if(!in_array($status_processo, $status_tipo)) {

	unset($valor, $data_pg, $n_parcelas);
}



$qr_insert = mysql_query("INSERT INTO proc_trab_andamento (proc_id, 	proc_status_id, andamento_data_movi,andamento_horario, andamento_valor,andamento_data_pg, andamento_parcelas,   andamento_data_cad, andamento_usuario_cad, andamento_status)
																		VALUES ('$id_processo', '$status_processo',  '$data_movimento','$horario','$valor', '$data_pg', '$n_parcelas',  NOW(), '$_COOKIE[logado]',1)") or die(mysql_error());
$id_andamento = mysql_insert_id();	
header('Location: anexar_doc_andamentos.php?id_processo='.$_POST['id_processo'].'&id_andamento='.$id_andamento);


}

////////////////////////////////////////////////////////////////////////////////////   MOVIMENTOS
if(isset($_POST['enviar']) and $_POST['tipo'] == 'movimentos'){
$id_processo      = mysql_real_escape_string($_POST['id_processo']);	
$data_movimento   = implode('-', array_reverse(explode('/',$_POST['data_movimento'])));

$qr_andamentos 	= mysql_query("SELECT * FROM proc_trab_andamento WHERE proc_id = '$id_processo' AND andamento_status = 1 ORDER BY proc_status_id  DESC");
$row_andamentos = mysql_fetch_assoc($qr_andamentos);



//$valor 		  	  =  str_replace(',','.',str_replace('.','',$_POST['valor']));		
//$data_pg 		  = implode('-',array_reverse(explode('/',$_POST['data_pg'])));	
//$n_parcelas		  = mysql_real_escape_string($_POST['n_parcelas']);
$documento        = $_FILES['documento'];
$obs 			  = $_POST['obs'];

////////////////
$status_tipo = array(7,8,9,10); 
if(!in_array($status_processo, $status_tipo)) {

	unset($valor, $data_pg, $n_parcelas);
}
//////////////////
$qr_insert = mysql_query("INSERT INTO proc_trab_movimentos (proc_id, proc_status_id, data_movimento,   obs, data_cad, user_cad, status)
						VALUES ('$id_processo', '$row_andamentos[proc_status_id]', '$data_movimento', '$obs',  NOW(), '$_COOKIE[logado]',1)") or die(mysql_error());

$id_movimento = mysql_insert_id();		
header('Location: anexar_doc_movimentos.php?id_processo='.$_POST['id_processo'].'&id_movimento='.$id_movimento);

}






// Obtendo o id do cadastro
$id_processo = mysql_real_escape_string($_GET['id_processo']);
$qr_processo = mysql_query("SELECT * FROM processos_juridicos WHERE proc_id = '$id_processo'");
$row_processo = mysql_fetch_assoc($qr_processo);

$qr_tipo_processo = mysql_query("SELECT * FROM processo_tipo WHERE proc_tipo_id = '$row_processo[proc_tipo_id]'");
$row_tipo_proc = mysql_fetch_assoc($qr_tipo_processo);



$id = 1;
$id_bol = $_REQUEST['bol'];
$id_pro = $_REQUEST['pro'];
$id_reg = $_REQUEST['regiao'];

$id_user = $_COOKIE['logado'];

$sql_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($sql_user);

$result = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS nova_data, date_format(data_saida, '%d/%m/%Y') AS data_saida2, date_format(dataalter, '%d/%m/%Y') AS dataalter2 FROM autonomo WHERE id_autonomo = '$row_processo[id_autonomo]'");
$row = mysql_fetch_array($result);




$result_tab = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_processo[id_projeto]' ");
$row_tab = mysql_fetch_array($result_tab);

$sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
$row_user2 = mysql_fetch_array($sql_user2);

$result_ban = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$id_reg' and id_projeto = '$id_pro'");

if($row['status'] == '0') {
	$texto = "<font color=red>Data de saída: $row[data_saida2]</font>";
} else {
	$texto = NULL;
}

$nome_arq = str_replace(' ', '_', $row['nome']);	

$ano_cad = substr($row['data_cad'],0,4);

if($ano_cad <= '2008') {
	$coluna_foto = $row['id_bolsista'];
} else {
	$coluna_foto = $row['0'];
}

if($row['foto'] == "1") {
	$nome_imagem = $row_processo['id_regiao'].'_'.$row_processo['id_projeto'].'_'. $row_processo['id_autonomo'].'.gif';
} else {
	$nome_imagem = "semimagem.gif";
}
?>
<html>
<head>
<title>:: Intranet ::</title>
<link rel='shortcut icon' href='favicon.ico'>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../../rh/css/estrutura_participante.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>

<script type="text/javascript" src="../../../uploadfy/scripts/swfobject.js"></script>
<script type="text/javascript" src="../../../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>
<script type="text/javascript" src=".../../../js/shadowbox.js"></script>

<link href="../../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../../js/highslide-with-html.js"></script> 

<script type="text/javascript" src="../../../jquery/validationEngine/jquery.validationEngine-pt.js"></script>
<script type="text/javascript" src="../../../jquery/validationEngine/jquery.validationEngine.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jquery/validationEngine/validationEngine.jquery.css" />

<link rel="stylesheet" type="text/css" href="../../../uploadfy/css/default.css" />
<link rel="stylesheet" type="text/css" href="../../../uploadfy/css/uploadify.css" />

<script type="text/javascript" src="../../../jquery/priceFormat.js"></script>

<script type="text/javascript">
$().ready(function(){
		$('#data_movimento').mask('99/99/9999');
		$('#data_movimento2').mask('99/99/9999');
	$('#data_pg').mask('99/99/9999');
	$('#horario').mask('99:99');
	$('#valor').priceFormat({
		
		prefix:'',
		centsSeparator:',',
		thousandSeparator:'.',
		
		
		});
	
	$('#form').validationEngine();
	
	
	$('#status_processo').change(function(){
		
		var valor = $(this).val();
		
		if(valor == 7  ||  valor == 8 || valor == 9 || valor == 10) {
			
			$('#outros').fadeIn();
		$('#campo_horario').fadeOut();
		
		} else if(valor != 22) {
			$('#outros').fadeOut();
			$('#campo_horario').fadeIn();
		} else {
			
		$('#outros').fadeOut();
			
		}
		
	
	});	
			
});
</script>
</head>
<body>
<div id="fileQueue"></div>
<div id="corpo">
<div id="conteudo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
  <td>
  <div style="float:right;"><?php include('../../../reportar_erro.php'); ?></div>
  <div style="clear:right;"></div>
  
  <?php if($_GET['sucesso'] == "cadastro") { ?>
  <div id="sucesso">
       Participante cadastrado com sucesso!
  </div>
  <?php } ?>
  <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
       <h2 style="float:left; font-size:18px;">PROCESSO <?php echo $row_tipo_proc['proc_tipo_nome']; ?>: <?=$row_processo['proc_nome']?> 
       </h2>
     <span style="float:right"><a href="../../index.php?regiao=<?php echo $id_reg;?>"><<< Voltar</a></span>
       <div class="clear"></div>
  </div></td>
  </tr>
  <tr>
   
    <td width="100%" bgcolor="#F3F3F3" valign="top">
            <table  width="100%"style="font-size:13px; line-height:22px;">
                                 
                  
                        <tr>
                            <td  colspan="4"><strong>Nº do processo: </strong>
                            <?php 
							$qr_n_processo  = mysql_query("SELECT * FROM n_processos WHERE proc_id = '$id_processo' ");
							
							$total = mysql_num_rows($qr_n_processo);
							while($row_n_processo = mysql_fetch_assoc($qr_n_processo)):
			
							 echo $row_n_processo['n_processo_numero'];
							 
							 if($cont++ < $total-1){
							 echo ',';
							 }
							endwhile;
							unset($cont);   
							
							?>    
                            </td>
                        </tr>  
                         <tr>
                            <td colspan="4">
                            <strong>Região:</strong> 
							<?php 
							echo mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_processo[id_regiao]'"),0);
							
							?>
                            </td>
                        </tr> 
                        <tr>
                            <td colspan="4"><strong>Local:</strong> <?php echo $row_processo['proc_local'].'xxxx';?></td>
                        </tr>
                        
                        <tr>
                            <td colspan="4"><strong>PARTES:</strong> </td>
                        </tr>
                         <tr>
                            <td colspan="4"><?php echo $row_processo['proc_nome'];?> </td>
                        </tr>
                         <tr>
                            <td colspan="4"><?php echo $row_processo['proc_nome1'];?> </td>
                        </tr>
                         <tr>
                            <td colspan="4"><?php echo $row_processo['proc_nome2'];?> </td>
                        </tr>
                        
                        
                         <tr>
                         
                         <td valign="top" width="50"><strong>Advogados:</strong></td>
                            <td valign="top">
                            <?php 
							if(!empty($row_processo['adv_id'])) {
								$id_advogados =  $row_processo['adv_id'];
								$qr_adv = mysql_query("SELECT * FROM advogados WHERE adv_id IN ($id_advogados)") or die(mysql_error());
								
								if(mysql_num_rows($qr_adv) != 0){
								while($row_advogado = mysql_fetch_assoc($qr_adv)):
								
									echo $row_advogado['adv_nome'].'<br>';
								
								endwhile;
								}
							} else {
								echo 'Nenhum advogado designado.';
							}
                            ?>
                            </td>
                            
                            
                            
                            <td valign="top" align="right"><strong>Preposto:</strong></td>
                            <td valign="top">
                            <?php 
							if(!empty($row_processo['preposto_id'])) {
								$id_preposto =  $row_processo['preposto_id'];
								
								
								$qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_id IN ($id_preposto)")or die(mysql_error());
								
								if(mysql_num_rows($qr_preposto) != 0){
								
									while($row_preposto = mysql_fetch_assoc($qr_preposto)):
									
									echo $row_preposto['prep_nome'].'<br>';
								
								endwhile;
								}
							} else {
								echo 'Nenhum preposto designado.';
							}
							?>
                            </td>
                            
                        </tr>     
                </table>   
    </td>
  </tr>
  
    <tr>
    <td>&nbsp;</td>
  </tr>
  <!--
   <tr>
    <td colspan="2" ><h1><span>MENU DE EDIÇÃO</span></h1></td>
  </tr>
  
  <tr>
  <td colspan="2" class="menu">
  
  <a href="../../../cooperativas/fichadecadastro.php?bol=<?=$row_processo['id_autonomo']?>&pro=<?=$row_processo['id_projeto']?>&id_reg=<?=$row_processo['id_regiao']?>" class="botao" target="_blank">Ver Ficha</a>
    <a href="../../../cooperativas/fichadecadastro.php?bol=<?=$row_processo['id_autonomo']?>&pro=<?=$row_processo['id_projeto']?>&id_reg=<?=$row_processo['id_regiao']?>" class="botao" target="_blank">Eventos</a>
  
  
  <?php // Consulta para Links
      
		
		
		
		
		
       switch($row_processo['proc_tipo_contratacao']) {
		   // Links para Autonomos
		   case 1: ?>
       
       <!-- linha 1 -->
       <p><?=$botao_editar?>
         

          

      <?php // Links para Cooperados
	        break;
			case 3: ?>
      
        
      <!-- linha 1 -->
      <p><?=$botao_editar?>
	  
     
	  <?php 
	  
	   //verifica se o projeto está desativado
	
	  
	  ?>  
     
     </p>
       
       
       
      <?php // Links para PJ
	        break;
			case 4: ?>
            
      <!-- linha 1 -->
      <p><?=$botao_editar?>
      
      <?php 

	  
	  ?>     
    
         <a href="cooperativas/fichadecadastro.php?bol=<?=$row[0]?>&pro=<?=$id_pro?>&id_reg=<?=$id_reg?>" class="botao" target="_blank">Ver Ficha</a></p>
           
     <?php } ?>

  </td>      -->
</table>
  
  </td>
  </tr>
  <tr>
	     <td colspan="2" align="left" ><h1 style="text-align:left"><span>PEDIDO DA AÇÃO</span></h1></td>
	 </tr>
     <tr>
     	<td colspan="2" align="left">
        	<div style="width:100%;height:auto;display:block;background-color:#F4F4F4;text-align:left;padding: 5px; text-transform:uppercase;">
        	<?php echo $row_processo['pedido_acao']; ?>
        	</div>
        </td>
     </tr>
      
  
    <tr>  
    	
        <td>
          <tr>
 			 <td colspan="2" align="left" ><h1 style="text-align:left"><span>ATUALIZAR ANDAMENTO</span></h1></td>
 		 </tr>
        
       <form name="form" id="form" method="post" action="ver_processo.php" enctype="multipart/form-data" > <table width="100%" style="font-size:12px;">
        	<tr>
            	<td>Status do processo:</td>
                <td colspan="4">
                <select name="status_processo" id="status_processo" class="validate[required]">
                <option value="">Selecione uma opção...</option> 
                <option value=""></option> 
					<?php 	
                    $qr_status = mysql_query("SELECT * FROM processo_status WHERE proc_status_id != 1");
                    while($row_status  = mysql_fetch_assoc($qr_status)):				
                    ?>
                      <option value="<?php echo $row_status['proc_status_id']?>"> <?php echo $row_status['proc_status_nome']?></option>                 
                    
                    
                    <?php
                    endwhile;
                    ?> 
                </select>               
                </td>
        	</tr>
            
            
            <tr id="campo_horario" style="display:none;">
            	<td>Horário:</td>
                <td colspan="4"><input type="text" name="horario" id="horario" size="5"/></tr>
            </tr>
            <tr id="outros" style="display:none;">
            	<td>Valor da parcela:</td>
                <td><input type="text" name="valor" id="valor"/></td>
        
                 <td>Data de pagamento</td>
                 <td><input type="text" name="data_pg" id="data_pg"/></td>
                 
                  <td>Número de parcelas</td>
            	 <td><input type="text" name="n_parcelas" id="n_parcelas" size=5/></td>
          	</tr>
            
            
            <tr>
            	<td>Data do movimento:</td>
            	<td colspan="4"><input name="data_movimento"  id="data_movimento"type="text" class="validate[required]"/></td>
            </tr>
         
            
            <tr>
            	<td colspan="4" align="center">
                	 <input name="tipo" type="hidden" value="andamentos"/>
                    <input name="id_processo" type="hidden" value="<?php echo $id_processo?>"/>
                	<input name="enviar" type="submit" value="Enviar"/>
                </td>
            </tr>
            
        </table>
        </form>
        </td>
    </tr>
  
  
   
	<!-------------MOVIMENTOS ----------------------->
	 <tr>
     	 <td colspan="2" align="left" ><h1 style="text-align:left"><span>ATUALIZAR MOVIMENTOS</span></h1></td>
 	 </tr>
        
       <form name="form" id="form" method="post" action="ver_processo.php" enctype="multipart/form-data" >
        <table width="100%" style="font-size:12px;">
        	       
            <tr>
            	<td>Data do movimento:</td>
            	<td colspan="4"><input name="data_movimento"  id="data_movimento2"type="text" class="validate[required]"/></td>
            </tr>            
            <tr>
            	<td>Observações</td>
           		<td colspan="4">  <textarea name="obs" rows="5" cols="40"></textarea> </td>
            </tr>        
                      
            <tr>
            	<td colspan="4" align="center">
                    <input name="id_processo" type="hidden" value="<?php echo $id_processo?>"/>
                    <input name="tipo" type="hidden" value="movimentos"/>
                	<input name="enviar" type="submit" value="Enviar"/>
                </td>
            </tr>
            
        </table>
        </form>
        </td>
    </tr>
  
   <tr>
  	<td colspan="2"><h1 style="text-align:left"><span>ANDAMENTOS E MOVIMENTOS</span></h1></td>
  </tr>
  <tr>
  <td colspan="2">
  
  <table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:13px;">
      <tr bgcolor="#dddddd">
        <td width="70%"><strong>DESCRIÇÃO</strong></td>
        <td>ANEXO</td>
        <td>PARCELAS</td>
        <td>EDITAR</td>
        <td>EXCLUIR</td>
       
      </tr>
     <?php
	  
	  $status_id = array(7,8,9,10);
	  
	 	$qr_status = mysql_query("SELECT * FROM processo_status WHERE 1  ORDER BY ordem");
		while($row_status = mysql_fetch_assoc($qr_status)):
		
			$qr_processo2 = mysql_query("SELECT * FROM  proc_trab_andamento WHERE   proc_status_id  = '$row_status[proc_status_id]' AND proc_id = '$id_processo' AND andamento_status = 1") or die(mysql_error());
			while($row_processo2 = mysql_fetch_assoc($qr_processo2)):
			$i++;	
			
			//////pegando os movimentos
			$qr_movimentos = mysql_query("SELECT *  FROM  proc_trab_movimentos WHERE proc_id = '$id_processo' AND proc_status_id = '$row_status[proc_status_id]' AND status = 1") or die (mysql_error());
			
					 	?>
						
							<tr bgcolor="#E4E4E4">
							
								<td><img src="../../../img_menu_principal/seta_azul.png" /> 
								<?php 
								echo $row_status['proc_status_nome']?> em <?php echo formato_brasileiro($row_processo2['andamento_data_movi']);
								
								if($row_processo2['andamento_horario'] != '00:00:00') {
									echo ' as '.substr($row_processo2['andamento_horario'],0,5).'h' ;
								}
								
								?>
                                
                                </td>
							 <td  align="center">
                            <?php 
							
                              $qr_anexo_andamento = mysql_query("SELECT * FROM  proc_andamento_anexo  WHERE andamento_id = '$row_processo2[andamento_id]'");
							 if(mysql_num_rows($qr_anexo_andamento) !=0) {
							 ?>                             
                             	<a href="anexo_trab.php?id_andamento=<?php echo $row_processo2['andamento_id']?>" OnClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">
                                <img src="../../../imagens/ver_anexo.gif" width="20" height="20"/>
                                </a>                                
                             
                             <?php }  ?>
                             
                             
                             </td> 
                             <td></td>
                                <td>
                                   <a href="editar_andamento.php?id_processo=<?php echo $id_processo?>&id=<?php echo $row_processo2['andamento_id'];?>" >
                                <img src="../../../imagens/editar_projeto.png"  width="20" height="20"/> </a>
                                </td>
                                
                                <td align="center">
                                <?php
                                if($row_status['proc_status_id'] != 1) {
								?>
                                
                                <a href="../excluir_and_mov.php?id_processo=<?php echo $id_processo?>&id=<?php echo $row_processo2['andamento_id'];?>&tp=2" onClick="return(confirm('Deseja excluir o movimento : <?php echo $row_status['proc_status_nome'] ?>?'))">
                                <img src="../../../imagens/excluir.png"  width="20" height="20"/> </a>
                                
                                <?php
								}
								?>
                                </td>
							</tr>
						
						<?php
					
					
				  /////MOVIMENTOS
					   while($row_movimentos = mysql_fetch_assoc($qr_movimentos)):
					   ?>
					   	<tr style="background-color: #F7F7F7;">
                        	<td> 
                            	 &nbsp;&nbsp;&nbsp;
                            	 <img src="../../../img_menu_principal/seta_vermelha.png" />
                                 <?php echo formato_brasileiro($row_movimentos['data_movimento']); ?>: <?php echo $row_movimentos['obs']; ?>
                            </td>
                            <td align="center">
                            <?php
							$qr_mov_anexos = mysql_query("SELECT * FROM proc_trab_mov_anexos WHERE 	proc_trab_mov_id = '$row_movimentos[proc_trab_mov_id]'");
							
							if(mysql_num_rows($qr_mov_anexos) !=0)	{?>				
                            
                                <a href="anexo_movimentos.php?id_movimento=<?php echo  $row_movimentos['proc_trab_mov_id']?>" OnClick="return hs.htmlExpand(this, { objectType: 'iframe' } )">
                                 <img src="../../../imagens/ver_anexo.gif" width="20" height="20"/>
                                </a>
                                <?php } ?>
                            </td>
                            <td></td>
                              <td>
                                   <a href="editar_movimento.php?id_processo=<?php echo $id_processo?>&id=<?php echo $row_movimentos['proc_trab_mov_id'];?>" >
                                <img src="../../../imagens/editar_projeto.png"  width="20" height="20"/> </a>
                                </td>
                            <td align="center">  
                            
                             <a href="../excluir_and_mov.php?id_movimento=<?php echo $row_movimentos['proc_trab_mov_id']; ?>&id_processo=<?php echo $id_processo?>" onClick="return(confirm('Deseja excluir o movimento?'))">
                                <img src="../../../imagens/excluir.png"  width="20" height="20"/> 
                                	</a>
                                    
                                    
                                    </td>
                        </tr>
                        
					   <?php  endwhile;  
					   //////////////////////////	 
			endwhile;
			
	 	endwhile;
	 ?>
    </table>
    
    </td>
    </tr>
    
</table>
</div>
       <div id="rodape">
<?php $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
      $master = mysql_fetch_assoc($qr_master);
	  ?>
            <p class="left"><img style="position:relative; top:7px;" src="../../../imagens/logomaster<?=$Master?>.gif" width="66" height="46"> <b><?=$master['razao']?></b>&nbsp;&nbsp;Acesso Restrito à Funcion&aacute;rios</p>
            <p class="right"><br><br><a href="#corpo">Subir ao topo</a></p>
            <div class="clear"></div>
  </div>
</div>
<script type="text/javascript">
var Accordion1 = new Spry.Widget.Accordion("Accordion1", { enableAnimation: false, useFixedPanelHeights: false, defaultPanel: -1 });
</script>
</body>
</html>