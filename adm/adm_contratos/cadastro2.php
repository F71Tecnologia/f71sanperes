<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');

if($_POST['pronto'] == 'cadastro') {
	
	if(!empty($_FILES['anexo']['name'])) {
		
		extract($_FILES['anexo']);
		
		$extensao = strrchr($name,'.');
		$anexo    = md5(uniqid($name)).$extensao;
		
		move_uploaded_file($tmp_name,'anexos/'.$anexo);
		
	}
	
	$nulos = array('Nome' => $_POST['nome']);
	
	foreach($nulos as $campo => $valor) {
		if(empty($valor)) {
		    header("Location: cadastro.php?nulo=$campo&m=$_POST[link_master]&projeto=$_POST[projeto]&aberto=$_GET[aberto]");
		    exit;
		}
	}
	
	$data_publicacao = implode('-',array_reverse(explode('/',$_POST['data_publicacao'])));
	
	mysql_query("UPDATE obrigacoes SET obrigacao_nome = '$_POST[nome]', obrigacao_descricao = '$_POST[descricao]', obrigacao_data_inicio = '$data_publicacao', obrigacao_anexo = '$anexo', obrigacao_autor = '$_COOKIE[logado]', obrigacao_data = NOW() WHERE obrigacao_id = '$_POST[obrigacao]' LIMIT 1") or die(mysql_error());
	
	mysql_query("INSERT INTO obrigacoes_entregues (entregue_obrigacao, entregue_dataproc, entregue_datareferencia, entregue_autor, entregue_data) VALUES ('$_POST[obrigacao]', NOW(), '$data_publicacao', '$_COOKIE[logado]', NOW())") or die(mysql_error());
	
	print "<script type='text/javascript'>
			  parent.window.location.reload();
			  if (parent.window.hs) {
				var exp = parent.window.hs.getExpander();
				if (exp) {
						exp.close();
				}
			  }
		   </script>";
	
}

$projeto    = $_GET['projeto'];
$subprojeto = $_GET['subprojeto'];
$obrigacao  = $_GET['obrigacao'];
?>
<html>
<head>
<title>Cadastro de Obriga&ccedil;&atilde;o</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<link href="../../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
<link href="../../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.core.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../../js/jquery.ui.datepicker-pt-BR.js"></script>
<script type="text/javascript">
$(function(){
	$('.data_publicacao').datepicker({
		changeMonth: true,
		changeYear: true
	});
});
</script>
</head>
<body>
<div id="conteudo">
  <h1><span>Cadastro de Obriga&ccedil;&atilde;o</span></h1>
    <h3><?php if($_GET['nulo']) { echo 'O campo <b>'.$_GET['nulo'].'</b> não pode ficar em branco!'; } ?></h3>
    
    <table cellspacing="0" cellpadding="4" class="relacao" width="100%">
      <tr>
        <td>

         <form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?projeto='.$projeto.'&aberto='.$_GET['aberto']; ?>" onSubmit="return validaForm()" enctype="multipart/form-data">            
            <table style="margin-top:15px;">
              <tr>
                <td class="secao">Projeto:</td>
                <td><?php if(!empty($subprojeto)) {
							  $qr_projetos = mysql_query("SELECT * FROM subprojeto a INNER JOIN regioes b ON a.id_regiao = b.id_regiao WHERE b.status = '1' AND a.status_reg = '1' AND a.id_master = '$Master' AND a.id_projeto = '$projeto' AND id_subprojeto = '$subprojeto' ORDER BY nome ASC");
							  $row_projeto = mysql_fetch_assoc($qr_projetos);
							  $nome = $subprojeto.' - '.$row_projeto['tipo_contrato'];
						  } else {
							  $qr_projetos = mysql_query("SELECT * FROM projeto a INNER JOIN regioes b ON a.id_regiao = b.id_regiao WHERE b.status = '1' AND a.status_reg = '1' AND a.id_master = '$Master' AND a.id_projeto = '$projeto' ORDER BY nome ASC");
							  $row_projeto = mysql_fetch_assoc($qr_projetos);
							  $nome = $projeto.' - '.$row_projeto['nome'];
						  }

						  echo $nome.' ('.@mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_projeto[id_regiao]'"),0).')<input type="hidden" name="projeto" value="'.$projeto.'">'; ?>
                </td>
              </tr>
              <tr>
                <td class="secao">Anexo:</td>
                <td><input type="file" name="anexo" /></td>
              </tr>
              <tr>
                <td class="secao">Nome:</td>
                <td><input type="text" name="nome" size="53"></td>
              </tr>
              <tr>
                <td class="secao">Descrição:</td>
                <td><textarea name="descricao" cols="40" rows="4"></textarea></td>
              </tr>
              <tr>
                <td class="secao">Data de Publicação:</td>
                <td><input type="text" name="data_publicacao" class="data_publicacao" size="10" /></td>
              </tr>
              <tr>
                <td></td>
                <td>
                   <input name="obrigacao" value="<?php echo $obrigacao; ?>" type="hidden" />
                   <input name="link_master" value="<?php echo $_GET['m']; ?>" type="hidden" />
                   <input name="pronto" value="cadastro" type="hidden" />
                   <input value="Cadastrar" type="submit" class="botao" />
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