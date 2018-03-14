<?php

/**
 * Description of BotoesClass
 *
 * @author Ramon Lima
 */
class ComprasChamados {

    //private $url = "http://f71.com.br/checklist/webservice.php";
    private $url = "";
    
    public function getChamados($tipo=1){
        if($tipo == 1){
            $fields = array(
                'method' => "getListForUser",
                'classe' => "Chamado",
                'p1' => "1"
            );
        }else if($tipo == 2){
            $fields = array(
                'method' => "getListForUser",
                'classe' => "Chamado",
                'p1' => "2"
            );
        }else if($tipo == 3){
            $fields = array(
                'method' => "getListForUser",
                'classe' => "Chamado",
                'p1' => "5"
            );
        }
        
        $fields_string = $this->getFieldsString($fields);
        
        $rs = $this->execCurl($fields, $fields_string);
        return $rs;
    }
    
    public function getAlertas($tipo=1){
        if($tipo == 1){
            $fields = array(
                'method' => "getListAlertForUser",
                'classe' => "Chamado",
                'p1' => "1"
            );
        }else if($tipo == 2){
            $fields = array(
                'method' => "getListAlertForUser",
                'classe' => "Chamado",
                'p1' => "2"
            );
        }
        
        $fields_string = $this->getFieldsString($fields);
        
        $rs = $this->execCurl($fields, $fields_string);
        return $rs;
    }
        
    public function getChamado($id_chamado){
        //DADOS DO CHAMADO
        $fields = array(
                'method' => "getChamadoJoin",
                'classe' => "Chamado",
                'p1' => $id_chamado
            );
        $fields_string = $this->getFieldsString($fields);
        $re_arr = $this->convertToArray($this->execCurl($fields, $fields_string));
        $rs['chamado'] = current($re_arr['dados']);
        //DADOS DAS MENSAGENS
        unset($fields);
        unset($re_arr);
        $fields = array(
                'method' => "getMensagensChamado",
                'classe' => "ChamadoMensagem",
                'p1' => $id_chamado
            );
        $fields_string = $this->getFieldsString($fields);
        $re_arr = $this->convertToArray($this->execCurl($fields, $fields_string));
        $rs['mensagens'] = $re_arr['dados'];
        //DADOS DOS ANEXOS
        unset($fields);
        unset($re_arr);
        $fields = array(
                'method' => "getAnexosChamado",
                'classe' => "ChamadoAnexo",
                'p1' => $id_chamado
            );
        $fields_string = $this->getFieldsString($fields);
        $re_arr = $this->convertToArray($this->execCurl($fields, $fields_string));
        $rs['anexos'] = $re_arr['dados'];
        
        return $rs;
    }
    
    public function atualizaChamado($id_chamado,$dados){
        
        $chamadoStatus = $dados['status'];
        $chamadoMensagem = $dados['msg'];
        $chamadoMensagemUsuario = $dados['usuario'];
        
        if(isset($dados['msg']) && !empty($dados['msg'])){ //GRAVA A RESPOSTA INDEPENDENDE DO BOTAO CLICADO
            $fields = array(
                    'method' => "salvar",
                    'classe' => "ChamadoMensagem",
                    'p1' => $id_chamado,
                    'p2' => $chamadoMensagem,
                    'p3' => $chamadoMensagemUsuario
                );
            $fields_string = $this->getFieldsString($fields);
            $re_arr = $this->convertToArray($this->execCurl($fields, $fields_string));
            $rs['chamado'] = current($re_arr['dados']);
        }
        
        //Atualiza o Chamado
        if($chamadoStatus > 0 ){
            $fields = array(
                    'method' => "salvar",
                    'classe' => "Chamado",
                    'p1' => $id_chamado,
                    'p2' => $chamadoStatus
                );
            $fields_string = $this->getFieldsString($fields);
            $re_arr = $this->convertToArray($this->execCurl($fields, $fields_string));
            $rs['chamado'] = current($re_arr['dados']);
        }
        
    }
    
    public static function getCountChamados(){
        $obj = new ComprasChamados();
        $re_arr = $obj->convertToArray($obj->getChamados(1));
        $rs = $re_arr['dados'];
        return count($rs);
    }
    
    /*
     * $fields = array(
            'lname' => urlencode($last_name),
            'fname' => urlencode($first_name),
            'title' => urlencode($title),
            'company' => urlencode($institution),
            'age' => urlencode($age),
            'email' => urlencode($email),
            'phone' => urlencode($phone)
        );
     * 
     * 
     * foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
     * 
     */
    private function getFieldsString($fields){
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');
        return $fields_string;
    }
    
    private function execCurl($fields=null,$fields_string=null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if($fields!==null){
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function convertToArray($xmlstring){
        $xml = simplexml_load_string(utf8_decode($xmlstring));
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        return $array;
    }
    
}

