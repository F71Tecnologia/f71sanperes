<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="login.php">Logar</a>';
	exit();
}

include('../conn.php');

$id = $_REQUEST['id'];

$sql = "select t.*, p.nome as projeto from terceirizado t inner join projeto p on t.id_projeto = p.id_projeto where id_terceirizado = {$id};";

$result = mysql_query($sql);
$row = mysql_fetch_array($result);

//$projeto = $row['id_projeto'];
$projeto = $_REQUEST['pro'];
//$regiao  = $row['id_regiao'];
$regiao  = $_REQUEST['reg'];

if($_REQUEST['confArq'] == "Y")
{
    require_once('../webmail/inc/funcao_upload.php');
    $blacklist = array('bat', 'com', 'exe');
    
    $arquivo = $_FILES['arquivo'];
    $enviar = uploadFile($arquivo, '../fotos/', $blacklist);
    $data[$i]['sucesso'] = false;

    if($enviar['erro']){
        $data[$i]['filepath'] = $enviar['erro'];
    }
    else{
        $data[$i]['sucesso'] = true;

        /* Caminho do arquivo */
        $data[$i]['filepath'] = $enviar['caminho'];
    }
    
    $sql = "update terceirizado set foto = '{$enviar['caminho']}' where id_terceirizado = {$id}";
    mysql_query ($sql) or die ("Ops! Erro<br>" . mysql_error());
    
    $row['foto'] = $enviar['caminho'];
}

?>
<html>
<head>
    <title>:: Intranet ::</title>
    <link rel="shortcut icon" href="http://www.netsorrindo.com/intranet/favicon.ico">
    <script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <link href="../rh/css/estrutura_participante.css" rel="stylesheet" type="text/css">
    <style>
	.fileUpload {
		position: relative;
		overflow: hidden;
		margin: 10px;
	}
	.fileUpload input.upload {
		position: absolute;
		top: 0;
		right: 0;
		margin: 0;
		padding: 0;
		font-size: 20px;
		cursor: pointer;
		opacity: 0;
		filter: alpha(opacity=0);
		background-color: #565556;
	} 
	.btn-gray {
	    background-color: #575757;
	    border-color: #D43F3A;
	    color: #FFFFFF;
	}
	.btn {
	    -moz-user-select: none;
	    background-image: none;
	    border: 1px solid rgba(0, 0, 0, 0);
	    border-radius: 2px;
	    cursor: pointer;
	    display: inline-block;
	    font-size: 14px;
	    font-weight: normal;
	    line-height: 1.42857;
	    margin-bottom: 0;
	    padding: 3px 6px;
	    text-align: center;
	    vertical-align: middle;
	    white-space: nowrap;
	}	
    </style>
</head>
<body style="background-color: #FAFAFA; font-family: Trebuchet MS,Helvetica,sans-serif;">
    <form id="formFoto" enctype="multipart/form-data" method="post" name="formFoto" action="ver_terceiro.php">
    <input type="hidden" name="confArq" id="confArq" value="N" />
    <input type="hidden" name="id" id="id" value="<?=$id?>" />
    <center>
	<div style="width: 650px; background-color: #FFFFFF">
	    <table style="width: 100%">
		<tr>
		    <td style="font-size:18px;text-align: right">
			<a onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )" href="http://www.netsorrindo.com/intranet/box_suporte.php?&regiao=&pagina=/intranet/terceirizado/ver_terceiro.php" style="background-color:transparent;">
			<img border="0" src="http://www.netsorrindo.com/intranet/img_menu_principal/helpdesk2.png">
			</a>			
		    </td>
		</tr>		
		<tr>
		    <td style="font-size:18px;">VISUALIZAR <span style="color: #669966">TERCEIRIZADO</span></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
		    <td style="border-bottom: 2px solid #F3F3F3; font-size:18px;">Matrícula:</td>
		</tr>
		<tr>
		    <td>&nbsp;</td>
		</tr>
		<tr>
		    <td>
			<table>
			    <tr>
				<td style="width: 120px; text-align: center;">
				    <img id="imgFile" width="100" height="130" style="margin-top:-12px; margin-bottom:5px;" name="imgFile" src="<?=trim($row['foto'])==""?"../fotos/semimagem.gif":$row['foto']?>">
				</td>
				<td>
				    <table style="background-color: #F3F3F3; padding: 5px;">
					<tr>
					    <td>
						<?=$row['id_terceirizado']?> - <?=$row['nome']?><br/>
						CPF: <?=$row['cpf']?><br/>
						Data de Entrada: <?=date("d/m/Y", strtotime($row['data_nasci']))?><br/>
						Projeto: <?=$row['projeto']?><br/>
					    </td>
					</tr>
				    </table>
				</td>
			    </tr>
			    <tr>
				<td style="text-align: center;">
				    <div class="fileUpload btn btn-gray">
					<span>Adcionar Foto</span>
					<input type="file" class="upload" id="arquivo" name="arquivo" />
				    </div>
				</td>
				<td></td>
			    </tr>
			</table>
		    </td>
		</tr>
		<tr>
		    <td>
			&nbsp;
		    </td>
		</tr>
		<tr>
		    <td style="border-bottom: 1px solid #FF3300; font-size:16px; color: #FF3300">
			MENU DE EDIÇÃO
		    </td>
		</tr>
		<tr>
		    <td>
			&nbsp;
		    </td>
		</tr>
		<tr>
		    <td>
			<a class="botao" href="alterterceiro.php<?= '?reg='.$regiao.'&pro='.$projeto.'&id='.$id; ?>">Editar Cadastro</a>
		    </td>
		</tr>			
	    </table>
	</div>
    </center>
    </form>
<script>
    $(document).ready(function() {
	$("#arquivo").change(function(){
	    $("#confArq").val("Y");
	    $("#formFoto").submit();
	});
    });
</script>
</body>
</html>