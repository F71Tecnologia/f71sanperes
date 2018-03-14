<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');

$parceiro = $_GET['id'];

$qr_parceiro  = mysql_query("SELECT * FROM parceiros WHERE parceiro_id = '$parceiro'");
$row_parceiro = mysql_fetch_assoc($qr_parceiro);

if($_POST['pronto'] == 'edicao') {

	$nulos = array('Nome' => $_POST['nome']);
	foreach($nulos as $campo => $valor) {
		if(empty($valor)) {
		    header("Location: cadastro.php?nulo=$campo&m=$_POST[link_master]");
		    exit;
		}
	}
	
	mysql_query("UPDATE parceiros SET parceiro_nome = '$_POST[nome]', parceiro_endereco = '$_POST[endereco]', parceiro_bairro = '$_POST[bairro]', parceiro_cidade = '$_POST[cidade]', parceiro_estado = '$_POST[estado]', parceiro_telefone = '$_POST[telefone]', parceiro_celular = '$_POST[celular]', parceiro_email = '$_POST[email]', parceiro_banco = '$_POST[banco]', parceiro_agencia = '$_POST[agencia]', parceiro_conta = '$_POST[conta]', parceiro_atualizacao = NOW() WHERE parceiro_id = '$_POST[parceiro]' LIMIT 1") or die(mysql_error());
	header("Location: index.php?sucesso=curso&m=$_POST[link_master]&curso=$id");
	
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
</head>
<body>
<div id="corpo">
    <div id="menu" class="contrato">
        <?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo">
        <h1><span>Edi&ccedil;&atilde;o de Parceiro</span></h1>
        <?php if($_GET['nulo']) { echo 'O campo <b>'.$_GET['nulo'].'</b> não pode ficar em branco!'; } ?>
        <form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validaForm()">          
        <table cellspacing="0" cellpadding="4" class="relacao">
          <tr>
            <td class="secao">Nome:</td>
            <td colspan="5"><input type="text" id="nome" name="nome" size="50" value="<?php echo $row_parceiro['parceiro_nome']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Endere&ccedil;o:</td>
            <td colspan="5"><input type="text" id="endereco" name="endereco" size="90" value="<?php echo $row_parceiro['parceiro_endereco']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Bairro:</td>
            <td><input type="text" id="bairro" name="bairro" size="40" value="<?php echo $row_parceiro['parceiro_bairro']; ?>"></td>
            <td class="secao">Cidade:</td>
            <td><input type="text" id="cidade" name="cidade" size="30" value="<?php echo $row_parceiro['parceiro_cidade']; ?>"></td>
            <td class="secao">Estado:</td>
            <td><input type="text" id="estado" name="estado" size="3" maxlength="2" value="<?php echo $row_parceiro['parceiro_estado']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Telefone:</td>
            <td class="descricao"><input type="text" id="telefone" name="telefone" size="25" value="<?php echo $row_parceiro['parceiro_telefone']; ?>"></td>
            <td class="secao">Celular:</td>
            <td colspan="3" class="descricao"><input type="text" id="celular" name="celular" size="25" value="<?php echo $row_parceiro['parceiro_celular']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Email:</td>
            <td colspan="5" class="descricao"><input type="text" id="email" name="email" size="40" value="<?php echo $row_parceiro['parceiro_email']; ?>"></td>
          </tr>
          <tr>
            <td class="secao">Banco:</td>
            <td class="descricao"><input type="text" id="banco" name="banco" size="30" value="<?php echo $row_parceiro['parceiro_banco']; ?>"></td>
            <td class="secao">Ag&ecirc;ncia:</td>
            <td class="descricao"><input type="text" id="agencia" name="agencia" size="10" value="<?php echo $row_parceiro['parceiro_agencia']; ?>"></td>
            <td class="secao">Conta:</td>
            <td class="descricao"><input type="text" id="conta" name="conta" size="17" value="<?php echo $row_parceiro['parceiro_conta']; ?>"></td>
          </tr>
          <tr>
            <td colspan="6" align="center">
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