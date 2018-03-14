<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');
include('../../classes_permissoes/projeto.class.php');

$PROJETO = new Projeto();



$id_oscip=$_GET['id'];
$qr_oscip=mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_oscip='$id_oscip'");
$row_oscip=mysql_fetch_assoc($qr_oscip);




if(isset($_POST['atualizar']))

{
$tipo=$_POST['tipo'];
$numero=$_POST['numero'];
$descricao=$_POST['descricao'];
$data_publicacao=implode('-',array_reverse(explode('/',$_POST['data_publicacao'])));
$numero_periodo=$_POST['numero_periodo'];
$periodo=$_POST['periodo'];
$usuario=$_COOKIE['logado'];
$id_projeto=$_POST['projeto'];
$inicio=implode('-',array_reverse(explode('/',$_POST['inicio'])));
$termino=implode('-',array_reverse(explode('/',$_POST['termino'])));
$endereco = $_POST['endereco']; 
$respostaEnviada = trim($_POST['respostaEnv']);
$respostaRecebida = trim($_POST['respostaRec']);

if($respostaEnviada != ""){$resposta = $respostaEnviada;}else if($respostaRecebida != ""){$resposta = $respostaRecebida;}





if($periodo=='Indeterminado'){
	$numero_periodo=NULL;
	$inicio=NULL;
		$termino=NULL;
	
	}else if($periodo=='Período')
	{
		$numero_periodo=NULL;
		} else if($periodo=='Dias' or $periodo=='Meses' or $periodo=='Anos' )
			{
				$inicio=NULL;
			$termino=NULL;
				}





$qr_inserir=mysql_query("UPDATE obrigacoes_oscip SET tipo_oscip='$tipo', numero_oscip='$numero', descricao='$descricao', data_publicacao='$data_publicacao', numero_periodo='$numero_periodo', periodo='$periodo', usuario_atualizacao='$usuario', data_atualizacao=NOW(),id_projeto='$id_projeto', oscip_data_inicio='$inicio', oscip_data_termino='$termino',oscip_endereco = '$endereco',resp_env_rec = '$resposta'
 WHERE id_oscip='$id_oscip' LIMIT 1 ")or die('Erro!');




$nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"),0);	
registrar_log('ADMINISTRAÇÃO - EDIÇÃO DE OBRIGAÇÕES DA EMPRESA', $nome_funcionario.' editou a obrigação: '.'('.$id_oscip.') - '.$tipo);	



header("Location: cadastro_oscip2.php?m=$link_master&id=$id_oscip");
}
?>

<html>
<head>
<title>:: Intranet :: Edição OSCIP</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../../js/ramon.js"></script>
<script type="text/javascript" src="../../jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">

<link href="../../js/highslide.css" rel="stylesheet" type="text/css"  /> 
<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 

<script>
  hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';


$(function(){
		$("#form1").validationEngine();	
		$('#data_publicacao').mask('99/99/9999');
		$('#tipo').validationEngine;
		$('#inicio').mask('99/99/9999');
		$('#termino').mask('99/99/9999');

		
			$('.alvara').change( function (){
			if($(this).val()=='Alvará de Funcionamento')
			{
				$('#projeto').hide();
				$('#endereco').fadeIn();
			}
			
			
			});
				
		
		$('.tipo').change( function (){
			if(($(this).val()!='Publicação Anexo 1 em Jornal') )
			{
			$('#projeto').fadeOut();
			
			} 
			
			if(($(this).val()!='Alvará de Funcionamento') )
						{
						$('#endereco').fadeOut();
						
						} 
			
			
			
		});
			
		$('.anexo').change( function (){
			
			
			if($(this).attr('checked')==true) 
				{
				$('#projeto').fadeIn();
				
				}		
			
				});	
	
		$('#periodo').change(function (){
			
						if($(this).val()=='Indeterminado') {
						
						$('#validade').fadeOut();
						$('#periodo_data').fadeOut();
						
						} 
						else 
						if($(this).val()=='Período') 					
									{
									$('#periodo_data').fadeIn();	
									$('#validade').hide();
													
									} else {
										$('#validade').fadeIn();
										$('#periodo_data').hide();
										}
						
				if($(this).val()=='Dias') {
					$('#menssagem').show();
					$('#menssagem').empty();
					$('#menssagem').append(' <i>Digite o número de dias</i>');
					}
					else 
						if($(this).val()=='Meses') {
						$('#menssagem').show();
						$('#menssagem').empty();	
						$('#menssagem').append(' <i>Digite o número de meses</i>');
						}
						 else
						 if($(this).val()=='Anos') {
							 $('#menssagem').show();
							 $('#menssagem').empty();
							 $('#menssagem').append(' <i>Digite o número de anos</i>');
						}
				
			
			
			
		});	
		
		
				
			$('.alvara').change( function (){
			if($(this).val()=='Alvará de Funcionamento')
			{
				$('#projeto').hide();
				$('#endereco').fadeIn();
			}
			
			
			});
				
		
		$('.tipo').change( function (){
			if(($(this).val()!='Publicação Anexo 1 em Jornal') )
			{
			$('#projeto').fadeOut();
			
			} 
			
			if(($(this).val()!='Alvará de Funcionamento') )
						{
						$('#endereco').fadeOut();
						
						} 
			
			
			
		});	
	
	if ($('.alvara').attr('checked')==true){
			$('#endereco').show();
			
			}
		if ($('.anexo').attr('checked')==true){
			$('#projeto').show();
			
			}
			if ($('.relatorio').attr('checked')==true){
			$('#projeto').show();
			
			}
			
	if($('#periodo').val()=='Indeterminado') {
			
			$('#validade').hide();
			$('#periodo_data').hide();
			
			} else if ( ($('#periodo').val()=='Período') ){
				
				$('#periodo_data').show();
				$('#validade').hide();
				}else {
					
					$('#validade').show();
				}
				
		
		
		
		
	$('#form1').submit( function(){
			
					
				$('#atualizar').hide();
				$('#atualizando').show();
				return true;			
			
		});
				
    $("input[type=radio]").click(function(){
        var tipo = $(this).val();
        if(tipo == 'Ofícios Enviados'){
            $('#linhaResposta,#respostaRec').show();
            $('#respostaEnv').hide();
            $('#respostaEnv').val("");
        }else if(tipo == 'Ofícios Recebidos'){
            $('#linhaResposta,#respostaEnv').show();
            $('#respostaRec').hide();
            $('#respostaRec').val("");
        }else{
            $('#linhaResposta,#respostaEnv,#respostaRec').hide();
            $('#respostaRec,#respostaEnv').val("");
        }
    });
});

</script>
</head>
<body>
<div id="corpo">





<table align="center" width="100%" cellspacing="0" cellpadding="12" class="secao">
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;">
               Editar: <span class="projeto"><?=$row_oscip['tipo_oscip'];?></span>
               
			

			     </span>
</h2>
              
           <p style="float:right;margin-top:40px;">
              <a href="../../adm/adm_contratos/dados_oscip.php?m=<?=$link_master?>"><span style="color:#900;">&laquo; Voltar</span></a>
           </p>
            <p style="float:right;">
           
             <?php    $pagina = $_SERVER['PHP_SELF']; ?>
             
         <span style="position:relative;  margin-right:10px;">   <a href="../../box_suporte.php?&regiao=<?php echo $regiao;?>&pagina=<?php echo $pagina;?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" ><img src="../../imagens/suporte.gif"  width="55" height="55"/></a></span>	
         
           </p>
           <div class="clear"></div>
      </div>

  
      <?php
$qr_oscip=mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_oscip='$id_oscip'");
$row_oscip=mysql_fetch_assoc($qr_oscip);

$usuario = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1;"));

if($row_oscip['tipo_oscip'] == 'Ofícios Enviados'){
    $displayRespostaEnv = 'display: none;';
}else if($row_oscip['tipo_oscip'] == 'Ofícios Recebidos'){
    $displayRespostaRec = 'display: none;';
}else{
    $displayRespostaRec = $displayRespostaEnv = 'display: none;';
}

$sqlRespostaEnv = "SELECT id_oscip, numero_oscip FROM obrigacoes_oscip WHERE  status='1' AND tipo_oscip = 'Ofícios Enviados' AND id_master='{$usuario['id_master']}' GROUP BY descricao ORDER BY tipo_oscip DESC;";
$sqlRespostaEnv = mysql_query($sqlRespostaEnv);
$respostaEnv = '<select name="respostaEnv" id="respostaEnv" style="width: 164px; '.$displayRespostaEnv.'">';
$respostaEnv .= '<option value="">Selecione</option>';
while($rowRespostaEnv = mysql_fetch_assoc($sqlRespostaEnv)){
    if($rowRespostaEnv['id_oscip'] == $row_oscip['resp_env_rec']){
        $respostaEnv .= '<option value="'.$rowRespostaEnv['id_oscip'].'" SELECTED >'.$rowRespostaEnv['numero_oscip'].'</option>';
    }else{
        $respostaEnv .= '<option value="'.$rowRespostaEnv['id_oscip'].'">'.$rowRespostaEnv['numero_oscip'].'</option>';
    }
}
$respostaEnv .= '</select>';

$sqlRespostaRec = "SELECT id_oscip, numero_oscip FROM obrigacoes_oscip WHERE  status='1' AND tipo_oscip = 'Ofícios Recebidos' AND id_master='{$usuario['id_master']}' GROUP BY descricao ORDER BY tipo_oscip DESC;";
$sqlRespostaRec = mysql_query($sqlRespostaRec);
$respostaRec = '<select name="respostaRec" id="respostaRec" style="width: 164px; '.$displayRespostaRec.'">';
$respostaRec .= '<option value="">Selecione</option>';
while($rowRespostaRec = mysql_fetch_assoc($sqlRespostaRec)){
    if($rowRespostaRec['id_oscip'] == $row_oscip['resp_env_rec']){
        $respostaRec .= '<option value="'.$rowRespostaRec['id_oscip'].'" SELECTED >'.$rowRespostaRec['numero_oscip'].'</option>';
    }else{
        $respostaRec .= '<option value="'.$rowRespostaRec['id_oscip'].'">'.$rowRespostaRec['numero_oscip'].'</option>';
    }
}
$respostaRec .= '</select>';

?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>?m=<?=$link_master?>&id=<?=$id_oscip?>" method="post" name="form1" id="form1" enctype="multipart/form-data" >
	  <table cellpadding="0" cellspacing="1" class="secao">
      <tr class="secao_pai">
      <td colspan="4" class="secao_pai">Tipo:</td>
      </tr>
	    <tr>
      
        <td colspan="4" >
        		  <?php
                    $qr_tipo_oscip = mysql_query("SELECT * FROM tipo_doc_oscip WHERE 1 ORDER BY tipo_nome");
                    while($row_tipo = mysql_fetch_assoc($qr_tipo_oscip)):  
//                    extract($row_tipo);
            
                        switch($row_tipo['tipo_id']) {
                            
                            case 10: 	 
										$classe = 'class="anexo" ';
                            break;
							
                            case 19: 	 $classe = 'class="relatorio" ';
                            break;
                            case 12: $classe = 'class="alvara"';
                            break;
                            
                            default: $classe = 'class="tipo"';
                            break;
                        }
                       
					    $checked=($row_tipo['tipo_nome'] == $row_oscip['tipo_oscip']) ?  'checked="checked"' : '';
					        
                    ?>
                  
                      <input type="radio" name="tipo" <?=$classe?> value="<?=$row_tipo['tipo_nome']?>"  <?=$checked?>>
                      <?=$row_tipo['tipo_nome']?><br>
                  
                  
                  <?php endwhile;  ?>
        
        
        
        
        
        
          
          </td>
            
            
        </tr>
        
           <tr  id="projeto" style="display:none;">
           <td class="secao	">
           
           Projeto:
           </td>
             	<td align="left"  colspan="3">
            <select name="projeto">
            <option value="">Selecione um projeto...</option>
            <?php
			$PROJETO->Preenhe_select_por_master($Master, $row_oscip['id_projeto']);
			
			?>
            
            </select>
            </td>  
          </tr>  
          
	    <tr>
	      <td width="10%" align="center" class="secao">N&ordm; do documento:</td>
	      <td colspan="3" ><label for="numero"></label>
	        <input type="text" name="numero" id="numero" value="<?=$row_oscip['numero_oscip']?>"></td>
	      </tr>
          
          <tr id="endereco" style="display:none;">
         	<td align="center" class="secao">Endereço:</td>
			<td colspan="5">
            <input name="endereco" type="text" value="<?php echo $row_oscip['oscip_endereco'];?>"/>
            </td>
         </tr> 
          
          
	    <tr>
	      <td align="center" class="secao">Descri&ccedil;&atilde;o:</td>
          
          
	      <td colspan="3" ><label for="descricao"></label>
	        <textarea name="descricao" id="descricao" cols="45" rows="5"><?=$row_oscip['descricao']?></textarea>
            
            </td>
	      </tr>
	    <tr>
	      <td align="center" valign="top" class="secao">Data da publica&ccedil;&atilde;o:</td>
	      <td colspan="3" valign="top" ><label for="data_publicacao"></label>
	        <input type="text" name="data_publicacao" id="data_publicacao" value="<?php echo formato_brasileiro($row_oscip['data_publicacao'])?>">	        <div id="barra_processo">
	          
	          
	          
	          </div>	        </td>
	      </tr>
       
          
    
     
             
             
             <tr>
             	<td align="center" class="secao">
                	Período:
                    </td> 
              <td width="24%" colspan="3"><select name="periodo" id="periodo" class="validate[required]" >
                <option value="">Selecione um per&iacute;odo..</option>
                <option value="Dias" <?php if ($row_oscip['periodo']=='Dias'){ echo 'selected="selected"'; } ?>>Dias</option>
                <option value="Meses" <?php if ($row_oscip['periodo']=='Meses'){ echo 'selected="selected"'; } ?>>Meses </option>
                <option value="Anos" <?php if ($row_oscip['periodo']=='Anos'){ echo 'selected="selected"'; } ?>>Anos</option>
                <option value="Período" <?php if ($row_oscip['periodo']=='Período'){ echo 'selected="selected"'; } ?>>Período</option>
                <option value="Indeterminado" <?php if ($row_oscip['periodo']=='Indeterminado'){ echo 'selected="selected"'; } ?>>Indeterminado</option>
                  
                
              </select></td>
          </tr>
            <tr id="linhaResposta" style="<?php echo $displayLinha; ?>">
                <td align="center" class="secao">Resposta De:</td> 
                <td width="24%" colspan="5">
                    <?php echo $respostaEnv.$respostaRec;?>
                </td>
            </tr>
             
          <tr id="validade" style="display:none;">
            <td align="center" class="secao">Validade:</td>
            <td width="24%" colspan="2">
              <input type="text" name="numero_periodo" id="numero_periodo"  value="<?=$row_oscip['numero_periodo']?>">
              </td>
              <td colspan="4">
               <div id="menssagem" style="display:none;"> </div>
              </td>
             
             </tr> 
          
             
             
           <tr id="periodo_data" style="display:none;">
             	<td align="center" class="secao">
                Data de início:
                
                </td>
                <td>
                <input name="inicio" type="text" id="inicio"  value="<?php if($row_oscip['oscip_data_inicio']!='0000-00-00'){ echo formato_brasileiro($row_oscip['oscip_data_inicio']);}?>">
                
                </td>
                <td align="center" class="secao">
                Data de termino:
                </td>
                
                <td>
                <input name="termino" type="text" id="termino" value="<?php if($row_oscip['oscip_data_termino']!='0000-00-00'){ echo formato_brasileiro($row_oscip['oscip_data_termino']);}?>">
                </td>
          </tr>
      
      
      </table>
    <br>
    <table>
    
     <div align="center">
     		<div id="atualizando" style="display:none;">
            	Atualizando...<br><img src="../../imagens/1-carregando.gif"/>
            </div>
           <input type="submit" name="atualizar" id="atualizar" value="Atualizar"/>
           </div>
		   
         
        <input type="hidden" name="usuario" value="<?=$id_user?>" />
        <input type="hidden" name="master" value="<?=$id_master?>" />
        
        <input type="hidden" name="update" value="1" />
     </form>
    </td>
  </tr>
</table>
<br>

</div>
</body>
</html>