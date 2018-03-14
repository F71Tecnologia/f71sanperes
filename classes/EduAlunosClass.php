<?php


/**
 * Description of EduAlunosClass
 * 
 * @author Juarez Garritano
 */
class EduAlunosClass {
    private $tabela = 'edu_escola_aluno';
    
    /**
     * Busca alunos na tabela com o parametro do id_aluno
     * @param int $id_escola
     * @return type
     */
    
    public function listAlunos($id_turma){
        $condicao = array('id_turma'=>$id_turma);
        $alunos = montaQuery($this->tabela, "*", $condicao, "id_aluno");
        return $alunos;
    }
    
    public function insereAluno($dados){
        $campos = array_keys($dados);
        
        return sqlInsert($this->tabela, $campos, $dados);
    }
    
    public function verAluno($id_aluno){
        $where = array("id_aluno" => $id_aluno);
        
        return montaQuery($this->tabela, "*", $where);
    }
    
    public function editaAluno($id_aluno, $arrayDados){
        $where = array("id_aluno" => $id_aluno);
        
        return sqlUpdate($this->tabela, $arrayDados, $where);
    }
    
    public function removeAluno($id_aluno){
        $where = array("id_aluno" => $id_aluno);
        
        return sqlUpdate($this->tabela, " status = 0 ", $where);
    }
    
}
