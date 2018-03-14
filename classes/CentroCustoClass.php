<?php

class CentroCusto{
    
    public function getCentroCusto($regiao, $nome) {
        if(!empty($nome)){
            $and = "AND nome LIKE '%{$nome}%'";
        }else{
            $and = "";
        }
        
        $sql = "SELECT *
            FROM centro_custo WHERE id_regiao = {$regiao} AND status = 1 {$and}";
        $qry = mysql_query($sql) or die("Erro getCentroCusto");
        
        return $qry;
    }
    
    public function getCentroCustoId($id){
        $sql = "SELECT *
            FROM centro_custo
            WHERE id_centro_custo = {$id}";
        $qry = mysql_query($sql);
        $res = mysql_fetch_assoc($qry);
        
        return $res;
    }
    
    public function cadCentroCusto() {
	$nome = acentoMaiusculo($_REQUEST['nome']);
        $regiao_id = $_REQUEST['regiao'];
        
        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        /*$local = "Centro de Custo";
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "Cadastro do Centro de Custo {$nome}";*/
        //$acao = "Inserчуo do Centro de Custo {$nome}";
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];
        
        //VERIFICA SE JA EXISTE O CENTRO DE CUSTO
        $sql_centExist = mysql_query("SELECT * FROM centro_custo WHERE nome = '{$nome}' AND id_regiao = {$regiao_id}");
        $res_centExist = mysql_num_rows($sql_centExist);
        
        if($res_centExist > 0){
            $_SESSION['MESSAGE'] = 'Jс existe o movimento '.$nome;
            $_SESSION['MESSAGE_TYPE'] = 'danger';
        }else{
            $insere = mysql_query("INSERT INTO centro_custo(nome, id_regiao) values 
            ('{$nome}', '{$regiao_id}')") or die(mysql_error());
            
            $id_centrocusto = mysql_insert_id();
            
            $local = "Centro de Custo";
            $ip = $_SERVER['REMOTE_ADDR'];
            $acao = "Cadastro do Centro de Custo: ID{$id_centrocusto}";
            
            $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
              ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());
            
            if ($insere && $insere_log) {
                $_SESSION['MESSAGE'] = 'Informaчѕes gravadas com sucesso!';            
                $_SESSION['MESSAGE_TYPE'] = 'info';                        
            } else {
                $_SESSION['MESSAGE'] = 'Erro ao cadastrar o centro de custo '.$nome;            
                $_SESSION['MESSAGE_TYPE'] = 'danger';
            }
            
            return $id_centrocusto;
        }
    }
    
    public function editCentroCusto() {
        $id = $_REQUEST['id'];
	$nome = strtoupper($_REQUEST['nome']);
        $regiao_id = $_REQUEST['regiao'];
        
        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        $local = "Centro de Custo";
        $ip = $_SERVER['REMOTE_ADDR'];
        //$acao = "{$usuario['nome']} editou o centro de custo {$id}";
        $acao = "Alteraчуo no Centro de Custo: ID{$id}";
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];
        
        $update = mysql_query("UPDATE centro_custo SET nome = '{$nome}', id_regiao = '{$regiao_id}' WHERE id_centro_custo = '{$id}'") or die(mysql_error());
        
        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
          ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());
        
        if ($update && $insere_log) {
            $_SESSION['MESSAGE'] = 'Informaчѕes alteradas com sucesso!';
            $_SESSION['MESSAGE_TYPE'] = 'info';
            session_write_close();
            header('Location: index.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao editar o centro de custo '.$nome;
            $_SESSION['MESSAGE_TYPE'] = 'danger';
        }                
    }
    
    public function delCentroCusto() {
        $id = $_REQUEST['id'];
        
        //dados usuario para cadastro de log
        $usuario = carregaUsuario();
        $local = "Centro de Custo";
        $ip = $_SERVER['REMOTE_ADDR'];
        //$acao = "{$usuario['nome']} excluiu o centro de custo {$id}";
         $acao = "Exclusуo de Centro de Custo: ID{$id}";
        $id_usuario = $usuario['id_funcionario'];
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao_usuario = $usuario['id_regiao'];
        
        $sql = mysql_query("UPDATE centro_custo SET status = 0 WHERE id_centro_custo = '{$id}'") or die(mysql_error());
        
        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
          ('{$id_usuario}', '{$regiao_usuario}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());
    }
    
}

?>