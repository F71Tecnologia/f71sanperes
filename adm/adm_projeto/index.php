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
            <div id="menu" class="nota">
                <?php include "include/menu.php"; ?>
            </div>
            <form action="" method="post" name="form1" id="form1">
                <div id="conteudo" style="text-transform:uppercase;">  
                    <?php
                   
                    
                    $qr_regioes = mysql_query("SELECT *, A.regiao as nome_regiao,
                                               IF(B.status_reg = 1, 'PROJETOS ATIVOS', 'PROJETOS INATIVOS') as tipo_status
                                               FROM regioes as A
                                               INNER JOIN projeto as B
                                               ON A.id_regiao = B.id_regiao
                                               INNER JOIN funcionario_regiao_assoc as C
                                               ON C.id_regiao = A.id_regiao
                                               where A.id_master = '$Master'
                                               AND C.id_funcionario = '$_COOKIE[logado]'
                                               AND B.status_reg IN(0,1)
                                               GROUP BY B.id_regiao, B.status_reg
                                               ORDER BY B.status_reg, B.regiao  DESC ;
                                               ;");
                    
                   while($row_regiao = mysql_fetch_assoc($qr_regioes)):
                        $ordem++;                  

                        if($row_regiao['status_reg'] != $status_anterior){   echo '<h3 class="titulo">' . $row_regiao['tipo_status'] . '</h3>'; }

                        if($row_regiao['id_regiao'] != $reg_anterior){      
                            
                         $seta =    ($_GET['aberto'] == $ordem) ?'seta_aberto': 'seta_fechado';
                         $link = "action.index_projeto.php?status=".$row_regiao['status_reg']."&regiao=".$row_regiao['id_regiao']."&m=".$link_master;
                        ?>  
                             <a class="show <?php echo $seta?>"  id="<?= $ordem ?>" href="<?php echo $link;?>"   onClick="return false">
                                 <span style="text-transform:uppercase;padding-left:10px;">  <?= $row_regiao['nome_regiao'] ?></span>              
                             </a>
                    
                              <div class="<?= $ordem ?>" style="width:90%; <?php if ($_GET['aberto'] != $ordem) { echo 'display:none;';}?> "></div>

                         <?php 
                        }
                        $reg_anterior    = $row_regiao['id_regiao'];
                        $status_anterior = $row_regiao['status_reg'];
                   endwhile; 
                   ?>
                </div>        
            </form>
            <div id="rodape">
            <?php include('../include/rodape.php'); ?>
            </div>
        </div>
</body>
</html>