<?php
if(empty($_COOKIE['logado'])){
print "<script>location.href = 'login.php?entre=true';</script>";
}  


include('conn.php');
include "funcoes.php";
include('classes_permissoes/regioes.class.php');
include('classes_permissoes/master.class.php');
include "wfunction.php";

$id_user     = $_COOKIE['logado'];
$sql         = "SELECT * FROM funcionario where id_funcionario = '$id_user'";
$result_user = mysql_query($sql, $conn);

$row_user    = mysql_fetch_array($result_user);

$result_regi = mysql_query("SELECT * FROM regioes where id_regiao = '$row_user[id_regiao]'", $conn);
$row_regi    = mysql_fetch_array($result_regi);
$regiao 	 = $row_regi['id_regiao'];

$grupo_usuario   = $row_user['grupo_usuario'];
$regiao_usuario  = $row_user['id_regiao'];
$apelido_usuario = $row_user['nome1'];
$tipo_user       = $row_user['tipo_usuario'];
$id_master       = $row_user['tipo_usuario'];

$qrEmails = "SELECT * FROM funcionario_email_assoc WHERE id_funcionario = '{$id_user}'";
$rsEmails = mysql_query($qrEmails);

if($id_user == '5' or $id_user == '32'){
//-------------VERIVICANDO AS CONTAS PARA HOJE------------------
$result_jr = mysql_query("SELECT * FROM saida where id_regiao = '$regiao_usuario' and status = '1'
and data_vencimento = '$ano-$mes_h-$dia_h' ORDER BY data_vencimento");
$result_banco_jr = mysql_query("SELECT * FROM bancos where id_regiao='$regiao_usuario' and saldo LIKE '-%'");
$linha_jr = mysql_num_rows($result_jr);
$linha_banco_jr = mysql_num_rows($result_banco_jr);
if($linha_jr > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr CONTA(S) A PAGAR HOJE');</script>";
}else{
}
if($linha_banco_jr > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_banco_jr SALDO(S) NEGATIVO(S)');</script>";
}
}
if($id_user == '3' or $id_user == '27'){
//-------------VERIVICANDO SE EXISTEM PEDIDOS DE COMPRAS------------------
$result_jr2 = mysql_query("SELECT * FROM compra where acompanhamento = '1' and status_reg = '1'");
$linha_jr2 = mysql_num_rows($result_jr2);
if($linha_jr2 > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $linha_jr2 SOLICITAÇÕES DE COMPRA');</script>";
}
}
//-----------VERIFICANDO SE EXISTE ALGUM CHAMADO RESPONDIDO OU COMBUSTIVEL ACEITO-------------------
$result_chamado = mysql_query("SELECT id_suporte FROM suporte where user_cad = '$id_user' and status = '2'");
$cont_chamado = mysql_num_rows($result_chamado);
if($cont_chamado > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\nVOCÊ POSSUI $cont_chamado CHAMADOS RESPONDIDOS NO SUPORTE ON-LINE');</script>";
}
/*
$RECom1 = mysql_query("SELECT id_combustivel FROM fr_combustivel WHERE status_reg = '2' and id_user = '$id_user'");
$ContCom1 = mysql_num_rows($RECom1);
if($ContCom1 > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContCom1 PEDIDOS DE COMBUSTIVEL LIBERADOS');</script>";
}
*/
if($id_user == '9' or $id_user == '1'){
//-----------VERIFICANDO SE EXISTE ALGUM CHAMADO RESPONDIDO-------------------
$result_chamado = mysql_query("SELECT id_suporte FROM suporte where status = '1' or status = '3'");
$cont_chamado = mysql_num_rows($result_chamado);
if($cont_chamado > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\n$cont_chamado CHAMADOS ABERTOS NO SUPORTE ON-LINE');</script>";
}
}
//-----------VERIFICANDO SE EXISTE ALGUM PEDIDO DE REEMBOLSO-------------------
if($id_user == '32' or $id_user == '27'){
$REReem= mysql_query("SELECT id_reembolso FROM fr_reembolso WHERE status = '1'");
$ContReem = mysql_num_rows($REReem);
if($ContReem > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContReem PEDIDOS DE REEMBOLSO EM ABERTO');</script>";
}
}
//-----------VERIFICANDO SE EXISTE ALGUM PEDIDO DE COMBUSTIVEL-------------------
if($id_user == '32' or $id_user == '27'){
$RECom= mysql_query("SELECT id_combustivel FROM fr_combustivel WHERE status_reg = '1'");
$ContCom = mysql_num_rows($RECom);
if($ContCom > "0"){
	print "<script type=\"text/javascript\">alert('..............ATENÇÃO..............\\n\\ $ContCom PEDIDOS DE COMBUSTIVEL EM ABERTO');</script>";
}
}


$data = date('d/m/Y');
$mes = date('m');

$q_master = mysql_query("SELECT * FROM master where id_master = '$row_regi[id_master]'", $conn);

$row_master_1    = mysql_fetch_array($q_master);

$cont_result = mysql_query("SELECT COUNT(*) FROM tarefa where usuario = '$apelido_usuario' and id_regiao = '$regiao_usuario' and status_tarefa = '1'  and status_reg = '1'", $conn);
$row_cont    = mysql_fetch_array($cont_result);


//-- ENCRIPTOGRAFANDO A VARIAVEL
$linkFolha = encrypt("$regiao"); 
$linkFolha = str_replace("+","--",$linkFolha);
// -----------------------------
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet ::</title>
<link rel="shortcut icon" href="favicon.ico" />
<link href="css_principal.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="js/lightbox.css" type="text/css" media="screen"/>
<link rel="stylesheet" type="text/css" href="js/highslide.css" />

<script type="text/javascript" src="js/highslide-with-html.js"></script>
<script type="text/javascript" src="js/ramon.js"></script>
<script src="jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script src="jquery/jquery.tools.min.js" type="text/javascript"></script>


<script language="JavaScript" type="text/JavaScript">

///HIGHSLIDE
 hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';
////




function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location=	'"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}

function popupfinanceiro(caminho,nome,largura,altura,rolagem) {
	var esquerda = (screen.width - largura) / 2;
	var cima = (screen.height - altura) / 2 -50;
	window.open(caminho,nome,'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + rolagem + ',resizable=yes,copyhistory=no,top=' + cima + ',left=' + esquerda + ',width=' + largura + ',height=' + altura);
}

//////////////////  JQUERY
$(function(){
	$.ajax({
		url: 'roundcube/index.php',
		successs: function(resposta){
			$('#email').html(resposta);
			
			}
		});
	
	
	
	
	
	$('tr.normal')
		.mouseover(function(){
			$(this).addClass('over');
		})
		.mouseout(function(){
			$(this).removeClass('over');
	});
	
	
	
	// setup ul.tabs to work as tabs for each div directly under div.panes
$("ul.tabs").tabs("div.panes > div");

$('#escolha_regiao').change(function(){
	
	var regiao = $(this).val();
	var regiao_de = $("#regiao_de").val();
	var user = $("#user").val();
	
	
		
		$.ajax({
			url: 'cadastro2.php?regiao='+regiao+'&regiao_de='+regiao_de+'&user='+user+'&id_cadastro=13',
		
			success: function(){
				location.href = 'index.php';
				}
			
			
			});
		
		
	
});

$('#escolha_master').change(function(){
	
	var master = $(this).val();
	var master_de = $("#master_de").val();
	var user = $("#user").val();
	
	
		
		$.ajax({
			url: 'cadastro2.php?master='+master+'&master_de='+master_de+'&user='+user+'&id_cadastro=26',		
			success: function(){
				location.href = 'index.php';
				}
			
			
			});
		
		
	
});

});

</script>




</head>
<body>

<div id="corpo">

	<div id="conteudo">

        <div id="topo">
                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" >
                      <tr>
                       <td align="center" valign="top">
                                <img src="imagens/logomaster<?=$row_user['id_master']?>.gif" width="110" height="79">
                        </td>
                        
                          <td  valign="top">
                            <div style="color:#333;font-size:12px;">
                            	<br />
                                <br />
                                Ol&aacute;,
                                <?php 
                                print "<br><span class='red'><b>$row_user[nome]</b></span>  <br>Data: <b>$data</b> <br>";
                                if ($tipo_user == "1" or $tipo_user == "4"){
                                print "você está visualizando a Região: <b>$row_regi[regiao]</b>" ;
                                }
                                ?>
                          </b><br>
                        </div>
                        </td>
                          <td width="132" align="center" valign="middle" style="display:none" >
                          
                                <span class="quadro_tarefas">
                                <?php echo $row_cont['0']; ?>
                                </span>
                          </td>
                          </tr>
                          <tr class="barra">
                          <td align="center">   
                                       
										
										<?php  // Visualizando Regiões NOVO   ?>                                        
                                            <span style="font-size:10px"> <strong>REGIÕES:</strong> </span>
                                            <select name="regiao" class="campotexto" id="escolha_regiao" >                                            
                                            <?php 												
											$a = new Regioes();											
											$a->Preenhe_select_por_master($row_user['id_master'], $regiao_usuario);											
											?>
                                        </select>  <br />                                                                             
                                        <input type="hidden" name="regiao_de" id="regiao_de" value="<?=$regiao_usuario?>"/>
                                        <input type="hidden" name="user"  id="user"  value="<?=$id_user?>"/>
                                        <input type="hidden" name="id_cadastro" value="13"/>
                                           
                        </td>
                        
                        <td>                  
                                                                      
                                        <?php  // TROCA MASTER NOVO    ?>
                                      		<span style="font-size:10px"><strong> EMPRESAS:</strong> </span>
                                            <select name="master" class="campotexto" id="escolha_master" >                                            
                                            <?php 												
											$obj_master = new Master();											
											$obj_master->Preenhe_master($row_user['id_master']);											
											?>
                                           </select>                                                                                 
                                            <input type="hidden" name="master_de" id="master_de" value="<?=$row_user['id_master']?>"/>                                        
                                            <input type="hidden" name="id_cadastro" value="26"/>
                                       
                          </td>
                          <td align="center"> <span class="sair"> <a href="logof.php" target="_parent" >SAIR  </a> </span></td>
                    </tr>
                    <tr><td colspan="3">&nbsp;</td></tr>
          </table>               
                  
            
        <?php
        /* Liberando o resultado */
        mysql_free_result($result_user);
        mysql_free_result($result_regi);
        mysql_free_result($cont_result);
        
       
        ?>
        
	</div>
<!-----------------------------------------    FIM TOPO  ---------------------------------------------------->

     
     
<!-----------------------------------------      MENU PRINCIPAL (ABAS)-------------------------------------------------->
   
    
    	  <div id="menu_principal">
                
             
                   
                  
                  <ul class="tabs">
            
			<?php
						
				$qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 1 ");
				while($row_pagina= mysql_fetch_assoc($qr_botoes_pg)): 	
				
                        $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]' ORDER BY botoes_menu_id ");
                        while($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
                        
                              $qr_botoes = mysql_query("SELECT * FROM botoes
							   
                                                        INNER JOIN botoes_assoc 
                                                        ON botoes.botoes_id = botoes_assoc.botoes_id
                                                        WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
                        ?>
                                    
                                   <li <?php if(mysql_num_rows($qr_botoes) == 0) echo 'style="display:none;"'; ?>>
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
    
    <!----=---------- FIM  MENU_ PRINCIPAL -------------->
    
      <!----=----------   SUBMENU (CONTEUDO DAS ABAS)-------------->          
   	<div id="submenu" class="panes" >
                
          <div class="conteudo_aba" style="display:none;">
		  
                <?php  include 'index_parte_todos.php'; ?>
          
          </div>
          
                    <?php 
												
                    $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 1 ");
                    while($row_pagina= mysql_fetch_assoc($qr_botoes_pg)): 	
	
                    $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'   AND botoes_menu_id != 1 ORDER BY botoes_menu_id ");
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
                                                            WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]' 
															AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  
															ORDER BY botoes.botoes_menu ASC");
							
							 
                              while($row_botoes = mysql_fetch_assoc($qr_botoes)):

									if($row_botoes['botoes_id'] == 33) {  //BOTÃO FOLHA DE PAGAMENTO COM A REGIAO CRIPTOGRAFADA
										?>
                                        
									  <li>                                       
                                         <a href="#"  onClick="window.open('<?=$row_botoes['botoes_link'].'&enc='.$linkFolha?>','<?=$row_botoes['botoes_nome'];?>','width=800,height=600,scrollbars=yes,resizable=yes')"  class="link"  title="<?php $row_botoes['botoes_descricao']?>">            
                                          <img src="<?=$row_botoes['botoes_img']?>" border="0" align="absmiddle"><br />
                                             <?=$row_botoes['botoes_nome']?>
                                        </a>
                                     </li>							
										
										<?php	}elseif($row_botoes['botoes_onclick'] == 2){  ?>
                                               
                                                 <li>                                       
                                                     <a href="<?=$row_botoes['botoes_link'].$regiao_usuario;?>"  onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"   class="link"  title="<?php echo $row_botoes['botoes_descricao']?>">            
                                                      <img src="<?=$row_botoes['botoes_img']?>" border="0"	><br />
                                                         <?=$row_botoes['botoes_nome']?>
                                                    </a>
                                                </li>  
                                                
                                        <?php }	else  {  ?>
                                        
                                        
                                                    <li>  
                                                         <a href="#" onClick="window.open('<?=$row_botoes['botoes_link'].$regiao_usuario;?>&id_user=<?=$_COOKIE['logado']?>','<?=$palavra?>','width=800,height=600,scrollbars=yes,resizable=yes')" class="link" title="<?php echo $row_botoes['botoes_descricao']?>" >    
                                                         <img src="<?=$row_botoes['botoes_img']?>" border="0" align="absmiddle"><br />
                                                          <?=$row_botoes['botoes_nome']?>
                                                        </a>
                                                    </li>
                                              
                                            <?php }	
                            endwhile; 
							?>
                            
							 </ul>
				 </div>
                 
     <?php  endwhile; ?>
                
</div>

<?php endwhile; ?> 
                <!------------------------------------        FIM SUBMENU ------------------------------------------->


      <div style="clear:left;"></div>
	
        
       <span class="rodape2"><?=$row_master_1['razao']?> - Acesso Restrito a Funcion&aacute;rios</span>	


	</div>
</div>
</body>
</html>
