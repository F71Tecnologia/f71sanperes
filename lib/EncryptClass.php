<?php 
/*
 * PHP-DOC - EncryptClass.php 
 * 
 * Classe para manipula��o de criptografia one-way (m�o �nica) ou (m�o dupla) com md5(), sha1(), base64_encode() e base64_decode() 
 *
 * 04-01-2016
 * 
 * @package EncryptClass
 * @access public   
 * 
 * @version
 *  
 * Vers�o: 1.00.0000 - 04/01/2016 - Jacques - Vers�o Beta da classe de conex�o
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

    private   $versao = 'tag_rev';
    

    /*
     * PHP-DOC 
     * 
     * @name __construct
     * 
     * @internal - Executa alguns m�todos na constru��o da classe
     */    
    public function __construct(){
        
        $this->setDefault();

    }

    /*
     * PHP-DOC 
     * 
     * @name __destruct
     * 
     * @internal - Executa alguns m�todos ao destruir � classe
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