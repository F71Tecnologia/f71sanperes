<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes_permissoes/regioes.class.php');

$REGIOES = new Regioes();



if($_POST['pronto'] == 'cadastro') {

	$nulos = array('Nome' => $_POST['nome']);
	foreach($nulos as $campo => $valor) {
		if(empty($valor)) {
		    header("Location: cadastro.php?nulo=$campo&m=$_POST[link_master]");
		    exit;
		}
	}
	
  $id = mysql_result(mysql_query("SELECT MAX(aula_id) FROM aulas"),0)+1;

$nome = $_POST['nome'];
$nome_logo=$_POST['nome_logo'];
$regiao=$_POST['regiao'];
$endereco=mysql_real_escape_string($_POST['endereco']);
$cnpj=$_POST['cnpj'];
$ccm=$_POST['ccm'];
$ie=$_POST['ie'];
$im=$_POST['im'];
$contato=$_POST['contato'];
$cpf=$_POST['cpf'];
$cidade=$_POST['cidade'];
$estado=$_POST['estado'];
$bairro=$_POST['bairro'];
$telefone=$_POST['telefone'];
$celular=$_POST['celular'];
$email=$_POST['email'];
$banco=$_POST['banco'];
$agencia=$_POST['agencia'];
$conta=$_POST['conta'];
$logado=$_COOKIE['logado'];
	
$qr_insert =mysql_query("INSERT INTO parceiros (
parceiro_nome, 
parceiro_logo,
id_regiao, 
parceiro_endereco, 
parceiro_cnpj, 	
parceiro_ccm, 	
parceiro_ie, 	
parceiro_im, 
parceiro_contato, 
parceiro_cpf, 
parceiro_cidade, 
parceiro_estado, 
parceiro_bairro, 
parceiro_telefone, 
parceiro_celular, 
parceiro_email, 
parceiro_banco, 
parceiro_agencia, 
parceiro_conta, 
parceiro_autor, 
parceiro_data) 

VALUES ('$nome', 
'$nome_logo',
' $regiao', 
' $endereco',
'$cnpj',
' $ccm',
'$_POST[ie]',
'$im', 
'$contato',
'$cpf', 
'$cidade', 
'$estado', 
'$bairro',
'$telefone', 
'$celular', 
'$email', 
'$banco', 
'$agencia', 
'$conta', 
'$logado', 
NOW())") or die(mysql_error());

$ultimo_id = mysql_insert_id();
if($qr_insert){
	
	$nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"),0);
	
	registrar_log('ADMINISTRAÇÃO - CADASTRO DE PARCEIROS', $nome_funcionario.' cadastrou o parceiro: '.'('.$ultimo_id.') - '.$nome);	
	header("Location: index.php?sucesso=curso&m=$_POST[link_master]&curso=$id");
}
}
?>
<html>
<head>
<title>Cadastro de Parceiro</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../js/ramon.js" type="text/javascript"></script>





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
	
	
	
		$("#logo").uploadify({
				'uploader'       : '../../uploadfy/scripts/uploadify.swf',
				'script'         : 'upload.php',
				'buttonText'     : 'Enviar Logo',
				'queueID'        : 'barra_processo',
				'cancelImg'      : '../../uploadfy/cancel.png',
				'width' :  200,
				'height' :40,
				'auto'           : true,
				'method'         : 'post',
 				'multi'          : false,
				'fileDesc'       : 'gif jpg pdf',
				'fileExt'        : '*.gif;*.jpg;*.pdf;',
				'onComplete'  : function(event, ID, fileObj, response, data) {
					eval('var resposta = '+ response);
					$('#visualiza_img').html('<img src="'+resposta.img+'" width="300" height="300" />');
					$('#nome_logo').val(resposta.nome_arquivo);
					
				}
			} );
			
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
        <h1><span>Cadastro de Parceiro Operacional</span></h1>
        <?php if($_GET['nulo']) { echo 'O campo <b>'.$_GET['nulo'].'</b> não pode ficar em branco!'; } ?>
        <form name="cadastro"  id="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >          
        <table cellspacing="0" cellpadding="4" class="secao">
          <tr>
            <td class="secao">Nome:</td>
            <td colspan="5" ><input type="text" id="nome" name="nome" size="50" class="validate[required]"></td>
          </tr>
          <tr>
            <td class="secao">Regiao:</td>
            <td colspan="5">
            	<select name="regiao" id="regiao" class="validate[required]">
                <option value="">Selecione a região..</option>
                <?php
				$REGIOES->Preenhe_select_por_master($Master);
				?>                  
                </select>
            </td>
          </tr>
          <tr>
            <td class="secao">Endere&ccedil;o:</td>
            <td colspan="5"><input type="text" id="endereco" name="endereco" size="90" class="validate[required]"></td>
          </tr>
          <tr>
            <td class="secao">CNPJ:</td>
            <td><span class="descricao">
              <input type="text" id="cnpj" name="cnpj" size="25" class="validate[required]">
            </span></td>
            <td class="secao">CCM</td>
            <td><span class="descricao">
              <input type="text" id="ccm" name="ccm" size="25">
            </span></td>
            <td class="secao">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="secao">I.E.:</td>
            <td><span class="descricao">
              <input type="text" id="ie" name="ie" size="25" >
            </span></td>
            <td class="secao">&nbsp;</td>
            <td>&nbsp;</td>
            <td class="secao">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td class="secao">Bairro:</td>
            <td><input type="text" id="bairro" name="bairro" size="40" class="validate[required]"></td>
            <td class="secao">Cidade:</td>
            <td><input type="text" id="cidade" name="cidade" size="30" class="validate[required]"></td>
            
            <td class="secao">Estado:</td>
            <td><select name="estado" id="estado" class="validate[required]" > 
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
</select>  
            </td>
          </tr>
          <tr>
            <td class="secao">Telefone:</td>
            <td class="descricao"><input type="text" id="telefone" name="telefone" size="25" class="validate[required]"></td>
            <td class="secao">Celular:</td>
            <td colspan="3" class="descricao"><input type="text" id="celular" name="celular" size="25"></td>
          </tr>
          <tr>
            <td class="secao">Email:</td>
            <td colspan="5" class="descricao"><input type="text" id="email" name="email" size="40" ></td>
          </tr>
          <tr>
            <td class="secao">Contato:</td>
            <td class="descricao"><input type="text" id="contato" name="contato" size="40"></td>
            <td class="secao">CPF:</td>
            <td class="descricao"><input type="text" id="cpf" name="cpf" size="25"class="validate[required]"></td>
            <td class="secao">&nbsp;</td>
            <td class="descricao">&nbsp;</td>
          </tr>
          <tr>
            <td class="secao">Banco:</td>
            <td class="descricao"><input type="text" id="banco" name="banco" size="30"></td>
            <td class="secao">Ag&ecirc;ncia:</td>
            <td class="descricao"><input type="text" id="agencia" name="agencia" size="10"></td>
            <td class="secao">Conta:</td>
            <td class="descricao"><input type="text" id="conta" name="conta" size="17"></td>
          </tr>
          <tr>
            <td colspan="6" align="center"><input type="file" name="logo" id="logo" />
            <div id="barra_processo"></div>
            	<div id="visualiza_img">
                </div>
            </td>
          </tr>
          <tr>
            <td colspan="6" align="center"> 
            <input type="hidden" name="nome_logo" id="nome_logo" value="" />
             <input name="master" value="<?php echo $Master; ?>" type="hidden" />
             <input name="link_master" value="<?php echo $_GET['m']; ?>" type="hidden" />
             <input name="pronto" value="cadastro" type="hidden" />
             <input value="Cadastrar" type="submit" class="botao" style="float:right;" />
            </td>
          </tr>
        </table>
        </form>
    </div>
    <div id="rodape"><?php include('include/rodape.php'); ?></div>
</div>
</body>
</html>