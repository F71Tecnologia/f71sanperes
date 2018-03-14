<?php 
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../../conn.php');
include('../../classes/formato_valor.php');
include('../../classes/formato_data.php');
include('../../funcoes.php');
include('../../adm/include/criptografia.php');

$subprojeto = $_GET['id'];
$regiao=$_GET['regiao'];






$query = mysql_query("SELECT * FROM subprojeto WHERE id_subprojeto = '$subprojeto'");
$row= mysql_fetch_assoc($query);
$total = mysql_num_rows($query);
$tipo_contratacao2=explode(',',$row['tipo_contratacao']);


if(isset($_POST['update'])) {
	
	

	

	$inicio			     = formato_americano($_POST['inicio']);
	$termino      	     = formato_americano($_POST['termino']);
	$data_assinatura=  formato_americano($_POST['data_assinatura']);
	$tipo_subprojeto=$_POST['termos'];
	
	if ($tipo_subprojeto = 'TERMO ADITIVO'){
	
	$tipo_termo_aditivo=$_POST['tipo_aditivo'];
	
	}
	
	
	$tipo=array();
		foreach($_POST['tipo_contratacao'] as $tipo_aux)
		{
			$tipo[]=$tipo_aux;
		}
		$tipo_contratacao=implode(',',$tipo);
		
	$total_participantes = (int)$_POST['total_participantes_clt'].' / '.(int)$_POST['total_participantes_autonomo'].' / '.(int)$_POST['total_participantes_cooperado'].' / '.(int)$_POST['total_participantes_autonomo_pj'];
	
	

								  
								  
	
		mysql_query("UPDATE subprojeto SET
										tipo_contrato = '$_POST[tipo_contrato]',numero_contrato='$_POST[numero_contrato]', inicio = '$inicio', termino = '$termino', 
										tipo_contratacao = '$tipo_contratacao', descricao = '$_POST[programa_trabalho]', total_participantes = '$total_participantes',
										verba_destinada = '$_POST[verba_destinada]', verba_periodo = '$_POST[verba_periodo]', taxa_adm = '$_POST[taxa_adm]', taxa_parceiro = '$_POST[taxa_parceiro]', id_parceiro = '$_POST[id_parceiro]', taxa_outra1 = '$_POST[taxa_outra1]', id_parceiro1 = '$_POST[id_parceiro1]', taxa_outra2 = '$_POST[taxa_outra2]', id_parceiro2 = '$_POST[id_parceiro2]', provisao_encargos = '$_POST[provisao_encargos]',tipo_subprojeto='$tipo_subprojeto', data_assinatura='$data_assinatura', tipo_termo_aditivo='$tipo_termo_aditivo', subprojeto_id_usuario_atualizacao='$_COOKIE[logado]', subprojeto_data_atualizacao=NOW()
								  WHERE id_subprojeto = '$subprojeto' LIMIT 1 ")
								  or die(mysql_error());
								  
		header("Location: renovacao2.php?m=$link_master&regiao=$regiao&id=$subprojeto");
		exit;

	
	
} else {
	
	
	$tipo_contrato       = $row['tipo_contrato'];
	$numero_contrato	 =$row['numero_contrato'];
	$inicio              = $row['inicio'];
	$termino             = $row['termino'];
	$prazo_renovacao     = $row['prazo_renovacao'];
	$tipo_contratacao    = $row['tipo_contratacao'];
	$programa_trabalho   = $row['descricao'];
	$total_participantes = $row['total_participantes'];
	$verba_destinada     = $row['verba_destinada'];
	$verba_periodo       = $row['verba_periodo'];
	$taxa_adm            = $row['taxa_adm'];
	$taxa_parceiro       = $row['taxa_parceiro'];
	$taxa_outra1         = $row['taxa_outra1'];
	$taxa_outra2         = $row['taxa_outra2'];
	$id_parceiro         = $row['id_parceiro'];
	$id_parceiro1        = $row['id_parceiro1'];
	$id_parceiro2        = $row['id_parceiro2'];
	$provisao_encargos   = $row['provisao_encargos'];
	
}

$regiao_origem = $_GET['regiao'];
$id_user       = $_COOKIE['logado'];

$qr_areas = mysql_query("SELECT area_nome FROM areas WHERE area_status = '1' ORDER BY area_nome ASC");

$area = explode(' / ', $area);
$tipo_contratacao    = explode(' / ', $tipo_contratacao);
$total_participantes = explode(' / ', $total_participantes);
?>
<html>
<head>
<title>:: Intranet :: Edi&ccedil;&atilde;o de Projeto</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="../favicon.ico">
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/ramon.js"></script>
<script type="text/javascript" src="../../jquery-1.3.2.js"></script>
<script type="application/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="application/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="application/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>
<script type="application/javascript" src="../../jquery/priceFormat.js" ></script>

<script>

$(function() {

$("#form1").validationEngine();
	$('#inicio').mask('99/99/9999');
	$('#termino').mask('99/99/9999');
	$('#prazo_renovacao').mask('99/99/9999');
	$('#verba_destinada').priceFormat();




	
	$('input[class=total_participantes][value=""]').attr('disabled',true);
	$('input[class=total_participantes][value="0"]').attr('disabled',true);
	$('.tipo_contratacao').change(function() {
		if($(this).val() == 'CLT') {
			if($('.total_participantes').eq(0).attr('disabled')) {
				$('.total_participantes').eq(0).attr('disabled',false).css('margin-bottom','1px');
			} else {
				$('.total_participantes').eq(0).attr('disabled',true);
			}
		} else if($(this).val() == 'Autônomo') {
			if($('.total_participantes').eq(1).attr('disabled')) {
				$('.total_participantes').eq(1).attr('disabled',false).css('margin-bottom','1px');
			} else {
				$('.total_participantes').eq(1).attr('disabled',true);
			}
		} else if($(this).val() == 'Cooperado') {
			if($('.total_participantes').eq(2).attr('disabled')) {
				$('.total_participantes').eq(2).attr('disabled',false).css('margin-bottom','1px');
			} else {
				$('.total_participantes').eq(2).attr('disabled',true);
			}
		} else if($(this).val() == 'Autônomo PJ') {
			if($('.total_participantes').eq(3).attr('disabled')) {
				$('.total_participantes').eq(3).attr('disabled',false).css('margin-bottom','1px');
			} else {
				$('.total_participantes').eq(3).attr('disabled',true);
			}
		}
	});
	
	$('.termos').change(function(){
	
		if($(this).val() =='TERMO ADITIVO' ) { 
													$('#tipo_aditivo').hide();
													$('#termo_aditivo').fadeIn(200);
													
													
													} else {
														
													$('#termo_aditivo').fadeOut(200);
													$('#tipo_aditivo').fadeIn(100);
													}
	
	});
	
	
	
	
	
	
	$('.tipo_aditivo').change(function(){
		
		if($(this).val() != 1) { 
			$('#tipo_aditivo').fadeOut(); 
			return false;
		}
		if($(this).attr('checked') == true ){
			$('#tipo_aditivo').fadeIn();
		}else{
			$('#tipo_aditivo').fadeOut();
			$('#termo_aditivo2').fadeIn();
		}
		
	});

	
}
)



</script>


</head>
<body>
<div id="corpo">
<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;">
               EDITAR SUBPROJETO: <span class="projeto"><?php echo $tipo_subprojeto;?></span>
           </h2>
           <p style="float:right;">
               <a href="renovacao2.php?m=<?=$link_master;?>&regiao=<?php echo $regiao; ?>&id=<?php echo $subprojeto; ?>">&laquo; Gerenciar Anexos</a>
           </p>
           <div class="clear"></div>
      </div>

      <?php if(!empty($erros)) {
		  		$erros = implode('<br>', $erros);
				echo '<p style="background-color:#C30; padding:4px; color:#FFF;">'.$erros.'</p><p>&nbsp;</p>';
			} ?>
      
	<form action="<?php echo $_SERVER['PHP_SELF'].'?m='.$link_master.'&regiao='.$regiao_origem?>&id=<?php echo $subprojeto?>" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
	  <table cellpadding="0" cellspacing="1" class="secao">
      
       <tr>
        <td class="secao_pai" colspan="6">RENOVAÇÕES</td>
      </tr>
      <tr>
        <td height="31" class="secao">Tipo de documento:</td>
        <td colspan="5" rowspan="2">
           <p>
             <label>
               <input type="radio" name="termos" value="TERMO DE PARCERIA " class="termos" <?php if($row['tipo_subprojeto']=='TERMO DE PARCERIA'){echo 'checked';}?> >
               Termo de Parceria </label>
             <br>
             <label>
               <input type="radio" name="termos" value="TERMO ADITIVO" class="termos" <?php if($row['tipo_subprojeto']=='TERMO ADITIVO'){echo 'checked';}?> >
               Termo Aditivo </label>
             <br>          
               <input type="radio" name="termos" value="NOVO CONV&Ecirc;NIO" class="termos"  <?php if($row['tipo_subprojeto']=='NOVO CONVÊNIO'){echo 'checked';}?> >
               Novo Conv&ecirc;nio</p></td>
      </tr>
      <tr>
        <td height="22" class="secao">&nbsp;</td>
      </tr>
      
      
      
      <tr>
      
    
     
         
         
	    <tr>
        <td class="secao_pai" colspan="4">PER&Iacute;ODO DO PROJETO</td>
      </tr>
      <tr>
        <td class="secao">Tipo de contrato:</td>
        <td colspan="3">
            <select name="tipo_contrato" id="tipo_contrato" class="validate[required]">
            
                <option value="Termo de Parceria" <?php if($tipo_contrato == 'Termo de Parceria') { echo 'selected="selected"'; } ?>>Termo de Parceria</option>
                <option value="Convênio" <?php if($tipo_contrato == 'Convênio') { echo 'selected="selected"'; } ?>>Convênio</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="secao">N&uacute;mero do contrato:</td>
        <td colspan="3"><label for="numero_contrato"></label>
          <input type="text" name="numero_contrato" id="numero_contrato" value="<?=$numero_contrato?>" />
          
          </td>
      </tr>
     <td class="secao" >
             Data de Assinatura:
             </td>
             <td colspan="3">
                <input name="data_assinatura" type="text" id="data_assinatura" size="15" maxlength="10" class="validate[required]" value="<?=formato_brasileiro($row['data_assinatura'])?>"/>
            </td>
    </tr>
    <tr  id="termo_aditivo" style="display:none;">
         
         
                          
                          <td class="secao">Tipo do Termo Aditivo:</td>
                          <td colspan="5">
                          <label>
                          <input type="radio" name="tipo_aditivo" class="tipo_aditivo" value="1" <?php if($row['tipo_termo_aditivo']==1){ echo 'checked';}?>> Prorrogação
                          </label>
                           <label>
                          <input type="radio" name="tipo_aditivo" class="tipo_aditivo" value="2"<?php if($row['tipo_termo_aditivo']==2){echo 'checked';}?>>Alteração Contratual
                          </label>
                          
                          </td>
                      
        </tr> 
           <tr  id="tipo_aditivo" style="display:none;">
           
           
             <td class="secao">In&iacute;cio:</td>
            
            <td>
                <input name="inicio" type="text" id="inicio" size="15" maxlength="10" value="<?=formato_brasileiro($row['inicio'])?>" />
            </td>
            <td class="secao">T&eacute;rmino:</td>
            <td>
              <input name="termino" type="text" id="termino" size="15" maxlength="10" value="<?=formato_brasileiro($row['termino'])?>"/>
            </td>
            
            </tr>
       
     
       
      
      
   
      
    
  </table>
    
    <table cellpadding="0" cellspacing="1" class="secao">
      <tr>
        <td class="secao_pai" colspan="6">OBJETOS DO CONTRATO</td>
      </tr>
      <tr>
        <td class="secao" style="text-align:left; padding:5px;">Tipo de contratação</td>
        <td class="secao" style="text-align:left; padding:5px;">Total de participantes</td>
        <td class="secao" style="text-align:left; padding:5px;" colspan="3">Objeto do contrato</td>
      </tr>
      <tr>
        <td valign="top">  
            <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="CLT" <?php if(in_array('CLT', $tipo_contratacao2)) { echo 'checked'; } ?>> CLT</label><br>
            <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Autônomo" <?php if(in_array('Autônomo', $tipo_contratacao2)) { echo 'checked'; } ?>> Autônomo</label><br>
            <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Cooperado" <?php if(in_array('Cooperado', $tipo_contratacao2)) { echo 'checked'; } ?>> Cooperado</label><br>
            <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Autônomo PJ" <?php if(in_array('Autônomo PJ', $tipo_contratacao2)) { echo 'checked'; } ?>> Autônomo PJ</label><br>
             <label><input name="tipo_contratacao[]" type="checkbox" class="tipo_contratacao reset" value="Nenhum" <?php if(in_array('Nenhum', $tipo_contratacao2)) { echo 'checked'; } ?>> Nenhum</label>
        </td>
        <td valign="top">
        	<input name="total_participantes_clt" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[0]; ?>" /><br>
            <input name="total_participantes_autonomo" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[1]; ?>" /><br>
            <input name="total_participantes_cooperado" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[2]; ?>" /><br>
            <input name="total_participantes_autonomo_pj" type="text" class="total_participantes" size="10" value="<?php echo $total_participantes[3]; ?>" />
        </td>
        <td colspan="5"><textarea name="programa_trabalho" type="text" id="programa_trabalho" cols="70" rows="25"><?php echo $programa_trabalho; ?></textarea></td>
      </tr>
    </table>
    
    <table cellpadding="0" cellspacing="1" class="secao">
      <tr>
        <td class="secao_pai" colspan="6">DADOS FINANCEIROS</td>
      </tr>
      <tr>
        <td class="secao" width="30%">Verba destinada:</td>
        <td colspan="5">
            <input name="verba_destinada" type="text"  class="validate[required]" id="verba_destinada" value="number<?php echo $verba_destinada; ?>" size="15" />
        </td>
      </tr>
      <tr>
        <td class="secao">Per&iacute;odo da verba:</td>
        <td colspan="5">
            <select name="verba_periodo" id="verba_periodo">
                <option value="Mensal" <?php if($verba_periodo == 'Mensal') { echo 'selected="selected"'; } ?>>Mensal</option>
                <option value="Trimestral" <?php if($verba_periodo == 'Trimestral') { echo 'selected="selected"'; } ?>>Trimestral</option>
                <option value="Semestral" <?php if($verba_periodo == 'Semestral') { echo 'selected="selected"'; } ?>>Semestral</option>
                <option value="Anual" <?php if($verba_periodo == 'Anual') { echo 'selected="selected"'; } ?>>Anual</option>
            </select>
        </td>
      </tr>
      <tr>
        <td class="secao">Taxas</td>
        <td colspan="5" style="line-height:28px;">Percentual ou Valor Apurado da Taxa do Projeto:
          <input name="taxa_adm" type="text" id="taxa_adm" size="15" value="<?php echo $taxa_adm; ?>" />
          (% ou fixo)<br>
            Parceiro Operacional: 
            <input name="taxa_parceiro" type="text" id="taxa_parceiro" size="15" value="<?php echo $taxa_parceiro; ?>" class="validate[required]"/>
            (% ou fixo)
            <select name="id_parceiro" id="id_parceiro">
            <option value="0">Selecione um parceiro</option>
			<?php $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
                  while($row_parceiro = mysql_fetch_assoc($qr_parceiros)) { 
                      echo '<option value="'.$row_parceiro['parceiro_id'].'"'; if($id_parceiro == $row_parceiro['parceiro_id']) { echo 'selected="selected"'; } ; echo '>'.$row_parceiro['parceiro_nome'].'</option>';
                  } ?>
            </select>
            <br>
            Parceiro Operacional 2: 
<input name="taxa_outra1" type="text" id="taxa_outra1" size="15" value="<?php echo $taxa_outra1; ?>" />
(% ou fixo)
<select name="id_parceiro1" id="id_parceiro1">
            <option value="0">Selecione um parceiro</option>
			<?php $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
                  while($row_parceiro = mysql_fetch_assoc($qr_parceiros)) { 
                      echo '<option value="'.$row_parceiro['parceiro_id'].'"'; if($id_parceiro1 == $row_parceiro['parceiro_id']) { echo 'selected="selected"'; } ; echo '>'.$row_parceiro['parceiro_nome'].'</option>';
                  } ?>
            </select>
            <br>
Parceiro Operacional 3: 
<input name="taxa_outra2" type="text" id="taxa_outra2" size="15" value="<?php echo $taxa_outra2; ?>" />
(% ou fixo)
<select name="id_parceiro2" id="id_parceiro2">
            <option value="0">Selecione um parceiro</option>
			<?php $qr_parceiros = mysql_query("SELECT * FROM parceiros WHERE parceiro_status = '1'");
                  while($row_parceiro = mysql_fetch_assoc($qr_parceiros)) { 
                      echo '<option value="'.$row_parceiro['parceiro_id'].'"'; if($id_parceiro2 == $row_parceiro['parceiro_id']) { echo 'selected="selected"'; } ; echo '>'.$row_parceiro['parceiro_nome'].'</option>';
                  } ?>
            </select>
        </td>
      </tr>
      <tr>
        <td class="secao" width="25%">Provis&atilde;o de encargos trabalhistas:</td>
        <td colspan="5">
            <input name="provisao_encargos" type="text" id="provisao_encargos" size="15" class="validate[required]" value="<?php echo formato_real($provisao_encargos); ?>" />
        </td>
      </tr>
    </table>

	<div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
	<div align="center">
        <input type="submit" name="Submit" value="ATUALIZAR" class="botao" />
    </div> 
    
   <input type="hidden" name="tipo_subprojeto" value="<?=$tipo_subprojeto?>" />
    	<input type="hidden" name="projeto" value="<?=$projeto?>" />
        <input type="hidden" name="usuario" value="<?=$id_user?>" />
        <input type="hidden" name="master" value="<?=$id_master?>" />
        <input type="hidden" name="update" value="1" />
     </form>
    </td>
  </tr>
</table>
</div>
</body>
</html>