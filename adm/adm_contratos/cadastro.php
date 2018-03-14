<?php 
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');

if($_POST['pronto'] == 'cadastro1') {
	
	$nulos = array('Modelo' => $_POST['modelo']);
	
	foreach($nulos as $campo => $valor) {
		if(empty($valor)) {
		    header("Location: cadastro.php?nulo=$campo&m=$_POST[link_master]&projeto=$_POST[projeto]");
		    exit;
		}
	}
	
	mysql_query("INSERT INTO obrigacoes (obrigacao_projeto, obrigacao_modelo, obrigacao_autor, obrigacao_data) VALUES ('$_POST[projeto]', '$_POST[modelo]', '$_COOKIE[logado]', NOW())") or die(mysql_error());
	header("Location: index.php?sucesso=obrigacao&m=$_POST[link_master]");
	
}

if($_POST['pronto'] == 'cadastro2') {
	
	if(!empty($_FILES['anexo']['name'])) {
		
		extract($_FILES['anexo']);
		
		$extensao = strrchr($name,'.');
		$anexo    = md5(uniqid($name)).$extensao;
		
		move_uploaded_file($tmp_name,'anexos/'.$anexo);
		
	}
	
	$nulos = array('Nome' => $_POST['nome'], 'Dia de Entrega' => $_POST['dia'], 'Periodicidade' => $_POST['periodicidade']);
	
	foreach($nulos as $campo => $valor) {
		if(empty($valor)) {
		    header("Location: cadastro.php?nulo=$campo&m=$_POST[link_master]&projeto=$_POST[projeto]&aberto=$_GET[aberto]");
		    exit;
		}
	}
	
	$data_inicio  = implode('-',array_reverse(explode('/',$_POST['data_inicio'])));
	$data_termino = implode('-',array_reverse(explode('/',$_POST['data_termino'])));
	
	mysql_query("INSERT INTO obrigacoes (obrigacao_projeto, obrigacao_nome, obrigacao_descricao, obrigacao_dia, obrigacao_periodicidade, obrigacao_data_inicio, obrigacao_data_termino, obrigacao_anexo, obrigacao_modelo, obrigacao_status, obrigacao_autor, obrigacao_data) VALUES ('$_POST[projeto]', '$_POST[nome]', '$_POST[descricao]', '$_POST[dia]', '$_POST[periodicidade]', '$data_inicio', '$data_termino', '$anexo', '$_POST[modelo]', '1', '$_COOKIE[logado]', NOW())") or die(mysql_error());
	header("Location: index.php?sucesso=obrigacao&m=$_POST[link_master]&aberto=$_GET[aberto]&#$_GET[aberto]");
	
}

