<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');

$parceiro = $_GET['id'];

$qr_parceiro  = mysql_query("SELECT * FROM parceiros WHERE parceiro_id = '$parceria'");
$row_parceiro = mysql_fetch_assoc($qr_parceiro);

if($_POST['pronto'] == 'exclusao') {
	
	mysql_query("DELETE FROM parceiros WHERE parceiro_id = '$_POST[parceiro]'") or die(mysql_error());
	header("Location: index.php?sucesso=curso&m=$_POST[link_master]&curso=$id");
	
}
?>
<html>
<head>
<title>Exclus&atilde;o de Parceiro</title>
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
        <h1><span>Exclus&atilde;o de Parceiro</span></h1>
        <form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validaForm()">          
            Excluir <?php $row_parceiro['parceiro_nome']; ?>?
            <input type="submit" value="Sim">
            <input name="master" value="<?php echo $Master; ?>" type="hidden" />
            <input name="parceiro" value="<?php echo $parceiro; ?>" type="hidden" />
            <input name="link_master" value="<?php echo $_GET['m']; ?>" type="hidden" />
            <input name="pronto" value="exclusao" type="hidden" />
        </form>
    </div>
    <div id="rodape"><?php include('include/rodape.php'); ?></div>
</div>
</body>
</html>