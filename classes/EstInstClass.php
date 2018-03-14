<?php

class EstInst{
    
    public function getEstInst($nome) {
        if(!empty($nome)){
            $and = "AND nome LIKE '%{$nome}%'";
        }else{
            $and = "";
        }
        
        $sql = "SELECT *
            FROM instituicoes_estagiario WHERE status = 1 {$and}";
        $qry = mysql_query($sql) or die("Erro getCentroCusto");
        
        return $qry;
    }
    
    public function getEstInstId($id){
        $sql = "SELECT *
            FROM instituicoes_estagiario
            WHERE id_instituicao = {$id}";
        $qry = mysql_query($sql);
        $res = mysql_fetch_assoc($qry);
        
        return $res;
    }
    
    public function cadEstInst() {
	$nome = acentoMaiusculo($_REQUEST['nome']);
        $regiao_id = $_REQUEST['regiao'];
        
        //dados usuario para cadastro de log
        $usuario = carregaUsuario();

        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];
        
        //VERIFICA SE JA EXISTE O CENTRO DE CUSTO
        $sql_centExist = mysql_query("SELECT * FROM instituicoes_estagiario WHERE nome = '{$nome}'");
        $res_centExist = mysql_num_rows($sql_centExist);
        
        if($res_centExist > 0){
            $_SESSION['MESSAGE'] = 'Já existe o movimento '.$nome;
            $_SESSION['MESSAGE_TYPE'] = 'danger';
        }else{
            $insere = mysql_query("INSERT INTO instituicoes_estagiario(nome) values 
            ('{$nome}')") or die(mysql_error());
            
            $id_centrocusto = mysql_insert_id();

            if ($insere) {
                $_SESSION['MESSAGE'] = 'Informações gravadas com sucesso!';            
                $_SESSION['MESSAGE_TYPE'] = 'info';                        
            } else {
                $_SESSION['MESSAGE'] = 'Erro ao cadastrar o centro de custo '.$nome;            
                $_SESSION['MESSAGE_TYPE'] = 'danger';
            }
            
            return $id_centrocusto;
        }
    }
    
    public function editEstInst() {
        $id = $_REQUEST['id'];
	$nome = strtoupper($_REQUEST['nome']);
        $regiao_id = $_REQUEST['regiao'];
        
        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        $update = mysql_query("UPDATE instituicoes_estagiario SET nome = '{$nome}' WHERE id_instituicao = '{$id}'") or die(mysql_error());
        
        if ($update) {
            $_SESSION['MESSAGE'] = 'Informações alteradas com sucesso!';
            $_SESSION['MESSAGE_TYPE'] = 'info';
            session_write_close();
            header('Location: index.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao editar o centro de custo '.$nome;
            $_SESSION['MESSAGE_TYPE'] = 'danger';
        }                
    }
    
    public function delEstInst($id) {
        $sql = mysql_query("UPDATE instituicoes_estagiario SET status = 0 WHERE id_instituicao = '{$id}'") or die(mysql_error());
    }
    
}

?>