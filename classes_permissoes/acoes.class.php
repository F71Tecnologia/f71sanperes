<?php
class Acoes {

//    private $sql;
//    private $row;

    /**
     * 
     * @param type $acoes_id
     * @return boolean
     */
    public function verifica_permissoes($acoes_id){
        $verifica_acoes = mysql_num_rows(mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id  = '$acoes_id'"));
        if($verifica_acoes != 0) { return true;} else { return false;}
    }
    
    /**
     * 
     * @param type $acoes_id
     * @param type $id_regiao
     * @return boolean
     */
    public function permissoes_folha($acoes_id,$id_regiao){
        $verifica_acoes = mysql_num_rows(mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND acoes_id = '$acoes_id' AND id_regiao = '$id_regiao'"));
        if($verifica_acoes != 0) { return true;} else { return false;}
    }
    
    /**
     * 
     * @param type $id_usuario
     * @param type $id_regiao
     * @return type
     */
    public function getAcoes($id_usuario, $id_regiao){
        
        $dados = array();
        $query = "SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = '{$id_usuario}' AND id_regiao = '{$id_regiao}'";
        $sql = mysql_query($query) or die("Erro ao selecionar acoes de folha");
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                   $dados[] = $rows['acoes_id'];
             }
        }
        
        return $dados;
    }
	
}

?>