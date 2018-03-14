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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

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
//		mail('fernanda.souza@sorrindo.org', 'Novo processo jurídico cadastrado.',$menssagem,$headers);
	

	header("Location: dados_processo/anexar_doc_andamentos.php?id_processo=$ultimo_id&id_andamento=$id_andamento&regiao=$regiao_id");		
			
	///////////////////////////////////////
	
	
	//header("Location:  dados_processo/ver_processo.php?id_processo=$ultimo_id&regiao=$regiao_id");
	
	}

}



$id_user   = $_COOKIE['logado'];

$regiao  = mysql_real_escape_string($_GET['reg']);
$id_trabalhador  = mysql_real_escape_string($_GET['trab']);
$id_projeto =  mysql_real_escape_string($_GET['projeto']);
$breadcrumb_config = array("nivel" => "../", "key_btn" => "24", "area" => "JURÍDICO", "ativo" => "Consultar Processos", "id_form" => "consulta_processo");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<!--<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css"/>-->
    
<link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all"/>
<link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all"/>
<link href="../../resources/css/main.css" rel="stylesheet" media="screen"/>
<link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen"/>
<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
<link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
<link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen"/>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    
<script type="text/javascript" src="../../js/ramon.js"></script>


<script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>

<script src="../../js/jquery.maskedinput-1.3.1.js"></script>

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

			var campo = '<label for="select" class="col-sm-1 control-label hidden-print">Nº do Processo:</label><div class="col-sm-5"><input name="n_processo[]" size="30" type="text" id="n_processo" " class="numero_processo form-control"/></div> <label for="select" class="col-sm-1 control-label hidden-print">Ordem:</label><div class="col-sm-5"><input name="ordem[]" class="form-control" type="text" size="2" /></div>';	
			
		$('#campos_n_processo').append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');
		$('.numero_processo').mask('9999999-99.9999.9.99.9999');
		
		
	});
	
	$('#add-nome').click(function(){
            var campo = '<input name="nome[]" class="form-control" size="50" type="text" id="nome2"/>';
            $('#campos-nome').append('<div>'+campo+'<a href="#" onclick="$(this).parent().remove()" class="excluir"> Excluir </a></div>');
        });
	

	
});

</script>


<title>::Intranet:: Cadastro de Processos CLT</title>
</head>
<body>
    <?php include("../../template/navbar_default.php"); ?>
<div class="container">
<div class="page-header box-juridico-header"><h2><span class="glyphicon glyphicon-briefcase"></span> - GESTÃO JURÍDICA <small> - CADASTRAR PROCESSO <?php echo $row_tipo_proc['proc_tipo_nome']?></small></h2></div>

<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" name="form1" class="form-horizontal top-margin1"
    id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
    <div class="panel panel-default">
    <div class="panel-heading text-bold hidden-print">Cadastrar</div>
    <div class="panel-body">
    

      <?php if(!empty($erros)) {
		  		$erros = implode('<br>', $erros);
				echo '<p style="background-color:#C30; padding:4px; color:#FFF;">'.$erros.'</p><p>&nbsp;</p>';
			} ?>
      
	
            <div class="panel panel-default"></div>

    <table cellpadding="0" cellspacing="1" class="secao">
        <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >PARTES:</label>
        </div>
        <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >Nomes <br/><a href="#" onclick="return(false)" id="add-nome"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></label>
            <div class="col-sm-5">
                <!--<input name="n_documento" type="text" class="form-control"/>-->
                <input name="nome[]" size="50" type="text" class="form-control" id="nome0"/><br/>
                 <input name="nome[]" size="50" type="text" class="form-control" id="nome1"/><br/>
                 <input name="nome[]" size="50" type="text" class="form-control"  id="nome2"/><br/>
                 <div id="campos-nome"></div>
            </div>
