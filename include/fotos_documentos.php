<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>

<link rel="stylesheet" type="text/css" href="<?php if($_POST['tipo_contratacao'] == '2') { echo '../'; } ?>js/shadowbox.css">
<script type="text/javascript" src="<?php if($_POST['tipo_contratacao'] == '2') { echo '../'; } ?>js/shadowbox.js"></script>
<script type="text/javascript">
Shadowbox.init();
</script>
Documento enviado com sucesso!<br>
<?php
include("../conn.php");
include "../upload/classes.php";

$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
$diretorio_padrao .= "intranet/documentos/";

if($_POST['tipo_contratacao'] == '2') {
	$dirInternet = "../documentos/";
} else {
	$dirInternet = "documentos/";
}

$DeldirInternet = "documentos/";

$regiao = sprintf("%03d",$_POST['id_regiao']);
$projeto = sprintf("%03d",$_POST['id_projeto']);

$Dir = $regiao."/".$projeto."/";					//RESOLVENDO O NOME DA PASTA ONDE VAI SER CRIADO A PASTA DO USUARIO
$novoDir = $_POST['tipo_contratacao']."_".$_POST['id_participante'];			//RESOLVENDO O NOME DA PASTA DO USUARIO
$DirCom = $Dir.$novoDir;

$dir = $diretorio_padrao.$DirCom;
$dirInternet .= $DirCom;
$DeldirInternet .= $DirCom;

// Abre um diretorio conhecido, e faz a leitura de seu conteudo
if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if($file == "." or $file == ".."){ }else{
				
				$tipoArquivo = explode("_",$file);
				$tipoArquivo = explode(".",$tipoArquivo[2]);
				
				$select = new upload();
				$TIPO = $select -> mostraTipo($tipoArquivo[0]);
				
				$DirFinal = $dirInternet."/".$file;
				$DelDirFinal = $DeldirInternet."/".$file;
				$ja_documentos[] = $file;
				
				echo "<div class='documentos'>";
				echo "<a class='documento' href='".$DirFinal."'  rel='shadowbox[documentos]' title='Visualizar $TIPO'>";
				echo "<img src='".$DirFinal."' width='75' height='75' border='0' alt='$TIPO'></a>";
				echo "<a href='#' onClick=\"Confirm('$DirFinal')\" title='Deletar $TIPO'>deletar</a>";
				echo "</div>";
				
			}
        }
        closedir($dh);
    }
}
?>