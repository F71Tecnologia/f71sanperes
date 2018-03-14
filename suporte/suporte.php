<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit;
}

include('../conn.php');

$id_user = $_COOKIE['logado'];
$regiao  = $_REQUEST['regiao'];

if(empty($_REQUEST['tela'])) {
	$tela = '1';
} else {
	$tela = $_REQUEST['tela'];
}

switch ($tela) {
	case 1:	
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net1.css" rel="stylesheet" type="text/css" />
<title>Suporte</title>
<style type="text/css">
body {
	text-align:center;
}
p {
	margin:0px;
}
a {
	color:#333; text-decoration:underline;
}
#corpo {
	width:90%; margin:0px auto; text-align:left; background-color:#FFF; padding:25px;
}
.topo {
	font-weight:bold; font-size:15px; background-color:#f5f5f5;border:1px solid #ddd; padding:20px; width:90%; margin:0px auto; text-align:center;
}
p.legendas {
	margin-top:20px; font-size:12px;
}
p.legendas img {
	margin-left:12px;
}
.secao_pai {
	font-weight:bold; font-size:11px; padding:50px 0px 10px 10px; background-color:#ddd; text-align:center;
}
.secao {
	font-weight:bold; text-align:right; font-size:12px; background-color:#f2f6f9;
}
.linha_um, .linha_dois {
	text-align:center;
}
.linha_um {
	background-color:#f5f5f5;
}
.linha_dois {
	background-color:#ebebeb;
}
.linha_um td, .linha_dois td {
	border-bottom:1px solid #ccc;
}
.secao_resposta {
	background-color:#eee; font-weight:bold; text-align:right; padding-right:4px;
}
table {
	width:95%; padding:20px; border:0; margin:0px auto;
}
</style>
</head>
<body>
<div id="corpo">
	<div class="topo">
        <img src="imgsuporte/suporte.png" width="39" height="39" /> SUPORTE
	</div>

	<form action="suporte.php" method="post" enctype="multipart/form-data" id="form1" name="form1" onSubmit="return validaForm()">
      <input type="hidden" value="<?=$regiao?>" name="regiao" />
      <input type="hidden" value="2" name="tela" />
		<table cellpadding="4" cellspacing="1">
            <tr>
              <td width="30%" class="secao">Tipo de Ocorr&ecirc;ncia:</td>
              <td width="70%">
                <select name="tipo" class="linha" id="tipo">
                  <option value="1">Informa&ccedil;&atilde;o</option>
                  <option value="2">Reclama&ccedil;&atilde;o</option>
                  <option value="3">Inclus&atilde;o</option>
                  <option value="4">Exclus&atilde;o</option>
                  <option value="5">Erro</option>
                  <option value="6">Sugest&atilde;o</option>
                  <option value="7">Altera&ccedil;&atilde;o</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="secao">Prioridade:</td>
              <td>
                <select name="prioridade" id="StPrioridade" class="linha">
                  <option value="1" style="background-color:#FC9;" selected="selected">Baixa</option>
                  <option value="2" style="background-color:#FC6;">Media</option>
                  <option value="3" style="background-color:#F90;">Alta</option>
                  <option value="4" style="background-color:#F30;">Urgente</option>
                </select>
              </td>
            </tr>
            <tr>
              <td class="secao">Exibi&ccedil;&atilde;o da Ocorr&ecirc;ncia:</td>
              <td colspan="3">
                <select name="exibicao" class="linha" id="exibicao">
                 <option value="1" selected>Particular</option>
                  <option value="3">P&uacute;blica</option>
                  </select>
              </td>
              </tr>
            <tr>
              <td class="secao">Assunto:</td>
              <td colspan="3"><input name="assunto" class="linha" id="assunto" size="50" maxlength="60" 
              onChange="this.value=this.value.toUpperCase()"/></td>
            </tr>
            <tr>
              <td class="secao">Mensagem:</td>
              <td colspan="3">
                <textarea name="mensagem" cols="48" rows="10" class="linha" id="StMensagem" 
                onChange="this.value=this.value.toUpperCase()"></textarea>
              </td>
            </tr>
            <tr>
              <td class="secao">Anexo:</td>
              <td colspan="3">&nbsp;&nbsp;
                <label class="linha">
                  <input name='foto' type='checkbox' id='foto' 
