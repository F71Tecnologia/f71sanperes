<?php

class  SupportVT {

    static function limpaMascaraMoney($valor){
        return isset($valor) ? str_replace('R$ ', '', str_replace(',', '.', $valor)) : '';
    }
    
    static function includeFileAjax($file,$data){
        
        ob_start();
        
        foreach($data as $k=>$v){
            $$k = $v;
        }
        
        include $file;
        $out1 = ob_get_contents();
        ob_end_clean();
        return utf8_encode($out1);
    }
    static function arrayToData(Array $arr, $encode=TRUE){
        
        if($encode){
            $n = array();
            foreach($arr as $k=>$v){
                $n[$k] = utf8_encode($v);
            }
            $arr = $n;
        }
        
        return str_replace('"', '\'', json_encode($arr));
        
    }
    static function dateBrToDb($date){
        return implode('-',array_reverse(explode('/', $date)));;
    }
    static function debugPrint($var, $tipo=1){
        ob_start();
        if($tipo==1){
        echo '<pre>';
            print_r($var);
        echo '</pre>';
        }elseif($tipo==2){
        echo '<pre>';
            var_dump($var);
        echo '</pre>';
        }else{
            echo($var);
        }
        $out1 = ob_get_contents();
        ob_end_clean();
        return utf8_encode($out1);
    }
}
