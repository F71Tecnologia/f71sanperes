<?php

class Permissoes{
    
    /**
     * M�TODO PARA SELECIONAR PERMISS�ES DE MASTER
     * @param type $usuario
     * @return type
     */
    public function getPermissaoMaster($usuario){
        
        $query = "SELECT A.id_usuario, A.id_master, B.nome, B.status
                    FROM sis_usuario_master AS A
                    LEFT JOIN master AS B ON(A.id_master = B.id_master)
                    WHERE A.id_usuario = '{$usuario}' AND B.status = 1" ;
        $sql = mysql_query($query) or die("Erro ao selecionar permiss�es do master");
        $dados = array();
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[] = $rows["id_master"];                
            }
        }
        
        return $dados;
    }
    
    /**
     * M�TODO PARA SELECIONAR PERMISS�ES DE REGI�O
     * @param type $usuario
     * @return boolean
     */
    public function getPermissaoRegiao($usuario){
        
        $query = "SELECT A.id_usuario, A.id_regiao, B.regiao, B.status
                    FROM sis_usuario_regiao AS A
                    LEFT JOIN regioes AS B ON(A.id_regiao = B.id_regiao)
                    WHERE A.id_usuario = '{$usuario}' AND B.status = 1" ;
        $sql = mysql_query($query) or die("Erro ao selecionar permiss�es de regi�o");
        $dados = array();
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[] = array("id_usuario" => $rows["id_usuario"], "id_regiao" => $rows["id_regiao"], "regiao" => $rows["regiao"], "status_regiao" => $rows["status"]);                
            }
        }
        
        return $dados;
    }
    
    /**
     * M�TODO PARA SELECIONAR PERMISS�ES DE ACOES
     * @param type $usuario
     * @return type
     */
    public function getPermissaoAcoes($usuario){
        
        $query = "SELECT A.id_usuario, A.id_acao, A.id_regiao, B.acoes_nome
                    FROM sis_usuario_acoes AS A
                    LEFT JOIN acoes AS B ON(A.id_acao = B.acoes_id)
                    WHERE A.id_usuario = '{$usuario}'" ;
        $sql = mysql_query($query) or die("Erro ao selecionar permiss�es de acoes");
        $dados = array();
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[$rows["id_usuario"]][$rows["id_regiao"]][$rows["id_acao"]] = $rows["acoes_nome"];                
            }
        }
        
        return $dados;
    }
    
    /**
     * M�TODO PARA SELECIONAR PERMISS�ES DE BOT�ES
     * @param type $usuario
     * @return type
     */
    public function getPermissaoBotoes($usuario){
        
        $query = "SELECT A.id_usuario, A.id_regiao, A.id_botoes, B.botoes_nome
                    FROM sis_usuario_botoes AS A
                    LEFT JOIN botoes AS B ON(A.id_botoes = B.botoes_id)
                    WHERE A.id_usuario = '{$usuario}' AND B.status = 1" ;
        $sql = mysql_query($query) or die("Erro ao selecionar permiss�es de acoes");
        $dados = array();
        if($sql){
            while($rows = mysql_fetch_assoc($sql)){
                $dados[$rows["id_usuario"]][$rows["id_regiao"]][$rows["id_botoes"]] = $rows["botoes_nome"];                
            }
        }
        
        return $dados;
    }
    
    /**
     * M�TODO PARA REMOVER PERMISS�ES DO MASTER POR FUNCIONARIO
     * @param type $funcionario
     */
    public function removePermissoesMasterByFuncionario($funcionario){
        $retorno = false;
        $query = "DELETE FROM sis_usuario_master WHERE id_usuario = '{$funcionario}'";
        $sql = mysql_query($query) or die("Erro ao remover permiss�es de master do funcionario");
        if($sql){
            $retorno = true;
        }
        
        return $retorno;
    }
    
    /**
     * M�TODO PARA CADASTRAR PERMISS�ES DE MASTER
     * @param type $funcionario
     * @param type $master
     * @return boolean
     */
    public function cadastraPermissaoMaster($funcionario, $master = array()){
        $retorno = false;
        if($this->removePermissoesMasterByFuncionario($funcionario)){
            $query = "INSERT INTO sis_usuario_master (id_usuario, id_master) VALUES ";
            foreach ($master as $dados_master){
                $query .= "('{$funcionario}','{$dados_master}'),";
            }
            $query = substr($query,0,-1);
            
            $sql = mysql_query($query) or die("Erro ao cadastrar permiss�es de master");
            if($sql){
                $retorno = true;
            }
            
            return $retorno;
        }
    }
    
    
}
