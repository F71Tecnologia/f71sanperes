<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiClass
 *
 * @author Ramon
 */
class ApiClass {
    //put your code here
    private $tabela = "api";
    private $empresa = "BRGaap";
    private $mEnvio = INPUT_GET;//INPUT_POST;
    
    public function getMethod($method){
        $condicao = array("requisicao"=>$method,"empresa"=>$this->empresa,"status"=>1);
        $rs = montaQueryFirst($this->tabela, "*", $condicao);
        return $rs;
    }
    
    public function getMethodParametros($id_api){
        $condicao = array("id_api"=>$id_api,"status"=>1);
        $rs = montaQuery("api_parametros", "*", $condicao,"ordem");
        return $rs;
    }
    
    public function trataParametrosMethod($id_api){
        $rs = $this->getMethodParametros($id_api);
        $params = "";
        foreach($rs as $value){
            if(isset($_REQUEST[$value['nome']]) && !empty($_REQUEST[$value['nome']])){
                $params .= "'".$_REQUEST[$value['nome']]."',";
            }else{
                return false;
            }
        }
        $params = substr($params, 0, -1);
        return $params;
    }
    
    public function post($variavel, $tipo = null, $antiInjection = false) {
        $re = "";
        if ($tipo !== null) {
            switch ($tipo) {
                case "INT":
                    $re = filter_input($this->mEnvio, $variavel, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case "EMAIL":
                    $re = filter_input($this->mEnvio, $variavel, FILTER_SANITIZE_EMAIL);
                    break;
                case "FLOAT":
                    $re = filter_input($this->mEnvio, $variavel, FILTER_SANITIZE_NUMBER_FLOAT);
                    break;
                case "URL":
                    $re = filter_input($this->mEnvio, $variavel, FILTER_SANITIZE_URL);
                    break;
                case "HTML":
                    $re = htmlspecialchars($variavel);
                    break;
                case "HTML_PHP":
                    $re = strip_tags($variavel);
                    break;
            }
        } else {
            $re = filter_input($this->mEnvio, $variavel, FILTER_SANITIZE_STRING);
        }
        
        if($antiInjection){
            $re = $this->antiInjection($re);
        }
        
        //MANUAL FILTER INPUT
        //http://php.net/manual/pt_BR/function.filter-input.php
        //http://www.w3schools.com/php/func_filter_input.asp
        //http://www.w3schools.com/php/php_ref_filter.asp

        return $re;
    }
    
    /**
    * MÉTODO QUE VERIFICA A EXISTENCIA DE SINTEXE SQL
    * E FAZ OUTROS TRATAMENTOS
    * @param type $string
    */
   private function antiInjection($string){
        //REMOVE PALAVRAS QUE CONTENHAM SINTEXE SQL
        $string = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables)/"),"",$string);
        //REMOVE ESPAÇOS VAZIOS NO INÍCIO E NO FINAL DO ARQUIVO
        $string = trim($string);
        //ADICIONA BARRA IVERTIDAS EM UMAM STRING
        $string = $this->post($string,"HTML");
        
        return $string;
   }
   
   /**
    * METODO QUE VAI MONTAR O ARRAY DE RETORNO
    * @param type $result
    * @return type
    */
   public function montaRetorno($result, $tipo = 1) {

        $array = null;
        $c = 0;
        if ($tipo == 1) {
            while ($row = mysql_fetch_assoc($result)) {
                foreach ($row as $k => $val) {
                    $array[$c][$k] = utf8_encode($val);
                }
                $c++;
            }
        } else {
            foreach ($result as $k => $val) {
                $array[$c][$k] = utf8_encode($val);
            }
            $c++;
        }
        return $array;
    }

    /**
    * 
    * @param type $dados
    */
    public function criaLog($dados = array()){
        
        try{
            if(!empty($dados)){

                $campos  = "";
                $valores = "";
                $qry = "INSERT INTO api_log ";

                foreach($dados as $k => $v){
                    if(!empty($v)){
                        $campos  .= "{$k},";
                        $valores .= "'{$v}',";
                    }
                } 

                $campos  = substr($campos,0,-1);
                $valores = substr($valores,0,-1);

                $qry .= " ({$campos}) VALUES ($valores); ";
                $sql = mysql_query($qry) or die("Erro ao criar log");
                
            }
        }catch(Exception $e){
            echo $e->getMessage('Erro ao criar log');
        }    
    }
   
}
