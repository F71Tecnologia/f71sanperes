<?php

if(empty($_COOKIE['logado3']) and empty($_COOKIE['logado2'])){
	header('location: login_3.php?entre=true');
}

include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "../include/criptografia.php";


function formato_brasileiro($data) {

if($data != '0000-00-00') {	
	echo implode('/',array_reverse(explode('-',$data)));	
}
	
}


if(isset($_POST['enviar'])){
	
$nome 		    	= mysql_real_escape_string($_POST['nome']);	
$data_nasci     	= implode('-',array_reverse(explode('/',mysql_real_escape_string($_POST['data_nasci']))));	
$rg 		    	= mysql_real_escape_string($_POST['rg']);
$cpf 		    	= mysql_real_escape_string($_POST['cpf']);		
$atividade_nome 	= mysql_real_escape_string($_POST['atividade_nome']);	
$data_entrada   	= implode('-',array_reverse(explode('/',$_POST['data_entrada'])));
$data_saida     	= implode('-',array_reverse(explode('/',$_POST['data_saida'])));
$regiao_id     	 	= mysql_real_escape_string($_POST['regiao_id']);
$projeto_id     	= mysql_real_escape_string($_POST['projeto_id']);
$unidade 			= mysql_real_escape_string($_POST['unidade']);
$n_processo 		= mysql_real_escape_string($_POST['n_processo']);	
$valor_pedido 		= str_replace(',','.',str_replace('.','',mysql_real_escape_string($_POST['valor_pedido'])));
$local 				= mysql_real_escape_string($_POST['local']);
$adv_id 			= implode(',', $_POST['advogado']);
$prep_id 			= implode(',',$_POST['preposto']);
$valor_encerramento = str_replace(',','.',str_replace('.','',mysql_real_escape_string($_POST['valor_encerramento'])));
$parcelas			= mysql_real_escape_string($_POST['parcelas']);
$id_clt 			= mysql_real_escape_string($_POST['id_clt']);
$status_processo 	= mysql_real_escape_string($_POST['status_processo']);

if($status_processo != 9 or $status_processo != 10 ) {
	
unset($parcelas, $valor_encerramento); 

}


$insert = mysql_query("INSERT INTO processo_trabalhista
							(id_projeto,
							id_regiao, 
							id_clt, 							
							adv_id,
							preposto_id, 
							proc_trab_tipo_contratacao, 
							proc_trab_nome, 
							proc_trab_cpf, 
							proc_trab_rg, 
							proc_trab_data_nasc, 
							proc_trab_atividade, 
							proc_trab_unidade, 
							proc_trab_data_entrada, 
							proc_trab_data_saida, 
							proc_trab_numero_processo, 
							proc_trab_valor_pedido, 
							proc_trab_local, 
							proc_trab_valor_encerramento, 
							proc_trab_parcelas, 
							proc_trab_status_processo)
							
							 VALUES 
							 
							 ('$projeto_id',
							  '$regiao_id',
							  '$id_clt',
							  '$adv_id',
							  '$prep_id',
							  '2',
							  '$nome',
							  '$cpf',
							  '$rg',
							  '$data_nasci',
							  '$atividade_nome',
							  '$unidade',
							  '$data_entrada',
							  '$data_saida',
							  '$n_processo',
							  '$valor_pedido',
							  '$local',
							  '$valor_encerramento',
							  '$parcelas',
							  '$status_processo'
							 )") or die(mysql_error());

						
	if($insert) {
		
	header("Location: ../index.php?regiao=$regiao_id");
	
	}

}




$id_user   = $_COOKIE['logado'];
$regiao  = mysql_real_escape_string($_GET['regiao']);
$id_clt  = mysql_real_escape_string($_GET['clt']);
$id_projeto =  mysql_real_escape_string($_GET['projeto']);


$qr_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt='$id_clt'") or die (mysql_error());
$row_clt = mysql_fetch_assoc($qr_clt);
extract($row_clt);

$q_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_projeto'");
$row_projeto = mysql_fetch_assoc($q_projeto);

$q_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_regiao'");
$row_regiao = mysql_fetch_assoc($q_regiao);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../../js/ramon.js"></script>
<script type="text/javascript" src="../../js/jquery-1.3.2.js"></script>

<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>

<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>

<script type="text/javascript">
$(function() {

$('#cpf').mask('999.999.999-99');
$('#telefone').mask('(99)9999-9999');
$('#cel').mask('(99)9999-9999');
$('#valor_pedido').priceFormat({
	
	prefix:'',
	centsSeparator:',',
	thousandSeparator:'.',
	
	});
	
$('#valor_encerramento').priceFormat({
	
	prefix:'',
	centsSeparator:',',
	thousandSeparator:'.',
	
	})


	$('#form1').validationEngine();
	$('input[name=tipo]').change(function(){
			
		var tipo = $(this).val();
		
		if(tipo == 1) {
			
			$('#oab').fadeIn();
		
		} else {
			$('#oab').fadeOut();
		}
	
	
	
	});
	
	$('#add_preposto').click(function(){
	
		var campo = $('#campo_preposto').html();
		
		$('#campo_preposto').next().append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');
		
		
	});
	
	
	$('#add_advogado').click(function(){
	
		var campo = $('#campo_advogado').html();
		
		$('#campo_advogado').next().append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');
		
		
	});
	
	$('#status').change(function(){
		
	
		if($(this).val() == 9 || $(this).val() == 10) {
			
			$('#encerramento').show();
			
			} else {
				
				$('#encerramento').hide();
			}
		
		
		});
	/*$('.excluir').click(function(){
		$(this).parent().remove();
	
	});*/
	
	
});

</script>


<title>::Intranet:: Cadastro de Processos CLT</title>
</head>
<body>
<div id="corpo">

<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
  <tr>
    <td>
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;">
               CADASTRAR PROCESSO:<span class="projeto">CLT</span>
           </h2>
           
            
           <p style="float:right;margin-top:40px;">
               <a href="index.php?regiao=<?=$regiao?>">&laquo; Voltar</a>
           </p>
           
           <p style="float:right;margin-left:15px;background-color:transparent;">
               <?php include('../../reportar_erro.php'); ?>   		
           </p>
           <div class="clear"></div>
      </div>

      <?php if(!empty($erros)) {
		  		$erros = implode('<br>', $erros);
				echo '<p style="background-color:#C30; padding:4px; color:#FFF;">'.$erros.'</p><p>&nbsp;</p>';
			} ?>
      
	<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" name="form1" 
    id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">

    <table cellpadding="0" cellspacing="1" class="secao">
          <tr>
            <td colspan="4" class="secao_pai" style="border-top:1px solid #777;">DADOS</td>
          </tr>
          
            <tr>
             <td class="secao" width="150">Código do CLT:</td>
             <td colspan="3"><input name="cod_clt" size="50" type="text" id="cod_clt"  value="<?=$id_clt?>" /></td>
          </tr>
          
          <tr>
             <td class="secao" >Nome:</td>
             <td colspan="3"><input name="nome" size="50" type="text" id="nome"  value="<?=$nome?>" /></td>
          </tr>
          
          <tr>
            <td class="secao" >Data de nascimento:</td>
             <td colspan="3"><input name="data_nasci" size="15" type="text" value="<?=$data_nasci?>" /> </td>
          </tr>
           
           <tr>
            <td class="secao">RG:</td>
             <td ><input name="rg" size="15" type="text" id="rg"   value="<?=$rg?>" /></td>
          
            <td class="secao">CPF:</td>
             <td ><input name="cpf" size="10" type="text" id="cpf"  value="<?=$cpf?>" /></td>
          </tr>
          
           <tr>
            <td class="secao">Atividade:</td>
            	<?php
                $q_atividade = mysql_query("SELECT * FROM curso WHERE id_curso='$id_curso'" ) or die(mysql_error());
				$row_atividade = mysql_fetch_assoc($q_atividade);
				?>
            
             <td colspan="3">
             <input name="atividade_nome" size="90" type="text" id="atividade_nome" value="<?=$row_atividade['nome']?>" />
           
             
             </td>
          </tr>
           <tr>
                 <td class="secao">Dta de entrada:</td>
                 <td>
                    <input name="data_entrada" size="20" type="text" id="data_entrada" value="<?=formato_brasileiro($data_entrada)?>" />              
                    
                    </td>
                 <td class="secao">Data de saída:</td>
                 <td>
                    <input name="data_saida" size="20" type="text" id="data_entrada" cvalue="<?=formato_brasileiro($data_saida)?>" />              
                    
                    </td>

                
          </tr>
           <tr>
            <td class="secao">Região:</td>
             <td colspan="3">
             	<input name="regiao_nome" size="50" type="text" id="regiao_nome" class="validate[required]" value="<?=$row_regiao['regiao']?>" disabled/>
                <input name="regiao_id"  type="hidden" id="regiao_id"  value="<?=$id_regiao;?>" />
                
                </td>
          </tr>
          
            <tr>
            <td class="secao">Projeto:</td>
             <td colspan="3">
             <input name="projeto_nome" size="50" type="text" id="projeto_nome" class="validate[required]" value="<?=$row_projeto['nome']?>"  disabled/>
             <input name="projeto_id"  type="hidden" id="projeto_id"  value="<?=$id_projeto?>" />
             
             </td>
          </tr>
          
           <tr>
            <td class="secao">Unidade:</td>
             <td colspan="3"><input name="unidade" size="50" type="text" id="unidade" class="validate[required]" value="<?=$locacao?>" /></td>
          </tr>
          
          <tr>
            <td class="secao">N&ordm; do Processo:</td>
             <td><input name="n_processo" size="30" type="text" id="n_processo" class="validate[required]" /></td>
             
             <td class="secao">Valor Pedido:</td>
             <td  colspan="1"><input name="valor_pedido" size="10" type="text" id="valor_pedido" /></td>
          </tr>
              
           <tr>
            <td class="secao">Local:</td>
             <td  colspan="3"><input name="local" size="30" type="text" id="local" /></td>
          </tr>
          
          
             <tr>
                 <td class="secao" >Advogado:
                 <br />
                 	<a href="#" onclick="return(false)" id="add_advogado"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                 </td>
                 <td>
                 <div id="campo_advogado">
                        <select name="advogado[]">
                        <?php
                        $qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");
                        while($row_advogado = mysql_fetch_assoc($qr_advogado)):
                        ?>
                        <option value="<?php echo $row_advogado['adv_id']?>"> <?php echo $row_advogado['adv_nome']?> </option>
                        
                        <?php
                        endwhile;
                        ?>
                        
                        
                        </select>
                 </div>
                    
                <div></div>
                </td>
                
                 <td class="secao" >Preposto:<br />
                 	<a href="#" onclick="return(false)" id="add_preposto"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                 </td>
                 <td>
                 <div id="campo_preposto">
                	<select name="preposto[]">
                    <?php
					$qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_status = 1");
					while($row_preposto = mysql_fetch_assoc($qr_preposto)):
					?>
					<option value="<?php echo $row_preposto['prep_id']?>"> <?php echo $row_preposto['prep_nome']?> </option>
                    
					<?php
					endwhile;
					?>
                    
                    </select>
                 </div>
                 <div></div>                 
                </td>
                
              </tr>
        
        <tr>
        	<td class="secao">Status do processo:</td>
            <td colspan="3">
            <select name="status_processo" id="status">
            <?php
			$qr_status = mysql_query("SELECT * FROM processo_status WHERE 1");
			while($row_status = mysql_fetch_assoc($qr_status)):
			?>
            <option value="<?php echo $row_status['proc_status_id'];?>"> <?php echo $row_status['proc_status_nome']?> </option>
			<?php
            endwhile;			
			?>
            </select>
            </td>
        </tr>
        <tr id="encerramento" style="display:none;">
        	<td class="secao">Valor do Acordo ou Sentença:</td>
            <td><input type="text" name="valor_encerramento" id="valor_encerramento"/></td>
            
            <td class="secao">Parcelas:</td>
            <td><input type="text" name="parcelas" /></td>
        </tr>
        
          
          
          <tr>
          	<td  colspan="4" align="center" style="text-align:center;">
            <input name="id_clt" type="hidden" value="<?php echo $id_clt;?>"/>
            <input name="enviar" type="submit" value="CADASTRAR"/>
            </td>
          </tr>
          
    </table>
    </form>
    </td>
    </tr>

</table>
</div>
</body>
</html>
