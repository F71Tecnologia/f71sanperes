<?php
include ("../include/restricoes.php");
include('../../conn.php');
include('../../funcoes.php');
//include "../funcoes.php";
include "../include/criptografia.php";
include "../../classes_permissoes/regioes.class.php";
include '../../wfunction.php';

$REGIOES = new Regioes();
$usuario = carregaUsuario();

function formato_brasileiro($data) {

if($data != '0000-00-00') {	
	echo implode('/',array_reverse(explode('-',$data)));	
}
	
}

$tipo_processo = mysql_real_escape_string($_GET['tipo_proc']); 

$qr_tipo_processo = mysql_query("SELECT * FROM processo_tipo WHERE proc_tipo_id = '$tipo_processo'");
$row_tipo_proc = mysql_fetch_assoc($qr_tipo_processo);

	
if(isset($_POST['enviar'])){

$array_n_processo   =  $_POST['n_processo'];
$array_ordem	    =   $_POST['ordem'];
	
foreach ($_REQUEST['nome'] as $value) {
    $nome[] = mysql_real_escape_string($value);
}

//$nome 		    	= mysql_real_escape_string($_POST['nome']);	
//$nome1 		    	= mysql_real_escape_string($_POST['nome1']);	
//$nome2		    	= mysql_real_escape_string($_POST['nome2']);	
$data_nasci     	= implode('-',array_reverse(explode('/',mysql_real_escape_string($_POST['data_nasci']))));	
$rg 		    	= mysql_real_escape_string($_POST['rg']);
$cpf 		    	= mysql_real_escape_string($_POST['cpf']);		
$atividade_nome 	= mysql_real_escape_string($_POST['atividade_nome']);	
$data_entrada   	= implode('-',array_reverse(explode('/',$_POST['data_entrada'])));
$data_saida     	= implode('-',array_reverse(explode('/',$_POST['data_saida'])));
$regiao_id     	 	= mysql_real_escape_string($_POST['regiao']);

$unidade 			= mysql_real_escape_string($_POST['unidade']);
$valor_pedido 		= str_replace(',','.',str_replace('.','',mysql_real_escape_string($_POST['valor_pedido'])));
$local 				= mysql_real_escape_string($_POST['local']);
$numero_vara 		= mysql_real_escape_string($_POST['n_vara']);
$uf_vara		    = mysql_real_escape_string($_POST['uf_vara']);
$adv_id 			= implode(',', $_POST['advogado']);
$prep_id 			= implode(',',$_POST['preposto']);
$id_autonomo 		= mysql_real_escape_string($_POST['id_autonomo']);
$tipo_contratacao 	= mysql_real_escape_string($_POST['tipo_contratacao']);	
$adv_principal      = $_POST['adv_principal'];
$tipo_processo 		= mysql_real_escape_string($_POST['tipo_processo']); 
$pedido_acao 		= $_POST['pedidos_acao'];
$n_oficio  			= $_POST['n_oficio'];


$insert = mysql_query("INSERT INTO processos_juridicos
							(adv_id,
							preposto_id,
							proc_tipo_id,
							id_regiao,							
							proc_cpf, 
							proc_rg, 
							proc_data_nasc, 
							proc_atividade, 														
							proc_numero_processo, 
							proc_valor_pedido, 
							proc_local,	
							proc_numero_vara,
							adv_id_principal,
							n_oficio,
							pedido_acao,
							uf_id,					
							proc_data_cad,
							status)
							
							 VALUES 
							 
							 ('$adv_id',
							  '$prep_id',
							  '$tipo_processo',		
							  '$regiao_id',					  
							  '$cpf',
							  '$rg',
							  '$data_nasci',
							  '$atividade_nome',							  						
							  '$n_processo',
							  '$valor_pedido',
							  '$local',							  
							  '$numero_vara',
							  '$adv_principal',
							  '$n_oficio',
							  '$pedido_acao',
							  '$uf_vara',							
							  NOW(),
							  1							  
							 )") or die(mysql_error());

	if($insert) {
		
	$ultimo_id = mysql_insert_id();	
        
        
        // INSERINDO NOME DAS PARTES
        if(count($nome) >= 1){
            foreach ($nome as $value) {
                echo "INSERT INTO processos_juridicos_nomes (proc_id,nome) VALUES ($ultimo_id,$value)";
                mysql_query("INSERT INTO processos_juridicos_nomes (proc_id,nome) VALUES ('$ultimo_id','$value')") or die(mysql_error());
            }
        }
        
        
	
	//INSERIRNDO DOS NÚMEROS DOS PROCESSSOS
	
	
		if(count($array_n_processo) == 1) {
		
		$valor = $array_n_processo[0];
		$ordem = $array_ordem[0];
		
		mysql_query("INSERT INTO n_processos (n_processo_numero, n_processo_ordem, proc_id, status)
													  VALUES
													  ('$valor','$ordem', '$ultimo_id', 1)") or die(mysql_error())	;
		
			
		} else {
		
				foreach($array_n_processo as $chave => $valor){
					
				$ordem = $array_ordem[$chave];	
				mysql_query("INSERT INTO n_processos (n_processo_numero, n_processo_ordem, proc_id, status)
													  VALUES
													  ('$valor','$ordem', '$ultimo_id', 1)") or die(mysql_error())	;
				
					
				}
	}
	
	
	//ADIciona o andamento PROCESSO  CADASTRADO		
		$data_movi = implode(array_reverse(explode('/',$_POST['data_andamento'])));
		
		$qr_insert = mysql_query("INSERT INTO proc_trab_andamento (proc_id, proc_status_id, andamento_data_movi,  andamento_data_cad, andamento_usuario_cad, andamento_status)
														VALUES     ('$ultimo_id', '1', '$data_movi', NOW(), '$_COOKIE[logado]',1) ") or die(mysql_error());
														
	$id_andamento = mysql_insert_id();	
	
	
	

		$nome_tipo = mysql_result(mysql_query("SELECT proc_tipo_nome FROM processo_tipo WHERE proc_tipo_id = '$tipo_processo'"),0);
		$nome_funcionario = mysql_result(mysql_query("SELECT nome1 FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"),0);
                
//		$nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao_id'"),0);
		
                $nome_regiao = mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao_id'"),0);
                
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$menssagem = 'Foi cadastrado um novo processo do tipo: '.$nome_tipo.' de número(s): '.implode(',',$array_n_processo).', no dia '.date('d/m/Y').', na região '.$nome_regiao.'<br> <br>
		Autor(a) do cadastro: '.$nome_funcionario ;	
		mail('fernanda.souza@sorrindo.org', 'Novo processo jurídico cadastrado.',$menssagem,$headers);
	

	header("Location: dados_processo/anexar_doc_andamentos.php?id_processo=$ultimo_id&id_andamento=$id_andamento&regiao=$regiao_id");		
			
	///////////////////////////////////////
	
	
	//header("Location:  dados_processo/ver_processo.php?id_processo=$ultimo_id&regiao=$regiao_id");
	
	}

}



$id_user   = $_COOKIE['logado'];

$regiao  = mysql_real_escape_string($_GET['reg']);
$id_trabalhador  = mysql_real_escape_string($_GET['trab']);
$id_projeto =  mysql_real_escape_string($_GET['projeto']);

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
$('#data_nasci').mask('99/99/9999');
$('#data_andamento').mask('99/99/9999');
$('.numero_processo').mask('9999999-99.9999.9.99.9999');
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
		
		$('#campo_preposto').next().append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir">  <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/> </a></div>');
		
		
	});
	
	
	$('#add_advogado').click(function(){
	
		var campo = $('#campo_advogado').html();
		
		$('#campo_advogado').next().append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir">  <img src="../../imagens/excluir.png" title="EXCLUIR" border="0" width="15" height="15"/> </a></div>');
		
		
	});
	
	$('#add_n_processo').click(function(){

			var campo = '<input name="n_processo[]" size="30" type="text" id="n_processo" " class="numero_processo"/> <label>Ordem:</label><input name="ordem[]" type="text" size="2" />';	
			
		$('#campos_n_processo').append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');
		$('.numero_processo').mask('9999999-99.9999.9.99.9999');
		
		
	});
	
	$('#add-nome').click(function(){
            var campo = '<input name="nome[]" size="50" type="text" id="nome2"/>';
            $('#campos-nome').append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');
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
               CADASTRAR PROCESSO <?php echo $row_tipo_proc['proc_tipo_nome']?>:<span class="projeto"> <?php 
				 ?>
            </span>
           </h2>
           
            
           <p style="float:right;margin-top:40px;">
               <a href="../index.php?regiao=<?=$regiao?>&tp=<?php echo $row_tipo['tipo_contratacao_id'];?>">&laquo; Voltar</a>
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
            <td colspan="4" class="secao_pai" style="border-top:1px solid #777;">PARTES</td>
          </tr>
          <tr>
             <td class="secao">
                 Nomes:
                 <a href="#" onclick="return(false)" id="add-nome"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
             </td>
             <td colspan="3">
                 <input name="nome[]" size="50" type="text" id="nome0"/><br/>
                 <input name="nome[]" size="50" type="text" id="nome1"/><br/>
                 <input name="nome[]" size="50" type="text" id="nome2"/><br/>
                 <div id="campos-nome"></div>
             </td>
          </tr>
          
        
         <tr>
            <td colspan="4" class="secao_pai" style="border-top:1px solid #777;">DADOS DO PROCESSO</td>
          </tr>
          
          <?php if($row_tipo_proc['proc_tipo_id'] == 5) {	?>
	         <tr>
	            <td class="secao" style="border-top:1px solid #777;">Nº do processo:</td>
	            <td colspan="3"><input type="text" name="n_oficio"/></td>
	          </tr>
          <?php } ?>
          
          <tr>
          	<td class="secao">Região:</td>
            <td colspan="3">
                <?php
                    $list = mysql_query("select id_master from funcionario where id_funcionario = $id_user");
                    $row = mysql_fetch_assoc($list);
                    
                    $sql = "SELECT * FROM regioes WHERE id_master = {$row['id_master']} order by regiao";
                    
                    $ativos = array();
                    $inativos = array();
                    $regioes = mysql_query($sql);                  
                    while($row_regiao = mysql_fetch_assoc($regioes)){
                        if($row_regiao['status']==0 && $row_regiao['status_reg']==0){
                            $inativos[] = $row_regiao;
                        }else{
                            $ativos[] = $row_regiao;
                        }
                    }
                ?>
            <select name="regiao">
                
            <?php
//                      $obj_regiao = new Regioes();
//                      $obj_regiao->Preenhe_select_sem_master();
            
                        echo '<optgroup label="REGIÕES ATIVAS">';
                            foreach($ativos as $ativo){
                                echo '<option value="'.$ativo['id_regiao'].'">'.$ativo['id_regiao'].' - '.$ativo['regiao'].'</option>';
                            }
                            echo '</optgroup>';
                            echo '<optgroup label="REGIÕES INATIVAS">';
                            foreach($inativos as $inativo){
                                echo '<option value="'.$inativo['id_regiao'].'">'.$inativo['id_regiao'].' - '.$inativo['regiao'].'</option>';
                            }
                        echo '</optgroup>';
                        
	   ?>
            </select>
            </td>
          </tr>
      
          
            <tr>
            <td class="secao">Advogado principal:</td>
             <td colspan="3">
              <select name="adv_principal" id="adv_principal">
               <option value="">Selecione uma opção..</option>
               <option value=""></option>
                <?php
                $qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");
                while($row_advogado = mysql_fetch_assoc($qr_advogado)):
                
				$estagiario = ($row_advogado['adv_estagiario'] == 1)? '(estagiário)':'';
				$selected = ($row_advogado['adv_id'] == $row_processo['adv_id_principal'])? 'selected="selected"':'';
                ?>
                <option value="<?php echo $row_advogado['adv_id']?>" <?php echo $selected;?>> <?php echo $row_advogado['adv_nome'].' '.$estagiario?> </option>                
                <?php
                endwhile;
                ?>
            </select>
             </td>
          </tr>    
          
          <tr>
          	<td class="secao"> Pedidos da ação</td>
            <td colspan="3"><textarea type="text" name="pedidos_acao" id="pedidos_acao" rows="6" cols="40"></textarea></td>
          </tr>
          
          <tr>
          	<td class="secao"> Data de cadastro:</td>
            <td colspan="3"><input type="text" name="data_andamento" id="data_andamento"/></td>
          </tr>
          
          <tr>
            <td class="secao">N&ordm; do Processo: 	<a href="#" onclick="return(false)" id="add_n_processo"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></td>
             <td>  <input name="n_processo[]" size="30" type="text" id="n_processo" class="numero_processo" />
             		<label>Ordem:</label><input name="ordem[]" type="text" size="2"/>
             <div id="campos_n_processo">
             
             
             </div>
             </td>
             
             <td class="secao">Valor Pedido:</td>
             <td  colspan="1"><input name="valor_pedido" size="10" type="text" id="valor_pedido" /></td>
          </tr>
              
              
           <tr>
            <td class="secao">Vara:</td>
             <td  ><input name="local" size="30" type="text" id="local" class="validate[required]"/></td>
             <td>Nº da vara:</td>
             <td><input name="n_vara" type="text"/></td>
             
          </tr>
          <tr>
          	<td class="secao">UF da VARA:</td>
            <td colspan="3">
             <select name="uf_vara">
            	<?php
                $qr_uf = mysql_query("SELECT * FROM uf");
				while($row_uf = mysql_fetch_assoc($qr_uf)):
				
					echo '<option value="'.$row_uf['uf_id'].'" >'.$row_uf['uf_sigla'].' </option>';
				
				endwhile;
				
				?>               
              </select> 
                            </td>
          </tr>
          
             <tr>
                 <td class="secao" >Advogado:
                 <br />
                 	<a href="#" onclick="return(false)" id="add_advogado"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a>
                 </td>
                 <td>
                 <div id="campo_advogado">
                        <select name="advogado[]">
                        <option value="">Selecione uma opção..</option>
                        <option value=""></option>
                        <?php
                        $qr_advogado = mysql_query("SELECT * FROM advogados WHERE adv_status = 1");
                        while($row_advogado = mysql_fetch_assoc($qr_advogado)):
						$estagiario = ($row_advogado['adv_estagiario'] == 1)? '(estagiário)':'';
                        ?>
                        <option value="<?php echo $row_advogado['adv_id']?>"> <?php echo $row_advogado['adv_nome'].' '.$estagiario?> </option>
                        
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
                     <option value="">Selecione uma opção..</option>
                        <option value=""></option>
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
          	<td  colspan="4" align="center" style="text-align:center;">
             <input name="tipo_processo" type="hidden" value="<?php echo $tipo_processo;?>"/>
            <input name="id_autonomo" type="hidden" value="<?php echo $id_autonomo;?>"/>
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