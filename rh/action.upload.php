<?php
//if(isset($_REQUEST['userfile'])){
//if(isset($_REQUEST['upload'])){
    
    include '../conn.php';
    
    if(isset($_REQUEST['enviar']) && $_REQUEST['enviar'] == "Enviar Arquivo") {
        
        $id_clt = $_REQUEST['id_clt'];
        $tipoDoc = $_REQUEST['tipoDoc'];

        $sqlNomeDoc = "SELECT * FROM upload WHERE id_upload = $tipoDoc";
        $queryNomeDoc = mysql_query($sqlNomeDoc);
        $arrNomeDoc = mysql_fetch_assoc($queryNomeDoc);

        $nome = $arrNomeDoc['arquivo'];

        $arrCount = count(explode('.',$_FILES['userfile']['name']));
        $arrCount = $arrCount - 1;

        $formato = explode('.',$_FILES['userfile']['name']);

        $nome = strtolower(ereg_replace("[^a-zA-Z0-9_]", "",strtr($nome, "АЮЦБИЙМСТУЗЭГаюцбиймстузэг ", "aaaaeeiooouucAAAAEEIOOOUUC_")));
        $nome = $nome . '.' . $formato[$arrCount];
        
        $_FILES['userfile']['name'] = $nome;
        
        $uploaddir = '../../intranet/rh/documentos/docsClt/'.$id_clt.'/';

        $uri =  "intranet/rh/documentos/docsClt/{$id_clt}/";

        if(!is_dir($uploaddir)) {
            mkdir($uploaddir,0777,true);
        }

        $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            
            $sqlVerif = "SELECT * FROM documento_clt_anexo WHERE id_clt = $id_clt AND id_upload = $tipoDoc";
            $queryVerif = mysql_query($sqlVerif);
            $arrVerif = mysql_fetch_assoc($queryVerif);
            
            if(empty($arrVerif)){
                $sqlUpload = "INSERT INTO documento_clt_anexo 
                                (id_upload, id_clt, anexo_nome, anexo_diretorio, anexo_extensao, ordem, data_cad, user_cad)
                                VALUES
                                ('$tipoDoc', '$id_clt', '$nome', '$uri', '$formato[$arrCount]', '{$arrNomeDoc['ordem']}', NOW(), '{$_COOKIE['logado']}')";
                $queryUpload = mysql_query($sqlUpload);
            } else {
                $sqlUpload = "UPDATE documento_clt_anexo SET anexo_extensao = '$formato[$arrCount]', data_cad = NOW(), user_cad = '{$_COOKIE['logado']}', anexo_status = 1 WHERE id_clt = $id_clt AND id_upload = $tipoDoc";
                $queryUpload = mysql_query($sqlUpload);
            }
 
            header("Location: ver_clt.php?reg={$_REQUEST['id_reg']}&clt={$_REQUEST['id_clt']}&ant={$_REQUEST['id_ant']}&pro={$_REQUEST['id_pro']}");
            
        } else {
            echo "Incapaz de realizar o upload!";
        }

        exit();
    }
    
    if(isset($_REQUEST['deletar']) && $_REQUEST['deletar'] == 'deletar') {
        $anexo = $_REQUEST['anexo'];
        
        $sqlSel = "SELECT anexo_diretorio, anexo_nome FROM documento_clt_anexo WHERE anexo_id = $anexo";
        $querySel = mysql_query($sqlSel);
        $arrSel = mysql_fetch_array($querySel);
        
        $file = ROOT_OLD_DIR .  $arrSel[0] . $arrSel[1];
        unlink($file);
        
        $sql = "UPDATE documento_clt_anexo SET data_cad = NOW(), anexo_status = 0 WHERE anexo_id = $anexo";
        $query = mysql_query($sql);
        $af = mysql_affected_rows();
        
        echo json_encode($af);
    }