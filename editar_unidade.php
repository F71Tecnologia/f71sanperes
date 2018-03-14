<?php
include('conn.php');
$unidade     = $_GET['id'];
$qr_unidade  = mysql_query("SELECT * FROM unidade WHERE id_unidade = '$unidade'");
$row_unidade = mysql_fetch_assoc($qr_unidade);

$result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_array($result_user);
$master = $row_user['id_master'];


if(isset($_POST['edicao']) and $_POST['edicao'] = 'edicao') {
	mysql_query("UPDATE unidade SET local = '$_POST[local]', tel = '$_POST[tel]', tel2 = '$_POST[tel2]', responsavel = '$_POST[responsavel]', cel = '$_POST[cel]', email = '$_POST[email]', endereco ='$_POST[endereco]',
                    bairro='$_POST[bairro]',cidade='$_POST[cidade]' ,cep ='$_POST[cep]',ponto_referencia = '$_POST[ponto_referencia]'
                    WHERE id_unidade = '$_POST[id_unidade]' LIMIT 1") or die(mysql_error());
	header("Location: visualizar_unidade.php?id=7&regiao=$_POST[regiao]&sucesso");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<link href="adm/css/estrutura.css" rel="stylesheet" type="text/css" />
<script src='jquery/jquery-1.4.2.min.js' type='text/javascript'></script>
<script src="jquery/validationEngine/jquery.validationEngine.js" type="text/javascript"></script>
<script src="jquery/validationEngine/jquery.validationEngine-pt.js" type="text/javascript"></script>
<script src="jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
<title>Editar Unidade</title>
<script type="text/javascript">
$(function() {
	
	$('#cep').mask('99999-999');
	$('#tel').mask('(99)9999-9999');
	$('#tel2').mask('(99)9999-9999');
	$('#cel').mask('(99)9999-9999');
});

</script>
</head>
<body>
	<div id="corpo">
    	<div id="conteudo">
        
         <div class="right"><?php include('reportar_erro.php'); ?></div>
       			 <div class="clear"></div>
                 
        		<img src="imagens/logomaster<?php echo $master?>.gif"/>
				<h3>EDITAR DE UNIDADES</h3>
                
            <form action='<?=$_SERVER['PHP_SELF']?>' method='post' name='form1'>
            <table width='100%' border='0' cellpadding='0' cellspacing='0' class="relacao" bgcolor="#EAEAEA"> 
           
            <tr>
              <td height="38" colspan='2' class="titulo_tabela1" >Edi&ccedil;&atilde;o de Unidade</td>
            </tr>
            <tr>
              <td width="30%" class="secao">Projeto:</td>
              <td width="70%" align="left">
            <?php $result_pro = mysql_query("SELECT * FROM projeto where id_projeto = '$row_unidade[campo1]' AND status_reg = '1'");
                  $row_pro    = mysql_fetch_array($result_pro);
                  echo $row_pro['id_projeto'].' - '.$row_pro['nome']; ?>
              </td>
            </tr>
            <tr>
            <td class="secao">Nome:</td>
            <td align="left"><?=$row_unidade['unidade']?></td>
            </tr>
            <tr>
                <td class="secao">Local:</td>
                <td align="left"><input name='local' type='text' class='campotexto' value="<?=$row_unidade['local']?>" id='local' size='20'></td>
            </tr>
            <tr>
            <td class="secao" align="right" >Endereco:</td>
            <td  align="left"><input name='endereco' type='text' class='campotexto' id='endereco' size='60' value="<?php echo $row_unidade['endereco']?>"/></td>
            </tr>
            <tr>
            <td class="secao" align="right" >Bairro:</td>
            <td  align="left"><input name='bairro' type='text' class='campotexto' id='bairro' size='20' value="<?php echo $row_unidade['bairro']?>"></td>
            </tr>
            <tr>
            <td class="secao" align="right" >Cidade:</td>
            <td  align="left"><input name='cidade' type='text' class='campotexto' id='cidade' size='20' value="<?php echo $row_unidade['cidade']?>" /> </td>
            </tr>
            <tr>
            <td class="secao" align="right" >CEP:</td>
            <td  align="left"><input name='cep' type='text' class='campotexto' id='cep' size='20' value="<?php echo $row_unidade['cep']?>"></td>
            </tr>
            <tr>
            <td class="secao" align="right" >Ponto de referência:</td>
            <td  align="left"><textarea name="ponto_referencia"><?php echo $row_unidade['ponto_referencia']?></textarea></td>
            </tr>
            <tr>
            <td class="secao">Telefone:</td>
            <td align="left"><input name='tel' type='text' id='tel' size='12' value="<?php if($row_unidade['tel'] != '(  )') { echo $row_unidade['tel']; } ?>" class='campotexto'></td>
            </tr>
            <tr>
            <td class="secao">Telefone Recado:</td>
            <td align="left"><input name='tel2' type='text'id='tel2' size='12' value="<?php if($row_unidade['tel2'] != '(  )') { echo $row_unidade['tel2']; } ?>" class='campotexto'></td>
            </tr>
            <tr>
            <td class="secao">Respons&aacute;vel:</td>
            <td align="left"><input name='responsavel' type='text' class='campotexto' value="<?=$row_unidade['responsavel']?>" id='responsavel' size='20'></td>
            </tr>
            <tr>
            <td class="secao">Celular do Respons&aacute;vel:</td>
            <td align="left">
            <input name='cel' type='text'  id='cel' size='12' value="<?php if($row_unidade['cel'] != '(  )') { echo $row_unidade['cel']; } ?>" class='campotexto'></td>
            </tr>
            <tr>
              <td class="secao">E-mail do Respons&aacute;vel:</td>
              <td align="left"><input name='email' type='text' class='campotexto' value="<?=$row_unidade['email']?>" id='email' size='20'></td>
            </tr>
            </table>
            
            <table width="100%">
            <tr>
              <td height="52"align='center'><input type='submit' name='Submit10' value='Atualizar'></td>
            </tr>
            </table>
            
            <input type="hidden" name="id_unidade" value="<?=$row_unidade['id_unidade']?>">
            <input type="hidden" name="regiao" value="<?=$row_unidade['id_regiao']?>">
            <input type="hidden" name="edicao" value="edicao">
            </form>
          </div>
 </div>
<br>

<a href='javascript:history.go(-1)' class='link'><img src='imagens/voltar.gif' border=0></a>
</body>
</html>