<?php

function getFeriado() {
    $sql = "SELECT *, CONCAT(DATE_FORMAT(A.data, '%d/%m'),'/',EXTRACT(YEAR FROM CURDATE())) AS data_m, B.regiao AS nome_regiao
            FROM rhferiados AS A
            LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
            WHERE A.status = 1
            ORDER BY DATE_FORMAT(A.data, '%m/%d')";
    $feriado = mysql_query($sql) or die(mysql_error());
    return $feriado;
}

function getFeriadoID($id_feriado) {
    $sql = "SELECT *, CONCAT(DATE_FORMAT(A.data, '%d/%m'),'/',EXTRACT(YEAR FROM CURDATE())) AS data_m, B.regiao AS nome_regiao
            FROM rhferiados AS A
            LEFT JOIN regioes AS B ON (A.id_regiao = B.id_regiao)
            WHERE A.status = 1 AND A.id_feriado = '{$id_feriado}'";
    $feriado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_assoc($feriado);
    return $row;
}

function getRegiao() {
    $sql = "SELECT * FROM regioes ORDER BY regiao";
    $regiao = mysql_query($sql) or die(mysql_error());    
    return $regiao;
}

function getFeriadoTotal($id_regiao, $nome_feriado){
    $sql = "SELECT *
            FROM rhferiados
            WHERE id_regiao = '{$id_regiao}'
            AND nome = '{$nome_feriado}'
            AND status = 1";
    $feriado = mysql_query($sql) or die(mysql_error());
    return $feriado;
}

