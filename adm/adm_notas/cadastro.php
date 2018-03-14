<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes_permissoes/regioes.class.php');
include('../../classes_permissoes/projeto.class.php');


$REGIOES = new Regioes();
$PROJETO = new Projeto();


if(isset($_POST['enviar'])){
	
$n_nota=trim(mysql_real_escape_string($_POST['n_nota']));
$id_parceiro=$_POST['parceiro'];
$data_emissao =  implode('-',array_reverse(explode('/',$_POST['data_emissao'])));
$descricao=mysql_real_escape_string($_POST['descricao']);
$valor	= str_replace(',','.',str_replace('.','',$_POST['valor']));
$tipo=$_POST['tipo'];
$usuario_id = $_COOKIE['logado'];
$anexo=$_FILES['anexo'];
$id_notas_files = $_POST['nota_anexo'];
$id_projeto=$_POST['projeto'];

$id_banco = $_POST['banco'];
$qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$id_banco' LIMIT 1");
$row_banco = mysql_fetch_assoc($qr_banco);
$id_regiao_entrada = $row_banco['id_regiao'];
$id_projeto_entrada = $row_banco['id_projeto'];
$ano_competencia = $_POST['ano_competencia'];




list ($tipo_contrato2,$tipo_contrato)=explode('_',$_POST['contrato']);


$vencimento_entrada = implode('-',array_reverse(explode('/',$_POST['data_entrada'])));



$sql=mysql_query("INSERT INTO `notas` (id_notas, numero,  id_parceiro, data_emissao, descricao, valor, tipo, nota_data, id_funcionario,status,id_projeto,tipo_contrato,tipo_contrato2,nota_ano_competencia)
VALUES
('','$n_nota','$id_parceiro','$data_emissao', '$descricao', '$valor', '$tipo', NOW() ,'$usuario_id','1','$id_projeto','$tipo_contrato','$tipo_contrato2','$ano_competencia') "); 
$ultimo_id = (int) @mysql_insert_id();


if($_POST['entrada'] == 'on'){
	// CRIANDO ENTRADA NO FINANCEIRO
	
	// recebendo o nome do projeto
	$qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$id_projeto'");
	$nome_projeto = @mysql_result($qr_projeto,0);
	
	// Montando o nome da entrada
	$nome_entrada = $id_projeto . ' - ' . $nome_projeto . ' | Nota Nº.: ' . $n_nota;
	
	$sql_entrada = "INSERT INTO entrada 
	( id_regiao, id_projeto, id_banco, 	id_user, 	nome, 	especifica, 	tipo, 	adicional, 	valor, 	data_proc, 	data_vencimento, comprovante, status) 
	VALUES 
	('$id_regiao_entrada', '$id_projeto_entrada', '$id_banco', '$usuario_id', '$nome_entrada', '$nome_entrada', '12' , '0,00' , '$valor' , NOW(),  '$vencimento_entrada', '2', '1');";
	mysql_query($sql_entrada);
	
	$id_entrada = (int) @mysql_insert_id();
	

	// atualizando com o id da entrada 
	//$update_nota = mysql_query("UPDATE notas SET  id_entrada = '$id_entrada' WHERE id_notas = '$ultimo_id' LIMIT 1");
	
	// MUDOU a estrutura como sempre rsrs
	mysql_query("INSERT INTO notas_assoc ( id_notas, id_entrada ) VALUES ( '$ultimo_id', '$id_entrada');");
	
	
} 


$query_update_notas = mysql_query("UPDATE notas_files SET id_notas = '$ultimo_id' WHERE id_file IN ($id_notas_files)");

$nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"),0);	
registrar_log('ADMINISTRAÇÃO - CADASTRO DE NOTAS FISCAIS', $nome_funcionario.' cadastrou a nota: '.'id_nota:('.$ultimo_id.') - Número: '.$n_nota);	



	header("Location: cadastro_2.php?m=".$link_master.'&id='.$ultimo_id);
	

}

?>
<html>
<head>
<title>Administra&ccedil;&atilde;o de Notas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<!--<link href="../../net1.css" rel="stylesheet" type="text/css" />-->
<!--<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />-->
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 




<script type="text/javascript" >

 hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
$(function(){
	$("#anexo").uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : 'upload.php',
				'buttonText'     : 'Enviar Nota',
				'queueID'        : 'barra_processo',
				'cancelImg'      : '../../uploadfy/cancel.png',
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : true,
			
				'fileDesc'       : 'gif jpg pdf',
				'fileExt'        : '*.gif;*.jpg;*.pdf;',
				'onComplete'  : function(event, ID, fileObj, response, data) {
					eval('var resposta = '+ response);
                                        console.log(resposta);
					if(resposta.erro){
                                            
						alert('Ocorreu um erro no envio do arquivo');
					}else{
						$("#anexo").hide();
						
						if($('#nota_anexo').val() == ''){
							$('#nota_anexo').val(resposta.ID);
						}else{
							$('#nota_anexo').val($('#nota_anexo').val() + ',' + resposta.ID);
						}
						
						
						$('#img_nota').append('<img src="'+resposta.img+'" width="200" height="250" />');
						$('#enviar').attr('disabled', false);
					}
				}
			}); 
			
	$("#cadastro").validationEngine();
	$('#data_emissao').mask('99/99/9999');
	$('#data_entrada').mask('99/99/9999');
	$('#valor').priceFormat();
	
	
	$('#entrada').change(function(){
		if($(this).attr('checked')){
			$('.bloco_banco').fadeIn();
		}else{
			$('.bloco_banco').fadeOut();
		}
	});	
	

	
	
	
	$('#projeto').change(function () {

                    var id_projeto = $(this).val();

                    $('#contrato').html('<option value="">Carregando...</option>');

                    $.ajax({
                        'url': 'actions/combo.subprojeto.json.php',
                        'data': {'id_projeto': id_projeto},
                        'success': function (resposta) {

                            $('#contrato').html('');



                            $.each(resposta, function (i, valor) {

                                $('#contrato').append('<optgroup label="' + i + '">');

                                $.each(valor, function (chave, registro) {

                                    $('#contrato').append('<option value="' + registro.tipo + '_' + registro.id_subprojeto + '" > Contrato Nº: <b>' + registro.numero_contrato + '</b> Inicio : ' + registro.inicio + ' - Fim : ' + registro.termino + '</option>');
                                });

                                $('#contrato').append('</optgroup>');

                            });



                        },
                        'dataType': 'json'
                    });
                });
	
});
</script>

