<?php 

include_once("../../../conn.php");
include_once("../classes/PontoClass.php");

$ponto = new Ponto();
$pasta = "../arquivos/"; 
$regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : "";
$projeto = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : "";
$erro = array();
$return = array("status" => 0);

/* formatos de imagem permitidos */ 
$permitidos = array(".csv"); 
if(isset($_FILES)){
    
    $nome_arquivo = $_FILES['arquivo']['name']; 
    
    /* pega a extensão do arquivo */ 
    $ext = strtolower(strrchr($nome_arquivo,".")); 
    //caminho temporário da imagem 
    $tmp = $_FILES['arquivo']['tmp_name']; 
    /* verifica se a extensão está entre as extensões permitidas */ 
    if(!empty($tmp)){
        if(in_array($ext,$permitidos)){ 
            //VALIDANDO CABEÇALHO
            $erros = $ponto->validaArquivo($tmp, $regiao, $projeto);
            //NOVO NOME
            $novo_nome_arquivo = $ponto->unidade . "_" . $ponto->competencia;

            if(count($erros) == 0){
                if(!$ponto->uploadFile($tmp, $pasta . $novo_nome_arquivo)){ 
                    $erro = array("erro" => "Erro ao mover arquivo");
                }else{
                    $return = array("status" => 1);
                }   
            }else{
               $return = array("erro" => $erros, "status" => 0);
            }
        }
    }else{
        $return = array("erro" => array("Selecione um arquivo"), "status" => 0);
    }
}

echo json_encode($return);
exit();

?>