<?php 
/*session_cache_limiter(540);
session_start();
$_SESSION['logado_adm'] = $_COOKIE['logado'];

*/


include "include/restricoes.php";
include('../conn.php');

include "../funcoes.php";
include "../wfunction.php";
include "include/criptografia.php";
include "../empresa.php";
include "../classes/funcionario.php";
include("../classes_permissoes/master.class.php");

$obj_master = new Master();
registrar_log('ADMINISTRAÇÃO','ACESSANDO ADMINISTRAÇÃO');

$usuario = carregaUsuario();
error_reporting(0);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Administra&ccedil;&atilde;o</title> 
<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../js/highslide-with-html.js"></script> 
<link href="../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>

<link href="../css_principal.css" rel="stylesheet" type="text/css" />


<script type="text/javascript" >

$(function() {
		
	$("ul.tabs").tabs("div.panes > div");
	
	
	/*$('.link').click(function(){
	
		var url_pg = $(this).attr('href');
		
		$.ajax({	
			type:'get',
			url: url_pg,
			dataType:'html',
			success: function(resposta) {
						
							$("#pagina").html();
						$("#pagina").html(resposta);
}		
			
			});
	
	});*/
	
});

  hs.graphicsDir = 'images-box/graphics/';
    hs.outlineType = 'rounded-white';



$(function(){

	
	$('#botoes ul li img').fadeTo('fast', 0.7).hover(function(){$(this).fadeTo('fast', 1.0)},function(){$(this).fadeTo('fast', 0.7)});
	$("#resumo table tr:odd").addClass('linha_dois');
	$("#resumo table tr:even").addClass('linha_um');
	$('#resumo').find('table').find('tr:first').addClass('titulo_table');
	
	$('#master').change(function(){
		
		var valor = $(this).val();
		
		$.ajax({
			url: 'encriptar.php?encriptar='+valor,
			success: function(link_encriptado){			location.href="index.php?m="+link_encriptado;
				}
			
			});
	});
	
});

function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
window.open(theURL,winName,features);
}






</script>
</head>
<body style="background-color:    #FFFFE8;">
<div id="corpo">

  <div id="conteudo">
                         
            <div id="topo">	
            <table width="100%">
            	<tr>
                	<td width="180" align="center"> <?php if(isset($_GET['m'])) {
                          $IMG = new empresa();
                          $IMG -> imagemCNPJ2($Master);
                         } ?>
                   </td>
                    <td align="center">
                    	<h1> Administração Geral</h1>
                    
                    </td>
                    <td>
                    	<?php include('../reportar_erro.php');?>
                    </td>
                </tr>
            
            </table>    
                
                
            </div>
            



<div id="menu_principal">

      <ul class="tabs">
            <li>
              <a href="#">
                <div class="sombra1"> PRINCIPAL                                                
                  <div class="texto"> PRINCIPAL</div>              
              </div>
              </a>
           </li>
           	  	<?php
						
				$qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 4 ");
				while($row_pagina= mysql_fetch_assoc($qr_botoes_pg)): 	
				
                        $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]' AND botoes_menu_id NOT IN(28) ORDER BY botoes_menu_id ");
                        while($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
                        
                         
                                    
                                 
                                    
                              $qr_botoes = mysql_query("SELECT * FROM botoes 
                                                        INNER JOIN botoes_assoc 
                                                        ON botoes.botoes_id = botoes_assoc.botoes_id
                                                        WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
                        ?>
                                    
                                   <li <?php if(mysql_num_rows($qr_botoes) == 0) echo 'style="display:none;"'; ?>>
                                        <a href="#">
                                        <div class="sombra1">* <?php echo $row_btn_menu['botoes_menu_nome'];?>   <div class="texto">  <?php echo $row_btn_menu['botoes_menu_nome'];?> </div>      </div>
                                      </a>
                                  </li>           
                   <?php 
                  	  endwhile;	
                 
				 endwhile;
                    ?>
      </ul>
   
				  		
		
    </div>
         
 
    
    <div id="submenu"  class="panes">
     
    		   <div class="conteudo_aba" style="display:none;"> 
     
        		<?php 			
				
                    
                            include 'include/menu_tt.php';
                       	
               ?>
            </div>
			<?php 
												
	$qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 4  ");
		while($row_pagina= mysql_fetch_assoc($qr_botoes_pg)): 	
		
				$qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]' AND botoes_menu_id NOT IN(28) ORDER BY botoes_menu_id ");
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
                        
                        if(($usuario['id_master'] != 1 and $row_botoes['botoes_id'] == 82) ) continue;
                      ?>
                              
                               <li> 
                                   <a href="<?=$row_botoes['botoes_link'].$link_master?>" title="<?=$row_botoes['botoes_descricao']?>"  class="link" target="_blank">
                                     	<img src="../img_menu_principal/<?php echo $row_botoes['botoes_img']?>" /> <br />  <?=$row_botoes['botoes_nome']?>
                                 </a>
                             </li>		
                             
                    <?php endwhile;  ///fim loop   ?>
	 		  </ul>
</div> 

<?php 
			  endwhile;
	endwhile;
?>

</div>
          
    <div class="clear"></div>
    
    <div id="pagina"> </div>
     
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