<!--          <div class="col-sm-5">
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
          </div>-->
        </div>
        <div class="form-group" >
            <label for="select" class="col-sm-2 control-label hidden-print" >DADOS DO PROCESSO</label>
        </div>
        <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >Região</label>
            
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
            
                    <div class="col-sm-5">
                        <select name="regiao" id="tipo" class="form-control">
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
                    </div>
            </div>
          
          <?php if($row_tipo_proc['proc_tipo_id'] == 5) {	?>
	         <tr>
	            <td class="secao" style="border-top:1px solid #777;">Nº do processo:</td>
	            <td colspan="3"><input type="text" name="n_oficio"/></td>
	          </tr>
          <?php } ?>


            <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >Advogado principal</label>
                       
                    <div class="col-sm-5">
                        <select name="adv_principal" id="tipo" class="form-control">
                            <option value="">Selecione uma opção..</option>
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
	   ?>
                        </select>
                    </div>
            </div>
   
          
            <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >Pedidos da ação</label>
                       
                    <div class="col-sm-5">
                        <textarea type="text" class="form-control" name="pedidos_acao" id="pedidos_acao" rows="6" cols="40"></textarea>
                    </div>
            </div>

            <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >Data de cadastro</label>
                       
                    <div class="col-sm-2">
                        <input type="text" name="data_andamento" class="form-control" id="data_andamento"/>
                    </div>
            </div>
        
          
                        
           <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >N&ordm; do Processo: <a href="#" onclick="return(false)" id="add_n_processo"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></label>
                    <div class="col-sm-5">
                       <input name="n_processo[]" size="30" type="text" id="n_processo" class="numero_processo form-control" />
                    </div>
            <label for="select" class="col-sm-1 control-label hidden-print" >Ordem:</label>
                    <div class="col-sm-5">
                        <input name="ordem[]" class="form-control" type="text" size="2"/>
                    </div>
            <div id="campos_n_processo">
                <br/>  
                <br/>  
                <br/>  
                <br/>  
           
            </div>
            </div>   

        
         <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >Valor Pedido</label>
                    <div class="col-sm-2">
                        <input name="valor_pedido" size="10" type="text" id="valor_pedido" class="form-control" />
                    </div>
            <label for="select" class="col-sm-1 control-label hidden-print" >Vara</label>
                    <div class="col-sm-3">
                        <input name="local" size="30" type="text" id="local" class="validate[required] form-control"/>
                    </div>
            <label for="select" class="col-sm-2 control-label hidden-print" >Nº da Vara</label>
                    <div class="col-sm-3">
                        <input name="n_vara" type="text" class="form-control"/>
                    </div>
            </div>
         
        
        <div class="form-group" >
            <label for="select" class="col-sm-1 control-label hidden-print" >UF da VARA</label>
                       
                    <div class="col-sm-1">
                        <select name="uf_vara" id="tipo" class="form-control">
                            <option value="">UF</option>
                 <?php
                $qr_uf = mysql_query("SELECT * FROM uf");
				while($row_uf = mysql_fetch_assoc($qr_uf)):
				
					echo '<option value="'.$row_uf['uf_id'].'" >'.$row_uf['uf_sigla'].' </option>';
				
				endwhile;
				
				?> 
                        </select>
                    </div>
            </div>
        <div class="form-group">
            
               <label for="select" class="col-sm-1 control-label hidden-print" >Advogado<br />
                 	<a href="#" onclick="return(false)" id="add_advogado"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></label>
                       
                    <div class="col-sm-5" id="campo_advogado">
                        <select name="advogado[]" id="tipo" class="form-control">
                            <option value="">Selecione uma opção..</option>
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
            
            <div class="col-sm-5"></div>
        </div>
        <div class="form-group">
                    <label for="select" class="col-sm-1 control-label hidden-print" >Preposto<br/>
                        <a href="#" onclick="return(false)" id="add_preposto"><img src="../../imagens/add.png" width="18" height="18" title="Adicionar"/></a></label>
                       
                    <div class="col-sm-5" id="campo_preposto">
                        <select name="preposto[]" id="tipo" class="form-control">
                            <option value="">Selecione uma opção..</option>
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
            
        <div class="col-sm-5"></div>
        </div>
        
        
          <tr>
          	<td  colspan="4" align="center" style="text-align:center;">
             <input name="tipo_processo" type="hidden" value="<?php echo $tipo_processo;?>"/>
            <input name="id_autonomo" type="hidden" value="<?php echo $id_autonomo;?>"/>
            
            </td>
          </tr>
          
    </table>
            </div>
    <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="enviar" id="enviar" value="CADASTRAR" class="btn btn-primary"><span class="fa fa-filter"></span> Cadastrar</button>
                    
                            <div style="text-align:left">
                    <?php include('../../template/footer.php'); ?>
                    <div class="clear"></div></div>      
                    </div>
    </form>
    </td>
    </tr>

</div>
</div>
</body>
</html>