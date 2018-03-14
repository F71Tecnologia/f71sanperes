<?php


abstract class MultiExtendClass
{
    // Array de objetos extendidos
    //
    public $extends = array ( );
    // Extende uma classe
    //
    public function extend ( $class )
    {
        // Declara e armazena objeto da classe fornecida
        //
        array_unshift ( $this -> extends , new $class );
    }
}