<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');


?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Notas Fiscais</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<style type="text/css">
.tr_titulo { font-size: 12px; font-weight: bold; }
</style>

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 
<script type="text/javascript"> 
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
</script>

<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript">
$(document).ready(function(){
     $('.show').click(function() {
		
		var pagina_action = $(this).attr('href');
		var proximo 	  = $(this).next();		
		var id_regiao 	  = $(this).next().children().val() ;
		
		
		
		$('.show').not(this).removeClass('seta_aberto');
	  	$('.show').not(this).addClass('seta_fechado');
		
		
		if($(this).attr('class')=='show seta_aberto') {
			
			$(this).removeClass('seta_aberto');
			$(this).addClass('seta_fechado');		
		
			
		} else {
			
			
			$(this).removeClass('seta_fechado');
			$(this).addClass('seta_aberto');
			
			$(this).append('<div id="carregando"> <img src="../../img_menu_principal/carregando.gif" height="15"/> </div>');	
						
			$.ajax({
				
					url: pagina_action,
					type:'GET',											
					success: function(resposta) {
						
						
						$('#carregando').remove();
						proximo.html('');	
						
						proximo.html(' <input type="hidden" name="regiao" value="'+id_regiao+'"/>' + resposta);	
						
						//cor da linha
						$('.azul').parent().css({'background-color': '#7EB3F1'});
						$('.vermelho').parent().css({'background-color': '#ffb8c0'});
						
					}
				
				
				
				});	
					
						
			
		}

		$('.show').not(this).next().hide();
		$(this).next().css({'width':'100%'}).slideToggle('fast');
    });

	
	
});	
</script>
<style>
#carregando{

width:100%;
text-align:left;
margin-left:-5px;
margin-top:-15px;
display:block;

}
</style>


</head>
<body>
<div id="corpo">
    <div id="menu" class="nota">
    	<?php include "include/menu.php"; ?>
    </div>
    <div id="conteudo" style="text-transform:uppercase;">  
<?php

$tipos = array(1 => 'Projetos Ativos',0 => 'Projetos Inativos');

// Loop dos Status  
foreach($tipos as $status => $nome_status) {
	      
	  $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_master = '$Master'  AND status_reg = '$status'");
	  while($row_projetos = mysql_fetch_assoc($qr_projetos)) :
	  
	       //Bloqueio das regiões 15, 36, 57 e do projeto 3236 pq não sei se pode apagar ele do banco		
		  //if($row_projetos['id_regiao']=='15' or $row_projetos['id_regiao']=='36' or $row_projetos['id_regiao']=='37' or $row_projetos['id_projeto'] == 3236) continue; 
		  
		  $projetos[] = $row_projetos['id_projeto'];   
		  
	  endwhile;
	  
		 
	  $projetos = implode(',', $projetos);
	 
	  // Loop dos Projetos e Subprojetos
      $qr_projeto = mysql_query("SELECT *
							       FROM projeto
							      WHERE id_projeto IN ($projetos) ORDER BY regiao ASC");
	  while($row_projeto = mysql_fetch_assoc($qr_projeto)): 
	  
			 //verificação de notas
			// ALTERADO POR MAIKOM PARA SUPRIR A NESCECIDADE DE MULTIPLAS NOTAS PARA 1 ENTRADA
			//$qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1' ORDER BY data_emissao DESC");
			$qr_notas = mysql_query("SELECT * FROM notas 
									WHERE id_projeto = '$row_projeto[id_projeto]' 
									AND status = '1' 
									AND id_notas NOT IN(
										SELECT id_notas FROM notas INNER JOIN notas_assoc USING(id_notas)
											WHERE notas_assoc.id_entrada IN(
											SELECT id_entrada 
											FROM notas_assoc
											GROUP BY id_entrada
											HAVING COUNT(*) > 1
										)
										AND notas.id_projeto = '$row_projeto[id_projeto]' 
										AND notas.status = '1'
										)
										
									ORDER BY data_emissao DESC") or die(mysql_error());
			
			$num_notas = mysql_num_rows($qr_notas);
			if (!empty($num_notas)) {
			
			
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
		      $row_regiao = mysql_fetch_assoc($qr_regiao); 	  ?>
		   
               <a class="show <?php if($_GET['aberto'] == $ordem) { echo 'seta_aberto'; } else { echo 'seta_fechado'; } ?>"  id="<?=$ordem?>" href="action.index_notas.php?status=<?php echo $row_projeto['status_reg'];?>&regiao=<?php echo $regiao ?>&m=<?php echo $link_master;?>"   onClick="return false">
                  <span style="text-transform:uppercase;padding-left:10px;">  <?=$row_regiao['regiao']?></span>
              </a>


    		  <div class="<?=$ordem?>" style="width:100%; <?php if($_GET['aberto'] != $ordem) { echo 'display:none;'; } ?>">
		  
		<?php
			 } // Fim da Verificação de Região
		
       
         
				
			?>
            
          
	 <?php 
		  $regiao_anterior = $row_projeto['id_regiao'];
		  $status_anterior = $row_projeto['status_reg'];
		 
		unset($totalizador_valor,$totalizador_repasse,$totalizador_diferenca);
			
		} ///fim if empty notas
	
	
	 endwhile; // FIM DO LOOP DOS PROJETOS 
	
	unset($projetos);

} // Fim do Loop dos Status

?>

    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('../include/rodape.php'); ?>
    </div>
</div>
</body>
</html>