<?php

/**
 * Description of BotoesClass
 *
 * @author Ramon Lima
 */
class SuporteClass {
    
    public $status = array(1 => "Aberto", 2 => "Respondido", 3 => "Replicado", 4 => "Fechado");
    public $prioridades = array(1 => "Baixa", 2 => "Média", 3 => "Alta", 4 => "Urgente");
    public $prioridadeClass = array(1 => "", 2 => "info", 3 => "warning", 4 => "danger");
    
    public function getListSuporte($objeto){
        $usuario = carregaUsuario();
        //$objeto->criado_por = 162; //ESSA LINHA É SÓ PARA TESTAR, POIS O USUARIO ADM NÃO TEM CHAMADO
        $objeto->criado_por = $usuario['id_funcionario'];
        $arr[] = montaCriteriaSimples($objeto->criado_por, "A.user_cad");
        $arr[] = montaCriteriaSimples($objeto->status, "A.status");
        
        $_campos = "A.*,
                    DATE_FORMAT(A.data_cad, '%d/%m/%Y %H:%i') AS criado_em,
                    DATE_FORMAT(A.ultima_alteracao, '%d/%m/%Y') AS alterado_em,
                    DATE_FORMAT(A.data_fechamento, '%d/%m/%Y') AS fechado_em,
                    B.nome AS aberto_por, C.nome AS respondido_por, D.nome AS fechado_por";
        
        if(isset($objeto->campos) && !empty($objeto->campos)){
            $_campos = $objeto->campos;
        }
        
        $criteria = (array_filter($arr));
        
        $query = "SELECT {$_campos}
                FROM suporte AS A
                INNER JOIN funcionario AS B ON (A.user_cad = B.id_funcionario)
                LEFT JOIN funcionario AS C ON (A.user_res = C.id_funcionario)
                LEFT JOIN funcionario AS D ON (A.id_fechamento = D.id_funcionario)
                WHERE ".implode(" AND ",$criteria)." 
                ORDER BY A.data_cad";
        
        $sql = mysql_query($query) or die("Erro ao selecionar suporte");
        
        while($linha = mysql_fetch_assoc($sql)){
            $dados[$linha['id_suporte']] = $linha;
        }
        
        return $dados;
    }
    
    public function getSuporte($id_suporte){
        $arr[] = montaCriteriaSimples($id_suporte, "A.id_suporte");
        
        $_campos = "A.*,
                    DATE_FORMAT(A.data_cad, '%d/%m/%Y %H:%i') AS criado_em,
                    DATE_FORMAT(A.ultima_alteracao, '%d/%m/%Y') AS alterado_em,
                    DATE_FORMAT(A.data_fechamento, '%d/%m/%Y') AS fechado_em,
                    B.nome AS aberto_por, C.nome AS respondido_por, D.nome AS fechado_por";
        
        $criteria = (array_filter($arr));
        
        $query = "SELECT {$_campos}
                FROM suporte AS A
                INNER JOIN funcionario AS B ON (A.user_cad = B.id_funcionario)
                LEFT JOIN funcionario AS C ON (A.user_res = C.id_funcionario)
                LEFT JOIN funcionario AS D ON (A.id_fechamento = D.id_funcionario)
                WHERE ".implode(" AND ",$criteria)." 
                ORDER BY A.data_cad";
        
        $sql = mysql_query($query) or die("Erro ao selecionar suporte");
        
        while($linha = mysql_fetch_assoc($sql)){
            $dados = $linha;
        }
        
        return $dados;
    }
    
    public function cadSuporte(){
        $usuario = carregaUsuario();
        $user_cad = $usuario['id_funcionario'];
        $regiao = $usuario['id_regiao'];
        $prioridade = $_REQUEST['prioridade'];
        $assunto = acentoMaiusculo($_REQUEST['assunto']);
        $mensagem = acentoMaiusculo($_REQUEST['mensagem']);
        $data_cad = date('Y-m-d H:i:s');
        $arquivo = $_FILES['arquivo'];
        $arquivo_name = $arquivo['name'];
        $ext_arquivo = strrchr($arquivo_name, '.');
        
        $cad_suporte = mysql_query("INSERT INTO suporte (user_cad,data_cad,id_regiao,prioridade,assunto,mensagem,arquivo,ultima_alteracao,tipo,status,status_reg)VALUES('{$user_cad}','{$data_cad}','{$regiao}','{$prioridade}','{$assunto}','{$mensagem}','{$ext_arquivo}','{$data_cad}','5','1','1')") or die(mysql_error());
        
        if($arquivo_name != ''){
            $id_suporte = mysql_insert_id();
            $diretorio = '../../suporte/arquivos/';
            $nome_tmp = 'suporte_'.$regiao.'_'.$id_suporte.$ext_arquivo;
            $nome_arquivo = $diretorio.$nome_tmp;
            
//            echo "NOME TEMP: {$nome_tmp}<br />";
//            echo "NOME ARQ: {$nome_arquivo}<br />";
            
            move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die ("Erro ao enviar o Arquivo: $nome_arquivo");           
        }
        
        if($cad_suporte){
            $_SESSION['MESSAGE_TYPE'] = 'info';
            $_SESSION['MESSAGE'] = 'Chamado criado com sucesso';
        }
    }
    
    public function delSuporte($id_suporte){                
        $local = "Fechamento de Chamado";
        $ip = $_SERVER['REMOTE_ADDR'];
        $data = date('Y-m-d H:i:s');
        $usuario = carregaUsuario();
        $user = $usuario['id_funcionario'];        
        $acao = "{$usuario['nome']} fechou o chamado " . $id_suporte;
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao = $usuario['id_regiao'];
        
        $sql = "UPDATE suporte SET id_fechamento = '$user', data_fechamento = '$data', ultima_alteracao = '$data', status = '4' WHERE id_suporte = '$id_suporte'";
        $qry = mysql_query($sql);
        $res = mysql_fetch_assoc($qry);         
        
        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
                                            ('{$user}', '{$regiao}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());    
                                            
        if($insere_log){
            return true;
        }else{
            return false;
        }   
        
    }


    public static function convertStatus($status){
        $obj = new SuporteClass();
        $arStatus = $obj->status;
        return $arStatus[$status];
    }
    
    public static function convertPrioridade($prioridade){
        $obj = new SuporteClass();
        $arPrioridades = $obj->prioridades;
        return $arPrioridades[$prioridade];
    }
    
    public static function convertPrioridadeClass($prioridade){
        $obj = new SuporteClass();
        $prioridadeClass = $obj->prioridadeClass;
        return $prioridadeClass[$prioridade];
    }
    
    public static function getQntChamadosByUsuario($id_usuario, $tipo = 1){
        $_tipo = ($tipo==1) ? " != 4" : " = 4";
        
        $query = "SELECT COUNT(id_suporte) as total
                FROM suporte
                WHERE user_cad = $id_usuario AND status $_tipo";
        
        $sql = mysql_query($query) or die("Erro ao selecionar suporte");
        $linha = mysql_fetch_assoc($sql);
        return $linha['total'];
    }
    
    public static function getQntChamadosMaster() {
        $usuario = carregaUsuario();
        $sql = mysql_query("SELECT COUNT(id_suporte) AS total
            FROM suporte
            WHERE status = 1 OR status = 3");
        $tot = mysql_result($sql,0);
        return $tot;
    }
}