$projeto = $_GET['projeto'];
?>
<html>
<head>
<title>Cadastro de Obriga&ccedil;&atilde;o</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../css/estrutura.css" rel="stylesheet" type="text/css">
<script src="../../js/ramon.js" type="text/javascript"></script>
</head>
<body>
<div id="corpo">
    <div id="menu" class="contrato">
        <?php include('include/menu.php'); ?>
    </div>
    <div id="conteudo">
        <h1><span>Cadastro de Obriga&ccedil;&atilde;o</span></h1>
            <h3><?php if($_GET['nulo']) { echo 'O campo <b>'.$_GET['nulo'].'</b> não pode ficar em branco!'; } ?></h3>
        
        <table cellspacing="0" cellpadding="4" class="relacao" width="100%">
          <tr>
            <td>
            
         <!--<form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?projeto='.$projeto; ?>" onSubmit="return validaForm()" enctype="multipart/form-data">
                <table>
                  <tr>
                    <td class="secao">Projeto:</td>
                    <td><?php if(!empty($projeto)) {
                                  $qr_projetos = mysql_query("SELECT * FROM projeto a INNER JOIN regioes b ON a.id_regiao = b.id_regiao WHERE b.status = '1' AND a.status_reg = '1' AND a.id_master = '$Master' AND a.id_projeto = '$projeto' ORDER BY nome ASC");
                                  $row_projeto = mysql_fetch_assoc($qr_projetos);
                                  echo $row_projeto['nome'].' ('.@mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_projeto[id_regiao]'"),0).')<input type="hidden" name="projeto" value="'.$projeto.'">';
                              } else {
                                  echo '<select name="projeto">';
                                  $qr_projetos = mysql_query("SELECT * FROM projeto a INNER JOIN regioes b ON a.id_regiao = b.id_regiao WHERE b.status = '1' AND a.status_reg = '1' AND a.id_master = '$Master' ORDER BY nome ASC");
                                  while($row_projeto = mysql_fetch_assoc($qr_projetos)) {
                                      echo '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['nome'].' ('.$row_projeto['regiao'].')</option>';
                                  }
                                  echo '</select>';
                              } ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="secao" valign="top">Modelo:</td>
                    <td><?php $qr_modelos = mysql_query("SELECT * FROM obrigacoes_modelos WHERE modelo_status = '1'");
                              while($row_modelo = mysql_fetch_assoc($qr_modelos)) { ?>
                                  <label><input type="radio" name="modelo" value="<?php echo $row_modelo['modelo_id']; ?>"> <?php echo $row_modelo['modelo_nome']; ?></label><br>
                        <?php } ?>
                    </td>
                  </tr>
                  <tr>
                    <td></td>
                    <td>
                       <input name="master" value="<?php echo $Master; ?>" type="hidden" />
                       <input name="link_master" value="<?php echo $_GET['m']; ?>" type="hidden" />
                       <input name="pronto" value="cadastro1" type="hidden" />
                       <input value="Cadastrar" type="submit" class="botao" />
                    </td>
                  </tr>
                </table>
             </form>
         
           </td>
         </tr>
         <tr>
           <td class="secao" colspan="2" style="color:#aaa; font-style:italic; text-align:left; padding-left:240px;">ou</td>
         </tr>
         <tr>
           <td>-->
            
             <form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?projeto='.$projeto.'&aberto='.$_GET['aberto']; ?>" onSubmit="return validaForm()" enctype="multipart/form-data">            
                <table style="margin-top:15px;">
                  <tr>
                    <td class="secao">Projeto:</td>
                    <td><?php if(!empty($projeto)) {
                                  $qr_projetos = mysql_query("SELECT * FROM projeto a INNER JOIN regioes b ON a.id_regiao = b.id_regiao WHERE b.status = '1' AND a.status_reg = '1' AND a.id_master = '$Master' AND a.id_projeto = '$projeto' ORDER BY nome ASC");
                                  $row_projeto = mysql_fetch_assoc($qr_projetos);
                                  echo $row_projeto['nome'].' ('.@mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_projeto[id_regiao]'"),0).')<input type="hidden" name="projeto" value="'.$projeto.'">';
                              } else {
                                  echo '<select name="projeto">';
                                  $qr_projetos = mysql_query("SELECT * FROM projeto a INNER JOIN regioes b ON a.id_regiao = b.id_regiao WHERE b.status = '1' AND a.status_reg = '1' AND a.id_master = '$Master' ORDER BY nome ASC");
                                  while($row_projeto = mysql_fetch_assoc($qr_projetos)) {
                                      echo '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['nome'].' ('.$row_projeto['regiao'].')</option>';
                                  }
                                  echo '</select>';
                              } ?>
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
                    <td class="secao">Dia de Entrega:</td>
                    <td><input type="text" name="dia" size="2" /></td>
                  </tr>
              <!--<tr>
                    <td class="secao">Data de In&iacute;cio:</td>
                    <td><input type="text" name="data_inicio" size="8" maxlength="10" onKeyPress="formatar('00/00/0000', this)" /></td>
                  </tr>-->
                  <tr>
                    <td class="secao">Periodicidade:</td>
                    <td>
                        <label>
                          <input type="radio" name="periodicidade" value="mensal" />  Mensal
                        </label>
                        <label>
                          <input type="radio" name="periodicidade" value="trimestral" /> Trimestral
                        </label>
                        <label>
                          <input type="radio" name="periodicidade" value="semestral" /> Semestral
                        </label>
                        <label>
                          <input type="radio" name="periodicidade" value="anual" /> Anual
                        </label>
                    </td>
                  </tr>
              <!--<tr>
                    <td class="secao">Data de T&eacute;rmino:</td>
                    <td><input type="text" name="data_termino" size="8" maxlength="10" onKeyPress="formatar('00/00/0000', this)" /></td>
                  </tr>-->
                  <tr>
                    <td></td>
                    <td>
                       <input name="master" value="<?php echo $Master; ?>" type="hidden" />
                       <input name="link_master" value="<?php echo $_GET['m']; ?>" type="hidden" />
                       <input name="pronto" value="cadastro2" type="hidden" />
                       <input value="Cadastrar" type="submit" class="botao" />
                    </td>
                  </tr>
                </table>
             </form>
            
            </td>
          </tr>
        </table>
    </div>
    <div id="rodape"><?php include('include/rodape.php'); ?></div>
</div>
</body>
</html>