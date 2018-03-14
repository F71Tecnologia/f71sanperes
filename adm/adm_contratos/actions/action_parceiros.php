<?php

include "../../../conn.php";
require("../../../funcoes.php");
require("../../../wfunction.php");
include("../../../classes/LogClass.php");
include("../../../classes/uploadfile.php");
require("../../../classes/ParceiroClass2.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();

$log = new Log();
$objParceiro = new ParceiroClass();
$objParceiro->setDefault();

if(!empty($_REQUEST['id_parceiro'])){
    $objParceiro->setIdParceiro($_REQUEST['id_parceiro']);
    if($objParceiro->select()){
        $objParceiro->getRow();
    } else {
        echo $objParceiro->getError();
        exit;
    }
}

switch ($action) {
    
    case 'cadastrar_parceiro' :
        
        $objParceiro->setParceiroNome($_REQUEST['parceiro_nome']);
        $objParceiro->setParceiroCnpj($_REQUEST['parceiro_cnpj']);
        $objParceiro->setParceiroCcm($_REQUEST['parceiro_ccm']);
        $objParceiro->setParceiroIe($_REQUEST['parceiro_ie']);
        $objParceiro->setParceiroEndereco($_REQUEST['parceiro_endereco']);
        $objParceiro->setParceiroEstado($_REQUEST['parceiro_estado']);
        $objParceiro->setParceiroBairro($_REQUEST['parceiro_bairro']);
        $objParceiro->setParceiroCidade($_REQUEST['parceiro_cidade']);
        $objParceiro->setParceiroContato($_REQUEST['parceiro_contato']);
        $objParceiro->setParceiroCpf($_REQUEST['parceiro_cpf']);
        $objParceiro->setParceiroTelefone($_REQUEST['parceiro_telefone']);
        $objParceiro->setParceiroCelular($_REQUEST['parceiro_celular']);
        $objParceiro->setParceiroEmail($_REQUEST['parceiro_email']);
        $objParceiro->setParceiroBanco($_REQUEST['parceiro_banco']);
        $objParceiro->setParceiroAgencia($_REQUEST['parceiro_agencia']);
        $objParceiro->setParceiroConta($_REQUEST['parceiro_conta']);
        
        $objParceiro->setIdRegiao($usuario['id_regiao']);
        $objParceiro->setParceiroStatus(1);
        $objParceiro->setParceiroAutor($usuario['id_funcionario']);
        $objParceiro->setParceiroData(date('Y-m-d H:i:s'));
        
        $objParceiro->insert();
        
        $log->gravaLog('Cadastro de Parceiro', 'Parceiro '.$objParceiro->getIdParceiro().' cadastrado');
        echo $objParceiro->getIdParceiro();
        
    break;
    
    case 'editar_parceiro' :
        
        $objParceiro->setParceiroNome($_REQUEST['parceiro_nome']);
        $objParceiro->setParceiroCnpj($_REQUEST['parceiro_cnpj']);
        $objParceiro->setParceiroCcm($_REQUEST['parceiro_ccm']);
        $objParceiro->setParceiroIe($_REQUEST['parceiro_ie']);
        $objParceiro->setParceiroEndereco($_REQUEST['parceiro_endereco']);
        $objParceiro->setParceiroEstado($_REQUEST['parceiro_estado']);
        $objParceiro->setParceiroBairro($_REQUEST['parceiro_bairro']);
        $objParceiro->setParceiroCidade($_REQUEST['parceiro_cidade']);
        $objParceiro->setParceiroContato($_REQUEST['parceiro_contato']);
        $objParceiro->setParceiroCpf($_REQUEST['parceiro_cpf']);
        $objParceiro->setParceiroTelefone($_REQUEST['parceiro_telefone']);
        $objParceiro->setParceiroCelular($_REQUEST['parceiro_celular']);
        $objParceiro->setParceiroEmail($_REQUEST['parceiro_email']);
        $objParceiro->setParceiroBanco($_REQUEST['parceiro_banco']);
        $objParceiro->setParceiroAgencia($_REQUEST['parceiro_agencia']);
        $objParceiro->setParceiroConta($_REQUEST['parceiro_conta']);
        
        $objParceiro->setParceiroIdAtualizacao($usuario['id_funcionario']);
        $objParceiro->setParceiroDataAtualizacao(date('Y-m-d H:i:s'));
        //print_array($objParceiro->parceiro_save);
        $objParceiro->update();
        
        $log->gravaLog('Edição de Parceiro', 'Parceiro '.$objParceiro->getIdParceiro().' editado');
        echo $objParceiro->getIdParceiro();

    break;
    
    case 'remover_logo' :
        $caminho = "../../../adm/adm_parceiros/logo/".$objParceiro->getParceiroLogo();
        //unlink($caminho);

        $objParceiro->setParceiroLogo("");
        $objParceiro->update();
        
        $log->gravaLog('Remoção de Logo', 'Remoção de Logo Parceiro: ' . $objParceiro->getIdParceiro());
    break;
    
    case 'excluir_parceiro' :
        $objParceiro->setParceiroStatus(0);
        $objParceiro->update();
        
        $log->gravaLog('Exclusão de Parceiro', 'Exclusão do Parceiro: ' . $objParceiro->getIdParceiro());
    break;
    
    case 'upload_anexo' : 
        
        $diretorio = "/intranet/adm/adm_parceiros/logo";
        $diretorio = "../../../adm/adm_parceiros/logo";
        
        $upload = new UploadFile($diretorio,array('jpg','gif','png','pdf','JPG','GIF','PNG','PDF'));
        $upload->arquivo($_FILES[file]);
        $upload->verificaFile();
        
        $nome_arquivo = uniqid('logo_');
        
        $objParceiro->setParceiroLogo($nome_arquivo.'.'.$upload->extensao);
        $objParceiro->update();
        
        $upload->NomeiaFile($nome_arquivo);
        
        $upload->Envia();
//        print_array($upload);exit;
          
        $log->gravaLog('Anexo de Logo', 'Anexo de Logo Parceiro: ' . $objParceiro->getIdParceiro());
        echo $diretorio.'/'.$nome_arquivo.'.'.$upload->extensao; 
        exit;
    break;
    
    default:
        echo 'action: ' . $action;
    break;
}