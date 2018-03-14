<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes_permissoes/regioes.class.php');

$REGIOES = new Regioes();



$parceiro = $_GET['id'];

$qr_parceiro  = mysql_query("SELECT * FROM parceiros WHERE parceiro_id = '$parceiro'");
$row_parceiro = mysql_fetch_assoc($qr_parceiro);

if($_POST['pronto'] == 'edicao') {
	
	$update_logo = (!empty($_POST['nome_logo'])) ? ", parceiro_logo = '$_POST[nome_logo]'" : "";

	$nulos = array('Nome' => $_POST['nome']);
	foreach($nulos as $campo => $valor) {
		if(empty($valor)) {
		    header("Location: cadastro.php?nulo=$campo&m=$_POST[link_master]");
		    exit;
		}
	}
	

$qr_insert = mysql_query("UPDATE parceiros SET 
						id_regiao 			= '$_POST[regiao]', 
						parceiro_nome 		= '$_POST[nome]'
						$update_logo,
						parceiro_endereco 	= '$_POST[endereco]', 
						parceiro_cnpj 		= '$_POST[cnpj]', 
						parceiro_ccm 		= '$_POST[ccm]', 
						parceiro_ie 		= '$_POST[ie]', 
						parceiro_im 		= '$_POST[im]', 
						parceiro_bairro 	= '$_POST[bairro]', 
						parceiro_cidade 	= '$_POST[cidade]', 
						parceiro_estado 	= '$_POST[estado]', 
						parceiro_telefone 	= '$_POST[telefone]', 
						parceiro_celular 	= '$_POST[celular]', 
						parceiro_email 		= '$_POST[email]', 
						parceiro_contato 	= '$_POST[contato]', 
						parceiro_cpf 		= '$_POST[cpf]', 
						parceiro_banco 		= '$_POST[banco]', 
						parceiro_agencia 	= '$_POST[agencia]', 
						parceiro_conta 		= '$_POST[conta]',
						parceiro_atualizacao = NOW(),
						parceiro_id_atualizacao = '$_COOKIE[logado]'
							
						WHERE parceiro_id = '$_POST[parceiro]' 
						LIMIT 1") or die(mysql_error());
	
	if($qr_insert){
	
		$nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"),0);	
		registrar_log('ADMINISTRAÇÃO - EDIÇÃO DE PARCEIROS', $nome_funcionario.' editou o parceiro: '.'('.$_POST['parceiro'].') - '.$_POST['nome']);	

		header("Location: index.php?sucesso=curso&m=$_POST[link_master]&curso=$id");
	}
}
?>
<html>
<head>
<title>Edi&ccedil;&atilde;o de Parceiro</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../js/ramon.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript">
function validaForm() {
	d = document.cadastro;
	if (d.nome.value == '') {
		alert('O campo Nome deve ser preenchido!');
		d.nome.focus();
		return false;
	}
	return true;   
}
</script>

<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.min.js" ></script>
<script type="text/javascript" src="../../uploadfy/scripts/swfobject.js" ></script>
<link href="../../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css">


<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
<script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
<link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="../../js/highslide-with-html.js"></script> 
<link rel="stylesheet" type="text/css" href="../../js/highslide.css" /> 

<script type="text/javascript" src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" ></script>

<script type="text/javascript" >

    hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';


$(function(){
	<?php 
		if($row_parceiro['parceiro_logo'] == ""){
			echo "$('#logomarca').hide(); $('#remove_logo').hide();";	
		}
	?>
	
	
		$("#logo").uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : 'upload.php',
				'buttonText'     : 'Alterar Logo',
				'queueID'        : 'barra_processo',
				'cancelImg'      : '../../uploadfy/cancel.png',
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : false,
				'fileDesc'       : 'gif jpg pdf',
				'fileExt'        : '*.gif;*.jpg;*.pdf;',
				'onComplete'  : function(event, ID, fileObj, response, data) {
					eval('var resposta = '+ response);
					$('#visualiza_img').html('<img src="'+resposta.img+'" width="300" height="300"  id="logomarca"/>');
					$('#nome_logo').val(resposta.nome_arquivo);
					$('#remove_logo').show();
					
					$('#logomarca').show(); $('#remove_logo').show();
					
				}
			});
	$('#remove_logo').click(function(){
			$.ajax({
				url : 'removeLogo.php',
				data : { 'caminho' : $('#logomarca').attr('src'), 'id' : $('#remove_logo').attr('rel')},
				success : function(){
					$('#visualiza_img').html('');
					$('#remove_logo').hide();
				}
			});
			
			
	});


					$("#cnpj").mask('99.999.999/9999-99');
			$("#telefone").mask('(99) 9999-9999');
			$("#celular").mask('(99) 9999-9999');
			$("#cpf").mask('999.999.999-99');
			
			$("#cadastro").validationEngine();
	
	
});
</script>


