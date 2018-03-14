<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include ("../classes/uploadfile.php");
include ("../wfunction.php");
include ("../conn.php");
require_once ('../classes/LogClass.php');

$log = new Log;

if(isset($_REQUEST['enviar'])) {
    
    $ext = explode('.',$file);
    $ext2 = count($ext);
    $ext = $ext[$ext2 - 1];
    
    $type = $_POST['evDocType'];
    $idDoc = $_POST['evDocId'];
    $idClt = $_POST['idClt'];
    $regClt = $_POST['regClt'];
    $proClt = $_POST['proClt'];
            
    switch($type) {
        case 1: $typeName = "admissao"; break;
        case 2: 
            $sql = "SELECT nome_status FROM rh_eventos WHERE id_evento = $idDoc";
            $query = mysql_query($sql);
            $row = mysql_fetch_array($query);
            $typeName = simplificaString($row[0]);
            break;
        case 3: $typeName = "ferias"; break;
        case 4: $typeName = "rescisao"; break;
    }
    
    $dir = 'documentos/docsClt/' . $idClt . '/' . 'docsEventos';
    
    if(!is_dir($dir)) {
        mkdir($dir,0777,true);
    }
    
    $arrCount = count(explode('.',$_FILES['evDocsFile']['name']));
    $arrCount = $arrCount - 1;
    
    $formato = explode('.',$_FILES['evDocsFile']['name']); 
    
    $nomeArquivo = $idDoc . '_' . $typeName;
    $typeName = $idDoc . '_' . $typeName . '.' . $formato[$arrCount];

    $_FILES['evDocsFile']['name'] = $typeName;
    $uri = 'intranet/rh/' . $dir;
    $dir .= '/' . $_FILES['evDocsFile']['name'];
    
    $usuario = $_COOKIE['logado'];
    
    if(move_uploaded_file($_FILES['evDocsFile']['tmp_name'], $dir)) {
        
        $sql = "SELECT * FROM eventos_anexos WHERE id_tipo_evento = $type AND id_evento = $idDoc AND status = 1";
        $query = mysql_query($sql);
        $rows = mysql_num_rows($query);
        if($rows < 1){
            $sql = "INSERT INTO eventos_anexos (id_clt, id_tipo_evento, id_evento, doc_name, doc_type, caminho, user_cad) VALUES ('$idClt', '$type', '$idDoc', '$nomeArquivo', '$formato[$arrCount]', '$uri', '$usuario')";
            $query = mysql_query($sql);
            $ultimo_id = mysql_insert_id();
            $log->gravaLog('Anexo de Evento', "Documento $ultimo_id anexado para o clt {$idClt}.");

            header("Location: ver_clt.php?reg=$regClt&clt=$idClt&ant=0&pro=$proClt");
        }
    }
    
    exit;
}

if(isset($_REQUEST['deletar'])) {
    
    $id = $_REQUEST['id'];
    $type = $_REQUEST['type'];
    $idAnexo = $_REQUEST['idAnexo'];
    
    $sql = "SELECT * FROM eventos_anexos WHERE id_tipo_evento = $type AND id_evento = $id AND status = 1";
    
    $query = mysql_query($sql);
    $arr = mysql_fetch_assoc($query);
    
    unlink(ROOT_OLD_DIR . $arr['caminho'] . '/' . $arr['doc_name'] . '.' . $arr['doc_type']);
    
    $sql = "UPDATE eventos_anexos SET status = 0 WHERE id_tipo_evento = $type AND id_evento = $id";
    $query = mysql_query($sql);
    $row = mysql_affected_rows();
    $log->gravaLog('Anexo de Evento', "Documento $idAnexo excluido com sucesso.");

    echo json_encode($row);
        
    exit;
    
}