<style>
    td.secao{
        font-size:12px;
    }
select option span{
	 font-weight:bold;
}
</style>


</head>
<body>
<div id="corpo">
    <div id="menu" class="nota">
    	<?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo">   
       <h1><span>Cadastro de Notas Fiscais</span></h1>
                <form name="cadastro" id="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?=$link_master?>">
                  <table width="818" >
                    <tr>
                      <td width="228" class="secao">N&ordm; do nota:</td>
                      <td  width="144" colspan="3"><label for="n_nota"></label>
                      <input type="text" name="n_nota" id="n_nota" class="validate[required]"></td>
                    </tr>
                    <tr>
                        <td width="219" class="secao">Parceiro Operacional:</td>
                    
                        <td  width="207" colspan="3"><label for="parceiro3"></label>
                        <select name="parceiro" size="1" id="parceiro3" class="validate[required]">
                        
                        <option value="">
                         Selecione um parceiro... 
                         </option>
                         
                         <option value=""></option>
                        <optgroup label="<< REGIÕES ATIVAS >>"></optgroup>
                         
                         
                          <?php
						  
						  
						   $sql_regiao=mysql_query("SELECT regioes.id_regiao, regioes.regiao,regioes.status FROM parceiros 
                                                                                INNER JOIN regioes ON parceiros.id_regiao = regioes.id_regiao 
                                                                                AND parceiros.parceiro_status='1'
                                                                                INNER JOIN funcionario_regiao_assoc 
                                                                                ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao 
                                                                                WHERE regioes.id_master = '$Master'
                                                                                AND regioes.status='1' AND  regioes.status_reg ='1'
                                                                               AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]' 
                                                                               GROUP BY regioes.id_regiao 
                                                                               ORDER BY regioes.id_regiao")or die("Erro");
					     	while($row_regiao = mysql_fetch_assoc($sql_regiao)):
							
						 ?>
                         <optgroup label="<?=$row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'];?>" >
                         	<?php 
                                $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE id_regiao = '$row_regiao[id_regiao]'");
                                while($row_parceiros = mysql_fetch_assoc($qr_parceiros)):


                                ?>

                            	<option value="<?=$row_parceiros['parceiro_id']?>" ><?=$row_parceiros['parceiro_nome']?></option>
                            <?php endwhile;?>
                         </optgroup>
                         
                         <?php 
						 
                        endwhile; ?>
                        
                        
                        
                        
                           
                         <option value=""></option>
                        <optgroup label="<< REGIÕES INATIVAS >>"></optgroup>
                         
                          <?php
                          $sql_regiao=mysql_query("SELECT regioes.id_regiao, regioes.regiao,regioes.status FROM parceiros 
														INNER JOIN regioes ON parceiros.id_regiao = regioes.id_regiao 
														AND parceiros.parceiro_status='1'
														INNER JOIN funcionario_regiao_assoc 
														ON regioes.id_regiao = funcionario_regiao_assoc.id_regiao 
														 WHERE regioes.id_master = '$Master'
														 AND (regioes.status='0' OR  regioes.status_reg ='0')
														AND funcionario_regiao_assoc.id_funcionario = '$_COOKIE[logado]' ORDER BY regioes.id_regiao")or die("Erro");
					     	while($row_regiao = mysql_fetch_assoc($sql_regiao)):
							
						
						 ?>
                         <optgroup label="<?=$row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'];?>" >
                         	<?php 
								$qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE id_regiao = '$row_regiao[id_regiao]'");
								while($row_parceiros = mysql_fetch_assoc($qr_parceiros)):
								
								
							?>
                            
                            	<option value="<?=$row_parceiros['parceiro_id']?>" ><?=$row_parceiros['parceiro_nome']?></option>
                            <?php endwhile;?>
                         </optgroup>
                         
                         <?php 
						 
						 endwhile; ?>
                        
                        
                        
                        
                        
                      </select></td>
                    </tr>
                    <tr>
                      <td width="228" class="secao">Projeto:</td>
                      <td colspan="3"><label for="projeto"></label>
                      
             
                        <select name="projeto" id="projeto" class="validate[required]">
                     	<?php  $PROJETO->Preenhe_select_por_master($Master);?>
                        
                      </select>
                      
                      </td>
                    </tr>
                    <tr>
                    
                    
                    <?php
                    
					$qr_renovacao=("SELECT * FROM sub_projeto WHERE id_projeto='$row_projeto[id_projeto]' ORDER BY inicio");
					
					
					
					?>
					
					
                      <td width="228" class="secao">Tipo de contrato:</td>
                      
                      <td colspan="3"><label for="contrato"></label>
                        <select name="contrato" id="contrato">
                       
                        
                      </select>
                      </td>
                    </tr>
                    <tr>
                      <td class="secao">Data de emiss&atilde;o:</td>
                      <td ><label for="data_emissao"> </label>
                        <input name="data_emissao" width="50"type="text" class="validate[required]" id="data_emissao" size="10">
                     </td>
                      <td class="secao">Ano da Competência</td>
                      <td colspan="3"><label for="ano_competencia"> </label>
                        <select name="ano_competencia"  class="validate[required]" id="data_emissao">
                        
                        	<option value="">Selecione o ano..</option>
							<?php 
                            for($ano=2005;$ano<= (date('Y')+1);$ano++):
                            ?>
                            <option value="<?php echo $ano; ?>"><?php echo $ano; ?></option>
                            
                            
                            <?php
                            endfor;
                            
                            ?>
                        
                        </select>
                     </td>
                    </tr>
                    <tr>
                      <td class="secao">Descri&ccedil;&atilde;o:</td>
                      <td colspan="5"><label for="descricao"></label>
                      <input name="descricao" type="text" id="descricao" size="50"></td>
                    </tr>
                    <tr>
                      <td class="secao">Valor:</td>
                      <td colspan="5"><label for="valor"></label>
                      <input type="text" name="valor" id="valor" class="validate[required]" value="0,00" ></td>
                    </tr>
                    <tr>
                      <td class="secao">Tipo:</td>
                      <td colspan="5"><label for="tipo"></label>
                        <select name="tipo" id="tipo">
                        <option value="1>">1- Nota</option>
                        <option value="2">2 - Carta de medi&ccedil;&atilde;o</option>
                      </select></td>
                    </tr>
                   <!--- <tr>
                      <td class="secao">Anexo:</td>
                      <td colspan="5"><input type="file" name="anexo" id="anexo" />
                      <div id="barra_processo"></div></td>
                    </tr>
                    ---->
                     <tr>
                      <td colspan="6" id="img_nota">
                      	
                      </td>
                    </tr>
                    
                    <tr>
                       <td class="secao"> Criar entrada no financeiro</td>
                       <td colspan="2"><input type="checkbox" id="entrada" name="entrada" /></td>
                       <td class="secao">&nbsp;</td>
                       <td class="secao">&nbsp;</td>
                       <td class="secao">&nbsp;</td>
                    </tr>
                    
                    <tr class="bloco_banco" style="display:none;">
                       <td class="secao"> Banco</td>
                       <td colspan="2">
                           <select name="banco" id="banco" >
                               <?php /*
                                 $qr_banco = mysql_query("SELECT id_banco, nome FROM bancos WHERE status_reg  = '1'");
                                 while($row_banco = mysql_fetch_assoc($qr_banco)):
                                 ?>
                                 <option  value="<?=$row_banco['id_banco']?>"><?=$row_banco['id_banco']?> - <?=$row_banco['nome']?></option>
                                 <?php endwhile; */ ?>

                               <?php
                               $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '1' AND status_reg = '1' AND id_master = {$Master}");
                               while ($row_regioes = mysql_fetch_assoc($qr_regioes)):
                                   ?>
                                   <?php
                                   $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$row_regioes[id_regiao]' AND status_reg = '1' AND interno = '1'");
                                   $num_bancos = mysql_num_rows($qr_bancos);
                                   if (empty($num_bancos))
                                       continue;
                                   ?>
                                   <optgroup label="<?= $row_regioes['id_regiao'] . ' - ' . $row_regioes['regiao']; ?>">
                                       <?php
                                       while ($row_bancos = mysql_fetch_assoc($qr_bancos)):
                                           ?>
                                           <option value="<?= $row_bancos['id_banco']; ?>">
                                           <?= $row_bancos['id_banco'] . ' - ' . $row_bancos['nome']; ?>
                                           </option>
                                   <?php endwhile; ?>
                                   </optgroup>
                           <?php endwhile; ?>
                           </select>
                       </td>
                       <td class="secao">&nbsp;</td>
                       <td class="secao">&nbsp;</td>
                       <td class="secao">&nbsp;</td>
                    </tr>
                    <tr class="bloco_banco" style="display:none;">
                    	<td class="secao">Data de vencimento</td>
                    	<td colspan="2"><input type="text" name="data_entrada" id="data_entrada" /></td>
                        <td class="secao">&nbsp;</td>
                        <td class="secao">&nbsp;</td>
                        <td class="secao">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="6" style="text-align:center;">
                      	<input type="hidden" name="nota_anexo" id="nota_anexo" />
                      	<input type="submit" name="enviar" id="enviar" value="Continuar" />
                      </td>
                    </tr>
                  </table>
</form>
				
    </div>
    <div id="rodape">
        <?php include('../include/rodape.php');?>
    </div>
</div>
</body>
</html>