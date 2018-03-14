<?php 

include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');


$acesso_exclusao = array(9,5);

?>
<html>
<head>
<title>:: Intranet :: Modelos de Documento</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<style type="text/css">
.tr_titulo { font-size: 12px; font-weight: bold; }
</style>



<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript">
$(document).ready(function(){
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
		$(this).next().css({'width':'100%'}).slideToggle('fast');
    });

	$('.azul').parent().css({'background-color': '#7EB3F1'});
	$('.vermelho').parent().css({'background-color': '#ffb8c0'});
	
});	
</script>
</head>
<body>
<div id="corpo">
    <div id="menu" class="documento">
    	<?php include "../include/menu_adm.php"; ?>
        
    </div>
    <div id="conteudo" style="text-transform:uppercase;">  
    
    	<h1><span>Modelos</span></h1>
    
   		<table  class="relacao">
        	<tr class="secao">
            	<td>NOME</td>
                <td>EDITAR</td>
                <td>DOWNLOAD</td>
                 <td>DESCRIÇÃO</td>
                 <?php 
				 if(in_array($_COOKIE['logado'],$acesso_exclusao)) {
				 ?>
                	<td>EXCLUIR</td>
                <?php
				 }
				?>
                
                <td>CADASTRO/EDIÇÃO</td>
            </tr>
           <?php
		   
		   	$qr_documentos = mysql_query("SELECT * FROM modelo_documentos WHERE documento_status = 1 AND id_master = '$Master' ORDER BY documento_id");
			while($row_documento = mysql_fetch_assoc($qr_documentos)):
			
				$qr_documento_anexo = mysql_query("SELECT * FROM modelo_documento_anexos WHERE anexo_id_documento = '$row_documento[documento_id]'");
				 $row_anexo =  mysql_fetch_assoc($qr_documento_anexo);
				?>
					<tr  class="novo2">
						<td  width="20%"><?php echo $row_documento['documento_nome'];?></td>
					 	<td  width="8%">
							<a href="edicao_documento.php?m=<?php echo $link_master;?>&id=<?php echo  $row_documento['documento_id'];?>"><img src="../../imagens/editar_projeto.png" title="Editar" width="30" height="30"/></a>
						</td>
                        
                        <td width="10%">
                          <a href="anexos/<?php echo $row_anexo['anexo_nome'].'.'.$row_anexo['anexo_extensao'];?>"><img src="../../imagens/download.png"  width="30" height="30" title="Download"/></a> 
						</td>
					
						<td width="20%"><?php echo $row_documento['documento_descricao'];?></td>
                        
						<?php  if(in_array($_COOKIE['logado'],$acesso_exclusao)) { ?>
						<td  width="10%">
							<a href="excluir_documento.php?m=<?php echo $link_master;?>&id=<?php echo  $row_documento['documento_id'];?>" onClick="return(confirm('Deseja excluir o documento: <?php echo $row_documento['documento_nome'];?> ?'))"><img src="../../imagens/lixo.gif" title="Excluir" width="25" height="25"/></a>
						</td>
                        
                        <?php } ?>
                        
						<td width="20%"> 
                        
                        <?php 
						$qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_documento[documento_usuario]'");
						$row_funcionario = mysql_fetch_assoc($qr_funcionario); 
						$nome_cadastro = explode(' ', $row_funcionario['nome']);
												  
						  if($row_documento['documento_data_atualizacao'] == '0000-00-00'){					  
						         
                                     
                        	echo 'Cadastrado por:  '.$nome_cadastro[0].'<br> em '.formato_brasileiro($row_documento['documento_data']);
							
						  } else {
							  $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_documento[documento_usuario]'");
							  $row_funcionario = mysql_fetch_assoc($qr_funcionario); 
							  $nome_edicao = explode(' ', $row_funcionario['nome']);
									
							
							 echo 'Editado por:  '.$nome_edicao[0].'<br> em '.formato_brasileiro($row_documento['documento_data_atualizacao']);
						  }
                        ?>
                        
                        
                        </td>
				</tr>
            
            <?php
			endwhile;
		   
		   ?>      
        
        </table>


    <p style="margin-bottom:40px;"></p>
    </div>
    <div id="rodape">
        <?php include('../include/rodape.php'); ?>
    </div>
</div>
</body>
</html>