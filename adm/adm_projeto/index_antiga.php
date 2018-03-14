<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');

$acesso_exclusao = array(9,5);

function verifica_expiracao($data)
{
	///verificar projeto expirado ou ok
	
			  list($ano,$mes,$dia)= explode('-', $data);
			  
			 $data_termino 		= mktime(0,0,0,$mes,$dia,$ano);
			 $data_hoje			= mktime(0,0,0,date('m'),date('d'),date('Y'));			 
			 $prazo_renovacao	= mktime(0,0,0,$mes,$dia-45,$ano); //45 dias
			
			 if(($prazo_renovacao<=$data_hoje) and($data_termino>=$data_hoje))
			 { 
			 $dif=($data_termino-$data_hoje)/86400;
			
					if($dif == 0 ){
						
						return '<span style="color:#F60;font-weight:bold;">Expira hoje!</span>';
						
					} else {
					
						return '<span style="color:#09C;font-weight:bold;">Expira em '.$dif.' dias!</span>';	 
					}
			 
			 }elseif($data_hoje>$data_termino)
			 {
				return '<span  style="color:#F00;font-weight:bold;"> Expirado</span>';
			 } else
			 {
				 return '<span style="color:#0C0;font-weight:bold;"> OK </span>';
			 }
} ?>

<html>
<head>
<title>Administra&ccedil;&atilde;o de Projetos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<script type="text/javascript" src="../../jquery-1.3.2.js"></script> 
<script type="text/javascript"> 
    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
	
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
			
			$(this).append('<div id="carregando"> <img src="../../imagens/carregando_adm.gif" height="15"/> </div>');	
						
			$.ajax({
				
					url: pagina_action,
					type:'GET',											
					success: function(resposta) {
						
						
						$('#carregando').remove();
						proximo.html('');	
						
						proximo.html(' <input type="hidden" name="regiao" value="'+id_regiao+'"/>' + resposta);	
						
					}
				
				
				
				});	
					
						
			
		}

		$('.show').not(this).next().hide();
		$(this).next().css({'width':'100%'}).slideToggle('fast');
    });

	$('.azul').parent().css({'background-color': '#e2edfe'});
	$('.vermelho').parent().css({'background-color': '#ffb8c0'});
	
});	

</script>
<style>
.linha_lista{
	font-size:12px;
	color:#000;
}
.linha_1{
	font-size:12px;
	background-color:#D2E9FF;}
.linha_2{
	font-size:12px;
	background-color:#F3F3F3;
}

.editar {
	margin-left:16px;	
}
#carregando{

width:100%;
text-align:left;
margin-left:-5px;
margin-top:-15px;
display:block;

}

</style>
</head>
<body style="text-transform:uppercase;">
<div id="corpo">
        <div id="menu" class="projeto">
        	<?php include "include/menu.php"; ?>
        </div>
<div id="conteudo">

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
	
			
		   $projeto 	 = $row_projeto['id_projeto'];
		   $subprojeto 	 = $row_projeto['id_subprojeto'];
		   $regiao		 = $row_projeto['id_regiao'];
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
		   
              <a class="show <?php if($_GET['aberto'] == $ordem) { echo 'seta_aberto'; } else { echo 'seta_fechado'; } ?>"  id="<?=$ordem?>" href="action.index_projeto.php?status=<?php echo $row_projeto['status_reg'];?>&regiao=<?php echo $regiao ?>&m=<?php echo $link_master;?>"   onClick="return false">
                  <span style="text-transform:uppercase;padding-left:10px;">  <?=$row_regiao['regiao']?></span>
              </a>

    		  <div class="<?=$ordem?>" style="width:100%; <?php if($_GET['aberto'] != $ordem) { echo 'display:none;'; } ?>">
              
		  	 <input type="hidden" name="regiao" value="<?php echo $row_regiao['id_regiao']?>"/>
             
		<?php
			 } // Fim da Verificação de Região
		?>
       

	 <?php 
		  $regiao_anterior = $row_projeto['id_regiao'];
		  $status_anterior = $row_projeto['status_reg'];
		
	 
	 endwhile; // FIM DO LOOP DOS PROJETOS 
	
	unset($projetos);

} // Fim do Loop dos Status

?>

<input name="id_master" id="id_master" type="hidden" value="<?php echo $link_master;?>"/>

    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('include/rodape.php'); ?>
    </div>
</div>
</body>
</html>