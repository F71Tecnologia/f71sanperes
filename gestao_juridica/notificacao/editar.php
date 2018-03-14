<?php 


include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "../include/criptografia.php";
include("../../classes_permissoes/regioes.class.php");

$obj_regiao = new Regioes();


$id_user   = $_COOKIE['logado'];
$id_notificacao = $_GET['id_noti'];


$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario   = mysql_fetch_array($query_funcionario);
$tipo_user         = $row_funcionario['tipo_usuario'];

$query_master      = mysql_query("SELECT id_master FROM regioes WHERE id_master = '$row_funcionario[id_master];'");
$id_master         = @mysql_result($query_master,0);

$regioes = new Regioes();

$qr_notificacao = mysql_query("SELECT * FROM notificacoes WHERE notificacao_id = '$id_notificacao'");
$row_not		= mysql_fetch_assoc($qr_notificacao);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Gest&atilde;o  Jur&iacute;dica</title> 
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script src="../../jquery/jquery.tools.min.js" type="text/javascript"></script>
<script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	$('#data_limite').mask('99/99/9999');
	
	$('#regiao').change(function(){
		
		var regiao = $(this).val();
		$.ajax({
			url:'../action.preenche_select.php?regiao='+regiao,
			success: function(resposta) {			
				$('#projeto').html( '<option value="todos">TODOS</option>' + resposta);				
				}		
			});
		});
		



$('.add_documento').click(function(){	
	var campo = '<div> <input type="text" name="documentos[]"  /> <a href="#" onclick="$(this).parent().remove(); return false;"><img src="../../imagens/excluir.png" width="18" height="18" title="Excluir"/>  </a> </div>';	
	$('#documentos').prepend(campo);
	});
	
$('.add_responsavel').click(function(){
	
	
		$.ajax({
			url: '../action.preenche_select.php?funcionario',
			success: function(resposta) {
			
					
			var campo = '<div> <select name="responsaveis[]">  <option value="" selected="selected"> Selecione o responsável...</option> '+resposta+'</select> <a href="#" onclick="$(this).parent().remove(); return false;"><img src="../../imagens/excluir.png" width="18" height="18" title="Excluir"/>  </a> </div>';	
			
			$('#responsaveis').prepend(campo);
			}
	
	 });
});
	



	
$('.add_documento' ).trigger('click');	
$('.add_responsavel' ).trigger('click');	
});	

	


</script>

<style>
.add_documento,.add_responsavel{
cursor:pointer;	
}
</style>