function alteraStatusFeriado($id_feriado) {
    $sql = "UPDATE rhferiados SET status = 0 WHERE id_feriado = {$id_feriado}";
    $qry = mysql_query($sql);
    $res = mysql_fetch_assoc($qry);         
    
    $usuario = carregaUsuario();
    
    //dados usuario para cadastro de log
    $local = "Exclusуo de Feriado";
    $ip = $_SERVER['REMOTE_ADDR'];
    $acao = "{$usuario['nome']} excluiu o feriado " . $id_empresa;
    $id_usuario = $usuario['id_funcionario'];
    $tipo_usuario = $usuario['tipo_usuario'];
    $grupo_usuario = $usuario['grupo_usuario'];
    $regiao = $usuario['id_regiao'];
    
    $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
                                        ('{$id_usuario}', '{$regiao}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());    
    return $res;    
}

////////////////////////////////////////////////////////////

function getRhProjeto($id_projeto) {
    $sql = "SELECT * FROM projeto WHERE id_projeto = {$id_projeto}";
    $clt = mysql_query($sql);
    $tot = mysql_num_rows($clt);
    return $tot;
}

function cadFeriado(){
    $usuario = carregaUsuario();
    $id_regiao = $_REQUEST['regiao_feriado'];
    $id_usuario = $usuario['id_funcionario'];
    $data_cad = date('Y-m-d');
    $nome_feriado = acentoMaiusculo($_REQUEST['nome_feriado']);
    $data_feriado = ($_REQUEST['data_feriado'] != '') ? converteData($_REQUEST['data_feriado']) : '';
    $tipo = $_REQUEST['tipo_feriado'];
    $movel = $_REQUEST['movel'];
    
    $feriado = getFeriadoTotal($id_regiao, $nome_feriado);
    $total_feriado = mysql_num_rows($feriado);            
    
    if ($total_feriado != 0) {
        $_SESSION['MESSAGE'] = 'Jс Existe um Feriado '.$nome_feriado.' cadastrado nessa Regiуo';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao; 
        
    }else{
        $insere_feriado = mysql_query("INSERT INTO rhferiados (id_user_cad, data_cad, id_regiao, tipo, nome, data, movel, status) VALUES 
                            ('{$id_usuario}', '{$data_cad}', '{$id_regiao}', '{$tipo}', '{$nome_feriado}', '{$data_feriado}','{$movel}', '1')
                            ") or die (mysql_error());
        
        //dados usuario para cadastro de log
        $local = "Cadastro de Feriado";
        $ip = $_SERVER['REMOTE_ADDR'];
        $acao = "{$usuario['nome']} cadastrou o feriado {$nome_feriado}";        
        $tipo_usuario = $usuario['tipo_usuario'];
        $grupo_usuario = $usuario['grupo_usuario'];
        $regiao = $usuario['id_regiao'];
        
        $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
                        ('{$id_usuario}', '{$regiao}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());
        
        if ($insere_feriado && $insere_log) {
            $_SESSION['MESSAGE'] = 'Informaчѕes gravadas com sucesso!';
            $_SESSION['MESSAGE_COLOR'] = 'message-blue';            
            session_write_close();
            header('Location: index.php');
        } else {
            $_SESSION['MESSAGE'] = 'Erro ao cadastrar o feriado '.$nome;
            $_SESSION['MESSAGE_COLOR'] = 'message-red';
            $_SESSION['regiao'] = $id_regiao;
            session_write_close();
            header('Location: index.php');
        }
    }
}

function alteraFeriado() {    
    $usuario = carregaUsuario();
    $id_regiao = $_REQUEST['regiao_feriado'];  
    $id_usuario = $usuario['id_funcionario'];
    $nome_feriado = acentoMaiusculo($_REQUEST['nome_feriado']);
    $data_feriado = ($_REQUEST['data_feriado'] != '') ? converteData($_REQUEST['data_feriado']) : '';
    $tipo = $_REQUEST['tipo_feriado'];
    $movel = $_REQUEST['movel'];
    $id_feriado = $_REQUEST['feriado'];
    
    //dados usuario para cadastro de log
    $local = "Alteraчуo de Feriado";
    $ip = $_SERVER['REMOTE_ADDR'];
    $acao = "{$usuario['nome']} alterou o feriado " . $id_feriado;
    $tipo_usuario = $usuario['tipo_usuario'];
    $grupo_usuario = $usuario['grupo_usuario'];
    $regiao = $usuario['id_regiao'];
    
    $altera_empresa = mysql_query("UPDATE rhferiados SET id_regiao = '{$id_regiao}', tipo = '{$tipo}', nome = '{$nome_feriado}', data = '{$data_feriado}', movel = '{$movel}' WHERE id_feriado = '{$id_feriado}'") or die(mysql_error());

    $insere_log = mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) VALUES
                                ('{$id_usuario}', '{$regiao}', '{$tipo_usuario}', '{$grupo_usuario}', '{$local}', NOW(), '{$ip}', '{$acao}')") or die(mysql_error());

    if ($altera_empresa && $insere_log) {
        $_SESSION['MESSAGE'] = 'Informaчѕes alteradas com sucesso!'.$id_regiao;
        $_SESSION['MESSAGE_COLOR'] = 'message-blue';
        $_SESSION['regiao'] = $id_regiao;
        header('Location: index.php');

    } else {
        $_SESSION['MESSAGE'] = 'Erro ao atualizar a unidade';
        $_SESSION['MESSAGE_COLOR'] = 'message-red';
        $_SESSION['regiao'] = $id_regiao;
    }    
}

function getFeriadoFiltrado($master, $id_regiao, $mes) {
    if(!empty($id_regiao) AND $id_regiao > 0){
        $auxRegiao = " AND A.id_regiao IN(0,$id_regiao) ";
    }
    if(!empty($mes) AND $mes > 0){
        $auxMes = " AND MONTH(data) = $mes ";
    }
    $sql = "
    SELECT A.*, CONCAT(DATE_FORMAT(A.data, '%d/%m'),'/',EXTRACT(YEAR FROM CURDATE())) AS data_m, B.regiao AS nome_regiao
    FROM rhferiados AS A LEFT JOIN regioes B ON (A.id_regiao = B.id_regiao)
    WHERE A.status = 1
    $auxMes $auxRegiao
    ORDER BY DATE_FORMAT(data, '%m/%d')";
    $feriado = mysql_query($sql) or die(mysql_error());
    return $feriado;
}
?>