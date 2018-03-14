<?php

include "../../../conn.php";
include "../../../classes/uploadfile.php";
require("../../../wfunction.php");
require("../../../classes/SaidaClass.php");
require("../../../classes/LogClass.php");
include "../../../funcoes.php";
include("../../../classes/global.php");

//$charset = mysql_set_charset('utf8');

$qr_func = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_func = mysql_fetch_assoc($qr_func);

$log = new Log();

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$grupo = isset($_REQUEST['grupo']) ? $_REQUEST['grupo'] : NULL;
$regiao = isset($_REQUEST['regiao']) ? $_REQUEST['regiao'] : NULL;
$projeto = isset($_REQUEST['projeto']) ? $_REQUEST['projeto'] : NULL;
$master = isset($_REQUEST['id_master']) ? $_REQUEST['id_master'] : NULL;
$subgrupo = isset($_REQUEST['subgrupo']) ? $_REQUEST['subgrupo'] : NULL;
$opt = isset($_REQUEST['opt']) ? $_REQUEST['opt'] : NULL;
$usuario = carregaUsuario();
switch ($action) {
  
    
    case 'upload_anexo':
//        print_array($_REQUEST);print_r($_FILES);exit;
       $id_orcamento = $_REQUEST['id_orcamento'];
        
//        $diretorio = "../comprovantes/saida";
        $diretorio = "../orcamento_pdf";
        
       
            
            $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
            $upload->arquivo($_FILES[file]);
            $upload->verificaFile();
            
                  

                
        
                    $insert = "UPDATE item_orcamento SET anexo = '/orcamento_pdf/$id_orcamento.$upload->extensao' WHERE id_orcamento = $id_orcamento;";

               
//                $insert = "INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida[$i]','.$upload->extensao');";
//                $tipo_anexo = 'Anexo';
                mysql_query($insert)or die(mysql_error());
                $id = mysql_insert_id();

                $upload->NomeiaFile("$id_orcamento");
                $upload->Envia();
//                $log->gravaLog('Anexo Saída', "$tipo_anexo $id inserido na Saída $id_saida[$i]");
        
    break;

    
    default:
        echo 'action: ' . $action;
    break;
}