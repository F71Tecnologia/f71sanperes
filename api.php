<?php

require_once('conn.php');
require_once('classes/ApiClass.php');
require_once('wfunction.php');
define('BASE_PATH', dirname(__FILE__));
define('WEBSERVICE', true);
error_reporting(0);

$api = new ApiClass();

$return = array();
$method = $api->post('method',null,true);

if(validate($method)){
    
    /**
     * RECUPERA O METODO E VERIFICA 
     * SE EXISTE E SE PODE UTILIZAR
     */
    $apiMethod = $api->getMethod($method);
    $param = print_r($_REQUEST, true);
    if($apiMethod !== false){
        
        $classe = $apiMethod['classe'];
        $filename = "classes/{$apiMethod['arquivo']}";
             
        if(is_file($filename)){
            
            include_once($filename);
            $ojb = new $classe();
           
            $methodClass = $apiMethod['method'];

            if(method_exists( $ojb , $methodClass )){
                $ok = 1;
                //NORMALMENTE SERÁ 0
                if($apiMethod['parametros'] == 0){
                    $rs = $ojb->$methodClass();
                }else{
                    $inofParametros = $api->trataParametrosMethod($apiMethod['id_api']);
                    //validação para saber se os parametros foram passados corretamente
                    if($inofParametros === false){
                        $ok = 0;
                        $return['status'] = $ok;
                        $return['msg'] = "Erro 010 - Erro na passagem dos parametros - Param: {$apiMethod['parametros']}";
                    }else{
                        eval('$rs = $ojb->$methodClass('.$inofParametros.');');
                    }
                }
                
                $return['status'] = $ok;
                $return['dados'] = $rs;
            }else{
                $return['status'] = "0";
                $return['msg'] = utf8_encode("Erro 008 - Method não encontrado");
            }

        }else{
            $return['status'] = "0";
            $return['msg'] = utf8_encode("Erro 009 - Classe não encontrada");
        }
        
    }else{
        
        $return['status'] = "0";
        $return['msg'] = utf8_encode("Erro 007 - Method Required não encontrado");
        
    }
    
    /**
     * LOG DE REQUISIÇÃO
     * TABELA: api_log -> criaLog(array)
     */
    $arrayRequisicaoAPI = array(
        "ip" => $_SERVER["REMOTE_ADDR"], 
        "method" => $method, 
        "erro" => isset($return['msg'])?$return['msg']:'',
        "parametros" => $param );
    $api->criaLog($arrayRequisicaoAPI);
    
    
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    
    
    if(isset($_REQUEST['type']) && !empty($_REQUEST['type']) && $_REQUEST['type'] == "xml"){
        
        header("Content-type: text/xml charset=utf-8"); 
        flush();
        
        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?> <root></root>");
        array_to_xml($return,$xml);
        echo $xml->asXML();
        
    }else{
        
        header("Content-Type: application/json charset=utf-8"); //charset=utf-8
        flush();
        
        echo "[".  json_encode($return)."]";
        
    }
    
    exit;
}