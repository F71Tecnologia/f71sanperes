<?php

include("conn.php");
include("wfunction.php");
$return = array();

//echo "<pre>";
// function defination to convert array to xml
function array_to_xml($student_info, &$xml_student_info) {
    foreach ($student_info as $key => $value) {

        if (is_array($value)) {
            if (!is_numeric($key)) {
                $subnode = $xml_student_info->addChild("$key");
                array_to_xml($value, $subnode);
            } else {
                $subnode = $xml_student_info->addChild("item$key");
                array_to_xml($value, $subnode);
            }
        } else {
            $xml_student_info->addChild("$key", utf8_encode($value));
        }
    }
}

if (validate($_REQUEST['method'])) {
    $method = $_REQUEST['method'];
    $classe = $_REQUEST['classe'];
    $filename = "classes/{$classe}.php";
    //print_r($_REQUEST);exit;
    if (is_file($filename)) {

        include($filename);
        //CASO O NOME OBJETO É DIFERENTE DO ARQUIVO
        if (isset($_REQUEST['classobj']) && !empty($_REQUEST['classobj'])) {
            $classe = $_REQUEST['classobj'];
        }

        //VERIFICA SE TEM PARAMETROS
        $param = null;
        foreach ($_REQUEST as $k => $val) {
            $pos = strripos($k, "p");
            if ($pos === 0) {
                $param[] = $val;
            }
        }
        //$param = substr($param, 0, -1);
        if (class_exists($classe)) {
            $ojb = new $classe();
        } else {//if (class_exists(str_replace('Class', '', $classe))) {
            $classe = str_replace('Class', '', $classe);
            $ojb = new $classe();
        }
        
//        var_dump($ojb);exit;
        if (method_exists($ojb, $method)) {
//            print_r($param);exit;

            $rs = $ojb->$method($param[0], $param[1]);
            //print_r($rs);exit;
            $return['status'] = "1";
            $return['dados'] = $rs;
        } else {
            $return['status'] = "0";
            $return['msg'] = "Metodo não encontrado: {$method}";
        }
    } else {
        $return['status'] = "0";
        $return['msg'] = "Classe não encontrada: {$classe}";
    }

    if (isset($_REQUEST['type']) && !empty($_REQUEST['type']) && $_REQUEST['type'] == "json") {
        echo json_encode($return);
    } else {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: text/html");
        /* header("Content-Length: " . filesize($filename));
          header("Content-Disposition: attachment; filename={$fname}"); */
        flush();

        $xml = new SimpleXMLElement("<?xml version='1.0' encoding='UTF-8'?> <root></root>");
        array_to_xml($return, $xml);
        echo $xml->asXML();
    }
    exit;
}