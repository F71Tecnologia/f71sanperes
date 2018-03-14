<?php
class Log {
    
    public function __construct() {
    
        
    }
    
    /**
     * @author Lucas Praxedes (09/12/2016)
     * NOVO LOG
     */
    public function log ($local, $acao, $tabela, $campoAntigo = null, $campoNovo = null) {
        $charset = mysql_set_charset('latin1');
        
        $sqlFunc = "SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1";
        $queryFunc = mysql_query($sqlFunc);
        $funcionario = mysql_fetch_assoc($queryFunc);
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "{$acao}";
        $now = date("Y-m-d H:i:s");
        
        $sqlLog = "INSERT INTO new_log 
                   (id_user, id_regiao, id_local, tabela, tipo_user, grupo_user, horario, ip, acao) 
                   VALUES 
                   ('{$funcionario['id_funcionario']}','{$funcionario['id_regiao']}','$local', '$tabela', '{$funcionario['tipo_usuario']}','{$funcionario['grupo_usuario']}',NOW(),'$ip','$acao')";
        $queryLog = mysql_query($sqlLog);
        $idLog = mysql_insert_id();
        
        if ($campoAntigo != null && $campoNovo != null) {
            
            foreach ($campoAntigo as $key => $value) {
                if ($value != $campoNovo[$key]) {
                    $sql = "INSERT INTO new_log_campos (id_log, campo, antes, depois) VALUES ('$idLog', '$key', '$value', '{$campoNovo[$key]}')";
                    $query = mysql_query($sql);
                }
            }
        }
    }
    
    /**
     * @author Lucas Praxedes Serra (09/12/2016)
     * MÉTODO PARA RECUPERAR OS DADOS DA LINHA ANTES DA ATUALIZAÇÃO
     */
    
    public function getLinha($tabela, $id) {
        $sql = "DESCRIBE $tabela";
        $query = mysql_query($sql);
        $result = mysql_result($query, 0);
        
        $sqlLinha = "SELECT * FROM $tabela WHERE $result = $id LIMIT 1";
        $queryLinha = mysql_query ($sqlLinha);
        $arrLinha = mysql_fetch_assoc($queryLinha);
        
        return $arrLinha;
    }
    
    //GRAVA LOG DE CRIACAO E EXCLUSAO DOS ARQUIVOS (DEPRECIADO)
    public function gravaLog($local, $acao) {
        $charset = mysql_set_charset('latin1');
        $f = mysql_fetch_assoc(mysql_query("SELECT * FROM funcionario WHERE id_funcionario = {$_COOKIE['logado']} LIMIT 1;"));
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "{$acao}";
        $now = date("Y-m-d H:i:s");
        $sqlLog = "INSERT INTO log 
        (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
        VALUES 
        ('$f[id_funcionario]', '$f[id_regiao]', '$f[tipo_usuario]', '$f[grupo_usuario]', '$local', NOW(), '$ip', '$acao')";
        mysql_query($sqlLog);
         
    }
}