onClick="document.all.logomarca.style.display = (document.all.logomarca.style.display == 'none') ? '' : 'none' ;" value="1"/> <b>Sim</b>
                </label>
                <span style="display:none;" id="logomarca"><br>&nbsp;&nbsp;
                  selecione:
                  <input type="file" name="arquivo" id="arquivo" class="campotexto">
                  <font color='#666666' style="font-size:9px;">(.jpg, .png, .gif, .jpeg)</font>
                </span></td>
            </tr>
            <tr>
              <td class="secao" align="center" colspan="4">
                <input type="submit" value="Criar Chamado" name="btnOK" />        
				<script>
                  function validaForm() {
                   d = document.form1;
                
                   if(d.assunto.value == "") {
					   alert("O campo Assunto deve ser preenchido!");
					   d.assunto.focus();
					   return false;
                   }
                
                   if(d.mensagem.value == "") {
					   alert("O campo Mensagem deve ser preenchido!");
					   d.mensagem.focus();
					   return false;
                   }
                
                  return true;   
                }
                </script>
             </td>
           </tr>
        </table>
	  </form>
	
    <?php $result_ab = mysql_query("SELECT *, date_format(data_cad, '%d/%m/%Y') AS data_cad, date_format(ultima_alteracao, '%d/%m/%Y') AS ultima_alteracao FROM suporte WHERE status != '4' ORDER BY id_suporte DESC"); 
		  $verifica  = mysql_num_rows($result_ab);
		  
		 
		  if(!empty($verifica)) { ?>
          
        <div class="topo" style="margin-top:50px;">
            CHAMADOS AGUARDANDO ATENDIMENTO
            <p class="legendas">
                <img src="imgsuporte/aberto.png" width="12" height="12" /> ABERTO
                <img src="imgsuporte/respondido.png" width="12" height="12" /> RESPONDIDO
                <img src="imgsuporte/replicado.png" width="12" height="12" /> REPLICADO
            </p>
        </div>

		<table cellpadding="4" cellspacing="0">
          <tr class="secao_pai">
            <td>Abertura</td>
            <td>Chamado</td>
            <td>Assunto</td>
            <td>&Uacute;ltimo movimento</td>
            <td>Situa&ccedil;&atilde;o</td>
            <td>Finalizar</td>
          </tr>

	<?php while($row_ab = mysql_fetch_array($result_ab)) {
		
			if($row_ab['status'] == '1') {
				
				$imagem = '<img src="imgsuporte/aberto.png" alt="aberto" width="18" height="18">';
			} elseif($row_ab['status'] == '2') {
				$imagem = '<img src="imgsuporte/respondido.png" alt="respondido" width="18" height="18">';
			} elseif($row_ab['status'] == '3') {
				$imagem = '<img src="imgsuporte/replicado.png" alt="replicado" width="18" height="18">';
			}
			
			if($row_ab['exibicao'] == 3 or ($row_ab['user_cad'] == $id_user or $row_ab['user_res'] == $id_user)) { ?>

            <tr class="linha_<?php if($cor++%2) { echo 'um'; } else { echo 'dois'; } ?>">
                <td><?=$row_ab['data_cad']?></td>
                <td><?=sprintf('%04d', $row_ab['id_suporte'])?></td>
                <td><a href="chamado.php?chamado=<?=$row_ab[0]?>&regiao=<?=$regiao?>"><?=$row_ab['assunto']?></a></td>
                <td><?=$row_ab['ultima_alteracao']?>&nbsp;</td>
                <td><?=$imagem?></td>
                <td><a href="suporte.php?tela=3&chamado=<?=$row_ab[0]?>&regiao=<?=$regiao?>">
                        <img src="imgsuporte/finalizar.png" alt="finalizar" border="0"/>
                    </a></td>
            </tr>
  
	<?php }
	 } ?>
    
    </table>
    
    <?php } $result_fe = mysql_query("SELECT *, date_format(data_cad, '%d/%m/%Y') AS data_cad, date_format(ultima_alteracao, '%d/%m/%Y') AS ultima_alteracao, date_format(data_fechamento, '%d/%m/%Y') AS data_fechamento FROM suporte WHERE status = '4' ORDER BY id_suporte DESC");
			$verifica  = mysql_num_rows($result_fe);
			
			if(!empty($verifica)) { ?>
    
    <div class="topo" style="margin-top:50px;">
    	CHAMADOS FINALIZADOS
	</div>
    
    <table cellpadding="4" cellspacing="0">
      <tr class="secao_pai">
        <td>Abertura</td>
        <td>Chamado</td>
        <td>Assunto</td>
        <td>&Uacute;ltimo movimento</td>
        <td>Finalizado</td>
      </tr>
      
  <?php while($row_fe = mysql_fetch_array($result_fe)) {
		
			if($row_fe['exibicao'] == 3 or ($row_fe['user_cad'] == $id_user or $row_fe['user_res'] == $id_user)) { ?>
        
            <tr class="linha_<?php if($cor++%2) { echo 'um'; } else { echo 'dois'; } ?>">
                <td><?=$row_fe['data_cad']?></td>
                <td><?=sprintf("%04d", $row_fe['id_suporte'])?></td>
                <td><a href="chamado.php?chamado=<?=$row_fe[0]?>&regiao=<?=$regiao?>"><?=$row_fe['assunto']?></a></td>
                <td><?=$row_fe['ultima_alteracao']?></td>
                <td><?=$row_fe['data_fechamento']?></td>
            </tr>
        
	  <?php }
  	    } ?>
         
     </table>
     
     <?php } ?>
                          
    	<div class="topo" style="margin-top:50px; font-size:12px;">
          <b>OBS:</b> 
          Chamados respondidos e n&atilde;o finalizados ser&atilde;o exclu&iacute;dos automaticamente em 10 dias.
          <br />
          Os usu&aacute;rios s&oacute; podem ver chamados do mesmo grupo de trabalho. N�o ser&atilde;o alteradas senhas ou altera&ccedil;&otilde;es em usu&aacute;rios de nenhum tipo.</div>
        
