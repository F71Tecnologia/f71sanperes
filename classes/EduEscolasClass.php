<?php


/**
 * Description of EduEscolasClass
 * 
 * @author Ramon Lima
 */
class EduEscolasClass {
    
    private $tabela = 'edu_escolas';
    
    public function listEscolas(){
        $escolas = montaQuery($this->tabela, "*", null, "id_escola");
        return $escolas;
    }
    
    public function insereEscola($dados){
        $campos = array_keys($dados);
        
        return sqlInsert($this->tabela, $campos, $dados);
    }
    
    public function verEscola($id_escola){
        $where = array("id_escola" => $id_escola);
        
        return montaQuery($this->tabela, "*", $where);
    }
    
    public function editaEscola($id_escola, $arrayDados){
        $where = array("id_escola" => $id_escola);
        
        return sqlUpdate($this->tabela, $arrayDados, $where);
    }
    
    public function removeEscola($id_escola){
        $where = array("id_escola" => $id_escola);
        
        return sqlUpdate($this->tabela, " status = 0 ", $where);
    }
    
}
