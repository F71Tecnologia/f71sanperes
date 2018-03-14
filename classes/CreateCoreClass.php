<?php
/*
 * PHP-DOC 
 * 
 * @name include
 * 
 * @internal - Include que cria e instancia as classes de core (Mãe) para uso da classe
 */


if(!isset($this->error)){

    $this->error = new ErrorClass();        

    if(!is_object($this->getSuperClass()->error)){

       $this->getSuperClass()->error = $this->error;

    }

}

if(!isset($this->db)){

    $this->db = new MySqlClass();

    if(!is_object($this->getSuperClass()->db)){

       $this->getSuperClass()->db = $this->db;

    }

}

if(!isset($this->date)){

    include_once(sprintf("DateClass.php" , $_SERVER[ "DOCUMENT_ROOT" ] ) );    

    $this->date = new DateClass();

    if(!is_object($this->getSuperClass()->date)){

       $this->getSuperClass()->date = $this->date;

    }
    
}

