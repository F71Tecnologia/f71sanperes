<?php


/**
 * Description of EduTurmasClass
 * 
 * @author Juarez Garritano
 */
class EduTurmasClass {
    private $tabela = 'edu_escolas_turmas';
    
    /**
     * Busca turmas na tabela com o parametro do id_escola
     * @param type $id_escola
     * @return type
     */
    
    public function listTurmas($id_escola){
        $condicao = array("id_escola"=>$id_escola);
        $turmas = montaQuery($this->tabela, "*", $condicao, "id_turma");
        return $turmas;
    }
            
    public function insereTurma($dados){
        $campos = array_keys($dados);
        
        return sqlInsert($this->tabela, $campos, $dados);
    }
    
    public function verTurma($id_turma){
        $where = array("id_turma" => $id_turma);
        
        return montaQuery($this->tabela, "*", $where);
    }
    
    public function editaTurma($id_escola, $arrayDados) {
        $where = array("id_escola" => $id_escola);
        
        return sqlUpdate($this->tabela, $arrayDados, $where);
    }
    
    public function removeTurma($id_turma){
        $where = array("id_turma" => $id_turma);
        
        return sqlUpdate($this->tabela, " status = 0 ", $where);
    }
    
}
