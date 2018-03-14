<?php
include "../../conn.php";

$id_advertencia = $_REQUEST['id_doc'];
$id_clt = $_REQUEST['clt'];

$qr = mysql_query("DELETE FROM rh_doc_status WHERE id_doc = {$id_advertencia}");

$nome_doc = $id_advertencia.'_'.$id_clt;
$caminho = 'arquivos_advertencia/'.$nome_doc.'.pdf';
if (file_exists($caminho) and !empty($nome_doc)){
    unlink($caminho);

}
?>
<script type="text/javascript">
alert("Removido com sucesso!");
window.history.go(-1);
</script>