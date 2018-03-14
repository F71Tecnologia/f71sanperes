<?php

class  SupportValeClass {

    static function factoryVale($tipoVale) {

        $dao = NULL;
        
        $sql = "SELECT A.*,B.*, A.`status` AS status_tipo, B.`status` AS status_categoria FROM rh_va_tipos AS A 
                LEFT JOIN rh_va_categorias AS B ON(A.id_va_categoria=B.id_va_categoria) 
                WHERE A.`status`=1 AND B.`status`=1 AND A.id_va_tipos=$tipoVale;";
        
//        echo $sql;
        
        $rowDao = mysql_fetch_assoc(mysql_query($sql));
        
        if(!is_file("classes/$rowDao[dao].php")){
            exit("Classe não encontrada! Configure o DAO correto para o tipo.");
        }
        include_once("classes/$rowDao[dao].php");
        
        $class = "$rowDao[dao]";
        
        return new $class($rowDao);
        
    }
    static function formData(){
        $dados = array();
        $find = array('_0', '_1','_2','_3','_4');
        foreach($_POST['form_data'] as $k=>$v){
            $dados[str_replace($find,'',$k)] = $v;
        }
        return $dados;
    }
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
    static function getInfoPedidoByRelacao($relacaoFuncionarios){
        $arr_k = array_keys($relacaoFuncionarios);
        $data['id_projeto'] = $relacaoFuncionarios[$arr_k[0]]['id_projeto'];
        $data['projeto'] = $relacaoFuncionarios[$arr_k[0]]['projeto'];
        $data['id_va_pedido'] = $relacaoFuncionarios[$arr_k[0]]['id_va_pedido'];
        $data['mes'] = $relacaoFuncionarios[$arr_k[0]]['mes'];
        $data['ano'] = $relacaoFuncionarios[$arr_k[0]]['ano'];
        $data['nome_empresa'] = $relacaoFuncionarios[$arr_k[0]]['nome_empresa'];
        $data['razao_social'] = $relacaoFuncionarios[$arr_k[0]]['razao_social'];
        $data['cnpj'] = $relacaoFuncionarios[$arr_k[0]]['cnpj'];
        $data['logradouro'] = $relacaoFuncionarios[$arr_k[0]]['logradouro'];
        $data['numero'] = $relacaoFuncionarios[$arr_k[0]]['numero'];
        $data['complemento'] = $relacaoFuncionarios[$arr_k[0]]['complemento'];
        $data['bairro'] = $relacaoFuncionarios[$arr_k[0]]['bairro'];
        $data['cidade'] = $relacaoFuncionarios[$arr_k[0]]['cidade'];
        $data['uf'] = $relacaoFuncionarios[$arr_k[0]]['uf'];
        $data['cep'] = $relacaoFuncionarios[$arr_k[0]]['cep'];
        $data['descricao_tp_logradouro'] = $relacaoFuncionarios[$arr_k[0]]['descricao_tp_logradouro'];
        unset($arr_k);
        return $data;
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
