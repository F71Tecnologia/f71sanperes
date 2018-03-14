<?php

Class Empresa{
    
    public function getParticipantes(){
        $query = "SELECT * FROM funcionario";
        $sql = mysql_query($query) or die("Error ao selecionar participantes");
        
        return $sql;
    }
    
}

?>