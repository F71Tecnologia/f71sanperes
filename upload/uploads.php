<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";
exit;
}
include "../conn.php";
include "classes.php";

$participante = $_REQUEST['participante'];
$contratacao = $_REQUEST['contratacao'];

$MsgErro = "<center>\n <hr><font size=3 color=#000000><b>Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png<br>
<br><a href='uploads.php?participante=$participante&contratacao=$contratacao'>Voltar</a></b></font><br><br>"; 

if(empty($_REQUEST['enviado'])){
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head> 
<title>:: Intranet ::</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../net.css" rel="stylesheet" type="text/css">
<style type="text/css">
.borda {border:#999 2px solid;}
</style>
</head>
<body \>
<form action="uploads.php" method="post" name="form1"  enctype='multipart/form-data' onSubmit="return validaForm()">
<table width="537" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center" class="borda">
  <tr>
    <th height="40" colspan="2" bgcolor="#CCCCCC">Tipo de Documento</th>
    <th width="247" height="40" bgcolor="#CCCCCC">Arquivo</th>
  </tr>
  <tr>
    <td width="34" align="center" valign="middle">1</td>
    <td width="252" height="30">&nbsp;&nbsp;      <?php $select = new upload(); $select -> montaSelect("upload1"); ?></td>
    <td height="30"><input type="file" value="Procurar" name="arquivo1" id="arquivo1"></td>
  </tr>
  <tr>
    <td align="center" valign="middle">2</td>
    <td height="30">&nbsp;&nbsp;
      <?php $select = new upload(); $select -> montaSelect("upload2"); ?></td>
    <td height="30"><input type="file" value="Procurar" name="arquivo2" id="arquivo2"></td>
  </tr>
  <tr>
    <td align="center" valign="middle">3</td>
    <td height="30">&nbsp;&nbsp;
      <?php $select = new upload(); $select -> montaSelect("upload3"); ?></td>
    <td height="30"><input type="file" value="Procurar" name="arquivo3" id="arquivo3"></td>
  </tr>
  <tr>
    <td align="center" valign="middle">4</td>
    <td height="30">&nbsp;&nbsp;
      <?php $select = new upload(); $select -> montaSelect("upload4"); ?></td>
    <td height="30"><input type="file" value="Procurar" name="arquivo4" id="arquivo4"></td>
  </tr>
  <tr>
    <td align="center" valign="middle">5</td>
    <td height="30">&nbsp;&nbsp;
      <?php $select = new upload(); $select -> montaSelect("upload5"); ?></td>
    <td height="30"><input type="file" value="Procurar" name="arquivo5" id="arquivo5"></td>
  </tr>
  <tr>
    <td align="center" valign="middle">6</td>
    <td height="30">&nbsp;&nbsp;Outros:&nbsp;<input type="text" name="upload6" id="upload6"></td>
    <td height="30"><input type="file" value="Procurar" name="arquivo6" id="arquivo6"></td>
  </tr>
  <tr>
    <td height="40" colspan="3" align="center" valign="middle" bgcolor="#CCCCCC"><br>
    <div style="color:#F00; font-weight:bold">Atenção, não selecione mais de uma vez o mesmo tipo de documento!</div>
    <br>
    <input type="hidden" name="contratacao" value="<?=$contratacao?>">
    <input type="hidden" name="participante" value="<?=$participante?>">
    <input type="hidden" name="reg" value="<?=$_GET['regiao']?>">
    <input type="hidden" name="pro" value="<?=$_GET['pro']?>">
    <input type="hidden" name="ant" value="<?=$_GET['ant']?>">
    <input type="hidden" name="enviado" value="1">
    <input type="submit" value="Enviar">
    <input type="button" value="Cancelar" onClick="javascript:location.href = 
	<?php if($contratacao != 2){ ?>
    '../ver_bolsista.php?reg=<?=$_GET['regiao']?>&bol=<?=$participante?>&pro=<?=$_GET['pro']?>&#foto'
	<?php } else { ?>
    '../rh/ver_clt.php?reg=<?=$_GET['regiao']?>&clt=<?=$participante?>&ant=<?=$_GET['ant']?>&pro=<?=$_GET['pro']?>&#foto'
	<?php } ?>
    ">
    </td>
    </tr>
</table>
</form>

<script>
function validaForm(){
	
	d = document.form1;
	
	if (d.upload1.value == "0" && d.arquivo1.value == "" || d.arquivo1.value != "" && d.upload1.value == "0" || d.arquivo1.value == "" && d.upload1.value != "0"){
		alert("ATENÇÃO ARQUIVO 1 FALTANDO INFORMAÇÃO!");
		d.upload1.focus();
		return false;
	}
	
	if (d.arquivo2.value != "" && d.upload2.value == "0" || d.arquivo2.value == "" && d.upload2.value != "0"){
		alert("ATENÇÃO ARQUIVO 2 FALTANDO INFORMAÇÃO!");
		d.upload2.focus();
		return false;
	}
	
	if (d.arquivo3.value != "" && d.upload3.value == "0" || d.arquivo3.value == "" && d.upload3.value != "0"){
		alert("ATENÇÃO ARQUIVO 3 FALTANDO INFORMAÇÃO!");
		d.upload3.focus();
		return false;
	}
	
	if (d.arquivo4.value != "" && d.upload4.value == "0" || d.arquivo4.value == "" && d.upload4.value != "0"){
		alert("ATENÇÃO ARQUIVO 4 FALTANDO INFORMAÇÃO!");
		d.upload4.focus();
		return false;
	}
	
	if (d.arquivo5.value != "" && d.upload5.value == "0" || d.arquivo5.value == "" && d.upload5.value != "0"){
		alert("ATENÇÃO ARQUIVO 5 FALTANDO INFORMAÇÃO!");
		d.upload5.focus();
		return false;
	}
	
	if (d.arquivo6.value != "" && d.upload6.value == "" || d.arquivo6.value == "" && d.upload6.value != ""){
		alert("ATENÇÃO ARQUIVO 6 FALTANDO INFORMAÇÃO!");
		d.upload6.focus();
		return false;
	}

return true;   
}
</script>

<br>
</body>
</html>
<?php
}elseif($_REQUEST['enviado'] == 1){

$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
$diretorio_padrao .= "intranet/documentos/";

$contratacao = $_REQUEST['contratacao'];
$participante = $_REQUEST['participante'];

if($contratacao != 2){
	$RE = mysql_query("SELECT * FROM autonomo WHERE id_autonomo = '$participante'");
	$Row = mysql_fetch_array($RE);
}else{
	$RE = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$participante'");
	$Row = mysql_fetch_array($RE);
}
	
$regiao = sprintf("%03d", $Row['id_regiao']);
$projeto = sprintf("%03d", $Row['id_projeto']);

$Dir = $regiao."/".$projeto."/";					//RESOLVENDO O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
$novoDir = $contratacao."_".$participante;			//RESOLVENDO O NOME DA PASTA DO USUARIO

$DirCompleto = $diretorio_padrao.$Dir.$novoDir;		//RESULTADO DO DIRETÓRIO FINAL A SER CRIADO

if(!is_dir($DirCompleto)) {							//VERIFICANDO SE O DIRETÓRIO EXISTE
	mkdir($DirCompleto, 0777);						//CRIANDO O NOVO DIRETORIO
}
chmod($DirCompleto, 0777);							//ALTERANDO AS PREFERENCIAS DA PASTA PARA PODER FAZER UPLOAD PARA A MESMA


//------------------------------ INICIANDO OS UPLOADS -----------------------------------------------

if($_REQUEST['upload1'] != "0"){					//FAZENDO UPLOAD DO PRIMEIRO ARQUIVO
	$TipoArquivo = $_REQUEST['upload1'];
	$arquivo = isset($_FILES['arquivo1']) ? $_FILES['arquivo1'] : FALSE;
	
	//INICIO ----------------------- VERIFICANDO O TIPO DE ARQUIVO
	if($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/jpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/pjpeg") {
	echo $MsgErro;
	echo "<br>ERRO NO ARQUIVO 1: ".$arquivo['name']." (".$arquivo['type'].")";
	exit; 
	}else{ //AQUI É UM DOS ARQUIVOS MENCIONADOS ACIMA

	$arr_basename = explode(".",$arquivo['name']); 
	$NumPedacos = count($arr_basename);
	$ultimoPedaco = $NumPedacos -1;
	$file_type = $arr_basename[$ultimoPedaco]; 
	   
		if($file_type == "gif"){
			$tipo_name =".gif"; 
		}elseif($file_type == "jpg" or $file_typ == "jpeg"){
			$tipo_name =".jpg"; 
		}elseif($file_type == "png") { 
			$tipo_name =".png"; 
		} 
		
	// Resolvendo o nome e para onde o arquivo será movido
	$NomeArquivo = $contratacao."_".$participante."_".$TipoArquivo.$tipo_name;
	$NomeARQ = $DirCompleto."/".$NomeArquivo;
	
	move_uploaded_file($arquivo['tmp_name'], $NomeARQ) or die ("Erro ao enviar o Arquivo<br>");
	}// FINAL DO TIPO DE ARQUIVO E DO UPLOAD
}
unset($NomeARQ);
unset($arquivo);
unset($TipoArquivo);
// ------------------------ FIM O UPLOAD DO ARQUIVO 1 ----------------------------------------------------

// ------------------------ INI O UPLOAD DO ARQUIVO 2 ----------------------------------------------------
if($_REQUEST['upload2'] != "0"){					//FAZENDO UPLOAD DO 2 ARQUIVO
	$TipoArquivo = $_REQUEST['upload2'];
	$arquivo = isset($_FILES['arquivo2']) ? $_FILES['arquivo2'] : FALSE;
	
	//INICIO ----------------------- VERIFICANDO O TIPO DE ARQUIVO
	if($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/jpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/pjpeg") {
	echo $MsgErro;
	echo "<br>ERRO NO ARQUIVO 2: ".$arquivo['name']." (".$arquivo['type'].")";
	exit; 
	}else{ //AQUI É UM DOS ARQUIVOS MENCIONADOS ACIMA

	$arr_basename = explode(".",$arquivo['name']); 
	$NumPedacos = count($arr_basename);
	$ultimoPedaco = $NumPedacos -1;
	$file_type = $arr_basename[$ultimoPedaco]; 
	   
		if($file_type == "gif"){
			$tipo_name =".gif"; 
		}elseif($file_type == "jpg" or $file_typ == "jpeg"){
			$tipo_name =".jpg"; 
		}elseif($file_type == "png") { 
			$tipo_name =".png"; 
		} 
		
	// Resolvendo o nome e para onde o arquivo será movido
	$NomeArquivo = $contratacao."_".$participante."_".$TipoArquivo.$tipo_name;
	$NomeARQ = $DirCompleto."/".$NomeArquivo;
	
	move_uploaded_file($arquivo['tmp_name'], $NomeARQ) or die ("Erro ao enviar o Arquivo<br>");
	}// FINAL DO TIPO DE ARQUIVO E DO UPLOAD
}
unset($NomeARQ);
unset($arquivo);
unset($TipoArquivo);
// ------------------------ FIM O UPLOAD DO ARQUIVO 2 ----------------------------------------------------

// ------------------------ INI O UPLOAD DO ARQUIVO 3 ----------------------------------------------------
if($_REQUEST['upload3'] != "0"){					//FAZENDO UPLOAD DO 3 ARQUIVO
	$TipoArquivo = $_REQUEST['upload3'];
	$arquivo = isset($_FILES['arquivo3']) ? $_FILES['arquivo3'] : FALSE;
	
	//INICIO ----------------------- VERIFICANDO O TIPO DE ARQUIVO
	if($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/jpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/pjpeg") {
	echo $MsgErro;
	echo "<br>ERRO NO ARQUIVO 3: ".$arquivo['name']." (".$arquivo['type'].")";
	exit; 
	}else{ //AQUI É UM DOS ARQUIVOS MENCIONADOS ACIMA

	$arr_basename = explode(".",$arquivo['name']); 
	$NumPedacos = count($arr_basename);
	$ultimoPedaco = $NumPedacos -1;
	$file_type = $arr_basename[$ultimoPedaco]; 
	   
		if($file_type == "gif"){
			$tipo_name =".gif"; 
		}elseif($file_type == "jpg" or $file_typ == "jpeg"){
			$tipo_name =".jpg"; 
		}elseif($file_type == "png") { 
			$tipo_name =".png"; 
		} 
		
	// Resolvendo o nome e para onde o arquivo será movido
	$NomeArquivo = $contratacao."_".$participante."_".$TipoArquivo.$tipo_name;
	$NomeARQ = $DirCompleto."/".$NomeArquivo;
	
	move_uploaded_file($arquivo['tmp_name'], $NomeARQ) or die ("Erro ao enviar o Arquivo<br>");
	}// FINAL DO TIPO DE ARQUIVO E DO UPLOAD
}
unset($NomeARQ);
unset($arquivo);
unset($TipoArquivo);
// ------------------------ FIM O UPLOAD DO ARQUIVO 3 ----------------------------------------------------

// ------------------------ INI O UPLOAD DO ARQUIVO 4 ----------------------------------------------------
if($_REQUEST['upload4'] != "0"){					//FAZENDO UPLOAD DO 4 ARQUIVO
	$TipoArquivo = $_REQUEST['upload4'];
	$arquivo = isset($_FILES['arquivo4']) ? $_FILES['arquivo4'] : FALSE;
	
	//INICIO ----------------------- VERIFICANDO O TIPO DE ARQUIVO
	if($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/jpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/pjpeg") {
	echo $MsgErro;
	echo "<br>ERRO NO ARQUIVO 4: ".$arquivo['name']." (".$arquivo['type'].")";
	exit; 
	}else{ //AQUI É UM DOS ARQUIVOS MENCIONADOS ACIMA

	$arr_basename = explode(".",$arquivo['name']); 
	$NumPedacos = count($arr_basename);
	$ultimoPedaco = $NumPedacos -1;
	$file_type = $arr_basename[$ultimoPedaco]; 
	   
		if($file_type == "gif"){
			$tipo_name =".gif"; 
		}elseif($file_type == "jpg" or $file_typ == "jpeg"){
			$tipo_name =".jpg"; 
		}elseif($file_type == "png") { 
			$tipo_name =".png"; 
		} 
		
	// Resolvendo o nome e para onde o arquivo será movido
	$NomeArquivo = $contratacao."_".$participante."_".$TipoArquivo.$tipo_name;
	$NomeARQ = $DirCompleto."/".$NomeArquivo;
	
	move_uploaded_file($arquivo['tmp_name'], $NomeARQ) or die ("Erro ao enviar o Arquivo<br>");
	}// FINAL DO TIPO DE ARQUIVO E DO UPLOAD
}
unset($NomeARQ);
unset($arquivo);
unset($TipoArquivo);
// ------------------------ FIM O UPLOAD DO ARQUIVO 4 ----------------------------------------------------

// ------------------------ INI O UPLOAD DO ARQUIVO 5 ----------------------------------------------------
if($_REQUEST['upload5'] != "0"){					//FAZENDO UPLOAD DO 5 ARQUIVO
	$TipoArquivo = $_REQUEST['upload5'];
	$arquivo = isset($_FILES['arquivo5']) ? $_FILES['arquivo5'] : FALSE;
	
	//INICIO ----------------------- VERIFICANDO O TIPO DE ARQUIVO
	if($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/jpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/pjpeg") {
	echo $MsgErro;
	echo "<br>ERRO NO ARQUIVO 5: ".$arquivo['name']." (".$arquivo['type'].")";
	exit; 
	}else{ //AQUI É UM DOS ARQUIVOS MENCIONADOS ACIMA

	$arr_basename = explode(".",$arquivo['name']); 
	$NumPedacos = count($arr_basename);
	$ultimoPedaco = $NumPedacos -1;
	$file_type = $arr_basename[$ultimoPedaco]; 
	   
		if($file_type == "gif"){
			$tipo_name =".gif"; 
		}elseif($file_type == "jpg" or $file_typ == "jpeg"){
			$tipo_name =".jpg"; 
		}elseif($file_type == "png") { 
			$tipo_name =".png"; 
		} 
		
	// Resolvendo o nome e para onde o arquivo será movido
	$NomeArquivo = $contratacao."_".$participante."_".$TipoArquivo.$tipo_name;
	$NomeARQ = $DirCompleto."/".$NomeArquivo;
	
	move_uploaded_file($arquivo['tmp_name'], $NomeARQ) or die ("Erro ao enviar o Arquivo<br>");
	}// FINAL DO TIPO DE ARQUIVO E DO UPLOAD
}
unset($NomeARQ);
unset($arquivo);
unset($TipoArquivo);
// ------------------------ FIM O UPLOAD DO ARQUIVO 5 ----------------------------------------------------


// ------------------------ INI O UPLOAD DO ARQUIVO 4 ----------------------------------------------------
if($_REQUEST['upload6'] != ""){					//FAZENDO UPLOAD DO 6 ARQUIVO
	$TipoArquivo = $_REQUEST['upload6'];
	$arquivo = isset($_FILES['arquivo6']) ? $_FILES['arquivo6'] : FALSE;
	
	//INICIO ----------------------- VERIFICANDO O TIPO DE ARQUIVO
	if($arquivo['type'] != "image/x-png" && $arquivo['type'] != "image/jpeg" && $arquivo['type'] != "image/gif" && $arquivo['type'] != "image/pjpeg") {
	echo $MsgErro;
	echo "<br>ERRO NO ARQUIVO 6: ".$arquivo['name']." (".$arquivo['type'].")";
	exit; 
	}else{ //AQUI É UM DOS ARQUIVOS MENCIONADOS ACIMA

	$arr_basename = explode(".",$arquivo['name']); 
	$NumPedacos = count($arr_basename);
	$ultimoPedaco = $NumPedacos -1;
	$file_type = $arr_basename[$ultimoPedaco]; 
	   
		if($file_type == "gif"){
			$tipo_name =".gif"; 
		}elseif($file_type == "jpg" or $file_typ == "jpeg"){
			$tipo_name =".jpg"; 
		}elseif($file_type == "png") { 
			$tipo_name =".png"; 
		} 
		
	// Resolvendo o nome e para onde o arquivo será movido
	$NomeArquivo = $contratacao."_".$participante."_".$TipoArquivo.$tipo_name;
	$NomeARQ = $DirCompleto."/".$NomeArquivo;
	
	move_uploaded_file($arquivo['tmp_name'], $NomeARQ) or die ("Erro ao enviar o Arquivo<br>");
	}// FINAL DO TIPO DE ARQUIVO E DO UPLOAD
}
unset($NomeARQ);
unset($arquivo);
unset($TipoArquivo);
// ------------------------ FIM O UPLOAD DO ARQUIVO 6 ----------------------------------------------------

$id_clt = $_GET['clt'];
$id_ant = $_POST['ant'];
$id_pro = $_POST['pro'];
$id_reg = $_POST['reg'];

if($contratacao != 2){
print "<script>location.href = '../ver_bolsista.php?reg=$id_reg&bol=$participante&pro=$id_pro&foto=enviado#foto';</script>";
} else {
print "<script>location.href = '../rh/ver_clt.php?reg=$id_reg&clt=$participante&ant=$id_ant&pro=$id_pro&foto=enviado#foto';</script>";
}

//$novoDir = "004";
//sprintf("%03d", 10);
/*
for($i=1 ; $i <= 24; $i ++){
	$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
	$diretorio_padrao .= "intranet/documentos/";
	
	$REprojetos = mysql_query("SELECT id_projeto FROM projeto WHERE id_regiao = '$i'");
	
	while($RowPro = mysql_fetch_array($REprojetos)){
		$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
		$diretorio_padrao .= "intranet/documentos/";
		
		$DirReg = sprintf("%03d", $i);
		$novoDir = sprintf("%03d", $RowPro['0']);
		
		$diretorio_padrao .= $DirReg."/";
		
		if(!is_dir($diretorio_padrao.$novoDir)) {
			mkdir($diretorio_padrao.$novoDir, 0777);
			//echo "mkdir = $diretorio_padrao$novoDir<br>";
		}
		chmod($diretorio_padrao.$novoDir, 0777);
		unset($diretorio_padrao);
	}
	//echo "<br>";
}
*/
//echo $diretorio_padrao."<br><br>";
/*
$pasta = "007/";
$dir = $diretorio_padrao.$pasta;

// Abre um diretorio conhecido, e faz a leitura de seu conteudo
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            print "filename: $file : filetype: " . filetype($dir . $file) . "<br>";
        }
        closedir($dh);
    }
}
*/
//echo "Quantidade de arquivos enviados: ".$Qnt;

}else{			//DELETANDO IMAGEM

$arquivo = $_REQUEST['arquivo'];
$voltar = $_REQUEST['voltar'];

$Deteta = new upload();
$Deteta -> DeleteArquivo("$arquivo","$voltar");


}
?>