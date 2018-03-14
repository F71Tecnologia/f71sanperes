<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtMovs
 *
 * @author Sinesio
 */
class ExtMovs {
    
    protected function removeMov($idClt, $idFolha, $idMov, $codMov){
        
        $retorno = false;
        try{
            
            $qry = "DELETE FROM rh_movimentos_clt WHERE id_clt = '{$idClt}' AND id_folha = '{$idFolha}' AND id_mov = '{$idMov}' AND cod_movimento = '{$codMov}' ";
            $sql = mysql_query($qry) or die('Erro ao Remover movimentos');
            
            if($sql){
                $retorno = true;
            } 
            
        }  catch (Exception $e){
            echo $e->getMessage();
        }  
        
        return $retorno;
    }
    
    public function debug($var,$exit=false){
        echo "<pre>";
        print_r($var);
        echo "</pre>";
        
        if($exit){
            exit();
        }
    }
    
}
