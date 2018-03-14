<?php

class webServiceSipef{
    
    private $url;
    
    function getUrl() {
        return $this->url;
    }

    function setUrl($url) {
        $this->url = $url;
    } 
    
    public function __construct() {
        ;
    }
    
    public function httpPost($url, $params){
        
        /**
         * OBJETO JSON  
         */
        $postDataJson  = json_encode($params);
         
        /**
         * CURL
         */
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postDataJson);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postDataJson))
        );     
        
        /**
         * OUTPUT   
         */
        $output = curl_exec($ch);
        
        /**
         * ERRO
         */
        if($output === false){
            echo "Error Number:".curl_errno($ch)."<br>";
            echo "Error String:".curl_error($ch);
        } 
        
        /**
         * CLOSE
         */
        curl_close($ch);
        
         
        return $output;
           
    }
    
}

?>