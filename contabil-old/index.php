<?php 
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";
include("../classes_permissoes/regioes.class.php");




$id_user   = $_COOKIE['logado'];
$regiao    = $_GET['regiao'];
$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);
$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);
$tipo_user         = $row_funcionario['tipo_usuario'];
$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$regiao'");
$id_master         = @mysql_result($query_master,0);

$REGIOES = new Regioes();

/*
//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkfo = encrypt("$regiao&1"); 
$linkfo = str_replace("+","--",$linkfo);
// -----------------------------

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkevento = encrypt("$regiao"); 
$linkevento = str_replace("+","--",$linkevento);

//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkferias = encrypt("$regiao&1"); 
$linkferias = str_replace("+","--",$linkferias);
// -----------------------------*/


/*Resumo*/



/*Resumo*/

// Bloqueio Administração

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o Cont&aacute;bil</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	$('#botoes ul li img').fadeTo('fast', 0.7).hover(function(){$(this).fadeTo('fast', 1.0)},function(){$(this).fadeTo('fast', 0.7)});
	$("#resumo table tr:odd").addClass('linha_dois');
	$("#resumo table tr:even").addClass('linha_um');
	$('#resumo').find('table').find('tr:first').addClass('titulo_table');
	
	
	$("ul.tabs").tabs("div.panes > div");
	
$('#regiao').change(function(){
	
	var id_regiao = $(this).val();
	location.href =	'index.php?regiao='+id_regiao;
	
	
	
	
	})

});	



</script>
<link rel="stylesheet" type="text/css" href="../css_principal.css"/>

</head>
<body class="fundo_contabil">
<div id="corpo">
	<div id="conteudo">
                <div id="topo">
                    
                        <table width="100%">
                            <tr>
                                <td width="11%" height="81" align="center"> <img src="../imagens/logomaster<?=$id_master?>.gif" width="110" height="79"></td>
                                <td width="36%" align="left" valign="top">
                                    <br />
                                  <span>Gest&atilde;o Contábil</span><br />
                                  <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
                                  <strong>Data:</strong> <?php echo date('d/m/Y'); ?><br />
                                  <strong>Regiao:</strong> <?php echo $rw_regiao[1]; ?></td>
                                <td width="53%" align="right"><table width="0" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                   <td align="left" width="60" style="margin-right:10px;"><?php include('../reportar_erro.php'); ?></td>
                                    <td>
                                <?php // Visualizando Regiões
                                      if($tipo_user == '1' or $tipo_user == '4') : ?>
                                    <span id="labregiao1">
                                    <select name="regiao" class="campotexto" id="regiao" onchange="MM_jumpMenu('parent',this,0)">
                                <?php echo $REGIOES->Preenhe_select_por_master($id_master,$regiao); ?>
                                
                                </select>
                                </span>
                                <?php endif; // Fim de Regiões?>
                                   </td>
                                   
                                  </tr>
                              </table></td>
                            </tr>
                        </table>
                    
                </div>
                
	 <div id="menu_principal">

      <ul class="tabs">
           
   <?php
				
	$qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 5");
	while($row_pagina= mysql_fetch_assoc($qr_botoes_pg)): 	
	
			$qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'  ORDER BY botoes_menu_id ");
			while($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
				
					  $qr_verifica = mysql_query("SELECT * FROM botoes 
												INNER JOIN botoes_assoc 
												ON botoes.botoes_id = botoes_assoc.botoes_id
												WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
				?>
							
						   <li <?php if(mysql_num_rows($qr_verifica) == 0) echo 'style="display:none;"'; ?>>
								<a href="#">
								<div class="sombra1"> <?php echo $row_btn_menu['botoes_menu_nome'];?>   <div class="texto">  <?php echo $row_btn_menu['botoes_menu_nome'];?> </div>      </div>
							  </a>
						  </li>           
           <?php 
		   	endwhile;
	endwhile;
		    ?>
      </ul>
   
				  		
		
    </div>
         
 
    
    <div id="submenu"  class="panes">
     
			<?php 
												
	$qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 5 ");
	while($row_pagina= mysql_fetch_assoc($qr_botoes_pg)): 	
	
			$qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'    ORDER BY botoes_menu_id ");
			while($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
						
															
					  ?>
					
					<div class="conteudo_aba" style="display:none;"> 
					
						 <table width="100%" >
								<tr>
									<td class="titulo_tabela">
										<div class="sombra1"> <?php  echo $row_btn_menu['botoes_menu_nome'];?>                                               
										 <div class="texto"> <?php echo $row_btn_menu['botoes_menu_nome'];?></div>              
									  </div>
									</td>
								</tr>
						</table>
						
						 <ul>
						
							 <?php			
							 $qr_botoes = mysql_query("SELECT * FROM botoes 
																	INNER JOIN botoes_assoc 
																	ON botoes.botoes_id = botoes_assoc.botoes_id
																	WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
							while($row_botoes = mysql_fetch_assoc($qr_botoes)) :
								
								if($row_botoes['botoes_id'] == '98'){
									
									echo '<li> 
										   <a href="'.$row_botoes['botoes_link'].'?m='.$Master.'&regiao='.$regiao.'" title="'.$row_botoes['botoes_descricao'].'">
												<img src="'.$row_botoes['botoes_img'].'"/><br>
												'.$row_botoes['botoes_nome'].'
										 </a>
									 </li>';	
									} else {
								
							    ?>
									  
									   <li> 
										   <a href="<?=$row_botoes['botoes_link'].$regiao?>" title="<?=$row_botoes['botoes_descricao']?>">
												<img src="<?=$row_botoes['botoes_img']?>"/><br />
                                                
                                                <?=$row_botoes['botoes_nome']?>
										 </a>
									 </li>		
									 
							<?php }
							 endwhile; ///fim loop   ?>
					  </ul>
		</div> 

		<?php 
        
                    endwhile;
        endwhile;
        ?>

</div>
       
          
   	 <div class="clear"></div> 
     
   <div class="rodape2">
     
     <?php
     $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
          $master = mysql_fetch_assoc($qr_master); ?>
     <?=$master['razao']?>
     &nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS    
  </div>
 
          
   </div>
 </div>
</body>
</html>