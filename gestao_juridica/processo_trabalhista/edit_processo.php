<?php
include ("../include/restricoes.php");
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



$array_n_processo =  $_POST['n_processo'];
$array_ordem	   =   $_POST['ordem'];	
	

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
$valor_pedido 		= str_replace(',','.',str_replace('.','',mysql_real_escape_string($_POST['valor_pedido'])));
$local 				= mysql_real_escape_string($_POST['local']);
$adv_id 			= implode(',', $_POST['advogado']);
$prep_id 			= implode(',',$_POST['preposto']);

$id_trabalhador 	= mysql_real_escape_string($_POST['id_trabalhador']);
$tipo_contratacao 	= mysql_real_escape_string($_POST['tipo_contratacao']);	
$id_processo 		=  mysql_real_escape_string($_POST['id_processo']);	
$numero_vara		= $_POST['n_vara'];
$pedido_acao        = $_POST['pedidos_acao'];

if($tipo_contratacao == 2) {
	$campo_trabalhador =  'id_clt';	
} else {
	$campo_trabalhador = 'id_autonomo';	
}



$insert = mysql_query("UPDATE processos_juridicos SET 
							id_projeto  		  = '$projeto_id',
							id_regiao			  = '$regiao_id',
							$campo_trabalhador    = '$id_trabalhador',						
							adv_id		 		  = '$adv_id',
							preposto_id 		  = '$prep_id',
							proc_tipo_id		  = '1',
							proc_tipo_contratacao = '$tipo_contratacao',
							proc_nome  		  	  = '$nome',
							proc_cpf  			  =  '$cpf',
							proc_rg 			  = '$rg',
							proc_data_nasc	      =  '$data_nasci',
							proc_atividade		  = '$atividade_nome',
							proc_unidade   		  = '$unidade',
							proc_data_entrada 	  = '$data_entrada',
							proc_data_saida  	  =  '$data_saida',
							proc_numero_processo  = '$n_processo',
							proc_valor_pedido 	  =  '$valor_pedido',
							proc_local			  = '$local',
							pedido_acao 		  = '$pedido_acao',
							proc_numero_vara      = '$numero_vara',
							data_atualizacao	  =	NOW(),
							usuario_atualizacao   = '$_COOKIE[logado]'			
							WHERE proc_id 	= '$id_processo'					
							") or die(mysql_error());

		
		    mysql_query("DELETE  FROM n_processos WHERE proc_id = $id_processo");				
			foreach($array_n_processo as $chave => $valor) {
					
				$ordem = $array_ordem[$chave];	
				mysql_query("INSERT INTO n_processos (n_processo_numero, n_processo_ordem, proc_id, status)
													  VALUES
													  ('$valor','$ordem', '$id_processo	', 1)") or die(mysql_error())	;
				}
					
	if($insert) {
		
	if(!empty($_POST['mudar_status']) and !empty($_POST['alteracao_status'])){
	
	$andamento_id  = $_POST['status_atual'];
	$status_id     = $_POST['alteracao_status'];
	$valor 		   = str_replace(',','',$_POST['valor']);
	$data_pg       =  implode('-',array_reverse(explode('/',$_POST['data_pg'])));
	$n_parcelas    = $_POST['n_parcelas'];

	mysql_query("UPDATE proc_trab_andamento SET  proc_status_id = '$status_id',
												 andamento_valor = '$valor',
												 andamento_data_pg = '$data_pg',
												 andamento_parcelas = '$n_parcelas'
											     WHERE andamento_id = '$andamento_id' LIMIT 1");
		
	}
		
		
	header("Location: ../index.php?regiao=$regiao_id");
	
	}

}




$id_user   = $_COOKIE['logado'];
$id_processo  = mysql_real_escape_string($_GET['id_processo']);

//DADOS DO PROCESSO E DO TRABALHADOR
$qr_processo = mysql_query("SELECT * FROM processos_juridicos WHERE proc_id = '$id_processo' ") or die(mysql_error());
$row_processo = mysql_fetch_assoc($qr_processo);

//TIPO DE CONTRATAÇÃO
$qr_tipo_contratacao = mysql_query("SELECT * FROM tipo_contratacao WHERE tipo_contratacao_id = '$row_processo[proc_tipo_contratacao]'");
$row_tipo = mysql_fetch_assoc($qr_tipo_contratacao);	

if($row_processo['proc_tipo_contratacao'] == 2) {
	
	$id_trabalhador = $row_processo['id_clt'];
	
} else {
	
	$id_trabalhador = $row_processo['id_autonomo'];
}






$q_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_processo[id_projeto]'");
$row_projeto = mysql_fetch_assoc($q_projeto);

$q_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_processo[id_regiao]'");
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
$('#data_saida').mask('99/99/9999');
$('#data_pg').mask('99/99/9999');
$('.numero_processo').mask('9999999-99.9999.9.99.9999');
$('#valor_pedido').priceFormat({
	
	prefix:'',
	centsSeparator:',',
	thousandSeparator:'.',
	
	});
$('#valor').priceFormat({
		
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
	
	var campo = $('#select_preposto').html();
		
		$('#campo_preposto').next().append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/ </a></div>');
		
		
	});
	
	
	$('#add_advogado').click(function(){
	
		var campo = $('#select_advogado').html();
		
		$('#campo_advogado').next().append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/ </a></div>');
		
		
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
	
	$('#mudar_status').change(function(){
		
		var checked = $(this).attr('checked');
		if(checked) { 
		
			$('#alteracao_status').show();
		
		} else {
			
			$('#alteracao_status').val('').hide();
			$('.outros').fadeOut();
			$('#campo_horario').val('');
			$('#data_pg').val('');
			$('#n_parcelas').val('');
			$('#valor').val('');
			
		}
		
	});
	
	
	
	$('#alteracao_status').change(function(){
		
		var valor = $(this).val();
		
		if(valor == 7  ||  valor == 8 || valor == 9 || valor == 10 || valor == 11) {
			
			$('.outros').fadeIn();
			$('#campo_horario').fadeOut();
		
		} else if(valor != 22) {	
					
					$('.outros').fadeOut();
						$('#data_pg').val('');
						$('#n_parcelas').val('');
						$('#valor').val('');
					
				} else {
					
					$('.outros').fadeOut();
					$('#campo_horario').val('');
					$('#data_pg').val('');
					$('#n_parcelas').val('');
					$('#valor').val('');
					
				}
		
	
	});	
	
		$('#add_n_processo').click(function(){

			var campo = '<input name="n_processo[]" size="30" type="text" id="n_processo"  class="numero_processo"/> <label>Ordem:</label><input name="ordem[]" type="text" size="2" />';	
			
		$('#campos_n_processo').append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');
		$('.numero_processo').mask('9999999-99.9999.9.99.9999');
		return false;
		
		
	});
	
	
	
});

</script>


<title>::Intranet:: Cadastro de Processos CLT</title>
</head>
<body>

<div id="corpo">

<table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">	
  <tr>
    <td><a href="#" onclick="history:back();"></a>
      <div style="border-bottom:2px solid #F3F3F3; margin:10px 0 18px 0;">
           <h2 style="float:left; font-size:18px;margin-top:40px;">
               EDITAR PROCESSO:<span class="projeto"> <?=$nome?>
            </span>
           </h2>
           
            
           <p style="float:right;margin-top:40px;">
                <a href="../index.php?regiao=<?=$_GET['regiao']?>">&laquo; Voltar</a>
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
            <td colspan="6" class="secao_pai" style="border-top:1px solid #777;">DADOS</td>
          </tr>
          
            <tr>
             <td class="secao" width="150">Código:</td>
             <td colspan="5">
             <input name="" size="50" type="text" id="cod"  value="<?php echo $id_trabalhador; ?>" disabled/>
             <input name="id_trabalhador" size="50" type="hidden" id="cod"  value="<?php echo $id_trabalhador; ?>" />
             
             </td>
          </tr>
          
           <tr>
             <td class="secao" width="150">Tipo de Contratação</td>
             <td colspan="5">            
              <input size="50" type="text"  value="<?=$row_tipo['tipo_contratacao_nome']?>" disabled="disabled" />
              <input name="tipo_contratacao" size="50" type="hidden" id="tipo_contratacao"  value="<?php echo  $row_processo['proc_tipo_contratacao'];?>" />
             </td>
          </tr>
          
          <tr>
             <td class="secao" >Nome:</td>
             <td colspan="5">
             <input name="q" size="50" type="text" id="nome"  value="<?=$row_processo['proc_nome']?>" disabled/>
             <input name="nome" size="50" type="hidden" id="nome"  value="<?=$row_processo['proc_nome']?>" />
             
             </td>
          </tr>
          
          <tr>
            <td class="secao" >Data de nascimento:</td>
             <td colspan="5">
             <input name="q" size="15" type="text" value="<?=$row_processo['proc_data_nasc']?>" disabled/> 
             <input name="data_nasci" size="15" type="hidden" value="<?=$row_processo['proc_data_nasc']?>" /> 
             
             </td>
          </tr>
           
           <tr>
            <td class="secao">RG:</td>
             <td>
             <input name="q" size="15" type="text" id="rg"   value="<?=$row_processo['proc_rg']?>" disabled/>
             <input name="rg" size="15" type="hidden" id="rg"   value="<?=$row_processo['proc_rg']?>" />
            </td>
          
            <td class="secao">CPF:</td>
             <td colspan="3">
             <input name="w" size="20" type="text" id="cpf"  value="<?=$row_processo['proc_cpf']?>" disabled/>
             <input name="cpf" size="20" type="hidden" id="cpf"  value="<?=$row_processo['proc_cpf']?>" />
             </td>
          </tr>
          
           <tr>
            <td class="secao">Atividade:</td>
            	<?php
                $q_atividade = mysql_query("SELECT * FROM curso WHERE id_curso='$id_curso'" ) or die(mysql_error());
				$row_atividade = mysql_fetch_assoc($q_atividade);
				?>
            
             <td colspan="5">
             <input name="q" size="90" type="text" id="atividade_nome" value="<?=$row_processo['proc_atividade']?>" disabled/>
             <input name="atividade_nome" size="90" type="hidden" id="atividade_nome" value="<?=$row_processo['proc_atividade']?>" />
           
             
             </td>
          </tr>
           <tr>
                 <td class="secao">Data de entrada:</td>
                 <td>
                    <input name="as" size="20" type="text" value="<?=formato_brasileiro($row_processo['proc_data_entrada'])?>" disabled />
                    <input name="data_entrada" size="20" type="hidden" id="data_entrada" value="<?=formato_brasileiro($row_processo['proc_data_entrada'])?>" />   
                    </td>
                 <td class="secao">Data de saída:</td>
                 <td colspan="3">
                    <input name="b" size="20" type="text" id="data_entrada" value="<?=formato_brasileiro($row_processo['proc_data_saida'])?>" disabled/>              
                    <input name="data_saida" size="20" type="hidden" id="data_entrada" value="<?=formato_brasileiro($row_processo['proc_data_saida'])?>"/>              
                    
                 </td>

                
          </tr>
           <tr>
            <td class="secao">Região:</td>
             <td colspan="5">
             	<input name="regiao_nome" size="50" type="text" id="regiao_nome" class="validate[required]" value="<?=$row_regiao['regiao']?>" disabled/>
                <input name="regiao_id"  type="hidden" id="regiao_id"  value="<?=$row_processo['id_regiao']?>" />
                
                </td>
          </tr>
          
            <tr>
            <td class="secao">Projeto:</td>
             <td colspan="5">
             <input name="projeto_nome" size="50" type="text" id="projeto_nome" class="validate[required]" value="<?=$row_projeto['nome']?>"  disabled/>
             <input name="projeto_id"  type="hidden" id="projeto_id"  value="<?=$row_processo['id_projeto']?>" />
             
             </td>
          </tr>
          
           <tr>
            <td class="secao">Unidade:</td>
             <td colspan="5"><input name="unidade" size="50" type="text" id="unidade" class="validate[required]" value="<?=$row_processo['proc_unidade']?>" /></td>
          </tr>
          
         <tr>
          <td class="secao"> Pedidos da ação</td>
          <td colspan="5"><textarea type="text" name="pedidos_acao" id="pedidos_acao" rows="6" cols="60"><?=$row_processo['pedido_acao'];?></textarea></td>
          </tr>
          
            <tr>
             	<td colspan="6" height="10px">&nbsp;</td>
             </tr>
          <!-------------------------MUDANÇA DE TIPO DE ANDAMENTO ------------->
       
	         <tr>
	          <td class="secao"> STATUS</td>
	          <td colspan="2">
              <?php 
			  $qr_andamento = mysql_query("SELECT * FROM proc_trab_andamento WHERE proc_id = '$row_processo[proc_id]' AND andamento_status = 1 ORDER BY  proc_status_id DESC");
			  $row_andamento = mysql_fetch_assoc($qr_andamento);
			  	
			  echo mysql_result(mysql_query("SELECT  proc_status_nome FROM processo_status WHERE proc_status_id = '$row_andamento[proc_status_id]'"),0);
			  echo '<input name="status_atual" type="hidden" value="'.$row_andamento['andamento_id'].'"/>';
			  ?>
              <input type="checkbox" name="mudar_status" value="1" id="mudar_status"/> Mudar status?
              
              
              </td>
              <td colspan="4">
              <select name="alteracao_status" id="alteracao_status" style="display:none;">
              <option value="">Selecione o novo status...</option>
              <?php
              $qr_status = mysql_query("SELECT  * FROM processo_status WHERE 1 ORDER BY ordem ASC") or die(mysql_error());
			  while($row_status = mysql_fetch_assoc($qr_status)):
			  	echo '<option value="'.$row_status['proc_status_id'].'">'.$row_status['proc_status_nome'].'</option>';
			  endwhile;
			  ?>
              </select>
              </td>
              </tr>
              
              
              
              <?php  
			  $status_encerrados = array(7,8,9,10,11);
			  ?>
	          </tr>
             <tr class="outros" style=" <?php if(!in_array($row_andamento['proc_status_id'],$status_encerrados)){ echo 'display:none;'; } ?> ">
	            <td class="secao">Valor da parcela:</td>
	            <td><input type="text" name="valor" id="valor" value="<?php   if($row_andamento['andamento_valor'] != '0') echo $row_andamento['andamento_valor'] ;?>"/></td>
	    
	             <td class="secao">Data de pagamento</td>
	             <td colspan="2"><input type="text" name="data_pg" id="data_pg" value="<?php  if($row_andamento['andamento_data_pg'] != '0000-00-00') echo implode('/',array_reverse(explode('-', $row_andamento['andamento_data_pg']))) ;?>"/></td>
                 </tr>
                 <tr class="outros" style=" <?php if(!in_array($row_andamento['proc_status_id'],$status_encerrados)){ echo 'display:none;'; } ?> ">
	       
	              <td class="secao">Número de parcelas</td>
	             <td colspan="4"><input type="text" name="n_parcelas" id="n_parcelas" size=5 value="<?php   if($row_andamento['andamento_parcelas'] != '0') echo $row_andamento['andamento_parcelas'] ;?>"/></td>
	        </tr>
         
           <!------------------------------------------------------------->          
		 
          <tr>
            <td colspan="8" height="10px">&nbsp;</td>
         </tr>
          
          <tr>
          <td class="secao" valign="top">N&ordm; do Processo: 	<a href="#" onclick="return(false)" id="add_n_processo"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></td>
             <td colspan="5">  
             <div id="campos_n_processo">
             <?php
             $qr_n_processo = mysql_query("SELECT * FROM n_processos WHERE proc_id = $id_processo");
			if(mysql_num_rows($qr_n_processo) == 0) {
				
			echo '<input name="n_processo[]" size="30" type="text" id="n_processo[]" class="validate[required]" class="numero_processo"/> <label>Ordem:</label><input name="ordem[]" type="text" size="2"/>';
				
			} else {
			
			 while($row_n_processos = mysql_fetch_assoc($qr_n_processo)):
			 
			 echo '<div>
			 			<input name="n_processo[]" size="30" type="text" id="n_processo[]" value="'.$row_n_processos['n_processo_numero'].'" class="numero_processo"/>
                         <label>Ordem:</label>  <input name="ordem[]" type="text" size="2" value="'.$row_n_processos['n_processo_ordem'].'"/>
						<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a>
				</div>';
								
			 endwhile;
			}
			 ?>
             
             </div>
             </td>
             </tr>
             
             <tr>
	        	 <td class="secao">Valor Pedido:</td>
	             <td  colspan="5"><input name="valor_pedido" size="10" type="text" id="valor_pedido" value="<?=$row_processo['proc_valor_pedido']?>" class="validate[required]" /></td>
	          </tr>
              
          
           <tr>
            <td class="secao">Vara:</td>
             <td  colspan="2"><input name="local" size="30" type="text" id="local" value="<?=$row_processo['proc_local']?>"/></td>
             <td class="secao">Nº da vara:</td>
             <td  colspan="2"> <input name="n_vara" type="text" value="<?php echo $row_processo['proc_numero_vara']; ?>"/></td>
          </tr>
          
            
          <!----------------ADVOGADO --------------------------------->
               <tr>
                 <td class="secao" >Advogado:
                 <br />
                 	<a href="#" onclick="return(false)" id="add_advogado"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                 </td>
                 <td colspan="2">
                
                 
                 <div id="campo_advogado">
                        <select name="advogado[]"  id="advogado" class="validate[required]" >
                       <option value="">Selecione uma opção..</option>
                       <option value=""></option>
                        <?php	
						$advogados = explode(',', $row_processo['adv_id']);
										
                        $qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");
                        while($row_advogado = mysql_fetch_assoc($qr_advogado)):
						
						if($row_advogado['adv_id'] == $advogados[0]) {$selected  = 'selected="selected"';  $adv_id = $row_advogado['adv_id'];} else {$selected  = '';}
						$estagiario = ($row_advogado['adv_estagiario'] == 1)? '(estagiário)':'';
						
                        ?>
                        
                       		 <option value="<?php echo $row_advogado['adv_id']?>" <?php echo $selected ?> > <?php echo $row_advogado['adv_nome'].' '.$estagiario?> </option>
                        
                        <?php
                        endwhile;
                        ?>
                        </select>
                           <a href="../gerar_carta_adv.php?id=<?php echo $adv_id;?>&processo=<?php echo $id_processo?>"> <img src="../../img_menu_principal/gerar_carta.png" title="GERAR PROCURAÇÃO" border="0" width="15" height="15"/></a> &nbsp;&nbsp;
                            
                            <a href="../gerar_subestabelecimento.php?id=<?php echo $adv_id;?>&processo=<?php echo $id_processo?>"> <img src="../../img_menu_principal/gerar_carta.png" title="GERAR CARTA DE SUBESBELECIMENTO" border="0" width="15" height="15"/></a> &nbsp;&nbsp;
                           <a href="#" onclick="$(this).parent().remove()" class="excluir" >  <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/> </a>
                 </div>
                  
                <div>
						  <?php  for($i=1;$i<count($advogados);$i++) {
                            ?>
                                <div>
                                <select name="advogado[]" id="advogado" class="validate[required]" >
                               <option value="">Selecione uma opção..</option>
                               <option value=""></option>
                                <?php	
                                $advogados = explode(',', $row_processo['adv_id']);
                                                
                                $qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");
                                while($row_advogado = mysql_fetch_assoc($qr_advogado)):
                                
                                if($row_advogado['adv_id'] == $advogados[$i]) {$selected = 'selected="selected"'; $adv_id = $row_advogado['adv_id']; } else {$selected = '';}
								$estagiario = ($row_advogado['adv_estagiario'] == 1)? '(estagiário)':'';
                                ?>
                                     <option value="<?php echo $row_advogado['adv_id']?>" <?php echo $selected;?>> <?php echo $row_advogado['adv_nome'].' '.$estagiario?> </option>
                                
                                <?php
                                endwhile;
                                ?>
                                </select>	
                                
                                <a href="../gerar_carta_adv.php?id=<?php echo $adv_id;?>&processo=<?php echo $id_processo?>"> <img src="../../img_menu_principal/gerar_carta.png" title="GERAR PROCURAÇÃO" border="0" width="15" height="15"/></a> &nbsp;&nbsp;
                                 
                                 <a href="../gerar_subestabelecimento.php?id=<?php echo $adv_id;?>&processo=<?php echo $id_processo?>"> <img src="../../img_menu_principal/gerar_carta.png" title="GERAR CARTA DE SUBESBELECIMENTO" border="0" width="15" height="15"/></a> &nbsp;&nbsp;
                                 
                                <a href="#" onclick="$(this).parent().remove()" class="excluir"> <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/></a>
                            </div>
                            <?php	
                                } 
                            ?>
                </div>
                </td>
                
                
                
                 <!----------------PREPOSTOS --------------------------------->                   
                 <td class="secao" >Preposto:<br />
                 	<a href="#" onclick="return(false)" id="add_preposto"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                 </td>
                 <td  colspan="2">
                
                 
                 <div id="campo_preposto">
                	<select name="preposto[]"id="preposto" class="validate[required]" >
                      <option value="">Selecione uma opção..</option>
                      <option value=""></option>
                    <?php
					$preposto = explode(',', $row_processo['preposto_id']);
					
					
					$qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_status = 1");
					while($row_preposto = mysql_fetch_assoc($qr_preposto)):
					
					
					if($row_preposto['prep_id'] == $preposto[0]) {$selected = 'selected="selected"'; $preposto_id = $row_preposto['prep_id']; } else {$selected = '';}
					?>
					<option value="<?php echo $row_preposto['prep_id']?>"  <?php echo $selected ;?>> <?php echo $row_preposto['prep_nome']?> </option>
                    
					<?php
					endwhile;
					?>
                    
                    </select>
                     <a href="../gerar_carta_prep.php?id=<?php echo $preposto_id;?>"> <img src="../../img_menu_principal/gerar_carta.png" title="GERAR PROCURAÇÃO" border="0" width="15" height="15"/></a> &nbsp;&nbsp;
                             <a href="#" onclick="$(this).parent().remove()" class="excluir">  <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/> </a>
                 </div>
                 <div>
                         
						 <?php
                         for($i=1;$i<count($preposto);$i++) {
                            ?>
                            <div>
                            <select name="preposto[]" id="preposto" class="validate[required]" >
                              <option value="">Selecione uma opção..</option>
                              <option value=""></option>
                            <?php
                            $preposto = explode(',', $row_processo['preposto_id']);
                            
                            
                            $qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_status = 1");
                            while($row_preposto = mysql_fetch_assoc($qr_preposto)):
                            
                            
                            if($row_preposto['prep_id'] == $preposto[$i]) {$selected = 'selected="selected"'; $preposto_id = $row_preposto['prep_id'];} else {$selected = '';}
                            ?>
                            <option value="<?php echo $row_preposto['prep_id']?>"  <?php echo $selected;?>> <?php echo $row_preposto['prep_nome']?> </option>
                            
                            <?php
                            endwhile;
                            ?>
                            
                            </select>
                             <a href="../gerar_carta_prep.php?id=<?php echo $preposto_id;?>"> <img src="../../img_menu_principal/gerar_carta.png" title="GERAR PROCURAÇÃO" border="0" width="15" height="15"/></a> &nbsp;&nbsp;
                             <a href="#" onclick="$(this).parent().remove()" class="excluir">  <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/> </a>
                            </div>
                            <?php	
                                } 
                            ?>
                           
                            
                 </div>                 
                </td>
                
              </tr>
          <tr>
          	<td  colspan="6" align="center" style="text-align:center;">
            <input name="id_processo" type="hidden" value="<?php echo $row_processo['proc_id']?>"/>
            <input name="enviar" type="submit" value="ATUALIZAR"/>
            </td>
          </tr>
          
    </table>
    </form>
    </td>
    </tr>

</table>






<div id="select_preposto" style="display:none;">
                 
                <select name="preposto[]"  >
                      <option value="">Selecione uma opção..</option>
                      <option value=""></option>
                    <?php
					$qr_preposto = mysql_query("SELECT * FROM prepostos WHERE prep_status = 1");
					while($row_preposto = mysql_fetch_assoc($qr_preposto)):
					?>
					<option value="<?php echo $row_preposto['prep_id']?>"  <?php echo $selected ;?>> <?php echo $row_preposto['prep_nome']?> </option> 
                    <?php
					endwhile;
					?>                    
                    </select>
                   
                 </div>

 <div id="select_advogado" style="display:none;">
                 <select name="advogado[]" id="advogado" class="validate[required]" >
                               <option value="">Selecione uma opção..</option>
                               <option value=""></option>
                                <?php	
                                $advogados = explode(',', $row_processo['adv_id']);
                                                
                                $qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");
                                while($row_advogado = mysql_fetch_assoc($qr_advogado)):
                                
                                if($row_advogado['adv_id'] == $advogados[$i]) {$selected = 'selected="selected"'; $adv_id = $row_advogado['adv_id']; } else {$selected = '';}
								$estagiario = ($row_advogado['adv_estagiario'] == 1)? '(estagiário)':'';
                                ?>
                                     <option value="<?php echo $row_advogado['adv_id']?>" <?php echo $selected;?>> <?php echo $row_advogado['adv_nome'].' '.$estagiario?> </option>
                                
                                <?php
                                endwhile;
                                ?>
                                </select>	
                                     
                 </div>
</div>
</body>
</html>