</div>
</body>
</html>

<?php
break;
case 2: 

$user_cad   = $_COOKIE['logado'];
$regiao     = $_REQUEST['regiao'];
$exibicao   = $_REQUEST['exibicao'];
$tipo       = $_REQUEST['tipo'];
$prioridade = $_REQUEST['prioridade'];
$assunto    = $_REQUEST['assunto'];
$mensagem   = $_REQUEST['mensagem'];
$data_cad   = date('Y-m-d H:i:s');
$foto       = $_REQUEST['foto'];
$arquivo    = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

if($foto == '1') {
  
  if($arquivo['type'] != 'image/x-png' && 
     $arquivo['type'] != 'image/pjpeg' && 
	 $arquivo['type'] != 'image/gif'   && 
	 $arquivo['type'] != 'image/jpeg') { ?>
     
     <center>
     <b>Tipo de arquivo n�o permitido, os �nicos padr�es permitidos s�o <i>gif</i>, <i>jpg</i> ou <i>png</i>
     <br><a href="suporte.php?regiao=<?=$regiao?>">Voltar</a></b>
     </center>

  <?php exit();

        } else { // aqui o arquivo � realente de imagem e vai ser carregado para o servidor

			list($nulo,$file_type) = explode('.', $arquivo['name']); 
		   
			if($file_type == 'gif') {
				$tipo_arquivo = '.gif'; 
			} elseif($file_type == 'jpg' or $arquivo['type'] == 'jpeg') {
				$tipo_arquivo = '.jpg'; 
			} elseif($file_type == 'png') { 
				$tipo_arquivo = '.png'; 
			}
			
			// Resolvendo o nome e para onde o arquivo ser� movido
			$diretorio = 'arquivos/';
	
        }

		mysql_query("INSERT INTO suporte (user_cad,data_cad,id_regiao,exibicao,tipo,prioridade,assunto,mensagem,arquivo,status,status_reg)
					 VALUES	('$user_cad','$data_cad','$regiao','$exibicao','$tipo','$prioridade','$assunto','$mensagem','$tipo_arquivo','1', '1')") or die (mysql_error());

		$id_insert = mysql_insert_id();

		$nome_tmp     = 'suporte_'.$regiao.'_'.$id_insert.$tipo_arquivo;
		$nome_arquivo = $diretorio.$nome_tmp;
  
		move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die ("Erro ao enviar o Arquivo: $nome_arquivo"); ?>

<script>
alert ("Obrigado por entrar em contato.... \n Acompanhe periodicamente seu pedido, logo lhe responderemos!");
location.href = 'suporte.php?regiao=<?=$regiao?>';
</script>

<?php } else { // AQUI N�O TEM ARQUIVO EM ANEXO

mysql_query("INSERT INTO suporte(user_cad,data_cad,id_regiao,exibicao,tipo,prioridade,assunto,mensagem,ultima_alteracao,status,status_reg)
VALUES ('$user_cad','$data_cad','$regiao','$exibicao','$tipo','$prioridade','$assunto','$mensagem','$data_cad','1', '1')") or die ("Nosso servidor se n�o se comportou como deveria, tente mais tarde! Obrigado.<br><br>".mysql_error()); ?>

<script>
alert("Obrigado por entrar em contato.... \n Acompanhe periodicamente seu pedido, logo lhe responderemos!");
location.href = 'suporte.php?regiao=<?=$regiao?>';
</script>

<?php }

break;
case 3:

$user    = $_COOKIE['logado'];
$regiao  = $_REQUEST['regiao'];
$chamado = $_REQUEST['chamado'];
$data    = date('Y-m-d');

mysql_query("UPDATE suporte SET id_fechamento = '$user', data_fechamento = '$data', ultima_alteracao = '$data', status = '4' WHERE id_suporte = '$chamado'"); 
?>

<script>
alert("Feito.... \n Chamado finalizado com sucesso!");
location.href = 'suporte.php?regiao=<?=$regiao?>';
</script>

<?php
break; }
?>