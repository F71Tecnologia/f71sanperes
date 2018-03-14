<?php
/*
 * PHP-DOC - EncryptClass.php 
 * 
 * Classe para manipulaзгo de criptografia one-way (mгo ъnica) ou (mгo dupla) com md5(), sha1(), base64_encode() e base64_decode() 
 *
 * 04-01-2016
 * 
 * @package EncryptClass
 * @access public   
 * 
 * @version
 *  
 * Versгo: 1.00.0000 - 04/01/2016 - Jacques - Versгo Beta da classe de conexгo
 * 
 * @author jacques@f71.com.br
 * 
 * @copyright www.f71.com.br
 */

class EncryptClass {
    
    /* 
     * Passar para carga de arquivo MySqlClass.ini os valores de set de conex?o 
     */

    public    $error;
    public    $cripto;

    private   $row;
    private   $rs;

    private   $versao = '5268';
    

    /*
     * PHP-DOC 
     * 
     * @name __construct
     * 
     * @internal - Executa alguns mйtodos na construзгo da classe
     */    
    public function __construct(){
        
        $this->setDefault();

    }

    /*
     * PHP-DOC 
     * 
     * @name __destruct
     * 
     * @internal - Executa alguns mйtodos ao destruir а classe
     */    
    public function __destruct() {
        

    }
    
    /*
     * PHP-DOC 
     * 
     * @name setDefault
     * 
     * @internal - Define valores default da classe
     */    
    public function setDefault(){
        
        
        
    }
    

}
?>