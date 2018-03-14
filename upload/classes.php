<?php
//CLASSE upload 29.07.2009
class upload{

//FUNÇÃO PUBLICA PARA CONEXAO E SELECT DE TODOS OS TIPOS DE UPLOADS
public function __construct() {
	//include "../conn.php";
	$id_user = $_COOKIE['logado'];
	
	$this->reup = mysql_query("SELECT * FROM upload")or die(mysql_error());
	
}

function montaSelect($nome){
	echo "<select id='$nome' name='$nome'>\n";
	echo "<option value='0'>--- Selecione ---</option>\n";
	while ($RowUP = mysql_fetch_array($this->reup)){
		echo "<option value='$RowUP[0]'>";
		echo "$RowUP[0] - $RowUP[arquivo]";
		echo "</option>\n";
	}
		
	echo "</select>\n";		
}

function mostraTipo($id){
	$RE = mysql_query("SELECT * FROM upload WHERE id_upload = '$id'")or die(mysql_error());
	$Row = mysql_fetch_array($RE);
	return $Row['arquivo'];
}

function DeleteArquivo($arquivo){
	
	$participante_del = $_REQUEST['participante'];
    $contratacao_del = $_REQUEST['contratacao'];
    $id_ant_del = $_REQUEST['ant'];
    $id_pro_del = $_REQUEST['pro'];
    $id_reg_del = $_REQUEST['regiao'];
	
	$diretorio_padrao = $_SERVER["DOCUMENT_ROOT"]."/";
	$diretorio_padrao .= "intranet/";
	
	$caminho = $diretorio_padrao.$arquivo;
	
	unlink ($caminho);
	if($contratacao_del != 2){
    $retorno = "<script>location.href = '../ver_bolsista.php?reg=$id_reg_del&bol=$participante_del&pro=$id_pro_del&foto=deletado#ancora_documentos';</script>";
    } else {
    $retorno = "<script>location.href = '../rh/ver_clt.php?reg=$id_reg_del&clt=$participante_del&ant=$id_ant_del&pro=$id_pro_del&foto=deletado#ancora_documentos';</script>";
    }
	
	
	echo $retorno;
}


}
?>