</head>
<body>
<div id="corpo">
    <div id="menu" class="parceiro">
        <?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo">
        <h1><span>Edi&ccedil;&atilde;o de Parceiro Comercial</span></h1>
        <?php if($_GET['nulo']) { echo 'O campo <b>'.$_GET['nulo'].'</b> não pode ficar em branco!'; } ?>
        <form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validaForm()">          
        <table cellspacing="0" cellpadding="4" class="relacao">
          <tr>
            <td class="secao">Nome:</td>
            <td colspan="5" align="left"><input type="text" id="nome" name="nome" size="50" value="<?php echo $row_parceiro['parceiro_nome']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Regiao:</td>
            <td colspan="5" align="left">
            	<select name="regiao" id="regiao" >
                <?php
					$REGIOES->Preenhe_select_por_master($Master,$row_parceiro['id_regiao']);
				?>                      
                </select>
            </td>
          </tr>
          <tr>
            <td class="secao">Endere&ccedil;o:</td>
            <td colspan="5" align="left"><input type="text" id="endereco" name="endereco" size="90" value="<?php echo $row_parceiro['parceiro_endereco']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">CNPJ:</td>
            <td align="left"><span class="descricao">
              <input type="text" id="cnpj" name="cnpj" size="25" value="<?php echo $row_parceiro['parceiro_cnpj']; ?>">
            </span></td>
            <td class="secao">CCM</td>
            <td class="secao" align="left"><span class="descricao">
              <input type="text" id="ccm" name="ccm" size="25" value="<?php echo $row_parceiro['parceiro_ccm']; ?>">
            </span></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="secao">I.E.:</td>
            <td align="left"><span class="descricao">
              <input type="text" id="ie" name="ie" size="25" value="<?php echo $row_parceiro['parceiro_ie']; ?>">
            </span></td>
            <td class="secao">&nbsp;</td>
            <td class="secao">&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="secao">Bairro:</td>
            <td align="left"><input type="text" id="bairro" name="bairro" size="40" value="<?php echo $row_parceiro['parceiro_bairro']; ?>"></td>
            <td class="secao">Cidade:</td>
            <td align="left"><input type="text" id="cidade" name="cidade" size="30" value="<?php echo $row_parceiro['parceiro_cidade']; ?>"></td>
            <td class="secao">Estado:</td>
            <td align="left">
            <select name="estado" id="estado"   class="validate[required]" > 
<option value="<?php echo $row_parceiro['parceiro_estado']; ?>"><?php echo $row_parceiro['parceiro_estado']; ?></option>   
<option value=""></option>
          
<option value="AC">AC</option>  
<option value="AL">AL</option>  
<option value="AM">AM</option>  
<option value="AP">AP</option>  
<option value="BA">BA</option>  
<option value="CE">CE</option>  
<option value="DF">DF</option>  
<option value="ES">ES</option>  
<option value="GO">GO</option>  
<option value="MA">MA</option>  
<option value="MG">MG</option>  
<option value="MS">MS</option>  
<option value="MT">MT</option>  
<option value="PA">PA</option>  
<option value="PB">PB</option>  
<option value="PE">PE</option>  
<option value="PI">PI</option>  
<option value="PR">PR</option>  
<option value="RJ">RJ</option>  
<option value="RN">RN</option>  
<option value="RO">RO</option>  
<option value="RR">RR</option>  
<option value="RS">RS</option>  
<option value="SC">SC</option>  
<option value="SE">SE</option>  
<option value="SP">SP</option>  
<option value="TO">TO</option>  
</select></td>
          </tr>
          <tr>
            <td class="secao">Telefone:</td>
            <td class="descricao" align="left"><input type="text" id="telefone" name="telefone" size="25" value="<?php echo $row_parceiro['parceiro_telefone']; ?>"></td>
            <td class="secao">Celular:</td>
            <td colspan="3" class="descricao" align="left"><input type="text" id="celular" name="celular" size="25" value="<?php echo $row_parceiro['parceiro_celular']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Email:</td>
            <td colspan="5" class="descricao" align="left"><input type="text" id="email" name="email" size="40" value="<?php echo $row_parceiro['parceiro_email']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Contato:</td>
            <td class="descricao" align="left"><input type="text" id="contato" name="contato" size="40" value="<?php echo $row_parceiro['parceiro_contato']; ?>"></td>
            <td class="secao">CPF:</td>
            <td class="descricao" align="left"><input type="text" id="cpf" name="cpf" size="25" value="<?php echo $row_parceiro['parceiro_cpf']; ?>"></td>
            <td class="secao">&nbsp;</td>
            <td class="descricao">&nbsp;</td>
          </tr>
          <tr>
            <td class="secao">Banco:</td>
            <td class="descricao" align="left"><input type="text" id="banco" name="banco" size="30" value="<?php echo $row_parceiro['parceiro_banco']; ?>"></td>
            <td class="secao">Ag&ecirc;ncia:</td>
            <td class="descricao" align="left"><input type="text" id="agencia" name="agencia" size="10" value="<?php echo $row_parceiro['parceiro_agencia']; ?>"></td>
            <td class="secao">Conta:</td>
            <td class="descricao" align="left"><input type="text" id="conta" name="conta" size="17" value="<?php echo $row_parceiro['parceiro_conta']; ?>"></td>
          </tr>
          <tr>
            <td colspan="6" align="center">
            	<div id="content_logo"><input type="file" name="logo" id="logo" /></div>
            <div id="barra_processo"></div>
            <a href="#" onClick="return false" id="remove_logo" rel="<?php echo $row_parceiro['parceiro_id'];?>">remover logo</a>
            	<div id="visualiza_img">
                	
                	<img src="<?php echo 'logo/'.$row_parceiro['parceiro_logo']; ?>" width="300" height="300" id="logomarca" />
                </div>
            </td>
          </tr>
          <tr>
            <td colspan="6" align="center">
             <input type="hidden" name="nome_logo" id="nome_logo" value="" />
             <input name="master" value="<?php echo $Master; ?>" type="hidden" />
             <input name="parceiro" value="<?php echo $parceiro; ?>" type="hidden" />
             <input name="link_master" value="<?php echo $_GET['m']; ?>" type="hidden" />
             <input name="pronto" value="edicao" type="hidden" />
             <input value="Atualizar" type="submit" class="botao" style="float:right;" />
            </td>
          </tr>
        </table>
        </form>
    </div>
    <div id="rodape"><?php include('include/rodape.php'); ?></div>
</div>
</body>
</html>