<link rel="stylesheet" type="text/css" href="../../adm/css/estrutura.css"/>
</head>
<body  class="fundo_juridico" >
<div id="corpo">
	<div id="conteudo">
    
    <div style="float:right; margin-top:5px; margin-right:10px;">
  		  <a href="cad_notificacao_2.php?id=<?php echo $id_notificacao;?>" > << GERENCIAR ANEXOS </a>
    </div>
    
    <div style="clear:right"></div>
    
   <div> <img src="../../imagens/logomaster<?php echo $id_master;?>.gif"/></div>
   
    <h3>EDITAR NOTIFICAÇÃO </h3>
    <form method="post" action="envia.notificacao.php" name="form" >
    <table width="100%">
    	<tr>
        	<td class="secao">TIPO:</td>
            <td align="left">
            <select name="tipo" id="tipo">
           		<option value="">Selecione o tipo...</option>
                <?php
                $qr_tipo = mysql_query("SELECT * FROM tipos_notificacoes WHERE tipos_notificacoes_status = 1");
				while($row_tipo = mysql_fetch_assoc($qr_tipo)):
				
				$selected = ($row_not['tipos_notificacoes_id'] == $row_tipo['tipos_notificacoes_id'])? 'selected="selected"':'';
				?>
              	  <option value="<?php echo $row_tipo['tipos_notificacoes_id']; ?>" <?php echo $selected; ?>> <?php echo $row_tipo['tipos_notificacoes_nome']; ?></option>                
				<?php
				endwhile;
				
				?>
            </select>
            </td>
        </tr>
        <tr>
        	<td  class="secao">Nº DO DOCUMENTO:</td>
            <td  align="left"><input name="n_documento" type="text"  value="<?php echo $row_not['notificacao_numero']?>"/></td>
        </tr>
        <tr>
        	<td  class="secao">REGIÃO:</td>
            <td align="left">
	            <select name="regiao" id="regiao">              
                <?php
				if($row_not['id_regiao'] != 'todos'){
					$regioes->Preenhe_select_sem_master($row_not['id_regiao']);
				} else {
				?>   
                <option value="todos" <?php if($row_not['id_regiao'] == 'todos') echo 'checked="cheked"'; ?>>TODOS</option>
                <?php } ?>
    	        </select>
            </td>
            </tr>
         <tr>
            <td  class="secao">PROJETO:</td>
            <td align="left">
                <select name="projeto" id="projeto">
                 <?php
				if($row_not['id_projeto'] != 'todos'){
					
					$qr_projeto = mysql_query("SELECT * FROM projeto WHERE 1");
					while($row_projeto = mysql_fetch_assoc($qr_projeto)):
						
						$checked= ($row_projeto['id_projeto'] == $row_not['id_projeto'])?'selected="selected"':'';
						echo '<option value="'.$row_projeto['id_projeto'].'" '.$checked.'>'.$row_projeto['nome'].'</option>';
					
					endwhile;		
					
				} else {
				?>   
               		 <option value="todos" <?php if($row_not['id_projeto'] == 'todos') echo 'selected="selected"'; ?>>TODOS</option>
                <?php } ?>
                </select>
            </td>

        </tr>
        
    	<tr>
        	<td class="secao">DESCRIÇÃO:</td>
            <td align="left"><textarea name="descricao" ><?php echo $row_not['notificacao_descricao']?></textarea></td>

		</tr>
        <tr>
        	<td  class="secao">
            DOCUMENTOS SOLICITADOS: <BR />
            <a href="#" class="add_documento"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
            </td>
            <td align="left">
            	<div id="documentos">
                <?php
                $qr_documentos = mysql_query("SELECT * FROM notific_doc_assoc WHERE notificacoes_id = '$row_not[notificacao_id]'");
				while($row_doc = mysql_fetch_assoc($qr_documentos)):
				
				echo '<div> <input type="text" name="documentos[]" value="'.$row_doc['nome_documento'].'"/> <a href="#" onclick="$(this).parent().remove(); return false;"><img src="../../imagens/excluir.png" width="18" height="18" title="Excluir"/>  </a> </div>';
					endwhile;
				?>
                
                </div>
            </td>
        </tr>
        <tr>
        	<td  class="secao">
            	RESPONSÁVEL: <br />
               <a href="#" class="add_responsavel"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
            </td>
            <td align="left">
            <div id="responsaveis">
              <?php
                $qr_responsavel = mysql_query("SELECT * FROM notific_responsavel_assoc WHERE notificacoes_id = '$row_not[notificacao_id]'");
				while($row_responsavel = mysql_fetch_assoc($qr_responsavel)):	
				
								
						?>
						<div><select name="responsaveis[]">
							<?php
                            $qr_funcionarios = mysql_query("SELECT * FROM funcionario WHERE status_reg = 1 ORDER BY nome1");
                            while($row_func  = mysql_fetch_assoc($qr_funcionarios)):
                            
                                $selected = ($row_func['id_funcionario'] == $row_responsavel['funcionario_id'])? 'selected="selected"': '';
                                echo '<option value="'.$row_func['id_funcionario'].'"'.$selected.' >'.$row_func['nome1'].'</option>';
                            
                            endwhile;
                            
                            ?>
                                  
                        </select>
                            <a href="#" onclick="$(this).parent().remove(); return false;"><img src="../../imagens/excluir.png" width="18" height="18" title="Excluir"/>  </a> 
                        
                        </div>  
				
				<?php
                endwhile;
				?>
            </div>	
            </td>
        </tr>    
         <tr>
         	<td  class="secao">DATA LIMITE:</td>
            <td  align="left"><input name="data_limite" type="text" id="data_limite" value="<?php echo implode('/',array_reverse(explode('-',$row_not['notificacao_data_limite'])))?>" /></td>
         </tr>   
         <tr>
         	<td colspan="2" align="center">
            <input name="notificacao_id" type="hidden" value="<?php echo $row_not['notificacao_id']?>"/>
            <input type="submit" name="atualizar" value="ATUALIZAR"/></td>
         </tr>
            
    </table>
 </form>
                
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